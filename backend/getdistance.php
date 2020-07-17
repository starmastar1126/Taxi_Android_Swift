<?php 
include_once('common.php');
include_once('generalFunctions.php');
$Source_point_Address = array("23.0734262","72.6243823");
checkDriverAirpotLocation($Source_point_Address,"31");
function checkDriverAirpotLocation($Source_point_Address, $iDriverId) {
	global $generalobj,$obj;
	$returnArr = array();
	/*$sql = "SELECT ls.fFlatfare,lm1.vLocationName as vFromname,lm2.vLocationName as vToname, lm1.tLatitude as fromlat, lm1.tLongitude as fromlong, lm2.tLatitude as tolat, lm2.tLongitude as tolong FROM `location_wise_fare` ls left join location_master lm1 on ls.iToLocationId = lm1.iLocationId left join location_master lm2 on ls.iFromLocationId = lm2.iLocationId  UNION ALL
          SELECT ls.fFlatfare,lm1.vLocationName as vToname,lm2.vLocationName as vFromname, lm1.tLatitude as tolat, lm1.tLongitude as tolong, lm2.tLatitude as fromlat, lm2.tLongitude as fromlong FROM `location_wise_fare` ls left join location_master lm1 on ls.iFromLocationId = lm1.iLocationId left join location_master lm2 on ls.iToLocationId = lm2.iLocationId
          WHERE lm1.eFor = 'FixFare' and lm1.eStatus = 'Active'";*/
  $sql = "SELECT lm1.iLocationId,lm1.vLocationName as vFromname,lm1.tLatitude as fromlat,lm1.tLongitude as fromlong FROM location_master lm1 WHERE lm1.eFor = 'Airport' AND lm1.eStatus = 'Active'";        
	$location_data = $obj->MySQLSelect($sql);
	//echo"<pre>";
	//print_r($location_data);die;
	$polygon = array();
	foreach ($location_data as $key => $value) {
	$fromlat = explode(",",$value['fromlat']);
	$fromlong = explode(",",$value['fromlong']);
		for ($x = 0; $x < count($fromlat); $x++) {
			if(!empty($fromlat[$x]) || !empty($fromlong[$x])) {
				$from_polygon[$key][] = array($fromlat[$x],$fromlong[$x]);
			}
		}	
		if(!empty($Source_point_Address)) {
			if(!empty($from_polygon[$key])) {
/*				print_r($from_polygon[$key]);
				echo"<br/>";*/
				$from_source_addresss = contains($Source_point_Address,$from_polygon[$key]) ? 'IN' : 'OUT';
				if($from_source_addresss == "IN") {
					$returnArr['iLocationId']=$location_data[$key]['iLocationId'];
					$returnArr['vFromname'] = $location_data[$key]['vFromname'];
					//return $returnArr;
				}
			}
		}
	} 
	if(empty($returnArr)) {
		$returnArr['iLocationId']=0;
		$returnArr['vFromname']="";
	}	
	echo "<pre>";print_r($returnArr);die;
	return $returnArr;
}


$milliseconds = time();
$number = 1021;
$len = 4-strlen($number);
$newstring = substr($milliseconds,0,$len);
echo $newstring = $number.$newstring;
$str .= 'pr' . $newstring;
exit;


//getVehicleCountryUnit_PricePerKm1("93","2");
function getVehicleCountryUnit_PricePerKm1($vehicleTypeID,$fPricePerKM){
    global $generalobj,$obj;
    
    $iCountryId = get_value("vehicle_type", "iCountryId", "iVehicleTypeId", $vehicleTypeID, '', 'true');
    if($iCountryId == "-1"){
       $eUnit = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
    }else{
       $eUnit = get_value("country", "eUnit", "iCountryId", $iCountryId, '', 'true');
    }
    
    if($eUnit == "" || $eUnit == NULL){
        $eUnit = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
    }
    
    if($eUnit == "Miles"){
       $PricePerKM = $fPricePerKM * 1.60934; 
    }else{
       $PricePerKM = $fPricePerKM;
    }
    echo $PricePerKM;exit;
    return  $PricePerKM;
    
}

//getMemberCountryUnit1("17");
function getMemberCountryUnit1($iMemberId,$UserType="Passenger"){
    global $generalobj,$obj;
                    
    if ($UserType == "Passenger") {
        $tblname = "register_user";
        $vCountryfield = "vCountry";
        $iUserId = "iUserId";
    } else {
        $tblname = "register_driver";
        $vCountryfield = "vCountry";
        $iUserId = "iDriverId";
    }        
    $vCountry = get_value($tblname, $vCountryfield, $iUserId, $iMemberId, '', 'true');    
               
    if($vCountry == "" || $vCountry == NULL){
        $vCountryCode = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
    }else{
        $vCountryCode = get_value("country", "eUnit", "vCountryCode", $vCountry, '', 'true');
    }
    echo $vCountryCode;exit;
    return $vCountryCode;
}


$vWorkLocationRadius = "14";
$radusArr = array(5,10,15);
if(!in_array($vWorkLocationRadius,$radusArr)){
   array_push($radusArr,$vWorkLocationRadius);
}

echo "<pre>";print_r($radusArr);exit;

?>