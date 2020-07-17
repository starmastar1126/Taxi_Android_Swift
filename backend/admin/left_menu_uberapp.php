<!-- MENU SECTION -->
<?php 
	$APP_DELIVERY_MODE = $generalobj->getConfigurations("configurations","APP_DELIVERY_MODE");
	$RIDE_LATER_BOOKING_ENABLED = $generalobj->getConfigurations("configurations","RIDE_LATER_BOOKING_ENABLED");
	// $APP_TYPE ="Delivery";
?>
<section class="sidebar">
	
    <!-- Sidebar -->
    <div id="sidebar" class="test" >
        <nav class="menu">
            <ul class="sidebar-menu">
                <!-- Main navigation -->
                
                <?php  if($_SESSION['sess_iGroupId'] == '2') { ?>		
                    <?php  if($RIDE_LATER_BOOKING_ENABLED == 'Yes'){ ?>
						<?php  if($APP_TYPE !="Delivery" || ($APP_TYPE =="Delivery" && $APP_DELIVERY_MODE != "Multi")){?>
							<li class="<?= (isset($script) && $script == 'booking') ? 'active' : ''; ?>"><a href="add_booking.php"><i class="fa fa-taxi1" style="margin:2px 0 0;"><img src="images/manual-taxi-icon.png" alt="" /></i> <span>Manual Taxi Dispatch</span> </a></li>
						<?php  } ?>
					<?php  } ?>
                    <?php  if($RIDE_LATER_BOOKING_ENABLED == 'Yes'){ ?>
                        <li class="<?= (isset($script) && $script == 'CabBooking') ? 'active' : ''; ?>"><a href="cab_booking.php"><i aria-hidden="true" class="icon-book1" style="margin:2px 0 0;"><img src="images/ride-later-bookings.png" alt="" /></i> <span>Ride Later Bookings</span> </a></li>
					<?php  } ?>
                        <li class="<?= (isset($script) && $script == 'LiveMap') ? 'active' : ''; ?>"><a href="map.php"><i aria-hidden="true" class="icon-map-marker1" style="left:5px;"><img src="images/god-view-icon.png" alt="" /></i> <span>God's View</span> </a></li>
                    <!-- If groupId = 3 -->	
                    <?php  }else if($_SESSION['sess_iGroupId'] == '3') { ?>
					
					<li class="<?= (isset($script) && $script == 'Trips') ? 'active' : ''; ?>"><a href="trip.php"><i aria-hidden="true" class="fa fa-exchange1" style="margin:2px 0 0;"><img src="images/trips-icon.png" alt="" /></i> <span>Trips</span> </a></li>
					<li class="treeview <?= (isset($script) && 
						($script == 'Payment_Report' || 
						$script == 'referrer' || 
						$script == 'Wallet Report' || 
						$script == 'Driver Payment Report' || 
						$script == 'Driver Log Report' || 
						$script == 'CancelledTrips' || 
						$script == 'Driver Accept Report' || 
						$script == 'Driver Trip Detail' || 
						$script == 'Total Trip Detail'
						
						)) ? 'active' : ''; ?>"><a href="#" title="" class="expand "><i class="icon-cogs1" style="margin:3px 0 0;"><img src="images/reports-icon.png" alt="" /></i><span>Reports</span></a>
						<ul class="treeview-menu menu_drop_down">
							<li class=""><a href="payment_report.php" class="<?= (isset($script) && ($script == 'Payment_Report' )) ? 'sub_active' : ''; ?>"><i class="icon-money"></i> Payment Report</a></li>
							<?php  if($REFERRAL_SCHEME_ENABLE == 'Yes'){ ?>
								<li class=""><a href="referrer.php" class="<?= (isset($script) && ($script == 'referrer' )) ? 'sub_active' : ''; ?>"><i aria-hidden="true" class="fa fa-hand-peace-o"></i> Referral Report</a></li>	
							<?php  } ?>
							<?php  if($WALLET_ENABLE == 'Yes'){ ?>
								<li class=""><a href="wallet_report.php" class="<?= (isset($script) && ($script == 'Wallet Report' )) ? 'sub_active' : ''; ?>"><i aria-hidden="true" class="fa fa-google-wallet"></i> User Wallet Report</a></li> 	
							<?php  } ?>
							<li class=""><a href="driver_pay_report.php" class="<?= (isset($script) && ($script == 'Driver Payment Report' )) ? 'sub_active' : ''; ?>"><i class="icon-money"></i> Driver Payment Report</a></li>
							<li class=""><a href="driver_log_report.php" class="<?= (isset($script) && ($script == 'Driver Log Report' )) ? 'sub_active' : ''; ?>"><i class="glyphicon glyphicon-list-alt"></i> Driver Log Report</a></li>	
							<li class=""><a href="cancelled_trip.php" class="<?= (isset($script) && ($script == 'CancelledTrips' )) ? 'sub_active' : ''; ?>"><i class="fa fa-exchange" aria-hidden="true"></i> Cancelled Trip</a></li> 
							<li class=""><a href="ride_acceptance_report.php" class="<?= (isset($script) && ($script == 'Driver Accept Report' )) ? 'sub_active' : ''; ?>"><i class="icon-group"></i> Trip Acceptance Report </a></li>   
							<li class=""><a href="driver_trip_detail.php" class="<?= (isset($script) && ($script == 'Driver Trip Detail' )) ? 'sub_active' : ''; ?>"><i class="fa fa-taxi"></i> Trip Time Variance </a></li>
						</ul>
					</li>
                    
					<?php  }else{ ?>	
					<li class="<?= (!isset($script) ? 'active' : ''); ?>"><a href="dashboard.php" title=""> <i class="fa fa-tachometer" aria-hidden="true"></i> <span>Dashboard</span></a> </li>
					<li class="<?= (isset($script) && $script == 'site') ? 'active' : ''; ?>"><a href="dashboard-a.php" title=""> <i class="fa fa-sitemap" aria-hidden="true"></i> <span>Site Statistics</span></a> </li>
					<li class="<?= (isset($script) && $script == 'Admin') ? 'active' : ''; ?>"> <a href="admin.php" title=""> <i class="icon-user1" style="margin:1px 0 0;">
					<img src="images/icon/admin-icon.png" alt="" /></i> <span>Admin</span> </a></li>
					<li class="<?= (isset($script) && $script == 'Company') ? 'active' : ''; ?>" id="dispatch_li"><a href="company.php"><i aria-hidden="true" class="fa fa-building-o" style="margin:2px 0 0 2px;"></i><span>Company</span></a> </li>
					<li class="<?= (isset($script) && $script == 'Driver') ? 'active' : ''; ?>"> <a href="driver.php" title=""> <i class="icon-user1" style="margin:2px 0 0;"><img src="images/icon/driver-icon.png" alt="" /></i> <span><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></span> </a></li>
					<?php  if($APP_TYPE == 'UberX'){ ?>
						<li class="treeview <?=(isset($script) && $script == 'PetType' || $script == 'userPets')?'active':'';?>"><a href="#" title="" class="expand "><i class="icon-cogs1" style="margin:3px 0 0;"><img src="images/reports-icon.png" alt="" /></i><span><?php  echo $langage_lbl_admin['LBL_PET_TYPE'];?></span></a>
							<ul class="treeview-menu menu_drop_down">
								<li class=""><a href="pettype.php"><i class="icon-money"></i> <?php  echo $langage_lbl_admin['LBL_PET_TYPE'];?></a></li>
								<li class=""><a href="user_pets.php"><i aria-hidden="true" class="fa fa-taxi"></i> <?php  echo $langage_lbl_admin['LBL_USER_PETS_ADMIN'];?></a></li>
							</ul>
						</li>
					<?php  } ?>
					<li class="<?= (isset($script) && $script == 'Vehicle') ? 'active' : ''; ?>"><a href="vehicles.php"><i class="icon-group1" style="margin:0px;"><i class="fa fa-taxi" aria-hidden="true"></i></i> <span><?php  echo $langage_lbl_admin['LBL_VEHICLE_TXT_ADMIN'];?></span> </a></li>
					
					<li class="<?= (isset($script) && $script == 'VehicleType') ? 'active' : ''; ?>"><a href="vehicle_type.php"><i class="icon-user1" style="margin:2px 0 0;">
					<img src="images/icon/vehicle-type-icon.png" alt="" /></i> <span><?php  echo $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?></span> </a></li>
					<?php  if($APP_TYPE == 'UberX'){ ?>
						<li class="<?= (isset($script) && $script == 'VehicleCategory') ? 'active' : ''; ?>"><a href="vehicle_category.php"><i class="icon-group1" style="margin:2px 0 0;"><i class="fa fa-plus-square" aria-hidden="true"></i> <span> Vehicle Category</span> </a></li>
						<?php  }?>
						<li class="<?= (isset($script) && $script == 'Rider') ? 'active' : ''; ?>"><a href="rider.php"><i class="icon-group1" style="margin:2px 0 0;"><img src="images/rider-icon.png" alt="" /></i> <span><?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?></span> </a></li>
						
<!-- 						<li class="<?= (isset($script) && $script == 'Hotel Rider') ? 'active' : ''; ?>"><a href="hotel_rider.php"><i class="fa fa fa-bed" aria-hidden="true" style="margin:4px 0 0;"></i><span>Hotel (kiosk) Riders</span> </a></li> -->
						
						<?php  if($RIDE_LATER_BOOKING_ENABLED == 'Yes'){ ?>
							<?php  if($APP_TYPE !="Delivery" || ($APP_TYPE =="Delivery" && $APP_DELIVERY_MODE != "Multi")){?>
							<li class="<?= (isset($script) && $script == 'booking') ? 'active' : ''; ?>"><a href="add_booking.php"><i class="fa fa-taxi1" style="margin:2px 0 0;"><img src="images/manual-taxi-icon.png" alt="" /></i> <span>Manual Taxi Dispatch</span> </a></li>
							<?php  } ?>
						<?php  } ?>
						<li class="<?= (isset($script) && $script == 'Trips') ? 'active' : ''; ?>"><a href="trip.php"><i aria-hidden="true" class="fa fa-exchange1" style="margin:2px 0 0;"><img src="images/trips-icon.png" alt="" /></i> <span><?php  echo $langage_lbl_admin['LBL_TRIPS_TXT_ADMIN'];?></span> </a></li>
<!-- 						<li class="<?= (isset($script) && $script == 'Restricted Area') ? 'active' : ''; ?>"><a href="restricted_area.php"><i class="fa fa-map-signs" aria-hidden="true" style="margin:4px 0 0;"></i><span>Restricted Area</span> </a></li> -->



						<?php  if($RIDE_LATER_BOOKING_ENABLED == 'Yes'){ ?>
							<li class="<?= (isset($script) && $script == 'CabBooking') ? 'active' : ''; ?>"><a href="cab_booking.php"><i aria-hidden="true" class="icon-book1" style="margin:2px 0 0;"><img src="images/ride-later-bookings.png" alt="" /></i> <span><?php  echo $langage_lbl_admin['LBL_RIDE_LATER_BOOKINGS_ADMIN'];?></span> </a></li>
						<?php  } ?>
						<li class="<?= (isset($script) && $script == 'Coupon') ? 'active' : ''; ?>"><a href="coupon.php"><i aria-hidden="true" class="fa fa-product-hunt1" style="margin:3px 0 0;"><img src="images/promo-code-icon.png" alt="" /></i> <span>PromoCode</span> </a></li>
						<li class="<?= (isset($script) && $script == 'LiveMap') ? 'active' : ''; ?>"><a href="map.php"><i aria-hidden="true" class="icon-map-marker1" style="left:6px; top:6px;"><img src="images/god-view-icon.png" alt="" /></i> <span>God's View</span> </a></li>
						<li class="<?= (isset($script) && $script == 'Heat Map') ? 'active' : ''; ?>"><a href="heatmap.php"><i aria-hidden="true" class="fa fa-header1" style="left:5px;"><img src="images/heat-icon.png" alt="" /></i><span>Heat View</span></a></li>
						<li class="<?= (isset($script) && $script == 'Review') ? 'active' : ''; ?>"><a href="review.php"><i class="icon-comments1" style="left:7px;"><img src="images/reviews-icon.png" alt="" /></i> <span>Reviews</span> </a></li>
						
						<li class="treeview <?= (isset($script) && 
							($script == 'Payment_Report' || 
							$script == 'referrer' || 
							$script == 'Wallet Report' || 
							$script == 'Driver Payment Report' || 
							$script == 'Driver Log Report' || 
							$script == 'CancelledTrips' || 
							$script == 'Driver Accept Report' || 
							$script == 'Driver Trip Detail' || 
							$script == 'Total Trip Detail'
							
							)) ? 'active' : ''; ?>"><a href="#" title="" class="expand "><i class="icon-cogs1" style="margin:3px 0 0;"><img src="images/reports-icon.png" alt="" /></i><span>Reports</span></a>
							<ul class="treeview-menu menu_drop_down">
								<li class=""><a href="payment_report.php" class="<?= (isset($script) && ($script == 'Payment_Report' )) ? 'sub_active' : ''; ?>"><i class="icon-money"></i> Payment Report</a></li>
								<?php  if($REFERRAL_SCHEME_ENABLE == 'Yes'){ ?>
									<li class=""><a href="referrer.php" class="<?= (isset($script) && ($script == 'referrer' )) ? 'sub_active' : ''; ?>"><i aria-hidden="true" class="fa fa-hand-peace-o"></i> Referral Report</a></li>	
								<?php  } ?>
								<?php  if($WALLET_ENABLE == 'Yes'){ ?>
									<li class=""><a href="wallet_report.php" class="<?= (isset($script) && ($script == 'Wallet Report' )) ? 'sub_active' : ''; ?>"><i aria-hidden="true" class="fa fa-google-wallet"></i> User Wallet Report</a></li> 	
								<?php  } ?>
								<li class=""><a href="driver_pay_report.php" class="<?= (isset($script) && ($script == 'Driver Payment Report' )) ? 'sub_active' : ''; ?>"><i class="icon-money"></i> Driver Payment Report</a></li>
								<li class=""><a href="driver_log_report.php" class="<?= (isset($script) && ($script == 'Driver Log Report' )) ? 'sub_active' : ''; ?>"><i class="glyphicon glyphicon-list-alt"></i> Driver Log Report</a></li>	
								<li class=""><a href="cancelled_trip.php" class="<?= (isset($script) && ($script == 'CancelledTrips' )) ? 'sub_active' : ''; ?>"><i class="fa fa-exchange" aria-hidden="true"></i> Cancelled Trip</a></li> 
								<li class=""><a href="ride_acceptance_report.php" class="<?= (isset($script) && ($script == 'Driver Accept Report' )) ? 'sub_active' : ''; ?>"><i class="icon-group"></i> Trip Acceptance Report </a></li>   
								<li class=""><a href="driver_trip_detail.php" class="<?= (isset($script) && ($script == 'Driver Trip Detail' )) ? 'sub_active' : ''; ?>"><i class="fa fa-taxi"></i> Trip Time Variance </a></li>
							</ul>
						</li>
						
						<li class="treeview <?= (isset($script) && ($script == 'Location' ||  $script == 'Restricted Area'))?'active':'';?>"><a href="#" title="" class="expand "><i class="fa fa-header1" style="left:6px;"><img src="images/location-icon.png" alt="" /></i><span> Manage Locations</span></a>
	                        <ul class="treeview-menu menu_drop_down">
	                            <li><a href="location.php" class="<?= (isset($script) && ($script == 'Location' )) ? 'sub_active' : ''; ?>"><i class="fa fa-map-marker"></i> Geo Fence Location</a></li>
	                            <li class="<?= (isset($script) && $script == 'Restricted Area') ? 'sub_active' : ''; ?>"><a href="restricted_area.php"><i class="fa fa-map-signs" aria-hidden="true"></i>Restricted Area</a></li>
	                        </ul>
               			 </li>


						<li class="treeview <?= (isset($script) && 
							($script == 'General' || 
							$script == 'email_templates' || 
							$script == 'Document Master' || 
							$script == 'Currency' || 
							$script == 'sms_templates' || 
							$script == 'seo_setting' || 
							$script == 'language_label' || 
							$script == 'language_label_other'
							
							)) ? 'active' : ''; ?>"><a href="#" title="" class="expand"><i class="icon-cogs1" style="margin:2px 0 0; left:9px;"><img src="images/settings-icon.png" alt="" /></i> <span>Settings</span> </a>
							<ul class="treeview-menu menu_drop_down">
								<li class=""><a href="general.php" class="<?= (isset($script) && ($script == 'General' )) ? 'sub_active' : ''; ?>"><i class="fa-cogs fa"></i> General </a></li>
								<li class=""><a href="email_template.php" class="<?= (isset($script) && ($script == 'email_templates' )) ? 'sub_active' : ''; ?>"><i class="fa fa-envelope"></i> Email Templates </a></li>
								<li class=""><a href="sms_template.php" class="<?= (isset($script) && ($script == 'sms_templates' )) ? 'sub_active' : ''; ?>"><i class="fa fa-comment"></i> SMS Templates </a></li>
								<li><a href="document_master_list.php" class="<?= (isset($script) && ($script == 'Document Master' )) ? 'sub_active' : ''; ?>"><i class="fa fa-file-text"></i>Manage Documents</a></li>	
								<?php  if($SITE_VERSION == "v5"){ ?>
								<li><a href="languages.php" class="<?= (isset($script) && ($script == 'language_label' )) ? 'sub_active' : ''; ?>"><i class="fa fa-language"></i>Language Label</a></li>	
								<?php  } else { ?>
								<li class="treeview <?= (isset($script) && ($script == 'language_label' || $script == 'language_label_other')) ? 'active' : '';?>"><a href="#" title="" ><i class="fa fa-language"></i> Language Label</a>
									<ul class="treeview-menu menu_drop_down">
										<li><a href="languages.php" class="<?= (isset($script) && ($script == 'language_label' )) ? 'sub_active' : ''; ?>"><i class="fa fa-dot-circle-o"></i> General Label </a></li>
										<li><a href="languages_admin.php" class="<?= (isset($script) && ($script == 'language_label_other' )) ? 'sub_active' : ''; ?>"><i class="fa fa-dot-circle-o"></i> Other Label </a></li>
									</ul>
								</li>
								<?php  } ?>
								<li><a href="currency.php" class="<?= (isset($script) && ($script == 'Currency' )) ? 'sub_active' : ''; ?>"><i class="fa fa-usd"></i> Currency</a></li>
								<li><a href="seo_setting.php" class="<?= (isset($script) && ($script == 'seo_setting' )) ? 'sub_active' : ''; ?>"><i class="fa fa-info"></i> SEO Settings</a></li>
							</ul>
						</li>
						<li class="treeview <?= (isset($script) && 
							($script == 'Make' ||
							//$script == 'location' ||
							$script == 'Model' || 
							$script == 'state' || 
							$script == 'city' || 
							$script == 'country' || 
							$script == 'page' ||
							$script == 'home_content' ||
							$script == 'Faq' || 
							$script == 'faq_categories'||
							$script == 'help_detail' || 
							$script == 'help_detail_categories'||
							$script == 'home_driver' || 
							$script == 'Push Notification' || 
							$script == 'Back-up'
							)) ? 'active' : ''; ?>"><a href="#" title="" class="expand"><i class="fa fa-wrench" style="margin:2px 0 0;"></i> <span>Utility</span> </a>
							<ul class="treeview-menu menu_drop_down">
								<li class="treeview <?= (isset($script) && ($script == 'country' || $script == 'city' || $script == 'state')) ? 'active' : '';?>"><a href="#" title="" ><i class="fa fa-globe"></i> Localization</a>
									<ul class="treeview-menu menu_drop_down">
										<li><a href="country.php" class="<?= (isset($script) && ($script == 'country' )) ? 'sub_active' : ''; ?>"><i class="fa fa-dot-circle-o"></i> Country</a></li>
										<li><a href="state.php" class="<?= (isset($script) && ($script == 'state' )) ? 'sub_active' : ''; ?>"><i class="fa fa-dot-circle-o"></i> State</a></li>
										<li><a href="city.php" class="<?= (isset($script) && ($script == 'city' )) ? 'sub_active' : ''; ?>"><i class="fa fa-dot-circle-o"></i> City</a></li>
									</ul>
								</li>
								<!-- <li><a href="location.php" class="<?= (isset($script) && ($script == 'location' )) ? 'sub_active' : ''; ?>"><i class="fa fa-map-marker"></i> Geo Fence Location</a></li> -->
								
								<li><a href="page.php" class="<?= (isset($script) && ($script == 'page' )) ? 'sub_active' : ''; ?>"><i class="fa fa-file-text-o"></i> Pages</a></li>
								<li><a href="home_content.php" class="<?= (isset($script) && ($script == 'home_content' )) ? 'sub_active' : ''; ?>"><i class="fa fa-file-text-o"></i> Edit Home Page</a></li>
								<?php  if($APP_TYPE != 'UberX'){ ?>
									<li><a href="make.php" class="<?= (isset($script) && ($script == 'Make' )) ? 'sub_active' : ''; ?>"><i class="fa fa-car"></i> <?php  echo $langage_lbl_admin['LBL_CAR_MAKE_ADMIN'];?> </a></li>
									<li><a href="model.php" class="<?= (isset($script) && ($script == 'Model' )) ? 'sub_active' : ''; ?>"><i class="fa fa-taxi"></i> <?php  echo $langage_lbl_admin['LBL_CAR_MODEL_ADMIN'];?>  </a></li>
								<?php  } ?>
								<li><a href="faq.php" class="<?= (isset($script) && ($script == 'Faq' )) ? 'sub_active' : ''; ?>"><i class="fa fa-question"></i> Faq</a></li>
								<li><a href="faq_categories.php" class="<?= (isset($script) && ($script == 'faq_categories' )) ? 'sub_active' : ''; ?>"><i class="fa fa-question-circle-o"></i> Faq Categories</a></li>

								<li><a href="help_detail.php" class="<?= (isset($script) && ($script == 'help_detail' )) ? 'sub_active' : ''; ?>"><i class="fa fa-question"></i>Help Topics</a></li>
								<li><a href="help_detail_categories.php" class="<?= (isset($script) && ($script == 'help_detail_categories' )) ? 'sub_active' : ''; ?>"><i class="fa fa-question-circle-o"></i>Help Topic Categories</a></li>

								<li><a href="home_driver.php" class="<?= (isset($script) && ($script == 'home_driver' )) ? 'sub_active' : ''; ?>"><i class="fa fa-users"></i> Our Drivers</a></li>
								<li><a href="send_notifications.php" class="<?= (isset($script) && ($script == 'Push Notification' )) ? 'sub_active' : ''; ?>"><i class="fa fa-globe"></i> Send Push-Notification</a></li>
								<li><a href="backup.php" class="<?= (isset($script) && ($script == 'Back-up' )) ? 'sub_active' : ''; ?>"><i class="fa fa-database"></i> DB Backup</a></li>
							</ul>
						</li>
						
					<?php  } ?>
					<li><a href="logout.php"><i class="icon-signin1" style="margin:2px 0 0;"><img src="images/logout-icon.png" alt="" /></i><span>Logout</span></a> </li>
				</ul>
				<!-- /main navigation -->
			</div>
			<!-- /sidebar -->
		</section>
	<!--END MENU SECTION -->	