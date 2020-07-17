<?php 
  require_once('config.php');
  require_once('stripe-php-2.1.4/lib/Stripe.php');
  //Stripe::setApiKey("sk_test_S9nJKYA1qzl6LzKuFoSNhzc1");
    
 
 # echo "<pre>"; print_r($_POST); exit;
 
 
$token = Stripe_Token::create(
                    array(
                            "card" => array(
                            "name" => "Phil",
                             "number" => "4242424242424242",
                              "exp_month" => 11,
                              "exp_year" => 2019,
                              "cvc" => "314"
                        )
                    )
                );
              
  $details = json_decode($token);
  $array = get_object_vars($details);
   
  //echo $array[status]; echo "<br><br>";
  echo "<pre>"; print_r($array); exit;

  echo '<h1>Successfully charged $50.00!</h1>';
?>