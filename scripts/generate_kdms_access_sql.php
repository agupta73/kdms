#!/usr/bin/env php
<?php

/**
 * Regenerates the DISTINCT asset_key list from includes/kdms_web_page_ids.php
 * for copy-paste into mysql_grant_kdms_page_ids.sql (STEP 2 / CROSS JOIN blocks).
 *
 * Usage (from repo root):
 *   php scripts/generate_kdms_access_sql.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
$map = require $root . '/includes/kdms_web_page_ids.php';

if (! is_array($map)) {
    fwrite(STDERR, "kdms_web_page_ids.php did not return an array.\n");
    exit(1);
}

$ids = array_values($map);
$ids = array_unique($ids);
sort($ids);

$count = count($ids);
echo "-- DISTINCT asset_key values from kdms_web_page_ids.php (count {$count})\n\n";

echo "-- asset_list INSERT fragments:\n";
foreach ($ids as $k) {
    $esc = str_replace("'", "''", $k);
    echo "  ('{$esc}', 'KDMS.auto', 'kdms_generator', NOW()),\n";
}

echo "\n-- CROSS JOIN nk fragment (UNION ALL lines):\n";
$firstNk = true;
foreach ($ids as $k) {
    $esc = str_replace("'", "''", $k);
    $line = $firstNk ? "SELECT '{$esc}' AS asset_key" : "UNION ALL SELECT '{$esc}'";
    echo "  {$line}\n";
    $firstNk = false;
}
