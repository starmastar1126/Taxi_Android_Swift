<?php 
include_once('../common.php');
if(!isset($generalobjAdmin)) {
	require_once(TPATH_CLASS."class.general_admin.php");
	$generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$iDriverId = isset($_REQUEST['id']) ? $_REQUEST['id'] : $_SESSION['sess_iUserId'];
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$action = ($iDriverId != '') ? 'Edit' : 'Add';

$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
$previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
    
$tbl_name = 'driver_manage_timing';
$script = 'My Services';
$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
$hours = array('01-02','02-03','03-04','04-05','05-06','06-07','07-08','08-09','09-10','10-11','11-12','12-13','13-14','14-15','15-16','16-17','17-18','18-19','19-20','20-21','21-22','22-23','23-24','12-01');
$hours_display = array('01 AM-02 AM','02 AM-03 AM','03 AM-04 AM','04 AM-05 AM','05 AM-06 AM','06 AM-07 AM','07 AM-08 AM','08 AM-09 AM','09 AM-10 AM','10 AM-11 AM','11 AM-12 PM','12 PM-01 PM','01 PM- 02PM','02 PM-03 PM','03 PM-04 PM','04 PM-05 PM','05 PM-06 PM','06 PM-07 PM','07 PM-08 PM','08 PM-09 PM','09 PM-10 PM','10 PM- 11 PM','11 PM-12 AM','12 AM-01 AM');
if (isset($_POST['submit1'])) {
	/*echo "<pre>";
	print_r($_POST);exit;*/
	if(SITE_TYPE=='Demo' && $action=='Edit')
	{
		$_SESSION['success'] = '2';
	    $_SESSION['var_msg'] = $langage_lbl['LBL_EDIT_DELETE_RECORD'];
		header("Location:driver.php");
		exit;
	}
	$data = $_POST['hours_value'];
	$iDriverId = isset($_POST['iDriverId']) ? $_POST['iDriverId'] : '';

	$sql = "select iDriverId from ".$tbl_name." where iDriverId = '" . $iDriverId . "' ";
	$db_drv_data=$obj->MySQLSelect($sql);
	if(count($db_drv_data) > 0){
		$sql="delete from ".$tbl_name." where iDriverId='".$iDriverId."'";
		$obj->sql_query($sql);	
	}

	if(!empty($data)) {
		foreach ($data as $key => $value) {
			$vAvailableTimes = implode(",", $value);
			$dayname = ucfirst($key);			
			$q = "INSERT INTO";
			$where = "";

			$query = $q . " `" . $tbl_name . "` SET		
			`iDriverId` = '" . $iDriverId . "',
			`vDay` = '" . $dayname . "',		
			`vAvailableTimes` = '" . $vAvailableTimes . "',
			`dAddedDate` = NOW(),
			`eStatus` = 'Active'"
			. $where;
			$obj->sql_query($query);
		}
		if ($action == "Edit") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Availability Updated Successfully.';
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Availability Inserted Successfully.';
        }
		header("Location:".$backlink);
		exit;
	}
}
if($action = "Edit") {
	$data_query ="SELECT * FROM `" . $tbl_name . "` WHERE iDriverId='".$iDriverId."'";
	$db_data=$obj->MySQLSelect($data_query);
	if (count($db_data) > 0) {
		foreach ($db_data as $key => $value) {
			$iDriverId = $value['iDriverId'];
			$vDay[] = $value['vDay'];
			$vAvailableTimes = $value['vAvailableTimes'];
			$vAvailableTime[$value['vDay']] = explode(",", $vAvailableTimes);
		}
	}
}
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
	    .add-car-services-hatch fieldset legend strong{
	    	text-transform: uppercase;
	    }
	    .small_box {
		    display: inline-block;
		    padding: 20px;
		    border: 1px solid #e5e5e5;
		    margin: 9px;
		}
		.add-services-hatch fieldset {
			border: 1px solid #e5e5e5;
		    padding: 15px;
		    margin: 0 0 30px;
		    float: left;
		    width: 100%;
		    position: relative;
		}
		.add-car-services-hatch fieldset legend {
		    margin: 0px;
		    padding: 0px;
		    float: left;
		    border: none;
		    width: 100%;
		    position: absolute;
		    top: -20px;
		    left: 20px;
		}
		.add-car-services-hatch fieldset legend strong {
		    background: #FFFFFF;
		    margin: 0px;
		    padding: 5px 10px;
		    float: left;
		    text-align: left;
		    width: auto;
		    font-size: 20px;
		}
		.add-services-taxi ul li {
		    margin: 0 0 20px;
		    padding: 0px;
		    float: left;
		    width: 100%;
		    list-style-type: none;
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
							<h2><?= $action." ".$langage_lbl_admin['LBL_AVAILABILITY'];?></h2>
							<a href="driver.php" class="back_link">
								<input type="button" value="<?=$langage_lbl_admin['LBL_BACK'];?>" class="add-btn">
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
                            <form name="frm1" method="post" action="">
								<input type="hidden" name="iDriverId" value="<?= $iDriverId; ?>"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="driver.php"/>
				    			<div class="car-type add-car-services-hatch add-services-hatch add-services-taxi">   
						          	<ul>
						          		<?php  foreach ($days as $dayname) {
						          		$name = "hours_value";
						          		$check_data = $vAvailableTime[ucfirst($dayname)];
						          		?>
										<fieldset>
										<legend><strong><?php  echo $dayname;?></strong></legend>
										<li>
											<?php  foreach($hours as $key=>$value) { ?>
											<label style="font-weight: normal;cursor:pointer;">
												<div class="small_box">
												<input type='checkbox' name='<?php  echo $name;?>[<?php  echo $dayname;?>][]' value='<?php  echo $value;?>' <?php  if(!empty($check_data) && in_array($value,$check_data)){?>checked<?php  } ?>>
												<?php  echo $hours_display[$key];?>
											</div>
										</label>
											<?php  } ?>
										</li>
										</fieldset>
										<?php  } ?>
									</ul>
				      				<strong><input type="submit" class="save-vehicle btn btn-default" name="submit1" id="submit1" value="Submit"> </strong>
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
		</script>
	</body>
	<!-- END BODY-->
</html>
