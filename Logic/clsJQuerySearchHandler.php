<?php //
//Depricated code... PLEASE DO NOT USE..
//========================================
//class DevoteeSearch{
//
//  private $requestData = array();
//  public function __construct($requestObject) {
//          $this->requestData = $requestObject;
//      }
//
//	public function searchData($searchVal){
//
//		try {
//      
//			$stmt = $this->requestData->prepare("SELECT * FROM `Devotee` WHERE `Devotee_First_Name` like :searchVal OR `Devotee_Last_Name` like :searchVal OR `Devotee_Station` like :searchVal OR `Devotee_Cell_Phone_Number` like :searchVal");
//			$val = "%$searchVal%";
//			$stmt->bindParam(':searchVal', $val , PDO::PARAM_STR);
//			$stmt->execute();
//
//			$Count = $stmt->rowCount();
//			//echo " Total Records Count : $Count .<br>" ;
//
//			$result ="" ;
//			if ($Count  > 0){
//				while($data=$stmt->fetch(PDO::FETCH_ASSOC)) {
//				   $result = $result .'<a href="addDevoteeI.php?devotee_key='.$data['Devotee_Key'].'"><div class="search-result">'.$data['Devotee_First_Name'].' '.$data['Devotee_Last_Name'].' - ('.$data['Devotee_Station'].') - '.$data['Devotee_Cell_Phone_Number'].'</div></a>';
//				}
//				return $result ;
//			}
//		}
//		catch (PDOException $e) {
//			echo 'Connection failed: ' . $e->getMessage();
//		}
//	}
//
//}


?>
