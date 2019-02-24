<?php

// User definded configuration
$directoryName = "kdms"; //name of main folder
$directory_seprator = '/';
$protocol = 'http://';
//---------------------------------------
// Don't change here

$host = $_SERVER['HTTP_HOST'] . $directory_seprator;
$webroot = $protocol . $host . $directoryName . $directory_seprator;
//echo $webroot;die;
$api_dir = $webroot . 'api' . $directory_seprator;


return [
    'webroot' => $webroot,
    'api_dir' => $api_dir
];
