<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
     require_once(TPATH_CLASS . "class.general_admin.php");

     $generalobjAdmin = new General_admin();
}

$generalobjAdmin->check_member_login();

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$iPetTypeId = isset($_REQUEST['iPetTypeId']) ? $_REQUEST['iPetTypeId'] : '';
$Status = isset($_REQUEST['Status']) ? $_REQUEST['Status'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$ksuccess=isset($_REQUEST['ksuccess']) ? $_REQUEST['ksuccess'] : 0;
$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : '';
$script = 'Pettype';


if($iPetTypeId != '' && $Status != ''){
  if(SITE_TYPE !='Demo'){
  $query = "UPDATE pet_type SET eStatus = '".$Status."' WHERE iPetTypeId = '".$iPetTypeId."'";
  $obj->sql_query($query);
  }
  else{
    header("Location:pettype.php?success=2");exit;
  }
}

 $sql="SELECT * FROM `pet_type`";
 $Data=$obj->MySQLSelect($sql);
if ($action == 'delete' && $hdn_del_id != '') {
	$status = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';   
     if(SITE_TYPE !='Demo'){
       $query = "DELETE FROM `pet_type` WHERE iPetTypeId = '" . $hdn_del_id . "'";
       $obj->sql_query($query);
       $action = "view";
       $success = "1";
       $ksuccess="3";
     }
     else{
       header("Location:pettype.php?success=2");exit;
     }
    
}

$tbl_name = "pet_type";

if ($action == 'view') {
    
    $sql="SELECT * FROM `pet_type`";
     $data_drv = $obj->MySQLSelect($sql);
     
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

     <!-- BEGIN HEAD-->
     <head>
          <meta charset="UTF-8" />
          <title>Admin | <?php  echo $langage_lbl_admin['LBL_PET_TYPE'];?> </title>
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
                                        <h2><?php  echo $langage_lbl_admin['LBL_PET_TYPE_TXT'];?> </h2>                               
                                        <a class="add-btn" href="pettype_action.php" style="text-align: center;">ADD PET TYPE</a>
                                        <input type="button" id="cancel-add-form" value="CANCEL" class="cancel-btn">
                                   </div>
                              </div>
                              <hr />
                         </div>
                         <?php  if($success == 1) { ?>
                         <div class="alert alert-success alert-dismissable">
                              <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                              
                                <?php  if($ksuccess == "1")
                                    {?>
                                        Record Insert Successfully.
                                    <?php  }
                                     else if ($ksuccess=="2")
                                     {?>
                                        Record Updated Successfully.
                                     <?php  }
                                      else if($ksuccess=="3") 
                                    {?>
                                        Record Deleted Successfully.
                                    <?php  } ?>
                                    <?php echo $msg;?>
                              
                         </div><br/>
                         <?php  }elseif ($success == 2 & $msg == '') { ?>
                           <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                           </div><br/>
                         <?php  } elseif ($success == 2 & $msg != '') { ?>
                           <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <?php echo $msg;?>
                           </div><br/>
                         <?php  } ?>                         
                         <div class="table-list">
                              <div class="row">
                                   <div class="col-lg-12">
                                        <div class="panel panel-default">
                                             <div class="panel-heading">
                                                  <?php  echo $langage_lbl_admin['LBL_PET_TYPE'];?> 
                                             </div>
                                             <div class="panel-body">
                                                  <div class="table-responsive">
                                                       <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                                            <thead>
                                                                 <tr>
                                                                      <th>TITLE</th>                                                           
																	                                   <th>STATUS</th>                                                                  
                                                                      <th>ACTION</th>
                                                                      <th>DELETE</th>                                                                     
                                                                 </tr>
                                                            </thead>
                                                            <tbody>
                                                                 <?php  for ($i = 0; $i < count($data_drv); $i++) { ?>
                                                                 <tr class="gradeA">
                                                                      <td><?= $data_drv[$i]['vTitle_'.$default_lang]; ?></td>                     
                                  																	  <td>
                                                                     <a href="pettype.php?iPetTypeId=<?= $data_drv[$i]['iPetTypeId']; ?>&Status=<?= ($data_drv[$i]['eStatus'] == "Active") ? 'Inactive' : 'Active' ?>">
                                    																			<button class="btn">
                                    																				<i class="<?= ($data_drv[$i]['eStatus'] == "Active") ? 'icon-eye-open' : 'icon-eye-close' ?>"></i> <?= ucfirst($data_drv[$i]['eStatus']); ?>
                                    																			</button>
                                  																		  </a>
                                                                      </td>                                                                     
                                                                      <td>
                                                                           <a href="pettype_action.php?id=<?= $data_drv[$i]['iPetTypeId']; ?>" style="float: left;">
                                                                                <button class="btn btn-primary">
                                                                                     <i class="icon-pencil icon-white"></i> Edit
                                                                                </button>
                                                                           </a>
                                                                       </td>
                                                                        <td>     
                                                                           <form name="delete_form" id="delete_form" method="post" action="" onSubmit="return confirm('Are you sure you want to delete record?')" class="margin0">
                                                                                <input type="hidden" name="hdn_del_id" id="hdn_del_id" value="<?= $data_drv[$i]['iPetTypeId']; ?>">
                                                                                <input type="hidden" name="action" id="action" value="delete">
                                                                                <button class="btn btn-danger">
                                                                                     <i class="icon-remove icon-white"></i> Delete
                                                                                </button>
                                                                           </form>
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