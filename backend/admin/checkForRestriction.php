<?php 
include_once("../common.php");
include_once("../generalFunctions.php");

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$fromLat = isset($_REQUEST['fromLat'])?$_REQUEST['fromLat']:'';
$fromLong = isset($_REQUEST['fromLong'])?$_REQUEST['fromLong']:'';
$toLat = isset($_REQUEST['toLat'])?$_REQUEST['toLat']:'';
$toLong = isset($_REQUEST['toLong'])?$_REQUEST['toLong']:'';
$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';

$sourceLocationArr =array($fromLat,$fromLong);
$destinationLocationArr =array($toLat,$toLong);

/*if($type == "both"){

	if($sourceLocationArr != "") {
		$allowed_ans = checkAllowedAreaNew($sourceLocationArr,"No");
	}
	if($destinationLocationArr != ""){
		$allowed_ans_drop = checkAllowedAreaNew($destinationLocationArr,"Yes");
	}
	if($allowed_ans == "No" && $allowed_ans_drop == "No"){
		echo $langage_lbl_admin['LBL_PICK_DROP_LOCATION_NOT_ALLOW'].'. Are You Sure Continue With This Loaction.' ;
		exit;
	}
	if($allowed_ans == "Yes" && $allowed_ans_drop == "No"){
		echo $langage_lbl_admin['LBL_DROP_LOCATION_NOT_ALLOW'] .'. Are You Sure Continue With This DropOff Loaction.';
		exit;
	}
	if($allowed_ans == "No" && $allowed_ans_drop == "Yes"){
		echo $langage_lbl_admin['LBL_PICKUP_LOCATION_NOT_ALLOW'].'. Are You Sure Continue With This Pickup Loaction.';
		exit;
	}

} */
if($type == "from"){

	if($sourceLocationArr != "") {
		$allowed_ans = checkAllowedAreaNew($sourceLocationArr,"No");
	}
	if($allowed_ans == "No"){
		echo $langage_lbl_admin['LBL_PICKUP_LOCATION_NOT_ALLOW'].'. Are You Sure Continue With This Loaction.';
		exit;
	}

}
if($type == "to"){

	if($destinationLocationArr != ""){
		$allowed_ans_drop = checkAllowedAreaNew($destinationLocationArr,"Yes");
	}
	if($allowed_ans_drop == "No"){
		echo $langage_lbl_admin['LBL_DROP_LOCATION_NOT_ALLOW'] .'. Are You Sure Continue With This Loaction.';
		exit;
	}

}

?>