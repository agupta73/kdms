<?php
// interface to upload images in the temprory bucket.
Class TempBucketImageUpload {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function upload($requestData) {

        $rawData = $requestData['image'];
        $filteredData = explode(',', $rawData);
        $unencoded = base64_decode($filteredData[1]);
        //$unencoded = base64_decode($rawData);
        $status = 0;
        // In photo table

        $query2 = "INSERT INTO kdms_ocr_image_bucket
                        SET
                    image_name=:id,
                    image=:photo,
                    status=:status";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(":id", $rawData['image_name']);
        $stmt2->bindParam(":photo", $unencoded);
        $stmt2->bindParam(":status", $status);

        if (!$stmt2->execute()) {
            return false;
        } else {
            return true;
        }
    }
    public function delete_images($requestData) {
        // if image_name is empty then clear bucket with all status 1 images
    }
}
?>
