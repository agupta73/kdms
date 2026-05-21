<?php

declare(strict_types=1);

/**
 * PDO for CLI scripts (repair, migrate, etc.). Loads kdms/.env via kdms_load_dotenv.php.
 *
 * From Mac/XAMPP host:
 *   KDMS_DB_HOST=127.0.0.1:3306 KDMS_DB_PASSWORD=... php scripts/...
 *
 * Or copy .env.example to .env in the project root.
 */
function kdms_cli_connect_database(): PDO
{
    require_once __DIR__ . '/kdms_load_dotenv.php';
    require_once dirname(__DIR__) . '/api/config/database.php';

    $database = new Database();
    $pdo = $database->getConnection();
    if ($pdo instanceof PDO) {
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    $dbName = getenv('KDMS_DB_NAME') ?: 'kdms';
    $user = getenv('KDMS_DB_USER') ?: 'kdms';
    $password = getenv('KDMS_DB_PASSWORD') ?: 'kdms';
    $hostSpec = getenv('KDMS_DB_HOST') ?: '127.0.0.1:3306';
    $socket = getenv('KDMS_DB_SOCKET');

    fwrite(STDERR, "MySQL connection failed (getConnection returned null).\n");
    fwrite(STDERR, "  KDMS_DB_NAME={$dbName}\n");
    fwrite(STDERR, "  KDMS_DB_USER={$user}\n");
    if (is_string($socket) && $socket !== '') {
        fwrite(STDERR, "  KDMS_DB_SOCKET={$socket}\n");
    } else {
        fwrite(STDERR, "  KDMS_DB_HOST={$hostSpec}\n");
    }
    fwrite(STDERR, "  Set KDMS_DB_DEBUG=1 to see PDO error from Database class.\n");
    fwrite(STDERR, "  Tip: copy .env.example to .env, or export KDMS_DB_HOST=127.0.0.1:3306 (XAMPP).\n");

    if (is_string($socket) && $socket !== '') {
        $dsn = 'mysql:unix_socket=' . $socket . ';dbname=' . $dbName;
    } elseif (preg_match('/^(.+):(\d+)$/', $hostSpec, $m)) {
        $dsn = 'mysql:host=' . $m[1] . ';port=' . $m[2] . ';dbname=' . $dbName;
    } else {
        $port = getenv('KDMS_DB_PORT') ?: '3306';
        $dsn = 'mysql:host=' . $hostSpec . ';port=' . $port . ';dbname=' . $dbName;
    }

    return new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}
