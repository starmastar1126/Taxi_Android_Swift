<?php 
include_once('../common.php');

$tbl_name 	= 'trips';
if (!isset($generalobjAdmin)) {
	 require_once(TPATH_CLASS . "class.general_admin.php");
	 $generalobjAdmin = new General_admin();
}

$ENABLE_TIP_MODULE = $generalobj->getConfigurations("configurations","ENABLE_TIP_MODULE");

	include_once('send_invoice_receipt.php');
	$generalobjAdmin->check_member_login();
	$iTripId = isset($_REQUEST['iTripId'])?$_REQUEST['iTripId']:'';
	$script="Trips";
	$sql = "select trips.*,vVehicleType as eCarType from trips left join vehicle_type on vehicle_type.iVehicleTypeId=trips.iVehicleTypeId where iActive ='Canceled' AND iTripId = '".$iTripId."'";
	$db_trip = $obj->MySQLSelect($sql);
	#echo '<pre>'; print_R($db_trip); echo '</pre>'; exit;
	$sql = "SELECT vt.*,vc.vCategory_".$default_lang." as vehcat from vehicle_type as vt LEFT JOIN vehicle_category as vc ON vc.iVehicleCategoryId = vt.iVehicleCategoryId where iVehicleTypeId = '".$db_trip[0]['iVehicleTypeId']."'";
	$db_vtype = $obj->MySQLSelect($sql);
	#echo '<pre>'; print_R($db_trip); echo '</pre>'; exit;

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
	$diff = abs($ts2 - $ts1);
	if ($db_trip[0]['eFareType'] == "Hourly") {
		$diff 	=	0;
		$sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$iTripId'";
		$db_tripTimes = $obj->MySQLSelect($sql22);

		foreach($db_tripTimes as $dtT){
			if($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
				$diff += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
			}
		}
		$diff = abs($diff);
	}
	
	$years = floor($diff / (365*60*60*24)); $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
	$minuts = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
	$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));
	$diff = $hours.':'.$minuts.':'.$seconds;

	if ($hours > 0) {
		$totalTime = $hours.':'.$minuts.':'.$seconds;
	}if ($minuts > 0) {
		$totalTime = $minuts.':'.$seconds. " " . $langage_lbl_admin['LBL_MINUTES_TXT'];
	}
	if ($totalTime < 1) {
		$totalTime = $seconds . " " . $langage_lbl_admin['LBL_SECONDS_TXT'];
	}

	$totalTimeInMinutes_trip=@round(($diff) / 60,2);

function converToTz($time, $toTz, $fromTz,$dateFormat="Y-m-d H:i:s") {
	$date = new DateTime($time, new DateTimeZone($fromTz));
	$date->setTimezone(new DateTimeZone($toTz));
	$time = $date->format($dateFormat);
	return $time;
}		
if(file_exists($tconfig["tsite_upload_images_driver_path"]. '/' . $db_driver[0]['iDriverId'] . '/2_' . $db_driver[0]['vImage'])){
	$img=$tconfig["tsite_upload_images_driver"]. '/' . $db_driver[0]['iDriverId'] . '/2_' .$db_driver[0]['vImage'];
}else{
	$img=$tconfig["tsite_url"]."webimages/icons/help/driver.png";
}
if(file_exists($tconfig["tsite_upload_images_passenger_path"]. '/' . $db_user[0]['iUserId'] . '/2_' . $db_user[0]['vImgName'])){
	$img1=$tconfig["tsite_upload_images_passenger"]. '/' . $db_user[0]['iUserId'] . '/2_' .$db_user[0]['vImgName'];
}else{
	$img1=$tconfig["tsite_url"]."webimages/icons/help/taxi_passanger.png";
}

