<?php 
	//date_default_timezone_set('Asia/Kolkata');
	
	@session_start();
	$_SESSION['sess_hosttype'] = 'ufxall';
	$inwebservice = "1";
	error_reporting(0);
	//include_once('include_taxi_webservices.php');
	include_once('include_config.php');
    include_once(TPATH_CLASS.'configuration.php');
    
    // 
	
	require_once('assets/libraries/stripe/config.php');
	require_once('assets/libraries/stripe/stripe-php-2.1.4/lib/Stripe.php');
	require_once('assets/libraries/pubnub/autoloader.php');
	require_once('assets/libraries/class.ExifCleaning.php');
	include_once(TPATH_CLASS .'Imagecrop.class.php');
	include_once(TPATH_CLASS .'twilio/Services/Twilio.php');
	include_once('generalFunctions.php');
	include_once('send_invoice_receipt.php');   
	$PHOTO_UPLOAD_SERVICE_ENABLE = "Yes";
	$host_arr = array();
	$host_arr = explode(".",$_SERVER["HTTP_HOST"]);
	$host_system = $host_arr[0];
	$parent_ufx_catid="0";
	if($host_system == "beautician"){
		$PHOTO_UPLOAD_SERVICE_ENABLE = "Yes";
	}
	if($host_system == "tutors"){
		$PHOTO_UPLOAD_SERVICE_ENABLE = "No";
	}
	$uuid = "fg5k3i7i7l5ghgk1jcv43w0j41";
	
	/* creating objects */
	$thumb 		= new thumbnail;
	
	/* Get variables */
	$type 		= isset($_REQUEST['type'])?trim($_REQUEST['type']):'';
	
	/* Paypal supported Currency Codes */
	$currency_supported_paypal = array('AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'TWD', 'NZD', 'NOK', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'TRY', 'USD');
	
	$demo_site_msg="Edit / Delete Record Feature has been disabled on the Demo Application. This feature will be enabled on the main script we will provide you.";
	
	if($type==''){
		$type 		= isset($_REQUEST['function'])?trim($_REQUEST['function']):'';
	}
	$lang_label = array();
	$lang_code 	= '';
	
	/* general fucntions */
	if($type != "generalConfigData" && $type != "signIn" && $type != "isUserExist" && $type != "signup" && $type != "LoginWithFB" && $type != "sendVerificationSMS" && $type != "countryList" && $type != "changelanguagelabel" && $type != "requestResetPassword" && $type != "UpdateLanguageLabelsValue" && $type != "staticPage" && $type != "sendContactQuery") {
		$tSessionId = isset($_REQUEST['tSessionId']) ? trim($_REQUEST['tSessionId']) : '';
		$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
		$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : '';
		if ($tSessionId == "" || $GeneralMemberId == "" || $GeneralUserType == "") {
		$returnArr['Action'] = "0";
		$returnArr['message'] = "SESSION_OUT";
		echo json_encode($returnArr);
		exit;
		} else {
		$userData = get_value($GeneralUserType == "Driver" ? "register_driver" : "register_user", $GeneralUserType == "Driver" ? "iDriverId as iMemberId,tSessionId" : "iUserId as iMemberId,tSessionId", $GeneralUserType == "Driver" ? "iDriverId" : "iUserId", $GeneralMemberId);
		if ($userData[0]['iMemberId'] != $GeneralMemberId || $userData[0]['tSessionId'] != $tSessionId) {
		$returnArr['Action'] = "0";
		$returnArr['message'] = "SESSION_OUT";
		echo json_encode($returnArr);
		exit;
		}
		}
	}   
	/* To Check App Version */
	$appVersion = isset($_REQUEST['AppVersion'])?trim($_REQUEST['AppVersion']):'';
	$Platform = isset($_REQUEST['Platform'])?trim($_REQUEST['Platform']):'Android';
  $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
  $vUserDeviceCountry = isset($_REQUEST["vUserDeviceCountry"]) ? $_REQUEST["vUserDeviceCountry"] : '';
	
	if($appVersion != ""){
		$UserType   = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		if($UserType == "Passenger"){
			$newAppVersion = $Platform == "IOS"? $PASSENGER_IOS_APP_VERSION:$PASSENGER_ANDROID_APP_VERSION;
			}else{
			//$newAppVersion = $generalobj->getConfigurations("configurations",$Platform == "IOS"? "DRIVER_IOS_APP_VERSION" : "DRIVER_ANDROID_APP_VERSION");
			$newAppVersion = $Platform == "IOS"? $DRIVER_IOS_APP_VERSION:$DRIVER_ANDROID_APP_VERSION;
		}
		$appVersion =  round($appVersion,2);
		if($newAppVersion != $appVersion && $newAppVersion > $appVersion){
			$returnArr['Action'] = "0";
			$returnArr['isAppUpdate'] = "true";
			$returnArr['message'] = "LBL_NEW_UPDATE_MSG";
			echo json_encode($returnArr);exit;
		}
	}
	
	if($type=="checkGetValue"){
		$check_payment = get_value('vehicle_type', '*', '', '');
		// print_r($check_payment);
		
		$row[0]['VehicleTypes'] = $check_payment;
		echo json_encode($row[0]);
	}
	
	
	function getPassengerDetailInfo($passengerID,$cityName){
		global $generalobj,$obj,$demo_site_msg,$PHOTO_UPLOAD_SERVICE_ENABLE,$parent_ufx_catid,$generalConfigArr,$tconfig,$vTimeZone,$vUserDeviceCountry;
		
		$where = " iUserId = '".$passengerID."'";
		$data_version['iAppVersion']="2";
		$data_version['eLogout'] = 'No';
		$obj->MySQLQueryPerform("register_user",$data_version,'update',$where);
		
		$updateQuery = "UPDATE trip_status_messages SET eReceived='Yes' WHERE iUserId='".$passengerID."' AND eToUserType='Passenger'";
		$obj->sql_query($updateQuery);
		
		$sql = "SELECT * FROM `register_user` WHERE iUserId='$passengerID'";
		$row = $obj->MySQLSelect($sql);
		
		if (count($row) > 0) {
			
			$page_link = $tconfig['tsite_url'] ."sign-up_rider.php?UserType=Rider&vRefCode=".$row[0]['vRefCode']; 
			$link = get_tiny_url($page_link); 
      //$activation_text = '<a href="'.$link.'" target="_blank"> '.$link.' </a>';
      $activation_text = "<a href='".$link."' target='_blank'> '".$link."' </a>";
      $vLanguage= $row[0]['vLang'];
			if($vLanguage == "" || $vLanguage == NULL){
				$vLanguage = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
			}
      $sql = "SELECT * FROM `language_label` WHERE vLabel = 'LBL_SHARE_CONTENT_PASSENGER' AND vCode = '".$vLanguage."'";
		  $db_label = $obj->MySQLSelect($sql);
      $LBL_SHARE_CONTENT_PASSENGER = $db_label[0]['vValue'];
      $row[0]['INVITE_SHARE_CONTENT'] = $LBL_SHARE_CONTENT_PASSENGER." ".$link;
			for($i=0;$i<count($generalConfigArr);$i++){
				$row[0][$generalConfigArr[$i]['vName']] = $generalConfigArr[$i]['vValue']; 
			}
			$row[0]['GOOGLE_ANALYTICS'] = "";
      $row[0]['SERVER_MAINTENANCE_ENABLE'] = $row[0]['MAINTENANCE_APPS'];
			$RIDER_EMAIL_VERIFICATION = $row[0]["RIDER_EMAIL_VERIFICATION"];
			$RIDER_PHONE_VERIFICATION = $row[0]["RIDER_PHONE_VERIFICATION"];
			
			if($RIDER_EMAIL_VERIFICATION == 'No'){
				$row[0]['eEmailVerified'] = "Yes";
			}
			
			if($RIDER_PHONE_VERIFICATION == 'No'){
				$row[0]['ePhoneVerified'] = "Yes";
			}
			
			## Check and update Device Session ID ##
			if($row[0]['tDeviceSessionId'] == ""){
				$random = substr( md5(rand()), 0, 7);
				$Update_Device_Session['tDeviceSessionId'] = session_id().time().$random;
				$Update_Device_Session_id = $obj->MySQLQueryPerform("register_user", $Update_Device_Session, 'update', $where);
				$row[0]['tDeviceSessionId'] = $Update_Device_Session['tDeviceSessionId'];
			}
			## Check and update Device Session ID ##
			## Check and update Session ID ##
			if($row[0]['tSessionId'] == ""){
				$Update_Session['tSessionId'] = session_id().time();
				$Update_Session_id = $obj->MySQLQueryPerform("register_user", $Update_Session, 'update', $where);
				$row[0]['tSessionId'] = $Update_Session['tSessionId'];
			}
			## Check and update Session ID ##
			
			if($row[0]['vImgName']!="" && $row[0]['vImgName']!="NONE"){
				$row[0]['vImgName']="3_".$row[0]['vImgName'];
			}
			//$row[0]['Passenger_Password_decrypt']= $generalobj->decrypt($row[0]['vPassword']);
			$row[0]['Passenger_Password_decrypt']= "";
			
			if($row[0]['eStatus']!="Active"){
				$returnArr['Action'] ="0";
				
				if($row[0]['eStatus'] !="Deleted"){
					$returnArr['message'] ="LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER";
					}else {
					$returnArr['message'] ="LBL_ACC_DELETE_TXT";
				}
				echo json_encode($returnArr);exit;
			}
			
			$TripStatus = $row[0]['vTripStatus'];
			$TripID=$row[0]['iTripId'];
			
			if ($TripStatus != "NONE") {
				$TripID = $row[0]['iTripId'];
				$row_result_trips = getTripPriceDetails($TripID,$passengerID,"Passenger");
				
				$row[0]['TripDetails'] = $row_result_trips;
				
				
				$row[0]['DriverDetails'] = $row_result_trips['DriverDetails'];
				
				
				$row_result_trips['DriverCarDetails']['make_title'] = $row_result_trips['DriverCarDetails']['vMake'];
				$row_result_trips['DriverCarDetails']['model_title'] = $row_result_trips['DriverCarDetails']['vTitle'];
				$row[0]['DriverCarDetails'] = $row_result_trips['DriverCarDetails'];
				
				$sql = "SELECT vPaymentUserStatus FROM `payments` WHERE iTripId='$TripID'";
				$row_result_payments = $obj->MySQLSelect($sql);
				
				if(count($row_result_payments)>0){
					
					if($row_result_payments[0]['vPaymentUserStatus']!='approved'){
						$row[0]['PaymentStatus_From_Passenger']="Not Approved";
						}else{
						$row[0]['PaymentStatus_From_Passenger']="Approved";
					}
					
					}else{
					
					$row[0]['PaymentStatus_From_Passenger']="No Entry";
				}
				
				$sql = "SELECT iTripId,eUserType FROM `ratings_user_driver` WHERE iTripId='$TripID'";
				$row_result_ratings = $obj->MySQLSelect($sql);
				
				if(count($row_result_ratings)>0){
					
					$count_row_rating=0;
					$ContentWritten="false";
					while(count($row_result_ratings) > $count_row_rating){
						
						$UserType=$row_result_ratings[$count_row_rating]['eUserType'];
						
						if($UserType=="Passenger"){
							$ContentWritten="true";
							$row[0]['Ratings_From_Passenger']="Done";
							}else if($ContentWritten=="false"){
							$row[0]['Ratings_From_Passenger']="Not Done";
						}
						
						$count_row_rating++;
					}
					}else{
					
					$row[0]['Ratings_From_Passenger']="No Entry";
				}
			}
			
			$sql = "SELECT count(iUserAddressId) as ToTalAddress from user_address WHERE iUserId = '".$passengerID."' AND eUserType = 'Rider' AND eStatus = 'Active'";
			$result_Address = $obj->MySQLSelect($sql);
			$row[0]['ToTalAddress'] = $result_Address[0]['ToTalAddress'];
			
			// $row[0]['PayPalConfiguration']=$generalobj->getConfigurations("configurations","PAYMENT_ENABLED");
			$row[0]['DefaultCurrencySign']=$row[0]["DEFAULT_CURRENCY_SIGN"]; 
			$row[0]['DefaultCurrencyCode']=$row[0]["DEFAULT_CURRENCY_CODE"];
			$row[0]['FETCH_TRIP_STATUS_TIME_INTERVAL'] = fetchtripstatustimeinterval();
			$row[0]['ENABLE_TOLL_COST'] = $row[0]['APP_TYPE'] != "UberX" ? $row[0]['ENABLE_TOLL_COST'] : "No";
			if($row[0]['APP_TYPE'] == "Ride" || $row[0]['APP_TYPE'] == "Ride-Delivery"){
				$row[0]['FEMALE_RIDE_REQ_ENABLE'] = $row[0]['FEMALE_RIDE_REQ_ENABLE'];
				$row[0]['HANDICAP_ACCESSIBILITY_OPTION'] = $row[0]['HANDICAP_ACCESSIBILITY_OPTION'];
				}else{
				$row[0]['FEMALE_RIDE_REQ_ENABLE'] = "No";
				$row[0]['HANDICAP_ACCESSIBILITY_OPTION'] = "No";
				// $row[0]['ENABLE_TOLL_COST'] = "No";
			}
			if($row[0]['APP_TYPE'] == "Ride" || $row[0]['APP_TYPE'] == "Ride-Delivery"){
				$row[0]['ENABLE_HAIL_RIDES']= $row[0]['ENABLE_HAIL_RIDES'];
				}else{
				$row[0]['ENABLE_HAIL_RIDES'] = "No"; 
			}
      if($row[0]['APP_PAYMENT_MODE'] == "Card"){
				$row[0]['ENABLE_HAIL_RIDES'] = "No"; 
			}
			$user_available_balance = $generalobj->get_user_available_balance($passengerID,"Rider");
			$row[0]['user_available_balance'] = strval($generalobj->userwalletcurrency(0,$user_available_balance,$row[0]['vCurrencyPassenger']));
			
			// $row[0]['PHOTO_UPLOAD_SERVICE_ENABLE']=$PHOTO_UPLOAD_SERVICE_ENABLE;
			
			$row[0]['PHOTO_UPLOAD_SERVICE_ENABLE']=$row[0]['APP_TYPE'] == "UberX" ? $PHOTO_UPLOAD_SERVICE_ENABLE :"No";
			$row[0]['ENABLE_TIP_MODULE']=$row[0]['ENABLE_TIP_MODULE'];
			
			
			$host_arr = array();
			$host_arr = explode(".",$_SERVER["HTTP_HOST"]);
			$host_system = $host_arr[0];
			$parent_ufx_catid="0";
			if($host_system == "carwash4"){
				$parent_ufx_catid="1";
			}
			if($host_system == "homecleaning4"){
				$parent_ufx_catid="2";
			}
			if($host_system == "doctor4"){
				$parent_ufx_catid="3";
			}
			if($host_system == "beautician4"){
				$parent_ufx_catid="4";
			}
			if($host_system == "massage4"){
				$parent_ufx_catid="5";
			}
			if($host_system == "tutors4"){
				$parent_ufx_catid="7";
			}
			if($host_system == "dogwalking4"){
				$parent_ufx_catid="8";
			}
			if($host_system == "towtruck4"){
				$parent_ufx_catid="9";
			}
			if($host_system == "plumbers4"){
				$parent_ufx_catid="10";
			}
			if($host_system == "electricians4"){
				$parent_ufx_catid="11";
			}
			if($host_system == "babysitting4"){
				$parent_ufx_catid="12";
			}
			if($host_system == "escorts4"){
				$parent_ufx_catid="18";
			}
			if($host_system == "fitnesscoach4"){
				$parent_ufx_catid="13";
			}
			if($host_system == "laundry4"){
				$parent_ufx_catid="6";
			}
			if($host_system == "snowplow4"){
				$parent_ufx_catid="29";
			}
			if($host_system == "securityguard4"){
				$parent_ufx_catid="64";
			}
			$row[0]['UBERX_PARENT_CAT_ID'] = $parent_ufx_catid;
			//$row[0]['UBERX_PARENT_CAT_ID'] = 1;
			if($row[0]['APP_TYPE'] == "UberX"){
				$row[0]['APP_DESTINATION_MODE'] = "None";
				$row[0]['ENABLE_TOLL_COST'] = "No";
				$row[0]['HANDICAP_ACCESSIBILITY_OPTION'] = "No";
				$row[0]['FEMALE_RIDE_REQ_ENABLE'] = "No";
				$row[0]['ENABLE_HAIL_RIDES'] = "No";
				$row[0]['ONLINE_DRIVER_LIST_UPDATE_TIME_INTERVAL']="5";
				}else{
				//$row[0]['APP_DESTINATION_MODE'] = "Strict";
			}
			// $row[0]['ENABLE_DELIVERY_MODULE']=$generalobj->getConfigurations("configurations","ENABLE_DELIVERY_MODULE");
			$row[0]['ENABLE_DELIVERY_MODULE']=SITE_TYPE=="Demo"?$row[0]['eDeliverModule']:$row[0]['ENABLE_DELIVERY_MODULE'];
			$row[0]['PayPalConfiguration']=$row[0]['ENABLE_DELIVERY_MODULE'] == "Yes"?"Yes":$row[0]['PAYMENT_ENABLED'];
			// if($row[0]['ENABLE_DELIVERY_MODULE'] == "Yes"){
			// $row[0]['PayPalConfiguration'] = "Yes";
			// }
			$row[0]['CurrencyList'] = get_value('currency', '*', 'eStatus', 'Active');
			$row[0]['SITE_TYPE']=SITE_TYPE;
			$row[0]['RIIDE_LATER']=RIIDE_LATER;
			$row[0]['PROMO_CODE']=PROMO_CODE;
			$row[0]['SITE_TYPE_DEMO_MSG']=$demo_site_msg;
			$row[0]['CurrencySymbol']= get_value('currency', 'vSymbol', 'vName', $row[0]['vCurrencyPassenger'],'','true');
			$eUnit = getMemberCountryUnit($passengerID,"Passenger");
			$row[0]['eUnit']=$eUnit;
			$row[0]['SourceLocations']=getusertripsourcelocations($passengerID,"SourceLocation");
			$row[0]['DestinationLocations']=getusertripsourcelocations($passengerID,"DestinationLocation");
			$sql = "SELECT * FROM user_fave_address where iUserId = '".$passengerID."' AND eUserType = 'Passenger' AND eStatus = 'Active' ORDER BY iUserFavAddressId ASC";
			$db_passenger_fav_address = $obj->MySQLSelect($sql);
			$row[0]['UserFavouriteAddress']= $db_passenger_fav_address;
      $usercountrydetailbytimezone = GetUserCounryDetail($passengerID,"Passenger",$vTimeZone,$vUserDeviceCountry);
      $row[0]['vDefaultCountry']= $usercountrydetailbytimezone['vDefaultCountry'];
      $row[0]['vDefaultCountryCode']= $usercountrydetailbytimezone['vDefaultCountryCode'];
      $row[0]['vDefaultPhoneCode']= $usercountrydetailbytimezone['vDefaultPhoneCode']; 
      $SITE_POLICE_CONTROL_NUMBER = getMemberCountryPoliceNumber($passengerID,"Passenger",$row[0]['vCountry']);
      $row[0]['SITE_POLICE_CONTROL_NUMBER']= $SITE_POLICE_CONTROL_NUMBER;
			/* fetch value */
			return $row[0];
			
			
			} else {
			
			$returnArr['Action'] ="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
			
			echo json_encode($returnArr);exit;
			
			
		}
		
	}
	
	function getDriverDetailInfo($driverId,$fromSignIN=0){
		
		global $generalobj,$obj,$demo_site_msg,$PHOTO_UPLOAD_SERVICE_ENABLE,$parent_ufx_catid,$generalConfigArr,$vTimeZone,$tconfig,$vUserDeviceCountry;
		
		$where = " iDriverId = '".$driverId."'";
		$data_version['iAppVersion']="2";
		$data_version['eLogout'] = 'No';
		$obj->MySQLQueryPerform("register_driver",$data_version,'update',$where);
		
		$updateQuery = "UPDATE trip_status_messages SET eReceived='Yes' WHERE iDriverId='".$driverId."' AND eToUserType='Driver'";
		$obj->sql_query($updateQuery);
		
		$returnArr = array();
		
		$sql = "SELECT rd.*,cmp.eStatus as cmpEStatus,(SELECT dv.vLicencePlate From driver_vehicle as dv WHERE rd.iDriverVehicleId != '' AND rd.iDriverVehicleId !='0' AND dv.iDriverVehicleId = rd.iDriverVehicleId) as vLicencePlateNo FROM `register_driver` as rd,`company` as cmp WHERE rd.iDriverId='$driverId' AND cmp.iCompanyId=rd.iCompanyId";
		
		$Data = $obj->MySQLSelect($sql);
		
		if(count($Data) > 0)
		{
			
			$page_link = $tconfig['tsite_url'] ."sign-up.php?UserType=Driver&vRefCode=" . $Data[0]['vRefCode']; 
			$link = get_tiny_url($page_link); 
      //$activation_text = '<a href="'.$link.'" target="_blank"> '.$link.' </a>';
      $activation_text = "<a href='".$link."' target='_blank'> '".$link."' </a>";
      $vLanguage= $Data[0]['vLang'];
			if($vLanguage == "" || $vLanguage == NULL){
				$vLanguage = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
			}
      $sql = "SELECT * FROM `language_label` WHERE vLabel = 'LBL_SHARE_CONTENT_DRIVER' AND vCode = '".$vLanguage."'";
		  $db_label = $obj->MySQLSelect($sql);
      $LBL_SHARE_CONTENT_DRIVER = $db_label[0]['vValue'];
      $Data[0]['INVITE_SHARE_CONTENT'] = $LBL_SHARE_CONTENT_DRIVER." ".$link;
			for($i=0;$i<count($generalConfigArr);$i++){
				$Data[0][$generalConfigArr[$i]['vName']] = $generalConfigArr[$i]['vValue']; 
			}
      $Data[0]['GOOGLE_ANALYTICS'] = "";
      $Data[0]['SERVER_MAINTENANCE_ENABLE'] = $Data[0]['MAINTENANCE_APPS'];
			$DRIVER_EMAIL_VERIFICATION = $Data[0]["DRIVER_EMAIL_VERIFICATION"];
			$DRIVER_PHONE_VERIFICATION = $Data[0]["DRIVER_PHONE_VERIFICATION"];
			
			if($DRIVER_EMAIL_VERIFICATION == 'No'){
				$Data[0]['eEmailVerified'] = "Yes";
			}
			
			if($DRIVER_PHONE_VERIFICATION == 'No'){
				$Data[0]['ePhoneVerified'] = "Yes";
			}
			
			## Check and vWorkLocationRadius For UberX ##
			$eUnit = getMemberCountryUnit($driverId,"Driver");
			$Data[0]['eUnit']=$eUnit;         
			if($Data[0]['vWorkLocationRadius'] == "" || $Data[0]['vWorkLocationRadius'] == "0" || $Data[0]['vWorkLocationRadius'] == 0){
				$vWorkLocationRadius = $Data[0]['RESTRICTION_KM_NEAREST_TAXI'];
				$Update_Driver_radius['vWorkLocationRadius'] = $vWorkLocationRadius;
				$obj->MySQLQueryPerform("register_driver", $Update_Driver_radius, 'update', $where);
				$Data[0]['vWorkLocationRadius'] = $vWorkLocationRadius;
				if($eUnit == "Miles"){
					$Data[0]['vWorkLocationRadius'] = round($vWorkLocationRadius * 0.621371,2);  
					}else{
					$Data[0]['vWorkLocationRadius'] = $vWorkLocationRadius;
				}
			}
			## Check and update Device Session ID ##
			if($Data[0]['tDeviceSessionId'] == ""){
				$random = substr( md5(rand()), 0, 7);
				$Update_Device_Session['tDeviceSessionId'] = session_id().time().$random;
				$Update_Device_Session_id = $obj->MySQLQueryPerform("register_driver", $Update_Device_Session, 'update', $where);
				$Data[0]['tDeviceSessionId'] = $Update_Device_Session['tDeviceSessionId'];
			}
			## Check and update Device Session ID ##
			## Check and update Session ID ##
			if($Data[0]['tSessionId'] == ""){
				$Update_Session['tSessionId'] = session_id().time();
				$Update_Session_id = $obj->MySQLQueryPerform("register_driver", $Update_Session, 'update', $where);
				$Data[0]['tSessionId'] = $Update_Session['tSessionId'];
			}
			## Check and update Session ID ##
			// $Data[0]['Driver_Password_decrypt']= $generalobj->decrypt($Data[0]['vPassword']);
			$Data[0]['Driver_Password_decrypt']= "";
			
			if($Data[0]['vImage']!="" && $Data[0]['vImage']!="NONE"){
				$Data[0]['vImage']="3_".$Data[0]['vImage'];
			}
			
			if($Data[0]['iDriverVehicleId'] == '' || $Data[0]['iDriverVehicleId'] == NULL){ 
				$sql = "SELECT iDriverVehicleId FROM  driver_vehicle WHERE `eStatus` = 'Active' AND `iDriverId` = '".$driverId."' ";
				$Data_vehicle = $obj->MySQLSelect($sql);
				$iDriver_VehicleId = $Data_vehicle[0]['iDriverVehicleId']; 
				$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$driverId."'";
				$obj->sql_query($sql);
				$Data[0]['iDriverVehicleId'] = $iDriver_VehicleId;
				$vLicencePlate=  get_value('driver_vehicle', 'vLicencePlate', 'iDriverVehicleId', $iDriver_VehicleId,'','true');
				$Data[0]['vLicencePlateNo'] = $vLicencePlate;
			}
			
			if($Data[0]['iDriverVehicleId'] != '' && $Data[0]['iDriverVehicleId'] != '0'){
				/*$data_vehicle_arr=  get_value('driver_vehicle', 'iMakeId, iModelId', 'iDriverVehicleId', $Data[0]['iDriverVehicleId']);
					$Data[0]['vMake'] = get_value('make', 'vMake', 'iMakeId', $data_vehicle_arr[0]['iMakeId'],'','true');
				$Data[0]['vModel'] = get_value('model', 'vTitle', 'iModelId', $data_vehicle_arr[0]['iModelId'],'','true');*/
				$sql = "SELECT ma.vMake,mo.vTitle FROM driver_vehicle as dv LEFT JOIN make as ma ON dv.iMakeId = ma.iMakeId LEFT JOIN model as mo ON dv.iModelId = mo.iModelId WHERE dv.iDriverVehicleId = '".$Data[0]['iDriverVehicleId']."'";
				$DriverVehicle = $obj->MySQLSelect($sql);
				$Data[0]['vMake'] = $DriverVehicle[0]['vMake'];
				$Data[0]['vModel'] = $DriverVehicle[0]['vTitle'];
			}
			
			if($Data[0]['eStatus']=="Deleted"){
				
				$returnArr['Action'] ="0";
        $returnArr['eStatus'] =$Data[0]['eStatus'];
				$returnArr['message'] ="LBL_ACC_DELETE_TXT";
				
				echo json_encode($returnArr);exit;
			} 
			
			$TripStatus = $Data[0]['vTripStatus'];
			$Data[0]['RegistrationDate'] = date("Y-m-d", strtotime( $Data[0]['tRegistrationDate'] . ' -1 day ' ));
			if ($TripStatus != "NONE") {
				$TripID         =  $Data[0]['iTripId'];
				
				$row_result_trips = getTripPriceDetails($TripID,$driverId,"Driver");
				
				
				$Data[0]['TripDetails'] = $row_result_trips;
				$Data[0]['PassengerDetails'] = $row_result_trips['PassengerDetails'];
				
				$sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$TripID'";
				$db_tripTimes = $obj->MySQLSelect($sql22);
				
				$totalSec = 0;
				$timeState = 'Pause';
				$iTripTimeId = '';
				foreach($db_tripTimes as $dtT){
					if($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
						$totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
						}else {
						$totalSec += strtotime(date('Y-m-d H:i:s')) - strtotime($dtT['dResumeTime']);
						$iTripTimeId = $dtT['iTripTimeId'];
						$timeState = 'Resume';
					}
				}
				
				// $diff = strtotime('2009-10-05 18:11:08') - strtotime('2009-10-05 18:07:13')
				
				$Data[0]['iTripTimeId'] = $iTripTimeId;
				$Data[0]['TotalSeconds'] = $totalSec;
				$Data[0]['TimeState'] = $timeState;
				
				$sql = "SELECT iTripId,eUserType FROM `ratings_user_driver` WHERE iTripId='$TripID'";
				$row_result_ratings = $obj->MySQLSelect($sql);
				
				if(count($row_result_ratings)>0){
					
					$count_row_rating=0;
					$ContentWritten="false";
					while(count($row_result_ratings) > $count_row_rating){
						
						$UserType=$row_result_ratings[$count_row_rating]['eUserType'];
						
						if($UserType == "Driver"){
							$ContentWritten="true";
							$Data[0]['Ratings_From_Driver']="Done";
							}else if($ContentWritten=="false"){
							$Data[0]['Ratings_From_Driver']="Not Done";
						}
						
						$count_row_rating++;
					}
					
					}else{
					
					$Data[0]['Ratings_From_Driver']="No Entry";
				}
			}
			$sql = "SELECT count(iUserAddressId) as ToTalAddress from user_address WHERE iUserId = '".$driverId."' AND eUserType = 'Driver' AND eStatus = 'Active'";
			$result_Address = $obj->MySQLSelect($sql);
			$Data[0]['ToTalAddress'] = $result_Address[0]['ToTalAddress'];
			
			$Data[0]['ABOUT_US_PAGE_DESCRIPTION']="";
			$Data[0]['DefaultCurrencySign']=$Data[0]["DEFAULT_CURRENCY_SIGN"];
			$Data[0]['DefaultCurrencyCode']=$Data[0]["DEFAULT_CURRENCY_CODE"];
			$Data[0]['SITE_TYPE']=SITE_TYPE;
			$Data[0]['RIIDE_LATER']=RIIDE_LATER;
			$Data[0]['SITE_TYPE_DEMO_MSG']=$demo_site_msg;
			$Data[0]['vLicencePlateNo']=is_null($Data[0]['vLicencePlateNo'])== false ? $Data[0]['vLicencePlateNo']:'';
			$Data[0]['FETCH_TRIP_STATUS_TIME_INTERVAL']=fetchtripstatustimeinterval();
			$Data[0]['ENABLE_TOLL_COST'] = $Data[0]['APP_TYPE'] != "UberX" ? $Data[0]['ENABLE_TOLL_COST'] : "No";
			if($Data[0]['APP_TYPE'] == "UberX"){
				$Data[0]['APP_DESTINATION_MODE'] = "None";
				
				$Data[0]['ENABLE_TOLL_COST'] = "No";
				$Data[0]['HANDICAP_ACCESSIBILITY_OPTION'] = "No";
				$Data[0]['FEMALE_RIDE_REQ_ENABLE'] = "No";
				$Data[0]['ENABLE_HAIL_RIDES'] = "No";
				}else{
				//$Data[0]['APP_DESTINATION_MODE'] = "Strict";
			}
			
			if($Data[0]['APP_TYPE'] == "Ride" || $Data[0]['APP_TYPE'] == "Ride-Delivery"){        
				$Data[0]['FEMALE_RIDE_REQ_ENABLE'] =  $Data[0]['FEMALE_RIDE_REQ_ENABLE'];
				$Data[0]['HANDICAP_ACCESSIBILITY_OPTION'] =  $Data[0]['HANDICAP_ACCESSIBILITY_OPTION'];
				}else{
				$Data[0]['FEMALE_RIDE_REQ_ENABLE'] = "No";
				$Data[0]['HANDICAP_ACCESSIBILITY_OPTION'] = "No";
			}
			if($Data[0]['APP_TYPE'] == "Ride" || $Data[0]['APP_TYPE'] == "Ride-Delivery"){
				$Data[0]['ENABLE_HAIL_RIDES']=$Data[0]['ENABLE_HAIL_RIDES'];
				}else{
				$Data[0]['ENABLE_HAIL_RIDES'] = "No"; 
			}
      if($Data[0]['APP_PAYMENT_MODE'] == "Card"){
        $Data[0]['ENABLE_HAIL_RIDES'] = "No";
      }
			
			$Data[0]['PHOTO_UPLOAD_SERVICE_ENABLE']=$Data[0]['APP_TYPE'] == "UberX" ? $PHOTO_UPLOAD_SERVICE_ENABLE :"No";
			$Data[0]['ENABLE_DELIVERY_MODULE']=SITE_TYPE=="Demo"?$Data[0]['eDeliverModule']:$Data[0]['ENABLE_DELIVERY_MODULE'];
			$Data[0]['PayPalConfiguration']=$Data[0]['ENABLE_DELIVERY_MODULE'] == "Yes"?"Yes": $Data[0]['PAYMENT_ENABLED'];
			// $Data[0]['CurrencyList']=($obj->MySQLSelect("SELECT * FROM currency"));                 
			$Data[0]['CurrencyList']=get_value('currency', '*', 'eStatus', 'Active');
			$Data[0]['UBERX_PARENT_CAT_ID'] = $parent_ufx_catid;
			$Data[0]['UBERX_SUB_CAT_ID'] = "0";
			
			$user_available_balance = $generalobj->get_user_available_balance($driverId,"Driver");
			$Data[0]['user_available_balance'] = strval($generalobj->userwalletcurrency(0,$user_available_balance,$Data[0]['vCurrencyDriver']));
			$Data[0]['CurrencySymbol']= get_value('currency', 'vSymbol', 'vName', $Data[0]['vCurrencyDriver'],'','true');
			
			$str_date = @date('Y-m-d H:i:s', strtotime('-1 minutes'));
			
			$sql_request = "SELECT * FROM passenger_requests WHERE iDriverId='".$driverId."' AND dAddedDate > '".$str_date."' ";
			$data_requst = $obj->MySQLSelect($sql_request);
			
			$Data[0]['CurrentRequests'] = $data_requst;
			$sql = "SELECT * FROM user_fave_address where iUserId = '".$driverId."' AND eUserType = 'Driver' AND eStatus = 'Active' ORDER BY iUserFavAddressId ASC";
			$db_driver_fav_address = $obj->MySQLSelect($sql);
			$Data[0]['UserFavouriteAddress']= $db_driver_fav_address;
			$usercountrydetailbytimezone = GetUserCounryDetail($driverId,"Driver",$vTimeZone,$vUserDeviceCountry);
      $Data[0]['vDefaultCountry']= $usercountrydetailbytimezone['vDefaultCountry'];
      $Data[0]['vDefaultCountryCode']= $usercountrydetailbytimezone['vDefaultCountryCode'];
      $Data[0]['vDefaultPhoneCode']= $usercountrydetailbytimezone['vDefaultPhoneCode'];
      $SITE_POLICE_CONTROL_NUMBER = getMemberCountryPoliceNumber($driverId,"Driver",$Data[0]['vCountry']);
      $row[0]['SITE_POLICE_CONTROL_NUMBER']= $SITE_POLICE_CONTROL_NUMBER;
			return $Data[0];
			} else {
			$returnArr['Action'] ="0";
      $returnArr['eStatus'] ="";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
			
			echo json_encode($returnArr);exit;
		}
	}
	
	
	
	/* function checkDistanceWithGoogleDirections($tripDistance,$startLatitude,$startLongitude,$endLatitude,$endLongitude){
		global $generalobj,$obj;
		
		$GOOGLE_API_KEY=$generalobj->getConfigurations("configurations","GOOGLE_SEVER_GCM_API_KEY");
		$url = "https://maps.googleapis.com/maps/api/directions/json?origin=".$startLatitude.",".$startLongitude."&destination=".$endLatitude.",".$endLongitude."&sensor=false&key=".$GOOGLE_API_KEY;
		try {
		$jsonfile = file_get_contents($url);
		} catch (ErrorException $ex) {
		echo "Failed";
		exit;
		}
		
		$jsondata = json_decode($jsonfile);
		$distance_google_directions=($jsondata->routes[0]->legs[0]->distance->value)/1000;
		
		$comparedDist=($distance_google_directions *85)/100;
		
		if($tripDistance>$comparedDist){
		return $tripDistance;
		}else{
		return round($distance_google_directions,2);
		}
	} */
	
	
	
	/* If no type found */
	if($type == '') {
		$result['result'] = 0;
		$result['message'] = 'Required parameter missing.';
		
		echo json_encode($result);
		exit;
	}
	
	/* function getLanguageLabelsArr($lCode = ''){
        global $obj;
		
		$sql = "SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";
		$default_label = $obj->MySQLSelect($sql);
		
		if($lCode == ''){
		$lCode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode'])?$default_label[0]['vCode']:'EN';
		}
		
		
        $sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label`  WHERE  `vCode` = '".$lCode."' ";
        $all_label = $obj->MySQLSelect($sql);
		
        $x = array();
        for($i=0; $i<count($all_label); $i++){
		$vLabel = $all_label[$i]['vLabel'];
		
		$vValue = $all_label[$i]['vValue'];
		$x[$vLabel]=$vValue;
        }
		
		$sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label_other`  WHERE  `vCode` = '".$lCode."' ";
        $all_label = $obj->MySQLSelect($sql);
		
        for($i=0; $i<count($all_label); $i++){
		$vLabel = $all_label[$i]['vLabel'];
		
		$vValue = $all_label[$i]['vValue'];
		$x[$vLabel]=$vValue;
        }
        $x['vCode'] = $lCode; // to check in which languge code it is loading
		
        return $x;
	} */
	
	/*-------------- For Luggage Lable default and as per user's Prefered language ----------------------- */
	if($type == 'language_label') {
		$lCode = isset($_REQUEST['vCode'])?clean(strtoupper($_REQUEST['vCode'])):''; // User's prefered language
		
		/* find default language of website set by admin */
		if($lCode == '') {
			$sql = "SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";
			$default_label = $obj->MySQLSelect($sql);
			
			$lCode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode'])?$default_label[0]['vCode']:'EN';
		}
		
		$sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label`  WHERE  `vCode` = '".$lCode."' ";
		$all_label = $obj->MySQLSelect($sql);
		
		$x = array();
		for($i=0; $i<count($all_label); $i++){
			$vLabel = $all_label[$i]['vLabel'];
			
			
			$vValue = $all_label[$i]['vValue'];
			
			$x[$vLabel]=$vValue;
		}
		$x['vCode'] = $lCode; // to check in which languge code it is loading
		
		echo json_encode($x);
		exit;
	}
	##########################################################################
	## NEW WEBSERVICE START ##
	##########################################################################
	
	##########################################################################
    if($type == 'generalConfigData') {
		$UserType             = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
    $vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
		
		$DataArr['LanguageLabels'] = getLanguageLabelsArr($vLang,"1");
		$DataArr['Action'] = "1";
		
		$sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ";
		$defLangValues = $obj->MySQLSelect($sql);
		$DataArr['LIST_LANGUAGES']=$defLangValues;
		for($i = 0;$i<count($defLangValues); $i++){
			if($defLangValues[$i]['eDefault'] == "Yes"){
				$DataArr['DefaultLanguageValues']=$defLangValues[$i];
			}
		}
    if($vLang != ""){
      $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `vCode` = '".$vLang."' ";
		  $requireLangValues = $obj->MySQLSelect($sql);
      $DataArr['DefaultLanguageValues']=$requireLangValues[0];
		}
		
		$sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ";
		$defCurrencyValues =  $obj->MySQLSelect($sql);
		$DataArr['LIST_CURRENCY']=$defCurrencyValues;
		for($i = 0;$i<count($defCurrencyValues); $i++){
			if($defCurrencyValues[$i]['eDefault'] == "Yes"){
				$DataArr['DefaultCurrencyValues']=$defCurrencyValues[$i];
			}
		}
		
		for($i = 0;$i<count($generalConfigArr); $i++) {
			$vName = $generalConfigArr[$i]["vName"];
			$vValue = $generalConfigArr[$i]["vValue"];
			$$vName = $vValue;
			$DataArr[$vName] = $vValue;
		}
		$DataArr['GOOGLE_ANALYTICS'] = "";
		if($UserType == "Passenger"){
			$DataArr['LINK_FORGET_PASS_PAGE_PASSENGER']=$tconfig["tsite_url"].$LINK_FORGET_PASS_PAGE_PASSENGER;
			$DataArr['CONFIG_CLIENT_ID']=$CONFIG_CLIENT_ID;
			$DataArr['FACEBOOK_LOGIN']=$PASSENGER_FACEBOOK_LOGIN;
			$DataArr['GOOGLE_LOGIN']=$PASSENGER_GOOGLE_LOGIN;
			$DataArr['TWITTER_LOGIN']=$PASSENGER_TWITTER_LOGIN;
			}else{
			$DataArr['LINK_FORGET_PASS_PAGE_DRIVER']=$tconfig["tsite_url"].$LINK_FORGET_PASS_PAGE_DRIVER;
			$DataArr['LINK_SIGN_UP_PAGE_DRIVER']=$tconfig["tsite_url"].$LINK_SIGN_UP_PAGE_DRIVER;
			$DataArr['FACEBOOK_LOGIN']=$DRIVER_FACEBOOK_LOGIN;
			$DataArr['GOOGLE_LOGIN']=$DRIVER_GOOGLE_LOGIN;
			$DataArr['TWITTER_LOGIN']=$DRIVER_TWITTER_LOGIN;
		} 
		$DataArr['SERVER_MAINTENANCE_ENABLE'] = $MAINTENANCE_APPS;
		$DataArr['SITE_TYPE']=SITE_TYPE;
    $usercountrydetailbytimezone = GetUserCounryDetail($GeneralMemberId,$UserType,$vTimeZone,$vUserDeviceCountry);
    $DataArr['vDefaultCountry']= $usercountrydetailbytimezone['vDefaultCountry'];
    $DataArr['vDefaultCountryCode']= $usercountrydetailbytimezone['vDefaultCountryCode'];
    $DataArr['vDefaultPhoneCode']= $usercountrydetailbytimezone['vDefaultPhoneCode'];
		
		$obj->MySQLClose();
		echo json_encode($DataArr);
		exit;
	}
	############################ country_list #############################
    if($type == 'countryList') {
		
        // $sql = "SELECT * FROM  `country` WHERE eStatus = 'Active' ";
        // $all_label = $obj->MySQLSelect($sql);
        // $returnArr['countryList'] = $all_label;
        // echo json_encode($returnArr);
        // exit;
		
		global $lang_label, $obj,$tconfig, $generalobj;
		
        $returnArr         = array();
		
        $counter=0;
        for($i=0;$i<26;$i++){
            $cahracter=  chr(65 + $i);
			
			$sql = "SELECT COU.* FROM country as COU WHERE COU.eStatus = 'Active' AND COU.vPhoneCode!='' AND COU.vCountryCode!='' AND COU.vCountry LIKE '$cahracter%' ORDER BY COU.vCountry";
            $db_rec = $obj->MySQLSelect($sql);
			
            if(count($db_rec) >0){
				
				$countryListArr = array();
				$subCounter=0;
				for($j=0;$j<count($db_rec);$j++){
					
					$countryListArr[$subCounter]=$db_rec[$j];
					$subCounter++;
				}
				
				if(count($countryListArr)>0){
                    $returnArr[$counter]['key'] = $cahracter;
                    $returnArr[$counter]['TotalCount'] = count($countryListArr);
                    $returnArr[$counter]['List'] = $countryListArr;
					
                    $counter++;
					
				}
			}
			
		}
		
        $countryArr['Action'] = "1";
        $countryArr['totalValues'] = count($returnArr);
        $countryArr['CountryList'] = $returnArr;
        echo json_encode($countryArr);
        exit;
	}
	
	###########################################################################
	
	if($type=="signup"){
		
		$fbid             = isset($_REQUEST["vFbId"]) ? $_REQUEST["vFbId"] : '';
		$Fname             = isset($_REQUEST["vFirstName"]) ? $_REQUEST["vFirstName"] : '';
		$Lname             = isset($_REQUEST["vLastName"]) ? $_REQUEST["vLastName"] : '';
		$email            = isset($_REQUEST["vEmail"]) ? $_REQUEST["vEmail"] : '';
		$email 			= strtolower($email);
		$phone_mobile     = isset($_REQUEST["vPhone"]) ? $_REQUEST["vPhone"] : '';
		$password         = isset($_REQUEST["vPassword"]) ? $_REQUEST["vPassword"] : '';
		$iGcmRegId        = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
		$phoneCode = isset($_REQUEST["PhoneCode"]) ? $_REQUEST["PhoneCode"] : '';
		$CountryCode = isset($_REQUEST["CountryCode"]) ? $_REQUEST["CountryCode"] : '';
		$vInviteCode = isset($_REQUEST["vInviteCode"]) ? $_REQUEST["vInviteCode"] : '';
		$deviceType = isset($_REQUEST["vDeviceType"]) ? $_REQUEST["vDeviceType"] : 'Android';
		$vCurrency = isset($_REQUEST["vCurrency"]) ? $_REQUEST["vCurrency"] : '';
		$vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
		$user_type = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$eSignUpType = isset($_REQUEST["eSignUpType"]) ? $_REQUEST["eSignUpType"] : 'Normal';
		$vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
    $vImageURL = isset($_REQUEST["vImageURL"]) ? $_REQUEST["vImageURL"] : '';
		
		if($email == "" && $phone_mobile == "" && $fbid == ""){
			$returnArr['Action'] ="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
			echo json_encode($returnArr);exit;
		}
		if($vCurrency=='')
		{
			$vCurrency=get_value('currency', 'vName', 'eDefault','Yes','','true');
		}
		if($vLang=='')
		{
			$vLang= get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
    
    if($fbid != ""){
      if($Lname == "" || $Lname == NULL){
         $username = explode(" ",$Fname);
         if($username[1] != ""){
           $Fname= $username[0]; 
           $Lname = $username[1]; 
         }
      }
    }
		
		if($user_type == "Passenger"){
			$tblname = "register_user";
			$eRefType = "Rider";
			$Data_passenger['vPhoneCode']=$phoneCode;
			$Data_passenger['vCurrencyPassenger']=$vCurrency;
			$vImage = 'vImgName';
			$iMemberId = 'iUserId';
			}else{
			$tblname = "register_driver";
			$eRefType = "Driver";
			$Data_passenger['vCode']=$phoneCode;
			$Data_passenger['vCurrencyDriver'] = $vCurrency;
			$Data_passenger['iCompanyId'] = 1;
			$vImage = 'vImage';
			$iMemberId = 'iDriverId';
		} 
		
		//$sql = "SELECT * FROM `register_user` WHERE vEmail = '$email' OR vPhone = '$phone_mobile'";
		$sql    = "SELECT * FROM $tblname WHERE 1=1 AND IF('$email'!='',vEmail = '$email',0) OR IF('$phone_mobile'!='',vPhone = '$phone_mobile',0) OR IF('$fbid'!='',vFbId = '$fbid',0)";
		$check_passenger    = $obj->MySQLSelect($sql);
		
		//$Password_passenger = $generalobj->encrypt($password);
		if($password != ""){
			$Password_passenger = $generalobj->encrypt_bycrypt($password);
			}else{
			$Password_passenger = "";
		}
		
		
		if(count($check_passenger)>0){
			$returnArr['Action'] ="0";
      
      if($check_passenger[0]['eStatus'] == "Deleted"){
         $returnArr['message'] ="LBL_ACCOUNT_STATUS_DELETED_TXT";
         echo json_encode($returnArr);exit;
      }
			
			if($email==strtolower($check_passenger[0]['vEmail'])){
				$returnArr['message'] ="LBL_ALREADY_REGISTERED_TXT";
				}else {
				$returnArr['message'] ="LBL_MOBILE_EXIST";      
			}
			echo json_encode($returnArr);exit;
			}else{
			$check_inviteCode ="";
			$inviteSuccess= false;
			if($vInviteCode != ""){
				$check_inviteCode = $generalobj->validationrefercode($vInviteCode);
				if($check_inviteCode == "" || $check_inviteCode == "0" || $check_inviteCode == 0){
					$returnArr['Action'] = "0";
					$returnArr['message'] = "LBL_INVITE_CODE_INVALID";
					echo json_encode($returnArr);
					exit;
					}else{
					$inviteRes= explode("|",$check_inviteCode);
					$Data_passenger['iRefUserId'] = $inviteRes[0];
					$Data_passenger['eRefType'] = $inviteRes[1];
					$inviteSuccess = true;
				}
			}
			
			$Data_passenger['vFbId']=$fbid;
			$Data_passenger['vName']=$Fname;
			$Data_passenger['vLastName']=$Lname;
			$Data_passenger['vEmail']=$email;
			$Data_passenger['vPhone']=$phone_mobile;
			$Data_passenger['vPassword']=$Password_passenger;
			$Data_passenger['iGcmRegId']=$iGcmRegId;
			$Data_passenger['vFirebaseDeviceToken']=$vFirebaseDeviceToken;
			$Data_passenger['vLang']=$vLang;
			//$Data_passenger['vPhoneCode']=$phoneCode;
			$Data_passenger['vCountry']=$CountryCode;
			$Data_passenger['eDeviceType']=$deviceType;
			$Data_passenger['vRefCode'] = $generalobj->ganaraterefercode($eRefType);
			//$Data_passenger['vCurrencyPassenger']=$vCurrency;
			$Data_passenger['dRefDate'] =  @date('Y-m-d H:i:s');
      $Data_passenger['tRegistrationDate'] = @date('Y-m-d H:i:s');
			$Data_passenger['eSignUpType'] =$eSignUpType;
			if($eSignUpType == "Facebook" || $eSignUpType == "Google"){
				$Data_passenger['eEmailVerified'] ="Yes";
			}
			$random = substr( md5(rand()), 0, 7);
			$Data_passenger['tDeviceSessionId'] = session_id().time().$random;
			$Data_passenger['tSessionId'] = session_id().time();
			
			if(SITE_TYPE=='Demo')
			{
				$Data_passenger['eStatus'] = 'Active';
				$Data_passenger['eEmailVerified'] = 'Yes';
				$Data_passenger['ePhoneVerified'] = 'Yes';
            }
            
            
            
            // echo json_encode("test_start");
            // echo json_encode($obj ? "ok" : "null");
            // echo json_encode("test_end");exit;


            $id = $obj->MySQLQueryPerform($tblname,$Data_passenger,'insert');
        
            
			## Upload Image of Member if SignUp from Google, Facebook Or Twitter ##
			if($fbid != 0 || $fbid != ""){
                
				$UserImage = UploadUserImage($id,$user_type,$eSignUpType,$fbid,$vImageURL);
				if($UserImage != ""){
					$where = " $iMemberId = '$id' ";
					$Data_update_image_member[$vImage] = $UserImage;
					$imageuploadid = $obj->MySQLQueryPerform($tblname,$Data_update_image_member,'update',$where);
				}   
			}
			## Upload Image of Member if SignUp from Google, Facebook Or Twitter ##
			
			//$sql_checkLangCode = "SELECT  vCode FROM  language_master WHERE `eStatus` = 'Active' AND `eDefault` = 'Yes' ";
			//$Data_checkLangCode = $obj->MySQLSelect($sql_checkLangCode);
            $returnArr['changeLangCode'] ="Yes";
			$returnArr['UpdatedLanguageLabels'] = getLanguageLabelsArr($vLang,"1");
            $returnArr['vLanguageCode'] = $vLang;
            // echo json_encode($fbid);
            // exit;

            $sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '".$vLang."' ";
			$Data_checkLangCode = $obj->MySQLSelect($sql_LangCode);
			$returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
			$returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
            $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ";
  		    $defLangValues = $obj->MySQLSelect($sql);
            $returnArr['LIST_LANGUAGES']=$defLangValues;

            
            
      for($i = 0;$i<count($defLangValues); $i++){
         if($defLangValues[$i]['eDefault'] == "Yes"){
            $returnArr['DefaultLanguageValues']=$defLangValues[$i];
         }
      }
      $sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ";
      $defCurrencyValues =  $obj->MySQLSelect($sql);
  		$returnArr['LIST_CURRENCY']=$defCurrencyValues;
      for($i = 0;$i<count($defCurrencyValues); $i++){
         if($defCurrencyValues[$i]['eDefault'] == "Yes"){
            $returnArr['DefaultCurrencyValues']=$defCurrencyValues[$i];
         }
      }
			
			
			if($APP_TYPE == 'UberX'){
				if(strtolower($user_type)=='driver'){
					$query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type`";
					$result = $obj->MySQLSelect($query);
					
					$Drive_vehicle['iDriverId'] = $id;
					$Drive_vehicle['iCompanyId'] = "1";
					$Drive_vehicle['iMakeId'] = "3";
					$Drive_vehicle['iModelId'] = "1";
					$Drive_vehicle['iYear'] = Date('Y');
					$Drive_vehicle['vLicencePlate'] = "My Services";
					$Drive_vehicle['eStatus'] = "Active";
					$Drive_vehicle['eCarX'] = "Yes";
					$Drive_vehicle['eCarGo'] = "Yes";		
					if(SITE_TYPE=='Demo'){
						$Drive_vehicle['vCarType'] = $result[0]['countId'];
						}else{
						$Drive_vehicle['vCarType'] = "";
					}
					$iDriver_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle,'insert');
					$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$id."'";
					$obj->sql_query($sql);
					if(SITE_TYPE=='Demo') {
						$days =  array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
						foreach ($days as $value) {
							$data_avilability['iDriverId'] = $id;
							$data_avilability['vDay'] = $value;
							$data_avilability['vAvailableTimes'] = '08-09,09-10,10-11,11-12,12-13,13-14,14-15,15-16,16-17,17-18,18-19,19-20,20-21,21-22';
							$data_avilability['dAddedDate'] = @date('Y-m-d H:i:s');
							$data_avilability['eStatus'] = 'Active';
							$data_avilability_add = $obj->MySQLQueryPerform('driver_manage_timing',$data_avilability,'insert');
						}
					}
					/*if($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes"){
						$sql="select iVehicleTypeId,iVehicleCategoryId,eFareType,fFixedFare,fPricePerHour from vehicle_type where 1=1";
						$data_vehicles = $obj->MySQLSelect($sql);
						//echo "<pre>";print_r($data_vehicles);exit;
						
						if($data_vehicles[$i]['eFareType'] != "Regular")
						{
						for($i=0 ; $i < count($data_vehicles); $i++){
						$Data_service['iVehicleTypeId'] = $data_vehicles[$i]['iVehicleTypeId'];
						$Data_service['iDriverVehicleId'] = $iDriver_VehicleId;
						
						if($data_vehicles[$i]['eFareType'] == "Fixed"){
						$Data_service['fAmount'] = $data_vehicles[$i]['fFixedFare'];
						}
						else if($data_vehicles[$i]['eFareType'] == "Hourly"){
						$Data_service['fAmount'] = $data_vehicles[$i]['fPricePerHour'];
						}
						$data_service_amount = $obj->MySQLQueryPerform('service_pro_amount',$Data_service,'insert');
						}
						}
					} */
				}
			}
			else
			{
				if(SITE_TYPE=='Demo')
				{
					$query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type`";
					$result = $obj->MySQLSelect($query);
					$Drive_vehicle['iDriverId'] = $id;
					$Drive_vehicle['iCompanyId'] = "1";
					$Drive_vehicle['iMakeId'] = "5";
					$Drive_vehicle['iModelId'] = "18";
					$Drive_vehicle['iYear'] = "2014";
					$Drive_vehicle['vLicencePlate'] = "CK201";
					$Drive_vehicle['eStatus'] = "Active";
					$Drive_vehicle['eCarX'] = "Yes";
					$Drive_vehicle['eCarGo'] = "Yes";		
					$Drive_vehicle['vCarType'] = $result[0]['countId'];
					$iDriver_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle,'insert');
					$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$id."'";
					$obj->sql_query($sql);
				}
			}
			
			if ($id > 0) {
				if($inviteSuccess == true){
					//$REFERRAL_AMOUNT = $generalobj->getConfigurations("configurations","REFERRAL_AMOUNT");
					$eFor = "Referrer";
					$tDescription = "Referral amount credited";
					$dDate = Date('Y-m-d H:i:s');
					$ePaymentStatus = "Unsettelled";
					//$generalobj->InsertIntoUserWallet($Data_passenger['iRefUserId'],$Data_passenger['eRefType'],$REFERRAL_AMOUNT,'Credit',0,$eFor,$tDescription,$ePaymentStatus,$dDate);
				}
				
				/*new added*/
				$returnArr['Action'] = "1";
				if($user_type == "Passenger"){
					$returnArr['message'] = getPassengerDetailInfo($id,"");
					}else{
					$returnArr['message'] = getDriverDetailInfo($id);
				}  
				
				echo json_encode($returnArr);
				
				$maildata['EMAIL'] = $email;
				$maildata['NAME'] = $Fname;
				$maildata['PASSWORD'] = "Password: ".$password;
				$maildata['SOCIALNOTES'] = '';
				
				if($user_type == "Passenger"){
	     			$generalobj->send_email_user("MEMBER_REGISTRATION_USER",$maildata);
				}else{
					$generalobj->send_email_user("DRIVER_REGISTRATION_USER",$maildata);
          
          $generalobj->send_email_user("DRIVER_REGISTRATION_ADMIN",$maildata);
				}  
				} else {
				$returnArr['Action'] ="0";
				$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
				echo json_encode($returnArr);
				exit;
			}
		}
		
	}
	
	######################### isUserExist #############################
	
    if ($type == "isUserExist") {
		
        $Emid = isset($_REQUEST["Email"]) ? $_REQUEST["Email"] : '';
        $Phone = isset($_REQUEST["Phone"]) ? $_REQUEST["Phone"] : '';
        $fbid = isset($_REQUEST["fbid"]) ? $_REQUEST["fbid"] : '';
        
		/*if($fbid != ''){
			$sql    = "SELECT vEmail,vPhone,vFbId FROM `register_user` WHERE vEmail = '$Emid' OR vPhone = '$Phone' OR vFbId = '$fbid'";
    		}else{
			$sql    = "SELECT vEmail,vPhone,vFbId FROM `register_user` WHERE vEmail = '$Emid' OR vPhone = '$Phone'";
		} */
		$sql    = "SELECT vEmail,vPhone,vFbId FROM register_user WHERE 1=1 AND IF('$Emid'!='',vEmail = '$Emid',0) OR IF('$Phone'!='',vPhone = '$Phone',0) OR IF('$fbid'!='',vFbId = '$fbid',0)";
        $Data = $obj->MySQLSelect($sql);
		
        if ( count($Data) >0  )
        {
			
            $returnArr['Action']="0";
			
            if($Emid==$Data[0]['vEmail']){
				$returnArr['message'] ="LBL_ALREADY_REGISTERED_TXT";
				}else if($Phone==$Data[0]['vPhone']) {
				$returnArr['message'] ="LBL_MOBILE_EXIST";
				}else {
				$returnArr['message'] ="LBL_FACEBOOK_ACC_EXIST";
			}
		}
        else
        {
            $returnArr['Action']="1";
		}
		
        echo json_encode($returnArr);
	}
	###########################################################################
	
	if ($type == "signIn") {
		
		$Emid = isset($_REQUEST["vEmail"]) ? $_REQUEST["vEmail"] : '';
		$Emid = strtolower($Emid);
		$Password_user = isset($_REQUEST["vPassword"]) ? $_REQUEST["vPassword"] : '';
		$GCMID = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
		$DeviceType = isset($_REQUEST["vDeviceType"]) ? $_REQUEST["vDeviceType"] : 'Android';
		$UserType   = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$vCurrency = isset($_REQUEST["vCurrency"]) ? $_REQUEST["vCurrency"] : '';
		$vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
		$vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
		
		//$Password_user = $generalobj->encrypt($Password_user);
		
		if(SITE_TYPE == "Demo"){
			$tablename = ($UserType == 'Passenger')?"register_user":"register_driver"; 
			$iMemberId = ($UserType == 'Passenger')?"iUserId":"iDriverId";
			$iUserId = ($UserType == 'Passenger')?"36":"31";  
			$Member_Currency = ($UserType == 'Passenger')?"vCurrencyPassenger":"vCurrencyDriver";
			$Member_Image = ($UserType == 'Passenger')?"vImgName":"vImage";
			$Data_Update_Member['vName'] = ($UserType == 'Passenger')?"MAC":"Mark";
			$Data_Update_Member['vLastName'] = ($UserType == 'Passenger')?"ANDREW":"Bruno";
			$Data_Update_Member['vEmail'] = ($UserType == 'Passenger')?"rider@gmail.com":"driver@gmail.com";
			$Password_User = $generalobj->encrypt_bycrypt("123456");
			$Data_Update_Member['vPassword'] = $Password_User;
			$Data_Update_Member['vCountry'] = ($UserType == 'Passenger')?"US":"US";
			$Data_Update_Member['vLang'] = ($UserType == 'Passenger')?"EN":"EN";
			$Data_Update_Member['eStatus'] = ($UserType == 'Passenger')?"Active":"active";
			$Data_Update_Member[$Member_Currency] = ($UserType == 'Passenger')?"USD":"USD";
			$Data_Update_Member[$Member_Image] = ($UserType == 'Passenger')?"1504878922_81109.jpg":"1505208397_54463.jpg";
			$where = " $iMemberId = '".$iUserId."'";      
			$Update_Member_id = $obj->MySQLQueryPerform($tablename,$Data_Update_Member,'update',$where);
		}
		
		if($UserType == "Passenger"){
			$sql = "SELECT iUserId,eStatus,vLang,vTripStatus,vLang,vPassword FROM `register_user` WHERE vEmail='$Emid' OR vPhone = '$Emid'";
			$Data = $obj->MySQLSelect($sql);
			
			/*$iCabRequestId= get_value('cab_request_now', 'max(iCabRequestId)', 'iUserId',$Data[0]['iUserId'],'','true');
			$eStatus_cab= get_value('cab_request_now', 'eStatus', 'iCabRequestId',$iCabRequestId,'','true');*/
			$sql_cabrequest = "SELECT iCabRequestId,eStatus FROM `cab_request_now` WHERE iUserId='".$Data[0]['iUserId']."' ORDER BY iCabRequestId DESC LIMIT 0,1";
			$Data_cabrequest = $obj->MySQLSelect($sql_cabrequest);
			$iCabRequestId = $Data_cabrequest[0]['iCabRequestId'];
			$eStatus_cab = $Data_cabrequest[0]['eStatus']; 
			if ( count($Data) > 0 ) {
					# Check For Valid password #
					$hash = $Data[0]['vPassword'];
					$checkValidPass = $generalobj->check_password($Password_user, $hash);
					//$checkValidPass = 1;
					if($checkValidPass == 0){
						$returnArr['Action']="0";
						$returnArr['message'] ="LBL_WRONG_DETAIL";
						echo json_encode($returnArr);exit;
					}
					# Check For Valid password #
        if($Data[0]['eStatus']=="Active"){
					
					$iUserId_passenger=$Data[0]['iUserId'];
					$where = " iUserId = '$iUserId_passenger' ";
					if($Data[0]['vLang'] == "" && $vLang == ""){
						$vLang= get_value('language_master', 'vCode', 'eDefault','Yes','','true');
						$Data_update_passenger['vLang']=$vLang;
					}
					if($vLang != ""){
						$Data_update_passenger['vLang']=$vLang;
						$Data[0]['vLang'] = $vLang; 
					}
					if($vCurrency != ""){
						$Data_update_passenger['vCurrencyPassenger']=$vCurrency;
					}
					if($GCMID!=''){
						$Data_update_passenger['iGcmRegId']=$GCMID;
						$Data_update_passenger['eDeviceType']=$DeviceType;
						$Data_update_passenger['tSessionId'] = session_id().time();
						$Data_update_passenger['vFirebaseDeviceToken']=$vFirebaseDeviceToken;
						if(SITE_TYPE == "Demo"){
							$Data_update_passenger['tRegistrationDate']=date('Y-m-d H:i:s');
						}
						$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
					}
					
					if($eStatus_cab == "Requesting"){
						$where1 = " iCabRequestId = '$iCabRequestId' ";
						$Data_update_cab_now['eStatus']="Cancelled";
						$id = $obj->MySQLQueryPerform("cab_request_now",$Data_update_cab_now,'update',$where1);
					}
					
					
					
						$returnArr['changeLangCode'] ="Yes";
						$returnArr['UpdatedLanguageLabels'] = getLanguageLabelsArr($Data[0]['vLang'],"1");
						$returnArr['vLanguageCode'] = $Data[0]['vLang'];
					
						$sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '".$Data[0]['vLang']."' ";
						$Data_checkLangCode = $obj->MySQLSelect($sql_LangCode);
						$returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
						$returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
            $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ";
        		$defLangValues = $obj->MySQLSelect($sql);
            $returnArr['LIST_LANGUAGES']=$defLangValues;
            for($i = 0;$i<count($defLangValues); $i++){
               if($defLangValues[$i]['eDefault'] == "Yes"){
                  $returnArr['DefaultLanguageValues']=$defLangValues[$i];
               }
            }
            $sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ";
            $defCurrencyValues =  $obj->MySQLSelect($sql);
        		$returnArr['LIST_CURRENCY']=$defCurrencyValues;
            for($i = 0;$i<count($defCurrencyValues); $i++){
               if($defCurrencyValues[$i]['eDefault'] == "Yes"){
                  $returnArr['DefaultCurrencyValues']=$defCurrencyValues[$i];
               }
					}
					
					$returnArr['Action'] = "1";
					$returnArr['message'] = getPassengerDetailInfo($Data[0]['iUserId'],'');
					echo json_encode($returnArr);
					
					createUserLog($UserType,"No",$Data[0]['iUserId'],"Android");
					}else{
					$returnArr['Action']="0";
  					if($Data[0]['eStatus'] !="Deleted"){
  						$returnArr['message'] ="LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER";
              $returnArr['eStatus'] = $Data[0]['eStatus'];
						}else{
  						$returnArr['message'] ="LBL_ACC_DELETE_TXT";
              $returnArr['eStatus'] = $Data[0]['eStatus'];
					}
					echo json_encode($returnArr);
				}
			}
			else
			{
				$returnArr['Action']="0";
				$returnArr['message'] ="LBL_WRONG_DETAIL";
				echo json_encode($returnArr);
			}
			}else{
			
			//$sql = "SELECT rd.iDriverId,rd.eStatus,rd.vLang,cmp.eStatus as cmpEStatus FROM `register_driver` as rd,`company` as cmp WHERE ( rd.vEmail='$Emid' OR rd.vPhone = '$Emid' )  AND rd.vPassword='$Password_user' AND cmp.iCompanyId=rd.iCompanyId";
			$sql = "SELECT rd.iDriverId,rd.eStatus,rd.vLang,rd.vPassword,cmp.eStatus as cmpEStatus FROM `register_driver` as rd,`company` as cmp WHERE ( rd.vEmail='$Emid' OR rd.vPhone = '$Emid' ) AND cmp.iCompanyId=rd.iCompanyId";
			$Data = $obj->MySQLSelect($sql);
			
			if ( count($Data) > 0 ) {
				
				
					# Check For Valid password #
					$hash = $Data[0]['vPassword'];
					//$checkValidPass = $generalobj->check_password($Password_user, $hash);
					$checkValidPass = 1;
					if($checkValidPass == 0){
						$returnArr['Action']="0";
						$returnArr['message'] ="LBL_WRONG_DETAIL";
						echo json_encode($returnArr);exit;
					}
					# Check For Valid password #
				if($Data[0]['eStatus'] !="Deleted"){
					if($GCMID!=''){
						
						$iDriverId_driver=$Data[0]['iDriverId'];
						$where = " iDriverId = '$iDriverId_driver' ";
						
						if($Data[0]['vLang'] == "" && $vLang == ""){
							$vLang= get_value('language_master', 'vCode', 'eDefault','Yes','','true');
							$Data_update_driver['vLang']=$vLang;
						}
						if($vLang != ""){
							$Data_update_driver['vLang']=$vLang;
							$Data[0]['vLang'] = $vLang; 
						}
						if($vCurrency != ""){
							$Data_update_driver['vCurrencyDriver']=$vCurrency;
						}
						$Data_update_driver['vFirebaseDeviceToken']=$vFirebaseDeviceToken;
						$Data_update_driver['tSessionId'] = session_id().time();
						$Data_update_driver['iGcmRegId']=$GCMID;
						$Data_update_driver['eDeviceType']=$DeviceType;
						$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
					}
					// echo json_encode(getDriverDetailInfo($Data[0]['iDriverId'],1));
					
					
					
						$returnArr['changeLangCode'] ="Yes";
						$returnArr['UpdatedLanguageLabels'] = getLanguageLabelsArr($Data[0]['vLang'],"1");
						$returnArr['vLanguageCode'] = $Data[0]['vLang'];
					
						$sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '".$Data[0]['vLang']."' ";
						$Data_checkLangCode = $obj->MySQLSelect($sql_LangCode);
						$returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
						$returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
            $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ";
        		$defLangValues = $obj->MySQLSelect($sql);
            $returnArr['LIST_LANGUAGES']=$defLangValues;
            for($i = 0;$i<count($defLangValues); $i++){
               if($defLangValues[$i]['eDefault'] == "Yes"){
                  $returnArr['DefaultLanguageValues']=$defLangValues[$i];
               }
            }
            $sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ";
            $defCurrencyValues =  $obj->MySQLSelect($sql);
        		$returnArr['LIST_CURRENCY']=$defCurrencyValues;
            for($i = 0;$i<count($defCurrencyValues); $i++){
               if($defCurrencyValues[$i]['eDefault'] == "Yes"){
                  $returnArr['DefaultCurrencyValues']=$defCurrencyValues[$i];
               }
					}
					
					$returnArr['Action'] = "1";
					$returnArr['message'] = getDriverDetailInfo($Data[0]['iDriverId'],1);
					echo json_encode($returnArr);
					
					createUserLog($UserType,"No",$Data[0]['iDriverId'],"Android");
					
					}else{
					$returnArr['Action']="0";
					$returnArr['message'] ="LBL_ACC_DELETE_TXT";
          $returnArr['eStatus'] = $Data[0]['eStatus'];
					echo json_encode($returnArr);
					exit;
				}
			}
			else
			{
				$returnArr['Action']="0";
				$returnArr['message'] ="LBL_WRONG_DETAIL";
				echo json_encode($returnArr);
				exit;
			}
		}
	}
	
	###########################################################################
	
	if ($type == "getDetail") {
		
		$iUserId  = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$GCMID = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
		$deviceType = isset($_REQUEST["vDeviceType"]) ? $_REQUEST["vDeviceType"] : 'Android';
		$UserType   = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $vLangCode   = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
		
		
		if($UserType == "Passenger"){
			$sql = "SELECT iGcmRegId,vTripStatus,vLang,eChangeLang FROM `register_user` WHERE iUserId='$iUserId'";
			$Data = $obj->MySQLSelect($sql);
			
			/*$iCabRequestId= get_value('cab_request_now', 'max(iCabRequestId)', 'iUserId',$iUserId,'','true');
			$eStatus_cab= get_value('cab_request_now', 'eStatus', 'iCabRequestId',$iCabRequestId,'','true');*/
			$sql_cab = "SELECT iCabRequestId,eStatus FROM cab_request_now WHERE iUserId = '".$iUserId."' ORDER BY iCabRequestId DESC LIMIT 0,1";
			$Data_cab = $obj->MySQLSelect($sql_cab);
			$iCabRequestId = $Data_cab[0]['iCabRequestId']; 
			$eStatus_cab = $Data_cab[0]['eStatus'];
			if(count($Data)>0){
				
				## Check and update Session ID ##
				/*$where = " iUserId = '".$iUserId."'";
					$Update_Session['tSessionId'] = session_id().time();
				$Update_Session_id = $obj->MySQLQueryPerform("register_user", $Update_Session, 'update', $where);*/
				## Check and update Session ID ##
				
				$iGCMregID=$Data[0]['iGcmRegId'];
				$vTripStatus=$Data[0]['vTripStatus'];
				
				// if($GCMID!=''){
				
				// if($iGCMregID != $GCMID){
				// $where = " iUserId = '$iUserId' ";
				
				// $Data_update_passenger['iGcmRegId']=$GCMID;
				// $Data_update_passenger['eDeviceType']=$deviceType;
				
				// $id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
				// }
				
				// }
				
				
				if($GCMID != "" && $GCMID != $iGCMregID){
					$returnArr['Action'] = "0";
          $returnArr['eStatus'] ="";
					$returnArr['message'] = "SESSION_OUT";
					echo json_encode($returnArr);
					exit;
				}
				
				if($Data[0]['vLang'] == ""){
					$where = " iUserId = '$iUserId' ";
					$vLang= get_value('language_master', 'vCode', 'eDefault','Yes','','true');
					$Data_update_passenger['vLang']=$vLang;
					$updateid = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
          $Data[0]['vLang'] = $vLang;
				}
				
				if($eStatus_cab == "Requesting"){
					$where = " iCabRequestId = '$iCabRequestId' ";
					
					$Data_update_cab_now['eStatus']="Cancelled";
					
					$id = $obj->MySQLQueryPerform("cab_request_now",$Data_update_cab_now,'update',$where);
				}
        if(($vLangCode != $Data[0]['vLang']) || $Data[0]['eChangeLang'] == "Yes"){
						$returnArr['changeLangCode'] ="Yes";
						$returnArr['UpdatedLanguageLabels'] = getLanguageLabelsArr($Data[0]['vLang'],"1");
						$returnArr['vLanguageCode'] = $Data[0]['vLang'];
            $sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '".$Data[0]['vLang']."' ";
					  $Data_checkLangCode = $obj->MySQLSelect($sql_LangCode);
            $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
						$returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
            $where = " iUserId = '$iUserId' ";
  					$Data_update_passenger_lang['eChangeLang'] = "No";
  					$updateLangid = $obj->MySQLQueryPerform("register_user",$Data_update_passenger_lang,'update',$where);
            $Data[0]['eChangeLang'] = "No";
            $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ";
        		$defLangValues = $obj->MySQLSelect($sql);
            $returnArr['LIST_LANGUAGES']=$defLangValues;
            for($i = 0;$i<count($defLangValues); $i++){
               if($defLangValues[$i]['eDefault'] == "Yes"){
                  $returnArr['DefaultLanguageValues']=$defLangValues[$i];
               }
            }
            $sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ";
            $defCurrencyValues =  $obj->MySQLSelect($sql);
        		$returnArr['LIST_CURRENCY']=$defCurrencyValues;
            for($i = 0;$i<count($defCurrencyValues); $i++){
               if($defCurrencyValues[$i]['eDefault'] == "Yes"){
                  $returnArr['DefaultCurrencyValues']=$defCurrencyValues[$i];
               }
            }
				}else{
						$returnArr['changeLangCode'] ="No";
				}
				
				$returnArr['Action'] = "1";
				$returnArr['message'] = getPassengerDetailInfo($iUserId,'');
				
				createUserLog($UserType,"Yes",$iUserId,"Android");
				
				}else{
				$returnArr['Action'] ="0";
        $returnArr['eStatus'] ="";
				$returnArr['message'] ="SESSION_OUT";
			}
			
			echo json_encode($returnArr);
			}else{
			$sql = "SELECT iGcmRegId,vLang,eChangeLang FROM `register_driver` WHERE iDriverId='$iUserId'";
			$Data = $obj->MySQLSelect($sql);
			
			if(count($Data)>0){
				
				$iGCMregID=$Data[0]['iGcmRegId'];
				
				## Check and update Session ID ##
				/*$where = " iDriverId = '$iUserId' ";
					$Update_Session['tSessionId'] = session_id().time();
				$Update_Session_id = $obj->MySQLQueryPerform("register_driver", $Update_Session, 'update', $where);  */
				## Check and update Session ID ##
				
				if($Data[0]['vLang'] == ""){
					$where = " iDriverId = '$iUserId' ";
					$vLang= get_value('language_master', 'vCode', 'eDefault','Yes','','true');
					$Data_update_driver['vLang']=$vLang;
					$updateid = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
				}
				
				// if($GCMID!=''){
				
				// if($iGCMregID!=$GCMID){
				// $where = " iDriverId = '$iUserId' ";
				
				// $Data_update_driver['iGcmRegId']=$GCMID;
				
				// $id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
				// }
				
				// }
				if($GCMID != "" && $GCMID != $iGCMregID){
					$returnArr['Action'] = "0";
          $returnArr['eStatus'] ="";
					$returnArr['message'] = "SESSION_OUT";
					echo json_encode($returnArr);
					exit;
				}
        if(($vLangCode != $Data[0]['vLang']) || $Data[0]['eChangeLang'] == "Yes"){
						$returnArr['changeLangCode'] ="Yes";
						$returnArr['UpdatedLanguageLabels'] = getLanguageLabelsArr($Data[0]['vLang'],"1");
						$returnArr['vLanguageCode'] = $Data[0]['vLang'];
            $sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '".$Data[0]['vLang']."' ";
					  $Data_checkLangCode = $obj->MySQLSelect($sql_LangCode);
            $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
						$returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
            $where = " iDriverId = '$iUserId' ";
  					$Data_update_passenger_lang['eChangeLang'] = "No";
  					$updateLangid = $obj->MySQLQueryPerform("register_driver",$Data_update_passenger_lang,'update',$where);
            $Data[0]['eChangeLang'] = "No";
            $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ";
        		$defLangValues = $obj->MySQLSelect($sql);
            $returnArr['LIST_LANGUAGES']=$defLangValues;
            for($i = 0;$i<count($defLangValues); $i++){
               if($defLangValues[$i]['eDefault'] == "Yes"){
                  $returnArr['DefaultLanguageValues']=$defLangValues[$i];
               }
            }
            $sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ";
            $defCurrencyValues =  $obj->MySQLSelect($sql);
        		$returnArr['LIST_CURRENCY']=$defCurrencyValues;
            for($i = 0;$i<count($defCurrencyValues); $i++){
               if($defCurrencyValues[$i]['eDefault'] == "Yes"){
                  $returnArr['DefaultCurrencyValues']=$defCurrencyValues[$i];
               }
            }
				}else{
						$returnArr['changeLangCode'] ="No";
				}
				
				$returnArr['Action'] = "1";
				$returnArr['message'] = getDriverDetailInfo($iUserId);
				
				createUserLog($UserType,"Yes",$iUserId,"Android");
				
				}else{
				$returnArr['Action'] ="0";
        $returnArr['eStatus'] ="";
				$returnArr['message'] ="SESSION_OUT";
			}
			
			echo json_encode($returnArr);
			
		}
		
		
		
	}
	
	###########################################################################
	
	if($type=="LoginWithFB"){
		
		$fbid             = isset($_REQUEST["iFBId"]) ? $_REQUEST["iFBId"] : '';
		$Fname             = isset($_REQUEST["vFirstName"]) ? $_REQUEST["vFirstName"] : '';
		$Lname             = isset($_REQUEST["vLastName"]) ? $_REQUEST["vLastName"] : '';
		$email            = isset($_REQUEST["vEmail"]) ? $_REQUEST["vEmail"] : '';
		$GCMID            = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
		$vDeviceType            = isset($_REQUEST["vDeviceType"]) ? $_REQUEST["vDeviceType"] : 'Android';
		$eLoginType            = isset($_REQUEST["eLoginType"]) ? $_REQUEST["eLoginType"] : 'Facebook';
		$user_type   = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$vCurrency = isset($_REQUEST["vCurrency"]) ? $_REQUEST["vCurrency"] : '';
		$vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
		$vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
    $vImageURL = isset($_REQUEST["vImageURL"]) ? $_REQUEST["vImageURL"] : '';
		
		if($fbid == ""){
			$returnArr['Action'] ="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
			
			echo json_encode($returnArr);exit;
		}
		//$DeviceType = "Android";
		$DeviceType = $vDeviceType; 
		
		if($user_type == "Passenger"){
			$tblname = "register_user";
			$iMemberId = 'iUserId';
			$vCurrencyMember = "vCurrencyPassenger";
			$vImageFiled = 'vImgName';
			}else{
			$tblname = "register_driver";
			$iMemberId = 'iDriverId';
			$vCurrencyMember = "vCurrencyDriver";
			$vImageFiled = 'vImage';
		} 
		
		if($user_type == "Passenger"){
			$sql = "SELECT iUserId as iUserId,eStatus,vFbId,vLang,vTripStatus,eSignUpType,vImgName as vImage  FROM $tblname WHERE 1=1 AND IF('$email'!='',vEmail = '$email',0) OR IF('$fbid'!='',vFbId = '$fbid',0)";
			}else{
			$sql = "SELECT iDriverId as iUserId,eStatus,vFbId,vLang,vTripStatus,eSignUpType,vImage as vImage FROM $tblname WHERE 1=1 AND IF('$email'!='',vEmail = '$email',0) OR IF('$fbid'!='',vFbId = '$fbid',0)";
		}
		
		/*if($email != ''){
            $sql = "SELECT iUserId,eStatus,vFbId,vLang,vTripStatus FROM `register_user` WHERE vEmail='$email' OR vFbId='$fbid'";
			}else{
            $sql = "SELECT iUserId,eStatus,vFbId,vLang,vTripStatus FROM `register_user` WHERE vFbId='$fbid'";
		}   */
		$Data = $obj->MySQLSelect($sql);
		if($user_type == "Passenger"){
			/*$iCabRequestId= get_value('cab_request_now', 'max(iCabRequestId)', 'iUserId',$Data[0]['iUserId'],'','true');
			$eStatus_cab= get_value('cab_request_now', 'eStatus', 'iCabRequestId',$iCabRequestId,'','true');*/
			$sql_cabrequest = "SELECT iCabRequestId,eStatus FROM `cab_request_now` WHERE iUserId='".$Data[0]['iUserId']."' ORDER BY iCabRequestId DESC LIMIT 0,1";
			$Data_cabrequest = $obj->MySQLSelect($sql_cabrequest);
			$iCabRequestId = $Data_cabrequest[0]['iCabRequestId'];
			$eStatus_cab = $Data_cabrequest[0]['eStatus'];
		}
		if ( count($Data) > 0 ) {
			if($Data[0]['eStatus']=="Active" || ($user_type == "Driver" && $Data[0]['eStatus']!="Deleted")){
				
                $iUserId_passenger=$Data[0]['iUserId'];
                //$where = " iUserId = '$iUserId_passenger' ";
                $where = " $iMemberId = '$iUserId_passenger' ";
                if($Data[0]['vLang'] == "" && $vLang == ""){
					$vLang= get_value('language_master', 'vCode', 'eDefault','Yes','','true');
					$Data_update_passenger['vLang']=$vLang;
				}
                if($vLang != ""){
					$Data_update_passenger['vLang']=$vLang;
					$Data[0]['vLang'] = $vLang; 
				}
                if($vCurrency != ""){
					$Data_update_passenger[$vCurrencyMember]=$vCurrency;
				}
                
                ## Upload Image of Member if SignUp from Google, Facebook Or Twitter ##
                $vImage = $Data[0]['vImage'];
                if($fbid != 0 || $fbid != ""){
					$userid = $Data[0]['iUserId']; 
					$eSignUpType = $eLoginType;
					$UserImage = UploadUserImage($userid,$user_type,$eSignUpType,$fbid,$vImageURL);
					if($UserImage != ""){
						$where = " $iMemberId = '$userid' ";
						$Data_update_image_member[$vImageFiled] = $UserImage;
						$imageuploadid = $obj->MySQLQueryPerform($tblname,$Data_update_image_member,'update',$where);
					}   
				}
                ## Upload Image of Member if SignUp from Google, Facebook Or Twitter ##
				
				if($GCMID!=''){
					
					$Data_update_passenger['iGcmRegId']=$GCMID;
					$Data_update_passenger['eDeviceType']=$DeviceType;
					$Data_update_passenger['vFbId']=$fbid;
					$Data_update_passenger['eSignUpType']=$eLoginType;
					$Data_update_passenger['tSessionId'] = session_id().time();
					$Data_update_passenger['vFirebaseDeviceToken']=$vFirebaseDeviceToken;
					/*if($Data[0]['vFbId'] =='' || $Data[0]['vFbId'] == "0"){
						$Data_update_passenger['vFbId']=$fbid;
					} */
					
					$id = $obj->MySQLQueryPerform($tblname,$Data_update_passenger,'update',$where);
				}
				
				
				if($user_type == "Passenger"){
					if($eStatus_cab == "Requesting"){
						$where1 = " iCabRequestId = '$iCabRequestId' ";
						$Data_update_cab_now['eStatus']="Cancelled";
						
						$id = $obj->MySQLQueryPerform("cab_request_now",$Data_update_cab_now,'update',$where1);
					}
				}  
				
				
				
					$returnArr['changeLangCode'] ="Yes";
					$returnArr['UpdatedLanguageLabels'] = getLanguageLabelsArr($Data[0]['vLang'],"1");
					$returnArr['vLanguageCode'] = $Data[0]['vLang'];
				
					$sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '".$Data[0]['vLang']."' ";
					$Data_checkLangCode = $obj->MySQLSelect($sql_LangCode);
					$returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
					$returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
          $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ";
      		$defLangValues = $obj->MySQLSelect($sql);
          $returnArr['LIST_LANGUAGES']=$defLangValues;
          for($i = 0;$i<count($defLangValues); $i++){
             if($defLangValues[$i]['eDefault'] == "Yes"){
                $returnArr['DefaultLanguageValues']=$defLangValues[$i];
             }
          }
          $sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ";
          $defCurrencyValues =  $obj->MySQLSelect($sql);
      		$returnArr['LIST_CURRENCY']=$defCurrencyValues;
          for($i = 0;$i<count($defCurrencyValues); $i++){
             if($defCurrencyValues[$i]['eDefault'] == "Yes"){
                $returnArr['DefaultCurrencyValues']=$defCurrencyValues[$i];
             }
          }
				
				
				$returnArr['Action'] = "1";
				if($user_type == "Passenger"){
					$returnArr['message'] = getPassengerDetailInfo($Data[0]['iUserId'],'');
					createUserLog("Passenger","No",$Data[0]['iUserId'],"Android");
					}else{
					$returnArr['message'] = getDriverDetailInfo($Data[0]['iUserId'],'');
					createUserLog("Driver","No",$Data[0]['iUserId'],"Android");
				}  
				
				echo json_encode($returnArr);exit;
				
				}else{
				$returnArr['Action']="0";     
  				/*if($Data[0]['eStatus'] !="Deleted"){
  					$returnArr['message'] ="LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER";
  					}else{
  					$returnArr['message'] ="LBL_ACC_DELETE_TXT";
				}*/
				if($Data[0]['eStatus'] =="Deleted"){
  					$returnArr['message'] ="LBL_ACC_DELETE_TXT";
				}
				echo json_encode($returnArr);exit;
			}
			
			}else{
			$returnArr['Action']="0";
			$returnArr['message'] ="DO_REGISTER";
			echo json_encode($returnArr);exit;
		}
	}
	
	########################### Get Available Taxi ##############################
	
	
	if ($type == "loadAvailableCab") {
		
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$passengerLat = isset($_REQUEST["PassengerLat"]) ? $_REQUEST["PassengerLat"] : '';
		$passengerLon = isset($_REQUEST["PassengerLon"]) ? $_REQUEST["PassengerLon"] : '';
		$iVehicleTypeId = isset($_REQUEST["iVehicleTypeId"]) ? $_REQUEST["iVehicleTypeId"] : '';
		$PickUpAddress = isset($_REQUEST["PickUpAddress"]) ? $_REQUEST["PickUpAddress"] : '';
		$geoCodeResult = isset($_REQUEST["currentGeoCodeResult"]) ? $_REQUEST["currentGeoCodeResult"] : '';
		$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		$scheduleDate =  isset($_REQUEST["scheduleDate"]) ? $_REQUEST["scheduleDate"] : '';
		//$APP_TYPE = $generalobj->getConfigurations("configurations","APP_TYPE");
		
		//$address_data = fetch_address_geocode($PickUpAddress,$geoCodeResult);
		
		if($APP_TYPE == "UberX" && $scheduleDate != ""){
			$Check_Driver_UFX = "Yes";
			$sdate = explode(" ",$scheduleDate);
			$shour = explode("-",$sdate[1]);
			$shour1 = $shour[0];   
			$Check_Date_Time = $sdate[0]." ".$shour1.":00:00";
			}else{
			$Check_Driver_UFX = "No";
			$Check_Date_Time = "";
		}
		
		$address_data['PickUpAddress'] = $PickUpAddress;
		
		$DataArr = getOnlineDriverArr($passengerLat,$passengerLon,$address_data,"No","No",$Check_Driver_UFX,$Check_Date_Time);
		$Data = $DataArr['DriverList'];
		// print_r($Data);
		// die;
		
		//$ALLOW_SERVICE_PROVIDER_AMOUNT = $generalobj->getConfigurations("configurations","ALLOW_SERVICE_PROVIDER_AMOUNT");
		$iVehicleCategoryId=get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId',$iVehicleTypeId,'','true');
		$iParentId = get_value('vehicle_category', 'iParentId', 'iVehicleCategoryId', $iVehicleCategoryId,'','true');
		if($iParentId == 0){
			$ePriceType=get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId',$iVehicleCategoryId,'','true');  
			}else{ 
			$ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId,'','true'); 
		}
		//$ePriceType=get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId',$iVehicleCategoryId,'','true');  
		$ALLOW_SERVICE_PROVIDER_AMOUNT = $ePriceType == "Provider"? "Yes" :"No";
		
		/*$vLang=get_value('register_user', 'vLang', 'iUserId', $iUserId,'','true');
			$vCurrencyPassenger=get_value('register_user', 'vCurrencyPassenger', 'iUserId', $iUserId,'','true');
			$vCurrencySymbol=get_value('currency', 'vSymbol', 'vName', $vCurrencyPassenger,'','true');
		$priceRatio=get_value('currency', 'Ratio', 'vName', $vCurrencyPassenger,'','true');*/
		$sqlp = "SELECT ru.vCurrencyPassenger,ru.vLang,cu.vSymbol,cu.Ratio FROM register_user as ru LEFT JOIN currency as cu ON ru.vCurrencyPassenger = cu.vName WHERE iUserId = '".$iUserId."'";
		$passengerData = $obj->MySQLSelect($sqlp);
		$vLang = $passengerData[0]['vLang'];  
		$vCurrencyPassenger = $passengerData[0]['vCurrencyPassenger'];
		$vCurrencySymbol = $passengerData[0]['vSymbol'];
		$priceRatio = $passengerData[0]['Ratio'];
		$i=0;
		while ( count($Data) > $i ) {
			if($Data[$i]['vImage']!="" && $Data[$i]['vImage']!="NONE"){
				$Data[$i]['vImage']="3_".$Data[$i]['vImage'];
			}
			$driverVehicleID = $Data[$i]['iDriverVehicleId'];
			
			$sql = "SELECT dv.*, make.vMake AS make_title, model.vTitle model_title FROM `driver_vehicle` dv, make, model
			WHERE dv.iMakeId = make.iMakeId
			AND dv.iModelId = model.iModelId
			AND iDriverVehicleId='$driverVehicleID'";
			$rows_driver_vehicle    = $obj->MySQLSelect($sql);
			$fAmount = "";
			if($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes"){
				$sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='".$rows_driver_vehicle[0]['iDriverVehicleId']."' AND iVehicleTypeId='".$iVehicleTypeId."'";
				$serviceProData = $obj->MySQLSelect($sqlServicePro);
				
				$vehicleTypeData = get_value('vehicle_type', 'eFareType,fPricePerHour,fFixedFare', 'iVehicleTypeId', $iVehicleTypeId);
				if($vehicleTypeData[0]['eFareType'] == "Fixed"){
					$fAmount = $vCurrencySymbol.formatNum($vehicleTypeData[0]['fFixedFare']*$priceRatio);
					}else if($vehicleTypeData[0]['eFareType'] == "Hourly"){
					$fAmount = $vCurrencySymbol.formatNum($vehicleTypeData[0]['fPricePerHour']*$priceRatio)."/hour";
				}
				
				if(count($serviceProData) > 0){
					$fAmount = formatNum($serviceProData[0]['fAmount']*$priceRatio);
					if($vehicleTypeData[0]['eFareType'] == "Fixed"){
						$fAmount = $vCurrencySymbol.$fAmount;
						}else if($vehicleTypeData[0]['eFareType'] == "Hourly"){
						$fAmount = $vCurrencySymbol.$fAmount."/hour";
					}
				}
				
				$rows_driver_vehicle[0]['fAmount'] = $fAmount;
				$rows_driver_vehicle[0]['vCurrencySymbol'] = $vCurrencySymbol;
			}
			
			$Data[$i]['DriverCarDetails'] = $rows_driver_vehicle[0];
			
			$i++;
		}
		$where = " iUserId='".$iUserId."'";
		$data['vLatitude']=$passengerLat;
		$data['vLongitude']=$passengerLon;
		$data['vRideCountry']=$vCountryCode;
		$data['tLastOnline']=@date("Y-m-d H:i:s");
		$obj->MySQLQueryPerform("register_user",$data,'update',$where);
		# Update User Location Date #
		Updateuserlocationdatetime($iUserId,"Passenger",$vTimeZone);    
		# Update User Location Date #     
		
		$returnArr['AvailableCabList'] = $Data;
		$returnArr['PassengerLat'] = $passengerLat;
		$returnArr['PassengerLon'] = $passengerLon;
		
		if($APP_TYPE == "Delivery"){
			$ssql.= " AND eType = 'Deliver'";
			}else if($APP_TYPE == "Ride-Delivery"){
			$ssql.= " AND ( eType = 'Deliver' OR eType = 'Ride')";
			}else if($APP_TYPE == "Ride-Delivery-UberX"){
			$ssql.= " AND ( eType = 'Deliver' OR eType = 'Ride' OR eType = 'UberX')";
			}else{
			$ssql.= " AND eType = '".$APP_TYPE."'";
		}
		
		$pickuplocationarr = array($passengerLat,$passengerLon);
		$GetVehicleIdfromGeoLocation = GetVehicleTypeFromGeoLocation($pickuplocationarr);
		//$sql23 = "SELECT * FROM `vehicle_type` WHERE (iCityId='".$cityId."' OR iCityId = '-1') AND (iStateId='".$stateId."' OR iStateId = '-1') AND (iCountryId='".$countryId."' OR iCountryId = '-1') ORDER BY iVehicleTypeId ASC";
		$sql23 = "SELECT * FROM `vehicle_type` WHERE iLocationid IN ($GetVehicleIdfromGeoLocation) $ssql ORDER BY iVehicleTypeId ASC";
		$vehicleTypes = $obj->MySQLSelect($sql23);
		
		// $vehicleTypes = get_value('vehicle_type', '*', '', '',' ORDER BY iVehicleTypeId ASC');
		
		
		for($i=0;$i<count($vehicleTypes);$i++){
			$Photo_Gallery_folder = $tconfig["tsite_upload_images_vehicle_type_path"].'/'.$vehicleTypes[$i]['iVehicleTypeId'].'/android/'.$vehicleTypes[$i]['vLogo'];
			if($vehicleTypes[$i]['vLogo'] != "" && file_exists($Photo_Gallery_folder)){
				$vehicleTypes[$i]['vLogo'] = $vehicleTypes[$i]['vLogo']; 
				}else{
				$vehicleTypes[$i]['vLogo'] = "";
			}
			$vehicleTypes[$i]['fPricePerKM']= round($vehicleTypes[$i]['fPricePerKM'] * $priceRatio,2);
			$vehicleTypes[$i]['fPricePerMin']= round($vehicleTypes[$i]['fPricePerMin'] * $priceRatio,2);
			$vehicleTypes[$i]['iBaseFare']= round($vehicleTypes[$i]['iBaseFare'] * $priceRatio,2);
			$vehicleTypes[$i]['fCommision']= round($vehicleTypes[$i]['fCommision'] * $priceRatio,2);
			$vehicleTypes[$i]['iMinFare']= round($vehicleTypes[$i]['iMinFare'] * $priceRatio,2);
			$vehicleTypes[$i]['FareValue']= round($vehicleTypes[$i]['fFixedFare'] * $priceRatio,2);
			$vehicleTypes[$i]['vVehicleType']= $vehicleTypes[$i]["vVehicleType_".$vLang];
		}
		
		if($APP_TYPE == "UberX"){
			$returnArr['VehicleTypes'] = array(); 
			}else{
			$returnArr['VehicleTypes'] = $vehicleTypes;
		}
		//$returnArr['CurrentCity'] = $address_data['city'];
		//$returnArr['CurrentCountry'] = $address_data['country'];
		
		echo json_encode($returnArr);
	}
	
	###########################################################################
	###########################################################################
	if($type=="getDriverStates"){
		$driverId = isset($_REQUEST['iDriverId'])?clean($_REQUEST['iDriverId']):'';
		$userType = isset($_REQUEST['UserType'])?clean($_REQUEST['UserType']):'Driver';
		
		$docUpload = 'Yes';
		$driverVehicleUpload = 'Yes';
		$driverStateActive = 'Yes';
    $driverVehicleDocumentUpload = 'Yes';
		//$APP_TYPE = $generalobj->getConfigurations("configurations", "APP_TYPE");
		
		$vCountry = get_value('register_driver', 'vCountry', 'iDriverId', $driverId,'',true);
		
		$sql1= "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, COALESCE(dl.doc_id,  '' ) as doc_id,COALESCE(dl.doc_masterid, '') as masterid_list ,COALESCE(dl.ex_date, '') as ex_date,COALESCE(dl.doc_file, '') as doc_file, COALESCE(dl.status, '') as status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='".$driverId."' ) dl on dl.doc_masterid=dm.doc_masterid  
		where dm.doc_usertype='driver' and (dm.country='".$vCountry."' OR dm.country='All') and dm.status='Active' ";
		$db_document = $obj->MySQLSelect($sql1);
		
		if(count($db_document) > 0){
			for($i=0;$i<count($db_document);$i++){
				if($db_document[$i]['doc_file'] == ""){
					$docUpload = 'No';
				}
			}
		}else{
        $docUpload = 'No';
		}
		
		if($APP_TYPE != 'UberX'){
			// echo $docUpload; die;
      ## Count Driver Vehicle ##
      $sql = "SELECT count(iDriverVehicleId) as TotalVehicles from driver_vehicle WHERE iDriverId = '".$driverId."' AND eStatus != 'Deleted'";
		  $db_Total_vehicle = $obj->MySQLSelect($sql);
      $TotalVehicles = $db_Total_vehicle[0]['TotalVehicles'];   
      $returnArr['TotalVehicles'] = strval($TotalVehicles);      
      ## Count Driver Vehicle ##
      $sql = "SELECT iDriverVehicleId from driver_vehicle WHERE iDriverId = '".$driverId."' AND eStatus != 'Deleted'";
			$db_drv_vehicle = $obj->MySQLSelect($sql);
			if(count($db_drv_vehicle) == 0){
				$driverVehicleUpload = 'No';
				}else if($driverVehicleUpload != 'No'){
				$test = array();
				# Check For Driver's selected vehicle's document are upload or not #
				$sql= "SELECT dl.*,dv.iDriverVehicleId FROM `driver_vehicle` AS dv LEFT JOIN document_list as dl ON dl.doc_userid=dv.iDriverVehicleId WHERE dv.iDriverId='$driverId' AND dl.doc_usertype = 'car' AND dv.eStatus != 'Deleted' ";
				$db_selected_vehicle = $obj->MySQLSelect($sql);
				if(count($db_selected_vehicle) > 0){
					for($i=0;$i<count($db_selected_vehicle);$i++){
						if($db_selected_vehicle[$i]['doc_file'] == ""){
							$test[] = '1';
						}
					}
				}
				if(count($test) == count($db_selected_vehicle)){
					$driverVehicleUpload = 'No';
				}
        ## Checking For All document's are upload or not for all vehicle's of driver ##
        /*$sql1= "SELECT doc_masterid FROM document_master where doc_usertype ='car' and ( country='".$vCountry."' OR country='All') and status='Active'";
		    $db_vehicle_document_master = $obj->MySQLSelect($sql1);   
        if(count($db_vehicle_document_master) > 0){
           for($i=0;$i<count($db_vehicle_document_master);$i++){
              $doc_masterid = $db_vehicle_document_master[$i]['doc_masterid']; 
              $sql = "SELECT iDriverVehicleId from driver_vehicle WHERE iDriverId = '".$driverId."' AND eStatus != 'Deleted'";
		          $db_driver_Total_vehicle = $obj->MySQLSelect($sql);
              if(count($db_driver_Total_vehicle) > 0){
                for($j=0;$j<count($db_driver_Total_vehicle);$j++){
                  $iDriverVehicleId = $db_driver_Total_vehicle[$j]['iDriverVehicleId'];
                  $sql = "SELECT doc_id from document_list WHERE doc_masterid = '".$doc_masterid."' AND doc_usertype = 'car' AND doc_userid = '".$iDriverVehicleId."'";
		              $db_driver_vehicle_document_upload = $obj->MySQLSelect($sql);
                  if(count($db_driver_vehicle_document_upload) == 0){
                    $driverVehicleDocumentUpload = "No";
                    break;
                  } 
                } 
              }else{
                  $driverVehicleDocumentUpload = "No";
              }
           }
        }    */ 
        ## Checking For All document's are upload or not for all vehicle's of driver ##         
			}
			}else{
			$sql = "SELECT vCarType from driver_vehicle WHERE iDriverId = '".$driverId."'";
			$db_drv_vehicle = $obj->MySQLSelect($sql);
			if($db_drv_vehicle[0]['vCarType'] == ""){
				$driverVehicleUpload = 'No';
				}else{
				$driverVehicleUpload = 'Yes';
			}
		}
		
		$sql = "SELECT rd.eStatus as driverstatus,cmp.eStatus as cmpEStatus FROM `register_driver` as rd,`company` as cmp WHERE rd.iDriverId='".$driverId."' AND cmp.iCompanyId=rd.iCompanyId";
		$Data = $obj->MySQLSelect($sql);
		
		if(strtolower($Data[0]['driverstatus']) != "active" || strtolower($Data[0]['cmpEStatus']) != "active"){
			$driverStateActive = 'No';
		}
		if($APP_TYPE == "UberX"){
			$sql = "select * from `driver_manage_timing` where iDriverId = '" . $driverId . "'";
			$db_driver_timing = $obj->MySQLSelect($sql);
			if(count($db_driver_timing) > 0){
				$returnArr['IS_DRIVER_MANAGE_TIME_AVAILABLE'] = "Yes";
				}else{
				$returnArr['IS_DRIVER_MANAGE_TIME_AVAILABLE'] = "No";
			}
		}
    if($driverStateActive == "Yes"){
        $docUpload = "Yes";
        $driverVehicleUpload = "Yes";
        $driverVehicleDocumentUpload = "Yes";
		}
		
		$returnArr['Action'] = "1";
		$returnArr['IS_DOCUMENT_PROCESS_COMPLETED'] = $docUpload;
		$returnArr['IS_VEHICLE_PROCESS_COMPLETED'] = $driverVehicleUpload;
    $returnArr['IS_VEHICLE_DOCUMENT_PROCESS_COMPLETED'] = $driverVehicleDocumentUpload;
		$returnArr['IS_DRIVER_STATE_ACTIVATED'] = $driverStateActive;
		echo json_encode($returnArr);
	}
	###########################################################################
	
	
	if($type=="CheckPromoCode"){
		$promoCode = isset($_REQUEST['PromoCode'])?clean($_REQUEST['PromoCode']):'';
		$iUserId = isset($_REQUEST['iUserId'])?clean($_REQUEST['iUserId']):'';
		
		$curr_date=@date("Y-m-d");
		
		$promoCode = strtoupper($promoCode);
		//$sql = "SELECT * FROM coupon where eStatus = 'Active' AND vCouponCode = '".$promoCode."' AND iUsageLimit > iUsed AND (eValidityType = 'Permanent' OR dExpiryDate > '$curr_date')";
		//$sql = "SELECT * FROM coupon where eStatus = 'Active' AND vCouponCode = '".$promoCode."' AND iUsageLimit > iUsed ORDER BY iCouponId ASC LIMIT 0,1";
		$sql = "SELECT * FROM coupon where eStatus = 'Active' AND vCouponCode = '".$promoCode."' ORDER BY iCouponId ASC LIMIT 0,1";
		$data = $obj->MySQLSelect($sql);
		
		if(count($data)>0){
			$sql="select iTripId from trips where vCouponCode = '$promoCode' and iActive = 'Finished' and iUserId='$iUserId'";
			$data_coupon = $obj->MySQLSelect($sql);
			// echo "<pre>";print_r($data_coupon);exit;
			if(!empty($data_coupon)){
				$returnArr['Action']="01";// code is already used one time
				$returnArr["message"] = "LBL_PROMOCODE_ALREADY_USED";
				echo json_encode($returnArr);exit;
				}else{
				$eValidityType = $data[0]['eValidityType'];
				$iUsageLimit = $data[0]['iUsageLimit'];
				$iUsed = $data[0]['iUsed'];
				if($iUsageLimit <= $iUsed){
					$returnArr['Action']="0";// code is invalid due to Usage Limit
					$returnArr["message"] = "LBL_PROMOCODE_COMPLETE_USAGE_LIMIT";
					echo json_encode($returnArr);exit;
				}
				if($eValidityType == "Permanent"){ 
					$returnArr['Action']="1"; // code is valid
          $returnArr["message"] = "LBL_PROMO_APPLIED";
					echo json_encode($returnArr);exit;
					}else{
					$dActiveDate = $data[0]['dActiveDate'];
					$dExpiryDate = $data[0]['dExpiryDate']; 
					if($dActiveDate <= $curr_date && $dExpiryDate >= $curr_date){
						$returnArr['Action']="1"; // code is valid
            $returnArr["message"] = "LBL_PROMO_APPLIED";
						echo json_encode($returnArr);exit;
						}else{
						$returnArr['Action']="0";// code is invalid due to expiration
						$returnArr["message"] = "LBL_PROMOCODE_EXPIRED";
						echo json_encode($returnArr);exit;
					}
				}
			}
			}else{
			$returnArr['Action']="0";// code is invalid
			//$returnArr['Action']="01";// code is used by this user
			$returnArr["message"] = "LBL_INVALID_PROMOCODE";
			echo json_encode($returnArr);exit;
		}
		
	}
	
	###########################################################################
	
	if($type=='estimateFare'){
    $sourceLocation = isset($_REQUEST["sourceLocation"]) ? $_REQUEST["sourceLocation"] : '';
		$destinationLocation = isset($_REQUEST["destinationLocation"]) ? $_REQUEST["destinationLocation"] : '';
		$iUserId          = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$distance          = isset($_REQUEST["distance"]) ? $_REQUEST["distance"] : '';
		$time          = isset($_REQUEST["time"]) ? $_REQUEST["time"] : '';
		$SelectedCar          = isset($_REQUEST["SelectedCar"]) ? $_REQUEST["SelectedCar"] : '';
		
    $sourceLocationArr = explode(",",$sourceLocation);
		$destinationLocationArr = explode(",",$destinationLocation);
		/*$vCurrencyPassenger=get_value('register_user', 'vCurrencyPassenger', 'iUserId', $iUserId,'','true');
		$priceRatio=get_value('currency', 'Ratio', 'vName', $vCurrencyPassenger,'','true');*/
		$sqlp = "SELECT ru.vCurrencyPassenger,cu.Ratio FROM register_user as ru LEFT JOIN currency as cu ON ru.vCurrencyPassenger = cu.vName WHERE iUserId = '".$iUserId."'";
		$passengerData = $obj->MySQLSelect($sqlp);
		$vCurrencyPassenger=$passengerData[0]['vCurrencyPassenger'];
		$priceRatio=$passengerData[0]['Ratio'];
    $eFlatTrip = "No";
		$fFlatTripPrice = 0;
		
    if($eFlatTrip == "No") {
		$Fare_data=calculateFareEstimate($time,$distance,$SelectedCar,$iUserId,1);
		
		$Fare_data[0]['Distance']= $distance ==NULL ? "0" : strval(round($distance,2));
        $Fare_data[0]['Time']= $time == NULL ? "0" : strval(round($time,2));
		$Fare_data[0]['total_fare'] = number_format(round($Fare_data[0]['total_fare'] * $priceRatio,1),2);
		$Fare_data[0]['iBaseFare'] = number_format(round($Fare_data[0]['iBaseFare'] * $priceRatio,1),2);
		$Fare_data[0]['fPricePerMin'] = number_format(round($Fare_data[0]['fPricePerMin'] * $priceRatio,1),2);
		$Fare_data[0]['fPricePerKM'] = number_format(round($Fare_data[0]['fPricePerKM'] * $priceRatio,1),2);
		$Fare_data[0]['fCommision'] = number_format(round($Fare_data[0]['fCommision'] * $priceRatio,1),2);
        $Fare_data[0]['eFlatTrip']= "No";
		if($Fare_data[0]['MinFareDiff'] >0){
			$Fare_data[0]['MinFareDiff'] = number_format(round($Fare_data[0]['MinFareDiff'] * $priceRatio,1),2);
			}else{
			$Fare_data[0]['MinFareDiff'] = "0";
		}
		$Fare_data[0]['MinFareDiff'] = "0";
    }else{
        $Fare_data[0]['Distance']= "0.00";
  			$Fare_data[0]['Time']= "0.00";
  			$Fare_data[0]['total_fare'] = $data_flattrip['Flatfare'];//number_format(round($fFlatTripPrice * $priceRatio,1),2);
  			$Fare_data[0]['iBaseFare'] = number_format(round($fFlatTripPrice * $priceRatio,1),2);
  			$Fare_data[0]['fPricePerMin'] = "0.00";
  			$Fare_data[0]['fPricePerKM'] = "0.00";
  			$Fare_data[0]['fCommision'] = number_format(round($fFlatTripPrice * $priceRatio,1),2);
  			$Fare_data[0]['eFlatTrip']= "Yes";
  			$Fare_data[0]['MinFareDiff'] = "0.00";
  			$Fare_data[0]['Flatfare'] = $data_flattrip['Flatfare'];
    }    
		$Fare_data[0]['Action'] = "1";
		
		echo json_encode($Fare_data[0]);
	}
	
	###########################################################################
	if($type=='estimateFareNew'){
		$iUserId          = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$distance          = isset($_REQUEST["distance"]) ? $_REQUEST["distance"] : '';
		$time          = isset($_REQUEST["time"]) ? $_REQUEST["time"] : '';
		$SelectedCar          = isset($_REQUEST["SelectedCar"]) ? $_REQUEST["SelectedCar"] : '';
		$StartLatitude    = isset($_REQUEST["StartLatitude"]) ? $_REQUEST["StartLatitude"] : '0.0';
		$EndLongitude    =isset($_REQUEST["EndLongitude"]) ? $_REQUEST["EndLongitude"] : '0.0';
    $DestLatitude    = isset($_REQUEST["DestLatitude"]) ? $_REQUEST["DestLatitude"] : '';
		$DestLongitude    =isset($_REQUEST["DestLongitude"]) ? $_REQUEST["DestLongitude"] : '';
		$iQty = isset($_REQUEST["iQty"]) ? $_REQUEST["iQty"] : '1';
		$PromoCode = isset($_REQUEST["PromoCode"]) ? $_REQUEST["PromoCode"] : '';
		$SelectedCarTypeID = isset($_REQUEST["SelectedCarTypeID"]) ? $_REQUEST["SelectedCarTypeID"] : ''; 
		
		$time = round(($time / 60),2);
		$distance = round(($distance / 1000),2);
    $isDestinationAdded = "No";
    if($DestLatitude != "" && $DestLongitude != ""){
       $isDestinationAdded = "Yes";
    }
    $sourceLocationArr = array($StartLatitude,$EndLongitude);       
  	$destinationLocationArr = array($DestLatitude,$DestLongitude);
		
    //$Fare_data=calculateFareEstimateAll($time,$distance,$SelectedCar,$iUserId,1);
		$Fare_data=calculateFareEstimateAll($time,$distance,$SelectedCar,$iUserId,1,"","",$PromoCode,1,0,0,0,"","Passenger",$iQty,$SelectedCarTypeID,$isDestinationAdded,$eFlatTrip,$fFlatTripPrice,$sourceLocationArr,$destinationLocationArr);
		
		$returnArr["Action"] = "1";
		$returnArr["message"] = $Fare_data;
    //$returnArr['eFlatTrip'] = $eFlatTrip;
		echo json_encode($returnArr);
	}
	###########################################################################
	###########################################################################
	if($type=='getEstimateFareDetailsArr'){
    //$sourceLocation = isset($_REQUEST["sourceLocation"]) ? $_REQUEST["sourceLocation"] : '';
		//$destinationLocation = isset($_REQUEST["destinationLocation"]) ? $_REQUEST["destinationLocation"] : '';
		$iUserId          = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$distance          = isset($_REQUEST["distance"]) ? $_REQUEST["distance"] : '';
		$time          = isset($_REQUEST["time"]) ? $_REQUEST["time"] : '';
		$SelectedCar          = isset($_REQUEST["SelectedCar"]) ? $_REQUEST["SelectedCar"] : '';
		$StartLatitude    = isset($_REQUEST["StartLatitude"]) ? $_REQUEST["StartLatitude"] : '0.0';
		$EndLongitude    =isset($_REQUEST["EndLongitude"]) ? $_REQUEST["EndLongitude"] : '0.0';
    $DestLatitude    = isset($_REQUEST["DestLatitude"]) ? $_REQUEST["DestLatitude"] : '';
		$DestLongitude    =isset($_REQUEST["DestLongitude"]) ? $_REQUEST["DestLongitude"] : '';
		$promoCode = isset($_REQUEST['PromoCode'])?clean($_REQUEST['PromoCode']):'';
		$userType = isset($_REQUEST["UserType"]) ? $_REQUEST['UserType'] : '';
		$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : '';
    $isDestinationAdded = isset($_REQUEST['isDestinationAdded']) ? trim($_REQUEST['isDestinationAdded']) : 'Yes';   // Yes , No
		if($userType == "" || $userType == NULL){
			$userType = $GeneralUserType;    
		}
    if($isDestinationAdded == "" || $isDestinationAdded == NULL){
			$isDestinationAdded = "Yes";    
		}
    ######### Checking For Flattrip #########
    if($isDestinationAdded == "Yes"){    
      $sourceLocationArr = array($StartLatitude,$EndLongitude);       
  		$destinationLocationArr = array($DestLatitude,$DestLongitude);
  		$eFlatTrip = "No"; 
      $fFlatTripPrice = 0;
    }else{
      $eFlatTrip = "No"; 
      $fFlatTripPrice = 0;
    }  
    ######### Checking For Flattrip #########
    $curr_date=@date("Y-m-d");
		$time = round(($time / 60),2);
		$distance = round(($distance / 1000),2);
    $Fare_data=calculateFareEstimateAll($time,$distance,$SelectedCar,$iUserId,1,"","",$promoCode,1,0,0,0,"DisplySingleVehicleFare",$userType,1,"",$isDestinationAdded,$eFlatTrip,$fFlatTripPrice);
    /*
    if($eFlatTrip == "No") {
		$curr_date=@date("Y-m-d");
		$time = round(($time / 60),2);
		$distance = round(($distance / 1000),2);
		$Fare_data=calculateFareEstimateAll($time,$distance,$SelectedCar,$iUserId,1,"","",$promoCode,1,0,0,0,"DisplySingleVehicleFare",$userType,1,"",$isDestinationAdded,$eFlatTrip,$fFlatTripPrice);
    }else{
       if($userType == "Passenger") {
    			$vCurrencyPassenger=get_value('register_user', 'vCurrencyPassenger', 'iUserId', $iUserId,'','true');
          $userlangcode = get_value("register_user", "vLang", "iUserId", $iUserId, '', 'true');
    	 }else{
          $vCurrencyPassenger=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iUserId,'','true');
    			$userlangcode = get_value("register_driver", "vLang", "iDriverId", $iUserId, '', 'true');
    	 }
       if ($vCurrencyPassenger == "" || $vCurrencyPassenger == NULL) {
    			$vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
    	 }
       $priceRatio=get_value('currency', 'Ratio', 'vName', $vCurrencyPassenger,'','true');
		   $vSymbol=get_value('currency', 'vSymbol', 'vName', $vCurrencyPassenger,'','true');
       if($userlangcode == "" || $userlangcode == NULL) {
    			$userlangcode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
    	 }
    	 $languageLabelsArr = getLanguageLabelsArr($userlangcode, "1");
       $Fare_data[0]['total_fare'] = round($fFlatTripPrice * $priceRatio, 2);
       $Fare_data[0][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = $vSymbol." ".$Fare_data[0]['total_fare'];
    }     */
		$returnArr["Action"] = "1";
		$returnArr["message"] = $Fare_data;
		echo json_encode($returnArr);
	}
	###########################################################################
	
	if ($type == "updateUserProfileDetail") {
		
		$vName        = isset($_REQUEST["vName"]) ? $_REQUEST["vName"] : '';
		$vLastName        = isset($_REQUEST["vLastName"]) ? stripslashes($_REQUEST["vLastName"]) : '';
		$vPhone      = isset($_REQUEST["vPhone"]) ? $_REQUEST["vPhone"] : '';
		$iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST['iMemberId'] : '';
		$phoneCode = isset($_REQUEST["vPhoneCode"]) ? $_REQUEST['vPhoneCode'] : '';
		$vCountry = isset($_REQUEST["vCountry"]) ? $_REQUEST['vCountry'] : '';
		$currencyCode = isset($_REQUEST["CurrencyCode"]) ? $_REQUEST['CurrencyCode'] : '';
		$languageCode = isset($_REQUEST["LanguageCode"]) ? $_REQUEST['LanguageCode'] : '';
		$userType = isset($_REQUEST["UserType"]) ? $_REQUEST['UserType'] : 'Passenger';
		$vEmail = isset($_REQUEST["vEmail"]) ? $_REQUEST['vEmail'] : '';
		$tProfileDescription = isset($_REQUEST["tProfileDescription"]) ? $_REQUEST['tProfileDescription'] : '';
		
		if($userType != "Driver"){
			$vEmail_userId_check =  get_value('register_user', 'iUserId', 'vEmail',$vEmail,'','true');
			$vPhone_userId_check =  get_value('register_user', 'iUserId', 'vPhone',$vPhone,'','true');
			
			$where = " iUserId = '$iMemberId'";
			$tableName="register_user";
			
			$Data_update_User['vPhoneCode']=$phoneCode;
			$Data_update_User['vCurrencyPassenger']=$currencyCode;
			$currentLanguageCode =  get_value('register_user', 'vLang', 'iUserId',$iMemberId,'','true');
			
			/*$vPhoneCode_orig =  get_value('register_user', 'vPhoneCode', 'iUserId',$iMemberId,'','true');
				$vPhone_orig =  get_value('register_user', 'vPhone', 'iUserId',$iMemberId,'','true');
			$vEmail_orig =  get_value('register_user', 'vEmail', 'iUserId',$iMemberId,'','true');*/
			$sqlp = "SELECT vPhoneCode,vPhone,vEmail FROM register_user WHERE iUserId = '".$iMemberId."'";
			$passengerData = $obj->MySQLSelect($sqlp);
			$vPhoneCode_orig = $passengerData[0]['vPhoneCode'];
			$vPhone_orig = $passengerData[0]['vPhone'];
			$vEmail_orig = $passengerData[0]['vEmail']; 
			}else{
			$vEmail_userId_check =  get_value('register_driver', 'iDriverId', 'vEmail',$vEmail,'','true');
			$vPhone_userId_check =  get_value('register_driver', 'iDriverId', 'vPhone',$vPhone,'','true');
			
			$where = " iDriverId = '$iMemberId'";
			$tableName="register_driver";
			
			$Data_update_User['vCode']=$phoneCode;
			$Data_update_User['vCurrencyDriver']=$currencyCode;
			$Data_update_User['tProfileDescription']=$tProfileDescription;
			/*$currentLanguageCode =  get_value('register_driver', 'vLang', 'iDriverId',$iMemberId,'','true');
				
				$vPhoneCode_orig =  get_value('register_driver', 'vCode', 'iDriverId',$iMemberId,'','true');
				$vPhone_orig =  get_value('register_driver', 'vPhone', 'iDriverId',$iMemberId,'','true');
			$vEmail_orig =  get_value('register_driver', 'vEmail', 'iDriverId',$iMemberId,'','true');*/
			$sqlp = "SELECT vLang,vCode,vPhone,vEmail FROM register_driver WHERE iDriverId = '".$iMemberId."'";
			$passengerData = $obj->MySQLSelect($sqlp);
			$currentLanguageCode = $passengerData[0]['vLang']; 
			$vPhoneCode_orig = $passengerData[0]['vCode'];
			$vPhone_orig = $passengerData[0]['vPhone'];
			$vEmail_orig = $passengerData[0]['vEmail'];
		}
		
		// $currentLanguageCode = ($obj->MySQLSelect("SELECT vLang FROM ".$tableName." WHERE".$where)[0]['vLang']);
		
		if($vEmail_userId_check != "" && $vEmail_userId_check != $iMemberId){
			$returnArr['Action']="0";
			$returnArr['message'] ="LBL_ALREADY_REGISTERED_TXT";
			echo json_encode($returnArr);exit;
		}
		if($vPhone_userId_check != "" && $vPhone_userId_check != $iMemberId){
			$returnArr['Action']="0";
			$returnArr['message'] ="LBL_MOBILE_EXIST";
			echo json_encode($returnArr);exit;
		}
		
		if($vPhone_orig != $vPhone || $vPhoneCode_orig != $phoneCode){
			$Data_update_User['ePhoneVerified'] = "No";
		}
		if($vEmail_orig != $vEmail){
			$Data_update_User['eEmailVerified'] = "No";
		}
		
		$Data_update_User['vName']=$vName;
		$Data_update_User['vLastName']=$vLastName;
		$Data_update_User['vPhone']=$vPhone;
		$Data_update_User['vCountry']=$vCountry;
		$Data_update_User['vLang']=$languageCode;
		if($vEmail != ""){
			$Data_update_User['vEmail']=$vEmail;
		}
		
		
		$id = $obj->MySQLQueryPerform($tableName,$Data_update_User,'update',$where);
		
		if($currentLanguageCode != $languageCode){
			$returnArr['changeLangCode'] ="Yes";
			$returnArr['UpdatedLanguageLabels'] = getLanguageLabelsArr($languageCode,"1");
			$returnArr['vLanguageCode'] = $languageCode;
			/*$returnArr['langType'] = get_value('language_master', 'eDirectionCode', 'vCode',$languageCode,'','true');
			$returnArr['vGMapLangCode'] = get_value('language_master', 'vGMapLangCode', 'vCode',$languageCode,'','true');*/
			$sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '".$languageCode."' ";
			$Data_checkLangCode = $obj->MySQLSelect($sql_LangCode);
			$returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
			$returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
      $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ";
  		$defLangValues = $obj->MySQLSelect($sql);
      $returnArr['LIST_LANGUAGES']=$defLangValues;
      for($i = 0;$i<count($defLangValues); $i++){
         if($defLangValues[$i]['eDefault'] == "Yes"){
            $returnArr['DefaultLanguageValues']=$defLangValues[$i];
         }
      }
      $sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ";
      $defCurrencyValues =  $obj->MySQLSelect($sql);
  		$returnArr['LIST_CURRENCY']=$defCurrencyValues;
      for($i = 0;$i<count($defCurrencyValues); $i++){
         if($defCurrencyValues[$i]['eDefault'] == "Yes"){
            $returnArr['DefaultCurrencyValues']=$defCurrencyValues[$i];
         }
      }
			}else{
			$returnArr['changeLangCode'] ="No";
		}
		if($userType != "Driver"){
			$returnArr['message'] = getPassengerDetailInfo($iMemberId,"");
			}else{
			$returnArr['message'] = getDriverDetailInfo($iMemberId);
		}
		if ($id >0) {
			$returnArr['Action']="1";
			} else {
			$returnArr['Action']="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		echo json_encode($returnArr);
		
	}
	
	###########################################################################
	
	if($type == "uploadImage"){
		global $generalobj,$tconfig;
		
		$iMemberId 	= isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
		$memberType 	= isset($_REQUEST['MemberType'])?clean($_REQUEST['MemberType']):'';
		$image_name = $vImage	= isset($_FILES['vImage']['name'])?$_FILES['vImage']['name']:'';
		$image_object	= isset($_FILES['vImage']['tmp_name'])?$_FILES['vImage']['tmp_name']:'';
		$image_name = "123.jpg";
		
		if($memberType == "Driver"){
			$Photo_Gallery_folder = $tconfig['tsite_upload_images_driver_path']."/".$iMemberId."/";
			}else{
			$Photo_Gallery_folder = $tconfig['tsite_upload_images_passenger_path']."/".$iMemberId."/";
		}
		
		// echo $Photo_Gallery_folder."===";
		if(!is_dir($Photo_Gallery_folder))
		mkdir($Photo_Gallery_folder, 0777);
		
		// echo $tconfig["tsite_upload_images_member_size1"];exit;
		
		$vImageName = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"], '', '', '', 'Y', '', $Photo_Gallery_folder);
		
		if($vImageName != ''){
			if($memberType == "Driver"){
				$where = " iDriverId = '".$iMemberId."'";
				$Data_passenger['vImage']=$vImageName;
				$id = $obj->MySQLQueryPerform("register_driver",$Data_passenger,'update',$where);
				}else{
				$where = " iUserId = '".$iMemberId."'";
				$Data_passenger['vImgName']=$vImageName;
				$id = $obj->MySQLQueryPerform("register_user",$Data_passenger,'update',$where);
			}
			
			
			if($id > 0){
				$returnArr['Action']="1";
				if($memberType == "Driver"){
					$returnArr['message'] = getDriverDetailInfo($iMemberId);
					}else{
					$returnArr['message'] = getPassengerDetailInfo($iMemberId,"");
				}
				
				
				}else{
				$returnArr['Action']="0";
				$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
			}
			
			}else{
			$returnArr['Action']="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		echo json_encode($returnArr);
		
	}
	
	####################### getRideHistory #############################
	if ($type == "getRideHistory") {
  		global $generalobj;
		
  		$page        = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
  		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
  		$eType = isset($_REQUEST["eType"]) ? $_REQUEST["eType"] : 'Ride';
  		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		
  		$vLanguage=get_value('register_user', 'vLang', 'iUserId',$iUserId,'','true');
  		if($vLanguage == "" || $vLanguage == NULL){
  			$vLanguage = "EN";
		}
		
  		$per_page=10;
  		$sql_all  = "SELECT COUNT(iTripId) As TotalIds FROM trips WHERE  iUserId='$iUserId' AND (iActive='Canceled' || iActive='Finished')";
  		$data_count_all = $obj->MySQLSelect($sql_all);
  		$TotalPages = ceil($data_count_all[0]['TotalIds'] / $per_page);
		
  		$start_limit = ($page - 1) * $per_page;
  		$limit       = " LIMIT " . $start_limit . ", " . $per_page;
		
  		//$sql = "SELECT tripRate.vRating1 as TripRating,tr.* FROM `trips` as tr,`ratings_user_driver` as tripRate  WHERE  tr.iUserId='$iUserId' AND tr.eType='$eType' AND tripRate.iTripId=tr.iTripId AND tripRate.eUserType='$UserType' AND (tr.iActive='Canceled' || tr.iActive='Finished') ORDER BY tr.iTripId DESC" . $limit;
		$sql = "SELECT tr.* FROM `trips` as tr WHERE tr.iUserId='$iUserId' AND (tr.iActive='Canceled' || tr.iActive='Finished') ORDER BY tr.iTripId DESC" . $limit;
  		$Data = $obj->MySQLSelect($sql);
  		$totalNum = count($Data);
		
  		$i=0;
  		if ( count($Data) > 0 ) {
			
			while ( count($Data)> $i ) {
				
				$returnArr = getTripPriceDetails($Data[$i]['iTripId'],$iUserId,"Passenger");
				
				$sql = "SELECT count(iRatingId) AS Total FROM `ratings_user_driver` WHERE iTripId = '".$Data[$i]['iTripId']."' and eUserType = '$UserType'";
				$rating_check = $obj->MySQLSelect($sql);
				$returnArr['is_rating'] = 'No';
				if($rating_check[0]['Total'] > 0) {
					$returnArr['is_rating'] = 'Yes';
				}
				
				$Data[$i] = array_merge($Data[$i], $returnArr);
				if($Data[$i]["eType"] == 'UberX' && $Data[$i]["eFareType"] != "Regular"){
					$Data[$i]['tDaddress'] = "";
				}
				$i++;
			}
			$returnData['message']=$Data;
			if ($TotalPages > $page) {
				$returnData['NextPage'] = "".($page + 1);
    			} else {
				$returnData['NextPage'] = "0";
			}
			$returnData['Action']="1";
			echo json_encode($returnData);
			
			}else{
  			$returnData['Action']="0";
  			$returnData['message']="LBL_NO_DATA_AVAIL";
  			echo json_encode($returnData);
		}
		
	}
	
	###########################################################################
	
	if($type == 'staticPage') {
		$iPageId = isset($_REQUEST['iPageId'])?clean($_REQUEST['iPageId']):'';
        $iMemberId = isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
        $vLangCode = isset($_REQUEST['vLangCode'])?clean($_REQUEST['vLangCode']):'';
        $appType = isset($_REQUEST['appType'])?clean($_REQUEST['appType']):''; // Passenger OR Driver
		
        $languageCode="";
        if($iMemberId != ""){
			if($appType == "Driver"){
				$languageCode = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId,'','true');
				}else{
				$languageCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId,'','true');
			}
			}else if($vLangCode != NULL && $vLangCode != ""){
			$check_lng = get_value('language_master', 'vTitle', 'vCode',$vLangCode,'','true');
			if($check_lng != NULL){
				$languageCode = $vLangCode;
			}
		}
		
		if($languageCode == ""){
			$languageCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
        $pageDesc = get_value('pages', 'tPageDesc_'.$languageCode, 'iPageId', $iPageId,'','true');
        // $meta['page_desc']=strip_tags($pageDesc);
        $meta['page_desc']=$pageDesc;
        echo json_encode($meta,JSON_UNESCAPED_UNICODE);
	}
	
	###########################################################################
	
	if($type=='sendContactQuery'){
		
		$UserType          = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : '';
		$UserId          = isset($_REQUEST["UserId"]) ? $_REQUEST["UserId"] : '';
		$message          = isset($_REQUEST["message"]) ? $_REQUEST["message"] : '';
		$subject          = isset($_REQUEST["subject"]) ? $_REQUEST["subject"] : '';
		
		if($UserType=='Passenger'){
			$sql="SELECT vName,vLastName,vPhone,vEmail FROM register_user WHERE iUserId=$UserId";
			
			$result_data = $obj->MySQLSelect($sql);
			
			}else if($UserType=='Driver'){
			$sql="SELECT vName,vLastName,vPhone,vEmail FROM register_driver WHERE iDriverId=$UserId";
			
			$result_data = $obj->MySQLSelect($sql);
			
		}
		
    if($UserId != ""){
		$Data['vFirstName'] = $result_data[0]['vName'];
		$Data['vLastName'] = $result_data[0]['vLastName'];
		$Data['vEmail'] = $result_data[0]['vEmail'];
		$Data['cellno'] = $result_data[0]['vPhone'];
    }else{
    $Data['vFirstName'] = "App User";
		$Data['vLastName'] = "";
		$Data['vEmail'] = "-";
		$Data['cellno'] = "-"; 
    }
    $Data['eSubject'] =  $subject;
		$Data['tSubject'] =  $message;
		$id= $generalobj->send_email_user("CONTACTUS",$Data);
		
		if($id>0){
			$returnArr['Action']="1";
			$returnArr['message'] ="LBL_SENT_CONTACT_QUERY_SUCCESS_TXT";
			}else{
			$returnArr['Action']="0";
			$returnArr['message'] ="LBL_FAILED_SEND_CONTACT_QUERY_TXT";
		}
		echo json_encode($returnArr);
	}
	
	############################# GetFAQ ######################################
	if ($type == "getFAQ") {
		$status = "Active";
		
        $iMemberId = isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
        $appType = isset($_REQUEST['appType'])?clean($_REQUEST['appType']):'';
		
        $languageCode="";
        if($appType == "Driver"){
            $languageCode =  get_value('register_driver', 'vLang', 'iDriverId',$iMemberId,'','true');
			}else{
			$languageCode =  get_value('register_user', 'vLang', 'iUserId',$iMemberId,'','true');
		}
		
		if($languageCode == ""){
			$languageCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		
        $sql = "SELECT * FROM `faq_categories` WHERE eStatus='$status' AND vCode='".$languageCode."' ORDER BY iDisplayOrder ASC ";
        $Data = $obj->MySQLSelect($sql);
		
        $i=0;
        if (count($Data) > 0) {
            $row = $Data;
            while (count($row)> $i) {
                $rows_questions = array();
                $iUniqueId = $row[$i]['iUniqueId'];
				
                $sql = "SELECT vTitle_" .$languageCode. " as vTitle,tAnswer_" .$languageCode. " as tAnswer FROM `faqs` WHERE iFaqcategoryId='".$iUniqueId."'";
                $row_questions = $obj->MySQLSelect($sql);
				
                $j=0;
                while (count($row_questions) > $j ) {
                    $rows_questions[$j] = $row_questions[$j];
                    $j++;
				}
                $row[$i]['Questions'] = $rows_questions;
                $i++;
			}
			
			$returnData['Action']="1";
			$returnData['message']=$row;
			}else{
			$returnData['Action']="0";
			$returnData['message']="LBL_FAQ_NOT_AVAIL";
		}
		
        echo json_encode($returnData);
	}
	###########################################################################
	
	if($type == 'getReceipt')
	{
        $iTripId = isset($_REQUEST['iTripId'])?clean($_REQUEST['iTripId']):'0';
        $UserType = isset($_REQUEST['UserType'])?clean($_REQUEST['UserType']):''; //Passenger OR Driver
		
		$value = sendTripReceipt($iTripId);
		
		if($value == true || $value == "true" || $value == "1"){
			$returnArr['Action'] = "1";
			$returnArr['message']="LBL_CHECK_INBOX_TXT";
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_FAILED_SEND_RECEIPT_EMAIL_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
		
	}
	
	###########################################################################
	
	if ($type == "cancelCabRequest") {
		$iUserId          = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$iCabRequestId          = isset($_REQUEST["iCabRequestId"]) ? $_REQUEST["iCabRequestId"] : '';
		
		if($iCabRequestId == ""){
			// $data = get_value('cab_request_now', 'max(iCabRequestId),eStatus', 'iUserId',$iUserId);
			$sql = "SELECT iCabRequestId, eStatus FROM cab_request_now WHERE iUserId='".$iUserId."' ORDER BY iCabRequestId DESC LIMIT 1 ";
			$data = $obj->MySQLSelect($sql);
			$iCabRequestId = $data[0]['iCabRequestId'];
			$eStatus = $data[0]['eStatus'];
			}else{
			$data = get_value('cab_request_now', 'eStatus', 'iCabRequestId',$iCabRequestId,'','true');
			$eStatus = $data[0]['eStatus'];
		}
		if($eStatus == "Requesting"){
			$where = " iCabRequestId='$iCabRequestId'";
			$Data_update_cab_request['eStatus']="Cancelled";
			
			$id = $obj->MySQLQueryPerform("cab_request_now",$Data_update_cab_request,'update',$where);
			
			
			if ($id) {
				$returnArr['Action'] = "1";
				$returnArr['message'] = "DO_RESET";
				}else{
				$returnArr['Action'] = "0";
				$returnArr['message'] = "LBL_REQUEST_CANCEL_FAILED_TXT";
			}
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] = "DO_RESTART";
		}
		
		echo json_encode($returnArr);
		
	}
	
	###########################################################################
	
	if ($type == "sendRequestToDrivers") {
		
		$driver_id_auto =isset($_REQUEST["driverIds"]) ? $_REQUEST["driverIds"] : '';
		$message        = isset($_REQUEST["message"]) ? $_REQUEST["message"] : '';
		$passengerId    = isset($_REQUEST["userId"]) ? $_REQUEST["userId"] : '';
		$cashPayment    =isset($_REQUEST["CashPayment"]) ? $_REQUEST["CashPayment"] : '';
		$selectedCarTypeID    = isset($_REQUEST["SelectedCarTypeID"]) ? $_REQUEST["SelectedCarTypeID"] : '';
		
		$eFemaleDriverRequest    = isset($_REQUEST["eFemaleDriverRequest"]) ? $_REQUEST["eFemaleDriverRequest"] : 'No';
		$eHandiCapAccessibility    = isset($_REQUEST["eHandiCapAccessibility"]) ? $_REQUEST["eHandiCapAccessibility"] : 'No';
		$PickUpLatitude    = isset($_REQUEST["PickUpLatitude"]) ? $_REQUEST["PickUpLatitude"] : '0.0';
		$PickUpLongitude    =isset($_REQUEST["PickUpLongitude"]) ? $_REQUEST["PickUpLongitude"] : '0.0';
		$PickUpAddress    =isset($_REQUEST["PickUpAddress"]) ? $_REQUEST["PickUpAddress"] : '';
		
		$DestLatitude    = isset($_REQUEST["DestLatitude"]) ? $_REQUEST["DestLatitude"] : '';
		$DestLongitude    =isset($_REQUEST["DestLongitude"]) ? $_REQUEST["DestLongitude"] : '';
		$DestAddress    =isset($_REQUEST["DestAddress"]) ? $_REQUEST["DestAddress"] : '';
		$promoCode    =isset($_REQUEST["PromoCode"]) ? $_REQUEST["PromoCode"] : '';
		$eType    =isset($_REQUEST["eType"]) ? $_REQUEST["eType"] : '';
		$iPackageTypeId    =isset($_REQUEST["iPackageTypeId"]) ? $_REQUEST["iPackageTypeId"] : '0';
		$vReceiverName    =isset($_REQUEST["vReceiverName"]) ? $_REQUEST["vReceiverName"] : '';
		$vReceiverMobile    =isset($_REQUEST["vReceiverMobile"]) ? $_REQUEST["vReceiverMobile"] : '';      
		$tPickUpIns    =isset($_REQUEST["tPickUpIns"]) ? $_REQUEST["tPickUpIns"] : '';
		$tDeliveryIns    =isset($_REQUEST["tDeliveryIns"]) ? $_REQUEST["tDeliveryIns"] : '';
		$tPackageDetails    =isset($_REQUEST["tPackageDetails"]) ? $_REQUEST["tPackageDetails"] : '';
		$vDeviceToken    =isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
		$iUserPetId    =isset($_REQUEST["iUserPetId"]) ? $_REQUEST["iUserPetId"] : '0';
		$quantity    =isset($_REQUEST["Quantity"]) ? $_REQUEST["Quantity"] : '1';
		$PickUpAddress = isset($_REQUEST["PickUpAddress"]) ? $_REQUEST["PickUpAddress"] : '';
		$fTollPrice = isset($_REQUEST["fTollPrice"]) ? $_REQUEST["fTollPrice"] : '';
		$vTollPriceCurrencyCode = isset($_REQUEST["vTollPriceCurrencyCode"]) ? $_REQUEST["vTollPriceCurrencyCode"] : '';
		$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		$eTollSkipped = isset($_REQUEST["eTollSkipped"]) ? $_REQUEST["eTollSkipped"] : 'Yes';
		$iUserAddressId = isset($_REQUEST["iUserAddressId"]) ? $_REQUEST["iUserAddressId"] : '0';
		$tUserComment = isset($_REQUEST["tUserComment"]) ? $_REQUEST["tUserComment"] : '';        
		$trip_status  = "Requesting";
      
		/*$iCabRequestId_cab_now= get_value('cab_request_now', 'max(iCabRequestId)', 'iUserId',$passengerId,'','true');
		$eStatus_cab_now= get_value('cab_request_now', 'eStatus', 'iCabRequestId',$iCabRequestId_cab_now,'','true');*/
		$sql_cabrequest = "SELECT iCabRequestId,eStatus FROM `cab_request_now` WHERE iUserId='".$passengerId."' ORDER BY iCabRequestId DESC LIMIT 0,1";
		$Data_cabrequest = $obj->MySQLSelect($sql_cabrequest);
		$iCabRequestId_cab_now = $Data_cabrequest[0]['iCabRequestId'];
		$eStatus_cab_now = $Data_cabrequest[0]['eStatus'];
		if($eStatus_cab_now == "Requesting"){
			$where_cab_now = " iCabRequestId = '$iCabRequestId_cab_now' ";
			$Data_update_cab_now['eStatus']="Cancelled";
			
			$obj->MySQLQueryPerform("cab_request_now",$Data_update_cab_now,'update',$where_cab_now);
		}
		
		checkmemberemailphoneverification($passengerId,"Passenger");
		## check pickup addresss for UberX #
		//$APP_TYPE = $generalobj->getConfigurations("configurations","APP_TYPE");
		if($APP_TYPE == "UberX"){
			$Data_update_passenger['tUserComment']=$tUserComment; 
			//$PickUpAddress=get_value('user_address', 'vServiceAddress', '	iUserAddressId',$iUserAddressId,'','true');
  			if($iUserAddressId != ""){
  				$Address=get_value('user_address', 'vAddressType,vBuildingNo,vLandmark,vServiceAddress,vLatitude,vLongitude', '	iUserAddressId',$iUserAddressId,'','');
  				$vAddressType = $Address[0]['vAddressType'];
  				$vBuildingNo = $Address[0]['vBuildingNo'];
  				$vLandmark = $Address[0]['vLandmark'];
  				$vServiceAddress = $Address[0]['vServiceAddress']; 
  				$PickUpAddress = ($vAddressType != "")? $vAddressType."\n" :"";
  				$PickUpAddress .= ($vBuildingNo != "")? $vBuildingNo."," :"";
  				$PickUpAddress .= ($vLandmark != "")? $vLandmark."\n" :"";
  				$PickUpAddress .= ($vServiceAddress != "")? $vServiceAddress :"";  
  				$Data_update_passenger['tSourceAddress']=$PickUpAddress;
  				$Data_update_passenger['iUserAddressId']=$iUserAddressId;
          $PickUpLatitude = $Address[0]['vLatitude'];
          $PickUpLongitude = $Address[0]['vLongitude'];     
  			}else{
  				$Data_update_passenger['tSourceAddress']=$PickUpAddress;
  			}       
		}else{
			$Data_update_passenger['tSourceAddress']=$PickUpAddress;
		}    
		## check pickup addresss for UberX #    
		
    ### Checking For Pickup And DropOff Disallow ###
    $pickuplocationarr = array($PickUpLatitude,$PickUpLongitude);
    $allowed_ans_pickup = checkAllowedAreaNew($pickuplocationarr,"No");
    if($allowed_ans_pickup == "No"){
			$returnArr['Action'] = "0";
  		$returnArr['message'] = "LBL_PICKUP_LOCATION_NOT_ALLOW";
  		echo json_encode($returnArr);
  		exit;
		}            
    if($DestLatitude != "" && $DestLongitude !=""){
    $dropofflocationarr = array($DestLatitude,$DestLongitude);
    $allowed_ans_dropoff = checkAllowedAreaNew($dropofflocationarr,"Yes");
    if($allowed_ans_dropoff == "No"){
			$returnArr['Action'] = "0";
  		$returnArr['message'] = "LBL_DROP_LOCATION_NOT_ALLOW";
  		echo json_encode($returnArr);
  		exit;
  		}
		}
    ### Checking For Pickup And DropOff Disallow ###       
		
		$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		
		$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
		$userwaitinglabel = $languageLabelsArr['LBL_TRIP_USER_WAITING'];
		$alertMsg = $userwaitinglabel;
		
		$address_data['PickUpAddress'] = $PickUpAddress;
		$address_data['DropOffAddress'] = $DestAddress;                                                  
		
		$DataArr = getOnlineDriverArr($PickUpLatitude,$PickUpLongitude,$address_data,"Yes","No","No","",$DestLatitude,$DestLongitude);
		$Data = $DataArr['DriverList'];
		if($DataArr['PickUpDisAllowed'] == "No" && $DataArr['DropOffDisAllowed'] == "No"){
			$returnArr['Action'] = "0";
  			$returnArr['message'] = "LBL_PICK_DROP_LOCATION_NOT_ALLOW";
  			echo json_encode($returnArr);
  			exit;
		}
		if($DataArr['PickUpDisAllowed'] == "Yes" && $DataArr['DropOffDisAllowed'] == "No"){
			$returnArr['Action'] = "0";
  			$returnArr['message'] = "LBL_DROP_LOCATION_NOT_ALLOW";
  			echo json_encode($returnArr);
  			exit;
		}
		if($DataArr['PickUpDisAllowed'] == "No" && $DataArr['DropOffDisAllowed'] == "Yes"){
			$returnArr['Action'] = "0";
  			$returnArr['message'] = "LBL_PICKUP_LOCATION_NOT_ALLOW";
  			echo json_encode($returnArr);
  			exit;
		}
		
		$sqlp = "SELECT iGcmRegId,vName,vLastName,vImgName,vFbId,vAvgRating,vPhone,vPhoneCode FROM register_user WHERE iUserId = '".$passengerId."'";
    $passengerData = $obj->MySQLSelect($sqlp);
    //$iGcmRegId=get_value('register_user', 'iGcmRegId', 'iUserId',$passengerId,'','true');
    $iGcmRegId = $passengerData[0]['iGcmRegId']; 
		
		if($vDeviceToken != "" && $vDeviceToken != $iGcmRegId){
			$returnArr['Action'] = "0";
			$returnArr['message'] = "SESSION_OUT";
			echo json_encode($returnArr);
			exit;
		}
		
		$final_message['Message'] = "CabRequested";
		$final_message['sourceLatitude'] = strval($PickUpLatitude);
		$final_message['sourceLongitude'] = strval($PickUpLongitude);
		$final_message['PassengerId'] = strval($passengerId);
		/*$passengerFName = get_value('register_user', 'vName', 'iUserId',$passengerId,'','true');
		$passengerLName = get_value('register_user', 'vLastName', 'iUserId',$passengerId,'','true');
		$final_message['PName'] = $passengerFName. " " .$passengerLName;
		$final_message['PPicName'] = get_value('register_user', 'vImgName', 'iUserId',$passengerId,'','true');
		$final_message['PFId'] = get_value('register_user', 'vFbId', 'iUserId',$passengerId,'','true');
		$final_message['PRating'] = get_value('register_user', 'vAvgRating', 'iUserId',$passengerId,'','true');
		$final_message['PPhone'] = get_value('register_user', 'vPhone', 'iUserId',$passengerId,'','true');
		$final_message['PPhoneC'] = get_value('register_user', 'vPhoneCode', 'iUserId',$passengerId,'','true'); */
    $passengerFName = $passengerData[0]['vName'];
    $passengerLName = $passengerData[0]['vLastName'];
    $final_message['PName'] = $passengerFName. " " .$passengerLName;
    $final_message['PPicName'] = $passengerData[0]['vImgName'];
    $final_message['PFId'] = $passengerData[0]['vFbId'];
    $final_message['PRating'] = $passengerData[0]['vAvgRating'];
    $final_message['PPhone'] = $passengerData[0]['vPhone'];
    $final_message['PPhoneC'] = $passengerData[0]['vPhoneCode'];
		$final_message['PPhone'] = '+'.$final_message['PPhoneC'].$final_message['PPhone'];
		$final_message['REQUEST_TYPE'] = $eType;
		$final_message['PACKAGE_TYPE'] = $eType == "Deliver"?get_value('package_type', 'vName', 'iPackageTypeId',$iPackageTypeId,'','true'):'';
		$final_message['destLatitude'] = strval($DestLatitude);
		$final_message['destLongitude'] = strval($DestLongitude);
		$final_message['MsgCode'] = strval(time().mt_rand(1000, 9999));
		$final_message['vTitle'] = $alertMsg;
		$final_message['iTripId'] = $iCabRequestId_cab_now;
		//$final_message['Time']= strval(date('Y-m-d'));
		if($eType == "UberX"){
			/*$iVehicleCategoryId=get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId',$selectedCarTypeID,'','true');
			$vVehicleTypeName=get_value('vehicle_type', 'vVehicleType_'.$vLangCode, 'iVehicleTypeId',$selectedCarTypeID,'','true');
      $eFareType=get_value('vehicle_type', 'eFareType', 'iVehicleTypeId',$selectedCarTypeID,'','true');*/
      $sqlv = "SELECT iVehicleCategoryId,vVehicleType_".$vLangCode." as vVehicleTypeName,eFareType,ePickStatus,eNightStatus from vehicle_type WHERE iVehicleTypeId = '".$selectedCarTypeID."'";
		  $tripVehicleData = $obj->MySQLSelect($sqlv);
      $iVehicleCategoryId = $tripVehicleData[0]['iVehicleCategoryId'];
      $vVehicleTypeName = $tripVehicleData[0]['vVehicleTypeName'];
      $eFareType = $tripVehicleData[0]['eFareType']; 
			if($iVehicleCategoryId != 0){
				$vVehicleCategoryName = get_value('vehicle_category', 'vCategory_'.$vLangCode, 'iVehicleCategoryId',$iVehicleCategoryId,'','true');
				$vVehicleTypeName = $vVehicleCategoryName . "-" . $vVehicleTypeName;
			}
			$final_message['SelectedTypeName']	= $vVehicleTypeName;
			$final_message['eFareType']	= $eFareType;      
			}else{
			$final_message['SelectedTypeName'] = "";
			$final_message['eFareType'] = "";      
		}
		
		
		/*$ePickStatus=get_value('vehicle_type', 'ePickStatus', 'iVehicleTypeId',$selectedCarTypeID,'','true');
		$eNightStatus=get_value('vehicle_type', 'eNightStatus', 'iVehicleTypeId',$selectedCarTypeID,'','true');*/
    $ePickStatus = $tripVehicleData[0]['ePickStatus'];
    $eNightStatus = $tripVehicleData[0]['eNightStatus'];
		
		$fPickUpPrice = 1;
		$fNightPrice = 1;
    $sourceLocationArr = array($PickUpLatitude,$PickUpLongitude);
		$destinationLocationArr = array($DestLatitude,$DestLongitude);
		
    $data_flattrip['eFlatTrip'] = "No";
    $data_flattrip['Flatfare'] = 0;
		$data_surgePrice = checkSurgePrice($selectedCarTypeID,"");
		
		if($data_surgePrice['Action'] == "0"){
			if($data_surgePrice['message'] == "LBL_PICK_SURGE_NOTE"){
				$fPickUpPrice=$data_surgePrice['SurgePriceValue'];
				}else{
				$fNightPrice=$data_surgePrice['SurgePriceValue'];
			}
		}
    if($APPLY_SURGE_ON_FLAT_FARE == "No" && $data_flattrip['eFlatTrip'] == "Yes"){
      $fPickUpPrice = 1;
		  $fNightPrice = 1;
    }
		$cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + 60) / 60);
		$str_date = @date('Y-m-d H:i:s', strtotime('-'.$cmpMinutes.' minutes'));
        $sql = "SELECT iGcmRegId,eDeviceType,iDriverId,vLang,tSessionId,iAppVersion FROM register_driver WHERE iDriverId IN (".$driver_id_auto.") AND tLocationUpdateDate > '$str_date' AND vAvailability='Available'";
        $result = $obj->MySQLSelect($sql);
		
		// echo "Res:count:".count($result);exit;
        if(count($result) == 0 || $driver_id_auto == "" || count($Data) == 0){
			$returnArr['Action'] = "0";
			$returnArr['message'] = "NO_CARS";
            echo json_encode($returnArr);
            exit;
		}
		
		if($cashPayment=='true'){
			$tripPaymentMode="Cash";
			}else{
			$tripPaymentMode="Card";
		}
		
		// $where = " iUserId = '$passengerId'";
		$where = "";
		
		// $Data_update_passenger['eStatus']=$trip_status;
		$Data_update_passenger['ePayType']=$tripPaymentMode;
		
		// if(($generalobj->getConfigurations("configurations","PAYMENT_ENABLED")) == 'Yes'){
		// $Data_update_passenger['vTripPaymentMode']=$tripPaymentMode;
		// }else{
		// $Data_update_passenger['vTripPaymentMode']="Cash";
		// }
		
		if($eTollSkipped=='No' || $fTollPrice != "")
		{
			$fTollPrice_Original = $fTollPrice;
			$vTollPriceCurrencyCode = strtoupper($vTollPriceCurrencyCode);
			$default_currency = get_value('currency', 'vName', 'eDefault', 'Yes','','true');
			$sql=" SELECT round(($fTollPrice/(SELECT Ratio FROM currency where vName='".$vTollPriceCurrencyCode."'))*(SELECT Ratio FROM currency where vName='".$default_currency."' ) ,2)  as price FROM currency  limit 1";
			$result_toll = $obj->MySQLSelect($sql);
			$fTollPrice = $result_toll[0]['price'];
			if($fTollPrice == 0){
				$fTollPrice = get_currency($vTollPriceCurrencyCode,$default_currency,$fTollPrice_Original);
			}
			$Data_update_passenger['fTollPrice']=$fTollPrice;
			$Data_update_passenger['vTollPriceCurrencyCode']=$vTollPriceCurrencyCode;
			$Data_update_passenger['eTollSkipped']=$eTollSkipped;
			}else{
			$Data_update_passenger['fTollPrice']="0";
			$Data_update_passenger['vTollPriceCurrencyCode']="";
			$Data_update_passenger['eTollSkipped']="No";
		}
		
		$Data_update_passenger['iUserId']=$passengerId;
		$Data_update_passenger['tMsgCode']=$final_message['MsgCode'];
		$Data_update_passenger['eStatus']='Requesting';
		$Data_update_passenger['vSourceLatitude']=$PickUpLatitude;
		$Data_update_passenger['vSourceLongitude']=$PickUpLongitude;
		
		$Data_update_passenger['vDestLatitude']=$DestLatitude;
		$Data_update_passenger['vDestLongitude']=$DestLongitude;
		$Data_update_passenger['tDestAddress']=$DestAddress;
		$Data_update_passenger['iVehicleTypeId']=$selectedCarTypeID;
		$Data_update_passenger['fPickUpPrice']=$fPickUpPrice;
		$Data_update_passenger['fNightPrice']=$fNightPrice;
		$Data_update_passenger['eType']=$eType;
		$Data_update_passenger['iPackageTypeId']= $eType == "Deliver"?$iPackageTypeId:'0';
		$Data_update_passenger['vReceiverName']=$eType == "Deliver"?$vReceiverName:'';
		$Data_update_passenger['vReceiverMobile']=$eType == "Deliver"?$vReceiverMobile:'';
		$Data_update_passenger['tPickUpIns']=$eType == "Deliver"?$tPickUpIns:'';
		$Data_update_passenger['tDeliveryIns']=$eType == "Deliver"?$tDeliveryIns:'';
		$Data_update_passenger['tPackageDetails']=$eType == "Deliver"?$tPackageDetails:'';
		$Data_update_passenger['vCouponCode']=$promoCode;
		$Data_update_passenger['iQty']=$quantity;
		$Data_update_passenger['vRideCountry']=$vCountryCode;
		$Data_update_passenger['eFemaleDriverRequest']=$eFemaleDriverRequest;
		$Data_update_passenger['eHandiCapAccessibility']=$eHandiCapAccessibility;
		$Data_update_passenger['vTimeZone']=$vTimeZone;
		$Data_update_passenger['dAddedDate']=date("Y-m-d H:i:s");
    $Data_update_passenger['eFlatTrip']=$data_flattrip["eFlatTrip"];
		$Data_update_passenger['fFlatTripPrice']=$data_flattrip["Flatfare"];
		
		$insert_id = $obj->MySQLQueryPerform("cab_request_now",$Data_update_passenger,'insert');
		// $insert_id = mysql_insert_id();
		
		$final_message['iCabRequestId'] = $insert_id;
		//$msg_encode  = json_encode($final_message,JSON_UNESCAPED_UNICODE);
		/*$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");
			$PUBNUB_DISABLED = $generalobj->getConfigurations("configurations","PUBNUB_DISABLED");
			$PUBNUB_PUBLISH_KEY = $generalobj->getConfigurations("configurations","PUBNUB_PUBLISH_KEY");
		$PUBNUB_SUBSCRIBE_KEY = $generalobj->getConfigurations("configurations","PUBNUB_SUBSCRIBE_KEY");*/
		if($PUBNUB_DISABLED == "Yes"){
			$ENABLE_PUBNUB = "No";
		}
		$alertSendAllowed = true;
		
		if($ENABLE_PUBNUB == "Yes" && $PUBNUB_PUBLISH_KEY != "" && $PUBNUB_SUBSCRIBE_KEY != ""){
			
			//$pubnub = new Pubnub\Pubnub($PUBNUB_PUBLISH_KEY, $PUBNUB_SUBSCRIBE_KEY);
			$pubnub = new Pubnub\Pubnub(array("publish_key" => $PUBNUB_PUBLISH_KEY,"subscribe_key" => $PUBNUB_SUBSCRIBE_KEY, "uuid" => $uuid));
			$filter_driver_ids = str_replace(' ', '', $driver_id_auto);
			$driverIds_arr = explode(",",$filter_driver_ids);
			
			$message= stripslashes(preg_replace("/[\n\r]/","",$message));
			
			$deviceTokens_arr_ios = array();
			$registation_ids_new = array();
			
			$sourceLoc = $PickUpLatitude.','.$PickUpLongitude;
			$destLoc = $DestLatitude.','.$DestLongitude;
			for($i=0;$i<count($driverIds_arr); $i++){
        /*
				// Add User Request
				$data_userRequest = array();
				$data_userRequest['iUserId'] = $passengerId;
				$data_userRequest['iDriverId'] = $driverIds_arr[$i];
				$data_userRequest['tMessage'] = $msg_encode;
				$data_userRequest['iMsgCode'] = $final_message['MsgCode'];
				$data_userRequest['dAddedDate'] = @date("Y-m-d H:i:s");
				$requestId = addToUserRequest2($data_userRequest);
				
				// Add Driver Request
				$data_driverRequest = array();
				$data_driverRequest['iDriverId'] = $driverIds_arr[$i];
				$data_driverRequest['iRequestId'] = $requestId;
				$data_driverRequest['iUserId'] = $passengerId;
				$data_driverRequest['iTripId'] = 0;
				$data_driverRequest['vMsgCode'] = $final_message['MsgCode'];
				$data_driverRequest['eStatus'] = "Timeout";
				$data_driverRequest['vStartLatlong'] = $sourceLoc;
				$data_driverRequest['vEndLatlong'] = $destLoc;
				$data_driverRequest['tStartAddress'] = $PickUpAddress;
				$data_driverRequest['tEndAddress'] = $DestAddress ;
				$data_driverRequest['tDate'] = @date("Y-m-d H:i:s");
				addToDriverRequest2($data_driverRequest);   */
				
				/* For PubNub Setting */
				/*$iAppVersion=get_value("register_driver", 'iAppVersion', "iDriverId",$driverIds_arr[$i],'','true');
				$eDeviceType=get_value("register_driver", 'eDeviceType', "iDriverId",$driverIds_arr[$i],'','true');
				$vDeviceToken=get_value("register_driver", 'iGcmRegId', "iDriverId",$driverIds_arr[$i],'','true');
        $tSessionId=get_value("register_driver", 'tSessionId', "iDriverId",$driverIds_arr[$i],'','true');  */
        $sqld = "SELECT iAppVersion,eDeviceType,iGcmRegId,tSessionId,vLang FROM register_driver WHERE iDriverId = '".$driverIds_arr[$i]."'";
        $driverTripData = $obj->MySQLSelect($sqld);
        $iAppVersion = $driverTripData[0]['iAppVersion'];
        $eDeviceType = $driverTripData[0]['eDeviceType'];
        $vDeviceToken = $driverTripData[0]['iGcmRegId'];
        $tSessionId = $driverTripData[0]['tSessionId'];
        $vLang = $driverTripData[0]['vLang']; 
				/* For PubNub Setting Finished */
				
				$final_message['tSessionId'] = $tSessionId;
        $alertMsg_db = get_value('language_label', 'vValue', 'vLabel', 'LBL_TRIP_USER_WAITING'," and vCode='".$vLang."'",'true');
        $final_message['vTitle'] = $alertMsg_db; 
				$msg_encode_pub = json_encode($final_message,JSON_UNESCAPED_UNICODE);
				$channelName = "CAB_REQUEST_DRIVER_".$driverIds_arr[$i];
				// $info = $pubnub->publish($channelName, $message);
				$info = $pubnub->publish($channelName, $msg_encode_pub );
				
				if($eDeviceType != "Android"){
					array_push($deviceTokens_arr_ios, $vDeviceToken);
				}
				
			}
			
		}
		
		if($alertSendAllowed == true){
			$deviceTokens_arr_ios = array();
			$registation_ids_new = array();
			
			foreach ($result as $item) {
				if($item['eDeviceType'] == "Android"){
					array_push($registation_ids_new, $item['iGcmRegId']);
					}else{
					array_push($deviceTokens_arr_ios, $item['iGcmRegId']);
				}
        
        $alertMsg_db = get_value('language_label', 'vValue', 'vLabel', 'LBL_TRIP_USER_WAITING'," and vCode='".$item['vLang']."'",'true');
				$tSessionId= $item['tSessionId'];

				$final_message['tSessionId'] = $tSessionId;
				$final_message['vTitle'] = $alertMsg_db;
        $msg_encode  = json_encode($final_message,JSON_UNESCAPED_UNICODE);
				// Add User Request
				$data_userRequest = array();
				$data_userRequest['iUserId'] = $passengerId;
				$data_userRequest['iDriverId'] = $item['iDriverId'];
				$data_userRequest['tMessage'] = $msg_encode;
				$data_userRequest['iMsgCode'] = $final_message['MsgCode'];
				$data_userRequest['dAddedDate'] = @date("Y-m-d H:i:s");
				$requestId = addToUserRequest2($data_userRequest);
				
				// Add Driver Request
				$data_driverRequest = array();
				$data_driverRequest['iDriverId'] = $item['iDriverId'];
				$data_driverRequest['iRequestId'] = $requestId;
				$data_driverRequest['iUserId'] = $passengerId;
				$data_driverRequest['iTripId'] = 0;
				$data_driverRequest['eStatus'] = "Timeout";
				$data_driverRequest['vMsgCode'] = $final_message['MsgCode'];
				$data_driverRequest['vStartLatlong'] = $sourceLoc;
				$data_driverRequest['vEndLatlong'] = $destLoc;
				$data_driverRequest['tStartAddress'] = $PickUpAddress;
				$data_driverRequest['tEndAddress'] = $DestAddress ;
				$data_driverRequest['tDate'] = @date("Y-m-d H:i:s");
				addToDriverRequest2($data_driverRequest);
				// addToUserRequest($passengerId,$item['iDriverId'],$msg_encode,$final_message['MsgCode']);
				// addToDriverRequest($item['iDriverId'],$passengerId,0,"Timeout");
			}
			if(count($registation_ids_new) > 0){
				// $Rmessage = array("message" => $message);
				$Rmessage = array("message" => $msg_encode);
				
				$result = send_notification($registation_ids_new, $Rmessage,0);
				
			}
			if(count($deviceTokens_arr_ios) > 0){
				// sendApplePushNotification(1,$deviceTokens_arr_ios,$message,$alertMsg,1);
				sendApplePushNotification(1,$deviceTokens_arr_ios,$msg_encode,$alertMsg,0);
			}
		}
		
		
		
		$returnArr['Action'] = "1";
        echo json_encode($returnArr);
	}
	
	###########################################################################
	
	if ($type == "cancelTrip") {
		
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$iUserId   = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$iTripId   = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '0';
		$userType   = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$driverComment   = isset($_REQUEST["Comment"]) ? $_REQUEST["Comment"] : '';
		$driverReason   = isset($_REQUEST["Reason"]) ? $_REQUEST["Reason"] : '';
		
		
		if($userType != "Driver"){
            $vTripStatus = get_value('register_user', 'vTripStatus', 'iUserId',$iUserId,'','true');
			
            if($vTripStatus != "Cancelled" && $vTripStatus != "Active" && $vTripStatus != "Arrived"){
				
				$returnArr['Action'] = "0";
				$returnArr['message'] = "DO_RESTART";
				echo json_encode($returnArr);
				exit;
			}
		}
		$tripCancelData = get_value('trips AS tr LEFT JOIN vehicle_type AS vt ON vt.iVehicleTypeId=tr.iVehicleTypeId', 'tr.vCouponCode,tr.vTripPaymentMode,tr.iUserId,tr.iFare,tr.vRideNo,tr.tTripRequestDate,vt.fCancellationFare,vt.iCancellationTimeLimit', 'iTripId',$iTripId);
		
		$currentDate =@date("Y-m-d H:i:s");
		$tTripRequestDate = $tripCancelData[0]['tTripRequestDate'];
		$fCancellationFare = 0;
		$eCancelChargeFailed = "No";
		$totalMinute=@round(abs(strtotime($currentDate) - strtotime($tTripRequestDate)) / 60,2);
		if($totalMinute >= $tripCancelData[0]['iCancellationTimeLimit'] && $userType != "Driver"){
			$fCancellationFare = $tripCancelData[0]['fCancellationFare'];
			$vTripPaymentMode = $tripCancelData[0]['vTripPaymentMode'];
			if($vTripPaymentMode == "Card" && $fCancellationFare > 0){
				$vStripeCusId = get_value('register_user', 'vStripeCusId', 'iUserId', $tripCancelData[0]['iUserId'],'','true');
				$currency = get_value('currency', 'vName', 'eDefault', 'Yes','','true');
				$price_new = $fCancellationFare * 100;
				$description = "Payment received for cancelled trip number:".$tripCancelData[0]['vRideNo'];
				try{
					if($fCancellationFare > 0){
						$charge_create = Stripe_Charge::create(array(
						"amount" => $price_new,
						"currency" => $currency,
						"customer" => $vStripeCusId,
						"description" =>  $description
						));
						$details = json_decode($charge_create);
						$result = get_object_vars($details);
						if($fCancellationFare == 0 || ($result['status']=="succeeded" && $result['paid']=="1")){
							$pay_data['tPaymentUserID']=$result['id'];
							$pay_data['vPaymentUserStatus']="approved";
							$pay_data['iTripId']=$iTripId;
							$pay_data['iAmountUser']=$fCancellationFare;
							$obj->MySQLQueryPerform("payments",$pay_data,'insert');
							}else{
							$eCancelChargeFailed ='Yes';
						}
					}
					}catch(Exception $e){
					$error3 = $e->getMessage();
					$eCancelChargeFailed ='Yes';
				}
			}
		}
		$active_status  = "Canceled";
		if($userType != "Driver"){
			$message  = "TripCancelled";
			}else{
			$message  = "TripCancelledByDriver";
		}
		
		$couponCode=$tripCancelData[0]['vCouponCode'];
		
		if($couponCode != ''){
			$noOfCouponUsed = get_value('coupon', 'iUsed', 'vCouponCode', $couponCode,'','true');
			
			$where = " vCouponCode = '".$couponCode."'";
			$data_coupon['iUsed']=$noOfCouponUsed - 1;
			$obj->MySQLQueryPerform("coupon",$data_coupon,'update',$where);
		}
		
        $statusUpdate_user = "Not Assigned";
        $trip_status       = "Cancelled";
		
		$sql = "SELECT CONCAT(rd.vName,' ',rd.vLastName) AS driverName, tr.vRideNo, tr.eType FROM trips as tr,register_driver as rd WHERE tr.iTripId=rd.iTripId AND rd.iDriverId = '".$iDriverId."'";
		$result = $obj->MySQLSelect($sql);
		/* For PubNub Setting */
		$tableName = $userType != "Driver"?"register_driver":"register_user";
		$iMemberId_VALUE = $userType != "Driver"?$iDriverId:$iUserId;
		$iMemberId_KEY = $userType != "Driver"?"iDriverId":"iUserId";
		/*$iAppVersion=get_value($tableName, 'iAppVersion', $iMemberId_KEY,$iMemberId_VALUE,'','true');
		$eLogout=get_value($tableName, 'eLogout', $iMemberId_KEY,$iMemberId_VALUE,'','true');
		$eDeviceType=get_value($tableName, 'eDeviceType', $iMemberId_KEY,$iMemberId_VALUE,'','true');*/
    $AppData = get_value($tableName, 'iAppVersion,eDeviceType,eLogout,vLang', $iMemberId_KEY, $iMemberId_VALUE);
    $iAppVersion = $AppData[0]['iAppVersion'];
    $eLogout = $AppData[0]['eLogout'];
    $eDeviceType = $AppData[0]['eDeviceType'];
		/* For PubNub Setting Finished */
		/*$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");
			$PUBNUB_DISABLED = $generalobj->getConfigurations("configurations","PUBNUB_DISABLED");
			$PUBNUB_PUBLISH_KEY = $generalobj->getConfigurations("configurations","PUBNUB_PUBLISH_KEY");
		$PUBNUB_SUBSCRIBE_KEY = $generalobj->getConfigurations("configurations","PUBNUB_SUBSCRIBE_KEY");*/
		if($PUBNUB_DISABLED == "Yes"){
			$ENABLE_PUBNUB = "No";
		}
		
		$alertMsg = "Trip canceled";
		//$vLangCode=get_value($tableName, 'vLang', $iMemberId_KEY,$iMemberId_VALUE,'','true');
    $vLangCode = $AppData[0]['vLang'];
		if($vLangCode == "" || $vLangCode == NULL){
			$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
		if($userType == "Driver"){
			$usercanceltriplabel = $languageLabelsArr['LBL_PREFIX_TRIP_CANCEL_DRIVER'].' '.$driverReason.' '.$languageLabelsArr['LBL_CANCEL_TRIP_BY_DRIVER_MSG_SUFFIX'];
			}else{
			$usercanceltriplabel = $languageLabelsArr['LBL_PASSENGER_CANCEL_TRIP_TXT'];
		}
		$alertMsg = $usercanceltriplabel;
		
		$message_arr = array();
        $message_arr['Message'] = $message;
		if($userType == "Driver"){
			$message_arr['Reason'] = $driverReason;
			$message_arr['isTripStarted'] = "false";
		}
        $message_arr['iTripId'] = $iTripId;
        $message_arr['iUserId'] = $iUserId;
		$message_arr['driverName'] = $result[0]['driverName'];
		$message_arr['vRideNo'] = $result[0]['vRideNo'];
		$message_arr['eType'] = $result[0]['eType'];
		$message_arr['vTitle'] = $alertMsg;
		
		$message = json_encode($message_arr,JSON_UNESCAPED_UNICODE);
		
		#####################Add Status Message#########################
		$DataTripMessages['tMessage']= $message;
		$DataTripMessages['iDriverId']= $iDriverId;
		$DataTripMessages['iTripId']= $iTripId;
		$DataTripMessages['iUserId']= $iUserId;
		if($userType != "Driver"){
			$DataTripMessages['eFromUserType']= "Passenger";
			$DataTripMessages['eToUserType']= "Driver";
			}else{
			$DataTripMessages['eFromUserType']= "Driver";
			$DataTripMessages['eToUserType']= "Passenger";
		}
		$DataTripMessages['eReceived']= "No";
		$DataTripMessages['dAddedDate']= @date("Y-m-d H:i:s");
		
		$obj->MySQLQueryPerform("trip_status_messages",$DataTripMessages,'insert');
		################################################################
		
		$where = " iTripId = '$iTripId'";
		$Data_update_trips['iActive']=$active_status;
		$Data_update_trips['tEndDate']=@date("Y-m-d H:i:s");
		if($vTripPaymentMode == "Card" && $fCancellationFare > 0){
			$Data_update_trips['eCancelChargeFailed']=$eCancelChargeFailed;
			$Data_update_trips['fCancellationFare']=$fCancellationFare;
		}
		
		$Data_update_trips['eCancelledBy']=$userType;
		if($userType == "Driver"){
			$Data_update_trips['vCancelReason'] = $driverReason;
			$Data_update_trips['vCancelComment'] = $driverComment;
			$Data_update_trips['eCancelled'] = "Yes";
		}
		
		$id = $obj->MySQLQueryPerform("trips",$Data_update_trips,'update',$where);
		
		
		$where = " iUserId = '$iUserId'";
		$Data_update_passenger['vCallFromDriver']=$statusUpdate_user;
		$Data_update_passenger['vTripStatus']=$trip_status;
		
		$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
		
		
		$where = " iDriverId='$iDriverId'";
		// $Data_update_driver['iTripId']=$statusUpdate_user;
		$Data_update_driver['vTripStatus']=$trip_status;
		
		$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
		
		
		
		
		
		
		
		
		if($ENABLE_PUBNUB == "Yes"  && $PUBNUB_PUBLISH_KEY != "" && $PUBNUB_SUBSCRIBE_KEY != ""/*  && $iAppVersion > 1 && $eDeviceType == "Android" */){
			
			//$pubnub = new Pubnub\Pubnub($PUBNUB_PUBLISH_KEY, $PUBNUB_SUBSCRIBE_KEY);
			$pubnub = new Pubnub\Pubnub(array("publish_key" => $PUBNUB_PUBLISH_KEY,"subscribe_key" => $PUBNUB_SUBSCRIBE_KEY, "uuid" => $uuid));
			
			
			if($userType!="Driver"){
				$channelName = "DRIVER_".$iDriverId;
				$tSessionId=get_value("register_driver", 'tSessionId', "iDriverId",$iDriverId,'','true');
				}else{
				$channelName = "PASSENGER_".$iUserId;
				$tSessionId=get_value("register_user", 'tSessionId', "iUserId",$iUserId,'','true');
			}
			$message_arr['tSessionId'] = $tSessionId;
			$message_pub = json_encode($message_arr,JSON_UNESCAPED_UNICODE);
			
			$info = $pubnub->publish($channelName, $message_pub);
			
		}
		
		if($userType !="Driver"){
			$sql = "SELECT iGcmRegId,eDeviceType,tLocationUpdateDate FROM register_driver WHERE iDriverId IN (".$iDriverId.")";
			}else{
			$sql = "SELECT iGcmRegId,eDeviceType,tLocationUpdateDate FROM register_user WHERE iUserId IN (".$iUserId.")";
		}
		
		$result = $obj->MySQLSelect($sql);
		
		$deviceTokens_arr_ios = array();
		$registation_ids_new = array();
		
		foreach ($result as $item) {
			if($item['eDeviceType'] == "Android"){
				array_push($registation_ids_new, $item['iGcmRegId']);
				}else{
				array_push($deviceTokens_arr_ios, $item['iGcmRegId']);
			}
		}
		
		$cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + 60) / 60);
		$compare_date = @date('Y-m-d H:i:s', strtotime('-'.$cmpMinutes.' minutes'));
		
		//$alertSendAllowed = false;
		$alertSendAllowed = true;
		
		if($ENABLE_PUBNUB == "Yes" && $PUBNUB_PUBLISH_KEY != "" && $PUBNUB_SUBSCRIBE_KEY != ""){
			//$message = $alertMsg;
			$tLocUpdateDate = date("Y-m-d H:i:s",strtotime($result[0]['tLocationUpdateDate']));
			
			if($tLocUpdateDate < $compare_date){
				$alertSendAllowed = true;
			}
			}else{
			$alertSendAllowed = true;
		}
		if($eLogout == "Yes"){
			$alertSendAllowed = false;
		}
		
		if($alertSendAllowed == true){
			if(count($registation_ids_new) > 0){
				$Rmessage = array("message" => $message);
				
				$result = send_notification($registation_ids_new, $Rmessage,0);
			}
			if(count($deviceTokens_arr_ios) > 0){
				
				if($userType == "Driver"){
					sendApplePushNotification(0,$deviceTokens_arr_ios,$message,$alertMsg,0);
					}else{
					sendApplePushNotification(1,$deviceTokens_arr_ios,$message,$alertMsg,0);
				}
				
			}
		}
		
		
		// Code for Check last logout date is update in driver_log_report
		
		$driverId_log=get_value('trips', 'iDriverId', 'iTripId',$iTripId,'','true');
		$query = "SELECT * FROM driver_log_report WHERE iDriverId = '".$driverId_log."' ORDER BY iDriverLogId DESC LIMIT 0,1";
		$db_driver = $obj->MySQLSelect($query);
		if(count($db_driver) > 0) {
			$driver_lastonline = @date("Y-m-d H:i:s");
			$updateQuery = "UPDATE driver_log_report set dLogoutDateTime='".$driver_lastonline."' WHERE iDriverLogId = ".$db_driver[0]['iDriverLogId'];
			$obj->sql_query($updateQuery);
		}
		// Code for Check last logout date is update in driver_log_report Ends
		
		//getTripChatDetails($iTripId);
		$returnArr['Action'] = "1";
        echo json_encode($returnArr);
		
	}
	
	###########################################################################
	
	if($type=="addDestination"){
		
		//$userId     = isset($_REQUEST["UserId"]) ? $_REQUEST["UserId"] : '';
		$Latitude     = isset($_REQUEST["Latitude"]) ? $_REQUEST["Latitude"] : '';
		$Longitude     = isset($_REQUEST["Longitude"]) ? $_REQUEST["Longitude"] : '';
		$Address     = isset($_REQUEST["Address"]) ? $_REQUEST["Address"] : '';
		$userType     = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : '';
		//$iDriverId     = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $iTripId     = isset($_REQUEST["TripId"]) ? $_REQUEST["TripId"] : '';
    $eConfirmByUser = isset($_REQUEST['eConfirmByUser']) ? $_REQUEST['eConfirmByUser'] : 'No';
    $eTollConfirmByUser = isset($_REQUEST['eTollConfirmByUser']) ? $_REQUEST['eTollConfirmByUser'] : 'No';
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $fTollPrice = isset($_REQUEST["fTollPrice"]) ? $_REQUEST["fTollPrice"] : '';
		$vTollPriceCurrencyCode = isset($_REQUEST["vTollPriceCurrencyCode"]) ? $_REQUEST["vTollPriceCurrencyCode"] : '';
		$eTollSkipped = isset($_REQUEST["eTollSkipped"]) ? $_REQUEST["eTollSkipped"] : 'Yes';
    if($eConfirmByUser == "" || $eConfirmByUser == NULL){
      $eConfirmByUser = "No";
    }
    if($eTollConfirmByUser == "" || $eTollConfirmByUser == NULL){
      $eTollConfirmByUser = "No";
    }
    if($UserType == "Passenger") {
      $tblname = "register_user";
			$iUserId = "iUserId";
			$vCurrency = "vCurrencyPassenger";
			$sqlp = "SELECT ru.vCurrencyPassenger,ru.vLang,cu.vSymbol,cu.Ratio FROM register_user as ru LEFT JOIN currency as cu ON ru.vCurrencyPassenger = cu.vName WHERE iUserId = '".$iMemberId."'";
      $passengerData = $obj->MySQLSelect($sqlp);
      $currencycode = $passengerData[0]['vCurrencyPassenger'];
      $currencySymbol = $passengerData[0]['vSymbol'];
      $priceRatio=$passengerData[0]['Ratio'];
    }else{
			$tblname = "register_driver";
			$iUserId = "iDriverId";
			$vCurrency = "vCurrencyDriver";
			$sqld = "SELECT rd.vCurrencyDriver,rd.vLang,cu.vSymbol,cu.Ratio FROM register_driver as rd LEFT JOIN currency as cu ON rd.vCurrencyDriver = cu.vName WHERE iDriverId = '".$iMemberId."'";
      $driverData = $obj->MySQLSelect($sqld);
      $currencycode = $driverData[0]['vCurrencyDriver'];
      $currencySymbol = $driverData[0]['vSymbol'];
      $priceRatio=$driverData[0]['Ratio'];
		}
    if($currencycode == "" || $currencycode == NULL){
      $sql = "SELECT vName,vSymbol,Ratio from currency WHERE eDefault = 'Yes'";
      $currencyData = $obj->MySQLSelect($sql);
      $currencycode = $currencyData[0]['vName'];
      $currencySymbol = $currencyData[0]['vSymbol'];
      $priceRatio=$currencyData[0]['Ratio'];
    }
    $dropofflocationarr = array($Latitude,$Longitude);
    $ChangeAddress = "No";
    $sql_trip = "SELECT iUserId,iDriverId,tStartLat,tStartLong,tEndLat as TripEndLat,tEndLong as TripEndLong,fPickUpPrice,fNightPrice,iVehicleTypeId from trips WHERE iTripId='".$iTripId."'";
    $data_trip = $obj->MySQLSelect($sql_trip);
    $userId = $data_trip[0]['iUserId'];
    $iDriverId = $data_trip[0]['iDriverId'];  
    $TripEndLat = $data_trip[0]['TripEndLat'];
    $TripEndLong = $data_trip[0]['TripEndLong']; 
    $tStartLat = $data_trip[0]['tStartLat'];
    $tStartLong = $data_trip[0]['tStartLong'];
    $fPickUpPrice = $data_trip[0]['fPickUpPrice'];
    $fNightPrice = $data_trip[0]['fNightPrice'];
    $iVehicleTypeId = $data_trip[0]['iVehicleTypeId']; 
    if($TripEndLat != "" && $TripEndLong != ""){
      $ChangeAddress = "Yes";
    }
    $allowed_ans = checkAllowedAreaNew($dropofflocationarr,"Yes");
    if($allowed_ans == "No"){
  	  	$returnArr['Action'] = "0";
    		$returnArr['message'] = "LBL_DROP_LOCATION_NOT_ALLOW";
        echo json_encode($returnArr);exit;
  	}
		
		if($userType !="Driver"){
			//$sql = "SELECT ru.iTripId,tr.iDriverId,rd.vTripStatus as driverStatus,rd.iGcmRegId as regId,rd.eDeviceType as deviceType FROM register_user as ru,trips as tr,register_driver as rd WHERE ru.iUserId='$userId' AND tr.iTripId=ru.iTripId AND rd.iDriverId=tr.iDriverId";
      $sql = "SELECT rd.vTripStatus as driverStatus,rd.iGcmRegId as regId,rd.eDeviceType as deviceType,rd.vLatitude as tDriverLatitude,rd.vLongitude as tDriverLongitude FROM register_driver as rd WHERE rd.iDriverId='".$iDriverId."'";
			}else{
			//$sql = "SELECT rd.iTripId,rd.vTripStatus as driverStatus,ru.iGcmRegId as regId,ru.eDeviceType as deviceType FROM trips as tr,register_driver as rd ,register_user as ru WHERE ru.iUserId='$userId' AND rd.iDriverId='$iDriverId'";
      $sql = "SELECT rd.vTripStatus as driverStatus,ru.iGcmRegId as regId,ru.eDeviceType as deviceType,rd.vLatitude as tDriverLatitude,rd.vLongitude as tDriverLongitude FROM register_driver as rd ,register_user as ru WHERE ru.iUserId='$userId' AND rd.iDriverId='$iDriverId'";
		}
		
		$data = $obj->MySQLSelect($sql);
		
		if(count($data) > 0){
			$driverStatus = $data[0]['driverStatus'];
      ######### Checking For Flattrip #########
      $sourceLocationArr = array($tStartLat,$tStartLong);       
  		$destinationLocationArr = array($Latitude,$Longitude);      
      $eFlatTrip = "No"; 
      $fFlatTripPrice = 0;
      $data_flattrip['eFlatTrip'] = $eFlatTrip;
      $data_flattrip['Flatfare'] = $fFlatTripPrice;
      if($eFlatTrip == "Yes"){
          $data_surgePrice = checkSurgePrice($iVehicleTypeId,"");
          $SurgePriceValue = 1;
          $SurgePrice = "";
          if($data_surgePrice['Action'] == "0"){
      			if($data_surgePrice['message'] == "LBL_PICK_SURGE_NOTE"){
      				$fPickUpPrice=$data_surgePrice['SurgePriceValue'];
      			}else{
      				$fNightPrice=$data_surgePrice['SurgePriceValue'];
      			}
            $SurgePriceValue = $data_surgePrice['SurgePriceValue'];
            $SurgePrice = $data_surgePrice['SurgePrice'];
      		} 
          if($APPLY_SURGE_ON_FLAT_FARE == "No" && $data_flattrip['eFlatTrip'] == "Yes"){
            $fPickUpPrice = 1;
      		  $fNightPrice = 1;
            $SurgePriceValue = 1;
            $SurgePrice = "";
          }
          if($eConfirmByUser == "No" && $eFlatTrip == "Yes"){
               $TripPrice = round($fFlatTripPrice * $priceRatio,2);
               //$fSurgePriceDiff = round(($TripPrice * $SurgePriceValue) - $TripPrice, 2);
               //$TripPrice = $TripPrice+$fSurgePriceDiff;
               $returnArr['Action']="0";
    		       $returnArr['message'] = "Yes";
               $returnArr['eFlatTrip'] = $eFlatTrip;
               $returnArr['SurgePrice'] = ""; // $SurgePrice
               $returnArr['SurgePriceValue'] = ""; // $SurgePriceValue
               $returnArr['fFlatTripPrice'] = $TripPrice;
               $returnArr['fFlatTripPricewithsymbol'] = $currencySymbol." ".$TripPrice;
               echo json_encode($returnArr);exit;
          }
          $Data_trips['fTollPrice']="0";
    			$Data_trips['vTollPriceCurrencyCode']="";
    			$Data_trips['eTollSkipped']="No";
      }else{
        $eFlatTrip = "No"; 
        $fFlatTripPrice = 0;
      }  
      ######### Checking For Flattrip #########
			
			$where_trip = " iTripId = '".$iTripId."'";
			$Data_trips['tEndLat']=$Latitude;
			$Data_trips['tEndLong']=$Longitude;
			$Data_trips['tDaddress']=$Address;
      $Data_trips['eFlatTrip']=$eFlatTrip;
      $Data_trips['fFlatTripPrice']=$fFlatTripPrice;
      $Data_trips['fPickUpPrice']=$fPickUpPrice;
      $Data_trips['fNightPrice']=$fNightPrice;
			$id = $obj->MySQLQueryPerform("trips",$Data_trips,'update',$where_trip);
      ## Insert Into trip Destination ###
      $Data_trip_destination['iTripId']=$iTripId;
      $Data_trip_destination['tDaddress']=$Address;      
      $Data_trip_destination['tEndLat']=$Latitude;
      $Data_trip_destination['tEndLong']=$Longitude;
      $Data_trip_destination['tDriverLatitude']=$data[0]['tDriverLatitude'];
      $Data_trip_destination['tDriverLongitude']=$data[0]['tDriverLongitude'];
      $Data_trip_destination['eUserType']=$userType;
      $Data_trip_destination['dAddedDate']=@date("Y-m-d H:i:s");
      $Data_trip_destination_id = $obj->MySQLQueryPerform('trip_destinations',$Data_trip_destination,'insert');            
      ## Insert Into trip Destination ###      
			
			if($driverStatus == "Active"){
				
				$where_passenger = " iUserId = '$userId'";
				$Data_passenger['tDestinationLatitude']=$Latitude;
				$Data_passenger['tDestinationLongitude']=$Longitude;
				$Data_passenger['tDestinationAddress']=$Address;
				$id = $obj->MySQLQueryPerform("register_user",$Data_passenger,'update',$where_passenger);
				
				}else{
				
				
				
				
				
				/*$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");
					$PUBNUB_DISABLED = $generalobj->getConfigurations("configurations","PUBNUB_DISABLED");
					$PUBNUB_PUBLISH_KEY = $generalobj->getConfigurations("configurations","PUBNUB_PUBLISH_KEY");
				$PUBNUB_SUBSCRIBE_KEY = $generalobj->getConfigurations("configurations","PUBNUB_SUBSCRIBE_KEY");*/
				if($PUBNUB_DISABLED == "Yes"){
					$ENABLE_PUBNUB = "No";
				}
				
				/*if($userType !="Driver"){
					$alertMsg = "Destination is added by passenger.";
				}else{
					$alertMsg = "Destination is added by driver.";
				}  */
        /* For PubNub Setting */
				$tableName = $userType != "Driver"?"register_driver":"register_user";
				$iMemberId_VALUE = $userType != "Driver"?$iDriverId:$userId;
				$iMemberId_KEY = $userType != "Driver"?"iDriverId":"iUserId";
				/*$iAppVersion=get_value($tableName, 'iAppVersion', $iMemberId_KEY,$iMemberId_VALUE,'','true');
				$eDeviceType=get_value($tableName, 'eDeviceType', $iMemberId_KEY,$iMemberId_VALUE,'','true');*/
        $AppData = get_value($tableName, 'iAppVersion,eDeviceType,vLang,tSessionId', $iMemberId_KEY, $iMemberId_VALUE);
        $iAppVersion = $AppData[0]['iAppVersion'];
        $eDeviceType = $AppData[0]['eDeviceType'];
        $tSessionId = $AppData[0]['tSessionId']; 
				/* For PubNub Setting Finished */
        //$vLangCode=get_value($tableName, 'vLang', $iMemberId_KEY,$iMemberId_VALUE,'','true'); 
        $vLangCode = $AppData[0]['vLang'];
				if($vLangCode == "" || $vLangCode == NULL){
					$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
				}
				$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");  
				if($ChangeAddress == "No"){
				$lblValue = $userType == "Driver"?"LBL_DEST_ADD_BY_DRIVER":"LBL_DEST_ADD_BY_PASSENGER";
        }else{
          $lblValue = $userType == "Driver"?"LBL_DEST_EDIT_BY_DRIVER":"LBL_DEST_EDIT_BY_PASSENGER";
        }
				$alertMsg = $languageLabelsArr[$lblValue];
        $message  = "DestinationAdded";
				$message_arr = array();
				$message_arr['Message'] = $message;
				$message_arr['DLatitude'] = $Latitude;
				$message_arr['DLongitude'] = $Longitude;
				$message_arr['DAddress'] = $Address;
        $message_arr['vTitle'] = $alertMsg;
        $message_arr['iTripId'] = $iTripId;
        $message_arr['eType'] = $APP_TYPE;
        $message_arr['eFlatTrip'] = $eFlatTrip;
        $message_arr['time'] = strval(time());
				$message = json_encode($message_arr);
        $alertSendAllowed = true;
        	#####################Add Status Message#########################
    		$DataTripMessages['tMessage']= $message;
    		$DataTripMessages['iDriverId']= $iDriverId;
    		$DataTripMessages['iTripId']= $iTripId;
    		$DataTripMessages['iUserId']= $userId;
    		if($userType != "Driver"){
    			$DataTripMessages['eFromUserType']= "Passenger";
    			$DataTripMessages['eToUserType']= "Driver";
    		}else{
    			$DataTripMessages['eFromUserType']= "Driver";
    			$DataTripMessages['eToUserType']= "Passenger";
    		}
    		$DataTripMessages['eReceived']= "No";
    		$DataTripMessages['dAddedDate']= @date("Y-m-d H:i:s");
    		$obj->MySQLQueryPerform("trip_status_messages",$DataTripMessages,'insert');
    		################################################################
				
				if($ENABLE_PUBNUB == "Yes"  && $PUBNUB_PUBLISH_KEY != "" && $PUBNUB_SUBSCRIBE_KEY != ""/*  && $iAppVersion > 1 && $eDeviceType == "Android" */){
					
					//$pubnub = new Pubnub\Pubnub($PUBNUB_PUBLISH_KEY, $PUBNUB_SUBSCRIBE_KEY);
					$pubnub = new Pubnub\Pubnub(array("publish_key" => $PUBNUB_PUBLISH_KEY,"subscribe_key" => $PUBNUB_SUBSCRIBE_KEY, "uuid" => $uuid));
					
					if($userType !="Driver"){
						$channelName = "DRIVER_".$iDriverId;
            //$tSessionId=get_value("register_driver", 'tSessionId', "iDriverId",$iDriverId,'','true');
						}else{
						$channelName = "PASSENGER_".$userId;
            //$tSessionId=get_value("register_user", 'tSessionId', "iUserId",$userId,'','true');
					}
					$message_arr['tSessionId'] = $tSessionId;
					$message_pub = json_encode($message_arr,JSON_UNESCAPED_UNICODE);
					$info = $pubnub->publish($channelName, $message_pub);
					
				}
				
				$deviceTokens_arr_ios = array();
				$registation_ids_new = array();
				
        if($alertSendAllowed == true){
          if($data[0]['deviceType'] == "Android" /*&& $ENABLE_PUBNUB != "Yes"*/){
					array_push($registation_ids_new, $data[0]['regId']);
					
					$Rmessage = array("message" => $message);
					
					$result = send_notification($registation_ids_new, $Rmessage,0);
					}else if($data[0]['deviceType'] != "Android" ){
					array_push($deviceTokens_arr_ios, $data[0]['regId']);
					
					/*if($ENABLE_PUBNUB == "Yes"){
						$message = "";
					} */
					
					if($message != ""){
						if($userType == "Driver"){
							sendApplePushNotification(0,$deviceTokens_arr_ios,$message,$alertMsg,0);
							}else{
							sendApplePushNotification(1,$deviceTokens_arr_ios,$message,$alertMsg,0);
						}
					}
				}
				}
				
			}
			
			$returnArr['Action']="1";
			
			}else{
			$returnArr['Action']="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		echo json_encode($returnArr);
	}
	
	###################### getAssignedDriverLocation ##########################
	if($type == "getDriverLocations"){
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		
		$sql = "SELECT vLatitude, vLongitude,vTripStatus FROM `register_driver` WHERE iDriverId='$iDriverId'";
		$Data = $obj->MySQLSelect($sql);
		
		if ( count($Data) == 1 )
		{	$returnArr['Action'] = "1";
			$returnArr['vLatitude'] = $Data[0]['vLatitude'];
			$returnArr['vLongitude'] = $Data[0]['vLongitude'];
			$returnArr['vTripStatus'] = $Data[0]['vTripStatus'];
		}
		else
		{
			$returnArr['Action'] = "0";
			$returnArr['message'] = 'Not Found';
		}
		echo json_encode($returnArr);
		
	}
	
	###########################################################################
	
	if($type=='displayFare'){
		global $currency_supported_paypal,$generalobj;
		
		$iMemberId          = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
		$userType     = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$iTripId          = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '';
		
		$tableName = $userType != "Driver"?"register_user":"register_driver";
		$iMemberId_KEY = $userType != "Driver"?"iUserId":"iDriverId";
		
		if($iTripId == ""){
			$iTripId=get_value($tableName, 'iTripId', $iMemberId_KEY,$iMemberId,'','true');
		}
		
		
		
		//$ENABLE_TIP_MODULE=$generalobj->getConfigurations("configurations","ENABLE_TIP_MODULE");
		$vTripPaymentMode =get_value('trips', 'vTripPaymentMode', 'iTripId',$iTripId,'','true');
		if($vTripPaymentMode == "Card"){
			$result_fare['ENABLE_TIP_MODULE']  = $ENABLE_TIP_MODULE;
			}else{
			$result_fare['ENABLE_TIP_MODULE']  = "No";
		}
		$result_fare['FormattedTripDate']=date('dS M Y \a\t h:i a',strtotime($result_fare[0]['tStartDate']));
		$result_fare['PayPalConfiguration']="No";
		$result_fare['DefaultCurrencyCode']="USD";
		$result_fare['PaypalFare']=strval($result_fare[0]['TotalFare']);
		$result_fare['PaypalCurrencyCode']=$vCurrencyCode;
		//$result_fare['APP_TYPE'] = $generalobj->getConfigurations("configurations","APP_TYPE");
		$result_fare['APP_TYPE'] = $APP_TYPE;
		/*if($result_fare['APP_TYPE'] == "UberX"){
			$result_fare['APP_DESTINATION_MODE'] = "None";
			}else{
			$result_fare['APP_DESTINATION_MODE'] = "Strict";
		}*/
    $result_fare['APP_DESTINATION_MODE'] = $APP_DESTINATION_MODE;
		// $result_fare['APP_DESTINATION_MODE'] = $generalobj->getConfigurations("configurations","APP_DESTINATION_MODE");
		$returnArr = gettrippricedetails($iTripId,$iMemberId,$userType, "DISPLAY");
		
		$result_fare = array_merge($result_fare, $returnArr);
		
		if(count($returnArr) > 0){
			$returnArr['Action'] = "1";
    		$returnArr['message'] = $result_fare;
			}else{
			$returnArr['Action'] = "0";
		}
		//echo "<pre>" ; print_r($returnArr); exit;
        echo json_encode($returnArr);
		
	}
	
	
	
	###########################################################################
	
    if($type=="submitRating"){
		
        //$iGeneralUserId = isset($_REQUEST["iGeneralUserId"]) ? $_REQUEST["iGeneralUserId"] : ''; // for both driver or passenger
        $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : ''; // for both driver or passenger
        $tripID    = isset($_REQUEST["tripID"]) ? $_REQUEST["tripID"] : '0';
        $rating  = isset($_REQUEST["rating"]) ? $_REQUEST["rating"] : '';
        $message   = isset($_REQUEST["message"]) ? $_REQUEST["message"] : '';
        $userType   = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger'; // Passenger or Driver
        $fAmount = isset($_REQUEST["fAmount"]) ? $_REQUEST["fAmount"] : '';
        $isCollectTip = isset($_REQUEST["isCollectTip"]) ? $_REQUEST["isCollectTip"] : ''; 
        if($isCollectTip == "" || $isCollectTip == NULL){
			    $isCollectTip = "No";
		   }
        
		$message = stripslashes($message);
		
        $sql = "SELECT * FROM `ratings_user_driver` WHERE iTripId = '$tripID' and eUserType = '$userType'";
        $row_check = $obj->MySQLSelect($sql);
		
		//$ENABLE_TIP_MODULE=$generalobj->getConfigurations("configurations","ENABLE_TIP_MODULE");
		
        if(count($row_check) > 0){
            // $returnArr['Action'] = "0"; //LBL_RATING_EXIST
            // $returnArr['message'] = "LBL_ERROR_RATING_SUBMIT_AGAIN_TXT"; //LBL_RATING_EXIST
            $returnArr['Action'] = "1";
			$returnArr['message'] = "LBL_TRIP_FINISHED_TXT";
            echo json_encode($returnArr);  exit;
		    }else{
            
            # Code For Tip Charge #
            if($isCollectTip == "Yes" && $userType == "Passenger"){
                if($fAmount > 0){
					TripCollectTip($iMemberId,$tripID,$fAmount);
				}
			} 
            # Code For Tip Charge # 
			
            if($userType == "Passenger"){
				$iDriverId =get_value('trips', 'iDriverId', 'iTripId',$tripID,'','true');
				$tableName = "register_driver";
				$where = "iDriverId='".$iDriverId."'";
				$iMemberId = $iDriverId;
				}else{   
				
				$where_trip = " iTripId = '$tripID'";
				
				$Data_update_trips['eVerified']="Verified";
				
				$id = $obj->MySQLQueryPerform("trips",$Data_update_trips,'update',$where_trip);
				
				$iUserId =get_value('trips', 'iUserId', 'iTripId',$tripID,'','true');
				$tableName = "register_user";
				$where = "iUserId='".$iUserId."'";
				$iMemberId = $iUserId;
			}
            /* Insert records into ratings table*/
            $Data_update_ratings['iTripId']=$tripID;
            $Data_update_ratings['vRating1']=$rating;
            $Data_update_ratings['vMessage']=$message;
            $Data_update_ratings['eUserType']=$userType;
			
			$id = $obj->MySQLQueryPerform("ratings_user_driver",$Data_update_ratings,'insert');
			
			/* Set average rating for passenger OR Driver */
			// Driver gives rating to passenger and passenger gives rating to driver
			/*$average_rating = getUserRatingAverage($iMemberId,$userType);
				
				$sql = "SELECT vAvgRating FROM ".$tableName.' WHERE '.$where;
				$fetchAvgRating= $obj->MySQLSelect($sql);
				
				if($fetchAvgRating[0]['vAvgRating'] > 0){
				$average_rating = round(($fetchAvgRating[0]['vAvgRating'] + $rating) / 2,1);
				}else{
				$average_rating = round($fetchAvgRating[0]['vAvgRating'] + $rating,1);
			} */
			
            $Data_update['vAvgRating']=getUserRatingAverage($iMemberId,$userType);
			
            $id = $obj->MySQLQueryPerform($tableName,$Data_update,'update',$where);
			
            if ($id) {
                $returnArr['Action'] = "1";
				$returnArr['message'] = "LBL_TRIP_FINISHED_TXT";
				$vTripPaymentMode =get_value('trips', 'vTripPaymentMode', 'iTripId',$tripID,'','true');
				if($vTripPaymentMode == "Card"){
					$returnArr['ENABLE_TIP_MODULE']  = $ENABLE_TIP_MODULE;
					}else{
					$returnArr['ENABLE_TIP_MODULE']  = "No";
				}
                echo json_encode($returnArr);
				} else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
                echo json_encode($returnArr);
			}
			
            if($userType == "Passenger"){
                sendTripReceipt($tripID);
				}else{
                sendTripReceiptAdmin($tripID);
			}
			// echo "come";
		}
		
		
	}
	
	###########################################################################
	
    if ($type == "updatePassword") {
        $user_id = isset($_REQUEST["UserID"]) ? $_REQUEST["UserID"] : '';
        $Upass        = isset($_REQUEST["pass"]) ? $_REQUEST["pass"] : '';
        $UserType = isset($_REQUEST["UserType"])?clean($_REQUEST["UserType"]):''; // UserType = Driver/Passenger
        $CurrentPassword  = isset($_REQUEST["CurrentPassword"]) ? $_REQUEST["CurrentPassword"] : '';
        if($UserType == "Passenger"){
			$tblname = "register_user";
			$vPassword=get_value('register_user', 'vPassword', 'iUserId',$user_id,'','true');
			}else{
			$tblname = "register_driver";
			$vPassword=get_value('register_driver', 'vPassword', 'iDriverId',$user_id,'','true');        
		}
        
        # Check For Valid password #
        if($CurrentPassword != ""){
			$hash = $vPassword;
			$checkValidPass = $generalobj->check_password($CurrentPassword, $hash);
			if($checkValidPass == 0){
				$returnArr['Action']="0";
				$returnArr['message'] ="LBL_WRONG_PASSWORD";
				echo json_encode($returnArr);exit;
			}
		}
        # Check For Valid password #  
		
        //$updatedPassword = $generalobj->encrypt($Upass);
        $updatedPassword = $generalobj->encrypt_bycrypt($Upass);
		
        $Data_update_user['vPassword']=$updatedPassword;
		
        if($UserType == "Passenger"){
			
            $where = " iUserId = '$user_id'";
            $id = $obj->MySQLQueryPerform("register_user",$Data_update_user,'update',$where);
			
            if ($id >0) {
				
                $returnArr['Action']="1";
                $returnArr['message']=getPassengerDetailInfo($user_id,"");
                echo json_encode($returnArr);
				
				} else {
				
                $returnArr['Action']="0";
                $returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
                echo json_encode($returnArr);
			}
			
			}else{
            $where = " iDriverId = '$user_id'";
            $id = $obj->MySQLQueryPerform("register_driver",$Data_update_user,'update',$where);
			
			
            if ($id >0) {
				
                $returnArr['Action']="1";
                $returnArr['message']=getDriverDetailInfo($user_id);
                echo json_encode($returnArr);
				
				} else {
				
                $returnArr['Action']="0";
                $returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
                echo json_encode($returnArr);
			}
		}
		
	}
	
	############################Send Sms Twilio####################################
	
	if($type=='sendVerificationSMS'){
		$mobileNo = isset($_REQUEST['MobileNo'])?clean($_REQUEST['MobileNo']):'';
		$mobileNo = str_replace('+','',$mobileNo);
		$iMemberId = isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
		$userType = isset($_REQUEST['UserType'])?clean($_REQUEST['UserType']):'Passenger';
		$REQ_TYPE = isset($_REQUEST["REQ_TYPE"]) ? $_REQUEST['REQ_TYPE'] : '';
		
		//$isdCode= $generalobj->getConfigurations("configurations","SITE_ISD_CODE");
		$isdCode= $SITE_ISD_CODE;
		//$toMobileNum= "+".$mobileNo;
		if($userType == "Passenger"){
			$tblname = "register_user";
			$fields = 'iUserId, vPhone,vPhoneCode as vPhoneCode, vEmail, vName, vLastName';
			$condfield = 'iUserId';
			$vLangCode=get_value('register_user', 'vLang', 'iUserId',$iMemberId,'','true');
			}else{
			$tblname = "register_driver";
			$fields = 'iDriverId, vPhone,vCode as vPhoneCode, vEmail, vName, vLastName';
			$condfield = 'iDriverId';
			$vLangCode=get_value('register_driver', 'vLang', 'iDriverId',$iMemberId,'','true');
		}
		
		if($vLangCode == "" || $vLangCode == NULL){
			$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
		
		$str = "select * from send_message_templates where vEmail_Code='VERIFICATION_CODE_MESSAGE'";
        $res = $obj->MySQLSelect($str);
        $prefix = $res[0]['vBody_'.$vLangCode];
        
		//$prefix = $languageLabelsArr['LBL_VERIFICATION_CODE_TXT'];
		$verificationCode_sms = mt_rand(1000, 9999);
		$verificationCode_email = mt_rand(1000, 9999);
		$message = $prefix.' '.$verificationCode_sms;
		
		
		if($iMemberId == "" && $REQ_TYPE == "DO_PHONE_VERIFY"){
			$toMobileNum = "+".$mobileNo;
			}else{
			$sql="select $fields from $tblname where $condfield = '".$iMemberId."'";
			$db_member = $obj->MySQLSelect($sql);
			
			$Data_Mail['vEmail'] = isset($db_member[0]['vEmail'])?$db_member[0]['vEmail']:'';
			$vFirstName = isset($db_member[0]['vName'])?$db_member[0]['vName']:'';
			$vLastName = isset($db_member[0]['vLastName'])?$db_member[0]['vLastName']:'';
			$Data_Mail['vName'] = $vFirstName." ".$vLastName ;
			$Data_Mail['CODE'] = $verificationCode_email ;
			$mobileNo = $db_member[0]['vPhoneCode'].$db_member[0]['vPhone'];
			$toMobileNum = "+".$mobileNo;
		}
		
		
		$emailmessage = "";
		$phonemessage = "";
		if($REQ_TYPE == "DO_EMAIL_PHONE_VERIFY"){
			$sendemail = $generalobj->send_email_user("APP_EMAIL_VERIFICATION_USER",$Data_Mail);
			if($sendemail != true || $sendemail != "true" || $sendemail != "1"){
				$sendemail = 0;
			}
			$result = sendEmeSms($toMobileNum,$message);
			if($result ==0){
				$toMobileNum = "+".$isdCode.$mobileNo;
				$result = sendEmeSms($toMobileNum,$message);
			}
			
			$returnArr['Action'] ="1";
			if($sendemail == 0 && $result ==0){
				$returnArr['Action'] ="0";
				$returnArr['message'] = "LBL_ACC_VERIFICATION_FAILED";
				}else{
				$returnArr['message_sms'] = $result == 0?"LBL_MOBILE_VERIFICATION_FAILED_TXT":$verificationCode_sms;
				$returnArr['message_email'] = $sendemail == 0?"LBL_EMAIL_VERIFICATION_FAILED_TXT":$verificationCode_email;
			}
			echo json_encode($returnArr);exit;
			} else if($REQ_TYPE == "DO_PHONE_VERIFY"){

			$result = sendEmeSms($toMobileNum,$message);
			if($result == 0){
				$toMobileNum = "+".$isdCode.$mobileNo;
				$result = sendEmeSms($toMobileNum,$message);
			}
			
			if($result == 0){
				$returnArr['Action'] ="0";
				$returnArr['message'] ="LBL_MOBILE_VERIFICATION_FAILED_TXT";
				echo json_encode($returnArr);exit;
				}else{
				$returnArr['Action'] ="1";
				$returnArr['message'] =$verificationCode_sms;
				echo json_encode($returnArr);exit;
			}
			}else if($REQ_TYPE == "DO_EMAIL_VERIFY"){
			$sendemail = $generalobj->send_email_user("APP_EMAIL_VERIFICATION_USER",$Data_Mail);
			if($sendemail != true || $sendemail != "true" || $sendemail != "1"){
				$sendemail = 0;
			}
			if($sendemail == 0){
				$returnArr['Action'] ="0";
				$returnArr['message'] = "LBL_EMAIL_VERIFICATION_FAILED_TXT";
				echo json_encode($returnArr);exit;
				}else{
				$returnArr['Action'] ="1";
				$returnArr['message'] =  $Data_Mail['CODE'];
				echo json_encode($returnArr);exit;
			}
			}else if($REQ_TYPE == "EMAIL_VERIFIED"){
			$where = " ".$condfield." = '".$iMemberId."'";
			$Data['eEmailVerified']="Yes";
			$id = $obj->MySQLQueryPerform($tblname,$Data,'update',$where);
			
			if ($id) {
				$returnArr['Action'] ="1";
				$returnArr['message']  = "LBL_EMAIl_VERIFIED";
				
				if($userType == 'Passenger'){
					$returnArr['userDetails']['Action']="1";
					$returnArr['userDetails']['message']=getPassengerDetailInfo($iMemberId);
					}else {
					$returnArr['userDetails']['Action']="1";
					$returnArr['userDetails']['message']=getDriverDetailInfo($iMemberId);
				}
				echo json_encode($returnArr);exit;
				} else {
				$returnArr['Action'] ="0";
				$returnArr['message']  = "LBL_EMAIl_VERIFIED_ERROR";
				echo json_encode($returnArr);exit;
			}
			
			}else if($REQ_TYPE == "PHONE_VERIFIED"){
			
			$where = " ".$condfield." = '".$iMemberId."'";
			$Data['ePhoneVerified']="Yes";
			$id = $obj->MySQLQueryPerform($tblname,$Data,'update',$where);
			
			if ($id) {
				$returnArr['Action'] ="1";
				$returnArr['message']  = "LBL_PHONE_VERIFIED";
				if($userType == 'Passenger'){
					$returnArr['userDetails']['Action']="1";
					$returnArr['userDetails']['message']=getPassengerDetailInfo($iMemberId);
					}else {
					$returnArr['userDetails']['Action']="1";
					$returnArr['userDetails']['message']=getDriverDetailInfo($iMemberId);
				}
				echo json_encode($returnArr);exit;
				} else {
				$returnArr['Action'] ="0";
				$returnArr['message'] = "LBL_PHONE_VERIFIED_ERROR";
				echo json_encode($returnArr);exit;
			}
		}
		
		//	$returnArr['message'] =$verificationCode;
		//echo json_encode($returnArr);
	}
	
	############################Send Sms Twilio END################################
	
	###########################################################################
	
	if ($type == "updateDriverStatus") {
		
		$iDriverId         = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$Status_driver    = isset($_REQUEST["Status"]) ? $_REQUEST["Status"] : '';
		$isUpdateOnlineDate    = isset($_REQUEST["isUpdateOnlineDate"]) ? $_REQUEST["isUpdateOnlineDate"] : '';
		$latitude_driver  = isset($_REQUEST["latitude"]) ? $_REQUEST["latitude"] : '';
		$longitude_driver = isset($_REQUEST["longitude"]) ? $_REQUEST["longitude"] : '';
		$iGCMregID = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
		$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		//$APP_TYPE = $generalobj->getConfigurations("configurations", "APP_TYPE");
		//$APP_PAYMENT_MODE = $generalobj->getConfigurations("configurations", "APP_PAYMENT_MODE");
		
		checkmemberemailphoneverification($iDriverId,"Driver");
		
		if($iDriverId==''){
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
			echo json_encode($returnArr);
			exit;
		}
		
		$GCMID =get_value('register_driver', 'iGcmRegId', 'iDriverId',$iDriverId,'','true');
		if($GCMID != "" && $iGCMregID!="" && $GCMID != $iGCMregID){
			$returnArr['Action'] = "0";
			$returnArr['message'] = "SESSION_OUT";
			echo json_encode($returnArr);
			exit;
		}
		$returnArr['Enable_Hailtrip'] = "No";
		
		//$COMMISION_DEDUCT_ENABLE=$generalobj->getConfigurations("configurations","COMMISION_DEDUCT_ENABLE");
		if($COMMISION_DEDUCT_ENABLE == 'Yes' && ($APP_PAYMENT_MODE == "Cash" || $APP_PAYMENT_MODE == "Cash-Card")) {
			$vLang =get_value('register_driver', 'vLang', 'iDriverId',$iDriverId,'','true');
			if($vLang == "" || $vLang == NULL){
				$vLang = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
			}
			$languageLabelsArr= getLanguageLabelsArr($vLang,"1");
			$user_available_balance = $generalobj->get_user_available_balance($iDriverId,"Driver");
			$driverDetail = get_value('register_driver AS rd LEFT JOIN currency AS c ON c.vName=rd.vCurrencyDriver', 'rd.vCurrencyDriver,c.Ratio,c.vSymbol', 'rd.iDriverId',$iDriverId);
			$vCurrencyDriver =$driverDetail[0]['vCurrencyDriver'];
			$ratio =$driverDetail[0]['Ratio'];
			$currencySymbol=$driverDetail[0]['vSymbol'];
			//$WALLET_MIN_BALANCE=$generalobj->getConfigurations("configurations","WALLET_MIN_BALANCE");
			if($WALLET_MIN_BALANCE > $user_available_balance){
				// $returnArr['Action'] = "0";
				$returnArr['message']="REQUIRED_MINIMUM_BALNCE";
				if($APP_TYPE == "UberX"){
					$returnArr['Msg']=str_replace('####', $currencySymbol.($WALLET_MIN_BALANCE * $ratio), $languageLabelsArr['LBL_REQUIRED_MINIMUM_BALNCE_UBERX']);
					}else{
					$returnArr['Msg']=str_replace('####', $currencySymbol.($WALLET_MIN_BALANCE * $ratio), $languageLabelsArr['LBL_REQUIRED_MINIMUM_BALNCE']);
				}
				
				if($APP_PAYMENT_MODE == "Cash"){ 
					if($Status_driver == "Available"){
						$returnArr['Action'] = "0";	
						echo json_encode($returnArr);
						exit;
					}
				} 
			}
			$returnArr['Enable_Hailtrip'] = "Yes";
		}
		
		if($COMMISION_DEDUCT_ENABLE == 'No' && ($APP_PAYMENT_MODE == "Cash" || $APP_PAYMENT_MODE == "Cash-Card")) {
			$returnArr['Enable_Hailtrip'] = "Yes"; 
		}
		
		// getDriverStatus($iDriverId);
		//$APP_TYPE = $generalobj->getConfigurations("configurations", "APP_TYPE");
		$ssql = "";
		if($APP_TYPE == "UberX"){
			$ssql = "And dv.vCarType !=''";
		}
		
		$sql = "SELECT make.vMake, model.vTitle, dv.*, rd.iDriverVehicleId as iSelectedVehicleId FROM `driver_vehicle` dv, make, model, register_driver as rd WHERE dv.iDriverId='$iDriverId' AND rd.iDriverId='$iDriverId' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId` AND dv.`eStatus`='Active'".$ssql ;
		
		$Data_Car = $obj->MySQLSelect($sql);
		
		if (count($Data_Car) > 0)
		{
			$status= "CARS_NOT_ACTIVE";
			
			$i=0;
			while(count($Data_Car)>$i){
				
				$eStatus=$Data_Car[$i]['eStatus'];
				if($eStatus == "Active"){
					$status= "CARS_AVAIL";
				}
				$i++;
			}
			
			if($status == "CARS_AVAIL" && ($Data_Car[0]['iSelectedVehicleId'] =="0" ||$Data_Car[0]['iSelectedVehicleId'] =="") ){
				// echo "SELECT_CAR";
				$returnArr['Action']="0";
				$returnArr['message']="LBL_SELECT_CAR_MESSAGE_TXT";
				echo json_encode($returnArr);
				exit;
				}else if($status == "CARS_NOT_ACTIVE"){
				// echo "CARS_NOT_ACTIVE";
				$returnArr['Action']="0";
				$returnArr['message']="LBL_INACTIVE_CARS_MESSAGE_TXT";
				echo json_encode($returnArr);
				exit;
			}
			
			
		}
		else
		{
			// echo "NO_CARS_AVAIL";
			$sql = "SELECT count(iDriverVehicleId) as TotalVehicles from driver_vehicle WHERE iDriverId = '".$iDriverId."' AND ( eStatus = 'Inactive' OR eStatus = 'Deleted')";
		  $db_Total_vehicle = $obj->MySQLSelect($sql);
      $TotalVehicles = $db_Total_vehicle[0]['TotalVehicles'];   
			$returnArr['Action']="0";
			if($TotalVehicles == 0){
			$returnArr['message']="LBL_NO_CAR_AVAIL_TXT";
      }else{
        $returnArr['message']="LBL_INACTIVE_CARS_MESSAGE_TXT";
      }
			echo json_encode($returnArr);
			exit;
		}
		
		$where = " iDriverId='".$iDriverId."'";
		if($Status_driver != ''){
			$Data_update_driver['vAvailability']=$Status_driver;
		}
		
		if($latitude_driver != '' && $longitude_driver != ''){
			$Data_update_driver['vLatitude']=$latitude_driver;
			$Data_update_driver['vLongitude']=$longitude_driver;
		}
		
		if($Status_driver == "Available"){
			$Data_update_driver['tOnline']=@date("Y-m-d H:i:s");
			// insert as online
			// Code for Check last logout date is update in driver_log_report
			$query = "SELECT * FROM driver_log_report WHERE dLogoutDateTime = '0000-00-00 00:00:00' AND iDriverId = '".$iDriverId."' ORDER BY iDriverLogId DESC LIMIT 0,1";
			$db_driver = $obj->MySQLSelect($query);
			if(count($db_driver) > 0) {
				$sql = "SELECT tLastOnline FROM register_driver WHERE iDriverId = '".$iDriverId."'";
				$db_drive_lastonline = $obj->MySQLSelect($sql);
				$driver_lastonline = $db_drive_lastonline[0]['tLastOnline'];
				$updateQuery = "UPDATE driver_log_report set dLogoutDateTime='".$driver_lastonline."' WHERE iDriverLogId = ".$db_driver[0]['iDriverLogId'];
				$obj->sql_query($updateQuery);
			}
			// Code for Check last logout date is update in driver_log_report Ends
			$vIP=get_client_ip();
			$curr_date= date('Y-m-d H:i:s');
			$sql = "INSERT INTO `driver_log_report` (`iDriverId`,`dLoginDateTime`,`vIP`) VALUES ('" . $iDriverId  . "','".$curr_date."','" . $vIP . "')";
			$insert_log = $obj->sql_query($sql);
		}
		
		if($Status_driver == "Not Available"){
			// update as offline
			$Data_update_driver['tLastOnline']=@date("Y-m-d H:i:s");
			$curr_date= date('Y-m-d H:i:s');
			$selct_query = "select * from driver_log_report WHERE iDriverId = '".$iDriverId."' order by `iDriverLogId` desc limit 0,1";
			$get_data_log = $obj->sql_query($selct_query);
			
			$update_sql = "UPDATE driver_log_report set dLogoutDateTime = '".$curr_date."' WHERE iDriverLogId ='".$get_data_log[0]['iDriverLogId']."'";
			$result = $obj->sql_query($update_sql);
		}
		
		if(($isUpdateOnlineDate == "true" && $Status_driver == "Available") || ($isUpdateOnlineDate == "" && $Status_driver == "") || $isUpdateOnlineDate == "true"){
			$Data_update_driver['tOnline']=@date("Y-m-d H:i:s");
			$Data_update_driver['tLastOnline']=@date("Y-m-d H:i:s");
		}
		
		$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
		
		# Update User Location Date #
		Updateuserlocationdatetime($iDriverId,"Driver",$vTimeZone);    
		# Update User Location Date #
		
		if ($id) {
			$returnArr['Action']="1";
			echo json_encode($returnArr);
			} else {
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
			echo json_encode($returnArr);
		}
		
	}
	
	###########################################################################
	
	if($type == "LoadAvailableCars"){
		$iDriverId              = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		
		$sql = "SELECT register_driver.iDriverVehicleId as DriverSelectedVehicleId,make.vMake, model.vTitle, dv.* FROM `driver_vehicle` dv, make, model,register_driver WHERE dv.iDriverId='$iDriverId' AND register_driver.iDriverId = '$iDriverId' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId` AND dv.`eStatus`='Active'";
    //$sql = "SELECT register_driver.iDriverVehicleId as DriverSelectedVehicleId,make.vMake, model.vTitle, dv.* FROM `driver_vehicle` dv, make, model,register_driver WHERE dv.iDriverId='$iDriverId' AND register_driver.iDriverId = '$iDriverId' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId`";
		
		$Data_Car = $obj->MySQLSelect($sql);
		
		if (count($Data_Car) > 0)
		{
			$status= "CARS_NOT_ACTIVE";
			
			$i=0;
			while(count($Data_Car)>$i){
				
				$eStatus=$Data_Car[$i]['eStatus'];
				if($eStatus == "Active"){
					$status= "CARS_AVAIL";
				}
				$i++;
			}
			if($status == "CARS_NOT_ACTIVE"){
				$returnArr['Action']="0";
				$returnArr['message']="LBL_INACTIVE_CARS_MESSAGE_TXT";
				echo json_encode($returnArr);
				exit;
			}
			
			// $returnArr['carList'] = $Data_Car;
      $db_vehicle_new = $Data_Car;
      for($i=0;$i<count($Data_Car);$i++){
         $vCarType = $Data_Car[$i]['vCarType']; 
         $sql = "SELECT iVehicleTypeId,eType  FROM `vehicle_type` WHERE `iVehicleTypeId` IN ($vCarType)";  
         $db_cartype = $obj->MySQLSelect($sql);
         $k=0;   
         if (count($db_cartype) > 0) {
            for($j=0;$j<count($db_cartype);$j++){
              $eType = $db_cartype[$j]['eType'];
               if($eType == "UberX"){
                   //unset($db_vehicle_new[$i]); 
		           }
            }
         }
      }
      $db_vehicle_new = array_values($db_vehicle_new); 
			
			// echo json_encode($returnArr);
			$returnArr['Action']="1";
			$returnArr['message']=$db_vehicle_new;
			echo json_encode($returnArr);
		}
		else
		{
			$sql = "SELECT count(iDriverVehicleId) as TotalVehicles from driver_vehicle WHERE iDriverId = '".$driverId."' AND ( eStatus = 'Inactive' OR eStatus = 'Deleted')";
		  $db_Total_vehicle = $obj->MySQLSelect($sql);
      $TotalVehicles = $db_Total_vehicle[0]['TotalVehicles'];   
			$returnArr['Action']="0";
			if($TotalVehicles == 0){
			$returnArr['message']="LBL_NO_CAR_AVAIL_TXT";
      }else{
        $returnArr['message']="LBL_INACTIVE_CARS_MESSAGE_TXT";
      }
			echo json_encode($returnArr);
			exit;
		}
	}
	
	########################### Set Driver CarID ############################
	if ($type == "SetDriverCarID") {
		
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$Data['iDriverVehicleId'] = isset($_REQUEST["iDriverVehicleId"]) ? $_REQUEST["iDriverVehicleId"] : '';
		
		$where = " iDriverId = '".$iDriverId."'";
		
		$sql = $obj->MySQLQueryPerform("register_driver",$Data,'update',$where);
		if ($sql > 0) {
			$returnArr['Action']="1";
			echo json_encode($returnArr);
		}
		else
		{
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
			echo json_encode($returnArr);
		}
	}
	
	###########################################################################
	
	if($type == "GenerateTrip"){
		$passenger_id =  isset($_REQUEST["PassengerID"]) ? $_REQUEST["PassengerID"] : '';
		$driver_id      =  isset($_REQUEST["DriverID"]) ? $_REQUEST["DriverID"] : '';
		$iCabRequestId      =  isset($_REQUEST["iCabRequestId"]) ? $_REQUEST["iCabRequestId"] : '';
		$Source_point_latitude=  isset($_REQUEST["start_lat"]) ? $_REQUEST["start_lat"] : '';
		$Source_point_longitude=  isset($_REQUEST["start_lon"]) ? $_REQUEST["start_lon"] : '';
		$Source_point_Address= isset($_REQUEST["sAddress"]) ? $_REQUEST["sAddress"] : '';
		$GoogleServerKey= isset($_REQUEST["GoogleServerKey"]) ? $_REQUEST["GoogleServerKey"] : '';
		$iCabBookingId= isset($_REQUEST["iCabBookingId"]) ? $_REQUEST["iCabBookingId"] : '';
		$vMsgCode= isset($_REQUEST["vMsgCode"]) ? $_REQUEST["vMsgCode"] : '';
		$setCron= isset($_REQUEST["setCron"]) ? $_REQUEST["setCron"] : 'No';
		//$APP_TYPE = $generalobj->getConfigurations("configurations","APP_TYPE");
    
    $sqldata = "SELECT iTripId FROM `trips` WHERE iActive='On Going Trip'  AND iDriverId='" .$driver_id. "'";
		$TripData = $obj->MySQLSelect($sqldata);
    if(count($TripData) > 0){
       $returnArr['Action'] = "0";
	   $returnArr['message'] = "LBL_DRIVER_NOT_ACCEPT_TRIP";
       echo json_encode($returnArr);exit;
    }
	#### Update Driver Request Status of Trip ####
	$iTripId = !empty($iTripId) ? $iTripId : '0'  ;

	// $returnArr['iTripId'] = $iTripId;
	// echo json_encode($returnArr);exit;
		UpdateDriverRequest2($driver_id,$passenger_id,$iTripId,"",$vMsgCode,"Yes");
		#### Update Driver Request Status of Trip ####
		
		
		if($iCabBookingId !=""){
			$bookingData = get_value('cab_booking', 'iUserId,vSourceLatitude,vSourceLongitude,vSourceAddresss,eType,dBooking_date', 'iCabBookingId', $iCabBookingId);
			$passenger_id = $bookingData[0]['iUserId'];
			$Source_point_latitude = $bookingData[0]['vSourceLatitude'];
			$Source_point_longitude = $bookingData[0]['vSourceLongitude'];
			$Source_point_Address = $bookingData[0]['vSourceAddresss'];
			$eType_cabbooking = $bookingData[0]['eType'];
      ## Check Timing For Later Booking ##
      $additional_mins = $BOOKING_LATER_ACCEPT_BEFORE_INTERVAL;  
      $additional_mins_into_secs = $additional_mins * 60;
      $dBooking_date = $bookingData[0]['dBooking_date']; 
      $currDate = date('Y-m-d H:i:s');
      //$currDate = date("Y-m-d H:i:s", strtotime($currDate . "-".$additional_mins." minutes"));
      $datediff = abs(strtotime($dBooking_date)-strtotime($currDate));
      if($datediff > $additional_mins_into_secs){
        $vDriverLangCode=get_value('register_driver', 'vLang', 'iDriverId',$driver_id,'','true');
        $mins = get_value('language_label', 'vValue', 'vLabel', 'LBL_MINUTES_TXT'," and vCode='".$vDriverLangCode."'",'true');
        $hrs = get_value('language_label', 'vValue', 'vLabel', 'LBL_HOURS_TXT'," and vCode='".$vDriverLangCode."'",'true'); 
        $LBL_RIDE_LATER_START_VALIDATION_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_RIDE_LATER_START_VALIDATION_TXT'," and vCode='".$vDriverLangCode."'",'true');
        if($additional_mins <= 60){
          $beforetext = $additional_mins." ".$mins; 
          $message = str_replace('####', $beforetext, $LBL_RIDE_LATER_START_VALIDATION_TXT);
        }else{
          $hours = floor($additional_mins / 60); 
          $beforetext = $hours." ".$hrs;
          $message = str_replace('####', $beforetext, $LBL_RIDE_LATER_START_VALIDATION_TXT);
        }
        $returnArr['Action'] = "0";
				$returnArr['message']=$message;
				echo json_encode($returnArr);
				exit;
      }
      ## Check Timing For Later Booking ##
		}
		
		$DriverMessage= "CabRequestAccepted";
		
		$TripRideNO = rand ( 10000000 , 99999999 );
        $TripVerificationCode = rand ( 1000 , 9999 );
		$Active= "Active";
		
		$vLangCode=get_value('register_user', 'vLang', 'iUserId',$passenger_id,'','true');
		if($vLangCode == "" || $vLangCode == NULL){
			$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		$vGMapLangCode=get_value('language_master', 'vGMapLangCode', 'vCode',$vLangCode,'','true');
		
		$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
		$tripdriverarrivlbl = $languageLabelsArr['LBL_DRIVER_ARRIVING'];
		
		/* if($Source_point_Address == ""){
			$url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=".$Source_point_latitude.",".$Source_point_longitude."&key=".$GoogleServerKey."&language=".$vGMapLangCode;
			
			try {
				
				$jsonfile = file_get_contents($url);
				$jsondata = json_decode($jsonfile);
				$source_address=$jsondata->results[0]->formatted_address;
				
				$Source_point_Address = $source_address ;
				
				} catch (ErrorException $ex) {
				
				$returnArr['Action'] = "0";
				$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
				echo json_encode($returnArr);
				exit;
			}
		}
		
		if($Source_point_Address == ""){
            $returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
            echo json_encode($returnArr);
            exit;
		} */
		
		$reqestId ="";
		$trip_status_chkField = "iCabRequestId";
		if($iCabRequestId != ""){
		$sql = "SELECT eStatus,ePayType,iVehicleTypeId,iCabBookingId,vSourceLatitude,vSourceLongitude,tSourceAddress,vDestLatitude,vDestLongitude,tDestAddress,vCouponCode,eType,iPackageTypeId,vReceiverName,vReceiverMobile,tPickUpIns,tDeliveryIns,tPackageDetails,fPickUpPrice,fNightPrice,iQty,vRideCountry,fTollPrice,vTollPriceCurrencyCode,eTollSkipped,vTimeZone,iUserAddressId,tUserComment,eFlatTrip,fFlatTripPrice FROM cab_request_now WHERE iUserId='$passenger_id' and iCabRequestId = '$iCabRequestId'";
		$check_row = $obj->MySQLSelect($sql);
		
		$eStatus = $check_row[0]['eStatus'];
		$eType = $check_row[0]['eType'];
		
		if($eType_cabbooking != ""){
			$eType = $eType_cabbooking;
			}else{
			$eType = $check_row[0]['eType'];
		}
			$reqestId = $iCabRequestId;
			$trip_status_chkField = "iCabRequestId";
		}else{
			$sql="select eStatus,eType from cab_booking where iCabBookingId = '$iCabBookingId'";
			$cab_data = $obj->MySQLSelect($sql);
			$eStatus = $cab_data[0]['eStatus'];
			$eType = $cab_data[0]['eType'];
			$reqestId = $iCabBookingId;
			$trip_status_chkField = "iCabBookingId";
		}
		
		if($eStatus == "Completed"){
				$returnArr['Action'] = "0";
				$returnArr['message']="LBL_FAIL_ASSIGN_TO_PASSENGER_TXT";
				echo json_encode($returnArr);
				exit;
		}else{
			if($APP_TYPE != "UberX"){ 	
				$sql="select iTripId,vTripStatus from register_user where iUserId='$passenger_id'";
				$user_data = $obj->MySQLSelect($sql);
				$iTripId = $user_data[0]['iTripId'];
				if($iTripId != "" && $iTripId != 0){
					$status_trip = get_value("trips", 'iActive', "iTripId",$iTripId,'','true');
					$cab_id = get_value("trips", $trip_status_chkField, "iTripId",$iTripId,'','true');
					if($status_trip == "Active" || $status_trip == "On Going Trip"){
						if($reqestId == $cab_id){
							$returnArr['Action'] = "0";
							$returnArr['message']="LBL_FAIL_ASSIGN_TO_PASSENGER_TXT";
							echo json_encode($returnArr);
							exit;
						}else{
							$returnArr['Action'] = "0";
							$returnArr['message']="LBL_USER_ON_ANOTHER_TRIP";
							echo json_encode($returnArr);
							exit;
						}
					}
				}
			}
			}   
		if ($eStatus == "Requesting" || (($eStatus == "Assign" || $eStatus == "Accepted") && $iCabBookingId != "" && $iCabRequestId == "")) {   
			
			// $eStatus      = $check_row[0]['eStatus'];
			
			// if ($eStatus == "Requesting") {
			if($iCabRequestId != ""){	
				$where = " iCabRequestId = '$iCabRequestId'";
				
				$Data_update_cab_request['eStatus']='Complete';
				
				$obj->MySQLQueryPerform("cab_request_now",$Data_update_cab_request,'update',$where);
			}  
			$sql = "SELECT vCurrencyPassenger,iAppVersion,iUserPetId FROM `register_user` WHERE iUserId = '$passenger_id'";
			$Data_passenger_detail = $obj->MySQLSelect($sql);
			
			$sql = "SELECT iDriverVehicleId,vCurrencyDriver,iAppVersion,vName,vLastName FROM `register_driver` WHERE iDriverId = '$driver_id'";
			$Data_vehicle = $obj->MySQLSelect($sql);
			
			$CAR_id_driver     = $Data_vehicle[0]['iDriverVehicleId'];
			
			if($iCabBookingId !=""){
				$sql_booking = "SELECT vSourceLatitude, vSourceLongitude,vSourceAddresss,vDestLatitude,vDestLongitude,tDestAddress,ePayType,iVehicleTypeId,eType,iPackageTypeId,vReceiverName,vReceiverMobile,tPickUpIns,tDeliveryIns,tPackageDetails,fPickUpPrice,fNightPrice,iUserPetId,vCouponCode,iQty,vRideCountry,fTollPrice,vTollPriceCurrencyCode,eTollSkipped, vTimeZone,iUserAddressId,tUserComment,eFlatTrip,fFlatTripPrice FROM cab_booking WHERE iCabBookingId='$iCabBookingId'";
				
				$data_booking = $obj->MySQLSelect($sql_booking);
				
				$iSelectedCarType = $data_booking[0]['iVehicleTypeId'];
				$vTripPaymentMode = $data_booking[0]['ePayType'];
				$tDestinationLatitude = $data_booking[0]['vDestLatitude'];
				$tDestinationLongitude = $data_booking[0]['vDestLongitude'];
				$tDestinationAddress = $data_booking[0]['tDestAddress'];
				$fPickUpPrice = $data_booking[0]['fPickUpPrice'];
				$fNightPrice = $data_booking[0]['fNightPrice'];
				$Source_point_latitude = $data_booking[0]['vSourceLatitude'];
				$Source_point_longitude = $data_booking[0]['vSourceLongitude'];
				$Source_point_Address = $data_booking[0]['vSourceAddresss'];
				
				$eType = $data_booking[0]['eType'];
				$iPackageTypeId = $data_booking[0]['iPackageTypeId'];
				$vReceiverName = $data_booking[0]['vReceiverName'];
				$vReceiverMobile = $data_booking[0]['vReceiverMobile'];
				$tPickUpIns = $data_booking[0]['tPickUpIns'];
				$tDeliveryIns = $data_booking[0]['tDeliveryIns'];
				$tPackageDetails = $data_booking[0]['tPackageDetails'];
				$iUserPetId = $data_booking[0]['iUserPetId'];
				$vCouponCode = $data_booking[0]['vCouponCode'];
				$iQty = $data_booking[0]['iQty'];
				$vRideCountry = $data_booking[0]['vRideCountry'];
				$fTollPrice = $data_booking[0]['fTollPrice'];
				$vTollPriceCurrencyCode = $data_booking[0]['vTollPriceCurrencyCode'];
				$eTollSkipped = $data_booking[0]['eTollSkipped'];
				$vTimeZone = $data_booking[0]['vTimeZone'];
				$iUserAddressId = $data_booking[0]['iUserAddressId'];
				$tUserComment = $data_booking[0]['tUserComment'];
        $eFlatTrip = $data_booking[0]['eFlatTrip'];
        $fFlatTripPrice = $data_booking[0]['fFlatTripPrice'];
				}else{
				$iSelectedCarType = $check_row[0]['iVehicleTypeId'];
				$vTripPaymentMode = $check_row[0]['ePayType'];
				$tDestinationLatitude = $check_row[0]['vDestLatitude'];
				$tDestinationLongitude = $check_row[0]['vDestLongitude'];
				$tDestinationAddress = $check_row[0]['tDestAddress'];
				$fPickUpPrice = $check_row[0]['fPickUpPrice'];
				$fNightPrice = $check_row[0]['fNightPrice'];
				$Source_point_latitude = $check_row[0]['vSourceLatitude'];
				$Source_point_longitude = $check_row[0]['vSourceLongitude'];
				$Source_point_Address = $check_row[0]['tSourceAddress'];
				
				$eType = $check_row[0]['eType'];
				$iPackageTypeId = $check_row[0]['iPackageTypeId'];
				$vReceiverName = $check_row[0]['vReceiverName'];
				$vReceiverMobile = $check_row[0]['vReceiverMobile'];
				$tPickUpIns = $check_row[0]['tPickUpIns'];
				$tDeliveryIns = $check_row[0]['tDeliveryIns'];
				$tPackageDetails = $check_row[0]['tPackageDetails'];
				$iUserPetId = $Data_passenger_detail[0]['iUserPetId'];
				$vCouponCode = $check_row[0]['vCouponCode'];
				$iQty = $check_row[0]['iQty'];
        $eFlatTrip = $check_row[0]['eFlatTrip'];
        $fFlatTripPrice = $check_row[0]['fFlatTripPrice'];
				$vRideCountry = $check_row[0]['vRideCountry'];
				$fTollPrice = $check_row[0]['fTollPrice'];
				$vTollPriceCurrencyCode = $check_row[0]['vTollPriceCurrencyCode'];
				$eTollSkipped = $check_row[0]['eTollSkipped'];
				$vTimeZone = $check_row[0]['vTimeZone'];
				$iUserAddressId = $check_row[0]['iUserAddressId'];
				$tUserComment = $check_row[0]['tUserComment'];
				$iCabBookingId = $check_row[0]['iCabBookingId'];
			}
			
			/* 	if($vRideCountry != "") {
				$newTimeZone = get_value('country', 'vTimeZone', 'LOWER(vCountry)', strtolower($vRideCountry),'',true);
				//$newTimeZone = $
				@date_default_timezone_set($newTimeZone);
				}
			*/	
			$Data_trips['vRideNo']=$TripRideNO;
			$Data_trips['iUserId']=$passenger_id;
			$Data_trips['iDriverId']=$driver_id;
			$Data_trips['tTripRequestDate']=@date("Y-m-d H:i:s");
			$Data_trips['tStartLat']=$Source_point_latitude;
			$Data_trips['tStartLong']=$Source_point_longitude;
			$Data_trips['tSaddress']=$Source_point_Address;
			$Data_trips['iActive']=$Active;
			$Data_trips['iDriverVehicleId']=$CAR_id_driver;
			$Data_trips['iVerificationCode']=$TripVerificationCode;
			$Data_trips['iVehicleTypeId']=$iSelectedCarType;
			/*$Data_trips['eFareType'] = get_value('vehicle_type', 'eFareType', 'iVehicleTypeId', $iSelectedCarType,'','true');
			$Data_trips['fVisitFee'] = get_value('vehicle_type', 'fVisitFee', 'iVehicleTypeId', $iSelectedCarType,'','true');  */
      $VehicleData = get_value('vehicle_type', 'eFareType,fVisitFee,eIconType', 'iVehicleTypeId', $iSelectedCarType);
      $Data_trips['eFareType'] = $VehicleData[0]['eFareType'];
      $Data_trips['fVisitFee'] = $VehicleData[0]['fVisitFee']; 
			
			$Data_trips['vTripPaymentMode']=$vTripPaymentMode;
			$Data_trips['tEndLat']=$tDestinationLatitude;
			$Data_trips['tEndLong']=$tDestinationLongitude;
			$Data_trips['tDaddress']=$tDestinationAddress;
			$Data_trips['fPickUpPrice']=$fPickUpPrice;
			$Data_trips['fNightPrice']=$fNightPrice;
			$Data_trips['iQty']=$iQty;
			
			$Data_trips['eType']=$eType;
			$Data_trips['iPackageTypeId']=$iPackageTypeId;
			$Data_trips['vReceiverName']=$vReceiverName;
			$Data_trips['vReceiverMobile']=$vReceiverMobile;
			$Data_trips['tPickUpIns']=$tPickUpIns;
			$Data_trips['tDeliveryIns']=$tDeliveryIns;
			$Data_trips['tPackageDetails']=$tPackageDetails;
			$Data_trips['iUserPetId']=$iUserPetId;
			$Data_trips['vCountryUnitRider']=getMemberCountryUnit($passenger_id,"Passenger");
			$Data_trips['vCountryUnitDriver']=getMemberCountryUnit($driver_id,"Driver");
			$Data_trips['fTollPrice']=$fTollPrice;
			$Data_trips['vTollPriceCurrencyCode']=$vTollPriceCurrencyCode;
			$Data_trips['eTollSkipped']=$eTollSkipped;
			$Data_trips['vTimeZone']=$vTimeZone;
			$Data_trips['iUserAddressId']=$iUserAddressId;
			$Data_trips['tUserComment']=$tUserComment;
			$Data_trips['iCabBookingId']=$iCabBookingId;
			$Data_trips['iCabRequestId']=$iCabRequestId;
      $Data_trips['eFlatTrip']=$eFlatTrip;
      $Data_trips['fFlatTripPrice']=$fFlatTripPrice;
      //$eIconType = get_value('vehicle_type', 'eIconType', 'iVehicleTypeId', $iSelectedCarType,'','true');
      $eIconType = $VehicleData[0]['eIconType'];
			if($APP_TYPE == "UberX"){
				$iVehicleCategoryId = get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId', $iSelectedCarType,'','true');
				$imageuploaddata = get_value('vehicle_category', 'eBeforeUpload, eAfterUpload', 'iVehicleCategoryId', $iVehicleCategoryId);
				$Data_trips['eBeforeUpload']=$imageuploaddata[0]['eBeforeUpload'];
				$Data_trips['eAfterUpload']=$imageuploaddata[0]['eAfterUpload']; 
			}
			
			if($vCouponCode != ''){
				$Data_trips['vCouponCode']=$vCouponCode;
				
				$noOfCouponUsed = get_value('coupon', 'iUsed', 'vCouponCode', $vCouponCode,'','true');
				$where = " vCouponCode = '".$vCouponCode."'";
				$data_coupon['iUsed']=$noOfCouponUsed + 1;
				$obj->MySQLQueryPerform("coupon",$data_coupon,'update',$where);
			}
			
			$currencyList = get_value('currency', '*', 'eStatus', 'Active');
			
			for($i=0;$i<count($currencyList);$i++){
				$currencyCode = $currencyList[$i]['vName'];
				$Data_trips['fRatio_'.$currencyCode] = $currencyList[$i]['Ratio'];
			}
			
			$Data_trips['vCurrencyPassenger']=$Data_passenger_detail[0]['vCurrencyPassenger'];
			$Data_trips['vCurrencyDriver']=$Data_vehicle[0]['vCurrencyDriver'];
			// $Data_trips['fRatioPassenger']=($obj->MySQLSelect("SELECT Ratio FROM currency WHERE vName='".$check_row[0]['vCurrencyPassenger']."' ")[0]['Ratio']);
			$Data_trips['fRatioPassenger']=get_value('currency', 'Ratio', 'vName', $Data_passenger_detail[0]['vCurrencyPassenger'],'','true');
			// $Data_trips['fRatioDriver']=($obj->MySQLSelect("SELECT Ratio FROM currency WHERE vName='".$Data_vehicle[0]['vCurrencyDriver']."' ")[0]['Ratio']);
			$Data_trips['fRatioDriver']=get_value('currency', 'Ratio', 'vName', $Data_vehicle[0]['vCurrencyDriver'],'','true');
			
			$id = $obj->MySQLQueryPerform("trips",$Data_trips,'insert');
			$iTripId  = $id;
			$trip_status = "Active";
			
			if($iCabRequestId != ""){	
				$where1 = " iCabRequestId = '$iCabRequestId'";
				$Data_update_cab_request['iTripId']=$iTripId;
				$Data_update_cab_request['iDriverId']=$driver_id;
				$obj->MySQLQueryPerform("cab_request_now",$Data_update_cab_request,'update',$where1);
			}  
			#### Update Driver Request Status of Trip ####
			UpdateDriverRequest2($driver_id,$passenger_id,$iTripId,"Accept",$vMsgCode,"No");
			#### Update Driver Request Status of Trip ####
			
			if($iCabBookingId > 0){
				$where = " iCabBookingId = '$iCabBookingId'";
				$data_update_booking['iTripId']=$iTripId;
				$data_update_booking['eStatus']="Completed";
				$data_update_booking['iDriverId']=$driver_id;
				$obj->MySQLQueryPerform("cab_booking",$data_update_booking,'update',$where);
			}
			
			$where = " iUserId = '$passenger_id'";
			$Data_update_passenger['iTripId']=$iTripId;
			$Data_update_passenger['vTripStatus']=$trip_status;
			$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
			
			$where = " iDriverId = '$driver_id'";
			$Data_update_driver['iTripId']=$iTripId;
			$Data_update_driver['vTripStatus']=$trip_status;
			$Data_update_driver['vRideCountry']=$vRideCountry;
			$Data_update_driver['vAvailability']="Not Available";
			$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
			
			if($eType == "Deliver"){
				$drivername = $Data_vehicle[0]['vName']." ".$Data_vehicle[0]['vLastName'];
				$tripdriverarrivlbl = $languageLabelsArr['LBL_DELIVERY_DRIVER_TXT']." ".$drivername." ".$languageLabelsArr['LBL_DRIVER_IS_ARRIVING']; 
			}
			$alertMsg = $tripdriverarrivlbl;
			$message_arr = array();
			$message_arr['iDriverId'] = $driver_id;
			$message_arr['Message'] = $DriverMessage;
			$message_arr['iTripId'] = strval($iTripId);
			$message_arr['DriverAppVersion'] = strval($Data_vehicle[0]['iAppVersion']);
			if($iCabBookingId > 0){
				$message_arr['iCabBookingId'] = $iCabBookingId;
				$message_arr['iBookingId'] = $iCabBookingId;
			}
			$message_arr['eType'] = $eType;
			$message_arr['iTripVerificationCode'] = $TripVerificationCode;
			$message_arr['driverName'] = $Data_vehicle[0]['vName']." ".$Data_vehicle[0]['vLastName'];
			$message_arr['vRideNo'] = $TripRideNO;
			$message_arr['vTitle'] = $alertMsg;
			
			$message = json_encode($message_arr);
			
			#####################Add Status Message#########################
			$DataTripMessages['tMessage']= $message;
			$DataTripMessages['iDriverId']= $driver_id;
			$DataTripMessages['iTripId']= $iTripId;
			$DataTripMessages['iUserId']= $passenger_id;
			$DataTripMessages['eFromUserType']= "Driver";
			$DataTripMessages['eToUserType']= "Passenger";
			$DataTripMessages['eReceived']= "No";
			$DataTripMessages['dAddedDate']= @date("Y-m-d H:i:s");
			
			$obj->MySQLQueryPerform("trip_status_messages",$DataTripMessages,'insert');
			################################################################
			
			if($setCron == 'Yes'){
				$passengerDetail = get_value('register_user', 'vName,vLastName,vPhone,vPhoneCode', 'iUserId', $passenger_id);
				$passengerName = $passengerDetail[0]['vName'].' '.$passengerDetail[0]['vLastName'];
				$vPhoneCode = $passengerDetail[0]['vPhoneCode'];
				$vPhone = $passengerDetail[0]['vPhone'];
				$driverName = $Data_vehicle[0]['vName'].' '.$Data_vehicle[0]['vLastName'];
				$messageEmail['details'] = '<p>Dear Administrator,</p>
				<p>Driver ( '.$driverName.' ) is assigned successfully for the following manual booking.</p>
				<p>Name: '.$passengerName.',</p>
				<p>Contact Number: +'.$vPhoneCode.$vPhone.'</p>';
				$mail = $generalobj->send_email_user('CRON_BOOKING_EMAIL',$messageEmail);
				$where_cabid2 = " iCabBookingId = '".$iCabBookingId."'";
				$Data_update2['eAssigned']= 'Yes';
				$Data_update2['iDriverId']= $driver_id;
				$id = $obj->MySQLQueryPerform("cab_booking",$Data_update2,'update',$where_cabid2); 
			}
			
			if ($iTripId > 0) {
				/*$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");
					$PUBNUB_DISABLED = $generalobj->getConfigurations("configurations","PUBNUB_DISABLED");
					$PUBNUB_PUBLISH_KEY = $generalobj->getConfigurations("configurations","PUBNUB_PUBLISH_KEY");
				$PUBNUB_SUBSCRIBE_KEY = $generalobj->getConfigurations("configurations","PUBNUB_SUBSCRIBE_KEY");*/
				if($PUBNUB_DISABLED == "Yes"){
					$ENABLE_PUBNUB = "No";
				}
				
				$alertSendAllowed = true;
				
				/* For PubNub Setting */
				$tableName = "register_user";
				$iMemberId_VALUE = $passenger_id;
				$iMemberId_KEY = "iUserId";
				/*$iAppVersion=get_value($tableName, 'iAppVersion', $iMemberId_KEY,$iMemberId_VALUE,'','true');
				$eDeviceType=get_value($tableName, 'eDeviceType', $iMemberId_KEY,$iMemberId_VALUE,'','true');*/
        $AppData = get_value($tableName, 'iAppVersion,eDeviceType', $iMemberId_KEY, $iMemberId_VALUE);
        $iAppVersion = $AppData[0]['iAppVersion'];
        $eDeviceType = $AppData[0]['eDeviceType'];
				/* For PubNub Setting Finished */
				
				$sql = "SELECT iGcmRegId,eDeviceType FROM register_user WHERE iUserId='$passenger_id'";
				$result = $obj->MySQLSelect($sql);
				$registatoin_ids = $result[0]['iGcmRegId'];
				$deviceTokens_arr_ios = array();
				$registation_ids_new = array();
				
				if($ENABLE_PUBNUB == "Yes"  && $PUBNUB_PUBLISH_KEY != "" && $PUBNUB_SUBSCRIBE_KEY != ""){
					//$pubnub = new Pubnub\Pubnub($PUBNUB_PUBLISH_KEY, $PUBNUB_SUBSCRIBE_KEY);
					$pubnub = new Pubnub\Pubnub(array("publish_key" => $PUBNUB_PUBLISH_KEY,"subscribe_key" => $PUBNUB_SUBSCRIBE_KEY, "uuid" => $uuid));
					$channelName = "PASSENGER_".$passenger_id;
					
					$tSessionId=get_value("register_user", 'tSessionId', "iUserId",$passenger_id,'','true');
					$message_arr['tSessionId'] = $tSessionId;
					$message_pub = json_encode($message_arr,JSON_UNESCAPED_UNICODE);
					
					$info = $pubnub->publish($channelName, $message_pub);
					if($result[0]['eDeviceType'] != "Android"){
						//$alertMsg = "Driver is arriving";
						//$alertMsg = $tripdriverarrivlbl;
						array_push($deviceTokens_arr_ios, $result[0]['iGcmRegId']);	
						// sendApplePushNotification(0,$deviceTokens_arr_ios,"",$alertMsg,0);
					}	
					}else{
					$alertSendAllowed = true;
				}
				if($alertSendAllowed == true){
					if($result[0]['eDeviceType'] == "Android"){
						array_push($registation_ids_new, $result[0]['iGcmRegId']);
						$Rmessage         = array("message" => $message);
						$result = send_notification($registation_ids_new, $Rmessage,0);	
						}else{
						//$alertMsg = "Driver is arriving";
						//$alertMsg = $tripdriverarrivlbl;
						array_push($deviceTokens_arr_ios, $result[0]['iGcmRegId']);
						sendApplePushNotification(0,$deviceTokens_arr_ios,$message,$alertMsg,0);
					}
				}
				
				$returnArr['Action'] = "1";
				$data['iTripId'] = $iTripId;
				$data['tEndLat'] = $tDestinationLatitude;
				$data['tEndLong'] = $tDestinationLongitude;
				$data['tDaddress'] = $tDestinationAddress;
				$data['PAppVersion'] = $Data_passenger_detail[0]['iAppVersion'];
				$data['eFareType'] = $Data_trips['eFareType'];
				$data['vVehicleType'] = $eIconType;
				//$returnArr['APP_TYPE'] = $generalobj->getConfigurations("configurations","APP_TYPE");
				$returnArr['APP_TYPE'] = $APP_TYPE;
				$returnArr['message']=$data;
				
				if($iCabBookingId !=""){
					$passengerData = get_value('register_user', 'vName,vLastName,vImgName,vFbId,vAvgRating,vPhone,vPhoneCode,iAppVersion', 'iUserId', $passenger_id);
					$returnArr['sourceLatitude'] = $Source_point_latitude;
					$returnArr['sourceLongitude'] = $Source_point_longitude;
					$returnArr['PassengerId'] = $passenger_id;
					$returnArr['PName'] = $passengerData[0]['vName'].' '.$passengerData[0]['vLastName'];
					$returnArr['PPicName'] = $passengerData[0]['vImgName'];
					$returnArr['PFId'] = $passengerData[0]['vFbId'];
					$returnArr['PRating'] = $passengerData[0]['vAvgRating'];
					$returnArr['PPhone'] = $passengerData[0]['vPhone'];
					$returnArr['PPhoneC'] = $passengerData[0]['vPhoneCode'];
					$returnArr['PAppVersion'] = $passengerData[0]['iAppVersion'];
					$returnArr['TripId'] = strval($iTripId);
					$returnArr['DestLocLatitude'] = $tDestinationLatitude;
					$returnArr['DestLocLongitude'] = $tDestinationLongitude;
					$returnArr['DestLocAddress'] = $tDestinationAddress;
					$returnArr['vVehicleType'] = $eIconType;
				}
				echo json_encode($returnArr); exit;
				} else {
				$data['Action'] = "0";
				$data['message']="LBL_TRY_AGAIN_LATER_TXT";
				echo json_encode($data);
				exit;
			}
			
			/* }else{
				$returnArr['Action'] = "0";
				$returnArr['message']="LBL_CAR_REQUEST_CANCELLED_TXT";
				echo json_encode($returnArr);
			} */
		}
		else{
			if($eStatus == "Complete"){
				$returnArr['Action'] = "0";
				$returnArr['message']="LBL_FAIL_ASSIGN_TO_PASSENGER_TXT";
				}else if($eStatus == "Cancelled"){
				$returnArr['Action'] = "0";
				$returnArr['message']="LBL_CAR_REQUEST_CANCELLED_TXT";
			}
            echo json_encode($returnArr);
		}
	}
	
	###########################################################################
	
	if($type == "DriverArrived"){
		$iDriverId = isset($_REQUEST["iDriverId"])?$_REQUEST["iDriverId"]:'';
		
		if($iDriverId != '') {
			
			$vTripStatus=get_value('register_driver', 'vTripStatus', 'iDriverId',$iDriverId,'','true');
			if($vTripStatus == "Cancelled"){
				$returnArr['Action'] = "0";
				$returnArr['message']="DO_RESTART";
				echo json_encode($returnArr);exit;
			}
			
			$where = " iDriverId = '$iDriverId'";
			
			$Data_update_driver['vTripStatus']='Arrived';
			
			$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
			
			if($id>0){
				
				$sql = "SELECT CONCAT(rd.vName,' ',rd.vLastName) AS driverName, tr.vRideNo, tr.tEndLat,tr.tEndLong,tr.tDaddress,tr.iUserId,tr.eType,rd.iTripId,tr.eTollSkipped,tr.eBeforeUpload,tr.eAfterUpload FROM trips as tr,register_driver as rd WHERE tr.iTripId=rd.iTripId AND rd.iDriverId = '".$iDriverId."'";
				$result = $obj->MySQLSelect($sql);
				
				// echo "<pre>"; print_r($result);  die;
				
				$returnArr['Action'] = "1";
				
				if($result[0]['iTripId'] != "") {
					// Update Trip Table
					$where1 = " iTripId = '".$result[0]['iTripId']."'";
					$Data_update_trips['tDriverArrivedDate']=date('Y-m-d H:i:s');
					$id = $obj->MySQLQueryPerform("trips",$Data_update_trips,'update',$where1);
				}
				
				if($result[0]['tEndLat'] != '' && $result[0]['tEndLong'] != ''){
					$data['DLatitude'] = $result[0]['tEndLat'];
					$data['DLongitude']=$result[0]['tEndLong'];
					$data['DAddress']=$result[0]['tDaddress'];
					}else{
					$data['DLatitude'] = "0";
					$data['DLongitude']="0";
					$data['DAddress']= "0";
				}
				$data['eTollSkipped']=$result[0]['eTollSkipped'];
				$data['eBeforeUpload']=$result[0]['eBeforeUpload'];
				$data['eAfterUpload']=$result[0]['eAfterUpload'];
				$returnArr['message'] = $data;
				// echo "UpdateSuccess";
				
				/*$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");
					$PUBNUB_DISABLED = $generalobj->getConfigurations("configurations","PUBNUB_DISABLED");
					$PUBNUB_PUBLISH_KEY = $generalobj->getConfigurations("configurations","PUBNUB_PUBLISH_KEY");
				$PUBNUB_SUBSCRIBE_KEY = $generalobj->getConfigurations("configurations","PUBNUB_SUBSCRIBE_KEY");*/
				if($PUBNUB_DISABLED == "Yes"){
					$ENABLE_PUBNUB = "No";
				}
				
				
				/* For PubNub Setting */
				$tableName = "register_user";
				$iMemberId_VALUE = $result[0]['iUserId'];
				$iMemberId_KEY = "iUserId";
				/*$iAppVersion=get_value($tableName, 'iAppVersion', $iMemberId_KEY,$iMemberId_VALUE,'','true');
				$eDeviceType=get_value($tableName, 'eDeviceType', $iMemberId_KEY,$iMemberId_VALUE,'','true');
				$iGcmRegId=get_value($tableName, 'iGcmRegId', $iMemberId_KEY,$iMemberId_VALUE,'','true');
				$vLangCode=get_value($tableName, 'vLang', $iMemberId_KEY,$iMemberId_VALUE,'','true');*/
        $AppData = get_value($tableName, 'iAppVersion,eDeviceType,iGcmRegId,vLang', $iMemberId_KEY, $iMemberId_VALUE);
        $iAppVersion = $AppData[0]['iAppVersion'];
        $eDeviceType = $AppData[0]['eDeviceType'];
        $iGcmRegId = $AppData[0]['iGcmRegId'];
        $vLangCode = $AppData[0]['vLang']; 
				/* For PubNub Setting Finished */
				
				if($vLangCode == "" || $vLangCode == NULL){
					$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
				}
				
				$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
				$driverArrivedLblValue = $languageLabelsArr['LBL_DRIVER_ARRIVED_NOTIMSG'];
				$driverArrivedLblValue_ride = $languageLabelsArr['LBL_DRIVER_ARRIVED_TXT'];
				
				$deviceTokens_arr_ios = array();
				$registation_ids_new = array();
				$message = "";
				
				$message_arr['Message'] = "DriverArrived";
				$message_arr['MsgType'] = "DriverArrived";
				$message_arr['iDriverId'] = $iDriverId;
				$message_arr['driverName'] = $result[0]['driverName'];
				$message_arr['vRideNo'] = $result[0]['vRideNo'];
				$message_arr['iTripId'] = $result[0]['iTripId'];
				$message_arr['eType'] = $result[0]['eType'];
				$eType = $result[0]['eType'];
				if($eType == "UberX" || $eType == "Deliver"){
					$alertMsg = $languageLabelsArr['LBL_DELIVERY_DRIVER_TXT'].' '.$result[0]['driverName'].' '.$driverArrivedLblValue.$result[0]['vRideNo'];
				}else{
					$alertMsg = $driverArrivedLblValue_ride;
				}
				$message_arr['vTitle'] = $alertMsg;
				$message = json_encode($message_arr);
				
				$alertSendAllowed = true;
				
				if($ENABLE_PUBNUB == "Yes"  && $PUBNUB_PUBLISH_KEY != "" && $PUBNUB_SUBSCRIBE_KEY != ""/*  && $iAppVersion > 1 && $eDeviceType == "Android" */){
					//$pubnub = new Pubnub\Pubnub($PUBNUB_PUBLISH_KEY, $PUBNUB_SUBSCRIBE_KEY);
					$pubnub = new Pubnub\Pubnub(array("publish_key" => $PUBNUB_PUBLISH_KEY,"subscribe_key" => $PUBNUB_SUBSCRIBE_KEY, "uuid" => $uuid));
					$channelName = "PASSENGER_".$result[0]['iUserId'];
					
					$tSessionId=get_value("register_user", 'tSessionId', "iUserId",$result[0]['iUserId'],'','true');
					$message_arr['tSessionId'] = $tSessionId;
					$message_pub = json_encode($message_arr,JSON_UNESCAPED_UNICODE);
					$info = $pubnub->publish($channelName, $message_pub);
				}
				
				#####################Add Status Message#########################
				$DataTripMessages['tMessage']= $message;
				$DataTripMessages['iDriverId']= $iDriverId;
				$DataTripMessages['iTripId']= $result[0]['iTripId'];
				$DataTripMessages['iUserId']= $result[0]['iUserId'];
				$DataTripMessages['eFromUserType']= "Driver";
				$DataTripMessages['eToUserType']= "Passenger";
				$DataTripMessages['eReceived']= "No";
				$DataTripMessages['dAddedDate']= @date("Y-m-d H:i:s");
				
				$obj->MySQLQueryPerform("trip_status_messages",$DataTripMessages,'insert');
				################################################################
				
				if($alertSendAllowed == true){	
					if($eDeviceType == "Android"){
    					
    					
    					array_push($registation_ids_new, $iGcmRegId);
    					$Rmessage         = array("message" => $message);
    					$result = send_notification($registation_ids_new, $Rmessage,0);
    					}else if($eDeviceType != "Android" ){
    					/*if($ENABLE_PUBNUB == "Yes"){
    						$message = "";
						} */
    					
    					array_push($deviceTokens_arr_ios, $iGcmRegId);
    					if($message != ""){
    						sendApplePushNotification(0,$deviceTokens_arr_ios,$message,$alertMsg,0);
						}
					}
				}  
				
				}else{
				$returnArr['Action'] = "0";
				$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
				// echo "UpdateFailed";
			}
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
	}
	
	############################################################################
	
	if ($type == "updateDriverLocations") {
		
		$iDriverId              = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$latitude_driver  = isset($_REQUEST["latitude"]) ? $_REQUEST["latitude"] : '';
		$longitude_driver = isset($_REQUEST["longitude"]) ? $_REQUEST["longitude"] : '';
		$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		
		
		$where = " iDriverId='$iDriverId'";
		$Data_update_driver['vLatitude']=$latitude_driver;
		$Data_update_driver['vLongitude']=$longitude_driver;
		
		$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
		# Update User Location Date #
		Updateuserlocationdatetime($iDriverId,"Driver",$vTimeZone);    
		# Update User Location Date #
		
		
		if ($id) {
			$returnArr['Action'] = "1";
			} else {
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
	}
	
	###########################################################################
	
	if ($type == "updateTripLocations") {
		
		$tripId     = isset($_REQUEST["TripId"]) ? $_REQUEST["TripId"] : '0';
		$latitudes  = isset($_REQUEST['latList']) ? $_REQUEST['latList'] : '';
		$longitudes = isset($_REQUEST['lonList']) ? $_REQUEST['lonList'] : '';
		$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
		
		if($iDriverId != "" && $tripId == ""){
			
			$iTripId = get_value('register_driver', 'iTripId', 'iDriverId',$iDriverId,'','true');
			if($iTripId != ""){
				$tripId = $iTripId;
			}
		}
		
		if($tripId != '' && $latitudes != '' && $longitudes!=''){
			$latitudes = preg_replace("/[^0-9,.-]/", "", $latitudes);
			$longitudes =preg_replace("/[^0-9,.-]/", "", $longitudes);
			$id = processTripsLocations($tripId,$latitudes,$longitudes);
		}
		
		if($id > 0){
			$returnArr['Action'] = "1";
			}else{
			$returnArr['Action'] = "0";
		}
		
		echo json_encode($returnArr);
	}
	
	###########################################################################
	
	
	if ($type == "StartTrip") {
		
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$iDriverId =isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$TripID = isset($_REQUEST["TripID"]) ? $_REQUEST["TripID"] : '0';
		$image_name = $vImage	= isset($_FILES['vImage']['name'])?$_FILES['vImage']['name']:'';
		$image_object	= isset($_FILES['vImage']['tmp_name'])?$_FILES['vImage']['tmp_name']:'';
		
		if($image_object){
			ExifCleaning::adjustImageOrientation($image_object); 
		}
		
		$startDateOfTrip=@date("Y-m-d H:i:s");
		$vLangCode=get_value('register_user', 'vLang', 'iUserId',$iUserId,'','true');
		if($vLangCode == "" || $vLangCode == NULL){
			$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		
		$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
		$tripstartlabel = $languageLabelsArr['LBL_DRIVER_START_NOTIMSG'];
		$tripstartlabel_ride = $languageLabelsArr['LBL_START_TRIP_DIALOG_TXT'];
		$message      = "TripStarted";
		
		$sql = "SELECT CONCAT(rd.vName,' ',rd.vLastName) AS driverName, tr.vRideNo FROM trips as tr,register_driver as rd WHERE tr.iTripId=rd.iTripId AND rd.iDriverId = '".$iDriverId."'";
		$result22 = $obj->MySQLSelect($sql);
		
		$verificationCode = rand ( 10000000 , 99999999 );
		
		/*$eType =get_value('trips', 'eType', 'iTripId',$TripID,'','true');
		$fVisitFee = get_value('trips', 'fVisitFee', 'iTripId', $TripID,'','true');  
		$eFareType = get_value('trips', 'eFareType', 'iTripId', $TripID,'','true');*/
    $TripData = get_value('trips', 'eType,fVisitFee,eFareType', 'iTripId', $TripID);
    $eType = $TripData[0]['eType'];
    $fVisitFee = $TripData[0]['fVisitFee'];
    $eFareType = $TripData[0]['eFareType']; 
		
		if($eType == "UberX"){
			$alertMsg = $languageLabelsArr['LBL_DELIVERY_DRIVER_TXT'].' '.$result22[0]['driverName'].' '.$tripstartlabel.$result22[0]['vRideNo'];
			}else{
			$alertMsg = $tripstartlabel_ride;
		}
		$message_arr = array();
		$message_arr['Message'] = $message;
		$message_arr['iDriverId'] = $iDriverId;
		$message_arr['iTripId'] = $TripID;
		$message_arr['driverName'] = $result22[0]['driverName'];
		$message_arr['vRideNo'] = $result22[0]['vRideNo'];
		if($eType == "Deliver"){
			$message_arr['VerificationCode'] = strval($verificationCode);
			}else{
			$message_arr['VerificationCode'] = "";
		}
		$message_arr['vTitle'] = $alertMsg;
		$message_arr['eType'] = $eType;
		
		$message = json_encode($message_arr,JSON_UNESCAPED_UNICODE);
		
		#####################Add Status Message#########################
		$DataTripMessages['tMessage']= $message;
		$DataTripMessages['iDriverId']= $iDriverId;
		$DataTripMessages['iTripId']= $TripID;
		$DataTripMessages['iUserId']= $iUserId;
		$DataTripMessages['eFromUserType']= "Driver";
		$DataTripMessages['eToUserType']= "Passenger";
		$DataTripMessages['eReceived']= "No";
		$DataTripMessages['dAddedDate']= @date("Y-m-d H:i:s");
		
		$obj->MySQLQueryPerform("trip_status_messages",$DataTripMessages,'insert');
		################################################################
		
		//Update passenger Table
		$where = " iUserId = '$iUserId'";
		
		$Data_update_passenger['vTripStatus']='On Going Trip';
		
		$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
		
		//Update Driver Table
		$where = " iDriverId = '$iDriverId'";
		
		$Data_update_driver['vTripStatus']='On Going Trip';
		
		$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
		
		
		$sql = "SELECT iGcmRegId,eDeviceType,iTripId,tLocationUpdateDate,eLogout,tSessionId FROM register_user WHERE iUserId='$iUserId'";
        $result = $obj->MySQLSelect($sql);
		
        // $Curr_TripID=$result[0]['iTripId'];
		
        $where = " iTripId = '$TripID'";
		
        $Data_update_trips['iActive']='On Going Trip';
        $Data_update_trips['tStartDate']=$startDateOfTrip;
		
		/*Code for Upload StartImage of trip Start */
		if($image_name != ""){
			//$Photo_Gallery_folder = $tconfig['tsite_upload_trip_images_path']."/".$TripID."/";
			$Photo_Gallery_folder = $tconfig['tsite_upload_trip_images_path'];
			if(!is_dir($Photo_Gallery_folder))
			mkdir($Photo_Gallery_folder, 0777);
			$vFile = $generalobj->fileupload($Photo_Gallery_folder,$image_object,$image_name,$prefix='', $vaildExt="pdf,doc,docx,jpg,jpeg,gif,png");
			$vImageName = $vFile[0];
			$Data_update_trips['vBeforeImage']=$vImageName;
		}
		/*Code for Upload StartImage of trip End */
        $id = $obj->MySQLQueryPerform("trips",$Data_update_trips,'update',$where);
		
		
		if($id > 0){
			$returnArr['Action'] = "1";
			$returnArr['fVisitFee'] = $fVisitFee;
			
			/*$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");
				$PUBNUB_DISABLED = $generalobj->getConfigurations("configurations","PUBNUB_DISABLED");
				$PUBNUB_PUBLISH_KEY = $generalobj->getConfigurations("configurations","PUBNUB_PUBLISH_KEY");
			$PUBNUB_SUBSCRIBE_KEY = $generalobj->getConfigurations("configurations","PUBNUB_SUBSCRIBE_KEY");*/
			if($PUBNUB_DISABLED == "Yes"){
				$ENABLE_PUBNUB = "No";
			}
			
			/* For PubNub Setting */
			$tableName = "register_user";
			$iMemberId_VALUE = $iUserId;
			$iMemberId_KEY = "iUserId";
			/*$iAppVersion=get_value($tableName, 'iAppVersion', $iMemberId_KEY,$iMemberId_VALUE,'','true');
			$eDeviceType=get_value($tableName, 'eDeviceType', $iMemberId_KEY,$iMemberId_VALUE,'','true');*/
      $AppData = get_value($tableName, 'iAppVersion,eDeviceType', $iMemberId_KEY, $iMemberId_VALUE);
      $iAppVersion = $AppData[0]['iAppVersion'];
      $eDeviceType = $AppData[0]['eDeviceType'];
			/* For PubNub Setting Finished */
			
			$cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + 60) / 60);
			$compare_date = @date('Y-m-d H:i:s', strtotime('-'.$cmpMinutes.' minutes'));
			
			//$alertSendAllowed = false;
			$alertSendAllowed = true;
			
			
			
			if($ENABLE_PUBNUB == "Yes"  && $PUBNUB_PUBLISH_KEY != "" && $PUBNUB_SUBSCRIBE_KEY != ""/*  && $iAppVersion > 1 && $eDeviceType == "Android" */){
				
				//$pubnub = new Pubnub\Pubnub($PUBNUB_PUBLISH_KEY, $PUBNUB_SUBSCRIBE_KEY);
				$pubnub = new Pubnub\Pubnub(array("publish_key" => $PUBNUB_PUBLISH_KEY,"subscribe_key" => $PUBNUB_SUBSCRIBE_KEY, "uuid" => $uuid));
				
				$channelName = "PASSENGER_".$iUserId;
				
        //$tSessionId=get_value("register_user", 'tSessionId', "iUserId",$iUserId,'','true');
        $tSessionId = $result[0]['tSessionId'];
				$message_arr['tSessionId'] = $tSessionId;
				$message_pub = json_encode($message_arr,JSON_UNESCAPED_UNICODE);
				$info = $pubnub->publish($channelName, $message_pub);
				
				//$message = $alertMsg;
				$tLocUpdateDate = date("Y-m-d H:i:s",strtotime($result[0]['tLocationUpdateDate']));
				if($tLocUpdateDate < $compare_date){
					$alertSendAllowed = true;
				}
				//$alertSendAllowed = true;
				}else{
				$alertSendAllowed = true;
			}
			if($result[0]['eLogout'] == "Yes"){
				$alertSendAllowed = false;
			}
			
			$deviceTokens_arr = array();
			
			if($alertSendAllowed == true){
				array_push($deviceTokens_arr, $result[0]['iGcmRegId']);
				
				if($result[0]['eDeviceType'] == "Android" ){
					$Rmessage = array("message" => $message);
					
					send_notification($deviceTokens_arr, $Rmessage,0);
					}else{
					sendApplePushNotification(0,$deviceTokens_arr,$message,$alertMsg,0);
				}
				
			}
			
			
			// Send SMS to receiver if trip type is delivery.
			if($eType == "Deliver"){
				$receiverMobile =get_value('trips', 'vReceiverMobile', 'iTripId',$TripID,'','true');
				$receiverMobile1 = "+".$receiverMobile;
				
				$where_trip_update = " iTripId = '$TripID'";
				$data_delivery['vDeliveryConfirmCode']=$verificationCode;
				$obj->MySQLQueryPerform("trips",$data_delivery,'update',$where);
				
				//$message_deliver = "SMS format goes here. Your verification code is ".$verificationCode." Please give this code to driver to end delivery process.";
				$message_deliver = deliverySmsToReceiver($TripID);
				$result = sendEmeSms($receiverMobile1,$message_deliver);
				if($result ==0){
					//$isdCode= $generalobj->getConfigurations("configurations","SITE_ISD_CODE");
					$isdCode= $SITE_ISD_CODE;
					$receiverMobile = "+".$isdCode.$receiverMobile;
					sendEmeSms($receiverMobile,$message_deliver);
				}
				
				$returnArr['message']=$verificationCode;
				$returnArr['SITE_TYPE']=SITE_TYPE;
			}
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		$returnArr['iTripTimeId'] = '';
		if($eFareType == 'Hourly'){
			$dTime = date('Y-m-d H:i:s');
			$Data_update['dResumeTime']=$dTime;
			$Data_update['iTripId']=$TripID;
			$id = $obj->MySQLQueryPerform("trip_times",$Data_update,'insert');
			$returnArr['iTripTimeId'] = $id;
		}
		echo json_encode($returnArr);
		
	}
	
	###########################################################################
	
	if($type == "ProcessEndTrip"){
		global $generalobj;
		$tripId     = isset($_REQUEST["TripId"]) ? $_REQUEST["TripId"] : '0';
		$userId     = isset($_REQUEST["PassengerId"]) ? $_REQUEST["PassengerId"] : '';
		$driverId     = isset($_REQUEST["DriverId"]) ? $_REQUEST["DriverId"] : '';
		$latitudes  = isset($_REQUEST["latList"]) ? $_REQUEST["latList"] : '';
		$longitudes = isset($_REQUEST["lonList"]) ? $_REQUEST["lonList"] : '';
		$tripDistance     = isset($_REQUEST["TripDistance"]) ? $_REQUEST["TripDistance"] : '0';
		$dAddress        = isset($_REQUEST["dAddress"]) ? $_REQUEST["dAddress"] : '';
		// $currentCity= isset($_REQUEST["currentCity"]) ? $_REQUEST["currentCity"] : '';
		$destination_lat = isset($_REQUEST["dest_lat"]) ? $_REQUEST["dest_lat"] : '';
		$destination_lon = isset($_REQUEST["dest_lon"]) ? $_REQUEST["dest_lon"] : '';
		$isTripCanceled = isset($_REQUEST["isTripCanceled"]) ? $_REQUEST["isTripCanceled"] : '';
		$driverComment   = isset($_REQUEST["Comment"]) ? $_REQUEST["Comment"] : '';
		$driverReason   = isset($_REQUEST["Reason"]) ? $_REQUEST["Reason"] : '';
		$image_name = $vImage	= isset($_FILES['vImage']['name'])?$_FILES['vImage']['name']:'';
		$image_object	= isset($_FILES['vImage']['tmp_name'])?$_FILES['vImage']['tmp_name']:'';
		$fMaterialFee  = isset($_REQUEST["fMaterialFee"]) ? $_REQUEST["fMaterialFee"] : '';
		$fMiscFee  = isset($_REQUEST["fMiscFee"]) ? $_REQUEST["fMiscFee"] : '';
		$fDriverDiscount  = isset($_REQUEST["fDriverDiscount"]) ? $_REQUEST["fDriverDiscount"] : '';
		$vCurrencyDriver = get_value('register_driver', 'vCurrencyDriver', 'iDriverId',$driverId,'','true');
		$DriverRation = get_value('currency', 'Ratio', 'vName',$vCurrencyDriver,'','true');
		
		if($image_object){
			ExifCleaning::adjustImageOrientation($image_object); 
		}
		//$exifDATA = exif_read_data($image_object, 0, true);
		//echo "EXIFData::<BR/>";
		//print_r($exifDATA);exit;
		//$currencyRatio = get_value('currency', 'Ratio', 'eDefault', 'Yes','','true');
		$fMaterialFee  = round($fMaterialFee / $DriverRation, 2);
		$fMiscFee  = round($fMiscFee / $DriverRation, 2);
		$fDriverDiscount  = round($fDriverDiscount / $DriverRation, 2);
		$eType = get_value('trips', 'eType', 'iTripId', $tripId,'','true');
		
		$Active="Finished";
		$vLangCode=get_value('register_user', 'vLang', 'iUserId',$userId,'','true');
		if($vLangCode == "" || $vLangCode == NULL){
			$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
    
		$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
		$tripcancelbydriver = $languageLabelsArr['LBL_TRIP_CANCEL_BY_DRIVER'];
		$tripfinish = $languageLabelsArr['LBL_DRIVER_END_NOTIMSG'];
		$tripfinish_ride = $languageLabelsArr['LBL_TRIP_FINISH'];
		
		$message_arr = array();
		$message_arr['ShowTripFare'] = "true";
		if($isTripCanceled == "true"){
			$message  = "TripCancelledByDriver";
			}else{
			$message      = "TripEnd";
		}
		
		$sql = "SELECT CONCAT(rd.vName,' ',rd.vLastName) AS driverName,tr.vRideNo FROM trips as tr,register_driver as rd WHERE tr.iTripId=rd.iTripId AND rd.iDriverId = '".$driverId."'";
		$result22 = $obj->MySQLSelect($sql);
        
		if($isTripCanceled == "true"){
			// $alertMsg = $tripcancelbydriver;
			if($eType == "UberX"){
				$usercanceltriplabel = $result22[0]['driverName'].':'.$result22[0]['vRideNo'].'-'. $languageLabelsArr['LBL_PREFIX_TRIP_CANCEL_DRIVER'].' '.$driverReason;
				}else{
				$usercanceltriplabel = $languageLabelsArr['LBL_PREFIX_TRIP_CANCEL_DRIVER'].' '.$driverReason;
			}
			$alertMsg = $usercanceltriplabel;
			}else{
			if($eType == "UberX"){
				//$alertMsg = $tripfinish;
				$alertMsg = $result22[0]['driverName']." ".$tripfinish." ".$result22[0]['vRideNo'];
				}else{
				$alertMsg = $tripfinish_ride;
			}
		}
        $message_arr['Message'] = $message;
        $message_arr['iTripId'] = $tripId;
        $message_arr['iDriverId'] = $driverId;
		$message_arr['driverName'] = $result22[0]['driverName'];
		$message_arr['vRideNo'] = $result22[0]['vRideNo'];
		if($isTripCanceled == "true"){
			$message_arr['Reason'] = $driverReason;
			$message_arr['isTripStarted'] = "true";
		}
		$message_arr['vTitle'] = $alertMsg;
		$message_arr['eType'] = $eType;
		
        $message = json_encode($message_arr,JSON_UNESCAPED_UNICODE);
		
		#####################Add Status Message#########################
		$DataTripMessages['tMessage']= $message;
		$DataTripMessages['iDriverId']= $driverId;
		$DataTripMessages['iTripId']= $tripId;
		$DataTripMessages['iUserId']= $userId;
		$DataTripMessages['eFromUserType']= "Driver";
		$DataTripMessages['eToUserType']= "Passenger";
		$DataTripMessages['eReceived']= "No";
		$DataTripMessages['dAddedDate']= @date("Y-m-d H:i:s");
		
		$obj->MySQLQueryPerform("trip_status_messages",$DataTripMessages,'insert');
		################################################################
		
		$couponCode=get_value('trips', 'vCouponCode', 'iTripId',$tripId,'','true');
		$discountValue = 0;
		$discountValueType= "cash";
		if( $couponCode != ''){
			/*$discountValue = get_value('coupon', 'fDiscount', 'vCouponCode', $couponCode,'','true');
			$discountValueType = get_value('coupon', 'eType', 'vCouponCode', $couponCode,'','true');*/
      $CouponData = get_value('coupon', 'fDiscount,eType', 'vCouponCode', $couponCode);
      $discountValue = $CouponData[0]['fDiscount'];
      $discountValueType = $CouponData[0]['eType']; 
		}
		
		
		if($latitudes != '' && $longitudes != ''){
			processTripsLocations($tripId,$latitudes,$longitudes);
		}
		
		$vCurrencyDriver=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $driverId,'','true');
		$currencySymbolDriver = get_value('currency', 'vSymbol', 'vName', $vCurrencyDriver,'','true');
		
		$sql = "SELECT tStartDate,tEndDate,iVehicleTypeId,tStartLat,tStartLong,eFareType,fRatio_".$vCurrencyDriver." as fRatioDriver, vTripPaymentMode,fPickUpPrice,fNightPrice, eType, fTollPrice,eFlatTrip,fFlatTripPrice FROM trips WHERE iTripId='$tripId'";
		$trip_start_data_arr = $obj->MySQLSelect($sql);
		
		$tripDistance=calcluateTripDistance($tripId);
		
		$sourcePointLatitude=$trip_start_data_arr[0]['tStartLat'];
		$sourcePointLongitude=$trip_start_data_arr[0]['tStartLong'];
		$startDate=$trip_start_data_arr[0]['tStartDate'];
		$vehicleTypeID=$trip_start_data_arr[0]['iVehicleTypeId'];
		$eFareType=$trip_start_data_arr[0]['eFareType'];
		$eType=$trip_start_data_arr[0]['eType'];
    $eFlatTrip=$trip_start_data_arr[0]['eFlatTrip'];
    $fFlatTripPrice=$trip_start_data_arr[0]['fFlatTripPrice'];
		
		//$endDateOfTrip=@date("Y-m-d H:i:s");
		$endDateOfTrip = $trip_start_data_arr[0]['tEndDate'];
		if($endDateOfTrip == "0000-00-00 00:00:00"){
			$endDateOfTrip=@date("Y-m-d H:i:s");    
		}
		
		if($eFareType == 'Hourly'){
			$sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$tripId'";
			$db_tripTimes = $obj->MySQLSelect($sql22);
			
			$totalSec = 0;
			$iTripTimeId = '';
			foreach($db_tripTimes as $dtT){
				if($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
					$totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
				}
			}
			$totalTimeInMinutes_trip=@round(abs($totalSec) / 60,2);
			}else {
			$totalTimeInMinutes_trip=@round(abs(strtotime($startDate) - strtotime($endDateOfTrip)) / 60,2);
		}
		if($totalTimeInMinutes_trip <= 1){
			$FinalDistance= $tripDistance;
			$FGDTime = 0;
			$FGDDistance = 0;
		}else{
			//$FinalDistance=checkDistanceWithGoogleDirections($tripDistance,$sourcePointLatitude,$sourcePointLongitude,$destination_lat,$destination_lon);
			$FinalDistanceArr=checkDistanceWithGoogleDirections($tripDistance,$sourcePointLatitude,$sourcePointLongitude,$destination_lat,$destination_lon,"0","",true);
			$FinalDistance= $FinalDistanceArr['Distance'];
			$FGDTime= $FinalDistanceArr['Time'];
			$FGDDistance= $FinalDistanceArr['GDistance'];
		}
		
		$tripDistance=$FinalDistance;
		
		$Fare_data=calculateFare($totalTimeInMinutes_trip,$tripDistance,$vehicleTypeID,$userId,1,$startDate,$endDateOfTrip,$couponCode,$tripId,$fMaterialFee,$fMiscFee,$fDriverDiscount);
		$where = " iTripId = '" . $tripId . "'";
		
		$Data_update_trips['tEndDate']=$endDateOfTrip;
		$Data_update_trips['tEndLat']=$destination_lat;
		$Data_update_trips['tEndLong']=$destination_lon;
		$Data_update_trips['tDaddress']=$dAddress;
		$Data_update_trips['iFare']=$Fare_data['total_fare'];
		$Data_update_trips['iActive']=$Active;
		$Data_update_trips['fDistance']=$tripDistance;
		$Data_update_trips['fDuration']=$totalTimeInMinutes_trip;
		$Data_update_trips['fPricePerMin']=$Fare_data['fPricePerMin'];
		$Data_update_trips['fPricePerKM']=$Fare_data['fPricePerKM'];
		$Data_update_trips['iBaseFare']=$Fare_data['iBaseFare'];
		$Data_update_trips['fCommision']=$Fare_data['fCommision'];

		// $Data_update_trips['fDiscount']=$Fare_data['fDiscount'];
		// echo json_encode($Data_update_trips);
		// exit;
	
		$Data_update_trips['fDiscount']=!empty($Fare_data['fDiscount']) ? $Fare_data['fDiscount'] :'0.0';
		$Data_update_trips['vDiscount'] =$Fare_data['vDiscount'] ;
		$Data_update_trips['fMinFareDiff']=$Fare_data['MinFareDiff'];
		$Data_update_trips['fSurgePriceDiff']=$Fare_data['fSurgePriceDiff'];
		$Data_update_trips['fWalletDebit']=$Fare_data['user_wallet_debit_amount'];
		$Data_update_trips['fTripGenerateFare']=$Fare_data['fTripGenerateFare'];
		$Data_update_trips['fMaterialFee']=$fMaterialFee;
		$Data_update_trips['fMiscFee']=$fMiscFee;
		$Data_update_trips['fDriverDiscount']=$fDriverDiscount;
    	$Data_update_trips['fTax1']=$Fare_data['fTax1'];
    	$Data_update_trips['fTax2']=$Fare_data['fTax2'];
    	$Data_update_trips['fGDtime']=$FGDTime;
		$Data_update_trips['fGDdistance']=$FGDDistance;
		
		if($isTripCanceled == "true"){
			$Data_update_trips['vCancelReason'] = $driverReason;
			$Data_update_trips['vCancelComment'] = $driverComment;
			$Data_update_trips['eCancelled'] = "Yes";
			$Data_update_trips['eCancelledBy'] = "Driver";
		}
		
		/*Code for Upload AfterImage of trip Start */
		if($image_name != ""){
			//$Photo_Gallery_folder = $tconfig['tsite_upload_trip_images_path']."/".$TripID."/";
			$Photo_Gallery_folder = $tconfig['tsite_upload_trip_images_path'];
			if(!is_dir($Photo_Gallery_folder))
			mkdir($Photo_Gallery_folder, 0777);
			$vFile = $generalobj->fileupload($Photo_Gallery_folder,$image_object,$image_name,$prefix='', $vaildExt="pdf,doc,docx,jpg,jpeg,gif,png");
			$vImageName = $vFile[0];
			$Data_update_trips['vAfterImage']=$vImageName;
		}
		/*Code for Upload AfterImage of trip End */
		$id = $obj->MySQLQueryPerform("trips",$Data_update_trips,'update',$where);
		
		$trip_status    = "Not Active";
		
		$where = " iUserId = '$userId'";
		$Data_update_passenger['iTripId']=$tripId;
		$Data_update_passenger['vTripStatus']=$trip_status;
		$Data_update_passenger['vCallFromDriver']='Not Assigned';
		
		$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
		
		$where = " iDriverId = '$driverId'";
		$Data_update_driver['iTripId']=$tripId;
		$Data_update_driver['vTripStatus']=$trip_status;
		
		$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
		
		if($id>0){
			
			/*$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");
				$PUBNUB_DISABLED = $generalobj->getConfigurations("configurations","PUBNUB_DISABLED");
				$PUBNUB_PUBLISH_KEY = $generalobj->getConfigurations("configurations","PUBNUB_PUBLISH_KEY");
			$PUBNUB_SUBSCRIBE_KEY = $generalobj->getConfigurations("configurations","PUBNUB_SUBSCRIBE_KEY");*/
			if($PUBNUB_DISABLED == "Yes"){
				$ENABLE_PUBNUB = "No";
			}
			
			
			/* For PubNub Setting */
			$tableName = "register_user";
			$iMemberId_VALUE = $userId;
			$iMemberId_KEY = "iUserId";
			/*$iAppVersion=get_value($tableName, 'iAppVersion', $iMemberId_KEY,$iMemberId_VALUE,'','true');
			$eDeviceType=get_value($tableName, 'eDeviceType', $iMemberId_KEY,$iMemberId_VALUE,'','true');
			$eLogout=get_value($tableName, 'eLogout', $iMemberId_KEY,$iMemberId_VALUE,'','true');
			$tLocationUpdateDate=get_value($tableName, 'tLocationUpdateDate', $iMemberId_KEY,$iMemberId_VALUE,'','true');
			$iGcmRegId=get_value($tableName, 'iGcmRegId', $iMemberId_KEY,$iMemberId_VALUE,'','true'); */
      $AppData = get_value($tableName, 'iAppVersion,eDeviceType,eLogout,tLocationUpdateDate,iGcmRegId', $iMemberId_KEY, $iMemberId_VALUE);
      $iAppVersion = $AppData[0]['iAppVersion'];
      $eDeviceType = $AppData[0]['eDeviceType'];
      $eLogout = $AppData[0]['eLogout'];
      $tLocationUpdateDate = $AppData[0]['tLocationUpdateDate'];
      $iGcmRegId = $AppData[0]['iGcmRegId']; 
			/* For PubNub Setting Finished */
			
			$cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + 60) / 60);
			$compare_date = @date('Y-m-d H:i:s', strtotime('-'.$cmpMinutes.' minutes'));
			
			//$alertSendAllowed = false;
			$alertSendAllowed = true;
			
			
			if($ENABLE_PUBNUB == "Yes"  && $PUBNUB_PUBLISH_KEY != "" && $PUBNUB_SUBSCRIBE_KEY != "" /* && $iAppVersion > 1 && $eDeviceType == "Android" */){
				
				//$pubnub = new Pubnub\Pubnub($PUBNUB_PUBLISH_KEY, $PUBNUB_SUBSCRIBE_KEY);
				$pubnub = new Pubnub\Pubnub(array("publish_key" => $PUBNUB_PUBLISH_KEY,"subscribe_key" => $PUBNUB_SUBSCRIBE_KEY, "uuid" => $uuid));
				
				$channelName = "PASSENGER_".$userId;
				
				$tSessionId=get_value("register_user", 'tSessionId', "iUserId",$userId,'','true');
				$message_arr['tSessionId'] = $tSessionId;
				$message_pub = json_encode($message_arr,JSON_UNESCAPED_UNICODE);
				
				$info = $pubnub->publish($channelName, $message_pub);
				
				//$message = $alertMsg;
				$tLocUpdateDate = date("Y-m-d H:i:s",strtotime($tLocationUpdateDate));
				if($tLocUpdateDate < $compare_date){
					$alertSendAllowed = true;
				}
				//$alertSendAllowed = true;  
				}else{
				$alertSendAllowed = true;
			}
			
			if($eLogout == "Yes"){
				$alertSendAllowed = false;
			}
			$deviceTokens_arr = array();
			
			if($alertSendAllowed == true){
				array_push($deviceTokens_arr, $iGcmRegId);
				
				if($eDeviceType == "Android" ){
					$Rmessage = array("message" => $message);
					
					send_notification($deviceTokens_arr, $Rmessage,0);
					}else{
					sendApplePushNotification(0,$deviceTokens_arr,$message,$alertMsg,0);
				}
				
			}
			
			$returnArr['Action'] = "1";
			$returnArr['iTripsLocationsID']=$id;
			// $returnArr['TotalFare']=round($Fare_data[0]['total_fare'] * $trip_start_data_arr[0]['fRatioDriver']);
			$returnArr['TotalFare']=round($Fare_data['total_fare'] * $trip_start_data_arr[0]['fRatioDriver'],1);
			// $returnArr['CurrencySymbol']=($obj->MySQLSelect("SELECT vSymbol FROM currency WHERE vName='".$trip_start_data_arr[0]['vCurrencyDriver']."' ")[0]['vSymbol']);
			$returnArr['CurrencySymbol']=$currencySymbolDriver;
			$returnArr['tripStartTime']=$startDate;
			$returnArr['TripPaymentMode']=$trip_start_data_arr[0]['vTripPaymentMode'];
			$returnArr['Discount']=round($Fare_data['fDiscount'] * $trip_start_data_arr[0]['fRatioDriver'],1);
			$returnArr['Message']="Data Updated";
			$returnArr['FormattedTripDate']=date('dS M Y \a\t h:i a',strtotime($startDate));
			
			
			$generalobj->get_benefit_amount($tripId);
			
			// Code for Check last logout date is update in driver_log_report
            $query = "SELECT * FROM driver_log_report WHERE iDriverId = '".$driverId."' ORDER BY iDriverLogId DESC LIMIT 0,1";
            $db_driver = $obj->MySQLSelect($query);
            if(count($db_driver) > 0) {
				$driver_lastonline = @date("Y-m-d H:i:s");
				$updateQuery = "UPDATE driver_log_report set dLogoutDateTime='".$driver_lastonline."' WHERE iDriverLogId = ".$db_driver[0]['iDriverLogId'];
				$obj->sql_query($updateQuery);
			}
            // Code for Check last logout date is update in driver_log_report Ends
			
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		//getTripChatDetails($tripId);
		echo json_encode($returnArr);
		
	}
	
	###########################################################################
	
	if($type=="CollectPayment"){
		$iTripId     = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '0';
		$isCollectCash     = isset($_REQUEST["isCollectCash"]) ? $_REQUEST["isCollectCash"] : '';
		
		$sql = "SELECT vTripPaymentMode,iUserId,iDriverId,iFare,vRideNo,fWalletDebit,fTripGenerateFare,fDiscount,fCommision,fTollPrice,eHailTrip FROM trips WHERE iTripId='$iTripId'";
		$tripData = $obj->MySQLSelect($sql);
		
		$vTripPaymentMode = $tripData[0]['vTripPaymentMode'];
		$data['vTripPaymentMode']=$vTripPaymentMode;
		$iUserId = $tripData[0]['iUserId'];
		//$iFare = $tripData[0]['iFare']+$tripData[0]['fTollPrice'];
		$iFare = $tripData[0]['iFare'];
		$vRideNo = $tripData[0]['vRideNo'];
		$eHailTrip = $tripData[0]['eHailTrip'];
		
		$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		
		$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
		
		if($vTripPaymentMode == "Card" && $isCollectCash == ""){
			
			$vStripeCusId = get_value('register_user', 'vStripeCusId', 'iUserId', $iUserId,'','true');
			
			$price_new = $iFare * 100;
			$currency = get_value('currency', 'vName', 'eDefault', 'Yes','','true');
			
			$description = $languageLabelsArr['LBL_TRIP_PAYMENT_RECEIVED']." ".$vRideNo;
			
			try{
				if($iFare > 0){
					$charge_create = Stripe_Charge::create(array(
					"amount" => $price_new,
					"currency" => $currency,
					"customer" => $vStripeCusId,
					"description" =>  $description
					));
					
					$details = json_decode($charge_create);
					$result = get_object_vars($details);
				}
				
				
				if($iFare == 0 || ($result['status']=="succeeded" && $result['paid']=="1")){
					
					$pay_data['tPaymentUserID']= $iFare == 0? "":$result['id'];
					$pay_data['vPaymentUserStatus']="approved";
					$pay_data['iTripId']=$iTripId;
					$pay_data['iAmountUser']=$iFare;
					
					$id = $obj->MySQLQueryPerform("payments",$pay_data,'insert');
					
					}else{
					$returnArr['Action'] = "0";
					$returnArr['message']="LBL_CHARGE_COLLECT_FAILED";
					
					echo json_encode($returnArr);exit;
				}
				
				
				}catch(Exception $e){
				$error3 = $e->getMessage();
				$returnArr['Action'] = "0";
				$returnArr['message']=$error3;
				//$returnArr['message']="LBL_CHARGE_COLLECT_FAILED";
				
				echo json_encode($returnArr);exit;
			}
			$data['vTripPaymentMode']="Card";
			}else if($vTripPaymentMode == "Card" && $isCollectCash == "true"){
			// echo "else if";exit;
			$data['vTripPaymentMode']="Cash";
		}
		
		// echo "out";exit;
		$where = " iTripId = '$iTripId'";
		$data['ePaymentCollect']="Yes";
		
		$id = $obj->MySQLQueryPerform("trips",$data,'update',$where);
		
		$fWalletDebit = $tripData[0]['fWalletDebit'];
		$fDiscount = $tripData[0]['fDiscount'];
		$discountValue = $fWalletDebit + $fDiscount;
		//$discountValue = $tripData[0]['fDiscount'];
		//$walletamountofcreditcard = $tripData[0]['fTripGenerateFare']+$tripData[0]['fTollPrice'];
		$walletamountofcreditcard = $tripData[0]['fTripGenerateFare'];
		$driverId = $tripData[0]['iDriverId'];
		
		//$COMMISION_DEDUCT_ENABLE=$generalobj->getConfigurations("configurations","COMMISION_DEDUCT_ENABLE");
		if($COMMISION_DEDUCT_ENABLE == 'Yes') {
			#Deduct Amount From Driver's Wallet Acount#
			$vTripPaymentMode = $data['vTripPaymentMode']; 
			if($vTripPaymentMode == "Cash"){
				$vRideNo = $tripData[0]['vRideNo'];
				$iBalance = $tripData[0]['fCommision'];
				$eFor = "Withdrawl";
				$eType = "Debit";
				$iTripId = $iTripId;	
				//$tDescription = 'Debited for booking#'.$vRideNo;
				$tDescription = '#LBL_DEBITED_BOOKING# '.$vRideNo;
				$ePaymentStatus = 'Settelled';
				$dDate =   Date('Y-m-d H:i:s');
				if($discountValue > 0){
					$eFor_credit = "Deposit";
					$eType_credit = "Credit";
					$tDescription_credit = '#LBL_CREDITED_BOOKING# '.$vRideNo;
					//$tDescription_credit = 'Credited for booking#'.$vRideNo;
					$generalobj->InsertIntoUserWallet($driverId,"Driver",$discountValue,$eType_credit,$iTripId,$eFor_credit,$tDescription_credit,$ePaymentStatus,$dDate);
				}
				$generalobj->InsertIntoUserWallet($driverId,"Driver",$iBalance,$eType,$iTripId,$eFor,$tDescription,$ePaymentStatus,$dDate);
				$Where = " iTripId = '$iTripId'";
				$Data_update_driver_paymentstatus['eDriverPaymentStatus']="Settelled";
				$Update_Payment_Id = $obj->MySQLQueryPerform("trips",$Data_update_driver_paymentstatus,'update',$Where);
			}
			/* else{
				$vRideNo = $tripData[0]['vRideNo'];
				$iBalance = $walletamountofcreditcard-$tripData[0]['fCommision'];
				$eFor = "Deposit";
				$eType = "Credit";
				$iTripId = $iTripId;	
				$tDescription = ' Amount '.$iBalance.' Credited into your account for booking no#'.$vRideNo;
				$ePaymentStatus = 'Settelled';
				$dDate =   Date('Y-m-d H:i:s');
				$generalobj->InsertIntoUserWallet($driverId,"Driver",$iBalance,$eType,$iTripId,$eFor,$tDescription,$ePaymentStatus,$dDate);
				$Where = " iTripId = '$iTripId'";
				$Data_update_driver_paymentstatus['eDriverPaymentStatus']="Settelled";
				$Update_Payment_Id = $obj->MySQLQueryPerform("trips",$Data_update_driver_paymentstatus,'update',$Where);
			} */
			#Deduct Amount From Driver's Wallet Acount#
		}
		if($id >0){
			$returnArr['Action'] = "1";
			
			// Rating entry if trip is hail
			if($eHailTrip == "Yes"){
				
				$Data_update_ratings['iTripId']=$iTripId;
				$Data_update_ratings['vRating1']="0.0";
				$Data_update_ratings['vMessage']="";
				$Data_update_ratings['eUserType']="Driver";
				
				$obj->MySQLQueryPerform("ratings_user_driver",$Data_update_ratings,'insert');
				
				$Data_update_ratings['eUserType']="Passenger";
				
				$obj->MySQLQueryPerform("ratings_user_driver",$Data_update_ratings,'insert');
			}
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		echo json_encode($returnArr);
	}
	
	###########################################################################
	
	###########################################################################
	
	if($type=="addMoneyUserWallet"){
		$iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
		$eMemberType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';  //Passenger,Driver
		$fAmount = isset($_REQUEST["fAmount"]) ? $_REQUEST["fAmount"] : '';
		if($eMemberType == "Passenger"){
			$tbl_name = "register_user";
			$currencycode = "vCurrencyPassenger";
			$iUserId = "iUserId";
			$eUserType = "Rider";
			}else{
			$tbl_name = "register_driver";
			$currencycode = "vCurrencyDriver";
			$iUserId = "iDriverId";
			$eUserType = "Driver";
		}
		/*$vStripeCusId = get_value($tbl_name, 'vStripeCusId', $iUserId, $iMemberId,'','true');
		$vStripeToken = get_value($tbl_name, 'vStripeToken', $iUserId, $iMemberId,'','true');
		$userCurrencyCode = get_value($tbl_name, $currencycode, $iUserId, $iMemberId,'','true');*/
    $UserCardData = get_value($tbl_name, 'vStripeCusId,vStripeToken,'.$currencycode.' as currencycode', $iUserId, $iMemberId);
    $vStripeCusId = $UserCardData[0]['vStripeCusId'];
    $vStripeToken = $UserCardData[0]['vStripeToken'];
    $userCurrencyCode = $UserCardData[0]['currencycode']; 
		$userCurrencyRatio = get_value('currency', 'Ratio', 'vName', $userCurrencyCode,'','true');
		$walletamount =  round($fAmount/$userCurrencyRatio,2);
		/*$currencyCode = get_value('currency', 'vName', 'eDefault', 'Yes','','true');
		$currencyratio = get_value('currency', 'Ratio', 'vName', $currencyCode,'','true');*/
    $DefaultCurrencyData = get_value('currency', 'vName,Ratio', 'eDefault', 'Yes');
    $currencyCode = $DefaultCurrencyData[0]['vName'];
    $currencyratio = $DefaultCurrencyData[0]['Ratio'];
		$price = $fAmount*$currencyratio;
		$price_new = $walletamount * 100;
		$price_new = round($price_new);
		if($vStripeCusId == "" || $vStripeToken == ""){
			$returnArr["Action"] = "0";
			$returnArr['message']="LBL_NO_CARD_AVAIL_NOTE";
			echo json_encode($returnArr);exit;
		}
		
		$dDate = Date('Y-m-d H:i:s');
		$eFor = 'Deposit';
		$eType = 'Credit';
		$iTripId = 0;
		//$tDescription = "Amount credited";
		$tDescription = '#LBL_AMOUNT_CREDIT#';
		$ePaymentStatus = 'Unsettelled';
		
		try{
			$charge_create = Stripe_Charge::create(array(
			"amount" => $price_new,
			"currency" => $currencyCode,
			"customer" => $vStripeCusId,
			"description" =>  $tDescription
			));
			
			$details = json_decode($charge_create);
			$result = get_object_vars($details);
			//echo "<pre>";print_r($result);exit;
			if($result['status']=="succeeded" && $result['paid']=="1"){
				$generalobj->InsertIntoUserWallet($iMemberId,$eUserType,$walletamount,'Credit',0,$eFor,$tDescription,$ePaymentStatus,$dDate);
				$user_available_balance = $generalobj->get_user_available_balance($iMemberId,$eUserType);
				$returnArr["Action"] = "1";
				$returnArr["MemberBalance"] = strval($generalobj->userwalletcurrency(0,$user_available_balance,$userCurrencyCode));
				$returnArr['message1']= "LBL_WALLET_MONEY_CREDITED";
				
				if($eMemberType != "Driver"){
					$returnArr['message'] = getPassengerDetailInfo($iMemberId,"");
					}else{
					$returnArr['message'] = getDriverDetailInfo($iMemberId);
				}
				
				echo json_encode($returnArr);exit;
				}else{
				$returnArr['Action'] = "0";
				$returnArr['message']="LBL_WALLET_MONEY_CREDITED_FAILED";
				
				echo json_encode($returnArr);exit;
			}
			
			}catch(Exception $e){
			//echo "<pre>";print_r($e);exit;
			$error3 = $e->getMessage();
			$returnArr["Action"] = "0";
			$returnArr['message']=$error3;
			//$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
			
			echo json_encode($returnArr);exit;
		}
		
	}
	###########################################################################
	
	if($type == "GenerateCustomer"){
		$iUserId     = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$eMemberType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';  //Passenger,Driver
		$vStripeToken     = isset($_REQUEST["vStripeToken"]) ? $_REQUEST["vStripeToken"] : '';
		$CardNo     = isset($_REQUEST["CardNo"]) ? $_REQUEST["CardNo"] : '';
		
		if($eMemberType == "Passenger"){
			$tbl_name = "register_user";
			$vEmail = "vEmail";
			$iMemberId = "iUserId";
			$eUserType = "Rider";
			}else{
			$tbl_name = "register_driver";
			$vEmail = "vEmail";
			$iMemberId = "iDriverId";
			$eUserType = "Driver";
		}
		
		/*$vEmail = get_value($tbl_name, $vEmail, $iMemberId, $iUserId,'','true');
		$vStripeCusId = get_value($tbl_name, 'vStripeCusId', $iMemberId, $iUserId,'','true');*/
    $UserDetail = get_value($tbl_name, 'vStripeCusId,'.$vEmail.' as memberemail', $iMemberId, $iUserId);
    $vEmail = $UserDetail[0]['memberemail'];
    $vStripeCusId = $UserDetail[0]['vStripeCusId']; 
		
		try{
			if($vStripeCusId  != ""){
				$customer 	= Stripe_Customer::retrieve($vStripeCusId);
				$sources = $customer -> sources;
				$stripeData = $sources -> data;
				
				if(count($stripeData) >0 && $stripeData[0]['id'] != ''){
					$customer->sources->retrieve($stripeData[0]['id'])->delete();
				}
				
				$card = $customer->sources->create(array("source" => $vStripeToken));
				
				}else{
				try{
					$customer 	= Stripe_Customer::create(array( "source" => $vStripeToken, "email" => $vEmail));
					$vStripeCusId = $customer->id;
					} catch (Exception $e) {
      				$error3 = $e->getMessage();
      				$returnArr['Action'] = "0";
      				$returnArr['message']=$error3;
      				echo json_encode($returnArr);exit;
				}
			}
			}catch(Exception $e){
			$errMsg = $e->getMessage();
			if (strpos($errMsg, 'No such customer') !== false) {
				try{
					$customer 	= Stripe_Customer::create(array( "source" => $vStripeToken, "email" => $vEmail));
					}catch(Exception $e){
					$error3 = $e->getMessage();
					$returnArr['Action'] = "0";
					$returnArr['message']=$error3;
					
					echo json_encode($returnArr);exit;
				}
				
				$vStripeCusId = $customer->id;
				}else{
				$returnArr['Action'] = "0";
				$returnArr['message']=$errMsg;
				
				echo json_encode($returnArr);exit;
			}
		}
		
		$where = " $iMemberId = '$iUserId'";
		$updateData['vStripeToken']=$vStripeToken;
		$updateData['vStripeCusId']=$vStripeCusId;
		$updateData['vCreditCard']=$CardNo;
		
		$id = $obj->MySQLQueryPerform($tbl_name,$updateData,'update',$where);
		if($eMemberType == "Passenger"){
			$profileData =  getPassengerDetailInfo($iUserId);
			}else{
			$profileData =  getDriverDetailInfo($iUserId);
		}
		
		if($id >0){
			$returnArr['Action'] = "1";
			$returnArr['message'] = $profileData;
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		/* if($vStripeCusId != ""){
			$customer 	= Stripe_Customer::retrieve($vStripeCusId);
			$sources = $customer -> sources;
			$data = $sources -> data;
			// print_r($sources);
			// echo "<br/>".$data[0]['id'];exit;
			
			if(count($data) >0 && $data[0]['id'] != ''){
			$customer->sources->retrieve($data[0]['id'])->delete();
			}
			
			$card = $customer->sources->create(array("source" => $vStripeToken));
			
			$where = " $iMemberId = '$iUserId'";
			$data_user['vStripeToken']=$vStripeToken;
			$data_user['vCreditCard']=$CardNo;
			
			$id = $obj->MySQLQueryPerform($tbl_name,$data_user,'update',$where);
			if($eMemberType == "Passenger"){
			$profileData =  getPassengerDetailInfo($iUserId);
			}else{
			$profileData =  getDriverDetailInfo($iUserId);
			}
			
			if($id >0){
			$returnArr['Action'] = "1";
			$returnArr['message'] = $profileData;
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
			}
			
			}else{
			try{
			$customer 	= Stripe_Customer::create(array( "source" => $vStripeToken, "email" => $vEmail));
			$vStripeCustomerId = $customer->id;
			
			$where = " $iMemberId = '$iUserId'";
			$data['vStripeToken']=$vStripeToken;
			$data['vStripeCusId']=$vStripeCustomerId;
			$data['vCreditCard']=$CardNo;
			
			$id = $obj->MySQLQueryPerform($tbl_name,$data,'update',$where);
			if($eMemberType == "Passenger"){
			$profileData =  getPassengerDetailInfo($iUserId);
			}else{
			$profileData =  getDriverDetailInfo($iUserId);
			}
			
			if($id >0){
			$returnArr['Action'] = "1";
			$returnArr['message'] = $profileData;
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
			}
			
			} catch (Exception $e) {
			$error3 = $e->getMessage();
			$returnArr['Action'] = "0";
			$returnArr['message']=$error3;
			//$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
			}
			
		} */
		echo json_encode($returnArr);
	}
	
	###########################################################################
	
	if($type == "CheckCard"){
		$iUserId     = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		
		$vStripeCusId = get_value('register_user', 'vStripeCusId', 'iUserId', $iUserId,'','true');
		
		if($vStripeCusId != ""){
			
			try{
				$customer 	= Stripe_Customer::retrieve($vStripeCusId);
				$sources = $customer -> sources;
				$data = $sources -> data;
				
				$cvc_check = $data[0]['cvc_check'];
				
				if($cvc_check && $cvc_check == "pass"){
					$returnArr['Action'] = "1";
					}else{
					$returnArr['Action'] = "0";
					$returnArr['message']="LBL_INVALID_CARD";
				}
				}catch (Exception $e) {
				$error3 = $e->getMessage();
				$returnArr['Action'] = "0";
				$returnArr['message']=$error3;
				//$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
				
			}
			
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		echo json_encode($returnArr);
	}
	
	###########################################################################
	
	if($type == "getDriverRideHistory"){
		$iDriverId     = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$date     = isset($_REQUEST["date"]) ? $_REQUEST["date"] : '';
		$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		$date = $date." "."12:01:00";
		$date = date("Y-m-d H:i:s",strtotime($date)); 
		$serverTimeZone = date_default_timezone_get();
		$date = converToTz($date,$serverTimeZone,$vTimeZone,"Y-m-d");  
		
		/*$vCurrencyDriver=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iDriverId,'','true');
		$vLanguage=get_value('register_driver', 'vLang', 'iDriverId',$iDriverId,'','true');*/
    $DriverDetail = get_value('register_driver', 'vCurrencyDriver,vLang', 'iDriverId', $iDriverId);
    $vCurrencyDriver = $DriverDetail[0]['vCurrencyDriver'];
    $vLanguage = $DriverDetail[0]['vLang'];
		// $currencySymbol=get_value('currency', 'vSymbol', 'eDefault', 'Yes','','true');
		// $priceRatio=1;
		// $fRatioDriver = get_value('currency', 'Ratio', 'vName', $vCurrencyDriver,'','true');
		$currencySymbol = get_value('currency', 'vSymbol', 'vName', $vCurrencyDriver,'','true');
		
		
		if($vLanguage == "" || $vLanguage == NULL){
			$vLanguage = "EN";
		}
		
		//$sql = "SELECT tr.*, rate.vRating1, rate.vMessage,ru.vName,ru.vLastName,ru.vImgName as vImage FROM trips as tr,ratings_user_driver as rate,register_user as ru WHERE tr.iDriverId='$iDriverId' AND tr.tTripRequestDate LIKE '".$date."%' AND tr.iActive='Finished' AND rate.iTripId = tr.iTripId AND rate.eUserType='Passenger' AND ru.iUserId=tr.iUserId";
		$sql = "SELECT tr.*, ru.vName,ru.vLastName,ru.vImgName as vImage FROM trips as tr,register_user as ru WHERE tr.iDriverId='$iDriverId' AND tr.tTripRequestDate LIKE '".$date."%' AND tr.iActive='Finished' AND ru.iUserId=tr.iUserId ORDER By tr.iTripId DESC";
		
		$tripData = $obj->MySQLSelect($sql);
		
		$totalEarnings = 0;
		$avgRating = 0;
		
		if(count($tripData) > 0){
			
			for($i=0;$i<count($tripData);$i++){
				// $iFare = $tripData[$i]['fTripGenerateFare']-$tripData[$i]['fTollPrice'];
				$iFare = $tripData[$i]['fTripGenerateFare'];
				//$iFare = $tripData[$i]['fTripGenerateFare'];
				$fCommision = $tripData[$i]['fCommision'];
				$fDiscount = $tripData[$i]['fDiscount'];
				$fTipPrice = $tripData[$i]['fTipPrice']; 
				$fTollPrice = $tripData[$i]['fTollPrice'];
        $fTax1 = $tripData[$i]['fTax1'];
        $fTax2 = $tripData[$i]['fTax2'];
				//$vRating1 = $tripData[$i]['vRating1'];
				$priceRatio = $tripData[$i]['fRatio_'.$vCurrencyDriver];
				
				$sql = "SELECT vRating1, vMessage FROM ratings_user_driver WHERE iTripId = '".$tripData[$i]['iTripId']."' AND eUserType='Passenger'";
			    $tripData_rating = $obj->MySQLSelect($sql);
				if(count($tripData_rating) > 0){
					$tripData[$i]['vRating1'] = $tripData_rating[0]['vRating1'];
					$tripData[$i]['vMessage'] = $tripData_rating[0]['vMessage'];
					$vRating1 = $tripData_rating[0]['vRating1'];
					}else{
					$tripData[$i]['vRating1'] = "0";
					$tripData[$i]['vMessage'] = "";
					$vRating1 = 0;
				}
				
				if(($iFare == "" || $iFare == 0) && $fDiscount > 0){
					$incValue = ($fDiscount - $fCommision - $fTax1 - $fTax2)+$fTipPrice;
					$totalEarnings = $totalEarnings + ($incValue * $priceRatio);
					}else if($iFare != "" && $iFare > 0){
					$incValue = ($iFare - $fCommision - $fTax1 - $fTax1)+$fTipPrice;
					$totalEarnings = $totalEarnings + ($incValue * $priceRatio);
				}
				
				$avgRating = $avgRating + $vRating1;
				
				$returnArr = getTripPriceDetails($tripData[$i]['iTripId'],$iDriverId,"Driver");
				$tripData[$i] = array_merge($tripData[$i], $returnArr);
				
				$eType = $tripData[$i]['eType']; 
				$iVehicleTypeId = $tripData[$i]['iVehicleTypeId'];
				$eFareType = get_value('vehicle_type', 'eFareType', 'iVehicleTypeId', $iVehicleTypeId,'','true');
				if($eType == 'UberX' &&  $eFareType != "Regular"){
					$tripData[$i]['tDaddress'] = "";
				}
			}
			
			$returnArr['Action'] = "1";
			$returnArr['message'] = $tripData;
			
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] = "LBL_NO_DATA_AVAIL";
		}
		$returnArr['TotalEarning'] = strval(round($totalEarnings,2));
		$returnArr['TripDate'] = date('l, dS M Y',strtotime($date));
		$returnArr['TripCount'] = strval(count($tripData));
		//$returnArr['AvgRating'] = strval(round(count($tripData) == 0? 0 : ($avgRating/count($tripData)),2));
    $returnArr['AvgRating'] = strval(getMemberAverageRating($iDriverId,"Driver",$date));
		$returnArr['CurrencySymbol'] = $currencySymbol;
		
		echo json_encode($returnArr);
		
	}
	###########################################################################
	
	if ($type == "loadDriverFeedBack") {
		global $generalobj,$tconfig;
		
		$page        = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		
		$vAvgRating =  get_value('register_driver', 'vAvgRating', 'iDriverId', $iDriverId,'','true');
		
		$per_page = 10;
		$sql_all  = "SELECT COUNT(iTripId) As TotalIds FROM trips WHERE  iDriverId='$iDriverId' AND iActive='Finished' AND eHailTrip='No'";
		
		
		$data_count_all = $obj->MySQLSelect($sql_all);
		
		$TotalPages = ceil($data_count_all[0]['TotalIds'] / $per_page);
		
		$start_limit = ($page - 1) * $per_page;
		$limit       = " LIMIT " . $start_limit . ", " . $per_page;
		
		//$sql  = "SELECT rate.*,DATE_FORMAT(rate.tDate, '%M, %Y') AS tDate FROM ratings_user_driver as rate, trips as tr WHERE  rate.iTripId = tr.iTripId AND tr.iDriverId='$iDriverId' AND tr.iActive='Finished' AND rate.eUserType='Passenger' ORDER BY tr.iTripId DESC". $limit;
		$sql  = "SELECT rate.*,CONCAT(ru.vName,' ',ru.vLastName) as vName,ru.iUserId as passengerid,ru.vImgName FROM ratings_user_driver as rate LEFT JOIN trips as tr ON tr.iTripId = rate.iTripId  LEFT JOIN register_user as ru ON ru.iUserId = tr.iUserId WHERE tr.iDriverId='$iDriverId' AND tr.iActive='Finished' AND tr.eHailTrip='No' AND rate.eUserType='Passenger' ORDER BY tr.iTripId DESC". $limit;
		
		$Data = $obj->MySQLSelect($sql);
		for($i=0;$i<count($Data);$i++){
			$Data[$i]['vImage'] = $tconfig["tsite_upload_images_passenger"].'/'.$Data[$i]['passengerid'].'/3_'.$Data[$i]['vImgName']; 
			$Data[$i]['tDateOrig'] = $Data[$i]['tDate'];
			$Data[$i]['tDate'] = $generalobj->DateTime($Data[$i]['tDate'], 14);
		}
		$totalNum = count($Data);
		
		if ( count($Data) > 0 ) {
			
			$returnData['message']=$Data;
			if ($TotalPages > $page) {
				$returnData['NextPage'] = $page + 1;
				} else {
				$returnData['NextPage'] = "0";
			}
			$returnData['vAvgRating']=strval($vAvgRating);
			$returnData['Action']="1";
			echo json_encode($returnData);
			
			}else{
			$returnData['Action']="0";
			$returnData['message']="LBL_NO_FEEDBACK";
			echo json_encode($returnData);
		}
		
	}
	
	###########################################################################
	
	if ($type == "loadEmergencyContacts") {
		global $generalobj;
		
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '0';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : '';
		$GeneralUserType = isset($_REQUEST["GeneralUserType"]) ? $_REQUEST["GeneralUserType"] : 'Passenger';
		
		if($UserType == ""){
			$UserType = $GeneralUserType;
		}
		//$data = get_value('user_emergency_contact', '*', 'iUserId', $iUserId);
		//$data = get_value('user_emergency_contact', '*', 'eUserType', $UserType,'','true');
		$sql = "SELECT * FROM user_emergency_contact WHERE iUserId='".$iUserId."' AND eUserType = '".$UserType."'";
		$data = $obj->MySQLSelect($sql);
		
		if(count($data) >0){
			$returnData['Action']="1";
			$returnData['message']= $data;
			}else{
			$returnData['Action']="0";
		}
		echo json_encode($returnData);
	}
	
	###########################################################################
	
	if ($type == "addEmergencyContacts") {
		global $generalobj;
		
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '0';
		$Phone = isset($_REQUEST["Phone"]) ? $_REQUEST["Phone"] : '0';
		$vName = isset($_REQUEST["vName"]) ? $_REQUEST["vName"] : '0';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		
		$sql  = "SELECT vPhone FROM user_emergency_contact WHERE iUserId = '".$iUserId."' AND vPhone='".$Phone."' AND eUserType='".$UserType."'";
		
		$Data_Exist = $obj->MySQLSelect($sql);
		
		if(count($Data_Exist) > 0){
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_EME_CONTACT_EXIST";
			}else{
			$Data['vName']=$vName;
			$Data['vPhone']=$Phone;
			$Data['iUserId']=$iUserId;
			$Data['eUserType']=$UserType;
			
			$id = $obj->MySQLQueryPerform("user_emergency_contact",$Data,'insert');
			
			if($id >0){
				$returnArr['Action']="1";
				$returnArr['message']="LBL_EME_CONTACT_LIST_UPDATE";
				}else{
				$returnArr['Action'] = "0";
				$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
			}
		}
		
		echo json_encode($returnArr);
	}
	
	###########################################################################
	
	if ($type == "deleteEmergencyContacts") {
		global $generalobj;
		
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '0';
		$iEmergencyId = isset($_REQUEST["iEmergencyId"]) ? $_REQUEST["iEmergencyId"] : '0';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		
		$sql = "DELETE FROM user_emergency_contact WHERE `iEmergencyId`='".$iEmergencyId."' AND `iUserId`='".$iUserId."' AND eUserType = '".$UserType."'";
		$id = $obj->sql_query($sql);
		// echo "ID:".$id;exit;
		if($id >0){
			$returnArr['Action']="1";
			$returnArr['message']="LBL_EME_CONTACT_LIST_UPDATE";
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
	}
	
	###########################################################################
	if ($type == "sendAlertToEmergencyContacts") {
		global $generalobj,$obj;
		
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '0';
		$iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '0';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		
		$sql  = "SELECT * FROM user_emergency_contact WHERE iUserId = '".$iUserId."' AND eUserType='".$UserType."'";
		
		$dataArr = $obj->MySQLSelect($sql);
		if($iTripId == "" || $iTripId == "0"){
			$tableName = $UserType != "Driver"?"register_user":"register_driver";
			$iMemberId_KEY = $UserType != "Driver"?"iUserId":"iDriverId";
			$iTripId=get_value($tableName, 'iTripId', $iMemberId_KEY,$iUserId,'','true');
		}
		
		if(count($dataArr) >0){
			$sql = "SELECT tr.*,dv.vLicencePlate,CONCAT(rd.vName,' ',rd.vLastName) as vDriverName,rd.vPhone as DriverPhone,CONCAT(ru.vName,' ',ru.vLastName) as vPassengerName,ru.vPhone as PassengerPhone FROM trips as tr, register_driver as rd, register_user as ru, driver_vehicle as dv WHERE tr.iTripId = '".$iTripId."' AND rd.iDriverId = tr.iDriverId AND ru.iUserId = tr.iUserId AND dv.iDriverVehicleId = tr.iDriverVehicleId";
			
			$tripData= $obj->MySQLSelect($sql);
      $tripData[0]['tStartDate'] = ($tripData[0]['tStartDate'] == '0000-00-00 00:00:00')? $tripData[0]['tTripRequestDate'] : $tripData[0]['tStartDate'];
			
			//$isdCode= $generalobj->getConfigurations("configurations","SITE_ISD_CODE");
			$isdCode= $SITE_ISD_CODE;
			if($APP_TYPE == "UberX"){
				if($UserType == "Passenger"){
					$message = "Important: ".$tripData[0]['vPassengerName'].' ('.$tripData[0]['PassengerPhone'].') has reached out to you via '.$SITE_NAME.' SOS. Please reach out to him/her urgently. The details of the Job are: Job start time: '.date('dS M \a\t h:i a',strtotime($tripData[0]['tTripRequestDate'])).'. Job Address: '.$tripData[0]['tSaddress'].'. Service Provider name: '.$tripData[0]['vDriverName'].'. Service Provider number:('.$tripData[0]['DriverPhone'].")";
				} else {
					$message = "Important: ".$tripData[0]['vDriverName'].' ('.$tripData[0]['DriverPhone'].') has reached out to you via '.$SITE_NAME.' SOS. Please reach out to him/her urgently. The details of the Job are: Job start time: '.date('dS M Y \a\t h:i a',strtotime($tripData[0]['tStartDate'])).'. Job Address: '.$tripData[0]['tSaddress'].'. User name: '.$tripData[0]['vPassengerName'].'. User number:('.$tripData[0]['PassengerPhone'].")";
				}
			} else {
				if($UserType == "Passenger"){
					$message = "Important: ".$tripData[0]['vPassengerName'].' ('.$tripData[0]['PassengerPhone'].') has reached out to you via '.$SITE_NAME.' SOS. Please reach out to him/her urgently. The details of the ride are: Trip start time: '.date('dS M Y \a\t h:i a',strtotime($tripData[0]['tStartDate'])).'. Pick up from: '.$tripData[0]['tSaddress'].'. Driver name: '.$tripData[0]['vDriverName'].'. Driver number:('.$tripData[0]['DriverPhone']."). Driver's car number: ".$tripData[0]['vLicencePlate'];
				} else {
					$message = "Important: ".$tripData[0]['vDriverName'].' ('.$tripData[0]['DriverPhone'].') has reached out to you via '.$SITE_NAME.' SOS. Please reach out to him/her urgently. The details of the ride are: Trip start time: '.date('dS M Y \a\t h:i a',strtotime($tripData[0]['tStartDate'])).'. Pick up from: '.$tripData[0]['tSaddress'].'. Passenger name: '.$tripData[0]['vPassengerName'].'. Passenger number:('.$tripData[0]['PassengerPhone']."). Driver's car number: ".$tripData[0]['vLicencePlate'];
				}
			}			
			
			for($i=0;$i<count($dataArr);$i++){
				$phone = preg_replace("/[^0-9]/", "", $dataArr[$i]['vPhone']);
				
				$toMobileNum= "+".$phone;
				
				$result = sendEmeSms($toMobileNum,$message);
				if($result ==0){
					$toMobileNum = "+".$isdCode.$phone;
					sendEmeSms($toMobileNum,$message);
				}
			}
			
			$returnArr['Action']="1";
			$returnArr['message']="LBL_EME_CONTACT_ALERT_SENT";
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_ADD_EME_CONTACTS";
		}
		
		
		echo json_encode($returnArr);
	}
	
	
	###########################################################################
	
	if($type== "ScheduleARide"){
		$iCabBookingId= isset($_REQUEST["iCabBookingId"]) ? $_REQUEST["iCabBookingId"] : '';
		$iUserId =  isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$pickUpLocAdd =  isset($_REQUEST["pickUpLocAdd"]) ? $_REQUEST["pickUpLocAdd"] : '';
		$pickUpLatitude =  isset($_REQUEST["pickUpLatitude"]) ? $_REQUEST["pickUpLatitude"] : '';
		$pickUpLongitude =  isset($_REQUEST["pickUpLongitude"]) ? $_REQUEST["pickUpLongitude"] : '';
		$destLocAdd =  isset($_REQUEST["destLocAdd"]) ? $_REQUEST["destLocAdd"] : '';
		$destLatitude =  isset($_REQUEST["destLatitude"]) ? $_REQUEST["destLatitude"] : '';
		$destLongitude =  isset($_REQUEST["destLongitude"]) ? $_REQUEST["destLongitude"] : '';
		$scheduleDate =  isset($_REQUEST["scheduleDate"]) ? $_REQUEST["scheduleDate"] : '';
		$iVehicleTypeId =  isset($_REQUEST["iVehicleTypeId"]) ? $_REQUEST["iVehicleTypeId"] : '';
		// $timeZone =  isset($_REQUEST["TimeZone"]) ? $_REQUEST["TimeZone"] : '';
		$eType =  isset($_REQUEST["eType"]) ? $_REQUEST["eType"] : '';
		$iPackageTypeId =  isset($_REQUEST["iPackageTypeId"]) ? $_REQUEST["iPackageTypeId"] : '';
		$vReceiverName =  isset($_REQUEST["vReceiverName"]) ? $_REQUEST["vReceiverName"] : '';
		$vReceiverMobile =  isset($_REQUEST["vReceiverMobile"]) ? $_REQUEST["vReceiverMobile"] : '';
		$tPickUpIns =  isset($_REQUEST["tPickUpIns"]) ? $_REQUEST["tPickUpIns"] : '';
		$tDeliveryIns =  isset($_REQUEST["tDeliveryIns"]) ? $_REQUEST["tDeliveryIns"] : '';
		$tPackageDetails =  isset($_REQUEST["tPackageDetails"]) ? $_REQUEST["tPackageDetails"] : '';
		$vCouponCode =  isset($_REQUEST["PromoCode"]) ? $_REQUEST["PromoCode"] : '';
		$iUserPetId =  isset($_REQUEST["iUserPetId"]) ? $_REQUEST["iUserPetId"] : '';
		$cashPayment    =isset($_REQUEST["CashPayment"]) ? $_REQUEST["CashPayment"] : '';
		$quantity    =isset($_REQUEST["Quantity"]) ? $_REQUEST["Quantity"] : '';
		$fTollPrice = isset($_REQUEST["fTollPrice"]) ? $_REQUEST["fTollPrice"] : '';
		$vTollPriceCurrencyCode = isset($_REQUEST["vTollPriceCurrencyCode"]) ? $_REQUEST["vTollPriceCurrencyCode"] : '';
		$eTollSkipped = isset($_REQUEST["eTollSkipped"]) ? $_REQUEST["eTollSkipped"] : 'Yes';
		$HandicapPrefEnabled = isset($_REQUEST["HandicapPrefEnabled"]) ? $_REQUEST["HandicapPrefEnabled"] : '';
		$PreferFemaleDriverEnable = isset($_REQUEST["PreferFemaleDriverEnable"]) ? $_REQUEST["PreferFemaleDriverEnable"] : '';
		//$eAutoAssign    = 'Yes';
		$iDriverId    =isset($_REQUEST["SelectedDriverId"]) ? $_REQUEST["SelectedDriverId"] : '';
		$vTimeZone    =isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		$iUserAddressId = isset($_REQUEST["iUserAddressId"]) ? $_REQUEST["iUserAddressId"] : '0';
		$tUserComment = isset($_REQUEST["tUserComment"]) ? $_REQUEST["tUserComment"] : '';
		
		$action = ($iCabBookingId != "") ? 'Edit' : 'Add';
    
		// $paymentMode =  isset($_REQUEST["paymentMode"]) ? $_REQUEST["paymentMode"] : 'Cash'; // Cash OR Card
		// $paymentMode = "Cash";
		// $paymentMode = $eType == "Deliver" ?"Card":"Cash";
		if($cashPayment=='true'){
			$paymentMode="Cash";
			}else{
			$paymentMode="Card";
		}
		
		checkmemberemailphoneverification($iUserId,"Passenger");
		## Check Pickup Address For UberX##
		//$APP_TYPE = $generalobj->getConfigurations("configurations","APP_TYPE");
		if($APP_TYPE == "UberX"){
			$Data['tUserComment']=$tUserComment;
			
			if($iUserAddressId != ""){	
				//$pickUpLocAdd=get_value('user_address', 'vServiceAddress', '	iUserAddressId',$iUserAddressId,'','true');
				$Address=get_value('user_address', 'vAddressType,vBuildingNo,vLandmark,vServiceAddress,vLatitude,vLongitude', '	iUserAddressId',$iUserAddressId,'','');
				$vAddressType = $Address[0]['vAddressType'];
				$vBuildingNo = $Address[0]['vBuildingNo'];
				$vLandmark = $Address[0]['vLandmark'];
				$vServiceAddress = $Address[0]['vServiceAddress']; 
				$pickUpLocAdd = ($vAddressType != "")? $vAddressType."\n" :"";
				$pickUpLocAdd .= ($vBuildingNo != "")? $vBuildingNo."," :"";
				$pickUpLocAdd .= ($vLandmark != "")? $vLandmark."\n" :"";
				$pickUpLocAdd .= ($vServiceAddress != "")? $vServiceAddress :"";
				$Data['vSourceAddresss']=$pickUpLocAdd;
				$Data['iUserAddressId']=$iUserAddressId;     
        $pickUpLatitude = $Address[0]['vLatitude'];
        $pickUpLongitude = $Address[0]['vLongitude'];    
				}else{
				$Data['vSourceAddresss']=$pickUpLocAdd;
			}
			$eAutoAssign    = 'No';     
			}else{
			$Data['vSourceAddresss']=$pickUpLocAdd;
			$eAutoAssign    = 'Yes';
		}    
    ### Checking For Pickup And DropOff Disallow ###
    $pickuplocationarr = array($pickUpLatitude,$pickUpLongitude);
    $dropofflocationarr = array($destLatitude,$destLongitude);
    $allowed_ans_pickup = checkAllowedAreaNew($pickuplocationarr,"No");
    if($allowed_ans_pickup == "No"){
			$returnArr['Action'] = "0";
  		$returnArr['message'] = "LBL_PICKUP_LOCATION_NOT_ALLOW";
  		echo json_encode($returnArr);
  		exit;
		}            
    if($destLatitude != "" && $destLongitude !=""){
      $allowed_ans_dropoff = checkAllowedAreaNew($dropofflocationarr,"Yes");
      if($allowed_ans_dropoff == "No"){
  			$returnArr['Action'] = "0";
    		$returnArr['message'] = "LBL_DROP_LOCATION_NOT_ALLOW";
    		echo json_encode($returnArr);
    		exit;
  		}
    }  
    ### Checking For Pickup And DropOff Disallow ###
		## Check Pickup Address For UberX##     
		## Check For PichUp/DropOff Location DisAllow ##
		$address_data['PickUpAddress'] = $pickUpLocAdd;
		$address_data['DropOffAddress'] = $destLocAdd;
		$DataArr = getOnlineDriverArr($pickUpLatitude,$pickUpLongitude,$address_data,"Yes","No","No","",$destLatitude,$destLongitude);
		if($DataArr['PickUpDisAllowed'] == "No" && $DataArr['DropOffDisAllowed'] == "No"){
			$returnArr['Action'] = "0";
  			$returnArr['message'] = "LBL_PICK_DROP_LOCATION_NOT_ALLOW";
  			echo json_encode($returnArr);
  			exit;
		}
		if($DataArr['PickUpDisAllowed'] == "Yes" && $DataArr['DropOffDisAllowed'] == "No"){
			$returnArr['Action'] = "0";
  			$returnArr['message'] = "LBL_DROP_LOCATION_NOT_ALLOW";
  			echo json_encode($returnArr);
  			exit;
		}
		if($DataArr['PickUpDisAllowed'] == "No" && $DataArr['DropOffDisAllowed'] == "Yes"){
			$returnArr['Action'] = "0";
  			$returnArr['message'] = "LBL_PICKUP_LOCATION_NOT_ALLOW";
  			echo json_encode($returnArr);
  			exit;
		}
		## Check For PichUp/DropOff Location DisAllow Ends##
		if($APP_TYPE == "UberX"){
			$sdate = explode(" ",$scheduleDate);
			$shour = explode("-",$sdate[1]);
			$shour1 = $shour[0];   
      $shour2 = $shour[1];
      if($shour1 == "12" && $shour2 == "01"){
        $shour1 = 00;
      }   
			$scheduleDate = $sdate[0]." ".$shour1.":00:00";
			$currentdate = date("Y-m-d H:i:s");
			$datediff = strtotime($scheduleDate)-strtotime($currentdate);
			/*if($datediff < 3600){
				$returnArr['Action'] = "0";
				$returnArr['message'] = "LBL_SCHEDULE_TIME_NOT_AVAILABLE";
				echo json_encode($returnArr);
				exit;
			} */
		}
		$Booking_Date_Time = $scheduleDate;
		$systemTimeZone = date_default_timezone_get();
		// echo "hererrrrr:::".$systemTimeZone;exit;
		$scheduleDate = converToTz($scheduleDate,$systemTimeZone,$vTimeZone);
		// $pickUpDateTime = convertTimeZone("2016-29-14 15:29:41","Asia/Calcutta");
		
		// date_default_timezone_set($timeZone);
		// echo gmdate('Y-m-d H:i', strtotime($scheduleDate));exit;
		
		// echo "hererrrrr:::".$pickUpDateTime;exit;
		/*$ePickStatus=get_value('vehicle_type', 'ePickStatus', 'iVehicleTypeId',$iVehicleTypeId,'','true');
		$eNightStatus=get_value('vehicle_type', 'eNightStatus', 'iVehicleTypeId',$iVehicleTypeId,'','true');*/
    $SurchargeDetail = get_value('vehicle_type', 'ePickStatus,eNightStatus', 'iVehicleTypeId', $iVehicleTypeId);
    $ePickStatus = $SurchargeDetail[0]['ePickStatus'];
    $eNightStatus = $SurchargeDetail[0]['eNightStatus']; 
		
		$fPickUpPrice = 1;
		$fNightPrice = 1;
		
    ## Checking For Flat Trip ##
    $data_flattrip['eFlatTrip'] = "No";
    $data_flattrip['Flatfare'] = 0;
    $eFlatTrip = $data_flattrip['eFlatTrip']; 
    $fFlatTripPrice = $data_flattrip['Flatfare'];
    ## Checking For Flat Trip ##
		$data_surgePrice = checkSurgePrice($selectedCarTypeID,$scheduleDate);
		
		if($data_surgePrice['Action'] == "0"){
			if($data_surgePrice['message'] == "LBL_PICK_SURGE_NOTE"){
				$fPickUpPrice=$data_surgePrice['SurgePriceValue'];
				}else{
				$fNightPrice=$data_surgePrice['SurgePriceValue'];
			}
		}
    if($APPLY_SURGE_ON_FLAT_FARE == "No" && $data_flattrip['eFlatTrip'] == "Yes"){
      $fPickUpPrice = 1;
		  $fNightPrice = 1;
    }
		if($eTollSkipped=='No' || $fTollPrice != "")
		{
			$fTollPrice_Original = $fTollPrice;
			$vTollPriceCurrencyCode = strtoupper($vTollPriceCurrencyCode);
			$default_currency = get_value('currency', 'vName', 'eDefault', 'Yes','','true');
			$sql=" SELECT round(($fTollPrice/(SELECT Ratio FROM currency where vName='".$vTollPriceCurrencyCode."'))*(SELECT Ratio FROM currency where vName='".$default_currency."' ) ,2)  as price FROM currency  limit 1";
			$result = $obj->MySQLSelect($sql);
			$fTollPrice = $result[0]['price'];
			if($fTollPrice == 0){
				$fTollPrice = get_currency($vTollPriceCurrencyCode,$default_currency,$fTollPrice_Original);
			}
			$Data['fTollPrice']=$fTollPrice;
			$Data['vTollPriceCurrencyCode']=$vTollPriceCurrencyCode;
			$Data['eTollSkipped']=$eTollSkipped;
			}else{
			$Data['fTollPrice']="0";
			$Data['vTollPriceCurrencyCode']="";
			$Data['eTollSkipped']="No";
		}
		
		$rand_num = rand ( 10000000 , 99999999 );
		
		/*$Booking_Date = @date('d-m-Y',strtotime($scheduleDate));    
		$Booking_Time = @date('H:i:s',strtotime($scheduleDate));*/
		$Booking_Date = @date('d-m-Y',strtotime($Booking_Date_Time));    
		$Booking_Time = @date('H:i:s',strtotime($Booking_Date_Time));
		$Data['iUserId']=$iUserId;
		$Data['vSourceLatitude']=$pickUpLatitude;
		$Data['vSourceLongitude']=$pickUpLongitude;
		$Data['vDestLatitude']=$destLatitude;
		$Data['vDestLongitude']=$destLongitude;
		//$Data['vSourceAddresss']=$pickUpLocAdd;
		$Data['tDestAddress']=$destLocAdd;
		$Data['ePayType']=$paymentMode;
		$Data['iVehicleTypeId']=$iVehicleTypeId;
		$Data['dBooking_date']=date('Y-m-d H:i', strtotime($scheduleDate));
		$Data['eCancelBy']="";
		$Data['fPickUpPrice']=$fPickUpPrice;
		$Data['fNightPrice']=$fNightPrice;
		$Data['eType']=$eType;
		$Data['iUserPetId']=$iUserPetId;
		$Data['iQty']=$quantity;
		$Data['vCouponCode']=$vCouponCode;
		$Data['eAutoAssign']=$eAutoAssign;
		$Data['vRideCountry']=$vCountryCode;
		// $Data['fTollPrice']=$fTollPrice;
		// $Data['vTollPriceCurrencyCode']=$vTollPriceCurrencyCode;
		// $Data['eTollSkipped']=$eTollSkipped;
		$Data['iDriverId']=$iDriverId;
		$Data['vTimeZone']=$vTimeZone;
		$Data['eFemaleDriverRequest']=$PreferFemaleDriverEnable;
		$Data['eHandiCapAccessibility']=$HandicapPrefEnabled;
    $Data['eFlatTrip']=$eFlatTrip; 
    $Data['fFlatTripPrice']=$fFlatTripPrice;
		if($eType == "Deliver"){
			$Data['iPackageTypeId']=$iPackageTypeId;
			$Data['vReceiverName']=$vReceiverName;
			$Data['vReceiverMobile']=$vReceiverMobile;
			$Data['tPickUpIns']=$tPickUpIns;
			$Data['tDeliveryIns']=$tDeliveryIns;
			$Data['tPackageDetails']=$tPackageDetails;
		}
		if ($action == "Add") {
			$Data['vBookingNo']=$rand_num; 
			$id = $obj->MySQLQueryPerform("cab_booking",$Data,'insert');
			} else {
			$Data['eStatus']="Pending";
			$Data['iCancelByUserId']="";
			$Data['vCancelReason']="";
			$where = " iCabBookingId = '" . $iCabBookingId . "'";
			$id = $obj->MySQLQueryPerform("cab_booking", $Data, 'update', $where);
		}
		
		if($id > 0){
			$returnArr["Action"] = "1";
			if($APP_TYPE == "UberX"){
				$returnArr['message']= "LBL_BOOKING_SUCESS_NOTE";
				}else{
				$returnArr['message']= $eType == "Deliver" ?"LBL_DELIVERY_BOOKED":"LBL_RIDE_BOOKED";
			}
			$sql = "SELECT concat(vName,' ',vLastName) as senderName,vEmail,vPhone,vPhoneCode,vLang from  register_user  WHERE iUserId ='".$iUserId."'";
			$userdetail = $obj->MySQLSelect($sql);
			$sql = "SELECT concat(vName,' ',vLastName) as drivername,vEmail,vPhone,vcode,iDriverVehicleId,vLang from  register_driver  WHERE iDriverId ='".$iDriverId."'";
			$driverdetail = $obj->MySQLSelect($sql);				
			$userPhoneNo = $userdetail[0]['vPhone'];
			$userPhoneCode = $userdetail[0]['vPhoneCode'];
			$UserLang=$userdetail[0]['vLang'];
			$DriverPhoneNo = $driverdetail[0]['vPhone'];
			$DriverPhoneCode = $driverdetail[0]['vcode'];
			$DriverLang=$driverdetail[0]['vLang'];
			$Data1['vRider']=$userdetail[0]['senderName'];
			$Data1['vDriver']=$driverdetail[0]['drivername'];
			$Data1['vDriverMail']=$driverdetail[0]['vEmail'];
			$Data1['vRiderMail']=$userdetail[0]['vEmail'];
			$Data1['vSourceAddresss']=$pickUpLocAdd;
			//$Data1['tDestAddress']=$destLocAdd;
			//$Data1['dBookingdate']=date('Y-m-d H:i', strtotime($scheduleDate));
			$Data1['dBookingdate']=date('Y-m-d H:i', strtotime($Booking_Date_Time));
			if ($action == "Add") {
				$Data1['vBookingNo']=$rand_num;	
				}else{
				$BookingNo=get_value('cab_booking', 'vBookingNo', 'iCabBookingId',$iCabBookingId,'','true');
				$Data1['vBookingNo']=$BookingNo; 
			}	
			$query = "SELECT vLicencePlate FROM driver_vehicle WHERE iDriverVehicleId=".$iVehicleTypeId; 
			$db_driver_vehicles = $obj->MySQLSelect($query);
			if($APP_TYPE == "UberX"){
				$sendMailfromDriver = $generalobj->send_email_user("MANUAL_TAXI_DISPATCH_DRIVER_APP_SP",$Data1);
				}else{
				$sendMailfromDriver = $generalobj->send_email_user("MANUAL_TAXI_DISPATCH_DRIVER_APP",$Data1);
				$sendMailfromUser = $generalobj->send_email_user("MANUAL_TAXI_DISPATCH_RIDER_APP",$Data1);			
			}
			if($APP_TYPE != "UberX"){  		
				$maildata['DRIVER_NAME'] = $Data1['vDriver'];     
				//$maildata['PLATE_NUMBER'] = $db_driver_vehicles[0]['vLicencePlate'];
				$maildata['BOOKING_DATE'] = $Booking_Date;      
				$maildata['BOOKING_TIME'] =  $Booking_Time;      
				$maildata['BOOKING_NUMBER'] = $Data1['vBookingNo'];
				$message_layout = $generalobj->send_messages_user("USER_SEND_MESSAGE_APP",$maildata,"",$UserLang);			
				$UsersendMessage = $generalobj->sendUserSMS($userPhoneNo,$userPhoneCode,$message_layout,"");
				if($UsersendMessage == 0){
					//$isdCode= $generalobj->getConfigurations("configurations","SITE_ISD_CODE");
					$isdCode= $SITE_ISD_CODE;
					$userPhoneCode = $isdCode;
					$UsersendMessage = $generalobj->sendUserSMS($userPhoneNo,$userPhoneCode,$message_layout,"");
				}
			}  
			$maildata1['PASSENGER_NAME'] = $Data1['vRider'];      
			$maildata1['BOOKING_DATE'] = $Booking_Date;      
			$maildata1['BOOKING_TIME'] =  $Booking_Time;      
			$maildata1['BOOKING_NUMBER'] = $Data1['vBookingNo'];
			$DRIVER_SMS_TEMPLATE = ($APP_TYPE == "UberX")?"DRIVER_SEND_MESSAGE_SP":"DRIVER_SEND_MESSAGE";
			$message_layout = $generalobj->send_messages_user($DRIVER_SMS_TEMPLATE,$maildata1,"",$DriverLang);
			$DriversendMessage = $generalobj->sendUserSMS($DriverPhoneNo,$DriverPhoneCode,$message_layout,"");
			if($DriversendMessage == 0){
				//$isdCode= $generalobj->getConfigurations("configurations","SITE_ISD_CODE");
				$isdCode= $SITE_ISD_CODE;
				$DriverPhoneCode = $isdCode;
				$UsersendMessage = $generalobj->sendUserSMS($DriverPhoneNo,$DriverPhoneCode,$message_layout,"");
			}
			}else{
			$returnArr["Action"] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
			
		}
		
		echo json_encode($returnArr);
	}
	
	
	
	###########################################################################
	
	if ($type == "checkBookings") {
		global $generalobj;
		
		$page        = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$bookingType = isset($_REQUEST["bookingType"]) ? $_REQUEST["bookingType"] : '';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$dataType = isset($_REQUEST["DataType"]) ? $_REQUEST["DataType"] : '';
		//$APP_TYPE = $generalobj->getConfigurations("configurations", "APP_TYPE");
		
		
		
		$per_page=10;
    $additional_mins = $BOOKING_LATER_ACCEPT_AFTER_INTERVAL;
		$currDate = date('Y-m-d H:i:s');
    $currDate = date("Y-m-d H:i:s", strtotime($currDate . "-".$additional_mins." minutes"));
		$ssql1 = " AND dBooking_date > '" . $currDate . "'";
		$ssql2 = " AND cb.dBooking_date > '" . $currDate . "'";
		
		if($UserType == "Driver"){
			if($APP_TYPE == "UberX"){
				if($dataType == "PENDING"){
					$sql_all  = "SELECT COUNT(iCabBookingId) As TotalIds FROM cab_booking WHERE iDriverId != '' AND eStatus = 'Pending' AND iDriverId='".$iDriverId."'".$ssql1;
					}else{
					$sql_all  = "SELECT COUNT(iCabBookingId) As TotalIds FROM cab_booking WHERE iDriverId != '' AND eStatus = 'Accepted' AND iDriverId='".$iDriverId."'". $ssql1;
				}
				}else{
				$sql_all  = "SELECT COUNT(iCabBookingId) As TotalIds FROM cab_booking WHERE iDriverId != '' AND eStatus = 'Assign' AND iDriverId='".$iDriverId."'".$ssql1;
			}  
			
			}else{
			$sql_all  = "SELECT COUNT(iCabBookingId) As TotalIds FROM cab_booking WHERE  iUserId='$iUserId' AND  ( eStatus = 'Assign' OR eStatus = 'Pending' OR eStatus = 'Accepted' OR eStatus = 'Declined' OR eStatus = 'Cancel') $ssql1";
		}
		
		$data_count_all = $obj->MySQLSelect($sql_all);
		$TotalPages = ceil($data_count_all[0]['TotalIds'] / $per_page);
		
		$start_limit = ($page - 1) * $per_page;
		$limit       = " LIMIT " . $start_limit . ", " . $per_page;
		
		if($UserType == "Driver"){
			if($APP_TYPE == "UberX"){	
				if($dataType == "PENDING"){
					$sql = "SELECT cb.* FROM `cab_booking` as cb  WHERE cb.iDriverId != '' AND  cb.eStatus = 'Pending' AND cb.iDriverId='$iDriverId' $ssql2 ORDER BY cb.iCabBookingId DESC" . $limit;
					}else{
					$sql = "SELECT cb.* FROM `cab_booking` as cb  WHERE cb.iDriverId != '' AND  cb.eStatus = 'Accepted' AND cb.iDriverId='$iDriverId' $ssql2 ORDER BY cb.iCabBookingId DESC" . $limit;
				}
				}else{
				$sql = "SELECT cb.* FROM `cab_booking` as cb  WHERE cb.iDriverId != '' AND  cb.eStatus = 'Assign' AND cb.iDriverId='$iDriverId' $ssql2 ORDER BY cb.iCabBookingId DESC" . $limit;
			}  
			
			}else{
			$sql = "SELECT cb.* FROM `cab_booking` as cb  WHERE cb.iUserId='$iUserId' AND ( cb.eStatus = 'Assign' OR cb.eStatus = 'Pending' OR eStatus = 'Accepted' OR eStatus = 'Declined'  OR eStatus = 'Cancel' ) $ssql2 ORDER BY cb.iCabBookingId DESC" . $limit;
		}
		$Data = $obj->MySQLSelect($sql);
		$totalNum = count($Data);
		
		if ( count($Data) > 0 ) {
			
			for($i=0;$i<count($Data);$i++){
				$Data[$i]['dBooking_dateOrig']=$Data[$i]['dBooking_date'];
				// Convert Into Timezone
				$tripTimeZone = $Data[0]['vTimeZone'];
				if($tripTimeZone != ""){
					$serverTimeZone = date_default_timezone_get();
					$Data[$i]['dBooking_dateOrig'] = converToTz($Data[$i]['dBooking_dateOrig'],$tripTimeZone,$serverTimeZone);
				}
				// Convert Into Timezone
				$Data[$i]['dBooking_date']=date('dS M Y \a\t h:i a',strtotime($Data[$i]['dBooking_date']));
				
				$eType = $Data[$i]['eType'];
				$iVehicleTypeId = $Data[$i]['iVehicleTypeId'];
				$eFareType = get_value('vehicle_type', 'eFareType', 'iVehicleTypeId', $iVehicleTypeId,'','true');
				$Data[$i]['eFareType'] = $eFareType;
				if($eType == 'UberX'){
					$Data[$i]['tDestAddress'] = "";
					$DisplayBookingDetails = array();
					$DisplayBookingDetails = DisplayBookingDetails($Data[$i]['iCabBookingId']);
					$Data[$i]['selectedtime'] = $DisplayBookingDetails['selectedtime'];
					$Data[$i]['selecteddatetime'] = $DisplayBookingDetails['selecteddatetime'];
					$Data[$i]['SelectedFareType'] = $DisplayBookingDetails['SelectedFareType'];
					$Data[$i]['SelectedQty'] = $DisplayBookingDetails['SelectedQty'];
					$Data[$i]['SelectedPrice'] = $DisplayBookingDetails['SelectedPrice'];
					$Data[$i]['SelectedCurrencySymbol'] = $DisplayBookingDetails['SelectedCurrencySymbol'];
					$Data[$i]['SelectedCurrencyRatio'] = $DisplayBookingDetails['SelectedCurrencyRatio'];
					$Data[$i]['SelectedVehicle'] = $DisplayBookingDetails['SelectedVehicle'];
					$Data[$i]['SelectedCategory'] = $DisplayBookingDetails['SelectedCategory'];
					$Data[$i]['SelectedCategoryId'] = $DisplayBookingDetails['SelectedCategoryId'];
					$Data[$i]['SelectedCategoryTitle'] = $DisplayBookingDetails['SelectedCategoryTitle'];
					$Data[$i]['SelectedCategoryDesc'] = $DisplayBookingDetails['SelectedCategoryDesc'];
					$Data[$i]['SelectedAllowQty'] = $DisplayBookingDetails['SelectedAllowQty'];
					$Data[$i]['SelectedPriceType'] = $DisplayBookingDetails['SelectedPriceType'];
					$Data[$i]['ALLOW_SERVICE_PROVIDER_AMOUNT'] = $DisplayBookingDetails['ALLOW_SERVICE_PROVIDER_AMOUNT'];
				}
			}
			$returnArr['Action'] ="1";
			$returnArr['message'] =$Data;
			
			if ($TotalPages > $page) {
				$returnArr['NextPage'] = $page + 1;
				} else {
				$returnArr['NextPage'] = "0";
			}
			
			}else{
			$returnArr['Action']="0";
			//$returnArr['message']= ($bookingType == "Ride" || $bookingType == "UberX")?"LBL_NO_BOOKINGS_AVAIL":"LBL_NO_DELIVERY_AVAIL";
			$returnArr['message']= "LBL_NO_DATA_AVAIL";
		}
		
		echo json_encode($returnArr);
	}
	
	/* if($type=="checkPassengerBookings"){
		$iUserId     = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		
		$sql = "SELECT * FROM cab_booking WHERE iUserId='$iUserId'";
		$data = $obj->MySQLSelect($sql);
		
		if(count($data)>0){
		
		for($i=0;$i<count($data);$i++){
		$eStatus = $data[$i]['eStatus'];
		
		if($eStatus == "Assign"){
		$iTripId = $data[$i]['iTripId'];
		
		$sql = "SELECT iActive,eCancelled FROM trips WHERE iTripId='$iTripId'";
		$trip_data_arr = $obj->MySQLSelect($sql);
		
		if($trip_data_arr[0]['iActive'] == "Finished" || $trip_data_arr[0]['iActive'] == "Canceled" || $trip_data_arr[0]['eCancelled'] == "Yes"){
		if($trip_data_arr[0]['eCancelled'] == "Yes"){
		$eStatus = "Cancelled by driver";
		}else{
		$eStatus = $trip_data_arr[0]['iActive'];
		}
		
		}
		}
		
		}
		$returnArr['Action'] ="1";
		$returnArr['Data'] =$data;
		}else{
		$returnArr['Action'] ="0";
		}
		
		echo json_encode($returnArr);
	} */
	
	###########################################################################
	if($type=="cancelBooking"){
		$iUserId     = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$userType     = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : '';
		$iCabBookingId     = isset($_REQUEST["iCabBookingId"]) ? $_REQUEST["iCabBookingId"] : '';
		$Reason     = isset($_REQUEST["Reason"]) ? $_REQUEST["Reason"] : '';
		//$APP_TYPE = $generalobj->getConfigurations("configurations","APP_TYPE");
		
		$where = " iCabBookingId = '$iCabBookingId'";
		$data_update_booking['eStatus']="Cancel";
		$data_update_booking['vCancelReason']=$Reason;
		$data_update_booking['iCancelByUserId']=$iUserId;
		$data_update_booking['dCancelDate']=@date("Y-m-d H:i:s");
		$data_update_booking['eCancelBy']=$userType == "Driver" ? $userType:"Rider";
		$id = $obj->MySQLQueryPerform("cab_booking",$data_update_booking,'update',$where);
		
		$sql="select cb.vBookingNo,concat(rd.vName,' ',rd.vLastName) as DriverName,concat(ru.vName,' ',ru.vLastName) as RiderName,ru.vEmail as vRiderMail,ru.vPhone as RiderPhone,ru.vPhoneCode as RiderPhoneCode,rd.vPhone as DriverPhone,rd.vCode as DriverPhoneCode,rd.vEmail as vDriverMail,rd.vLang as driverlang, ru.vLang as riderlang ,cb.vSourceAddresss,cb.tDestAddress,cb.dBooking_date,cb.vCancelReason,cb.dCancelDate from cab_booking cb
		left join register_driver rd on rd.iDriverId = cb.iDriverId
		left join register_user ru on ru.iUserId = cb.iUserId where cb.iCabBookingId = '$iCabBookingId'";
		$data_cab = $obj->MySQLSelect($sql);
		
		$RiderPhoneNo = $data_cab[0]['RiderPhone'];
		$RiderPhoneCode = $data_cab[0]['RiderPhoneCode'];
		$UserLang = $data_cab[0]['riderlang'];
		$DriverPhoneNo = $data_cab[0]['DriverPhone'];
		$DriverPhoneCode = $data_cab[0]['DriverPhoneCode'];
		$DriverLang = $data_cab[0]['driverlang'];
		$Data['vBookingNo'] = $data_cab[0]['vBookingNo'];
		$Data['DriverName'] = $data_cab[0]['DriverName'];
		$Data['RiderName'] = $data_cab[0]['RiderName'];
		$Data['vDriverMail'] = $data_cab[0]['vDriverMail'];
		$Data['vRiderMail'] = $data_cab[0]['vRiderMail'];
		$Data['vSourceAddresss'] = $data_cab[0]['vSourceAddresss'];
		$Data['tDestAddress'] = $data_cab[0]['tDestAddress'];
		$Data['dBookingdate']=date('Y-m-d H:i', strtotime($data_cab[0]['dBooking_date']));
		$Data['vCancelReason'] = $Reason;
		$Data['dCancelDate'] = $data_cab[0]['dCancelDate'];
		
		if($userType == "Driver"){
			$generalobj->send_email_user("MANUAL_CANCEL_TRIP_ADMIN",$Data);
		}
		if($APP_TYPE == "UberX"){
			$USER_EMAIL_TEMPLATE = ($userType == "Driver")?"MANUAL_BOOKING_CANCEL_BYDRIVER_SP":"MANUAL_BOOKING_CANCEL_BYRIDER_SP";
			$generalobj->send_email_user($USER_EMAIL_TEMPLATE,$Data);
			$UserPhoneNo = ($userType == "Driver")?$RiderPhoneNo:$DriverPhoneNo;
			$UserPhoneCode = ($userType == "Driver")?$RiderPhoneCode:$DriverPhoneCode; 
			$USER_SMS_TEMPLATE = ($userType == "Driver")?"BOOKING_CANCEL_BYDRIVER_MESSAGE_SP":"BOOKING_CANCEL_BYRIDER_MESSAGE_SP";
			$message_layout = $generalobj->send_messages_user($USER_SMS_TEMPLATE,$Data,"",$UserLang);
			$UsersendMessage = $generalobj->sendUserSMS($UserPhoneNo,$UserPhoneCode,$message_layout,"");
			if($UsersendMessage == 0){
				//$isdCode= $generalobj->getConfigurations("configurations","SITE_ISD_CODE");
				$isdCode= $SITE_ISD_CODE;
  				$UserPhoneCode = $isdCode;
  				$UsersendMessage = $generalobj->sendUserSMS($UserPhoneNo,$UserPhoneCode,$message_layout,"");
			}
		}
		
		if($id){
			$returnArr['Action']="1";
			$returnArr['message']="LBL_BOOKING_CANCELED";
			}else{
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		echo json_encode($returnArr);
	}
	###########################################################################
	if($type == "loadPackageTypes"){
		$vehicleTypes = get_value('package_type', '*', 'eStatus', 'Active');
		
		if(count($vehicleTypes) >0){
			$returnArr['Action']="1";
			$returnArr['message']=$vehicleTypes;
			}else{
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
	}
	###########################################################################
	if($type == "loadDeliveryDetails"){
		$iDriverId     = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$iTripId     = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '0';
		
		$sql = "SELECT tr.vReceiverName,tr.vReceiverMobile,tr.tPickUpIns,tr.tDeliveryIns,tr.tPackageDetails,pt.vName as packageType,concat(ru.vName,' ',ru.vLastName) as senderName, concat('+',ru.vPhoneCode,'',ru.vPhone) as senderMobile from trips as tr, register_user as ru, package_type as pt WHERE ru.iUserId = tr.iUserId AND tr.iTripId = '".$iTripId."' AND pt.iPackageTypeId = tr.iPackageTypeId";
		$Data = $obj->MySQLSelect($sql);
		
		if(count($Data) >0 && $iTripId != ""){
			$returnArr['Action']="1";
			$returnArr['message']=$Data[0];
			}else{
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		echo json_encode($returnArr);
	}
	
	###########################################################################
	
	if ($type == "checkSurgePrice") {
		$selectedCarTypeID    = isset($_REQUEST["SelectedCarTypeID"]) ? $_REQUEST["SelectedCarTypeID"] : '';
		$selectedTime    = isset($_REQUEST["SelectedTime"]) ? $_REQUEST["SelectedTime"] : '';
		
		$vTimeZone    =isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		$PickUpLatitude    = isset($_REQUEST["PickUpLatitude"]) ? $_REQUEST["PickUpLatitude"] : '0.0';
		$PickUpLongitude    =isset($_REQUEST["PickUpLongitude"]) ? $_REQUEST["PickUpLongitude"] : '0.0';
		$DestLatitude    = isset($_REQUEST["DestLatitude"]) ? $_REQUEST["DestLatitude"] : '';
		$DestLongitude    =isset($_REQUEST["DestLongitude"]) ? $_REQUEST["DestLongitude"] : '';
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    ######### Checking For Flattrip #########
    if($UserType == "Passenger") {
      $tblname = "register_user";
			$iUserId = "iUserId";
			$vCurrency = "vCurrencyPassenger";
			$sqlp = "SELECT ru.vCurrencyPassenger,ru.vLang,cu.vSymbol,cu.Ratio FROM register_user as ru LEFT JOIN currency as cu ON ru.vCurrencyPassenger = cu.vName WHERE iUserId = '".$iMemberId."'";
      $passengerData = $obj->MySQLSelect($sqlp);
      $currencycode = $passengerData[0]['vCurrencyPassenger'];
      $currencySymbol = $passengerData[0]['vSymbol'];
      $priceRatio=$passengerData[0]['Ratio'];
    }else{
			$tblname = "register_driver";
			$iUserId = "iDriverId";
			$vCurrency = "vCurrencyDriver";
			$sqld = "SELECT rd.vCurrencyDriver,rd.vLang,cu.vSymbol,cu.Ratio FROM register_driver as rd LEFT JOIN currency as cu ON rd.vCurrencyDriver = cu.vName WHERE iDriverId = '".$iMemberId."'";
      $driverData = $obj->MySQLSelect($sqld);
      $currencycode = $driverData[0]['vCurrencyDriver'];
      $currencySymbol = $driverData[0]['vSymbol'];
      $priceRatio=$driverData[0]['Ratio'];
		}
    if($currencycode == "" || $currencycode == NULL){
      $sql = "SELECT vName,vSymbol,Ratio from currency WHERE eDefault = 'Yes'";
      $currencyData = $obj->MySQLSelect($sql);
      $currencycode = $currencyData[0]['vName'];
      $currencySymbol = $currencyData[0]['vSymbol'];
      $priceRatio=$currencyData[0]['Ratio'];
    }
    ######### Checking For Flattrip #########
    $isDestinationAdded = "No";
    if($DestLatitude != "" && $DestLongitude != ""){
       $isDestinationAdded = "Yes";
    }
    if($isDestinationAdded == "Yes"){
      $sourceLocationArr = array($PickUpLatitude,$PickUpLongitude);       
  		$destinationLocationArr = array($DestLatitude,$DestLongitude);      
      $data_flattrip['eFlatTrip'] = "No";
      $data_flattrip['Flatfare'] = 0;
      $eFlatTrip = $data_flattrip['eFlatTrip']; 
      $fFlatTripPrice = $data_flattrip['Flatfare'];
    }else{
      $eFlatTrip = "No"; 
      $fFlatTripPrice = 0;
    }  
    ######### Checking For Flattrip #########
		if($selectedTime != '' && $vTimeZone != '') {
			$systemTimeZone = date_default_timezone_get();
			$selectedTime = converToTz($selectedTime,$systemTimeZone,$vTimeZone);
		}
		
		//$APP_TYPE = $generalobj->getConfigurations("configurations","APP_TYPE");
    $SurgePriceValue = 1;
		if($APP_TYPE == "UberX"){
			$data['Action'] = "1";
			}else{
			$data= checkSurgePrice($selectedCarTypeID,$selectedTime);
      if($data['Action'] == "0"){
        $SurgePriceValue = $data['SurgePriceValue'];
      }
		}
    if($APPLY_SURGE_ON_FLAT_FARE == "No" && $eFlatTrip == "Yes"){
       $SurgePriceValue = 1;
       $data['Action'] = "1";
    }
    $fFlatTripPrice = round($fFlatTripPrice * $priceRatio,2);
    $fSurgePriceDiff = round(($fFlatTripPrice * $SurgePriceValue) - $fFlatTripPrice, 2);
    $fFlatTripPrice = $fFlatTripPrice+$fSurgePriceDiff;
    $data['eFlatTrip'] = $eFlatTrip;
    $data['fFlatTripPrice'] = $fFlatTripPrice;
    $data['fFlatTripPricewithsymbol'] = $currencySymbol." ".$fFlatTripPrice;
		
		echo json_encode($data);
	}
  
  ###########################################################################
	
	if ($type == "getTransactionHistory")
	{
		global $generalobj;
		#echo "hello"; exit;
		
		$page        = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
		$iUserId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : '';
		$tripTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		$ListType = isset($_REQUEST["ListType"]) ? $_REQUEST["ListType"] : 'All';
		if($page == "0" || $page == 0){
			$page = 1;
		}
		if($UserType=="Passenger"){
			$UserType="Rider";
		}
		
		$ssql='';
		if($ListType != "All"){
			$ssql.= " AND eType ='".$ListType."'";
		}
		$per_page=10;
		$sql_all  = "SELECT COUNT(iUserWalletId) As TotalIds FROM user_wallet WHERE  iUserId='".$iUserId."' AND eUserType = '".$UserType."' ".$ssql." ";
		$data_count_all = $obj->MySQLSelect($sql_all);
		$TotalPages = ceil($data_count_all[0]['TotalIds'] / $per_page);
		
		$start_limit = ($page - 1) * $per_page;
		$limit       = " LIMIT " . $start_limit . ", " . $per_page;
		
		$user_available_balance = $generalobj->get_user_available_balance($iUserId,$UserType);
		
		//$sql = "SELECT tripRate.vRating1 as TripRating,tr.* FROM `trips` as tr,`ratings_user_driver` as tripRate  WHERE  tr.iUserId='$iUserId' AND tripRate.iTripId=tr.iTripId AND tripRate.eUserType='$UserType' AND (tr.iActive='Canceled' || tr.iActive='Finished') ORDER BY tr.iTripId DESC" . $limit;
		$sql = "SELECT * from user_wallet where iUserId='".$iUserId."' AND eUserType = '".$UserType."' ".$ssql." ORDER BY iUserWalletId DESC". $limit;
		$Data = $obj->MySQLSelect($sql);
		$totalNum = count($Data);
		
		$vSymbol = get_value('currency', 'vSymbol', 'eDefault','Yes','','true');
		if($UserType == 'Driver')
		{
			/*$uservSymbol = get_value('register_driver', 'vCurrencyDriver', 'iDriverId',$iUserId,'','true');
			$vLangCode = get_value('register_driver', 'vLang', 'iDriverId',$iUserId,'','true');*/
      $UserData = get_value('register_driver', 'vCurrencyDriver,vLang', 'iDriverId', $iUserId);
      $uservSymbol = $UserData[0]['vCurrencyDriver'];
      $vLangCode = $UserData[0]['vLang'];
		}
		else
		{
			/*$uservSymbol = get_value('register_user', 'vCurrencyPassenger', 'iUserId',$iUserId,'','true');
			$vLangCode = get_value('register_user', 'vLang', 'iUserId',$iUserId,'','true');  */
      $UserData = get_value('register_user', 'vCurrencyPassenger,vLang', 'iUserId', $iUserId);
      $uservSymbol = $UserData[0]['vCurrencyPassenger'];
      $vLangCode = $UserData[0]['vLang'];
		}
		
		$userCurrencySymbol = get_value('currency', 'vSymbol', 'vName',$uservSymbol,'','true');
		
		if($vLangCode == "" || $vLangCode == NULL){
			$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
	    $languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
		
		$i=0;
		if ( count($Data) > 0 ) {
			
			$row = $Data;
			$prevbalance = 0;
            while ( count($row)> $i ) {
				
            	if(!empty($row[$i]['tDescription'])){
            		$pat = '/\#([^\"]*?)\#/';
                    preg_match($pat, $row[$i]['tDescription'], $tDescription_value);
                    $tDescription_translate = $languageLabelsArr[$tDescription_value[1]];
                    $row[$i]['tDescription'] = str_replace($tDescription_value[0], $tDescription_translate, $row[$i]['tDescription']);
				}
            	
				// Convert Into Timezone
				if($tripTimeZone != ""){
					$serverTimeZone = date_default_timezone_get();
					$row[$i]['dDate'] = converToTz($row[$i]['dDate'],$tripTimeZone,$serverTimeZone);
				}
				// Convert Into Timezone
            	if($row[$i]['eType'] == "Credit"){
					$row[$i]['currentbal'] = $prevbalance+$row[$i]['iBalance'];
					}else{
				    $row[$i]['currentbal'] = $prevbalance-$row[$i]['iBalance'];
				}
                $prevbalance = $row[$i]['currentbal'];
				$row[$i]['dDateOrig'] = $row[$i]['dDate'];
                $row[$i]['dDate'] = date('d-M-Y',strtotime($row[$i]['dDate']));
				
				//$row[$i]['currentbal'] = $vSymbol.$row[$i]['currentbal'];
				//$row[$i]['iBalance'] = $vSymbol.$row[$i]['iBalance'];
				$row[$i]['currentbal'] = $generalobj->userwalletcurrency($row[$i]['fRatio_'.$uservSymbol],$row[$i]['currentbal'],$uservSymbol);
				$row[$i]['iBalance'] = $generalobj->userwalletcurrency($row[$i]['fRatio_'.$uservSymbol],$row[$i]['iBalance'],$uservSymbol);
				$i++;
			}
			
			//$returnData['message'] = array_reverse($row);
			$returnData['message'] = $row;
			if ($TotalPages > $page) {
				$returnData['NextPage'] = $page + 1;
				} else {
				$returnData['NextPage'] = 0;
			}
			
			$returnData['user_available_balance_default']=$vSymbol.$user_available_balance;
			$returnData['user_available_balance'] = strval($generalobj->userwalletcurrency(0,$user_available_balance,$uservSymbol));
			$returnData['Action']="1";
			#echo "<pre>"; print_r($returnData); exit;
			echo json_encode($returnData);
			
			}else{
			$returnData['Action']="0";
			$returnData['message']="LBL_NO_TRANSACTION_AVAIL";
			$returnData['user_available_balance']= $userCurrencySymbol."0.00";
			echo json_encode($returnData);
		}
		
	}
	
	###########################################################################
	if($type=="loadPassengersLocation"){
		
		global $generalobj,$obj;
		
		/*$iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
			$radius = isset($_REQUEST["Radius"]) ? $_REQUEST["Radius"] : '';
			$sourceLat = isset($_REQUEST["Latitude"]) ? $_REQUEST["Latitude"] : '';
			$sourceLon = isset($_REQUEST["Longitude"]) ? $_REQUEST["Longitude"] : '';
			
			$str_date = @date('Y-m-d H:i:s', strtotime('-5 minutes'));
			
			$sql = "SELECT ROUND(( 3959 * acos( cos( radians(".$sourceLat.") )
			* cos( radians( vLatitude ) )
			* cos( radians( vLongitude ) - radians(".$sourceLon.") )
			+ sin( radians(".$sourceLat.") )
			* sin( radians( vLatitude ) ) ) ),2) AS distance, register_driver.*  FROM `register_driver`
			WHERE (vLatitude != '' AND vLongitude != '' AND eStatus='Active' AND tLastOnline > '$str_date')
			HAVING distance < ".$radius." ORDER BY `register_driver`";
			
			
		$Data = $obj->MySQLSelect($sql);*/
		
		
		
		$iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
		$radius = isset($_REQUEST["Radius"]) ? $_REQUEST["Radius"] : '';
		$sourceLat = isset($_REQUEST["Latitude"]) ? $_REQUEST["Latitude"] : '';
		$sourceLon = isset($_REQUEST["Longitude"]) ? $_REQUEST["Longitude"] : '';
		
		$str_date = @date('Y-m-d H:i:s', strtotime('-5 minutes'));
		
		// register_user table
		$sql = "SELECT ROUND(( 3959 * acos( cos( radians(".$sourceLat.") )
		* cos( radians( vLatitude ) )
		* cos( radians( vLongitude ) - radians(".$sourceLon.") )
		+ sin( radians(".$sourceLat.") )
		* sin( radians( vLatitude ) ) ) ),2) AS distance, register_user.*  FROM `register_user`
		WHERE (vLatitude != '' AND vLongitude != '' AND eStatus='Active' AND tLastOnline > '$str_date')
		HAVING distance < ".$radius." ORDER BY `register_user`.iUserId ASC";
		
		
		$Data = $obj->MySQLSelect($sql);
		$storeuser = array();
		$storetrip = array();
		
		foreach ($Data as $value) {
			
			$dataofuser = array("Type"=>'Online',"Latitude"=>$value['vLatitude'],"Longitude"=>$value['vLongitude'],"iUserId"=>$value['iUserId']);
			array_push($storeuser, $dataofuser);
			
		}
		
		// trip table
		if(SITE_TYPE == 'Demo'){
			$sql_trip = "SELECT ROUND(( 3959 * acos( cos( radians(".$sourceLat.") )
			* cos( radians( tStartLat ) )
			* cos( radians( tStartLong ) - radians(".$sourceLon.") )
			+ sin( radians(".$sourceLat.") )
			* sin( radians( tStartLat ) ) ) ),2) AS distance, trips.*  FROM `trips`
			WHERE (tStartLat != '' AND tStartLong != '' AND tTripRequestDate >= DATE_SUB(CURDATE(), INTERVAL 2500 HOUR))
			HAVING distance < ".$radius." ORDER BY `trips`.iTripId DESC";
		} else {
			$sql_trip = "SELECT ROUND(( 3959 * acos( cos( radians(".$sourceLat.") )
			* cos( radians( tStartLat ) )
			* cos( radians( tStartLong ) - radians(".$sourceLon.") )
			+ sin( radians(".$sourceLat.") )
			* sin( radians( tStartLat ) ) ) ),2) AS distance, trips.*  FROM `trips`
			WHERE (tStartLat != '' AND tStartLong != '' AND tTripRequestDate >= DATE_SUB(CURDATE(), INTERVAL 24 HOUR))
			HAVING distance < ".$radius." ORDER BY `trips`.iTripId DESC";
		}
		
		$Dataoftrips = $obj->MySQLSelect($sql_trip);
		
		foreach ($Dataoftrips as $value1) {
			
			$valuetrip = array("Type"=>'History',"Latitude"=>$value1['tStartLat'],"Longitude"=>$value1['tStartLong'],"iTripId"=>$value1['iTripId']);
			array_push($storetrip, $valuetrip);
			
		}
		
		$finaldata = array_merge($storeuser,$storetrip);
		//echo "<pre>"; print_r($finaldata); exit;
		
		if(count($finaldata)>0){
			$returnData['Action']="1";
			$returnData['message']=$finaldata;
			}else{
			$returnData['Action']="0";
		}
		echo json_encode($returnData);
		
	}
	###########################################################################
	###########################################################################
	if($type=="loadPetsType"){
		$iUserId     = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		
		if($iUserId != ""){
			$vLanguage=get_value('register_user', 'vLang', 'iUserId',$iUserId,'','true');
			
			$vLanguage = $vLanguage ==""?"EN":$vLanguage;
			
			$petTypes = get_value('pet_type', 'iPetTypeId, vTitle_'.$vLanguage.' as vTitle', 'eStatus', 'Active');
			
			$returnData['Action']="1";
			$returnData['message']=$petTypes;
			}else{
			$returnData['Action']="0";
		}
		echo json_encode($returnData);
	}
	###########################################################################
	
	if($type == "loadUserPets"){
		$iUserId     = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$page        = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
		
		$vLanguage=get_value('register_user', 'vLang', 'iUserId',$iUserId,'','true');
		
		$vLanguage = $vLanguage ==""?"EN":$vLanguage;
		
		$per_page=10;
		$sql = "SELECT COUNT(iUserPetId) as TotalIds from user_pets WHERE iUserId='".$iUserId."'";
		
		$Data_all = $obj->MySQLSelect($sql);
		$TotalPages = ceil($Data_all[0]['TotalIds'] / $per_page);
		
		
		$start_limit = ($page - 1) * $per_page;
		$limit       = " LIMIT " . $start_limit . ", " . $per_page;
		
		$sql = "SELECT up.*,pt.vTitle_".$vLanguage." as petType from user_pets as up,  pet_type as pt WHERE pt.iPetTypeId = up.iPetTypeId AND up.iUserId='".$iUserId."'".$limit;
		$Data = $obj->MySQLSelect($sql);
		
		$totalNum = count($Data);
		
		if(count($Data) >0 && $iUserId != ""){
			$returnArr['Action']="1";
			$returnArr['message']=$Data;
			if ($TotalPages > $page) {
				$returnArr['NextPage'] = $page + 1;
				} else {
				$returnArr['NextPage'] = "0";
			}
			}else{
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		echo json_encode($returnArr);
	}
	###########################################################################
	if($type == "deleteUserPets"){
		global $generalobj;
		
		$iUserPetId = isset($_REQUEST["iUserPetId"]) ? $_REQUEST["iUserPetId"] : '0';
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '0';
		
		$sql = "DELETE FROM user_pets WHERE `iUserPetId`='".$iUserPetId."' AND `iUserId`='".$iUserId."'";
		$id = $obj->sql_query($sql);
		// echo "ID:".$id;exit;
		if($id >0){
			$returnArr['Action']="1";
			$returnArr['message']="LBL_INFO_UPDATED_TXT";
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
	}
	###########################################################################
	if($type == "addUserPets"){
		global $generalobj;
		
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '0';
		$iPetTypeId = isset($_REQUEST["iPetTypeId"]) ? $_REQUEST["iPetTypeId"] : '0';
		$vTitle = isset($_REQUEST["vTitle"]) ? $_REQUEST["vTitle"] : '';
		$vWeight = isset($_REQUEST["vWeight"]) ? $_REQUEST["vWeight"] : '';
		$tBreed = isset($_REQUEST["tBreed"]) ? $_REQUEST["tBreed"] : '';
		$tDescription = isset($_REQUEST["tDescription"]) ? $_REQUEST["tDescription"] : '';
		
		$Data_pets['iUserId']=$iUserId;
		$Data_pets['iPetTypeId']=$iPetTypeId;
		$Data_pets['vTitle']=$vTitle;
		$Data_pets['vWeight']=$vWeight;
		$Data_pets['tBreed']=$tBreed;
		$Data_pets['tDescription']=$tDescription;
		
		$id = $obj->MySQLQueryPerform("user_pets",$Data_pets,'insert');
		
		if ($id > 0) {
			$returnArr['Action']="1";
			
			} else {
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
	}
	###########################################################################
	if($type == "editUserPets"){
		$iUserPetId = isset($_REQUEST["iUserPetId"]) ? $_REQUEST['iUserPetId'] : '';
		$iUserId    = isset($_REQUEST["iUserId"]) ? $_REQUEST['iUserId'] : '';
		$iPetTypeId = isset($_REQUEST["iPetTypeId"]) ? $_REQUEST['iPetTypeId'] : '';
		$vTitle 	= isset($_REQUEST["vTitle"]) ? $_REQUEST['vTitle'] : '';
		$vWeight 	= isset($_REQUEST["vWeight"]) ? $_REQUEST['vWeight'] : '';
		$tBreed 	= isset($_REQUEST["tBreed"]) ? $_REQUEST['tBreed'] : '';
		$tDescription 	= isset($_REQUEST["tDescription"]) ? $_REQUEST['tDescription'] : '';
		
		$where = " iUserPetId = '".$iUserPetId."' AND `iUserId`='".$iUserId."'";
		
		$Data['iUserId']=$iUserId;
		$Data['iPetTypeId']=$iPetTypeId;
		$Data['vTitle']=$vTitle;
		$Data['vWeight']=$vWeight;
		$Data['tBreed']=$tBreed;
		$Data['tDescription']=$tDescription;
		$id = $obj->MySQLQueryPerform("user_pets",$Data,'update',$where);
		
		
		if ($id) {
			$returnArr['Action']="1";
			$returnArr['message']="LBL_INFO_UPDATED_TXT";
			} else {
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
	}
	###########################################################################
	if($type == "loadPetDetail"){
		$iUserPetId = isset($_REQUEST["iUserPetId"]) ? $_REQUEST['iUserPetId'] : '';
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST['iUserId'] : '';
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST['iDriverId'] : '';
		
		
		$vLanguage=get_value('register_user', 'vLang', 'iDriverId',$iDriverId,'','true');
		if($vLanguage == "" || $vLanguage == NULL){
			$vLanguage = "EN";
		}
		
		$sql = "SELECT up.*,pt.vTitle_".$vLanguage." as petTypeName from user_pets as up,  pet_type as pt WHERE pt.iPetTypeId = up.iPetTypeId AND up.iUserId='".$iUserId."' AND up.iUserPetId='".$iUserPetId."'";
		$Data = $obj->MySQLSelect($sql);
		
		if (count($Data) > 0) {
			$returnArr['Action']="1";
			$returnArr['message']=$Data[0];
			} else {
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
	}
	###########################################################################
	if($type=="collectTip"){
		$iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
		$iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '0';
		$fAmount = isset($_REQUEST["fAmount"]) ? $_REQUEST["fAmount"] : '';
		
		$tbl_name = "register_user";
		$currencycode = "vCurrencyPassenger";
		$iUserId = "iUserId";
		$eUserType = "Rider";
		
		if($iMemberId == "") {
			$iMemberId = get_value('trips', 'iUserId', 'iTripId', $iTripId,'','true');
		}
		/*$vStripeCusId = get_value($tbl_name, 'vStripeCusId', $iUserId, $iMemberId,'','true');
		$vStripeToken = get_value($tbl_name, 'vStripeToken', $iUserId, $iMemberId,'','true');
		$userCurrencyCode = get_value($tbl_name, $currencycode, $iUserId, $iMemberId,'','true'); */
		$UserData = get_value($tbl_name, 'vStripeCusId,vStripeToken,vCurrencyPassenger', $iUserId, $iMemberId);
    $vStripeCusId = $UserData[0]['vStripeCusId'];
    $vStripeToken = $UserData[0]['vStripeToken'];
    $userCurrencyCode = $UserData[0]['vCurrencyPassenger'];
    /*$currencyCode = get_value('currency', 'vName', 'eDefault', 'Yes','','true');
		$currencyratio = get_value('currency', 'Ratio', 'vName', $userCurrencyCode,'','true');*/
    $DefaultCurrencyData = get_value('currency', 'vName,Ratio', 'eDefault', 'Yes');
    $currencyCode = $DefaultCurrencyData[0]['vName'];
    $currencyratio = $DefaultCurrencyData[0]['Ratio'];
		//$price = $fAmount*$currencyratio;
		$price = $fAmount/$currencyratio;
		$price_new = $price * 100;
		$price_new = round($price_new);
		if($vStripeCusId == "" || $vStripeToken == ""){
			$returnArr["Action"] = "0";
			$returnArr['message']="LBL_NO_CARD_AVAIL_NOTE";
			echo json_encode($returnArr);exit;
		}
		
		$dDate = Date('Y-m-d H:i:s');
		$eFor = 'Deposit';
		$eType = 'Credit';
		$tDescription = '#LBL_AMOUNT_DEBIT#';
		//$tDescription = "Amount debited";
		$ePaymentStatus = 'Unsettelled';
		
		$userAvailableBalance = $generalobj->get_user_available_balance($iMemberId,$eUserType);
		if($userAvailableBalance > $price){
			$where = " iTripId = '$iTripId'";
			$data['fTipPrice']= $price;
			
			$id = $obj->MySQLQueryPerform("trips",$data,'update',$where);
			
			$vRideNo = get_value('trips', 'vRideNo', 'iTripId',$tripId,'','true');
			$data_wallet['iUserId']=$iUserId;
			$data_wallet['eUserType']="Rider";
			$data_wallet['iBalance']=$price;
			$data_wallet['eType']="Debit";
			$data_wallet['dDate']=date("Y-m-d H:i:s");
			$data_wallet['iTripId']=$iTripId;
			$data_wallet['eFor']="Booking";
			$data_wallet['ePaymentStatus']="Unsettelled";
			$data_wallet['tDescription']='#LBL_DEBITED_BOOKING#'.$vRideNo;
			//$data_wallet['tDescription']="Debited for trip#".$vRideNo;
			
			$generalobj->InsertIntoUserWallet($data_wallet['iUserId'],$data_wallet['eUserType'],$data_wallet['iBalance'],$data_wallet['eType'],$data_wallet['iTripId'],$data_wallet['eFor'],$data_wallet['tDescription'],$data_wallet['ePaymentStatus'],$data_wallet['dDate']);
			
			$returnArr["Action"] = "1";
			echo json_encode($returnArr);exit;
			
			}else if($price > 0.51){
			try{
				$charge_create = Stripe_Charge::create(array(
				"amount" => $price_new,
				"currency" => $currencyCode,
				"customer" => $vStripeCusId,
				"description" =>  $tDescription
				));
				
				$details = json_decode($charge_create);
				$result = get_object_vars($details);
				//echo "<pre>";print_r($result);exit;
				if($result['status']=="succeeded" && $result['paid']=="1"){
					
					$where = " iTripId = '$iTripId'";
					$data['fTipPrice']= $price;
					
					$id = $obj->MySQLQueryPerform("trips",$data,'update',$where);
					
					$returnArr["Action"] = "1";
					echo json_encode($returnArr);exit;
					}else{
  					$returnArr['Action'] = "0";
  					$returnArr['message']="LBL_TRANS_FAILED";
					
  					echo json_encode($returnArr);exit;
				}
				
				}catch(Exception $e){
				//echo "<pre>";print_r($e);exit;
  				$error3 = $e->getMessage();
  				$returnArr["Action"] = "0";
				$returnArr['message']=$error3;
				//$returnArr['message']="LBL_TRANS_FAILED";
				
  				echo json_encode($returnArr);exit;
			}
			
			}else{
			$returnArr["Action"] = "0";
			$returnArr['message']="LBL_REQUIRED_MINIMUM_AMOUT";
			$returnArr['minValue'] = strval(round(51 * $currencyratio));
			
			echo json_encode($returnArr);exit;
		}
		
		
	}
	###########################################################################
	############################ UBER-For-X ################################
	
	/*if($type=="getServiceCategories"){
		global $generalobj;
		
		$parentId = isset($_REQUEST['parentId'])?clean($_REQUEST['parentId']):0;
		$userId = isset($_REQUEST['userId'])?clean($_REQUEST['userId']):'';
		if($userId != "") {
		$sql1 = "SELECT vLang FROM `register_user` WHERE iUserId='$userId'";
		$row = $obj->MySQLSelect($sql1);
		$lang = $row[0]['vLang'];
		if($lang == "") { $lang = "EN"; }
		
		//$vehicle_category = get_value('vehicle_category', 'iVehicleCategoryId, vLogo,vCategory_'.$row[0]['vLang'].' as vCategory', 'eStatus', 'Active');
		// $sql2 = "SELECT iVehicleCategoryId, vLogo,vCategory_".$lang." as vCategory FROM vehicle_category WHERE eStatus='Active' AND iParentId='$parentId'";
		if($parentId == 0){
		$sql2 = "SELECT vc.iVehicleCategoryId, vc.vLogo,vc.vCategory_".$lang." as vCategory FROM vehicle_category as vc WHERE vc.eStatus='Active' AND vc.iParentId='$parentId' and (select count(iVehicleCategoryId) from vehicle_category where iParentId=vc.iVehicleCategoryId) > 0";
		}else{
		$sql2 = "SELECT iVehicleCategoryId, vLogo,vCategory_".$lang." as vCategory FROM vehicle_category WHERE eStatus='Active' AND iParentId='$parentId'";
		}
		
		$Data = $obj->MySQLSelect($sql2);
		
		for($i=0;$i<count($Data);$i++){
		$Data[$i]['vLogo_image'] = $tconfig['tsite_upload_images_vehicle_category'].'/'.$Data[$i]['iVehicleCategoryId'].'/android/'.$Data[$i]['vLogo'];
		}
		
		// if(!empty($Data)){
		$returnArr['Action']="1";
		$returnArr['message'] = $Data;
		// }else{
		// $returnArr['Action']="0"; 
		// $returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		// }
		}else{
		$returnArr['Action']="0"; 
		$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
	}    */
	
	if($type=="getServiceCategories"){
		global $generalobj;
		
		$parentId = isset($_REQUEST['parentId'])?clean($_REQUEST['parentId']):0;
		$userId = isset($_REQUEST['userId'])?clean($_REQUEST['userId']):'';
		if($userId != "") {
			$sql1 = "SELECT vLang FROM `register_user` WHERE iUserId='$userId'";
			$row = $obj->MySQLSelect($sql1);
			$lang = $row[0]['vLang'];
			if($lang == "") { $lang = "EN"; }
			
			//$vehicle_category = get_value('vehicle_category', 'iVehicleCategoryId, vLogo,vCategory_'.$row[0]['vLang'].' as vCategory', 'eStatus', 'Active');
			// $sql2 = "SELECT iVehicleCategoryId, vLogo,vCategory_".$lang." as vCategory FROM vehicle_category WHERE eStatus='Active' AND iParentId='$parentId'";
			/*if($parentId == 0){
				$sql2 = "SELECT vc.iVehicleCategoryId, vc.vLogo,vc.vCategory_".$lang." as vCategory FROM vehicle_category as vc WHERE vc.eStatus='Active' AND vc.iParentId='$parentId' and (select count(iVehicleCategoryId) from vehicle_category where iParentId=vc.iVehicleCategoryId) > 0";
				}else{
				$sql2 = "SELECT iVehicleCategoryId, vLogo,vCategory_".$lang." as vCategory FROM vehicle_category WHERE eStatus='Active' AND iParentId='$parentId'";
			}   */
			$sql2 = "SELECT iVehicleCategoryId, vLogo,vCategory_".$lang." as vCategory FROM vehicle_category WHERE eStatus='Active' AND iParentId='$parentId' ";
			$Data = $obj->MySQLSelect($sql2);
			$Datacategory = array();
			if($parentId == 0){
				if(count($Data) > 0){
					$k = 0;
					for($i=0;$i<count($Data);$i++){
						$sql3 = "SELECT iVehicleCategoryId, vLogo,vCategory_".$lang." as vCategory FROM vehicle_category WHERE eStatus='Active' AND iParentId='".$Data[$i]['iVehicleCategoryId']."'";
						$Data2 = $obj->MySQLSelect($sql3);
						if(count($Data2) > 0){
							for($j=0;$j<count($Data2);$j++){
								$sql4 = "SELECT iVehicleTypeId FROM vehicle_type WHERE iVehicleCategoryId='".$Data2[$j]['iVehicleCategoryId']."'";
								$Data3 = $obj->MySQLSelect($sql4);
								if(count($Data3) > 0){
									$Datacategory[$k]['iVehicleCategoryId'] = $Data[$i]['iVehicleCategoryId'];
									$Datacategory[$k]['vLogo'] = $Data[$i]['vLogo'];
									$Datacategory[$k]['vLogo_image'] = $tconfig['tsite_upload_images_vehicle_category'].'/'.$Data[$i]['iVehicleCategoryId'].'/android/'.$Data[$i]['vLogo'];
									$Datacategory[$k]['vCategory'] = $Data[$i]['vCategory'];
									$k++; 
								}
							}
							//$Datacategory = array_map('unserialize', array_unique(array_map('serialize', $Datacategory)));
						}
						
					}
				}  
				}else{
				if(count($Data) > 0){
					$k = 0;
					for($j=0;$j<count($Data);$j++){
						$sql4 = "SELECT iVehicleTypeId FROM vehicle_type WHERE iVehicleCategoryId='".$Data[$j]['iVehicleCategoryId']."'";
						$Data3 = $obj->MySQLSelect($sql4);
						if(count($Data3) > 0){
							$Datacategory[$k]['iVehicleCategoryId'] = $Data[$j]['iVehicleCategoryId'];
							$Datacategory[$k]['vLogo'] = $Data[$j]['vLogo'];
							$Datacategory[$k]['vLogo_image'] = $tconfig['tsite_upload_images_vehicle_category'].'/'.$Data[$j]['iVehicleCategoryId'].'/android/'.$Data[$j]['vLogo'];
							$Datacategory[$k]['vCategory'] = $Data[$j]['vCategory'];
							$k++; 
						}
						//$unique = array_map('unserialize', array_unique(array_map('serialize', $array)));
					}
					//$Datacategory = array_map('unserialize', array_unique(array_map('serialize', $Datacategory)));
				} 
			} 
			
			$Datacategory1 = array_unique($Datacategory, SORT_REGULAR);
			$DatanewArr = array();
			foreach($Datacategory1 as $inner) {
				array_push($DatanewArr,$inner);
			}
			
			$returnArr['Action']="1";
			$returnArr['message'] = array_reverse($DatanewArr);
			}else{
			$returnArr['Action']="0"; 
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
	}
	
	if($type=="getServiceCategoryTypes"){
		global $generalobj;
		
		$iVehicleCategoryId = isset($_REQUEST['iVehicleCategoryId'])?clean($_REQUEST['iVehicleCategoryId']):0;
		$vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
		$vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
		$userId = isset($_REQUEST['userId'])?clean($_REQUEST['userId']):'';
		$eCheck = isset($_REQUEST['eCheck'])?clean($_REQUEST['eCheck']):'No';
		$pickuplocationarr = array($vLatitude,$vLongitude);
		$GetVehicleIdfromGeoLocation = GetVehicleTypeFromGeoLocation($pickuplocationarr);
		if($eCheck == "" || $eCheck == NULL){
			$eCheck = "No";
		}
		if($eCheck == "Yes"){
       //$allowed_ans = checkRestrictedAreaNew($pickuplocationarr,"No");
       $allowed_ans = checkAllowedAreaNew($pickuplocationarr,"No");
			if($allowed_ans == "Yes"){
				$sql23 = "SELECT iVehicleTypeId FROM `vehicle_type` WHERE iLocationid IN ($GetVehicleIdfromGeoLocation) AND iVehicleCategoryId = '".$iVehicleCategoryId."' ORDER BY iVehicleTypeId ASC";
				$vehicleTypes = $obj->MySQLSelect($sql23);
				if(count($vehicleTypes) > 0){
					$returnArr['Action']="1";
					}else{
					$returnArr['Action']="0";
					$returnArr['message']="LBL_NO_SERVICES_AVAIL_FOR_JOB_LOC";
				}
				}else{
				$returnArr['Action']="0"; 
				$returnArr['message']="LBL_JOB_LOCATION_NOT_ALLOWED";
			}  
			$obj->MySQLClose();
			echo json_encode($returnArr);exit;
			}else{
			if($userId != "") {
    			$sql1 = "SELECT vLang,vCurrencyPassenger FROM `register_user` WHERE iUserId='$userId'";
    			$row = $obj->MySQLSelect($sql1);
    			$lang = $row[0]['vLang'];
    			if($lang == "" || $lang == NULL) { $lang = "EN"; }
    			
    			
    			$vCurrencyPassenger = $row[0]['vCurrencyPassenger'];
    			if($vCurrencyPassenger == "" || $vCurrencyPassenger == NULL){
    				$vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes','','true');
				}
    			$UserCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyPassenger);
    			$priceRatio = $UserCurrencyData[0]['Ratio'];
    			$vSymbol = $UserCurrencyData[0]['vSymbol']; 
    			
    			$vehicleCategoryData = get_value('vehicle_category', "vCategoryTitle_".$lang." as vCategoryTitle, tCategoryDesc_".$lang." as tCategoryDesc", 'iVehicleCategoryId', $iVehicleCategoryId);
    			$vCategoryTitle = $vehicleCategoryData[0]['vCategoryTitle'];
    			$vCategoryDesc = $vehicleCategoryData[0]['tCategoryDesc'];
    			$sql2 = "SELECT vc.iVehicleCategoryId, vc.iParentId,vc.vCategory_".$lang." as vCategory, vc.ePriceType, vt.iVehicleTypeId, vt.vVehicleType_".$lang." as vVehicleType, vt.eFareType, vt.fFixedFare, vt.fPricePerHour, vt.fPricePerKM, vt.fPricePerMin, vt.iBaseFare,vt.fCommision, vt.iMinFare,vt.iPersonSize, vt.vLogo as vVehicleTypeImage, vt.eType, vt.eIconType, vt.eAllowQty, vt.iMaxQty, vt.iVehicleTypeId, fFixedFare FROM vehicle_category as vc LEFT JOIN vehicle_type AS vt ON vt.iVehicleCategoryId = vc.iVehicleCategoryId WHERE vc.eStatus='Active' AND vt.iVehicleCategoryId='$iVehicleCategoryId' AND vt.iLocationid IN ($GetVehicleIdfromGeoLocation)";
    			//AND vt.eType='UberX'
    			
    			$Data = $obj->MySQLSelect($sql2);
    			if(!empty($Data)){
    				for($i=0;$i<count($Data);$i++){
    					$Data[$i]['fFixedFare_value']= round($Data[$i]['fFixedFare'] * $priceRatio,2);
    					$fFixedFare = round($Data[$i]['fFixedFare'] * $priceRatio,2);
						$Data[$i]['fFixedFare']= $vSymbol.formatNum($fFixedFare);
						$Data[$i]['fPricePerHour_value']= round($Data[$i]['fPricePerHour'] * $priceRatio,2);
						$fPricePerHour = round($Data[$i]['fPricePerHour'] * $priceRatio,2);
    					$Data[$i]['fPricePerHour']= $vSymbol.formatNum($fPricePerHour);
						$fPricePerKM = round($Data[$i]['fPricePerKM'] * $priceRatio,2);
						$Data[$i]['fPricePerKM']= $vSymbol.formatNum($fPricePerKM);
						$fPricePerMin = round($Data[$i]['fPricePerMin'] * $priceRatio,2); 
    					$Data[$i]['fPricePerMin']= $vSymbol.formatNum($fPricePerMin);
						$iBaseFare = round($Data[$i]['iBaseFare'] * $priceRatio,2);
    					$Data[$i]['iBaseFare']= $vSymbol.formatNum($iBaseFare);
						$fCommision = round($Data[$i]['fCommision'] * $priceRatio,2);
    					$Data[$i]['fCommision']= $vSymbol.formatNum($fCommision);
						$iMinFare = round($Data[$i]['iMinFare'] * $priceRatio,2);
    					$Data[$i]['iMinFare']= $vSymbol.formatNum($iMinFare);
    					$Data[$i]['vSymbol']= $vSymbol;
    					$Data[$i]['vCategoryTitle']= $vCategoryTitle;
    					$Data[$i]['vCategoryDesc']= $vCategoryDesc;
						$iParentId = $Data[$i]['iParentId'];
						if($iParentId == 0){
							$ePriceType = $Data[$i]['ePriceType']; 
							}else{
							$ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId,'','true');
						} 
						$Data[$i]['ePriceType'] = $ePriceType; 
						$Data[$i]['ALLOW_SERVICE_PROVIDER_AMOUNT']= $ePriceType == "Provider"? "Yes" :"No";
						//$Data[$i]['ALLOW_SERVICE_PROVIDER_AMOUNT']= $Data[$i]['ePriceType'] == "Provider"? "Yes" :"No";
					}
    				
    				$returnArr['Action']="1";
    				$returnArr['message'] = $Data;
    				//$returnArr['ALLOW_SERVICE_PROVIDER_AMOUNT'] = $ALLOW_SERVICE_PROVIDER_AMOUNT;
    				$returnArr['vCategoryTitle'] = $vCategoryTitle;
    				$returnArr['vCategoryDesc'] = $vCategoryDesc;
    				}else{
    				$returnArr['Action']="0"; 
    				$returnArr['message'] ="LBL_NO_DATA_AVAIL";
				}
				}else{
    			$returnArr['Action']="0"; 
    			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
			}
		}  
		echo json_encode($returnArr, JSON_HEX_QUOT | JSON_HEX_TAG);
	}
	
	if($type=="getBanners"){
		global $generalobj;
		
		$iMemberId = isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
		if($iMemberId != "") {
			
			$vLanguage=get_value('register_user', 'vLang', 'iDriverId',$iDriverId,'','true');
			if($vLanguage == "" || $vLanguage == NULL){
				$vLanguage = "EN";
			}
			
			$banners= get_value('banners', 'vImage', 'vCode',$vLanguage,' ORDER BY iDisplayOrder ASC');
			
			$data = array();
			$count =0;
			for($i = 0; $i< count($banners); $i++){
				if($banners[$i]['vImage'] != ""){
					$data[$count]['vImage'] = $tconfig["tsite_url"].'assets/img/images/'.$banners[$i]['vImage'];
					$count++;
				}
			}
			
			$returnArr['Action']="1"; 
			$returnArr['message'] =$data;
			}else{
			$returnArr['Action']="0"; 
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
	}
	
	if($type=="getUserVehicleDetails"){
		global $generalobj;
		
		$iMemberId = isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
		$user_type   = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Driver';
		$eType   = isset($_REQUEST["eType"]) ? $_REQUEST["eType"] : '';
		$APP_TYPE = $eType; 
		$vCountry = '';
		if ($user_type == "Passenger") {
			$tblname = "register_user";
			$vLangCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
			} else {
			$tblname = "register_driver";
			$driveData = get_value('register_driver', 'vLang,vCountry', 'iDriverId', $iMemberId);
			$vLangCode = $driveData[0]['vLang'];
			$vCountry = $driveData[0]['vCountry'];
		}
		if ($vLangCode == "" || $vLangCode == NULL) {
			$vLangCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
		}
		
		$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
		$lbl_all = $languageLabelsArr['LBL_ALL'];
		
		//$APP_TYPE = $generalobj->getConfigurations("configurations", "APP_TYPE");
		if($APP_TYPE == "Delivery"){
			$ssql.= " AND eType = 'Deliver'";
			}else if($APP_TYPE == "Ride-Delivery"){
			$ssql.= " AND ( eType = 'Deliver' OR eType = 'Ride')";
			}else{
			$ssql.= " AND eType = '".$APP_TYPE."'";
		}
		
		if($vCountry != ""){
			$iCountryId = get_value('country', 'iCountryId', 'vCountryCode', $vCountry, '', 'true');
			//$ssql.= " AND (iCountryId = '".$iCountryId."' OR iCountryId = '-1' OR iCountryId = '0')";
			$sql = "SELECT * FROM location_master WHERE eStatus='Active' AND iCountryId = '".$iCountryId."' AND eFor = 'VehicleType'";
			$db_country = $obj->MySQLSelect($sql);
			$country_str = "-1";
			if(count($db_country) > 0){
				for($i=0;$i<count($db_country);$i++){
					$country_str .= ",".$db_country[$i]['iLocationId']; 
				}
			}
			$ssql.= " AND iLocationid IN ($country_str) ";
		}
		
		$sql = "SELECT iVehicleTypeId,vVehicleType_".$vLangCode." as vVehicleType,iLocationid,iCountryId,iStateId,iCityId,eType FROM `vehicle_type` WHERE 1".$ssql;
		$db_vehicletype = $obj->MySQLSelect($sql);
		if($APP_TYPE == 'UberX'){
			$sql = "SELECT vCarType FROM `driver_vehicle` where iDriverId ='".$iMemberId."'";
			$db_vCarType = $obj->MySQLSelect($sql);
			
			/* if(count($db_vCarType) > 0){
				$vehicle_service_id= explode(",", $db_vCarType[0]['vCarType']);
				$data_service = array();
				for($i=0;$i<count($db_vehicletype); $i++){					
				$data_service[$i]=$db_vehicletype[$i];
				if(in_array($data_service[$i]['iVehicleTypeId'],$vehicle_service_id)){
				$data_service[$i]['VehicleServiceStatus']= 'true'; 							
				}else{
				$data_service[$i]['VehicleServiceStatus']= 'false'; 				
				}
				}	
			} */ 	
			if(count($db_vehicletype)>0 &&count($db_vCarType) > 0){
				$vehicle_service_id= explode(",", $db_vCarType[0]['vCarType']);
				for($i=0;$i<count($db_vehicletype); $i++){		
					if(in_array($db_vehicletype[$i]['iVehicleTypeId'],$vehicle_service_id)){
						$db_vehicletype[$i]['VehicleServiceStatus']= 'true'; 							
						}else{
						$db_vehicletype[$i]['VehicleServiceStatus']= 'false'; 				
					}
				}
			}
			}else{
			
			if(count($db_vehicletype)>0){
				for($i=0;$i<count($db_vehicletype); $i++){	
					if($db_vehicletype[$i]['iLocationid'] == "-1"){
						$db_vehicletype[$i]['SubTitle']= $lbl_all;
						}else{
						$sql = "SELECT vLocationName FROM location_master WHERE iLocationId = '".$db_vehicletype[$i]['iLocationid']."'";
						$locationname = $obj->MySQLSelect($sql);
						$db_vehicletype[$i]['SubTitle']= $locationname[0]['vLocationName'];
					}
					
					/*$iCountryId= $db_vehicletype[$i]['iCountryId'];
						$iStateId= $db_vehicletype[$i]['iStateId'];
						$iCityId= $db_vehicletype[$i]['iCityId'];
						
						$subTitle = "";
						if($iCountryId == "" || $iCountryId == 0 || $iCountryId == "0" || $iCountryId == -1 || $iCountryId == "-1"){
						$subTitle = $lbl_all;
						}else{
						$country = get_value('country', 'vCountry', 'iCountryId', $iCountryId, '', 'true');
						$subTitle = $country;
						}
						if($iStateId == "" || $iStateId == 0 || $iStateId == "0" || $iStateId == -1 || $iStateId == "-1"){
						$subTitle = $subTitle . "/".$lbl_all;
						}else{
						$state = get_value('state', 'vState', 'iStateId', $iStateId, '', 'true');
						$subTitle = $subTitle . "/".$state;
						}
						if($iCityId == "" || $iCityId == 0 || $iCityId == "0" || $iCityId == -1 || $iCityId == "-1"){
						$subTitle = $subTitle."/".$lbl_all;
						}else{
						$city = get_value('city', 'vCity', 'iCityId', $iCityId, '', 'true');
						$subTitle = $subTitle . "/".$city;
					}   */
					
					
				}
			}
		}	
		//$make = get_value('make', '*', 'eStatus', 'Active');
    $sql1 = "select * from make where eStatus = 'Active' ORDER BY vMake ASC ";
    $make = $obj->MySQLSelect($sql1);
		$start = @date('Y');
		$end = '1970';
		$year = array();
		for($j = $start; $j >= $end; $j--) {
			$year[] = strval($j);
			//$year .= $j.",";  
		}
		
		//echo "<pre>";print_r($year);exit;
		$carlist = array();
		if(count($make) > 0){
			//echo "<pre>";print_r($make);exit;
			for($i = 0; $i< count($make); $i++){
				//$ModelArr['List']=get_value('model', '*', 'iMakeId', $make[$i]['iMakeId']); 
				$sql = "SELECT  * FROM  `model` WHERE iMakeId = '" . $make[$i]['iMakeId'] . "' AND `eStatus` = 'Active' ORDER BY vTitle ASC ";
				$db_model = $obj->MySQLSelect($sql);
				$ModelArr['List'] = $db_model; 
				$carlist[$i]['iMakeId'] = $make[$i]['iMakeId'];
				$carlist[$i]['vMake'] = $make[$i]['vMake'];
				$carlist[$i]['vModellist'] = $ModelArr['List'];
			}
			$data['year'] = $year;
			$data['carlist'] = $carlist;
			
			$data['vehicletypelist'] = $db_vehicletype; 
			
      if(count($db_vehicletype) == 0){
         $returnArr['message1'] ="LBL_EDIT_VEHI_RESTRICTION_TXT";
      }
			
			$returnArr['Action']="1"; 
			$returnArr['message'] =$data;
			}else{
			$returnArr['Action']="0"; 
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}  
		//echo "<pre>";print_r($data);exit;
		$obj->MySQLClose();
		echo json_encode($returnArr);exit;  
	}
	
	###########################Add/Edit Driver Vehicle#######################################################
	if ($type == "UpdateDriverVehicle") {
		$iDriverVehicleId = isset($_REQUEST['iDriverVehicleId']) ? $_REQUEST['iDriverVehicleId'] : '';
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$iMakeId = isset($_REQUEST["iMakeId"]) ? $_REQUEST["iMakeId"] : '';
		$iModelId = isset($_REQUEST["iModelId"]) ? $_REQUEST["iModelId"] : '';
		$iYear = isset($_REQUEST["iYear"]) ? $_REQUEST["iYear"] : '';
		$vLicencePlate = isset($_REQUEST["vLicencePlate"]) ? $_REQUEST["vLicencePlate"] : '';
		$eCarX = isset($_REQUEST["eCarX"]) ? $_REQUEST["eCarX"] : 'No';
		$eCarGo = isset($_REQUEST["eCarGo"]) ? $_REQUEST["eCarGo"] : 'No';
		$vColour = isset($_REQUEST["vColor"]) ? $_REQUEST["vColor"] : '';
		//$eStatus = ($generalobj->getConfigurations("configurations", "VEHICLE_AUTO_ACTIVATION") == 'Yes') ? 'Active' : 'Inactive';
		$vCarType = isset($_REQUEST["vCarType"]) ? $_REQUEST["vCarType"] : '';
		$handiCap = isset($_REQUEST["HandiCap"]) ? $_REQUEST["HandiCap"] : 'No';
		$iVehicleCategoryId    =isset($_REQUEST["iVehicleCategoryId"]) ? $_REQUEST["iVehicleCategoryId"] : '';
		
		//$APP_TYPE = $generalobj->getConfigurations("configurations", "APP_TYPE");
		
		
		if($APP_TYPE == "UberX" && ($iDriverVehicleId == "" || $iDriverVehicleId == 0 || $iDriverVehicleId == NULL)){
			$iDriverVehicleId=get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $iDriverId,'','true');
		}
		if($APP_TYPE == "Ride-Delivery-UberX"){
			$query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type`";
			$result = $obj->MySQLSelect($query);
			$vCarType = $result[0]['countId']; 
		}
		
		$action = ($iDriverVehicleId != 0) ? 'Edit' : 'Add';
		if($action == "Add"){
			$eStatus = "Inactive";	
		}
    if($action == "Edit" && $ENABLE_EDIT_DRIVER_VEHICLE == "No" && $APP_TYPE != "UberX"){
        $returnArr['Action'] = "0";
			  $returnArr['message'] = "LBL_EDIT_VEHICLE_DISABLED";
        echo json_encode($returnArr);
		    exit;
    }
		$sql = "select iCompanyId from `register_driver` where iDriverId = '" . $iDriverId . "'";
		$db_usr = $obj->MySQLSelect($sql);
		$iCompanyId = $db_usr[0]['iCompanyId'];
		
		$Data_Driver_Vehicle['iDriverId'] = $iDriverId;
		$Data_Driver_Vehicle['iCompanyId'] = $iCompanyId;
		
		if(SITE_TYPE == "Demo"){
			$Data_Driver_Vehicle['eStatus'] = "Active"; 
			}else{
			if($action == "Add"){
				$Data_Driver_Vehicle['eStatus'] = $eStatus;
			}
		}
		## Update Vehicle Type For UberX ##
		if($APP_TYPE == "UberX" && $action == "Edit"){
			$sql = "select vCarType from `driver_vehicle` where iDriverVehicleId = '" . $iDriverVehicleId . "'";
			$vCarTypeData = $obj->MySQLSelect($sql);
			$vCarTypeData = explode(",",$vCarTypeData[0]['vCarType']);
			$sql = "select iVehicleTypeId from `vehicle_type` where iVehicleCategoryId = '" . $iVehicleCategoryId . "'";
			$db_vehicategoryid = $obj->MySQLSelect($sql);	
			$array_vehiclie_id = array();
			for($i=0; $i< count($db_vehicategoryid); $i++){	
				array_push($array_vehiclie_id,$db_vehicategoryid[$i]['iVehicleTypeId']);
			}
			$arraydiff = array_diff($vCarTypeData, $array_vehiclie_id);
			$sssql2 = "";
			if(count($arraydiff) > 0){
				$sssql2 =  implode(",",$arraydiff);
			}
			if($vCarType !=""){
				$vCarType = $vCarType.",".$sssql2;
				if($sssql2 == ""){
					$vCarType = substr($vCarType,0,-1);
				} 	
				}else{
				$vCarType = $sssql2;
			}
		} 
		## Update Vehicle Type For UberX ##        
		$Data_Driver_Vehicle['eCarX'] = $eCarX;
		$Data_Driver_Vehicle['eCarGo'] = $eCarGo;
		$Data_Driver_Vehicle['vCarType'] = $vCarType;
		$Data_Driver_Vehicle['eHandiCapAccessibility'] = $handiCap;
		
		if($iMakeId != ""){
			$Data_Driver_Vehicle['iMakeId'] = $iMakeId;
		}
		if($iModelId != ""){
			$Data_Driver_Vehicle['iModelId'] = $iModelId;
		}
		
		if($iYear != ""){
			$Data_Driver_Vehicle['iYear'] = $iYear;
		}
		
		if($vColour != ""){
			$Data_Driver_Vehicle['vColour'] = $vColour;
		}
		if($vLicencePlate != ""){
			$Data_Driver_Vehicle['vLicencePlate'] = $vLicencePlate;
		}
		
		if($APP_TYPE == 'UberX'){
			$Data_Driver_Vehicle['iCompanyId'] = "1";
			$Data_Driver_Vehicle['iMakeId'] = "3";
			$Data_Driver_Vehicle['iModelId'] = "1";
			$Data_Driver_Vehicle['iYear'] = Date('Y');
			$Data_Driver_Vehicle['vLicencePlate'] = "My Services";
			$Data_Driver_Vehicle['eStatus'] = "Active";
			$Data_Driver_Vehicle['eCarX'] = "Yes";
			$Data_Driver_Vehicle['eCarGo'] = "Yes";
		}
		
		// $Data_Driver_Vehicle['vColour'] = $vColour;
		// $Data_Driver_Vehicle['vLicencePlate'] = $vLicencePlate;
		
		if ($action == "Add") {
			
			
			$id = $obj->MySQLQueryPerform("driver_vehicle", $Data_Driver_Vehicle, 'insert');
			// $value['id'] = $id;
			// echo json_encode($value);
			// exit;
		} else {
			$where = " iDriverVehicleId = '" . $iDriverVehicleId . "'";
			$id = $obj->MySQLQueryPerform("driver_vehicle", $Data_Driver_Vehicle, 'update', $where);
		}
		
		if ($id > 0) {
			$returnArr['Action'] = "1";
			//$returnArr['message'] = GetDriverDetail($iDriverId);
			$returnArr['message'] = ($action == 'Add') ? 'LBL_VEHICLE_ADD_SUCCESS_NOTE' : 'LBL_VEHICLE_UPDATE_SUCCESS';
      $returnArr['VehicleInsertId'] = $id;
      $returnArr['VehicleStatus'] = $Data_Driver_Vehicle['eStatus'];
			//$eStatus = ($generalobj->getConfigurations("configurations", "VEHICLE_AUTO_ACTIVATION") == 'Yes') ? 'Active' : 'Inactive';
			} else {
			$returnArr['Action'] = "0";
			$returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	###########################Add/Edit Driver Vehicle End#######################################################
	################################Delete Driver Vehicle###############################################################
	
	################################Delete Driver Vehicle #######################################################
	if ($type == 'deletedrivervehicle') {
		global $generalobj, $tconfig,$obj;
		$returnArr = array();
		$iMemberCarId = isset($_REQUEST['iDriverVehicleId']) ? clean($_REQUEST['iDriverVehicleId']) : '';
		$iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
		// getLanguageCode($iMemberId); //create array of language_label
		$iDriverVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $iDriverId, '', 'true');
    	if($iDriverVehicleId == $iMemberCarId) {
    		$returnArr['Action'] = 0;
    		$returnArr['message'] = "LBL_DELETE_VEHICLE_ERROR";
    		echo json_encode($returnArr);
    		exit;
		}
		
		//$sql = "DELETE FROM driver_vehicle WHERE iDriverVehicleId='" . $iMemberCarId . "' AND iDriverId='" . $iDriverId . "'";
		$sql = "UPDATE driver_vehicle set eStatus='Deleted' WHERE iDriverVehicleId='".$iMemberCarId."' AND iDriverId = '".$iDriverId."'";
		$db_sql = $obj->sql_query($sql);
		//if (mysql_affected_rows() > 0) {
		if($obj->GetAffectedRows() > 0){
			$returnArr['Action'] = 1;
			$returnArr['message'] = "LBL_DELETE_VEHICLE";
			} else {
			$returnArr['Action'] = 0;
			$returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	
	###########################displayDocList##########################################################
	
	if ($type == "displayDocList") {
		global $generalobj, $tconfig;
		
		$iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
		$memberType = isset($_REQUEST['MemberType']) ? clean($_REQUEST['MemberType']) : 'Driver';
		$iDriverVehicleId = isset($_REQUEST['iDriverVehicleId']) ? clean($_REQUEST['iDriverVehicleId']) : '';
		$doc_usertype = isset($_REQUEST['doc_usertype']) ? clean(strtolower($_REQUEST['doc_usertype'])) : 'driver';
		
		if($doc_usertype == "vehicle"){
			$doc_usertype = "car";
		}
		$doc_userid = ($doc_usertype == 'car') ? $iDriverVehicleId : $iMemberId;
		//$APP_TYPE = $generalobj->getConfigurations("configurations", "APP_TYPE");
		
		/*$vCountry = get_value('register_driver', 'vCountry', 'iDriverId', $iMemberId,'',true);
		$vLang = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId,'',true);*/
    $UserData = get_value('register_driver', 'vCountry,vLang', 'iDriverId', $iMemberId);
    $vCountry = $UserData[0]['vCountry'];
    $vLang = $UserData[0]['vLang'];
		if($vLang=='' || $vLang == NULL)
		{
			$vLang= get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		
		$sql1= "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name_".$vLang." as doc_name ,dm.ex_status,dm.status, COALESCE(dl.doc_id,  '' ) as doc_id,COALESCE(dl.doc_masterid, '') as masterid_list ,COALESCE(dl.ex_date, '') as ex_date,COALESCE(dl.doc_file, '') as doc_file, COALESCE(dl.status, '') as status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" . $doc_userid . "' ) dl on dl.doc_masterid=dm.doc_masterid  
		where dm.doc_usertype='".$doc_usertype."' AND (dm.country='".$vCountry."' OR dm.country='All') and dm.status='Active' ";
		$db_vehicle = $obj->MySQLSelect($sql1);
		if (count($db_vehicle) > 0) {
			//$Photo_Gallery_folder = $tconfig['tsite_upload_driver_doc']."/".$iMemberId."/";
			if($doc_usertype == "driver") {
				$Photo_Gallery_folder = $tconfig['tsite_upload_driver_doc'] . "/" . $iMemberId . "/"; 
				} else {
				$Photo_Gallery_folder = $tconfig['tsite_upload_vehicle_doc_panel'] . "/" . $iDriverVehicleId . "/";
			}
			for($i=0;$i<count($db_vehicle);$i++){
				if($db_vehicle[$i]['doc_file'] != ""){
					$db_vehicle[$i]['vimage'] = $Photo_Gallery_folder.$db_vehicle[$i]['doc_file'];
					}else{
					$db_vehicle[$i]['vimage'] = "";
				}
        ## Checking for expire date of document ##
        $ex_date = $db_vehicle[$i]['ex_date'];
        $todaydate = date('Y-m-d');
        if($ex_date == "" || $ex_date == "0000-00-00"){
           $expire_document = "No";
        }else{
           if(strtotime($ex_date) < strtotime($todaydate)){
             $expire_document = "Yes";
           }else{
             $expire_document = "No";
           }
        }        
        $db_vehicle[$i]['EXPIRE_DOCUMENT'] = $expire_document;
        ## Checking for expire date of document ##        
			}
			$returnArr['Action'] = "1";
			$returnArr['message'] = $db_vehicle;
			} else {
			$returnArr['Action'] = "0";
			$returnArr['message'] = "LBL_NO_DOC_AVAIL";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	####################################################################################################
	
	###########################displaydrivervehicles##########################################################
	
	if ($type == "displaydrivervehicles") {
		global $generalobj, $tconfig;
		
		$iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
		$memberType = isset($_REQUEST['MemberType']) ? clean($_REQUEST['MemberType']) : 'Driver';
		
		$sql = "select iCompanyId from `register_driver` where iDriverId = '" . $iMemberId . "'";
		$db_usr = $obj->MySQLSelect($sql);
		$iCompanyId = $db_usr[0]['iCompanyId'];
		//$APP_TYPE = $generalobj->getConfigurations("configurations", "APP_TYPE");
		
		if ($APP_TYPE == 'UberX') {
			$sql = "SELECT * FROM driver_vehicle where iCompanyId = '" . $iCompanyId . "' and iDriverId = '" . $iMemberId . "' and eStatus != 'Deleted'";
			$db_vehicle = $obj->MySQLSelect($sql);
		} else {
			
			$sql="SELECT m.vTitle, mk.vMake,dv.* ,case WHEN (dv.vInsurance='' OR dv.vPermit='' OR dv.vRegisteration='') THEN 'TRUE' ELSE 'FALSE' END as 'VEHICLE_DOCUMENT'
			FROM driver_vehicle as dv JOIN model m ON dv.iModelId=m.iModelId JOIN make mk ON dv.iMakeId=mk.iMakeId where iCompanyId = '".$iCompanyId."' and iDriverId = '".$iMemberId."' and dv.eStatus != 'Deleted' Order By mk.vMake asc";  
			// echo   $sql = "SELECT m.vTitle, mk.vMake,dv.*  FROM driver_vehicle as dv JOIN model m ON dv.iModelId=m.iModelId JOIN make mk ON dv.iMakeId=mk.iMakeId where iCompanyId = '" . $iCompanyId . "' and iDriverId = '" . $iMemberId . "' and dv.eStatus != 'Deleted'";
			$db_vehicle = $obj->MySQLSelect($sql);
			$db_vehicle_new = $db_vehicle;
			for($i=0;$i<count($db_vehicle);$i++){
				$vCarType = $db_vehicle[$i]['vCarType']; 
				$sql = "SELECT iVehicleTypeId,eType  FROM `vehicle_type` WHERE `iVehicleTypeId` IN ($vCarType)";
				$db_cartype = $obj->MySQLSelect($sql);
				$k=0;   
				if (count($db_cartype) > 0) {
					for($j=0;$j<count($db_cartype);$j++){
						$eType = $db_cartype[$j]['eType'];
						if($eType == "UberX"){
							//unset($db_vehicle_new[$i]); 
						}
					}
				}
			}
		}
		$db_vehicle_new = array_values($db_vehicle_new); 
		if (count($db_vehicle_new) > 0) {
			$returnArr['Action'] = "1";
			$returnArr['message'] = $db_vehicle_new;
			} else {
			$returnArr['Action'] = "0";
			$returnArr['message'] = "LBL_NO_VEHICLES_FOUND";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);   
		exit;
	} 
	
	###########################Display Driver's Vehicle Listing End##########################################################
	###########################Add/Update Driver's Document and Vehilcle Document ##########################################################
	if ($type == "uploaddrivedocument") {
		global $generalobj, $tconfig;
		$iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
		$iDriverVehicleId = isset($_REQUEST['iDriverVehicleId']) ? clean($_REQUEST['iDriverVehicleId']) : '';
		//$doc_userid = isset($_REQUEST['doc_userid']) ? clean($_REQUEST['doc_userid']) : '';
		$memberType = isset($_REQUEST['MemberType']) ? clean($_REQUEST['MemberType']) : 'Driver';
		$doc_usertype = isset($_REQUEST['doc_usertype']) ? clean(strtolower($_REQUEST['doc_usertype'])) : 'driver';     // vehicle OR driver
		$doc_masterid = isset($_REQUEST['doc_masterid']) ? clean($_REQUEST['doc_masterid']) : '';
		$doc_name = isset($_REQUEST['doc_name']) ? clean($_REQUEST['doc_name']) : '';
		$doc_id = isset($_REQUEST['doc_id']) ? clean($_REQUEST['doc_id']) : '';
		$doc_file = isset($_REQUEST['doc_file']) ? clean($_REQUEST['doc_file']) : '';
		$ex_date = isset($_REQUEST['ex_date']) ? clean($_REQUEST['ex_date']) : '';
        $ex_status = isset($_REQUEST['ex_status']) ? clean($_REQUEST['ex_status']) : '';
        // $value['doc_name1'] = $doc_name;
        // echo json_encode($value);
        // exit;
		if($doc_usertype == "vehicle"){
			$doc_usertype = "car";
		}
		$doc_userid = ($doc_usertype == 'car') ? $iDriverVehicleId : $iMemberId;
		$status = ($doc_usertype == 'car') ? "Active" : "Inactive"; 
		$image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
		$image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
		//$image_name = "123.jpg";
		$action = ($doc_id != '') ? 'Edit' : 'Add';
		$addupdatemode = ($action == 'Add') ? 'insert' : 'update'; 
		
		if($doc_file != ""){
            $vImageName = $doc_file;

            // echo json_encode($vImageName);
            // exit;
        }else{
            
            if($doc_usertype == "driver") {
                //$tconfig['tsite_upload_driver_doc_path'] = '/var/www/html/webimages/upload/documents/driver';
                $Photo_Gallery_folder = $tconfig['tsite_upload_driver_doc_path'] . "/" . $iMemberId . "/"; 
                $value['upload_path'] = $Photo_Gallery_folder;
            } else {
                //$tconfig['tsite_upload_driver_doc_path'] = '/var/www/html/webimages/upload/documents/vheicles';
                $Photo_Gallery_folder = $tconfig['tsite_upload_vehicle_doc'] . "/" . $iDriverVehicleId . "/";
                $value['upload_path'] = $Photo_Gallery_folder;
            }
            
            if(!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
                //error_log("error_mkdir : ".error_get_last()."\n", 3, "/var/www/html/my.log");
            }
            $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png,xls,xlsx,csv");
            $vImageName = $vFile[0];
            
        }
            $value['doc_id'] = $doc_id;
            // $value['doc_file'] = $doc_file;
             $value['vImageName'] =$vFile;
            // $value['doc_usertype'] = $doc_usertype;
            // $value['doc_name'] = $doc_name;
            // $value['memberType'] = $memberType;
            // $value['image_object'] = (isset($image_object)?"ok":"null");
            // $value['ex_status'] = $ex_status;
            // echo json_encode($value);
            // exit;

		
		if ($vImageName != '') {
			$Data_Update["doc_masterid"] = $doc_masterid;
			$Data_Update["doc_usertype"] = $doc_usertype;
			$Data_Update["doc_userid"] = $doc_userid;
			$Data_Update["ex_date"] = $ex_date != '' ? $ex_date : '0000-00-00';
			$Data_Update["doc_file"] = $vImageName;
            $Data_Update["edate"] = @date("Y-m-d H:i:s");
            // $value['action11']   = $action;
            // echo json_encode($value);
            // exit;
			if($action == "Add"){
                $Data_Update["status"] = $status;
                // $value['status'] = $Data_Update["status"] ;
                // echo json_encode($value);
                // exit;
                $id = $obj->MySQLQueryPerform("document_list", $Data_Update, 'insert');
                
				}else{
				$where = " doc_id = '" . $doc_id . "'";
				$id = $obj->MySQLQueryPerform("document_list", $Data_Update, 'update', $where);
            }
			//$generalobj->save_log_data('0', $iMemberId, 'driver', $doc_name, $vImageName);
			if ($id > 0) {
				$returnArr['Action'] = "1";
				//$returnArr['message'] = getDriverDetailInfo($iMemberId);
				} else {
				$returnArr['Action'] = "0";
				$returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
			}
			} else {
			$returnArr['Action'] = "0";
			$returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	###########################Add/Update Driver's Document and Vehilcle Document Ends##########################################################
    ###########################Add/Update User's Vehicle Listing End##########################################################
	
	if($type=="UpdateUserVehicleDetails"){
		global $generalobj,$tconfig;
		$iUserVehicleId = isset($_REQUEST['iUserVehicleId'])?$_REQUEST['iUserVehicleId']:'';
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$iMakeId = isset($_REQUEST["iMakeId"]) ? $_REQUEST["iMakeId"] : '';
		$iModelId = isset($_REQUEST["iModelId"]) ? $_REQUEST["iModelId"] : '';
		$iYear = isset($_REQUEST["iYear"]) ? $_REQUEST["iYear"] : '';
		$vLicencePlate = isset($_REQUEST["vLicencePlate"]) ? $_REQUEST["vLicencePlate"] : '';
		$vColour = isset($_REQUEST["vColour"]) ? $_REQUEST["vColour"] : '';
		$eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Inactive';
		//$vImage = isset($_REQUEST["vImage"]) ? $_REQUEST["vImage"] : '';
		
		$image_name = $vImage = isset($_FILES['vImage']['name'])?$_FILES['vImage']['name']:'';
		$image_object	= isset($_FILES['vImage']['tmp_name'])?$_FILES['vImage']['tmp_name']:'';
		
		$Photo_Gallery_folder = $tconfig['tsite_upload_images_passenger_vehicle']."/".$iUserVehicleId."/"; // /webimages/upload/uservehicle
		
		// echo $Photo_Gallery_folder."===";
		if(!is_dir($Photo_Gallery_folder))
		mkdir($Photo_Gallery_folder, 0777);
		
		$action = ($iUserVehicleId != '')?'Edit':'Add';
		
		$Data_User_Vehicle['iUserId']=$iUserId;
		$Data_User_Vehicle['iMakeId']=$iMakeId;
		$Data_User_Vehicle['iModelId']=$iModelId;
		$Data_User_Vehicle['iYear']=$iYear;
		$Data_User_Vehicle['vLicencePlate']=$vLicencePlate;
		$Data_User_Vehicle['eStatus']=$eStatus;
		$Data_User_Vehicle['vColour']=$vColour;
		//$Data_User_Vehicle['vImage']=$vImage;
		
		if($action == "Add"){
			$id = $obj->MySQLQueryPerform("user_vehicle",$Data_User_Vehicle,'insert');
			$updateimageid = $id; 
			}else{
			$where = " iUserVehicleId = '".$iUserVehicleId."'";
			$updateimageid = $iUserVehicleId;
			$id = $obj->MySQLQueryPerform("user_vehicle",$Data_User_Vehicle,'update',$where);
		}
		
		if($image_name != ""){
			$vFile = $generalobj->fileupload($Photo_Gallery_folder,$image_object,$image_name,$prefix='', $vaildExt="pdf,doc,docx,jpg,jpeg,gif,png");
			$vImageName = $vFile[0];
			$Data_passenger["vImage"]=$vImageName;
			$where_image = " iUserVehicleId = '".$updateimageid."'";
			$id = $obj->MySQLQueryPerform("user_vehicle",$Data_passenger,'update',$where_image);
		}
		
		if($id > 0){
			$returnArr['Action'] = "1";
			$returnArr['message'] = getPassengerDetailInfo($iUserId);
			}else{
			$returnArr['Action'] ="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	
	if($type == "displayuservehicles"){
		global $generalobj,$tconfig;
		
		$iUserId 	= isset($_REQUEST['iUserId'])?clean($_REQUEST['iUserId']):'';
		$sql = "SELECT m.vTitle, mk.vMake,uv.*  FROM user_vehicle as uv JOIN model m ON uv.iModelId=m.iModelId JOIN make mk ON uv.iMakeId=mk.iMakeId where iUserId = '".$iUserId."' and uv.eStatus != 'Deleted'";
		$db_vehicle = $obj->MySQLSelect($sql);
		
		if(count($db_vehicle) > 0){
			$returnArr['Action'] = "1";
			$returnArr['message'] = $db_vehicle;
			}else{
			$returnArr['Action'] ="0";
			$returnArr['message'] ="No Vehicles Found";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	
	if($type == 'changelanguagelabel') { 
		$vLang = isset($_REQUEST['vLang'])?clean($_REQUEST['vLang']):'';
		$UpdatedLanguageLabels = getLanguageLabelsArr($vLang,"1");
		
		$lngData = get_value('language_master', 'vCode, vGMapLangCode, eDirectionCode as eType, vTitle', 'vCode', $vLang);
		
		$returnArr['Action'] = "1";
		$returnArr['message'] = $UpdatedLanguageLabels;
		$returnArr['vCode'] =  $lngData[0]['vCode'];
		$returnArr['vGMapLangCode'] = $lngData[0]['vGMapLangCode'];
		$returnArr['eType'] = $lngData[0]['eType'];
		$returnArr['vTitle'] = $lngData[0]['vTitle'];
		
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	
	if($type == 'displaytripcharges') {
		$TripID = isset($_REQUEST["TripID"]) ? $_REQUEST["TripID"] : '0';
		$destination_lat = isset($_REQUEST["dest_lat"]) ? $_REQUEST["dest_lat"] : '';
		$destination_lon = isset($_REQUEST["dest_lon"]) ? $_REQUEST["dest_lon"] : '';
		$iTripTimeId = isset($_REQUEST["iTripTimeId"]) ? $_REQUEST["iTripTimeId"] : '';
		//$ALLOW_SERVICE_PROVIDER_AMOUNT = $generalobj->getConfigurations("configurations", "ALLOW_SERVICE_PROVIDER_AMOUNT");
		
		$where = " iTripId = '".$TripID."'";
		$data_update['tEndDate']=@date("Y-m-d H:i:s");
		$data_update['tEndLat']=$destination_lat;
		$data_update['tEndLong']=$destination_lon;
		$obj->MySQLQueryPerform("trips",$data_update,'update',$where);
		
		if($iTripTimeId != "") {
			$where = " iTripTimeId = '$iTripTimeId'";
			$Data_update['dPauseTime']=$data_update['tEndDate'];
			$Data_update['iTripId']=$TripID;
			$id = $obj->MySQLQueryPerform("trip_times",$Data_update,'update',$where);
		}
		
		$sql = "SELECT * from trips WHERE iTripId = '".$TripID."'";
		$tripData = $obj->MySQLSelect($sql);
		// echo "<pre>"; print_r($tripData); die;
		$iDriverVehicleId = $tripData[0]['iDriverVehicleId'];
		$iVehicleTypeId = $tripData[0]['iVehicleTypeId'];
		$fVisitFee = $tripData[0]['fVisitFee'];
		$startDate = $tripData[0]['tStartDate'];
		$endDateOfTrip = $tripData[0]['tEndDate']; 
		$iQty = $tripData[0]['iQty']; 
		//$endDateOfTrip=@date("Y-m-d H:i:s");
    /*$iVehicleCategoryId=get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId',$iVehicleTypeId,'','true');
    $iParentId = get_value('vehicle_category', 'iParentId', 'iVehicleCategoryId', $iVehicleCategoryId,'','true');*/
    $sql = "SELECT vc.iParentId from vehicle_category as vc LEFT JOIN vehicle_type as vt ON vc.iVehicleCategoryId=vt.iVehicleCategoryId WHERE vt.iVehicleTypeId = '".$iVehicleTypeId."'";
		$VehicleCategoryData = $obj->MySQLSelect($sql);
    $iParentId = $VehicleCategoryData[0]['iParentId']; 
		if($iParentId == 0){
			$ePriceType=get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId',$iVehicleCategoryId,'','true');  
			}else{ 
			$ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId,'','true'); 
		}
		//$ePriceType=get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId',$iVehicleCategoryId,'','true');  
		$ALLOW_SERVICE_PROVIDER_AMOUNT = $ePriceType == "Provider"? "Yes" :"No";
		
		if($tripData[0]['eFareType'] == 'Hourly'){
			$sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$TripID'";
			$db_tripTimes = $obj->MySQLSelect($sql22);
			
			$totalSec = 0;
			$iTripTimeId = '';
			foreach($db_tripTimes as $dtT){
				if($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
					$totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
				}
			}
			$totalTimeInMinutes_trip=@round(abs($totalSec) / 60,2);
			}else {
			$totalTimeInMinutes_trip=@round(abs(strtotime($startDate) - strtotime($endDateOfTrip)) / 60,2);
		}
		$totalHour = $totalTimeInMinutes_trip / 60;
		$tripDistance=calcluateTripDistance($tripId);
		$sourcePointLatitude=$tripData[0]['tStartLat'];
		$sourcePointLongitude=$tripData[0]['tStartLong'];
		if($totalTimeInMinutes_trip <= 1){
			$FinalDistance = $tripDistance;
			}else{
			$FinalDistance = checkDistanceWithGoogleDirections($tripDistance,$sourcePointLatitude,$sourcePointLongitude,$destination_lat,$destination_lon);
		}
		$tripDistance=$FinalDistance;
		$fPickUpPrice = $tripData[0]['fPickUpPrice'];
		$fNightPrice = $tripData[0]['fNightPrice'];
		$eFareType = get_value('trips', 'eFareType', 'iTripId', $TripID, '', 'true');
		$surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);
		$fAmount = 0;
		$Fare_data = getVehicleFareConfig("vehicle_type", $iVehicleTypeId);
		// echo "<pre>"; print_r($tripData); die;
    $fPricePerKM = getVehicleCountryUnit_PricePerKm($iVehicleTypeId,$Fare_data[0]['fPricePerKM']);
		$Minute_Fare = round($Fare_data[0]['fPricePerMin'] * $totalTimeInMinutes_trip * $surgePrice,2);
		$Distance_Fare = round($fPricePerKM * $tripDistance * $surgePrice,2);
		$iBaseFare = round($Fare_data[0]['iBaseFare'] * $surgePrice,2);
		$iMinFare = round($Fare_data[0]['iMinFare'] * $surgePrice,2); 
		$total_fare = $iBaseFare + $Minute_Fare + $Distance_Fare; 
		if($iMinFare > $total_fare){
            $total_fare = $iMinFare; 
		}
		if($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes"){
			
			$sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='".$iDriverVehicleId."' AND iVehicleTypeId='".$iVehicleTypeId."'";
			$serviceProData = $obj->MySQLSelect($sqlServicePro);
            
            if(count($serviceProData) > 0){
				$fAmount = $serviceProData[0]['fAmount'];
				if($eFareType == "Fixed"){
					$fAmount = $fAmount * $iQty;
					}else if($eFareType == "Hourly"){
					$fAmount = $fAmount * $totalHour;
					}else{
					$fAmount = $total_fare;
				}
				}else{ 
				if($eFareType == "Fixed"){
					$fAmount = round($Fare_data[0]['fFixedFare'] * $iQty,2);
					}else if($eFareType == "Hourly"){
					$fAmount = round($Fare_data[0]['fPricePerHour'] * $totalHour,2);
					}else{
					$fAmount = $total_fare;
				}
			}
			}else{   
			if($eFareType == "Fixed"){
				$fAmount = round($Fare_data[0]['fFixedFare'] * $iQty,2);
				}else if($eFareType == "Hourly"){
				$fAmount = round($Fare_data[0]['fPricePerHour'] * $totalHour,2);
				}else{
				$fAmount = $total_fare;
			}
		}
		
		$final_display_charge = $fAmount+$fVisitFee;
		$returnArr['Action']="1";
		/*$vCurrencyDriver=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $tripData[0]['iDriverId'],'','true');
			$currencySymbolRationDriver = get_value('currency', 'vSymbol,Ratio', 'vName', $vCurrencyDriver);
		$returnArr['message']=$currencySymbolRationDriver[0]['vSymbol']." ".number_format(round($final_display_charge * $currencySymbolRationDriver[0]['Ratio'],1),2);*/
		//$currencySymbol = get_value('currency', 'vSymbol', 'eDefault', 'Yes','',true);
		$vCurrencyDriver=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $tripData[0]['iDriverId'],'','true');
		$currencySymbolRationDriver = get_value('currency', 'vSymbol,Ratio', 'vName', $vCurrencyDriver);   
		$currencySymbol = $currencySymbolRationDriver[0]['vSymbol'];
		$currencyRationDriver = $currencySymbolRationDriver[0]['Ratio']; 
		$final_display_charge = $final_display_charge * $currencyRationDriver;
		$final_display_charge = round($final_display_charge,2);
		//$final_display_charge = formatNum($final_display_charge);
		$returnArr['message']=$currencySymbol.' '.$final_display_charge;
		$returnArr['FareValue']=$final_display_charge;
		$returnArr['CurrencySymbol']=$currencySymbol;
		$obj->MySQLClose();
		echo json_encode($returnArr); exit;
	}
	
	########################### UBER-For-X ######################################
	###########################################################################
	###########################################################################
	if($type=="checkUserStatus"){
		$iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		//$APP_TYPE = $generalobj->getConfigurations("configurations", "APP_TYPE");
		
		if($UserType == "Passenger"){
			// $tblname = "register_user";
			// $fields = 'iUserId as iMemberId, vPhone,vPhoneCode as vPhoneCode, vEmail, vName, vLastName,vPassword, vLang';
			$condfield = 'iUserId';
			}else{
			// $tblname = "register_driver";
			// $fields = 'iDriverId  as iMemberId, vPhone,vCode as vPhoneCode, vEmail, vName, vLastName,vPassword, vLang';
			$condfield = 'iDriverId';
		}
		
		if($APP_TYPE == "UberX"){
			$sql = "SELECT iTripId FROM trips WHERE 1=1 AND $condfield = '".$iMemberId."' AND vTripPaymentMode != 'Cash' AND eType!='Ride' AND (iActive=	'Active' OR iActive='On Going Trip')";
			$checkStatus = $obj->MySQLSelect($sql);
			}else {
			$sql = "SELECT iTripId FROM trips WHERE 1=1 AND $condfield = '".$iMemberId."' AND vTripPaymentMode != 'Cash' AND eType='Ride' AND (iActive=	'Active' OR iActive='On Going Trip') order by iTripId DESC limit 1";
			$checkStatus = $obj->MySQLSelect($sql);
		}
		
		if(count($checkStatus) > 0){
			$returnArr['Action']="0";
			$returnArr['message'] = 'LBL_DIS_ALLOW_EDIT_CARD';
			}else {
			$returnArr['Action']="1";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	###########################################################################
	###########################################################################
	
	###########################################################################
	
	#########################################################################
	## NEW WEBSERVICE END ##
	##########################################################################
	############################ language_master #############################
	if($type == 'language_master') {
		
		$sql = "SELECT * FROM  `language_master` WHERE eStatus = 'Active' ";
		$all_label = $obj->MySQLSelect($sql);
		$returnArr['language_master_code'] = $all_label;
		echo json_encode($returnArr);
		exit;
	}
	##########################################################################
	
	if($type == 'GetLinksConfiguration'){
		$UserType = isset($_REQUEST['UserType'])?clean($_REQUEST['UserType']):'';
		
		if($UserType=='Passenger'){
			$DataArr['LINK_FORGET_PASS_PAGE_PASSENGER']=$tconfig["tsite_url"].$LINK_FORGET_PASS_PAGE_PASSENGER;
			$DataArr['FACEBOOK_APP_ID']=$FACEBOOK_APP_ID;
			$DataArr['CONFIG_CLIENT_ID']=$CONFIG_CLIENT_ID;
			$DataArr['GOOGLE_SENDER_ID']=$GOOGLE_SENDER_ID;
			$DataArr['MOBILE_VERIFICATION_ENABLE']=$MOBILE_VERIFICATION_ENABLE;
			
			echo json_encode($DataArr);
			}else if($UserType=='Driver'){
			$DataArr['LINK_FORGET_PASS_PAGE_DRIVER']=$tconfig["tsite_url"].$LINK_FORGET_PASS_PAGE_DRIVER;
			$DataArr['LINK_SIGN_UP_PAGE_DRIVER']=$tconfig["tsite_url"].$LINK_SIGN_UP_PAGE_DRIVER;
			$DataArr['GOOGLE_SENDER_ID']=$GOOGLE_SENDER_ID;
			$DataArr['MOBILE_VERIFICATION_ENABLE']=$MOBILE_VERIFICATION_ENABLE;
			
			echo json_encode($DataArr);
		}
	}
	
	##########################################################################
	
	if($type=='UpdateLanguageCode'){
		
		$lCode = isset($_REQUEST['vCode'])?clean(strtoupper($_REQUEST['vCode'])):''; // User's prefered language
		$UserID = isset($_REQUEST['UserID'])?clean($_REQUEST['UserID']):'';
		$UserType = isset($_REQUEST['UserType'])?clean($_REQUEST['UserType']):'';
		
		if($UserType=="Passenger"){
			
			$where = " iUserId = '$UserID'";
			$Data_update_passenger['vLang']=$lCode;
			
			$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
			// echo $id; exit;
			if($id<0){
				echo "UpdateFailed";
				exit;
			}
			}else if($UserType=="Driver"){
			$where = " iDriverId = '$UserID'";
			$Data_update_driver['vLang']=$lCode;
			
			$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
			// echo $id; exit;
			if($id<0){
				echo "UpdateFailed";
				exit;
			}
		}
		
		/* find default language of website set by admin */
		if($lCode == '') {
			$sql = "SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ";
			$default_label = $obj->MySQLSelect($sql);
			
			$lCode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode'])?$default_label[0]['vCode']:'EN';
		}
		
		$sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label`  WHERE  `vCode` = '".$lCode."' ";
		$all_label = $obj->MySQLSelect($sql);
		
		$x = array();
		for($i=0; $i<count($all_label); $i++){
			$vLabel = $all_label[$i]['vLabel'];
			$vValue = $all_label[$i]['vValue'];
			$x[$vLabel]=$vValue;
		}
		$x['vCode'] = $lCode; // to check in which languge code it is loading
		
		echo json_encode($x);
		
	}
	
	##########################################################################
	
	
	
	/* get variables value directly */
	if($type == 'get_value') {
		global $obj;
		$returnArr 	= array();
		$table 				= isset($_REQUEST['table'])?clean($_REQUEST['table']):'';
		$field_name 		= isset($_REQUEST['field_name'])?clean($_REQUEST['field_name']):'';
		$condition_field 	= isset($_REQUEST['condition_field'])?clean($_REQUEST['condition_field']):'';
		$condition_value 	= isset($_REQUEST['condition_value'])?clean($_REQUEST['condition_value']):'';
		
		$where		= ($condition_field != '')?' WHERE '.$condition_field:'';
		$where		.= ($where != '' && $condition_value != '')?' = "'.$condition_value.'"':'';
		
		$returnArr = get_value($table, $field_name, $condition_field, $condition_value);
		
		echo json_encode($returnArr);
		exit;
	}
	
	
	############################## Get DriverDetail ###################################
	if ($type == "getDriverDetail") {
		
		
		$Did = isset($_REQUEST["DriverAutoId"]) ? $_REQUEST["DriverAutoId"] : '';
		$GCMID = isset($_REQUEST["GCMID"]) ? $_REQUEST["GCMID"] : '';
		
		$sql = "SELECT iGcmRegId FROM `register_driver` WHERE iDriverId='$Did'";
		$Data = $obj->MySQLSelect($sql);
		
		if(count($Data)>0){
			
			$iGCMregID=$Data[0]['iGcmRegId'];
			
			if($GCMID!=''){
				
				if($iGCMregID!=$GCMID){
					$where = " iDriverId = '$Did' ";
					
					$Data_update_driver['iGcmRegId']=$GCMID;
					
					$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
				}
				
			}
			
		}
		$obj->MySQLClose();
		echo json_encode(getDriverDetailInfo($Did));
		
		exit;
		
	}
	###########################################################################
	
	######################## Get Driver Car Detail ############################
	if ($type == "getDriverCarDetail")
	{
		$Did = isset($_REQUEST["DriverAutoId"]) ? $_REQUEST["DriverAutoId"] : '';
		
		$sql = "SELECT make.vMake, model.vTitle, dv.*  FROM `driver_vehicle` dv, make, model WHERE dv.iDriverId='$Did' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId` AND dv.`eStatus`='Active'";
		
		$Data = $obj->MySQLSelect($sql);
		if (count($Data) > 0)
		{
			
			$i=0;
			while(count($Data)>$i){
				
				$Data[$i]['vModel']=$Data[$i]['vTitle'];
				$i++;
			}
			
			$returnArr['carList'] = $Data;
			
			echo json_encode($returnArr);
		}
		else
		{
			$returnArr['action'] = 0; //duplicate entry
			$returnArr['message'] = 'Fail';
			
			echo json_encode($returnArr);
		}
		
	}
	###########################################################################
	
	
	###########################################################################
	
	
	############################ checkUser_FB ################################
	
	if ($type == "checkUser_FB") {
		
		$fbid = isset($_REQUEST["fbid"]) ? $_REQUEST["fbid"] : '';
		$cityName = isset($_REQUEST["cityName"]) ? $_REQUEST["cityName"] : '';
		$emailId = isset($_REQUEST["emailId"]) ? $_REQUEST["emailId"] : '';
		$GCMID = isset($_REQUEST["GCMID"]) ? $_REQUEST["GCMID"] : '';
		$autoSign = isset($_REQUEST["autoSign"]) ? $_REQUEST["autoSign"] : '';
		$vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
		
		
		if($fbid==''){
			echo "LBL_NO_REG_FOUND";
			exit;
		}
		
		$sql    = "SELECT iUserId,eStatus,iGcmRegId FROM `register_user` WHERE vFbId=" . $fbid . " OR vEmail='$emailId'";
		$row = $obj->MySQLSelect($sql);
		
		if (count($row) > 0) {
			if($row[0]['eStatus']=="Active"){
				if($autoSign=="true"){
					$iGCMregID=$row[0]['iGcmRegId'];
					
					if($GCMID!=''){
						
						if($iGCMregID!=$GCMID){
							
							$iUserID_passenger=$row[0]['iUserId'];
							$where = " iUserId = '$iUserID_passenger' ";
							$Data_update_passenger['tSessionId'] = session_id().time();
							$Data_update_passenger['iGcmRegId']=$GCMID;
							$Data_update_passenger['vFirebaseDeviceToken']=$vFirebaseDeviceToken;
							
							$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
						}
						
					}
					
					}else{
					if($GCMID!=''){
						$iUserId_passenger=$row[0]['iUserId'];
						$where = " iUserId = '$iUserId_passenger' ";
						$Data_update_passenger['tSessionId'] = session_id().time();
						$Data_update_passenger['iGcmRegId']=$GCMID;
						$Data_update_passenger['vFirebaseDeviceToken']=$vFirebaseDeviceToken;
						
						$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
					}
				}
				
				echo json_encode(getPassengerDetailInfo($row[0]['iUserId'],$cityName));
				}else{
				echo "LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER";
			}
			
			} else {
			echo "LBL_NO_REG_FOUND";
		}
		
	}
	
	###########################################################################
	
	if($type=='checkFacebookUser'){
		$FbID = isset($_REQUEST["FbID"]) ? $_REQUEST["FbID"] : '';
		$EmailID = isset($_REQUEST["EmailID"]) ? $_REQUEST["EmailID"] : '';
		
		$sql    = "SELECT iUserId FROM `register_user` WHERE vFbId=" . $FbID . " OR vEmail='$EmailID' ";
		$row = $obj->MySQLSelect($sql);
		
		if(count($row)>0){
			echo "Failed";
			}else{
			echo "success";
		}
		exit;
	}
	
	###########################################################################
	
	######################### checkUser_passenger #############################
	
	if ($type == "checkUser_passenger") {
		
		$Emid = isset($_REQUEST["Email"]) ? $_REQUEST["Email"] : '';
		$Phone = isset($_REQUEST["Phone"]) ? $_REQUEST["Phone"] : '';
		
		$sql    = "SELECT vEmail,vPhone FROM `register_user` WHERE vEmail = '$Emid' OR vPhone = '$Phone'";
		$Data = $obj->MySQLSelect($sql);
		
		if ( count($Data) >0  )
		{
			
			if($Emid==$Data[0]['vEmail']){
				echo "EMAIL_EXIST";
				}else {
				echo "MOBILE_EXIST";
			}
		}
		else
		{
			echo "NO_REG_FOUND";
		}
		
	}
	###########################################################################
	
	######################## getDriverDetail_signIN ###########################
	
	if ($type == "getDriverDetail_signIN") {
		$Driver_email = $_REQUEST["DriverId"];
		$Password_driver = $generalobj->encrypt($_REQUEST["Pass"]);
		$GCMID = isset($_REQUEST["GCMID"]) ? $_REQUEST["GCMID"] : '';
		$vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
		
		
		$DeviceType = "Android";
		$sql = "SELECT rd.iDriverId,rd.eStatus,cmp.eStatus as cmpEStatus FROM `register_driver` as rd,`company` as cmp WHERE rd.vEmail='$Driver_email'  AND rd.vPassword='$Password_driver' AND cmp.iCompanyId=rd.iCompanyId";
		$Data = $obj->MySQLSelect($sql);
		
		if ( count($Data) > 0 ) {
			
			
			if($Data[0]['eStatus'] !="Deleted"){
				if($GCMID!=''){
					
					$iDriverId_driver=$Data[0]['iDriverId'];
					$where = " iDriverId = '$iDriverId_driver' ";
					
					$Data_update_driver['iGcmRegId']=$GCMID;
					$Data_update_driver['eDeviceType']=$DeviceType;
					$Data_update_driver['vFirebaseDeviceToken']=$vFirebaseDeviceToken;
					$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
					
				}
				echo json_encode(getDriverDetailInfo($Data[0]['iDriverId'],1));
				
				}else{
				echo "ACC_DELETED";
			}
		}
		else
		{
			$sql = "SELECT * FROM `register_driver` WHERE vEmail='$Driver_email'";
			$num_rows_Email = $obj->MySQLSelect($sql);
			if ( count($num_rows_Email) == 1 )
			{
				echo "LBL_PASSWORD_ERROR_TXT";
			}
			else
			{
				echo "LBL_NO_REG_FOUND";
			}
		}
		
	}
	
	###########################################################################
	
	###########################################################################
	if ($type == "getDetail_signIN_passenger") {
		
		$Emid = isset($_REQUEST["Email"]) ? $_REQUEST["Email"] : '';
		$Password_user = isset($_REQUEST["Pass"]) ? $_REQUEST["Pass"] : '';
		$cityName = isset($_REQUEST["cityName"]) ? $_REQUEST["cityName"] : '';
		$GCMID = isset($_REQUEST["GCMID"]) ? $_REQUEST["GCMID"] : '';
		$vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
		
		$Password_passenger = $generalobj->encrypt($Password_user);
		
		$DeviceType = "Android";
		
		$sql = "SELECT iUserId,eStatus,vLang,vTripStatus FROM `register_user` WHERE vEmail='$Emid'  && vPassword='$Password_passenger'";
		$Data = $obj->MySQLSelect($sql);
		
		/*$iCabRequestId= get_value('cab_request_now', 'max(iCabRequestId)', 'iUserId',$Data[0]['iUserId'],'','true');
		$eStatus_cab= get_value('cab_request_now', 'eStatus', 'iCabRequestId',$iCabRequestId,'','true');*/
    $sql_cabrequest = "SELECT iCabRequestId,eStatus FROM `cab_request_now` WHERE iUserId='".$Data[0]['iUserId']."' ORDER BY iCabRequestId DESC LIMIT 0,1";
    $Data_cabrequest = $obj->MySQLSelect($sql_cabrequest);
    $iCabRequestId = $Data_cabrequest[0]['iCabRequestId'];
    $eStatus_cab = $Data_cabrequest[0]['eStatus'];
		if ( count($Data) > 0 ) {
			if($Data[0]['eStatus']=="Active"){
				
				
                $iUserId_passenger=$Data[0]['iUserId'];
                $where = " iUserId = '$iUserId_passenger' ";
				
				if($GCMID!=''){
					$Data_update_passenger['tSessionId'] = session_id().time();
					$Data_update_passenger['iGcmRegId']=$GCMID;
					$Data_update_passenger['eDeviceType']=$DeviceType;
					$Data_update_passenger['vFirebaseDeviceToken']=$vFirebaseDeviceToken;
					
					$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
				}
				
				if($eStatus_cab == "Requesting"){
					$where1 = " iCabRequestId = '$iCabRequestId' ";
					$Data_update_cab_now['eStatus']="Cancelled";
					
					$id = $obj->MySQLQueryPerform("cab_request_now",$Data_update_cab_now,'update',$where1);
				}
				
				
				
					$returnArr['changeLangCode'] ="Yes";
					$returnArr['UpdatedLanguageLabels'] = getLanguageLabelsArr($Data[0]['vLang'],"1");
          $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ";
      		$defLangValues = $obj->MySQLSelect($sql);
          $returnArr['LIST_LANGUAGES']=$defLangValues;
          for($i = 0;$i<count($defLangValues); $i++){
             if($defLangValues[$i]['eDefault'] == "Yes"){
                $returnArr['DefaultLanguageValues']=$defLangValues[$i];
             }
          }
          $sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ";
          $defCurrencyValues =  $obj->MySQLSelect($sql);
      		$returnArr['LIST_CURRENCY']=$defCurrencyValues;
          for($i = 0;$i<count($defCurrencyValues); $i++){
             if($defCurrencyValues[$i]['eDefault'] == "Yes"){
                $returnArr['DefaultCurrencyValues']=$defCurrencyValues[$i];
             }
          }
				
				$returnArr['ProfileData'] = getPassengerDetailInfo($Data[0]['iUserId'],$cityName);
				echo json_encode($returnArr);
				}else{
				if($Data[0]['eStatus'] !="Deleted"){
					echo "LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER";
					}else{
					echo "ACC_DELETED";
				}
			}
			
		}
		else
		{
			$sql = "SELECT * FROM `register_user` WHERE vEmail='$Emid'";
			$num_rows_Email = $obj->MySQLSelect($sql);
			if ( count($num_rows_Email) == 1 )
			{
				echo "LBL_PASSWORD_ERROR_TXT";
			}
			else
			{
				echo "LBL_NO_REG_FOUND";
			}
		}
		
	}
	###########################################################################
	
	
	###########################################################################
	
	###########################################################################
	if($type == "getFareConfigurations"){
		
		
		$configurations=array();
		$configurations["LBL_PAYMENT_ENABLED"] = $PAYMENT_ENABLED;
		$configurations["LBL_BASE_FARE"]= $BASE_FARE;
		$configurations["LBL_FARE_PER_MINUTE"]= $FARE_PER_MINUTE;
		$configurations["LBL_FARE_PAR_KM"]= $FARE_PAR_KM;
		$configurations["LBL_SERVICE_TAX"]= $SERVICE_TAX;
		
		echo json_encode($configurations);
	}
	###########################################################################
	
	
	//**********************Update Details************************************//
	###########################################################################
	
	if ($type == "updatePassengerGcmID") {
		$user_id_auto = isset($_REQUEST["UidAuto"]) ? $_REQUEST['UidAuto'] : '';
		$GcmID        = isset($_REQUEST["GcmId"]) ? $_REQUEST['GcmId'] : '';
		
		$where = " iUserId = '".$user_id_auto."'";
		$Data['iGcmRegId']=$GcmID;
		$id = $obj->MySQLQueryPerform("register_user",$Data,'update',$where);
		
		
		if ($id) {
			echo "Update Successful..";
			} else {
			echo "No Update.";
		}
		
	}
	###########################################################################
	
	###########################################################################
	if ($type == "updateDriverGcmID") {
		$user_id_auto = isset($_REQUEST["UidAuto"]) ? $_REQUEST['UidAuto'] : '';
		$GcmID        = isset($_REQUEST["GcmId"]) ? $_REQUEST['GcmId'] : '';
		
		$where = " iDriverId = '".$user_id_auto."'";
		$Data['iGcmRegId']=$GcmID;
		$id = $obj->MySQLQueryPerform("register_driver",$Data,'update',$where);
		
		
		if ($id) {
			echo "Update Successful..";
			} else {
			echo "No Update.";
		}
		
	}
	###########################################################################
	
	
	
	###########################################################################
	
	if ($type == "getTripIdFor_driver") {
		
		
		$driver_id = isset($_REQUEST["driver_id"]) ? $_REQUEST["driver_id"] : '';
		
		$sql = "SELECT iTripId FROM `register_driver` WHERE iDriverId = '$driver_id'";
		$Data = $obj->MySQLSelect($sql);
		
		if ( count($Data) == 1 )
		{
			$current_trip_id = $Data[0]['iTripId'];
		}
		echo $current_trip_id;
		
	}
	
	###########################################################################
	if ($type == "updateUserImage") {
		
		$user_id_auto = isset($_REQUEST["user_id"]) ? $_REQUEST["user_id"] : '';
		$UIpath       = isset($_REQUEST["Path"]) ? $_REQUEST["Path"] : '';
		
		$where = " iUserId = '$user_id_auto'";
		$Data_update_passenger['vImgName']=$UIpath;
		
		$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
		
		
		if ($id) {
			echo "Update Successful..";
			} else {
			
			echo "Failed.";
		}
		
	}
	###########################################################################
	
	if ($type == "updateDriverImage") {
		
		$user_id_auto = isset($_REQUEST["user_id"]) ? $_REQUEST["user_id"] : '';
		$UIpath       = isset($_REQUEST["Path"]) ? $_REQUEST["Path"] : '';
		
		$where = " iDriverId = '$user_id_auto'";
		$Data_update_driver['vImage']=$UIpath;
		
		$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
		
		if ($id) {
			echo "Update Successful..";
			} else {
			
			echo "Failed.";
		}
		
	}
	
	###########################################################################
	
	if($type=="UpdateLastOnline_Driver"){
		
		$Did              = isset($_REQUEST["DriverAutoId"]) ? $_REQUEST["DriverAutoId"] : '';
		$availabilityStatus              = isset($_REQUEST["Status"]) ? $_REQUEST["Status"] : '';
		
		$where = " iDriverId='$Did'";
		
		$Data_update_driver['tLastOnline']=@date("Y-m-d H:i:s");
		$Data_update_driver['vAvailability']=$availabilityStatus;
		
		$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
		
		if($id>0){
			echo "UpdateSuccessful";
			}else{
			echo "Failed";
		}
	}
	###########################################################################
	###########################################################################
	
	if ($type == "update_pass_passenger_Detail") {
		$user_id_auto = isset($_REQUEST["user_id"]) ? $_REQUEST["user_id"] : '';
		$Upass        = isset($_REQUEST["pass"]) ? $_REQUEST["pass"] : '';
		
		$Password_passenger = $generalobj->encrypt($Upass);
		$where = " iUserId = '$user_id_auto'";
		$Data_update_passenger['vPassword']=$Password_passenger;
		
		$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
		
		
		if ($id >0) {
			
			echo json_encode(getPassengerDetailInfo($user_id_auto,"none"));
			
			} else {
			
			echo "Failed.";
		}
		
	}
	
	###########################################################################
	
	if ($type == "update_pass_Detail_driver") {
		$user_id_auto = isset($_REQUEST["user_id"]) ? $_REQUEST["user_id"] : '';
		$Upass        = isset($_REQUEST["pass"]) ? $_REQUEST["pass"] : '';
		
		$Password_driver = $generalobj->encrypt($Upass);
		
		$where = " iDriverId = '$user_id_auto'";
		$Data_update_driver['vPassword']=$Password_driver;
		
		$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
		
		
		if ($id >0) {
			echo json_encode(getDriverDetailInfo($user_id_auto));
			} else {
			echo "Failed.";
		}
		
	}
	
	###########################################################################
	
	if ($type == "update_payment_Detail_passenger") {
		
		$user_id_auto = isset($_REQUEST["user_id"]) ? $_REQUEST["user_id"] : '';
		$UcrdNO       = isset($_REQUEST["crd_no"]) ? $_REQUEST["crd_no"] : '';
		$UexMonth     = isset($_REQUEST["expMonth"]) ? $_REQUEST["expMonth"] : '';
		$UexYear      = isset($_REQUEST["expYear"]) ? $_REQUEST["expYear"] : '';
		$UCVV         = isset($_REQUEST["cvv_no"]) ? $_REQUEST['cvv_no'] : '';
		
		
		$where = " iUserId = '$user_id_auto'";
		$Data_update_passenger['vCreditCard']=$UcrdNO;
		$Data_update_passenger['vExpMonth']=$UexMonth;
		$Data_update_passenger['vExpYear']=$UexYear;
		$Data_update_passenger['vCvv']=$UCVV;
		
		$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
		
		
		if ($id) {
			echo "Update Successful..";
			} else {
			
			echo "No Update.";
		}
		
	}
	
	###########################################################################
	
	if ($type == "update_payment_Detail_driver") {
		
		$user_id_auto = isset($_REQUEST["user_id"]) ? $_REQUEST["user_id"] : '';
		$UcrdNO       = isset($_REQUEST["crd_no"]) ? $_REQUEST["crd_no"] : '';
		$UexMonth     = isset($_REQUEST["expMonth"]) ? $_REQUEST["expMonth"] : '';
		$UexYear      = isset($_REQUEST["expYear"]) ? $_REQUEST["expYear"] : '';
		$UCVV         = isset($_REQUEST["cvv_no"]) ? $_REQUEST['cvv_no'] : '';
		
		
		$where = " iDriverId = '$user_id_auto'";
		$Data_update_driver['vCreditCard']=$UcrdNO;
		$Data_update_driver['vExpMonth']=$UexMonth;
		$Data_update_driver['vExpYear']=$UexYear;
		$Data_update_driver['vCvv']=$UCVV;
		
		$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
		
		
		if ($id) {
			echo "Update Successful..";
			} else {
			
			echo "No Update.";
		}
		
	}
	
	###########################################################################
	
	if ($type == "updateName_Mobile_Detail_passenger") {
		
		$Fname        = isset($_REQUEST["Fname"]) ? $_REQUEST["Fname"] : '';
		$Lname        = isset($_REQUEST["Lname"]) ? $_REQUEST["Lname"] : '';
		$Umobile      = isset($_REQUEST["mobile"]) ? $_REQUEST["mobile"] : '';
		$user_id_auto = isset($_REQUEST["user_id"]) ? $_REQUEST['user_id'] : '';
		$phoneCode = isset($_REQUEST["phoneCode"]) ? $_REQUEST['phoneCode'] : '';
		
		
		$where = " iUserId = '$user_id_auto'";
		$Data_update_passenger['vName']=$Fname;
		$Data_update_passenger['vLastName']=$Lname;
		$Data_update_passenger['vPhone']=$Umobile;
		$Data_update_passenger['vPhoneCode']=$phoneCode;
		
		$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
		
		if ($id >0) {
			echo json_encode(getPassengerDetailInfo($user_id_auto,"none"));
			} else {
			echo "Failed.";
		}
		
	}
	
	
	
	###########################################################################
	
	if ($type == "updateName_Mobile_Detail_driver") {
		
		$Fname        = isset($_REQUEST["Fname"]) ? $_REQUEST["Fname"] : '';
		$Lname        = isset($_REQUEST["Lname"]) ? $_REQUEST["Lname"] : '';
		$Umobile      = isset($_REQUEST["mobile"]) ? $_REQUEST["mobile"] : '';
		$user_id_auto = isset($_REQUEST["user_id"]) ? $_REQUEST['user_id'] : '';
		$phoneCode = isset($_REQUEST["phoneCode"]) ? $_REQUEST['phoneCode'] : '';
		
		
		$where = " iDriverId = '$user_id_auto'";
		$Data_update_driver['vName']=$Fname;
		$Data_update_driver['vLastName']=$Lname;
		$Data_update_driver['vPhone']=$Umobile;
		$Data_update_driver['vCode']=$phoneCode;
		
		$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
		
		
		if ($id >0) {
			echo json_encode(getDriverDetailInfo($user_id_auto));
			} else {
			echo "Failed.";
		}
	}
	
	###########################################################################
	
	if ($type == "uploadImage_driver") {
		
		$target_path      = "webimages/upload/";
		$user_id          = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
		$base             = isset($_REQUEST['image']) ? $_REQUEST['image'] : '';
		$name             = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '';
		$target_path_temp = $target_path . "Driver/";
		$target_path      = $target_path_temp . $user_id . "/";
		
		if (is_dir($target_path) === false) {
			mkdir($target_path, 0755);
		}
		// base64 encoded utf-8 string
		$binary = base64_decode($base);
		
		header('Content-Type: bitmap; charset=utf-8');
		
		$time_val = time();
		$img_arr = explode(".", $name);
		$fileextension = $img_arr[count($img_arr) - 1];
		
		$Random_filename = mt_rand(11111, 99999);
		// $ImgFileName="3_".$name;
		$ImgFileName=$time_val . "_" . $Random_filename . "." . $fileextension;
		
		$file = fopen($target_path . '/' . $ImgFileName, "w");
		
		fwrite($file, $binary);
		fclose($file);
		
		$path = $target_path . $ImgFileName;
		
		
		if (file_exists($path)) {
			
			$where = " iDriverId = '".$user_id."'";
			$Data_Driver['vImage']=$ImgFileName;
			$id = $obj->MySQLQueryPerform("register_driver",$Data_Driver,'update',$where);
			
			if($id > 0){
				// echo "UPLOADSUCCESS";
				$thumb->createthumbnail($target_path . '/' . $ImgFileName); // generate image_file, set filename to resize/resample
				$thumb->size_auto($tconfig["tsite_upload_images_member_size1"]);    // set the biggest width or height for thumbnail
				$thumb->jpeg_quality(100);
				$thumb->save($target_path . "1" . "_" . $time_val . "_" . $Random_filename . "." . $fileextension);
				
				$thumb->createthumbnail($target_path . "/" . $ImgFileName);   // generate image_file, set filename to resize/resample
				$thumb->size_auto($tconfig["tsite_upload_images_member_size2"]);       // set the biggest width or height for thumbnail
				$thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
				$thumb->save($target_path . "2" . "_" . $time_val . "_" . $Random_filename . "." . $fileextension);
				
				$thumb->createthumbnail($target_path . "/" . $ImgFileName);   // generate image_file, set filename to resize/resample
				$thumb->size_auto($tconfig["tsite_upload_images_member_size3"]);       // set the biggest width or height for thumbnail
				$thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
				$thumb->save($target_path . "3" . "_" . $time_val . "_" . $Random_filename . "." . $fileextension);
				
				$returnArrayImg['Action']="SUCCESS";
				$returnArrayImg['ImgName']='3_'.$ImgFileName;
				echo json_encode($returnArrayImg);
				}else{
				echo "Failed";
			}
			
			} else {
			// handle the error
			
			echo "Failed";
		}
		
		exit;
		
	}
	
	###########################################################################
	
	if ($type == "uploadImage_passenger") {
		
		$target_path      = "webimages/upload/";
		$user_id          = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
		$base             = isset($_REQUEST['image']) ? $_REQUEST['image'] : '';
		$name             = isset($_REQUEST['cmd']) ? $_REQUEST['cmd'] : '';
		
		$target_path_temp = $target_path . "Passenger/";
		$target_path      = $target_path_temp . $user_id . "/";
		
		if (is_dir($target_path) === false) {
			mkdir($target_path, 0777);
		}
		// base64 encoded utf-8 string
		$binary = base64_decode($base);
		// binary, utf-8 bytes
		header('Content-Type: bitmap; charset=utf-8');
		
		$time_val = time();
		$img_arr = explode(".", $name);
		$fileextension = $img_arr[count($img_arr) - 1];
		
		$Random_filename = mt_rand(11111, 99999);
		// $ImgFileName="3_".$name;
		$ImgFileName=$time_val . "_" . $Random_filename . "." . $fileextension;
		
		$file = fopen($target_path . '/' . $ImgFileName, "w");
		
		fwrite($file, $binary);
		fclose($file);
		
		$path = $target_path . $ImgFileName;
		
		if (file_exists($path)) {
			
			$where = " iUserId = '".$user_id."'";
			$Data_passenger['vImgName']=$ImgFileName;
			$id = $obj->MySQLQueryPerform("register_user",$Data_passenger,'update',$where);
			
			if($id > 0){
				// echo "UPLOADSUCCESS";
				$thumb->createthumbnail($target_path . '/' . $ImgFileName); // generate image_file, set filename to resize/resample
				$thumb->size_auto($tconfig["tsite_upload_images_member_size1"]);    // set the biggest width or height for thumbnail
				$thumb->jpeg_quality(100);
				$thumb->save($target_path . "1" . "_" . $time_val . "_" . $Random_filename . "." . $fileextension);
				
				$thumb->createthumbnail($target_path . "/" . $ImgFileName);   // generate image_file, set filename to resize/resample
				$thumb->size_auto($tconfig["tsite_upload_images_member_size2"]);       // set the biggest width or height for thumbnail
				$thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
				$thumb->save($target_path . "2" . "_" . $time_val . "_" . $Random_filename . "." . $fileextension);
				
				$thumb->createthumbnail($target_path . "/" . $ImgFileName);   // generate image_file, set filename to resize/resample
				$thumb->size_auto($tconfig["tsite_upload_images_member_size3"]);       // set the biggest width or height for thumbnail
				$thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
				$thumb->save($target_path . "3" . "_" . $time_val . "_" . $Random_filename . "." . $fileextension);
				
				$returnArrayImg['Action']="SUCCESS";
				$returnArrayImg['ImgName']='3_'.$ImgFileName;
				echo json_encode($returnArrayImg);
				//exit;
				}else{
				echo "Failed";
			}
			} else {
			echo "Failed";
		}
		
	}
	
	
	
	###########################################################################
	
	
	###########################################################################
	
	if($type=="registerFbUser"){
		$fbid             = isset($_REQUEST["fbid"]) ? $_REQUEST["fbid"] : '';
		$Fname             = isset($_REQUEST["Fname"]) ? $_REQUEST["Fname"] : '';
		$Lname             = isset($_REQUEST["Lname"]) ? $_REQUEST["Lname"] : '';
		$email            = isset($_REQUEST["email"]) ? $_REQUEST["email"] : '';
		$GCMID            = isset($_REQUEST["GCMID"]) ? $_REQUEST["GCMID"] : '';
		$phone_mobile     = isset($_REQUEST["phone"]) ? $_REQUEST["phone"] : '';
		$CountryCode = isset($_REQUEST["CountryCode"]) ? $_REQUEST["CountryCode"] : '';
		$PhoneCode = isset($_REQUEST["PhoneCode"]) ? $_REQUEST["PhoneCode"] : '';
		$vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
		
		// $Language_Code=($obj->MySQLSelect("SELECT `vCode` FROM `language_master` WHERE `eDefault`='Yes'")[0]['vCode']);
		$Language_Code= get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		
		$deviceType="Android";
		
		$sql = "SELECT * FROM `register_user` WHERE vEmail = '$email' OR vPhone = '$phone_mobile'";
		$check_passenger    = $obj->MySQLSelect($sql);
		
		if(count($check_passenger)>0){
			if($email==$check_passenger[0]['vEmail']){
				echo "EMAIL_EXIST";
				}else {
				echo "MOBILE_EXIST";
			}
			}else{
			
			$Data_passenger['vFbId']=$fbid;
			$Data_passenger['vName']=$Fname;
			$Data_passenger['vLastName']=$Lname;
			$Data_passenger['vEmail']=$email;
			$Data_passenger['vPhone']=$phone_mobile;
			$Data_passenger['vPassword']='';
			$Data_passenger['iGcmRegId']=$GCMID;
			$Data_passenger['vLang']=$Language_Code;
			$Data_passenger['vPhoneCode']=$PhoneCode;
			$Data_passenger['vCountry']=$CountryCode;
			$Data_passenger['vFirebaseDeviceToken']=$vFirebaseDeviceToken;
			$Data_passenger['eDeviceType']=$deviceType;
			// $Data_passenger['vCurrencyPassenger']=($obj->MySQLSelect("SELECT vName FROM currency WHERE eDefault='Yes'")[0]['vName']);
			$Data_passenger['vCurrencyPassenger']=get_value('currency', 'vName', 'eDefault','Yes','','true');
			
			$id = $obj->MySQLQueryPerform("register_user",$Data_passenger,'insert');
			
			if ($id > 0) {
				/*new added*/
				
				echo json_encode(getPassengerDetailInfo($id,$cityName));
				
				$maildata['EMAIL'] = $email;
				$maildata['NAME'] = $Fname;
				$maildata['PASSWORD'] = $password;
				$generalobj->send_email_user("MEMBER_REGISTRATION_USER",$maildata);
				} else {
				echo "Registration UnSuccessful.";
			}
		}
	}
	
	###########################################################################
	###########################################################################
	
	###########################################################################
	
	
	
	if($type=="setVehicleTypes"){
		// $startDate="2016-04-04 14:33:58";
		
		// echo date('dS M \a\t h:i a',strtotime($startDate));
		// $value= get_value('user_emergency_contact', 'COUNT(iEmergencyId) as Count', 'iUserId', "34");
		// echo $value[0]['Count'];
		// echo $res = preg_replace("/[^0-9]/", "", "Every 6.1,0--//+2 Months" );
		
		/* $tripID    = isset($_REQUEST["tripID"]) ? $_REQUEST["tripID"] : '';
			$rating  = isset($_REQUEST["rating"]) ? $_REQUEST["rating"] : '';
			
			$iUserId =get_value('trips', 'iUserId', 'iTripId',$tripID,'','true');
			$tableName = "register_user";
			$where = " WHERE iUserId='".$iUserId."'";
			
			$sql = "SELECT vAvgRating FROM ".$tableName.' '.$where;
            $fetchAvgRating= $obj->MySQLSelect($sql);
			
			
			
            $fetchAvgRating[0]['vAvgRating'] = floatval($fetchAvgRating[0]['vAvgRating']);
			// echo  "Fetch:".$fetchAvgRating[0]['vAvgRating'];exit;
			
			if($fetchAvgRating[0]['vAvgRating'] > 0){
			$average_rating = round(($fetchAvgRating[0]['vAvgRating'] + $rating) / 2,1);
			}else{
			$average_rating = round($fetchAvgRating[0]['vAvgRating'] + $rating,1);
			}
			
            $Data_update['vAvgRating']=$average_rating;
			
		echo "AvgRate:".$Data_update['vAvgRating']; */
		
		$langCodesArr = get_value('language_master', 'vCode', '', '');
		
		print_r($langCodesArr);
		
		echo "<BR/>";
		
		for($i=0;$i<count($langCodesArr);$i++){
			$currLngCode = $langCodesArr[$i]['vCode'];
			$vVehicleType = $langCodesArr[$i]['vVehicleType'];
			$fieldName = "vVehicleType_".$currLngCode;
			$suffixName = $i==0?"vVehicleType":"vVehicleType_".$langCodesArr[$i-1]['vCode'];
			
			
			$sql = "ALTER TABLE vehicle_type ADD ".$fieldName." VARCHAR(50) AFTER"." ".$suffixName;
			$id= $obj->sql_query($sql);
		}
		
		
		$vehicleTypesArr = get_value('vehicle_type', 'vVehicleType,iVehicleTypeId', '', '');
		
		for($j=0;$j<count($vehicleTypesArr);$j++){
			$vVehicleType = $vehicleTypesArr[$j]['vVehicleType'];
			$iVehicleTypeId = $vehicleTypesArr[$j]['iVehicleTypeId'];
			
			echo "vVehicleType:".$vVehicleType."<BR/>";
			for($k=0;$k<count($langCodesArr);$k++){
				$currLngCode = $langCodesArr[$k]['vCode'];
				$fieldName = "vVehicleType_".$currLngCode;
				$suffixName = $k==0?"vVehicleType":"vVehicleType_".$langCodesArr[$k-1]['vCode'];
				
				
				// $sql = "ALTER TABLE vehicle_type ADD ".$fieldName." VARCHAR(50) AFTER"." ".$suffixName;
				// $id= $obj->sql_query($sql);
				echo $sql = "UPDATE `vehicle_type` SET ".$fieldName." = '".$vVehicleType."' WHERE iVehicleTypeId = '$iVehicleTypeId'";
				echo "<br/>";
				$id1= $obj->sql_query($sql);
				
				echo "<br/>".$id1;
			}
			
		}
		
		// echo $sql = "UPDATE `vehicle_type` SET ".$fieldName." = ".$vVehicleType;
		// $id1= $obj->sql_query($sql);
		// echo "<br/>".$id;
		
	}
	###########################################################################
	
	if ($type == "callToDriver_Message") {
		
		$driver_id_auto = isset($_REQUEST["DautoId"])?$_REQUEST["DautoId"]:'';
		$user_id_auto   =isset($_REQUEST["UautoId"])?$_REQUEST["UautoId"]:'';
		$message_rec    = isset($_REQUEST["message_rec"])?$_REQUEST["message_rec"]:'';
		$message        = isset($_REQUEST["message"])?$_REQUEST["message"]:'';
		$tripID   = isset($_REQUEST["tripID"])?$_REQUEST["tripID"]:'0';
		
		$sender_type = "Passenger";
		
		$where = " iUserId = '$user_id_auto'";
		
		$Data_update_Messages['tMessage']=$message;
		$Data_update_Messages['tSendertype']=$sender_type;
		$Data_update_Messages['iTripId']=$tripID;
		
		$id = $obj->MySQLQueryPerform("driver_user_messages",$Data_update_Messages,'insert');
		
		$message_new_combine = $message_rec . $message;
		
		$DArray = explode(',', $driver_id_auto);
		
		foreach ($DArray as $key => $val) {
			
			$sql = "SELECT iGcmRegId FROM register_driver WHERE iDriverId='$val'  AND eDeviceType = 'Android'";
			$result = $obj->MySQLSelect($sql);
			
			$rows[] = $result[0];
			
		}
		
		
		foreach ($rows as $item) {
			
			$registatoin_ids = $item['iGcmRegId'];
			
			
			$Rregistatoin_ids = array(
            $registatoin_ids
			);
			
			$Rmessage = array(
            "message" => $message_new_combine
			);
			$result   = send_notification($Rregistatoin_ids, $Rmessage);
			
			echo $result;
		}
		
	}
	
	###########################################################################
	
	if ($type == "callToUser_Message") {
		
		$driver_id_auto = isset($_REQUEST["DautoId"])?$_REQUEST["DautoId"]:'';
		$user_id_auto   = isset($_REQUEST["UautoId"])?$_REQUEST["UautoId"]:'';
		$message_rec    = isset($_REQUEST["message_rec"])?$_REQUEST["message_rec"]:'';
		$message        = isset($_REQUEST["message"])?$_REQUEST["message"]:'';
		$tripID   = isset($_REQUEST["tripID"])?$_REQUEST["tripID"]:'0';
		
		$sender_type = "Driver";
		
		$Data_update_Messages['tMessage']=$message;
		$Data_update_Messages['tSendertype']=$sender_type;
		$Data_update_Messages['iTripId']=$tripID;
		
		$id = $obj->MySQLQueryPerform("driver_user_messages",$Data_update_Messages,'insert');
		
		$message_new_combine = $message_rec . $message;
		
		$sql = "SELECT iGcmRegId FROM register_user WHERE iUserId='$user_id_auto'  AND eDeviceType = 'Android'";
		$result = $obj->MySQLSelect($sql);
		
		$registatoin_ids = $result[0]['iGcmRegId'];
		
		$Rregistatoin_ids = array(
        $registatoin_ids
		);
		$Rmessage         = array(
        "message" => $message_new_combine
		);
		$result           = send_notification($Rregistatoin_ids, $Rmessage);
		
		echo $result;
		
		
	}
	
	###########################################################################
	
	if ($type == "submit_rating_user") {
		
		$usr_email = isset($_REQUEST["usr_email"]) ? $_REQUEST["usr_email"] : '';
		$driver_id = isset($_REQUEST["driver_id"]) ? $_REQUEST["driver_id"] : '';
		$tripID    = isset($_REQUEST["tripID"]) ? $_REQUEST["tripID"] : '0';
		$rating_1  = isset($_REQUEST["rating_1"]) ? $_REQUEST["rating_1"] : '';
		
		$message   = isset($_REQUEST["message"]) ? $_REQUEST['message'] : '';
		$tripVerificationCode   = isset($_REQUEST["verification_code"]) ? $_REQUEST['verification_code'] : '';
		
		$average_rating = $rating_1;
		
		$sql = "SELECT iVerificationCode FROM `trips`  WHERE  iTripId='$tripID'";
		$row_code = $obj->MySQLSelect($sql);
		
		$verificationCode=$row_code[0]['iVerificationCode'];
		
		// if($tripVerificationCode==$verificationCode){
		
		$VerificationStatus="Verified";
		
		
		$where = " iTripId = '$tripID'";
		
		$Data_update_trips['eVerified']=$VerificationStatus;
		
		$id = $obj->MySQLQueryPerform("trips",$Data_update_trips,'update',$where);
		
		
		$sql = "SELECT vAvgRating FROM `register_user` WHERE iUserId='$usr_email'";
		$row = $obj->MySQLSelect($sql);
		
		
		$average_rating = ($row[0]['vAvgRating'] + $average_rating) / 2;
		
		$usrType   = "Driver";
		
		$sql = "SELECT * FROM `ratings_user_driver` WHERE iTripId = '$tripID' && eUserType = '$usrType'";
		$row = $obj->MySQLSelect($sql);
		
		
		if (count($row) > 0) {
			echo "LBL_RATING_EXIST";
			
			} else {
			
			$where = " iUserId = '$usr_email'";
			
			$Data_update_passenger['vAvgRating']=round($average_rating,1);
			
			$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
			
			
			$Data_update_ratings['iTripId']=$tripID;
			$Data_update_ratings['vRating1']=$rating_1;
			$Data_update_ratings['vMessage']=$message;
			$Data_update_ratings['eUserType']=$usrType;
			
			$id = $obj->MySQLQueryPerform("ratings_user_driver",$Data_update_ratings,'insert');
			
			
			if ($id >0) {
				echo "Ratings Successful.";
				} else {
				
				echo "Ratings UnSuccessful.";
			}
			sendTripReceiptAdmin($tripID);
		}
		
		
	}
	
	###########################################################################
	
	if($type=="submit_rating_driver"){
		
		$usr_email = isset($_REQUEST["usr_email"]) ? $_REQUEST["usr_email"] : '';
		$driver_id = isset($_REQUEST["driver_id"]) ? $_REQUEST["driver_id"] : '';
		$tripID    = isset($_REQUEST["tripID"]) ? $_REQUEST["tripID"] : '0';
		$rating_1  = isset($_REQUEST["rating_1"]) ? $_REQUEST["rating_1"] : '';
		$message   = isset($_REQUEST["message"]) ? $_REQUEST['message'] : '';
		$tripVerificationCode   = isset($_REQUEST["verification_code"]) ? $_REQUEST['verification_code'] : '';
		//$average_rating=($rating_1+$rating_2+$rating_3+$rating_4)/4 ;
		
		$average_rating = $rating_1;
		
		$usrType   = "Passenger";
		
		$sql = "SELECT * FROM `ratings_user_driver` WHERE iTripId = '$tripID' and eUserType = '$usrType'";
		$row_check = $obj->MySQLSelect($sql);
		
		$sql = "SELECT vAvgRating FROM `register_driver` WHERE iDriverId = '$driver_id'";
		$row = $obj->MySQLSelect($sql);
		
		$average_rating = ($row[0]['vAvgRating'] + $average_rating) / 2;
		
		
		if (count($row_check) > 0) {
			
			echo "LBL_RATING_EXIST";
			
			} else {
			
			$where = " iDriverId = '$driver_id'";
			
			$Data_update_driver['vAvgRating']=round($average_rating,1);
			
			$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
			
			$Data_update_ratings['iTripId']=$tripID;
			$Data_update_ratings['vRating1']=$rating_1;
			$Data_update_ratings['vMessage']=$message;
			$Data_update_ratings['eUserType']=$usrType;
			
			$id = $obj->MySQLQueryPerform("ratings_user_driver",$Data_update_ratings,'insert');
			
			
			if ($id) {
				echo "Ratings Successful.";
				} else {
				
				echo "Ratings UnSuccessful.";
			}
			
			sendTripReceipt($tripID);
			
		}
		
	}
	
	###########################################################################
	
	if ($type == "updateLog") {
		$Uid          = isset($_REQUEST["access_sign_token_user_id_auto"]) ? $_REQUEST["access_sign_token_user_id_auto"] : '';
		
		$where = " iUserId='$Uid'";
		$Data_update_passenger['vLogoutDev']="false";
		
		$id = $obj->MySQLQueryPerform("register_user",$Data_update_passenger,'update',$where);
		
		if ($id) {
			echo "Update Successful";
		}
		
	}
	
	
	
	###########################################################################
	
	if($type=='getCarTypes'){
		$sql="SELECT * FROM vehicle_type";
		
		$row_result_vehivle_type = $obj->MySQLSelect($sql);
		
		$arr_temp['Types']=$row_result_vehivle_type;
		echo  json_encode($arr_temp);
	}
	
	
	###########################################################################
	
	###########################################################################
	
	if($type=='CheckVerificationCode'){
		$tripId          = isset($_REQUEST["TripId"]) ? $_REQUEST["TripId"] : '0';
		
		$sql="SELECT eVerified FROM trips WHERE iTripId=$tripId";
		
		$result_eVerified = $obj->MySQLSelect($sql);
		
		if($result_eVerified[0]['eVerified']=="Verified"){
            echo "Verified";
			}else{
			echo "Not Verified";
		}
		
	}
	###########################################################################
	###########################################################################
	
	if($type=='AddPaypalPaymentData'){
		$tripId          = isset($_REQUEST["TripId"]) ? $_REQUEST["TripId"] : '0';
		$PayPalPaymentId          = isset($_REQUEST["PayPalPaymentId"]) ? $_REQUEST["PayPalPaymentId"] : '';
		$PaidAmount          = isset($_REQUEST["PaidAmount"]) ? $_REQUEST["PaidAmount"] : '';
		
		$Data_payments['tPaymentUserID']=$PayPalPaymentId;
		$Data_payments['vPaymentUserStatus']="approved";
		$Data_payments['iTripId']=$tripId;
		$Data_payments['iAmountUser']=$PaidAmount;
		
		$id = $obj->MySQLQueryPerform("payments",$Data_payments,'insert');
		if($id>0){
			echo "PaymentSuccessful";
			}else{
			echo "PaymentUnSuccessful";
		}
	}
	
	####################### To get Currency Values ##############################
	
	if($type =="getCurrencyList"){
		// $returnArr['List']=($obj->MySQLSelect("SELECT * FROM currency WHERE eStatus='Active'"));
		$returnArr['List']=get_value('currency', '*', 'eStatus', 'Active');
		echo json_encode($returnArr);
	}
	
	####################### To get Currency Values END############################
	
	
	####################### Update Currency Values ##############################
	
	if($type =="updateCurrencyValue"){
		$Uid          = isset($_REQUEST["UserID"]) ? $_REQUEST["UserID"] : '';
		$currencyCode = isset($_REQUEST["vCurrencyCode"]) ? $_REQUEST["vCurrencyCode"] : '';
		$UserType     = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : '';
		
		if($UserType == "Driver"){
			$where = " iDriverId = '$Uid'";
			$Data_update_user['vCurrencyDriver']=$currencyCode;
			$id = $obj->MySQLQueryPerform("register_driver",$Data_update_user,'update',$where);
			}else{
			$where = " iUserId = '$Uid'";
			$Data_update_user['vCurrencyPassenger']=$currencyCode;
			$id = $obj->MySQLQueryPerform("register_user",$Data_update_user,'update',$where);
		}
		
		
		if($id){
			echo "SUCCESS";
			}else{
			echo "UpdateFailed";
		}
	}
	
	####################### To get Currency Values END############################
	
	
	if($type=="enc_pass"){
		$pass 		= isset($_REQUEST['pass'])?clean($_REQUEST['pass']):'';
		
		echo $generalobj->encrypt($pass);
	}
    
    if($type=="DeclineTripRequest"){
  		$passenger_id = isset($_REQUEST["PassengerID"]) ? $_REQUEST["PassengerID"] : '';
		$driver_id      = isset($_REQUEST["DriverID"]) ? $_REQUEST["DriverID"] : '';
		$vMsgCode      = isset($_REQUEST["vMsgCode"]) ? $_REQUEST["vMsgCode"] : '';
		
    $sql = "SELECT iDriverRequestId,eAcceptAttempted FROM `driver_request` WHERE iDriverId = '" . $driver_id . "' AND iUserId = '" . $passenger_id . "' AND iTripId = '0' AND vMsgCode='".$vMsgCode."' AND eAcceptAttempted = 'No'";
		$db_sql = $obj->MySQLSelect($sql);
    if(count($db_sql) > 0){
        $request_count = UpdateDriverRequest2($driver_id,$passenger_id,"0","Decline",$vMsgCode,"No");
    }else{
        $request_count = 0;
    }
		
		echo $request_count;
		
	}
	
	###########################################################################
	###########################################################################
	###########################################################################
	###########################################################################
	
	
	if($type =="getOngoingUserTrips"){
		global $generalobj,$obj;
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		
		$vLangCode=get_value('register_user', 'vLang', 'iUserId',$iUserId,'','true');
		if($vLangCode == "" || $vLangCode == NULL){
			$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		
		$Data1 = array();
		if($iUserId != "") {
			$sql1 = "SELECT rd.iDriverId,rd.vImage as driverImage,concat(rd.vName,' ',rd.vLastName) as driverName, rd.vCode ,rd.vPhone as driverMobile ,rd.vLatitude as driverLatitude,rd.vLongitude as driverLongitude,rd.vTripStatus as driverStatus, rd.vAvgRating as driverRating, tr.`vRideNo`, tr.tSaddress,tr.iTripId, tr.iVehicleTypeId,tr.tTripRequestDate,tr.eFareType,tr.vTimeZone from trips as tr 
			LEFT JOIN register_driver as rd ON rd.iDriverId=tr.iDriverId
			WHERE tr.iActive != 'Canceled' AND iActive != 'Finished' AND iUserId='".$iUserId."' ORDER BY tr.iTripId DESC";
			
			$Data1 = $obj->MySQLSelect($sql1);
			if(count($Data1) > 0){
				for($i=0;$i<count($Data1);$i++){
					$iVehicleCategoryId=get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId',$Data1[$i]['iVehicleTypeId'],'','true');
					$vVehicleTypeName=get_value('vehicle_type', 'vVehicleType_'.$vLangCode, 'iVehicleTypeId',$Data1[$i]['iVehicleTypeId'],'','true');
					if($iVehicleCategoryId != 0){
						$vVehicleCategoryName = get_value('vehicle_category', 'vCategory_'.$vLangCode, 'iVehicleCategoryId',$iVehicleCategoryId,'','true');
						$vVehicleTypeName = $vVehicleCategoryName . "-" . $vVehicleTypeName;
					}
					$Data1[$i]['SelectedTypeName'] = $vVehicleTypeName;
					// Convert Into Timezone
					$tripTimeZone = $Data1[$i]['vTimeZone'];
					if($tripTimeZone != ""){
						$serverTimeZone = date_default_timezone_get();
						$Data1[$i]['tTripRequestDate'] = converToTz($Data1[$i]['tTripRequestDate'],$tripTimeZone,$serverTimeZone);
					}
					// Convert Into Timezone
					$Data1[$i]['dDateOrig'] = $Data1[$i]['tTripRequestDate'];
				}
				$returnArr['Action'] = "1";
				$returnArr['SERVER_TIME'] = date('Y-m-d H:i:s');
				$returnArr['message'] = $Data1;
				}else {
				$returnArr['Action'] = "0"; 
				$returnArr['message'] = "LBL_NO_ONGOING_TRIPS_AVAIL";
			}
			}else {
			$returnArr['Action'] = "0"; 
			$returnArr['message'] = "LBL_NO_ONGOING_TRIPS_AVAIL";
		}
		echo json_encode($returnArr);
	}
	
	if($type =="getTripDeliveryLocations"){
		global $generalobj,$obj;
		$iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '0';
		$userType = isset($_REQUEST["userType"]) ? $_REQUEST["userType"] : 'Passenger';
		$Data = array();
		if($iTripId != "") {
			if($userType != 'Passenger') {
				$sql = "SELECT ru.iUserId,ru.vimgname as riderImage,concat(ru.vName,' ',ru.vLastName) as riderName, ru.vPhoneCode ,ru.vPhone as riderMobile,ru.vTripStatus as driverStatus, ru.vAvgRating as riderRating, tr.* from trips as tr 
				LEFT JOIN register_user as ru ON ru.iUserId=tr.iUserId
				WHERE tr.iTripId = '".$iTripId."'";
				$dataUser = $obj->MySQLSelect($sql);
				$Data['driverDetails'] =$dataUser[0];
				$iMemberId=get_value('trips', 'iDriverId', 'iTripId',$iTripId,'','true');
				$vLangCode=get_value('register_driver', 'vLang', 'iDriverId',$iMemberId,'','true');
				}else {
				$sql = "SELECT rd.iDriverId,rd.vImage as driverImage,concat(rd.vName,' ',rd.vLastName) as driverName, rd.vCode ,rd.vPhone as driverMobile,rd.vTripStatus as driverStatus, rd.vAvgRating as driverRating, tr.* from trips as tr 
				LEFT JOIN register_driver as rd ON rd.iDriverId=tr.iDriverId
				WHERE tr.iTripId = '".$iTripId."'";
				$dataUser = $obj->MySQLSelect($sql);
				$Data['driverDetails'] =$dataUser[0];
				$iMemberId=get_value('trips', 'iUserId', 'iTripId',$iTripId,'','true');
				$vLangCode=get_value('register_user', 'vLang', 'iUserId',$iMemberId,'','true');
			}
			if($vLangCode == "" || $vLangCode == NULL){
				$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
			}
			$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
			$lbl_at = $languageLabelsArr['LBL_AT_GENERAL'];
			$lbl_minago = $languageLabelsArr['LBL_MIN_AGO'];
      if($userType == "Driver"){
        $Driver_Acceprt_Delivery_Request = $languageLabelsArr['LBL_DRIVER1_ACCEPTED_DELIVERY_REQUEST_TXT'];
  			$Driver_Arrived_Pick_Location= $languageLabelsArr['LBL_DRIVER1_ARRIVED_PICK_LOCATION_TXT'];
  			$Driver_Start_job= $languageLabelsArr['LBL_PROVIDER1_START_JOB_TXT'];
  			$Driver_Finished_job= $languageLabelsArr['LBL_PROVIDER1_FINISHED_JOB_TXT'];
      }else{
			$Driver_Acceprt_Delivery_Request = $languageLabelsArr['LBL_DRIVER_ACCEPTED_DELIVERY_REQUEST_TXT'];
			$Driver_Arrived_Pick_Location= $languageLabelsArr['LBL_DRIVER_ARRIVED_PICK_LOCATION_TXT'];
			$Driver_Start_job= $languageLabelsArr['LBL_PROVIDER_START_JOB_TXT'];
			$Driver_Finished_job= $languageLabelsArr['LBL_PROVIDER_FINISHED_JOB_TXT'];
      }
			$testBool = 1;
			
			if(count($dataUser) > 0){
				$Data['States'] = array();
				$Data_tTripRequestDate= $dataUser[0]['tTripRequestDate'];
				$Data_tDriverArrivedDate= $dataUser[0]['tDriverArrivedDate'];
				$Data_dDeliveredDate= $dataUser[0]['dDeliveredDate'];
				$Data_tStartDate= $dataUser[0]['tStartDate'];
				$Data_tEndDate= $dataUser[0]['tEndDate'];
				$i=0;
				
				if($Data_tTripRequestDate != "" && $Data_tTripRequestDate != "0000-00-00 00:00:00" && $testBool == 1){
					$msg = 'Provider accepted the request.';
					if($userType != 'Passenger'){
						$msg = 'You accepted the request.';
					}
					$Data['States'][$i]['text'] = $Driver_Acceprt_Delivery_Request;
					$Data['States'][$i]['time'] = date("h:i A",strtotime($Data_tTripRequestDate)); 
					$Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_tTripRequestDate) - strtotime(date("Y-m-d H:i:s"))) / 60,0)." ". $lbl_minago; 
					$Data['States'][$i]['type'] = "Accept";
					$i++;
					}else {
					$testBool = 0;
				}
				
				if($Data_tDriverArrivedDate != "" && $Data_tDriverArrivedDate != "0000-00-00 00:00:00" && $testBool == 1){
					$msg = "Provider arrived to your location.";
					if($userType != 'Passenger'){
						$msg = "You arrived to user's location.";
					}
					$Data['States'][$i]['text'] = $Driver_Arrived_Pick_Location;
					$Data['States'][$i]['time'] = date("h:i A",strtotime($Data_tDriverArrivedDate)); 
					$Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_tDriverArrivedDate) - strtotime(date("Y-m-d H:i:s"))) / 60,0)." ". $lbl_minago; 
					$Data['States'][$i]['type'] = "Arrived"; 
					$i++;
					}else {
					$testBool = 0;
				}
				
				if($Data_tStartDate != "" && $Data_tStartDate != "0000-00-00 00:00:00" && $testBool == 1){
					$msg = 'Provider has started the job.';
					if($userType != 'Passenger'){
						$msg = 'You started the job.';
					}
					$Data['States'][$i]['text'] = $Driver_Start_job; 
					$Data['States'][$i]['time'] = date("h:i A",strtotime($Data_tStartDate)); 
					$Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_tStartDate) - strtotime(date("Y-m-d H:i:s"))) / 60,0)." ". $lbl_minago; 
					$Data['States'][$i]['type'] = "Onway";
					$i++;
					}else {
					$testBool = 0;
				}
				
				if($Data_tEndDate != "" && $Data_tEndDate != "0000-00-00 00:00:00" && $testBool == 1 && $dataUser[0]['iActive'] == "Finished"){
					$msg = 'Provider has completed the job.';
					if($userType != 'Passenger'){
						$msg = 'You completed the job.';
					}
					$Data['States'][$i]['text'] = $Driver_Finished_job; 
					$Data['States'][$i]['time'] = date("h:i A",strtotime($Data_tEndDate)); 
					$Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_tEndDate) - strtotime(date("Y-m-d H:i:s"))) / 60,0)." ". $lbl_minago; 
					$Data['States'][$i]['type'] = "Delivered"; 
					$i++;
				}
				}else{
				$Data['States'] = array();
			}
			if(count($Data) > 0){
				$returnArr['Action'] = "1";
				$returnArr['message'] = $Data;
				}else {
				$returnArr['Action'] = "0"; 
				$returnArr['message'] = "LBL_NO_DRIVER_FOUND";
			}
			}else {
			$returnArr['Action'] = "0"; 
			$returnArr['message'] = "LBL_NO_TRIP_FOUND";
		}
		echo json_encode($returnArr);
	}
	
	if($type =="SetTimeForTrips"){
		global $generalobj,$obj;
		$iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '0';
		$iTripTimeId = isset($_REQUEST["iTripTimeId"]) ? $_REQUEST["iTripTimeId"] : '';
		$dTime = date('Y-m-d H:i:s');
		
		if($iTripTimeId == '') {
			$Data_update['dResumeTime']=$dTime;
			$Data_update['iTripId']=$iTripId;
			$id = $obj->MySQLQueryPerform("trip_times",$Data_update,'insert');
			$returnArr['Action'] = "1";
			$returnArr['message'] = $id;
			}else {
			$where = " iTripTimeId = '$iTripTimeId'";
			$Data_update['dPauseTime']=$dTime;
			$Data_update['iTripId']=$iTripId;
			$id = $obj->MySQLQueryPerform("trip_times",$Data_update,'update',$where);
			$returnArr['Action'] = "1";
			$returnArr['message'] = $id;
		}
		$sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$iTripId'";
		$db_tripTimes = $obj->MySQLSelect($sql22);
		
		$totalSec = 0;
		$timeState = 'Pause';
		$iTripTimeId = '';
		foreach($db_tripTimes as $dtT){
			if($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
				$totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
				}else {
				$totalSec += strtotime(date('Y-m-d H:i:s')) - strtotime($dtT['dResumeTime']);
			}
		}
		$returnArr['totalTime'] = $totalSec;
		$obj->MySQLClose();
		echo json_encode($returnArr); exit;
	}
	
	if($type == "getYearTotalEarnings"){
		global $generalobj,$obj;
		
		$iDriverId = isset($_REQUEST['iDriverId'])?clean($_REQUEST['iDriverId']):'';
		$year  = isset($_REQUEST["year"]) ? $_REQUEST["year"] : @date('Y');
		if($year == ""){
			$year = @date('Y'); 
		}
		
		$vCurrencyDriver=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iDriverId,'','true');
		$vCurrencySymbol=get_value('currency', 'vSymbol', 'vName', $vCurrencyDriver,'','true');
		
		$start = @date('Y');
		$end = '1970';
		$year_arr = array();
		for($j = $start; $j >= $end; $j--) {
  			$year_arr[] = strval($j);
		}
		
		$Month_Array = array('01' => 'Jan', '02' =>'Feb', '03' =>'Mar', '04' =>'Apr', '05' =>'May', '06' =>'Jun', '07' =>'Jul', '08' =>'Aug', '09' =>'Sep', '10' =>'Oct', '11' =>'Nov', '12' =>'Dec');
		
		$sql = "SELECT * FROM trips WHERE iDriverId='".$iDriverId."' AND tTripRequestDate LIKE '".$year."%'";
		$tripData = $obj->MySQLSelect($sql);
		$totalEarnings = 0;
		
		//if(count($tripData) > 0){
		for($i=0;$i<count($tripData);$i++){
			$iFare = $tripData[$i]['fTripGenerateFare'];
			$fCommision = $tripData[$i]['fCommision'];
			$priceRatio = $tripData[$i]['fRatio_'.$vCurrencyDriver];
			$totalEarnings += ($iFare - $fCommision) * $priceRatio;
		}
		
		$yearmontharr = array();
		$yearmontearningharr_Max = array();
		foreach ($Month_Array as $key=>$value) {
			$tripyearmonthdate = $year."-".$key;
			$sql_Month = "SELECT * FROM trips WHERE iDriverId='".$iDriverId."' AND tTripRequestDate LIKE '".$tripyearmonthdate."%'";
			$tripyearmonthData = $obj->MySQLSelect($sql_Month);
			$tripData_M = strval(count($tripyearmonthData));
			$yearmontearningharr = array();
			$totalEarnings_M = 0;
			for($j=0;$j<count($tripyearmonthData);$j++){
				$iFare_M = $tripyearmonthData[$j]['fTripGenerateFare'];
				$fCommision_M = $tripyearmonthData[$j]['fCommision'];
				$priceRatio_M = $tripyearmonthData[$j]['fRatio_'.$vCurrencyDriver];
				$totalEarnings_M += ($iFare_M - $fCommision_M) * $priceRatio_M;  
			}
			$yearmontearningharr_Max[] = $totalEarnings_M;
			$yearmontearningharr["CurrentMonth"] = $value;
			$yearmontearningharr["TotalEarnings"] = strval(round($totalEarnings_M < 0 ? 0 : $totalEarnings_M,1));
			$yearmontearningharr["TripCount"] = strval(round($tripData_M,1));
			array_push($yearmontharr,$yearmontearningharr);
		}
		foreach ($yearmontearningharr_Max as $key => $value) {
			if ($value >= $max) 
            $max = $value;     
		}
		$returnArr['Action'] = "1";
		$returnArr['TotalEarning'] = $vCurrencySymbol." ".strval(round($totalEarnings,1));
		$returnArr['TripCount'] = strval(count($tripData));
		$returnArr["CurrentYear"] = $year;
		$returnArr['MaxEarning'] = strval($max);
		$returnArr['YearMonthArr'] = $yearmontharr;
		$returnArr['YearArr'] = $year_arr;
		/*}else{
			$returnArr['Action'] = "0";
		} */
		
		echo json_encode($returnArr);
	}     
	/* For Forgot Password */
	if($type == 'requestResetPassword'){ 
		global $generalobj,$obj,$tconfig;
		$Emid = isset($_REQUEST["vEmail"]) ? $_REQUEST["vEmail"] : '';
		$userType = isset($_REQUEST["UserType"])?clean($_REQUEST["UserType"]):''; // UserType = Driver/Passenger
		if($userType == "" || $userType == NULL){
			$userType = "Passenger";    
		}
		if($userType == "Passenger"){
			$tblname = "register_user";
			$fields = 'iUserId as iMemberId, vPhone,vPhoneCode as vPhoneCode, vEmail, vName, vLastName, vPassword, vLang';
			$condfield = 'iUserId';
			$EncMembertype= base64_encode(base64_encode('rider'));
			}else{
			$tblname = "register_driver";
			$fields = 'iDriverId  as iMemberId, vPhone,vCode as vPhoneCode, vEmail, vName, vLastName,	vPassword, vLang';
			$condfield = 'iDriverId';
			$EncMembertype= base64_encode(base64_encode('driver'));
		}
		$sql="select $fields from $tblname where vEmail = '".$Emid."'";
		$db_member = $obj->MySQLSelect($sql);
		if(count($db_member) > 0){
			$vLangCode = $db_member[0]['vLang'];
			if($vLangCode == "" || $vLangCode == NULL){
		    	$vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
			}
			$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
			$clickherelabel = $languageLabelsArr['LBL_CLICKHERE_SIGNUP'];
			
			$milliseconds = time();
			$tempGenrateCode = substr($milliseconds, 1);
			$Today=Date('Y-m-d H:i:s');
			$today= base64_encode(base64_encode($Today));
			$type= $EncMembertype;
			$id= $generalobj->encrypt($db_member[0]["iMemberId"]);
			$newToken = $generalobj->RandomString(32);
			$url = $tconfig["tsite_url"].'reset_password.php?type='.$type.'&id='.$id.'&_token='.$newToken;
			$activation_text = '<a href="'.$url.'" target="_blank"> '.$clickherelabel.' </a>';
			$maildata['EMAIL'] = $db_member[0]["vEmail"];
			$maildata['NAME'] = $db_member[0]["vName"]." ".$db_member[0]["vLastName"];				
			$maildata['LINK'] = $activation_text; 
			$status = $generalobj->send_email_user("CUSTOMER_RESET_PASSWORD",$maildata);
			if($status == 1){
				$sql = "UPDATE $tblname set vPassword_token='".$newToken."' WHERE vEmail='".$Emid."' and eStatus != 'Deleted'";  
				$obj->sql_query($sql);
				
				$returnArr['Action'] = "1";
				$returnArr['message'] = "LBL_PASSWORD_SENT_TXT";
				}else{
				$returnArr['Action'] = "0";
				$returnArr['message'] = "LBL_ERROR_PASSWORD_MAIL";
			}
			}else{
			$returnArr['Action'] = "0";
  			$returnArr['message'] = "LBL_WRONG_EMAIL_PASSWORD_TXT";
		}  
		$obj->MySQLClose();
		echo json_encode($returnArr);exit;  
	}
	/* For Forgot Password */
	
  	###########################################################################
	/* For Driver Vehicle Details */
	if($type=="getDriverVehicleDetails"){
		$driverId = isset($_REQUEST['iDriverId'])?clean($_REQUEST['iDriverId']):'';
		$userType = isset($_REQUEST['UserType'])?clean($_REQUEST['UserType']):'Driver';
		$distance = isset($_REQUEST["distance"]) ? $_REQUEST["distance"] : '';
		$time = isset($_REQUEST["time"]) ? $_REQUEST["time"] : '';
		$StartLatitude    = isset($_REQUEST["StartLatitude"]) ? $_REQUEST["StartLatitude"] : '0.0';
		$EndLongitude    =isset($_REQUEST["EndLongitude"]) ? $_REQUEST["EndLongitude"] : '0.0';
    $DestLatitude    = isset($_REQUEST["DestLatitude"]) ? $_REQUEST["DestLatitude"] : '';
		$DestLongitude    =isset($_REQUEST["DestLongitude"]) ? $_REQUEST["DestLongitude"] : '';
		$PickUpAddress = isset($_REQUEST["PickUpAddress"]) ? $_REQUEST["PickUpAddress"] : '';
		$time = round(($time / 60),2);
		$distance = round(($distance / 1000),2);
		$VehicleTypeIds = isset($_REQUEST["VehicleTypeIds"]) ? $_REQUEST["VehicleTypeIds"] : '';
    $isDestinationAdded = "No";
    if($DestLatitude != "" && $DestLongitude != ""){
       $isDestinationAdded = "Yes";
    }
		$vLang = get_value('register_driver', 'vLang', 'iDriverId', $driverId,'','true');
		if($vLang == "" || $vLang == NULL){
			$vLang = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		$iDriverVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $driverId,'','true');
		if($iDriverVehicleId > 0){
			$Fare_Data = array();
			
			$vCarType = get_value('driver_vehicle', 'vCarType', 'iDriverVehicleId', $iDriverVehicleId,'','true');  
			$DriverVehicle_Arr = explode(",",$vCarType); 
			//echo "<pre>";print_r($DriverVehicle_Arr);echo "<br />";
			//$sql11 = "SELECT vVehicleType_".$vLang." as vVehicleTypeName, iVehicleTypeId, vLogo, iPersonSize FROM `vehicle_type`  WHERE  iVehicleTypeId IN (".$vCarType.") AND eType='Ride'";
			if($VehicleTypeIds != ""){
				$sql11 = "SELECT  vVehicleType_".$vLang." as vVehicleTypeName,iVehicleTypeId, vLogo,vLogo1, iPersonSize FROM vehicle_type WHERE iVehicleTypeId IN (".$VehicleTypeIds.") AND eType='Ride'";
				}else{
				$pickuplocationarr = array($StartLatitude,$EndLongitude);
				$GetVehicleIdfromGeoLocation = GetVehicleTypeFromGeoLocation($pickuplocationarr);
				$sql_vehicle = "SELECT iVehicleTypeId FROM vehicle_type WHERE iLocationid IN (".$GetVehicleIdfromGeoLocation.") AND eType='Ride'";
				$db_vehicle_location = $obj->MySQLSelect($sql_vehicle);
				$array_vehiclie_id = array();
				for($i=0; $i< count($db_vehicle_location); $i++){	
					array_push($array_vehiclie_id,$db_vehicle_location[$i]['iVehicleTypeId']);
				}
				//echo "<pre>";print_r($array_vehiclie_id);echo "<br />";
				$Vehicle_array_diff = array_values(array_intersect($DriverVehicle_Arr,$array_vehiclie_id));
				$VehicleTypeIds_Str = implode(",",$Vehicle_array_diff);
				if($VehicleTypeIds_Str == ""){
					$VehicleTypeIds_Str = "0";
				} 
				$sql11 = "SELECT  vVehicleType_".$vLang." as vVehicleTypeName,iVehicleTypeId, vLogo,vLogo1, iPersonSize FROM vehicle_type WHERE iVehicleTypeId IN (".$VehicleTypeIds_Str.") AND eType='Ride'";
			}
			
			$vCarType_Arr = $obj->MySQLSelect($sql11);
			$Fare_Data = array();
			if(count($vCarType_Arr)>0){
				for($i=0;$i<count($vCarType_Arr);$i++){    
          ######### Checking For Flattrip #########
          if($isDestinationAdded == "Yes"){    
            $sourceLocationArr = array($StartLatitude,$EndLongitude);       
        		$destinationLocationArr = array($DestLatitude,$DestLongitude);      
            $eFlatTrip = "No"; 
            $fFlatTripPrice = 0;
          }else{
            $eFlatTrip = "No"; 
            $fFlatTripPrice = 0;
          }  
          $Fare_Data[$i]['eFlatTrip'] = $eFlatTrip;
					$Fare_Data[$i]['fFlatTripPrice'] = $fFlatTripPrice;
          ######### Checking For Flattrip #########
          $Fare_Single_Vehicle_Data=calculateFareEstimateAll($time,$distance,$vCarType_Arr[$i]['iVehicleTypeId'],$driverId,1,"","","",1,0,0,0,"DisplySingleVehicleFare","Driver",1,"",$isDestinationAdded,$eFlatTrip,$fFlatTripPrice);
					$Fare_Data[$i]['iVehicleTypeId'] = $vCarType_Arr[$i]['iVehicleTypeId'];
					$Fare_Data[$i]['vVehicleTypeName'] = $vCarType_Arr[$i]['vVehicleTypeName'];
					//$Fare_Data[$i]['vLogo'] = $vCarType_Arr[$i]['vLogo'];
					$Photo_Gallery_folder = $tconfig["tsite_upload_images_vehicle_type_path"].'/'.$vCarType_Arr[$i]['iVehicleTypeId'].'/android/'.$vCarType_Arr[$i]['vLogo'];
					if($vCarType_Arr[$i]['vLogo'] != "" && file_exists($Photo_Gallery_folder)){
						$Fare_Data[$i]['vLogo'] = $vCarType_Arr[$i]['vLogo']; 
						}else{
						$Fare_Data[$i]['vLogo'] = "";
					}
          $Photo_Gallery_folder_vLogo1 = $tconfig["tsite_upload_images_vehicle_type_path"].'/'.$vCarType_Arr[$i]['iVehicleTypeId'].'/android/'.$vCarType_Arr[$i]['vLogo1'];
					if($vCarType_Arr[$i]['vLogo1'] != "" && file_exists($Photo_Gallery_folder_vLogo1)){
						$Fare_Data[$i]['vLogo1'] = $vCarType_Arr[$i]['vLogo1']; 
					}else{
						$Fare_Data[$i]['vLogo1'] = "";
					}
					$Fare_Data[$i]['iPersonSize'] = $vCarType_Arr[$i]['iPersonSize'];
					$lastvalue = end($Fare_Single_Vehicle_Data);
					$lastvalue1 = array_shift($lastvalue);
					$Fare_Data[$i]['SubTotal'] = $lastvalue1;                      
					$Fare_Data[$i]['VehicleFareDetail'] = $Fare_Single_Vehicle_Data;  
					//array_push($Fare_Data, $Fare_Single_Vehicle_Data);   
					
				}    
			}   
			$returnArr['Action'] = "1"; 
			$returnArr['message'] = $Fare_Data; 
      //$returnArr['eFlatTrip'] = $eFlatTrip;
			}else{
			$returnArr['Action'] = "0"; 
			$returnArr['message'] = "LBL_NO_VEHICLE_SELECTED";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);exit;
	}
	/* For Driver Vehicle Details */
	###########################################################################
	
	if($type == "updateuserPref"){
		$iMemberId = isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
		$userType = isset($_REQUEST['UserType'])?clean($_REQUEST['UserType']):'Driver';
		$eFemaleOnly = isset($_REQUEST['eFemaleOnly'])?clean($_REQUEST['eFemaleOnly']):'No';
		
		$where = " iDriverId = '$iMemberId'";
		$Data_update_User['eFemaleOnlyReqAccept']=$eFemaleOnly;
		
		$id = $obj->MySQLQueryPerform("register_driver",$Data_update_User,'update',$where);
		
		if ($id >0) {
			$returnArr['Action']="1";
			$returnArr['message'] = getDriverDetailInfo($iMemberId);
			} else {
			$returnArr['Action']="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		echo json_encode($returnArr);
	}
  	###########################################################################
	###########################################################################
	
	if($type == "updateUserGender"){
		$iMemberId = isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
		$userType = isset($_REQUEST['UserType'])?clean($_REQUEST['UserType']):'Driver';
		$eGender = isset($_REQUEST['eGender'])?clean($_REQUEST['eGender']):'';
		
		if($userType == "Driver"){
			$where = " iDriverId = '$iMemberId'";
			$Data_update_User['eGender']=$eGender;
			
			$id = $obj->MySQLQueryPerform("register_driver",$Data_update_User,'update',$where);
			}else{
			$where = " iUserId = '$iMemberId'";
			$Data_update_User['eGender']=$eGender;
			
			$id = $obj->MySQLQueryPerform("register_user",$Data_update_User,'update',$where);
		}
		
		
		if ($id >0) {
			$returnArr['Action']="1";
			if($userType != "Driver"){
				$returnArr['message'] = getPassengerDetailInfo($iMemberId,"");
				}else{
				$returnArr['message'] = getDriverDetailInfo($iMemberId);
			}
			} else {
			$returnArr['Action']="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		
		echo json_encode($returnArr);
	}
  	###########################################################################
    
	
	/* For Sending Trip Message and Notification  */
	if($type=="SendTripMessageNotification"){
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$iFromMemberId = isset($_REQUEST["iFromMemberId"]) ? $_REQUEST["iFromMemberId"] : '';
		$iToMemberId = isset($_REQUEST['iToMemberId'])?clean($_REQUEST['iToMemberId']):'';
		$iTripId = isset($_REQUEST['iTripId'])?clean($_REQUEST['iTripId']):'0';
		$tMessage = isset($_REQUEST['tMessage'])?stripslashes($_REQUEST['tMessage']):'';
		
		$Data['iTripId'] = $iTripId;
		$Data['iFromMemberId'] = $iFromMemberId;
		$Data['iToMemberId'] = $iToMemberId;
		$Data['tMessage'] = $tMessage;
		$Data['dAddedDate'] = @date("Y-m-d H:i:s");
		$Data['eStatus'] = "Unread";
		$Data['eUserType'] = $UserType;
		$id = $obj->MySQLQueryPerform('trip_messages',$Data,'insert');
		if($id > 0){
			$returnArr['Action'] ="1";
			// $message = sendTripMessagePushNotification($iFromMemberId,$UserType,$iToMemberId,$iTripId,$tMessage);
			// if($message == 1){
			// $returnArr['Action'] ="1";
			// }else{
			// $returnArr['Action'] ="0";
			// $returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
			// }
			sendTripMessagePushNotification($iFromMemberId,$UserType,$iToMemberId,$iTripId,$tMessage);
			$obj->MySQLClose();
			echo json_encode($returnArr);
			exit; 
			}else{
			$returnArr['Action'] ="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
			$obj->MySQLClose();
			echo json_encode($returnArr);
			exit;
		}
	}
	/* For Sending Trip Message and Notification  */
	###########################################################################
	###########################################################################
	/* For Update values of Language Labels */
	if($type=="UpdateLanguageLabelsValue"){
		//echo "Try Later";exit;
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$vLangLabel = isset($_REQUEST['vLangLabel'])?$_REQUEST['vLangLabel']:'';
		$vLangLabel = urldecode(stripslashes($vLangLabel));
		//$vLangLabel = '{"LBL_NO_REFERRAL_CODES":"No Referral Code Found"}';
		$vCode = isset($_REQUEST['vCode'])?clean($_REQUEST['vCode']):'';
		$vLangLabelArr = json_decode($vLangLabel,TRUE);   //echo "<pre>";print_r($vLangLabelArr);exit;
		if(count($vLangLabelArr) > 0){  
			foreach($vLangLabelArr as $key => $val){
				$vLabel = $key;
				$vValue = $val;
				$sql = "SELECT LanguageLabelId FROM `language_label` where vLabel = '".$vLabel."' AND vCode = '".$vCode."'";
				$db_language_label = $obj->MySQLSelect($sql);
				$count = count($db_language_label);
				if($count > 0){
					$where = " LanguageLabelId = '".$db_language_label[0]['LanguageLabelId']."'";
					$data_label_update['vValue']=$vValue;
					$obj->MySQLQueryPerform("language_label",$data_label_update,'update',$where);
					//UpdateOtherLanguage($vLabel,$vValue,$vCode,'language_label');
					}else{
					$sql = "SELECT LanguageLabelId FROM `language_label_other` where vLabel = '".$vLabel."' AND vCode = '".$vCode."'";
					$db_language_label_other = $obj->MySQLSelect($sql);
					$countOther = count($db_language_label_other);
					if($countOther > 0){
						$where = " LanguageLabelId = '".$db_language_label_other[0]['LanguageLabelId']."'";
						$data_label_update_other['vValue']=$vValue;
						$obj->MySQLQueryPerform("language_label_other",$data_label_update_other,'update',$where);
						//UpdateOtherLanguage($vLabel,$vValue,$vCode,'language_label_other');
					}
				}
			} 
			$returnArr['Action'] = "1";
			$returnArr['UpdatedLanguageLabels'] = getLanguageLabelsArr($vCode,"1");
			$returnArr['message'] ="LBL_UPDATE_MSG_TXT";
			echo json_encode($returnArr);
			exit;
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
			echo json_encode($returnArr);
			exit;
		}  
	}
	/* For Update values of Language Labels */
	#############################################################################
	#############################################################################
	#############################################################################
    if($type == "pushNotification"){
		
		//echo $pass= $generalobj->decrypt("XcIZDZwoXA==");exit;
        $deviceToken = $_REQUEST['Token'];
        //5240381e085cf439d5bda4f322440fc0b9cd750315b91c725cfdc12996545eb1
		
        // Put your private key's passphrase here:
        $passphrase = '123456';
		
        // Put your alert message here:
        $message['key'] = 'push notification!';
		
        $message_json = json_encode($message);
        ////////////////////////////////////////////////////////////////////////////////
		
        $ctx = stream_context_create();
		//        stream_context_set_option($ctx, 'ssl', 'local_cert', 'apn-dev-uberapp.pem');'driver_apns_dev.pem'
        stream_context_set_option($ctx, 'ssl', 'local_cert', $_REQUEST['pemName']);
        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
		
        // Open a connection to the APNS server
        $fp = stream_socket_client(
		'ssl://gateway.push.apple.com:2195', $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
		echo "<BR/>fp:".$fp."<BR/>";
		
        if (!$fp)
		exit("Failed to connect: $err $errstr" . PHP_EOL);
		
        echo 'Connected to APNS' . PHP_EOL;
        // $msg = "{\"iDriverId\":\"20\"}";
        // Create the payload body
        $body['aps'] = array(
		'alert' => $_REQUEST['message'],
		'content-available' => 1,
		'body'  => $_REQUEST['message'],
		'sound' => 'default'
		
		);
		
        // Encode the payload as JSON
        $payload = json_encode($body);
		
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
		
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
		
        if (!$result)
		echo 'Message not delivered' . PHP_EOL;
        else
		echo 'Message successfully delivered' . PHP_EOL;
		
        // Close the connection to the server
        fclose($fp);
	}
	
	###########################################################################
	###########################################################################
	
    if($type == "pushNotificationGCM"){
		
		$deviceToken = $_REQUEST['Token'];
        $registation_ids_new = array();
		
		array_push($registation_ids_new, $deviceToken);
		
		$Rmessage = array("message" => $_REQUEST['message']);
		
		$result = send_notification($registation_ids_new, $Rmessage,0);
        echo "<pre>";print_r($result);exit;
		
	}
	
	if($type == "checkTripstatus"){
		$iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '0';
		$iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
		$userType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
		$vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
		$isSubsToCabReq = isset($_REQUEST["isSubsToCabReq"]) ? $_REQUEST["isSubsToCabReq"] : '';
		//$APP_TYPE = $generalobj->getConfigurations("configurations", "APP_TYPE");
		$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		
		if($iMemberId != ""){
			if (!empty($isSubsToCabReq) && $isSubsToCabReq == 'true') {
				$driver_update['tLastOnline'] = date('Y-m-d H:i:s');
				$driver_update['tOnline'] = date('Y-m-d H:i:s');
			}
			
			if (!empty($vLatitude) && !empty($vLongitude)) {
				$driver_update['vLatitude'] = $vLatitude;
				$driver_update['vLongitude'] = $vLongitude;
				$user_update['vLatitude'] = $vLatitude;
				$user_update['vLongitude'] = $vLongitude;
			}
			
			if ($isSubsToCabReq == 'true' || !empty($vLatitude) || !empty($vLongitude)) {
				if($userType == "Driver"){
					$where = " iDriverId = '".$iMemberId."'";
					$Update_driver = $obj->MySQLQueryPerform("register_driver", $driver_update, "update", $where);
					}else {
					$where = " iUserId = '".$iMemberId."'";
					$Update_driver = $obj->MySQLQueryPerform("register_user", $user_update, "update", $where);
				}
			}
		}
		# Update User Location Date #
		Updateuserlocationdatetime($iMemberId,$userType,$vTimeZone);    
		# Update User Location Date #
		
		if($userType == "Passenger"){
			$condfield = 'iUserId';
			if($iTripId != ""){
				$sql = "SELECT t.*, CONCAT(rd.vName,' ',rd.vLastName) AS driverName, rd.vTripStatus, rd.iDriverId, rd.iAppVersion FROM trips AS t LEFT JOIN register_driver AS rd ON rd.iDriverId=t.iDriverId WHERE t.iTripId='".$iTripId."'";
				$msg = $obj->MySQLSelect($sql);
				
				if(!empty($msg)){
					if($msg[0]['iActive'] == 'Active'){
						$DriverMessage= "CabRequestAccepted";
						
						$message_arr = array();
						$message_arr['iDriverId'] = $msg[0]['iDriverId'];
						$message_arr['Message'] = $DriverMessage;
						$message_arr['iTripId'] = strval($msg[0]['iTripId']);
						$message_arr['DriverAppVersion'] = strval($msg[0]['iAppVersion']);
						$message_arr['iTripVerificationCode'] = $msg[0]['iVerificationCode'];
						
						$returnArr['Action'] = "1";
						$returnArr['message'] = $message_arr;
						}else if($msg[0]['iActive'] == 'Canceled' && $msg[0]['eCancelledBy'] == 'Driver'){
						$message  = "TripCancelledByDriver";
						$message_arr = array();
						$message_arr['Message'] = $message;
						$message_arr['Reason'] = $msg[0]['vCancelReason'];
						$message_arr['isTripStarted'] = "false";
						$message_arr['iUserId'] = $msg[0]['iUserId'];
						$message_arr['driverName'] = $msg[0]['driverName'];
						$message_arr['vRideNo'] = $msg[0]['vRideNo'];
						
						$returnArr['Action'] = "1";
						$returnArr['message'] = $message_arr;
						}else if($msg[0]['vTripStatus'] == 'Arrived'){
						$message_arr = array();
						$message_arr['Message'] = "DriverArrived";
						$message_arr['MsgType'] = "DriverArrived";
						$message_arr['iDriverId'] = $msg[0]['iDriverId'];
						$message_arr['driverName'] = $msg[0]['driverName'];
						$message_arr['vRideNo'] = $msg[0]['vRideNo'];
						
						$returnArr['Action'] = "1";
						$returnArr['message'] = $message_arr;
						
						}else if($msg[0]['iActive'] == 'On Going Trip'){
						$message      = "TripStarted";
						$message_arr = array();
						$message_arr['Message'] = $message;
						$message_arr['iDriverId'] = $msg[0]['iDriverId'];
						$message_arr['driverName'] = $msg[0]['driverName'];
						$message_arr['vRideNo'] = $msg[0]['vRideNo'];
						if($msg[0]['eType'] == "Deliver"){
							$message_arr['VerificationCode'] = $msg[0]['vDeliveryConfirmCode'];
							}else{
							$message_arr['VerificationCode'] = "";
						}
						
						$returnArr['Action'] = "1";
						$returnArr['message'] = $message_arr;
						}else if($msg[0]['iActive'] == 'Finished'){
						$message_arr = array();
						if($msg[0]['eCancelled'] == "true"){
							$message  = "TripCancelledByDriver";
							$message_arr['Reason'] = $msg[0]['vCancelReason'];
							$message_arr['isTripStarted'] = "true";
							}else{
							$message      = "TripEnd";
						}
						$message_arr['Message'] = $message;
						$message_arr['iDriverId'] = $msg[0]['iDriverId'];
						$message_arr['driverName'] = $msg[0]['driverName'];
						$message_arr['vRideNo'] = $msg[0]['vRideNo'];
						
						$returnArr['Action'] = "1";
						$returnArr['message'] = $message_arr;
					}
					}else {
					$returnArr['Action'] = "0";
					$returnArr['message'] = "LBL_NO_TRIP_FOUND";
				}
				}else {
				$sql = "SELECT t.*, CONCAT(rd.vName,' ',rd.vLastName) AS driverName, rd.vTripStatus, rd.iDriverId, rd.iAppVersion FROM trips AS t LEFT JOIN register_driver AS rd ON rd.iDriverId=t.iDriverId WHERE t.iUserId='".$iMemberId."' ORDER BY t.iTripId DESC limit 1";
				$msg = $obj->MySQLSelect($sql);
				
				if(!empty($msg)){
					
					// Cab Accepted MEssage
					$DriverMessage= "CabRequestAccepted";
					
					$message_arr1 = array();
					$message_arr1['iDriverId'] = $msg[0]['iDriverId'];
					$message_arr1['Message'] = $DriverMessage;
					$message_arr1['iTripId'] = strval($msg[0]['iTripId']);
					$message_arr1['DriverAppVersion'] = strval($msg[0]['iAppVersion']);
					$message_arr1['iTripVerificationCode'] = $msg[0]['iVerificationCode'];
					$returnArr['message']['Accepted'] = $message_arr1;
					
					// Trip Cancelled Message
					$message  = "TripCancelledByDriver";
					$message_arr2 = array();
					$message_arr2['Message'] = $message;
					$message_arr2['Reason'] = $msg[0]['vCancelReason'];
					$message_arr2['isTripStarted'] = "false";
					$message_arr2['iUserId'] = $msg[0]['iUserId'];
					$message_arr2['driverName'] = $msg[0]['driverName'];
					$message_arr2['vRideNo'] = $msg[0]['vRideNo'];
					$returnArr['message']['Cancel'] = $message_arr2;
					
					// Driver Arrived Message
					$message_arr3 = array();
					$message_arr3['Message'] = "DriverArrived";
					$message_arr3['MsgType'] = "DriverArrived";
					$message_arr3['iDriverId'] = $msg[0]['iDriverId'];
					$message_arr3['driverName'] = $msg[0]['driverName'];
					$message_arr3['vRideNo'] = $msg[0]['vRideNo'];
					$returnArr['message']['Arrived'] = $message_arr3;
					
					// Trip Started Message
					$message      = "TripStarted";
					$message_arr4 = array();
					$message_arr4['Message'] = $message;
					$message_arr4['iDriverId'] = $msg[0]['iDriverId'];
					$message_arr4['driverName'] = $msg[0]['driverName'];
					$message_arr4['vRideNo'] = $msg[0]['vRideNo'];
					if($msg[0]['eType'] == "Deliver"){
						$message_arr4['VerificationCode'] = $msg[0]['vDeliveryConfirmCode'];
						}else{
						$message_arr4['VerificationCode'] = "";
					}
					$returnArr['message']['Started'] = $message_arr4;
					
					// Trip Finished Message
					$message_arr = array();
					if($msg[0]['eCancelled'] == "true"){
						$message  = "TripCancelledByDriver";
						$message_arr5['Reason'] = $msg[0]['vCancelReason'];
						$message_arr5['isTripStarted'] = "true";
						}else{
						$message      = "TripEnd";
					}
					$message_arr5['Message'] = $message;
					$message_arr5['iDriverId'] = $msg[0]['iDriverId'];
					$message_arr5['driverName'] = $msg[0]['driverName'];
					$message_arr5['vRideNo'] = $msg[0]['vRideNo'];
					$returnArr['message']['TripEnd'] = $message_arr5;
					
					$returnArr['Action'] = "1";
					}else {
					$returnArr['Action'] = "0";
					$returnArr['message'] = "LBL_NO_TRIP_FOUND";
				}
			}
			}else{
			if($iTripId != ""){
				$sql = "SELECT t.iTripId, t.iUserId, t.vRideNo, CONCAT(rd.vName,' ',rd.vLastName) AS driverName FROM trips AS t LEFT JOIN register_driver AS rd ON rd.iDriverId=t.iDriverId WHERE t.iTripId='".$iTripId."' AND t.iActive='Canceled' AND t.eCancelledBy='Passenger'";
				$msg = $obj->MySQLSelect($sql);
				
				if(!empty($msg)){
					$message  = "TripCancelled";
					$message_arr = array();
					$message_arr['Message'] = $message;
					$message_arr['iUserId'] = $msg[0]['iUserId'];
					$message_arr['driverName'] = $msg[0]['driverName'];
					$message_arr['vRideNo'] = $msg[0]['vRideNo'];
					
					$returnArr['Action'] = "1";
					$returnArr['message'] = $message_arr;
					}else {
					$returnArr['Action'] = "0";
					$returnArr['message'] = "LBL_NO_TRIP_FOUND";
				}
				}else {
				$sql = "SELECT tMessage as msg FROM passenger_requests WHERE iDriverId='".$iMemberId."' ORDER BY iRequestId DESC LIMIT 1 ";
				$msg = $obj->MySQLSelect($sql);
				
				
				if(!empty($msg)){
					$returnArr['Action'] = "1";
					$returnArr['message'] = $msg;
					}else {
					$returnArr['Action'] = "0";
					$returnArr['message'] = "LBL_NO_TRIP_FOUND";
				}
			}
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	
	if($type=="configDriverTripStatus"){
		$iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
		$userType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
		$vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
		$iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '0';
		$isSubsToCabReq = isset($_REQUEST["isSubsToCabReq"]) ? $_REQUEST["isSubsToCabReq"] : '';
		$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		
		if($iMemberId != ""){
			if (!empty($isSubsToCabReq) && $isSubsToCabReq == 'true') {
				$driver_update['tLastOnline'] = date('Y-m-d H:i:s');
				$driver_update['tOnline'] = date('Y-m-d H:i:s');
			}
			
			if (!empty($vLatitude) && !empty($vLongitude)) {
				$driver_update['vLatitude'] = $vLatitude;
				$driver_update['vLongitude'] = $vLongitude;
			}
			
			if(count($driver_update) > 0){
				$where = " iDriverId = '".$iMemberId."'";
				$Update_driver = $obj->MySQLQueryPerform("register_driver", $driver_update, "update", $where);
				# Update User Location Date #
				Updateuserlocationdatetime($iMemberId,"Driver",$vTimeZone);    
				# Update User Location Date #
			}
			
		}
		if($iTripId != ""){
			$sql = "SELECT tMessage as msg, iStatusId FROM trip_status_messages WHERE iDriverId='".$iMemberId."' AND eToUserType='Driver' AND eReceived='No' ORDER BY iStatusId DESC LIMIT 1 ";
			$msg = $obj->MySQLSelect($sql);
			}else{
			$date = @date("Y-m-d");
			$sql = "SELECT passenger_requests.tMessage as msg  FROM passenger_requests LEFT JOIN driver_request ON  driver_request.iRequestId=passenger_requests.iRequestId  LEFT JOIN register_driver ON register_driver.iDriverId=passenger_requests.iDriverId where date_format(passenger_requests.dAddedDate,'%Y-%m-%d')= '" . $date . "' AND  passenger_requests.iDriverId=" . $iMemberId . " AND driver_request.eStatus='Timeout' AND driver_request.iDriverId='".$iMemberId."' AND register_driver.vTripStatus IN ('Not Active','NONE','Cancelled') ORDER BY passenger_requests.iRequestId DESC LIMIT 1 "; 
			$msg = $obj->MySQLSelect($sql);
		}
		
		
		$returnArr['Action'] = "0";
		if(!empty($msg)){
			
			$returnArr['Action'] = "1";
			
			if($iTripId != ""){
				//$updateQuery = "UPDATE trip_status_messages SET eReceived = 'Yes' WHERE iStatusId='".$msg[0]['iStatusId']."'";
				$updateQuery = "UPDATE trip_status_messages SET eReceived = 'Yes' WHERE iDriverId='".$iMemberId."'";
				$obj->sql_query($updateQuery);
				
				$returnArr['Action'] = "1";
				$returnArr['message'] = $msg[0]['msg'];
				}else{
				
				$driver_request['eStatus'] = "Received";				
				$where = " iDriverId =" . $iMemberId . " and date_format(tDate,'%Y-%m-%d') = '" . $date . "' AND eStatus = 'Timeout' ";
				$obj->MySQLQueryPerform("driver_request", $driver_request, "update", $where);
				
				
				// $updatequery = "update driver_request set eStatus='Received' where iDriverId='".$iMemberId."' AND   date_format(tDate,'%Y-%m-%d') = '" . $date . "'  AND eStatus = 'Timeout'";
				// $obj->sql_query($updateQuery);
				
				
				$returnArr['Action'] = "1";
				$dataArr = array();
				for($i=0;$i<count($msg); $i++){
					$dataArr[$i] = $msg[$i]['msg'];
				}
				$returnArr['message'] = $dataArr;
			}
			
		}
		$obj->MySQLClose();
		echo json_encode($returnArr, JSON_UNESCAPED_UNICODE);
		exit;
	}
	
	if($type=="configPassengerTripStatus"){
		$iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
		$userType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
		$vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
		$iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '0';
		$CurrentDriverIds = isset($_REQUEST["CurrentDriverIds"]) ? explode(',',$_REQUEST["CurrentDriverIds"]) : '';
		$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		
		if($CurrentDriverIds == "" && $iTripId != ""){
			$sql = "SELECT iDriverId FROM trips WHERE iTripId='".$iTripId."'";
			$data_requst = $obj->MySQLSelect($sql);   
			$iDriverId = $data_requst[0]['iDriverId']; 
			$CurrentDriverIds = (array)$iDriverId; 
		}
		
		if($iMemberId != ""){
			
			if (!empty($vLatitude) && !empty($vLongitude)) {
				$user_update['vLatitude'] = $vLatitude;
				$user_update['vLongitude'] = $vLongitude;
				$where = " iUserId = '".$iMemberId."'";
				$Update_driver = $obj->MySQLQueryPerform("register_user", $user_update, "update", $where);
				# Update User Location Date #
				Updateuserlocationdatetime($iMemberId,"Passenger",$vTimeZone);    
				# Update User Location Date #
			}
		}
		
		$currDriver = array();
		if(!empty($CurrentDriverIds)){
			$k=0;
			foreach($CurrentDriverIds as $cDriv){
				$driverDetails = array();
				$driverDetails=get_value('register_driver', 'iDriverId,vLatitude,vLongitude', 'iDriverId', $cDriv);
				$currDriver[$k]['iDriverId'] = $driverDetails[0]['iDriverId'];
				$currDriver[$k]['vLatitude'] = $driverDetails[0]['vLatitude'];
				$currDriver[$k]['vLongitude'] = $driverDetails[0]['vLongitude'];
				$k++;
			}
		}
		
		$sql = "SELECT tMessage as msg, iStatusId FROM trip_status_messages WHERE iUserId='".$iMemberId."' AND eToUserType='Passenger' AND eReceived='No' ORDER BY iStatusId DESC LIMIT 1 ";
		$msg = $obj->MySQLSelect($sql);
		
		$returnArr['Action'] = "0";
		if(!empty($msg)){
			//$updateQuery = "UPDATE trip_status_messages SET eReceived ='Yes' WHERE iStatusId='".$msg[0]['iStatusId']."'";
			$updateQuery = "UPDATE trip_status_messages SET eReceived ='Yes' WHERE iUserId='".$iMemberId."'";
			$obj->sql_query($updateQuery);
			
			$returnArr['Action'] = "1";
			$returnArr['message'] = $msg[0]['msg'];
		}
		
		$returnArr['currentDrivers'] = $currDriver;
		$obj->MySQLClose();
		echo json_encode($returnArr, JSON_UNESCAPED_UNICODE);
		exit;
	}
	
	if($type == "callOnLogout"){
		global $generalobj,$obj;
		
		$iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
		$userType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		
		$Data_logout = array();
		
		if($userType == "Passenger"){
			$Data_logout['eLogout'] = 'Yes';
			$tableName = "register_user";
			$where = " iUserId='".$iMemberId."'";
      $id = $obj->MySQLQueryPerform($tableName,$Data_logout,'update', $where);
			}else{
			$Data_logout['vAvailability'] = 'Not Available';
			$Data_logout['eLogout'] = 'Yes';
			$tableName = "register_driver";
			$where = " iDriverId='".$iMemberId."'";
      $id = $obj->MySQLQueryPerform($tableName,$Data_logout,'update', $where);
      $curr_date= date('Y-m-d H:i:s');
      $selct_query = "select * from driver_log_report WHERE iDriverId = '".$iMemberId."' AND dLogoutDateTime = '0000-00-00 00:00:00' order by `iDriverLogId` desc limit 0,1";
			$get_data_log = $obj->sql_query($selct_query);
			if(count($get_data_log) > 0) {
        $update_sql = "UPDATE driver_log_report set dLogoutDateTime = '".$curr_date."' WHERE iDriverLogId ='".$get_data_log[0]['iDriverLogId']."'";
  			$result = $obj->sql_query($update_sql);
      }  
		}
		
		
		if($id){
			$returnArr['Action']="1";
			}else{
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	
	if($type == "getCabRequestAddress"){
		global $generalobj,$obj;
		
		$iCabRequestId = isset($_REQUEST["iCabRequestId"]) ? $_REQUEST["iCabRequestId"] : '';
    	$iDriverId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : '';
		$fields="iVehicleTypeId,eType,tSourceAddress,tDestAddress,tUserComment";
		
		$Data_cab_request = get_value('cab_request_now', $fields, 'iCabRequestId', $iCabRequestId,'','');
    $eType = $Data_cab_request[0]['eType']; 
    if($eType == "UberX"){
      $vLang=get_value('register_driver', 'vLang', 'iDriverId', $iDriverId,'','true');
      if($vLang == "" || $vLang == NULL){
  				$vLang = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
  		}
		
      $iVehicleTypeId = $Data_cab_request[0]['iVehicleTypeId'];
      $sqlv = "SELECT iVehicleCategoryId,vVehicleType_".$vLang." as vVehicleTypeName from vehicle_type WHERE iVehicleTypeId = '".$iVehicleTypeId."'";
  	  $tripVehicleData = $obj->MySQLSelect($sqlv);
      $iVehicleCategoryId = $tripVehicleData[0]['iVehicleCategoryId'];
      $vVehicleTypeName = $tripVehicleData[0]['vVehicleTypeName'];
      if($iVehicleCategoryId != 0){
  			$vVehicleCategoryName = get_value('vehicle_category', 'vCategory_'.$vLang, 'iVehicleCategoryId',$iVehicleCategoryId,'','true');
  			$vVehicleTypeName = $vVehicleCategoryName . "-" . $vVehicleTypeName;
  		}
      $Data_cab_request[0]['SelectedTypeName']	= $vVehicleTypeName;
    }else{
      $Data_cab_request[0]['SelectedTypeName']	= "";
    }  
		if(!empty($Data_cab_request)){
			$returnArr['Action']="1";
			$returnArr['message']=$Data_cab_request[0];
			}else{
			$returnArr['Action']="0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	
	###########################################################################
	########################Get Driver Bank Details############################  
	
	if($type == "DriverBankDetails"){
		global $generalobj,$obj;
		
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$userType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Driver';
		$eDisplay = isset($_REQUEST["eDisplay"]) ? $_REQUEST["eDisplay"] : 'Yes';
		$vPaymentEmail = isset($_REQUEST["vPaymentEmail"]) ? $_REQUEST["vPaymentEmail"] : '';
		$vBankAccountHolderName = isset($_REQUEST["vBankAccountHolderName"]) ? $_REQUEST["vBankAccountHolderName"] : '';
		$vAccountNumber = isset($_REQUEST["vAccountNumber"]) ? $_REQUEST["vAccountNumber"] : '';
		$vBankLocation = isset($_REQUEST["vBankLocation"]) ? $_REQUEST["vBankLocation"] : '';
		$vBankName = isset($_REQUEST["vBankName"]) ? $_REQUEST["vBankName"] : '';
		$vBIC_SWIFT_Code = isset($_REQUEST["vBIC_SWIFT_Code"]) ? $_REQUEST["vBIC_SWIFT_Code"] : ''; 
		
		if($eDisplay == "" || $eDisplay == NULL){
			$eDisplay = "Yes";
		}
		$returnArr = array();
		if($eDisplay == "Yes"){
			$Driver_Bank_Arr=  get_value('register_driver', 'vPaymentEmail, vBankAccountHolderName, vAccountNumber, vBankLocation, vBankName, vBIC_SWIFT_Code', 'iDriverId', $iDriverId);
			$returnArr['Action']="1";
			$returnArr['message']=$Driver_Bank_Arr[0];
			echo json_encode($returnArr);exit;
			}else{
			$Data_Update['vPaymentEmail'] = $vPaymentEmail;
			$Data_Update['vBankAccountHolderName'] = $vBankAccountHolderName;
			$Data_Update['vAccountNumber'] = $vAccountNumber;
			$Data_Update['vBankLocation'] = $vBankLocation;
			$Data_Update['vBankName'] = $vBankName;
			$Data_Update['vBIC_SWIFT_Code'] = $vBIC_SWIFT_Code;
			
			$where = " iDriverId = '".$iDriverId."'";
    		$obj->MySQLQueryPerform("register_driver",$Data_Update,'update',$where);
			
			$returnArr['Action']="1";
			$returnArr['message']=getDriverDetailInfo($iDriverId);
			echo json_encode($returnArr);exit;
		}
	}
	########################Get Driver Bank Details############################
	
	if($type == "getvehicleCategory"){ 
		$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
  		$page        = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
  		$iVehicleCategoryId        = isset($_REQUEST['iVehicleCategoryId']) ? trim($_REQUEST['iVehicleCategoryId']) : 0;
		
		$languageCode="";
		if($iDriverId !=""){
			$languageCode =  get_value('register_driver', 'vLang', 'iDriverId',$iDriverId,'','true');		
		}			
		
		if($languageCode == "" || $languageCode == NULL){
			$languageCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		
		$ssql_category = "";
		$returnName = "vTitle";
		if($iVehicleCategoryId != "" && ($iVehicleCategoryId == 0 || $iVehicleCategoryId == "0")){
			$ssql_category = " and (select count(iVehicleCategoryId) from vehicle_category where iParentId=vc.iVehicleCategoryId AND eStatus='Active') > 0";
			$returnName = "vCategory";
		}
		
		$per_page=10;
  		$sql_all  = "SELECT COUNT(iVehicleCategoryId) As TotalIds FROM vehicle_category as vc WHERE vc.eStatus='Active' AND vc.iParentId='".$iVehicleCategoryId."'".$ssql_category ;
  		$data_count_all = $obj->MySQLSelect($sql_all);
  		$TotalPages = ceil($data_count_all[0]['TotalIds'] / $per_page);
		
  		$start_limit = ($page - 1) * $per_page;
  		$limit       = " LIMIT " . $start_limit . ", " . $per_page;
		
		$sql = "SELECT vc.iVehicleCategoryId, vc.vCategory_".$languageCode." as '".$returnName."' FROM vehicle_category as vc WHERE vc.eStatus='Active' AND vc.iParentId='".$iVehicleCategoryId."'".$ssql_category . $limit;
		$vehicleCategoryDetail = $obj->MySQLSelect($sql);
		
		$vehicleCategoryData = array();
		
		if(count($vehicleCategoryDetail) >0){
			$vehicleCategoryData = $vehicleCategoryDetail;
			if($iVehicleCategoryId != "" && ($iVehicleCategoryId == 0 || $iVehicleCategoryId == "0")){
				$i=0;
				while (count($vehicleCategoryDetail)> $i) {
					
					$iVehicleCategoryId = $vehicleCategoryDetail[$i]['iVehicleCategoryId'];
					
					$sql = "SELECT vCategory_" .$languageCode. " as vTitle,iVehicleCategoryId FROM `vehicle_category` WHERE iParentId='".$iVehicleCategoryId."' AND eStatus='Active'";
					$subCategoryData = $obj->MySQLSelect($sql);
					
					$vehicleCategoryData[$i]['SubCategory'] = $subCategoryData;
					$i++;
				}
			}
			
			$returnArr['Action'] = "1";	
			if ($TotalPages > $page) {
				$returnArr['NextPage'] = "".($page + 1);
    			} else {
				$returnArr['NextPage'] = "0";
			}
			$returnArr['message'] = $vehicleCategoryData;			
			
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";		
			
		}
		echo json_encode($returnArr);
		
	}	
	
	###########################################################################
	###########################################################################     
	if($type == "getServiceTypes"){ 		 
		$iVehicleCategoryId = isset($_REQUEST['iVehicleCategoryId']) ? $_REQUEST['iVehicleCategoryId'] : '';
		$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';	
		$languageCode="";
		if($iDriverId !=""){
			$languageCode =  get_value('register_driver', 'vLang', 'iDriverId',$iDriverId,'','true');		
		}			
		if($languageCode == "" || $languageCode == NULL){
			$languageCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		
		$sql = "SELECT * FROM `register_driver` where iDriverId ='".$iDriverId."'";
		$db_driverdetail = $obj->MySQLSelect($sql);	
		$vCountry = $db_driverdetail[0]['vCountry'];
		$languageLabelsArr= getLanguageLabelsArr($languageCode,"1");
		$lbl_all = $languageLabelsArr['LBL_ALL'];
		$ssql = "";
		if($vCountry != ""){
			$iCountryId = get_value('country', 'iCountryId', 'vCountryCode', $vCountry, '', 'true');
			$sql = "SELECT * FROM location_master WHERE eStatus='Active' AND iCountryId = '".$iCountryId."' AND eFor = 'VehicleType'";
			$db_country = $obj->MySQLSelect($sql);
			$country_str = "-1";
			if(count($db_country) > 0){
				for($i=0;$i<count($db_country);$i++){
					$country_str .= ",".$db_country[$i]['iLocationId']; 
				}
			}
			$ssql.= " AND iLocationid IN ($country_str) ";
		} 
		$sql2 = "SELECT iVehicleTypeId, vVehicleType_".$languageCode." as vTitle,eFareType,eAllowQty,iMaxQty,fFixedFare,fPricePerHour,iLocationid from vehicle_type where iVehicleCategoryId in($iVehicleCategoryId)".$ssql ; 
		$vehicleDetail = $obj->MySQLSelect($sql2);
		$vCurrencyDriver=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $db_driverdetail[0]['iDriverId'],'','true');
		if($vCurrencyDriver == "" || $vCurrencyDriver == NULL){
			$vCurrencyDriver = get_value('currency', 'vName', 'eDefault', 'Yes','','true'); 
		}
		$vCurrencyData=get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyDriver);
		$vCurrencySymbol =  $vCurrencyData[0]['vSymbol'];
		$vCurrencyRatio =  $vCurrencyData[0]['Ratio'];
		$iParentId = get_value('vehicle_category', 'iParentId', 'iVehicleCategoryId', $iVehicleCategoryId,'','true');
		if($iParentId == 0){
			$ePriceType=get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iVehicleCategoryId,'','true');
			}else{ 
			$ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId,'','true'); 
		}
		$iDriverVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId',$iDriverId,'','true');
		$sql = "SELECT vCarType FROM `driver_vehicle` where iDriverId ='".$iDriverId."' AND iDriverVehicleId = '".$iDriverVehicleId."'";
		$db_vCarType = $obj->MySQLSelect($sql);					
		if(count($db_vCarType) > 0){
			$vehicle_service_id= explode(",", $db_vCarType[0]['vCarType']);
			for($i=0;$i<count($vehicleDetail); $i++){		
				$sql3 = "SELECT * FROM `service_pro_amount` where iDriverVehicleId ='".$db_driverdetail[0]['iDriverVehicleId']."' AND iVehicleTypeId='".$vehicleDetail[$i]['iVehicleTypeId']."'";
				$db_serviceproviderid = $obj->MySQLSelect($sql3); 
				if(count($db_serviceproviderid) > 0){
					$vehicleDetail[$i]['fAmount'] = $db_serviceproviderid[0]['fAmount'];
					}else{
  					if($vehicleDetail[$i]['eFareType'] == "Hourly"){
						$vehicleDetail[$i]['fAmount'] = $vehicleDetail[$i]['fPricePerHour'];
						}else{
						$vehicleDetail[$i]['fAmount'] = $vehicleDetail[$i]['fFixedFare'];
					}
				} 
  				// $vehicleDetail[$i]['iDriverVehicleId']=$db_driverdetail[0]['iDriverVehicleId'];	
				$fAmount = round($vehicleDetail[$i]['fAmount'] * $vCurrencyRatio,2);		
				$vehicleDetail[$i]['fAmount'] = $fAmount; 		
  				$vehicleDetail[$i]['ePriceType']=$ePriceType;					
  				$vehicleDetail[$i]['vCurrencySymbol']=$vCurrencySymbol;					
  				$data_service[$i]=$vehicleDetail[$i];					
  				if(in_array($data_service[$i]['iVehicleTypeId'],$vehicle_service_id)){
  					$vehicleDetail[$i]['VehicleServiceStatus']= 'true'; 							
					}else{
					$vehicleDetail[$i]['VehicleServiceStatus']= 'false'; 				
				}
				if($vehicleDetail[$i]['iLocationid'] == "-1"){
					$vehicleDetail[$i]['SubTitle']= $lbl_all;
					}else{
					$sql = "SELECT vLocationName FROM location_master WHERE iLocationId = '".$vehicleDetail[$i]['iLocationid']."'";
					$locationname = $obj->MySQLSelect($sql);
					$vehicleDetail[$i]['SubTitle']= $locationname[0]['vLocationName'];
				}
			}	
		} 	
		if(count($vehicleDetail) >0){		
			$returnArr['Action'] = "1";		
			$returnArr['message'] = $vehicleDetail;				
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message'] ="LBL_NO_DATA_AVAIL";		
		}
		echo json_encode($returnArr);
	}
	###########################################################################
	###########################################################################         
	if($type == "UpdateDriverServiceAmount"){
		$iVehicleTypeId = isset($_REQUEST['iVehicleTypeId']) ? $_REQUEST['iVehicleTypeId'] : '';
		$iDriverVehicleId = isset($_REQUEST['iDriverVehicleId']) ? $_REQUEST['iDriverVehicleId'] : '';
		$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Driver';
		$fAmount = isset($_REQUEST['fAmount']) ? $_REQUEST['fAmount'] : '';
		if($iDriverVehicleId == "" || $iDriverVehicleId == 0 || $iDriverVehicleId == NULL){
			$iDriverVehicleId=get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $iDriverId,'','true');
		}
		$vCurrencyDriver=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iDriverId,'','true');
		if($vCurrencyDriver == "" || $vCurrencyDriver == NULL){
			$vCurrencyDriver = get_value('currency', 'vName', 'eDefault', 'Yes','','true'); 
		}
		$vCurrencyData=get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyDriver);
		$vCurrencyRatio =  $vCurrencyData[0]['Ratio'];
		$Amount = $fAmount/$vCurrencyRatio;
		$Amount = round($Amount,2);
		$sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='".$iDriverVehicleId."' AND iVehicleTypeId='".$iVehicleTypeId."'";
		$serviceProData = $obj->MySQLSelect($sqlServicePro);
		if(count($serviceProData) > 0){
			$updateQuery = "UPDATE service_pro_amount set fAmount='".$Amount."' WHERE iDriverVehicleId='".$iDriverVehicleId."' AND iVehicleTypeId='".$iVehicleTypeId."'";
			$id = $obj->sql_query($updateQuery);
			}else{
			$Data["iDriverVehicleId"] = $iDriverVehicleId;
			$Data["iVehicleTypeId"] = $iVehicleTypeId;
			$Data["fAmount"] = $Amount;
			$id = $obj->MySQLQueryPerform("service_pro_amount",$Data,'insert');
		} 
     	if($id){		
			$returnArr['Action'] = "1";		
			$returnArr['message'] = "LBL_SERVICE_AMOUT_UPDATED"; 
			}else{
  			$returnArr['Action'] = "0";
  			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";		
		}
		$obj->MySQLClose();
  		echo json_encode($returnArr);exit;
	}
	###########################################################################
	###########################################################################         
	if($type == "UpdateBookingStatus"){
		$iCabBookingId= isset($_REQUEST["iCabBookingId"]) ? $_REQUEST["iCabBookingId"] : '';
		$eStatus= isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : '';
		$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
		$vCancelReason = isset($_REQUEST['vCancelReason']) ? $_REQUEST['vCancelReason'] : ''; 
    $eConfirmByProvider = isset($_REQUEST['eConfirmByProvider']) ? $_REQUEST['eConfirmByProvider'] : 'No';
    if($eConfirmByProvider == "" || $eConfirmByProvider == NULL){
      $eConfirmByProvider = "No";
    } 
    ############################################################### CheckPendingBooking UBERX  For same Time booking (Accept , Pending)###########################################################
    if($APP_TYPE == "UberX"){  
      $sql_book = "SELECT dBooking_date from cab_booking WHERE iCabBookingId ='".$iCabBookingId."'";
      $checkbooking = $obj->MySQLSelect($sql_book);
      $dBooking_date = $checkbooking[0]['dBooking_date'];
      $sql = "SELECT iCabBookingId from cab_booking WHERE iDriverId ='".$iDriverId."' AND dBooking_date = '".$dBooking_date."' AND eStatus = 'Accepted' AND iCabBookingId != '".$iCabBookingId."'";
  		$pendingacceptdriverbooking = $obj->MySQLSelect($sql); 
      if(count($pendingacceptdriverbooking) > 0){
           $returnArr['Action'] = "0";
      		 $returnArr['message'] ="LBL_PENDING_PLUS_ACCEPT_BOOKING_AVAIL_TXT";
           $returnArr['message1'] = "Accept";
           echo json_encode($returnArr);exit;
      }else{
         $sql = "SELECT iCabBookingId from cab_booking WHERE iDriverId ='".$iDriverId."' AND dBooking_date = '".$dBooking_date."' AND eStatus = 'Pending' AND iCabBookingId != '".$iCabBookingId."'";
  		   $pendingdriverbooking = $obj->MySQLSelect($sql);
         if(count($pendingdriverbooking) > 0 && $eConfirmByProvider == "No"){
           $returnArr['Action'] = "0";
      		 $returnArr['message'] ="LBL_PENDING_BOOKING_AVAIL_TXT";
           $returnArr['message1'] = "Pending";
           $returnArr['BookingFound'] = "Yes";
           echo json_encode($returnArr);exit;
         }
      }
    }  
    ############################################################### CheckPendingBooking UBERX ###########################################################    
    ### Checking For booking timing availablity when driver accept booking ###
    if($eConfirmByProvider == "No" && $eStatus == "Accepted" && $APP_TYPE == "UberX"){
      $sql = "SELECT dBooking_date from cab_booking WHERE iCabBookingId ='".$iCabBookingId."'";
		  $bookingdate = $obj->MySQLSelect($sql);
      $dBooking_date=$bookingdate[0]['dBooking_date']; 
      $additional_mins = $PROVIDER_BOOKING_ACCEPT_TIME_INTERVAL;
      $FromDate = date("Y-m-d H:i:s", strtotime($dBooking_date . "-".$additional_mins." minutes"));
      $ToDate = date("Y-m-d H:i:s", strtotime($dBooking_date . "+".$additional_mins." minutes"));
      $sql = "SELECT iCabBookingId from cab_booking WHERE (dBooking_date BETWEEN '".$FromDate."' AND '".$ToDate."') AND iCabBookingId != '".$iCabBookingId."' AND eStatus = 'Accepted' AND iDriverId = '".$iDriverId."'";
		  $checkbookingdate = $obj->MySQLSelect($sql);
      if(count($checkbookingdate) > 0){
         $returnArr['Action'] = "0";
         $returnArr['BookingFound'] = "Yes";
			   $returnArr['message'] ="LBL_PROVIDER_JOB_FOUND_TXT";
         echo json_encode($returnArr);exit;		
      }
    }
    ### Checking For booking timing availablity when driver accept booking ###
		$where = " iCabBookingId = '$iCabBookingId' "; 
		$Data['eStatus'] = $eStatus;     
		$Data['vCancelReason'] = $vCancelReason;     
		$Update_Booking_id = $obj->MySQLQueryPerform("cab_booking",$Data,'update',$where);
		if($Update_Booking_id){		
			
			$sql = "SELECT cb.*,concat(ru.vName,' ',ru.vLastName) as UserName,ru.vEmail,ru.vPhone,ru.vPhoneCode,ru.vLang as userlang,concat(rd.vName,' ',rd.vLastName) as DriverName from cab_booking as cb LEFT JOIN register_user as ru ON ru.iUserId=cb.iUserId LEFT JOIN register_driver as rd ON rd.iDriverId=cb.iDriverId WHERE cb.iCabBookingId ='".$iCabBookingId."'";
			$bookingdetail = $obj->MySQLSelect($sql);
			$UserPhoneNo = $bookingdetail[0]['vPhone'];
			$UserPhoneCode = $bookingdetail[0]['vPhoneCode']; 
			$UserLang = $bookingdetail[0]['userlang'];
			$Data1['vRider']=$bookingdetail[0]['UserName'];
			$Data1['vDriver']=$bookingdetail[0]['DriverName'];
			$Data1['vRiderMail']=$bookingdetail[0]['vEmail'];
			$Data1['vBookingNo']=$bookingdetail[0]['vBookingNo'];
			$Data1['dBookingdate']=date('Y-m-d H:i', strtotime($bookingdetail[0]['dBooking_date'])); 
			if($eStatus == "Accepted"){
				$returnArr['message'] = "LBL_JOB_ACCEPTED";
				$sendMailtoUser = $generalobj->send_email_user("MANUAL_BOOKING_ACCEPT_BYDRIVER_SP",$Data1);
				}else if($eStatus == "Declined"){
				$returnArr['message'] = "LBL_JOB_DECLINED";
				$sendMailtoUser = $generalobj->send_email_user("MANUAL_BOOKING_DECLINED_BYDRIVER_SP",$Data1);
				}else{
				$returnArr['message'] = getDriverDetailInfo($iDriverId); 
			} 
			if($eStatus == "Accepted" || $eStatus == "Declined"){
				$USER_SMS_TEMPLATE = ($eStatus == "Accepted")?"BOOKING_ACCEPT_BYDRIVER_MESSAGE_SP":"BOOKING_DECLINED_BYDRIVER_MESSAGE_SP";
				$message_layout = $generalobj->send_messages_user($USER_SMS_TEMPLATE,$Data1,"",$UserLang);
      			$UsersendMessage = $generalobj->sendUserSMS($UserPhoneNo,$UserPhoneCode,$message_layout,"");
      			if($UsersendMessage == 0){
      				$isdCode= $SITE_ISD_CODE;
      				$UserPhoneCode = $isdCode;
      				$UsersendMessage = $generalobj->sendUserSMS($UserPhoneNo,$UserPhoneCode,$message_layout,"");
				}
			} 
			$returnArr['Action'] = "1";
			if($eStatus == "Accepted"){
				$returnArr['message'] = "LBL_JOB_ACCEPTED";
				}else if($eStatus == "Declined"){
				$returnArr['message'] = "LBL_JOB_DECLINED";
				}else{
				$returnArr['message'] = getDriverDetailInfo($iDriverId); 
			} 
			}else{
  			$returnArr['Action'] = "0";
  			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";		
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);exit;
	}               
	###########################################################################
	###########################################################################
	###########################Display User Address##########################################################
	if($type == "DisplayUserAddress") {
		global $generalobj, $tconfig;
		$iUserId = isset($_REQUEST['iUserId']) ? clean($_REQUEST['iUserId']) : '';
		$eUserType = isset($_REQUEST['eUserType']) ? clean($_REQUEST['eUserType']) : 'Passenger';
		if($eUserType == "Passenger"){
			$eUserType = "Rider";
		}
		$sql = "select * from `user_address` where iUserId = '" . $iUserId . "' AND eUserType = '".$eUserType."' AND eStatus = 'Active' ORDER BY iUserAddressId DESC";
		$db_userdata = $obj->MySQLSelect($sql);
		if (count($db_userdata) > 0) {
			$returnArr['Action'] = "1";
			$returnArr['message'] = $db_userdata;
			} else {
			$returnArr['Action'] = "0";
			$returnArr['message'] = "LBL_NO_USER_ADDRESS_FOUND";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	} 
	###########################Display User Address End######################################################
	###########################Add/Update User Address ##########################################################
	if($type=="UpdateUserAddressDetails"){
		global $generalobj,$tconfig;
		$iUserAddressId = isset($_REQUEST['iUserAddressId'])?$_REQUEST['iUserAddressId']:'';
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$eUserType = isset($_REQUEST["eUserType"]) ? $_REQUEST["eUserType"] : 'Passenger';
		$vServiceAddress = isset($_REQUEST["vServiceAddress"]) ? $_REQUEST["vServiceAddress"] : '';
		$vBuildingNo = isset($_REQUEST["vBuildingNo"]) ? $_REQUEST["vBuildingNo"] : '';
		$vLandmark = isset($_REQUEST["vLandmark"]) ? $_REQUEST["vLandmark"] : '';
		$vAddressType = isset($_REQUEST["vAddressType"]) ? $_REQUEST["vAddressType"] : '';
		$vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
		$vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
		$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		$eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active';
		$iSelectVehicalId  = isset($_REQUEST["iSelectVehicalId"]) ? $_REQUEST["iSelectVehicalId"] : '';
		$IsProceed = "Yes";
		if($iSelectVehicalId == "" || $iSelectVehicalId == NULL){
			$IsProceed = "Yes";
		} 
		
		if($iSelectVehicalId != ""){  
			$pickuplocationarr = array($vLatitude,$vLongitude);
      //$allowed_ans = checkRestrictedAreaNew($pickuplocationarr,"No");
      $allowed_ans = checkAllowedAreaNew($pickuplocationarr,"No");
			if($allowed_ans == "Yes"){
				$GetVehicleIdfromGeoLocation = GetVehicleTypeFromGeoLocation($pickuplocationarr);
				$sql23 = "SELECT iVehicleTypeId FROM `vehicle_type` WHERE iLocationid IN ($GetVehicleIdfromGeoLocation) ORDER BY iVehicleTypeId ASC";
				$vehicleTypes = $obj->MySQLSelect($sql23);
				$Vehicle_Str = "";
				if(count($vehicleTypes) > 0){
					for($i=0;$i<count($vehicleTypes);$i++){
						$Vehicle_Str .= $vehicleTypes[$i]['iVehicleTypeId'].","; 
					}
					$Vehicle_Str = substr($Vehicle_Str,0,-1);
				}
				$Vehicle_Str_Arr = explode(",",$Vehicle_Str);
				if(in_array($iSelectVehicalId,$Vehicle_Str_Arr)){
					$IsProceed = "Yes";
					} else {
					$IsProceed = "No";
				}
				}else{
				$IsProceed = "No";
			}   
		}  
		
		if($eUserType == "Passenger"){
			$UserType = "Rider";
			}else{
			$UserType = "Driver";
		}
		$dAddedDate = @date("Y-m-d H:i:s"); 
		$action = ($iUserAddressId != '')?'Edit':'Add';
		
		$Data_User_Address['iUserId']=$iUserId;
		$Data_User_Address['eUserType']=$UserType;
		$Data_User_Address['vServiceAddress']=$vServiceAddress;
		$Data_User_Address['vBuildingNo']=$vBuildingNo;
		$Data_User_Address['vLandmark']=$vLandmark;
		$Data_User_Address['vAddressType']=$vAddressType;
		$Data_User_Address['vLatitude']=$vLatitude;
		$Data_User_Address['vLongitude']=$vLongitude;
		$Data_User_Address['dAddedDate']=$dAddedDate;
		$Data_User_Address['vTimeZone']=$vTimeZone;
		$Data_User_Address['eStatus']=$eStatus;
		
		if($action == "Add"){
			$insertid = $obj->MySQLQueryPerform("user_address",$Data_User_Address,'insert');
			$AddressId = $insertid;
			}else{
			$where = " iUserAddressId = '".$iUserAddressId."'";
			$insertid = $obj->MySQLQueryPerform("user_address",$Data_User_Address,'update',$where);
			$AddressId = $iUserAddressId;
		}
		
		if($insertid > 0){
			$returnArr['Action'] = "1";
			$returnArr['AddressId'] = $insertid;
			$returnArr['message1'] = "LBL_ADDRSS_ADD_SUCCESS";
			$returnArr['IsProceed'] = $IsProceed;
			if($eUserType == "Passenger"){
				$returnArr['message'] = getPassengerDetailInfo($iUserId,"");
				}else{
				$returnArr['message'] = getDriverDetailInfo($iUserId);
			}
			}else{
			$returnArr['Action'] ="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	##############################Add/Update User Address End##########################################################
	##############################Delete User Address #################################################################
	if ($type == "DeleteUserAddressDetail") {
		global $generalobj,$tconfig;
		$iUserAddressId = isset($_REQUEST['iUserAddressId'])?$_REQUEST['iUserAddressId']:'';
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$eUserType = isset($_REQUEST["eUserType"]) ? $_REQUEST["eUserType"] : 'Passenger';
		if($eUserType == "Passenger"){
			$UserType = "Rider";
			}else{
			$UserType = "Driver";
		}
		$sql = "Update user_address set eStatus = 'Deleted' WHERE `iUserAddressId`='".$iUserAddressId."' AND `iUserId`='".$iUserId."' AND eUserType = '".$UserType."'";
		$id = $obj->MySQLSelect($sql);
		if($id >0){
			$returnArr['Action']="1";
			$returnArr['message1']="LBL_USER_ADDRESS_DELETED_TXT";
			if($eUserType == "Passenger"){
				$returnArr['message'] = getPassengerDetailInfo($iUserId,"");
				}else{
				$returnArr['message'] = getDriverDetailInfo($iUserId);
			}
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);exit;
	}
	##############################Delete User Address Ends#################################################################   
	##############################Update Driver Manage Timing #################################################################
	if ($type == "UpdateDriverManageTiming") {
		global $generalobj,$tconfig;
		$iDriverTimingId = isset($_REQUEST['iDriverTimingId'])?$_REQUEST['iDriverTimingId']:'';
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$vAvailableTimes = isset($_REQUEST["vAvailableTimes"]) ? $_REQUEST["vAvailableTimes"] : '';  // 4-5,5-6,7-8,11-12,14-15
		$scheduleDate =  isset($_REQUEST["scheduleDate"]) ? $_REQUEST["scheduleDate"] : ''; // 2017-10-18
		$eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active'; 
		$vDay = date('l', strtotime($scheduleDate));
		$dAddedDate = @date("Y-m-d H:i:s");
		$vAvailableTimes = CheckAvailableTimes($vAvailableTimes);  // Convert to 04-05,05-06,07-08,11-12,14-15
		$action = ($iDriverTimingId != '')?'Edit':'Add';
		$Data_Update_Timing['iDriverId']=$iDriverId;
		$Data_Update_Timing['vDay']=$vDay;
		$Data_Update_Timing['vAvailableTimes']=$vAvailableTimes;
		$Data_Update_Timing['dAddedDate']=$dAddedDate;
		$Data_Update_Timing['eStatus']=$eStatus;
		if($action == "Add"){
			$insertid = $obj->MySQLQueryPerform("driver_manage_timing",$Data_Update_Timing,'insert');
			}else{
			$where = " iDriverTimingId = '".$iDriverTimingId."'";
			$insertid = $obj->MySQLQueryPerform("driver_manage_timing",$Data_Update_Timing,'update',$where);
		}
		if($insertid > 0){
			$returnArr['Action'] = "1";
			$returnArr['message'] = getDriverDetailInfo($iDriverId);
			}else{
			$returnArr['Action'] ="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	##############################Update Driver Manage Timing Ends#################################################################   
	###########################Display Availability##########################################################
	if($type == "DisplayAvailability") {
		global $generalobj, $tconfig;
		$iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
		$vDay = isset($_REQUEST['vDay']) ? clean($_REQUEST['vDay']) : '';
		$sql = "select * from `driver_manage_timing` where iDriverId = '" . $iDriverId . "' AND vDay LIKE '" . $vDay . "' ORDER BY iDriverTimingId DESC";
		$db_data = $obj->MySQLSelect($sql);
		if (count($db_data) > 0) {
			$returnArr['Action'] = "1";
			$returnArr['message'] = $db_data[0];
			} else {
			$returnArr['Action'] = "0";
			$returnArr['message'] = "LBL_NO_AVAILABILITY_FOUND";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	} 
	###########################Display Availability End######################################################
	###########################Add/Update Availability ##########################################################
	if($type=="UpdateAvailability"){
		global $generalobj,$tconfig;
		$iDriverTimingId = isset($_REQUEST['iDriverTimingId'])?$_REQUEST['iDriverTimingId']:'';
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$vDay = isset($_REQUEST["vDay"]) ? $_REQUEST["vDay"] : '';
		$vAvailableTimes = isset($_REQUEST["vAvailableTimes"]) ? $_REQUEST["vAvailableTimes"] : '';
		$eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active';
		$dAddedDate = @date("Y-m-d H:i:s");
		$vAvailableTimes = CheckAvailableTimes($vAvailableTimes);  // Convert to 04-05,05-06,07-08,11-12,14-15
		$sql = "select iDriverTimingId from `driver_manage_timing` where iDriverId = '" . $iDriverId . "' AND vDay LIKE '" . $vDay . "'";
		$db_data = $obj->MySQLSelect($sql);
		//$action = ($iDriverTimingId != '')?'Edit':'Add';
		if(count($db_data) > 0){
			$action = "Edit";
			$iDriverTimingId = $db_data[0]['iDriverTimingId'];
			}else{             
			$action = "Add";
		}
		$Data_driver_timing['iDriverId']=$iDriverId;
		$Data_driver_timing['vDay']=$vDay;
		$Data_driver_timing['vAvailableTimes']=$vAvailableTimes;
		$Data_driver_timing['dAddedDate']=$dAddedDate;
		$Data_driver_timing['eStatus']=$eStatus;
		if($action == "Add"){
			$insertid = $obj->MySQLQueryPerform("driver_manage_timing",$Data_driver_timing,'insert');
      		$TimingId = $insertid;
			}else{
			$where = " iDriverTimingId = '".$iDriverTimingId."'";
			$insertid = $obj->MySQLQueryPerform("driver_manage_timing",$Data_driver_timing,'update',$where);
     		$TimingId = $iDriverTimingId;
		}
		if($insertid > 0){
			$returnArr['Action'] = "1";
      		$returnArr['TimingId'] = $insertid;
			$returnArr['message'] ="LBL_TIMESLOT_ADD_SUCESS_MSG";
			}else{
			$returnArr['Action'] ="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	##############################Add/Update User Address End##########################################################  
	#===================Display user status=========================
	if($type == "GetUserStats") {
		global $generalobj, $tconfig;
		$iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
		$currDate = date('Y-m-d H:i:s');
		$ssql1 = " AND dBooking_date > '" . $currDate . "'";
		$sql = "select count(iCabBookingId) as Total_Pending from `cab_booking` where iDriverId != '' AND eStatus = 'Pending' AND iDriverId = '" . $iDriverId . "' ".$ssql1." ORDER BY iCabBookingId DESC";
		$db_data_pending = $obj->MySQLSelect($sql);
		$sql1 = "select count(iCabBookingId) as Total_Upcoming from `cab_booking` where  iDriverId != '' AND eStatus = 'Accepted' AND iDriverId='".$iDriverId."' ".$ssql1." ORDER BY iCabBookingId DESC";
		$db_data_assign = $obj->MySQLSelect($sql1);
		$sql2 = "SELECT vWorkLocationRadius as Radius FROM register_driver where iDriverId = '" . $iDriverId . "' ORDER BY iDriverId DESC ";
		$db_data_radius = $obj->MySQLSelect($sql2);
		// $radius = ($db_data_radius[0] != "") ?  $db_data_radius[0] : array("Radius"=>"0");
		$eUnit = getMemberCountryUnit($iDriverId,"Driver");
		if($eUnit == "Miles"){
			$db_data_radius[0]['Radius'] = round($db_data_radius[0]['Radius'] * 0.621371); 
		}
		$returnArr['Action'] = "1";
		$returnArr['Pending_Count'] = (count($db_data_pending) > 0 && empty($db_data_pending) == false)? $db_data_pending[0]['Total_Pending'] : 0;
		$returnArr['Upcoming_Count'] = (count($db_data_assign) > 0 && empty($db_data_assign) == false) ? $db_data_assign[0]['Total_Upcoming'] : 0;
		$returnArr['Radius'] = count($db_data_radius) > 0 ? $db_data_radius[0]['Radius'] : 0;
		/* if (count($db_data_pending) > 0 || count($db_data_assign) > 0 || count($db_data_radius) > 0) {
			$returnArr['Action'] = "1";
			$returnArr['Pending_Count'] = $db_data_pending[0]['Total_Pending'];
			$returnArr['Upcoming_Count'] = $db_data_assign[0]['Total_Upcoming'];
			$returnArr['Radius'] = $radius['Radius'];
			} else {
			$returnArr['Action'] = "0";
			$returnArr['Message'] = "LBL_NO_DATA_FOUND";
		} */
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	} 
	##############################Display user status End########################################################## 
	##############################Update Radius ##########################################################
	if($type=="UpdateRadius"){
		global $generalobj,$tconfig;
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
		$vWorkLocationRadius = isset($_REQUEST["vWorkLocationRadius"]) ? $_REQUEST["vWorkLocationRadius"] : '';
		$eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active';
		$Data_register_driver['vWorkLocationRadius']=$vWorkLocationRadius;
		$eUnit = getMemberCountryUnit($iDriverId,"Driver");
		if($eUnit == "Miles"){
			$Data_register_driver['vWorkLocationRadius'] = round($vWorkLocationRadius * 1.60934,2);  // convert miles to km 
		}
		$where = " iDriverId = '".$iDriverId."'";
		$updateid = $obj->MySQLQueryPerform("register_driver",$Data_register_driver,'update',$where);
		if($updateid > 0){
			$returnArr['Action'] = "1";
      		$returnArr['UpdateId'] = $iDriverId;
			$returnArr['message'] = getDriverDetailInfo($iDriverId);
			$returnArr['message1'] ="LBL_INFO_UPDATED_TXT";
			}else{
			$returnArr['Action'] ="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	##############################Update Radius  End##########################################################
	###########################Display Driver Day Availability##########################################################
	if($type == "DisplayDriverDaysAvailability") {
		global $generalobj, $tconfig;
		$iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
		$sql = "select vDay from `driver_manage_timing` where iDriverId = '" . $iDriverId . "' AND  vAvailableTimes <> '' ORDER BY iDriverTimingId DESC";
		$db_data = $obj->MySQLSelect($sql);
		if (count($db_data) > 0) {
			$returnArr['Action'] = "1";
			$returnArr['message'] = $db_data;
			} else {
			$returnArr['Action'] = "0";
			$returnArr['message'] = "LBL_NO_AVAILABILITY_FOUND";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	} 
	###########################Display Driver Day Availability Ends########################################################## 
	###########################Check  Schedule Booking Time Availability##########################################################
	if($type == "CheckScheduleTimeAvailability") {
		global $generalobj, $tconfig;
		$scheduleDate =  isset($_REQUEST["scheduleDate"]) ? $_REQUEST["scheduleDate"] : '';
		$vTimeZone    =isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		$systemTimeZone = date_default_timezone_get();
		// echo "hererrrrr:::".$systemTimeZone;exit;
		$currentdate = date("Y-m-d H:i:s");
		$currentdate = converToTz($currentdate,$vTimeZone,$systemTimeZone);
		$sdate = explode(" ",$scheduleDate);
		$shour = explode("-",$sdate[1]);
		$shour1 = $shour[0];   
    $shour2 = $shour[1];
    if($shour1 == "12" && $shour2 == "01"){
        $shour1 = 00;
    }  
		$scheduleDate = $sdate[0]." ".$shour1.":00:00";
		$datediff = strtotime($scheduleDate)-strtotime($currentdate);
		if($datediff > 3600){
			$returnArr['Action'] = "1";
			$returnArr['message'] = "";
			} else {
			$returnArr['Action'] = "0";
			$returnArr['message'] = "LBL_SCHEDULE_TIME_NOT_AVAILABLE";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	############################Check  Schedule Booking Time Availability Ends########################################################## 
	#############################Display  Schedule Booking Details######################################################################
	if($type == "DisplayScheduleBookingDetail") {
		global $generalobj, $tconfig;
		$iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
		$eUserType = isset($_REQUEST["eUserType"]) ? $_REQUEST["eUserType"] : 'Passenger';
		$iCabBookingId= isset($_REQUEST["iCabBookingId"]) ? $_REQUEST["iCabBookingId"] : '';
		//$APP_TYPE = $generalobj->getConfigurations("configurations","APP_TYPE");
		//$APP_TYPE = "UberX";
		if($iCabBookingId != ""){
			$sql = "SELECT * from cab_booking WHERE iCabBookingId = '" . $iCabBookingId . "'";
    		$bookingData = $obj->MySQLSelect($sql);
			if($eUserType == "Passenger"){
    			$tableName = "register_driver";
				$fields = 'iDriverId, vPhone,vCode as vPhoneCode, vEmail, CONCAT(vName," ",vLastName) as vName,vAvgRating,vImage as Imgname,vLang';
    			$condfield = 'iDriverId';
				$UserId = $bookingData[0]['iDriverId']; 
				$Photo_Gallery_folder_path = $tconfig['tsite_upload_images_driver_path']."/".$UserId."/";
				$Photo_Gallery_folder = $tconfig['tsite_upload_images_driver']."/".$UserId."/";
				$vCurrency = get_value('register_user', 'vCurrencyPassenger', 'iUserId', $bookingData[0]['iUserId'], '', 'true');
				}else{
    			$tableName = "register_user";
				$fields = 'iUserId, vPhone,vPhoneCode as vPhoneCode, vEmail, CONCAT(vName," ",vLastName) as vName,vAvgRating,vImgName as Imgname,vLang';
    			$condfield = 'iUserId';
				$UserId = $bookingData[0]['iUserId'];
				$Photo_Gallery_folder_path = $tconfig['tsite_upload_images_passenger_path']."/".$UserId."/";
				$Photo_Gallery_folder = $tconfig['tsite_upload_images_passenger']."/".$UserId."/";
				$vCurrency = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $bookingData[0]['iDriverId'], '', 'true');
			}  
			$sql = "select $fields from $tableName where $condfield = '" .$UserId. "'";
    		$db_member = $obj->MySQLSelect($sql);
			$lang = $db_member[0]['vLang'];
		    if ($lang == "" || $lang == NULL) {
    			$lang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
			}
			$db_member[0]['vLang'] = $lang;
			if($vCurrency == "" || $vCurrency == NULL){
				$vCurrency = get_value('currency', 'vName', 'eDefault', 'Yes','','true');
			}
			$UserCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrency);
    		$priceRatio = $UserCurrencyData[0]['Ratio'];
    		$vSymbol = $UserCurrencyData[0]['vSymbol'];
			$db_member[0]['vSymbol'] = $vSymbol;
			$imgpath = $Photo_Gallery_folder_path."2_".$db_member[0]['Imgname'];
			if($db_member[0]['Imgname'] != "" && file_exists($imgpath)){
				$db_member[0]['Imgname'] = $Photo_Gallery_folder."2_".$db_member[0]['Imgname']; 
				}else{
				$db_member[0]['Imgname'] = "";
			}
			$vehicleDetailsArr = array();
			$iVehicleTypeId = $bookingData[0]['iVehicleTypeId'];
			$sql2 = "SELECT vc.iVehicleCategoryId, vc.iParentId,vc.vCategory_".$lang." as vCategory, vc.vCategoryTitle_".$lang." as vCategoryTitle, vc.tCategoryDesc_".$lang." as tCategoryDesc, vc.ePriceType, vt.vVehicleType_".$lang." as vVehicleType, vt.eFareType, vt.fFixedFare, vt.fPricePerHour, vt.fPricePerKM, vt.fPricePerMin, vt.iBaseFare,vt.fCommision, vt.iMinFare,vt.iPersonSize, vt.vLogo as vVehicleTypeImage, vt.eType, vt.eIconType, vt.eAllowQty, vt.iMaxQty, vt.iVehicleTypeId, fFixedFare FROM vehicle_category as vc LEFT JOIN vehicle_type AS vt ON vt.iVehicleCategoryId = vc.iVehicleCategoryId WHERE vt.iVehicleTypeId='".$iVehicleTypeId."'";
		    $Data = $obj->MySQLSelect($sql2);
			$iParentId = $Data[0]['iParentId'];
			if($iParentId == 0){
				$ePriceType = $Data[0]['ePriceType'];
				}else{ 
				$ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId,'','true'); 
			} 
			$ALLOW_SERVICE_PROVIDER_AMOUNT = $ePriceType == "Provider"? "Yes" :"No";
			if($Data[0]['eFareType'] == "Fixed"){
    			//$fAmount = $vCurrencySymbol.$vehicleTypeData[0]['fFixedFare'];
				$fAmount = $Data[0]['fFixedFare'];
				}else if($Data[0]['eFareType'] == "Hourly"){
    			//$fAmount = $vCurrencySymbol.$vehicleTypeData[0]['fPricePerHour']."/hour";
				$fAmount = $Data[0]['fPricePerHour'];
				}else{
				$vDistance = $bookingData[0]['vDistance'];
				$vDuration = $bookingData[0]['vDuration'];
				$Minute_Fare = round($Data[0]['fPricePerMin'] * $vDuration, 2);
				$Distance_Fare = round($Data[0]['fPricePerKM'] * $vDistance, 2);
				$iBaseFare = round($Data[0]['iBaseFare'], 2);
				$fAmount = $iBaseFare + $Minute_Fare + $Distance_Fare;
			}
			$iPrice = $fAmount; 
			if($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes"){
				$sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='".$iDriverVehicleId."' AND iVehicleTypeId='".$iVehicleTypeId."'";
				$serviceProData = $obj->MySQLSelect($sqlServicePro);
				if(count($serviceProData) > 0){
					$fAmount = $serviceProData[0]['fAmount'];
    				}else{
					$fAmount = $iPrice;
				}
				$iPrice = $fAmount;
			} 
			$iPrice = $iPrice * $priceRatio;  
			$iPrice = round($iPrice,2);
			$vehicleDetailsArr['fAmount'] = $vSymbol." ".$iPrice;
			$vehicleDetailsArr['ePriceType'] = $ePriceType;
			$vehicleDetailsArr['ALLOW_SERVICE_PROVIDER_AMOUNT'] = $ALLOW_SERVICE_PROVIDER_AMOUNT;
			$returnArr['Action']="1";
			$returnArr['MemberDetails'] = $db_member;
			$returnArr['VehicleDetails'] = $vehicleDetailsArr;
			}else{
			$returnArr['Action']="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}   
		$obj->MySQLClose();
		echo json_encode($returnArr);exit;
	}  
	#############################Display  Schedule Booking Details Ends#################################################################
	#############################Check Source Location and get Vehicle Deteails#################################################################
	if($type == "CheckSourceLocationState") {
		global $generalobj, $tconfig;
		$PickUpLatitude    = isset($_REQUEST["PickUpLatitude"]) ? $_REQUEST["PickUpLatitude"] : '0.0';
		$PickUpLongitude    =isset($_REQUEST["PickUpLongitude"]) ? $_REQUEST["PickUpLongitude"] : '0.0';
		$selectedCarTypeID    = isset($_REQUEST["SelectedCarTypeID"]) ? $_REQUEST["SelectedCarTypeID"] : '';
		//$APP_TYPE = $generalobj->getConfigurations("configurations","APP_TYPE");
		$CurrentCabGeneralType =  isset($_REQUEST["CurrentCabGeneralType"]) ? $_REQUEST["CurrentCabGeneralType"] : ''; 
		$APP_TYPE = $CurrentCabGeneralType;
		
		if($APP_TYPE == "Delivery" || $APP_TYPE == "Deliver"){
			$ssql.= " AND eType = 'Deliver'";
			}else if($APP_TYPE == "Ride-Delivery" || $APP_TYPE == "Ride-Deliver"){
			$ssql.= " AND ( eType = 'Deliver' OR eType = 'Ride')";
			}else if($APP_TYPE == "Ride-Delivery-UberX" || $APP_TYPE == "Ride-Deliver-UberX"){
			$ssql.= " AND ( eType = 'Deliver' OR eType = 'Ride' OR eType = 'UberX')";
			}else{
			$ssql.= " AND eType = '".$APP_TYPE."'";
		}
		
		$pickuplocationarr = array($PickUpLatitude,$PickUpLongitude);
    $allowed_ans = checkAllowedAreaNew($pickuplocationarr,"No");
    if($allowed_ans == "No"){
       $returnArr['Action']="1";
       $obj->MySQLClose();
       echo json_encode($returnArr);exit;
    }
		$GetVehicleIdfromGeoLocation = GetVehicleTypeFromGeoLocation($pickuplocationarr);
		//$sql23 = "SELECT iVehicleTypeId FROM `vehicle_type` WHERE iLocationid IN ($GetVehicleIdfromGeoLocation) AND iVehicleTypeId IN ($selectedCarTypeID) ORDER BY iVehicleTypeId ASC";
		$sql23 = "SELECT iVehicleTypeId FROM `vehicle_type` WHERE iLocationid IN ($GetVehicleIdfromGeoLocation) $ssql ORDER BY iVehicleTypeId ASC";
		$vehicleTypes = $obj->MySQLSelect($sql23);  
		$Vehicle_Str = "";
		if(count($vehicleTypes) > 0){
			for($i=0;$i<count($vehicleTypes);$i++){
				$Vehicle_Str .= $vehicleTypes[$i]['iVehicleTypeId'].","; 
			}
			$Vehicle_Str = substr($Vehicle_Str,0,-1);
		}
		$selectedCarTypeID_Arr = explode(",",$selectedCarTypeID);
		$Vehicle_Str_Arr = explode(",",$Vehicle_Str);
		if($selectedCarTypeID_Arr === array_intersect($selectedCarTypeID_Arr, $Vehicle_Str_Arr) && $Vehicle_Str_Arr === array_intersect($Vehicle_Str_Arr, $selectedCarTypeID_Arr)) {
			$returnArr['Action']="0";
		} else {
			$returnArr['Action']="1";
		}
		
		$obj->MySQLClose();
		echo json_encode($returnArr);exit;
	}
	#############################Check Source Location and get Vehicle Deteails#################################################################
	#############################Check Restriction For Pickup and DropOff Location For Delivery#########################################
	if($type=="Checkpickupdropoffrestriction"){
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$PickUpLatitude    = isset($_REQUEST["PickUpLatitude"]) ? $_REQUEST["PickUpLatitude"] : '0.0';
		$PickUpLongitude    =isset($_REQUEST["PickUpLongitude"]) ? $_REQUEST["PickUpLongitude"] : '0.0';
    $DestLatitude    = isset($_REQUEST["DestLatitude"]) ? $_REQUEST["DestLatitude"] : '';
		$DestLongitude    =isset($_REQUEST["DestLongitude"]) ? $_REQUEST["DestLongitude"] : '';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$CheckType = isset($_REQUEST["CheckType"]) ? $_REQUEST["CheckType"] : 'Pickup'; // Pickup Or Drop
		if($CheckType == "" || $CheckType == NULL){
			$CheckType = "Pickup";
		}
    $pickuplocationarr = array($PickUpLatitude,$PickUpLongitude);
    $allowed_ans = checkAllowedAreaNew($pickuplocationarr,"No");
    $dropofflocationarr = array($DestLatitude,$DestLongitude);
    $allowed_ans_drop = checkAllowedAreaNew($dropofflocationarr,"Yes");
		$returnArr['Action'] = "1";
    if($allowed_ans == "No" && $allowed_ans_drop == "No"){
				$returnArr['Action'] = "0";
  			$returnArr['message'] = "LBL_PICK_DROP_LOCATION_NOT_ALLOW";
  			echo json_encode($returnArr);
  			exit;
			}
		if($allowed_ans == "Yes" && $allowed_ans_drop == "No"){
				$returnArr['Action'] = "0";
    			$returnArr['message'] = "LBL_DROP_LOCATION_NOT_ALLOW";
  			echo json_encode($returnArr);
  			exit;
			}
		if($allowed_ans == "No" && $allowed_ans_drop == "Yes"){
			  $returnArr['Action'] = "0";
  			$returnArr['message'] = "LBL_PICKUP_LOCATION_NOT_ALLOW";
  			echo json_encode($returnArr);
  			exit;
		}
		echo json_encode($returnArr);exit;
	} 
	#############################Check Restriction For Pickup and DropOff Location For Delivery#########################################
	#############################Check Restriction For Pickup and DropOff Location For UberX#########################################
	if($type=="Checkuseraddressrestriction"){
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
		$iUserAddressId = isset($_REQUEST["iUserAddressId"]) ? $_REQUEST["iUserAddressId"] : '';
		$iSelectVehicalId  = isset($_REQUEST["iSelectVehicalId"]) ? $_REQUEST["iSelectVehicalId"] : '';
		
		$sql = "SELECT vLatitude,vLongitude FROM user_address WHERE iUserAddressId='".$iUserAddressId."'";
		$address_data = $obj->MySQLSelect($sql);
		if(count($address_data) > 0){
			$StartLatitude = $address_data[0]['vLatitude'];
			$EndLongitude = $address_data[0]['vLongitude'];
			$pickuplocationarr = array($StartLatitude,$EndLongitude);
      //$allowed_ans = checkRestrictedAreaNew($pickuplocationarr,"No");
      $allowed_ans = checkAllowedAreaNew($pickuplocationarr,"No");
			if($allowed_ans == "Yes"){
				$GetVehicleIdfromGeoLocation = GetVehicleTypeFromGeoLocation($pickuplocationarr);
				$sql23 = "SELECT iVehicleTypeId FROM `vehicle_type` WHERE iLocationid IN ($GetVehicleIdfromGeoLocation) ORDER BY iVehicleTypeId ASC";
				$vehicleTypes = $obj->MySQLSelect($sql23);
				$Vehicle_Str = "";
				if(count($vehicleTypes) > 0){
					for($i=0;$i<count($vehicleTypes);$i++){
						$Vehicle_Str .= $vehicleTypes[$i]['iVehicleTypeId'].","; 
					}
					$Vehicle_Str = substr($Vehicle_Str,0,-1);
				}
				$Vehicle_Str_Arr = explode(",",$Vehicle_Str);
				if(in_array($iSelectVehicalId,$Vehicle_Str_Arr)){
					$returnArr['Action']="1";
					} else {
					$returnArr['Action']="0";
					$returnArr['message']="LBL_NO_SERVICES_AVAIL_FOR_JOB_LOC";
				}
				}else{     
				$returnArr['Action']="0";
				$returnArr['message']="LBL_JOB_LOCATION_NOT_ALLOWED";
			}
			}else{
			$returnArr['Action']="0";
			$returnArr['message']="LBL_JOB_LOCATION_NOT_ALLOWED";
		}  
		$obj->MySQLClose();
		echo json_encode($returnArr);exit;
	} 
	#############################Check Restriction For Pickup and DropOff Location For UberX#########################################
	#################################### Add/Update User Favourite Address ##########################################################
	if($type=="UpdateUserFavouriteAddress"){
		global $generalobj,$tconfig;
		$iUserFavAddressId = isset($_REQUEST['iUserFavAddressId'])?$_REQUEST['iUserFavAddressId']:'';
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$eUserType = isset($_REQUEST["eUserType"]) ? $_REQUEST["eUserType"] : 'Passenger';    // Passenger , Driver
		$vAddress = isset($_REQUEST["vAddress"]) ? $_REQUEST["vAddress"] : '';
		$vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
		$vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
		$eType  = isset($_REQUEST["eType"]) ? $_REQUEST["eType"] : 'Home';   // Home,Work
		$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
		$eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active';
		$dAddedDate = @date("Y-m-d H:i:s"); 
		$action = ($iUserFavAddressId != '')?'Edit':'Add';
		$Data_User_Address['iUserId']=$iUserId;
		$Data_User_Address['eUserType']=$eUserType;
		$Data_User_Address['vAddress']=$vAddress;
		$Data_User_Address['vLatitude']=$vLatitude;
		$Data_User_Address['vLongitude']=$vLongitude;
		$Data_User_Address['eType']=$eType;
		$Data_User_Address['dAddedDate']=$dAddedDate;
		$Data_User_Address['vTimeZone']=$vTimeZone;
		$Data_User_Address['eStatus']=$eStatus;
		if($action == "Add"){
			$insertid = $obj->MySQLQueryPerform("user_fave_address",$Data_User_Address,'insert');
			$AddressId = $insertid;
			}else{
			$where = " iUserFavAddressId = '".$iUserFavAddressId."'";
			$insertid = $obj->MySQLQueryPerform("user_fave_address",$Data_User_Address,'update',$where);
			$AddressId = $iUserAddressId;
		}
		
		if($insertid > 0){
			$returnArr['Action'] = "1";
			$returnArr['AddressId'] = $insertid;
			$returnArr['message1'] = "LBL_ADDRSS_ADD_SUCCESS";
			if($eUserType == "Passenger"){
				$returnArr['message'] = getPassengerDetailInfo($iUserId,"");
				}else{
				$returnArr['message'] = getDriverDetailInfo($iUserId);
			}
			}else{
			$returnArr['Action'] ="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);
		exit;
	}
	#################################### Add/Update User Favourite Address ##########################################################
	##############################Delete User Favourite Address #################################################################
	if($type == "DeleteUserFavouriteAddress") {
		global $generalobj,$tconfig;
		$iUserFavAddressId = isset($_REQUEST['iUserFavAddressId'])?$_REQUEST['iUserFavAddressId']:'';
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$eUserType = isset($_REQUEST["eUserType"]) ? $_REQUEST["eUserType"] : 'Passenger';
		
		$sql = "DELETE FROM user_fave_address WHERE `iUserFavAddressId`='".$iUserFavAddressId."'";
		$id = $obj->MySQLSelect($sql);
		if($id >0){
			$returnArr['Action']="1";
			$returnArr['message1']="LBL_USER_ADDRESS_DELETED_TXT";
			if($eUserType == "Passenger"){
				$returnArr['message'] = getPassengerDetailInfo($iUserId,"");
				}else{
				$returnArr['message'] = getDriverDetailInfo($iUserId);
			}
			}else{
			$returnArr['Action'] = "0";
			$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
		}
		$obj->MySQLClose();
		echo json_encode($returnArr);exit;
	}
	##############################Delete User Favourite Address Ends#################################################################
	##########################################################
	##############################Check Vehicle eligble for hail ride #################################################
	if($type == "CheckVehicleEligibleForHail") {
		global $generalobj,$tconfig;
		$iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';

		if($COMMISION_DEDUCT_ENABLE == 'Yes' && ($APP_PAYMENT_MODE == "Cash" || $APP_PAYMENT_MODE == "Cash-Card")) {
			$user_available_balance = $generalobj->get_user_available_balance($iDriverId,"Driver");
			$driverDetail = get_value('register_driver AS rd LEFT JOIN currency AS c ON c.vName=rd.vCurrencyDriver', 'rd.vCurrencyDriver,c.Ratio,c.vSymbol', 'rd.iDriverId',$iDriverId);
			$ratio =$driverDetail[0]['Ratio'];
			$currencySymbol=$driverDetail[0]['vSymbol'];
			
			$vLang =get_value('register_driver', 'vLang', 'iDriverId',$iDriverId,'','true');
			if($vLang == "" || $vLang == NULL){
				$vLang = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
			}
			$languageLabelsArr= getLanguageLabelsArr($vLang,"1");
			
			if($WALLET_MIN_BALANCE > $user_available_balance){
				$returnArr['Action'] = "0";
				$returnArr['message']="REQUIRED_MINIMUM_BALNCE";
				if($APP_TYPE == "UberX"){
					$returnArr['Msg']=str_replace('####', $currencySymbol.($WALLET_MIN_BALANCE * $ratio), $languageLabelsArr['LBL_REQUIRED_MINIMUM_BALNCE_UBERX']);
					} else {
					$returnArr['Msg']=str_replace('####', $currencySymbol.($WALLET_MIN_BALANCE * $ratio), $languageLabelsArr['LBL_REQUIRED_MINIMUM_BALNCE_HAIL']);
				}
				echo json_encode($returnArr);exit;
			}
		}

		$iDriverVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $iDriverId,'','true'); 
		if($iDriverVehicleId > 0){
			$sql="SELECT vCarType FROM driver_vehicle WHERE iDriverVehicleId = '".$iDriverVehicleId."'";
			$vCarType = $obj->MySQLSelect($sql);
			$vehicleIds = explode(",", $vCarType[0]['vCarType']);
			$vehicleListIds = implode("','", $vehicleIds);
			$sql1 = "SELECT count(iVehicleTypeId) as total_ridevehicle FROM vehicle_type WHERE iVehicleTypeId IN ('".$vehicleListIds."') AND eType = 'Ride'";
			$Vehiclelist = $obj->MySQLSelect($sql1);	
			if($Vehiclelist[0]['total_ridevehicle'] > 0){
				$returnArr['Action']="1";
			} else {
				$returnArr['Action']="0";
				$returnArr['message']="LBL_VEHICLE_ELIGIBLE_FOR_HAIL_RIDE_MSG";
			}
		} /*else {
			$query="SELECT vCarType FROM driver_vehicle WHERE iDriverId = '".$iDriverId."'";
			$vCarType = $obj->MySQLSelect($query);
			foreach ($vCarType as $key => $value) {
				$vehicleType = $value['vCarType'];
				$vehicle_ids = explode(",", $vehicleType);
				$vehicle_id_list = implode("','", $vehicle_ids);
				$query1 = "SELECT count(iVehicleTypeId) as total_ridevehicle FROM vehicle_type WHERE iVehicleTypeId IN ('".$vehicle_id_list."') AND eType = 'Ride'";
				$Vehiclelist = $obj->MySQLSelect($query1);
				if($Vehiclelist[0]['total_ridevehicle'] > 0){
					$returnArr['Action']="1";
				} else {
					$returnArr['Action']="0";
					$returnArr['message']="Your Have Not Any Eligible Vehicle For Hali Ride.Please Add Vehicle.";
				}
			}
		}*/

		$obj->MySQLClose();
		echo json_encode($returnArr);exit;
	}
	##############################Check Vehicle eligble for hail ride Ends#################################################################  
################################################Get Member Wallet Balance########################################################
if($type=="GetMemberWalletBalance"){
		$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
		$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    if($UserType == "Passenger"){
			$tbl_name = "register_user";
			$currencycode = "vCurrencyPassenger";
			$iMemberId = "iUserId";
      $eUserType = "Rider";
		}else{
			$tbl_name = "register_driver";
			$currencycode = "vCurrencyDriver";
			$iMemberId = "iDriverId";
      $eUserType = "Driver";
		}
    $userCurrencyCode = get_value($tbl_name, $currencycode, $iMemberId, $iUserId,'','true');
    $user_available_balance = $generalobj->get_user_available_balance($iUserId,$eUserType);
    $returnArr['Action']="1";
    $returnArr["MemberBalance"] = strval($generalobj->userwalletcurrency(0,$user_available_balance,$userCurrencyCode));
    $obj->MySQLClose();
    echo json_encode($returnArr);exit;
}
################################################Get Member Wallet Balance########################################################
################################################CheckPendingBooking UBERX########################################################
if($type == "CheckPendingBooking"){
    $iCabBookingId= isset($_REQUEST["iCabBookingId"]) ? $_REQUEST["iCabBookingId"] : '';
		$eStatus= isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : '';
		$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
    $sql_book = "SELECT dBooking_date from cab_booking WHERE iCabBookingId ='".$iCabBookingId."'";
    $checkbooking = $obj->MySQLSelect($sql_book);
    $dBooking_date = $checkbooking[0]['dBooking_date'];
    $sql = "SELECT iCabBookingId from cab_booking WHERE iDriverId ='".$iDriverId."' AND dBooking_date = '".$dBooking_date."' AND eStatus = 'Accepted' AND iCabBookingId != '".$iCabBookingId."'";
		$pendingacceptdriverbooking = $obj->MySQLSelect($sql); 
    if(count($pendingacceptdriverbooking) > 0){
         $returnArr['Action'] = "0";
    		 $returnArr['message'] ="LBL_PENDING_PLUS_ACCEPT_BOOKING_AVAIL_TXT";
         $returnArr['message1'] = "Accept";
    }else{
       $sql = "SELECT iCabBookingId from cab_booking WHERE iDriverId ='".$iDriverId."' AND dBooking_date = '".$dBooking_date."' AND eStatus = 'Pending' AND iCabBookingId != '".$iCabBookingId."'";
		   $pendingdriverbooking = $obj->MySQLSelect($sql);
       if(count($pendingdriverbooking) > 0){
         $returnArr['Action'] = "0";
    		 $returnArr['message'] ="LBL_PENDING_BOOKING_AVAIL_TXT";
         $returnArr['message1'] = "Pending";
       }else{
          $returnArr['Action'] = "1";
       }
    }
    $obj->MySQLClose();
		echo json_encode($returnArr);exit;
}
################################################CheckPendingBooking UBERX########################################################
################################################UBERX Driver Update worklocation address, lat, long########################################################
if($type == "UpdateDriverWorkLocationUFX") {
	$iDriverId              = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
	$vWorkLocationLatitude  = isset($_REQUEST["vWorkLocationLatitude"]) ? $_REQUEST["vWorkLocationLatitude"] : '';
	$vWorkLocationLongitude = isset($_REQUEST["vWorkLocationLongitude"]) ? $_REQUEST["vWorkLocationLongitude"] : '';
	$vWorkLocation = isset($_REQUEST["vWorkLocation"]) ? $_REQUEST["vWorkLocation"] : '';
	$where = " iDriverId='$iDriverId'";
	$Data_update_driver['vWorkLocationLatitude']=$vWorkLocationLatitude;
	$Data_update_driver['vWorkLocationLongitude']=$vWorkLocationLongitude;
  $Data_update_driver['vWorkLocation']=$vWorkLocation;
	$id = $obj->MySQLQueryPerform("register_driver",$Data_update_driver,'update',$where);
  if ($id) {
		$returnArr['Action'] = "1";
	} else {
		$returnArr['Action'] = "0";
		$returnArr['message']="LBL_TRY_AGAIN_LATER_TXT";
	}
  $obj->MySQLClose();
	echo json_encode($returnArr);exit;
}
################################################UBERX Driver Update worklocation address, lat, long########################################################
################################Get Help Category ##################################################################### 
if ($type == "getHelpDetailCategoty"){
	$status = "Active";
	$iMemberId = isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
	$appType = isset($_REQUEST['appType'])?clean($_REQUEST['appType']):'';
	$languageCode="";
	if($appType == "Driver"){
		$languageCode =  get_value('register_driver', 'vLang', 'iDriverId',$iMemberId,'','true');
		}else{
		$languageCode =  get_value('register_user', 'vLang', 'iUserId',$iMemberId,'','true');
	}
	if($languageCode == ""){
		$languageCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
	}
	$sql = "SELECT * FROM `help_detail_categories` WHERE eStatus='$status' AND vCode='".$languageCode."' ORDER BY iDisplayOrder ASC ";
	$Data = $obj->MySQLSelect($sql);
	if(count($Data) > 0){
	$arr_cat = array();
		for($i=0;$i<count($Data);$i++){
			$arr_cat[$i]['iHelpDetailCategoryId'] = $Data[$i]['iHelpDetailCategoryId'];
			$arr_cat[$i]['vTitle'] = $Data[$i]['vTitle'];
			$arr_cat[$i]['vTitle'] = $Data[$i]['vTitle'];
			$arr_cat[$i]['iUniqueId'] = $Data[$i]['iUniqueId'];		
		}
		$returnData['Action']="1";
		$returnData['message']=$arr_cat;		
	}else{
		$returnData['Action']="0";
		$returnData['message']="LBL_HELP_DETAIL_NOT_AVAIL";
	}
	echo json_encode($returnData);
}
############################# End Get Help Category ################################################################ 
############################# getsubHelpdetail ##################################################################### 
if ($type == "getsubHelpdetail") {
	$status = "Active";
	$iMemberId = isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
	$iUniqueId = isset($_REQUEST['iUniqueId'])?clean($_REQUEST['iUniqueId']):'';
	$appType = isset($_REQUEST['appType'])?clean($_REQUEST['appType']):'';
	$languageCode="";
	if($appType == "Driver"){
		$languageCode =  get_value('register_driver', 'vLang', 'iDriverId',$iMemberId,'','true');
		}else{
		$languageCode =  get_value('register_user', 'vLang', 'iUserId',$iMemberId,'','true');
	}
	if($languageCode == ""){
		$languageCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
	}
	$sql = "SELECT vTitle_".$languageCode." as vTitle,tAnswer_".$languageCode." as tAnswer,eShowDetail,iHelpDetailId FROM `help_detail` WHERE eStatus='$status'  AND iHelpDetailCategoryId='".$iUniqueId."' ORDER BY iDisplayOrder ASC ";
	$Data = $obj->MySQLSelect($sql);
	if(count($Data) > 0){
		$arr_helpdetail = array();
		for($j=0;$j<count($Data); $j++){
			$arr_helpdetail[$j]['iHelpDetailId'] = $Data[$j]['iHelpDetailId'] ;
			$arr_helpdetail[$j]['vTitle'] = $Data[$j]['vTitle'] ;
			$arr_helpdetail[$j]['tAnswer'] = $Data[$j]['tAnswer'] ;
			$arr_helpdetail[$j]['eShowFrom'] = $Data[$j]['eShowDetail'] ;			
			}
			$returnData['Action']="1";
			$returnData['message']=$arr_helpdetail;		
		}else{
				$returnData['Action']="0";
				$returnData['message']="LBL_HELP_DETAIL_NOT_AVAIL";
			}
	echo json_encode($returnData);
}
#############################End getsubHelpdetail ##################################################################### 
#############################Start getHelpDetail ##################################################################### 
if ($type == "getHelpDetail") {
		$status = "Active";
        $iMemberId = isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
        $appType = isset($_REQUEST['appType'])?clean($_REQUEST['appType']):'';
        $languageCode="";
        if($appType == "Driver"){
            $languageCode =  get_value('register_driver', 'vLang', 'iDriverId',$iMemberId,'','true');
			}else{
			$languageCode =  get_value('register_user', 'vLang', 'iUserId',$iMemberId,'','true');
		}
		if($languageCode == ""){
			$languageCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		}
		 $sql = "SELECT vTitle_" .$languageCode. " as vTitle,iHelpDetailId FROM `help_detail` WHERE eStatus='$status'";
          $Data_detail = $obj->MySQLSelect($sql);        
        if (count($Data_detail) > 0) {
			$returnData['Action']="1";
			$returnData['message']=$Data_detail;
			}else{
			$returnData['Action']="0";
			$returnData['message']="LBL_HELP_DETAIL_NOT_AVAIL";
		}
        echo json_encode($returnData);
	}
############################# End getHelpDetail ##################################################################### 	
############################# Start submitTripHelpDetail ############################################################ 	
if ($type == "submitTripHelpDetail") {
	global $generalobj,$obj;
	$TripId = isset($_REQUEST['TripId'])?clean($_REQUEST['TripId']):'0';
     $iMemberId = isset($_REQUEST['iMemberId'])?clean($_REQUEST['iMemberId']):'';
     $iHelpDetailId = isset($_REQUEST['iHelpDetailId'])?clean($_REQUEST['iHelpDetailId']):'';
     $vComment = isset($_REQUEST['vComment'])?clean($_REQUEST['vComment']):'';
	 $appType = isset($_REQUEST['appType'])?clean($_REQUEST['appType']):'';
	 $current_date = date('Y-m-d H:i:s');
	   if($appType == "Driver"){	  
		$sql = "SELECT CONCAT(vName,' ',vLastName) as Name FROM `register_driver` WHERE iDriverId='".$iMemberId."'";	
		}else{	
		$sql = "SELECT CONCAT(vName,' ',vLastName) as Name FROM `register_user` WHERE iUserId='".$iMemberId."'";
	} 	  
	  $Data = $obj->MySQLSelect($sql);
	 $Data_trip_help_detail['iTripId'] = $TripId;
	$Data_trip_help_detail['iUserId'] = $iMemberId;						
	$Data_trip_help_detail['iHelpDetailId'] = $iHelpDetailId;						
	$Data_trip_help_detail['vComment'] = $vComment;						
	$Data_trip_help_detail['tDate'] = $current_date;						
	$id = $obj->MySQLQueryPerform('trip_help_detail',$Data_trip_help_detail,'insert');
	if($id > 0){	 
		$maildata['iTripId'] = $TripId;
		$maildata['NAME'] = $Data[0]['Name'];
		$maildata['vComment'] = $vComment;
		$maildata['Ddate'] = $current_date;
		$generalobj->send_email_user("RIDER_TRIP_HELP_DETAIL",$maildata);
		$returnArr['Action'] ="1";
		$returnArr['message'] = "LBL_COMMENT_ADDED_TXT";
		}else{
			$returnArr['Action'] ="0";
			$returnArr['message'] ="LBL_TRY_AGAIN_LATER_TXT";
		}
		echo json_encode($returnArr);
		exit; 
}	
############################# End submitTripHelpDetail ############################################################
  $obj->MySQLClose();
?>