<?php
// get database connection
  include_once 'config/database.php';
  include_once 'Interface/devotees.php';

  $database = new Database();
  $db = $database->getConnection();

  $devotee = new Devotee($db);
  $requestData = $_POST;
  //echo json_encode($requestData); die;
  $res = array();
  $response = array('flag' => false,'message'=>"Request failed", 'info'=>$requestData);
  // echo json_encode($response); die;
  if (!empty($requestData['requestType'])) {
      $response = array('flag' => false,'message'=>"Request failed", 'info'=>$requestData['requestType']);
    switch ($requestData['requestType']) {
        case "addToPrintQueue":
        case "removeFromPrintQueue":
            $res = $devotee->manageCardPrinting($requestData);
            break;

        case "manageAmenity":
            $res = $devotee->manageAmenityAllocation($requestData);
            break;
        
        case "upsertDevotee":
            
            $res = $devotee->upsertDevotee($requestData);
            break;
        
        default :
            $response = array('flag' => false,'message'=>"Request type not specified or incorrect", 'info'=>$requestData['requestType']);
            break;
    }
}
else {
    $response = array('flag' => false,'message'=>"Request data empty", 'info'=>$requestData);
}
    
if(!empty($res['status'])){
    
if ($res['status']) {
      $response = array('flag' => true,'message'=>$res['message'], 'info'=>$res['info']);
  } else {
      $response = array('flag' => false,'message'=>$res['message'], 'info'=>$res['info']);
  }
  
}
  echo json_encode($response);
  die;
?>
