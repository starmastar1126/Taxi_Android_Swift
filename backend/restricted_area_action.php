<?php 
	include_once('../common.php');

	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();

	$id 		= isset($_REQUEST['restricted_id'])?$_REQUEST['restricted_id']:'';
	$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
	$action 	= ($id != '')?'Edit':'Add';

	$tbl_name 	= 'restricted_negative_area';
	$script 	= 'Settings';

	//echo '<prE>'; print_R($_REQUEST); echo '</pre>';

	// set all variables with either post (when submit) either blank (when insert)
	$vCountry = isset($_POST['vCountry'])?$_POST['vCountry']:'';
	$vState = isset($_POST['vState'])?$_POST['vState']:'';
	$vCity = isset($_POST['vCity'])?$_POST['vCity']:'';
	$vAddress = isset($_POST['vAddress'])?$_POST['vAddress']:'';
	$eStatus_check = isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	$eStatus = ($eStatus_check == 'on')?'Active':'Inactive';

	if(isset($_POST['submit'])) {


				if(SITE_TYPE=='Demo')
				{
						header("Location:country_action.php?id=".$id.'&success=2');
						exit;
				}

		$q = "INSERT INTO ";
		$where = '';

		if($id != '' ){
			$q = "UPDATE ";
			$where = " WHERE `iRestrictedNegativeId` = '".$id."'";
		}


		$query = $q ." `".$tbl_name."` SET
		`iCountryId` = '".$vCountry."',
		`iStateId` = '".$vState."',
		`iCityId` = '".$vCity."',
		`vAddress` = '".$vAddress."',
		`eStatus` = '".$eStatus."'"
		.$where;

		$obj->sql_query($query);
		$id = ($id != '')?$id:$obj->GetInsertId();
		if($id == " "){
			header("Location:restricted_area_action.php?success=1");
			
		}
		else{
			header("Location:restricted_area_action.php?restricted_id=".$id."&success=1");
		}
										
	}

	// for Edit
	if($action == 'Edit') {
		$sql = "SELECT * FROM ".$tbl_name." WHERE iRestrictedNegativeId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);

		$vLabel = $id;
		if(count($db_data) > 0) {
			foreach($db_data as $key => $value) {
				$iCountryId	 = $value['iCountryId'];
				$iStateId	 = $value['iStateId'];
				$iCityId	 = $value['iCityId'];
				$vAddress	 = $value['vAddress'];
				$eStatus = $value['eStatus'];
			}
		}
	}
	
	$sql_country = "SELECT * FROM country";
	$db_data_country = $obj->MySQLSelect($sql_country);
	
	$sql_state = "SELECT * FROM state where iCountryId='".$iCountryId."'";
	$db_data_state = $obj->MySQLSelect($sql_state);
	
	$sql_city = "SELECT * FROM city where iStateId='".$iStateId."'";
	$db_data_city = $obj->MySQLSelect($sql_city);
	//echo '<pre>'; print_R($db_data_state); echo '</pre>';die;
	
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin | Restricted Area <?=$action;?></title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link href="css/bootstrap-select.css" rel="stylesheet" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

		<?php  include_once('global_files.php');?>
		<!-- On OFF switch -->
		<link href="../assets/css/jquery-ui.css" rel="stylesheet" />
		<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
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
				<div class="inner">
					<div class="row">
						<div class="col-lg-12">
							<h2><?=$action;?> Restricted Area</h2>
							<a href="restricted_area.php">
								<input type="button" value="Back to Listing" class="add-btn">
							</a>
						</div>
					</div>
					<hr />
					<div class="body-div">
						<div class="form-group">
							<?php  if($success == 1) { ?>
								<div class="alert alert-success alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									Record Updated successfully.
								</div><br/>
								<?php  }elseif ($success == 2) { ?>
									<div class="alert alert-danger alert-dismissable">
											 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
											 "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
									</div><br/>
								<?php  }?>
							<form method="post" action="">
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<div class="row">
									<div class="col-lg-12">
										<label>Country Name<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										 <select id="lunch" onChange="showState(this.value);" name="vCountry" class="selectpicker" data-live-search="true" required="required">
										 <option selected="selected" value="">Select Country</option>
												
												
											<?php 
											foreach($db_data_country as $country):?>
												<?php  if($country['iCountryId']==$iCountryId):?>
												<option selected="selected" value="<?php  echo $country['iCountryId'];?>"><?php  echo $country['vCountry'];?></option>
												<?php  else:?>
												<option value="<?php  echo $country['iCountryId'];?>"><?php  echo $country['vCountry'];?></option>
												<?php  endif;?>
												<?php  endforeach;?>
												
											</select>
										</div>
								</div>
								
								<div class="row">
									<div class="col-lg-12">
										<label>State Name<span class="red"> </span></label>
									</div>
									<div class="col-lg-6">
											<select  id="state" name="vState" onChange="showCity(this.value,$id);" class="selectpicker" data-live-search="true" >
										
												
											<?php 
											foreach($db_data_state as $state):?>
												<?php  if($state['iStateId']==$iStateId):?>
											<option selected="selected" value="<?php  echo $state['iStateId'];?>"><?php  echo $state['vState'];?></option>
												<?php  else:?>
												<option value="<?php  echo $state['iStateId'];?>"><?php  echo $state['vState'];?></option>
												<?php  endif;?>
												<?php  endforeach;?>
												
											</select>
											
										</div>
								</div>

								
								<div class="row">

                                             <div class="col-lg-12">
                                                  <label><?php  echo "City";?><span class="red"> </span></label>
                                             </div>
                                            <div class="col-lg-6">
											<select id="city" name="vCity" class="selectpicker" data-live-search="true" >
												
											<?php 
												foreach($db_data_city as $city):?>
												<?php  if($city['iCityId'] == $iCityId):?>
												<option selected="selected" value="<?php  echo $city['iCityId'];?>"><?php  echo $city['vCity'];?></option>
												<?php  else:?>
												<option value="<?php  echo $city['iCityId'];?>"><?php  echo $city['vCity'];?></option>
												<?php  endif;?>
												<?php  endforeach;?>
												</select>
											</div>
                                        </div>
								
								<div class="row">
									<div class="col-lg-12">
										<label>Area<span class="red"></span></label>
									</div>
									<div class="col-lg-3">
										<input type="text" class="form-control" name="vAddress"  id="vAddress" value="<?=$vAddress;?>" placeholder="Area address">
									</div>
								</div>
								
								
								<div class="row">
									<div class="col-lg-12">
										<label>Status</label>
									</div>
									<div class="col-lg-6">
										<div class="make-switch" data-on="success" data-off="warning">
											<input type="checkbox" name="eStatus" <?=($id != '' && $eStatus == 'Inactive')?'':'checked';?>/>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<input type="submit" class="save btn-info" name="submit" id="submit" value="<?=$action;?> Area">
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>
		<!--END MAIN WRAPPER -->


		<?php  include_once('footer.php');?>
		<script type="text/javascript">



