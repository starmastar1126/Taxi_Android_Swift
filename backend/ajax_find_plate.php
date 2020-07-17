<?php  
	include_once("common.php");
	$plate=isset($_REQUEST['plate']) ? $_REQUEST['plate'] : '';
	$id=isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
	//print_r($_REQUEST);exit;
	if($plate != ""){
		$ssql1="";
		if($id!="")
		{
			$ssql1=" and iDriverVehicleId != '$id'";
		}
		$sql="select * from driver_vehicle where vLicencePlate='$plate' and eStatus!='Deleted'".$ssql1;
		$db_veh_det= $obj->MySQLSelect($sql);
		
		if(count($db_veh_det)>0)
		{	
			echo $langage_lbl['LBL_LICENCE_PLATE_EXIST'];
		}else {
			echo "yes";
		}	
	}
?>