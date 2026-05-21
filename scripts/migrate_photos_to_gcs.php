#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * One-time migration: copy devotee_photo / devotee_id LONGBLOBs to GCS.
 * Does NOT null BLOB columns. Idempotent (skips rows with Gcs_Path already set).
 *
 * Memory-safe: dry-run never loads BLOBs; live mode processes one row at a time.
 *
 * Usage:
 *   php scripts/migrate_photos_to_gcs.php --dry-run
 *   php scripts/migrate_photos_to_gcs.php --limit=100
 *   php scripts/migrate_photos_to_gcs.php --batch-size=25
 *   php scripts/migrate_photos_to_gcs.php --sample=5          (dry-run: print 5 example keys per table)
 *
 * From Mac/host PHP (not Docker), MySQL is usually localhost:
 *   KDMS_DB_HOST=127.0.0.1:3306 php scripts/migrate_photos_to_gcs.php --dry-run
 *
 * Production: run from Cloud Shell with Cloud SQL Auth Proxy; use batches, e.g.:
 *   php scripts/migrate_photos_to_gcs.php --limit=500
 *   (repeat until counts reach zero; or run full — one BLOB in memory at a time)
 *
 * Requires: composer install (google/cloud-storage), DB env vars, GCP ADC for live run.
 */

$root = dirname(__DIR__);
require_once $root . '/vendor/autoload.php';
require_once $root . '/includes/kdms_load_dotenv.php';
require_once $root . '/includes/kdms_log.php';
require_once $root . '/includes/PhotoStorage.php';
require_once $root . '/api/config/database.php';

kdms_log_bootstrap();

$dryRun = in_array('--dry-run', $argv, true);
$report = in_array('--report', $argv, true);
$limit = 0;
$batchSize = 100;
$sampleLines = 3;
$tableFilter = 'both';
$maxBlobBytes = (int) (getenv('KDMS_MIGRATE_MAX_BLOB_MB') ?: '20') * 1024 * 1024;

foreach ($argv as $arg) {
    if (preg_match('/^--limit=(\d+)$/', $arg, $m)) {
        $limit = (int) $m[1];
    }
    if (preg_match('/^--batch-size=(\d+)$/', $arg, $m)) {
        $batchSize = max(1, (int) $m[1]);
    }
    if (preg_match('/^--sample=(\d+)$/', $arg, $m)) {
        $sampleLines = max(0, (int) $m[1]);
    }
    if (preg_match('/^--table=(photo|id|both)$/', $arg, $m)) {
        $tableFilter = $m[1];
    }
}

$db = migratePhotosConnectDatabase();

echo 'Bucket: ' . PhotoStorage::bucketName() . PHP_EOL;
echo 'Mode: ' . ($dryRun ? 'DRY-RUN (no BLOBs loaded)' : 'LIVE (one BLOB per row)') . PHP_EOL;
if (!$dryRun) {
    echo 'Max blob size: ' . ($maxBlobBytes / 1024 / 1024) . ' MB (set KDMS_MIGRATE_MAX_BLOB_MB to override)' . PHP_EOL;
}

$stats = [
    'photo_would_migrate' => 0,
    'photo_migrated' => 0,
    'photo_already_path' => 0,
    'photo_no_blob' => 0,
    'photo_skipped' => 0,
    'id_would_migrate' => 0,
    'id_migrated' => 0,
    'id_already_path' => 0,
    'id_no_blob' => 0,
    'id_skipped' => 0,
    'errors' => 0,
    'oversized_skipped' => 0,
];

migratePrintBaselineCounts($db, $report);

if ($tableFilter === 'photo' || $tableFilter === 'both') {
migrateTable(
    $db,
    'devotee_photo',
    'Devotee_Key',
    'Devotee_Photo',
    'Devotee_Photo_Gcs_Path',
    static fn (string $key): string => PhotoStorage::objectPathForPhoto($key),
    $stats,
    'photo',
    $dryRun,
    $batchSize,
    $limit,
    $sampleLines,
    $maxBlobBytes
);
}

if ($tableFilter === 'id' || $tableFilter === 'both') {
migrateTable(
    $db,
    'devotee_id',
    'Devotee_Key',
    'Devotee_ID_Image',
    'Devotee_ID_Image_Gcs_Path',
    static fn (string $key): string => PhotoStorage::objectPathForIdImage($key),
    $stats,
    'id',
    $dryRun,
    $batchSize,
    $limit,
    $sampleLines,
    $maxBlobBytes
);
}

echo PHP_EOL . 'Summary:' . PHP_EOL;
print_r($stats);

if ($report) {
    migratePrintReport($stats, $tableFilter);
}

