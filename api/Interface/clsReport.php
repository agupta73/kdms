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

        $status = true;
        if (!empty($requestData['type'])) {
            switch ($requestData['type']) {
                case "AccoCount": //Accommodation Counts
                    return $this->getAccommodationCounts($AccoSpecific, $eventId);
                    break;

                case "DevoteeCount": //Accommodation Counts
                    return $this->getDevoteeCounts($eventId);
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

            default:
                $query = $query .
                        " aa.Available_Count <= 1000" ;
                break;
        }

        if($eventId != ""){
            $query = $query . " AND  aa.Accommodation_Event = '" . $eventId . "' ";
        }

        $query = $query . " Order by Available_Count DESC";

        if($this->debug){echo $query; }

        $results = $this->conn->query($query, MYSQLI_USE_RESULT);

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
        $query[0] = "SELECT sum(allocated_Count) as SpaceOccupiedOrDevoteesPresent FROM `Accommodation_Availability` WHERE available_count < 10000";

        //2. Total devotees registered this year
        $query[1] = "select sum(Allocated_Count) as RegisteredDevoteesIncludingLocals from Accommodation_Availability WHERE 1=1 ";
        //" where Availability_Update_Date_Time >= DATE_SUB(NOW(), INTERVAL 3 MONTH)" ;
        //3. Total spaces available for allocation
        $query[2] = "SELECT sum(Available_Count) as AvailableSpaces FROM `Accommodation_Availability` where Available_Count < 10000";

        //4. Total spaces reserved
        $query[3] = "SELECT sum(Reserved_Count) as ReservedSpaces FROM `Accommodation_Availability` where Available_Count < 10000";

        //5. Total devotees with own arrangement
        $query[4] = "SELECT sum(allocated_Count) as DevoteesWithOwnArrangements FROM `Accommodation_Availability` WHERE available_count > 10000";


        for ($i = 0; $i < sizeof($query); $i++) {
            if($eventId != ""){
                $query[$i] = $query[$i] . " AND Accommodation_Event = '" . $eventId . "'";
                if($this->debug){echo "<br>", $query[$i], "<br>";}
            }

            $result = $this->conn->query($query[$i], MYSQLI_USE_RESULT);
            if (!empty($row = $result->fetchObject())) {
                $devoteeResults[$i] = $row;
                if($this->debug){var_dump($row);}
            }
        }

        return $devoteeResults;
    }

}
