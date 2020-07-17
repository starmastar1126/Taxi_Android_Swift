<?php 
	include_once('common.php');
	if(isset($_REQUEST['vCouponCode']))
	{
			$user_name=$_REQUEST['vCouponCode'];
			$sql = "SELECT * FROM coupon WHERE vCouponCode = '".$user_name."' ";
			$db_comp = $obj->MySQLSelect($sql);
			
		if(count($db_comp)>0 )
		{
				echo 0;
		}
		else
		{	
				echo 1;
		}
	}
?>