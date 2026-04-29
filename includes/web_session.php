<?php

declare(strict_types=1);

/**
 * Mandatory auth for KDMS HTML pages. Include as the very first dependency (before any output).
 */

require_once __DIR__ . '/kdms_log.php';
kdms_log_bootstrap();

$scriptBasename = basename($_SERVER['SCRIPT_FILENAME'] ?? '');

if ($scriptBasename === 'login.php') {
    throw new RuntimeException('Do not load web_session.php from login.php');
}

$pageMapPath = __DIR__ . '/kdms_web_page_ids.php';

if (! is_readable($pageMapPath)) {
    kdms_log('ERROR', 'kdms_web_page_ids.php missing', []);
    http_response_code(500);
    echo 'KDMS configuration error.';
    exit;
}

/** @var array<string, string> $pageMap */
$pageMap = require $pageMapPath;

if (! isset($pageMap[$scriptBasename])) {
    kdms_log('ERROR', 'Missing web page ACL mapping', ['script' => $scriptBasename]);
    http_response_code(500);
    echo 'KDMS configuration error: missing page mapping for this URL.';
    exit;
}

$current_page_id = $pageMap[$scriptBasename];

require_once dirname(__DIR__) . '/initialize.php';
require_once dirname(__DIR__) . '/sessionCheck.php';

/*
 * PHP → Apache CURL helpers (kdms_begin/kdms_end) call session_write_close() then session_start().
 * If markup was already echoed to the browser, php.ini may forbid sending session cookies afterward.
 * Starting one output buffer defers flushing so HTTP headers (and cookie headers) remain sendable until
 * the buffer is flushed — required for authenticated internal curls on UI pages after any output.
 */
if (function_exists('ob_start') && ob_get_level() === 0) {
    ob_start();
}
