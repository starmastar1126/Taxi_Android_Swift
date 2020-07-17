<?php 
include_once('common.php');

$hdn_del_id = isset($_POST['hdn_del_id'])?$_POST['hdn_del_id']:'';
$iMakeId 	= isset($_GET['iMakeId'])?$_GET['iMakeId']:'';
$status 	= isset($_GET['status'])?$_GET['status']:'';
$tbl_name 	= 'make';

if($hdn_del_id != ''){
	$query = "DELETE FROM `".$tbl_name."` WHERE iMakeId = '".$hdn_del_id."'";
	$obj->sql_query($query);
}
if($iMakeId != '' && $status != ''){
	$query = "UPDATE `".$tbl_name."` SET eStatus = '".$status."' WHERE iMakeId = '".$iMakeId."'";
	$obj->sql_query($query);
}

$sql = "SELECT * FROM ".$tbl_name." ORDER BY iMakeId DESC";
$db_data = $obj->MySQLSelect($sql);
//echo '<pre>'; print_R($db_data); echo '</pre>';
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>"> <!--<![endif]-->

<!-- BEGIN HEAD-->
<head>
	<meta charset="UTF-8" />
    <title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_MAKE_TXT']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="keywords" />
	<meta content="" name="description" />
	<meta content="" name="author" />	
    <?php  include_once('global_files.php');?>
	
    <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
	<script type="text/javascript">
		function confirm_delete()
		{
			var confirm_ans = confirm("Are You sure You want to Delete Make?");
			return confirm_ans;
			//document.getElementById(id).submit();
		}
	</script>
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
						<h2><?=$langage_lbl['LBL_MAKE_TXT']; ?></h2>
						<a href="make_action.php">
							<input type="button" value="Add Make" class="add-btn">
						</a>
					</div>
				</div>
				<hr />
                <div class="body-div">
					<div class="table-list">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										<?=$langage_lbl['LBL_MAKE_TXT']; ?>
									</div>
									<div class="panel-body">
										<div class="table-responsive">
											<table class="table table-striped table-bordered table-hover" id="dataTables-example">
												<thead>
													<tr>
														<th><?=$langage_lbl['LBL_MAKE_TXT']; ?></th>
														<th><?=$langage_lbl['LBL_Status']; ?></th>
														<th><?=$langage_lbl['LBL_EDIT']; ?></th>
														<th><?=$langage_lbl['LBL_DELETE']; ?></th>
													</tr>
												</thead>
												<tbody>
													<?php  
													$count_all = count($db_data);
													if($count_all > 0) {
														for($i=0;$i<$count_all;$i++) {
															$id = $db_data[$i]['iMakeId'];
															$vMake = $db_data[$i]['vMake']; 
															$eStatus = $db_data[$i]['eStatus'];
															$checked = ($eStatus=="Active")?'checked':'';
															?>
															<tr class="gradeA">
																<td width="40%"><?=$vMake;?></td>
																<td width="40%">
																	<a href="make.php?iMakeId=<?=$id;?>&status=<?=($eStatus=="Active")?'Inactive':'Active'?>">
																		<!-- <button class="btn <?=($eStatus=="Active")?'btn-success':'btn-danger'?>"> -->
																		<button class="btn">
																			<i class="<?=($eStatus=="Active")?'icon-eye-open':'icon-eye-close'?>"></i> <?=$eStatus;?>
																		</button>
																	</a>
																</td>
																<td class="center">
																	<a href="make_action.php?id=<?=$id;?>">
																		<button class="btn btn-primary">
																			<i class="icon-pencil icon-white"></i> <?=$langage_lbl['LBL_EDIT']; ?>
																		</button>
																	</a>
																</td>
																<td class="center">
																	<!-- <a href="languages.php?id=<?=$id;?>&action=delete"><i class="icon-trash"></i> Delete</a>-->
																	<form name="delete_form" id="delete_form" method="post" action="" onsubmit="return confirm_delete()" class="margin0">
																		<input type="hidden" name="hdn_del_id" id="hdn_del_id" value="<?=$id;?>">
																		<button class="btn btn-danger">
																			<i class="icon-remove icon-white"></i> <?=$langage_lbl['LBL_DELETE']; ?>
																		</button>
																	</form>
																</td>
															</tr>
														<?php  } 
													} else { ?>
														<tr class="gradeA">
															<td colspan="4"><?=$langage_lbl['LBL_NO_RECORDS_FOUND1']; ?>.</td>
														</tr>
													<?php  } ?>
												</tbody>
											</table>
										</div>
									   
									</div>
								</div>
							</div> <!--TABLE-END-->
						</div>
					</div> 
				</div>
			</div>
        </div>
       <!--END PAGE CONTENT -->
    </div>
    <!--END MAIN WRAPPER -->

	<?php  include_once('footer.php');?>
	
    <script src="assets/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="assets/plugins/dataTables/dataTables.bootstrap.js"></script>
    <script>
         $(document).ready(function () {
             $('#dataTables-example').dataTable();
         });
    </script>
</body>
	<!-- END BODY-->    
</html>
