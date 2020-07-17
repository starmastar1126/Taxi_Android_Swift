<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$companyId = isset($_REQUEST['id']) ? $_REQUEST['id'] : ''; 
$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';  
$sql = "SELECT vName,vLastName,iDriverId FROM register_driver where iCompanyId='".$companyId."'";  
$db_driver = $obj->MySQLSelect($sql);

if (count($db_driver) > 0) {
	 echo "<option value=''>Search By Driver</option>";
				for($i=0;$i<count($db_driver);$i++){
					$selected='';					
					if($db_driver[$i]['iDriverId'] == $iDriverId){
						$selected = "selected=selected";						
						
					}
					echo "<option value=".$db_driver[$i]['iDriverId']." ".$selected.">".$generalobjAdmin->clearName($db_driver[$i]['vName']." ".$db_driver[$i]['vLastName'])."</option>";			
					
				}
				 exit;
	
	
}
?>