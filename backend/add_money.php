<?php 

include_once("common.php");
$action_form = $_REQUEST["action"];
$iMemberId = $_REQUEST["iMemberId"];
$eMemberType = $_REQUEST["eMemberType"];
$Member_Available_Balance = $_REQUEST["Member_Available_Balance"];
$fAmount = $_REQUEST["fAmount"];

/*For Currency Entry in User Wallet*/
/*$sql = "SELECT * FROM currency WHERE eStatus = 'Active'";
$db_currency = $obj->MySQLSelect($sql);
for($i=0;$i<count($db_currency);$i++)
{
	if($db_currency[$i]['vName'] == 'GBP')
	{
		$fRatio_GBP = $db_currency[$i]['Ratio'];
	}
	
	if($db_currency[$i]['vName'] == 'USD')
	{
		$fRatio_USD = $db_currency[$i]['Ratio'];
	}
	
	if($db_currency[$i]['vName'] == 'EUR')
	{
		$fRatio_EUR = $db_currency[$i]['Ratio'];
	}		
}*/
/*For Currency Entry in User Wallet End*/

//echo "<pre>"; print_r($_REQUEST); exit;

/*$action = $generalobj->decrypt($_REQUEST["action"]);
$iMemberId = $generalobj->decrypt($_REQUEST["iMemberId"]);
$eMemberType = $generalobj->decrypt($_REQUEST["eMemberType"]);
$Member_Available_Balance = $generalobj->decrypt($_REQUEST["Member_Available_Balance"]);*/




//$generalobj->check_member_login();
$abc = 'admin,driver,company';
$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//$generalobj->setRole($abc,$url);

if($action_form == "add_money"){

	$dDate = Date('Y-m-d H:i:s');
	$eFor = 'Deposit';
	$eType = 'Credit';
	$iTripId = 0;	
	$tDescription = ' Amount '.$fAmount.' credited into your account from administrator';
	$ePaymentStatus = 'Unsettelled';

		/*$sql = "INSERT INTO `user_wallet` (`iUserId`,`eUserType`,`iBalance`,`eType`,`iTripId`, `eFor`, `tDescription`, `ePaymentStatus`, `dDate`, fRatio_GBP, fRatio_EUR, fRation_USD) VALUES ('" .$iMemberId . "','".$eMemberType."', '" . $fAmount . "','" . $eType . "', '" . $iTripId . "', '" . $eFor . "', '" .$tDescription. "', '" .$ePaymentStatus. "', '" .$dDate. "', '".$fRatio_GBP."', '".$fRatio_EUR."', '".$fRatio_USD."')";		
			$result = $obj->sql_query($sql);	*/

	 $sql = "INSERT INTO `user_wallet` (`iUserId`,`eUserType`,`iBalance`,`eType`,`iTripId`, `eFor`, `tDescription`, `ePaymentStatus`, `dDate`) VALUES ('" .$iMemberId . "','".$eMemberType."', '" . $fAmount . "','" . $eType . "', '" . $iTripId . "', '" . $eFor . "', '" .$tDescription. "', '" .$ePaymentStatus. "', '" .$dDate. "')";	

	  $result = $obj->MySQLInsert($sql);
		$sql = "SELECT * FROM currency WHERE eStatus = 'Active'";
		$db_curr = $obj->MySQLSelect($sql);
		$where = " iUserWalletId = '".$result."'";
		for($i=0;$i<count($db_curr);$i++)
		{
		  $data_currency_ratio['fRatio_'.$db_curr[$i]['vName']]=$db_curr[$i]['Ratio'];
		  $obj->MySQLQueryPerform("user_wallet",$data_currency_ratio,'update',$where);
		}	

	 
	 if($result){
	 	$script="Add money";
		$meta = $generalobj->getStaticPage(31,$_SESSION['sess_lang']);
		?>

		<!DOCTYPE html>
			<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
			<head>
			    <meta charset="UTF-8">
			    <meta name="viewport" content="width=device-width,initial-scale=1">
			    <title><?=$meta['meta_title'];?></title>
				<meta name="keywords" value="<?=$meta['meta_keyword'];?>"/>
				<meta name="description" value="<?=$meta['meta_desc'];?>"/>
			    <!-- Default Top Script and css -->
			    <?php  include_once("top/top_script.php");?>
			    <!-- End: Default Top Script and css-->
			</head>
			<body> <div id="main-uber-page">
			    <!-- Left Menu -->
			    <?php  include_once("top/left_menu.php");?>
			    <!-- End: Left Menu-->
			    <!-- home page -->
			   
			        <!-- Top Menu -->
			        <?php  include_once("top/header_topbar.php");?>
			        <!-- End: Top Menu-->
			        <!-- contact page-->
					<div class="page-contant">
					<div class="page-contant-inner">
					      <h2 class="header-page trip-detail"><?=$meta['page_title'];?></h2>
					      <!-- trips detail page -->
					      <div class="static-page">
					        <?=$meta['page_desc'];?>
					      </div>
					    </div>
					</div>
			    
			    <!-- home page end-->
			    <!-- footer part -->
			    <?php  include_once('footer/footer_home.php');?>
			        <!-- End:contact page-->
			        <div style="clear:both;"></div>
			    </div>
			    <!-- footer part end -->
			    <!-- Footer Script -->
			    <?php  include_once('top/footer_script.php');?>
			    <!-- End: Footer Script -->
			</body>
			</html>

	<?php  } else{

		header('Location: rider_wallet.php'); exit;
	}

} ?>
