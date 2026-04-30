<?php
// ===================================
// Common Functions
// ===================================
function printTitle($param, $debug = false)
{
    if ($debug) {
        //echo "<br>From Print Title => ";
        print_r($param);
    }
    print_r(("<div class='reportTitleContainer'><label class='reportTitle'>"));
    print_r(implode($param));
    print_r(("</label></div>"));

}

function printTableTitle($param, $numOfCols = 6, $addDate = false, $debug = false)
{
    if ($debug) {
        //echo "<br>From Print Title => ";
        print_r($param);
    }
    //print_r(("<tr><label class='reportTitle'>"));
    if($addDate == true){        
        print_r(("<tr><th colspan=" . (int)$numOfCols  . "><label class='reportTitle'>"));        
        print_r(implode($param));
        print_r(("</label></th><td colspan=3 align='right'><label > <b> [Dated - " . date('d/M/Y') . "] </b></label></td></tr>"));
    }
    else {
        print_r(("<tr><th colspan=1000><label class='reportTitle'>"));
        print_r(implode($param));
        print_r(("</label></th></tr>"));
    }
}

function printSuperTitle($param, $debug = false)
{
    if ($debug) {
        //echo "<br>From Print Title => ";
        print_r($param);
    }
    print_r(("<div class='reportHeaderContainer'><label class='reportHeader'>"));
    print_r(implode($param));
    print_r(("</label></div>"));

}

function printTable($param="" ,$debug=false)
{
    //echo "<br>From Print Table => ";
    if($debug){
        var_dump($param);
    }

    if($param != ""){

        print_r(("<br><form id='F-". $param['table_id'] ."'><table class='reportTable' >"));
    }
    else {
        print_r(("<br><form><table class='reportTable'>"));
    }
    
}
function printCloseTable($param)
{
    //echo "<br>From Print Table => ";
    print_r(("</table></form>"));
}

function printFooter($param, $debug = false)
{
    if ($debug) {
        echo "<br>From Print Footer => ";
    }
    print_r($param);
    print_r(("<!--footer -->"));
    
}

function printHeader($param, $debug = false)
{

    if ($debug) {
        echo "<br>From Print Header => ";
    }

    print_r(("<tr>"));
    foreach ($param as $name) {
        print_r(("<th class='reportHead'><label>"));
        print_r(($name));
        print_r(("</label></th>"));
    }
    print_r(("<tr>"));

}

function printRow($param, $debug = false)
{
    print_r(("<tr>"));
    foreach ($param as $paramKey => $name) {
        if (strtolower($paramKey) == 'devotee_photo') {
            if (strlen($name) > 10000) {
                print_r("<td class='reportCol reportImageSection'><div>");
                print_r('<img class="reportDevoteeProfileImage" src="data:image/jpeg;base64,' . $name . '" alt="devotee image" height="80px" width="80px"></img>');
            } else {
                print_r("<td class='reportCol reportImageSection'><div>");
                print_r('<img class="reportDevoteeProfileImage" src="../assets/img/faces/devotee.ico" alt="Devotee Image"></img>');
            }
        }
        else {
            print_r(('<td class="' . str_replace("+", " ", strtolower($paramKey)) . ' reportCol reportCell"><label>'));
            print_r(str_replace("+", " ", urldecode($name)));
            print_r(("</label></td>"));
        } 
    }
    print_r(("</tr>"));
}

