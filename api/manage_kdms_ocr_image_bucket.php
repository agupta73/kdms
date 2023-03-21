<?php
// Setting
$Interface_path = "Interface/";
$requestData = $_POST;
$api_type = 0; // Default
$devotee_key = "";
// each api call will have "api_type" to reconize it.
if (!empty($requestData['api_type'])) {
    $api_type = $requestData['api_type'];
}

// #3 scan image upload to temp table.
if ($api_type == 0) {
    $is_update = false;
    include_once $Interface_path . 'kdms_ocr_image_bucket_upload.php';
    include_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    // Now check if request is for new devotee or existing
    $new = false;
    // Create object
    $imageClass = new TempBucketImageUpload($db);
    if ($imageClass->upload($requestData, $devotee_key, $is_update)) {
        // if ($new) {
        //     res_success($devotee_key);
        // } else {
        //     res_success('Devotee image updated successfully !');
        //     //res_success($requestData['image']);
        // }
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
