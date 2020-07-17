<?php 
	include_once('common.php');
	$ssql = "";
	if(isset($_REQUEST['iUserId']) && $_REQUEST['iUserId'] != "") {
		$ssql = " AND iUserId!='".$_REQUEST['iUserId']."'";
	}
	
	if(isset($_REQUEST['vPhone']))
	{
		$vPhone=$_REQUEST['vPhone'];
		$sql = "SELECT vPhone FROM register_user WHERE vPhone = '".$vPhone."'".$ssql;
		$db_user = $obj->MySQLSelect($sql);
			
		if(count($db_user)>0)
		{
				echo 'false';
		}
		else
		{	
				echo 'true';
		}
	}
?>