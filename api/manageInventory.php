<?php

// get database connection
include_once 'config/database.php';
include_once 'Interface/inventory.php';

$debug = false;

$database = new Database();
$db = $database->getConnection("inv");

$inventory = new inventory($db);
$submitMethod = "";

if (isset($_GET)) {
  $requestData = $_GET;
  $submitMethod = "GET";
} else {
  $requestData = $_POST;
  $submitMethod = "POST";
}

//echo json_encode($requestData); die;
$res = array();
$response = array('flag' => false, 'message' => "Request failed", 'info' => $requestData);

if ($debug) {
  var_dump($requestData);
}

if (!empty($requestData['requestType'])) {
  $response = array('flag' => false, 'message' => "Request failed", 'info' => $requestData['requestType']);

  switch ($requestData['requestType']) {
    case "serachCategories":
      try {
        $res = $inventory->search($requestData);
        echo json_encode($res);
        die;
      } catch (Exception $e) {
        $response['message'] = $e->getMessage();
      }
      break;

    case "upsertCategory":
      try {
        $res = $inventory->upsert($requestData);
        echo json_encode($res);
        die;
        if ($debug) {
          echo "from upsert category php, after calling API ";
          var_dump($res);
        }
      } catch (Exception $e) {
        $response['message'] = $e->getMessage();
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


//  switch (json_last_error()) {
//        case JSON_ERROR_NONE:
//            echo ' - No errors';
//        break;
//        case JSON_ERROR_DEPTH:
//            echo ' - Maximum stack depth exceeded';
//        break;
//        case JSON_ERROR_STATE_MISMATCH:
//            echo ' - Underflow or the modes mismatch';
//        break;
//        case JSON_ERROR_CTRL_CHAR:
//            echo ' - Unexpected control character found';
//        break;
//        case JSON_ERROR_SYNTAX:
//            echo ' - Syntax error, malformed JSON';
//        break;
//        case JSON_ERROR_UTF8:
//            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
//        break;
//        default:
//            echo ' - Unknown error';
//        break;
//  };

?>