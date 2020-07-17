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

	$tbl_name 	= 'seo_sections';
	$script 	= 'seo_setting';

	// fetch all lang from home_screens table
	$sql = "SELECT * FROM `seo_sections`";
	$db_master = $obj->MySQLSelect($sql);	
	
	$count_all = count($db_master);

	
	// set all variables with either post (when submit) either blank (when insert)	
	 $iId = isset($_POST['iId'])?$_POST['iId']:$id;
	 $vPagetitle = isset($_REQUEST['vPagetitle'])?$_REQUEST['vPagetitle']:'';
	 $vMetakeyword = isset($_REQUEST['vMetakeyword'])?$_REQUEST['vMetakeyword']:'';
	 $tDescription = isset($_REQUEST['tDescription'])?$_REQUEST['tDescription']:'';
	
	
	if(isset($_POST['submit'])) {

		if(SITE_TYPE=="Demo"){
			header("Location:seosetting_action.php?id=".$iId.'&success=2');
			exit;
		}
		require_once("library/validation.class.php");
		$validobj = new validation();
		$validobj->add_fields($_POST['vPagetitle'], 'req', 'Page title is required.');
		$validobj->add_fields($_POST['vMetakeyword'], 'req', 'Meta keyword is required.');
		$validobj->add_fields($_POST['tDescription'], 'req', 'Description  is required.');
		$error = $validobj->validate();
		
		
		if ($error) {
        $success = 3;
        $newError = $error;
        //exit;
    } 
	else 
	{
		if($id != '' ){
			$q = "UPDATE ";
			$where = " WHERE `iId` = '".$iId."'";
		}		

		$query = $q ." `".$tbl_name."` SET 
		`vPagetitle` = '".$vPagetitle."',	
		`vMetakeyword` = '".$vMetakeyword."',	
		`tDescription` = '".$tDescription."'";	
		
		 $query.=$where;
		$Id = $obj->sql_query($query);
		
		header("Location:seosetting_action.php?id=".$iId.'&success=1');

	}
	}	
	
	// for Edit
	if($action == 'Edit') {
		$sql = "SELECT * FROM ".$tbl_name." WHERE iId = '".$iId."'";
		$db_data = $obj->MySQLSelect($sql);
				
		if(count($db_data) > 0) {
			for($i=0;$i<count($db_master);$i++)
			{
				foreach($db_data as $key => $value) {					
					$vPagename = $value['vPagename'];
					$vPagetitle = $value['vPagetitle'];
					$vMetakeyword = $value['vMetakeyword'];
					$tDescription = $value['tDescription'];						
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
		<title>Admin | SEO Setting Page <?=$action;?></title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

		<?php  include_once('global_files.php');?>
		<!-- PAGE LEVEL STYLES -->
		<link rel="stylesheet" href="../assets/plugins/Font-Awesome/css/font-awesome.css" />
		<link rel="stylesheet" href="../assets/plugins/wysihtml5/dist/bootstrap-wysihtml5-0.0.2.css" />
		<link rel="stylesheet" href="../assets/css/Markdown.Editor.hack.css" />
		<link rel="stylesheet" href="../assets/plugins/CLEditor1_4_3/jquery.cleditor.css" />
		<!--<link rel="stylesheet" href="../assets/css/jquery.cleditor-hack.css" />-->
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
							<h2><?=$action;?>  SEO Setting Page</h2>
							<a href="seo_setting.php">
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
							 <?php  if ($success == 3) {?>
          <div class="alert alert-danger alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
			<?php  print_r($error); ?>
             </div>
          <br/>
          <?php } ?>
							<form method="post" action=""  enctype="multipart/form-data" name="_seo_form" id="_seo_form">
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<div class="row">
									<div class="col-lg-12">
										<label>Page Name</label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vPagename" value="<?=$vPagename;?>" placeholder="Page Name" disabled >
									</div>
								</div>	
								<div class="row">
									<div class="col-lg-12">
										<label>Page Title</label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vPagetitle" value="<?=$vPagetitle;?>" placeholder="Page Title">
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<label>Meta Keyword</label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vMetakeyword" value="<?=$vMetakeyword;?>" placeholder="Meta Keyword"  >
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<label>Meta Description</label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="tDescription" value="<?=$tDescription;?>" placeholder="Description"  >
									</div>
								</div>							
								<div class="row">
										<div class="col-lg-12">
										    <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?= $action; ?>" SEO Setting >
										    <input type="reset" value="Reset" class="btn btn-default">
											<!-- <a href="javascript:void(0);" onclick="reset_form('_seo_form');" class="btn btn-default">Reset</a> -->
											<a href="seo_setting.php" class="btn btn-default back_link">Cancel</a>
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
		<!--<script src="../assets/plugins/jquery-2.0.3.min.js"></script>-->
		<script src="../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
		<script src="../assets/plugins/modernizr-2.6.2-respond-1.1.0.min.js"></script>
		<!-- END GLOBAL SCRIPTS -->

		<!-- PAGE LEVEL SCRIPTS -->
		<script src="../assets/plugins/wysihtml5/lib/js/wysihtml5-0.3.0.js"></script>
		<script src="../assets/plugins/bootstrap-wysihtml5-hack.js"></script>
		<script src="../assets/plugins/CLEditor1_4_3/jquery.cleditor.min.js"></script>
		<script src="../assets/plugins/pagedown/Markdown.Converter.js"></script>
		<script src="../assets/plugins/pagedown/Markdown.Sanitizer.js"></script>
		<1<script src="../assets/plugins/Markdown.Editor-hack.js"></script>-->
		<script src="../assets/plugins/ckeditor/ckeditor.js"></script>
		<script src="../assets/plugins/ckeditor/config.js"></script>
		<script src="../assets/js/editorInit.js"></script>
		<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
		<script>
			$(function () { formWysiwyg(); });
			/* CKEDITOR.replace( 'ckeditor',{
				allowedContent : {
					i:{
						classes:'fa*'
					},
					span: true
				}
				} ); */
		</script>

	</body>
	<!-- END BODY-->
</html>
