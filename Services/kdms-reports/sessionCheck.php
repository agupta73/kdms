<?php
$debug = false;
$result = true;
require_once __DIR__ . '/kmreports_log.php';
kmreports_log_bootstrap();

//session_start();

$config_data = include("site_config.php");
if ($debug) {
    echo "<br>current page ID: ";
    var_dump($current_page_id);
    echo "<br>Search result of current page ID: ";
    var_dump(explode(',', $_SESSION["Access"]));
    var_dump(array_search($current_page_id, explode(',', $_SESSION["Access"])));
    echo "<br>";
}
if (session_status() == PHP_SESSION_DISABLED) {
    $result = false;
} else {
    if (!isset($_SESSION["eventId"])) {
        $result = false;
    } elseif ($_SESSION["eventId"] == "") {
        $result = false;
    }

    if (!isset($_SESSION["eventDesc"])) {
        $result = false;
    } elseif ($_SESSION["eventDesc"] == "") {
        $result = false;
    }

    if (!isset($_SESSION["LoginID"])) {
        $result = false;
    } elseif ($_SESSION["LoginID"] == "") {
        $result = false;
    }

    if (!isset($_SESSION["Role"])) {
        $result = false;
    } elseif ($_SESSION["Role"] == "") {
        $result = false;
    }
    
    if ($config_data['check_access']) {
        if (!isset($_SESSION["Access"])) {
            $result = false;
        } elseif ($_SESSION["Access"] == "") {
            $result = false;
        } elseif (isset($current_page_id)) {
            if (!in_array($current_page_id, explode(',', $_SESSION["Access"]))) {
                $result = false;                
                kmreports_log('WARNING', 'KMReports page ACL denied', ['page_id' => $current_page_id]);
                print_r("<b>YOU DON'T HAVE ACCESS TO THIS PAGE!!</b>");                
                die;
            }
        }
    }
}

if (!$result) {
    kmreports_log('NOTICE', 'KMReports session/auth check failed');

    $url = $config_data['local_root'] . "/UI/login.php";
    header("Location: " . $url);
    
    exit();
}
?>