<?php
// get database connection
  include_once 'config/database.php';
  include_once 'Interface/devotees.php';

  $database = new Database();
  $db = $database->getConnection();

  $devotee = new Devotee($db);
  $requestData = $_POST;
  
  if(!empty($requestData['requestType']) && ($requestData['requestType']="addToPrintQueue" )){
        $res=$devotee->manageCardPrinting($requestData);
  }
  else{
      $res=$devotee->upsertDevotee($requestData);
  }
  if ($res['status']) {
      $response = array('flag' => true, 'info'=>$res['info']);
  } else {
      $response = array('flag' => false,'message'=>$res['message'], 'info'=>$res['info']);
  }
  echo json_encode($response);
  die;
?>
