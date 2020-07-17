<?php 
include_once('common.php');
chkServer('gateway.sandbox.push.apple.com',2195); 
//chkServer('gateway.push.apple.com',2195); 

function chkServer($host, $port) 
{ 
$hostip = @gethostbyname($host); 

if ($hostip == $host) 
{ 
echo "Server is down or does not exist"; 
} 
else 
{ 
if (!$x = @fsockopen($hostip, $port, $errno, $errstr, 5)) 
{ 
echo "Port $port is closed."; 
} 
else 
{ 
echo "Port $port is open."; 
if ($x) 
{ 
@fclose($x); 
} 
} 
} 
} 
?>
