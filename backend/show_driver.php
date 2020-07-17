<?php  
	include_once('common.php');
	$id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
    $sql = "SELECT * FROM register_driver where iDriverId='$id'";
    $data_drv = $obj->MySQLSelect($sql);
	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
?>

<span><b>Email: </b><?= $generalobjAdmin->clearEmail($data_drv[0]['vEmail']);?></span>
<br>
<span><b>Phone Number: </b>(<?= $data_drv[0]['vCode'];?>) <?= $generalobjAdmin->clearPhone($data_drv[0]['vPhone']);?></span>

