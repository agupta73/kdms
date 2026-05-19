<?php

declare(strict_types=1);

namespace KdmsRegistration;

final class Csrf
{
    private const TTL_SECONDS = 1800;
    private const SESSION_KEY = 'kdms_reg_csrf';

    public static function issue(): string
    {
        $token = bin2hex(random_bytes(32));
        $_SESSION[self::SESSION_KEY] = [
            'token' => $token,
            'expires' => time() + self::TTL_SECONDS,
        ];

        return $token;
    }

    public static function validate(?string $token): bool
    {
        if ($token === null || $token === '') {
            return false;
        }
        $stored = $_SESSION[self::SESSION_KEY] ?? null;
        if (!is_array($stored) || empty($stored['token']) || empty($stored['expires'])) {
            return false;
        }
        if (time() > (int) $stored['expires']) {
            return false;
        }

        return hash_equals((string) $stored['token'], $token);
    }

    public static function tokenFromRequest(): ?string
    {
        if (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
            return trim((string) $_SERVER['HTTP_X_CSRF_TOKEN']);
        }
        $raw = file_get_contents('php://input');
        if (is_string($raw) && $raw !== '') {
            $json = json_decode($raw, true);
            if (is_array($json) && !empty($json['csrf_token'])) {
                return trim((string) $json['csrf_token']);
            }
        }

        return null;
    }
}
