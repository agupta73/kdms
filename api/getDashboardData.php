<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/api_session.php';


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
