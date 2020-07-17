<?php 
	include_once('../common.php');

	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();

	//require_once(TPATH_CLASS."Imagecrop.class.php");

	$id 		= isset($_REQUEST['id'])?$_REQUEST['id']:'';
	$vEmail_Code = isset($_REQUEST['vEmail_Code'])?$_REQUEST['vEmail_Code']:''; 
	$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
	$action 	= ($id != '')?'Edit':'Add';

	$tbl_name 	= 'email_templates';
	$script 	= 'email_templates';
	
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
    $previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';

	//echo '<prE>'; print_R($_REQUEST); echo '</pre>';

	// fetch all lang from language_master table
	$sql = "SELECT * FROM `language_master` ORDER BY `iDispOrder`";
	$db_master = $obj->MySQLSelect($sql);
	$count_all = count($db_master);
	//echo '<pre>'; print_R($db_master); echo '</pre>';

	// set all variables with either post (when submit) either blank (when insert)
	$iEmailId = isset($_POST['iEmailId'])?$_POST['iEmailId']:$id;
	/* $vPageName = isset($_REQUEST['vPageName'])?$_REQUEST['vPageName']:'';
	$vTitle = isset($_REQUEST['vTitle'])?$_REQUEST['vTitle']:'';
	$tMetaKeyword = isset($_REQUEST['tMetaKeyword'])?$_REQUEST['tMetaKeyword']:'';
	$tMetaDescription = isset($_REQUEST['tMetaDescription'])?$_REQUEST['tMetaDescription']:'';
	$vImage 		= isset($_POST['vImage'])?$_POST['vImage']:'';
	$thumb = new thumbnail(); */
	if($count_all > 0) {
		for($i=0;$i<$count_all;$i++) {
			$vSubject = 'vSubject_'.$db_master[$i]['vCode'];
			$$vSubject  = isset($_POST[$vSubject])?$_POST[$vSubject]:'';
			$vBody = 'vBody_'.$db_master[$i]['vCode'];
			$$vBody  = isset($_POST[$vBody])?$_POST[$vBody]:'';
		}
	}

	if(isset($_POST['submit'])) {
		if(SITE_TYPE=='Demo')
		{
				header("Location:email_template_action.php?id=".$iEmailId.'&success=2');
				exit;
		}
		//echo "<pre>";print_r($_REQUEST);echo "</pre>";exit;
		if(count($db_master) > 0) {
			$str = '';
			for($i=0;$i<count($db_master);$i++) {
				$vSubject = 'vSubject_'.$db_master[$i]['vCode'];
				$$vSubject = $_REQUEST[$vSubject];
				$vBody = 'vBody_'.$db_master[$i]['vCode'];
				$$vBody = $_REQUEST[$vBody];

				$str .= " ".$vSubject." = '".$$vSubject."', ".$vBody." = '".$$vBody."', ";
			}

		}

		/* $image_object = $_FILES['vImage']['tmp_name'];
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
				$generalobj->getPostForm($_POST,$var_msg,$tconfig['tsite_url']."page_action&success=0");
				exit;
				}else{
				$Photo_Gallery_folder = $tconfig["tsite_upload_page_images_panel"].'/';
				if(!is_dir($Photo_Gallery_folder)){
                   	mkdir($Photo_Gallery_folder, 0777);
				}
				$img = $generalobj->fileupload($Photo_Gallery_folder,$image_object,$image_name, '','jpg,png,gif,jpeg');
				$vImage = $img[0];
			}
		} */

		$q = "INSERT INTO ";
		$where = '';

		if($id != '' ){
			$q = "UPDATE ";
			$where = " WHERE `iEmailId` = '".$iEmailId."'";
		}


		$query = $q ." `".$tbl_name."` SET ".$str."
		`vEmail_Code` = '".$vEmail_Code."'"
		.$where;
		$Id = $obj->sql_query($query);
		//$s = $obj->GetInsertId();
		if($action == 'Add')
		{
			$iEmailId = $obj->GetInsertId();
		}

		//header("Location:email_template_action.php?id=".$iEmailId.'&success=1');
		if ($action == "Add") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Record Insert Successfully.';
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Record Updated Successfully.';
        }
		 header("location:".$backlink);


	}

	// for Edit
	if($action == 'Edit') {
		$sql = "SELECT * FROM ".$tbl_name." WHERE iEmailId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);
		//echo '<pre>'; print_R($db_data); echo '</pre>'; exit;
		$vLabel = $id;


		if(count($db_data) > 0) {
			for($i=0;$i<count($db_master);$i++)
			{
				foreach($db_data as $key => $value) {
					$vSubject = 'vSubject_'.$db_master[$i]['vCode'];
					$$vSubject = $value[$vSubject];
					$vBody = 'vBody_'.$db_master[$i]['vCode'];
					$$vBody = $value[$vBody];
					$vEmail_Code = $value['vEmail_Code'];
					$vSection = $value['vSection'];
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
		<title>Admin | Email Template <?=$action;?></title>
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
		
		<script type="text/javascript">
		  (function () {
			var converter1 = Markdown.getSanitizingConverter();
			var editor1 = new Markdown.Editor(converter1);
			editor1.run();
		  } );
		</script>
		
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
							<h2><?=$action;?> Email Template</h2>
							<a href="email_template.php" class="back_link">
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
								<?php  }elseif ($success == 2) { ?>
									<div class="alert alert-danger alert-dismissable">
											 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
											 "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
									</div><br/>
								<?php  }?> 
							<form method="post" name="_email_template_form" id="_email_template_form" action=""  enctype="multipart/form-data">
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="email_template.php"/>
								<input type="hidden" name="vEmail_Code" id="vEmail_Code" value="<?=$vEmail_Code;?>">

								<?php 
									if($count_all > 0) {
										for($i=0;$i<$count_all;$i++) {
											$vCode = $db_master[$i]['vCode'];
											$vLTitle = $db_master[$i]['vTitle'];
											$eDefault = $db_master[$i]['eDefault'];

											$vSubject = 'vSubject_'.$vCode;
											$vBody = 'vBody_'.$vCode;

											$required = ($eDefault == 'Yes')?'required':'';
											$required_msg = ($eDefault == 'Yes')?'<span class="red"> *</span>':'';
										?>
										<div class="row">
											<div class="col-lg-12">
												<label><?=$vLTitle;?> Subject <?=$required_msg;?></label>
											</div>
											<div class="col-lg-6">
												<input type="text" class="form-control " name="<?=$vSubject;?>"  id="<?=$vSubject;?>" value="<?=$$vSubject;?>" placeholder="<?=$vLTitle;?> Subject" <?=$required;?>>
											</div>
										</div>

										<!--- Editor -->
										<div class="row">
											<div class="col-lg-12">
												<label><?=$vLTitle;?> Body <?=$required_msg;?></label>
											</div>
											<div class="col-lg-6">
												<textarea class="form-control wysihtml5" rows="10" name="<?=$vBody;?>"  id="<?=$vBody;?>"  placeholder="<?=$vLTitle;?> Body" <?=$required;?>> <?=$$vBody;?></textarea>
											</div>
										</div>

										<!--- Editor -->
										<?php  }
									} ?>
									<div class="row">
										<div class="col-lg-12">
										<input type="submit" class="btn btn-default" name="submit" id="submit" value="<?=$action;?> Email Template">
										 <input type="reset" value="Reset" class="btn btn-default">
										<!-- <a href="javascript:void(0);" onclick="reset_form('_email_template_form');" class="btn btn-default">Reset</a> -->
                                        <a href="email_template.php" class="btn btn-default back_link">Cancel</a>
										
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

		<!-- PAGE LEVEL SCRIPTS -->
		 
		<script src="../assets/plugins/CLEditor1_4_3/jquery.cleditor.min.js"></script>
		<script src="../assets/plugins/wysihtml5/lib/js/wysihtml5-0.3.0.js"></script>
		<script src="../assets/plugins/bootstrap-wysihtml5-hack.js"></script>
		<!-- <script src="../assets/plugins/pagedown/pagedown_init.js"></script> -->
		<!-- <script src="../assets/js/editorInit.js"></script> -->
		

	</body>
	<!-- END BODY-->
</html>
<script>
			$(function () { 
			
				$('.wysihtml5').wysihtml5({
					"html": true,
				});
				//formWysiwyg();
				/*var converter1 = Markdown.getSanitizingConverter();
				var editor1 = new Markdown.Editor(converter1);
				editor1.run();*/					
			});
</script>
<script>
$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){ //alert('pre1');
		referrer =  document.referrer;
		// alert(referrer);
	}else { //alert('pre2');
		referrer = $("#previousLink").val();
	}

	if(referrer == "") {
		referrer = "email_template.php";
	}else { //alert('hi');
		$("#backlink").val(referrer);
		// alert($("#backlink").val(referrer));
	}
	$(".back_link").attr('href',referrer); 
	//alert($(".back_link").attr('href',referrer));	
});
</script>