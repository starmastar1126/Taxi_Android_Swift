<?php 
	$_SESSION['sess_user'] = isset($_SESSION['sess_user'])?$_SESSION['sess_user']:'';
	if ($_SESSION['sess_user'] == 'company') {
		$sql = "select * from company where iCompanyId = '" . $_SESSION['sess_iUserId'] . "'";
		$db_user = $obj->MySQLSelect($sql);
	}
	if ($_SESSION['sess_user'] == 'driver') {
		$sql = "select * from register_driver where iDriverId = '" . $_SESSION['sess_iUserId'] . "'";
		$db_user = $obj->MySQLSelect($sql);
	}
	if ($_SESSION['sess_user'] == 'rider'){
		$sql = "select * from register_user where iUserId = '".$_SESSION['sess_iUserId']."'";
		$db_user = $obj->MySQLSelect($sql);
	}
	//echo "<pre>";print_r($_SESSION);exit;
	// echo "<pre>";
	// print_r($db_user);
	$col_class = "";
	if($user != "") { 
		$col_class = "top-inner-color";
	}

	if($host_system == 'cubetaxiplus') {
		$logo = "logo.png";
	} else if($host_system == 'ufxforall') {
		$logo = "ufxforall-logo.png";
	} else if($host_system == 'uberridedelivery4') {
		$logo = "ride-delivery-logo.png";
	} else if($host_system == 'uberdelivery4') {
		$logo = "delivery-logo-only.png";
	} else {
		$logo = "logo.png";
	}
