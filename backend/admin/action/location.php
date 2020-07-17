<?php 
include_once('../../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$reload = $_SERVER['REQUEST_URI']; 

$urlparts = explode('?',$reload);
$parameters = $urlparts[1];

$iLocationId = isset($_REQUEST['iLocationId']) ? $_REQUEST['iLocationId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
 //echo "<pre>"; print_r($_REQUEST);die;
//Start Location deleted
if ($method == 'delete' && $iLocationId != '') {
	if(SITE_TYPE !='Demo'){
            $query1 = "SELECT * FROM location_master WHERE iLocationId = '" . $iLocationId . "'";
            $checklocation = $obj->MySQLSelect($query1);

           if($checklocation[0]['eFor'] == 'VehicleType') {
                $sql = "SELECT count(iLocationid) as total_active_vehicle FROM  `vehicle_type` WHERE iLocationid = '".$iLocationId."'";
                $active_vehicle = $obj->MySQLSelect($sql);
                if($active_vehicle[0]['total_active_vehicle'] > 0) {
                    $_SESSION['success'] = '3';
                    $_SESSION['var_msg'] = 'Location is alredy use for Vehicle type.';
                    header("Location:".$tconfig["tsite_url_main_admin"]."location.php?".$parameters);
                    exit;
                } 
            } else if($checklocation[0]['eFor'] == 'Restrict') {
                $sql = "SELECT count(iLocationid) as restriction_area FROM  `restricted_negative_area` WHERE iLocationid = '".$iLocationId."'";
                $restriction_area = $obj->MySQLSelect($sql);
                if($restriction_area[0]['restriction_area'] > 0) {
                    $_SESSION['success'] = '3';
                    $_SESSION['var_msg'] = 'Location is alredy use in restriction area.';
                    header("Location:".$tconfig["tsite_url_main_admin"]."location.php?".$parameters);
                    exit;
                } 
            } else if($checklocation[0]['eFor'] == 'FixFare') {
                $sql = "SELECT count(iLocatioId) as fixfare FROM  `location_wise_fare` WHERE 1=1 AND (iToLocationId = '".$iLocationId."' OR iFromLocationId = '".$iLocationId."') ";
                $locationfare_area = $obj->MySQLSelect($sql);
                if($locationfare_area[0]['fixfare'] > 0) {
                    $_SESSION['success'] = '3';
                    $_SESSION['var_msg'] = 'Location is alredy use in location wise fare.';
                    header("Location:".$tconfig["tsite_url_main_admin"]."location.php?".$parameters);
                    exit;
                } 
            }

            $query = "DELETE FROM location_master WHERE iLocationId = '" . $iLocationId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Location deleted successfully.';   
	} else {
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."location.php?".$parameters); exit;
}
//End Location deleted

//Start Change single Status
if ($iLocationId != '' && $status != '') {
	if(SITE_TYPE !='Demo') {
            $query1 = "SELECT * FROM location_master WHERE iLocationId = '" . $iLocationId . "'";
            $checklocation = $obj->MySQLSelect($query1);

            if($checklocation[0]['eFor'] == 'VehicleType') {
                $sql = "SELECT count(iLocationid) as total_active_vehicle FROM  `vehicle_type` WHERE iLocationid = '".$iLocationId."'";
                $active_vehicle = $obj->MySQLSelect($sql);
                if($active_vehicle[0]['total_active_vehicle'] > 0) {
                    $_SESSION['success'] = '3';
                    $_SESSION['var_msg'] = 'Location is alredy use for Vehicle type.';
                    header("Location:".$tconfig["tsite_url_main_admin"]."location.php?".$parameters);
                    exit;
                } 
            } else if($checklocation[0]['eFor'] == 'Restrict') {
                $sql = "SELECT count(iLocationid) as restriction_area FROM  `restricted_negative_area` WHERE iLocationid = '".$iLocationId."'";
                $restriction_area = $obj->MySQLSelect($sql);
                if($restriction_area[0]['restriction_area'] > 0) {
                    $_SESSION['success'] = '3';
                    $_SESSION['var_msg'] = 'Location is alredy use in restriction area.';
                    header("Location:".$tconfig["tsite_url_main_admin"]."location.php?".$parameters);
                    exit;
                } 
            } else if($checklocation[0]['eFor'] == 'FixFare') {
                $sql = "SELECT count(iLocatioId) as fixfare FROM  `location_wise_fare` WHERE 1=1 AND (iToLocationId = '".$iLocationId."' OR iFromLocationId = '".$iLocationId."') ";
                $locationfare_area = $obj->MySQLSelect($sql);
                if($locationfare_area[0]['fixfare'] > 0) {
                    $_SESSION['success'] = '3';
                    $_SESSION['var_msg'] = 'Location is alredy use in location wise fare.';
                    header("Location:".$tconfig["tsite_url_main_admin"]."location.php?".$parameters);
                    exit;
                } 
            }

            $query = "UPDATE location_master SET eStatus = '" . $status . "' WHERE iLocationId = '" . $iLocationId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Location activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Location inactivated successfully.';
            }

	} else {
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."location.php?".$parameters);
        exit;
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){
        if($statusVal == "Deleted") {
            $query = "DELETE FROM location_master WHERE iLocationId IN (" . $checkbox . ")";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Location(s) deleted successfully.';
        } else {
            $query = "UPDATE location_master SET eStatus = '" . $statusVal . "' WHERE iLocationId IN (" . $checkbox . ")";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Location(s) updated successfully.';
        }
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."location.php?".$parameters);
        exit;
}
//End Change All Selected Status

?>