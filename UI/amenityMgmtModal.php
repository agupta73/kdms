<form id="formAmenity">
  <div class="modal fade" id="AmenityModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog kdms-modal" role="document">
      <div class="modal-content">
        <div class="card-header card-header-primary">
          <h4 class="card-title">Allocated Amenities</h4>
        </div>
        <div class="modal-body">
          <div class="card">
            <div class="row">
              <div class="col-md-12">
                <?php
                $devoteeAmenitySearch = new clsDevoteeSearch($requestData);
                $amenityResponse = $devoteeAmenitySearch->getDevoteeAmenities($eventId);
                unset($devoteeAmenitySearch);
                if (!empty($amenityResponse)) {
                  if (empty($amenityResponse['message'])) {
                ?>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead class=" text-primary">
                      <th>
                        Amenity Type
                      </th>
                      <th>
                        Allocated Qty
                      </th>
                      <th>
                        Available Qty
                      </th>
                      <th>
                        Issue More
                      </th>
                      <th>
                        Return
                      </th>
                    </thead>
                    <tr>
                      <?php
                    foreach ($amenityResponse as $key => $amenityValue) {
                      print_r("<td style='width: 150px;font-weight: bold;font-size: medium'>");
                      print_r($amenityValue['Amenity_Name']);
                      print_r("</td><td style='width: 150px' align='center'>");
                      print_r("<input type='text' class='form-control' align='center' readonly='true' id='L" . $amenityValue['Amenity_Key'] . "' value='" . $amenityValue['Amenity_Quantity'] . "'");
                      print_r("</td><td style='width: 150px' align='center'>" . $amenityValue['Available_Count']);
                      print_r("</td><td><input type='text' id='I" . $amenityValue['Amenity_Key'] . "'");
                      print_r("</td><td><input type='text' id='R" . $amenityValue['Amenity_Key'] . "'");
                      print_r("</td>");
                      if ($key < count($amenityResponse)) {
                        print_r("</tr><tr>");
                      }
                    }
                      ?>
                    </tr>
                  </table>
                </div>
                <?php
                  } else { ?>
                <table>
                  <tr>
                    <td style="align-content: center">
                      No amenities allocated yet..
                    </td>
                  </tr>
                </table>

                <?php
                  }
                }
                ?>
              </div>
              <?php
              if (!empty($amenities)) {

              ?>
              <div class="col-md-8">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Issue/Return Amenities</h4>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover" style="width: 500px">
                    <thead class=" text-primary">
                      <th>
                        Amenity Type
                      </th>
                      <th align='right'>
                        Available Quantity
                      </th>
                      <th align='right'>
                        Issue
                      </th>
                      <th align='right'>
                        Return
                      </th>
                    </thead>
                    <?php
                foreach ($amenities as $key => $amenityRecord) {

                  print_r("<tr>");
                  print_r("<td>");
                  print_r($amenityRecord['Amenity_Name']);
                  print_r("</td>");
                  print_r("<td>");
                  print_r($amenityRecord['Available_Count']);
                  print_r("</td>");
                  print_r("<td>");
                  print_r("<input type='text' id='I" . $amenityRecord['amenity_key'] . "'");
                  print_r("</td>");
                  print_r("<td>");
                  print_r("<input type='text' id='R" . $amenityRecord['amenity_key'] . "'");
                  print_r("</td>");
                  print_r("</tr>");
                } ?>
                  </table>
                </div>
              </div>

              <?php


              }
              ?>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button id="save-amenity" type="button" class="btn btn-primary"
            onclick="saveAmenityData('formAmenity'); return false;">Save Changes</button>
          <input type="hidden" id="devotee_key" name="devotee_key" value="<?php print_r($devotee_key); ?>">
          <input type="hidden" id="requestType" name="requestType" value="manageAmenity">
          <input type="hidden" id="amenity_key" name="amenity_key">
          <input type="hidden" id="amenity_quantity" name="amenity_quantity">
          <input type="hidden" id="eventId" name="eventid" value="<?php echo $eventId; ?>">
        </div>
      </div>
    </div>
  </div>

</form>
<script>

  //javascript function for ajax call
  function saveAmenityData(formId) {

    if (validateAmenity(formId)) {
      var strKey = "";
      var strVal = "";
      var updateSuccess = true;

      for (i = 0; i < document.getElementById(formId).length; i++) {

        if (document.getElementById(formId)[i].type == "text") {

          if (document.getElementById(formId)[i].id.substring(0, 1) == "I" && document.getElementById(formId)[i].value.trim().length != 0) {
            strKey = strKey + document.getElementById(formId)[i].id.substring(1, document.getElementById(formId)[i].id.length) + ",";
            strVal = strVal + document.getElementById(formId)[i].value.trim() + ",";
          }
          else if (document.getElementById(formId)[i].id.substring(0, 1) == "R" && document.getElementById(formId)[i].value.trim().length != 0) {
            strKey = strKey + document.getElementById(formId)[i].id.substring(1, document.getElementById(formId)[i].id.length) + ",";
            strVal = strVal + "-" + document.getElementById(formId)[i].value.trim() + ",";
          }
        }
      }

      var formData = {
        'devotee_key': document.getElementById("devotee_key").value,
        'amenity_key': strKey.substring(0, strKey.length - 1),
        'amenity_quantity': strVal.substring(0, strVal.length - 1),
        'requestType': "manageAmenity",
        'eventId': document.getElementById("eventId").value
      };

      $.ajax({
        url: '/KDMS/Logic/requestManager.php',
        type: 'POST',
        data: formData,
        async: false,
        success: function (response) {
          //alert(response);
          var r = JSON.parse(response);
          if (r['flag'] == true) {
            alert("Amenity successfully updated!");
            clearForm(formId);
            $('#AmenityModalLong').modal('hide');
          }
          else {
            alert(r['message']);
            updateSuccess = false;
          }
        }
      });
    }
  }

  function clearForm(formId) {
    for (i = 0; i < document.getElementById(formId).length; i++) {
      /* if (document.getElementById(formId)[i].type == "text") {
          document.getElementById(formId)[i].value = "";        
      } */
      strID = document.getElementById(formId)[i].id;
      strLabelID = "";
      if (strID.substring(0, 1) == "I" && document.getElementById(strID).value.trim().length != 0) {
        strLabelID = strID.replace("I", "L");
        document.getElementById(strLabelID).value = parseInt(document.getElementById(strLabelID).value) + parseInt(document.getElementById(strID).value);
        document.getElementById(strID).value = "";
      }
      else if (strID.substring(0, 1) == "R" && document.getElementById(strID).value.trim().length != 0) {
        strLabelID = strID.replace("R", "L");
        document.getElementById(strLabelID).value = parseInt(document.getElementById(strLabelID).value) - parseInt(document.getElementById(strID).value);
        document.getElementById(strID).value = "";
      }
    }
  }

  function validateAmenity(formId) {
    var valueEntered = false;
    var valueNonNumber = false;

    for (i = 0; i < document.getElementById(formId).length; i++) {
      if (document.getElementById(formId)[i].type == "text") {
        if (document.getElementById(formId)[i].value.trim().length != 0) {
          //alert(document.getElementById(formId)[i].id.substring(1,document.getElementById(formId)[i].id.length));
          valueEntered = true;
          valueNonNumber = isNaN(document.getElementById(formId)[i].value.trim());
        }
      }
    }
    if (!valueEntered || valueNonNumber) {
      alert("Please enter number to issue or return amenity.");
      return false;
    } else {
      return true;
    }
  }
</script>