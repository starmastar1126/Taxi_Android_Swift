<?php  
error_reporting(E_ALL);

ob_start();
// session_start();
include_once('common.php');

$userType = (isset($_REQUEST['userType'])) ? $_REQUEST['userType'] : '';
if(!isset($_SESSION['fb_user']) || $_SESSION['fb_user'] == '' || $_SESSION['fb_user'] != $userType) {
	$_SESSION['fb_user'] = $userType;
	$_SESSION['fb_user'] = ($_SESSION['fb_user'] == '') ? 'rider' : $_SESSION['fb_user'];
}

// Google App Details

define('GOOGLE_APP_NAME', $GOOGLE_PLUS_APP_NAME);
define('GOOGLE_OAUTH_CLIENT_ID', $GOOGLE_PLUS_OAUTH_CLIENT_ID);
define('GOOGLE_OAUTH_CLIENT_SECRET', $GOOGLE_PLUS_OAUTH_CLIENT_SECRET);
define('GOOGLE_OAUTH_REDIRECT_URI', $GOOGLE_PLUS_OAUTH_REDIRECT_URI);
define("GOOGLE_SITE_NAME", $GOOGLE_PLUS_SITE_NAME);

/*******Google ******/
require_once 'google_login/Google/src/config.php';
require_once 'google_login/Google/src/Google_Client.php';
require_once 'google_login/Google/src/contrib/Google_PlusService.php';
require_once 'google_login/Google/src/contrib/Google_Oauth2Service.php'; 

$client = new Google_Client();
$client->setScopes(array('https://www.googleapis.com/auth/plus.login','https://www.googleapis.com/auth/userinfo.email', 'https://www.googleapis.com/auth/plus.me'));
$client->setApprovalPrompt('auto');

$plus       = new Google_PlusService($client);
$oauth2     = new Google_Oauth2Service($client);
//unset($_SESSION['access_token']);

include_once($tconfig["tsite_libraries_v"]."/Imagecrop.class.php");
$thumb = new thumbnail();
$temp_gallery = $tconfig["tsite_temp_gallery"];

include_once($tconfig["tsite_libraries_v"]."/SimpleImage.class.php");
$img = new SimpleImage();  

