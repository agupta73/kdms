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

// post data to temp bucket.
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
    if ($imageClass->upload($requestData)) {
        res_success('uploading image to temp bucket successfull!');
    } else {
        res_error('Error while updating image to temp bucket!');
    }
}
if ($api_type == 1) {
    include_once $Interface_path . 'kdms_ocr_image_bucket_upload.php';
    include_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    // Create object
    $imageClass = new TempBucketImageUpload($db);
    $result = $imageClass->get_temp_image_bucket_data($requestData);
    if (count($result)) {
        return res_success($result);
    } else {
        $msg = 'No record found!';
        return res_error($msg);
    }
}

if ($api_type == 2) {
    include_once $Interface_path . 'kdms_ocr_image_bucket_upload.php';
    include_once 'config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    // Create object
    $imageClass = new TempBucketImageUpload($db);
    $result = $imageClass->delete_images($requestData);
    if ($imageClass->delete_images($requestData)) {
        res_success('image deleted from temp bucket successfully!');
    } else {
        res_error('Error occurred while deleting image from temp bucket!');
    }
}

function res_success($data) {
    echo json_encode(['data' => $data, 'status' => true]);
    die;
}

function res_error($msg) {
    echo json_encode(['message' => $msg, 'status' => false]);
    die;
}
