<?php
// getRegistrationCounts.php - Provides JSON data for AJAX refresh of registration counts
header('Content-Type: application/json');

// Include necessary files
$config_data = include("../site_config.php");
include_once("../Logic/clsDevoteeSearch.php");
include_once("../Logic/clsReportHandler.php");
include_once("../Logic/clsOptionHandler.php");

$eventId = $config_data['event_id'];
$debug = false; // Set to true for debugging

// Get accommodation and devotee counts
$getReport = new clsReportHandler();
try {
    $response = $getReport->getAccomodationCountsForEventDashbaord($eventId);
    
    // Handle different possible response formats (direct array or array of arrays)
    if (isset($response[0]) && is_array($response[0])) {
        // If it's an array of arrays, take the first item
        $response = $response[0];
    }
    
    $status = "success";
    
    if($debug) {
        error_log("API Response: " . print_r($response, true));
    }
} catch (Exception $e) {
    // Create an empty response structure if data cannot be retrieved
    $response = [
        'Event_ID' => $eventId,
        'Total_Registration_Count' => 0,
        'Ashram_Residents_Count' => 0,
        'Temporary_Day_Visitors_Count' => 0,
        'OwnArrangement_Local_Count' => 0
    ];
    $status = "error";
    $errorMessage = $e->getMessage();
}
unset($getReport);

// Extract the data values and ensure they're properly converted to integers
$eventId = !empty($response['Event_ID']) ? $response['Event_ID'] : $config_data['event_id'];
$totalCount = !empty($response['Total_Registration_Count']) ? intval($response['Total_Registration_Count']) : 0;
$ashramResidingCount = !empty($response['Ashram_Residents_Count']) ? intval($response['Ashram_Residents_Count']) : 0;
$tempRegistrationCount = !empty($response['Temporary_Day_Visitors_Count']) ? intval($response['Temporary_Day_Visitors_Count']) : 0;
$ownArrangementCount = !empty($response['OwnArrangement_Local_Count']) ? intval($response['OwnArrangement_Local_Count']) : 0;

// Add debug logging to troubleshoot
if($debug) {
    error_log("Extracted values:");
    error_log("Total: $totalCount");
    error_log("Ashram: $ashramResidingCount");
    error_log("Temporary: $tempRegistrationCount");
    error_log("Own Arrangement: $ownArrangementCount");
}

// Calculate percentages for the UI display
$ashramPercentage = $totalCount > 0 ? round(($ashramResidingCount / $totalCount) * 100) : 0;
$tempPercentage = $totalCount > 0 ? round(($tempRegistrationCount / $totalCount) * 100) : 0;
$ownArrangementPercentage = $totalCount > 0 ? round(($ownArrangementCount / $totalCount) * 100) : 0;

// Prepare the data to return
$data = [
    'status' => $status,
    'eventId' => $eventId,
    'totalCount' => $totalCount,
    'ashramResidingCount' => $ashramResidingCount,
    'tempRegistrationCount' => $tempRegistrationCount,
    'ownArrangementCount' => $ownArrangementCount,
    'ashramPercentage' => $ashramPercentage,
    'tempPercentage' => $tempPercentage,
    'ownArrangementPercentage' => $ownArrangementPercentage,
    'refreshTime' => date('H:i:s')
];

// Add error message if any
if (isset($errorMessage)) {
    $data['errorMessage'] = $errorMessage;
}

echo json_encode($data);
?>
