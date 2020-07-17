<?php 
include_once("common.php");

$from = isset($_REQUEST['from'])?$_REQUEST['from']:'';
if($from == 'other'){
	$tbl_name = 'language_label_other';
}else {
	$tbl_name = 'language_label';
}
$sql = "SELECT * FROM `language_master` ORDER BY `iDispOrder`";
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);

//echo "<pre>"; print_r($_REQUEST); die;

$id 		= isset($_REQUEST['id'])?$_REQUEST['id']:'';
$pageid 	= isset($_REQUEST['lp_id'])?$_REQUEST['lp_id']:0;
$lp_name 	= isset($_REQUEST['lp_name'])?$_REQUEST['lp_name']:'';

if($count_all > 0) {
	for($i=0;$i<$count_all;$i++) {
		$vValue = 'vValue_'.$db_master[$i]['vCode'];
		$$vValue  = isset($_POST[$vValue])?$_POST[$vValue]:'';
	}
}

if($count_all > 0) {
	for($i=0;$i<$count_all;$i++) {

		$q = "INSERT INTO ";
		$where = '';

		if($id != '' ){
			$q = "UPDATE ";
			$sql = "SELECT vLabel FROM ".$tbl_name." WHERE LanguageLabelId = '".$id."'";
			$db_data = $obj->MySQLSelect($sql);	    
			$sql = "SELECT * FROM ".$tbl_name." WHERE vLabel = '".$db_data[0]['vLabel']."'";
			$db_data = $obj->MySQLSelect($sql);		
			$vLabel = $db_data[0]['vLabel'];
			$where = " WHERE `vLabel` = '".$vLabel."' AND vCode = '".$db_master[$i]['vCode']."'";
		}

		$vValue = 'vValue_'.$db_master[$i]['vCode'];

		 $query = $q ." `".$tbl_name."` SET
		`lPage_id` = '".$pageid."',
		`vLabel` = '".$vLabel."',
		`vCode` = '".$db_master[$i]['vCode']."',
		`vValue` = '".$$vValue."'"
		.$where;
		$obj->sql_query($query);
	}
}

?>