<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/web_session.php';

$config_data = include dirname(__DIR__) . '/site_config.php';

require_once dirname(__DIR__) . '/Logic/clsOptionHandler.php';

$requestData = $_GET;
$eventId = $config_data['event_id'];

$accommodation_key = '';
$accommodation_name = '';
$available_count = 0;
$allocated_count = 0;
$accomodation_capacity = 0;
$reserved_count = 0;
$out_of_availability_count = 0;
$accomodation_updated_by = '';
$devotee_key = '';

function kdms_pick_accommodation_field(array $row, string ...$keys): mixed {
    foreach ($keys as $k) {
        if (array_key_exists($k, $row) && $row[$k] !== null && $row[$k] !== '') {
            return $row[$k];
        }
    }

    return null;
}

if (! empty($requestData['accommodation_key'])) {
    $optionHandler = new clsOptionHandler('AccommodationDetail');
    $optionHandler->setOptionKey($requestData['accommodation_key']);
    $optionHandler->setEventId($eventId);
    $response = $optionHandler->getOptions();

    unset($optionHandler);

    if (! is_array($response)) {
        $response = [];
    }

    if (! (isset($response['status']) && $response['status'] === false)) {
        if (kdms_pick_accommodation_field($response, 'Accomodation_Key', 'accomodation_key') !== null) {
            $accommodation_key = urldecode((string) kdms_pick_accommodation_field($response, 'Accomodation_Key', 'accomodation_key'));
        }

        if (kdms_pick_accommodation_field($response, 'Accomodation_Name', 'accomodation_name') !== null) {
            $accommodation_name = urldecode((string) kdms_pick_accommodation_field($response, 'Accomodation_Name', 'accomodation_name'));
        }

        if (kdms_pick_accommodation_field($response, 'Accomodation_Capacity', 'accomodation_capacity') !== null) {
            $accomodation_capacity = (int) kdms_pick_accommodation_field($response, 'Accomodation_Capacity', 'accomodation_capacity');
        }

        $av = kdms_pick_accommodation_field($response, 'Available_Count', 'available_count');
        if ($av !== null) {
            $available_count = (int) $av;
        }

        $al = kdms_pick_accommodation_field($response, 'Allocated_Count', 'allocated_count');
        if ($al !== null) {
            $allocated_count = (int) $al;
        }

        $rs = kdms_pick_accommodation_field($response, 'Reserved_Count', 'reserved_count');
        if ($rs !== null) {
            $reserved_count = (int) $rs;
        }

        $oa = kdms_pick_accommodation_field($response, 'Out_Of_Availability_Count', 'out_of_availability_count');
        if ($oa !== null) {
            $out_of_availability_count = (int) $oa;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>KDMS (Add Accommodation I)</title>
  <?php include_once 'header.php'; ?>
  <script>

    function _(el) {
      return document.getElementById(el);
    }


    //javascript function for ajax call
    function saveFormData(formId, flag) {

      var formData = $(formId).serialize();

      if (validateInput()) {
        $.ajax({
          url: '<?= $config_data['webroot'] ?>Logic/requestManager.php',
          type: 'POST',
          data: formData,
          success: function (response) {

            var r = JSON.parse(response);
            //alert(response);
            if (r['status'] == true) {
              alert("Accommodation record updated successfully!");
              window.location.assign("<?= $config_data['webroot'] ?>UI/addAccommodationI.php?accommodation_key=" + r['info']);
            }
            else {
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


    function validateInput() {
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
            <form id="myForm">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Add/Update Accommodation Information</h4>
                </div>
                <div class="card-body">

                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="bmd-label-floating">Accommodation Key </label>
                        <input type="text" name="accommodation_key" id="accommodation_key" class="form-control" maxlength="4" oninput="this.value = this.value.toUpperCase()"
                          value="<?php print_r($accommodation_key); ?>" <?php
                          if ($accommodation_key != "") {
                              print_r("readonly=true");
                            } ?>>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-group">
                        <label class="bmd-label-floating">Accommodation Name</label>
                        <input type="text" class="form-control" name="accommodation_name" id="accommodation_name"
                          value="<?php print_r($accommodation_name); ?>">
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="bmd-label-floating">Accommodation Capacity</label>
                        <input type="text" class="form-control" name="accomodation_capacity" id="accomodation_capacity"
                          value="<?php print_r($accomodation_capacity); ?>">
                      </div>
                    </div>

                  </div>
                  <div class="row">
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="bmd-label-floating">Spots Reserved</label>
                        <input type="text" class="form-control" name="reserved_count" id="reserved_count"
                          value="<?php print_r($reserved_count); ?>">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="bmd-label-floating"> Unavailable Spots</label>
                        <input type="text" class="form-control" name="out_of_availability_count"
                          id="out_of_availability_count" value="<?php print_r($out_of_availability_count); ?>">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="bmd-label-floating">Allocated Spots</label>
                        <input type="text" class="form-control" name="allocated_count" id="allocated_count"
                          readonly="true" value="<?php print_r($allocated_count); ?>">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="form-group">
                        <label class="bmd-label-floating">Available Spots</label>
                        <input type="text" class="form-control" name="available_count" id="available_count"
                          readonly="true" value="<?php print_r($available_count); ?>">
                      </div>
                    </div>

                  </div>


                </div>
                <div class="row">
                  <div class="col-md-6"></div>
                  <div class="col-md-6">
                    <input type="hidden" name="requestType" id="requestType" value="upsertAcco">
                    <input type="hidden" name="eventId" id="eventId" value="<?php echo $eventId; ?>">
                    <button type="reset" class="btn btn-success pull-right">Cancel</button>
                    <button class="btn btn-success pull-right"
                      onclick="saveFormData('#myForm', 1); return false;">Save</button>
                  </div>
                </div>
                <div class="clearfix"></div>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>

    <footer class="footer">

    </footer>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="CameraModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content" style="width:800px;height:500px;">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Camera</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <video class="photoImage" 
 id="video" width="180" height="230" autoplay></video>
              <button id="click-pic" class="btn btn-secondary">Snap Photo</button>
            </div>

            <div class="col-md-5">
              <canvas id="canvas"></canvas>
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
  <div class="modal fade" id="IDModalLong" tabindex="-2" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Upload ID</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
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
  </div>
  <!--   Core JS Files   -->
  <?php
  include_once("scriptJS.php") ?>
  <script src="../assets/js/pages/capture.js"></script>

</body>

</html>