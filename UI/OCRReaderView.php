<!DOCTYPE html>
<?php
$config_data = include("../site_config.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page_id = 'KD-DVT-SCR';
include_once("../sessionCheck.php");
include_once("header.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsDevoteeSearch.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/kdms/Logic/clsOptionHandler.php");
// Include new config file in each page ,where we need data from configuration

$eventId = $config_data['event_id'];
$debug = false;
//if($debug){var_dump( $_GET);}
?>
<html lang="en">

<head>
    <title> KDMS (OCR Reader) </title>
    <?php
    include_once("header.php");
    ?>
</head>

<body class="">
    <div class="wrapper ">
        <?php include_once("nav.php"); ?>
        <div class="main-panel">
            <!-- Navbar -->
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="row">
                                <div class="col-12">
                                    <!-- image selection area -->
                                    <div class="card-body image-selection-area">
                                        <!-- Todo: css changes to position drag and drop label to middle -->
                                        <!-- <label class="bmd-label-floating image-selection-area-label">
                                            Drag and Drop
                                            Images Here</label> -->
                                        <span class="dragBox" >
                                            Click or Drag and Drop images here
                                            <input type="file" onChange="dragNdrop(event)"  ondragover="drag()" ondrop="drop()" id="uploadFile"  multiple="multiple" />
                                        </span>
                                        <button class="btn btn-success pull-right">
                                            <i class="material-icons">upload_file</i> Upload to temp bucket
                                        </button>
                                        <!-- Todo: css changes to position the button on bottom middle with icon -->
                                        <!-- <button class="btn btn-info pull-right">Upload Image</button> -->
                                    </div>
                                </div>
                            </div>
                            <div class="row selected-images">
                                <div class="col-12">
                                    <!-- selected temp image list area -->
                                    <div id="ocr_image_preview" class="card-body">
                                    </div>
                                </div>
                            </div>
                            <div class="row image-list">
                                <div class="col-12">
                                    <!-- uploaded image list area -->
                                    <div class="card-body">
                                        <button class="btn btn-warning pull-right">
                                            <i class="material-icons">clear_all</i>
                                            clear bucket
                                        </button>
                                        <h4>Temprory bucket list view</h4>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#Sr. No.</th>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">Is file used</th>
                                                        <th scope="col">Uploaded at</th>
                                                        <th scope="col">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th scope="row">1</th>
                                                        <td>Image 1</td>
                                                        <td>
                                                            <i class="material-icons">check</i>
                                                        </td>
                                                        <td>Time of upload</td>
                                                        <td class="action-btns">
                                                            <button class="btn btn-success pull-right">
                                                                <i class="material-icons">upload_file</i>
                                                            </button>
                                                            <button class="btn btn-info pull-right">
                                                                <i class="material-icons">visibility</i>
                                                            </button>
                                                            <button class="btn btn-danger pull-right">
                                                                <i class="material-icons">delete</i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">2</th>
                                                        <td>Image 2</td>
                                                        <td>
                                                            <i class="material-icons">check</i>
                                                        </td>
                                                        <td>Time of upload</td>
                                                        <td class="action-btns">
                                                            <button class="btn btn-success pull-right">
                                                                <i class="material-icons">upload_file</i>
                                                            </button>
                                                            <button class="btn btn-info pull-right">
                                                                <i class="material-icons">visibility</i>
                                                            </button>
                                                            <button class="btn btn-danger pull-right">
                                                                <i class="material-icons">delete</i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row">3</th>
                                                        <td>Image 3</td>
                                                        <td>
                                                            <i class="material-icons">close</i>
                                                        </td>
                                                        <td>Time of upload</td>
                                                        <td class="action-btns">
                                                            <button class="btn btn-success pull-right">
                                                                <i class="material-icons">upload_file</i>
                                                            </button>
                                                            <button class="btn btn-info pull-right">
                                                                <i class="material-icons">visibility</i>
                                                            </button>
                                                            <button class="btn btn-danger pull-right">
                                                                <i class="material-icons">delete</i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="col-12">
                                    <!-- data preview and edit section -->
                                    <div class="card-body">
                                        <h4>Data Preview and Edit Section</h4>
                                        <form id="searchForm">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="bmd-label-floating">Name</label>
                                                        <input type="text" class="form-control"
                                                            name="devotee_name" id="devotee_name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4" >
                                                    <div class="form-group gender-kdms-ocr-view">
                                                        <label class="bmd-label-floating">Gender</label>
                                                        <!-- <input type="text" class="form-control" name="devotee_gender" id="devotee_gender" value="<?php print_r($devotee_gender); ?>"> -->
                                                        <select type="text" class="form-control" name="devotee_gender" id="devotee_gender"  value="<?php print_r($devotee_gender); ?>">
                                                            <option value="-" <?php
                                                            // if ($devotee_gender == "m"  || $devotee_gender == "M" || empty($devotee_gender)) {
                                                            //     print_r("selected");
                                                            // }
                                                            ?>>Not Selected</option>
                                                            <option value="M" <?php
                                                            // if ($devotee_gender == "m"  || $devotee_gender == "M" || empty($devotee_gender)) {
                                                            //     print_r("selected");
                                                            // }
                                                            ?>>Male</option>
                                                            <option value="F" <?php
                                                            // if ($devotee_gender == "F"  || $devotee_gender == "f") {
                                                            //     print_r("selected");
                                                            // }
                                                            ?>>Female</option>                                                            
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="bmd-label-floating">Date of Birth</label>
                                                        <input type="text" class="form-control" name="devotee_dob" id="devotee_dob" value="<?php 
                                                        // print_r($devotee_dob); 
                                                        ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="bmd-label-floating">ID Number</label>
                                                        <input type="text" class="form-control" name="devotee_id_number"
                                                            id="devotee_id_number">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="bmd-label-floating">Mobile</label>
                                                        <input type="text" class="form-control" name="devotee_cell_number"
                                                            id="devotee_cell_number">
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Address</label>
                                                            <!--<label class="bmd-label-floating"> Add additional Information</label>-->
                                                            <textarea class="form-control" rows="2" name="comments" id="comments"> 
                                                                <?php 
                                                                // print_r($comments); 
                                                                ?>
                                                            </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="reset" class="btn btn-warning pull-right">Cancel</button>
                                            <button class="btn btn-success pull-right">Submit</button>
                                        </form>
                                        <!--<div class="clearfix"></div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <!-- image preview section -->
                                    <h4>Image Preview</h4>
                                    <div id="ocr_selected_image_preview" class="card-body ocr-id-image-preview-section">
                                        
                                        <!-- <label class="bmd-label-floating">First Name</label>
                                        <button type="reset" class="btn btn-success pull-right">Cancel</button>
                                        <button class="btn btn-success pull-right">Search</button> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<?php include_once("scriptJS.php") ?>
<script src="../assets/js/ocr_reader/file_upload.js"></script>
</html>