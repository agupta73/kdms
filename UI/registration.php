<?php
$is_photo_available = false;
$is_doc_available = false;
$is_key_available = false;
if (!empty($_GET['devotee_key'])) { // If this page is called after clicking picture or ID scan, and devotee id is passed
    $devotee_key = $_GET['devotee_key'];
    $is_key_available = true;
    include_once '../api/config/database.php';
    include_once '../api/Interface/devotees.php';

    $database = new Database();
    $db = $database->getConnection();

    $requestData['mode'] = "KEY";
    $requestData['key'] = $devotee_key; // Todo here, sql enjection preventation.
    //
    $devotee = new Devotee($db);
    try {
        $new_devotee = $devotee->search($requestData);
    } catch (Exception $e) {
        echo $e->getMessage();
        die;
    }
    if (!empty($new_devotee->Devotee_Photo)) {
        $is_photo_available = true;
    }
    if (!empty($new_devotee->Devotee_ID_Image)) {
        $is_doc_available = true;
    }

//
}
?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <title>
            KDMS (Registration)
        </title>
        <?php
        include_once("header.php");
        ?>

<!--  <script src="../assets/js/jquery-3.2.1.min.js"></script>
  <style>
    #search-data{
      padding: 10px;
      border: solid 1px #BDC7D8;
      border-radius: 7px;
      margin-bottom: 20px;
      display: inline;
      background-color: rgb(255, 255, 255);
      width: 100%;
      color: grey;

    }
    .search-result{
      padding:10px;
      background-color: rgb(255, 255, 255);
      font-size: 16px;color:grey;
    }
  .search-result{
    border:solid 1px grey;
  }
  </style>

    <script>
      $(document).ready(function() {
      $('#search-data').unbind().keyup(function(e) {
          var value = $(this).val();
          if (value.length>2) {
                //alert(99933);
              searchData(value);
          } else {
               $('#search-result-container').hide();
          }
      });
      });

      function searchData(val){
        $('#search-result-container').show();
        $('#search-result-container').html('<div><img src="../assets/img/preloader.gif" width="50px;" height="50px"> <span style="font-size: 20px;">Please Wait...</span></div>');
        $.post('../api/jQuerySearchDevotee.php',{'search-data': val}, function(data){

                if(data != "")
                        $('#search-result-container').html(data);
              else
                $('#search-result-container').html("<div class='search-result'>No Result Found...</div>");
        }).fail(function(xhr, ajaxOptions, thrownError) { //any errors?

           alert(thrownError); //alert with HTTP error
        });
      }

  </script>-->


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
                <!-- End Navbar -->
                <div class="content">
                    <div class="container-fluid">
                        <!--          <div style="width: 500px;z-index:5;position:absolute;top:50px;left:50px;">
                                  <div id="search-box-container" >
                                  <input  type="text" id="search-data" name="searchData" placeholder="Search By name, phone or station ..." autocomplete="off" />
                                  </div>
                                  <div id="search-result-container" style="display:none;position:relative;">
                                       search results comes over here 
                                  </div>
                                  </div>-->
                        <div class="card"  style="z-index:0;">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Photo and ID Scan</h4>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                </div>
                                <div class="col-md-4">
                                    <div class="card card-profile">
                                        <div class="card-body" style="height:380px;">
                                            <h4 class="card-title">Devotee Photo</h4>
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#CameraModalLong" data-backdrop="static"  data-keyboard="false">
                                                Open Camera
                                            </button>
                                            </br>
                                            </br>

                                            <?php
                                            if (!$is_photo_available) {
                                                ?>
                                                <img src="../assets/img/faces/devotee.ico" alt="devotee image" height="200px" width="220px"></img>
                                                <?php
                                            } else {
                                                ?>
                                                <?php
                                                echo '<img class="img-responsive"  width="250px" src="data:image/jpeg;base64,' . $new_devotee->Devotee_Photo . '"/>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card card-profile">
                                        <div class="card-body" style="height:380px;">
                                            <h4 class="card-title">Devotee ID Scan</h4>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#IDModalLong" data-keyboard="false" data-backdrop="static">
                                                Upload ID
                                            </button>
                                            </br>
                                            </br>
                                            <?php
                                            if (empty($is_doc_available)) {
                                                ?>                                                
                                                <img src="../assets/img/faces/doc.png" alt="devotee image" height="200px" width="200px"></img>
                                                <?php
                                            } else {
                                                ?>
                                                <?php
                                                echo '<img class="img-responsive"  width="250px" src="data:image/jpeg;base64,' . $new_devotee->Devotee_ID_Image . '"/>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-10">
                            <div class="card-body">
                                <form>
                                    <button type="reset" class="btn btn-success pull-right">Cancel</button>
                                    <button type="submit" class="btn btn-success pull-right">Update existing Devotee</button>
                                    <button type="submit" class="btn btn-success pull-right">Register New Devotee</button>
                                    <button type="submit" class="btn btn-success pull-right">Save for later</button>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
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
                            <input type="hidden" id="devotee_key_modal" name="devotee_key_modal" value="<?=$devotee_key;?>">
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
                                            echo '<input type="hidden" name="request_from" value="registration.php">';
                                    
                                            // If devotee key is available , add key to update existing data
                                            if ($is_key_available) {
                                                echo '<input type="hidden" name="devotee_key" value="' . $devotee_key . '">';
                                            }
                                            // Add api type 
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
