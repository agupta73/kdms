<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/web_session.php';

$eventId = $config_data['event_id']; // This variable is set but is not always used elsewhere.
$debug = false;

$devotees_to_print = [];

if (!empty($_GET['key'])) {
    include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsDevoteeSearch.php");
    // Assuming clsDevoteeSearch handles sanitization of $_GET['key'] internally
    $devoteeSearch = new clsDevoteeSearch($_GET);
    $response = $devoteeSearch->getDevoteeRecords($eventId);
    unset($devoteeSearch);

    if (!empty($response)) {
        foreach ($response as $devoteeRecord) {
            $get_val = function ($key, $default = "N/A") use ($devoteeRecord) {
                return !empty($devoteeRecord[$key]) ? urldecode($devoteeRecord[$key]) : $default;
            };

            $devotee_key = $get_val('devotee_key');

            // Only process if a devotee key exists
            if ($devotee_key !== "N/A") {
                $devotees_to_print[] = [
                    'key'                   => $devotee_key,
                    'first_name'            => $get_val('devotee_first_name'),
                    'last_name'             => $get_val('devotee_last_name'),
                    'station'               => $get_val('devotee_station'),
                    'status'                => $get_val('devotee_status'),
                    'devotee_type'          => $get_val('Devotee_Type'),
                    'cell_phone_number'     => $get_val('devotee_cell_phone_number'),
                    'devotee_referral'      => $get_val('Devotee_Referral'),
                    'accommodation_name'    => $get_val('accomodation_name'), // Check spelling: accomodation vs accommodation
                    'photo'                 => !empty($devoteeRecord['Devotee_Photo']) ? $devoteeRecord['Devotee_Photo'] : "",
                ];
            }
        }
    }
}

if ($debug && isset($response)) { // Check if $response is set before var_dump
    var_dump($response);
    // To debug processed data: var_dump($devotees_to_print);
    die;
}
?>
<html>
<head>
    <title> Card Print </title>
    <style>
        body {
            font-family: sans-serif;
        }
        .card-item {
            background-color: #fff;
            border-radius: 3px;
            border-style: double;
            height: 190px; /* Consider using min-height if content can vary */
            width: 315px;
            margin-bottom: 7px;
            page-break-inside: avoid;
        }
        .card-item img.banner {
            display: block; /* Prevents small gap under image */
        }
        .card-label {
            text-align: left;
            width: 70px;
            float: left;
            clear: left;
            margin-right: 5px; /* Added some space */
            font-weight: bold; /* Made labels bold for clarity */
            font-size: 13px;
            padding-top: 2px;
        }
        .card-data {
            font-size: 13px;
            vertical-align: middle;
            padding-top: 2px; /* Align with label */
            display: block; /* Make it take up rest of the space */
            margin-left: 75px; /* Space for the floated label */
            word-wrap: break-word; /* Prevent overflow */
        }
        .devotee-name {
            display: block;
            text-align: center;
            width: 100%;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 3px;
        }
        .devotee-status {
            display: block;
            text-align: center;
            width: 100%;
            font-weight: bold;
            font-size: 26px;
            margin-bottom: 3px;
        }
        .devotee-status.blocked { color: red; }
        .card-footer {
            font-size: 9px;
            text-align: center;
            width: 100%;
            display: block;
            margin-top: 5px;
        }
        .details-row > div {
            padding: 2px 0; /* Consistent padding for detail items */
        }
        /* Print-specific styles */
        @media print {
            body {
                margin: 0;
                font-family: sans-serif; /* Ensure font consistency */
            }
            .card-item {
                margin-bottom: 7mm; /* Space between cards on a printed page */
                page-break-after: auto; /* Let browser decide, or 'always' if one card per page */
            }
            .no-print {
                display: none;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Automatically trigger print dialog if there's content to print
            if (document.getElementById("printpage").children.length > 0 &&
                !document.getElementById("printpage").textContent.includes("No devotee records found")) {
                printDivContent();
            }
        }, false);

        function printDivContent() {
            var printContent = document.getElementById("printpage").innerHTML;
            var pageStyles = "";
            // Collect all style rules
            for (let i = 0; i < document.styleSheets.length; i++) {
                try {
                    var rules = document.styleSheets[i].cssRules || document.styleSheets[i].rules;
                    for (let j = 0; j < rules.length; j++) {
                        pageStyles += rules[j].cssText + "\n";
                    }
                } catch (e) {
                    // Catch potential cross-origin stylesheet errors
                    console.warn("Could not read styles from stylesheet: " + document.styleSheets[i].href, e);
                }
            }

            var popupWin = window.open('', '_blank', 'width=800,height=700,scrollbars=yes,resizable=yes');
            popupWin.document.open();
            popupWin.document.write('<html><head><title>Print Card</title>');
            popupWin.document.write('<style type="text/css">' + pageStyles + '</style>');
            popupWin.document.write('</head><body onload="window.print(); window.close();">');
            popupWin.document.write(printContent);
            popupWin.document.write('</body></html>');
            popupWin.document.close();

            // Optional: Close the original window if this page is only a launcher
            // window.close();
            return false;
        }
    </script>
