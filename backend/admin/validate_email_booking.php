<?php 
	include_once('../common.php');
	//$iCompanyId=$_REQUEST['iCompanyId'];
	//$iDriverId=$_REQUEST['iDriverId'];
	$iUserId=$_REQUEST['id'];	
	
	if($iUserId!='')
	{
	    $ssql2=" AND iUserId !='".$iUserId."' "; 
	}
	else
	{
		$ssql2=" ";		
	}
	
	if(isset($_REQUEST['id']))
	{
		$email=$_REQUEST['id'];
		   
		$sql4 = "SELECT * FROM register_user WHERE vEmail = '".$email."'"; 
	    $db_river = $obj->MySQLSelect($sql4);
				
		if(count($db_river)>0)
		{
			if($db_river[0]['eStatus']=='Deleted' || $db_river[0]['eStatus']=='Inactive')
				{
						echo 0;
				}
			    else
				{       
						 echo 1;
				}
		}
		else
		{	
				echo 1;
		}		
	}
?>