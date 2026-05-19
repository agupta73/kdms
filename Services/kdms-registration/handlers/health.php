<?php

declare(strict_types=1);

use KdmsRegistration\AccommodationAssigner;
use KdmsRegistration\Database;

try {
    $db = (new Database())->pdo();
    AccommodationAssigner::warnIfOtherMissing($db, reg_active_event_id());
} catch (Throwable $e) {
    kdms_log('WARNING', 'Health check DB probe failed', ['error' => $e->getMessage()]);
}

reg_json_response(200, ['status' => 'ok', 'service' => 'kdms-registration']);
