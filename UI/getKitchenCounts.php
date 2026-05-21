<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/api_session.php';

header('Content-Type: application/json');

include_once dirname(__DIR__) . '/api/config/database.php';
include_once dirname(__DIR__) . '/api/Interface/clsKitchenDashboard.php';

$eventId = $config_data['event_id'] ?? '';
$debug = false;

$database = new Database();
$db = $database->getConnection();

$kitchen = new clsKitchenDashboard($db);

try {
    $rows = $kitchen->getReport(['eventId' => $eventId]);
    $row = $rows[0] ?? [];
    $status = 'success';
} catch (Throwable $e) {
    $row = [
        'Event_ID' => $eventId,
        'Residents_Today' => 0,
        'Day_Visitors_Printed_Today' => 0,
        'Total_For_Kitchen' => 0,
    ];
    $status = 'error';
    $errorMessage = $e->getMessage();
}

echo json_encode([
    'status' => $status,
    'eventId' => (string) ($row['Event_ID'] ?? $eventId),
    'residentsToday' => (int) ($row['Residents_Today'] ?? 0),
    'dayVisitorsPrintedToday' => (int) ($row['Day_Visitors_Printed_Today'] ?? 0),
    'totalForKitchen' => (int) ($row['Total_For_Kitchen'] ?? 0),
    'refreshTime' => date('H:i:s'),
    'errorMessage' => isset($errorMessage) ? $errorMessage : null,
]);
