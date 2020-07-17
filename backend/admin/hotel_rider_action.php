<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
     require_once(TPATH_CLASS . "class.general_admin.php");
     $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();

$sql = "select * from country where eStatus='Active'";
$db_country = $obj->MySQLSelect($sql);

//For Currency
$sql="select * from  currency where eStatus='Active'";
$db_currency = $obj->MySQLSelect($sql);

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$action = ($id != '') ? 'Edit' : 'Add';
$script = 'Hotel Rider';
$tbl_name = 'hotel';

$sql = "select * from language_master where eStatus = 'Active'";
$db_lang = $obj->MySQLSelect($sql);

$vName = isset($_POST['vName']) ? $_POST['vName'] : '';
$vLastName = isset($_POST['vLastName']) ? $_POST['vLastName'] : '';
$vEmail = isset($_POST['vEmail']) ? $_POST['vEmail'] : '';
$vPassword = isset($_POST['vPassword']) ? $_POST['vPassword'] : '';
$vPhone = isset($_POST['vPhone']) ? $_POST['vPhone'] : '';
$vPhoneCode = isset($_POST['vPhoneCode']) ? $_POST['vPhoneCode'] : '';
$vCountry = isset($_POST['vCountry']) ? $_POST['vCountry'] : '';
$eStatus = isset($_POST['eStatus']) ? $_POST['eStatus'] : 'Inactive';
$vCurrencyPassenger=isset($_POST['vCurrencyPassenger']) ? $_POST['vCurrencyPassenger'] : '';
$vLang = isset($_POST['vLang']) ? $_POST['vLang'] : '';
$vPass = ($vPassword != "") ? $generalobj->encrypt_bycrypt($vPassword) : '';
$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
$previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
$oldImage = isset($_POST['oldImage']) ? $_POST['oldImage'] : '';


if($action == 'Add'){
	$vCountry = $DEFAULT_COUNTRY_CODE_WEB;
}

