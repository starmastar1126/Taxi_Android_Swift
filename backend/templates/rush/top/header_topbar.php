<?php 
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
?> 
<div id="top-part" class="<?=$col_class;?>">
    <div class="top-part-inner">
		<?php  $logoName = strstr($_SERVER['SCRIPT_NAME'],'/') && strstr($_SERVER['SCRIPT_NAME'],'/index.php')?'logo.png':'logo-inner.png' ;?>
        <div class="logo"><a href="index.php"><img src="assets/img/<?php  echo $logoName; ?>" alt=""></a></div>        	
        <?php 
            if($user==""){ 
			?>
            <div class="top-link"> 
                <span>
            		<a href="help-center" class="<?=(isset($script) && $script == 'Help Center')?'active':'';?>"><?=$langage_lbl['LBL_HELP_TXT'];?></a>
            		<a href="sign-in" class="<?php  echo strstr($_SERVER['SCRIPT_NAME'],'/sign-in') || strstr($_SERVER['SCRIPT_NAME'],'/login-new')?'active':'' ?>"><?=$langage_lbl['LBL_HEADER_TOPBAR_SIGN_IN_TXT'];?></a>
				</span>
			</div>
			<?php 
			}
            else{
			?>
            <div class="top-link-login">
                <span>
                    <?php 
                        if($user == 'driver'){
						?>
						<a href="profile" class="<?=(isset($script) && $script == 'Profile')?'active':'';?>"><i class="fa fa-user" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_PROFILE_TITLE_TXT'];?></a>
						 <?php  if($APP_TYPE != 'UberX'){ ?>
						<a href="vehicle" class="<?=(isset($script) && $script == 'Vehicle')?'active':'';?>"><i class="fa fa-car" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_VEHICLES'];?></a>
						<?php  }else{?>
							<a href="add_services.php" class="<?=(isset($script) && $script == 'My Availability')?'active':'';?>"><i class="fa fa-car" aria-hidden="true"></i><?=$langage_lbl['LBL_MY_AVAILABILITY'];?></a>
						<?php  } ?>
						<a href="driver-trip" class="<?=(isset($script) && $script == 'Trips')?'active':'';?>"><i class="fa fa-calendar" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_TRIPS'];?></a>
						<a href="payment-request" class="<?=(isset($script) && $script == 'Payment Request')?'active':'';?>"><i class="fa fa-usd" aria-hidden="true"></i><?=$langage_lbl['LBL_MY_EARN']; ?></a>
						<?php  if($WALLET_ENABLE == 'Yes'){ ?> 
							<a href="driver_wallet" class="<?=(isset($script) && $script == 'Rider Wallet')?'active':'';?>"><i class="fa fa-money" aria-hidden="true"></i><?=$langage_lbl['LBL_RIDER_WALLET'];?></a>
						<?php  } ?>
						<a href="logout"><i class="fa fa-power-off" aria-hidden="true"></i><?=$langage_lbl['LBL_LOGOUT']; ?></a>
						<?php 
						}
                        else if($user == 'company'){
						?>
                        <a href="profile" class="<?=(isset($script) && $script == 'Profile')?'active':'';?>"><i class="fa fa-user" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_PROFILE_TITLE_TXT'];?></a>
                        <a href="driverlist" class="<?=(isset($script) && $script == 'Driver')?'active':'';?>"><i class="fa fa-taxi" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_DRIVER']; ?></a>
						<?php  if($APP_TYPE != "UberX") {?>
							<a href="vehicle" class="<?=(isset($script) && $script == 'Vehicle')?'active':'';?>"><i class="fa fa-car" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_VEHICLES'];?></a>
						<?php  } ?> 
                        <a href="company-trip" class="<?=(isset($script) && $script == 'Trips')?'active':'';?>"><i class="fa fa-calendar" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_TRIPS'];?></a>
						<?php  if($APP_TYPE != "UberX") {?>
							<a href="booking.php" class="<?=(isset($script) && $script == 'Booking')?'active':'';?>"><i class="fa fa-taxi" aria-hidden="true"></i>My Bookings</a>
						<?php  } ?>
                        <a href="logout"><i class="fa fa-power-off" aria-hidden="true"></i><?=$langage_lbl['LBL_LOGOUT']; ?></a>
						<?php 
						}
                        else if($user == 'rider'){
						?>
                        <a href="profile-rider" class="<?=(isset($script) && $script == 'Profile')?'active':'';?>"><i class="fa fa-user" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_PROFILE_TITLE_TXT'];?></a>
                        <a href="mytrip" class="<?=(isset($script) && $script == 'Trips')?'active':'';?>"><i class="fa fa-calendar" aria-hidden="true"></i><?=$langage_lbl['LBL_HEADER_TOPBAR_TRIPS'];?></a>
						<?php   if($WALLET_ENABLE == 'Yes'){ ?> 
                        <a href="rider_wallet" class="<?=(isset($script) && $script == 'Rider Wallet')?'active':'';?>"><i class="fa fa-money" aria-hidden="true"></i><?=$langage_lbl['LBL_RIDER_WALLET'];?></a>
						<?php  } ?>
                        <a href="logout"><i class="fa fa-power-off" aria-hidden="true"></i><?=$langage_lbl['LBL_LOGOUT']; ?></a>
						<?php 
						}
					?>
				</span>
			</div>
            <!-- -->
			<?php  if($user != "") { 
				
				if (($db_user[0]['vImage'] == 'NONE' || $db_user[0]['vImage'] == '') && ($db_user[0]['vImgName'] == 'NONE' || $db_user[0]['vImgName'] == '')) 
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
				<div class="user-part-login">
					<span>Welcome!<br /><a href="profile.php">
						<?php  
							if($_SESSION['sess_user'] != 'company')
							{echo $db_user[0]['vName']."&nbsp".$db_user[0]['vLastName'];
								}else{
								echo $db_user[0]['vCompany'];
							} 
						?></a></span>
						<b><img src="<?= $img_url ?>" alt=""></b>
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