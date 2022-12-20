<!DOCTYPE html>
<html lang="en">

<head>
  <title>
    KDMS (Add/Update Events)
  </title>
  <?php
  $config_data = include("../site_config.php");
  if (session_status() === PHP_SESSION_NONE){
      session_start();
    }
  $current_page_id = 'KD-EVNT-II';
  include_once("../sessionCheck.php");
    include_once("header.php");
    include_once("../Logic/clsOptionHandler.php");
  ?>
</head>

<body class="">
  
  <div class="wrapper ">
    <?php
    //TODO: Notify user if no event is current or more than one event is current
        include_once("nav.php");
        $eventSearch = new clsOptionHandler("Event");
        $response = $eventSearch->getOptions();
        //var_dump($response);

        unset($eventSearch);
      ?>

    <div class="main-panel">
      <div class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title">Event Records</h4>
            </div>
            <div class="row">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead class=" text-primary">
                      <th>
                        Event ID
                      </th>
                      <th>
                          Event Description
                      </th>
                      <th>
                          Event Status
                      </th>
                    </thead>
                    <tbody>
                         <?php
                              $recordCount = 0;
                              if (!empty($response) ) {
                                      foreach ($response as $eventRecord) {
                                      $eventID = "--Unavailable--";
                                      $eventDescription = "--Unavailable--";
                                          $eventStatus = "--";

                                      if (!empty($eventRecord['Event_Id'])) {
                                          $eventID = urldecode($eventRecord['Event_Id']);
                                      }

                                      if (!empty($eventRecord['Event_Description'])) {
                                          $eventDescription = urldecode($eventRecord['Event_Description']);
                                      }

                                          if (!empty($eventRecord['Event_Status'])) {
                                              $eventStatus = urldecode($eventRecord['Event_Status']);
                                          }
                                      
                                      if($eventID !="--Unavailable--" ){
                                          $recordCount = $recordCount + 1;
                                      print_r("
                             <tr>
                             <td>
                                 <a href='upsertEventI.php?event_ID=" . $eventID . "'>" . $eventID . "</a>
                             </td>
                             <td>
                                 <a href='upsertEventI.php?event_ID=" . $eventID . "'>" . $eventDescription . "</a>
                             </td>
                             <td>
                                 <a href='upsertEventI.php?event_ID=" . $eventID . "'>" . $eventStatus . "</a>
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
               <form action="upsertEventI.php">
<!--                    <button type="submit" class="btn btn-success pull-right" style="margin-left:30px;">Cancel</button>
                    <button type="submit" class="btn btn-success pull-right">Add Devotee without photo/image</button>-->
                    <button type="submit" class="btn btn-success pull-right" >Add/Update Event</button>
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

