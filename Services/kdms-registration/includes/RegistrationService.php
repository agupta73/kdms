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

        $existing = $this->findRecentIdempotent($idType, $idNumber);
        if ($existing !== null) {
            return ['success' => true, 'Devotee_Key' => $existing, 'action' => 'new'];
        }

        $phone = $this->sanitizePhone((string) ($input['Devotee_Cell_Phone_Number'] ?? ''));
        $dob = $this->sanitizeDate((string) ($input['Devotee_DOB'] ?? ''));
        $station = $this->sanitizeShort((string) ($input['Devotee_Station'] ?? ''));
        $idStaging = $this->sanitizePath((string) ($input['id_staging_gcs_path'] ?? ''));
        $selfiePath = $this->sanitizePath((string) ($input['selfie_gcs_path'] ?? ''));
        $eventId = reg_active_event_id();

        try {
            $this->db->beginTransaction();

            $devoteeKey = GenerateId::generate($this->db);

            $stmt = $this->db->prepare(
                'INSERT INTO devotee (
                    Devotee_Key,
                    Devotee_Type,
                    Devotee_First_Name,
                    Devotee_Last_Name,
                    Devotee_Gender,
                    Devotee_DOB,
                    Devotee_ID_Type,
                    Devotee_ID_Number,
                    Devotee_Station,
                    Devotee_Cell_Phone_Number,
                    Devotee_Status,
                    Devotee_Record_Update_Date_Time,
                    Devotee_Record_Updated_By
                ) VALUES (
                    :key, :type, :first, :last, :gender, :dob, :id_type, :id_number,
                    :station, :phone, :status, NOW(), :updated_by
                )'
            );
            $stmt->execute([
                'key' => $devoteeKey,
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

            if ($idStaging !== '') {
                $idStmt = $this->db->prepare(
                    'INSERT INTO devotee_id (Devotee_Key, Devotee_ID_Type, Devotee_ID_Number, Devotee_ID_Image_Gcs_Path)
                     VALUES (:key, :type, :number, :gcs_path)'
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
                     VALUES (:key, :gcs_path)'
                );
                $photoStmt->execute(['key' => $devoteeKey, 'gcs_path' => $selfiePath]);
            }

            AccommodationAssigner::assignOther($this->db, $devoteeKey, $eventId);

            $this->db->commit();
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            kdms_log('ERROR', 'Registration DB transaction failed', ['error' => $e->getMessage()]);

            return ['success' => false, 'error' => 'Registration could not be completed. Please try again or ask for help at the counter.'];
        }

        $dedupFields = [
            'Devotee_First_Name' => $first,
            'Devotee_Last_Name' => $last,
            'Devotee_ID_Type' => $idType,
            'Devotee_ID_Number' => $idNumber,
        ];
        $dedup = KdmsApiClient::deduplicate($devoteeKey, $dedupFields);
        $survivorKey = $dedup['survivor_key'];

        if ($eventId !== '') {
            KdmsApiClient::addToPrintQueue($survivorKey, $eventId);
        }

        return [
            'success' => true,
            'Devotee_Key' => $survivorKey,
            'action' => $dedup['action'],
        ];
    }

    private function findRecentIdempotent(string $idType, string $idNumber): ?string
    {
        $stmt = $this->db->prepare(
            "SELECT Devotee_Key FROM devotee
             WHERE Devotee_ID_Type = :type
               AND Devotee_ID_Number = :number
               AND Devotee_Status = 'D'
               AND Devotee_Record_Update_Date_Time >= (NOW() - INTERVAL 60 SECOND)
             ORDER BY Devotee_Record_Update_Date_Time DESC
             LIMIT 1"
        );
        $stmt->execute(['type' => $idType, 'number' => $idNumber]);
        $key = $stmt->fetchColumn();

        return $key === false ? null : (string) $key;
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

    private function sanitizePath(string $value): string
    {
        $value = trim($value);
        if ($value === '' || str_contains($value, '..')) {
            return '';
        }
        if (!preg_match('#^(id-staging|devotee-selfies)/[0-9]{4}-[0-9]{2}-[0-9]{2}/[a-f0-9\-]+\.jpg$#i', $value)) {
            return '';
        }

        return $value;
    }
}
