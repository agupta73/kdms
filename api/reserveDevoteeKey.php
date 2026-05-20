<?php

declare(strict_types=1);

/**
 * Reserve a Devotee_Key for staff add-devotee / photo-ID upload before main save (Phase 2).
 * Does not insert into devotee — only allocates an unused key.
 */
require_once __DIR__ . '/../includes/api_session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed']);
    exit;
}

include_once __DIR__ . '/config/database.php';
include_once __DIR__ . '/Interface/devotees.php';

$database = new Database();
$db = $database->getConnection();
$devotee = new Devotee($db);
$key = $devotee->generateId();

echo json_encode([
    'status' => true,
    'Devotee_Key' => $key,
]);
