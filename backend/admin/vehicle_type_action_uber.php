<?php 
include_once('../common.php');

require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();

if (!isset($generalobjAdmin)) {
     require_once(TPATH_CLASS . "class.general_admin.php");
     $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();



$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$message_print_id=$id;
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;

$tbl_name = 'vehicle_type';
$script = 'VehicleType';

$vVehicleType = isset($_POST['vVehicleType']) ? $_POST['vVehicleType'] : '';
$fPricePerKM = isset($_POST['fPricePerKM']) ? $_POST['fPricePerKM'] : '';
$fPricePerMin = isset($_POST['fPricePerMin']) ? $_POST['fPricePerMin'] : '';
$iBaseFare = isset($_POST['iBaseFare']) ? $_POST['iBaseFare'] : '';
$fCommision = isset($_POST['fCommision']) ? $_POST['fCommision'] : '';
$iPersonSize = isset($_POST['iPersonSize']) ? $_POST['iPersonSize'] : '';
$fPickUpPrice = isset($_POST['fPickUpPrice']) ? $_POST['fPickUpPrice'] : '';
$fNightPrice = isset($_POST['fNightPrice']) ? $_POST['fNightPrice'] : '';
$tPickStartTime = isset($_POST['tPickStartTime']) ? $_POST['tPickStartTime'] : '';
$tPickEndTime = isset($_POST['tPickEndTime']) ? $_POST['tPickEndTime'] : '';
$tNightStartTime = isset($_POST['tNightStartTime']) ? $_POST['tNightStartTime'] : '';
$tNightEndTime = isset($_POST['tNightEndTime']) ? $_POST['tNightEndTime'] : '';
$eStatus_picktime 	= isset($_POST['ePickStatus'])?$_POST['ePickStatus']:'off';
$ePickStatus 		= ($eStatus_picktime == 'on')?'Active':'Inactive';
$eStatus_nighttime 	= isset($_POST['eNightStatus'])?$_POST['eNightStatus']:'off';
$eNightStatus 		= ($eStatus_nighttime == 'on')?'Active':'Inactive';

	
 
	if(isset($_POST['btnsubmit'])){
	
	if(isset($_FILES['vLogo']) && $_FILES['vLogo']['name'] != ""){
		 $filecheck = basename($_FILES['vLogo']['name']);
		 $fileextarr = explode(".", $filecheck);
		 $ext = strtolower($fileextarr[count($fileextarr) - 1]);
		 $flag_error = 0;
		 if($ext != "png") {
			  $flag_error = 1;
			  $var_msg = "Upload only png image";
		 }
		 $data = getimagesize($_FILES['vLogo']['tmp_name']);
		 $width = $data[0];
		 $height = $data[1];
		 
		 if($width != 360 && $height != 360) {
		 
			  $flag_error = 1;
			  $var_msg = "Please Upload image only 360px * 360px";
		 }
		 if ($flag_error == 1) {
			
			
			if($action == "Add"){
			header("Location:vehicle_type_action.php?var_msg=".$var_msg);
			exit;	
			}else{
			header("Location:vehicle_type_action.php?id=".$id."&var_msg=".$var_msg);			
			exit;
			}	
			
			 // $generalobj->getPostForm($_POST, $var_msg, "vehicle_type_action.php?success=0&var_msg=".$var_msg);
			 // exit;
		 }
	}
	
	if(isset($_FILES['vLogo1']) && $_FILES['vLogo1']['name'] != ""){
		 $filecheck = basename($_FILES['vLogo1']['name']);
		 $fileextarr = explode(".", $filecheck);
		 $ext = strtolower($fileextarr[count($fileextarr) - 1]);
		 $flag_error = 0;
		 if($ext != "png") {
			  $flag_error = 1;
			  $var_msg = "Upload only png image";
		 }
		 $data = getimagesize($_FILES['vLogo1']['tmp_name']);
		 $width = $data[0];
		 $height = $data[1];
		 
		 if($width != 360 && $height != 360) {
		 
			  $flag_error = 1;
			  $var_msg = "Please Upload image only 360px * 360px";
		 }
		 if ($flag_error == 1) {
		 
			if($action == "Add"){
			header("Location:vehicle_type_action.php?var_msg=".$var_msg);
			//$generalobj->getPostForm($_POST,$var_msg,"banner_action.php");
			exit;	
			}else{
			header("Location:vehicle_type_action.php?id=".$id."&var_msg=".$var_msg);
			//$generalobj->getPostForm($_POST,$var_msg,"banner_action.php?id=".$id."&var_msg=".$var_msg);
			exit;
			}	
			  //$generalobj->getPostForm($_POST, $var_msg, "vehicle_type_action.php?success=0&var_msg=".$var_msg);
			  exit;
		 }
	}	
	
		if($ePickStatus == "Active"){  
		  if($tPickStartTime > $tPickEndTime){
			header("Location:vehicle_type_action.php?id=".$id."&success=3");exit;
		  }
		}  
		if($eNightStatus == "Active"){  
		  if($tNightStartTime > $tNightEndTime){
			header("Location:vehicle_type_action.php?id=".$id."&success=4");exit;
		  }
		}
		if(SITE_TYPE =='Demo'){
		   header("Location:vehicle_type_action.php?id=".$id."&success=2");exit;
		}
		$q = "UPDATE ";
         $where = " WHERE `iVehicleTypeid` = '" . $id . "'";

		 $query = $q . " `" . $tbl_name . "` SET
		`vVehicleType` = '" . $vVehicleType . "',
		`fPricePerKM` = '" . $fPricePerKM . "',
		`fPricePerMin` = '" . $fPricePerMin . "',
		`iBaseFare` = '" . $iBaseFare . "',
		`fCommision` = '" . $fCommision . "',
		`iPersonSize` = '" . $iPersonSize. "',
		`fPickUpPrice` = '" . $fPickUpPrice. "',
		`fNightPrice` = '" . $fNightPrice. "',
		`tPickStartTime` = '" . $tPickStartTime. "',
		`tPickEndTime` = '" . $tPickEndTime. "',
		`tNightStartTime` = '" . $tNightStartTime. "',
		`tNightEndTime` = '" . $tNightEndTime. "',
		`ePickStatus` = '" . $ePickStatus. "',
		`eNightStatus` = '" . $eNightStatus. "'". $where;
		
		$obj->sql_query($query);
		$id = ($id != '') ? $id : $obj->GetInsertId();
		
		
		if(isset($_FILES['vLogo']) && $_FILES['vLogo']['name'] != ""){		
		
			$img_path = $tconfig["tsite_upload_images_vehicle_type_path"];
			$temp_gallery = $img_path . '/';
			$image_object = $_FILES['vLogo']['tmp_name'];
			$image_name = $_FILES['vLogo']['name'];
			
			$check_file_query = "select iVehicleTypeId,vLogo from vehicle_type where iVehicleTypeId=" . $id;
			$check_file = $obj->sql_query($check_file_query);		
			
			if($image_name != "") {
			
			
				if($message_print_id != "") {
					 $check_file['vLogo'] = $img_path . '/' . $id . '/android/' . $check_file[0]['vLogo'];						
					 $android_path = $img_path . '/' . $id . '/android';
					 $ios_path = $img_path . '/' . $id . '/ios';
					
					if ($check_file['vLogo'] != '' && file_exists($check_file['vLogo'])) {
						@unlink($android_path . '/'.$check_file['vLogo1']);
						@unlink($android_path . '/mdpi_'.$check_file['vLogo']);
						@unlink($android_path . '/hdpi_'.$check_file['vLogo']);
						@unlink($android_path . '/xhdpi_'.$check_file['vLogo']);
						@unlink($android_path . '/xxhdpi_'.$check_file['vLogo']);
						@unlink($android_path . '/xxxhdpi_'.$check_file['vLogo']);
						@unlink($ios_path . '/'.$check_file['vLogo1']);
						@unlink($ios_path . '/1x_'.$check_file['vLogo']);
						@unlink($ios_path . '/2x_'.$check_file['vLogo']);
						@unlink($ios_path . '/3x_'.$check_file['vLogo']);
					}
				}
				echo   $Photo_Gallery_folder = $img_path . '/' . $id . '/';
				echo  $Photo_Gallery_folder_android = $Photo_Gallery_folder . 'android/';
				echo  $Photo_Gallery_folder_ios = $Photo_Gallery_folder . 'ios/';
				if (!is_dir($Photo_Gallery_folder)) {
				   mkdir($Photo_Gallery_folder, 0777);
				   mkdir($Photo_Gallery_folder_android, 0777);
				   mkdir($Photo_Gallery_folder_ios, 0777);
				}	  
			 
			$img = $generalobj->general_upload_image_vehicle_android($image_object, $image_name, $Photo_Gallery_folder_android, $tconfig["tsite_upload_images_vehicle_type_size1_android"], $tconfig["tsite_upload_images_vehicle_type_size2_android"], $tconfig["tsite_upload_images_vehicle_type_size3_both"], $tconfig["tsite_upload_images_vehicle_type_size4_android"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_type_size5_both"], $Photo_Gallery_folder_android,$vVehicleType,NULL);
			$img1 = $generalobj->general_upload_image_vehicle_ios($image_object, $image_name, $Photo_Gallery_folder_ios, '', '', $tconfig["tsite_upload_images_vehicle_type_size3_both"], $tconfig["tsite_upload_images_vehicle_type_size5_both"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_type_size5_ios"], $Photo_Gallery_folder_ios,$vVehicleType,NULL);
			$vImage = "ic_car_".$vVehicleType.".png";
			
			  
				$sql = "UPDATE ".$tbl_name." SET `vLogo` = '" . $vImage . "' WHERE `iVehicleTypeId` = '" . $id . "'";
			
				$obj->sql_query($sql);
			}
		}
	   
	    if(isset($_FILES['vLogo1']) && $_FILES['vLogo1']['name'] != ""){
			$img_path = $tconfig["tsite_upload_images_vehicle_type_path"];
			$temp_gallery = $img_path . '/';
			$image_object = $_FILES['vLogo1']['tmp_name'];
			$image_name = $_FILES['vLogo1']['name'];
			$check_file_query = "select iVehicleTypeId,vLogo1 from vehicle_type where iVehicleTypeId=" . $id;
			$check_file = $obj->sql_query($check_file_query);
				if($image_name != "") {
				  if($message_print_id != "") {
						$check_file['vLogo1'] = $img_path . '/' . $id . '/android/' . $check_file[0]['vLogo1'];
						$android_path = $img_path . '/' . $id . '/android';
						$ios_path = $img_path . '/' . $id . '/ios';
						
						if ($check_file['vLogo1'] != '' && file_exists($check_file['vLogo1'])) {
							@unlink($android_path . '/'.$check_file['vLogo1']);
							@unlink($android_path . '/mdpi_hover_'.$check_file['vLogo1']);
							@unlink($android_path . '/hdpi_hover_'.$check_file['vLogo1']);
							@unlink($android_path . '/xhdpi_hover_'.$check_file['vLogo1']);
							@unlink($android_path . '/xxhdpi_hover_'.$check_file['vLogo1']);
							@unlink($android_path . '/xxxhdpi_hover_'.$check_file['vLogo1']);
							@unlink($ios_path . '/'.$check_file['vLogo1']);
							@unlink($ios_path . '/1x_hover_'.$check_file['vLogo1']);
							@unlink($ios_path . '/2x_hover_'.$check_file['vLogo1']);
							@unlink($ios_path . '/3x_hover_'.$check_file['vLogo1']);
						}
					}
					$Photo_Gallery_folder = $img_path . '/' . $id . '/';
					$Photo_Gallery_folder_android = $Photo_Gallery_folder . '/android/';
					$Photo_Gallery_folder_ios = $Photo_Gallery_folder . '/ios/';
					if (!is_dir($Photo_Gallery_folder)) {
					   mkdir($Photo_Gallery_folder, 0777);
					   mkdir($Photo_Gallery_folder_android, 0777);
					   mkdir($Photo_Gallery_folder_ios, 0777);
					}				  
					  $img = $generalobj->general_upload_image_vehicle_android($image_object, $image_name, $Photo_Gallery_folder_android, $tconfig["tsite_upload_images_vehicle_type_size1_android"], $tconfig["tsite_upload_images_vehicle_type_size2_android"], $tconfig["tsite_upload_images_vehicle_type_size3_both"], $tconfig["tsite_upload_images_vehicle_type_size4_android"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_type_size5_both"], $Photo_Gallery_folder_android,$vVehicleType,"hover_");
					  $img1 = $generalobj->general_upload_image_vehicle_ios($image_object, $image_name, $Photo_Gallery_folder_ios, '', '', $tconfig["tsite_upload_images_vehicle_type_size3_both"], $tconfig["tsite_upload_images_vehicle_type_size5_both"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_type_size5_ios"], $Photo_Gallery_folder_ios,$vVehicleType,"hover_");
					  $vImage1 = "ic_car_".$vVehicleType.".png";
					  
					  $sql = "UPDATE ".$tbl_name." SET `vLogo1` = '" . $vImage1 . "' WHERE `iVehicleTypeId` = '" . $id . "'";
					  $obj->sql_query($sql);
				}
	    }	
		
		 $obj->sql_query($query);
		 header("Location:vehicle_type_action.php?id=" . $id . '&success=1');
	}

// for Edit

     $sql = "SELECT * FROM " . $tbl_name . " WHERE iVehicleTypeid = '" . $id . "'";
     $db_data = $obj->MySQLSelect($sql);

     $vLabel = $id;
     if (count($db_data) > 0) {
          foreach ($db_data as $key => $value) {
               $vVehicleType = $value['vVehicleType'];
               $fPricePerKM = $value['fPricePerKM'];
               $fPricePerMin = $value['fPricePerMin'];
               $iBaseFare = $value['iBaseFare'];
               $fCommision = $value['fCommision'];
			         $iPersonSize = $value['iPersonSize'];
			         $fPickUpPrice = $value['fPickUpPrice'];
			         $fNightPrice = $value['fNightPrice'];
			         $tPickStartTime = $value['tPickStartTime'];
			         $tPickEndTime = $value['tPickEndTime'];
			         $tNightStartTime = $value['tNightStartTime'];
			         $tNightEndTime = $value['tNightEndTime'];
			         $ePickStatus = $value['ePickStatus'];
			         $eNightStatus = $value['eNightStatus'];
					  $vLogo = $value['vLogo'];
          }
     }

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

     <!-- BEGIN HEAD-->
     <head>
          <meta charset="UTF-8" />
          <title>Admin | Vehicle Type <?= $action; ?></title>
          <meta content="width=device-width, initial-scale=1.0" name="viewport" />
          <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
          <?php 
          include_once('global_files.php');
          ?>
          <!-- On OFF switch -->
          <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
          <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
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
               <div id="content">
                    <div class="inner">
                         <div class="row">
                              <div class="col-lg-12">
                                   <h2> Vehicle Type </h2>
                                   <a href="vehicle_type.php">
                                        <input type="button" value="Back to Listing" class="add-btn">
                                   </a>
                              </div>
                         </div>
                         <hr />
                         <div class="body-div">
                              <div class="form-group">
                                   <?php  if ($success == 1) {?>
                                   <div class="alert alert-success alert-dismissable">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                        Record Updated successfully.
                                   </div><br/>
                                   <?php  } elseif ($success == 2) { ?>
                         						<div class="alert alert-danger alert-dismissable">
                         								 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                         								 "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                         						</div><br/>
                         					<?php  } elseif ($success == 3) { ?>
                                   <div class="alert alert-danger alert-dismissable">
                         								 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                         								 "Please Select Pickup Start Time less than Pickup End Time." 
                         						</div><br/>	
                         					<?php  } elseif ($success == 4) { ?>
                                   <div class="alert alert-danger alert-dismissable">
                         								 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                         								 "Please Select Night Start Time less than Night End Time." 
                         						</div><br/>	
                         					<?php  } ?>
									<?php  if($_REQUEST['var_msg'] !=Null) { ?>
								<div class="alert alert-danger alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									Record  Not Updated .
								</div><br/>
							<?php  } ?>		
								   <div id="price1" >

                                   </div><br/>
								   <div id="price" ></div><br/>
                                   <form id="vtype" method="post" action="" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= $id; ?>"/>
                                        <div class="row">

                                             <div class="col-lg-12">
                                                  <label>Vehicle Type<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="vVehicleType"  id="vVehicleType" readonly value="<?= $vVehicleType; ?>"  required>
                                             </div>
                                        </div>
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label> Price/Km<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="fPricePerKM"  id="fPricePerKM" value="<?= $fPricePerKM; ?>" required" onchange="getpriceCheck(this.value)">
                                             </div>

                                        </div>

										<div class="row">
                                             <div class="col-lg-12">
                                                  <label> Price/min<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="fPricePerMin"  id="fPricePerMin" value="<?= $fPricePerMin; ?>"  required onchange="getpriceCheck(this.value)">

                                             </div>
                                        </div>
										<div class="row">
                                             <div class="col-lg-12">
                                                  <label> Base Fare<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="iBaseFare"  id="iBaseFare" value="<?= $iBaseFare; ?>" required onchange="getpriceCheck(this.value)">

                                             </div>
                                        </div>
										<div class="row">
                                             <div class="col-lg-12">
                                                  <label> Commision<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="fCommision"  id="fCommision" value="<?= $fCommision; ?>" required onchange="getpriceCheck(this.value)">

                                             </div>
                                        </div>
										<div class="row">
                                             <div class="col-lg-12">
                                                  <label> Person Size<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="iPersonSize"  id="iPersonSize" value="<?= $iPersonSize; ?>" required onchange="getpriceCheck(this.value),onlydigit(this.value)">

                                             </div>
											 <div id="digit"></div>
                                        </div>
                                        
                                        
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label> Peak Time Surcharge (X)<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="fPickUpPrice"  id="fPickUpPrice" value="<?= $fPickUpPrice; ?>" required onchange="getpriceCheck(this.value)"> 

                                             </div>
                                        </div>
                                        <div class="row">
                                          <div class="col-lg-12">
                                                  <label> Peak Time Surcharge On/Off <span class="red"> *</span></label>
                                          </div>
                                          <div class="col-lg-6">
                        										<div class="make-switch" data-on="success" data-off="warning">
                        											<input type="checkbox" id="ePickStatus" onchange="showhidepickuptime();" name="ePickStatus" <?=($id != '' && $ePickStatus == 'Inactive')?'':'checked';?>/>
                        											</div>
                        									</div>
                                        </div>
                                        
                                        <div id="showpickuptime" style="display:none;">
                                         <div class="row">
                                             <div class="col-lg-12">
                                                  <label> Peak Time Surcharge Start Time<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" readonly class=" form-control" name="tPickStartTime"  id="tPickStartTime" value="<?= $tPickStartTime; ?>" placeholder="Select Pickup Start Time" required>
                                             </div>
                                         </div>
                                         <div class="row">
                                             <div class="col-lg-12">
                                                  <label> Peak Time Surcharge End Time<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" readonly class=" form-control" name="tPickEndTime"  id="tPickEndTime" value="<?= $tPickEndTime; ?>" placeholder="Select Pickup End Time" required>
                                             </div>
                                         </div> 
                                        </div> 
                                        
                                        <div class="row">
                                          <div class="col-lg-12">
                                                  <label> Night Charges On/Off <span class="red"> *</span></label>
                                          </div>
                                          <div class="col-lg-6">
                        										<div class="make-switch" data-on="success" data-off="warning">
                        											<input type="checkbox" id="eNightStatus" onchange="showhidenighttime();" name="eNightStatus" <?=($id != '' && $eNightStatus == 'Inactive')?'':'checked';?>/>
                        											</div>
                        									</div>
                                        </div>
                                         
                                         <div id="shownighttime" style="display:none;">
                                            <div class="row">
                                               <div class="col-lg-12">
                                                    <label> Night Charges Start Time<span class="red"> *</span></label>
                                               </div>
                                               <div class="col-lg-6">
                                                    <input type="text" readonly class=" form-control" name="tNightStartTime"  id="tNightStartTime" value="<?= $tNightStartTime; ?>" placeholder="Select Night Start Time" required>
                                               </div>
                                           </div>
                                           <div class="row">
                                               <div class="col-lg-12">
                                                    <label> Night Charges End Time<span class="red"> *</span></label>
                                               </div>
                                               <div class="col-lg-6">
                                                    <input type="text" readonly class=" form-control" name="tNightEndTime"  id="tNightEndTime" value="<?= $tNightEndTime; ?>" placeholder="Select Night End Time" required>
                                               </div>
                                           </div>                                                    
                                         </div>                                   
                                         <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Vehicle Type Picture (Gray image)</label>
                                             </div>
                                             <div class="col-lg-6">
                                             	  <?php  if($vLogo != '') { ?>
                                             	  <img src="<?=$tconfig['tsite_upload_images_vehicle_type']."/".$id."/ios/3x_".$vLogo;?>" style="width:100px;height:100px;">
                                             	  <?php }?>
                                                  <input type="file" class="form-control" name="vLogo" <?php  echo $required_rule; ?> id="vLogo" placeholder="" style="padding-bottom: 39px;">
                                                  <br/>
                                                  [Note: Upload only png image size of 360px*360px.]
                                             </div>
                                        </div>										
										<div class="row">
                                             <div class="col-lg-12">
                                                  <label>Vehicle Type Picture (Orange image)</label>
                                             </div>
                                             <div class="col-lg-6">
                                             	  <?php  if($vLogo != '') { ?>
                                             	  <img src="<?=$tconfig['tsite_upload_images_vehicle_type']."/".$id."/ios/3x_hover_".$vLogo;?>" style="width:100px;height:100px;">
                                             	  <?php }?>
                                                  <input type="file" class="form-control" name="vLogo1" <?php  echo $required_rule; ?> id="vLogo1" placeholder="" style="padding-bottom: 39px;">
                                                  <br/>
                                                  [Note: Upload only png image size of 360px*360px.]
                                             </div>
                                        </div>



                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <input type="submit" class="save btn-info" name="btnsubmit" id="btnsubmit" value="Update" >
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


          <?php 
          include_once('footer.php');
          ?>
          <script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
          <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
		      <script type="text/javascript" src="js/moment.min.js"></script>
		      <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
          <script>
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
                                                                 url: 'validate_email.php',
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
											 function getpriceCheck(id)
											    {
													/*var km_rs=document.getElementById('fPricePerKM').value;
													var min_rs=document.getElementById('fPricePerMin').value;
													var base_rs=document.getElementById('iBaseFare').value;
													var com_rs=document.getElementById('fCommision').value;
													if(km_rs != 0 && min_rs !=0 && base_rs != 0 && com_rs != 0)
													{
													}*/

													if(id>0)
													{
														$('input[type="submit"]').removeAttr('disabled');
													}
													else
													{
															$('#price').html('<i class="alert-danger alert"> You can not EnterAny price Zero or Letter</i>');
															$('input[type="submit"]').attr('disabled','disabled');
													}
												}
												function onlydigit(id)
												{
													var digi=/^[1-9]{1}$/;
													result=digi.test(id);
													if(result==true)
													{
														$('input[type="submit"]').removeAttr('disabled');
													}
													else
													{
														$('#digit').html('<i class="alert-danger alert">Only Decimal Number less Than 10</i>');
															$('input[type="submit"]').attr('disabled','disabled');
													}

												}
												
												function checkDates() {   
                            if (tPickStartTime.val() != '' && tPickEndTime.val() != '') {
                                if (Date.parse(tPickStartTime.val()) > Date.parse(tPickEndTime.val())) {
                                    alert('End date should be before start date');
                                    endDate.val(tPickStartTime.val());
                                }
                            }
                        }
                        
                        
                        $(function () {
                    				newDate = new Date('Y-M-D');
                              $('#tPickStartTime').datetimepicker({
                    					format: 'HH:mm:ss',
                    					//minDate: moment().format('l'),
                    					ignoreReadonly: true,
                    					//sideBySide: true,
                    				});
                        });
                        
                        $(function () {
                    				newDate = new Date('Y-M-D');
                              $('#tPickEndTime').datetimepicker({
                    					format: 'HH:mm:ss',
                    					//minDate: moment().format('l'),
                    					ignoreReadonly: true,
                    					//sideBySide: true,
                    				})
                        }); 
                                
                           
                        $(function () {
                    				newDate = new Date('Y-M-D');
                              $('#tNightStartTime').datetimepicker({
                    					format: 'HH:mm:ss',
                    					//minDate: moment().format('l'),
                    					ignoreReadonly: true,
                    					//sideBySide: true,
                    				});
                        });
                        
                        $(function () {
                    				newDate = new Date('Y-M-D');
                              $('#tNightEndTime').datetimepicker({
                    					format: 'HH:mm:ss',
                    					//minDate: moment().format('l'),
                    					ignoreReadonly: true,
                    					//sideBySide: true,
                    				})
                        });    
                        
                        /*
                        $(function () {
                            $('#startTime, #endTime').datetimepicker({
                                format: 'hh:mm',
                                pickDate: false,
                                pickSeconds: false,
                                pick12HourFormat: false            
                            });
                        });
                        */
                         $(document).ready(function() {
                            $.validator.addMethod("tPickEndTime", function(value, element) {
                                var startDate = $('#tPickStartTime').val();
                                return Date.parse(startDate) <= Date.parse(value) || value == "";
                            }, "* End date must be after start date");
                            $('#vtype').validate();
                        });
                        
                        function showhidepickuptime(){
                           if($('input[name=ePickStatus]').is(':checked')){
                                //alert('Checked');
                                $("#showpickuptime").show();
                           }else{
                                //alert('Not checked');
                                $("#showpickuptime").hide();
                           }
                        }
                        
                        function showhidenighttime(){
                           if($('input[name=eNightStatus]').is(':checked')){
                                //alert('Checked');
                                $("#shownighttime").show();
                           }else{
                                //alert('Not checked');
                                $("#shownighttime").hide();
                           }
                        }
                        showhidepickuptime();
                        showhidenighttime();
          </script>
          
     </body>
     <!-- END BODY-->
</html>
