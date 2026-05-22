<?php

declare(strict_types=1);

/**
 * Same-host proxy for staff photo / ID uploads (kdms-prod → kdms-api managePhoto.php).
 */
require_once dirname(__DIR__) . '/includes/web_session.php';
require_once dirname(__DIR__) . '/includes/kdms_internal_http.php';

$config_data = include dirname(__DIR__) . '/site_config.php';

if ($_POST === [] && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($contentLength > 0 && empty($_FILES)) {
        http_response_code(413);
        header('Content-Type: application/json');
        echo json_encode([
            'message' => 'Upload payload too large for server limits (post_max_size). Try a smaller image.',
            'status' => false,
        ]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Method not allowed', 'status' => false]);
    exit;
}

$apiBase = rtrim((string) ($config_data['api_dir_server'] ?? $config_data['api_dir']), '/') . '/';
$url = $apiBase . 'managePhoto.php';

$post = $_POST;
if (!empty($_FILES['id_image']['tmp_name']) && is_uploaded_file((string) $_FILES['id_image']['tmp_name'])) {
    $file = $_FILES['id_image'];
    $mime = $file['type'] ?? 'image/jpeg';
    $post['id_image'] = new CURLFile((string) $file['tmp_name'], (string) $mime, $file['name'] ?? 'id_image.jpg');
}

kdms_begin_internal_apache_curl();
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
kdms_curl_setopt_internal_cookie($ch);

$body = curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
kdms_end_internal_apache_curl();

http_response_code($httpCode > 0 ? $httpCode : 502);
header('Content-Type: application/json');
echo is_string($body) ? $body : json_encode(['message' => 'Photo proxy failed', 'status' => false]);
exit;
