<?php 
	include_once('common.php');
	
	$ssql="";
	if($_REQUEST['uid'] != ""){
		$ssql="and iUserId != '".$_REQUEST['uid']."'";
	}
	
	if(isset($_REQUEST['id']))
	{
			$email=$_REQUEST['id'];
			$sql = "SELECT * FROM register_user WHERE vEmail = '".$email."' $ssql  ";
			$db_user = $obj->MySQLSelect($sql);
		if(count($db_user)>0 )
		{
				if($db_user[0]['eStatus']=='Deleted')
				{
						echo 2;
				}
				else
				{
						echo 0;
				}
		}
		else
		{	
				echo 1;
		}
	}
?>