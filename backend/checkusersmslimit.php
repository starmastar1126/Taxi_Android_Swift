<?php 
  include_once("common.php");
  include_once('include_config.php');
  include_once('generalFunctions.php');
  //include_once('include_taxi_webservices.php');
	//include_once(TPATH_CLASS.'configuration.php');
   
  $iMemberId = "1"; 
  $UserType = "Driver";
  CheckUserSmsLimit($iMemberId,$UserType);
  
  function CheckUserSmsLimit($iMemberId,$UserType="Passenger"){
		global $obj, $generalobj, $tconfig,$VERIFICATION_CODE_RESEND_COUNT,$VERIFICATION_CODE_RESEND_TIME_IN_SECONDS;
                           
    if($UserType == "Passenger"){
			$tblname = "register_user";
			$fields = 'vVerificationCount,dSendverificationDate,vLang';
			$condfield = 'iUserId';
		}else{
			$tblname = "register_driver";
			$fields = 'vVerificationCount,dSendverificationDate,vLang';
			$condfield = 'iDriverId';
		}
    
    $sql = "select $fields from $tblname where $condfield='".$iMemberId."'";
    $db_user = $obj->MySQLSelect($sql);
    $vVerificationCount = $db_user[0]['vVerificationCount'];
    $dSendverificationDate = $db_user[0]['dSendverificationDate'];
    
    $vLang = $db_user[0]['vLang'];
    if($vLang == "" || $vLang == NULL) {
			$vLang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
		} 
    $languageLabelsArr= getLanguageLabelsArr($vLang,"1");
    
    $currentdate = @date("Y-m-d H:i:s");
    $totalMinute = @round(abs(strtotime($dSendverificationDate) - strtotime($currentdate)) / 60);
    
    $hours =  floor($totalMinute/60); // No. of mins/60 to get the hours and round down
    $mins =   $totalMinute % 60; // No. of mins/60 - remainder (modulus) is the minutes
    $LBL_HOURS_TXT = ($hours > 1)? $languageLabelsArr['LBL_HOURS_TXT'] : $languageLabelsArr['LBL_HOUR_TXT'];
    //$LBL_MINUTES_TXT = ($mins > 1)? $languageLabelsArr['LBL_MINUTES_TXT'] : $languageLabelsArr['LBL_MINUTE'];
    $LBL_MINUTES_TXT = ($mins > 1)? $languageLabelsArr['LBL_MINS_SMALL'] : $languageLabelsArr['LBL_MINUTE'];
    if($hours >= 1){
       $timeDurationDisplay = $hours." ".$LBL_HOURS_TXT." ".$mins." ".$LBL_MINUTES_TXT;
    }else{
       $timeDurationDisplay = $mins." ".$LBL_MINUTES_TXT;
    }
    
    $message = $languageLabelsArr['LBL_SMS_MAXIMAM_LIMIT_TXT']." ".$timeDurationDisplay;
    
    if(($dSendverificationDate < $currentdate) && $dSendverificationDate != "0000-00-00 00:00:00"){
       $updateQuery = "UPDATE $tblname set dSendverificationDate='0000-00-00 00:00:00',vVerificationCount = 0 WHERE $condfield = ".$iMemberId;
			 $obj->sql_query($updateQuery);
       $vVerificationCount = 0;
       $dSendverificationDate = "0000-00-00 00:00:00";
    } 
        
    if($vVerificationCount == $VERIFICATION_CODE_RESEND_COUNT){
       $returnArr['Action'] ="0";
			 $returnArr['message'] = $message;
			 echo json_encode($returnArr);exit;
    }
    UpdateUserSmsLimit($iMemberId,$UserType);
		return $iMemberId;
  }
  
  function UpdateUserSmsLimit($iMemberId,$UserType="Passenger"){
		global $obj, $generalobj, $tconfig,$VERIFICATION_CODE_RESEND_COUNT,$VERIFICATION_CODE_RESEND_COUNT_RESTRICTION;
                           
    if($UserType == "Passenger"){
			$tblname = "register_user";
			$fields = 'vVerificationCount,dSendverificationDate';
			$condfield = 'iUserId';
		}else{
			$tblname = "register_driver";
			$fields = 'vVerificationCount,dSendverificationDate';
			$condfield = 'iDriverId';
		}
    
    $sql = "select $fields from $tblname where $condfield='".$iMemberId."'";
    $db_user = $obj->MySQLSelect($sql); 
    $vVerificationCount = $db_user[0]['vVerificationCount'];
    $dSendverificationDate = $db_user[0]['dSendverificationDate'];
    
    $currentdate = @date("Y-m-d H:i:s");
    $checklastcount = $VERIFICATION_CODE_RESEND_COUNT-1;
    if($vVerificationCount == $checklastcount){
       $minutes = $VERIFICATION_CODE_RESEND_COUNT_RESTRICTION;
       $expire_stamp = date('Y-m-d H:i:s', strtotime("+".$minutes." minute"));
       $updateQuery = "UPDATE $tblname set dSendverificationDate='".$expire_stamp."',vVerificationCount = vVerificationCount+1 WHERE $condfield = ".$iMemberId;
			 $obj->sql_query($updateQuery);
    }else{
       $vVerificationCount = $vVerificationCount+1;     
       if($vVerificationCount > $VERIFICATION_CODE_RESEND_COUNT){
          $vVerificationCount = $VERIFICATION_CODE_RESEND_COUNT;
       }
       $updateQuery = "UPDATE $tblname set vVerificationCount = '".$vVerificationCount."' WHERE $condfield = ".$iMemberId;
			 $obj->sql_query($updateQuery);
    }
    
		return $iMemberId;
  }
?>