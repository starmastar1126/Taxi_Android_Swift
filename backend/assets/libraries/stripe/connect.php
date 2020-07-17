<?php  
require_once('./config/config.php');

// initialize a new generic provider
$provider = new \League\OAuth2\Client\Provider\GenericProvider([
    'clientId'                => CLIENT_ID,
    'clientSecret'            => SECRET_KEY,
    'redirectUri'             => REDIRECT_URL,
    'urlAuthorize'            => 'https://connect.stripe.com/oauth/authorize',
    'urlAccessToken'          => 'https://connect.stripe.com/oauth/token',
    'urlResourceOwnerDetails' => 'https://api.stripe.com/v1/account'
]);

// Check for an authorization code
if (isset($_GET['code'])){
  $code = $_GET['code'];

  // Try to retrieve the access token
  try {
    $accessToken = $provider->getAccessToken('authorization_code', 
      ['code' => $_GET['code']
    ]);

    // You could retrieve the API key with `$accessToken->getToken()`, but it's better to authenticate using the Stripe-account header (below)

    // Retrieve the account ID to be used for authentication: https://stripe.com/docs/connect/authentication
    // TODO: Save this account ID to your database for later use.
    $account_id = $provider->getResourceOwner($accessToken)->getId();
		
    // Retrieve the account from Stripe: https://stripe.com/docs/api/php#retrieve_account
    $account = \Stripe\Account::Retrieve($account_id);

    $success = "Your Stripe account has been connected!";
  }
  catch (Exception $e){
    $error = $e->getMessage();
  }
}
// Handle errors
elseif (isset($_GET['error'])){
  $error = $_GET['error_description'];
}
// No authorization code -- display an error, etc. 
else {
  $error = "No authorization code received";
}

?>