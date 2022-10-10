<?php
// Include new config file in each page ,where we need data from configuration
$config_data=include_once("../site_config.php");
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <title>
            KDMS (Add Devotee I)
        </title>
        <?php
        include_once("header.php");
        include_once("../Logic/clsDevoteeSearch.php");
        include_once("../Logic/clsOptionHandler.php");

        $requestData = $_GET;
        $is_key_available=false;
        $devotee_key = "";
        $devotee_type = "P";
        $devotee_first_name = "";
        $devotee_last_name = "";
        $devotee_id_type = "";
        $devotee_id_number = "";
        $devotee_station = "";
        $devotee_cell_phone_number = "";
        $devotee_status = "A";
        $devotee_remarks = "";
        $devotee_accommodation_id = "";
        $devotee_seva_id = "";
        $devotee_photo = "";
        $devotee_id_image = "";
        $devotee_referral = "";
        $Devotee_Address_1 = "" ; 
        $Devotee_Address_2 = "" ; 
        $Devotee_State = "" ; 
        $Devotee_Zip = "" ; 
        $Devotee_Country = "" ; 
        $Comments  = "" ; 
        $Joined_Since  = "" ; 


        //load accommodation options and available spots
        $loadAccommodation = new clsOptionHandler("Accommodation");
        $accommodations = $loadAccommodation->getOptions();
        unset($loadAccommodations);
        
        //load seva options and assigned devotee counts
        $loadSeva = new clsOptionHandler("Seva");
        $sevas = $loadSeva->getOptions();
        unset($loadSeva);
        
