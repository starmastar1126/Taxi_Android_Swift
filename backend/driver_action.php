<?php 
	include_once('common.php');
	
	require_once(TPATH_CLASS . "/Imagecrop.class.php");
	$thumb = new thumbnail();
	$generalobj->check_member_login();
	$sql = "select * from country ORDER BY  vCountry ASC ";
	$db_country = $obj->MySQLSelect($sql);
	
	$sql="select * from  currency where eStatus='Active' ORDER BY vName ASC ";
    $db_currency=$obj->MySQLSelect($sql);
					
	if($_REQUEST['id'] != '' && $_SESSION['sess_iCompanyId'] != ''){
		
		$sql = "select * from register_driver where iDriverId = '".$_REQUEST['id']."' AND iCompanyId = '".$_SESSION['sess_iCompanyId']."'";
		$db_cmp_id = $obj->MySQLSelect($sql);
		
		if(!count($db_cmp_id) > 0) 
		{
			header("Location:driver.php?success=0&var_msg=".$langage_lbl['LBL_NOT_YOUR_DRIVER']);
		}
	}
	
	$var_msg = isset($_REQUEST["var_msg"]) ? $_REQUEST["var_msg"] : '';
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
	$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
	$action = ($id != '') ? 'Edit' : 'Add';
	$iCompanyId = $_SESSION['sess_iUserId'];
	$tbl_name = 'register_driver';
	$script = 'Driver';
	
	$sql = "select * from language_master where eStatus = 'Active' ORDER BY vTitle ASC";
	$db_lang = $obj->MySQLSelect($sql);
	
	$sql = "select * from company where eStatus != 'Deleted'";
	$db_company = $obj->MySQLSelect($sql);
	
	// set all variables with either post (when submit) either blank (when insert)
	$vName = isset($_POST['vName']) ? $_POST['vName'] : '';
	
	$vLastName = isset($_POST['vLastName']) ? $_POST['vLastName'] : '';
	$vEmail = isset($_POST['vEmail']) ? $_POST['vEmail'] : '';
	$vUserName = isset($_POST['vEmail']) ? $_POST['vEmail'] : '';
	$vPassword = isset($_POST['vPassword']) ? $_POST['vPassword'] : '';
	$vPhone = isset($_POST['vPhone']) ? $_POST['vPhone'] : '';
	$vCountry = isset($_POST['vCountry']) ? $_POST['vCountry'] : '';
	$vCaddress = isset($_POST['vCaddress']) ? $_POST['vCaddress'] : '';
	$vState = isset($_POST['vState']) ? $_POST['vState'] : '';
	$vCity = isset($_POST['vCity']) ? $_POST['vCity'] : '';
	$vZip = isset($_POST['vZip']) ? $_POST['vZip'] : '';
	$vCode = isset($_POST['vCode']) ? $_POST['vCode'] : '';
	$eStatus = isset($_POST['eStatus']) ? $_POST['eStatus'] : '';
	$vLang = isset($_POST['vLang']) ? $_POST['vLang'] : '';
	$vImage = isset($_POST['vImage']) ? $_POST['vImage'] : '';
	$vPass = ($vPassword != "") ? $generalobj->encrypt_bycrypt($vPassword) : '';
	$vCurrencyDriver = isset($_REQUEST['vCurrencyDriver']) ? $_REQUEST['vCurrencyDriver'] : '';
	$vPaymentEmail = isset($_REQUEST['vPaymentEmail']) ? $_REQUEST['vPaymentEmail'] : '';
	$vBankAccountHolderName = isset($_REQUEST['vBankAccountHolderName']) ? $_REQUEST['vBankAccountHolderName'] : '';
	$vAccountNumber = isset($_REQUEST['vAccountNumber']) ? $_REQUEST['vAccountNumber'] : '';
	$vBankLocation = isset($_REQUEST['vBankLocation']) ? $_REQUEST['vBankLocation'] : '';
	$vBankName = isset($_REQUEST['vBankName']) ? $_REQUEST['vBankName'] : '';
	$vBIC_SWIFT_Code = isset($_REQUEST['vBIC_SWIFT_Code']) ? $_REQUEST['vBIC_SWIFT_Code'] : '';
	//$dBirthDate="";
