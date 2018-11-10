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
  //echo $res;   die;
  echo json_encode($res);
//  switch (json_last_error()) {
//        case JSON_ERROR_NONE:
//            echo ' - No errors';
//        break;
//        case JSON_ERROR_DEPTH:
//            echo ' - Maximum stack depth exceeded';
//        break;
//        case JSON_ERROR_STATE_MISMATCH:
//            echo ' - Underflow or the modes mismatch';
//        break;
//        case JSON_ERROR_CTRL_CHAR:
//            echo ' - Unexpected control character found';
//        break;
//        case JSON_ERROR_SYNTAX:
//            echo ' - Syntax error, malformed JSON';
//        break;
//        case JSON_ERROR_UTF8:
//            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
//        break;
//        default:
//            echo ' - Unknown error';
//        break;
//  };
  die;
?>
