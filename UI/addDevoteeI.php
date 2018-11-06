<!DOCTYPE html>
<html lang="en">

<head>
  <title>
    KDMS (Add Devotee I)
  </title>
  <?php
    include_once("header.php");  
    include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsDevoteeSearch.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsOptionHandler.php");

    $requestData = $_GET;
    
    $devotee_key = "";
    $devotee_type="P";
    $devotee_first_name="";
    $devotee_last_name= "";
    $devotee_id_type="";
    $devotee_id_number="";
    $devotee_station="";
    $devotee_cell_phone_number="";
    $devotee_status="A";
    $devotee_remarks="";
    $devotee_accommodation_id="";
    $devotee_photo="";
    
    
    //load accommodation options and available spots
    $loadAccommodation = new clsOptionHandler("Accommodation");
    $accommodations = $loadAccommodation->getOptions();
    unset($loadAccommodations);

    //var_dump($requestData);die;
    //Pre-populate devotee record in case of edit
    if (!empty($requestData['devotee_key'])) {
        
        $devoteeSearch = new clsDevoteeSearch($requestData);    
        $response = $devoteeSearch->getDevoteeDetails();
        //var_dump($response); die;
        //$response = json_decode($response);
        //var_dump($response);
        
        //assign values
        if(!empty($response['Devotee_Key'])){
            $devotee_key = urldecode($response['Devotee_Key']); //"P1810142093" 
        }
        
        if(!empty($response['Devotee_Type'])){
            $devotee_type=urldecode($response['Devotee_Type']); // "p" "P";
        }
        
        if(!empty($response['Devotee_First_Name'])){
            $devotee_first_name= urldecode($response['Devotee_First_Name']); // "Anil+6" ;
        }
        
        if(!empty($response['Devotee_Last_Name'])){
            $devotee_last_name=  urldecode($response['Devotee_Last_Name']); // "Gupta" 
        }
        
        if(!empty($response['Devotee_ID_Type'])){
            $devotee_id_type= urldecode($response['Devotee_ID_Type']); // NULL     
        }
        
        if(!empty($response['Devotee_ID_Number'])){
            $devotee_id_number= urldecode($response['Devotee_ID_Number']); // "" 
        }
        
        if(!empty($response['Devotee_Station'])) {
            $devotee_station= urldecode($response['Devotee_Station']); // "New+Delhi" 
        }
        
        if(!empty($response['Devotee_Cell_Phone_Number'])) {
               $devotee_cell_phone_number= urldecode($response['Devotee_Cell_Phone_Number']); //  "4156227879" 
        }
        
        if(!empty($response['Devotee_Status'])){
            $devotee_status= urldecode($response['Devotee_Status']); // "A" ;
        }
        
        if(!empty($response['Devotee_Remarks'])){
            $devotee_remarks= urldecode($response['Devotee_Remarks']); //  "" 
        }
        
        if(!empty($response['Accomodation_Key'])){
            $devotee_accommodation_id = urldecode($response['Accomodation_Key']); //  "" 
        }
   
        
        //$devotee_accommodation_id="";
        //$response['Devotee_Gender']; // "" 
        ////$response['Devotee_Record_Update_Date_Time']; // "2018-10-14 07:23:23" 
        //$response['Devotee_Record_Updated_By']; //  "0" 
        //$response['Devotee_ID_Image']; // NULL 
        //$response['Devotee_ID_XML']; // NULL 
        //$response['Photo_type']; // NULL 
        if(!empty($response['Devotee_Photo'])){  // NULL
            $devotee_photo = $response['Devotee_Photo'];
            //var_dump($devotee_photo);die;
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
          url:'/KDMS/Logic/requestManager.php',
          type:'POST',
          data:formData,
          success:function(response){
		
                var r = JSON.parse(response);
                
		if(r['flag'] == true){
                    alert("Devotee record updated successfully!");
                    window.location.assign("/KDMS/UI/adddevoteei.php");;
                }
		else{
                    alert(r['message']);
                }   
          }
        });

        //save and exit
        if(flag == -1)
          window.location.assign("/KDMS/UI/printID.php");

        //save and print
        if(flag == 0)
          window.location.assign("/KDMS/UI/index.php");
    }
    /*
   document.getElementById("myForm").action = "/KDMS/Logic/requestManager.php";
   document.getElementById("myForm").method = "POST";
   document.getElementById(formId).submit();
   */
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