/*	if($_POST['vYear'] != "" && $_POST['vMonth'] != "" && $_POST['vDay'] != "") {
		$dBirthDate=$_POST['vYear'].'-'.$_POST['vMonth'].'-'.$_POST['vDay'];
	}*/
	
	if (isset($_POST['submit'])) {
		// if(SITE_TYPE=='Demo' && $action=='Edit')
		// {
			// header("Location:driver_action.php?id=" . $id . '&success=2');
			// exit;
		// }
		$iCompanyId = $_SESSION['sess_iUserId'];
		
		
		//Start :: Upload Image Script
		
		// echo "<pre>";print_r($_POST);exit;
		if(!empty($id)){
			
			if(isset($_FILES['vImage'])){
				$id = $_REQUEST['id'];
				$img_path = $tconfig["tsite_upload_images_driver_path"];
				$temp_gallery = $img_path . '/';
				$image_object = $_FILES['vImage']['tmp_name'];
				$image_name = $_FILES['vImage']['name'];
				$check_file_query = "select iDriverId,vImage from register_driver where iDriverId=" . $id;
				$check_file = $obj->sql_query($check_file_query);
				if ($image_name != "") {
					$check_file['vImage'] = $img_path . '/' . $id . '/' . $check_file[0]['vImage'];
					
					if ($check_file['vImage'] != '' && file_exists($check_file['vImage'])) {
						unlink($img_path . '/' . $id. '/' . $check_file[0]['vImage']);
						unlink($img_path . '/' . $id. '/1_' . $check_file[0]['vImage']);
						unlink($img_path . '/' . $id. '/2_' . $check_file[0]['vImage']);
						unlink($img_path . '/' . $id. '/3_' . $check_file[0]['vImage']);
					}
					
					$filecheck = basename($_FILES['vImage']['name']);
					$fileextarr = explode(".", $filecheck);
					$ext = strtolower($fileextarr[count($fileextarr) - 1]);
					$flag_error = 0;
					if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp") {
						$flag_error = 1;
						$var_msg = $langage_lbl['LBL_UPLOAD_IMG_ERROR'];
					}
					/*if ($_FILES['vImage']['size'] > 1048576) {
						$flag_error = 1;
						$var_msg = "Image Size is too Large";
					}*/
					if ($flag_error == 1) {
						$generalobj->getPostForm($_POST, $var_msg, "driver_action?success=0&var_msg=" . $var_msg);
						exit;
						} else {
						
						$Photo_Gallery_folder = $img_path . '/' . $id . '/';
						
						if (!is_dir($Photo_Gallery_folder)) {
							mkdir($Photo_Gallery_folder, 0777);
						}
						$img = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"], '', '', '', 'Y', '', $Photo_Gallery_folder);
						$vImage = $img;
					}
					}else{
                    $vImage = $check_file[0]['vImage'];
				}
				//die();
			}
		}
		//End :: Upload Image Script
		$vRefCodePara = '';
		$q = "INSERT INTO ";
		$where = '';
		if ($action == 'Edit') {
			//$str = ", eStatus = 'Inactive' ";
			$str ="";
		} else {
			
			if(SITE_TYPE=='Demo')
			{	
				$str = ", eStatus = 'active' ";
			}
			else
			{
				$sqlc = "select vValue from configurations where vName = 'DEFAULT_CURRENCY_CODE'";
				$db_currency = $obj->MySQLSelect($sqlc);				
				$defaultCurrency = $db_currency[0]['vValue'];
	
				$str = ", vCurrencyDriver = '$defaultCurrency'";
			}
			$eReftype = "Driver";
			$refercode = $generalobj->ganaraterefercode($eReftype);
			$dRefDate  = Date('Y-m-d H:i:s');
			$vRefCodePara = "`vRefCode` = '" . $refercode . "',";
		}
		if ($id != '') {
			$q = "UPDATE ";
			$where = " WHERE `iDriverId` = '" . $id . "'";
			
			$sql="select * from ".$tbl_name .$where;
			$edit_data=$obj->sql_query($sql);
			
			if($vEmail != $edit_data[0]['vEmail'])
			{
				$query = $q ." `".$tbl_name."` SET `eEmailVerified` = 'No' ".$where;
				$obj->sql_query($query);
			}
		//echo"<pre>";print_r($query);die;
			if($vPhone != $edit_data[0]['vPhone'])
			{
				$query = $q ." `".$tbl_name."` SET `ePhoneVerified` = 'No' ".$where;
				$obj->sql_query($query);
			}
			#echo"<pre>";print_r($query);
			if($vCode != $edit_data[0]['vCode'])
			{
				$query = $q ." `".$tbl_name."` SET `ePhoneVerified` = 'No' ".$where;
				$obj->sql_query($query);		
			}		
		}
		
		$passPara = '';
		if($vPass != ""){
			$passPara = "`vPassword` = '" . $vPass . "',";
		}

		if ($action == 'Add') {
            $str1 = "`tRegistrationDate` = '".date("Y-m-d H:i:s")."',";
        } else {
            $str1 = '';
        }

		 $query = $q . " `" . $tbl_name . "` SET
		`vName` = '" . $vName . "',
		`vLastName` = '" . $vLastName . "',
		`vCountry` = '" . $vCountry . "',
		`vCaddress` = '" . $vCaddress . "',
		`vState` = '" . $vState . "',
		`vCity` = '" . $vCity . "',
		`vZip` = '" . $vZip . "',
		`vCode` = '" . $vCode . "',
		`vEmail` = '" . $vEmail . "',
		`vLoginId` = '" . $vEmail . "',
		$passPara
		`iCompanyId` = '" . $iCompanyId . "',
		`vPhone` = '" . $vPhone . "',
		`vImage` = '" . $vImage . "',
		`vPaymentEmail` = '" . $vPaymentEmail . "',
		`vBankAccountHolderName` = '" . $vBankAccountHolderName . "',
		`vAccountNumber` = '" . $vAccountNumber . "',
		`vBankLocation` = '" . $vBankLocation . "',
		`vBankName` = '" . $vBankName . "',
		`vBIC_SWIFT_Code` = '" . $vBIC_SWIFT_Code . "',
		$vRefCodePara
		`dRefDate` = '" . $dRefDate . "',
		 $str1
		`vLang` = '" . $vLang . "' $str" . $where; 
		
		$obj->sql_query($query);
		

		$id = ($id != '') ? $id : $obj->GetInsertId();
		if ($action == "Add") {
           
			if($action == "Add") {
				if($SITE_VERSION == "v5"){
					$set_driver_pref = $generalobj->Insert_Default_Preferences($id);
				}
				
				if($APP_TYPE == 'UberX') {
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
						//$Drive_vehicle['vCarType'] = $result[0]['countId'];
						$iDriver_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle,'insert');
						$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$id."'";
						$obj->sql_query($sql);
						
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
						}*/
				}
			}
		
			if(isset($_FILES['vImage'])) {
                $img_path = $tconfig["tsite_upload_images_driver_path"];
                $temp_gallery = $img_path . '/';
                $image_object = $_FILES['vImage']['tmp_name'];
                $image_name = $_FILES['vImage']['name'];
                $check_file_query = "select iDriverId,vImage from register_driver where iDriverId=" . $id;
                $check_file = $obj->sql_query($check_file_query);
                if ($image_name != "") {
					$check_file['vImage'] = $img_path . '/' . $id . '/' . $check_file[0]['vImage'];
					
					if ($check_file['vImage'] != '' && file_exists($check_file['vImage'])) {
						unlink($img_path . '/' . $id. '/' . $check_file[0]['vImage']);
						unlink($img_path . '/' . $id. '/1_' . $check_file[0]['vImage']);
						unlink($img_path . '/' . $id. '/2_' . $check_file[0]['vImage']);
						unlink($img_path . '/' . $id. '/3_' . $check_file[0]['vImage']);
					}
					
					$filecheck = basename($_FILES['vImage']['name']);
					$fileextarr = explode(".", $filecheck);
					$ext = strtolower($fileextarr[count($fileextarr) - 1]);
					$flag_error = 0;
					if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp") {
						$flag_error = 1;
						$var_msg = $langage_lbl['LBL_UPLOAD_IMG_ERROR'];
					}
					/*if ($_FILES['vImage']['size'] > 1048576) {
						$flag_error = 1;
						$var_msg = "Image Size is too Large";
					}*/
					if ($flag_error == 1) {
						$generalobj->getPostForm($_POST, $var_msg, "driver_action?success=0&var_msg=" . $var_msg);
						exit;
						} else {
						
						$Photo_Gallery_folder = $img_path . '/' . $id . '/';
						if (!is_dir($Photo_Gallery_folder)) {
							mkdir($Photo_Gallery_folder, 0777);
						}
						$img = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"], '', '', '', 'Y', '', $Photo_Gallery_folder);
						$vImage = $img;
						
						$sql = "UPDATE ".$tbl_name." SET `vImage` = '" . $vImage . "' WHERE `iDriverId` = '" . $id . "'";
						$obj->sql_query($sql);
					}
				}
			}
		}
		$id = ($id != '') ? $id : $obj->GetInsertId();
		if($action== 'Edit')
		{
			$var_msg = $langage_lbl['LBL_Record_Updated_successfully.'];
		}
		else
		{
			$var_msg = $langage_lbl['LBL_RECORD_INSERT_MSG'];
		}
		
		$maildata['NAME'] =$vName;
		$maildata['EMAIL'] =  $vEmail;
		$maildata['PASSWORD'] =  $langage_lbl['LBL_PASSWORD']." : ".$vPassword;
		 $maildata['SOCIALNOTES'] = '';
		//$generalobj->send_email_user("MEMBER_REGISTRATION_USER",$maildata);
		if($_REQUEST['id'] == '') {
			$generalobj->send_email_user("DRIVER_REGISTRATION_ADMIN",$maildata);
			$generalobj->send_email_user("DRIVER_REGISTRATION_USER",$maildata);
		}
		header("Location:driver.php?id=" . $id . '&success=1&var_msg='.$var_msg);
		exit;
	}
	// for Edit
	
	if ($action == 'Edit') {
		$sql = "SELECT * FROM " . $tbl_name . " WHERE iDriverId = '" . $id . "'";
		$db_data = $obj->MySQLSelect($sql);		
		$vLabel = $id;
		if (count($db_data) > 0) {
			foreach ($db_data as $key => $value) {
				$vName = $value['vName'];
				$iCompanyId = $value['iCompanyId'];
				$vLastName = $generalobj->clearName(" ".$value['vLastName']);
				$vCountry = $value['vCountry'];
				$vCaddress = $value['vCaddress'];
				$vState = $value['vState'];
				$vCity = $value['vCity'];
				$vZip = $value['vZip'];
				$vCode = $value['vCode'];
				$vEmail = $generalobj->clearEmail($value['vEmail']);
				$vUserName = $value['vLoginId'];
				$vCurrencyDriver = $value['vCurrencyDriver'];
				$vPassword = $value['vPassword'];
				$vPhone = $generalobj->clearMobile($value['vPhone']);
				$vLang = $value['vLang'];
				$vImage = $value['vImage'];
				$vPaymentEmail = $value['vPaymentEmail'];
				$vBankAccountHolderName = $value['vBankAccountHolderName'];
				$vAccountNumber = $value['vAccountNumber'];
				$vBankLocation = $value['vBankLocation'];
				$vBankName = $value['vBankName'];
				$vBIC_SWIFT_Code = $value['vBIC_SWIFT_Code'];
			}
			
			$sql = "select iCountryId from country where 1=1 and eStatus = 'Active' AND vCountryCode='".$vCountry."'";
			$db_cntr = $obj->MySQLSelect($sql);
		
			$sql = "select iStateId, vState from state where 1=1 and eStatus = 'Active' and iCountryId = '".$db_cntr[0]['iCountryId']."'";
			$db_state = $obj->MySQLSelect($sql);
			
			$sql = "select iCityId, vcity from city where iStateId = '".$vState."' and eStatus = 'Active'";
			$db_city = $obj->MySQLSelect($sql);
		}
		
		if($SITE_VERSION == "v5"){
			$data_driver_pref = $generalobj->Get_User_Preferences($id);
		}
		// echo "<pre>";print_r($db_data);exit;
	}

