<?php 
 
require_once('stripe-php-2.1.4/lib/Stripe.php');

/* for test account */

$stripe = array(
  "secret_key"      => "sk_test_xGFa5eB2dkX29R8DIaHwCdLb",
  "publishable_key" => "pk_test_TJeNhmUQgsrhU0mGDztG9q6r"
);
/* for live account */
/*
$stripe = array(
  "secret_key"      => "sk_live_CeUc7N3M08hz7RoR1J92PkKM",
  "publishable_key" => "pk_live_txM7ge6T6NuaHjCM3LDHBACv"
); */

Stripe::setApiKey($stripe['secret_key']);
?>