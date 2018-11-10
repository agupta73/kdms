<?php

Class Devotee {

    private $conn;
    private $table_name = "Devotee";

// constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    public function search($requestData){
        if(!empty($requestData['mode'])){
                switch ($requestData['mode']){
                    case "KEY":                                
                            return $this->getDetails($requestData['key']);                    
                    break;

                    case "SET":
                            return $this->searchDevotee($requestData['key']);
                    break;

                    default :
                        return $this->getDetails($requestData['key']);                    
                    break;
                }            
            }
            else{
                return $this->getDetails($requestData['key']);                    
        }
    }
    
    private function getDetails($devotee_key){
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        if (empty($devotee_key)) {
            $errormsg .= " Devotee Key is missing.";
            $status = false;
        }
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
        
        $query = "select d.*, did.Devotee_ID_Image, did.Devotee_ID_XML, 
                    did.Devotee_ID_Type as DID_Devotee_ID_Type, dp.Photo_type, dp.Devotee_Photo, da.Accomodation_Key 
                 from 
                    devotee d 
                    left outer join devotee_id did on d.Devotee_Key=did.Devotee_Key
                    left outer join Devotee_Photo dp on d.Devotee_Key=dp.Devotee_Key
                    left outer join Devotee_accomodation da on d.Devotee_key=da.Devotee_Key AND da.accomodation_year = YEAR(NOW()) AND Accomodation_Status = 'Allocated'  
                 where 
                    d.Devotee_Key = '" . $devotee_key . "' ORDER BY da.Devotee_Accomodation_update_Date_Time Desc LIMIT 1";
        
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $DevoteeDetails = array();
        
        if(!empty($row = $results->fetchObject())){
            $row->{'Devotee_Photo'} = base64_encode($row->{'Devotee_Photo'});
            $DevoteeDetails=$row;
        }
        else{
            $DevoteeDetails['status'] = false;
            $DevoteeDetails['message'] = "Devotee details not found!";
            $DevoteeDetails['info'] = $results;
        }
        
        return $DevoteeDetails;
    }
    
    private function searchDevotee($requestData){
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        if (empty($requestData)) {
            $errormsg .= "Set key is missing.";
            $status = false;
        }
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
       
        $query = "select 
                    d.devotee_key, CONCAT(d.devotee_first_name, ', ', d.devotee_last_name) as Devotee_Name, 
                    d.devotee_station, d.devotee_cell_phone_number,
                    did.Devotee_ID_Image, 
                    dp.Devotee_Photo 
                 from 
                    devotee d 
                    left outer join devotee_id did on d.Devotee_Key=did.Devotee_Key
                    left outer join Devotee_Photo dp on d.Devotee_Key=dp.Devotee_Key";
                
        switch ($requestData){
            case "PWD": //Photo without Devotee Details                   
                $query = $query . 
                            " WHERE
                                (d.Devotee_First_Name is null OR d.Devotee_Last_Name is null) 
                              AND 
                                (did.Devotee_ID_Image is not null OR dp.Devotee_Photo is not null)";
                    
                break;

            case "DWP": //Devotee records without Photo or ID
                $query = $query . 
                            " WHERE
                                (d.Devotee_First_Name is not null OR d.Devotee_Last_Name is NOT null) 
                              AND 
                                (did.Devotee_ID_Image is null OR dp.Devotee_Photo is  null)";
                break;

            default : //Search based on user supplied search criteria

                break;
        }    
        
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $devoteeSearchResult = array();
        $i = 0;
        while($row = $results->fetchObject()){
            $row->{'Devotee_Photo'} = base64_encode($row->{'Devotee_Photo'});
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
    
   

    public function upsertDevotee($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        $errormsg = "";
        $status = true;

        $Devotee_Record_Updated_By='Anil'; //to be fixed userid
        $now = date('Y-m-d H:i:s');

        if (empty($requestData['devotee_type'])) {
            $errormsg .= " Devotee Type is missing.";
            $status = false;
        }
        else{
            $Devotee_Type=htmlspecialchars(strip_tags($requestData['devotee_type']));
        }

        if (empty($requestData['devotee_first_name'])) {
            $errormsg .= " First name missing.";
            $status = false;
        }
        else{
            $Devotee_First_Name=htmlspecialchars(strip_tags($requestData['devotee_first_name']));
        }

        if (empty($requestData['devotee_last_name'])) {
            $errormsg .= " Devotee last name misssing.";
            $status = false;
        }
        else {
            $Devotee_Last_Name=htmlspecialchars(strip_tags($requestData['devotee_last_name']));
        }



        if (empty($requestData['devotee_gender'])){
            $Devotee_Gender="";
        }
        else{
            $Devotee_Gender=htmlspecialchars(strip_tags($requestData['devotee_gender']));
        }

        if (empty($requestData['devotee_id_type'])){
            $Devotee_ID_Type="";
        }
        else{
            $Devotee_ID_Type=htmlspecialchars(strip_tags($requestData['devotee_id_type']));
        }

        if (empty($requestData['devotee_id_number'])){
            $Devotee_ID_Number="";
        }
        else{
            $Devotee_ID_Number=htmlspecialchars(strip_tags($requestData['devotee_id_number']));
        }

        if (empty($requestData['devotee_station'])){
            $Devotee_Station= "";
        }
        else{
            $Devotee_Station=htmlspecialchars(strip_tags($requestData['devotee_station']));
        }

        if (empty($requestData['devotee_cell_phone_number'])){
            $Devotee_Cell_Phone_Number="";
        }
        else {
            $Devotee_Cell_Phone_Number=htmlspecialchars(strip_tags($requestData['devotee_cell_phone_number']));
        }

        if (empty($requestData['devotee_status'])){
            $Devotee_Status="A";
        }
        else {
            $Devotee_Status=htmlspecialchars(strip_tags($requestData['devotee_status']));
        }

        if (empty($requestData['devotee_remarks'])){
            $Devotee_Remarks="";
        }
        else {
            $Devotee_Remarks=htmlspecialchars(strip_tags($requestData['devotee_remarks']));
        }

        if (empty($requestData['devotee_accommodation_id'])){
            $Devotee_Accommodation_ID="0";
        }
        else {
            $Devotee_Accommodation_ID=htmlspecialchars(strip_tags($requestData['devotee_accommodation_id']));
        }

        $Devotee_Accomodation_Year = date('y');
        $Devotee_Accomodation_Status = "Allocated";
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "";
        $unique_id = "";
        
        if (!empty($requestData['devotee_key'])) {
            // Edit
            $unique_id = $requestData['devotee_key'];
            $query = "CALL PROC_UPDATE_DEVOTEE(";
        } else {
            // Add
            // Generate unique ID
            $unique_id = $this->generateId();
            $query = "CALL PROC_INSERT_DEVOTEE(";
        }
        
//        $query = $query . "
//                :id,
//                :devotee_type,
//                :devotee_first_name,
//                :devotee_last_name,
//                :devotee_gender,
//                :devotee_id_type,
//                :devotee_id_number,
//                :devotee_station,
//                :devotee_cell_phone_number,
//                :devotee_status,
//                :devotee_remarks,
//                :devotee_record_updated_by,
//                :devotee_accommodation_id,
//                :devotee_accommodation_status)" ;

         $query = $query . "'" .
                $unique_id . "', '" . //:id,
                $Devotee_Type . "', '" . //:devotee_type,
                $Devotee_First_Name . "', '" . //:devotee_first_name,
                $Devotee_Last_Name . "', '" . //:devotee_last_name,
                $Devotee_Gender . "', '" . //:devotee_gender,
                $Devotee_ID_Type . "', '" . //:devotee_id_type,
                $Devotee_ID_Number . "', '" . //:devotee_id_number,
                $Devotee_Station . "', '" . //:devotee_station,
                $Devotee_Cell_Phone_Number . "', '" . //:devotee_cell_phone_number,
                $Devotee_Status . "', '" . //:devotee_status,
                $Devotee_Remarks . "', '" . //:devotee_remarks,
                $Devotee_Record_Updated_By . "', '" . //:devotee_record_updated_by,
                $Devotee_Accommodation_ID . "', '" . //:devotee_accommodation_id,
                $Devotee_Accomodation_Status . "')" ; //:devotee_accommodation_status)" ;
// prepare query
        $stmt = $this->conn->prepare($query);

//        $stmt->bindParam(":id", $unique_id);
//        $stmt->bindParam(":devotee_type", $Devotee_Type);
//        $stmt->bindParam(":devotee_first_name", $Devotee_First_Name);
//        $stmt->bindParam(":devotee_last_name", $Devotee_Last_Name);
//        $stmt->bindParam(":devotee_gender", $Devotee_Gender);
//        $stmt->bindParam(":devotee_id_type", $Devotee_ID_Type);
//        $stmt->bindParam(":devotee_id_number", $Devotee_ID_Number);
//        $stmt->bindParam(":devotee_station", $Devotee_Station);
//        $stmt->bindParam(":devotee_cell_phone_number", $Devotee_Cell_Phone_Number);
//        $stmt->bindParam(":devotee_status", $Devotee_Status);
//        $stmt->bindParam(":devotee_remarks", $Devotee_Remarks);

//        $stmt->bindParam(":devotee_record_updated_by", $Devotee_Record_Updated_By);
//        $stmt->bindParam(":devotee_accommodation_id", $Devotee_Accommodation_ID);

//        $stmt->bindParam(":devotee_accommodation_status", $Devotee_Accomodation_Status);

//      
        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = $unique_id;
        }
        else{
            $res['status'] = false;
            $res['message'] = "[Devotees] Adding Devotee Record Failed at API!!";
            $res['info'] = $stmt;
        }
        return $res;
  
        
        
        //$UpdateDevoteeAccommodationRes = array();   
        
        //$UpdateDevoteeAccommodationRes = $this->updateDevoteeAccommodation($unique_id,$Devotee_Accommodation_ID);
        
        //$res['status'] = $UpdateDevoteeAccommodationRes['status'];
        //$res['message'] = $UpdateDevoteeAccommodationRes['message'];
        //$res['info'] = $UpdateDevoteeAccommodationRes['info'];
        //return $res;
    }

    public function generateId() {
        $result = ['1'];
        // $id="KDHM15562AF1ACE";
        while (!empty($result)) {
            //$id = 'KDHM' . rand(0, 9999) . substr(md5(rand()), 0, 7);
            //$id = 'P' . date('y') . date('m') . date('d') . rand(0, 9999) . substr(md5(rand()), 0, 7);
            $id = 'P' . date('y') . date('m') . date('d') . rand(0, 999) ;
            $sql = "SELECT * FROM " . $this->table_name . " where devotee_key = '" . $id . "'";
            $result = [];
            foreach ($this->conn->query($sql) as $row) {
                //var_dump($row);
                if (!empty($row)) {
                   array_push($result, $row);
              }
            }
        }
        return strtoupper($id);
    }

    }

?>
