<?php

Class Devotee {

    private $conn;
    private $table_name = "Devotee";

// constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    public function upsert($requestData) {
      //print_r($requestData);
        if (!empty($requestData['devotee_key'])) {
// Edit
            return $this->edit($requestData);
        } else {
// query to insert record
            return $this->add($requestData);
        }
    }

    public function search($requestData){
        
        if (!empty($requestData['devotee_key'])) {
            return $this->getDetails($requestData['devotee_key']);
        } else {
            return $this->searchDevotee($requestData);
        }
    }

    private function add($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        $errormsg = "";
        $status = true;


        // Generate unique ID
        $unique_id = $this->generateId();

        //$Devotee_Record_Update_Date_Time=htmlspecialchars(strip_tags($requestData['devotee_record_update_date_time']));
        $Devotee_Record_Updated_By=1; //to be fixed userid
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

//
//        $query = "INSERT INTO
//                " . $this->table_name . "
//            SET
//                Devotee_Key=:id,
//                Devotee_Type=:devotee_type,
//                Devotee_First_Name=:devotee_first_name,
//                Devotee_Last_Name=:devotee_last_name,
//                Devotee_Gender=:devotee_gender,
//                Devotee_ID_Type=:devotee_id_type,
//                Devotee_ID_Number=:devotee_id_number,
//                Devotee_Station=:devotee_station,
//                Devotee_Cell_Phone_Number=:devotee_cell_phone_number,
//                Devotee_Status=:devotee_status,
//                Devotee_Remarks=:devotee_remarks,
//                Devotee_Record_Update_Date_Time=:devotee_record_update_date_time,
//                Devotee_Record_Updated_By=:devotee_record_updated_by" ;

        $query = "CALL PROC_INSERT_DEVOTEE(
                :id,
                :devotee_type,
                :devotee_first_name,
                :devotee_last_name,
                :devotee_gender,
                :devotee_id_type,
                :devotee_id_number,
                :devotee_station,
                :devotee_cell_phone_number,
                :devotee_status,
                :devotee_remarks,
                :devotee_record_update_date_time,
                :devotee_record_updated_by,
                :devotee_accommodation_id,
                :devotee_accommodation_year,
                :devotee_accommodation_status)" ;

// prepare query
        $stmt = $this->conn->prepare($query);


// sanitize
//        $firstName = htmlspecialchars(strip_tags($requestData['first_name']));
//        $lastName = htmlspecialchars(strip_tags($requestData['last_name']));
//        $lmbDate = htmlspecialchars(strip_tags($requestData['last_modified_by']));

        $stmt->bindParam(":id", $unique_id);
        $stmt->bindParam(":devotee_type", $Devotee_Type);
        $stmt->bindParam(":devotee_first_name", $Devotee_First_Name);
        $stmt->bindParam(":devotee_last_name", $Devotee_Last_Name);
        $stmt->bindParam(":devotee_gender", $Devotee_Gender);
        $stmt->bindParam(":devotee_id_type", $Devotee_ID_Type);
        $stmt->bindParam(":devotee_id_number", $Devotee_ID_Number);
        $stmt->bindParam(":devotee_station", $Devotee_Station);
        $stmt->bindParam(":devotee_cell_phone_number", $Devotee_Cell_Phone_Number);
        $stmt->bindParam(":devotee_status", $Devotee_Status);
        $stmt->bindParam(":devotee_remarks", $Devotee_Remarks);
        $stmt->bindParam(":devotee_record_update_date_time", $now);
        $stmt->bindParam(":devotee_record_updated_by", $Devotee_Record_Updated_By);
        $stmt->bindParam(":devotee_accommodation_id", $Devotee_Accommodation_ID);
        $stmt->bindParam(":devotee_accommodation_year", $Devotee_Accomodation_Year);
        $stmt->bindParam(":devotee_accommodation_status", $Devotee_Accomodation_Status);

        //var_dump($query); die;
        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = "";
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
    

    private function updateDevoteeAccommodation($Devotee_Key, $Devotee_Accommodation_ID) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;
        $now = date('Y-m-d H:i:s');
        
        $Accomodation_Year = date('y');
        $Arrival_Date_Time = $now;
        $Accomodation_Status = "Allocated";
        $Devotee_Accomodation_Update_Date_Time = $now;
        $Devotee_Accomodation_Updated_By = 1;
        
        if (empty($Devotee_Key)) {
            $errormsg .= " Devotee Key is missing.";
            $status = false;
        }
        
        if (empty($Devotee_Accommodation_ID)) {
            $errormsg .= " Accommodation ID is missing.";
            $status = false;
        }
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }
        
        if(!$this->cleanseAccomodationRecord($Devotee_Key, $Devotee_Accommodation_ID)['status']){
            return false;
            die;
        }
        
        $query = "INSERT INTO Devotee_Accomodation             
                  SET
                    Accomodation_Key=:devotee_accomocation_id,
                    Devotee_Key=:devotee_key,
                    Accomodation_Year=:accomodation_year,
                    Arrival_Date_Time=:arrival_date_time,
                    Accomodation_Status=:accomodation_status,
                    Devotee_Accomodation_Update_Date_Time=:devotee_accomodation_update_date_time,
                    Devotee_Accomodation_Updated_By=:devotee_accomodation_updated_by";

