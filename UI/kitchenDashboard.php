<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/web_session.php';

$config_data = include dirname(__DIR__) . '/site_config.php';
$eventId = $config_data['event_id'] ?? '';

include_once dirname(__DIR__) . '/api/config/database.php';
include_once dirname(__DIR__) . '/api/Interface/clsKitchenDashboard.php';

$database = new Database();
$db = $database->getConnection();
$kitchen = new clsKitchenDashboard($db);
$rows = $kitchen->getReport(['eventId' => $eventId]);
$row = $rows[0] ?? [];

$residents = (int) ($row['Residents_Printed_For_Event'] ?? 0);
$dayVisitors = (int) ($row['Day_Visitors_Printed_Today'] ?? 0);
$total = (int) ($row['Total_For_Kitchen'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once 'header.php'; ?>
    <title>Kitchen Dashboard</title>
    <style>
        .kitchen-metric {
            text-align: center;
            padding: 24px 12px;
        }
        .kitchen-metric .value {
            font-size: 3.5rem;
            font-weight: 700;
            line-height: 1.1;
        }
        .kitchen-metric .label {
            font-size: 1.1rem;
            color: #555;
            margin-top: 8px;
        }
        .kitchen-note {
            font-size: 0.9rem;
            color: #666;
            max-width: 720px;
            margin: 0 auto 16px;
        }
        #kitchen-refresh-time {
            font-size: 0.85rem;
            color: #888;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include_once 'nav.php'; ?>
    <div class="main-panel">
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Kitchen — meal planning</h4>
                                <p class="card-category">
                                    Event: <?= htmlspecialchars((string) ($row['Event_ID'] ?? $eventId), ENT_QUOTES, 'UTF-8'); ?>
                                    · <span id="kitchen-refresh-time">Updated <?= date('H:i:s'); ?></span>
                                </p>
                            </div>
                            <div class="card-body">
                                <p class="kitchen-note text-center">
                                    <strong>Residents printed (event)</strong> — distinct devotees with a card print recorded in
                                    <code>print_log</code> for this event (any date), excluding day visitors (status D, type T).
                                    <strong>Day visitors printed today</strong> — distinct D/T devotees with <code>print_log</code> dated today only.
                                    Queuing via registration does not count until the card is printed.
                                    <strong>Total for kitchen</strong> is the sum of those two figures (no double-count).
                                </p>
                                <div class="row">
                                    <div class="col-md-4 kitchen-metric">
                                        <div class="value" id="metric-residents"><?= $residents; ?></div>
                                        <div class="label">Residents printed (event)</div>
                                    </div>
                                    <div class="col-md-4 kitchen-metric">
                                        <div class="value" id="metric-day-visitors"><?= $dayVisitors; ?></div>
                                        <div class="label">Day visitors printed today</div>
                                    </div>
                                    <div class="col-md-4 kitchen-metric">
                                        <div class="value" id="metric-total"><?= $total; ?></div>
                                        <div class="label">Total for kitchen</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'scriptJS.php'; ?>
<script>
(function () {
    var refreshMs = 5 * 60 * 1000;

    function refreshKitchenCounts() {
        fetch('getKitchenCounts.php', { credentials: 'same-origin' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.status !== 'success') {
                    return;
                }
                document.getElementById('metric-residents').textContent = data.residentsToday;
                document.getElementById('metric-day-visitors').textContent = data.dayVisitorsPrintedToday;
                document.getElementById('metric-total').textContent = data.totalForKitchen;
                document.getElementById('kitchen-refresh-time').textContent =
                    'Updated ' + (data.refreshTime || '');
            })
            .catch(function () {});
    }

    setInterval(refreshKitchenCounts, refreshMs);
})();
</script>
</body>
</html>
