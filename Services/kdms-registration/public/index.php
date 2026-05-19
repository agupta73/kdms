<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/bootstrap.php';

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = is_string($path) ? rtrim($path, '/') : '';

if (preg_match('#/api/(csrf-token|ocr-extract|selfie-upload-url|register|health)$#', $path, $m)) {
    $route = $m[1];
    $handler = dirname(__DIR__) . '/handlers/' . str_replace('-', '_', $route) . '.php';
    if (is_file($handler)) {
        require $handler;
        exit;
    }
}

http_response_code(404);
header('Content-Type: application/json');
echo json_encode(['error' => 'Not found']);
