<?php                      
  require_once('config.php');       
  require_once('Stripe.php');
  //Stripe::setApiKey("sk_test_S9nJKYA1qzl6LzKuFoSNhzc1");
  
  use Stripe\Account;
  use Stripe\Stripe;
  
  Stripe::setApiKey($stripe['secret_key']);
try{ 
  $account = Account::retrieve("acct_19tZHJKoJeVOF6nY");
  $account->delete();
  //echo "id".$id = $customer['id'];
  echo "<pre>";print_r($account);exit;
  //$details = json_decode($customer);
  //$array = get_object_vars($details);
   
  //echo $array[status]; echo "<br><br>";
  //echo "<pre>"; print_r($array); exit;
}catch(Exception $e){
echo "<pre>"; print_r($e); exit;
}
  echo "<pre>";print_r($account);exit;
  /*$details = json_decode($customer);
  $array = get_object_vars($details);
   
  echo $array[status]; echo "<br><br>";
  echo "<pre>"; print_r($array); exit; */

?>