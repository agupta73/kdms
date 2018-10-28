<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of clsOptionHandler
 *
 * @author agupta
 */
class clsOptionHandler {
    private $url = "http://localhost/KDMS/api/loadOptions.php";
    private $request = "";
    //put your code here
     public function __construct($requestObject) {
        $this->request = $requestObject;
        
    }
    public function getOptions(){
        
        $response = $this->curl_rest($this->request);
        return $response;
    }
    
    public function refreshOptions(){
        $response = $this->curl_rest($this->request);
        return $response;
    }


    private function curl_rest($requestData) {

        $ch = curl_init();
        $this->url = $this->url . "?option_type=" . $requestData;
        
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        
        $response = json_decode($response, true);
        //var_dump($response);
        curl_close($ch);
        return $response;
    }
}

