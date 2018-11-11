<?php


class clsDevoteeSearch {
    private $url = "http://localhost/KDMS/api/searchDevotee.php";
    private $request = array();
    //put your code here
     public function __construct($requestObject) {
        $this->request = $requestObject;
    }
    public function getDevoteeDetails(){
        
        $response = $this->get_records_from_API($this->request['devotee_key'], "KEY");
        return $response;
    }
    
    public function getDevoteeRecords() {
        $response = "";
        if(!empty($this->request['mode']) AND !empty($this->request['key'])){            
                $response = $this->get_records_from_API($this->request['key'],$this->request['mode']);             
        }
        return $response;
    }
    

    private function get_records_from_API($requestData,$mode) {

        $ch = curl_init();
        $this->url = $this->url . "?key=" . $requestData . "&mode=" . $mode;
        
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);        
        $response = json_decode($response, true);
        //var_dump($response);
        curl_close($ch);
        return $response;
    }
}
