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

// #3 Profile image upload
if ($api_type == 3) {
    $is_update = false;
    include_once $Interface_path . 'Image.php';
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
    // Create object
    $imageClass = new Image($db);
    // Old method to convert raw data
//    $rawData = $requestData['image'];
//        $filteredData = explode(',', $rawData);
//        $unencoded = base64_decode($filteredData[0]);
//        echo $unencoded;
//        die;

    if ($imageClass->upload($requestData, $devotee_key, $is_update)) {
        if ($new) {
            res_success($devotee_key);
        } else {
            res_success('Devotee image updated successfully !');
            //res_success($requestData['image']);
        }
    } else {
        res_error('Error while  updating devotee image !');
    }
} else if ($api_type == 4) {  // Upload ID/scanned document
    if (empty($_FILES['devotee-id-scan']) || $_FILES['devotee-id-scan']['error'] != 0) {
        res_error('file missing or failed to uplaod !');
    } else {
        $requestData['file'] = $_FILES['devotee-id-scan'];
    }

    $is_update = false;
    include_once $Interface_path . 'Image.php';
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
    // Create object of  Image class
    $imageClass = new Image($db);

    if ($imageClass->uploadDocument($requestData, $devotee_key, $is_update)) {
        // Since it is normal post , So relaod page
        if(!empty($requestData['request_from'])){
            $redirect='../UI/'.$requestData['request_from'].'?devotee_key=' . $devotee_key;
             header('Location: ' .$redirect);
        }else{
            res_success('Data updated !');
        }
       
        die;
    } else {
        res_error('Error while  updating devotee\'s document !');
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
