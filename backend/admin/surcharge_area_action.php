<?php 
	include_once('../common.php');
	
	if (!isset($generalobjAdmin)) {
		require_once(TPATH_CLASS . "class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	$script = "SurchargeArea";
	
	$tbl_name = 'location_surcharge';
	$tbl_name1 = 'location_surcharge_rates';
	$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : '';
	$var_msg = isset($_REQUEST['var_msg']) ? $_REQUEST['var_msg'] : '';
	$iSurchargeId = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
	$action = ($iSurchargeId != '') ? 'Edit' : 'Add';
	
	//For Country
	$sql = "SELECT vt.iVehicleTypeId,c.vCountry,st.vState,ct.vCity,vt.vAddress,vt.vVehicleType from vehicle_type vt
			left join country as c ON c.iCountryId = vt.iCountryId 
			left join state as st ON st.iStateId = vt.iStateId 
			left join city as ct ON ct.iCityId = vt.iCityId order by vt.vVehicleType";
	$data_vehicle = $obj->MySQLSelect($sql);
	// echo "<pre>";print_r($data_vehicle);exit;
	
	$vlatitudes = isset($_REQUEST['from_lat']) ? $_REQUEST['from_lat'] : '';
	$vlongitudes = isset($_REQUEST['from_long']) ? $_REQUEST['from_long'] : '';
	$vAddress = isset($_REQUEST['vAddress']) ? $_REQUEST['vAddress'] : '';
	$fRadius = isset($_REQUEST['fRadius']) ? $_REQUEST['fRadius'] : '';
	$vCountry = isset($_REQUEST['vCountry']) ? $_REQUEST['vCountry'] : '';
	$vState = isset($_REQUEST['vState']) ? $_REQUEST['vState'] : '';
	$vCity = isset($_REQUEST['vCity']) ? $_REQUEST['vCity'] : '';
	
	
	if(isset($_POST['btnsubmit'])){
		 // echo "<pre>";print_r($_REQUEST);exit;
		$fPercentage = isset($_REQUEST['fPercentage']) ? $_REQUEST['fPercentage'] : '';
		 // echo "<pre>";print_r($fPercentage);
		
		$vehicleType = isset($_REQUEST['vehicleType']) ? $_REQUEST['vehicleType'] : '';
		
		
		$q = "INSERT INTO ";
		$where = '';
		if($action != "Add"){
			$q = "UPDATE ";
			$where = " WHERE `iSurchargeId` = '".$iSurchargeId."'";
			
			$sql="delete from location_surcharge_rates where iSurchargeId = '".$iSurchargeId."'";
			$obj->sql_query($sql);
		}
			
			 $query = $q ." `".$tbl_name."` SET 	
			`vlatitudes` = '".$vlatitudes."',
			`vlongitudes` = '".$vlongitudes."',
			`vAddress` = '".$vAddress."',	
			`vCountry` = '".$vCountry."',
			`vState` = '".$vState."',
			`vCity` = '".$vCity."',
			`fRadius` = '".$fRadius."'"
			.$where;
			
			$id_insert =$obj->sql_query($query);
		
		$iSurchargeId_new = ($action != "Add") ? $iSurchargeId : $obj->GetInsertId();
		
		foreach($vehicleType as $val){
			// echo "<br>";
			$iVehicleTypeId = $val;
			$fPercentage_new = $fPercentage[$val];
			
			$q = "INSERT INTO ";
			$where = '';
			
			 $query = $q ." `".$tbl_name1."` SET 	
			`iSurchargeId` = '".$iSurchargeId_new."',
			`fPercentage` = '".$fPercentage_new."',
			`iVehicleTypeId` = '".$iVehicleTypeId."'"
			.$where;
			
			$db_data =$obj->sql_query($query);
		}
		$_SESSION['success'] = '1';
		$_SESSION['var_msg'] = 'Surcharge Location Added Successfully.';
		header("location:surcharge_area_action.php?id=".$iSurchargeId_new);
		exit;
	}
		
	if ($action == 'Edit') {
		$sql = "SELECT ls.*,lsr.* FROM " . $tbl_name . " as ls
		left join $tbl_name1 lsr on ls.iSurchargeId=lsr.iSurchargeId
		WHERE ls.iSurchargeId = '" . $iSurchargeId . "'";
		$db_data_surge = $obj->MySQLSelect($sql);
	
		if (count($db_data_surge) > 0) {
			foreach ($db_data_surge as $key => $value) {
				$vlatitudes = $value['vlatitudes'];
				$vlongitudes = $value['vlongitudes'];
				$vCountry = $value['vCountry'];
				$vState = $value['vState'];
				$vCity = $value['vCity'];
				$vAddress = $value['vAddress'];
				$fRadius = $value['fRadius'];
				$iVehicleTypeId[] = $value['iVehicleTypeId'];
				$fPercentage[$value['iVehicleTypeId']] = $value['fPercentage'];
			}
		}
		// echo "<pre>";print_r($iVehicleTypeId);exit;
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>
<?=$SITE_NAME;?>
| Manual<?php  echo $langage_lbl_admin['LBL_TEXI_ADMIN']; ?>Dispatch</title>
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<link rel="stylesheet" href="css/select2/select2.min.css" type="text/css" >
<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
<?php  include_once('global_files.php');?>
<script src="https://maps.google.com/maps/api/js?sensor=fasle&key=<?= $GOOGLE_SEVER_API_KEY_WEB ?>&libraries=places" type="text/javascript"></script>
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
        <div class="col-lg-12">
          <h1><?=$action?> Surcharge Location</h1>
		   <a class="add-btn" href="surcharge_area.php"> Back To Listing</a>
        </div>
      </div>
      <hr />
	  <?php  include('valid_msg.php'); ?>
      <form name="frm3" id="" method="post" action="" >
        <div class="surcharge-area-page">
          <div class="form-group">
            <?php  if ($success == "1") {?>
            <div class="alert alert-success alert-dismissable">
              <button aria-hidden="true" data-dismiss="alert" class="close" type="button">�</button>
              <?php 
									echo ($vassign != "1")?'Booking Has Been Added Successfully.':$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Has Been Assigned Successfully.';
								?>
            </div>
            <br/>
            <?php  } ?>
            <?php  if ($success == 2) 
							{
							?>
            <div class="alert alert-danger alert-dismissable">
              <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
              "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you. </div>
            <br/>
            <?php  } ?>
            <?php  if ($success == 0 && $var_msg != "") 
							{
							?>
						<div class="alert alert-danger alert-dismissable">
						  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
						  <?= $var_msg; ?>
						</div>
						<br/>
            <?php  } ?>
            <input type="hidden" name="from_lat_long" id="from_lat_long" value="<?="(".$vlatitudes.", ".$vlongitudes.")"; ?>" >
            <input type="hidden" name="from_lat" id="from_lat" value="<?= $vlatitudes; ?>" >
            <input type="hidden" name="from_long" id="from_long" value="<?= $vlongitudes; ?>" >
            <input type="hidden" name="vCountry" id="country" value="<?= $vCountry; ?>" >
            <input type="hidden" name="vState" id="state" value="<?= $vState; ?>" >
            <input type="hidden" name="vCity" id="city" value="<?= $vCity; ?>" >
            <input type="hidden" name="countryshort" id="countryshort" value="" >
            <input type="hidden" value="1" id="location_found" name="location_found">
            <input type="hidden" value="<?= $GOOGLE_SEVER_API_KEY_WEB; ?>" id="google_server_key" name="google_server_key" >
            <div class="map-main-page-inner">
              <div class="map-main-page-inner-tab">
                <div class="col-lg-12 map-live-hs-mid"> 
                <div class="row">
                  <label>Add From/To Location</label>
                  <input type="text" class="ride-location1 highalert txt_active form-control last-name1" name="vAddress"  id="from" value="<?= $vAddress; ?>" placeholder="Add Location" required>
                  </div> 
                  <div class="row">
                  <label>Enter Radius (KM)</label>
                  <input type="text" class="ride-location1 highalert txt_active form-control last-name1" name="fRadius"  id="radius" value="<?= $fRadius; ?>" placeholder="Enter radius in km" required onKeyUp="change_map_bounry(this.value);" pattern="([0-9]+[.])?[0-9]+" title="Please enter only digits for percentage.">
                  </div> 
                  
                  <div class="row">
                  <h4><b>Vehicle Types :</b></h4>
				
                  </div>
                  <?php  for($i=0;$i<count($data_vehicle);$i++){ 
					$localization = '';
					 if($data_vehicle[$i]['vCountry']== ''){ 
						$localization.= 'All  / ';	
					 }else{	
						$localization.= $data_vehicle[$i]['vCountry'].' / ';
					 }
					 
					 if($data_vehicle[$i]['vState']== ''){  
						$localization.= 'All  / ';	
					 }else{ 
						$localization.= $data_vehicle[$i]['vState'].' / ';
					 }
					 if($data_vehicle[$i]['vCity']== ''){  
						$localization.= 'All ';		
					 }else{	 
						$localization.= $data_vehicle[$i]['vCity'];
					 }
		
		 ?>
                  <div class="row has-switch1">
                    <div class="col-lg-12">
                      <?=$data_vehicle[$i]['vVehicleType']." ( ".$localization." )"?>
                    </div>
					 <?php 
						$style = "display:none";
						$checked = "";
						if($action == "Edit" && in_array($data_vehicle[$i]['iVehicleTypeId'],$iVehicleTypeId)){
							$style = "display:block";
							$checked = "checked";
						}
					  ?>
                    <div class="col-lg-6">
                      <div class="make-switch" data-on="success" data-off="warning">
                        <input type="checkbox" class="chk" id="chk_<?=$data_vehicle[$i]['iVehicleTypeId']?>" name="vehicleType[]" value="<?=$data_vehicle[$i]['iVehicleTypeId']?>" onChange="show_surge_area(this.value)" <?=$checked?>>
                      </div>
					 
					  <div id="surcharge_<?=$data_vehicle[$i]['iVehicleTypeId']?>" style="<?=$style?>" class="surchrge-input">
						<input type="text" id="input_<?=$data_vehicle[$i]['iVehicleTypeId']?>"  class="ride-location1 highalert txt_active form-control last-name1" name="fPercentage[<?=$data_vehicle[$i]['iVehicleTypeId']?>]" value="<?=$fPercentage[$data_vehicle[$i]['iVehicleTypeId']]?>" placeholder="Enter <?=$data_vehicle[$i]['vVehicleType']?> surge in percentage"  pattern="([0-9]+[.])?[0-9]+" title="Please enter only digits for surcharge percentage." >
					  </div>
                    </div>
                  </div>
				  
                  <?php  } ?>
				    <div class="red col-lg-12" style="display:none;font-size:15px" id="alert-vehicle">Please select at least one vehicle type from list.</div>
                  <div class="book-now-reset book-now-reset-surcharge-area"><span>
                    <input type="submit" class="save btn-info button-submit" name="btnsubmit" id="btnsubmit" value="Save" onclick="return check_vehicle_type_checked();">
                    <input type="reset" class="save btn-info button-submit" name="reset" id="reset12" value="Reset" >
                    </span></div>
                </div>
              </div>
              <div class="map-page">
        <div class="panel-heading location-map" style="background:none;">
          <div class="google-map-wrap">
            <div id="map-canvas" class="google-map" style="width:100%; height:500px;"></div>
          </div>
        </div>
      </div>
            </div>
          </div>
        </div>
      </form>
      
      <!-- popup -->
      <div class="map-popup" style="display:none" id="driver_popup"></div>
      <!-- popup end -->
    </div>
  </div>

<!--END PAGE CONTENT -->
</div>
<div style="clear:both;"></div>
<?php  include_once('footer.php'); ?>
<div style="clear:both;"></div>
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
<script>

				
				var map;
				var marker;
				var geocoder;
				var autocomplete_from;
				var markers = [];
				var bounds = [];
				var circles = [];
				var geocoder = new google.maps.Geocoder();
				var directionsService = new google.maps.DirectionsService(); // For Route Services on map
				var directionsOptions = {  // For Polyline Route line options on map
					polylineOptions: {
						strokeColor: '#FF7E00',
						strokeWeight: 5
					}
				};
				var directionsDisplay = new google.maps.DirectionsRenderer(directionsOptions);
				
				
				
				function initialize() {
					var thePoint = new google.maps.LatLng('20.1849963', '64.4125062');
					var mypoint = new google.maps.LatLng('23.0120', '72.5108');
					// var radius = 5*1000;
					
					var mapOptions = {
						zoom: 4,
						center: thePoint
					};
					map = new google.maps.Map(document.getElementById('map-canvas'),
					mapOptions);
					
					<?php  if($action == "Edit"){?>
							var lat1 = $("#from_lat").val();
							var lng1 = $("#from_long").val();
							var radius = $("#radius").val();
							
							var latlng1 = new google.maps.LatLng(lat1,lng1);
							setMarker(latlng1,'from_loc');
							change_map_bounry(radius);
					<?php  } ?>
					
					
					/* google.maps.event.addListener(map,"dblclick", function(event){
						// console.log(event);
						var lat = event.latLng.lat();
						var lng = event.latLng.lng();
						
						$("#from_lat_long").val(event.latLng);
						$("#from_lat").val(lat);
						$("#from_long").val(lng);
						
						DeleteCustom('from_loc','marker');
						setMarker(event.latLng,'from_loc');
						getAddress(lat,lng,'from');
					}); */
					
					google.maps.event.addListener(map,"click", function(event){
						// console.log(event);
						var lat = event.latLng.lat();
						var lng = event.latLng.lng();
						
						console.log(lat , lng);
					});
					
					
				}
				
				$(document).ready(function () {
					google.maps.event.addDomListener(window, 'load', initialize);
				});
				

				
				
				 var from = document.getElementById('from');
				 autocomplete_from = new google.maps.places.Autocomplete(from);
				 google.maps.event.addListener(autocomplete_from, 'place_changed', function () {
                    var place = autocomplete_from.getPlace();
					
					// console.log(place.address_components);
					for(var i=0;i<place.address_components.length;i++){
						if(place.address_components[i].types[0] == "country" && place.address_components[i].types[1] == "political"){
							$("#country").val(place.address_components[i].long_name);
							$("#countryshort").val(place.address_components[i].short_name);
						}else if(place.address_components[i].types[0] == "administrative_area_level_1" && place.address_components[i].types[1] == "political"){
							$("#state").val(place.address_components[i].long_name);
						}if(place.address_components[i].types[0] == "administrative_area_level_2" && place.address_components[i].types[1] == "political"){
							$("#city").val(place.address_components[i].long_name);
						}
						
					}
					
					$("#from_lat_long").val(place.geometry.location);
					$("#from_lat").val(place.geometry.location.lat());
					$("#from_long").val(place.geometry.location.lng());
                    
					go_for_action($("#from").val(),'from');
                });
				 
				
				
				function go_for_action(address,id) {
				geocoder.geocode( { 'address': address}, function(results, status) {
					// console.log(results)
						if (status == google.maps.GeocoderStatus.OK) {
							if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
									$("#"+id+"_lat_long").val((results[0].geometry.location));
									//alert(results[0].geometry.location);
									DeleteCustom(id+'_loc','marker');
									setMarker(results[0].geometry.location,id+'_loc');
								} else {
								alert("No results found");
							}
							} else {
							var place19 = autocomplete_from.getPlace();
							$("#from_lat_long").val(place19.geometry.location);
						}
					});
				}
				
				
				function setMarker(postitions,valIcon) {
					// alert(postitions);
					newIcon = '../webimages/upload/mapmarker/PinFrom.png';
					marker = new google.maps.Marker({
						map: map,
						draggable: true,
						animation: google.maps.Animation.DROP,
						position: postitions,
						icon: newIcon
					});
					map.setCenter(marker.getPosition());
					map.setZoom(5);
					marker.id = valIcon;
					markers.push(marker);
					
					
					marker.addListener('dragend', function(event) {
						 // console.log(event)
						var lat = event.latLng.lat(); 
						var lng = event.latLng.lng(); 
						var myLatlongs1 = new google.maps.LatLng(lat,lng);
						$("#from_lat").val(lat);
						$("#from_long").val(lng);
						$("#from_lat_long").val(myLatlongs1);
						
						getAddress(lat,lng,'from');
						
						var radius1= $("#radius").val();
						if(radius1 !=""){
							change_map_bounry(radius1);
						}
					});
					
				}
				
				
				function DeleteCustom(newId,type) {
					// console.log(markers[i]);
					var custom = [];
					if(type == 'marker'){
						custom = markers;
					}else if(type == 'circle'){
						custom = circles;
					}
					for (var i = 0; i < custom.length; i++) {
						if(newId != '') {
							if(custom[i].id == newId) {
								custom[i].setMap(null);
							}
						}else {
							custom[i].setMap(null);
							custom = [];
						}
					}
					
				}
				
			function getAddress(mDlatitude,mDlongitude,addId) {
				var mylatlang = new google.maps.LatLng(mDlatitude,mDlongitude);
				geocoder.geocode( {'latLng': mylatlang},
				function(results, status) {
				// console.log(results);
					if(status == google.maps.GeocoderStatus.OK) {
						if(results[0]) {
							document.getElementById(addId).value = results[0].formatted_address;
							$('#'+addId).val(results[0].formatted_address);
							for(var i=0;i<results[0].address_components.length;i++){
								if(results[0].address_components[i].types[0] == "country" && results[0].address_components[i].types[1] == "political"){
									$("#country").val(results[0].address_components[i].long_name);
									$("#countryshort").val(results[0].address_components[i].short_name);
								}else if(results[0].address_components[i].types[0] == "administrative_area_level_1" && results[0].address_components[i].types[1] == "political"){
									$("#state").val(results[0].address_components[i].long_name);
								}if(results[0].address_components[i].types[0] == "administrative_area_level_2" && results[0].address_components[i].types[1] == "political"){
									$("#city").val(results[0].address_components[i].long_name);
								}
								
							} 
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
			
			function change_map_bounry(radius){
				// console.log(parseInt(radius));
				var from_val = $("#from").val();
				  if(radius != "" && parseInt(radius)!='NaN' && radius > 0 && from_val != ""){
					var lat = marker.getPosition().lat();
					var lng = marker.getPosition().lng();
					// var latlng = marker.getPosition();
					
					var mylatlang = new google.maps.LatLng(lat,lng);
					// console.log(mylatlang);
					
					 // for (var city in citymap) {
					  // Add the circle for this city to the map.
					  DeleteCustom('from','circle');
					  var cityCircle = new google.maps.Circle({
							strokeColor: '#FF0000',
							strokeOpacity: 0.8,
							strokeWeight: 2,
							fillColor: '#FF0000',
							fillOpacity: 0.35,
							map: map,
							center: mylatlang,
							radius: parseFloat(radius) * 1000,
						});
						
						google.maps.event.addListener(cityCircle,'radius_changed',function(){
							var chngradius = parseFloat(cityCircle.getRadius() /1000).toFixed(2);
							$("#radius").val(chngradius);
					   })
						
						map.fitBounds(cityCircle.getBounds());
						cityCircle.id='from';
						circles.push(cityCircle);
				 }  
			}
			
			function show_surge_area(val){
				// alert("#chk_"+val);
				if($("#chk_"+val).is(':checked')){
					 $("#surcharge_"+val).show();
					 $("#input_"+val).attr('required','required');
				}else{
					 $("#surcharge_"+val).hide();
					 $("#input_"+val).removeAttr('required');
				}
			}
			
			function isNumberKey(evt)
            {
                var charCode = (evt.which) ? evt.which : evt.keyCode;
				// console.log(charCode);
					// if (charCode > 31 && (charCode < 35 || charCode > 57)){
				if (((charCode < 48 || charCode > 57) && (charCode < 96 || charCode > 105)) && (charCode!=144 && charCode!=190 && charCode!=110 && charCode != 8 && charCode!=9)){ 
						return false;
					}
					return true;
            }
			
			function check_vehicle_type_checked(){
				var a= $('input[type=checkbox]:checked').size();
				if(a <= 0){
					$("#alert-vehicle").show();
					return false;
				}else{
					$("#alert-vehicle").hide();
					return true;
				}
			}
			
		</script>
</body>
<!-- END BODY-->
</html>
