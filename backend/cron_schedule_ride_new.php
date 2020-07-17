<?php 
	error_reporting(0);
	include_once('common.php');
	include_once(TPATH_CLASS.'class.general.php');
	require_once('assets/libraries/pubnub/autoloader.php');
	include_once(TPATH_CLASS.'configuration.php');
	include_once(TPATH_CLASS .'Imagecrop.class.php');
	include_once(TPATH_CLASS .'twilio/Services/Twilio.php');
	include_once('generalFunctions.php');

  $uuid = "fg5k3i7i7l5ghgk1jcv43w0j41";
	/* creating objects */
	$thumb 		= new thumbnail;
	$generalobj = new General();
	
	$ToDate = date('Y-m-d');
	$sql1 = "SELECT iCabBookingId,iCronStage,eAssigned,dBooking_date,vTimeZone FROM cab_booking WHERE eStatus='Pending' AND dBooking_date LIKE '%$ToDate%' AND eAutoAssign = 'Yes' AND iCronStage != '3' AND eAssigned='No'";
	$data_bks = $obj->MySQLSelect($sql1);
	// echo "<pre>"; print_r($data_bks); die;
	// echo date("Y-m-d H:i:s"); die;
	for($i=0;$i<count($data_bks);$i++){

		/*$tripTimeZone = $data_bks[$i]['vTimeZone'];
		if($tripTimeZone != ""){
      $serverTimeZone = date_default_timezone_get();
			$FromDate = converToTz(date('Y-m-d H:i:s'),$tripTimeZone,$serverTimeZone);
      $ToDate = converToTz($data_bks[$i]['dBooking_date'],$tripTimeZone,$serverTimeZone);
    }else{
      $FromDate = date('Y-m-d H:i:s');
		  // $FromDate = date('2017-06-06 13:38:36');
		  $ToDate = $data_bks[$i]['dBooking_date'];
    }*/
    
    $FromDate = date('Y-m-d H:i:s');
		  // $FromDate = date('2017-06-06 13:38:36');
		  $ToDate = $data_bks[$i]['dBooking_date'];
    
		$datetime1 = strtotime($FromDate);
		$datetime2 = strtotime($ToDate);
		$interval  = abs($datetime2 - $datetime1);
		
		$minutes   = round($interval / 60);
		
		if($data_bks[$i]['iCronStage'] == 0) {
			if($minutes <= 12 && $minutes >= 8) {
				sendRequest($data_bks[$i]['iCabBookingId']);
			}
		}

		if($data_bks[$i]['iCronStage'] == 1 || $data_bks[$i]['iCronStage'] == 0) {
			if($minutes <= 8 && $minutes >= 4) {
				sendRequest($data_bks[$i]['iCabBookingId']);
			}
		}

		if($data_bks[$i]['iCronStage'] == 2 || $data_bks[$i]['iCronStage'] == 1 || $data_bks[$i]['iCronStage'] == 0) {
			if($minutes <= 4 && $minutes >= 0) {
				sendRequest($data_bks[$i]['iCabBookingId']);
			}
		}
	}

	function sendRequest($cabId){
		global $generalobj, $obj,$uuid;
		$sql = "SELECT cb.*,CONCAT(ru.vName,' ', ru.vLastName) as passengerName,ru.vFbId,ru.vImgName,ru.vAvgRating,ru.vPhoneCode,ru.vPhone,ru.eGender FROM cab_booking as cb
		LEFT JOIN register_user as ru ON ru.iUserId = cb.iUserId
		WHERE cb.iCabBookingId='".$cabId."'";

		$data_booking = $obj->MySQLSelect($sql);
		if(count($data_booking) > 0) {

			$iUserId = $data_booking[0]['iUserId'];
			$sql="select iTripId,vTripStatus from register_user where iUserId='$iUserId'";
			$user_data = $obj->MySQLSelect($sql);
			$iTripId = $user_data[0]['iTripId'];
			if($iTripId != "" && $iTripId != 0){
				$status_trip = get_value("trips", 'iActive', "iTripId",$iTripId,'','true');
				// $cab_id = get_value("trips", 'iCabBookingId', "iTripId",$iTripId,'','true');
				if($status_trip == "Active" || $status_trip == "On Going Trip"){
					$where1 = " iCabBookingId = '$cabId' ";
					$Data_update_cab_booking['eCancelBySystem']="Yes";
					$Data_update_cab_booking['eStatus']="Cancel";
					$Data_update_cab_booking['vCancelReason']="User on another trip.";
					$Data_update_cab_booking['eCancelBy']="Admin";
					$id = $obj->MySQLQueryPerform("cab_booking",$Data_update_cab_booking,'update',$where1);
					return false;
					// break;
				}
			}
			$deviceTokens_arr_ios = array();
			$registation_ids_new = array();
			$vSourceLatitude= $data_booking[0]['vSourceLatitude'];
			$vSourceLongitude= $data_booking[0]['vSourceLongitude'];
			$vDestLatitude= $data_booking[0]['vDestLatitude'];
			$vDestLongitude= $data_booking[0]['vDestLongitude'];
			$eType = $data_booking[0]['eType'];
			$passengerId = $data_booking[0]['iUserId'];
			$passengerName = $data_booking[0]['passengerName'];
			$PPicName = $data_booking[0]['vImgName'];
			$vFbId = $data_booking[0]['vFbId'];
			$vAvgRating = $data_booking[0]['vAvgRating'];
			$vPhone = $data_booking[0]['vPhone'];
			$vPhoneCode = $data_booking[0]['vPhoneCode'];
			$iCronStage = $data_booking[0]['iCronStage'];

			$messageArr['Message'] = "CabRequested";
			$messageArr['iBookingId']= $data_booking[0]['iCabBookingId'];
			$messageArr['setCron']= 'Yes';
			$messageArr['sourceLatitude'] = strval($vSourceLatitude);
			$messageArr['sourceLongitude'] = strval($vSourceLongitude);
			$messageArr['PassengerId'] = strval($passengerId);
			$messageArr['PName'] = $passengerName;
			$messageArr['PPicName'] = $PPicName;
			$messageArr['PFId'] = $vFbId;
			$messageArr['PRating'] = $vAvgRating;
			$messageArr['PPhone'] = $vPhone;
			$messageArr['PPhoneC'] = $vPhoneCode;
			$messageArr['REQUEST_TYPE'] = $eType;
			$messageArr['PACKAGE_TYPE'] = $eType == "Deliver" ? get_value('package_type', 'vName', 'iPackageTypeId',$iPackageTypeId,'','true'):'';
			$messageArr['destLatitude'] = strval($vDestLatitude);
			$messageArr['destLongitude'] = strval($vDestLongitude);
			$messageArr['MsgCode'] = strval(time().mt_rand(1000, 9999));

			if($iCronStage > 0){
				$message = array();
				$addMsg = "Now trying to send another request.";
				if($iCronStage == 2){
					$addMsg = "Last time trying to send request to driver for the ride.";
				}
				$message['details'] = '<p>Dear Administrator,</p>
							<p>Driver was not available / not accepted request for the following manual booking in stage '.$iCronStage.'.'.$addMsg.' </p>
							<p>Name: '.$passengerName.',</p>
							<p>Contact Number: +'.$vPhoneCode.$vPhone.'</p>';
				$mail = $generalobj->send_email_user('CRON_BOOKING_EMAIL',$message);
			}

			$where_cabid = " iCabBookingId = '".$data_booking[0]['iCabBookingId']."'";
			$Data_update['iCronStage']= $iCronStage+1;
			$id = $obj->MySQLQueryPerform("cab_booking",$Data_update,'update',$where_cabid);
			
			$Data = array();
			$Data = getOnlineDriverArr($vSourceLatitude,$vSourceLongitude,"","","Yes");
			// echo "<pre>"; print_r($Data); die;
      ### Checking For Female Driver Request ##
      $Datalist = array(); 
      $Datalist = $Data['DriverList'];
      $DatalistNewArr = array();
      $DatalistNewArr = $Datalist;
      for($i=0;$i<count($Datalist); $i++){
         //echo $iDriverId=$Datalist[$i]['iDriverId'];echo "<br />";
         $isRemoveDriverIntoList = "No"; 
         $iVehicleTypeId=$data_booking[0]['iVehicleTypeId'];
         $iDriverVehicleId = $Datalist[$i]['iDriverVehicleId'];
         $sql = "SELECT vCarType,eHandiCapAccessibility FROM `driver_vehicle` WHERE iDriverVehicleId = '".$iDriverVehicleId."'";
			   $rows_driver_vehicle = $obj->MySQLSelect($sql);
         $DriverVehicleTypeArr = explode(",",$rows_driver_vehicle[0]['vCarType']);
         if(!in_array($iVehicleTypeId,$DriverVehicleTypeArr)){
  				  $isRemoveDriverIntoList = "Yes"; 							
  			 }
         //echo "Driver Id >> ".$Datalist[$i]['iDriverId']." >> Remove From Vehicle List >> ".$isRemoveDriverIntoList; echo "<br />";
         if($eType == "Ride"){
           $eHandiCapAccessibility=$data_booking[0]['eHandiCapAccessibility'];
           if($eHandiCapAccessibility == "" || $eHandiCapAccessibility == NULL){
              $eHandiCapAccessibility = "No";
           }  
           $DriverVehicleeHandiCapAccessibility=$rows_driver_vehicle[0]['eHandiCapAccessibility'];
           if($eHandiCapAccessibility == "Yes" && $DriverVehicleeHandiCapAccessibility != "Yes"){
              $isRemoveDriverIntoList = "Yes";
           }
         }
         //echo "Driver Id >> ".$Datalist[$i]['iDriverId']." >> Remove From HandiCapAccessibility List >> ".$isRemoveDriverIntoList; echo "<br />";
         if($eType == "Ride"){
           $DriverFemaleOnlyReqAccept = $Datalist[$i]['eFemaleOnlyReqAccept'];
           if($DriverFemaleOnlyReqAccept == "" || $DriverFemaleOnlyReqAccept == NULL){
              $DriverFemaleOnlyReqAccept = "No";
           }       
           $RiderGender = $data_booking[0]['eGender'];
           if($DriverFemaleOnlyReqAccept == "Yes" && $RiderGender == "Male"){
              $isRemoveDriverIntoList = "Yes";
           }
         }
         //echo "Driver Id >> ".$Datalist[$i]['iDriverId']." >> Remove From Driver Profile FemaleDriverRequest List >> ".$isRemoveDriverIntoList; echo "<br />";
         if($eType == "Ride"){
           $eFemaleDriverRequest=$data_booking[0]['eFemaleDriverRequest'];
           if($eFemaleDriverRequest == "" || $eFemaleDriverRequest == NULL){
              $eFemaleDriverRequest = "No";
           }     
           $DriverGender = $Datalist[$i]['eGender'];
           if($eFemaleDriverRequest == "Yes" && $DriverGender != "Female"){
              $isRemoveDriverIntoList = "Yes";
           }
         }
         //echo "Driver Id >> ".$Datalist[$i]['iDriverId']." >> Remove From Cabbooking FemaleDriverRequest List >> ".$isRemoveDriverIntoList; echo "<br />";
         $ePayType = $data_booking[0]['ePayType'];
         $ACCEPT_CASH_TRIPS = $Datalist[$i]['ACCEPT_CASH_TRIPS'];
         if($eType != "UberX"){
           if($ePayType == "Cash" && $ACCEPT_CASH_TRIPS == "No"){
              $isRemoveDriverIntoList = "Yes";
           }
         }
         //echo "Driver Id >> ".$Datalist[$i]['iDriverId']." >> For Ride,Delivery APP Type Remove From ACCEPT_CASH_TRIPS is No AND ePayType is Cash List >> ".$isRemoveDriverIntoList; echo "<br />";
         if($eType == "UberX"){
           $APP_PAYMENT_MODE = $generalobj->getConfigurations("configurations", "APP_PAYMENT_MODE");
           if($APP_PAYMENT_MODE == "Cash" && $ACCEPT_CASH_TRIPS == "No"){
              $isRemoveDriverIntoList = "Yes";
           }
         }
         //echo "Driver Id >> ".$Datalist[$i]['iDriverId']." >> For UberX APP Type Remove From ACCEPT_CASH_TRIPS is No AND APP_PAYMENT_MODE is Cash List >> ".$isRemoveDriverIntoList; echo "<br />";
         if($isRemoveDriverIntoList == "Yes"){
             unset($DatalistNewArr[$i]);
         } 
      }
      //echo "<pre>"; print_r(array_values($DatalistNewArr)); die;
      ### Checking For Female Driver Request ##
			// $Data = array();
			$driversActive = array();
      $driversActive = array_values($DatalistNewArr);
      $Data['DriverList']  = $driversActive; 
			//if(count($Data) > 0){
      if(count($driversActive) > 0){
				$iCabRequestId=get_value("cab_request_now", 'max(iCabRequestId)', "iUserId",$passengerId,'','true');
				$eStatus_cab=get_value("cab_request_now", 'eStatus', "iCabRequestId",$iCabRequestId,'','true');
				if($eStatus_cab == "Requesting"){
					$where1 = " iCabRequestId = '$iCabRequestId' ";
					$Data_update_cab['eStatus']="Cancelled";
					$id = $obj->MySQLQueryPerform("cab_request_now",$Data_update_cab,'update',$where1);
				}
				
				$Data_update_cab_now['iCabBookingId']=$data_booking[0]['iCabBookingId'];
				$Data_update_cab_now['fTollPrice']=$data_booking[0]['fTollPrice'];
				$Data_update_cab_now['vTollPriceCurrencyCode']=$data_booking[0]['vTollPriceCurrencyCode'];
				$Data_update_cab_now['eTollSkipped']=$data_booking[0]['eTollSkipped'];
				$Data_update_cab_now['iUserId']=$passengerId;
				$Data_update_cab_now['tMsgCode']=$messageArr['MsgCode'];
				$Data_update_cab_now['eStatus']='Requesting';
				$Data_update_cab_now['vSourceLatitude']=$vSourceLatitude;
				$Data_update_cab_now['vSourceLongitude']=$vSourceLongitude;
				$Data_update_cab_now['tSourceAddress']=$data_booking[0]['vSourceAddresss'];
				$Data_update_cab_now['vDestLatitude']=$vDestLatitude;
				$Data_update_cab_now['vDestLongitude']=$vDestLongitude;
				$Data_update_cab_now['tDestAddress']=$data_booking[0]['tDestAddress'];
				$Data_update_cab_now['iVehicleTypeId']=$data_booking[0]['iVehicleTypeId'];
				$Data_update_cab_now['fPickUpPrice']=$data_booking[0]['fPickUpPrice'];
				$Data_update_cab_now['fNightPrice']=$data_booking[0]['fNightPrice'];
				$Data_update_cab_now['eType']=$eType;
				$Data_update_cab_now['iPackageTypeId']= $eType == "Deliver"?$data_booking[0]['iPackageTypeId']:'';
				$Data_update_cab_now['vReceiverName']=$eType == "Deliver"?$data_booking[0]['vReceiverName']:'';
				$Data_update_cab_now['vReceiverMobile']=$eType == "Deliver"?$data_booking[0]['vReceiverMobile']:'';
				$Data_update_cab_now['tPickUpIns']=$eType == "Deliver"?$data_booking[0]['tPickUpIns']:'';
				$Data_update_cab_now['tDeliveryIns']=$eType == "Deliver"?$data_booking[0]['tDeliveryIns']:'';
				$Data_update_cab_now['tPackageDetails']=$eType == "Deliver"?$data_booking[0]['tPackageDetails']:'';
				$Data_update_cab_now['vCouponCode']=$data_booking[0]['vCouponCode'];
				$Data_update_cab_now['iQty']=$data_booking[0]['iQty'];
				$Data_update_cab_now['vRideCountry']=$data_booking[0]['vRideCountry'];
				$Data_update_cab_now['eFemaleDriverRequest']=$data_booking[0]['eFemaleDriverRequest'];
				$Data_update_cab_now['eHandiCapAccessibility']=$data_booking[0]['eHandiCapAccessibility'];
				$Data_update_cab_now['vTimeZone']=$data_booking[0]['vTimeZone'];
				$Data_update_cab_now['dAddedDate']=date("Y-m-d H:i:s");
				$Data_update_cab_now['eFromCronJob']="Yes";
				
				$insert_id = $obj->MySQLQueryPerform("cab_request_now",$Data_update_cab_now,'insert');
				$messageArr['iCabRequestId'] = strval($insert_id);
				
				/* tested Email 
				$to2 = "nirav.esw@gmail.com";
				$subject = "Email script from cubetaxi cron schedule ride new ".date('Y-m-d H:i:s');				
				$message = "";			
				$message .= "Email script from cubetaxi cron schedule ride new<br>";
				$header = "From:no-reply@webprojectsdemo.com \r\n";
				$header .= "MIME-Version: 1.0\r\n";
				$header .= "Content-type: text/html\r\n";
				$emailsend1 = mail ($to2,$subject,$message,$header);
				/* tested Email */
        
        $vLangCode = get_value('language_master', 'vCode', 'eDefault','Yes','','true');
		
    		$languageLabelsArr= getLanguageLabelsArr($vLangCode,"1");
    		$userwaitinglabel = $languageLabelsArr['LBL_TRIP_USER_WAITING'];
        $alertMsg = $userwaitinglabel; 
    		
				
				$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");
				$PUBNUB_PUBLISH_KEY = $generalobj->getConfigurations("configurations","PUBNUB_PUBLISH_KEY");
				$PUBNUB_SUBSCRIBE_KEY = $generalobj->getConfigurations("configurations","PUBNUB_SUBSCRIBE_KEY");
        $PUBNUB_DISABLED = $generalobj->getConfigurations("configurations","PUBNUB_DISABLED");
        if($PUBNUB_DISABLED == "Yes"){
           $ENABLE_PUBNUB = "No";
        }
        
        $alertSendAllowed = true;
        
        $message = json_encode($messageArr);
				$msg_encode  = json_encode($messageArr,JSON_UNESCAPED_UNICODE);
				
				if($ENABLE_PUBNUB == "Yes" && $PUBNUB_PUBLISH_KEY != "" && $PUBNUB_SUBSCRIBE_KEY != ""){
					//$pubnub = new Pubnub\Pubnub($PUBNUB_PUBLISH_KEY, $PUBNUB_SUBSCRIBE_KEY);
          $pubnub = new Pubnub\Pubnub(array("publish_key" => $PUBNUB_PUBLISH_KEY,"subscribe_key" => $PUBNUB_SUBSCRIBE_KEY, "uuid" => $uuid));
					$deviceTokens_arr_ios = array();
					$registation_ids_new = array();
					
					for($i=0;$i<count($driversActive); $i++){
						
						addToUserRequest($passengerId,$driversActive[$i]['iDriverId'],$msg_encode,$messageArr['MsgCode']);
						addToDriverRequest($driversActive[$i]['iDriverId'],$passengerId,0,"Timeout");
						
						/* For PubNub Setting */
						$iAppVersion=get_value("register_driver", 'iAppVersion', "iDriverId",$driversActive[$i]['iDriverId'],'','true');
						$eDeviceType=get_value("register_driver", 'eDeviceType', "iDriverId",$driversActive[$i]['iDriverId'],'','true');
						$vDeviceToken=get_value("register_driver", 'iGcmRegId', "iDriverId",$driversActive[$i]['iDriverId'],'','true');
						/* For PubNub Setting Finished */
						
						$channelName = "CAB_REQUEST_DRIVER_".$driversActive[$i]['iDriverId'];
						$info = $pubnub->publish($channelName, $msg_encode );
						 // echo "<pre>"; print_r($info); die;
						if($eDeviceType != "Android"){
							array_push($deviceTokens_arr_ios, $vDeviceToken);
						}
					}
					
					if(count($deviceTokens_arr_ios) > 0){
						sendApplePushNotification(1,$deviceTokens_arr_ios,"",$alertMsg,0);
					}
					
				}
        
        if($alertSendAllowed == true){
					$deviceTokens_arr_ios = array();
					$registation_ids_new = array();
					
					foreach ($driversActive as $item) {
						if($item['eDeviceType'] == "Android"){
							array_push($registation_ids_new, $item['iGcmRegId']);
						}else{
							array_push($deviceTokens_arr_ios, $item['iGcmRegId']);
						}
						addToUserRequest($passengerId,$item['iDriverId'],$msg_encode,$messageArr['MsgCode']);
						addToDriverRequest($item['iDriverId'],$passengerId,0,"Timeout");
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
			}else{
				//Email to admin for Not assigned Driver
				$message = array();
				$message['details'] = '<p>Dear Administrator,</p>
							<p>Driver is not available for the following manual booking in stage '.$iCronStage.'</p>
							<p>Name: '.$passengerName.',</p>
							<p>Contact Number: +'.$vPhoneCode.$vPhone.'</p>';
				$mail = $generalobj->send_email_user('CRON_BOOKING_EMAIL',$message);
				//Email to admin for Not assigned Driver
			}
		}
	}
?>