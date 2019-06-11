<?php
$config_data = include("../site_config.php");
?>
<html>
    <head>
        <title> Card Print </title>
        <style>
            #card{
                background-color: aliceblue;
                border-radius: 3px;
                border-style:double;
                height: 190px;
                width: 315px;   
            }
            *{
                font-family: sans-serif;
            }

            label{
                text-align: left;
                width: 70px;
                /*display: block;*/
                float: left;
                clear: left;
                margin-right: 0px;
                font-weight:regular;
                font-size:12px
            }
        </style>

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
                var header = '<header><div align="center"><h3 style="color:#EB5005"> Card Print </h3></div><br></header><hr><br>'

                var popupWin = window.open('', 'blank', 'width=800px,height=700px');
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
            <?php
            include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsDevoteeSearch.php");
            if (!empty($_GET['key'])) {
                $devoteeSearch = new clsDevoteeSearch($_GET);
                $response = $devoteeSearch->getDevoteeRecords();
                unset($devoteeSearch);
            }
            //var_dump($response);
            $recordCount = 0;
            if (!empty($response)) {
                foreach ($response as $devoteeRecord) {
                    $devotee_key = "--Unavailable--";
                    $devotee_first_name = "--Unavailable--";
                    $devotee_last_name = "--Unavailable--";
                    $devotee_station = "--Unavailable--";
                    $devotee_cell_phone_number = "--Unavailable--";
                    $accommodation_name = "--Unavailable--";
                    $devotee_photo = "";
                    
                    if (!empty($devoteeRecord['devotee_key'])) {
                        $devotee_key = urldecode($devoteeRecord['devotee_key']);
                    }

                    if (!empty($devoteeRecord['devotee_first_name'])) {
                        $devotee_first_name = urldecode($devoteeRecord['devotee_first_name']);
                    }

                    if (!empty($devoteeRecord['devotee_last_name'])) {
                        $devotee_last_name = urldecode($devoteeRecord['devotee_last_name']);
                    }

                    if (!empty($devoteeRecord['devotee_station'])) {
                        $devotee_station = urldecode($devoteeRecord['devotee_station']);
                    }

                    if (!empty($devoteeRecord['devotee_cell_phone_number'])) {
                        $devotee_cell_phone_number = urldecode($devoteeRecord['devotee_cell_phone_number']);
                    }

                    if (!empty($devoteeRecord['accomodation_name'])) {
                        $accommodation_name = urldecode($devoteeRecord['accomodation_name']);
                    }

                    if (!empty($devoteeRecord['Devotee_Photo'])) {
                        $devotee_photo = $devoteeRecord['Devotee_Photo'];
                    }
                   
                    if ($devotee_key != "--Unavailable--") {
                        $recordCount = $recordCount + 1;
                        ?>
                       <div id="card">
                            <span> <img src="/kdms/assets/img/banner.png"  height="35px" width="314px"></span>
                            
                            <div>
                                <table width="309px">
                                   
                                    <tr>
                                        <td>  
                                            <table > 
                                                 <tr>
                                        <td >
                                            <label style="text-align:center; width:220px; font-weight:bold; font-size:20px;"><?php echo $devotee_first_name . ' ' . $devotee_last_name; ?></label>
                                        </td>
                                            </tr>
                                                <tr>
                                                    <td>
                                                        <span>
                                                            <label style="font-weight:bold; padding-top: 2px">Reg No.:</label>                                      
                                                            <input type="text" style="border:none; background-color: transparent; width: 130px; vertical-align: middle" id="devotee_key" value=" <?php echo $devotee_key; ?> ">
                                                        </span>
                                                    </td>


                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span>
                                                            <label  style="font-weight:bold; padding-top: 2px">Station:</label>                                        
                                                            <input type="text" style="border:none; background-color: transparent; width: 130px; vertical-align: middle" name="devotee_name" id="devotee_name" value=" <?php echo $devotee_station; ?> ">
                                                        </span> 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span>
                                                            <label style="font-weight:bold; padding-top: 2px">Staying At:</label>                                        
                                                            <input type="text" style="border:none; width: 130px; background-color: transparent;   vertical-align: middle" id="accommodation_name" value=" <?php echo $accommodation_name; ?> ">
                                                        </span> 
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <span>
                                                            <label style="font-weight:bold; padding-top: 2px"> Mobile No:</label>                                        
                                                            <input type="text" style="border:none; background-color: transparent; width: 130px; vertical-align: middle" id="devotee_cell_phone_number" value=" <?php echo $devotee_cell_phone_number; ?> ">
                                                        </span> 
                                                    </td>
                                                </tr>
 <tr>
                                                    <td>
                                                        <span>
                                                            <label style="font-weight:bold; padding-top: 2px"> Date:</label>                                        
                                                            <input type="text" style="border:none; background-color: transparent; width: 130px; vertical-align: middle" id="devotee_cell_phone_number" value=" <?php echo date('jS F Y'); ?> ">
                                                        </span> 
                                                    </td>
                                                </tr>
                                            </table>
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
                                </table>
                                <br>
                                <label style="font-size:9px; text-align:center; width:300px;">This card is not valid after 15 June 2019</label>
                            </div>
                        </div> 
                        <br>
                        <br>
            <?php } } } ?>
       </div>
        <br>
        <a href = "#" onclick="printdiv()"><button >Print </button> </a>
        <form id="printForm" action="<?=$config_data['webroot'];?>Logic/requestManager.php" method="POST">
            <input type="hidden" id="requestType" value="removeFromPrintQueue">
<!--            <input type="hidden" id="devotee_key" value=" <?php print_r($_GET); ?>">-->
            <input type="hidden" id="devotee_key" value="Devotee_Key">
        </form>
        
    </body>
</html>