<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/web_session.php';

require_once '../Lib/PhpExcelComponent.php';
include_once("../Logic/clsOptionHandler.php");
/* Uncomment these lines for initial setup testing
 * 
  $PhpExcel=New PhpExcelComponent();
  $PhpExcel->createExcel();
  $PhpExcel->downloadFile();
 */
getSevaAssignmentCounts();
function getSevaAssignmentCounts() {
    // Create excel object
    $PhpExcel=New PhpExcelComponent();
    $PhpExcel->createExcel();
    
    $sevaSearch = new clsOptionHandler("Seva");
    $sevaRes = $sevaSearch->getOptions();
    // Add heading to Sheet
    $PhpExcel->writeCellValue('A1','Seva');
    $PhpExcel->fillCellColour('A1','717d7e');
    $PhpExcel->fillCellColour('B1','717d7e');
    $PhpExcel->writeCellValue('B1','Total Devotee');
    //---------------------------
    foreach($sevaRes as $key=>$seva){
        $sevaTitle= urldecode($seva['Seva_Description']);
        $sevaCount=$seva['assigned_count'];
        $index= intval($key+2);
        $PhpExcel->writeCellValue('A'.$index,$sevaTitle);
        $PhpExcel->writeCellValue('B'.$index,$sevaCount);
    }
    $PhpExcel->downloadFile();
    //echo '<pre>';
    //print_r($sevaRes);
}
