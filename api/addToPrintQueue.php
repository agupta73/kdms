<?php

declare(strict_types=1);

/**
 * Add devotee card(s) to print queue (service-key or session auth).
 * POST JSON or form: devotee_key (required), eventId (optional), print_requested_by (optional, default REG-PWA).
 */
require_once __DIR__ . '/../includes/api_session.php';

header('Content-Type: application/json');

$raw = file_get_contents('php://input');
$payload = [];
if (is_string($raw) && trim($raw) !== '') {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $payload = $decoded;
    }
}
if ($payload === []) {
    $payload = $_POST;
}

$devoteeKey = trim((string) ($payload['devotee_key'] ?? $payload['Devotee_Key'] ?? ''));
if ($devoteeKey === '') {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'devotee_key is required']);
    exit;
}

$requestData = [
    'requestType' => 'addToPrintQueue',
    'devotee_key' => $devoteeKey,
];
if (!empty($payload['eventId'])) {
    $requestData['eventId'] = trim((string) $payload['eventId']);
}

include_once __DIR__ . '/config/database.php';
include_once __DIR__ . '/Interface/devotees.php';

$database = new Database();
$db = $database->getConnection();
$devotee = new Devotee($db);

$res = $devotee->manageCardPrinting($requestData);

if (!empty($res['status'])) {
    echo json_encode([
        'status' => true,
        'message' => $res['message'] ?? '',
        'Devotee_Key' => $res['info'] ?? $devoteeKey,
    ]);
    exit;
}

http_response_code(500);
echo json_encode([
    'status' => false,
    'message' => $res['message'] ?? 'Failed to add to print queue',
]);
