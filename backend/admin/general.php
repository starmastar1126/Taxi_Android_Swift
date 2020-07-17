<?php 
header('X-XSS-Protection:0');
  include_once('../common.php');
	$msgType = "";
	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();

	$script 	= 'General';
	$activeTab = "General";
  $msgType = isset($_REQUEST['msgType']) ? $_REQUEST['msgType'] : '';
  $msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : '';

	if(isset($_POST['frm_type']) && $_POST['frm_type']!="") {   
	  if(SITE_TYPE =='Demo'){
       $msgType = 0;
       $msg = "Edit / Delete Record Feature has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.";
       header("Location:general.php?msgType=".$msgType."&msg=".$msg);exit;
       exit;
    }
		$activeTab = str_replace(" ","_",$_REQUEST['frm_type']);
		foreach ($_REQUEST['Data'] as $key => $value) {
			unset($updateData);
			$updateData['vValue'] = $value;
			$where = " vName = '".$key."' AND eType = '".$_REQUEST['frm_type']."'";
			$res = $obj->MySQLQueryPerform("configurations",$updateData,'update',$where);
		}
		if($res) {
			$msgType = 1;
			$msg = "Successfully updated Configuration";
		}
		else {
			$msgType = 0;
			$msg = "Error in update configuration";
		}
	}

	$sql = "SELECT * FROM configurations WHERE eStatus = 'Active' ORDER BY eType, vOrder";
	$data_gen = $obj->MySQLSelect($sql);
	//country
	$sql1 = "SELECT * FROM country WHERE eStatus = 'Active' ";
	$country_name = $obj->MySQLSelect($sql1);

	foreach ($data_gen as $key => $value) {	
		$db_gen[$value['eType']][$key]['iSettingId'] = $value['iSettingId'];
		$db_gen[$value['eType']][$key]['tDescription'] = $value['tDescription'];
		$db_gen[$value['eType']][$key]['vValue'] = $value['vValue'];
		$db_gen[$value['eType']][$key]['tHelp'] = $value['tHelp'];
		$db_gen[$value['eType']][$key]['vName'] = $value['vName'];
		$db_gen[$value['eType']][$key]['eInputType'] = $value['eInputType'];
		$db_gen[$value['eType']][$key]['tSelectVal'] = $value['tSelectVal'];
	}
	//echo "<pre>";print_r($db_gen);exit;
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title><?=$SITE_NAME;?> | Configuration</title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />

		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

		<?php  include_once('global_files.php');?>
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
								<h2> General Settings </h2>
							</div>
						</div>
						<hr />
					</div>
					<div class="table-list">
						<div class="row">
							<div class="col-lg-12">
								<div class="panel panel-default">
									<div class="panel-heading">
										 General  Settings
									</div>
									<div class="panel-body">
										<div class="row">
											<div class="col-lg-12">
											<?php 
												if($msgType == '1') {
											?>	
												<div class="alert alert-success alert-dismissable">
													<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
													<?=$msg?> 
												</div>
											<?php 
												}
												elseif ($msgType == '0') {
											?>
												<div class="alert alert-danger alert-dismissable">
													<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
													<?=$msg?> 
												</div>
											<?php 
												}
											?>
											</div>
										</div>
										<ul class="nav nav-tabs">
											<?php 
												//$active =0;
												foreach ($db_gen as $key => $value) {
													//$active++;
													$newKey = str_replace(" ","_",$key);
											?>
												<li class="<?php  echo $activeTab == $newKey?'active':''?>">
													<a data-toggle="tab" href="#<?=$newKey?>">
														<?php 
															if($key == "Apperance")
																echo "Appearance";
															else
																echo $key
														?>
													</a>
												</li>
											<?php 
												}
											?>
										</ul>
										<div class="tab-content">
											<?php 
												//$active =0;
												foreach ($db_gen as $key => $value) {
													//$active++;
													$cnt = count($value);
													$tab1 = ceil(count($value)/2);
													$tab2 = $cnt - $tab1;
													$newKey = str_replace(" ","_",$key);
											?>
											
											<div id="<?=$newKey?>" class="tab-pane <?php  echo $activeTab == $newKey?'active':''?>">
												<form method="POST" action="" name="frm_<?=$key?>">
													<input type="hidden" name="frm_type" value="<?=$key?>">
													<div class="row">
														<div class="col-lg-6">
														<?php 
															$i = 0;
															$temp = true;
															foreach ($value as $key1 => $value1) {
																$i++;
																if($tab1 < $i && $temp){
																	$temp = false;
															?>
															</div>
															<div class="col-lg-6">
														<?php 
															}
														?>
															<div class="form-group">
																<label><?=$value1['tDescription']?><?php  if($value1['tHelp']!=""){?> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='<?= htmlspecialchars($value1['tHelp'], ENT_QUOTES, 'UTF-8') ?>'></i><?php  }?></label>
																<?php 
																	
																	if($value1['eInputType'] == 'Textarea') {
																?>
																	<textarea class="form-control" rows="5" name="Data[<?=$value1['vName']?>]"><?=$value1['vValue']?></textarea>
																<?php 
																	} elseif ($value1['eInputType'] == 'Select') {
																		$optionArr = explode(',', $value1['tSelectVal']);
																	
																		if($value1['vName'] == 'DEFAULT_COUNTRY_CODE_WEB'){ ?>
																		
																			<select class="form-control" name="Data[<?=$value1['vName']?>]">
																			<?php 
																			foreach ($country_name as $Value) {
																				$selected = $value1['vValue']==$Value['vCountryCode']?'selected':'';
																			?>
																			<option value="<?=$Value['vCountryCode']?>" <?=$selected?>><?=$Value['vCountry']. ' ('.$Value['vCountryCode'].')';?></option>
																			<?php 
																				}
																			?>
																			</select>
																		<?php  } else if($value1['vName'] == 'ENABLE_HAIL_RIDES') { ?>

																			<select class="form-control" name="Data[<?=$value1['vName']?>]">
																			<?php 
																				foreach ($optionArr as $oKey => $oValue) {
																					$selected = $oValue==$value1['vValue']?'selected':'';
																			?>
																				<option value="<?=$oValue?>" <?=$selected?>><?=$oValue?></option>
																			<?php 
																				}
																			?>
																			</select>
																			<div> [Note: This option will not work if you have selected payment mode "Card"] </div>
																		<?php  }  else if($value1['vName'] == 'DRIVER_REQUEST_METHOD') { ?>
																			<select class="form-control" name="Data[<?=$value1['vName']?>]">
																			<?php 
																				foreach ($optionArr as $oKey => $oValue) {
																					$selected = $oValue==$value1['vValue']?'selected':'';
																					if($oValue == 'All'){
																						$oValuenew = $oValue." (COMPETITIVE ALGORITHM)";
																					} else if($oValue == 'Distance') {
																						$oValuenew = $oValue." (Nearest 1st Algorithm)";
																					} else if($oValue == 'Time') {
																						$oValuenew = $oValue." (FIFO Algorithm)";
																					} else {
																						$oValuenew = $oValue;
																					};
																			?>
																				<option value="<?=$oValue?>" <?=$selected?>><?=$oValuenew?></option>
																			<?php 
																				}
																			?>
																			</select>
																		<?php  } else { ?>
																		
																			<select class="form-control" name="Data[<?=$value1['vName']?>]">
																			<?php 
																				foreach ($optionArr as $oKey => $oValue) {
																					$selected = $oValue==$value1['vValue']?'selected':'';
																			?>
																				<option value="<?=$oValue?>" <?=$selected?>><?=$oValue?></option>
																			<?php 
																				}
																			?>
																			</select>
																		
																		<?php 	}	?>
																		<!--<select class="form-control" name="Data[<?=$value1['vName']?>]">
																			<?php 
																				foreach ($optionArr as $oKey => $oValue) {
																					$selected = $oValue==$value1['vValue']?'selected':'';
																			?>
																				<option value="<?=$oValue?>" <?=$selected?>><?=$oValue?></option>
																			<?php 
																				}
																			?>
																		</select>-->
																<?php 
																	} else {
																?>
																	<input type="text" name="Data[<?=$value1['vName']?>]" class="form-control" value="<?=$value1['vValue']?>" >
																<?php 
																	}
																?>
															</div>
														<?php 
															}
														?>
														</div>
													</div>
													<div class="row">
														<div class="col-lg-12">
															<div class="form-group" style="text-align: center;">
																<button class="btn btn-primary save-configuration" type="submit">Save Changes</button>
															</div>
														</div>
													</div>
												</form>
											</div>
											<?php 
												}
											?>
										</div>
									</div>
								</div>
							</div> <!--TABLE-END-->
						</div>
					</div>
                    <div class="clear"></div>
				      <?php  if(SITE_TYPE !='Demo') { ?>
						<div class="admin-notes">
						<h4>Notes:</h4>
						<ul>
						    <li>
						        Please close the application and open it again to see the settings reflected after saving the new setting values above.
						    </li>
						</ul>
						</div>
					<?php  } ?>
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>
		<!--END MAIN WRAPPER -->
		
		<?php 
			include_once('footer.php');
		?>
		<script src="../assets/plugins/dataTables/jquery.dataTables.js"></script>
		<script src="../assets/plugins/dataTables/dataTables.bootstrap.js"></script>
		<script>
			$('[data-toggle="tooltip"]').tooltip();
			$(document).ready(function () {
				$('#dataTables-example').dataTable();
			});
			function confirm_delete()
			{
				var confirm_ans = confirm("Are You sure You want to Delete Driver?");
				return confirm_ans;
				//document.getElementById(id).submit();
			}
			function changeCode(id)
			{
				var request = $.ajax({
					type: "POST",
					url: 'change_code.php',
					data: 'id='+id,

					success: function(data)
					{
						document.getElementById("code").value = data ;
						//window.location = 'profile.php';
					}
				});
			}
		</script>
	</body>
	<!-- END BODY-->
</html>
