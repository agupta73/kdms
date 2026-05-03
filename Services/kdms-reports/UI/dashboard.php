<html>

<head>
    <title> Report Dashboard </title>
</head>

<?php

$debug = false;
//session_start();
include_once("header.php");
$current_page_id = "KR-DSBRD";
include_once("../sessionCheck.php");
if ($debug) {
    var_dump($_SESSION);
    }
// include_once("../Logic/clsDevoteeSearch.php");
// include_once("../Logic/clsReportHandler.php");
// include_once("../Logic/clsOptionHandler.php");
// Include new config file in each page ,where we need data from configuration
include_once("../Logic/clsServicesManager.php");
$config_data = include("../site_config.php");

$eventId = $_SESSION['eventId'];

// TODO: build functionality to manage authorization to run report
// ================================================================= //


// ================================================================= //



/*
$accoType = "All";
if (!empty($_GET['accoType'])) {
$accoType = $_GET['accoType'];
}
$sevaType = "All";
if (!empty($_GET['sevaType'])) {
$sevaType = $_GET['sevaType'];
}
$AccoResponse = $getReport->getAccommodationRecords($accoType, $eventId);
unset($getReport);
if($debug){echo "Accociation response =: " ; var_dump($AccoResponse);}
*/

$getReport = new clsServicesManager(""); //no constructor variable needed
$getReport->setEventId($eventId);
$getReport->setOptionType("accoCounts");
$response = $getReport->getRecords();
unset($getReport);

$sevaSearch = new clsServicesManager("All"); //for all seva counts
$sevaSearch->setEventId($eventId);
$sevaSearch->setOptionType("sevaCounts"); //for different types of counts assigned
$sevaRes = $sevaSearch->getRecords();
unset($sevaSearch);

$sevaOptions = new clsServicesManager("Assigned"); //for all seva counts
$sevaOptions->setEventId($eventId);
$sevaOptions->setOptionType("sevaCounts"); //for different types of counts assigned
$sevaOptionsRes = $sevaOptions->getRecords();
unset($sevaOptions);


$dutyOptions = new clsServicesManager(""); //for all duty Locations
$dutyOptions->setOptionType("dutyLocations"); //for different types of duty locations
$dutyOptionsRes = $dutyOptions->getRecords();
unset($sevaOptions);

$accoOptions = new clsServicesManager("Allocated"); //for all allocated accommodations
$accoOptions->setEventId($eventId);
$accoOptions->setOptionType("accoCount"); //for accommodation name/count etc
$accoOptionsRes = $accoOptions->getRecords();
unset($accoOptions);

$request = array("user_key" => $_SESSION['LoginID'], "fav_type" => "REPORT", "type" => "favorites");

$userFav = new clsServicesManager($request); //for report type user favorites
$userFav->setOptionType("favorites"); //for user favorites
$userFavRes = $userFav->getRecords();
unset($accoOptions);

if ($debug) {
    var_dump($userFavRes);
    //var_dump($_SESSION['Access']);
    
}
?>

