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
  
 /**
     * Function called when clicking the Login/Logout button.
     */
    // [START buttoncallback]
    function toggleSignIn() {
       if (!firebase.auth().currentUser) {
        // [START createprovider]
		
        //firebase.auth().signOut();
        var provider = new firebase.auth.FacebookAuthProvider();
        // [END createprovider]
        // [START addscopes]
        provider.addScope('user_likes');
        // [END addscopes]
        // [START signin]
        firebase.auth().signInWithRedirect(provider);
        // [END signin]
      } 
	   else {
         firebase.auth().signOut();
       }
    }
    // [END buttoncallback]
    /**
     * initApp handles setting up UI event listeners and registering Firebase auth listeners:
     *  - firebase.auth().onAuthStateChanged: This listener is called when the user is signed in or
     *    out, and that is where we update the UI.
     *  - firebase.auth().getRedirectResult(): This promise completes when the user gets back from
     *    the auth redirect flow. It is where you can get the OAuth access token from the IDP.
     */
    function initApp() {
      // Result from Redirect auth flow.
      // [START getidptoken]
      firebase.auth().getRedirectResult().then(function(result) {
        if (result.credential) {
          // This gives you a Facebook Access Token. You can use it to access the Facebook API.
          var token = result.credential.accessToken;
          // [START_EXCLUDE]
          document.getElementById('quickstart-oauthtoken').textContent = token;
        } else {
          document.getElementById('quickstart-oauthtoken').textContent = 'null';
          // [END_EXCLUDE]
        }
        // The signed-in user info.
        var user = result.user;
      }).catch(function(error) {
        // Handle Errors here.
        var errorCode = error.code;
        var errorMessage = error.message;
        // The email of the user's account used.
        var email = error.email;
        // The firebase.auth.AuthCredential type that was used.
        var credential = error.credential;
        // [START_EXCLUDE]
        if (errorCode === 'auth/account-exists-with-different-credential') {
          alert('You have already signed up with a different auth provider for that email.');
          // If you are using multiple auth providers on your app you should handle linking
          // the user's accounts here.
        } else {
          console.error(error);
        }
        // [END_EXCLUDE]
      });
      // [END getidptoken]
      // Listening for auth state changes.
      // [START authstatelistener]
      firebase.auth().onAuthStateChanged(function(user) {
        if (user) {
          // User is signed in.
          var displayName = user.displayName;
          var email = user.email;
          var emailVerified = user.emailVerified;
          var photoURL = user.photoURL;
          var isAnonymous = user.isAnonymous;
          var uid = user.uid;
          var providerData = user.providerData;
          // [START_EXCLUDE]
          document.getElementById('quickstart-sign-in-status').textContent = 'Signed in';
          document.getElementById('quickstart-sign-in').textContent = 'Log out';
          document.getElementById('quickstart-account-details').textContent = JSON.stringify(user, null, '  ');
          // [END_EXCLUDE]
        } else {
          // User is signed out.
          // [START_EXCLUDE]
          document.getElementById('quickstart-sign-in-status').textContent = 'Signed out';
          document.getElementById('quickstart-sign-in').textContent = 'Log in with Facebook';
          document.getElementById('quickstart-account-details').textContent = 'null';
          document.getElementById('quickstart-oauthtoken').textContent = 'null';
          // [END_EXCLUDE]
        }
        // [START_EXCLUDE]
        document.getElementById('quickstart-sign-in').disabled = false;
        // [END_EXCLUDE]
      });
      // [END authstatelistener]
     // document.getElementById('quickstart-sign-in').addEventListener('click', toggleSignIn, false);
    }
    // window.onload = function() {
      // initApp();
    // };
  
  </script>
<script>
   var tsite_url = "<?=$tconfig['tsite_url'];?>";
   $(document).ready(function() {
			//alert("Hellosdd");return false;
      <?php  if($eSignUpType == "Facebook"){?>
	  
      initApp();
	  //toggleSignIn();
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
  
<div class="demo-layout mdl-layout mdl-js-layout mdl-layout--fixed-header">

  <!-- Header section containing title -->
  <header class="mdl-layout__header mdl-color-text--white mdl-color--light-blue-700">
    <div class="mdl-cell mdl-cell--12-col mdl-cell--12-col-tablet mdl-grid">
      <div class="mdl-layout__header-row mdl-cell mdl-cell--12-col mdl-cell--12-col-tablet mdl-cell--8-col-desktop">
        <a href="/"><h3>Firebase Authentication</h3></a>
      </div>
    </div>
  </header>

  <main class="mdl-layout__content mdl-color--grey-100">
    <div class="mdl-cell mdl-cell--12-col mdl-cell--12-col-tablet mdl-grid">

      <!-- Container for the demo -->
      <div class="mdl-card mdl-shadow--2dp mdl-cell mdl-cell--12-col mdl-cell--12-col-tablet mdl-cell--12-col-desktop">
        <div class="mdl-card__title mdl-color--light-blue-600 mdl-color-text--white">
          <h2 class="mdl-card__title-text">Facebook Authentication with Redirect</h2>
        </div>
        <div class="mdl-card__supporting-text mdl-color-text--grey-600">
          <p>Log in with your Facebook account below.</p>

          <!-- Button that handles sign-in/sign-out -->
          <button disabled class="mdl-button mdl-js-button mdl-button--raised" id="quickstart-sign-in">Log in with Facebook</button>

          <!-- Container where we'll display the user details -->
          <div class="quickstart-user-details-container">
            Firebase sign-in status: <span id="quickstart-sign-in-status">Unknown</span>
            <div>Firebase auth <code>currentUser</code> object value:</div>
            <pre><code id="quickstart-account-details">null</code></pre>
            <div>Facebook OAuth Access Token:</div>
            <pre><code id="quickstart-oauthtoken">null</code></pre>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>
</body>
</html>



