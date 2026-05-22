<?php

declare(strict_types=1);

/**
 * Normalize devotee phone for storage (digits only; India 10-digit after optional +91).
 *
 * @return array{0: string, 1: ?string} [normalized, error message]
 */
function kdms_normalize_devotee_phone(string $raw): array
{
    $raw = trim($raw);
    if ($raw === '') {
        return ['', null];
    }

    $digits = preg_replace('/\D+/', '', $raw) ?? '';
    if ($digits === '') {
        return ['', 'Phone number must contain digits only.'];
    }

    if (strlen($digits) === 12 && str_starts_with($digits, '91')) {
        $digits = substr($digits, 2);
    }
    if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
        $digits = substr($digits, 1);
    }

    if (strlen($digits) > 10) {
        return ['', 'Phone number must be at most 10 digits (use a 10-digit mobile number without country code).'];
    }

    return [$digits, null];
}
