<?php 
include_once('../common.php');

if(!isset($generalobjAdmin)){
	require_once(TPATH_CLASS."class.general_admin.php");
	$generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$driverId = isset($_REQUEST['driverId']) ? $_REQUEST['driverId'] : '';

$sql = "SELECT iDriverId,vEmail,iCompanyId, CONCAT(vName,' ',vLastName) AS FULLNAME,vLatitude,vLongitude,vServiceLoc,vAvailability,vTripStatus,iTripId,tLastOnline, vImage, vCode, vPhone FROM register_driver WHERE vLatitude !='' AND vLongitude !='' AND iDriverId = '$driverId'";
$db_drivers = $obj->MySQLSelect($sql);
$db_driver = $db_drivers[0];
?>
<ul class="<?php  echo $db_driver['iDriverId']; ?>">
	<li>
		<h3><strong><?php  echo $langage_lbl_admin['LBL_DELIVER_DETAILS']; ?></strong></h3>
		<label><b><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT']; ?> :</b><?php  echo $generalobjAdmin->clearName($db_driver['FULLNAME']); ?></label>
		<label><b><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT']."&nbsp;".$langage_lbl_admin['LBL_EMAIL_TEXT']; ?> :</b><?php  echo $generalobjAdmin->clearEmail($db_driver['vEmail']); ?></label>
		<label><b>Phone No :</b>+<?php  echo $generalobjAdmin->clearPhone($db_driver['vCode'].$db_driver['vPhone']); ?></label>
	</li>
	
	<?php  if($db_driver['vTripStatus'] == 'Active' || $db_driver['vTripStatus'] == 'On Going Trip' || $db_driver['vTripStatus'] == 'Arrived') { 
	
	$sql2 = "SELECT CONCAT(vName,' ',vLastName) as FullName,ru.vPhone, ru.vPhoneCode, t.tSaddress, t.tDaddress FROM trips as t LEFT JOIN register_user as ru on ru.iUserId=t.iUserId WHERE t.iTripId = '".$db_driver['iTripId']."'";
	$db_custs = $obj->MySQLSelect($sql2);
	$db_cust = $db_custs[0];
	?>
	
	<li>
		<h3><strong><?php  echo $langage_lbl_admin['LBL_RIDER_DETAILS']; ?></strong></h3>
		<label><b><?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?> :</b><?php  echo $generalobjAdmin->clearName($db_cust['FullName']); ?></label>
		<label><b>Phone No :</b>+<?php  echo $db_cust['vPhoneCode']; ?><?php  echo $generalobjAdmin->clearPhone($db_cust['vPhone']); ?></label>
	</li>
	
	<?php  } ?>
</ul>
<?php  if($db_driver['vTripStatus'] == 'Active' || $db_driver['vTripStatus'] == 'On Going Trip' || $db_driver['vTripStatus'] == 'Arrived') {  ?>
<div class="map-popup-location">
	<span>
		<h4><?php  echo $langage_lbl_admin['LBL_PICKUP']; ?></h4>
		<p><?php  echo $db_cust['tSaddress']; ?></p>
	</span>
	<?php  if($db_cust['tDaddress'] != "") { ?>
	<span>
		<h4><?php  echo $langage_lbl_admin['LBL_ADMIN_DROPOFF']; ?></h4>
		<p><?php  echo $db_cust['tDaddress']; ?></p>
	</span>
	<?php  } ?>
</div>
<?php  } ?>
<!--span class="button"><a href="javascript:void(0)" onClick="AssignDriver('<?php  //echo $db_driver['iDriverId']; ?>');">Assign Driver</a></span-->