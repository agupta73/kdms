<?php

declare(strict_types=1);

/**
 * Stream devotee photo or ID image (Phase 6 Stream B).
 * Auth: staff session or X-KDMS-SERVICE-KEY. Missing image → 200 + 1×1 JPEG placeholder.
 */
require_once __DIR__ . '/../includes/api_session.php';
require_once __DIR__ . '/../includes/devotee_photo_http.php';
require_once __DIR__ . '/config/database.php';

$trusted = defined('KDMS_TRUSTED_SERVICE_AUTH') && KDMS_TRUSTED_SERVICE_AUTH;
if (!$trusted && empty($_SESSION['LoginID'])) {
    http_response_code(401);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Unauthorized';
    exit;
}

$rawKey = (string) ($_GET['devotee_key'] ?? '');
$type = (string) ($_GET['type'] ?? 'photo');
$devoteeKey = kdms_validate_devotee_key_for_photo($rawKey);

if ($devoteeKey === null) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Invalid devotee_key';
    exit;
}

$database = new Database();
$db = $database->getConnection();
kdms_stream_devotee_photo($db, $devoteeKey, $type);
