<?php 
	include_once('../common.php');
	$iCompanyId=$_REQUEST['iCompanyId'];
	//$iDriverId=$_REQUEST['iDriverId'];
	$iDriverId=$_REQUEST['id'];
	$iUserId=$_REQUEST['iUserId'];
	if($iCompanyId !='')
	{
		$ssql=" AND iCompanyId !='".$iCompanyId."'";
	}
	else
	{
		$ssql=" ";
	}
	
	if($iDriverId!='')
	{
		//$ssql1=" AND iDriverId !='".$iDriverId."' AND eStatus ==  'Deleted'";
		$ssql1=" AND iDriverId !='".$iDriverId."' ";
	}
	else
	{
		$ssql1=" ";		
	}
	
	if($iUserId!='')
	{
	    $ssql2=" AND iUserId !='".$iUserId."' "; //die;
	}
	else
	{
		$ssql2=" ";		
	}
	
	if(isset($_REQUEST['id']))
	{
			$email=$_REQUEST['id'];
		    $sql = "SELECT * FROM company WHERE vEmail = '".$email."'".$ssql; 
			$db_comp = $obj->MySQLSelect($sql);
			//print_r($db_driver); //die;
			
		    $sql3 = "SELECT * FROM register_driver WHERE vEmail = '".$email."'".$ssql1; 
			$db_driver = $obj->MySQLSelect($sql3);
			
			
		    $sql4 = "SELECT * FROM register_user WHERE vEmail = '".$email."'".$ssql2;  
			$db_river = $obj->MySQLSelect($sql4);
			
		if(count($db_comp)>0)
		{     
				if($db_comp[0]['eStatus']=='Deleted')
				{ 
						echo 1;
				}
				else
				{      
						echo 0;
				}
		}        
		else if(count($db_driver)>0 or count($db_river)>0)
		{
			//echo count($db_river);exit;
			if($db_driver[0]['eStatus']=='Deleted' || $db_river[0]['eStatus']=='Deleted')
				{ 
						echo 1;
				}
				else
				{       
						echo 0;
				}
		}
		else
		{	
				echo 1;
		}
		
	}
?>