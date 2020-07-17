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
$iVehicleCategoryId = isset($_REQUEST['iVehicleCategoryId']) ? $_REQUEST['iVehicleCategoryId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
$sub_cid = isset($_REQUEST['sub_cid']) ? $_REQUEST['sub_cid'] : '';
//echo "<pre>";print_r($_REQUEST);exit;

//Start make deleted
if ($method == 'delete' && $sub_cid != '') {
	if(SITE_TYPE !='Demo'){

			$sql = "SELECT count(iVehicleTypeId) as total_type FROM vehicle_type WHERE iVehicleCategoryId = '".$iVehicleCategoryId."'";

            $data_cat = $obj->MySQLSelect($sql);

            if($data_cat[0]['total_type'] > 0){

                $_SESSION['success'] = '3';

                $_SESSION['var_msg'] = 'This category have service type so you can not delete this category. Please delete service type than after delete this category.';

            } else {

	            $query = "DELETE FROM vehicle_category WHERE iVehicleCategoryId = '" . $iVehicleCategoryId . "' AND iParentId = '" . $sub_cid . "'";

	            $obj->sql_query($query);

	            $_SESSION['success'] = '1';

	            $_SESSION['var_msg'] = 'Vehicle Sub Category deleted successfully.';   
        	}
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."vehicle_sub_category.php?".$parameters); exit;
}


//Start Change single Status
if ($iVehicleCategoryId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE vehicle_category SET eStatus = '" . $status . "' WHERE iVehicleCategoryId = '" . $iVehicleCategoryId . "' AND iParentId = '" . $sub_cid . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Vehicle Sub Category activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Vehicle Sub Category inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
	 header("Location:".$tconfig["tsite_url_main_admin"]."vehicle_sub_category.php?".$parameters);
        exit;
       
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){
		if($statusVal == "Deleted") {
			$query = "DELETE FROM vehicle_category WHERE iVehicleCategoryId IN (" . $checkbox . ") AND iParentId = '" . $sub_cid . "'";
			$obj->sql_query($query);
		 	$_SESSION['success'] = '1';
		 	$_SESSION['var_msg'] = 'Vehicle Sub Category(s) Deleted successfully.';
		} else {
		 	$query = "UPDATE vehicle_category SET eStatus = '" . $statusVal . "' WHERE iVehicleCategoryId IN (" . $checkbox . ") AND iParentId = '" . $sub_cid . "'";
		 	$obj->sql_query($query);
		 	$_SESSION['success'] = '1';
		 	$_SESSION['var_msg'] = 'Vehicle Sub Category(s) updated successfully.';
		}

	}
	else{
		$_SESSION['success']=2;
	}
		
        header("Location:".$tconfig["tsite_url_main_admin"]."vehicle_sub_category.php?".$parameters);
        exit;
}

?>