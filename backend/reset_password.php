<?php 
	include_once("common.php");	
	$_REQUEST['type']=(base64_decode(base64_decode(trim($_REQUEST['type']))));
	$_REQUEST['id'] = $generalobj->decrypt($_REQUEST['id']);	
	$_REQUEST['time']=(base64_decode(base64_decode(trim($_REQUEST['time']))));
	$success=(isset($_REQUEST['success'])?$_REQUEST['success']:'');
	$type = isset($_REQUEST['type'])?$_REQUEST['type']:'';
	$id = isset($_REQUEST['id'])?$_REQUEST['id']:'';
	$token = isset($_REQUEST['_token'])?$_REQUEST['_token']:'';
	if($type == 'rider'){		
		$sql = "SELECT iUserId FROM register_user WHERE iUserId='".$id."' AND vPassword_token='".$token."'";
		$db_user = $obj->MySQLSelect($sql);				
		if(count($db_user)>0){
			$tablename ='register_user'; 
			$type1 = 'rider';
			$filed_Id ='iUserId';
			$type_id = $db_user[0]['iUserId'];
		}
	}
	if($type == 'company'){	
		$sql = "SELECT iCompanyId FROM company WHERE iCompanyId='".$id."' AND vPassword_token='".$token."'"; 
		$db_company = $obj->MySQLSelect($sql);
		if(count($db_company)>0){
			$tablename ='company';
			$type1 = 'driver';
			$filed_Id = 'iCompanyId';
			$type_id = $db_company[0]['iCompanyId'];
			
		}
	}
	if($type == 'driver'){	
						
		$sql = "SELECT iDriverId FROM register_driver WHERE iDriverId='".$id."' AND vPassword_token='".$token."'"; 
		$db_driver = $obj->MySQLSelect($sql);
		if(count($db_driver)>0){
			$tablename ='register_driver';
			$type1 = 'driver';
			$filed_Id = 'iDriverId';
			$type_id = $db_driver[0]['iDriverId'];			
		}			
	}
	if($tablename !='' && $type1!='' && $type_id !=''){
		$sql = "SELECT * FROM ".$tablename." WHERE ".$filed_Id."='".$type_id."'"; 
		$deatail = $obj->MySQLSelect($sql);
		//print_r($deatail);
		
	}else{	
	
		if($type == 'rider'){
			$type="rider";		
		}else{
			$type="driver";	
		}
		header("Location:login_new.php?action=".$type.""); exit;	
	}	
	if($_POST['submit']){
		
		$newpassword =$_POST['newpassword'];
		$vPassword =$_POST['vPassword'];		
		$_POST['type']=(base64_decode(base64_decode(trim($_POST['type']))));
		$_POST['id'] = $generalobj->decrypt($_POST['id']);
		$token = isset($_REQUEST['_token'])?$_REQUEST['_token']:'';
		$success=(isset($_REQUEST['success'])?$_REQUEST['success']:'');
		$type = isset($_POST['type'])?$_POST['type']:''; 
		$id = isset($_POST['id'])?$_POST['id']:'';
		$time = isset($_POST['time'])?$_POST['time']:'';
	
		if($type == 'rider'){		
			$sql = "SELECT iUserId FROM register_user WHERE iUserId='".$id."'"; 
			$db_user = $obj->MySQLSelect($sql);			
			
			if(count($db_user)>0){
				$tablename ='register_user'; 
				$type_action = 'rider';
				$filed_Id ='iUserId';
				
			}else{
				$type= base64_encode(base64_encode($type));
				$id= base64_encode(base64_encode($id));
				$id = $generalobj->encrypt($id);
				$time= base64_encode(base64_encode($time));
			echo $var_msg = "Record is Not Found.";
			header("Location:reset_password.php?type=".$type."&id=".$id."&_token=".$token."&&success=1&var_msg=".$var_msg); exit;
			}
			
		}
		if($type == 'company'){		
			$type_action = 'company';
			$sql = "SELECT iCompanyId FROM company WHERE iCompanyId='".$id."'"; 
			$db_company = $obj->MySQLSelect($sql);
			if(count($db_company)>0){
			
				$tablename ='company';
				$type_action = 'company';
				$filed_Id = 'iCompanyId';
				
			}else{					
				
				$type= base64_encode(base64_encode($type));
				$id = $generalobj->encrypt($id);
				$time= base64_encode(base64_encode($time));
				$var_msg = "Record is Not Found";
				header("Location:reset_password.php?type=".$type."&id=".$id."&_token=".$token."&&success=1&var_msg=".$var_msg); exit;			
					
			}			
		}
		if($type == 'driver'){		
							
			$sql = "SELECT iDriverId FROM register_driver WHERE iDriverId='".$id."'"; 
			$db_driver = $obj->MySQLSelect($sql);
			if(count($db_driver)>0){
				$tablename ='register_driver';
				$type_action = 'driver';
				$filed_Id = 'iDriverId';
				
			}else{
			$type= base64_encode(base64_encode($type));
			$id = $generalobj->encrypt($id);
			$var_msg = "Record is Not Found";
			header("Location:reset_password.php?type=".$type."&id=".$id."&_token=".$token."&&success=1&var_msg=".$var_msg); exit;			
			}		
						
		}	
		if($tablename !='' && $type_action!='' && $id !=''){			
			if($newpassword == $vPassword){				
				$sql = "UPDATE ".$tablename." set vPassword='".$generalobj->encrypt_bycrypt($vPassword)."',vPassword_token='' WHERE ".$filed_Id."='".$id."'";  
				$obj->sql_query($sql);

				if($type_action == 'driver' || $type_action == 'company'){				
					header("Location:login_new.php?action=driver"); exit;
				}else{
					header("Location:login_new.php?action=rider");exit;				
				}
			}else{
				if($type_action == 'driver' || $type_action == 'company'){	
					$type_action ='driver';
				}else{
					$type_action='rider';				
				}
				$type= base64_encode(base64_encode($type_action));
				$id = $generalobj->encrypt($id);
				$var_msg = "Sorry !  Password Not Matched.";
				header("Location:reset_password.php?type=".$type."&id=".$id."&_token=".$token."&&success=1&var_msg=".$var_msg); exit;
			}
			
		}	
		
	}	
