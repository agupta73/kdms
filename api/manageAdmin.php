<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/kdms_log.php';
kdms_log_bootstrap();

header('Content-Type: application/json; charset=utf-8');

  // get database connection
  include_once 'config/database.php';
  include_once 'Interface/clsAdmin.php';

  $database = new Database();
  $db = $database->getConnection();

  if ($db === null) {
      echo json_encode([
          'status'  => false,
          'message' => 'Database connection failed. Check KDMS_DB_* env vars and MySQL reachability.',
      ]);
      exit;
  }

  $report = new clsAdmin($db);
  
  $requestData = $_POST;
  $res = $report->processAdminTask($requestData);

  $json = json_encode($res, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
  if ($json === false) {
      http_response_code(500);
      echo '{"status":false,"message":"Login response could not be encoded."}';

      exit;
  }

  echo $json;

  exit;

