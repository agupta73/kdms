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
        $status = 0;
        // In photo table

        $query2 = "INSERT INTO kdms_ocr_image_bucket
                        SET
                    image_name=:id,
                    image=:photo,
                    status=:status";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(":id", $requestData['image_name']);
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
        $query2 = "DELETE FROM kdms_ocr_image_bucket
                        WHERE
                    image_name=:id";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(":id", $requestData['image_name']);
        if (!$stmt2->execute()) {
            return false;
        } else {
            return true;
        }
    }

    public function get_temp_image_bucket_data($requestData) {
        $query = "SELECT * FROM kdms_ocr_image_bucket;";
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $devoteeSearchResult = array();
        $i = 0;
        while($row = $results->fetchObject()){
            $row->{'image'} = base64_encode($row->{'image'});
            $row->{'image_name'} = $row->{'image_name'};
            $row->{'image_uploaded_at'} = $row->{'image_uploaded_at'};
            $row->{'status'} = $row->{'status'};
            $devoteeSearchResult[]=$row;
            $i = $i+1;
        }
        //var_dump($devoteeSearchResult);
        if($i==0){
            $devoteeSearchResult['status'] = false;
            $devoteeSearchResult['message'] = "No record found!";
            $devoteeSearchResult['info'] = $results;
        }
        return $devoteeSearchResult;
    }
}
?>
