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
class clsReport {

    private $conn;
    private $debug = false;
// constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    public function getReport($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';

        if (!empty($requestData['accoType'])) {
            $AccoSpecific = $requestData['accoType'];
        } else {
            $AccoSpecific = "All";
        }

        if (!empty($requestData['eventId'])) {
            $eventId = $requestData['eventId'];
        } else {
            $eventId = "";
        }

        if (!empty($requestData['key'])) {
            $key = $requestData['key'];
        } else {
            $key = "";
        }

        $status = true;
        if (!empty($requestData['type'])) {
            switch ($requestData['type']) {
                case "AccoCount": //Accommodation Counts
                    return $this->getAccommodationCounts($AccoSpecific, $eventId);
                    break;

                case "DevoteeCount": //Accommodation Counts
                    return $this->getDevoteeCounts($eventId);
                    break;
                
                case "DutyReport": //Accommodation Counts

                    return $this->getDutyReport($key, $eventId);
                    
                    break;

                default :
                    $res['message'] = "Request type invalid!";
                    return $res;
                    break;
            }
        } else {
            $res['message'] = "Request type not specified!";
            return $res;
        }
    }

    //Returns accommodations and their counts
    private function getAccommodationCounts($AccoSpecific, $eventId) {
        $query = "SELECT" .
                " aa.accomodation_key, " .
                " am.accomodation_name, " .
                " aa.available_count," .
                " ( " .
                " am.accomodation_capacity - aa.available_count" .
                " ) AS occupied_count," .
                " aa.reserved_count," .
                " am.accomodation_capacity, " .
                " aa.allocated_count," .
                " aa.Out_of_Availability_Count" .
                " FROM" .
                " Accommodation_Availability aa" .
                " LEFT OUTER JOIN Accommodation_Master am ON" .
                " aa.accomodation_key = am.Accomodation_Key" .
                " WHERE";

        switch ($AccoSpecific) {
            case "All":
                $query = $query .
                        " aa.Available_Count <= 1000" ;
                break;

            case "Available":
                $query = $query .
                        " aa.Available_Count <= 1000" .
                        " AND aa.Available_Count > 0 ";
                break;

            case "Reserved":
                $query = $query .
                        " aa.Available_Count <= 1000" .
                        " AND aa.Reserved_Count > 0 " ;
                break;

            case "Occupied":
                $query = $query .
                        " aa.Available_Count <= 1000" .
                        " AND aa.Allocated_Count > 0 ";
                break;

            case "Allocated":
                $query = $query .                        
                        " aa.Allocated_Count > 0 ";
                break;

            default:
                $query = $query .
                        " aa.Available_Count <= 1000" ;
                break;
        }

        if($eventId != ""){
            $query = $query . " AND  aa.Accommodation_Event = '" . $eventId . "' ";
        }

        //$query = $query . " Order by Available_Count DESC";

        $query = $query . " Order by am.accomodation_name ";

        if($this->debug){echo $query; }

        $results = $this->conn->query($query);

        $accommodationResult = array();
        $i = 0;
        while ($row = $results->fetchObject()) {
            $accommodationResult[] = $row;
            $i = $i + 1;
        }
        return $accommodationResult;
    }

    //Returns 
    //1. Total devotees present in the ashram
    //2. Total devotees registered this year
    //3. Total spaces available for allocation
    //4. Total spaces reserved

    private function getDevoteeCounts($eventId) {
        $query = array();
        $devoteeResults = array();


        //1. Total devotees present in the ashram
        $query[0] = "SELECT sum(acco.allocated_Count) as SpaceOccupiedOrDevoteesPresent FROM `Accommodation_Availability` acco WHERE acco.available_count < 1000 ";

        //2. Total devotees registered this year
        $query[1] = "select sum(acco.Allocated_Count) as RegisteredDevoteesIncludingLocals from Accommodation_Availability acco WHERE 1=1 ";
        //" where Availability_Update_Date_Time >= DATE_SUB(NOW(), INTERVAL 3 MONTH)" ;
        //3. Total spaces available for allocation
        $query[2] = "SELECT sum(acco.Available_Count) as AvailableSpaces FROM `Accommodation_Availability` acco where acco.Available_Count < 1000";

        //4. Total spaces reserved
        $query[3] = "SELECT sum(acco.Reserved_Count) as ReservedSpaces FROM `Accommodation_Availability` acco where acco.Available_Count < 1000";

        //5. Total devotees with own arrangement
        $query[4] = "SELECT sum(acco.allocated_Count) as DevoteesWithOwnArrangements FROM `Accommodation_Availability` acco WHERE acco.available_count > 1000";

        //6. Total mature devotees (12 years or older))
        $query[5] = "SELECT count(distinct dm.devotee_key) as MatureDevotee FROM `Devotee_Accomodation` acco
                        LEFT OUTER JOIN `Devotee` dm ON acco.devotee_key = dm.devotee_key AND (DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),devotee_dob)), '%Y') + 0) < 60 
                    WHERE  acco.accomodation_status = 'Allocated' ";

        //7. Total Senior devotees (60 Years or Older)
        $query[6] = "SELECT count(distinct dm.devotee_key) as SeniorDevotee FROM `Devotee_Accomodation` acco
                        LEFT OUTER JOIN `Devotee` dm ON acco.devotee_key = dm.devotee_key AND (DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(),devotee_dob)), '%Y') + 0) >= 60 
                    WHERE  acco.accomodation_status = 'Allocated' AND dm.devotee_key IS NOT NULL ";

        //8. Total Male and female devotees
        $query[7] = "SELECT count(distinct dm.devotee_key) as MaleDevotee, count(df.devotee_key) as FemaleDevotee, count(du.devotee_key) as UnknownGender FROM `Devotee_Accomodation` acco
                        LEFT OUTER JOIN `Devotee` dm ON acco.devotee_key = dm.devotee_key AND dm.devotee_gender = 'M'
                        LEFT OUTER JOIN `Devotee` df ON acco.devotee_key = df.devotee_key AND df.devotee_gender = 'F'
                        LEFT OUTER JOIN `Devotee` du ON acco.devotee_key = du.devotee_key AND (du.devotee_gender = '' OR du.devotee_gender is null)
                     WHERE acco.accomodation_status = 'Allocated' ";
        
        


        for ($i = 0; $i < sizeof($query); $i++) {
            if($eventId != ""){
                $query[$i] = $query[$i] . " AND acco.Accommodation_Event = '" . $eventId . "'";
                if($this->debug){echo "<br>", $query[$i], "<br>";}
            }

            $result = $this->conn->query($query[$i]);
            if (!empty($row = $result->fetchObject())) {
                $devoteeResults[$i] = $row;
                if($this->debug){var_dump($row);}
            }
        }

        return $devoteeResults;
    }

    private function getDutyReport($key="",$eventId) {
        
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        if (empty($eventId)) {
            $errormsg .= "Event ID is missing.";
            $status = false;
        }       
        
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }

        if($this->debug){echo "key passed is: ", $key; }
        //concat('(',substr(num_cleansed,1,3),') ',substr(num_cleansed,4,3),'-',substr(num_cleansed,7)) AS num_formatted
        $query = "SELECT dlm.duty_location_name, d.devotee_key, CONCAT(d.devotee_first_name , ' ' , d.devotee_last_name) AS devotee_name, dp.devotee_photo,
                        IFNULL(CONCAT('(', SUBSTR(d.devotee_cell_phone_number, 1, 3),')-', SUBSTR(d.devotee_cell_phone_number, 4, 3), '-', SUBSTR(d.devotee_cell_phone_number, 7)),  '(###)-###-####') AS devotee_cell_phone_number
                  FROM duty_location_master dlm 
                    LEFT OUTER JOIN office_duty od ON dlm.duty_location_key = od.Duty_Location_Key
                    LEFT OUTER JOIN devotee d ON od.devotee_key = d.devotee_key
                    LEFT OUTER JOIN devotee_photo dp ON d.devotee_key = dp.devotee_key
                  WHERE d.devotee_key IS NOT NULL AND od.duty_event =  '" . $eventId . "' ";

        if($key != ""){
            $key = trim(urldecode($key));
            if(substr($key, 0) == "," or substr($key, -1) == ",") {
                $key = trim($key, ",");
            }
            $key = str_replace(",", "','", $key);

            $query = $query . " AND dlm.duty_location_key IN ('" . $key . "')";
        }


        if($this->debug){echo $query; }

        $results = $this->conn->query($query);

        $dutyReportResult = array();
        $i = 0;
        while ($row = $results->fetchObject()) {
            $row->{'devotee_photo'} = base64_encode($row->{'devotee_photo'});           
            $dutyReportResult[] = $row;
            $i = $i + 1;
        }
        if($this->debug){echo "from API, after calling function: "; var_dump($dutyReportResult);}
        return $dutyReportResult;
    }
}