/*
function printRowWithRem($param, $debug = false)
{
    print_r(("<tr>"));
    if ($debug) {var_dump($param);}
    foreach ($param as $paramKey => $val) {
        if (strtolower($paramKey) == 'devotee_photo') {
            print_r("<td class='reportCol reportImageSection'><a href='#' title='Click to add seva feedback!' data-toggle='modal' class='identifyingClass' data-target='#RemarksModalLong' data-backdrop='static'  data-keyboard='false' data-id='" . $param["devotee_key"] . "'>");
            if (strlen($val) > 10000) {                
                print_r('<img class="reportDevoteeProfileImage" src="data:image/jpeg;base64,' . $val . '" alt="devotee image" height="80px" width="80px"></img>');                
            } else {                
                print_r('<img class="reportDevoteeProfileImage" src="../assets/img/faces/devotee.ico" alt="Devotee Image"></img>');                
            }
            print_r('</a>');
        }
        else {
            print_r(('<td class="' . strtolower($paramKey) . ' reportCol reportCell"><label>'));
            if ($debug) {echo "<a> "; echo $param["devotee_key"]; echo "</a> ";}
            if( strtolower($paramKey) == "devotee_key" or strtolower($paramKey) == "remarks" ){
                print_r("<a href='#' title='Click to add seva feedback!' data-target='#RemarksModalLong' data-toggle='modal' class='identifyingClass' data-id='" . $param["devotee_key"] . "'>");
                print_r(str_replace("+", " ", urldecode($val)));
                print_r("</a>");
            }
            elseif( strtolower($paramKey) == 'attendance'){
                print_r("<a href='#' title='Click to add attendance feedback!' data-target='#RemarksModalLong' data-toggle='modal' class='remIdentifyingClass' data-id='" . $param["devotee_key"] . "'>");
                print_r(str_replace("+", " ", urldecode($val)));
                print_r("</a>");
            }
            elseif(strtolower($paramKey) == "devotee_name" ){
                print_r("<a href='#' title='Click to see more details about devotee!' data-target='#ParticipationModalLong' data-toggle='modal' class='identifyingClass2' data-id='" . $param["devotee_key"] . "'>");
                //print_r("<a href='#' onclick=javascript:showIFrame('" . $param["devotee_key"] . "') >");
                print_r(str_replace("+", " ", urldecode($val)));
                print_r("</a>");
            }
            else {
                print_r(str_replace("+", " ", urldecode($val)));
            }
            
            print_r(("</label></td>"));
        } 

    }
    print_r(("</tr>"));
}
*/

function printRowWithRem($param, $debug = false)
{
    print_r(("<tr>"));
    if ($debug) {var_dump($param);}

    foreach ($param as $paramKey => $val) {
        switch (strtolower($paramKey)) {
            case 'devotee_photo':

                print_r("<td class='reportCol reportImageSection'><a href='#' title='Click to add seva feedback!' data-toggle='modal' class='identifyingClass' data-target='#RemarksModalLong' data-backdrop='static'  data-keyboard='false' data-id='" . $param["devotee_key"] . "'>");
                if (strlen($val) > 10000) {
                    print_r('<img class="reportDevoteeProfileImage" src="data:image/jpeg;base64,' . $val . '" alt="devotee image" height="80px" width="80px"></img>');
                } else {
                    print_r('<img class="reportDevoteeProfileImage" src="../assets/img/faces/devotee.ico" alt="Devotee Image"></img>');
                }
                print_r('</a>');
                break;

            case 'devotee_key':
            case 'remarks':
                if ($debug) { echo "<a> "; echo $param["devotee_key"]; echo "</a> "; }

                print_r(('<td class="' . strtolower($paramKey) . ' reportCol reportCell"><label>'));
                print_r("<a href='#' title='Click to add seva feedback!' data-target='#RemarksModalLong' data-toggle='modal' class='identifyingClass' data-id='" . $param["devotee_key"] . "'>");
                print_r(str_replace("+", " ", urldecode($val)));
                print_r("</a>");
                break;

            case 'mark_attendance':
                print_r(('<td class="' . strtolower($paramKey) . ' reportCol reportCell"><label>'));
                print_r("Mark: "); 
                print_r("<a  id='Y-" . $param["devotee_key"] . "'title='Click to mark attendance!' onclick=submitAttendance(5,'Present','" . $param["devotee_key"] . "','" . $param["[h]seva_id"] . "')>");
                print_r(" Present ");                
                print_r("</a>");
                //print_r(" / ");
                print_r("<a  id='N-" . $param["devotee_key"] . "'title='Click to mark attendance!' onclick=submitAttendance(0,'Absent','" . $param["devotee_key"] . "','" . $param["[h]seva_id"] . "')>");
                print_r(" Absent");                
                print_r("</a>");
                break;

            case 'attendance':
                print_r(('<td class="' . strtolower($paramKey) . ' reportCol reportCell"><label>'));
                print_r("<a id='A-" . $param["devotee_key"] . "'>");
                print_r(str_replace("+", " ", urldecode($val)));              
                print_r("</a>");
                break;

            case 'devotee_name':
                print_r(('<td class="' . strtolower($paramKey) . ' reportCol reportCell"><label>'));
                print_r("<a href='#' title='Click to see more details about devotee!' data-target='#ParticipationModalLong' data-toggle='modal' class='identifyingClass2' data-id='" . $param["devotee_key"] . "'>");
                //print_r("<a href='#' onclick=javascript:showIFrame('" . $param["devotee_key"] . "') >");
                print_r(str_replace("+", " ", urldecode($val)));
                print_r("</a>");
                break;

            default:
            if(substr($paramKey, 0, 3) <> "[h]"){
                print_r(('<td class="' . strtolower($paramKey) . ' reportCol reportCell"><label>'));    
                print_r(str_replace("+", " ", urldecode($val)));
            }
            else {
                    print_r("<input type='hidden' name='H-" . strtolower(substr($paramKey, 3)) . "' id='H-" . urldecode($val) . "' value='" . urldecode($val) . "'>");                
            }
                break;
        }

        print_r(("</label></td>"));
    }
    print_r(("</tr>"));
}

