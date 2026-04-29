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
class clsAdmin
{

    private $conn;
    public $debug = false;
    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function processAdminTask($requestData)
    {
        if ($this->conn === null) {
            return [
                'status'  => false,
                'message' => 'Database connection failed.',
            ];
        }

        $res = array();
        $res['status'] = false;
        $res['message'] = '';
       
        if (isset($requestData['type'])) {
            $type = $requestData['type'];
        } elseif (isset($requestData['requestType'])) {
            $type = $requestData['requestType'];
        } else {
            $type = '';
        }
        

        $status = true;
        if (!empty($type)) {
            switch ($type) {
                case "login": //User Login
                    $userID = $requestData['loginID'];
                    $password = $requestData['password'];
                    if ($this->debug) {
                        echo "from clsAdmin - User ID : ", $userID, " Password: ", $password, " Type: ", $type, "<br>";
                    }
                    return $this->checkLogin($userID, $password);
                    break;

                case "favorites": //User Login
                    $userID = $requestData['user_key'];
                    $favType = "";
                    if(!empty($requestData['fav_type'])){
                        $favType = $requestData['fav_type'];
                    }
                    
                    if ($this->debug) {
                        echo "from clsAdmin - User ID : ", $userID,  "<br>";
                    }
                    return $this->checkFavorites($userID, $favType);
                    break;
                    
                case "Get_user_name_from_id": //User Favorites
                    if ($this->debug) {
                        echo "from clsAdmin - request Data: " ;
                        var_dump($requestData);
                    }
                    return $this->Get_user_name_from_id($requestData);
                    break;

                case "upsertFav": //User Favorites
                    if ($this->debug) {
                        echo "from clsAdmin - request Data: " ;
                        var_dump($requestData);
                    }
                    return $this->upsertUserFavorite($requestData);
                    break;
                default:
                    $res['message'] = "Request type invalid!";
                    return $res;
                    break;
            }
        } else {
            $res['message'] = "Request type not specified!";
            return $res;
        }
    }

    private function checkLogin($userID, $password)
    {
        $query = "SELECT 
                    um.User_Key
                    , um.User_Name
                    , um.User_Role
                    , um.User_Email
                    , um.User_Phone
                    , IFNULL(GROUP_CONCAT(ua.asset_key SEPARATOR ',' ), '') as Access
                FROM user_master um 
                LEFT OUTER JOIN user_access ua ON (um.User_Key = ua.user_role_key OR um.User_Role = ua.user_role_key) AND ua.access_value <> 'NONE' AND ua.access_value IS NOT NULL
                WHERE  um.User_Key = '" . $userID . "' AND um.User_Password = '" . $password . "'
                GROUP BY um.User_Role, um.User_Key ";

        if ($this->debug) {
            echo "from clsAdmin->checkLogin ", $query, "<br>";
        }
        try {
            $results = $this->conn->query($query);
        } catch (PDOException $e) {
            return [
                'status'  => false,
                'message' => 'Login query failed.',
                'info'    => $e->getMessage(),
            ];
        }
        if ($results === false) {
            $err = $this->conn->errorInfo();

            return [
                'status'  => false,
                'message' => 'Login query failed.',
                'info'    => $err[2] ?? '',
            ];
        }

        $row = $results->fetch(PDO::FETCH_ASSOC);
        if ($row !== false) {
            return $row;
        }

        return [
            'status'  => false,
            'message' => 'Devotee details not found!',
        ];
    }
    public function Get_user_name_from_id($requestData) { 
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $errormsg = "";
        $status = true;

        $user_id = "";
       
        
        if (empty($requestData['user_key'])) {
            $errormsg .= "User key not supplied.";
            $status = false;
        }
        else {
            $user_key = htmlspecialchars(strip_tags($requestData['user_key']));
        }
        
       
        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
            die;
        }
        
        $query = "SELECT user_name FROM user_master 
		            WHERE user_key = '". $user_key ."'" ;


        if ($this->debug) {
            var_dump($query);
        }

        $results = $this->conn->query($query);
        if ($results === false) {
            return '# Database error #';
        }

        $i = 0;
        $user_name = '';
        foreach ($results as $row) {
            $i++;
            $user_name = $row['user_name'];
        }

        if ($i == 0) {
            return "# User Not Found #";
        }
        else{
            return $user_name;
        }	
    }
    private function checkFavorites($userID, $favType = "")
    {
        $query = "SELECT fav_url, fav_public, fav_type, fav_name
                  FROM user_favorites 
                  WHERE (fav_public = 'YES' OR user_key = '" . $userID . "') ";
        
        if($favType != ""){
            $query = $query . " AND fav_type = '" . $favType . "'";
        }
        
        $query = $query . " ORDER BY fav_public, fav_url";

        if ($this->debug) {
            echo "from clsAdmin->checkFavorites ", $query, "<br>";
        }
        $results = $this->conn->query($query);
        if ($results === false) {
            return [];
        }

        $favResult = array();
        $i = 0;
        while ($row = $results->fetchObject()) {
            $favResult[] = $row;
            $i = $i + 1;
        }
        return $favResult;
    }
    public function upsertUserFavorite($requestData)
    {
        $res = array();
        $res['status'] = false;
        $res['message'] = '';
        $res['info'] = '';
        $errormsg = "";
        $status = true;

        $query = "";
        $userKey = "";
        $favName = "Favorite";
        $favType = "REPORT";
        $favURL = "";
        $favPublic = "NO";
        $favUpdatedBy = "Unknown";

        if (empty($requestData['user_key'])) {
            $errormsg .= " User Key is missing.";
            $status = false;
        } else {
            $userKey = htmlspecialchars(strip_tags($requestData['user_key']));
        }

        if (empty($requestData['fav_name'])) {
            $errormsg .= " Favorite Name is missing.";
            $status = false;
        } else {
            $favName = htmlspecialchars(strip_tags($requestData['fav_name']));
        }
        
        
        if (empty($requestData['fav_type'])) {
            $errormsg .= " Favorite Type is missing.";
            $status = false;
        } else {
            $favType = htmlspecialchars(strip_tags($requestData['fav_type']));
        }

        if (!empty($requestData['fav_url'])) {
            $favURL = htmlspecialchars(strip_tags($requestData['fav_url']));
        }

        if (!empty($requestData['fav_public'])) {
            $favPublic = htmlspecialchars(strip_tags($requestData['fav_public']));
        }

        if (!empty($requestData['fav_update_by'])) {
            $favUpdatedBy = htmlspecialchars(strip_tags($requestData['fav_update_by']));
        }

        if ($this->debug) {
            echo "reaching here..";
            echo $status, " ", $errormsg;
        }

        if ($status == false) {
            $res['status'] = $status;
            $res['message'] = $errormsg;
            return $res;
        }
        $query = "REPLACE INTO `user_favorites`
                (`user_key`,
                `fav_name`,
                `fav_type`,
                `fav_url`,
                `fav_public`,
                `fav_updated_by`,
                `fav_update_date_time`) 
                VALUES 
                ('" . $userKey . "', 
                '" . $favName . "', 
                '" . $favType . "', 
                '" . $favURL . "', 
                '" . $favPublic . "', 
                '" . $favUpdatedBy . "', 
                NOW()) ";

        if ($this->debug) {
            echo "\n >>";
            var_dump($query);
        }
        // prepare query
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            //var_dump($stmt);
            $res['status'] = true;
            $res['message'] = "";
            $res['info'] = $userKey;
        } else {
            $res['status'] = false;
            $res['message'] = "[Attendance] Upserting User Favorites Failed at API!!";
            $res['info'] = $stmt;
        }
        return $res;
    }
}