if($action == 'Add'){
	$action_lbl = $langage_lbl['LBL_ACTION_ADD'];
} elseif($action == 'Edit') {
	$action_lbl = $langage_lbl['LBL_ACTION_EDIT'];
}
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_VEHICLE_DRIVER_TXT_ADMIN']; ?> <?= $action; ?></title>
		<!-- Default Top Script and css -->
		<?php  include_once("top/top_script.php");?>
		<!-- End: Default Top Script and css-->
	</head>
	<body>
		<!-- home page -->
		<div id="main-uber-page">
			<!-- Left Menu -->
			<?php  include_once("top/left_menu.php");?>
			<!-- End: Left Menu-->
			<!-- Top Menu -->
			<?php  include_once("top/header_topbar.php");?>
			<!-- End: Top Menu-->
			<!-- contact page-->
			<div class="page-contant ">
				<div class="page-contant-inner page-trip-detail">

					<h2 class="header-page trip-detail driver-detail1"><?= $action_lbl; ?> <?=$langage_lbl['LBL_VEHICLE_DRIVER_TXT_ADMIN']; ?> <?= $vName; ?>

					<a href="driverlist">
						<img src="assets/img/arrow-white.png" alt=""> <?=$langage_lbl['LBL_BACK_To_Listing']; ?>
					</a></h2>
					<!-- login in page -->
					<div class="driver-action-page">
						<?php  if ($success == 1) {?>
							<div class="alert alert-success alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								<?php  echo $langage_lbl['LBL_Record_Updated_successfully']; ?>
							</div>
							<?php }else if($success == 2){ ?>
							<div class="alert alert-danger alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								<?php  echo $langage_lbl['LBL_EDIT_DELETE_RECORD']; ?>
							</div>
							<?php  
							}
						?>
						<form id="frm1" method="post" class="companydriver" enctype="multipart/form-data">
							 <!-- onSubmit="return editPro('login')" -->
							<input  type="hidden" class="edit" name="action" value="login">
							<div id="hide-profile-div" class="row col-md-12">
								<?php  if($id){?>
									<?php  if ($vImage == 'NONE' || $vImage == '') { ?>
										<img src="assets/img/profile-user-img.png" alt="">
										<?php }else{?>
										<div class="col-lg-2">
                                        <b class="img-b"><img class="img-ipm1" src = "<?php  echo $tconfig["tsite_upload_images_driver"]. '/' .$id. '/3_' .$vImage ?>"/></b></div>
									<?php }?>
								<?php  }?>
								
								<?php  if($SITE_VERSION == "v5"){ ?>
								<div class="col-lg-5 col-vs">
                                 <fieldset class="col-md-12 field-a">
								<legend class="lable-b">	<h4 class="headind-a1"><?=$langage_lbl['LBL_PREFERENCES_TEXT']?>: </h4></legend>
									
										<div class="div-img1"> <?php  foreach($data_driver_pref as $val){?>
													<img data-toggle="tooltip" class="borderClass-aa1 border_class-bb1" title="<?=$val['pref_Title']?>" src="<?=$tconfig["tsite_upload_preference_image_panel"].$val['pref_Image']?>">
															<?php  } ?>
											</div>
											
											<span class="col-md-12 span-box"><a href="preferences.php?id=<?=$id?>&d_name=<?=$vName?>" id="show-edit-language-div" class="hide-language">
											<i class="fa fa-pencil" aria-hidden="true"></i>
											<?=$langage_lbl['LBL_MANAGE_PREFERENCES_TXT']?></a></span>
											
										   
								
									</fieldset>
									</div>
								<?php  } ?>
							</div>
							<div class="driver-action-page-right validation-form driver-action-new">
								<div class="row-a1">
									<div class="action-driv">
									<div class="col-md-6">
										<span>
											<label><?=$langage_lbl['LBL_YOUR_FIRST_NAME']; ?><span class="red">*</span></label>

											<input type="text" class="driver-action-page-input" name="vName"  id="vName" value="<?= $generalobj->cleanall(htmlspecialchars($vName)); ?>" placeholder="<?=$langage_lbl['LBL_YOUR_FIRST_NAME']; ?>" required>
											<div id="vName_validate"></div>
										</span> 
									</div>
									<div class="col-md-6">
										<span>
											<label><?=$langage_lbl['LBL_YOUR_LAST_NAME']; ?><span class="red">*</span></label>	

											<input type="text" class="driver-action-page-input" name="vLastName"  id="vLastName" value="<?= $generalobj->cleanall(htmlspecialchars($vLastName)); ?>" placeholder="<?=$langage_lbl['LBL_YOUR_LAST_NAME']; ?>" required>
											<div id="vLastName_validate"></div>
										</span> 
									</div>
									</div>
                                    <div class="action-driv">
									<div class="col-md-4">
										<span>
											<label><?=$langage_lbl['LBL_EMAIL_TEXT_SIGNUP']; ?><span class="red">*</span></label>

											<input type="email" class="driver-action-page-input " name="vEmail"  id="vEmail" value="<?= $vEmail; ?>" placeholder="<?=$langage_lbl['LBL_EMAIL_TEXT_SIGNUP']; ?>" required <?php   if(!empty($_REQUEST['id'])){?> readonly <?php  } ?>>
											<!--onChange="validate_email(this.value)"-->
											<div id="vEmail_validate"></div>
											<div style="float: none;margin-top: 14px;" id="emailCheck"></div>
										</span> 
									</div>
									<div class="col-md-4">
										<span>
											<label><?=$langage_lbl['LBL_PROFILE_RIDER_PASSWORD']; ?><span class="red">*</span></label>
											<input type="password" class="driver-action-page-input" name="vPassword"  id="vPassword" value="" placeholder="<?=$langage_lbl['LBL_COMPANY_DRIVER_PASSWORD']; ?>" pattern=".{6,}" title="Six or more characters" <?php  if ($action != 'Edit') { ?> required <?php  } ?>>
											<div id="vPassword_validate"></div>
										</span> 
									</div>
									<div class="col-md-4">
										<span>
											<label><?=$langage_lbl['LBL_SELECT_IMAGE']; ?></label>
											<input type="file" class="driver-action-page-input" name="vImage"  id="vImage" placeholder="Name Label" accept="image/*" onChange="validate_fileextension(this.value);">
										</span>
										<div class="fileerror error"></div> 
									</div>
									</div>
                                    <div class="action-driv">
								    <div class="col-md-4"> 
										<span>
											<label><?=$langage_lbl['LBL_COUNTRY_TXT']; ?><span class="red">*</span></label>
											<select class="custom-select-new" name = 'vCountry' onChange="changeCode(this.value);setState(this.value,'<?=$vState?>');" required>
												<option value=""><?= $langage_lbl['LBL_SELECT_TXT'] ?></option>
												<?php  for($i=0;$i<count($db_country);$i++){ ?>
													<option value = "<?= $db_country[$i]['vCountryCode'] ?>" <?php if($DEFAULT_COUNTRY_CODE_WEB == $db_country[$i]['vCountryCode'] && $action == 'Add') { ?> selected <?php  } else if($vCountry==$db_country[$i]['vCountryCode']){?>selected<?php  } ?>><?= $db_country[$i]['vCountry'] ?></option>
												<?php  } ?>
											</select>
											<div id="vCountry_validate"></div>
										</span>
									</div>
									<div class="col-md-4"> 
										<span>
											<label><?=$langage_lbl['LBL_STATE_TXT']; ?></label>
											<select class="custom-select-new" name = 'vState' id="vState" onChange="setCity(this.value,'<?=$vCity?>');">
												<option value=""><?= $langage_lbl['LBL_SELECT_TXT'] ?></option>
												<?php   for($i=0;$i<count($db_state);$i++){ ?>
													<option value = "<?= $db_state[$i]['iStateId'] ?>" <?php if($vState == $db_state[$i]['iStateId']) { ?> selected <?php  } ?>><?= $db_state[$i]['vState'] ?></option>
												<?php  } ?>
											</select>
										</span>
									</div>
									<div class="col-md-4"> 
										<span>
											<label><?=$langage_lbl['LBL_CITY_TXT']; ?></label>
											<select class="custom-select-new" name = 'vCity' id="vCity" >
												<option value=""><?= $langage_lbl['LBL_SELECT_TXT'] ?></option>
												<?php   for($i=0;$i<count($db_city);$i++){ ?>
													<option value = "<?= $db_city[$i]['iCityId'] ?>" <?php if($vCity == $db_city[$i]['iCityId']) { ?> selected <?php  } ?>><?= $db_city[$i]['vcity'] ?></option>
												<?php  } ?>
											</select>
										</span>
									</div>
                                    </div>
									</div>
									
									
									<div style="clear:both"> </div>
									<div class="row">
										<div class="action-driv"> 
											<div class="col-md-4">   
												<span class="driver-phone-number">
													<label><?=$langage_lbl['LBL_Phone_Number']; ?><span class="red">*</span></label>
													<input type="text" class="input-phNumber1" id="code" name="vCode" value="<?= $vCode ?>" readonly >

													<input name="vPhone" type="text" value="<?= $vPhone; ?>" class="driver-action-page-input input-phNumber2" placeholder="<?=$langage_lbl['LBL_Phone_Number']; ?>" " title="Please enter proper mobile number." required /> <!-- pattern="[0-9]{1,} -->
													<div id="vPhone_validate"></div>
												</span>
											</div>
											<div class="col-md-8"> 
												<span>
													<label><?=$langage_lbl['LBL_PROFILE_ADDRESS']; ?><span class="red">*</span></label>
													<input type="text" class="driver-action-page-input" name="vCaddress"  id="vCaddress" value="<?= $generalobj->cleanall(htmlspecialchars($vCaddress)); ?>" placeholder="<?=$langage_lbl['LBL_PROFILE_ADDRESS']; ?>" required>
													<div id="vCaddress_validate"></div>
												</span>
											</div>
										</div>
										<div class="action-driv">
											<div class="col-md-4"> 
												<span>
													<label><?=$langage_lbl['LBL_ZIP_CODE_SIGNUP']; ?><span class="red">*</span></label>
													<input type="text" class="driver-action-page-input" name="vZip"  id="vZip" value="<?= $vZip; ?>" placeholder="<?=$langage_lbl['LBL_ZIP_CODE_SIGNUP']; ?>" required>
													<div id="vZip_validate"></div>
												</span>
											</div>

											<div class="col-md-4">
												<span>       
													<label><?=$langage_lbl['LBL_PROFILE_SELECT_LANGUAGE']; ?><span class="red">*</span></label>                         
													<select  class="custom-select-new" name = 'vLang' required>

														<option value="">--<?=$langage_lbl['LBL_SELECT_LANGUAGE_TXT']; ?>--</option>

														<?php  for ($i = 0; $i < count($db_lang); $i++) { ?>
															<option value = "<?= $db_lang[$i]['vCode'] ?>" <?= ($db_lang[$i]['vCode'] == $vLang) ? 'selected' : ''; ?>><?= $db_lang[$i]['vTitle'] ?></option>
														<?php  } ?>
													</select>
													<div id="vLang_validate"></div>
												</span>
											</div>

											<div class="col-md-4">
												<span>
												<label><?=$langage_lbl['LBL_SELECT_CURRENCY_SIGNUP']; ?></label>
													<select class="custom-select-new" name = 'vCurrencyDriver'>
														<?php  for($i=0;$i<count($db_currency);$i++){ ?>
														<option value = "<?= $db_currency[$i]['vName'] ?>" <?php if($action == "Add" && $db_currency[$i]['eDefault']=="Yes"){?>selected<?php }else if($db_currency[$i]['vName'] == $vCurrencyDriver) { ?> selected <?php  } ?>>
														<?= $db_currency[$i]['vName'] ?>
														</option>
														<?php  } ?>
													</select>
												</span>
											</div>
										</div>
