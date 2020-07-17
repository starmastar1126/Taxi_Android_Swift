<?php 
	include_once('../common.php');
	
	if (!isset($generalobjAdmin)) {
		require_once(TPATH_CLASS . "class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	
	$script = '';  	
	
	$id =$_REQUEST['id']; 
	
	$query = "SELECT *
	FROM language_page_details 
	WHERE lp_id = '".$id."'";			
	$result_page_details = $obj->MySQLSelect($query); 
	
	$query = "SELECT vLabel, vCode 
	FROM language_label 
	WHERE lPage_id = '".$id."' 
	AND language_label.vCode = (SELECT vCode FROM language_master WHERE eDefault = 'Yes' LIMIT 1)";			
	$result_all = $obj->MySQLSelect($query); 
	//echo '<pre>'; print_R($result_all); print_R($result_page_details); echo '</pre>';
	
	$query = "SELECT vTitle, vCode FROM language_master WHERE eStatus = 'Active' ORDER BY iDispOrder";
	$tot_lang = $obj->MySQLSelect($query); 
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	
	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin | <?=ucfirst($result_page_details[0]['lp_type'])." ".$result_page_details[0]['lp_name'];?></title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
		<?php  include_once('global_files.php');?>
	</head>
	<!-- END  HEAD-->
	<!-- BEGIN BODY-->
	<body class="padTop53">
		
		<!-- MAIN WRAPPER -->
		<div id="wrap">
			<?php  include_once('header.php'); ?>
			<?php  include_once('left_menu.php'); ?>
			
			<!--PAGE CONTENT -->
			<div id="content">
				<div class="inner">
					
					<div class="row">
						<div class="col-lg-12">
							<h2><?=ucfirst($result_page_details[0]['lp_type'])." >> ".$result_page_details[0]['lp_name'];?></h2>
							<a href="lang_all.php?tab=<?=$result_page_details[0]['lp_type'];?>"> 
								<input type="button" value="Back to Listing" class="add-btn"> 
							</a>
						</div>
					</div>
					<hr />                       
					<div class="table-list">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										<?=$result_page_details[0]['lp_name'];?> Screen page
									</div>
									<div class="panel-body">
										<a title="<?=$result_page_details[0]['lp_name'];?>" href="#">
											<img class="thumbnail img-responsive" src="img/screen/<?=$result_page_details[0]['lp_id'];?>.PNG">
										</a>
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover" id="dataTables-example">
												<thead>
													<tr>
														<th width="35%">
															Default value
														</th>
														<?php  foreach($tot_lang as $val) { ?>
														<th>
															<?=$val['vLabel'];?> Value
														</th>
														<?php  } ?>
													</tr>
												</thead>
												<tbody>										 
													
													<?php  
														$count = count($result_driver);
														if($count > 0){
															for($i=0;$i<count($result_driver);$i++){ ?>										 
															
															<tr class="gradeA">
																<td ><?=$result_driver[$i]['vName'];?> <?= $result_driver[$i]['vLastName'];?></td> 
																<?php  
																	$time = strtotime($result_driver[$i]['dRefDate']);
																	$myFormatForView = date("jS F Y", $time);																	 
																?>
																<td><?= $myFormatForView ?></td> 
																<!--<td>$ <?=$result_driver[$i]['iBalance']?></td> -->
															</tr>																 
															
															<?php   }
															
														}else{ ?>																 
														<tr class="gradeA">
															<td colspan ="3" align="center"> No Details Found </td> 
														</tr>
														
													<?php   } 	?>                                                               
													
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
		</div>
		
		<!--END PAGE CONTENT -->
	</div>
	<!--END MAIN WRAPPER -->
	
	
	<?php  include_once('footer.php');?>
	<script>
		$(document).ready(function() {
			$('.thumbnail').click(function(){
				$('.modal-body').empty();
				var title = $(this).parent('a').attr("title");
				$('.modal-title').html(title);
				$($(this).parents('div').html()).appendTo('.modal-body');
				$('#myModal').modal({show:true});
			});
		});
	</script>
</body>
<!-- END BODY-->
</html>
