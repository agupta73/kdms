<?php
// get database connection
  include_once 'config/database.php';
  include_once 'Interface/clsOptions.php';

  $database = new Database();
  $db = $database->getConnection();

  $options = new clsOptions($db);
  $requestData = $_POST;
  //echo "reaching api..";
  //var_dump($requestData);
  $res=$options->upsertOption($requestData);
  
//  if ($res['status']) {
//      $response = array('flag' => true, 'info'=>$res['info']);
//  } else {
//      $response = array('flag' => false,'message'=>$res['message'], 'info'=>$res['info']);
//  }
  echo json_encode($res);
  die;
?>
