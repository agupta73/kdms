<?php

$debug = false;
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if ($debug) {echo "current session ID: ", session_id(), "<br>", "session_status: ", session_status(), "<br>";}

$config_data=include("../site_config.php");
//include_once("../Logic/clsOptionHandler.php");

//Fetch Event Description once and set to Session variable
$serviceClass = new clsServicesManager("");
$serviceClass->setOptionType("config");
$response = array();
$response = $serviceClass->getRecords();
$res = unserialize($response);    
unset($serviceClass);
$eventID = "";

if (!empty($res["event_id"])) {
  $eventID = $res["event_id"];
  $_SESSION["eventId"] = $eventID;
}

if($debug){ echo "event ID: ", $eventID; }

if(!isset($_SESSION["eventDesc"])){
    $_SESSION["eventDesc"] = "";
    if ($debug) {echo "Reset event desc to blank.", "<br>";}
}
if ($debug) {echo "event ID: ", $eventID, "Event desc: ", $_SESSION["eventDesc"] , "<br>";}

if( $eventID == ""){$_SESSION["eventDesc"] = "Event not set";}

if($_SESSION["eventDesc"]== ""){
    if($debug){ echo "Repopulating event description..", "<br>";}
    
    //Pre-populate event record in case of edit
    $optionHandler = new clsServicesManager($eventID);
    $optionHandler->setOptionType("event");  
    $optionHandler->setEventId($eventID);
    $response = $optionHandler->getRecords();
    if($debug){echo "<br> Response: "; var_dump($response);}
    
    //assign values
    if(!empty($response['Event_Status'])){
        if($response['Event_Status'] == "Current"){
            if(!empty($response['Event_Description'])){
                $_SESSION["eventDesc"] = urldecode($response['Event_Description']);
                //$message = $_SESSION["eventDesc"] ;
                if($debug){echo "setting session to: ", $_SESSION["eventDesc"] , "<br>";}
            }
            else{
                $_SESSION["eventDesc"]= "Event not found";
                if($debug){ echo "session not set", "<br>";}
            }
        }
        else{
            $_SESSION["eventDesc"]= "Event ID " . $eventID ." is not current."." <br> "."  Please use Event Manager page.";
            if($debug){ echo "session not set", "<br>";}
        }
    }
    else{
        $_SESSION["eventDesc"]= "Event not found."." <br> "." Please use Event Manager page to initialize.";
        if($debug){ echo "session not set", "<br>";}
    }
}
if ($debug) {echo "eventDesc:";echo $_SESSION['eventDesc'], "<br>"; die;}
?>
