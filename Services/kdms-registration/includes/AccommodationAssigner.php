<?php

declare(strict_types=1);

namespace KdmsRegistration;

use PDO;

final class AccommodationAssigner
{
    /** Day-visitor / PWA default accommodation (accommodation_master.Accomodation_Key). */
    public const DAY_VISITOR_ACCOM_KEY = 'othr';

    private const UPDATED_BY = 'REG-PWA';

    public static function warnIfOtherMissing(PDO $db, string $eventId): void
    {
        if ($eventId === '') {
            return;
        }
        $stmt = $db->prepare(
            'SELECT 1 FROM accommodation_master WHERE Accomodation_Key = :key LIMIT 1'
        );
        $stmt->execute(['key' => self::DAY_VISITOR_ACCOM_KEY]);
        if ($stmt->fetchColumn() === false) {
            kdms_log('WARNING', 'accommodation_master missing day-visitor accommodation key', [
                'Accomodation_Key' => self::DAY_VISITOR_ACCOM_KEY,
                'event' => $eventId,
            ]);
        }
    }

    /**
     * Assign day-visitor accommodation (othr), matching PROC_REPLACE_DEVOTEE_W_SEVA_I behaviour:
     * - If already allocated othr for this event → no-op.
     * - If allocated elsewhere for this event → mark Departed and restore availability, then allocate othr.
     */
    public static function assignOther(PDO $db, string $devoteeKey, string $eventId): bool
    {
        if ($eventId === '') {
            kdms_log('WARNING', 'ACTIVE_EVENT_ID not configured; skipping accommodation');

            return false;
        }

        $accomKey = self::DAY_VISITOR_ACCOM_KEY;
        $stmt = $db->prepare(
            'SELECT 1 FROM accommodation_master WHERE Accomodation_Key = :key LIMIT 1'
        );
        $stmt->execute(['key' => $accomKey]);
        if ($stmt->fetchColumn() === false) {
            kdms_log('WARNING', 'No day-visitor accommodation in accommodation_master; skipping insert', [
                'Accomodation_Key' => $accomKey,
            ]);

            return false;
        }

        if (self::hasAllocatedKeyForEvent($db, $devoteeKey, $eventId, $accomKey)) {
            return true;
        }

        self::departOtherAllocationsForEvent($db, $devoteeKey, $eventId, $accomKey);

        $insert = $db->prepare(
            'INSERT INTO devotee_accomodation (
                Accomodation_Key,
                Devotee_Key,
                Accommodation_Event,
                Arrival_Date_Time,
                Departure_Date_Time,
                Accomodation_Status,
                Devotee_Accomodation_Update_Date_Time,
                Devotee_Accomodation_Updated_By
            ) VALUES (
                :accom_key,
                :devotee_key,
                :event_id,
                NOW(),
                NULL,
                :status,
                NOW(),
                :updated_by
            )'
        );
        $insert->execute([
            'accom_key' => $accomKey,
            'devotee_key' => $devoteeKey,
            'event_id' => $eventId,
            'status' => 'Allocated',
            'updated_by' => self::UPDATED_BY,
        ]);

        self::ensureAvailabilityRow($db, $accomKey, $eventId);
        self::incrementAvailability($db, $accomKey, $eventId);

        return true;
    }

    private static function hasAllocatedKeyForEvent(
        PDO $db,
        string $devoteeKey,
        string $eventId,
        string $accomKey
    ): bool {
        $stmt = $db->prepare(
            "SELECT 1 FROM devotee_accomodation
             WHERE Devotee_Key = :key
               AND Accommodation_Event = :event
               AND Accomodation_Status = 'Allocated'
               AND Accomodation_Key = :accom
             LIMIT 1"
        );
        $stmt->execute(['key' => $devoteeKey, 'event' => $eventId, 'accom' => $accomKey]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Depart any other Allocated accommodation for this devotee+event (staff procedure equivalent).
     */
    private static function departOtherAllocationsForEvent(
        PDO $db,
        string $devoteeKey,
        string $eventId,
        string $exceptKey
    ): void {
        $stmt = $db->prepare(
            "SELECT Accomodation_Key FROM devotee_accomodation
             WHERE Devotee_Key = :key
               AND Accommodation_Event = :event
               AND Accomodation_Status = 'Allocated'
               AND Accomodation_Key <> :except"
        );
        $stmt->execute(['key' => $devoteeKey, 'event' => $eventId, 'except' => $exceptKey]);
        $keys = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if ($keys === []) {
            return;
        }

        $depart = $db->prepare(
            "UPDATE devotee_accomodation SET
                Accomodation_Status = 'Departed',
                Departure_Date_Time = NOW(),
                Devotee_Accomodation_Update_Date_Time = NOW(),
                Devotee_Accomodation_Updated_By = :by
             WHERE Devotee_Key = :key
               AND Accommodation_Event = :event
               AND Accomodation_Status = 'Allocated'
               AND Accomodation_Key = :accom"
        );

        foreach ($keys as $oldKey) {
            $depart->execute([
                'by' => self::UPDATED_BY,
                'key' => $devoteeKey,
                'event' => $eventId,
                'accom' => $oldKey,
            ]);
            self::decrementAvailability($db, (string) $oldKey, $eventId);
        }
    }

    private static function ensureAvailabilityRow(PDO $db, string $accomKey, string $eventId): void
    {
        $stmt = $db->prepare(
            'SELECT 1 FROM accommodation_availability
             WHERE Accomodation_Key = :accom AND Accommodation_Event = :event LIMIT 1'
        );
        $stmt->execute(['accom' => $accomKey, 'event' => $eventId]);
        if ($stmt->fetchColumn() !== false) {
            return;
        }

        $ins = $db->prepare(
            'INSERT INTO accommodation_availability (
                Accomodation_Key, Accommodation_Event,
                Allocated_Count, Reserved_Count, Out_of_Availability_Count, Available_Count,
                Availability_Update_Date_Time, Availability_Updated_By
            ) VALUES (:accom, :event, 0, 0, 0, 0, NOW(), :by)'
        );
        try {
            $ins->execute(['accom' => $accomKey, 'event' => $eventId, 'by' => self::UPDATED_BY]);
        } catch (\Throwable $e) {
            kdms_log('WARNING', 'accommodation_availability seed skipped', ['error' => $e->getMessage()]);
        }
    }

    private static function incrementAvailability(PDO $db, string $accomKey, string $eventId): void
    {
        try {
            $upd = $db->prepare(
                'UPDATE accommodation_availability SET
                    Allocated_Count = Allocated_Count + 1,
                    Available_Count = Available_Count - 1
                 WHERE Accomodation_Key = :accom AND Accommodation_Event = :event'
            );
            $upd->execute(['accom' => $accomKey, 'event' => $eventId]);
        } catch (\Throwable $e) {
            kdms_log('WARNING', 'accommodation_availability update skipped', ['error' => $e->getMessage()]);
        }
    }

    private static function decrementAvailability(PDO $db, string $accomKey, string $eventId): void
    {
        try {
            $upd = $db->prepare(
                'UPDATE accommodation_availability SET
                    Allocated_Count = GREATEST(Allocated_Count - 1, 0),
                    Available_Count = Available_Count + 1
                 WHERE Accomodation_Key = :accom AND Accommodation_Event = :event'
            );
            $upd->execute(['accom' => $accomKey, 'event' => $eventId]);
        } catch (\Throwable $e) {
            kdms_log('WARNING', 'accommodation_availability decrement skipped', ['error' => $e->getMessage()]);
        }
    }
}
