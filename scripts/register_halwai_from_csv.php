#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Batch-register permanent Halwai devotees from a CSV (Name, Address columns).
 *
 * Fixed defaults:
 *   Referral: Halwai | Seva: HLW | Accommodation: PHB | Gender: M
 *   Type: P (Permanent) | Status: G (Good) | Country: India
 *
 * Every CSV row is processed (duplicates are not skipped). After each successful
 * registration the devotee is added to card_print_log (print queue).
 *
 * Quick usage:
 *   php scripts/register_halwai_from_csv.php --csv=/path/to/halwai.csv --dry-run
 *   php scripts/register_halwai_from_csv.php --csv=/path/to/halwai.csv --event-id=2026JB --output=halwai_results.csv
 */

$root = dirname(__DIR__);
require_once $root . '/includes/kdms_load_dotenv.php';
require_once $root . '/includes/kdms_log.php';
require_once $root . '/api/config/database.php';
require_once $root . '/api/Interface/devotees.php';

kdms_log_bootstrap();

const DEFAULT_REFERRAL = 'Halwai';
const DEFAULT_SEVA_ID = 'HLW';
const DEFAULT_ACCOMMODATION = 'PHB';
const DEFAULT_GENDER = 'M';
const DEFAULT_TYPE = 'P';
const DEFAULT_STATUS = 'G';
const DEFAULT_COUNTRY = 'India';
const DEFAULT_STATE = 'Uttar Pradesh';

$opts = parseCliOptions($argv);
if ($opts['csv'] === '') {
    fwrite(STDERR, "Error: --csv=<path> is required.\n");
    fwrite(STDERR, usageText());
    exit(1);
}

if (!is_readable($opts['csv'])) {
    fwrite(STDERR, "Error: CSV not readable: {$opts['csv']}\n");
    exit(1);
}

$eventId = $opts['event_id'] !== ''
    ? $opts['event_id']
    : (getenv('KDMS_EVENT_ID') ?: '2026JB');

$rows = loadCsvRows($opts['csv']);
if ($rows === []) {
    fwrite(STDERR, "Error: no data rows in CSV (expected header: Name, Address).\n");
    exit(1);
}

if ($opts['limit'] > 0) {
    $rows = array_slice($rows, 0, $opts['limit']);
}

echo 'CSV: ' . $opts['csv'] . PHP_EOL;
echo 'Event: ' . $eventId . PHP_EOL;
echo 'Mode: ' . ($opts['dry_run'] ? 'DRY-RUN' : 'LIVE') . PHP_EOL;
echo 'Rows: ' . count($rows) . ' (all rows processed; none skipped)' . PHP_EOL;
echo 'Defaults: referral=' . DEFAULT_REFERRAL
    . ', seva_id=' . DEFAULT_SEVA_ID
    . ', accommodation_key=' . DEFAULT_ACCOMMODATION
    . ', type=' . DEFAULT_TYPE
    . ', status=' . DEFAULT_STATUS
    . ', gender=' . DEFAULT_GENDER
    . ', print_queue=yes' . PHP_EOL;

foreach (findCsvDuplicates($rows) as $w) {
    echo 'NOTE: duplicate CSV row (still processed) — ' . $w . PHP_EOL;
}

$db = null;
if (!$opts['dry_run']) {
    $db = halwaiConnectDatabase();
    validateEventOptions($db, $eventId, DEFAULT_SEVA_ID, DEFAULT_ACCOMMODATION);
}

$devotee = $db !== null ? new Devotee($db) : null;
$results = [];
$ok = 0;
$fail = 0;
$queued = 0;
$dryCount = 0;

