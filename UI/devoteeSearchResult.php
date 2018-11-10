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
                    $gridTitle = "Incomplete Records with photo/ID";
                break;

                default :
                    $gridTitle = "Available Devotee Records";
                break; 
            }
            
            $devoteeSearch = new clsDevoteeSearch($_GET);    
            $response = $devoteeSearch->getDevoteeRecords();
            //var_dump($response);die;
        }
       ?>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title">
                <?php print_r($gridTitle); ?>
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
                      <th>
                        Photo
                      </th>
                      <th>
                        ID Image
                      </th>

                    </thead>
                    <tbody>
                      <tr>
                        <td>
                            <a href="adddevoteei.php?devotee_key=P1810142093">Jugal</a>
                        </td>
                        <td>
                            <a href="adddevoteei.php?devotee_key=P1810142093">P1810142093</a>
                        </td>
                        <td>
                            <a href="adddevoteei.php?devotee_key=P1810142093">cell number</a>
                        </td>
                          <td>
                              <a href="adddevoteei.php?devotee_key=P1810142093">Station</a>
                        </td>
                        <td>
                          <img src="../assets/img/faces/devotee.ico" height="70px" width="70px" alt="Devotee Image" />
                        </td>
                        <td>
                          <img src="../assets/img/faces/doc.png" height="70px" width="70px" alt="Devotee Scan ID" />
                        </td>
                        
                      </tr>
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
  <?php
  include_once("scriptJS.php") ?>
</body>

</html>
