<?php   
  include_once("common.php");
	global $generalobj;

	$query = "SELECT * FROM cab_booking WHERE iDriverId != '0' AND ( eStatus = 'Pending' OR eStatus = 'Assign' ) AND eMessageSend = 'No' AND eAutoAssign = 'No'";
	$db_cab = $obj->MySQLSelect($query);
	if(count($db_cab) > 0) { 

        for($i=0;$i<count($db_cab);$i++){
          
            $current_date_time = @date('Y-m-d H:i:s');             
            $current_date = @date('Y-m-d'); 
            $booking_date = @date('Y-m-d',strtotime($db_cab[$i]['dBooking_date'])); 

            $time_diff = date("H:i", strtotime($current_date_time) - strtotime($db_cab[$i]['dBooking_date']) ) ; 
            $times = explode(':', $time_diff);

            $total_minutes_diff = (($times[0]*60) + $times[1]);

            if($current_date == $booking_date){           

                if($total_minutes_diff >= 30){

                    if($db_cab[$i]['eMessageSend'] == 'No'){   

                       // echo "Send Mail successfully";                       

                        $getdata_driver = get_DriverDetail($db_cab[$i]['iDriverId'],$db_cab[$i]['dBooking_date'],$db_cab[$i]['iUserId'],$db_cab[$i]['vBookingNo'],$db_cab[$i]['vTimeZone']);
                        $getdata_user  =  get_PassengerDetail($db_cab[$i]['iUserId'],$db_cab[$i]['dBooking_date'],$db_cab[$i]['iDriverId'],$db_cab[$i]['vBookingNo'],$db_cab[$i]['vTimeZone']);
                        if($getdata_driver['action'] == 1 &&  $getdata_user['action'] == 1){

                           echo "Send Mail Successfully....";     

                        }else{
                            echo " Send Mail Failed..... ";     

                        }                        

                        $updateQuery = "UPDATE cab_booking SET eMessageSend ='Yes' WHERE iCabBookingId=".$db_cab[$i]['iCabBookingId'];                       
                        $db_cab = $obj->sql_query($updateQuery);
                    }   
                }

            }

        }       
    
	}   

    function get_DriverDetail($id,$booking_date,$iUserId,$BookingNo,$vTimeZone){
        global $generalobj,$obj;

        $query = "SELECT * FROM register_driver WHERE iDriverId=".$id;
        $db_driver = $obj->MySQLSelect($query);
        $vPhone = $db_driver[0]['vPhone'];
        $vcode = $db_driver[0]['vCode'];
        $vLang = $db_driver[0]['vLang'];  

        //$Booking_Date = @date('d-m-Y',strtotime($booking_date));    
        //$Booking_Time = @date('H:i:s',strtotime($booking_date)); 
        $systemTimeZone = date_default_timezone_get();
    	// echo "hererrrrr:::".$systemTimeZone;exit;
    	$scheduleDate = converToTz($booking_date,$vTimeZone,$systemTimeZone); 
        $Booking_Date = @date('d-m-Y',strtotime($scheduleDate));    
        $Booking_Time = @date('H:i:s',strtotime($scheduleDate));  

        $query = "SELECT * FROM register_user WHERE iUserId=".$iUserId;
        $db_user= $obj->MySQLSelect($query);
        $Pass_name = $db_user[0]['vName'].' '.$db_user[0]['vLastName']; 

        $maildata['PASSENGER_NAME'] = $Pass_name;      
        $maildata['BOOKING_DATE'] = $Booking_Date;      
        $maildata['BOOKING_TIME'] =  $Booking_Time;      
        $maildata['BOOKING_NUMBER'] = $BookingNo;      

        $message_layout = send_messages_user("DRIVER_SEND_MESSAGE",$maildata,"",$vLang);
        
        return sendCode($vPhone,$vcode,$message_layout,"");    

    }

    function get_PassengerDetail($iUserId,$booking_date,$id,$BookingNo,$vTimeZone){
        global $generalobj,$obj;

        $query = "SELECT * FROM register_user WHERE iUserId=".$iUserId;
        $db_pass = $obj->MySQLSelect($query);

        $query = "SELECT * FROM register_driver WHERE iDriverId=".$id;
        $db_driver = $obj->MySQLSelect($query);

        $query = "SELECT * FROM driver_vehicle WHERE iDriverVehicleId=".$db_driver[0]['iDriverVehicleId'];  
        $db_driver_vehicles = $obj->MySQLSelect($query);

        $vPhone = $db_pass[0]['vPhone'];
        $vcode = $db_pass[0]['vPhoneCode'];
        $vLang = $db_pass[0]['vLang'];
        $driver_name = $db_driver[0]['vName'].' '.$db_driver[0]['vLastName'];

        //$Booking_Date = @date('d-m-Y',strtotime($booking_date));    
        //$Booking_Time = @date('H:i:s',strtotime($booking_date));
        //$Booking_Date = @date('d-m-Y',strtotime($booking_date));    
        //$Booking_Time = @date('H:i:s',strtotime($booking_date)); 
        $systemTimeZone = date_default_timezone_get();
    		// echo "hererrrrr:::".$systemTimeZone;exit;
    		$scheduleDate = converToTz($booking_date,$vTimeZone,$systemTimeZone); 
        $Booking_Date = @date('d-m-Y',strtotime($scheduleDate));    
        $Booking_Time = @date('H:i:s',strtotime($scheduleDate));
      

        $maildata['DRIVER_NAME'] = $driver_name;      
        $maildata['PLATE_NUMBER'] = $db_driver_vehicles[0]['vLicencePlate'];      
        $maildata['BOOKING_DATE'] = $Booking_Date;      
        $maildata['BOOKING_TIME'] =  $Booking_Time;      
        $maildata['BOOKING_NUMBER'] = $BookingNo;      

        $message_layout = send_messages_user("USER_SEND_MESSAGE",$maildata,"",$vLang);
        return sendCode($vPhone,$vcode,$message_layout,"");    

    }

    function send_messages_user($type, $db_rec = '', $newsid = ''){
        
        global $MAIL_FOOTER,$generalobj,$obj;
            $str = "select * from send_message_templates where vEmail_Code='" . $type . "'";
            $res = $obj->MySQLSelect($str);
            switch ($type) {
                case "DRIVER_SEND_MESSAGE":              
                $key_arr = Array("#PASSENGER_NAME#","#BOOKING_DATE#","#BOOKING_TIME#","#BOOKING_NUMBER#","#MAILFOOTER#");
                $val_arr = Array($db_rec['PASSENGER_NAME'], $db_rec['BOOKING_DATE'], $db_rec['BOOKING_TIME'] , $db_rec['BOOKING_NUMBER'],$MAIL_FOOTER);
                break;

                case "USER_SEND_MESSAGE":               
                 $key_arr = Array("#DRIVER_NAME#","#PLATE_NUMBER#","#BOOKING_DATE#","#BOOKING_TIME#","#BOOKING_NUMBER#","#MAILFOOTER#");
                $val_arr = Array($db_rec['DRIVER_NAME'], $db_rec['PLATE_NUMBER'],$db_rec['BOOKING_DATE'], $db_rec['BOOKING_TIME'] , $db_rec['BOOKING_NUMBER'],$MAIL_FOOTER);      
                break;
            }  
            
           // $maillanguage = get_user_preffered_language($to_email);
            $maillanguage = (isset($maillanguage) && $maillanguage != '') ? $maillanguage : 'EN';

            $mailsubject = $res[0]['vSubject_' . $maillanguage];
            $tMessage = $res[0]['vBody_' . $maillanguage];  
            $tMessage = str_replace($key_arr, $val_arr, $tMessage);
            return $tMessage;
    }

    function get_user_preffered_language($vEmail) {
         global $obj, $tconfig;
        $sql = "select vLang from register_user where vEmail ='" . $vEmail . "'";
        $res = $obj->MySQLSelect($sql);
        $preflang = "EN";

        if (count($res) > 0) {
            $preflang = $res[0]['vLang'];
        }
        return $preflang;
    }
    function getConfigurations($tabelName, $LABEL) {
        global $obj;

        $sql = "SELECT vValue FROM `" . $tabelName . "` WHERE vName='$LABEL'";
        $Data = $obj->MySQLSelect($sql);
        $Data_value = $Data[0]['vValue'];
        return $Data_value;
    } 
  
    function sendCode($mobileNo,$code,$fpass,$pass=''){
        global $site_path;
        // $mobileNo=$this->clearPhone($mobileNo);
        // $mobileNo=$code.$mobileNo;

        require_once(TPATH_CLASS .'twilio/Services/Twilio.php');

        $account_sid = getConfigurations("configurations","MOBILE_VERIFY_SID_TWILIO");
        $auth_token = getConfigurations("configurations","MOBILE_VERIFY_TOKEN_TWILIO");
        $twilioMobileNum= getConfigurations("configurations","MOBILE_NO_TWILIO");

        $client = new Services_Twilio($account_sid, $auth_token);

        $toMobileNum= "+".$code.$mobileNo;      
        
        //echo $twilioMobileNum;
        //echo $toMobileNum;
        //echo $fpass;
        try{
            $sms = $client->account->messages->sendMessage($twilioMobileNum,$toMobileNum,$fpass);
            $returnArr['action'] ="1";
           // echo "<pre>";print_r($sms);exit;
        } catch (Services_Twilio_RestException $e) {
            $returnArr['action'] ="0";
           // echo "<pre>";print_r($e);
           // echo "<pre>";print_r($returnArr);exit;
        } 
        $returnArr['verificationCode'] =$code;
        return $returnArr;
    }


    function converToTz($time, $toTz, $fromTz,$dateFormat="Y-m-d H:i:s") {
		$date = new DateTime($time, new DateTimeZone($fromTz));
		$date->setTimezone(new DateTimeZone($toTz));
		$time = $date->format($dateFormat);
		return $time;
	}    
?>