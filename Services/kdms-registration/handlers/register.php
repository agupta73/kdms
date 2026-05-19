<?php

declare(strict_types=1);

use KdmsRegistration\Csrf;
use KdmsRegistration\Database;
use KdmsRegistration\RateLimiter;
use KdmsRegistration\RegistrationService;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    reg_json_response(405, ['error' => 'Method not allowed']);
    exit;
}

if (!RateLimiter::isAllowed(RateLimiter::clientIp())) {
    reg_json_response(429, ['error' => 'Too many requests. Please wait.']);
    exit;
}

$raw = file_get_contents('php://input');
$payload = [];
if (is_string($raw) && trim($raw) !== '') {
    $decoded = json_decode($raw, true);
    if (is_array($decoded)) {
        $payload = $decoded;
    }
}

$csrf = null;
if (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
    $csrf = trim((string) $_SERVER['HTTP_X_CSRF_TOKEN']);
} elseif (!empty($payload['csrf_token'])) {
    $csrf = trim((string) $payload['csrf_token']);
}

if (!Csrf::validate($csrf)) {
    reg_json_response(403, ['success' => false, 'error' => 'Session expired. Please refresh the page and try again.']);
    exit;
}

try {
    $db = (new Database())->pdo();
    $service = new RegistrationService($db);
    $result = $service->register($payload);
    $code = !empty($result['success']) ? 200 : 400;
    reg_json_response($code, $result);
} catch (Throwable $e) {
    kdms_log('ERROR', 'Register handler failed', ['error' => $e->getMessage()]);
    reg_json_response(500, [
        'success' => false,
        'error' => 'Registration could not be completed. Please try again or ask for help at the counter.',
    ]);
}
