<?php

class Database {

    private $host = "192.168.0.23:3306";
    private $db_name = "KDMS";
	//private $db_name = "kdms_testing";
	
    private $username = "kdms";
    private $password = "kdms";
    public $conn;

    // get the database connection
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
