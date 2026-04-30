
<div class="modal fade" id="FavModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog kdms-modal" role="document">
        <div class="modal-content">
            <form id="favForm">
                <div class="modal-body">
                    <div class="card">
                        <div class="col-md-12">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Add Remarks</h4>
                            </div>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">                                      
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="bmd-label-floating">Private/Public </label>
                                                <select type="text" class="form-control" name="fav_public" id="fav_public"
                                                    value="NO">
                                                    <option value="NO" selected>Private - available to me only</option>
                                                    <option value="YES">Public - everyone can see thie report</option>                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="bmd-label-floating">Favorite Name </label>
                                                <textarea class="form-control" rows="1" name="fav_name"
                                                    id="fav_name"> </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="type" id="type" value="upsertFav">                    
                    <input type="hidden" name="user_key" id="user_key" value="<?php print_r($_SESSION['LoginID']); ?>">
                    <input type="hidden" name="fav_type" id="fav_type" value="REPORT">   
                    <input type="hidden" id="fav_url" name="fav_url" value="">
                    <input type="hidden" name="fav_updated_by" id="fav_updated_by" value="<?php print_r($_SESSION['LoginID']); ?>">   
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button id="save-fav" type="button" class="btn btn-primary" onclick="submitFav('#favForm'); return false;">Submit Favorite</button>                    
                </div>
            </form>
        </div>
    </div>
</div>


    <script>

    
    //javascript function for ajax call
    function submitFav(formId) {
        var formData = $(formId).serialize();
        //alert(formData);
        if (validateFav()) {
            $.ajax({
                url: '../Logic/requestManager.php',
                type: 'POST',
                data: formData,
                success: function (response) {
                    //alert('from the page:' + response);
                    var r = JSON.parse(response);
                    console.log(r);

                    if (r['status'] == true) {
                        alert("Favorite added successfully!");                        
                        $('#FavModalLong').modal('hide');                        
                    } else {                    
                        alert(r['message']+ '\n Please make sure that the favorite name is not duplicate.');
                        updateSuccess = false;
                    }
                }
            });
        }

        //   document.getElementById("myForm").action = "/KDMS/Logic/requestManager.php";
        //   document.getElementById("myForm").method = "POST";
        //   document.getElementById(formId).submit();

    }

    function clearRemarkForm() {
        /*  for (i = 0; i < document.getElementById(formId).length; i++) {
           if (document.getElementById(formId)[i].type == "text") {
                document.getElementById(formId)[i].value = "";        
            
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
        }} */
        document.getElementById('remark').value = "";
        document.getElementById('devotee_key').value = "";
        //alert($(document.getElementById('remark')).value);
    }

    function validateFav() {
            var res = true;
            if (document.getElementById('user_key').value == "") {
                alert("User Key not specified.");
                res = false;
            } 
            
            if (document.getElementById('fav_name').value == "") {
                alert("Report Name not specified.");
                res = false;
            } 
            return res;
        }

    $(document).ready(function() {
        $("#fav_url").val(window.location.href);     
    });
</script>
