<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/web_session.php';

$config_data = include dirname(__DIR__) . '/site_config.php';
$eventId = (string) ($config_data['event_id'] ?? '');
$initialAnchor = strtoupper(trim((string) ($_GET['anchor'] ?? '')));
$webroot = rtrim((string) $config_data['webroot'], '/');

include_once 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>KDMS — Merge Duplicate Devotees</title>
    <style>
        .merge-summary { display: flex; gap: 12px; align-items: flex-start; }
        .merge-summary-photo img { object-fit: cover; border-radius: 4px; border: 1px solid #ddd; }
        #merge-step-compare { display: none; }
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
                                <h4 class="card-title">Merge Duplicate Devotees</h4>
                                <p class="card-category">Find possible duplicates for a record, compare side by side, and merge one duplicate at a time</p>
                            </div>
                            <div class="card-body">
                                <div id="merge-alert" class="alert" style="display:none;" role="alert"></div>

                                <div class="form-row align-items-end mb-4">
                                    <div class="col-md-4">
                                        <label for="merge-anchor-input">Anchor devotee key</label>
                                        <input type="text" class="form-control" id="merge-anchor-input" placeholder="e.g. P250610123" autocomplete="off">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary btn-block" id="merge-load-anchor">Find duplicates</button>
                                    </div>
                                </div>

                                <div id="merge-step-duplicates">
                                    <h5>Anchor record</h5>
                                    <div id="merge-anchor-card" class="mb-4 text-muted">Enter a devotee key and click Find duplicates.</div>

                                    <h5>Possible duplicates</h5>
                                    <p class="text-muted small">All match scores are shown. Lower-confidence matches (e.g. name + station) can still be reviewed and merged manually.</p>
                                    <div id="merge-no-matches" class="alert alert-light" style="display:none;">No duplicate candidates found.</div>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>Key</th>
                                                <th>Name</th>
                                                <th>Score</th>
                                                <th>Signal</th>
                                                <th>Station</th>
                                                <th>Phone</th>
                                                <th>Child records</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="merge-matches-body"></tbody>
                                        </table>
                                    </div>
                                </div>

                                <div id="merge-step-compare">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0">Compare and merge</h5>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="merge-back-btn">Back to list</button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card card-plain">
                                                <div class="card-body" id="merge-survivor-col"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card card-plain">
                                                <div class="card-body" id="merge-tbm-col"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="mt-3">Field preview</h6>
                                    <div id="merge-preview-fields"></div>

                                    <h6 class="mt-3">Images</h6>
                                    <div id="merge-preview-images" class="small"></div>

                                    <h6 class="mt-3">Related records</h6>
                                    <div id="merge-preview-children" class="small"></div>

                                    <button type="button" class="btn btn-warning mt-3" id="merge-confirm-btn">Merge duplicate into survivor</button>
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
window.kdmsMergeUtility = <?= json_encode([
    'contextUrl' => $webroot . '/Logic/devoteeMergeContextProxy.php',
    'mergeUrl' => $webroot . '/Logic/adminMergeProxy.php',
    'photoProxy' => $webroot . '/Logic/devoteePhotoProxy.php',
    'eventId' => $eventId,
    'initialAnchor' => $initialAnchor,
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script src="../assets/js/pages/devotee_merge.js"></script>
</body>
</html>
