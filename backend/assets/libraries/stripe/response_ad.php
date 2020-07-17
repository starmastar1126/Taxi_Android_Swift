<?php  

  ob_start();      
  session_start();
  define( '_TEXEC', 1 );
  define('TPATH_BASE', '/home3/eswdemo/public_html/accounting_hub' );
  define( 'DS', '/' );
  
  require_once ( '../includes/defines.php' ); 
  require_once ( '../includes/configuration.php' );
  
  require_once('config.php');
  require_once('stripe-php-2.1.4/lib/Stripe.php');
  
  
  $body = @file_get_contents('php://input');
  $event_json = json_decode($body);
  $result = get_object_vars($event_json);
 # echo "<pre>"; print_r($result); exit;  
  $result_data = get_object_vars($result['data']);
  $result_plan_cust = get_object_vars($result_data['object']);
  #echo "<pre>"; print_r($result_plan_cust); exit;
  
  $sql = "SELECT * FROM member WHERE vStripeCustomerId = '".$result_plan_cust['customer']."'";
  $db_customer = $obj->MySQLSelect($sql);
  /*     
  $customer_id = $event_json->data->object->customer;
  $customer = Stripe_Customer::retrieve($customer_id);
 
  $charge_id = $event_json->data->object->id;
  $charge = Stripe_Charge::retrieve($charge_id);
  $last4 = $charge->card->last4;
  $type = $charge->card->type;
  $exp_month = $charge->card->exp_month;
  $exp_year = $charge->card->exp_year;
  $description = $customer->description;
  $amount = $charge->amount/100;
  $currency = $charge->currency;
  */
   $iMemberId = $db_customer[0]['iMemberId'];
   if($result['type']=="invoice.created"){
      
      $sql = "SELECT subscr_id,iMemberPlanId FROM member_plan_purchase WHERE iMemberId = '".$iMemberId."' AND subscr_id<>'' order by iMemberPurchPlanId desc limit 0,1";
      $db_member_purcahse_plan = $obj->MySQLSelect($sql);
      
      if(count($db_member_purcahse_plan)>0){
        if($db_member_purcahse_plan[0]['iMemberPlanId']=="1"){
          $plan_id="basic_monthly";
        }else if($db_member_purcahse_plan[0]['iMemberPlanId']=="2"){
          $plan_id="basic_yearly";
        }else if($db_member_purcahse_plan[0]['iMemberPlanId']=="3"){
          $plan_id="premium_monthly";
        }else if($db_member_purcahse_plan[0]['iMemberPlanId']=="4"){
          $plan_id="premium_yearly";
        }else if($db_member_purcahse_plan[0]['iMemberPlanId']=="5"){
          $plan_id="platinum_monthly";
        }else if($db_member_purcahse_plan[0]['iMemberPlanId']=="6"){
          $plan_id="platinum_yearly";
        }
     }else{
       exit;
     }
      $sql = "SELECT * FROM member_plan WHERE iMemberPlanId = '".$db_member_purcahse_plan[0]['iMemberPlanId']."'";
      $db_plans = $obj->MySQLSelect($sql);
      
      $Data_pay['iMemberPlanId'] = $db_plans[0]['iMemberPlanId'];
      
      $Data_pay['vPlanName'] = $db_plans[0]['vPlanName'];
      $Data_pay['fPrice'] = $db_plans[0]['fPrice'];
      $Data_pay['iDuration'] = $db_plans[0]['iDuration'];
      $Data_pay['eType'] = 'Regular';
      $Data_pay['iBidConnects'] = $db_plans[0]['iBidConnects'];
      $Data_pay['ePlanType'] = $db_plans[0]['eType'];
      $Data_pay['eStatus'] = 'Pending';
      $Data_pay['dPurchaseDate'] = date('Y-m-d H:i:s');
      $Data_pay['dActivationDate'] = date('Y-m-d H:i:s');
      $Data_pay['iMemberId']=$iMemberId;
      $iProjectPaymentId = $obj->MySQLQueryPerform("member_plan_purchase",$Data_pay,'insert');
    exit;
   }
   
   if($result['type']=="invoice.payment_succeeded"){
      $result_plan_cust1 = get_object_vars($result_plan_cust['lines']);  
      $result_plan_cust2 = get_object_vars($result_plan_cust1['data'][0]);
     # echo $result_plan_cust2['id']; exit;
      $sql = "SELECT iMemberPurchPlanId, subscr_id FROM member_plan_purchase WHERE iMemberId = '".$iMemberId."' ORDER BY iMemberPurchPlanId DESC LIMIT 0,1";
      $db_plan_purchase_details = $obj->MySQLSelect($sql); 
      $Data1['subscr_id'] = $result_plan_cust2['id'];
      $where = " iMemberPurchPlanId = '".$db_plan_purchase_details[0]['iMemberPurchPlanId']."'";
      $res = $obj->MySQLQueryPerform("member_plan_purchase",$Data1,'update',$where);
      exit;
   }
   
   if($result['type']=="charge.succeeded"){
    $x_response_code = $result_plan_cust['status'];
    $status="succeeded"; 
    
    if($x_response_code == $status){   
     
      $sql = "SELECT iMemberPurchPlanId, subscr_id FROM member_plan_purchase WHERE iMemberId = '".$iMemberId."' ORDER BY iMemberPurchPlanId DESC LIMIT 0,1";
      $db_plan_purchase_details = $obj->MySQLSelect($sql);
      
      if(count($db_plan_purchase_details) > 0 && $db_plan_purchase_details['subscr_id']==""){        
        $payment_type = 'Old';
      }else{
        $payment_type = 'New';
      }
      $iMemberPlanId=$db_plan_purchase_details[0]['iMemberPlanId'];
      $sql = "SELECT * FROM member_plan WHERE 1=1 AND iMemberPlanId = '".$iMemberPlanId."'";
      $db_plan_details = $obj->MySQLSelect($sql);
      $sql = "SELECT * FROM member_plan_master WHERE iMemberMastrePlanId = '".$db_plan_details[0]['iMemberMastrePlanId']."'";
      $db_master_plan_details = $obj->MySQLSelect($sql);
      $sql = "SELECT * FROM member_plan_purchase order by iMemberPurchPlanId DESC limit 0,1";
      $db_plans = $obj->MySQLSelect($sql);
      
      if($db_plans[0]['eStatus'] == 'Pending'){        
       // $Data['subscr_id'] = $result_plan_cust['id'];
        $Data['eStatus'] = 'Approved';
        $date= $db_plans[0]['dActivationDate'];
        $Data['eProfileStatus'] = 'Payment';
        if($db_plans[0]['ePlanType']=='Y'){
          $TYPE = 'Yearly';
          $Data['dExpireDate'] = date("Y-m-d",mktime(0, 0, 0, date("m",strtotime($date)),   date("d",strtotime($date )),   date("Y",strtotime($date ))+ $db_plans[0]['iDuration']));
        }
        if($db_plans[0]['ePlanType']=='M'){
          $TYPE = 'Monthly';
          $Data['dExpireDate'] = date("Y-m-d",mktime(0, 0, 0, date("m",strtotime($date))+ $db_plans[0]['iDuration'] ,   date("d",strtotime($date )),   date("Y",strtotime($date))));
        }
        if($db_plans[0]['ePlanType']=='D'){
          $TYPE = 'Daily';
          $Data['dExpireDate'] = date("Y-m-d",mktime(0, 0, 0, date("m",strtotime($date )),   date("d",strtotime($date))+ $db_plans[0]['iDuration'],   date("Y",strtotime($date))));
        }  
        
      
        $where = " iMemberPurchPlanId = '".$db_plans[0]['iMemberPurchPlanId']."'";
        $res = $obj->MySQLQueryPerform("member_plan_purchase",$Data,'update',$where);
        if($res){
          $sql = "UPDATE member SET iBidConnects = iBidConnects + ".$db_plan_details[0]['iBidConnects']." WHERE iMemberId = '".$iMemberId."'";
          $db_sql = $obj->sql_query($sql); 
          
          $sql = "SELECT * FROM member WHERE iMemberId = '".$iMemberId."'";
          $db_customer_details = $obj->MySQLSelect($sql);
          
          if($payment_type == 'New'){
            #new plane purchase
            if($db_customer_details[0]['eType'] == 'Individual'){
              $cont_customer .= 'Hello, '.$db_customer_details[0]['vFirstName'].' '.$db_customer_details[0]['vLastName'];
            }else{
              $cont_customer .= 'Hello, '.$db_customer_details[0]['vCompanyName'];
            }
            
            $cont_customer .= '<br>Thank you for choosing to services with iAccounOn! Please keep this email as it contains important information you may need.';
            $cont_customer .= '<br>You are currently enrolled in our '.$TYPE.' plan.';
            $cont_customer .= '<br><br>Belows are details of plan you purchased.';
            $cont_customer .= '<br>-------------------------------------------';
            $cont_customer .= '<br><b>Name:</b> '.$db_master_plan_details[0]['vPlanName'];
            $cont_customer .= '<br><b>Price:</b> '.$generalobj->Make_Currency($db_plan_details[0]['fPrice']);
            $cont_customer .= '<br><b>Bids:</b> '.$db_plan_details[0]['iBidConnects'];
            $cont_customer .= '<br><b>Activation Date:</b> '.$generalobj->DateTime($date,9);
            $cont_customer .= '<br><b>Expire Date:</b> '.$generalobj->DateTime($Data['dExpireDate'],9); 
            
            $Data_email_cust['DETAILS'] = $cont_customer;
            $Data_email_cust['EMAIL'] = $db_customer_details[0]['vEmail'];
            $generalobj->send_email_user('NEW_PLAN_PURCHASE_ADVISOR', $Data_email_cust);
            
            #-----------------------------------------------------#
            
            $cont_admin .= 'Hello, Admin';
            $cont_admin .= '<br>Successful payment of member from iAccountOn.';             
            $cont_admin .= '<br><br>Belows are details of plan purchased.';
            $cont_admin .= '<br>-------------------------------------------';
            $cont_admin .= '<br><b>Name:</b> '.$db_master_plan_details[0]['vPlanName'];
            $cont_admin .= '<br><b>Price:</b> '.$generalobj->Make_Currency($db_plan_details[0]['fPrice']);
            $cont_admin .= '<br><b>Bids:</b> '.$db_plan_details[0]['iBidConnects'];
            $cont_admin .= '<br><b>Activation Date:</b> '.$generalobj->DateTime($date,9);
            $cont_admin .= '<br><b>Expire Date:</b> '.$generalobj->DateTime($Data['dExpireDate'],9); 
            $cont_admin .= '<br><br>Belows are details of member.';
            $cont_admin .= '<br>-------------------------------------------';
            if($db_customer_details[0]['eType'] == 'Individual'){
              $cont_admin .= $db_customer_details[0]['vFirstName'].' '.$db_customer_details[0]['vLastName'];
            }else{
              $cont_admin .= $db_customer_details[0]['vCompanyName'];
            }
            $cont_admin .= '<br><b>Email:</b> '.$db_customer_details[0]['vEmail'];
            $cont_admin .= '<br><b>Phone:</b> '.$db_customer_details[0]['vPhone'];
            
            $Data_email_admin['DETAILS'] = $cont_admin;
             $Data_email_admin['Email'] = $db_customer_details[0]['vEmail'];
            $generalobj->send_email_user('NEW_PLAN_PURCHASE_ADVISOR_ADMIN', $Data_email_admin);              
          }else{
            #payment
            if($db_customer_details[0]['eType'] == 'Individual'){
              $cont_customer .= 'Hello, '.$db_customer_details[0]['vFirstName'].' '.$db_customer_details[0]['vLastName'];
            }else{
              $cont_customer .= 'Hello, '.$db_customer_details[0]['vCompanyName'];
            }
            $cont_customer .= '<br>Your payment has been subcribed successfully on iAccountOn.';
            $cont_customer .= '<br>You are currently enrolled in our '.$TYPE.' plan.';
            $cont_customer .= '<br><br>Belows are details of plan you purchased.';
            $cont_customer .= '<br>-------------------------------------------';
            $cont_customer .= '<br><b>Name:</b> '.$db_master_plan_details[0]['vPlanName'];
            $cont_customer .= '<br><b>Price:</b> '.$generalobj->Make_Currency($db_plan_details[0]['fPrice']);
            $cont_customer .= '<br><b>Bids:</b> '.$db_plan_details[0]['iBidConnects'];
            $cont_customer .= '<br><b>Activation Date:</b> '.$generalobj->DateTime($date,9);
            $cont_customer .= '<br><b>Expire Date:</b> '.$generalobj->DateTime($Data['dExpireDate'],9); 
            
            $Data_email_cust['DETAILS'] = $cont_customer;
            $Data_email_cust['EMAIL'] = $db_customer_details[0]['vEmail'];
            $generalobj->send_email_user('RENEW_PLAN_PURCHASE_CUSTOMER', $Data_email_cust);
            
            #-----------------------------------------------------#
            
            $cont_admin .= 'Hello, Admin';
            $cont_admin .= '<br>Successful payment of customer from iAccountOn.';             
            $cont_admin .= '<br><br>Belows are details of plan purchased.';
            $cont_admin .= '<br>-------------------------------------------';
            $cont_admin .= '<br><b>Name:</b> '.$db_master_plan_details[0]['vPlanName'];
            $cont_admin .= '<br><b>Price:</b> '.$generalobj->Make_Currency($db_plan_details[0]['fPrice']);
            $cont_admin .= '<br><b>Bids:</b> '.$db_plan_details[0]['iBidConnects'];
            $cont_admin .= '<br><b>Activation Date:</b> '.$generalobj->DateTime($date,9);
            $cont_admin .= '<br><b>Expire Date:</b> '.$generalobj->DateTime($Data['dExpireDate'],9); 
            $cont_admin .= '<br><br>Belows are details of customer.';
            $cont_admin .= '<br>-------------------------------------------';
            if($db_customer_details[0]['eType'] == 'Individual'){
              $cont_admin .= $db_customer_details[0]['vFirstName'].' '.$db_customer_details[0]['vLastName'];
            }else{
              $cont_admin .= $db_customer_details[0]['vCompanyName'];
            }
            $cont_admin .= '<br><b>Email:</b> '.$db_customer_details[0]['vEmail'];
            $cont_admin .= '<br><b>Phone:</b> '.$db_customer_details[0]['vPhone'];
            
            $Data_email_admin['DETAILS'] = $cont_admin;
            $Data_email_admin['Email'] = $db_customer_details[0]['vEmail'];
            $generalobj->send_email_user('RENEW_PLAN_PURCHASE_ADMIN', $Data_email_admin);               
          }
        //  $message .= '<p>Thank you for payment. Your payment has been successfully completed. You will receive an email confirmation shortly.<br><br>If you have any questions, please do not hesitate to contact us.</p>';
        //  $title = 'Payment Success';
          exit;
        }else{
           // $message .= '<p>Something1 goes worng. Please try again.<br><br>If you have any questions, please do not hesitate to contact us.</p>';
           // $title = 'Payment Fail';
           exit;
        } 
      }else{
          //$message .= '<p>Something2 goes worng. Please try again.<br><br>If you have any questions, please do not hesitate to contact us.</p>';
          //$title = 'Payment Fail';
          exit;
      }
    }
   }
   
   if($result['type']=="customer.subscription.deleted"){
      $x_response_code = $result_plan_cust['status'];
      $status="canceled";       
      if($x_response_code == $status){ 
        $sql = "UPDATE member SET vStripeCustomerId='' WHERE iMemberId = '".$iMemberId."'";
        $db_sql = $obj->sql_query($sql);
        
        $sql = "SELECT iMemberPurchPlanId, subscr_id FROM member_plan_purchase WHERE iMemberId = '".$iMemberId."' ORDER BY iMemberPurchPlanId DESC LIMIT 0,1";
        $db_plan_purchase_details = $obj->MySQLSelect($sql);
        
        $sql = "UPDATE member_plan_purchase SET subscr_id='' WHERE iMemberPurchPlanId = '".$db_plan_purchase_details[0]['iMemberPurchPlanId']."'";
        $db_sql = $obj->sql_query($sql);
      }
   }
   exit;
?>