foreach ($rows as $index => $row) {
    $lineNum = $index + 2;
    [$first, $last] = splitDevoteeName($row['name']);
    $address = trim($row['address']);
    $station = inferStationFromAddress($address);

    $payload = [
        'devotee_first_name' => $first,
        'devotee_last_name' => $last,
        'devotee_gender' => DEFAULT_GENDER,
        'devotee_type' => DEFAULT_TYPE,
        'devotee_status' => DEFAULT_STATUS,
        'devotee_referral' => DEFAULT_REFERRAL,
        'devotee_seva_id' => DEFAULT_SEVA_ID,
        'devotee_accommodation_id' => DEFAULT_ACCOMMODATION,
        'devotee_address_1' => $address,
        'devotee_station' => $station,
        'devotee_state' => DEFAULT_STATE,
        'devotee_country' => DEFAULT_COUNTRY,
        'eventId' => $eventId,
        'requestType' => 'upsertDevotee',
    ];

    $label = trim($first . ' ' . $last) . ' | ' . $address;

    if ($opts['dry_run']) {
        echo sprintf(
            "[dry-run] line %d: %s → seva=%s, accom=%s, station=%s → register + print queue\n",
            $lineNum,
            $row['name'],
            DEFAULT_SEVA_ID,
            DEFAULT_ACCOMMODATION,
            $station
        );
        $results[] = resultRow($lineNum, $row['name'], $address, '', 'dry-run', '', 'dry-run');
        $dryCount++;
        continue;
    }

    if ($devotee === null) {
        throw new RuntimeException('Database connection is not available.');
    }

    $res = $devotee->upsertDevotee($payload);
    $success = !empty($res['status']);
    $key = is_string($res['info'] ?? null) ? $res['info'] : '';
    $msg = is_string($res['message'] ?? null) ? trim($res['message']) : '';

    if (!$success || $key === '') {
        $fail++;
        $err = $msg !== '' ? $msg : 'upsertDevotee failed';
        echo sprintf("[FAIL] line %d: %s — %s\n", $lineNum, $label, $err);
        $results[] = resultRow($lineNum, $row['name'], $address, $key, 'fail', $err, 'skipped');
        kdms_log('ERROR', 'register_halwai_from_csv upsert failed', [
            'line' => $lineNum,
            'name' => $row['name'],
            'error' => $err,
        ]);
        continue;
    }

    $printRes = $devotee->manageCardPrinting([
        'devotee_key' => $key,
        'requestType' => 'addToPrintQueue',
        'eventId' => $eventId,
    ]);
    $printOk = !empty($printRes['status']);
    $printMsg = is_string($printRes['message'] ?? null) ? trim($printRes['message']) : '';

    if ($printOk) {
        $queued++;
        $ok++;
        echo sprintf(
            "[OK] line %d: %s → %s (queued)%s\n",
            $lineNum,
            $label,
            $key,
            $msg !== '' ? " — $msg" : ''
        );
        $results[] = resultRow($lineNum, $row['name'], $address, $key, 'ok', $msg, 'queued');
    } else {
        $fail++;
        $err = $printMsg !== '' ? $printMsg : 'addToPrintQueue failed';
        echo sprintf("[FAIL] line %d: %s — registered %s but print queue failed: %s\n", $lineNum, $label, $key, $err);
        $results[] = resultRow($lineNum, $row['name'], $address, $key, 'partial', $msg, $err);
        kdms_log('ERROR', 'register_halwai_from_csv print queue failed', [
            'line' => $lineNum,
            'devotee_key' => $key,
            'error' => $err,
        ]);
    }
}

echo PHP_EOL;
if ($opts['dry_run']) {
    echo "Summary: dry-run={$dryCount} (no DB changes)\n";
} else {
    echo "Summary: registered+queued={$ok}, print_queue={$queued}, fail={$fail}\n";
}

if ($opts['output'] !== '' && $results !== []) {
    writeResultsCsv($opts['output'], $results);
    echo 'Wrote results: ' . $opts['output'] . PHP_EOL;
}

exit($fail > 0 ? 1 : 0);

// ---------------------------------------------------------------------------

