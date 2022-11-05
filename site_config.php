<?php

// User defind configuration
$directoryName = "kdms"; //name of main folder
$directory_seprator = '/';
$protocol = 'http://';
//---------------------------------------
// Don't change here

$host = $_SERVER['HTTP_HOST'] . $directory_seprator;
$webroot = $protocol . $host . $directoryName . $directory_seprator;
//echo $webroot;die;
$api_dir = $webroot . 'api' . $directory_seprator;

//Set the event ID of the current event
//Please see event_master table for available events or
// use the manage event functionality, available from dashboard of application
$event_id = "2022NR";

return [
    'webroot' => $webroot,
    'api_dir' => $api_dir,
    'event_id' => $event_id
];
?>