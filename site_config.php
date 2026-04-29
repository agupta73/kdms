<?php

/**
 * Application config. Production: set WEBROOT_URL, API_BASE_URL, KDMS_EVENT_ID via environment.
 * Falls back to host-derived URLs for legacy local XAMPP (path .../htdocs/kdms).
 */
require_once __DIR__ . '/includes/kdms_load_dotenv.php';

$directoryName = getenv('KDMS_PATH_SEGMENT') ?: 'kdms';
$directory_seprator = '/';
$event_id = getenv('KDMS_EVENT_ID') ?: '2026JB';
$checkAccess = true;
$rawCheck = getenv('KDMS_CHECK_ACCESS');
if ($rawCheck !== false) {
    $b = filter_var($rawCheck, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
    $checkAccess = $b !== null ? $b : in_array(
        strtolower((string) $rawCheck),
        ['1', 'true', 'yes', 'on'],
        true
    );
}
$phpTz = getenv('PHP_TZ') ?: 'Asia/Kolkata';
date_default_timezone_set($phpTz);

$webrootOverride = getenv('WEBROOT_URL');
$apiOverride = getenv('API_BASE_URL');
$publicUrlOverride = getenv('KDMS_PUBLIC_BASE_URL');

if (!empty($webrootOverride) && !empty($apiOverride)) {
    $webroot = rtrim($webrootOverride, '/') . '/';
    $api_dir = rtrim($apiOverride, '/') . '/';
} else {
    $isHttps = false;
    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $isHttps = true;
    } elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $isHttps = true;
    }
    if (getenv('KDMS_FORCE_HTTPS') === '1') {
        $isHttps = true;
    }
    $protocol = $isHttps ? 'https://' : 'http://';
    if (getenv('KDMS_PROTOCOL') === 'https') {
        $protocol = 'https://';
    } elseif (getenv('KDMS_PROTOCOL') === 'http') {
        $protocol = 'http://';
    }
    if (!empty($publicUrlOverride)) {
        $p = rtrim($publicUrlOverride, '/');
        $webroot = $p . '/';
        $api_dir = $p . '/api/';
    } else {
        $host = ($_SERVER['HTTP_HOST'] ?? 'localhost') . $directory_seprator;
        $webroot = $protocol . $host . $directoryName . $directory_seprator;
        $api_dir = $webroot . 'api' . $directory_seprator;
    }
}

// URLs for PHP server-side HTTP clients (curl) to this app’s API. Set KDMS_INTERNAL_ORIGIN to the
// base URL Apache uses inside the container (e.g. http://127.0.0.1:8080/kdms when using /kdms prefix).
$internal_origin = getenv('KDMS_INTERNAL_ORIGIN');
// If INTERNAL is unset, Docker prefix + PORT still needs loopback:curl must not use http://localhost
// on port 80 when Apache listens on $PORT inside the container (login would hang indefinitely).
if ((! is_string($internal_origin) || $internal_origin === '')
    && getenv('KDMS_APACHE_USE_PREFIX') === '1') {
    $port = getenv('PORT');
    $segment = getenv('KDMS_PATH_SEGMENT');
    $segment = is_string($segment) && $segment !== '' ? $segment : $directoryName;
    if ($port !== false && $port !== '') {
        $internal_origin = 'http://127.0.0.1:' . $port . '/' . trim($segment, '/');
    }
}
if (is_string($internal_origin) && $internal_origin !== '') {
    $internal_origin = rtrim($internal_origin, '/');
    $webroot_server = $internal_origin . '/';
    $api_dir_server = $internal_origin . '/api/';
} else {
    $webroot_server = $webroot;
    $api_dir_server = $api_dir;
}

return [
    'webroot'          => $webroot,
    'webroot_server'   => $webroot_server,
    'api_dir'          => $api_dir,
    'api_dir_server'   => $api_dir_server,
    'event_id'         => $event_id,
    'check_access'     => $checkAccess,
];
