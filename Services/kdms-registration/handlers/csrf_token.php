<?php

declare(strict_types=1);

use KdmsRegistration\Csrf;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    reg_json_response(405, ['error' => 'Method not allowed']);
    exit;
}

reg_json_response(200, ['token' => Csrf::issue()]);
