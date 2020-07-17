<?php 
include_once("common.php");
$currDate=Date('Y-m-d');
$sql="SELECT * FROM register_driver WHERE eStatus='active'";
$db_licence_check = $obj->MySQLSelect($sql);	
for($i=0;$i<count($db_licence_check);$i++)
{
	$reqDate=$db_licence_check[$i]['dLicenceExp'];
	if(strtotime($currDate)>strtotime($reqDate))
	{
		$sql="UPDATE register_driver SET eStatus='inactive' WHERE iDriverId=".$$db_licence_check[$i]['iDriverId'];
		$db_licence_check = $obj->MySQLSelect($sql);
	}
}
?>