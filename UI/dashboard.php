<head>
        <title> KDMS Dashboard </title></head>
<?php

//TODO: refresh seva and accommodation counts is not working

$debug= false;
include_once("header.php");
$current_page_id = "KD-DSBRD";
include_once("../sessionCheck.php");
include_once("../Logic/clsDevoteeSearch.php");
include_once("../Logic/clsReportHandler.php");
include_once("../Logic/clsOptionHandler.php");
// Include new config file in each page ,where we need data from configuration
$config_data = include("../site_config.php");

$eventId = $config_data['event_id'];


$getReport = new clsReportHandler();
$response = $getReport->getAccommodationCounts($eventId);
unset($getReport);

if($debug){echo "eventId =: ", $config_data['event_id'] ; var_dump($response);}

$accoType = "All";

if (!empty($_GET['accoType'])) {
    $accoType = $_GET['accoType'];
}
$sevaType = "All";

if (!empty($_GET['sevaType'])) {
    $sevaType = $_GET['sevaType'];
}

$getReport = new clsReportHandler();
$AccoResponse = $getReport->getAccommodationRecords($accoType, $eventId);
unset($getReport);
if($debug){echo "Accociation response =: " ; var_dump($AccoResponse);}
$sevaSearch = new clsOptionHandler("Seva"); //Constructor =>request=>optionType=>Seva
$sevaSearch->setEventId($eventId);
$sevaSearch->setOptionKey($sevaType); // Option Key for the API => either all or specific seva count (like assigned)
$sevaRes = $sevaSearch->getOptions();
//var_dump($response); die;
//array(5) { [0]=> array(3) { ["Seva_Id"]=> string(2) "AT" ["Seva_Description"]=> string(11) "A test Seva" ["assigned_count"]=> string(1) "0" } [1]=> array(3) { ["Seva_Id"]=> string(2) "KU" ["Seva_Description"]=> string(14) "Kitchen+Upper+" ["assigned_count"]=> string(1) "0" } [2]=> array(3) { ["Seva_Id"]=> string(2) "MP" ["Seva_Description"]=> string(12) "Mal+Pua+Seva" ["assigned_count"]=> string(1) "1" } [3]=> array(3) { ["Seva_Id"]=> string(2) "PV" ["Seva_Description"]=> string(19) "Prasaad+Vitran+Seva" ["assigned_count"]=> string(1) "0" } [4]=> array(3) { ["Seva_Id"]=> string(2) "UN" ["Seva_Description"]=> string(14) "-- Un Known --" ["assigned_count"]=> string(1) "4" } }
unset($sevaSearch);
?> 
<script>
    //Debug only function.. do not use
    function clickHandler2(formId, flag) {

        //document.getElementById("requestType").value = "refreshAcco";
        var formData = $(formId).serialize();
        alert(formData);
        alert(formId);
        document.getElementById(formId).action = "<?=$config_data['webroot'];?>Logic/requestManager.php";
        document.getElementById(formId).method = "POST";
        //document.getElementById("myFormID").data = formData;
        document.getElementById(formId).submit();

    }

    //javascript function for ajax call
    function clickHandler(formId, flag) {
       var formData = $(formId).serialize();

        <?php
        if($debug){
            echo "alert(formData);";
        }
        ?>
        if (validateInput()) {

            switch (flag) {
                case 1: //Refresh count

                    $.ajax({
                        url: "<?=$config_data['webroot'];?>Logic/requestManager.php",
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
                        url: "<?=$config_data['webroot'];?>Logic/requestManager.php",
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
//                        url: '<?=$config_data['webroot']?>Logic/requestManager.php',
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
        <div class="row">
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
       <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">
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
                                <b>  <?php echo $response[0]['SpaceOccupiedOrDevoteesPresent']; ?> </b> </a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">home</i>
                            <a href="../UI/index.php?accoType=Available" class="dash-link">Total Spaces Available:  
                                <b>  <?php echo $response[2]['AvailableSpaces']; ?> </b> </a>
                        </div> 
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">home</i>
                            <a href="../UI/index.php?accoType=Reserved" class="dash-link">Total Spaces Reserved:
                                <b>  <?php echo $response[3]['ReservedSpaces']; ?> </b> </a></div> 
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
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
                            <i class="material-icons text-danger">home</i>
                            <a href="../UI/devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Devotees Residing in Ashram:  
                                <b>  <?php echo $response[0]['SpaceOccupiedOrDevoteesPresent']; ?> </b> </a>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">home</i>
                            <a href="../UI/devoteeSearchResult.php?mode=CUS&key=devotee_accommodation_key=OWNAR" class="dash-link">Devotees with Own Arrangement:
                                <b>  <?php echo $response[4]['DevoteesWithOwnArrangements']; ?> </b> </a></div>
                    </div>
                    <div class="card-footer">
                        <div class="stats">
                            <i class="material-icons text-danger">home</i>
                            <!-- <a href="../UI/devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Devotees Registered for Seva: -->
                            <a href="../UI/index.php?sevaType=Assigned" class="dash-link">Devotees Registered for Seva:
                                <b>  <?php echo $response[1]['RegisteredDevoteesIncludingLocals']; ?> </b> </a>
                        </div> 
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
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
        </div> 
        <div class="row">
                <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h4 class="card-title">
                                <?php
                                print_r($accoType . " ");
                                ?>
                                Accommodations </h4>
                        </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class=" text-primary">
                                        <th align='left'>
                                            Accommodation Name
                                        </th>
                                        <th align='left'>
                                            Capacity
                                        </th>
                                        
                                        <th align='left'>
                                            Allocated 
                                        </th>
                                        
                                        <th align='left'>
                                            Reserved
                                        </th>
                                        <th align='left'>
                                            Unavailable 
                                        </th>
                                        <th align='left'>
                                            Available 
                                        </th>
                                        </thead>
                                        <tbody >
                                        <tr>
                                            <td colspan="12">
                                            <div class="scrollbar-dash" id="style-6">
                                                <table class="table table-striped"> 
                                            <?php
                                            $recordCount = 0;
                                            if (!empty($AccoResponse)) {
                                                foreach ($AccoResponse as $accommodationRecord) {
                                                    $accomodationKey = "--Unavailable--";
                                                    $accomodationName = "--Unavailable--";
                                                    $accomodationCapacity = "--";
                                                    $reservedCount = "--";
                                                    $outOfAvailabilityCount = "--";
                                                    $allocatedCount = "--";
                                                    $availableCount = "--";
                                                    $occupiedCount = "--";


                                                    if (!empty($accommodationRecord['accomodation_key'])) {
                                                        $accomodationKey = urldecode($accommodationRecord['accomodation_key']);
                                                    }

                                                    if (!empty($accommodationRecord['accomodation_name'])) {
                                                        $accomodationName = urldecode($accommodationRecord['accomodation_name']);
                                                    }

                                                    if (!empty($accommodationRecord['accomodation_capacity'])) {
                                                        $accomodationCapacity = urldecode($accommodationRecord['accomodation_capacity']);
                                                    }
                                                    
                                                    if (!empty($accommodationRecord['allocated_count'])) {
                                                        $allocatedCount = $accommodationRecord['allocated_count'];
                                                    }
                                                    
                                                    if (!empty($accommodationRecord['reserved_count'])) {
                                                        $reservedCount = urldecode($accommodationRecord['reserved_count']);
                                                    }                                                    

                                                    if (!empty($accommodationRecord['Out_of_Availability_Count'])) {
                                                        $outOfAvailabilityCount = $accommodationRecord['Out_of_Availability_Count'];
                                                    }
                                                    
                                                    if (!empty($accommodationRecord['available_count'])) {
                                                        $availableCount = $accommodationRecord['available_count'];
                                                    }
                                                    
                                                    if ($accomodationKey != "--Unavailable--") {
                                                        $recordCount = $recordCount + 1;

                                                        print_r("
                                                            <tr >
                                                            <td align='left'>
                                                                <a href='addAccommodationI.php?accommodation_key=" . $accomodationKey . "'>" . $accomodationName . "</a>
                                                            </td>
                                                            <td align='left' class='table-data'>
                                                                <a href='./devoteeSearchResult.php?mode=AOD&key=" . $accomodationKey . "&eventId=" . $eventId . "'>"  . $accomodationCapacity . "</a>
                                                            </td>
                                                            <td align='left' class='table-data'>
                                                                <a href='./devoteeSearchResult.php?mode=AOD&key=" . $accomodationKey . "&eventId=" . $eventId . "'>" . $allocatedCount . "</a>
                                                            </td>
                                                            
                                                            <td align='left' class='table-data'>
                                                                <a href='addAccommodationI.php?accommodation_key=" . $accomodationKey . "'>" . $reservedCount . "</a>
                                                            </td>
                                                            
                                                            <td align='left' class='table-data'>
                                                                <a href='addAccommodationI.php?accommodation_key=" . $accomodationKey . "'>" . $outOfAvailabilityCount . "</a>
                                                            </td>
                                                            <td align='left' class='table-data'>
                                                                <a href='addAccommodationI.php?accommodation_key=" . $accomodationKey . "'>" . $availableCount . "</a>
                                                            </td>
                                                            </tr>
                                                            ");
                                                    }
                                                }
                                            }
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
                <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <!-- <h4 class="card-title">Seva Assignment Counts </h4> -->
                            <h4 class="card-title">
                                <?php
                                    if($sevaType==""){
                                        echo "All";
                                    }
                                    else {
                                        echo $sevaType;
                                    }
                                    // print_r($accoType . " ");
                                ?>
                            Sevas
                            </h4>
                        </div>
                            <div class="card-body">
                            <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class=" text-primary">
                                <th align='left'>
                                    Seva
                                </th>
                                <th align='left' class="assign-devotees">
                                    Assigned Devotees 
                                </th>
                                </thead>
                                <tbody >
                                <tr>
                                    <td colspan="12">
                                    <div class="scrollbar-dash" id="style-6">
                                        <table class="table table-striped"> 
                                    <?php
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
