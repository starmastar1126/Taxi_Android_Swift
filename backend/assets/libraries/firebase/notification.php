<?php 
//https://github.com/eosobande/php-firebase-class#initialization
include_once('../configuration.php');
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(-1);
require_once ('src/firebase.php');

$FB_API_KEY = "AIzaSyAagUfKvkf0DIPQ6DWiit4rfLDmr7hSK7U";
$APP_NAME = "TaxiRiderV4"
$FB_URL = "https://chat-ebad5.firebaseio.com/";

$fcm = new FCM(FCM::CONTENT_JSON, 60*60*24*5, null);

$token = ['32b1330f09b51ab7fc1f81317c6b98a2b019fa4d781e8c94b0ce7f67eaafb29a'];
$body = "New weather update";
$data = ['temperature'=>'10', 'humidity'=>987];

$notidication = $fcm->notification($token, $body, $data);
echo "<pre>";print_r($notidication);exit;
?>