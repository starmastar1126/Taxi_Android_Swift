<?php                      
  require_once('config.php');       
  require_once('Stripe.php');
  //Stripe::setApiKey("sk_test_S9nJKYA1qzl6LzKuFoSNhzc1");
  
  use Stripe\Account;
  use Stripe\Stripe;
  
  Stripe::setApiKey($stripe['secret_key']);

  // Retrieve the request's body and parse it as JSON
$input = @file_get_contents("php://input");
$event_json = json_decode($input);

// Do something with $event_json

http_response_code(200); // PHP 5.4 or greater

?>