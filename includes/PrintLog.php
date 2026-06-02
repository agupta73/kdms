<?php

declare(strict_types=1);

/**
 * Append-only print_log for kitchen meal counts.
 *
 * - Idempotent: at most one row per (Devotee_Key, Event_Id, calendar day).
 * - Never updated or deleted by application code (audit / kitchen history).
 */
final class PrintLog
{
    /**
     * Record a physical card print when not already logged for this devotee, event, and day.
     *
     * @return bool true if a new row was inserted, false if already existed or invalid input
     */
    public static function recordIfNotExistsToday(
        PDO $db,
        string $devoteeKey,
        string $eventId,
        string $requestedBy = 'Admin'
    ): bool {
        $devoteeKey = strtoupper(trim($devoteeKey));
        $eventId = preg_replace('/[^\w.-]/', '', trim($eventId)) ?? '';
        $requestedBy = trim($requestedBy);
        if ($requestedBy === '') {
            $requestedBy = 'Admin';
        }

        if ($devoteeKey === '' || $eventId === '') {
            return false;
        }

        $stmt = $db->prepare(
            'INSERT INTO print_log (Devotee_Key, Event_Id, Print_Requested_By_User, Print_Date_Time)
             SELECT :key, :event, :by, NOW()
             FROM DUAL
             WHERE NOT EXISTS (
                 SELECT 1 FROM print_log pl
                 WHERE pl.Devotee_Key = :key_chk
                   AND pl.Event_Id = :event_chk
                   AND DATE(pl.Print_Date_Time) = CURDATE()
             )'
        );

        $stmt->execute([
            'key' => $devoteeKey,
            'event' => $eventId,
            'by' => $requestedBy,
            'key_chk' => $devoteeKey,
            'event_chk' => $eventId,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * @param list<string> $devoteeKeys
     */
    public static function recordManyIfNotExistsToday(
        PDO $db,
        array $devoteeKeys,
        string $eventId,
        string $requestedBy = 'Admin'
    ): void {
        foreach ($devoteeKeys as $key) {
            self::recordIfNotExistsToday($db, (string) $key, $eventId, $requestedBy);
        }
    }
}
