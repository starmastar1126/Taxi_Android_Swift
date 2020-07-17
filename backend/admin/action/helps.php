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
$iHelpsId = isset($_REQUEST['iHelpsId']) ? $_REQUEST['iHelpsId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';

$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
//echo '<pre>'; print_r($_REQUEST); echo '</pre>'; die;
//Start faqs deleted
if ($method == 'delete' && $iHelpsId != '') {
	if(SITE_TYPE !='Demo'){
           // $query = "UPDATE faqs SET eStatus = 'Deleted' WHERE iFaqId = '" . $iFaqId . "'";
            $query = "DELETE FROM helps WHERE iHelpsId = '" . $iHelpsId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Helps deleted successfully.';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."helps.php?".$parameters); exit;
}
//End faqs deleted

//Start Change single Status
if ($iHelpsId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE helps SET eStatus = '" . $status . "' WHERE iHelpsId = '" . $iHelpsId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Helps activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Helps inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."helps.php?".$parameters);
        echo "test"; die;
        exit;
}
//End Change single Status

//Start Change All Deleted Selected Status
//echo '<pre>'; print_r($_REQUEST); echo '</pre>'; die;

if ($checkbox != ''  && $statusVal == 'Deleted') { 
   if (SITE_TYPE != 'Demo') {
	  
       $query = "DELETE FROM helps WHERE iHelpsId IN (" . $checkbox . ")"; //die;    
	   
	   $obj->sql_query($query);
	   $status='deleted';
       $_SESSION['success'] = '1';
       $_SESSION['var_msg'] = "Helps(s) " . $status . " Successfully.";
       header("Location:".$tconfig["tsite_url_main_admin"]."helps.php?".$parameters);
       exit;
   } else {
       $_SESSION['success']=2;
       header("Location:".$tconfig["tsite_url_main_admin"]."helps.php?".$parameters);
       exit;
   }
 
}
//End Change All Deleted Selected Status


//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") { 
	if(SITE_TYPE !='Demo'){
		 $query = "UPDATE helps SET eStatus = '" . $statusVal . "' WHERE iHelpsId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Helps(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."helps.php?".$parameters);
        exit;
}
//End Change All Selected Status



?>