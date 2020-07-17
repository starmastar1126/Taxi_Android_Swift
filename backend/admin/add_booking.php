<?php 
	include_once('../common.php');
	
	if (!isset($generalobjAdmin)) {
		require_once(TPATH_CLASS . "class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	$APP_DELIVERY_MODE = $generalobj->getConfigurations("configurations","APP_DELIVERY_MODE");
	$ENABLE_TOLL_COST = $generalobj->getConfigurations("configurations","ENABLE_TOLL_COST");
	$TOLL_COST_APP_ID = $generalobj->getConfigurations("configurations","TOLL_COST_APP_ID");
	$TOLL_COST_APP_CODE = $generalobj->getConfigurations("configurations","TOLL_COST_APP_CODE");
	$script = "booking";
	
	$tbl_name = 'cab_booking';
	function converToTz($time, $toTz, $fromTz,$dateFormat="Y-m-d H:i:s") {
	    $date = new DateTime($time, new DateTimeZone($fromTz));
	    $date->setTimezone(new DateTimeZone($toTz));
	    $time = $date->format($dateFormat);
	    return $time;
	}
	$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : '';
	$var_msg = isset($_REQUEST['var_msg']) ? $_REQUEST['var_msg'] : '';
	$iCabBookingId = isset($_REQUEST['booking_id']) ? $_REQUEST['booking_id'] : '';
	
	$action = ($iCabBookingId != '') ? 'Edit' : 'Add';
	
	//For Country
	$sql = "SELECT vCountryCode,vCountry from country where eStatus = 'Active'";
	$db_code = $obj->MySQLSelect($sql);
	
	$sql="select cn.vCountryCode,cn.vCountry,cn.vPhoneCode,cn.vTimeZone from country cn inner join 
	configurations c on c.vValue=cn.vCountryCode where c.vName='DEFAULT_COUNTRY_CODE_WEB'";
	$db_con = $obj->MySQLSelect($sql);
	$vPhoneCode = $generalobjAdmin->clearPhone($db_con[0]['vPhoneCode']);
	$vRideCountry = isset($_REQUEST['vRideCountry']) ? $_REQUEST['vRideCountry'] : $db_con[0]['vCountryCode'];
	$vTimeZone = isset($_REQUEST['vTimeZone']) ? $_REQUEST['vTimeZone'] : $db_con[0]['vTimeZone'];
	$vCountry = $db_con[0]['vCountryCode'];

	$address = $db_con[0]['vCountry']; // Google HQ
	$prepAddr = str_replace(' ','+',$address);
	// $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$prepAddr}&key=".$GOOGLE_SEVER_API_KEY_WEB;
 
    // $geocode=file_get_contents($url);
    // $output= json_decode($geocode);
    // $latitude = $output->results[0]->geometry->location->lat;
    // $longitude = $output->results[0]->geometry->location->lng;

	$dBooking_date = "";
	
	$sql1 = "SELECT * FROM `package_type` WHERE eStatus='Active'";
	$db_PackageType = $obj->MySQLSelect($sql1);

	$iUserId = "";
	$iDriverId = "";
	$vDistance = "";
	$vDuration = "";
	$dBookingDate = "";
	$vSourceAddresss = "";
	$tDestAddress = "";
	$iVehicleTypeId = "";
	$vPhone = "";
	$vName = "";
	$vLastName =  "";
	$vEmail = "";
	$vPhoneCode = "";
	$vCountry = "";
	$iPackageTypeId = "";
	$tPackageDetails = "";
	$tDeliveryIns = "";
	$tPickUpIns = "";
	$vReceiverName = "";
	$vReceiverMobile = "";
	$eStatus = "";
	$from_lat_long = "";
	$from_lat = "";
	$from_long = "";
	$to_lat_long = "";
	$to_lat = "";
	$to_long = "";
	$eAutoAssign = "";
	$fPickUpPrice = "";
	$fNightPrice = "";
	$vRideCountry = "";
	$vTimeZone = "";
	$eFemaleDriverRequest = "";
	$eHandiCapAccessibility = "";
	$etype = "";
	$eFlatTrip = "";
	$fFlatTripPrice = "";
	$eTollSkipped = "";
	$fTollPrice = "";
	$vTollPriceCurrencyCode = "";
	
	if ($action == 'Edit') {
		$sql = "SELECT $tbl_name.*,$tbl_name.fNightPrice as NightSurge,$tbl_name.fPickUpPrice as PickSurge,
		register_user.vPhone,register_user.vName,register_user.vLastName,register_user.vEmail,register_user.vPhoneCode,register_user.vCountry FROM " . $tbl_name . " LEFT JOIN register_user on register_user.iUserId=" . $tbl_name . ".iUserId WHERE " . $tbl_name . ".iCabBookingId = '" . $iCabBookingId . "'";
		$db_data = $obj->MySQLSelect($sql);
		
		$vLabel = $id;
		$systemTimeZone = date_default_timezone_get();
		if (count($db_data) > 0) {
			foreach ($db_data as $key => $value) {
				$iUserId = $value['iUserId'];
				$iDriverId = $value['iDriverId'];
				$vDistance = $value['vDistance'];
				$vDuration = $value['vDuration'];
				$dBookingDate = $value['dBooking_date'];
				$vSourceAddresss = $value['vSourceAddresss'];
				$tDestAddress = $value['tDestAddress'];
				$iVehicleTypeId = $value['iVehicleTypeId'];
				$vPhone = $generalobjAdmin->clearPhone($value['vPhone']);
				$vName = $value['vName'];
				$vLastName =  $generalobjAdmin->clearName(" ".$value['vLastName']);
				$vEmail = $generalobjAdmin->clearEmail($value['vEmail']);
				$vPhoneCode = $generalobjAdmin->clearPhone($value['vPhoneCode']);
				$vCountry = $value['vCountry'];
				$iPackageTypeId = $value['iPackageTypeId'];
				$tPackageDetails = $value['tPackageDetails'];
				$tDeliveryIns = $value['tDeliveryIns'];
				$tPickUpIns = $value['tPickUpIns'];
				$vReceiverName = $value['vReceiverName'];
				$vReceiverMobile = $value['vReceiverMobile'];
				$eStatus = $value['eStatus'];
				$from_lat_long = '('.$value['vSourceLatitude'].', '.$value['vSourceLongitude'].')';
				$from_lat = $value['vSourceLatitude'];
				$from_long = $value['vSourceLongitude'];
				$to_lat_long = '('.$value['vDestLatitude'].', '.$value['vDestLongitude'].')';
				$to_lat = $value['vDestLatitude'];
				$to_long = $value['vDestLongitude'];
				$eAutoAssign = $value['eAutoAssign'];
				$fPickUpPrice = $value['PickSurge'];
				$fNightPrice = $value['NightSurge'];
				$vRideCountry = $value['vRideCountry'];
				$vTimeZone = $value['vTimeZone'];
				$eFemaleDriverRequest = $value['eFemaleDriverRequest'];
				$eHandiCapAccessibility = $value['eHandiCapAccessibility'];
				$etype = $value['eType'];
				$eFlatTrip = $value['eFlatTrip'];
				$fFlatTripPrice = $value['fFlatTripPrice'];
				$eTollSkipped = $value['eTollSkipped'];
				$fTollPrice = $value['fTollPrice'];
				$vTollPriceCurrencyCode = $value['vTollPriceCurrencyCode'];
				
				$dBooking_date = converToTz($dBookingDate,$vTimeZone,$systemTimeZone);
				
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title><?=$SITE_NAME;?> | Manual<?php  echo $langage_lbl_admin['LBL_TEXI_ADMIN']; ?>Dispatch</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link rel="stylesheet" href="css/select2/select2.min.css" type="text/css" >
        <?php  include_once('global_files.php');?>
        <script src="//maps.google.com/maps/api/js?sensor=true&key=<?= $GOOGLE_SEVER_API_KEY_WEB ?>&libraries=places" type="text/javascript"></script>
        <script type='text/javascript' src='../assets/map/gmaps.js'></script>
        <script type='text/javascript' src='../assets/js/jquery-ui.min.js'></script>
         <script type='text/javascript' src='../assets/js/bootbox.min.js'></script>
	</head>
    <body class="padTop53">
        <div id="wrap">
            <?php  include_once('header.php'); ?>
            <?php  include_once('left_menu.php'); ?>
            <div id="content">
                <div class="inner" style="min-height: 700px;">
                    <div class="row">
                        <div class="col-lg-8">
                        	<?php  if($APP_TYPE != "UberX"){ ?>
								<h1> Manual <?php  echo $langage_lbl_admin['LBL_TEXI_ADMIN']; ?> Dispatch </h1>
								<?php  } else { ?>
								<h1> <?php  echo $langage_lbl_admin['LBL_MANUAL_TAXI_DISPATCH']; ?> </h1>
							<?php  } ?>
						</div>
						<div class="col-lg-4">
							<?php  if($APP_TYPE != "UberX"){ ?>
								<h1 class="float-right"><a class="btn btn-primary how_it_work_btn" data-toggle="modal" data-target="#myModal"><i class="fa fa-question-circle" style="font-size: 18px;"></i> How it works?</a></h1>
							<?php  } else {?>
								<h1 class="float-right"><a class="btn btn-primary how_it_work_btn" data-toggle="modal" data-target="#myModalufx"><i class="fa fa-question-circle" style="font-size: 18px;"></i> How it works?</a></h1>

              <?php }?>
						</div>
					</div>
                    <hr />
					<form name="add_booking_form" id="add_booking_form" method="post" action="action_booking.php" >
						<div class="form-group" style="display: inline-block;">
							<?php  if ($success == "1") {?>
								<div class="alert alert-success alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">�</button>
									<?php 
										echo ($vassign != "1")?'Booking Has Been Added Successfully.':$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Has Been Assigned Successfully.';
									?>
								</div>
								<br/>
							<?php  } ?>
							<?php  if ($success == 2) { ?>
								<div class="alert alert-danger alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
								"Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you. </div>
								<br/>
							<?php  } ?>
							<?php  if ($success == 0 && $var_msg != "") { ?>
								<div class="alert alert-danger alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
									<?= $var_msg; ?>
								</div>
								<br/>
							<?php  } ?>
							<input type="hidden" name="previousLink" id="previousLink" value=""/>
							<input type="hidden" name="backlink" id="backlink" value="cab_booking.php"/>
                            <input type="hidden" name="distance" id="distance" value="<?= $vDistance; ?>">
                            <input type="hidden" name="duration" id="duration" value="<?= $vDuration; ?>">
                            <input type="hidden" name="from_lat_long" id="from_lat_long" value="<?= $from_lat_long; ?>" >
                            <input type="hidden" name="from_lat" id="from_lat" value="<?= $from_lat; ?>" >
                            <input type="hidden" name="from_long" id="from_long" value="<?= $from_long; ?>" >
                            <input type="hidden" name="to_lat_long" id="to_lat_long" value="<?= $to_lat_long; ?>" >
                            <input type="hidden" name="to_lat" id="to_lat" value="<?= $to_lat; ?>" >
                            <input type="hidden" name="to_long" id="to_long" value="<?= $to_long; ?>" >
                            <input type="hidden" name="fNightPrice" id="fNightPrice" value="<?= $fNightPrice; ?>" >
                            <input type="hidden" name="fPickUpPrice" id="fPickUpPrice" value="<?= $fPickUpPrice; ?>" >
                            <input type="hidden" name="eFlatTrip" id="eFlatTrip" value="<?= $eFlatTrip; ?>" >
                            <input type="hidden" name="fFlatTripPrice" id="fFlatTripPrice" value="<?= $fFlatTripPrice; ?>" >
                            <input type="hidden" value="1" id="location_found" name="location_found">
                            <input type="hidden" value="" id="user_type" name="user_type" >
                            <input type="hidden" value="<?= $iUserId; ?>" id="iUserId" name="iUserId" >
                            <input type="hidden" value="<?= $eStatus; ?>" id="eStatus" name="eStatus" >
                            <input type="hidden" value="<?= $vTimeZone; ?>" id="vTimeZone" name="vTimeZone" >
                            <input type="hidden" value="<?= $vRideCountry; ?>" id="vRideCountry" name="vRideCountry" >
                            <input type="hidden" value="<?= $iCabBookingId; ?>" id="iCabBookingId" name="iCabBookingId" >
                            <input type="hidden" value="<?= $GOOGLE_SEVER_API_KEY_WEB; ?>" id="google_server_key" name="google_server_key" >
                            <input type="hidden" value="" id="getradius" name="getradius" >
                            <input type="hidden" value="KMs" id="eUnit" name="eUnit" >
                            <input type="hidden" name="fTollPrice" id="fTollPrice" value="<?= $fTollPrice?>">
						    <input type="hidden" name="vTollPriceCurrencyCode" id="vTollPriceCurrencyCode" value="<?= $vTollPriceCurrencyCode?>">
						    <input type="hidden" name="eTollSkipped" id="eTollSkipped" value="<?= $eTollSkipped?>">
                            <?php  if($APP_TYPE !='Ride-Delivery' || ($APP_TYPE =='Ride-Delivery' && $APP_DELIVERY_MODE == "Multi")){ ?>
								<input type="hidden" value="<?= $etype?>" id="eType" name="eType" />
							<?php  } ?>
							
                            <div class="add-booking-form-taxi add-booking-form-taxi1 col-lg-12"> <span class="col0">
								<select name="vCountry" id="vCountry" class="form-control form-control-select" onChange="changeCode(this.value,'<?php  echo $iVehicleTypeId; ?>');setDriverListing();" required>
									<!-- <option value="">Select Country</option> -->
									<?php  for($i=0;$i<count($db_code);$i++) { ?>
                                        <option value="<?= $db_code[$i]['vCountryCode'] ?>" 
										<?php  if ($db_code[$i]['vCountryCode'] == $vCountry) { echo "selected"; } ?> >
											<?= $db_code[$i]['vCountry']; ?>
										</option>
									<?php  } ?>
								</select>
							</span> 
							<span class="col6">
								<input type="text" class="form-control add-book-input" name="vPhoneCode" id="vPhoneCode" value="<?= $vPhoneCode; ?>" readonly />
							</span>
							<span class="col2">
								<input type="text" pattern="[0-9]{1,}" title="Enter Mobile Number." class="form-control add-book-input" name="vPhone"  id="vPhone" value="<?= $vPhone; ?>" placeholder="Enter Phone Number" onKeyUp="return isNumberKey(event)"  onblur="return isNumberKey(event)"  required  />
							</span> 
							<span class="col3">
								<input type="text" class="form-control first-name1" name="vName"  id="vName" value="<?= $vName; ?>" placeholder="First Name" required />
								<input type="text" class="form-control last-name1" name="vLastName"  id="vLastName" value="<?= $vLastName; ?>" placeholder="Last Name" required />
							</span> 
							<span class="col4" style="margin: 0px;">
								<input type="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$" class="form-control" name="vEmail" id="vEmail" value="<?= $vEmail; ?>" placeholder="Email" required >
								<div id="emailCheck"></div>
							</span>
                            </div>
						</div>
						<div class="form-group">
							<?php 
								if($APP_TYPE=='Ride-Delivery' && $APP_DELIVERY_MODE != "Multi"){  ?>
								<div class="col-lg-12 add-booking-radiobut">
									<input class="add-booking" id="r1" name="eType" type="radio" value="Ride" <?php  if($etype == 'Ride') { echo 'checked'; } ?> onChange="show_type(this.value),showVehicleCountryVise($('#vCountry option:selected').val(),'<?php  echo $iVehicleTypeId; ?>',this.value);" checked="checked">
								<label for="r1">Ride</label></div>								
								<div class="col-lg-12 add-booking-radiobut">
									<input id="r2" name="eType" type="radio" value="Deliver" <?php  if($etype == 'Deliver') { echo 'checked'; } ?> onChange="show_type(this.value),showVehicleCountryVise($('#vCountry option:selected').val(),'<?php  echo $iVehicleTypeId; ?>',this.value);">
								<label for="r2">Delivery</label></div> 
							<?php  } ?>
						</div>
						<div class="map-main-page-inner">
							<div class="map-main-page-inner-tab">
								<div class="col-lg-12 map-live-hs-mid">
									<?php  if($APP_TYPE=='Ride-Delivery' || $APP_TYPE=='Delivery'){ ?>
										<div id="ride-delivery-type" style="display:none;">
											<label style="margin: 10px">Delivery Options :</label>
											<span>
												<select class="form-control form-control-select form-control14" name="iPackageTypeId"  id="iPackageTypeId">  
													<option value="">Select Package Type</option>
													<?php  foreach($db_PackageType as $val){?>
														<option value="<?=$val['iPackageTypeId']?>" <?php if($val['iPackageTypeId'] == $iPackageTypeId && $action== "Edit"){ ?>selected<?php }?>><?=$val['vName'];?></option>
													<?php  } ?>
												</select>
											</span> 
											<span>
												<input type="text" class="form-control form-control14" name="vReceiverName"  id="vReceiverName" value="<?= $vReceiverName; ?>" placeholder="Recipient's name" />
											</span> 
											<span>
												<input type="text" class="form-control form-control14" pattern="[0-9]{1,}" title="Enter Mobile Number." name="vReceiverMobile"  id="vReceiverMobile" value="<?= $vReceiverMobile; ?>" placeholder="Recipient's mobile" >
											</span> 
											<span> <input type="text" class="form-control form-control14" name="tPickUpIns"  id="tPickUpIns" value="<?= $tPickUpIns; ?>" placeholder="Pick up Ins"></span>
											<span> <input type="text" class="form-control form-control14" name="tDeliveryIns"  id="tDeliveryIns" value="<?= $tDeliveryIns; ?>" placeholder="Delivery Ins"></span>
											<span style="margin-bottom: 0px"> <input type="text" class="form-control form-control14" name="tPackageDetails"  id="tPackageDetails" value="<?= $tPackageDetails; ?>" placeholder="Package details"></span> 
										</div>
									<?php  }  ?>
								</div>
								<div class="col-lg-12 map-live-hs-mid">
									<span class="col5">
										<input type="text" class="ride-location1 highalert txt_active form-control first-name1" name="vSourceAddresss"  id="from" value="<?= $vSourceAddresss; ?>" placeholder="<?= ucfirst(strtolower($langage_lbl_admin['LBL_PICKUP_LOCATION_HEADER_TXT'])); ?>" required onpaste="checkrestrictionfrom('from');">
										
										<?php  if($APP_TYPE != "UberX"){ ?>
											<input type="text" class="ride-location1 highalert txt_active form-control last-name1" name="tDestAddress"  id="to" value="<?= $tDestAddress; ?>" placeholder="Drop Off Location" required onpaste="checkrestrictionto('to');">
										<?php  } ?>
									</span>
									<span>
										<input type="text" class="form-control form-control14" name="dBooking_date"  id="datetimepicker4" value="<?= $dBooking_date; ?>" placeholder="Select Date / Time" onBlur="getFarevalues('');<?php  if($APP_TYPE == "UberX") {?>setDriverListing();<?php  } ?>" required>
									</span>
									<span>
										<select class="form-control form-control-select form-control14" name='iVehicleTypeId' id="iVehicleTypeId" required onChange="showAsVehicleType(this.value)">
											<option value="" >Select <?php  echo $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT']; ?></option>
											<?php  /*
												$sql1 = "SELECT iVehicleTypeId, vVehicleType FROM `vehicle_type` WHERE 1";
												$db_carType = $obj->MySQLSelect($sql1);
												foreach ($db_carType as $db_car) {
												?>
												<option value="<?php  echo $db_car['iVehicleTypeId']; ?>" <?php  if ($iVehicleTypeId == $db_car['iVehicleTypeId']) {
												echo "selected";
												} ?> ><?php  echo $db_car['vVehicleType']; ?></option>
											<?php  } */?>
										</select>
									</span>
									<?php  if($APP_TYPE != 'UberX'){ ?>
										<?php  if($APP_TYPE == 'Ride' || $APP_TYPE == 'Ride-Delivery'){ ?>
										<div id="ride-type" style="display:block;">
											<span class="auto_assign001">
												<input type="checkbox" name="eFemaleDriverRequest" id="eFemaleDriverRequest" value="Yes" <?php  if ($eFemaleDriverRequest == 'Yes') echo 'checked'; ?>>
												<p>Ladies Only Ride?</p>
											</span>
											
											<span class="auto_assign001">
												<input type="checkbox" name="eHandiCapAccessibility" id="eHandiCapAccessibility" value="Yes" <?php  if ($eHandiCapAccessibility == 'Yes') echo 'checked'; ?>>
												<p>Prefer Handicap Accessibility?</p>
											</span>
										</div>
										<?php  } ?>
										<span class="auto_assign001">
											<input type="checkbox" name="eAutoAssign" id="eAutoAssign" value="Yes" <?php  if ($eAutoAssign == 'Yes') echo 'checked'; ?>>
											<p>Auto Assign <?= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></p>
										</span>
										<span class="auto_assignOr">
											<h3>OR</h3>
										</span>
									<?php  } ?>
									<span id="showdriverSet001" style="display:none;"><p class="margin-right5">Assigned <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT']; ?>: </p><p id="driverSet001"></p></span>
								</div>
								<span class="add-booking1">
									<input name="" type="text" placeholder="Type <?= $langage_lbl_admin['LBL_DRIVER_PROVIDER']; ?> name to search from below list" id="name_keyWord" onKeyUp="get_drivers_list(this.value)">
								</span>
								<ul id="driver_main_list" style="">
									<div class="" id="imageIcons" style="width:100%;">
										<div align="center">                                                                       
											<img src="default.gif">                                                              
											<span>Retrieving <?php  echo $langage_lbl_admin['LBL_DIVER']; ?> list.Please Wait...</span>                       
										</div>                                                                                 
									</div>
								</ul>
								<input type="text" name="iDriverId" id="iDriverId" value="" required   class="form-control height-1" >
							</div>
							<div class="map-page">
								<div class="panel-heading location-map" style="background:none;">
									<div class="google-map-wrap">
										<div class="map-color-code">
											<div>
												<label style="width: 20%;"><?php  echo $langage_lbl['LBL_PROVIDER_DRIVER_AVAILABILITY']; ?> </label>
												<span class="select-map-availability"><select onChange="setNewDriverLocations(this.value)" id="newSelect02">
													<option value='' data-id=""><?php  echo $langage_lbl['LBL_ALL']; ?></option>
													<option value="Available" data-id="img/green-icon.png"><?= $langage_lbl['LBL_AVAILABLE']; ?></option>
													<option value="Active" data-id="img/red.png"><?php  echo $langage_lbl['LBL_ENROUTE_TO']; ?></option>
													<option value="Arrived" data-id="img/blue.png"><?php  echo $langage_lbl['LBL_REACHED_PICKUP']; ?></option>
													<option value="On Going Trip" data-id="img/yellow.png"><?php  echo $langage_lbl['LBL_JOURNEY_STARTED']; ?></option>
													<option value="Not Available" data-id="img/offline-icon.png"><?= $langage_lbl['LBL_OFFLINE']; ?></option>
												</select></span>
											</div>
											<div style="margin-top: 15px;">
												<label style="width: 20%;">Map Zoom Level</label>
												<span>
													<?php  $radius_driver = array(5, 10, 20, 30); ?>
													<select class="form-control form-control-select form-control14" name='radius-id' id="radius-id" onChange="play(this.value)" style="width: 40%;display: inline-block;">
														<option value=""> Select Radius </option>
														<?php  foreach ($radius_driver as $value) { ?>
															<option value="<?php  echo $value ?>"><?php  echo $value . $DEFAULT_DISTANCE_UNIT .' Radius'; ?></option>
														<?php  } ?>
													</select>
												</span>
											</div>
										</div>
										<div id="map-canvas" class="google-map" style="width:100%; height:500px;"></div>
									</div>
								</div>
							</div>
							
							<?php  if($APP_TYPE != 'UberX'){ ?>
								<div class="total-price total-price1" style="float:left;"> <b>Fare Estimation</b>
									<hr>
									<ul>
										<li id="MinFare">
											<b>Minimum Fare</b> :
											<?php  echo $generalobj->symbol_currency(); ?>
											<em id="minimum_fare_price">0</em>
										</li>
										<li id="BaseFare">
											<b>Base Fare</b> :
											<?php  echo $generalobj->symbol_currency(); ?>
											<em id="base_fare_price">0</em>
										</li>
										<li id="FixFare" style="display:none">
											<b>Fix Fare</b> :
											<?php  echo $generalobj->symbol_currency(); ?>
											<em id="fix_fare_price">0</em>
										</li>
										<li id="DistanceFare">
											<b>Distance (<em id="dist_fare">0</em> <em id="change_eUnit"><?php  echo $DEFAULT_DISTANCE_UNIT;?></em>)</b> :
											<?php  echo $generalobj->symbol_currency(); ?>
											<em id="dist_fare_price">0</em>
										</li>
										<li id="TimeFare">
											<b>Time (<em id="time_fare">0</em> Minutes)</b> :
											<?php  echo $generalobj->symbol_currency(); ?>
											<em id="time_fare_price">0</em>
										</li>
										<li id="fare_normal" style="display:none">
											<b>Normal Fare</b> : <?php  echo $generalobj->symbol_currency(); ?> 
											<em id="normal_fare_price">0</em>
										</li>
										
										<li id="fare_surge" style="display:none">
											<b> Surcharge Difference (<em id="fare_surge_price">0</em> X)</b>
											: <?php  echo $generalobj->symbol_currency(); ?> <em id="surge_fare_diff">0</em>
										</li>
										<li id="toll_price" style="display:none">
											<b> Toll Cost</b>
											: <?php  echo $generalobj->symbol_currency(); ?> <em id="toll_price_val"><?= $fTollPrice?></em>
										</li>
									</ul>
									<span>Total Fare<b>
										<?php  echo $generalobj->symbol_currency(); ?>
									<em id="total_fare_price">0</em></b></span> </div>
							<?php  } ?>
							
							<!-- popup -->
							<div class="map-popup" style="display:none" id="driver_popup"></div>
							<!-- popup end -->
						</div>
						<input type="hidden" name="newType" id="newType" value="">
						<input type="hidden" name="submitbtn" id="submitbtn">
						<div style="clear:both;"></div>
						<div class="book-now-reset add-booking-button"><span>
							<input type="submit" class="save btn-info button-submit" name="submitbutton" id="submitbutton" value="Book Now">
							<input type="reset" class="save btn-info button-submit" name="reset" id="reset12" value="Reset" >
						</span></div>
					</form>
                    
					<div class="admin-notes">
						<h4>Notes:</h4>
						<ul>
							<li>
								Administrator can Add / Edit <?php  echo $langage_lbl['LBL_RIDER_RIDE_MAIN_SCREEN']; ?> later booking on this page.
							</li>
							<li>
								Drivers current availability is not connected with booking being made. Please confirm future avaialbility of driver before doing booking.
							</li>
							<li>Adding booking from here will not send request to driver immediately.</li> 
							<li>In case of "Auto Assign Driver" option selected, driver(s) get automatic request before 8-12 minutes of actual booking time.</li>
							<li>In case of "Auto Assign Driver" option not selected, driver(s) get booking confirmation sms as well as reminder sms before 30 minutes of actual booking. Driver has to start the scheduled trip by going to "Your Trip" >> Upcoming section from Driver App.</li>
							<li>In case of "Auto Assign Driver", the competitive algorithm will be followed instead of one you have selected in settings.</li>
						</ul>
					</div>
					
				</div>
                <!--END PAGE CONTENT -->
				
			</div>
            <?php  include_once('footer.php'); ?>
            <div style="clear:both;"></div>
			
			<!--Wallet Low Balance-->
			<div class="modal fade" id="usermodel" tabindex="-1" role="dialog" aria-labelledby="usermodel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<input type="hidden" name="iDriverId_temp" id="iDriverId_temp">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
							<h4 class="modal-title" id="inactiveModalLabel">Low Wallet Balance </h4>
						</div>
						<div class="modal-body">
							<p><span style="font-size: 15px;"> This driver is having low balance in his wallet and is not able to accept cash ride. Would you still like to assign this driver?</span></p>
							<p><b style="font-size: 15px;"> Minimum Required Balance : </b><span style="font-size: 15px;"><?php  echo $generalobj->symbol_currency()." ".number_format($WALLET_MIN_BALANCE,2); ?></span></p>
							<p><b style="font-size: 15px;"> Available Balance : </b><span style="font-size: 15px;"><?php  echo $generalobj->symbol_currency(); ?> <span id="usr-bal"></span></span></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">Not Now</button> 
							<button type="button" class="btn btn-success btn-ok action_modal_submit" data-dismiss="modal" onClick="AssignDriver();">OK</button>
						</div>
					</div>
				</div>
			</div>
			<!--end Wallet Low Balance-->
			<!--user inactive/deleted-->
			<div class="modal fade" id="inactiveModal" tabindex="-1" role="dialog" aria-labelledby="inactiveModalLabel">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
							<h4 class="modal-title" id="inactiveModalLabel">User Detail</h4>
						</div>
						<div class="modal-body">
							<span style="font-size: 15px;"> User is inactive/deleted. Do you want to book a ride with user?</span>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-success" data-dismiss="modal">Continue</button>
							<!-- <button type="button" class="btn btn-primary">Continue</button> -->
						</div>
					</div>
				</div>
			</div>
			<!--end user inactive/deleted-->
			<!--surcharge confirmation-->
			<div class="modal fade" id="surgemodel" tabindex="-1" role="dialog" aria-labelledby="surgemodel" aria-hidden="true">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
							<h4 class="modal-title" id="inactiveModalLabel">Confirm Surcharge</h4>
						</div>
						<div class="modal-body">
							<p><span style="font-size: 15px;"> This trip is comes under the surcharge timing.surcharge will be applied as per below.</span></p>
							<table style="font-size: 15px;" cellspacing="5" cellpadding="5">
								<tr>
									<td width="100px"> <b>Surge Type </b></td>
									<td> : <span id="surge_type"></span> Surcharge</td>
								</tr>
								<tr>
									<td><b>Surge Factor</b></td>
									<td> : <span id="surge_factor"></span> X</td>
								</tr>
								<tr>
									<td><b>Surge Timing</b></td>
									<td> : <span id="surge_timing"></span></td>
								</tr>
							</table>
						</div>
						<div class="modal-footer">
							<!-- <button type="button" class="btn btn-default" data-dismiss="modal">Not Now</button> -->
							<button type="button" class="btn btn-success btn-ok action_modal_submit" data-dismiss="modal" onClick="">OK</button>
						</div>
					</div>
				</div>
			</div>
			<!--end surcharge confirmation-->
			
			
			
			<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
			<script type="text/javascript" src="js/moment.min.js"></script>
			<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
			<script type="text/javascript" src="js/plugins/select2.min.js"></script>
			<script>
				var eType = "";
				var APP_DELIVERY_MODE = '<?=$APP_DELIVERY_MODE?>';
				var ENABLE_TOLL_COST = "<?= $ENABLE_TOLL_COST?>";
				switch ("<?php  echo $APP_TYPE;?>") {
					case "Ride-Delivery":
					if(APP_DELIVERY_MODE == "Multi"){
						eType = 'Ride';
					}else{
						eType =  $('input[name=eType]:checked').val();
					}
					break;

					case "Delivery":
					eType =  'Deliver';
					break;

				    case "UberX":
					eType =  'UberX';
					break;

				    default:
					eType = 'Ride';
				}

				
				function show_type(etype) {
					//alert(etype);
					if(etype == 'Ride'){
						$('#ride-delivery-type').hide();
						$('#ride-type').show();
						$('#iPackageTypeId').removeAttr('required');
						$('#vReceiverMobile').removeAttr('required');
						$('#vReceiverName').removeAttr('required');
						}else if(etype == 'Deliver'){
						$('#ride-delivery-type').show();
						$('#ride-type').hide();
						$('#iPackageTypeId').attr('required','required');
						$('#vReceiverMobile').attr('required','required');
						$('#vReceiverName').attr('required','required');
					}
				}
				
				// $("#surgemodel").modal('show');
				function formatData(state) {
					if (!state.id) { return state.text; }
					var optimage = $(state.element).data('id'); 
					if(!optimage){
						return state.text;
						} else {
						var $state = $(
						'<span class="userName"><img src="' + optimage + '" class="mpLocPic" /> ' + $(state.element).text() + '</span>'
						);
						return $state;
					}
				}
				
				$("#newSelect02").select2({
					templateResult: formatData,
					templateSelection: formatData
				});
				var eFlatTrip = 'No';
				var eTypeQ11 = 'yes';
				var map;
				var geocoder;
				var circle;
				var markers = [];
				var driverMarkers = [];
				var bounds = [];
				var newLocations = "";
				var autocomplete_from;
				var autocomplete_to;
				var eLadiesRide = 'No';
				var eHandicaps = 'No';
				var geocoder = new google.maps.Geocoder();
				var directionsService = new google.maps.DirectionsService(); // For Route Services on map
				var directionsOptions = {  // For Polyline Route line options on map
					polylineOptions: {
						strokeColor: '#FF7E00',
						strokeWeight: 5
					}
				};
				var directionsDisplay = new google.maps.DirectionsRenderer(directionsOptions);
				var showsurgemodal = "Yes";
				
				function setDriverListing(iVehicleTypeId) {
					dBooking_date =  $("#datetimepicker4").val();
					vCountry = $("#vCountry").val();
					keyword = $("#name_keyWord").val();
					if($("#eFemaleDriverRequest").is(":checked")){
						eLadiesRide = 'Yes';
					} else {
						eLadiesRide = 'No';
					}
					
					if($("#eHandiCapAccessibility").is(":checked")){
						eHandicaps = 'Yes';
					} else {
						eHandicaps = 'No';
					}
					
					
					$.ajax({
						type: "POST",
						url: "get_available_driver_list.php",
						dataType: "html",
						data: {vCountry: vCountry,type: '',iVehicleTypeId: iVehicleTypeId,keyword: keyword,eLadiesRide: eLadiesRide,eHandicaps: eHandicaps,dBooking_date:dBooking_date},
						success: function (dataHtml2) {
							if(dataHtml2 != ""){
								$('#driver_main_list').show();
								$('#driver_main_list').html(dataHtml2);
								if($("#eAutoAssign").is(':checked')){
									$(".assign-driverbtn").attr('disabled','disabled');
								}
								}else{
								$('#driver_main_list').html('<h4 style="margin:25px 0 0 15px">Sorry , No <?php  echo $langage_lbl_admin['LBL_DIVER']; ?> Found.</h4>');
								$('#driver_main_list').show();
							}
							}, error: function (dataHtml2) {
							
						}
					});
				}
				
				function AssignDriver(driverId=""){
					if(driverId == ""){
						driverId = $('#iDriverId_temp').val();
					}
					$('#iDriverId').val(driverId);
					$("#showdriverSet001").show();
					$("#driverSet001").html($('.driver_'+driverId).html());
				}
				
				function checkUserBalance(driverId){
					$.ajax({
						type: "POST",
						url: "ajax_get_user_balance.php",
						data: "driverId="+driverId+"&type=Driver",
						success: function (data) {
							data1 = data.split("|");
							var CDE = '<?=$COMMISION_DEDUCT_ENABLE?>';
							var Min_Bal = '<?=$WALLET_MIN_BALANCE?>';
							// alert(CDE);
							
							if(CDE == "Yes"){
								if(parseFloat(data1[1]) < parseFloat(Min_Bal)){
									var amt = parseFloat(data1[1]).toFixed(2);
									$("#usr-bal").text(amt);
									$("#iDriverId_temp").val(driverId);
									$("#usermodel").modal('show');
									return false;
									}else{
									AssignDriver(driverId);
									return false;
								}
								}else{
								AssignDriver(driverId);
								return false;
							}
							}, error: function (dataHtml2) {
							
						}
					});
				}
				
				function setDriversMarkers(flag) {
					newType = $("#newType").val();
					vType = $("#iVehicleTypeId").val();
					
					if($("#eFemaleDriverRequest").is(":checked")){
						eLadiesRide = 'Yes';
						}else {
						eLadiesRide = 'No';
					}
					
					if($("#eHandiCapAccessibility").is(":checked")){
						eHandicaps = 'Yes';
						}else {
						eHandicaps = 'No';
					}
					
                    $.ajax({
                        type: "POST",
                        url: "get_map_drivers_list.php",
                        dataType: "json",
                        data: {type: newType,iVehicleTypeId: vType,eLadiesRide: eLadiesRide,eHandicaps: eHandicaps},
                        success: function (dataHtml) {
                            for (var i = 0; i < driverMarkers.length; i++) {
                                driverMarkers[i].setMap(null);
							}
                            newLocations = dataHtml.locations;
							var infowindow = new google.maps.InfoWindow();
                            for (var i = 0; i < newLocations.length; i++) {
                                if (newType == newLocations[i].location_type || newType == "") {
									var str33 = newLocations[i].location_carType;
									if(vType == "" || (str33 != null && str33.indexOf(vType) != -1)){
										newName = newLocations[i].location_name;
										newOnlineSt = newLocations[i].location_online_status;
										newLat = newLocations[i].google_map.lat;
										newLong = newLocations[i].google_map.lng;
										newDriverImg = newLocations[i].location_image;
										newMobile = newLocations[i].location_mobile;
										newDriverID = newLocations[i].location_ID;
										newImg = newLocations[i].location_icon;
										driverId = newLocations[i].location_driverId;
										latlng = new google.maps.LatLng(newLat, newLong);
										content = '<table><tr><td rowspan="4"><img src="' + newDriverImg + '" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + newDriverID + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+' + newMobile + '</b></td></tr></table>';
										
										var drivermarker = new google.maps.Marker({
											map: map,
											position: latlng,
											icon: newImg
										});
										google.maps.event.addListener(drivermarker,'click', (function(drivermarker,content,infowindow){ 
											return function() {
												infowindow.setContent(content);
												infowindow.open(map,drivermarker);
											};
										})(drivermarker,content,infowindow));
										driverMarkers.push(drivermarker);
									}
								}
							}
							//var markers = [];//some array
							if(flag != 'test') {
								var bounds = new google.maps.LatLngBounds();
								for (var i = 0; i < driverMarkers.length; i++) {
									bounds.extend(driverMarkers[i].getPosition());
								}
								//console.log(bounds);
								map.fitBounds(bounds);
								map.setZoom(13);
							}
                            setDriverListing(vType);
						},
                        error: function (dataHtml) {
							
						}
					});
				}
				
				function initialize() {
					var thePoint = new google.maps.LatLng('20.1849963', '64.4125062');
					var mapOptions = {
						zoom: 4,
						center: thePoint
					};
					map = new google.maps.Map(document.getElementById('map-canvas'),
					mapOptions);
					
					circle = new google.maps.Circle({radius: 25, center: thePoint}); 
					// map.fitBounds(circle.getBounds()); 
					if(eType == "Deliver") {
						show_type(eType);
					}
					showVehicleCountryVise('<?php  echo $vCountry ?>','<?php  echo $iVehicleTypeId; ?>',eType);
					<?php  if($action == "Edit") { ?>
						callEditFundtion();
						// show_locations();
						// showVehicleCountryVise('<?php  echo $vCountry ?>','<?php  echo $iVehicleTypeId; ?>');
					<?php  } ?>
					//setDriversMarkers('test');
					//alert('test');
				}
				
				$(document).ready(function () {
					google.maps.event.addDomListener(window, 'load', initialize);
					setDriversMarkers('test');
					$("#eType").val(eType);
				    $('input[type=radio][name=eType]').change(function() {
				        eType = $('input[name=eType]:checked').val();
					});
				});
				
				function play(radius){
					// return Math.round(14-Math.log(radius)/Math.LN2);
					var pt = new google.maps.LatLng($("#from_lat").val(),$("#from_long").val());
					map.setCenter(pt);
					var newRadius = Math.round(24-Math.log(radius)/Math.LN2);
					newRadius = newRadius-9;
					map.setZoom(newRadius);
				}
				function getAddress(mDlatitude,mDlongitude,addId) {
					var mylatlang = new google.maps.LatLng(mDlatitude,mDlongitude);
					geocoder.geocode( {'latLng': mylatlang},
					function(results, status) {
						// console.log(results);
						if(status == google.maps.GeocoderStatus.OK) {
							if(results[0]) {
								// document.getElementById(addId).value = results[0].formatted_address;
								$('#'+addId).val(results[0].formatted_address);
							}
							else {
								document.getElementById('#'+addId).value = "No results";
							}
						}
						else {
							document.getElementById('#'+addId).value = status;
						}
					});
				}
				
				function DeleteMarkers(newId) {
					// Loop through all the markers and remove
					for (var i = 0; i < markers.length; i++) {
						if(newId != '') {
							if(markers[i].id == newId) {
								markers[i].setMap(null);
							}
							}else {
							markers[i].setMap(null);
						}
					}
					if(newId == '') { markers = []; }
				};
				
				function setMarker(postitions,valIcon) {
					var newIcon;
					if(valIcon == 'from_loc') {
						newIcon = '../webimages/upload/mapmarker/PinFrom.png';
						}else if(valIcon == 'to_loc') {
						newIcon = '../webimages/upload/mapmarker/PinTo.png';
						}else {
						newIcon = '../webimages/upload/mapmarker/PinTo.png';
					}
					var marker = new google.maps.Marker({
						map: map,
						draggable: true,
						animation: google.maps.Animation.DROP,
						position: postitions,
						icon: newIcon
					});
					marker.id = valIcon;
					markers.push(marker);
					map.setCenter(marker.getPosition());
					map.setZoom(15);
					
					if(valIcon == "from_loc"){
						marker.addListener('dragend',function(event){
							// console.log(event);
							var lat = event.latLng.lat(); 
							var lng = event.latLng.lng(); 
							var myLatlongs = new google.maps.LatLng(lat,lng);
							showsurgemodal = "No";
							
							$("#from_lat").val(lat);
							$("#from_long").val(lng);
							$("#from_lat_long").val(myLatlongs);
							getAddress(lat,lng,'from');
							routeDirections();
						});
					}
					if(valIcon == 'to_loc') {
						marker.addListener('dragend', function(event) {	
							var lat = event.latLng.lat(); 
							var lng = event.latLng.lng(); 
							var myLatlongs1 = new google.maps.LatLng(lat,lng);
							showsurgemodal = "No";
							
							$("#to_lat").val(lat);
							$("#to_long").val(lng);
							$("#to_lat_long").val(myLatlongs1);
							getAddress(lat,lng,'to');
							routeDirections();
						});
					}
					routeDirections();
				}
				
				function routeDirections() {
					directionsDisplay.setMap(null); // Remove Previous Route.

					if(($("#from").val() != "" && $("#from_lat_long").val() != "") && ($("#to").val() != "" && $("#to_lat_long").val() != "")) {
						var newFrom = $("#from_lat").val()+", "+$("#from_long").val();
						var newTo = $("#to_lat").val()+", "+$("#to_long").val();
						
						//Make an object for setting route
						var request = {
							origin: newFrom, // From locations latlongs
							destination: newTo, // To locations latlongs
							travelMode: google.maps.TravelMode.DRIVING // Set the Path of Driving
						};
						
						//Draw route from the object
						directionsService.route(request, function(response, status){
							if(status == google.maps.DirectionsStatus.OK) {
								// Check for allowed and disallowed.
								var response1 = JSON.stringify(response);
								/*$.ajax({
									type: "POST",
									url: 'checkForRestriction.php',
									dataType: 'html',
									data: {fromLat: $("#from_lat").val(),fromLong: $("#from_long").val(),toLat: $("#to_lat").val(),toLong: $("#to_long").val(),type:'both'},
									success: function(dataHtml5)
									{
										if(dataHtml5 != ''){
											alert(dataHtml5);
										}
									},
									error: function(dataHtml5)
									{
									}
								});*/

								// console.log(response);
								directionsDisplay.setMap(map);
								directionsDisplay.setOptions( { suppressMarkers: true } ); //, preserveViewport: true, suppressMarkers: false for setting auto markers from google api
								directionsDisplay.setDirections(response); // Set route
								var route = response.routes[0];
								for (var i = 0; i < route.legs.length; i++) {
									$("#distance").val(route.legs[i].distance.value);
									$("#duration").val(route.legs[i].duration.value);
								}
								
								var dist_fare = parseFloat($("#distance").val(), 10) / parseFloat(1000, 10);
								// alert(dist_fare);
								if($("#eUnit").val() != 'KMs') {
									dist_fare = dist_fare * 0.621371;
								}
								// alert(dist_fare);
								$('#dist_fare').text(dist_fare.toFixed(2));
								var time_fare = parseFloat($("#duration").val(), 10) / parseFloat(60, 10);
								$('#time_fare').text(time_fare.toFixed(2));
								var vehicleId = $('#iVehicleTypeId').val();
								var booking_date = $('#datetimepicker4').val();
								var vCountry = $('#vCountry').val();
								var tollcostval = $('#fTollPrice').val();
								
								$.ajax({
									type: "POST",
									url: 'ajax_estimate_by_vehicle_type.php',
									dataType: 'json',
									data: {'vehicleId' : vehicleId,'booking_date':booking_date,'vCountry':vCountry,'FromLatLong':newFrom,'ToLatLong':newTo},
									success: function (dataHtml)
									{
										if (dataHtml != "") {
											// var result = dataHtml.split(':');
											var iBaseFare = parseFloat(dataHtml.iBaseFare).toFixed(2);
											var fPricePerKM = parseFloat(dataHtml.fPricePerKM).toFixed(2);
											var fPricePerMin = parseFloat(dataHtml.fPricePerMin).toFixed(2);
											var iMinFare = parseFloat(dataHtml.iMinFare).toFixed(2);
											var fPickUpPrice = parseFloat(dataHtml.fPickUpPrice).toFixed(2);
											var fNightPrice = parseFloat(dataHtml.fNightPrice).toFixed(2);
											var fSurgePrice = parseFloat(dataHtml.fSurgePrice).toFixed(2);
											var SurgeType = dataHtml.SurgeType;
											var Time = dataHtml.Time;
											eFlatTrip = dataHtml.eFlatTrip;
											var fFlatTripPrice = dataHtml.fFlatTripPrice;

											if(eFlatTrip == 'Yes'){
												fFlatTripPrice = parseFloat(fFlatTripPrice).toFixed(2);
												$('#fix_fare_price').text(fFlatTripPrice);
												fPricePerMin = 0;
												fPricePerKM = 0;
												iMinFare = 0;
												$('#eFlatTrip').val(eFlatTrip);
												$('#fFlatTripPrice').val(fFlatTripPrice);
												$("#FixFare").show();
												$("#BaseFare").hide();
												$("#MinFare").hide();
												$("#DistanceFare").hide();
												$("#TimeFare").hide();
												$("#toll_price").hide();
											} else {
												$('#eFlatTrip').val(eFlatTrip);
												$("#FixFare").hide();
												$("#BaseFare").show();
												$("#MinFare").show();
												$("#DistanceFare").show();
												$("#TimeFare").show();
											}
											var increased = parseInt($('#fTollPrice').val());
											if(isNaN(increased) || increased <= 0){
												$("#vTollPriceCurrencyCode").val('');
										    	$("#fTollPrice").val('0');
										    	$("#eTollSkipped").val('No');
									    	}

											$('#minimum_fare_price').text(iMinFare);
											$('#base_fare_price').text(iBaseFare);
											$('#dist_fare_price').text(parseFloat(fPricePerKM*$('#dist_fare').text()).toFixed(2));
											/* var eunit = $("#eUnit").val();
												if(eunit == "Miles"){
												$('#dist_fare_price').text(parseFloat((fPricePerKM*($('#dist_fare').text()*1.6))).toFixed(2));
											} */
											$('#time_fare_price').text(parseFloat(fPricePerMin*$('#time_fare').text()).toFixed(2));

/*											if($('#eTollSkipped').val() == 'No' && eFlatTrip != 'Yes'  && eType != 'UberX'){
												$("#toll_price").show();
											} else {
												$("#toll_price").hide();
											}*/
											if(ENABLE_TOLL_COST == 'Yes') {
												if($('#fTollPrice').val() > 0 && $('#eTollSkipped').val() == 'No' && eFlatTrip != 'Yes' && eType != 'UberX'){
													$("#toll_price").show();
													$('#toll_price_val').text(tollcostval);
												} else {
													$("#toll_price").hide();
												}
											} 

											if(eFlatTrip == 'Yes'){
												var totalPrice = (parseFloat($('#fix_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat($('#time_fare_price').text())).toFixed(2);
											} else {
												if(ENABLE_TOLL_COST == 'Yes'){
													if($('#fTollPrice').val() > 0 && $('#eTollSkipped').val() == 'No' && eFlatTrip != 'Yes' && eType != 'UberX'){
															var totalPrice = (parseFloat($('#base_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat(tollcostval)+parseFloat($('#time_fare_price').text())).toFixed(2);
													} else {
												var totalPrice = (parseFloat($('#base_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat($('#time_fare_price').text())).toFixed(2);
													}
												} else {
													var totalPrice = (parseFloat($('#base_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat($('#time_fare_price').text())).toFixed(2);
												}
											}
											
											if(parseFloat(totalPrice) >= parseFloat($('#minimum_fare_price').text())) {
												$('#total_fare_price').text(totalPrice);
												if($('#fTollPrice').val() > 0 && $('#eTollSkipped').val() == 'No' && eFlatTrip != 'Yes' && eType != 'UberX'){
													$('#totalcost').text(totalPrice-tollcostval);
												} else {
													$('#totalcost').text(totalPrice);
												}
												$("#MinFare").hide();
											} else {
												$('#total_fare_price').text($('#minimum_fare_price').text());
												$('#totalcost').text($('#minimum_fare_price').text());
												$("#MinFare").show();
											}
											if(fSurgePrice > 1){
												if($('#fTollPrice').val() > 0 && $('#eTollSkipped').val() == 'No' && eFlatTrip != 'Yes' && eType != 'UberX'){
													var normalfare = parseFloat($('#total_fare_price').text() - tollcostval).toFixed(2);
												} else {
												var normalfare = $('#total_fare_price').text();
												}
												$('#normal_fare_price').text(normalfare);
												if($('#fTollPrice').val() > 0 && $('#eTollSkipped').val() == 'No' && eFlatTrip != 'Yes' && eType != 'UberX'){
													var totalfare = ($('#total_fare_price').text()-tollcostval);
													var surgefare = parseFloat(totalfare*fSurgePrice).toFixed(2);
													var surgefarenew = (parseFloat(tollcostval)+parseFloat(surgefare)).toFixed(2);
													$('#total_fare_price').text(surgefarenew);
												} else {
												var surgefare = parseFloat($('#total_fare_price').text()*fSurgePrice).toFixed(2);
													$('#total_fare_price').text(surgefare);
												}
												$('#totalcost').text(surgefare);
												var difference = parseFloat(surgefare-normalfare).toFixed(2);
												// console.log(normalfare+" "+surgefare+" "+difference);
												if(SurgeType == "Night"){
													$("#fNightPrice").val(fSurgePrice);
													$("#fPickUpPrice").val(1);
													}else{
													$("#fNightPrice").val(1);
													$("#fPickUpPrice").val(fSurgePrice);
													
												}
												$("#surge_fare_diff").text(difference);
												$("#fare_surge_price").text(fSurgePrice);
												$("#fare_surge").show();
												
												if(showsurgemodal == "Yes"){
													$("#surge_factor").text(fSurgePrice);
													$("#surge_type").text(SurgeType);
													$("#surge_timing").text(Time);
													$("#surgemodel").modal('show');
												}

												if(eFlatTrip == 'Yes'){
													$("#fare_normal").hide();
												} else {
													$("#fare_normal").show();
												}
												//$("#fare_normal").show();
												
											} else {
												$("#fare_surge").hide();
												$("#fare_normal").hide();
												$("#fNightPrice").val(1);
												$("#fPickUpPrice").val(1);
											}
											showsurgemodal = "Yes";
										}else {
											$('#minimum_fare_price').text('0');
											$('#base_fare_price').text('0');
											$('#dist_fare_price').text('0');
											$('#time_fare_price').text('0');
											$('#total_fare_price').text('0');
										}
									}
								});
							} else { 
								alert("Directions request failed: " + status); 
							}
						});
						
						<?php  if($iVehicleTypeId != ""){?>
							var iVehicleTypeId = '<?=$iVehicleTypeId?>';
							getFarevalues(iVehicleTypeId);
							showAsVehicleType(iVehicleTypeId);
						<?php  } ?>
						
					}
				}
				
				function show_locations(){
					if($("#from").val() != "" && $("#to").val() == ''){
						DeleteMarkers('from_loc');
						var latlng = new google.maps.LatLng($("#from_lat").val(),$("#from_long").val());
						setMarker(latlng,'from_loc');
					}
					if($("#to").val() != "" && $("#from").val() == ''){
						DeleteMarkers('to_loc');
						var latlng_to = new google.maps.LatLng($("#to_lat").val(),$("#to_long").val());
						setMarker(latlng_to,'to_loc');
					}
					if ($("#from").val() != '' && $("#to").val() != '') {
                        from_to($("#from").val(), $("#to").val());
					}
				}
				function from_to(from, to) {
					//  clearThat();
					DeleteMarkers('from_loc');
					DeleteMarkers('to_loc');
					if (from == '')
                    from = $('#from').val();
					
					if (to == '')
                    to = $('#to').val();
					//alert("from_to" + from +"   to "+to);
					$("#from_lat_long").val('');
					$("#from_lat").val('');
					$("#from_long").val('');
					$("#to_lat_long").val('');
					$("#to_lat").val('');
					$("#to_long").val('');
					
					// var chks = document.getElementsByName('loc');
					// var waypts = [];
					if(from != ''){
						geocoder.geocode( { 'address': from}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
								if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
									// console.log(results[0].geometry.location);
									$("#from_lat_long").val((results[0].geometry.location));
									$("#from_lat").val(results[0].geometry.location.lat());
									$("#from_long").val(results[0].geometry.location.lng());
									
									setMarker(results[0].geometry.location,'from_loc');
									} else {
									alert("No results found");
								}
								} else {
								var place19 = autocomplete_from.getPlace();
								$("#from_lat_long").val(place19.geometry.location);
							}
						});
					}
					if(to != ''){
						geocoder.geocode( { 'address': to}, function(results, status) {
							if (status == google.maps.GeocoderStatus.OK) {
								if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
									$("#to_lat_long").val((results[0].geometry.location));
									$("#to_lat").val(results[0].geometry.location.lat());
									$("#to_long").val(results[0].geometry.location.lng());
									setMarker(results[0].geometry.location,'to_loc');
									} else {
									alert("No results found");
								}
								} else {
								var place20 = autocomplete_to.getPlace();
								$("#to_lat_long").val(place20.geometry.location);
							}
						});
					}
					// alert('sasa');
					
					// routeDirections();
				}
				
				function callEditFundtion(){
					var from_lat = $('#from_lat').val();
					var from_lng = $('#from_long').val();
					
					var from = new google.maps.LatLng(from_lat,from_lng);
					
					if(from != ''){
						setMarker(from,'from_loc');
					}
					
					var to_lat = $('#to_lat').val();
					var to_lng = $('#to_long').val();
					var to = new google.maps.LatLng(to_lat,to_lng);
					if(to != ''){
						setMarker(to,'to_loc');
					}
					
					// var fromLatlongs = $("#from_lat").val()+", "+$("#from_long").val();
					// var toLatlongs = $("#to_lat").val()+", "+$("#to_long").val();
				}

				$(function () {
					// newDate = new Date();
					// var today = new Date();
					// today.setHours(today.getHours() + 1);

					$('#datetimepicker4').datetimepicker({
						format: 'YYYY-MM-DD HH:mm:ss',
						//minDate: moment(),
						ignoreReadonly: true,
						sideBySide: true,
					}).on('dp.change', function(e) {
					  $('#datetimepicker4').data("DateTimePicker").minDate(moment().add(5,'m'))
					});
					// date: new Date(1434544882775)
					
					
					var from = document.getElementById('from');
					autocomplete_from = new google.maps.places.Autocomplete(from);
					google.maps.event.addListener(autocomplete_from, 'place_changed', function() {
						var place = autocomplete_from.getPlace();
						$("#from_lat_long").val(place.geometry.location);
						$("#from_lat").val(place.geometry.location.lat());
						$("#from_long").val(place.geometry.location.lng());
						// remove disable from zoom level when from has value
						$('#radius-id').prop('disabled', false);
						// routeDirections();
						if(from != ''){
							checkrestrictionfrom('from');	
						}
						show_locations();
					});
					var to = document.getElementById('to');
					autocomplete_to = new google.maps.places.Autocomplete(to);
					google.maps.event.addListener(autocomplete_to, 'place_changed', function() {
						var place = autocomplete_to.getPlace();
						$("#to_lat_long").val(place.geometry.location);
						$("#to_lat").val(place.geometry.location.lat());
						$("#to_long").val(place.geometry.location.lng());
						// routeDirections();
						if(to != ''){
							checkrestrictionto('to');
						}
						show_locations();
					});

			
				});
				
				
				function isNumberKey(evt){
					showPhoneDetail();
					var charCode = (evt.which) ? evt.which : evt.keyCode
					if (charCode > 31 && (charCode < 35 || charCode > 57)){
					return false;
					} else {	
					return true;
					}
				}
				
				function changeCode(id,vehicleId) {
					// alert(id);
					$.ajax({
						type: "POST",
						url: 'change_code.php',
						dataType: 'json',
						data: {id: id,eUnit: 'yes'},
						success: function (dataHTML)
						{
							document.getElementById("vPhoneCode").value = dataHTML.vPhoneCode;
							document.getElementById("eUnit").value = dataHTML.eUnit;
							document.getElementById("vRideCountry").value = dataHTML.vCountryCode;
							document.getElementById("vTimeZone").value = dataHTML.vTimeZone;
							$("#change_eUnit").text(dataHTML.eUnit);
							var substr = <?php  echo json_encode($radius_driver); ?>;
							substr.forEach(function(item) {
								$('#radius-id option[value="'+item+'"]').text(item + " " +dataHTML.eUnit+' Radius');
							});					
							showPhoneDetail();
							showVehicleCountryVise(id,vehicleId,eType);
						}
					});
				}
				$(document).ready(function(){
					var con = $("#vCountry").val();
					changeCode(con,'<?php  echo $iVehicleTypeId; ?>');
					if($("#from").val() == ""){
				        $('#radius-id').prop('disabled', 'disabled');
					} else {
				 		$('#radius-id').prop('disabled', false);
				    }
				});

				$('#from').on('change', function () {
				    if (this.value == '') {
				      $('#radius-id').prop('disabled', 'disabled');
				    } else {
				      $('#radius-id').prop('disabled', false);
				    }
				});

				function showPopupDriver(driverId) {
                    if ($("#driver_popup").is(":visible") && $('#driver_popup ul').attr('class') == driverId) {
                        $("#driver_popup").hide("slide", {direction: "right"}, 700);
						} else {
                        //alert(driverId);
                        $("#driver_popup").hide();
                        $.ajax({
                            type: "POST",
                            url: "get_driver_detail_popup.php",
                            dataType: "html",
                            data: {driverId: driverId},
                            success: function (dataHtml2) {
                                $('#driver_popup').html(dataHtml2);
                                $("#driver_popup").show("slide", {direction: "right"}, 700);
								}, error: function (dataHtml2) {
								
							}
						});
					}
				}
				
				function showVehicleCountryVise(countryId,vehicleId,eType) {
					/*alert(countryId);
					alert(eType);*/
					$.ajax({
						type: "POST",
						url: "ajax_booking_details.php",
						dataType: "html",
						data: {countryId: countryId,type: 'getVehicles',iVehicleTypeId: vehicleId,eType:eType},
						success: function (dataHtml2) {
							$('#iVehicleTypeId').html(dataHtml2);
							// $("#driver_popup").show("slide", {direction: "right"}, 700);
							}, error: function (dataHtml2) {
							
						}
					});
				}
				
				
                $(document).mouseup(function (e)
                {
                    var container = $("#driver_popup");
                    var container1 = $("#driver_main_list");
					
                    if (!container.is(e.target) && !container1.is(e.target) // if the target of the click isn't the container...
					&& container.has(e.target).length === 0 && container1.has(e.target).length === 0) // ... nor a descendant of the container
                    {
                        container.hide("slide", {direction: "right"}, 700);
					}
				});
				
				function showPhoneDetail() {
					var phone = $('#vPhone').val();
					var phoneCode = $('#vPhoneCode').val();
					if(phone != "" && phoneCode != ""){
						$.ajax({
							type: "POST",
							url: 'ajax_find_rider_by_number.php',
							data: {phone: phone,phoneCode: phoneCode},
							success: function (dataHtml)
							{
								if (dataHtml != "") {
									$("#user_type").val('registered');
									var result = dataHtml.split(':');
									$('#vName').val(result[0]);
									$('#vLastName').val(result[1]);
									$('#vEmail').val(result[2]);
									$('#iUserId').val(result[3]);
									$('#eStatus').val(result[4]);
									if(result[4] == "Inactive" || result[4] == "Deleted") {
										$('#inactiveModal').modal('show'); 
									}
									}else {
									$("#user_type").val('');
									$('#vName').val('');
									$('#vLastName').val('');
									$('#vEmail').val('');
									$('#iUserId').val('');
									$('#eStatus').val('');
								}
							}
							
						});
						}else {
						$("#user_type").val('');
						$('#vName').val('');
						$('#vLastName').val('');
						$('#vEmail').val('');
						$('#iUserId').val('');
						$('#eStatus').val('');
					}
				}
				
				function setNewDriverLocations(type) {
					// alert(type);
					$("#newType").val(type);
					vType = $("#iVehicleTypeId").val();
					for (var i = 0; i < driverMarkers.length; i++) {
						driverMarkers[i].setMap(null);
					}
					//console.log(newLocations);
					//return false;
					var infowindow = new google.maps.InfoWindow();
					for (var i = 0; i < newLocations.length; i++) {
						if (type == newLocations[i].location_type || type == "") {
							var str33 = newLocations[i].location_carType;
							if(vType == "" || (str33 != null && str33.indexOf(vType) != -1)){
								newName = newLocations[i].location_name;
								newOnlineSt = newLocations[i].location_online_status;
								newLat = newLocations[i].google_map.lat;
								newLong = newLocations[i].google_map.lng;
								newDriverImg = newLocations[i].location_image;
								newMobile = newLocations[i].location_mobile;
								newDriverID = newLocations[i].location_ID;
								newImg = newLocations[i].location_icon;
								latlng = new google.maps.LatLng(newLat, newLong);
								// bounds.push(latlng);
								// alert(newImg);
								content = '<table><tr><td rowspan="4"><img src="' + newDriverImg + '" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + newDriverID + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+' + newMobile + '</b></td></tr></table>';
								var drivermarker = new google.maps.Marker({
									map: map,
									//animation: google.maps.Animation.DROP,
									position: latlng,
									icon: newImg
								});
								google.maps.event.addListener(drivermarker,'click', (function(drivermarker,content,infowindow){ 
									return function() {
										infowindow.setContent(content);
										infowindow.open(map,drivermarker);
									};
								})(drivermarker,content,infowindow));
								// alert(content);
								driverMarkers.push(drivermarker);
							}
						}
					}
					//var markers = [];//some array
					// var bounds = new google.maps.LatLngBounds();
					// for (var i = 0; i < driverMarkers.length; i++) {
					// bounds.extend(driverMarkers[i].getPosition());
					// }
					
					// map.fitBounds(bounds);
					setDriverListing(vType);
				}
				
				function getFarevalues(vehicleId) {
					var booking_date = $("#datetimepicker4").val();
					var vCountry = $('#vCountry').val();
					var tollcostval = $('#fTollPrice').val();
					if(vehicleId == ""){
						vehicleId = $("#iVehicleTypeId").val();
					}
					if(($("#from").val() != "") && ($("#to").val() != "")) {
						var FromLatLong = $("#from_lat").val()+", "+$("#from_long").val();
						var ToLatLong = $("#to_lat").val()+", "+$("#to_long").val();
					}
					// alert(vehicleId);
					if(vehicleId != ""){
						$.ajax({
							type: "POST",
							url: 'ajax_estimate_by_vehicle_type.php',
							dataType: 'json',
							data: {'vehicleId' : vehicleId,'booking_date':booking_date,'vCountry':vCountry,'FromLatLong':FromLatLong,'ToLatLong':ToLatLong},
							success: function (dataHtml)
							{
								if (dataHtml != "") {
									// var result = dataHtml.split(':');
									var iBaseFare = parseFloat(dataHtml.iBaseFare).toFixed(2);
									var fPricePerKM = parseFloat(dataHtml.fPricePerKM).toFixed(2);
									var fPricePerMin = parseFloat(dataHtml.fPricePerMin).toFixed(2);
									var iMinFare = parseFloat(dataHtml.iMinFare).toFixed(2);
									var fPickUpPrice = parseFloat(dataHtml.fPickUpPrice).toFixed(2);
									var fNightPrice = parseFloat(dataHtml.fNightPrice).toFixed(2);
									var fSurgePrice = parseFloat(dataHtml.fSurgePrice).toFixed(2);
									var SurgeType = dataHtml.SurgeType;
									var Time = dataHtml.Time;
									eFlatTrip = dataHtml.eFlatTrip;
									var fFlatTripPrice = dataHtml.fFlatTripPrice;

									if(eFlatTrip == 'Yes'){
										fFlatTripPrice = parseFloat(fFlatTripPrice).toFixed(2);
										$('#fix_fare_price').text(fFlatTripPrice);
										fPricePerMin = 0;
										fPricePerKM = 0;
										iMinFare = 0;
										$('#eFlatTrip').val(eFlatTrip);
										$('#fFlatTripPrice').val(fFlatTripPrice);
										$("#FixFare").show();
										$("#BaseFare").hide();
										$("#MinFare").hide();
										$("#DistanceFare").hide();
										$("#TimeFare").hide();
									} else {
										$('#eFlatTrip').val(eFlatTrip);
										$("#FixFare").hide();
										$("#BaseFare").show();
										$("#MinFare").show();
										$("#DistanceFare").show();
										$("#TimeFare").show();
									}

									$('#minimum_fare_price').text(iMinFare);
									$('#base_fare_price').text(iBaseFare);
									$('#dist_fare_price').text(parseFloat(fPricePerKM*$('#dist_fare').text()).toFixed(2));
									/* var eunit = $("#eUnit").val();
										if(eunit == "Miles"){
										$('#dist_fare_price').text(parseFloat((fPricePerKM*($('#dist_fare').text()*1.6))).toFixed(2));
									} */
									
									$('#time_fare_price').text(parseFloat(fPricePerMin*$('#time_fare').text()).toFixed(2));
									if(ENABLE_TOLL_COST == 'Yes'){
										if($('#fTollPrice').val() > 0 && $('#eTollSkipped').val() == 'No' && eFlatTrip != 'Yes' && eType != 'UberX'){
											$("#toll_price").show();
											$('#toll_price_val').text(tollcostval);
										}  else {
											$("#toll_price").hide();
										}
									}
									if(eFlatTrip == 'Yes'){
										var totalPrice = (parseFloat($('#fix_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat($('#time_fare_price').text())).toFixed(2);
									} else {
										if(ENABLE_TOLL_COST == 'Yes'){
											if($('#fTollPrice').val() > 0 && $('#eTollSkipped').val() == 'No' && eFlatTrip != 'Yes' && eType != 'UberX'){
													var totalPrice = (parseFloat($('#base_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat(tollcostval)+parseFloat($('#time_fare_price').text())).toFixed(2);
											} else {
										var totalPrice = (parseFloat($('#base_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat($('#time_fare_price').text())).toFixed(2);
											}
										} else {
											var totalPrice = (parseFloat($('#base_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat($('#time_fare_price').text())).toFixed(2);
										}
									}
									
									if(parseFloat(totalPrice) >= parseFloat($('#minimum_fare_price').text())) {
										$('#total_fare_price').text(totalPrice);
										if($('#fTollPrice').val() > 0 && $('#eTollSkipped').val() == 'No' && eFlatTrip != 'Yes' && eType != 'UberX'){
											$('#totalcost').text(totalPrice-tollcostval);
										} else {
											$('#totalcost').text(totalPrice);
										}
										$("#MinFare").hide();
										}else {
										$('#total_fare_price').text($('#minimum_fare_price').text());
										$('#totalcost').text($('#minimum_fare_price').text());
										$("#MinFare").show();
									}
									if(fSurgePrice > 1){
										if($('#fTollPrice').val() > 0 && $('#eTollSkipped').val() == 'No' && eFlatTrip != 'Yes' && eType != 'UberX'){
											var normalfare = parseFloat($('#total_fare_price').text() - tollcostval).toFixed(2);
										} else {
										var normalfare = $('#total_fare_price').text();
										}
										$('#normal_fare_price').text(normalfare);
										if($('#fTollPrice').val() > 0 && $('#eTollSkipped').val() == 'No' && eFlatTrip != 'Yes' && eType != 'UberX'){
											var totalfare = ($('#total_fare_price').text()-tollcostval);
											var surgefare = parseFloat(totalfare*fSurgePrice).toFixed(2);
											var surgefarenew = (parseFloat(tollcostval)+parseFloat(surgefare)).toFixed(2);
											$('#total_fare_price').text(surgefarenew);
										} else {
										var surgefare = parseFloat($('#total_fare_price').text()*fSurgePrice).toFixed(2);
											$('#total_fare_price').text(surgefare);
										}
										$('#totalcost').text(surgefare);
										var difference = parseFloat(surgefare-normalfare).toFixed(2);
										// console.log(normalfare+" "+surgefare+" "+difference);
										if(SurgeType == "Night"){
											$("#fNightPrice").val(fSurgePrice);
											$("#fPickUpPrice").val(1);
										} else {
											$("#fNightPrice").val(1);
											$("#fPickUpPrice").val(fSurgePrice);
											
										}
										$("#surge_fare_diff").text(difference);
										$("#fare_surge_price").text(fSurgePrice);
										$("#fare_surge").show();
										if(showsurgemodal == "Yes"){
											$("#surge_factor").text(fSurgePrice);
											$("#surge_type").text(SurgeType);
											$("#surge_timing").text(Time);
											$("#surgemodel").modal('show');
										}

										if(eFlatTrip == 'Yes'){
											$("#fare_normal").hide();
										} else {
											$("#fare_normal").show();
										}

										showsurgemodal == "Yes";
									} else {
										$("#fare_surge").hide();
										$("#fare_normal").hide();
										$("#fNightPrice").val(1);
										$("#fPickUpPrice").val(1);
									}
									
									}else {
									$('#minimum_fare_price').text('0');
									$('#base_fare_price').text('0');
									$('#dist_fare_price').text('0');
									$('#time_fare_price').text('0');
									$('#total_fare_price').text('0');
								}
							}
						});
						// setDriverListing(vehicleId);
						// getDriversList(vehicleId);
					}
				}
				
				function showAsVehicleType(vType) {
					var type = $("#newType").val();
					for (var i = 0; i < driverMarkers.length; i++) {
						driverMarkers[i].setMap(null);
					}
					//console.log(newLocations);
					//return false;
					var infowindow = new google.maps.InfoWindow();
					for (var i = 0; i < newLocations.length; i++) {
						if (type == newLocations[i].location_type || type == "") {
							var str33 = newLocations[i].location_carType;
							if(vType == "" || (str33 != null && str33.indexOf(vType) != -1)){
								newName = newLocations[i].location_name;
								newOnlineSt = newLocations[i].location_online_status;
								newLat = newLocations[i].google_map.lat;
								newLong = newLocations[i].google_map.lng;
								newDriverImg = newLocations[i].location_image;
								newMobile = newLocations[i].location_mobile;
								newDriverID = newLocations[i].location_ID;
								newImg = newLocations[i].location_icon;
								latlng = new google.maps.LatLng(newLat, newLong);
								// bounds.push(latlng);
								// alert(newImg);
								content = '<table><tr><td rowspan="4"><img src="' + newDriverImg + '" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + newDriverID + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+' + newMobile + '</b></td></tr></table>';
								var drivermarker = new google.maps.Marker({
									map: map,
									//animation: google.maps.Animation.DROP,
									position: latlng,
									icon: newImg
								});
								google.maps.event.addListener(drivermarker,'click', (function(drivermarker,content,infowindow){ 
									return function() {
										infowindow.setContent(content);
										infowindow.open(map,drivermarker);
									};
								})(drivermarker,content,infowindow));
								// alert(content);
								driverMarkers.push(drivermarker);
							}
						}
					}
					//var markers = [];//some array
					// var bounds = new google.maps.LatLngBounds();
					// for (var i = 0; i < driverMarkers.length; i++) {
					// bounds.extend(driverMarkers[i].getPosition());
					// }
					
					// map.fitBounds(bounds);
					setDriverListing(vType);
					getFarevalues(vType);
				}
				
				setInterval(function() {
					if(eTypeQ11 == 'yes') {
						setDriversMarkers('test');
						$("#driver_main_list").html('');
					}
				},35000);
				
				
				function setFormBook(){
					var statusVal = $('#vEmail').val();
					if(statusVal != ''){
						$.ajax({
							type: "POST",
							url: 'ajax_checkBooking_email.php',
							data: 'vEmail=' + statusVal,
							success: function (dataHtml)
							{
								var testEstatus = dataHtml.trim();
								if(testEstatus != 'Active' && testEstatus != '') {
									if(confirm("The selected user account is in 'Inactive / Deleted' mode. Do you want to Active this User ?'")){
										eTypeQ11 = 'no';
										$("#add_booking_form").attr('action','action_booking.php');
										$( "#submitbutton" ).trigger( "click" );
										// e.stopPropagation();
										// e.preventDefault();
										return false;
										}else {
										$("#vEmail").focus();
										return false;
									}
									}else {
									eTypeQ11 = 'no';
									$("#add_booking_form").attr('action','action_booking.php');
									$( "#submitbutton" ).trigger( "click" );
									// e.stopPropagation();
									// e.preventDefault();
									return false;
								}
							}
						});	
						}else {
						return false;
					}
				}
				
				function get_drivers_list(keyword) {
					vCountry = $("#vCountry").val();
					vType = $("#iVehicleTypeId").val();
					
					if($("#eFemaleDriverRequest").is(":checked")){
						eLadiesRide = 'Yes';
						}else {
						eLadiesRide = 'No';
					}
					
					if($("#eHandiCapAccessibility").is(":checked")){
						eHandicaps = 'Yes';
						}else {
						eHandicaps = 'No';
					}
					// $("#imageIcon").show();
					
					$.ajax({
						type: "POST",
						url: "get_available_driver_list.php",
						dataType: "html",
						data: {vCountry: vCountry,keyword: keyword,iVehicleTypeId:vType,eLadiesRide: eLadiesRide,eHandicaps: eHandicaps},
						success: function(dataHtml2){
							$('#driver_main_list').show();
							if(dataHtml2 != ""){
							$('#driver_main_list').html(dataHtml2);
							} else {
								$('#driver_main_list').html('<h4 style="margin:25px 0 0 15px">Sorry , No <?php  echo $langage_lbl_admin['LBL_DIVER']; ?> Found.</h4>');
							}
							if($("#eAutoAssign").is(':checked')){
								$(".assign-driverbtn").attr('disabled','disabled');
							}
							$("#imageIcon").hide();
							},error: function(dataHtml2) {
							
						}
					});
				}
				
				$("#eAutoAssign").on('change', function(){
					if($(this).prop('checked')) {
						$("#iDriverId").val('');
						$("#iDriverId").attr('disabled','disabled');
						$(".assign-driverbtn").attr('disabled','disabled');
						$("#showdriverSet001").hide();
						$('#myModalautoassign').modal('show');
					}else {
						$("#iDriverId").removeAttr('disabled');
						$(".assign-driverbtn").removeAttr('disabled');
						$('#myModalautoassign').modal('hide');
					}
				});
				var bookId = '<?php  echo $iCabBookingId; ?>';
				if(bookId != "") {
					if($("#eAutoAssign").prop('checked')) {
						$("#iDriverId").val('');
						$("#iDriverId").attr('disabled','disabled');
						}else {
						$("#iDriverId").removeAttr('disabled');
					}
				}
				
				$(document).ready(function() {
					var referrer;
					if($("#previousLink").val() == "" ){
						referrer =  document.referrer;	
						//alert(referrer);
						}else { 
						referrer = $("#previousLink").val();
					}
					if(referrer == "") {
						referrer = "cab_booking.php";
						}else {
						$("#backlink").val(referrer);
					}
					// $(".back_link").attr('href',referrer);
				});
				
				$('#datetimepicker4').keydown(function(e) {
					e.preventDefault();
					return false;
				});
				
				$('#eFemaleDriverRequest').click(function() {
					if($(this).is(':checked'))
					setDriversMarkers('true');
					else
					setDriversMarkers('true');
				});
				
				$('#eHandiCapAccessibility').click(function() {
					if($(this).is(':checked'))
					setDriversMarkers('true');
					else
					setDriversMarkers('true');
				});
				$('#reset12').click(function(){
					window.location.reload(true);
					/*$('#newSelect02').prop('selectedIndex',0);
					$("#newSelect02").val("").trigger("change");
					setDriverListing();*/
				});

				function checkrestrictionfrom(type) {
					if(($("#from").val() != "") || ($("#to").val() != "")) {
						$.ajax({
							type: "POST",
							url: 'checkForRestriction.php',
							dataType: 'html',
							data: {fromLat: $("#from_lat").val(),fromLong: $("#from_long").val(),type:type},
							success: function(dataHtml5)
							{
								if($.trim(dataHtml5) != ''){
									alert($.trim(dataHtml5));
								}
							},
							error: function(dataHtml5)
							{
							}
						});
					}
				}

				function checkrestrictionto(type) {
					if(($("#from").val() != "") || ($("#to").val() != "")) {
						$.ajax({
							type: "POST",
							url: 'checkForRestriction.php',
							dataType: 'html',
							data: {toLat: $("#to_lat").val(),toLong: $("#to_long").val(),type:type},
							success: function(dataHtml5)
							{
								if($.trim(dataHtml5) != ''){
									alert($.trim(dataHtml5));
								}
							},
							error: function(dataHtml5)
							{
							}
						});
					}
				}

				$('#add_booking_form').on('keyup keypress', function(e) {
				  var keyCode = e.keyCode || e.which;
				  if (keyCode === 13) { 
				    e.preventDefault();
				    return false;
				  }
				});
				$("#submitbutton").on("click", function(event) {
					var isvalidate = $("#add_booking_form")[0].checkValidity();
    				if (isvalidate) {
				  	event.preventDefault();
				  //	$('#submitbutton').prop('disabled', true);
					  	if(eType != 'UberX' && eFlatTrip != 'Yes'){
							if(ENABLE_TOLL_COST == 'Yes') {
					  			$(".loader-default").show();
								if(($("#from").val() != "" && $("#from_lat_long").val() != "") && ($("#to").val() != "" && $("#to_lat_long").val() != "")) {
									var newFromtoll = $("#from_lat").val()+","+$("#from_long").val();
									var newTotoll = $("#to_lat").val()+","+$("#to_long").val();
							  		$.getJSON("https://tce.cit.api.here.com/2/calculateroute.json?app_id=<?=$TOLL_COST_APP_ID?>&app_code=<?=$TOLL_COST_APP_CODE?>&waypoint0="+newFromtoll+"&waypoint1="+newTotoll+"&mode=fastest;car", function(result){
										var tollCurrency = result.costs.currency;
										var tollCost = result.costs.details.tollCost;
										<?php  if ($eTollSkipped == 'Yes') { ?>
											var tollskip = 'Yes';
										<?php  } else { ?>
											var tollskip = 'No';
										<?php  } ?>
										$('#tollcost').text(tollCurrency+" "+tollCost);
					        	 		if(tollCost != '0.0' && $.trim(tollCost) != "" && tollCost != '0' ) {
					        	 			$(".loader-default").hide();
											    var modal = bootbox.dialog({
											        message: $(".form-content").html(),
											        title: "Toll Route",
											        buttons: [
											          {
											            label: "Continue",
											            className: "btn btn-primary",
											            callback: function(result) {
											           // alert("toll"+tollskip);
											            $("#vTollPriceCurrencyCode").val(tollCurrency);
												    	$("#fTollPrice").val(tollCost);
												    	$("#eTollSkipped").val(tollskip);
														$("#add_booking_form").submit();
											            return true;
											            }
											          },
											          {
											            label: "Close",
											            className: "btn btn-default",
											            callback: function() {
											             // $('#submitbutton').prop('disabled', false);
											            }
											          }
											        ],
											        show: false,
											        onEscape: function() {
											          modal.modal("hide");
											          //$('#submitbutton').prop('disabled', false);
											        }
											    });
											    modal.on('shown.bs.modal', function() {     
											    	modal.find('.modal-body').on('change', 'input[type="checkbox"]', function(e) {
											    		$(this).attr("checked", this.checked);
											    		//$(this).val(this.checked ? "Yes" : "No");
											    		if($(this).is(':checked')){
											            	tollskip = 'Yes';
											            } else {
															tollskip ='No';
														}
														//alert(tollskip);
											    	}); 
											    }); 
											    modal.modal("show");
					        	 		} else {
					        	 			$(".loader-default").hide();
				        	 				$("#add_booking_form").submit();
				        	 				return true;
					        	 		}
							    	}).fail(function() { alert("Please Select Proper Route.");$(".loader-default").hide();});
								} /*else {
									$(".loader-default").hide();
		        	 				$("#add_booking_form").submit();
		        	 				return true;
								}*/
							} else {
		        	 			$("#add_booking_form").submit();
		        	 			return true;
			    	 		}
						} else {
	    	 				$("#add_booking_form").submit();
	    	 				return true;
		    	 		} 
					}
				});
			</script>
		</body>
		<!-- END BODY-->
	</html>	
	<div class="loader-default"></div>
	<div class="form-content" style="display:none;">
		  <p><?php  echo $langage_lbl_admin['LBL_TOLL_PRICE_DESC']; ?></p>
	      <form class="form" role="form" id="formtoll">
	      	<div class="checkbox">
	          <label>
	          	<input type="checkbox" name="eTollSkipped1" id="eTollSkipped1" value="Yes" <?php  if ($eTollSkipped == 'Yes') echo 'checked'; ?>/> Ignore Toll Route
	          </label>
	      </div>
	      </form>
	      <p style="text-align: center;font-weight: bold;">
		      <span>Total Fare <?php  echo $generalobj->symbol_currency(); ?><b id="totalcost">0</b></span>+
		      <span>Toll Price <b id="tollcost">0</b></span>
	  	</p>
    </div>
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-large">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">x</span>
					</button>
					<h4 class="modal-title" id="myModalLabel"> How It Works?</h4>
				</div>
				<div class="modal-body">
					<p><b>Flow </b>: Through "Manual Taxi Dispatch" Feature, you can book Rides for customers who ordered for a Ride by calling you. There will be customers who may not have iPhone or Android Phone or may not have app installed on their phone. In this case, they will call Taxi Company (your company) and order Ride which may be needed immediately or after some time later.</p>
					<p>- Here, you will fill their info in the form and dispatch a taxi for him.</p>
					<p>- If the customer is already registered with us, just enter his phone number and his info will be fetched from the database when "Get Details" button is clicked. Else fill the form.</p>
					<p>- Once the Trip detail is added, Fare estimate will be calculated based on Pick-Up Location, Drop-Off Location and Car Type.</p>
					<p>- Admin will need to communicate & confirm with Driver and then select him as Driver so the Ride can be allotted to him. </p>
					<p>- Clicking on "Book" Button, the Booking detail will be saved and will take Administrator to the "Ride Later Booking" Section. This page will show all such bookings.</p>
					<p>- Both Driver and Rider will receive the booking details through Email and SMS as soon as the form is submitted. Based on this booking details, Driver will pickup the rider at the scheduled time.</p>
					<p>- They both will get the reminder SMS and Email as well before 30 minutes of actual trip</p>
					<p>- The assigned Driver can see the upcoming Bookings from his App under "My Bookings" section.</p>
					<p>- Driver will have option to "Start Trip" when he reaches the Pickup Location at scheduled time or "Cancel Trip" if he cannot take the ride for some reason. If the Driver clicks on "Cancel Trip", a notification will be sent to Administrator so he can make alternate arrangements.</p>
					<p>- Upon clicking on "Start Trip", the ride will start in driver's App in regular way.</p>
					<p>&nbsp;</p>
					<p><b>Auto Assign Driver </b>: The "Ride Later" booking made from the mobile application has the "Driver Auto Assign" by default enabled. Furthermore Admin can also select this option while adding the booking manually from admin panel</p>
					<p>- Driver auto assignment process works as explained below</p>
					<p>- System will automatically sends the request to drivers who are online and available within pickup location radius</p>
					<p>- Driver(s) will get the 30 seconds dial screen request  before 8-12 minutes before the actual pickup time. This request is same like "Request Now" one.</p>
					<p>- If no driver(s) accepts the request then system will make a 2nd try after 4 minutes and sends the request again . At this point system also notifies the admin through email that no any drivers had accepted the request in first try.</p>
					<p>- Again If no driver(s) accepts the request then system will make a 3rd and last try after 4 minutes and send the request again. At this point system also notifies admin through email that no any drivers had accepted the request in 2nd try.</p>
					<p>- System makes the 3 trials and if in any trial, no drivers availabe in that area then it will inform administrator about unavailability of driver so administrator takes necessary action to contact that rider and arrange the taxi for him</p>
					<!--	<p><span><img src="images/mobile_app_booking.png"></img></span></p>-->
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="myModalufx" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-large">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">x</span>
					</button>
					<h4 class="modal-title" id="myModalLabel"> How It Works?</h4>
				</div>
				<div class="modal-body">
					<p><b>Flow </b>: Through "Manual Booking" Feature, you can book providers for users who ordered for a Service by calling you. There will be users who may not have iPhone or Android Phone or may not have app installed on their phone. In this case, they will call Company (your company) and order service which may be needed immediately or after some time later.</p>
					<p>- Here, you will fill their info in the form and dispatch a service provider for them.</p>
					<p>- If the user is already registered with us, just enter his phone number and his info will be fetched from the database when "Get Details" button is clicked. Else fill the form.</p>
					<p>- Once the Job detail is added, estimate will be calculated based on Service or Service provider selected.</p>
					<p>- Admin will need to communicate & confirm with provider and then select him as provider so the Job can be allotted to him. </p>
					<p>- Clicking on "Book Now" Button, the Booking detail will be saved and will take Administrator to the "Scheduled Booking" Section. This page will show all such bookings.</p>
					<p>- Both Provider and User will receive the booking details through Email and SMS as soon as the form is submitted. Based on this booking details, Provider will go to user's location at the scheduled time.</p>
					<p>- They both will get the reminder SMS and Email as well before 30 minutes of actual job</p>
					<p>- The assigned provider can see the upcoming Bookings from his App under "My Jobs" section.</p>
					<p>- Provider will have option to "Start Job" when he reaches the Job Location at scheduled time or "Cancel Job" if he cannot take the job for some reason. If the provider clicks on "Cancel Job", a notification will be sent to Administrator so he can make alternate arrangements.</p>
					<p>- Upon clicking on "Start Job", the service  will start in provider's App in regular way.</p>
					<p>&nbsp;</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="myModalautoassign" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-large">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">x</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Alert</h4>
				</div>
				<div class="modal-body">
					<p style="font-size: 15px;"> Please make sure that the booking time is 20 minutes ahead from current time. So if your current time is 3:00 P.M then please select 3:20 P.M as booking time.  This gives a room to auto assign drivers properly.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary btn-success" data-dismiss="modal">OK</button>
				</div>
			</div>
		</div>
	</div>

