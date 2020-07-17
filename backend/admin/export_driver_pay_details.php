<?php             

include_once('../common.php');

$tbl_name 	= 'trips';

if (!isset($generalobjAdmin)) {

     require_once(TPATH_CLASS . "class.general_admin.php");

     $generalobjAdmin = new General_admin();

}

$generalobjAdmin->check_member_login();

$abc = 'admin,company';

$action = $_REQUEST['action'];

$ssql=" AND tr.iActive = 'Finished' ";

//$startDate = isset($_REQUEST['prev_start']) ? date("Y-m-d",strtotime($_REQUEST['prev_start'])) : '';

//$endDate = isset($_REQUEST['prev_end']) ? date("Y-m-d",strtotime($_REQUEST['prev_end'])) : '';

$iCountryCode = isset($_REQUEST['prev_country']) ? $_REQUEST['prev_country'] : '';


$startDate = isset($_REQUEST['prev_start']) ? $_REQUEST['prev_start'] : '';

$endDate = isset($_REQUEST['prev_end']) ? $_REQUEST['prev_end'] : '';

$searchCompany = isset($_REQUEST['prevsearchCompany']) ? $_REQUEST['prevsearchCompany'] : '';
$searchDriver = isset($_REQUEST['prevsearchDriver']) ? $_REQUEST['prevsearchDriver'] : '';
$order = isset($_REQUEST['prev_order']) ? $_REQUEST['prev_order'] : '';
$sortby = isset($_REQUEST['prev_sortby']) ? $_REQUEST['prev_sortby'] : '';

$ord = ' ORDER BY rd.iDriverId DESC';
if($sortby == 1){
  if($order == 0)
  $ord = " ORDER BY rd.iDriverId ASC";
  else
  $ord = " ORDER BY rd.iDriverId DESC";
}

if($sortby == 2){
  if($order == 0)
  $ord = " ORDER BY rd.vName ASC";
  else
  $ord = " ORDER BY rd.vName DESC";
}

if($sortby == 3){
  if($order == 0)
  $ord = " ORDER BY rd.vBankAccountHolderName ASC";
  else
  $ord = " ORDER BY rd.vBankAccountHolderName DESC";
}

if($sortby == 4){
  if($order == 0)
  $ord = " ORDER BY rd.vBankName ASC";
  else
  $ord = " ORDER BY rd.vBankName DESC";
}

if($action != '' && $action == "export")

