<?php 
	
	 $iCompanyId = isset($_REQUEST['iCompanyid']) ? $_REQUEST['iCompanyid'] : '';
     $ssqlcmp ='';
	if($iCompanyId !=''){
		$ssqlcmp =" AND rd.iCompanyId ='$iCompanyId'";
		
	}
     $data1   = $_POST["result"];
     $data    = json_decode($data1, true);
     $status=array();
     for($i=0;$i<count($data);$i++)
     {
          if($data[$i]=="Active")
          {
              $status[] = "'Active'";
          }
          if($data[$i]=="Inactive")
          {
               $status[] = "'Inactive'";    
          }
          if($data[$i]=="Deleted")
          {
               $status[] = "'Deleted'";
          }
     }
     $status=implode(",",$status);
      if($status != NULL)
     {
        $status_query="And rd.eStatus IN(".$status.")";
     }
     else
     {
          $status_query="And rd.eStatus = 'Not Deleted'";    
     }
     
     #echo"<pre>";print_r($status);
     $cmp_ssql = "";
     if(SITE_TYPE =='Demo'){
          $cmp_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
     }
     include '../common.php';
    
     $sql = "SELECT rd.*,c.vCompany FROM register_driver rd left join company c on rd.iCompanyId=c.iCompanyId WHERE 1=1 ".$cmp_ssql.$status_query.$ssqlcmp;
     $data_drv = $obj->MySQLSelect($sql);
    if(!isset($generalobjAdmin)){
     require_once(TPATH_CLASS."class.general_admin.php");
     $generalobjAdmin = new General_admin();
}
     #echo"<pre>";print_r($data_drv);
?>


	<table class="table table-striped table-bordered table-hover admin-td-button" id="dataTables-example">
			<thead>
				<tr>
					<th><?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> NAME</th>
					<th>COMPANY NAME</th>
					<th>EMAIL</th>
					<th>SIGN UP DATE</th>
					<!--<th>SERVICE LOCATION</th>-->
					<th>MOBILE</th>
					<!--<th>LANGUAGE</th>-->
					<th>STATUS</th>
					<th>EDIT DOCUMENT</th>
					<th style="text-align: center;">ACTION</th>
					
				</tr>
			</thead>
			<tbody>
				<?php  for ($i = 0; $i < count($data_drv); $i++) { ?>
					<tr class="gradeA" >
						<td width="10%"><?= $generalobjAdmin->clearName($data_drv[$i]['vName'] . ' ' . $data_drv[$i]['vLastName']); ?></td>
						<td width="10%"><?= $data_drv[$i]['vCompany']; ?></td>
						<td width="10%"><?= $generalobjAdmin->clearEmail($data_drv[$i]['vEmail']);?></td>
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
								$newUrl2 = "driver_document_action.php?id=".$data_drv[$i]['iDriverId']."&action=edit";
							}
							?>
							<?php  if($data_drv[$i]['eStatus']!="Deleted"){?> 
								<a href="<?= $newUrl2; ?>" data-toggle="tooltip" title="Edit <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> Document">
									<img src="img/edit-doc.png" alt="Edit Document" >
								</a>
							<?php  }?>
						</td>
						
						<td width="20%">
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
								<form name="delete_form" id="delete_form" method="post" action="" onSubmit="return confirm('Are you sure you want to delete <?=$generalobjAdmin->clearName($data_drv[$i]['vName']." ".$data_drv[$i]['vLastName']); ?> record?')" class="margin0">
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
								<form name="reset_form" id="reset_form" method="post" action="" onSubmit="return confirm('Are you sure ? You want to reset <?= $generalobjAdmin->clearName($data_drv[$i]['vName']." ".$data_drv[$i]['vLastName']); ?> account?')" class="margin0">
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
<script>
$(document).ready(function () {
	$('#dataTables-example').dataTable({
		"order": [[ 3, "desc" ]]
	});
});
</script>












