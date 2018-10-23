<?php


class clsDevoteeSearch {
    private $url = "http://localhost/KDMS/api/searchDevotee.php";
    private $request = array();
    //put your code here
     public function __construct($requestObject) {
        $this->request = $requestObject;
        
    }
    public function getDevoteeDetails(){
        
        $response = $this->curl_rest($this->request['devotee_key']);
        return $response;
    }
    
    private function curl_rest($requestData) {

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
}
