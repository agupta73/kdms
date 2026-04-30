<?php
if (!defined('KMREPORTS_SESSION_READY')) {
    $debug = false;
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $current_page_id = "KR-DSBRD";
    include_once("../sessionCheck.php");
    $config_data = include("../site_config.php");
}
?>
<div class="sidebar" data-color="purple" data-background-color="white" data-image="../assets/img/sidebar-1.jpg">
  <!--
      Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

      Tip 2: you can also add an image using data-image tag
  -->


  <div class="logo">
    <a href="#" onclick=refreshSession() class="simple-text kdms-title logo-normal">
      <h3>KM Reports</h3>
        <!--<br>
       <b> <?=$_SESSION["eventDesc"];?> </b>-->
    </a>

      <script type="text/javascript">
          function refreshSession(){
              //alert("This doesn't do anything. You will need to somehow kill your session, if you are trying to load the next event. Adding log out functionality will fix this issue!");

              <?php
              //session_unset();
              //header("Location: /index.php");
              if ($debug) {echo "current session ID: ", session_id(), "<br>", "session_status: ", session_status(), "<br>";}
              ?>
              //location.reload();
          }
      </script>

  </div>

  <div class="sidebar-wrapper">
    <ul class="nav">
      <li class="nav-item active  ">
        <a class="nav-link" href="./index.php">
          <i class="material-icons">dashboard</i>
          <p>Reports Dashboard</p>
        </a>
      </li>     
    </ul>
  </div>
</div>
<?php include_once("topNav.php"); ?>

 <script src="../assets/js/jquery-3.2.1.min.js"></script>
    <script>
      $(document).ready(function() {
      $('#search-data').unbind().keyup(function(e) {
          var value = $(this).val();
          if (value.length>2) {
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
