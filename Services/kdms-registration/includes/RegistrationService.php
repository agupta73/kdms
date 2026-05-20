<?php

declare(strict_types=1);

namespace KdmsRegistration;

use PDO;
use PDOException;

final class RegistrationService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * @param array<string, mixed> $input
     * @return array{success: bool, Devotee_Key?: string, action?: string, error?: string}
     */
    public function register(array $input): array
    {
        $first = $this->sanitizeName((string) ($input['Devotee_First_Name'] ?? ''));
        $last = $this->sanitizeName((string) ($input['Devotee_Last_Name'] ?? ''));
        $idType = $this->sanitizeShort((string) ($input['Devotee_ID_Type'] ?? ''));
        $idNumber = IdNormalizer::normalize($idType, (string) ($input['Devotee_ID_Number'] ?? ''));

        if ($first === '' || $last === '' || $idType === '' || $idNumber === '') {
            return ['success' => false, 'error' => 'Please fill in all required fields.'];
        }

        $candidateKey = strtoupper(trim((string) ($input['Devotee_Key'] ?? '')));
        if ($candidateKey === '') {
            $candidateKey = GenerateId::generate($this->db);
        }

        $phone = $this->sanitizePhone((string) ($input['Devotee_Cell_Phone_Number'] ?? ''));
        $dob = $this->sanitizeDate((string) ($input['Devotee_DOB'] ?? ''));
        $station = $this->sanitizeShort((string) ($input['Devotee_Station'] ?? ''));
        $idStaging = $this->sanitizePath((string) ($input['id_staging_gcs_path'] ?? ''), $candidateKey);
        $selfiePath = $this->sanitizePath((string) ($input['selfie_gcs_path'] ?? ''), $candidateKey);
        $eventId = reg_active_event_id();

        $dedupPayload = [
            'Devotee_Key' => $candidateKey,
            'Devotee_First_Name' => $first,
            'Devotee_Last_Name' => $last,
            'Devotee_ID_Type' => $idType,
            'Devotee_ID_Number' => $idNumber,
            'Devotee_Cell_Phone_Number' => $phone,
            'Devotee_DOB' => $dob,
            'Devotee_Station' => $station,
            'Devotee_Type' => 'T',
            'Devotee_Status' => 'D',
            'eventId' => $eventId,
        ];

        $dedup = KdmsApiClient::deduplicate($dedupPayload);
        if (!$dedup['ok']) {
            return [
                'success' => false,
                'error' => 'Registration could not be verified. Please try again or ask for help at the counter.',
            ];
        }

        $survivorKey = $dedup['survivor_key'];
        $action = $dedup['action'];

        if ($action === 'merged') {
            try {
                $this->db->beginTransaction();
                $this->attachChildRows($survivorKey, $idType, $idNumber, $idStaging, $selfiePath);
                if ($eventId !== '' && !$this->hasAccommodationForEvent($survivorKey, $eventId)) {
                    AccommodationAssigner::assignOther($this->db, $survivorKey, $eventId);
                }
                $this->db->commit();
            } catch (PDOException $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                kdms_log('ERROR', 'Registration attach after merge failed', ['error' => $e->getMessage()]);

                return ['success' => false, 'error' => 'Registration could not be completed. Please try again or ask for help at the counter.'];
            }
        } else {
            try {
                $this->db->beginTransaction();
                $this->insertDevotee($survivorKey, $first, $last, $idType, $idNumber, $phone, $dob, $station);
                $this->attachChildRows($survivorKey, $idType, $idNumber, $idStaging, $selfiePath);
                AccommodationAssigner::assignOther($this->db, $survivorKey, $eventId);
                $this->db->commit();
            } catch (PDOException $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }
                kdms_log('ERROR', 'Registration DB transaction failed', ['error' => $e->getMessage()]);

                return ['success' => false, 'error' => 'Registration could not be completed. Please try again or ask for help at the counter.'];
            }
        }

        if ($eventId !== '') {
            KdmsApiClient::addToPrintQueue($survivorKey, $eventId);
        }

        return [
            'success' => true,
            'Devotee_Key' => $survivorKey,
            'action' => $action,
        ];
    }

    private function insertDevotee(
        string $key,
        string $first,
        string $last,
        string $idType,
        string $idNumber,
        string $phone,
        string $dob,
        string $station
    ): void {
        $stmt = $this->db->prepare(
            'INSERT INTO devotee (
                Devotee_Key, Devotee_Type, Devotee_First_Name, Devotee_Last_Name, Devotee_Gender,
                Devotee_DOB, Devotee_ID_Type, Devotee_ID_Number, Devotee_Station,
                Devotee_Cell_Phone_Number, Devotee_Status, Devotee_Record_Update_Date_Time, Devotee_Record_Updated_By
            ) VALUES (
                :key, :type, :first, :last, :gender, :dob, :id_type, :id_number,
                :station, :phone, :status, NOW(), :updated_by
            )'
        );
        $stmt->execute([
            'key' => $key,
            'type' => 'T',
            'first' => $first,
            'last' => $last,
            'gender' => '',
            'dob' => $dob !== '' ? $dob : null,
            'id_type' => $idType,
            'id_number' => $idNumber,
            'station' => $station,
            'phone' => $phone,
            'status' => 'D',
            'updated_by' => 'REG-PWA',
        ]);
    }

    private function attachChildRows(
        string $devoteeKey,
        string $idType,
        string $idNumber,
        string $idStaging,
        string $selfiePath
    ): void {
        if ($idStaging !== '') {
            $idStmt = $this->db->prepare(
                'INSERT INTO devotee_id (Devotee_Key, Devotee_ID_Type, Devotee_ID_Number, Devotee_ID_Image_Gcs_Path)
                 VALUES (:key, :type, :number, :gcs_path)
                 ON DUPLICATE KEY UPDATE
                    Devotee_ID_Image_Gcs_Path = VALUES(Devotee_ID_Image_Gcs_Path),
                    Devotee_ID_Type = VALUES(Devotee_ID_Type),
                    Devotee_ID_Number = VALUES(Devotee_ID_Number)'
            );
            $idStmt->execute([
                'key' => $devoteeKey,
                'type' => $idType,
                'number' => $idNumber,
                'gcs_path' => $idStaging,
            ]);
        }

        if ($selfiePath !== '') {
            $photoStmt = $this->db->prepare(
                'INSERT INTO devotee_photo (Devotee_Key, Devotee_Photo_Gcs_Path)
                 VALUES (:key, :gcs_path)
                 ON DUPLICATE KEY UPDATE Devotee_Photo_Gcs_Path = VALUES(Devotee_Photo_Gcs_Path)'
            );
            $photoStmt->execute(['key' => $devoteeKey, 'gcs_path' => $selfiePath]);
        }
    }

    private function hasAccommodationForEvent(string $devoteeKey, string $eventId): bool
    {
        $stmt = $this->db->prepare(
            "SELECT 1 FROM devotee_accomodation
             WHERE Devotee_Key = :key AND Accommodation_Event = :event AND Accomodation_Status = 'Allocated'
             LIMIT 1"
        );
        $stmt->execute(['key' => $devoteeKey, 'event' => $eventId]);

        return (bool) $stmt->fetchColumn();
    }

    private function sanitizeName(string $value): string
    {
        $value = trim(strip_tags($value));
        if (strlen($value) > 50) {
            $value = substr($value, 0, 50);
        }

        return $value;
    }

    private function sanitizeShort(string $value): string
    {
        $value = trim(strip_tags($value));

        return strlen($value) > 100 ? substr($value, 0, 100) : $value;
    }

    private function sanitizePhone(string $value): string
    {
        $value = preg_replace('/[^\d+\-\s]/', '', trim($value)) ?? '';

        return strlen($value) > 15 ? substr($value, 0, 15) : $value;
    }

    private function sanitizeDate(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        $d = \DateTime::createFromFormat('Y-m-d', $value);

        return ($d && $d->format('Y-m-d') === $value) ? $value : '';
    }

    private function sanitizePath(string $value, string $devoteeKey): string
    {
        $value = trim($value);
        if ($value === '' || str_contains($value, '..')) {
            return '';
        }
        $key = preg_quote(strtoupper($devoteeKey), '#');
        if (!preg_match('#^(id-staging|devotee-selfies)/[0-9]{4}-[0-9]{2}-[0-9]{2}/' . $key . '\.jpg$#i', $value)) {
            return '';
        }

        return $value;
    }
}
