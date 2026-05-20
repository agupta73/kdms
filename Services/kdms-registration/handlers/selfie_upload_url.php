<?php

declare(strict_types=1);

use KdmsRegistration\RegistrationGcs;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    reg_json_response(405, ['error' => 'Method not allowed']);
    exit;
}

$devoteeKey = strtoupper(trim((string) ($_GET['Devotee_Key'] ?? $_GET['devotee_key'] ?? '')));
if ($devoteeKey === '' || !preg_match('/^P[0-9A-Z]+$/', $devoteeKey)) {
    reg_json_response(400, ['error' => 'Devotee_Key is required before selfie upload.']);
    exit;
}

$path = RegistrationGcs::stagingSelfiePath($devoteeKey);
$result = RegistrationGcs::signedPutUrl($path, 900);
if ($result === null) {
    reg_json_response(500, ['error' => 'Could not prepare upload. Please try again.']);
    exit;
}

reg_json_response(200, [
    'upload_url' => $result['upload_url'],
    'selfie_gcs_path' => $result['selfie_gcs_path'],
    'Devotee_Key' => $devoteeKey,
]);
