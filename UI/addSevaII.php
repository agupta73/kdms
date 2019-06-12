<!DOCTYPE html>
<html lang="en">

<head>
  <title>
    KDMS (Add Accommodation II) 
  </title>
  <?php
    include_once("header.php");
    include_once("../Logic/clsOptionHandler.php");
  ?>
</head>

<body class="">
  
  <div class="wrapper ">
    <?php
        include_once("nav.php");
        $sevaSearch = new clsOptionHandler("Seva");    
        $response = $sevaSearch->getOptions();
        //var_dump($response); die;
        //string(3) "BKT" ["Seva_Description"]=> string(25) "Blanket Distribution Duty" } [1]=> array(2) { ["Seva_Id"]=> string(2) "MP" ["Seva_Description"]=> string(7) "Mal Pua" } [2]=> array(2) { ["Seva_Id"]=> string(2) "PV" ["Seva_Description"]=> string(14) "Prasaad Vitran" } [3]=> array(2) { ["Seva_Id"]=> string(2) "SS" ["Seva_Description"]=> string(10) "Shoe Stand" } [4]=> array(2) { ["Seva_Id"]=> string(3) "tst" ["Seva_Description"]=> string(22) "Testing+of+Seva+Update" } }
        unset($sevaSearch);
      ?>

    <div class="main-panel">
      <!-- Navbar -->
      <?php
        include_once("navBottom.php");
        
       ?>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title">Seva Records</h4>
            </div>
            <div class="row">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead class=" text-primary">
                      <th>
                        Seva ID
                      </th>
                      <th>
                          Seva Description
                      </th>
<!--                      <th align='right'>
                         Capacity
                      </th>
                      <th align='right'>
                         Reserved
                      </th>
                      <th align='right'>
                        Unavailable 
                      </th>
                      <th align='right'>
                        Allocated 
                      </th>
                      <th align='right'>
                        Available 
                      </th>-->
                    </thead>
                    <tbody>
                         <?php
                              $recordCount = 0;
                              if (!empty($response) ) {
                                      foreach ($response as $sevaRecord) {
                                      $sevaID = "--Unavailable--";
                                      $sevaDescription = "--Unavailable--";
//                                      $accomodationCapacity = "--";
//                                      $reservedCount = "--";
//                                      $outOfAvailabilityCount = "--";
//                                      $allocatedCount = "--";
//                                      $availableCount = "--";


                                      if (!empty($sevaRecord['Seva_Id'])) {
                                          $sevaID = urldecode($sevaRecord['Seva_Id']);
                                      }

                                      if (!empty($sevaRecord['Seva_Description'])) {
                                          $sevaDescription = urldecode($sevaRecord['Seva_Description']);
                                      }

//                                      if (!empty($accommodationRecord['Accomodation_Capacity'])) {
//                                          $accomodationCapacity = urldecode($accommodationRecord['Accomodation_Capacity']);
//                                      }
//
//                                      if (!empty($accommodationRecord['Reserved_Count'])) {
//                                          $reservedCount = urldecode($accommodationRecord['Reserved_Count']);
//                                      }
//
//                                      if (!empty($accommodationRecord['Out_Of_Availability_Count'])) {
//                                          $outOfAvailabilityCount = $accommodationRecord['Out_Of_Availability_Count'];
//                                      }
//
//                                      if (!empty($accommodationRecord['Allocated_Count'])) {
//                                          $allocatedCount = $accommodationRecord['Allocated_Count'];
//                                      }
//
//                                      if (!empty($accommodationRecord['Available_Count'])) {
//                                          $availableCount = $accommodationRecord['Available_Count'];
//                                      }      
//                                      
                                      if($sevaID !="--Unavailable--" ){
                                          $recordCount = $recordCount + 1;
                                      print_r("
                             <tr>
                             <td>
                                 <a href='addSevaI.php?seva_id=" . $sevaID . "'>" . $sevaID . "</a>
                             </td>
                             <td>
                                 <a href='addSevaI.php?seva_id=" . $sevaID . "'>" . $sevaDescription . "</a>
                             </td>
                             
                             </tr>
                             ");
                                     
                                  }
                              }
                              }
                              ?>
                    </tbody>
                  </table>
                </div>                  
              </div>  
                </div>
                <div class="card-body">
           <div class="col-md-12">                  
               <form action="addSevaI.php">
<!--                    <button type="submit" class="btn btn-success pull-right" style="margin-left:30px;">Cancel</button>
                    <button type="submit" class="btn btn-success pull-right">Add Devotee without photo/image</button>-->
                    <button type="submit" class="btn btn-success pull-right" >Add New Seva Type</button>
                    <div class="clearfix"></div>
                  </form>
                </div>
            </div>
          
          </div>

              
          </div>
        </div>
      </div>

    <!-- id modial -->

  </div>
  <!--   Core JS Files   -->
  <?php
  include_once("scriptJS.php") ?>
</body>

</html>
