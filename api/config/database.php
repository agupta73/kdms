<?php

/**
 * MySQL via PDO. Configure with environment variables (e.g. Cloud Run, docker-compose, .env).
 * Local: copy .env.example to .env or set variables in the shell / Apache.
 */
class Database
{
    private $host;
    private $db_name;
    private $inv_db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        $this->host = getenv('KDMS_DB_HOST') ?: '127.0.0.1:3306';
        if (getenv('KDMS_DB_SOCKET')) {
            $this->host = getenv('KDMS_DB_HOST') ?: 'localhost';
        }
        $this->db_name = getenv('KDMS_DB_NAME') ?: 'kdms';
        $this->inv_db_name = getenv('KDMS_DB_INV_NAME') ?: 'kinv2023';
        $this->username = getenv('KDMS_DB_USER') ?: 'kdms';
        $this->password = getenv('KDMS_DB_PASSWORD') ?: 'kdms';
    }

    private function buildDsn(string $database): string
    {
        if (getenv('KDMS_DB_SOCKET')) {
            $socket = getenv('KDMS_DB_SOCKET');
            return "mysql:unix_socket=" . $socket . ";dbname=" . $database;
        }
        return "mysql:host=" . $this->host . ";dbname=" . $database;
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                $this->buildDsn($this->db_name),
                $this->username,
                $this->password
            );
            $this->conn->exec('set names utf8');
        } catch (PDOException $exception) {
            if (getenv('KDMS_DB_DEBUG') === '1') {
                echo 'Connection error: ' . $exception->getMessage();
            }
        }
        return $this->conn;
    }

    public function getInvConnection()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                $this->buildDsn($this->inv_db_name),
                $this->username,
                $this->password
            );
            $this->conn->exec('set names utf8');
        } catch (PDOException $exception) {
            if (getenv('KDMS_DB_DEBUG') === '1') {
                echo 'Connection error: ' . $exception->getMessage();
            }
        }
        return $this->conn;
    }
}
