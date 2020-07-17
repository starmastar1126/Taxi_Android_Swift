<?php 
include_once("../common.php");

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$iCompanyId = isset($_REQUEST['company'])?$_REQUEST['company']:'';
$iDriverId = isset($_REQUEST['iDriverId'])?$_REQUEST['iDriverId']:''; 
$selected = "selected"; 
if($iCompanyId != '')
{
	$sql = "select * from register_driver where iCompanyId = '".$iCompanyId."' and eStatus != 'Deleted' order by vName ASC";
	$db_model = $obj->MySQLSelect($sql);
	$cont = '';
	$cont .= '<select class="validate[required] form-control" id="iDriverId1" name="iDriverId">';
    $cont .= '<option value="">CHOOSE DRIVER </option>';
    for($i=0;$i<count($db_model);$i++){
		if($db_model[$i]['iDriverId'] == $iDriverId)
	
            $cont .= '<option value="'.$db_model[$i]['iDriverId'].'"  '.$selected.'>'.$generalobjAdmin->clearName($db_model[$i]['vName'].' '.$db_model[$i]['vLastName']).'</option>'; 
        else
			 $cont .= '<option value="'.$db_model[$i]['iDriverId'].'">'.$generalobjAdmin->clearName($db_model[$i]['vName'].' '.$db_model[$i]['vLastName']).'</option>'; 
    }
    $cont .= '</select>';
    
    echo $cont; exit;
}
?>