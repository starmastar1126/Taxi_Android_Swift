<?php 
  require_once('config.php');
  require_once('stripe-php-2.1.4/lib/Stripe.php');
  //Stripe::setApiKey("sk_test_S9nJKYA1qzl6LzKuFoSNhzc1");
    
  //$token  = $_POST['stripeToken'];
  $token = "tok_19tDRRHN4z9AQID98xCnJb9H";
  //$email  = $_POST['stripeEmail'];
  $email  = "kandarp1992@gmail.com";
 # echo "<pre>"; print_r($_POST); exit;
try{ 
  $customer = Stripe_Customer::create(array(
      'email' => $email,
      'card'  => $token
  ),array("stripe_account" => "ca_ADeTFiGCUrgBfvM0p65nnmboNZU1CewD"));
}catch(Exception $e){
echo "<pre>"; print_r($e); exit;
}
  
  $details = json_decode($customer);
  $array = get_object_vars($details);
   
  echo $array[status]; echo "<br><br>";
  echo "<pre>"; print_r($array); exit;

 /* $charge = Stripe_Charge::create(array(
      'customer' => $customer->id,
      'amount'   => 5000,
      'currency' => 'usd',
      'description' => 'Widget, Qty 1'
  ));
  $details = json_decode($charge);
  $array = get_object_vars($details);
   
  echo $array[status]; echo "<br><br>";
  echo "<pre>"; print_r($array); exit;

  echo '<h1>Successfully charged $50.00!</h1>';   */
?>