<?php
// get database connection
  include_once 'config/database.php';
  include_once 'Interface/devotees.php';

  $database = new Database();
  $db = $database->getConnection();

  $devotee = new Devotee($db);
  $requestData = $_POST;
  
  if (!empty($requestData['requestType'])) {
    switch ($requestData['requestType']) {
        case "addToPrintQueue":
        case "removeFromPrintQueue":
            $res = $devotee->manageCardPrinting($requestData);
            break;

        case "manageAmenity":
            $res = $devotee->manageAmenityAllocation($requestData);
            break;
        
        default:
            $res = $devotee->upsertDevotee($requestData);
            break;
    }
}

if ($res['status']) {
      $response = array('flag' => true, 'info'=>$res['info']);
  } else {
      $response = array('flag' => false,'message'=>$res['message'], 'info'=>$res['info']);
  }
  echo json_encode($response);
  die;
?>
