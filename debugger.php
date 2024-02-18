<?php
include('api/config/database.php');
echo "------------------------------------------------------------Database connection--------------------------------<br />";
$db = New Database();
$con=$db->getConnection();
var_dump($con);
?>