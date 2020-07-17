<?php 
	include_once('../common.php');
	if (!isset($generalobjAdmin)) {
		require_once(TPATH_CLASS . "class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	$script = "AdminFareEstimate";
	
	// echo date("Y-m-d H:i:s");exit;
	function converToTz($time, $toTz, $fromTz,$dateFormat="Y-m-d H:i:s") {
	    $date = new DateTime($time, new DateTimeZone($fromTz));
	    $date->setTimezone(new DateTimeZone($toTz));
	    $time = $date->format($dateFormat);
	    return $time;
	}
	
	$iVehicleTypeId = isset($_REQUEST['iVehicleTypeId']) ? $_REQUEST['iVehicleTypeId'] : '';
	$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
	// $varmsg = isset($_REQUEST['varmsg']) ? $_REQUEST['varmsg'] : '';
	
	//For Country
	
	$dBooking_date = "";
	
	
	$sql="select vVehicleType,fPricePerKM,fPricePerMin,iBaseFare,iMinFare,fCommision,iCountryId from vehicle_type where iVehicleTypeId = '$iVehicleTypeId'";
	$db_vehicle_details = $obj->MySQLSelect($sql);
	
	$default_currency = $generalobj->symbol_currency();
	$vVehicleType = $db_vehicle_details[0]['vVehicleType'];
	$fPricePerKM = $db_vehicle_details[0]['fPricePerKM'];
	$fPricePerMin = $db_vehicle_details[0]['fPricePerMin'];
	$iBaseFare = $db_vehicle_details[0]['iBaseFare'];
	$iMinFare = $db_vehicle_details[0]['iMinFare'];
	$fCommision = $db_vehicle_details[0]['fCommision'];
	$iCountryId = $db_vehicle_details[0]['iCountryId'];
	
	// $default_currency = $generalobj->symbol_currency();
	
	$vCountry = "All";
	$eUnit = $DEFAULT_DISTANCE_UNIT;
	if($iCountryId != "-1"){
		$sql="select vCountry,eUnit from country where iCountryId = '$iCountryId'";
		$db_country = $obj->MySQLSelect($sql);
		$vCountry = $db_country[0]['vCountry'];
		$eUnit = $db_country[0]['eUnit'];
	}

	if(isset($_POST['btnsubmit'])){
		// echo "<pre>";print_r($_POST);exit;
		$iBaseFare = isset($_POST['iBaseFare']) ? $_POST['iBaseFare'] : '';
		$fPricePerKM = isset($_POST['fPricePerKM']) ? $_POST['fPricePerKM'] : '';
		$fPricePerMin = isset($_POST['fPricePerMin']) ? $_POST['fPricePerMin'] : '';
		$iMinFare = isset($_POST['iMinFare']) ? $_POST['iMinFare'] : '';
		$fCommision = isset($_POST['fCommision']) ? $_POST['fCommision'] : '';
		// $vSourceAddresss = isset($_POST['vSourceAddresss']) ? $_POST['vSourceAddresss'] : '';
		// $tDestAddress = isset($_POST['tDestAddress']) ? $_POST['tDestAddress'] : '';
		/* 
			
			$from_lat_long = isset($_POST['from_lat_long']) ? $_POST['from_lat_long'] : '';
			$from_lat = isset($_POST['from_lat']) ? $_POST['from_lat'] : '';
			$from_long = isset($_POST['from_long']) ? $_POST['from_long'] : '';
			$to_lat_long = isset($_POST['to_lat_long']) ? $_POST['to_lat_long'] : '';
			$to_lat = isset($_POST['to_lat']) ? $_POST['to_lat'] : '';
		$to_long = isset($_POST['to_long']) ? $_POST['to_long'] : ''; */
		
		$where = " WHERE `iVehicleTypeId` = '" . $iVehicleTypeId . "'";
		$query = "update vehicle_type SET
		`iBaseFare` = '" . $iBaseFare . "',
		`fPricePerKM` = '" . $fPricePerKM . "',
		`fPricePerMin` = '" . $fPricePerMin . "',
		`iMinFare` = '" . $iMinFare . "',
		`fCommision` = '" . $fCommision . "'"
		. $where;
		
        $obj->sql_query($query);
		
		header("Location:vehicle_fare_estimate.php?iVehicleTypeId=" .$iVehicleTypeId . '&success=1');
        exit;
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title><?=$SITE_NAME;?> | Fare Estimator</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link rel="stylesheet" href="css/select2/select2.min.css" type="text/css" >
        <?php  include_once('global_files.php');?>
        <script src="http://maps.google.com/maps/api/js?sensor=true&key=<?= $GOOGLE_SEVER_API_KEY_WEB ?>&libraries=places" type="text/javascript"></script>
        <script type='text/javascript' src='../assets/map/gmaps.js'></script>
        <script type='text/javascript' src='../assets/js/jquery-ui.min.js'></script>
	</head>
    <body class="padTop53">
        <div id="wrap">
            <?php  include_once('header.php'); ?>
            <?php  include_once('left_menu.php'); ?>
            <div id="content">
                <div class="inner" style="min-height: 700px;">
                    <div class="row">
                        <div class="col-lg-8">
                        	
							<h1> Fare Estimator</h1>
							
						</div>
						
					</div>
                    <hr />
					<?php  if ($success == 1) {?>
						<div class="alert alert-success alert-dismissable msgs_hide">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
							<?= $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT']; ?> Updated successfully.
						</div><br/>
						<?php  } elseif ($success == 2) { ?>
						<div class="alert alert-danger alert-dismissable ">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
							"Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
						</div><br/>
					<?php  } ?>
					<form id="_vehicleType_esti_form" name="_vehicleType_esti_form" method="post" action="" enctype="multipart/form-data">
						<div class="form-group" style="display: inline-block;">
							
							<input type="hidden" name="previousLink" id="previousLink" value=""/>
							<input type="hidden" name="backlink" id="backlink" value="cab_booking.php"/>
                            <input type="hidden" name="iCountryId" id="iCountryId" value="<?= $iCountryId; ?>">
                            <input type="hidden" name="distance" id="distance" value="<?= $vDistance; ?>">
                            <input type="hidden" name="duration" id="duration" value="<?= $vDuration; ?>">
                            <input type="hidden" name="from_lat_long" id="from_lat_long" value="<?= $from_lat_long; ?>" >
                            <input type="hidden" name="from_lat" id="from_lat" value="<?= $from_lat; ?>" >
                            <input type="hidden" name="from_long" id="from_long" value="<?= $from_long; ?>" >
                            <input type="hidden" name="to_lat_long" id="to_lat_long" value="<?= $to_lat_long; ?>" >
                            <input type="hidden" name="to_lat" id="to_lat" value="<?= $to_lat; ?>" >
                            <input type="hidden" name="to_long" id="to_long" value="<?= $to_long; ?>" >
                            <input type="hidden" value="1" id="location_found" name="location_found">
                            <input type="hidden" value="<?= $GOOGLE_SEVER_API_KEY_WEB; ?>" id="google_server_key" name="google_server_key" >
                            <input type="hidden" value="KMs" id="eUnit" name="eUnit" >
                            
							
						</div>
						<div class="map-main-page-inner">
							<div class="map-main-page-inner-tab fare-estimate-left">
                                <input type="hidden" name="APP_TYPE" value="<?= $APP_TYPE; ?>"/>
								<input type="hidden" name="iVehicleTypeId" id="iVehicleTypeId" value="<?= $iVehicleTypeId; ?>">
								
								<div class="row">
									<div class="col-lg-12">
										<label><?php  echo $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT']; ?><span class="red"> *</span> 
											<?php  if($APP_TYPE != "UberX"){ ?>
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Type of vehicle like Small car, Luxury car, SUV, VAN for example'></i>
											<?php  } ?>
										</label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vVehicleType"  id="vVehicleType"  value="<?= $vVehicleType; ?>"  readonly>
									</div>
									
								</div>
								<div class="row">
									 <div class="col-lg-12">
										  <label>Country <span class="red"> *</span></label>
									 </div>
									 <div class="col-lg-6">
										  <select class="form-control" name = 'iCountryId' id="iCountryId" disabled>
											   <option value="<?=$iCountryId?>"><?=$vCountry?></option>
										  </select>
									 </div>
									</div>
								
								<div id="Regular_div1">
									<?php  // if($APP_TYPE != 'UberX'){  ?>
									<div class="row" id="hide-basefare">
										<div class="col-lg-12">
											<label> Base Fare<span class="red"> *</span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Base fare is the price that the taxi meter will start at a certain point. Let say if you set base fare $3 then the meter will be set at $3 to begin, and not $0.'></i></label>
										</div>
										<div class="col-lg-6">
											<input type="text" class="form-control" name="iBaseFare"  id="iBaseFare" value="<?= $iBaseFare; ?>" onChange="getpriceCheck_digit(this.value)"> <!-- onChange="getpriceCheck(this.value)" -->
										</div>
									</div>
									<div class="row" id="hide-km">
										<div class="col-lg-12">
											<label> Price Per <em id="change_eUnit" style="font-style: normal"><?=$eUnit;?></em><span class="red"> *</span></label>
										</div>
										<div class="col-lg-6">
											<input type="text" class="form-control" name="fPricePerKM"  id="fPricePerKM" value="<?= $fPricePerKM; ?>" onChange="getpriceCheck_digit(this.value)">  <!-- onchange="getpriceCheck(this.value)" -->
										</div>
										
									</div>
									<?php  // }  ?> 
									
									<div class="row" id="hide-price">
										<div class="col-lg-12">
											<label><?php  echo $langage_lbl_admin['LBL_PRICE_MIN_TXT_ADMIN']; ?><span class="red"> *</span></label>
										</div>
										<div class="col-lg-6">
											<input type="text" class="form-control" name="fPricePerMin"  id="fPricePerMin" value="<?= $fPricePerMin; ?>" onChange="getpriceCheck_digit(this.value)"> <!-- onChange="getpriceCheck(this.value)" -->
											
										</div>
									</div>
									
									<?php  //if($APP_TYPE != 'UberX'){  ?> 
									<div class="row" id="hide-minimumfare">
										<div class="col-lg-12">
											<label>Minimum Fare<span class="red"> *</span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='The minimum fare is the least amount you have to pay. For eg : if you travel a distance of 1 km  , the actual fare will be $10 (base fare $6 + $2/km + $2/min) assuming that it takes 1 min to travel but still you are liable to pay the minimum fare which is $15 for example.'></i></label>
										</div>
										<div class="col-lg-6">
											<input type="text" class="form-control" name="iMinFare"  id="iMinFare" value="<?= $iMinFare; ?>" onChange="getpriceCheck_digit(this.value)">
											<!-- onchange="getpriceCheck(this.value)" -->
											
										</div>
									</div>
									
									<?php  // }  ?> 
								</div>										
								<div class="row">
									<div class="col-lg-12">
										<label> Commision (%)<span class="red"> *</span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='This is % amount that will go to site for each ride.'></i></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="fCommision"  id="fCommision" value="<?= $fCommision; ?>" required onChange="getpriceCheck_digit(this.value)" >
									</div>
								</div>
								<div class="col-lg-12 map-live-hs-mid map-live-hs-pickup">
									<span class="col5">
										<input type="text" class="ride-location1 highalert txt_active form-control first-name1" name="vSourceAddresss"  id="from" value="<?= $vSourceAddresss; ?>" placeholder="<?= ucfirst(strtolower($langage_lbl_admin['LBL_PICKUP_LOCATION_HEADER_TXT'])); ?>" >
										<label class="red" id="loc_from"></label>
										
										<input type="text" class="ride-location1 highalert txt_active form-control last-name1 lolo" name="tDestAddress"  id="to" value="<?= $tDestAddress; ?>" placeholder="Drop Off Location">
										<label class="red" id="loc_to"></label>
									</span>
									
									
								</div>
								<div id="price" style="margin:10px;"></div>
								<div class="col-lg-12 fare-button">
									<a href="javascript:void(0);" onClick="show_locations_price('button')" class="btn btn-default">Check Price Effects</a>
                                    <input type="submit" class="btn btn-default" name="btnsubmit" id="btnsubmit" value="Save Prices" ></div>
							</div>
							<div class="map-page map-page-right map-page-right2">
								<div class="" id="imageIcons" style="width:100%; display:none">
									<div align="center" >                                                                       
										<img src="default.gif"><br/>                                                              
										<span>Retrieving details,Please Wait...</span>                       
									</div>                                                                                 
								</div>
								<div id="show_details"></div>
								<!-- new design
									<div class="map-page-box1">
									<h3>Estimation :</h3>
									<ul>
									<li><em>Distance</em>658.29 KMs</li>
									<li><em>Time</em>670.08 Minutes</li>
									<li><em>Date</em>Nov 13 at 01:18 pm</li>
									</ul>
									</div>
									<div class="map-page-box2">
									<h3>Fare Calculation Details</h3>
									<ul>
									<li>Base/Starting Fare: <em>$ 5.00</em></li>
									</ul>
									</div>
									<div class="map-page-box2">
									<h3>Distance Calculation:</h3>
									<ul>
									<li>Price Per KMs ($ 2.00) * Estimated Distance (658.29 KMs) <em>$ 1,316.58</em></li>
									</ul>
									</div>
									<div class="map-page-box2">
									<h3>Time Calculation:</h3>
									<ul>
									<li>Price Per Minute ($ 5.00) * Estimated Time (670.08 Minutes)<em>$ 3,350.40</em></li>
									</ul>
									</div>
									
									<div class="map-page-box3">
									<h3>PickUp Surcharge Calculation:</h3>
									<ul>
									<li>Surcharge Timing<em>From 10:54:52 To 18:10:00</em></li>
									<li>Surcharge Factor<em>1.60 X</em></li>
									<li>Surcharge Applied<em>Yes</em></li>
									<li><em>Total Fare ($ 9.00) * Surcharge Factor (1.36 X)</em>14.4</li>
									</ul>
								</div> -->
								<!-- new design -->
							</div>
						</div>
					</form>
					<div style="clear:both;"></div>
					<div class="admin-notes">
						<h4>Notes:</h4>
						<ul>
							<!--<li>
								Restricted Area module will list all Restricted Area on this page.
							</li> -->
							<li>Administrator can Add / Edit <?php  echo $langage_lbl['LBL_RIDER_RIDE_MAIN_SCREEN']; ?> later booking on this page.
							</li>
							<!--  <li>
								Administrator need to take care about area locations and restrictions.
							</li>-->
							
						</ul>
					</div>
					
				</div>
                <!--END PAGE CONTENT -->
				
			</div>
            <?php  include_once('footer.php'); ?>
            <div style="clear:both;"></div>
			
			
			
			
		</div>
		<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
		<script type="text/javascript" src="js/moment.min.js"></script>
		<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
		<script type="text/javascript" src="js/plugins/select2.min.js"></script>
		<script>
			var eType = "";
			switch ("<?php  echo $APP_TYPE;?>") {
				case "Ride-Delivery":
				eType =  $('input[name=eType]:checked').val();
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
			
			
			var eTypeQ11 = 'yes';
			var map;
			var geocoder;
			
			var bounds = [];
			var newLocations = "";
			var autocomplete_from;
			var autocomplete_to;
			var service = new google.maps.DistanceMatrixService();
			
			function show_locations_price(click){
				if(($("#from").val() != "" && $("#from_lat_long").val() != "") && ($("#to").val() != "" && $("#to_lat_long").val() != "")) {
					$("#loc_from").text('');
					$("#loc_to").text('');
					$("#imageIcons").show();
					$("#show_details").html('');
					var newFrom = $("#from_lat").val()+", "+$("#from_long").val();
					var newTo = $("#to_lat").val()+", "+$("#to_long").val();
					
					service.getDistanceMatrix({origins: [newFrom],destinations: [newTo],travelMode: 'DRIVING'}, function(response, status){
						
						var route = response.rows[0];
						// console.log(route.elements.length);
						for (var i = 0; i < route.elements.length; i++) {
							$("#distance").val(route.elements[i].distance.value);
							$("#duration").val(route.elements[i].duration.value);
						}
						
						var time_fare = (parseFloat($("#duration").val(), 10) / parseFloat(60, 10)).toFixed(2);
						$('#time_fare').text(time_fare);
						var dist_fare = (parseFloat($("#distance").val(), 10) / parseFloat(1000, 10)).toFixed(2);
						$('#dist_fare').text(dist_fare);
						
						var iBaseFare = $("#iBaseFare").val();
						var fPricePerKM = $("#fPricePerKM").val();
						var fPricePerMin = $("#fPricePerMin").val();
						var iMinFare = $("#iMinFare").val();
						var fCommision = $("#fCommision").val();
						var iCountryId = $("#iCountryId").val();
						var iVehicleTypeId = $("#iVehicleTypeId").val();
						
						$.ajax({
							type: "POST",
							url: 'ajax_estimate_vehicle_admin.php',
							dataType: 'html',
							data: {'iBaseFare' : iBaseFare,'fPricePerKM':fPricePerKM,'fPricePerMin':fPricePerMin,'iMinFare':iMinFare,'fCommision':fCommision,'time':time_fare,'distance':dist_fare,'iCountryId':iCountryId,'iVehicleTypeId':iVehicleTypeId},
							success: function (dataHtml)
							{	
								$("#imageIcons").hide();
								$("#show_details").html(dataHtml);
							}
						});
					});
					
					}else{
					if(click != ""){
						if($("#from").val() == ""){
							$("#loc_from").text('*Please enter pick up location');
							}else{
							$("#loc_from").text('');
						}
						if($("#to").val() == ""){
							$("#loc_to").text('*Please enter drop off location');
							}else{
							$("#loc_to").text('');
						}
					}
				}
			}
			
			$(function () {
				// newDate = new Date();
				// var today = new Date();
				// today.setHours(today.getHours() + 1);
				
				$('#datetimepicker4').datetimepicker({
					format: 'YYYY-MM-DD HH:mm:ss',
					minDate: moment(),
					ignoreReadonly: true,
					sideBySide: true,
				});
				// date: new Date(1434544882775)
				
				
				var from = document.getElementById('from');
				autocomplete_from = new google.maps.places.Autocomplete(from);
				google.maps.event.addListener(autocomplete_from, 'place_changed', function() {
					var place = autocomplete_from.getPlace();
					$("#from_lat_long").val(place.geometry.location);
					$("#from_lat").val(place.geometry.location.lat());
					$("#from_long").val(place.geometry.location.lng());
					show_locations_price('');
				});
				
				var to = document.getElementById('to');
				autocomplete_to = new google.maps.places.Autocomplete(to);
				google.maps.event.addListener(autocomplete_to, 'place_changed', function() {
					var place = autocomplete_to.getPlace();
					$("#to_lat_long").val(place.geometry.location);
					$("#to_lat").val(place.geometry.location.lat());
					$("#to_long").val(place.geometry.location.lng());
					show_locations_price('');
				});
			});
			
			
			function isNumberKey(evt){
				showPhoneDetail();
				var charCode = (evt.which) ? evt.which : evt.keyCode
				if (charCode > 31 && (charCode < 35 || charCode > 57))
				return false;
				return true;
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
			
			
		</script>
	</body>
	<!-- END BODY-->
</html>	

