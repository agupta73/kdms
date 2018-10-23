<!DOCTYPE html>
<html>
<head>
  <title>
    KDMS
  </title>
  <?php
  include_once("header.php") ?>
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
                    <h4 class="card-title">Account Recovery</h4>
                  </div>
                  <div class="card-body">
                      <form  id="myForm">
                      <div class="row">

                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="bmd-label-floating">E-mail</label>
                            <input type="text" class="form-control" name="email" id="email">
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">

                      <button class="btn btn-success pull-right" onclick="" >Submit</button>

                    <a href="login.php">SignIn</a>
                    </form>
                     <div class="clearfix"></div>
                  </div>
                  <label class="bmd-label-floating">Note : New password will be send to your registered email address</label>

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
