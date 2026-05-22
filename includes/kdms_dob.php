<?php

declare(strict_types=1);

/**
 * Normalize staff/PWA DOB input to ISO Y-m-d (accepts Y-m-d, d-m-Y, d/m/Y).
 */
function kdms_normalize_devotee_dob(string $raw): ?string
{
    $raw = trim($raw);
    if ($raw === '') {
        return null;
    }

    foreach (['Y-m-d', 'd-m-Y', 'd/m/Y'] as $format) {
        $dt = DateTime::createFromFormat('!' . $format, $raw);
        if ($dt === false) {
            continue;
        }
        $errors = DateTime::getLastErrors();
        if (($errors['warning_count'] ?? 0) > 0 || ($errors['error_count'] ?? 0) > 0) {
            continue;
        }
        if ($dt->format($format) !== $raw) {
            continue;
        }

        return $dt->format('Y-m-d');
    }

    return null;
}
