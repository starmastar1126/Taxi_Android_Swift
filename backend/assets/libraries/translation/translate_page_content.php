<?php 
// DEMO LINK http://192.168.1.131/cubetaxidev/translate_page_content.php?vLangCode=fr
//http://192.168.1.131/cubetaxidev/assets/libraries/translation/translate_page_content.php?vLangCode=zh-CN

	include_once("../../../common.php");
	include_once(TPATH_CLASS.'class.general.php');
	include_once(TPATH_CLASS.'configuration.php');
	include_once('../../../generalFunctions.php');

	$LangCode = isset($_REQUEST['vLangCode'])?$_REQUEST['vLangCode']:'';
	$sql = "SELECT iLanguageMasId,vTitle,vCode,vLangCode FROM language_master WHERE vCode != 'EN' && vLangCode = '".$LangCode."'";
	$data_language_master = $obj->MySQLSelect($sql); 
	$vCode = $data_language_master[0]['vCode'];

	$sql1 = "SELECT iPageId,vPageName,vPageTitle_EN,vPageTitle_".$vCode.",tPageDesc_EN,tPageDesc_".$vCode." FROM pages";
	$data = $obj->MySQLSelect($sql1);

	if(count($data)>0)
	{
		foreach ($data as $key => $value) {
			$englishLabelValue = $value['vPageTitle_EN'];
			//$englishDescValue = $value['tPageDesc_EN'];
			$iPageId = $value['iPageId'];
			$vAPILangCode	= $data_language_master[0]['vLangCode'];
			$translateValue = $value['vPageTitle_'.$vCode];
			//$translateValueDesc = $value['tPageDesc_'.$vCode];  

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
				$updateQuery = "UPDATE pages SET vPageTitle_".$vCode." ='".$translatedText."' WHERE iPageId ='".$iPageId."'";
				$db_data = $obj->sql_query($updateQuery);
			} 
			/*if($translateValueDesc == '' && $vAPILangCode != '') {

				echo $urlDesc = 'http://api.mymemory.translated.net/get?q='.urlencode($englishDescValue).'&de=harshilmehta1982@gmail.com&langpair=en|'.$vAPILangCode;
				echo"<br/>";
				$resultDesc = file_get_contents($urlDesc);
				$finalResultDesc = json_decode($resultDesc);
				$getText = $finalResultDesc->responseData;
				$responseStatus=$finalResultDesc->responseStatus;
				$quotaFinished=$finalResultDesc->quotaFinished;
				$translatedTextDesc = $obj->SqlEscapeString($getText->translatedText);
				if($responseStatus != "200"){
					$translatedTextDesc=$englishDescValue;
				}
				$updateDescQuery = "UPDATE pages SET tPageDesc_".$vCode." ='".$translatedTextDesc."' WHERE iPageId ='".$iPageId."'";
				$db_data_desc = $obj->sql_query($updateDescQuery);
			} */
		}
		echo "Page Content Translation Successfully.";
	}