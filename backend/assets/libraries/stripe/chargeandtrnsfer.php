<?php 
  require_once('config.php');
  require_once('stripe-php-2.1.4/lib/Stripe.php');
  //Stripe::setApiKey("sk_test_S9nJKYA1qzl6LzKuFoSNhzc1");
    
// Token is created using Stripe.js or Checkout!
// Get the payment token submitted by the form:
//$token = $_POST['stripeToken'];
$token = "cus_9KFecdiGmmSSq5";

try{
// Create a Charge:
$charge = Stripe_Charge::create(array(
  "amount" => 5000,
  "currency" => "usd",
  "customer" => $token,
  "description" => "Order10",
));
}catch(Exception $e){
echo "<pre>"; print_r($e); exit;
}

// Create a Transfer to a connected account (later):
/*$transfer = Stripe_Transfer::create(array(
  "amount" => 3743,
  "currency" => "usd",
  "destination" => "cus_ADdDfeTbmzZR79",
  "transfer_group" => "Order10",
));   */


 $details = json_decode($charge);
  $array = get_object_vars($details);
   
  $array[status]; echo "<br><br>";
  echo "<pre>"; print_r($array); exit;
  
$token = $array['balance_transaction'];

try{
// Create a Charge:
$charge1 = Stripe_Charge::create(array(
  "amount" => 1000,
  "currency" => "usd",
  "source" => $token,
), array("stripe_account" => "cus_9KFecdiGmmSSq5"));
}catch(Exception $e){
echo "<pre>"; print_r($e); exit;
}

$details1 = json_decode($charge1);
  $array1 = get_object_vars($details1);
   
  echo $array1[status]; echo "<br><br>";
  echo "<pre>"; print_r($array1); exit;
?>