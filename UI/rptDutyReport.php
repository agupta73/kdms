<?php
$config_data = include("../site_config.php");
?>
<html>
    <head>
        <title> Seva Report </title>
        

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                printdiv();
            }, false);
            
            function printdiv() {
            //alert(document.getElementById("requestType").value());
//            document.getElementById("printForm").submit();
            
//            $.ajax({
//                    url:'/KDMS/Logic/requestManager.php',
//                    type:'POST',
//                    data:{'devotee_key': "<?php echo $_GET['key']; ?>", 'requestType': "removeFromPrintQueue"},
//                    async: false,
//                    success:function(response){
//                        alert("getting here..");
//                          var r = JSON.parse(response);
//                          
//                          if(r['flag'] == true){
//                              alert("Card removed from the printing queue!");
//                              window.location.assign("./devoteeSearchResult.php?mode=SET&key=CTP");
//                          }
//                          else{
//                              alert(r['message']);
//                              updateSuccess=false;
//                          }   
//                      }
//                  });   
                 
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
    <body>

       <div id="printpage">
            <div id="report">
                            <div>
                                <u><label style="text-align:center; width:700px; font-weight:bold; font-size:20px;">Mal Pua Sevak Report</label></u>
                                <br>
                                <br>
                                <table width="700px">
                                    <tr>
                                        <td>
                                            <label style="text-align:center; width:220px; font-weight:bold; font-size:20px;">Sr#</label>
                                        </td>
                                        <td>
                                            <label style="text-align:center; width:220px; font-weight:bold; font-size:20px;">Name</label>
                                        </td>
                                        <td>
                                            <label style="text-align:center; width:220px; font-weight:bold; font-size:20px;">Station</label>
                                        </td>
                                        <td>
                                            <label style="text-align:center; width:220px; font-weight:bold; font-size:20px;">Cell Phone</label>
                                        </td>
                                        <td>
                                            <label style="text-align:center; width:220px; font-weight:bold; font-size:20px;">Photo</label>
                                        </td>
                                    </tr>
            <?php
            include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsDevoteeSearch.php");
            if (!empty($_GET['key'])) {
                $devoteeSearch = new clsDevoteeSearch($_GET);
                $response = $devoteeSearch->getDevoteeRecords();
                unset($devoteeSearch);
            }
            
            // ["devotee_key"]=> string(9) "P19051428" ["Devotee_Name"]=> string(19) "KUNDAN+SINGH+ GAIRA" ["devotee_station"]=> string(8) "Nainital" ["devotee_cell_phone_number"]=> string(0) "" ["Devotee_ID_Image"], "Devotee_Photo"]
                $recordCount = 0;
            
            if (!empty($response)) {
                foreach ($response as $devoteeRecord) {
                    $devotee_key = "--Unavailable--";
                    $devotee_first_name = "--Unavailable--";
                    $devotee_last_name = "--Unavailable--";
                    $devotee_station = "--Unavailable--";
                    $devotee_cell_phone_number = "--Unavailable--";
//                    $accommodation_name = "--Unavailable--";
                    $devotee_photo = "";
                    
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

//                    if (!empty($devoteeRecord['accomodation_name'])) {
//                        $accommodation_name = urldecode($devoteeRecord['accomodation_name']);
//                    }

                    if (!empty($devoteeRecord['Devotee_Photo'])) {
                        $devotee_photo = $devoteeRecord['Devotee_Photo'];
                    }
                   
                    if ($devotee_key != "--Unavailable--") {
                        $recordCount = $recordCount + 1;
                        ?>
                      
                                    <tr>
                                        <td>
                                            <label style="text-align:center; "><?php echo $recordCount; ?></label>
                                        </td>
                                        <td>
                                            <label style="text-align:center; "><?php echo $devotee_name; ?></label>
                                        </td>
                                        <td>
                                            <label style="text-align:center; "><?php echo $devotee_station; ?></label>
                                        </td>
                                        
                                        <td>
                                            <label style="text-align:center; "><?php echo $devotee_cell_phone_number; ?></label>
                                        </td>
                                        
                                        <td>
                                            <div>
                                                <!--<img src="../assets/img/faces/doc.png" alt="devotee ID" height="80px" width="80px">-->
                                              <?php
                                                if ($devotee_photo == "") {
                                                  print_r('<img src="../assets/img/faces/devotee.ico" alt="Devotee Image" height="80px" width="80px"></img>');
                                              } else {
                                                  print_r('<img src="data:image/jpeg;base64,' . $devotee_photo . '" alt="devotee image" height="80px" width="80px"></img>');
                                              }
                                              ?>
                                            </div>
                                        </td>
                                    </tr>
                                
            <?php } } } ?>
      </table>
                                <br>
                                
                            </div>
                        </div> 
                        <br>
                        <br> </div>
        <br>
        <a href = "#" onclick="printdiv()"><button >Print </button> </a>
        <!--<form id="printForm" action="<?=$config_data['webroot'];?>Logic/requestManager.php" method="POST">-->
            <!--<input type="hidden" id="requestType" value="removeFromPrintQueue">-->
<!--            <input type="hidden" id="devotee_key" value=" <?php print_r($_GET); ?>">-->
            <!--<input type="hidden" id="devotee_key" value="Devotee_Key">-->
        <!--</form>-->
        
    </body>
</html>