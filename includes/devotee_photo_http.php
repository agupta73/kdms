<?php

declare(strict_types=1);

/**
 * Shared devotee photo streaming (Phase 6 Stream B).
 * Used by api/devoteePhoto.php and same-host proxies.
 */

require_once __DIR__ . '/PhotoStorage.php';

/**
 * @return string|null Normalized key or null if invalid
 */
function kdms_validate_devotee_key_for_photo(string $key): ?string
{
    $key = strtoupper(trim($key));
    if ($key === '' || strlen($key) > 12 || !preg_match('/^[A-Z0-9]+$/', $key)) {
        return null;
    }

    return $key;
}

function kdms_transparent_jpeg_placeholder(): string
{
    static $bytes = null;
    if ($bytes === null) {
        $bytes = base64_decode(
            '/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAgGBgcGBQgHBwcJCQgKDBQNDAsLDBkSEw8UHRofHh0aHBwgJC4nICIsIxwcKDcpLDAxNDQ0Hyc5PTgyPC4zNDL/2wBDAQkJCQwLDBgNDRgyIRwhMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjL/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAn/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwCwAA8A/9k=',
            true
        ) ?: '';
    }

    return $bytes;
}

/**
 * Stream JPEG bytes for devotee photo or ID image. Missing image → 200 + 1×1 placeholder.
 */
function kdms_stream_devotee_photo(PDO $db, string $devoteeKey, string $type): void
{
    $type = strtolower(trim($type));
    if ($type !== 'photo' && $type !== 'id') {
        http_response_code(400);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Invalid type';
        exit;
    }

    header('Cache-Control: private, max-age=300');
    header('Content-Type: image/jpeg');

    $result = $type === 'photo'
        ? PhotoStorage::readDevoteePhoto($db, $devoteeKey)
        : PhotoStorage::readDevoteeIdImage($db, $devoteeKey);

    if ($result !== null && $result['bytes'] !== '') {
        echo $result['bytes'];
        exit;
    }

    echo kdms_transparent_jpeg_placeholder();
    exit;
}

/**
 * Proxy stream from kdms-api devoteePhoto.php (session cookie and/or service key).
 */
function kdms_proxy_devotee_photo_from_api(string $devoteeKey, string $type, string $apiBaseUrl): void
{
    $type = strtolower(trim($type));
    if ($type !== 'photo' && $type !== 'id') {
        http_response_code(400);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Invalid type';
        exit;
    }

    $base = rtrim($apiBaseUrl, '/') . '/';
    $url = $base . 'devoteePhoto.php?devotee_key=' . rawurlencode($devoteeKey) . '&type=' . rawurlencode($type);

    kdms_begin_internal_apache_curl();
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    kdms_curl_setopt_internal_cookie($ch);

    $body = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    kdms_end_internal_apache_curl();

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

    if (!is_string($body) || $body === '') {
        http_response_code(502);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Photo service unavailable';
        exit;
    }

    header('Cache-Control: private, max-age=300');
    if ($contentType !== '') {
        header('Content-Type: ' . $contentType);
    } else {
        header('Content-Type: image/jpeg');
    }
    echo $body;
    exit;
}
