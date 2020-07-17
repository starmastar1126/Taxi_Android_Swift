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
	$iUniqueId 		= isset($_GET['iUniqueId'])?$_GET['iUniqueId']:''; 
	$status 		= isset($_GET['status'])?$_GET['status']:'';
	//sort order 
	$flag 			= isset($_GET['flag'])?$_GET['flag']:'';
	$id 			= isset($_GET['id'])?$_GET['id']:'';
	
	$tbl_name 		= 'banners';
	$script 		= 'Banner';
	
	//delete record
	if($hdn_del_id != ''){
		$data_q 	= "SELECT Max(iDisplayOrder) AS iDisplayOrder FROM `".$tbl_name."` WHERE vCode = '".$default_lang."'";
		$data_rec  	= $obj->MySQLSelect($data_q);
		//echo '<pre>'; print_r($data_rec); echo '</pre>';
		$order = isset($data_rec[0]['iDisplayOrder'])?$data_rec[0]['iDisplayOrder']:0;
		
		$data_logo =  $obj->MySQLSelect("SELECT iDisplayOrder FROM ".$tbl_name." WHERE iUniqueId = '".$hdn_del_id."' AND vCode = '".$default_lang."'");
		
		if(count($data_logo) > 0)
		{
			$iDisplayOrder =  isset($data_logo[0]['iDisplayOrder'])?$data_logo[0]['iDisplayOrder']:'';
			$obj->sql_query("DELETE FROM `".$tbl_name."` WHERE iUniqueId = '".$hdn_del_id."'");
			
			if($iDisplayOrder < $order)
			for($i = $iDisplayOrder+1; $i <= $order; $i++)
			$obj->sql_query("UPDATE ".$tbl_name." SET iDisplayOrder = ".($i-1)." WHERE iDisplayOrder = ".$i);
		}
	}
	
	if($id != 0) {
		if($flag == 'up')
		{
			$sel_order = $obj->MySQLSelect("SELECT iDisplayOrder FROM ".$tbl_name." WHERE iUniqueId ='".$id."' AND vCode = '".$default_lang."'");
			$order_data = isset($sel_order[0]['iDisplayOrder'])?$sel_order[0]['iDisplayOrder']:0;
			$val = $order_data - 1;
			if($val > 0) {
				$obj->MySQLSelect("UPDATE ".$tbl_name." SET iDisplayOrder='".$order_data."' WHERE iDisplayOrder='".$val."'");
				$obj->MySQLSelect("UPDATE ".$tbl_name." SET iDisplayOrder='".$val."' WHERE iUniqueId = '".$id."'");
			}
		}
		
		else if($flag == 'down')
		{
			$sel_order = $obj->MySQLSelect("SELECT iDisplayOrder FROM ".$tbl_name." WHERE iUniqueId ='".$id."' AND vCode = '".$default_lang."'");
			
			$order_data = isset($sel_order[0]['iDisplayOrder'])?$sel_order[0]['iDisplayOrder']:0;
			
			$val = $order_data+ 1;
			$obj->MySQLSelect("UPDATE ".$tbl_name." SET iDisplayOrder='".$order_data."' WHERE iDisplayOrder='".$val."'");
			$obj->MySQLSelect("UPDATE ".$tbl_name." SET iDisplayOrder='".$val."' WHERE iUniqueId = '".$id."'");
		}
		header("Location:banner.php");
	}
	
	if($iUniqueId != '' && $status != ''){
		$query = "UPDATE `".$tbl_name."` SET eStatus = '".$status."' WHERE iUniqueId = '".$iUniqueId."'";
		$obj->sql_query($query);
	}
	
	$sql = "SELECT * FROM ".$tbl_name." WHERE vCode = '".$default_lang."' ORDER BY iDisplayOrder";
	$db_data = $obj->MySQLSelect($sql);
	//echo '<pre>'; print_R($db_data); echo '</pre>';
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	
	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin | Banners</title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<?php  include_once('global_files.php');?>
		
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
		<script type="text/javascript">
			function confirm_delete()
			{
				var confirm_ans = confirm("Are You sure You want to Delete Banner?");
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
							<h2>Banner</h2>
							<a href="banner_action.php">
								<input type="button" value="Add Banner" class="add-btn">
							</a>
						</div>
					</div>
					<hr />
					<div class="table-list">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										Banner
									</div>
									<div class="panel-body">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover" id="dataTables-example">
												<thead>
													<tr>
														<th>Image</th>
														<th>Title</th>
														<th>Order</th>
														<th>Status</th>
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
																$vImage 		= $db_data[$i]['vImage']; 
																$iDisplayOrder 	= $db_data[$i]['iDisplayOrder']; 
																$eStatus 		= $db_data[$i]['eStatus'];
																$iUniqueId 		= $db_data[$i]['iUniqueId'];
																$checked		= ($eStatus=="Active")?'checked':'';
															?>
															<tr class="gradeA">
																<td width="10%" align="center">
																	<?php  if($vImage != '' && file_exists($tconfig['tsite_upload_images_panel'].'/'.$vImage)) { ?>
																		<img src="<?=$tconfig['tsite_upload_images'].$vImage;?>" height="50" width="50">
																	<?php  } else echo $vImage; ?>
																</td>
																<td><?=$vTitle;?></td>
																<td width="10%" align="center">
																	<?php  if($iDisplayOrder != 1) { ?>
																		<a href="banner.php?id=<?=$iUniqueId;?>&flag=up">
																			<button class="btn btn-warning">
																				<i class="icon-arrow-up"></i> 
																			</button>
																		</a>
																		<?php  } if($iDisplayOrder != $count_all) { ?>
																		<a href="banner.php?id=<?=$iUniqueId;?>&flag=down">
																			<button class="btn btn-warning">
																				<i class="icon-arrow-down"></i> 
																			</button>
																		</a>
																	<?php  } ?>
																	
																</td>
																<td width="10%" align="center">
																	<a href="banner.php?iUniqueId=<?=$iUniqueId;?>&status=<?=($eStatus=="Active")?'Inactive':'Active'?>">
																		<!-- <button class="btn <?=($eStatus=="Active")?'btn-success':'btn-danger'?>"> -->
																		<button class="btn">
																			<i class="<?=($eStatus=="Active")?'icon-eye-open':'icon-eye-close'?>"></i> <?=$eStatus;?>
																		</button>
																	</a>
																</td>
																<td width="10%" align="center">
																	<a href="banner_action.php?id=<?=$iUniqueId;?>">
																		<button class="btn btn-primary">
																			<i class="icon-pencil icon-white"></i> Edit
																		</button>
																	</a>
																</td>
																<td width="10%" align="center">
																	<!-- <a href="languages.php?id=<?=$id;?>&action=delete"><i class="icon-trash"></i> Delete</a>-->
																	<form name="delete_form" id="delete_form" method="post" action="" onsubmit="return confirm_delete()" class="margin0">
																		<input type="hidden" name="hdn_del_id" id="hdn_del_id" value="<?=$iUniqueId;?>">
																		<button class="btn btn-danger">
																			<i class="icon-remove icon-white"></i> Delete
																		</button>
																	</form>
																</td>
															</tr>
															<?php  } 
														}  ?>
												</tbody>
											</table>
										</div>
										
									</div>
								</div>
							</div> <!--TABLE-END-->
						</div>
					</div> 
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
