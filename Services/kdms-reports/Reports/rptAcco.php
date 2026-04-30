<?php

$config_data = include("../site_config.php");

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$current_page_id = 'KR-R-ACCO';
include_once("../sessionCheck.php");
include_once("rptGenerator.php");
$eventId = $_SESSION['eventId'];
$userId = $_SESSION['LoginID'];?>
<script src="../assets/js/core/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<?php
include_once("../UI/scriptJS.php");
$debug = false;
?>
<html>
    <head>
        <title> Accommodation Report </title>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // printdiv();
            }, false);

            function printdiv() {
                var newstr = document.getElementById("printpage").innerHTML;
                var header = '<header><div align="center"><h3 style="color:#EB5005">  </h3></div><br></header><hr><br>'

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
        $acco_old = "old";
        $recordCount = 0;
        $rptLayout = array();
        $key= "";


        if (!empty($_GET['photo_required'])) {
            if ($_GET['photo_required'] == "Y") {
                $photoRequired = true;
            }
        }
        
        if (!empty($_GET['key'])) {        
                $key = $_GET['key'];
        }

        // ===================================
    // Load Data
    // ===================================
    //if (!empty($_GET['key'])) { //because empty key means all acco

        $accoSearch = new clsServicesManager($key); //key => request => Accommodation ID
        $accoSearch->setOptionType("AOD"); //mode => OptionType => DutyReport
        $accoSearch->setEventId($eventId);
        $response = $accoSearch->getRecords();
        unset($accoSearch);

    if($debug){var_dump($response);die;}

    // ===================================
    // Define report layout
    // ===================================
        if (!empty($response)) {
            if(isset($response["status"])){
                if($response["status"] == false){
                    echo "ERROR MESSAGE: ";
                    echo $response["message"];
                    
                    die;
                }
            }
            generateReport("superTitle", "BABA SHREE NEEM KAROLI MAHARAAJ JI KI JAI");
            $rptLayout[] = "superTitle";

            generateReport("title", "ACCOMMODATION REPORT");
            $rptLayout[] = "title";

            generateReport("modal", "", $photoRequired, 0, $eventId, $userId);
            $rptLayout[] = "modal";

            $recordCount = 0;
            foreach ($response as $accoRecord) {
                $recordCount = $recordCount+1;
                if ($acco_old != $accoRecord["accomodation_key"]) {                    
                    $acco_old = $accoRecord["accomodation_key"];
                    $recordCount = 1;

                    generateReport("tableClose", $accoRecord);
                    $rptLayout[] = "tableClose";
                    //generateReport("title", $accoRecord);
                    //$rptLayout[] = "title";                    
                    generateReport("tableCreate", $accoRecord);
                    $rptLayout[] = "tableCreate";
                    generateReport("tableTitle", $accoRecord);
                    $rptLayout[] = "tableTitle";
                    generateReport("tableHeader", $accoRecord, $photoRequired);
                    $rptLayout[] = "tableHeader";
                }
                generateReport("row_w_rem", $accoRecord, $photoRequired, $recordCount);
                $rptLayout[] = "row_w_rem";

            } //end of foreach ($response as $accoRecord) 
            generateReport("tableClose", $accoRecord);
            $rptLayout[] = "tableClose";
            
            $rptLayout[] = "footer";
            generateReport("footer", $accoRecord);
            
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
            $ttlParam = array("accomodation_name" => " ");
            $tableTtlParam = array("accomodation_name" => " ");
            $hdrParamWOP =array("SN", "Date", "Name of Devotee","Card Number", "Gender", "Address", "Mobile No.", "ID Type", "ID Number", "Allocations", "Remarks");
            $hdrParamWP = array("SN", "Date", "Name of Devotee","Card Number", "Gender", "Address", "Mobile No.", "ID Type", "ID Number", "Allocations", "Remarks");
            $rowParamWOP =array("SN" => "", "arrival_date_time" => "", "Devotee_Name" => "", "devotee_key" => "", "devotee_gender" => "", "Devotee_address" => "", 
            "devotee_cell_phone_number" => "",  "devotee_id_type" => "",   "devotee_id_number" => "", "allocations" => "", "remarks" => "");
            $rowParamWP = array("SN" => "", "arrival_date_time" => "", "Devotee_Name" => "", "devotee_key" => "", "devotee_gender" => "", "Devotee_address" => "", 
                                    "devotee_cell_phone_number" => "",  "devotee_id_type" => "",   "devotee_id_number" => "", "allocations" => "", "remarks" => "");
            $ftrParam = array("Sr" => "");

            switch ($rptElmt) {
                case "superTitle":
                    $sttlParam["super_title"] = $rptData;
                    printSuperTitle($sttlParam);

                    break;
               /* case "title":
                    $ttlParam["accomodation_name"] = "Accommodation Name: " . urldecode($rptData["accomodation_name"] . "");
                    printTitle($ttlParam);
*/
                case "title":
                    $ttlParam["title"] = $rptData;
                    printTitle($ttlParam);
                    break;

                case "tableTitle":
                    $tableTtlParam["accomodation_name"] = "Accommodation Name: " . urldecode($rptData["accomodation_name"] . "");
                    printTableTitle($tableTtlParam, 9, true);

                    break;

                case "tableCreate":
                    printTable("");

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
                                $rowParamWP[$key] = $rptData[$key];
                            }
                        }
                        printRow($rowParamWP, "Devotee_Photo");

                    } else {
                        $rowParamWOP["SN"] = $recordCount;
                        foreach (array_keys($rowParamWOP) as $key) {
                            if (!empty($rptData[$key])) {
                                $rowParamWOP[$key] = urldecode($rptData[$key]);
                            }
                        }
                        printRow($rowParamWOP);
                    }

                    break;

                    case "row_w_rem":
                        if ($photoRequired) {
                            $rowParamWP["SN"] = $recordCount;
                            foreach (array_keys($rowParamWP) as $key) {
                                if (!empty($rptData[$key])) {
                                    $rowParamWP[$key] = $rptData[$key];
                                }
                            }
                            printRowWithRem($rowParamWP);
    
                        } else {
                            $rowParamWOP["SN"] = $recordCount;
                            foreach (array_keys($rowParamWOP) as $key) {
                                if (!empty($rptData[$key])) {
                                    $rowParamWOP[$key] = urldecode($rptData[$key]);
                                }
                            }
                            printRowWithRem($rowParamWOP);
                        }
    
                        break;

                    case "modal":
                        includeRemarkModal("../UI/rptRemarksModal.php", $eventId, $userId, "ACCOMMODATION", false);
                        includeDisplayOnlyModal("../UI/rptParticipationRecords.php", $eventId, $userId, false);    
                        
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

    </body>
</html>