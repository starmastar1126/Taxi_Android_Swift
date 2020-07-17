<?php 
include_once('../../../include_taxi_webservices.php');
include_once('../configuration.php');
require_once('stripe-php-2.1.4/lib/Stripe.php');
                 
//$STRIPE_PUBLISH_KEY = $generalobj->getConfigurations("configurations","STRIPE_PUBLISH_KEY");
//$STRIPE_SECRET_KEY = $generalobj->getConfigurations("configurations","STRIPE_SECRET_KEY");
$stripe = array();
//array_push($stripe,$STRIPE_SECRET_KEY);


/* for test account */

$stripe = array(
  "secret_key"      => $STRIPE_SECRET_KEY,
  "publishable_key" => $STRIPE_PUBLISH_KEY
); 
/* for live account */
/*
$stripe = array(
  "secret_key"      => "sk_live_CeUc7N3M08hz7RoR1J92PkKM",
  "publishable_key" => "pk_live_txM7ge6T6NuaHjCM3LDHBACv"
); */

Stripe::setApiKey($stripe['secret_key']);
?>