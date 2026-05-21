<?php

declare(strict_types=1);

/**
 * Kitchen meal-planning counts (Phase 3).
 * Allocated (non–day-visitor) = Allocated accommodation for the event, excluding
 * Devotee_Status D + Devotee_Type T (day visitors counted only via print_log today).
 * Includes own-arrangement, local, and ashram rooms.
 * Day visitors today = distinct day visitors printed today (print_log + devotee D/T).
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
                SELECT COUNT(DISTINCT d.Devotee_Key)
                FROM devotee d
                JOIN devotee_accomodation da ON d.Devotee_Key = da.Devotee_Key
                WHERE da.Accommodation_Event = {$qEvent}
                    AND da.Accomodation_Status = 'Allocated'
                    AND NOT (d.Devotee_Status = 'D' AND d.Devotee_Type = 'T')
            ) AS Residents_Today,
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
                'Residents_Today' => 0,
                'Day_Visitors_Printed_Today' => 0,
                'Total_For_Kitchen' => 0,
            ]];
        }

        $residents = (int) ($row['Residents_Today'] ?? 0);
        $dayVisitors = (int) ($row['Day_Visitors_Printed_Today'] ?? 0);

        return [[
            'Event_ID' => (string) ($row['Event_ID'] ?? $eventId),
            'Residents_Today' => $residents,
            'Day_Visitors_Printed_Today' => $dayVisitors,
            'Total_For_Kitchen' => $residents + $dayVisitors,
        ]];
    }
}
