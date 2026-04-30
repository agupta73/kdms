<?php
$config_data = include("../../site_config.php");
include_once("../../sessionCheck.php");
#include_once("rptGenerator.php");
$eventId = $_SESSION['eventId'];
include_once("../../Logic/clsServicesManager.php");

$photoRequired = false;
$devotee_seva_old = "old";
$recordCount = 0;
$rptLayout = array();

$rType=$_GET['type'];

// ===================================
// Load Data
// ===================================
//if (!empty($_GET['key'])) { //because empty key means all sevas
$devoteeSearch = new clsServicesManager($_GET); //key => request => Seva ID
$devoteeSearch->setOptionType("ADS"); //mode => OptionType => asigned devotee for seva
$devoteeSearch->setEventId($eventId);
$response = $devoteeSearch->getRecords();
unset($devoteeSearch);
echo "<pre>";
print_r($response);
die;
// ===================================
// Define report layout
// ===================================
if (!empty($response)) {
    $recordCount = 0;
    foreach ($response as $devoteeRecord) {
        $recordCount = $recordCount + 1;
        if ($devotee_seva_old != $devoteeRecord["Seva_description"]) {
            $devotee_seva_old = $devoteeRecord["Seva_description"];
            generateReport("tableClose", $devoteeRecord);
            $rptLayout[] = "tableClose";
            generateReport("title", $devoteeRecord);
            $rptLayout[] = "title";
            generateReport("tableCreate", $devoteeRecord);
            $rptLayout[] = "tableCreate";
            generateReport("tableHeader", $devoteeRecord, $photoRequired);
            $rptLayout[] = "tableHeader";
        }
        generateReport("row", $devoteeRecord, $photoRequired, $recordCount);
        $rptLayout[] = "row";
    } //end of foreach ($response as $devoteeRecord) 
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
function generateReport($rptElmt, $rptData, $photoRequired = false, $recordCount = 0) {
    $ttlParam = array("Seva_description" => " ");
    $hdrParamWOP = array("SN", "Name", "Station", "Cell Phone", "Assigned Seva");
    $hdrParamWP = array("SN", "Name", "Station", "Cell Phone", "Assigned Seva", "Photo");
    $rowParamWOP = array("SN" => "", "Devotee_Name" => "", "devotee_station" => "", "devotee_cell_phone_number" => "", "Seva_description" => "");
    $rowParamWP = array("SN" => "", "Devotee_Name" => "", "devotee_station" => "", "devotee_cell_phone_number" => "", "Seva_description" => "", "Devotee_Photo" => "");
    $ftrParam = array("Sr" => "");

    switch ($rptElmt) {
        case "title":
            $ttlParam["Seva_description"] = urldecode($rptData["Seva_description"]);
            printTitle($ttlParam);

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

        case "footer":
            printFooter("</table>");

            break;
    } // end of switch
}
?>
