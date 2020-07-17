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
$iHelpscategoryId = isset($_REQUEST['iHelpscategoryId']) ? $_REQUEST['iHelpscategoryId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';

$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
//echo '<pre>'; print_r($_REQUEST); echo '</pre>'; die;

if ($method == 'delete' && $iHelpscategoryId != '') {
	if(SITE_TYPE !='Demo'){           
            $query = "DELETE FROM helps_categories WHERE iHelpscategoryId = '" . $iHelpscategoryId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Helps categories deleted successfully.';   
	}
	else{
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."helps_categories.php?".$parameters); exit;
}
//End faqs deleted

//Start Change single Status
if ($iHelpscategoryId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE helps_categories SET eStatus = '" . $status . "' WHERE iHelpscategoryId = '" . $iHelpscategoryId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Helps Categories activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Helps Categories inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."helps_categories.php?".$parameters);
        echo "test"; die;
        exit;
}
//End Change single Status

//Start Change All Deleted Selected Status
//echo '<pre>'; print_r($_REQUEST); echo '</pre>'; die;

if ($checkbox != ''  && $statusVal == 'Deleted') { 
   if (SITE_TYPE != 'Demo') {
	   //echo '<pre>'; print_r($status); echo '</pre>';die;
      
       $query = "DELETE FROM helps_categories WHERE iHelpscategoryId IN (" . $checkbox . ")"; //die;    
	   
	   $obj->sql_query($query);
	   $status='deleted';
       $_SESSION['success'] = '1';
       $_SESSION['var_msg'] = "Helps Categories(s) " . $status . " Successfully.";
       header("Location:".$tconfig["tsite_url_main_admin"]."helps_categories.php?".$parameters);
       exit;
   } else {
       $_SESSION['success']=2;
       header("Location:".$tconfig["tsite_url_main_admin"]."helps_categories.php?".$parameters);
       exit;
   }
   //header("Location:".$tconfig["tsite_url_main_admin"]."faq.php?".$parameters);
}
//End Change All Deleted Selected Status


//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") { 
	if(SITE_TYPE !='Demo'){
		 $query = "UPDATE helps_categories SET eStatus = '" . $statusVal . "' WHERE iHelpscategoryId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Helps Categories(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."helps_categories.php?".$parameters);
        exit;
}
//End Change All Selected Status



?>