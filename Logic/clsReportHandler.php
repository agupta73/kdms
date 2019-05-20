<?php

class clsReportHandler {

    private $url;
    //$url ="http://localhost/KDMS/api/searchDevotee.php";
    private $request = array();

    //put your code here

    public function __construct() {
        //$this->request = $requestObject;
        // Include new config file in each page ,where we need data from configuration
        $config_data = include("../site_config.php");
        $this->url = $config_data['api_dir'] . 'getReport.php';
    }

    public function getAccommodationCounts() {
        $type = "DevoteeCount";
        $response = $this->get_acco_records_from_API($type, "");
        return $response;
    }

    public function getAccommodationRecords($accoType) {
        $type = "AccoCount";
        if (empty($accoType)) {
            $accoType = "";
        }
        $response = $this->get_acco_records_from_API($type, $accoType);
        return $response;
    }

//    private function get_acco_counts_from_API($type) {
//
//        $ch = curl_init();
//        $url =$this->url . "?type=" . $type ;
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $response = curl_exec($ch);
//       
//        try{
//            $response = json_decode($response, true);
//        }
//        catch (PDOException $e) {
//            echo  $e->getMessage();
//            die;
//                    }
//        curl_close($ch);
//        return $response;
//    }

    private function get_acco_records_from_API($type, $accoType) {

        $ch = curl_init();
        $url = $this->url . "?type=" . $type;
        if ($accoType != "") {
            $url = $url . "&accoType=" . $accoType;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        try {
            $response = json_decode($response, true);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die;
        }
        curl_close($ch);
        return $response;
    }

}
