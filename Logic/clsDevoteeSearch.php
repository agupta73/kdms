<?php

class clsDevoteeSearch {

    private $url;
    //$url ="http://localhost/KDMS/api/searchDevotee.php";
    private $request = array();

    //put your code here

    public function __construct($requestObject) {
        $this->request = $requestObject;
        $site_setting = include_once '../site_config.php';
        $this->url = $site_setting['api_dir'] . 'searchDevotee.php';
    }

    public function getDevoteeDetails() {

        $response = $this->get_records_from_API($this->request['devotee_key'], "KEY");
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
        $url = $this->url . "?key=" . $requestData . "&mode=" . $mode;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if ($mode != 'DYN') {
            $response = json_decode($response, true);
        }
        //var_dump($response);
        curl_close($ch);
        return $response;
    }

}
