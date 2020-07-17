<?php 
# https://stackoverflow.com/questions/37418372/firebase-where-is-my-account-secret-in-the-new-console // find secred token
include_once('../../../include_taxi_webservices.php');
include_once('../configuration.php');
require_once('src/firebaseInterface.php');
require_once('src/firebaseLib.php');

const DEFAULT_URL = 'https://ufxv4app.firebaseio.com/';
const DEFAULT_TOKEN = 'xcmWvKUsFF9rP7UmZp9qd14powmT1VH8GW1457aO';
const DEFAULT_PATH = '835770094542-chat';

$firebase = new \Firebase\FirebaseLib(DEFAULT_URL, DEFAULT_TOKEN);
global $obj;

// --- storing an array ---
$test = array(
    "foo" => "bar",
    "i_love" => "lamp",
    "id" => 42
);
//$dateTime = new DateTime();
//$firebase->set(DEFAULT_PATH . '/' . $dateTime->format('c'), $test);
 
// --- storing a string ---
$dateTime = @date("Y-m-d");
//$insert = $firebase->set(DEFAULT_PATH.'/'.$dateTime, $test);
$fetch = $firebase->get(DEFAULT_PATH.'/321-Trip');
$fetchdeco = json_decode($fetch);
//$fetchresarr = get_object_vars($fetchdeco);
//echo "<pre>";print_r($fetchdeco);exit;
foreach ($fetchdeco as $obj){
   //echo "<pre>";print_r($obj); 
   $Data['iTripId'] = $obj->iTripId;
   $Data['tMessage'] = $obj->Text;  
   $iUserId = $obj->passengerId;
   $iDriverId = $obj->driverId; 
   $Data['dAddedDate'] = @date("Y-m-d H:i:s");
   $eUserType = $obj->eUserType;
   $Data['eUserType'] = $eUserType;
   $Data['eStatus'] = "Unread";
   $Data['iFromMemberId'] = ($eUserType == "Passenger")? $iUserId :$iDriverId;
   $Data['iToMemberId'] = ($eUserType == "Passenger")? $iDriverId :$iUserId;
   $sql = "INSERT INTO `user_wallet` (`iTripId`,`iFromMemberId`,`iToMemberId`,`tMessage`,`dAddedDate`, `eStatus`, `eUserType`) VALUES ('" .$Data['iTripId'] . "','".$Data['iFromMemberId']."', '" . $Data['iToMemberId'] . "','" . $Data['tMessage'] . "', '" . $Data['dAddedDate'] . "', '" . $Data['eStatus'] . "', '" .$Data['eUserType']. "')";
   // mysql_query($sql);
   $obj->sql_query($sql);
   //$id = $obj->MySQLQueryPerform("trip_messages",$Data,'insert');
}
//$firebase->delete(DEFAULT_PATH.'/321-Trip');        // deletes value from Firebase
echo "end";exit;
// --- reading the stored string ---
//$name = $firebase->get(DEFAULT_PATH . '/name/contact001');


?>