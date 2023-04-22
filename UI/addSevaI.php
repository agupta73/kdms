<!DOCTYPE html>
<html lang="en">

<head>
  <title>
    KDMS (Add Seva I)
  </title>
  <?php

  //TODO: Pass seva_event to get the count for the event, as opposed to total count

    
    $config_data = include("../site_config.php");
    if (session_status() === PHP_SESSION_NONE){
        session_start();
      }
    $current_page_id = 'KD-SEVA-I';
    include_once("../sessionCheck.php");
    include_once("header.php");  
    include_once("../Logic/clsOptionHandler.php");

    $debug = false;

    $requestData = $_GET;
    
    $seva_id = "";
    $seva_description="";
    $eventId = $config_data['event_id'];

    //Pre-populate devotee record in case of edit
    if (!empty($requestData['seva_id'])) {
        
        $optionHandler = new clsOptionHandler("SevaDetail");  
        $optionHandler->setOptionKey($requestData['seva_id']);
        $optionHandler->setEventId($config_data['event_id']);
        $response = $optionHandler->getOptions();
        if(!empty($response['Seva_ID'])){
            $seva_id = urldecode($response['Seva_ID']); //"P1810142093" 
        }
        
        if(!empty($response['Seva_Description'])){
            $seva_description=urldecode($response['Seva_Description']); // "p" "P";
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
          if(validateInput()){
               $.ajax({
                url:'<?=$config_data['webroot']?>Logic/requestManager.php',
          type:'POST',
          data:formData,
          success:function(response){
                var r = JSON.parse(response);
                
		if(r['status'] == true){
                    alert("Seva record updated successfully!");
                   window.location.assign("<?=$config_data['webroot']?>UI/addSevaI.php?seva_id=" + r['info'] );
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
      <div class="content">
        <div class="container-fluid">
<!--          <div class="row">-->
            <div class="col-md-10">
                <form  id="myForm">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Add/Update Seva Information</h4>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="bmd-label-floating">Seva ID </label>
                          <input type="text" name="seva_id" id="seva_id" class="form-control" maxlength="4" oninput="this.value = this.value.toUpperCase()"
                          value="<?php print_r($seva_id); ?>" <?php if($seva_id!=""){print_r("readonly=true");} ?>>
                        </div>
                      </div>
                    
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Seva Description</label>
                          <input type="text" class="form-control" name="seva_description" id="seva_description" value="<?php print_r($seva_description); ?>">
                        </div>
                      </div>
                     </div>
                   <div class="row"> 
                       <div class="col-md-6"></div>
                       <div class="col-md-6" >
                    <input type="hidden" name="requestType" id="requestType" value="upsertSeva">
                           <input type="hidden" name="eventId" id="eventId" value="<?php echo $eventId; ?>">
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
    </div>
  </div>
  </div>
  <!--   Core JS Files   -->
 
</body>

</html>
