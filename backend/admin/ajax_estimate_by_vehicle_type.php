<?php 
include_once("../common.php");

$vehicleId = isset($_REQUEST['vehicleId'])?$_REQUEST['vehicleId']:'';
$varfrom = isset($_REQUEST['varfrom'])?$_REQUEST['varfrom']:'';
$booking_date = isset($_REQUEST['booking_date'])?$_REQUEST['booking_date']:'';
$vCountry = isset($_REQUEST['vCountry'])?$_REQUEST['vCountry']:'';
$FromLatLong = isset($_REQUEST['FromLatLong'])?$_REQUEST['FromLatLong']:'';
$ToLatLong = isset($_REQUEST['ToLatLong'])?$_REQUEST['ToLatLong']:'';
if(!empty($FromLatLong) && !empty($ToLatLong)){
	$pickUpLatLong = explode(",", $FromLatLong);
	$dropoffLatLong = explode(",", $ToLatLong);
	$pickuplocationarr = array($pickUpLatLong[0],$pickUpLatLong[1]);
	$dropofflocationarr = array($dropoffLatLong[0],$dropoffLatLong[1]);
}

if($booking_date == "")
{
	$booking_date = date("Y-m-d H:i:s");
}
function clean($str) {
global $obj;
$str = trim($str);
	$str = $obj->SqlEscapeString($str);
	$str = htmlspecialchars($str);
	$str = strip_tags($str);
	return($str);
} 

function getVehicleCountryUnit_PricePerKm($vehicleTypeID,$fPricePerKM){
	global $generalobj,$obj,$DEFAULT_DISTANCE_UNIT,$vCountry;
	$iLocationid = get_value("vehicle_type", "iLocationid", "iVehicleTypeId", $vehicleTypeID, '', 'true');
	$eUnit = get_value("country", "eUnit", "vCountryCode", $vCountry, '', 'true');
	
	/* if($iLocationid == "-1"){
		$eUnit = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
	} else {
		$iCountryId = get_value("location_master", "iCountryId", "iLocationid", $iLocationid, '', 'true');
		$eUnit = get_value("country", "eUnit", "iCountryId", $iCountryId, '', 'true');
	} */
	
	if($eUnit == "" || $eUnit == NULL){
		$eUnit = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
	}

	$PricePerKM = $fPricePerKM;
	if($iLocationid == "-1"){
		if($eUnit != $DEFAULT_DISTANCE_UNIT){
			if($eUnit == "KMs"){
				$PricePerKM = $fPricePerKM * 0.621371;
			}else if($eUnit == "Miles"){
				$PricePerKM = $fPricePerKM / 0.621371 ;
			}
		}
	}

	return round($PricePerKM,2);
}
	
function get_value($table, $field_name, $condition_field = '', $condition_value = '', $setParams = '', $directValue = '') {
	global $obj;
	$returnValue = array();
	
	$where = ($condition_field != '') ? ' WHERE ' . clean($condition_field) : '';
	$where .= ($where != '' && $condition_value != '') ? ' = "' . clean($condition_value) . '"' : '';
	
	if ($table != '' && $field_name != '' && $where != '') {
		$sql = "SELECT $field_name FROM  $table $where";
		if ($setParams != '') {
			$sql .= $setParams;
		}
		$returnValue = $obj->MySQLSelect($sql);
		} else if ($table != '' && $field_name != '') {
		$sql = "SELECT $field_name FROM  $table";
		if ($setParams != '') {
			$sql .= $setParams;
		}
		$returnValue = $obj->MySQLSelect($sql);
	}
	if ($directValue == '') {
		return $returnValue;
		} else {
		$temp = $returnValue[0][$field_name];
		return $temp;
	}
}

