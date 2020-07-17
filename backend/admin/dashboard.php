<?php 
	include_once('../common.php');
	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$script = 'site';
	$generalobjAdmin->check_member_login();
	$company 	= $generalobjAdmin->getCompanyDetails();
	$driver 	= $generalobjAdmin->getDriverDetailsDashboard('');
	//$rider 		= $generalobjAdmin->getRiderDetails();

	/*added*/
	$rider_count 		= $generalobjAdmin->getRiderCount();
	$rider = $rider_count[0]['count(iUserId)'];


	//$vehicle	= $generalobjAdmin->getVehicleDetails();
	//$trips		= $generalobjAdmin->getTripsDetails();
	$totalEarns	= $generalobjAdmin->getTotalEarns();
	$totalRides = $generalobjAdmin->getTripStates('total');
	$onRides = $generalobjAdmin->getTripStates('on ride');
	$finishRides = $generalobjAdmin->getTripStates('finished');
	$cancelRides = $generalobjAdmin->getTripStates('cancelled');
	$actDrive = $generalobjAdmin->getDriverDetailsDashboard('active');
	$inaDrive = $generalobjAdmin->getDriverDetailsDashboard('inactive');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	<!-- BEGIN HEAD-->
    	<head>
		<meta charset="UTF-8" />
		<title><?=$SITE_NAME;?> | Dashboard</title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<!--[if IE]>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<![endif]-->
		<!-- GLOBAL STYLES -->
		<?php  include_once('global_files.php');?>
		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="css/new_main.css" />
		<link rel="stylesheet" href="css/adminLTE/AdminLTE.min.css" />
		<script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
		<script type="text/javascript" src="js/plugins/morris/raphael-min.js"></script>
        <script type="text/javascript" src="js/plugins/morris/morris.min.js"></script> 
		<script type="text/javascript" src="js/actions.js"></script>
        <!-- END THIS PAGE PLUGINS-->
		<!--END GLOBAL STYLES -->

		<!-- PAGE LEVEL STYLES -->
		<!-- END PAGE LEVEL  STYLES -->
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
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
				<div class="inner" style="min-height:700px;">
					<div class="row">
						<div class="col-lg-12">
							<h1> Dashboard </h1>
						</div>
					</div>
					<hr />
					
					<div class="row">
					<div class="col-lg-6">
					<div class="panel panel-primary bg-gray-light" >
                            <div class="panel-heading" >
								<div class="panel-title-box">
								<i class="fa fa-bar-chart"></i> Site Statistics
								</div>                                  
							</div>
							<div class="row padding_005">
                            <div class="col-lg-6"><a href="rider.php">
								<div class="info-box bg-aqua">
									<span class="info-box-icon"><i class="fa fa-users"></i></span>

									<div class="info-box-content">
										<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_DASHBOARD_USERS_ADMIN'];?> </span>
										<span class="info-box-number"><?=number_format($rider);?></span>
									</div>
									<!-- /.info-box-content -->
								</div></a>
								<!-- /.info-box -->
							</div>
							<!-- /.col -->
							<div class="col-lg-6"><a href="driver.php?type=approve">
								<div class="info-box bg-yellow">
									<span class="info-box-icon"><i class="fa fa-male"></i></span>

									<div class="info-box-content">
										<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_DASHBOARD_DRIVERS_ADMIN'];?> </span>
										<span class="info-box-number"><?=number_format($driver);?></span>
									</div>
									<!-- /.info-box-content -->
								</div></a>
								<!-- /.info-box -->
							</div>
							<div class="col-lg-6"><a href="company.php">
								<div class="info-box bg-red">
									<span class="info-box-icon"><i class="fa fa-building-o"></i></span>

									<div class="info-box-content">
										<span class="info-box-text">Companies</span>
										<span class="info-box-number"><?=number_format($company);?></span>
									</div>
									<!-- /.info-box-content -->
								</div></a>
								<!-- /.info-box -->
							</div>

							<div class="col-lg-6"><a href="total_trip_detail.php">
								<div class="info-box bg-green">
									<span class="info-box-icon"><i class="fa fa-money"></i></span>

									<div class="info-box-content">
										<span class="info-box-text">Total Earnings</span>
										<!--<span class="info-box-number"><?=number_format($totalEarns,2);?></span>-->
										<span class="info-box-number"><?=$generalobj->trip_currency($totalEarns,'','',2);?></span>
									</div>
									<!-- /.info-box-content -->
								</div></a>
								<!-- /.info-box -->
							</div>
							</div>
                        </div>
					</div>
					
					<div class="col-lg-6">
					<div class="panel panel-primary bg-gray-light" >
							<div class="panel-heading" >
								<div class="panel-title-box">
								   <i class="fa fa-area-chart"></i> <?php  echo $langage_lbl_admin['LBL_RIDE_STATISTICS_ADMIN'];?>
								</div>                                  
							</div>
							<div class="row padding_005">
                            <div class="col-lg-6"><a href="trip.php">
								<div class="info-box bg-aqua">
									<span class="info-box-icon"><i class="fa fa-cubes"></i></span>

									<div class="info-box-content">
										<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_TOTAL_RIDES_ADMIN'];?> </span>
										<span class="info-box-number"><?=number_format($totalRides);?></span>
									</div>
									<!-- /.info-box-content -->
								</div></a>
								<!-- /.info-box -->
							</div>
							<!-- /.col -->
							<div class="col-lg-6"><a href="trip.php?vStatus=onRide">
								<div class="info-box bg-yellow">
									<span class="info-box-icon"><i class="fa fa-clone"></i></span>

									<div class="info-box-content">
										<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_ON_RIDES_ADMIN'];?> </span>
										<span class="info-box-number"><?=number_format($onRides);?></span>
									</div>
									<!-- /.info-box-content -->
								</div></a>
								<!-- /.info-box -->
							</div>
							
							<div class="col-lg-6"><a href="trip.php?vStatus=cancel">
								<div class="info-box bg-red">
									<span class="info-box-icon"><i class="fa fa-times-circle-o"></i></span>

									<div class="info-box-content">
										<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_CANCELLED_RIDES_ADMIN'];?> </span>
										<span class="info-box-number"><?=number_format($cancelRides);?></span>
									</div>
									<!-- /.info-box-content -->
								</div></a>
								<!-- /.info-box -->
							</div>
							<!-- /.col -->


							<div class="col-lg-6"><a href="trip.php?vStatus=complete">
								<div class="info-box bg-green">
									<span class="info-box-icon"><i class="fa fa-check"></i></span>

									<div class="info-box-content">
										<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_COMPLETED_RIDES_ADMIN'];?> </span>
										<span class="info-box-number"><?=number_format($finishRides);?></span>
									</div>
									<!-- /.info-box-content -->
								</div></a>
								<!-- /.info-box -->
							</div>
							</div>
                        </div>
					</div>
					</div>
					
					<hr />
					<div class="row">
					<div class="col-lg-6">
					<div class="panel panel-primary bg-gray-light" >
                            <div class="panel-heading" >
								<div class="panel-title-box">
								   <i class="fa fa-bar-chart"></i> <?php  echo $langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>
								</div>                                  
							</div>
							<div class="panel-body padding-0">
							<div class="col-lg-6">
								<div class="chart-holder" id="dashboard-rides" style="height: 200px;"></div>
							</div>
							<div class="col-lg-6">
								<h3><?php  echo $langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>  Count : <?=number_format($totalRides);?></h3>
								<p>Today : 
									<b><?=number_format($generalobjAdmin->getTripDateStates('today'));?></b>
								</p>
								<p>This Month : 
									<b><?=number_format($generalobjAdmin->getTripDateStates('month'));?></b>
								</p>
								<p>This Year : 
									<b><?=number_format($generalobjAdmin->getTripDateStates('year'));?></b>
								</p>
								<br />
								<p>
									* This is count for all <?=$langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?> (Finished, ongoing, cancelled.)
								</p>
							</div>
							</div>
						</div>
						<!-- END VISITORS BLOCK -->
					</div>
					
					<div class="col-lg-6">
					<div class="panel panel-primary bg-gray-light" >
                            <div class="panel-heading" >
								<div class="panel-title-box">
								   <i class="fa fa-bar-chart"></i> <?php  echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'];?>
								</div>                                  
							</div>
							<div class="panel-body padding-0">
							<div class="col-lg-6">
								<div class="chart-holder" id="dashboard-drivers" style="height: 200px;"></div>
							</div>
							<div class="col-lg-6">
								<h3><?php  echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'];?>  Count : <?=number_format($driver);?></h3>
								<p>Today : <b><?=number_format(count($generalobjAdmin->getDriverDateStatus('today')));?></b></p>
								<p>This Month : <b><?=number_format(count($generalobjAdmin->getDriverDateStatus('month')));?></b></p>
								<p>This Year : <b><?=number_format(count($generalobjAdmin->getDriverDateStatus('year')));?></b></p>
							</div>
							</div>
						</div>
						<!-- END VISITORS BLOCK -->
					</div>
					</div>
					<!-- COMMENT AND NOTIFICATION  SECTION -->
					<div class="row">
						<div class="col-lg-6">
						<div class="chat-panel panel panel-success">
								<div class="panel-heading">
									<div class="panel-title-box">
									   <i class="icon-comments"></i> Latest <?php  echo $langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>
									   <a class="btn btn-info btn-sm ride-view-all001" href="trip.php">View All</a>
									</div>                                  
								</div>
								<?php   for($i=0,$n=$i+2;$i<count($db_finished);$i++,$n++){?>
									<div class="panel-heading" style="background:none;">
										<ul class="chat">
											<?php  if($n%2==0){ ?>
											<a href=<?php  echo "invoice.php?iTripId=".$db_finished[$i]['iTripId'];?> target="_blank">
												<li class="left clearfix">
													<span class="chat-img pull-left">
														<?php  if($db_finished[$i]['vImage']!='' && $db_finished[$i]['vImage']!="NONE" && file_exists( "../webimages/upload/Driver/".$db_finished[$i]['iDriverId']."/".$db_finished[$i]['vImage'])){?>
															<img src="../webimages/upload/Driver/<?php  echo $db_finished[$i]['iDriverId']."/".$db_finished[$i]['vImage'];?>" alt="User Avatar" class="img-circle"  height="50" width="50"/>
														<?php  }else{?>

														<img src="../assets/img/profile-user-img.png" alt="" class="img-circle"  height="50" width="50">
														<?php  }?>
													</span>
													<div class="chat-body clearfix">
														<div class="header">
															<strong class="primary-font "> <?php  echo $generalobjAdmin->clearName($db_finished[$i]['vName']." ".$db_finished[$i]['vLastName']); ?> </strong>
															<small class="pull-right text-muted label label-danger">
																<i class="icon-time"></i>
																<?php 
																	$regDate=$db_finished[$i]['tEndDate'];
																	$dif=strtotime(Date('Y-m-d H:i:s'))-strtotime($regDate);
																	if($dif<60)
																	{
																		$time=floor($dif/(60));
																		echo "Just Now";
																	}
																	else if($dif<3600)
																	{
																		$time=floor($dif/(60));
																		$texts = "Minute";
																		if($time > 1) {
																			$texts = "Minutes";
																		}
																		echo $time." $texts ago";
																	}
																	else if($dif<86400)
																	{
																		$time=floor($dif/(60*60));
																		$texts = "Hour";
																		if($time > 1) {
																			$texts = "Hours";
																		}
																		echo $time." $texts ago";
																	}
																	else
																	{
																		$time=floor($dif/(24*60*60));
																		$texts = "Day";
																		if($time > 1) {
																			$texts = "Days";
																		}
																		echo $time." $texts ago";
																	}
																?>
															</small>
														</div>
														<br />
														<p>
															<?php  echo $db_finished[$i]['tSaddress']." --> ".$db_finished[$i]['tDaddress']."<br/>";
																echo "<b>Status: ".$db_finished[$i]['iActive']."</b>";
															?>
														</p>
													</div>
												</li>
												</a>
												<?php  } else { ?>
												<li class="right clearfix">
													<a href=<?php  echo "invoice.php?iTripId=".$db_finished[$i]['iTripId'];?> target="_blank">
													<span class="chat-img pull-right">
														<?php  if($db_finished[$i]['vImage']!='' && $db_finished[$i]['vImage']!="NONE"){?>
															<?php  if(file_exists( "../webimages/upload/Driver/".$db_finished[$i]['iDriverId']."/".$db_finished[$i]['vImage'])){ ?>
															<img src="../webimages/upload/Driver/<?php  echo $db_finished[$i]['iDriverId']."/".$db_finished[$i]['vImage'];?>" alt="User Avatar" class="img-circle"  height="50" width="50"/>
														<?php   } else {?>
															<img src="../assets/img/profile-user-img.png" alt="" class="img-circle"  height="50" width="50">
														<?php  } ?>
														<?php  }else{?>

														<img src="../assets/img/profile-user-img.png" alt="" class="img-circle"  height="50" width="50">
														<?php  }?>
													</span>
													<div class="chat-body clearfix">
														<div class="header">
															<small class=" text-muted label label-info">
																<i class="icon-time"></i> <?php 
																	$regDate=$db_finished[$i]['tEndDate'];
																	$dif=strtotime(Date('Y-m-d H:i:s'))-strtotime($regDate);
																	if($dif<60)
																	{
																		$time=floor($dif/(60));
																		echo "Just Now";
																	}
																	else if($dif<3600)
																	{
																		$time=floor($dif/(60));
																		$texts = "Minute";
																		if($time > 1) {
																			$texts = "Minutes";
																		}
																		echo $time." $texts ago";
																	}
																	else if($dif<86400)
																	{
																		$time=floor($dif/(60*60));
																		$texts = "Hour";
																		if($time > 1) {
																			$texts = "Hours";
																		}
																		echo $time." $texts ago";
																	}
																	else
																	{
																		$time=floor($dif/(24*60*60));
																		$texts = "Day";
																		if($time > 1) {
																			$texts = "Days";
																		}
																		echo $time." $texts ago";
																	}
																?></small>
																<strong class="pull-right primary-font"> <?php  echo $generalobjAdmin->clearName($db_finished[$i]['vName']." ".$db_finished[$i]['vLastName']); ?></strong>
														</div>
														<br />
														<p>
															<?php  echo $db_finished[$i]['tSaddress']." --> ".$db_finished[$i]['tDaddress']."<br/>";
																echo "<b>Status: ".$db_finished[$i]['iActive']."</b>";
															?>
														</p>
													</div>
												</a>
												</li>
											<?php  }?>
										</ul>
									</div>
								<?php  } ?>
						</div>


					</div>
					<div class="col-lg-6">
						<div class="panel panel-danger">
								<div class="panel-heading">
									<div class="panel-title-box">
									   <i class="icon-bell"></i> Notifications Alerts Panel
									</div>                                  
								</div>

							<div class="panel-body">
								<?php 
								
								if(count($db_notification)>0)
								{
								for($i=0;$i<count($db_notification);$i++) {?>
										<div class="list-group">
											<?php 
												if($db_notification[$i]['doc_usertype']=='driver'){
													$url = "driver_document_action.php";
													$id = $db_notification[$i]['iDriverId'];
													if($db_notification[$i]['doc_name_'.$default_lang] != ''){
														$msg = strtoupper($db_notification[$i]['doc_name_'.$default_lang])." uploaded by ".$langage_lbl['LBL_DRIVER_TXT_ADMIN']." : ".$generalobjAdmin->clearName($db_notification[$i]['Driver']);
													} else {
														$msg = $db_notification[$i]['doc_name_'.$default_lang]." uploaded by ".$langage_lbl['LBL_DRIVER_TXT_ADMIN']." : ".$generalobjAdmin->clearName($db_notification[$i]['Driver']);
													}
												}
												else if($db_notification[$i]['doc_usertype']=='company')
												{
													$url = "company_document_action.php";
													$id = $db_notification[$i]['iCompanyId'];
													if($db_notification[$i]['doc_name_'.$default_lang] != ''){
														$msg = strtoupper( $db_notification[$i]['doc_name_'.$default_lang])." uploaded by ".$db_notification[$i]['doc_usertype']." : ".$generalobjAdmin->clearCmpName($db_notification[$i]['vCompany']);
													} else {
														$msg = $db_notification[$i]['doc_name_'.$default_lang]." uploaded by ".$db_notification[$i]['doc_usertype']." : ".$generalobjAdmin->clearCmpName($db_notification[$i]['vCompany']);
													}
												}
												else if($db_notification[$i]['doc_usertype']=='car')
												{
													$url = "vehicle_document_action.php";
													$id = $db_notification[$i]['iDriverVehicleId'];
													if($db_notification[$i]['doc_name_'.$default_lang] != ''){
														$msg =strtoupper($db_notification[$i]['doc_name_'.$default_lang])." uploaded by ".$langage_lbl['LBL_DRIVER_TXT_ADMIN'] ." : ".$generalobjAdmin->clearName($db_notification[$i]['DriverName']);
													} else {
														$msg =$db_notification[$i]['doc_name_'.$default_lang]." uploaded by ".$langage_lbl['LBL_DRIVER_TXT_ADMIN'] ." : ".$generalobjAdmin->clearName($db_notification[$i]['DriverName']);
													}
												}
												?>
												<a href="<?=$url;?>?id=<?php  echo $id;?>&action=edit" class="list-group-item" target="_blank">
													<i class=" icon-comment"></i>
													<?=$msg ;?>
													<span class="pull-right text-muted small">
													<em>
														<?php  
														
														/*$reDate=$db_notification[$i]['edate'];

															$dif=strtotime(Date('Y-m-d H:i:s'))-strtotime($reDate);
															if($dif<3600)
															{
																$time=floor($dif/(60));
																echo $time." minites ago";
															}
															else if($dif<86400)
															{
																$time=floor($dif/(60*60));
																echo $time." hour ago";
															}
															else
															{
																$time=floor($dif/(24*60*60));
																echo $time." Days ago";
															}	*/											
															
															$reDate=$db_notification[$i]['edate']; 
															$dif=strtotime(Date('Y-m-d H:i:s'))-strtotime($reDate);
															if($dif<60)
															{
																$time=floor($dif/(60));
																echo "Just Now";
															}
															else if($dif<3600)
															{
																$time=floor($dif/(60));
																$texts = "Minute";
																if($time > 1) {
																	$texts = "Minutes";
																}
																echo $time." $texts ago";
															}
															else if($dif<86400)
															{
																$time=floor($dif/(60*60));
																$texts = "Hour";
																if($time > 1) {
																	$texts = "Hours";
																}
																echo $time." $texts ago";
															}
															else
															{
																$time=floor($dif/(24*60*60));
																$texts = "Day";
																if($time > 1) {
																	$texts = "Days";
																}
																echo $time." $texts ago";
															}


														?>
													</em>
													</span>
												</a>

												</div>

								<?php  } }
											else
											{
												echo "No Notification";
											}

											?>
								</div>

							</div>



						</div>
					</div>
					<!-- END COMMENT AND NOTIFICATION  SECTION -->
				</div>
			</div>

			<!--END PAGE CONTENT -->
		</div>

		<?php  include_once('footer.php'); ?>

	</body>
	<!-- END BODY-->
	<?php 
		// if(SITE_TYPE=='Demo'){
			// $generalobjAdmin->remove_unwanted();
		  // }
	?>
