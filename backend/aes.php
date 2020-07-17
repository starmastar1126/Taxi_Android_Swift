<?php 
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC);
	$iv = "1234123412341234";
	$key = "simplekey";
	$text = "this is my plain text";

	$crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, hash( 'sha256', $key ), $text, MCRYPT_MODE_CBC, $iv);

	echo $crypttext = base64_encode($crypttext);
	$key= base64_encode($key);
	$iv = base64_encode($iv);
	
	// echo "sd==". $data = openssl_encrypt($text, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

?>