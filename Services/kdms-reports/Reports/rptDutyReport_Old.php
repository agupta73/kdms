<?php
$config_data = include("../site_config.php");
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once("../sessionCheck.php");
$eventId = $_SESSION['eventId'];
$debug = false;
?>

<html>

<head>
    <title> Seva Report </title>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // printdiv();
        }, false);

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
</head>
<style>
    table,
    th,
    td {
        border: 1px solid black;
        border-collapse: collapse;
    }

    th {
        background-color: #96D4D4;
    }
</style>

<body>

<?php
include_once("../Logic/clsServicesManager.php");

$sevaDesc = "";
$photoRequired = "";
$devotee_seva_old = "old";

if (!empty($_GET['photo_required'])) {
    $photoRequired = $_GET['photo_required'];
}
//if (!empty($_GET['key'])) { //because empty key means all sevas
$devoteeSearch = new clsServicesManager($_GET); //key => request => Seva ID
$devoteeSearch->setOptionType("ADS"); //mode => OptionType => asigned devotee for seva
$devoteeSearch->setEventId($eventId);
$response = $devoteeSearch->getRecords();
unset($devoteeSearch);

?>
    <div id="printpage">
        <div id="report">
            <div>
                <?php
                $recordCount = 0;

                if (!empty($response)) {
                    foreach ($response as $devoteeRecord) { 
                        if($debug){var_dump($devoteeRecord['Seva_description']); var_dump($devoteeRecord['Devotee_Name']); echo "<br><br>";}
                        $devotee_key = "--Unavailable--";
                        $devotee_first_name = "--Unavailable--";
                        $devotee_last_name = "--Unavailable--";
                        $devotee_station = "Unknown";
                        $devotee_cell_phone_number = "Unavailable";                        
                        $devotee_photo = "";
                        $devotee_seva = "";

                        if (!empty($devoteeRecord['devotee_key'])) {
                            $devotee_key = urldecode($devoteeRecord['devotee_key']);
                        }

                        if (!empty($devoteeRecord['Devotee_Name'])) {
                            $devotee_name = urldecode($devoteeRecord['Devotee_Name']);
                        }

                        if (!empty($devoteeRecord['devotee_station'])) {
                            $devotee_station = urldecode($devoteeRecord['devotee_station']);
                        }

                        if (!empty($devoteeRecord['devotee_cell_phone_number'])) {
                            $devotee_cell_phone_number = urldecode($devoteeRecord['devotee_cell_phone_number']);
                        }

                        if ($photoRequired == "Y") {
                            if (!empty($devoteeRecord['Devotee_Photo'])) {
                                $devotee_photo = $devoteeRecord['Devotee_Photo'];
                            }
                        }

                        if (!empty($devoteeRecord['Seva_description'])) {
                            $devotee_seva = urldecode($devoteeRecord['Seva_description']);
                        }
                        if($debug) {echo "Devotee old seva : ", $devotee_seva_old, " || Devotee new seva : ", $devotee_seva, "<br><br>"; }

                            if ($devotee_seva_old != $devotee_seva) {
                                $devotee_seva_old = $devotee_seva;
                                // Last record is somehow going into next section - meaning, the next section is printing before the count
                                
                                echo "<br><u><label style='text-align:center; width:700px; font-weight:bold; font-size:20px;'>" ,  $devotee_seva , " Seva Report</label></u><br><br>";
                               //echo "<br><u><label style='text-align:center; width:700px; font-weight:bold; font-size:20px;'>" ,   " Seva Report</label></u><br><br>";
                            }  
                                echo "<table width='700px'>";
                                print_r("<tr>");
                                if($debug) {echo "Devotee old seva : ", $devotee_seva_old, " || Devotee new seva : ", $devotee_seva, "<br><br>"; }
                                print_r("<th>");
                                print_r("<label style='text-align:center; width:220px; font-weight:bold; font-size:20px;'>" . "SN" . "</label>");
                                print_r("</th>");
                                print_r("<th><label style='text-align:center; width:220px; font-weight:bold; font-size:20px;'>Name</label></th>");
                                print_r("<th><label style='text-align:center; width:220px; font-weight:bold; font-size:20px;'>Station</label></th>");
                                print_r("<th><label style='text-align:center; width:220px; font-weight:bold; font-size:20px;'>Cell Phone</label></th>");
                                print_r("<th><label style='text-align:center; width:220px; font-weight:bold; font-size:20px;'>Assigned Seva</label></th>");
                                if ($photoRequired == "Y") { 
                                    print_r("<th><label style='text-align:center; width:220px; font-weight:bold; font-size:20px;'>Photo</label></th>");                                    
                                } 
                                print_r("</tr>");
                                
                            //}
                        
                        if ($devotee_key != "--Unavailable--") {
                            $recordCount = $recordCount + 1;
                        }
                        
                                            
                        print_r("<tr><td><label style='text-align:center; '>");
                        echo $recordCount;
                        print_r("</label></td>");
                        print_r("<td><label style='text-align:center; '>");
                        echo $devotee_name; 
                        print_r("</label></td>");
                        print_r("<td><label style='text-align:center; '>");
                        echo $devotee_station; 
                        print_r("</label></td>");
                        print_r("<td><label style='text-align:center; '>");
                        echo $devotee_cell_phone_number;
                        print_r("</label></td>");
                        print_r("<td><label style='text-align:center; '>");
                        echo $devotee_seva; 
                        print_r("</label></td>");
                        if ($photoRequired == "Y") { 
                            print_r("<td><div>");
                            if ($devotee_photo == "") {
                                print_r('<img src="../assets/img/faces/devotee.ico" alt="Devotee Image" height="80px" width="80px"></img>');
                            } else {
                                print_r('<img src="data:image/jpeg;base64,' . $devotee_photo . '" alt="devotee image" height="80px" width="80px"></img>');
                            }
                            print_r("</div></td>");
                        }
                    
                        print_r("</tr>");
                        

                    }
                    print_r("</table>");
                }
                ?>
                
                <br>

            </div>
        </div>
        <br>
        <br>
    </div>
    <br>
    <a href="#" onclick="printdiv()"><button>Print </button> </a>
    <!--<form id="printForm" action="<?= $config_data['webroot']; ?>Logic/requestManager.php" method="POST">-->
    <!--<input type="hidden" id="requestType" value="removeFromPrintQueue">-->
    <!--            <input type="hidden" id="devotee_key" value=" <?php print_r($_GET); ?>">-->
    <!--<input type="hidden" id="devotee_key" value="Devotee_Key">-->
    <!--</form>-->

</body>

</html> 