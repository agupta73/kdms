<?php
$config_data = include("../site_config.php");
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$current_page_id = 'KR-R-ATTN';
include_once("../sessionCheck.php");
$eventId = $_SESSION['eventId'];
$userId = $_SESSION['LoginID'];
include_once("rptGenerator.php");
?>
<html>
<head>
    <script src="../assets/js/core/jquery.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<?php


include_once("../UI/scriptJS.php");
    $debug = false;
    ?>
    <title> Seva Attendance </title>
    <script>

        function printdiv() {
            var newstr = document.getElementById("printpage").innerHTML;
            var header = '<header><div align="center"><h3 style="color:#EB5005"> Seva Report </h3></div><br></header><hr><br>'

            var popupWin = window.open('', 'blank', 'width=800px,height=900px');
            popupWin.document.open();
            //popupWin.document.write('<html><body onload="window.print()">' + newstr + '</html></br>');
            popupWin.document.write('<html><body>' + newstr + '</html></br>');
            popupWin.document.close();
            window.close();

            return false;
        }
    </script>
    <link href="../assets/css/custom-css-kdms.css" rel="stylesheet" />
</head>

<body>
    <div id="printpage">
        <div id="report">
            <?php

                // ===================================
                // Initialize variables
                // ===================================
                include_once("../Logic/clsServicesManager.php");

                $photoRequired = false;
                $devotee_seva_old = "old";
                $devotee_seva_id_old = "";
                $recordCount = 0;
                $rptLayout = array();


                if (!empty($_GET['photo_required'])) {
                    if ($_GET['photo_required'] == "Y") {
                        $photoRequired = true;
                    }
                }

                // ===================================
                // Load Data
                // ===================================
                //if (!empty($_GET['key'])) { //because empty key means all sevas
                $devoteeSearch = new clsServicesManager($_GET); //key => request => Seva ID
                $devoteeSearch->setOptionType("DSA"); //mode => OptionType => asigned devotee for seva
                $devoteeSearch->setEventId($eventId);
                $response = $devoteeSearch->getRecords();
                unset($devoteeSearch);
                if($debug) {
                var_dump($response);
                }

                // ===================================
                // Define report layout
                // ===================================
                if (!empty($response)) {
                    $recordCount = 0;
                    if(isset($response["status"])){
                        if($response["status"] == false){
                            echo "ERROR MESSAGE: ";
                            echo $response["message"];
                            
                            die;
                        }
                    }
                    generateReport("superTitle", "BABA SHREE NEEM KAROLI MAHARAAJ JI KI JAI");
                    $rptLayout[] = "superTitle";

                    generateReport("title", "ATTENDANCE REPORT");
                    $rptLayout[] = "title";

                    generateReport("modal", "", $photoRequired, 0, $eventId, $userId);
                    $rptLayout[] = "modal";

                    foreach ($response as $devoteeRecord) {
                        $recordCount = $recordCount + 1;
                        if ($devotee_seva_old != $devoteeRecord["Seva_description"]) {
                            if ($devotee_seva_old != "old") {
                                generateReport("attendanceButton", $devotee_seva_id_old);
                                $rptLayout[] = "attendanceButton";
                            }
                            $devotee_seva_old = $devoteeRecord["Seva_description"];
                            $devotee_seva_id_old = $devoteeRecord["seva_id"];
                            $recordCount = 1;

                            generateReport("tableClose", $devoteeRecord);
                            $rptLayout[] = "tableClose";
                            //generateReport("title", $devoteeRecord);
                            //$rptLayout[] = "title";
                            generateReport("tableCreate", $devotee_seva_id_old);
                            $rptLayout[] = "tableCreate";
                            generateReport("tableTitle", $devoteeRecord);
                            $rptLayout[] = "tableTitle";
                            generateReport("tableHeader", $devoteeRecord, $photoRequired);
                            $rptLayout[] = "tableHeader";
                        }
                        generateReport("row_w_rem", $devoteeRecord, $photoRequired, $recordCount);
                        $rptLayout[] = "row_w_rem";
                        if( !next($response) ) {
                            generateReport("attendanceButton", $devotee_seva_id_old);
                            $rptLayout[] = "attendanceButton";
                        }

                    } //end of foreach ($response as $devoteeRecord) 
                    generateReport("tableClose", $devoteeRecord);
                    $rptLayout[] = "tableClose";
                    
                    $rptLayout[] = "footer";
                    generateReport("footer", $devoteeRecord);

                    if ($debug) {
                        echo "<br> <br>Report Layout:";
                        foreach ($rptLayout as $element) {
                            echo "<br>", $element;
                        }
                    }

                } //end of !empty($response)
                
                // ===================================
                // Generate report 
                // ===================================  
                function generateReport($rptElmt, $rptData, $photoRequired = false, $recordCount = 0, $eventId = "", $userId = "")
                {
                    $sttlParam = array("super_title" => " ");
                    $ttlParam = array("Seva_description" => " ");
                    $tableTtlParam = array("Seva_description" => " ");
                    $hdrParamWOP = array("SN", "Name", "Card Number", "Station", "Cell Phone", "Attendance" , "Mark Attendance");
                    $hdrParamWP = array("SN", "Name", "Card Number", "Station", "Cell Phone",  "Photo", "Attendance" , "Mark Attendance");
                    $rowParamWOP = array("SN" => "", "Devotee_Name" => "", "devotee_key" => "", "devotee_station" => "", "devotee_cell_phone_number" => "",   "attendance" => "", "mark_attendance" => "", "[h]seva_id" => "","[h]devotee_key" => "");
                    $rowParamWP = array("SN" => "", "Devotee_Name" => "", "devotee_key" => "", "devotee_station" => "", "devotee_cell_phone_number" => "",   "Devotee_Photo" => "", "attendance" => "", "mark_attendance" => "", "[h]seva_id" => "", "[h]devotee_key" => "");
                    $btnParam = array("button_id" => "");
                    $tblParam = array("table_id" => "");
                    $ftrParam = array("Sr" => "");

                    switch ($rptElmt) {
                        case "superTitle":
                            $sttlParam["super_title"] = $rptData;
                            printSuperTitle($sttlParam);
        
                            break;
                            
                        case "title":
                            $ttlParam["title"] = $rptData;
                            printTitle($ttlParam);

                            break;

                        case "tableTitle":
                            $tableTtlParam["Seva_description"] = "Seva Position: " . str_replace("+", " ", urldecode($rptData["Seva_description"] . ""));
                            printTableTitle($tableTtlParam, 5, true);
        
                            break;

                    case "tableCreate":
                        
                            $tblParam["table_id"] = $rptData;                            
                            printTable($tblParam);

                            break;

                        case "tableClose":
                            printCloseTable("");

                            break;

                        case "tableHeader":

                            if ($photoRequired) {
                                printHeader($hdrParamWP);
                            } else {
                                printHeader($hdrParamWOP);
                            }
                            break;

                        case "row":
                            if ($photoRequired) {
                                $rowParamWP["SN"] = $recordCount;
                                foreach (array_keys($rowParamWP) as $key) {
                                    if (!empty($rptData[$key])) {
                                        $rowParamWP[$key] = $rptData[str_replace("[h]", "", $key)];
                                    }
                                }
                                printRow($rowParamWP, $key);

                            } else {
                                $rowParamWOP["SN"] = $recordCount;
                                foreach (array_keys($rowParamWOP) as $key) {
                                    if (!empty($rptData[$key])) {
                                        $rowParamWOP[$key] = str_replace("+", " ", urldecode($rptData[str_replace("[h]", "", $key)]));
                                    }
                                }
                                printRow($rowParamWOP);
                            }

                            break;

                        case "row_w_rem":
                            if ($photoRequired) {
                                $rowParamWP["SN"] = $recordCount;
                                foreach (array_keys($rowParamWP) as $key) {
                                    if (!empty($rptData[str_replace("[h]", "", $key)])) {
                                        $rowParamWP[$key] = $rptData[str_replace("[h]", "", $key)];                                    
                                    }
                                }
                                printRowWithRem($rowParamWP);
        
                            } else {
                                $rowParamWOP["SN"] = $recordCount;
                                foreach (array_keys($rowParamWOP) as $key) {
                                    if (!empty($rptData[str_replace("[h]", "", $key)])) {
                                        $rowParamWOP[$key] = urldecode($rptData[str_replace("[h]", "", $key)]);
                                    }
                                }
                                printRowWithRem($rowParamWOP);
                            }
        
                            break;
    
                        case "modal":
                            includeRemarkModal("../UI/rptRemarksModal.php", $eventId, $userId, "SEVA", false);
                            includeAttendanceFunction("", $eventId, $userId, "", false);
                            includeDisplayOnlyModal("../UI/rptParticipationRecords.php", $eventId, $userId, false);
                            includeAllAttendanceFunction();
                            break;

                        case "attendanceButton":
                            $btnParam["button_id"] = $rptData;                            
                            printButton($btnParam);

                            break;
                        case "footer":
                            printFooter("</table>");
                            includeFavModal("../UI/rptFavModal.php");
                            break;
                    } // end of switch
                }

                ?>
            <br>
        </div>
    </div>

    <a href="#" onclick="printdiv()"><button>Print </button> </a>
    <!--<form id="printForm" action="<?= $config_data['webroot']; ?>Logic/requestManager.php" method="POST">-->
    <!--<input type="hidden" id="requestType" value="removeFromPrintQueue">-->
    <!--            <input type="hidden" id="devotee_key" value=" <?php print_r($_GET); ?>">-->
    <!--<input type="hidden" id="devotee_key" value="Devotee_Key">-->
    <!--</form>-->

    <script type="text/javascript">
        /*
        function submitAttendance(rating, devoteeKey) {
            //['remark_type','rating','remark','devotee_key','requestType','eventId','userId']
            const params = new URLSearchParams({
                remark_type: "ATTENDANCE",
                rating: $rating,
                remark: "",
                devotee_key: $devoteeKey,
                requestType: "upsertRemark",
                eventId: eventId,
                userId: userId
            });

            //console.log(params.toString());
            var formData = params.toString();
            //alert(formData);        
            $.ajax({
                url: '../Logic/requestManager.php',
                type: 'POST',
                data: formData,
                success: function (response) {
                    //alert(response);
                    var r = JSON.parse(response);

                    if (r['flag'] == true) {
                        alert("Remark submitted successfully!");
                        clearRemarkForm();
                        $('#RemarksModalLong').modal('hide');                        
                    } else {                        
                        alert(r['message']);
                        updateSuccess = false;
                    }
                }
            });
        }
        */

        


        

    



    </script>
    </body>
</html>
