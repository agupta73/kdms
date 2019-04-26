<?php

/**
 * Description of clsDevoteeHandler
 *
 * @author agupta
 */
class clsDevoteeHandler {

    private $requestData = array();
    private $api_type = 1;
    private $url = "http://localhost/KDMS/api/upsertDevotee.php";

    public function __construct($requestObject) {
        $this->requestData = $requestObject;
        // Include new config file in each page ,where we need data from configuration
        $config_data = include("../site_config.php");
        $this->url=$config_data['api_dir']."upsertDevotee.php";
    }

    public function setAPIType($apiType) {
        /* Api type 1= add/edit data (default), 2= get data */
        if (!empty($apiType) && ($apiType == 2)) {
            $this->api_type = 2;
        }
    }

    public function upsertDevotee() {
        $response = array();
        // Add or edit data
        if ($this->api_type == 1) {
            if (!empty($this->requestData)) {
                $response = $this->curl_rest($this->url, true, $this->requestData);
            }
        }
        /* Get data */ else if ($api_type == 2) {
            
        }
        return $response;
    }

    public function manageCardPrint() {
        $response = array();
        // Add or edit data
        if (!empty($this->requestData)) {
            $response = $this->curl_rest($this->url, true, $this->requestData);
        }
        return $response;
    }

    public function manageAmenityAllocation() {
        $response = array();
        // Add or edit data
        if (!empty($this->requestData)) {
            $response = $this->curl_rest($this->url, true, $this->requestData);
        }
        return $response;
    }

    private function curl_rest($url, $is_post = true, $post_fields = null, $request_type = null) {

        //open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($is_post) {
            curl_setopt($ch, CURLOPT_POST, count($post_fields));
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request_type);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        // curl_setopt($ch, CURLOPT_USERPWD, "kdms_admin:oxwkV-3]S&{t");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        //$response = json_decode($response, true);

        curl_close($ch);
        return $response;
    }

}

?>