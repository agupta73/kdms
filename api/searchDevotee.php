<?php

  // get database connection
  include_once 'config/database.php';
  include_once 'Interface/devotees.php';

  $database = new Database();
  $db = $database->getConnection();

  $devotee = new Devotee($db);
  $requestData = $_GET;
   //echo "--&&++++++++++++++++++++++++++++++++++++++++++++++++";
   //var_dump($_GET);
   //die;
  try{
        $res=$devotee->search($requestData);
        if($requestData['mode'] != 'DYN'){
          echo json_encode($res);
        }
        else {
          echo $res;
        }
  }
  catch (Exception $e) {
        echo  $e->getMessage();                        
        die;
    }
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
