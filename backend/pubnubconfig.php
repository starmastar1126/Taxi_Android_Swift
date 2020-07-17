<script src="https://cdn.pubnub.com/sdk/javascript/pubnub.4.4.3.js"></script>
<?php 
ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);
require_once('assets/libraries/pubnub/autoloader.php');
	

// $pubnub = new Pubnub\Pubnub(array("publish_key" => "pub-c-4b576e8a-2726-4e0b-901b-d4b8e3217f42","subscribe_key" => "sub-c-eb8cf22c-96ed-11e7-ae90-9e4639429209", "uuid" => "789789789789789"));


$pubnub = new Pubnub\Pubnub(array("publish_key" => "pub-c-4305e271-d3b6-4420-ba87-a86904584d38","subscribe_key" => "sub-c-1f475106-9c2b-11e7-b8fe-0e6b753288ab", "uuid" => "87456478745874252"));
	
// $info = $pubnub->publish('ONLINE_DRIVER_LOC_38', 'Hey World!');

$message_arr = array();
$message_arr['MsgType'] = "LocationUpdate";
$message_arr['iDriverId'] = "156";
$message_arr['vLatitude'] = $_REQUEST['vLatitude'];
$message_arr['vLongitude'] = $_REQUEST['vLongitude'];
$message_arr['ChannelName'] = "ONLINE_DRIVER_LOC_156";
		
$message= json_encode($message_arr);
$info = $pubnub->publish('ONLINE_DRIVER_LOC_156', $message);

// $history = $pubnub->history('ONLINE_DRIVER_LOC_156',100);
  
  // echo "History==<BR><PRE/>";
// print_r($history);
print_r($info);
?>
