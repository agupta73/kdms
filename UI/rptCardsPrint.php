<?php
//    include_once("conn/conn.php");
//
//    $id = $_GET['id_no'];
//    
//    //Just seprate query for images and data
//
//    $query="SELECT d.* FROM devotee d WHERE d.devotee_key = '".$id."'"; 
//        
//	$result = $conn -> query($query);
//	$row = $result -> fetch_array(MYSQLI_ASSOC);
//
//    $query1 = "SELECT * from output_images where kdmm_id = '".$id."'";
//	$result1 = $conn -> query($query1);
//	//var_dump($result1); die;
//	$row1 = $result1 -> fetch_array(MYSQLI_ASSOC);
//
//       
//    $sql = "select dl.*, l.* from devotee_location dl, location l where dl.loc_id = l.L_id and dl.kdmm_id = '".$id."'";
//    $sql_res = $conn -> query($sql);
//    $row_loc = $sql_res -> fetch_array();
//    $x = "";
//    if($row_loc['L_name'] != "")
//        $x = $row_loc['L_name'];
//    else
//        $x = "Own";
   
$devotee_key = "1234567890123456789012345678901234567890";
$devotee_first_name = "Anil";
$devotee_last_name = "Gupta";
$devotee_id_type = "Passport";
$devotee_station = "California";
$accommodation_name = "Gargachal";
$devotee_cell_phone_number = "+1 415-622-7879";
$devotee_photo = "";

?>

<!DOCTYPE html>

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
                text-align: right;
                width: 80px;
                /*display: block;*/
                float: left;
                clear: left;
                margin-right: 0px;
                font-weight:regular;
                font-size:12px
              }
        </style>
    </head>

    <body>
      <div id="printpage">
          <?php 
          $i=0;
          for($i = 0; $i < 3; $i++)
          {
          ?>
                <div id="card">
                    <span> <img src="/kdms/assets/img/banner.png"  height="35px" width="314px"></span>
                    <label style="text-align:center; width:300px; font-weight:bold; font-size:20px;"><?php echo $devotee_first_name . ' ' . $devotee_last_name; ?></label>
                    <div>
                    <table width="309px">
<!--                        <tr>
                            <td>
                        <label style="text-align:center; width:200px; font-weight:bold; font-size:16px;">Anil Gupta</label>
                            </td>
                        </tr>-->
                        <tr>
                            <td>  
                                <table >                                   
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
                                                <input type="text" style="border:none; background-color: transparent; width: 130px;  vertical-align: middle" id="accommodation_name" value=" <?php echo $accommodation_name; ?> ">
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
                                    
                                    </table>
                                </td>
                                <td>
                                    <div>

                                            <img src="../assets/img/faces/doc.png" alt="devotee ID" height="80px" width="80px"></img>

                                    </div>
                                </td>
                            </tr>
                    </table>
                        <label style="font-size:10px; text-align:center; width:300px;">This card is not valid after 15 June 2019</label>
                     </div>
                </div> 
          <br>
          <br>
          <?php 
            }
          ?>
      </div>
        <br>
        <a href = "#" onclick="printdiv()"><button >Print </button> </a>

        <script>
            function printdiv() {

                var newstr = document.getElementById("printpage").innerHTML;
                var header = '<header><div align="center"><h3 style="color:#EB5005"> Your Header </h3></div><br></header><hr><br>'

                // var footer = "Authorized Devotee id Generated By KDMMV2.0"

                var popupWin = window.open('', 'blank', 'width=1000,height=700');
                popupWin.document.open();
                popupWin.document.write('<html><body onload="window.print()">' + newstr + '</html></br>');
                popupWin.document.close();
                return false;

            }

        </script>  
    </body>
</html>
