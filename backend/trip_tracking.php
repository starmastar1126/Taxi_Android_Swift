<?php 
//echo "dsddsd"; exit;
include_once('common.php');
$iTripId = isset($_REQUEST['iTripId']) ? $_REQUEST['iTripId'] : '';

$sql ="select iDriverId,iActive From trips where iTripId=".$iTripId; 
$db_dtrip = $obj->MySQLSelect($sql);
//print_r($db_dtrip); exit;

?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
	<!--<meta http-equiv="refresh" content="10" > -->
    <title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_TRIP_TRACKING']; ?></title>
    <!-- Default Top Script and css -->
	<!-- <link rel="stylesheet" href="css/style.css" /> -->
    <?php  include_once("top/top_script.php");?>
    <!-- End: Default Top Script and css-->
	<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=en&key=<?=$GOOGLE_SEVER_API_KEY_WEB?>"></script>
	
</head>
<body>
<!-- home page -->
	<div id="main-uber-page">
		<!-- Left Menu -->
		<?php  include_once("top/left_menu.php");?>
			<!-- End: Left Menu-->
			<!-- Top Menu -->
		<?php  include_once("top/top_script.php");?>
		<link href="assets/css/checkbox.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/radio.css" rel="stylesheet" type="text/css" />
		<?php  include_once("top/validation.php");
		include_once("top/header_topbar.php");?>
		  <link rel="stylesheet" href="assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
		  <!-- End: Top Menu-->
		  <!-- contact page-->
        <div class="page-contant page-contant trip-tracking-main">
			<div class="page-contant-inner trip-tracking">	
				<h2 class="header-page add-car-vehicle">
                    <!--<a href="new_delivery.php"><?=$langage_lbl['LBL-back_to_listing']; ?></a>-->
                </h2>
				<?php  if($db_dtrip[0]['iActive'] == 'Active'|| $db_dtrip[0]['iActive'] == 'On Going Trip') { ?>
				<div class="map-page" style="display:none;">
					<div class="panel-heading location-heading">
						<i class="icon-map-marker"></i>
						<?=$langage_lbl['LBL_LOCATIONS_TXT']; ?>
					</div>
					<div class="panel-heading location-map" style="background:none;">
						<div class="google-map-wrap" >
							<!--<div id="google-map" class="google-map">-->
							<div class="gmap-div gmap-div1"><div id="map-canvas" class="gmap3 google-map" style="height:500px;"></div></div>
						</div><!-- #google-map -->
					</div>
				</div>
				<?php  }else if($db_dtrip[0]['iActive'] == 'Finished'){ ?>
				<br><br><br>
				<div class="row">
                    <div class="alert alert-danger paddiing-10">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						<?=$langage_lbl['LBL_TRIP_IS_FINISHED']; ?>.
                    </div>
                </div>
				 <?php  }else if($db_dtrip[0]['iActive'] == 'Canceled'){ ?> 
				<br><br><br>
				<div class="row">
                    <div class="alert alert-danger paddiing-10">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						<?=$langage_lbl['LBL_TRIP_ IS_CANCELLED']; ?>.
                    </div>
                </div>
				<?php  } ?>
            <div style="clear:both;"></div>    
			</div>				
		</div>
   
    <!-- footer part -->
    <?php  include_once('footer/footer_home.php');?>
    <!-- footer part end -->
            <!-- End:contact page-->
            <div style="clear:both;"></div>
    </div>
    <!-- home page end-->

