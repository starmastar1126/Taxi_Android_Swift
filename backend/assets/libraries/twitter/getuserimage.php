<?php 
ini_set('display_errors', 0);
require_once('TwitterAPIExchange.php');

/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
$settings = array(
    'oauth_access_token' => "875667835535499265-NpMDvzyWhMsNLHgefII03myBvqlOpvG",
    'oauth_access_token_secret' => "FzDnbplhDc6a4tZULmzbkbnuqMAzPeBZy66xE6vmAa0Ln",
    'consumer_key' => "xxRy20BWLBgTrQZKiW410hxPQ",
    'consumer_secret' => "7UEfFYAmMWhrK84ptZzU2dACvroC7CmD64omyz1m0n8FD4vqDt"
);

// Chooose the url you want from the docs, this is the users/show
$url = 'https://api.twitter.com/1.1/users/show.json';
$requestMethod = 'GET';

/** POST fields required by the URL above. See relevant docs as above **/
$postfields = array(
    'user_id' => '424303272' 
);

/** Perform a POST request and echo the response **/
$twitter = new TwitterAPIExchange($settings);
$twitter->buildOauth($url, $requestMethod)
             ->setGetfield($postfields)
             ->performRequest();

/** Perform a GET request and echo the response **/
/** Note: Set the GET field BEFORE calling buildOauth(); **/
$url = 'https://api.twitter.com/1.1/users/show.json';
$getfield = '?user_id=874205811936854016';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$twitterArr = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
$jsondata = json_decode($twitterArr); //echo "<pre>";print_r($jsondata);exit;   
$profile_image_url = $jsondata->profile_image_url;           
             
?>