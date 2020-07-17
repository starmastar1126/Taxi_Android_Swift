<?php 
/**
@author muni
@copyright http:www.smarttutorials.net
 */

require_once 'messages.php';

//site specific configuration declartion
define( 'BASE_PATH', 'http://cubetaxiplus.bbcsproducts.com/appadmin/twitter/twitter/');
define( 'DB_HOST', 'localhost' );
define( 'DB_USERNAME', 'bbcsprod_cubeplu');
define( 'DB_PASSWORD', 'rZWKt*S$U#$F');
define( 'DB_NAME', 'bbcsprod_cubeplu');

//Twitter login
define('TWITTER_CONSUMER_KEY', 'jgJ9OyAZsBU04HekM3tlOe04O');
define('TWITTER_CONSUMER_SECRET', 'qBttUQry3gVctnXpmai0PsijltHk32HVGB5koO45NMLOKWVRBb');
define('TWITTER_OAUTH_CALLBACK', 'http://cubetaxiplus.bbcsproducts.com/appadmin/twitter/twitter/index.php');



function __autoload($class)
{
	$parts = explode('_', $class);
	$path = implode(DIRECTORY_SEPARATOR,$parts);
	require_once $path . '.php';
}
