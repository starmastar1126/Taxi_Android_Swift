<?php 
include_once('common.php');
echo 'PHP version is <b>'. phpversion().'</b>';
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
      echo "<br /><span style='color:#FF0000;text-align:center;'>Port $port is <b>CLOSED.</b></span>"; 
    } 
    else 
    { 
      echo "<br /><span style='color:#32CD32;text-align:center;'>Port $port is <b>OPEN.</b></span>"; 
      if ($x) 
      { 
        @fclose($x); 
      } 
    } 
  } 
} 

 if(extension_loaded('ionCube Loader')) {
  echo "<br /><span style='color:#32CD32;text-align:center;'>ionCube Loader <b>installed</b></span>";
}       
else{
  echo "<br /><span style='color:#FF0000;text-align:center;'>ionCube Loader <b>NOT</b> installed</span>";
}

if(extension_loaded('mbstring')) {
  echo "<br /><span style='color:#32CD32;text-align:center;'>mbstring  <b>installed</b></span>";
}       
else{
  echo "<br /><span style='color:#FF0000;text-align:center;'>mbstring  <b>NOT</b> installed</span>";
}

if(extension_loaded('curl')) {
  echo "<br /><span style='color:#32CD32;text-align:center;'>curl  <b>installed</b></span>";
}       
else{
  echo "<br /><span style='color:#FF0000;text-align:center;'>curl  <b>NOT</b> installed</span>";
}
if(extension_loaded('mysql')) {
  echo "<br /><span style='color:#32CD32;text-align:center;'>mysql <b>installed</b></span>";
}       
else{
  echo "<br /><span style='color:#FF0000;text-align:center;'>mysql <b>NOT</b> installed</span>";
} 
if(extension_loaded('mysqli')) {
  echo "<br /><span style='color:#32CD32;text-align:center;'>mysqli <b>enabled</b></span>";
}       
else{
  echo "<br /><span style='color:#FF0000;text-align:center;'>mysqli <b>NOT</b> installed</span>";
}

if( ini_get('allow_url_fopen') ) {
  echo "<br /><span style='color:#32CD32;text-align:center;'>allow_url_fopen <b>OPEN</b></span>";
} 
else{
 echo "<br /><span style='color:#FF0000;text-align:center;'>allow_url_fopen <b>NOT</b> OPEN</span>";
}
if( ini_get('short_open_tag') ) {
  echo "<br /><span style='color:#32CD32;text-align:center;'>short_open_tag <b>ON</b></span>";
} 
else{
 echo "<br /><span style='color:#FF0000;text-align:center;'>short_open_tag <b>OFF</b></span>";
}


//print_r(get_loaded_extensions());
 //print_r(mysql_get_server_info());
        
//phpinfo();
?>
