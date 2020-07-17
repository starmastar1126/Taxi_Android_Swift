<?php 
	include_once('common.php');
	
	$iCompanyId=isset($_REQUEST['iCompanyId'])?$_REQUEST['iCompanyId']:'';
	$iAdminId=isset($_REQUEST['iAdminId'])?$_REQUEST['iAdminId']:'';
	$iDriverId=isset($_REQUEST['iDriverId'])?$_REQUEST['iDriverId']:'';
	$iUserId=isset($_REQUEST['iUserId'])?$_REQUEST['iUserId']:'';
	$usertype=isset($_REQUEST['usertype'])?$_REQUEST['usertype']:'';
	//echo '<pre>'; print_r($_POST); exit;
	if($iCompanyId !='') {
		$ssql=" AND iCompanyId !='".$iCompanyId."'";
	}else if($iAdminId != "") {
		$ssql=" AND iAdminId !='".$iAdminId."'";
	}else if($iDriverId != "") {
		$ssql=" AND iDriverId !='".$iDriverId."'";
	}else if($iUserId != "") {
		$ssql=" AND iUserId !='".$iUserId."'";
	}else {
		$ssql=" ";
	}
	
	if(isset($_REQUEST['iAdminId']) && isset($_REQUEST['vEmail']))
	{
		$email=$_REQUEST['vEmail'];
		
		$sql1 = "SELECT count('vEmail') as Total,eStatus FROM administrators WHERE vEmail = '".$email."'".$ssql;
		$db_adm = $obj->MySQLSelect($sql1);
		
		/*$sql2 = "SELECT count('vEmail') as Total,eStatus FROM register_driver WHERE vEmail = '".$email."' ";
		$db_driver = $obj->MySQLSelect($sql2);
		
		$sql2 = "SELECT count('vEmail') as Total,eStatus FROM company WHERE vEmail = '".$email."' ";
		$db_comp = $obj->MySQLSelect($sql2);
		
		$sql2 = "SELECT count('vEmail') as Total,eStatus FROM register_user WHERE vEmail = '".$email."' ";
		$db_user = $obj->MySQLSelect($sql2);*/
		
		if($db_adm[0]['Total'] > 0 ) {
			if(ucfirst($db_adm[0]['eStatus'])=='Deleted'){ 
				echo 'deleted';
			} else {
				echo 'false';
			}
		} else {
			echo 'true';
		}
	}
	
	if($usertype=='company' && isset($_REQUEST['vEmail']))
	{
		$email=$_REQUEST['vEmail'];
		
		$sql2 = "SELECT count('vEmail') as Total,eStatus FROM company WHERE vEmail = '".$email."'";
		$db_comp = $obj->MySQLSelect($sql2);
		
		if($db_comp[0]['Total'] > 0) {
			//comented for get email validation for deleted reord : 
			 if(ucfirst($db_comp[0]['eStatus'])=='Deleted'){ 
				 echo 'deleted';
			 } else {
				 echo 'false';
			 } 
		} else {
			echo 'true';
		}
	}

	if($usertype=='driver' && isset($_REQUEST['vEmail']))
	{
		$email=$_REQUEST['vEmail'];
		
		/*$sql1 = "SELECT count('vEmail') as Total,eStatus FROM administrators WHERE vEmail = '".$email."'";
		$db_adm = $obj->MySQLSelect($sql1);*/
		
		$sql2 = "SELECT count('vEmail') as Total,eStatus FROM register_driver WHERE vEmail = '".$email."'".$ssql;
		$db_driver = $obj->MySQLSelect($sql2);
		
		/*$sql2 = "SELECT count('vEmail') as Total,eStatus FROM company WHERE vEmail = '".$email."'";
		$db_comp = $obj->MySQLSelect($sql2);*/
		
		if($db_driver[0]['Total'] > 0) {
			//comented for get email validation for deleted reord : 
			if(ucfirst($db_driver[0]['eStatus'])=='Deleted'){ 
				 echo 'deleted';
			 } else {
				 echo 'false';
			 } 
		} else {
			echo 'true';
		}
	}
	
	if(isset($_REQUEST['iUserId']) && isset($_REQUEST['vEmail']))
	{
		$email=$_REQUEST['vEmail'];
		
		/*$sql1 = "SELECT count('vEmail') as Total,eStatus FROM administrators WHERE vEmail = '".$email."'";
		$db_adm = $obj->MySQLSelect($sql1);*/
		
		$sql2 = "SELECT count('vEmail') as Total,eStatus FROM register_user WHERE vEmail = '".$email."'".$ssql;
		$db_user = $obj->MySQLSelect($sql2);
		
		/*$sql2 = "SELECT count('vEmail') as Total,eStatus FROM company WHERE vEmail = '".$email."'";
		$db_comp = $obj->MySQLSelect($sql2);*/
		
		 if($db_user[0]['Total'] > 0) {
			if(ucfirst($db_user[0]['eStatus'])=='Deleted'){ 
				echo 'deleted';
			} else {
				echo 'false';
			} 
		} else {
			echo 'true';
		}
	}
?>