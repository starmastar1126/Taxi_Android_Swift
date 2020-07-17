<?php 
	include_once('../common.php');
		if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	// $APP_TYPE = "UberX";
	
	$start = @date("Y");
	$end = '1970';

	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
	$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
	$action = ($id != '') ? 'Edit' : 'Add';
	$tbl_name = 'driver_vehicle';
	$tbl_name1 = 'service_pro_amount';
	
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
    $previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	
	$script = 'Vehicle';

	$sql = "select * from driver_vehicle where iDriverVehicleId = '" . $id . "' ";
	$db_mdl = $obj->MySQLSelect($sql);
	
	$vLicencePlate = isset($_POST['vLicencePlate']) ? $_POST['vLicencePlate'] : '';
	$iCompanyId = isset($_POST['iCompanyId']) ? $_POST['iCompanyId'] : '';
	$iMakeId = isset($_POST['iMakeId']) ? $_POST['iMakeId'] : '';
	$iModelId = isset($_POST['iModelId']) ? $_POST['iModelId'] : '';
	$fAmount = isset($_POST['fAmount']) ? $_POST['fAmount'] : '';
	$iYear = isset($_POST['iYear']) ? $_POST['iYear'] : '';
	$eStatus_check = isset($_POST['eStatus']) ? $_POST['eStatus'] : 'off';
	$iDriverId = isset($_POST['iDriverId']) ? $_POST['iDriverId'] :'';
	$vCarType = isset($_POST['vCarType']) ? $_POST['vCarType'] : '';
	$iCompanyId = isset($_POST['iCompanyId']) ? $_POST['iCompanyId'] : '';
	$eStatus = ($eStatus_check == 'on') ? 'Active' : 'Inactive';
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	$previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	
	$sql = "select iDriverVehicleId from driver_vehicle where iDriverId = '" . $iDriverId . "' ";
	$db_drv_veh=$obj->MySQLSelect($sql);

	$sql = "SELECT * from make WHERE eStatus='Active' ORDER By vMake ASC";
	$db_make = $obj->MySQLSelect($sql);

	$sql = "SELECT * from company WHERE eStatus='Active'";
	$db_company = $obj->MySQLSelect($sql);

	if (isset($_POST['submit'])) {
		
		if(SITE_TYPE=='Demo' && $id != '')
		{
			$_SESSION['success'] = 2;
			header("Location:vehicles.php?id=".$id);exit;
		}
		require_once("library/validation.class.php");
		$validobj = new validation();
		
		
		if(empty($_REQUEST['vCarType'])) {
			$validobj->add_fields($_POST['vCarType'], 'req', 'You must select at least one '.$langage_lbl_admin["LBL_CAR_TXT_ADMIN"].' type!');
		}
		$error = $validobj->validate();
		
	if ($error) {
        $success = 3;
        $newError = $error;
        //exit;
    }
	else{
		
		if($APP_TYPE == 'UberX'){
			$vLicencePlate	= 'My Services';
		}else{
			$vLicencePlate = $vLicencePlate;
		}
		$q = "INSERT INTO ";
		$where = '';
		//echo "<pre>";print_R($_REQUEST);exit;

		if ($action == 'Edit') {
			$str = ' ';
		} else {
			$eStatus = 'Active';
		}

		$cartype = implode(",", $_REQUEST['vCarType']);
		if ($id != '') {
			$q = "UPDATE ";
			$where = " WHERE `iDriverVehicleId` = '" . $id . "'";
		}
		$query = $q . " `" . $tbl_name . "` SET
		`iModelId` = '" . $iModelId . "',
		`vLicencePlate` = '" . $vLicencePlate . "',
		`iYear` = '" . $iYear . "',
		`iMakeId` = '" . $iMakeId . "',
		`iCompanyId` = '" . $iCompanyId . "',
		`iDriverId` = '" . $iDriverId . "',
		`eStatus` = 'Active',
		`vCarType` = '" . $cartype . "' $str"
		. $where;
		$obj->sql_query($query);
		//echo"<pre>";print_r($query);exit;
		
		if($id != "" && $db_mdl[0]['eStatus'] != $eStatus) {
			//echo $db_mdl[0]['eStatus']; die;
			if($SEND_TAXI_EMAIL_ON_CHANGE == 'Yes') {
				$sql23 = "SELECT m.vMake, md.vTitle,rd.vEmail, rd.vName, rd.vLastName, c.vName as companyFirstName
					FROM driver_vehicle dv, register_driver rd, make m, model md, company c
					WHERE dv.eStatus != 'Deleted' AND dv.iDriverId = rd.iDriverId  AND dv.iCompanyId = c.iCompanyId AND dv.iModelId = md.iModelId AND dv.iMakeId = m.iMakeId AND dv.iDriverVehicleId = '".$id."'";
				$data_email_drv = $obj->MySQLSelect($sql23);
				$maildata['EMAIL'] =$data_email_drv[0]['vEmail'];
				$maildata['NAME'] = $data_email_drv[0]['vName'];
				//$maildata['LAST_NAME'] = $data_drv[0]['companyFirstName'];
				$maildata['DETAIL']="Your ".$langage_lbl_admin['LBL_TEXI_ADMIN']." ".$data_email_drv[0]['vTitle']." For COMPANY ".$data_email_drv[0]['companyFirstName'] ." is temporarly ".$eStatus;
				$generalobj->send_email_user("ACCOUNT_STATUS",$maildata);
			}
		}

		$id = ($id != '') ? $id : $obj->GetInsertId();

		if(!empty($fAmount)){
			
			$amt_man=$fAmount;
			//echo "<pre>";print_r($_POST);print_r($vCarType);print_r($fAmount);exit;
			// for($a=0;$a<count($vCarType);$a++)
			// {$type=$vCarType[$a];
				// foreach($amt_man as $key1=>$value1)	
				// {
					// if($key1==$type && $value1 == "")
					// {
						// $error_msg="Please Enter Amount.";
						// header("Location:add_services.php?success=2&error_msg=".$error_msg);
						// exit;}
					// }
			// }
		
			$sql = "select iServProAmntId,iDriverVehicleId from ".$tbl_name1." where iDriverVehicleId = '" . $db_drv_veh[0]['iDriverVehicleId'] . "' ";
			$db_drv_price=$obj->MySQLSelect($sql);
			//echo "<pre>";print_r($db_drv_veh);//exit;
			if(count($db_drv_price) > 0){
				$sql="delete from ".$tbl_name1." where iDriverVehicleId='".$db_drv_price[0]['iDriverVehicleId']."'";
				$obj->sql_query($sql);	
			}
			
			foreach($amt_man as $key=>$value)
			{
				if($value != ""){
					$q = "Insert Into ";
					$query = $q . " `" . $tbl_name1 . "` SET
					`iDriverVehicleId` = '" . $db_drv_veh[0]['iDriverVehicleId'] . "',
					`iVehicleTypeId` = '" . $key . "',
					`fAmount` = '" . $value . "'";  
					$db_parti_price=$obj->sql_query($query);
				}
			}
			
		}


		if($action=="Add")
		{
			$sql="SELECT * FROM company WHERE iCompanyId = '" . $iCompanyId . "'";
			$db_compny = $obj->MySQLSelect($sql);

			$sql="SELECT * FROM register_driver WHERE iDriverId = '" . $iDriverId . "'";
			$db_status = $obj->MySQLSelect($sql);

			$maildata['EMAIL'] =$db_status[0]['vEmail'];
			$maildata['NAME'] = $db_status[0]['vName']." ".$db_status[0]['vLastName'];
			//$maildata['LAST_NAME'] = $db_compny[0]['vName'];
			//$maildata['DETAIL']="Your Vehicle is Added For ".$db_compny[0]['vCompany']." and will process your document and activate your account ";
			$maildata['DETAIL']="Thanks for adding your ".$langage_lbl_admin['LBL_TEXI_ADMIN'].".<br />We will soon verify and check it's documentation and proceed ahead with activating your account.<br />We will notify you once your account become active and you can then take ".$langage_lbl_admin['LBL_RIDE_TXT_ADMIN']." with ". $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'].".";
			$generalobj->send_email_user("VEHICLE_BOOKING",$maildata);
		}
		//header("Location:vehicles.php?id=" . $id . '&success=3');
		if ($action == "Add") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin["LBL_TEXI_ADMIN"].' Inserted Successfully.';
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin["LBL_TEXI_ADMIN"].' Updated Successfully.';
        }
        header("location:".$backlink);
	}
	}

	// for Edit
	if ($action == 'Edit') {
		//$sql = "SELECT * from  $tbl_name where iDriverVehicleId = '" . $id . "'";
		$sql = "SELECT t.*,t1.* from  $tbl_name as t left join $tbl_name1 t1
				on t.iDriverVehicleId=t1.iDriverVehicleId
				where t.iDriverVehicleId = '" . $id . "'";
		$db_data = $obj->MySQLSelect($sql);
		$vLabel = $id;
		if (count($db_data) > 0) {
			foreach ($db_data as $key => $value) {
				$iMakeId = $value['iMakeId'];
				$iModelId = $value['iModelId'];
				$vLicencePlate = $value['vLicencePlate'];
				$iYear = $value['iYear'];
				$eCarX = $value['eCarX'];
				$eCarGo = $value['eCarGo'];
				$iDriverId = $value['iDriverId'];
				$vCarType = $value['vCarType'];
				$iCompanyId=$value['iCompanyId'];
				$eStatus=$value['eStatus'];
				$fAmount[$value['iVehicleTypeId']]=$value['fAmount'];
			}
		}
	}
	 $vCarTyp = explode(",", $vCarType);
	//print_r($vCarTyp); exit;

	$Vehicle_type_name = ($APP_TYPE == 'Delivery')? 'Deliver':$APP_TYPE ;	
	if($Vehicle_type_name == "Ride-Delivery"){
		$vehicle_type_sql = "SELECT * from  vehicle_type where(eType ='Ride' or eType ='Deliver') AND iCountryId='-1'";
		$vehicle_type_data = $obj->MySQLSelect($vehicle_type_sql);
	}else{
		if($Vehicle_type_name == 'UberX'){

/*			$vehicle_type_sql = "SELECT vt.vVehicleType,vc.iParentId,vc.vCategory_".$default_lang.",vc.iVehicleCategoryId from  vehicle_type as vt  left join vehicle_category as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId where vt.eType='".$Vehicle_type_name."' GROUP BY vc.iVehicleCategoryId";

			$vehicle_type_dataOld = $obj->MySQLSelect($vehicle_type_sql);

			$vehicle_type_data = array();

			$i = 0;

			foreach($vehicle_type_dataOld as $vData) {

				$vehicle_type_sql1 = "SELECT vt.*,vc.* from  vehicle_type as vt  left join vehicle_category as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId where vt.eType='".$Vehicle_type_name."' and vc.iVehicleCategoryId = '".$vData['iVehicleCategoryId']."'";

				$vehicle_type_dataNew = $obj->MySQLSelect($vehicle_type_sql1);


				$vehicle_type_data[$i] = $vData;

				$vehicle_type_data[$i]['newData'] = $vehicle_type_dataNew;

				$i++;

			}*/

				$userSQL = "SELECT c.iCountryId from register_driver AS rd LEFT JOIN country AS c ON c.vCountryCode=rd.vCountry where rd.iDriverId='".$iDriverId."'";
				$drivers = $obj->MySQLSelect($userSQL);
				$iCountryId = $drivers[0]['iCountryId'];
				
				$getvehiclecat = "SELECT vc.iVehicleCategoryId, vc.vCategory_EN as main_cat FROM vehicle_category as vc WHERE vc.eStatus='Active' AND vc.iParentId='0'";
				$vehicle_type_data = $obj->MySQLSelect($getvehiclecat);
				$i = 0;
				foreach ($vehicle_type_data as $key => $val) {
					$vehicle_type_sql = "SELECT vt.vVehicleType,vc.iParentId,vc.vCategory_".$_SESSION['sess_lang'].",vc.iVehicleCategoryId from  vehicle_type as vt  left join vehicle_category as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId where vt.eType='".$Vehicle_type_name."' AND vc.iParentId ='".$val['iVehicleCategoryId']."'  AND vc.eStatus='Active' GROUP BY vc.iVehicleCategoryId";
					$vehicle_type_dataOld = $obj->MySQLSelect($vehicle_type_sql);
					$vehicle_type_data[$i]['SubCategory'] = $vehicle_type_dataOld;
					$j = 0;
					foreach ($vehicle_type_dataOld as $subkey => $subvalue) {
						$vehicle_type_sql1 = "SELECT vt.*,vc.*,lm.vLocationName from  vehicle_type as vt  left join vehicle_category as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId left join location_master as lm ON lm.iLocationId = vt.iLocationid where vt.eType='".$Vehicle_type_name."' and vc.iVehicleCategoryId = '".$subvalue['iVehicleCategoryId']."' AND (lm.iCountryId='".$iCountryId."' || vt.iLocationid='-1')";
						$vehicle_type_dataNew = $obj->MySQLSelect($vehicle_type_sql1);
						$vehicle_type_data[$i]['SubCategory'][$j]['VehicleType'] = $vehicle_type_dataNew;
						$j++;
					}

					$i++;
				} 
		}else{
			$vehicle_type_sql = "SELECT * from  vehicle_type where eType='".$Vehicle_type_name."' AND iCountryId='-1'";		
			$vehicle_type_data = $obj->MySQLSelect($vehicle_type_sql);
		}
	}
	//echo "<pre>"; print_r($vehicle_type_data);  exit;
