<?php

require_once dirname(__DIR__) . '/includes/kdms_internal_http.php';

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

    // Removed hard coding of url
    //private $url = "http://localhost/KDMS/api/loadOptions.php";
    //private $urlUpsert = "http://localhost/KDMS/api/upsertOption.php";
    private $url = "";
    private $urlUpsert = "";


    private $optionType = "";
    private $optionKey = "";
    private $eventId = "";

    //put your code here
    public function __construct($requestObject) {
        $this->optionType = $requestObject;
        // Include new config file in each page ,where we need data from configuration
        $config_data = include("../site_config.php");
        $this->url = $config_data['webroot_server'] . 'api/loadOptions.php';
        $this->urlUpsert = $config_data['webroot_server'] . 'api/upsertOption.php';
    }

    public function setOptionKey($optKey) {
        $this->optionKey = $optKey;
    }

    public function setEventId($currentEventId) {
        $this->eventId = $currentEventId;
    }
    public function getOptions() {
        $response = null;

        switch ($this->optionType) {

            case "Amenity":
            case "Event":
                $response = $this->getOptionsFromAPI($this->optionType, "");
                break;

            case "RefreshAmenity":
            case "RefreshSeva":
                $response = $this->getOptionsFromAPI($this->optionType, "");
                break;

            case "RefreshAcco":
            case "Accommodation":
            $response = $this->getOptionsFromAPI($this->optionType,  $this->eventId);
            break;

            case "Seva":
                $response = $this->getOptionsFromAPI($this->optionType, $this->optionKey, $this->eventId);
                break;

            case "AccommodationDetail":
            case "AmenityDetail":
            case "SevaDetail":
            case "EventDetail":
                $response = $this->getOptionsFromAPI($this->optionType, $this->optionKey, $this->eventId);
                break;

            default:

                break;
        }

        return $response;
    }

    public function upsertOption($requestData) {
        //$response
        $response = null;
        switch ($this->optionType) {
            case "upsertAcco":
            case "upsertAmenity":
            case "upsertSeva":
            case "upsertEvent":
                $response = $this->upsertOptionRecord($this->urlUpsert, $requestData);
                break;

            default:

                break;
        }
        return $response;
    }

    public function upsertOptionRecord($url, $post_fields = null) {
        kdms_begin_internal_apache_curl();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POST, count($post_fields));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        kdms_curl_setopt_internal_cookie($ch);
        // curl_setopt($ch, CURLOPT_USERPWD, "kdms_admin:oxwkV-3]S&{t");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



        $response = curl_exec($ch);

        //$response = json_decode($response, true);

        curl_close($ch);
        kdms_end_internal_apache_curl();
        return $response;
    }

    private function getOptionsFromAPI($optionType, $optionKey, $eventId="") {

        kdms_begin_internal_apache_curl();
        $ch = curl_init();
        // Do not mutate $this->url — a second getOptionsFromAPI call would append another query string.
        $requestUrl = $this->url . "?option_type=" . $optionType . "&key=" . $optionKey . "&eventId=" . $eventId;
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        kdms_curl_setopt_internal_cookie($ch);
        $response = curl_exec($ch);
        if ($optionType != "RefreshAcco" && $optionType != "RefreshSeva") {
            $response = json_decode($response, true);
        }

        curl_close($ch);
        kdms_end_internal_apache_curl();
        return $response;
    }


}
