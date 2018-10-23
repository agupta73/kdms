<?php
	include("../Logic/clsJQuerySearchHandler.php");
	include_once 'config/database.php';

  $database = new Database();
  $db = $database->getConnection();

	if(isset($_POST["search-data"])){
		$searchVal = trim($_POST["search-data"]);
		$dao = new DevoteeSearch($db);
		echo $dao->searchData($searchVal);
	}
?>
