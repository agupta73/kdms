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
        $fields = RegistrationFields::fromRegistrationInput($input);

        if ($fields['first'] === '' || $fields['last'] === '' || $fields['idType'] === '' || $fields['idNumber'] === '') {
            return ['success' => false, 'error' => 'Please fill in all required fields.'];
        }

        $rawEmail = trim(strip_tags((string) ($input['Devotee_Email'] ?? '')));
        if ($rawEmail !== '' && $fields['email'] === '') {
            return ['success' => false, 'error' => 'Please enter a valid email address.'];
        }

        $candidateKey = strtoupper(trim((string) ($input['Devotee_Key'] ?? '')));
        if ($candidateKey === '') {
            $candidateKey = GenerateId::generate($this->db);
        }

        $idPath = $this->sanitizeGcsPath(
            (string) ($input['id_gcs_path'] ?? $input['id_staging_gcs_path'] ?? ''),
            $candidateKey
        );
        $selfiePath = $this->sanitizeGcsPath((string) ($input['selfie_gcs_path'] ?? ''), $candidateKey);
        $eventId = reg_active_event_id();

        $dedup = KdmsApiClient::deduplicate(RegistrationFields::toDedupPayload($candidateKey, $fields, $eventId));
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
                $this->saveDevoteeRow($survivorKey, $fields, true);
                $this->attachChildRows($survivorKey, $fields['idType'], $idPath, $selfiePath);
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
                $this->saveDevoteeRow($survivorKey, $fields, false);
                $this->attachChildRows($survivorKey, $fields['idType'], $idPath, $selfiePath);
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

    /**
     * @param array<string, string> $fields
     */
    private function saveDevoteeRow(string $key, array $fields, bool $overwrite): void
    {
        if ($overwrite) {
            $stmt = $this->db->prepare(
                'UPDATE devotee SET
                    Devotee_First_Name = :first,
                    Devotee_Last_Name = :last,
                    Devotee_Gender = :gender,
                    Devotee_DOB = :dob,
                    Devotee_ID_Type = :id_type,
                    Devotee_ID_Number = :id_number,
                    Devotee_Address_1 = :addr1,
                    Devotee_Address_2 = :addr2,
                    Devotee_Station = :station,
                    Devotee_State = :state,
                    Devotee_Zip = :zip,
                    Devotee_Cell_Phone_Number = :phone,
                    Devotee_Email = :email,
                    Devotee_Referral = :referral,
                    Devotee_Record_Update_Date_Time = NOW(),
                    Devotee_Record_Updated_By = :updated_by
                 WHERE Devotee_Key = :key'
            );
            $stmt->execute($this->devoteeBindParams($key, $fields, 'REG-PWA'));

            return;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO devotee (
                Devotee_Key, Devotee_Type, Devotee_First_Name, Devotee_Last_Name, Devotee_Gender,
                Devotee_DOB, Devotee_ID_Type, Devotee_ID_Number,
                Devotee_Address_1, Devotee_Address_2, Devotee_Station, Devotee_State, Devotee_Zip,
                Devotee_Cell_Phone_Number, Devotee_Email, Devotee_Referral,
                Devotee_Status, Devotee_Record_Update_Date_Time, Devotee_Record_Updated_By
            ) VALUES (
                :key, :type, :first, :last, :gender, :dob, :id_type, :id_number,
                :addr1, :addr2, :station, :state, :zip,
                :phone, :email, :referral, :status, NOW(), :updated_by
            )'
        );
        $params = $this->devoteeBindParams($key, $fields, 'REG-PWA');
        $params['type'] = 'T';
        $params['status'] = 'D';
        $stmt->execute($params);
    }

    /**
     * @param array<string, string> $fields
     * @return array<string, mixed>
     */
    private function devoteeBindParams(string $key, array $fields, string $updatedBy): array
    {
        return [
            'key' => $key,
            'first' => $fields['first'],
            'last' => $fields['last'],
            'gender' => $fields['gender'],
            'dob' => $fields['dob'] !== '' ? $fields['dob'] : null,
            'id_type' => $fields['idType'],
            'id_number' => $fields['idNumber'],
            'addr1' => $fields['address1'],
            'addr2' => $fields['address2'],
            'station' => $fields['station'],
            'state' => $fields['state'],
            'zip' => $fields['zip'],
            'phone' => $fields['phone'],
            'email' => $fields['email'],
            'referral' => $fields['referral'],
            'updated_by' => $updatedBy,
        ];
    }

    private function attachChildRows(
        string $devoteeKey,
        string $idType,
        string $idGcsPath,
        string $selfiePath
    ): void {
        if ($idGcsPath !== '') {
            $idStmt = $this->db->prepare(
                'INSERT INTO devotee_id (Devotee_Key, Devotee_ID_Type, Devotee_ID_Image_Gcs_Path)
                 VALUES (:key, :type, :gcs_path)
                 ON DUPLICATE KEY UPDATE
                    Devotee_ID_Image_Gcs_Path = VALUES(Devotee_ID_Image_Gcs_Path),
                    Devotee_ID_Type = VALUES(Devotee_ID_Type)'
            );
            $idStmt->execute([
                'key' => $devoteeKey,
                'type' => $idType,
                'gcs_path' => $idGcsPath,
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

    private function sanitizeGcsPath(string $value, string $devoteeKey): string
    {
        $value = trim($value);

        return RegistrationGcs::isAllowedPath($value, $devoteeKey) ? $value : '';
    }
}
