<?php
// get database connection
include_once 'config/database.php';
include_once 'Interface/devotees.php';

$debug = false;
$database = new Database();
$db = $database->getConnection();

$devotee = new Devotee($db);
$requestData = $_POST;
//echo json_encode($requestData); die;
$res = array();
$response = array('flag' => false, 'message' => "Request failed", 'info' => $requestData);

if ($debug) {
    var_dump($_POST);
    var_dump($requestData); die;
}

if (!empty($requestData['requestType'])) {
    $response = array('flag' => false, 'message' => "Request failed", 'info' => $requestData['requestType']);
    switch ($requestData['requestType']) {
        case "addToPrintQueue":
        case "removeFromPrintQueue":
            $res = $devotee->manageCardPrinting($requestData);
            break;

        case "manageAmenity":
            $res = $devotee->manageAmenityAllocation($requestData);
            
            if ($debug) {
                echo "from manage amenity allocation php, after calling API ";
                var_dump($res);
            }
            break;

        case "upsertDevotee":
            if ($debug) {
                var_dump($requestData);
            }
            $res = $devotee->upsertDevotee($requestData);
            if ($debug) {
                echo "from upsert devotee php, after calling upsertDevotee ";
                var_dump($res);
            }
            break;

        case "deleteDevotee":
            if ($debug) {
                var_dump($requestData);
            }
            $res = $devotee->deleteDevoteeRecord($requestData);
            if ($debug) {
                echo "from delete devotee record, after calling deleteDevoteeRecord ";
                var_dump($res);
            }
            break;
            
        case "upsertRemark":
            if ($debug) {
                echo "from upsert devotee remark php, BEFORE calling upsertDevoteeRemark ";
                var_dump($requestData);
            }
            $res = $devotee->upsertDevoteeRemark($requestData);
            if ($debug) {
                echo "from upsert devotee remark php, after calling upsertDevoteeRemark ";
                var_dump($res);
            }
            break;

        case "upsertAttendance":
            if ($debug) {
                echo "from upsert devotee attendance php, BEFORE calling upsertDevoteeAttendance ";
                var_dump($requestData);
            }
            $res = $devotee->upsertDevoteeAttendance($requestData);
            if ($debug) {
                echo "from upsert devotee attendance php, after calling upsertDevoteeAttendance ";
                var_dump($res);
            }
            break;
        default:
            $response = array('flag' => false, 'message' => "Request type not specified or incorrect", 'info' => $requestData['requestType']);
            break;
    }
} else {
    $response = array('flag' => false, 'message' => "Request data empty", 'info' => $requestData);
}

if (!empty($res['status'])) {

    if ($res['status']) {
        $response = array('flag' => true, 'message' => $res['message'], 'info' => $res['info']);
    } else {
        $response = array('flag' => false, 'message' => $res['message'], 'info' => $res['info']);
    }

}
echo json_encode($response);
die;
?>