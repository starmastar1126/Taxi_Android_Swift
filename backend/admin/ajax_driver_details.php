<?php 
include_once("../common.php");

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$iDriverId = isset($_REQUEST['iDriverId'])?$_REQUEST['iDriverId']:''; 
// $generalobjAdmin->clearName(

$sql="select rd.iDriverId,concat(rd.vName,' ',rd.vLastName) as Name,rd.vEmail,rd.vCode,rd.vPhone,rd.vImage,rd.eStatus,rd.vCaddress,rd.vCadress2,rd.vZip,rd.vVat,rd.tRegistrationDate,rd.vAvgRating,cn.vCountry as country,ct.vCity as city,st.vState as state,cmp.vCompany from register_driver rd 
left join company cmp on cmp.iCompanyId=rd.iCompanyId 
left join country cn on cn.vCountryCode = rd.vCountry
left join city ct on ct.iCityId = rd.vCity
left join state st on st.iStateId = rd.vState
where iDriverId = '$iDriverId'";
$data_driver = $obj->MySQLSelect($sql);

$reg_date1 = $data_driver[0]['tRegistrationDate'];
if($reg_date1 != "0000-00-00 00:00:00"){
	$reg_date = date("l, M d \<\s\u\p\>S\<\/\s\u\p\>\ Y",strtotime($reg_date1));
}else{
	$reg_date = "";
}
 // exit;
if($data_driver[0]['vImage'] != "" && file_exists($tconfig["tsite_upload_images_driver_path"].'/'.$iDriverId.'/2_'.$data_driver[0]['vImage']))
	$image_path = $tconfig["tsite_upload_images_driver"].'/'.$iDriverId.'/2_'.$data_driver[0]['vImage'];
else{
	$image_path = "../assets/img/profile-user-img.png";
}

$rating_width = ($data_driver[0]['vAvgRating'] * 100) / 5;
if($data_driver[0]['vAvgRating'] > 0){
	$Rating = '<span title="'.$data_driver[0]['vAvgRating'].'" style="display: block; width: 65px; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 0;">
	<span style="margin: 0;float:left;display: block; width: '.$rating_width.'%; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 -13px;"></span>
	</span>';
}else{
	$Rating = "No ratings received";
}


?>
	<style>
	.text_design{
		font-size: 12px;
		font-weight: bold;
		font-family: verdana;
	}
	.border_table{
		border:1px solid #dddddd;
	}
	.no-cursor{
	    cursor: text;
	}
	</style>
	
		<table border="1" class="table table-bordered" width="100%" align="center" cellspacing="5" cellpadding="10px">
						<tbody>
						<tr>
							<td rowspan="3" height="150px" width="150px" ><img width="150px" src="<?=$image_path?>"></td>
							<td>
								<table border="0" width="100%" height="150px" cellspacing="5" cellpadding="5px">
									<tr>
										<td width="140px" class="text_design">Driver Name</td>
										<td><?=$generalobjAdmin->clearName($data_driver[0]['Name'])?></td>
									</tr>
									<tr>
										<td class="text_design">Email</td>
										<td><?=$generalobjAdmin->clearEmail($data_driver[0]['vEmail'])?></td>
									</tr>
									
									<tr>
										<td class="text_design">Phone Number</td>
										<td>
											<?php 
												$phone = "+";
												if($data_driver[0]['vCode'] != ""){
													$phone .= $data_driver[0]['vCode']."-";
												}
												$phone .= $data_driver[0]['vPhone'];
												echo $generalobjAdmin->clearPhone($phone);
											?>
										</td>
									</tr>
									
									<tr>
										<td class="text_design">Rating</td>
										<td><?=$Rating?></td>
									</tr>
									<tr>
										<td class="text_design">Status</td>
										<td>
											<?php 
												$class="";
												if($data_driver[0]['eStatus'] == "active"){
													$class = "btn-success";
												}else if($data_driver[0]['eStatus'] == "inactive"){
													$class = "btn";
												}else if($data_driver[0]['eStatus'] == "Suspend"){
													$class = "btn-info";
												}else{
													$class = "btn-danger";
												}
											?>
											<button class="btn <?=$class?> no-cursor"><?=ucfirst($data_driver[0]['eStatus'])?></button>
										</td>
									</tr>
									
								</table>
							</td>
						</tr><tr></tr><tr></tr><tr></tr>
						<tr>
							<td class="text_design">Address</td>
							<td>
								<?php 
									$address1 = $data_driver[0]['vCaddress'];
									if($data_driver[0]['vCadress2'] != ""){
										$conc = ($address1 != "") ? ", " : "";
										$address1 .= $conc.$data_driver[0]['vCadress2'];
									}
									if($data_driver[0]['city'] != ""){
										$conc = ($address1 != "") ? ", " : "";
										$address1 .= $conc.$data_driver[0]['city'];
									}
									if($data_driver[0]['vZip'] != ""){
										$conc = ($address1 != "") ? ", " : "";
										$address1 .= $conc.$data_driver[0]['vZip'];
									}
									if($data_driver[0]['state'] != ""){
										$conc = ($address1 != "") ? ", " : "";
										$address1 .= $conc.$data_driver[0]['state'];
									}
									if($data_driver[0]['country'] != ""){
										$conc = ($address1 != "") ? ", " : "";
										$address1 .= $conc.$data_driver[0]['country'];
									}
									echo $address1;
								?>	
							</td>
						</tr>
						<tr>
							<td class="text_design">Driver Company</td>
							<td><?=$generalobjAdmin->clearCmpName($data_driver[0]['vCompany']);?></td>
						</tr>
						<?php  if($reg_date != ""){?>
									<tr>
										<td class="text_design">Registration Date</td>
										<!-- <td>Tuesday, Aug  22<sup>nd</sup> 2017</td> -->
										<td><?=$reg_date?></td>
									</tr>
									<?php  } ?>
						<?php  if($data_driver[0]['vVat'] != ""){?>
						<tr>
							<td class="text_design">Vat Number</td>
							<td>
								<?=$data_driver[0]['vVat'];?>
							</td>
						</tr>
						<?php  } ?>
						<!--
						<tr>
							<td>Total Vehicles</td>
							<td>25</td>
						</tr>
						<tr>
							<td>Driver Details</td>
							<td>
								<table  border="0" width="100%" cellspacing="2" cellpadding="10px">
									<tr>
										<td width="140px">Total Drivers</td>
										<td>25</td>
									</tr>
									<tr>
										<td>Active Drivers</td>
										<td>15</td>
									</tr>
									<tr>
										<td>Inactive Drivers</td>
										<td>10</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>Trip Details</td>
							<td>
								<table  border="0" width="100%"  cellspacing="5" cellpadding="10px">
									<tr>
										<td width="140px">Total Trips</td>
										<td>125</td>
									</tr>
									<tr>
										<td>Completed Trips</td>
										<td>100</td>
									</tr>
									<tr>
										<td>Cancelled Trips</td>
										<td>12</td>
									</tr>
									<tr>
										<td>Rejected Trips</td>
										<td>13</td>
									</tr>
								</table>
							</td>
						</tr> -->
						
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<a href="driver_action.php?id=<?=$iDriverId; ?>" class="btn btn-primary btn-ok" target="blank">Edit Driver</a>
					<button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Close</button>
				</div>
		