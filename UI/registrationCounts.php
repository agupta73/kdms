<?php
$config_data = include("../site_config.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Header removed for authentication-free access
// Session check removed for authentication-free access
include_once("../Logic/clsDevoteeSearch.php");
include_once("../Logic/clsReportHandler.php");
include_once("../Logic/clsOptionHandler.php");
// Include new config file in each page
$config_data = include("../site_config.php");

$eventId = $config_data['event_id'];
$debug = false;

// Get accommodation and devotee counts
$getReport = new clsReportHandler();
try {
    $response = $getReport->getAccomodationCountsForEventDashbaord($eventId);
    
    // Handle different possible response formats (direct array or array of arrays)
    if (isset($response[0]) && is_array($response[0])) {
        // If it's an array of arrays, take the first item
        $response = $response[0];
    }
} catch (Exception $e) {
    // Create an empty response structure if data cannot be retrieved
    $response = [
        'Total_Registration_Count' => 0,
        'Ashram_Residents_Count' => 0,
        'Temporary_Day_Visitors_Count' => 0,
        'OwnArrangement_Local_Count' => 0
    ];
}
unset($getReport);
if($debug){
    echo "eventId =: ", $config_data['event_id']; 
    var_dump($response);
}

// Calculate the statistics we need from the response
$eventId = !empty($response['Event_ID']) ? $response['Event_ID'] : $config_data['event_id'];
$registeredDevoteesCount = !empty($response['Total_Registration_Count']) ? intval($response['Total_Registration_Count']) : 0;
$ashramResidingCount = !empty($response['Ashram_Residents_Count']) ? intval($response['Ashram_Residents_Count']) : 0;
$tempRegistrationCount = !empty($response['Temporary_Day_Visitors_Count']) ? intval($response['Temporary_Day_Visitors_Count']) : 0;
$ownArrangementCount = !empty($response['OwnArrangement_Local_Count']) ? intval($response['OwnArrangement_Local_Count']) : 0;

// Calculate percentages for visual indicators
$ashramPercentage = $registeredDevoteesCount > 0 ? round(($ashramResidingCount / $registeredDevoteesCount) * 100) : 0;
$tempPercentage = $registeredDevoteesCount > 0 ? round(($tempRegistrationCount / $registeredDevoteesCount) * 100) : 0;
$ownArrangementPercentage = $registeredDevoteesCount > 0 ? round(($ownArrangementCount / $registeredDevoteesCount) * 100) : 0;

// Total count is the sum of all registered devotees
$totalCount = $registeredDevoteesCount;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Registration Counts</title>
    <style>
        * {
            box-sizing: border-box;
        }
        html, body {
            height: 100%;
            width: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Prevent scrolling at the document level */
        }
        body {
            font-family: 'Roboto', 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            -webkit-text-size-adjust: 100%;
            -webkit-font-smoothing: antialiased;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 100%;
            height: 100vh;
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: min(30px, 3vh);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            overflow: hidden; /* Prevent internal scrolling */
        }
        .header {
            text-align: center;
            margin-bottom: min(20px, 2vh);
            padding-bottom: 8px;
            border-bottom: 2px solid #9c27b0;
            flex-shrink: 0;
        }
        .header h1 {
            color: #9c27b0;
            margin-bottom: 5px;
            font-size: clamp(24px, 4vh, 36px);
        }
        .counts-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex-grow: 1;
            justify-content: center;
            width: 100%;
            margin-bottom: min(20px, 2vh);
            overflow: hidden; /* Prevent internal scrolling */
        }
        .count-row {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: min(15px, 1.5vh);
            gap: min(20px, 2vw);
        }
        .count-box {
            background-color: #fff;
            border-radius: 10px;
            padding: clamp(12px, 2.5vh, 30px) clamp(18px, 2.5vw, 35px);
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
            width: 100%;
            transition: transform 0.3s, box-shadow 0.3s;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .count-box-left {
            margin-right: min(7px, 0.7vw);
        }
        .count-box-right {
            margin-left: min(7px, 0.7vw);
        }
        .count-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
        }
        .total-box {
            background-color: #9c27b0;
            color: white;
            border-radius: 15px;
            padding: clamp(25px, 5vh, 60px) clamp(25px, 4vw, 50px);
            text-align: center;
            box-shadow: 0 12px 40px rgba(156, 39, 176, 0.5);
            margin-bottom: min(30px, 4vh);
            width: 100%;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .total-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(156, 39, 176, 0.7);
        }
        .count-title {
            font-size: clamp(16px, 2.2vh, 24px);
            font-weight: 600;
            margin-bottom: min(12px, 1.2vh);
            color: #555;
        }
        .total-title {
            font-size: clamp(20px, 3vh, 30px);
            font-weight: 600;
            margin-bottom: min(15px, 1.5vh);
            color: #fff;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .count-value {
            font-size: clamp(24px, 3.5vh, 42px);
            font-weight: 700;
            color: #333;
            display: flex;
            align-items: baseline;
            justify-content: center;
            line-height: 1.1;
        }
        .total-value {
            font-size: clamp(48px, 8vh, 80px);
            font-weight: 700;
            color: #fff;
            line-height: 1.1;
            text-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        .percentage {
            font-size: clamp(12px, 1.5vh, 18px);
            font-weight: 500;
            color: #666;
            margin-left: min(6px, 1vw);
        }
        .event-id {
            font-size: clamp(12px, 1.5vh, 16px);
            color: rgba(255, 255, 255, 0.8);
            margin-top: 5px;
        }
        .progress-bar {
            height: min(8px, 0.8vh);
            width: 100%;
            background-color: #f0f0f0;
            border-radius: 4px;
            margin-top: min(12px, 1.2vh);
            overflow: hidden;
            box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
        }
        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.8s ease;
        }
        .progress-fill.ashram {
            background-color: #4CAF50;
            background-image: linear-gradient(to right, #43A047, #4CAF50);
        }
        .progress-fill.temp {
            background-color: #FFA000;
            background-image: linear-gradient(to right, #FF8F00, #FFA000);
        }
        .progress-fill.own {
            background-color: #2196F3;
            background-image: linear-gradient(to right, #1E88E5, #2196F3);
        }
        /* Button styles removed as they are no longer needed */
        /* Responsive styles for different screen sizes */
        @media screen and (max-width: 1024px) {
            .container {
                padding: min(20px, 2vh);
                border-radius: 0;
                max-width: 100%;
                height: 100vh;
            }
        }
        
        @media screen and (max-width: 768px) {
            .container {
                padding: min(15px, 1.5vh);
                border-radius: 0;
            }
            .date-display {
                text-align: center;
            }
            .refresh-info {
                padding: min(10px, 1vh) min(10px, 1vw);
            }
            .refresh-info p {
                margin: min(8px, 0.8vh) 0;
            }
        }
        
        @media screen and (max-width: 480px) {
            .container {
                padding: min(10px, 1vh);
                border-radius: 0;
            }
            .header {
                margin-bottom: min(10px, 1vh);
                padding-bottom: min(5px, 0.5vh);
            }
            /* Switch to a simplified layout for smallest screens */
            .count-row {
                flex-direction: column;
                gap: min(10px, 1vh);
            }
            .count-box {
                margin: 0 !important;
                padding: min(12px, 1.2vh) min(10px, 1vw);
            }
            .total-box {
                margin-bottom: min(18px, 2vh);
                padding: min(18px, 2vh) min(12px, 1.5vw);
            }
            .refresh-info {
                margin-top: min(10px, 1vh);
                padding: min(8px, 0.8vh) min(5px, 0.5vw);
            }
            .btn-icon {
                padding: 1px min(5px, 0.5vw);
            }
            .progress-bar {
                margin-top: min(8px, 0.8vh);
            }
        }
        
        /* Extra small screens and low height */
        @media screen and (max-width: 320px), (max-height: 600px) {
            .container {
                padding: min(8px, 0.8vh);
            }
            .header p {
                margin: min(2px, 0.2vh) 0;
                font-size: clamp(11px, 1.3vh, 14px);
            }
            .total-box {
                padding: min(15px, 1.5vh) min(12px, 1.2vw);
            }
            .refresh-info p {
                margin: min(4px, 0.4vh) 0;
            }
            /* Ultra compact mode for very small screens */
            .refresh-info .last-updated:nth-child(3) {
                display: none;
            }
        }
        
        /* Optimize for landscape orientation on mobile */
        @media screen and (max-height: 500px) and (orientation: landscape) {
            .count-row {
                flex-direction: row;
                gap: min(8px, 0.8vw);
            }
            .total-box {
                padding: min(12px, 1.2vh);
                margin-bottom: min(12px, 1.2vh);
            }
            .count-box {
                padding: min(8px, 0.8vh) min(10px, 1vw);
            }
            .refresh-info {
                display: flex;
                justify-content: space-between;
            }
            .refresh-info p {
                margin: 0 min(10px, 1vw);
            }
        }
        
        @media print {
            .no-print {
                display: none;
            }
            .container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                width: 100%;
            }
            body {
                background-color: white;
                padding: 0;
            }
        }
        .date-display {
            text-align: right;
            font-size: clamp(12px, 1.4vh, 14px);
            color: #666;
            margin-bottom: min(10px, 1vh);
            flex-shrink: 0;
        }
        .updated {
            animation: highlight 1s ease-in-out;
        }
        @keyframes highlight {
            0% { background-color: rgba(255, 193, 7, 0.8); }
            100% { background-color: transparent; }
        }
        .refresh-info {
            text-align: center;
            margin-top: min(15px, 1.5vh);
            margin-bottom: min(10px, 1vh);
            font-size: clamp(11px, 1.3vh, 14px);
            color: #888;
            background-color: #f8f9fa;
            padding: clamp(8px, 1vh, 12px);
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            opacity: 0.9;
            width: 100%;
            box-sizing: border-box;
            overflow: hidden;
            flex-shrink: 0;
        }
        .timer {
            font-weight: bold;
            color: #9c27b0;
            font-size: clamp(12px, 1.4vh, 16px);
        }
        .last-updated {
            font-style: italic;
            margin-top: min(5px, 0.6vh);
            color: #777;
        }
        .updated {
            animation: highlight 1.5s ease-in-out;
        }
        @keyframes highlight {
            0% { background-color: rgba(156, 39, 176, 0.2); box-shadow: 0 0 15px rgba(156, 39, 176, 0.4); }
            50% { background-color: rgba(156, 39, 176, 0.1); box-shadow: 0 0 10px rgba(156, 39, 176, 0.2); }
            100% { background-color: transparent; box-shadow: none; }
        }
        .highlight-change {
            color: #9c27b0;
            font-weight: bold;
            animation: pulse 1.5s ease-in-out;
        }
        @keyframes pulse {
            0% { transform: scale(1.1); }
            50% { transform: scale(1); }
            100% { transform: scale(1); }
        }
        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            vertical-align: middle;
            padding: 2px 8px;
            border-radius: 50%;
            transition: all 0.3s;
        }
        .btn-icon:hover {
            background-color: rgba(156, 39, 176, 0.1);
        }
        .status-indicator {
            margin-top: 10px;
            font-size: 12px;
        }
        .status-active {
            color: #4CAF50;
            font-weight: bold;
        }
        .status-paused {
            color: #FFA000;
            font-weight: bold;
        }
        .status-error {
            color: #F44336;
            font-weight: bold;
        }
        .small-text {
            font-size: clamp(9px, 1vh, 11px);
            color: #999;
            margin-left: 5px;
        }
        /* Additional class for landscape mode */
        .refresh-info.landscape-mode {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: center;
        }
        .refresh-info.landscape-mode p {
            margin: 0 min(5px, 0.5vw);
            flex: 0 1 auto;
        }
        /* Ultra-compact view styles */
        .ultra-compact-view .container {
            padding: min(5px, 0.5vh) !important;
        }
        .ultra-compact-view .header {
            margin-bottom: min(5px, 0.5vh);
        }
        .ultra-compact-view .header h1 {
            margin: 0;
            font-size: clamp(18px, 3vh, 24px);
        }
        .ultra-compact-view .header p {
            font-size: clamp(10px, 1.2vh, 12px);
            margin: 0;
        }
        .ultra-compact-view .total-box {
            padding: min(10px, 1vh);
            margin-bottom: min(10px, 1vh);
        }
        .ultra-compact-view .count-box {
            padding: min(6px, 0.6vh);
            margin-bottom: min(6px, 0.6vh) !important;
        }
        .ultra-compact-view .refresh-info {
            padding: min(3px, 0.3vh);
            margin-top: min(6px, 0.6vh);
        }
        .ultra-compact-view .refresh-info p {
            margin: min(2px, 0.2vh) 0;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Registration Summary</h1>
        <p>Event: <?php
            // Get event description without requiring authentication
            $eventId = $config_data['event_id'];
            if(isset($eventId)) {
                echo "Event ID: " . htmlspecialchars($eventId);
            } else {
                echo "Current Event";
            }
        ?> | Date: <?php echo date('j F Y'); ?></p>
    </div>

    <div class="counts-container">
        <!-- Total Registration Count (Large) -->
        <div class="total-box">
            <div class="total-title">Total Registrations</div>
            <div id="totalCount" class="total-value"><?php echo number_format($totalCount); ?></div>
        </div>
        
        <div class="count-row">
            <!-- Registrations Residing in Ashram -->
            <div class="count-box count-box-left">
                <div class="count-title">Devotees Residing in Ashram</div>
                <div id="ashramCount" class="count-value"><?php echo number_format($ashramResidingCount); ?>
                    <span class="percentage"><?php echo $ashramPercentage; ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill ashram" style="width: <?php echo $ashramPercentage; ?>%"></div>
                </div>
            </div>
            
            <!-- Own Arrangement Count -->
            <div class="count-box count-box-left">
                <div class="count-title">Own Arrangement / Local</div>
                <div id="ownArrangementCount" class="count-value"><?php echo number_format($ownArrangementCount); ?>
                    <span class="percentage"><?php echo $ownArrangementPercentage; ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill own" style="width: <?php echo $ownArrangementPercentage; ?>%"></div>
                </div>
            </div>
        </div>
        
        <!-- <div class="count-row">

            <!-- Temporary Registration -->
            <!-- <div class="count-box count-box-right">
                <div class="count-title">Temporary Cards/Day Visitors</div>
                <div id="tempCount" class="count-value"><?php echo number_format($tempRegistrationCount); ?>
                    <span class="percentage"><?php echo $tempPercentage; ?>%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill temp" style="width: <?php echo $tempPercentage; ?>%"></div>
                </div>
            </div> -->
            <!-- Reserved vs Available Ratio -->
            <!-- <div class="count-box count-box-right">
                <div class="count-title">Ashram vs Temporary Ratio</div>
                <div id="ratioDisplay" class="count-value">
                    <?php echo $ashramPercentage; ?>% / <?php echo $tempPercentage; ?>%
                </div>
                <div class="progress-bar" style="height: 12px; margin-top: 20px;">
                    <div class="progress-fill ashram" style="width: <?php echo $ashramPercentage; ?>%; float: left;"></div>
                    <div class="progress-fill temp" style="width: <?php echo $tempPercentage; ?>%; float: left;"></div>
                </div>
            </div> -->
        <!-- </div> -->
    </div>
    
    <div class="refresh-info">
        <p>Auto-refreshing in <span id="refreshTimer" class="timer">30</span> seconds 
        <button id="pauseRefresh" class="btn-icon" onclick="togglePauseRefresh()">⏸️</button>
        </p>
        <p class="last-updated">Last refresh: <span id="lastRefreshed"><?php echo date('H:i:s'); ?></span> <span id="responseTime" class="small-text"></span></p>
        <p class="last-updated">Last change: <span id="lastChanged">-</span></p>
        <p class="status-indicator"><span id="connectionStatus" class="status-active">Active</span></p>
    </div>
</div>
<script>
    // Set up auto-refresh functionality with enhanced features
    document.addEventListener('DOMContentLoaded', function() {
        // Set viewport height for mobile browsers (fixes 100vh issues)
        setViewportHeight();
        
        // Initial data fetch
        fetchLatestCounts();
        
        // Store previous values to detect changes
        window.previousValues = {
            totalCount: 0,
            ashramCount: 0,
            tempCount: 0,
            reservedCount: 0,
            availableCount: 0
        };
        
        // Handle window resize events and orientation changes
        window.addEventListener('resize', function() {
            // Update the UI based on screen size
            updateUIForScreenSize();
        });
        
        // Also listen for orientation changes specifically
        window.addEventListener('orientationchange', function() {
            // Slight delay to ensure dimensions are updated first
            setTimeout(updateUIForScreenSize, 100);
        });
        
        // Set up adaptive refresh interval
        // Start with 30 seconds, will adjust based on activity
        window.refreshInterval = 30000; // 30 seconds
        window.minRefreshInterval = 10000; // 10 seconds
        window.maxRefreshInterval = 60000; // 60 seconds
        window.backgroundRefreshInterval = 120000; // 2 minutes when page is in background
        window.consecutiveNoChanges = 0;
        window.maxConsecutiveNoChanges = 5;
        window.refreshPaused = false;
        
        // Schedule the first refresh
        scheduleNextRefresh();
        
        // Update connection status when visibility changes
        document.addEventListener('visibilitychange', function() {
            updateConnectionStatus();
        });
        
        // Update status initially
        updateConnectionStatus();
        
        // Initialize UI based on screen size
        updateUIForScreenSize();
    });
    
    // Toggle pause/resume auto-refresh
    function togglePauseRefresh() {
        if (window.refreshPaused) {
            // Resume refreshing
            window.refreshPaused = false;
            document.getElementById('pauseRefresh').innerHTML = '⏸️';
            document.getElementById('connectionStatus').innerHTML = 'Active';
            document.getElementById('connectionStatus').className = 'status-active';
            // Immediately schedule the next refresh
            scheduleNextRefresh();
        } else {
            // Pause refreshing
            window.refreshPaused = true;
            document.getElementById('pauseRefresh').innerHTML = '▶️';
            document.getElementById('connectionStatus').innerHTML = 'Paused';
            document.getElementById('connectionStatus').className = 'status-paused';
            // Clear any existing refresh timer
            if (window.refreshTimer) {
                clearTimeout(window.refreshTimer);
            }
            // Clear the countdown display
            if (window.refreshTimerInterval) {
                clearInterval(window.refreshTimerInterval);
                document.getElementById('refreshTimer').textContent = '--';
            }
        }
    }
    
    // Update the connection status based on page visibility and refresh status
    function updateConnectionStatus() {
        if (window.refreshPaused) {
            document.getElementById('connectionStatus').innerHTML = 'Paused';
            document.getElementById('connectionStatus').className = 'status-paused';
        } else if (document.visibilityState === 'hidden') {
            document.getElementById('connectionStatus').innerHTML = 'Background';
            document.getElementById('connectionStatus').className = 'status-paused';
        } else {
            document.getElementById('connectionStatus').innerHTML = 'Active';
            document.getElementById('connectionStatus').className = 'status-active';
        }
    }
    
    // Schedule the next data refresh with adaptive timing
    function scheduleNextRefresh() {
        // Don't schedule if paused
        if (window.refreshPaused) {
            return;
        }
        
        // Clear any existing timers
        if (window.refreshTimer) {
            clearTimeout(window.refreshTimer);
        }
        
        // Use longer interval if page is in background to save resources
        var interval = document.visibilityState === 'hidden' ? window.backgroundRefreshInterval : window.refreshInterval;
        
        // Set up the refresh timer display
        setupRefreshTimer(interval / 1000);
        
        // Schedule the next data fetch
        window.refreshTimer = setTimeout(fetchLatestCounts, interval);
        
        // Update connection status to reflect background state if needed
        updateConnectionStatus();
    }
    
    // Function to fetch the latest counts via AJAX
    function fetchLatestCounts() {
        // Record start time to measure response time
        var startTime = new Date();
        
        // Create an AJAX request to fetch just the counts data
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'getRegistrationCounts.php', true);
        
        xhr.onload = function() {
            // Calculate response time
            var endTime = new Date();
            var responseTime = endTime - startTime;
            if (this.status == 200) {
                try {
                    var data = JSON.parse(this.responseText);
                    
                    // Track which elements have changed
                    var changedElements = [];
                    
                    // Check if total count changed
                    if (!window.previousValues.totalCount || window.previousValues.totalCount != data.totalCount) {
                        changedElements.push('totalCount');
                        window.previousValues.totalCount = data.totalCount;
                    }
                    
                    // Check if ashram count changed
                    if (!window.previousValues.ashramCount || window.previousValues.ashramCount != data.ashramResidingCount) {
                        changedElements.push('ashramCount');
                        window.previousValues.ashramCount = data.ashramResidingCount;
                    }
                    
                    // Check if temp count changed
                    if (!window.previousValues.tempCount || window.previousValues.tempCount != data.tempRegistrationCount) {
                        changedElements.push('tempCount');
                        window.previousValues.tempCount = data.tempRegistrationCount;
                    }
                    
                    // Check if own arrangement count changed
                    if (!window.previousValues.ownArrangementCount || window.previousValues.ownArrangementCount != data.ownArrangementCount) {
                        changedElements.push('ownArrangementCount');
                        window.previousValues.ownArrangementCount = data.ownArrangementCount;
                    }
                    
                    // Update all elements on the page with new counts and percentages
                    document.getElementById('totalCount').innerHTML = formatNumber(data.totalCount);
                    
                    // Update event ID if it exists
                    if (data.eventId && document.querySelector('.event-id')) {
                        document.querySelector('.event-id').innerHTML = data.eventId;
                    }
                    
                    // Update ashram residents with count and percentage
                    document.getElementById('ashramCount').innerHTML = formatNumber(data.ashramResidingCount) + 
                        '<span class="percentage">' + data.ashramPercentage + '%</span>';
                    document.querySelector('.progress-fill.ashram').style.width = data.ashramPercentage + '%';
                    
                    // Update temporary visitors with count and percentage
                    document.getElementById('tempCount').innerHTML = formatNumber(data.tempRegistrationCount) + 
                        '<span class="percentage">' + data.tempPercentage + '%</span>';
                    document.querySelector('.progress-fill.temp').style.width = data.tempPercentage + '%';
                    
                    // Update own arrangement count
                    document.getElementById('ownArrangementCount').innerHTML = formatNumber(data.ownArrangementCount) + 
                        '<span class="percentage">' + data.ownArrangementPercentage + '%</span>';
                    document.querySelector('.progress-fill.own').style.width = data.ownArrangementPercentage + '%';
                    
                    // Update ratio display
                    document.getElementById('ratioDisplay').innerHTML = data.ashramPercentage + '% / ' + data.tempPercentage + '%';
                    
                    // Update the last refreshed time - use shorter format for small screens
                    var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
                    var isMobile = width <= 480;
                    document.getElementById('lastRefreshed').innerHTML = data.refreshTime;
                    
                    // Update response time information - simpler for small screens
                    document.getElementById('responseTime').innerHTML = isMobile ? 
                        '(' + responseTime + 'ms)' : 
                        '(' + responseTime + 'ms)';
                    
                    // Adjust the refresh interval based on activity
                    if (changedElements.length > 0) {
                        // Data changed, so decrease interval (refresh more frequently)
                        window.refreshInterval = Math.max(window.minRefreshInterval, window.refreshInterval * 0.8);
                        window.consecutiveNoChanges = 0;
                        
                        // Update the last data change time
                        document.getElementById('lastChanged').innerHTML = data.refreshTime;
                        document.getElementById('lastChanged').classList.add('highlight-change');
                        // Remove highlight after 2 seconds
                        setTimeout(function() {
                            document.getElementById('lastChanged').classList.remove('highlight-change');
                        }, 2000);
                        
                        // Flash only the changed elements
                        flashUpdatedElements(changedElements);
                        
                        // Play a subtle notification sound for changes
                        playNotificationSound();
                    } else {
                        // No changes, gradually increase interval (refresh less frequently)
                        window.consecutiveNoChanges++;
                        if (window.consecutiveNoChanges >= window.maxConsecutiveNoChanges) {
                            window.refreshInterval = Math.min(window.maxRefreshInterval, window.refreshInterval * 1.2);
                        }
                    }
                    
                    // Schedule the next refresh
                    scheduleNextRefresh();
                    
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    // If there's an error, try again after a short delay
                    setTimeout(scheduleNextRefresh, 5000);
                }
            } else {
                console.error('Request returned status:', this.status);
                // Show error state
                document.getElementById('connectionStatus').innerHTML = 'Error';
                document.getElementById('connectionStatus').className = 'status-error';
                // If there's an error, try again after a short delay
                setTimeout(function() {
                    scheduleNextRefresh();
                    updateConnectionStatus();
                }, 5000);
            }
        };
        
        xhr.onerror = function() {
            console.error('Request error');
            // Show error state
            document.getElementById('connectionStatus').innerHTML = 'Connection Error';
            document.getElementById('connectionStatus').className = 'status-error';
            // If there's an error, try again after a short delay
            setTimeout(function() {
                scheduleNextRefresh();
                updateConnectionStatus();
            }, 5000);
        };
        
        xhr.send();
    }
    
    // Format numbers with commas
    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }
    
    // Visual feedback when counts update
    function flashUpdatedElements(elementIds) {
        elementIds.forEach(function(id) {
            var element = document.getElementById(id);
            if (element) {
                // Add a class that triggers a CSS animation
                element.classList.add('updated');
                
                // Remove the class after animation completes
                setTimeout(function() {
                    element.classList.remove('updated');
                }, 1000);
            }
        });
    }
    
    // Play a subtle notification sound
    function playNotificationSound() {
        // Create a simple audio context for a subtle beep
        try {
            // Check if AudioContext is available
            if (window.AudioContext || window.webkitAudioContext) {
                var audioContext = new (window.AudioContext || window.webkitAudioContext)();
                var oscillator = audioContext.createOscillator();
                var gainNode = audioContext.createGain();
                
                // Set properties for a gentle sound
                oscillator.type = 'sine';
                oscillator.frequency.value = 1000; // Hz
                gainNode.gain.value = 0.1; // Low volume
                
                // Connect and play for a short duration
                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);
                
                // Modern browsers require user interaction before playing audio
                if (audioContext.state === 'running') {
                    oscillator.start();
                    setTimeout(function() {
                        oscillator.stop();
                    }, 200); // Play for 200ms
                }
            }
        } catch (e) {
            console.log('Audio notification not supported');
        }
    }
    
    // Setup countdown timer to next refresh
    function setupRefreshTimer(seconds) {
        var timerElement = document.getElementById('refreshTimer');
        var secondsLeft = Math.round(seconds);
        
        // Clear any existing timer
        if (window.refreshTimerInterval) {
            clearInterval(window.refreshTimerInterval);
        }
        
        // Update immediately and then every second
        updateTimerDisplay();
        
        window.refreshTimerInterval = setInterval(function() {
            secondsLeft--;
            updateTimerDisplay();
            
            if (secondsLeft <= 0) {
                clearInterval(window.refreshTimerInterval);
            }
        }, 1000);
        
        function updateTimerDisplay() {
            if (timerElement) {
                timerElement.textContent = secondsLeft;
            }
        }
    }
    
    // Force an immediate refresh when the user clicks the refresh button
    function manualRefresh() {
        // Reset the interval to the minimum value
        window.refreshInterval = window.minRefreshInterval;
        // Clear any existing timers
        if (window.refreshTimer) {
            clearTimeout(window.refreshTimer);
        }
        
        // Check if we need to resume first
        if (window.refreshPaused) {
            // Just do a one-time refresh without resuming auto-refresh
            document.getElementById('connectionStatus').innerHTML = 'Manual Refresh';
            document.getElementById('connectionStatus').className = 'status-active';
            // Reset status after 2 seconds
            setTimeout(function() {
                document.getElementById('connectionStatus').innerHTML = 'Paused';
                document.getElementById('connectionStatus').className = 'status-paused';
            }, 2000);
        }
        
        // Fetch the data immediately
        fetchLatestCounts();
        
        // Don't reload the page
        return false;
    }
    
    // Fix for mobile browsers where 100vh doesn't account for address bar
    function setViewportHeight() {
        // Set a CSS variable with the actual viewport height
        let vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);
        
        // Update on resize
        window.addEventListener('resize', function() {
            let vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty('--vh', `${vh}px`);
        });
    }
    
    // Update UI elements based on current screen size
    function updateUIForScreenSize() {
        // Get current screen dimensions
        var width = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        var height = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
        
        // Calculate screen classifications
        var isMobile = width <= 480;
        var isVerySmall = width <= 320 || height <= 500;
        var isSmallHeight = height <= 600;
        var isLandscape = width > height;
        var isUltraCompact = (width <= 360 && height <= 640) || height <= 450;
        
        // Apply compact classes to body for global styling based on available space
        if (isUltraCompact) {
            document.body.classList.add('ultra-compact-view');
        } else {
            document.body.classList.remove('ultra-compact-view');
        }
        
        // Update refresh info text for different screen sizes
        var refreshText = document.querySelector('.refresh-info p:first-child');
        var timerValue = document.getElementById('refreshTimer') ? 
                         document.getElementById('refreshTimer').textContent : '30';
        var pauseIcon = window.refreshPaused ? '▶️' : '⏸️';
        
        if (refreshText) {
            if (isMobile || isSmallHeight) {
                refreshText.innerHTML = 'Refresh: <span id="refreshTimer" class="timer">' + timerValue + 
                                      '</span>s <button id="pauseRefresh" class="btn-icon" onclick="togglePauseRefresh()">' + 
                                      pauseIcon + '</button>';
            } else {
                refreshText.innerHTML = 'Auto-refreshing in <span id="refreshTimer" class="timer">' + 
                                      timerValue + '</span> seconds <button id="pauseRefresh" class="btn-icon" onclick="togglePauseRefresh()">' + 
                                      pauseIcon + '</button>';
            }
        }
        
        // Handle extra-small screens by hiding certain elements
        var lastChangedInfo = document.querySelector('.refresh-info p:nth-child(3)');
        if (lastChangedInfo) {
            lastChangedInfo.style.display = isVerySmall ? 'none' : '';
        }
        
        // For landscape on very small heights, optimize the layout
        var refreshInfoContainer = document.querySelector('.refresh-info');
        if (refreshInfoContainer) {
            if (isLandscape && height <= 500) {
                refreshInfoContainer.classList.add('landscape-mode');
            } else {
                refreshInfoContainer.classList.remove('landscape-mode');
            }
        }
        
        // Apply more extreme changes for ultra-compact mode
        if (isUltraCompact) {
            // Hide status indicator in ultra-compact mode
            var statusIndicator = document.querySelector('.status-indicator');
            if (statusIndicator) {
                statusIndicator.style.display = 'none';
            }
        } else {
            var statusIndicator = document.querySelector('.status-indicator');
            if (statusIndicator) {
                statusIndicator.style.display = '';
            }
        }
    }
</script>

</body>
</html>
