function _(el) {
    return document.getElementById(el);
}
var newDevotee ={};

//javascript function for ajax call
function saveFormData(formId, flag) {
    var r =null; // so that we can access it outside .ajax();
    // Check if current devotee's data already saved.
    if (dataSaved) {
        if (!duplicateEntryBlocker(2)) {
            alert("Devotee record already added!"); 
            return false;
        }
        
    } 
    var formData = jQuery(formId).serialize();
    var updateSuccess = false;
    if (validateInput()) {
        jQuery.ajax({
            url: '/'+directoryName+'/Logic/requestManager.php',
            type: 'POST',
            data: formData,
            async: false,
            success: function (response) {
                console.log(response);
                    
                r = JSON.parse(response);

                if (r['flag'] == true) {
                    updateSuccess = true;
                    dataSaved = true;
                    document.getElementById("devotee_key").value = r['info'];
                    duplicateEntryBlocker(1);
                } else {
                    alert(r['message']);
                    updateSuccess = false;
                }
            }
        });
        //Save and stay on the record
        if (flag == 1 && updateSuccess) {
            alert("Devotee record updated successfully!");                        
            window.location.assign("/KDMS/UI/addDevoteeI.php?devotee_key=" + r['info']);
        }
        var check =false;
        if(flag== -2){
            var pcnt=parseInt($('.btn-sgc').attr('data-pcount'));
            var timet='times';
            if (pcnt<2){
                timet='time';
            }
             check=confirm("Card already printed "+pcnt+" "+timet+" for this Devotee!. Do you still want to print");                        
        }
        if(check){
              var flag = -1;
        }
        //save and Print
        if (flag == -1 && updateSuccess) {
            console.log("calling ajax to add devotee card. ;");   
            console.log('devotee_key' + r['info'] + 'requestType'+ "addToPrintQueue");
            $.ajax({
                url: '/KDMS/Logic/requestManager.php',
                type: 'POST',
                //data: {'devotee_key': document.getElementById("devotee_key").value, 'requestType': "addToPrintQueue"},
                data: {'devotee_key': r['info'], 'requestType': "addToPrintQueue"},
                async: false,
                success: function (response) {

                    var r = JSON.parse(response);

                    if (r['flag'] == true) {
                        alert("Devotee Record updated and card added to Print Queue!");
                        //window.location.assign("/KDMS/UI/devoteeSearchResult.php?mode=SET&key=CTP");
                        //window.location.assign("/KDMS/UI/addDevoteeI.php");
                        window.location.assign("/KDMS/UI/addDevoteeI.php?devotee_key=" + r['info']);
                    } else {
                        alert(r['message']);
                        updateSuccess = false;
                    }
                }
            });
        }
        //save and exit
        if (flag == 0 && updateSuccess) {
            alert("Devotee record updated successfully!");
            window.location.assign("/KDMS/UI/index.php");
        }
    }
}

function duplicateEntryBlocker(step) {
        var first_name = jQuery('#devotee_first_name').val();
        var last_name = jQuery('#devotee_last_name').val();
        var dob = jQuery('#devotee_dob').val();
        var id_number = jQuery('#devotee_id_number').val();
        var phone_number = jQuery('#devotee_cell_phone_number').val();
        var station = jQuery('#devotee_station').val();
        
    if (step == 1) {
        newDevotee.first_name = first_name;
        newDevotee.last_name = last_name;
        newDevotee.dob = dob;
        newDevotee.id_number = id_number;
        newDevotee.phone_number = phone_number;
        newDevotee.station = station;
    } else if (step == 2) {
        if (newDevotee.first_name == first_name && newDevotee.last_name == last_name && newDevotee.dob == dob
              && newDevotee.id_number == id_number && newDevotee.phone_number == phone_number && newDevotee.station == station) {
              return false; 
        }
    }
    return true;
}
function validateInput() {
    var response = true;
    var message = "";
    if (document.getElementById("devotee_first_name").value == "") {
        message = "Devotee first name is missing.\n";
        response = false;
    }

    if (document.getElementById("devotee_last_name").value == "") {
        message = message + "Devotee last name is missing. \n";
        response = false;
    }

    if (document.getElementById("devotee_email").value != "") {                        
        if (!validateEmail(document.getElementById("devotee_email").value)) {
            message = message + "Email is invalid.\n";
            response = false;
        }
    }

    if (document.getElementById("devotee_dob").value != "") {                        
        if (!validateDate(document.getElementById("devotee_dob").value)) {
            message = message + "Date of birth is invalid.\n";
            response = false;
        }
    }

    if (!response) {
        alert(message);
    }

    return response;
}

function validateEmail(email) {
    var re = /\S+@\S+\.\S+/;
    
    return re.test(email);
}

function validateDate(isoDate) {
    if (isNaN(Date.parse(isoDate))) {
        return false;
    } else {
        if (isoDate != (new Date(isoDate)).toISOString().substr(0, 10)) {
            return false;
        }
    }
    return true;
}