//    $loadAmenity = new clsOptionHandler("Amenity");
//    $amenities = $loadAmenity->getOptions();
//    unset($loadAmenity);
        //var_dump($amenities);
        //die;
        //var_dump($accommodations);die;
        //Pre-populate devotee record in case of edit
        if (!empty($requestData['devotee_key'])) {
            $devotee_key=$requestData['devotee_key'];
            $is_key_available=true;
            $devoteeSearch = new clsDevoteeSearch($requestData);
            $response = $devoteeSearch->getDevoteeDetails();
//           unset($devoteeSearch);
//               var_dump($response); die;
            //$response = json_decode($response);
            //var_dump($response);
            //assign values
            if (!empty($response['Devotee_Key'])) {
                $devotee_key = urldecode($response['Devotee_Key']); //"P1810142093" 
            }

            if (!empty($response['Devotee_Type'])) {
                $devotee_type = urldecode($response['Devotee_Type']); // "p" "P";
            }

            if (!empty($response['Devotee_First_Name'])) {
                $devotee_first_name = urldecode($response['Devotee_First_Name']); // "Anil+6" ;
            }

            if (!empty($response['Devotee_Last_Name'])) {
                $devotee_last_name = urldecode($response['Devotee_Last_Name']); // "Gupta" 
            }

            if (!empty($response['Devotee_ID_Type'])) {
                $devotee_id_type = urldecode($response['Devotee_ID_Type']); // NULL     
            }

            if (!empty($response['Devotee_ID_Number'])) {
                $devotee_id_number = urldecode($response['Devotee_ID_Number']); // "" 
            }

            if (!empty($response['Devotee_Station'])) {
                $devotee_station = urldecode($response['Devotee_Station']); // "New+Delhi" 
            }

            if (!empty($response['Devotee_Cell_Phone_Number'])) {
                $devotee_cell_phone_number = urldecode($response['Devotee_Cell_Phone_Number']); //  "4156227879" 
            }

            if (!empty($response['Devotee_Status'])) {
                $devotee_status = urldecode($response['Devotee_Status']); // "A" ;
            }

            if (!empty($response['Devotee_Remarks'])) {
                $devotee_remarks = urldecode($response['Devotee_Remarks']); //  "" 
            }

            if (!empty($response['Devotee_Referral'])) {
                $devotee_referral = urldecode($response['Devotee_Referral']); //  "" 
            }
            
            if (!empty($response['Accomodation_Key'])) {
                $devotee_accommodation_id = urldecode($response['Accomodation_Key']); //  "" 
            }

            if (!empty($response['Seva_ID'])) {
                $devotee_seva_id = urldecode($response['Seva_ID']); //  "" 
            }
            
            if (!empty($response['Devotee_Address_1'])) {
                $Devotee_Address_1 = urldecode($response['Devotee_Address_1']); //  "" 
            }
            if (!empty($response['Devotee_Address_2'])) {
                $Devotee_Address_2 = urldecode($response['Devotee_Address_2']); //  "" 
            }
            if (!empty($response['Devotee_State'])) {
                $Devotee_State = urldecode($response['Devotee_State']); //  "" 
            }
            if (!empty($response['Devotee_Zip'])) {
                $Devotee_Zip = urldecode($response['Devotee_Zip']); //  "" 
            }
            if (!empty($response['Devotee_Country'])) {
                $Devotee_Country = urldecode($response['Devotee_Country']); //  "" 
            }
            if (!empty($response['Comments'])) {
                $Comments = urldecode($response['Comments']); //  "" 
            }
            if (!empty($response['Joined_Since'])) {
                $Joined_Since = urldecode($response['Joined_Since']); //  "" 
            }
            
            //$devotee_accommodation_id="";
            //$response['Devotee_Gender']; // "" 
            ////$response['Devotee_Record_Update_Date_Time']; // "2018-10-14 07:23:23" 
            //$response['Devotee_Record_Updated_By']; //  "0"         
            //$response['Devotee_ID_XML']; // NULL 
            //$response['Photo_type']; // NULL 
            if (!empty($response['Devotee_Photo'])) {  // NULL
                $devotee_photo = $response['Devotee_Photo'];
                //var_dump($devotee_photo);die;
            }
            //var_dump($response);die;
            if (!empty($response['Devotee_ID_Image'])) {  // NULL
                $devotee_id_image = $response['Devotee_ID_Image'];
            }
        }
        ?>
        <script>

            function _(el) {
                return document.getElementById(el);
            }

            //javascript function for ajax call
            function saveFormData(formId, flag) {
                var r =null; // so that we can access it outside .ajax();
                var formData = $(formId).serialize();
                var updateSuccess = false;
                //alert(formData);
                if (validateInput()) {
                    $.ajax({
                        url: '<?=$config_data['webroot'];?>Logic/requestManager.php',
                        type: 'POST',
                        data: formData,
                        async: false,
                        success: function (response) {
                            //alert(response);
                            r = JSON.parse(response);

                            if (r['flag'] == true) {
                                //                    alert("Devotee record updated successfully!");
                                //                    window.location.assign("/KDMS/UI/adddevoteei.php?devotee_key=" + r['info'] );
                                updateSuccess = true;
                            } else {
                                alert(r['message']);
                                updateSuccess = false;
                            }
                        }
                    });
                    //Save and stay on the record
                    if (flag == 1 && updateSuccess) {
                        alert("Devotee record updated successfully!");
                        window.location.assign("<?=$config_data['webroot'];?>UI/addDevoteeI.php?devotee_key=" + r['info']);
                    }
                    //save and Print
                    if (flag == -1 && updateSuccess) {

                        $.ajax({
                            url: '<?=$config_data['webroot'];?>Logic/requestManager.php',
                            type: 'POST',
                            data: {'devotee_key': document.getElementById("devotee_key").value, 'requestType': "addToPrintQueue"},
                            async: false,
                            success: function (response) {

                                var r = JSON.parse(response);

                                if (r['flag'] == true) {
                                    alert("Devotee Record updated and card added to Print Queue!");
                                    window.location.assign("<?=$config_data['webroot'];?>UI/devoteeSearchResult.php?mode=SET&key=CTP");
                                } else {
                                    alert(r['message']);
                                    updateSuccess = false;
                                }
                            }
                        });

                        //          document.getElementById("myForm").action = "/KDMS/Logic/requestManager.php";
                        //          document.getElementById("myForm").method = "POST";
                        //          document.getElementById("requestType").value = "addToPrintQueue";
                        //
                        //          document.getElementById(formId).submit();
                        //          
                        //          window.location.assign("./devoteeSearchResult.php?mode=SET&key=CTP");
                    }
                    //save and exit
                    if (flag == 0 && updateSuccess) {
                        alert("Devotee record updated successfully!");
                        window.location.assign("<?=$config_data['webroot'];?>UI/index.php");
                    }
                    /*
                     document.getElementById("myForm").action = "/KDMS/Logic/requestManager.php";
                     document.getElementById("myForm").method = "POST";
                     document.getElementById(formId).submit();
                     */
                }
            }
            function validateInput() {
                var response = true;
                var message = "";
                if (document.getElementById("devotee_first_name").value == "") {
                    message = "Devotee first name";
                    response = false;
                }

                if (document.getElementById("devotee_last_name").value == "") {
                    if (message != "") {
                        message = message + " and last name";
                    } else {
                        message = "Devotee last name";
                    }
                    response = false;
                }

                if (!response) {
                    alert(message + " missing!!");
                }

                return response;
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
                                                        <label class="bmd-label-floating">Devotee ID (non editable)</label>
                                                        <input type="text" name="devotee_key" id="devotee_key" class="form-control" readonly="true" value="<?php print_r($devotee_key); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-7">
                                                        <div id="qrcode" align="right"></div>
                                                        <script type="text/javascript">
                                                            new QRCode(document.getElementById("qrcode"), {
                                                            text: document.getElementById("devotee_key").value,
                                                            width: 100,
                                                            height: 100,
                                                            colorDark : "#000000",
                                                            colorLight : "#ffffff",
                                                            correctLevel : QRCode.CorrectLevel.H
                                                            }
                                                        );
                                                </script>
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
                                                            <option value="none" <?php
                                                            if ($devotee_id_type == "none" || empty($devotee_id_type)) {
                                                                print_r("selected");
                                                            }
                                                            ?>>--Not Selected--</option>
                                                            <option value="Adhaar" <?php
                                                            if ($devotee_id_type == "Adhaar") {
                                                                print_r("selected");
                                                            }
                                                            ?>>Addhar</option>
                                                            <option value="DL" <?php
                                                            if ($devotee_id_type == "DL") {
                                                                print_r("selected");
                                                            }
                                                            ?>>DL</option>
                                                            <option value="Other" <?php
                                                            if ($devotee_id_type == "Other") {
                                                                print_r("selected");
                                                            }
                                                            ?>>Other Gov. ID</option>
                                                            <option value="PAN" <?php
                                                            if ($devotee_id_type == "PAN") {
                                                                print_r("selected");
                                                            }
                                                            ?>>PAN</option>
                                                            <option value="Passport" <?php
                                                            if ($devotee_id_type == "Passport") {
                                                                print_r("selected");
                                                            }
                                                            ?>>Passport</option>
                                                            <option value="Voter ID" <?php
                                                            if ($devotee_id_type == "Voter ID") {
                                                                print_r("selected");
                                                            }
                                                            ?>>Voter ID</option>
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
                                                            <option value="P" <?php
                                                            if ($devotee_type == "p" || empty($devotee_type) || $devotee_type == "P") {
                                                                print_r("selected");
                                                            }
                                                            ?>>Permanent</option>
                                                            <option value="T" <?php
                                                            if ($devotee_type == "t" || $devotee_type == "T") {
                                                                print_r("selected");
                                                            }
                                                            ?>>Temporary</option>
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
                                                        <label class="bmd-label-floating">Accommodation</label>

                                                        <select type="text" class="form-control" name="devotee_accommodation_id" id="devotee_accommodation_id" >
                                                            <?php
                                                            foreach ($accommodations as $accommodation) {
                                                                print_r("<option value='" . $accommodation['accomodation_key'] . "'");
                                                                if (empty($devotee_accommodation_id)) {
                                                                    $devotee_accommodation_id = 'OWN';
                                                                }
                                                                if ($devotee_accommodation_id == $accommodation['accomodation_key']) {
                                                                    print_r("selected");
                                                                }
                                                                Print_r(">" . $accommodation['Accomodation_Name'] . " - " . $accommodation['Available_Count'] . "</option>");
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="bmd-label-floating">Assigned Seva</label>

                                                        <select type="text" class="form-control" name="devotee_seva_id" id="devotee_seva_id" >
                                                            <?php
                                                            foreach ($sevas as $seva) {
                                                                print_r("<option value='" . $seva['Seva_Id'] . "'");
                                                                if (empty($devotee_seva_id)) {
                                                                    $devotee_seva_id = 'UN';
                                                                }
                                                                if ($devotee_seva_id == $seva['Seva_Id']) {
                                                                    print_r("selected");
                                                                }
                                                                //Print_r(">" . $seva['Seva_Description'] . " - " . $accommodation['Available_Count'] . "</option>");
                                                                Print_r(">" . urldecode($seva['Seva_Description']) . "</option>");
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group" style="margin-top:62px;">
                                                        <label class="bmd-label-floating">Referral</label>
                                                        <input type="text" class="form-control" name="devotee_referral" id="devotee_referral" value="<?php print_r($devotee_referral); ?>">
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="form-group" style="margin-top:62px;">
                                                        <label class="bmd-label-floating">Joined Since</label>
                                                        <input type="text" class="form-control" name="joined_since" id="joined_since" value="<?php print_r($Joined_Since); ?>">
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group" >
                                                        <label class="bmd-label-floating">Address Line 1</label>
                                                        <input type="text" class="form-control" name="devotee_address_1" id="devotee_address_1" value="<?php print_r($Devotee_Address_1); ?>">
                                                    </div>
                                                </div>
<!--                                                <div class="col-md-4">
                                                    <div class="form-group" style="margin-top:62px;">
                                                        <label class="bmd-label-floating">Phone No.</label>
                                                        <input type="text" class="form-control" name="devotee_cell_phone_number" id="devotee_cell_phone_number" value="<?php print_r($devotee_cell_phone_number); ?>">
                                                    </div>
                                                </div>-->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="bmd-label-floating">Address Line 2</label>

                                                        <input type="text" class="form-control" name="devotee_address_2" id="devotee_address_2" value="<?php print_r($Devotee_Address_2); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group" >
                                                        <label class="bmd-label-floating">State</label>
                                                        <input type="text" class="form-control" name="devotee_state" id="devotee_state" value="<?php print_r($Devotee_State); ?>">
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="bmd-label-floating">Zip/Pin Code</label>
                                                        <input type="text" class="form-control" name="devotee_zip" id="devotee_zip" value="<?php print_r($Devotee_Zip); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="bmd-label-floating">Country</label>
                                                        <input type="text" class="form-control" name="devotee_country" id="devotee_country" value="<?php print_r($Devotee_Country); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Remarks</label>
                                                        <div class="form-group">
                                                            
                                                            <!--<label class="bmd-label-floating"> Add additional Information</label>-->
                                                            <textarea class="form-control" rows="2" name="devotee_remarks" id="devotee_remarks"> <?php print_r($devotee_remarks); ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div><div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Feedback/Coments</label>
                                                        <div class="form-group">
                                                            
                                                            <!--<label class="bmd-label-floating"> Add additional Information</label>-->
                                                            <textarea class="form-control" rows="2" name="comments" id="comments"> <?php print_r($Comments); ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="requestType" id="requestType" value="upsertDevotee">
                                            <button type="reset" class="btn btn-success pull-right">Cancel</button>                    
                                            <button type="button" class="btn btn-success pull-right" onclick="saveFormData('#myForm', 0); return false;">Save and Exit</button>
                                            <button type="button" class="btn btn-success pull-right" onclick="saveFormData('#myForm', -1);
                                                    return false;">Save and Generate Card</button>
                                            <button type="button" class="btn btn-success pull-right" onclick="saveFormData('#myForm', 1);
                                                    return false;" >Save</button>
                                        </form>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card card-profile">
                                    <div class="card-body" style="height:280px;" data-toggle="modal" data-target="#CameraModalLong" data-backdrop="static"  data-keyboard="false">


                                        </br>

<!--                  <img src="../assets/img/faces/devotee.ico" alt="devotee image" height="200px" width="200px"></img>-->
                                        <?php
                                        if ($devotee_photo == "") {
                                            echo '<div  id="photo2"><img src="../assets/img/faces/devotee.ico" alt="devotee image" height="200px" width="220px"></img></div>';
                                        } else {
                                            echo '<div  id="photo2"><img src="data:image/jpeg;base64,' . $devotee_photo . '" alt="devotee image" width="240px"></img></div>';
                                        }
                                        ?> 
                                        <canvas id="canvas2" style="margin-top:-5%;"></canvas>
                                    </div>
                                </div>
                                <div class="card card-profile">
                                    <div class="card-body" style="height:280px;" data-toggle="modal" data-target="#IDModalLong" data-backdrop="static"  data-keyboard="false">
                                        </br>
                                        <!--<img src="../assets/img/faces/doc.png" alt="devotee image" height="200px" width="200px"></img>-->
                                        <?php
                                        if ($devotee_id_image == "") {
                                            echo '<img src="../assets/img/faces/doc.png" alt="devotee ID" height="200px" width="200px"></img>';
                                        } else {
                                            echo '<img src="data:image/jpeg;base64,' . $devotee_id_image . '" alt="devotee ID" height="200px" width="200px"></img>';
                                        }
                                        ?> 
                                    </div>
                                </div>
                                <?php if ($devotee_key != "") { ?>
                                    <div class="card card-profile">
                                        <div class="card-body" style="height:80px;" >

                                            <button class="btn btn-primary btn-med" data-toggle="modal" data-target="#AmenityModalLong">
                                                Manage Amenities
                                            </button>

                                        </div>
                                    </div>
                                    <!--Modal Window for Amenity Management-->
                                    <?php include_once("amenityMgmtModal.php"); ?>

                                    <!--END - Modal Window for Amenity Management-->                
                                <?php } ?>
                            </div>

                        </div>
                    </div>
                </div>
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
    <div class="modal fade" id="IDModalLong" tabindex="-2" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Upload ID</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" action="../api/managePhoto.php">
                    <div class="modal-body" >
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <?php
                                    echo '<input type="hidden" name="request_from" value="addDevoteeI.php">';
                                    // If devotee key is available , add key to update existing data
                                    if ($is_key_available) {
                                    echo '<input type="hidden" name="devotee_key" value="' . $devotee_key . '">';
                                    }// Add api type 
                                    echo '<input type="hidden" name="api_type" value="4">'; // type=document 
                                    ?>
                                    <label for="devotee-id-scan">Please select the scanned document.</label>
                                    <input type="file" style="opacity:1;position:static;" class="form-control-file" id="devotee-id-scan" name="devotee-id-scan">
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>




</div>
<!--   Core JS Files   -->
<?php include_once("scriptJS.php") ?>
<script src="../assets/js/pages/capture.js"></script>

</body>

</html>
