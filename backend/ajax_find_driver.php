<?php 
include_once("common.php");
$iCompanyId = isset($_REQUEST['company'])?$_REQUEST['company']:'';
$iDriverId = isset($_REQUEST['iDriverId'])?$_REQUEST['iDriverId']:''; 
$selected = "selected"; 
if($iCompanyId != '')
{
	
	if($APP_TYPE != 'UberX'){
	$sql = "select * from register_driver where iCompanyId = '".$iCompanyId."' and eStatus != 'Deleted' order by vName ASC";
	
	
	}else{
	
	$sql = "select * from register_driver where iCompanyId = '".$iCompanyId."' and eStatus != 'Deleted' And iDriverVehicleId = '0' order by vName ASC"; 
	
	}
	//$sql = "select * from register_driver where iCompanyId = '".$iCompanyId."' and eStatus != 'Deleted'";
	$db_model = $obj->MySQLSelect($sql);
	$cont = '';
	$cont .= '<select class="validate[required] form-control" id="iDriverId1" name="iDriverId">';
    $cont .= '<option value="">CHOOSE DRIVER </option>';
    for($i=0;$i<count($db_model);$i++){
		if($db_model[$i]['iDriverId'] == $iDriverId)
	
            $cont .= '<option value="'.$db_model[$i]['iDriverId'].'"  '.$selected.'>'.$db_model[$i]['vName'].' '.$db_model[$i]['vLastName'].'</option>'; 
        else
			 $cont .= '<option value="'.$db_model[$i]['iDriverId'].'">'.$db_model[$i]['vName'].' '.$db_model[$i]['vLastName'].'</option>'; 
    }
    $cont .= '</select>';
    
    echo $cont; exit;
}
?>