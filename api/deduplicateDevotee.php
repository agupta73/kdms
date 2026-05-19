<?php

declare(strict_types=1);

/**
 * Deduplication endpoint (Phase 2 will implement full logic).
 * Phase 1.5 stub: always returns inserted with same Devotee_Key.
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
    echo json_encode([
        'status' => false,
        'action' => 'error',
        'message' => 'devotee_key is required',
    ]);
    exit;
}

echo json_encode([
    'status' => true,
    'action' => 'inserted',
    'Devotee_Key' => $devoteeKey,
    'merge_score' => 0,
    'alias_count' => 0,
    'stub' => true,
]);
