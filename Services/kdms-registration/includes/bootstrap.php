<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/kdms_log.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

function reg_json_response(int $code, array $body): void
{
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($body, JSON_UNESCAPED_UNICODE);
}

function reg_active_event_id(): string
{
    $id = getenv('ACTIVE_EVENT_ID');
    if (is_string($id) && trim($id) !== '') {
        return trim($id);
    }
    $fallback = getenv('KDMS_EVENT_ID');

    return is_string($fallback) ? trim($fallback) : '';
}

function reg_service_key(): string
{
    $key = getenv('KDMS_SERVICE_KEY');

    return is_string($key) ? $key : '';
}

function reg_api_base(): string
{
    $base = getenv('KDMS_API_BASE_URL');
    if (!is_string($base) || trim($base) === '') {
        return '';
    }
    $base = rtrim(trim($base), '/');
    if (!str_ends_with($base, '/api')) {
        $base .= '/api';
    }

    return $base . '/';
}
