<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of clsAdmin
 *
 * @author agupta
 */
class clsAdmin {

    private $conn;
    public $debug = false;
// constructor with $db as database connection
    public function __construct($db) {
        $this->conn = $db;
    }

    public function processAdminTask($requestData) {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';

        $userID = $requestData['loginID'];
        $password = $requestData['password'];
        $type =  $requestData['type'];

        if($this->debug){
            echo "from clsAdmin - User ID : ", $userID, " Password: ", $password, " Type: ", $type , "<br>";
        }

        $status = true;
        if (!empty($type)) {
            switch ($type) {
                case "login": //User Login
                    return $this->checkLogin($userID, $password);
                    break;

                default :
                    $res['message'] = "Request type invalid!";
                    return $res;
                    break;
            }
        } else {
            $res['message'] = "Request type not specified!";
            return $res;
        }
    }

    //Returns accommodations and their counts
    private function checkLogin($userID, $password) {
        $query = "SELECT" .
                " * FROM user_master" .
                " WHERE " .
                " User_Key = '" . $userID . "' AND User_Password = '" . $password . "'";

        if($this->debug){
            echo "from clsAdmin->checkLogin ", $query, "<br>";
        }
        $results = $this->conn->query($query, MYSQLI_USE_RESULT);


        $loginResult = array();
        if(!empty($row = $results->fetchObject())){
            $loginResult=$row;
        }
        else{
            $loginResult['status'] = false;
            $loginResult['message'] = "Devotee details not found!";
            $loginResult['info'] = $results;
        }
        if($this->debug){
            echo "Result from clsAdmin->checkLogin ",  "<br>";
            var_dump($loginResult);
        }
        return $loginResult;
    }
}
