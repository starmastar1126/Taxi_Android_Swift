<?php 
include_once 'common.php';
$generalobj->go_to_home();
// echo "<pre>";print_r($_GET);
$action = isset($_GET['action'])?$_GET['action']:'';
$iscompany = isset($_GET['iscompany'])?$_GET['iscompany']:'0';
$type = "Driver";

if($iscompany == "1"){
	$_SESSION['postDetail']['user_type'] = "company";
	$type = "Company";
}
$forpsw =  isset($_REQUEST['forpsw'])?$_REQUEST['forpsw']:'';
$forgetPWd =  isset($_REQUEST['forgetPWd'])?$_REQUEST['forgetPWd']:'';
$depart = '';
if(isset($_REQUEST['depart'])) {
	$_SESSION['sess_depart'] = $_REQUEST['depart'];
	$depart = $_SESSION['sess_depart'];
}else {
	if(isset($_REQUEST['depart'])) { unset($_SESSION['sess_depart']); }
}

$err_msg = "";
if(isset($_SESSION['sess_error_social'])){
	$err_msg = $_SESSION['sess_error_social'];
	// echo "<pre>";print_r($_SESSION);
	
	unset($_SESSION['sess_error_social']);
	unset($_SESSION['fb_user']);			//facebook
	unset($_SESSION['oauth_token']);		//twitter
	unset($_SESSION['oauth_token_secret']); //twitter
	unset($_SESSION['access_token']);		//google
	
	// echo "<pre>";print_r($_SESSION);exit;
}

