<?php  
	

$to = "nimit.esw@gmail.com";
$subject = "Email script from domain.com  - ".date('Y-m-d H:i:s');	

$message = "just checking email";
//$message .= $message;

$header = "From:no-reply@domain.com \r\n";
$header .= "MIME-Version: 1.0\r\n";
$header .= "Content-type: text/html\r\n";

$emailsend = mail ($to,$subject,$message,$header);


if($emailsend){
	echo 'send';
}else{
	
	print_r(error_get_last());

}



?>