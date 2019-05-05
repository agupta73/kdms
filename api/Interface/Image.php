<?php

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
//            $query02 = "INSERT INTO Devotee
//                   SET
//                Devotee_Key=:id";
//            $stmt02 = $this->conn->prepare($query02);
//            $stmt02->bindParam(":id", $devotee_id);
//            if (!$stmt02->execute()) {
//                return false;
//            }
//
//            // In photo table
//            $query2 = "INSERT INTO Devotee_Photo
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

    public function upload($requestData, $devotee_id, $is_update) {

        $rawData = $requestData['image'];
        $filteredData = explode(',', $rawData);
        $unencoded = base64_decode($filteredData[1]);
        //$unencoded = base64_decode($rawData);
        $type = "self";
        $status = 1;
        // Now save this info to db
        /*
         * If it is update ,insert data to Devotee_photo table only.
         * Else create an empty row in Devotee table with given id
         */
        // To devotee table 

        if ($is_update) {  //
//            $query0 = "UPDATE Devotee_Photo
//                        SET
//                        Devotee_Photo=:photo 
//                        WHERE 
//                        Devotee_Key=:id";
            $query0 = "REPLACE INTO Devotee_Photo
                        SET
                        Devotee_Key=:id,
                        Devotee_Photo=:photo,
                        photo_type=:type,
                        status=:status";
            $stmt = $this->conn->prepare($query0);
            $stmt->bindParam(":photo", $unencoded);
            $stmt->bindParam(":id", $devotee_id);
            $stmt->bindParam(":type", $type);
            $stmt->bindParam(":status", $status);

            if (!$stmt->execute()) {
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
                Devotee_Photo=:photo,
                photo_type=:type,
                status=:status";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(":id", $devotee_id);
            $stmt2->bindParam(":photo", $unencoded);
            $stmt2->bindParam(":type", $type);
            $stmt2->bindParam(":status", $status);
            
            if (!$stmt2->execute()) {
                return false;
            } else {
                return true;
            }
        }
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
            $query0 = "REPLACE INTO Devotee_ID
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
            $query02 = "INSERT INTO Devotee
                   SET
                Devotee_Key=:id";
            $stmt02 = $this->conn->prepare($query02);
            $stmt02->bindParam(":id", $devotee_id);
            if (!$stmt02->execute()) {
                return false;
            }

            // In ID table
            $type = "self";
            $query2 = "INSERT INTO Devotee_ID
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