?>
<!DOCTYPE html>
<html lang="en">

	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title><?=$SITE_NAME?> |  <?php  echo $langage_lbl_admin['LBL_VEHICLE_TXT_ADMIN'];?> <?= $action; ?></title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<meta content="" name="keywords" />
		<meta content="" name="description" />
		<meta content="" name="author" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

		<?php  include_once('global_files.php');?>
		<!-- On OFF switch -->
		<link href="../assets/css/jquery-ui.css" rel="stylesheet" />
		<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
		<link rel="stylesheet" href="../assets/validation/validatrix.css" />
		<style>
		.car-type-custom fieldset {
			margin: 0 0 30px;padding: 15px; border: 1px solid #e5e5e5;float: left;position: relative;width: 100%;
		}
		.car-type-custom legend {
			border: medium none;  float: left; left: 20px;margin: 0;padding: 0;position: absolute;top: -20px;width: 100%;
		}
		.car-type-custom strong {
			background: #ffffff none repeat scroll 0 0; float: left;font-size: 18px; margin: 0;padding: 5px 10px;text-align: left;width: auto;
		}
		.car-type-custom fieldset li b {font-size: 16px; width: 320px;float: left;font-weight: normal;margin: 10px 0 0; padding: 0;
		}
		.car-type-custom ul li {
		    width: 100%;
		    float: left;
		}
		.add-services-hatch .make-switch {
		    float: left;
		}
		.add-services-hatch ul li .form-control {
		    float: left;
	  	  	width: 200px;
		}
		.add-services-hatch .hatchback-search {
		    float: left;
		    margin: 3px 0 0 10px;
		    width: 350px;
		}
		.add-services-hatch ul li label.fare_type {
		    float: left;
		    font-weight: normal;
		    margin: 7px 0 0 10px;
		    font-weight: 600;
		}
		</style>
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
							<h2><?= $action." ".$langage_lbl_admin['LBL_TEXI_ADMIN'];?></h2>
							<a href="Driver.php" class="back_link">
								<input type="button" value="<?=$langage_lbl_admin['LBL_BACK_TAXI_LISTING'];?>" class="add-btn">
							</a>
						</div>
					</div>
					<hr />
					<div class="body-div">
						<div class="form-group">
							<?php  if ($success == 3) {?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php  print_r($error); ?>
                            </div><br/>
                            <?php } ?>
							<form name="vehicle_form" id="vehicle_form" method="post" action="">	
							<input type="hidden" name="iDriverId"  value="<?= $iDriverId?>"/>
							<input type="hidden" name="iCompanyId"  value="<?= $iCompanyId?>"/>
							<input type="hidden" name="iMakeId"  value="<?= $iMakeId?>"/>
							<input type="hidden" name="iModelId"  value="<?= $iModelId?>"/>
							<input type="hidden" name="iYear"  value="<?= $iYear?>"/>
							<input type="hidden" name="vLicencePlate"  value="My Services"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="driver.php"/>
								<div class="row">
									<div class="col-lg-12">
										<label><?=$langage_lbl_admin['LBL_TEXI_ADMIN'];?> Type <span class="red">*</span></label>
									</div>
								</div>
								<div class="checkbox-group required add-services-hatch car-type-custom">
								<ul>
								<?php 
									foreach ($vehicle_type_data as $key => $value) {
										foreach ($value['SubCategory'] as $Vehicle_Type) {
											
											if(!empty($Vehicle_Type['VehicleType'])) { ?>
										<fieldset>
											<?php 
											if($Vehicle_type_name =='UberX'){
												$vname = $Vehicle_Type['vCategory_'.$_SESSION['sess_lang']];
												$vehicle_Name = $Vehicle_Type['vVehicleType'];
											}else{
												$vname= $Vehicle_Type['vVehicleType'];	
											}
											$iParentcatId = $Vehicle_Type['iParentId'];
											$sql_query = "SELECT ePriceType FROM vehicle_category WHERE iVehicleCategoryId = '".$iParentcatId."' ";
											$ePricetype_data = $obj->MySQLSelect($sql_query);
											$ePricetype = $ePricetype_data[0]['ePriceType'];
											 ?>
											 
											<legend>
												<strong><?php  echo $vname;?></strong>
											</legend>
										
											<?php  foreach($Vehicle_Type['VehicleType'] as $val) {
												if($val['eFareType'] == 'Fixed'){
													$eFareType = 'Fixed';
													$fAmount_old = $val['fFixedFare'];
												} else if($val['eFareType'] == 'Hourly'){
													$eFareType = 'Per hour'; 
													$fAmount_old = $val['fPricePerHour'];
												}else{
													$eFareType = '';
													$fAmount_old = '';
												
												}
									  			$vehicle_typeName =$val['vVehicleType_'.$_SESSION['sess_lang']];

									  		if(!empty($val['vLocationName'])) {
												$localization = '(Location : '.$val["vLocationName"].')';
											} else {
												$localization = '';
											}
									  		?>
												<li style="list-style: outside none none;">
													<b><?php  echo $vehicle_typeName;?><br/>
														<span style="font-size: 12px;"><?php  echo $localization;?></span>
													</b>
													<div class="make-switch" data-on="success" data-off="warning">
														<input type="checkbox" class="chk" name="vCarType[]" id="vCarType_<?=$val['iVehicleTypeId'] ?>" <?php  if($ePricetype == "Provider"){ ?>onchange="check_box_value(this.value);" <?php  } ?> <?php  if(in_array($val['iVehicleTypeId'],$vCarTyp)){?>checked<?php  } ?> value="<?=$val['iVehicleTypeId'] ?>"/>
													</div>
													<?php  
													if($ePricetype == "Provider"){
														$p001="style='display:none;'";
														if(in_array($val['iVehicleTypeId'],$vCarTyp)){
															$p001="style='display:block;'";
														}
														$fAmount_new = $fAmount[$val['iVehicleTypeId']];
														$famount_val = (empty($fAmount_new)) ? $fAmount_old : $fAmount_new ;
														?>
													<div class="hatchback-search" id="amt1_<?=$val['iVehicleTypeId'] ?>" <?php  echo $p001;?>>
														<input type="hidden" name="desc" id="desc_<?=$val['iVehicleTypeId']?>" value="<?=$val['vVehicleType_'.$default_lang] ?>">
														<?php  if($val['eFareType'] != 'Regular'){ ?>	
														<input class="form-control" type="text" name="fAmount[<?=$val['iVehicleTypeId']?>]" value="<?=$famount_val;?>" placeholder="Enter Amount for <?=$val['vVehicleType_'.$default_lang] ?>" id="fAmount_<?=$val['iVehicleTypeId']?>" maxlength="10"><label class="fare_type"><?php  echo $eFareType;?></label>
														</div>
													<?php  	}
													}
													?>
												</li>
											<?php  } ?>
										</fieldset>
								<?php  }
									}
								} ?>
								</ul>
								<!-- <div class="row" id="vehicleTypes001">
								</div> -->
							</div>
							<div class="row" style="display: none;">
								<div class="col-lg-12">
								  <label>Status</label>
								</div>
								<div class="col-lg-6">
								  <div class="make-switch" data-on="success" data-off="warning">
									   <input type="checkbox" name="eStatus" id="eStatus" <?= ($id != '' && $eStatus == 'Inactive') ? '' : 'checked'; ?> />
								  </div>
								</div>
							</div>
							<div class="clear"></div>
								<div class="row">
                                    <div class="col-lg-12">
                                        <input type="submit" class="btn btn-default" name="submit" id="submit" value="<?= $action." ".$langage_lbl_admin['LBL_TEXI_ADMIN']; ?>" onclick="return check_empty();">
                                        <a href="javascript:void(0);" onclick="reset_form('vehicle_form');" class="btn btn-default">Reset</a>
                                        <a href="vehicles.php" class="btn btn-default back_link">Cancel</a>
                                    </div>
                                </div>
							</form>
						</div>
					</div>
                    <div style="clear:both;"></div>
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>
		<!--END MAIN WRAPPER -->


		<?php  include_once('footer.php');?>
		<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
	</body>
	<!-- END BODY-->
