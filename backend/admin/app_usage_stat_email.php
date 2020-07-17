<?php 

include_once('common.php');

if($_REQUEST['todaydate'] != ""){

	$today = $_REQUEST['todaydate'];	

}else{

	$today = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d")-1,date("Y")));

}

require_once("app_usage_stat_print.php");

$mail_content=ob_get_clean();



if($host_system == "uberridedelivery"){

	$subject = "Ride Delivery App. Usage Stats As On: ".date("d-M-Y",strtotime($today));

}elseif($host_system == "ufxforall"){

	$subject = "Service only App. Usage Stats As On: ".date("d-M-Y",strtotime($today));

}elseif($host_system == "uberdelivery"){

	$subject = "Delivery only App. Usage Stats As On: ".date("d-M-Y",strtotime($today));

}elseif($host_system == "carwash"){

	$subject = "Carwash App. Usage Stats As On: ".date("d-M-Y",strtotime($today));

}else{

	$subject = "UberClone App. Usage Stats As On: ".date("d-M-Y",strtotime($today));

}

$generalobj->send_email_smtp("chiragd.esw@gmail.com",$NOREPLY_EMAIL,$EMAIL_FROM_NAME,$subject, $mail_content);

//$generalobj->send_email_smtp("sales@v3cube.com",$NOREPLY_EMAIL,$EMAIL_FROM_NAME,$subject, $mail_content);

?>    

    

