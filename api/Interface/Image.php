<?php

require_once dirname(__DIR__, 2) . '/includes/PhotoStorage.php';
require_once dirname(__DIR__, 2) . '/includes/kdms_log.php';

Class Image {

    //private $ImageDir = '../assets/devotee/';
    private $ImageDir = '../assets/devotee/';
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /* Function    upload_old will be used if we want to store images in folder not in datatbase.
      So don't remove it.
     * 
     */

//    public function upload_old($requestData, $devotee_id, $is_update = false) {
//        $rawData = $requestData['image'];
//        $filteredData = explode(',', $rawData);
//        $unencoded = base64_decode($filteredData[1]);
//        $iname = $devotee_id . '.png';
//        if (!file_exists($this->ImageDir . date('Y'))) {
//            mkdir($this->ImageDir . date('Y'), 0777);
//        }
//        $imgWithLoc = $this->ImageDir . date('Y') . '/' . $iname;
//        //Create the image 
//        $fp = fopen($imgWithLoc, 'a+');
//        fwrite($fp, $unencoded);
//        fclose($fp);
//        // Now save this info to db
//        /*
//         * If it is update ,insert data to Devotee_photo table only.
//         * Else create an empty row in Devotee table with given id
//         */
//        // To devotee table 
//        if ($is_update) {  //
//            $status = 0; // Disable all old
//            $query0 = "UPDATE
//                	Devotee_Photo
//            SET
//                status=:status  Where Devotee_Key:id";
//            $stmt = $this->conn->prepare($query0);
//            $stmt->bindParam(":status", $status);
//            $stmt->bindParam(":id", $devotee_id);
//            if (!$stmt->execute()) {
//                return false;
//            }
//            // Insert new
//
//            $query1 = "INSERT INTO
//                	Devotee_Photo
//            SET
//                Devotee_Photo=:photo,
//                Devotee_Key:id";
//            $stmt1 = $this->conn->prepare($query1);
//            $stmt1->bindParam(":photo", $iname);
//            $stmt1->bindParam(":id", $devotee_id);
//            if (!$stmt1->execute()) {
//                return false;
//            } else {
//                return true;
//            }
//        } else {
//            // In devotee table
//            $query02 = "INSERT INTO devotee
//                   SET
//                Devotee_Key=:id";
//            $stmt02 = $this->conn->prepare($query02);
//            $stmt02->bindParam(":id", $devotee_id);
//            if (!$stmt02->execute()) {
//                return false;
//            }
//
//            // In photo table
//            $query2 = "INSERT INTO devotee_photo
//                   SET
//                Devotee_Key=:id,
//                Devotee_Photo=:photo;";
//            $stmt2 = $this->conn->prepare($query2);
//            $stmt2->bindParam(":id", $devotee_id);
//            $stmt2->bindParam(":photo", $iname);
//            if (!$stmt2->execute()) {
//                return false;
//            } else {
//                return true;
//            }
//        }
//    }

    /**
     * @return array{gcs: ?string, blob: ?string}
     */
    private function persistPhotoBytes(string $devoteeKey, string $bytes): array
    {
        $path = PhotoStorage::objectPathForPhoto($devoteeKey);
        $written = PhotoStorage::writeGcsObject($path, $bytes, 'image/jpeg');
        if ($written !== null) {
            kdms_log('INFO', 'Staff devotee photo written to GCS', [
                'devotee_key' => $devoteeKey,
                'path' => $path,
            ]);

            // Keep BLOB on upload so UI can read photos when GCS read is unavailable locally.
            return ['gcs' => $path, 'blob' => $bytes];
        }

        kdms_log('ERROR', 'Staff devotee photo GCS write failed; using BLOB fallback', [
            'devotee_key' => $devoteeKey,
            'path' => $path,
        ]);

        return ['gcs' => null, 'blob' => $bytes];
    }

    /**
     * @param array<string, mixed> $requestData
     */
    private function decodeDataUrlImage(array $requestData, string $devoteeKey, string $label): ?string
    {
        $rawData = $requestData['image'] ?? '';
        if (!is_string($rawData) || strpos($rawData, ',') === false) {
            kdms_log('ERROR', 'Staff ' . $label . ' upload: missing or invalid image data', [
                'devotee_key' => $devoteeKey,
            ]);

            return null;
        }
        $filteredData = explode(',', $rawData, 2);
        $unencoded = base64_decode($filteredData[1], true);
        if ($unencoded === false || strlen($unencoded) < 64) {
            kdms_log('ERROR', 'Staff ' . $label . ' upload: base64 decode failed or image too small', [
                'devotee_key' => $devoteeKey,
            ]);

            return null;
        }

        return $unencoded;
    }

    private function saveDevoteePhotoRow(string $devoteeKey, ?string $gcsPath, ?string $blob, string $photoType, int $status): bool
    {
        $query0 = 'REPLACE INTO devotee_photo
                    SET Devotee_Key = :id,
                        Devotee_Photo_Gcs_Path = :gcs,
                        Devotee_Photo = :photo,
                        photo_type = :type,
                        status = :status';
        $stmt = $this->conn->prepare($query0);
        $stmt->bindValue(':id', $devoteeKey);
        $stmt->bindValue(':gcs', $gcsPath);
        $stmt->bindValue(':photo', $blob, $blob === null ? PDO::PARAM_NULL : PDO::PARAM_LOB);
        $stmt->bindValue(':type', $photoType);
        $stmt->bindValue(':status', $status, PDO::PARAM_INT);

        return (bool) $stmt->execute();
    }

    /**
     * @param bool $stageOnly When true (reserved key, no devotee row yet), only write devotee_photo — no INSERT into devotee.
     */
    public function upload($requestData, $devotee_id, $is_update, $stageOnly = false) {

        $unencoded = $this->decodeDataUrlImage($requestData, (string) $devotee_id, 'photo');
        if ($unencoded === null) {
            return false;
        }
        $type = "self";
        $status = 1;
        $stored = $this->persistPhotoBytes((string) $devotee_id, $unencoded);

        if ($is_update || $stageOnly) {
            return $this->saveDevoteePhotoRow(
                (string) $devotee_id,
                $stored['gcs'],
                $stored['blob'],
                $type,
                $status
            );
        }

        $query02 = "INSERT INTO devotee SET Devotee_Key=:id";
        $stmt02 = $this->conn->prepare($query02);
        $stmt02->bindParam(":id", $devotee_id);
        if (!$stmt02->execute()) {
            return false;
        }

        return $this->saveDevoteePhotoRow(
            (string) $devotee_id,
            $stored['gcs'],
            $stored['blob'],
            $type,
            $status
        );
    }

    /**
     * @return array{gcs: ?string, blob: ?string}
     */
    private function persistIdImageBytes(string $devoteeKey, string $bytes): array
    {
        $path = PhotoStorage::objectPathForIdImage($devoteeKey);
        $written = PhotoStorage::writeGcsObject($path, $bytes, 'image/jpeg');
        if ($written !== null) {
            kdms_log('INFO', 'Staff devotee ID image written to GCS', [
                'devotee_key' => $devoteeKey,
                'path' => $path,
            ]);

            return ['gcs' => $path, 'blob' => $bytes];
        }

        kdms_log('ERROR', 'Staff devotee ID image GCS write failed; using BLOB fallback', [
            'devotee_key' => $devoteeKey,
            'path' => $path,
        ]);

        return ['gcs' => null, 'blob' => $bytes];
    }

    private function saveDevoteeIdRow(string $devoteeKey, ?string $gcsPath, ?string $blob, string $idType): bool
    {
        $query0 = 'REPLACE INTO devotee_id
                    SET Devotee_Key = :id,
                        Devotee_ID_Image_Gcs_Path = :gcs,
                        Devotee_ID_Image = :photo,
                        Devotee_ID_Type = :type';
        $stmt = $this->conn->prepare($query0);
        $stmt->bindValue(':id', $devoteeKey);
        $stmt->bindValue(':gcs', $gcsPath);
        $stmt->bindValue(':photo', $blob, $blob === null ? PDO::PARAM_NULL : PDO::PARAM_LOB);
        $stmt->bindValue(':type', $idType);

        try {
            return (bool) $stmt->execute();
        } catch (Exception $e) {
            kdms_log('ERROR', 'saveDevoteeIdRow failed', ['devotee_key' => $devoteeKey, 'error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * @param bool $stageOnly When true, only write devotee_id — no INSERT into devotee.
     */
    /**
     * @param array<string, mixed> $requestData
     */
    public function uploadDocumentID($requestData, $devotee_id, $is_update, $stageOnly = false) {

        $unencoded = $this->decodeDataUrlImage($requestData, (string) $devotee_id, 'ID image');
        if ($unencoded === null) {
            return false;
        }

        return $this->uploadDocumentIDBytes(
            $unencoded,
            (string) $devotee_id,
            $is_update,
            $stageOnly,
            $this->resolveIdTypeFromRequest($requestData)
        );
    }

    /**
     * Multipart upload from staff UI (avoids huge base64 POST bodies).
     *
     * @param array<string, mixed> $file $_FILES['id_image']
     * @param array<string, mixed> $requestData
     */
    public function uploadDocumentIDFile(array $file, $devotee_id, $is_update, $stageOnly, array $requestData): bool
    {
        $tmp = $file['tmp_name'] ?? '';
        if (!is_string($tmp) || $tmp === '' || !is_uploaded_file($tmp)) {
            kdms_log('ERROR', 'Staff ID image upload: missing uploaded file', [
                'devotee_key' => $devotee_id,
            ]);

            return false;
        }

        $bytes = file_get_contents($tmp);
        if ($bytes === false || strlen($bytes) < 64) {
            kdms_log('ERROR', 'Staff ID image upload: file empty or unreadable', [
                'devotee_key' => $devotee_id,
            ]);

            return false;
        }

        $size = (int) ($file['size'] ?? strlen($bytes));
        if ($size > 5 * 1024 * 1024) {
            kdms_log('ERROR', 'Staff ID image upload: file exceeds 5MB', [
                'devotee_key' => $devotee_id,
                'size' => $size,
            ]);

            return false;
        }

        return $this->uploadDocumentIDBytes(
            $bytes,
            (string) $devotee_id,
            $is_update,
            $stageOnly,
            $this->resolveIdTypeFromRequest($requestData)
        );
    }

    /**
     * @param array<string, mixed> $requestData
     */
    private function resolveIdTypeFromRequest(array $requestData): string
    {
        if (!empty($requestData['devotee_id_type'])) {
            return (string) $requestData['devotee_id_type'];
        }
        if (!empty($requestData['Devotee_ID_Type'])) {
            return (string) $requestData['Devotee_ID_Type'];
        }

        return 'self';
    }

    private function uploadDocumentIDBytes(
        string $unencoded,
        string $devotee_id,
        bool $is_update,
        bool $stageOnly,
        string $type
    ): bool {
        $stored = $this->persistIdImageBytes($devotee_id, $unencoded);

        if ($is_update || $stageOnly) {
            return $this->saveDevoteeIdRow($devotee_id, $stored['gcs'], $stored['blob'], $type);
        }

        $query02 = 'INSERT INTO devotee SET Devotee_Key=:id';
        $stmt02 = $this->conn->prepare($query02);
        $stmt02->bindParam(':id', $devotee_id);
        if (!$stmt02->execute()) {
            return false;
        }

        return $this->saveDevoteeIdRow($devotee_id, $stored['gcs'], $stored['blob'], $type);
    }

    public function uploadDocument($requestData, $devotee_id, $is_update) {

        $file_data = file_get_contents($requestData['file']['tmp_name']);
        $type = "self";
        //$status=1;
        // Now save this info to db
        /*
         * If it is update ,insert data to Devotee_ID table only.
         * Else create an empty row in Devotee table with given
         */
        // To devotee table 
        if ($is_update) {  //
            $query0 = "REPLACE INTO devotee_id
                        SET
                        Devotee_ID_Image=:photo,
                        Devotee_Key=:id,
                        Devotee_ID_Type=:type";
            $stmt = $this->conn->prepare($query0);
            $stmt->bindParam(":photo", $file_data);
            $stmt->bindParam(":id", $devotee_id);
            $stmt->bindParam(":type", $type);
            try {
                $stmt->execute();
                $error_info = $stmt->errorInfo();
                if (!empty($error_info[0] != '00000')) {
                    print_r($error_info);
                    die;
                }
                //print_r($stmt->errorInfo());
            } catch (Exception $e) {
                echo 'Error : ' . $e->getMessage();
                die;
            }
            return true;
        } else {
            // In devotee table
            $query02 = "INSERT INTO devotee
                   SET
                Devotee_Key=:id";
            $stmt02 = $this->conn->prepare($query02);
            $stmt02->bindParam(":id", $devotee_id);
            if (!$stmt02->execute()) {
                return false;
            }

            // In ID table
            $type = "self";
            $query2 = "INSERT INTO devotee_id
                   SET
                Devotee_Key=:id,
                Devotee_ID_Image=:photo,
                Devotee_ID_Type=:type";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(":id", $devotee_id);

            $stmt2->bindParam(":photo", $file_data);
            $stmt2->bindParam(":type", $type);
            try {
                $stmt2->execute();
                $error_info = $stmt2->errorInfo();
                if (!empty($error_info[0] != '00000')) {
                    print_r($error_info);
                    die;
                }
            } catch (Exception $e) {
                echo 'Error : ' . $e->getMessage();
                die;
            }
            return true;
        }
    }

}

?>
