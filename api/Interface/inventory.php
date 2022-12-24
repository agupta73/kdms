<?php

Class inventory {

    
    private $conn;
    
    private $debug = false;
    
// constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    public function search($requestData){

        if(!empty($requestData['mode'])){
                switch ($requestData['mode']){
                    case "KEY": //Devotee key supplied
                            //return $this->getDetails(urldecode($requestData['key']), $requestData['eventId']);
                    break;
                }            
            }
            else{
                //return $this->getDetails($requestData['key']);                    
        }
        return "to be implemented";
    }
  
    public function upsert($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        $errormsg = "";
        $status = true;


        //Devotee Type
        if (empty($requestData['devotee_type'])) {
            $errormsg .= " Devotee Type is missing.";
            $status = false;
        }
        else{
            $Devotee_Type=htmlspecialchars(strip_tags($requestData['devotee_type']));
        }

        //Devotee Firsl Name
        if (empty($requestData['devotee_first_name'])) {
            $errormsg .= " First name missing.";
            $status = false;
        }
        else{
            $Devotee_First_Name=htmlspecialchars(strip_tags($requestData['devotee_first_name']));
        }

        //Devotee_Last_Name
        if (empty($requestData['devotee_last_name'])) {
            $errormsg .= " Devotee last name misssing.";
            $status = false;
        }
        else {
            $Devotee_Last_Name=htmlspecialchars(strip_tags($requestData['devotee_last_name']));
        }

        //Devotee Gender
        if (empty($requestData['devotee_gender'])){
            $Devotee_Gender="";
        }
        else{
            $Devotee_Gender=htmlspecialchars(strip_tags($requestData['devotee_gender']));
        }

        //Devotee DOB
        if (empty($requestData['devotee_dob'])){
            $Devotee_DOB="1900-01-01";
        }
        elseif ($this->validateDate(htmlspecialchars(strip_tags($requestData['devotee_dob'])))) {

            $Devotee_DOB=htmlspecialchars(strip_tags($requestData['devotee_dob']));
        }
        else {
            $Devotee_DOB = "";
            $errormsg .= " Date of Birth is invalid.";
            $status = false;
        }

        //Devotee ID Type
        if (empty($requestData['devotee_id_type'])){
            $Devotee_ID_Type="";
        }
        else{
            $Devotee_ID_Type=htmlspecialchars(strip_tags($requestData['devotee_id_type']));
        }

        //Devotee ID Number
        if (empty($requestData['devotee_id_number'])){
            $Devotee_ID_Number="";
        }
        else{
            $Devotee_ID_Number=htmlspecialchars(strip_tags($requestData['devotee_id_number']));
        }

        //Devotee Address 1
        if (empty($requestData['devotee_address_1'])){
            $Devotee_Address_1="";
        }
        else {
            $Devotee_Address_1=htmlspecialchars(strip_tags($requestData['devotee_address_1']));
        }

        //Devotee Address 2
        if (empty($requestData['devotee_address_2'])){
            $Devotee_Address_2="";
        }
        else {
            $Devotee_Address_2=htmlspecialchars(strip_tags($requestData['devotee_address_2']));
        }

        //Devotee Station
        if (empty($requestData['devotee_station'])){
            $Devotee_Station= "";
        }
        else{
            $Devotee_Station=htmlspecialchars(strip_tags($requestData['devotee_station']));
        }
        
        //Devotee State
        if (empty($requestData['devotee_state'])){
            $Devotee_State="";
        }
        else {
            $Devotee_State=htmlspecialchars(strip_tags($requestData['devotee_state']));
        }
        
        //Devotee Zip
        if (empty($requestData['devotee_zip'])){
            $Devotee_Zip="";
        }
        else {
            $Devotee_Zip=htmlspecialchars(strip_tags($requestData['devotee_zip']));
        }

        //Devotee Country
        if (empty($requestData['devotee_country'])){
            $Devotee_Country="";
        }
        else {
            $Devotee_Country=htmlspecialchars(strip_tags($requestData['devotee_country']));
        }

        /*if($this->debug){
            echo "email >> "; 
            var_dump(urldecode(strip_tags($requestData['devotee_email']))); 
            echo "result: "; 
            var_dump(filter_var(urldecode(strip_tags($requestData['devotee_email'])), FILTER_VALIDATE_EMAIL));             
        }*/
        //Devotee email address
        if (empty($requestData['devotee_email'])) {
            $Devotee_Email = "";
        } elseif (filter_var(urldecode(strip_tags($requestData['devotee_email'])), FILTER_VALIDATE_EMAIL)) {
            $Devotee_Email = urldecode(strip_tags($requestData['devotee_email']));
        } else {
            $Devotee_Email = "";
            $errormsg .= " Email Address is invalid.";
            $status = false;
        }

        //Devotee Cell Phone Number
        if (empty($requestData['devotee_cell_phone_number'])){
            $Devotee_Cell_Phone_Number="";
        }
        else {
            $Devotee_Cell_Phone_Number=htmlspecialchars(strip_tags($requestData['devotee_cell_phone_number']));
        }

        //Devotee Status
        if (empty($requestData['devotee_status'])){
            $Devotee_Status="A";
        }
        else {
            $Devotee_Status=htmlspecialchars(strip_tags($requestData['devotee_status']));
        }

        //Joined  Since
        if (empty($requestData['joined_since'])){
            $Joined_Since="";
        }
        else {
            $Joined_Since=htmlspecialchars(strip_tags($requestData['joined_since']));
        }

        //devotee Referral
        if (empty($requestData['devotee_referral'])){
            $Devotee_Referral="";
        }
        else {
            $Devotee_Referral=htmlspecialchars(strip_tags($requestData['devotee_referral']));
        }

        // Devotee Remarks
        if (empty($requestData['devotee_remarks'])){
            $Devotee_Remarks="";
        }
        else {
            $Devotee_Remarks=htmlspecialchars(strip_tags($requestData['devotee_remarks']));
        }
     
        //Comments
        if (empty($requestData['comments'])){
            $Comments="";
        }
        else {
            $Comments=htmlspecialchars(strip_tags($requestData['comments']));
        }

        //Devotee record updated by
        $Devotee_Record_Updated_By='Anil'; //to be fixed userid

        //Devotee Seva ID
        if (empty($requestData['devotee_seva_id'])){
            $Devotee_Seva_ID="UN";
        }
        else {
            $Devotee_Seva_ID=htmlspecialchars(strip_tags($requestData['devotee_seva_id']));
        }
        
        //Devotee Seva Status
        $Devotee_Seva_Status = "Assigned";

        //Devotee Accommodation ID
        if (empty($requestData['devotee_accommodation_id'])){
            $Devotee_Accommodation_ID="0";
        }
        else {
            $Devotee_Accommodation_ID=htmlspecialchars(strip_tags($requestData['devotee_accommodation_id']));
        }

        //Devotee Accommodation Status
        $Devotee_Accomodation_Status = "Allocated";
        
        //-- Not needed anymore since event functionality has been added
        //$Devotee_Accomodation_Year = date('y'); 
        //$Devotee_Seva_Year = date('y');
        // $now = date('Y-m-d H:i:s');

        // Event ID
        if (empty($requestData['eventId'])) {
            $errormsg .= " Event ID is missing.";
            $status = false;
        }
        else {
            $eventId = htmlspecialchars(strip_tags($requestData['eventId']));
        }
   
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        //Devotee Key
        $query = "";
        $unique_id = "";
        
        if (!empty($requestData['devotee_key'])) {
            // Edit scenario
            $unique_id = $requestData['devotee_key'];
        } else {
            // Add scenario - Generate unique ID
            $unique_id = $this->generateId();
        }

        //Use replace function for insert as well as update thru stored procedure
        $query = "CALL PROC_REPLACE_DEVOTEE_W_SEVA_I(";
        $query = $query . "'" .
            $unique_id . "', '" . // `p_Devotee_Key` 
            $Devotee_Type . "', '" . // `p_Devotee_Type` 
            $Devotee_First_Name . "', '" . // `p_Devotee_First_Name` VARCHAR(50),
            $Devotee_Last_Name . "', '" . // `p_Devotee_Last_Name` VARCHAR(50),
            $Devotee_Gender . "', '" . // `p_Devotee_Gender` VARCHAR(6),
            $Devotee_DOB . "', '" . // `p_Devotee_DOB` DATE,
            $Devotee_ID_Type . "', '" . // `p_Devotee_ID_Type` VARCHAR(10),
            $Devotee_ID_Number . "', '" . // `p_Devotee_ID_Number` VARCHAR(50),
            $Devotee_Address_1 . "', '" . // `p_Devotee_Address_1` VARCHAR(100),
            $Devotee_Address_2 . "', '" . // `p_Devotee_Address_2` VARCHAR(100),
            $Devotee_Station . "', '" . // `p_Devotee_Station` VARCHAR(50),
            $Devotee_State . "', '" . // `p_Devotee_State` VARCHAR(25),
            $Devotee_Zip . "', '" . // `p_Devotee_Zip` VARCHAR(12),
            $Devotee_Country . "', '" . // `p_Devotee_Country` VARCHAR(20) ,
            $Devotee_Email . "', '" . // `p_Devotee_Email` VARCHAR(40) ,
            $Devotee_Cell_Phone_Number . "', '" . // `p_Devotee_Cell_Phone_Number` VARCHAR(15),
            $Devotee_Status . "', '" . // `p_Devotee_Status` VARCHAR(20),
            $Joined_Since . "', '" . // `p_Joined_Since`  VARCHAR(4),
            $Devotee_Referral . "', '" . // `p_Devotee_Referral` VARCHAR(50),
            $Devotee_Remarks . "', '" . // `p_Devotee_Remarks` VARCHAR(250),
            $Comments . "', '" . // `p_Comments`  VARCHAR(250),
            $Devotee_Record_Updated_By . "', '" . // `p_Devotee_Record_Updated_By`  VARCHAR(10),
            $Devotee_Seva_ID . "', '" . // `p_Devotee_Seva_Id` VARCHAR(6),
            $Devotee_Seva_Status . "', '" . // `p_Devotee_Seva_Status` VARCHAR(10),
            $Devotee_Accommodation_ID . "', '" . // `p_Devotee_Accommodation_ID` VARCHAR(10),
            $Devotee_Accomodation_Status . "', '" . // `p_Devotee_Accomodation_Status` VARCHAR(10),
            $eventId . "')" ; // `p_Event_ID` VARCHAR(10)
       // old code - to be deleted after testing of new code is complete
        /*
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
                $Devotee_Referral . "', '" . //:devotee_referral,
                $Devotee_Seva_ID . "', '" . //:devotee_seva,
                "Assigned" . "', '" . //:devotee_seva_status,
                $Devotee_Record_Updated_By . "', '" . //:devotee_record_updated_by,
                $Devotee_Accommodation_ID . "', '" . //:devotee_accommodation_id,
                $Devotee_Accomodation_Status . "','" . //:devotee_accommodation_status)" ;
                $Devotee_Address_1 . "', '" . 
                $Devotee_Address_2 . "', '" . 
                $Devotee_State . "', '" . 
                $Devotee_Zip . "', '" . 
                $Devotee_Country . "', '" . 
                $Comments . "', '" .
                $Joined_Since . "' , '".
                $eventId . "')" ;
           
         if($this->debug){
             echo $query; 
         }
        */    

        // prepare query
        $stmt = $this->conn->prepare($query);

        if($this->debug){ var_dump($stmt); die;}

        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = $stmt;
            $res['info'] = $unique_id;
        } else {
            $res['status'] = false;
            $res['message'] = "[Devotees] Adding Devotee Record Failed at API!!";
            if ($this->debug) {
                $res['info'] = $query;
            } else {
                $res['info'] = $stmt;
            }
        }
        return $res;
         
    }
}
?>
