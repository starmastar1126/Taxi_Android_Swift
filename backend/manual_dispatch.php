<?php  
    include_once("common.php");
	$generalobj->check_member_login();
	$script='Booking';
	
	$tbl_name = 'cab_booking';
	$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : '';
	$var_msg = isset($_REQUEST['var_msg']) ? $_REQUEST['var_msg'] : '';
	$iCabBookingId = isset($_REQUEST['booking_id']) ? $_REQUEST['booking_id'] : '';
	$iCompanyId = $_SESSION['sess_iUserId'];
	$action = ($iCabBookingId != '') ? 'Edit' : 'Add';
	
	$sql1 = "SELECT * FROM `vehicle_type` WHERE 1";
	$db_carType = $obj->MySQLSelect($sql1);
	
	//For Country
	$sql = "SELECT * from country where eStatus = 'Active'" ;
	$db_code = $obj->MySQLSelect($sql);
	//For Currency
	$sql="select * from  currency where eStatus='Active'";
	$db_currency=$obj->MySQLSelect($sql);
	
	$cmp_ssql = " AND iCompanyId = '" . $iCompanyId . "'";
	$sql2 = "select * FROM register_driver WHERE 1 AND eStatus='active' ".$cmp_ssql." ORDER BY vName ASC";
	$db_records_online = $obj->MySQLSelect($sql2);
	// echo "<pre>";
	// print_r($db_records_online); die;
	
	if ($action == 'Edit') {
		$sql = "SELECT * FROM " . $tbl_name . " LEFT JOIN register_user on register_user.iUserId=" . $tbl_name . ".iUserId WHERE " . $tbl_name . ".iCabBookingId = '" . $iCabBookingId . "'";
		$db_data = $obj->MySQLSelect($sql);
		//echo "<pre>";print_R($db_data);echo "</pre>"; die;
		$vPass = $generalobj->decrypt($db_data[0]['vPassword']);
		$vLabel = $id;
		if (count($db_data) > 0) {
			foreach ($db_data as $key => $value) {
				$iUserId = $value['iUserId'];
				$vDistance = $value['vDistance'];
				$vDuration = $value['vDuration'];
				$dBooking_date = $value['dBooking_date'];
				$vSourceAddresss = $value['vSourceAddresss'];
				$tDestAddress = $value['tDestAddress'];
				$iVehicleTypeId = $value['iVehicleTypeId'];
				$vPhone = $value['vPhone'];
				$vName = $value['vName'];
				$vLastName = $value['vLastName'];
				$vEmail = $value['vEmail'];
				$vPhoneCode = $value['vPhoneCode'];
				$vCountry = $value['vCountry'];
				$eAutoAssign = $value['eAutoAssign'];
				$from_lat_long = '('.$value['vSourceLatitude'].', '.$value['vSourceLongitude'].')';
				$from_lat = $value['vSourceLatitude'];
				$from_long = $value['vSourceLongitude'];
				$to_lat_long = '('.$value['vDestLatitude'].', '.$value['vDestLongitude'].')';
				$to_lat = $value['vDestLatitude'];
				$to_long = $value['vDestLongitude'];
				#$vCurrencyDriver=$value['vCurrencyDriver'];
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<!-- <title><?=$COMPANY_NAME?>| Signup</title>-->
		<title><?php  echo $meta_arr['meta_title'];?></title>
		<!-- Default Top Script and css -->
		<?php  include_once("top/top_script.php");?>
		<link href="assets/css/checkbox.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/radio.css" rel="stylesheet" type="text/css" />
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=en&key=<?=$GOOGLE_SEVER_API_KEY_WEB?>"></script>
		<?php  include_once("top/validation.php");?>
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
				<div class="page-contant-inner">
					<h2 class="header-page trip-detail"><?=$langage_lbl['LBL_MANUAL']; ?>  <?php  echo $langage_lbl_admin['LBL_TEXI_ADMIN'];?> <?=$langage_lbl['LBL_DISPATCH']; ?>
						<a href="booking.php">
							<img src="assets/img/arrow-white.png" alt=""> <?=$langage_lbl['LBL_BACK_ To_ Listing']; ?>	
						</a>
					</h2>
					<!-- trips detail page -->
					<div class="manual-dispatch">
						<a class="btn btn-primary how_it_work_btn" data-toggle="modal" data-target="#myModal"><i class="fa fa-question-circle" style="font-size: 18px;"></i> <?=$langage_lbl['LBL_DIS_HOW_IT_WORKS']; ?></a>
						
						<div class="modal fade manual-dispatch-popup" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-large">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
										<h4 class="modal-title" id="myModalLabel"> <?=$langage_lbl['LBL_DIS_HOW_IT_WORKS']; ?></h4>
									</div>
									<div class="modal-body">
										<p><b>Flow </b>: Through "Manual Taxi Dispatch" Feature, you can book Rides for customers who ordered for a Ride by calling you. There will be customers who may not have iPhone or Android Phone or may not have app installed on their phone. In this case, they will call Taxi Company (your company) and order Ride which may be needed immediately or after some time later.</p>
										<p>Here, you will fill their info in the form and dispatch book a taxi ride for him.</p>
										<p>The Driver will receive info on his App and will pickup the rider at the scheduled time.</p>
										<p>- If the customer is already registered with us, just enter his phone number and his info will be fetched from the database when "Get Details" button is clicked. Else fill the form.</p>
										<p>- Once the Trip detail is added, Fare estimate will be calculated based on Pick-Up Location, Drop-Off Location and Car Type.</p>
										<p>- Admin will need to communicate & confirm with Driver and then select him as Driver so the Ride can be allotted to him. </p>
										<p>- Clicking on "Book" Button, the Booking detail will be saved and will take Administrator to the "Ride Later Booking" Section. This page will show all such bookings.</p>
										<p>- The assigned Driver can see the upcoming Bookings from his App under "My Bookings" section.</p>
										<p>- Driver will have option to "Start Trip" when he reaches the Pickup Location at scheduled time or "Cancel Trip" if he cannot take the ride for some reason. If the Driver clicks on "Cancel Trip", a notification will be sent to Administrator so he can make alternate arrangements.</p>
										<p>- Upon clicking on "Start Trip", the ride will start in driver's App in regular way.</p>
										<p><span><img src="<?php  echo $tconfig["tsite_url_main_admin"]?>images/mobile_app_booking.png"></img></span></p>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>
						
                        <div class="form-group">
                            <?php  if ($success == "1") {?>
								<div class="alert alert-success alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">�</button>
									<?php 
										if ($vassign != "1") {
										?>
										Booking Has Been Added Successfully.
										<?php  } else {
										?>
										Driver Has Been Assigned Successfully.
									<?php  } ?>
									
								</div><br/>
							<?php  } ?>
							
                            <?php  if ($success == 2) {?>
								<div class="alert alert-danger alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">�</button>
									"Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
								</div><br/>
							<?php  } ?>
							<?php  if ($success == 0 && $var_msg != "") {?>
								<div class="alert alert-danger alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">�</button>
									<?=$var_msg;?>
								</div><br/>
							<?php  } ?>
                            <div class="col-lg-5">
                                <form name="add_booking_form" id="add_booking_form" method="post" action="action_booking.php" enctype="multipart/form-data">
									<input type="hidden" name="iCompanyId" id="iCompanyId" value="<?= $_SESSION['sess_iUserId']; ?>">
                                    <input type="hidden" name="distance" id="distance" value="<?= $vDistance; ?>">
                                    <input type="hidden" name="duration" id="duration" value="<?= $vDuration; ?>">
									<input type="hidden" name="from_lat_long" id="from_lat_long" value="<?= $from_lat_long; ?>" >
                                    <input type="hidden" name="from_lat" id="from_lat" value="<?= $from_lat; ?>" >
                                    <input type="hidden" name="from_long" id="from_long" value="<?= $from_long; ?>" >
                                    <input type="hidden" name="to_lat_long" id="to_lat_long" value="<?= $to_lat_long; ?>" >
                                    <input type="hidden" name="to_lat" id="to_lat" value="<?= $to_lat; ?>" >
                                    <input type="hidden" name="to_long" id="to_long" value="<?= $to_long; ?>" >
                                    <input type="hidden" value="1" id="location_found" name="location_found">
                                    <input type="hidden" value="" id="user_type" name="user_type" >
                                    <input type="hidden" value="<?= $iUserId; ?>" id="iUserId" name="iUserId" >
									<input type="hidden" value="<?= $iCabBookingId; ?>" id="iCabBookingId" name="iCabBookingId" >
									
									<div class="add-booking-form">
										<span>
											<select name="vCountry" class="form-control form-control-select" onChange="changeCode(this.value); " required>
												<option value=""><?=$langage_lbl['LBL_SELECT_CONTRY']; ?></option>
												<?php  for($i=0;$i<count($db_code);$i++) { ?>
													<option value="<?=$db_code[$i]['vCountryCode']?>" <?php  if($db_code[$i]['vCountryCode'] == $vCountry){ echo "selected"; }?> >
														<?=$db_code[$i]['vCountry']?>
													</option>
												<?php  } ?>
											</select>
										</span>
										<span>
											<input type="text"  name="vPhoneCode" readonly  class="form-control form-control14" placeholder="Code" id="code" value="<?=$vPhoneCode; ?>" />
											<input type="text" maxlength="12"  title="Please enter 10 digit mobile number." class="form-control add-book-input" name="vPhone"  id="vPhone" value="<?= $vPhone; ?>" onKeyPress="return isNumberKey(event)" placeholder="Enter Phone Number" required style="">
											<a class="btn btn-sm btn-info" id="get_details" ><?=$langage_lbl['LBL_GET_DETAILS']; ?></a>
										</span>
										
										<span> <input type="text" pattern="[a-zA-Z]+" title="Only Alpha character allow" class="form-control first-name1" name="vName"  id="vName" value="<?= $vName; ?>" placeholder="First Name" required>  <input type="text" pattern="[a-zA-Z]+" title="Only Alpha character allow" class="form-control last-name1" name="vLastName"  id="vLastName" value="<?= $vLastName; ?>" placeholder="Last Name" required></span>
										<span><input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" class="form-control" name="vEmail" onChange="validate_email(this.value)"  id="vEmail" value="<?= $vEmail; ?>" placeholder="Email" required >
										<div id="emailCheck"></div></span>
										<span> <input type="text" class="ride-location1 highalert txt_active form-control first-name1" name="vSourceAddresss"  id="from" value="<?= $vSourceAddresss; ?>" placeholder="Pickup Location" required><input type="text" class="ride-location1 highalert txt_active form-control last-name1" name="tDestAddress"  id="to" value="<?= $tDestAddress; ?>" placeholder="Drop Off Location" required></span>
										
										<span> <input type="text" class=" form-control" name="dBooking_date"  id="datetimepicker4" value="<?= $dBooking_date; ?>" placeholder="Select Date / Time" required readonly></span>
										
										
										<span class="auto_assign001">
										<input type="checkbox" name="eAutoAssign" id="eAutoAssign" value="Yes" <?php  if($eAutoAssign == 'Yes') echo 'checked'; ?>> <b><?=$langage_lbl['LBL_AUTO_ASSIGNED']; ?> <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></b>
										</span>
										<span class="auto_assignOr">
											<h4><b><?=$langage_lbl['LBL_OR']; ?></b></h4>
										</span>
										<?php  if(!empty($db_records_online)) { ?>
											<span class="col-lg-6 vehicle-type2">
												<select class="form-control form-control-select" name='iDriverId' id="iDriverId" required onChange="shoeDriverDetail002(this.value);" >
													<option value="" >Select <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></option>
													<?php  foreach ($db_records_online as $db_online) { ?>
														<option value="<?php  echo $db_online['iDriverId']; ?>"><?php  echo $db_online['vName'].' '.$db_online['vLastName']; ?></option>
													<?php  } ?>
												</select>
											</span>											
											<?php  }else { ?>
											<div class="row show_drivers_lists">
												<div class="col-lg-6">
													<h5><?=$langage_lbl['LBL_NO_DRIVERS_FOUND']; ?></h5>
												</div>
											</div>
										<?php  } ?>										
										<span class="vehicle-type1">
											<select class="form-control form-control-select" name='iVehicleTypeId' id="iVehicleTypeId" required onChange="getFarevalues(this.value)">
												<option value="" >Select <?php  echo $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?></option>
												<?php  foreach ($db_carType as $db_car) { ?>
													<option value="<?php  echo $db_car['iVehicleTypeId']; ?>" <?php  if($iVehicleTypeId == $db_car['iVehicleTypeId']){ echo "selected"; } ?> ><?php  echo $db_car['vVehicleType']; ?></option>
												<?php  } ?>
											</select>
										</span>
										<span class="col-lg-6" id="showDriver003"></span>
										<span> 
											<input type="submit" class="save btn-info button-submit" name="submit" id="submit" value="Book" >
											<input type="reset" class="save btn-info button-submit" name="reset" id="reset12" value="Reset" >
										</span>     
									</div>
								</form>
                                <div class="total-price">
									<ul>
										<li><b><?=$langage_lbl['LBL_MINIMUM_FARE']; ?></b> : $ <em id="minimum_fare_price">0</em></li>
										<li><b><?=$langage_lbl['LBL_BASE_FARE_SMALL_TXT']; ?></b> : $ <em id="base_fare_price">0</em></li>
										<li><b><?=$langage_lbl['LBL_DISTANCE_TXT']; ?> (<em id="dist_fare">0</em> KMs)</b> : $ <em id="dist_fare_price">0</em></li>
										<li><b><?=$langage_lbl['LBL_RIDER_TIME_TXT']; ?> (<em id="time_fare">0</em> Minutes)</b> : $ <em id="time_fare_price">0</em></li>
									</ul>
									<span><?=$langage_lbl['LBL_Total_Fare']; ?><b>$ <em id="total_fare_price">0</em></b></span>
								</div>
							</div>
                            <div class="col-lg-7">
                                <div class="gmap-div gmap-div1" style="height:665px;"><div id="map-canvas" class="gmap3" style="height:665px;"></div></div>
							</div>
						</div>
					</div>
					<!-- -->
					<div style="clear:both;"></div>            
				</div>
			</div>
			<!-- footer part -->
			<?php  include_once('footer/footer_home.php');?>
			<!-- footer part end -->
			<!-- -->
			<div style="clear:both;"></div>
		</div>
		<!-- home page end-->
		<!-- Footer Script -->
		<?php  include_once('top/footer_script.php');?>
		<link rel="stylesheet" type="text/css" media="screen" href="<?php  echo $tconfig["tsite_url_main_admin"]?>css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
		<script type="text/javascript" src="<?php  echo $tconfig["tsite_url_main_admin"]?>js/moment.min.js"></script>
		<script type="text/javascript" src="<?php  echo $tconfig["tsite_url_main_admin"]?>js/bootstrap-datetimepicker.min.js"></script>
        <script>
			
			$('.gallery').each(function() { // the containers for all your galleries
				$(this).magnificPopup({
					delegate: 'a', // the selector for gallery item
					type: 'image',
					gallery: {
						enabled:true
					}
				});
			});
			
			$(function () {
				newDate = new Date('Y-M-D');
                $('#datetimepicker4').datetimepicker({
					format: 'YYYY-MM-DD HH:mm:ss',
					minDate: moment().format('l'),
					ignoreReadonly: true,
					sideBySide: true,
				});
			});
            function getFarevalues(vehicleId) {
                $.ajax({
                    type: "POST",
                    url: '<?php  echo $tconfig["tsite_url_main_admin"]?>ajax_find_rider_by_number.php',
                    data: 'vehicleId=' + vehicleId,
                    success: function (dataHtml)
                    {
						console.log(dataHtml);
                        if (dataHtml != "") {
                            var result = dataHtml.split(':');
                            $('#minimum_fare_price').text(parseFloat(result[3]).toFixed(2));
                            $('#base_fare_price').text(parseFloat(result[0]).toFixed(2));
                            $('#dist_fare_price').text(parseFloat(result[1]*$('#dist_fare').text()).toFixed(2));
                            $('#time_fare_price').text(parseFloat(result[2]*$('#time_fare').text()).toFixed(2));
							var totalPrice = (parseFloat($('#base_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat($('#time_fare_price').text())).toFixed(2);
							if(parseInt(totalPrice) >= parseInt($('#minimum_fare_price').text())) {
								$('#total_fare_price').text(totalPrice);
								}else {
								$('#total_fare_price').text($('#minimum_fare_price').text());
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
			}
            
            $('#get_details').on('click', function () {
                var phone = $('#vPhone').val();
                $.ajax({
                    type: "POST",
                    url: '<?php  echo $tconfig["tsite_url_main_admin"]?>ajax_find_rider_by_number.php',
                    data: 'phone=' + phone,
                    success: function (dataHtml)
                    {
                        if (dataHtml != "") {
                            $("#user_type").val('registered');
                            var result = dataHtml.split(':');
                            $('#vName').val(result[0]);
                            $('#vLastName').val(result[1]);
                            $('#vEmail').val(result[2]);
							$('#iUserId').val(result[3]);
							}else {
                            $("#user_type").val('');
                            $('#vName').val('');
                            $('#vLastName').val('');
                            $('#vEmail').val('');
							$('#iUserId').val('');
						}
					}
				});
			});
			
            var map;
			var geocoder;
			var autocomplete_from;
			var autocomplete_to;
            function initialize() {
                geocoder = new google.maps.Geocoder();
                var mapOptions = {
                    zoom: 4,
                    center: new google.maps.LatLng('20.1849963', '64.4125062')
				};
                map = new google.maps.Map(document.getElementById('map-canvas'),
				mapOptions);
				<?php  if($action == "Edit") { ?>
					callEditFundtion();
				<?php  } ?>
			}
			
            $(document).ready(function () {
                google.maps.event.addDomListener(window, 'load', initialize);
			});
			
			
            $(function () {
				
                var from = document.getElementById('from');
				autocomplete_from = new google.maps.places.Autocomplete(from);
				google.maps.event.addListener(autocomplete_from, 'place_changed', function() {
					var place = autocomplete_from.getPlace();
					//console.log(autocomplete_from.);
					$("#from_lat_long").val(place.geometry.location);
					$("#from_lat").val(place.geometry.location.lat());
					$("#from_long").val(place.geometry.location.lng());
					go_for_action();
				});
				
				var to = document.getElementById('to');
				autocomplete_to = new google.maps.places.Autocomplete(to);
				google.maps.event.addListener(autocomplete_to, 'place_changed', function() {
					var place = autocomplete_to.getPlace();
					$("#to_lat_long").val(place.geometry.location);
					$("#to_lat").val(place.geometry.location.lat());
					$("#to_long").val(place.geometry.location.lng());
					go_for_action();
				});
				
                function go_for_action() {
                    if ($("#from").val() != '' && $("#to").val() == '') {
                        show_location($("#from").val());
					}
					
                    if ($("#to").val() != '' && $("#from").val() == '') {
                        show_location($("#to").val());
					}
					
                    if ($("#from").val() != '' && $("#to").val() != '') {
                        from_to($("#from").val(), $("#to").val());
					}
				}
			});
			
		</script>
        <script type="text/javascript" src="<?php  echo $tconfig["tsite_url_main_admin"]?>js/gmap3.js"></script>
        <script type="text/javascript">
            var chk_route;
            function show_location(address) {
                //alert("show_location");
                clearThat();
                $('#map-canvas').gmap3({
                    marker: {
                        address: address
					},
                    map: {
                        options: {
                            zoom: 8
						}
					}
				});
			}
			
            function clearThat() {
                var opts = {};
                opts.name = ["marker", "directionsrenderer"];
                opts.first = true;
                $('#map-canvas').gmap3({clear: opts});
			}
			
            function from_to(from, to) {
				
                clearThat();
                if (from == '')
				from = $('#from').val();
				
                if (to == '')
				to = $('#to').val();
                //alert("from_to" + from +"   to "+to);
                $("#from_lat_long").val('');
                $("#to_lat_long").val('');
				
                var chks = document.getElementsByName('loc');
                if(from != ''){
					geocoder.geocode( { 'address': from}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
								$("#from_lat_long").val((results[0].geometry.location));
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
								} else {
								alert("No results found");
							}
							} else {
							var place20 = autocomplete_to.getPlace();
							$("#to_lat_long").val(place20.geometry.location);
						}
					});
				}
				
				var fromLatlongs = $("#from_lat").val()+", "+$("#from_long").val();
				var toLatlongs = $("#to_lat").val()+", "+$("#to_long").val();
				
                $("#map-canvas").gmap3({
                    getroute: {
                        options: {
                            origin: fromLatlongs,
                            destination: toLatlongs,
                            travelMode: google.maps.DirectionsTravelMode.DRIVING
						},
                        callback: function (results, status) {
                            chk_route = status;
                            if (!results)
							return;
                            $(this).gmap3({
                                map: {
                                    options: {
                                        zoom: 8,
                                        //center: [51.511214, -0.119824]
                                        center: [58.0000, 20.0000]
									}
								},
                                directionsrenderer: {
                                    options: {
                                        directions: results
									}
								}
							});
						}
					}
				});
				
                $("#map-canvas").gmap3({
                    getdistance: {
                        options: {
                            origins: fromLatlongs,
                            destinations: toLatlongs,
                            travelMode: google.maps.TravelMode.DRIVING
						},
                        callback: function (results, status) {
                            var html = "";
                            if (results) {
                                for (var i = 0; i < results.rows.length; i++) {
                                    var elements = results.rows[i].elements;
                                    for (var j = 0; j < elements.length; j++) {
                                        switch (elements[j].status) {
                                            case "OK":
											html += elements[j].distance.text + " (" + elements[j].duration.text + ")<br />";
											document.getElementById("distance").value = elements[j].distance.value;
											document.getElementById("duration").value = elements[j].duration.value;
											var dist_fare = parseInt(elements[j].distance.value, 10) / parseInt(1000, 10);
											$('#dist_fare').text(Math.round(dist_fare));
											var time_fare = parseInt(elements[j].duration.value, 10) / parseInt(60, 10);
											$('#time_fare').text(Math.round(time_fare));
											var vehicleId = $('#iVehicleTypeId').val();
											$.ajax({
												type: "POST",
												url: '<?php  echo $tconfig["tsite_url_main_admin"]?>ajax_find_rider_by_number.php',
												data: 'vehicleId=' + vehicleId,
												success: function (dataHtml)
												{
													if (dataHtml != "") {
														var result = dataHtml.split(':');
														$('#minimum_fare_price').text(parseFloat(result[3]).toFixed(2));
														$('#base_fare_price').text(parseFloat(result[0]).toFixed(2));
														$('#dist_fare_price').text(parseFloat(result[1]*$('#dist_fare').text()).toFixed(2));
														$('#time_fare_price').text(parseFloat(result[2]*$('#time_fare').text()).toFixed(2));
														var totalPrice = (parseFloat($('#base_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat($('#time_fare_price').text())).toFixed(2);
														if(parseInt(totalPrice) >= parseInt($('#minimum_fare_price').text())) {
															$('#total_fare_price').text(totalPrice);
															}else {
															$('#total_fare_price').text($('#minimum_fare_price').text());
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
											document.getElementById("location_found").value = 1;
											break;
                                            case "NOT_FOUND":
											document.getElementById("location_found").value = 0;
											break;
                                            case "ZERO_RESULTS":
											document.getElementById("location_found").value = 0;
											break;
										}
									}
								}
								} else {
                                html = "error";
							}
                            $("#results").html(html);
						}
					}
				});
			}
			
			function callEditFundtion() {
				var from = $('#from').val();
				var to = $('#to').val();
				
				if(from != ''){
					geocoder.geocode( { 'address': from}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
								$("#from_lat_long").val((results[0].geometry.location));
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
								} else {
								alert("No results found");
							}
							} else {
							var place20 = autocomplete_to.getPlace();
							$("#to_lat_long").val(place20.geometry.location);
						}
					});
				}
				
				var fromLatlongs = $("#from_lat").val()+", "+$("#from_long").val();
				var toLatlongs = $("#to_lat").val()+", "+$("#to_long").val();
				//alert(fromLatlongs+toLatlongs);
				$("#map-canvas").gmap3({
					getroute: {
						options: {
							origin: fromLatlongs,
							destination: toLatlongs,
							travelMode: google.maps.DirectionsTravelMode.DRIVING
						},
						callback: function (results, status) {
							chk_route = status;
							if (!results)
							return;
							$(this).gmap3({
								map: {
									options: {
										zoom: 8,
										//       center: [51.511214, -0.119824]
										center: [58.0000, 20.0000]
									}
								},
								directionsrenderer: {
									options: {
										directions: results
									}
								}
							});
						}
					}
				});
				
				$("#map-canvas").gmap3({
					getdistance: {
						options: {
							origins: fromLatlongs,
							destinations: toLatlongs,
							travelMode: google.maps.TravelMode.DRIVING
						},
						callback: function (results, status) {
							var html = "";
							if (results) {
								for (var i = 0; i < results.rows.length; i++) {
									var elements = results.rows[i].elements;
									for (var j = 0; j < elements.length; j++) {
										switch (elements[j].status) {
											case "OK":
											html += elements[j].distance.text + " (" + elements[j].duration.text + ")<br />";
											document.getElementById("distance").value = elements[j].distance.value;
											document.getElementById("duration").value = elements[j].duration.value;
											var dist_fare = parseInt(elements[j].distance.value, 10) / parseInt(1000, 10);
											$('#dist_fare').text(Math.round(dist_fare));
											var time_fare = parseInt(elements[j].duration.value, 10) / parseInt(60, 10);
											$('#time_fare').text(Math.round(time_fare));
											var vehicleId = $('#iVehicleTypeId').val();
											$.ajax({
												type: "POST",
												url: '<?php  echo $tconfig["tsite_url_main_admin"]?>ajax_find_rider_by_number.php',
												data: 'vehicleId=' + vehicleId,
												success: function (dataHtml)
												{
													if (dataHtml != "") {
														var result = dataHtml.split(':');
														$('#minimum_fare_price').text(parseFloat(result[3]).toFixed(2));
														$('#base_fare_price').text(parseFloat(result[0]).toFixed(2));
														$('#dist_fare_price').text(parseFloat(result[1]*$('#dist_fare').text()).toFixed(2));
														$('#time_fare_price').text(parseFloat(result[2]*$('#time_fare').text()).toFixed(2));
														var totalPrice = (parseFloat($('#base_fare_price').text())+parseFloat($('#dist_fare_price').text())+parseFloat($('#time_fare_price').text())).toFixed(2);
														if(parseInt(totalPrice) >= parseInt($('#minimum_fare_price').text())) {
															$('#total_fare_price').text(totalPrice);
															}else {
															$('#total_fare_price').text($('#minimum_fare_price').text());
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
											document.getElementById("location_found").value = 1;
											break;
											case "NOT_FOUND":
											document.getElementById("location_found").value = 0;
											break;
											case "ZERO_RESULTS":
											document.getElementById("location_found").value = 0;
											break;
										}
									}
								}
								} else {
								html = "error";
							}
							$("#results").html(html);
						}
					}
				});
			}
			
		</script>
        <script src="assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
        <script>
            function changeCode(id)
            {
                var request = $.ajax({
                    type: "POST",
                    url: 'change_code.php',
                    data: 'id=' + id,
                    success: function (data)
                    {
                        document.getElementById("code").value = data;
					}
				});
			}
            function validate_email(id)
            {
				
                var request = $.ajax({
                    type: "POST",
                    url: 'validate_email.php',
                    data: 'id=' + id,
                    success: function (data)
                    {
                        if (data == 0)
                        {
                            $('#emailCheck').html('<i class="icon icon-remove alert-danger alert">Already Exist,Select Another</i>');
							setTimeout(function() {
								$('#emailCheck').html('');
							}, 3000);
                            $('input[type="submit"]').attr('disabled', 'disabled');
						} else if (data == 1)
                        {
                            var eml = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                            result = eml.test(id);
                            if (result == true)
                            {
								$('#emailCheck').html('<i class="icon icon-ok alert-success alert"> Valid</i>');
								setTimeout(function() {
									$('#emailCheck').html('');
								}, 3000);
                                $('input[type="submit"]').removeAttr('disabled');
							} else
                            {
                                $('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> Enter Proper Email</i>');
								setTimeout(function() {
									$('#emailCheck').html('');
								}, 3000);
                                $('input[type="submit"]').attr('disabled', 'disabled');
							}
						}
					}
				});
			}
			
			function showhideDrivers(dtype) {
				if(dtype == "manual") {
					$('.show_drivers_lists').slideDown();
					$('select[name="iDriverId"]').attr('required','required');
					}else {
					$('.show_drivers_lists').slideUp();
					$('select[name="iDriverId"]').removeAttr('required');
				}
			}
			
			function isNumberKey(evt){
				var charCode = (evt.which) ? evt.which : evt.keyCode
				if (charCode > 31 && (charCode < 35 || charCode > 57))
				return false;
				return true;
			}
			
			$("#reset12").on('click',function(){
				$('#dist_fare').text('0');
				$('#time_fare').text('0');
				$('#minimum_fare_price').text('0');
				$('#base_fare_price').text('0');
				$('#dist_fare_price').text('0');
				$('#time_fare_price').text('0');
				$('#total_fare_price').text('0');
			});
			function shoeDriverDetail002(id) {
				if(id != "") {
					var request2 = $.ajax({
						type: "POST",
						url: 'show_driver.php',
						dataType: 'html',
						data: 'id=' + id,
						success: function (data)
						{
							$("#showDriver003").html(data);
							}, error: function(data) {
							
						}
					});
					}else {
					$("#showDriver003").html('');
				}
			}
			
			$("#eAutoAssign").on('change', function(){
				if($(this).prop('checked')) {
					$("#iDriverId").val('');
					$("#iDriverId").attr('disabled','disabled');
				}else {
					$("#iDriverId").removeAttr('disabled');
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
			
		</script>
		<!-- End: Footer Script -->
	</body>
</html>
