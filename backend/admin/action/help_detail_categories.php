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
$iHelpDetailCategoryId = isset($_REQUEST['iHelpDetailCategoryId']) ? $_REQUEST['iHelpDetailCategoryId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$iUniqueId = $_REQUEST['iUniqueId'];
$hdn_del_id = isset($_REQUEST['hdn_del_id2']) ? $_REQUEST['hdn_del_id2'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';

$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';

//Start help detail cat deleted
if ($method == 'delete' && $iUniqueId != '') { 
	if(SITE_TYPE !='Demo'){           
            $query = "DELETE FROM help_detail_categories WHERE iUniqueId = '" . $iUniqueId . "'"; //die;
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Help Detail Category deleted successfully.';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."help_detail_categories.php?".$parameters); exit;
}
//End faq_categories deleted

//Start Change single Status
if ($iUniqueId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE help_detail_categories SET eStatus = '" . $status . "' WHERE iUniqueId = '" . $iUniqueId . "'"; 
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Help Detail Category activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Help Detail Category inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."help_detail_categories.php?".$parameters);
        echo "test"; die;
        exit;
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal == "Deleted") {
	if(SITE_TYPE !='Demo'){
		
		 $query = "DELETE FROM help_detail_categories WHERE iUniqueId IN (" . $checkbox . ")";//die;
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Help Detail Category(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."help_detail_categories.php?".$parameters);
        exit;
} else {
	if(SITE_TYPE !='Demo'){
		 $query = "UPDATE help_detail_categories SET eStatus = '" . $statusVal . "' WHERE iUniqueId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Help Detail Category(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."help_detail_categories.php?".$parameters);
        exit;
}

?>