
<?php

include_once("../Logic/clsDevoteeSearch.php");
include_once("../Logic/clsDevoteeHandler.php");
include_once("../Logic/clsOptionHandler.php");

$url="";
$requestType = "";
$requestData = array();


//var_dump($_POST);die;
if (!empty($_POST['requestType'])){
    $requestType = $_POST['requestType'];
}
else {

}

//var_dump($requestType);
switch ($requestType) {
    case "upsertDevotee":
        //$url = "http://localhost/kdms/api/upsertDevotee.php";

        $fields_as_post = ['devotee_key','devotee_type', 'devotee_first_name', 'devotee_last_name', 'devotee_id_type', 'devotee_id_number',
            'devotee_station', 'devotee_cell_phone_number', 'devotee_remarks', 'devotee_accommodation_id',
            'devotee_status', 'devotee_gender'];

        foreach ($fields_as_post as $fld) {
            if (!empty($_POST[$fld])) {
                $requestData[$fld] = urlencode($_POST[$fld]);
            }
        }
        
        $devoteeHandler = new clsDevoteeHandler($requestData);
        $response =  $devoteeHandler->upsertDevotee();

        echo $response;
        die;
        break;

        
    case "refreshAcco":
        
        $optionHandler = new clsOptionHandler('RefreshAcco');
        $response =  $optionHandler->getOptions();
        //var_dump(json_encode($response));
        
        echo json_encode($response);
        die;
        break;
        
    default:
        break;
}


?>
