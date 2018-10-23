<?php

  // get database connection
  include_once 'config/database.php';
  include_once 'Interface/devotees.php';

  $database = new Database();
  $db = $database->getConnection();

  $devotee = new Devotee($db);
  $requestData = $_GET;
  //print_r($_POST);die;
  
  $res=$devotee->search($requestData);
  //echo $res;   
  echo json_encode($res);
  die;
?>
