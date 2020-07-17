<?php 
include_once("common.php");
$iMakeId = isset($_REQUEST['model'])?$_REQUEST['model']:'';
$iModelId = isset($_REQUEST['iModelId'])?$_REQUEST['iModelId']:'';
$selected = "selected";
if($iMakeId != '')
{
	$sql = "select * from model where iMakeId = '".$iMakeId."' and eStatus='Active' ORDER BY vTitle ASC ";
	$db_model = $obj->MySQLSelect($sql);
	$cont = '';
	$cont .= '<option value="">'.$langage_lbl['LBL_CHOOSE_VEHICLE_MODEL'].'</option>';
    $cont .= '';
    for($i=0;$i<count($db_model);$i++){
		if($db_model[$i]['iModelId'] == $iModelId)

            $cont .= '<option value="'.$db_model[$i]['iModelId'].'"  '.$selected.'>'.$db_model[$i]['vTitle'].'</option>';
        else
			 $cont .= '<option value="'.$db_model[$i]['iModelId'].'">'.$db_model[$i]['vTitle'].'</option>';
    }
    echo $cont; exit;
}
?>
