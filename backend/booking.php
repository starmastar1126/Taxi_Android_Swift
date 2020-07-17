<?php 
	include_once('common.php');
	$generalobj->check_member_login();
	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
	$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
	$var_msg = isset($_REQUEST["var_msg"]) ? $_REQUEST["var_msg"] : '';
	$iCompanyId = $_SESSION['sess_iUserId'];
	
	//echo "<pre>";print_r($_SESSION);exit;
	
	$sql = "select * from country";
	$db_country = $obj->MySQLSelect($sql);
	
	$sql = "select * from language_master where eStatus = 'Active'";
	$db_lang = $obj->MySQLSelect($sql);
	
	$script = 'Booking';
		
	$tbl_name = "cab_booking";
	
	$cmp_ssql = " AND cb.iCompanyId = '" . $iCompanyId . "'";
	if(SITE_TYPE =='Demo'){
		$cmp_ssql .= " And cb.dAddredDate > '".WEEK_DATE."'";
	}

	if ($action == 'view') {
		 $sql = "SELECT cb.*,CONCAT(ru.vName,' ',ru.vLastName) as rider,CONCAT(rd.vName,' ',rd.vLastName) as driver,vt.vVehicleType FROM cab_booking as cb
		 LEFT JOIN register_user as ru on ru.iUserId=cb.iUserId
		 LEFT JOIN register_driver as rd on rd.iDriverId=cb.iDriverId
		 LEFT JOIN vehicle_type as vt on vt.iVehicleTypeId=cb.iVehicleTypeId WHERE 1".$cmp_ssql;
		 $data_drv = $obj->MySQLSelect($sql);
		 //echo "<pre>"; print_r($data_drv); die;
	}
	
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width,initial-scale=1">
		<title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_MY_BOOKINGS']; ?></title>
		<!-- Default Top Script and css -->
		<?php  include_once("top/top_script.php");?>
		<!-- End: Default Top Script and css-->
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
			<!-- contact page-->
			<div class="page-contant">
				<div class="page-contant-inner">
					<h2 class="header-page trip-detail driver-detail1"><?=$langage_lbl['LBL_MY_BOOKINGS']; ?><a href="javascript:void(0);" onClick="manual_dispatch_form();"><?=$langage_lbl['LBL_ADD_BOOKING']; ?></a></h2>
					<!-- trips page -->
					<div class="trips-page trips-page1">
						<?php  if ($_REQUEST['success']==1) {?>
							<div class="alert alert-success alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button> 
								<?= $var_msg ?>
							</div>
							<?php }else if($_REQUEST['success']==2){ ?>
							<div class="alert alert-danger alert-dismissable">
								<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								"Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
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
												<th width="25%"><?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?></th>
												<th width="20%"><?php  echo $langage_lbl_admin['LBL_DATE_SIGNUP'];?></th>
												<th><?php  echo $langage_lbl_admin['LBL_PICKUP'];?></th>
												<th width="10%"><?php  echo $langage_lbl_admin['LBL_DESTINATION'];?></th>
												<th width="15%" style="width: 67px;"><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></th>
												<th width="14%"><?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'];?>  <?php  echo $langage_lbl_admin['LBL_DETAILS'];?></th>
												<th width="8%"><?php  echo $langage_lbl_admin['LBL_Status'];?></th>
											</tr>
										</thead>
										<tbody>
											<?php  for ($i = 0; $i < count($data_drv); $i++) { ?>
												<tr class="gradeA">
													<td><?= $data_drv[$i]['rider']; ?></td>
													<td data-order="<?=$data_drv[$i]['iCabBookingId']; ?>"><?= date('dS M Y,',strtotime($data_drv[$i]['dBooking_date'])); ?> <?= date('H:i',strtotime($data_drv[$i]['dBooking_date'])); ?></td>
													<td><?= $data_drv[$i]['vSourceAddresss']; ?></td>
													<td><?= $data_drv[$i]['tDestAddress']; ?></td>
													
													<?php  if ($data_drv[$i]['eAutoAssign'] == "Yes") { ?>
														<td><?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> <?=$langage_lbl['LBL_AUTO_ASSIGNED']; ?><a class="btn btn-info" href="manual_dispatch.php?booking_id=<?= $data_drv[$i]['iCabBookingId']; ?>" data-tooltip="tooltip" title="Edit"><i class="icon-edit icon-flip-horizontal icon-white"></i></a><br>( Car Type : <?= $data_drv[$i]['vVehicleType']; ?>)</td>
													<?php  } else if ($data_drv[$i]['eStatus'] == "Pending") { ?>
														<td><a class="btn btn-info" href="manual_dispatch.php?booking_id=<?= $data_drv[$i]['iCabBookingId']; ?>"><i class="icon-shield icon-flip-horizontal icon-white"></i> Assign <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></a><br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>)</td>
													<?php  } else if($data_drv[$i]['eCancelBy'] == "Driver" && $data_drv[$i]['eStatus'] == "Cancel") { ?>
														<td><a class="btn btn-info" href="manual_dispatch.php?booking_id=<?= $data_drv[$i]['iCabBookingId']; ?>"><i class="icon-shield icon-flip-horizontal icon-white"></i> Assign <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></a><br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>)</td>
													<?php  } else if ($data_drv[$i]['driver'] != "" && $data_drv[$i]['driver'] != "0") { ?>
														<td><b><?= $data_drv[$i]['driver']; ?></b><br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>) </td>
													<?php  } else  { ?>
														<td>---<br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>)</td>
													<?php  } ?>
													
													<td><?php  if($data_drv[$i]['iTripId'] != "" && $data_drv[$i]['eStatus'] == "Completed") { ?><a class="btn btn-primary" href="invoice.php?iTripId=<?=$data_drv[$i]['iTripId'];?>"><?=$langage_lbl['LBL_MYTRIP_VIEW'];?></a><?php  }else {echo "---"; } ?></td>
													<td>
													<?php  if($data_drv[$i]['eStatus'] == "Assign") {
													echo "Driver Assigned";
													}else { 
														$sql="select iActive from trips where iTripId=".$data_drv[$i]['iTripId'];
														$data_stat=$obj->MySQLSelect($sql);
													//echo "<pre>";print_r($data_stat); die;
													if($data_stat)
													{
														for($d=0;$d<count($data_stat);$d++)
														{echo $data_stat[$d]['iActive']; }
													}
													 else
													 {echo $data_drv[$i]['eStatus'];}
												   }?>
													<?php  if ($data_drv[$i]['eStatus'] == "Cancel") { ?>
														<br /><a href="javascript:void(0);" class="btn btn-info" data-toggle="modal" data-target="#uiModal_<?=$data_drv[$i]['iCabBookingId'];?>"><?=$langage_lbl['LBL_CANCEL_REASON'];?></a>
													<?php  } ?>
											  </td>
											 </tr>
										   <div class="col-lg-12">
												 <div class="modal fade" id="uiModal_<?=$data_drv[$i]['iCabBookingId'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
													  <div class="modal-content image-upload-1" style="width:400px;">
														   <div class="upload-content" style="width:350px;">
																<h3><?=$langage_lbl['LBL_BOOKING_CANCEL_REASON']; ?> :</h3>
																<h4><?=$langage_lbl['LBL_CANCEL_BY']; ?> : <?=$data_drv[$i]['eCancelBy'];?></h4>
																<h4><?=$langage_lbl['LBL_CANCEL_REASON']; ?> : <?=$data_drv[$i]['vCancelReason'];?></h4>
																<form class="form-horizontal" id="frm6" method="post" enctype="multipart/form-data" action="" name="frm6">
																	 <div class="form-group">
																		  <div class="col-lg-12">
																			  
																		  </div>
																	 </div>
																	 <div class="col-lg-13">
																		  
																	 </div>
									 
																	 
																	 <input type="button" class="save" data-dismiss="modal" name="cancel" value="Close">
																</form>
														   </div>
													  </div>
												 </div>
											</div>                                                                 
											 <?php  } ?>
										</tbody>
									</table>
								</div>  </div>
						</div>
						<!-- -->
						<?php  //if(SITE_TYPE=="Demo"){?>
							<!-- <div class="record-feature"> <span><strong>“Edit / Delete Record Feature”</strong> has been disabled on the Demo Admin Version you are viewing now.
							This feature will be enabled in the main product we will provide you.</span> </div> --->
						<?php  //}?>
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
				$('#dataTables-example').dataTable();
			});
			function confirm_delete(id)
			{
				bootbox.confirm("<?=$langage_lbl['LBL_DELETE_DRIVER_CONFIRM_MSG']; ?>", function(result) {
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
			
			function manual_dispatch_form(){
				window.location.href = "manual_dispatch.php";
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
