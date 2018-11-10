<!DOCTYPE html>
<html lang="en">
<head>
  <title> KDMS (Available Devotee Records) </title>
  <?php 
    include_once("header.php"); 
    include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsDevoteeSearch.php");
  ?>
</head>

<body class="">
  <div class="wrapper ">
    <?php include_once("nav.php"); ?>

       <div class="main-panel">
      <!-- Navbar -->
       <?php
        include_once("navBottom.php");
        
        $DisplayMode = "";
        $gridTitle = "";
        if(!empty($_GET['DisplayMode'])){
            $DisplayMode = $_GET['DisplayMode'];
        
            switch ($DisplayMode){
                case "PWD":
                    $gridTitle = "Incomplete Devotee Records with photo or ID";
                break;
            
                case "DWP":
                    $gridTitle = "Devotee Records without photo or ID";
                break;
            
                case "CTP":
                    $gridTitle = "Devotee Cards to be Printed";
                break;
            
                default :
                    $gridTitle = "Available Devotee Records";
                break; 
            }
            
            $devoteeSearch = new clsDevoteeSearch($_GET);    
            $response = $devoteeSearch->getDevoteeRecords();
            //var_dump($response);die;
            unset($devoteeSearch);
        }
       ?>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-header card-header-primary">
                <h4 class="card-title" id="pageHeader">
                <?php print_r($gridTitle); ?>
              </h4>
            </div>
            <div class="row">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead class="text-primary">
                      <th>
                        Name
                      </th>
                      <th>
                          Devotee ID
                      </th>
                       <th>
                        Station
                      </th>
                      <th>
                        Cell Number
                      </th>
                      <th>
                        Photo
                      </th>
                      <th>
                        ID Image
                      </th>

                    </thead>
                    <tbody>
                      <?php
                            $recordCount=0;
                            foreach ($response as $devoteeRecord){
                               $devoteeKey = "--Unavailable--"; 
                               $devoteeName = "--Unavailable--";
                               $devoteeStation = "--Unavailable--";
                               $devoteeCellNumber = "--Unavailable--";
                               $devoteePhoto = "";
                               $devoteeID = "";
                               $recordCount = $recordCount+1;
                
                               if(!empty($devoteeRecord['devotee_key'])){
                                   $devoteeKey = urldecode($devoteeRecord['devotee_key']);
                               }
                               
                               if(!empty($devoteeRecord['Devotee_Name'])){
                                   $devoteeName = urldecode($devoteeRecord['Devotee_Name']);
                               }
                               
                               if(!empty($devoteeRecord['devotee_station'])){
                                   $devoteeStation = urldecode($devoteeRecord['devotee_station']);
                               }
                               
                               if(!empty($devoteeRecord['devotee_cell_phone_number'])){
                                   $devoteeCellNumber = urldecode($devoteeRecord['devotee_cell_phone_number']);
                               }
                               
                               if(!empty($devoteeRecord['Devotee_Photo'])){
                                   $devoteePhoto = $devoteeRecord['Devotee_Photo'];
                               }
                                print_r("
                                 <tr>
                                 <td>
                                     <a href='adddevoteei.php?devotee_key=" . $devoteeKey . "'>" . $devoteeName . "</a>
                                 </td>
                                 <td>
                                     <a href='adddevoteei.php?devotee_key=" . $devoteeKey . "'>" . $devoteeKey ."</a>
                                 </td>
                                 <td>
                                     <a href='adddevoteei.php?devotee_key=" . $devoteeKey . "'>" . $devoteeStation . "</a>
                                 </td>
                                   <td>
                                       <a href='adddevoteei.php?devotee_key=" . $devoteeKey . "'>" . $devoteeCellNumber . "</a>
                                 </td>
                                 <td>
                                 " );
                                //<img src='../assets/img/faces/devotee.ico' height='70px' width='70px' alt='Devotee Image' />
                                if($devoteePhoto==""){
                                    print_r( '<img src="../assets/img/faces/devotee.ico" alt="Devotee Image" height="70px" width="70px"></img>');
                                 }
                                 else{
                                    print_r('<img src="data:image/jpeg;base64,'. $devoteePhoto . '" alt="devotee image" height="70px" width="70px"></img>');  
                                 }                                   
                                 
                                 print_r("</td>
                                 <td>
                                   <img src='../assets/img/faces/doc.png' height='70px' width='70px' alt='Devotee Scan ID' />
                                 </td>

                               </tr>
                               ");
                            }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
          </div>

          </div>
            <div class="col-md-8" hidden="true">
              <div class="card-body">
                  <form>
                    <button type="submit" class="btn btn-success pull-right" style="margin-left:30px;">Cancel</button>
                    <button type="submit" class="btn btn-success pull-right">Add Devotee without photo/image</button>
                    <button type="submit" class="btn btn-success pull-right" style="margin-right:30px;">Add photo/ID image</button>
                    <div class="clearfix"></div>
                  </form>
                </div>
            </div>
          </div>
        </div>                     
      </div>

    <!-- id modial -->
  </div>
  <!--   Core JS Files   -->
      <script>
          document.getElementById('pageHeader').textContent = document.getElementById('pageHeader').textContent  + "(" + <?php print_r($recordCount); ?> + " records found!)";
      </script>
  <?php
  include_once("scriptJS.php") ?>
</body>

</html>
