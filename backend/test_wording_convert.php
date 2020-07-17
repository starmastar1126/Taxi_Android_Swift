<?php 
	$lang_trans = "1";
	include_once("common.php");
	include_once(TPATH_CLASS.'class.general.php');
	include_once(TPATH_CLASS.'configuration.php');
	include_once('generalFunctions.php');
	
	$sql = "SELECT * FROM language_master WHERE vCode = 'BS'";
	$data_language_master = $obj->MySQLSelect($sql); 
	/*
	$sql = "SELECT * FROM document_master";
	$data_LanguageLabel = $obj->MySQLSelect($sql);
	 
	if(count($data_LanguageLabel)>0)
	{
		
		for($i=0;$i<count($data_LanguageLabel);$i++)
		{
			
			$englishLabelValue = $data_LanguageLabel[$i]['doc_name_EN'];  
			$doc_masterid = $data_LanguageLabel[$i]['doc_masterid'];  
			
			if(count($data_language_master)>0){
				
				for($j=0;$j<count($data_language_master);$j++){
					
					$vCode 			= $data_language_master[$j]['vCode'];
					$vAPILangCode	= $data_language_master[$j]['vGMapLangCode']; 
					
					$translateValue = $data_LanguageLabel[$i]['doc_name_'.$vCode];  
					
					$pos = strpos($translateValue, '?');
					
					if($pos >= 0 || $translateValue == '') {
					
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
						echo '<BR>'. $updateQuery = "UPDATE document_master SET doc_name_".$vCode." ='".$translatedText."' WHERE doc_masterid ='".$doc_masterid."'";
						$db_cab = $obj->sql_query($updateQuery);
					}
				} 
				
			}
			
		}
	}
	
	*/
	$sql = "SELECT * FROM cancel_reason LIMIT 1";
	$data_LanguageLabel = $obj->MySQLSelect($sql);
	 
	if(count($data_LanguageLabel)>0)
	{
		
		for($i=0;$i<count($data_LanguageLabel);$i++)
		{
			
			$englishLabelValue = $data_LanguageLabel[$i]['vTitle_EN'];  
			$iCancelReasonId = $data_LanguageLabel[$i]['iCancelReasonId'];  
			
			if(count($data_language_master)>0){
				
				for($j=0;$j<count($data_language_master);$j++){
					
					$vCode 			= $data_language_master[$j]['vCode'];
					$vAPILangCode	= $data_language_master[$j]['vGMapLangCode']; 
					
					$translateValue = $data_LanguageLabel[$i]['vTitle_'.$vCode];  
					
					$pos = strpos($translateValue, '?');
					
					if($pos >= 0 || $translateValue == '') {
					
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
						echo '<BR>'. $updateQuery = "UPDATE cancel_reason SET vTitle_".$vCode." ='".$translatedText."' WHERE iCancelReasonId ='".$iCancelReasonId."'";
						$db_cab = $obj->sql_query($updateQuery);
					}
				} 
				
			}
			
		}
	}
	
?>	