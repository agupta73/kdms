<?php

declare(strict_types=1);

/**
 * Kitchen meal-planning counts (Phase 3).
 *
 * Residents: distinct devotees with a print_log row for the event (any print date),
 *   excluding day visitors (Devotee_Status D + Devotee_Type T).
 * Day visitors: distinct D/T devotees with print_log for the event on the current calendar day only.
 *
 * PWA registration queues card_print_log via addToPrintQueue; print_log is append-only and
 * idempotent (one row per devotee + event + calendar day via PrintLog::recordIfNotExistsToday).
 * Written on removeFromPrintQueue when eventId is supplied. Rows are never deleted by the app.
 * Merged duplicates are not counted until the survivor's card is printed.
 */
class clsKitchenDashboard
{
    private PDO $conn;

    private bool $debug = false;

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * @param array<string, mixed> $requestData
     * @return list<array<string, int|string>>
     */
    public function getReport(array $requestData): array
    {
        $eventId = trim((string) ($requestData['eventId'] ?? ''));
        if ($eventId === '') {
            $eventId = date('Y') . 'JB';
        }

        $qEvent = $this->conn->quote($eventId);

        $query = "SELECT
            {$qEvent} AS Event_ID,
            (
                SELECT COUNT(DISTINCT pl.Devotee_Key)
                FROM print_log pl
                INNER JOIN devotee d ON pl.Devotee_Key = d.Devotee_Key
                WHERE pl.Event_Id = {$qEvent}
                    AND NOT (d.Devotee_Status = 'D' AND d.Devotee_Type = 'T')
            ) AS Residents_Printed_For_Event,
            (
                SELECT COUNT(DISTINCT pl.Devotee_Key)
                FROM print_log pl
                INNER JOIN devotee d ON pl.Devotee_Key = d.Devotee_Key
                WHERE pl.Event_Id = {$qEvent}
                    AND DATE(pl.Print_Date_Time) = CURDATE()
                    AND d.Devotee_Status = 'D'
                    AND d.Devotee_Type = 'T'
            ) AS Day_Visitors_Printed_Today
        ";

        if ($this->debug) {
            echo $query;
            die;
        }

        $result = $this->conn->query($query);
        $row = $result !== false ? $result->fetch(PDO::FETCH_ASSOC) : false;
        if ($row === false) {
            return [[
                'Event_ID' => $eventId,
                'Residents_Printed_For_Event' => 0,
                'Day_Visitors_Printed_Today' => 0,
                'Total_For_Kitchen' => 0,
            ]];
        }

        $residents = (int) ($row['Residents_Printed_For_Event'] ?? 0);
        $dayVisitors = (int) ($row['Day_Visitors_Printed_Today'] ?? 0);

        return [[
            'Event_ID' => (string) ($row['Event_ID'] ?? $eventId),
            'Residents_Printed_For_Event' => $residents,
            'Day_Visitors_Printed_Today' => $dayVisitors,
            'Total_For_Kitchen' => $residents + $dayVisitors,
        ]];
    }
}
