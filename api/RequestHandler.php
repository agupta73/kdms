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

// #3 Profile image upload
if ($api_type == 3) {
    $is_update=false;
    include_once $Interface_path . 'Image.php';
    include_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    // Now check if request is for new devotee or existing
   
    if (empty($requestData['devotee_key'])) {  // new
        include_once $Interface_path . 'devotees.php';
        $devotee = new Devotee($db);
        $devotee_key = $devotee->generateId();
    } else { // Existing
        $is_update=true;
        $devotee_key = $requestData['devotee_key'];
    }
    // Create database connection
    $imageClass = new Image($db);
    if ($imageClass->upload($requestData, $devotee_key,$is_update)) {
        res_success('Devotee image updated successfully !');
    } else {
        res_error('Error while  updating devotee image !');
    }
}

function res_success($msg) {
    echo json_encode(['message' => $msg, 'status' => true]);
    die;
}

function res_error($msg) {
    echo json_encode(['message' => $msg, 'status' => false]);
    die;
}