<script>
    //Debug only function.. do not use
    function clickHandler2(formId, flag) {

        //document.getElementById("requestType").value = "refreshAcco";
        var formData = $(formId).serialize();
        alert(formData);
        alert(formId);
        document.getElementById(formId).action = "<?= $config_data['webroot']; ?>Logic/requestManager.php";
        document.getElementById(formId).method = "POST";
        //document.getElementById("myFormID").data = formData;
        document.getElementById(formId).submit();

    }

    //javascript function for ajax call
    function clickHandler(formId, flag) {
        var formData = $(formId).serialize();

        <?php
        if ($debug) {
            echo " alert(formData);";
        }
        ?>
        if (validateInput()) {

            switch (flag) {
                case 1: //Refresh count

                    $.ajax({
                        url: "<?= $config_data['webroot']; ?>Logic/requestManager.php",
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            var r = JSON.parse(response);

                            if (r['status'] == true) {
                                alert("Accomodation count refreshed successfully!");
                            } else {
                                alert(response);
                            }
                        }
                    });
                    break;

                case 2: //Refresh count
                    document.getElementById("requestType").value = "refreshSeva";
                    formData = $(formId).serialize();

                    $.ajax({
                        url: "<?= $config_data['webroot']; ?>Logic/requestManager.php",
                        type: 'POST',
                        data: formData,
                        success: function (response) {
                            var r = JSON.parse(response);
                            if (r['status'] == true) {
                                alert("Seva count refreshed successfully!");
                            } else {
                                alert(response);
                            }
                        }
                    });
                    break;


                //                case 2: //Manage accommodations
                //                    document.getElementById("myForm").action = "addAccommodationII.php";
                //                    document.getElementById(formId).submit();
                //                    break;
                //
                default:
                    break;
            }
        }
    }
    function validateInput() {
        return true;
    }

    /**
     * Opens multiple report URLs in one user gesture. Do NOT use setTimeout — deferred opens
     * lose user activation and browsers block every popup after the first.
     * Checkboxes may have no value attribute (then .value is "" or "on"); selection is by id + checked.
     */
    function submitPrint(formId, flag) {
        var printForm = document.getElementById(formId);
        if (!printForm) {
            alert("Print form is not available.");
            return;
        }

        var urlsToOpen = [];
        var I = 0;

        for (I = 0; I < printForm.length; I++) {
            try {
                if (!printForm[I] || printForm[I].type !== "checkbox" || !printForm[I].checked) {
                    continue;
                }
                var option = printForm[I].value || "";
                var fid = printForm[I].id;

                var fallbackInput = document.getElementById("S" + fid);
                if (fallbackInput && typeof fallbackInput.value === "string") {
                    option = fallbackInput.value;
                }

                var selectId = "";
                try {
                    selectId = $("#" + fid).parent().siblings("td").children("select").prop("id") || "";
                } catch (e1) {
                    selectId = "";
                }

                if (selectId && typeof $.fn !== "undefined" && $.fn.select2) {
                    try {
                        var $sel = $("#" + selectId);
                        if ($sel.length && $sel.data && $sel.data("select2")) {
                            var data = $sel.select2("data");
                            if (Array.isArray(data) && data.length > 0) {
                                var txt = "";
                                $(data).each(function (idx, u) {
                                    var sVal = (u && typeof u.id !== "undefined") ? u.id : "";
                                    if (sVal === "") {
                                        return;
                                    }
                                    if (idx === 0 || txt === "") {
                                        txt = sVal;
                                    } else {
                                        txt = txt + "," + sVal;
                                    }
                                });
                                if (txt !== "") {
                                    option = txt;
                                }
                            }
                        }
                    } catch (e2) {
                        if (typeof console !== "undefined" && console.warn) {
                            console.warn("KMReports submitPrint select2", fid, e2);
                        }
                    }
                }

                var enc = encodeURIComponent(option);
                switch (fid) {
                    case "DRWP":
                        urlsToOpen.push("../reports/rptDutyReport.php?mode=ADS&photo_required=Y&key=" + enc);
                        break;
                    case "DRWOP":
                        urlsToOpen.push("../reports/rptDutyReport.php?mode=ADS&photo_required=N&key=" + enc);
                        break;
                    case "ADRWP":
                        urlsToOpen.push("../reports/rptAttendanceReport.php?mode=ADS&photo_required=N&key=" + enc);
                        break;
                    case "ODRWP":
                        urlsToOpen.push("../reports/rptOfficeDuty.php?mode=DutyReport&photo_required=Y&key=" + enc);
                        break;
                    case "ODRWOP":
                        urlsToOpen.push("../reports/rptOfficeDuty.php?mode=DutyReport&photo_required=N&key=" + enc);
                        break;
                    case "ARWP":
                        urlsToOpen.push("../reports/rptAcco.php?mode=AccoReport&photo_required=Y&key=" + enc);
                        break;
                    case "ARWOP":
                        urlsToOpen.push("../reports/rptAcco.php?mode=AccoReport&photo_required=N&key=" + enc);
                        break;
                    case "AAR":
                        urlsToOpen.push("../reports/rptAccoAvailability.php");
                        break;
                    default:
                        break;
                }
            } catch (eRow) {
                if (typeof console !== "undefined" && console.warn) {
                    console.warn("KMReports submitPrint row", eRow);
                }
            }
        }

        if (urlsToOpen.length === 0) {
            alert("Please select at least one report to print.");
            return;
        }

        /* Prefer programmatic <a target=_blank> clicks in one synchronous turn; multiple
           window.open calls are often reduced to one tab in Chromium-based browsers. */
        var j = 0;
        for (j = 0; j < urlsToOpen.length; j++) {
            var a = document.createElement("a");
            a.href = urlsToOpen[j];
            a.target = "_blank";
            a.rel = "noopener noreferrer";
            a.style.display = "none";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    }

    function generateReport(formId, flag) {
        printForm = document.getElementById(formId);
        //                var I = 0;
        var printString = ""
        //                for (I = 0; I < printForm.length; I++) {
        //                    if (printForm[I].value != "") {
        //                        //alert(searchForm[I].id + ": " + searchForm[I].value);
        //                        if (printForm[I].type == 'checkbox' && printForm[I].checked) {
        //                            printString = printString + "'" + encodeURI(printForm[I].value) + "',";
        //                        }
        //                    }
        //                }

        //                if (printString.length > 1) {

        window.open("./rptDutyReport.php?mode=CUS&key=devotee_accommodation_key=MP");
        //window.open("./rptCardsPrint.php?key=" + printString.substr(0, printString.length - 1) + "&mode=PCD");
        //window.location.assign("./devoteeSearchResult.php?mode=SET&key=CTP");

        //if(confirm("Card printed successfully?")){
        //                    $.ajax({
        //                        url: '<?= $config_data['webroot'] ?>Logic/requestManager.php',
        //                        type: 'POST',
        //                        data: {'devotee_key': printString.substr(0, printString.length - 1), 'requestType': "removeFromPrintQueue"},
        //                        async: false,
        //                        success: function (response) {
        //
        //                            var r = JSON.parse(response);
        //
        //                            if (r['flag'] == true) {
        //                                alert("Card removed from the printing queue!");
        //                                window.location.assign("./devoteeSearchResult.php?mode=SET&key=CTP");
        //                            } else {
        //                                alert(r['message']);
        //                                updateSuccess = false;
        //                            }
        //                        }
        //                    });
        //            }

        //                } else {
        //                    alert("Please select a card to print!");
        //                }
    }
    function exportTosheet(thisObj){
        var $row = $(thisObj).closest('tr');
        var $checkbox = $row.find("input[type='checkbox']").first();
        if ($checkbox.length === 0) {
            alert("Unable to identify report for export.");
            return;
        }

        var reportId = $checkbox.attr("id");
        var option = "";
        var fallbackInput = document.getElementById("S" + reportId);
        if (fallbackInput && typeof fallbackInput.value === "string") {
            option = fallbackInput.value;
        }

        var $select = $row.find("select").first();
        if ($select.length > 0 && $.fn && $.fn.select2) {
            var data = $select.select2('data');
            if (Array.isArray(data) && data.length > 0) {
                var selectedVals = [];
                $(data).each(function(_, u){
                    if (u && typeof u.id !== "undefined" && u.id !== "") {
                        selectedVals.push(u.id);
                    }
                });
                if (selectedVals.length > 0) {
                    option = selectedVals.join(",");
                }
            }
        }

        var downloadUrl = "../Reports/excel/downloadReport.php?report_id=" + encodeURIComponent(reportId) + "&key=" + encodeURIComponent(option);
        window.open(downloadUrl, "_blank");
    }
</script>
<link rel="stylesheet" href="../assets/css/select2.min.css">
<script src="../assets/js/select2.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
     // $('select').multipleSelect()
      $('.select2-drop').select2({
          width:'resolve'
      });
    });
