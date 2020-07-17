<?php 
	include_once('../common.php');
	//echo "<pre>";print_r($_REQUEST);exit;
	if (!isset($generalobjAdmin)) {
		require_once(TPATH_CLASS . "class.general_admin.php");
		
		$generalobjAdmin = new General_admin();
	}
	
	//$actionType = $_REQUEST['type'];
	$actionType = "";
	$generalobjAdmin->check_member_login();
	
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
	$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
	$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
	$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
	$res_id = isset($_REQUEST['res_id']) ? $_REQUEST['res_id'] : '';
	$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
	$ksuccess=isset($_REQUEST['ksuccess']) ? $_REQUEST['ksuccess'] : 0;
	$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : '';
	$script = 'Driver';
	
	$sql = "select * from country";
	$db_country = $obj->MySQLSelect($sql);
	
	$sql = "select * from company WHERE eStatus != 'Deleted'";
	$db_company = $obj->MySQLSelect($sql);
	
	$sql = "select * from language_master where eStatus = 'Active'";
	$db_lang = $obj->MySQLSelect($sql);
	
	if ($iDriverId != '' && $status != '') {
		
		$sql="SELECT register_driver.iDriverId from register_driver
		LEFT JOIN company on register_driver.iCompanyId=company.iCompanyId
		LEFT JOIN driver_vehicle on driver_vehicle.iDriverId=register_driver.iDriverId
		WHERE company.eStatus='Active' AND driver_vehicle.eStatus='Active' AND register_driver.iDriverId='".$iDriverId."'".$ssl;
		$Data=$obj->MySQLSelect($sql);
		//echo "<pre>";print_r($Data);exit;
		if($status == 'active') {			
			$query = "UPDATE register_driver SET eStatus = 'inactive' WHERE iDriverId = '" . $iDriverId . "'";
			$obj->sql_query($query);
			
			$sql="SELECT * FROM register_driver WHERE iDriverId = '" . $iDriverId . "'";
			$db_status = $obj->MySQLSelect($sql);
			$maildata['EMAIL'] =$db_status[0]['vEmail'];
			$maildata['NAME'] = $db_status[0]['vName'].' '.$db_status[0]['vLastName'];
			//$maildata['LAST_NAME'] = $db_status[0]['vName'];
			$maildata['DETAIL']="Your Account is ".ucfirst($db_status[0]['eStatus']).".";
			$generalobj->send_email_user("ACCOUNT_STATUS",$maildata);
			
			$msg=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Inactive Successfully';
			header("Location:driver.php?success=1&msg=".$msg);exit;
			
		} else if(SITE_TYPE !='Demo' && count($Data)>0)
		{
			$query = "UPDATE register_driver SET eStatus = 'active' WHERE iDriverId = '" . $iDriverId . "'";
			$obj->sql_query($query);
	
			$sql="SELECT * FROM register_driver WHERE iDriverId = '" . $iDriverId . "'";
			$db_status = $obj->MySQLSelect($sql);
			$maildata['EMAIL'] =$db_status[0]['vEmail'];
			$maildata['NAME'] = $db_status[0]['vName'];
			//$maildata['LAST_NAME'] = $db_status[0]['vName'];
			$maildata['DETAIL']="Your Account is ".ucfirst($db_status[0]['eStatus']).".<p>You can now login and take rides with passengers.</p>";
			$generalobj->send_email_user("ACCOUNT_STATUS",$maildata);
			
			$msg=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Active Successfully';
			header("Location:driver.php?type=approve&success=1&msg=".$msg);exit;
		}
		else {
			$msg=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Have No Any Active Company Or Vehicle';
			header("Location:driver.php?success=2&msg=".$msg."&type=".$actionType);exit;
		}
	}
	
	
	if ($action == 'delete' && $hdn_del_id != '') {
		$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';
		//$query    = "DELETE FROM `" . $tbl_name . "` WHERE iDriverId = '" . $id . "'";
		if(SITE_TYPE !='Demo'){
			$query = "UPDATE register_driver SET eStatus = 'Deleted' WHERE iDriverId = '" . $hdn_del_id . "'";
			$obj->sql_query($query);
			$action = "view";
			$success = "1";
			$msg=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' Deleted Successfully.';
		}
		else{
			header("Location:driver.php?success=2&type=".$actionType);exit;
		}
		//header("Location:driver.php?success=1");
	}
	if ($action == 'reset' && $res_id != '') {
		
		if(SITE_TYPE !='Demo'){
		
			$query = "UPDATE register_driver SET iTripId='0',vTripStatus='NONE' WHERE iDriverId = '" . $res_id . "'";
			$obj->sql_query($query);
			$action = "view";
			$success = "1";
			$msg="Driver Status Reseted Successfully.";
			
		}
		else{
			header("Location:driver.php?success=2&type=".$actionType);exit;
		}
		//header("Location:driver.php?success=1");
	}
	
	$vName = isset($_POST['vName']) ? $_POST['vName'] : '';
	$vLname = isset($_POST['vLname']) ? $_POST['vLname'] : '';
	$vEmail = isset($_POST['vEmail']) ? $_POST['vEmail'] : '';
	$vPassword = isset($_POST['vPassword']) ? $_POST['vPassword'] : '';
	$vPhone = isset($_POST['vPhone']) ? $_POST['vPhone'] : '';
	$vCode = isset($_POST['vCode']) ? $_POST['vCode'] : '';
	$vCountry = isset($_POST['vCountry']) ? $_POST['vCountry'] : '';
	$iCompanyId = isset($_POST['iCompanyId']) ? $_POST['iCompanyId'] : '1';
	$vLang = isset($_POST['vLang']) ? $_POST['vLang'] : '';
	$vPass = $generalobj->encrypt_bycrypt($vPassword);
	$eStatus = isset($_POST['eStatus']) ? $_POST['eStatus'] : '';
	$iCompanyid = isset($_REQUEST['iCompanyid']) ? $_REQUEST['iCompanyid'] : '';
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
		if($action=="Add")
		{
			$ksuccess="1";

		}
        else if ($action=="delete") 
        {
			$ksuccess="3";
		}
		else
		{
			$ksuccess="2";
		}
		header("Location:driver.php?id=" . $id . '&success=1&success='.$ksuccess."&type=".$actionType);
	}
	$cmp_ssql = "";
	if(SITE_TYPE =='Demo'){
		$cmp_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
	}
	$ssqlcmp ='';
	if($iCompanyid !=''){
		$ssqlcmp =" AND rd.iCompanyId ='$iCompanyid'";
		
	}
	if ($action == 'view') {
		//$ssl = " AND rd.eStatus = 'inactive'";
		$title = "Pending ";
		if($actionType != "" && $actionType == "approve") {
			$title = "Approved ";
			$ssl = " AND rd.eStatus = 'active'";
		}
		//$sql = "SELECT rd.*, c.vName companyFirstName, c.vLastName companyLastName FROM register_driver rd, company c WHERE rd.iCompanyId = c.iCompanyId AND rd.eStatus != 'Deleted' AND c.eStatus != 'Deleted'";
		$sql = "SELECT rd.*, c.vCompany companyFirstName, c.vLastName companyLastName FROM register_driver rd LEFT JOIN company c ON rd.iCompanyId = c.iCompanyId and c.eStatus != 'Deleted' WHERE 1=1 ".$ssl.$cmp_ssql.$ssqlcmp;
		$data_drv = $obj->MySQLSelect($sql);
		
	}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--><html lang="en"> <!--<![endif]-->
	<!-- BEGIN HEAD-->
