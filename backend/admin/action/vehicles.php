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
$iDriverVehicleId = isset($_REQUEST['iDriverVehicleId']) ? $_REQUEST['iDriverVehicleId'] : '';
$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? $_REQUEST['checkbox'] : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';

 //Start make deleted
if ($method == 'delete' && $iDriverVehicleId != '') {
if(SITE_TYPE !='Demo'){
	$sql1 = "SELECT * FROM trips WHERE iDriverVehicleId = '" . $iDriverVehicleId . "' AND iActive IN ('Active',  'On Going Trip')";
	$current_active_trip = $obj->MySQLSelect($sql1);
	
	if(empty($current_active_trip)) {
		$query = "UPDATE driver_vehicle SET eStatus = 'Deleted' WHERE iDriverVehicleId = '" . $iDriverVehicleId . "'";
        $obj->sql_query($query);

        //$sql = "SELECT * FROM register_driver WHERE iDriverId = '".$iDriverId."' AND vAvailability = 'Available' AND iDriverVehicleId = '" . $iDriverVehicleId . "'";

		$sql = "SELECT * FROM register_driver WHERE iDriverId = '".$iDriverId."' AND iDriverVehicleId = '" . $iDriverVehicleId . "'";
		$avail_driver = $obj->MySQLSelect($sql);
		if(!empty($avail_driver)) {
	        $sql_update = "UPDATE register_driver SET vAvailability = 'Not Avilable', `iDriverVehicleId`= '0' WHERE iDriverId = '" . $iDriverId . "' AND iDriverVehicleId = '" . $iDriverVehicleId . "'";
	        $obj->sql_query($sql_update);
		}
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = 'Vehicle deleted successfully.';
    } else { 
    	$_SESSION['success'] = '3';
        $_SESSION['var_msg'] = "Vehicle can't delete because of driver has on trip.";
    }
} else {
    $_SESSION['success'] = '2';
}
	header("Location:".$tconfig["tsite_url_main_admin"]."vehicles.php?".$parameters); exit;
}
//End make deleted

//Start Change single Status
// For active or inactive
if ($iDriverVehicleId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
			if($status == 'Inactive') {
				$sql1 = "SELECT * FROM trips WHERE iDriverVehicleId = '" . $iDriverVehicleId . "' AND iActive IN ('Active',  'On Going Trip')";
				$current_active_trip = $obj->MySQLSelect($sql1);
				if(empty($current_active_trip)) {
					$query = "UPDATE driver_vehicle SET eStatus = '" . $status . "' WHERE iDriverVehicleId = '" . $iDriverVehicleId . "'";
	            	$obj->sql_query($query);

	            	$sql = "SELECT * FROM register_driver WHERE iDriverVehicleId = '" . $iDriverVehicleId . "'";
					$avail_driver = $obj->MySQLSelect($sql);
					if(!empty($avail_driver)) {
				        $sql_update = "UPDATE register_driver SET vAvailability = 'Not Avilable', `iDriverVehicleId`= '0' WHERE iDriverId = '" . $avail_driver[0]['iDriverId'] . "' AND iDriverVehicleId = '" . $iDriverVehicleId . "'";
				        $obj->sql_query($sql_update);
					}
	            	if($SEND_TAXI_EMAIL_ON_CHANGE == 'Yes') {
						$sql23 = "SELECT m.vMake, md.vTitle,rd.vEmail, rd.vName, rd.vLastName, c.vCompany as companyFirstName
								FROM driver_vehicle dv, register_driver rd, make m, model md, company c
								WHERE dv.eStatus != 'Deleted' AND dv.iDriverId = rd.iDriverId AND dv.iCompanyId = c.iCompanyId  AND dv.iModelId = md.iModelId AND dv.iMakeId = m.iMakeId AND dv.iDriverVehicleId = '".$iDriverVehicleId."'";
						$data_email_drv = $obj->MySQLSelect($sql23);
						$maildata['EMAIL'] =$data_email_drv[0]['vEmail'];
						$maildata['NAME'] = $data_email_drv[0]['vName'];
						$maildata['DETAIL']="Your ".$langage_lbl_admin['LBL_TEXI_ADMIN']." ".$data_email_drv[0]['vTitle']." For COMPANY ".$data_email_drv[0]['companyFirstName'] ." is temporarly ".$status;
						$generalobj->send_email_user("ACCOUNT_STATUS",$maildata);
					}
	            	$_SESSION['success'] = '1';
	            	$_SESSION['var_msg'] = 'Vehicle inactivated successfully.';
				} else {
					$_SESSION['success'] = '3';
        			$_SESSION['var_msg'] = "Vehicle can't inactive because of driver has on trip.";
				}
			} else {
	            $query = "UPDATE driver_vehicle SET eStatus = '" . $status . "' WHERE iDriverVehicleId = '" . $iDriverVehicleId . "'";
	            $obj->sql_query($query);
				if($SEND_TAXI_EMAIL_ON_CHANGE == 'Yes') {
					$sql23 = "SELECT m.vMake, md.vTitle,rd.vEmail, rd.vName, rd.vLastName, c.vCompany as companyFirstName
							FROM driver_vehicle dv, register_driver rd, make m, model md, company c
							WHERE
							  dv.eStatus != 'Deleted'
							  AND dv.iDriverId = rd.iDriverId
							  AND dv.iCompanyId = c.iCompanyId
							  AND dv.iModelId = md.iModelId
							  AND dv.iMakeId = m.iMakeId AND dv.iDriverVehicleId = '".$iDriverVehicleId."'";
					$data_email_drv = $obj->MySQLSelect($sql23);
					$maildata['EMAIL'] =$data_email_drv[0]['vEmail'];
					$maildata['NAME'] = $data_email_drv[0]['vName'];
					$maildata['DETAIL']="Your ".$langage_lbl_admin['LBL_TEXI_ADMIN']." ".$data_email_drv[0]['vTitle']." For COMPANY ".$data_email_drv[0]['companyFirstName'] ." is temporarly ".$status;
					$generalobj->send_email_user("ACCOUNT_STATUS",$maildata);
				}
            	$_SESSION['success'] = '1';
            	if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Vehicle activated successfully.';
            	}else {
                   $_SESSION['var_msg'] = 'Vehicle inactivated successfully.';
            	}
            }
	} else {
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."vehicles.php?".$parameters);
        exit;
}
//End Change single Status

