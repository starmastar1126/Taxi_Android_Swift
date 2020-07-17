<?php 
 
   require_once('config.php');       
  require_once('Stripe.php');
  //Stripe::setApiKey("sk_test_S9nJKYA1qzl6LzKuFoSNhzc1");
  
  use Stripe\Token;
  use Stripe\Stripe;
  
  Stripe::setApiKey($stripe['secret_key']);
$token = Token::create(
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
              
  //$details = json_decode($token);
  //$array = get_object_vars($details);
  echo $tokenid = $token["id"];
  //echo $array[status]; echo "<br><br>";
  echo "<pre>"; print_r($token); exit;

  
?>