<?php 
include_once('common.php');
$tbl_name 	= 'register_driver';
$script="payment_request";
$generalobj->check_member_login();
$abc = 'admin,driver,company';
$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$generalobj->setRole($abc,$url);
$action=(isset($_REQUEST['action'])?$_REQUEST['action']:'');

$vHolderName = (isset($_REQUEST['vHolderName1'])?$_REQUEST['vHolderName1']:'');
$vBankName = (isset($_REQUEST['vBankName1'])?$_REQUEST['vBankName1']:'');
$iBankAccountNo =(isset($_REQUEST['iBankAccountNo1'])?$_REQUEST['iBankAccountNo1']:'');
$BICSWIFTCode =(isset($_REQUEST['BICSWIFTCode1'])?$_REQUEST['BICSWIFTCode1']:'');
$vBankBranch = (isset($_REQUEST['vBankBranch1'])?$_REQUEST['vBankBranch1']:'');	
//echo "<pre>"; print_r($_REQUEST); exit;
	
	if($_SESSION['sess_user']== "driver")
	{
	  $sql = "SELECT * FROM register_".$_SESSION['sess_user']." WHERE iDriverId='".$_SESSION['sess_iUserId']."'";
	  $db_booking = $obj->MySQLSelect($sql);

	  $sql = "SELECT fThresholdAmount, Ratio, vName, vSymbol FROM currency WHERE vName='".$db_booking[0]['vCurrencyDriver']."'";
	  $db_curr_ratio = $obj->MySQLSelect($sql);
	}
	else
	{
	  $sql = "SELECT * FROM register_".$_SESSION['sess_user']." WHERE iUserId='".$_SESSION['sess_iUserId']."'";
	  $db_booking = $obj->MySQLSelect($sql);  

	  $sql = "SELECT fThresholdAmount, Ratio, vName, vSymbol FROM currency WHERE vName='".$db_booking[0]['vCurrencyPassenger']."'";
	  $db_curr_ratio = $obj->MySQLSelect($sql);
	}
	$tripcursymbol=$db_curr_ratio[0]['vSymbol'];
	$tripcur=$db_curr_ratio[0]['Ratio'];
	$tripcurname=$db_curr_ratio[0]['vName'];
	$tripcurthholsamt=$db_curr_ratio[0]['fThresholdAmount'];
	
	
	if($action=="send_equest")
	{
		
		$iTripId = $_REQUEST['iTripId'];
		if(is_array($iTripId)){
			$iTripId = implode(",",$iTripId);
		}
		
		$sql="SELECT * From trips WHERE iTripId IN($iTripId)";
		$db_dtrip = $obj->MySQLSelect($sql);
		
		$tot_records = count($db_dtrip);
		$payout_limit=0;
		if(count($db_dtrip)>0)
		{
			for($i=0;$i<count($db_dtrip);$i++)
			{
				$fare=$generalobj->trip_currency_payment($db_dtrip[$i]['fTripGenerateFare'],$db_dtrip[$i]['fRatio_'.$tripcurname]);
				// $fare=$db_dtrip[$i]['iFare'];
				$comission=$generalobj->trip_currency_payment($db_dtrip[$i]['fCommision'],$db_dtrip[$i]['fRatio_'.$tripcurname]);
				// $comission=$db_dtrip[$i]['fCommision'];
				$payment=$fare-$comission;
				$total+=$payment;
				if($tot_records == ($i+1)){
					$seperator = "";
				}else{
					$seperator = ",";
				}
				$maildata['TripIds'].= $db_dtrip[$i]['vRideNo']."".$seperator;
			}
		}

		if($total>$tripcurthholsamt)
		{
			
			$data = array('ePayment_request'=>'Yes');
			$where = " iTripId IN (".$iTripId.")";
			$res = $obj->MySQLQueryPerform("trips",$data,'update',$where);
			if($res)
			{
				#echo "<script>alert('Request Send Successfully');document.location='payment_request.php'; </script>";
				
				$maildata['Name'] = $db_booking[0]['vName'].' '.$db_booking[0]['vLastName'];
				$maildata['vEmail'] = $db_booking[0]['vEmail'];
				$maildata['Total_Amount'] = $generalobj->trip_currency($total);
				$maildata['Account_Name'] = $vHolderName;
				$maildata['Bank_Name'] = $vBankName;
				$maildata['Account_Number'] = $iBankAccountNo;
				$maildata['BIC/SWIFT_Code'] = $BICSWIFTCode;
				$maildata['Bank_Branch'] = $vBankBranch;
				
				//to send email 
				$generalobj->send_email_user("PAYMENT_REQUEST_ADMIN",$maildata);
				
				header("Location:payment_request.php?success=1&var_msg=".$langage_lbl['LBL_SEND_REQUEST_SUCCESSFULLY']."");
			}
			
		}
		else
		{
			$var_msg = $langage_lbl['LBL_THRESHOLDAMOUNT_NOTE2']." ".$tripcursymbol." ".number_format($tripcurthholsamt,2, '.', '');
			header("Location:payment_request.php?success=0&var_msg=".$var_msg."");
			exit; 
		}
	}
	
?>