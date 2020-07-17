<?php 
include_once('../common.php');

if(!isset($generalobjAdmin)){
	require_once(TPATH_CLASS."class.general_admin.php");
	$generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
$iVehicleTypeId = isset($_REQUEST['iVehicleTypeId']) ? $_REQUEST['iVehicleTypeId'] : '';
$vCountry = isset($_REQUEST['vCountry']) ? $_REQUEST['vCountry'] : '';
$dBooking_date = isset($_REQUEST['dBooking_date']) ? $_REQUEST['dBooking_date'] : '';

$ssql = " AND rd.eStatus='Active'";
if($keyword != "") {
	$ssql .= " AND CONCAT(rd.vName,' ',rd.vLastName) like '%$keyword%'";
}

$eLadiesRide = isset($_REQUEST['eLadiesRide']) ? $_REQUEST['eLadiesRide'] : '';
$eHandicaps = isset($_REQUEST['eHandicaps']) ? $_REQUEST['eHandicaps'] : '';

if($eLadiesRide == 'Yes'){
	$ssql .= " AND (rd.eFemaleOnlyReqAccept = 'Yes' OR rd.eGender = 'Female')";
}

if($eHandicaps == 'Yes'){
	$ssql .= " AND dv.eHandiCapAccessibility = 'Yes'";
}

if(!empty($vCountry)){
	$ssql .= " AND rd.vCountry LIKE '".$vCountry."'";
}

if($APP_TYPE == "UberX" && !empty($dBooking_date)){
	$vday =  date('l', strtotime($dBooking_date));
	$curr_hour = date('H', strtotime($dBooking_date));


	$next_hour = $curr_hour + 01;

	if($curr_hour == "00"){
		$curr_hour = "12";
		$next_hour = "01";
	}

	$selected_time = $curr_hour."-".$next_hour;
	$ssql .= "AND vDay LIKE '%".$vday."%' AND dmt.vAvailableTimes LIKE '%".$selected_time."%'";
}
if($APP_TYPE == "UberX"){
	$sql = "SELECT rd.iDriverId,rd.vEmail,rd.iCompanyId, CONCAT(rd.vName,' ',rd.vLastName) AS FULLNAME,rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline, rd.vImage, rd.vCode, rd.vPhone, dv.vCarType FROM register_driver AS rd LEFT JOIN driver_vehicle AS dv ON dv.iDriverVehicleId=rd.iDriverVehicleId RIGHT JOIN driver_manage_timing  AS dmt ON rd.iDriverId = dmt.iDriverId  WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql." GROUP BY dmt.iDriverId";
} else {
	$sql = "SELECT rd.iDriverId,rd.vEmail,rd.iCompanyId, CONCAT(rd.vName,' ',rd.vLastName) AS FULLNAME,rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline, rd.vImage, rd.vCode, rd.vPhone, dv.vCarType FROM register_driver AS rd LEFT JOIN driver_vehicle AS dv ON dv.iDriverVehicleId=rd.iDriverVehicleId WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql;
}

$db_records = $obj->MySQLSelect($sql);

// echo "<pre>"; 
// print_r($db_records); die;

/* if($COMMISION_DEDUCT_ENABLE == 'Yes') {
	$j=0;
	for($i=0;$i<count($db_records);$i++){
		$user_available_balance = $generalobj->get_user_available_balance($db_records[$i]['iDriverId'],"Driver");
		if($user_available_balance > $WALLET_MIN_BALANCE){
			$db_records_new[$j] = $db_records[$i];
			$db_records_new[$j]['user_available_balance'] = $user_available_balance;
			$j++;
		}
	}
	$db_records = $db_records_new;
} */
// echo "<pre>";print_r($db_records); die;

$dbDrivers = array();
for($i=0;$i<count($db_records);$i++){
	$newArray = array();
	$newArray = explode(',',$db_records[$i]['vCarType']);
	if($iVehicleTypeId == '' || (!empty($newArray) && in_array($iVehicleTypeId,$newArray))) {
	if ($db_records[$i]['vImage'] != 'NONE' && $db_records[$i]['vImage'] != '' && file_exists($tconfig["tsite_upload_images_driver_path"]. '/' . $db_records[$i]['iDriverId'] . '/2_'.$db_records[$i]['vImage'])) { 
		$DriverImage = $tconfig["tsite_upload_images_driver"]. '/' . $db_records[$i]['iDriverId'] . '/2_'.$db_records[$i]['vImage'];
	}else{
		$DriverImage = $tconfig["tsite_url"]."assets/img/profile-user-img.png";
	}
	$db_records[$i]['vImageDriver'] = $DriverImage;
	$time = time();
	$last_online_time = strtotime($db_records[$i]['tLastOnline']);
	$time_difference = $time-$last_online_time;
	if($db_records[$i]['vAvailability'] == "Available"){
		$db_records[$i]['vAvailability'] = "Available";
		$dbDrivers[$i] = $db_records[$i];
	}else{
	 $vTripStatus = $db_records[$i]['vTripStatus'];
	  if($vTripStatus == 'Active' || $vTripStatus == 'On Going Trip' || $vTripStatus == 'Arrived'){
		 $db_records[$i]['vAvailability'] = $vTripStatus;
	  }else{
		$db_records[$i]['vAvailability'] = "Not Available";
	  }
	  $dbDrivers[$i] = $db_records[$i];
	}
	}
}
// echo "<pre>";
// print_r($dbDrivers); die;
#marker Add
$con = "";
foreach ($dbDrivers as $key => $value) {
		if($value['vAvailability'] == "Available") {
			$statusIcon = "../assets/img/green-icon.png";
		}else if($value['vAvailability'] == "Active") {
			$statusIcon = "../assets/img/red.png";
		}else if($value['vAvailability'] == "On Going Trip") {
			$statusIcon = "../assets/img/yellow.png";
		}else if($value['vAvailability'] == "Arrived"){
			$statusIcon = "../assets/img/blue.png";
		}else {
			$statusIcon = "../assets/img/offline-icon.png";
		}
		$con .= '<li onclick="showPopupDriver('.$value['iDriverId'].');"><label class="map-tab-img"><label class="map-tab-img1"><img src="'.$value['vImageDriver'].'"></label><img src="'.$statusIcon.'"></label><p class="driver_'.$value['iDriverId'].'">'.$generalobjAdmin->clearName($value['FULLNAME']).' <b>+'.$generalobjAdmin->clearPhone($value['vCode'].$value['vPhone']).'</b></p><a href="javascript:void(0)" class="btn btn-success assign-driverbtn" onClick=\'checkUserBalance('.$value['iDriverId'].');\'>'.$langage_lbl_admin['LBL_ASSIGN_DRIVER_BUTTON'].'</a></li>';
}
echo $con; exit;
?>