<?php 
	include_once('common.php');
	if(isset($_REQUEST['id']))
	{
			$user_name=$_REQUEST['id'];
			$sql = "SELECT * FROM company WHERE vLoginId = '".$user_name."' ";
			$db_comp = $obj->MySQLSelect($sql);
			
			$sql = "SELECT * FROM register_driver WHERE vLoginId = '".$user_name."' ";
			$db_driver = $obj->MySQLSelect($sql);
		if(count($db_comp)>0 or count($db_driver)>0)
		{
				echo 0;
		}
		else
		{	
				echo 1;
		}
	}
?>