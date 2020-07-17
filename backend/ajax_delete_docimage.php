<?php 
include_once("common.php");
//echo "<pre>";print_r($_REQUEST);
$doc_type = isset($_REQUEST['doc_type'])?$_REQUEST['doc_type']:'';

if($doc_type == "common"){
	 $type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
	 $user_type = isset($_REQUEST['user_type'])?$_REQUEST['user_type']:'';
	 $id = isset($_REQUEST['id'])?$_REQUEST['id']:''; 
	 $path= isset($_REQUEST['doc_path'])?$_REQUEST['doc_path']:'';
	 $file_name= isset($_REQUEST['file_name'])?$_REQUEST['file_name']:'';
	 
	 if($user_type == 'company')
	 {
		 $table_name="company";
		 $field="iCompanyId";
	 }else
	 {
		 $table_name="register_driver";
		 $field="iDriverId";
	 }
			$file_del=$path.'/'.$id.'/'.$file_name;
			unlink($file_del);

			$sql="update ".$table_name." set ".$type." = '' where ".$field."='".$id."'";
			$drv_upd=$obj->MySQLSelect($sql);
		
		exit;
 }
 
 if($doc_type == "veh_doc"){
	 $type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
	 $user_type = isset($_SESSION['sess_user'])?$_REQUEST['sess_user']:'';
	 $veh_id = isset($_REQUEST['veh_id'])?$_REQUEST['veh_id']:''; 
	 //$path= $tconfig["tsite_upload_vehicle_doc"] . '/' .$veh_id. '/';
	 $file_name= isset($_REQUEST['img'])?$_REQUEST['img']:'';
	 
		$sql="update driver_vehicle set ".$type." = '' where iDriverVehicleId = '".$veh_id."'";
		$veh_doc_delete = $obj->MySQLSelect($sql);
		
		// $file_del=$path.$file_name;
		// unlink($file_del);
		
		exit;
	}
?>