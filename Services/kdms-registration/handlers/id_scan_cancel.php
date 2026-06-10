<?php

declare(strict_types=1);

use KdmsRegistration\Csrf;
use KdmsRegistration\RegistrationGcs;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    reg_json_response(405, ['error' => 'Method not allowed']);
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

$csrf = null;
if (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
    $csrf = trim((string) $_SERVER['HTTP_X_CSRF_TOKEN']);
} elseif (!empty($payload['csrf_token'])) {
    $csrf = trim((string) $payload['csrf_token']);
}

if (!Csrf::validate($csrf)) {
    reg_json_response(403, ['success' => false, 'error' => 'Session expired. Please refresh the page and try again.']);
    exit;
}

$devoteeKey = strtoupper(trim((string) ($payload['Devotee_Key'] ?? $payload['devotee_key'] ?? '')));
if ($devoteeKey === '' || !preg_match('/^P[0-9A-Z]+$/', $devoteeKey)) {
    reg_json_response(400, ['success' => false, 'error' => 'Devotee_Key is required.']);
    exit;
}

$idPath = trim((string) ($payload['id_gcs_path'] ?? ''));
if ($idPath !== '' && !RegistrationGcs::isAllowedPath($idPath, $devoteeKey)) {
    reg_json_response(400, ['success' => false, 'error' => 'Invalid ID image path.']);
    exit;
}

if ($idPath !== '' && !RegistrationGcs::deleteObject($idPath)) {
    reg_json_response(500, ['success' => false, 'error' => 'Could not remove ID image. Please try again.']);
    exit;
}

reg_json_response(200, ['success' => true]);
