<!DOCTYPE html>
<html lang="en">
    <head>
        <title> KDMS (Available Devotee Records) </title>
        <?php
        include_once("header.php");
        include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsDevoteeSearch.php");
        include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsOptionHandler.php");
        // Include new config file in each page ,where we need data from configuration
        $config_data = include("../site_config.php");
        ?>
        <script>

            function _(el) {
                return document.getElementById(el);
            }


            //javascript function for ajax call
            function submitSearch(formId, flag) {
                searchForm = document.getElementById(formId);
                var I = 0;
                var searchString = ""
                for (I = 0; I < searchForm.length; I++) {
                    if (searchForm[I].value != "") {
                        //alert(searchForm[I].id + ": " + searchForm[I].value);
                        searchString = searchString + searchForm[I].id + "=" + encodeURI(searchForm[I].value) + ",";
                    }
                }
                //alert(searchString);

                if (searchString.length > 1) {
                    window.location = "./devoteeSearchResult.php?mode=CUS&key=" + searchString.substr(0, searchString.length - 1);
                } else {
                    alert("Please specify a search criteria!");
                }
            }

            function submitPrint(formId, flag) {
                printForm = document.getElementById(formId);
                var I = 0;
                var printString = ""
                for (I = 0; I < printForm.length; I++) {
                    if (printForm[I].value != "") {
                        //alert(searchForm[I].id + ": " + searchForm[I].value);
                        if (printForm[I].type == 'checkbox' && printForm[I].checked) {
                            printString = printString + "'" + encodeURI(printForm[I].value) + "',";
                        }
                    }
                }

                if (printString.length > 1) {

                    window.open("./rptCardsPrint.php?key=" + printString.substr(0, printString.length - 1) + "&mode=PCD");
                    //window.location.assign("./devoteeSearchResult.php?mode=SET&key=CTP");

                    //if(confirm("Card printed successfully?")){
                    $.ajax({
                        url: '<?=$config_data['webroot']?>Logic/requestManager.php',
                        type: 'POST',
                        data: {'devotee_key': printString.substr(0, printString.length - 1), 'requestType': "removeFromPrintQueue"},
                        async: false,
                        success: function (response) {

                            var r = JSON.parse(response);

                            if (r['flag'] == true) {
                                alert("Card removed from the printing queue!");
                                window.location.assign("./devoteeSearchResult.php?mode=SET&key=CTP");
                            } else {
                                alert(r['message']);
                                updateSuccess = false;
                            }
                        }
                    });
                    //            }

                } else {
                    alert("Please select a card to print!");
                }
            }

            function removePrint(formId, flag) {
                printForm = document.getElementById(formId);
                var I = 0;
                var printString = ""
                for (I = 0; I < printForm.length; I++) {
                    if (printForm[I].value != "") {
                        //alert(searchForm[I].id + ": " + searchForm[I].value);
                        if (printForm[I].type == 'checkbox' && printForm[I].checked) {
                            printString = printString + "'" + encodeURI(printForm[I].value) + "',";
                        }
                    }
                }

                if (printString.length > 1) {

                    //window.open("./rptCardsPrint.php?key=" + printString.substr(0, printString.length - 1) + "&mode=PCD");
                    //window.location.assign("./devoteeSearchResult.php?mode=SET&key=CTP");

                    //if(confirm("Card printed successfully?")){
                    $.ajax({
                        url: '<?=$config_data['webroot']?>Logic/requestManager.php',
                        type: 'POST',
                        data: {'devotee_key': printString.substr(0, printString.length - 1), 'requestType': "removeFromPrintQueue"},
                        async: false,
                        success: function (response) {

                            var r = JSON.parse(response);

                            if (r['flag'] == true) {
                                alert("Card removed from the printing queue!");
                                window.location.assign("./devoteeSearchResult.php?mode=SET&key=CTP");
                            } else {
                                alert(r['message']);
                                updateSuccess = false;
                            }
                        }
                    });
                    //            }

                } else {
                    alert("Please select a card to print!");
                }
            }

            function checkAll()
            {
                var checktoggle = false;
                var checkboxes = new Array();
                if (document.getElementById("headerCheck").checked) {
                    checktoggle = true;
                }
                checkboxes = document.getElementsByTagName('input');
                for (var i = 0; i < checkboxes.length; i++) {
                    if (checkboxes[i].type == 'checkbox') {
                        checkboxes[i].checked = checktoggle;
                    }
                }
            }

            function validateInput() {
                return true;
            }
        </script>
    </head>

    <body class="">
        <div class="wrapper ">
