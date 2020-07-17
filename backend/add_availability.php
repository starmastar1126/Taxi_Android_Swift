<?php 
include_once('common.php');
$generalobj->check_member_login();
$abc = 'admin,driver,company';
$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$generalobj->setRole($abc, $url);
$script="My Services";
$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : $_SESSION['sess_iUserId'];
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$action = ($iDriverId != '') ? 'Edit' : 'Add';
$tbl_name = 'driver_manage_timing';
$days = array('monday','tuesday','wednesday','thursday','friday','saturday','sunday');
$days_display = array($langage_lbl['LBL_MONDAY_TXT'],$langage_lbl['LBL_TUESDAY_TXT'],$langage_lbl['LBL_WEDNESDAY_TXT'],$langage_lbl['LBL_THURSDAY_TXT'],$langage_lbl['LBL_FRIDAY_TXT'],$langage_lbl['LBL_SATURDAY_TXT'],$langage_lbl['LBL_SUNDAY_TXT']);
$hours = array('01-02','02-03','03-04','04-05','05-06','06-07','07-08','08-09','09-10','10-11','11-12','12-13','13-14','14-15','15-16','16-17','17-18','18-19','19-20','20-21','21-22','22-23','23-24','12-01');
$hours_display = array('01 AM-02 AM','02 AM-03 AM','03 AM-04 AM','04 AM-05 AM','05 AM-06 AM','06 AM-07 AM','07 AM-08 AM','08 AM-09 AM','09 AM-10 AM','10 AM-11 AM','11 AM-12 PM','12 PM-01 PM','01 PM- 02PM','02 PM-03 PM','03 PM-04 PM','04 PM-05 PM','05 PM-06 PM','06 PM-07 PM','07 PM-08 PM','08 PM-09 PM','09 PM-10 PM','10 PM- 11 PM','11 PM-12 AM','12 AM-01 AM');
if (isset($_POST['submit1'])) {
	//echo "<pre>";
	//print_r($_POST);//exit;
	if(SITE_TYPE=='Demo' && $action=='Edit')
	{
		$error_msg= $langage_lbl['LBL_EDIT_DELETE_RECORD'];
		header("Location:add_availability.php?iDriverId=" .$iDriverId."&error_msg=".$error_msg."&success=2");
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
		header("Location:add_availability.php?iDriverId=" .$iDriverId."&success=1");
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
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_MY_AVAILABILITY'];?></title>
    <!-- Default Top Script and css -->
    <?php  include_once("top/top_script.php");?>
    <!-- End: Default Top Script and css-->
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
	</style>
</head>
<body>
        <!-- home page -->
    <div id="main-uber-page">
    <!-- Left Menu -->
    <?php  include_once("top/left_menu.php");?>
    <!-- End: Left Menu-->
    <!-- Top Menu -->
    <?php  include_once("top/header_topbar.php");?>
    <link rel="stylesheet" href="assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
    <!-- End: Top Menu-->
    <!-- contact page-->
    <div class="page-contant">
	    <div class="page-contant-inner page-trip-detail">
	      	<h2 class="header-page trip-detail driver-detail1"><?=$langage_lbl['LBL_MY_AVAILABILITY'];?>
				<?php  if($APP_TYPE == "UberX" && $_SESSION['sess_user'] == "company"){?>
					<a href="driverlist">
						<img src="assets/img/arrow-white.png" alt="">
						<?=$langage_lbl['LBL_BACK']; ?>
					</a>
				<?php  }?>
			</h2>
	      	<!-- trips detail page -->
	      	<div class="driver-add-vehicle">
	      		<?php  if($success == 1) { ?>
					<div class="alert alert-success alert-dismissable">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						<?=$langage_lbl['LBL_Record_Updated_successfully.']; ?>
					</div>
					<?php  }else if($success == 2){?>
					<div class="alert alert-danger alert-dismissable">
						<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
						<?= isset($_REQUEST['error_msg']) ? $_REQUEST['error_msg'] : ' '; ?>
					</div>
				<?php } ?>
				<form name="frm1" method="post" action="">
					<input type="hidden" name="iDriverId" value="<?= $iDriverId; ?>"/>
	    			<div class="car-type add-car-services-hatch add-services-hatch add-services-taxi">   
			          	<ul>
			          		<?php  foreach ($days as $k => $dayname) {
			          		$name = "hours_value";
			          		$check_data = $vAvailableTime[ucfirst($dayname)];
			          		?>
							<fieldset>
							<legend><strong><?php  echo $days_display[$k]?></strong></legend>
							<li>
								<?php  foreach($hours as $key => $value) { ?>
								<div class="small_box">
									<input type='checkbox' name='<?php  echo $name;?>[<?php  echo $dayname;?>][]' value='<?php  echo $value;?>' <?php  if(!empty($check_data) && in_array($value,$check_data)){?>checked<?php  } ?>><?php  echo $hours_display[$key];?>
								</div>
								<?php  } ?>
							</li>
							</fieldset>
							<?php  } ?>
						</ul>
	      				<strong><input type="submit" class="save-vehicle" name="submit1" id="submit1" value="<?=$langage_lbl['LBL_SUBMIT_BUTTON_TXT']; ?>"> </strong>
	  				</div>
				</form>
	      	</div>
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
    <!-- Footer Script -->
    <?php  include_once('top/footer_script.php');?>
    <!-- End: Footer Script -->
</body>
</html>
