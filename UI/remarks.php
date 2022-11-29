
<div class="modal fade" id="RemarksModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog kdms-modal" role="document">
        <div class="modal-content">
            <form id="remarkForm">
                <div class="modal-body">
                    <div class="card">
                        <div class="col-md-12">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Add Remarks</h4>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="bmd-label-floating">Remark Type </label>
                                                <select type="text" class="form-control" name="remark_type"
                                                    id="remark_type" value="MISC">
                                                    <option value="MISC" selected>Miscellenous</option>
                                                    <option value="ATTENDANCE">Attendance</option>
                                                    <option value="ACCOMMODATION">Accommodation</option>
                                                    <option value="SEVA">Seva</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="bmd-label-floating">Rating </label>
                                                <select type="text" class="form-control" name="rating" id="rating"
                                                    value="5">
                                                    <option value="5" selected>Excellent</option>
                                                    <option value="4">Very Good</option>
                                                    <option value="3">Good</option>
                                                    <option value="2">Fair</option>
                                                    <option value="1">Can be better</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="bmd-label-floating">Remark (Optional) </label>
                                                <textarea class="form-control" rows="1" name="remark"
                                                    id="remark"> </textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="requestType" id="requestType" value="upsertRemark">
                    <input type="hidden" name="eventId" id="eventId" value="<?php echo $eventId; ?>">
                    <input type="hidden" name="userId" id="userId" value="<?php echo $userId; ?>">
                    <input type="hidden" id="devotee_key" name="devotee_key" value="<?php print_r($devotee_key); ?>">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="save-amenity" type="button" class="btn btn-primary"
                        onclick="submitRemark('#remarkForm'); return false;">Submit Remark</button>                    
                </div>
            </form>
        </div>
    </div>
</div>

<script>

    //javascript function for ajax call
    function submitRemark(formId) {
        var formData = $(formId).serialize();
        //alert(formData);
        if (validateInput()) {
            $.ajax({
                url: '<?= $config_data['webroot'] ?>Logic/requestManager.php',
                type: 'POST',
                data: formData,
                success: function (response) {
                    // alert(response);
                    var r = JSON.parse(response);

                    if (r['flag'] == true) {
                        //alert("Remark submitted successfully!");
                        //clearRemarkForm(formId);  
                        demo.showCustomAlert('bottom','center', 'success', 'Remark submitted successfully!');
                    } else {
                        demo.showCustomAlert('bottom','center', 'danger', r['message']);
                        //alert(r['message']);
                        updateSuccess = false;
                    }
                }
            });
        }

        //   document.getElementById("myForm").action = "/KDMS/Logic/requestManager.php";
        //   document.getElementById("myForm").method = "POST";
        //   document.getElementById(formId).submit();

    }

    function clearRemarkForm(formId) {
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

    function validateRemark(formId) {
        var valueEntered = false;
        var valueNonNumber = false;

        for (i = 0; i < document.getElementById(formId).length; i++) {
            if (document.getElementById(formId)[i].type == "text") {
                if (document.getElementById(formId)[i].value.trim().length == 0) {
                    alert("Please enter number to issue or return amenity.");
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
</script>