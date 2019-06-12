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
    private $urlUpsert = "http://localhost/KDMS/api/upsertOption.php";
    private $optionType = "";
    private $optionKey = "";

    //put your code here
    public function __construct($requestObject) {
        $this->optionType = $requestObject;
        // Include new config file in each page ,where we need data from configuration
        $config_data = include("../site_config.php");
        $this->url = $config_data['webroot'] . 'api/loadOptions.php';
        $this->urlUpsert = $config_data['webroot'] . 'api/upsertOption.php';
    }

    public function setOptionKey($optKey) {
        $this->optionKey = $optKey;
    }

    public function getOptions() {

        switch ($this->optionType) {
            case "Accommodation":
            case "Amenity":
            case "Seva":
                $response = $this->getOptionsFromAPI($this->optionType, "");
                break;

            case "RefreshAcco":
            case "RefreshAmenity":
            case "RefreshSeva":
                $response = $this->getOptionsFromAPI($this->optionType, "");
                break;

            case "AccommodationDetail":
            case "AmenityDetail":
            case "SevaDetail":
                $response = $this->getOptionsFromAPI($this->optionType, $this->optionKey);
                break;

            default:

                break;
        }

        return $response;
    }

    public function upsertOption($requestData) {
        //$response
        switch ($this->optionType) {
            case "upsertAcco":
            case "upsertAmenity":
            case "upsertSeva":
                $response = $this->upsertOptionRecord($this->urlUpsert, $requestData);
                break;

            default:

                break;
        }
        return $response;
    }

    public function upsertOptionRecord($url, $post_fields = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, count($post_fields));

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        // curl_setopt($ch, CURLOPT_USERPWD, "kdms_admin:oxwkV-3]S&{t");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



        $response = curl_exec($ch);

        //$response = json_decode($response, true);

        curl_close($ch);
        return $response;
    }

    private function getOptionsFromAPI($optionType, $optionKey) {

        $ch = curl_init();
        $this->url = $this->url . "?option_type=" . $optionType . "&key=" . $optionKey;
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if ($optionType != "RefreshAcco" && $optionType != "RefreshSeva") {
            $response = json_decode($response, true);
        }

        curl_close($ch);
        return $response;
    }

}
