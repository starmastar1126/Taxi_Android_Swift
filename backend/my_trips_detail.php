<?php 
include_once('common.php');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>"> <!--<![endif]-->

<!-- BEGIN HEAD-->
<head>
	<meta charset="UTF-8" />
    <title><?=$SITE_NAME?> | Blank Page</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="keywords" />
	<meta content="" name="description" />
	<meta content="" name="author" />
	
	
	
	
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
            <div class="inner" style="min-height:600px;">
                <div class="row">
						<div class="col-lg-12 heading-top">
						<h2>YOUR TRIP</h2>
						</div>
				</div>
				<div class="row">		
                   		<div class="col-lg-12">
						<p class="heading">3:45 PM on September 4 2015</p>
						</div>
                </div>
                <hr />
				
				<div class="body-div trips-detail-body-div">
				<div class="row trip-detail-top-btn-part">
					<div class="col-md-3">
						<input type="button" class="trip-detail-top-btn-1" value="Resend" />
					</div>
					<div class="col-md-3">
						<input type="button" class="trip-detail-top-btn-2" value="Resend" />
					</div>
					<div class="col-md-3">
						<input type="button" class="trip-detail-top-btn-3" value="Resend" />
					</div>
					<div class="col-md-3">
						<input type="button" class="trip-detail-top-btn-4" value="Resend" />
					</div>
				</div>
				<div class="row">
					<div class="col-md-6 trip-detail-left">
						<div class="trip-detail-left-inner">
							<span><img src="assets/img/map-simple-2.png" alt=""></span>
							<div class="row trip-detail-left-inner-bottom">
								<div class="trip-address trip-detail-address">
									<div class="trip-address-start">
										<p>3:45 PM</p>
										<p><b>Prahlad Nagar S.T Pickup Stand, Prahlad Nagar, Ahmedabad, Gujarat 380015, India</b></p>
									</div>
									<div class="trip-address-end trips-destination">
										<p>4:10 PM</p>
										<p><b>9, National Highway 8C, Vasant Nagar, Ognaj, Ahmedabad, Gujarat 382481, India</b></p>
									</div>
							</div>
								<div class="row">	
									<div class="trip-details">
										<div class="col-md-4">
											<p>CAR</p>
											<p><b><?=$SITE_NAME?>GO</b></p>
										</div>
										<div class="col-md-4">
											<p>KILOMETERS</p>
											<p><b>10.41</b></p>
										</div>
										<div class="col-md-4">
											<p>TRIP TIME</p>
											<p><b>00:15:15</b></p>
										</div>
									</div>
								</div>	
							</div>
						</div>
					</div>
					<div class="col-md-6 trip-detail-right">
						<div class="row">
							<div class="col-md-12">
								<h2>FARE BREAKDOWN</h2>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6 ">
								<p>Base Fare</p>
							</div>
							<div class="col-md-6 right-text">
								<p>20</p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<p>Distance</p>
							</div>
							<div class="col-md-6 right-text">
								<p>72.87</p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<p>Time</p>
							</div>
							<div class="col-md-6 right-text">
								<p>15.25</p>
							</div>
						</div>
						<div class="row trip-detail-right-part-inner-subtotal">
							<div class="col-md-6">
								<p><b>Subtotal</b></p>
							</div>
							<div class="col-md-6 right-text">
								<p><b>108.12</b></p>
							</div>
						</div>
						<div class="row trip-detail-right-part-inner-last">
							<div class="col-md-6 ">
								<p class="left-gray-part">CHARGED<br> CASH</p>
							</div>
							<div class="col-md-6 right-text">
								<p><b>108.12</b></p>
							</div>
						</div>
						
					</div>
				</div>
				
            </div>
				<div class="trip-detail-bottom-part">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-2 round-image">
								<span><img src="assets/img/driver-photo.png" alt=""></span>
							</div>
							<div class="col-md-4">
								<h2>You rode with jaydeep</h2>
							</div>
							<div class="col-md-3">
								<p>RATE YOUR RIDE</p>
							</div>
							<div class="col-md-3">
								<span><img src="assets/img/star-blue.png" alt=""></span>
							</div>
						</div>
					</div>
				</div>
				</div>			
		</div>
		
		
		</div>
       <!--END PAGE CONTENT -->
    </div>
     <!--END MAIN WRAPPER -->

	<?php  include_once('footer.php');?>
	
</body>
	<!-- END BODY-->    
</html>