</html>
<script>
	$(document).ready(function(){
			/* Donut dashboard chart */
			 var total_ride = '<?=$totalRides;?>';
			 var complete_ride = '<?=$finishRides;?>';
			 var cancel_ride = '<?=$cancelRides;?>';
			 var on_ride = '<?=$onRides;?>';			
			
			 //var total_ride = 0;	
			 //var complete_ride = 0;
			 //var cancel_ride = 0;
			 //var on_ride = 0;
			
	        if(complete_ride > 0 || cancel_ride > 0 || total_ride > 0 ) 
			{
			    Morris.Donut({
				element: 'dashboard-rides',
				data: [
					{label: "On Going", value: on_ride},
					{label: "Completed", value: complete_ride},
					{label: "Cancelled", value: cancel_ride}
				],
				
				formatter: function (x) { return (x/total_ride *100).toFixed(2)+'%'+ ' ('+x+')'; },
				colors: ['#33414E', '#1caf9a', '#FEA223'],
				resize: true
				});
			} 
			else
			{					
				Morris.Donut({
				element: 'dashboard-rides',
				data: [
					{label: "On Going", value: on_ride},
					{label: "Completed", value: complete_ride},
					{label: "Cancelled", value: cancel_ride}
				],
				backgroundColor: '#f7f7f7',				
				formatter: function (x) { return (0)+' %'+ ' ('+x+')'; },
				colors: ['#33414E', '#1caf9a', '#FEA223'],
				resize: true
				});					
			}
				
			
			
			var total_drive = '<?=$driver;?>';
			var active_drive = '<?=$actDrive;?>';
			var inactive_drive = '<?=$inaDrive;?>';
			Morris.Donut({
				element: 'dashboard-drivers',
				data: [
					{label: "Active", value: active_drive},
					{label: "Pending", value: inactive_drive},
				],
				formatter: function (x) { return (x/total_drive *100).toFixed(2)+'%'+ '('+x+')'; },
				colors: ['#33414E', '#1caf9a', '#FEA223'],
				resize: true
			});
			/* END Donut dashboard chart */
	});
</script>
