<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/api_session.php';

// Setting
$Interface_path = "Interface/";
$requestData = $_POST;
if ($requestData === [] && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    if ($contentLength > 0) {
        res_error('Upload payload too large for server limits (post_max_size). Try a smaller image.');
    }
}
$api_type = 0; // Default
// each api call will have "api_type" to reconize it.
if (!empty($requestData['api_type'])) {
    $api_type = (int) $requestData['api_type'];
}

include_once $Interface_path . 'Image.php';
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

/**
 * @return bool
 */
function kdms_devotee_row_exists(PDO $db, string $devoteeKey): bool
{
    $stmt = $db->prepare('SELECT 1 FROM devotee WHERE Devotee_Key = :k LIMIT 1');
    $stmt->execute(['k' => strtoupper(trim($devoteeKey))]);

    return (bool) $stmt->fetchColumn();
}

// #3 Profile image upload
if ($api_type === 3) {
    if (empty($requestData['devotee_key'])) {
        res_error('devotee_key is required. Refresh Add Devotee to reserve a new key.');
    }
    $devotee_key = strtoupper(trim((string) $requestData['devotee_key']));
    $rowExists = kdms_devotee_row_exists($db, $devotee_key);
    $stageOnly = !$rowExists;

    $imageClass = new Image($db);
    if ($imageClass->upload($requestData, $devotee_key, $rowExists, $stageOnly)) {
        res_success($rowExists ? 'Devotee image updated successfully !' : $devotee_key);
    } else {
        res_error('Error while updating devotee image !');
    }
} elseif ($api_type === 4) {
    if (empty($requestData['devotee_key'])) {
        res_error('devotee_key is required. Refresh Add Devotee to reserve a new key.');
    }
    $devotee_key = strtoupper(trim((string) $requestData['devotee_key']));
    $rowExists = kdms_devotee_row_exists($db, $devotee_key);
    $stageOnly = !$rowExists;

    $imageClass = new Image($db);
    $uploaded = false;
    if (
        !empty($_FILES['id_image']['tmp_name'])
        && is_uploaded_file((string) $_FILES['id_image']['tmp_name'])
    ) {
        $uploaded = $imageClass->uploadDocumentIDFile($_FILES['id_image'], $devotee_key, $rowExists, $stageOnly, $requestData);
    } elseif (!empty($requestData['image'])) {
        $uploaded = $imageClass->uploadDocumentID($requestData, $devotee_key, $rowExists, $stageOnly);
    } else {
        res_error('ID image is required (multipart id_image or base64 image field).');
    }
    if ($uploaded) {
        res_success($rowExists ? 'Devotee document id image updated successfully!' : $devotee_key);
    } else {
        res_error('Error while updating document id image!');
    }
} else {
    res_error('Unsupported api_type');
}

function res_success($msg) {
    echo json_encode(['message' => $msg, 'status' => true]);
    die;
}

function res_error($msg) {
    echo json_encode(['message' => $msg, 'status' => false]);
    die;
}
