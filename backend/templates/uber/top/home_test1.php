<script type="text/javascript" src="assets/js/amazingcarousel.js"></script>
<script type="text/javascript" src="assets/js/initcarousel.js"></script>
<!-- css -->
<link rel="stylesheet" type="text/css" href="assets/css/animate.css">
<?php  if($SITE_VERSION != 'v5') { ?> 
<!-- <link rel="stylesheet" type="text/css" href="assets/css/gallery.css"/> -->
<link rel="stylesheet" type="text/css" href="assets/css/gallery_v5.css"/>
<?php  } else { ?>
<link rel="stylesheet" type="text/css" href="assets/css/gallery_v5.css"/>
<?php  } ?>
<!-- js -->
<script type="text/javascript" src="assets/js/jquery-1.11.0.js"></script>
<script type="text/javascript" src="assets/js//waypoints.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=en&key=<?=$GOOGLE_SEVER_API_KEY_WEB?>"></script>
<script type="text/javascript">//<![CDATA[ 
	$(function(){
		function onScrollInit( items, trigger ) {
			items.each( function() {
                var osElement = $(this),
				osAnimationClass = osElement.attr('data-os-animation'),
				osAnimationDelay = osElement.attr('data-os-animation-delay');
				
				osElement.css({
					'-webkit-animation-delay':  osAnimationDelay,
					'-moz-animation-delay':     osAnimationDelay,
					'animation-delay':          osAnimationDelay
				});
				var osTrigger = ( trigger ) ? trigger : osElement;
				
				osTrigger.waypoint(function() {
					osElement.addClass('animated').addClass(osAnimationClass);
					},{
					triggerOnce: true,
					offset: '100%'
				});
			});
		}
		onScrollInit( $('.os-animation') );
		onScrollInit( $('.staggered-animation'), $('.staggered-animation-container') );
	});//]]>  
</script>
<!-- -->
<?php  
	$sql="select count('iDriverId') as Total from home_driver where eStatus='Active'";
	$count_driver = $obj->MySQLSelect($sql);
	
	if($count_driver[0]['Total'] > 4){
		$ssql = " order by rand()";	
	}else{
		$ssql = " order by iDisplayOrder";
	}
	$sql="select * from home_driver where eStatus='Active' $ssql limit 4";
	$db_home_drv=$obj->MySQLSelect($sql);
	//echo "<pre>";print_r($db_home_drv);exit;
	
	//for default country
	$sql = "SELECT vCountry from country where eStatus = 'Active' and vCountryCode = '$DEFAULT_COUNTRY_CODE_WEB'" ;
	$db_def_con = $obj->MySQLSelect($sql);
	
?>
<div class="home-hero-page">
	<div class="home-hero-page-left" style="background: rgba(0, 0, 0, 0) url('<?=$tconfig["tsite_upload_page_images"].$homepage_banner["vImage"];?>') no-repeat scroll center top / cover">
		<div class="home-hero-page-left-text"> <span> <?php  if(empty($_SESSION['sess_iUserId'])) { ?><a href="sign-up"><em><?php  echo $langage_lbl['LBL_HOME_SIGN_UP'];?></em></a><?php  } ?>
			<p><?php  echo $langage_lbl['LBL_HOME_DRIVER_COMPANY_TEXT'];?></p>
		</span> </div>
	</div>
	<div class="home-hero-page-right">
		<div class="home-hero-page-right-text"> <span>
			<p>
				<?=$langage_lbl['LBL_HOME_RIDING_TEXT']; ?>
			</p>
			<?php  if(empty($_SESSION['sess_iUserId'])) { ?>
			<a href="sign-up-rider"><em>
				<?=$langage_lbl['LBL_HOME_SIGN_UP']; ?>
			</em></a>
			<?php  } ?>
			 </span> </div>
	</div>
</div>
<!-- End: Second Section -->
<!-- Third Section -->
<div class="tap-app-ride">
	<div class="tap-app-ride-inner">
		<h2><?php  echo $meta1['page_title'];?></h2>
		<?php  echo $meta1['page_desc'];?>
		<div style="clear:both;"></div>
	</div>
</div>
<!-- End: Third Section -->
<div class="home-body-mid-part">
	<div class="home-body-mid-part-inner">
		<ul>
			<li>
				<div class="home-body-mid-img"><img <?php  if(file_exists($tconfig["tsite_upload_page_images_panel"].'/'.$meta2['vImage'])) { ?> src="<?php  echo $tconfig["tsite_upload_page_images"].$meta2['vImage']; ?>" <?php  } else { ?> src="assets/img/home-box1.jpg" <?php  } ?> alt="home1" /></div>
				<h3><?php  echo $meta2['page_title'];?><!-- Ever Ready, Anytime, Anywhere, Everyday --></h3>
				<?php  echo $meta2['page_desc'];?>
				<!-- <p>Wherever you want to go, irrespective of time, date and place, we are always ready. No waiting charges, no reservations, just our professionalism and courteous service.</p>
				<p>At your service 24/7, we take pride in what we do and it reflects in our driving. Building a relationship with clients that goes beyond professionalism.</p> -->
			</li>
			<li>
				<div class="home-body-mid-img"><img <?php  if(file_exists($tconfig["tsite_upload_page_images_panel"].'/'.$meta3['vImage'])) { ?> src="<?php  echo $tconfig["tsite_upload_page_images"].$meta3['vImage']; ?>" <?php  } else { ?> src="assets/img/home-box2.jpg" <?php  } ?> alt="home2" /></div>
				<h3><?php  echo $meta3['page_title'];?><!-- Luxury At Your Doorstep At Your Choice --></h3>
				<?php  echo $meta3['page_desc'];?>
				<!-- <p>Budgeted or luxury, we will have the ride of your choice. Go shopping in a mini and enjoy a limousine ride to your office party. Be spoilt for choice!</p>
				<p>Make your travelling experience a memorable one, we bring you the best on-demand cabbing experience. Pure comfort, luxury and style - the ultimate dream ride!</p> -->
			</li>
			<li>
				<div class="home-body-mid-img"><img <?php  if(file_exists($tconfig["tsite_upload_page_images_panel"].'/'.$meta4['vImage'])) { ?> src="<?php  echo $tconfig["tsite_upload_page_images"].$meta4['vImage']; ?>" <?php  } else { ?> src="assets/img/home-box3.jpg" <?php  } ?> alt="home3" /></div>
				<h3><?php  echo $meta4['page_title'];?><!-- Just For You, Just Like You --></h3>
				<?php  echo $meta4['page_desc'];?>
				<!-- <p>Ordinary people just like you and me are driving you places. Any city, big or small, any profession high paying or manual, whatever you do, wherever you are, can drive for us. From homemakers to students, from engineers to teachers, is all part of our driving family!</p> -->
			</li>
		</ul>
		<div style="clear:both;"></div>
	</div>
