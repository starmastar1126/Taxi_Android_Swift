<?php 
include_once('../common.php');
$script = "Coupon";
if (!isset($generalobjAdmin)) {
     require_once(TPATH_CLASS . "class.general_admin.php");

     $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$iCouponId = isset($_REQUEST['iCouponId']) ? $_REQUEST['iCouponId'] : '';
$vCouponCode = isset($_REQUEST['vCouponCode']) ? $_REQUEST['vCouponCode'] : '';
$fDiscount = isset($_REQUEST['fDiscount']) ? $_REQUEST['fDiscount'] : '';
$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : '';
$eValidityType = isset($_REQUEST['eValidityType']) ? $_REQUEST['eValidityType'] : '';
$dActiveDate = isset($_REQUEST['dActiveDate']) ? $_REQUEST['dActiveDate'] : '';
$dExpiryDate = isset($_REQUEST['dExpiryDate']) ? $_REQUEST['dExpiryDate'] : '';
$iUsageLimit = isset($_REQUEST['iUsageLimit']) ? $_REQUEST['iUsageLimit'] : '';
$iUsed = isset($_REQUEST['iUsed']) ? $_REQUEST['iUsed'] : '';
$eStatus = isset($_REQUEST['eStatus']) ? $_REQUEST['eStatus'] : '';
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : '';
$msg = isset($_REQUEST['msg']) ? $_REQUEST['msg'] : '';

if($iCouponId != '' && $eStatus != ''){
  if(SITE_TYPE !='Demo'){
    $query = "UPDATE coupon SET eStatus = '".$eStatus."' WHERE iCouponId = '".$iCouponId."'";
    $obj->sql_query($query);
	$msg="Promo code ".$eStatus." successfully.";
	header("Location:coupon.php?success=2&msg=".$msg); exit;
  }
  else{
     header("Location:coupon.php?success=2"); exit;
  }
}


$sql = "select *,DATE_FORMAT(dExpiryDate,'%d/%m/%Y') AS dExpiryDate,DATE_FORMAT(dActiveDate,'%d/%m/%Y') AS dActiveDate from coupon WHERE eStatus != 'Deleted'";
$db_coupon = $obj->MySQLSelect($sql);

$sql = "select *,DATE_FORMAT(dExpiryDate,'%d/%m/%Y') AS dExpiryDate,DATE_FORMAT(dActiveDate,'%d/%m/%Y') AS dActiveDate from coupon WHERE eStatus != 'Deleted'";

$db_coupon = $obj->MySQLSelect($sql);

//print_r($db_coupon); exit;

$sql = "select * from language_master where eStatus = 'Active'";
$db_lang = $obj->MySQLSelect($sql);
if($_POST['action'] == 'delete' )
{
	if(SITE_TYPE =='Demo'){
	  header("Location:coupon.php?success=2");exit;
	}
	$query = "UPDATE coupon SET eStatus = 'Deleted' WHERE iCouponId = '".$iCouponId."'";
	$obj->sql_query($query);
	$action = "view";
	$msg="Promo code deleted successfully.";
	header("Location:coupon.php?success=2&msg=".$msg);
}
if($action == 'view')
{
	$sql = "SELECT *,DATE_FORMAT(dExpiryDate,'%d/%m/%Y') AS dExpiryDate,DATE_FORMAT(dActiveDate,'%d/%m/%Y') AS dActiveDate FROM coupon WHERE eStatus != 'Deleted' ";
	$data_drv  	= $obj->MySQLSelect($sql);

}



    
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

     <!-- BEGIN HEAD-->
     <head>
          <meta charset="UTF-8" />
          <title>Admin | Coupon</title>
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
                                        <h2>Promo Code</h2>
                                        <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                                        <a class="add-btn" href="coupon_action.php" style="text-align: center;">ADD Promo Code</a>
                                        <input type="button" id="cancel-add-form" value="CANCEL" class="cancel-btn">
                                   </div>
                              </div>
                              <hr />
                         </div>
                         <?php  if($_GET['success'] == 1) { ?>
                         <div class="alert alert-success alert-dismissable msgs_hide">
                              <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                              Promo code updated successfully.
                         </div><br/>
                         <?php  }elseif ($_GET['success'] == 2 & $msg == '') { ?>
                           <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                           </div><br/>
                         <?php  } elseif ($_GET['success'] == 2 & $msg != '') { ?>
                           <div class="alert alert-success alert-dismissable msgs_hide">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <?php echo $msg;?>
                           </div><br/>
                         <?php  } ?>
                         <div id="add-hide-div">
                              <form name = "myForm" method="post" action="">
                                   <div class="page-form">
                                   <input type="hidden" name="iCouponId" id="iCouponId" value="{$iCouponId}" />
                                    <input type="hidden" name="action" id="action" value="{$mode}" />
                                        <h2>ADD PromoCode</h2>
                                        <br><br>
                                        <ul>
                                             <li>
                                                  Gift/Certificate Code * :<br>
                                                  <input type="text" id="vCouponCode" name="vCouponCode" class="inputbox"   title="Gift/Certificate Code" required placeholder="Gift/Certificate Code"/>
                                                  
                                             </li>
                                             <li>
                                                  Discount * :<br>
                                                  <input type="text" id="fDiscount" name="fDiscount" class="inputbox"  title="Discount" required placeholder="Discount"/>
                                                  <select id="eType" name="eType">
                                                    <option value="%" <?php  if($db_coupon[0]['eType'] == "%"){ ?> selected <?php  }?> >%</option>
                                                    <option value="cash" <?php  if($db_coupon[0]['eType'] == "cash"){ ?>selected <?php  }?> >$</option>
                                                  </select>
                                             </li>
                                             <li>
                                                  Validity :<br>
                                                  <input type="radio" name="eValidityType" onclick="showhidedate(this.value)" value="Permanent" <?php  if($db_coupon[0]['eValidityType'] == 'Permanent'){ ?> checked <?php  }else { ?> checked <?php  } ?> >
                                                    Permanent
                                                    <input type="radio" name="eValidityType" onClick="showhidedate(this.value)" value="Defined" <?php  if($db_coupon[0]['eValidityType'] == 'Defined'){?> checked <?php  } ?> >Defined 
                                                    
                                                  
                                             </li>
                                             <li>
                                                  Activation Date :<br>
                                                  <input type="text" Readonly id="dActiveDate" name="dActiveDate" style="width:100px;" class="inputbox" value="<?php  echo $db_coupon[0]['dActiveDate']; ?>"  title="Activation Date"/>
                                             </li>
                                             <li>
                                                  Expiry Date:<br>
                                                  <input type="text" Readonly id="dExpiryDate" name="dExpiryDate" style="width:100px;" class="inputbox" value="<?php  echo $db_coupon[0]['dExpiryDate']; ?>"  title="Expiry Date"/>
                                             </li>
                                             <li>
                                                  Usage Limit :<br>
                                                  <input type="text" id="iUsageLimit" name="iUsageLimit" class="inputbox" value="<?php  echo $db_coupon[0]['iUsageLimit']; ?>" title="Usage Limit"/>
                                             </li>
                                             <li>
                                                  Status :<br>
                                                  <select id="eStatus" name="eStatus">
                                                    <option value="Active" <?php  if($db_coupon[0]['eStatus'] == "Active"){ ?>selected <?php  } ?> >Active</option>
                                                    <option value="Inactive" <?php  if($db_coupon[0]['eStatus'] == "Inactive"){?>selected <?php  } ?> >Inactive</option>
                                                  </select>
                                             </li>

                                             <li>
                                                  <input type="submit" name="submit" class="submit-btn" value="SUBMIT" >
                                                  <input type="reset" name="reset" class="submit-btn" value="RESET" >
                                             </li>
                                        </ul>
                                   </div>
                              </form>
                         </div>
                         <div class="table-list">
                              <div class="row">
                                   <div class="col-lg-12">
                                        <div class="panel panel-default">
                                             <div class="panel-heading">
                                                  Promo Code
                                             </div>
                                             <div class="panel-body">
                                                  <div class="table-responsive">
                                                       <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                                            <thead>
                                                                 <tr>
                                                                      <th>Gift/Certificate Code</th>
                                                                      <th>Discount</th>
                                                                      
                                                                      <th>Validity</th>
                                                                      <th>Activation Date</th>
                                                                      <!--<th>SERVICE LOCATION</th>-->
                                                                      <th>Expiry Date</th>
                                                                      <!--<th>LANGUAGE</th>-->
																	   <th>Usage Limit</th>
                                                                       <th>Used</th>
																	   <th>Status</th>
                                                                       <th align="center" style="text-align:center;">Action</th>
                                                                 </tr>
                                                            </thead>
                                                            <tbody>
                                                                 <?php  for ($i = 0; $i < count($db_coupon); $i++) { ?>
                                                                 <tr class="gradeA">
                                                                      <td><?= $db_coupon[$i]['vCouponCode']; ?></td>
                                                                      <?php  if($db_coupon[$i]['eType'] == "percentage"){$e_value = "%";}else{$e_value = "$";} ?>
                                                                      <td><?= $db_coupon[$i]['fDiscount']." ".$e_value; ?></td>
                                                                       
                                                                      <td><?php if($db_coupon[$i]['eValidityType'] == "Defined") { echo "Custom"; } else { echo $db_coupon[$i]['eValidityType']; }?></td>
                                                                      <?php  if($db_coupon[$i]['dActiveDate'] == "00/00/0000"){ ?>
                                                                      <td style="text-align:center;">---</td> 
                                                                      <?php  }else{ ?> 
                                                                      <td><?= $db_coupon[$i]['dActiveDate']; ?></td>
                                                                      <?php }?>
                                                                      <?php  if($db_coupon[$i]['dExpiryDate'] == "00/00/0000"){ ?>
                                                                      <td style="text-align:center;">---</td> 
                                                                      <?php  }else{ ?> 
                                                                      <td><?= $db_coupon[$i]['dExpiryDate']; ?></td>
                                                                      <?php  } ?>
                                                                      <td><?= $db_coupon[$i]['iUsageLimit']; ?></td>
                                                                      <td><?= $db_coupon[$i]['iUsed']; ?></td>
                                                                      
																	  <td width="10%" align="center">
																		<?php  if($db_coupon[$i]['eStatus'] == 'Active') {
																		   $dis_img = "img/active-icon.png";
																			}else if($db_coupon[$i]['eStatus'] == 'Inactive'){
																			 $dis_img = "img/inactive-icon.png";
																				}else if($db_coupon[$i]['eStatus'] == 'Deleted'){
																				$dis_img = "img/delete-icon.png";
																				}?>
																		<img src="<?=$dis_img;?>" alt="<?=$db_coupon[$i]['eStatus']?>"> 
                                                                      </td>                                                                      
                                                                      <td class="veh_act" align="center" style="text-align:center;">
                                                                           <a href="coupon_action.php?iCouponId=<?= $db_coupon[$i]['iCouponId']; ?>" data-toggle="tooltip" title="Edit Coupon">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                           </a>
                                                                      
																	<a href="coupon.php?iCouponId=<?= $db_coupon[$i]['iCouponId']; ?>&eStatus=Active" data-toggle="tooltip" title="Active Coupon">
																		<img src="img/active-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >
																	</a>
																
																	<a href="coupon.php?iCouponId=<?= $db_coupon[$i]['iCouponId']; ?>&eStatus=Inactive" data-toggle="tooltip" title="Inactive Coupon">
																		<img src="img/inactive-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >
																	</a>
                                                                           <form name="delete_form" id="delete_form" method="post" action="" onSubmit="return confirm_delete()" class="margin0">
                                                                                <input type="hidden" name="iCouponId" id="iCouponId" value="<?= $db_coupon[$i]['iCouponId']; ?>">
                                                                                <input type="hidden" name="action" id="action" value="delete">
                                                                                <button class="remove_btn001" data-toggle="tooltip" title="Delete Coupon">
                                                                                     <img src="img/delete-icon.png" alt="Delete">
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
                        <div class="clear"></div>
                    </div>
               </div>
               <!--END PAGE CONTENT -->
          </div>
          <!--END MAIN WRAPPER -->


        <?php  include_once('footer.php');?>
    <script src="../assets/plugins/dataTables/jquery.dataTables.js"></script>
    <script src="../assets/plugins/dataTables/dataTables.bootstrap.js"></script>
    <link rel="stylesheet" media="all" type="text/css" href="../assets/dtp/jquery-ui.css" />
<link rel="stylesheet" media="all" type="text/css" href="../assets/dtp/jquery-ui-timepicker-addon.css" />
<script type="text/javascript" src="../assets/dtp/jquery-ui.min.js"></script>
<script type="text/javascript" src="../assets/dtp/jquery-ui-timepicker-addon.js"></script>
	<script>
   var successMSG1 = '<?php  echo $success;?>';



      if(successMSG1 != ''){                       
           setTimeout(function() {
              $(".msgs_hide").hide(1000)
          }, 5000);
      }
            
		$(document).ready(function () {
			$('#dataTables-example').dataTable({
        "order": [[ 3, "desc" ]]
      });
		});
		function confirm_delete()
          {
               var confirm_ans = confirm("Are You sure You want to Delete Coupon?");
               return confirm_ans;
               //document.getElementById(id).submit();
          }


	</script>
</body>
<!-- END BODY-->
</html>
