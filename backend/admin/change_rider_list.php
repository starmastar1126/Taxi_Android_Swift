<?php 
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
        $status_query="And eStatus IN(".$status.")";
     }
     else
     {
          $status_query="And eStatus = 'Not Deleted'";    
     }
     
     #echo"<pre>";print_r($status);
     $cmp_ssql = "";
     if(SITE_TYPE =='Demo'){
          $cmp_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
     }
     include '../common.php';
    
     $sql = "SELECT iUserId,vName,vLastName,vEmail,tRegistrationDate,vPhone,vPhoneCode,eStatus FROM register_user WHERE 1=1 ".$cmp_ssql.$status_query;
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
                <th>NAME</th>
                <th>EMAIL</th>
                <th>SIGN UP DATE</th>
                <th>MOBILE</th>
                <th>STATUS</th>
                <th>ACTION</th>
            </tr>
	</thead>
	<tbody>
		<?php  for($i=0;$i<count($data_drv);$i++) {?>
			<tr class="gradeA">
				<td><?php  echo $data_drv[$i]['vName'].' '.$data_drv[$i]['vLastName']; ?></td>
				<td><?php  echo $generalobjAdmin->clearEmail($data_drv[$i]['vEmail']); ?></td>
				<td data-order="<?=$data_drv[$i]['iUserId']; ?>"><?php  echo $data_drv[$i]['tRegistrationDate']; ?></td>
				<td class="center"><?= $generalobjAdmin->clearPhone($data_drv[$i]['vPhone']);?></td>
				<td width="10%" align="center">
					<?php  if($data_drv[$i]['eStatus'] == 'Active') {
						$dis_img = "img/active-icon.png";
						}else if($data_drv[$i]['eStatus'] == 'Inactive'){
						$dis_img = "img/inactive-icon.png";
						}else if($data_drv[$i]['eStatus'] == 'Deleted'){
						$dis_img = "img/delete-icon.png";
					}?>
					<img src="<?=$dis_img;?>" alt="<?=$data_drv[$i]['eStatus']?>">   
				</td>
				<td class="veh_act">
					<?php  if($data_drv[$i]['eStatus']!="Deleted"){?>
						<a href="rider_action.php?id=<?= $data_drv[$i]['iUserId']; ?>" data-toggle="tooltip" title="Edit <?=$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?>">
							<img src="img/edit-icon.png" alt="Edit">
						</a>
					<?php  }?>
					
					<a href="rider.php?iUserId=<?= $data_drv[$i]['iUserId']; ?>&status=Active" data-toggle="tooltip" title="Active <?=$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?>">
                                            <img src="img/active-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >
					</a>
					<a href="rider.php?iUserId=<?= $data_drv[$i]['iUserId']; ?>&status=Inactive" data-toggle="tooltip" title="Inactive <?=$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?>">
                                            <img src="img/inactive-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >
					</a>
					<?php  if($data_drv[$i]['eStatus']!="Deleted"){?>
                                            <form name="delete_form" id="delete_form" method="post" action="" onSubmit="return confirm_delete()" class="margin0">
                                                <input type="hidden" name="hdn_del_id" id="hdn_del_id" value="<?= $data_drv[$i]['iUserId']; ?>">
                                                <input type="hidden" name="action" id="action" value="delete">
                                                <button class="remove_btn001" data-toggle="tooltip" title="Delete <?=$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?>">
                                                    <img src="img/delete-icon.png" alt="Delete">
                                                </button>
                                            </form>

                                            <form name="reset_form" id="reset_form" method="post" action="" onSubmit="return confirm('Are you sure?you want to reset <?= $data_drv[$i]['vName'].' '.$data_drv[$i]['vLastName'];?> account?')" class="margin0">
                                                <input type="hidden" name="res_id" id="res_id" value="<?= $data_drv[$i]['iUserId']; ?>">
                                                <input type="hidden" name="action" id="action" value="reset">
                                                <button class="remove_btn001" data-toggle="tooltip" title="Reset <?=$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?>">
                                                    <img src="img/reset-icon.png" alt="Reset">
                                                </button>
                                            </form>
					<?php  }?>
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