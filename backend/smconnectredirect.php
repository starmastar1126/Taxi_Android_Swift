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
    firebase.auth().getRedirectResult().then(function(result) {      
        if (result.credential) {
          // This gives you a Google Access Token. You can use it to access the Google API.
          var token = result.credential.accessToken;
        } 
        // The signed-in user info.
        var user = result.user;
    });
    
     // [START authstatelistener]
      firebase.auth().onAuthStateChanged(function(user) {   //alert("Hello2");return false;
        if (user) {
          // User is signed in.
           var displayName = user.displayName;
           var UserName = displayName.split(' ');
           var first_name = UserName[0]; 
           var last_name = UserName[1];  
           var email = user.email;
           var emailVerified = user.emailVerified;
           var photo_url = user.photoURL;
           var isAnonymous = user.isAnonymous;
           //var id = user.uid;
           var providerData = user.providerData;
           var id = providerData[0].uid;  
           var phoneNumber  = user.phoneNumber;
           if(phoneNumber == null){
               phoneNumber = "";
           }  
               
          // ...
          //console.log(token); console.log(user);console.log("Hello");
          //console.log(user);return false; 
          //console.log("Email >> " + email);console.log("First Name >> " + first_name);console.log("Last Name >> " + last_name);console.log("Gender >> " + gender);
          //console.log("Facebook id >> " + id);console.log("Photo URL>> " + photo_url); console.log("PhoneNumber >> " + phoneNumber); 
          updatedUrl = "&vEmail="+email+"&vName="+first_name+"&vLastName="+last_name+"&vFbId="+id+"&photo_url="+photo_url+"&vPhone="+phoneNumber;//console.log(updatedUrl);return false;
          window.location = tsite_url+"success.php?success=1"+updatedUrl;    
          return false;    
          // [END_EXCLUDE]
        } else {
         //alert("Hello3");return false;
         var provider = new firebase.auth.FacebookAuthProvider();
         //console.log(provider);       
         provider.addScope('email');
         provider.addScope('user_friends');
         provider.setCustomParameters({
          'auth_type': 'rerequest'
        });
         firebase.auth().signInWithRedirect(provider);     
          // User is signed out.
        }
      });
      // [END authstatelistener]
  }   
            
  
  function checkgooglelogin(){
    //alert("Hello");return false;
      
    firebase.auth().getRedirectResult().then(function(result) {    
        if (result.credential) {
          // This gives you a Google Access Token. You can use it to access the Google API.
          var token = result.credential.accessToken;    
        } 
        // The signed-in user info.
        var user = result.user;
    });
    
     // [START authstatelistener]
      firebase.auth().onAuthStateChanged(function(user) {   //alert("Hello2");return false;
        if (user) {      
          // User is signed in.
           var displayName = user.displayName;
           var UserName = displayName.split(' ');
           var first_name = UserName[0]; 
           var last_name = UserName[1];  
           var email = user.email;
           var emailVerified = user.emailVerified;
           var photo_url = user.photoURL;
           var isAnonymous = user.isAnonymous;
           //var id = user.uid;
           var providerData = user.providerData;
           var id = providerData[0].uid;  
           var phoneNumber  = user.phoneNumber;
           if(phoneNumber == null){
               phoneNumber = "";
           }        
      // ...
      //console.log(token); console.log(user);console.log("Hello");
      //console.log(user);return false; 
      //console.log("Email >> " + email);console.log("First Name >> " + first_name);console.log("Last Name >> " + last_name);console.log("Gender >> " + gender);
      //console.log("Facebook id >> " + id);console.log("Photo URL>> " + photo_url); console.log("PhoneNumber >> " + phoneNumber);
      localStorage.removeItem("firebase:authUser:AIzaSyDzkZGzppVrupywqEBAjxQsIrkIp8IpFgU:[DEFAULT]");
      console.log(localStorage);           
      localStorage.clear();
      firebase.auth().signOut();  
      updatedUrl = "&vEmail="+email+"&vName="+first_name+"&vLastName="+last_name+"&vFbId="+id+"&photo_url="+photo_url+"&vPhone="+phoneNumber;  //console.log(updatedUrl);return false;
      window.location = tsite_url+"success.php?success=1"+updatedUrl;
      return false;    
          // [END_EXCLUDE]
        } else {     
         //alert("Hello3");return false;     
         var provider = new firebase.auth.GoogleAuthProvider();
         //console.log(provider);       
         provider.addScope('https://www.googleapis.com/auth/plus.login');
         provider.setCustomParameters({
          'prompt': 'select_account'
        });
         firebase.auth().signInWithRedirect(provider);                 
          // User is signed out.             
        }
      });
      // [END authstatelistener]
  }
  
  function checktwitterlogin(){
    //alert("Hello");return false; 
    firebase.auth().getRedirectResult().then(function(result) {    
        if (result.credential) {
          // This gives you a Google Access Token. You can use it to access the Google API.
          var token = result.credential.accessToken;
        } 
        // The signed-in user info.
        var user = result.user;
    });
    
     // [START authstatelistener]
      firebase.auth().onAuthStateChanged(function(user) {   //alert("Hello2");return false;
        if (user) {
          // User is signed in.
           var displayName = user.displayName;
           var UserName = displayName.split(' ');
           var first_name = UserName[0]; 
           var last_name = UserName[1];
           if(last_name == null){
               last_name = "";
           }  
           var email = user.email;
           if(email == null){
               email = "";
           }
           var emailVerified = user.emailVerified;
           var photo_url = user.photoURL;
           var isAnonymous = user.isAnonymous;
           //var id = user.uid;
           var providerData = user.providerData;
           var id = providerData[0].uid;  
           var phoneNumber  = user.phoneNumber;
           if(phoneNumber == null){
               phoneNumber = "";
           }
      // ...
      //console.log(token); console.log(user);console.log("Hello");
      //console.log(user);return false; 
      //console.log("Email >> " + email);console.log("First Name >> " + first_name);console.log("Last Name >> " + last_name);console.log("Gender >> " + gender);
      //console.log("Facebook id >> " + id);console.log("Photo URL>> " + photo_url); console.log("PhoneNumber >> " + phoneNumber);
      
      updatedUrl = "&vEmail="+email+"&vName="+first_name+"&vLastName="+last_name+"&vFbId="+id+"&photo_url="+photo_url+"&vPhone="+phoneNumber; // console.log(updatedUrl);return false;
      window.location = tsite_url+"success.php?success=1"+updatedUrl;
      return false;    
          // [END_EXCLUDE]
        } else {
         //alert("Hello3");return false;
         var provider = new firebase.auth.TwitterAuthProvider();
         //console.log(provider);       
         firebase.auth().signInWithRedirect(provider);     
          // User is signed out.
        }
      });
      // [END authstatelistener]
  } 
  
  </script>

</body>
</html>



