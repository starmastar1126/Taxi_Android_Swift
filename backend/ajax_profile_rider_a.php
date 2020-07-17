<?php 
	include_once('common.php');
	//echo "<pre>";print_R($_REQUEST);exit;
	$action =isset($_REQUEST['action'])?$_REQUEST['action']:'';
    $iCompanyId = $_SESSION['sess_iCompanyId'];
	$iUserId = $_SESSION['sess_iUserId'];

	$tbl = 'register_user';
	$where = " WHERE `iUserId` = '".$iUserId."'";

	if($action == 'login')
	{
		$email = isset($_REQUEST['email'])?$_REQUEST['email']:'';
		$fname = isset($_REQUEST['fname'])?$_REQUEST['fname']:'';
		$lname = isset($_POST['lname'])?$_POST['lname']:'';
		$location = isset($_POST['country'])?$_POST['country']:'';
		$lang = isset($_POST['lang1'])?$_POST['lang1']:'';
		$vCurrencyPassenger=isset($_POST['vCurrencyPassenger']) ? $_POST['vCurrencyPassenger'] : '';
		$_SESSION["sess_vCurrency"] = $vCurrencyPassenger;
		
		$sql = "select vPhoneCode from country where vCountryCode = '".$location."'";
		$db_code = $obj->MySQLSelect($sql);

		$sql="select * from ".$tbl .$where;
		$edit_data=$obj->sql_query($sql);
		$q = "UPDATE ";
		if($_REQUEST['email'] != $edit_data[0]['vEmail'])
		{
			$query = $q ." `".$tbl."` SET `eEmailVerified` = 'No' ".$where;
			$obj->sql_query($query);
			
		}

		echo $query = "UPDATE  `".$tbl."` SET
		    `vEmail` = '".$email."',
			`vName` = '".$fname."',
			`vLastName` = '".$lname."',
			`vCountry` = '".$location."',
			`vLang` = '".$lang."',
			`vCurrencyPassenger`='" .$vCurrencyPassenger . "',
			`vPhoneCode` = '".$db_code[0]['vPhoneCode']."'
		".$where; //exit;
		$obj->sql_query($query);
		
		$_SESSION["sess_lang"]=$lang;
		//header("Location:profile_rider.php?success=1");
		exit;
	}
	if($action == 'email')
	{
		/* $email = isset($_REQUEST['email'])?$_REQUEST['email']:'';
		$lang = isset($_POST['lang'])?$_POST['lang']:'';

		$query = "UPDATE  `".$tbl."` SET
			`vEmail` = '".$email."',
			`vLang` = '".$lang."'".$where;
		$obj->sql_query($query);

		header("Location:profile_rider.php?success=1");
		exit; */
	}
	if($action == 'pass')
	{
		$npass = isset($_REQUEST['npass'])?$_REQUEST['npass']:'';
		$npass=$generalobj->encrypt_bycrypt($npass);

		$query = "UPDATE `".$tbl."` SET
			`vPassword` = '".$npass."'".$where;
		$obj->sql_query($query);

		header("Location:profile_rider.php?&success=1");
		exit;
	}
	/* code for email update quickly */
	if($action == 'email')
	{
		$email = isset($_REQUEST['email'])?$_REQUEST['email']:'';

		$query = "UPDATE `".$tbl."` SET `vEmail` = '".$email."'".$where;
		$obj->sql_query($query);

		header("Location:profile_rider.php?success=1");
		exit;
	}
	/* code for email update quickly */
	if($action == 'phone')
	{
		$phone = isset($_REQUEST['phone'])?$_REQUEST['phone']:'';

		$query = "UPDATE `".$tbl."` SET `vPhone` = '".$phone."'".$where;
		$obj->sql_query($query);

		header("Location:profile_rider.php?success=1");
		exit;
	}
	if($action == 'vat')
	{
		$vat = isset($_REQUEST['vat'])?$_REQUEST['vat']:'';

		$query = "UPDATE `".$tbl."` SET `vVat` = '".$vat."'".$where;
		$obj->sql_query($query);

		header("Location:profile.php?success=1");
		exit;
	}
?>