function showState(id){
	//alert("Hello");
//$('#stateloading').show();
 $.ajax({
   type: "POST",
   url: "functions_area.php",
   data: "country_id_not_required="+id,
    success: function(data){
	
	
    if(data.success){           
        document.write//alert(data.status);
		
		//$('#state').html("Hello");
        } else {
			
			//alert(data);
          // $('#msg').html(data).fadeIn('slow');
          $('#state').html(data); //also show a success message 
		  $('#state').selectpicker('refresh');
		   CityId=$('#state option:selected').val();
			/*  var json_obj = $.parseJSON(data);//parse JSON
				alert(json_obj.json);
			 */	// var a=JSON.stringify(data);
			//alert(a);
			//showCity(CityId);
           
        }
 }
});

}
function showCity(id){
	///alert(id);
	//$('#cityloading').show();
 $.ajax({
   type: "POST",
   url: "functions_area.php",
   data: "state_id_not_required="+id,
    success: function(data){
	
	
    if(data.success){           
        document.write//alert(data.status);
        } else {
			
          // $('#msg').html(data).fadeIn('slow');
          $('#city').html(data); //also show a success message 
		  $('#city').selectpicker('refresh');
           
        }
 }
});

}
</script>
		<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
		<script src="js/bootstrap-select.js"></script>
	</body>
	<!-- END BODY-->
</html>
