<?php

/**
 * Landing redirect when DirectoryIndex hits. Honors WEBROOT_URL path (e.g. /kdms for Docker prefix).
 */
$prefix = '';
$wu = getenv('WEBROOT_URL');
if (is_string($wu) && $wu !== '') {
    $path = parse_url($wu, PHP_URL_PATH);
    if (is_string($path) && $path !== '' && $path !== '/') {
        $prefix = rtrim($path, '/');
    }
}

header('Location: ' . $prefix . '/UI/login.php', true, 302);
exit;
