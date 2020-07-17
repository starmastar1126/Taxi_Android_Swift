<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$countryid = isset($_REQUEST['countryid']) ? $_REQUEST['countryid'] : ''; 
if($countryid != ""){
	$sql="select vCountryCode from country where iCountryId =".$countryid;
	$data = $obj->MySQLSelect($sql);
	
	echo $data[0]['vCountryCode'];
}

?>