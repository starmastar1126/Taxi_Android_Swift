<?php 
include_once("../common.php");
if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$company_id = isset($_REQUEST['company_id'])?$_REQUEST['company_id']:'';
if(isset($company_id) && !empty($company_id)) {
	$sql = 	"select iDriverId,CONCAT(vName,' ',vLastName) AS driverName from register_driver WHERE eStatus != 'Deleted' AND iCompanyId = '".$company_id."' ";
	$db_drivers = $obj->MySQLSelect($sql);
	echo "<option value=''>Select Driver</option>";
	foreach($db_drivers as $dbd) { 
	   echo "<option value='".$dbd["iDriverId"]."'>".$generalobjAdmin->clearName($dbd['driverName'])."</option>";
	}
} else {
	$sql = 	"select iDriverId,CONCAT(vName,' ',vLastName) AS driverName from register_driver WHERE eStatus != 'Deleted'";
	$db_drivers = $obj->MySQLSelect($sql);
	echo "<option value=''>Select Driver</option>";
	foreach($db_drivers as $dbd) { 
		echo "<option value='".$dbd["iDriverId"]."'>".$generalobjAdmin->clearName($dbd['driverName'])."</option>";
	}
}