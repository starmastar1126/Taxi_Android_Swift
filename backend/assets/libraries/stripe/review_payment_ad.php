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
  
  $advisorsobj->member_session_check();
  $iMemberId=$_SESSION['sess_iMemberId'];
  
  $sql = "SELECT vStripeCustomerId FROM member WHERE iMemberId = '".$iMemberId."'";
  $db_customer_details = $obj->MySQLSelect($sql);
  $sql = "SELECT subscr_id FROM member_plan_purchase WHERE iMemberId = '".$iMemberId."' AND subscr_id<>'' order by iMemberPurchPlanId desc limit 0,1";
  $db_member_purcahse_plan = $obj->MySQLSelect($sql);
  
  if($_SESSION['selected_plan'] == ''){
    header("Location: ".$tconfig["tsite_url"]."advisor-login");
	  exit;
  }else{
    if($_SESSION['selected_plan']=="1"){
      $plan_id="basic_monthly";
    }else if($_SESSION['selected_plan']=="2"){
      $plan_id="basic_yearly";
    }else if($_SESSION['selected_plan']=="3"){
      $plan_id="premium_monthly";
    }else if($_SESSION['selected_plan']=="4"){
      $plan_id="premium_yearly";
    }else if($_SESSION['selected_plan']=="5"){
      $plan_id="platinum_monthly";
    }else if($_SESSION['selected_plan']=="6"){
      $plan_id="platinum_yearly";
    }
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
      $sql = "SELECT * FROM member_plan WHERE iMemberPlanId = '".$_SESSION['selected_plan']."'";
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
      if($Data_pay['ePlanType']=='Y')
      $Data_pay['dExpireDate'] = date("Y-m-d",mktime(0, 0, 0, date("m",strtotime($Data_pay['dActivationDate'] )),   date("d",strtotime($Data_pay['dActivationDate'] )),   date("Y",strtotime($Data_pay['dActivationDate'] ))+ $Data_pay['iDuration']));
      if($Data_pay['ePlanType']=='M')
      $Data_pay['dExpireDate'] = date("Y-m-d",mktime(0, 0, 0, date("m",strtotime($Data_pay['dActivationDate']))+ $Data_pay['iDuration'] ,   date("d",strtotime($Data_pay['dActivationDate'] )),   date("Y",strtotime($Data_pay['dActivationDate'] ))));
      if($Data_pay['ePlanType']=='D')
      $Data_pay['dExpireDate'] = date("Y-m-d",mktime(0, 0, 0, date("m",strtotime($Data_pay['dActivationDate'] )),   date("d",strtotime($Data_pay['dActivationDate']))+ $Data_pay['iDuration'],   date("Y",strtotime($Data_pay['dActivationDate'] ))));
     
      $Data_pay['iMemberId']=$iMemberId;
      //echo "<pre>";print_r($Data_pay);exit;
      $iProjectPaymentId = $obj->MySQLQueryPerform("member_plan_purchase",$Data_pay,'insert');
      if($iProjectPaymentId){
      //  header("Location:".$tconfig['tsite_url'].'index.php?file=c-resp&x_response_code=1&cid='.$_SESSION['sess_iCustomerId'].'&mid='.$iProjectPaymentId);
      //  exit;
        $token = $_POST['stripeToken'];
        $email = $_POST['stripeEmail'];
        try {
         if($db_customer_details[0]['vStripeCustomerId']=='' && count($db_member_purcahse_plan)<=0){
            $customer = Stripe_Customer::create(array(
              "source" => $token,
              "plan" => $plan_id,
              "email" => $email)
            );
            $vStripeCustomerId=$customer->id;
            $Data2['vStripeCustomerId'] = $vStripeCustomerId;     
            $where = " iMemberId = '".$iMemberId."'";
            $res1 = $obj->MySQLQueryPerform("member",$Data2,'update',$where);
         }else{
              $cu = Stripe_Customer::retrieve($db_customer_details[0]['vStripeCustomerId']);
              $subscription = $cu->subscriptions->retrieve($db_member_purcahse_plan[0]['subscr_id']);
              $subscription->plan = $plan_id;
              $subscription->save();
         }
        }catch (Stripe_CardError $e) {
          $message .= '<p>Your Card Information is wrong. Please try again.<br><br>If you have any questions, please do not hesitate to contact us.</p>';
          $title = 'Payment Fail';
          header("Location:".$tconfig["tsite_url"]."index.php?file=c-resp&x_response_code=3");
          exit;
        }
        header("Location:".$tconfig["tsite_url"]."index.php?file=c-resp&x_response_code=1");
        exit;
      }
    }
  }  
  $sql="SELECT member_plan_master.vPlanName, member_plan_master.tDescription, member_plan.iMemberPlanId, member_plan.fPrice, member_plan.ePlanFor, member_plan.eType, member_plan.eStatus, member_plan.iDuration, member_plan.iBidConnects 
        FROM member_plan 
        LEFT JOIN member_plan_master ON member_plan_master.iMemberMastrePlanId = member_plan.iMemberMastrePlanId
        WHERE member_plan.iMemberPlanId = '".$_SESSION['selected_plan']."' ORDER BY member_plan.iMemberPlanId ASC $var_limit";	
  $db_member_plan = $obj->MySQLSelect($sql);
  
  $nwarr = array();
  $nwarr['vFirstName'] = $_POST['vFirstName'];
  $nwarr['vLastName'] = $_POST['vLastName'];
  $nwarr['vEmail'] = $_POST['vEmail'];
  $nwarr['vPhone'] = $_POST['vPhone'];
  $nwarr['vAddress'] = $_POST['vAddress'];
  $nwarr['vCountry'] = $_POST['vCountry'];
  $nwarr['vState'] = $_POST['vState'];
  $nwarr['vCity'] = $_POST['vCity'];
  $nwarr['vZip'] = $_POST['vZip'];
  
  $_SESSION['billing_details'] = $nwarr;
  $_SESSION['complete_billing'] = 'Yes';
  $smarty->assign('db_member_plan', $db_member_plan);   
?>