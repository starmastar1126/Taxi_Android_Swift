<?php 
include_once('../../common.php');

if (!isset($generalobjDriver)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjDriver = new General_admin();
}
$generalobjDriver->check_member_login();

$reload = $_SERVER['REQUEST_URI']; 

$urlparts = explode('?',$reload);
$parameters = $urlparts[1];

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
 // echo "<pre>"; print_r($_REQUEST);
// die;
 //Start make deleted
if ($method == 'delete' && $iDriverId != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE register_driver SET eStatus = 'Deleted' WHERE iDriverId = '" . $iDriverId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"].' Delete Successfully.';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."driver.php?".$parameters); exit;
}
//End make deleted

//Start Change single Status
if ($iDriverId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
		
		if(strtolower($status) == 'active'){
			$sql="SELECT register_driver.iDriverId from register_driver
			LEFT JOIN driver_vehicle on driver_vehicle.iDriverId=register_driver.iDriverId
			WHERE driver_vehicle.eStatus='Active' AND driver_vehicle.vCarType != '' AND register_driver.iDriverId='".$iDriverId."'";
			$Data=$obj->MySQLSelect($sql);
			if(count($Data) == 0){
				$_SESSION['success'] = '3';
				if($APP_TYPE == 'Ride-Delivery-UberX'){
					$_SESSION['var_msg'] = $langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"] .' status can not be activated because either '. $langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"].' has not added any vehicle or his added vehicle is not activated yet or not selected any services. Please try again after adding and activating the vehicle/services.';
				} elseif($APP_TYPE == 'UberX'){
					$_SESSION['var_msg'] = $langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"] .' status can not be activated because either '. $langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"].' has not selected any services. Please try again after adding and activating the services.';
				}else {
					$_SESSION['var_msg'] = $langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"] .' status can not be activated because either '. $langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"].' has not added any vehicle or his added vehicle is not activated yet. Please try again after adding and activating the vehicle.';
				}
				header("Location:".$tconfig["tsite_url_main_admin"]."driver.php?".$parameters);
				exit;
			}
		}
		
		$query = "UPDATE register_driver SET eStatus = '" . $status . "' WHERE iDriverId = '" . $iDriverId . "'";
		$obj->sql_query($query);
		$_SESSION['success'] = '1';
		if($status == 'Active') {
			   $_SESSION['var_msg'] = $langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"].' Activated Successfully';
		}else {
			   $_SESSION['var_msg'] = $langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"].' Inactivated Successfully';
		}
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."driver.php?".$parameters);
        exit;
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){
		 $query = "UPDATE register_driver SET eStatus = '" . $statusVal . "' WHERE iDriverId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].'(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."driver.php?".$parameters);
        exit;
}
if ($method == 'reset' && $iDriverId != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE register_driver SET vCreditCard='NULL',iTripId='0',vTripStatus='NONE',vStripeToken='',vStripeCusId='' WHERE iDriverId = '" . $iDriverId . "'";          
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] =  $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].'Reset successfully';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."driver.php"); exit;
}
//End Change All Selected Status
?>