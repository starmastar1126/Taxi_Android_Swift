<!DOCTYPE html>
<html>
<head>
  <!--meta http-equiv="refresh" content="60"-->
</head> 
<body>
<?php 
	error_reporting(0);
	include_once('../common.php');
	include_once(TPATH_CLASS.'class.general.php');
	require_once('../assets/libraries/pubnub/autoloader.php');
	include_once(TPATH_CLASS.'configuration.php');
	include_once(TPATH_CLASS .'Imagecrop.class.php');
	include_once(TPATH_CLASS .'twilio/Services/Twilio.php');
	include_once('../generalFunctions.php');

	/* creating objects */
	$thumb 		= new thumbnail;
	$generalobj = new General();
	
	$ToDate = date('Y-m-d');
	$sql1 = "SELECT iCabBookingId,iCronStage,eAssigned,dBooking_date FROM cab_booking WHERE eStatus='Assign' AND dBooking_date LIKE '%$ToDate%' AND eAutoAssign = 'Yes' AND iCronStage != '3' AND eAssigned='No'";
	$data_bks = $obj->MySQLSelect($sql1);
// echo "<pre>"; print_r($data_bks); die;
	// echo $CRON_TIME; die;
	for($i=0;$i<count($data_bks);$i++){
		$FromDate = date('2017-06-06 13:38:36');
		
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
		
		if($data_bks[$i]['iCronStage'] == 1) {
			if($minutes <= 8 && $minutes >= 4) {
				sendRequest($data_bks[$i]['iCabBookingId']);
			}
		}
		
		if($data_bks[$i]['iCronStage'] == 2) {
			if($minutes <= 4 && $minutes >= 0) {
				sendRequest($data_bks[$i]['iCabBookingId']);
			}
		}
	}
	
	
	function sendRequest($cabId){
		global $generalobj, $obj;
		$sql = "SELECT cb.*, CONCAT(ru.vName,' ', ru.vLastName) as passengerName,ru.vFbId,ru.vImgName,ru.vAvgRating,ru.vPhoneCode,ru.vPhone FROM cab_booking as cb
		LEFT JOIN register_user as ru ON ru.iUserId = cb.iUserId
		WHERE cb.iCabBookingId='".$cabId."'";
		
		$data_booking = $obj->MySQLSelect($sql);
		
		//Update cron time in config
		// $where_config = " vName = 'CRON_TIME'";
		// $Data_config['vValue']= $ToDate;
		// $id = $obj->MySQLQueryPerform("configurations",$Data_config,'update',$where_config);
		//Update cron time in config
		// echo "<pre>"; print_r($data_booking);
		if(count($data_booking) > 0) {
		
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
			$messageArr['PACKAGE_TYPE'] = $eType == "Deliver"?get_value('package_type', 'vName', 'iPackageTypeId',$iPackageTypeId,'','true'):'';
			$messageArr['destLatitude'] = strval($vDestLatitude);
			$messageArr['destLongitude'] = strval($vDestLongitude);
			$messageArr['MsgCode'] = strval(mt_rand(1000, 9999));
			
			$where_cabid = " iCabBookingId = '".$data_booking[0]['iCabBookingId']."'";
			$Data_update['iCronStage']= $iCronStage+1;
			// $id = $obj->MySQLQueryPerform("cab_booking",$Data_update,'update',$where_cabid);
			$message = json_encode($messageArr);
			$msg_encode  = json_encode($messageArr,JSON_UNESCAPED_UNICODE);
			$Data = array();
			$Data = getOnlineDriverArr($vSourceLatitude,$vSourceLongitude);
			// echo "<pre>"; print_r($Data); die;
			if(count($Data) > 0){
				$ENABLE_PUBNUB = $generalobj->getConfigurations("configurations","ENABLE_PUBNUB");
				$PUBNUB_PUBLISH_KEY = $generalobj->getConfigurations("configurations","PUBNUB_PUBLISH_KEY");
				$PUBNUB_SUBSCRIBE_KEY = $generalobj->getConfigurations("configurations","PUBNUB_SUBSCRIBE_KEY");

				if($ENABLE_PUBNUB == "Yes"){
					$pubnub = new Pubnub\Pubnub($PUBNUB_PUBLISH_KEY, $PUBNUB_SUBSCRIBE_KEY);
					$deviceTokens_arr_ios = array();
					$registation_ids_new = array();
					
					for($i=0;$i<count($Data); $i++){
						
						addToUserRequest($passengerId,$Data[$i]['iDriverId'],$msg_encode,$messageArr['MsgCode']);
						addToDriverRequest($Data[$i]['iDriverId'],$passengerId,0,"Timeout");
						
						/* For PubNub Setting */
						$iAppVersion=get_value("register_driver", 'iAppVersion', "iDriverId",$Data[$i]['iDriverId'],'','true');
						$eDeviceType=get_value("register_driver", 'eDeviceType', "iDriverId",$Data[$i]['iDriverId'],'','true');
						$vDeviceToken=get_value("register_driver", 'iGcmRegId', "iDriverId",$Data[$i]['iDriverId'],'','true');
						/* For PubNub Setting Finished */
						
						$channelName = "CAB_REQUEST_DRIVER_".$Data[$i]['iDriverId'];
						$info = $pubnub->publish($channelName, $msg_encode );
						 // echo "<pre>"; print_r($info); die;
						if($eDeviceType != "Android"){
							array_push($deviceTokens_arr_ios, $vDeviceToken);
						}
					}
					
					if(count($deviceTokens_arr_ios) > 0){
						sendApplePushNotification(1,$deviceTokens_arr_ios,"",$alertMsg,0);
					}
					
				}else{
					$deviceTokens_arr_ios = array();
					$registation_ids_new = array();
					
					foreach ($Data as $item) {
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
</body>
</html>