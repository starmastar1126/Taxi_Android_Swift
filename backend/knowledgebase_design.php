<?php 
	include_once("common.php");
	//error_reporting(E_ALL);
	global $generalobj;
	$script="About Us";
	$meta = $generalobj->getStaticPage(1,$_SESSION['sess_lang']);
	 //echo "<pre>";print_r($_);exit;
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>
<?=$meta['meta_title'];?>
</title>
<meta name="keywords" value="<?=$meta['meta_keyword'];?>"/>
<meta name="description" value="<?=$meta['meta_desc'];?>"/>
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
  <div class="page-contant custom-error-page">
    <div class="breadcrumbs">
    <div class="breadcrumbs-inner">
    <span><a href="#">Administrator Panel ></a>Front Panel</span>
    <b><input name="" type="text" placeholder="Search"></b>
    </div>
    </div>

    <div class="page-contant-inner">
      <h2 class="header-page trip-detail">Help</h2>
      <!-- trips detail page -->
      <div class="static-page custom-error-page">
      <div class="custom-error-left-part">
      <ul>
      <li><a href="#"><img src="assets/img/administrator-panel-icon.png" alt="">Administrator Panel</a></li>
      <li><a href="#"><img src="assets/img/front-panel-icon.png" alt="">Front Panel</a></li>
      <li><a href="#"><img src="assets/img/rider-application-icon.png" alt="">Rider Application</a></li>
      <li><a href="#"><img src="assets/img/driver-application-icon.png" alt="">Driver Application</a></li>
      </ul>
      </div>
      <div class="custom-error-right-part">
      <h3>Administrator Panel</h3>
      <div class="custom-error-right-part-box">
      <ul>
      <h4>Advanced</h4>
      <li><a href="#">Custom Error Pages</a></li>
      <li><a href="#">Error Page Codes</a></li>
      <li><a href="#">What Are Cron Jobs?</a></li>
      <li><a href="#">What Do I Put for the Cron Job Command?</a></li>
      <li><a href="#">How Do I Create and Delete a Cron Job?</a></li>
      </ul>
      <ul>
      <h4>Advanced</h4>
      <li><a href="#">Custom Error Pages</a></li>
      <li><a href="#">Error Page Codes</a></li>
      <li><a href="#">What Are Cron Jobs?</a></li>
      <li><a href="#">What Do I Put for the Cron Job Command?</a></li>
      <li><a href="#">How Do I Create and Delete a Cron Job?</a></li>
      </ul>
      </div>
      <div class="custom-error-right-part-box">
      <ul>
      <h4>Advanced</h4>
      <li><a href="#">Custom Error Pages</a></li>
      <li><a href="#">Error Page Codes</a></li>
      <li><a href="#">What Are Cron Jobs?</a></li>
      <li><a href="#">What Do I Put for the Cron Job Command?</a></li>
      <li><a href="#">How Do I Create and Delete a Cron Job?</a></li>
      </ul>
      <ul>
      <h4>Advanced</h4>
      <li><a href="#">Custom Error Pages</a></li>
      <li><a href="#">Error Page Codes</a></li>
      <li><a href="#">What Are Cron Jobs?</a></li>
      <li><a href="#">What Do I Put for the Cron Job Command?</a></li>
      <li><a href="#">How Do I Create and Delete a Cron Job?</a></li>
      </ul>
      </div>
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
