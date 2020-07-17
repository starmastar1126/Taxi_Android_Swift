<?php 

// See full code example here: https://gist.github.com/3507366
//$_GET['code'] = "ac_ADfzzebd1ck8bsTcIRA2JSDcHbSyUXUf";
if (isset($_GET['code'])) { // Redirect w/ code
  $code = $_GET['code'];

  $token_request_body = array(
    'grant_type' => 'authorization_code',
    'client_id' => 'ca_ADeTFiGCUrgBfvM0p65nnmboNZU1CewD',
    'code' => $code,
    'client_secret' => 'sk_test_xGFa5eB2dkX29R8DIaHwCdLb'
  );

  $req = curl_init(TOKEN_URI);
  curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($req, CURLOPT_POST, true );
  curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));

  // TODO: Additional error handling
  $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
  $resp = json_decode(curl_exec($req), true);  echo "<pre>";print_r($resp);exit;
  curl_close($req);

  echo $resp['access_token'];
} else if (isset($_GET['error'])) { // Error
  echo $_GET['error_description'];
} else { // Show OAuth link
  $authorize_request_body = array(
    'response_type' => 'code',
    'scope' => 'read_write',
    'client_id' => 'ca_ADeTFiGCUrgBfvM0p65nnmboNZU1CewD'
  );

  $url = AUTHORIZE_URI . '?' . http_build_query($authorize_request_body);
  echo "<a href='$url'>Connect with Stripe</a>";
}


?>