<head>
<meta charset="UTF-8" />
<title>Admin | <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> </title>
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
		
		<?php  include_once('global_files.php');?>
		<script>
			$(document).ready(function () {
				$("#show-add-form").click(function () {
					$("#show-add-form").hide(1000);
					$("#add-hide-div").show(1000);
					$("#cancel-add-form").show(1000);
				});
				
			});
		</script>
		<script>
			$(document).ready(function () {
				$("#cancel-add-form").click(function () {
					$("#cancel-add-form").hide(1000);
					$("#show-add-form").show(1000);
					$("#add-hide-div").hide(1000);
				});
				
			});
			
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
					<div id="add-hide-show-div">
						<div class="row">
							<div class="col-lg-12">
								<h2><?php  echo $langage_lbl_admin['LBL_DRIVERS_TXT_ADMIN'];?> </h2>
								<!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
								<a class="add-btn" href="driver_action.php" style="text-align: center;">ADD A <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></a>
								<input type="button" id="cancel-add-form" value="CANCEL" class="cancel-btn">
							</div>
						</div>
						<hr />
					</div>
					<?php  if($success == 1) { ?>
						<div class="alert alert-success alert-dismissable">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
							
							<?php  if($ksuccess == "1")
								{?>
								<?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> Inserted Successfully.
								<?php  }
								else if ($ksuccess=="2")
								{?>
								<?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> Updated Successfully.
								<?php  }
								else if($ksuccess=="3") 
								{?>
								<?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> Deleted Successfully.
							<?php  } ?>
							<?php echo $msg;?>
							
						</div><br/>
						<?php  }elseif ($success == 2 & $msg == '') { ?>
						<div class="alert alert-danger alert-dismissable">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
							"Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
						</div><br/>
						<?php  } elseif ($success == 2 & $msg != '') { ?>
						<div class="alert alert-danger alert-dismissable">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
							<?php echo $msg;?>
						</div><br/>
					<?php  } ?>
					<div id="add-hide-div">
						<form name = "myForm" method="post" action="">
							<div class="page-form">
								<h2>ADD DRIVER</h2>
								<br><br>
								<ul>
									<li>
										FIRST NAME<br>
										<input type="text" name="vName" class="form-control" placeholder="First" required>
									</li>
									<li>
										LAST NAME<br>
										<input type="text" name="vLname" class="form-control" placeholder="Last" required>
									</li>
									<li>
										EMAIL<br>
										<input type="email" name="vEmail" class="form-control" placeholder="" required>
									</li>
									<li>
										Company<br>
										<select class="form-control" name = 'iCompanyId' id = 'iCompanyId' required>
											<option value="">--select--</option>
											<?php  for ($i = 0; $i < count($db_company); $i++) { ?>
												<option value ="<?= $db_company[$i]['iCompanyId'] ?>"><?= $db_company[$i]['vName'] . " " . $db_company[$i]['vLastName'] . " (" . $db_company[$i]['vCompany'] . ")"; ?></option>
											<?php  } ?>
										</select>
										<!--<input type="text" name="vEmail" class="form-control" placeholder="" >-->
									</li>
									<li>
										Country<br>
										<select class="contry-select" name = 'vCountry' onChange="changeCode(this.value);" required>
											<option value="">--select--</option>
											<?php  for ($i = 0; $i < count($db_country); $i++) { ?>
												<option value = "<?= $db_country[$i]['vCountryCode'] ?>"><?= $db_country[$i]['vCountry'] ?></option>
											<?php  } ?>
										</select>
										<!--<input type="text" name="vEmail" class="form-control" placeholder="" >-->
									</li>
									<li>
										Language<br>
										<select name = 'vLang' class="language-select" required>
											<option value="">--select--</option>
											<?php 	for ($i = 0; $i < count($db_lang); $i++) { ?>
												<option value = "<?= $db_lang[$i]['vCode'] ?>"><?= $db_lang[$i]['vTitle'] ?></option>
											<?php  } ?>
										</select>
										<!--<input type="text" name="vEmail" class="form-control" placeholder="" >-->
									</li>
									<li>
										MOBILE<br>
										<input type="text" class="form-select-2" id="code" name="vCode">
										<input type="text" name="vPhone" class="mobile-text" placeholder="" required pattern=".{10}"/>
									</li>
									
									<li>
										PASSWORD<br>
										<input type="password" class="form-control" placeholder="" name="vPassword" required>
									</li>
									
									<li>
										<input type="submit" name="submit" class="submit-btn" value="SUBMIT" >
									</li>
								</ul>
							</div>
						</form>
					</div>
					<div class="table-list">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading driver-neww1">
										<b><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> </b>
                                       <div class="button-group driver-neww">
															<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"><span class="">Select Option</span> <span class="caret"></span></button>
															<ul class="dropdown-menu">
																<li><a href="#" class="small" data-value="Active" tabIndex="-1"><input type="checkbox" id="checkbox" checked="checked"/>&nbsp;Active</a></li>
																<li><a href="#" class="small" data-value="Inactive" tabIndex="-1"><input type="checkbox" id="checkbox"  checked="checked"/>&nbsp;Inactive</a></li>
																<li><a href="#" class="small" data-value="Deleted" tabIndex="-1"><input type="checkbox" id="checkbox" checked="checked" />&nbsp;Delete</a></li>
																
															</ul>
														</div>
									</div>
									<div class="panel-body">
										<div class="table-responsive" id="data_drv001">
											<table class="table table-striped table-bordered table-hover admin-td-button" id="dataTables-example">
												<thead>
													<tr>
														<th><?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> NAME</th>
														<th>COMPANY NAME</th>
														<th>EMAIL</th>
                            <th>TAXI COUNT</th>
														<th>SIGN UP DATE</th>
														<!--<th>SERVICE LOCATION</th>-->
														<th>MOBILE</th>
														<!--<th>LANGUAGE</th>-->
														<th>STATUS</th>
														<th>EDIT DOCUMENT</th>
														<th style="text-align:center;" align="center">ACTION</th>
														
													</tr>
												</thead>
												<tbody>
													<?php  for ($i = 0; $i < count($data_drv); $i++) { ?>
														<tr class="gradeA" >
															<td width="10%"><?= $data_drv[$i]['vName'] . ' ' . $data_drv[$i]['vLastName']; ?></td>
															<td width="10%"><?= $data_drv[$i]['companyFirstName']; ?></td>
															<td width="10%"><?= $generalobjAdmin->clearEmail($data_drv[$i]['vEmail']);?></td>
                              <?php 
                              $sql = "select * from driver_vehicle WHERE iDriverId=".$data_drv[$i]['iDriverId'];
	                            $dbVehicle = $obj->MySQLSelect($sql);
                              ?>
                              <td><a href="vehicles.php?&actionSearch=1&iDriverId=<?=$data_drv[$i]['iDriverId'];?>" target="_blank"><?php  echo count($dbVehicle);?></a></td>
															<td width="15%" data-order="<?=$data_drv[$i]['iDriverId']; ?>"><?= $data_drv[$i]['tRegistrationDate']; ?></td>
															<!--<td class="center"><?= $data_drv[$i]['vServiceLoc']; ?></td>-->
															<td width="8%"><?= $generalobjAdmin->clearPhone($data_drv[$i]['vPhone']);?></td>
															<!--<td><?= $data_drv[$i]['vLang']; ?></td>-->
															<td width="8%" align="center">
																 <?php  if($data_drv[$i]['eDefault']!='Yes'){?>
																
																	<?php  if($data_drv[$i]['eStatus'] == 'active') {
																			$dis_img = "img/active-icon.png";
																		}else if($data_drv[$i]['eStatus'] == 'inactive'){
																			 $dis_img = "img/inactive-icon.png";
																		}else if($data_drv[$i]['eStatus'] == 'Deleted'){
																			$dis_img = "img/delete-icon.png";
																		}?>
																			<img src="<?=$dis_img;?>" alt="image">
																		<?php 
																	  }
																	  else
																	  {
																		?><img src="img/active-icon.png" alt="image"><?php 
																		}
																	  ?>
															</td>
															<td width="10%" align="center">
																<?php  if($data_drv[$i]['eStatus']=="Deleted"){
																	$newUrl2 = "javascript:void(0);";
																}else {
																	$newUrl2 = "driver_document_action.php?id=".$data_drv[$i]['iDriverId']."&action=edit&user_type=driver";
																}
																?>
																<?php  if($data_drv[$i]['eStatus']!="Deleted"){?> 
																	<a href="<?= $newUrl2; ?>" data-toggle="tooltip" title="Edit <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> Document">
																		<img src="img/edit-doc.png" alt="Edit Document" >
																	</a>
																<?php  }?>
															</td>
															
															<td width="20%" align="center">
																<?php  if($data_drv[$i]['eStatus']=="Deleted"){
																	$newUrl = "javascript:void(0);";
																}else {
																	$newUrl = "driver_action.php?id=".$data_drv[$i]['iDriverId'];
																}
																?>
																<?php  if($data_drv[$i]['eStatus']!="Deleted"){?> 
																	<a href="<?= $newUrl; ?>" data-toggle="tooltip" title="Edit <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?>">
																		<img src="img/edit-icon.png" alt="Edit">
																	</a>
																<?php  }?>
																
																<a href="driver.php?iDriverId=<?= $data_drv[$i]['iDriverId']; ?>&status=inactive" data-toggle="tooltip" title="Active <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?>">
																	<img src="img/active-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >
																</a>
																<a href="driver.php?iDriverId=<?= $data_drv[$i]['iDriverId']; ?>&status=active" data-toggle="tooltip" title="Inactive <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?>">
																	<img src="img/inactive-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >
																</a>
												
																<?php  if($data_drv[$i]['eStatus']!="Deleted"){?>	
																	<form name="delete_form" id="delete_form" method="post" action="" onSubmit="return confirm('Are you sure you want to delete <?= $data_drv[$i]['vName']; ?> <?= $data_drv[$i]['vLastName']; ?> record?')" class="margin0">
																		<input type="hidden" name="hdn_del_id" id="hdn_del_id" value="<?= $data_drv[$i]['iDriverId']; ?>">
																		<input type="hidden" name="action" id="action" value="delete">
																			<button class="remove_btn001" data-toggle="tooltip" title="Delete <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?>">	
																				<img src="img/delete-icon.png" alt="Delete">
																			</button>
																	</form>
																<?php  }else{?>
																		<label></label>
																<?php  } ?>	
																
																<?php  if($data_drv[$i]['eStatus']!="Deleted"){?>
																	<form name="reset_form" id="reset_form" method="post" action="" onSubmit="return confirm('Are you sure ? You want to reset <?= $data_drv[$i]['vName']; ?> <?= $data_drv[$i]['vLastName']; ?> account?')" class="margin0">
																		<input type="hidden" name="action" id="action" value="reset">
																		<input type="hidden" name="res_id" id="res_id" value="<?= $data_drv[$i]['iDriverId']; ?>">
																		<button class="remove_btn001" data-toggle="tooltip" title="Reset <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?>">
																			<img src="img/reset-icon.png" alt="Reset">
																		</button>
																	</form>
																<?php  }else{?>
																		<label></label>
																<?php  } ?>
															</td>
															
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
					<div style="clear:both;"></div>
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>
		<!--END MAIN WRAPPER -->
		
		
        <?php  include_once('footer.php');?>
		<script src="../assets/plugins/dataTables/jquery.dataTables.js"></script>
		<script src="../assets/plugins/dataTables/dataTables.bootstrap.js"></script>
		<script type="text/javascript">
			 var options = ["Active","Inactive","Deleted"];
			
			$( '.dropdown-menu a' ).on( 'click', function( event ) {
				//alert(options);
				var $target = $( event.currentTarget ),
				val = $target.attr( 'data-value' ),
				$inp = $target.find( 'input' ),
				idx;
				
				if ( ( idx = options.indexOf( val ) ) > -1 ) {
					options.splice( idx, 1 );
					setTimeout( function() { $inp.prop( 'checked', false ) }, 0);
					} else {
					options.push( val );
					setTimeout( function() { $inp.prop( 'checked', true ) }, 0);
				}
				//alert(options);
				$( event.target ).blur();
				
				//console.log( options );
				//alert(options);
				var request = $.ajax({
					type: "POST",
					url: 'change_driver_list.php',
					data: {result:JSON.stringify(options)},
					success: function (data)
					{
						$("#data_drv001").html('');
						$("#data_drv001").html(data);
						//document.getElementById("code").value = data;
						//window.location = 'profile.php';
					}
				});
				return false;
			});
		</script>
		
		<script>
			$(document).ready(function () {
				$('#dataTables-example').dataTable({
					"order": [[ 3, "desc" ]]
				});
			});
			
			
		</script>
	</body>
	<!-- END BODY-->
</html>