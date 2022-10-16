<?php
$debug = false;
$result = true;
$config_data=include("../site_config.php");
if($debug){var_dump(session_status());}
if(session_status() == PHP_SESSION_DISABLED) { $result=false; }
else {
    if (!isset($_SESSION["eventDesc"])) {$result = false; }
    elseif ($_SESSION["eventDesc"] == "") {$result = false;}

    if(!isset($_SESSION["LoginID"])){$result = false;}
    elseif($_SESSION["LoginID"] == ""){ $result = false;}

    if(!isset($_SESSION["Role"])){$result = false;}
    elseif($_SESSION["Role"] == ""){ $result = false;}

    if ($debug) {
        echo "SessionCheck", "result: ";
        var_dump($result);
        die;
    }
}
if(!$result){
    $url = $config_data['webroot']."UI/login.php";
    header("Location: ".$url);
    exit();}
?>
