<?php
$config_data = include("../../site_config.php");
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once("../../sessionCheck.php");
include_once("../../Logic/clsServicesManager.php");

$eventId = isset($_SESSION['eventId']) ? (string)$_SESSION['eventId'] : "";
$reportId = isset($_GET['report_id']) ? trim((string)$_GET['report_id']) : "";
$key = isset($_GET['key']) ? trim((string)$_GET['key']) : "";

if ($eventId === "" || $reportId === "") {
    http_response_code(400);
    echo "Missing report context.";
    exit;
}

$reportConfig = [
    "DRWP" => [
        "optionType" => "ADS",
        "request" => ["key" => $key],
        "filePrefix" => "general-seva-report",
        "columns" => ["Seva_description", "Devotee_Name", "devotee_key", "devotee_station", "devotee_cell_phone_number", "remarks"],
    ],
    "DRWOP" => [
        "optionType" => "ADS",
        "request" => ["key" => $key],
        "filePrefix" => "general-seva-report-no-photo",
        "columns" => ["Seva_description", "Devotee_Name", "devotee_key", "devotee_station", "devotee_cell_phone_number", "remarks"],
    ],
    "ADRWP" => [
        "optionType" => "DSA",
        "request" => ["key" => $key],
        "filePrefix" => "attendance-report",
        "columns" => ["Seva_description", "Devotee_Name", "devotee_key", "devotee_station", "devotee_cell_phone_number", "attendance", "mark_attendance"],
    ],
    "ODRWP" => [
        "optionType" => "DutyReport",
        "request" => ["key" => $key, "photo_required" => "N"],
        "filePrefix" => "office-duty-report",
        "columns" => ["duty_location_name", "devotee_name", "devotee_key", "devotee_cell_phone_number", "delivery_signature", "return_signature"],
    ],
    "ODRWOP" => [
        "optionType" => "DutyReport",
        "request" => ["key" => $key, "photo_required" => "N"],
        "filePrefix" => "office-duty-report-no-photo",
        "columns" => ["duty_location_name", "devotee_name", "devotee_key", "devotee_cell_phone_number", "delivery_signature", "return_signature"],
    ],
    "ARWP" => [
        "optionType" => "AOD",
        "request" => $key,
        "filePrefix" => "accommodation-report",
        "columns" => ["accomodation_name", "arrival_date_time", "Devotee_Name", "devotee_key", "devotee_gender", "devotee_cell_phone_number", "Devotee_address"],
    ],
    "ARWOP" => [
        "optionType" => "AOD",
        "request" => $key,
        "filePrefix" => "accommodation-report-no-photo",
        "columns" => ["accomodation_name", "arrival_date_time", "Devotee_Name", "devotee_key", "devotee_gender", "devotee_cell_phone_number", "Devotee_address"],
    ],
    "AAR" => [
        "optionType" => "accoAvailability",
        "request" => "",
        "filePrefix" => "accommodation-availability-report",
        "columns" => ["SN", "Name", "Capacity", "Allocated", "Available"],
    ],
];

if (!isset($reportConfig[$reportId])) {
    http_response_code(400);
    echo "Unsupported report for export.";
    exit;
}

$cfg = $reportConfig[$reportId];
$service = new clsServicesManager($cfg["request"]);
$service->setOptionType($cfg["optionType"]);
$service->setEventId($eventId);
$response = $service->getRecords();
unset($service);

if (isset($response["status"]) && $response["status"] === false) {
    http_response_code(502);
    echo "Export failed: " . (isset($response["message"]) ? $response["message"] : "API returned error.");
    exit;
}

if (!is_array($response)) {
    $response = [];
}

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"" . $cfg["filePrefix"] . "-" . date("Ymd-His") . ".csv\"");
header("Pragma: no-cache");
header("Expires: 0");

$out = fopen("php://output", "w");

if ($reportId === "AAR") {
    $availableRows = [];
    $reservedRows = [];
    $unavailableRows = [];
    $outOfPremisesRows = [];

    foreach ($response as $row) {
        if (!is_array($row)) {
            continue;
        }
        $name = trim(urldecode((string)($row['accomodation_name'] ?? '')));
        $nameLower = strtolower($name);
        $capacity = (int)($row['accomodation_capacity'] ?? 0);
        $allocated = (int)($row['allocated_count'] ?? 0);
        $available = (int)($row['available_count'] ?? 0);

        $normalized = [
            'name' => $name,
            'capacity' => $capacity,
            'allocated' => $allocated,
            'available' => $available,
        ];

        $isReserved = (strpos($nameLower, 'reserved') !== false);
        $isUnavailable = (strpos($nameLower, 'unavailable') !== false) || (strpos($nameLower, 'removed') !== false) || $capacity === 0;
        $isOutOfPremises = $capacity > 999;

        if ($isReserved) {
            $reservedRows[] = $normalized;
        } elseif ($isUnavailable) {
            $unavailableRows[] = $normalized;
        } elseif ($isOutOfPremises) {
            $outOfPremisesRows[] = $normalized;
        } else {
            $availableRows[] = $normalized;
        }
    }

    $groups = [
        urldecode($eventId) . " Accommodations Status" => $availableRows,
        "Reserved Accommodations for " . urldecode($eventId) => $reservedRows,
        "Unavailable Accommodations for " . urldecode($eventId) => $unavailableRows,
        "Out of Premises Accommodation Counts for " . urldecode($eventId) => $outOfPremisesRows,
    ];

    foreach ($groups as $title => $rows) {
        fputcsv($out, [$title, "", "", "", ""]);
        fputcsv($out, $cfg["columns"]);
        $sn = 0;
        $totalCapacity = 0;
        $totalAllocated = 0;
        foreach ($rows as $row) {
            $sn++;
            $totalCapacity += (int)$row['capacity'];
            $totalAllocated += (int)$row['allocated'];
            fputcsv($out, [
                $sn,
                $row['name'],
                $row['capacity'],
                $row['allocated'],
                $row['available'],
            ]);
        }
        fputcsv($out, ["", "TOTAL", $totalCapacity, $totalAllocated, ""]);
        fputcsv($out, ["", "", "", "", ""]);
    }
} else {
    fputcsv($out, $cfg["columns"]);
    foreach ($response as $row) {
        if (!is_array($row)) {
            continue;
        }
        $csvRow = [];
        foreach ($cfg["columns"] as $col) {
            $val = isset($row[$col]) ? (string)$row[$col] : "";
            $csvRow[] = trim(urldecode($val));
        }
        fputcsv($out, $csvRow);
    }
}

fclose($out);
exit;
