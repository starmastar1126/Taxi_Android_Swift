<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$countryId = isset($_REQUEST['countryId']) ? $_REQUEST['countryId'] : ''; 
$iVehicleTypeId = isset($_REQUEST['iVehicleTypeId']) ? $_REQUEST['iVehicleTypeId'] : ''; 
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : 'Ride';

$sql= "SELECT iCountryId FROM country WHERE vCountryCode = '".$countryId."'";
$countryarray = $obj->MySQLSelect($sql);
$countryid = $countryarray[0]['iCountryId'];
if ($type == 'getVehicles') {
	if($eType == "UberX") {
		$sql23 = "SELECT vt.*,vc.vCategory_EN,lm.vLocationName FROM `vehicle_type` AS vt LEFT JOIN `country` AS c ON c.iCountryId=vt.iCountryId LEFT JOIN vehicle_category as vc on vc.iVehicleCategoryId = vt.iVehicleCategoryId left join location_master as lm ON lm.iLocationId = vt.iLocationid WHERE (lm.iCountryId='".$countryid."' OR vt.iLocationid = '-1') AND vt.eType='".$eType."' AND vc.eStatus = 'Active' ORDER BY vt.iVehicleTypeId ASC";
	} else {
		$sql23 = "SELECT vt.*,lm.vLocationName FROM `vehicle_type` AS vt LEFT JOIN `country` AS c ON c.iCountryId=vt.iCountryId left join location_master as lm ON lm.iLocationId = vt.iLocationid WHERE (lm.iCountryId='".$countryid."' OR vt.iLocationid = '-1') AND vt.eType='".$eType."' ORDER BY vt.iVehicleTypeId ASC";
	}
	$db_carType = $obj->MySQLSelect($sql23);

	if($eType == "UberX") {
		echo '<option value="" >Select Service Type</option>';
	} else {
		echo '<option value="" >Select Vehicle Type</option>';
	}

	foreach ($db_carType as $db_car) {
		$selected='';
		if($db_car['iVehicleTypeId'] == $iVehicleTypeId){
			$selected = "selected=selected";
		}
		if($db_car['vLocationName'] != ''){
			$location = " (".$db_car['vLocationName'].")";
		} else {
			$location="";
		}
		if($eType == "UberX") {
			echo "<option value=".$db_car['iVehicleTypeId']." ".$selected.">".$db_car['vCategory_EN']."-".$db_car['vVehicleType'].$location."</option>";
		} else {
			echo "<option value=".$db_car['iVehicleTypeId']." ".$selected.">".$db_car['vVehicleType_EN'].$location."</option>";
		}
	}
	exit;
}
?>