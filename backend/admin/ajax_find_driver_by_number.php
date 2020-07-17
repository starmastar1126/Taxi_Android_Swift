<?php 
include_once("../common.php");

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$getlan = isset($_REQUEST['getlan'])?$_REQUEST['getlan']:'';
$getlng = isset($_REQUEST['getlng'])?$_REQUEST['getlng']:'';
$googlekey = isset($_REQUEST['googlekey'])?$_REQUEST['googlekey']:'';
$limitdistance = isset($_REQUEST['limitdistance'])?$_REQUEST['limitdistance']:''; 


 		$str_date = @date('Y-m-d H:i:s', strtotime('-5 minutes'));
        //$LIST_DRIVER_LIMIT_BY_DISTANCE = $generalobj->getConfigurations("configurations","LIST_DRIVER_LIMIT_BY_DISTANCE");
		$DRIVER_REQUEST_METHOD = $generalobj->getConfigurations("configurations","DRIVER_REQUEST_METHOD");
		$param = ($DRIVER_REQUEST_METHOD == "Time")? "tOnline":"tLastOnline";
		
    
        $sql = "SELECT ROUND(( 3959 * acos( cos( radians(".$getlan.") )
        * cos( radians( vLatitude ) )
        * cos( radians( vLongitude ) - radians(".$getlng.") )
        + sin( radians(".$getlan.") )
        * sin( radians( vLatitude ) ) ) ),2) AS distance, register_driver.*  FROM `register_driver`
        WHERE (vLatitude != '' AND vLongitude != '' AND vAvailability = 'Available' AND vTripStatus != 'Active' AND eStatus='active' AND tLastOnline > '$str_date')
        HAVING distance < ".$limitdistance." ORDER BY `register_driver`.`".$param."` ASC"; 
          $Data = $obj->MySQLSelect($sql);

          $store =array();
		    
		    for($i=0;$i<count($Data);$i++){

		    	$store[$i]["name"] = $generalobjAdmin->clearName($Data[$i]['vName'].' '.$Data[$i]['vLastName']);
		    	$store[$i]["add"] = $Data[$i]['vCaddress'];
		    	$store[$i]["lat"] = $Data[$i]['vLatitude'];
		    	$store[$i]["lag"] = $Data[$i]['vLongitude'];
		    	$store[$i]["iDriverId"] = $Data[$i]['iDriverId'];
				
		    }
		   echo  json_encode($store); exit;
?>