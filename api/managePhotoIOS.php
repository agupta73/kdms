<?php

// Setting
$Interface_path = "Interface/";
$requestData = $_POST;
$api_type = 0; // Default
$devotee_key = "";
// each api call will have api_type to reconize it.
if (!empty($requestData['api_type'])) {
    $api_type = $requestData['api_type'];
}

$is_update = false;
include_once $Interface_path . 'ImageIOS.php';
include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
// Now check if request is for new devotee or existing
$new = false;

if (empty($requestData['devotee_key'])) {  // new
    $new = true;
    include_once $Interface_path . 'devotees.php';
    $devotee = new Devotee($db);
    $devotee_key = $devotee->generateId();
} else { // Existing
    $is_update = true;
    $devotee_key = $requestData['devotee_key'];
}

$type = "";
if(!empty($requestData['type'])) {
    $type = $requestData['type'];
}
// Create database connection
$imageClass = new ImageIOS($db);

// #3 Profile image upload
if ($api_type == 3) {
    if($type == "") {
        $type = "self";
    }
    $rMessage = $imageClass->upload($requestData, $devotee_key, $type);
} else {
    if($type == "")  {
        $type = "Other";
    }
    $rMessage = $imageClass->uploadID($requestData, $devotee_key, $type);
}

res_success($rMessage, $devotee_key);

function res_success($msg, $devotee_key) {
    echo json_encode(['message' => $msg, 'info' => $devotee_key , 'status' => true]);
    die;
}

function res_error($msg) {
    echo json_encode(['message' => $msg, 'status' => false]);
    die;
}
