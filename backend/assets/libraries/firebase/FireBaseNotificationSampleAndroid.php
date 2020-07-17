<?php 
// API access key from Google API's Console
// https://gist.github.com/MohammadaliMirhamed/7384b741a5c979eb13633dc6ea1269ce
//https://gist.github.com/prime31/5675017
//https://gist.github.com/rolinger/d6500d65128db95f004041c2b636753a
include_once('../configuration.php');
define( 'API_ACCESS_KEY', 'AAAAPsiFat0:APA91bEv78uuYN0eplDkmHdb9CJcOzVTo9TkuvPg30T_2SP8pRjbMkt61ZZg3T1yeXX5eMdB8GVHsXrq6UzQlPdjaK-nIPNDwlNaJjYEE9258zpf7Zk58aUg2i6rvy4mYidXF5HXvj1G');
//$registrationIds = array( $_GET['id'] );
$registrationIds = array( "32b1330f09b51ab7fc1f81317c6b98a2b019fa4d781e8c94b0ce7f67eaafb29a" );
// prep the bundle
/*$msg = array
    (
        'message'   => 'Hello',
        'title'     => 'Hello 1',
        'subtitle'  => 'This is a subtitle. subtitle',
        'tickerText'    => 'Ticker text here...Ticker text here...Ticker text here',
        'vibrate'   => 1,
        'sound'     => 1,
        'largeIcon' => 'large_icon',
        'smallIcon' => 'small_icon'
    
    );  */
    
    $msg = array
    (
        'body' 	=> 'Body  Of Notification',
        'title'	=> 'Title Of Notification',
       	//'icon'	=> 'myicon',/*Default Icon*/
       	//'sound' => 'mySound'/*Default sound*/
    );
     
     $fields = array
    (
        'registration_ids'  => $registrationIds,
        'priority' => "high",
        'notification' => $msg,
        //'data'          => $msg
        'data'         => array ("message" => $msg) 
    ); 

    $headers = array
    (
    	'Authorization: key=' . API_ACCESS_KEY,
    	'Content-Type: application/json'
    );
 
$ch = curl_init();
curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
//curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
curl_setopt( $ch,CURLOPT_POST, true );
curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
curl_setopt( $ch,CURLOPT_RETURNTRANSFER, false );
curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
$result = curl_exec($ch );
curl_close( $ch );
echo $result;
?>

<?php 
/*
https://stackoverflow.com/questions/38064102/high-priority-gcm-message-in-android-with-php
function send_notification($con,$registatoin_ids,$idDestino,$titulo, $message, $nombreOrigenNotificacion, $dia, $mes, $anio, $idCancha, $idOrigen, $esComplejo) { 
    $url = 'https://android.googleapis.com/gcm/send';

    $fields = array(
        'data' =>array("idDestino" => $idDestino,"title" => $titulo,"message" => $message,"nombreOrigenNotificacion" => $nombreOrigenNotificacion,"dia" => $dia,"mes" => $mes,"anio" => $anio,"idCancha" => $idCancha,"idOrigen" => $idOrigen,"esComplejo" => $esComplejo),
        'registration_ids' => $registatoin_ids
    );
    $headers = array(
        'Authorization: key=' . apiKey,
        'Content-Type: application/json'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    $result = curl_exec($ch);
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    //echo $result;


}
*/
?>