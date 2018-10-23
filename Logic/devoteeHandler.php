<?php

//**********************************************//
//Depricated code -- PLEASE DO NOT USE ---
//**********************************************//

/*
$api_type = 1;
if (!empty($_POST['api_type']) && ($_POST['api_type'] == 2)) {
    $api_type = 2;
}

$url = "http://localhost/KDMS/api/upsertDevotee.php";

$requestData = array();
 var_dump($requestData); die;
if ($api_type == 1) {  // Adding data or edit
    $fields_as_post = ['devotee_type', 'devotee_first_name', 'devotee_last_name', 'devotee_id_type', 'devotee_id_number',
        'devotee_station', 'devotee_cell_phone_number', 'devotee_remarks', 'devotee_accommodation_id',
        'devotee_status', 'devotee_gender'];

        foreach ($fields_as_post as $fld) {
            if (!empty($_POST[$fld])) {
                $requestData[$fld] = urlencode($_POST[$fld]);
            }
        }

        $response = curl_rest($url, true, $requestData);
        
    } else if ($api_type == 2) {  // Get data

        
    }


echo $response;
die;

function curl_rest($url, $is_post = true, $post_fields = null, $request_type = null) {

        //open connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($is_post) {
            curl_setopt($ch, CURLOPT_POST, count($post_fields));
        } else {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request_type);
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
       // curl_setopt($ch, CURLOPT_USERPWD, "kdms_admin:oxwkV-3]S&{t");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        $response = json_decode($response, true);
        //var_dump($response);die;
        curl_close($ch);
        return $response;
    }
    */
?>
