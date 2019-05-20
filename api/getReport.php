<?php

  // get database connection
  include_once 'config/database.php';
  include_once 'Interface/clsReport.php';

  $database = new Database();
  $db = $database->getConnection();

  $report = new clsReport($db);
  
  $requestData = $_GET;
  $res=$report->getReport($requestData);
  
  echo json_encode($res);
  
  die;
?>
