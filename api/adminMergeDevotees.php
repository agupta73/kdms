<?php

declare(strict_types=1);

/**
 * Manual merge from admin UI (session auth). POST JSON: base_devotee_key, tbm_devotee_keys[].
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

$baseKey = strtoupper(trim((string) ($payload['base_devotee_key'] ?? $payload['Devotee_Key'] ?? '')));
$tbm = $payload['tbm_devotee_keys'] ?? $payload['merge_keys'] ?? [];
if (!is_array($tbm)) {
    $tbm = [];
}
$tbmKeys = array_values(array_filter(array_map(
    static fn ($k) => strtoupper(trim((string) $k)),
    $tbm
)));

if ($baseKey === '' || $tbmKeys === []) {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'base_devotee_key and tbm_devotee_keys are required']);
    exit;
}

$eventId = trim((string) ($payload['eventId'] ?? getenv('KDMS_EVENT_ID') ?: ''));
$updatedBy = (string) ($_SESSION['LoginID'] ?? 'ADMIN');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/../includes/DeduplicationService.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    $svc = new DeduplicationService($db, $eventId, $updatedBy);
    $survivor = $svc->mergeRecords($baseKey, $tbmKeys, $payload, 'manual', 100);
    echo json_encode([
        'status' => true,
        'action' => 'merged',
        'Devotee_Key' => $survivor,
    ]);
} catch (Throwable $e) {
    require_once __DIR__ . '/../includes/kdms_log.php';
    kdms_log('ERROR', 'adminMergeDevotees failed', ['error' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Merge failed']);
}
