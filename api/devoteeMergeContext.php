<?php

declare(strict_types=1);

/**
 * Staff merge utility: anchor duplicates list and survivor/TBM preview.
 * GET ?anchor=P...&eventId=...  — anchor summary + duplicate candidates
 * GET ?survivor=P...&tbm=P...&eventId=... — merge preview
 */
require_once __DIR__ . '/../includes/api_session.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/../includes/DeduplicationService.php';

header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Method not allowed']);
    exit;
}

$eventId = trim((string) ($_GET['eventId'] ?? getenv('KDMS_EVENT_ID') ?: ''));
$anchorKey = strtoupper(trim((string) ($_GET['anchor'] ?? '')));
$survivorKey = strtoupper(trim((string) ($_GET['survivor'] ?? '')));
$tbmKey = strtoupper(trim((string) ($_GET['tbm'] ?? '')));

$database = new Database();
$db = $database->getConnection();
$svc = new DeduplicationService($db, $eventId, (string) ($_SESSION['LoginID'] ?? 'STAFF'));

try {
    if ($survivorKey !== '' && $tbmKey !== '') {
        if (strcasecmp($survivorKey, $tbmKey) === 0) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Survivor and duplicate must be different records']);
            exit;
        }
        $preview = $svc->buildMergePreview($survivorKey, $tbmKey);
        echo json_encode([
            'status' => true,
            'mode' => 'preview',
            'preview' => $preview,
        ]);
        exit;
    }

    if ($anchorKey === '') {
        http_response_code(400);
        echo json_encode(['status' => false, 'message' => 'anchor or survivor+tbm is required']);
        exit;
    }

    $sel = $db->prepare('SELECT * FROM devotee WHERE Devotee_Key = :k LIMIT 1');
    $sel->execute(['k' => $anchorKey]);
    $anchorRow = $sel->fetch(PDO::FETCH_ASSOC);
    if (!$anchorRow) {
        http_response_code(404);
        echo json_encode(['status' => false, 'message' => 'Devotee not found']);
        exit;
    }

    $check = $svc->findDuplicates($anchorRow);
    $matches = [];
    foreach ($check['matches'] as $match) {
        $key = strtoupper(trim((string) ($match['devotee_key'] ?? '')));
        if ($key === '' || strcasecmp($key, $anchorKey) === 0) {
            continue;
        }
        $summary = $svc->getDevoteeMergeSummary($key);
        if ($summary === null) {
            continue;
        }
        $matches[] = array_merge($match, ['summary' => $summary]);
    }

    usort($matches, static function (array $a, array $b): int {
        $scoreCmp = ((int) ($b['score'] ?? 0)) <=> ((int) ($a['score'] ?? 0));
        if ($scoreCmp !== 0) {
            return $scoreCmp;
        }

        return strcmp((string) ($a['devotee_key'] ?? ''), (string) ($b['devotee_key'] ?? ''));
    });

    $anchorSummary = $svc->getDevoteeMergeSummary($anchorKey);
    echo json_encode([
        'status' => true,
        'mode' => 'anchor',
        'anchor' => $anchorSummary,
        'recommended_action' => $check['recommended_action'],
        'merge_score' => $check['merge_score'],
        'matches' => $matches,
    ]);
} catch (Throwable $e) {
    require_once __DIR__ . '/../includes/kdms_log.php';
    kdms_log('ERROR', 'devoteeMergeContext failed', ['error' => $e->getMessage()]);
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => $e->getMessage()]);
}
