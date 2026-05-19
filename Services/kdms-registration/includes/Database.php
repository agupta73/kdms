<?php

declare(strict_types=1);

namespace KdmsRegistration;

use PDO;
use PDOException;

final class Database
{
    private ?PDO $conn = null;

    public function pdo(): PDO
    {
        if ($this->conn instanceof PDO) {
            return $this->conn;
        }

        $name = getenv('KDMS_DB_NAME') ?: 'kdms';
        $user = getenv('KDMS_DB_USER') ?: 'kdms_reg';
        $pass = getenv('KDMS_DB_PASSWORD') ?: '';

        if (getenv('KDMS_DB_SOCKET')) {
            $dsn = 'mysql:unix_socket=' . getenv('KDMS_DB_SOCKET') . ';dbname=' . $name;
        } else {
            $hostSpec = getenv('KDMS_DB_HOST') ?: '127.0.0.1:3306';
            if (preg_match('/^(.+):(\d+)$/', $hostSpec, $m)) {
                $host = $m[1];
                $port = (int) $m[2];
            } else {
                $host = $hostSpec;
                $port = (int) (getenv('KDMS_DB_PORT') ?: 3306);
            }
            $dsn = 'mysql:host=' . $host . ';port=' . $port . ';dbname=' . $name;
        }

        try {
            $this->conn = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            $this->conn->exec('SET NAMES utf8mb4');
        } catch (PDOException $e) {
            kdms_log('ERROR', 'Database connection failed', ['error' => $e->getMessage()]);
            throw $e;
        }

        return $this->conn;
    }
}
