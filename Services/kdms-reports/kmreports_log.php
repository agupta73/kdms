<?php

declare(strict_types=1);

/**
 * KMReports logging helpers (stderr JSON lines for Cloud Run / Docker logs).
 */
function kmreports_log_bootstrap(): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    ini_set('log_errors', '1');
    if (file_exists('/proc/self/fd/2') && @is_writable('/proc/self/fd/2')) {
        ini_set('error_log', '/proc/self/fd/2');
    }
    if (getenv('KMREPORTS_DISPLAY_ERRORS') === '0') {
        ini_set('display_errors', '0');
    }
}

function kmreports_log(string $severity, string $message, array $context = []): void
{
    kmreports_log_bootstrap();
    $payload = [
        'severity'  => $severity,
        'message'   => $message,
        'timestamp' => gmdate('c'),
    ];
    if ($context !== []) {
        $payload['context'] = $context;
    }
    $line = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($line !== false) {
        error_log($line);
    }
}
