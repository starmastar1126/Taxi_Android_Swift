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
  
  $profbj->member_session_check();
  $iMemberId = $_SESSION['sess_iMemberId'];
  
  $sql = "SELECT * FROM member WHERE iMemberId = '".$iMemberId."'";
  $db_customer_details = $obj->MySQLSelect($sql);
  
  if($_SESSION['selected_iProfessionalplanid'] == ''){
    header("Location: ".$tconfig["tsite_url"]."companies-login");
	  exit;
  }
  
  if($_POST['action'] == 'pay_now'){    
    $Data['vBFirstName'] = $_SESSION['billing_details']['vFirstName'];
    $Data['vBLastName'] = $_SESSION['billing_details']['vLastName'];
    $Data['vBEmail'] = $_SESSION['billing_details']['vEmail'];
    $Data['vBPhone'] = $_SESSION['billing_details']['vPhone'];
    $Data['vBAddress'] = $_SESSION['billing_details']['vAddress'];
    $Data['vBZip'] = $_SESSION['billing_details']['vZip'];
    $Data['vBCountry'] = $_SESSION['billing_details']['vCountry'];
    $Data['vBState'] = $_SESSION['billing_details']['vState'];
    $Data['vBCity'] = $_SESSION['billing_details']['vCity'];     
    $where = " iMemberId = '".$iMemberId."'";
    $res = $obj->MySQLQueryPerform("member",$Data,'update',$where); 
    if($res){                       
      $sql = "SELECT * FROM professional_plans WHERE iProfessionalplanid = '".$_SESSION['selected_iProfessionalplanid']."'";
      $db_plans = $obj->MySQLSelect($sql);
      
      $Data_pay['iProjectId'] = $_SESSION['project_post_id'];
      $Data_pay['iProfessionalplanid'] = $_SESSION['selected_iProfessionalplanid'];
      $Data_pay['vPlanName'] = $db_plans[0]['vPlanName'];
      $Data_pay['fPrice'] = $db_plans[0]['fPrice'];
      $Data_pay['iBids'] = $db_plans[0]['iBids'];
      $Data_pay['eUnlimited'] = $db_plans[0]['eUnlimited'];
      $Data_pay['iDays'] = $db_plans[0]['iDays'];
      $Data_pay['eStatus'] = 'Pending';
      $Data_pay['dAddedDate'] = date('Y-m-d H:i:s');
      $Data_pay['iMemberId']=$iMemberId;
      $iProjectPaymentId = $obj->MySQLQueryPerform("projects_payments",$Data_pay,'insert');
      if($iProjectPaymentId){
       // header("Location:".$tconfig["tsite_url"]."index.php?file=pr-resp_services&x_response_code=1&cid='".$_SESSION['project_post_id'].'_'.$_SESSION['selected_iProfessionalplanid'].'_'.$_SESSION['sess_iMemberId']."'");
       // exit;
        $token  = $_POST['stripeToken'];
        $email  = $_POST['stripeEmail'];
        try {
         if($db_customer_details[0]['vStripeCustomerId']==''){
            $customer = Stripe_Customer::create(array(
                'email' => $email,
                'card'  => $token
            ));
            $vStripeCustomerId=$customer->id;
          }else{
            $vStripeCustomerId=$db_customer_details[0]['vStripeCustomerId'];
          }
          
          $charge = Stripe_Charge::create(array(
              'customer' => $vStripeCustomerId,
              'amount'   => $db_plans[0]['fPrice']*100,
              'currency' => 'usd',
              'description' => $db_plans[0]['vPlanName']
          ));
        }catch (Stripe_CardError $e) {
          $message .= '<p>Soething goes worng. Please try again.<br><br>If you have any questions, please do not hesitate to contact us.</p>';
          $title = 'Payment Fail';
          header("Location:".$tconfig["tsite_url"]."index.php?file=pr-resp_services&x_response_code=3");
          exit;
        }
          #echo "<pre>"; print_r($charge); exit;
          $details = json_decode($charge);
          $result = get_object_vars($details);
          if($result['status']=="succeeded" && $result['paid']=="1"){
            $Data1['eStatus'] = 'Active';
            $Data1['vTransactionId'] = $result['balance_transaction'];
            $where = " iProjectPaymentId = '".$iProjectPaymentId."'";
            $res = $obj->MySQLQueryPerform("projects_payments",$Data1,'update',$where);
            
            $Data2['vStripeCustomerId'] = $vStripeCustomerId;     
            $where = " iMemberId = '".$iMemberId."'";
            $res1 = $obj->MySQLQueryPerform("member",$Data2,'update',$where);
          //  $sql = "SELECT * FROM member WHERE iMemberId = '".$iMemberId."'";
          //  $db_customer_details = $obj->MySQLSelect($sql);
            if($res){
              $date = date("Y-m-d H:i:s");
              $dLastDateShow = date("Y-m-d H:i:s", strtotime($date ." +".$db_plans[0]['iDays']." day"));
              $Data_project['dLastDateShow'] = $dLastDateShow;
              $Data_project['iBidsForshow'] = $db_plans[0]['iBids'];
              $Data_project['eUnlimited'] = $db_plans[0]['eUnlimited'];
              $Data_project['dPostedDate'] = $date;
              $Data_project['ePosted'] = 'Yes';
              $Data_project['eStatus'] = 'Active';
              $where = " iProjectId = '".$_SESSION['project_post_id']."' AND iMemberId = '".$iMemberId."'";
              $res_project = $obj->MySQLQueryPerform("projects",$Data_project,'update',$where);
              if($res_project){              
                $cont_customer .= 'Hello, '.$db_customer_details[0]['vCompanyName'];
                $cont_customer .= '<br>Thank you for choosing to services with iAccounOn! Please keep this email as it contains important information you may need.';
                $cont_customer .= '<br><br>Belows are details of plan you purchased.';
                $cont_customer .= '<br>-------------------------------------------';
                $cont_customer .= '<br><b>Name:</b> '.$db_plans[0]['vPlanName'];
                $cont_customer .= '<br><b>Price:</b> '.$generalobj->Make_Currency($db_plans[0]['fPrice']);
                if($db_plans[0]['iBids']==0 && $db_plans[0]['eUnlimited']=='Yes') 
                $cont_customer .= '<br><b>Bids:</b>Unlimited';
                else
                $cont_customer .= '<br><b>Bids:</b> '.$db_plans[0]['iBids'];
                $Data_email_cust['DETAILS'] = $cont_customer;
                $Data_email_cust['EMAIL'] = $db_customer_details[0]['vEmail'];
                $generalobj->send_email_user('NEW_PLAN_PURCHASE_CUSTOMER', $Data_email_cust);
               
                $cont_admin .= 'Hello, Admin';
                $cont_admin .= '<br>Successful payment of member from iAccountOn.';             
                $cont_admin .= '<br><br>Belows are details of plan purchased.';
                $cont_admin .= '<br>-------------------------------------------';
                $cont_admin .= '<br><b>Name:</b> '.$db_plans[0]['vPlanName'];
                $cont_admin .= '<br><b>Price:</b> '.$generalobj->Make_Currency($db_plans[0]['fPrice']);
                if($db_plans[0]['iBids']==0 && $db_plans[0]['eUnlimited']=='Yes') 
                $cont_customer .= '<br><b>Bids:</b>Unlimited';
                else
                $cont_customer .= '<br><b>Bids:</b> '.$db_plans[0]['iBids'];
                
                $cont_admin .= '<br><br>Belows are details of member.';
                $cont_admin .= '<br>-------------------------------------------';
                $cont_admin .= '<br><b>Name:</b> '.$db_customer_details[0]['vCompanyName'];
                
                $cont_admin .= '<br><b>Email:</b> '.$db_customer_details[0]['vEmail'];
                $cont_admin .= '<br><b>Phone:</b> '.$db_customer_details[0]['vPhone'];
                
                $Data_email_admin['DETAILS'] = $cont_admin;
                $Data_email_admin['Email'] = $db_customer_details[0]['vEmail'];
                $generalobj->send_email_user('NEW_PLAN_PURCHASE_ADMIN', $Data_email_admin);
                
                
                $message .= '<p>Thank you for payment. Your payment has been successfully completed. You will receive an email confirmation shortly.<br><br>If you have any questions, please do not hesitate to contact us.</p>';
                $title = 'Payment Success'; 
                header("Location:".$tconfig["tsite_url"]."index.php?file=pr-resp_services&x_response_code=1");
                exit;
              }
              }else{
                $message .= '<p>Soething goes worng. Please try again.<br><br>If you have any questions, please do not hesitate to contact us.</p>';
                $title = 'Payment Fail';
                header("Location:".$tconfig["tsite_url"]."index.php?file=pr-resp_services&x_response_code=2");
                exit;
              }
          }else{
            $message .= '<p>Soething goes worng. Please try again.<br><br>If you have any questions, please do not hesitate to contact us.</p>';
            $title = 'Payment Fail';
            header("Location:".$tconfig["tsite_url"]."index.php?file=pr-resp_services&x_response_code=3");
            exit;
          }  
      }else{
        $message .= '<p>Soething goes worng. Please try again.<br><br>If you have any questions, please do not hesitate to contact us.</p>';
        $title = 'Payment Fail';
        header("Location:".$tconfig["tsite_url"]."index.php?file=pr-resp_services&x_response_code=3");
        exit;
      }
    }else{
      $message .= '<p>Soething goes worng. Please try again.<br><br>If you have any questions, please do not hesitate to contact us.</p>';
      $title = 'Payment Fail';
      header("Location:".$tconfig["tsite_url"]."index.php?file=pr-resp_services&x_response_code=3");
      exit;
    }     
    exit;
  }
  
?>