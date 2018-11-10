<!DOCTYPE html>
<html lang="en">

<head>
  <title>
    KDMS (Available Devotees)
  </title>
  <?php
    include_once("header.php");
  ?>
</head>

<body class="">

  <div class="wrapper ">
    <?php
        include_once("nav.php");
      ?>

    <div class="main-panel">
      <!-- Navbar -->
      <?php
        include_once("navBottom.php");
        $DisplayMode = "";
        if(!empty($_GET['DisplayMode'])){
            $DisplayMode = $_GET['DisplayMode'];
        }
       ?>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title">
                <?php               
                      switch ($DisplayMode){
                        case "PWD":
                            print_r("Incomplete Records with photo/ID");
                        break;
                        
                        default :
                            print_r("Available Devotee Records");
                        break;                                 
                      }                                                                                                   
                ?>
              </h4>
            </div>
            <div class="row">
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead class=" text-primary">
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
                    </thead>
                    <tbody>
                      <tr>
                        <td>
                            Jugal
                        </td>
                        <td>
                            P1810142093
                        </td>
                        <td>
                          Nainital
                        </td>
                        <td>
                          8393XXXX33
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
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
