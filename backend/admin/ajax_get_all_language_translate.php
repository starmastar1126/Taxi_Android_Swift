<?php 
include_once("../common.php");

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

  $englishText = isset($_POST['englishText'])?$_POST['englishText']:'';
  $englishText="Home";
	//echo '<prE>'; echo $englishText; echo '</pre>';

	
	
	// fetch all lang from language_master table
	$sql = "SELECT vCode,vLangCode FROM `language_master` where vCode!='".$default_lang."' ORDER BY `iDispOrder`";
	$db_master = $obj->MySQLSelect($sql);
	$count_all = count($db_master);
  

  $sql = "SELECT vLangCode FROM language_master where eStatus='Active' AND eDefault = 'Yes'";
  $data = $obj->MySQLSelect($sql);
  $vGMapLangCode = isset($data[0]["vLangCode"]) ? $data[0]["vLangCode"] : 'en';

  /*echo "<pre>";
  print_r($db_master);*/
  
  if($count_all > 0) {
	   for($i=0;$i<$count_all;$i++) {
          
            $vCode = $db_master[$i]['vCode'];
      
            $vGmapCode = $db_master[$i]['vLangCode'];
            //$def_lang = strtolower($default_lang);
            
            $vValue = 'vValue_'.$vCode;
            
            // $url = 'http://api.mymemory.translated.net/get?q='.urlencode($englishText).'&de=harshilmehta1982@gmail.com&langpair=en|'.$vGmapCode;
            $url = 'http://api.mymemory.translated.net/get?q='.urlencode($englishText).'&de=harshilmehta1982@gmail.com&langpair='.$vGMapLangCode.'|'.$vGmapCode;

            $result = file_get_contents($url);
  
            $finalResult = json_decode($result);
            
            $getText = $finalResult->responseData;
            $responseStatus=$finalResult->responseStatus;
          	if($responseStatus != "200"){
          		$translatedText=$englishLabelValue;
          	}else{
            $translatedText=$getText->translatedText;
            }
            
            $data['result'][] = array(
                                  $vValue => $translatedText 
                                );
            
            
     }
  }
  // echo "<pre>";print_r($data['result']);
  $output = array();
  foreach($data['result'] as $Result){
    /*$output[key($Result)] = current($Result);*/
    if(current($Result) != "") { 
      $output[key($Result)] = current($Result);
    } else {
      $output[key($Result)] = $englishText;
    }
  }
  
  /*echo "<pre>";
  print_r($data['results']);
  die*/
  
  echo json_encode($output);
  exit;
  

?>