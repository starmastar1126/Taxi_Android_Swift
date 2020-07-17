<script type="text/javascript" src="assets/js/amazingcarousel.js"></script>
<script type="text/javascript" src="assets/js/initcarousel.js"></script>

<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places&language=en&key=<?=$GOOGLE_SEVER_API_KEY_WEB?>"></script>
<!-- Second Section -->
        <div class="home-hero-page">
            <div class="home-hero-page-left" style="background: rgba(0, 0, 0, 0) url('<?=$tconfig["tsite_upload_page_images"].$homepage_banner["vImage"];?>') no-repeat scroll center top / cover ">
                <div class="home-hero-page-left-text"> 
                    <span>
                        <a href="sign-up"><?php  echo $langage_lbl['LBL_HOME_SIGN_UP'];?></a>
                        <p><?php  echo $langage_lbl['LBL_HOME_DRIVER_COMPANY_TXT'];?></p>
                    </span> 
                </div>
            </div>
            <div class="home-hero-page-right">
                <div class="home-hero-page-right-text">
                    <span>
                        <p><?=$langage_lbl['LBL_HOME_RIDING_TXT']; ?></p>
                        <a href="sign-up-rider"><?=$langage_lbl['LBL_HOME_SIGN_UP']; ?></a>
                    </span>
                </div>
            </div>
        </div>
        <!-- End: Second Section -->
        <!-- Third Section -->
        <div class="home-sldier">
            <div class="home-sldier-inner">
			<div id="amazingcarousel-container-7">
			<div id="amazingcarousel-7">
			<div class="amazingcarousel-list-container">
                <ul class="amazingcarousel-list">					
					<?php 
					
						$sql = "SELECT * FROM home_screens WHERE eStatus ='Active' ORDER BY iDescOrd ASC";
						$db_data = $obj->MySQLSelect($sql);
						
						foreach($db_data as $image_detail){
						
							if(!empty($image_detail['vImageName'])){
								$filename1 = $tconfig['tsite_upload_apppage_images'].$image_detail['vImageName'];							
								if (file_exists($filename1)) {								
									
									?>
								 
									 <li class="amazingcarousel-item">
										<div class="amazingcarousel-item-container">
										<div class="amazingcarousel-image"><img src="<?php  echo $tconfig['tsite_upload_apppage_images_panel'].$image_detail['vImageName'];?>" alt="<?=$image_detail['vImageName'];?>" /></div>
										</div>
									</li>
								 
								<?php  }	
							}	
							
						} 
					?>	
					<!--<li class="amazingcarousel-item">
						<div class="amazingcarousel-item-container">
						<div class="amazingcarousel-image"><img src="assets/img/page/<?php  echo $image1['vImage']?>"  alt="<?=$image1['page_title']?>" /></div>
						</div>
					</li>
					<li class="amazingcarousel-item">
						<div class="amazingcarousel-item-container">
						<div class="amazingcarousel-image"><img src="assets/img/page/<?php  echo $image2['vImage']?>"  alt="<?=$image2['page_title']?>" /></div>
						</div>
					</li>
					<li class="amazingcarousel-item">
						<div class="amazingcarousel-item-container">
						<div class="amazingcarousel-image"><img src="assets/img/page/<?php  echo $image3['vImage']?>"  alt="<?=$image3['page_title']?>" /></div>
						</div>
					</li>
					<li class="amazingcarousel-item">
						<div class="amazingcarousel-item-container">
						<div class="amazingcarousel-image"><img src="assets/img/page/<?php  echo $image4['vImage']?>"  alt="<?=$image4['page_title']?>" /></div>
						</div>
					</li>
					<li class="amazingcarousel-item">
						<div class="amazingcarousel-item-container">
						<div class="amazingcarousel-image"><img src="assets/img/page/<?php  echo $image1['vImage']?>"  alt="<?=$image1['page_title']?>" /></div>
						</div>
					</li>
					<li class="amazingcarousel-item">
						<div class="amazingcarousel-item-container">
						<div class="amazingcarousel-image"><img src="assets/img/page/<?php  echo $image2['vImage']?>"  alt="<?=$image2['page_title']?>" /></div>
						</div>
					</li>
					<li class="amazingcarousel-item">
						<div class="amazingcarousel-item-container">
						<div class="amazingcarousel-image"><img src="assets/img/page/<?php  echo $image3['vImage']?>"  alt="<?=$image3['page_title']?>" /></div>
						</div>
					</li>
					<li class="amazingcarousel-item">
						<div class="amazingcarousel-item-container">
						<div class="amazingcarousel-image"><img src="assets/img/page/<?php  echo $image4['vImage']?>"  alt="<?=$image4['page_title']?>" /></div>
						</div>
					</li> -->
                </ul>
				<div class="amazingcarousel-prev"></div>
				<div class="amazingcarousel-next"></div>
			</div>
			</div>
			</div>
		</div>
        </div>
		
        <div class="tap-app-ride">
            <div class="tap-app-ride-inner">
                <h2><?php  echo $meta1['page_title'];?></h2>
                <p><?php  echo $meta1['page_desc'];?></p>
                <div style="clear:both;"></div>
            </div>
        </div>
        <!-- End: Third Section -->
        <!-- Forth Section -->
        <div class="people-going-way">
            <!--<div class="people-going-way-left" style="background : rgba(0, 0, 0, 0) url(<?php  echo $tconfig['tsite_upload_page_images'].$meta2['vImage']?>) no-repeat scroll center top / cover ">&nbsp;</div>-->
            <div class="people-going-way-mid">
                <div class="people-going-way-mid-inner">
                    <h3><?php  echo $meta2['page_title'];?></h3>
                    <?php  echo $meta2['page_desc'];?>
                    <div style="clear:both;"></div>
                </div>
            </div>
            <div class="people-going-way-right" style="background : rgba(0, 0, 0, 0) url('<?php  echo $tconfig['tsite_upload_page_images'].$meta2['vImage']?>') no-repeat scroll center top / cover">&nbsp;</div>
        </div>
        <!-- End: Forth Section -->
        <!-- Fifth Section -->
        <div class="helping-cities">
            <div class="helping-cities-left" style="background : rgba(0, 0, 0, 0) url('<?php  echo $tconfig['tsite_upload_page_images'].$meta3['vImage']?>') no-repeat scroll center top / cover">&nbsp;</div>
            <div class="helping-cities-mid">
                <div class="helping-cities-mid-inner">
                    <h3><?php  echo $meta3['page_title'];?></h3>
                    <?php  echo $meta3['page_desc'];?>
                    <div style="clear:both;"></div>
                </div>
            </div>
           <!-- <div class="helping-cities-right" style="background : rgba(0, 0, 0, 0) url(<?php  echo $tconfig['tsite_upload_page_images'].$meta3['vImage1']?>) no-repeat scroll center top / cover">&nbsp;</div>-->
        </div>
        <!-- End: Fifth Section -->
        <!-- Sixth Section -->
        <div class="Safety-people">
            <div class="Safety-people-left">&nbsp;</div>
            <div class="Safety-people-right">
                <div class="Safety-people-right-inner">
                    <div class="Safety-people-text">
                        <h3><?php  echo $meta4['page_title'];?></h3>
                    <?php  echo $meta4['page_desc'];?>
                    </div>
                    <div class="Safety-people-img"><img src="<?php  echo $tconfig['tsite_upload_page_images'].$meta4['vImage']?>" alt=""></div>
                    <div style="clear:both;"></div>
                </div>
            </div>
        </div>
        <!-- End: Sixth Section -->
		
