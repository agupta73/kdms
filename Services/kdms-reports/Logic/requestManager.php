
<?php
include_once("clsServicesManager.php");
$debug = false;

$requestType = "";
$requestData = array();

if($debug){
    var_dump($_POST);
}

if (!empty($_POST['requestType'])){
    $requestType = $_POST['requestType'];
}
else {
    $requestType = $_POST['type'];
}


switch ($requestType) {
    case "upsertRemark":
    
        if (!empty($_POST['devotee_key']) AND !empty($_POST['eventId'])) {
            $fields_as_post = ['remark_type','rating','remark','devotee_key','requestType','eventId','userId'];

            foreach ($fields_as_post as $fld) {
                //if (!empty($_POST[$fld])) {
                    $requestData[$fld] = urlencode($_POST[$fld]);
                //}
            }       
            //print_r("reaching here..");
                $devoteeHandler = new clsServicesManager($_POST);                
                $response =  $devoteeHandler->upsertRecords();
            }
        
        echo $response;
        break;

        case "upsertFav":
    
            if (!empty($_POST['user_key']) AND !empty($_POST['fav_name'])) {
                $fields_as_post = ['fav_public','fav_name','user_key','fav_type','type','fav_url','fav_updated_by'];
    
                foreach ($fields_as_post as $fld) {
                    //if (!empty($_POST[$fld])) {
                        $requestData[$fld] = urlencode($_POST[$fld]);
                    //}
                }
            if($debug){
                echo "reaching upsert Fav..";
                die;
            }
                
                    $favHandler = new clsServicesManager($_POST);                
                    $response =  $favHandler->upsertRecords();
                }
            
            echo $response;
            break;
        
        case "upsertAttendance":
    
            if (!empty($_POST['devotee_key']) AND !empty($_POST['eventId'])) {
                $fields_as_post = ['remark_type','rating','remark','devotee_key','seva_id','attendance_date','requestType','eventId','userId'];
    
                foreach ($fields_as_post as $fld) {
                    //if (!empty($_POST[$fld])) {
                        $requestData[$fld] = urlencode($_POST[$fld]);
                    //}
                }       
                //print_r("reaching here..");
                    $devoteeHandler = new clsServicesManager($_POST);                
                    $response =  $devoteeHandler->upsertRecords();
                }
            
            echo $response;
            break;

    default:
        echo "request type not specified!"; 
        break;
}


?>