</div>
<!-- -->
<div class="home-mobile-app">
	<div class="home-mobile-app-inner">
		<div class="home-mobile-app-left os-animation" data-os-animation="fadeInLeft" data-os-animation-delay="0.2s">
		<?php if(APP_TYPE == "Ride-Delivery" || APP_TYPE == "Delivery"){?>
    <img src="assets/img/mobile-img-del.png" alt="">
    <?php }else{?>
    <img src="assets/img/mobile-img.png" alt="">
    <?php }?>
    </div>
		<div class="home-mobile-app-right os-animation" data-os-animation="fadeInRight" data-os-animation-delay="0.2s">
		<h3><?php  echo $meta5['page_title'];?><!-- Lorem Ipsum content<b>Ipsum content goes </b> --></h3>
		<?php  echo $meta5['page_desc'];?>
		<!-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic.</p> -->
		<!--<span><a href="about-us"><em><?php  echo $langage_lbl['LBL_MORE_INFO'];?></em></a></span>-->
	</div>
	
	
	<div style="clear:both;"></div>
</div>
</div>
<!-- -->

<?php if(APP_TYPE == "Ride"){?>
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
<?php }?>
<!-- -->
<div class="taxi-app">
	<div class="taxi-app1 <?php if(APP_TYPE == "Ride-Delivery" || APP_TYPE == "Delivery"){?>deliv<?php }?>">
		<div class="taxi-app-inner">
			<div class="taxi-app-right-part os-animation" data-os-animation="fadeInRight" data-os-animation-delay="0.2s">
				<h3><?php  echo $meta7['page_title'];?><!-- Lorem Ipsum content --></h3>
				<?php  echo $meta7['page_desc'];?>
				<!-- <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever, when an unknown printer took a galley of type and scrambled it to make a type specimen book. </p> 
				<span><a href="about-us"><em><?php  echo $langage_lbl['LBL_MORE_INFO'];?></em></a></span>-->
			</div>
            <div class="taxi-app-right-part-img-res"><img src="assets/img/taxi-app-img-res.jpg" alt=""></div>
			<div style="clear:both;"></div>
		</div>
	</div>
</div>
<!-- -->
<div class="gallery-part">
	<div class="gallery-page">
		<h2><?=$langage_lbl['LBL_HOME_MEET_DRIVER']?></h2>
		<em><?=$langage_lbl['LBL_HOME_MEET_DRIVER_CONTENT']?></em>
        <div class="gallery-page-inner">
		<?php 
			
			$dlang = $_SESSION['sess_lang'];
			
			for($i=0;$i<count($db_home_drv);$i++)
			{
			?>
				<div id="box-2" class="box"> <b>
					<img width="290" height="270" id="image-1" src="<?=$tconfig["tsite_upload_images"].$db_home_drv[$i]['vImage']?>"/></b>
					<span class="caption full-caption">
					<h3>
						<p><?=$db_home_drv[$i]['tText_'.$dlang];?></p>
						<strong><?=$db_home_drv[$i]['vName_'.$dlang]?>
							<?php  if($db_home_drv[$i]['vDesignation_'.$dlang] != ""){
								echo ",".$db_home_drv[$i]['vDesignation_'.$dlang];}?>
						</strong>
					</h3>
				</span>
				</div>
		<?php  } ?>
        </div>
	</div>
</div>
<!-- -->
<div class="home-map"></div>
<!-- -->
<script>
	var map;
	var geocoder;
	var autocomplete_from;
	var autocomplete_to;
	function initialize() {
		geocoder = new google.maps.Geocoder();
		var mapOptions = {
			zoom: 4,
			//center: new google.maps.LatLng('20.1849963', '64.4125062')
		};
		map = new google.maps.Map(document.getElementById('map-canvas'),
		mapOptions);
		
		var location = '<?=$db_def_con[0]['vCountry']?>';
			   geocoder = new google.maps.Geocoder();
				geocoder.geocode( { 'address': location }, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
					//console.log(results);
						map.setCenter(results[0].geometry.location);
					} else {
						alert("Could not find location: " + location);
					}
				});
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
									var fromLoc = $('#from').val();
									$.ajax({
										type: "POST",
										url: 'ajax_find_estimate.php',
										data: {dist_fare: dist_fare,time_fare: time_fare, fromLoc: fromLoc },
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