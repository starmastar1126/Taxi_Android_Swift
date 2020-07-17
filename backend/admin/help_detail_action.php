<?php 
	include_once('../common.php');
	
	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	
	require_once(TPATH_CLASS."Imagecrop.class.php");
	
	$default_lang = $generalobj->get_default_lang();
	$id 		= isset($_REQUEST['id'])?$_REQUEST['id']:''; // iFaqcategoryId
	$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
	$help_detail_cat_id	= isset($_REQUEST['help_detail_cat_id'])?$_REQUEST['help_detail_cat_id']:'';
	$eShowDetail	= isset($_REQUEST['eShowDetail'])?$_REQUEST['eShowDetail']:'No';
	$action 	= ($id != '')?'Edit':'Add';
	
	//$temp_gallery = $tconfig["tpanel_path"];
	$tbl_name 	= 'help_detail';
	$script 	= 'help_detail';
	
	//echo '<prE>'; print_R($_REQUEST); echo '</pre>';
	
	// fetch all lang from language_master table 
	$sql = "SELECT * FROM `language_master` ORDER BY `iDispOrder`";
	$db_master = $obj->MySQLSelect($sql);
	$count_all = count($db_master);
	//echo '<pre>'; print_R($db_master); echo '</pre>';
	
	// set all variables with either post (when submit) either blank (when insert)
	$eStatus_check 	= isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
    $previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	
	$eStatus 		= ($eStatus_check == 'on')?'Active':'Inactive';
	$thumb = new thumbnail();
	/* to fetch max iDisplayOrder from table for insert */
	$select_order	= $obj->MySQLSelect("SELECT count(iDisplayOrder) AS iDisplayOrder FROM ".$tbl_name);
	$iDisplayOrder	= isset($select_order[0]['iDisplayOrder'])?$select_order[0]['iDisplayOrder']:0;
	$iDisplayOrder_max	= $iDisplayOrder + 1; // Maximum order number

	$iHelpDetailCategoryId	= isset($_POST['iHelpDetailCategoryId'])?$_POST['iHelpDetailCategoryId']:$help_detail_cat_id;
	$iDisplayOrder	= isset($_POST['iDisplayOrder'])?$_POST['iDisplayOrder']:$iDisplayOrder;
	$temp_order 	= isset($_POST['temp_order'])? $_POST['temp_order'] : "";
	
	if($count_all > 0) {
		for($i=0;$i<$count_all;$i++) {
			$vTitle = 'vTitle_'.$db_master[$i]['vCode'];
			$$vTitle  = isset($_POST[$vTitle])?$_POST[$vTitle]:'';
			$tAnswer = 'tAnswer_'.$db_master[$i]['vCode'];
			$$tAnswer  = isset($_POST[$tAnswer])?$_POST[$tAnswer]:'';
		}
	}
	
	
	if(isset($_POST['submit'])) { //form submit
	  if(!empty($help_detail_cat_id)){
          if(SITE_TYPE=='Demo')
          {
            header("Location:help_detail_action.php?id=".$id."&help_detail_cat_id=".$help_detail_cat_id."&success=2");
            exit;
          }
          
      }
	  
		// echo "<pre>";print_r($_REQUEST);echo '</pre>'; echo $temp_order.'=='.$iDisplayOrder;
		if($temp_order == "1" && $action == "Add"){
			$temp_order = $iDisplayOrder_max;
		}
		if($temp_order > $iDisplayOrder) { 
			for($i = $temp_order-1; $i >= $iDisplayOrder; $i--) { 
				$sql="UPDATE ".$tbl_name." SET iDisplayOrder = '".($i+1)."' WHERE iDisplayOrder = '".$i."'";
				$obj->sql_query($sql);
			}
		} else if($temp_order < $iDisplayOrder) {
			for($i = $temp_order+1; $i <= $iDisplayOrder; $i++) {
				$sql="UPDATE ".$tbl_name." SET iDisplayOrder = '".($i-1)."' WHERE iDisplayOrder = '".$i."'";
				$obj->sql_query($sql);
			}
		}		
		
		$q = "INSERT INTO ";
		$where = '';
		
		if($id != '' ){ 
			$q = "UPDATE ";
			$where = " WHERE `iHelpDetailId` = '".$id."'";
			
		}
		$sql_str = '';
		if($count_all > 0) {
			for($i=0;$i<$count_all;$i++) {	
				$vTitle = 'vTitle_'.$db_master[$i]['vCode'];
				$sql_str .= $vTitle." = '".$$vTitle."',";
				$tAnswer = 'tAnswer_'.$db_master[$i]['vCode'];
				$sql_str .= $tAnswer." = '".$$tAnswer."',";
			}
		}
		
		$query = $q ." `".$tbl_name."` SET 	
				".$sql_str."
				`eStatus` = '".$eStatus."',
				`eShowDetail` = '".$eShowDetail."',
				`iHelpDetailCategoryId` = '".$iHelpDetailCategoryId."',
				`iDisplayOrder` = '".$iDisplayOrder."'"
				.$where;
				$obj->sql_query($query);
			
		$id = ($id != '')?$id:$obj->GetInsertId();
		
		//header("Location:faq_action.php?id=".$id."&help_detail_cat_id=".$iFaqcategoryId."&success=1");
		if ($action == "Add") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Help Detail Insert Successfully.';
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Help Detail Updated Successfully.';
        }
		 header("location:".$backlink);
	}
		
		
	// for Edit
	if($action == 'Edit') { 
		$sql = "SELECT * FROM ".$tbl_name." WHERE iHelpDetailId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);	
		//echo '<pre>'; print_R($db_data); echo '</pre>'; 
		
		if($count_all > 0) {
			for($i=0;$i<$count_all;$i++) {
				$vTitle = 'vTitle_'.$db_master[$i]['vCode'];
				$$vTitle  = isset($db_data[0][$vTitle])?$db_data[0][$vTitle]:$$vTitle;
				$tAnswer = 'tAnswer_'.$db_master[$i]['vCode'];
				$$tAnswer  = isset($db_data[0][$tAnswer])?$db_data[0][$tAnswer]:$$tAnswer;
				
				$eStatus 			= $db_data[0]['eStatus'];
				$iDisplayOrder_db 	   = $db_data[0]['iDisplayOrder'];
				$eShowDetail 	   = $db_data[0]['eShowDetail'];
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
		<title>Admin | Help Topic  <?=$action;?></title>
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
							<h2><?=$action;?> Help Topic </h2>
							<a href="help_detail.php" class="back_link">
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
							<?php  } ?>
							
							<?php  if ($success == 2) {?>
                 <div class="alert alert-danger alert-dismissable">
                      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                      "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                 </div><br/>
               <?php } ?>

							<form method="post" name="_help_detail_form" id="_help_detail_form" action="" enctype="multipart/form-data">
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="help_detail.php"/>
								<?php  
								$sql = "SELECT * FROM help_detail_categories WHERE vCode = '".$default_lang."' AND eStatus = 'Active' ORDER BY  vTitle ASC ";
								$db_cat = $obj->MySQLSelect($sql);
								//echo "<pre>";print_r($db_cat);exit;
								if(count($db_cat) > 0) { ?>
								<div class="row">
									<div class="col-lg-12">
										<label>Category</label>
									</div>
									<div class="col-lg-6">
										<select name="iHelpDetailCategoryId" id="iHelpDetailCategoryId" class="form-control">
											<?php  for($i=0; $i<count($db_cat); $i++){?>
											<option value="<?=$db_cat[$i]['iUniqueId'];?>" <?=($db_cat[$i]['iUniqueId'] == $help_detail_cat_id)?'selected':'';?>>
												-- <?= $db_cat[$i]['vTitle'] ?> --
											</option>
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
										
										<input type="hidden" name="temp_order" id="temp_order" value="<?=($action == 'Edit') ? $iDisplayOrder_db : '1';?>">
										<?php 
											$display_numbers = ($action=="Add") ? $iDisplayOrder_max : $iDisplayOrder;
										?>
										<select name="iDisplayOrder" class="form-control">
											<?php  for($i=1; $i <= $display_numbers; $i++){ ?>
												<option value="<?=$i?>" <?php if($i == $iDisplayOrder_db){echo "selected";}?>> -- <?=$i?> --</option>
											<?php  } ?>
										</select>
										
									</div>
								</div>
							
								<?php  
								if($count_all > 0) {
										for($i=0;$i<$count_all;$i++) {
											$vCode = $db_master[$i]['vCode'];
											$vTitle = $db_master[$i]['vTitle'];
											
											$vTitle_val = "vTitle_".$vCode;
											$tAnswer_val = "tAnswer_".$vCode;
											
											$eDefault = $db_master[$i]['eDefault'];
											
											$required = ($eDefault == 'Yes')?'required':'';
											$required_msg = ($eDefault == 'Yes')?'<span class="red"> *</span>':'';
										?>
										<div class="row">
											<div class="col-lg-12">
												<label><?=$vTitle;?> Question <?=$required_msg;?></label>
											</div>
											<div class="col-lg-6">
												<input type="text" class="form-control" name="<?=$vTitle_val;?>"  id="<?=$vTitle_val;?>" value="<?=$$vTitle_val;?>" placeholder="Help Detail" <?=$required;?>>
											</div>
										</div>
										<div class="row">
											<div class="col-lg-12">
												<label><?=$vTitle;?> Answer <?=$required_msg;?></label>
											</div>
											<div class="col-lg-12">
												<textarea class="form-control wysihtml5" name="<?=$tAnswer_val;?>"  id="<?=$tAnswer_val;?>" placeholder="Answer" <?=$required;?>><?=$$tAnswer_val;?></textarea>
											</div>
										</div>
										<?php  } 
									} ?>									
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Show Contact Form<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <select  class="form-control" name = 'eShowDetail' required id='eShowDetail'>

                                                    <option value="Yes" <?php  if ($eShowDetail == "Yes") echo 'selected="selected"'; ?> >Yes</option>
                                                    <option value="No"<?php  if ($eShowDetail == "No") echo 'selected="selected"'; ?>>No</option>
                                                </select>
                                            </div>
                                        </div>
									<div class="row">
										<div class="col-lg-12">							
											<input type="submit" class="btn btn-default" name="submit" id="submit" value="<?=$action;?> Help Topic">
											<input type="reset" value="Reset" class="btn btn-default">
											<!-- <a href="javascript:void(0);" onclick="reset_form('_faq_form');" class="btn btn-default">Reset</a> -->
                                            <a href="help_detail.php" class="btn btn-default back_link">Cancel</a>
										</div>
									</div>
								<?php  } else { ?>
									Please enter Help Topic Catgory
								<?php  } ?>
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
		<script src="../assets/plugins/Markdown.Editor-hack.js"></script>
		<script src="../assets/js/editorInit.js"></script>
		<script>
			$(function () { 
				formWysiwyg();
			 });
		</script>
	</body>
	<!-- END BODY--> 
<script>
$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){ alert(referrer);	
		referrer =  document.referrer;	
       	
	}else { 
		referrer = $("#previousLink").val();
	}
	if(referrer == "") {
		referrer = "help_detail.php";
	}else { 
		$("#backlink").val(referrer);		
	}
	$(".back_link").attr('href',referrer); 	
});
/**
 * This will reset the CKEDITOR using the input[type=reset] clicks.
 */
$(function() {
    if (typeof CKEDITOR != 'undefined') {
        $('form').on('reset', function(e) {
            if ($(CKEDITOR.instances).length) {
                for (var key in CKEDITOR.instances) {
                    var instance = CKEDITOR.instances[key];
                    if ($(instance.element.$).closest('form').attr('name') == $(e.target).attr('name')) {
                        instance.setData(instance.element.$.defaultValue);
                    }
                }
            }
        });
    }
});
</script>	
</html>