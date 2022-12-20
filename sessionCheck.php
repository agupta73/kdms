<?php
$debug = false;
$result = true;

//session_start();

$config_data = include("site_config.php");
if ($debug) {
    echo "<br>current page ID: ";
    var_dump($current_page_id);
    echo "<br>Search result of current page ID: ";
    var_dump(explode(',', $_SESSION["Access"]));
    var_dump(array_search($current_page_id, explode(',', $_SESSION["Access"])));
    echo "<br>entire session: ";
    var_dump($_SESSION);
    echo "<br>";
    
}
if (session_status() == PHP_SESSION_DISABLED) {
    $result = false;
} else {
    
    if (!isset($_SESSION["eventDesc"])) {
        $result = false;
    } elseif ($_SESSION["eventDesc"] == "") {
        $result = false;
    }
    if ($debug) {
        echo "<br> result : ";
        var_dump($result);
    }
    if (!isset($_SESSION["LoginID"])) {
        $result = false;
    } elseif ($_SESSION["LoginID"] == "") {
        $result = false;
    }
    if ($debug) {
        echo "<br> result : ";
        var_dump($result);
    }
    if (!isset($_SESSION["Role"])) {
        $result = false;
    } elseif ($_SESSION["Role"] == "") {
        $result = false;
    }
    if ($debug) {
        echo "<br> result : ";
        var_dump($result);
    }
    if ($config_data['check_access']) {
        if (!isset($_SESSION["Access"])) {
            $result = false;
        } elseif ($_SESSION["Access"] == "") {
            $result = false;
        } elseif (isset($current_page_id)) {
            if (!in_array($current_page_id, explode(',', $_SESSION["Access"]))) {
                $result = false;                
                print_r("<b>YOU DON'T HAVE ACCESS TO THIS PAGE!!</b>");                
                die;
            }
        }
    }
}

if ($debug) {
    echo "<br> result : ";
    var_dump($result);
}
if(!$result){
    
    $url = $config_data['webroot']."UI/login.php";
    header("Location: ".$url);
    exit();}
?>
