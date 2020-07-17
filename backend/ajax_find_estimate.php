<?php 
include_once("common.php");

$dist_fare = isset($_REQUEST['dist_fare'])?$_REQUEST['dist_fare']:'';
$time_fare = isset($_REQUEST['time_fare'])?$_REQUEST['time_fare']:'';
$fromLoc = isset($_REQUEST['fromLoc'])?$_REQUEST['fromLoc']:'';
$from_lat = isset($_REQUEST['from_lat'])?$_REQUEST['from_lat']:'';
$from_long = isset($_REQUEST['from_long'])?$_REQUEST['from_long']:'';


if($dist_fare != '' && $time_fare != "")
{
	$priceRatio = 1;
	$db_country = array();
	$pickuplocationarr = array($from_lat,$from_long);
    $GetVehicleIdfromGeoLocation = $generalobj->GetVehicleTypeFromGeoLocation($pickuplocationarr);

	$sql = "select vt.*,lm.iCountryId as ConId from vehicle_type as vt LEFT JOIN location_master as lm on lm.iLocationId = vt.iLocationid WHERE vt.iLocationid in ($GetVehicleIdfromGeoLocation) AND vt.eType = '".$APP_TYPE."'";
	$db_vType = $obj->MySQLSelect($sql);
	
	// echo "<pre>";print_r($db_vType);exit;
	foreach($db_vType as $val){
		if($val['ConId'] != ""){
			$sql2 = "select eUnit from country WHERE iCountryId = '".$val['ConId']."'";
			$db_country = $obj->MySQLSelect($sql2);
			break;
		}
	}
	$eUnit = $DEFAULT_DISTANCE_UNIT;
	if(!empty($db_country)){
		if($db_country[0]['eUnit'] == 'KMs' || $db_country[0]['eUnit'] == ''){
			$dist_fare_new = $dist_fare;
			$eUnit = "KMs";
		}else {
			$dist_fare_new = $dist_fare * 0.621371;
			$eUnit = "Miles";
		}
	}else {
		if($eUnit == "KMs"){
			$dist_fare_new = $dist_fare;
		}else{
			$dist_fare_new = $dist_fare * 0.621371;
		}
	} 
	
	//0.621371 for miles
	// echo $dist_fare_new;
	$cont = '';
	$cont .= '<ul>';
    for($i=0;$i<count($db_vType);$i++){
		$fPricePerKM = $db_vType[$i]['fPricePerKM'];
		
		if($db_vType[$i]['iLocationid'] == "-1"){
			if($eUnit != $DEFAULT_DISTANCE_UNIT){
				if($eUnit == "KMs"){
					$fPricePerKM = round($db_vType[$i]['fPricePerKM'] * 0.621371,2);
				}else if($eUnit == "Miles"){
					$fPricePerKM = round($db_vType[$i]['fPricePerKM'] / 0.621371,2) ;
				}
			}
		}
		// echo "\n==>".$fPricePerKM;
		
		$Minute_Fare =round($db_vType[$i]['fPricePerMin']*$time_fare,2) * $priceRatio;
		$Distance_Fare =round($fPricePerKM*$dist_fare_new,2)* $priceRatio;
		// echo "\n==>".$Distance_Fare;
		$iBaseFare =round($db_vType[$i]['iBaseFare'],2)* $priceRatio;
		$total_fare=$iBaseFare+$Minute_Fare+$Distance_Fare;
		
		$cont .= '<li><label>'.$db_vType[$i]['vVehicleType_'.$_SESSION["sess_lang"]].'<img style="display:none;" src="assets/img/question-icon.jpg" alt="" title="'.$langage_lbl['LBL_APPROX_DISTANCE_TXT'].' '.$langage_lbl['LBL_FARE_ESTIMATE_TXT'].'"><b>'.$generalobj->trip_currency($total_fare).'</b></label></li>';		
    }
	$cont .= '<li><p>'.stripcslashes($langage_lbl['LBL_HOME_PAGE_GET_FIRE_ESTIMATE_TXT']).'</p></li>';
	if(!isset($_SESSION['sess_user']) && $_SESSION['sess_user'] == "") {
		$cont .= '<li><strong><a href="sign-up-rider"><em>'.$langage_lbl['LBL_RIDER_SIGNUP1_TXT'].'</em></a></strong></li>';
	}
	$cont .= '</ul>';
    echo $cont; exit;
}
?>
