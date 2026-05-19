<?php

declare(strict_types=1);

namespace KdmsRegistration;

final class RateLimiter
{
    public const LIMIT = 30;
    public const WINDOW_SECONDS = 60;

    public static function isAllowed(string $clientKey): bool
    {
        $now = time();
        $file = sys_get_temp_dir() . '/kdms_reg_rl_' . md5($clientKey) . '.json';
        $hits = [];

        if (is_file($file)) {
            $raw = @file_get_contents($file);
            if (is_string($raw) && $raw !== '') {
                $decoded = json_decode($raw, true);
                if (is_array($decoded)) {
                    $hits = $decoded;
                }
            }
        }

        $hits = array_values(array_filter($hits, static fn ($t) => is_int($t) && $t > $now - self::WINDOW_SECONDS));
        if (count($hits) >= self::LIMIT) {
            return false;
        }

        $hits[] = $now;
        @file_put_contents($file, json_encode($hits), LOCK_EX);

        return true;
    }

    public static function clientIp(): string
    {
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $parts = explode(',', (string) $_SERVER['HTTP_X_FORWARDED_FOR']);

            return trim($parts[0]);
        }

        return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }
}