function includeRemarkModal($path = "", $eventId = "", $userId = "", $remarkType = "MISC", $debug=false){
    if($path != ""){
        include_once($path); 
    }
    
    print_r("<script type='text/javascript'>");
    print_r("$(function () {");
        print_r("$('.identifyingClass').click(function () {");
            print_r("var devotee_key_value = $(this).data('id');");            
            
            print_r("$('.modal-footer #devotee_key').val(devotee_key_value);");  
            print_r("$('.modal-footer #remark_type').val('". $remarkType ."');");  
            print_r("$('.modal-footer #eventId').val('". $eventId ."');");  
            print_r("$('.modal-footer #userId').val('". $userId ."');");            
            print_r("})");
            print_r("});");
    print_r("</script>");
}

function printButton($buttonId, $debug=false){
    print_r(("<tr><td colspan=1000 align='right'>"));
    if ($debug) {
        var_dump($buttonId);
    }    
    print_r(("<button id='BP-". $buttonId['button_id'] ."' type='button'  class='btn btn-primary' onclick=submitAllAttendance('" . $buttonId['button_id'] . "',5,'Present');>Mark All Present</button> "));
    print_r((" "));
    print_r(("<button id='BA-". $buttonId['button_id'] ."' type='button'  class='btn btn-primary' onclick=submitAllAttendance('" . $buttonId['button_id'] . "',0,'Absent');>Mark All Absent</button> "));
    print_r(("<tr></td>"));
}

function includeAllAttendanceFunction($debug = false)
{
    print_r("<script type='text/javascript'>\n");
    print_r("function submitAllAttendance( sevaId, rating=5, remark='') {\n");
    print_r("for (i = 0; i < document.getElementById('F-' + sevaId).length; i++) {   \n");

    print_r("if(document.getElementById('F-' + sevaId)[i].type == 'hidden') { \n");
    if ($debug) {
        print_r("console.log(document.getElementById('F-' + sevaId)[i].value); \n");
        print_r("console.log(document.getElementById('F-' + sevaId)[i].name); \n");
    }

    print_r("if(document.getElementById('F-' + sevaId)[i].name == 'H-devotee_key'){ \n");
    print_r("var devotee_key = document.getElementById('F-' + sevaId)[i].value; \n");
    print_r("submitAttendance(rating, remark, devotee_key, sevaId); \n");
    if ($debug) {
        print_r("console.log(document.getElementById('F-' + sevaId)[i].value); \n");
    }
    print_r("} \n");
    print_r("} \n");
    print_r("} \n");
    print_r("} \n");
    print_r("</script>\n");
}
function includeFavModal($path = "", $debug=false){
    if($path != ""){
        include_once($path); 
    }
    print_r("<a href='#' title='Click to add this reprot to your favorite list!' data-target='#FavModalLong' data-toggle='modal'>");
    print_r("Add to favorite </a>");
}

