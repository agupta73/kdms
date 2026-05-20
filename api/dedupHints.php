<?php

declare(strict_types=1);

/**
 * Staff UI: potential duplicate hints (read-only).
 * GET ?devotee_key=P...&eventId=...
 */
require_once __DIR__ . '/../includes/api_session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed']);
    exit;
}

$devoteeKey = trim((string) ($_GET['devotee_key'] ?? ''));
if ($devoteeKey === '') {
    http_response_code(400);
    echo json_encode(['status' => false, 'message' => 'devotee_key is required']);
    exit;
}

$eventId = trim((string) ($_GET['eventId'] ?? getenv('KDMS_EVENT_ID') ?: ''));

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/../includes/DeduplicationService.php';

$database = new Database();
$db = $database->getConnection();

$sel = $db->prepare('SELECT * FROM devotee WHERE Devotee_Key = :k LIMIT 1');
$sel->execute(['k' => strtoupper($devoteeKey)]);
$row = $sel->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    http_response_code(404);
    echo json_encode(['status' => false, 'message' => 'Devotee not found']);
    exit;
}

$svc = new DeduplicationService($db, $eventId, $_SESSION['LoginID'] ?? 'STAFF');
$check = $svc->findDuplicates($row);

echo json_encode([
    'status' => true,
    'devotee_key' => strtoupper($devoteeKey),
    'recommended_action' => $check['recommended_action'],
    'merge_score' => $check['merge_score'],
    'matches' => $check['matches'],
]);
