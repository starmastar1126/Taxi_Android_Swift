<?php 
	include_once('common.php');
	include_once('generalFunctions.php');
	$tbl_name 	= 'trips';
	$generalobj->check_member_login();

	$APP_DELIVERY_MODE = $generalobj->getConfigurations("configurations","APP_DELIVERY_MODE");
	
	if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != "")
	{
		$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
		$_SESSION['HTTP_REFERER'] = $HTTP_REFERER;
		
	}
	else
	{
		//$_SESSION['HTTP_REFERER'] = "";
	}
	$_REQUEST['iTripId'] = base64_decode(base64_decode(trim($_REQUEST['iTripId'])));
	$iTripId = isset($_REQUEST['iTripId'])?$_REQUEST['iTripId']:'';
	$script="Trips";
	$sdsql = "";
	if($_SESSION['sess_user']== "driver")
	{
		$sess_iUserId = $_SESSION['sess_iUserId'];
		$sdsql = " AND iDriverId = '".$sess_iUserId."' ";
	}
	
	if($_SESSION['sess_user']== "rider")
	{
		$sess_iUserId = $_SESSION['sess_iUserId'];
		$sdsql = " AND iUserId = '".$sess_iUserId."' ";
	}
	
	$sql = "select trips.*,vVehicleType as eCarType from trips left join vehicle_type on vehicle_type.iVehicleTypeId=trips.iVehicleTypeId where iTripId = '".$iTripId."'" . $sdsql;
	$db_trip = $obj->MySQLSelect($sql);
	
	
	$eUnit = $db_trip[0]['vCountryUnitRider'];
	$tripDistance = $db_trip[0]['fDistance'];
	if($eUnit == "Miles"){
		$DisplayDistanceTxt = $langage_lbl['LBL_MILE_DISTANCE_TXT']; 
		$tripDistanceDisplay =round($tripDistance * 0.621371, 2);
		}else{
		$DisplayDistanceTxt = $langage_lbl['LBL_KM_DISTANCE_TXT'];
		$tripDistanceDisplay = $tripDistance;
	}
	
	$sql = "SELECT vt.*,vc.vCategory_".$_SESSION['sess_lang']." as vehcat from vehicle_type as vt LEFT JOIN vehicle_category as vc ON vc.iVehicleCategoryId = vt.iVehicleCategoryId where iVehicleTypeId = '".$db_trip[0]['iVehicleTypeId']."'";
	$db_vtype = $obj->MySQLSelect($sql);
	 if($db_vtype[0]['vehcat'] != ""){
		   $car = $db_vtype[0]['vehcat'] .' - '.$db_vtype[0]['vVehicleType_'.$_SESSION['sess_lang']];
    }else{
       $car = $db_vtype[0]['vVehicleType_'.$_SESSION['sess_lang']];
    }

	$sql = "select * from ratings_user_driver where iTripId = '".$iTripId."' AND eUserType='Driver'";
	$db_ratings = $obj->MySQLSelect($sql);
	//echo"<pre>";print_r($db_ratings);exit;

	$rating_width = ($db_ratings[0]['vRating1'] * 100) / 5;
	$db_ratings[0]['vRating1'] = '<span style="display: block; width: 65px; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 0;">
		<span style="float: left !important; margin: 0;display: block; width: '.$rating_width.'%; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 -13px;"></span>
		</span>';
		//echo"<pre>";print_r($db_ratings);exit;
	$sql = "select * from register_driver where iDriverId = '".$db_trip[0]['iDriverId']."' LIMIT 0,1";
	$db_driver = $obj->MySQLSelect($sql);

	$sql = "select * from register_user where iUserId = '".$db_trip[0]['iUserId']."' LIMIT 0,1";
	$db_user = $obj->MySQLSelect($sql);
	
	$sql = "SELECT Ratio, vName, vSymbol FROM currency WHERE vName='".$db_user[0]['vCurrencyPassenger']."'";
    $db_curr_ratio = $obj->MySQLSelect($sql);

	$tripcursymbol=$db_curr_ratio[0]['vSymbol'];
	$tripcur=$db_curr_ratio[0]['Ratio'];
	$tripcurname=$db_curr_ratio[0]['vName'];
	if($db_trip[0]['fCancellationFare'] > 0){
		$ts1 = strtotime($db_trip[0]['tEndDate']);
	} else {
		$ts1 = strtotime($db_trip[0]['tStartDate']);
	}
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
	

	if ($days > 0){
		$hours = ($days * 24) + $hours;
	}
	if ($hours > 0) {
		$totalTime = $hours.':'.$minuts.':'.$seconds;
	}else if ($minuts > 0) {
		$totalTime = $minuts.':'.$seconds. " " . $langage_lbl['LBL_MINUTES_TXT'];
	}
	if ($totalTime < 1) {
		$totalTime = $seconds . " " . $langage_lbl['LBL_SECONDS_TXT'];
	}
	$diffss = $hours.':'.$minuts.':'.$seconds;

	$totalTimeInMinutes_trip=@round(($diff) / 60,2);

	
	if($_SESSION['sess_user']== "company")
	{
		$sql = "select iCompanyId from register_driver where iDriverId = '".$db_trip[0]['iDriverId']."' LIMIT 0,1";
		$db_check = $obj->MySQLSelect($sql);
		if($db_check[0]['iCompanyId'] != $_SESSION['sess_iCompanyId'])
			$db_trip = array();
	}
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_MYEARNING_INVOICE']; ?></title>
    <!-- Default Top Script and css -->
    <?php  include_once("top/top_script.php");?>
   
     <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=<?=$GOOGLE_SEVER_API_KEY_WEB?>"></script>

    <!-- End: Default Top Script and css-->