// prepare query
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":devotee_accomocation_id", $Devotee_Accommodation_ID);
        $stmt->bindParam(":devotee_key", $Devotee_Key);
        $stmt->bindParam(":accomodation_year", $Accomodation_Year);
        $stmt->bindParam(":arrival_date_time", $Arrival_Date_Time);
        $stmt->bindParam(":accomodation_status", $Accomodation_Status);
        $stmt->bindParam(":devotee_accomodation_update_date_time", $Devotee_Accomodation_Update_Date_Time);
        $stmt->bindParam(":devotee_accomodation_updated_by", $Devotee_Accomodation_Updated_By);

//        var_dump($Devotee_Accommodation_ID);
//        var_dump($Devotee_Key);
//        var_dump($Accomodation_Year);
//        var_dump($Arrival_Date_Time);
//        var_dump($Accomodation_Status);
//        var_dump($Devotee_Accomodation_Update_Date_Time);
//        var_dump($Devotee_Accomodation_Updated_By);

        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = $stmt;         
        }
        else {
            $res['status'] = false;
            $res['message'] = "Update Devotee Accomodation Failed!";
            $res['info'] = $stmt;
        }
        
        return $res;
    }
    
    private function edit($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';

        $now = date('Y-m-d H:i:s');
        $id = $requestData['devotee_key'];
        //Check if id exists
        $sql = "SELECT * FROM " . $this->table_name . " where devotee_key ='" . $id . "'";
        $result = [];
        foreach ($this->conn->query($sql) as $row) {
            if (!empty($row)) {
                array_push($result, $row);
            }
        }
        if (empty($result)) {
            $res['message'] = 'Invalid Devotee Key passed!!';
            return $res;
        }
        // Update now
        $query = "UPDATE " . $this->table_name . " SET ";
        $qUp = [];
        
//  ["devotee_type"]
  if (!empty($requestData['devotee_type'])) {
            $devotee_type = htmlspecialchars(strip_tags($requestData['devotee_type']));
            array_push($qUp, "devotee_type ='" . $devotee_type . "'");
        }
//  ["devotee_first_name"]
    if (!empty($requestData['devotee_first_name'])) {
            $devotee_first_name = htmlspecialchars(strip_tags($requestData['devotee_first_name']));
            array_push($qUp, "devotee_first_name ='" . $devotee_first_name . "'");
        }
//  ["devotee_last_name"]
    if (!empty($requestData['devotee_last_name'])) {
            $devotee_last_name = htmlspecialchars(strip_tags($requestData['devotee_last_name']));
            array_push($qUp, "devotee_last_name ='" . $devotee_last_name . "'");
        }
//  ["devotee_gender"]
    if (!empty($requestData['devotee_gender'])) {
            $devotee_gender = htmlspecialchars(strip_tags($requestData['devotee_gender']));
            array_push($qUp, "devotee_gender ='" . $devotee_gender . "'");
        }
//  ["devotee_id_type"]
    if (!empty($requestData['devotee_id_type'])) {
            $devotee_id_type = htmlspecialchars(strip_tags($requestData['devotee_id_type']));
            array_push($qUp, "devotee_id_type ='" . $devotee_id_type . "'");
        }
//  ["devotee_id_number"]
    if (!empty($requestData['devotee_id_number'])) {
            $devotee_id_number = htmlspecialchars(strip_tags($requestData['devotee_id_number']));
            array_push($qUp, "devotee_id_number ='" . $devotee_id_number . "'");
        }
//  ["devotee_station"]
    if (!empty($requestData['devotee_station'])) {
            $devotee_station = htmlspecialchars(strip_tags($requestData['devotee_station']));
            array_push($qUp, "devotee_station ='" . $devotee_station . "'");
        }
//  ["devotee_cell_phone_number"]
    if (!empty($requestData['devotee_cell_phone_number'])) {
            $devotee_cell_phone_number = htmlspecialchars(strip_tags($requestData['devotee_cell_phone_number']));
            array_push($qUp, "devotee_cell_phone_number ='" . $devotee_cell_phone_number . "'");
        }
//  ["devotee_status"]
    if (!empty($requestData['devotee_status'])) {
            $devotee_status = htmlspecialchars(strip_tags($requestData['devotee_status']));
            array_push($qUp, "devotee_status ='" . $devotee_status . "'");
        }
//  ["devotee_remarks"]
    if (!empty($requestData['devotee_remarks'])) {
            $devotee_remarks = htmlspecialchars(strip_tags($requestData['devotee_remarks']));
            array_push($qUp, "devotee_remarks ='" . $devotee_remarks . "'");
        }
//  ["devotee_accommodation_id"]
//        if (!empty($requestData['devotee_accommodation_id'])) {
//            $devotee_accommodation_id = htmlspecialchars(strip_tags($requestData['devotee_accommodation_id']));
//            array_push($qUp, "devotee_accommodation_id ='" . $devotee_accommodation_id . "'");
//        }

//  [devotee_record_update_date_time]
            array_push($qUp, "devotee_record_update_date_time ='" . $now . "'");
        

//  [devotee_record_updated_by]
            array_push($qUp, "devotee_record_updated_by ='" . 1 . "'");
        

//        if (!empty($requestData['first_name'])) {
//            $first_name = htmlspecialchars(strip_tags($requestData['first_name']));
//            array_push($qUp, "first_name ='" . $first_name . "'");
//        }
//        if (!empty($requestData['last_name'])) {
//            $last_name = htmlspecialchars(strip_tags($requestData['last_name']));
//            array_push($qUp, "last_name ='" . $last_name . "'");
//        }
//        if (!empty($qUp)) {
//            $now = date('Y-m-d H:i:s');
//            array_push($qUp, "modified ='" . $now . "'");
//        }
//        
        $qUp = implode(',', $qUp);
        
        $query .= $qUp;
        $query .= " WHERE devotee_key ='" . $id . "'";
        
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = "";
        }
        else{
            $res['status'] = false;
            $res['message'] = "Update devotee record failed!";
            $res['info'] = $stmt;
        }
        return $res;
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
    
    private function cleanseAccomodationRecord($Devotee_Key, $Devotee_Accommodation_ID){
        $res = array();
        $res['status'] = false;
        $res['message'] = 'Error in cleasning Accomodation Record!';
        $errormsg = "";
        $status = false;
//        $res['message'] = $Devotee_Key + $Devotee_Accommodation_ID ;
                
//      $result = ['1'];"
//        // $id="KDHM15562AF1ACE";
//        while (!empty($result)) {
//            //$id = 'KDHM' . rand(0, 9999) . substr(md5(rand()), 0, 7);
//            $id = 'P' . date('y') . date('m') . date('d') . rand(0, 9999) . substr(md5(rand()), 0, 7);
//            $sql = "SELECT * FROM " . $this->table_name . " where devotee_key = '" . $id . "'";
//            $result = [];
//            foreach ($this->conn->query($sql) as $row) {
//                //var_dump($row);
//                if (!empty($row)) {
//                   array_push($result, $row);
//              }
//            }
//        }
        $status = true;
        if ($status) {
            $res['status'] = true;
            $res['message'] = ""; 
            $res['info'] = "";
        }
        return $res;
    }
    
    private function updateAccomodationCount($Devotee_Accommodation_ID){
        $res = array();
        $res['status'] = false;
        $res['message'] = 'Error in cleasning Accomodation Record!';
        $errormsg = "";
        $status = false;
        $res['message'] = $Devotee_Accommodation_ID;
//        $result = ['1'];
//        // $id="KDHM15562AF1ACE";
//        while (!empty($result)) {
//            //$id = 'KDHM' . rand(0, 9999) . substr(md5(rand()), 0, 7);
//            $id = 'P' . date('y') . date('m') . date('d') . rand(0, 9999) . substr(md5(rand()), 0, 7);
//            $sql = "SELECT * FROM " . $this->table_name . " where devotee_key = '" . $id . "'";
//            $result = [];
//            foreach ($this->conn->query($sql) as $row) {
//                //var_dump($row);
//                if (!empty($row)) {
//                   array_push($result, $row);
//              }
//            }
//        }
        $status = true;
        if ($status) {
            $res['status'] = true;
            $res['message'] = "";  
            $res['info'] = "";
        }
        return $res;
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
            
            $DevoteeDetails=$row;
            //var_dump($DevoteeDetails);
        }
        else{
            $DevoteeDetails['status'] = false;
            $DevoteeDetails['message'] = "Devotee details not found!";
            $DevoteeDetails['info'] = $results;
        }
        
        return $DevoteeDetails;
    }
    
    private function searchDevotee($requestData){
        
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
            $res['info'] = "";
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
    
        }

?>
