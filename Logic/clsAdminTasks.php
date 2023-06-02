<?php

class clsAdminTasks {


    private $url;
    private $request = array();
    public $debug = false;

    public function __construct($requestObject) {
        $this->request = $requestObject;
        // Include new config file in each page ,where we need data from configuration
        $config_data = include("../site_config.php");
        $this->url = $config_data['api_dir'] . 'manageAdmin.php';
    }

    public function processAdminTasks() {
        $response = $this->curl_rest($this->url, true, $this->request);

        if($this->debug) {
            echo "from API call - Response", "<br>";
            var_dump($response);
        }
        return $response;
    }

    /*
    private function get_records_from_API($requestData) {
        if($this->debug) {
            echo "from API call", "<br>";
            var_dump($requestData);
        }
        $ch = curl_init();
        $url =$this->url . urlencode($requestData) ;
        if($this->debug) {
            echo "from API call - URL", "<br>";
            var_dump($url);

        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        curl_close($ch);
        return $response;
    }
    */
    private function curl_rest($url, $is_post = true, $post_fields = null, $request_type = null) {

        if($this->debug) {
            echo "from API call", "<br>";
            var_dump($post_fields);
        }
        //open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        if ($is_post) {
            curl_setopt($ch, CURLOPT_POST, count($post_fields));
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request_type);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        // curl_setopt($ch, CURLOPT_USERPWD, "kdms_admin:oxwkV-3]S&{t");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if($this->debug) {
            echo "after API call", "<br>";
            var_dump($response);
        }
        try{
            $response = json_decode($response, true);
        }
        catch (PDOException $e) {
            echo  $e->getMessage();
            die;
        }
        //$response = json_decode($response, true);

        curl_close($ch);
        return $response;
    }
}
