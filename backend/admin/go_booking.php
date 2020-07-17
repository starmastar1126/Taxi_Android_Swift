<?php 
include_once('../common.php');

//$pickups = explode(',', $_POST['from_lat_long']);
//$passengerLat = isset($pickups[0]) ? $pickups[0] : '';
//$passengerLon = isset($pickups[1]) ? $pickups[1] : '';
////$passengerLat = isset($_REQUEST["PassengerLat"]) ? $_REQUEST["PassengerLat"] : '';
////$passengerLon = isset($_REQUEST["PassengerLon"]) ? $_REQUEST["PassengerLon"] : '';
//
//$Data = getOnlineDriverArr($passengerLat, $passengerLon);
//
//$i = 0;
//while (count($Data) > $i) {
//    if ($Data[$i]['vImage'] != "" && $Data[$i]['vImage'] != "NONE") {
//        $Data[$i]['vImage'] = "3_" . $Data[$i]['vImage'];
//    }
//    $driverVehicleID = $Data[$i]['iDriverVehicleId'];
//
//    $sql = "SELECT dv.*, make.vMake AS make_title, model.vTitle model_title FROM `driver_vehicle` dv, make, model 
//        WHERE dv.iMakeId = make.iMakeId 
//        AND dv.iModelId = model.iModelId
//        AND iDriverVehicleId='$driverVehicleID'";
//        $rows_driver_vehicle = $obj->MySQLSelect($sql);
//
//    $Data[$i]['DriverCarDetails'] = $rows_driver_vehicle[0];
//
//    $i++;
//}
//$returnArr['AvailableCabList'] = $Data;
//$returnArr['PassengerLat'] = $passengerLat;
//$returnArr['PassengerLon'] = $passengerLon;
//echo json_encode($returnArr);

function getOnlineDriverArr($sourceLat, $sourceLon) {
    global $generalobj, $obj;
    $str_date = @date('Y-m-d H:i:s', strtotime('-5 minutes'));
    $LIST_DRIVER_LIMIT_BY_DISTANCE = $generalobj->getConfigurations("configurations", "LIST_DRIVER_LIMIT_BY_DISTANCE");
    $DRIVER_REQUEST_METHOD = $generalobj->getConfigurations("configurations", "DRIVER_REQUEST_METHOD");
    $param = ($DRIVER_REQUEST_METHOD == "Time") ? "tOnline" : "tLastOnline";
    // if($DRIVER_REQUEST_METHOD == "Time"){
    // $param = " ORDER BY `register_driver`.`tOnline` ASC";	
    // }else{
    // $param = " ORDER BY `register_driver`.`tLastOnline` ASC";
    // }
    $Data = array();
    $sql = "SELECT ROUND(( 3959 * acos( cos( radians(" . $sourceLat . ") ) 
            * cos( radians( vLatitude ) ) 
            * cos( radians( vLongitude ) - radians(" . $sourceLon . ") ) 
            + sin( radians(" . $sourceLat . ") ) 
            * sin( radians( vLatitude ) ) ) ),2) AS distance, register_driver.*  FROM `register_driver`
            WHERE (vLatitude != '' AND vLongitude != '' AND vAvailability = 'Available' AND vTripStatus != 'Active' AND eStatus='active' AND tLastOnline > '$str_date')
            HAVING distance < " . $LIST_DRIVER_LIMIT_BY_DISTANCE . " ORDER BY `register_driver`.`" . $param . "` ASC";
    $Data = $obj->MySQLSelect($sql);
    return $Data;
}
?>