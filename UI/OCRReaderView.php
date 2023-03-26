<!DOCTYPE html>
<?php
$config_data = include("../site_config.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page_id = 'KD-DVT-SCR';
include_once("../sessionCheck.php");
include_once("header.php");
$eventId = $config_data['event_id'];
// Include new config file in each page ,where we need data from configuration
?>
<html lang="en">

<head>
    <title> KDMS (OCR Reader) </title>
</head>

<body class="">
    <div class="wrapper ">
        <?php include_once("nav.php"); ?>
        <div class="main-panel">
            <div id="loading">
                <img id="loading-image" src="loader.gif" alt="Loading..." />
            </div>
            <!-- Navbar -->
            <div class="card" id="ocr_panel" data-event-id="<?php echo $eventId; ?>">
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
                                        <span class="dragBox">
                                            Click or Drag and Drop images here
                                            <input type="file" onChange="dragNdrop(event)" ondragover="drag()"
                                                ondrop="drop()" id="uploadFile" multiple="multiple" />
                                        </span>
                                        <!-- <button class="btn btn-success pull-right">
                                            <i class="material-icons">upload_file</i> Upload to temp bucket
                                        </button> -->
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
                                        <button class="btn btn-warning pull-right" onclick="remove_all_image_from_temp_bucket()">
                                            <i class="material-icons">clear_all</i>
                                            clear bucket
                                        </button>
                                        <h4>Temprory bucket list view</h4>
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#Sr.</th>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">Is file used</th>
                                                        <th scope="col">Uploaded at</th>
                                                        <th scope="col">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tempbucket_table_body">
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
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="bmd-label-floating">Name</label>
                                                        <input type="text" class="form-control" name="devotee_name"
                                                            id="ocr_form_devotee_name">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group gender-kdms-ocr-view">
                                                        <label class="bmd-label-floating">Gender</label>
                                                        <select type="text" class="form-control" name="devotee_gender" id="ocr_form_devotee_gender"
                                                            id="ocr_form_devotee_gender"
                                                            value="">
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
                                                        <input type="text" class="form-control" name="devotee_dob"
                                                            id="ocr_form_devotee_dob" value="<?php
                                                            // print_r($devotee_dob); 
                                                            ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="bmd-label-floating">ID Number</label>
                                                        <input type="text" class="form-control" name="devotee_id_number"
                                                            id="ocr_form_devotee_id_number">
                                                    </div>
                                                </div>
                                                <!-- <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="bmd-label-floating">Mobile</label>
                                                        <input type="text" class="form-control"
                                                            name="devotee_cell_number" id="devotee_cell_number">
                                                    </div>
                                                </div> -->
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label>Address</label>
                                                        <!--<label class="bmd-label-floating"> Add additional Information</label>-->
                                                        <textarea class="form-control" rows="2" name="address"
                                                            id="ocr_form_devotee_address">
                                                                <?php
                                                                // print_r($comments); 
                                                                ?>
                                                            </textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <button id="kdms_ocr_create_anyway" class="btn btn-success pull-right" onclick="kdms_ocr_submit_btn(true)">Create Anyway</button>
                                            <button id="kdms_ocr_clear" class="btn btn-warning pull-right" onclick="kdms_ocr_clear_btn()">Clear</button>
                                            <button id="kdms_ocr_submit" class="btn btn-success pull-right" onclick="kdms_ocr_submit_btn(false)">Submit</button>
                                        <!--<div class="clearfix"></div>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <!-- image preview section -->
                                    <h4>Image Preview</h4>
                                        <div class="card-body ocr-id-image-preview-section" id="ocr_selected_image_preview" >
                                        </div>
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
    <div class="modal fade" id="myModal" tabindex="-2" role="dialog" aria-labelledby="ModalTitle" aria-hidden="true">
        <div class="modal-dialog id-upload-modal" role="document" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalTitle">Search Results</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" >
                    <div class="col-12">
                        <!-- uploaded image list area -->
                        <div class="card-body">
                            <h4>Devotee Matched Records</h4>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#Sr.</th>
                                            <th scope="col">Photo</th>
                                            <th scope="col">Key</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Cell Phone</th>
                                            <th scope="col">Station</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="devotee_matched_records_table_body">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- </form> -->
            </div>
        </div>
    </div>
</body>
<?php include_once("scriptJS.php") ?>
<script src="../assets/js/ocr_reader/file_upload.js"></script>
</html>