function halwaiConnectDatabase(): PDO
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

    fwrite(STDERR, 'MySQL connection failed (getConnection returned null).' . PHP_EOL);
    fwrite(STDERR, '  KDMS_DB_NAME=' . $dbName . PHP_EOL);
    fwrite(STDERR, '  KDMS_DB_USER=' . $user . PHP_EOL);
    if (is_string($socket) && $socket !== '') {
        fwrite(STDERR, '  KDMS_DB_SOCKET=' . $socket . PHP_EOL);
    } else {
        fwrite(STDERR, '  KDMS_DB_HOST=' . $hostSpec . PHP_EOL);
    }
    fwrite(STDERR, '  Tip (Mac CLI + XAMPP): export KDMS_DB_HOST=127.0.0.1:3306' . PHP_EOL);
    fwrite(STDERR, '  Tip (prod via proxy): export KDMS_DB_HOST=127.0.0.1:9470 KDMS_DB_NAME=kdms_prod ...' . PHP_EOL);
    fwrite(STDERR, '  Set KDMS_DB_DEBUG=1 to see connection errors from Database class.' . PHP_EOL);

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
 * @return array{csv: string, dry_run: bool, limit: int, event_id: string, output: string}
 */
function parseCliOptions(array $argv): array
{
    $opts = [
        'csv' => '',
        'dry_run' => false,
        'limit' => 0,
        'event_id' => '',
        'output' => '',
    ];

    foreach ($argv as $arg) {
        if ($arg === '--dry-run') {
            $opts['dry_run'] = true;
        } elseif (preg_match('/^--csv=(.+)$/', $arg, $m)) {
            $opts['csv'] = $m[1];
        } elseif (preg_match('/^--limit=(\d+)$/', $arg, $m)) {
            $opts['limit'] = max(0, (int) $m[1]);
        } elseif (preg_match('/^--event-id=(.+)$/', $arg, $m)) {
            $opts['event_id'] = trim($m[1]);
        } elseif (preg_match('/^--output=(.+)$/', $arg, $m)) {
            $opts['output'] = $m[1];
        } elseif ($arg === '--help' || $arg === '-h') {
            echo usageText();
            exit(0);
        }
    }

    return $opts;
}

function usageText(): string
{
    return <<<'TXT'
register_halwai_from_csv.php — batch Halwai permanent devotee registration + print queue

  --csv=<path>       Input CSV (columns: Name, Address) [required]
  --dry-run          Parse and print actions without writing to DB
  --limit=N          Process only first N rows (for smoke tests)
  --event-id=2026JB  Event for seva/accommodation (default: KDMS_EVENT_ID or 2026JB)
  --output=<path>    Results CSV: line,name,address,devotee_key,status,message,print_queue
  --help             Show this help

Defaults: referral=Halwai, seva_id=HLW, accommodation_key=PHB, type=P, status=G, gender=M

DB (required for live runs, not --dry-run):
  export KDMS_DB_HOST=127.0.0.1:3306
  export KDMS_DB_NAME=kdms
  export KDMS_DB_USER=kdms
  export KDMS_DB_PASSWORD=...

TXT;
}

/**
 * @return array{line: string, name: string, address: string, devotee_key: string, status: string, message: string, print_queue: string}
 */
function resultRow(
    int $lineNum,
    string $name,
    string $address,
    string $key,
    string $status,
    string $message,
    string $printQueue
): array {
    return [
        'line' => (string) $lineNum,
        'name' => $name,
        'address' => $address,
        'devotee_key' => $key,
        'status' => $status,
        'message' => $message,
        'print_queue' => $printQueue,
    ];
}

/**
 * @return list<array{name: string, address: string}>
 */
function loadCsvRows(string $path): array
{
    $fh = fopen($path, 'rb');
    if ($fh === false) {
        throw new RuntimeException('Cannot open CSV: ' . $path);
    }

    $header = fgetcsv($fh);
    if ($header === false) {
        fclose($fh);

        return [];
    }

    $nameIdx = null;
    $addrIdx = null;
    foreach ($header as $i => $col) {
        $norm = strtolower(trim((string) $col));
        if ($norm === 'name') {
            $nameIdx = $i;
        }
        if ($norm === 'address') {
            $addrIdx = $i;
        }
    }

    if ($nameIdx === null || $addrIdx === null) {
        fclose($fh);
        throw new RuntimeException('CSV must have Name and Address columns');
    }

    $rows = [];
    while (($data = fgetcsv($fh)) !== false) {
        $name = trim((string) ($data[$nameIdx] ?? ''));
        $address = trim((string) ($data[$addrIdx] ?? ''));
        if ($name === '' && $address === '') {
            continue;
        }
        if ($name === '') {
            continue;
        }
        $rows[] = ['name' => $name, 'address' => $address];
    }
    fclose($fh);

    return $rows;
}

