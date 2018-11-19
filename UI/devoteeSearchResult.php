<!DOCTYPE html>
<html lang="en">
<head>
  <title> KDMS (Available Devotee Records) </title>
  <?php 
    include_once("header.php"); 
    include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsDevoteeSearch.php");
    include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsOptionHandler.php");
  ?>
<script>

  function _(el){
    return document.getElementById(el);
  }


//javascript function for ajax call
  function submitSearch(formId, flag){
      searchForm = document.getElementById(formId);      
      var I = 0;
      var searchString = ""
        for(I = 0; I < searchForm.length; I++) {
            if(searchForm[I].value != ""){
                //alert(searchForm[I].id + ": " + searchForm[I].value);
                searchString = searchString + searchForm[I].id + "=" + encodeURI(searchForm[I].value) + ",";
            }
        }
        
        if(searchString.length > 1){            
            window.location = "./devoteeSearchResult.php?mode=CUS&key=" + searchString.substr(0,searchString.length-1);
        }
        else{
            alert("Please specify a search criteria!");
        }
  }

function validateInput(){
    return true;
}
  </script>
</head>

<body class="">
  <div class="wrapper ">
    <?php include_once("nav.php"); ?>

       <div class="main-panel">
      <!-- Navbar -->
       <?php
        include_once("navBottom.php");
        
        $searchKey = "";
        $gridTitle = "";
        $showSelection = FALSE;
        $hideSearchArea = FALSE;
       // var_dump($_GET);
        if(!empty($_GET['key'])){
            $searchKey = $_GET['key'];
        
            switch ($searchKey){
                case "CUS": //Future use.. isn't used currently
                    $gridTitle = "Devotee Search Result";
                break;
            
                case "PWD":
                    $gridTitle = "Incomplete Devotee Records with photo or ID";
                break;
            
                case "DWP":
                    $gridTitle = "Devotee Records without photo or ID";
                break;
            
                case "CTP":
                    $gridTitle = "Devotee Cards to be Printed";
                    $showSelection = TRUE;
                break;
            
                default :
                    $gridTitle = "Devotee Search Result";
                break; 
            }
            $devoteeSearch = new clsDevoteeSearch($_GET);    
            $response = $devoteeSearch->getDevoteeRecords();
            
            unset($devoteeSearch);
            
                $hideSearchArea=TRUE;
            
        }
        else { //If custom search and criteria has not been specified, load accommodation options and available spots                
                $loadAccommodation = new clsOptionHandler("Accommodation");
                $accommodations = $loadAccommodation->getOptions();
                unset($loadAccommodations);
        }
        
       ?>
      <!-- End Navbar -->
      <div class="card" <?php if ($hideSearchArea) {
          print_r(" hidden=true");
      } ?> >

          <div class="col-md-12">
              <div class="card">
                  <div class="card-header card-header-primary">
                      <h4 class="card-title">Search Devotee</h4>
                  </div>
                  <div class="card-body">
                      <form  id="searchForm">                    
                          <div class="row">
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <label class="bmd-label-floating">First Name</label>
                                      <input type="text" class="form-control" name="devotee_first_name" id="devotee_first_name">
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <label class="bmd-label-floating">Last Name</label>
                                      <input type="text" class="form-control" name="devotee_last_name" id="devotee_last_name" >
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <label class="bmd-label-floating">Cell Phone Number</label>
                                      <input type="text" class="form-control" name="devotee_cell_phone_number" id="devotee_cell_phone_number" >
                                  </div>
                              </div>
                              <div class="col-md-3">
                                  <div class="form-group" >
                                      <label class="bmd-label-floating" >ID Number</label>
                                      <input type="text" class="form-control" name="devotee_id_number" id="devotee_id_number" >
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-3">
                                  <div class="form-group">
                                      <label class="bmd-label-floating">Accommodation</label>            
                                      <select type="text" class="form-control" name="devotee_accommodation_key" id="devotee_accommodation_key" >
                                          <option value="">-No Accomodation-</option>
                                          <?php
                                          if(!empty($accommodations)) {
                                                foreach ($accommodations as $accommodation){
                                                          print_r("<option value='" . $accommodation['accomodation_key'] . "'");                                                    
                                                          Print_r(">" . $accommodation['Accomodation_Name'] . " - " . $accommodation['Available_Count'] . "</option>");
                                                } 
                                          }
                                          ?>
                                      </select>
                                  </div>
                              </div>  
                              <div class="col-md-3">
                                  <div class="form-group" style="margin-top:62px">
                                      <label class="bmd-label-floating">Station</label>
                                      <input type="text" class="form-control" name="devotee_station" id="devotee_station" >
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group" style="margin-top:62px">
                                      <label class="bmd-label-floating">Remarks</label>
                                      <input type="text" class="form-control" name="devotee_remarks" id="devotee_remarks" >
                                  </div>
                              </div>
                          </div>                          
                          <button type="reset" class="btn btn-success pull-right">Cancel</button>
                          <button class="btn btn-success pull-right" onclick="submitSearch('searchForm', 1); return false;">Search</button>
                      </form>
                      <!--<div class="clearfix"></div>-->
                  </div>
              </div>
          </div>
      </div>
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
                                      $recordCount = 0;
                                      if (!empty($response) ) {
                                          
                                          
                                          
                                              foreach ($response as $devoteeRecord) {
                                              $devoteeKey = "--Unavailable--";
                                              $devoteeName = "--Unavailable--";
                                              $devoteeStation = "--Unavailable--";
                                              $devoteeCellNumber = "--Unavailable--";
                                              $devoteePhoto = "";
                                              $devoteeID = "";
                                              

                                              if (!empty($devoteeRecord['devotee_key'])) {
                                                  $devoteeKey = urldecode($devoteeRecord['devotee_key']);
                                              }

                                              if (!empty($devoteeRecord['Devotee_Name'])) {
                                                  $devoteeName = urldecode($devoteeRecord['Devotee_Name']);
                                              }

                                              if (!empty($devoteeRecord['devotee_station'])) {
                                                  $devoteeStation = urldecode($devoteeRecord['devotee_station']);
                                              }

                                              if (!empty($devoteeRecord['devotee_cell_phone_number'])) {
                                                  $devoteeCellNumber = urldecode($devoteeRecord['devotee_cell_phone_number']);
                                              }

                                              if (!empty($devoteeRecord['Devotee_Photo'])) {
                                                  $devoteePhoto = $devoteeRecord['Devotee_Photo'];
                                              }
                                              if($devoteeKey !="--Unavailable--" ){
                                                  $recordCount = $recordCount + 1;
                                              print_r("
                                     <tr>
                                     <td>
                                         <a href='addDevoteeI.php?devotee_key=" . $devoteeKey . "'>" . $devoteeName . "</a>
                                     </td>
                                     <td>
                                         <a href='addDevoteeI.php?devotee_key=" . $devoteeKey . "'>" . $devoteeKey . "</a>
                                     </td>
                                     <td>
                                         <a href='addDevoteeI.php?devotee_key=" . $devoteeKey . "'>" . $devoteeStation . "</a>
                                     </td>
                                       <td>
                                           <a href='addDevoteeI.php?devotee_key=" . $devoteeKey . "'>" . $devoteeCellNumber . "</a>
                                     </td>
                                     <td>
                                     ");
                                              //<img src='../assets/img/faces/devotee.ico' height='70px' width='70px' alt='Devotee Image' />
                                              if ($devoteePhoto == "") {
                                                  print_r('<img src="../assets/img/faces/devotee.ico" alt="Devotee Image" height="70px" width="70px"></img>');
                                              } else {
                                                  print_r('<img src="data:image/jpeg;base64,' . $devoteePhoto . '" alt="devotee image" height="70px" width="70px"></img>');
                                              }

                                              print_r("</td>
                                     <td>
                                       <img src='../assets/img/faces/doc.png' height='70px' width='70px' alt='Devotee Scan ID' />
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
  <?php include_once("scriptJS.php") ?>
</body>

</html>
