<?php 
include_once('../common.php');

if(!isset($generalobjAdmin)){
	require_once(TPATH_CLASS."class.general_admin.php");
	$generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();  //check related map issue. e.g. dispatcher login.

/* START COUNT QUERY */
$sql = "select count(iDriverId) AS ONLINE FROM register_driver WHERE vLatitude !='' AND vLongitude !='' AND vAvailability = 'Available' ";
$db_records_online = $obj->MySQLSelect($sql);
$sql = "select count(iDriverId) AS OFFLINE FROM register_driver WHERE vLatitude !='' AND vLongitude !='' AND vAvailability = 'Not Available' ";
$db_records_offline = $obj->MySQLSelect($sql);
#echo "<pre>"; print_r($db_records_online );echo "</pre>";
$sql = "select iDriverId,tLastOnline,vAvailability,vTripStatus FROM register_driver WHERE vLatitude !='' AND vLongitude !='' ";
$db_total_driver = $obj->MySQLSelect($sql);
#echo "<pre>"; print_r($db_total_driver );echo "</pre>";exit;
$tot_online = 0;
$tot_ofline = 0;
$tot_ontrip = 0;
for($ji=0;$ji<count($db_total_driver);$ji++){
   $curtime = time();  
   $last_driver_online_time = strtotime($db_total_driver[$ji]['tLastOnline']);   
   $online_time_difference = $curtime-$last_driver_online_time;  
   if($online_time_difference <= 300 && $db_total_driver[$ji]['vAvailability'] == "Available"){
      $tot_online = $tot_online+1;
   }else{
      $vTripStatus = $db_total_driver[$ji]['vTripStatus'];
      if($vTripStatus == 'Active' || $vTripStatus == 'On Going Trip' || $vTripStatus == 'Arrived'){
         $tot_ontrip = $tot_ontrip+1;
      }else{
         $tot_ofline = $tot_ofline+1;
      }
   } 
}  
$newStatus['ONLINE'] = $tot_online;
$newStatus['OFFLINE'] = $tot_ofline;
$newStatus['ONTRIP'] = $tot_ontrip;
$newStatus['All'] = $tot_online+$tot_ofline+$tot_ontrip;
#echo date("Y-m-d H:i:s"); echo "<br/>";
#echo $tot_online;echo "<br/>";
#echo $tot_ofline;echo "<br/>";  exit;

/* END COUNT QUERY */
function getaddress($lat,$lng)
{
   $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false&key='.$GOOGLE_SEVER_API_KEY_WEB;
   $json = @file_get_contents($url);
   $data=json_decode($json);
   $status = $data->status;
   if($status=="OK")
   {
     return $data->results[0]->formatted_address;
   }
   else
   {
     return "Address Not Found";
   }
}

//echo "<pre>"; print_r($_SESSION);echo "</pre>";
if(isset($_REQUEST['type']) && $_REQUEST['type'] != '')
{
	if($_REQUEST['type'] == 'online' )
		//$tsql = " AND vAvailability = 'Available'";
    $tsql ="";
	else if($_REQUEST['type'] == 'offline' )
		//$tsql = " AND vAvailability = 'Not Available'";
    $tsql ="";
	else
		$tsql ="";
}
$tsql .= " AND eStatus='Active'";

$sql = "SELECT iDriverId,iCompanyId, CONCAT(vName,' ',vLastName) AS FULLNAME,vLatitude,vLongitude,vServiceLoc,vAvailability,vTripStatus,tLastOnline
							FROM register_driver
								WHERE vLatitude !='' AND vLongitude !='' $tsql ";
$db_records = $obj->MySQLSelect($sql);

for($i=0;$i<count($db_records);$i++){
   $time = time();  
   $last_online_time = strtotime($db_records[$i]['tLastOnline']);
   $time_difference = $time-$last_online_time;
   if($time_difference <= 300 && $db_records[$i]['vAvailability'] == "Available"){
      $db_records[$i]['vAvailability'] = "Available";
   }else{
      //$db_records[$i]['vAvailability'] = "Not Available";
      $vTripStatus = $db_records[$i]['vTripStatus'];
      if($vTripStatus == 'Active' || $vTripStatus == 'On Going Trip' || $vTripStatus == 'Arrived'){
         //$tot_ontrip = $tot_ontrip+1;
         $db_records[$i]['vAvailability'] = "Ontrip";
      }else{
         //$tot_ofline = $tot_ofline+1;
         $db_records[$i]['vAvailability'] = "Not Available";
      }
   } 
   $db_records[$i]['vServiceLoc'] = getaddress($db_records[$i]['vLatitude'],$db_records[$i]['vLongitude']);
}
#echo "<pre>";print_r($db_records);exit;
#echo "<pre>"; print_r($db_records);echo "</pre>";
$locations = array();

#marker Add
if($_REQUEST['type'] == ''){
  foreach ($db_records as $key => $value) {   
  	$locations[] = array(
  		'google_map' => array(
  			'lat' => $value['vLatitude'],
  			'lng' => $value['vLongitude'],
  		),
  		'location_address' => $value['vServiceLoc'],
  		'location_name'    => $generalobjAdmin->clearName($value['FULLNAME']),
  		'location_online_status'    => $value['vAvailability'],
  	);
  }    
}else if($_REQUEST['type'] == 'online'){
  foreach ($db_records as $key => $value) {
    if($value['vAvailability'] == "Available"){ 
    	$locations[] = array(
    		'google_map' => array(
    			'lat' => $value['vLatitude'],
    			'lng' => $value['vLongitude'],
    		),
    		'location_address' => $value['vServiceLoc'],
    		'location_name'    => $generalobjAdmin->clearName($value['FULLNAME']),
    		'location_online_status'    => $value['vAvailability'],
    	);
    }  
  }
}else if($_REQUEST['type'] == 'enroute'){
  foreach ($db_records as $key => $value) {
    if($value['vAvailability'] == "Ontrip"){ 
    	$locations[] = array(
    		'google_map' => array(
    			'lat' => $value['vLatitude'],
    			'lng' => $value['vLongitude'],
    		),
    		'location_address' => $value['vServiceLoc'],
    		'location_name'    => $generalobjAdmin->clearName($value['FULLNAME']),
    		'location_online_status'    => $value['vAvailability'],
    	);
    }  
  }
}else{
  foreach ($db_records as $key => $value) {
    if($value['vAvailability'] == "Not Available"){ 
    	$locations[] = array(
    		'google_map' => array(
    			'lat' => $value['vLatitude'],
    			'lng' => $value['vLongitude'],
    		),
    		'location_address' => $value['vServiceLoc'],
    		'location_name'    => $generalobjAdmin->clearName($value['FULLNAME']),
    		'location_online_status'    => $value['vAvailability'],
    	);
    }  
  }
}

$returnArr['Action'] = "0";
$returnArr['locations'] = $locations;
$returnArr['db_records'] = $db_records;
$returnArr['newStatus'] = isset($newStatus)?$newStatus:'';
echo json_encode($returnArr);exit;
?>