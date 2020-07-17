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
	$iHelpscategoryId	= isset($_REQUEST['iHelpscategoryId'])?$_REQUEST['iHelpscategoryId']:'';
	$action 	= ($id != '')?'Edit':'Add';
	
	//$temp_gallery = $tconfig["tpanel_path"];
	$tbl_name 	= 'helps';
	$script 	= 'Helps';	
	
	
	// set all variables with either post (when submit) either blank (when insert)
	$eStatus_check 	= isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
    $previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	
	$eStatus 		= ($eStatus_check == 'on')?'Active':'Inactive';
	$thumb = new thumbnail();
	/* to fetch max iDisplayOrder from table for insert */
	$select_order	= $obj->MySQLSelect("SELECT MAX(iDisplayOrder) AS iDisplayOrder FROM ".$tbl_name);
	$iDisplayOrder	= isset($select_order[0]['iDisplayOrder'])?$select_order[0]['iDisplayOrder']:0;
	$iDisplayOrder	= $iDisplayOrder + 1; // Maximum order number
	
	$iHelpscategoryId	= isset($_POST['iHelpscategoryId'])?$_POST['iHelpscategoryId']:$iHelpscategoryId;
	$iDisplayOrder	= isset($_POST['iDisplayOrder'])?$_POST['iDisplayOrder']:$iDisplayOrder;
	$temp_order 	= isset($_POST['temp_order'])? $_POST['temp_order'] : "";
	$vTitle 	= isset($_POST['vTitle'])? $_POST['vTitle'] : "";
	$tDescription 	= isset($_POST['tDescription'])? $_POST['tDescription'] : "";	
	
	if(isset($_POST['submit'])) { //form submit
	  if(!empty($id)){
          if(SITE_TYPE=='Demo')
          {
            header("Location:helps_action.php?id=".$id."&iHelpsId=".$id."&success=2");
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
		
		$q = "INSERT INTO ";
		$where = '';
		
		if($id != '' ){ 
			$q = "UPDATE ";
			$where = " WHERE `iHelpsId` = '".$id."'";			
		}		
		
		$query = $q ." `".$tbl_name."` SET 	
				`eStatus` = '".$eStatus."',
				`iHelpscategoryId` = '".$iHelpscategoryId."',
				`tDescription` = '".$tDescription."',
				`vTitle` = '".$vTitle."',
				`iDisplayOrder` = '".$iDisplayOrder."'"
				.$where;
				//echo $query;exit;
				$obj->sql_query($query);
			
		$id = ($id != '')?$id:$obj->GetInsertId();		
		if ($action == "Add") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'helps Insert Successfully.';
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'helps Updated Successfully.';
        }
		 header("location:".$backlink);
	}		
		
	// for Edit
	if($action == 'Edit') { 
		$sql = "SELECT * FROM ".$tbl_name." WHERE iHelpsId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);	
		 
		$eStatus 			= $db_data[0]['eStatus'];
		$iDisplayOrder 		= $db_data[0]['iDisplayOrder'];
		$vTitle 		= $db_data[0]['vTitle'];
		$tDescription 		= $db_data[0]['tDescription'];
		$iHelpscategoryId 		= $db_data[0]['iHelpscategoryId'];		
		$iHelpscategoryId 		= $db_data[0]['iHelpscategoryId'];		
		
	}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	
	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin | Helps  <?=$action;?></title>
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
							<h2><?=$action;?> Helps </h2>
							<a href="helps.php" class="back_link">
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

							<form method="post" name="_helps_form" id="_helps_form" action="" enctype="multipart/form-data">
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="helps.php"/>
								<input type="hidden" name="temp_order" id="temp_order" value="1">
								<?php  
								$sql = "SELECT * FROM helps_categories";
								$db_cat = $obj->MySQLSelect($sql);
								
								if(count($db_cat) > 0) { ?>
								<div class="row">
									<div class="col-lg-12">
										<label>Category</label>
									</div>
									<div class="col-lg-6">
										<select name="iHelpscategoryId" id="iHelpscategoryId" class="form-control">
											<?php  for($i=0; $i<count($db_cat); $i++){?>
											<option value="<?=$db_cat[$i]['iHelpscategoryId'];?>" <?=($db_cat[$i]['iHelpscategoryId'] == $iHelpscategoryId)?'selected':'';?>>
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
								<?php  if($action == 'Edit') { ?>
								<div class="row">
									<div class="col-lg-12">
										<label>Order</label>
									</div>
									<div class="col-lg-6">
										<?php 
											$temp = 1;
											
											$dataArray = array();
																						
											$query1 = "SELECT iDisplayOrder FROM ".$tbl_name." WHERE iHelpsId = ".$id." ORDER BY iDisplayOrder";
											$data_orders = $obj->MySQLSelect($query1);
											foreach($data_orders as $value)
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
								<?php  } else{ ?>
										<div class="row">
											<div class="col-lg-12">
												<label>Order</label>
											</div>
											<div class="col-lg-6">
												<?php 
												$temp = 1;
												$dataArray = array();
												
												$query1 = "SELECT max(iDisplayOrder) as MAXDORDER FROM ".$tbl_name;
												$data_order_one = $obj->MySQLSelect($query1);
												foreach($data_order_one as $value)
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
								<?php  } ?>
								
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
                                                  <label>Description<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <textarea  class="form-control" name="tDescription"  id="tDescription" placeholder="Desciption" rows="10" ><?=$tDescription;?></textarea>
                                             </div>
                                </div>
								
									<div class="row">
										<div class="col-lg-12">							
											<input type="submit" class="btn btn-default" name="submit" id="submit" value="<?=$action;?> Helps">
											<a href="javascript:void(0);" onclick="reset_form('_helps_form');" class="btn btn-default">Reset</a>
                                            <a href="helps.php" class="btn btn-default back_link">Cancel</a>
										</div>
									</div>
								<?php  } else { ?>
									Please enter Helps Catgory
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
			$(function () { formWysiwyg(); });
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
		referrer = "helps.php";
	}else { 
		$("#backlink").val(referrer);		
	}
	$(".back_link").attr('href',referrer); 	
});
</script>	
</html>