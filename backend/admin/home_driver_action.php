<?php 
	include_once('../common.php');
	
	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	
	require_once(TPATH_CLASS."Imagecrop.class.php");
	$thumb = new thumbnail();
	
	$default_lang = $generalobj->get_default_lang();
	$id 		= isset($_REQUEST['id'])?$_REQUEST['id']:''; // iDriverId
	$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
	$backlink=isset($_POST['backlink'])?$_POST['backlink']:'';
	$action 	= ($id != '')?'Edit':'Add';
	
	//$temp_gallery = $tconfig["tpanel_path"];
	$tbl_name 	= 'home_driver';
	$script 	= 'home_driver';
	
	//echo '<prE>';print_R($_FILES); print_R($_REQUEST); echo '</pre>';exit;
	
	// fetch all lang from language_master table 
	$sql = "SELECT * FROM `language_master` ORDER BY `iDispOrder`";
	$db_master = $obj->MySQLSelect($sql);
	$count_all = count($db_master);
	//echo '<pre>'; print_R($db_master); echo '</pre>';
	
	// set all variables with either post (when submit) either blank (when insert)
	$eStatus_check 	= isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	
	$eStatus 		= ($eStatus_check == 'on')?'Active':'Inactive';
	$thumb = new thumbnail();
	/* to fetch max iDisplayOrder from table for insert */
	$select_order	= $obj->MySQLSelect("SELECT MAX(iDisplayOrder) AS iDisplayOrder FROM ".$tbl_name);
	$iDisplayOrder	= isset($select_order[0]['iDisplayOrder']) ? $select_order[0]['iDisplayOrder'] : 0;
	$iDisplayOrder	= $iDisplayOrder + 1; // Maximum order number
	
	$iDriverId	= isset($_POST['iDriverId'])?$_POST['iDriverId']:$iDriverId;
	$iDisplayOrder	= isset($_POST['iDisplayOrder'])?$_POST['iDisplayOrder']:$iDisplayOrder;
	$temp_order 	= isset($_POST['temp_order'])? $_POST['temp_order'] : "";
	$vImage = isset($_POST['vImage_old']) ? $_POST['vImage_old'] : '';
	if($count_all > 0) {
		for($i=0;$i<$count_all;$i++) {	 
			
			$vName = 'vName_'.$db_master[$i]['vCode'];
			$$vName  = isset($_POST[$vName])?$_POST[$vName]:'';
			$vDesignation = 'vDesignation_'.$db_master[$i]['vCode'];
			$$vDesignation  = isset($_POST[$vDesignation]) ? addslashes($_POST[$vDesignation]):'';
			$tText = 'tText_'.$db_master[$i]['vCode'];
			$$tText  = isset($_POST[$tText]) ? addslashes($_POST[$tText]):'';
		}
	}
	
	
	if(isset($_POST['submit'])) { //form submit
	  if(!empty($iDriverId)){
          if(SITE_TYPE=='Demo')
          {
            header("Location:home_driver_action.php?id=".$id."&success=2");
            exit;
          }
          
      }
		
		//echo "<pre>";print_r($_REQUEST);echo '</pre>'; echo $temp_order.'=='.$iDisplayOrder;
		if($temp_order > $iDisplayOrder) { 
			for($i = $temp_order; $i >= $iDisplayOrder; $i--) { 
				 $sql="UPDATE ".$tbl_name." SET iDisplayOrder = ".($i+1)." WHERE iDisplayOrder = ".$i;
				$obj->sql_query($sql);
			}
			} else if($temp_order < $iDisplayOrder) {
			for($i = $temp_order; $i <= $iDisplayOrder; $i++) {
				$sql="UPDATE ".$tbl_name." SET iDisplayOrder = ".($i-1)." WHERE iDisplayOrder = ".$i;
				$obj->sql_query($sql);
			}
		}
		
		/* $select_order		= $obj->MySQLSelect("SELECT MAX(iFaqcategoryId) AS iFaqcategoryId FROM ".$tbl_name." WHERE vCode = '".$default_lang."'");
		$iFaqcategoryId			= isset($select_order[0]['iFaqcategoryId'])?$select_order[0]['iFaqcategoryId']:0;
		$iFaqcategoryId			= $iFaqcategoryId + 1; // Maximum order number */
		
		/* if($count_all > 0) {
			for($i=0;$i<$count_all;$i++) {
				 */
		$q = "INSERT INTO ";
		$where = '';
		
		if($id != '' ){ 
			$q = "UPDATE ";
			$where = " WHERE `iDriverId` = '".$id."'";
			
		}
		if(isset($_FILES['vImage']) && $_FILES['vImage']['name'] != ""){
				$image_object = $_FILES['vImage']['tmp_name'];  
				$image_name   = $_FILES['vImage']['name'];
				
				if($image_name != ""){
					$filecheck = basename($_FILES['vImage']['name']);                            
					$fileextarr = explode(".",$filecheck);
					$ext=strtolower($fileextarr[count($fileextarr)-1]);
					$flag_error = 0;
					if($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp"){
						$flag_error = 1;
						$var_msg = "Not valid image extension of .jpg, .jpeg, .gif, .png";
					}
					if($_FILES['vImage']['size'] > 1048576){
						$flag_error = 1;
						$var_msg = "Image Size is too Large";
					}
					if($flag_error == 1){
						//$generalobj->getPostForm($_POST,$var_msg,$tconfig['tsite_url']."home_driver_action.php&id=".$id."&success=0");
						header("Location:home_driver_action.php?id=".$id.'&success=0&var_msg='.$var_msg);
						exit;
						}else{
						$Photo_Gallery_folder = $tconfig["tsite_upload_images_panel"];
						// if(!is_dir($Photo_Gallery_folder)){
							// mkdir($Photo_Gallery_folder, 0777);
						// }
						
						//$img = $generalobj->fileupload($Photo_Gallery_folder,$image_object,$image_name, '','jpg,png,gif,jpeg');
						$img = $generalobj->general_upload_image_function($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_images_home"], '', '', '', '', '', 'Y', '', $Photo_Gallery_folder);
						$vImage = $img;
/*						if($img[2] != '1'){
							if (file_exists($Photo_Gallery_folder.$vImage)) {
								unlink($Photo_Gallery_folder.$vImage);
							}
						}*/
						//$vImage = $img[0];
					}
				}
		}
		$sql_str = '';
		if($count_all > 0) {
			for($i=0;$i<$count_all;$i++) {	
				$vName = 'vName_'.$db_master[$i]['vCode'];
				$sql_str .= $vName." = '".$$vName."',";
				$vDesignation = 'vDesignation_'.$db_master[$i]['vCode'];
				$sql_str .= $vDesignation." = '".$$vDesignation."',";
				$tText = 'tText_'.$db_master[$i]['vCode'];
				$sql_str .= $tText." = '".$$tText."',";
			}
		}
		
		$query = $q ." `".$tbl_name."` SET 	
				".$sql_str."
				`vImage` = '".$vImage."', 
				`eStatus` = '".$eStatus."', 
				`iDisplayOrder` = '".$iDisplayOrder."'"
				.$where;
				//echo $query ;exit;
				$obj->sql_query($query);
			
		$id = ($id != '')? $id : $obj->GetInsertId();
		
		//header("Location:home_driver_action.php?id=".$id."&success=1");
		if ($action == "Add") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Record inserted successfully.';
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Record Updated successfully.';
        }
        header("location:".$backlink);
	}
		
		
	// for Edit
	if($action == 'Edit') { 
		$sql = "SELECT * FROM ".$tbl_name." WHERE iDriverId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);	
		//echo '<pre>'; print_R($db_data); echo '</pre>'; exit;
		
		if($count_all > 0) {
			for($i=0;$i<$count_all;$i++) {
				$vName = 'vName_'.$db_master[$i]['vCode'];
				$$vName  = isset($db_data[0][$vName])?$db_data[0][$vName]:$$vName;
				$vDesignation = 'vDesignation_'.$db_master[$i]['vCode'];
				$$vDesignation  = isset($db_data[0][$vDesignation])?$db_data[0][$vDesignation]:$$vDesignation;
				$tText = 'tText_'.$db_master[$i]['vCode'];
				$$tText  = isset($db_data[0][$tText])?$db_data[0][$tText]:$$tText;
				$vImage = $db_data[0]['vImage'];
				$eStatus 			= $db_data[0]['eStatus'];
				$iDisplayOrder 		= $db_data[0]['iDisplayOrder'];
			}
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
		<title>Admin | Home Page <?=$langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"];?>  <?=$action;?></title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
		
		<?php  include_once('global_files.php');?>
		<!-- On OFF switch -->
		<link href="../assets/css/jquery-ui.css" rel="stylesheet" />
		<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />

		<!-- PAGE LEVEL STYLES -->
		<link rel="stylesheet" href="../assets/plugins/Font-Awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="../assets/plugins/wysihtml5/dist/bootstrap-wysihtml5-0.0.2.css" />
		<link rel="stylesheet" href="../assets/css/Markdown.Editor.hack.css" />
		<link rel="stylesheet" href="../assets/plugins/CLEditor1_4_3/jquery.cleditor.css" />
		<link rel="stylesheet" href="../assets/css/jquery.cleditor-hack.css" />
		<link rel="stylesheet" href="../assets/css/bootstrap-wysihtml5-hack.css" />
		<style>
			ul.wysihtml5-toolbar > li {
				position: relative;
			}
		</style>
	</head>
	<!-- END  HEAD-->
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
							<h2><?=$action;?> Home Page <?=$langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"];?> </h2>
							<a href="home_driver.php">
								<input type="button" value="Back to Listing" class="add-btn">
							</a>
						</div>
					</div>
					<hr />	
					<div class="body-div">
						<div class="form-group">
						<?php  if ($success == 0 && $_REQUEST['var_msg'] != "") {?>
							<div class="alert alert-danger alert-dismissable">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								<?php  echo $_REQUEST['var_msg']; ?>
							</div><br/>
						<?php } ?>
						
							<?php  if($success == 1) { ?>
								<div class="alert alert-success alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									Driver Information Updated successfully.
								</div><br/>
							<?php  } ?>
							
							<?php  if ($success == 2) {?>
                 <div class="alert alert-danger alert-dismissable">
                      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                      "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                 </div><br/>
               <?php } ?>

							<form method="post" action="" enctype="multipart/form-data" id="home_driver_action" name="home_driver_action">
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<input type="hidden" name="temp_order" id="temp_order" value="1	">
								<input type="hidden" name="vImage_old" value="<?=$vImage?>">
								<input type="hidden" name="backlink" id="backlink" value="home_driver.php"/>
								<div class="row">
									<div class="col-lg-12">
										<label>Image</label>
									</div>
									<div class="col-lg-6">
										<?php  if($vImage != '') { ?>
											<img src="<?=$tconfig['tsite_upload_images'].$vImage;?>" style="height:100px;">
										<?php  } ?>
										<input type="file" name="vImage" id="vImage" value="<?=$vImage;?>"/>
										<br/>
										[Note: Upload only png image size of 290px * 270px.]
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<label>Status</label>
									</div>
									<div class="col-lg-6">
										<div class="make-switch" data-on="success" data-off="warning">
											<input type="checkbox" name="eStatus" <?=($id != '' && $eStatus == 'Inactive')?'':'checked';?>/>
											</div>
									</div>
								</div>
								<?php  /*if($action == 'Edit') {*/ ?>
								<div class="row">
									<div class="col-lg-12">
										<label>Order</label>
									</div>
									<div class="col-lg-6">
										<?php 
											$temp = 1;
											$query1	= $obj->MySQLSelect("SELECT max(iDisplayOrder) as maxnumber FROM ".$tbl_name." ORDER BY iDisplayOrder");
											$maxnum	= isset($query1[0]['maxnumber']) ? $query1[0]['maxnumber'] : 0;
											$dataArray = array();
											for ($i=1; $i <= $maxnum ; $i++) { 
												$dataArray[] = $i;
												$temp = $iDisplayOrder;
											}											
											/*while($res = mysqli_fetch_array($query1)) 
											{
												$dataArray[] = $res['iDisplayOrder'];
												$temp = $iDisplayOrder;
											}*/
										?>
										<input type="hidden" name="temp_order" id="temp_order" value="<?=$temp?>">
										<select name="iDisplayOrder" class="form-control">
											<?php  foreach($dataArray as $arr):?>
											<option <?= $arr == $temp ? ' selected="selected"' : '' ?> value="<?=$arr;?>" >
												-- <?= $arr ?> --
											</option>
											<?php  endforeach; ?>
											<?php if($action=="Add") {?>
												<option value="<?=$temp;?>" >
													-- <?= $temp ?> --
												</option>
											<?php  }?>
										</select>
										
									</div>
								</div>
								<?php  /*} else{*/ ?>
<!-- 								<div class="row">
									<div class="col-lg-12">
										<label>Order</label>
									</div>
									<div class="col-lg-6">
										<?php 
										$temp = 1;
										
										$dataArray = array();
										
											$query1 = "SELECT max(iDisplayOrder) as MAXDORDER FROM ".$tbl_name."  ORDER BY iDisplayOrder";
											$data_order_one = $obj->MySQLSelect($query1);
											foreach($data_order_one as $value)
											{
												$dataArray[] = $value['MAXDORDER'];
												$temp = $iDisplayOrder;//$iDisplayOrder+1
											}
										?>
										<input type="hidden" name="temp_order" id="temp_order" value="<?=$temp?>">
										<select name="iDisplayOrder" class="form-control">
												<option value="<?=$temp;?>" >
													-- <?= $temp ?> --
												</option>
										</select>
									</div>
								</div> -->
								<?php  //}
								if($count_all > 0) {
										for($i=0;$i<$count_all;$i++) {
											$vCode = $db_master[$i]['vCode'];
											$vTitle = $db_master[$i]['vTitle'];
											
											$vTitle_val = "vName_".$vCode;
											$vDesig_val = "vDesignation_".$vCode;
											$tAnswer_val = "tText_".$vCode;
											
											$eDefault = $db_master[$i]['eDefault'];
											
											$required = ($eDefault == 'Yes')?'required':'';
											$required_msg = ($eDefault == 'Yes')?'<span class="red"> *</span>':'';
										?>
										<div class="row">
											<div class="col-lg-12">
												<label><?=$vTitle;?> Name <?=$required_msg;?></label>
											</div>
											<div class="col-lg-6">
												<input type="text" class="form-control" name="<?=$vTitle_val;?>"  id="<?=$vTitle_val;?>" value="<?=$$vTitle_val;?>" placeholder="Name" <?=$required;?>>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12">
												<label><?=$vTitle;?> Designation <?=$required_msg;?></label>
											</div>
											<div class="col-lg-6">
												<input type="text" class="form-control" name="<?=$vDesig_val;?>"  id="<?=$vDesig_val;?>" value="<?=$$vDesig_val;?>" placeholder="Designation" <?=$required;?>>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12">
												<label><?=$vTitle;?> Text <?=$required_msg;?></label>
											</div>
											<div class="col-lg-12">
												<textarea class="form-control wysihtml5" name="<?=$tAnswer_val;?>"  id="<?=$tAnswer_val;?>" placeholder="Text" <?=$required;?>><?=$$tAnswer_val;?></textarea>
											</div>
										</div>
										<?php  } 
									} ?>
									<div class="row">
									
									
									
									<div class="col-lg-12">
                                        <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?= $action; ?> Home Page <?=$langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"];?>" >
                                        <input type="reset" value="Reset" class="btn btn-default">
                                        <!-- <a href="javascript:void(0);" onclick="reset_form('home_driver_action');" class="btn btn-default">Reset</a> -->
                                        <a href="home_driver.php" class="btn btn-default back_link">Cancel</a>
                                    </div>
									
										
									</div>
								
							</form>
						</div>
					</div>
                    <div class="clear"></div>
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>
		<!--END MAIN WRAPPER -->
		
		
		<?php  include_once('footer.php');?>
		<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
		
		<!-- PAGE LEVEL SCRIPTS -->
		<script src="../assets/plugins/wysihtml5/lib/js/wysihtml5-0.3.0.js"></script>
		<script src="../assets/plugins/bootstrap-wysihtml5-hack.js"></script>
		<script src="../assets/plugins/CLEditor1_4_3/jquery.cleditor.min.js"></script>
		<script src="../assets/plugins/pagedown/Markdown.Converter.js"></script>
		<script src="../assets/plugins/pagedown/Markdown.Sanitizer.js"></script>
		<script src="../assets/plugins/Markdown.Editor-hack.js"></script>
		<script src="../assets/js/editorInit.js"></script>
		<script>
			$(function () { formWysiwyg(); });
		</script>
	</body>
	<!-- END BODY-->    
</html>