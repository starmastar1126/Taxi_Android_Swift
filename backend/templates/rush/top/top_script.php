<meta content="width=device-width, initial-scale=1.0" name="viewport" />
<meta content="" name="<?php  echo $meta_arr['meta_keyword']; ?>" />
<meta content="" name="<?php  echo $meta_arr['meta_desc']; ?>" />
<meta content="" name="author" />
<link rel="icon" href="favicon.ico" type="image/x-icon">
<?php 
$lang = isset($_SESSION['sess_lang']) ? $_SESSION['sess_lang'] : "EN";
$lang_arr = array('AR', 'UR' ,'HW', 'PS');
$lang_ltr = "";
if(in_array($lang,$lang_arr)) {
	$lang_ltr = 'yes';
}
?>
<!--[if IE]>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<![endif]-->
<!-- GLOBAL STYLES -->
<!-- PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php  echo $templatePath; ?>assets/plugins/bootstrap/css/bootstrap.css" />
<link rel="stylesheet" href="<?php  echo $templatePath; ?>assets/css/sign-up.css" />
<link rel="stylesheet" href="<?php  echo $templatePath; ?>assets/plugins/magic/magic.css" />
<!-- END PAGE LEVEL STYLES -->
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
<!-- Front Css-->
<link rel="stylesheet" href="assets/css/bootstrap-front.css" />
<link rel="stylesheet" type="text/css" href="assets/css/jquery-ui.css">
<link rel="stylesheet" href="assets/plugins/Font-Awesome/css/font-awesome.css" />
<link rel="stylesheet" href="assets/css/design.css">
<link rel="stylesheet" href="assets/css/style_rush.css">
<link rel="stylesheet" href="assets/css/fa-icon.css">
<link href="assets/css/initcarousel.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="assets/css/media.css">
<?php  if($lang_ltr == "yes") { ?>
<link rel="stylesheet" href="assets/css/style_rtl.css">
<?php  } ?>
<!-- Font CSS-->
<link href='http://fonts.googleapis.com/css?family=Raleway:400,700,300,500,900,800,600,200,100' rel='stylesheet' type='text/css'>

<!-- Default js-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js" type="text/javascript"></script>