<?php
$config_data=include("../site_config.php");
?>
<div class="sidebar" data-color="purple" data-background-color="white" data-image="../assets/img/sidebar-1.jpg">
  <!--
    Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

    Tip 2: you can also add an image using data-image tag
-->

  <div class="logo">
    <a href="#" class="simple-text logo-normal">
      KDMS 
    </a>
  </div>

  <div class="sidebar-wrapper">
    <ul class="nav">
      <li class="nav-item active  ">
        <a class="nav-link" href="./index.php">
          <i class="material-icons">dashboard</i>
          <p>Dashboard</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="./registration.php">
          <i class="material-icons">camera_alt</i>
          <p>Photo/ID Capture</p>
        </a>
      </li>
       <li class="nav-item ">
        <a class="nav-link" href="./addDevoteeI.php">
          <i class="material-icons">person_add</i>
          <p>Register New Devotee</p>
        </a>
      </li>     
      <li class="nav-item ">
        <a class="nav-link" href="./devoteeSearchResult.php?mode=CUS&key=">
          <i class="material-icons">search</i>
          <p>Search Devotees</p>
        </a>
      </li>

      <li class="nav-item ">
        <a class="nav-link" href="./devoteeSearchResult.php?mode=SET&key=PWD">
          <i class="material-icons">camera_front</i>
          <p>Devotee Records with Photo</p>
        </a>
      </li>
      <li class="nav-item ">
        <a class="nav-link" href="./devoteeSearchResult.php?mode=SET&key=DWP">
          <i class="material-icons">camera_enhance</i>
          <p>Devotee Records w/o Photo</p>
        </a>
      </li>     
      <li class="nav-item ">
        <a class="nav-link" href="./devoteeSearchResult.php?mode=SET&key=CTP">
          <i class="material-icons">print</i>
          <p>Devotee Cards for Printing</p>
        </a>
      </li>         
    </ul>
      
      
  </div>
 <div class="content">
        <div class="container-fluid">
<!--          <div style="width: 500px;z-index:5;position:absolute;top:50px;left:50px;">-->
          <div style="width: 240px;z-index:15;position:absolute;top:500px;left:10px;">

          <div id="search-box-container" >
          <input  type="text" id="search-data" name="searchData" placeholder="Search By name, phone or stn" autocomplete="off" />
          </div>
          <div >
          <div class="scrollbar-dynamic-search" id="style-6">
              <div class="force-overflow" id="search-result-container" style="display:none;position:relative;">
                  <!-- search results comes over here -->
              </div>
            </div>
          </div>
          </div>
        </div>
     </div>
</div>
<br>


 <script src="../assets/js/jquery-3.2.1.min.js"></script>
  <style>
    #search-data{
      padding: 10px;
      border: solid 1px #BDC7D8;
      border-radius: 7px;
      margin-bottom: 20px;
      display: inline;
      background-color: rgb(255, 255, 255);
      width: 100%;
      color: grey;

    }
    .search-result{
      padding:10px;
      background-color: rgb(255, 255, 255);
      font-size: 16px;color:grey;
    }
  .search-result{
    border:solid 1px grey;
  }
  </style>

    <script>
      $(document).ready(function() {
      $('#search-data').unbind().keyup(function(e) {
          var value = $(this).val();
          if (value.length>2) {
      		//alert(99933);
              searchData(value);
          } else {
               $('#search-result-container').hide();
          }
      });
      });

      function searchData(val){
          var reqType = "dynamicSearchDevotee";
      	$('#search-result-container').show();
      	$('#search-result-container').html('<div><img src="../assets/img/preloader.gif" width="50px;" height="50px"> <span style="font-size: 20px;">Please Wait...</span></div>');
        $.post('<?=$config_data['webroot'];?>Logic/requestManager.php',{'key': val, 'requestType': reqType}, function(data){
      		if(data != ""){
      			$('#search-result-container').html(data);
                    // add some css to scroll/view all data
                    $('.sidebar').css({'overflow-y':'scroll'});
                    $('.sidebar-wrapper').css({'overflow-y':'scroll'});
                }else{
      		$('#search-result-container').html("<div class='search-result'> Please wait.. </div>");
            }
      	}).fail(function(xhr, ajaxOptions, thrownError) { //any errors?
            $('#search-result-container').html('');
      	   alert(thrownError); //alert with HTTP error
      	});
      }

  </script>