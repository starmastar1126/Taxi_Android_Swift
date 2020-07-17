<?php 
	include_once('../common.php');
	
	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	
	$default_lang 	= $generalobj->get_default_lang();
	
	//Delete
	$hdn_del_id 	= isset($_POST['hdn_del_id'])?$_POST['hdn_del_id']:'';
	// Update eStatus 
	$iCancelReasonId= isset($_GET['iCancelReasonId'])?$_GET['iCancelReasonId']:''; 
	$status 		= isset($_GET['status'])?$_GET['status']:'';
	$AllowedCharge 		= isset($_GET['AllowedCharge'])?$_GET['AllowedCharge']:'';
	//sort order 
	$flag 			= isset($_GET['flag'])?$_GET['flag']:'';
	$id 			= isset($_GET['id'])?$_GET['id']:'';
	
	$tbl_name 		= 'cancel_reason';
	$script 		= 'Page';
	
	//delete record
	if($hdn_del_id != ''){ 
	  if(SITE_TYPE =='Demo'){
	     header("Location:faq.php?success=2");exit;
	  }
		$data_q 	= "SELECT Max(iSortId) AS iSortId FROM `".$tbl_name."`";
		$data_rec  	= $obj->MySQLSelect($data_q);
		//echo '<pre>'; print_r($data_rec); echo '</pre>';
		$order = isset($data_rec[0]['iSortId'])?$data_rec[0]['iSortId']:0;
		
		$data_logo =  $obj->MySQLSelect("SELECT iSortId FROM ".$tbl_name." WHERE iCancelReasonId = '".$hdn_del_id."'");
		
		if(count($data_logo) > 0)
		{
			$iDisplayOrder =  isset($data_logo[0]['iSortId'])?$data_logo[0]['iSortId']:'';
			$obj->sql_query("DELETE FROM `".$tbl_name."` WHERE iCancelReasonId = '".$hdn_del_id."'");
			
			if($iDisplayOrder < $order)
			for($i = $iDisplayOrder+1; $i <= $order; $i++)
			$obj->sql_query("UPDATE ".$tbl_name." SET iSortId = ".($i-1)." WHERE iSortId = ".$i);
		}
	}
	
	if($id != 0) {
		if($flag == 'up')
		{
			$sel_order = $obj->MySQLSelect("SELECT iSortId FROM ".$tbl_name." WHERE iCancelReasonId ='".$id."'");
			$order_data = isset($sel_order[0]['iSortId'])?$sel_order[0]['iSortId']:0;
			$val = $order_data - 1;
			if($val > 0) {
				$obj->MySQLSelect("UPDATE ".$tbl_name." SET iSortId='".$order_data."' WHERE iSortId='".$val."'");
				$obj->MySQLSelect("UPDATE ".$tbl_name." SET iSortId='".$val."' WHERE iCancelReasonId = '".$id."'");
			}
		}
		
		else if($flag == 'down')
		{
			$sel_order = $obj->MySQLSelect("SELECT iSortId FROM ".$tbl_name." WHERE iCancelReasonId ='".$id."'");
			
			$order_data = isset($sel_order[0]['iSortId'])?$sel_order[0]['iSortId']:0;
			
			$val = $order_data+ 1;
			$obj->MySQLSelect("UPDATE ".$tbl_name." SET iSortId='".$order_data."' WHERE iSortId='".$val."'");
			$obj->MySQLSelect("UPDATE ".$tbl_name." SET iSortId='".$val."' WHERE iCancelReasonId = '".$id."'");
		}
		header("Location:cancel.php");
	}
	
	if($iCancelReasonId	 != '' && $status != ''){
		$query = "UPDATE `".$tbl_name."` SET eStatus = '".$status."' WHERE iCancelReasonId	 = '".$iCancelReasonId	."'";
		$obj->sql_query($query);
	}

	if($iCancelReasonId	 != '' && $AllowedCharge != ''){
		$query = "UPDATE `".$tbl_name."` SET eAllowedCharge = '".$AllowedCharge."' WHERE iCancelReasonId	 = '".$iCancelReasonId	."'";
		$obj->sql_query($query);
	}
	
	$sql = "SELECT vCode FROM language_master WHERE eDefault = 'Yes'";
	$lang_mast = $obj->MySQLSelect($sql);
	$defcode=$lang_mast[0]['vCode'];
	
	 $sql = "SELECT iCancelReasonId, vTitle_".$defcode." as vTitle, iSortId, eStatus, eAllowedCharge FROM ".$tbl_name." WHERE 1=1 ORDER BY iCancelReasonId";
	$db_data = $obj->MySQLSelect($sql);
	#echo '<pre>'; print_R($db_data); echo '</pre>';	exit;
	
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	
	<!-- BEGIN HEAD-->
	<head>
	
		<meta charset="UTF-8" />
		<title>Admin | Cancel Reason</title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<meta content="" name="keywords" />
		<meta content="" name="description" />
		<meta content="" name="author" />	
		<?php  include_once('global_files.php');?>
		
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
		<script type="text/javascript">
			function confirm_delete()
			{
				var confirm_ans = confirm("Are You sure You want to Delete FAQ?");
				return confirm_ans;
				//document.getElementById(id).submit();
			}
		</script>
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
							<h2>Cancel Reason </h2>
							<a href="cancel_action.php">
								<input type="button" value="Add Cancel Reason " class="add-btn">
							</a>
						</div>
					</div>
					<hr />
					<div class="table-list">
						<div class="row">
							<div class="col-lg-12">
							   <?php  if ($_GET['success'] == 2) {?>
                   <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">�</button>
                        "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                   </div><br/>
                   <?php } ?>
								<div class="panel panel-default">
									<div class="panel-heading">
										Cancel Reason 
									</div>
									<div class="panel-body">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover" id="dataTables-example">
												<thead>
													<tr>
														<th>Title</th>
														<th>Allow Charge</th>
														<th>Status</th>
														<th>Order</th>
														<th>Edit</th>
														<th>Delete</th>
													</tr>
												</thead>
												<tbody>
													<?php  
														$count_all = count($db_data);
														if($count_all > 0) {
															for($i=0;$i<$count_all;$i++) {
																$vTitle 		= $db_data[$i]['vTitle']; 
																$eAllowedCharge 		= $db_data[$i]['eAllowedCharge']; 
																$iDisplayOrder 	= $db_data[$i]['iSortId']; 
																$eStatus 		= $db_data[$i]['eStatus'];
																$iCancelReasonId = $db_data[$i]['iCancelReasonId'];
																$checked		= ($eStatus=="Active")?'checked':'';
															?>
															<tr class="gradeA">
																<td><?=$vTitle;?></td>
																<!-- <td><?=$eAllowedCharge;?></td> -->
																<td width="10%" align="center">
																	<a href="cancel.php?iCancelReasonId=<?=$iCancelReasonId;?>&AllowedCharge=<?=($eAllowedCharge=="Yes")?'No':'Yes'?>">
																		
																		<button class="btn">
																			<i class="<?=($eAllowedCharge=="Yes")?'icon-eye-open':'icon-eye-close'?>"></i> <?=$eAllowedCharge;?>
																		</button>
																	</a>
																</td>
																<td width="10%" align="center">
																	<a href="cancel.php?iCancelReasonId=<?=$iCancelReasonId;?>&status=<?=($eStatus=="Active")?'Inactive':'Active'?>">
																		<!-- <button class="btn <?=($eStatus=="Active")?'btn-success':'btn-danger'?>"> -->
																		<button class="btn">
																			<i class="<?=($eStatus=="Active")?'icon-eye-open':'icon-eye-close'?>"></i> <?=$eStatus;?>
																		</button>
																	</a>
																</td>
																<td width="10%" align="center">
																	<?php  if($iDisplayOrder != 1) { ?>
																		<a href="cancel.php?id=<?=$iCancelReasonId;?>&flag=up">
																			<button class="btn btn-warning">
																				<i class="icon-arrow-up"></i> 
																			</button>
																		</a>
																		<?php  } if($iDisplayOrder != $count_all) { ?>
																		<a href="cancel.php?id=<?=$iCancelReasonId;?>&flag=down">
																			<button class="btn btn-warning">
																				<i class="icon-arrow-down"></i> 
																			</button>
																		</a>
																	<?php  } ?>
																	
																</td>
																
																<td width="10%" align="center">
																	<a href="cancel_action.php?id=<?=$iCancelReasonId;?>">
																		<button class="btn btn-primary">
																			<i class="icon-pencil icon-white"></i> Edit
																		</button>
																	</a>
																</td>
																<td width="10%" align="center">
																	<!-- <a href="languages.php?id=<?=$id;?>&action=delete"><i class="icon-trash"></i> Delete</a>-->
																	<form name="delete_form" id="delete_form" method="post" action="" onSubmit="return confirm_delete()" class="margin0">
																		<input type="hidden" name="hdn_del_id" id="hdn_del_id" value="<?=$iCancelReasonId;?>">
																		<button class="btn btn-danger">
																			<i class="icon-remove icon-white"></i> Delete
																		</button>
																	</form>
																</td>
															</tr>
															<?php  } 
														} else { ?>
														<tr class="gradeA">
															<td colspan="6">No Records found.</td>
														</tr>
													<?php  } ?>
												</tbody>
											</table>
										</div>
										
									</div>
								</div>
							</div> <!--TABLE-END-->
						</div>
					</div>
                    <div class="clear"></div>
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>
		<!--END MAIN WRAPPER -->
		
		<?php  include_once('footer.php');?>
		
		<script src="../assets/plugins/dataTables/jquery.dataTables.js"></script>
		<script src="../assets/plugins/dataTables/dataTables.bootstrap.js"></script>
		<script>
			$(document).ready(function () {
				$('#dataTables-example').dataTable( {"bSort": false } );
			});
		</script>
	</body>
	<!-- END BODY-->    
</html>
