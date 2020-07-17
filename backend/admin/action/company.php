<?php 
include_once('../../common.php');

if (!isset($generalobjCompany)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjCompany = new General_admin();
}
$generalobjCompany->check_member_login();

$reload = $_SERVER['REQUEST_URI']; 

$urlparts = explode('?',$reload);
$parameters = $urlparts[1];

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$iCompanyId = isset($_REQUEST['iCompanyId']) ? $_REQUEST['iCompanyId'] : '';
$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
$statusVal = isset($_REQUEST['statusVal']) ? $_REQUEST['statusVal'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';
//echo "<pre>"; print_r($_REQUEST); die;
//Start make deleted
if ($method == 'delete' && $iCompanyId != '') {
	if(SITE_TYPE !='Demo'){
		
             $qur1 = "UPDATE register_driver SET register_driver.iCompanyId=1 WHERE register_driver.iCompanyId=$iCompanyId ";
	         $res1 = $obj->sql_query($qur1);
     
             $qur3 = "UPDATE driver_vehicle SET driver_vehicle.iCompanyId=1 WHERE driver_vehicle.iCompanyId=$iCompanyId ";
	         $res3 = $obj->sql_query($qur3);
      
             if($res1==1)
             {
              $qur2 = "UPDATE company SET eStatus = 'Deleted' WHERE iCompanyId = '" . $iCompanyId . "'";
              $res2 = $obj->sql_query($qur2);
		     }
            
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Record deleted successfully.';   
	} else {
            $_SESSION['success'] = '2';
	}
	header("Location:".$tconfig["tsite_url_main_admin"]."company.php?".$parameters); exit;
}
//End make deleted

//Start Change single Status
if ($iCompanyId != '' && $status != '') {
	if(SITE_TYPE !='Demo'){
            $query = "UPDATE company SET eStatus = '" . $status . "' WHERE iCompanyId = '" . $iCompanyId . "'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if($status == 'Active') {
                   $_SESSION['var_msg'] = 'Company activated successfully.';
            }else {
                   $_SESSION['var_msg'] = 'Company inactivated successfully.';
            }
	}
	else{
            $_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."company.php?".$parameters);
        exit;
}
//End Change single Status

//Start Change All Selected Status
if($checkbox != "" && $statusVal != "") {
	if(SITE_TYPE !='Demo'){
		 $query = "UPDATE company SET eStatus = '" . $statusVal . "' WHERE iCompanyId IN (" . $checkbox . ")";
		 $obj->sql_query($query);
		 $_SESSION['success'] = '1';
		 $_SESSION['var_msg'] = 'Company(s) updated successfully.';
	}
	else{
		$_SESSION['success']=2;
	}
        header("Location:".$tconfig["tsite_url_main_admin"]."company.php?".$parameters);
        exit;
}
//End Change All Selected Status

?>