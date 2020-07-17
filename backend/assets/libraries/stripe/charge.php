<?php 
  require_once('config.php');
  require_once('stripe-php-2.1.4/lib/Stripe.php');
  //Stripe::setApiKey("sk_test_S9nJKYA1qzl6LzKuFoSNhzc1");
    
  $token  = $_POST['stripeToken'];
  $email  = $_POST['stripeEmail'];
 # echo "<pre>"; print_r($_POST); exit;
  $customer = Stripe_Customer::create(array(
      'email' => $email,
      'card'  => $token
  ));

  $charge = Stripe_Charge::create(array(
      'customer' => $customer->id,
      'amount'   => 5000,
      'currency' => 'usd',
      'description' => 'Widget, Qty 1'
  ));
  $details = json_decode($charge);
  $array = get_object_vars($details);
   
  echo $array[status]; echo "<br><br>";
  echo "<pre>"; print_r($array); exit;

  echo '<h1>Successfully charged $50.00!</h1>';
?>