<?php 
	include_once('common.php');
	
	// echo "<pre>"; print_r($_REQUEST); die;
	
	$userType = isset($_REQUEST['userType']) ? $_REQUEST['userType'] : '';
	
	if($userType == 'rider'){
		$table = "register_user";
	}else{
		$table = "register_driver";
	}
	
	if(isset($_REQUEST['vPhone']))
	{
			$vPhone=$_REQUEST['vPhone'];
			$sql = "SELECT vPhone FROM $table WHERE vPhone = '".$vPhone."' ";
			$db_comp = $obj->MySQLSelect($sql);
			
		if(count($db_comp)>0)
		{
				echo 'false';
		}
		else
		{	
				echo 'true';
		}
		exit;
	}
?>