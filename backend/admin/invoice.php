<?php 
	include_once('../common.php');
	include_once("../generalFunctions.php");

	$tbl_name 	= 'trips';
	if (!isset($generalobjAdmin)) {
		require_once(TPATH_CLASS . "class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	
	$ENABLE_TIP_MODULE = $generalobj->getConfigurations("configurations","ENABLE_TIP_MODULE");
	$APP_DELIVERY_MODE = $generalobj->getConfigurations("configurations","APP_DELIVERY_MODE");
	
	include_once('../send_invoice_receipt.php');
	$generalobjAdmin->check_member_login();
	$iTripId = isset($_REQUEST['iTripId'])?$_REQUEST['iTripId']:'';
	$script="Trips";

	$db_trip_data = $generalobj->getTripPriceDetailsForWeb($iTripId,'','');

	if(file_exists($tconfig["tsite_upload_images_driver_path"]. '/' .$db_trip_data['DriverDetails']['iDriverId'] . '/2_' . $db_trip_data['DriverDetails']['vImage'])){
		$img=$tconfig["tsite_upload_images_driver"]. '/' . $db_trip_data['DriverDetails']['iDriverId'] . '/2_' .$db_trip_data['DriverDetails']['vImage'];
		} else {
		$img=$tconfig["tsite_url"]."webimages/icons/help/driver.png";
	}
	if(file_exists($tconfig["tsite_upload_images_passenger_path"]. '/' . $db_trip_data['PassengerDetails']['iUserId'] . '/2_' . $db_trip_data['PassengerDetails']['vImgName'])){
		$img1=$tconfig["tsite_upload_images_passenger"]. '/' . $db_trip_data['PassengerDetails']['iUserId'] . '/2_' .$db_trip_data['PassengerDetails']['vImgName'];
		}else{
		$img1=$tconfig["tsite_url"]."webimages/icons/help/taxi_passanger.png";
	}
?>

<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	
	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin | Invoice</title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<meta content="" name="keywords" />
		<meta content="" name="description" />
		<meta content="" name="author" />
		<?php  include_once('global_files.php');?>		
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=<?=$GOOGLE_SEVER_API_KEY_WEB?>"></script>
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
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
							<h2>Invoice</h2>
							<!-- <a href="mytrip.php">-->
                            <input type="button" class="add-btn" value="Close" onClick="javascript:window.top.close();">
							<!-- </a> -->
                            <div style="clear:both;"></div>
						</div>
					</div>
					<hr />
					<?php  if (isset($_REQUEST['success']) && $_REQUEST['success'] ==1) { ?>
						<div class="alert alert-success paddiing-10">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							Email send successfully.
						</div>
					<?php  }?>
					<?php  
						$systemTimeZone = date_default_timezone_get();
						if($db_trip_data['fCancellationFare'] > 0 && $db_trip_data['vTimeZone'] != "") {
							$dBookingDate = $endDate = converToTz($db_trip_data['tEndDate'],$db_trip_data['vTimeZone'],$systemTimeZone);
						} else if($db_trip_data['tTripRequestDateOrig']!= "" && $db_trip_data['vTimeZone'] != "")  {
							$dBookingDate = converToTz($db_trip_data['tTripRequestDateOrig'],$db_trip_data['vTimeZone'],$systemTimeZone);
							$endDate = converToTz($db_trip_data['tEndDate'],$db_trip_data['vTimeZone'],$systemTimeZone);
						} else {
							$dBookingDate = $db_trip_data['tTripRequestDateOrig'];
							$endDate = $db_trip_data['tEndDate'];
						}
					?>
					<div class="table-list">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										<b>Your <?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'];?> </b> <?php  if($db_trip_data['tTripRequestDateOrig']== "0000-00-00 00:00:00"){ echo "Was Cancelled.";}else{echo @date('h:i A',@strtotime($dBookingDate));?> on <?=@date('d M Y',@strtotime($dBookingDate));}?>
									</div>
									<div class="panel-body rider-invoice-new">
										<div class="row">
											
											<div class="col-sm-6 rider-invoice-new-left">
												<?php  if($APP_TYPE != 'UberX' && ($APP_DELIVERY_MODE != "Multi" || $db_trip_data['eType'] == "Ride")) { ?>
													<div id="map-canvas" class="gmap3" style="width:100%;height:200px;margin-bottom:10px;"></div>
												<?php  } ?>
												<span class="location-from"><i class="icon-map-marker"></i>
												<b><?=@date('h:i A',@strtotime($dBookingDate));?><p><?=$db_trip_data['tSaddress'];?></p></b></span>
												<?php  if($APP_TYPE != 'UberX' && ($APP_DELIVERY_MODE != "Multi" || $db_trip_data['eType'] == "Ride")){ ?> 
													
													<span class="location-to"><i class="icon-map-marker"></i> <b><?=@date('h:i A',@strtotime($endDate));?><p><?=$db_trip_data['tDaddress'];?></p></b></span>
												<?php  } ?> 
												
												<?php  
							              			if($APP_TYPE == 'UberX') {
														
							              				$class_name = 'col-sm-6';
														
													} else {
														
							              				$class_name = 'col-sm-4';
													}
												?>
                                                <div class="rider-invoice-bottom">
													<div class="<?php  echo $class_name; ?>">
														<?php  echo $langage_lbl_admin['LBL_CAR_TXT_ADMIN'];?> <br /> 
														<b>	
														<?php  if(!empty($db_trip_data['vVehicleCategory'])){
						                    			  echo $db_trip_data['vVehicleCategory'] . "-" . $db_trip_data['vVehicleType'];
						                    			} else {
						                    			  echo $db_trip_data['vVehicleType'];
						                    			} ?>
						                    			</b><br/>
													</div>
													
													<?php  if($APP_TYPE != 'UberX'){ ?> 
														
														<div class="<?php  echo $class_name; ?>">
															Distance<br /> 
															<b><?=$db_trip_data['fDistance'].$db_trip_data['DisplayDistanceTxt'];?></b> <br/>
														</div>														
														<div class="<?php  echo $class_name; ?>">
															<?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'];?>  time<br />
															<b><?php  echo $db_trip_data['TripTimeInMinutes'];?></b>
														</div>
													<?php  } ?> 
													
													<?php  if((!empty($db_trip_data['vSignImage'])) && $APP_DELIVERY_MODE == 'Multi' && $db_trip_data['eType'] == "Deliver"){ ?>
														<div class="rider-invoice-bottom">
															<div class="col-sm-6">
																<b><?php  echo $langage_lbl_admin['LBL_SENDER_SIGN'];?></b>
															</div>
															<?php  if(file_exists($tconfig["tsite_upload_trip_signature_images_path"]. '/'. $db_trip_data['vSignImage'])){
																$img123=$tconfig["tsite_upload_trip_signature_images"]. '/' .$db_trip_data['vSignImage'];
															} ?>
															<div class="col-sm-6">
																<img src="<?php  echo $img123;?>" align="left" style="width: 100px;">
															</div>
														</div>
													<?php  } ?>
													
												</div>
												
												<?php  if($APP_DELIVERY_MODE != 'Multi'){ ?>
													<div class="rider-invoice-bottom">
														<div class="col-sm-6">
															<div class="left col-sm-3"> 
																<img src="<?php  echo $img;?>" style="outline:none;text-decoration:none;display:inline-block;width:45px!important;min-height:45px!important;border-radius:50em;max-width:45px!important;min-width:45px!important;border:1px solid #d7d7d7" align="left" height="45" width="45" class="CToWUd">
															</div>
															<div class="right col-sm-9">
																<div><b><?php  echo $langage_lbl_admin['LBL_DIVER'];?></b></div>
																<div><?php  echo $generalobjAdmin->clearName($db_trip_data['DriverDetails']['vName'])."&nbsp;".$generalobjAdmin->clearName($db_trip_data['DriverDetails']['vLastName']); ?></div>
																<div><?php  echo $generalobjAdmin->clearEmail($db_trip_data['DriverDetails']['vEmail']); ?></div>
															</div>
														</div>
														<div class="col-sm-6">
															<div class="left col-sm-3"> 
																<img src="<?php  echo  $img1; ?>" style="outline:none;text-decoration:none;display:inline-block;width:45px!important;min-height:45px!important;border-radius:50em;max-width:45px!important;min-width:45px!important;border:1px solid #d7d7d7" align="left" height="45" width="45" class="CToWUd">
															</div>
															<div class="right col-sm-9">
																<div><b><?php  echo $langage_lbl_admin['LBL_RIDER'];?></b></div>
																<div><?php  echo $generalobjAdmin->clearName( $db_trip_data['PassengerDetails']['vName'])."&nbsp;".$generalobjAdmin->clearName( $db_trip_data['PassengerDetails']['vLastName']); ?></div>
																<div><?php  echo $generalobjAdmin->clearEmail( $db_trip_data['PassengerDetails']['vEmail']); ?></div>
															</div>
														</div>
													</div>
												<?php  } ?>
											</div>
											
											<div class="col-sm-6 rider-invoice-new-right">
												<h4 style="text-align:center;">	<?php  echo $langage_lbl_admin['LBL_FARE_BREAKDOWN_RIDE_NO_TXT'];?> :<?= $db_trip_data['vRideNo'];?></h4><hr/>
												<table style="width:100%" cellpadding="5" cellspacing="0" border="0">
													<tbody>
													<?php 
													foreach ($db_trip_data['HistoryFareDetailsNewArr'] as $key => $value) {					foreach ($value as $k => $val) {
															if($k == $langage_lbl['LBL_EARNED_AMOUNT']) {
																continue;
															} else if($k == $langage_lbl['LBL_SUBTOTAL_TXT']){
																continue;
															} else { ?>
																<tr>
																	<td><?=$k; ?></td>
																	<td align="right"><?php  echo $val;?></td>
																</tr>
													<?php 		}
														}
													}

													 ?>
													 <tr>
														<td colspan="2"><hr style="margin-bottom:0px"/></td>
													</tr>
													<tr>
														<td><b><?php  echo $langage_lbl_admin['LBL_Total_Fare_TXT'];?> (Via <?=$db_trip_data['vTripPaymentMode']?>)</b></td>
														<td align="right">
															<b>
							              					<?=$db_trip_data['FareSubTotal'];?>
															</b>
														</td>
													</tr>
													</tbody>
												</table>
												<br><br><br>
												
												<?php  
													if($db_trip_data['fTipPrice'] !="" && $db_trip_data['fTipPrice'] !="0" && $db_trip_data['fTipPrice'] !="0.00")
													{
														if($ENABLE_TIP_MODULE == "Yes"){ 
														?>
														<table style="border:dotted 2px #000000;" cellpadding="5px" cellspacing="2px" width="100%">
															<tr>
																<td><b>Tip given to Driver</b></td>
																<td align="right"><b><?=$db_trip_data['fTipPrice'];?></b></td>
															</tr>
														</table><br>
													<?php  }
												}?>
													
													
													
													<?php  if($db_trip_data['eType'] == 'Deliver' && $APP_DELIVERY_MODE != 'Multi'){ ?>
														
														<h4 style="text-align:center;"><?php  echo $langage_lbl_admin['LBL_DELIVERY_DETAILS_TXT_ADMIN'];?></h4><hr/>
														
														<table style="width:100%" cellpadding="5" cellspacing="0" border="0">
															<tr>
																<td><?php  echo $langage_lbl['LBL_RECEIVER_NAME'];?></td>
																<td><?=$db_trip_data['vReceiverName'];?></td>
															</tr>
															<tr>
																<td><?php  echo $langage_lbl['LBL_RECEIVER_MOBILE'];?></td>
																<td><?=$db_trip_data['vReceiverMobile'];?></td>
															</tr>
															<tr>
																<td><?php  echo $langage_lbl['LBL_PICK_UP_INS'];?></td>
																<td><?=$db_trip_data['tPickUpIns'];?></td>
															</tr>
															<tr>
																<td><?php  echo $langage_lbl['LBL_DELIVERY_INS'];?></td>
																<td><?=$db_trip_data['tDeliveryIns'];?></td>
															</tr>
															<tr>
																<td><?php  echo $langage_lbl['LBL_PACKAGE_DETAILS'];?></td>
																<td><?=$db_trip_data['tPackageDetails'];?></td>
															</tr>
															<tr>
																<td><?php  echo $langage_lbl['LBL_DELIVERY_CONFIRMATION_CODE_TXT'];?></td>
																<td><?=$db_trip_data['vDeliveryConfirmCode'];?></td>
															</tr>
														</table>
														
													<?php  } ?>
													
													
													<?php  if($APP_TYPE == 'UberX' && ($db_trip_data['vBeforeImage'] != '' || $db_trip_data['vAfterImage'] != '') ){	
														$img_path = $tconfig["tsite_upload_trip_images"];
													?> 
													<h4 style="text-align:center;"><?php  echo $langage_lbl_admin['LBL_TRIP_DETAIL_HEADER_TXT'];?></h4><hr/>
													<div class="invoice-right-bottom-img">
														<?php  if($db_trip_data['vBeforeImage'] != '') {?>
														<div class="col-sm-6">											
															<h3><?php  echo $langage_lbl_admin['LBL_SERVICE_BEFORE_TXT_ADMIN'];?></h3>
															<b><a href="<?= $db_trip_data['vBeforeImage']; ?>" target="_blank" ><img src = "<?= $db_trip_data['vBeforeImage'] ?>" style="width:200px;" alt ="Before Images"/></a></b>
														</div>
														<?php  } ?>
														<?php  if($db_trip_data['vAfterImage'] != '') {?>
														<div class="col-sm-6">
															<h3><?php  echo $langage_lbl_admin['LBL_SERVICE_AFTER_TXT_ADMIN'];?></h3>
															<b><a href="<?= $db_trip_data['vAfterImage']; ?>" target="_blank" ><img src = "<?= $db_trip_data['vAfterImage']; ?>" style="width:200px;" alt ="After Images"/></a></b>
														</div>
														<?php  } ?>
													</div>
													<?php  } ?>
											</div>
											
											<div class="clear"></div>
											
											<?php  if($APP_DELIVERY_MODE == "Multi" && $db_trip[0]['eType'] == 'Deliver'){?>
												<div class="invoice-table">
													<?php  
														
														$sql1 = "SELECT * FROM trips_delivery_locations AS tdl WHERE iTripId = '".$iTripId."'";
														$db_trips_locations = $obj->MySQLSelect($sql1);
													?>
													<?php  $i= 1; 
														if(!empty($db_trips_locations)){
															foreach($db_trips_locations as $dtls) { 
															$class = (!empty($dtls['vSignImage'])) ? 'sign-img' : '';?>
															<div class="col-sm-6 <?php  echo $class;?>">
																<h4><?php  echo $langage_lbl_admin['LBL_RECIPIENT_LIST_TXT'].'&nbsp;'. $i;?></h4><hr/>
																<table style="width:100%" cellpadding="5" cellspacing="0" border="0">
																	<tr>
																		<td class="label_left"><?php  echo $langage_lbl_admin['LBL_RECIPIENT_NAME_HEADER_TXT'];?></td>
																		<td class="detail_right"><?=$dtls['vReceiverName'];?></td>
																	</tr>
																	<tr>
																		<td class="label_left"><?php  echo $langage_lbl_admin['LBL_DROP_OFF_LOCATION_RIDE_DETAIL'];?></td>
																		<td class="detail_right"><?= $dtls['tPickUpIns'] .",".$dtls['tDaddress'];?></td>
																	</tr>
																	<tr>
																		<td class="label_left"><?php  echo $langage_lbl_admin['LBL_DELIVERY_INS'];?></td>
																		<td class="detail_right"><?=$dtls['tDeliveryIns'];?></td>
																	</tr>
																	<tr>
																		<td class="label_left"><?php  echo $langage_lbl_admin['LBL_PACKAGE_DETAILS'];?></td>
																		<td class="detail_right"><?=$dtls['tPackageDetails'];?></td>
																	</tr>
																	<tr>
																		<td class="label_left"><?=$langage_lbl_admin['LBL_DELIVERY_STATUS_TXT']; ?></td>
																		<td class="detail_right"><b><?=$dtls['iActive'];?></b></td>
																	</tr>
																	<?php  if(!empty($dtls['vSignImage'])) {?>
																		<tr>
																			<td class="label_left"><?=$langage_lbl_admin['LBL_RECEIVER_SIGN']; ?></td>
																			<td class="detail_right">
																				<?php  if(file_exists($tconfig["tsite_upload_trip_signature_images_path"]. '/'. $dtls['vSignImage'])){
																					$img1=$tconfig["tsite_upload_trip_signature_images"]. '/' .$dtls['vSignImage'];
																				} ?>
																				<img src="<?php  echo $img1;?>" align="left" >
																			</td>
																		</tr>
																	<?php  } ?>
																</table>
															</div>
															<?php  
																$i++;
															}
														}
													 ?>
												</div>
												
											<?php  } ?>
											
											<div class="row invoice-email-but">
												<span>
													<a href="../send_invoice_receipt.php?action_from=mail&iTripId=<?= $db_trip_data['iTripId']?>"><button class="btn btn-primary ">E-mail</button></a>
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>
							
						</div>
                        <div class="clear"></div>
					</div>
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>
		
		<!--END MAIN WRAPPER -->
		
		<?php  include_once('footer.php');?>
		<script src="../assets/js/gmap3.js"></script>
		<script>
			h = window.innerHeight;
			$("#page_height").css('min-height', Math.round( h - 99)+'px');
			
			function from_to(){
				
				$("#map-canvas").gmap3({
					getroute:{
						options:{
							//origin:'<?= $db_trip_data['tSaddress']?>',
							//destination:'<?= $db_trip_data['tDaddress']?>',
              origin:'<?php  echo $db_trip_data['tStartLat'].",".$db_trip_data['tStartLong']?>',
							destination:'<?php  echo $db_trip_data['tEndLat'].",".$db_trip_data['tEndLong']?>',
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
