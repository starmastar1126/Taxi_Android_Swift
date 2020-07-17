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
$iMakeId = isset($_REQUEST['iMakeId']) ? $_REQUEST['iMakeId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
// echo "<pre>"; print_r($_REQUEST);
//Start make deleted
if ($method == 'delete' && $iMakeId != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE make SET eStatus = 'Deleted' WHERE iMakeId = '" . $iMakeId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Make deleted successfully.';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."make.php?".$parameters); exit;
}
//End make deleted

//Start Change single Status
if ($iMakeId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE make SET eStatus = '" . $status . "' WHERE iMakeId = '" . $iMakeId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Make activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Make inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."make.php?".$parameters);
        echo "test"; die;
        exit;
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){
	     $query = "UPDATE make SET eStatus = '" . $statusVal . "' WHERE iMakeId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Make(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."make.php?".$parameters);
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