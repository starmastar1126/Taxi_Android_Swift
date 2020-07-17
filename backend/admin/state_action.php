<?php 
	include_once('../common.php');

	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();

	$id 		= isset($_REQUEST['id'])?$_REQUEST['id']:'';
	$state_id = isset($_REQUEST['state_id'])?$_REQUEST['state_id']:'';
	$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
	$action 	= ($id != '')?'Edit':'Add';
	$tbl_name 	= 'state';
	$script 	= 'state';
	
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
    $previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';

	// set all variables with either post (when submit) either blank (when insert)
	$vCountry = isset($_POST['vCountry'])?$_POST['vCountry']:'';
	$vState = isset($_POST['vState'])?$_POST['vState']:'';
	$vStateCode = isset($_POST['vStateCode'])?$_POST['vStateCode']:'';
	$eStatus_check = isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	$eStatus = ($eStatus_check == 'on')?'Active':'Inactive';

	if(isset($_POST['submit'])) {
		if(SITE_TYPE=='Demo' && $id != "") {
			$_SESSION['success'] = '2';
			header("location:".$backlink);
			exit;
		}

		require_once("library/validation.class.php");
		$validobj = new validation();
		$validobj->add_fields($_POST['vCountry'], 'req', 'Country is required');
		$validobj->add_fields($_POST['vState'], 'req', 'State Name is required');
		$validobj->add_fields($_POST['vStateCode'], 'req', 'State Code is required');
		$error = $validobj->validate();
		
		if ($error) {
			$success = 3;
			$newError = $error;
			//exit;
		}else {
			$q = "INSERT INTO ";
			$where = '';

			if($id != '' ){
				$q = "UPDATE ";
				$where = " WHERE `iStateId` = '".$id."'";
			}


			$query = $q ." `".$tbl_name."` SET
			`iCountryId` = '".$vCountry."',
			`vState` = '".$vState."',
			`vStateCode` = '".$vStateCode."',
			`eStatus` = '".$eStatus."'"
			.$where;

			$obj->sql_query($query);
			$id = ($id != '')?$id:$obj->GetInsertId();
			if ($action == "Add") {
				$_SESSION['success'] = '1';
				$_SESSION['var_msg'] = 'State Inserted Successfully.';
			} else {
				$_SESSION['success'] = '1';
				$_SESSION['var_msg'] = 'State Updated Successfully.';
			}	
			header("location:".$backlink);
		}
	}
	
	$sql1 = "SELECT * FROM country WHERE eStatus != 'Deleted' ORDER BY vCountry ASC";
	$db_data1 = $obj->MySQLSelect($sql1);
	
	// for Edit
	if($action == 'Edit') {
		$sql = "SELECT * FROM state WHERE iStateId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);

		$vLabel = $id;
		if(count($db_data) > 0) {
			foreach($db_data as $key => $value) {
				//$vCountry	 = $value['vCountry'];
				$vCountry	 = $value['iCountryId'];
				$vState	 = $value['vState'];
				$vStateCode	 = $value['vStateCode'];
				$eStatus = $value['eStatus'];
				//$vCountryCodeISO_3	 = $value['vCountryCodeISO_3'];
				//$vPhoneCode	 = $value['vPhoneCode'];
				
			}
		}
	}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin | State <?=$action;?></title>
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
							<h2><?=$action;?> State</h2>
							<a href="state.php" class="back_link">
								<input type="button" value="Back to Listing" class="add-btn">
							</a>
						</div>
					</div>
					<hr />
					<div class="body-div">
						<div class="form-group">
							<?php  if ($success == 2) {?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                            </div><br/>
                            <?php } ?>
                            <?php  if ($success == 3) {?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
								<?php  print_r($error); ?>
                            </div><br/>
                            <?php } ?>
							<form method="post" action="" name="_state_form" id="_state_form" >
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="state.php"/>
								<div class="row">
									<div class="col-lg-12">
										<label>Country Name<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										 <select id="lunch" name="vCountry" class="selectpicker" data-live-search="true">
											<option value="">Select Country</option>
											<?php 
											foreach($db_data1 as $country):?>
												<?php  if($country['iCountryId']==$vCountry):?>
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
										<label>State Name<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vState"  id="vState" value="<?=$vState;?>" placeholder="State Name" >
									</div>
								</div>
								
								<div class="row">
									<div class="col-lg-12">
										<label>State Code<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vStateCode"  id="vStateCode" value="<?=$vStateCode;?>" placeholder="State Code" >
									</div>
								</div>
								<!-- <div class="row">
									<div class="col-lg-12">
										<label>Country Code ISO_3<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vCountryCodeISO_3"  id="vCountryCodeISO_3" value="<?=$vCountryCodeISO_3;?>" placeholder="Country Code ISO_3" required>
									</div>
								</div> -->
								
								<!-- <div class="row">
									<div class="col-lg-12">
										<label>Country Phone Code<span class="red"> *</span></label>
									</div>
									<div class="col-lg-6">
										<input type="text" class="form-control" name="vPhoneCode"  id="vPhoneCode" value="<?=$vPhoneCode;?>" placeholder="Country Phone Code" required>
									</div>
								</div> -->

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
										<input type="submit" class="btn btn-default" name="submit" id="submit" value="<?=$action;?> State">
										<input type="reset" value="Reset" class="btn btn-default">
										<!-- <a href="javascript:void(0);" onclick="reset_form('_state_form');" class="btn btn-default">Reset</a> -->
                                        <a href="state.php" class="btn btn-default back_link">Cancel</a>
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
		
<script>
$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){ 
		referrer =  document.referrer;		
	}else { 
		referrer = $("#previousLink").val();
	}
	if(referrer == "") {
		referrer = "state.php";
	}else { 
		$("#backlink").val(referrer);		
	}
	$(".back_link").attr('href',referrer); 	
});
</script>
		<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
		<script src="js/bootstrap-select.js"></script>
	</body>
	<!-- END BODY-->
</html>
