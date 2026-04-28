<?php

/**
 * Application config. Production: set WEBROOT_URL, API_BASE_URL, KDMS_EVENT_ID via environment.
 * Falls back to host-derived URLs for legacy local XAMPP (path .../htdocs/kdms).
 */
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

return [
    'webroot' => $webroot,
    'api_dir' => $api_dir,
    'event_id' => $event_id,
    'check_access' => $checkAccess
];