if($action == 'driver' && $iscompany != "1"){
	$meta_arr = $generalobj->getsettingSeo(9);		
}elseif($action == 'rider'){	
	$meta_arr = $generalobj->getsettingSeo(8);		
} elseif($action == 'driver' &&  $iscompany == "1"){ 
 $meta_arr = $generalobj->getsettingSeo(10);  
}
if($host_system == "carwash"){
	$rider_email="user@demo.com";
	$driver_email="washer@demo.com";	
}elseif($host_system == "beautician"){
	$rider_email="user@demo.com";
	$driver_email="beautician@demo.com";	
}elseif($host_system == "massage4"){
	if($iscompany == "1"){
		$driver_email="company@gmail.com";
	}else{
		$driver_email="massager@demo.com";	
	}
	$rider_email="user@demo.com";	
}elseif($host_system == "doctor4"){
	if($iscompany == "1"){
		$driver_email="company@gmail.com";
	}else{
		$driver_email="doctor@demo.com";
	}
	$rider_email="patient@demo.com";	
}elseif($host_system == "beautician4"){
	if($iscompany == "1"){
		$driver_email="company@gmail.com";
	}else{
		$driver_email="beautician@demo.com";	
	}
	$rider_email="user@demo.com";
}elseif($host_system == "carwash4"){
	if($iscompany == "1"){
		$driver_email="company@gmail.com";
	}else{
		$driver_email="carwasher@demo.com";	
	}
	$rider_email="user@demo.com";
}elseif($host_system == "dogwalking4"){
	if($iscompany == "1"){
		$driver_email="company@gmail.com";
	}else{
		$driver_email="dogwalker@demo.com";	
	}
	$rider_email="user@demo.com";
}elseif($host_system == "towtruck4"){
	if($iscompany == "1"){
		$driver_email="company@gmail.com";
	}else{
		$driver_email="provider@demo.com";
	}
	$rider_email="user@demo.com";
}elseif($host_system == "tutors"){
	$rider_email="student@demo.com";
	$driver_email="tutor@demo.com";	
}elseif($host_system == "ufxforall"){
	$rider_email="provider@demo.com";
	$driver_email="user@demo.com";	
}elseif($host_system == "ufxforall4"){
	if($iscompany == "1"){
		$driver_email="company@gmail.com";
	}else{
		$driver_email="provider@demo.com";			
	}
	$rider_email="user@demo.com";
}else{
	$rider_email="rider@gmail.com";
	if($iscompany == "1"){
		$driver_email="company@gmail.com";
	}else{
		$driver_email="driver@gmail.com";
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
		<div class="page-contant">
			<div class="page-contant-inner">
				<h2 class="header-page" id="label-id"><?=$langage_lbl['LBL_SIGN_IN_TXT'];?>
				<?php if(SITE_TYPE =='Demo'){?>
				<p><?=$langage_lbl['LBL_SINCE_IT_IS_DEMO'];?></p>
				<?php }?>
				</h2>
				<!-- login in page -->
				<div class="login-form">
				<div class="login-err">
				<p id="errmsg" style="display:none;" class="text-muted btn-block btn btn-danger btn-rect error-login-v"></p>
				<p style="display:none;" class="btn-block btn btn-rect btn-success error-login-v" id="success" ></p>
				</div>
						<div class="login-form-left"> <form action="<?=($action == 'rider')?'mytrip.php':'profile.php';?>" class="form-signin" method = "post" id="login_box" onSubmit="return chkValid('<?=$action?>');" >	
							<b>
								
								<input type="hidden" name="action" value="<?php echo $action?>"/>
								<input type="hidden" name="type_usr" value="<?php echo $type?>"/>
								
                                <label><?=$langage_lbl['LBL_EMAIL_MOBILE_NO_TXT_MSG']; ?></label>
								<input name="vEmail" type="text" placeholder="<?=$langage_lbl['ENTER_EMAIL_ID_OR_MOBILE_TXT']; ?>" class="login-input" id="vEmail" value="<?=(SITE_TYPE == 'Demo') ? (($action == 'rider') ? $rider_email:$driver_email) : '';?>" required /></b>
                                <b>
								
								
                                 <label><?=$langage_lbl['LBL_COMPANY_DRIVER_PASSWORD']; ?></label>
								<input name="vPassword" type="password" placeholder="<?=$langage_lbl['LBL_PASSWORD_LBL_TXT']; ?>" class="login-input" id="vPassword" value="<?=(SITE_TYPE == 'Demo') ? '123456' : ''?>" required />
							</b> 
							<b>
								<input type="submit" class="submit-but" value="<?=$langage_lbl['LBL_SIGN_IN_TXT'];?>" />
								<a onClick="change_heading('forgot')"><?=$langage_lbl['LBL_FORGET_PASS_TXT'];?></a>
							</b> </form>
						
					
					
						
                        <form action="" method="post" class="form-signin" id="frmforget" onSubmit="return forgotPass();" style="display: none;">
                         
							<input type="hidden" name="action" id="action" value="<?=$action?>">
							<b>
                            <label><?=$langage_lbl['LBL_EMAIL_LBL_TXT']; ?></label>
								<input name="femail" type="text" placeholder="<?=$langage_lbl['LBL_EMAIL_LBL_TXT']; ?>" class="login-input" id="femail" value="" required />
							</b>
							<b>
								<input type="submit" class="submit-but" value="<?=$langage_lbl['LBL_Recover_Password']; ?>" />
								<a onClick="change_heading('login')"><?=$langage_lbl['LBL_LOGIN'];?></a>
							</b>	 </form>	
						</div>					
					
					<div class="login-form-right login-form-right1">
					<div class="login-form-right1-inner">
						      <h3><?=$langage_lbl['LBL_DONT_HAVE_ACCOUNT'];?></h3>
						      <span><a  class="company" href="<?=($action == 'rider')?'sign-up-rider':'sign-up';?>"><?=$langage_lbl['LBL_LOGIN_NEW_SIGN_UP'];?></a></span> 
				</div>
				<?php  if($iscompany == "0"){ ?>
				<div class="login-form-right1-inner">
				<?php  ?>
                  <!--span class="fb-login"><a href="facebook"><img alt="" src="assets/img/reg-fb.jpg"><?php //=$langage_lbl['LBL_SIGN_UP_WITH_FACEBOOK'];?></a></span-->
				  
					<!--<span class="login-socials">
						<a href="facebook/<?=$action?>" class="fa fa-facebook"></a>
						<a href="twitter/<?=$action?>" class="fa fa-twitter"></a>
						<a href="google/<?=$action?>" class="fa fa-google"></a>
					</span>-->
					<?php   if($action=='driver'){ 
					if($DRIVER_TWITTER_LOGIN == "Yes" || $DRIVER_GOOGLE_LOGIN == "Yes" || $DRIVER_FACEBOOK_LOGIN == "Yes"){ ?>
                 	<h3><?=$langage_lbl['LBL_REGISTER_WITH_ONE_CLICK'];?></h3>
				<?php  } ?>
						<span class="login-socials">
						<?php  if($DRIVER_FACEBOOK_LOGIN == "Yes"){ ?>						
							<a href="facebook/<?=$action?>" class="fa fa-facebook"></a>
						<?php  } 
						if($DRIVER_TWITTER_LOGIN == "Yes"){ ?>
							
							<a href="twitter/<?=$action?>" class="fa fa-twitter"></a>
						<?php  } if($DRIVER_GOOGLE_LOGIN == "Yes"){ ?>
							
							<a href="google/<?=$action?>" class="fa fa-google"></a>
						<?php  } ?>
							
						</span>
				<?php  } 
				if($action=='rider'){
				if($PASSENGER_FACEBOOK_LOGIN == "Yes" || $PASSENGER_TWITTER_LOGIN == "Yes" || $PASSENGER_GOOGLE_LOGIN == "Yes"){ ?>
                 	<h3><?=$langage_lbl['LBL_REGISTER_WITH_ONE_CLICK'];?></h3>
				<?php  } ?>
					<span class="login-socials">
					<?php  if($PASSENGER_FACEBOOK_LOGIN == "Yes"){?>
					
						<a href="facebook-rider/<?=$action?>" class="fa fa-facebook"></a>
					<?php  } 
					if($PASSENGER_TWITTER_LOGIN == "Yes"){ ?>
						
						<a href="twitter/<?=$action?>" class="fa fa-twitter"></a>
					<?php  } if($PASSENGER_GOOGLE_LOGIN == "Yes"){ ?>
						
						<a href="google/<?=$action?>" class="fa fa-google"></a>
					<?php  } ?>
						
					</span>
				<?php  } ?>
				  
    			</div>
				<?php  } ?>
				</div>   
				</div>
					
				<div style="clear:both;"></div>
				<?php 
					if(SITE_TYPE == 'Demo'){
					 	if($action=='rider'){
		 		?>
				     
		     	<div class="text-center" style="text-align:left;">
					<?php if($host_system == "carwash"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new user, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Rider : </b><br />
					Username: user@demo.com<br />
					Password: 123456
					</p>
					<?php }elseif($host_system == "beautician" || $host_system == "beautician4" || $host_system == "carwash4" || $host_system == "dogwalking4" || $host_system == "towtruck4" || $host_system == "massage4" || $host_system == "ufxforall4"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new user, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>User : </b><br />
					Username: user@demo.com<br />
					Password: 123456
					</p>
					<?php }elseif($host_system == "tutors"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new student, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Student : </b><br />
					Username: student@demo.com<br />
					Password: 123456
					</p>
					<?php }elseif($host_system == "doctor4"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new patient, use your registered Email Id and Password to view the detail of your Appointment.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Doctor : </b><br />
					Username: patient@demo.com<br />
					Password: 123456
					</p>
					<?php }else{?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new Rider, use your registered Email Id and Password to view the detail of your Rides.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Rider : </b><br />
					Username: rider@gmail.com<br />
					Password: 123456
					</p>

					<?php }?>
				<!--<h4 ><?=$langage_lbl['LBL_PLEASE_USE_BELOW'];?> </h4>
						<h5>
							<p><?=$langage_lbl['LBL_IF_YOU_HAVE_REGISTER'];?></p>
							<p><b><?=$langage_lbl['LBL_USER_NAME_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_USERNAME'];?></p> 
							<p><b><?=$langage_lbl['LBL_PASSWORD_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_PASSWORD'];?> </p>
						</h5>
						-->
				</div>
		     	<?php  
		     			}else{ 
 				?>
		     	<div class="text-center" style="text-align:left;">
					<?php if($host_system == "carwash"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new Washer, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Washer : </b><br />
					Username: washer@demo.com<br />
					Password: 123456
					</p>
					<?php }elseif($host_system == "beautician" || $host_system == "beautician4"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new beautician , use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Beautician : </b><br />
					Username: beautician@demo.com<br />
					Password: 123456
					</p>
					<?php }elseif($host_system == "tutors"){?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new Tutor, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Tutor : </b><br />
					Username: tutor@demo.com<br />
					Password: 123456
					</p>
					<?php }elseif($host_system == "carwash4"){ ?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new car washer, use your registered Email Id and Password to view the detail of your Jobs.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Car Washer : </b><br />
					Username: carwasher@demo.com<br />
					Password: 123456
					</p>			
					<?php  } elseif($host_system == "doctor4"){ ?>
						<h4>
						<b>Note :</b><br /> 
						- If you have registered as a new doctor, use your registered Email Id and Password to view the detail of your Jobs.<br />
						</h4>
						To view the Standard Features of the Apps use below access detail :<br /><br />
						<p>
						<b>Doctor : </b><br />
						Username: doctor@demo.com<br />
						Password: 123456
						</p>			
					<?php  } elseif($host_system == "massage4"){ ?>
						<h4>
						<b>Note :</b><br /> 
						- If you have registered as a new massge therapist, use your registered Email Id and Password to view the detail of your Jobs.<br />
						</h4>
						To view the Standard Features of the Apps use below access detail :<br /><br />
						<p>
						<b>Massage Therapist : </b><br />
						Username: massager@demo.com<br />
						Password: 123456
						</p>			
					<?php  } elseif($host_system == "dogwalking4"){ ?>
						<h4>
						<b>Note :</b><br /> 
						- If you have registered as a new dog walker, use your registered Email Id and Password to view the detail of your Jobs.<br />
						</h4>
						To view the Standard Features of the Apps use below access detail :<br /><br />
						<p>
						<b>Dog Walker : </b><br />
						Username: dogwalker@demo.com<br />
						Password: 123456
						</p>			
					<?php  }  elseif($host_system == "towtruck4"){ ?>
						<h4>
						<b>Note :</b><br /> 
						- If you have registered as a new towing driver, use your registered Email Id and Password to view the detail of your Jobs.<br />
						</h4>
						To view the Standard Features of the Apps use below access detail :<br /><br />
						<p>
						<b>Dog Walker : </b><br />
						Username: provider@demo.com<br />
						Password: 123456
						</p>			
					<?php  } elseif($host_system == "ufxforall4"){ ?>
						<h4>
						<b>Note :</b><br /> 
						- If you have registered as a new provider, use your registered Email Id and Password to view the detail of your Jobs.<br />
						</h4>
						To view the Standard Features of the Apps use below access detail :<br /><br />
						<p>
						<b>Provider : </b><br />
						Username: provider@demo.com<br />
						Password: 123456
						</p>			
					<?php  } else{?>
					<h4>
					<b>Note :</b><br /> 
					- If you have registered as a new Driver, use your registered Email Id and Password to view the detail of your Rides.<br />
					</h4>
					To view the Standard Features of the Apps use below access detail :<br /><br />
					<p>
					<b>Driver : </b><br />
					Username: driver@gmail.com<br />
					Password: 123456
					</p>

					<?php }?>
					<p>
					<br /><b>Company : </b><br />
					Username: company@gmail.com<br />
					Password: 123456
					</p>
					<!--<h4 ><?=$langage_lbl['LBL_PLEASE_USE_BELOW_DRIVER'];?> </h4>
					<h5 >
						<p><?=$langage_lbl['LBL_IF_YOU_HAVE_REGISTER_DRIVER'];?></p>
						<p><b><?=$langage_lbl['LBL_USER_NAME_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_USERNAME_DRIVER'];?></p>
						<p><b><?=$langage_lbl['LBL_PASSWORD_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_PASSWORD'];?> </p>
					</h5>
					<h4 ><?=$langage_lbl['LBL_PLEASE_USE_BELOW_DEMO'];?></h4>
					<h5 >
						<p><?=$langage_lbl['LBL_IF_YOU_HAVE_REGISTER_COMPANY'];?></p>
						<p><b><?=$langage_lbl['LBL_USER_NAME_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_USERNAME_COMPANY'];?></p>
						<p><b><?=$langage_lbl['LBL_PASSWORD_LBL_TXT'];?></b>: <?=$langage_lbl['LBL_PASSWORD'];?> </p>
					</h5> -->
				</div>
				<?php 
						}
					}
				?>
				
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
    <script>
		<?php  if($forgetPWd==1){ ?>
				$('#frmforget').show();
				$('#login_box').hide();
				$('#label-id').text("<?=$langage_lbl['LBL_FORGOR_PASSWORD'];?>");
		<?php  } ?>
		
		function change_heading(type)
		{
			$('.error-login-v').hide();
			if(type=='forgot'){
				
				$('#frmforget').show();
				$('#login_box').hide();
				$('#label-id').text("<?=addslashes($langage_lbl['LBL_FORGOR_PASSWORD']);?>");
			}				
			else{
				$('#frmforget').hide();
				$('#login_box').show();
				$('#label-id').text("<?=addslashes($langage_lbl['LBL_SIGN_IN_TXT']);?>");
			}
		}
		function chkValid(login_type)
		{
			var id = document.getElementById("vEmail").value;
			var pass = document.getElementById("vPassword").value;
			if(id == '' || pass == '')
			{
				document.getElementById("errmsg").innerHTML = '<?=addslashes($langage_lbl['LBL_EMAIL_PASS_ERROR_MSG']);?>';
				document.getElementById("errmsg").style.display = '';
				return false;
			}
			else
			{
				var request = $.ajax({
					type: "POST",
					url: 'ajax_login_action.php',
					data: $("#login_box").serialize(),

					success: function(data)
					{
						if(data == 1){
							document.getElementById("errmsg").innerHTML = '<?=addslashes($langage_lbl['LBL_ACC_DELETE_TXT']);?>';
							document.getElementById("errmsg").style.display = '';
							return false;
						}
						else if(data == 2){
							document.getElementById("errmsg").style.display = 'none';
							departType = '<?php  echo $depart; ?>';
							if(login_type == 'rider' && departType == 'mobi')
								window.location = "mobi";
							else if(login_type == 'rider')
								window.location = "profile_rider.php";
							else if(login_type == 'driver')
								window.location = "profile.php";

							return true; // success registration
						}
						else if(data == 3) {
							document.getElementById("errmsg").innerHTML = '<?=addslashes($langage_lbl['LBL_INVALID_EMAIL_MOBILE_PASS_ERROR_MSG']);?>';
							document.getElementById("errmsg").style.display = '';
						   return false;

						}else if(data == 4) {
							document.getElementById("errmsg").innerHTML = '<?=addslashes($langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG']);?>';
							document.getElementById("errmsg").style.display = '';
						   return false;

						}
						else {
							document.getElementById("errmsg").innerHTML = '<?=addslashes($langage_lbl['LBL_INVALID_EMAIL_MOBILE_PASS_ERROR_MSG']);?>';
							document.getElementById("errmsg").style.display = '';
							//setTimeout(function() {document.getElementById('errmsg1').style.display='none';},2000);
							return false;
						}
					}
				});

				request.fail(function(jqXHR, textStatus) {
					alert( "Request failed: " + textStatus );
					return false;
				});
				return false;
			}
		}
		function forgotPass()
		{
			$('.error-login-v').hide();
			var site_type='<?php echo SITE_TYPE;?>';
			var id = document.getElementById("femail").value;
			if(id == '')
			{
				document.getElementById("errmsg").style.display = '';
				document.getElementById("errmsg").innerHTML = '<?=addslashes($langage_lbl['LBL_FEILD_EMAIL_ERROR_TXT_IPHONE']);?>';
			}
			else {
				var request = $.ajax({
					type: "POST",
					url: 'ajax_fpass_action.php',
					data: $("#frmforget").serialize(),
					dataType: 'json',
					beforeSend:function()
					{
						//alert(id);
						},
					success: function(data)
					{

						if(data.status == 1)
						{
							change_heading('login');
							document.getElementById("success").innerHTML = data.msg;
							document.getElementById("success").style.display = '';
							
						}
						else
						{
							document.getElementById("errmsg").innerHTML = data.msg;
							document.getElementById("errmsg").style.display = '';
						}
						
					}
				});

				request.fail(function(jqXHR, textStatus) {
					alert( "Request failed: " + textStatus );
				});

				
			}
			return false;
		}

		function fbconnect()
		{
			javscript:window.location='fbconnect.php';
		}
		
		$(document).ready(function(){
			var err_msg = '<?=$err_msg?>';
			// alert(err_msg);
			if(err_msg != ""){
				document.getElementById("errmsg").innerHTML = err_msg;
				document.getElementById("errmsg").style.display = '';
				return false;
			}
		});
	</script>
	<?php  
	if($forpsw == 1){ ?>
		<script>
			change_heading('forgot');
		</script>
	<?php  }

	?>
</body>
</html>
