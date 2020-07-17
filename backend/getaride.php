<?php 
	include_once("common.php");
	error_reporting(E_ALL);
	global $generalobj;
	
	$meta = $generalobj->getStaticPage(21,$_SESSION['sess_lang']);
	 //echo "<pre>";print_r($_);exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<?php  include_once("common.php");?>
	<?php  include_once("top/header_home.php");?>
	<title><?=$meta['meta_title'];?></title>
	<meta name="keywords" value="<?=$meta['meta_keyword'];?>"/>
	<meta name="description" value="<?=$meta['meta_desc'];?>"/>
    <link href='https://fonts.googleapis.com/css?family=Raleway:400,100,500,700' rel='stylesheet' type='text/css'>
	<link href="assets/css/home/parallax.css" rel="stylesheet" type="text/css">
	<link href="assets/css/style.css" rel="stylesheet" type="text/css">
</head>
<body>
	<!--?php include("top/left_menu.php");?>-->
	<!--?php include("top/header_menu.php");?>-->
	
	<div class="main-page">
		<?php  include("top/header_logo_menu.php");?>
		<section class="banner-div">
			<img src="assets/img/home/about-banner.jpg" alt="">
			<div class="container breadcrumb-main">
				<div class="row ">
					<div class="col-sm-12 breadcrumb-top">
						<ol class="breadcrumb">
						  <li><i class="glyphicon glyphicon-home"></i> <a href="index.php">Home</a></li>
						  <li class="active"><?=$meta['page_title'];?></li>
						</ol>							
					</div>
				</div>
			</div>
		</section>
		<div class="clear"></div>
		
		<section class="about-page">
			<div class="container">
				<div class="row page-heading">
					<h2><?=$meta['page_title'];?></h2>
				</div>
				<div class="page-content">
				<?=$meta['page_desc'];?>
				</div>
			</div>
		</section>
	
		<?php  include("footer/footer_home.php");?>
	</div>
	<script>
	var main = function() {
		$('.icon-menu').click(function() {
		$('.menu').animate({
			left: "0px"
		}, 200);
		$('body').animate({
			left: "285px"
		}, 200);
 
	});
	$('.icon-close').click(function() {
 $('.menu').animate({
  left: "-285px"
 }, 200);

    
$('body').animate({
    
  left: "0px"
  
  }, 200);
  
});

};



$(document).ready(main);
</script>
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-36251023-1']);
  _gaq.push(['_setDomainName', 'jqueryscript.net']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>
</html>
