<?php 
	include_once('common.php');
	$generalobj->check_member_login();
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
	$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
	$var_msg = isset($_REQUEST["var_msg"]) ? $_REQUEST["var_msg"] : '';
	$iCompanyId = $_SESSION['sess_iUserId'];
	
	$sql = "select * from country";
	$db_country = $obj->MySQLSelect($sql);
	
	$sql = "select * from language_master where eStatus = 'Active'";
	$db_lang = $obj->MySQLSelect($sql);
	
	$script = 'Driver';
	if ($action == 'delete') {
		 if(SITE_TYPE != 'Demo')
		 {
			$query = "UPDATE register_driver SET eStatus = 'Deleted' WHERE iDriverId = '" . $hdn_del_id . "'";
			$obj->sql_query($query);
		$var_msg = $langage_lbl['LBL_COMPNAY_FRONT_DELETE_TEXT'];
		header("Location:driver.php?success=1&var_msg=". $var_msg);
			exit();
	} else {
			header("Location:driver.php?success=2");
			exit();
		 }
	}
	
	$vName = isset($_POST['vName']) ? $_POST['vName'] : '';
	$vLname = isset($_POST['vLname']) ? $_POST['vLname'] : '';
	$vEmail = isset($_POST['vEmail']) ? $_POST['vEmail'] : '';
	$vPassword = isset($_POST['vPassword']) ? $_POST['vPassword'] : '';
	$vPhone = isset($_POST['vPhone']) ? $_POST['vPhone'] : '';
	$vCode = isset($_POST['vCode']) ? $_POST['vCode'] : '';
	$vCountry = isset($_POST['vCountry']) ? $_POST['vCountry'] : '';
	$vLang = isset($_POST['vLang']) ? $_POST['vLang'] : '';
	$vPass = $generalobj->encrypt($vPassword);
	$eStatus = isset($_POST['eStatus']) ? $_POST['eStatus'] : '';
	$tbl_name = "register_driver";
	
	if (isset($_POST['submit'])) {
		
		$q = "INSERT INTO ";
		$where = '';
		
		if ($action == 'Edit') {
			$eStatus = ", eStatus = 'Inactive' ";
			} else {
			$eStatus = '';
		}
		
		if ($id != '') {
			$q = "UPDATE ";
			$where = " WHERE `iDriverId` = '" . $id . "'";
		}
		
		
		$query = $q . " `" . $tbl_name . "` SET
        `vName` = '" . $vName . "',
        `vLastName` = '" . $vLname . "',
        `vCountry` = '" . $vCountry . "',
        `vCode` = '" . $vCode . "',
        `vEmail` = '" . $vEmail . "',
        `vLoginId` = '" . $vEmail . "',
        `vPassword` = '" . $vPass . "',
        `vPhone` = '" . $vPhone . "',
        `vLang` = '" . $vLang . "',
        `eStatus` = '" . $eStatus . "',
        `iCompanyId` = '" . $iCompanyId . "'" . $where;
		
		$obj->sql_query($query);
		$id = ($id != '') ? $id : $obj->GetInsertId();
	if(SITE_TYPE != 'Demo'){
  			if ($action == 'Edit') {
			$var_msg = $langage_lbl['LBL_COMPNAY_FRONT_UPDATE_DRIVER_TEXT'];
			header("Location:driver.php?id=" . $id . "&success=1&var_msg=". $var_msg);
		      exit;
	      	}else{
      		$var_msg = $langage_lbl['LBL_COMPNAY_FRONT_ADD_DRIVER_TEXT'];
			header("Location:driver.php?id=" . $id . "&success=1&var_msg=". $var_msg);
		      exit;
		    }
	} else {
			 header("Location:driver.php?success=2");
			 exit;
		 }

	}
	
	$dri_ssql = "";
	if (SITE_TYPE == 'Demo') {
		$dri_ssql = " And tRegistrationDate > '" . WEEK_DATE . "'";
	}
	
	if ($action == 'view') {
		$sql = "SELECT * FROM register_driver where iCompanyId = '" . $iCompanyId . "' and eStatus != 'Deleted' $dri_ssql order by tRegistrationDate DESC";
		$data_drv = $obj->MySQLSelect($sql);

		$sql1 = "SELECT doc_masterid as total FROM `document_master` WHERE `doc_usertype` ='driver' AND status = 'Active'";
		$doc_count_query = $obj->MySQLSelect($sql1);
		$doc_count = count($doc_count_query);
	}
	if ($action == 'edit') {
		// echo "<script>document.getElementById('cancel-add-form').style.display='';document.getElementById('show-add-form').style.display='none';document.getElementById('add-hide-div').style.display='none';</script>";
	}
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_VEHICLE_DRIVER_TXT_ADMIN']; ?></title>
		<!-- Default Top Script and css -->
		<?php  include_once("top/top_script.php");?>
		
	</head>
	<body>
		<!-- home page -->
		<div id="main-uber-page">
			<!-- Left Menu -->
			<?php  include_once("top/left_menu.php");?>
			<!-- End: Left Menu-->
			<!-- Top Menu -->
			<?php  include_once("top/header_topbar.php");?>
			<!-- End: Top Menu-->
			<!-- Driver page-->
			<div class="page-contant">
				<div class="page-contant-inner">
					<h2 class="header-page-d1 trip-detail driver-detail1"><?=$langage_lbl['LBL_DRIVER_COMPANY_TXT']; ?><a href="javascript:void(0);" onClick="add_driver_form();"><?=$langage_lbl['LBL_ADD_DRIVER_COMPANY_TXT']; ?></a></h2>
					<!-- driver list page -->
					<div class="trips-page trips-page1">
						<?php  if ($_REQUEST['success']==1) {?>
							<div class="alert alert-success alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button> 
								<?= $var_msg ?>
							</div>
							<?php }else if($_REQUEST['success']==2){ ?>
							<div class="alert alert-danger alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								<?= $langage_lbl['LBL_EDIT_DELETE_RECORD']; ?>
							</div>
							<?php  
							} else if(isset($_REQUEST['success']) && $_REQUEST['success']==0){?>
							<div class="alert alert-danger alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button> 
								<?= $var_msg ?>
							</div>
							<?php  }
						?>
						<div class="trips-table trips-table-driver trips-table-driver-res"> 
							<div class="trips-table-inner">
								<div class="driver-trip-table">
									<table width="100%" border="0" cellpadding="0" cellspacing="0" id="dataTables-example">
										<thead>
											<tr>
												<?php  if($APP_TYPE != "UberX"){?>
												<th width="25%"><?=$langage_lbl['LBL_USER_NAME_HEADER_SLIDE_TXT']; ?></th>
												<?php  } ?>
												<th width="20%"><?=$langage_lbl['LBL_DRIVER_EMAIL_LBL_TXT']; ?></th>
												<!--<th>Service Location</th>-->
												<th width="10%"><?=$langage_lbl['LBL_MOBILE_NUMBER_HEADER_TXT']; ?></th>
												<th width="15%" style="width: 67px;">
													<?php  if($APP_TYPE != "UberX"){
														echo $langage_lbl['LBL_SHORT_LANG_TXT']; 
													}else{
														echo $langage_lbl['WASHING_SERVICE_TYPES_TXT'];
													} ?>
												</th>
												<?php  if($APP_TYPE == "UberX") { ?>
												<th>
													<?php  echo $langage_lbl['LBL_AVAILABILITY'];?>
												</th>
												<?php  } ?>
												<?php  if($doc_count != 0 ) { ?>
												<th width="14%"><?php  echo $langage_lbl['LBL_EDIT_DOCUMENTS_TXT'];?></th>
												<?php  } ?>
												<th width="8%"><?=$langage_lbl['LBL_DRIVER_EDIT']; ?></th>
												<th width="8%"><?=$langage_lbl['LBL_DRIVER_DELETE']; ?></th>
											</tr>
										</thead>
										<tbody>
											<?php  for ($i = 0; $i < count($data_drv); $i++) { ?>
												<tr class="gradeA">
													<?php  if($APP_TYPE != "UberX"){?>
													<td><?= $generalobj->clearName($data_drv[$i]['vName'] . ' ' . $data_drv[$i]['vLastName']); ?></td>
													<?php  } ?>
													<td><?= $generalobj->clearEmail($data_drv[$i]['vEmail']); ?></td>
													<!--<td class="center"><?= $data_drv[$i]['vServiceLoc']; ?></td>-->
													<td><?= $generalobj->clearMobile($data_drv[$i]['vPhone']); ?></td>
													<td>
														<?php 
															if($APP_TYPE != "UberX"){
																echo $data_drv[$i]['vLang']; 
															}else{
																?><a href="add_services.php?iDriverId=<?= $data_drv[$i]['iDriverId']; ?>">
																	<button class="btn btn-success">
																		<i class="icon-pencil icon-white"></i>
																		<?=$langage_lbl['WASHING_SERVICE_TYPES_TXT'];?>
																	</button>
																</a><?php 
															}
														 ?>
													</td>
													<?php  if($APP_TYPE == "UberX") { ?>
													<td>
														<a href="add_availability.php?iDriverId=<?= $data_drv[$i]['iDriverId']; ?>">
															<button class="btn btn-success">
																<i class="icon-pencil icon-white"></i>
																<?= $langage_lbl['LBL_AVAILABILITY'];?>
															</button>
														</a>
													</td>
													<?php  } ?>
													<?php  if($doc_count != 0 ) { ?>
													<td align="center" >
														<a href="driver_document_action.php?id=<?= $data_drv[$i]['iDriverId']; ?>&action=edit">
															<button class="btn btn-primary">
																<i class="icon-pencil icon-white"></i> <?=$langage_lbl['LBL_EDIT_DOCUMENTS_TXT']; ?>
															</button>
														</a>
													</td>
													<?php  } ?>
													<td align="center" >
														<a href="driver_action.php?id=<?= $data_drv[$i]['iDriverId']; ?>&action=edit">
															<button class="btn btn-primary">
																<i class="icon-pencil icon-white"></i> <?=$langage_lbl['LBL_DRIVER_EDIT']; ?>
															</button>
														</a>
													</td>
													<td align="center" >
														<form name="delete_form_<?= $data_drv[$i]['iDriverId']; ?>" id="delete_form_<?= $data_drv[$i]['iDriverId']; ?>" method="post" action="" class="margin0">
															<input type="hidden" name="hdn_del_id" id="hdn_del_id" value="<?= $data_drv[$i]['iDriverId']; ?>">
															<input type="hidden" name="action" id="action" value="delete">
															<button type="button" class="btn btn-danger" onClick="confirm_delete('<?= $data_drv[$i]['iDriverId']; ?>');">
																<i class="icon-remove icon-white"></i> <?=$langage_lbl['LBL_DRIVER_DELETE']; ?>
															</button>
														</form>
													</td>
												</tr>
											<?php  } ?>
										</tbody>
									</table>
								</div>  </div>
						</div>
						<!-- -->
						<?php  //if(SITE_TYPE=="Demo"){?>
							<!--<div class="record-feature"> <span><strong>“Edit / Delete Record Feature”</strong> has been disabled on the Demo Admin Version you are viewing now.
							This feature will be enabled in the main product we will provide you.</span> </div>
						<?php  //}?> -->
						<!-- -->
					</div>
					<!-- -->
					<div style="clear:both;"></div>
				</div>
			</div>
			<!-- footer part -->
			<?php  include_once('footer/footer_home.php');?>
			<!-- footer part end -->
            <!-- End:contact page-->
            <div style="clear:both;"></div>
		</div>
		<!-- home page end-->
		<!-- Footer Script -->
		<?php  include_once('top/footer_script.php');?>
		<script src="assets/js/jquery-ui.min.js"></script>
		<script src="assets/plugins/dataTables/jquery.dataTables.js"></script>
		<script type="text/javascript">
			$(document).ready(function () {
				$('#dataTables-example').dataTable({
					"aaSorting": []
				});
			});
			function confirm_delete(id)
			{
				bootbox.confirm('<?=$langage_lbl['LBL_DELETE_DRIVER_CONFIRM_MSG']; ?>', function(result) {
					if(result){
						document.getElementById('delete_form_'+id).submit();
					}
				});
			}
			function changeCode(id)
			{
				var request = $.ajax({
					type: "POST",
					url: 'change_code.php',
					data: 'id=' + id,
					success: function (data)
					{
						document.getElementById("code").value = data;
						//window.location = 'profile.php';
					}
				});
			}
			
			function add_driver_form(){
				window.location.href = "driver_action.php";
			}
		</script>
		
		<script type="text/javascript">
			$(document).ready(function(){
				$("[name='dataTables-example_length']").each(function(){
					$(this).wrap("<em class='select-wrapper'></em>");
					$(this).after("<em class='holder'></em>");
				});
				$("[name='dataTables-example_length']").change(function(){
					var selectedOption = $(this).find(":selected").text();
					$(this).next(".holder").text(selectedOption);
				}).trigger('change');
			})
		</script>
		<!-- End: Footer Script -->
	</body>
</html>