?> 
<div id="top-part" class="<?=$col_class;?>">
    <div class="top-part-inner">
		<?php  $logoName = strstr($_SERVER['SCRIPT_NAME'],'/') && strstr($_SERVER['SCRIPT_NAME'],'/index.php')?'logo.png':'logo-inner.png' ;?>

        <?php  if($user=="") { ?>
			<div class="logo">
			<a href="index.php">
				<img src="assets/img/<?php  echo $logo;?>" alt="">
			</a>
		
			<span class="top-logo-link" ><a href="about" class="<?=(isset($script) && $script == 'About Us')?'active':'';?>"><?=$langage_lbl['LBL_ABOUT_US_HEADER_TXT']; ?></a><a href="contact-us" class="<?=(isset($script) && $script == 'Contact Us')?'active':'';?>"><?=$langage_lbl['LBL_FOOTER_HOME_CONTACT_US_TXT']; ?></a></span>
		</div> 
           	<?php  
           		if(isset($_REQUEST['edit_lbl'])){ ?>

           	<div class="top-link"> 
                <span>
            	<a href="help-center" class="<?=(isset($script) && $script == 'Help Center')?'active':'';?>"><?=$langage_lbl['LBL_HEADER_HELP_TXT'];?></a>
            	<a href="sign-in"  class="<?php  echo strstr($_SERVER['SCRIPT_NAME'],'/sign-in') || strstr($_SERVER['SCRIPT_NAME'],'/login-new')?'active':'' ?>"><?=$langage_lbl['LBL_HEADER_TOPBAR_SIGN_IN_TXT'];?></a>
				</span>
			</div>

           <?php  } else {?>

           	<div class="top-link"> 
                <span>
            	<a href="help-center" class="<?=(isset($script) && $script == 'Help Center')?'active':'';?>"><?=$langage_lbl['LBL_HEADER_HELP_TXT'];?></a>
            	<a href="sign-in"  class="<?php  echo strstr($_SERVER['SCRIPT_NAME'],'/sign-in') || strstr($_SERVER['SCRIPT_NAME'],'/login-new')?'active':'' ?>"><?=$langage_lbl['LBL_HEADER_TOPBAR_SIGN_IN_TXT'];?></a>
				</span>
			</div>

           <?php  }?>
          
          <?php 
			} else {
			?>
			<?php  if($user != "") { 
				
				if (isset($db_user[0]['vImage']) && ($db_user[0]['vImage'] == 'NONE' || $db_user[0]['vImage'] == '') && ($db_user[0]['vImgName'] == 'NONE' || $db_user[0]['vImgName'] == '')) 
				{
					$img_url = "assets/img/profile-user-img.png";
					}else{
					if($_SESSION['sess_user'] == 'company'){
						$img_path = $tconfig["tsite_upload_images_compnay"];
						$img_url = $img_path . '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_data[0]['vImage'];
						}else if($_SESSION['sess_user'] == 'driver'){
						$img_path = $tconfig["tsite_upload_images_driver"];
						$img_url = $img_path . '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_data[0]['vImage'];
						}else {
						$img_path = $tconfig["tsite_upload_images_passenger"];
						$img_url = $img_path . '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_data[0]['vImgName'];
					}
					
				}?>
				<div class="logo">
					<a href="index.php"><img src="assets/img/<?php  echo $logo; ?>" alt=""></a>
		
					<span class="top-logo-link" ><a href="profile" class="<?=(isset($script) && $script == 'Profile')?'active':'';?>"><?=$langage_lbl['LBL_HEADER_TOPBAR_PROFILE_TITLE_TXT'];?></a><a href="logout"><?=$langage_lbl['LBL_HEADER_LOGOUT']; ?></a></span>
				</div> 
                <div class="top-link-login-new">
				<div class="user-part-login">
				<b><img src="<?= $img_url ?>" alt=""></b>
                <div class="top-link-login">
                <label><img src="assets/img/arrow-menu.png" alt=""></label>
                <ul>
                    <?php 
                        if($user == 'driver'){
						?>
						<li><a href="profile" class="<?=(isset($script) && $script == 'Profile')?'active':'';?>"><i class="fa fa-user" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_PROFILE_TITLE_TXT'];?></a></li>
						<?php  /*
						<?php  if($APP_TYPE != 'UberX'){ ?>
						<li><a href="vehicle" class="<?=(isset($script) && $script == 'Vehicle')?'active':'';?>"><i class="fa fa-car" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_VEHICLES'];?></a></li>
						<?php  }else{?>

							<li><a href="add_services.php" class="<?=(isset($script) && $script == 'My Availability')?'active':'';?>"><i class="fa fa-car" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_MY_AVAILABILITY'];?></a></li>


						<?php  } ?>
						<li><a href="driver-trip" class="<?=(isset($script) && $script == 'Trips')?'active':'';?>"><i class="fa fa-calendar" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_TRIPS_TEXT'];?></a></li>
						<li><a href="payment-request" class="<?=(isset($script) && $script == 'Payment Request')?'active':'';?>"><i class="fa fa-usd" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_MY_EARN']; ?></a></li>
						<?php  if($WALLET_ENABLE == 'Yes'){ ?> 
							<li><a href="driver_wallet" class="<?=(isset($script) && $script == 'Rider Wallet')?'active':'';?>"><i class="fa fa-money" aria-hidden="true"></i><?=$langage_lbl['LBL_RIDER_WALLET'];?></a></li>
						<?php  } ?> */ ?>
						<li><a href="logout"><i class="fa fa-power-off" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_LOGOUT']; ?></a></li>
						<?php 
						}
                        else if($user == 'company'){
						?>
                        <li><a href="profile" class="<?=(isset($script) && $script == 'Profile')?'active':'';?>"><i class="fa fa-user" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_PROFILE_TITLE_TXT'];?></a></li>
                        <?php  /*
						<li><a href="driverlist" class="<?=(isset($script) && $script == 'Driver')?'active':'';?>"><i class="fa fa-taxi" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_DRIVER']; ?></a></li>
                        <?php  if($APP_TYPE != 'UberX'){ ?>
                        <li><a href="vehicle" class="<?=(isset($script) && $script == 'Vehicle')?'active':'';?>"><i class="fa fa-car" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_VEHICLES'];?></a></li>
                        <?php  }
							/* else{?>
							<li><a href="add_services.php" class="<?=(isset($script) && $script == 'My Availability')?'active':'';?>"><i class="fa fa-car" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_MY_AVAILABILITY'];?></a></li>

						<?php  } 
                        <li><a href="company-trip" class="<?=(isset($script) && $script == 'Trips')?'active':'';?>"><i class="fa fa-calendar" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_TRIPS'];?></a></li>
						<!--<li><a href="booking.php" class="<?=(isset($script) && $script == 'Booking')?'active':'';?>"><i class="fa fa-taxi" aria-hidden="true"></i>My Bookings</a></li>--> */?>
                        <li><a href="logout"><i class="fa fa-power-off" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_LOGOUT']; ?></a></li>
						<?php 
						} else if($user == 'rider') {
						?>
                        <li><a href="profile-rider" class="<?=(isset($script) && $script == 'Profile')?'active':'';?>"><i class="fa fa-user" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_PROFILE_TITLE_TXT'];?></a></li>
                        <?php  /* <li><a href="mytrip" class="<?=(isset($script) && $script == 'Trips')?'active':'';?>"><i class="fa fa-calendar" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_TRIPS'];?></a></li>
						<?php   if($WALLET_ENABLE == 'Yes'){ ?> 
                        <li><a href="rider_wallet" class="<?=(isset($script) && $script == 'Rider Wallet')?'active':'';?>"><i class="fa fa-money" aria-hidden="true"></i><?=$langage_lbl['LBL_RIDER_WALLET'];?></a></li>
						<?php  } ?>*/?>
                        <li><a href="logout"><i class="fa fa-power-off" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_LOGOUT']; ?></a></li>
						<?php 
						}
					?>
                    </ul>
				
			</div>
				</div>
                
                
                </div>
			<?php  } ?>
            <!-- -->
			<?php 
			}
		?>
		
        <?php 
			//var_dump();
            if($user=="" && !stristr($_SERVER['SCRIPT_NAME'],'/index.php')){
			?>
			<!--div class="top-right-button">
				<span>
                <a href="sign-up-rider" ><?php  //=$langage_lbl['LBL_HEADER_TOPBAR_SIGN_UP_TO_RIDE']; ?></a>
                <a class="active" href="sign-up"><?php  //=$langage_lbl['LBL_HEADER_TOPBAR_BECOME_A_DRIVER']; ?></a>
				</span>
			</div-->
			<?php 
			}
		?>
        <div style="clear:both;"></div>
	</div>
</div>