<!DOCTYPE html>
<html lang="en">

<head>
  <title>
    KDMS (Add Devotee II) 
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
       ?>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <div class="card">
            <div class="card-header card-header-primary">
              <h4 class="card-title">Available Records to be added</h4>
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
                        Photo
                      </th>
                      <th>
                        ID Image
                      </th>
                      <th>
                        Select
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
                          <img src="../assets/img/faces/devotee.ico" height="70px" width="70px" alt="Devotee Image" />
                        </td>
                        <td>
                          <img src="../assets/img/faces/doc.png" height="70px" width="70px" alt="Devotee Scan ID" />
                        </td>
                        <td class="text-primary">
                          <button type="button" class="btn btn-info" name="select" id="selectD" onclick="window.location.href='adddevoteei.php?devotee_key=P1810142093'">Add Details</button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
          </div>

          </div>
            <div class="col-md-8">
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
