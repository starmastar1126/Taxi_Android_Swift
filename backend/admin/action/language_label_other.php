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
$LanguageLabelId = isset($_REQUEST['LanguageLabelId']) ? $_REQUEST['LanguageLabelId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
//print_r($_REQUEST['iUniqueId']); die;
$vLabel = $_REQUEST['vLabel']; //die;
//$iUniqueId 	= isset($_POST['iUniqueId'])?$_POST['iUniqueId']:'';
$hdn_del_id = isset($_REQUEST['hdn_del_id2']) ? $_REQUEST['hdn_del_id2'] : '';
//$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$checkbox = isset($_REQUEST['checkbox']) ? $_REQUEST['checkbox']:'';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
// echo "<pre>"; print_r($_REQUEST);
//Start language_label deleted
if ($method == 'delete' && $vLabel != '') { 
	if(SITE_TYPE !='Demo'){
           // $query = "UPDATE language_label SET eStatus = 'Deleted' WHERE LanguageLabelId = '" . $LanguageLabelId . "'";
            $query = "DELETE FROM language_label_other WHERE vLabel = '" . $vLabel . "'";  //die;
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Language Label deleted successfully.';  //die; 
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."languages_admin.php?".$parameters); //exit;
}
//End language_label deleted

//Start Change single Status
/* if ($iUniqueId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE language_label SET eStatus = '" . $status . "' WHERE iUniqueId = '" . $iUniqueId . "'"; 
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Language Label activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Language Label inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."language_label.php?".$parameters);
        echo "test"; die;
        exit;
} */
//End Change single Status

//Start Change All Selected Status
//echo '------>'.$statusVal;die;
//$vLabel = $_REQUEST['vLabel'];

if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){		
		 //$query = "UPDATE language_label SET eStatus = '" . $statusVal . "' WHERE LanguageLabelId IN (" . $checkbox . ")";
        //echo $query = "DELETE FROM language_label_other WHERE vLabel IN ('" . $checkbox . "')"; die;         
          $query = "DELETE FROM language_label_other WHERE vLabel IN ('" . implode("', '", $checkbox) . "')"; 		 
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Language Label(s) deleted successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."languages_admin.php?".$parameters);
        exit;
}


//End Change All Selected Status

//if ($LanguageLabelId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE language_label SET eStatus = '" . $status . "' WHERE LanguageLabelId = '" . $LanguageLabelId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = "Admin " . $status . " Successfully.";
//        header("Location:".$tconfig["tsite_url_main_admin"]."language_label.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."language_label.php?".$parameters);
//        exit;
//    }
//}
?>