<?php

declare(strict_types=1);

/**
 * Same-host proxy for staff duplicate check from form fields.
 */
require_once dirname(__DIR__) . '/includes/web_session.php';
require_once dirname(__DIR__) . '/includes/kdms_internal_http.php';

$config_data = include dirname(__DIR__) . '/site_config.php';

if (!in_array($_SERVER['REQUEST_METHOD'] ?? '', ['GET', 'POST'], true)) {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Method not allowed']);
    exit;
}

$query = $_SERVER['QUERY_STRING'] ?? '';
$apiBase = rtrim((string) ($config_data['api_dir_server'] ?? $config_data['api_dir']), '/') . '/';
$url = $apiBase . 'dedupCheck.php' . ($query !== '' ? '?' . $query : '');

kdms_begin_internal_apache_curl();
$ch = curl_init($url);
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
}
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
kdms_curl_setopt_internal_cookie($ch);

$body = curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
kdms_end_internal_apache_curl();

http_response_code($httpCode > 0 ? $httpCode : 502);
header('Content-Type: application/json');
echo is_string($body) ? $body : json_encode(['status' => false, 'message' => 'Dedup check proxy failed']);
exit;
