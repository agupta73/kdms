#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * NULL LONGBLOB columns after GCS migration is verified (Phase 6 Stream C).
 * Run ONLY on production after migrate_photos_to_gcs.php completes.
 *
 * Usage:
 *   php scripts/null_blobs_after_migration.php --dry-run
 *   php scripts/null_blobs_after_migration.php --table=photo|id|both --limit=10
 */

$root = dirname(__DIR__);
require_once $root . '/vendor/autoload.php';
require_once $root . '/includes/kdms_load_dotenv.php';
require_once $root . '/includes/kdms_log.php';
require_once $root . '/api/config/database.php';

kdms_log_bootstrap();

$dryRun = in_array('--dry-run', $argv, true);
$limit = 0;
$tableFilter = 'both';
$batchSize = 100;

foreach ($argv as $arg) {
    if (preg_match('/^--limit=(\d+)$/', $arg, $m)) {
        $limit = (int) $m[1];
    }
    if (preg_match('/^--table=(photo|id|both)$/', $arg, $m)) {
        $tableFilter = $m[1];
    }
}

$database = new Database();
$db = $database->getConnection();
if (!$db instanceof PDO) {
    fwrite(STDERR, "Database connection failed.\n");
    exit(1);
}

$targets = [];
if ($tableFilter === 'photo' || $tableFilter === 'both') {
    $targets[] = ['table' => 'devotee_photo', 'blob' => 'Devotee_Photo', 'path' => 'Devotee_Photo_Gcs_Path', 'key' => 'Devotee_Key'];
}
if ($tableFilter === 'id' || $tableFilter === 'both') {
    $targets[] = ['table' => 'devotee_id', 'blob' => 'Devotee_ID_Image', 'path' => 'Devotee_ID_Image_Gcs_Path', 'key' => 'Devotee_Key'];
}

$total = 0;
foreach ($targets as $t) {
    $sql = "SELECT COUNT(*) FROM {$t['table']}
            WHERE {$t['path']} IS NOT NULL AND TRIM({$t['path']}) <> ''
              AND {$t['blob']} IS NOT NULL";
    $total += (int) $db->query($sql)->fetchColumn();
}

echo 'Rows with GCS path and non-null BLOB: ' . $total . PHP_EOL;

if ($dryRun) {
    echo 'Dry-run complete. No rows updated.' . PHP_EOL;
    exit(0);
}

if ($total === 0) {
    echo 'Nothing to null.' . PHP_EOL;
    exit(0);
}

echo 'About to NULL ' . $total . ' BLOB column(s). Type YES to continue: ';
$confirm = trim((string) fgets(STDIN));
if (strtoupper($confirm) !== 'YES') {
    echo "Aborted.\n";
    exit(1);
}

$processed = 0;
foreach ($targets as $t) {
    $remaining = $limit > 0 ? max(0, $limit - $processed) : PHP_INT_MAX;
    while ($remaining > 0) {
        $take = min($batchSize, $remaining);
        $sel = $db->prepare(
            "SELECT {$t['key']} AS row_key FROM {$t['table']}
             WHERE {$t['path']} IS NOT NULL AND TRIM({$t['path']}) <> ''
               AND {$t['blob']} IS NOT NULL
             LIMIT :lim"
        );
        $sel->bindValue(':lim', $take, PDO::PARAM_INT);
        $sel->execute();
        $keys = $sel->fetchAll(PDO::FETCH_COLUMN);
        if ($keys === [] || $keys === false) {
            break;
        }

        $upd = $db->prepare(
            "UPDATE {$t['table']} SET {$t['blob']} = NULL WHERE {$t['key']} = :key LIMIT 1"
        );
        foreach ($keys as $key) {
            $key = (string) $key;
            $upd->execute(['key' => $key]);
            kdms_log('DEBUG', 'Nulled BLOB after GCS migration', [
                'table' => $t['table'],
                'devotee_key' => $key,
            ]);
            $processed++;
            if ($limit > 0 && $processed >= $limit) {
                break 2;
            }
        }
        usleep(100000);
    }
}

echo "Nulled {$processed} BLOB column(s)." . PHP_EOL;
echo 'Verify display is correct, then run manually:' . PHP_EOL;
echo '  OPTIMIZE TABLE devotee_photo;' . PHP_EOL;
echo '  OPTIMIZE TABLE devotee_id;' . PHP_EOL;
echo '  -- Optional Phase 7+: ALTER TABLE devotee_photo DROP COLUMN Devotee_Photo;' . PHP_EOL;
echo '  -- Only after confirming zero non-null rows and a verified backup exists.' . PHP_EOL;
