<?php

Class Image {

    //private $ImageDir = '../assets/devotee/';
    private $ImageDir = '../assets/devotee/';
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function upload_old($requestData, $devotee_id, $is_update = false) {
        $rawData = $requestData['image'];
        $filteredData = explode(',', $rawData);
        $unencoded = base64_decode($filteredData[1]);
        $iname = $devotee_id . '.png';
        if (!file_exists($this->ImageDir . date('Y'))) {
            mkdir($this->ImageDir . date('Y'), 0777);
        }
        $imgWithLoc = $this->ImageDir . date('Y') . '/' . $iname;
        //Create the image 
        $fp = fopen($imgWithLoc, 'a+');
        fwrite($fp, $unencoded);
        fclose($fp);
        // Now save this info to db
        /*
         * If it is update ,insert data to Devotee_photo table only.
         * Else create an empty row in Devotee table with given id
         */
        // To devotee table 
        if ($is_update) {  //
            $status = 0; // Disable all old
            $query0 = "UPDATE
                	Devotee_Photo
            SET
                status=:status  Where Devotee_Key:id";
            $stmt = $this->conn->prepare($query0);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":id", $devotee_id);
            if (!$stmt->execute()) {
                return false;
            }
            // Insert new

            $query1 = "INSERT INTO
                	Devotee_Photo
            SET
                Devotee_Photo=:photo,
                Devotee_Key:id";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->bindParam(":photo", $iname);
            $stmt1->bindParam(":id", $devotee_id);
            if (!$stmt1->execute()) {
                return false;
            } else {
                return true;
            }
        } else {
            // In devotee table
            $query02 = "INSERT INTO Devotee
                   SET
                Devotee_Key=:id";
            $stmt02 = $this->conn->prepare($query02);
            $stmt02->bindParam(":id", $devotee_id);
            if (!$stmt02->execute()) {
                return false;
            }

            // In photo table
            $query2 = "INSERT INTO Devotee_Photo
                   SET
                Devotee_Key=:id,
                Devotee_Photo=:photo;";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(":id", $devotee_id);
            $stmt2->bindParam(":photo", $iname);
            if (!$stmt2->execute()) {
                return false;
            } else {
                return true;
            }
        }
    }

    public function upload($requestData, $devotee_id, $is_update ) {
        $rawData = $requestData['image'];
        $filteredData = explode(',', $rawData);
        $unencoded = base64_decode($filteredData[1]);
        // Now save this info to db
        /*
         * If it is update ,insert data to Devotee_photo table only.
         * Else create an empty row in Devotee table with given id
         */
        // To devotee table 
        if ($is_update) {  //
             
            $query0 = "UPDATE Devotee_Photo
                        SET
                        Devotee_Photo=:photo 
                        WHERE 
                        Devotee_Key=:id";
                        
            $stmt = $this->conn->prepare($query0);
            $stmt->bindParam(":photo", $unencoded);
            
            $stmt->bindParam(":id", $devotee_id);
            if (!$stmt->execute()) {
                return false;
            }

        } else {
            // In devotee table
            $query02 = "INSERT INTO Devotee
                   SET
                Devotee_Key=:id";
            $stmt02 = $this->conn->prepare($query02);
            $stmt02->bindParam(":id", $devotee_id);
            if (!$stmt02->execute()) {
                return false;
            }

            // In photo table
            $query2 = "INSERT INTO Devotee_Photo
                   SET
                Devotee_Key=:id,
                Devotee_Photo=:photo;";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(":id", $devotee_id);
            $stmt2->bindParam(":photo", $unencoded);
            if (!$stmt2->execute()) {
                return false;
            } else {
                return true;
            }
        }
    }

}

?>
