<?php 
  include_once("common.php");
  //include_once('include_taxi_webservices.php');
	//include_once(TPATH_CLASS.'configuration.php');
  
  $iTripId = "326";
  echo "TripId >> ".$iTripId;exit;
  getTripChatDetails($iTripId);
  function getTripChatDetails($iTripId){
		global $obj, $generalobj, $tconfig;
    require_once('assets/libraries/firebase/src/firebaseInterface.php');
    require_once('assets/libraries/firebase/src/firebaseLib.php');  
    //$DEFAULT_URL = 'https://ufxv4app.firebaseio.com/';
    //$DEFAULT_TOKEN = 'xcmWvKUsFF9rP7UmZp9qd14powmT1VH8GW1457aO';
    //$DEFAULT_PATH = '835770094542-chat';
    $FIREBASE_DEFAULT_URL = $generalobj->getConfigurations("configurations", "FIREBASE_DEFAULT_URL");
    $FIREBASE_DEFAULT_TOKEN = $generalobj->getConfigurations("configurations", "FIREBASE_DEFAULT_TOKEN");
    $GOOGLE_SENDER_ID = $generalobj->getConfigurations("configurations", "GOOGLE_SENDER_ID");
    $DEFAULT_PATH = $GOOGLE_SENDER_ID."-chat"; 

    $firebase = new \Firebase\FirebaseLib($FIREBASE_DEFAULT_URL, $FIREBASE_DEFAULT_TOKEN);
    
    $fetch = $firebase->get($DEFAULT_PATH.'/'.$iTripId.'-Trip'); // reads value from Firebase
    $fetchdeco = json_decode($fetch);
    //echo "<pre>";print_r($fetchdeco);exit;
    
    $Tripdata_Arr = array();
    foreach($fetchdeco as $Tripobj){
       $Data['iTripId'] = $Tripobj->iTripId;
       $Data['tMessage'] = $Tripobj->Text;  
       $iUserId = $Tripobj->passengerId;
       $iDriverId = $Tripobj->driverId; 
       $Data['dAddedDate'] = @date("Y-m-d H:i:s");
       $eUserType = $Tripobj->eUserType;
       $Data['eUserType'] = $eUserType;
       $Data['eStatus'] = "Unread";
       $Data['iFromMemberId'] = ($eUserType == "Passenger")? $iUserId :$iDriverId;
       $Data['iToMemberId'] = ($eUserType == "Passenger")? $iDriverId :$iUserId;
       $id = $obj->MySQLQueryPerform("trip_messages",$Data,'insert');
    }
    
    $delchat = $firebase->delete($DEFAULT_PATH.'/'.$iTripId.'-Trip');        // deletes value from Firebase

		return $iTripId;
  }
?>