</html>
<?php  if ($action == 'Edit') { ?>
	<script>
		window.onload = function () {
			get_model('<?php  echo $db_mdl[0]['iMakeId']; ?>', '<?php  echo $db_mdl[0]['iModelId']; ?>');
			get_driver('<?php  echo $iCompanyId; ?>', '<?php  echo $iDriverId; ?>');
			get_vehicleType('<?php  echo $iDriverId; ?>','<?php  echo $vCarType; ?>');
		};
	</script>
<?php } ?>
<script>

$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){
		referrer =  document.referrer;	
		//alert(referrer);
	}else { 
		referrer = $("#previousLink").val();
	}
	if(referrer == "") {
		referrer = "vehicles.php";
	}else {
		$("#backlink").val(referrer);
	}
	$(".back_link").attr('href',referrer);
});

function get_model(model, modelid) {
	$("#carmdl").html('Wait...');
	var request = $.ajax({
		type: "POST",
		url: '../ajax_find_model.php',
		data: "action=get_model&model=" + model + "&iModelId=" + modelid,
		success: function (data) {
			$("#carmdl").html(data);
		}
	});
	request.fail(function (jqXHR, textStatus) {
		alert("Request failed: " + textStatus);
	});
}
function get_driver(company, companyid) {
	$("#driver").html('Wait...');
	var request = $.ajax({
		type: "POST",
		url: 'ajax_find_driver.php',
		data: "action=get_driver&company=" + company + "&iDriverId=" + companyid,
		success: function (data) {
			$("#driver").html(data);
		}
	});

	request.fail(function (jqXHR, textStatus) {
		alert("Request failed: " + textStatus);
	});
}

