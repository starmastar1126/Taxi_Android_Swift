<?php 
include_once("../common.php");
 // error_reporting(E_ALL);  
// ini_set('display_errors','1');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$iCompanyId = isset($_REQUEST['iCompanyId'])?$_REQUEST['iCompanyId']:''; 
// $generalobjAdmin->clearName(

// $sql="select cmp.* from company where iCompanyId = '$iCompanyId'";

$sql="select cmp.*,cn.vCountry as country,ct.vCity as city,st.vState as state from company cmp
left join country cn on cn.vCountryCode = cmp.vCountry
left join city ct on ct.iCityId = cmp.vCity
left join state st on st.iStateId = cmp.vState
where iCompanyId = '$iCompanyId'";
$data_company = $obj->MySQLSelect($sql);

$reg_date1 = $data_company[0]['tRegistrationDate'];
// Tuesday, Aug  22<sup>nd</sup> 2017
if($reg_date1 != "0000-00-00 00:00:00"){
	$reg_date = date("l, M d \<\s\u\p\>S\<\/\s\u\p\>\ Y",strtotime($reg_date1));
}else{
	$reg_date = "";
}
 // exit;
if($data_company[0]['vImage'] != "")
	$image_path = $tconfig["tsite_upload_images_compnay"].'/'.$iCompanyId.'/2_'.$data_company[0]['vImage'];
else{
	$image_path = "../assets/img/profile-user-img.png";
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
	<!--<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4><i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i>Company Details
					<button type="button" class="close" data-dismiss="modal">x</button>
					</h4>
				</div>
				<div class="modal-body" style="max-height: 450px;overflow: auto;"> -->
				
				
					<table border="1" class="table table-bordered" width="100%" align="center" cellspacing="5" cellpadding="10px">
						<tbody>
						<tr>
							<td rowspan="3" height="150px" width="150px" ><img width="150px" src="<?=$image_path?>"></td>
							<td>
								<table border="0" width="100%" height="150px" cellspacing="5" cellpadding="5px">
									<tr>
										<td width="140px" class="text_design">Company Name</td>
										<td><?=$generalobjAdmin->clearCmpName($data_company[0]['vCompany'])?></td>
									</tr>
									<tr>
										<td class="text_design">Email</td>
										<td><?=$generalobjAdmin->clearEmail($data_company[0]['vEmail'])?></td>
									</tr>
									
									<tr>
										<td class="text_design">Phone Number</td>
										<td>
											<?php 
												$phone = "+";
												if($data_company[0]['vCode'] != ""){
													$phone .= $data_company[0]['vCode']."-";
												}
												$phone .= $data_company[0]['vPhone'];
												echo $generalobjAdmin->clearPhone($phone);
											?>
										</td>
									</tr>
									<?php  if($reg_date != ""){?>
									<tr>
										<td class="text_design">Registration Date</td>
										<!-- <td>Tuesday, Aug  22<sup>nd</sup> 2017</td> -->
										<td><?=$reg_date?></td>
									</tr>
									<?php  } ?>
									<tr>
										<td class="text_design">Status</td>
										<td>
											<?php 
												$class="";
												if($data_company[0]['eStatus'] == "Active"){
													$class = "btn-success";
												}else if($data_company[0]['eStatus'] == "Inactive"){
													$class = "btn";
												}else{
													$class = "btn-danger";
												}
											?>
											<button class="btn <?=$class?> no-cursor"><?=$data_company[0]['eStatus']?></button>
										</td>
									</tr>
									
								</table>
							</td>
						</tr><tr></tr><tr></tr><tr></tr>
						<tr>
							<td class="text_design">Company Address</td>
							<td>
								<?php 
									$address1 = $data_company[0]['vCaddress'];
									if($data_company[0]['vCadress2'] != ""){
										$conc = ($address1 != "") ? ", " : "";
										$address1 .= $conc.$data_company[0]['vCadress2'];
									}
									if($data_company[0]['city'] != ""){
										$conc = ($address1 != "") ? ", " : "";
										$address1 .= $conc.$data_company[0]['city'];
									}
									if($data_company[0]['vZip'] != ""){
										$conc = ($address1 != "") ? ", " : "";
										$address1 .= $conc.$data_company[0]['vZip'];
									}
									if($data_company[0]['state'] != ""){
										$conc = ($address1 != "") ? ", " : "";
										$address1 .= $conc.$data_company[0]['state'];
									}
									
									if($data_company[0]['country'] != ""){
										$conc = ($address1 != "") ? ", " : "";
										$address1 .= $conc.$data_company[0]['country'];
									}
									echo $address1;
								?>	
							</td>
						</tr>
						<?php  if($data_company[0]['vVat'] != ""){?>
						<tr>
							<td class="text_design">Vat Number</td>
							<td>
								<?=$data_company[0]['vVat'];?>
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
					<a href="company_action.php?id=<?=$iCompanyId; ?>" class="btn btn-primary btn-ok" target="blank">Edit Company</a>
					<button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Close</button>
				</div>
				
				
	<!--</div>
	 </div> -->