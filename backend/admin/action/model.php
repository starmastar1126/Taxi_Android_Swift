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
$iModelId = isset($_REQUEST['iModelId']) ? $_REQUEST['iModelId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
// echo "<pre>"; print_r($_REQUEST);
//Start model deleted
if ($method == 'delete' && $iModelId != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE model SET eStatus = 'Deleted' WHERE iModelId = '" . $iModelId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Model deleted successfully.';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."model.php?".$parameters); exit;
}
//End model deleted

//Start Change single Status
if ($iModelId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE model SET eStatus = '" . $status . "' WHERE iModelId = '" . $iModelId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Model activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Model inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."model.php?".$parameters);
        echo "test"; die;
        exit;
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){
		 $query = "UPDATE model SET eStatus = '" . $statusVal . "' WHERE iModelId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Model(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."model.php?".$parameters);
        exit;
}
//End Change All Selected Status

//if ($iModelId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE model SET eStatus = '" . $status . "' WHERE iModelId = '" . $iModelId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = "Admin " . $status . " Successfully.";
//        header("Location:".$tconfig["tsite_url_main_admin"]."model.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."model.php?".$parameters);
//        exit;
//    }
//}
?>