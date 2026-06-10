<?php

declare(strict_types=1);

/**
 * kmreports same-host proxy → kdms-api devoteePhoto.php (Phase 6 Stream B).
 * Passes 302 redirects to GCS through to the browser.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once dirname(__DIR__) . '/sessionCheck.php';

$rawKey = (string) ($_GET['devotee_key'] ?? '');
$type = strtolower(trim((string) ($_GET['type'] ?? 'photo')));
$key = strtoupper(trim($rawKey));

if ($key === '' || strlen($key) > 12 || !preg_match('/^[A-Z0-9]+$/', $key)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Invalid devotee_key';
    exit;
}

if ($type !== 'photo' && $type !== 'id') {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Invalid type';
    exit;
}

$config = include dirname(__DIR__) . '/site_config.php';
$apiBase = rtrim((string) $config['api_dir'], '/') . '/';
$url = $apiBase . 'devoteePhoto.php?devotee_key=' . rawurlencode($key) . '&type=' . rawurlencode($type);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$serviceKey = getenv('KDMS_SERVICE_KEY');
if (is_string($serviceKey) && $serviceKey !== '') {
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['X-KDMS-SERVICE-KEY: ' . $serviceKey]);
}

$response = curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
curl_close($ch);

if ($httpCode === 401) {
    http_response_code(401);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Unauthorized';
    exit;
}

if ($httpCode === 400) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Bad request';
    exit;
}

if (!is_string($response)) {
    http_response_code(502);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Photo service unavailable';
    exit;
}

$rawHeaders = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

if ($httpCode === 302 || $httpCode === 301) {
    $location = null;
    foreach (preg_split('/\r\n|\n|\r/', $rawHeaders) as $line) {
        if (stripos($line, 'Location:') === 0) {
            $location = trim(substr($line, 9));
            break;
        }
    }
    if ($location !== null && $location !== '') {
        header('Cache-Control: private, max-age=300');
        header('Location: ' . $location, true, $httpCode);
        exit;
    }
}

if ($body === '') {
    http_response_code(502);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'Photo service unavailable';
    exit;
}

$contentType = 'image/jpeg';
foreach (preg_split('/\r\n|\n|\r/', $rawHeaders) as $line) {
    if (stripos($line, 'Content-Type:') === 0) {
        $contentType = trim(substr($line, 13));
        break;
    }
}

header('Cache-Control: private, max-age=300');
header('Content-Type: ' . $contentType);
echo $body;
exit;
