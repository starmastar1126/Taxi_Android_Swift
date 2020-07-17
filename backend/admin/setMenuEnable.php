<?php 
include_once('../common.php');
$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : '';

if($data != "" ){
	$where = " vName = 'SET_MENU_ENABLE'";
	$Update_Session['vValue'] = $data;
	$Update_Session_id = $obj->MySQLQueryPerform("configurations", $Update_Session, 'update', $where);
}
?>