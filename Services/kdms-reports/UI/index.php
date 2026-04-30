<?php
$debug = false;
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$current_page_id = "KR-DSBRD";
include_once("../sessionCheck.php");
$config_data = include("../site_config.php");
define('KMREPORTS_SESSION_READY', true);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php include_once("header.php"); ?>
  </head>
  <body class="">
    <div class="wrapper ">
      <?php include_once("nav.php"); ?>
      <div class="main-panel">
        <?php include("dashboard.php");?>
      </div>
    </div>
    <!--   Core JS Files   -->
    <?php include_once("scriptJS.php") ?>
  </body>
</html>
