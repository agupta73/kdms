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

    public function loadOption($requestData) {
        $option = "";
        if(!empty($requestData['option_type'])){
            $option=$requestData['option_type'];
        }
        else{
            $option = "not provided";
        }
        switch ($option) {
            case "Accommodation":
//                $accomodationValues = array(array());
//                $accomodationValues[0][0] = 'RK1';
//                $accomodationValues[0][1] = '23';
//                $accomodationValues[1][0] = 'GA1';
//                $accomodationValues[1][1] = '22';
//                $accomodationValues[2][0] = 'OWN';
//                $accomodationValues[2][1] = '10000000';
                
//                return $accomodationValues;
                
                  return $this->getAccommodations();

                break;

            default:
                print_r("Not provided optoin");
                break;
        }
        
    }
    
    private function getAccommodations(){
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;
        
        
        $query = "SELECT am.accomodation_key, am.`Accomodation_Name`, aa.Available_Count 
            FROM `Accommodation_Master` am 
            LEFT OUTER JOIN accommodation_availability aa 
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
  
  }
    

