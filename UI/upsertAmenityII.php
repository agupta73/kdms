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
        $amenitySearch = new clsOptionHandler("Amenity");    
        $response = $amenitySearch->getOptions();
        //var_dump($response);
        
        unset($amenitySearch);
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
              <h4 class="card-title">Amenity Records</h4>
            </div>
            <div class="row">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead class=" text-primary">
                      <th>
                        Amenity Key
                      </th>
                      <th>
                          Name
                      </th>
                      <th>
                          Status
                      </th>
                      <th align='right'>
                         Quantity
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
                                      foreach ($response as $amenityRecord) {
                                      $amenityKey = "--Unavailable--";
                                      $amenityName = "--Unavailable--";
                                      $amenityStatus = "--";
                                      $amenityCapacity = "--";
                                      $reservedCount = "--";
                                      $outOfAvailabilityCount = "--";
                                      $allocatedCount = "--";
                                      $availableCount = "--";


                                      if (!empty($amenityRecord['amenity_key'])) {
                                          $amenityKey = urldecode($amenityRecord['amenity_key']);
                                      }

                                      if (!empty($amenityRecord['Amenity_Name'])) {
                                          $amenityName = urldecode($amenityRecord['Amenity_Name']);
                                      }
                                      
                                      if (!empty($amenityRecord['Amenity_Status'])) {
                                          $amenityStatus = urldecode($amenityRecord['Amenity_Status']);
                                      }

                                      if (!empty($amenityRecord['Amenity_Quantity'])) {
                                          $amenityQuantity = urldecode($amenityRecord['Amenity_Quantity']);
                                      }

                                      if (!empty($amenityRecord['Reserved_Count'])) {
                                          $reservedCount = urldecode($amenityRecord['Reserved_Count']);
                                      }

                                      if (!empty($amenityRecord['Out_Of_Availability_Count'])) {
                                          $outOfAvailabilityCount = $amenityRecord['Out_Of_Availability_Count'];
                                      }

                                      if (!empty($amenityRecord['Allocated_Count'])) {
                                          $allocatedCount = $amenityRecord['Allocated_Count'];
                                      }

                                      if (!empty($amenityRecord['Available_Count'])) {
                                          $availableCount = $amenityRecord['Available_Count'];
                                      }      
                                      
                                      if($amenityKey !="--Unavailable--" ){
                                          $recordCount = $recordCount + 1;
                                      print_r("
                             <tr>
                             <td>
                                 <a href='upsertAmenityI.php?amenity_key=" . $amenityKey . "'>" . $amenityKey . "</a>
                             </td>
                             <td>
                                 <a href='upsertAmenityI.php?amenity_key=" . $amenityKey . "'>" . $amenityName . "</a>
                             </td>
                             <td>
                                 <a href='upsertAmenityI.php?amenity_key=" . $amenityKey . "'>" . $amenityStatus . "</a>
                             </td>
                             <td align='right'>
                                 <a href='upsertAmenityI.php?amenity_key=" . $amenityKey . "'>" . $amenityQuantity . "</a>
                             </td>
                               <td align='right'>
                                   <a href='upsertAmenityI.php?amenity_key=" . $amenityKey . "'>" . $reservedCount . "</a>
                             </td>
                             <td align='right'>
                                 <a href='upsertAmenityI.php?amenity_key=" . $amenityKey . "'>" . $outOfAvailabilityCount . "</a>
                             </td>
                             <td align='right'>
                                 <a href='upsertAmenityI.php?amenity_key=" . $amenityKey . "'>" . $allocatedCount . "</a>
                             </td>
                             <td align='right'>
                                 <a href='addAccommodationI.php?amenity_key=" . $amenityKey . "'>" . $availableCount . "</a>
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
               <form action="upsertAmenityI.php">
<!--                    <button type="submit" class="btn btn-success pull-right" style="margin-left:30px;">Cancel</button>
                    <button type="submit" class="btn btn-success pull-right">Add Devotee without photo/image</button>-->
                    <button type="submit" class="btn btn-success pull-right" >Add/Update Amenity</button>
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

