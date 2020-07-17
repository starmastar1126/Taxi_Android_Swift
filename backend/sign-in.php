<?php 
    include_once("common.php");		
    $generalobj->go_to_home();
    $action = isset($_GET['action'])?$_GET['action']:'';
	$script="Login Main";	
	$meta_arr = $generalobj->getsettingSeo(1);		
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!--<title><?=$SITE_NAME?> | Login Page</title>-->
    <title><?php  echo $meta_arr['meta_title'];?></title>
    <!-- Default Top Script and css -->
    <?php  include_once("top/top_script.php");?>
    <!-- End: Default Top Script and css-->
</head>
<body>
  <div id="main-uber-page">
    <!-- Left Menu -->
    <?php  include_once("top/left_menu.php");?>
    <!-- End: Left Menu-->
    <!-- home page -->
  
        <!-- Top Menu -->
        <?php  include_once("top/header_topbar.php");?>
        <!-- End: Top Menu-->
        <!-- contact page-->
        <div class="page-contant">
            <div class="page-contant-inner">
                <h2 class="header-page"><?=$langage_lbl['LBL_SIGN_IN_SIGN_IN_TXT'];?></h2>
                <!-- login in page -->
                <div class="sign-in">
					<div class="sign-in-driver">
						<h3><?=$langage_lbl['LBL_Company'];?></h3>
							<p><?=$langage_lbl['LBL_SIGN_NOTE3'];?></p>
							<span><a href="company-login"><?=$langage_lbl['LBL_SIGNIN_COMPNY_SIGNIN'];?><img src="assets/img/arrow-white-right.png" alt="" /></a></span>
						</div>
                    <div class="sign-in-driver">
                        <h3><?=$langage_lbl['LBL_SIGNIN_DRIVER'];?></h3>
                        <p><?=$langage_lbl['LBL_SIGN_NOTE1'];?></p>
                        <span><a href="driver-login"><?= $langage_lbl['LBL_SIGNIN_DRIVERSIGNIN'];?><img src="assets/img/arrow-white-right.png" alt="" /></a></span>
                    </div>
                    
					<div class="sign-in-rider">
                    <h3><?=$langage_lbl['LBL_SIGNIN_RIDER'];?></h3>
                        <p><?=$langage_lbl['LBL_SIGN_NOTE2'];?></p>
                        <span><a href="rider-login"><?=$langage_lbl['LBL_SIGNIN_RIDER_SIGNIN'];?><img src="assets/img/arrow-white-right.png" alt="" /></a></span>
                    </div>
                </div>
                <div style="clear:both;"></div>
            </div>
        </div>
    <!-- home page end-->
    <!-- footer part -->
    <?php  include_once('footer/footer_home.php');?>
      <!-- End:contact page-->
      <div style="clear:both;"></div>
    </div>
    <!-- footer part end -->
    <!-- Footer Script -->
    <?php  include_once('top/footer_script.php');?>
    <!-- End: Footer Script -->
</body>
</html>
