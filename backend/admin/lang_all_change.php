<?php 
include_once('../common.php');

$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$platform = isset($_REQUEST['platform']) ? $_REQUEST['platform'] : '';

if($platform == 'web'){
	if($type == 'enable'){
		$_SESSION['sess_editingToken'] = "nt_".time();
		$sql = "UPDATE configurations SET vValue = '".$_SESSION['sess_editingToken']."' WHERE vName='EASY_EDITING_TOKEN'";
		$obj->MySQLSelect($sql);
		
	}else {
		unset($_SESSION['sess_editingToken']);
		$sql = "UPDATE configurations SET vValue = '' WHERE vName='EASY_EDITING_TOKEN'";
		$obj->MySQLSelect($sql);
	}
}
header('location:lang_all.php');
?>