if (isset($_POST['submit'])) {
	if(!empty($id) && SITE_TYPE =='Demo'){
		$_SESSION['success'] = 2;
		header("Location:rider.php?id=".$id);exit;
	}

	require_once("library/validation.class.php");
    $validobj = new validation();
    $validobj->add_fields($_POST['vName'], 'req', ' Name is required.');
    $validobj->add_fields($_POST['vLastName'], 'req', 'Last name is required.');
    $validobj->add_fields($_POST['vEmail'], 'req', 'Email address is required.');
    $validobj->add_fields($_POST['vEmail'], 'email', '* Please enter valid Email Address.');
	if ($action == "Add") {
		$validobj->add_fields($_POST['vPassword'], 'req', 'Password is required.');
	}
    $validobj->add_fields($_POST['vPhone'], 'req', 'Phone number is required.');
    $validobj->add_fields($_POST['vCountry'], 'req', 'Country is required.');
    $validobj->add_fields($_POST['vLang'], 'req', 'Language is required.');
    $validobj->add_fields($_POST['vCurrencyPassenger'], 'req', 'Currency is required.');
    $error = $validobj->validate();
	
	
	//Other Validations
    if ($vEmail != "") {
        if ($id != "") {
            $msg1 = $generalobj->checkDuplicateAdminNew('iHotelId', $tbl_name, Array('vEmail'), $id, "");
        } else {
            $msg1 = $generalobj->checkDuplicateAdminNew('vEmail', $tbl_name, Array('vEmail'), "", "");
        }
        
        if ($msg1 == 1) {
            $error .= '* Email Address is already exists.<br>';
        }
    }
	$error .= $validobj->validateFileType($_FILES['vImgName'], 'jpg,jpeg,png,gif,bmp', '* Image file is not valid.');
	
    if ($error) {
        $success = 3;
        $newError = $error;
    }
	else
	{
		$vRefCodePara = '';
		$q = "INSERT INTO ";
		$where = '';
		if ($id != '') {
			$q = "UPDATE ";
			$where = " WHERE `iHotelId` = '" . $id . "'";
		}
		
		$passPara = '';
		if($vPass != ""){
			$passPara = "`vPassword` = '" . $vPass . "',";
		}
		$query = $q . " `" . $tbl_name . "` SET
			`vName` = '" . $vName . "',
			`vLastName` = '" . $vLastName . "',
			`vEmail` = '" . $vEmail . "',
			`vImgName` = '" . $oldImage . "',
				$passPara
			`vPhone` = '" . $vPhone . "',
			`vLang` = '" . $vLang . "',			
			`vCountry` = '" . $vCountry . "',			
			`vPhoneCode` = '" . $vPhoneCode . "',			
			`eStatus` = '" . $eStatus . "',			
			`vCurrencyPassenger`='" . $vCurrencyPassenger . "'" . $where;
		$obj->sql_query($query);

		if($id == "") {
			$id = $obj->GetInsertId();
		}
		
		if($_FILES['vImgName']['name']!=''){
			$img_path = $tconfig["tsite_upload_images_hotel_passenger_path"];
			$temp_gallery = $img_path . '/';
			$image_object = $_FILES['vImgName']['tmp_name'];
			$image_name = $_FILES['vImgName']['name'];
			$check_file = $img_path . '/' . $id. '/' .$oldImage;
			if ($oldImage != '' && file_exists($check_file)) {
				@unlink($img_path . '/' . $id. '/' . $oldImage);
				@unlink($img_path . '/' . $id. '/1_' . $oldImage);
				@unlink($img_path . '/' . $id. '/2_' . $oldImage);
				@unlink($img_path . '/' . $id. '/3_' . $oldImage);
			}
			
			$Photo_Gallery_folder = $img_path . '/' . $id . '/';
			if (!is_dir($Photo_Gallery_folder)) {
				mkdir($Photo_Gallery_folder, 0777);
			}
			$img1 = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, '','','', '', '', '', 'Y', '', $Photo_Gallery_folder);

			if($img1!=''){
				if(is_file($Photo_Gallery_folder.$img1)) {
				   include_once(TPATH_CLASS."/SimpleImage.class.php");
				   $img = new SimpleImage();
				   list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$img1);
				   if($width < $height){
					  $final_width = $width;
				   }else{
					  $final_width = $height;
				   }
				   $img->load($Photo_Gallery_folder.$img1)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$img1);
				   $img1 = $generalobj->img_data_upload($Photo_Gallery_folder,$img1,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],"");
				}
			}
			$vImgName = $img1;
			$sql = "UPDATE ".$tbl_name." SET `vImgName` = '" . $vImgName . "' WHERE `iHotelId` = '" . $id . "'";
			$obj->sql_query($sql);
		}
		
		
		if ($action == "Add") {
			$_SESSION['success'] = '1';
			$_SESSION['var_msg'] = 'Record Insert Successfully.';
		} else {
			$_SESSION['success'] = '1';
			$_SESSION['var_msg'] = 'Record Updated Successfully.';
		}
		header("Location:".$backlink);exit;
	}
}