<?php  include_once('top/footer_script.php'); ?>
<script type="text/javascript" src="<?php  echo $tconfig["tsite_url_main_admin"]?>js/gmap3.js"></script>
<script>
	var iTripId = '<?php  echo $iTripId; ?>';
	var latlng;
	var locallat;
	var locallang;
	var map;
	var interval3;
	var marker = [];
	var myOptions=[];
	
	  function initialize(){
			directionsService2 = new google.maps.DirectionsService();
			directionsDisplay2 = new google.maps.DirectionsRenderer();
	  		$.ajax({			
				type: "POST",
				url: "ajax_getdirver_detail.php",
				dataType: "json",
				data: {iTripId:iTripId},
				success: function(driverdetail){				
					if(driverdetail != 1 ){
						$('.map-page').show();
						 var latdrv = driverdetail.vLatitude;
						 var longdrv = driverdetail.vLongitude;
						 latlng = new google.maps.LatLng(latdrv, longdrv);
						 locallat = new google.maps.LatLng(driverdetail.tStartLat,driverdetail.tStartLong);
						 locallang = new google.maps.LatLng(driverdetail.tEndLat,driverdetail.tEndLong);	
						
						fromLatlongs = driverdetail.tStartLat+", "+driverdetail.tStartLong;					
						toLatlongs = driverdetail.tEndLat+", "+driverdetail.tEndLong;
						//toLatlongs = '23.0146207'+", "+'72.5284118';
						
						myOptions = {
						 zoom: 16,
						 center: latlng,			
						}
						
						map = new google.maps.Map(document.getElementById("map-canvas"), myOptions);
						  marker = new google.maps.Marker({
						  position: latlng, 
						  map: map,
						  icon: "webimages/upload/mapmarker/source_marker.png"	, 
									
						});
					
					}else{
						$('.map-page').hide();	
						//alert('No Online Vehicle');
						// var site_url1 = "<?php  echo $tconfig["tsite_url"]?>";
					}
				}
			});
	}
	google.maps.event.addDomListener(window,'load', initialize);
</script>

<?php  //if($ENABLE_PUBNUB != 'Yes') { ?>
<script>	
	interval3 = setInterval(function() {
		$.ajax({			
			type: "POST",
			url: "ajax_getdirver_detail.php",
			dataType: "json",
			data: {iTripId:iTripId},
			success: function(driverdetail){
			//marker.setMap(null);	
			
			if(driverdetail != 1 ){
					$('.map-page').show();
					
					 var latdrv = driverdetail.vLatitude;
					 var longdrv = driverdetail.vLongitude;
					 latlng = new google.maps.LatLng(latdrv, longdrv);
					 locallat = new google.maps.LatLng(driverdetail.tStartLat,driverdetail.tStartLong);
					 locallang = new google.maps.LatLng(driverdetail.tEndLat,driverdetail.tEndLong);	
					
					marker.setMap(null);
					marker = new google.maps.Marker({
					  position: latlng, 
					  map: map,
					  icon: "webimages/upload/mapmarker/source_marker.png"
					});
					
				}else{
					$('.map-page').hide();
					clearInterval(interval3);
					alert('No Online Vehicle');
				}
			}
		});
	},30000);
</script>

<?php  //}else { ?>

<!-- <script src="http://cdn.pubnub.com/pubnub.min.js"></script>
<script>(function(){
var publishKey = '<?php  echo $PUBNUB_PUBLISH_KEY; ?>';
var subscribeKey = '<?php  echo $PUBNUB_SUBSCRIBE_KEY; ?>';
//Define Pubnub
var pubnub = PUBNUB.init({publish_key:publishKey, subscribe_key:subscribeKey});
//Get Channel
var channel = 'ONLINE_DRIVER_LOC_<?php  echo $db_dtrip[0]["iDriverId"]; ?>';
//Get Response
pubnub.subscribe({
    channel  : channel,
    callback : function(response) {
		console.log(response);
		var response = JSON.parse(response);
		if(response.vLatitude != "" && response.vLongitude != "") {
			$('.map-page').show();
			latlng = new google.maps.LatLng(response.vLatitude, response.vLongitude);
			marker.setMap(null);
			marker = new google.maps.Marker({
			  position: latlng, 
			  map: map,
			  icon: "webimages/upload/mapmarker/source_marker.png"
			});
		}
	}
});
})();
</script> -->
<?php  //} ?>

</body>
</html>

