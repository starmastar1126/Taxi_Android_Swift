<?php  
	include_once('../common.php');

	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();

	require_once(TPATH_CLASS."Imagecrop.class.php");

	$id 		= isset($_REQUEST['id'])?$_REQUEST['id']:'';
	$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
	$action 	= ($id != '')?'Edit':'Add';

	$tbl_name 	= 'home_screens';
	$script 	= 'Settings';

	// fetch all lang from home_screens table
	$sql = "SELECT * FROM `home_screens`";
	$db_master = $obj->MySQLSelect($sql);	
	
	$count_all = count($db_master);

	
	// set all variables with either post (when submit) either blank (when insert)	
	$iId = isset($_POST['iId'])?$_POST['iId']:$id;
	$vImageTitle = isset($_REQUEST['vImageTitle'])?$_REQUEST['vImageTitle']:'';
	$vImageName = isset($_REQUEST['vImageName'])?$_REQUEST['vImageName']:'';
	$iDescOrd = isset($_REQUEST['iDescOrd'])?$_REQUEST['iDescOrd']:'';
	$eStatus_check = isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	$eStatus = ($eStatus_check == 'on')?'Active':'Inactive';
	$thumb = new thumbnail();	
	
	if(isset($_POST['btnsubmit'])) {

		if(SITE_TYPE=="Demo"){
			header("Location:appscreenshot_action.php?id=".$iId.'&success=2');
			exit;
		}	
		
		$image_object = $_FILES['vImageName']['tmp_name'];
		$image_name   = $_FILES['vImageName']['name'];
		
		if($image_name != ""){
			$filecheck = basename($_FILES['vImageName']['name']);
			$fileextarr = explode(".",$filecheck);
			$ext = strtolower($fileextarr[count($fileextarr)-1]);
			$flag_error = 0;
			if($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp"){
				$flag_error = 1;
				$var_msg = "Not valid image extension of .jpg, .jpeg, .gif, .png";
			}
			/*if($_FILES['vImageName']['size'] > 1048576){
				$flag_error = 1;
				$var_msg = "Image Size is too Large";
			}*/
			 $data = getimagesize($_FILES['vImageName']['tmp_name']);
			 $width = $data[0];
			 $height = $data[1];
			 
			 if($width != 240 && $height != 466) {	 
				
				  $flag_error = 1;
				  $var_msg = "Please Upload image only 240px * 466px";
			 }
			if($flag_error == 1){			
			
				if($action == "Add"){
				header("Location:appscreenshot_action.php?var_msg=".$var_msg);			
				exit;	
				}else{
					header("Location:appscreenshot_action.php?id=".$id."&var_msg=".$var_msg);		
				exit;
				}				
					/* 	echo "gdgd";
						exit; */
					
						//echo $generalobj->getPostForm($_POST,$var_msg,$tconfig['tsite_url']."/appscreenshot_action&success=0");
					//	exit;
			}else{				
			
				//$Photo_Gallery_folder = $tconfig["tsite_upload_page_images_panel"].'/';
				 $Photo_Gallery_folder = $tconfig["tsite_upload_apppage_images"];
				
				if(!is_dir($Photo_Gallery_folder)){
                   	mkdir($Photo_Gallery_folder, 0777);
				}

			   $img = $generalobj->fileupload($Photo_Gallery_folder,$image_object,$image_name, '','jpg,png,gif,jpeg');
				
				$vImageName = $img[0];
				
			}
		}		

		 $q = "INSERT INTO ";
		$where = '';

		if($id != '' ){
			$q = "UPDATE ";
			$where = " WHERE `iId` = '".$iId."'";
		}		

		$query = $q ." `".$tbl_name."` SET 
		`vImageTitle` = '".$vImageTitle."',	
		`iDescOrd` = '".$iDescOrd."',
		`eStatus` = '".$eStatus."'";
		
		if($vImageName!=''){
			$query.= ", vImageName = '".$vImageName."'";
		}
		
		 $query.=$where;
		$Id = $obj->sql_query($query);
		
		if($action == 'Add')
		{
			
			$iId =  $obj->GetInsertId();
		}

		header("Location:appscreenshot_action.php?id=".$iId.'&success=1');

	}

	// for Edit
	if($action == 'Edit') {
		$sql = "SELECT * FROM ".$tbl_name." WHERE iId = '".$iId."'";
		$db_data = $obj->MySQLSelect($sql);
				
		if(count($db_data) > 0) {
			for($i=0;$i<count($db_master);$i++)
			{
				foreach($db_data as $key => $value) {					
					$vImageTitle = $value['vImageTitle'];
					$iDescOrd = $value['iDescOrd'];					
					$vImageName = $value['vImageName'];
					$eStatus = $value['eStatus'];
					$iId = $value['iId'];
					
				}
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
		<title>Admin | App Screen Short Page <?=$action;?></title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

		<?php  include_once('global_files.php');?>
		<!-- PAGE LEVEL STYLES -->
		<link rel="stylesheet" href="../assets/plugins/Font-Awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="../assets/plugins/wysihtml5/dist/bootstrap-wysihtml5-0.0.2.css" />
		<link rel="stylesheet" href="../assets/css/Markdown.Editor.hack.css" />
		<link rel="stylesheet" href="../assets/plugins/CLEditor1_4_3/jquery.cleditor.css" />
		<link rel="stylesheet" href="../assets/css/jquery.cleditor-hack.css" />
		<link rel="stylesheet" href="../assets/css/bootstrap-wysihtml5-hack.css" />
		<link href="../assets/css/jquery-ui.css" rel="stylesheet" />
		<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
		<script src="../assets/plugins/ckeditor/ckeditor.js"></script>
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
							<h2><?=$action;?>  App ScreenShot Page</h2>
							<a href="app_screenshot.php">
								<input type="button" value="Back to Listing" class="add-btn">
							</a>
						</div>
					</div>
					<hr />
					<div class="body-div">
						<div class="form-group">
							<?php  if($success == 1) { ?>
								<div class="alert alert-success alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									Record Updated successfully.
								</div><br/>
							<?php  }elseif($success == 2){ ?>
								<div class="alert alert-danger alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you. 
								</div><br/>
							<?php  } ?>
								<?php  if($_REQUEST['var_msg'] !=Null) { ?>
								<div class="alert alert-danger alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									Record  Not Updated .
								</div><br/>
							<?php  } ?>
							<form method="post" action=""  enctype="multipart/form-data">
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<div class="row">
									<div class="col-lg-12">
										<label>Screen Title</label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vImageTitle" value="<?=$vImageTitle;?>" placeholder="Screen Titile" required >
									</div>
								</div>								
								<div class="row">
									<div class="col-lg-12">
										<label>Upload ScreenShot</label>
									</div>
									<div class="col-lg-6">
										<?php  if($vImageName != '') { ?>
											<a target="_blank" href="<?=$tconfig['tsite_upload_apppage_images_panel'].$vImageName;?>"><img src="<?=$tconfig['tsite_upload_apppage_images_panel'].$vImageName;?>" style="width:200px;height:100px;"></a>
										<?php  } ?>
										<input type="file" name="vImageName" id="vImageName" />
									</div>
									
								</div>
								<div class="row">
									<div class="col-lg-12">
										<label>[Note:Recommended ScreenShot size is Width:240px And Height:466px]</label>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										  <label>Display Order<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<select  class="form-control" name ='iDescOrd' id='app_descId' required>
											<?php 
											
												// select count of image order
												$sql = "SELECT * FROM ".$tbl_name;
												$db_Of_data = $obj->MySQLSelect($sql);
												//$count_item = count($db_Of_data);
												if(!empty($db_Of_data)){
										
													for($i=0;$i<=count($db_Of_data);$i++){ ?>
													
														<option value="<?=$i+1;?>" <?php  if($iDescOrd ==$i+1){?> selected <?php  }?>><?=$i+1?></option>
														
													<?php  }						
													
												}else { ?>													 
												  <option value="1">1</option>
												<?php   }	?>												  												  
										</select>
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
								<div class="row">
										<div class="col-lg-12">
											<input type="submit" class="save btn-info" name="btnsubmit" id="btnsubmit" value="<?=$action;?> Image">
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

		<!-- GLOBAL SCRIPTS -->
		<script src="../assets/plugins/jquery-2.0.3.min.js"></script>
		<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
		<script src="../assets/plugins/modernizr-2.6.2-respond-1.1.0.min.js"></script>
		<!-- END GLOBAL SCRIPTS -->

		<!-- PAGE LEVEL SCRIPTS -->
		<script src="../assets/plugins/wysihtml5/lib/js/wysihtml5-0.3.0.js"></script>
		<script src="../assets/plugins/bootstrap-wysihtml5-hack.js"></script>
		<script src="../assets/plugins/CLEditor1_4_3/jquery.cleditor.min.js"></script>
		<script src="../assets/plugins/pagedown/Markdown.Converter.js"></script>
		<script src="../assets/plugins/pagedown/Markdown.Sanitizer.js"></script>
		<script src="../assets/plugins/Markdown.Editor-hack.js"></script>
		<script src="../assets/plugins/ckeditor/ckeditor.js"></script>
		<script src="../assets/plugins/ckeditor/config.js"></script>
		<script src="../assets/js/editorInit.js"></script>
		<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
		<script>
			$(function () { formWysiwyg(); });
			CKEDITOR.replace( 'ckeditor',{
				allowedContent : {
					i:{
						classes:'fa*'
					},
					span: true
				}
				} );
		</script>

	</body>
	<!-- END BODY-->
</html>
