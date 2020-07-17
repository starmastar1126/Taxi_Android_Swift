<?php 
//echo '<prE>'; print_R($_REQUEST); echo '</pre>'; 
include_once('common.php');
$id 		= isset($_REQUEST['id'])?$_REQUEST['id']:'';
$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
$action 	= ($id != '')?'Edit':'Add';

$tbl_name 	= 'driver_vehicle';

//echo '<prE>'; print_R($_REQUEST); echo '</pre>'; 

// set all variables with either post (when submit) either blank (when insert)
$vMake = isset($_POST['vMake'])?$_POST['vMake']:'';
$iMakeId = isset($_POST['iMakeId'])?$_POST['iMakeId']:'';
$iModelId = isset($_POST['iModelId'])?$_POST['iModelId']:'';
$vYear = isset($_POST['vYear'])?$_POST['vYear']:'';
$vPlateNo = isset($_POST['vPlateNo'])?$_POST['vPlateNo']:'';

$eStatus_check = isset($_POST['eStatus'])?$_POST['eStatus']:'off';
$eStatus = ($eStatus_check == 'on')?'Active':'Inactive';

if(isset($_POST['submit'])) {
	echo "here";
	$q = "INSERT INTO ";
	$where = '';
	
	if($id != '' ){ 
		$q = "UPDATE ";
		$where = " WHERE `iMakeId` = '".$id."'";
	}
	
		
	echo $query = $q ." `".$tbl_name."` SET 	
		`iMakeId` = '".$iMakeId."',
		`iYear` ='".$vYear."',
		`vLicencePlate` = '".$vPlateNo."',
		`iModelId` = '".$iModelId."'"
		.$where;
	
	$obj->sql_query($query);
	$id = ($id != '')?$id:$obj->GetInsertId();
	
	header("Location:vehicle_add_form.php?id=".$id.'&success=1');

}

// for Edit
if($action == 'Edit') {
	$sql = "SELECT * FROM ".$tbl_name." WHERE iMakeId = '".$id."'";
	$db_data = $obj->MySQLSelect($sql);	
	
	$vLabel = $id;
	if(count($db_data) > 0) {
		foreach($db_data as $key => $value) {
			$vMake	 = $value['vMake'];
			$eStatus = $value['eStatus'];
		}
	}
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>"> <!--<![endif]-->

<!-- BEGIN HEAD-->
<head>
	<meta charset="UTF-8" />
    <title><?=$SITE_NAME?> | Make <?=$action;?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="keywords" />
	<meta content="" name="description" />
	<meta content="" name="author" />
    <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

    <?php  include_once('global_files.php');?>
	<!-- On OFF switch -->
	<link href="assets/css/jquery-ui.css" rel="stylesheet" />
	<link rel="stylesheet" href="assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />	
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 " >

    <!-- MAIN WRAPPER -->
    <div id="wrap">
		<?php  include_once('header.php'); ?>
		<?php  include_once('left_menu.php'); ?>       
        <!--PAGE CONTENT -->
         <div id="content">
            <div class="inner">
				<div class="row">
					<div class="col-lg-12">
						<h2><?=$action;?> Make</h2>
						<a href="make.php">
							<input type="button" value="Back to Listing" class="add-btn">
						</a>
					</div>
				</div>
				<hr />	
                <div class="body-div">
					<div class="form-group">
						<?php  if($success == 1) { ?>
						<div class="alert alert-success alert-dismissable">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							Record Updated successfully.
						</div><br/>
						<?php  } ?>
						<form method="post" action="">
							<input type="hidden" name="id" value="<?=$id;?>"/>
							<div class="row">
								<div class="col-lg-12">
									<label>Label</label>
								</div>
								<div class="col-lg-6">
									<select class="form-control" name="iMakeId" onchange="get_model(this.value);">
										<option>CHOOSE VEHICLE MAKE</option>
										<?php  for($j=0;$j<count($db_make);$j++){ ?>
										  <option value="<?=$db_make[$j]['iMakeId'];?>"><?=$db_make[$j]['vMake'];?></option>
										<?php  } ?>
									</select>
									<input type="text" class="form-control" name="vMake"  id="vMake" value="<?=$vMake;?>" placeholder="Make Label" required>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<label>Status</label>
								</div>
								<div class="col-lg-6">
									<div class="make-switch" data-on="success" data-off="warning">
										<input type="checkbox" name="eStatus" <?=($id != '' && $eStatus == 'Inactive')?'':'checked';?>/>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">							
									<input type="submit" class="save btn-info" name="submit" id="submit" value="<?=$action;?> Make">
								</div>
							</div>
						</form>
                	</div>
				</div>
            </div>
	   </div>
		<!--END PAGE CONTENT -->
    </div>
     <!--END MAIN WRAPPER -->
     

	<?php  include_once('footer.php');?>
	<script src="assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
</body>
	<!-- END BODY-->    
</html>