<?php

class clsReportHandler {

    private $url;

    private $request = array();
    private $debug = false;
    //put your code here

    public function __construct() {
        //$this->request = $requestObject;
        // Include new config file in each page ,where we need data from configuration
        $config_data = include("../site_config.php");
        $this->url = $config_data['api_dir'] . 'getReport.php';
        $this->dashboard_url = $config_data['api_dir'] . 'getDashboardData.php';
    }

    public function getAccommodationCounts($eventId) {
        $type = "DevoteeCount";
        if(!empty($eventId)){
            $response = $this->get_acco_records_from_API($type, "", $eventId);
        }
        else {
            $response = $this->get_acco_records_from_API($type, "");
        }
        if($this->debug){var_dump($response); die;}
        return $response;
    }

    public function getAccomodationCountsForEventDashbaord($eventId) {
        $type = "DevoteeCount";
        if(!empty($eventId)){
            $response = $this->get_acco_count_for_dashboard($type, "", $eventId);
        }
        return $response;
    }

    public function getAccommodationRecords($accoType, $eventId) {
        $type = "AccoCount";
        if (empty($accoType)) {
            $accoType = "";
        }
        $response = $this->get_acco_records_from_API($type, $accoType, $eventId);
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

    private function get_acco_records_from_API($type, $accoType, $eventId) {

        $ch = curl_init();
        $url = $this->url . "?type=" . $type;

        if ($accoType != "") {
            $url = $url . "&accoType=" . $accoType;
        }

        if($eventId != ""){
            $url = $url . "&eventId=" . $eventId;
        }

        if($this->debug){echo "<br> url: ", $url, "<br>";}
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);     
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); 
        //curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        $response = curl_exec($ch);
        if($this->debug){echo "<br> response: "; var_dump( $ch); var_dump( $response); }
        try {
            $response = json_decode($response, true);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die;
        }
        curl_close($ch);
        return $response;
    }
    private function get_acco_count_for_dashboard($type, $eventId) {

        $ch = curl_init();
        $url = $this->dashboard_url . "?type=" . $type;

        if($eventId != ""){
            $url = $url . "&eventId=" . $eventId;
        }

        if($this->debug){echo "<br> url: ", $url, "<br>";}
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
        $response = curl_exec($ch);
        if($this->debug){echo "<br> response: "; var_dump( $ch); var_dump( $response); }
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