function migratePhotosConnectDatabase(): PDO
{
    $database = new Database();
    $pdo = $database->getConnection();
    if ($pdo instanceof PDO) {
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $pdo;
    }

    $dbName = getenv('KDMS_DB_NAME') ?: 'kdms';
    $user = getenv('KDMS_DB_USER') ?: 'kdms';
    $password = getenv('KDMS_DB_PASSWORD') ?: 'kdms';
    $hostSpec = getenv('KDMS_DB_HOST') ?: '127.0.0.1:3306';
    $socket = getenv('KDMS_DB_SOCKET');

    fwrite(STDERR, "MySQL connection failed (getConnection returned null)." . PHP_EOL);
    fwrite(STDERR, '  KDMS_DB_NAME=' . $dbName . PHP_EOL);
    fwrite(STDERR, '  KDMS_DB_USER=' . $user . PHP_EOL);
    if (is_string($socket) && $socket !== '') {
        fwrite(STDERR, '  KDMS_DB_SOCKET=' . $socket . PHP_EOL);
    } else {
        fwrite(STDERR, '  KDMS_DB_HOST=' . $hostSpec . PHP_EOL);
    }
    fwrite(STDERR, '  Tip: From Mac/host CLI use KDMS_DB_HOST=127.0.0.1:3306 (not host.docker.internal).' . PHP_EOL);

    try {
        if (is_string($socket) && $socket !== '') {
            $dsn = 'mysql:unix_socket=' . $socket . ';dbname=' . $dbName;
        } elseif (preg_match('/^(.+):(\d+)$/', $hostSpec, $m)) {
            $dsn = 'mysql:host=' . $m[1] . ';port=' . $m[2] . ';dbname=' . $dbName;
        } else {
            $port = getenv('KDMS_DB_PORT') ?: '3306';
            $dsn = 'mysql:host=' . $hostSpec . ';port=' . $port . ';dbname=' . $dbName;
        }

        $pdo = new PDO($dsn, $user, $password);
        $pdo->exec('set names utf8');
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

        return $pdo;
    } catch (PDOException $e) {
        fwrite(STDERR, 'PDO error: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
}

/**
 * @param array<string, int> $stats
 */
function migrateTable(
    PDO $db,
    string $table,
    string $keyColumn,
    string $blobColumn,
    string $pathColumn,
    callable $pathForKey,
    array &$stats,
    string $label,
    bool $dryRun,
    int $batchSize,
    int $limit,
    int $sampleLines,
    int $maxBlobBytes
): void {
    $pending = migrateCountPending($db, $table, $blobColumn, $pathColumn);
    echo PHP_EOL . "{$table}: {$pending} row(s) pending migration" . PHP_EOL;

    if ($dryRun) {
        $would = $limit > 0 ? min($pending, $limit) : $pending;
        $stats["{$label}_would_migrate"] += $would;
        echo "[dry-run] {$table}: would migrate {$would} object(s)" . PHP_EOL;

        if ($sampleLines > 0) {
            migratePrintKeySamples($db, $table, $keyColumn, $blobColumn, $pathColumn, $pathForKey, $sampleLines);
        }

        return;
    }

    $processed = 0;
    $lastKey = '';

    $listKeysSql = "SELECT {$keyColumn} AS row_key
        FROM {$table}
        WHERE {$blobColumn} IS NOT NULL
          AND ({$pathColumn} IS NULL OR TRIM({$pathColumn}) = '')
          AND {$keyColumn} > :last_key
        ORDER BY {$keyColumn}
        LIMIT :batch_limit";

    $fetchBlobSql = "SELECT {$blobColumn} AS blob_data
        FROM {$table}
        WHERE {$keyColumn} = :row_key
        LIMIT 1";

    $listStmt = $db->prepare($listKeysSql);
    $blobStmt = $db->prepare($fetchBlobSql);
    $updateStmt = $db->prepare(
        "UPDATE {$table} SET {$pathColumn} = :path WHERE {$keyColumn} = :row_key"
    );

    while (true) {
        if ($limit > 0 && $processed >= $limit) {
            break;
        }

        $take = $limit > 0 ? min($batchSize, $limit - $processed) : $batchSize;
        $listStmt->bindValue(':last_key', $lastKey, PDO::PARAM_STR);
        $listStmt->bindValue(':batch_limit', $take, PDO::PARAM_INT);
        $listStmt->execute();

        $keys = $listStmt->fetchAll(PDO::FETCH_COLUMN, 0);
        if (!is_array($keys) || $keys === []) {
            break;
        }
        $keyBatchCount = count($keys);

        foreach ($keys as $key) {
            $key = (string) $key;
            $lastKey = $key;

            $blobStmt->bindValue(':row_key', $key, PDO::PARAM_STR);
            $blobStmt->execute();
            $row = $blobStmt->fetch(PDO::FETCH_ASSOC);
            $blobStmt->closeCursor();

            $blob = $row['blob_data'] ?? null;
            if (is_resource($blob)) {
                $blob = stream_get_contents($blob);
            }
            unset($row);

            if (!is_string($blob) || $blob === '') {
                $stats["{$label}_skipped"]++;
                continue;
            }

            if (strlen($blob) > $maxBlobBytes) {
                $stats['oversized_skipped']++;
                echo "[skip] {$table} {$key} blob " . round(strlen($blob) / 1024 / 1024, 1) . " MB exceeds limit" . PHP_EOL;
                unset($blob);
                continue;
            }

            $objectPath = $pathForKey($key);
            $written = PhotoStorage::writeGcsObject($objectPath, $blob);
            unset($blob);

            if ($written === null) {
                $stats['errors']++;
                echo "[error] {$table} {$key} GCS write failed" . PHP_EOL;
                continue;
            }

            $updateStmt->execute(['path' => $objectPath, 'row_key' => $key]);
            $stats["{$label}_migrated"]++;
            echo "[ok] {$table} {$key} -> {$objectPath}" . PHP_EOL;
            $processed++;

            if ($processed % 500 === 0) {
                echo "[progress] {$table}: {$processed} row(s) migrated so far" . PHP_EOL;
                gc_collect_cycles();
            }
        }

        unset($keys);
        usleep(100000);

        if ($keyBatchCount < $take) {
            break;
        }
    }
}

function migratePrintBaselineCounts(PDO $db, bool $report): void
{
    if (!$report) {
        return;
    }
    $sql = 'SELECT
        (SELECT COUNT(*) FROM devotee_photo WHERE Devotee_Photo IS NOT NULL) AS blobs_photo,
        (SELECT COUNT(*) FROM devotee_photo WHERE Devotee_Photo_Gcs_Path IS NOT NULL AND TRIM(Devotee_Photo_Gcs_Path) <> \'\') AS gcs_photo,
        (SELECT COUNT(*) FROM devotee_id WHERE Devotee_ID_Image IS NOT NULL) AS blobs_id,
        (SELECT COUNT(*) FROM devotee_id WHERE Devotee_ID_Image_Gcs_Path IS NOT NULL AND TRIM(Devotee_ID_Image_Gcs_Path) <> \'\') AS gcs_id';
    $row = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
    echo PHP_EOL . 'Baseline counts:' . PHP_EOL;
    echo '  devotee_photo BLOB rows: ' . (int) ($row['blobs_photo'] ?? 0) . PHP_EOL;
    echo '  devotee_photo GCS path rows: ' . (int) ($row['gcs_photo'] ?? 0) . PHP_EOL;
    echo '  devotee_id BLOB rows: ' . (int) ($row['blobs_id'] ?? 0) . PHP_EOL;
    echo '  devotee_id GCS path rows: ' . (int) ($row['gcs_id'] ?? 0) . PHP_EOL;
}

/**
 * @param array<string, int> $stats
 */
function migratePrintReport(array $stats, string $tableFilter): void
{
    echo PHP_EOL . 'Report:' . PHP_EOL;
    if ($tableFilter === 'photo' || $tableFilter === 'both') {
        $n = (int) ($stats['photo_migrated'] ?? 0) + (int) ($stats['photo_would_migrate'] ?? 0);
        echo "Photos: {$n} migrated, " . (int) ($stats['photo_already_path'] ?? 0) . ' already had path, '
            . (int) ($stats['photo_no_blob'] ?? 0) . " had no BLOB (skipped)" . PHP_EOL;
    }
    if ($tableFilter === 'id' || $tableFilter === 'both') {
        $n = (int) ($stats['id_migrated'] ?? 0) + (int) ($stats['id_would_migrate'] ?? 0);
        echo "IDs:    {$n} migrated, " . (int) ($stats['id_already_path'] ?? 0) . ' already had path, '
            . (int) ($stats['id_no_blob'] ?? 0) . " had no BLOB (skipped)" . PHP_EOL;
    }
}

function migrateCountPending(PDO $db, string $table, string $blobColumn, string $pathColumn): int
{
    $sql = "SELECT COUNT(*) FROM {$table}
            WHERE {$blobColumn} IS NOT NULL
              AND ({$pathColumn} IS NULL OR TRIM({$pathColumn}) = '')";
    $count = $db->query($sql)->fetchColumn();

    return (int) $count;
}

function migratePrintKeySamples(
    PDO $db,
    string $table,
    string $keyColumn,
    string $blobColumn,
    string $pathColumn,
    callable $pathForKey,
    int $sampleLines
): void {
    $sql = "SELECT {$keyColumn} AS row_key
            FROM {$table}
            WHERE {$blobColumn} IS NOT NULL
              AND ({$pathColumn} IS NULL OR TRIM({$pathColumn}) = '')
            ORDER BY {$keyColumn}
            LIMIT " . (int) $sampleLines;

    $stmt = $db->query($sql);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $key = (string) $row['row_key'];
        $path = $pathForKey($key);
        echo "  sample {$key} -> gs://" . PhotoStorage::bucketName() . "/{$path}" . PHP_EOL;
    }
}
