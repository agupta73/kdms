<!DOCTYPE html>
<html lang="en">

<head>
  <title>
    KDMS (Add Seva I)
  </title>
  <?php
    $config_data=include_once("../site_config.php");
    include_once("header.php");  
    include_once("../Logic/clsOptionHandler.php");

    $requestData = $_GET;
    
    $seva_id = "";
    $seva_description="";
    
    //Pre-populate devotee record in case of edit
    if (!empty($requestData['seva_id'])) {
        
        $optionHandler = new clsOptionHandler("SevaDetail");  
        $optionHandler->setOptionKey($requestData['seva_id']);
        $response = $optionHandler->getOptions();
       
        //assign values
        if(!empty($response['Seva_ID'])){
            $seva_id = urldecode($response['Seva_ID']); //"P1810142093" 
        }
        
        if(!empty($response['Seva_Description'])){
            $seva_description=urldecode($response['Seva_Description']); // "p" "P";
        }
    }
//        var_dump($accommodation_key);
//      var_dump($accommodation_name);
//      var_dump($available_count);
//      var_dump($allocated_count);
//      var_dump($accomodation_capacity);
//      var_dump($reserved_count);
//      var_dump($out_of_availability_count);
//      var_dump($accomodation_updated_by);
    //die;
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
                    alert("Seva record updated successfully!");
                   window.location.assign("<?=$config_data['webroot']?>UI/addSevaI.php?seva_id=" + r['info'] );
                }
		else{
                    alert(r['message']);
                }   
          }
        });

        //save and exit
//        if(flag == -1)
//          window.location.assign("/KDMS/UI/printID.php");
//
//        //save and print
//        if(flag == 0)
//          window.location.assign("/KDMS/UI/index.php");
    }
    
//   document.getElementById("myForm").action = "/KDMS/Logic/requestManager.php";
//   document.getElementById("myForm").method = "POST";
//   document.getElementById(formId).submit();
   
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
                  <h4 class="card-title">Add/Update Seva Information</h4>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="bmd-label-floating">Seva ID </label>
                          <input type="text" name="seva_id" id="seva_id" class="form-control"  value="<?php print_r($seva_id); ?>" <?php if($seva_id!=""){print_r("readonly=true");} ?>>
                        </div>
                      </div>
                    
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Seva Description</label>
                          <input type="text" class="form-control" name="seva_description" id="seva_description" value="<?php print_r($seva_description); ?>">
                        </div>
                      </div>
                        
<!--                         <div class="col-md-3">
                        <div class="form-group">
                          <label class="bmd-label-floating">Accommodation Capacity</label>
                          <input type="text" class="form-control" name="accomodation_capacity" id="accomodation_capacity" value="<?php print_r($accomodation_capacity); ?>">
                        </div>
                      </div>
                      
                    </div>
                   <div class="row">
                       <div class="col-md-3">
                        <div class="form-group" >
                          <label class="bmd-label-floating">Spots Reserved</label>
                          <input type="text" class="form-control" name="reserved_count" id="reserved_count" value="<?php print_r($reserved_count); ?>">
                        </div>
                      </div>
                        <div class="col-md-3">
                        <div class="form-group" >
                          <label class="bmd-label-floating"> Unavailable Spots</label>
                          <input type="text" class="form-control" name="out_of_availability_count" id="out_of_availability_count" value="<?php print_r($out_of_availability_count); ?>">
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="form-group">
                          <label class="bmd-label-floating">Allocated Spots</label>
                          <input type="text" class="form-control" name="allocated_count" id="allocated_count" readonly="true" value="<?php print_r($allocated_count); ?>">
                        </div>
                      </div>
                        <div class="col-md-3">
                        <div class="form-group">
                          <label class="bmd-label-floating">Available Spots</label>
                          <input type="text" class="form-control" name="available_count" id="available_count" readonly="true" value="<?php print_r($available_count); ?>">
                        </div>
                      </div>
                        
                    </div>-->
                      
                       
                     </div>
                   <div class="row"> 
                       <div class="col-md-6"></div>
                       <div class="col-md-6" >
                    <input type="hidden" name="requestType" id="requestType" value="upsertSeva">
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

  <!-- Modal -->
    <div class="modal fade" id="CameraModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
      <div class="modal-dialog" role="document" >
        <div class="modal-content" style="width:800px;height:500px;">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">Camera</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" >
            <div class="row">
              <div class="col-md-6">
                <video id="video" width="300" height="250" autoplay></video>
                <button id="click-pic" class="btn btn-secondary">Snap Photo</button>
              </div>

              <div class="col-md-5">
            <canvas id="canvas" ></canvas>
            <div id="photo"></div>
            </div>
          </div>
            <script>
//            var video = document.getElementById('video');
//              // Get access to the camera!
//              if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
//                // Not adding `{ audio: true }` since we only want video now
//                navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
//                    video.src = window.URL.createObjectURL(stream);
//                    video.play();
//                });
//              }
//              // Elements for taking the snapshot
//              var canvas = document.getElementById('canvas');
//              var context = canvas.getContext('2d');
//              var video = document.getElementById('video');
//
//              // Trigger photo take
//              document.getElementById("snap").addEventListener("click", function() {
//              	context.drawImage(video, 0, 0, 300, 300);
//              });
//            </script>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button id="upload-pic" type="button" style="visibility:hidden;" class="btn btn-primary">Save changes</button>
            <input type="hidden" id="devotee_key_modal" name="devotee_key_modal" value="<?php print_r($devotee_key); ?>">
          </div>
        </div>
      </div>
    </div>

    <!-- id modial -->


    <!-- Modal -->
<!--      <div class="modal fade" id="IDModalLong" tabindex="-2" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document" >
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLongTitle">Upload ID</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" >
              <div class="row">
                <div class="col-md-12">
                  ...
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Save changes</button>
            </div>
          </div>
        </div>
      </div>
    </div>-->
  </div>
  <!--   Core JS Files   -->
  <?php
  include_once("scriptJS.php") ?>
  <script src="../assets/js/pages/capture.js"></script>
 
</body>

</html>
