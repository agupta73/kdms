<?php

include('api/config/database.php');
echo "------------------------------------------------------------Database connection--------------------------------<br />";
$db = New Database();
try {
    $con = $db->getConnection();
} catch (Exception $e) {
    var_dump($con);
    die;
}
//---------------------------------------------------------
/*
For Linux only
 *  */
//convertTablesToCamelcase($con);
//die;
//----------------------------------------------------------
echo "------------------------------------------------------------OS and Store Procedure excution Check--------------------------------<br />";
// SP are using camelcase name of Tables, In Ubuntu SP are failing due to this.
$browser = $_SERVER['HTTP_USER_AGENT'];
if ((strpos($browser, 'Ubuntu') >= 0)) {
    // Check table
    $sql = "SELECT * from Event_Master";
    $result = $con->query($sql);
    if (!$result) {
        echo "Check if Event_Master table exists! <br>";
        echo "If yes , Please check if it is in Camelcase.Table refrences in All Store procedures, are in Camel case";
        die;
    } else {
        echo "------------------------------------------------------------Table Name OKAY---------------------------------";
    }
}
/*
 *  Convert table name to camelcase
 */
function convertTablesToCamelcase($con) {

    $query = $con->prepare('show tables');
    $query->execute();
    while ($rows = $query->fetch(PDO::FETCH_ASSOC)) {
        foreach ($rows as $k=>$table){
            $tmp_name= explode('_', $table);
            $new_n="";
            foreach ($tmp_name as $tn){
                if($new_n!=""){
                    $new_n = $new_n."_".ucfirst($tn);
                }else{
                    $new_n = ucfirst($tn);
                }
                
            }
            $query2= "RENAME TABLE $table
                  TO $new_n";
           $con->query($query2);
        }
    }
}

?>