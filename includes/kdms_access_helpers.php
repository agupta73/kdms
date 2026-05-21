<?php

declare(strict_types=1);

/**
 * Session Access CSV helpers (keys from login / user_access).
 */
function kdms_session_has_asset(string $assetKey): bool
{
    if ($assetKey === '' || empty($_SESSION['Access'])) {
        return false;
    }

    $parts = explode(',', (string) $_SESSION['Access']);

    return in_array($assetKey, $parts, true);
}
