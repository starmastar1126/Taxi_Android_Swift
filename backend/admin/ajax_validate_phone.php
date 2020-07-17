<?php 
	include_once('../common.php');
	
	$iCompanyId=isset($_REQUEST['iCompanyId'])?$_REQUEST['iCompanyId']:'';
	$iAdminId=isset($_REQUEST['iAdminId'])?$_REQUEST['iAdminId']:'';
	$iDriverId=isset($_REQUEST['iDriverId'])?$_REQUEST['iDriverId']:'';
	$iUserId=isset($_REQUEST['iUserId'])?$_REQUEST['iUserId']:'';
	if($iCompanyId !='') {
		$ssql=" AND iCompanyId !='".$iCompanyId."'";
	}else if($iDriverId != "") {
		$ssql=" AND iDriverId !='".$iDriverId."'";
	}else if($iUserId != "") {
		$ssql=" AND iUserId !='".$iUserId."'";
	}else {
		$ssql=" ";
	}
	
	if(isset($_REQUEST['iCompanyId']) && isset($_REQUEST['vPhone']))
	{
		$vPhone=$_REQUEST['vPhone'];
		
		$sql1 = "SELECT count('vPhone') as Total,eStatus FROM company WHERE vPhone = '".$vPhone."'".$ssql;
		$db_comp = $obj->MySQLSelect($sql1);
		
		if($db_comp[0]['Total'] > 0) {
/*			if(ucfirst($db_comp[0]['eStatus'])=='Deleted'){ 
				echo 'false';
			} else {
				echo 'false';
			}*/
			echo 'false';
		} else {
			echo 'true';
		}
	}
	
	if(isset($_REQUEST['iDriverId']) && isset($_REQUEST['vPhone']))
	{
		$vPhone=$_REQUEST['vPhone'];
		
		$sql2 = "SELECT count('vPhone') as Total,eStatus FROM register_driver WHERE vPhone = '".$vPhone."'".$ssql;
		$db_driver = $obj->MySQLSelect($sql2);

		if($db_driver[0]['Total'] > 0) {
/*			if(ucfirst($db_driver[0]['eStatus'])=='Deleted'){ 
				echo 'false';
			} else {
				echo 'false';
			}*/
			echo 'false';
		} else {
			echo 'true';
		}
	}
	
	if(isset($_REQUEST['iUserId']) && isset($_REQUEST['vPhone']))
	{
		$vPhone=$_REQUEST['vPhone'];

		$sql2 = "SELECT count('vPhone') as Total,eStatus FROM register_user WHERE vPhone = '".$vPhone."'".$ssql;
		$db_user = $obj->MySQLSelect($sql2);

		if($db_user[0]['Total'] > 0) {
/*			if(ucfirst($db_user[0]['eStatus'])=='Deleted'){ 
				echo 'false';
			} else {
				echo 'false';
			}*/
			echo 'false';
		} else {
			echo 'true';
		}
	}
?>