<?php 
include_once('../common.php');
header('Content-Type: text/html; charset=utf-8');
if(!isset($generalobjAdmin)){
	require_once(TPATH_CLASS."class.general_admin.php");
	$generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

function fetchtripstatustimeMAXinterval(){
	global $generalobjAdmin,$FETCH_TRIP_STATUS_TIME_INTERVAL;
	
	//$FETCH_TRIP_STATUS_TIME_INTERVAL = $generalobj->getConfigurations("configurations", "FETCH_TRIP_STATUS_TIME_INTERVAL");
	$FETCH_TRIP_STATUS_TIME_INTERVAL_ARR = explode("-",$FETCH_TRIP_STATUS_TIME_INTERVAL);
	
	$FETCH_TRIP_STATUS_TIME_INTERVAL_MAX = $FETCH_TRIP_STATUS_TIME_INTERVAL_ARR[1];
	
	return $FETCH_TRIP_STATUS_TIME_INTERVAL_MAX;
}

$type = $_REQUEST['type'];
$cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + 60) / 60);
$str_date = @date('Y-m-d H:i:s', strtotime('-'.$cmpMinutes.' minutes'));

$ssql = " AND rd.eStatus='Active'";



$eLadiesRide = isset($_REQUEST['eLadiesRide']) ? $_REQUEST['eLadiesRide'] : '';
$eHandicaps = isset($_REQUEST['eHandicaps']) ? $_REQUEST['eHandicaps'] : '';

if($eLadiesRide == 'Yes'){
	$ssql .= " AND (rd.eFemaleOnlyReqAccept = 'Yes' OR rd.eGender = 'Female')";
}

if($eHandicaps == 'Yes'){
	$ssql .= " AND dv.eHandiCapAccessibility = 'Yes'";
}


$sql = "SELECT rd.iDriverId,rd.vEmail,rd.iCompanyId, rd.vLatitude,rd.vLongitude,rd.vServiceLoc,rd.vAvailability,rd.vTripStatus,rd.tLastOnline, rd.vImage, rd.vCode, rd.vPhone, dv.vCarType,rd.tLocationUpdateDate FROM register_driver AS rd LEFT JOIN driver_vehicle AS dv ON dv.iDriverVehicleId=rd.iDriverVehicleId WHERE rd.vLatitude !='' AND rd.vLongitude !='' ".$ssql;
$db_records = $obj->MySQLSelect($sql);

// echo "<pre>"; print_r($db_records); die;

for($i=0;$i<count($db_records);$i++){
	if ($db_records[$i]['vImage'] != 'NONE' && $db_records[$i]['vImage'] != '') { 
		$DriverImage = $tconfig["tsite_upload_images_driver"]. '/' . $db_records[$i]['iDriverId'] . '/2_'.$db_records[$i]['vImage'];
	}else{
		$DriverImage = $tconfig["tsite_url"]."assets/img/profile-user-img.png";
	}
	$db_records[$i]['vImageDriver'] = $DriverImage;
	$time = time();  
	$last_online_time = strtotime($db_records[$i]['tLastOnline']);
	$time_difference = $time-$last_online_time;
	$vTripStatus = $db_records[$i]['vTripStatus'];
	if($vTripStatus == 'Active'){
		$db_records[$i]['vAvailability'] = $vTripStatus;
	} else if($vTripStatus == 'Arrived') {
		$db_records[$i]['vAvailability'] = $vTripStatus;
	} else if($vTripStatus == 'On Going Trip'){
		$db_records[$i]['vAvailability'] = $vTripStatus;
	} else if($vTripStatus != 'Active' && $db_records[$i]['vAvailability'] == "Available" && $db_records[$i]['tLocationUpdateDate'] > $str_date){
		$db_records[$i]['vAvailability'] = "Available";
	} else {
		$db_records[$i]['vAvailability'] = "Not Available";
	}
	/*if($db_records[$i]['vAvailability'] == "Available"){
	  $db_records[$i]['vAvailability'] = "Available";
	}else{
	  $vTripStatus = $db_records[$i]['vTripStatus'];
	  //if($vTripStatus == 'Active' || $vTripStatus == 'On Going Trip' || $vTripStatus == 'Arrived'){
	  if($vTripStatus == 'Active' || $vTripStatus == 'On Going Trip' || $vTripStatus == 'Arrived'){
			$db_records[$i]['vAvailability'] = $vTripStatus;
	  }else{
			$db_records[$i]['vAvailability'] = "Not Available";
	  }
	}*/
	$db_records[$i]['vEmail'] = $generalobjAdmin->clearEmail($db_records[$i]['vEmail']);
	$db_records[$i]['vPhone'] = $generalobjAdmin->clearPhone($db_records[$i]['vPhone']);
}
$locations = array();
// if($type != "") {
// }
#marker Add
foreach ($db_records as $key => $value) {
	if($APP_TYPE == 'UberX') {
		if($value['vAvailability'] == "Available") {
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/male-green.png";
		}else if($value['vAvailability'] == "Active") {
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/male-red.png";
		}else if($value['vAvailability'] == "Arrived") {
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/male-blue.png";
		}else if($value['vAvailability'] == "On Going Trip"){
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/male-yellow.png";
		} else if($value['vAvailability'] == "Not Available") {
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/male-gray.png";
		} else {
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/male-gray.png";
		}
	} else {
		if($value['vAvailability'] == "Available") {
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/available.png";
		}else if($value['vAvailability'] == "Active") {
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/enroute.png";
		}else if($value['vAvailability'] == "Arrived") {
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/reached.png";
		}else if($value['vAvailability'] == "On Going Trip"){
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/started.png";
		}else if($value['vAvailability'] == "Not Available") {
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/offline.png";
		}else {
			$statusIcon = $tconfig["tsite_url"]."webimages/upload/mapmarker/offline.png";
		}
	}
  	$locations[] = array(
  		'google_map' => array(
  			'lat' => $value['vLatitude'],
  			'lng' => $value['vLongitude'],
  		),
		'location_icon' => $statusIcon,
  		'location_address' => $value['vServiceLoc'],
  		'location_image'    => $value['vImageDriver'],
  		'location_mobile'    => $generalobjAdmin->clearPhone($value['vCode'].$value['vPhone']),
  		'location_ID'    => $generalobjAdmin->clearEmail($value['vEmail']),
  		'location_type'    => $value['vAvailability'],
  		'location_online_status'    => $value['vAvailability'],
  		'location_carType'    => $value['vCarType'],
  		'location_driverId'    => $value['iDriverId'],
  	);
}

$returnArr['Action'] = "0";
$returnArr['locations'] = $locations;
$returnArr['db_records'] = $db_records;
$returnArr['newStatus'] = $newStatus;

// echo "<pre>"; print_r($returnArr); die;
echo json_encode($returnArr);exit;
?>