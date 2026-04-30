<?php

// User defind configuration
$directoryName = getenv('KMREPORTS_KDMS_DIR') ?: "kdms"; //name of main folder
$localDirName = getenv('KMREPORTS_LOCAL_DIR');
$localDirName = ($localDirName === false) ? "KMReports" : (string) $localDirName;
$directory_seprator = '/';
$protocol = getenv('KMREPORTS_PROTOCOL') ?: 'http://';
$webrootOverride = getenv('KMREPORTS_WEBROOT_URL');
$apiOverride = getenv('KMREPORTS_API_BASE_URL');
$localRootOverride = getenv('KMREPORTS_LOCAL_ROOT');
//---------------------------------------
// Don't change here

if (!empty($webrootOverride)) {
    $webroot = rtrim((string) $webrootOverride, '/') . $directory_seprator;
} else {
    $host = ($_SERVER['HTTP_HOST'] ?? 'localhost') . $directory_seprator;
    $webroot = $protocol . $host . trim($directoryName, '/') . $directory_seprator;
}

if (!empty($localRootOverride)) {
    $localroot = rtrim((string) $localRootOverride, '/') . $directory_seprator;
} else {
    $host = ($_SERVER['HTTP_HOST'] ?? 'localhost') . $directory_seprator;
    $localSegment = trim($localDirName, '/');
    $localroot = $localSegment === '' ? ($protocol . $host) : ($protocol . $host . $localSegment . $directory_seprator);
}
//echo $webroot;die;
$api_dir = !empty($apiOverride) ? (rtrim((string) $apiOverride, '/') . $directory_seprator) : ($webroot . 'api' . $directory_seprator);
$service_dir =  $webroot . 'Services' . $directory_seprator;

//date_default_timezone_set("Asia/Kolkata");
date_default_timezone_set("America/Los_Angeles");
//Set the event ID of the current event
//Please see event_master table for available events or
// use the manage event functionality, available from dashboard of application
// $event_id = "2022NR";
$checkAccess = true;

return [
    'webroot' => $webroot,
    'api_dir' => $api_dir,
    'service_dir' => $service_dir,
    'local_root' => $localroot,  
    'check_access' => $checkAccess,
];
?>