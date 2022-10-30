<?php

$debug = false;
session_start();
if ($debug) {echo "current session ID: ", session_id(), "<br>", "session_status: ", session_status(), "<br>";}

$config_data=include("../site_config.php");
include_once("../Logic/clsOptionHandler.php");

//Fetch Event Description once and set to Session variable
$eventID = $config_data['event_id'];

if ($debug) {echo "eventID:", $eventID, "<br>";}

if(!isset($_SESSION["eventDesc"])){
    $_SESSION["eventDesc"] = "";
    if ($debug) {echo "Reset event desc to blank.", "<br>";}
}
if ($debug) {echo "event ID: ", $eventID, "Event desc: ", $_SESSION["eventDesc"] , "<br>";}
if( $eventID == ""){$_SESSION["eventDesc"] = "Event not set";}

if($_SESSION["eventDesc"]== ""){
    if($debug){ echo "Repopulating event description..", "<br>";}
    //Pre-populate event record in case of edit
    $optionHandler = new clsOptionHandler("EventDetail");
    $optionHandler->setOptionKey($eventID);
    $optionHandler->setEventId($eventID);
    $response = $optionHandler->getOptions();
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
