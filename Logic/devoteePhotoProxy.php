<?php

declare(strict_types=1);

/**
 * Same-host proxy for lazy grid/report images (kdms-prod → kdms-api devoteePhoto.php).
 */
require_once dirname(__DIR__) . '/includes/web_session.php';
require_once dirname(__DIR__) . '/includes/kdms_internal_http.php';
require_once dirname(__DIR__) . '/includes/devotee_photo_http.php';

$config_data = include dirname(__DIR__) . '/site_config.php';

$rawKey = (string) ($_GET['devotee_key'] ?? '');
$type = (string) ($_GET['type'] ?? 'photo');
$devoteeKey = kdms_validate_devotee_key_for_photo($rawKey);

if ($devoteeKey === null) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Invalid devotee_key';
    exit;
}

$apiBase = $config_data['api_dir_server'] ?? $config_data['api_dir'];
kdms_proxy_devotee_photo_from_api($devoteeKey, $type, (string) $apiBase);
