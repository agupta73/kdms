<?php

  // get database connection
  include_once 'config/database.php';
  include_once 'Interface/clsDashboard.php';

  $database = new Database();
  $db = $database->getConnection();

  $report = new clsDashboard($db);
  
  $requestData = $_GET;
  $res=$report->getReport($requestData);
  
  echo json_encode($res);
  
  die;
?>
