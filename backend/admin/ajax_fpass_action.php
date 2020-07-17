<?php 
	include_once('../common.php');
	$email = isset($_POST['femail'])?$_POST['femail']:'';
	//$action = isset($_POST['action'])?$_POST['action']:'';


		$sql = "SELECT * from administrators WHERE vEmail = '".$email."' and eStatus != 'Deleted'";
		$db_login = $obj->MySQLSelect($sql);

		if(count($db_login)>0)
		{
			$status = $generalobj->send_email_user("CUSTOMER_FORGETPASSWORD",$db_login);
			if($status == 1)
			{
				$var_msg = "Your Password has been sent Successfully.";
				$error_msg = "1";
			}
			else
			{
				$var_msg = "Error in Sending password.";
				$error_msg = "0";
			}
		}
			else
			{
				 $var_msg = "Sorry ! The Email address you have entered is not found.";
				 $error_msg = "0";
			}

?>
