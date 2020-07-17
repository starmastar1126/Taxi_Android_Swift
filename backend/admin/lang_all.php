<?php 
	include_once('../common.php');
	
	if ($REFERRAL_SCHEME_ENABLE == "No") {
		header('Location: dashboard.php');
		exit;
	}
	
	if (!isset($generalobjAdmin)) {
		require_once(TPATH_CLASS . "class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	$script = '';
	
	
	$sql = "SELECT DISTINCT language_page_details.lp_id, count( * ) AS cnt, lp_name, lp_link, lp_type, vDescription
	FROM `language_label` JOIN `language_page_details`
	ON `language_label`.`lPage_id` = `language_page_details`.`lp_id`
	WHERE language_label.vCode = (SELECT vCode FROM language_master WHERE eDefault = 'Yes' LIMIT 1)
	GROUP BY language_page_details.lp_id";
	$totalData = $obj->MySQLSelect($sql);
	
	$endRecord 	= count($totalData);
	
	$driver 	= array();
	$rider 		= array();
	$web 		= array();
	$other	 	= array();
	
	$tab  = isset($_REQUEST['tab'])?$_REQUEST['tab']:'web';
	
	foreach ($totalData as $key=>$val) { 
		
		if($val['lp_type'] == 'web') {
			$web[] = $val;
		}
		else if($val['lp_type'] == 'rider') {
			$rider[] = $val;
		}
		else if($val['lp_type'] == 'driver') {
			$driver[] = $val;
		}  
		else
		$other[] = $val;
	} 
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?=$SITE_NAME?> | Language Handling</title>
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
                    <div id="add-hide-show-div">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2>Langunage Handling</h2>
							</div>
						</div>
                        <hr />
					</div> 
					<!-- Tab Menu -->
					<ul class="nav nav-tabs">
						<li <?=($tab =='web')?'class="active"':'';?>>
							<a data-toggle="tab" href="#webmenu">Web Menu</a>
						</li>
						<li <?=($tab =='rider')?'class="active"':'';?>>
							<a data-toggle="tab" href="#riderapp">Rider App</a>
						</li>
						<li <?=($tab =='driver')?'class="active"':'';?>>
							<a data-toggle="tab" href="#menu2">Driver App</a>
						</li> 
					</ul>
					<div class="tab-content">
						<div id="webmenu" class="tab-pane fade <?=($tab =='web')?'in active':'';?>">
							<h3>Web Languange Handling</h3>
							<BR><BR> 
							
							<?php  if (SITE_TYPE != 'Demo') { ?>
								<?php  if(!isset($_SESSION['sess_editingToken'])){ ?>
									<p>For Easy editing click "Enable Online Web Editing"</p>
									<?php  } else { ?> 
									<p>To disable Easy editing click "Disable Online Web Editing"</p>
								<?php  } ?>
								<?php  if(!isset($_SESSION['sess_editingToken'])){ ?>
									<a href="lang_all_change.php?type=enable&platform=web" class="btn btn-primary">Enable Online Web Editing</a>
									<?php  }else { ?>
									<a href="lang_all_change.php?type=disable&platform=web" class="btn btn-danger">Disable Online Web Editing</a>  <a href="<?php  echo $tconfig['tsite_url']; ?>" target="_blank" class="btn btn-primary">View Website</a> 
								<?php  } ?> 
							<?php  } ?>
						</div>
						<div id="riderapp" class="tab-pane fade <?=($tab =='rider')?'in active':'';?>">
							<h3>Rider App Languange Handling</h3>
							<p>Below are the pages of Rider app. Click on view button to edit the wording of each page.</p>
							
							<!-- 
							<?php  
								if(count($rider) > 0)  { ?>
								<table class="table table-striped">
									<thead>
										<tr>
											<th>#</th>
											<th>Page Image</th>
											<th>Page Name</th>
											<th>Label Count</th>
											<th>Page Desc</th>
											<th>View</th>
										</tr>
									</thead>								
									<tbody>
										<?php 
											$i = 1;
											foreach ($rider as $key=>$val) {  
											?> 
											<tr>
												<td><?=$i++;?></td>
												<td width="100px;" height="150px;"> 
													<div class="row">
														<div class="col-sm-12">
															<a title="Image 1" href="#">
																<img class="thumbnail img-responsive" src="img/screen/<?=$val['lp_id'];?>.PNG">
															</a>
														</div> 
														
													</div>
												</td>
												<td><?=$val['lp_name'];?></td>
												<td><?=$val['cnt'];?></td>
												<td><?=$val['vDescription'];?></td>
												<td><a href="lang_all_action.php?id=<?=$val['lp_id'];?>" class="btn btn-primary">View</a></td>
											</tr>
										<?php  } ?>
									</tbody>
								</table>
							<?php  } ?> 
							-->
							<?php  
								if(count($rider) > 0)  { ?>
								<div class="lang-part">
									<ul>
										<?php 
											$i = 1;
											foreach ($rider as $key=>$val) {  
											?>
											<li>
												<label> 
													<div class="container">
														<div class="row">
															<div class="col-sm-12">
																<a title="<?=$val['lp_name'];?>" href="#">
																	<img class="thumbnail img-responsive" src="img/screen/<?=$val['lp_id'];?>.PNG">
																</a>
															</div> 
															
														</div>
													</div>
												</label>
												<b><?=$i++.". ".$val['lp_name'];?></b>
												<p><?=$val['vDescription'];?></p>
												<span><a href="lang_all_action.php?id=<?=$val['lp_id'];?>" class="btn btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i> Edit <?=$val['cnt'];?> Language</a></span>
											</li>
										<?php  } ?>
									</ul>
								</div>
							<?php  } ?>
						</div>
						<div id="menu2" class="tab-pane fade <?=($tab =='driver')?'in active':'';?>">
							<h3>Driver App Languange Handling</h3>
							<p>Below are the pages of Driver app. Click on view button to edit the wording of each page.</p>
							<?php  
								if(count($driver) > 0)  { ?>
								<div class="lang-part">
									<ul>
										<?php 
											$i = 1;
											foreach ($driver as $key=>$val) {  
											?>
											<li>
												<label> 
													<div class="container">
														<div class="row">
															<div class="col-sm-12">
																<a title="<?=$val['lp_name'];?>" href="#">
																	<img class="thumbnail img-responsive" src="img/screen/<?=$val['lp_id'];?>.PNG">
																</a>
															</div> 
															
														</div>
													</div>
												</label>
												<b><?=$i++.". ".$val['lp_name'];?></b>
												<p><?=$val['vDescription'];?></p>
												<span><a href="lang_all_action.php?id=<?=$val['lp_id'];?>" class="btn btn-primary"><i class="fa fa-pencil" aria-hidden="true"></i> Edit <?=$val['cnt'];?> Language</a></span>
											</li>
										<?php  } ?>
									</ul>
								</div>
							<?php  } ?>
							
							
						</div> 
						<div style="clear:both;"></div>
						<div tabindex="-1" class="modal fade" id="myModal" role="dialog">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button class="close" type="button" data-dismiss="modal">×</button>
										<h3 class="modal-title">Heading</h3>
									</div>
									<div class="modal-body">
										
									</div>
									<div class="modal-footer">
										<button class="btn btn-default" data-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- Test -->
				
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