if(isset($_GET['code'])) {
	$client->authenticate(); // Authenticate
	$_SESSION['access_token'] = $client->getAccessToken(); // get the access token here
	header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if(isset($_SESSION['access_token'])) {
	$client->setAccessToken($_SESSION['access_token']);
}

if ($client->getAccessToken()) {
	$_SESSION['access_token'] = $client->getAccessToken();
	$user = $oauth2->userinfo->get();
	try {
		// echo "<pre>";
		// print_r($user);
		// echo "</pre>";
		
		$fbid = $user['id'];
		$fbfirstname = $user['given_name'];
		$fblastname = $user['family_name'];
		$femail = $user['email'];
		$picture_img = $user['picture'];
		
		if($femail != '') {
			$sqll001 = " vEmail='".$femail."'";
		}else {
			$sqll001 = " vFbId = '".$fbid."' AND eSignUpType = 'Google'";
		}
		if($_SESSION['fb_user'] == 'rider') {
			if($femail != '' || $fbid != '') {
				$sql = "SELECT iUserId,vImgName,eGender,vPhone,eStatus FROM register_user WHERE $sqll001";
				$db_user = $obj->MySQLSelect($sql);
			}

			if(count($db_user) > 0){
				if($db_user[0]['eStatus'] == "Deleted" || $db_user[0]['eStatus'] == "Inactive"){
					if($db_user[0]['eStatus'] == "Deleted"){
						$_SESSION['sess_error_social'] = addslashes($langage_lbl['LBL_ACC_DELETE_TXT']);
					}else{
						$_SESSION['sess_error_social'] = addslashes($langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG']);
					}
					$link = $tconfig["tsite_url"]."rider-login";
					header("Location:".$link);exit;
				}
				
				$Photo_Gallery_folder =$tconfig["tsite_upload_images_passenger_path"]."/".$db_user[0]['iUserId']."/";
				
				unlink($Photo_Gallery_folder.$db_user[0]['vImgName']);
				unlink($Photo_Gallery_folder."1_".$db_user[0]['vImgName']);
				unlink($Photo_Gallery_folder."2_".$db_user[0]['vImgName']);
				unlink($Photo_Gallery_folder."3_".$db_user[0]['vImgName']);   
				unlink($Photo_Gallery_folder."4_".$db_user[0]['vImgName']);   
			
				if(!is_dir($Photo_Gallery_folder)) { 
					mkdir($Photo_Gallery_folder, 0777); 
				}
				$baseurl =  $picture_img;
				$url = $fbid.".jpg";
				$image_name = $generalobj->copyRemoteFile($baseurl,  $Photo_Gallery_folder.$url);
			
				if(is_file($Photo_Gallery_folder.$url)) {
			 
					list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$url);           
					if($width < $height){
						$final_width = $width;
					}else{
						$final_width = $height;
					}       
					$img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
					$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],""); 
				}

				$sql = "UPDATE register_user set vFbId='".$fbid."', vImgName='".$imgname."',eGender='".$db_user[0]['eGender']."',eSignUpType = 'Google' WHERE iUserId='".$db_user[0]['iUserId']."'";
				$obj->sql_query($sql); 				

				if(SITE_TYPE=='Demo'){
				  $login_sql = "insert into member_log (iMemberId, eMemberType, eMemberLoginType,vIP) VALUES ('".$db_user[0]['iUserId']."', 'Passenger', 'WebLogin','".$_SERVER['REMOTE_ADDR']."')";
				  $obj->sql_query($login_sql);
				}
				return $generalobj->Checkverification_mobile($db_user[0]['iUserId'],'rider');
				
			}else{
				$sql = "select * from currency where eDefault = 'Yes'";
				$db_curr = $obj->MySQLSelect($sql);
	
				$curr = $db_curr[0]['vName'];
	
				$sql = "select * from language_master where eDefault = 'Yes'";
				$db_lang = $obj->MySQLSelect($sql);
	
				$lang = $db_lang[0]['vCode'];
				$eReftype = "Rider";
				$refercode = $generalobj->ganaraterefercode($eReftype);
				$dRefDate  = Date('Y-m-d H:i:s');
				$tRegistrationDate	= Date('Y-m-d H:i:s');
				if($femail != "") {

					$sql = "insert INTO register_user (vFbId,vName, vLastName, vEmail, eStatus,vImgName,eGender,vLang,vCurrencyPassenger,vRefCode,dRefDate,tRegistrationDate) VALUES ('".$fbid."','".$fbfirstname."', '".$fblastname."', '".$femail."', 'Active','','".$fbgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."','".$tRegistrationDate."')";
					$iUserId =$obj->MySQLInsert($sql);
				} else {
					$sql = "INSERT INTO register_user (vFbId,vName, vLastName, vEmail, eStatus,vImgName,eGender,vLang,vCurrencyPassenger,vRefCode,dRefDate,tRegistrationDate) VALUES ('".$fbid."','', '".$fbfirstname."', '".$fblastname."', '".$femail."','Active','".$fbgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."','".$tRegistrationDate."')";
					$iUserId =  $obj->MySQLInsert($sql);
				}				
				
				$db_sql = "select * from register_user WHERE iUserId='".$iUserId."'";
				$db_user = $obj->MySQLSelect($db_sql);	
				
				$type= base64_encode(base64_encode('rider'));	
				$id = $generalobj->encrypt($iUserId);
				$newToken = $generalobj->RandomString(32);
				$url = $tconfig["tsite_url"].'reset_password.php?type='.$type.'&id='.$id.'&_token='.$newToken;
				
				$maildata['EMAIL'] = $femail;
				$maildata['NAME'] = $fbfirstname." ".$fblastname;
				$maildata['PASSWORD'] = '';
				$maildata['SOCIALNOTES'] = $langage_lbl['LBL_SOCIAL_MEDIA_NOTES1_TXT'].'<br>'.$url.'<br>'.$langage_lbl['LBL_SOCIAL_MEDIA_NOTES2_TXT'];
				$generalobj->send_email_user("MEMBER_REGISTRATION_USER",$maildata);

				$Photo_Gallery_folder = $tconfig["tsite_upload_images_passenger_path"]."/". $iUserId . '/';
			   
				@unlink($Photo_Gallery_folder.$db_user[0]['vImgName']);
				@unlink($Photo_Gallery_folder."1_".$db_user[0]['vImgName']);
				@unlink($Photo_Gallery_folder."2_".$db_user[0]['vImgName']);
				@unlink($Photo_Gallery_folder."3_".$db_user[0]['vImgName']);   
				@unlink($Photo_Gallery_folder."4_".$db_user[0]['vImgName']);   
		
				if(!is_dir($Photo_Gallery_folder))
				{
					mkdir($Photo_Gallery_folder, 0777);
				}
	  
				$baseurl =  $picture_img;
				$url = $fbid.".jpg";
				$image_name = $generalobj->copyRemoteFile($baseurl,  $Photo_Gallery_folder.$url);
			  
				if(is_file($Photo_Gallery_folder.$url)) {
				 
					list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$url);           
					if($width < $height){
						$final_width = $width;
					}else{
						$final_width = $height;
					}       
					$img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
					$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],""); 
				}  
				 
				$sql = "UPDATE register_user set  vImgName='".$imgname."',eSignUpType = 'Google' WHERE iUserId='".$iUserId."'";
				$obj->sql_query($sql); 
				
				return $generalobj->Checkverification_mobile($db_user[0]['iUserId'],'rider');
			}
		
		}else {
			
			if($femail != '' || $fbid != '') {
				$sql = "SELECT iDriverId,vImage,eGender,vPhone,eStatus FROM register_driver WHERE $sqll001 ";
				$db_user = $obj->MySQLSelect($sql);
			}
			
			if(count($db_user) > 0){			
				if($db_user[0]['eStatus'] == "Deleted"){
					$_SESSION['sess_error_social'] = addslashes($langage_lbl['LBL_ACC_DELETE_TXT']);
					$link = $tconfig["tsite_url"]."driver-login";
					header("Location:".$link);exit;
				}
					
				$Photo_Gallery_folder =$tconfig["tsite_upload_images_driver_path"]."/".$db_user[0]['iDriverId']."/";
				
				unlink($Photo_Gallery_folder.$db_user[0]['vImage']);
				unlink($Photo_Gallery_folder."1_".$db_user[0]['vImage']);
				unlink($Photo_Gallery_folder."2_".$db_user[0]['vImage']);
				unlink($Photo_Gallery_folder."3_".$db_user[0]['vImage']);   
				unlink($Photo_Gallery_folder."4_".$db_user[0]['vImage']);   
			
				if(!is_dir($Photo_Gallery_folder)) { 
					mkdir($Photo_Gallery_folder, 0777); 
				}
				$baseurl =  $picture_img;
				$url = $fbid.".jpg";
				$image_name = $generalobj->copyRemoteFile($baseurl,  $Photo_Gallery_folder.$url);
			
				if(is_file($Photo_Gallery_folder.$url)) {
			 
					list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$url);           
					if($width < $height){
						$final_width = $width;
					}else{
						$final_width = $height;
					}       
					$img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
					$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],""); 
				}

				$sql = "UPDATE register_driver set vFbId='".$fbid."', vImage='".$imgname."',eGender='".$db_user[0]['eGender']."',eSignUpType = 'Google' WHERE iDriverId='".$db_user[0]['iDriverId']."'";
				$obj->sql_query($sql); 				 

				if(SITE_TYPE=='Demo'){
				  $login_sql = "insert into member_log (iMemberId, eMemberType, eMemberLoginType,vIP) VALUES ('".$db_user[0]['iDriverId']."', 'Passenger', 'WebLogin','".$_SERVER['REMOTE_ADDR']."')";
				  $obj->sql_query($login_sql);
				}
				return $generalobj->Checkverification_mobile($db_user[0]['iDriverId'],'driver');
			} else {

				$sql = "select * from currency where eDefault = 'Yes'";
				$db_curr = $obj->MySQLSelect($sql);
				$curr = $db_curr[0]['vName'];
				
				$sql = "select * from language_master where eDefault = 'Yes'";
				$db_lang = $obj->MySQLSelect($sql);
				
				$lang = $db_lang[0]['vCode'];
				$eReftype = "Driver";
				$refercode = $generalobj->ganaraterefercode($eReftype);
				$dRefDate  = Date('Y-m-d H:i:s');
				$tRegistrationDate	= Date('Y-m-d H:i:s');
				if($femail != "") {
					$sql = "INSERT INTO register_driver (vFbId,vName, vLastName, vEmail, iCompanyId, eStatus,vImage,eGender,vLang,vCurrencyDriver,vRefCode,dRefDate,tRegistrationDate) VALUES ('".$fbid."','".$fbfirstname."', '".$fblastname."', '".$femail."', '1', 'Inactive','','".$fbgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."','".$tRegistrationDate."')";
					$iDriverId =$obj->MySQLInsert($sql);
				} else {
					$sql = "INSERT INTO register_driver (vFbId,vName, vLastName, vEmail, iCompanyId, eStatus,vImage,eGender,vLang,vCurrencyDriver,vRefCode,dRefDate,tRegistrationDate) VALUES ('".$fbid."','".$fbfirstname."', '".$fblastname."', '".$femail."', '1', 'Inactive','','".$fbgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."','".$tRegistrationDate."')";
					$iDriverId =  $obj->MySQLInsert($sql);
				}
				
				if($APP_TYPE == 'UberX') {
					$query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type`";
					$result = $obj->MySQLSelect($query);
					
					$Drive_vehicle['iDriverId'] = $iDriverId;
					$Drive_vehicle['iCompanyId'] = "1";
					$Drive_vehicle['iMakeId'] = "3";
					$Drive_vehicle['iModelId'] = "1";
					$Drive_vehicle['iYear'] = Date('Y');
					$Drive_vehicle['vLicencePlate'] = "My Services";
					$Drive_vehicle['eStatus'] = "Active";
					$Drive_vehicle['eCarX'] = "Yes";
					$Drive_vehicle['eCarGo'] = "Yes";		
					$Drive_vehicle['vCarType'] = $result[0]['countId'];
					$iDriver_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle,'insert');
					$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$iDriverId."'";
					$obj->sql_query($sql);
					
					//if($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes"){
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
					//}
				} else {
					if(SITE_TYPE=='Demo')
					{
						$query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type`";
						$result = $obj->MySQLSelect($query);
						$Drive_vehicle['iDriverId'] = $iDriverId;
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
						$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$iDriverId."'";
						$obj->sql_query($sql);
					}		
				}			
				
				$db_sql = "select * from register_driver WHERE iDriverId='".$iDriverId."'";
				$db_user = $obj->MySQLSelect($db_sql);	
				
				$type= base64_encode(base64_encode('driver'));	
				$id = $generalobj->encrypt($iDriverId);
				$newToken = $generalobj->RandomString(32);
				$url = $tconfig["tsite_url"].'reset_password.php?type='.$type.'&id='.$id.'&_token='.$newToken;					
				
				$maildata['EMAIL'] = $femail;
				$maildata['NAME'] = $fbfirstname." ".$fblastname;
				$maildata['PASSWORD'] = '';
				$maildata['SOCIALNOTES'] = $langage_lbl['LBL_SOCIAL_MEDIA_NOTES1_TXT'].'<br>'.$url.'<br>'.$langage_lbl['LBL_SOCIAL_MEDIA_NOTES2_TXT'];	
				
				$generalobj->send_email_user("DRIVER_REGISTRATION_USER",$maildata);

				$Photo_Gallery_folder = $tconfig["tsite_upload_images_driver_path"]."/". $iDriverId . '/';
			   
				@unlink($Photo_Gallery_folder.$db_user[0]['vImage']);
				@unlink($Photo_Gallery_folder."1_".$db_user[0]['vImage']);
				@unlink($Photo_Gallery_folder."2_".$db_user[0]['vImage']);
				@unlink($Photo_Gallery_folder."3_".$db_user[0]['vImage']);   
				@unlink($Photo_Gallery_folder."4_".$db_user[0]['vImage']);   
		
				if(!is_dir($Photo_Gallery_folder))
				{
					mkdir($Photo_Gallery_folder, 0777);
				}
	  
				$baseurl =  $picture_img;
				$url = $fbid.".jpg";
				$image_name = $generalobj->copyRemoteFile($baseurl,  $Photo_Gallery_folder.$url);
			  
				if(is_file($Photo_Gallery_folder.$url)) {
				 
					list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$url);           
					if($width < $height){
						$final_width = $width;
					} else {
						$final_width = $height;
					}       
					$img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
					$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],""); 
				}  
				 
				$sql = "UPDATE register_driver set vImage='".$imgname."',eSignUpType = 'Google' WHERE iDriverId='".$iDriverId."'";
				$obj->sql_query($sql); 
				
				return $generalobj->Checkverification_mobile($db_user[0]['iDriverId'],'driver');
			}
		}
	}catch (Exception $e) {
		$error = $e->getMessage();
		echo $error; exit;
		// echo "<pre>";
		// print_r($error);
		// echo "</pre>";
	}
}else {
	$url = $client->createAuthUrl();
	header("Location:".$url);
}
?>