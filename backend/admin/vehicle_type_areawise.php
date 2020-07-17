<?php 

include_once('../common.php');


if (!isset($generalobjAdmin)) {
     require_once(TPATH_CLASS . "class.general_admin.php");
     $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'delete';
$success	= isset($_REQUEST['success'])?$_REQUEST['success']:'';

$script = 'VehicleType';  

 $Vehicle_type_name = ($APP_TYPE == 'Delivery')? 'Deliver':$APP_TYPE ; 
 if($Vehicle_type_name == "Ride-Delivery"){

      $vehicle_type_sql = "SELECT * from  vehicle_type where(eType ='Ride' or eType ='Deliver')";
      $data_drv = $obj->MySQLSelect($vehicle_type_sql);
     // print_r($data_drv);


 }else{

        if($APP_TYPE == 'UberX'){

            $vehicle_type_sql = "SELECT vt.*,vc.iVehicleCategoryId,vc.vCategory_".$default_lang." from  vehicle_type as vt  left join vehicle_category as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId where vt.eType='".$Vehicle_type_name."' ";
            $data_drv = $obj->MySQLSelect($vehicle_type_sql);

        }else{
          $vehicle_type_sql = "SELECT * from  vehicle_type where eType='".$Vehicle_type_name."'";
         $data_drv = $obj->MySQLSelect($vehicle_type_sql);


        }

      
 }    

 
  $vahicale_hdn_del_id    = isset($_POST['vahicale_hdn_del_id'])?$_POST['vahicale_hdn_del_id']:'';
 
	if ($action == 'delete' && $vahicale_hdn_del_id != '') {

      if($vahicale_hdn_del_id != ''){   
	  
		if(SITE_TYPE !='Demo'){
      
			$query = "DELETE FROM vehicle_type WHERE iVehicleTypeId ='".$vahicale_hdn_del_id."'";
           $obj->sql_query($query);
		   $var_msg="Vehicle Type Deleted Successfully.";
            header("Location:vehicle_type.php?success=1&var_msg=".$var_msg);exit; 
		}else{			
			//echo "sdfsdfsd"; exit;
			header("Location:vehicle_type.php?success=2");exit;
 
		}
     
      }
	  
	} 


?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

     <!-- BEGIN HEAD-->
     <head>
          <meta charset="UTF-8" />
          <title>Admin | <?php  echo $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> </title>
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
			   
                 function confirm_delete(id)
                {                                
                     var request = $.ajax({
                    type: "POST",
                    url: 'ajax_delete_vehicle_type.php',
                    //data: 'id =' + id,
                    data: {id:id},
                         success: function (data)

                         {
                              //alert(data);
                              if(data == true){

                                  //alert("Selected vehicle type is not delete because some of driver has used selected vehicle type."); 
                                  alert("This vehicle type can not be deleted because its in use by some vehicles. Please remove this vehicle type from those vehicles and delete after that.");
                                  return false;
                              }else{   

                                     document.getElementById("vahicale_hdn_del_id_"+id).value = +id;
                                     
                                     var strconfirm = confirm("Are you sure you want to delete?");
                                    if (strconfirm == true) {

                                        document.getElementById('delete_frm_'+id).submit();

                                    }else{

                                        return false;
                                    }                                   
                              }
                            
                         }
                     });                    
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
                         
                         <div id="add-hide-show-div">
                              <div class="row">
                                   <div class="col-lg-12">
                                        <h2><?php  echo "Vehicle Type Areawise";?> </h2>
                                        <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                                        <?php  //if($APP_TYPE != 'UberX'){?>
                                          <a class="add-btn" href="vehicle_type_action.php" style="text-align: center;">ADD <?php  echo $langage_lbl_admin['LBL_VEHICLE_TYPE_TXT'];?></a> <?php  //} ?>
                                       
                                        <input type="button" id="cancel-add-form" value="CANCEL" class="cancel-btn">
                                   </div>
                              </div>
                              <hr />
                         </div>
						<?php  if ($success == 2) { ?>
						<div class="alert alert-danger alert-dismissable msgs_hide">
								 <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
								 "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
						</div><br/>
					<?php  }else if($success == 1){ ?>
						<div class="alert alert-success alert-dismissable msgs_hide">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
									<?=isset($_REQUEST['var_msg']) ? $_REQUEST['var_msg'] : ''?>
								</div><br/>
					<?php 	} ?>
                        
                         <div class="table-list">
                              <div class="row">
                                   <div class="col-lg-12">
                                        <div class="panel panel-default">
                                             <div class="panel-heading">
                                                 <?php  echo $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> 
                                             </div>
                                             <div class="panel-body">
                                                  <div class="table-responsive">
                                                       <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                                            <thead>
                                                                 <tr>
                                                                         <th>Type</th>
                                                                         <th>Country</th>
                                                                         <th>State</th>
                                                                         <th>City</th>
                                                                         <th>Address</th>

                                                                   <?php   if($APP_TYPE == 'UberX'){ ?>
                                                                      <th>Category</th>                                                              
                                                                      <?php  } ?>
                                                                       
                                                                      <?php  if($APP_TYPE != 'UberX'){ ?> 
                                                                      <th>Price Per Km</th>
                                                                      
                                                                      <th>Price Per Min</th>
                                                                      <!--<th>SERVICE LOCATION</th>-->
                                                                      <th>Base Fare</th>
                                                                      <th>Commission (%)</th>
														               <th>Person capacity</th>
                                                                      <?php  } ?> 
                                                                      <?php    if($Vehicle_type_name == "Ride-Delivery"){ ?>
                                                                        <th>Vehicle Category Type</th>  <?php   }?>    
                                                                      <th align="center" style="text-align:center;">Action</th>
                                                                       <!--<?php /* if($APP_TYPE != 'UberX'){?>
                                                                        <th>Delete</th>
                                                                         <?php  }*/?>-->
                                                                      
                                                                 </tr>
                                                            </thead>
                                                            <tbody>
                                                                 <?php  for ($i = 0; $i < count($data_drv); $i++) { ?>
                                                                 <tr class="gradeA">
                                                                    <td><?= $data_drv[$i]['vVehicleType'] ?></td>
                                                                    <td><?= !empty($data_drv[$i]['iCountryId'])? $data_drv[$i]['iCountryId']:"-" ?></td>
                                                                    <td><?= !empty($data_drv[$i]['iStateId'])? $data_drv[$i]['iStateId']:"-" ?></td>
                                                                    <td><?= !empty($data_drv[$i]['iCityId'])? $data_drv[$i]['iCityId']:"-" ?></td>
                                                                    <td><?= !empty($data_drv[$i]['vVehicleAddress'])? $data_drv[$i]['vVehicleAddress']:"-" ?></td>

                                                                 <?php   if($APP_TYPE == 'UberX'){ ?> 
                                                                    <td><?= $data_drv[$i]['vCategory_'.$default_lang] ?></td>
                                                                    <?php  } ?>

																	                                 <?php  if($APP_TYPE != 'UberX'){ ?> 
                                																	 <td><?= $data_drv[$i]['fPricePerKM'] ?></td>

                                																	 
                                																	  <td><?= $data_drv[$i]['fPricePerMin'] ?></td>
                                																	  
                                																	   <td><?= $data_drv[$i]['iBaseFare'] ?></td>
                                																	  <td><?= $data_drv[$i]['fCommision'] ?></td>
                                																	  <td><?= $data_drv[$i]['iPersonSize'] ?></td>
                                                                                        <?php    if($Vehicle_type_name == "Ride-Delivery"){ ?>
                                                                                          <td><?= $data_drv[$i]['eType']; ?></td>
                                                                                           <?php   }?> 
                                                                      <?php  } ?>                          
																	 <td class="veh_act"  align="center" style="text-align:center;">
                                                                    
                                                                           <a href="vehicle_type_action.php?id=<?= $data_drv[$i]['iVehicleTypeId']; ?>" data-toggle="tooltip" title="Edit <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?>">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                           </a>
                                                                      
                                                                        <?php  if($APP_TYPE != 'UberX'){?>
                                                                                         
                                                                      <!--<button class="btn btn-danger" onclick="deletevehicle(<?php //php echo $data_drv[$i]['iVehicleTypeId'] ?>);">
                                                                           <i class="icon-remove icon-white"></i> Delete
                                                                                  </button>  -->  
																				  
                                                                          <form name="delete_form" id="delete_frm_<?php  echo $data_drv[$i]['iVehicleTypeId'];?>" method="post" action="" class="margin0">
                                                                            <input type="hidden" name="vahicale_hdn_del_id" id="vahicale_hdn_del_id_<?php  echo $data_drv[$i]['iVehicleTypeId'];?>" value="">
                                                                                <input type="hidden" name="action" id="action" value="delete">
                                                                                 <!--  <input type="button" class="btn btn-danger" onclick="confirm_delete(<?php  echo $data_drv[$i]['iVehicleTypeId'] ?>);" value="Delete"> -->
																					<button type="button" class="remove_btn001" onClick="confirm_delete(<?php  echo $data_drv[$i]['iVehicleTypeId'] ?>);" value="Delete" data-toggle="tooltip" title="Delete <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?>">
																						<img src="img/delete-icon.png" alt="Delete">
																					</button>
                                                                              </form> 
                                                                         <?php  }?>
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
         
     </body>
     <!-- END BODY-->    
</html>
<script>
	
	var successMsg ='<?php  echo $success?>';
			if(successMsg != ''){
				
				setTimeout(function() {
				        $(".msgs_hide").hide(1000)
				    }, 5000);


			}
	</script>