//Start Change All Selected Status
if(!empty($checkbox) && $statusVal != "") {
	$checkbox_values = implode(',',$_REQUEST['checkbox']);
	if(SITE_TYPE !='Demo'){
		$current_active_trip = "";
		if(($statusVal == "Deleted") || ($statusVal == "Inactive")) {
			$sql = "SELECT iDriverId FROM driver_vehicle WHERE iDriverVehicleId IN (" . $checkbox_values . ")";
			$driverids = $obj->MySQLSelect($sql);
			foreach ($driverids as $key => $value) {
				$data[$value['iDriverId']] = $value['iDriverId'];
			}
			$driverid = implode(",",$data);
			$sql1 = "SELECT * FROM register_driver as d LEFT JOIN trips as t ON t.iDriverId = d.iDriverId WHERE t.iDriverId IN (" . $driverid  . ") AND  t.iDriverVehicleId  IN (" . $checkbox_values . ") AND  t.iActive IN ('Active',  'On Going Trip') ";
		    $current_active_trip = $obj->MySQLSelect($sql1);
		}
	    if(empty($current_active_trip)) {	
			$query = "UPDATE driver_vehicle SET eStatus = '" . $statusVal . "' WHERE iDriverVehicleId IN (" . $checkbox_values . ")";
			$obj->sql_query($query);

			$sql = "SELECT * FROM register_driver WHERE iDriverId IN (" . $driverid . ") AND vAvailability = 'Available' AND iDriverVehicleId IN (" . $checkbox_values . ")";
			$avail_driver = $obj->MySQLSelect($sql);
			if($statusVal == "Deleted" && !empty($avail_driver)) {
				$sql_update = "UPDATE register_driver SET vAvailability = 'Not Avilable', `iDriverVehicleId`= '0' WHERE iDriverId IN (" . $driverid . ")";
				$obj->sql_query($sql_update);
			}

			if($SEND_TAXI_EMAIL_ON_CHANGE == 'Yes') {
				foreach($checkbox as $iDriverVehicleId){
					$sql23 = "SELECT m.vMake, md.vTitle,rd.vEmail, rd.vName, rd.vLastName, c.vCompany as companyFirstName
							FROM driver_vehicle dv, register_driver rd, make m, model md, company c
							WHERE dv.eStatus != 'Deleted' AND dv.iDriverId = rd.iDriverId AND dv.iCompanyId = c.iCompanyId AND dv.iModelId = md.iModelId AND dv.iMakeId = m.iMakeId AND dv.iDriverVehicleId = '".$iDriverVehicleId."'";
					$data_email_drv = $obj->MySQLSelect($sql23);
					$maildata['EMAIL'] =$data_email_drv[0]['vEmail'];
					$maildata['NAME'] = $data_email_drv[0]['vName'];
					$maildata['DETAIL']="Your ".$langage_lbl_admin['LBL_TEXI_ADMIN']." ".$data_email_drv[0]['vTitle']." For COMPANY ".$data_email_drv[0]['companyFirstName'] ." is temporarily ".$statusVal;
					$generalobj->send_email_user("ACCOUNT_STATUS",$maildata);
				}
			}
			 
			 $_SESSION['success'] = '1';
			 $_SESSION['var_msg'] = 'Vehicle(s) updated successfully.';
		} else {
			$_SESSION['success'] = '3';
       		$_SESSION['var_msg'] = "Record can't ".$statusVal." because one of driver has on trip.";	
		}
	} else {
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."vehicles.php?".$parameters);
        exit;
}
//End Change All Selected Status
?>