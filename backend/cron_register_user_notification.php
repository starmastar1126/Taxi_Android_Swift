<?php 
	include_once("common.php");
	global $generalobj;

	$query = "SELECT vEmail, vName, vLastName, iDriverId FROM register_driver WHERE eSentNotification != 'Yes' limit 100";
	$db_user = $obj->MySQLSelect($query);

	if(count($db_user)) {

		$sub = "New Driver Registration on UBER at ".date('M d, Y');
			$msg = "Dear Administrator,"
				."\n\n"
				."You have a new lead on UBER \n";

		foreach ($db_user as $key => $value) {
			
			
			$msg .="Name:- ".$value['vName']." ".$value['vLastName']."\n"
				."Email:- ".$value['vEmail']."\n"
				."\n";
				
			$updateQuery = "UPDATE register_driver set eSentNotification='Yes',dSentNotification='".DATE('Y-m-d')."' WHERE iDriverId = ".$value['iDriverId'];
			$obj->sql_query($updateQuery);
		}
		$msg .="Regards";
		#echo "<pre>";
		#	echo "$sub <br/> $msg";
		mail('vishalb.esw@gmail.com', $sub, $msg);
    #sales@blablacarscript.com
    mail('sales@blablacarscript.com', $sub, $msg);
	}

		
?>