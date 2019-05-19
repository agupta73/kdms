<?php

class clsDevoteeSearch {

    private $url;
    //$url ="http://localhost/KDMS/api/searchDevotee.php";
    private $request = array();

    //put your code here

    public function __construct($requestObject) {
        $this->request = $requestObject;
        
        // Include new config file in each page ,where we need data from configuration
        $config_data = include("../site_config.php");
        $this->url = $config_data['api_dir'] . 'searchDevotee.php';
    }

    public function getDevoteeDetails() {

        $response = $this->get_records_from_API($this->request['devotee_key'], "KEY");
        return $response;
    }

    public function getDevoteeAmenities() {

        $response = $this->get_records_from_API($this->request['devotee_key'], "DAD");                
        return $response;
        
    }
    
    public function getDevoteeRecords() {
        $response = "";
        if (!empty($this->request['mode']) AND ! empty($this->request['key'])) {
            $response = $this->get_records_from_API($this->request['key'], $this->request['mode']);            
        }
        return $response;
    }

    public function dynamicSearchDevotees() {
        $response = "";
        if (!empty($this->request['key'])) {
            $response = $this->get_records_from_API($this->request['key'], "DYN");
        }
        return $response;
    }

    private function get_records_from_API($requestData, $mode) {

        $ch = curl_init();
        $url =$this->url . "?key=" . urlencode($requestData) . "&mode=" . $mode;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
       
//        return $response;
        try{
            if ($mode != 'DYN') {
                $response = json_decode($response, true);
                
            }
        }
        catch (PDOException $e) {
                        echo  $e->getMessage();
                        die;
                    }
        curl_close($ch);
        return $response;
    }

}
