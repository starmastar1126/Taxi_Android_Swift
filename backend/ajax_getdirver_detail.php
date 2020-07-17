<?php 
include_once("common.php");
$iTripId = isset($_REQUEST['iTripId'])?$_REQUEST['iTripId']:'';
if($iTripId !=''){

$driver = array();
$sql = "SELECT t.*,d.* FROM trips t LEFT JOIN register_driver d ON t.iDriverId = d.iDriverId
WHERE t.iTripId =".$iTripId." AND (t.iActive = 'Active' OR t.iActive = 'On Going Trip') ORDER BY t.iTripId DESC";  
$db_dtrip = $obj->MySQLSelect($sql);

	if(!empty($db_dtrip)){
		echo  json_encode($db_dtrip[0]); exit;
	}else{
		$returnArr = "1";
		echo  json_encode($returnArr); exit;
	}
}
?>
