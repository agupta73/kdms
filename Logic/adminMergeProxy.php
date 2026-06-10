<?php

declare(strict_types=1);

/**
 * Same-host proxy for staff manual merge (session auth on kdms-prod, internal curl to API).
 * Avoids cross-origin POST to kdms-api-prod which fails CORS preflight with 401.
 */
require_once dirname(__DIR__) . '/includes/web_session.php';
require_once dirname(__DIR__) . '/includes/kdms_internal_http.php';

$config_data = include dirname(__DIR__) . '/site_config.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
if (!is_string($raw)) {
    $raw = '';
}

$apiBase = rtrim((string) ($config_data['api_dir_server'] ?? $config_data['api_dir']), '/') . '/';
$url = $apiBase . 'adminMergeDevotees.php';

kdms_begin_internal_apache_curl();
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $raw);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
kdms_curl_setopt_internal_cookie($ch);

$body = curl_exec($ch);
$curlErr = curl_error($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
kdms_end_internal_apache_curl();

if ($body === false || $httpCode <= 0) {
    require_once dirname(__DIR__) . '/includes/kdms_log.php';
    kdms_log('ERROR', 'adminMergeProxy curl failed', [
        'url' => $url,
        'curl_error' => $curlErr !== '' ? $curlErr : '(none)',
        'http_code' => $httpCode,
    ]);
    http_response_code(502);
    header('Content-Type: application/json');
    echo json_encode(['status' => false, 'message' => 'Merge proxy failed: could not reach API']);
    exit;
}

http_response_code($httpCode);
header('Content-Type: application/json');
echo is_string($body) ? $body : json_encode(['status' => false, 'message' => 'Merge proxy failed']);
exit;
