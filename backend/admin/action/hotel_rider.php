<?php 
include_once('../../common.php');

if (!isset($generalobjRider)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjRider = new General_admin();
}
$generalobjRider->check_member_login();

$reload = $_SERVER['REQUEST_URI']; 

$urlparts = explode('?',$reload);
$parameters = $urlparts[1];

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$iHotelId = isset($_REQUEST['iHotelId']) ? $_REQUEST['iHotelId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
// echo "<pre>"; print_r($_REQUEST);
// echo "<pre>";print_r($_REQUEST);exit;
//Start make deleted
if ($method == 'delete' && $iHotelId != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE hotel SET eStatus = 'Deleted' WHERE iHotelId = '" . $iHotelId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Record deleted successfully.';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."hotel_rider.php?".$parameters); exit;
}
//End make deleted

//Start make reset

//End make reset

//Start Change single Status
if ($iHotelId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE hotel SET eStatus = '" . $status . "' WHERE iHotelId = '" . $iHotelId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Hotel Rider activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Hotel Rider inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."hotel_rider.php?".$parameters);
        exit;
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){
		 $query = "UPDATE hotel SET eStatus = '" . $statusVal . "' WHERE iHotelId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Hotel Rider(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."hotel_rider.php?".$parameters);
        exit;
}

?>