<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of clsOptions
 *
 * @author agupta
 */
class clsOptions {
    private $conn;


// constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    
    public function upsertOption($requestData) {
        $option = "";
        $res = array();
        if(!empty($requestData['requestType'])){
            $option=$requestData['requestType'];
        }
        else{
            $option = "not provided";
        }
        
        switch ($option) {
            case "upsertAcco": 
                $res=$this->upsertAccommodation($requestData);
                break;
            
            case "upsertSeva": 
                $res=$this->upsertSeva($requestData);
                break;
            
            
            case "upsertAmenity": 
                //print_r("Reaching upsert option");
                $res=$this->upsertAmenity($requestData);
                break;
            
            default :
                
                break;
        }
        
        return $res;
    }
    
    private function upsertAccommodation($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        $errormsg = "";
        $status = true;
        
        $query = "";
        $Accommodation_Key="";
        $Accommodation_Name="";
        $Accomodation_Capacity=0;
        $Reserved_Count=0;
        $Out_of_Availability_Count=0;
        $Accommodation_Record_Updated_By='Anil'; //to be fixed userid
        $now = date('Y-m-d H:i:s');

        
        if (empty($requestData['accommodation_key'])) {
            $errormsg .= " Accommodation Key is missing.";
            $status = false;
        }
        else{
            $Accommodation_Key = htmlspecialchars(strip_tags($requestData['accommodation_key']));
        }
        
        if (!empty($requestData['accommodation_name'])) {
            $Accommodation_Name = htmlspecialchars(strip_tags($requestData['accommodation_name']));
        }
        else{
            $Accommodation_Name=$Accommodation_Key;
        }
        
        if (!empty($requestData['accomodation_capacity'])) {
            $Accomodation_Capacity = htmlspecialchars(strip_tags($requestData['accomodation_capacity']));
        }
        
        if (!empty($requestData['reserved_count'])) {
            $Reserved_Count = htmlspecialchars(strip_tags($requestData['reserved_count']));
        }
        
        if (!empty($requestData['out_of_availability_count'])) {
            $Out_of_Availability_Count = htmlspecialchars(strip_tags($requestData['out_of_availability_count']));
        }
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        
        $query= "CALL PROC_UPSERT_ACCO(";
//    IN `p_Accomodation_Key` VARCHAR(5),
//    IN `p_Accomodation_Name` VARCHAR(100),
//    IN `p_Accomodation_Capacity` INT(11),
//    IN `p_Reserved_Count` INT(11),
//    IN `p_Out_of_Availability_Count` INT(11),
//    IN `p_Accomodation_Updated_By` VARCHAR(10)

         $query = $query . "'" .
                $Accommodation_Key . "', '" . 
                $Accommodation_Name . "', " . 
                $Accomodation_Capacity . ", " . 
                $Reserved_Count . ", " . 
                $Out_of_Availability_Count . ", '" . 
                $Accommodation_Record_Updated_By . "')" ; 
        
  // prepare query
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute()) {
            //var_dump($stmt);
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = $Accommodation_Key;
        }
        else{
            $res['status'] = false;
            $res['message'] = "[Accommodation] Upserting Accommodation Record Failed at API!!";
            $res['info'] = $stmt;
        }
        return $res;
  
    }
    
    private function upsertSeva($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        $errormsg = "";
        $status = true;
        
        $query = "";
        $Seva_Id="";
        $Seva_Description="";
        $Seva_Record_Updated_By='Anil'; //to be fixed userid
        $now = date('Y-m-d H:i:s');

        
        if (empty($requestData['seva_id'])) {
            $errormsg .= " Seva ID is missing.";
            $status = false;
        }
        else{
            $Seva_Id = htmlspecialchars(strip_tags($requestData['seva_id']));
        }
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }
        
        if (!empty($requestData['seva_description'])) {
            $Seva_Description = htmlspecialchars(strip_tags($requestData['seva_description']));
        }
        else{
            $Seva_Description=$Seva_Id;
        }
                
        $query= "CALL PROC_UPSERT_SEVA_W_AVAIL_UPDATE(";
