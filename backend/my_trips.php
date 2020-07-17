<?php 
	include_once('common.php');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>"> <!--<![endif]-->
	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title><?=$SITE_NAME?> | MY TRIPS</title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<meta content="" name="keywords" />
		<meta content="" name="description" />
		<meta content="" name="author" />
		<link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
		<link href="assets/css/jquery-ui.css" rel="stylesheet" />
		<link rel="stylesheet" href="assets/plugins/uniform/themes/default/css/uniform.default.css" />
		<link rel="stylesheet" href="assets/plugins/inputlimiter/jquery.inputlimiter.1.0.css" />
		<link rel="stylesheet" href="assets/plugins/chosen/chosen.min.css" />
		<link rel="stylesheet" href="assets/plugins/colorpicker/css/colorpicker.css" />
		<link rel="stylesheet" href="assets/plugins/tagsinput/jquery.tagsinput.css" />
		<link rel="stylesheet" href="assets/plugins/daterangepicker/daterangepicker-bs3.css" />
		<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css" />
		<link rel="stylesheet" href="assets/plugins/timepicker/css/bootstrap-timepicker.min.css" />
		<link rel="stylesheet" href="assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
		<?php  include_once('global_files.php');?>
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
				<div class="inner" id="page_height" style="">
					<div class="row">
						<div class="col-lg-5">
							<div class="header-left-part">
								<a href="#"><span><i class="icon-list"></i> </span><?=$langage_lbl['LBL_FILTER_TRIPS']; ?></a>
							</div>
						</div>   
						<div class="col-lg-7">
							<h2>MY TRIPS</h2>
						</div>
					</div>
					<hr />
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-default">
								<div class="panel-body">
									<div class="table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th></th>
													<th>Pickup</th>
													<th>Driver</th>
													<th>Fare</th>
													<th>Car</th>
													<th>City</th>
													<th>Payment Method</th>																						
												</tr>
											</thead>
											<tbody>
												<tr>
													<td><a class="accordion-toggle minimize-box" data-toggle="collapse" href="#div-1">
													<i class="icon-chevron-up"></i></a>
													</td>
													<td>Mark</td>
													<td>Otto</td>
													<td>@mdo</td>
													<td>Otto</td>
													<td>@mdo</td>
													<td>cash</td>
												</tr>
												<tr id="div-1" class="accordion-body collapse">
													<td style="border-top:none;" colspan="7">
																<div class="row">
																	<div class="col-md-4 trip-detail-inner-left">
																		<div class="row">
																		<span><img src="assets/img/map-simple.png" alt=""></span>
																		</div>
																	</div>
																	<div class="col-md-4 trip-detail-inner-center">
																		<div class="row">
																		<h3>108.12</h3>
																		</div>
																		<div class="row">
																		<p>
																			<span class="cash-icon">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
																			<span>•••• Cash</span>
																		</p>
																		</div>
																		<div class="row">
																		<p><em>Friday, September 4 2015 3:45 PM</em></p>
																		</div>
																		<div class="row">
																		<div class="trip-address">
																			<div class="trip-address-start">
																				<p>3:45 PM</p>
																				<p><b>Prahlad Nagar S.T Pickup Stand, Prahlad Nagar, Ahmedabad, Gujarat 380015, India</b></p>
																			</div>
																			<div class="trip-address-end">
																				<p>4:10 PM</p>
																				<p><b>9, National Highway 8C, Vasant Nagar, Ognaj, Ahmedabad, Gujarat 382481, India</b></p>
																			</div>
																		</div>
																		</div>
																	</div>
																	<div class="col-md-4 trip-detail-inner-right">
																	<div class="row">
																		<span><img src="assets/img/star-blue.png" alt=""></span><br>
																	</div>	
																	<div class="row">
																		<input type="button" class="trip-detail-inner-right-btn-1" value="Resend" /><br>
																	</div>	
																	<div class="row">
																	<a href="my_trips_detail.php">
																		<input type="button" class="trip-detail-inner-right-btn-2" value="View Detail"/>																	</a>
																	</div>		
																	</div>
															</div>
													</td>
												</tr>
												<tr>
													<td><a class="accordion-toggle minimize-box" data-toggle="collapse" href="#div-2">
													<i class="icon-chevron-up"></i></a>
													</td>
													<td>Jacob</td>
													<td>Thornton</td>
													<td>@fat</td>
													<td>Otto</td>
													<td>@mdo</td>
													<td>cash</td>
												</tr>
												<tr id="div-2"  class="accordion-body collapse">
													<td style="border-top:none;" colspan="7">
														<div class="trip-detail">
															<div class="trip-detail-inner">
																<div class="row">
																	<div class="col-md-4 trip-detail-inner-left">
																		<span><img src="assets/img/map-simple.png" alt=""></span>
																	</div>
																	<div class="col-md-4 trip-detail-inner-center">
																		<h3>108.12</h3>
																		<p>
																			<span class="cash-icon">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
																			<span>•••• Cash</span>
																		</p>
																		<p><em>Friday, September 4 2015 3:45 PM</em></p>
																		<div class="trip-address">
																			<div class="trip-address-start">
																				<p>3:45 PM</p>
																				<p><b>Prahlad Nagar S.T Pickup Stand, Prahlad Nagar, Ahmedabad, Gujarat 380015, India</b></p>
																			</div>
																			<div class="trip-address-end">
																				<p>4:10 PM</p>
																				<p><b>9, National Highway 8C, Vasant Nagar, Ognaj, Ahmedabad, Gujarat 382481, India</b></p>
																			</div>
																		</div>
																	</div>
																	<div class="col-md-4 trip-detail-inner-right">
																		<span><img src="assets/img/star-blue.png" alt=""></span><br>
																		<input type="button" class="trip-detail-inner-right-btn-1" value="Resend" /><br>
																		<input type="button" class="trip-detail-inner-right-btn-2" value="View Detail"/>
																	</div>
																</div>
															</div>
														</div>
													</td>
												</tr>	
											</tbody>
										</table>
									</div>
								</div>
								<ul class="pager">
									<li><a href="#">Previous</a></li>
									<li><a href="#">Next</a></li>
								</ul>
							</div>
						</div>
					</div>				
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>
		<!--END MAIN WRAPPER -->
		<?php  include_once('footer.php');?>
		<script src="assets/plugins/dataTables/jquery.dataTables.js"></script>
		<script src="assets/plugins/dataTables/dataTables.bootstrap.js"></script>
		<script>
			$(document).ready(function () {
				$('#dataTables-example').dataTable();
			});
			function changeCode(id)
			{
				var request = $.ajax({  
					type: "POST",
					url: 'change_code.php',  
					data: 'id='+id, 	   	 	  
					success: function(data)
					{  
						document.getElementById("code").value = data ;
						//window.location = 'profile.php';	  
					}
				});
			}
		</script>
		<script src="assets/js/jquery-ui.min.js"></script>
		<script src="assets/plugins/uniform/jquery.uniform.min.js"></script>
		<script src="assets/plugins/inputlimiter/jquery.inputlimiter.1.3.1.min.js"></script>
		<script src="assets/plugins/chosen/chosen.jquery.min.js"></script>
		<script src="assets/plugins/autosize/jquery.autosize.min.js"></script>
		<script src="assets/js/formsInit.js"></script>
        <script>
            $(function () { formInit(); });
			
			h = window.innerHeight;
			$("#page_height").css('min-height', Math.round( h - 100)+'px');
		</script>
	</body>
	<!-- END BODY-->    
</html>