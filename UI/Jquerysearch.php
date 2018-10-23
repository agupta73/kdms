<?php
	include("../Logic/devoteeSearchJquery.php");
	if(isset($_POST["search-data"])){
		$searchVal = trim($_POST["search-data"]);
		$dao = new DAO();
		echo $dao->searchData($searchVal);
	}
?>
