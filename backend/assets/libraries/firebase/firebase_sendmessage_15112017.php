<?php 
# https://stackoverflow.com/questions/37418372/firebase-where-is-my-account-secret-in-the-new-console // find secred token
include_once('../configuration.php');
require_once('src/firebaseInterface.php');
require_once('src/firebaseLib.php');

$DEFAULT_URL = 'https://ufxv4app.firebaseio.com/ ';
$DEFAULT_TOKEN = 'xcmWvKUsFF9rP7UmZp9qd14powmT1VH8GW1457aO';
//$DEFAULT_PATH = '/firebase/example';
$DEFAULT_PATH = '/ufxv4app/firebase/example';

$firebase = new \Firebase\FirebaseLib($DEFAULT_URL, $DEFAULT_TOKEN);

// --- storing an array ---
$test = array(
    "foo" => "bar",
    "i_love" => "lamp",
    "id" => 42
);
//$name = $firebase->get($DEFAULT_PATH);
//echo "<pre>";print_r($name);exit;
$dateTime = new DateTime();
//$sendmsg = $firebase->set($DEFAULT_PATH . '/' . $dateTime->format('c'), $test);
//echo "<pre>";print_r($sendmsg);exit;
$dateTime = date("Y-m-d H:i:s");
$sendmsg = $firebase->set($DEFAULT_PATH . '/' . $dateTime, $test);
echo "<pre>";print_r($sendmsg);exit;

//$delval = $firebase->delete($DEFAULT_PATH);        // deletes value from Firebase
//echo "<pre>";print_r($delval);exit;
// --- storing a string ---
$firebase->set($DEFAULT_PATH . '/name/contact001', "John Doe");

// --- reading the stored string ---
$name = $firebase->get($DEFAULT_PATH . '/name/contact001');
?>