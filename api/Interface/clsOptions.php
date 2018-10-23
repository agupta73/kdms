<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of clsOptions
 *
 * @author agupta
 */
class clsOptions {
    private $conn;


// constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    public function loadOption($requestData) {
      
        if(empty($requestData)){
            $requestData = "not provided";
        }
        switch ($requestData) {
            case "Accommodation":
                $accomodationValues = array(array());
                $accomodationValues[0][0] = 'RK1';
                $accomodationValues[0][1] = '23';
                $accomodationValues[1][0] = 'GA1';
                $accomodationValues[1][1] = '22';
                $accomodationValues[2][0] = 'OWN';
                $accomodationValues[2][1] = '10000000';
                
                return $accomodationValues;

                break;

            default:
                break;
        }
    }
  
  }
    

