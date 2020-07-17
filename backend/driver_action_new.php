<?php 
	include_once('common.php');
	
	require_once(TPATH_CLASS . "/Imagecrop.class.php");
	$thumb = new thumbnail();
	$generalobj->check_member_login();
	$sql = "select * from country";
	$db_country = $obj->MySQLSelect($sql);
	
	$sql="select * from  currency where eStatus='Active'";
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
	
	//echo '<prE>'; print_R($_REQUEST); echo '</pre>';
	// set all variables with either post (when submit) either blank (when insert)
	$vName = isset($_POST['vName']) ? $_POST['vName'] : '';
	
	$vLastName = isset($_POST['vLastName']) ? $_POST['vLastName'] : '';
	$vEmail = isset($_POST['vEmail']) ? $_POST['vEmail'] : '';
	$vUserName = isset($_POST['vEmail']) ? $_POST['vEmail'] : '';
	$vPassword = isset($_POST['vPassword']) ? $_POST['vPassword'] : '';
	$vPhone = isset($_POST['vPhone']) ? $_POST['vPhone'] : '';
	$vCountry = isset($_POST['vCountry']) ? $_POST['vCountry'] : '';
	$vCode = isset($_POST['vCode']) ? $_POST['vCode'] : '';
	$eStatus = isset($_POST['eStatus']) ? $_POST['eStatus'] : '';
	$vLang = isset($_POST['vLang']) ? $_POST['vLang'] : '';
	$vImage = isset($_POST['vImage']) ? $_POST['vImage'] : '';
	$vPass = ($vPassword != "") ? $generalobj->encrypt_bycrypt($vPassword) : '';
	$vCurrencyDriver = isset($_REQUEST['vCurrencyDriver']) ? $_REQUEST['vCurrencyDriver'] : '';
	$dBirthDate="";
	if($_POST['vYear'] != "" && $_POST['vMonth'] != "" && $_POST['vDay'] != "") {
		$dBirthDate=$_POST['vYear'].'-'.$_POST['vMonth'].'-'.$_POST['vDay'];
	}
	
	if (isset($_POST['submit'])) {
		// if(SITE_TYPE=='Demo' && $action=='Edit')
		// {
			// header("Location:driver_action.php?id=" . $id . '&success=2');
			// exit;
		// }
		$iCompanyId = $_SESSION['sess_iUserId'];
		
		echo "ss";exit;
		//Start :: Upload Image Script
		
		
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
						$var_msg = "Not valid image extension of .jpg, .jpeg, .gif, .png";
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
			$str = ", eStatus = 'Inactive' ";
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
	
				$str = ", vCurrencyDriver = '$defaultCurrency',dBirthDate ='$dBirthDate'";
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
			#echo"<pre>";print_r($query);
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

		 $query = $q . " `" . $tbl_name . "` SET
		`vName` = '" . $vName . "',
		`vLastName` = '" . $vLastName . "',
		`vCountry` = '" . $vCountry . "',
		`vCode` = '" . $vCode . "',
		`vEmail` = '" . $vEmail . "',
		`vLoginId` = '" . $vEmail . "',
		$passPara
		`iCompanyId` = '" . $iCompanyId . "',
		`vPhone` = '" . $vPhone . "',
		`vImage` = '" . $vImage . "',
		$vRefCodePara
		`dRefDate` = '" . $dRefDate . "',
		`vLang` = '" . $vLang . "' $str" . $where; 
		
		$obj->sql_query($query);
		
		if ($obj->GetInsertId() != '') {
            $id = $obj->GetInsertId();
			if($action == "Add"){
				if($SITE_VERSION == "v5"){
					$set_driver_pref = $generalobj->Insert_Default_Preferences($id);
				}
				
				if($APP_TYPE == 'UberX'){
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
						$Drive_vehicle['vCarType'] = $result[0]['countId'];
						$iDriver_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle,'insert');
						$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$id."'";
						$obj->sql_query($sql);
						
						if($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes"){
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
						}
				}
			}
		
			if(isset($_FILES['vImage'])){
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
						$var_msg = "Not valid image extension of .jpg, .jpeg, .gif, .png";
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
			$var_msg="Record Updated successfully";
		}
		else
		{
			$var_msg="Record inserted successfully";
		}
		
		$maildata['NAME'] =$vName;
		$maildata['EMAIL'] =  $vEmail;
		$maildata['PASSWORD'] = $langage_lbl['LBL_PASSWORD'] .": ". $vPassword;
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
				$vCode = $value['vCode'];
				$vEmail = $generalobj->clearEmail($value['vEmail']);
				$vUserName = $value['vLoginId'];
				$vCurrencyDriver = $value['vCurrencyDriver'];
				$vPassword = $value['vPassword'];
				$vPhone = $generalobj->clearMobile($value['vPhone']);
				$vLang = $value['vLang'];
				$vImage = $value['vImage'];
			}
		}
		
		if($SITE_VERSION == "v5"){
			$data_driver_pref = $generalobj->Get_User_Preferences($id);
		}
		// echo "<pre>";print_r($data_driver_pref);exit;
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
					<h2 class="header-page trip-detail driver-detail1"><?= $action; ?> <?=$langage_lbl['LBL_VEHICLE_DRIVER_TXT_ADMIN']; ?> <?= $vName; ?>
					<a href="driverlist">
						<img src="assets/img/arrow-white.png" alt=""> <?=$langage_lbl['LBL_BACK_To_Listing']; ?>
					</a></h2>
					<!-- login in page -->
					<div class="driver-action-page">
						<?php  if ($success == 1) {?>
							<div class="alert alert-success alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								Record Updated successfully.
							</div>
							<?php }else if($success == 2){ ?>
							<div class="alert alert-danger alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								"Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
							</div>
							<?php  
							}
						?>
						<form id="frm1" method="post" onSubmit="return editPro('login')" enctype="multipart/form-data">
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
								
								<?php  if($SITE_VERSION == "v5" && $action == "Edit"){ ?>
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
							<div class="driver-action-page-right validation-form">
								<div class="row-a1">
									<div class="col-md-6">
										<span class="newrow">
											<strong>
											<label><?=$langage_lbl['LBL_YOUR_FIRST_NAME']; ?><span class="red">*</span></label>
											<input type="text" class="driver-action-page-input" name="vName"  id="vName" value="<?= $generalobj->cleanall(htmlspecialchars($vName)); ?>" placeholder="First Name" >
											</strong>
										</span> 
									</div>
									<div class="col-md-6">
										<span class="newrow">
											<strong>
											<label><?=$langage_lbl['LBL_YOUR_LAST_NAME']; ?><span class="red">*</span></label>	
											<input type="text" class="driver-action-page-input" name="vLastName"  id="vLastName" value="<?= $generalobj->cleanall(htmlspecialchars($vLastName)); ?>" placeholder="Last Name" >
											</strong>
										</span> 
									</div>
									<div class="col-md-6">
										<span class="newrow">
											<strong>
											<label><?=$langage_lbl['LBL_EMAIL_TEXT_SIGNUP']; ?><span class="red">*</span></label>
											<input type="email" class="driver-action-page-input " name="vEmail"  id="vEmail" value="<?= $vEmail; ?>" placeholder="Email"  <?php   if(!empty($_REQUEST['id'])){?> readonly <?php  } ?>>
											<div style="float: none;margin-top: 14px;" id="emailCheck"></div>
											</strong>
										</span> 
									</div>
									<div class="col-md-6">
										<span >
											<strong>
											<label><?=$langage_lbl['LBL_SELECT_IMAGE']; ?></label>
											<input type="file" class="driver-action-page-input" name="vImage"  id="vImage" placeholder="Name Label">
											</strong>
										</span> 
									</div>
									<div class="col-md-6"> 
										<span class="newrow">
										<strong>
											<label><?=$langage_lbl['LBL_SELECT_CONTRY']; ?><span class="red">*</span></label>
											<select class="custom-select-new newrow" name = 'vCountry' onChange="changeCode(this.value);" >
												<option value="">--Select Country--</option>
												<?php  for($i=0;$i<count($db_country);$i++){ ?>
													<option value = "<?= $db_country[$i]['vCountryCode'] ?>" <?php if($DEFAULT_COUNTRY_CODE_WEB == $db_country[$i]['vCountryCode'] && $action == 'Add') { ?> selected <?php  } else if($vCountry==$db_country[$i]['vCountryCode']){?>selected<?php  } ?>><?= $db_country[$i]['vCountry'] ?></option>
												<?php  } ?>
											</select>
											</strong>
										</span>
									</div>
									<div class="col-md-6">   
										<span class="driver-phone-number newrow">
										<strong>
											<label><?=$langage_lbl['LBL_Phone_Number']; ?><span class="red">*</span></label>
											<input type="text" class="input-phNumber1" id="code" name="vCode" value="<?= $vCode ?>" readonly >
											<input name="vPhone" type="text" value="<?= $vPhone; ?>" class="driver-action-page-input input-phNumber2" placeholder="Phone Number"  />
											</strong>
										</span>
									</div>
									<div class="col-md-6">
										<span class="newrow">      
											<strong>
											<label><?=$langage_lbl['LBL_PROFILE_SELECT_LANGUAGE']; ?><span class="red">*</span></label>                         
											<select  class="custom-select-new" name = 'vLang' >
												<option value="">--Select Language--</option>
												<?php  for ($i = 0; $i < count($db_lang); $i++) { ?>
													<option value = "<?= $db_lang[$i]['vCode'] ?>" <?= ($db_lang[$i]['vCode'] == $vLang) ? 'selected' : ''; ?>><?= $db_lang[$i]['vTitle'] ?></option>
												<?php  } ?>
											</select>
											</strong>
										</span>
									</div>
									<div class="col-md-6">
										<span class="newrow">
										<strong>
											<label><?=$langage_lbl['LBL_PROFILE_RIDER_PASSWORD']; ?><span class="red">*</span></label>
											<input type="password" class="driver-action-page-input" name="vPassword"  id="vPassword" value="" placeholder="<?=$langage_lbl['LBL_COMPANY_DRIVER_PASSWORD']; ?>" <?php  if ($action != 'Edit') { ?>  <?php  } ?>>
											</strong>
										</span> 
									</div>
									
									<div class="col-md-6">
										<span class="newrow">
										<strong>
										<label><?=$langage_lbl['LBL_SELECT_CURRENCY_SIGNUP']; ?><span class="red">*</span></label>
											<select class="custom-select-new" name = 'vCurrencyDriver'>
												<?php  for($i=0;$i<count($db_currency);$i++){ ?>
												<option value = "<?= $db_currency[$i]['vName'] ?>" <?php if($action == "Add" && $db_currency[$i]['eDefault']=="Yes"){?>selected<?php }else if($db_currency[$i]['vName'] == $vCurrencyDriver) { ?> selected <?php  } ?>>
												<?= $db_currency[$i]['vName'] ?>
												</option>
												<?php  } ?>
											</select>
											</strong>
										</span>
									</div>
									
									<?php  if($action == "Add"){?>
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
												 <?php  for($i=(date("Y")-$START_BIRTH_YEAR_DIFFERENCE);$i >= ((date("Y")-1)-$BIRTH_YEAR_DIFFERENCE);$i--) {?>
												<option value="<?=$i?>">
												<?=$i?>
												</option>
												<?php  }?>
											</select>
										</b>
									</span></div>
								<?php  } ?>
									
									<p>
										<input type="submit" class="save-but" name="submit" id="submit" value="<?= $action; ?> Driver">
										
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
		<?php  include_once('top/footer_script.php');?>
		<script type="text/javascript" src="assets/js/validation/jquery.validate.min.js" ></script>
		<script type="text/javascript" src="assets/js/validation/additional-methods.js" ></script>
		<script>
		$('#frm1').validate({
		ignore: 'input[type=hidden]',
		errorClass: 'help-block',
		errorElement: 'span',
		errorPlacement: function (error, e) {
			e.parents('.newrow > strong').append(error);
		},
		highlight: function (e) {
			$(e).closest('.newrow').removeClass('has-success has-error').addClass('has-error');
			$(e).closest('.newrow strong input').addClass('has-shadow-error');
			$(e).closest('.help-block').remove();
		},
		success: function (e) {
			e.prev('input').removeClass('has-shadow-error');
			e.closest('.newrow').removeClass('has-success has-error');
			e.closest('.help-block').remove();
			e.closest('.help-inline').remove();
		},
		rules: {
			vName: {required: true, minlength: 2,maxlength:30},
			vLastName: {required: true, minlength: 2,maxlength:30},
			vLang: {required: true},
			vPassword: {required: true,noSpace: true, minlength: 6},
			vEmail: {required: true, email: true,
					remote: {
							url: 'ajax_validate_email_new.php',
							type: "post",
							data: {iDriverId: ''},
						}
			},
			vPhone: {required: true, phonevalidate: true,
						remote: {
							url: 'ajax_driver_mobile_new.php',
							type: "post",
							data: {iDriverId: ''},
						}
			},
		},
		messages: {
			vEmail: {remote: 'Email address is already exists.'},
			vPhone: {remote: 'Phone Number is already exists.'},
		}
	});
		
		
			function changeCode(id)
			{
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
			function validate_email(id)
			{
				
				var request = $.ajax({
					type: "POST",
					url: 'ajax_validate_email.php',
					data: 'id=' +id,
					success: function (data)
					{
						if(data==0)
						{
							$('#emailCheck').html('<i class="icon icon-remove alert-danger alert">Already Exist,Select Another</i>');
							$('input[type="submit"]').attr('disabled','disabled');
						}
						else if(data==1)
						{
							var eml=/^[-.0-9a-zA-Z]+@[a-zA-z]+\.[a-zA-z]{2,3}$/;
							result=eml.test(id);
							if(result==true)
							{
								$('#emailCheck').html('<i class="icon icon-ok alert-success alert"> Valid</i>');
								$('input[type="submit"]').removeAttr('disabled');
							}
							else
							{
								$('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> Enter Proper Email</i>');
								$('input[type="submit"]').attr('disabled','disabled');
							}
						}
					}
				});
			}
			
			$(document).ready(function(){
				$('[data-toggle="tooltip"]').tooltip();
			});
		</script>
		<!-- End: Footer Script -->
	</body>
</html>

