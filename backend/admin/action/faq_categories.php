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
$iFaqcategoryId = isset($_REQUEST['iFaqcategoryId']) ? $_REQUEST['iFaqcategoryId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
//print_r($_REQUEST['iUniqueId']); die;
$iUniqueId = $_REQUEST['iUniqueId'];
//$iUniqueId 	= isset($_POST['iUniqueId'])?$_POST['iUniqueId']:'';
$hdn_del_id = isset($_REQUEST['hdn_del_id2']) ? $_REQUEST['hdn_del_id2'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';

$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';

//Start faq_categories deleted
if ($method == 'delete' && $iUniqueId != '') { 
	if(SITE_TYPE !='Demo'){
           // $query = "UPDATE faq_categories SET eStatus = 'Deleted' WHERE iFaqcategoryId = '" . $iFaqcategoryId . "'";
            $query = "DELETE FROM faq_categories WHERE iUniqueId = '" . $iUniqueId . "'"; //die;
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'FAQ Category deleted successfully.';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."faq_categories.php?".$parameters); exit;
}
//End faq_categories deleted

//Start Change single Status
if ($iUniqueId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE faq_categories SET eStatus = '" . $status . "' WHERE iUniqueId = '" . $iUniqueId . "'"; 
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'FAQ Category activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'FAQ Category inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."faq_categories.php?".$parameters);
        echo "test"; die;
        exit;
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal == "Deleted") {
	if(SITE_TYPE !='Demo'){
		// $query = "UPDATE faq_categories SET eStatus = '" . $statusVal . "' WHERE iFaqcategoryId IN (" . $checkbox . ")";
		 $query = "DELETE FROM faq_categories WHERE iUniqueId IN (" . $checkbox . ")";//die;
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'FAQ Category(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."faq_categories.php?".$parameters);
        exit;
} else {
	if(SITE_TYPE !='Demo'){
		 $query = "UPDATE faq_categories SET eStatus = '" . $statusVal . "' WHERE iUniqueId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'FAQ Category(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."faq_categories.php?".$parameters);
        exit;
}
//End Change All Selected Status

//if ($iFaqcategoryId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE faq_categories SET eStatus = '" . $status . "' WHERE iFaqcategoryId = '" . $iFaqcategoryId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = "Admin " . $status . " Successfully.";
//        header("Location:".$tconfig["tsite_url_main_admin"]."faq_categories.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."faq_categories.php?".$parameters);
//        exit;
//    }
//}
?>