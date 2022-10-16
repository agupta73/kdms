<!DOCTYPE html>
<?php
//TODO: 1. Kill session (move code from nav.php
//2. start session
//3. validate credentials
//3. add session variables
//- login ID
//- authority
//- event ID/Desc

$debug = true;
if ($debug) {echo "current session ID: ", session_id(), "<br>", "session_status: ", session_status(), "<br>";}
//Distroy session, if active
if(session_status() == PHP_SESSION_ACTIVE ){
    session_unset();
    session_destroy();
    if ($debug) {echo "current session ID: ", session_id(), "<br>", "session_status: ", session_status(), "<br>";}
}
?>

<?php
$config_data=include_once("../site_config.php");
include_once("../Logic/clsAdminTasks.php");

$requestData = $_POST;

$loginID = "";
$password="";
$role="";
$name = "";
$email = "";
$phone ="";

//Pre-populate devotee record in case of edit
if (!empty($requestData['loginID'])) {
    $response = array();
    $adminTasks = new clsAdminTasks($requestData);
    $response=  $adminTasks->processAdminTasks();

    if($debug){
        var_dump($response);
    }
    if($debug){
        echo "Response -> User key: ", urldecode($response['User_Key']), "<br>";
    }
    //assign values
    if(!empty($response['User_Key'])){

        $loginID = urldecode($response['User_Key']); //"P1810142093"
    }

    if(!empty($response['User_Name'])){
        $name=urldecode($response['User_Name']); // "p" "P";
    }

    if(!empty($response['User_Role'])){
        $role=urldecode($response['User_Role']); // "p" "P";
    }

    if(!empty($response['User_Email'])){
        $email= urldecode($response['User_Email']); // "Anil+6" ;
    }

    if(!empty($response['User_Phone'])){
        $phone=  urldecode($response['User_Phone']); // "Gupta"
    }
if($debug){
    echo "<br>","login: ", $loginID, "<br>";
    var_dump($password);
    var_dump($role);
    var_dump($name);
    var_dump($email);
    var_dump($phone);
    die;
}
}
?>
<script>


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
                    <h4 class="card-title">KDMS Login </h4>
                  </div>
                  <div class="card-body">
                      <form  id="myForm" method="post" action="login.php">
                      <div class="row">

                      </div>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="bmd-label-floating">Username</label>
                            <input type="text" class="form-control" name="loginID" id="loginID">
                          </div>
                        </div>
                      </div>

                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label class="bmd-label-floating">Password</label>
                            <input type="password" class="form-control" name="password" id="password">
                                <input type="hidden" name="type" id="type" value="login">
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
