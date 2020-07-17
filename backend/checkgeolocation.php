<?php 
include_once("common.php");	
//https://stackoverflow.com/questions/14087116/extract-address-from-string
//http://nominatim.openstreetmap.org/reverse?format=json&lat=23.016310&lon=72.505042&zoom=18&addressdetails=1
$PickUpAddress = "SG Hwy Service Rd, Prahlad Nagar, Ahmedabad, Gujarat 380015, India";
$DropOffAddress = "SG Hwy Service Rd, Prahlad Nagar, Ahmedabad, Gujarat 380015, India";

//https://www.google.co.in/maps/place/SG+Hwy+Service+Rd,+Prahlad+Nagar,+Ahmedabad,+Gujarat+380015/@23.0149581,72.5017284,17z/data=!3m1!4b1!4m5!3m4!1s0x395e9b26d505429f:0xef12dd24af7a1e15!8m2!3d23.0149581!4d72.5039171
$passengerLat = "23.2267841";
$passengerLon = "72.6575624";
//getcountrycitystatefromaddress($address);
$sourceLocationArr =array($passengerLat,$passengerLon);
//echo $allowed_ans = checkRestrictedAreaNew($sourceLocationArr,"No");
echo $allowed_ans = GetVehicleTypeFromGeoLocation($sourceLocationArr);
//$address_data['CheckAddress'] = $DropOffAddress;
//$allowed_ans_drop = checkRestrictedAreaNew($address_data,"Yes");

   function GetVehicleTypeFromGeoLocation($Address_Array){
		global $generalobj, $obj;
		
    $Vehicle_Str = "-1";     
    if(!empty($Address_Array)){
			$sqlaa = "SELECT * FROM location_master WHERE eStatus='Active' AND eFor = 'VehicleType'";
			$allowed_data = $obj->MySQLSelect($sqlaa);   
			if(!empty($allowed_data)){
				$polygon = array();
				foreach($allowed_data as $key => $val) {
					$latitude = explode(",",$val['tLatitude']);
					$longitude = explode(",",$val['tLongitude']);
					for ($x = 0; $x < count($latitude); $x++) {
						if(!empty($latitude[$x]) || !empty($longitude[$x])) {
							$polygon[$key][] = array($latitude[$x],$longitude[$x]);
						}
					}
					//print_r($polygon[$key]);
					if($polygon[$key]){
						
            $address = contains($Address_Array,$polygon[$key]) ? 'IN' : 'OUT';
						if($address == 'IN'){
							$Vehicle_Str .= ",".$val['iLocationId'];
              //break;
						}
					}
				}    
			} 
		}     
		return $Vehicle_Str;
	}

	function checkRestrictedAreaNew($Address_Array,$DropOff) {
  		//print_r($Address_Array);die;
		global $generalobj, $obj;
		$ssql = "";
		if($DropOff == "No") {
			$ssql.= " AND (eRestrictType = 'Pick Up' OR eRestrictType = 'All')";
		} else {
			$ssql.= " AND (eRestrictType = 'Drop Off' OR eRestrictType = 'All')";
		}
		if(!empty($Address_Array)){
			$sqlaa = "SELECT rs.iLocationId,lm.vLocationName,lm.tLatitude,lm.tLongitude FROM `restricted_negative_area` AS rs LEFT JOIN location_master as lm ON lm.iLocationId = rs.iLocationId WHERE rs.eStatus='Active' AND lm.eFor = 'Restrict' AND eType='Allowed'".$ssql;
			$allowed_data = $obj->MySQLSelect($sqlaa);
			$allowed_ans = 'No';
			if(!empty($allowed_data)){
				$polygon = array();
				foreach($allowed_data as $key => $val) {
					$latitude = explode(",",$val['tLatitude']);
					$longitude = explode(",",$val['tLongitude']);
					for ($x = 0; $x < count($latitude); $x++) {
						if(!empty($latitude[$x]) || !empty($longitude[$x])) {
							$polygon[$key][] = array($latitude[$x],$longitude[$x]);
						}
					}
					//print_r($polygon[$key]);
					if($polygon[$key]){
						$address = contains($Address_Array,$polygon[$key]) ? 'IN' : 'OUT';
						if($address == 'IN'){
							$allowed_ans = 'Yes';
							break;
						}
					}
				}
			} 

			if($allowed_ans == 'No') {
				$sqlas = "SELECT rs.iLocationId,lm.vLocationName,lm.tLatitude,lm.tLongitude FROM `restricted_negative_area` AS rs LEFT JOIN location_master as lm ON lm.iLocationId = rs.iLocationId WHERE rs.eStatus='Active' AND lm.eFor = 'Restrict' AND eType='Disallowed'".$ssql;
				$restricted_data = $obj->MySQLSelect($sqlas);
				$allowed_ans = 'Yes';
				if(!empty($restricted_data)){
					$polygon_dis = array();
					foreach($restricted_data as $key => $value){
						$latitude = explode(",",$value['tLatitude']);
						$longitude = explode(",",$value['tLongitude']);
						for ($x = 0; $x < count($latitude); $x++) {
							if(!empty($latitude[$x]) || !empty($longitude[$x])) {
								$polygon_dis[$key][] = array($latitude[$x],$longitude[$x]);
							}
						}
						if($polygon_dis[$key]){
							$address_dis = contains($Address_Array,$polygon_dis[$key]) ? 'IN' : 'OUT';
							if($address_dis == 'IN') {
								$allowed_ans = 'No';
								break;
							} 
						}
					}
				}
			}  
		}
		return $allowed_ans;
	}


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
/*
https://stackoverflow.com/questions/27535809/extracting-city-and-zipcode-from-string-in-php
$array = array();
$array[0] = "123 Main Street, New Haven, CT 06518";
$array[1] = "123 Main Street, New Haven, CT";
$array[2] = "123 Main Street, New Haven,                            CT 06511";
$array[3] = "New Haven,CT 66554, United States";
$array[4] = "New Haven, CT06513";
$array[5] = "06513";
$array[6] = "123 Main    Street, New Haven CT 06518, united states";

$array[7] = "1253 McGill College, Montreal, QC H3B 2Y5"; // google Montreal  / Canada
$array[8] = "1600 Amphitheatre Parkway, Mountain View, CA 94043"; // google CA  / US
$array[9] = "20 West Kinzie St., Chicago, IL 60654"; // google IL / US
$array[10] = "405 Rue Sainte-Catherine Est, Montreal, QC"; // Montreal address shows hyphened street names
$array[11] = "48 Pirrama Road, Pyrmont, NSW 2009"; // google Australia
*/
?>
