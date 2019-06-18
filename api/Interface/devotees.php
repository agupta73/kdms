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
                    case "KEY": //Devotee key supplied
                            return $this->getDetails(urldecode($requestData['key']));                    
                    break;

                    case "SET": //set query, like devotee without photo
                            return $this->searchDevotee($requestData['key']);
                    break;
                
                    case "CUS": //Custom query
                            return $this->searchDevotee($requestData['key']);
                    break;
                       
                    case "iSET": //set query, like devotee without photo
                            return $this->iSearchDevotee($requestData['key']);
                    break;
                
                    case "PCD": //Print Queue 
                            return $this->getDevoteeDetailsForPrint($requestData['key']);
                    break;
                
                    case "DAD": //Devotee Amenity Details 
                            return $this->getDevoteeAmenityDetails($requestData['key']);
                    break;
                
                    case "DYN": //Dynamic search
                            return $this->dynamicSearchDevotee($requestData['key']);
                    break;
                
                    case "AOD": //Accommodation Occupier Devotees  
                            return $this->getDevoteesForAccommodation($requestData['key']);
                    break;
                
                    case "ADS": //Assigned Devotees to Seva
                            return $this->getDevoteesForSeva($requestData['key']);
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
        
        $query = "SELECT " .
                    "d.* " .
                    ", ds.Seva_ID " .
                    ", did.Devotee_ID_Image, did.Devotee_ID_XML " .
                    ", did.Devotee_ID_Type as DID_Devotee_ID_Type " .
                    ", dp.Photo_type, dp.Devotee_Photo, da.Accomodation_Key " .
                 "from " .
                    "Devotee d " .
                    "left outer join Devotee_ID did on d.Devotee_Key=did.Devotee_Key " .
                    "left outer join Devotee_Photo dp on d.Devotee_Key=dp.Devotee_Key " .
                    "left outer join Devotee_Accomodation da on d.Devotee_key=da.Devotee_Key AND da.accomodation_year = YEAR(NOW()) AND Accomodation_Status = 'Allocated'  " .
                    "left outer join Devotee_Seva ds on d.Devotee_key=ds.Devotee_Key AND ds.seva_year = YEAR(NOW()) AND Seva_Status = 'Assigned'  " .
                 "where " .
                    " d.Devotee_Key = '" . $devotee_key . "' ORDER BY da.Devotee_Accomodation_update_Date_Time Desc LIMIT 1";
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $DevoteeDetails = array();
        
        if(!empty($row = $results->fetchObject())){
            $row->{'Devotee_Photo'} = base64_encode($row->{'Devotee_Photo'});
            $row->{'Devotee_ID_Image'} = base64_encode($row->{'Devotee_ID_Image'});
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
       
        $query = "select " .
                    "d.devotee_key, CONCAT(d.devotee_first_name, ' ', d.devotee_last_name) as Devotee_Name " .
                    ", d.devotee_station, d.devotee_cell_phone_number " .
                    ", did.Devotee_ID_Image " .
                    ", dp.Devotee_Photo ".
                 "from " .
                    " Devotee d ".
                    " left outer join Devotee_ID did on d.Devotee_Key=did.Devotee_Key " .
                    " left outer join Devotee_Photo dp on d.Devotee_Key=dp.Devotee_Key " .
                    " left outer join Devotee_Accomodation da on d.Devotee_Key=da.Devotee_key  " .
                        " AND da.Accomodation_year = YEAR(NOW()) AND da.Accomodation_Status = 'Allocated' ";
                
        switch ($requestData){
            case "PWD": //Photo without Devotee Details                   
                $query = $query . 
                            " WHERE  " .
                                " (d.Devotee_First_Name is null OR d.Devotee_Last_Name is null)  " .
                              "AND  " .
                                "(did.Devotee_ID_Image is not null OR dp.Devotee_Photo is not null)  " .
                                "ORDER BY d.Devotee_Record_update_date_time Desc  LIMIT 50";
                    
                break;

            case "DWP": //Devotee records without Photo or ID
                $query = $query . 
                            " WHERE " .
                                "(d.Devotee_First_Name is not null OR d.Devotee_Last_Name is NOT null)  " .
                              "AND  " .
                                "(did.Devotee_ID_Image is null OR dp.Devotee_Photo is  null)  " .
                                "ORDER BY d.Devotee_Record_update_date_time Desc  LIMIT 50";
                break;
            
            case "CTP": //Card print queue
                $query = $query .
                            " LEFT OUTER JOIN Card_Print_Log cpl on d.Devotee_Key = cpl.Devotee_Key "
                          . " WHERE cpl.Print_Status = 'A'  ORDER BY d.Devotee_Record_update_date_time Desc  LIMIT 50";
                
                break;
            
//            case "PCD": //Print Cards
//                $query = $query .
//                            " LEFT OUTER JOIN accommodation_master acm on da.accomodation_key = acm.accomodation_key "
//                          . " WHERE d.devotee_key in (" . $requestData . ")  ORDER BY d.Devotee_Record_update_date_time Desc";
//                
//                break;
                
            default : //Search based on user supplied search criteria
                $query = $query .
                            " WHERE " . $this->prepareSearchClause($requestData) . " ORDER BY d.Devotee_Record_update_date_time Desc  LIMIT 50"; 
                break;
        }
        
        //var_dump($query);die;
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $devoteeSearchResult = array();
        $i = 0;
        while($row = $results->fetchObject()){
            $row->{'Devotee_Photo'} = base64_encode($row->{'Devotee_Photo'});
            $row->{'Devotee_ID_Image'} = base64_encode($row->{'Devotee_ID_Image'});
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
    
    private function iSearchDevotee($requestData){
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
       
        $query = "select " .
                    "d.devotee_key, d.devotee_first_name, ' ', d.devotee_last_name " .
                    ", d.devotee_station, d.devotee_cell_phone_number, d.devotee_type, d.devotee_id_type " .
                    ", d.devotee_id_number, d.devotee_status, d.devotee_remarks " .
                    ", da.accomodation_key, am.accomodation_name " .
                    ", did.Devotee_ID_Image " .
                    ", dp.Devotee_Photo ".
                 "from " .
                    " Devotee d ".
                    " left outer join Devotee_ID did on d.Devotee_Key=did.Devotee_Key " .
                    " left outer join Devotee_Photo dp on d.Devotee_Key=dp.Devotee_Key " .
                    " left outer join Devotee_Accomodation da on d.Devotee_Key=da.Devotee_key  " .
                        " AND da.Accomodation_year = YEAR(NOW()) AND da.Accomodation_Status = 'Allocated' " .
                    " left outer join Accommodation_master am on da.accomodation_key=am.accomodation_key ";
                
                
        switch ($requestData){
            case "PWD": //Photo without Devotee Details                   
                $query = $query . 
                            " WHERE  " .
                                " (d.Devotee_First_Name is null OR d.Devotee_Last_Name is null)  " .
                              "AND  " .
                                "(did.Devotee_ID_Image is not null OR dp.Devotee_Photo is not null)  " .
                                "ORDER BY d.Devotee_Record_update_date_time Desc  LIMIT 50";
                    
                break;

            case "DWP": //Devotee records without Photo or ID
                $query = $query . 
                            " WHERE " .
                                "(d.Devotee_First_Name is not null OR d.Devotee_Last_Name is NOT null)  " .
                              "AND  " .
                                "(did.Devotee_ID_Image is null OR dp.Devotee_Photo is  null)  " .
                                "ORDER BY d.Devotee_Record_update_date_time Desc  LIMIT 50";
                break;
            
            case "CTP": //Card print queue
                $query = $query .
                            " LEFT OUTER JOIN Card_Print_Log cpl on d.Devotee_Key = cpl.Devotee_Key "
                          . " WHERE cpl.Print_Status = 'A'  ORDER BY d.Devotee_Record_update_date_time Desc  LIMIT 50";
                
                break;
            
//            case "PCD": //Print Cards
//                $query = $query .
//                            " LEFT OUTER JOIN accommodation_master acm on da.accomodation_key = acm.accomodation_key "
//                          . " WHERE d.devotee_key in (" . $requestData . ")  ORDER BY d.Devotee_Record_update_date_time Desc";
//                
//                break;
                
            default : //Search based on user supplied search criteria
                $query = $query .
                            " WHERE " . $this->prepareSearchClause($requestData) . " ORDER BY d.Devotee_Record_update_date_time Desc  LIMIT 50"; 
                break;
        }
        
        //var_dump($query);die;
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $devoteeSearchResult = array();
        $i = 0;
        while($row = $results->fetchObject()){
            $row->{'Devotee_Photo'} = base64_encode($row->{'Devotee_Photo'});
            $row->{'Devotee_ID_Image'} = base64_encode($row->{'Devotee_ID_Image'});
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
    
    private function prepareSearchClause($requestData) {
        $searchClause = "";
        $subClauses = "";
        $subKey = "";
        $subValue = "";

        foreach(explode(",", $requestData) as $subClauses){
                list($subKey, $subValue) = explode("=", $subClauses);
                switch ($subKey) {
                    // First Name
                    case "devotee_first_name":
                    case "first_name":
                    case "first name":
                    case "First Name":
                    case "FirstName":
                        $searchClause = $searchClause . "d.devotee_first_name like '%" . $subValue . "%' AND ";
                        break;
                    
                    // Last Name
                    case "devotee_last_name" :
                    case "last_name" :
                    case "last name" :
                    case "Last Name" :
                    case "LastName" :
                        $searchClause = $searchClause . "d.devotee_last_name like '%" . $subValue . "%' AND ";
                        break;
                    
                    // Station
                    case "devotee_station" :
                    case "Station" :
                    case "Devotee Station" :
                    case "DevoteeStation" :
                        $searchClause = $searchClause . "d.devotee_station = '" . $subValue . "' AND ";
                        break;
                    
                    // Cell Phone Number
                    case "devotee_cell_phone_number" :
                    case "cell phone number" :
                    case "devotee cell phone number" :
                    case "Cell Phone Number" :
                    case "Devotee Cell Phone Number" :
                        $searchClause = $searchClause . "d.devotee_cell_phone_number like '%" . $subValue . "%' AND ";
                        break;
                    
                    // Remarks
                    case "devotee_remarks" :
                    case "remarks" :
                    case "devotee_remark" :
                    case "remark" :
                    case "Devotee Remark" :
                        $searchClause = $searchClause . "d.devotee_remarks like '%" . $subValue . "%' AND ";
                        break;                    
                    
                    // ID Number
                    case "devotee_id_number" :
                    case "Devotee_ID_Number" :
                    case "id_number" :
                    case "ID_Number" :
                    case "Devotee ID Number" :
                        $searchClause = $searchClause . "d.devotee_id_number like '%" . $subValue . "%' AND ";
                        break; 
                    
                    // Accommodation
                    case "devotee_accommodation" :
                    case "devotee_accommodation_key" :
                    case "Devotee Accommodation Key" :
                    case "DevoteeAccommodationKey" :
                    case "Accommodation" :
                    case "accommodation" :                        
                    case "Accommodation Key" :  
                    case "devotee_accomodation" :
                    case "devotee_accomodation_key" :
                    case "Devotee Accomodation Key" :
                    case "DevoteeAccomodationKey" :
                    case "Accomodation" :
                    case "accomodation" :                        
                    case "Accomodation Key" :                          
                        $searchClause = $searchClause . "da.accomodation_key = '" . $subValue . "' AND ";
                        break;
                }           
        }
        
        $searchClause = substr($searchClause, 0, strlen($searchClause)-5);
        //var_dump($searchClause);
        //$searchClause = $requestData;
        return $searchClause;
    }

    private function dynamicSearchDevotee($requestData) {
    
        try {
                $query = "SELECT * FROM `Devotee` WHERE "
                        . "`Devotee_First_Name` like :requestData OR "
                        . "`Devotee_Last_Name` like :requestData OR "
                        . "`Devotee_Station` like :requestData OR "
                        . "`Devotee_Cell_Phone_Number` like :requestData";

                $stmt = $this->conn->prepare($query);
                $val = "%$requestData%";
                $stmt->bindParam(':requestData', $val , PDO::PARAM_STR);
                $stmt->execute();

                $Count = $stmt->rowCount();
                //echo " Total Records Count : $Count .<br>" ;

                $result ="" ;
                if ($Count  > 0){
                            while($data=$stmt->fetch(PDO::FETCH_ASSOC)) {
                               $result = $result .'<a href="addDevoteeI.php?devotee_key='.$data['Devotee_Key'].'"><div class="search-result">'.$data['Devotee_First_Name'].' '.$data['Devotee_Last_Name'].' - ('.$data['Devotee_Station'].') - '.$data['Devotee_Cell_Phone_Number'].'</div></a>';
                            }
                        return $result ;
                    }
                }
                catch (PDOException $e) {
                        echo 'Connection failed: ' . $e->getMessage();
                }
    }
    
    private function getDevoteeDetailsForPrint($requestData){
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        if (empty($requestData)) {
            $errormsg .= "Devotee keys for printing not supplied.";
            $status = false;
        }
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
       
        $query = "select " .
                    "d.devotee_key, devotee_first_name, d.devotee_last_name " .
                    ", d.devotee_station, d.devotee_cell_phone_number " .
                    ", acm.accomodation_name " .
                    ", dp.Devotee_Photo ".
                 "from " .
                    " Devotee d ".
                    " left outer join Devotee_ID did on d.Devotee_Key=did.Devotee_Key " .
                    " left outer join Devotee_Photo dp on d.Devotee_Key=dp.Devotee_Key " .
                    " left outer join Devotee_Accomodation da on d.Devotee_Key=da.Devotee_key  " .
                        " AND da.Accomodation_year = YEAR(NOW()) AND da.Accomodation_Status = 'Allocated' " .
                    " left outer join Accommodation_Master acm on da.accomodation_key = acm.accomodation_key " .
                 "where " .
                    "d.devotee_key in (" . $requestData . ") ORDER BY d.Devotee_Record_update_date_time Desc" ;
                
        
           
        //var_dump($query);die;
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $devoteeSearchResult = array();
        $i = 0;
        while($row = $results->fetchObject()){
            $row->{'Devotee_Photo'} = base64_encode($row->{'Devotee_Photo'});
            
            $devoteeSearchResult[]=$row;
            $i = $i+1;
        }
        //var_dump($devoteeSearchResult);die;
        if($i==0){
            $devoteeSearchResult['status'] = false;
            $devoteeSearchResult['message'] = "No record found!";
            $devoteeSearchResult['info'] = $results;
        }
        
        return $devoteeSearchResult;
    }
    
    private function getDevoteesForAccommodation($requestData){
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        if (empty($requestData)) {
            $errormsg .= "Accommodation keys not supplied.";
            $status = false;
        }
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
       
        $query = "select " .
                    "d.devotee_key, devotee_first_name, d.devotee_last_name, CONCAT(d.devotee_first_name, ' ', d.devotee_last_name) as Devotee_Name " .
                    ", d.devotee_station, d.devotee_cell_phone_number " .
                    ", acm.accomodation_name " .
                    ", did.Devotee_ID_Image " .
                    ", dp.Devotee_Photo ".
                 "from " .
                    " Devotee d ".
                    " left outer join Devotee_ID did on d.Devotee_Key=did.Devotee_Key " .
                    " left outer join Devotee_Photo dp on d.Devotee_Key=dp.Devotee_Key " .
                    " left outer join Devotee_Accomodation da on d.Devotee_Key=da.Devotee_key  " .
                        " AND da.Accomodation_year = YEAR(NOW()) AND da.Accomodation_Status = 'Allocated' " .
                    " left outer join Accommodation_Master acm on da.accomodation_key = acm.accomodation_key " .
                 "where " .
                    "da.Accomodation_key = '" . $requestData . "' ORDER BY da.Devotee_Accomodation_update_date_time Desc" ;
                
        
           
        //var_dump($query);die;
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $devoteeSearchResult = array();
        $i = 0;
        while($row = $results->fetchObject()){
            $row->{'Devotee_Photo'} = base64_encode($row->{'Devotee_Photo'});
            $row->{'Devotee_ID_Image'} = base64_encode($row->{'Devotee_ID_Image'});
            $devoteeSearchResult[]=$row;
            $i = $i+1;
        }
        //var_dump($devoteeSearchResult);die;
        if($i==0){
            $devoteeSearchResult['status'] = false;
            $devoteeSearchResult['message'] = "No record found!";
            $devoteeSearchResult['info'] = $results;
        }
        
        return $devoteeSearchResult;
    }
    
    private function getDevoteesForSeva($requestData){
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        if (empty($requestData)) {
            $errormsg .= "Seva id not supplied.";
            $status = false;
        }
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
       
        $query = "select " .
                    "d.devotee_key, devotee_first_name, d.devotee_last_name, CONCAT(d.devotee_first_name, ' ', d.devotee_last_name) as Devotee_Name " .
                    ", d.devotee_station, d.devotee_cell_phone_number " .
                    ", did.Devotee_ID_Image " .
                    ", dp.Devotee_Photo ".
                 "from " .
                    " Devotee d ".
                    " left outer join Devotee_ID did on d.Devotee_Key=did.Devotee_Key " .
                    " left outer join Devotee_Photo dp on d.Devotee_Key=dp.Devotee_Key " .
                    " LEFT OUTER JOIN Devotee_Seva ds ON d.Devotee_Key = ds.Devotee_Key " . 
                        "AND ds.Seva_Year = YEAR(NOW()) AND ds.Seva_Status = 'Assigned' " .
                 "WHERE " .
                        " ds.Seva_ID = '" . $requestData . "' " .
                 "ORDER BY ds.Seva_ID Desc" ;
                
        
           
        //var_dump($query);die;
                
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $devoteeSearchResult = array();
        $i = 0;
        while($row = $results->fetchObject()){
            $row->{'Devotee_Photo'} = base64_encode($row->{'Devotee_Photo'});
            $row->{'Devotee_ID_Image'} = base64_encode($row->{'Devotee_ID_Image'});
            $devoteeSearchResult[]=$row;
            $i = $i+1;
        }
        //var_dump($devoteeSearchResult);die;
        if($i==0){
            $devoteeSearchResult['status'] = false;
            $devoteeSearchResult['message'] = "No record found!";
            $devoteeSearchResult['info'] = $results;
        }
        
        return $devoteeSearchResult;
    }
    
    private function getDevoteeAmenityDetails($requestData){
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        if (empty($requestData)) {
            $errormsg .= "Devotee keys for Amenity not supplied.";
            $status = false;
        }
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
       
        $query = "SELECT " .
                    "AM.`Amenity_Key`, " . 
                    "AM.`Amenity_Name`, " . 
                    "IFNULL(DAA.`Amenity_Quantity`,0) AS Amenity_Quantity, " .
                    "IFNULL(AA.Available_Count, 0) AS Available_Count " .
                "FROM " . 
                    "`Amenity_Master` AM " .
                "LEFT OUTER JOIN `Devotee_Amenities_Allocation` DAA ON " .
                    "DAA.Amenity_key = AM.Amenity_key " .
                "AND " .
                    "DAA.Devotee_Key = '" . $requestData . "' AND " .
                    "DAA.`Amenity_Quantity` <> 0 AND " .
                    "DAA.`Amenity_Allocation_Year` = YEAR(NOW()) " .
                "LEFT OUTER JOIN Amenities_Availability AA ON " .
                    "AM.Amenity_Key = AA.Amenity_Key " .
                "ORDER BY " .
                    "`Amenity_Allocation_Date_Time` DESC" ;
        
           // var_dump($query);
        $results = $this->conn->query($query,MYSQLI_USE_RESULT);
        
        $devoteeAmenityResult = array();
        $i = 0;
        while($row = $results->fetchObject()){
            $devoteeAmenityResult[]=$row;
            $i = $i+1;
        }
        //var_dump($devoteeSearchResult);die;
        if($i==0){
            $devoteeAmenityResult['status'] = false;
            $devoteeAmenityResult['message'] = "No record found!";
            $devoteeAmenityResult['info'] = $results;
        }
        
        return $devoteeAmenityResult;
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

        if (empty($requestData['devotee_referral'])){
            $Devotee_Referral="";
        }
        else {
            $Devotee_Referral=htmlspecialchars(strip_tags($requestData['devotee_referral']));
        }
        
        if (empty($requestData['devotee_accommodation_id'])){
            $Devotee_Accommodation_ID="0";
        }
        else {
            $Devotee_Accommodation_ID=htmlspecialchars(strip_tags($requestData['devotee_accommodation_id']));
        }

        if (empty($requestData['devotee_seva_id'])){
            $Devotee_Seva_ID="UN";
        }
        else {
            $Devotee_Seva_ID=htmlspecialchars(strip_tags($requestData['devotee_seva_id']));
        }
        
        if (empty($requestData['devotee_address_1'])){
            $Devotee_Address_1="";
        }
        else {
            $Devotee_Address_1=htmlspecialchars(strip_tags($requestData['devotee_address_1']));
        }

        if (empty($requestData['devotee_address_2'])){
            $Devotee_Address_2="";
        }
        else {
            $Devotee_Address_2=htmlspecialchars(strip_tags($requestData['devotee_address_2']));
        }

        if (empty($requestData['devotee_state'])){
            $Devotee_State="";
        }
        else {
            $Devotee_State=htmlspecialchars(strip_tags($requestData['devotee_state']));
        }
        
        if (empty($requestData['devotee_zip'])){
            $Devotee_Zip="";
        }
        else {
            $Devotee_Zip=htmlspecialchars(strip_tags($requestData['devotee_zip']));
        }

        if (empty($requestData['devotee_country'])){
            $Devotee_Country="";
        }
        else {
            $Devotee_Country=htmlspecialchars(strip_tags($requestData['devotee_country']));
        }

        if (empty($requestData['comments'])){
            $Comments="";
        }
        else {
            $Comments=htmlspecialchars(strip_tags($requestData['comments']));
        }

        if (empty($requestData['joined_since'])){
            $Joined_Since="";
        }
        else {
            $Joined_Since=htmlspecialchars(strip_tags($requestData['joined_since']));
        }
        
        $Devotee_Accomodation_Year = date('y');
        $Devotee_Seva_Year = date('y');
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
            //$query = "CALL PROC_UPDATE_DEVOTEE(";
            $query = "CALL PROC_REPLACE_DEVOTEE_W_SEVA_I(";
        } else {
            // Add
            // Generate unique ID
            $unique_id = $this->generateId();
            $query = "CALL PROC_INSERT_DEVOTEE_W_SEVA_I(";
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
                $Joined_Since . "')" ;
                
         
//            $res['status'] = true;
//            $res['message'] = $query;
//            $res['info'] = $unique_id;
//            return $res;
//            die;
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
            $res['message'] = $stmt;
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

    public function manageCardPrinting($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info']='';
        $errormsg = "Error occured";
        $status = true;
        $query = "";
        $Print_Record_Updated_By='Anil'; //to be fixed userid
        $now = date('Y-m-d H:i:s');
        
        if (empty($requestData['devotee_key'])) {
            $errormsg .= " Devotee Key is missing.";
            $status = false;
        }
        else{
            $Devotee_Key=htmlspecialchars(strip_tags($requestData['devotee_key']));
        }
        
        
        
        if($requestData['requestType']== "addToPrintQueue"){
            $query = "REPLACE INTO `Card_Print_Log`(
                    `Devotee_Key`,
                    `Print_Status`,
                    `Print_Requested_Date_Time`,
                    `Print_Requested_By_User`
                )
                VALUES('" . $Devotee_Key . "','A', NOW(), '" . $Print_Record_Updated_By . "')";
                      
        }
        else{
            //$query = "UPDATE `Card_Print_Log` SET Print_Status = 'C', Print_Completion_Date_Time = NOW() WHERE `Devotee_Key` in (" . $Devotee_Key . ")";
            $query = "DELETE from `Card_Print_Log` WHERE `Devotee_Key` in (" . $Devotee_Key . ")";
        }                
        
        $stmt = $this->conn->prepare($query);
        
        if ($stmt->execute()) {
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = $Devotee_Key;
        }
        else{
            $res['status'] = false;
            $res['message'] = "[Card Print] Adding/Removing Devotee Card to/from print queue failed at API!! Error INfo: " . $query;
            $res['info'] = $stmt;
        }
        return $res;
    }
    
    public function manageAmenityAllocation($requestData) {
        $res = array();
        $res['status'] = TRUE;
        $res['message'] = '';
        $res['info']='';
        $errormsg = "Error occured";
        $status = true;
        $query = "";
        $Amenity_Managed_By='Anil'; //to be fixed userid
        $now = date('Y-m-d H:i:s');
        
        $Devotee_Key="";
        $Amenity_Keys="";
        $Amenity_Quantities="";
        $Amenity_Key = array();
        $Amenity_Quantity = array();
        
        if (empty($requestData['devotee_key'])) {
            $errormsg .= " Devotee Key is missing.";
            $status = false;
        }
        else{
            $Devotee_Key=htmlspecialchars(strip_tags($requestData['devotee_key']));
        }
        
        if (empty($requestData['amenity_key'])) {
            $errormsg .= " Amenity Key is missing.";
            $status = false;
        }
        else{
            $Amenity_Keys=htmlspecialchars(strip_tags($requestData['amenity_key']));
        }
        
        if (empty($requestData['amenity_quantity'])) {
            $errormsg .= " Amenity quantity is missing.";
            $status = false;
        }
        else{
            $Amenity_Quantities=htmlspecialchars(strip_tags($requestData['amenity_quantity']));
        }
        
        $Amenity_Key = explode(",", $Amenity_Keys);
        $Amenity_Quantity = explode(",", $Amenity_Quantities);
        
        foreach ($Amenity_Key as $key => $Amenity_Key_Value) {
            if(!empty($Amenity_Key[$key]) && !empty($Amenity_Quantity[$key])){
                $query = "CALL `PROC_MANAGE_AMENITY`( '" .
                    $Devotee_Key . "','" .
                    $Amenity_Key[$key] . "'," .
                    $Amenity_Quantity[$key] . ",'" .
                    $Amenity_Managed_By . "')";
                
                $stmt = $this->conn->prepare($query);
                if ($stmt->execute()) {                    
                    $res['info'] = $res['info'] . " Devotee_Key: " . $Devotee_Key . ", Amenity_Key: " . $Amenity_Key[$key] . " processed!" ;
                }
                else{
                    $res['status'] = false;
                    $res['message'] = "[Amenity Management] Adding/Removing Devotee Amenity failed at API!! Error Info: " . $query;
                    $res['info'] = $res['info'] . " Devotee_Key: " . $Devotee_Key . ", Amenity_Key: " . $Amenity_Key[$key] . " failed to process!" ;
                }
            }            
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

    }

?>
