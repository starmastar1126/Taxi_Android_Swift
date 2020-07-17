<?php 
// DEMO LINK http://192.168.1.131/cubetaxidev/translate_home_driver_content.php?vLangCode=es
	include_once("../../../common.php");
	include_once(TPATH_CLASS.'class.general.php');
	include_once(TPATH_CLASS.'configuration.php');
	include_once('../../../generalFunctions.php');

	$LangCode = isset($_REQUEST['vLangCode'])?$_REQUEST['vLangCode']:'';
	$sql = "SELECT iLanguageMasId,vTitle,vCode,vLangCode FROM language_master WHERE vCode != 'EN' && vLangCode = '".$LangCode."'";
	$data_language_master = $obj->MySQLSelect($sql); 
	$vCode = $data_language_master[0]['vCode'];

	$sql1 = "SELECT iDriverId,vName_EN,vName_".$vCode.",vDesignation_EN,vDesignation_".$vCode." FROM home_driver";
	$data = $obj->MySQLSelect($sql1);

	if(count($data)>0)
	{
		foreach ($data as $key => $value) {
			$englishLabelValue = $value['vName_EN'];
			$englishDescValue = $value['vDesignation_EN'];
			$iDriverId = $value['iDriverId'];
			$vAPILangCode	= $data_language_master[0]['vLangCode'];
			$translateValue = $value['vName_'.$vCode];
			$translateValueDesc = $value['vDesignation_'.$vCode];  

			if($translateValue == '' && $vAPILangCode != '') {

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
				$updateQuery = "UPDATE home_driver SET vName_".$vCode." ='".$translatedText."' WHERE iDriverId ='".$iDriverId."'";
				$db_data = $obj->sql_query($updateQuery);
			} 
			if($translateValueDesc == '' && $vAPILangCode != '') {

				$urlDesc = 'http://api.mymemory.translated.net/get?q='.urlencode($englishDescValue).'&de=harshilmehta1982@gmail.com&langpair=en|'.$vAPILangCode;
				$resultDesc = file_get_contents($urlDesc);
				$finalResultDesc = json_decode($resultDesc);
				$getText = $finalResultDesc->responseData;
				$responseStatus=$finalResultDesc->responseStatus;
				$quotaFinished=$finalResultDesc->quotaFinished;
				$translatedTextDesc = $obj->SqlEscapeString($getText->translatedText);
				if($responseStatus != "200"){
					$translatedTextDesc=$englishDescValue;
				}
				$updateDescQuery = "UPDATE home_driver SET vDesignation_".$vCode." ='".$translatedTextDesc."' WHERE iDriverId ='".$iDriverId."'";
				$db_data_desc = $obj->sql_query($updateDescQuery);
			} 
		}
		echo "Home Driver Content Translation Successfully.";
	}