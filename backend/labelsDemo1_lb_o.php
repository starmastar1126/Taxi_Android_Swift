<?php 
	include_once('include_taxi_webservices.php');
	include_once(TPATH_CLASS.'configuration.php');
	
	require_once('assets/libraries/stripe/config.php');
	require_once('assets/libraries/stripe/stripe-php-2.1.4/lib/Stripe.php');
	require_once('assets/libraries/pubnub/autoloader.php');
	include_once(TPATH_CLASS .'Imagecrop.class.php');
	include_once(TPATH_CLASS .'twilio/Services/Twilio.php');
	include_once('generalFunctions.php');
	include_once('send_invoice_receipt.php');

$dataLblArr=array();





$dataLblArr['HOME_FIRST_LBL']='Make your day';
$dataLblArr['LBL_HOME_RIDING_TXT']='Start riding
with Projectname
Now';
$dataLblArr['LBL_HOME_DRIVER_COMPANY_TXT']='Driver? Or a company? What are you waiting for? Just sign up.';
$dataLblArr['LBL_SIGN_IN_DRIVER']='Driver';
$dataLblArr['LBL_RIDER']='Rider';
$dataLblArr['LBL_DRIVER_SIGNIN']='Driver Sign In';
$dataLblArr['LBL_RIDER_SIGNIN']='Rider sign in';
$dataLblArr['LBL_HEADER_TOPBAR_TRIPS']='My Trips';
$dataLblArr['LBL_SEARCH_RIDES_POSTED_BY_DATE']='Search rides by date...';
$dataLblArr['LBL_SEARCH_RIDES_POSTED_BY_TIME_PERIOD']='Search Rides by Time period...';
$dataLblArr['LBL_RIDE_NO']='Ride No.';
$dataLblArr['LBL_DRIVER_TRIP_RIDER']='Rider';
$dataLblArr['LBL_DRIVER_TRIP_Trip_Date']='Trip Date';
$dataLblArr['LBL_DRIVER_TRIP_Car_Type']='Car Type';
$dataLblArr['LBL_RECENT_RIDE']='Recent Rides';
$dataLblArr['LBL_PAYMENT_REQ']='Payment Request';
$dataLblArr['LBL_RECENT_RIDE']='Recent Rides';
$dataLblArr['LBL_PAID_TRIP']='Paid Trips';
$dataLblArr['LBL_TRIP_TXT_ADMIN']='Trip';
$dataLblArr['LBL_TRIP_NO']='Trip No.';
$dataLblArr['LBL_RIDE_NO_ADMIN']='Ride No';
$dataLblArr['LBL_PROGRESS_DRIVER_ADMIN']='Driver';
$dataLblArr['LBL_PROGRESS_RIDER_NAME_ADMIN']='Rider';
$dataLblArr['LBL_DASHBOARD_USERS_ADMIN']='Riders';
$dataLblArr['LBL_DASHBOARD_DRIVERS_ADMIN']='Drivers';
$dataLblArr['LBL_RIDE_STATISTICS_ADMIN']='Ride Statistics';
$dataLblArr['LBL_TOTAL_RIDES_ADMIN']='Total Rides';
$dataLblArr['LBL_ON_RIDES_ADMIN']='On Going Rides';
$dataLblArr['LBL_CANCELLED_RIDES_ADMIN']='Cancelled Rides';
$dataLblArr['LBL_COMPLETED_RIDES_ADMIN']='Completed Rides';
$dataLblArr['LBL_RIDES_NAME_ADMIN']='Rides';
$dataLblArr['LBL_DRIVERS_NAME_ADMIN']='Drivers';
$dataLblArr['LBL_DRIVERS_TXT_ADMIN']='DRIVERS';
$dataLblArr['LBL_DRIVER_TXT_ADMIN']='Driver';
$dataLblArr['LBL_VEHICLE_DRIVER_TXT_ADMIN']='Driver';
$dataLblArr['LBL_CHOOSE_DRIVER_ADMIN']='CHOOSE DRIVER';
$dataLblArr['LBL_RIDER_NAME_TXT_ADMIN']='Rider';
$dataLblArr['LBL_RIDERS_TXT_ADMIN']='RIDERS';
$dataLblArr['LBL_EDIT_RIDERS_TXT_ADMIN']='Riders';
$dataLblArr['LBL_TEXI_ADMIN']='Taxi';
$dataLblArr['LBL_TRIPS_TXT_ADMIN']='Trips';
$dataLblArr['LBL_RIDE_TXT_ADMIN']='Ride';
$dataLblArr['LBL_TRIP_DATE_ADMIN']='Trip Date';
$dataLblArr['LBL_CAR_TXT_ADMIN']='Car';
$dataLblArr['LBL_PASSANGER_TXT_ADMIN']='Passenger';
$dataLblArr['LBL_CAR_MAKE_ADMIN']='Car Make';
$dataLblArr['LBL_CAR_MODEL_ADMIN']='Car Model';
$dataLblArr['LBL_TRIP_TYPE_TXT_ADMIN']='Trip Type';
$dataLblArr['LBL_DELIVERY_DETAILS_TXT_ADMIN']='Delivery Details';
$dataLblArr['LBL_VEHICLE_CATEGORY_TXT_ADMIN']='VEHICLE CATEGORY';
$dataLblArr['LBL_USER_PETS_ADMIN']='User Pets';
$dataLblArr['LBL_USER_PETS_TXT_ADMIN']='USER PETS';
$dataLblArr['LBL_PET_TYPE_TXT_ADMIN']='PET TYPE';
$dataLblArr['LBL_PET_TYPE']='Pet Type';
$dataLblArr['LBL_RIDE_LATER_BOOKINGS_ADMIN']='Ride Later Bookings';
$dataLblArr['LBL_RIDE_NUMBER_TXT_ADMIN']='RIDE NUMBER';
$dataLblArr['LBL_THANKS_FOR_CHOOSING_TXT_ADMIN']='Thanks for choosing';
$dataLblArr['LBL_CAR_ADMIN']='CAR';
$dataLblArr['LBL_KILOMETERS_TXT_ADMIN']='KILOMETERS';
$dataLblArr['LBL_TRIP_TIME_TXT_ADMIN']='TRIP TIME';
$dataLblArr['LBL_DELIVERY_DETAILS_ADMIN']='DELIVERY DETAILS';
$dataLblArr['LBL_VEHICLE_CATEGORY_ADMIN']='Vehicle Category';
$dataLblArr['LBL_DESCRIPTION_TXT_ADMIN']='Description';
$dataLblArr['LBL_BREED_TXT_ADMIN']='Breed';
$dataLblArr['LBL_WEIGHT_TXT_ADMIN']='Weight';
$dataLblArr['LBL_TITLE_TXT_ADMIN']='Title';
$dataLblArr['LBL_TRIP_FINISH']='Your Trip is finished';
$dataLblArr['LBL_TRIP_CANCEL_BY_DRIVER']='Your trip is cancelled by driver.';
$dataLblArr['LBL_DRIVER_ARRIVING']='Driver is arriving';
$dataLblArr['LBL_TRIP_USER_WAITING']='Passenger is waiting for you';
$dataLblArr['LBL_YOUR_TRIP_START']='Your trip is started';
$dataLblArr['LBL_NOT_CLOSE_APP']='Please do not close app.';
$dataLblArr['LBL_VEHICLE_TXT_ADMIN']='Driver Taxis';
$dataLblArr['LBL_VEHICLE_CAPITAL_TXT_ADMIN']='TAXIS';
$dataLblArr['LBL_DRIVER_NAME_ADMIN']='DRIVER';
$dataLblArr['LBL_VEHICLE_TYPE_SMALL_TXT']='Vehicle Type';
$dataLblArr['LBL_VEHICLE_TYPE_TXT']='VEHICLE TYPE';
$dataLblArr['LBL_SERVICE_BEFORE_TXT_ADMIN']='Service Before';
$dataLblArr['LBL_SERVICE_AFTER_TXT_ADMIN']='Service After';
$dataLblArr['LBL_FIXED_FARE_TXT_ADMIN']='Fixed Fare';
$dataLblArr['LBL_PRICE_KM_TXT_ADMIN']='Price/Km';
$dataLblArr['LBL_PRICE_MIN_TXT_ADMIN']='Price Per Min';
$dataLblArr['LBL_FARE_TYPE_TXT_ADMIN']='Fare Type';
$dataLblArr['LBL_ASSIGN_JOB_MANUALLY_TXT']='Assign Jobs manually';
$dataLblArr['WASHING_SERVICE_TYPES_TXT']='Washing Service Types';
$dataLblArr['LBL_MANUAL_TAXI_DISPATCH']='Manual Taxi Dispatch';
$dataLblArr['LBL_MY_AVAILABILITY']='My Availability';

foreach($dataLblArr as $key => $value)
{
	
	$sql = "SELECT * FROM `language_label` WHERE  vLabel='".$key."'";
	$data = $obj->MySQLSelect($sql);
	
	if(count($data) < 1){
		$sql_other = "SELECT * FROM `language_label_other` WHERE  vLabel='".$key."'";
		$data_other = $obj->MySQLSelect($sql_other);
		
		if(count($data_other) < 1){
			$sql_code = "SELECT * FROM `language_master`";
			$data_code = $obj->MySQLSelect($sql_code);
			echo $key."<BR/>";
			
			for($i=0;$i<count($data_code);$i++){
				$vCode = $data_code[$i]['vCode'];
				
				$LangData['vCode'] = $vCode;
				$LangData['vLabel']=$key;
				$LangData['vValue']=$value;
				$LangData['lPage_id']="0";
				// echo "<pre>";print_r($LangData);exit;
				$obj->MySQLQueryPerform("language_label_other",$LangData,'insert');
			}
		}
	}
}
?>