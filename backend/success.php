<?php 
   include_once("common.php");
   $success = (isset($_REQUEST['success'])) ? $_REQUEST['success'] : '0';    // Facebook,Twitter,Google
   if($success == 1){ 
    echo json_encode($_REQUEST);  exit;
     //echo "<pre>";print_r($_REQUEST);exit;
   }else{            
    echo json_encode($_REQUEST);  exit;
     //echo "<pre>";print_r($_REQUEST);exit;
   }
?>
