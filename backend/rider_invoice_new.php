<?php 
	include_once('common.php');
	$tbl_name 	= 'trips';

	$generalobj->check_member_login();
	$iTripId = isset($_REQUEST['iTripId'])?$_REQUEST['iTripId']:'';

	$sql = "select * from trips t where iTripId = '".$iTripId."'";
	$db_trip = $obj->MySQLSelect($sql);


	/* #echo '<pre>'; print_R($db_trip); echo '</pre>';
		$to_time = @strtotime($db_trip[0]['tStartDate']);
		$from_time = @strtotime($db_trip[0]['tEndDate']);
		$diff=round(abs($to_time - $from_time) / 60,2);
		$db_trip[0]['starttime'] = $generalobj->DateTime($db_trip[0]['tStartDate'],18);
		$db_trip[0]['endtime'] = $generalobj->DateTime($db_trip[0]['tEndDate'],18);
		$db_trip[0]['triptime'] = $diff;
	*/
	$sql = "select * from ratings_user_driver where iTripId = '".$iTripId."'";
	$db_ratings = $obj->MySQLSelect($sql);

	$rating_width = ($db_ratings[0]['vRating1'] * 100) / 5;
	$db_ratings[0]['vRating1'] = '<span style="display: block; width: 65px; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 0;">
		<span style="margin: 0;display: block; width: '.$rating_width.'%; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 -13px;"></span>
		</span>';

	$sql = "select * from register_driver where iDriverId = '".$db_trip[0]['iDriverId']."' LIMIT 0,1";
	$db_driver = $obj->MySQLSelect($sql);

	$sql = "select * from register_user where iUserId = '".$db_trip[0]['iUserId']."' LIMIT 0,1";
	$db_user = $obj->MySQLSelect($sql);

	$ts1 = strtotime($db_trip[0]['tStartDate']);
	$ts2 = strtotime($db_trip[0]['tEndDate']);
	$diff = abs($ts1 - $ts2)/60;
	$diff = gmdate("H:i:s", $diff);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>"> <!--<![endif]-->

	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title><?=$SITE_NAME?> | Invoice</title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<meta content="" name="keywords" />
		<meta content="" name="description" />
		<meta content="" name="author" />
		<?php  include_once('global_files.php');?>
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
		<link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
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
						<div class="col-lg-12">
							<h2><p>Invoice</p></h2>
							<!-- <a href="mytrip.php">
							--><input type="button" class="add-btn" value="Back to listing" onClick="history.go(-1)">
							<!-- </a> -->
						</div>
					</div>
					<hr />
					<div class="body-div">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										<b>Your trip</b> <?=@date('h:i A',@strtotime($db_trip[0]['tStartDate']));?> on <?=@date('d M Y',@strtotime($db_trip[0]['tStartDate']));?>
										<!-- <div class="pull-right">
											<a href="pdf.php"><i class="icon-download"> PDF </i></a>
										</div> -->
									</div>
									<div class="panel-body rider-invoice-new">
										<div class="row">
											<!-- <div class="col-sm-12">
												<?php  if($db_user[0]['vImgName'] != '' && file_exists($tconfig["tsite_upload_images_panel"].'/'.$db_user[0]['vImgName'])){?>
													<img src = "<?=$tconfig["tsite_upload_images"].$db_user[0]['vImgName']?>" style="width:200px;height:150px;"/>
													<?php  }else{ ?>
													<img src="assets/img/profile-user-img.png" alt="" style="width:200px;height:150px;">
												<?php  } ?>


												<?php  if($db_driver[0]['vImage'] != '' && file_exists($tconfig["tsite_upload_images_panel"].'/'.$db_driver[0]['vImage'])){?>
													<img src = "<?=$tconfig["tsite_upload_images"].$db_driver[0]['vImage']?>" style="width:200px;height:150px;"/>
													<?php  }else{ ?>
													<img src="assets/img/profile-user-img.png" alt="" style="width:200px;height:150px;">
												<?php  } ?>


											</div> -->
											<div class="col-sm-6 rider-invoice-new-left">
												<div id="map-canvas" class="gmap3" style="width:100%;height:200px;margin-bottom:10px;"></div>
												<span class="location-from"><i class="icon-map-marker"></i> 
                                                <b><?=@date('h:i A',@strtotime($db_trip[0]['tStartDate']));?><p><?=$db_trip[0]['tSaddress'];?></p></b></span>
                                                
												<span class="location-to"><i class="icon-map-marker"></i> <b><?=@date('h:i A',@strtotime($db_trip[0]['tEndDate']));?><p><?=$db_trip[0]['tDaddress'];?></p></b></span>
												
                                                <div class="rider-invoice-bottom">
												<div class="col-sm-4">
														Car<br /> <b><?=$db_trip[0]['eCarType'];?></b><br/>
												</div>
												<div class="col-sm-4">
														Distance<br /> <b><?=$db_trip[0]['fDistance'];?></b> <br/>
												</div>
												<div class="col-sm-4">
														Trip time<br /><b><?php echo $diff;?></b>
												</div>
                                                </div>
											</div>
											<div class="col-sm-6 rider-invoice-new-right">
												<h4 style="text-align:center;">Fare Breakdown</h4><hr/>
												<?php $tot=$db_trip[0]['fPricePerKM']+$db_trip[0]['fPricePerMin']+$db_trip[0]['iBaseFare']+$db_trip[0]['fCommision'];?>
												<table style="width:100%" cellpadding="5" cellspacing="0" border="0">
													<tr>
															<td>Basic Fare  </td>
															<td><?=$generalobj->trip_currency($db_trip[0]['iBaseFare'],$db_trip[0]['fRatioPassenger'],$db_trip[0]['vCurrencyPassenger']);?></td>
													</tr>
													<tr>
															<td>Distance</td>
															<td><?=$generalobj->trip_currency($db_trip[0]['fPricePerKM'],$db_trip[0]['fRatioPassenger'],$db_trip[0]['vCurrencyPassenger']);?></td>
													</tr>
													<tr>
															<td>Time</td>
															<td><?=$generalobj->trip_currency($db_trip[0]['fPricePerMin'],$db_trip[0]['fRatioPassenger'],$db_trip[0]['vCurrencyPassenger']);?></td>
													</tr>
													<tr>
															<td>Commision </td>
															<td><?=$generalobj->trip_currency($db_trip[0]['fCommision'],$db_trip[0]['fRatioPassenger'],$db_trip[0]['vCurrencyPassenger']);?></td>
													</tr>
													<tr>
															<td colspan="2"><hr style="margin-bottom:0px"/></td>
													</tr>
													<tr>
															<td><b>Total Fare (Via <?=$db_trip[0]['vTripPaymentMode']?>)</b></td>
															<td><b><?=$generalobj->trip_currency($tot,$db_trip[0]['fRatioPassenger'],$db_trip[0]['vCurrencyPassenger']);?></b></td>
													</tr>
													
													<!--<tr>
														<td>Charged<br /><?=$db_trip[0]['vTripPaymentMode']?> </td>
														<td><b><?=$generalobj->trip_currency($tot,$db_trip[0]['fRatioPassenger'],$db_trip[0]['vCurrencyPassenger']);?></b></td>
													</tr>-->
												</table>

											</div>
										</div>
										<br /><br />
										<div class="row">
											<div class="col-sm-12">
												<!--<table style="width:100%; background-color:#f1f1f1;">
													<tr>
															<td width="25%">
																<span class="invoice-img">
																<?php  if($db_driver[0]['vImage'] != '' && file_exists($tconfig["tsite_upload_images_driver_path"]. '/' . $db_driver[0]['iDriverId'] . '/2_' . $db_driver[0]['vImage'])){?>
																<img src = "<?= $tconfig["tsite_upload_images_driver"]. '/' . $db_driver[0]['iDriverId'] . '/2_' .$db_driver[0]['vImage'] ?>" style="height:150px;"/>
																<?php  }else{ ?>
																<img src="assets/img/profile-user-img.png" alt="">
																<?php  } ?></span>
															</td>
															<td width="25%">
																	<span>You ride with <?= $db_driver[0]['vName']?></span>
															</td>
															<td width="25%">
																	RATE YOUR RIDE
															</td>
															<td width="25%">
																<?=$db_ratings[0]['vRating1'];?>
															</td>
													</tr>
												</table>-->
											
                                            
													<span class="invoice-img">
													<?php  if($db_driver[0]['vImage'] != '' && file_exists($tconfig["tsite_upload_images_driver_path"]. '/' . $db_driver[0]['iDriverId'] . '/2_' . $db_driver[0]['vImage'])){?>
													<img src = "<?= $tconfig["tsite_upload_images_driver"]. '/' . $db_driver[0]['iDriverId'] . '/2_' .$db_driver[0]['vImage'] ?>" style="height:150px;"/>
													<?php  }else{ ?>
													<img src="assets/img/profile-user-img.png" alt="">
													<?php  } ?></span>
													<span class="invoice-name">You ride with <?= $db_driver[0]['vName']?></span>
													<span class="invoice-ride">RATE YOUR RIDE</span>
												    <span class="invoice-rating"><?=$db_ratings[0]['vRating1'];?></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>

		<!--END MAIN WRAPPER -->

		<?php  include_once('footer.php');?>
		<script src="assets/plugins/jasny/js/bootstrap-fileupload.js"></script>
		<script src="assets/js/gmap3.js"></script>
		<script>
			h = window.innerHeight;
			$("#page_height").css('min-height', Math.round( h - 99)+'px');

			function from_to(){

				$("#map-canvas").gmap3({
					getroute:{
						options:{
							origin:'<?= $db_trip[0]['tSaddress']?>',
							destination:'<?= $db_trip[0]['tDaddress']?>',
							travelMode: google.maps.DirectionsTravelMode.DRIVING
						},
						callback: function(results){
							if (!results) return;
							$(this).gmap3({
								map:{
									options:{
										zoom: 13,
										center: [-33.879, 151.235]
									}
								},
								directionsrenderer:{
									options:{
										directions:results
									}
								}
							});
						}
					}
				});
			}
			from_to();
		</script>
	</body>
	<!-- END BODY-->
</html>
