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

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$iRestrictedNegativeId = isset($_REQUEST['iRestrictedNegativeId']) ? $_REQUEST['iRestrictedNegativeId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
// echo "<pre>"; print_r($_REQUEST);
//Start make deleted
if ($method == 'delete' && $iRestrictedNegativeId != '') {
	if(SITE_TYPE !='Demo'){
            $query = "DELETE FROM restricted_negative_area WHERE iRestrictedNegativeId = '" . $iRestrictedNegativeId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Record deleted successfully.';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."restricted_area.php?".$parameters); exit;
}
//End make deleted

//Start Change single Status
if ($iRestrictedNegativeId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE restricted_negative_area SET eStatus = '" . $status . "' WHERE iRestrictedNegativeId = '" . $iRestrictedNegativeId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Restricted Area activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Restricted Area inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."restricted_area.php?".$parameters);
        exit;
}
//End Change single Status

//Start Change All Selected Status+

if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){
		
		// echo $statusVal; die;
		if($statusVal == 'Deleted'){
			$query = "DELETE FROM restricted_negative_area WHERE iRestrictedNegativeId IN (" . $checkbox . ")";//die;
		}else {
			$query = "UPDATE restricted_negative_area SET eStatus = '" . $statusVal . "' WHERE iRestrictedNegativeId IN (" . $checkbox . ")";
		}
		$obj->sql_query($query);
		$_SESSION['success'] = '1';
		$_SESSION['var_msg'] = 'Restricted Area(s) deleted successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."restricted_area.php?".$parameters);
	exit;
}
?>