function get_vehicleType(iDriverId,selected) {
	$("#vehicleTypes001").html('Wait...');
	var request = $.ajax({
		type: "POST",
		url: '../ajax_find_vehicleType.php',
		data: "iDriverId=" + iDriverId +"&selected="+selected,
		success: function (data) {
			$("#vehicleTypes001").html(data);
		}
	});

	request.fail(function (jqXHR, textStatus) {
		alert("Request failed: " + textStatus);
	});
}

function check_licence_plate(plate,id1=''){
var request= $.ajax({
	type: "POST",
	url: '../ajax_find_plate.php',
	data: "plate="+plate+"&id="+id1,
	success: function (data){			
		if($.trim(data) == 'yes') {
			$('input[type="submit"]').removeAttr('disabled');
			$("#plate_warning").html("");
		}else {
			$("#plate_warning").html(data);
			$('input[type="submit"]').attr('disabled','disabled');
		}
	}
	});
}
function check_box_value(val1)
{
	if($('#vCarType_'+val1).is(':checked'))
	{
		$("#amt1_"+val1).show();
		$("#fAmount_"+val1).focus();
	}else{
		$("#amt1_"+val1).hide();
	}	
}
function check_empty()
{	
	var err=0;
	$("input[type=checkbox]:checked").each ( function() {
		var tmp="fAmount_"+$(this).val();
		var tmp1="desc_"+$(this).val();
		var tmp1_val=$("#"+tmp1).val();

		if ( $("#"+tmp).val() == "" )
		{
			alert('Please Enter Amount for '+tmp1_val+'.');
			$("#"+tmp).focus();
			err=1;
			return false;
		}
	});
	if(err == 1)
	{
		return false;
	}else{
		document.vehicle_form.submit();
	}	
}
</script>