if ($action == 'Edit') {
   $sql = "SELECT iHotelId,vName,vLastName,vEmail,vPassword,vPhone,vPhoneCode,vCountry,eStatus,vCurrencyPassenger,vImgName,vLang FROM " . $tbl_name . " WHERE iHotelId = '" . $id . "'";
    $db_data = $obj->MySQLSelect($sql);
    
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $vName = $value['vName'];
            $vLastName = $generalobjAdmin->clearName(" ".$value['vLastName']);
            $vEmail = $generalobjAdmin->clearEmail($value['vEmail']);
            $vPassword = $value['vPassword'];           
            $vPhone = $generalobjAdmin->clearPhone($value['vPhone']);
            $eGender = $value['eGender'];
            $vPhoneCode = $generalobjAdmin->clearPhone($value['vPhoneCode']);
            $vCountry = $value['vCountry'];           
            $eStatus = $value['eStatus'];          
            $vCurrencyPassenger=$value['vCurrencyPassenger'];
			$vLang = $value['vLang'];
			$oldImage = $value['vImgName'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

     <!-- BEGIN HEAD-->
     <head>
          <meta charset="UTF-8" />
          <title><?=$SITE_NAME?>	 | <?php  echo $langage_lbl_admin['LBL_EDIT_RIDERS_TXT_ADMIN'];?>  <?= $action; ?></title>
          <meta content="width=device-width, initial-scale=1.0" name="viewport" />

          <?php  include_once('global_files.php');?>
          <!-- On OFF switch -->
          <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
          <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
     </head>
     <!-- END HEAD-->
     <!-- BEGIN BODY-->
     <body class="padTop53 " >

          <!-- MAIN WRAPPER -->
          <div id="wrap">
               <?php  include_once('header.php'); ?>
               <?php  include_once('left_menu.php'); ?>
               <!--PAGE CONTENT -->
               <div id="content">
                    <div class="inner">
						<div class="row">
                            <div class="col-lg-12">
                                 <h2><?= $action; ?> <?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?> <?= $vName; ?> <?= $vLastName; ?></h2>
                                 <a class="back_link" href="rider.php">
                                      <input type="button" value="Back to Listing" class="add-btn">
                                 </a>
                            </div>
						</div>
						<hr />
						<div class="body-div">
						<div class="form-group">
                            <?php  if ($success == 3) {?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php  print_r($error); ?>
                            </div><br/>
                            <?php } ?>
						   <form method="post" action="" enctype="multipart/form-data" id="_rider_form" name="_rider_form">
										<input type="hidden" name="actionOf" id="actionOf" value="<?php  echo $action; ?>"/>
                                        <input type="hidden" name="id" id="iHotelId" value="<?= $id; ?>"/>
                                        <input type="hidden" name="oldImage" value="<?= $oldImage; ?>"/>
										<input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
										<input type="hidden" name="backlink" id="backlink" value="hotel_rider.php"/>
									<?php  if($id){?>
                                        <div class="row" id="hide-profile-div">
                                             <div class="col-lg-4">
													<b>
														<?php  if ($oldImage == 'NONE' || $oldImage == '') { ?>
															<img src="../assets/img/profile-user-img.png" alt="">
														<?php }else{?>
															<img src = "<?php  echo $tconfig["tsite_upload_images_hotel_passenger"]. '/' .$id. '/3_' .$oldImage ?>" style="height:150px;"/>
														<?php }?>
													</b>
                                             </div>
                                        </div>
                                        <?php  } ?>
                                       
									   <div class="row">
                                             <div class="col-lg-12">
                                                  <label>First Name <span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="vName"  id="vName" value="<?= $vName; ?>" placeholder="First Name">
                                             </div>
                                        </div>
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Last Name <span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="vLastName"  id="vLastName" value="<?= $vLastName; ?>" placeholder="Last Name">
                                             </div>
                                        </div>

                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Email <span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="vEmail"  id="vEmail" value="<?= $vEmail; ?>"  placeholder="Email"  />

                                             </div>
											 <label id="emailCheck"><label>
                                        </div>
										<div class="row">
                                             <div class="col-lg-12">
                                                  <label>Password<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="password" class="form-control" name="vPassword"  id="vPassword" value="" placeholder="Password">
                                             </div>
                                        </div>
										<div class="row">
                                             <div class="col-lg-12">
                                                  <label>Profile Picture</label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="file" class="form-control" name="vImgName"  id="vImgName" placeholder="Name Label" accept='image/*'>
                                             </div>
                                        </div>
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Country <span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select class="form-control" name = 'vCountry' onChange="changeCode(this.value);">
                                                       <option value="">--select--</option>
                                                       <?php  for($i=0;$i<count($db_country);$i++){ ?>
                                                       <option value = "<?= $db_country[$i]['vCountryCode'] ?>" <?php if($DEFAULT_COUNTRY_CODE_WEB == $db_country[$i]['vCountryCode'] && $action == 'Add') { ?> selected <?php  } else if($vCountry==$db_country[$i]['vCountryCode']){?>selected<?php  } ?>><?= $db_country[$i]['vCountry'] ?></option>
                                                       <?php  } ?>
                                                  </select>
                                             </div>
                                        </div>

                                        <div class="row">
                                             <div class="col-lg-12" style="width:30%">
                                                  <label>Phone<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6"  style="width:50%">
												 <input type="text" class="form-select-2 form-select-21" id="code" readonly name="vPhoneCode" value="<?= $vPhoneCode ?>">
                                                  <input type="text" pattern = "[0-9]{1,}" title="Please enter proper mobile number."  class="mobile-text form-control form-select-3" name="vPhone" id="vPhone" value="<?= $vPhone; ?>" placeholder="Phone">
                                             </div>
                                        </div>
										
										
										<?php  
										if(count($db_lang) <=1){ ?>
										<input name="vLang" type="hidden" class="create-account-input" value="<?php  echo $db_lang[0]['vCode'];?>"/>
											
										<?php  }else{ ?>
										 <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Language<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select  class="form-control" name = 'vLang'>
                                                       <option value="">--select--</option>
                                                       <?php  for ($i = 0; $i < count($db_lang); $i++) { ?>
                                                       <option value = "<?= $db_lang[$i]['vCode'] ?>" <?= ($db_lang[$i]['vCode'] == $vLang) ? 'selected' : ''; ?>><?=$db_lang[$i]['vTitle']?> </option>
                                                       <?php  } ?>
                                                  </select>
                                             </div>
                                        </div>
										<?php  }	?>
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Currency <span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select class="form-control" name = 'vCurrencyPassenger'>
                                                       <option value="">--select--</option>
                                                       <?php  for($i=0;$i<count($db_currency);$i++){ ?>
                                                       <option value = "<?= $db_currency[$i]['vName'] ?>" <?php if($vCurrencyPassenger==$db_currency[$i]['vName']){?>selected<?php  } ?>><?= $db_currency[$i]['vName'] ?></option>
                                                       <?php  } ?>
                                                  </select>
                                             </div>
                                        </div>                                     
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Status</label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <div class="make-switch" data-on="success" data-off="warning">
                                                       <input type="checkbox" name="eStatus" id="eStatus" <?= ($id != '' && $eStatus == 'Inactive') ? '' : 'checked'; ?> value="1"/>
                                                  </div>
                                             </div>
                                        </div>

										<div class="row">
											<div class="col-lg-12">
												<input type="submit" class="btn btn-default" name="submit" id="submit" value="<?= $action; ?> <?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?>" >
												<a href="javascript:void(0);" onClick="reset_form('_rider_form');" class="btn btn-default">Reset</a>
												<a href="hotel_rider.php" class="btn btn-default back_link">Cancel</a>
											</div>
										</div>
                                   </form>
                              </div>
                         </div>
                    </div>
               </div>
               <!--END PAGE CONTENT -->
          </div>
          <!--END MAIN WRAPPER -->
<?php  include_once('footer.php');?>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script>
$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){
		referrer =  document.referrer;	
		//alert(referrer);
	}else { 
		referrer = $("#previousLink").val();
	}
	if(referrer == "") {
		referrer = "hotel_rider.php";
	}else {
		$("#backlink").val(referrer);
	}
	$(".back_link").attr('href',referrer);
});
            
function changeCode(id) {
	var request = $.ajax({
		type: "POST",
		url: 'change_code.php',
		data: 'id=' + id,
		success: function (data)
		{
			document.getElementById("code").value = data;
		}
	});
}
changeCode('<?php  echo $vCountry; ?>');
</script>
</body>
<!-- END BODY-->
</html>
