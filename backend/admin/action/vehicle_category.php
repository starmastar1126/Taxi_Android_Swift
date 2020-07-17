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
//echo "<pre>"; print_r($_REQUEST);die;

//Start make deleted
if ($method == 'delete' && $iVehicleCategoryId != '') {
	if(SITE_TYPE !='Demo'){
            $sql = "SELECT count(iVehicleCategoryId) as total_sub FROM vehicle_category WHERE iParentId = '".$iVehicleCategoryId."'";
            $data_cat = $obj->MySQLSelect($sql);
            if($data_cat[0]['total_sub'] > 0){
                $_SESSION['success'] = '3';
                $_SESSION['var_msg'] = 'This category have sub categories so you can not delete this category. Please delete sub category than after delete this category.';
            } else {
                $query = "DELETE FROM  vehicle_category WHERE iVehicleCategoryId = '" . $iVehicleCategoryId . "'";
                
                $obj->sql_query($query);

                $_SESSION['success'] = '1';
                $_SESSION['var_msg'] = 'Record deleted successfully.';   
            }


	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."vehicle_category.php?".$parameters); exit;
}

//Start Change single Status
if ($iVehicleCategoryId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE vehicle_category SET eStatus = '" . $status . "' WHERE iVehicleCategoryId = '" . $iVehicleCategoryId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Vehicle Category activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Vehicle Category inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."vehicle_category.php?".$parameters);
        exit;
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){
		 $query = "UPDATE vehicle_category SET eStatus = '" . $statusVal . "' WHERE iVehicleCategoryId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Vehicle Category(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."vehicle_category.php?".$parameters);
        exit;
}

?>