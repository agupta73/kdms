<?php

$servername = "mysql";
$username = "kdms_user";
$password = "root123";
$database = "kdms";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to MySQL successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