//        $Devotee_Gender=htmlspecialchars(strip_tags($requestData['devotee_gender']));
//        $Devotee_Status=htmlspecialchars(strip_tags($requestData['devotee_status']));
        ?>

      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-8">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Add Devotee Information</h4>
                </div>
                <div class="card-body">
                    <form  id="myForm">
                    <div class="row">
                      <div class="col-md-5">
                        <div class="form-group">
                          <label class="bmd-label-floating">Devotee ID (disabled)</label>
                          <input type="text" name="devotee_key" id="devotee_key" class="form-control" readonly="true" value="<?php print_r($devotee_key); ?>">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">First Name</label>
                          <input type="text" class="form-control" name="devotee_first_name" id="devotee_first_name" value="<?php print_r($devotee_first_name); ?>">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="bmd-label-floating">Last Name</label>
                          <input type="text" class="form-control" name="devotee_last_name" id="devotee_last_name" value="<?php print_r($devotee_last_name); ?>">
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="bmd-label-floating">ID Type</label>
                          <select type="text" class="form-control" name="devotee_id_type" id="devotee_id_type" value="<?php print_r($devotee_id_type); ?>">
                            <option value="none" <?php if($devotee_id_type=="none" || empty($devotee_id_type) ){print_r("selected");} ?>>--Not Selected--</option>
                            <option value="Adhaar" <?php if($devotee_id_type=="Adhaar"){print_r("selected");} ?>>Addhar</option>
                            <option value="DL" <?php if($devotee_id_type=="DL"){print_r("selected");} ?>>DL</option>
                            <option value="Other" <?php if($devotee_id_type=="Other"){print_r("selected");} ?>>Other Gov. ID</option>
                            <option value="PAN" <?php if($devotee_id_type=="PAN"){print_r("selected");} ?>>PAN</option>
                            <option value="Passport" <?php if($devotee_id_type=="Passport"){print_r("selected");} ?>>Passport</option>
                            <option value="Voter ID" <?php if($devotee_id_type=="Voter ID"){print_r("selected");} ?>>Voter ID</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group" style="margin-top:62px;">
                          <label class="bmd-label-floating">ID Number</label>
                          <input type="text" class="form-control" name="devotee_id_number" id="devotee_id_number" value="<?php print_r($devotee_id_number); ?>">
                        </div>
                      </div>
                        <div class="col-md-4">
                        <div class="form-group">
                          <label class="bmd-label-floating">Devotee Type</label>
                          <select type="text" class="form-control" name="devotee_type" id="devotee_type" value="<?php print_r($devotee_type); ?>">
                            <option value="P" <?php if($devotee_type=="p" || empty($devotee_type) || $devotee_type == "P" ){print_r("selected");} ?>>Permanent</option>
                            <option value="T" <?php if($devotee_type=="t" || $devotee_type == "T" ){print_r("selected");} ?>>Temporary</option>
                          </select>
                        </div>
                      </div>
                     </div>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="form-group" style="margin-top:62px;">
                          <label class="bmd-label-floating">Station</label>
                          <input type="text" class="form-control" name="devotee_station" id="devotee_station" value="<?php print_r($devotee_station); ?>">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group" style="margin-top:62px;">
                          <label class="bmd-label-floating">Phone No.</label>
                          <input type="text" class="form-control" name="devotee_cell_phone_number" id="devotee_cell_phone_number" value="<?php print_r($devotee_cell_phone_number); ?>">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="form-group">
                          <label class="bmd-label-floating">Location</label>
<!--                          <select type="text" class="form-control" name="devotee_accommodation_id" id="devotee_accommodation_id" value="<?php print_r($devotee_accommodation_id); ?>">
                            <option value="OWN" selected>--Own Arrangement--</option>
                            <option value="RK1">Radha Kuti 1</option>
                            <option value="GA1">Gargachal 1</option>
                          </select>-->
                          <select type="text" class="form-control" name="devotee_accommodation_id" id="devotee_accommodation_id" >
                            <?php
                            foreach ($accommodations as $accommodation){
                                print_r("<option value='" . $accommodation['accomodation_key'] . "'");
                                if(empty($devotee_accommodation_id)){
                                    $devotee_accommodation_id = 'OWN';
                                }
                                if($devotee_accommodation_id == $accommodation['accomodation_key']){
                                    print_r("selected");
                                }
                                Print_r(">" . $accommodation['Accomodation_Name'] . " - " . $accommodation['Available_Count'] . "</option>");
                            } 
                            
//                            <option value="OWN" selected>--Own Arrangement--</option>
//                            <option value="RK1">Radha Kuti 1</option>
//                            <option value="GA1">Gargachal 1</option>
                            ?>
                          </select>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <div class="form-group">
                          <label>Remarks</label>
                          <div class="form-group">
                            <label class="bmd-label-floating"> Add additional Information</label>
                            <textarea class="form-control" rows="2" name="devotee_remarks" id="devotee_remarks"> <?php print_r($devotee_remarks); ?></textarea>
                          </div>
                        </div>
                      </div>
                    </div>
                        <input type="hidden" name="requestType" id="requestType" value="upsertDevotee">
                    <button type="reset" class="btn btn-success pull-right">Cancel</button>
                    <button class="btn btn-success pull-right" onclick="saveFormData('#myForm', -1); return false;">Save and Print Card</button>
                    <button class="btn btn-success pull-right" onclick="saveFormData('#myForm', 0); return false;">Save and Exit</button>
                    <button class="btn btn-success pull-right" onclick="saveFormData('#myForm', 1); return false;" >Save</button>
                    </form>
                   <div class="clearfix"></div>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card card-profile">
                <div class="card-body" style="height:280px;">
                </br>
<!--                  <img src="../assets/img/faces/devotee.ico" alt="devotee image" height="200px" width="200px"></img>-->
                <?php 
                    if($devotee_photo==""){
                       echo '<img src="../assets/img/faces/devotee.ico" alt="devotee image" height="200px" width="200px"></img>';
                    }
                    else{
                       echo '<img src="data:image/jpeg;base64,'. $devotee_photo . '" alt="devotee image" height="200px" width="200px"></img>';  
                    }
                ?> 
                </div>
              </div>
              <div class="card card-profile">
                <div class="card-body" style="height:280px;">
                  </br><img src="../assets/img/faces/doc.png" alt="devotee image" height="200px" width="200px"></img>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
      <footer class="footer">

      </footer>
    </div>
  </div>
  </div>
  <!--   Core JS Files   -->
  <?php
  include_once("scriptJS.php")


  ?>
</body>

</html>
