<?php 
include_once("common.php");	

$PickUpAddress = "SG Hwy Service Rd, Prahlad Nagar, Ahmedabad, Gujarat 380015, India";
$DropOffAddress = "SG Hwy Service Rd, Prahlad Nagar, Ahmedabad, Gujarat 380015, India";

//https://www.google.co.in/maps/place/SG+Hwy+Service+Rd,+Prahlad+Nagar,+Ahmedabad,+Gujarat+380015/@23.0149581,72.5017284,17z/data=!3m1!4b1!4m5!3m4!1s0x395e9b26d505429f:0xef12dd24af7a1e15!8m2!3d23.0149581!4d72.5039171
$passengerLat = "22.099866";
$passengerLon = "72.531425";
//getcountrycitystatefromaddress($address);

$pickuplocationarr = array($passengerLat,$passengerLon);
echo $allowed_ans = checkAllowedAreaNew($pickuplocationarr,"No");
function checkAllowedAreaNew($Address_Array,$DropOff) {
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
			
      if(count($allowed_data) > 0){
			  $allowed_ans = 'No';
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
			}else{
        $allowed_ans = 'Yes';
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
?>
