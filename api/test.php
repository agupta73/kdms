<?php

$servername = "anilMac.local";
$username = "kdms";
$password = "kdms";
$database = "kdms_2025_prod";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to MySQL successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