?>
<!DOCTYPE html>
	<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
 <!--   <title><?=$SITE_NAME?> | Login Page</title>-->
   <title><?php  echo $meta_arr['meta_title'];?></title>
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
		<div class="page-contant reset-password">
			<div class="page-contant-inner">
				<h2 class="header-page"><?=$langage_lbl['LBL_RESET_PASSWORD_TXT'];?>
				<?php if(SITE_TYPE =='Demo'){?>
				<p><?=$langage_lbl['LBL_SINCE_IT_IS_DEMO'];?></p>
				<?php }?>
				</h2>
				<div class="login-form">	
				<?php   
				if($type!='' && $id!=''){
					if(count($deatail)>=0 ){				  
							// $current_date = Date('Y-m-d H:i:s');	
							// $total_hours= ((strtotime($current_date) - strtotime($time))/3600);
							// if($total_hours <=24){
								if($success == 1) { ?>
								 <div class="alert alert-danger alert-dismissable">
								  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
								 <?php  echo $_REQUEST['var_msg']; ?>
							 </div><br/>
							 <?php  } ?>
						
								<div class="login-form-left reset-password-page">
									<form name="resetpassword" action="" class="form-signin" method = "post" id="resetpassword" >	
									<input type="hidden" name="type" value="<?php echo base64_encode(base64_encode($type));?>"/>
									<input type="hidden" name="id" value="<?php echo $generalobj->encrypt($id);?>"/>
									<input type="hidden" name="_token" value="<?php  echo $token;?>"/>
										<span class="newrow reset-password-img">
										<b><em>
										<?php  										
											if($type == 'company'){
												$img_path = $tconfig["tsite_upload_images_compnay"];
												$imagename =$deatail[0]['vImage'];
												$name= $deatail[0]['vCompany'];
												}else if($type == 'driver'){
												$img_path = $tconfig["tsite_upload_images_driver"];
												$imagename =$deatail[0]['vImage'];
												$name= $deatail[0]['vName']." ".$deatail[0]['vLastName'];
											}else{
												$img_path = $tconfig["tsite_upload_images_passenger"];
												$imagename =$deatail[0]['vImgName'];
												$name= $deatail[0]['vName']." ".$deatail[0]['vLastName'];
											}
											if($imagename !='' && $imagename !='NONE' ){?>
											
											<img src = "<?=$img_path. '/' .$id . '/2_' .$imagename; ?>" style="height:150px;"/>
											<?php  }else{ ?>
											<img src="assets/img/profile-user-img.png" alt="" style="height:150px;">
												
											<?php  }
											if($type == 'company' || $type == 'driver'){
												$type='driver';
											}else{
												$type="rider";
											}
											$link ="login_new.php?action=".$type;?>		
                                            </em>	 </b>	
											<b>					
											  <?php 												 
											 if($name !=''){ 
												 echo $name.'<br>';
											 }?>	 
											
												 
											 <a href ="<?php  echo $link;?>"> <?=$langage_lbl['LBL_RESET_PAGE_BACK_LINK_TXT']; ?> </a>
                                        </b>
										</span>
										<span class="newrow">
										<b>						
										<label><?=$langage_lbl['LBL_NEW_PASSWORD_TXT']; ?></label>
										<input name="newpassword" type="password"  id="newpassword" placeholder="<?=$langage_lbl['LBL_NEW_PASSWORD_TXT']; ?>" class="login-input" value="" required /></b>
										</span>
										<span class="newrow">
										<b>
										<label><?=$langage_lbl['LBL_CONFORM_PASSWORD_TXT']; ?></label>
											<input name="vPassword" id="vPassword" type="password" placeholder="<?=$langage_lbl['LBL_CONFORM_PASSWORD_TXT']; ?>" class="login-input" value="" required />
										</b> 
                                        </span>
										<b>
										<input type="submit" class="submit-but" name="submit" value="<?=$langage_lbl['LBL_SUBMIT_BUTTON_TXT']; ?>" />								
										</b> 
									</form>								
								</div>						
						
						<?php  /* }else{?>
						<div class="alert alert-danger alert-dismissable">
							  <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							<?=$langage_lbl['LBL_SORRY_TIME_EXPIRED_TXT']; ?>
						 </div><br/>	
								
					<?php  } */
					}
				}
				 else{
					if($type == 'driver' || $type == 'company'){	
						$type ='driver';
					}else{
					$type='rider';				
					}
				header("Location:login_new.php?action=".$type.""); exit;
				} 
				?>
				</div>				
				<div style="clear:both;"></div>
			</div>
		</div>
	<!-- footer part -->
    <?php  include_once('footer/footer_home.php');?>
    <!-- footer part end -->
    		<!-- -->
            <div style="clear:both;"></div>
	</div>
	<!-- home page end-->
    <!-- Footer Script -->
    <?php  include_once('top/footer_script.php');?>
    <!-- End: Footer Script -->
	<script type="text/javascript" src="assets/js/validation/jquery.validate.min.js" ></script>
	<script type="text/javascript" src="assets/js/validation/additional-methods.js" ></script>
    <script>
	$('#resetpassword').validate({
		ignore: 'input[type=hidden]',
		errorClass: 'help-block',
		errorElement: 'span',
		errorPlacement: function (error, e) {
			e.parents('.newrow > b').append(error);
		},
		highlight: function (e) {
			$(e).closest('.newrow').removeClass('has-success has-error').addClass('has-error');
			$(e).closest('.newrow b input').addClass('has-shadow-error');
			$(e).closest('.help-block').remove();
		},
		success: function (e) {
			e.prev('input').removeClass('has-shadow-error');
			e.closest('.newrow').removeClass('has-success has-error');
			e.closest('.help-block').remove();
			e.closest('.help-inline').remove();
		},
		rules: {
			vEmail: {required: true, email: true},
			vPassword: {required: true, equalTo: "#newpassword"},
			newpassword: {required: true, minlength: 6},
		},
		messages: {
			
		}
	});
		
	</script>
	
</body>
</html>