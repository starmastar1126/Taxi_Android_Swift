<?php 
   require_once('config.php');       
  require_once('Stripe.php');
  //Stripe::setApiKey("sk_test_S9nJKYA1qzl6LzKuFoSNhzc1");
  
  use Stripe\Customer;
  use Stripe\Stripe;
  $token = "tok_19ta8iHN4z9AQID9lSBNOtrZ";
  //$email  = $_POST['stripeEmail'];
  $email  = "demo1a@gmail.com";
  
  Stripe::setApiKey($stripe['secret_key']);
try{ 
  $customer = Customer::create(array(
              "description" => $email,
              "source" => $token // obtained with Stripe.js
            ));
            
  echo "id".$id = $customer['id'];
  echo "<pre>";print_r($customer);exit;            
}catch(Exception $e){
echo "<pre>"; print_r($e); exit;
}
  
  echo "id".$id = $customer['id'];
  echo "<pre>";print_r($customer);exit;
  /*$details = json_decode($customer);
  $array = get_object_vars($details);
   
  echo $array[status]; echo "<br><br>";
  echo "<pre>"; print_r($array); exit;
*/
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