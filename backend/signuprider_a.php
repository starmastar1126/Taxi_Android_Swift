<?php  include_once("common.php");

//echo "<pre>";print_r($_POST);exit;
if($_POST)
{
	/*if($_POST['vPassword'] != $_POST['vRPassword'])
	{
		$generalobj->getPostForm($_POST,"Password doesn't match","SignUp");
		exit;
	}*/
	$msg= $generalobj->checkDuplicateFront('vEmail', "register_user" , Array('vEmail'),$tconfig["tsite_url"]."sign-up_rider.php?error=1&var_msg=Email already Exists", "Email already Exists","" ,"");


	$eReftype = "Rider";
	$Data['vRefCode'] = $generalobj->ganaraterefercode($eReftype);
	$Data['iRefUserId'] = !empty($_POST['iRefUserId'])?$_POST['iRefUserId']:'0';
	$Data['eRefType'] = !empty($_POST['eRefType'])?$_POST['eRefType']:'Rider';
	$Data['vName'] = isset($_POST['vName'])?$_POST['vName']:'';
	$Data['vLang'] = isset($_POST['vLang'])?$_POST['vLang']:'';
	$Data['vLastName'] = isset($_POST['vLastName'])?$_POST['vLastName']:'';

	$Data['vPassword'] = $generalobj->encrypt_bycrypt($_REQUEST['vPassword']);
	$Data['vEmail'] = isset($_POST['vEmail'])?$_POST['vEmail']:'';
	$Data['vPhone'] = isset($_POST['vPhone'])?$_POST['vPhone']:'';
	$Data['vCountry']=isset($_POST['vCountry'])?$_POST['vCountry']:'';
	$Data['vPhoneCode'] = isset($_POST['vPhoneCode'])?$_POST['vPhoneCode']:'';
	$Data['vZip'] = isset($_POST['vZip'])?$_POST['vZip']:'';

	$Data['vInviteCode'] = isset($_POST['vInviteCode'])?$_POST['vInviteCode']:'';
	$Data['vCurrencyPassenger'] = isset($_POST['vCurrencyPassenger'])?$_POST['vCurrencyPassenger']:'';
	//$Data['eGender'] = $_POST['eGender'];
	$Data['dRefDate'] =  Date('Y-m-d H:i:s');
    $Data['tRegistrationDate']  =  Date('Y-m-d H:i:s');
    // echo json_encode($Data);
    // exit;

	if(SITE_TYPE=='Demo')
	{
		$Data['eStatus'] = 'Active';
	}

	$id = $obj->MySQLQueryPerform("register_user",$Data,'insert');
	
//        $eFor = "Referrer";
//	$tDescription = "Referal amount credit ".$REFERRAL_AMOUNT." into your account";
//	$dDate = Date('Y-m-d H:i:s');
//	$ePaymentStatus = "Unsettelled";
//	$REFERRAL_AMOUNT; 
//	if($_POST['vRefCode'] != "" && !empty($_POST['vRefCode'])){
//		$generalobj->InsertIntoUserWallet($_POST['iRefUserId'],$_POST['eRefType'],$REFERRAL_AMOUNT,'Credit',0,$eFor,$tDescription,$ePaymentStatus,$dDate);
//	}
        
	if($id != "")
	{
		$_SESSION['sess_iUserId'] = $id;
        $_SESSION["sess_vName"] = $Data['vName'].' '.$Data['vLastName'];
		$_SESSION["sess_company"] = " ";
        $_SESSION["sess_vEmail"] = $Data['vEmail'];
		$_SESSION["sess_user"] = "rider";
		$_SESSION["sess_vCurrency"] = $Data['vCurrencyPassenger'];
		$maildata['EMAIL'] = $_SESSION["sess_vEmail"];
        $maildata['NAME'] = $_SESSION["sess_vName"];
        $maildata['PASSWORD'] = $langage_lbl["LBL_PASSWORD"].": ".$_REQUEST['vPassword'];
        $maildata['SOCIALNOTES'] = '';
	    $generalobj->send_email_user("MEMBER_REGISTRATION_USER",$maildata);
		if($_REQUEST['depart'] != "" && $_REQUEST['depart'] == 'mobi') {
			header("Location:mobi");
			exit;
		}
		header("Location:profile_rider.php");
		exit;
	}
	}
?>
