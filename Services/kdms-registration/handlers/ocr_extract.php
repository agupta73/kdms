<?php

declare(strict_types=1);

use KdmsRegistration\DocumentAiOcr;
use KdmsRegistration\RegistrationGcs;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    reg_json_response(405, ['error' => 'Method not allowed']);
    exit;
}

if (empty($_FILES['id_image']) || !is_uploaded_file($_FILES['id_image']['tmp_name'] ?? '')) {
    reg_json_response(400, ['error' => 'id_image is required']);
    exit;
}

$file = $_FILES['id_image'];
$size = (int) ($file['size'] ?? 0);
if ($size <= 0 || $size > 5 * 1024 * 1024) {
    reg_json_response(400, ['error' => 'Image must be between 1 byte and 5MB']);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']) ?: '';
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
if (!isset($allowed[$mime])) {
    reg_json_response(400, ['error' => 'Only JPEG or PNG images are allowed']);
    exit;
}

$bytes = file_get_contents($file['tmp_name']);
if ($bytes === false) {
    reg_json_response(400, ['error' => 'Could not read uploaded image']);
    exit;
}

$stagingPath = RegistrationGcs::stagingIdPath();
$contentType = $mime === 'image/png' ? 'image/png' : 'image/jpeg';
$saved = RegistrationGcs::uploadBytes($stagingPath, $bytes, $contentType);
if ($saved === null) {
    reg_json_response(500, ['error' => 'Could not store image. Please try again.']);
    exit;
}

$fields = DocumentAiOcr::extract($bytes, $mime);
$fields['id_staging_gcs_path'] = $saved;

reg_json_response(200, $fields);
