<?php  
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ob_start();
session_start();
include_once('common.php');

$eSignUpType = (isset($_REQUEST['eSignUpType'])) ? $_REQUEST['eSignUpType'] : 'Google';    // Facebook,Twitter,Google
?>
<!DOCTYPE html>
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=windows-1250">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title></title>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/4.8.1/firebase-auth.js"></script>
<script>
   var tsite_url = "<?=$tconfig['tsite_url'];?>";
   $(document).ready(function() {
			//alert("Hellosdd");return false;
      <?php  if($eSignUpType == "Facebook"){?>
      checklogin();
      <?php }?>
       <?php  if($eSignUpType == "Google"){?>
      checkgooglelogin();
      <?php }?>
      <?php  if($eSignUpType == "Twitter"){?>
      checktwitterlogin();
      <?php }?>
	 });
</script>
  </head>
  <body>
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
  
  function checklogin(){
    //alert("Hello");return false;   
    var provider = new firebase.auth.FacebookAuthProvider();
    //console.log(provider);
    
    provider.addScope('email');
    provider.addScope('user_friends');
    
    //firebase.auth().signInWithRedirect(provider);
    
       
    firebase.auth().signInWithPopup(provider).then(function(result) {
          // This gives you a Facebook Access Token. You can use it to access the Facebook API.
          var token = result.credential.accessToken;
          // The signed-in user info.
          var user = result.user;
          var email = result.additionalUserInfo.profile.email;
          var first_name  = result.additionalUserInfo.profile.first_name;
          var gender  = result.additionalUserInfo.profile.gender;
          var id  = result.additionalUserInfo.profile.id;
          var last_name  = result.additionalUserInfo.profile.last_name;
          var photo_url  = result.user.photoURL;
          var phoneNumber  = result.user.phoneNumber;
          if(phoneNumber == null){
             phoneNumber = "";
          }
          // ...
          //console.log(token); console.log(user);console.log("Hello");
          //console.log(result);
          //console.log("Email >> " + email);console.log("First Name >> " + first_name);console.log("Last Name >> " + last_name);console.log("Gender >> " + gender);
          //console.log("Facebook id >> " + id);console.log("Photo URL>> " + photo_url); console.log("PhoneNumber >> " + phoneNumber);
          
          updatedUrl = "&vEmail="+email+"&vName="+first_name+"&vLastName="+last_name+"&eGender="+gender+"&vFbId="+id+"&photo_url="+photo_url+"&vPhone="+phoneNumber;
          window.location = tsite_url+"success.php?success=1"+updatedUrl;
          return false;
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
      window.location = tsite_url+"success.php?success=0";
      return false;
    });  
    
    
     /*firebase.auth().getRedirectResult().then(function(result) {
      if (result.credential) {
        // This gives you a Google Access Token.
        var token = result.credential.accessToken;
        var user = result.user;
        console.log("User >> "+ user);
      }
      
    })   */
  }
  
  function checkgooglelogin(){
    //alert("Hello");return false;
    var provider = new firebase.auth.GoogleAuthProvider();
    //console.log(provider);
    
    
    firebase.auth().signInWithPopup(provider).then(function(result) {
  // This gives you a Google Access Token. You can use it to access the Google API.
      var token = result.credential.accessToken;
      // The signed-in user info.
      var email = result.additionalUserInfo.profile.email;
      var first_name  = result.additionalUserInfo.profile.given_name;
      var gender  = result.additionalUserInfo.profile.gender;
      var id  = result.additionalUserInfo.profile.id;
      var last_name  = result.additionalUserInfo.profile.family_name;
      var photo_url  = result.user.photoURL;
      var phoneNumber  = result.user.phoneNumber;
      if(phoneNumber == null){
         phoneNumber = "";
      }
      // ...
      //console.log(token); console.log(user);console.log("Hello");
      //console.log(result); 
      //console.log("Email >> " + email);console.log("First Name >> " + first_name);console.log("Last Name >> " + last_name);console.log("Gender >> " + gender);
      //console.log("Facebook id >> " + id);console.log("Photo URL>> " + photo_url); console.log("PhoneNumber >> " + phoneNumber);
      
      updatedUrl = "&vEmail="+email+"&vName="+first_name+"&vLastName="+last_name+"&eGender="+gender+"&vFbId="+id+"&photo_url="+photo_url+"&vPhone="+phoneNumber;
      window.location = tsite_url+"success.php?success=1"+updatedUrl;
      return false;
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
      window.location = tsite_url+"success.php?success=0";
      return false;
    });
   
  }
  
  function checktwitterlogin(){
    //alert("Hello");return false;
    var provider = new firebase.auth.TwitterAuthProvider();
    //console.log(provider);
    
    
    firebase.auth().signInWithPopup(provider).then(function(result) {
  // This gives you a Twitter Access Token. You can use it to access the Google API.
      var token = result.credential.accessToken;
      var secret = result.credential.secret;
      // The signed-in user info.
      var email = result.additionalUserInfo.profile.email;
      var first_name  = result.additionalUserInfo.profile.name;
      //var gender  = result.additionalUserInfo.profile.gender;
      var gender = "";
      var id  = result.additionalUserInfo.profile.id;
      //var last_name  = result.additionalUserInfo.profile.family_name;
      var last_name  = "";
      var photo_url  = result.user.photoURL;
      var phoneNumber  = result.user.phoneNumber;
      if(phoneNumber == null){
         phoneNumber = "";
      }
      // ...
      //console.log(token); console.log(user);console.log("Hello");
      //console.log(result);return false; 
      //console.log("Email >> " + email);console.log("First Name >> " + first_name);console.log("Last Name >> " + last_name);console.log("Gender >> " + gender);
      //console.log("Facebook id >> " + id);console.log("Photo URL>> " + photo_url); console.log("PhoneNumber >> " + phoneNumber);
      
      updatedUrl = "&vEmail="+email+"&vName="+first_name+"&vLastName="+last_name+"&eGender="+gender+"&vFbId="+id+"&photo_url="+photo_url+"&vPhone="+phoneNumber;
      window.location = tsite_url+"success.php?success=1"+updatedUrl;
      return false;
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
      window.location = tsite_url+"success.php?success=0";
      return false;
    });
   
  }
  
  </script>

</body>
</html>



