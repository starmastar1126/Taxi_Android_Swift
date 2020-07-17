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
$iPackageTypeId = isset($_REQUEST['iPackageTypeId']) ? $_REQUEST['iPackageTypeId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
// echo "<pre>"; print_r($_REQUEST);
//Start make deleted
if ($method == 'delete' && $iPackageTypeId != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE package_type SET eStatus = 'Deleted' WHERE iPackageTypeId = '" . $iPackageTypeId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Package Type deleted successfully.';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."package_type.php?".$parameters); exit;
}
//End make deleted

//Start Change single Status
if ($iPackageTypeId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE package_type SET eStatus = '" . $status . "' WHERE iPackageTypeId = '" . $iPackageTypeId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Package Type activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Package Type inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."package_type.php?".$parameters);
        echo "test"; die;
        exit;
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){
	     $query = "UPDATE package_type SET eStatus = '" . $statusVal . "' WHERE iPackageTypeId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Package Type updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."package_type.php?".$parameters);
        exit;
}
//End Change All Selected Status

//if ($iMakeId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE make SET eStatus = '" . $status . "' WHERE iMakeId = '" . $iMakeId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = "Admin " . $status . " Successfully.";
//        header("Location:".$tconfig["tsite_url_main_admin"]."make.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."make.php?".$parameters);
//        exit;
//    }
//}
?>