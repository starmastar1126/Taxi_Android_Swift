<?php 
//echo "here";
include '../common.php';
$eUnit = isset($_REQUEST['eUnit']) ? $_REQUEST['eUnit'] : '';
if($eUnit == 'yes'){
	$sql = "select vPhoneCode,eUnit,vCountryCode,vTimeZone from country where vCountryCode = '".$_REQUEST['id']."' OR iCountryId = '".$_REQUEST['id']."'";
	$db_data = $obj->MySQLSelect($sql);
	echo json_encode($db_data[0]);
}else {
	$sql = "select vPhoneCode from country where vCountryCode = '".$_REQUEST['id']."'";
	$db_data = $obj->MySQLSelect($sql);
	echo $db_data[0]['vPhoneCode'];
	exit;
}
?>