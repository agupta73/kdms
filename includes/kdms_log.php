<?php

declare(strict_types=1);

/**
 * Application logging: PHP errors to stderr (Cloud Run), optional structured lines.
 */

function kdms_log_bootstrap(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    ini_set('log_errors', '1');
    $custom = getenv('KDMS_PHP_ERROR_LOG');
    if (is_string($custom) && $custom !== '') {
        ini_set('error_log', $custom);
    } elseif (file_exists('/proc/self/fd/2') && @is_writable('/proc/self/fd/2')) {
        ini_set('error_log', '/proc/self/fd/2');
    }

    if (getenv('APP_ENV') === 'production' || getenv('KDMS_DISPLAY_ERRORS') === '0') {
        ini_set('display_errors', '0');
    }
}

/**
 * Structured line for Google Cloud Logging (JSON on stderr).
 *
 * @param 'DEBUG'|'INFO'|'NOTICE'|'WARNING'|'ERROR'|'CRITICAL' $severity
 */
function kdms_log(string $severity, string $message, array $context = []): void
{
    kdms_log_bootstrap();
    $base = [
        'severity'  => $severity,
        'message'   => $message,
        'timestamp' => gmdate('c'),
    ];
    if ($context !== []) {
        $base['context'] = $context;
    }
    $line = json_encode($base, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($line !== false) {
        error_log($line);
    }
}