if($db_vtype[0]['vehcat'] != ""){
	
	$carname = $db_vtype[0]['vehcat']." - ".$db_trip[0]['eCarType'];
}else{
	
	$carname = $db_trip[0]['eCarType'];
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
					<?php  if ($_REQUEST['success'] ==1) { ?>
						 <div class="alert alert-success paddiing-10">
						 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						 Email send successfully.
						 </div>
				 <?php  }?>
				 <?php  
				 	$systemTimeZone = date_default_timezone_get();
					if($db_trip[0]['tStartDate']!= "" && $db_trip[0]['vTimeZone'] != "")  {
                        $dBookingDate = converToTz($db_trip[0]['tStartDate'],$db_trip[0]['vTimeZone'],$systemTimeZone);
                        $endDate = converToTz($db_trip[0]['tEndDate'],$db_trip[0]['vTimeZone'],$systemTimeZone);
                    } else {
                        $dBookingDate = $db_trip[0]['tStartDate'];
                        $endDate = $db_trip[0]['tEndDate'];
                    }
					
					if($db_trip[0]['iActive'] == 'Canceled' && $db_trip[0]['eCancelledBy'] == 'Driver'){
				 ?>
				 
					<div class="table-list">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										<b>Your <?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'];?> </b> <?php  if($db_trip[0]['tStartDate']== "0000-00-00 00:00:00"){ echo "Was Canceled.";}else{echo @date('h:i A',@strtotime($dBookingDate));?> on <?=@date('d M Y',@strtotime($dBookingDate));}?>
									</div>
									<div class="panel-body rider-invoice-new">
										<div class="row">

											<div class="col-sm-6 rider-invoice-new-left">
												<?php  if($APP_TYPE != 'UberX') { ?>
												<div id="map-canvas" class="gmap3" style="width:100%;height:200px;margin-bottom:10px;"></div>
												<?php  } ?>
												<span class="location-from"><i class="icon-map-marker"></i>
                                                <b><?=@date('h:i A',@strtotime($dBookingDate));?><p><?=$db_trip[0]['tSaddress'];?></p></b></span>
                                                 <?php  if($APP_TYPE != 'UberX'){ ?> 

												<span class="location-to"><i class="icon-map-marker"></i> <b><?=@date('h:i A',@strtotime($endDate));?><p><?=$db_trip[0]['tDaddress'];?></p></b></span>
												<?php  } ?> 

												<?php  
							              			if($APP_TYPE == 'UberX'){

							              				$class_name = 'col-sm-6';

							              			}else{

							              				$class_name = 'col-sm-4';
							              			}
							              			?>
                                                <div class="rider-invoice-bottom">
													<div class="<?php  echo $class_name; ?>">
															<?php  echo $langage_lbl_admin['LBL_CAR_TXT_ADMIN'];?> <br /> <b><?= $carname;?></b><br/>
													</div>

													 <?php  if($APP_TYPE != 'UberX'){ ?> 

													<div class="<?php  echo $class_name; ?>">
															Distance<br /> <b><?=$db_trip[0]['fDistance'];?> km</b> <br/>
													</div>
													


													<div class="<?php  echo $class_name; ?>">
															<?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'];?>  time<br /><b><?php echo $diff;?></b>
													</div>
													 <?php  } ?> 
												</div>
												<div class="rider-invoice-bottom">
													<div class="col-sm-6">
														<div class="left col-sm-3"> 
															<img src="<?php  echo $img;?>" style="outline:none;text-decoration:none;display:inline-block;width:45px!important;min-height:45px!important;border-radius:50em;max-width:45px!important;min-width:45px!important;border:1px solid #d7d7d7" align="left" height="45" width="45" class="CToWUd">
														</div>
														<div class="right col-sm-9">
															<div><b><?php  echo $langage_lbl_admin['LBL_DIVER'];?></b></div>
															<div><?php  echo $db_driver[0]['vName']."&nbsp;".$db_driver[0]['vLastName']; ?></div>
															<div><?php  echo $db_driver[0]['vEmail']; ?></div>
														</div>
													</div>
													<div class="col-sm-6">
														<div class="left col-sm-3"> 
															<img src="<?php  echo  $img1; ?>" style="outline:none;text-decoration:none;display:inline-block;width:45px!important;min-height:45px!important;border-radius:50em;max-width:45px!important;min-width:45px!important;border:1px solid #d7d7d7" align="left" height="45" width="45" class="CToWUd">
														</div>
														<div class="right col-sm-9">
															<div><b><?php  echo $langage_lbl_admin['LBL_RIDER'];?></b></div>
															<div><?php  echo $db_user[0]['vName']."&nbsp;".$db_user[0]['vLastName']; ?></div>
															<div><?php  echo $db_user[0]['vEmail']; ?></div>
														</div>
													</div>
												</div>
											</div>

									<div class="col-sm-6 rider-invoice-new-right">
									<h4 style="text-align:center;">	Cancellation Fare For Ride No  :<?= $db_trip[0]['vRideNo'];?></h4><hr/>
												<table style="width:100%" cellpadding="5" cellspacing="0" border="0">
												<?php 
												if($db_trip[0]['eFareType'] != 'Fixed' && $db_trip[0]['eFareType'] != 'Hourly')
												{
													?>
													<tr>
															<td>Basic Fare  </td>
															<td align="right"><?=$generalobj->trip_currency($db_trip[0]['iBaseFare']);?></td>
															</tr>
													<tr>
															<?php  if($APP_TYPE == "UberX") { ?>
																<td>Distance(<?=$db_trip[0]['fDistance'];?> km)</td>
															<?php  } else { ?>
																<td>Distance</td>
															<?php  } ?>
															<td align="right"><?=$generalobj->trip_currency($db_trip[0]['fPricePerKM']);?></td>
													</tr>
													<tr>
															<?php  if($APP_TYPE == "UberX") { ?>
																<td>Time(<?php  echo $totalTime?>)</td>
															<?php  } else { ?>
																<td>Time</td>
															<?php  } ?>
															
															<td align="right"><?=$generalobj->trip_currency($db_trip[0]['fPricePerMin']);?></td>
													</tr>
													<?php 
												} else {
													if($db_trip[0]['eFareType'] == 'Hourly') {
												?>
													<tr>
															<td><?php  echo $langage_lbl_admin['LBL_TIME_TXT'];?> (<?php  echo $totalTime?>)</td>
															<td align="right">
																<?=$generalobj->trip_currency($db_trip[0]['fPricePerMin']);?></td>
													</tr>
													<?php  } else {?>	
													<tr>
														<td><?php  echo $langage_lbl_admin['LBL_Total_Fare_TXT'];?> </td>
														<td align="right">
															<?php  $vVehicleFare_price = $generalobj->cal_trip_price_details($db_trip[0]['iTripId'],$db_trip[0]['iDriverVehicleId'],$db_trip[0]['iVehicleTypeId']);

															$vVehicleFare = $generalobj->trip_currency($vVehicleFare_price);

															echo ($db_trip[0]['iQty'] > 1)?$db_trip[0]['iQty'].' X '. $vVehicleFare : $vVehicleFare;?>
															</td>
													</tr>
													<?php }
												}
												if($db_trip[0]['fWalletDebit'] > 0)
												{
													?>
													<tr>
														<td><?=$langage_lbl['LBL_WALLET_DEBIT_MONEY']; ?> </td>
														<td align="right"> - <?=$generalobj->trip_currency($db_trip[0]['fWalletDebit']);?> </td>
												</tr>
													<?php 
												}
												
												if($db_trip[0]['fDiscount'] > 0)
												{
													?>
													<tr>
														<td><?=$langage_lbl['LBL_DISCOUNT']; ?> </td>
														<td align="right"> - <?=$generalobj->trip_currency($db_trip[0]['fDiscount']);?> </td>
												</tr>
													<?php 
												}
												
												if($db_trip[0]['fSurgePriceDiff'] > 0)
												{
													?>
													<tr>
														<td>Surge Money </td>
														<td align="right"><?=$generalobj->trip_currency($db_trip[0]['fSurgePriceDiff']);?></td>
													</tr>
														<?php 
													}
													
													?>
												<?php  
													if($db_trip[0]['fVisitFee'] > 0){ ?> 
														<tr>
														<td><?php  echo $langage_lbl_admin['LBL_VISIT_FEE'];?> </td>
														<td align="right"><?=$generalobj->trip_currency($db_trip[0]['fVisitFee']);?></td>
													</tr>
														
													<?php  	}
													if($db_trip[0]['fMaterialFee'] > 0){ ?>
														<tr>
														<td><?php  echo $langage_lbl_admin['LBL_MATERIAL_FEE'];?> </td>
														<td align="right"><?=$generalobj->trip_currency($db_trip[0]['fMaterialFee']);?></td>
													</tr>
													<?php  }
													if($db_trip[0]['fMiscFee'] > 0){ ?>
														<tr>
														<td><?php  echo $langage_lbl_admin['LBL_MISC_FEE'];?> </td>
														<td align="right"><?=$generalobj->trip_currency($db_trip[0]['fMiscFee']);?></td>
													</tr><?php  } 
													if($db_trip[0]['fDriverDiscount'] > 0){ ?>
														<tr>
														<td><?php  echo $langage_lbl_admin['LBL_PROVIDER_DISCOUNT'];?> </td>
														<td align="right"> - <?=$generalobj->trip_currency($db_trip[0]['fDriverDiscount']);?></td>
													</tr>
													<?php  } ?>
														<?php  

													if($db_trip[0]['fMinFareDiff']!="" && $db_trip[0]['fMinFareDiff'] > 0){
														$minimum_fare=$db_trip[0]['iBaseFare']+$db_trip[0]['fPricePerKM']+$db_trip[0]['fPricePerMin']+$db_trip[0]['fMinFareDiff'];
														?>

													<tr>
															<td><b><?=$generalobj->trip_currency($minimum_fare);?></b> <?=$langage_lbl['LBL_MINIMUM']; ?> </td>
															<td align="right"><?=$generalobj->trip_currency($db_trip[0]['fMinFareDiff']);?></td>
													</tr>

													<?php  } ?>
													<tr>
															<td>Commission </td>
															<td align="right">- <?=$generalobj->trip_currency($db_trip[0]['fCommision']);?></td>
													</tr>
													<tr>
															<td colspan="2"><hr style="margin-bottom:0px"/></td>
													</tr>
													<tr>
															<td><b><?php  echo $langage_lbl_admin['LBL_Total_Fare_TXT'];?> (Via <?=$db_trip[0]['vTripPaymentMode']?>)</b></td>
															<td align="right"><b><?=$generalobj->trip_currency($db_trip[0]['iFare']);?></b></td>
													</tr>

												</table><br><br><br>
												
												<?php  
													if($db_trip[0]['fTipPrice'] > 0)
													{
														if($ENABLE_TIP_MODULE == "Yes"){ 
														?>
															<table style="border:dotted 2px #000000;" cellpadding="5px" cellspacing="2px" width="100%">
																<tr>
																	<td><b>Tip given to Driver</b></td>
																	<td align="right"><b><?=$generalobj->trip_currency($db_trip[0]['fTipPrice']);?></b></td>
																</tr>
															</table><br><br><br>
														<?php }
													}
													
														
												if($db_trip[0]['vCancelReason'] != "")
												{?>
												<table style="border:dotted 2px #000000;" cellpadding="5px" cellspacing="2px" width="100%">
													<tr>
														<td><b>Trip Cancel Resion</b></td>
														<td align="right"><b><?php  echo $db_trip[0]['vCancelReason'];?></b></td>
													</tr>
												</table><br>
													<?php }?>
													
												
												
												<?php  if($db_trip[0]['eType'] == 'Deliver'){ ?>

												<h4 style="text-align:center;"><?php  echo $langage_lbl_admin['LBL_DELIVERY_DETAILS_TXT_ADMIN'];?></h4><hr/>

												<table style="width:100%" cellpadding="5" cellspacing="0" border="0">
													<tr>
															<td><?php  echo $langage_lbl['LBL_RECEIVER_NAME'];?></td>
															<td><?=$db_trip[0]['vReceiverName'];?></td>
													</tr>
													<tr>
															<td><?php  echo $langage_lbl['LBL_RECEIVER_MOBILE'];?></td>
															<td><?=$db_trip[0]['vReceiverMobile'];?></td>
													</tr>
													<tr>
															<td><?php  echo $langage_lbl['LBL_PICK_UP_INS'];?></td>
															<td><?=$db_trip[0]['tPickUpIns'];?></td>
													</tr>
													<tr>
															<td><?php  echo $langage_lbl['LBL_DELIVERY_INS'];?></td>
															<td><?=$db_trip[0]['tDeliveryIns'];?></td>
													</tr>
													<tr>
															<td><?php  echo $langage_lbl['LBL_PACKAGE_DETAILS'];?></td>
															<td><?=$db_trip[0]['tPackageDetails'];?></td>
													</tr>
													<tr>
															<td><?php  echo $langage_lbl['LBL_DELIVERY_CONFIRMATION_CODE_TXT'];?></td>
															<td><?=$db_trip[0]['vDeliveryConfirmCode'];?></td>
													</tr>
												</table>

												<?php  } ?>


												<?php  if($APP_TYPE == 'UberX' && $db_trip[0]['vBeforeImage'] != ''){

												 ?> 
												<h4 style="text-align:center;"><?php  echo $langage_lbl_admin['LBL_TRIP_DETAIL_HEADER_TXT'];?></h4><hr/>						

												<div class="invoice-right-bottom-img">
													<div class="col-sm-6">											
														<h3>
														<?php 														
														$img_path = $tconfig["tsite_upload_trip_images"];
														echo $langage_lbl_admin['LBL_SERVICE_BEFORE_TXT_ADMIN'];?></h3>
														 <b><a href="<?= $img_path .$db_trip[0]['vBeforeImage']; ?>" target="_blank" ><img src = "<?= $img_path.$db_trip[0]['vBeforeImage'] ?>" style="width:200px;" alt ="Before Images"/></b></a>
													</div>
													<div class="col-sm-6">
														<h3><?php  echo $langage_lbl_admin['LBL_SERVICE_AFTER_TXT_ADMIN'];?></h3>
														 <b><a href="<?= $img_path .$db_trip[0]['vBeforeImage']; ?>" target="_blank" ><img src = "<?= $img_path.$db_trip[0]['vAfterImage']; ?>" style="width:200px;" alt ="After Images"/></b></a>
												</div>

												<?php  } ?>

											</div>
										</div>						
										 <div class="clear"></div>
										<!--<div class="row invoice-email-but">
														<span>
														<a href="../send_invoice_receipt.php?action_from=mail&iTripId=<?= $db_trip[0]['iTripId']?>"><button class="btn btn-primary ">E-mail</button></a>
													</span>
											</div>-->
									</div>
									</div>
								</div>
							</div>
                         
						</div>
                        <div class="clear"></div>
					</div>
					<?php  } else{ ?>
						<div class="table-list">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										<b>Your <?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'];?> </b> <?php  if($db_trip[0]['tStartDate']== "0000-00-00 00:00:00"){ echo "Was Canceled.";}else{echo @date('h:i A',@strtotime($dBookingDate));?> on <?=@date('d M Y',@strtotime($dBookingDate));}?>
									</div>
									<div class="panel-body rider-invoice-new">
										<div class="row">								

									<div class="col-sm-12 rider-invoice-new-right">
									<h4 style="text-align:center;">	Cancellation Fare For Ride No  :<?= $db_trip[0]['vRideNo'];?></h4><hr/>
												<table style="width:100%" cellpadding="5" cellspacing="0" border="0">											
													
														
														<tr>
															<td>Cancellation Change </td>
															<td align="right"> <?=$generalobj->trip_currency($db_trip[0]['fCancellationFare']);?></td>
													</tr>
															
														
													
													<tr>
															<td colspan="2"><hr style="margin-bottom:0px"/></td>
													</tr>
													<tr>
															<td><b><?php  echo $langage_lbl_admin['LBL_Total_Fare_TXT'];?> (Via <?=$db_trip[0]['vTripPaymentMode']?>)</b></td>
															<td align="right"><b><?=$generalobj->trip_currency($db_trip[0]['fCancellationFare']);?></b></td>
													</tr>

												</table><br><br><br>
												
												<?php  
													if($db_trip[0]['fTipPrice'] > 0)
													{
													if($ENABLE_TIP_MODULE == "Yes"){ 
													?>
												<table style="border:dotted 2px #000000;" cellpadding="5px" cellspacing="2px" width="100%">
													<tr>
														<td><b>Tip given to Driver</b></td>
														<td align="right"><b><?=$generalobj->trip_currency($db_trip[0]['fTipPrice']);?></b></td>
													</tr>
												</table><br>
													<?php }}?>
													
												
												
												<?php  if($db_trip[0]['eType'] == 'Deliver'){ ?>

												<h4 style="text-align:center;"><?php  echo $langage_lbl_admin['LBL_DELIVERY_DETAILS_TXT_ADMIN'];?></h4><hr/>

												<table style="width:100%" cellpadding="5" cellspacing="0" border="0">
													<tr>
															<td><?php  echo $langage_lbl['LBL_RECEIVER_NAME'];?></td>
															<td><?=$db_trip[0]['vReceiverName'];?></td>
													</tr>
													<tr>
															<td><?php  echo $langage_lbl['LBL_RECEIVER_MOBILE'];?></td>
															<td><?=$db_trip[0]['vReceiverMobile'];?></td>
													</tr>
													<tr>
															<td><?php  echo $langage_lbl['LBL_PICK_UP_INS'];?></td>
															<td><?=$db_trip[0]['tPickUpIns'];?></td>
													</tr>
													<tr>
															<td><?php  echo $langage_lbl['LBL_DELIVERY_INS'];?></td>
															<td><?=$db_trip[0]['tDeliveryIns'];?></td>
													</tr>
													<tr>
															<td><?php  echo $langage_lbl['LBL_PACKAGE_DETAILS'];?></td>
															<td><?=$db_trip[0]['tPackageDetails'];?></td>
													</tr>
													<tr>
															<td><?php  echo $langage_lbl['LBL_DELIVERY_CONFIRMATION_CODE_TXT'];?></td>
															<td><?=$db_trip[0]['vDeliveryConfirmCode'];?></td>
													</tr>
												</table>

												<?php  } ?>


												<?php  if($APP_TYPE == 'UberX' && $db_trip[0]['vBeforeImage'] != ''){

												 ?> 
												<h4 style="text-align:center;"><?php  echo $langage_lbl_admin['LBL_TRIP_DETAIL_HEADER_TXT'];?></h4><hr/>						

												<div class="invoice-right-bottom-img">
													<div class="col-sm-6">											
														<h3>
														<?php 														
														$img_path = $tconfig["tsite_upload_trip_images"];
														echo $langage_lbl_admin['LBL_SERVICE_BEFORE_TXT_ADMIN'];?></h3>
														 <b><a href="<?= $img_path .$db_trip[0]['vBeforeImage']; ?>" target="_blank" ><img src = "<?= $img_path.$db_trip[0]['vBeforeImage'] ?>" style="width:200px;" alt ="Before Images"/></b></a>
													</div>
													<div class="col-sm-6">
														<h3><?php  echo $langage_lbl_admin['LBL_SERVICE_AFTER_TXT_ADMIN'];?></h3>
														 <b><a href="<?= $img_path .$db_trip[0]['vBeforeImage']; ?>" target="_blank" ><img src = "<?= $img_path.$db_trip[0]['vAfterImage']; ?>" style="width:200px;" alt ="After Images"/></b></a>
												</div>

												<?php  } ?>

											</div>
										</div>
										
										
										
										 <div class="clear"></div>
										<!--<div class="row invoice-email-but">
														<span>
														<a href="../send_invoice_receipt.php?action_from=mail&iTripId=<?= $db_trip[0]['iTripId']?>"><button class="btn btn-primary ">E-mail</button></a>
													</span>
											</div>-->
									</div>
									</div>
								</div>
							</div>
                         
						</div>
                        <div class="clear"></div>
					</div>
						
						
						<?php  } ?>
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