############### Check FlatTrip Or Not  ###################################################################
function checkFlatTripnew($Source_point_Address, $Destination_point_Address,$iVehicleTypeId) {
	global $generalobj,$obj;
	$returnArr = array();
	/*$sql = "SELECT ls.fFlatfare,lm1.vLocationName as vFromname,lm2.vLocationName as vToname, lm1.tLatitude as fromlat, lm1.tLongitude as fromlong, lm2.tLatitude as tolat, lm2.tLongitude as tolong FROM `location_wise_fare` ls left join location_master lm1 on ls.iToLocationId = lm1.iLocationId left join location_master lm2 on ls.iFromLocationId = lm2.iLocationId  UNION ALL
          SELECT ls.fFlatfare,lm1.vLocationName as vToname,lm2.vLocationName as vFromname, lm1.tLatitude as tolat, lm1.tLongitude as tolong, lm2.tLatitude as fromlat, lm2.tLongitude as fromlong FROM `location_wise_fare` ls left join location_master lm1 on ls.iFromLocationId = lm1.iLocationId left join location_master lm2 on ls.iToLocationId = lm2.iLocationId
          WHERE lm1.eFor = 'FixFare' and lm1.eStatus = 'Active'";*/
  $sql = "SELECT ls.fFlatfare,lm1.vLocationName as vFromname,lm2.vLocationName as vToname, lm1.tLatitude as fromlat, lm1.tLongitude as fromlong, lm2.tLatitude as tolat, lm2.tLongitude as tolong FROM `location_wise_fare` ls left join location_master lm1 on ls.iFromLocationId = lm1.iLocationId left join location_master lm2 on ls.iToLocationId = lm2.iLocationId WHERE lm1.eFor = 'FixFare' AND lm1.eStatus = 'Active' AND ls.eStatus = 'Active' AND ls.iVehicleTypeId = '".$iVehicleTypeId."'";        
	$location_data = $obj->MySQLSelect($sql);
	//echo"<pre>";
	//print_r($location_data);die;
	$polygon = array();
	foreach ($location_data as $key => $value) {
	$fromlat = explode(",",$value['fromlat']);
	$fromlong = explode(",",$value['fromlong']);
	$tolat = explode(",",$value['tolat']);
	$tolong = explode(",",$value['tolong']);
		for ($x = 0; $x < count($fromlat); $x++) {
			if(!empty($fromlat[$x]) || !empty($fromlong[$x])) {
				$from_polygon[$key][] = array($fromlat[$x],$fromlong[$x]);
			}
		}	
		for ($y = 0; $y < count($tolat); $y++) {
			if(!empty($tolat[$y]) || !empty($tolong[$y])) {
				$to_polygon[$key][] = array($tolat[$y],$tolong[$y]);
			}
		}
		if(!empty($Source_point_Address) && !empty($Destination_point_Address)) {
			if(!empty($from_polygon[$key]) && !empty($to_polygon[$key])) {
/*				print_r($from_polygon[$key]);
				echo"<br/>";*/
				$from_source_addresss = contains($Source_point_Address,$from_polygon[$key]) ? 'IN' : 'OUT';
				$to_source_addresss = contains($Destination_point_Address,$to_polygon[$key]) ? 'IN' : 'OUT';
/*				echo"<br/>";
				print_r($to_polygon[$key]);
				echo"<br/>";*/
				$to_dest_addresss = contains($Destination_point_Address,$to_polygon[$key])? 'IN' : 'OUT';
				$from_dest_addresss = contains($Source_point_Address,$from_polygon[$key])? 'IN' : 'OUT';
				if(($from_source_addresss == "IN" && $to_source_addresss == "IN") || ($to_dest_addresss == "IN" && $from_dest_addresss == "IN")) {
					$returnArr['Flatfare']=$location_data[$key]['fFlatfare'];
					$returnArr['eFlatTrip'] = "Yes";
					return $returnArr;
				}
			}
		}
	} 
	if(empty($returnArr)) {
		$returnArr['eFlatTrip']="No";
		$returnArr['Flatfare']=0;
	}	
	//print_r($returnArr);
	// die;
	return $returnArr;
}

############### Check FlatTrip Or Not  ###################################################################

