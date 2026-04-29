<?php

/**
 * MySQL via PDO. Configure with environment variables (e.g. Cloud Run, docker-compose).
 * Local: copy .env.example to .env or set variables in the shell / Apache.
 *
 * KDMS_DB_HOST may be host:port (e.g. host.docker.internal:3306). PDO requires host and port
 * in separate DSN parts; a single "host:port" in mysql:host= is invalid.
 *
 * Note: PDO::MYSQL_ATTR_CONNECT_TIMEOUT exists only in PHP 8.5+; do not use it on 8.3 images.
 */
require_once dirname(__DIR__, 2) . '/includes/kdms_load_dotenv.php';

class Database
{
    private $mysql_host;
    private $mysql_port;
    private $db_name;
    private $inv_db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        $this->db_name = getenv('KDMS_DB_NAME') ?: 'kdms';
        $this->inv_db_name = getenv('KDMS_DB_INV_NAME') ?: 'kinv2023';
        $this->username = getenv('KDMS_DB_USER') ?: 'kdms';
        $this->password = getenv('KDMS_DB_PASSWORD') ?: 'kdms';

        if (getenv('KDMS_DB_SOCKET')) {
            $this->mysql_host = getenv('KDMS_DB_HOST') ?: 'localhost';
            $this->mysql_port = null;
            return;
        }

        $host_spec = getenv('KDMS_DB_HOST') ?: '127.0.0.1:3306';
        if (preg_match('/^(.+):(\d+)$/', $host_spec, $m)) {
            $this->mysql_host = $m[1];
            $this->mysql_port = (int) $m[2];
        } else {
            $this->mysql_host = $host_spec;
            $p = getenv('KDMS_DB_PORT');
            $this->mysql_port = ($p !== false && $p !== '') ? (int) $p : 3306;
        }
    }

    private function buildDsn(string $database): string
    {
        if (getenv('KDMS_DB_SOCKET')) {
            $socket = getenv('KDMS_DB_SOCKET');

            return 'mysql:unix_socket=' . $socket . ';dbname=' . $database;
        }

        return 'mysql:host=' . $this->mysql_host . ';port=' . $this->mysql_port . ';dbname=' . $database;
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
