<?php
$config_data = include("../site_config.php");
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$current_page_id = 'KR-R-OFDT';
include_once("../sessionCheck.php");
$eventId = $_SESSION['eventId'];
$userId = $_SESSION['LoginID'];
include_once("rptGenerator.php");
//include_once("../UI/header.php");?>
<script src="../assets/js/core/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<?php
include_once("../UI/scriptJS.php");

$debug = false;
?>
<html>

<head>
    <title> Office Duty Report </title>
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
            $duty_old = "old";
            $recordCount = 0;
            $rptLayout = array();
            $key = "";


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
            //if (!empty($_GET['key'])) { //because empty key means all sevas
            
            $dutySearchRequest = array(
                "key" => $key,
                "photo_required" => $photoRequired ? "Y" : "N"
            );
            $dutySearch = new clsServicesManager($dutySearchRequest); // key => Duty location ID
            $dutySearch->setOptionType("DutyReport"); //mode => OptionType => DutyReport
            $dutySearch->setEventId($eventId);
            $response = $dutySearch->getRecords();
            unset($dutySearch);

            if ($debug) {
                var_dump($response);
                die;
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
                
                generateReport("title", "OFFICE DUTY REPORT");
                    $rptLayout[] = "title";

                generateReport("modal", "", $photoRequired, 0, $eventId, $userId);
                $rptLayout[] = "modal";

                 foreach ($response as $dutyRecord) {
                    $recordCount = $recordCount + 1;
                    if ($duty_old != $dutyRecord["duty_location_name"]) {
                        $duty_old = $dutyRecord["duty_location_name"];
                        $recordCount = 1;

                        generateReport("tableClose", $dutyRecord);
                        $rptLayout[] = "tableClose";
                        //generateReport("title", $dutyRecord);
                        //$rptLayout[] = "title";
                        generateReport("tableCreate", $dutyRecord);
                        $rptLayout[] = "tableCreate";
                        generateReport("tableTitle", $dutyRecord);
                        $rptLayout[] = "tableTitle";
                        generateReport("tableHeader", $dutyRecord, $photoRequired);
                        $rptLayout[] = "tableHeader";
                    }
                    generateReport("row_w_rem", $dutyRecord, $photoRequired, $recordCount);
                    $rptLayout[] = "row_w_rem";

                } //end of foreach ($response as $dutyRecord) 
                
                generateReport("tableClose", $dutyRecord);
                $rptLayout[] = "tableClose";
                generateReport("footer", $dutyRecord);
                $rptLayout[] = "footer";
                
                
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
                $ttlParam = array("duty_location_name" => " ");
                $tableTtlParam = array("duty_location_name" => " ");
                $hdrParamWOP = array("SN", "Name", "Card Number", "Cell Phone",  "Delivery Signature", "Return Signature");
                $hdrParamWP = array("SN", "Name", "Card Number", "Cell Phone", "Photo", "Delivery Signature", "Return Signature");
                $rowParamWOP = array("SN" => "", "devotee_name" => "", "devotee_key" => "", "devotee_cell_phone_number" => "",  "delivery_signature" => "", "return_signature" => "");
                $rowParamWP = array("SN" => "", "devotee_name" => "", "devotee_key" => "", "devotee_cell_phone_number" => "",  "devotee_photo" => "", "delivery_signature" => "", "return_signature" => "");
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
                        $tableTtlParam["duty_location_name"] = "Duty Position: " . urldecode($rptData["duty_location_name"] . "");
                        printTableTitle($tableTtlParam);
    
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
                            printRow($rowParamWP);
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
                        includeRemarkModal("../UI/rptRemarksModal.php", $eventId, $userId, "SEVA", false);
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
    <!--<div id="remark-model"></div> -->
    <a href="#" onclick="printdiv()"><button>Print </button> </a>
    <!--<form id="printForm" action="<?= $config_data['webroot']; ?>Logic/requestManager.php" method="POST">-->
    <!--<input type="hidden" id="requestType" value="removeFromPrintQueue">-->
    <!--            <input type="hidden" id="devotee_key" value=" <?php print_r($_GET); ?>">-->
    <!--<input type="hidden" id="devotee_key" value="Devotee_Key">-->
    <!--</form>-->
    <script>
        /*
    $(document).ready(function(){
        $('.identifyingClass2').click(function(){
           var uKey=$(this).data('id');
            $.ajax({
                url: '../UI/rptParticipationRecords.php?devoteeKey='+uKey,
            }).done(function(data){
                $('#remark-model').html(data);
                $('#ParticipationModalLong').modal('show');
            });
          });  
    });
    */
    </script>
</body>

</html>