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
 * Lazy load: 302 to signed GCS URL when path exists; else stream BLOB/GCS bytes.
 * Missing image → 200 + 1×1 placeholder.
 */
function kdms_serve_devotee_image(PDO $db, string $devoteeKey, string $type): void
{
    $type = strtolower(trim($type));
    if ($type !== 'photo' && $type !== 'id') {
        http_response_code(400);
        header('Content-Type: text/plain; charset=UTF-8');
        echo 'Invalid type';
        exit;
    }

    $gcsPath = PhotoStorage::resolveGcsObjectPath($db, $devoteeKey, $type);
    if ($gcsPath !== null) {
        $signed = PhotoStorage::signedUrl($gcsPath);
        if ($signed !== null) {
            header('Cache-Control: private, max-age=300');
            header('Location: ' . $signed, true, 302);
            exit;
        }
    }

    kdms_stream_devotee_photo($db, $devoteeKey, $type);
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
 * Proxy from kdms-api devoteePhoto.php. Passes 302 to GCS through to the browser.
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
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    kdms_curl_setopt_internal_cookie($ch);

    $response = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
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
}
