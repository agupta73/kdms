<?php
$debug = false;
require_once dirname(__DIR__) . '/kmreports_log.php';
kmreports_log_bootstrap();

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include_once("../Logic/clsServicesManager.php");
$config_data=include("../site_config.php");

if ($debug) {echo "current session ID: ", session_id(), "<br>", "session_status: ", session_status(), "<br>";}

//Distroy session, if active
if(session_status() == PHP_SESSION_ACTIVE ){
    session_unset();
    session_destroy();
    if ($debug) {echo "current session ID from inside reset code: ", session_id(), "<br>", "session_status: ", session_status(), "<br>";}
}

$requestData = $_POST;
$loginID = "";
$password = "";
$role="";
$name = "";
$email = "";
$phone ="";
$access = "";
$message = "";
//Setting login ID and password to display on the login screen
//if(isset($_POST['loginID'])){$loginID = $_POST['loginID']; }
//if(isset($_POST['password'])){ $password = $_POST['password'];}

unset($_POST);
//Pre-populate devotee record in case of edit
if (!empty($requestData['loginID'])) {
    kmreports_log('INFO', 'KMReports login attempt', [
        'login_id' => (string) $requestData['loginID'],
    ]);
    $response = array();
    $adminTasks = new clsServicesManager($requestData);
    $adminTasks->setOptionType("login");
    $response=  $adminTasks->getRecords();
    unset($serviceClass);
    if($debug){
        echo "fetched the login data <br>";
        var_dump($response);
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

    if(!empty($response['Access'])){
      if($response['Access'] != ""){
        $access =  urldecode($response['Access']); 
      }
    }

    if($debug){
        echo "<br>","login: ", $loginID, "<br>";
        var_dump($password);
        var_dump($role);
        var_dump($name);
        var_dump($email);
        var_dump($phone);
        var_dump($access);    
    }
    if(!empty($loginID)){
        kmreports_log('NOTICE', 'KMReports login success', [
            'login_id' => $loginID,
            'role'     => $role,
        ]);
        if($debug){echo "reaching non-empty login ID..";}
        include_once("../initialize.php");
        $_SESSION["LoginID"] = $loginID;
        $_SESSION["UserName"] = $name;
        $_SESSION["UserEmail"] = $email;
        $_SESSION["Role"] = $role;
        $_SESSION["Access"] = $access;
        $url = $config_data['local_root']."UI/Index.php";
        header("Location: ".$url);
        exit();
    }
    else{
        kmreports_log('WARNING', 'KMReports login failed', [
            'login_id' => (string) ($requestData['loginID'] ?? ''),
            'response' => is_array($response) ? $response : ['raw' => (string) $response],
        ]);
        if (!empty($response['message'])) {
            $message = "Login failed: " . $response['message'];
        } elseif (!empty($response['status']) && $response['status'] === false && !empty($response['info'])) {
            $message = "Login failed: " . $response['info'];
        } else {
            $message = "Incorrect credentials!";
        }
        $loginID = $requestData['loginID'];
        $password = $requestData['password'];
        if($debug){echo "LoginID: ", $loginID, " password: " , $password, " Message: " , $message, " Access: " , $access; }
    
    }
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>
    SignIn(Kainchi Management Reports)
  </title>
  <?php include_once("header.php"); ?>
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
              <h4 class="card-title">KM Reports Login </h4>
            </div>
            <div class="card-body">
              <form id="myForm" method="post" action="login.php">
                <div class="row">

                </div>
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Username</label>
                      <input type="text" class="form-control" name="loginID" id="loginID" value="<?php echo $loginID; ?>">
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label class="bmd-label-floating">Password</label>
                      <input type="password" class="form-control" name="password" id="password"
                        value="<?php echo $password; ?>">
                      <input type="hidden" name="type" id="type" value="login">
                    </div>
                  </div>

                </div>
                <div class="row">
                  <div class="col-md-12">
                    <p class="text-danger">
                      <?php echo $message; ?>
                    </p>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-12">

                    <div class="form-group">
                      <a href="recovery.php"> <label class="bmd-label-floating">Forgot password</label></a>
                    </div>
                  </div>
                </div>

                <button class="btn btn-success pull-right" onclick="">SignIn</button>
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
  <?php include_once("scriptJS.php"); ?>
</body>


</html>