<?php include_once("nav.php"); ?>

            <div class="main-panel">
                <!-- Navbar -->
            <?php
            include_once("navBottom.php");

            $searchKey = "";
            $gridTitle = "";
            $showSelection = FALSE;
            $hideSearchArea = FALSE;
            // var_dump($_GET);
            if (!empty($_GET['key'])) {
                $searchKey = $_GET['key'];

                switch ($searchKey) {
                    case "CUS": //Future use.. isn't used currently
                        $gridTitle = "Devotee Search Result";
                        break;

                    case "PWD":
                        $gridTitle = "Incomplete Devotee Records with photo or ID";
                        break;

                    case "DWP":
                        $gridTitle = "Devotee Records without photo or ID";
                        break;

                    case "CTP":
                        $gridTitle = "Devotee Cards to be Printed";
                        $showSelection = TRUE;
                        break;

                    default :
                        $gridTitle = "Devotee Search Result";
                        break;
                }
                $devoteeSearch = new clsDevoteeSearch($_GET);
                $response = $devoteeSearch->getDevoteeRecords();
//                if(!empty($_GET)) {
//                    var_dump($_GET);die;
//                }

                unset($devoteeSearch);

                $hideSearchArea = TRUE;
            } else { //If custom search and criteria has not been specified, load accommodation options and available spots                
                $loadAccommodation = new clsOptionHandler("Accommodation");
                $accommodations = $loadAccommodation->getOptions();
                unset($loadAccommodations);
            }
            ?>
                <!-- End Navbar -->
                <div class="content-search" <?php
                if ($hideSearchArea) {
                    print_r(" hidden=true");
                }
                ?> >

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Search Devotee</h4>
                            </div>
                            <div class="card-body">
                                <form  id="searchForm">                    
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="bmd-label-floating">First Name</label>
                                                <input type="text" class="form-control" name="devotee_first_name" id="devotee_first_name">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="bmd-label-floating">Last Name</label>
                                                <input type="text" class="form-control" name="devotee_last_name" id="devotee_last_name" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="bmd-label-floating">Cell Phone Number</label>
                                                <input type="text" class="form-control" name="devotee_cell_phone_number" id="devotee_cell_phone_number" >
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" >
                                                <label class="bmd-label-floating" >ID Number</label>
                                                <input type="text" class="form-control" name="devotee_id_number" id="devotee_id_number" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="bmd-label-floating">Accommodation</label>            
                                                <select type="text" class="form-control" name="devotee_accommodation_key" id="devotee_accommodation_key" >
                                                    <option value="">-No Accomodation-</option>
<?php
if (!empty($accommodations)) {
    foreach ($accommodations as $accommodation) {
        print_r("<option value='" . $accommodation['accomodation_key'] . "'");
        Print_r(">" . $accommodation['Accomodation_Name'] . " - " . $accommodation['Available_Count'] . "</option>");
    }
}
?>
                                                </select>
                                            </div>
                                        </div>  
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-top:62px">
                                                <label class="bmd-label-floating">Station</label>
                                                <input type="text" class="form-control" name="devotee_station" id="devotee_station" >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group" style="margin-top:62px">
                                                <label class="bmd-label-floating">Remarks</label>
                                                <input type="text" class="form-control" name="devotee_remarks" id="devotee_remarks" >
                                            </div>
                                        </div>
                                    </div>                          
                                    <button type="reset" class="btn btn-success pull-right">Cancel</button>
                                    <button class="btn btn-success pull-right" onclick="submitSearch('searchForm', 1);
                                            return false;">Search</button>
                                </form>
                                <!--<div class="clearfix"></div>-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-search">
                    <div class="container-fluid">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title" id="pageHeader">
<?php print_r($gridTitle); ?>
                                </h4>
                            </div>
                            <form id="printForm">
                                <div class="row">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="text-primary">
