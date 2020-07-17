<?php  
include 'common.php';
$generalobj->go_to_home();
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
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie9"> <![endif]-->
<!--[if !IE]><!--><html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="UTF-8" />
<title><?=$SITE_NAME?> |<?=$langage_lbl['LBL_LOGIN_PAGE']; ?> </title>
<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<meta content="" name="keywords" />
<meta content="" name="description" />
<meta content="" name="author" />
<!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <![endif]-->
<!-- GLOBAL STYLES -->
<!-- PAGE LEVEL STYLES -->
<link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.css" />
<link rel="stylesheet" href="assets/css/login.css" />
<link rel="stylesheet" href="assets/plugins/magic/magic.css" />
<!-- END PAGE LEVEL STYLES -->
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<!-- PAGE CONTENT -->
<div class="container">
  <div class="text-center"> <a href="index.php"> <img src="<?=$tconfig['tsite_img'].'/'.$logo;?>" id="<?=$SITE_NAME?>" alt=" <?=$SITE_NAME?>" /> </a> </div>
  <div class="sign-in-heading" >
    <h3><?=$langage_lbl['LBL_HOME_SIGN_UP']; ?></h3>
  </div>
  <div class="tab-content">
    <div id="login" class="tab-pane active">
    <div class="signin-part">
    <div class="signin-part-inner login-signup-page">
      <form action="index.html" class="form-signin2 form-login">
        <a href = "sign-up_rider.php"><?=$langage_lbl['LBL_SIGN_UP_AS_A_RIDER']; ?></a> <a href = "sign-up.php" class="login-option-2"><?=$langage_lbl['LBL_SIGN_UP_AS_A_DRIVER_COMPANY']; ?></a><br>
      </form>
      <div style="clear:both;"></div>
      </div>
      </div>
    </div>
  </div>
</div>
</div>
<!--END PAGE CONTENT -->
<!-- PAGE LEVEL SCRIPTS -->
<script src="assets/plugins/jquery-2.0.3.min.js"></script>
<script src="assets/plugins/bootstrap/js/bootstrap.js"></script>
<script src="assets/js/login.js"></script>
<!--END PAGE LEVEL SCRIPTS -->
</body>
<!-- END BODY -->
</html>