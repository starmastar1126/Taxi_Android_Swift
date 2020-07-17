<?php 
	include_once('common.php');
	if(isset($_REQUEST['id']))
	{
			$vPhone=$_REQUEST['id'];
			$sql = "SELECT vPhone FROM register_driver WHERE vPhone = '".$vPhone."' ";
			$db_comp = $obj->MySQLSelect($sql);
			
		if(count($db_comp)>0)
		{
				echo 0;
		}
		else
		{	
				echo 1;
		}
	}
?>