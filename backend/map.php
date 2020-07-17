<?php 
include_once('../common.php');

if(!isset($generalobjAdmin)){
	require_once(TPATH_CLASS."class.general_admin.php");
	$generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

/* START COUNT QUERY */
$sql = "select count(iDriverId) AS ONLINE FROM register_driver WHERE vLatitude !='' AND vLongitude !='' AND vAvailability = 'Available' ";
$db_records_online = $obj->MySQLSelect($sql);
$sql = "select count(iDriverId) AS OFFLINE FROM register_driver WHERE vLatitude !='' AND vLongitude !='' AND vAvailability = 'Not Available' ";
$db_records_offline = $obj->MySQLSelect($sql);
#echo "<pre>"; print_r($db_records_online );echo "</pre>";



$sql = "select iDriverId,tLastOnline,vAvailability,vTripStatus FROM register_driver WHERE vLatitude !='' AND vLongitude !='' ";
$db_total_driver = $obj->MySQLSelect($sql);
#echo "<pre>"; print_r($db_total_driver );echo "</pre>";exit;
$tot_online = 0;
$tot_ofline = 0;
$tot_ontrip = 0;

for($ji=0;$ji<count($db_total_driver);$ji++){
   $curtime = time();  
   $last_driver_online_time = strtotime($db_total_driver[$ji]['tLastOnline']);   
   $online_time_difference = $curtime-$last_driver_online_time;  
   if($db_total_driver[$ji]['vAvailability'] == "Available"){
      $tot_online = $tot_online+1;
   }else{
      $vTripStatus = $db_total_driver[$ji]['vTripStatus'];
      if($vTripStatus == 'Active' || $vTripStatus == 'On Going Trip' || $vTripStatus == 'Arrived'){
         $tot_ontrip = $tot_ontrip+1;
      }else{
         $tot_ofline = $tot_ofline+1;
      }
   } 
}
$db_records_online[0]['ONLINE'] = $tot_online;
$db_records_offline[0]['OFFLINE'] = $tot_ofline;
$db_records_offline[0]['ONTRIP'] = $tot_ontrip;
#echo date("Y-m-d H:i:s"); echo "<br/>";
#echo $tot_online;echo "<br/>";
#echo $tot_ofline;echo "<br/>";  exit;

/* END COUNT QUERY */
$script="Map";
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

<!-- BEGIN HEAD-->
<head>
	<meta charset="UTF-8" />
	<title><?=$langage_lbl['LBL_ADMIN']; ?> | <?=$langage_lbl['LBL_DASHBOARD']; ?></title>
	<meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta http-equiv="refresh" content="60">
	<!--[if IE]>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<![endif]-->
	<!-- GLOBAL STYLES -->
	<?php  include_once('global_files.php');?>
	<link rel="stylesheet" href="css/style.css" />

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
	<script src="http://maps.google.com/maps/api/js?sensor=true&key=<?=$GOOGLE_SEVER_API_KEY_WEB?>" type="text/javascript"></script>
	<script type='text/javascript' src='../assets/map/gmaps.js'></script>
	<!--END GLOBAL STYLES -->

	<!-- PAGE LEVEL STYLES -->
	<!-- END PAGE LEVEL  STYLES -->
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
	<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
	<![endif]-->
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

		<div class="inner" style="min-height: 700px;">
			<div class="row">
				<div class="col-lg-12">
					<h1><?=$langage_lbl['LBL_GODS_VIEW_MAP_VIEW']; ?> </h1>
				</div>
			</div>
			<hr />

			<!-- COMMENT AND NOTIFICATION  SECTION -->
			<div class="row">
				<div class="col-lg-12">

					<div class="chat-panel panel panel-default">
						<div class="panel-heading">
							<div class="panel-title-box">
							   <i class="icon-map-marker"></i> <?=$langage_lbl['LBL_LOCATIONS_TXT']; ?>
							   <ul class="panel-controls">
								<li><a title="Refresh" onclick="window.location.reload();" href="javascript:void(0);" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
								</ul>
							</div>
						</div>

						<div class="panel-heading" style="background:none;">
							<div class="google-map-wrap" >
								<div id="google-map" class="google-map">
								</div><!-- #google-map -->
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="hrhr"><hr /></div>
			<div class="row">
				<div class="col-lg-12">
					<div style="" class="quick-btn-bottom-part">
						<a class="quick-btn" href="map.php?type=online">
							<img src="../webimages/upload/mapmarker/green.png">
							<span><?=$langage_lbl['LBL_ONLINE']; ?></span>
							<span><?php  echo $db_records_online[0]['ONLINE'];?></span>
						</a>
						<a class="quick-btn" href="map.php?type=enroute">
							<img src="../webimages/upload/mapmarker/orange.png">
							<span><?=$langage_lbl['LBL_ON']; ?> &nbsp;&nbsp; <?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'];?> </span>
							<span><?php  echo $db_records_offline[0]['ONTRIP'];?></span>
						</a>
						<a class="quick-btn" href="map.php?type=offline">
							<img src="../webimages/upload/mapmarker/gray.png">
							<span><?=$langage_lbl['LBL_OFFLINE']; ?></span>
							<span><?php  echo $db_records_offline[0]['OFFLINE'];?></span>
						</a>
						<a class="quick-btn" href="map.php">
							<b><i class="icon-align-justify icon-1x"></i></b>
							<span><?=$langage_lbl['LBL_ALL']; ?></span>
							<span><?php  echo $db_records_online[0]['ONLINE'] + $db_records_offline[0]['ONTRIP'] + $db_records_offline[0]['OFFLINE'];?></span>
						</a>
					</div>
				</div>

			</div>

		</div>
		<!-- END COMMENT AND NOTIFICATION  SECTION -->
	</div>
</div>

<!--END PAGE CONTENT -->
</div>

<?php  include_once('footer.php'); ?>

<?php 
function getaddress($lat,$lng)
{
   $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($lat).','.trim($lng).'&sensor=false';
   $json = @file_get_contents($url);
   $data=json_decode($json);
   $status = $data->status;
   if($status=="OK")
   {
     return $data->results[0]->formatted_address;
   }
   else
   {
     return "Address Not Found";
   }
}

//echo "<pre>"; print_r($_SESSION);echo "</pre>";
if(isset($_REQUEST['type']) && $_REQUEST['type'] != '')
{
	if($_REQUEST['type'] == 'online' )
		//$tsql = " AND vAvailability = 'Available'";
		$tsql ="";
	else if($_REQUEST['type'] == 'offline' )
		//$tsql = " AND vAvailability = 'Not Available'";
		$tsql ="";
	else
		$tsql ="";
}


$sql = "SELECT iDriverId,iCompanyId, CONCAT(vName,' ',vLastName) AS FULLNAME,vLatitude,vLongitude,vServiceLoc,vAvailability,vTripStatus,tLastOnline
							FROM register_driver
								WHERE vLatitude !='' AND vLongitude !='' $tsql ";
$db_records = $obj->MySQLSelect($sql);

for($i=0;$i<count($db_records);$i++){
   $time = time();  
   $last_online_time = strtotime($db_records[$i]['tLastOnline']);
   $time_difference = $time-$last_online_time;
   if($time_difference <= 300 && $db_records[$i]['vAvailability'] == "Available"){
      $db_records[$i]['vAvailability'] = "Available";
   }else{
      //$db_records[$i]['vAvailability'] = "Not Available";
      $vTripStatus = $db_records[$i]['vTripStatus'];
      if($vTripStatus == 'Active' || $vTripStatus == 'On Going Trip' || $vTripStatus == 'Arrived'){
         //$tot_ontrip = $tot_ontrip+1;
         $db_records[$i]['vAvailability'] = "Ontrip";
      }else{
         //$tot_ofline = $tot_ofline+1;
         $db_records[$i]['vAvailability'] = "Not Available";
      }
   } 
   $db_records[$i]['vServiceLoc'] = getaddress($db_records[$i]['vLatitude'],$db_records[$i]['vLongitude']);
}
#echo "<pre>";print_r($db_records);exit;
//echo "<pre>"; print_r($db_records);echo "</pre>"; die;
$locations = array();

#marker Add
if($_REQUEST['type'] == ''){
  foreach ($db_records as $key => $value) {   
  	$locations[] = array(
  		'google_map' => array(
  			'lat' => $value['vLatitude'],
  			'lng' => $value['vLongitude'],
  		),
  		'location_address' => $value['vServiceLoc'],
  		'location_name'    => $value['FULLNAME'],
  		'location_online_status'    => $value['vAvailability'],
  	);
  }    
}else if($_REQUEST['type'] == 'online'){
  foreach ($db_records as $key => $value) {
    if($value['vAvailability'] == "Available"){ 
    	$locations[] = array(
    		'google_map' => array(
    			'lat' => $value['vLatitude'],
    			'lng' => $value['vLongitude'],
    		),
    		'location_address' => $value['vServiceLoc'],
    		'location_name'    => $value['FULLNAME'],
    		'location_online_status'    => $value['vAvailability'],
    	);
    }  
  }
}else if($_REQUEST['type'] == 'enroute'){
  foreach ($db_records as $key => $value) {
    if($value['vAvailability'] == "Ontrip"){ 
    	$locations[] = array(
    		'google_map' => array(
    			'lat' => $value['vLatitude'],
    			'lng' => $value['vLongitude'],
    		),
    		'location_address' => $value['vServiceLoc'],
    		'location_name'    => $value['FULLNAME'],
    		'location_online_status'    => $value['vAvailability'],
    	);
    }  
  }
}else{
  foreach ($db_records as $key => $value) {
    if($value['vAvailability'] == "Not Available"){ 
    	$locations[] = array(
    		'google_map' => array(
    			'lat' => $value['vLatitude'],
    			'lng' => $value['vLongitude'],
    		),
    		'location_address' => $value['vServiceLoc'],
    		'location_name'    => $value['FULLNAME'],
    		'location_online_status'    => $value['vAvailability'],
    	);
    }  
  }
}  
#echo "<pre>";print_r($locations);exit;
/* Marker  */
/*	$locations[] = array(
        'google_map' => array(
            'lat' => '-6.976622',
            'lng' => '110.39068959999997',
        ),
        'location_address' => 'Puri Anjasmoro B1/22 Semarang',
        'location_name'    => 'Loc A',
    );


    $locations[] = array(
        'google_map' => array(
            'lat' => '-6.974426',
            'lng' => '110.38498099999993',
        ),
        'location_address' => 'Puri Anjasmoro P5/20 Semarang',
        'location_name'    => 'Loc B',
    );


    $locations[] = array(
        'google_map' => array(
            'lat' => '-7.002475',
            'lng' => '110.30163800000003',
        ),
        'location_address' => 'Ngaliyan Semarang',
        'location_name'    => 'Loc C',
    ); */
#echo "<pre>"; print_r($locations);echo "</pre>";
?>

<?php 
/* Set Default Map Area Using First Location */
$map_area_lat = isset( $locations[0]['google_map']['lat'] ) ? $locations[0]['google_map']['lat'] : '';
$map_area_lng = isset( $locations[0]['google_map']['lng'] ) ? $locations[0]['google_map']['lng'] : '';
?>

<script>
	jQuery( document ).ready( function($) {
		/* Do not drag on mobile. */
		var is_touch_device = 'ontouchstart' in document.documentElement;

		var map = new GMaps({
			el: '#google-map',
			lat: '<?php  echo $map_area_lat; ?>',
			lng: '<?php  echo $map_area_lng; ?>',
			scrollwheel: false,
			draggable: ! is_touch_device
		});

		/* Map Bound */
		var bounds = [];

		<?php  /* For Each Location Create a Marker. */
		foreach( $locations as $location ){
		$name = $location['location_name'];
		$addr = $location['location_address'];
		$online_status = $location['location_online_status'];
		$map_lat = $location['google_map']['lat'];
		$map_lng = $location['google_map']['lng'];
		?>
		/* Set Bound Marker */
		var latlng = new google.maps.LatLng(<?php  echo $map_lat; ?>, <?php  echo $map_lng; ?>);
		bounds.push(latlng);
		/* Add Marker */
		map.addMarker({
			lat: <?php  echo $map_lat; ?>,
			lng: <?php  echo $map_lng; ?>,
			title: '<?php  echo $name; ?>',
			<?php  if($online_status == 'Available'){?>
			icon: '../webimages/upload/mapmarker/green.png',
			<?php  }else if($online_status == 'Ontrip'){?>
			icon: '../webimages/upload/mapmarker/orange.png',
      <?php  }else{ ?>
			icon: '../webimages/upload/mapmarker/gray.png',
			<?php  }?>
			infoWindow: {
				content: '<table style="font-weight:bold;"><tr><td>Name </td><td>- <?php  echo $name; ?></tr><tr><td>Last Location </td><td>- <?php  echo $addr; ?></tr></table'
			}
		});

		<?php  } //end foreach locations ?>

		/* Fit All Marker to map */
		map.fitLatLngBounds(bounds);

		/* Make Map Responsive */
		var $window = $(window);
		function mapWidth() {
			var size = $('.google-map-wrap').width();
			$('.google-map').css({width: size + 'px', height: (size/2) + 'px'});
		}
		mapWidth();
		$(window).resize(mapWidth);

	});
</script>
</body>
<!-- END BODY-->
</html>