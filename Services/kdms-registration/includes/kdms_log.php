<?php

declare(strict_types=1);

function kdms_log(string $level, string $message, array $context = []): void
{
    $line = '[' . $level . '] ' . $message;
    if ($context !== []) {
        $safe = [];
        foreach ($context as $k => $v) {
            $key = strtolower((string) $k);
            if (str_contains($key, 'password') || str_contains($key, 'secret') || str_contains($key, 'key')
                || str_contains($key, 'id_number') || str_contains($key, 'dob') || str_contains($key, 'gcs_path')) {
                continue;
            }
            $safe[$k] = $v;
        }
        if ($safe !== []) {
            $line .= ' ' . json_encode($safe);
        }
    }
    error_log($line);
}
