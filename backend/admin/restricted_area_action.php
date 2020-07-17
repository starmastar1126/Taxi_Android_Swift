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
	$script 	= 'Restricted Area';

	//echo '<prE>'; print_R($_REQUEST); echo '</pre>';die;

	// set all variables with either post (when submit) either blank (when insert)
	$iLocationId = isset($_POST['iLocationId']) ? $_POST['iLocationId'] : '';
	$eRestrictType = isset($_POST['eRestrictType'])?$_POST['eRestrictType']:'All';
	$eType = isset($_POST['eType'])?$_POST['eType']:'Disallowed';
	$eStatus_check = isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	$eStatus = ($eStatus_check == 'on')?'Active':'Inactive';
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	$previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';

	if(isset($_POST['submit'])) {
		if(SITE_TYPE=='Demo' && $id != '')
		{
				$_SESSION['success'] = 2;
				header("Location:restricted_area.php");
				exit;
		}

		$q = "INSERT INTO ";
		$where = '';

		if($id != '' ){
			$q = "UPDATE ";
			$where = " WHERE `iRestrictedNegativeId` = '".$id."'";
		}
		
		$query = $q ." `".$tbl_name."` SET
		`iLocationId` = '".$iLocationId."',
		`eRestrictType` = '".$eRestrictType."',
		`eType` = '".$eType."',
		`eStatus` = '".$eStatus."'"
		.$where;

		$obj->sql_query($query);
		$id = ($id != '')?$id:$obj->GetInsertId();
		if ($action == "Add") {
			$_SESSION['success'] = '1';
			$_SESSION['var_msg'] = 'Restricted Area Insert Successfully.';
		} else {
			$_SESSION['success'] = '1';
			$_SESSION['var_msg'] = 'Restricted Area Updated Successfully.';
		}
		header("Location:".$backlink);exit;
	}

	// for Edit
	if($action == 'Edit') {
		$sql = "SELECT * FROM ".$tbl_name." WHERE iRestrictedNegativeId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);

		$vLabel = $id;
		if(count($db_data) > 0) {
			foreach($db_data as $key => $value) {
				$iLocationId = $value['iLocationId'];
				$eRestrictType	 = $value['eRestrictType'];
				$eType	 = $value['eType'];
				$eStatus = $value['eStatus'];
			}
		}
	}
	
	$sql_geo_location = "SELECT * FROM location_master WHERE eStatus = 'Active' AND eFor = 'Restrict' ORDER BY vLocationName ASC ";
	$db_data_geo_location = $obj->MySQLSelect($sql_geo_location);
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
							<a class="back_link" href="restricted_area.php">
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
								<form id="_restricted_form" name="_restricted_form" method="post" action="">
								<input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="restricted_area.php"/>
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<div class="row">
									<div class="col-lg-12">
										<label>Geo Location Area<span class="red"> *</span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Select the location which you would like to allow/restrict for trips/jobs. You can define these locations from "Manage Locations >> Geo Fence Location" section'></i></label>
									</div>
									<div class="col-lg-3">
										<select id="lunch" name="iLocationId" class="form-control selectpicker" data-live-search="true" required="required" onchange="checkareaexist(this.value)">
											<option selected="selected" value="">Select Location</option>
											<?php 
											foreach($db_data_geo_location as $geo_location):?>
											<?php  if($geo_location['iLocationId'] == $iLocationId):?>
											<option selected="selected" value="<?php  echo $geo_location['iLocationId'];?>"><?php  echo $geo_location['vLocationName'];?></option>
											<?php  else:?>
											<option  value="<?php  echo $geo_location['iLocationId'];?>"><?php  echo $geo_location['vLocationName'];?></option>
											<?php  endif;?>
											<?php  endforeach;?>
										</select>
									</div>
									<div class="clear"></div>
									<div class="col-lg-12 restrict_area">
										<div class="exist_area error"></div>
									</div>
								</div>
								
								<?php  if ($APP_TYPE == 'UberX') { ?>
									  <input type="hidden" class="form-control" name = 'eRestrictType' id="eRestrictType" value="All">
								<?php  } else { ?>
								<div class="row">
								 	<div class="col-lg-12">
										  <label>Restrict area <span class="red"> *</span></label>
									</div>
								 	<div class="col-lg-3">
									  	<select class="form-control" name = 'eRestrictType' id="eRestrictType" >
										   <option value="All" <?php  if($eRestrictType == 'All') { ?> selected <?php  } ?> >All</option>
										   <option value="Pick Up" <?php  if($eRestrictType == 'Pick Up') { ?> selected <?php  } ?>>Pick Up</option>
										   <option value="Drop Off" <?php  if($eRestrictType == 'Drop Off') { ?> selected <?php  } ?>>Drop Off</option>
									  	</select>
								 	</div>
							 	</div>
								<?php  } ?>

								
								<div class="row">
								 <div class="col-lg-12">
									  <label>Restrict Type <span class="red"> *</span></label>
								 </div>
								 <div class="col-lg-3">
									  <select class="form-control" name = 'eType' id="eType" >
										   <option value="Disallowed" <?php  if($eType == 'Disallowed') { ?> selected <?php  } ?>>Disallowed</option>
										   <option value="Allowed" <?php  if($eType == 'Allowed') { ?> selected <?php  } ?>>Allowed</option>
									  </select>
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
										<input type="submit" class="btn btn-default" name="submit" id="submit" value="<?= $action; ?> Area" >
                                       <!--  <a href="javascript:void(0);" onclick="reset_form('_restricted_form');" class="btn btn-default">Reset</a> -->
                                        <a href="restricted_area.php" class="btn btn-default back_link">Cancel</a>
									</div>
								</div>
							</form>
						</div>
					</div>
					<div class="admin-notes">
                            <h4>Notes:</h4>
                            <ul>
                                    <li>
                                            Administrator can Add / Edit Restricted area on this page.
                                    </li>
                                    <li>
                                            Administrator need to take care about area locations.
                                    </li>
                                  
                            </ul>
                    </div>
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>
		<!--END MAIN WRAPPER -->

<?php  include_once('footer.php');?>

<!-- <script src="https://maps.google.com/maps/api/js?sensor=true&key=<?= $GOOGLE_SEVER_API_KEY_WEB ?>&libraries=places" type="text/javascript"></script> -->

<script type="text/javascript">
$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
function checkareaexist(iLocationId) {
	var restricted_id = "";
	<?php  if(!empty($id)) { ?>
	restricted_id = <?php  echo $id ?>;
	<?php  } ?>
	var request = $.ajax({
		type: "POST",
		url: 'ajax_check_restricted_area.php',
		data: 'iLocationId=' + iLocationId + '&restricted_id='+ restricted_id,
		success: function (data)
		{
			if(data > 0) {
				$('.restrict_area').css('padding-top','15px');
				$( "div.exist_area" ).html("Please Check, This Resticted Area Already Selected.");
				$('input[type="submit"]').attr('disabled','disabled');
			} else {
				$('.restrict_area').css('padding-top','0px');
				$( "div.exist_area" ).html("");
				$('input[type="submit"]').removeAttr('disabled');
			}
		}
	});
}
	
$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){
		referrer =  document.referrer;
	}else {
		referrer = $("#previousLink").val();
	}
	if(referrer == "") {
		referrer = "restricted_area.php";
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
