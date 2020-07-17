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
	$action 	= ($id != '')?'Edit':'Add';
	
	//$temp_gallery = $tconfig["tpanel_path"];
	$tbl_name 	= 'cancel_reason';
	$script 	= 'Page';
	
	//echo '<prE>'; print_R($_REQUEST); echo '</pre>';
	
	// fetch all lang from language_master table 
	$sql = "SELECT * FROM `language_master` ORDER BY `iDispOrder`";
	$db_master = $obj->MySQLSelect($sql);
	$count_all = count($db_master);
	//echo '<pre>'; print_R($db_master); echo '</pre>';
	
	// set all variables with either post (when submit) either blank (when insert)
	$eAllowedCharge_check 	= isset($_POST['eAllowedCharge'])?$_POST['eAllowedCharge']:'off';
	$eAllowedCharge 	= ($eAllowedCharge_check == 'on')?'Yes':'No';
	
	$eStatus_check 	= isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	$eStatus 		= ($eStatus_check == 'on')?'Active':'Inactive';
	$thumb = new thumbnail();
	/* to fetch max iDisplayOrder from table for insert */
	$select_order	= $obj->MySQLSelect("SELECT MAX(iSortId) AS iSortId FROM ".$tbl_name);
	$iDisplayOrder	= isset($select_order[0]['iSortId'])?$select_order[0]['iSortId']:0;
	$iDisplayOrder	= $iDisplayOrder + 1; // Maximum order number

	$iDisplayOrder	= isset($_POST['iDisplayOrder'])?$_POST['iDisplayOrder']:$iDisplayOrder;
	$temp_order 	= isset($_POST['temp_order'])? $_POST['temp_order'] : "";
	
	if($count_all > 0) {
		for($i=0;$i<$count_all;$i++) {
			$vTitle = 'vTitle_'.$db_master[$i]['vCode'];
			$$vTitle  = isset($_POST[$vTitle])?$_POST[$vTitle]:'';			
		}
	}
	
	
	if(isset($_POST['submit'])) { //form submit
	  if(!empty($faq_cat_id)){
          if(SITE_TYPE=='Demo')
          {
            header("Location:cancel_action.php?id=".$id."&success=2");
            exit;
          }
          
      }
		
		//echo "<pre>";print_r($_REQUEST);echo '</pre>'; echo $temp_order.'=='.$iDisplayOrder;
		if($temp_order > $iDisplayOrder) { 
			for($i = $temp_order; $i >= $iDisplayOrder; $i--) { 
				 $sql="UPDATE ".$tbl_name." SET iSortId = ".($i+1)." WHERE iSortId = ".$i;
				$obj->sql_query($sql);
			}
			} else if($temp_order < $iDisplayOrder) {
			for($i = $temp_order; $i <= $iDisplayOrder; $i++) {
				$sql="UPDATE ".$tbl_name." SET iSortId = ".($i-1)." WHERE iSortId = ".$i;
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
			$where = " WHERE `iCancelReasonId` = '".$id."'";
			
		}
		$sql_str = '';
		if($count_all > 0) {
			for($i=0;$i<$count_all;$i++) {	
				$vTitle = 'vTitle_'.$db_master[$i]['vCode'];
				$sql_str .= $vTitle." = '".$$vTitle."',";				
			}
		}
		
		$query = $q ." `".$tbl_name."` SET 	
				".$sql_str."
				`eStatus` = '".$eStatus."',
				`eAllowedCharge` = '".$eAllowedCharge."',
				`iSortId` = '".$iDisplayOrder."'"
				.$where;
				$obj->sql_query($query);
			
		$id = ($id != '')?$id:$obj->GetInsertId();
		#echo"<pre>";print_r($id);exit;
		header("Location:cancel_action.php?id=".$id."&success=1");
	}
		
		
	// for Edit
	if($action == 'Edit') { 
		$sql = "SELECT * FROM ".$tbl_name." WHERE iCancelReasonId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);	
		//echo '<pre>'; print_R($db_data); echo '</pre>'; 
		
		if($count_all > 0) {
			for($i=0;$i<$count_all;$i++) {
				$vTitle = 'vTitle_'.$db_master[$i]['vCode'];
				$$vTitle  = isset($db_data[0][$vTitle])?$db_data[0][$vTitle]:$$vTitle;
				$eStatus = $db_data[0]['eStatus'];
				$eAllowedCharge = $db_data[0]['eAllowedCharge'];
				$iDisplayOrder = $db_data[0]['iSortId'];
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
		<title>Admin | Cancel Reason  <?=$action;?></title>
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
							<h2><?=$action;?> Cancel Reason </h2>
							<a href="cancel.php">
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

							<form method="post" action="" enctype="multipart/form-data">
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<input type="hidden" name="temp_order" id="temp_order" value="1	">
								<?php  
								$sql = "SELECT * FROM language_master WHERE vCode = '".$default_lang."' ORDER BY iDisplayOrder";
								$db_cat = $obj->MySQLSelect($sql);
								//echo "<pre>";print_r($db_cat);exit;
								if(count($db_cat) > 0) { ?>				
								
								
								<?php  if($count_all > 0) {
										for($i=0;$i<$count_all;$i++) {
											$vCode = $db_master[$i]['vCode'];
											$vTitle = $db_master[$i]['vTitle'];
											
											$vTitle_val = "vTitle_".$vCode;
											
											$eDefault = $db_master[$i]['eDefault'];
											
											$required = ($eDefault == 'Yes')?'required':'';
											$required_msg = ($eDefault == 'Yes')?'<span class="red"> *</span>':'';
										?>
										<div class="row">
											<div class="col-lg-12">
												<label><?=$vTitle;?> Cancel Reason <?=$required_msg;?></label>
											</div>
											<div class="col-lg-6">
												<input type="text" class="form-control" name="<?=$vTitle_val;?>"  id="<?=$vTitle_val;?>" value="<?=$$vTitle_val;?>" placeholder="Cancel Reason" <?=$required;?>>
											</div>
										</div>
										<?php  } 
									} ?>
								<?php  if($action == 'Edit') { ?>
								<div class="row">
									<div class="col-lg-12">
										<label>Order</label>
									</div>
									<div class="col-lg-6">
										<?php 
											$temp = 1;
											
											$dataArray = array();
											$query1 = "SELECT iSortId FROM ".$tbl_name." WHERE 1=1 ORDER BY iSortId";
											$data_order = $obj->MySQLSelect($query1);
																						
											foreach($data_order as $value)
											{
												$dataArray[] = $value['iSortId'];
												$temp = $iDisplayOrder;
											}
											
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
								<?php  } else{ ?>
										<div class="row">
											<div class="col-lg-12">
												<label>Order</label>
											</div>
											<div class="col-lg-6">
												<?php 
												$temp = 1;
												
												$dataArray = array();
												
												$query1 = "SELECT max(iSortId) as MAXDORDER FROM ".$tbl_name."  ORDER BY iSortId";
												$data_order_sort = $obj->MySQLSelect($query1);
												foreach($data_order_sort as $value)
												{
													$dataArray[] = $value['MAXDORDER'];
													$temp = $iDisplayOrder;
												}
												?>
												<input type="hidden" name="temp_order" id="temp_order" value="<?=$temp?>">
												<select name="iDisplayOrder" class="form-control">
														<option value="<?=$temp;?>" >
															-- <?= $temp ?> --
														</option>
												</select>
											</div>
										</div>
								<?php  }?>
								<div class="row">
									<div class="col-lg-12">
										<label>Allowed Charge</label>
									</div>
									<div class="col-lg-6">
										<div class="make-switch" data-on="success" data-off="warning">
											<input type="checkbox" name="eAllowedCharge" <?=($id != '' && $eAllowedCharge == 'No')?'':'checked';?>/>
											</div>
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
											<input type="submit" class="save btn-info" name="submit" id="submit" value="<?=$action;?> Cancel Reason">
										</div>
									</div>
								<?php  } else { ?>
									Please enter Cancelation reason 
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
		<script src="../assets/js/editorInit.js"></script>
		<script>
			$(function () { formWysiwyg(); });
		</script>
	</body>
	<!-- END BODY-->    
</html>