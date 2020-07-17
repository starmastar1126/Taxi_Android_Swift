<?php 
	include_once('common.php');
	
	$ssql="";
	$ssql1="";
	$usertype = $_SESSION['sess_user'];
	$type = $_REQUEST['usr'];
	
	if($_REQUEST['uid'] != "" && $usertype == 'company')
	{
		$ssql="and iCompanyId != '".$_REQUEST['uid']."'";
	} 

	if($_REQUEST['uid'] != "" && $usertype == 'driver'){
		$ssql1="and iDriverId != '".$_REQUEST['uid']."'";
	} 

	if($_REQUEST['uid'] != "" && $usertype == 'rider'){
		$ssql2="and iUserId != '".$_REQUEST['uid']."'";
	}

	if($_REQUEST['uid'] != "" && $usertype == 'company' && $type =='driver'){
		$ssql1="and iDriverId != '".$_REQUEST['uid']."'";
	}
	
	
	if(isset($_REQUEST['id']) && $usertype == 'company')
	{
		$email=$_REQUEST['id'];
		if($usertype == 'company' && $type == 'company') {
			$sql = "SELECT vEmail,eStatus FROM company WHERE vEmail = '".$email."' $ssql";
			$db_user = $obj->MySQLSelect($sql);
		}

		if($usertype == 'company' && $type == 'driver'){
			$sql = "SELECT vEmail,eStatus FROM register_driver WHERE vEmail = '".$email."' $ssql1";
			$db_user = $obj->MySQLSelect($sql);
		}

		if(count($db_user)>0)
		{
			if(ucfirst($db_user[0]['eStatus'])=='Deleted'){ 
				echo 'deleted';
			} else {
				echo 0;
			}

		} else {
			echo 1;
		}
		
	}

	if(isset($_REQUEST['id']) && $usertype == 'driver')
	{
		$email=$_REQUEST['id'];

		if($usertype == 'driver' || ($usertype == 'company' && $type =='company')){
			$sql = "SELECT vEmail,eStatus FROM register_driver WHERE vEmail = '".$email."' $ssql1";
			$db_user = $obj->MySQLSelect($sql);
		}

		if(count($db_user)>0)
		{
			if(ucfirst($db_user[0]['eStatus'])=='Deleted'){ 
				echo 'deleted';
			} else {
				echo 0;
			}

		} else {
			echo 1;
		}
		
	}

	if(isset($_REQUEST['id']) && $usertype == 'rider')
	{
		$email=$_REQUEST['id'];
		if($usertype == 'rider'){
		    $sql4 = "SELECT vEmail,eStatus FROM register_user WHERE vEmail = '".$email."'".$ssql2; //exit;
			$db_user = $obj->MySQLSelect($sql4);
		}
		
		if(count($db_user)>0)
		{
			if(ucfirst($db_user[0]['eStatus'])=='Deleted'){ 
				echo 'deleted';
			} else {	
				echo 0;
			}

		} else {
			echo 1;
		}
		
	}
?>