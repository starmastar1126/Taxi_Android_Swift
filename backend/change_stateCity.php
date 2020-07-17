<?php 
include_once('common.php');

$countryId = isset($_REQUEST['countryId']) ? $_REQUEST['countryId'] : '';
$stateId = isset($_REQUEST['stateId']) ? $_REQUEST['stateId'] : '';
$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : '';
$fromMod = isset($_REQUEST['fromMod']) ? $_REQUEST['fromMod'] : '';

if(isset($_REQUEST['countryId'])) {
	
	if($fromMod == ""){
		$cons = "<option value='-1'>All</option>";
		$where = " AND iCountryId = '".$countryId."'";
	}else {
		
		$sql = "select iCountryId from country where 1=1 and eStatus = 'Active' AND vCountryCode='".$countryId."'";
		$db_cntr = $obj->MySQLSelect($sql);
		
		$cons = "<option value=''>Select</option>";
		$where = " AND iCountryId = '".$db_cntr[0]['iCountryId']."'";
	}
	if($countryId != ""){
		$sql = "select iStateId, vState from state where 1=1 and eStatus = 'Active' $where ORDER BY vState ASC ";
		$db_states = $obj->MySQLSelect($sql);
		
		foreach($db_states as $dbs) {
			$cons .= "<option value='".$dbs['iStateId']."'";
			if($dbs['iStateId'] == $selected){
				$cons .= " selected";
			}
			$cons .= ">".$dbs['vState']."</option>";
		}
	}
	echo $cons; exit;
}

if(isset($_REQUEST['stateId'])) {
	if($fromMod == "")
		$cons = "<option value='-1'>All</option>";
	else 
		$cons = "<option value=''>Select</option>";
	if($stateId != ""){
		$sql = "select iCityId, vcity from city where iStateId = '".$stateId."' and eStatus = 'Active' ORDER BY vcity ASC ";
		$db_states = $obj->MySQLSelect($sql);
		
		foreach($db_states as $dbs) {
			$cons .= "<option value='".$dbs['iCityId']."'";
			if($dbs['iCityId'] == $selected){
				$cons .= " selected";
			}
			$cons .= ">".$dbs['vcity']."</option>";
		}
	}
	echo $cons; exit;
}
?>