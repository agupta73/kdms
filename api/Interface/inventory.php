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
  
    public function delete_upsert($requestData) {
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
    // public function Get_tax_field() { 
    //     $res = array();
    //     $res['status'] = false;
    //     $res['message'] = '';
    //     $errormsg = "";
    //     $status = true;
        
    //     $query = "SELECT * FROM tax_ims WHERE tax_status = 'Enable' ORDER BY tax_name ASC" ;


    //     if($this->debug) {var_dump($query);}
                
    //     $results = $this->conn->query($query,MYSQLI_USE_RESULT);
    //     $i = 0;
    //     $result = array();
    //     while($row = $results->fetchObject()){
    //         $result[]=$row;
    //         $i++;
    //     }	

    //     if($i==0){
    //         $res['status'] = false;
    //         $res['message'] = $i . " results found!";
    //         $res['info'] = $results;
    //         return $res;
    //     }
    //     else{
    //         return $result;
    //     }	
    // }
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
        //$query = "SELECT * FROM item_manufacuter_company_ims WHERE item_manufacuter_company_id = '" . $item_manufacuter_company_id . "'" ;

        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		$result = array();
        //foreach($results as $row){
        while($row = $results->fetchObject()){
        //    $result[]=$row['company_short_name'];
        $result[]=$row;
            $i++;
        }	
        if($i==0){
            $result= array('company_short_name'=>'Company not found');
        }
        
            return $result;
        
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
   /* public function Get_user_name_from_id($requestData) { 
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
    */
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
    public function Get_item_purchase_record($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $item_purchase_id = "";
       
        
        if (!empty($requestData['item_purchase_id'])) {            
            $item_purchase_id = htmlspecialchars(strip_tags($requestData['item_purchase_id']));
        }
               
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
        
        $query = "SELECT * FROM item_purchase_ims ";
        if($item_purchase_id != ""){
            $query .= " WHERE item_purchase_id = '". $item_purchase_id ."'" ;
        }

        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		$result = array();
        while($row = $results->fetchObject()){
            $result[]=$row;
            $i++;
        }	
        /*$i=0;
		$result = array();       
        foreach($results as $row)
		{
            $i++;
            $result[] = $row;
            if($this->debug) {var_dump($row);}
        }*/
        if($this->debug) {var_dump($result);}

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
    public function get_items_for_purchase_id($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $item_purchase_id = "";
       
        
        if (empty($requestData['item_purchase_id'])) {
            $errormsg .= " item_purchase_id is missing.";
            $status = false;
        } else {
            $item_purchase_id = htmlspecialchars(strip_tags($requestData['item_purchase_id']));
        }
               
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
        
        $query = "SELECT * FROM item_purchase_ims 
                    INNER JOIN item_ims ON item_ims.item_id =  item_purchase_ims.item_id 
                    WHERE item_purchase_ims.item_purchase_id = '" . $item_purchase_id . "'" ;
        

        if($this->debug) {var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		$result = array();
        while($row = $results->fetchObject()){
            //foreach($results as $row){
            $result[]=$row;
            $i++;
        }	
        
        if($this->debug) {var_dump($result);}

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
    public function get_item_for_item_id($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $item_id = "";
       
        
        if (!empty($requestData['item_id'])) {
            $item_id = htmlspecialchars(strip_tags(trim($requestData['item_id'])));
        }
               
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }

        $query = "SELECT * FROM item_ims ";
        if($item_id != "") {
            $query .= "WHERE item_id = '" . $item_id . "'" ;
        }

        if($this->debug) {
            echo "/n request data: ";
            var_dump($requestData);
            echo "/n query: ";
            var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		$result = array();
        while($row = $results->fetchObject()){
            //foreach($results as $row){
            $result[]=$row;
            $i++;
        }	
        
        if($this->debug) {var_dump($result);}

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
    public function get_category_for_category_id($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $category_id = "";
       
        
        if (!empty($requestData['category_id'])) {
            $category_id = htmlspecialchars(strip_tags(trim($requestData['category_id'])));
        }
               
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }

        $query = "SELECT * FROM category_ims ";
        if($category_id != "") {
            $query .= "WHERE category_id = '" . $category_id . "'" ;
        }

        if($this->debug) {
            echo "/n request data: ";
            var_dump($requestData);
            echo "/n query: ";
            var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		$result = array();
        while($row = $results->fetchObject()){
            //foreach($results as $row){
            $result[]=$row;
            $i++;
        }	
        
        if($this->debug) {var_dump($result);}

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
    public function get_supplier_for_supplier_id($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $supplier_id = "";
       
        
        if (!empty($requestData['supplier_id'])) {
            $supplier_id = htmlspecialchars(strip_tags(trim($requestData['supplier_id'])));
        }
               
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }

        $query = "SELECT * FROM supplier_ims ";
        if($supplier_id != "") {
            $query .= "WHERE supplier_id = '" . $supplier_id . "'" ;
        }

        if($this->debug) {
            echo "/n request data: ";
            var_dump($requestData);
            echo "/n query: ";
            var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		$result = array();
        while($row = $results->fetchObject()){
            //foreach($results as $row){
            $result[]=$row;
            $i++;
        }	
        
        if($this->debug) {var_dump($result);}

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
    public function get_tax_for_tax_id($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $tax_id = "";
       
        
        if (!empty($requestData['tax_id'])) {
            $tax_id = htmlspecialchars(strip_tags(trim($requestData['tax_id'])));
        }
               
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }

        $query = "SELECT * FROM tax_ims ";
        if($tax_id != "") {
            $query .= "WHERE tax_id = '" . $tax_id . "'" ;
        }

        if($this->debug) {
            echo "/n request data: ";
            var_dump($requestData);
            echo "/n query: ";
            var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		$result = array();
        while($row = $results->fetchObject()){
            //foreach($results as $row){
            $result[]=$row;
            $i++;
        }	
        
        if($this->debug) {var_dump($result);}

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
    public function get_location_for_location_id($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $location_rack_id = "";
       
        
        if (!empty($requestData['location_rack_id'])) {
            $location_rack_id = htmlspecialchars(strip_tags(trim($requestData['location_rack_id'])));
        }
               
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }

        $query = "SELECT * FROM location_rack_ims ";
        
        if($location_rack_id != "") {
            $query .= "WHERE location_rack_id =  '" . $location_rack_id . "'" ;
        }

        if($this->debug) {
            echo "/n request data: ";
            var_dump($requestData);
            echo "/n query: ";
            var_dump($query);}
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $i=0;
		$result = array();
        while($row = $results->fetchObject()){
            //foreach($results as $row){
            $result[]=$row;
            $i++;
        }	
        
        if($this->debug) {var_dump($result);}

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

        if($order_0_col != "" ){
            $query .=  'ORDER BY '.$order_0_col.' '.$order_0_dir.' ';       
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
    public function fetch_purchase($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $is_master = false;
        $user_id = "";        
        $search_value = "";
        $order_0_col = "";
        $order_0_dir = "";
        $start = "0";
        $length = "";


        if(!empty($requestData['is_master'])) {            
            $is_master = htmlspecialchars(strip_tags($requestData['is_master']));
        }
        
        if(!empty($requestData['user_id'])) {            
            $user_id = htmlspecialchars(strip_tags($requestData['user_id']));
        }
        
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

        $query = "SELECT * FROM item_purchase_ims 
                    INNER JOIN item_ims ON item_ims.item_id = item_purchase_ims.item_id 
                    INNER JOIN  supplier_ims ON  supplier_ims.supplier_id = item_purchase_ims.supplier_id  ";

        $where = 'WHERE ';
        if($is_master == false){
            $where .= " item_purchase_ims.item_purchase_enter_by = '". $user_id ."' AND ";
        }

        if($search_value != "" ){
            $query .= $where . '(item_ims.item_name LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_purchase_ims.item_batch_no LIKE "%'.$search_value.'%" ';
			$query .= 'OR supplier_ims.supplier_name LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_purchase_ims.item_purchase_qty LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_purchase_ims.available_quantity LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_purchase_ims.item_purchase_price_per_unit LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_purchase_ims.item_purchase_total_cost LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_purchase_ims.item_sale_price_per_unit LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_purchase_ims.item_purchase_datetime LIKE "%'.$search_value.'%" ';
			$query .= 'OR item_purchase_ims.item_purchase_status LIKE "%'.$search_value.'%" ) ';
        }

        if($order_0_col != "" ){
            $query .=  'ORDER BY '.$order_0_col.' '.$order_0_dir.' ';       
        }
        else {
            $query .= 'ORDER BY  item_purchase_ims.item_purchase_id DESC ';
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
    public function get_order_for_order_id($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $order_id = 0;

        if(!empty($requestData['order_id'])) {             
            $order_id = htmlspecialchars(strip_tags($requestData['order_id']));
        }
        
        $query = " SELECT * FROM order_ims ";
        if($order_id != 0){
            $query .= "WHERE order_id = '" . $order_id . "'";
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
        return $result;        
    }
    public function get_order_item_for_order_id($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $order_id = "";

        if(empty($requestData['order_id'])) { 
            $errormsg .= " order_id is missing.";
            $status = false;
        }
        else{
            $order_id = htmlspecialchars(strip_tags($requestData['order_id']));
        }
        
        $query = " SELECT * FROM order_item_ims WHERE order_id = '" . $order_id . "'";

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
        return $result;        
    }
    public function fetch_orders($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $is_master = false;
        $user_id = "";        
        $search_value = "";
        $order_0_col = "";
        $order_0_dir = "";
        $start = "0";
        $length = "0";


        if(!empty($requestData['is_master'])) {            
            $is_master = htmlspecialchars(strip_tags($requestData['is_master']));
        }
        
        if(!empty($requestData['user_id'])) {            
            $user_id = htmlspecialchars(strip_tags($requestData['user_id']));
        }
        
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

        $query = "SELECT * FROM order_ims  ";
                    // INNER JOIN user_ims ON user_ims.user_id = order_ims.order_created_by ";
                    //INNER JOIN  supplier_ims ON  supplier_ims.supplier_id = item_purchase_ims.supplier_id  ";

        $where = 'WHERE ';
        if($is_master == false){
            $where .= " order_ims.order_created_by = '". $user_id ."' AND ";
        }

        if ($search_value != "") {
            $query .= $where . '(order_ims.order_id LIKE "%'. $search_value.'%" ';
			$query .= 'OR order_ims.buyer_name LIKE "%'. $search_value.'%" ';
			$query .= 'OR order_ims.order_total_amount LIKE "%'. $search_value.'%" ';
			$query .= 'OR order_ims.order_added_on LIKE "%'. $search_value.'%" ';
			$query .= 'OR order_ims.order_updated_on LIKE "%'. $search_value.'%" ';
			$query .= 'OR order_ims.order_status LIKE "%'. $search_value.'%") ';
        }
        if($order_0_col != "" ){
            $query .=  'ORDER BY '.$order_0_col.' '.$order_0_dir.' ';       
        }
        else {
            $query .= 'ORDER BY order_id DESC ';
        }

        if($length != -1 AND $length != 0)
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
        return $result;        
    }

    public function fetch_product($requestData)
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
        $length = "0";


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
                    INNER JOIN category_ims ON category_ims.category_id = item_ims.item_category 
                    INNER JOIN  item_manufacuter_company_ims ON  item_manufacuter_company_ims.item_manufacuter_company_id = item_ims.item_manufactured_by 
                    INNER JOIN location_rack_ims ON location_rack_ims.location_rack_id = item_ims.item_location_rack ";
                    


        if ($search_value != "") {
            $query .= 'WHERE item_ims.item_name LIKE "%'. $search_value .'%" ';
			$query .= 'OR item_manufacuter_company_ims.company_name LIKE "%'. $search_value .'%" ';
			$query .= 'OR category_ims.category_name LIKE "%'. $search_value .'%" ';
			$query .= 'OR location_rack_ims.location_rack_name LIKE "%'. $search_value .'%" ';
			$query .= 'OR item_ims.item_status LIKE "%'. $search_value .'%" ';
		}

        if($order_0_col != "" ){
            $query .=  'ORDER BY '.$order_0_col.' '.$order_0_dir.' ';       
        }
        else {
            $query .= 'ORDER BY item_id DESC ';
        }

        if($length != -1 AND $length != 0)
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
        return $result;        
    }
    public function fetch_tax($requestData)
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
        $length = "0";


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

        $query = "SELECT * FROM tax_ims ";
                    
        if ($search_value != "") {
            $query .= 'WHERE tax_name LIKE "%'. $search_value .'%" ';
			$query .= 'OR tax_percentage LIKE "%'. $search_value .'%" ';
			$query .= 'OR tax_status LIKE "%'. $search_value .'%" ';
			$query .= 'OR tax_added_on LIKE "%'. $search_value .'%" ';
			$query .= 'OR tax_updated_on LIKE "%'. $search_value .'%" ';
		}

        if($order_0_col != "" ){
            $query .=  'ORDER BY '.$order_0_col.' '.$order_0_dir.' ';       
        }
        else {
            $query .= 'ORDER BY tax_id DESC ';
        }

        if($length != -1 AND $length != 0)
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
        return $result;        
    }

    public function fetch_location_rack($requestData)
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
        $length = "0";


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

        $query = "SELECT * FROM location_rack_ims ";
                    
        if ($search_value != "") {
            $query .= 'WHERE location_rack_name LIKE "%'. $search_value .'%" ';
			$query .= 'OR location_rack_status LIKE "%'. $search_value .'%" ';
		}

        if($order_0_col != "" ){
            $query .=  'ORDER BY '.$order_0_col.' '.$order_0_dir.' ';       
        }
        else {
            $query .= 'ORDER BY location_rack_id DESC ';
        }

        if($length != -1 AND $length != 0)
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
        return $result;        
    }
    public function fetch_category($requestData)
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
        $length = "0";


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

        $query = "SELECT * FROM category_ims ";
                    


        if ($search_value != "") {
            $query .= 'WHERE category_name LIKE "%'. $search_value .'%" ';
			$query .= 'OR category_status LIKE "%'. $search_value .'%" ';
		}

        if($order_0_col != "" ){
            $query .=  'ORDER BY '.$order_0_col.' '.$order_0_dir.' ';       
        }
        else {
            $query .= 'ORDER BY category_id DESC ';
        }

        if($length != -1 AND $length != 0)
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
        return $result;        
    }
    public function fetch_supplier($requestData)
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
        $length = "0";


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

        $query = "SELECT * FROM supplier_ims ";
                    


        if ($search_value != "") {
            $query .= 'WHERE supplier_name LIKE "%'. $search_value .'%" ';
			$query .= 'OR supplier_address LIKE "%'. $search_value .'%" ';
			$query .= 'OR supplier_contact_no LIKE "%'. $search_value .'%" ';
			$query .= 'OR supplier_email LIKE "%'. $search_value .'%" ';
			$query .= 'OR supplier_status LIKE "%'. $search_value .'%" ';
			$query .= 'OR supplier_datetime LIKE "%'. $search_value .'%" ';
		}

        if($order_0_col != "" ){
            $query .=  'ORDER BY '.$order_0_col.' '.$order_0_dir.' ';       
        }
        else {
            $query .= 'ORDER BY supplier_id DESC ';
        }

        if($length != -1 AND $length != 0)
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
        return $result;        
    }
    public function add_supplier($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $supplier_name = "";
        $supplier_address = "";
        $supplier_contact_no = "";
        $supplier_email = "";
        $supplier_status = 'Enable';
        $supplier_datetime = 'NOW()';

        if (empty($requestData['supplier_name'])) {
            $errormsg .= " supplier_name is missing.";
            $status = false;
        } else {
            $supplier_name = htmlspecialchars(strip_tags($requestData['supplier_name']));
        }

        if (empty($requestData['supplier_address'])) {
            $errormsg .= " supplier_address is missing.";
            $status = false;
        } else {
            $supplier_address = htmlspecialchars(strip_tags($requestData['supplier_address']));
        }

        if (empty($requestData['supplier_contact_no'])) {
            $errormsg .= " supplier_contact_no is missing.";
            $status = false;
        } else {
            $supplier_contact_no = htmlspecialchars(strip_tags($requestData['supplier_contact_no']));
        }

        if (empty($requestData['supplier_email'])) {
            $errormsg .= " supplier_email is missing.";
            $status = false;
        } else {
            $supplier_email = htmlspecialchars(strip_tags($requestData['supplier_email']));
        }

        if (!empty($requestData['supplier_status'])) {
            $supplier_status = htmlspecialchars(strip_tags($requestData['supplier_status']));
        }

        if (!empty($requestData['supplier_datetime'])) {
            $supplier_datetime = htmlspecialchars(strip_tags($requestData['supplier_datetime']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "SELECT * FROM supplier_ims WHERE supplier_email = '". $supplier_email ."'" ;

        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        
        while ($row = $results->fetchObject()) {
            $i++;
        }   
        if ($i > 0) {
            $res['status'] = false;
            $res['message'] = '<li>Supplier Already Exists</li>';
            $res['info'] = $query;
            return $res;
        } else {

            $query = "INSERT INTO supplier_ims 
                        (
                            supplier_name, 
                            supplier_address, 
                            supplier_contact_no, 
                            supplier_email, 
                            supplier_status, 
                            supplier_datetime
                        ) 
                      VALUES 
                        (
                            '" . $supplier_name . "', 
                            '" . $supplier_address . "', 
                            '" . $supplier_contact_no . "',
                            '" . $supplier_email . "',
                            '" . $supplier_status . "',
                            " . $supplier_datetime . "
                        )";

            // prepare query
            $stmt = $this->conn->prepare($query);

            if ($this->debug) {
                var_dump($stmt);
                die;
            }

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Added Supplier!!";
                $res['info'] = $this->conn->lastInsertId();
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Supplier creation failed at API!!";
                $res['info'] = $query;
            }
            return $res;
        }
    }
    public function edit_supplier($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $supplier_name = "";
        $supplier_address = "";
        $supplier_contact_no = "";
        $supplier_email = "";
        $supplier_id = "";
        
        if (empty($requestData['supplier_id'])) {
            $errormsg .= " supplier_id is missing.";
            $status = false;
        } else {
            $supplier_id = htmlspecialchars(strip_tags($requestData['supplier_id']));
        }

        if (empty($requestData['supplier_name'])) {
            $errormsg .= " supplier_name is missing.";
            $status = false;
        } else {
            $supplier_name = htmlspecialchars(strip_tags($requestData['supplier_name']));
        }

        if (empty($requestData['supplier_address'])) {
            $errormsg .= " supplier_address is missing.";
            $status = false;
        } else {
            $supplier_address = htmlspecialchars(strip_tags($requestData['supplier_address']));
        }

        if (empty($requestData['supplier_contact_no'])) {
            $errormsg .= " supplier_contact_no is missing.";
            $status = false;
        } else {
            $supplier_contact_no = htmlspecialchars(strip_tags($requestData['supplier_contact_no']));
        }

        if (empty($requestData['supplier_email'])) {
            $errormsg .= " supplier_email is missing.";
            $status = false;
        } else {
            $supplier_email = htmlspecialchars(strip_tags($requestData['supplier_email']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "SELECT * FROM supplier_ims 
                    WHERE supplier_email = '". $supplier_email ."' 
                    AND supplier_id != '". $supplier_id ."'";

        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        
        while ($row = $results->fetchObject()) {
            $i++;
        }   
        if ($i > 0) {
            $res['status'] = false;
            $res['message'] = "<li>Supplier Already Exists</li>";
            $res['info'] = $query;
            return $res;
        } else {

            $query = "UPDATE supplier_ims 
                        SET supplier_name = '" . $supplier_name . "', 
                        supplier_address = '" . $supplier_address . "', 
                        supplier_contact_no = '" . $supplier_contact_no . "', 
                        supplier_email = '" . $supplier_email . "'
                        WHERE supplier_id = '" . $supplier_id . "'";

            // prepare query
            $stmt = $this->conn->prepare($query);

            if ($this->debug) {
                var_dump($stmt);
                die;
            }

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Updated Supplier!!";
                $res['info'] = $query;
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Supplier update failed at API!!";
                $res['info'] = $query;
            }
            return $res;
        }
    }
    public function delete_supplier($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $supplier_id = "";
        $supplier_status = "Disable";

        if (empty($requestData['supplier_id'])) {
            $errormsg .= " supplier_id is missing.";
            $status = false;
        } else {
            $supplier_id = htmlspecialchars(strip_tags($requestData['supplier_id']));
        }

        if (!empty($requestData['supplier_status'])) {
            $supplier_status = htmlspecialchars(strip_tags($requestData['supplier_status']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "UPDATE supplier_ims 
                    SET supplier_status = '" . $supplier_status . "'
                    WHERE supplier_id = '" . $supplier_id . "'";

        if ($this->debug) {
            var_dump($query);
        }

        $stmt = $this->conn->prepare($query);

        if ($this->debug) {
            var_dump($stmt);
            die;
        }

        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "[Inventory] Successfully Deleted Supplier!!";
            $res['info'] = $query;
        } else {
            $res['status'] = false;
            $res['message'] = "[Inventory] Supplier deletion failed at API!!";
            $res['info'] = $query;
        }
        return $res;
    
    }
    public function add_category($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $category_name = "";
        $category_status = 'Enable';
        $category_datetime = "NOW()";

        if (empty($requestData['category_name'])) {
            $errormsg .= " category_name is missing.";
            $status = false;
        } else {
            $category_name = htmlspecialchars(strip_tags($requestData['category_name']));
        }

        
        if (!empty($requestData['category_status'])) {
            $category_status = htmlspecialchars(strip_tags($requestData['category_status']));
        }

        if (!empty($requestData['category_datetime'])) {
            $category_datetime = htmlspecialchars(strip_tags($requestData['category_datetime']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "SELECT * FROM category_ims WHERE category_name = '" . $category_name . "'" ;

        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        
        while ($row = $results->fetchObject()) {
            $i++;
        }   
        if ($i > 0) {
            $res['status'] = false;
            $res['message'] = "<li>Category Name Already Exists</li>";
            $res['info'] = $query;
            return $res;
        } else {

            $query = "INSERT INTO category_ims 
                    (
                        category_name, 
                        category_status, 
                        category_datetime
                    ) 
                 VALUES 
                    (
                        '" . $category_name . "', 
                        '" . $category_status . "',  
                        " . $category_datetime . "
                    )";

            // prepare query
            $stmt = $this->conn->prepare($query);

            if ($this->debug) {
                var_dump($stmt);
                die;
            }

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Added Category!!";
                $res['info'] = $this->conn->lastInsertId();
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Category creation failed at API!!";
                $res['info'] = $query;
            }
            return $res;
        }
    }
    public function edit_category($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $category_id = "";
        $category_name = "";
        
        if (empty($requestData['category_id'])) {
            $errormsg .= " category_id is missing.";
            $status = false;
        } else {
            $category_id = htmlspecialchars(strip_tags($requestData['category_id']));
        }

        if (empty($requestData['category_name'])) {
            $errormsg .= " category_name is missing.";
            $status = false;
        } else {
            $category_name = htmlspecialchars(strip_tags($requestData['category_name']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "SELECT * FROM category_ims 
                    WHERE category_name = '". $category_name ."' 
                    AND category_id != '". $category_id ."'";

        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        
        while ($row = $results->fetchObject()) {
            $i++;
        }   
        if ($i > 0) {
            $res['status'] = false;
            $res['message'] = "<li>Category Name Already Exists</li>";
            $res['info'] = $query;
            return $res;
        } else {

            $query = "UPDATE category_ims 
                        SET category_name = '" . $category_name . "'
                        WHERE category_id = '" . $category_id . "'";

            // prepare query
            $stmt = $this->conn->prepare($query);

            if ($this->debug) {
                var_dump($stmt);
                die;
            }

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Updated Category!!";
                $res['info'] = $query;
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Category update failed at API!!";
                $res['info'] = $query;
            }
            return $res;
        }
    }
    public function delete_category($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $category_id = "";
        $category_status = "Disable";

        if (empty($requestData['category_id'])) {
            $errormsg .= " category_id is missing.";
            $status = false;
        } else {
            $category_id = htmlspecialchars(strip_tags($requestData['category_id']));
        }

        if (!empty($requestData['category_status'])) {
            $category_status = htmlspecialchars(strip_tags($requestData['category_status']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "UPDATE category_ims 
                    SET category_status = '" . $category_status . "'
                    WHERE category_id = '" . $category_id . "'";

        if ($this->debug) {
            var_dump($query);
        }

        $stmt = $this->conn->prepare($query);

        if ($this->debug) {
            var_dump($stmt);
            die;
        }

        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "[Inventory] Successfully Deleted Category!!";
            $res['info'] = $query;
        } else {
            $res['status'] = false;
            $res['message'] = "[Inventory] Category deletion failed at API!!";
            $res['info'] = $query;
        }
        return $res;
    
    }
    public function add_product($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $item_name = "";
        $item_manufactured_by = "";
        $item_category = "";
        $item_available_quantity = 0;
        $item_location_rack = "";
        $item_status = 'Enable';
        $item_add_datetime = "NOW()";
        $item_update_datetime = "NOW()";


        if (empty($requestData['item_name'])) {
            $errormsg .= " item_name is missing.";
            $status = false;
        } else {
            $item_name = htmlspecialchars(strip_tags($requestData['item_name']));
        }

        if (empty($requestData['item_manufactured_by'])) {
            $errormsg .= " item_manufactured_by is missing.";
            $status = false;
        } else {
            $item_manufactured_by = htmlspecialchars(strip_tags($requestData['item_manufactured_by']));
        }

        if (empty($requestData['item_category'])) {
            $errormsg .= " item_category is missing.";
            $status = false;
        } else {
            $item_category = htmlspecialchars(strip_tags($requestData['item_category']));
        }

        if (!empty($requestData['item_available_quantity'])) {
            $item_available_quantity = htmlspecialchars(strip_tags($requestData['item_available_quantity']));
        }

        if (!empty($requestData['item_location_rack'])) {
            $item_location_rack = htmlspecialchars(strip_tags($requestData['item_location_rack']));
        }

        if (!empty($requestData['item_status'])) {
            $item_status = htmlspecialchars(strip_tags($requestData['item_status']));
        }

        if (!empty($requestData['item_add_datetime'])) {
            $item_add_datetime = htmlspecialchars(strip_tags($requestData['item_add_datetime']));
        }

        if (!empty($requestData['item_update_datetime'])) {
            $item_update_datetime = htmlspecialchars(strip_tags($requestData['item_update_datetime']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "SELECT * FROM item_ims 
                    WHERE item_name = '" . $item_name . "' 
                    AND item_manufactured_by = '" . $item_manufactured_by . "'";

        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        
        while ($row = $results->fetchObject()) {
            $i++;
        }   
        if ($i > 0) {
            $res['status'] = false;
            $res['message'] = "<li>Product Already Exists</li>";
            $res['info'] = $query;
            return $res;
        } else {

            $query = "INSERT INTO item_ims 
                    (
                        item_name, 
                        item_manufactured_by, 
                        item_category,
                        item_available_quantity, 
                        item_location_rack, 
                        item_status, 
                        item_add_datetime, 
                        item_update_datetime
                    ) 
                 VALUES 
                    (
                        '" . $item_name . "', 
                        '" . $item_manufactured_by . "',  
                        '" . $item_category . "', 
                        " . $item_available_quantity . ",  
                        '" . $item_location_rack . "',  
                        '" . $item_status . "', 
                        " . $item_add_datetime . ",  
                        " . $item_update_datetime . "
                    )";

            // prepare query
            $stmt = $this->conn->prepare($query);

            if ($this->debug) {
                var_dump($stmt);
                die;
            }

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Added Product!!";
                $res['info'] = $this->conn->lastInsertId();
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Product creation failed at API!!";
                $res['info'] = $query;
            }
            return $res;
        }
    }
    public function edit_product($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $item_id = "";
        $item_name = "";
        $item_manufactured_by = "";
        $item_category = "";
        $item_location_rack = "";
        $item_update_datetime = "NOW()";


        if (empty($requestData['item_id'])) {
            $errormsg .= " item_id is missing.";
            $status = false;
        } else {
            $item_id = htmlspecialchars(strip_tags($requestData['item_id']));
        }

        if (empty($requestData['item_name'])) {
            $errormsg .= " item_name is missing.";
            $status = false;
        } else {
            $item_name = htmlspecialchars(strip_tags($requestData['item_name']));
        }

        if (empty($requestData['item_manufactured_by'])) {
            $errormsg .= " item_manufactured_by is missing.";
            $status = false;
        } else {
            $item_manufactured_by = htmlspecialchars(strip_tags($requestData['item_manufactured_by']));
        }

        if (empty($requestData['item_category'])) {
            $errormsg .= " item_category is missing.";
            $status = false;
        } else {
            $item_category = htmlspecialchars(strip_tags($requestData['item_category']));
        }

        if (!empty($requestData['item_location_rack'])) {
            $item_location_rack = htmlspecialchars(strip_tags($requestData['item_location_rack']));
        }
        
        if (!empty($requestData['item_update_datetime'])) {
            $item_update_datetime = htmlspecialchars(strip_tags($requestData['item_update_datetime']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "SELECT * FROM item_ims 
                    WHERE item_name = '". $item_name ."' 
                    AND item_manufactured_by = '". $item_manufactured_by ."'
                    AND item_id != '". $item_id ."'";

        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        
        while ($row = $results->fetchObject()) {
            $i++;
        }   
        if ($i > 0) {
            $res['status'] = false;
            $res['message'] = "<li>Product Name Already Exists</li>";
            $res['info'] = $query;
            return $res;
        } else {

            $query = "UPDATE item_ims 
                        SET item_name        = '". $item_name ."', 
                        item_manufactured_by = '". $item_manufactured_by ."', 
                        item_category        = '". $item_category ."', 
                        item_location_rack   = '". $item_location_rack ."', 
                        item_update_datetime = ". $item_update_datetime ." 
                      WHERE item_id          = '". $item_id ."'";

            // prepare query
            $stmt = $this->conn->prepare($query);

            if ($this->debug) {
                var_dump($stmt);
                die;
            }

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Updated Product!!";
                $res['info'] = $this->conn->lastInsertId();
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Product update failed at API!!";
                $res['info'] = $query;
            }
            return $res;
        }
    }
    public function delete_product($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $item_id = "";
        $item_status = "Disable";
        $item_update_datetime = "NOW()";


        if (empty($requestData['item_id'])) {
            $errormsg .= " item_id is missing.";
            $status = false;
        } else {
            $item_id = htmlspecialchars(strip_tags($requestData['item_id']));
        }

        if (!empty($requestData['item_status'])) {
            $item_status = htmlspecialchars(strip_tags($requestData['item_status']));
        }

        if (!empty($requestData['item_update_datetime'])) {
            $item_update_datetime = htmlspecialchars(strip_tags($requestData['item_update_datetime']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "UPDATE item_ims 
                    SET item_status = '" . $item_status . "', 
                    item_update_datetime = " . $item_update_datetime . "
                    WHERE item_id = '". $item_id ."'";

        if ($this->debug) {
            var_dump($query);
        }

        $stmt = $this->conn->prepare($query);

        if ($this->debug) {
            var_dump($stmt);
            die;
        }

        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "[Inventory] Successfully Deleted Product!!";
            $res['info'] = $this->conn->lastInsertId();
        } else {
            $res['status'] = false;
            $res['message'] = "[Inventory] Product deletion failed at API!!";
            $res['info'] = $query;
        }
        return $res;
    
    }
    public function add_tax($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $tax_name= "";
        $tax_percentage = "";
        $tax_status =  "Enable";
        $tax_added_on = "NOW()";
        $tax_updated_on = "NOW()";


        if (empty($requestData['tax_name'])) {
            $errormsg .= " tax_name is missing.";
            $status = false;
        } else {
            $tax_name = htmlspecialchars(strip_tags($requestData['tax_name']));
        }

        if (empty($requestData['tax_percentage'])) {
            $errormsg .= " tax_percentage is missing.";
            $status = false;
        } else {
            $tax_percentage = htmlspecialchars(strip_tags($requestData['tax_percentage']));
        }

        if (empty($requestData['tax_status'])) {
            $errormsg .= " tax_status is missing.";
            $status = false;
        } else {
            $tax_status = htmlspecialchars(strip_tags($requestData['tax_status']));
        }

        if (!empty($requestData['tax_added_on'])) {
            $tax_added_on = htmlspecialchars(strip_tags($requestData['tax_added_on']));
        }

        if (!empty($requestData['tax_updated_on'])) {
            $tax_updated_on = htmlspecialchars(strip_tags($requestData['tax_updated_on']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "SELECT * FROM tax_ims WHERE tax_name = '" .$tax_name . "'";

        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        
        while ($row = $results->fetchObject()) {
            $i++;
        }   
        if ($i > 0) {
            $res['status'] = false;
            $res['message'] = "<li>Tax Name Already Exists</li>";
            $res['info'] = $query;
            return $res;
        } else {

            $query = "INSERT INTO tax_ims 
                        (
                            tax_name, 
                            tax_percentage, 
                            tax_status, 
                            tax_added_on, 
                            tax_updated_on
                        ) 
                    VALUES 
                        (
                            '" . $tax_name . "', 
                            '" . $tax_percentage . "',
                            '" . $tax_status . "',
                            " . $tax_added_on . ",
                            " . $tax_updated_on . "
                        )";

            // prepare query
            $stmt = $this->conn->prepare($query);

            if ($this->debug) {
                var_dump($stmt);
                die;
            }

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Added Tax!!";
                $res['info'] = $this->conn->lastInsertId();
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Tax creation failed at API!!";
                $res['info'] = $query;
            }
            return $res;
        }
    }
    public function edit_tax($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $tax_id = "";
        $tax_name = "";
        $tax_percentage = "";
        $tax_updated_on = "NOW()";
        


        if (empty($requestData['tax_id'])) {
            $errormsg .= " tax_id is missing.";
            $status = false;
        } else {
            $tax_id = htmlspecialchars(strip_tags($requestData['tax_id']));
        }

        if (empty($requestData['tax_name'])) {
            $errormsg .= " tax_name is missing.";
            $status = false;
        } else {
            $tax_name = htmlspecialchars(strip_tags($requestData['tax_name']));
        }

        if (!empty($requestData['tax_percentage'])) {
            $tax_percentage = htmlspecialchars(strip_tags($requestData['tax_percentage']));
        }
        
        if (!empty($requestData['tax_updated_on'])) {
            $tax_updated_on = htmlspecialchars(strip_tags($requestData['tax_updated_on']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "SELECT * FROM tax_ims 
                    WHERE tax_name = '". $tax_name ."' 
                    AND tax_id != '". $tax_id ."'";

        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        
        while ($row = $results->fetchObject()) {
            $i++;
        }   
        if ($i > 0) {
            $res['status'] = false;
            $res['message'] = "<li>Product Name Already Exists</li>";
            $res['info'] = $query;
            return $res;
        } else {

            $query = "UPDATE tax_ims 
                        SET tax_name = '" . $tax_name . "', 
                        tax_percentage = '" .$tax_percentage . "', 
                        tax_updated_on = " .$tax_updated_on . " 
                        WHERE tax_id = '" . $tax_id . "'";

            // prepare query
            $stmt = $this->conn->prepare($query);

            if ($this->debug) {
                var_dump($stmt);
                die;
            }

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Updated Txt!!";
                $res['info'] = $this->conn->lastInsertId();
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Tax update failed at API!!";
                $res['info'] = $query;
            }
            return $res;
        }
    }
    public function delete_tax($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $tax_id = "";
        $tax_status = "Disable";
        
        if (empty($requestData['tax_id'])) {
            $errormsg .= " tax_id is missing.";
            $status = false;
        } else {
            $tax_id = htmlspecialchars(strip_tags($requestData['tax_id']));
        }

        if (!empty($requestData['tax_status'])) {
            $tax_status = htmlspecialchars(strip_tags($requestData['tax_status']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "UPDATE tax_ims 
                    SET tax_status = '" . $tax_status . "' 
                    WHERE tax_id = '" . $tax_id ."'";

        if ($this->debug) {
            var_dump($query);
        }

        $stmt = $this->conn->prepare($query);

        if ($this->debug) {
            var_dump($stmt);
            die;
        }

        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "[Inventory] Successfully Deleted Tax!!";
            $res['info'] = $this->conn->lastInsertId();
        } else {
            $res['status'] = false;
            $res['message'] = "[Inventory] Tax deletion failed at API!!";
            $res['info'] = $query;
        }
        return $res;
    
    }

    public function add_location_rack($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $location_rack_name = "";
        $location_rack_status= 'Enable';
        $location_rack_datetime = "NOW()";

        if (empty($requestData['location_rack_name'])) {
            $errormsg .= " location_rack_name is missing.";
            $status = false;
        } else {
            $location_rack_name = htmlspecialchars(strip_tags($requestData['location_rack_name']));
        }

        if (!empty($requestData['location_rack_status'])) {
            $location_rack_status = htmlspecialchars(strip_tags($requestData['location_rack_status']));
        }

        if (!empty($requestData['location_rack_datetime'])) {
            $location_rack_datetime = htmlspecialchars(strip_tags($requestData['location_rack_datetime']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "SELECT * FROM location_rack_ims WHERE location_rack_name = '". $location_rack_name ."'";

        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        
        while ($row = $results->fetchObject()) {
            $i++;
        }   
        if ($i > 0) {
            $res['status'] = false;
            $res['message'] = "<li>Tax Name Already Exists</li>";
            $res['info'] = $query;
            return $res;
        } else {

            $query = "INSERT INTO location_rack_ims 
                        (
                            location_rack_name, 
                            location_rack_status, 
                            location_rack_datetime
                        ) 
                     VALUES 
                        (
                            '" . $location_rack_name . "',
                            '" . $location_rack_status . "', 
                            " . $location_rack_datetime ."
                        )";

            // prepare query
            $stmt = $this->conn->prepare($query);

            if ($this->debug) {
                var_dump($stmt);
                die;
            }

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Added Location Rack!!";
                $res['info'] = $this->conn->lastInsertId();
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Location Rack creation failed at API!!";
                $res['info'] = $query;
            }
            return $res;
        }
    }
    public function edit_location_rack($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $location_rack_name = "";
        $location_rack_id = "";

        if (empty($requestData['location_rack_id'])) {
            $errormsg .= " location_rack_id is missing.";
            $status = false;
        } else {
            $location_rack_id = htmlspecialchars(strip_tags($requestData['location_rack_id']));
        }

        if (empty($requestData['location_rack_name'])) {
            $errormsg .= " location_rack_name is missing.";
            $status = false;
        } else {
            $location_rack_name = htmlspecialchars(strip_tags($requestData['location_rack_name']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "SELECT * FROM location_rack_ims 
                    WHERE location_rack_name = '". $location_rack_name ."' 
                    AND location_rack_id != '". $location_rack_id ."'";

        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        
        while ($row = $results->fetchObject()) {
            $i++;
        }   
        if ($i > 0) {
            $res['status'] = false;
            $res['message'] = "<li>Location Rack Name Already Exists</li>";
            $res['info'] = $query;
            return $res;
        } else {

            $query = "UPDATE location_rack_ims 
                        SET location_rack_name = '" . $location_rack_name . "'
                        WHERE location_rack_id = '" . $location_rack_id . "'";

            // prepare query
            $stmt = $this->conn->prepare($query);

            if ($this->debug) {
                var_dump($stmt);
                die;
            }

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Updated Location Rack!!";
                $res['info'] = $this->conn->lastInsertId();
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Location Rack update failed at API!!";
                $res['info'] = $query;
            }
            return $res;
        }
    }
    public function delete_location_rack($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $location_rack_id = "";
        $location_rack_status = "Disable";
        
        if (empty($requestData['location_rack_id'])) {
            $errormsg .= " location_rack_id is missing.";
            $status = false;
        } else {
            $location_rack_id = htmlspecialchars(strip_tags($requestData['location_rack_id']));
        }

        if (!empty($requestData['location_rack_status'])) {
            $location_rack_status = htmlspecialchars(strip_tags($requestData['location_rack_status']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "UPDATE location_rack_ims 
                    SET location_rack_status = '" . $location_rack_status . "'
                    WHERE location_rack_id = '" . $location_rack_id ."'";

        if ($this->debug) {
            var_dump($query);
        }

        $stmt = $this->conn->prepare($query);

        if ($this->debug) {
            var_dump($stmt);
            die;
        }

        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "[Inventory] Successfully Deleted Location Rack !!";
            $res['info'] = $this->conn->lastInsertId();
        } else {
            $res['status'] = false;
            $res['message'] = "[Inventory] Location Rack deletion failed at API!!";
            $res['info'] = $query;
        }
        return $res;
    
    }

    public function create_order($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        $errormsg = "";
        $status = true;
   
        $buyer_name = ""; 
        $order_total_amount = 0; 
        $order_created_by = "unknown"; 
        $order_status = "Enable"; 
        $order_added_on = "NOW()"; 
        $order_updated_on = "NOW()"; 
        $order_tax_name = ""; 
        $order_tax_percentage = 0;
        /*
        
       */

       if(empty($requestData['buyer_name'])) { 
            $errormsg .= " buyer_name is missing.";
            $status = false;
        }
        else{
            $buyer_name = htmlspecialchars(strip_tags($requestData['buyer_name']));
        }

        if(!empty($requestData['order_total_amount'])) {             
            $order_total_amount = htmlspecialchars(strip_tags($requestData['order_total_amount']));
        }

        if(!empty($requestData['order_created_by'])) {             
            $order_created_by = htmlspecialchars(strip_tags($requestData['order_created_by']));
        }

        if(!empty($requestData['order_status'])) {             
            $order_status = htmlspecialchars(strip_tags($requestData['order_status']));
        }

        if(!empty($requestData['order_added_on'])) {             
            $order_added_on = htmlspecialchars(strip_tags($requestData['order_added_on']));
        }

        if(!empty($requestData['order_updated_on'])) {             
            $order_updated_on = htmlspecialchars(strip_tags($requestData['order_updated_on']));
        }

        if(!empty($requestData['order_tax_name'])) {             
            $order_tax_name = htmlspecialchars(strip_tags($requestData['order_tax_name']));
        }

        if(!empty($requestData['order_tax_percentage'])) {             
            $order_tax_percentage = htmlspecialchars(strip_tags($requestData['order_tax_percentage']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }
        
        $query = "INSERT INTO order_ims 
                    (
                        buyer_name, 
                        order_total_amount, 
                        order_created_by, 
                        order_status, 
                        order_added_on, 
                        order_updated_on, 
                        order_tax_name, 
                        order_tax_percentage
                    ) 
                VALUES 
                    (
                         '" . $buyer_name . "',  
                          " . $order_total_amount . ",  
                         '" . $order_created_by . "',  
                         '" . $order_status . "',  
                          " . $order_added_on . ",  
                          " . $order_updated_on . ",  
                         '" . $order_tax_name . "',  
                         '" . $order_tax_percentage . "'
                    ) ";

        // prepare query
        $stmt = $this->conn->prepare($query);

        if($this->debug){ var_dump($stmt); die;}

        if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Added Order!!";
                $res['info'] = $this->conn->lastInsertId();
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Order creation failed at API!!";
                if ($this->debug) {
                    $res['info'] = $query;
                } else {
                    $res['info'] = $stmt;
                }
            }
       
        return $res;         
    }

    public function create_order_line($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        $errormsg = "";
        $status = true;
   
        $order_id = 0; 
        $item_id = 0; 
        $item_purchase_id = 0; 
        $item_quantity = 0; 
        $item_price = 0;         
        

       if(empty($requestData['order_id'])) { 
            $errormsg .= " order_id is missing.";
            $status = false;
        }
        else{
            $order_id = htmlspecialchars(strip_tags($requestData['order_id']));
        }
        
        if(empty($requestData['item_id'])) { 
            $errormsg .= " item_id is missing.";
            $status = false;
        }
        else{
            $item_id = htmlspecialchars(strip_tags($requestData['item_id']));
        }

        if(!empty($requestData['item_purchase_id'])) {             
            $item_purchase_id = htmlspecialchars(strip_tags($requestData['item_purchase_id']));
        }

        if(!empty($requestData['item_quantity'])) {             
            $item_quantity = htmlspecialchars(strip_tags($requestData['item_quantity']));
        }

        if(!empty($requestData['item_price'])) {             
            $item_price = htmlspecialchars(strip_tags($requestData['item_price']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }
        
        $query = "INSERT INTO order_item_ims 
                    (
                        order_id, 
                        item_id, 
                        item_purchase_id, 
                        item_quantity, 
                        item_price
                    ) 
                VALUES 
                    (
                         " . $order_id . ",  
                          " . $item_id . ",  
                          " . $item_purchase_id . ",  
                          " . $item_quantity . ",  
                          " . $item_price . "
                    ) ";

        // prepare query
        $stmt = $this->conn->prepare($query);

        if($this->debug){ var_dump($stmt); die;}

        if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Added Order Line!!";
                $res['info'] = $this->conn->lastInsertId();

                $query = "UPDATE item_purchase_ims 
                            SET available_quantity = available_quantity - ". $item_quantity ." 
                            WHERE item_purchase_id = ". $item_purchase_id  ;

                $stmt = $this->conn->prepare($query);
            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Added Order Line and updated item purchase record available quantity!!";
                $res['info'] = $order_id;

                $query = "UPDATE item_ims 
                            SET item_available_quantity = item_available_quantity - ". $item_quantity ." 
                            WHERE item_id = ". $item_id ;

                $stmt = $this->conn->prepare($query);

                if ($stmt->execute()) {
                    $res['status'] = true;
                    $res['message'] = "[Inventory] Successfully Added Order Line and updated item purchase & item available quantities!!";
                    $res['info'] = $order_id;
                }
                else {
                    $res['status'] = false;
                    $res['message'] = "[Inventory] Successfully Added Order Line and updated item purchase available quantities, but failed to update item available quantity!!";
                    $res['info'] = $query;
                }
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Successfully Added Order Line but failed to update item purchase and item available quantity!!";
                $res['info'] = $query;
            }

        } else {
            $res['status'] = false;
            $res['message'] = "[Inventory] Order line creation failed at API!!";
            if ($this->debug) {
                $res['info'] = $query;
            } else {
                $res['info'] = $stmt;
            }
        }
       
        return $res;         
    }
    public function remove_order_line($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $order_item_id = 0;

        if (empty($requestData['order_item_id'])) {
            $errormsg .= " order_item_id is missing.";
            $status = false;
        } else {
            $order_item_id = htmlspecialchars(strip_tags($requestData['order_item_id']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        $query = "SELECT * FROM order_item_ims 
                    WHERE order_item_id = " . $order_item_id . " ";

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

        $i = 0;
        $result = array();
        foreach ($results as $item_row) {
            $i++;

            $item_id = $item_row["item_id"];
            $item_purchase_id = $item_row["item_purchase_id"];
            $item_quantity = $item_row["item_quantity"];
            $item_price = $item_row["item_price"];
            $order_id = $item_row["order_id"];
            $request = array('order_id' => $order_id);
            $tax_per_arr = explode(',', $this->Get_order_tax_percentage($request)['order_tax_percentage']);
            $item_amt_with_tax = 0;
            $item_amt_without_tax = $item_quantity * $item_price;
            $tax_amt = 0;

            for ($i = 0; $i < count($tax_per_arr); $i++) {
                $tax_amt = floatval($tax_amt) + floatval($item_amt_without_tax * $tax_per_arr[$i] / 100);
            }
            $item_amt_with_tax = floatval($item_amt_without_tax) + floatval($tax_amt);

            if ($this->debug) {
                echo "\n item_row: ";
                var_dump($item_row);
                echo "\n item_id: ";
                var_dump($item_id);
                echo "\n item_quantity: ";
                var_dump($item_quantity);
                echo "\n tax_per_arr: ";
                var_dump($tax_per_arr);
                echo "\n item_amt_without_tax; ";
                var_dump($item_amt_without_tax);
                echo "\n tax_amt: ";
                var_dump($tax_amt);
                echo "\n item_amt_with_tax: ";
                var_dump($item_amt_with_tax);
                echo "\n ====end===";
            }

            $query = "DELETE FROM order_item_ims WHERE order_item_id = " . $order_item_id;
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Deleted Order Line!!";
                $res['info'] = $order_item_id;

                $sub_query = array();
                $sub_query[0] = "UPDATE order_ims 
                                SET order_total_amount = order_total_amount - " . $item_amt_with_tax . " 
                                WHERE order_id = " . $order_id;

                $sub_query[1] = "UPDATE item_purchase_ims 
                                SET available_quantity = available_quantity + " . $item_quantity . " 
                                WHERE item_purchase_id = " . $item_purchase_id;

                $sub_query[2] = "UPDATE item_ims 
                                SET item_available_quantity = item_available_quantity + " . $item_quantity . " 
                                WHERE item_id = " . $item_id;

                for ($i = 0; $i < sizeof($sub_query); $i++) {
                    $stmt = $this->conn->prepare($sub_query[$i]);

                    if (!$stmt->execute()) {
                        $res['status'] = false;
                        $res['message'] = "[Inventory] Updating Item Available Quantity Failed, but order successfully deleted at API!!";
                        $res['info'] = $stmt;
                        break;
                    }
                }

            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Order line removal failed at API!!";
                if ($this->debug) {
                    $res['info'] = $query;
                } else {
                    $res['info'] = $stmt;
                }
            }
        }
        return $res;
    }
    public function update_order($requestData)
    {
        //>>Declarations
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $buyer_name = "";
        $order_total_amount = 0;
        $order_updated_on = 'NOW()';
        $order_tax_name = "";
        $order_tax_percentage = 0;
        $order_id = 0;
        $item_id = array();
        $item_purchase_id = array();
        $item_quantity = array();
        $item_price = array();

        if($this->debug){
            echo "\n Passed data: ";
            var_dump($requestData);
        }

        if (empty($requestData['order_id'])) {
            $errormsg .= " order_id is missing.";
            $status = false;
        } else {
            $order_id = htmlspecialchars(strip_tags($requestData['order_id']));
        }

        if (empty($requestData['item_id'])) {
            $errormsg .= " item_id is missing.";
            $status = false;
        } else {                
                $item_id = json_decode($requestData['item_id'], true);
        }
 
        if (empty($requestData['item_purchase_id'])) {
            $errormsg .= " item_purchase_id is missing.";
            $status = false;
        } else {
            $item_purchase_id  = json_decode($requestData['item_purchase_id'], true);
        }

        if (empty($requestData['buyer_name'])) {
            $errormsg .= " buyer_name is missing.";
            $status = false;
        } else {
            $buyer_name = htmlspecialchars(strip_tags($requestData['buyer_name']));
        }

        if (!empty($requestData['order_total_amount'])) {
            $order_total_amount = htmlspecialchars(strip_tags($requestData['order_total_amount']));
        }

        if (!empty($requestData['order_updated_on'])) {
            $order_updated_on = htmlspecialchars(strip_tags($requestData['order_updated_on']));
        }

        if (!empty($requestData['order_tax_name'])) {
            $order_tax_name = htmlspecialchars(strip_tags($requestData['order_tax_name']));
        }

        if (!empty($requestData['order_tax_percentage'])) {
            $order_tax_percentage = htmlspecialchars(strip_tags($requestData['order_tax_percentage']));
        }

        if (!empty($requestData['item_quantity'])) {
            $item_quantity = json_decode($requestData['item_quantity'], true);
        }

        if (!empty($requestData['item_price'])) {
            $item_price = json_decode($requestData['item_price'], true);
        }

        if($this->debug ){
            echo "\n item Ids: ";
            var_dump($item_id);
            echo "\n item_purchase_ids: ";
            var_dump($item_purchase_id);
            echo "\n item_quantity: ";
            var_dump($item_quantity);
            echo "\n item_price: ";
            var_dump($item_price);            
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }
        //<< endof declarations
        // Update order header
        $query = "UPDATE order_ims 
                    SET buyer_name = '" . $buyer_name . "', 
                    order_total_amount = " . $order_total_amount . ", 
                    order_updated_on = " . $order_updated_on . ", 
                    order_tax_name = '" . $order_tax_name . "', 
                    order_tax_percentage = '" . $order_tax_percentage . "' 
                    WHERE order_id = " . $order_id;

        $stmt = $this->conn->prepare($query);

        if($this->debug ){
            echo "\n Order IMS update statement: ";
            var_dump($stmt);
        }

        // if order header successfully updated
        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "[Inventory] Successfully Updated Order!!";
            $res['info'] = $order_id;
            if($this->debug ){
                echo "\n item Ids: ";
                var_dump($item_id);
            }
            //get records for each line item
            for ($i = 0; $i < count($item_id); $i++) {
                $query = "SELECT * FROM order_item_ims 
                            WHERE order_id = '" . $order_id . "' 
                            AND item_id = '" . $item_id[$i] . "' 
                            AND item_purchase_id = '" . $item_purchase_id[$i] . "'";

                $order_item_result = $this->conn->query($query, MYSQLI_USE_RESULT);
                if($this->debug ){
                    echo "\n Order lines found: ";
                    var_dump($order_item_result);
                }
                
                //and check if the quantity was changed in any line
                foreach ($order_item_result as $order_item_row) {
                    
                    $itemid = $order_item_row["item_id"];
                    $itempurchaseid = $order_item_row["item_purchase_id"];
                    $itemquantity = $order_item_row["item_quantity"];
                    $medicineprice = $order_item_row["item_price"];

                    //If the quantity was actually changed, modify the line records
                    if ($itemquantity != $item_quantity[$i]) {
                        $query = " UPDATE order_item_ims 
                                    SET item_quantity = " . $item_quantity[$i] . "
                                    WHERE order_item_id = " . $order_item_row['order_item_id'];

                        $stmt = $this->conn->prepare($query);

                        if($this->debug ){
                            echo "\n order item updated statement: ";
                            var_dump($stmt);
                        }
                        if ($stmt->execute()) {
                            $res['status'] = true;
                            $res['message'] = "[Inventory] Successfully Updated Order Item table!!";
                            $res['info'] = $order_id;

                            $final_update_qty = 0;
                            $sub_query = array();
                            //changed the purchase and item available quantity also, if the line quantity was changed
                            if ($itemquantity > $item_quantity[$i]) {
                                $final_update_qty = $itemquantity - $item_quantity[$i];


                                $sub_query[0] = "UPDATE item_purchase_ims 
                                                    SET available_quantity = available_quantity + " . $final_update_qty . " 
                                                    WHERE item_purchase_id = '" . $item_purchase_id[$i] . "'";

                                $sub_query[1] = "UPDATE item_ims 
                                                    SET item_available_quantity = item_available_quantity + " . $final_update_qty . " 
                                                    WHERE item_id = '" . $item_id[$i] . "'";
                            } else {
                                $final_update_qty = $item_quantity[$i] - $itemquantity;

                                $sub_query[0] = " UPDATE item_purchase_ims 
                                                    SET available_quantity = available_quantity - " . $final_update_qty . " 
                                                    WHERE item_purchase_id = '" . $item_purchase_id[$i] . "'";


                                $sub_query[1] = " UPDATE item_ims 
                                                    SET item_available_quantity = item_available_quantity - " . $final_update_qty . " 
                                                    WHERE item_id = '" . $item_id[$i] . "'";
                            }
                            for ($j = 0; $j < sizeof($sub_query); $j++) {
                                $stmt = $this->conn->prepare($sub_query[$j]);

                                if ($this->debug) {
                                    echo "\n item and purchase quantity update statement: ";
                                    var_dump($stmt);}

                                if (!$stmt->execute()) {
                                    $res['status'] = false;
                                    $res['message'] = "[Inventory] Updating Order quantities on item and item purchase Failed, but order and lines successfully updated at API!!";
                                    $res['info'] = $stmt;
                                    if($this->debug){
                                        echo "\n failure occured: ";
                                        var_dump($res);
                                    }
                                    break;
                                }
                            }

                        } else {
                            $res['status'] = false;
                            $res['message'] = "[Inventory] Successfully Updated Order table, but order item table failed to update!!";
                            $res['info'] = $stmt;
                            if($this->debug){
                                echo "\n failure occured: ";
                                var_dump($res);
                            }
                        }
                    }
                }
            }
        } else {
            $res['status'] = false;
            $res['message'] = "[Inventory] Failed to Updated Order table and didn't try rest of the dependent talbles!!";
            $res['info'] = $stmt;
            if($this->debug){
                echo "\n failure occured: ";
                var_dump($res);
            }
        }
        return $res;
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
    public function delete_order($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $order_id = "";
        
        if (empty($requestData['order_id'])) {
            $errormsg .= " order_id is missing.";
            $status = false;
        } else {
            $order_id = htmlspecialchars(strip_tags($requestData['order_id']));
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        //get order items
        $query = "SELECT * FROM order_item_ims WHERE order_id = ". $order_id ;

        
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        if($this->debug) {var_dump($results);}

        $i=0;
        $sub_query = array();
        foreach($results as $item_row)
		{   
            $i++;         
            $sub_query[0] = "UPDATE item_purchase_ims 
                        SET available_quantity = available_quantity + ". $item_row['item_quantity'] ." 
                        WHERE item_purchase_id = '". $item_row['item_purchase_id'] ."'";

            $sub_query[1] = "UPDATE item_ims 
                        SET item_available_quantity = item_available_quantity + ". $item_row['item_quantity'] ." 
                        WHERE item_id = '". $item_row['item_id'] ."'";

            for ($j = 0; $j < sizeof($sub_query); $j++) {
                $stmt = $this->conn->prepare($sub_query[$j]);

                if ($this->debug) {var_dump($stmt);}

                if (!$stmt->execute()) {
                    $res['status'] = false;
                    $res['message'] = "[Inventory] Updating Order quantities on item and item purchase Failed at API!!";
                    $res['info'] = $stmt;
                    return $res;
                    break;
                }
            }
		}
        
        $sub_query2 = array();
        $sub_query2[0] = "DELETE FROM order_item_ims WHERE order_id = '".$order_id."'";
        $sub_query2[1] = "DELETE FROM order_ims WHERE order_id = '".$order_id."'";
        
        for ($k = 0; $k < sizeof($sub_query2); $k++) {
            $stmt = $this->conn->prepare($sub_query2[$k]);

            if ($this->debug) {var_dump($stmt);}

            if (!$stmt->execute()) {
                $res['status'] = false;
                $res['message'] = "[Inventory] Deleting Order or order item Failed at API!!";
                $res['info'] = $stmt;
                return $res;
                break;
            } 
        }
        $res['status'] = true;
        $res['message'] = "[Inventory] Order Deleted Successfully!!";
        $res['info'] = $order_id;
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

        $item_purchase_status = "";
        $item_purchase_id = ""; //  $item_purchase_id
        $item_id = "";
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

        if (empty($requestData['item_purchase_status'])) {
            $errormsg .= " item_purchase_status is missing.";
            $status = false;
        } else {
            $item_purchase_status = htmlspecialchars(strip_tags($requestData['item_purchase_status']));
        }


        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }

        //check if purchase qty was changed
        $tmpRequest = array('item_purchase_id' => $item_purchase_id);
        $tmpResult = $this->Get_item_purchase_qty($tmpRequest);
        if ($this->debug) {
            echo "after getting the original item purchase quantity. Results: ";
            var_dump($tmpResult);
        }
        foreach ($tmpResult as $temp_row) {
            $original_item_purchase_qty = $tmpResult['item_purchase_qty'];
        }

        unset($tmpRequest);
        unset($tmpResult);

        if ($this->debug) {
            echo "<br>original quantity: ", $original_item_purchase_qty;
        }

        $query = "UPDATE item_purchase_ims         
                  SET                     
                    item_purchase_status = '" . $item_purchase_status . "'                    
                    WHERE item_purchase_id = " . $item_purchase_id;

        // prepare query
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {

            if ($item_purchase_status == 'Disable') {
                $iQuery = " UPDATE item_ims 
                                SET item_available_quantity = item_available_quantity - " . $original_item_purchase_qty . " 
                                WHERE item_id = '" . $item_id . "'";
            } else {
                $iQuery = " UPDATE item_ims 
                                SET item_available_quantity = item_available_quantity + " . $original_item_purchase_qty . " 
                                WHERE item_id = '" . $item_id . "'";
            }

            $stmt = $this->conn->prepare($iQuery);
            if ($stmt->execute()) {
                $res['status'] = true;
                $res['message'] = "[Inventory] Successfully Deleted Purchase!!";
                $res['info'] = $item_id;
            } else {
                $res['status'] = false;
                $res['message'] = "[Inventory] Updating Item Available Quantity Failed, but purchase successfully deleted at API!!";
                if ($this->debug) {
                    $res['info'] = $query;
                } else {
                    $res['info'] = $stmt;
                }
            }
        } 
        else {
            $res['status'] = false;
            $res['message'] = "[Inventory] Deleting Purchase Failed at API!!";
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
