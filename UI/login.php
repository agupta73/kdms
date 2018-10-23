<!DOCTYPE html>
<html>
<head>
  <title>
    SignIn(KDMS)
  </title>
  <?php
    include_once("header.php");
  ?>
</head>
<body>

  <div class="content">
      <div class="container-fluid">
            <div class="row">
              <div class="col-md-4">
              </div>
                  <div class="col-md-4">
                <div class="card">
                  <div class="card-header card-header-primary">
                    <h4 class="card-title">KDMS LogIn </h4>
                  </div>
                  <div class="card-body">
                      <form  id="myForm">
                      <div class="row">

                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="bmd-label-floating">Username</label>
                            <input type="text" class="form-control" name="username" id="username">
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="bmd-label-floating">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                          </div>
                        </div>

                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <a href="recovery.php"<label class="bmd-label-floating">Forgot password</label></a>

                      <button class="btn btn-success pull-right" onclick="" >SignIn</button>
                  </form>
                     <div class="clearfix"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
            </div>
          </div>
        </div>
      </div>
      <?php
      include_once("scriptJS.php") ?>
</body>
</html>
