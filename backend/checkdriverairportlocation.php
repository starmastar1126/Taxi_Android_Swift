<?php 
  include_once("common.php");
  include_once('include_config.php');
  //include_once('generalFunctions.php');
  //include_once('include_taxi_webservices.php');
	//include_once(TPATH_CLASS.'configuration.php');
############### Check Member (Driver/PAssenger) Airport Location   ###################################################################
//$Source_point_Address = array("23.014832841515943","72.50621404498816"); // check for passenger
//$eUserType = "Passenger";
$Source_point_Address = array("23.012400","72.503607"); // check for driver
$eUserType = "Driver";
//checkMemberAirpotLocation($Source_point_Address, $eUserType);
function checkMemberAirpotLocation($Source_point_Address, $eUserType = "Passenger") {
	global $generalobj,$obj;
	
  if($eUserType == "Passenger"){
     $fields = "iAirportLocationId,vLocationName as vFromname,tPassengerLatitude as fromlat,tPassengerLongitude as fromlong";
  }else{
     $fields = "iAirportLocationId,vLocationName as vFromname,tDriverLatitude as fromlat,tDriverLongitude as fromlong";
  }
  
  $returnArr = array();
	$sql = "SELECT $fields FROM airport_location_master WHERE eStatus = 'Active'";        
	$location_data = $obj->MySQLSelect($sql);
	//echo"<pre>";print_r($location_data);die;
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
        //echo "<pre>";print_r($from_polygon[$key]);echo"<br/>";
				$from_source_addresss = contains($Source_point_Address,$from_polygon[$key]) ? 'IN' : 'OUT';
				if($from_source_addresss == "IN") {
					$returnArr['iAirportLocationId']=$location_data[$key]['iAirportLocationId'];
          $returnArr['eAirportLocation']="Yes";
					//return $returnArr;
				}
			}
		}
	} 
	if(empty($returnArr)) {
		$returnArr['iAirportLocationId']=0;
		$returnArr['eAirportLocation']="No";
	}	
	echo "<pre>";print_r($returnArr);die;
	return $returnArr;
}
############### Check Member (Driver/PAssenger) Airport Location   ###################################################################  
  

############### Check Driver Airport Location   ###################################################################   
  $iAirportLocationId = "1"; 
  $iDriverId = "33"; 
  $eFor = "Remove";
  UpdateDriverAirportLocation($iAirportLocationId,$iDriverId,$eFor);
  
function UpdateDriverAirportLocation($iAirportLocationId,$iDriverId,$eFor = "Insert") {
	global $generalobj,$obj;
                
  if($eFor == "Insert"){
    $sql = "SELECT  * FROM  `driver_location_airport` WHERE iDriverId = '".$iDriverId."' AND `iAirportLocationId` = '".$iAirportLocationId."' ";
  	$db_sql = $obj->MySQLSelect($sql);
    
    if(count($db_sql) == 0){
       $data['iAirportLocationId'] = $iAirportLocationId;
    	 $data['iDriverId'] = $iDriverId;
    	 $data['tAddedDate'] = @date("Y-m-d H:i:s");
    	 	
    	 $id = $obj->MySQLQueryPerform("driver_location_airport", $data, 'insert');
       if($id > 0){
         UpdateDriverAirportLocation($iAirportLocationId,$iDriverId,"Check");
       }
    }else{
      UpdateDriverAirportLocation($iAirportLocationId,$iDriverId,"Check");
    }
  }
  
  if($eFor == "Check"){
    $sql = "SELECT  * FROM  `driver_location_airport` WHERE `iAirportLocationId` = '".$iAirportLocationId."' ORDER BY tAddedDate ASC";
  	$db_sql = $obj->MySQLSelect($sql);
    
    $TotalDriver = count($db_sql);
    $returnArr['TotalDriver'] = $TotalDriver;
    $DriverPosition = 0;
    if(count($db_sql) > 0){
       $j = 1;
       for($i=0;$i<count($db_sql);$i++){
          $db_sql[$i]['DriverPosition']= $j;
          $j++;
          if($iDriverId == $db_sql[$i]['iDriverId']){
             $DriverPosition = $db_sql[$i]['DriverPosition'];
             $returnArr['IsDriverExistinAirport'] = "Yes";
             $returnArr['DriverPosition'] = $DriverPosition;
             break;  
          }else{
             $returnArr['IsDriverExistinAirport'] = "No";
             $returnArr['DriverPosition'] = 0;
          }
       }
       
    }else{
       $returnArr['IsDriverExistinAirport'] = "No";
       $returnArr['DriverPosition'] = 0;
       $returnArr['TotalDriver'] = 0;
    }
  }
  
  if($eFor == "Update"){
    $updateQuery = "UPDATE driver_location_airport SET tAddedDate='".date("Y-m-d H:i:s")."' WHERE iDriverId = '".$iDriverId."' AND `iAirportLocationId` = '".$iAirportLocationId."'";
		$obj->sql_query($updateQuery);
		
    UpdateDriverAirportLocation($iAirportLocationId,$iDriverId,"Check");
  }
  
  if($eFor == "Remove"){
    $ssql = "";
    if($iAirportLocationId > 0){
      $ssql .= "AND `iAirportLocationId` = '".$iAirportLocationId."'";
    }
    
    $sql = "DELETE FROM driver_location_airport WHERE iDriverId = '".$iDriverId."' $ssql";
		$id = $obj->sql_query($sql);
		
    UpdateDriverAirportLocation($iAirportLocationId,$iDriverId,"Check");
  }
  echo "<pre>";print_r($returnArr);
  return $returnArr; 
}
############### Check Driver Airport Location   ###################################################################

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