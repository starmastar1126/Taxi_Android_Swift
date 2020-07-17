<?php 
//echo "here";
include 'common.php';
//print_r($_REQUEST);
$sql = "select vPhoneCode from country where vCountryCode = '".$_REQUEST['id']."'";
$db_data = $obj->MySQLSelect($sql);

$iDriverId = isset($_SESSION['sess_iUserId'])?$_SESSION['sess_iUserId']:'';
$sql="select * from register_driver where `iDriverId` = '".$iDriverId."'";
$edit_data=$obj->sql_query($sql);
if(isset($_REQUEST['id']) && $edit_data &&$_REQUEST['id'] != $edit_data[0]['vCountry'])
{
	$q = "UPDATE ";
	$tbl = 'register_driver';
	$where = " WHERE `iDriverId` = '".$iDriverId."'";
	$query = $q ." `".$tbl."` SET `ePhoneVerified` = 'No' ".$where;
	$obj->sql_query($query);

	#echo"<pre>";print_r($query);	
}

echo $db_data[0]['vPhoneCode'];
exit;
?>