<?php if ($showSelection) {
    Print_r("
                                  <th>
                                      Select <input type='checkbox' name='headerCheck' id='headerCheck' value='' onclick=checkAll(); return false;>
                                  </th>");
}
?>
                                                <th>
                                                    Name
                                                </th>
                                                <th>
                                                    Devotee ID
                                                </th>
                                                <th>
                                                    Station
                                                </th>
                                                <th>
                                                    Cell Number
                                                </th>
                                                <th>
                                                    Photo
                                                </th>
                                                <th>
                                                    ID Image
                                                </th>

                                                </thead>
                                                <tbody>
<?php
$recordCount = 0;
if (!empty($response)) {



    foreach ($response as $devoteeRecord) {
        $devoteeKey = "--Unavailable--";
        $devoteeName = "--Unavailable--";
        $devoteeStation = "--Unavailable--";
        $devoteeCellNumber = "--Unavailable--";
        $devoteePhoto = "";
        $devoteeIdImage = "";
        $devoteeID = "";


        if (!empty($devoteeRecord['devotee_key'])) {
            $devoteeKey = urldecode($devoteeRecord['devotee_key']);
        }

        if (!empty($devoteeRecord['Devotee_Name'])) {
            $devoteeName = urldecode($devoteeRecord['Devotee_Name']);
        }

        if (!empty($devoteeRecord['devotee_station'])) {
            $devoteeStation = urldecode($devoteeRecord['devotee_station']);
        }

        if (!empty($devoteeRecord['devotee_cell_phone_number'])) {
            $devoteeCellNumber = urldecode($devoteeRecord['devotee_cell_phone_number']);
        }

        if (!empty($devoteeRecord['Devotee_Photo'])) {
            $devoteePhoto = $devoteeRecord['Devotee_Photo'];
        }

        if (!empty($devoteeRecord['Devotee_ID_Image'])) {
            $devoteeIdImage = $devoteeRecord['Devotee_ID_Image'];
        }

        if ($devoteeKey != "--Unavailable--") {
            $recordCount = $recordCount + 1;

            print_r("<tr>");
            if ($showSelection) {
                print_r("<td> <input type='checkbox' name='" . $recordCount . "' id='" . $recordCount . "' value='" . $devoteeKey . "'> </td>");
            }
            print_r(" <td>
                                                         <a href='addDevoteeI.php?devotee_key=" . $devoteeKey . "'>" . $devoteeName . "</a>
                                                     </td>
                                                     <td>
                                                         <a href='addDevoteeI.php?devotee_key=" . $devoteeKey . "'>" . $devoteeKey . "</a>
                                                     </td>
                                                     <td>
                                                         <a href='addDevoteeI.php?devotee_key=" . $devoteeKey . "'>" . $devoteeStation . "</a>
                                                     </td>
                                                       <td>
                                                           <a href='addDevoteeI.php?devotee_key=" . $devoteeKey . "'>" . $devoteeCellNumber . "</a>
                                                     </td><td>");
            //<img src='../assets/img/faces/devotee.ico' height='70px' width='70px' alt='Devotee Image' />
            if ($devoteePhoto == "") {
                print_r('<img src="../assets/img/faces/devotee.ico" alt="Devotee Image" height="70px" width="75px"></img>');
            } else {
                print_r('<img src="data:image/jpeg;base64,' . $devoteePhoto . '" alt="devotee image" height="70px" width="70px"></img>');
            }

            print_r("</td> <td>");
            //"<img src='../assets/img/faces/doc.png' height='70px' width='70px' alt='Devotee Scan ID' /> " ;

            if ($devoteeIdImage == "") {
                print_r('<img src="../assets/img/faces/doc.png" alt="Devotee ID Image" height="65px" width="65px"></img>');
            } else {
                print_r('<img src="data:image/jpeg;base64,' . $devoteeIdImage . '" alt="devotee image" height="70px" width="70px"></img>');
            }

            print_r("</td> </td> </tr>");
        }
    }
}
?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="col-md-8" >
                                        <div class="card-body">
                                            <button type="submit" <?php if (!$showSelection) {
                                                        print_r("hidden='true'");
                                                    } ?> class="btn btn-success pull-right" >Cancel</button>
                                            <button type="submit" hidden='true' class="btn btn-success pull-right">Add Devotee without photo/image</button>
                                            
                                            <button type="submit" <?php if (!$showSelection) {
                                                        print_r("hidden='true'");
                                                    } ?> class="btn btn-success pull-right" onclick="removePrint('printForm', 1); return false;">Cancel Print for Selected Cards</button>
                                            <button type="submit" <?php if (!$showSelection) {
                                                        print_r("hidden='true'");
                                                    } ?> class="btn btn-success pull-right" onclick="submitPrint('printForm', 1); return false;">Print Selected Cards</button>
                                            <div class="clearfix"></div>

                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>                     
            </div>

            <!-- id modial -->
        </div>
        <!--   Core JS Files   -->
        <script>
            document.getElementById('pageHeader').textContent = document.getElementById('pageHeader').textContent + "(" + <?php print_r($recordCount); ?> + " records found!)";
        </script>
<?php include_once("scriptJS.php") ?>
    </body>

</html>
