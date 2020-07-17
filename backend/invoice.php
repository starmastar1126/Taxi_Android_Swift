<?php 
include_once('common.php');
include_once('generalFunctions.php');

$tbl_name 	= 'trips';
$script="Trips";
$generalobj->check_member_login();


$APP_DELIVERY_MODE = $generalobj->getConfigurations("configurations","APP_DELIVERY_MODE");
	
	
if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != "") {
	$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
	$_SESSION['HTTP_REFERER'] = $HTTP_REFERER;
	
} else {
	//$_SESSION['HTTP_REFERER'] = "";
}

$_REQUEST['iTripId'] = base64_decode(base64_decode(trim($_REQUEST['iTripId'])));

$iTripId = isset($_REQUEST['iTripId'])?$_REQUEST['iTripId']:'';
	
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$SITE_NAME?> |<?=$langage_lbl['LBL_MYEARNING_INVOICE']; ?> </title>
    <?php  include_once("top/top_script.php");?>  
     <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=<?=$GOOGLE_SEVER_API_KEY_WEB?>"></script>
</head>
<body>
<!-- home page -->
<div id="main-uber-page">
    <?php  include_once("top/left_menu.php");?>
    <?php  include_once("top/header_topbar.php");?>
    <?php  
    if($_SESSION['sess_user'] == 'driver'){
    	$db_trip_data = $generalobj->getTripPriceDetailsForWeb($iTripId,$_SESSION['sess_iUserId'],'Driver'); 
    } else if($_SESSION['sess_user'] == 'rider') {
    	$db_trip_data = $generalobj->getTripPriceDetailsForWeb($iTripId,$_SESSION['sess_iUserId'],'Passenger');
    } else {
    	$db_trip_data = $generalobj->getTripPriceDetailsForWeb($iTripId,'','');
    }
  // echo"<pre>";print_r($db_trip_data);die;?>
	<div class="page-contant">
		<div class="page-contant-inner page-trip-detail">
      		<h2 class="header-page trip-detail"><?=$langage_lbl['LBL_Invoice']; ?>				
				<a onClick="javascript:window.top.close();"><?=$langage_lbl['LBL_CLOSE_TXT']; ?></a>					
				<?php  $systemTimeZone = date_default_timezone_get();
                if($db_trip_data['fCancellationFare'] > 0 && $db_trip_data['vTimeZone'] != "") {
					 $dBookingDate = $endDate = converToTz($db_trip_data['tEndDate'],$db_trip_data['vTimeZone'],$systemTimeZone);
				} else if($db_trip_data['tTripRequestDateOrig']!= "" && $db_trip_data['vTimeZone'] != "")  {
                      $dBookingDate = converToTz($db_trip_data['tTripRequestDateOrig'],$db_trip_data['vTimeZone'],$systemTimeZone);
                      $endDate = converToTz($db_trip_data['tEndDate'],$db_trip_data['vTimeZone'],$systemTimeZone);
                } else {
                      $dBookingDate = $db_trip_data['tTripRequestDateOrig'];
                      $endDate = $db_trip_data['tEndDate'];
                }
				if(!empty($db_trip_data)){?>
        		<p><?=$langage_lbl['LBL_RATING_PAGE_HEADER_TXT']; ?> <strong><?=@date('h:i A',@strtotime($dBookingDate));?> <?=$langage_lbl['LBL_ON']; ?> <?=@date('d M Y',@strtotime($dBookingDate));?></strong></p>
				<?php  }?>
      		</h2>
			<?php  if(!empty($db_trip_data)){ ?>
	      	<div class="trip-detail-page">
	            <div class="trip-detail-page-inner">
	        		<div class="trip-detail-page-left">
	        			<?php  if($APP_TYPE != 'UberX' && ($APP_DELIVERY_MODE != "Multi" || $db_trip_data['eType'] == "Ride")) { ?>
	          			<div class="trip-detail-map">
	          				<div id="map-canvas" class="gmap3" style="width:100%;height:200px;margin-bottom:10px;"></div>
	          			</div>
	          			<?php  } ?>
	          			<div class="map-address">
	            			<ul>
	              				<li> 
	              					<b><i aria-hidden="true" class="fa fa-map-marker fa-22 green-location"></i></b> 
	          						<span>
	                					<h3><?=@date('h:i A',@strtotime($dBookingDate));?></h3>
	            						<?=$db_trip_data['tSaddress'];?>
	        						</span> 
	    						</li>
	                 			<?php  if($APP_TYPE != 'UberX' && ($APP_DELIVERY_MODE != "Multi" || $db_trip_data['eType'] == "Ride")){ ?> 
	          					<li> 
	          						<b><i aria-hidden="true" class="fa fa-map-marker fa-22 red-location"></i></b> 
	      							<span>
	                					<h3><?=@date('h:i A',@strtotime($endDate));?></h3>
	                					<?=$db_trip_data['tDaddress'];?>
	                				</span> 
	            				</li>
	                    		<?php  } ?> 
	            			</ul>
	          			</div>
		                <?php  
		                if($APP_TYPE == 'UberX') {
		                  $class_name = 'location-time location-time-second';
		                } else {
		                  $class_name = 'location-time';
		                }
		                ?>
	          			<div class="<?php  echo $class_name?>">
	            			<ul>
	                  			<li>
	                    			<h3><?=$langage_lbl['LBL_INVOICE_Car']; ?></h3>
	                    			<?php  if(!empty($db_trip_data['vVehicleCategory'])){
	                    			  echo $db_trip_data['vVehicleCategory'] . "-" . $db_trip_data['vVehicleType'];
	                    			} else {
	                    			  echo $db_trip_data['vVehicleType'];
	                    			} ?>
	            				</li>
	                  			<?php  if($APP_TYPE != 'UberX'){ ?> 
	                  			<li>
	                    			<h3><?=$langage_lbl['LBL_DISTANCE_TXT']; ?></h3>
	                    			<?php  ?>
	                    			<?=$db_trip_data['fDistance'].$db_trip_data['DisplayDistanceTxt'];?> 
	                			</li>
	                  			<li>
	                    			<h3><?=$langage_lbl['LBL_Trip_time']; ?></h3>
	                    			<?php echo $db_trip_data['TripTimeInMinutes'];?>
	                			</li>
	                			<?php  } ?> 
	                		</ul>
	          			</div>
	        		</div>
	        		<div class="trip-detail-page-right">
	        			<?php  if ($_SESSION['sess_user']== "company") {?>
	        			<div class="driver-info" style="height: auto;">
	          				<h3 style=" margin: 30px 0;"><?= $generalobj->clearName($db_trip_data['PassengerDetails']['vName'].' '.$db_trip_data['PassengerDetails']['vLastName']);?> <?=$langage_lbl['LBL_RIDE_TXT_ADMIN']; ?> <?=$langage_lbl['LBL_WITH_TXT']; ?>  <?= $generalobj->clearName($db_trip_data['DriverDetails']['vName'].' '.$db_trip_data['DriverDetails']['vLastName']);?></h3>
	          			</div>
	          			<?php  } else if($_SESSION['sess_user']== "driver") { ?>
	          			<div class="driver-info">
	          				<div class="driver-img">
	          					<span>
	          					<?php  if($db_trip_data['PassengerDetails']['vImgName'] != '' && file_exists($tconfig["tsite_upload_images_passenger_path"]. '/' . $db_trip_data['PassengerDetails']['iUserId'] . '/2_' . $db_trip_data['PassengerDetails']['vImgName'])){
	      						?>
	          						<img src = "<?= $tconfig["tsite_upload_images_passenger"]. '/' . $db_trip_data['PassengerDetails']['iUserId'] . '/2_' .$db_trip_data['PassengerDetails']['vImgName'] ?>" style="height:150px;"/>
	      						<?php  }else{ ?>
									<img src="assets/img/profile-user-img.png" alt="">
								<?php  } ?>
	          				</div>
	            			<h3><?=$langage_lbl['LBL_You_ride_with']; ?> <?= $generalobj->clearName($db_trip_data['PassengerDetails']['vName'].' '.$db_trip_data['PassengerDetails']['vLastName']);?></h3>
	            			<p><b><?=$langage_lbl['LBL_Rate_Your_Ride']; ?>:</b>
	            				<?php 
	            				$rating_width = ($db_trip_data['TripRating'] * 100) / 5;
	            				$db_trip_data['TripRating'] = '<span style="display: block; width: 65px; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 0;">
								<span style="margin: 0;float:left;display: block; width: '.$rating_width.'%; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 -13px;"></span>
								</span>';
								?>
	            				<?=$db_trip_data['TripRating'];?></p>
	          			</div>
	          			<?php  } else { ?>
	          			<div class="driver-info">
	          				<div class="driver-img">
	          					<span>
	          					<?php  if($db_trip_data['DriverDetails']['vImage'] != '' && file_exists($tconfig["tsite_upload_images_driver_path"]. '/' . $db_trip_data['DriverDetails']['iDriverId'] . '/2_' . $db_trip_data['DriverDetails']['vImage'])){
	      						?>
	          						<img src = "<?= $tconfig["tsite_upload_images_driver"]. '/' . $db_trip_data['DriverDetails']['iDriverId'] . '/2_' .$db_trip_data['DriverDetails']['vImage'] ?>" style="height:150px;"/>
	      						<?php  }else{ ?>
									<img src="assets/img/profile-user-img.png" alt="">
								<?php  } ?>
	          				</div>
	            			<h3><?=$langage_lbl['LBL_You_ride_with']; ?> <?= $generalobj->clearName($db_trip_data['DriverDetails']['vName'].' '.$db_trip_data['DriverDetails']['vLastName']);?></h3>
	            			<p><b><?=$langage_lbl['LBL_Rate_Your_Ride']; ?>:</b>
	            				<?php 
	            				$rating_width = ($db_trip_data['TripRating'] * 100) / 5;
	            				$db_trip_data['TripRating'] = '<span style="display: block; width: 65px; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 0;">
								<span style="margin: 0;float:left;display: block; width: '.$rating_width.'%; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 -13px;"></span>
								</span>';
								?>
	            				<?=$db_trip_data['TripRating'];?></p>
	          			</div>
	          			<?php  } ?>
	      				<div class="fare-breakdown">
	            			<div class="fare-breakdown-inner">
	              				<h3><?php  echo $langage_lbl['LBL_FARE_BREAKDOWN_RIDE_NO_TXT'];?>. <b><?= $db_trip_data['vRideNo']; ?></b></h3>
	              				<ul>
									<?php 
									foreach ($db_trip_data['HistoryFareDetailsNewArr'] as $key => $value) {									foreach ($value as $k => $val) {
											if($k == $langage_lbl['LBL_EARNED_AMOUNT']) {
												continue;
											} else if($k == $langage_lbl['LBL_SUBTOTAL_TXT']){
												continue;
											} else { ?>
												<li><strong><?=$k; ?></strong><b><?php  echo $val;?></b></li>
									<?php 		}
										}
									}
									 ?>
	              				</ul>
	              				<span>
								<?php  					
								$paymentMode = ($db_trip_data['vTripPaymentMode'] == 'Cash')? $langage_lbl['LBL_VIA_CASH_TXT']: $langage_lbl['LBL_VIA_CARD_TXT']?>
	              					<h4><?=$langage_lbl['LBL_Total_Fare']; ?> (<?php  echo $paymentMode?>)</h4>
	              					<?php  if($_SESSION['sess_user']== "driver" || $_SESSION['sess_user']== "company"){ ?>
	              						<em><?=$db_trip_data['HistoryFareDetailsArr'][$langage_lbl['LBL_EARNED_AMOUNT']];?></em>
	              					<?php  } else { ?>
	              						<em><?=$db_trip_data['HistoryFareDetailsArr'][$langage_lbl['LBL_SUBTOTAL_TXT']];?></em>
	              					<?php  } ?>
	          					</span>
								<?php  if($db_trip_data['fTipPrice'] !="" && $db_trip_data['fTipPrice'] !="0" && $db_trip_data['fTipPrice'] !="0.00")
								{ ?>
									<ul><li><strong><?=$langage_lbl['LBL_TIP_RS_TXT']; ?></strong><b> <?=$db_trip_data['fTipPrice'];?></b></li></ul>
								<?php } ?>
	              				<div style="clear:both;"></div>
	                      <?php  if($db_trip_data['eType'] == 'Deliver' && $APP_DELIVERY_MODE != 'Multi'){ ?>
	                      <br>
	                    <h3><?=$langage_lbl['LBL_DELIVERY_DETAILS']; ?></h3><hr/>

	                    <ul style="border-bottom:none">
	                        <li><strong><?=$langage_lbl['LBL_RECEIVER_NAME']; ?> </strong><b><?=$db_trip_data['vReceiverName'];?></b></li>
	                        <li><strong><?=$langage_lbl['LBL_RECEIVER_MOBILE']; ?> </strong><b><?=$db_trip_data['vReceiverMobile'];?></b></li>
	                        <li><strong><?=$langage_lbl['LBL_PICK_UP_INS']; ?> </strong><b><?=$db_trip_data['tPickUpIns'];?></b></li>
	                        <li><strong><?=$langage_lbl['LBL_DELIVERY_INS']; ?> </strong><b><?=$db_trip_data['tDeliveryIns'];?></b></li>
	                        <li><strong><?=$langage_lbl['LBL_PACKAGE_DETAILS']; ?></strong><b><?=$db_trip_data['tPackageDetails'];?></b></li>
	                        <li><strong><?=$langage_lbl['LBL_DELIVERY_CONFIRMATION_CODE_TXT']; ?> </strong><b><?=$db_trip_data['vDeliveryConfirmCode'];?></b></li>       
	                      
	                    </ul>

	                    <?php  } ?>

	                    <div style="clear:both;"></div>
	                    <?php  if($APP_TYPE == 'UberX' &&  ($db_trip_data['vBeforeImage'] != '' || $db_trip_data['vAfterImage'] != '')){
	                        $img_path = $tconfig["tsite_upload_trip_images"];
	                     ?> 
	                     <h3><?php  echo $langage_lbl_admin['LBL_TRIP_DETAIL_HEADER_TXT'];?></b></h3>                      

	                    <div class="invoice-right-bottom-img">
	                      <?php  if($db_trip_data['vBeforeImage'] != '') { ?>                     
	                      	<div class="col-sm-6">
	                        <h4> <?php  echo $langage_lbl_admin['LBL_SERVICE_BEFORE_TXT_ADMIN'];?></h4>
	                         <b><a href="<?= $db_trip_data['vBeforeImage'] ?>" target="_blank" ><img src = "<?= $db_trip_data['vBeforeImage'] ?>" style="width:200px;" alt ="Before Images"/></a></b>
	                     	</div>
	                        <?php  } 
	                         if($db_trip_data['vAfterImage'] != '') {?>
	                      <div class="col-sm-6">
	                        <h4><?php  echo $langage_lbl_admin['LBL_SERVICE_AFTER_TXT_ADMIN'];?></h4>
	                         <b><a href="<?= $db_trip_data['vAfterImage'] ?>" target="_blank" ><img src = "<?= $db_trip_data['vAfterImage'] ?>" style="width:200px;" alt ="After Images"/></a></b>
	                      </div>
	                         <?php  } ?>
	                    </div>
	                    <?php  } ?>
	            		</div>
	          			</div>
	        		</div>
					<div style="clear:both;"></div>
					<?php  if($APP_DELIVERY_MODE == "Multi" && $db_trip_data['eType'] == 'Deliver'){?>
						
	        			<div class="invoice-part-bottom invoice-part-bottom1">	
	      				<?php  	
	      				$sql1 = "SELECT * FROM trips_delivery_locations AS tdl WHERE iTripId = '".$iTripId."'";
      					$db_trips_locations = $obj->MySQLSelect($sql1);
      					 ?>
	                        <?php  $i=1 ;if(!empty($db_trips_locations)){
							foreach($db_trips_locations as $dtls) { 
								$class = (!empty($dtls['vSignImage'])) ? 'sign-img' : ''; ?>
	                    	<div class="col-sm-6 <?php  echo $class;?>"> 
	                        <h3><?= $langage_lbl['LBL_RECIPIENT_LIST_TXT'].'&nbsp;'. $i; ?></h3>
	                        <table style="width:100%" class="deliverytable" cellpadding="5" cellspacing="0" border="0">
								<tr>
	                            	<td style="min-width: 150px;"><b><?=$langage_lbl['LBL_RECIPIENT_NAME_HEADER_TXT']; ?> </b></td>
	                            	<td><?=$dtls['vReceiverName'];?></td>
	                        	</tr>
	                            <tr>
	                            	<td style="min-width: 150px;"><b><?=$langage_lbl['LBL_DROP_OFF_LOCATION_RIDE_DETAIL']; ?> </b></td>
	                            	<td><?=$dtls['tPickUpIns'].",".$dtls['tDaddress'];?></td>
	                        	</tr>
	                            <tr>
	                            	<td style="min-width: 150px;"><b><?=$langage_lbl['LBL_DELIVERY_INS']; ?></b></td>
	                            	<td><?=$dtls['tDeliveryIns'];?></td>
	                            </tr>
	                            <tr>
	                        		<td style="min-width: 150px;"><b><?=$langage_lbl['LBL_PACKAGE_DETAILS']; ?></b></td>
	                    			<td><?=$dtls['tPackageDetails'];?></td>
	                			</tr>
	                    		<tr>
	                            	<td style="min-width: 150px;"><b><?=$langage_lbl['LBL_DELIVERY_STATUS_TXT']; ?></b></td>
	                        		<td><?=$dtls['iActive'];?></td>
	                    		</tr>
	                    		<?php  if(!empty($dtls['vSignImage'])) {?>
	                    		<tr>
	                            	<td class="label_left"><b><?=$langage_lbl['LBL_RECEIVER_SIGN']; ?></b></td>
	                        		<td class="detail_right">
	                        			<?php  if(file_exists($tconfig["tsite_upload_trip_signature_images_path"]. '/'. $dtls['vSignImage'])){
									$img1=$tconfig["tsite_upload_trip_signature_images"]. '/' .$dtls['vSignImage'];
								} ?>
									<img src="<?php  echo $img1;?>" align="left" style="width:150px;" >
								</td>
	                    		</tr>
	                    		<?php  } ?>
	                        </table>
	                    	</div>
							<?php  
								$i++;
								} }   ?>               				 
						</div>
					
					<?php  } ?>
	                </div>
	      		</div>
				<?php 
				}
				else
				{
				?>
				<div class="trip-detail-page">
	            <div class="trip-detail-page-inner">
					We could not find INVOICE details for this Trip. Please click browser's back button and check again.
				</div>
				</div>
				<?php  }?> 
    	</div>
	</div>
    <?php  include_once('footer/footer_home.php');?>
    <div style="clear:both;"></div>
</div>
<!-- home page end-->
<!-- Footer Script -->
<?php  include_once('top/footer_script.php');?>
<script src="assets/js/gmap3.js"></script>
<script type="text/javascript">
	h = window.innerHeight;
	$("#page_height").css('min-height', Math.round( h - 99)+'px');

	function from_to(){

		$("#map-canvas").gmap3({
			getroute:{
				options:{
					/*origin:'<?= $db_trip_data['tSaddress']?>',
					destination:'<?= $db_trip_data['tDaddress']?>',*/
        		origin:'<?=$db_trip_data['tStartLat'].",".$db_trip_data['tStartLong']?>',
					destination:'<?=$db_trip_data['tEndLat'].",".$db_trip_data['tEndLong']?>',
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
<!-- End: Footer Script -->
</body>
</html>
