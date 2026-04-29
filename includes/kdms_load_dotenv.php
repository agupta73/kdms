<?php

declare(strict_types=1);

/**
 * Load kdms/.env into getenv/$_ENV/$_SERVER when not already set by the real OS environment.
 * Docker/Apache sometimes does not propagate compose env vars to PHP getenv(); this fixes DB URL resolution.
 *
 * Existing env wins (duplicate keys from .env are skipped).
 */

(function (): void {
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $path = dirname(__DIR__) . '/.env';
    if (! is_readable($path)) {
        return;
    }

    $lines = @file($path, FILE_IGNORE_NEW_LINES);
    if (! is_array($lines)) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strncmp($line, '#', 1) === 0) {
            continue;
        }
        if (! str_contains($line, '=')) {
            continue;
        }
        [$name, $value] = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if ($name === '') {
            continue;
        }
        if ($value !== '' && ($value[0] === '"' || $value[0] === '\'')) {
            $q = $value[0];
            if (strlen($value) >= 2 && $value[strlen($value) - 1] === $q) {
                $value = substr($value, 1, -1);
            }
        }

        if (getenv($name) !== false) {
            continue;
        }
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
})();
