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
  
  if(isset($_POST['type']) && $_POST['type']=="cancel"){
    $sql = "SELECT iMemberPurchPlanId, subscr_id FROM member_plan_purchase WHERE iMemberId = '".$iMemberId."' ORDER BY iMemberPurchPlanId DESC LIMIT 0,1";
    $db_plan_purchase_details = $obj->MySQLSelect($sql);                                         
    $cu = Stripe_Customer::retrieve($db_customer_details[0]['vStripeCustomerId']);
    $cu->subscriptions->retrieve($db_plan_purchase_details[0]['subscr_id'])->cancel();  
    header("Location:".$tconfig["tsite_url"]."index.php?file=c-resp&x_response_code=4");
    exit;                                                                                
  }else if(isset($_POST['type']) && $_POST['type']=="upgrade"){
    $sql = "SELECT iMemberPurchPlanId, subscr_id FROM member_plan_purchase WHERE iMemberId = '".$iMemberId."' ORDER BY iMemberPurchPlanId DESC LIMIT 0,1";
    $db_plan_purchase_details = $obj->MySQLSelect($sql);                                         
    $cu = Stripe_Customer::retrieve($db_customer_details[0]['vStripeCustomerId']);
    $cu->subscriptions->retrieve($db_plan_purchase_details[0]['subscr_id'])->cancel();  
    header("Location:".$tconfig["tsite_url"]."index.php?file=ad-membership_plans");
    exit;
  }else{
    header("Location:".$tconfig["tsite_url"]."index.php?file=c-resp&x_response_code=5");
    exit;
  }
  
  exit;
?>

