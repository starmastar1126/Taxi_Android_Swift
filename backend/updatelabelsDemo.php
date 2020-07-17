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
    // echo "Sd";exit;
$dataLblArr=array();
$table_name = "language_label_other";

$sql = "SELECT * FROM $table_name WHERE vValue = '' ORDER BY LanguageLabelId DESC LIMIT 0,25";
$data = $obj->MySQLSelect($sql);
//echo "<pre>";print_r($data);exit;

for($i=0;$i<count($data);$i++){
  $vCode = $data[$i]['vCode'];
  $vLabel = $data[$i]['vLabel'];
  $LanguageLabelId = $data[$i]['LanguageLabelId']; 
  
  $sql = "SELECT vLangCode FROM `language_master` where vCode = '".$vCode."'";
	$db_master = $obj->MySQLSelect($sql);
  $vGmapCode = $db_master[0]['vLangCode'];
  
  $sql = "SELECT vValue FROM $table_name WHERE vCode = 'EN' AND vLabel = '".$vLabel."'";
  $Englishdata = $obj->MySQLSelect($sql);
  $vValue = $Englishdata[0]['vValue'];
  
  $url = 'http://api.mymemory.translated.net/get?q='.urlencode($vValue).'&de=harshilmehta1982@gmail.com&langpair=en|'.$vGmapCode;
	$result = file_get_contents($url);
	$finalResult = json_decode($result);
	$getText = $finalResult->responseData;
  $resulttext = $getText->translatedText;
	if($resulttext == ""){
		//$resulttext = $vValue;
	} 
  $where = " LanguageLabelId = '".$LanguageLabelId."'";
	$data_update['vValue']=$resulttext;
	$obj->MySQLQueryPerform($table_name,$data_update,'update',$where);
} 

?>