<?php 
include_once('../common.php');

if($REFERRAL_SCHEME_ENABLE == "No"){
	
header('Location: dashboard.php'); exit;

}

if (!isset($generalobjAdmin)) {
     require_once(TPATH_CLASS . "class.general_admin.php");
     $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$script = 'view-referrer';  	
	
$id =$_REQUEST['id'];
$etype ="";	
$type=(isset($_REQUEST['eUserType'])?$_REQUEST['eUserType']:'');

//echo $_GET['driver'];
if($type == 'Driver'){

	$tablename ='register_driver';
	$iUserId = "iDriverId";
}else{	
	$tablename = 'register_user';
	$iUserId = 'iUserId';
}	

$query = "SELECT concat(vName, ' ' ,vLastName) as MemberName FROM ".$tablename." WHERE ".$iUserId." = '".$id."' ";			
$result = $obj->MySQLSelect($query);	
$MemberName = $generalobjAdmin->clearName($result[0]['MemberName']);

/*
$query_driver = "SELECT uw.*,rd.iDriverId,rd.vName,rd.vLastName,rd.dRefDate FROM register_driver as rd LEFT JOIN user_wallet as uw ON uw.dDate=rd.dRefDate WHERE uw.eUserType='".$type."' AND uw.iUserId='".$id."' AND uw.eFor = 'Referrer' AND rd.eRefType = '".$type."'";
$result_driver = $obj->MySQLSelect($query_driver);	

$query_reider = "SELECT uw.*,urd.vName,urd.vLastName,urd.dRefDate FROM register_user as urd LEFT JOIN user_wallet as uw ON uw.dDate=urd.dRefDate WHERE uw.eUserType='".$type."' AND uw.iUserId='".$id."' AND uw.eFor = 'Referrer' AND urd.eRefType = '".$type."'";
$result_rider = $obj->MySQLSelect($query_reider);
*/
	
$query_driver = "SELECT uw.*,rd.iDriverId,rd.vName,rd.vLastName,rd.dRefDate FROM register_driver as rd LEFT JOIN user_wallet as uw ON rd.iRefUserId=uw.iUserId WHERE uw.eUserType='".$type."' AND uw.iUserId='".$id."' AND uw.eFor = 'Referrer' AND rd.eRefType = '".$type."' GROUP BY rd.iDriverId";
$result_driver = $obj->MySQLSelect($query_driver);	

$query_reider = "SELECT uw.*,urd.iUserId,urd.vName,urd.vLastName,urd.dRefDate FROM register_user as urd LEFT JOIN user_wallet as uw ON urd.iRefUserId=uw.iUserId WHERE uw.eUserType='".$type."' AND uw.iUserId='".$id."' AND uw.eFor = 'Referrer' AND urd.eRefType = '".$type."' GROUP BY urd.iUserId";
$result_rider = $obj->MySQLSelect($query_reider);
	
	
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

     <!-- BEGIN HEAD-->
     <head>
          <meta charset="UTF-8" />
          <title>Admin | Referrer</title>
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
					
					<div class="row">
						<div class="col-lg-12">
							<h2><?=$MemberName;?>  Referral Details</h2>
							<a href="javascript:void(0);" class="back_link">
								<input type="button" value="Back to Listing" class="add-btn">
							</a>
						</div>
					</div>
					<hr />                       
							
						
                         <div class="table-list">
                              <div class="row">
                                   <div class="col-lg-12">
                                        <div class="panel panel-default">
                                             <div class="panel-heading">
                                             	<strong>
                                             	<?php  echo $MemberName;?>'s Referral <?php  echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'];?>                                      
                                             </strong>
                                             </div>
                                             <div class="panel-body">
                                                  <div class="table-responsive">
                                                       <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                                            <thead>
                                                                <tr>
																	<th width="35%">Referred Member Name</th>
																	<th width="35%">Date Referred</th>
																	<!--<th width="30%">Referred Amount</th>-->
                                                                </tr>
                                                            </thead>
                                                            <tbody>										 
																	
															<?php  
																 $count = count($result_driver);
																 if($count > 0){
																 for($i=0;$i<count($result_driver);$i++){ ?>										 
																	 
																	 <tr class="gradeA">
																		<td ><?=$result_driver[$i]['vName'];?> <?= $result_driver[$i]['vLastName'];?></td> 
																		<?php  
																			$time = strtotime($result_driver[$i]['dRefDate']);
																			$myFormatForView = date("jS F Y", $time);																	 
																		?>
																		<td><?= $myFormatForView ?></td> 
																		<!--<td>$ <?=$result_driver[$i]['iBalance']?></td> -->
																	 </tr>																 
																	 
																<?php   }
																	
																 }else{ ?>																 
																  <tr class="gradeA">
																  <td colspan ="3" align="center"> No Details Found </td> 
																  </tr>
																 
																<?php   } 	?>                                                               

                                                            </tbody>
                                                       </table>
													   
                                                  </div>

                                             </div>
                                        </div>
                                   </div> <!--TABLE-END-->
                              </div>
                         </div>
						 
							
                         <div class="table-list">
                              <div class="row">
                                   <div class="col-lg-12">
                                        <div class="panel panel-default">
                                             <div class="panel-heading">
                                             <strong>
                                             	<?php  echo $MemberName;?>'s Referral <?php  echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN'];?>   												
                                             </strong>
                                             </div>
											 </hr>
											 
                                             <div class="panel-body">
                                                  <div class="table-responsive">
                                                       <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                                            <thead>
                                                                <tr>
																	<th width="35%">Referred Member Name</th>
																	<th width="35%">Date Referred</th>
																	<!--<th width="30%">Referred Amount</th>-->									
                                                                </tr>
                                                            </thead>
                                                            <tbody>                                                                 
																 <?php  
																  $count = count($result_rider);
																 if($count > 0){
																 for($i=0;$i<count($result_rider);$i++){ ?>										 
																	 
																	 <tr class="gradeA">
																		<td><?= $result_rider[$i]['vName'];?> <?= $result_rider[$i]['vLastName'];?></td> 
																		<?php  
																			$time = strtotime($result_rider[$i]['dRefDate']);
																			$myFormatForView = date("jS F Y", $time);																	 
																		?>
																		<td><?= $myFormatForView ?></td> 
																		<!--<td>$ <?=$result_rider[$i]['iBalance']?></td> -->
																	 </tr>																 
																	 
																<?php   }
																	
																 }else{ ?>																 
																  <tr class="gradeA">
																  <td colspan ="3" align="center"> No Details Found </td> 
																  </tr>
																 
																<?php   } 	?> 
																           
                                                                

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


          <?php  include_once('footer.php');?>
          <script>
			function confirm_delete(action,id)
			{
					 //alert(action);alert(id);
				 var confirm_ans = confirm("Are You sure You want to Delete this Rider?");
					   //alert(confirm_ans);
				 if(confirm_ans=='false')
					 {
						return false;
						}
					 else
					 {
						 $('#action').val(action);
						 $('#iRatingId').val(id);
						 document.frmreview.submit();
					}
													 
			 }
			 function getReview(type)
			{
				
				$('#reviewtype').val(type);
				document.frmreview.submit();
					
			}
			
			$(document).ready(function() {
				var referrer;
				referrer =  document.referrer;
				if(referrer == "") {
					referrer = "referrer.php";
				}
				$(".back_link").attr('href',referrer);
			});
			
			
		</script>
     </body>
     <!-- END BODY-->
</html>
