<?php 
// API access key from Google API's Console
// https://stackoverflow.com/questions/39506040/how-to-send-push-notifications-to-iphone-using-fcmfirebase-console-in-php
// https://stackoverflow.com/questions/39506040/how-to-send-push-notifications-to-iphone-using-fcmfirebase-console-in-php
//https://gist.github.com/rolinger/d6500d65128db95f004041c2b636753a
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


//error_reporting(0);

include_once('../configuration.php');
define( 'API_ACCESS_KEY', 'AAAAPsiFat0:APA91bEv78uuYN0eplDkmHdb9CJcOzVTo9TkuvPg30T_2SP8pRjbMkt61ZZg3T1yeXX5eMdB8GVHsXrq6UzQlPdjaK-nIPNDwlNaJjYEE9258zpf7Zk58aUg2i6rvy4mYidXF5HXvj1G');
//$registrationIds = array( $_GET['id'] );
$registrationIds = array("f-Ik3YPV6UU:APA91bGUD8EsdM7WEUo6jWBWCFfw2MrqnWrzMsaIVaP9cVdZ4y4TYAG89HbAcLfrZ-Li54dXujFIYRvamxZiNcBPtAsdiWuXn-6pnj9nUH3vV0jyJldwcM0ChFDGsVEA_l04Wvr0dDgN");
// prep the bundle
    //The device token.
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
        'Content-Type: application/json',
    );
    //Setup headers:
      // echo "<pre>";print_r($headers);exit;
    //Setup curl, add headers and post parameters.
    $ch = curl_init();
    curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
    curl_setopt( $ch,CURLOPT_POST, true );
    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true  );
    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    

    //Send the request
    $response = curl_exec($ch); echo "<pre>";print_r($response);exit;
    $responseArr = json_decode($response);
    //echo "<pre>";print_r($responseArr);exit;
    $success = $responseArr->success; 
    //Close request
    curl_close($ch);
    return $success;
    //echo $response;
?>