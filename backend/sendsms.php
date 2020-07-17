<?php 
    include_once('common.php');
    include_once('include_taxi_webservices.php');
	include_once(TPATH_CLASS.'configuration.php');
	echo "send";exit;
	
	function getConfigurations($tabelName, $LABEL) {
			global $obj;

			$sql = "SELECT vValue FROM `" . $tabelName . "` WHERE vName='$LABEL'";
			$Data = $obj->MySQLSelect($sql);
			$Data_value = $Data[0]['vValue'];
			return $Data_value;
		}

	
function sendCode($mobileNo,$code,$fpass='code',$pass=''){
			global $site_path;
			// $mobileNo=$this->clearPhone($mobileNo);
			// $mobileNo=$code.$mobileNo;

			require_once(TPATH_CLASS .'twilio/Services/Twilio.php');

			$account_sid = getConfigurations("configurations","MOBILE_VERIFY_SID_TWILIO");
			$auth_token = getConfigurations("configurations","MOBILE_VERIFY_TOKEN_TWILIO");
			$twilioMobileNum= getConfigurations("configurations","MOBILE_NO_TWILIO");

			$client = new Services_Twilio($account_sid, $auth_token);

			$toMobileNum= "+".$mobileNo;
			if($fpass=="forgot"){
				$text_prefix_reset_pass = getConfigurations("configurations","PREFIX_PASS_RESET_SMS");
				// $verificationCode='Your Password is '.$this->decrypt($pass);
				$code=$this->decrypt($pass);
				$verificationCode=$text_prefix_reset_pass.' '.$code;
			}
			else{
				$text_prefix_verification_code = getConfigurations("configurations","PREFIX_VERIFICATION_CODE_SMS");
				$code=mt_rand(1000, 9999);
				$verificationCode = $text_prefix_verification_code .' '.$code;
			}
			// echo $client;exit;
			echo $twilioMobileNum;
			try{
		echo		$sms = $client->account->messages->sendMessage($twilioMobileNum,$toMobileNum,$verificationCode);
				$returnArr['action'] ="1";
				echo "<pre>";print_r($sms);exit;
			} catch (Services_Twilio_RestException $e) {
				$returnArr['action'] ="0";
				echo "<pre>";print_r($returnArr);exit;
			} 
			$returnArr['verificationCode'] =$code;
			return $returnArr;
		}


		sendCode("441422400666", "5489","code","");

		echo "send";exit;
?>