</head>
<body>
     <!-- home page -->
    <div id="main-uber-page">
   <!-- Left Menu -->
    <?php  include_once("top/left_menu.php");?>
    <!-- End: Left Menu-->
        <!-- Top Menu -->
        <?php  include_once("top/header_topbar.php");?>
        <!-- End: Top Menu-->
        <!-- contact page-->
        <div class="page-contant">
    		<div class="page-contant-inner page-trip-detail">
          		<h2 class="header-page trip-detail"><?=$langage_lbl['LBL_RIDER_Invoice']; ?>
          			<!--<a href="<?=$tconfig["tsite_url"].'mytrip'?>"><img src="assets/img/arrow-white.png" alt="" /><?=$langage_lbl['LBL_RIDER_back_to_listing']; ?></a>-->
					
					<a onClick="javascript:window.top.close();"><?=$langage_lbl['LBL_CLOSE_TXT']; ?></a>
					<?php  	$systemTimeZone = date_default_timezone_get();
						if($db_trip[0]['fCancellationFare'] > 0 && $db_trip[0]['vTimeZone'] != "") {
							 $dBookingDate = $endDate = converToTz($db_trip[0]['tEndDate'],$db_trip[0]['vTimeZone'],$systemTimeZone);
						} else if($db_trip[0]['tStartDate']!= "" && $db_trip[0]['vTimeZone'] != "")  {
	                          $dBookingDate = converToTz($db_trip[0]['tStartDate'],$db_trip[0]['vTimeZone'],$systemTimeZone);
	                          $endDate = converToTz($db_trip[0]['tEndDate'],$db_trip[0]['vTimeZone'],$systemTimeZone);
                        } else {
	                          $dBookingDate = $db_trip[0]['tStartDate'];
	                          $endDate = $db_trip[0]['tEndDate'];
                        }
					if(count($db_trip) > 0){?>
            		<p><?=$langage_lbl['LBL_RIDER_RATING_PAGE_HEADER_TXT']; ?> <strong><?=@date('h:i A',@strtotime($dBookingDate));?> <?=$langage_lbl['LBL_ON']; ?> <?=@date('d M Y',@strtotime($dBookingDate));?></strong></p>
					<?php  }?>
          		</h2>
          		<!-- trips detail page -->
				<?php  
				if(count($db_trip) > 0)	
				{
				?>
          		<div class="trip-detail-page">
                <div class="trip-detail-page-inner">
            		<div class="trip-detail-page-left">
            			<?php  if($APP_TYPE != 'UberX' && ($APP_DELIVERY_MODE != "Multi" || $db_trip[0]['eType'] == "Ride")){ ?>
              			<div class="trip-detail-map"><div id="map-canvas" class="gmap3" style="width:100%;height:200px;margin-bottom:10px;"></div></div>
              			<?php  } ?>
              			<div class="map-address">
                			<ul>
                  				<li> 
                  					<b><i aria-hidden="true" class="fa fa-map-marker fa-22 green-location"></i></b> 
              						<span>
                    					<h3><?=@date('h:i A',@strtotime($dBookingDate));?></h3>
                						<?=$db_trip[0]['tSaddress'];?>
            						</span> 
        						</li>
        						<?php  if($APP_TYPE != 'UberX' && ($APP_DELIVERY_MODE != "Multi" || $db_trip[0]['eType'] == "Ride")){ ?> 
              					<li> 
              						<b><i aria-hidden="true" class="fa fa-map-marker fa-22 red-location"></i></b> 
          							<span>
                    					<h3><?=@date('h:i A',@strtotime($endDate));?></h3>
                    					<?=$db_trip[0]['tDaddress'];?>
                    				</span> 
                				</li>
                				<?php  } ?> 
                			</ul>
              			</div>
              			<?php  
              			if($APP_TYPE == 'UberX'){

              				$class_name = 'location-time location-time-second';

              			}else{

              				$class_name = 'location-time';
              			}
              			?>
              			<div class="<?php  echo $class_name;?>">
	            			<ul>
	                  			<li>
	                    			<h3><?=$langage_lbl['LBL_RIDER_RIDER_INVOICE_Car']; ?></h3>
	                    				<?php //=$db_vtype[0]['vehcat'].$car;?>
										<?=$car;?>
	            				</li>
	            					<?php  if($APP_TYPE != 'UberX'){ ?> 
	                  			<li>
	                    			<h3><?=$langage_lbl['LBL_RIDER_DISTANCE_TXT']; ?></h3>
	                    			<?=$tripDistanceDisplay." ". $DisplayDistanceTxt;?> 
	                			</li>
	                			
	                  			<li>
	                    			<h3><?=$langage_lbl['LBL_RIDER_Trip_time']; ?></h3>
	                    			<?php echo $diffss;?>
	                			</li>
	                			<?php  } ?>
	                		</ul>
              			</div>
            		</div>
            		<div class="trip-detail-page-right">
              			<div class="driver-info">
              				<div class="driver-img">
              					<span class="invoice-img">
													<?php  if($db_driver[0]['vImage'] != '' && file_exists($tconfig["tsite_upload_images_driver_path"]. '/' . $db_driver[0]['iDriverId'] . '/2_' . $db_driver[0]['vImage'])){?>
													<img src = "<?= $tconfig["tsite_upload_images_driver"]. '/' . $db_driver[0]['iDriverId'] . '/2_' .$db_driver[0]['vImage'] ?>" style="height:150px;"/>
													<?php  }else{ ?>
													<img src="assets/img/profile-user-img.png" alt="">
													<?php  } ?></span>
              				</div>
                			<h3><?=$langage_lbl['LBL_RIDER_You_ride_with']; ?> <?= $generalobj->clearName($db_driver[0]['vName'].' '.$db_driver[0]['vLastName']);?></h3>
                			<p><b><?=$langage_lbl['LBL_RIDER_Rate_Your_Ride']; ?>:</b><?=$db_ratings[0]['vRating1'];?></p>
              			</div>
          				<div class="fare-breakdown">
                			<div class="fare-breakdown-inner">
                  				<h3><?=$langage_lbl['LBL_RIDER_FARE_BREAK_DOWN_TXT']; ?></h3>
                  				<ul>
									<?php 
									if($db_trip[0]['fCancellationFare'] > 0){ ?>
										<li><strong><?=$langage_lbl['LBL_CANCELLATION_FEE']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fCancellationFare'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
									<?php  }
									else if($db_trip[0]['eFareType'] != 'Fixed' && $db_trip[0]['eFareType'] != 'Hourly')
									{
										?>
										<li><strong><?=$langage_lbl['LBL_RIDER_Basic_Fare']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['iBaseFare'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php  if($APP_TYPE == "UberX") {?>
											<li><strong><?=$langage_lbl['LBL_RIDER_DISTANCE_TXT']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fPricePerKM'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
											<li><strong><?=$langage_lbl['LBL_RIDER_TIME_TXT']; ?> </strong><b><?=$generalobj->trip_currency($db_trip[0]['fPricePerMin'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php  } else { ?>
											<li><strong><?=$langage_lbl['LBL_RIDER_DISTANCE_TXT']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fPricePerKM'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
											<li><strong><?=$langage_lbl['LBL_RIDER_TIME_TXT']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fPricePerMin'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php  }
									}
									else
									{
										if($db_trip[0]['eFareType'] == 'Hourly') { ?>													
											<li><strong><?php  echo $langage_lbl['LBL_TIME_TXT'];?> (<?php  echo $totalTime?>)</strong><b><?=$generalobj->trip_currency($db_trip[0]['fPricePerMin'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php  } else {?>	
											<li><strong><?=$langage_lbl['LBL_RIDER_Total_Fare']; ?></strong><b>
												<?php  
											$vVehicleFare_price = $generalobj->cal_trip_price_details($db_trip[0]['iTripId'],$db_trip[0]['iDriverVehicleId'],$db_trip[0]['iVehicleTypeId']);

											$vVehicleFare = $generalobj->trip_currency($vVehicleFare_price,$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);

											echo ($db_trip[0]['iQty'] > 1)?$db_trip[0]['iQty'].' X '. $vVehicleFare : $vVehicleFare;?>
												<!-- <?=$generalobj->trip_currency($db_trip[0]['iFare'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?> -->
													
												</b></li>
										<?php  }
									}
									if($db_trip[0]['fWalletDebit'] > 0)
									{
										?>
											<li><strong><?=$langage_lbl['LBL_RIDER_WALLET_DEBIT_MONEY']; ?></strong><b> - <?=$generalobj->trip_currency($db_trip[0]['fWalletDebit'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?> </b></li>
											<?php 
									}
									if($db_trip[0]['fDiscount'] > 0)
									{
										?>
										<li><strong><?=$langage_lbl['LBL_RIDER_DISCOUNT']; ?> </strong><b> - <?=$generalobj->trip_currency($db_trip[0]['fDiscount'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php 
									}
									if($db_trip[0]['fSurgePriceDiff'] > 0)
										{
											?>
											<li><strong><?=$langage_lbl['LBL_RIDER_SURGE_MONEY']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fSurgePriceDiff'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
											<?php 
										}
									?>

				                    <!-- <li><strong><?=$langage_lbl['LBL_RIDER_Commision']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fCommision'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li> -->							
									<?php  
										if($db_trip[0]['fVisitFee'] > 0){ ?> 
										<li><strong><?=$langage_lbl['LBL_VISIT_FEE']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fVisitFee'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php  } ?>	
										<?php  
										if($db_trip[0]['fMaterialFee'] > 0){ ?> 
										<li><strong><?=$langage_lbl['LBL_MATERIAL_FEE']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fMaterialFee'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php  } ?>	
										<?php  
										if($db_trip[0]['fMiscFee'] > 0){ ?> 
										<li><strong><?=$langage_lbl['LBL_MISC_FEE']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fMiscFee'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php  } ?>	
										<?php  
										if($db_trip[0]['fDriverDiscount'] > 0){ ?> 
										<li><strong><?=$langage_lbl['LBL_PROVIDER_DISCOUNT']; ?></strong><b> - <?=$generalobj->trip_currency($db_trip[0]['fDriverDiscount'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
										<?php  } ?>
				                     <?php  if($db_trip[0]['fMinFareDiff']!="" && $db_trip[0]['fMinFareDiff'] > 0){
			                            //$minimum_fare=round($db_trip[0]['fMinFareDiff'] * $db_trip[0]['fRatioPassenger'],1);
										//$minimum_fare=$db_trip[0]['iBaseFare']+$db_trip[0]['fPricePerKM']+$db_trip[0]['fPricePerMin']+$db_trip[0]['fMinFareDiff'];
										$minimum_fare = $db_trip[0]['fTripGenerateFare'];
			                            ?>

			                           <li><strong><?=$generalobj->trip_currency($minimum_fare,$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b> <?=$langage_lbl['LBL_RIDER_MINIMUM']; ?>
			                              </strong><b>
			                              <?=$generalobj->trip_currency($db_trip[0]['fMinFareDiff'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
			                          

			                          <?php  }
			                          ?>
			                          <?php  
			                          if($db_trip[0]['fTollPrice'] > 0){
										$eTollSkipped = $db_trip[0]['eTollSkipped'];
									} else {
										$eTollSkipped = "Yes";
									}
									 if($db_trip[0]['fTollPrice'] > 0 && $eTollSkipped == "No"){
			                          ?>
									<li><strong><?=$langage_lbl['LBL_TOLL_PRICE_TOTAL']; ?></strong><b><?=$generalobj->trip_currency($db_trip[0]['fTollPrice'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></b></li>
						<?php  } ?>
                  				</ul>
                  				<span>
								<?php  $paymentMode = ($db_trip[0]['vTripPaymentMode'] == 'Cash')? $langage_lbl['LBL_VIA_CASH_TXT']: $langage_lbl['LBL_VIA_CARD_TXT']?>
                  					<h4><?=$langage_lbl['LBL_RIDER_Total_Fare']; ?> (<?=$paymentMode;?>)</h4>
                  					<?php  if($db_trip[0]['fCancellationFare'] > 0){  ?>
                  					<em><?=$generalobj->trip_currency($db_trip[0]['fCancellationFare'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></em>
                  					<?php  } else {?>
                  					<em><?=$generalobj->trip_currency($db_trip[0]['iFare'],$db_trip[0]['fRatio_'.$tripcurname],$tripcurname);?></em>
                  					<?php  } ?>
              					</span>
								<?php  if($db_trip[0]['fTipPrice'] > 0)
								{ ?>
									<ul><li><strong><?=$langage_lbl['LBL_TIP_GIVEN_TXT']; ?></strong><b> <?=$generalobj->trip_currency($db_trip[0]['fTipPrice']);?></b></li></ul>
								<?php } ?>
                  				<div style="clear:both;"></div>

                  				<?php  if($db_trip[0]['eType'] == 'Deliver' && $APP_DELIVERY_MODE != 'Multi'){ ?>
			                          <br>
			                        <h3><?=$langage_lbl['LBL_DELIVERY_DETAILS']; ?></h3><hr/>

			                        <ul style="border-bottom:none">
			                            <li><strong><?=$langage_lbl['LBL_RECEIVER_NAME']; ?> </strong><b><?=$db_trip[0]['vReceiverName'];?></b></li>
			                            <li><strong><?=$langage_lbl['LBL_RECEIVER_MOBILE']; ?> </strong><b><?=$db_trip[0]['vReceiverMobile'];?></b></li>
			                            <li><strong><?=$langage_lbl['LBL_PICK_UP_INS']; ?> </strong><b><?=$db_trip[0]['tPickUpIns'];?></b></li>
			                            <li><strong><?=$langage_lbl['LBL_DELIVERY_INS']; ?> </strong><b><?=$db_trip[0]['tDeliveryIns'];?></b></li>
			                            <li><strong><?=$langage_lbl['LBL_PACKAGE_DETAILS']; ?></strong><b><?=$db_trip[0]['tPackageDetails'];?></b></li>
			                            <li><strong><?=$langage_lbl['LBL_DELIVERY_CONFIRMATION_CODE_TXT']; ?> </strong><b><?=$db_trip[0]['vDeliveryConfirmCode'];?></b></li>       
			                          
			                        </ul>

			                        <?php  } ?>

                       				 <div style="clear:both;"></div>
									<?php  if($APP_TYPE == 'UberX' &&  ($db_trip[0]['vBeforeImage'] != '' || $db_trip[0]['vAfterImage'] != '')){
			                            $img_path = $tconfig["tsite_upload_trip_images"];
			                         ?> 
			                         <h3><?php  echo $langage_lbl_admin['LBL_TRIP_DETAIL_HEADER_TXT'];?></b></h3>                      

			                        <div class="invoice-right-bottom-img">
			                          <?php  if($db_trip[0]['vBeforeImage'] != '') { ?>                     
			                          	<div class="col-sm-6">
			                            <h4> <?php  echo $langage_lbl_admin['LBL_SERVICE_BEFORE_TXT_ADMIN'];?></h4>
			                             <b><a href="<?= $img_path .$db_trip[0]['vBeforeImage'] ?>" target="_blank" ><img src = "<?= $img_path . $db_trip[0]['vBeforeImage'] ?>" style="width:200px;" alt ="Before Images"/></a></b>
			                         	</div>
			                            <?php  } 
			                             if($db_trip[0]['vAfterImage'] != '') {?>
			                          <div class="col-sm-6">
			                            <h4><?php  echo $langage_lbl_admin['LBL_SERVICE_AFTER_TXT_ADMIN'];?></h4>
			                             <b><a href="<?= $img_path .$db_trip[0]['vAfterImage'] ?>" target="_blank" ><img src = "<?= $img_path. $db_trip[0]['vAfterImage'] ?>" style="width:200px;" alt ="After Images"/></a></b>
			                          </div>
			                             <?php  } ?>
			                        </div>
			                        <?php  } ?>
                			</div>
              			</div>
            		</div>
					
					<?php  if($APP_DELIVERY_MODE == "Multi" && $db_trip[0]['eType'] == 'Deliver'){?>
						<div style="clear:both;"></div>
						<div class="invoice-part-bottom invoice-part-bottom1">	
							<?php  	
							$sql1 = "SELECT * FROM trips_delivery_locations AS tdl WHERE iTripId = '".$iTripId."'";
								$db_trips_locations = $obj->MySQLSelect($sql1);
								if($db_trip[0]['eType'] == 'Deliver'){ ?>
								<?php  $i=1 ;if(!empty($db_trips_locations)){
								foreach($db_trips_locations as $dtls) { 
									$class = (!empty($dtls['vSignImage'])) ? 'sign-img' : '';?>
								<div class="col-sm-6 <?php  echo $class;?>"> 
								<h3><?= $langage_lbl['LBL_RECIPIENT_LIST_TXT'].'&nbsp;'. $i; ?></h3>
								<table style="width:100%" class="deliverytable" cellpadding="5" cellspacing="0" border="0">
									<tr>
										<td style="min-width: 150px;"><b><?=$langage_lbl['LBL_RECIPIENT_NAME_HEADER_TXT']; ?></b></td>
										<td><?=$dtls['vReceiverName'];?></td>
									</tr> 
									<tr>
										<td style="min-width: 150px;"><b><?=$langage_lbl['LBL_DROP_OFF_LOCATION_RIDE_DETAIL']; ?></b></td>
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
								<?php  $i++;
								} } ?>
								<?php  } ?>               				 
						</div>
					<?php  } ?>
					
                    </div>
            		<!-- -->
        		 	<?php  //if(SITE_TYPE=="Demo"){?>
            		<!-- <div class="record-feature"> 
            			<span><strong>“Edit / Delete Record Feature”</strong> has been disabled on the Demo Admin Version you are viewing now.
              			This feature will be enabled in the main product we will provide you.</span> 
              		</div> -->
              		<?php  //}?>
        		<!-- -->
          		</div>
				<?php 
				}
				else
				{
				?>
				<div class="trip-detail-page">
                <div class="trip-detail-page-inner">
					<?php  echo $langage_lbl['LBL_NO_INVOICE_FOUND_MSG']; ?>
				</div>
				</div>
				<?php  }?>
        	</div>
  		</div>
    <!-- footer part -->
    <?php  include_once('footer/footer_home.php');?>
    <!-- footer part end -->
    <!-- End:contact page-->
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
						/*origin:'<?= $db_trip[0]['tSaddress']?>',
						destination:'<?= $db_trip[0]['tDaddress']?>',*/
            origin:'<?=$db_trip[0]['tStartLat'].",".$db_trip[0]['tStartLong']?>',
						destination:'<?=$db_trip[0]['tEndLat'].",".$db_trip[0]['tEndLong']?>',
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
