
<?php
include_once("../Logic/clsDevoteeSearch.php");
include_once("../Logic/clsDevoteeHandler.php");
include_once("../Logic/clsOptionHandler.php");
$debug = false;

//Removed redundant variable.
//$url="";
$requestType = "";
$requestData = array();

if($debug){
    var_dump($_POST); die;
}

if (!empty($_POST['requestType'])){
    $requestType = $_POST['requestType'];
}


switch ($requestType) {
    case "upsertDevotee":
        //$url = "http://localhost/kdms/api/upsertDevotee.php";

        /*$fields_as_post = ['devotee_key','devotee_type', 'devotee_first_name', 'devotee_last_name', 'devotee_id_type', 'devotee_id_number',
            'devotee_station', 'devotee_cell_phone_number', 'devotee_remarks', 'devotee_referral', 'devotee_seva_id', 'devotee_accommodation_id',
            'devotee_status', 'devotee_gender','requestType', 'joined_since', 'devotee_address_1', 'devotee_address_2', 'devotee_state','devotee_zip','devotee_country','comments' ]; */

        //Included event ID in the field list
        $fields_as_post = ['devotee_key','devotee_type', 'devotee_first_name', 'devotee_last_name', 'devotee_gender', 'devotee_dob', 'devotee_id_type', 'devotee_id_number',
        'devotee_address_1', 'devotee_address_2', 'devotee_station','devotee_state','devotee_zip','devotee_country',  'devotee_email', 'devotee_cell_phone_number', 'devotee_status', 
         'devotee_referral', 'devotee_remarks', 'comments', 'devotee_seva_id', 'devotee_accommodation_id',
             'requestType', 'joined_since',  'eventId' ];

        foreach ($fields_as_post as $fld) {
            if (!empty($_POST[$fld])) {
                $requestData[$fld] = urlencode(trim($_POST[$fld]));
            }
        }
        
        if($debug){var_dump( $requestData); }
        
        $devoteeHandler = new clsDevoteeHandler($requestData);
        $response =  $devoteeHandler->upsertDevotee();

        echo $response;
        die;
        break;
        
    case "upsertAmenity":
        //$url = "http://localhost/kdms/api/upsertDevotee.php";

         $fields_as_post = ['amenity_key','amenity_name','amenity_status','available_count','allocated_count','amenity_quantity','reserved_count','out_of_availability_count','requestType', 'eventId'];

        foreach ($fields_as_post as $fld) {
            //if (!empty($_POST[$fld])) {
                $requestData[$fld] = urlencode($_POST[$fld]);
            //}
        }       

        $optionHandler = new clsOptionHandler($requestType);
        $response =  $optionHandler->upsertOption($requestData);
        
        echo $response;
        die;
        break;
    
    case "manageAmenity":
        
        if (!empty($_POST['devotee_key'])) {
            //print_r("reaching here..");
                $devoteeHandler = new clsDevoteeHandler($_POST);
                
                $response =  $devoteeHandler->manageAmenityAllocation();
            }
        
        echo $response;
        break;
    
    case "upsertAcco":
        //$url = "http://localhost/kdms/api/upsertDevotee.php";

         $fields_as_post = ['accommodation_key','accommodation_name','available_count','allocated_count','accomodation_capacity','reserved_count','out_of_availability_count','requestType','eventId'];

        foreach ($fields_as_post as $fld) {
            //if (!empty($_POST[$fld])) {
                $requestData[$fld] = urlencode($_POST[$fld]);
            //}
        }       

        $optionHandler = new clsOptionHandler($requestType);
        $response =  $optionHandler->upsertOption($requestData);
        
        echo $response;
        die;
        break;
        
        
    case "upsertSeva":
        //$url = "http://localhost/kdms/api/upsertDevotee.php";

         $fields_as_post = ['seva_id','seva_description','requestType', 'eventId'];

        foreach ($fields_as_post as $fld) {
            //if (!empty($_POST[$fld])) {
                $requestData[$fld] = urlencode($_POST[$fld]);
            //}
        }       
        
        $optionHandler = new clsOptionHandler($requestType);
        $response =  $optionHandler->upsertOption($requestData);
        
        echo $response;
        die;
        break;

    case "upsertEvent":
        //$url = "http://localhost/kdms/api/upsertDevotee.php";

        $fields_as_post = ['event_id','event_description', 'event_status','requestType'];

        foreach ($fields_as_post as $fld) {
            //if (!empty($_POST[$fld])) {
            $requestData[$fld] = urlencode($_POST[$fld]);
            //}
        }

        $optionHandler = new clsOptionHandler($requestType);
        $response =  $optionHandler->upsertOption($requestData);

        echo $response;
        die;
        break;
    case "refreshAcco":

        $optionHandler = new clsOptionHandler($requestType);
        $optionHandler = new clsOptionHandler('RefreshAcco');
        $optionHandler->setEventId($_POST['eventId']);
        $response =  $optionHandler->getOptions();
        if($debug){echo "<br>", "reaching the function call. ";}
        echo $response;
        die;
        break;
        
    case "refreshSeva":
        $optionHandler = new clsOptionHandler('RefreshSeva');
        $response =  $optionHandler->getOptions();

        echo $response;
        die;
        break;
        
    
    case "dynamicSearchDevotee":
        $devoteeSearch = new clsDevoteeSearch($_POST);
        $response =  $devoteeSearch->dynamicSearchDevotees();

        echo $response;
        die;
        break;
    
    case "addToPrintQueue":
    case "removeFromPrintQueue":
         
        if (!empty($_POST['devotee_key'])) {
            //print_r("reaching here..");
                $devoteeHandler = new clsDevoteeHandler($_POST);
                $response =  $devoteeHandler->manageCardPrint();
            }
        
        echo $response;
        break;
    
    default:
        break;
}


?>
