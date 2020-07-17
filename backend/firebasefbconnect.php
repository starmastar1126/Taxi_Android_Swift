<?php  
header('Access-Control-Allow-Origin: *');  
error_reporting(E_ALL);
ob_start();
session_start();
include_once('common.php');



$redirect = (isset($_REQUEST['redirect'])) ? $_REQUEST['redirect'] : '0';
if($redirect == 1){
    echo "<pre>";print_r($_REQUEST);exit;
} 
/*require_once 'autoload.php';
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\Entities\AccessToken;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\HttpClients\FacebookHttpable;

// Create our Application instance (replace this with your appId and secret).
$sql="SELECT vValue FROM configurations WHERE vName='FACEBOOK_APP_ID'";
$db_appid=$obj->MySQLSelect($sql);

$sql="SELECT vValue FROM configurations WHERE vName='FACEBOOK_APP_SECRET_KEY'";
$db_key=$obj->MySQLSelect($sql);

include_once($tconfig["tsite_libraries_v"]."/Imagecrop.class.php");
$thumb = new thumbnail();
$temp_gallery = $tconfig["tsite_temp_gallery"];

include_once($tconfig["tsite_libraries_v"]."/SimpleImage.class.php");
$img = new SimpleImage();

$userType = (isset($_REQUEST['userType'])) ? $_REQUEST['userType'] : 'rider';

// init app with app id and secret
FacebookSession::setDefaultApplication( "204511760118542","9432a3446c63838228e7a143ae18cf0c" );
// login helper with redirect_uri
$helper = new FacebookRedirectLoginHelper($tconfig["tsite_url"].'firebasefbconnect.php?userType='.$userType);

try {
  $session = $helper->getSessionFromRedirect();  
} catch( FacebookRequestException $ex ) {
  // When Facebook returns an error
  error_log($ex);
} catch( Exception $ex ) {
  // When validation fails or other local issues
  error_log($ex);    
}
  */

?>    
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <link rel="stylesheet" href="templates/uber/assets/plugins/bootstrap/css/bootstrap.css" />
<link rel="stylesheet" href="templates/uber/assets/css/sign-up.css" />
<link rel="stylesheet" href="templates/uber/assets/plugins/magic/magic.css" />
<link rel="stylesheet" href="assets/css/bootstrap-front.css" />
<link rel="stylesheet" type="text/css" href="assets/css/jquery-ui.css">
<link rel="stylesheet" href="assets/plugins/Font-Awesome/css/font-awesome.css" />
<link rel="stylesheet" href="assets/css/design_v5.css">
	<link rel="stylesheet" href="assets/css/style_v5.css">  
	<!-- <link rel="stylesheet" href="assets/css/style_v5_color.css"> -->
		<link rel="stylesheet" href="assets/css/style_v5_color.css"> 
		<!-- <link rel="stylesheet" href="assets/css/style-dd.css">-->

<link rel="stylesheet" href="assets/css/fa-icon.css">
<link href="assets/css/initcarousel.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" href="assets/css/media.css">
<!--<link rel="stylesheet" href="assets/css/style_theme.css">-->
<!-- Font CSS-->
<link href='//fonts.googleapis.com/css?family=Raleway:400,700,300,500,900,800,600,200,100' rel='stylesheet' type='text/css'>


	<!--<link rel="stylesheet" href="assets/css/lang/en.css">--> 
   
  <title></title>
  </head>
  <body>
      	<span class="login-socials">
							<!--<a class="fa fa-facebook" href="javascript:void(0);" onClick="checklogin();"></a>-->
              <a class="fa fa-facebook" style="width:5%;" href="javascript:void(0);" onClick="checklogin();"></a>
				</span>
  
  <script src="https://www.gstatic.com/firebasejs/4.8.1/firebase.js"></script>
  
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase-database.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase-firestore.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase-messaging.js"></script>

<script>
  // Initialize Firebase
  var config = {
    apiKey: "AIzaSyDzkZGzppVrupywqEBAjxQsIrkIp8IpFgU",
    authDomain: "sociallogin-7b9e3.firebaseapp.com",
    //authDomain: "",
    databaseURL: "https://sociallogin-7b9e3.firebaseio.com",
    projectId: "sociallogin-7b9e3",
    storageBucket: "sociallogin-7b9e3.appspot.com",
    messagingSenderId: "319678817192"
  };
  firebase.initializeApp(config);
  
  function facebookSignin() {
  
  var provider = new firebase.auth.FacebookAuthProvider();
    //console.log(provider);
    provider.addScope('email');
    provider.addScope('user_friends');
  
   firebase.auth().signInWithPopup(provider)
   
   .then(function(result) {
      var token = result.credential.accessToken;
      var user = result.user;
		
      /*console.log(token)
      console.log("\n")
      console.log(user)
      console.log("\n")
      console.log("Result >> \n")*/
      //console.log(result)
      console.log("Hello")
   }).catch(function(error) {
      console.log(error.code);
      console.log(error.message);
   });
}
  
  function checklogin(){
    //alert("Hello");return false;
    var provider = new firebase.auth.FacebookAuthProvider();
    //console.log(provider);
    
    /*provider.setCustomParameters({
      hd: "192.168.1.131"
    });    */
    provider.addScope('email');
    provider.addScope('user_friends');
    //firebase.auth().signInWithRedirect(provider);

    /*provider.setCustomParameters({
      'display': 'popup'
    });    */
    
    
    firebase.auth().signInWithPopup(provider).then(function(result) {
  // This gives you a Facebook Access Token. You can use it to access the Facebook API.
      var token = result.credential.accessToken;
      // The signed-in user info.
      var user = result.user;
      // ...
      console.log(token);
      console.log(user);
      //console.log("Hello")
      console.log(result);
    }).catch(function(error) {
      // Handle Errors here.
      var errorCode = error.code;
      var errorMessage = error.message;
      // The email of the user's account used.
      var email = error.email;
      // The firebase.auth.AuthCredential type that was used.
      var credential = error.credential;
      // ...
      console.log(error);
    });
    
    firebase.auth().getRedirectResult().then(function(result) {
      if (result.credential) {
        // This gives you a Google Access Token.
        var token = result.credential.accessToken;
      }
      var user = result.user;
     
    }) 
    
  }
  </script>    
  </body>
</html>




