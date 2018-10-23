<?php

  // get database connection
  include_once 'config/database.php';
  include_once 'Interface/clsOptions.php';

  $database = new Database();
  $db = $database->getConnection();

  $options = new clsOptions($db);
  $requestData = "Accommodation";
  $res=$options->loadOption($requestData);
  //echo $res;   
  echo json_encode($res);
  die;
?>
