<?php

  // get database connection
  include_once 'config/database.php';
  include_once 'Interface/clsAdmin.php';

  $database = new Database();
  $db = $database->getConnection();

  $report = new clsAdmin($db);
  
  $requestData = $_POST;
  $res=$report->processAdminTask($requestData);
  
  echo json_encode($res);
  //echo $res;
  
  die;
?>
