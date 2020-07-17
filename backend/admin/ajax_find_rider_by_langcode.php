<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$iUserId = isset($_REQUEST['uid']) ? $_REQUEST['uid'] : '';
$iUserPetId = isset($_REQUEST['iUserPetId']) ? $_REQUEST['iUserPetId'] : '';  
$sql2 ="select iPetTypeId from  user_pets where iUserPetId ='".$iUserPetId."'";
$db_res_user_pets = $obj->MySQLSelect($sql2);

$sql = "SELECT vLang FROM register_user where iUserId='".$iUserId."'";  
$db_register_user = $obj->MySQLSelect($sql);

if (count($db_register_user) > 0) {

	 	  $sql = "SELECT iPetTypeId,vTitle_".$db_register_user[0]['vLang']." FROM pet_type  where eStatus='Active'"; 
 	      $db_pettype = $obj->MySQLSelect($sql);
 	      for($i=0;$i<count($db_pettype);$i++){


 	      	if($db_res_user_pets[0]['iPetTypeId'] == $db_pettype[$i]['iPetTypeId'] ){
 	      		$selected = "selected=selected";

 	      		 $code = "vTitle_".$db_register_user[0]['vLang']; 
 	      		  $db_pettype[$i][$code];
 	      		echo "<option value=".$db_pettype[$i]['iPetTypeId']." ".$selected.">".$db_pettype[$i][$code]."</option>";
 	      	}else{
 	      	
 	      		 $code = "vTitle_".$db_register_user[0]['vLang']; 
 	      		  $db_pettype[$i][$code];
 	      		echo "<option value=".$db_pettype[$i]['iPetTypeId'].">".$db_pettype[$i][$code]."</option>";	

 	      	}	
 	      	
 	         }
 	      exit;

}

?>