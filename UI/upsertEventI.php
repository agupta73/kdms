<!DOCTYPE html>
<html lang="en">

<head>
  <title>
    KDMS (Add/update Event)
  </title>
  <?php

  //TODO: call initialize event stored procedure when an event is made current or an event is closed, provided there is only one event that's current
  //TODO: Notify user if no event is current or more than one event is current
  //TODO: Log out if the current event ID is not same as the event ID in the event management page. Alert user to change the event ID in site_config

    $config_data=include_once("../site_config.php");
    include_once("header.php");  
    include_once("../Logic/clsOptionHandler.php");

    $requestData = $_GET;
    
    $eventID = "";
    $eventDescription="";
    $eventUpdatedBy = "";
    $eventStatus = "";
   
    //Pre-populate event record in case of edit
    if (!empty($requestData['event_ID'])) {
        
        $optionHandler = new clsOptionHandler("EventDetail");
        $optionHandler->setOptionKey($requestData['event_ID']);
        $response = $optionHandler->getOptions();
       
        //var_dump($response);die;
        //assign values
        if(!empty($response['Event_ID'])){
            $eventID = urldecode($response['Event_ID']);
        }
        
        if(!empty($response['Event_Description'])){
            $eventDescription=urldecode($response['Event_Description']);
        }

        if(!empty($response['Event_Status'])){
            $eventStatus=urldecode($response['Event_Status']);
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
              //alert(response);
                var r = JSON.parse(response);

		if(r['status'] == true){
                    alert("Event record updated successfully!");
                   window.location.assign("<?=$config_data['webroot']?>UI/upsertEventI.php?event_ID=" + r['info'] );
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
      <div class="content">
        <div class="container-fluid">
<!--          <div class="row">-->
            <div class="col-md-10">
                <form  id="myForm">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Add/Update Event Information</h4>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group" style="margin-top:35px;">
                          <label class="bmd-label-floating">Event ID </label>
                          <input type="text" name="event_id" id="event_id" class="form-control"  value="<?php print_r($eventID); ?>" <?php if($eventID!=""){print_r("readonly=true");} ?>>
                        </div>
                      </div>
                    
                      <div class="col-md-6">
                        <div class="form-group" style="margin-top:35px;">
                          <label class="bmd-label-floating">Event Description</label>
                          <input type="text" class="form-control" name="event_description" id="event_description" value="<?php print_r($eventDescription); ?>">
                        </div>
                      </div>
                        
                        <div class="col-md-3">
                        <div class="form-group">
                          <label class="bmd-label-floating">Status</label>
                          <select type="text" class="form-control" name="event_status" id="event_status" value="<?php print_r($eventStatus); ?>">
                            <option value="Current" <?php if($eventStatus=="Current" || empty($eventStatus) ){print_r("selected");} ?>>Current</option>
                            <option value="Future" <?php if($eventStatus=="Future"){print_r("selected");} ?>>Future</option>
                            <option value="Closed" <?php if($eventStatus=="Closed"){print_r("selected");} ?>>Closed</option>
                          </select>
                        </div>
                      </div>
                      
                    </div>
                    



                     </div>
                   <div class="row"> 
                       <div class="col-md-6"></div>
                       <div class="col-md-6" >
                    <input type="hidden" name="requestType" id="requestType" value="upsertEvent">
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
