<?php
$config_data = include("../site_config.php");

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$current_page_id = 'KR-R-ACCO';
include_once("../sessionCheck.php");
include_once("rptGenerator.php");
include_once("../Logic/clsServicesManager.php");

$eventId = $_SESSION['eventId'];
$userId = $_SESSION['LoginID'];
$debug = false;
$response = array();
$availableRows = array();
$unavailableRows = array();
$outOfPremisesRows = array();
$managedByOfficeRows = array();

$reportService = new clsServicesManager("");
$reportService->setOptionType("accoAvailability");
$reportService->setEventId($eventId);
$response = $reportService->getRecords();
unset($reportService);
?>
<script src="../assets/js/core/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../assets/css/bootstrap.min.css">
<?php include_once("../UI/scriptJS.php"); ?>
<html>
<head>
    <title>Accommodation Availability Report</title>
    <script>
        function printdiv() {
            var newstr = document.getElementById("printpage").innerHTML;
            var popupWin = window.open('', 'blank', 'width=800px,height=900px');
            popupWin.document.open();
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
if ($debug) {
    var_dump($response);
    die;
}

if (isset($response["status"]) && $response["status"] == false) {
    echo "ERROR MESSAGE: ";
    echo $response["message"];
    die;
}

generateReport("superTitle", "BABA SHREE NEEM KAROLI MAHARAAJ JI KI JAI");
generateReport("title", "ACCOMMODATION AVAILABILITY REPORT");

if (!empty($response)) {
    foreach ($response as $row) {
        $row['accomodation_capacity'] = (int)($row['accomodation_capacity'] ?? 0);
        $row['allocated_count'] = (int)($row['allocated_count'] ?? 0);
        $row['available_count'] = (int)($row['available_count'] ?? 0);
        $accoName = strtolower((string)($row['accomodation_name'] ?? ''));
        $isManagedByOffice = (strpos($accoName, 'reserved') !== false);
        $isUnavailableByName = (strpos($accoName, 'unavailable') !== false) || (strpos($accoName, 'removed') !== false);
        $isUnavailableByCapacity = ((int)$row['accomodation_capacity'] === 0);
        $isUnavailable = $isUnavailableByName || $isUnavailableByCapacity;
        $isOutOfPremises = ((int)$row['accomodation_capacity'] > 999);

        if ($isManagedByOffice) {
            $managedByOfficeRows[] = $row;
        } elseif ($isUnavailable) {
            $unavailableRows[] = $row;
        } elseif ($isOutOfPremises) {
            $outOfPremisesRows[] = $row;
        } else {
            $availableRows[] = $row;
        }
    }
}

renderGroupTable(
    urldecode($eventId) . " Accommodations Status",
    $availableRows,
    "AVAILABLE"
);

renderGroupTable(
    "Reserved Accommodations for " . urldecode($eventId),
    $managedByOfficeRows,
    "RESERVED"
);

renderGroupTable(
    "Unavailable Accommodations for " . urldecode($eventId),
    $unavailableRows,
    "UNAVAILABLE"
);

renderGroupTable(
    "Out of Premises Accommodation Counts for " . urldecode($eventId),
    $outOfPremisesRows,
    "OUT OF PREMISES"
);

generateReport("footer", "");

function renderGroupTable($groupTitle, $rows, $groupLabel)
{
    generateReport("tableCreate", "");
    generateReport("groupTitleInTable", $groupTitle);
    generateReport("tableHeader", "");

    $sn = 0;
    $totalCapacity = 0;
    $totalAllocated = 0;
    if (!empty($rows)) {
        foreach ($rows as $row) {
            $sn++;
            $row['SN'] = $sn;
            $totalCapacity += (int)$row['accomodation_capacity'];
            $totalAllocated += (int)$row['allocated_count'];
            generateReport("row", $row);
        }
    }

    generateReport("footerRow", array(
        "group_label" => $groupLabel,
        "total_capacity" => $totalCapacity,
        "total_allocated" => $totalAllocated
    ));
    generateReport("tableClose", "");
}

function generateReport($rptElmt, $rptData)
{
    $hdrParam = array("SN", "Name", "Capacity", "Allocated", "Available");
    $rowParam = array(
        "SN" => "",
        "accomodation_name" => "",
        "accomodation_capacity" => "",
        "allocated_count" => "",
        "available_count" => ""
    );

    switch ($rptElmt) {
        case "superTitle":
            printSuperTitle(array("super_title" => $rptData));
            break;
        case "title":
            printTitle(array("title" => $rptData));
            break;
        case "groupTitleInTable":
            // Keep subheader in-grid and within the same 5 columns.
            echo "<tr>";
            echo "<th colspan='5' class='reportHead' style='text-align:left;'>";
            echo "<span>" . htmlspecialchars((string)$rptData, ENT_QUOTES, 'UTF-8') . "</span>";
            echo "<span style='float:right;'>[Dated - " . date('d/M/Y') . "]</span>";
            echo "</th>";
            echo "</tr>";
            break;
        case "tableCreate":
            printTable("");
            break;
        case "tableHeader":
            echo "<tr>";
            echo "<th class='reportHead' style='width:8%;border:1px solid #000;'><label>SN</label></th>";
            echo "<th class='reportHead' style='width:62.5%;border:1px solid #000;'><label>Name</label></th>";
            echo "<th class='reportHead' style='width:9.833%;border:1px solid #000;'><label>Capacity</label></th>";
            echo "<th class='reportHead' style='width:9.833%;border:1px solid #000;'><label>Allocated</label></th>";
            echo "<th class='reportHead' style='width:9.833%;border:1px solid #000;'><label>Available</label></th>";
            echo "</tr>";
            break;
        case "row":
            $displayRow = $rowParam;
            foreach (array_keys($rowParam) as $key) {
                if (isset($rptData[$key])) {
                    $displayRow[$key] = urldecode((string)$rptData[$key]);
                }
            }
            echo "<tr>";
            echo "<td style='width:8%;border:1px solid #000;'>" . htmlspecialchars((string)$displayRow["SN"], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td style='width:62.5%;border:1px solid #000;'>" . htmlspecialchars((string)$displayRow["accomodation_name"], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td style='width:9.833%;border:1px solid #000;text-align:right;'>" . htmlspecialchars((string)$displayRow["accomodation_capacity"], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td style='width:9.833%;border:1px solid #000;text-align:right;'>" . htmlspecialchars((string)$displayRow["allocated_count"], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td style='width:9.833%;border:1px solid #000;text-align:right;'>" . htmlspecialchars((string)$displayRow["available_count"], ENT_QUOTES, 'UTF-8') . "</td>";
            echo "</tr>";
            break;
        case "footerRow":
            $label = isset($rptData["group_label"]) ? (string)$rptData["group_label"] : "TOTAL";
            $totalCapacity = (int)($rptData["total_capacity"] ?? 0);
            $totalAllocated = (int)($rptData["total_allocated"] ?? 0);
            echo "<tr style='font-weight:bold;font-size:104%;'>";
            echo "<td style='border:1px solid #000;'></td>";
            echo "<td style='border:1px solid #000;'>" . htmlspecialchars($label . " TOTAL", ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td style='border:1px solid #000;text-align:right;'>" . $totalCapacity . "</td>";
            echo "<td style='border:1px solid #000;text-align:right;'>" . $totalAllocated . "</td>";
            echo "<td style='border:1px solid #000;'></td>";
            echo "</tr>";
            break;
        case "tableClose":
            printCloseTable("");
            break;
        case "footer":
            printFooter("</table>");
            includeFavModal("../UI/rptFavModal.php");
            break;
    }
}
?>
    <br>
    </div>
</div>
<a href="#" onclick="printdiv()"><button>Print </button> </a>
</body>
</html>
