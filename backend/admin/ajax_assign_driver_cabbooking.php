<?php 
	include_once('../common.php');
		
	$tbl_name = 'cab_booking';
	$tbl_name2 = 'register_user';
	
	$driverId = $_REQUEST['driverId'];
	$bookingId = $_REQUEST['bookingId'];
	$eStatus1 = 'Assign';
	
	$q = "UPDATE ";
    $where = " WHERE `iCabBookingId` = '" . $bookingId . "'";
	$query = $q . " ".$tbl_name." SET `iDriverId`='".$driverId."', `eAutoAssign`='No' ,`eStatus`='".$eStatus1."'".$where;
	// echo '<pre>'; print_r($_REQUEST);
    $obj->sql_query($query);
	
	$SQL3 = "SELECT * FROM $tbl_name WHERE iCabBookingId = '$bookingId'";
	$db_bookings = $obj->MySQLSelect($SQL3);
	
	// echo "<pre>"; print_r($db_bookings); die;
	
	$SQL1 = "SELECT vName,vLastName,vEmail FROM $tbl_name2 WHERE iUserId = '".$db_bookings[0]['iUserId']."'";
	$email_exist = $obj->MySQLSelect($SQL1);
	
	$sql2="select vName,vLastName,vEmail from register_driver where iDriverId=".$driverId;
	$driver_db=$obj->MySQLSelect($sql2);
	
	$Data1['vRider']=$email_exist[0]['vName']." ".$email_exist[0]['vLastName'];
	$Data1['vDriver']=$driver_db[0]['vName']." ".$driver_db[0]['vLastName'];
	$Data1['vDriverMail']=$driver_db[0]['vEmail'];
	$Data1['vRiderMail']=$email_exist[0]['vEmail'];
	$Data1['vSourceAddresss']=$db_bookings[0]['vSourceAddresss'];
	$Data1['tDestAddress']=$db_bookings[0]['tDestAddress'];
	$Data1['dBookingdate']=$db_bookings[0]['dBooking_date'];
	$Data1['vBookingNo']=$db_bookings[0]['vBookingNo'];
	
	// echo '<pre>'; print_r($Data1);
	$return = $generalobj->send_email_user("MANUAL_TAXI_DISPATCH_DRIVER",$Data1);
	$return1 = $generalobj->send_email_user("MANUAL_TAXI_DISPATCH_RIDER",$Data1);
	if($return && $return1){
			$success = 1;
	}else{
			$success = 0;
	}
	echo $success; die;
?>