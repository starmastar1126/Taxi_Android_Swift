<?php 
	include_once('../common.php');
	$type = $_REQUEST['type'];
	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();

	$default_lang 	= $generalobj->get_default_lang();
	$hdn_del_id 	= isset($_POST['hdn_del_id'])?$_POST['hdn_del_id']:'';
	$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
	$tbl_name 		= 'language_page_details';
	$script			= "Language Settings";

	if($hdn_del_id != ''){
			if(SITE_TYPE !='Demo'){
		$query = "DELETE FROM `".$tbl_name."` WHERE vLabel = '".$hdn_del_id."'";
		$obj->sql_query($query);
	}
	else{
		header("Location:languages.php?success=2");exit;
	}
	}
	$db_data = array();
	if(isset($type) && $type != "") {
		$sql = "SELECT * FROM ".$tbl_name." WHERE lp_type = '".$type."' ORDER BY lp_id ASC";
		$db_results = $obj->MySQLSelect($sql);

	
		if(!empty($db_results)) {
			foreach($db_results as $db_datas) {
				$sql1 = "SELECT count(lPage_id) as Total FROM language_label WHERE vCode = '".$default_lang."' AND lPage_id = '".$db_datas['lp_id']."' GROUP BY 'vLabel'";
				$db_data1 = $obj->MySQLSelect($sql1);
				#echo '<pre>'; print_R($sql1); echo '</pre>';die;
				$db_datas['total'] = $db_data1[0]['Total'];
				$db_data[] = $db_datas;
			}
		}
	}
		 
	#echo '<pre>'; print_R($db_results); echo '</pre>';
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin | Development Language</title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<meta content="" name="keywords" />
		<meta content="" name="description" />
		<meta content="" name="author" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

		<?php  include_once('global_files.php');?>
		<script type="text/javascript">
			function confirm_delete()
			{
				var confirm_ans = confirm("Are You sure You want to Delete Language Label?");
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
							<h2><?=ucfirst($type); ?> Development</h2>
						</div>
					</div>
					<hr />
					<?php  if ($success == 2) { ?>
							<div class="alert alert-danger alert-dismissable">
									 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									 "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
							</div><br/>
						<?php  } ?>
					<div class="table-list">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										Language Label
									</div>
									<div class="panel-body">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover" id="dataTables-example">
												<thead>
													<tr>
														<th>Page Name</th>
														<!---
														<th>Page Link</th>
														--->
														<th>No Of Labels</th>
														<th>Description</th>
														<th>Add</th>
													</tr>
												</thead>
												<tbody>
													<tr class="gradeA">
														<td>All Labels</td>
														<td align="center">
															<?php 
																$sql="select count(vLabel) as totlabels from language_label WHERE 1=1 and vCode = '".$default_lang."' group by 'vLabel' ";
																$total_count = $obj->MySQLSelect($sql); 
																
																echo $total_count[0]['totlabels'];
															?>
														</td>
														<td>View All Labels</td>
														<td>
															<a href="languages.php">
																<button class="btn btn-primary">
																	<i class="icon-pencil icon-white"></i> Add / View Labels
																</button>
															</a>
														</td>
													</tr>
													<?php 
														$count_all = count($db_data);
														if($count_all > 0) {
															for($i=0;$i<$count_all;$i++) {
																$lp_id = $db_data[$i]['lp_id'];
																$lp_name = $db_data[$i]['lp_name'];
																$lp_link = $db_data[$i]['lp_link'];
																$total = $db_data[$i]['total'];
																if($total == ""){
																	$total= 0;
																}
																$vDescription = $db_data[$i]['vDescription'];
															?>
															<tr class="gradeA">
																<td width="30%"><?=$lp_name;?></td>
																<!---
																<td width="40%"><?=$lp_link;?></td>
																--->
																<td width="11%" align="center"><?=$total;?></td>
																<td width="45%"><?=$vDescription;?></td>
																
																<td class="center" >
																	<a href="languages.php?lp_id=<?=$lp_id;?>&lp_name=<?= $db_data[$i]['lp_name']; ?>">
																		<button class="btn btn-primary">
																			<i class="icon-pencil icon-white"></i> Add / View Labels
																		</button>
																	</a>
																</td>
															</tr>
															<?php  }
														} ?>
														<tr class="gradeA">
														<td>Other Labels</td>
														<td align="center">
															<?php 
																$sql="select count(vLabel) as tot_label from language_label WHERE lPage_id = '0' and vCode = '".$default_lang."' group by 'vLabel' ";
																$total_label = $obj->MySQLSelect($sql); 
																
																echo $total_label[0]['tot_label'];
															?>
														</td>
														<td>View Other Labels</td>
														<td>
															<a href="languages.php?lp_id=0&lp_name=Other">
																<button class="btn btn-primary">
																	<i class="icon-pencil icon-white"></i> Add / View Labels
																</button>
															</a>
														</td>
													</tr>
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
				/// DISABLED TO SHOW ALL PAGES
				//$('#dataTables-example').dataTable();
				/// DISABLED TO SHOW ALL PAGES
			});
		</script>
	</body>
	<!-- END BODY-->
</html>
