<?php 
include_once("../common.php");

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$phone = isset($_REQUEST['phone'])?$_REQUEST['phone']:'';
$phoneCode = isset($_REQUEST['phoneCode'])?$_REQUEST['phoneCode']:'';
$vehicleId = isset($_REQUEST['vehicleId'])?$_REQUEST['vehicleId']:'';
if($phone != '')
{
	$phonQr = '';
	if($phoneCode != ""){ $phonQr = " AND vPhoneCode='".$phoneCode."'"; }
	$sql = "select vName,vLastName,vEmail,iUserId,eStatus from register_user where vPhone = '".$phone."' $phonQr LIMIT 1";
	$db_model = $obj->MySQLSelect($sql);
	$cont = '';
    for($i=0;$i<count($db_model);$i++){
		$cont .= $db_model[$i]['vName'].":";
		$cont .= $generalobjAdmin->clearName(" ".$db_model[$i]['vLastName']).":";
		$cont .= $db_model[$i]['vEmail'].":";
		$cont .= $db_model[$i]['iUserId'].":";
		$cont .= $db_model[$i]['eStatus'];
    }
    echo $cont; exit;
}

if(isset($_REQUEST['vehicleId'])){
	if($vehicleId != '') {
		$sql = "select iBaseFare,fPricePerKM,fPricePerMin,iMinFare from vehicle_type where iVehicleTypeId = '".$vehicleId."' LIMIT 1";
		$db_model = $obj->MySQLSelect($sql);
		$cont = '';
		for($i=0;$i<count($db_model);$i++){
			$cont .= $db_model[$i]['iBaseFare'].":";
			$cont .= $db_model[$i]['fPricePerKM'].":";
			$cont .= $db_model[$i]['fPricePerMin'].":";
			$cont .= $db_model[$i]['iMinFare'];
		}
	}else {
		$cont = '';
		$cont .= 0.00;
		$cont .= ":";
		$cont .= 0.00;
		$cont .= ":";
		$cont .= 0.00;
		$cont .= ":";
		$cont .= 0.00;
	}
	echo $cont; exit;
}
?>