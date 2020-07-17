<?php 
	include_once('include_taxi_webservices.php');
	include_once(TPATH_CLASS.'configuration.php');
	
	require_once('assets/libraries/stripe/config.php');
	require_once('assets/libraries/stripe/stripe-php-2.1.4/lib/Stripe.php');
	require_once('assets/libraries/pubnub/autoloader.php');
	include_once(TPATH_CLASS .'Imagecrop.class.php');
	include_once(TPATH_CLASS .'twilio/Services/Twilio.php');
	include_once('generalFunctions.php');
	include_once('send_invoice_receipt.php');

$dataLblArr=array();

$sql = "SELECT * FROM `language_label` WHERE vCode = 'EN' AND lPage_id='0'";
$data = $obj->MySQLSelect($sql);

for($i=0;$i<count($data);$i++){
  // echo $data[$i]['vLabel']." - ".$data[$i]['vValue'];
  echo "<br>";
  echo "$"."dataLblArr['".$data[$i]['vLabel']."']" . "='".addslashes($data[$i]['vValue'])."';";
                                          // '$'.'dataLblArr'.'['".$data[$i]['vLabel']."'] = '".$data[$i]['vValue']."';
} 


$sql = "SELECT * FROM `language_label_other` WHERE vCode = 'EN' AND lPage_id='0'";
$data_other = $obj->MySQLSelect($sql);

for($i=0;$i<count($data_other);$i++){
  // echo $data[$i]['vLabel']." - ".$data[$i]['vValue'];
  echo "<br>";
  echo "$"."dataLblArr['".$data_other[$i]['vLabel']."']" . "='".addslashes($data_other[$i]['vValue'])."';";
                                          // '$'.'dataLblArr'.'['".$data[$i]['vLabel']."'] = '".$data[$i]['vValue']."';
} 

?>