</script>
<div class="content">
    <div class="container-fluid">

        <!-- <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">add</i>
                        </div>
                        <p class="card-category">Registration</p>
                        <h3 class="card-title"> Registration</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">image</i>
                            <a href="../UI/addDevoteeI.php" class="dash-link">Photo and ID Scan</a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">add</i>
                            <a href="../UI/addDevoteeI.php"  class="dash-link">Add Devotee</a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">print</i>
                            <a href="../UI/devoteeSearchResult.php?mode=SET&key=CTP" class="dash-link">Print Cards</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">edit</i>
                        </div>
                        <p class="card-category">Update</p>
                        <h3 class="card-title">Devotee Update</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">search</i>
                            <a href="./devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Search Devotee</a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">edit</i>
                            <a href="../UI/devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Modify Devotee Record</a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">print</i>
                            <a href="./devoteeSearchResult.php?mode=SET&key=PWD" class="dash-link">Add Devotee Info to Photos/ID</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <form name="myForm1" id="myFormID1">
                    <input type="hidden" name="requestType1" id="requestType1" value="none">
                    <div class="card card-stats">
                        <div class="card-header card-header-danger card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">links</i>
                            </div>
                            <p class="card-category">Links</p>
                            <h3 class="card-title">Quick Links</h3>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons text-danger">refresh</i>
                                <a href="AddSevaII.php" class="dash-link">Manage Seva Types</a>
                            </div>
                        </div>
                        <div class="card-footer" >
                            <div class="stats">
                                <i class="material-icons text-danger">add</i>
                                <a href="addAccommodationII.php" class="dash-link">Manage Accommodations</a>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons text-danger">edit</i>
                                <a href="upsertAmenityII.php" class="dash-link">Manage Amenities</a>
                            </div>
                        </div>
                    </div>             
                </form>
            </div>
        </div>
        -->
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-warning card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">hotel</i>
                        </div>
                        <p class="card-category">Statistics</p>
                        <h3 class="card-title"> Accommodations</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">home</i>
                            <a href="../UI/index.php?accoType=Occupied" class="dash-link">Total Spaces Allocated:
                                <b>
                                    <?php echo $response[0]['SpaceOccupiedOrDevoteesPresent']; ?>
                                </b> </a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">home</i>
                            <a href="../UI/index.php?accoType=Available" class="dash-link">Total Spaces Available:
                                <b>
                                    <?php echo $response[2]['AvailableSpaces']; ?>
                                </b> </a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">home</i>
                            <a href="../UI/index.php?accoType=Reserved" class="dash-link">Total Spaces Reserved:
                                <b>
                                    <?php echo $response[3]['ReservedSpaces']; ?>
                                </b> </a>
                        </div>
                    </div>
                    <!--<div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">home</i>
                            <a href="../UI/devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Devotees Residing in Ashram:  
                                <b>  <?php echo $response[0]['SpaceOccupiedOrDevoteesPresent']; ?> </b> </a>
                        </div>
                    </div>-->
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">home</i>
                            <a href="../UI/devoteeSearchResult.php?mode=CUS&key=devotee_accommodation_key=OWN"
                                class="dash-link">Devotees with Own Arrangement:
                                <b>
                                    <?php echo $response[4]['DevoteesWithOwnArrangements']; ?>
                                </b> </a>
                        </div>
                    </div>
                    <!--<div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">home</i>
                            <a href="../UI/devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Devotees Residing in
                                Ashram:
                                <b>
                                    <?php echo $response[0]['SpaceOccupiedOrDevoteesPresent']; ?>
                                </b> </a>
                        </div>
                    </div> -->
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="card card-stats">
                    <div class="card-header card-header-success card-header-icon">
                        <div class="card-icon">
                            <i class="material-icons">people</i>
                        </div>
                        <p class="card-category">Statistics</p>
                        <h3 class="card-title">Devotees</h3>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">person</i>
                            <!-- <a href="../UI/devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Devotees Registered for Seva: -->
                            <a href="../UI/index.php?sevaType=Assigned" class="dash-link">Devotees Registered for Seva:
                                <b>
                                    <?php echo $response[1]['RegisteredDevoteesIncludingLocals']; ?>
                                </b> </a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">person</i>
                            <!-- <a href="../UI/devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Devotees Registered for Seva: -->
                            <a href="../UI/index.php?sevaType=Assigned" class="dash-link">Mature Devotees (12 year or
                                older):
                                <b>
                                    <?php echo $response[5]['MatureDevotee']; ?>
                                </b> </a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">person</i>
                            <!-- <a href="../UI/devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Devotees Registered for Seva: -->
                            <a href="../UI/index.php?sevaType=Assigned" class="dash-link">Senior Devotees (60 years or
                                older):
                                <b>
                                    <?php echo $response[6]['SeniorDevotee']; ?>
                                </b> </a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">person</i>
                            <!-- <a href="../UI/devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Devotees Registered for Seva: -->
                            <a href="../UI/index.php?sevaType=Assigned" class="dash-link">Male vs Female Devotees:
                                <b>
                                    <?php echo $response[7]['MaleDevotee'], " / ", $response[7]['FemaleDevotee']; ?>
                                </b> </a>
                        </div>
                    </div>
                </div>
            </div>
            <!--   <div class="col-lg-4 col-md-6 col-sm-6">
                <form name="myForm" id="myFormID">
                    <input type="hidden" name="requestType" id="requestType" value="refreshAcco">
                    <input type="hidden" name="eventId" id="eventId" value="<?php echo $config_data['event_id']; ?>">
                    <div class="card card-stats">
                        <div class="card-header card-header-danger card-header-icon">
                            <div class="card-icon">
                                <i class="material-icons">settings</i>
                            </div>
                            <p class="card-category">Maintenance</p>
                            <h3 class="card-title">Admin Tasks</h3>
                        </div>
                        <div class="card-footer" onclick="clickHandler('#myFormID', 1); return false;">
                            <div class="stats">
                                <i class="material-icons text-danger">refresh</i>
                                <a href class="dash-link">Refresh Accommodation Counts</a>
                            </div>
                        </div>
                        <div class="card-footer" onclick="clickHandler('#myFormID', 2); return false;">
                            <div class="stats">
                                <i class="material-icons text-danger">refresh</i>
                                <a href class="dash-link">Refresh Seva Counts</a>
                            </div>
                        </div>
                        <div class="card-footer" onclick="generateReport('#myFormID', 2); return false;">
                            <div class="stats">
                                <i class="material-icons text-danger">home</i>
                                <a href class="dash-link">Generate Mal Pua Report</a>
                            </div>
                        </div>
                    </div>             
                </form>
            </div>
                            -->
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title"> Daily Reports </h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <form id="printForm">
                                <table class="table table-hover">
                                    <thead class=" text-primary">
                                        <th align='left'>
                                            Select
                                        </th>
                                        <th align='left'>
                                            Report Name
                                        </th>
                                        <th align='left'>
                                            Options
                                        </th>
                                        <th align='left'>
                                            Action
                                        </th>
                                    </thead>
                                    <tbody>

                                        <!-- <div class="scrollbar-dash" id="style-3">
                                    <table class="table table-striped"> -->
                                        <tr>
                                            <td align='center'>
                                                <input type="checkbox" id="DRWP" name="DutyReportWithPhoto">
                                            </td>
                                            <td align='left' class='table-data'>
                                                <a href="../reports/rptDutyReport.php?mode=ADS&key=&photo_required=Y"
                                                    target="_blank">General Seva Report</a>
                                            </td>
                                            <td align='left' class='table-data'>
                                                <select type="text" multiple="multiple" class="select2-drop form-control" name="SaveOoptionWithOutPrint"
                                                    id="SDRWP">
                                                    <option value='' selected>All Sevas</option>
                                                    <?php
                                                                if (!empty($sevaOptionsRes)) {
                                                                    foreach ($sevaOptionsRes as $sevaOption) {
                                                                        echo "<option value='", $sevaOption['Seva_Id'], "'>", urldecode($sevaOption['Seva_Description']), "</option>";
                                                                    }
                                                                }
                                                                ?>
                                                </select>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="exportTosheet(this);"><i class="material-icons">cloud_download</i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center'>
                                                <input type="checkbox" id="DRWOP" name="DutyReportWithoutPhoto">
                                            </td>
                                            <td align='left' class='table-data'>
                                                <a href="../reports/rptDutyReport.php?mode=ADS&key=&photo_required=N"
                                                    target="_blank">General Seva Report (w/o photo)</a>
                                            </td>
                                            <td align='left' class='table-data'>
                                                <select type="text" multiple="multiple"  class="select2-drop form-control" name="SaveOoptionWithOutPrint"
                                                    id="SDRWOP">
                                                    <option value='' selected> All Sevas </option>
                                                    <?php
                                                                if (!empty($sevaOptionsRes)) {
                                                                    foreach ($sevaOptionsRes as $sevaOption) {
                                                                        echo "<option value='", $sevaOption['Seva_Id'], "'>", urldecode($sevaOption['Seva_Description']), "</option>";
                                                                    }
                                                                }
                                                                ?>
                                                </select>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="exportTosheet(this);"><i class="material-icons">cloud_download</i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center'>
                                                <input type="checkbox" id="ADRWP" name="AttendanceReportWithPhoto">
                                            </td>
                                            <td align='left' class='table-data'>
                                                <a href="../reports/rptAttendanceReport.php?mode=ADS&key=&photo_required=Y"
                                                    target="_blank">General Attendance Report</a>
                                            </td>
                                            <td align='left' class='table-data'>
                                                <select type="text" multiple="multiple" class="select2-drop form-control" name="AttendanceOptionWithOutPrint"
                                                    id="SADRWP">
                                                    <option value='' selected>All Sevas</option>
                                                    <?php
                                                                if (!empty($sevaOptionsRes)) {
                                                                    foreach ($sevaOptionsRes as $sevaOption) {
                                                                        echo "<option value='", $sevaOption['Seva_Id'], "'>", urldecode($sevaOption['Seva_Description']), "</option>";
                                                                    }
                                                                }
                                                                ?>
                                                </select>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="exportTosheet(this);"><i class="material-icons">cloud_download</i></a>
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td align='center'>
                                                <input type="checkbox" id="ODRWP" name="OfficeDutyReportWithPhoto">
                                            </td>
                                            <td align='left' class='table-data'>
                                                <a href="../reports/rptOfficeDuty.php?mode=DutyReport&key=&photo_required=Y"
                                                    target="_blank">Office Duty Report</a>
                                            </td>
                                            <td align='left' class='table-data'>
                                                <select type="text" multiple="multiple"  class="select2-drop form-control" name="SaveOoptionWithOutPrint"
                                                    id="SODRWP">
                                                    <option value='' selected>All Duties</option>
                                                    <?php
                                                    if (!empty($dutyOptionsRes)) {
                                                        foreach ($dutyOptionsRes as $locationOption) {
                                                            echo "<option value='", $locationOption['Duty_Location_Key'], "'>", urldecode($locationOption['Duty_Location_Name']), "</option>";
                                                        }
                                                    }
                                                    ?>

                                                </select>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="exportTosheet(this);"><i class="material-icons">cloud_download</i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center'>
                                                <input type="checkbox" id="ODRWOP" name="OfficeDutyReportWithoutPhoto">
                                            </td>
                                            <td align='left' class='table-data'>
                                                <a href="../reports/rptOfficeDuty.php?mode=DutyReport&key=&photo_required=N"
                                                    target="_blank">Office Duty Report (w/o photo)</a>
                                            </td>
                                            <td align='left' class='table-data'>
                                                <select type="text" multiple="multiple"  class="select2-drop form-control" name="SaveOoptionWithOutPrint"
                                                    id="SODRWOP">
                                                    <option value='' selected> All Duties </option>
                                                    <?php
                                                    if (!empty($dutyOptionsRes)) {
                                                        foreach ($dutyOptionsRes as $locationOption) {
                                                            echo "<option value='", $locationOption['Duty_Location_Key'], "'>", urldecode($locationOption['Duty_Location_Name']), "</option>";
                                                        }
                                                    }
    
                                                ?>

                                                </select>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="exportTosheet(this);"><i class="material-icons">cloud_download</i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center'>
                                                <input type="checkbox" id="ARWOP" name="AccommodationReportWithoutPhoto">
                                            </td>
                                            <td align='left' class='table-data'>
                                                <a href="../reports/rptAcco.php?mode=Acco&key=&photo_required=N"
                                                    target="_blank">Accommodation Report (w/o photo)</a>
                                            </td>
                                            <td align='center' class='table-data'>
                                                <select type="text" multiple="multiple"  class="select2-drop form-control" name="AccoOptionWithoutPhoto"
                                                    id="SARWOP">
                                                    <option value='' selected> All Accommodations </option>
                                                    <?php
                                                    if (!empty($accoOptionsRes)) {
                                                        foreach ($accoOptionsRes as $accoOption) {
                                                            echo "<option value='", $accoOption['accomodation_key'], "'>", urldecode($accoOption['accomodation_name']), " (", urldecode($accoOption['allocated_count']), " Allocations)</option>";
                                                        }
                                                    }
                                                    ?>

                                                </select>
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="exportTosheet(this);"><i class="material-icons">cloud_download</i></a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align='center'>
                                                <input type="checkbox" id="AAR" name="AccommodationAvailabilityReport">
                                            </td>
                                            <td align='left' class='table-data'>
                                                <a href="../reports/rptAccoAvailability.php" target="_blank">Accommodation Availability Report</a>
                                            </td>
                                            <td align='center' class='table-data'>
                                                Current Event
                                            </td>
                                            <td>
                                                <a href="javascript:void(0)" onclick="exportTosheet(this);"><i class="material-icons">cloud_download</i></a>
                                            </td>
                                        </tr>

                                        <!-- </table>
                                            </div> -->
                                </table>
                            </div>
                        <button type="button" class="btn btn-success pull-right"
                            onclick="submitPrint('printForm', 1); return false;">Print Selected Reports</button>
                        </form>
                                    
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <!-- <h4 class="card-title">Seva Assignment Counts </h4> -->
                        <h4 class="card-title">My Favorite Reports</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class=" text-primary">
                                    
                                    <th align='left'>
                                        Report Name
                                    </th>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="12">
                                            <div class="scrollbar-dash" id="style-6">
                                                <table class="table table-striped">                                                
                                           <?php
                                           $hasAccoAvailabilityFav = false;
                                           if(!empty($userFavRes)) {
                                            foreach($userFavRes as $favRec){
                                                   $favUrl = urldecode($favRec['fav_url']);
                                                   $favName = $favRec['fav_name'];

                                                   // Hide old accommodation report favorites from the list.
                                                   if (stripos($favUrl, 'rptAcco.php') !== false) {
                                                       continue;
                                                   }

                                                   if (stripos($favUrl, 'rptAccoAvailability.php') !== false) {
                                                       $hasAccoAvailabilityFav = true;
                                                   }

                                                   print_r("<tr><td align='left' class='table-data'>");
                                                   print_r("<a target='_blank' href=" . $favUrl . ">" );
                                                   print_r($favName);
                                                   print_r("</a></td></tr>");
                                            }
                                           }

                                           if (!$hasAccoAvailabilityFav) {
                                               print_r("<tr><td align='left' class='table-data'>");
                                               print_r("<a target='_blank' href='../reports/rptAccoAvailability.php'>Accommodation Availability Report</a>");
                                               print_r("</td></tr>");
                                           }
                                           
                                                /*
                                                $sevaRecordCount = 0;
                                                if (!empty($sevaRes)) {
                                                foreach ($sevaRes as $sevaRecord) {
                                                $sevaID = "--Unavailable--";
                                                $sevaDesc = "--Unavailable--";
                                                $assignCount = "--";
                                                if (!empty($sevaRecord['Seva_Id'])) {
                                                $sevaID = urldecode($sevaRecord['Seva_Id']);
                                                }
                                                if (!empty($sevaRecord['Seva_Description'])) {
                                                $sevaDesc = urldecode($sevaRecord['Seva_Description']);
                                                }
                                                if (!empty($sevaRecord['assigned_count'])) {
                                                $assignCount = $sevaRecord['assigned_count'];
                                                }
                                                if ($sevaDesc != "--Unavailable--") {
                                                $sevaRecordCount = $sevaRecordCount + 1;                         
                                                print_r("
                                                <tr >
                                                <td align='left'>
                                                <a href='./devoteeSearchResult.php?mode=ADS&key=" . $sevaID . " &eventId=". $eventId . "'>" . $sevaDesc . "</a>
                                                </td>
                                                <td align='left' class='table-data'>
                                                <a href='./devoteeSearchResult.php?mode=ADS&key=" . $sevaID . "&eventId=" . $eventId . "'>" . $assignCount . "</a>
                                                </td>
                                                
                                                </tr>
                                                ");
                                                }
                                                }
                                                }
                                                */
                                                ?>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>