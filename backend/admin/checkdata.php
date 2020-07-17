<?php  
include_once('../common.php');
if(isset($_POST['vCouponCode']))
{
	$vCouponCode=$_POST['vCouponCode'];

	$checkdata=" SELECT vCouponCode FROM coupon WHERE vCouponCode='$vCouponCode' ";

	// $query=mysqli_query($checkdata);
	$query=$obj->MySQLSelect($checkdata);

	if(!empty($query) && count($query)>0)
	{
	echo "Coupon Code Already Exist";
	}
	else
	{
	echo "OK";
	}
exit();
}
?>