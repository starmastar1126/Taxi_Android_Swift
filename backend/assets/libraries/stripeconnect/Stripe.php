<?php 

// Tested on PHP 5.2, 5.3

// This snippet (and some of the curl code) due to the Facebook SDK.

    


if (!function_exists('curl_init')) {
  throw new Exception('Stripe needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Stripe needs the JSON PHP extension.');
}
if (!function_exists('mb_detect_encoding')) {
  throw new Exception('Stripe needs the Multibyte String PHP extension.');
}
       
// Stripe singleton
require_once(dirname(__FILE__) . '/Stripe/Stripe.php');   

//HttpClient
require_once(dirname(__FILE__) . '/Stripe/HttpClient/ClientInterface.php');
require_once(dirname(__FILE__) . '/Stripe/HttpClient/CurlClient.php');

// Utilities
require_once(dirname(__FILE__) . '/Stripe/Util/RequestOptions.php');
require_once(dirname(__FILE__) . '/Stripe/Util/Util.php');
require_once(dirname(__FILE__) . '/Stripe/Util/Set.php');

//Errors
require_once(dirname(__FILE__) . '/Stripe/Error/Base.php');
require_once(dirname(__FILE__) . '/Stripe/Error/InvalidRequest.php');
require_once(dirname(__FILE__) . '/Stripe/Error/Authentication.php');
//require_once(dirname(__FILE__) . '/Stripe/Error.php');
//require_once(dirname(__FILE__) . '/Stripe/ApiError.php');
//require_once(dirname(__FILE__) . '/Stripe/ApiConnectionError.php');
//require_once(dirname(__FILE__) . '/Stripe/AuthenticationError.php');
//require_once(dirname(__FILE__) . '/Stripe/CardError.php');
//require_once(dirname(__FILE__) . '/Stripe/InvalidRequestError.php');
//require_once(dirname(__FILE__) . '/Stripe/RateLimitError.php');

// Plumbing
//require_once(dirname(__FILE__) . '/Stripe/Object.php');
require_once(dirname(__FILE__) . '/Stripe/JsonSerializable.php');
require_once(dirname(__FILE__) . '/Stripe/StripeObject.php');
require_once(dirname(__FILE__) . '/Stripe/ApiResponse.php');
require_once(dirname(__FILE__) . '/Stripe/ApiRequestor.php');
require_once(dirname(__FILE__) . '/Stripe/ApiResource.php');    
require_once(dirname(__FILE__) . '/Stripe/SingletonApiResource.php');
require_once(dirname(__FILE__) . '/Stripe/AttachedObject.php');    
require_once(dirname(__FILE__) . '/Stripe/Collection.php');
//require_once(dirname(__FILE__) . '/Stripe/List.php');

// Stripe API Resources
require_once(dirname(__FILE__) . '/Stripe/Account.php');    
require_once(dirname(__FILE__) . '/Stripe/ExternalAccount.php');
require_once(dirname(__FILE__) . '/Stripe/Card.php');     
require_once(dirname(__FILE__) . '/Stripe/Balance.php');      
require_once(dirname(__FILE__) . '/Stripe/BalanceTransaction.php');
require_once(dirname(__FILE__) . '/Stripe/Charge.php');          
require_once(dirname(__FILE__) . '/Stripe/Customer.php');     
require_once(dirname(__FILE__) . '/Stripe/Invoice.php');
require_once(dirname(__FILE__) . '/Stripe/InvoiceItem.php');
require_once(dirname(__FILE__) . '/Stripe/Plan.php');
require_once(dirname(__FILE__) . '/Stripe/Subscription.php');
require_once(dirname(__FILE__) . '/Stripe/Token.php');
require_once(dirname(__FILE__) . '/Stripe/Coupon.php');
require_once(dirname(__FILE__) . '/Stripe/Event.php');
require_once(dirname(__FILE__) . '/Stripe/Transfer.php');
require_once(dirname(__FILE__) . '/Stripe/Recipient.php');
require_once(dirname(__FILE__) . '/Stripe/ApplicationFee.php');  