function includeAttendanceFunction($path = "", $eventId = "", $userId = "", $remarkType = "MISC", $debug = false )
{
    
    print_r("<script type='text/javascript'>\n");
    print_r("function submitAttendance(rating, remark, devoteeKey, sevaId) {\n");

    print_r("const params = new URLSearchParams({\n");
    print_r("remark_type: 'ATTENDANCE',\n");
    print_r("rating: rating,\n");
    print_r("attendance_date: '" . date('Y-m-d') . "',\n");
    print_r("remark: remark,\n");
    print_r("devotee_key: devoteeKey,\n");
    print_r("seva_id: sevaId,\n");
    print_r("requestType: 'upsertAttendance',\n");
    print_r("eventId: '" . $eventId . "',\n");
    print_r("userId: '" . $userId . "'\n");
    print_r("});\n");

    
    print_r("var formData = params.toString();\n");
    if($debug) {
        print_r("console.log(params.toString());\n");
        //print_r("alert(formData);        \n");
    }
    print_r("$.ajax({");
    print_r("url: '../Logic/requestManager.php',\n");
    print_r("type: 'POST',\n");
    print_r("data: formData,\n");
    print_r("success: function (response) {\n");
    if($debug) {        
        print_r("alert(response);\n");    
    }
    print_r("var r = JSON.parse(response);\n");

    print_r("if (r['flag'] == true) {\n");
    print_r("//alert('Attendance submitted successfully!');\n");
    print_r("if(rating == 5) {\n");
        print_r("document.getElementById('Y-' + devoteeKey).innerHTML = '';\n");
        print_r("document.getElementById('N-' + devoteeKey).innerHTML = 'Absent';\n");
        print_r("document.getElementById('A-' + devoteeKey).innerHTML = 'Present';\n");
        print_r("}\n");
        print_r("else {\n");
            print_r("document.getElementById('N-' + devoteeKey).innerHTML = '';\n");
            print_r("document.getElementById('Y-' + devoteeKey).innerHTML = 'Present';\n");
            print_r("document.getElementById('A-' + devoteeKey).innerHTML = 'Absent';\n");
    print_r("}\n");

    print_r("} else {\n");
    print_r("alert(r['message']);\n");

    print_r("}\n");
    print_r("}\n");
    print_r("});\n");
    print_r("}\n");
    print_r("</script>\n");
}

function includeDisplayOnlyModal($path = "", $eventId = "", $userId = "", $debug=false){
  
    print_r("<script type='text/javascript'>\n");
    print_r("$(function () {\n");
        print_r("$('.identifyingClass2').click(function () {\n");
            print_r("var devotee_key_value = $(this).data('id');\n");            
            print_r("$('.modal-body #devoteeKey').val(devotee_key_value);\n");  
            print_r("})\n");
            print_r("});\n");
    print_r("</script>\n");

    print_r("<div id='show-record-modal'></div>\n");
    
    print_r("<script>\n");
    print_r("$(document).ready(function(){\n");
        //print_r("alert('test');");
        print_r("$('.identifyingClass2').click(function(){\n");
            print_r("var uKey=$(this).data('id');\n");
            print_r("$('#show-record-modal').html('');\n");
            print_r("$('#ParticipationModalLong').modal('hide');\n");
            //print_r("$('#ParticipationModalLong').modal('dispose');\n");
            print_r("$.ajax({\n");
                print_r("url: '" . $path . "?devoteeKey='+uKey,\n");
                print_r("}).done(function(data){\n");
                    //print_r("console.log(data);\n");
                    print_r("$('#show-record-modal').html(data);\n");
                    print_r("$('#ParticipationModalLong').modal('show');\n");
                    print_r("});\n");
                    print_r("});  \n");
                    print_r("});\n");
    print_r("</script>");

}


?>
