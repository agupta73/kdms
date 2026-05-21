#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * One-time repair: collapse multiple devotee_photo / devotee_id rows per Devotee_Key.
 *
 * Usage:
 *   php scripts/repair_duplicate_photo_id_rows.php P16200766
 *   php scripts/repair_duplicate_photo_id_rows.php --all
 *
 * DB (pick one):
 *   cp .env.example .env   # edit KDMS_DB_* for your MySQL
 *   KDMS_DB_HOST=127.0.0.1:3306 KDMS_DB_PASSWORD=secret php scripts/repair_duplicate_photo_id_rows.php P16200766
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "CLI only.\n");
    exit(1);
}

$root = dirname(__DIR__);
require_once $root . '/includes/kdms_cli_database.php';
require_once $root . '/includes/DeduplicationService.php';

$arg = $argv[1] ?? '';
if ($arg === '' || $arg === '-h' || $arg === '--help') {
    fwrite(STDERR, "Usage: php scripts/repair_duplicate_photo_id_rows.php DEVOTEE_KEY\n");
    fwrite(STDERR, "       php scripts/repair_duplicate_photo_id_rows.php --all\n");
    exit($arg === '' ? 1 : 0);
}

try {
    $db = kdms_cli_connect_database();
} catch (PDOException $e) {
    fwrite(STDERR, 'MySQL connection failed: ' . $e->getMessage() . "\n");
    exit(1);
}

$eventId = getenv('KDMS_EVENT_ID') ?: '2026JB';
$svc = new DeduplicationService($db, $eventId, 'REPAIR-SCRIPT');

if ($arg === '--all') {
    $keys = [];
    foreach (['devotee_photo', 'devotee_id'] as $table) {
        $sql = "SELECT Devotee_Key FROM {$table} GROUP BY Devotee_Key HAVING COUNT(*) > 1";
        foreach ($db->query($sql)->fetchAll(PDO::FETCH_COLUMN) as $k) {
            $keys[(string) $k] = true;
        }
    }
    $keys = array_keys($keys);
    if ($keys === []) {
        echo "No duplicate photo/id rows found.\n";
        exit(0);
    }
    foreach ($keys as $key) {
        $svc->repairDuplicatePhotoAndIdRows($key);
        echo "Repaired {$key}\n";
    }
    exit(0);
}

$svc->repairDuplicatePhotoAndIdRows($arg);
echo "Repaired {$arg}\n";
