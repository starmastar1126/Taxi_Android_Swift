<?php 
include_once('../common.php');
	
if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
	
	$vEmail=isset($_REQUEST['vEmail'])?$_REQUEST['vEmail']:'';
	$sql1 = "SELECT eStatus FROM register_user WHERE vEmail = '".$vEmail."'";
	$db_user = $obj->MySQLSelect($sql1);
	echo $db_user[0]['eStatus']; exit;
?>