//    IN `p_Accomodation_Key` VARCHAR(5),
//    IN `p_Accomodation_Name` VARCHAR(100),
//    IN `p_Accomodation_Capacity` INT(11),
//    IN `p_Reserved_Count` INT(11),
//    IN `p_Out_of_Availability_Count` INT(11),
//    IN `p_Accomodation_Updated_By` VARCHAR(10)

         $query = $query . "'" .
                $Seva_Id . "', '" . 
                $Seva_Description . "')" ; 
        
  // prepare query
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute()) {
            //var_dump($stmt);
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = $Seva_Id;
        }
        else{
            $res['status'] = false;
            $res['message'] = "[Seva] Upserting Seva Record Failed at API!!";
            $res['info'] = $stmt;
        }
        return $res;
  
    }
    
    private function upsertAmenity($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        $errormsg = "";
        $status = true;
        
        $query = "";
        $Amenity_Key="";
        $Amenity_Name="";
        $Amenity_Status="Available";
        $Amenity_Quantity=0;
        $Reserved_Count=0;
        $Out_of_Availability_Count=0;
        $Amenity_Record_Updated_By='Anil'; //to be fixed userid
        $now = date('Y-m-d H:i:s');

        
        if (empty($requestData['amenity_key'])) {
            $errormsg .= " Amenity Key is missing.";
            $status = false;
        }
        else{
            $Amenity_Key = htmlspecialchars(strip_tags($requestData['amenity_key']));
        }
        
        if (!empty($requestData['amenity_name'])) {
            $Amenity_Name = htmlspecialchars(strip_tags($requestData['amenity_name']));
        }
        else{
            $Amenity_Name=$Amenity_Key;
        }
        
        if (!empty($requestData['amenity_status'])) {
            $Amenity_Status = htmlspecialchars(strip_tags($requestData['amenity_status']));
        }
        
        if (!empty($requestData['amenity_quantity'])) {
            $Amenity_Quantity = htmlspecialchars(strip_tags($requestData['amenity_quantity']));
        }
        
        if (!empty($requestData['reserved_count'])) {
            $Reserved_Count = htmlspecialchars(strip_tags($requestData['reserved_count']));
        }
        
        if (!empty($requestData['out_of_availability_count'])) {
            $Out_of_Availability_Count = htmlspecialchars(strip_tags($requestData['out_of_availability_count']));
        }
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        
        $query= "CALL PROC_UPSERT_AMENITY(";
//    IN `p_Accomodation_Key` VARCHAR(5),
//    IN `p_Accomodation_Name` VARCHAR(100),
//    IN `p_Accomodation_Capacity` INT(11),
//    IN `p_Reserved_Count` INT(11),
//    IN `p_Out_of_Availability_Count` INT(11),
//    IN `p_Accomodation_Updated_By` VARCHAR(10)

         $query = $query . "'" .
                $Amenity_Key . "', '" . 
                $Amenity_Name . "', '" . 
                $Amenity_Status . "', " . 
                $Amenity_Quantity . ", " . 
                $Reserved_Count . ", " . 
                $Out_of_Availability_Count . ", '" . 
                $Amenity_Record_Updated_By . "')" ; 
        //var_dump($query);
  // prepare query
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute()) {
            //var_dump($stmt);
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = $Amenity_Key;
        }
        else{
            $res['status'] = false;
            $res['message'] = "[Amenity] Upserting Amenity Record Failed at API!!";
            $res['info'] = $stmt;
        }
        return $res;
  
    }
    
    public function loadOption($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        $optionType = "";
        
        if(!empty($requestData['option_type'])){
            $optionType=$requestData['option_type'];
        }
        else{            
            $res['message'] = "Option type not provided";
            return $res;
        }
        
        switch ($optionType) {
            case "Accommodation":
                  return $this->getAccommodations();
                break;           

            case "Seva":
                  return $this->getSevas();
                break; 
            
            Case "AccommodationDetail":                
                if(!empty($requestData['option_type'])){
                    return $this->getAccommodationDetail($requestData['key']);
                }
                else {
                    $res['message'] = "Option key not provided";
                    return $res;                    
                }
                break;
              
            Case "SevaDetail":                
                if(!empty($requestData['option_type'])){
                    return $this->getSevaDetail($requestData['key']);
                }
                else {
                    $res['message'] = "Option key not provided";
                    return $res;                    
                }
                break;
                
            case "Amenity":
                  return $this->getAmenities();
                break;
            
            Case "AmenityDetail":                
                if(!empty($requestData['option_type'])){
                    return $this->getAmenityDetail($requestData['key']);
                }
                else {
                    $res['message'] = "Option key not provided";
                    return $res;                    
                }
                break;
                
            Case "RefreshAcco":                
                return $this->refreshAccommodations();
                break;
            
            Case "RefreshAmenity":                
                return $this->refreshAmenities();
                break;
            
            Case "RefreshSeva":                
                return $this->refreshSeva();
                break;
            
            default:
                print_r("Not provided option");
                break;
        }
        
    }
    
    private function getAccommodations(){
//        $res = array();
//        $res['status'] = false;
//        $res['message'] = '';
//        $errormsg = "";
//        $status = true;
        
        
        $query = "SELECT am.accomodation_key, am.`Accomodation_Name`, aa.Available_Count, am.Accomodation_Capacity,
            aa.Allocated_Count, aa.Reserved_Count, aa.Out_Of_Availability_Count
            FROM `Accommodation_Master` am 
            LEFT OUTER JOIN Accommodation_Availability aa 
            ON am.accomodation_key = aa.accomodation_key";
        
        
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $AccomodationDetail = array();
        $i = 0;
        while($row = $results->fetchObject()){
            //var_dump($row);
            $AccomodationDetail[]=$row;
            $i = $i+1;
        }
        //var_dump($AccomodationDetail);
        if($i==0){
            $AccomodationDetail['status'] = false;
            $AccomodationDetail['message'] = "Accomodation details not found!";
            $AccomodationDetail['info'] = $results;
        }
        
        return $AccomodationDetail;
    }
  
    private function getSevas(){
//        $res = array();
//        $res['status'] = false;
//        $res['message'] = '';
//        $errormsg = "";
//        $status = true;
        
        
        $query = "SELECT sm.Seva_Id, sm.Seva_Description, sa.assigned_count " . 
            " FROM `Seva_Master` sm " .
            " left outer join Seva_Availability sa on sm.seva_id = sa.Seva_Id ";
        
        
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $Sevas = array();
        $i = 0;
        while($row = $results->fetchObject()){
            //var_dump($row);
            $Sevas[]=$row;
            $i = $i+1;
        }
        //var_dump($AccomodationDetail);
        if($i==0){
            $Sevas['status'] = false;
            $Sevas['message'] = "Seva records not found!";
            $Sevas['info'] = $results;
        }
        
        return $Sevas;
    }
  
    
    private function getAmenities(){
//        $res = array();
//        $res['status'] = false;
//        $res['message'] = '';
//        $errormsg = "";
//        $status = true;
        
        
        $query = "SELECT am.amenity_key, am.`Amenity_Name`, am.Amenity_Status, aa.Available_Count, am.Amenity_Quantity,
            aa.Allocated_Count, aa.Reserved_Count, aa.Out_Of_Availability_Count
            FROM `Amenity_Master` am 
            LEFT OUTER JOIN Amenities_Availability aa 
            ON am.amenity_key = aa.amenity_key";
        
        
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $AmenityDetail = array();
        $i = 0;
        while($row = $results->fetchObject()){
            //var_dump($row);
            $AmenityDetail[]=$row;
            $i = $i+1;
        }
        //var_dump($AccomodationDetail);
        if($i==0){
            $AmenityDetail['status'] = false;
            $AmenityDetail['message'] = "Accomodation details not found!";
            $AmenityDetail['info'] = $results;
        }
        
        return $AmenityDetail;
    }
  
     private function getAccommodationDetail($accommodationKey){
//        $res = array();
//        $res['status'] = false;
//        $res['message'] = '';
//        $errormsg = "";
//        $status = true;
        
        
        $query = "SELECT am.Accomodation_Key, am.`Accomodation_Name`, am.Accomodation_Capacity, aa.Available_Count, 
            aa.Allocated_Count, aa.Reserved_Count, aa.Out_Of_Availability_Count, aa.Available_Count
            FROM `Accommodation_Master` am 
            LEFT OUTER JOIN Accommodation_Availability aa 
            ON am.accomodation_key = aa.accomodation_key 
            WHERE am.accomodation_key = '" . $accommodationKey . "'";
        
        
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $AccomodationDetail = array();
        
        if($row = $results->fetchObject()){
            //var_dump($row);
            $AccomodationDetail=$row;           
        }
        //var_dump($AccomodationDetail);
        else{
            $AccomodationDetail['status'] = false;
            $AccomodationDetail['message'] = "Accomodation details not found!";
            $AccomodationDetail['info'] = $results;
        }
        
        return $AccomodationDetail;
    }
    
    private function getSevaDetail($sevaKey){
//        $res = array();
//        $res['status'] = false;
//        $res['message'] = '';
//        $errormsg = "";
//        $status = true;
        
        
        $query = "SELECT sm.Seva_ID, sm.`Seva_Description`
            FROM `Seva_Master` sm             
            WHERE sm.seva_id = '" . $sevaKey . "'";
        
        
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $SevaDetail = array();
        
        if($row = $results->fetchObject()){
            //var_dump($row);
            $SevaDetail=$row;           
        }
        //var_dump($AccomodationDetail);
        else{
            $SevaDetail['status'] = false;
            $SevaDetail['message'] = "Accomodation details not found!";
            $SevaDetail['info'] = $results;
        }
        
        return $SevaDetail;
    }
    
    private function getAmenityDetail($amenityKey){
//        $res = array();
//        $res['status'] = false;
//        $res['message'] = '';
//        $errormsg = "";
//        $status = true;
        
        
        $query = "SELECT am.Amenity_Key, am.`Amenity_Name`,  am.Amenity_Status, am.Amenity_Quantity, aa.Available_Count, 
            aa.Allocated_Count, aa.Reserved_Count, aa.Out_Of_Availability_Count, aa.Available_Count
            FROM `Amenity_Master` am 
            LEFT OUTER JOIN Amenities_Availability aa 
            ON am.amenity_key = aa.amenity_key 
            WHERE am.amenity_key = '" . $amenityKey . "'";
        
        //var_dump($query);
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $AmenityDetail = array();
        
        if($row = $results->fetchObject()){
            //var_dump($row);
            $AmenityDetail=$row;           
        }
        //var_dump($AccomodationDetail);
        else{
            $AmenityDetail['status'] = false;
            $AmenityDetail['message'] = "Amenity details not found!";
            $AmenityDetail['info'] = $results;
        }
        
        return $AmenityDetail;
    }
        
    private function refreshAccommodations(){
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        
        $query = "CALL PROC_REFRESH_ACCO_COUNT()";
        $stmt = $this->conn->prepare($query);
        
         if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = "";
        }
        else{
            $res['status'] = false;
            $res['message'] = "[Accommodation] Refreshing accomodation count failed at API!!";
            $res['info'] = $stmt;
        }
        return $res;
    }
    
    private function refreshAmenities(){
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        
        $query = "CALL PROC_REFRESH_AMENITY_COUNT()";
        $stmt = $this->conn->prepare($query);
        
         if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = "";
        }
        else{
            $res['status'] = false;
            $res['message'] = "[Amenity] Refreshing amenity count failed at API!!";
            $res['info'] = $stmt;
        }
        return $res;
    }
    
    private function refreshSeva(){
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        
        $query = "CALL PROC_REFRESH_SEVA_COUNT()";
        $stmt = $this->conn->prepare($query);
        
         if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = "";
        }
        else{
            $res['status'] = false;
            $res['message'] = "[Seva] Refreshing seva count failed at API!!";
            $res['info'] = $stmt;
        }
        return $res;
    }
  }
    

