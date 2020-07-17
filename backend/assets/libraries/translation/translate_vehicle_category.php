<?php 
// DEMO LINK http://192.168.1.131/cubetaxidev/translate_vehicle_category.php?vLangCode=da
	include_once("../../../common.php");
	include_once(TPATH_CLASS.'class.general.php');
	include_once(TPATH_CLASS.'configuration.php');
	include_once('../../../generalFunctions.php');

	$LangCode = isset($_REQUEST['vLangCode'])?$_REQUEST['vLangCode']:'';
	$sql = "SELECT iLanguageMasId,vTitle,vCode,vLangCode FROM language_master WHERE vCode != 'EN' && vLangCode = '".$LangCode."'";
	$data_language_master = $obj->MySQLSelect($sql); 
	$vCode = $data_language_master[0]['vCode'];

	$sql1 = "SELECT iVehicleCategoryId,vCategory_EN,vCategory_".$vCode.",tCategoryDesc_EN,tCategoryDesc_".$vCode." FROM vehicle_category";
	$data_LanguageLabel = $obj->MySQLSelect($sql1);


	if(count($data_LanguageLabel)>0)
	{
		foreach ($data_LanguageLabel as $key => $value) {
			$englishLabelValue = $value['vCategory_EN'];
			$iVehicleCategoryId = $value['iVehicleCategoryId'];
			$vAPILangCode	= $data_language_master[0]['vLangCode'];
			$translateValue = $value['vCategory_'.$vCode];  

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
				$updateQuery = "UPDATE vehicle_category SET vCategory_".$vCode." ='".$translatedText."' WHERE iVehicleCategoryId ='".$iVehicleCategoryId."'";
				$db_cab = $obj->sql_query($updateQuery);
				
			} 
		}
		echo "Vehicle Category Translation Successfully.";
	}