<?php 
	require_once('config.php');       
	require_once('Stripe.php');
	//Stripe::setApiKey("sk_test_S9nJKYA1qzl6LzKuFoSNhzc1");
	
	use Stripe\Transfer;
	use Stripe\Charge;
	use Stripe\Stripe;
	$amount = 3000;
	$currency = "usd"; 
	$vStripeCusId = "cus_AE2D6MfpqKr1Te"; // demo1a@gmail.com 
	//$email  = $_POST['stripeEmail'];
	$email  = "demo1a@gmail.com";
	
	Stripe::setApiKey($stripe['secret_key']);
	try{ 
		
		$charge = Charge::create(array(
		"amount" => $amount,
		"currency" => $currency,
		"customer" => $vStripeCusId,
		"description" => "Charge for demo1a@gmail.com"
		));         
		
		//echo "status - ".$charge['status'];             
		//echo "id".$id = $charge['id'];
		//echo "<pre>";print_r($charge);exit;
		
		// Transfer to connect account - https://stripe.com/docs/connect/charges-transfers
		// Create a Transfer to the connected account (later):
		$transfer = Transfer::create(array(
		"amount" => 1500,
		"currency" => $currency,                     
		"destination" => "acct_19tb7XCKfJBypbhH",
		"description" => "Transfer for joshidarshil382@gmail.com"             
		));
		// Transfer to connect account - https://stripe.com/docs/connect/charges-transfers
		echo "id".$id = $transfer['id'];
		echo "<pre>";print_r($transfer);exit;            
		}catch(Exception $e){
		echo "<pre>"; print_r($e); exit;
	}
	
	echo "id".$id = $charge['id'];
	echo "<pre>";print_r($charge);exit;
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