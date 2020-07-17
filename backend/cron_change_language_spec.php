<?php 
set_time_limit(0);
$lang_trans = "1";
include_once("common.php");
include_once(TPATH_CLASS.'class.general.php');
include_once(TPATH_CLASS.'configuration.php');
include_once('generalFunctions.php');

$sql = "select * from language_label where vCode='EN' order by LanguageLabelId limit 0,200";
$data_LanguageLabel = $obj->MySQLSelect($sql);
$vCode = "PT";
$vAPILangCode = "ru";

if(count($data_LanguageLabel)>0){
    
   for($i=0;$i<count($data_LanguageLabel);$i++){
   
	$englishLabelValue = $data_LanguageLabel[$i]['vValue'];  

	$url = 'http://api.mymemory.translated.net/get?q='.urlencode($englishLabelValue).'&de=joshidarshil382@gmail.com&langpair=en|'.$vAPILangCode;
	$result = file_get_contents($url);
	$finalResult = json_decode($result);
	$getText = $finalResult->responseData;
	$responseStatus=$finalResult->responseStatus;
	$quotaFinished=$finalResult->quotaFinished;
	$translatedText = $obj->SqlEscapeString($getText->translatedText);
	if($responseStatus != "200"){
		$translatedText=$englishLabelValue;
	}
	$updateQuery = "UPDATE language_label SET vValue ='".$translatedText."' WHERE vCode='".$vCode."' AND vLabel='".$data_LanguageLabel[$i]['vLabel']."'";
	$db_cab = $obj->sql_query($updateQuery);

   } 
}

?>