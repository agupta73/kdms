<?php

declare(strict_types=1);

use KdmsRegistration\Database;
use KdmsRegistration\GenerateId;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    reg_json_response(405, ['error' => 'Method not allowed']);
    exit;
}

try {
    $db = (new Database())->pdo();
    $key = GenerateId::generate($db);
    reg_json_response(200, ['Devotee_Key' => $key]);
} catch (Throwable $e) {
    kdms_log('ERROR', 'reserve-devotee-key failed', ['error' => $e->getMessage()]);
    reg_json_response(500, ['error' => 'Could not reserve devotee key']);
}
