<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
function converToTz($time, $toTz, $fromTz,$dateFormat="Y-m-d H:i:s") {
    $date = new DateTime($time, new DateTimeZone($fromTz));
    $date->setTimezone(new DateTimeZone($toTz));
    $time = $date->format($dateFormat);
    return $time;
}
function get_currency($from_Currency, $to_Currency, $amount) {
	$forignalamount = $amount;
	$amount = urlencode($amount);
	$from_Currency = urlencode($from_Currency);
	$to_Currency = urlencode($to_Currency);
	//$url = "http://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";
	$url = "https://finance.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";
	$ch = curl_init();
	$timeout = 0;
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt ($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$rawdata = curl_exec($ch);
	curl_close($ch);
	$data = explode('bld>', $rawdata);
	$data = explode($to_Currency, $data[1]);
	$ftollprice = round($data[0], 2);
	if($ftollprice == 0 || $ftollprice == 0.00){
	$ftollprice = $amount;
	} 
	//return round($data[0], 2);
	return $ftollprice;
}
$generalobjAdmin->check_member_login();

$tbl_name = 'register_user';
$tbl_name1 = 'cab_booking';
$vName = isset($_POST['vName']) ? $_POST['vName'] : '';
$vLastName = isset($_POST['vLastName']) ? $_POST['vLastName'] : '';
$vEmail = isset($_POST['vEmail']) ? $_POST['vEmail'] : '';
$vPassword = isset($_POST['vPassword']) ? $_POST['vPassword'] : '';
$vPhone = isset($_POST['vPhone']) ? $_POST['vPhone'] : '';
$vPhoneCode = isset($_POST['vPhoneCode']) ? $_POST['vPhoneCode'] : '';
$vCountry = isset($_POST['vCountry']) ? $_POST['vCountry'] : '';
$vCity = isset($_POST['vCity']) ? $_POST['vCity'] : '';
$eStatus = 'Active';
$vInviteCode = isset($_POST['vInviteCode']) ? $_POST['vInviteCode'] : '';
$vImgName = isset($_POST['vImgName']) ? $_POST['vImgName'] : '';
$tPackageDetails = isset($_POST['tPackageDetails']) ? $_POST['tPackageDetails'] : '';
$tDeliveryIns = isset($_POST['tDeliveryIns']) ? $_POST['tDeliveryIns'] : '';
$tPickUpIns = isset($_POST['tPickUpIns']) ? $_POST['tPickUpIns'] : '';
$vCurrencyPassenger = isset($_POST['vCurrencyPassenger']) ? $_POST['vCurrencyPassenger'] : '';
$vPass = $generalobj->encrypt_bycrypt($vPassword);
$eType = isset($_POST['eType']) ? $_POST['eType'] : '';

$fTollPrice = isset($_REQUEST["fTollPrice"]) ? $_REQUEST["fTollPrice"] : '';
$vTollPriceCurrencyCode = isset($_REQUEST["vTollPriceCurrencyCode"]) ? $_REQUEST["vTollPriceCurrencyCode"] : '';
$eTollSkipped = isset($_REQUEST["eTollSkipped"]) ? $_REQUEST["eTollSkipped"] : 'Yes';
$eFemaleDriverRequest    = isset($_REQUEST["eFemaleDriverRequest"]) ? $_REQUEST["eFemaleDriverRequest"] : '';
$eHandiCapAccessibility    = isset($_REQUEST["eHandiCapAccessibility"]) ? $_REQUEST["eHandiCapAccessibility"] : '';

$sql = "select vName from currency where eStatus='Active' AND eDefault='Yes'";
$db_currency = $obj->MySQLSelect($sql);

$sql1 = "select vCode from language_master where eStatus='Active' AND eDefault='Yes'";
$db_language= $obj->MySQLSelect($sql1);

$sql="select cn.vCountry,cn.vPhoneCode from country cn inner join 
configurations c on c.vValue=cn.vCountryCode where c.vName='DEFAULT_COUNTRY_CODE_WEB'";
$db_con = $obj->MySQLSelect($sql);

if (isset($_POST['submitbtn'])) {
	
	$pickups = explode(',', $_POST['from_lat_long']); // from latitude-Longitude
	$dropoff = explode(',', $_POST['to_lat_long']); // To latitude-Longitude
    $vSourceLatitude = isset($pickups[0]) ? trim(str_replace("(","",$pickups[0])) : '';
    $vSourceLongitude = isset($pickups[1]) ? trim(str_replace(")","",$pickups[1])) : '';
	$vDestLatitude = isset($dropoff[0]) ? trim(str_replace("(","",$dropoff[0])) : '';
    $vDestLongitude = isset($dropoff[1]) ? trim(str_replace(")","",$dropoff[1])) : '';
	$vDistance = isset($_POST['distance']) ? (round(number_format($_POST['distance']/1000))) : '';
	$vDuration = isset($_POST['duration']) ? (round(number_format($_POST['duration']/60))) : '';
	$iUserId = isset($_POST['iUserId']) ? $_POST['iUserId'] : '';
	$iDriverId = isset($_POST['iDriverId']) ? $_POST['iDriverId'] : '';
	$dBooking_date = isset($_POST['dBooking_date']) ? $_POST['dBooking_date'] : '';
	$vSourceAddresss = isset($_POST['vSourceAddresss']) ? $_POST['vSourceAddresss'] : '';
	$tDestAddress = isset($_POST['tDestAddress']) ? $_POST['tDestAddress'] : '';
	$eAutoAssign = isset($_POST['eAutoAssign']) ? $_POST['eAutoAssign'] : 'No';
	$eStatus1 =  ($eAutoAssign == 'Yes') ? 'Pending' : 'Assign';

    $iPackageTypeId = isset($_POST['iPackageTypeId']) ? $_POST['iPackageTypeId'] : '0';
    $vReceiverName = isset($_POST['vReceiverName']) ? $_POST['vReceiverName'] : '';
    $vReceiverMobile = isset($_POST['vReceiverMobile']) ? $_POST['vReceiverMobile'] : '';

    $tPackageDetails = isset($_POST['tPackageDetails']) ? $_POST['tPackageDetails'] : '';
    $tDeliveryIns = isset($_POST['tDeliveryIns']) ? $_POST['tDeliveryIns'] : '';
    $tPickUpIns = isset($_POST['tPickUpIns']) ? $_POST['tPickUpIns'] : '';
	$iVehicleTypeId = isset($_POST['iVehicleTypeId']) ? $_POST['iVehicleTypeId'] : '';
	$iCabBookingId = isset($_POST['iCabBookingId']) ? $_POST['iCabBookingId'] : '';
	$fNightPrice = isset($_POST['fNightPrice']) ? $_POST['fNightPrice'] : '1';
	$fPickUpPrice = isset($_POST['fPickUpPrice']) ? $_POST['fPickUpPrice'] : '1';
	$vTimeZone = isset($_POST['vTimeZone']) ? $_POST['vTimeZone'] : '';
	$vRideCountry = isset($_POST['vRideCountry']) ? $_POST['vRideCountry'] : '';
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	$previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	
	$fWalletMinBalance = $WALLET_MIN_BALANCE;
	$user_available_balance = $generalobj->get_user_available_balance($iDriverId,"Driver");
	$fWalletBalance = $user_available_balance;

	$eFlatTrip = isset($_POST['eFlatTrip']) ? $_POST['eFlatTrip'] : 'No';
	$fFlatTripPrice = isset($_POST['fFlatTripPrice']) ? $_POST['fFlatTripPrice'] : 0;
	
	
	$SQL1 = "SELECT vValue FROM configurations WHERE vName = 'COMMISION_DEDUCT_ENABLE'";
	$config_data = $obj->MySQLSelect($SQL1);
	$eCommisionDeductEnable = $config_data[0]['vValue'];
	
	$SQL1 = "SELECT vName,vLastName,vEmail,iUserId FROM $tbl_name WHERE vEmail = '$vEmail'";
	$email_exist = $obj->MySQLSelect($SQL1);
	$iUserId = $email_exist[0]['iUserId'];
    if(count($email_exist) == 0 && $iCabBookingId == "") {
        $eReftype = "Rider";
        $vRefCode = $generalobj->ganaraterefercode($eReftype);
        $vRefCodePara = "`vRefCode` = '" . $vRefCode . "',";
		$vPassword = $generalobj->encrypt_bycrypt('123456');
        $q = "INSERT INTO ";
        $where = '';
        $query = $q . " `" . $tbl_name . "` SET
			`vName` = '" . $vName . "',
			`vLastName` = '" . $vLastName . "',
			`vEmail` = '" . $vEmail . "',
			`vPassword` = '".$vPassword."',
			`vPhone` = '" . $vPhone . "',
			`vCountry` = '" . $vCountry . "',
			`vPhoneCode` = '" . $vPhoneCode . "',
            $vRefCodePara
			`eStatus` = '" . $eStatus . "',
			`vImgName` = '" . $vImgName . "',
			`vCurrencyPassenger` = '" . $db_currency[0]['vName'] . "',
			`vLang` = '" . $db_language[0]['vCode']. "',
			`tRegistrationDate` = '".date("Y-m-d H:i:s")."',
			`vInviteCode` = '" . $vInviteCode . "'";
		$obj->sql_query($query);
		$iUserId = $obj->GetInsertId();
		if($iUserId != ""){
			$maildata['EMAIL'] =  $vEmail;
			$maildata['NAME'] = $vName.' '.$vLastName;
			$maildata['PASSWORD'] = '123456';       
			$generalobj->send_email_user("MEMBER_REGISTRATION_USER_FOR_MANUAL_BOOKING",$maildata);
			
		}
		
		
		
    }else {
		$SQL1 = "UPDATE $tbl_name SET eStatus='$eStatus',`vCountry` = '" . $vCountry . "' WHERE vEmail = '$vEmail'";
		$obj->sql_query($SQL1);
	}
    //if($iUserId == "" || $iUserId == "0" || $iDriverId == "" || $iDriverId == "0" || $vSourceAddresss == "" || $tDestAddress == ""){
    if(($iUserId == "" || $iUserId == "0" || $vSourceAddresss == "" || $tDestAddress == "") && $APP_TYPE != "UberX"){
       $var_msg = "Booking details is not add/update because missing information";
       if($iCabBookingId == ""){
			header("location:add_booking.php?success=0&var_msg=".$var_msg); exit;
       }else{
			header("location:add_booking.php?booking_id=".$iCabBookingId."success=0&var_msg=".$var_msg); exit;
       }
    }else if(($iUserId == "" || $iUserId == "0" || $vSourceAddresss == "") && $APP_TYPE == "UberX"){
		$var_msg = "Booking details is not add/update because missing information";
		if($iCabBookingId == ""){
			header("location:add_booking.php?success=0&var_msg=".$var_msg); exit;
		}else{
			header("location:add_booking.php?booking_id=".$iCabBookingId."success=0&var_msg=".$var_msg); exit;
		}
	}
    //if($_POST['rideType'] == "manual"){
		$rand_num=rand ( 10000000 , 99999999 );
        $systemTimeZone = date_default_timezone_get();
        $dBookingDate = converToTz($dBooking_date,$systemTimeZone,$vTimeZone);
        $dBookingDate_new = date('Y-m-d H:i', strtotime($dBookingDate));

		$q1 = "INSERT INTO ";
		$whr = ",`vBookingNo`='".$rand_num."'";
		$edit = "";
		if($iCabBookingId != "" && $iCabBookingId != '0') {
			$q1 = "UPDATE ";
			$whr = " WHERE `iCabBookingId` = '" . $iCabBookingId . "'";
			$edit = '1';
		}
        if($APP_TYPE == 'UberX' &&  !empty($iDriverId) ) {
            $eStatus1 = "Accepted";
        }
        if($eTollSkipped == 'No' || $fTollPrice != "" )
		{
			$fTollPrice_Original = $fTollPrice;
			$vTollPriceCurrencyCode = strtoupper($vTollPriceCurrencyCode);
			$default_currency = $db_currency[0]['vName'];
			$sql=" SELECT round(($fTollPrice/(SELECT Ratio FROM currency where vName='".$vTollPriceCurrencyCode."'))*(SELECT Ratio FROM currency where vName='".$default_currency."' ) ,2)  as price FROM currency  limit 1";
			$result_toll = $obj->MySQLSelect($sql);
			$fTollPrice = $result_toll[0]['price'];
			if($fTollPrice == 0){
				$fTollPrice = get_currency($vTollPriceCurrencyCode,$default_currency,$fTollPrice_Original);
			}
			$fTollPrice = $fTollPrice;
			$vTollPriceCurrencyCode = $vTollPriceCurrencyCode;
			$eTollSkipped = $eTollSkipped;
		} else {
			$fTollPrice = "0";
			$vTollPriceCurrencyCode = "";
			$eTollSkipped = "No";
		}
		$query1 = $q1 . " `" . $tbl_name1 . "` SET
                `iUserId` = '" . $iUserId . "',
                `iDriverId` = '" . $iDriverId . "',
                `vSourceLatitude` = '" . $vSourceLatitude . "',
                `vSourceLongitude` = '" . $vSourceLongitude . "',
                `vDestLatitude` = '" . $vDestLatitude . "',
                `vDestLongitude` = '" . $vDestLongitude . "',
        		`vDistance` = '" . $vDistance . "',
        		`vDuration` = '" . $vDuration . "',
                `dBooking_date` = '" . $dBookingDate_new . "',
                `vSourceAddresss` = '" . $vSourceAddresss . "',
                `tPackageDetails` = '" . $tPackageDetails . "',
                `iPackageTypeId` = '" . $iPackageTypeId . "',
                `tDeliveryIns` = '" . $tDeliveryIns . "',
                `tPickUpIns` = '" . $tPickUpIns . "',
                `vReceiverName` = '" . $vReceiverName . "',
                `vReceiverMobile` = '" . $vReceiverMobile . "',
                `tDestAddress` = '" . $tDestAddress . "',
                `eType` = '" . $eType . "',
                `eStatus`='" . $eStatus1 . "',
                `eAutoAssign`='" . $eAutoAssign . "',
                `fPickUpPrice`='" . $fPickUpPrice . "',
                `fNightPrice`='" . $fNightPrice . "',
				`eCancelBy`='',
				`fWalletMinBalance`='".$fWalletMinBalance."',
				`fWalletBalance`='".$fWalletBalance."',
				`vRideCountry`='".$vRideCountry."',
				`vTimeZone`='".$vTimeZone."',
				`fTollPrice`='".$fTollPrice."',
				`vTollPriceCurrencyCode` = '".$vTollPriceCurrencyCode."',
				`eTollSkipped` = '".$eTollSkipped."',
				`eCommisionDeductEnable`='".$eCommisionDeductEnable."',
				`eFlatTrip`='".$eFlatTrip."',
				`fFlatTripPrice` = '".$fFlatTripPrice."',
				`eFemaleDriverRequest`= '".$eFemaleDriverRequest."',
				`eHandiCapAccessibility`= '".$eHandiCapAccessibility."',
                `iVehicleTypeId` = '" . $iVehicleTypeId . "'".$whr;
				
        $obj->sql_query($query1);
		$sql="select vName,vLastName,vEmail,iDriverVehicleId,vPhone,vcode,vLang from register_driver where iDriverId=".$iDriverId;
		$driver_db=$obj->MySQLSelect($sql);
		//echo "<pre>";print_r($driver_db);
		
		$Data1['vRider']=$email_exist[0]['vName']." ".$email_exist[0]['vLastName'];
		$Data1['vDriver']=$driver_db[0]['vName']." ".$driver_db[0]['vLastName'];
		$Data1['vDriverMail']=$driver_db[0]['vEmail'];
		$Data1['vRiderMail']=$email_exist[0]['vEmail'];
		$Data1['vSourceAddresss']=$vSourceAddresss;
		$Data1['tDestAddress']=$tDestAddress;
		$Data1['dBookingdate']=$dBooking_date;
		$Data1['vBookingNo']=$rand_num;
		
		if($edit == '1')
		{
			$sql="select vBookingNo from cab_booking where `iCabBookingId` = '" . $iCabBookingId . "'";
			$cab_id=$obj->MySQLSelect($sql);
			$Data1['vBookingNo']=$cab_id[0]['vBookingNo'];
		}
		//$Data1['vDistance']=$vDistance;
		//$Data1['vDuration']=$vDuration;
		
		//echo "<pre>";print_r($Data1);exit;
		$return = $generalobj->send_email_user("MANUAL_TAXI_DISPATCH_DRIVER",$Data1);
		$return1 = $generalobj->send_email_user("MANUAL_TAXI_DISPATCH_RIDER",$Data1);
		
		// Start Send SMS
		$query = "SELECT vLicencePlate FROM driver_vehicle WHERE iDriverVehicleId=".$driver_db[0]['iDriverVehicleId'];  
        $db_driver_vehicles = $obj->MySQLSelect($query);
		
		$vPhone = $vPhone;
        $vcode = $db_con[0]['vPhoneCode'];
        $Booking_Date = @date('d-m-Y',strtotime($dBooking_date));    
        $Booking_Time = @date('H:i:s',strtotime($dBooking_date));    

        $query = "SELECT vPhoneCode,vLang FROM register_user WHERE iUserId=".$iUserId;
        $db_user= $obj->MySQLSelect($query);

        $maillanguage = $db_user[0]['vLang'];

        $Pass_name = $vName.' '.$vLastName; 
		$vcode = $db_user[0]['vPhoneCode']; 
		$maildata['DRIVER_NAME'] = $Data1['vDriver'];     
        $maildata['PLATE_NUMBER'] = $db_driver_vehicles[0]['vLicencePlate'];
        $maildata['BOOKING_DATE'] = $Booking_Date;      
        $maildata['BOOKING_TIME'] =  $Booking_Time;      
        $maildata['BOOKING_NUMBER'] = $Data1['vBookingNo'];
		//Send sms to User

		$message_layout = $generalobj->send_messages_user("USER_SEND_MESSAGE",$maildata,"",$maillanguage);
        $return4 = $generalobj->sendUserSMS($vPhone,$vcode,$message_layout,"");

		//Send sms to Driver

		$vPhone = $driver_db[0]['vPhone'];
        $vcode1 = $driver_db[0]['vcode']; 
		$maillanguage1 = $driver_db[0]['vLang'];

        $maildata1['PASSENGER_NAME'] = $Pass_name;      
        $maildata1['BOOKING_DATE'] = $Booking_Date;      
        $maildata1['BOOKING_TIME'] =  $Booking_Time;      
        $maildata1['BOOKING_NUMBER'] = $Data1['vBookingNo'];
		
		$message_layout = $generalobj->send_messages_user("DRIVER_SEND_MESSAGE",$maildata1,"",$maillanguage1);
        $return5 = $generalobj->sendUserSMS($vPhone,$vcode1,$message_layout,"");
		
		if ($iCabBookingId == "") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Booking Added Successfully.';
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Booking Updated Successfully.';
        }
        header("location:".$backlink);
		
		
		
		if($return && $return1){
			$success = 1;
			$var_msg = "Booking Has Been Added Successfully.";
			header("location:cab_booking.php?success=1&vassign=$edit"); exit;
		}else{
			$error = 1;
			$var_msg = $langage_lbl['LBL_ERROR_OCCURED'];
		}
		//$msg = "Booking Has Been Added Successfully.";
		header("location:cab_booking.php?success=1&vassign=$edit"); exit;
	//}
   // include_once("go_booking.php");
}else {
	header("location:cab_booking.php?success=1&vassign=$edit"); exit;
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title>Admin | Add New Booking </title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
        <?php 
        include_once('global_files.php');
        ?>
        <!-- On OFF switch -->
        <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
        <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
        <!-- Google Map Js -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
	<script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>
	<script type='text/javascript' src='../assets/map/gmaps.js'></script>
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53 " >

        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php 
            include_once('header.php');
            include_once('left_menu.php');
            ?>
            <!--PAGE CONTENT -->
            <input type="hidden" name="distance" id="distance" value="<?php  echo $_POST['distance']; ?>">
            <input type="hidden" name="duration" id="duration" value="<?php  echo $_POST['duration']; ?>">
            <input type="hidden" name="from" id="from" value="<?php  echo $_POST['from']; ?>">
            <input type="hidden" name="to" id="to" value="<?php  echo $_POST['to']; ?>">
            <input type="hidden" name="from_lat_long" id="from_lat_long" value="<?php  echo $_POST['from_lat_long']; ?>" >
            <input type="hidden" name="to_lat_long" id="to_lat_long" value="<?php  echo $_POST['to_lat_long']; ?>" >
            <input type="hidden" value="1" id="location_found" name="location_found">
            <div id="content">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Continue Booking</h2>
                        </div>
                    </div>
                    <hr />
                    <div class="body-div">
                        <div class="form-group">
                            <?php  if ($success == 1) {?>
                            <div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">�</button>
                                <?php 
                                if ($ksuccess == "1") {
                                    ?>
                                    Record Insert Successfully.
                                <?php  } else {
                                    ?>
                                    Record Updated Successfully.
                                <?php  } ?>

                            </div><br/>
                            <?php  } ?>

                            <?php  if ($success == 2) {?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">�</button>
                                "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                            </div><br/>
                            <?php  } ?>
                            <div class="col-lg-5">
                                <h3 class="title_set">Send Request to Drivers</h3>
                                <form name="all_request_form" action="javascript:void(0);" id="all_request_form" method="post" >
                                <div class="row">
                                    <div class="col-lg-12">
                                        <input type="submit" class="save btn-info padding_set" id="send_to_all" value="Send Request to All">
                                    </div>
                                </div>
                                </form>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4>OR</h4>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <a class="save btn-info padding_set" id="send_to_specific">Send Request to Specific one</a>
                                    </div>
                                </div>
                                
                                <form name="specific_request_form" action="javascript:void(0);" id="all_request_form" method="post" >
                                <?php  if(!empty($Data)) { ?>
                                <div class="row show_specific">
                                    <div class="col-lg-12">
                                    <?php  for($ji=0;$ji<count($Data);$ji++){ ?>
                                    <input type="radio" name="set_driver" value="">&nbsp;&nbsp;<?php  echo $Data[$ji]['vName'].' '.$Data[$ji]['vLastName']; ?><br>
                                    <?php  } ?>
                                    </div>
                                </div>
                                <div class="row show_specific">
                                    <div class="col-lg-12">
                                        <input type="submit" class="btn btn-success" value="Send" >
                                    </div>
                                </div>
                                </form>
                                <?php  }else { ?>
                                <div class="row show_specific">
                                    <div class="col-lg-12">
                                        <h5>No Drivers Found.</h5>
                                    </div>
                                </div>
                                <?php  } ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <h4>OR</h4>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-lg-12">
                                        <a class="save btn-info padding_set" id="send_to_others">Send Request to Other area's</a>
                                    </div>
                                </div>
                                <form name="other_request_form" action="javascript:void(0);" id="all_request_form" method="post" >
                                <?php  if(!empty($Data)) { ?>
                                <div class="row show_others">
                                    <div class="col-lg-12">
                                    <?php  for($ji=0;$ji<count($Data);$ji++){ ?>
                                    <input type="radio" name="other_driver" value="">&nbsp;&nbsp;<?php  echo $Data[$ji]['vName'].' '.$Data[$ji]['vLastName']; ?><br>
                                    <?php  } ?>
                                    </div>
                                </div>
                                <div class="row show_others">
                                    <div class="col-lg-12">
                                        <input type="submit" class="btn btn-success" value="Send" >
                                    </div>
                                </div>
                                </form>
                                <?php  }else { ?>
                                <div class="row show_others">
                                    <div class="col-lg-12">
                                        <h5>No Drivers Found.</h5>
                                    </div>
                                </div>
                                <?php  } ?>
                            </div>
                            <div class="col-lg-7">
                                    <div class="gmap-div"><div id="map-canvas" class="gmap3"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->


        <?php 
        include_once('footer.php');
        ?>

        <script>
            $('.show_specific').hide();
            $('.show_others').hide();
            $('#send_to_specific').click(function(){
               $('.show_specific').slideToggle();
               $('.show_others').slideUp();
            });

            $('#send_to_others').click(function(){
               $('.show_others').slideToggle();
               $('.show_specific').slideUp();
            });
        </script>
        <script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
        
        <?php 
function getaddress($lat,$lng)
{
   $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=true';
   $json = @file_get_contents($url);
   $data=json_decode($json);
   $status = $data->status;
   if($status=="OK")
   {
     return $data->results[0]->formatted_address;
   }
   else
   {
     return "Address Not Found";
   }
}

for($i=0;$i<count($Data);$i++){
   $time = time();  
   $last_online_time = strtotime($Data[$i]['tLastOnline']);
   $time_difference = $time-$last_online_time;
   if($time_difference <= 300 && $Data[$i]['vAvailability'] == "Available"){
      $Data[$i]['vAvailability'] = "Available";
   }else{
      $Data1[$i]['vAvailability'] = "Not Available";
   } 
   $Data[$i]['vServiceLoc'] = getaddress($Data[$i]['vLatitude'],$Data[$i]['vLongitude']);
}
#echo "<pre>";print_r($db_records);exit;
#echo "<pre>"; print_r($db_records);echo "</pre>";
$locations = array();

#marker Add
foreach ($Data as $key => $value) {
  if($value['vAvailability'] == "Available"){ 
      $locations[] = array(
              'google_map' => array(
                      'lat' => $value['vLatitude'],
                      'lng' => $value['vLongitude'],
              ),
              'location_address' => $value['vServiceLoc'],
              'location_name'    => $value['FULLNAME'],
              'location_online_status'    => $value['vAvailability'],
      );
  }  
}
?>

<?php 
/* Set Default Map Area Using First Location */
$map_area_lat = isset( $locations[0]['google_map']['lat'] ) ? $locations[0]['google_map']['lat'] : '';
$map_area_lng = isset( $locations[0]['google_map']['lng'] ) ? $locations[0]['google_map']['lng'] : '';
?>
<script type="text/javascript" src="js/gmap3.js"></script>
<script>
	
        
        $(function(){
        var from = $('#from').val();
        var to = $('#to').val();
        var waypts = [];
        if (from != '') {
                    //alert("in from "+from);
                    $("#map-canvas").gmap3({
                        getlatlng: {
                            address: from,
                            callback: function (results) {
                                console.log(results[0]);
                                $("#from_lat_long").val(results[0].geometry.location);
                            }
                        }
                    });
                }
                if (to != '') {
                    $("#map-canvas").gmap3({
                        getlatlng: {
                            address: to,
                            callback: function (results) {
                                $("#to_lat_long").val(results[0].geometry.location);
                            }
                        }
                    });
                }

                $("#map-canvas").gmap3({
                    getroute: {
                        options: {
                            origin: from,
                            destination: to,
                            waypoints: waypts,
                            travelMode: google.maps.DirectionsTravelMode.DRIVING
                        },
                        callback: function (results, status) {
                            chk_route = status;
                            if (!results)
                                return;
                            $(this).gmap3({
                                map: {
                                    options: {
                                        zoom: 8,
                                        //       center: [51.511214, -0.119824]
                                        center: [58.0000, 20.0000]
                                    }
                                },
                                directionsrenderer: {
                                    options: {
                                        directions: results
                                    }
                                }
                            });
                        }
                    }
                });

                $("#map-canvas").gmap3({
                    getdistance: {
                        options: {
                            origins: from,
                            destinations: to,
                            travelMode: google.maps.TravelMode.DRIVING
                        },
                        callback: function (results, status) {
                            var html = "";
                            if (results) {
                                for (var i = 0; i < results.rows.length; i++) {
                                    var elements = results.rows[i].elements;
                                    for (var j = 0; j < elements.length; j++) {
                                        switch (elements[j].status) {
                                            case "OK":
                                                html += elements[j].distance.text + " (" + elements[j].duration.text + ")<br />";
                                                document.getElementById("distance").value = elements[j].distance.text;
                                                document.getElementById("duration").value = elements[j].duration.text;
                                                document.getElementById("location_found").value = 1;
                                                break;
                                            case "NOT_FOUND":
                                                document.getElementById("location_found").value = 0;
                                                break;
                                            case "ZERO_RESULTS":
                                                document.getElementById("location_found").value = 0;
                                                break;
                                        }
                                    }
                                }
                            } else {
                                html = "error";
                            }
                            $("#results").html(html);
                        }
                    }
                });
            });
</script>
        
        
    </body>
    <!-- END BODY-->
</html>