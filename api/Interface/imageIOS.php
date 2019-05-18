<?php

Class ImageIOS {

    //private $ImageDir = '../assets/devotee/';
    private $ImageDir = '../assets/devotee/';
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function upload($requestData, $devotee_id, $photoType ) {
        
        $rawData = $requestData['image'];
        $filteredData = explode(',', $rawData);
        $unencoded = base64_decode($filteredData[0]);
        
        if (!empty($photoType)) {
            $type=$photoType;
        } else {
            $type = "self";
        }
        
            
        $status=1;
        // Now save this info to db
        /*
         * If it is update ,insert data to Devotee_photo table only.
         * Else create an empty row in Devotee table with given id
         */
        // To devotee table 
        
//        if ($is_update) {  //
             
//            $query0 = "UPDATE Devotee_Photo
//                        SET
//                        Devotee_Photo=:photo 
//                        WHERE 
//                        Devotee_Key=:id";
                 
            $query0 = "REPLACE INTO Devotee_Photo
                        SET
                        Devotee_Photo=:photo,
                        Devotee_Key=:id,
                        photo_type=:type,
                        status=:status";
            $stmt = $this->conn->prepare($query0);
            $stmt->bindParam(":photo", $unencoded);
            $stmt->bindParam(":id", $devotee_id);
            $stmt->bindParam(":type", $type);
            $stmt->bindParam(":status", $status);
            
            if (!$stmt->execute()) {
                //return $stmt->errorInfo();
                return "Error";
            }
            else{
                return "Success";
            }

//        } 
    }

    public function uploadID($requestData, $devotee_id, $IDType ) {
        
        $rawData = $requestData['image'];
        $filteredData = explode(',', $rawData);
        $unencoded = base64_decode($filteredData[0]);
        //$unencoded = base64_decode($rawData);
        if (!empty($IDType)) {
            $type=$IDType;
        } else {
            $type = "Other";
        }
        
        $status=1;
        // Now save this info to db
        /*
         * If it is update ,insert data to Devotee_photo table only.
         * Else create an empty row in Devotee table with given id
         */
        // To devotee table 
        
//        if ($is_update) {  //
             
//            $query0 = "UPDATE Devotee_Photo
//                        SET
//                        Devotee_Photo=:photo 
//                        WHERE 
//                        Devotee_Key=:id";
                 
            $query0 = "REPLACE INTO Devotee_ID
                        SET
                        Devotee_ID_Image=:photo,
                        Devotee_Key=:id,
                        Devotee_ID_Type=:type";
            $stmt = $this->conn->prepare($query0);
            $stmt->bindParam(":photo", $unencoded);
            $stmt->bindParam(":id", $devotee_id);
            $stmt->bindParam(":type", $type);
//            $stmt->bindParam(":status", $status);
            
            if (!$stmt->execute()) {
                //return $stmt->errorInfo();
                return "Error";
            }
            else{
                return "Success";
            }

//        } 
    }
}

?>
