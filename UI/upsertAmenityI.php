<!DOCTYPE html>
<html lang="en">

<head>
  <title>
    KDMS (Add Amenity I)
  </title>
  <?php
    $config_data=include_once("../site_config.php");
    include_once("header.php");  
    include_once("../Logic/clsOptionHandler.php");

    $requestData = $_GET;
    
    $amenity_key = "";
    $amenity_name="";
    $amenity_status="";
    $available_count=0;
    $allocated_count = 0;
    $amenity_quantity = 0;
    $reserved_count = 0;
    $out_of_availability_count = 0 ;
    $amenity_updated_by = "";
   
    //Pre-populate devotee record in case of edit
    if (!empty($requestData['amenity_key'])) {
        
        $optionHandler = new clsOptionHandler("AmenityDetail");  
        $optionHandler->setOptionKey($requestData['amenity_key']);
        $response = $optionHandler->getOptions();
       
        //var_dump($response);die;
        //assign values
        if(!empty($response['Amenity_Key'])){
            $amenity_key = urldecode($response['Amenity_Key']); 
        }
        
        if(!empty($response['Amenity_Name'])){
            $amenity_name=urldecode($response['Amenity_Name']); 
        }
        
        if(!empty($response['Amenity_Status'])){
            $amenity_status=urldecode($response['Amenity_Status']); 
        }
        
        if(!empty($response['Amenity_Quantity'])){
            $amenity_quantity=urldecode($response['Amenity_Quantity']); 
        }
        
        if(!empty($response['Available_Count'])){
            $available_count= urldecode($response['Available_Count']); 
        }
        
        if(!empty($response['Allocated_Count'])){
            $allocated_count=  urldecode($response['Allocated_Count']);  
        }
        
        if(!empty($response['Reserved_Count'])){
            $reserved_count= urldecode($response['Reserved_Count']);      
        }
        
        if(!empty($response['Out_Of_Availability_Count'])){
            $out_of_availability_count= urldecode($response['Out_Of_Availability_Count']); 
        }
    }
    
    ?>
  <script>

  function _(el){
    return document.getElementById(el);
  }


//javascript function for ajax call
  function saveFormData(formId, flag){
    
    var formData = $(formId).serialize();
    //alert(formData);
    if(validateInput()){ 
         $.ajax({
          url:'<?=$config_data['webroot']?>Logic/requestManager.php',
          type:'POST',
          data:formData,
          success:function(response){
		
                var r = JSON.parse(response);
                //alert(response);
		if(r['status'] == true){
                    alert("Amenity record updated successfully!");
                   window.location.assign("<?=$config_data['webroot']?>UI/upsertAmenityI.php?amenity_key=" + r['info'] );
                }
		else{
                    alert(r['message']);
                }   
          }
        });       
    }
  }
  

function validateInput(){
    return true;
}
  </script>
</head>

<body class="">
  <div class="wrapper ">
    <?php
        include_once("nav.php");
      ?>

    <div class="main-panel">
      <!-- Navbar -->
      <?php
        include_once("navBottom.php");

        ?>

      <div class="content">
        <div class="container-fluid">
<!--          <div class="row">-->
            <div class="col-md-10">
                <form  id="myForm">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Add/Update Amenity Information</h4>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group" style="margin-top:35px;">
                          <label class="bmd-label-floating">Amenity Key </label>
                          <input type="text" name="amenity_key" id="amenity_key" class="form-control"  value="<?php print_r($amenity_key); ?>" <?php if($amenity_key!=""){print_r("readonly=true");} ?>>
                        </div>
                      </div>
                    
                      <div class="col-md-6">
                        <div class="form-group" style="margin-top:35px;">
                          <label class="bmd-label-floating">Amenity Name</label>
                          <input type="text" class="form-control" name="amenity_name" id="amenity_name" value="<?php print_r($amenity_name); ?>">
                        </div>
                      </div>
                        
                        <div class="col-md-3">
                        <div class="form-group">
                          <label class="bmd-label-floating">Status</label>
<!--                          <input type="text" class="form-control" name="amenity_status" id="amenity_status" value="<?php print_r($amenity_status); ?>">-->
                          <select type="text" class="form-control" name="amenity_status" id="amenity_status" value="<?php print_r($amenity_status); ?>">
                            <option value="Available" <?php if($amenity_status=="Available" || empty($amenity_status) ){print_r("selected");} ?>>Available</option>
                            <option value="Out of Stock" <?php if($amenity_status=="Out of Stock"){print_r("selected");} ?>>Out of Stock</option>
                            <option value="Unavailable" <?php if($amenity_status=="Unavailable"){print_r("selected");} ?>>Unavailable</option>                            
                          </select>
                        </div>
                      </div>
                      
                    </div>
                    
                    <div class="row">
                       <div class="col-md-3">
                        <div class="form-group">
                          <label class="bmd-label-floating">Quantity</label>
                          <input type="text" class="form-control" name="amenity_quantity" id="amenity_quantity" value="<?php print_r($amenity_quantity); ?>">
                        </div>
                      </div><div class="col-md-2">
                        <div class="form-group" >
                          <label class="bmd-label-floating">Reserved Quantity</label>
                          <input type="text" class="form-control" name="reserved_count" id="reserved_count" value="<?php print_r($reserved_count); ?>">
                        </div>
                      </div>
                        <div class="col-md-3">
                        <div class="form-group" >
                          <label class="bmd-label-floating"> Unavailable Quantity</label>
                          <input type="text" class="form-control" name="out_of_availability_count" id="out_of_availability_count" value="<?php print_r($out_of_availability_count); ?>">
                        </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">Allocated Quantity</label>
                          <input type="text" class="form-control" name="allocated_count" id="allocated_count" readonly="true" value="<?php print_r($allocated_count); ?>">
                        </div>
                      </div>
                        <div class="col-md-2">
                        <div class="form-group">
                          <label class="bmd-label-floating">Available Quantity</label>
                          <input type="text" class="form-control" name="available_count" id="available_count" readonly="true" value="<?php print_r($available_count); ?>">
                        </div>
                      </div>
                        
                    </div>
                      
                       
                     </div>
                   <div class="row"> 
                       <div class="col-md-6"></div>
                       <div class="col-md-6" >
                    <input type="hidden" name="requestType" id="requestType" value="upsertAmenity">
                    <button type="reset" class="btn btn-success pull-right">Cancel</button>
                    <button class="btn btn-success pull-right" onclick="saveFormData('#myForm', 1); return false;" >Save</button>
                       </div>
                </div>
                   <div class="clearfix"></div>
                </div>
                                    </form>

              </div>
            </div>

            

          </div>
        </div>
      <!--</div>-->
      <footer class="footer">

      </footer>
    </div>
  </div>
  </div>

 
</body>

</html>
