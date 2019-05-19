  <?php
        include_once("header.php");
        include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsDevoteeSearch.php");
        include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsReportHandler.php");
        // Include new config file in each page ,where we need data from configuration
        $config_data = include("../site_config.php");
        
        $getReport = new clsReportHandler();
        $response = $getReport->getAccommodationCounts();
        
        unset($getReport);

        ?> 
<script> //javascript function for ajax call 
  function clickHandler(formId, flag){
        
        document.getElementById("requestType").value = "refreshAcco";
        var formData = $(formId).serialize();
        
    if(validateInput()){
        
       switch (flag) {       
                case 1: //Refresh count
                    $.ajax({
                     url:'/KDMS/Logic/requestManager.php',
                     type:'POST',
                     data:formData,
                     success:function(response){
                           var r = JSON.parse(response);
                           if(r['status'] == true){
                               alert("Accomodation count refreshed successfully!");
                           }
                           else{
                               alert(response);
                           }  
                        }
                   });                
                   break;
                   
//                case 2: //Manage accommodations
//                    document.getElementById("myForm").action = "addAccommodationII.php";            
//                    document.getElementById(formId).submit();
//                    break;
//                
                default:
                    break;
               }
    }
  }

function validateInput(){
    return true;
}
  </script>
  <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">add</i>
                  </div>
                  <p class="card-category">Registration</p>
                  <h3 class="card-title"> Registration</h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">image</i>
                    <a href="../UI/registration.php" class="dash-link">Photo and ID Scan</a>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">add</i>
                    <a href="../UI/addDevoteeI.php"  class="dash-link">Add Devotee</a>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">print</i>
                    <a href="#pablo" class="dash-link">Print Cards</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">edit</i>
                  </div>
                  <p class="card-category">Update</p>
                  <h3 class="card-title">Devotee Update</h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">search</i>
                    <a href="./devoteeSearchResult.php?mode=CUS&key=" class="dash-link">Search Devotee</a>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">edit</i>
                    <a href="#pablo" class="dash-link">Modify Delete Record</a>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">print</i>
                    <a href="./devoteeSearchResult.php?mode=SET&key=CTP" class="dash-link">Print Cards</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <form name="myForm" id="myFormID">
                    <input type="hidden" name="requestType" id="requestType" value="none">
                    <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">links</i>
                  </div>
                  <p class="card-category">Links</p>
                  <h3 class="card-title">Quick Links</h3>
                </div>
                <div class="card-footer" onclick="clickHandler('#myFormID', 1); return false;">
                  <div class="stats">
                    <i class="material-icons text-danger">refresh</i>
                    <a href class="dash-link">Refresh Accommodation Counts</a>
                  </div>
                </div>
                <div class="card-footer" >
                  <div class="stats">
                    <i class="material-icons text-danger">add</i>
                    <a href="addAccommodationII.php" class="dash-link">Manage Accommodations</a>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">edit</i>
                    <a href="upsertAmenityII.php" class="dash-link">Manage Amenities</a>
                  </div>
                </div>
                </div>             
              </form>
            </div>
            </div>
          <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-warning card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">Accommodation</i>
                  </div>
                  <p class="card-category">Statistics</p>
                  <h3 class="card-title"> Accommodations</h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">home</i>
                    <a href="../UI/registration.php" class="dash-link">Total Spaces Occupied:  
                      <b>  <?php echo $response[0]['SpaceOccupiedOrDevoteesPresent'];  ?> </b> </a>
                  </div>
                </div>
                <div class="card-footer">
                 <div class="stats">
                    <i class="material-icons text-danger">home</i>
                    <a href="../UI/registration.php" class="dash-link">Total Spaces Available:  
                      <b>  <?php echo $response[2]['AvailableSpaces'];  ?> </b> </a>
                   </div> 
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">home</i>
                    <a href="../UI/registration.php" class="dash-link">Total Spaces Reserved:
                      <b>  <?php echo $response[3]['ReservedSpaces'];  ?> </b> </a></div> 
                  </div>
              </div>
            </div>
           <div class="col-lg-4 col-md-6 col-sm-6">
              <div class="card card-stats">
                <div class="card-header card-header-success card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">Devotees</i>
                  </div>
                  <p class="card-category">Statistics</p>
                  <h3 class="card-title">Devotees</h3>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">home</i>
                    <a href="../UI/registration.php" class="dash-link">Devotees Residing in Ashram:  
                      <b>  <?php echo $response[0]['SpaceOccupiedOrDevoteesPresent'];  ?> </b> </a>
                  </div>
                </div>
                <div class="card-footer">
                 <div class="stats">
                    <i class="material-icons text-danger">home</i>
                    <a href="../UI/registration.php" class="dash-link">Devotees Registered for Seva:  
                      <b>  <?php echo $response[1]['RegisteredDevoteesIncludingLocals'];  ?> </b> </a>
                   </div> 
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">home</i>
                    <a href="../UI/registration.php" class="dash-link">Devotees with Own Arrangement:
                      <b>  <?php echo $response[4]['DevoteesWithOwnArrangements'];  ?> </b> </a></div> 
                  </div>
              </div>
            </div>
<!--            <div class="col-lg-4 col-md-6 col-sm-6">
                <form name="myForm" id="myFormID">
                    <input type="hidden" name="requestType" id="requestType" value="none">
                    <div class="card card-stats">
                <div class="card-header card-header-danger card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">links</i>
                  </div>
                  <p class="card-category">Links</p>
                  <h3 class="card-title">Quick Links</h3>
                </div>
                <div class="card-footer" onclick="clickHandler('#myFormID', 1); return false;">
                  <div class="stats">
                    <i class="material-icons text-danger">refresh</i>
                    <a href class="dash-link">Refresh Accommodation Counts</a>
                  </div>
                </div>
                <div class="card-footer" >
                  <div class="stats">
                    <i class="material-icons text-danger">add</i>
                    <a href="addAccommodationII.php" class="dash-link">Manage Accommodations</a>
                  </div>
                </div>
                <div class="card-footer">
                  <div class="stats">
                    <i class="material-icons text-danger">edit</i>
                    <a href="upsertAmenityII.php" class="dash-link">Manage Amenities</a>
                  </div>
                </div>
                </div>             
              </form>
            </div>-->
            </div></div>
        </div>
