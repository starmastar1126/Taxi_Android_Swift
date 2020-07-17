<?php 
	include_once('../common.php');
	if(!isset($generalobjAdmin))
	{
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	unset($_POST['dataTables-example_length']);
	unset($_POST['submit']);
	$ratio = $_REQUEST['Ratio'];
	$thresholdamount = $_REQUEST['fThresholdAmount'];
	$vSymbol = $_REQUEST['vSymbol'];
	$iCurrencyId=$_REQUEST['iCurrencyId'];
	$eDefault =$_REQUEST['eDefault'];

	$sql= "select * from currency WHERE eStatus = 'Active' order by iCurrencyId";
	$db_sq = $obj->MySQLSelect($sql);
	if(SITE_TYPE=='Demo')
	{
		header("location:currency.php?success=2");
		exit;
	}
	else
	{
		for($i=0;$i<count($db_sq);$i++)
		{
			$name=$db_sq[$i]["vName"];
			$j=0;
			$str="UPDATE currency SET ";
			foreach($db_sq as  $arr)
			{
				$str.= "vSymbol"."='".$vSymbol[$i]."',";
				$str.= "Ratio"."='".$ratio[$i]."',";
				$str.= "fThresholdAmount"."='".$thresholdamount[$i]."',";
			}
			$str=substr_replace($str ," ",-1);
			$id= $db_sq[$i]['iCurrencyId'];
			$str.="where iCurrencyId=".$iCurrencyId[$i];
			$db_update = $obj->sql_query($str);
		}
		
		$sql="UPDATE currency SET eDefault = 'No' ";
		$db_update = $obj->sql_query($sql);
		
		$sql="UPDATE currency SET eDefault = 'Yes' WHERE iCurrencyId = '".$eDefault."' ";
		$db_update = $obj->sql_query($sql);
		
		header("location:currency.php?success=1");
		exit;
	}
?>