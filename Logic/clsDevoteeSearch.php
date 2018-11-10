<?php


class clsDevoteeSearch {
    private $url = "http://localhost/KDMS/api/searchDevotee.php";
    private $request = array();
    //put your code here
     public function __construct($requestObject) {
        $this->request = $requestObject;
    }
    public function getDevoteeDetails(){
        
        $response = $this->get_details_from_API($this->request['devotee_key']);
        return $response;
    }
    
    public function getDevoteeRecords() {
        $response = "";
        if(!empty($this->request['DisplayMode'])){
            $response = $this->get_records_from_API($this->request['DisplayMode']);            
        }
        return $response;
    }
    
    private function get_details_from_API($requestData) {

        $ch = curl_init();
        $this->url = $this->url . "?devotee_key=" . $requestData;
        
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);       
        $response = json_decode($response, true);
        //var_dump($response);
        curl_close($ch);
        return $response;
    }
    
    private function get_records_from_API($requestData) {

        $ch = curl_init();
        $this->url = $this->url . "?display_mode=" . $requestData;
        
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);        
        $response = json_decode($response, true);
        //var_dump($response);
        curl_close($ch);
        return $response;
    }
}
