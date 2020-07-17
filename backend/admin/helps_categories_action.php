<?php 
	include_once('../common.php');
	
	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	
	require_once(TPATH_CLASS."Imagecrop.class.php");
	
	$default_lang = $generalobj->get_default_lang();
	$id 		= isset($_REQUEST['id'])?$_REQUEST['id']:''; // iUniqueId
	$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
	$action 	= ($id != '')?'Edit':'Add';
	
	//$temp_gallery = $tconfig["tpanel_path"];
	$tbl_name 		= 'helps_categories';
	$script 		= 'Hepls Category';
	
	
	
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
    $previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	
	// set all variables with either post (when submit) either blank (when insert)	
	$eStatus_check 	= isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	
	$eStatus 		= ($eStatus_check == 'on')?'Active':'Inactive';
	$thumb = new thumbnail();
	/* to fetch max iDisplayOrder from table for insert */
	$select_order	= $obj->MySQLSelect("SELECT MAX(iDisplayOrder) AS iDisplayOrder FROM ".$tbl_name);
	$iDisplayOrder	= isset($select_order[0]['iDisplayOrder'])?$select_order[0]['iDisplayOrder']:0;
	$iDisplayOrder	= $iDisplayOrder + 1; // Maximum order number
	
	$iDisplayOrder	= isset($_POST['iDisplayOrder'])?$_POST['iDisplayOrder']:$iDisplayOrder;
	$temp_order 	= isset($_POST['temp_order'])? $_POST['temp_order'] : "";
	$vTitle 	= isset($_POST['vTitle'])? $_POST['vTitle'] : "";	
	$eTopic 	= isset($_POST['eTopic'])? $_POST['eTopic'] : "";	
	
	
	if(isset($_POST['submit'])) { //form submit
		
		if(!empty($id)){
          if(SITE_TYPE=='Demo')
          {
            header("Location:helps_categories_action.php?id=".$id.'&success=2');
            exit;
          }          
      }   
    
    if($temp_order > $iDisplayOrder) {
			for($i = $temp_order; $i >= $iDisplayOrder; $i--) { 
				$obj->sql_query("UPDATE ".$tbl_name." SET iDisplayOrder = ".($i+1)." WHERE iDisplayOrder = ".$i);
			}
			} else if($temp_order < $iDisplayOrder) {
			for($i = $temp_order; $i <= $iDisplayOrder; $i++) {
				$obj->sql_query("UPDATE ".$tbl_name." SET iDisplayOrder = ".($i-1)." WHERE iDisplayOrder = ".$i);
			}
		}
		
		$select_order		= $obj->MySQLSelect("SELECT MAX(iHelpscategoryId) AS iHelpscategoryId FROM ".$tbl_name."");
		$iHelpscategoryId			= isset($select_order[0]['iHelpscategoryId'])?$select_order[0]['iHelpscategoryId']:0;
		$iHelpscategoryId			= $iHelpscategoryId + 1; // Maximum order number	
				
		$q = "INSERT INTO ";
		$where = '';
		
		if($id != '' ){ 
			$q = "UPDATE ";
			$where = " WHERE `iHelpscategoryId` = '".$id."'";
			$iHelpscategoryId = $id;
		}				
		
		$query = $q ." `".$tbl_name."` SET 	
		`vTitle` = '".$vTitle."',				
		`eStatus` = '".$eStatus."',				
		`eTopic` = '".$eTopic."',				
		`iDisplayOrder` = '".$iDisplayOrder."'"
		.$where;
		
		//echo $query; exit;
		$obj->sql_query($query); 
				
			
		//header("Location:faq_categories_action.php?id=".$iUniqueId.'&success=1');
		if ($action == "Add") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Hepls Category Insert Successfully.';
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Hepls Category Updated Successfully.';
        }
		 //header("location:".$backlink);
		 header("Location:helps_categories.php?id=".$iHelpscategoryId.'&success=1');
	}
	
	// for Edit
	if($action == 'Edit') {
		$sql = "SELECT * FROM ".$tbl_name." WHERE iHelpscategoryId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);	
		//echo '<pre>'; print_R($db_data); echo '</pre>'; 
		$iHelpscategoryId = $id;
		if(count($db_data) > 0) {
			foreach($db_data as $key => $value) { 	
			
				$vTitle 			= $value['vTitle'];				
				$eStatus 			= $value['eStatus'];			
				$iDisplayOrder 		= $value['iDisplayOrder'];
				$eTopic 		= $value['eTopic'];
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
		<title>Admin | Hepls Category <?=$action;?></title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
		
		<?php  include_once('global_files.php');?>
		<!-- On OFF switch -->
		<link href="../assets/css/jquery-ui.css" rel="stylesheet" />
		<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />	
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
							<h2><?=$action;?> Hepls Category</h2>
							<a href="helps_categories.php" class="back_link">
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
									Hepls Category Updated successfully.
								</div><br/>
							<?php  } ?>
							 <?php  if ($success == 2) {?>
                 <div class="alert alert-danger alert-dismissable">
                      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                      "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                 </div><br/>
               <?php } ?>
							<form method="post" name="_helps_cat_form" id="_helps_cat_form"  action="" enctype="multipart/form-data">
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="helps_categories.php"/>
								
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
										<label>Order</label>
									</div>
									<div class="col-lg-6">
										<?php 
											$temp = 1;
											
											$dataArray = array();
											
											$query1 = "SELECT iDisplayOrder FROM ".$tbl_name;
											$data_order = $obj->MySQLSelect($query1);
											foreach($data_order as $value)
											{
												$dataArray[] = $value['iDisplayOrder'];
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
								<div class="row">
                                             <div class="col-lg-12">
                                                  <label>Title Name<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="vTitle"  id="vTitle" value="<?= $vTitle; ?>" placeholder="Title Name" >
                                             </div>
                                </div>

								<div class="row">
											<div class="col-lg-12">
												<label>Topic <span class="red"> *</span></label>
											</div>
											<div class="col-lg-6">
												<select class="form-control" name = 'eTopic' id="eTopic">
													<option value="">Select Topic</option>									
													<option value="Front" <?php  echo ($eTopic == "Front") ? 'Selected':'';?>>Front</option>									
													<option value="Admin" <?php  echo ($eTopic == "Admin") ? 'Selected':'';?>>Admin</option>									
													<option value="RiderApp" <?php  echo ($eTopic == "RiderApp") ? 'Selected':'';?>>RiderApp</option>								
													<option value="DriverApp" <?php  echo ($eTopic == "DriverApp") ? 'Selected':'';?>>DriverApp</option>							
													<option value="General" <?php  echo ($eTopic == "General") ? 'Selected':'';?>>General</option>								
													</option>
												
												</select>
											</div>
										</div>
									<div class="row faq-but">
										<div class="col-lg-12">							
											<input type="submit" class="btn btn-default" name="submit" id="submit" value="<?=$action;?> hepls Category">
											<a href="javascript:void(0);" onclick="reset_form('_helps_cat_form');" class="btn btn-default">Reset</a>
                                            <a href="helps_categories.php" class="btn btn-default back_link">Cancel</a>
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
		<script>
$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){ 
		referrer =  document.referrer;
        // alert(referrer);		
	}else { 
		referrer = $("#previousLink").val();
	}
	if(referrer == "") {
		referrer = "page.php";
	}else { 
		$("#backlink").val(referrer);		
	}
	$(".back_link").attr('href',referrer); 	
});
</script>
	</body>
	<!-- END BODY-->    
</html>