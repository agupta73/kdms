<html>

<head>
    <title> Report Dashboard </title>
    <!--link rel="stylesheet" href="../assets/dist/multiple-select.min.css"-->
    
</head>

<body>
    
    <?php

    $debug = false;
    include_once("header.php");
    include_once("../sessionCheck.php");
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
    
    $getReport = new clsServicesManager(""); //no constructor variable needed
    $getReport->setEventId($eventId);
    $getReport->setOptionType("accoCounts");
    $response = $getReport->getRecords();
    unset($getReport);

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

    if ($debug) {
        var_dump($sevaOptionsRes);
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

//        $(function () {
//          //  $('select').multipleSelect()
//        })

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

        function submitPrint(formId, flag) {
            printForm = document.getElementById(formId);
            
            var I = 0;
            var option = "";
            if (printForm.length < 1) {
                alert("Please select a report to print");
            }
            else {

                for (I = 0; I < printForm.length; I++) {
                    if (printForm[I].value != "") {

                        /*alert(printForm[I].id);
                        alert(printForm[I].type);
                        alert(printForm[I].value); */

                    if (printForm[I].type == 'checkbox' && printForm[I].checked) {
                       
                        ////---New code-----------------
                        var data=$('#SDRWP').select2('data');
                        var txt="";
                        $(data).each(function(i,u){
                            var sVal=u.id;
                            if(i==0){
                                txt=sVal;
                            }else{
                                txt=txt+','+sVal;
                            }
                        });
                       
                        option=txt;
                        //-----------new code----------------
                        //option = document.getElementById("S" + printForm[I].id).value;
                        //alert(printForm[I].id);
                        switch (printForm[I].id) {
                            case "DRWP":
                                window.open("../reports/rptDutyReport.php?mode=ADS&photo_required=Y&key=" + option, "_blank");
                                break;
                            case "DRWOP":
                                window.open("../reports/rptDutyReport.php?mode=ADS&photo_required=N&key=" + option, "_blank");
                                break;
                            case "ODRWP":
                                window.open("../reports/rptOfficeDuty.php?mode=DutyReport&photo_required=Y&key=" + option, "_blank");
                                break;
                            case "ODRWOP":
                                window.open("../reports/rptOfficeDuty.php?mode=DutyReport&photo_required=N&key=" + option, "_blank");
                                break;
                        }

                        }
                    }
                }
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
                                <a href="../UI/index.php?sevaType=Assigned" class="dash-link">Devotees Registered for
                                    Seva:
                                    <b>
                                        <?php echo $response[1]['RegisteredDevoteesIncludingLocals']; ?>
                                    </b> </a>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="stats">
                                <i class="material-icons text-danger">person</i>
                                <!-- <a href="../UI/devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Devotees Registered for Seva: -->
                                <a href="../UI/index.php?sevaType=Assigned" class="dash-link">Mature Devotees (12 year
                                    or
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
                                <a href="../UI/index.php?sevaType=Assigned" class="dash-link">Senior Devotees (60 years
                                    or
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
                                                    <!-- <select type="text" class="form-control" -->
                                                    <select type="text" class="select2-drop" multiple="multiple" id="SDRWP" width="100%" placeholder="Select Specific Sevas", data-display-values=true data-open-on-hover=false showClear=true>
                                                        <!--<option value='' selected>All Sevas</option> -->
                                                        <?php
                                                        if (!empty($sevaOptionsRes)) {
                                                            foreach ($sevaOptionsRes as $sevaOption) {
                                                                echo "<option value='", $sevaOption['Seva_Id'], "'>", urldecode($sevaOption['Seva_Description']), "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
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
                                                    <!-- <select type="text" class="form-control" -->
                                                    <select type="text" class="select2-drop" multiple="multiple" id="SDRWOP" width="100%" placeholder="Select Specific Sevas", data-display-values=true data-open-on-hover=false showClear=true>
                                                        <!-- <opt value='' selected> All Sevas </option> -->
                                                        <?php
                                                        if (!empty($sevaOptionsRes)) {
                                                            foreach ($sevaOptionsRes as $sevaOption) {
                                                                echo "<option value='", $sevaOption['Seva_Id'], "'>", urldecode($sevaOption['Seva_Description']), "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
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
                                                    <!-- <select type="text" class="form-control" -->
                                                    <select type="text" class="select2-drop" multiple="multiple" id="SODRWP" width="100%" placeholder="Select Specific Duties", data-display-values=true, showClear=true>
                                                        <!-- <option value='' selected>All Duties</option> -->
                                                        <?php
                                                        if (!empty($dutyOptionsRes)) {
                                                            foreach ($dutyOptionsRes as $locationOption) {
                                                                echo "<option value='", $locationOption['Duty_Location_Key'], "'>", urldecode($locationOption['Duty_Location_Name']), "</option>";
                                                            }
                                                        }
                                                        ?>

                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align='center'>
                                                    <input type="checkbox" id="ODRWOP"
                                                        name="OfficeDutyReportWithoutPhoto">
                                                </td>
                                                <td align='left' class='table-data'>
                                                    <a href="../reports/rptOfficeDuty.php?mode=DutyReport&key=&photo_required=N"
                                                        target="_blank">Office Duty Report (w/o photo)</a>
                                                </td>
                                                <!-- <td align='center' class='table-data'> -->
                                                <td align='left' class='table-data'>
                                                        <!-- Multiple Select -->
                                                    <select type="text" class="select2-drop"  multiple="multiple" id="SODRWOP" width="100%"  placeholder="Select Specific Duties" data-display-values=true data-open-on-hover=false showClear=true>
                                                   <!-- <option value='' selected> All Duties </option> -->
                                                    <?php
                                                        if (!empty($dutyOptionsRes)) {
                                                            foreach ($dutyOptionsRes as $locationOption) {
                                                                echo "<option value='", $locationOption['Duty_Location_Key'], "'>", urldecode($locationOption['Duty_Location_Name']), "</option>";
                                                            }
                                                        }

                                                        ?>

                                                      <!-- <option value="2">February</option>
                                                    <option value="3">March</option>
                                                    <option value="4">April</option>
                                                    <option value="5">May</option>
                                                    <option value="6">June</option>
                                                    <option value="7">July</option>
                                                    <option value="8">August</option>
                                                    <option value="9">September</option>
                                                    <option value="10">October</option>
                                                    <option value="11">November</option>
                                                    <option value="12">December</option>
                                                    </select>
                                                   <select type="text" class="form-control"
                                                        name="SaveOoptionWithOutPrint" id="SODRWOP">
                                                        <option value='' selected> All Duties </option>
                                                        <?php
                                                        if (!empty($dutyOptionsRes)) {
                                                            foreach ($dutyOptionsRes as $locationOption) {
                                                                echo "<option value='", $locationOption['Duty_Location_Key'], "'>", urldecode($locationOption['Duty_Location_Name']), "</option>";
                                                            }
                                                        }

                                                        ?>

                                                    </select> -->
                                                </td>
                                            </tr>

                                            <!-- </table>
                                            </div> -->
                                    </table>
                            </div>
                            </td>
                            </tr>
                            </tbody>
                            </table>
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
                            <h4 class="card-title">Admin Reports</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class=" text-primary">
                                        <th align='left'>
                                            Select
                                        </th>
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
    
   
    <script src="../assets/js/jquery-3.2.1.min.js" type="text/javascript"></script>
    <!--script src="../assets/dist/multiple-select.min.js"></script-->
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
</body>

</html>
