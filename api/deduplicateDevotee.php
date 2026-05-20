<?php

declare(strict_types=1);

/**
 * Deduplication for registration and trusted services (Phase 2).
 * POST JSON: devotee fields + reserved Devotee_Key. Mode register (default) does not INSERT devotee.
 */
require_once __DIR__ . '/../includes/api_session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed']);
    exit;
}

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

$mode = trim((string) ($payload['mode'] ?? 'register'));
$eventId = trim((string) ($payload['eventId'] ?? $payload['ACTIVE_EVENT_ID'] ?? getenv('KDMS_EVENT_ID') ?: getenv('ACTIVE_EVENT_ID') ?: ''));

$first = trim((string) ($payload['Devotee_First_Name'] ?? ''));
$last = trim((string) ($payload['Devotee_Last_Name'] ?? ''));
$idType = trim((string) ($payload['Devotee_ID_Type'] ?? ''));
$idNumber = trim((string) ($payload['Devotee_ID_Number'] ?? ''));

if ($first === '' || $last === '' || $idType === '' || $idNumber === '') {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'action' => 'error',
        'message' => 'Devotee_First_Name, Devotee_Last_Name, Devotee_ID_Type, and Devotee_ID_Number are required',
    ]);
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/../includes/DeduplicationService.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $svc = new DeduplicationService($db, $eventId, 'DEDUP-API');

    if ($mode === 'preview') {
        $check = $svc->findDuplicates($payload);
        echo json_encode([
            'status' => true,
            'recommended_action' => $check['recommended_action'],
            'matches' => $check['matches'],
            'Devotee_Key' => $check['survivor_key'],
            'merge_score' => $check['merge_score'],
        ]);
        exit;
    }

    $persistInsert = ($mode === 'persist');
    $result = $svc->applyDeduplication($payload, $persistInsert);

    echo json_encode([
        'status' => true,
        'action' => $result['action'],
        'Devotee_Key' => $result['devotee_key'],
        'merge_score' => $result['merge_score'],
        'alias_count' => $result['alias_count'],
    ]);
} catch (Throwable $e) {
    require_once __DIR__ . '/../includes/kdms_log.php';
    kdms_log('ERROR', 'deduplicateDevotee failed', ['error' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'action' => 'error',
        'message' => 'Deduplication could not be completed',
    ]);
}
