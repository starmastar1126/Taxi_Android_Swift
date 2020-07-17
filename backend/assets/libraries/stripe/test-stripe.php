<?php 
	require_once('config.php');
	require_once('stripe-php-2.1.4/lib/Stripe.php'); 
	//Stripe::setApiKey("sk_test_S9nJKYA1qzl6LzKuFoSNhzc1");
	
	$token = "cus_ADf45fwW6GmXPQ"; 
	
	try
	{
		// Create a Charge:
		$charge = Stripe_Account::retrieve(array(
		"country" => "US",
		"managed" => true
		));
		
		
	}
	catch(Exception $e)
	{
		echo "<pre>"; print_r($e); exit;
	}
	
	echo '<pre>'; print_R((array)$charge); echo '</pre>';
		exit;
		
	/*
		try{
		// Create a Charge:
		$charge = Stripe_Charge::create(array(
		"amount" => 25000,
		"currency" => "usd",
		"customer" => "cus_ADf45fwW6GmXPQ",
		"description" => "test",
		"application_fee" => 10000,
		"destination" => "cus_ADf4dabxYvQX7n"
		));
		}catch(Exception $e){
		echo "<pre>"; print_r($e); exit;
		}
		echo '<pre>'; print_R((array)$charge); echo '</pre>';
		exit;
		
		/* 
		$details = json_decode($charge);
		$array = get_object_vars($details);
		
		$array[status]; echo "<br><br>";
	echo "<pre>"; print_r($array);*/
	
	$token = $array['balance_transaction'];
	
	try{
		// Create a Charge:
		$charge1 = Stripe_Charge::create(array(
		"amount" => 1200,
		"currency" => "usd",
		"source" => $token,
		"application_fee" => "200",
		"destination" => "cus_ADf4dabxYvQX7n"
		), array("stripe_account" => "cus_ADf4dabxYvQX7n"));
		}catch(Exception $e){
		echo "<pre>"; print_r($e); exit;
	}
	
	$details1 = json_decode($charge1);
	$array1 = get_object_vars($details1);
	
	echo $array1[status]; echo "<br><br>";
	echo "<pre>"; print_r($array1); exit;
?>