</head>
<body>

<div id="printpage">
    <?php if (!empty($devotees_to_print)): ?>
        <?php foreach ($devotees_to_print as $index => $devotee): ?>
        <div class="card-item" id="card-<?php echo $index; ?>">
            <img src="/kdms/assets/img/banner.png" height="35px" width="314px" alt="Banner" class="banner">
            <div style="padding: 5px;">
                <span class="devotee-name">
                    <?php echo htmlspecialchars($devotee['first_name'] . ' ' . $devotee['last_name']); ?>
                </span>
                <!-- This is for prasad vitran -->
                <?php if ($devotee['status'] == "D" && $devotee['devotee_type'] == "T" && stripos($devotee['devotee_referral'], 'devesh') === 0) : // Day Visitor Card ?>
                    <span class="devotee-status" style="font-size: 14px;">
                        <?php echo '( ' . $devotee['key'] . ' )'; ?>
                    </span>
                    <span class="devotee-status" style="margin:0;">Prasad Vitran</span>
                    <hr style="width: 80%; margin: 5px 0; margin: 0 auto;">

                        <span style="display: block; text-align: center; margin-bottom: 2px; margin-top: 10px; font-size: 14px; font-weight: bold;">Referred by: </span>
                        <div class="details-row" style="margin-top: 0; margin-bottom:10px;">
                            <div class="details-row" style="text-align: center;">
                                <span class="card-data"
                                      style="font-size: 16px; font-weight: bold; display: inline-block; margin-left: 0;">
                                    <?php echo 'Devesh Agarwal Ji'; ?>
                                </span>
                            </div>
                        </div>
                        <span class="card-footer">
                            This card is not valid after 15th June <strong>2025</strong>
                        </span>
                <?php elseif ($devotee['status'] == "D" && $devotee['devotee_type'] == "T"): // Day Visitor Card ?>
                    <?php if (!empty($devotee['accommodation_name']) && $devotee['accommodation_name'] !== "N/A" && $devotee['accommodation_name'] !== "Own Arrangement (Outside)" && $devotee['accommodation_name'] !== "Local"): ?>
                    <span class="devotee-status" style="margin: 20px 0; margin: 5px 0; font-size: 24px;">Temporary Accommodation for 2025</span>
                    <?php else: ?>
                    <span class="devotee-status" style="margin: 20px 0; margin: 5px 0;">Temporary</span>
                    <?php endif; ?>
                    <hr style="width: 80%; margin: 5px 0; margin: 0 auto;">
                    <?php if (!empty($devotee['accommodation_name']) && $devotee['accommodation_name'] !== "N/A" && $devotee['accommodation_name'] !== "Own Arrangement (Outside)" && $devotee['accommodation_name'] !== "Local"): ?>
                        <span style="display: block; text-align: center; margin-bottom: 2px; margin-top: 10px; font-size: 14px; font-weight: bold;">Staying at: </span>
                        <div class="details-row" style="margin-top: 0; margin-bottom:10px;">
                            <div class="details-row" style="text-align: center;">
                                <span class="card-data"
                                      style="font-size: 16px; font-weight: bold; display: inline-block; margin-left: 0;">
                                    <?php echo htmlspecialchars($devotee['accommodation_name']); ?>
                                </span>
                            </div>
                        </div>
                    <?php elseif (!empty($devotee['devotee_referral']) && $devotee['devotee_referral'] !== "N/A"): ?>
                        <span style="display: block; text-align: center; margin-bottom: 2px; margin-top: 10px; font-size: 14px; font-weight: bold;">Referred by: </span>
                        <div class="details-row" style="margin-top: 0; margin-bottom:10px;">
                            <div class="details-row" style="text-align: center;">
                                <span class="card-data"
                                      style="font-size: 16px; font-weight: bold; display: inline-block; margin-left: 0;">
                                    <?php echo htmlspecialchars($devotee['devotee_referral']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php // Photo and other details are intentionally omitted for Day Visitor ?>

                <?php else: // Standard Card (including Blocked, etc.) ?>
                    <table style="width:100%;">
                        <tr>
                            <td style="width:70%; vertical-align:top;">
                                <div class="details-row">
                                    <span class="card-label">Reg No.:</span>
                                    <span class="card-data"><?php echo htmlspecialchars($devotee['key']); ?></span>
                                </div>
                                <div class="details-row">
                                    <span class="card-label">Station:</span>
                                    <span class="card-data"><?php echo htmlspecialchars($devotee['station']); ?></span>
                                </div>
                                <div class="details-row">
                                    <span class="card-label">Staying at:</span>
                                    <span class="card-data"><?php echo htmlspecialchars($devotee['accommodation_name']); ?></span>
                                </div>
                                <?php if (!empty($devotee['devotee_referral']) && $devotee['devotee_referral'] !== "N/A"): ?>
                                <div class="details-row">
                                    <span class="card-label">Reference:</span>
                                    <span class="card-data"><?php echo htmlspecialchars($devotee['devotee_referral']); ?></span>
                                </div>
                                <?php else: ?>
                                <div class="details-row">
                                    <span class="card-label">Mobile No:</span>
                                    <span class="card-data"><?php echo htmlspecialchars($devotee['cell_phone_number']); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="details-row">
                                    <span class="card-label">Date:</span>
                                    <span class="card-data"><?php echo date('jS F Y'); ?></span>
                                </div>
                            </td>
                            <td style="width:30%; text-align:center; vertical-align:top;">
                                <?php if (empty($devotee['photo'])): ?>
                                    <img src="../assets/img/faces/devotee.ico" alt="Devotee Image" height="80px" width="80px">
                                <?php else: ?>
                                    <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($devotee['photo']); ?>" alt="Devotee Image" height="80px" width="80px">
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                    <?php if ($devotee['status'] == "B"): ?>
                    <span class="devotee-status blocked">BLOCKED</span>
                    <?php endif; ?>
                    <span class="card-footer">
                        This card is not valid after <?php echo isset($_SESSION['eventDesc']) ? htmlspecialchars($_SESSION['eventDesc']) : 'EVENT_END_DATE'; ?>
                    </span>
                <?php endif; // End of conditional card content ?>

            </div>
        </div>
        <?php if (count($devotees_to_print) > 1 && ($index < count($devotees_to_print) - 1)): ?>
            <div style="page-break-after: always;" class="no-print"></div> <!-- Ensures page break for printing multiple cards -->
        <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No devotee records found to print.</p>
    <?php endif; ?>
</div>

<div class="no-print" style="text-align:left; margin-top:20px;">
    <button type="button" onclick="printDivContent();">Print Cards</button>
    <!-- The form below was part of the original code. Its purpose is unclear without the AJAX context. -->
    <!-- If it's related to 'removeFromPrintQueue', that logic needs to be implemented, possibly via AJAX after printing. -->
    <form id="printForm" action="<?php echo htmlspecialchars($config_data['webroot']); ?>Logic/requestManager.php" method="POST" style="display:none;">
        <input type="hidden" name="requestType" value="removeFromPrintQueue_placeholder">
        <input type="hidden" name="devotee_key_data" value="<?php echo htmlspecialchars($_GET['key'] ?? ''); ?>">
    </form>
</div>

</body>
</html>
