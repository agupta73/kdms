<?php

declare(strict_types=1);

/**
 * Mandatory session authentication for KDMS endpoints that return JSON or non-redirect responses.
 */

require_once __DIR__ . '/kdms_log.php';
kdms_log_bootstrap();

if (! defined('KDMS_AUTH_RESPONSE_JSON')) {
    define('KDMS_AUTH_RESPONSE_JSON', true);
}

unset($current_page_id, $GLOBALS['current_page_id']);

/*
 * Prevent initialize.php from curling into JSON APIs (which include this file): that caused
 * nested HTTP back to loadOptions.php and exhaustion of Apache MaxRequestWorkers.
 */
if (! defined('KDMS_SKIP_OPTION_PRELOAD')) {
    define('KDMS_SKIP_OPTION_PRELOAD', true);
}

$trustedService = false;
$configuredServiceKey = getenv('KDMS_SERVICE_KEY');
if (is_string($configuredServiceKey) && $configuredServiceKey !== '') {
    $headerServiceKey = '';
    if (!empty($_SERVER['HTTP_X_KDMS_SERVICE_KEY'])) {
        $headerServiceKey = (string) $_SERVER['HTTP_X_KDMS_SERVICE_KEY'];
    } elseif (!empty($_REQUEST['service_key'])) {
        $headerServiceKey = (string) $_REQUEST['service_key'];
    }

    if ($headerServiceKey !== '' && hash_equals($configuredServiceKey, $headerServiceKey)) {
        $trustedService = true;
        define('KDMS_TRUSTED_SERVICE_AUTH', true);
    }
}

require_once dirname(__DIR__) . '/initialize.php';
if (! $trustedService) {
    require_once dirname(__DIR__) . '/sessionCheck.php';
}