function contains($point, $polygon)
{
    if($polygon[0] != $polygon[count($polygon)-1])
        $polygon[count($polygon)] = $polygon[0];
    $j = 0;
    $oddNodes = false;
    $x = $point[1];
    $y = $point[0];
    $n = count($polygon);
    for ($i = 0; $i < $n; $i++)
    {
        $j++;
        if ($j == $n)
        {
            $j = 0;
        }
        if ((($polygon[$i][0] < $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] < $y) && ($polygon[$i][0] >=
            $y)))
        {
            if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -
                $polygon[$i][1]) < $x)
            {
                $oddNodes = !$oddNodes;
            }
        }
    }
    return $oddNodes;
}

if($vehicleId != '' && $booking_date != "")
{
	global $generalobj;
	$fPickUpPrice = "1";
	$fNightPrice = "1";
	$surgeprice = "1";
	$surgetype = "None";
	 ## Checking For Flat Trip ##
	if(!empty($pickuplocationarr) && !empty($dropofflocationarr)){
		   $data_flattrip = checkFlatTripnew($pickuplocationarr,$dropofflocationarr,$vehicleId);
		   $eFlatTrip = $data_flattrip['eFlatTrip']; 
		   $fFlatTripPrice = $data_flattrip['Flatfare'];
	} else {
        $eFlatTrip = "No"; 
        $fFlatTripPrice = 0;
	}
	## Checking For Flat Trip ##

	$Data=$generalobj->checkSurgePrice($vehicleId,$booking_date);
	

	if($Data['Action'] != "1"){
		$fPickUpPrice = $Data['fPickUpPrice'];
		$fNightPrice = $Data['fNightPrice'];
		$surgeprice = $Data['surgeprice'];
		$surgetype = $Data['surgetype'];
		if($surgetype == "PickUp"){
			// $returnArr['PickStartTime'] = $Data['pickStartTime'];
			// $returnArr['PickEndTime'] = $Data['pickEndTime'];
			$returnArr['Time'] = $Data['pickStartTime']." To ".$Data['pickEndTime'];
		}else if($surgetype == "Night"){
			// $returnArr['NightStartTime'] = $Data['nightStartTime'];
			// $returnArr['NightEndTime'] = $Data['nightEndTime'];
			$returnArr['Time'] = "From ".$Data['nightStartTime']." To ".$Data['nightEndTime'];
		}
	}
	
	
	$sql = "select iBaseFare,fPricePerKM,fPricePerMin,iMinFare from vehicle_type where iVehicleTypeId = '".$vehicleId."' LIMIT 1";
	$db_model = $obj->MySQLSelect($sql);
	
	// echo "<pre>";print_r($db_model);exit;
	$APPLY_SURGE_ON_FLAT_FARE = $generalobj->getConfigurations("configurations","APPLY_SURGE_ON_FLAT_FARE");
	if($APPLY_SURGE_ON_FLAT_FARE == "No" && $data_flattrip['eFlatTrip'] == "Yes"){
      	$fPickUpPrice = 1;
		$fNightPrice = 1;
		$surgeprice = 1;
    }
	
	$returnArr['iBaseFare'] = $db_model[0]['iBaseFare'];
	$returnArr['fPricePerKM'] = getVehicleCountryUnit_PricePerKm($vehicleId,$db_model[0]['fPricePerKM']);
	$returnArr['fPricePerMin'] = $db_model[0]['fPricePerMin'];
	$returnArr['iMinFare'] = $db_model[0]['iMinFare'];
	$returnArr['iBaseFare'] = $db_model[0]['iBaseFare'];
	$returnArr['fPickUpPrice'] = $fPickUpPrice;
	$returnArr['fNightPrice'] = $fNightPrice;
	$returnArr['fSurgePrice'] = $surgeprice;
	$returnArr['SurgeType'] = $surgetype;
	$returnArr['eFlatTrip']=$eFlatTrip; 
    $returnArr['fFlatTripPrice']=$fFlatTripPrice;
	
	//echo "<pre>";print_r($returnArr);exit;
    echo json_encode($returnArr); exit;
}
?>