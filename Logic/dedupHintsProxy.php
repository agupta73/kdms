<?php

declare(strict_types=1);

/**
 * Same-host proxy for staff duplicate hints (kdms-prod → kdms-api dedupHints.php).
 */
require_once dirname(__DIR__) . '/includes/web_session.php';
require_once dirname(__DIR__) . '/includes/kdms_internal_http.php';

$config_data = include dirname(__DIR__) . '/site_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Method not allowed']);
    exit;
}

$query = $_SERVER['QUERY_STRING'] ?? '';
$apiBase = rtrim((string) ($config_data['api_dir_server'] ?? $config_data['api_dir']), '/') . '/';
$url = $apiBase . 'dedupHints.php' . ($query !== '' ? '?' . $query : '');

kdms_begin_internal_apache_curl();
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
kdms_curl_setopt_internal_cookie($ch);

$body = curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
kdms_end_internal_apache_curl();

http_response_code($httpCode > 0 ? $httpCode : 502);
header('Content-Type: application/json');
echo is_string($body) ? $body : json_encode(['status' => false, 'message' => 'Dedup hints proxy failed']);
exit;
