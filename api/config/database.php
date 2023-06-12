<?php

class Database
{

    private $host = "KainchiDell:3306";
    //private $host = "192.168.0.100:3306";
    private $db_name = "kdms";
    private $inv_db_name = 'kinv2023';
    //private $db_name = "kdms_gold";
    //private $inv_db_name = 'kinv_gold';
    //private $host = "localhost";
    //private $db_name = "kdms2023";
    //private $inv_db_name = 'kinv2023';
    //private $db_name = "kdms_gold";
    //private $inv_db_name = 'kinv_gold';

    private $username = "kdms";
    private $password = "kdms";
    public $conn;

    // get the database connection
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
    public function getInvConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->inv_db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