<!-- 									<?php  if($action == "Add"){?>
									<div class="col-md-6 driver-action1" style="margin-top: 25px;"><span>
										<b id="li_dob">
												<strong>
												<?=$langage_lbl['LBL_Date_of_Birth']; ?></strong>
												<select name="vDay" data="DD" class="custom-select-new" required oninvalid="this.setCustomValidity('Please Select Date')" onChange="setCustomValidity('')">
													<option value=""><?=$langage_lbl['LBL_DATE_SIGNUP']; ?></option>
													<?php  for($i=1;$i<=31;$i++) {?>
													<option value="<?=$i?>">
													<?=$i?>
													</option>
													<?php  }?>
												</select>
												<select data="MM" name="vMonth" class="custom-select-new" required oninvalid="this.setCustomValidity('Please Select Month')" onChange="setCustomValidity('')">
													<option value=""><?=$langage_lbl['LBL_MONTH_SIGNUP']; ?></option>
													<?php  for($i=1;$i<=12;$i++) {?>
													<option value="<?=$i?>">
													<?=$i?>
													</option>
													<?php  }?>
												</select>
												<select data="YYYY" name="vYear" class="custom-select-new" required oninvalid="this.setCustomValidity('Please Select Year')" onChange="setCustomValidity('')">
													<option value=""><?=$langage_lbl['LBL_YEAR_SIGNUP']; ?></option>
													 <?php  for($i=(date("Y")-1);$i >= ((date("Y")-1)-$BIRTH_YEAR_DIFFERENCE);$i--) {?>
													<option value="<?=$i?>">
													<?=$i?>
													</option>
													<?php  }?>
												</select>
											</b>
										</span></div>
									<?php  } ?> -->
									<div style="clear:both"></div>
									
									<div class="panel-group bank-detail1-main" style="margin-top:25px">
										<div class="panel panel-default">
										 <a data-toggle="collapse" href="#collapse1">
										  <div class="panel-heading bank-detail1">
											<h4 class="panel-title">Bank Details</h4>
										  </div></a>
										  <div id="collapse1" class="panel-collapse collapse in">
											<div class="panel-body">
												<div class="row">
													<div class="col-md-4">
														<span>
															<label><?=$langage_lbl['LBL_PAYMENT_EMAIL_TXT']; ?></label>
															<input type="email" class="driver-action-page-input" name="vPaymentEmail"  id="vPaymentEmail" value="<?= $vPaymentEmail; ?>" placeholder="<?=$langage_lbl['LBL_PAYMENT_EMAIL_TXT']; ?>" >
														</span>
													</div>
													<div class="col-md-4">
														<span>
															<label><?=$langage_lbl['LBL_ACCOUNT_HOLDER_NAME']; ?></label>
															<input type="text" class="driver-action-page-input" name="vBankAccountHolderName"  id="vBankAccountHolderName" value="<?= $vBankAccountHolderName; ?>" placeholder="<?=$langage_lbl['LBL_ACCOUNT_HOLDER_NAME']; ?>" >
														</span>
													</div>
													<div class="col-md-4">
														<span>
															<label><?=$langage_lbl['LBL_ACCOUNT_NUMBER']; ?></label>
															<input type="text" class="driver-action-page-input" name="vAccountNumber"  id="vAccountNumber" value="<?= $vAccountNumber; ?>" placeholder="<?=$langage_lbl['LBL_ACCOUNT_NUMBER']; ?>" >
														</span>
													</div>
												</div>
												<div class="row">
													<div class="col-md-4">
														<span>
															<label><?=$langage_lbl['LBL_NAME_OF_BANK']; ?></label>
															<input type="text" class="driver-action-page-input" name="vBankName"  id="vBankName" value="<?= $vBankName; ?>" placeholder="<?=$langage_lbl['LBL_NAME_OF_BANK']; ?>" >
														</span>
													</div>
													<div class="col-md-4">
														<span>
															<label><?=$langage_lbl['LBL_BANK_LOCATION']; ?></label>
															<input type="text" class="driver-action-page-input" name="vBankLocation"  id="vBankLocation" value="<?= $vBankLocation; ?>" placeholder="<?=$langage_lbl['LBL_BANK_LOCATION']; ?>" >
														</span>
													</div>
													<div class="col-md-4">
														<span>
															<label><?=$langage_lbl['LBL_BIC_SWIFT_CODE']; ?></label>
															<input type="text" class="driver-action-page-input" name="vBIC_SWIFT_Code"  id="vBIC_SWIFT_Code" value="<?= $vBIC_SWIFT_Code; ?>" placeholder="<?=$langage_lbl['LBL_BIC_SWIFT_CODE']; ?>" >
														</span>
													</div>
												</div>
											</div>
										  </div>
										</div>
									 </div>
									  
									<p>

									<input type="submit" class="save-but" name="submit" id="submit" value="<?= $action_lbl;?> <?=$langage_lbl['LBL_VEHICLE_DRIVER_TXT_ADMIN']; ?>">

									</p>
									<div style="clear:both;"></div>
								</div>  
							</div>                      
						</form>
					</div>
					<div style="clear:both;"></div>
				</div>
			</div>
			<!-- footer part -->
			<?php  include_once('footer/footer_home.php');?>
			<!-- footer part end -->
			<!-- End:contact page-->
			<div style="clear:both;"></div>
		</div>
		<!-- home page end-->
		<!-- Footer Script -->
		<?php  include_once('top/footer_script.php');
		$lang = get_langcode($_SESSION['sess_lang']);?>
		<style>
		span.help-block{
		margin:0;
		padding: 0;
		}
		</style>
		<script type="text/javascript" src="<?php  echo $tconfig["tsite_url_main_admin"]?>js/validation/jquery.validate.min.js" ></script>
		<?php  if($lang != 'en') { ?>
			<script type="text/javascript" src="assets/js/validation/localization/messages_<?= $lang; ?>.js" ></script>
		<?php  } ?>
		<script type="text/javascript" src="assets/js/validation/additional-methods.js" ></script>
		<script>
			function changeCode(id)
			{
				// alert('sasa');
				var request = $.ajax({
					type: "POST",
					url: 'change_code.php',
					data: 'id=' + id,
					success: function (data)
					{
						document.getElementById("code").value = data;
						//window.location = 'profile.php';
					}
				});
			}