{

	if($startDate!=''){

		$ssql.=" AND Date(tr.tTripRequestDate) >='".$startDate."'";

	}

	if($endDate!=''){

		$ssql.=" AND Date(tr.tTripRequestDate) <='".$endDate."'";

	}

	if($iCountryCode != ''){

		$ssql.=" AND rd.vCountry ='".$iCountryCode."'";

	}

	if ($searchCompany != '') {
        $ssql1 .= " AND rd.iCompanyId ='" . $searchCompany . "'";
    }
    if ($searchDriver != '') {
        $ssql .= " AND tr.iDriverId ='" . $searchDriver . "'";
    }
	

	$sql = "select rd.iDriverId,tr.eDriverPaymentStatus,concat(rd.vName,' ',rd.vLastName) as dname,rd.vCountry,rd.vBankAccountHolderName,rd.vAccountNumber,rd.vBankLocation,rd.vBankName,rd.vBIC_SWIFT_Code from register_driver as rd LEFT JOIN trips as tr ON tr.iDriverId=rd.iDriverId WHERE 1=1 AND tr.eDriverPaymentStatus='Unsettelled' $ssql $ssql1 GROUP BY rd.iDriverId $ord";

	$db_payment = $obj->MySQLSelect($sql);

	// echo "<pre>"; print_r($db_payment); die;

	for($i=0;$i<count($db_payment);$i++) {

		$db_payment[$i]['cashPayment'] = $generalobjAdmin->getAllCashCountbyDriverId($db_payment[$i]['iDriverId'],$ssql);

		$db_payment[$i]['cardPayment'] = $generalobjAdmin->getAllCardCountbyDriverId($db_payment[$i]['iDriverId'],$ssql);
		
		$db_payment[$i]['walletPayment'] = $generalobjAdmin->getAllWalletCountbyDriverId($db_payment[$i]['iDriverId'],$ssql);
	  
    $db_payment[$i]['promocodePayment'] = $generalobjAdmin->getAllPromocodeCountbyDriverId($db_payment[$i]['iDriverId'],$ssql);

    if ($ENABLE_TIP_MODULE == "Yes") {
  		$db_payment[$i]['tipPayment'] = $generalobjAdmin->getAllTipCountbyDriverId($db_payment[$i]['iDriverId'],$ssql);
      $db_payment[$i]['transferAmount'] = $generalobjAdmin->getTransforAmountbyDriverId($db_payment[$i]['iDriverId'],$ssql);
  	} else {
		  $db_payment[$i]['transferAmount'] = $generalobjAdmin->getTransforAmountbyDriverId($db_payment[$i]['iDriverId'],$ssql); 
    }


	}


$header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Name"."\t";

$header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Account Name"."\t";

$header .= "Bank Name"."\t";

$header .= "Account Number"."\t";

$header .= "Sort Code"."\t";

$header .= "A = Total".$langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." Commission To Take From ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." For Cash ".$langage_lbl_admin['LBL_TRIPS_TXT_ADMIN']."\t";

$header .= "B = Total ".$langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." Amount Pay to ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." For Card ".$langage_lbl_admin['LBL_TRIPS_TXT_ADMIN']."\t";

if($ENABLE_TIP_MODULE == "Yes") {

$header .= "C = Total Tip Amount To Pay to ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].""."\t";

}

$header .= "D = Total Wallet Adjustment Amount Pay to ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." For Cash ".$langage_lbl_admin['LBL_TRIPS_TXT_ADMIN']."\t";

$header .= "E = Total Promocode Discount Amount Pay to ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." For Cash ".$langage_lbl_admin['LBL_TRIPS_TXT_ADMIN']."\t";

$header .= "Final Amount Pay to ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." F = B-A+C+D+E"."\t";

$header .= "Final Amount to take back from  ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." G = B-A+C+D+E"."\t";

$header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment Status"."\t";




for($j=0;$j<count($db_payment);$j++)

{

    $data .= $generalobjAdmin->clearName($db_payment[$j]['dname'])."\t";

    $data .= ($db_payment[$j]['vBankAccountHolderName'] != "")?$db_payment[$j]['vBankAccountHolderName']:'---';

	$data .= "\t";

	$data .= ($db_payment[$j]['vBankName'] != "")?$db_payment[$j]['vBankName']:'---';

	$data .= "\t";

	$data .= ($db_payment[$j]['vAccountNumber'] != "")?$db_payment[$j]['vAccountNumber']:'---';

	$data .= "\t";

	$data .= ($db_payment[$j]['vBIC_SWIFT_Code'] != "")?$db_payment[$j]['vBIC_SWIFT_Code']:'---';

	$data .= "\t";

    $data .= $db_payment[$j]['cashPayment']."\t";

    $data .= $db_payment[$j]['cardPayment']."\t";
    
    if($ENABLE_TIP_MODULE == "Yes") {
    
    $data .= $db_payment[$j]['tipPayment']."\t";
    
    }
    
    $data .= $db_payment[$j]['walletPayment']."\t";
    
    $data .= $db_payment[$j]['promocodePayment']."\t";

    if($db_payment[$j]['transferAmount'] > 0)
    {
      $data .= $db_payment[$j]['transferAmount']."\t";
    } else {
      $data .= "---"."\t";
    }
  

    if($db_payment[$j]['transferAmount'] >= 0) {
      $data .= "---"."\t";
    } else{
      $data .= $db_payment[$j]['transferAmount']."\t";
    }

   
	$data .= $db_payment[$j]['eDriverPaymentStatus']."\n";

}

}

$data = str_replace( "\r" , "" , $data );

ob_clean();

header("Content-type: application/octet-stream");

header("Content-Disposition: attachment; filename=payment_reports.xls");

header("Pragma: no-cache");

header("Expires: 0");

print "$header\n$data";

exit;

?>

