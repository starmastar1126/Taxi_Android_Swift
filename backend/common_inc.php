<?php 
	$default_lang 	= $generalobj->get_default_lang();
	
	$def_lang_name = $generalobj->get_default_lang_name();
	// $APP_TYPE = "UberX";
	
	
	if(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] == ""){
		$sql="select eDirectionCode from language_master where vCode='$default_lang'";
		$lang = $obj->MySQLSelect($sql);
		$_SESSION['eDirectionCode'] = $lang[0]['eDirectionCode'];
	}
	
	function get_langcode($lang) {
		global $obj;
		$sql = "SELECT vLangCode FROM language_master WHERE vCode = '".$lang."'";
		$result = $obj->MySQLSelect($sql);
		// echo "<pre>";print_r($result);exit;
		return $result[0]['vLangCode'];
	} 
?>