<!--------------------------------------------------------------------->
<!-- -->
<div class="home-map"></div>
<!-- -->


<!-------------------------------------------------------------->
<!-- -->
<div class="get-fare-estimation">
	<div class="get-fare-estimation-inner">
    <div class="get-free1">
		<div class="get-fare-estimation-left ">
			<h3><?=$langage_lbl['LBL_GET_FARE_ESTIMATION_TXT']; ?></h3>
			<span>
			<form name="_fare_estimate_form" id="_fare_estimate_form" method="post" >
				<input type="hidden" name="distance" id="distance" value="">
				<input type="hidden" name="duration" id="duration" value="">
				<input type="hidden" name="from_lat_long" id="from_lat_long" value="" >
				<input type="hidden" name="from_lat" id="from_lat" value="" >
				<input type="hidden" name="from_long" id="from_long" value="" >
				<input type="hidden" name="to_lat_long" id="to_lat_long" value="" >
				<input type="hidden" name="to_lat" id="to_lat" value="" >
				<input type="hidden" name="to_long" id="to_long" value="" >
				<input type="hidden" name="location_found" id="location_found" value="" >
				<b><input name="vPickup" type="text" id="from" placeholder="<?=$langage_lbl['LBL_HOME_ADD_PICKUP_LOC']; ?>" class="trip-start" /></b>
				<b><input name="vDest" type="text" id="to" placeholder="<?=$langage_lbl['LBL_ADD_DESTINATION_LOCATION_TXT']; ?>" class="trip-end" /><button type="button"><i aria-hidden="true" class="fa fa-arrow-right"></i></button></b>
			</form>
			</span>
			<div style="display:" id="setEstimate_figure"></div>
            <!-- <a href="#"><em>calculate</em></a>-->
		</div>
        <div class="home-page-map" id="map-canvas"></div>
        </div>
		<div style="clear:both;"></div>
	</div>
</div>
<!-- -->
<!-------------------------------------------------------------->
<script>
	var map;
	var geocoder;
	var autocomplete_from;
	var autocomplete_to;
	function initialize() {
		geocoder = new google.maps.Geocoder();
		var mapOptions = {
			zoom: 7,
			center: new google.maps.LatLng('-15.4168189', '28.2737126')
		};
		map = new google.maps.Map(document.getElementById('map-canvas'),
		mapOptions);
	}
	
	$(document).ready(function () {
		$("#setEstimate_figure").hide();
		google.maps.event.addDomListener(window, 'load', initialize);
	});
	
	
	$(function () {
		
		var from = document.getElementById('from');
		autocomplete_from = new google.maps.places.Autocomplete(from);
		google.maps.event.addListener(autocomplete_from, 'place_changed', function() {
			var place = autocomplete_from.getPlace();
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
				show_location($("#from_lat").val(),$("#from_long").val());
			}
			if ($("#to").val() != '' && $("#from").val() == '') {
				show_location($("#to_lat").val(),$("#to_long").val());
			}
			if ($("#from").val() != '' && $("#to").val() != '') {
				from_to($("#from").val(), $("#to").val());
			}
		}
	});
</script>
<script type="text/javascript" src="assets/js/gmap3.js"></script>
<script type="text/javascript">
	var chk_route;
	function show_location(set,dest) {
		//alert("show_location");
		clearThat();
		$('#map-canvas').gmap3({
			marker: {
				latLng:[set,dest]
			},
			map: {
				options: {
					zoom: 16
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
		if(from != '' && to != '') {
		
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
					$('.get-fare-estimation-left').addClass('new-dd001');
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
										url: 'ajax_find_estimate.php',
										data: {dist_fare: dist_fare,time_fare: time_fare },
										dataType: 'html',
										success: function (dataHtml)
										{
											$("#setEstimate_figure").show();
											$("#setEstimate_figure").html(dataHtml);
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
	}
</script>