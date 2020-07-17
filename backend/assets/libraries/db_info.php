<?php 

//date_default_timezone_set('Asia/Calcutta'); 

//ini_set('display_errors',1);

//ini_set('display_startup_errors',1);

//error_reporting(-1);

// error_reporting(E_ALL);  

// ini_set('display_errors','1');

// Code for Local Server Only

ini_set('memory_limit', '1024M');

$hst_arr = explode("/",$_SERVER["REQUEST_URI"]);

$hst_var = $hst_arr[1];





// if($UpdateDatabase == 'yes')

// {

// 	define( 'TSITE_SERVER',$hostName);

// 	define( 'TSITE_DB',$databaseName);

// 	define( 'TSITE_USERNAME',$userName);

// 	define( 'TSITE_PASS',$passwordName);

// }

// else

// {



	if($_SERVER["HTTP_HOST"] == "192.168.0.75")

	{

		define( 'TSITE_SERVER','localhost');

		define( 'TSITE_DB','fastca14_fastcab');

		define( 'TSITE_USERNAME','fastca14_fastcab');

		define( 'TSITE_PASS','DhN2g]cd0n#3');

	}

	else

	{

		define( 'TSITE_SERVER','localhost');

		define( 'TSITE_DB','fastca14_fastcab');

		define( 'TSITE_USERNAME','fastca14_fastcab');

		define( 'TSITE_PASS','DhN2g]cd0n#3');

	}

	

// }





/* function get_langcode($lang) {

	$sql = mysqli_query("SELECT vGMapLangCode FROM language_master WHERE vCode = '".$lang."'");

	$result = mysqli_fetch_object($sql);

	return $result->vGMapLangCode;

} */





?>