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
        $accommodationSearch = new clsOptionHandler("Accommodation");    
        $response = $accommodationSearch->getOptions();
        //var_dump($response);
        
        unset($accommodationSearch);
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
              <h4 class="card-title">Accommodation Records</h4>
            </div>
            <div class="row">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead class=" text-primary">
                      <th>
                        Accommodation Key
                      </th>
                      <th>
                          Accommodation Name
                      </th>
                      <th align='right'>
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
                      </th>
                    </thead>
                    <tbody>
                         <?php
                              $recordCount = 0;
                              if (!empty($response) ) {
                                      foreach ($response as $accommodationRecord) {
                                      $accomodationKey = "--Unavailable--";
                                      $accomodationName = "--Unavailable--";
                                      $accomodationCapacity = "--";
                                      $reservedCount = "--";
                                      $outOfAvailabilityCount = "--";
                                      $allocatedCount = "--";
                                      $availableCount = "--";


                                      if (!empty($accommodationRecord['accomodation_key'])) {
                                          $accomodationKey = urldecode($accommodationRecord['accomodation_key']);
                                      }

                                      if (!empty($accommodationRecord['Accomodation_Name'])) {
                                          $accomodationName = urldecode($accommodationRecord['Accomodation_Name']);
                                      }

                                      if (!empty($accommodationRecord['Accomodation_Capacity'])) {
                                          $accomodationCapacity = urldecode($accommodationRecord['Accomodation_Capacity']);
                                      }

                                      if (!empty($accommodationRecord['Reserved_Count'])) {
                                          $reservedCount = urldecode($accommodationRecord['Reserved_Count']);
                                      }

                                      if (!empty($accommodationRecord['Out_Of_Availability_Count'])) {
                                          $outOfAvailabilityCount = $accommodationRecord['Out_Of_Availability_Count'];
                                      }

                                      if (!empty($accommodationRecord['Allocated_Count'])) {
                                          $allocatedCount = $accommodationRecord['Allocated_Count'];
                                      }

                                      if (!empty($accommodationRecord['Available_Count'])) {
                                          $availableCount = $accommodationRecord['Available_Count'];
                                      }      
                                      
                                      if($accomodationKey !="--Unavailable--" ){
                                          $recordCount = $recordCount + 1;
                                      print_r("
                             <tr>
                             <td>
                                 <a href='addAccommodationI.php?accommodation_key=" . $accomodationKey . "'>" . $accomodationKey . "</a>
                             </td>
                             <td>
                                 <a href='addAccommodationI.php?accommodation_key=" . $accomodationKey . "'>" . $accomodationName . "</a>
                             </td>
                             <td align='right'>
                                 <a href='addAccommodationI.php?accommodation_key=" . $accomodationKey . "'>" . $accomodationCapacity . "</a>
                             </td>
                               <td align='right'>
                                   <a href='addAccommodationI.php?accommodation_key=" . $accomodationKey . "'>" . $reservedCount . "</a>
                             </td>
                             <td align='right'>
                                 <a href='addAccommodationI.php?accommodation_key=" . $accomodationKey . "'>" . $outOfAvailabilityCount . "</a>
                             </td>
                             <td align='right'>
                                 <a href='addAccommodationI.php?accommodation_key=" . $accomodationKey . "'>" . $allocatedCount . "</a>
                             </td>
                             <td align='right'>
                                 <a href='addAccommodationI.php?accommodation_key=" . $accomodationKey . "'>" . $availableCount . "</a>
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
               <form action="addAccommodationI.php">
<!--                    <button type="submit" class="btn btn-success pull-right" style="margin-left:30px;">Cancel</button>
                    <button type="submit" class="btn btn-success pull-right">Add Devotee without photo/image</button>-->
                    <button type="submit" class="btn btn-success pull-right" >Add New Accommodation</button>
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
