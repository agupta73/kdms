<?php

// get database connection
include_once 'config/database.php';
include_once 'Interface/inventory.php';

$debug = false;

$database = new Database();
$db = $database->getInvConnection();

$inventory = new inventory($db);
$submitMethod = "";

if (!empty($_GET)) {
  $requestData = $_GET;
  $submitMethod = "GET";
} else {
  $requestData = $_POST;
  $submitMethod = "POST";
}

//echo json_encode($requestData); die;
$res = array();
$response = array('flag' => false, 'message' => "Request failed", 'info' => $requestData);

if ($debug) {
  var_dump($requestData);
  var_dump($_GET);
  var_dump($_POST);
}

if (!empty($requestData['requestType'])) {
  $response = array('flag' => false, 'message' => "Request failed", 'info' => $requestData['requestType']);
  try {
    switch ($requestData['requestType']) {
      case "fill_category":
        $res = $inventory->fill_category();
        echo json_encode($res);
        die;
        break;

      case "fill_location_rack":
        $res = $inventory->fill_location_rack();
        echo json_encode($res);
        die;
        break;

      case "fill_supplier":
        $res = $inventory->fill_supplier();
        echo json_encode($res);
        die;
        break;

      case "fill_company":
        $res = $inventory->fill_company();
        echo json_encode($res);
        die;
        break;

      case "fill_tax":
        $res = $inventory->fill_tax();
        echo json_encode($res);
        die;
        break;
      
      case "fill_tax_raw":
        $res = $inventory->fill_tax();
        echo json_encode($res);
        die;
        break;

      case "fill_item":
        $res = $inventory->fill_item();
        echo json_encode($res);
        die;
        break;

      case "get_product_array":
        $res = $inventory->get_product_array();
        echo json_encode($res);
        die;
        break;

      case "Get_tax_field":
        $res = $inventory->fill_tax();
        echo json_encode($res);
        die;
        break;

      case "Get_total_no_of_product":
        $res = $inventory->Get_total_no_of_product();
        echo json_encode($res);
        die;
        break;

      case "Get_total_product_purchase":
        $res = $inventory->Get_total_product_purchase();
        echo json_encode($res);
        die;
        break;

      case "Get_total_product_sale":
        $res = $inventory->Get_total_product_sale();
        echo json_encode($res);
        die;
        break;

      case "Count_outstock_product":
        $res = $inventory->Count_outstock_product();
        echo json_encode($res);
        die;
        break;

      case "Get_currency_symbol":
        $res = $inventory->Get_currency_symbol();
        echo json_encode($res);
        die;
        break;

      case "Get_Product_company_code":
        $res = $inventory->Get_Product_company_code($requestData);
        echo json_encode($res);
        die;
        break;

      case "Get_category_name":
        $res = $inventory->Get_category_name($requestData);
        echo json_encode($res);
        die;
        break;

      case "Get_order_tax_percentage":
        $res = $inventory->Get_order_tax_percentage($requestData);
        echo json_encode($res);
        die;
        break;

      case "Get_user_name_from_id":
        $res = $inventory->Get_user_name_from_id($requestData);
        echo json_encode($res);
        die;
        break;

      case "Get_product_name":
        $res = $inventory->Get_product_name($requestData);
        echo json_encode($res);
        die;
        break;

      case "fetch_chart_data":
        $res = $inventory->fetch_chart_data($requestData);
        echo json_encode($res);
        die;
        break;
  
      case "fetch_out_stock_product":
        $res = $inventory->fetch_out_stock_product($requestData);
        echo json_encode($res);
        die;
        break;
        
      case "fetch_purchase":
        $res = $inventory->fetch_purchase($requestData);
        echo json_encode($res);
        die;
        break;  
          
      case "Get_item_purchase_qty":
        $res = $inventory->Get_item_purchase_qty($requestData);
        echo json_encode($res);
        die;
        break;

      case "Get_item_purchase_record":
        $res = $inventory->Get_item_purchase_record($requestData);
        echo json_encode($res);
        die;
        break;
    
      case "get_items_for_purchase_id":
        $res = $inventory->get_items_for_purchase_id($requestData);
        echo json_encode($res);
        die;
        break;

      case "get_item_for_item_id":
        $res = $inventory->get_item_for_item_id($requestData);
        echo json_encode($res);
        die;
        break;
  
      case "get_category_for_category_id":
        $res = $inventory->get_category_for_category_id($requestData);
        echo json_encode($res);
        die;
        break;

      case "get_tax_for_tax_id":
        $res = $inventory->get_tax_for_tax_id($requestData);
        echo json_encode($res);
        die;
        break;

      case "purchase_item":
        $res = $inventory->purchase_item($requestData);
        echo json_encode($res);
        die;        
        break;

      case "update_purchase":
        $res = $inventory->update_purchase($requestData);
        echo json_encode($res);
        die;        
        break;
        
      case "delete_purchase":
        $res = $inventory->delete_purchase($requestData);
        echo json_encode($res);
        die;        
        break;
      
      case "fetch_orders":
        $res = $inventory->fetch_orders($requestData);
        echo json_encode($res);
        die;
        break;    
      
      case "fetch_product":
        $res = $inventory->fetch_product($requestData);
        echo json_encode($res);
        die;
        break;    
      
      case "fetch_tax":
        $res = $inventory->fetch_tax($requestData);
        echo json_encode($res);
        die;
        break; 

      case "fetch_category":
        $res = $inventory->fetch_category($requestData);
        echo json_encode($res);
        die;
        break;    
      
      case "get_order_for_order_id":
        $res = $inventory->get_order_for_order_id($requestData);
        echo json_encode($res);
        die;
        break; 
      
      case "get_order_item_for_order_id":
        $res = $inventory->get_order_item_for_order_id($requestData);
        echo json_encode($res);
        die;
        break; 
        
      case "create_order":
        $res = $inventory->create_order($requestData);
        echo json_encode($res);
        die;        
        break;
      
      case "add_category":
        $res = $inventory->add_category($requestData);
        echo json_encode($res);
        die;        
        break;
      
      case "edit_category":
        $res = $inventory->edit_category($requestData);
        echo json_encode($res);
        die;        
        break;
       
      case "delete_category":
        $res = $inventory->delete_category($requestData);
        echo json_encode($res);
        die;        
        break;
        
      case "add_product":
        $res = $inventory->add_product($requestData);
        echo json_encode($res);
        die;        
        break;

      case "edit_product":
        $res = $inventory->edit_product($requestData);
        echo json_encode($res);
        die;        
        break;

      case "delete_product":
        $res = $inventory->delete_product($requestData);
        echo json_encode($res);
        die;        
        break;

      case "add_tax":
        $res = $inventory->add_tax($requestData);
        echo json_encode($res);
        die;        
        break;

      case "edit_tax":
        $res = $inventory->edit_tax($requestData);
        echo json_encode($res);
        die;        
        break;

      case "delete_tax":
        $res = $inventory->delete_tax($requestData);
        echo json_encode($res);
        die;        
        break;

      case "create_order_line":
        $res = $inventory->create_order_line($requestData);
        echo json_encode($res);
        die;        
        break;
  
      case "remove_order_line":
        $res = $inventory->remove_order_line($requestData);
        echo json_encode($res);
        die;        
        break;

      case "update_order":
        $res = $inventory->update_order($requestData);
        echo json_encode($res);
        die;        
        break;

      case "delete_order":
        $res = $inventory->delete_order($requestData);
        echo json_encode($res);
        die;        
        break;

      default:
        $response = array('flag' => false, 'message' => "Request type not specified or incorrect", 'info' => $requestData['requestType']);
        break;
    }
  } catch (Exception $e) {
    $response['message'] = $e->getMessage();
  }
} else {
  $response = array('flag' => false, 'message' => "Request data empty", 'info' => $requestData);
}

if (!empty($res['status'])) {

  if ($res['status']) {
    $response = array('flag' => true, 'message' => $res['message'], 'info' => $res['info']);
  } else {
    $response = array('flag' => false, 'message' => $res['message'], 'info' => $res['info']);
  }

}
echo json_encode($response);
die;


//  switch (json_last_error()) {
//        case JSON_ERROR_NONE:
//            echo ' - No errors';
//        break;
//        case JSON_ERROR_DEPTH:
//            echo ' - Maximum stack depth exceeded';
//        break;
//        case JSON_ERROR_STATE_MISMATCH:
//            echo ' - Underflow or the modes mismatch';
//        break;
//        case JSON_ERROR_CTRL_CHAR:
//            echo ' - Unexpected control character found';
//        break;
//        case JSON_ERROR_SYNTAX:
//            echo ' - Syntax error, malformed JSON';
//        break;
//        case JSON_ERROR_UTF8:
//            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
//        break;
//        default:
//            echo ' - Unknown error';
//        break;
//  };

?>