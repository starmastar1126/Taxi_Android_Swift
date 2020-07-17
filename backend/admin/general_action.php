<?php 
	include_once('../common.php');

	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	$tbl_name = 'configurations';
	$script   = 'Settings';

	// set all variables with either post (when submit) either blank (when insert)
	$id      = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
	$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
	$action  = ($id != '') ? 'Edit' : 'Add';

	$vName     = isset($_POST['vName']) ? $_POST['vName'] : '';
	$tDescription = isset($_POST['tDescription']) ? $_POST['tDescription'] : '';
	$vValue = isset($_POST['vValue']) ? $_POST['vValue'] : '';

	if (isset($_POST['submit'])) {
		
		/*  */
		if($vName=='APP_IMG')
		{
		$image_object = $_FILES['vValue']['tmp_name'];
		$image_name   = $_FILES['vValue']['name'];

		if($image_name != ""){
			$filecheck = basename($_FILES['vValue']['name']);
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
				$generalobj->getPostForm($_POST,$var_msg,$tconfig['tsite_url']."page_action&success=0");
				exit;
				}else{
				$Photo_Gallery_folder = $tconfig["tsite_upload_page_images_panel"].'/';
				if(!is_dir($Photo_Gallery_folder)){
                   	mkdir($Photo_Gallery_folder, 0777);
				}


			  $img = $generalobj->fileupload($Photo_Gallery_folder,$image_object,$image_name, '','jpg,png,gif,jpeg');
				//echo "<pre>";print_r($img);exit;
				 $vImage = $img[0];
				 $vValue=$vImage ;
			}
		}
		
		
		}
		
	/*	*/

		$q     = "INSERT INTO ";
		$where = '';

		if ($id != '') {
			if(SITE_TYPE =='Demo'){
				 header("Location:general_action.php?id=".$id."&success=2");exit;
			}
			$q     = "UPDATE ";
			$where = " WHERE `iSettingId` = '" . $id . "'";
		}

	$query = $q . " `" . $tbl_name . "` SET
		`vName` = '" . $vName . "',
		`vValue` = '" . $vValue . "'" . $where;
		//echo '<pre>'; print_r($query); exit;
		$obj->sql_query($query);
		$id = ($id != '') ? $id : $obj->GetInsertId();
		header("Location:general_action.php?id=" . $id . '&success=1');

	}
	// for Edit

	if ($action == 'Edit') {
		$sql     = "SELECT * FROM " . $tbl_name . " WHERE iSettingId = '" . $id . "'";
		$db_data = $obj->MySQLSelect($sql);
		//print_R($db_data);exit;
		//echo "<pre>";print_R($db_data);echo "</pre>";
		if (count($db_data) > 0) {
			foreach ($db_data as $key => $value) {
				$vValue = $value['vValue'];
				$vName = $value['vName'];
				$tDescription = $value['tDescription'];
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
		<title>Admin | General <?= $action; ?></title>
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
							<h2> General</h2>
							<a href="general.php">
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
							<?php } elseif ($success == 2) { ?>
								<div class="alert alert-danger alert-dismissable">
										 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
										 "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
								</div><br/>
							<?php  } ?>
							<form method="post" action="" enctype="multipart/form-data">
								<input type="hidden" name="id" value="<?= $id; ?>"/>
							 <div class="row">
									<div class="col-lg-12">
										<label>Label
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vName"  id="vName" value="<?=$vName;?>" placeholder="Last Name" required readonly >
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<label><?=$tDescription;?><span class="red"> *</span></label>
									</div>





									<div class="col-lg-6">
										<?php 
											if($vName=='APP_IMG'){
												?>
										<input type="file" class="form-control" name="vValue"  id="vValue" value="<?= $vValue; ?>" placeholder="First Name" required>
										<?php 
											}else if($vName=='FACEBOOK_IFRAME')
											{?>
										<textarea class="form-control" name="vValue"  id="vValue" cols="" rows="8"><?= $vValue; ?></textarea>
										<?php }
												else{
													?>
										<input type="text" class="form-control" name="vValue"  id="vValue" value="<?= $vValue; ?>" placeholder="" required>
										<?php 		}
											?>
									</div>
								</div>

								<!-- <div class="row">
									<div class="col-lg-12">
										<label>Description<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="hidden" class="form-control" name="tDescription"  id="tDescription" value="<?=$tDescription;?>" placeholder="Last Name" required>
									</div>
								</div>-->

								<div class="row">
									<div class="col-lg-12">
										<input type="submit" class="save btn-info" name="submit" id="submit" value="Save">
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
		<script>
			function changeCode(id)
			{
				var request = $.ajax({  					type: "POST",
					url: 'change_code.php',
					data: 'id='+id,

					success: function(data)
					{
						document.getElementById("code").value = data ;
						//window.location = 'profile.php';
					}
				});
			}
		</script>
	</body>
	<!-- END BODY-->
</html>