/**
 * @return array{0: string, 1: string}
 */
function splitDevoteeName(string $fullName): array
{
    $fullName = preg_replace('/\s+/u', ' ', trim($fullName)) ?? '';
    if ($fullName === '') {
        return ['Unknown', '.'];
    }

    $parts = explode(' ', $fullName);
    if (count($parts) === 1) {
        return [$parts[0], '.'];
    }

    $first = array_shift($parts);

    return [$first, implode(' ', $parts)];
}

function inferStationFromAddress(string $address): string
{
    $address = trim($address);
    if ($address === '') {
        return 'Mathura';
    }

    if (str_contains(strtolower($address), 'agra')) {
        return 'Agra';
    }

    $segments = array_map('trim', explode(',', $address));
    $last = end($segments);
    if ($last !== false && $last !== '') {
        return $last;
    }

    return 'Mathura';
}

/**
 * @param list<array{name: string, address: string}> $rows
 * @return list<string>
 */
function findCsvDuplicates(array $rows): array
{
    $seen = [];
    $notes = [];
    foreach ($rows as $i => $row) {
        $key = strtolower($row['name'] . '|' . $row['address']);
        if (isset($seen[$key])) {
            $notes[] = sprintf(
                'lines %d and %d: %s',
                $seen[$key] + 2,
                $i + 2,
                $row['name']
            );
        } else {
            $seen[$key] = $i;
        }
    }

    return $notes;
}

function validateEventOptions(PDO $db, string $eventId, string $sevaId, string $accommodationKey): void
{
    $stmt = $db->prepare(
        'SELECT 1 FROM seva_availability WHERE Seva_Id = :sid AND Seva_Event = :eid LIMIT 1'
    );
    $stmt->execute(['sid' => $sevaId, 'eid' => $eventId]);
    if (!$stmt->fetchColumn()) {
        $stmt2 = $db->prepare('SELECT 1 FROM seva_master WHERE Seva_Id = :sid LIMIT 1');
        $stmt2->execute(['sid' => $sevaId]);
        if (!$stmt2->fetchColumn()) {
            fwrite(STDERR, "Warning: seva_id '{$sevaId}' not found for event {$eventId}.\n");
        }
    }

    $stmt = $db->prepare(
        'SELECT 1 FROM accommodation_availability WHERE Accomodation_Key = :k AND Accommodation_Event = :eid LIMIT 1'
    );
    $stmt->execute(['k' => $accommodationKey, 'eid' => $eventId]);
    if (!$stmt->fetchColumn()) {
        $stmt2 = $db->prepare('SELECT 1 FROM accommodation_master WHERE Accomodation_Key = :k LIMIT 1');
        $stmt2->execute(['k' => $accommodationKey]);
        if (!$stmt2->fetchColumn()) {
            fwrite(STDERR, "Warning: accommodation_key '{$accommodationKey}' not found for event {$eventId}.\n");
        }
    }
}

/**
 * @param list<array<string, string>> $results
 */
function writeResultsCsv(string $path, array $results): void
{
    $fh = fopen($path, 'wb');
    if ($fh === false) {
        throw new RuntimeException('Cannot write: ' . $path);
    }
    fputcsv($fh, ['line', 'name', 'address', 'devotee_key', 'status', 'message', 'print_queue']);
    foreach ($results as $row) {
        fputcsv($fh, [
            $row['line'],
            $row['name'],
            $row['address'],
            $row['devotee_key'],
            $row['status'],
            $row['message'],
            $row['print_queue'],
        ]);
    }
    fclose($fh);
}
