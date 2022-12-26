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

    public function fill_category() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT * FROM category_ims WHERE category_status = 'Enable' ORDER BY category_name ASC" ;

        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }
    }
    public function fill_location_rack() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT * FROM location_rack_ims WHERE location_rack_status = 'Enable' ORDER BY location_rack_name ASC" ;

        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }
    }
    public function fill_supplier() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT * FROM supplier_ims WHERE supplier_status = 'Enable' ORDER BY supplier_name ASC" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }
    }
    public function fill_company() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT * FROM item_manufacuter_company_ims WHERE company_status = 'Enable' ORDER BY company_name ASC" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }
    }
    public function fill_tax() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT * FROM tax_ims WHERE tax_status = 'Enable' ORDER BY tax_name ASC" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }
    }
    public function fill_item() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT * FROM item_ims 
                    WHERE item_status = 'Enable' 
                    ORDER BY item_name ASC" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }
    }
    public function get_product_array() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT * FROM item_purchase_ims 
                    INNER JOIN item_ims 
                    ON item_ims.item_id =  item_purchase_ims.item_id 
                    WHERE item_purchase_ims.item_purchase_status = 'Enable' 
                    AND item_ims.item_status = 'Enable' 
                    AND item_ims.item_available_quantity > 0 
                    ORDER BY item_ims.item_name ASC" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }
    }
    public function Get_tax_field() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT * FROM tax_ims WHERE tax_status = 'Enable' ORDER BY tax_name ASC" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }	
    }
    public function Get_total_no_of_product() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT COUNT(item_id) AS Total FROM item_ims 
		WHERE item_status = 'Enable' AND item_available_quantity > 0 " ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $output = array();
        foreach($results as $row)
		{
            $i++;
            $output["Total"] = $row["Total"];
		}

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $output;
        }	
    }
    public function Get_total_product_purchase() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT SUM(item_purchase_total_cost) AS Total FROM item_purchase_ims 
		            WHERE item_purchase_status = 'Enable'" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $output = array();
        foreach($results as $row)
		{
            $i++;
            $output["Total"] = $row["Total"];
		}

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $output;
        }	
    }
    public function Get_total_product_sale() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT SUM(order_total_amount) AS Total FROM order_ims 
		            WHERE order_status = 'Enable'" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $output = array();
        foreach($results as $row)
		{
            $i++;
            $output["Total"] = $row["Total"];
		}

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $output;
        }		             
    }
    public function Count_outstock_product() {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT COUNT(item_id) AS Total FROM item_ims WHERE item_status = 'Enable' AND item_available_quantity <= 0 " ;

        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }
     }
    public function Get_currency_symbol() { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        $query = "SELECT store_currency FROM store_ims LIMIT 1" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $i = 0;
        $result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	

        if($i==0){
            $res['status'] = false;
            $res['message'] = $i . " results found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }
    }
    public function Get_Product_company_code($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $item_manufacuter_company_id = "";
       
        
        if (empty($requestData['item_manufacuter_company_id'])) {
            $errormsg .= "item_manufacuter_company id not supplied.";
            $status = false;
        }
        else {
            $item_manufacuter_company_id = htmlspecialchars(strip_tags($requestData['item_manufacuter_company_id']));
        }
        
       
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
        
        $query = "SELECT company_short_name FROM item_manufacuter_company_ims WHERE item_manufacuter_company_id = '" . $item_manufacuter_company_id . "'" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		$result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	
        if($i==0){
            $res['status'] = false;
            $res['message'] = "No record found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }
    }
    public function Get_category_name($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $category_id = "";
       
        
        if (empty($requestData['category_id'])) {
            $errormsg .= "category id not supplied.";
            $status = false;
        }
        else {
            $category_id = htmlspecialchars(strip_tags($requestData['category_id']));
        }
        
       
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
        
        $query = "SELECT category_name FROM category_ims 
		        WHERE category_id = '". $category_id ."'" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		$result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	
        if($i==0){
            $res['status'] = false;
            $res['message'] = "No record found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }	
    }
    public function Get_order_tax_percentage($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $order_id = "";
       
        
        if (empty($requestData['order_id'])) {
            $errormsg .= "order id not supplied.";
            $status = false;
        }
        else {
            $order_id = htmlspecialchars(strip_tags($requestData['order_id']));
        }
        
       
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
        
        $query = "SELECT order_tax_percentage FROM order_ims 
		        WHERE order_id = ". $order_id  ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        $output = array();
        $i=0;
		foreach($results as $row)
		{
            $i++;		
			$output['order_tax_percentage'] = $row['order_tax_percentage'];
		}

        if($i==0){
            $res['status'] = false;
            $res['message'] = "No record found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $output;
        }	
    }
    public function Get_user_name_from_id($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $user_id = "";
       
        
        if (empty($requestData['user_id'])) {
            $errormsg .= "User id not supplied.";
            $status = false;
        }
        else {
            $user_id = htmlspecialchars(strip_tags($requestData['user_id']));
        }
        
       
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
        
        $query = "SELECT user_name FROM user_ims 
		            WHERE user_id = '". $user_id ."'" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		foreach($results as $row)
		{
            $i++;		
			$user_name = $row["user_name"];
		}

        if($i==0){
            $res['status'] = false;
            $res['message'] = "No record found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $user_name;
        }	
    }
    public function Get_product_name($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $item_id = "";
        $item_purchase_id = "";
        
        if (empty($requestData['item_id'])) {
            $errormsg .= "Item id not supplied.";
            $status = false;
        }
        else {
            $item_id = htmlspecialchars(strip_tags($requestData['item_id']));
        }
        
        if (empty($requestData['item_purchase_id'])) {
            $errormsg .= "Item purchase id not supplied.";
            $status = false;
        }
        else {
            $item_purchase_id = htmlspecialchars(strip_tags($requestData['item_purchase_id']));
        }
        

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
        
        $query = "SELECT * FROM item_ims 
                    INNER JOIN item_manufacuter_company_ims ON item_manufacuter_company_ims.item_manufacuter_company_id = item_ims.item_manufactured_by 
                    WHERE item_ims.item_id = '" . $item_id . "'" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $data = array();

		foreach($results as $row)
		{
			$data['item_name'] = $row['item_name'];
			$data['company_short_name'] = $row['company_short_name'];
		}

		$query = "SELECT * FROM item_purchase_ims 
		            WHERE item_purchase_id = '" . $item_purchase_id . "'";

        $results = $this->conn->query($query,MYSQLI_USE_RESULT);

        $i=0;
		foreach($results as $row)
		{
            $i++;
			$data['item_batch_no'] = $row['item_batch_no'];
			$data['expiry_date'] = $row['item_expired_month'] . ' / ' . $row["item_expired_year"];
			$data['item_sale_price_per_unit'] = $row["item_sale_price_per_unit"];
		}

        if($i==0){
            $res['status'] = false;
            $res['message'] = "No record found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $data;
        }		
    }
    public function Get_item_purchase_qty($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $item_purchase_id = "";
       
        
        if (empty($requestData['item_purchase_id'])) {
            $errormsg .= "item_purchase_id not supplied.";
            $status = false;
        }
        else {
            $item_purchase_id = htmlspecialchars(strip_tags($requestData['item_purchase_id']));
        }
        
       
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
        
        $query = "SELECT item_purchase_qty FROM item_purchase_ims 
                    WHERE item_purchase_id = '". $item_purchase_id ."'" ;


        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		$result = array();
        /*while($row = $results->fetchObject()){
            $result[]=$row['item_purchase_qty'];
            $i++;
        }*/
        foreach($results as $row)
		{
            $i++;
            $result["item_purchase_qty"] = $row["item_purchase_qty"];
		}
	
        if($i==0){
            $res['status'] = false;
            $res['message'] = "No record found!";
            $res['info'] = $results;
            return $res;
        }
        else{
            return $result;
        }	
    }
    public function fetch_chart_data($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $start_date = "";
        $end_date = "";


        if (empty($requestData['start_date'])) {
            $errormsg .= "start_date not supplied.";
            $status = false;
        } else {
            $start_date = htmlspecialchars(strip_tags($requestData['start_date']));
        }

        if (empty($requestData['end_date'])) {
            $errormsg .= "end_date not supplied.";
            $status = false;
        } else {
            $end_date = htmlspecialchars(strip_tags($requestData['end_date']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }

        $query = "SELECT SUM(order_total_amount) AS Total, DATE(order_added_on) AS Order_date FROM order_ims 
                    WHERE order_status = 'Enable' 
                    AND DATE(order_added_on) >= '". $start_date ."' 
                    AND DATE(order_added_on) <= '". $end_date ."' 
                    GROUP BY Order_date";


        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        $result = array();
        while ($row = $results->fetchObject()) {
            $result[] = $row;
            $i++;
        }
        //if ($i == 0) {
        //    $res['status'] = false;
        //    $res['message'] = "No record found!";
        //    $res['info'] = $results;
        //    return $res;
        //} else {
            return $result;
        //}
    }
    public function fetch_out_stock_product($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $search_value = "";
        $order_0_col = "";
        $order_0_dir = "";
        $start = "0";
        $length = "";

        if (!empty($requestData['search_value'])) {            
            $search_value = htmlspecialchars(strip_tags($requestData['search_value']));
        }

        if (!empty($requestData['order_0_col'])) {            
            $order_0_col = htmlspecialchars(strip_tags($requestData['order_0_col']));
        }

        if (!empty($requestData['order_0_dir'])) {            
            $order_0_dir = htmlspecialchars(strip_tags($requestData['order_0_dir']));
        }

        if (!empty($requestData['start'])) {            
            $start = htmlspecialchars(strip_tags($requestData['start']));
        }

        if (!empty($requestData['length'])) {            
            $length = htmlspecialchars(strip_tags($requestData['length']));
        }

        $query = "SELECT * FROM item_ims 
                    INNER JOIN category_ims 
                    ON category_ims.category_id = item_ims.item_category 
                    INNER JOIN  item_manufacuter_company_ims 
                    ON  item_manufacuter_company_ims.item_manufacuter_company_id = item_ims.item_manufactured_by 
                    INNER JOIN location_rack_ims 
                    ON location_rack_ims.location_rack_id = item_ims.item_location_rack 
                    WHERE item_ims.item_status = 'Enable' 
                    AND item_ims.item_available_quantity < 1 ";

        if($search_value <> "" ){
            $query .= 'AND (item_ims.item_name LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_manufacuter_company_ims.company_name LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_ims.item_available_quantity LIKE "%'.$search_value.'%" ';
			$query .= 'OR location_rack_ims.location_rack_name LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_ims.item_status LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_ims.item_add_datetime LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_ims.item_update_datetime LIKE "%'.$search_value.'%") ';
        }

        if($order_0_col <> "" AND $order_0_dir <> ""){
            $query .=  'ORDER BY '.$order_0_col.', '.$order_0_dir.' ';       
        }
        else {
            $query .= 'ORDER BY item_ims.item_name ASC ';
        }

        if($length != -1)
		{
			$query .= 'LIMIT ' . $start . ', ' . $length;
		}

        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        $result = array();
        while ($row = $results->fetchObject()) {
            $result[] = $row;
            $i++;
        }        
        //if ($i == 0) {
        //    $res['status'] = false;
        //    $res['message'] = "No record found!";
        //    $res['info'] = $results;
        //    return $res;
        //} else {
            return $result;
        //} 
    }
    public function purchase_item($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        $errormsg = "";
        $status = true;

        $item_id                      = "";         //  $formdata['item_id'],
        $supplier_id                  = "";         //  $formdata['supplier_id'],
        $item_batch_no                = "";         //  $formdata['item_batch_no'],
        $item_purchase_qty            = 1;         //  $formdata['item_purchase_qty'], 
        $available_quantity           = "";         //  $formdata['item_purchase_qty'], 
        $item_purchase_price_per_unit = 1;         //  $formdata['item_purchase_price_per_unit'],
        $item_purchase_total_cost     = 1;         //  $total_cost,
        $item_manufacture_month       = "MONTH(NOW())";         //  $formdata['item_manufacture_month'],
        $item_manufacture_year        = "YEAR(NOW())";         //  $formdata['item_manufacture_year'],
        $item_expired_month           = "MONTH(NOW())";         //  $formdata['item_expired_month'],
        $item_expired_year            = "YEAR(NOW()) + 10";         //  $formdata['item_expired_year'],
        $item_sale_price_per_unit     = 1;         //  $formdata['item_sale_price_per_unit'],
        $item_purchase_datetime       = "NOW()";      //  $object->now,
        $item_purchase_status         = "Enable";         //  'Enable',
        $item_purchase_enter_by       = "unknown";  //  $_SESSION['LoginID'],
        

        if(empty($requestData['item_id'])) { 
            $errormsg .= " item_id is missing.";
            $status = false;
        }
        else{
            $item_id = htmlspecialchars(strip_tags($requestData['item_id']));
        }

        if(empty($requestData['supplier_id'])) { 
            $errormsg .= " supplier_id is missing.";
            $status = false;
        }
        else{
            $supplier_id=htmlspecialchars(strip_tags($requestData['supplier_id']));
        }

        if(empty($requestData['item_batch_no'])) { 
            $errormsg .= " item_batch_no is missing.";
            $status = false;
        }
        else{
            $item_batch_no = htmlspecialchars(strip_tags($requestData['item_batch_no']));
        }

        if(empty($requestData['item_purchase_qty'])) { 
            $errormsg .= " item_purchase_qty is missing.";
            $status = false;
        }
        else{
            $item_purchase_qty = htmlspecialchars(strip_tags($requestData['item_purchase_qty']));
        }

        if(empty($requestData['available_quantity'])) {
            $available_quantity = $item_purchase_qty;
        }
        else{
            $available_quantity = htmlspecialchars(strip_tags($requestData['item_purchase_qty']));
        
        }

        if(!empty($requestData['item_purchase_price_per_unit'])) {
            $item_purchase_price_per_unit = htmlspecialchars(strip_tags($requestData['item_purchase_price_per_unit']));
        }

        if(!empty($requestData['item_purchase_total_cost'])) {
            $item_purchase_total_cost  = htmlspecialchars(strip_tags($requestData['item_purchase_total_cost']));
        }

        if(!empty($requestData['item_manufacture_month'])) {             
            $item_manufacture_month  = htmlspecialchars(strip_tags($requestData['item_manufacture_month']));
        }

        if(!empty($requestData['item_manufacture_year'])) {             
            $item_manufacture_year = htmlspecialchars(strip_tags($requestData['item_manufacture_year']));
        }

        if(!empty($requestData['item_expired_month'])) {             
            $item_expired_month = htmlspecialchars(strip_tags($requestData['item_expired_month']));
        }

        if (!empty($requestData['item_expired_year'])) {            
            $item_expired_year = htmlspecialchars(strip_tags($requestData['item_expired_year']));
        }

        if(!empty($requestData['item_sale_price_per_unit'])) {            
            $item_sale_price_per_unit=htmlspecialchars(strip_tags($requestData['item_sale_price_per_unit']));
        }

        //if(empty($requestData['item_purchase_datetime'])) { 

        //}

        if(!empty($requestData['item_purchase_status'])) {             
            $item_purchase_status = htmlspecialchars(strip_tags($requestData['item_purchase_status']));
        }

        if(empty($requestData['item_purchase_enter_by'])) {
            $item_purchase_enter_by = "unknown";
        }
        else{
            $item_purchase_enter_by = htmlspecialchars(strip_tags($requestData['item_purchase_enter_by']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        //Use replace function for insert as well as update thru stored procedure
        $query = "INSERT INTO item_purchase_ims 
                    (
                        item_id, 
                        supplier_id, 
                        item_batch_no, 
                        item_purchase_qty, 
                        available_quantity, 
                        item_purchase_price_per_unit, 
                        item_purchase_total_cost, 
                        item_manufacture_month, 
                        item_manufacture_year, 
                        item_expired_month, 
                        item_expired_year, 
                        item_sale_price_per_unit, 
                        item_purchase_datetime, 
                        item_purchase_status, 
                        item_purchase_enter_by
                    ) 
                    VALUES 
                    (
                        '" . $item_id . "', 
                        '" . $supplier_id . "',
                        '" . $item_batch_no . "',
                        " . $item_purchase_qty . ",
                        " . $available_quantity . ",
                        " . $item_purchase_price_per_unit . ",
                        " . $item_purchase_total_cost . ",
                        " . $item_manufacture_month . ",
                        " . $item_manufacture_year . ",
                        " . $item_expired_month . ",
                        " . $item_expired_year . ",
                        " . $item_sale_price_per_unit . ",
                        NOW(),
                        '" . $item_purchase_status . "',
                        '" . $item_purchase_enter_by . "'
                    )  ";

        // prepare query
        $stmt = $this->conn->prepare($query);

        if($this->debug){ var_dump($stmt); die;}

        if ($stmt->execute()) {

            $query = "UPDATE item_ims 
                    SET item_available_quantity = item_available_quantity + " . $item_purchase_qty . " 
                    WHERE item_id = '" . $item_id . "'";
            $stmt = $this->conn->prepare($query);

            if ($this->debug) {
                var_dump($stmt);
                die;
            }

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Added Purchase!!";
                $res['info'] = $item_id;
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Updating Item Available Quantity Failed, but purchase successfully added at API!!";
                if ($this->debug) {
                    $res['info'] = $query;
                } else {
                    $res['info'] = $stmt;
                }
            }
        }
        else {
            $res['status'] = false;
            $res['message'] = "[Inventory] Adding Purchase Failed at API!!";
            if ($this->debug) {
                $res['info'] = $query;
            } else {
                $res['info'] = $stmt;
            }
        }
        return $res;         
    }

    public function update_purchase($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $item_id = ""; //  $formdata['item_id'],
        $supplier_id = ""; //  $formdata['supplier_id'],
        $item_batch_no = ""; //  $formdata['item_batch_no'],
        $item_purchase_qty = 1; //  $formdata['item_purchase_qty'], 
        $available_quantity = ""; //  $formdata['item_purchase_qty'], 
        $item_purchase_price_per_unit = 1; //  $formdata['item_purchase_price_per_unit'],
        $item_purchase_total_cost = 1; //  $total_cost,
        $item_manufacture_month = "MONTH(NOW())"; //  $formdata['item_manufacture_month'],
        $item_manufacture_year = "YEAR(NOW())"; //  $formdata['item_manufacture_year'],
        $item_expired_month = "MONTH(NOW())"; //  $formdata['item_expired_month'],
        $item_expired_year = "YEAR(NOW()) + 10"; //  $formdata['item_expired_year'],
        $item_sale_price_per_unit = 1; //  $formdata['item_sale_price_per_unit'],        
        $item_purchase_id = ""; //  $item_purchase_id
        $original_item_purchase_qty = 0;

        if (empty($requestData['item_purchase_id'])) {
            $errormsg .= " item_purchase_id is missing.";
            $status = false;
        } else {
            $item_purchase_id = htmlspecialchars(strip_tags($requestData['item_purchase_id']));
        }

        if (empty($requestData['item_id'])) {
            $errormsg .= " item_id is missing.";
            $status = false;
        } else {
            $item_id = htmlspecialchars(strip_tags($requestData['item_id']));
        }

        if (empty($requestData['supplier_id'])) {
            $errormsg .= " supplier_id is missing.";
            $status = false;
        } else {
            $supplier_id = htmlspecialchars(strip_tags($requestData['supplier_id']));
        }

        if (empty($requestData['item_batch_no'])) {
            $errormsg .= " item_batch_no is missing.";
            $status = false;
        } else {
            $item_batch_no = htmlspecialchars(strip_tags($requestData['item_batch_no']));
        }

        if (empty($requestData['item_purchase_qty'])) {
            $errormsg .= " item_purchase_qty is missing.";
            $status = false;
        } else {
            $item_purchase_qty = htmlspecialchars(strip_tags($requestData['item_purchase_qty']));
        }

        if (empty($requestData['available_quantity'])) {
            $available_quantity = $item_purchase_qty;
        } else {
            $available_quantity = htmlspecialchars(strip_tags($requestData['item_purchase_qty']));

        }

        if (!empty($requestData['item_purchase_price_per_unit'])) {
            $item_purchase_price_per_unit = htmlspecialchars(strip_tags($requestData['item_purchase_price_per_unit']));
        }

        if (!empty($requestData['item_purchase_total_cost'])) {
            $item_purchase_total_cost = htmlspecialchars(strip_tags($requestData['item_purchase_total_cost']));
        }

        if (!empty($requestData['item_manufacture_month'])) {
            $item_manufacture_month = htmlspecialchars(strip_tags($requestData['item_manufacture_month']));
        }

        if (!empty($requestData['item_manufacture_year'])) {
            $item_manufacture_year = htmlspecialchars(strip_tags($requestData['item_manufacture_year']));
        }

        if (!empty($requestData['item_expired_month'])) {
            $item_expired_month = htmlspecialchars(strip_tags($requestData['item_expired_month']));
        }

        if (!empty($requestData['item_expired_year'])) {
            $item_expired_year = htmlspecialchars(strip_tags($requestData['item_expired_year']));
        }

        if (!empty($requestData['item_sale_price_per_unit'])) {
            $item_sale_price_per_unit = htmlspecialchars(strip_tags($requestData['item_sale_price_per_unit']));
        }


        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }
        //check if purchase qty was changed
        $tmpRequest = array('item_purchase_id' => $item_purchase_id);
        $tmpResult = $this->Get_item_purchase_qty($tmpRequest);
        if($this->debug){
            echo "<br>>>>after getting the original item purchase quantity. Results: "; 
            var_dump($tmpResult);
        }
        foreach ($tmpResult as $temp_row) {
            $original_item_purchase_qty = $tmpResult['item_purchase_qty'];
        }
        
        unset($tmpRequest);
        unset($tmpResult);
        if ($this->debug) {
            echo "<br>>>> from the section where item IMS is updated with change in purchase quantity:";
            echo "<br>original quantity: ", $original_item_purchase_qty;
            echo "<br>updated quantity: ", $item_purchase_qty, "<br><br>";
        }
        
        $query = "UPDATE item_purchase_ims         
                  SET                     
                    item_id    =                '" . $item_id . "', 
                    supplier_id =               '" . $supplier_id . "',
                    item_batch_no =             '" . $item_batch_no . "',
                    item_purchase_qty =          " . $item_purchase_qty . ",
                    available_quantity =         " . $available_quantity . ",
                    item_purchase_price_per_unit=" . $item_purchase_price_per_unit . ",
                    item_purchase_total_cost =   " . $item_purchase_total_cost . ",
                    item_manufacture_month =     " . $item_manufacture_month . ",
                    item_manufacture_year =      " . $item_manufacture_year . ",
                    item_expired_month =         " . $item_expired_month . ",
                    item_expired_year =          " . $item_expired_year . ",
                    item_sale_price_per_unit =   " . $item_sale_price_per_unit . "
                WHERE item_purchase_id =         " . $item_purchase_id;

        // prepare query
        $stmt = $this->conn->prepare($query);

        if ($this->debug) {
            var_dump($stmt);            
        }

        if ($stmt->execute()) {
            
            if ($original_item_purchase_qty != $item_purchase_qty) {
                if ($this->debug) {
                    echo "<br>>>> from the section where item IMS is updated with change in purchase quantity:";
                    echo "<br>original quantity: ", $original_item_purchase_qty;
                    echo "<br>updated quantity: ", $item_purchase_qty;
                }
                $final_update_qty = 0;
                if ($original_item_purchase_qty > $item_purchase_qty) {
                    $final_update_qty = $original_item_purchase_qty - $item_purchase_qty;

                    $iQuery = "  UPDATE item_ims 
                                SET item_available_quantity = item_available_quantity - " . $final_update_qty . " 
                                WHERE item_id = '" . $item_id . "'
                    ";
                } else {
                    $final_update_qty = $item_purchase_qty - $original_item_purchase_qty;

                    $iQuery = " UPDATE item_ims 
                                SET item_available_quantity = item_available_quantity + " . $final_update_qty . " 
                                WHERE item_id = '" . $item_id . "'
                    ";
                }


                $stmt = $this->conn->prepare($iQuery);
                if ($this->debug) {
                    var_dump($stmt);
                }
                if ($stmt->execute()) {
                    $res['status'] = true;
                    $res['message'] = "[Inventory] Successfully Updated Purchase!!";
                    $res['info'] = $item_id;
                } else {
                    $res['status'] = false;
                    $res['message'] = "[Inventory] Updating Item Available Quantity Failed, but purchase successfully updated at API!!";
                    if ($this->debug) {
                        $res['info'] = $query;
                    } else {
                        $res['info'] = $stmt;
                    }
                }
            } else {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Updated Purchase. No adjustment needed on the item table!!";
                $res['info'] = $item_id;
            }
        } else {
            $res['status'] = false;
            $res['message'] = "[Inventory] Updating Purchase Failed at API!!";
            if ($this->debug) {
                $res['info'] = $query;
            } else {
                $res['info'] = $stmt;
            }
        }
        return $res;
    }

    public function delete_purchase($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $item_id = ""; //  $formdata['item_id'],
        $supplier_id = ""; //  $formdata['supplier_id'],
        $item_batch_no = ""; //  $formdata['item_batch_no'],
        $item_purchase_qty = 1; //  $formdata['item_purchase_qty'], 
        $available_quantity = ""; //  $formdata['item_purchase_qty'], 
        $item_purchase_price_per_unit = 1; //  $formdata['item_purchase_price_per_unit'],
        $item_purchase_total_cost = 1; //  $total_cost,
        $item_manufacture_month = "MONTH(NOW())"; //  $formdata['item_manufacture_month'],
        $item_manufacture_year = "YEAR(NOW())"; //  $formdata['item_manufacture_year'],
        $item_expired_month = "MONTH(NOW())"; //  $formdata['item_expired_month'],
        $item_expired_year = "YEAR(NOW()) + 10"; //  $formdata['item_expired_year'],
        $item_sale_price_per_unit = 1; //  $formdata['item_sale_price_per_unit'],        
        $item_purchase_id = ""; //  $item_purchase_id
        $original_item_purchase_qty = 0;

        if (empty($requestData['item_purchase_id'])) {
            $errormsg .= " item_purchase_id is missing.";
            $status = false;
        } else {
            $item_purchase_id = htmlspecialchars(strip_tags($requestData['item_purchase_id']));
        }

        if (empty($requestData['item_id'])) {
            $errormsg .= " item_id is missing.";
            $status = false;
        } else {
            $item_id = htmlspecialchars(strip_tags($requestData['item_id']));
        }

        if (empty($requestData['supplier_id'])) {
            $errormsg .= " supplier_id is missing.";
            $status = false;
        } else {
            $supplier_id = htmlspecialchars(strip_tags($requestData['supplier_id']));
        }

        if (empty($requestData['item_batch_no'])) {
            $errormsg .= " item_batch_no is missing.";
            $status = false;
        } else {
            $item_batch_no = htmlspecialchars(strip_tags($requestData['item_batch_no']));
        }

        if (empty($requestData['item_purchase_qty'])) {
            $errormsg .= " item_purchase_qty is missing.";
            $status = false;
        } else {
            $item_purchase_qty = htmlspecialchars(strip_tags($requestData['item_purchase_qty']));
        }

        if (empty($requestData['available_quantity'])) {
            $available_quantity = $item_purchase_qty;
        } else {
            $available_quantity = htmlspecialchars(strip_tags($requestData['item_purchase_qty']));

        }

        if (!empty($requestData['item_purchase_price_per_unit'])) {
            $item_purchase_price_per_unit = htmlspecialchars(strip_tags($requestData['item_purchase_price_per_unit']));
        }

        if (!empty($requestData['item_purchase_total_cost'])) {
            $item_purchase_total_cost = htmlspecialchars(strip_tags($requestData['item_purchase_total_cost']));
        }

        if (!empty($requestData['item_manufacture_month'])) {
            $item_manufacture_month = htmlspecialchars(strip_tags($requestData['item_manufacture_month']));
        }

        if (!empty($requestData['item_manufacture_year'])) {
            $item_manufacture_year = htmlspecialchars(strip_tags($requestData['item_manufacture_year']));
        }

        if (!empty($requestData['item_expired_month'])) {
            $item_expired_month = htmlspecialchars(strip_tags($requestData['item_expired_month']));
        }

        if (!empty($requestData['item_expired_year'])) {
            $item_expired_year = htmlspecialchars(strip_tags($requestData['item_expired_year']));
        }

        if (!empty($requestData['item_sale_price_per_unit'])) {
            $item_sale_price_per_unit = htmlspecialchars(strip_tags($requestData['item_sale_price_per_unit']));
        }


        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }
        //check if purchase qty was changed
        $tmpRequest = array('item_purchase_id' => $item_purchase_id);
        $tmpResult = $this->Get_item_purchase_qty($tmpRequest);
        foreach ($tmpResult as $temp_row) {
            $original_item_purchase_qty = $temp_row["item_purchase_qty"];
        }
        unset($tmpRequest);
        unset($tmpResult);

        
        $query = "UPDATE item_purchase_ims         
                  SET                     
                    item_id    =                '" . $item_id . "', 
                    supplier_id =               '" . $supplier_id . "',
                    item_batch_no =             '" . $item_batch_no . "',
                    item_purchase_qty =          " . $item_purchase_qty . ",
                    available_quantity =         " . $available_quantity . ",
                    item_purchase_price_per_unit=" . $item_purchase_price_per_unit . ",
                    item_purchase_total_cost =   " . $item_purchase_total_cost . ",
                    item_manufacture_month =     " . $item_manufacture_month . ",
                    item_manufacture_year =      " . $item_manufacture_year . ",
                    item_expired_month =         " . $item_expired_month . ",
                    item_expired_year =          " . $item_expired_year . ",
                    item_sale_price_per_unit =   " . $item_sale_price_per_unit . "
                WHERE item_purchase_id =         " . $item_purchase_id;

        // prepare query
        $stmt = $this->conn->prepare($query);

        if ($this->debug) {
            var_dump($stmt);
            die;
        }

        if ($stmt->execute()) {
            
            if ($original_item_purchase_qty != $item_purchase_qty) {
                $final_update_qty = 0;
                if ($original_item_purchase_qty > $item_purchase_qty) {
                    $final_update_qty = $original_item_purchase_qty - $item_purchase_qty;

                    $query = "  UPDATE item_ims 
                                SET item_available_quantity = item_available_quantity - " . $final_update_qty . " 
                                WHERE item_id = '" . $item_id . "'
                    ";
                } else {
                    $final_update_qty = $item_purchase_qty - $original_item_purchase_qty;

                    $query = " UPDATE item_ims 
                                SET item_available_quantity = item_available_quantity + " . $final_update_qty . " 
                                WHERE item_id = '" . $item_id . "'
                    ";
                }

                if ($this->debug) {
                    var_dump($stmt);
                }

                if ($stmt->execute()) {
                    $res['status'] = true;
                    $res['message'] = "[Inventory] Successfully Updated Purchase!!";
                    $res['info'] = $item_id;
                } else {
                    $res['status'] = false;
                    $res['message'] = "[Inventory] Updating Item Available Quantity Failed, but purchase successfully updated at API!!";
                    if ($this->debug) {
                        $res['info'] = $query;
                    } else {
                        $res['info'] = $stmt;
                    }
                }
            }
        } else {
            $res['status'] = false;
            $res['message'] = "[Inventory] Updating Purchase Failed at API!!";
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
