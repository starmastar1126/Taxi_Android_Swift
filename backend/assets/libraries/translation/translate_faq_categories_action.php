<?php 
// DEMO LINK http://192.168.1.131/cubetaxidev/translate_faq_categories_action.php?vLangCode=es
	include_once("common.php");
	include_once(TPATH_CLASS.'class.general.php');
	include_once(TPATH_CLASS.'configuration.php');
	include_once('generalFunctions.php');

	$LangCode = isset($_REQUEST['vLangCode'])?$_REQUEST['vLangCode']:'';
	$sql = "SELECT iLanguageMasId,vTitle,vCode,vLangCode FROM language_master WHERE vCode != 'EN' && vLangCode = '".$LangCode."'";
	$data_language_master = $obj->MySQLSelect($sql); 
	$vCode = $data_language_master[0]['vCode'];

	$sql1 = "SELECT * FROM faq_categories WHERE vCode = 'EN'";
	$data = $obj->MySQLSelect($sql1);

	if(count($data)>0)
	{
		foreach ($data as $key => $value) {

			$englishLabelValue = $value['vTitle'];
			$iFaqcategoryId = $value['iFaqcategoryId'];
			$vAPILangCode	= $data_language_master[0]['vLangCode'];
			$iUniqueId = $value['iUniqueId'];  
			$iDisplayOrder = $value['iDisplayOrder'];  

			if($iUniqueId != '' && $vAPILangCode != '') {

				$url = 'http://api.mymemory.translated.net/get?q='.urlencode($englishLabelValue).'&de=harshilmehta1982@gmail.com&langpair=en|'.$vAPILangCode;

				$result = file_get_contents($url);
				$finalResult = json_decode($result);
				$getText = $finalResult->responseData;
				$responseStatus=$finalResult->responseStatus;
				$quotaFinished=$finalResult->quotaFinished;
				$translatedText = $obj->SqlEscapeString($getText->translatedText);
				if($responseStatus != "200"){
					$translatedText=$englishLabelValue;
				}
				$checkdata = "SELECT iFaqcategoryId FROM `faq_categories` WHERE vCode = '".$vCode."' AND iUniqueId = '".$iUniqueId."'";
				$getdata = $obj->MySQLSelect($checkdata);

				if(empty($getdata)){
					$updateQuery = "INSERT INTO `faq_categories`(`eStatus`, `iDisplayOrder`, `vTitle`, `vImage`, `vCode`, `iUniqueId`) VALUES ('Active','".$iDisplayOrder."','".$translatedText."','','".$vCode."','".$iUniqueId."')";
					$db_data = $obj->sql_query($updateQuery);
				} else {
					$updateQuery = "UPDATE `faq_categories` SET `vTitle`='".$translatedText."' WHERE iFaqcategoryId = '".$getdata[0]['iFaqcategoryId']."'";
					$db_data = $obj->sql_query($updateQuery);
				}
			} 
			
		}
		echo "Faq Category Translation Successfully.";
	}