/*			function validate_email(id)
			{
				var request = $.ajax({
					type: "POST",
					url: 'ajax_validate_email.php',
					data: 'id=' +id+'&usr=company',
					success: function (data)
					{
						if(data==0)
						{
							$('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> <?=$langage_lbl['LBL_EMAIL_EXISTS_MSG']; ?></i>');
							$('input[type="submit"]').attr('disabled','disabled');
						} else if(data==1) {
							var eml= /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
							result=eml.test(id);
							if(result==true)
							{
								$('#emailCheck').html('<i class="icon icon-ok alert-success alert"><?= $langage_lbl['LBL_VALID'] ?></i>');
								$('input[type="submit"]').removeAttr('disabled');
							}
						} else if(data=='deleted') {
							$('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> <?=$langage_lbl['LBL_CHECK_DELETE_ACCOUNT']; ?></i>');
							$('input[type="submit"]').attr('disabled','disabled');
						}
					}
				});
			}*/
		    function validate_fileextension(filename) {
		        var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
		        if ($.inArray(filename.split('.').pop().toLowerCase(), fileExtension) == -1) {
		            $( ".fileerror" ).html( "Only formats are allowed : "+fileExtension.join(', ') );
		            $('.save-but').prop("disabled", true);
		            return false;
		        } else {
		            $('.save-but').prop("disabled", false);
		            $( ".fileerror" ).html("");
		        }
		    } 
			$(document).ready(function(){
				$('[data-toggle="tooltip"]').tooltip();
			});
			
		function setState(id,selected)
		{
			// changeCode(id);
			// alert(selected);
			// if(id != selected){
				$("#vState + em").html('<?=$langage_lbl['LBL_SELECT_TXT']?>');
				$("#vCity + em").html('<?=$langage_lbl['LBL_SELECT_TXT']?>');
			// }
			var fromMod = 'driver';
			var request = $.ajax({
				type: "POST",
				url: 'change_stateCity.php',
				data: {countryId: id, selected: selected,fromMod:fromMod},
				success: function (dataHtml)
				{
					$("#vCity").html('<option value=""><?=$langage_lbl['LBL_SELECT_TXT']?></option>');
					$("#vState").html(dataHtml);
					if(selected == '')
						setCity('',selected);
				}
			});
		}
		
		/* $("select[name='vCountry']").change(function(){
			$("#vState + em").html('<?=$langage_lbl['LBL_SELECT_TXT']?>');
			// $("#vState").html('');
			$("#vCity + em").html('<?=$langage_lbl['LBL_SELECT_TXT']?>');
		})
		 */
		function setCity(id,selected)
		{
			// alert(selected);
			var fromMod = 'driver';
			var request = $.ajax({
				type: "POST",
				url: 'change_stateCity.php',
				data: {stateId: id, selected: selected,fromMod:fromMod},
				success: function (dataHtml)
				{
					$("#vCity").html(dataHtml);
				}
			});
		}
		 /*  <?php  if($action == "Edit"){ ?>
			var country = '<?=$vCountry?>';
			var state = '<?=$vState?>';
			var city = '<?=$vCity?>';
			
			// setState(country,state);
			// setCity(state,city);
		<?php  } ?>   */
		var errormessage;
		$('.companydriver').validate({
			ignore: 'input[type=hidden]',
			errorClass: 'help-block error',
			errorElement: 'span',
			errorPlacement: function (error, element) {
	            var name = $(element).attr("name");
	            error.appendTo($("#" + name + "_validate"));
	        },
			rules: {
				vEmail:{required: true, email: true,
					remote: {
						url: 'ajax_validate_email.php',
						type: "post",
						cache: false,
					    data: {
					    	id:function(e){
                                return $('#vEmail').val();
                            },
                            uid:'<?php  echo $id;?>',
	                        usr:'driver'
	                    },
	                    dataFilter: function(response) {
	                        //response = $.parseJSON(response);
	                        if (response == 'deleted')  {
	                            errormessage = "<?= $langage_lbl['LBL_CHECK_DELETE_ACCOUNT']; ?>";
	                            return false;
	                        } else if(response == 0){
	                            errormessage = "<?= $langage_lbl['LBL_EMAIL_EXISTS_MSG']; ?>";
	                            return false;
	                        } else {
	                            return true;
	                        }
	                    },
	                    async: false
					}
				},
				vPhone: {
					required: true,minlength: 3,digits: true,
					remote: {
							url: 'ajax_driver_mobile_new.php',
							type: "post",
							data: {iDriverId:'<?php  echo $id;?>',usertype:'driver'},
							async: false
							},
				}
			},
			messages: {
				vEmail: {remote: function(){ return errormessage; }},
				vPhone: {minlength: 'Please enter at least three Number.',digits: 'Please enter proper mobile number.',remote: '<?= $langage_lbl['LBL_PHONE_EXIST_MSG']; ?>'}
			}
			
		});
		</script>
		<!-- End: Footer Script -->
	</body>
</html>

