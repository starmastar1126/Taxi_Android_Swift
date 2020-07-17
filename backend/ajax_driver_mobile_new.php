<?php 
	include_once('common.php');
	/*if(isset($_REQUEST['vPhone']))
	{
			$vPhone=$_REQUEST['vPhone'];
			echo $sql = "SELECT vPhone FROM register_driver WHERE vPhone = '".$vPhone."' ";
			$db_comp = $obj->MySQLSelect($sql);
			
		if(count($db_comp)>0)
		{
				echo 'false';
		}
		else
		{	
				echo 'true';
		}
	}*/
	// added
	$iCompanyId=isset($_REQUEST['iCompanyId'])?$_REQUEST['iCompanyId']:'';
	$iDriverId=isset($_REQUEST['iDriverId'])?$_REQUEST['iDriverId']:'';
	$usertype=isset($_REQUEST['usertype'])?$_REQUEST['usertype']:'';

	if($iCompanyId !='') {
		$ssql=" AND iCompanyId !='".$iCompanyId."'";
	}else if($iDriverId != "") {
		$ssql=" AND iDriverId !='".$iDriverId."'";
	}else {
		$ssql=" ";
	}
	
	if($usertype=='company' && isset($_REQUEST['vPhone']))
	{
		$vPhone=$_REQUEST['vPhone'];
		
		$sql1 = "SELECT count('vPhone') as Total,eStatus FROM company WHERE vPhone = '".$vPhone."'".$ssql;
		$db_comp = $obj->MySQLSelect($sql1);
		
		if($db_comp[0]['Total'] > 0) {
			/*if(ucfirst($db_comp[0]['eStatus'])=='Deleted'){ 
				echo 'true';
			}
			else {
				echo 'false';
			}*/
			echo 'false';
		} else {
			echo 'true';
		}
	}
	
	if($usertype=='driver' && isset($_REQUEST['vPhone']))
	{
		$vPhone=$_REQUEST['vPhone'];
		
		$sql2 = "SELECT count('vPhone') as Total,eStatus FROM register_driver WHERE vPhone = '".$vPhone."'".$ssql;
		$db_driver = $obj->MySQLSelect($sql2);

		if($db_driver[0]['Total'] > 0) {
			/*if(ucfirst($db_driver[0]['eStatus'])=='Deleted'){ 
				echo 'true';
			} else {
				echo 'false';
			}*/
			echo 'false';
		} else {
			echo 'true';
		}
	}

	if($usertype=='company' && isset($_REQUEST['phone']))
	{
		$phone=$_REQUEST['phone'];
		
		$sql1 = "SELECT count('vPhone') as Total,eStatus FROM company WHERE vPhone = '".$phone."'".$ssql;
		$db_comp = $obj->MySQLSelect($sql1);
		
		if($db_comp[0]['Total'] > 0) {
			/*if(ucfirst($db_comp[0]['eStatus'])=='Deleted'){ 
				echo 'true';
			} else {
				echo 'false';
			} */
			echo 'false';
		} else {
			echo 'true';
		}
	}

	if($usertype=='driver' && isset($_REQUEST['phone']))
	{
		$phone=$_REQUEST['phone'];
		
		$sql2 = "SELECT count('vPhone') as Total,eStatus FROM register_driver WHERE vPhone = '".$phone."'".$ssql;
		$db_driver = $obj->MySQLSelect($sql2);

		if($db_driver[0]['Total'] > 0) {
			/*if(ucfirst($db_driver[0]['eStatus'])=='Deleted'){ 
				echo 'true';
			} else {
				echo 'false';
			}*/
			echo 'false';
		} else {
			echo 'true';
		}
	}
	