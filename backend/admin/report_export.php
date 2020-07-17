<?php 
include_once('../common.php');
if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$section = isset($_REQUEST['section']) ? $_REQUEST['section'] : '';
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : '';
$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : '';
$iCompanyId = isset($_REQUEST['searchCompany']) ? $_REQUEST['searchCompany'] : '';
$iDriverId = isset($_REQUEST['searchDriver']) ? $_REQUEST['searchDriver'] : '';
$iUserId = isset($_REQUEST['searchRider']) ? $_REQUEST['searchRider'] : '';
$serachTripNo = isset($_REQUEST['serachTripNo']) ? $_REQUEST['serachTripNo'] : '';
$vTripPaymentMode = isset($_REQUEST['searchPaymentType']) ? $_REQUEST['searchPaymentType'] : '';
$eDriverPaymentStatus = isset($_REQUEST['searchDriverPayment']) ? $_REQUEST['searchDriverPayment'] : '';

$ssql = "";

function converToTz($time, $toTz, $fromTz,$dateFormat="Y-m-d H:i:s") {

    $date = new DateTime($time, new DateTimeZone($fromTz));

    $date->setTimezone(new DateTimeZone($toTz));

    $time = $date->format($dateFormat);

    return $time;

}

function mediaTimeDeFormater($seconds) {
    $ret = "";
   
    $hours = (string )floor($seconds / 3600);
    $secs = (string )$seconds % 60;
    $mins = (string )floor(($seconds - ($hours * 3600)) / 60);

    if (strlen($hours) == 1)
        $hours = "0" . $hours;
    if (strlen($secs) == 1)
        $secs = "0" . $secs;
    if (strlen($mins) == 1)
        $mins = "0" . $mins;

    if ($hours == 0){
        $mint="";
        $secondss="";
        if($mins > 01){
            $mint = "$mins mins";
        }else{
            $mint = "$mins min";
        }
        if($secs > 01){
            $secondss = "$secs seconds";
        }else{
            $secondss = "$secs second";
        }
         $ret = "$mint $secondss";
    } else {
        $mint="";
        $secondss="";
        if($mins > 01){
            $mint = "$mins mins";
        }else{
            $mint = "$mins min";
        }
        if($secs > 01){
            $secondss = "$secs seconds";
        }else{
            $secondss = "$secs second";
        }
        if($hours > 01){
          $ret = "$hours hrs $mint $secondss";
        }else{
          $ret = "$hours hr $mint $secondss";
        }
    }
    return  $ret;
}
function cleanData(&$str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if (strstr($str, '"'))
        $str = '"' . str_replace('"', '""', $str) . '"';
}

if ($section == 'driver_payment') {
$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : '';
    $trp_ssql = "";
    if (SITE_TYPE == 'Demo') {
        $trp_ssql = " And tr.tTripRequestDate > '" . WEEK_DATE . "'";
    }

    $ord = ' ORDER BY tr.iTripId DESC';

    if ($sortby == 1) {
        if ($order == 0)
            $ord = " ORDER BY rd.vName ASC";
        else
            $ord = " ORDER BY rd.vName DESC";
    }

    if ($sortby == 2) {
        if ($order == 0)
            $ord = " ORDER BY ru.vName ASC";
        else
            $ord = " ORDER BY ru.vName DESC";
    }

    if ($sortby == 3) {
        if ($order == 0)
            $ord = " ORDER BY tr.tStartDate ASC";
        else
            $ord = " ORDER BY tr.tStartDate DESC";
    }

    if ($sortby == 4) {
        if ($order == 0)
            $ord = " ORDER BY d.vName ASC";
        else
            $ord = " ORDER BY d.vName DESC";
    }

    if ($sortby == 5) {
        if ($order == 0)
            $ord = " ORDER BY u.vName ASC";
        else
            $ord = " ORDER BY u.vName DESC";
    }

    if ($sortby == 6) {
        if ($order == 0)
            $ord = " ORDER BY tr.eType ASC";
        else
            $ord = " ORDER BY tr.eType DESC";
    }
    $ssql = "";
    if ($startDate != '') {
        $ssql .= " AND Date(tTripRequestDate) >='" . $startDate . "'";
    }
    if ($endDate != '') {
        $ssql .= " AND Date(tTripRequestDate) <='" . $endDate . "'";
    }
    if ($iCompanyId != '') {
        $ssql .= " AND rd.iCompanyId = '" . $iCompanyId . "'";
    }
    if ($iDriverId != '') {
        $ssql .= " AND tr.iDriverId = '" . $iDriverId . "'";
    }

    if ($iUserId != '') {
        $ssql .= " AND tr.iUserId = '" . $iUserId . "'";
    }
	if ($serachTripNo != '') {
        $ssql .= " AND tr.vRideNo ='" . $serachTripNo . "'";
    }

    if ($vTripPaymentMode != '') {
        $ssql .= " AND tr.vTripPaymentMode = '" . $vTripPaymentMode . "'";
    }
    if ($eDriverPaymentStatus != '') {
        $ssql .= " AND tr.eDriverPaymentStatus = '" . $eDriverPaymentStatus . "'";
    }
    if ($eType != '') {
        $ssql .= " AND tr.eType ='" . $eType . "'";
    }
    //$sql_admin = "SELECT * from trips WHERE 1=1 ".$ssql." ORDER BY iTripId DESC";
    $sql_admin = "SELECT tr.iTripId,tr.vRideNo,tr.iDriverId,tr.iUserId,tr.tTripRequestDate,tr.fTripGenerateFare,tr.fCommision, tr.fDiscount, tr.fWalletDebit, tr.fTipPrice,tr.eDriverPaymentStatus,tr.ePaymentCollect,tr.vTripPaymentMode,tr.iActive,tr.eType, tr.eHailTrip,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName FROM trips AS tr LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId 
		WHERE if(tr.iActive ='Canceled',if(tr.vTripPaymentMode='Card',1=1,0),1=1) AND tr.iActive ='Finished'  $ssql $trp_ssql $ord";
    $db_trip = $obj->MySQLSelect($sql_admin);
    //echo "<pre>";print_r($db_trip); exit;
  if($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery'){
        $header .= $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']. "\t";
    }
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." No." . "\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']. " Name" . "\t";
    $header .= $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']. " Name" . "\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." Date" . "\t";
    $header .= "A=Total Fare" . "\t";
    $header .= "B=Platform Fees" . "\t";
    $header .= "C= Promo Code Discount" . "\t";
    $header .= "D = Wallet Debit" . "\t";
    if ($ENABLE_TIP_MODULE == "Yes") {
        $header .= "E = Tip" . "\t";
    }
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']. " pay Amount" . "\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." Status" . "\t";
    $header .= "Payment method" . "\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']. " Payment Status";

    $driver_payment = 0.00;
    $total_tip = 0.00;
    $tot_fare = 0.00;
    $tot_site_commission = 0.00;
    $tot_promo_discount = 0.00;
    $tot_driver_refund = 0.00;
    $tot_wallentPayment = 0.00;
    
    for ($j = 0; $j < count($db_trip); $j++) {
        $totalfare = $db_trip[$j]['fTripGenerateFare'];
        $site_commission = $db_trip[$j]['fCommision'];
        $promocodediscount = $db_trip[$j]['fDiscount'];
        $wallentPayment = $db_trip[$j]['fWalletDebit'];
        $fTipPrice = $db_trip[$j]['fTipPrice'];
        
        //$driver_payment = $totalfare - $site_commission;
        if($db_trip[$j]['vTripPaymentMode'] == "Cash"){
          $driver_payment =  $promocodediscount + $wallentPayment - $site_commission;
        }else{
          $driver_payment = $totalfare - $site_commission + $fTipPrice;
        }
        
        $tot_fare = $tot_fare + $totalfare;
        $tot_site_commission = $tot_site_commission + $site_commission;
        $tot_promo_discount = $tot_promo_discount + $promocodediscount;
        $tot_wallentPayment = $tot_wallentPayment + $wallentPayment;
        $total_tip = $total_tip + $fTipPrice;
        $tot_driver_refund = $tot_driver_refund + $driver_payment;
        
        if ($db_trip[$j]['eMBirr'] == "Yes") {
            $paymentmode = "M-birr";
        } else {
            $paymentmode = $db_trip[$j]['vTripPaymentMode'];
        }

        $eType = $db_trip[$j]['eType'];
        if($eType == 'Ride'){
            $trip_type = 'Ride';
        } else if($eType == 'UberX') {
            $trip_type = 'Other Services';
        }  else if($eType == 'Deliver') {
            $trip_type = 'Delivery';
        }
        if($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery'){
           if($db_trip[$j]['eHailTrip'] != "Yes"){
                $data .= $trip_type . "\t";
            }else{
               $data .= $trip_type ." ( Hail )". "\t";
            } 
        }
        $data .= $db_trip[$j]['vRideNo'] . "\t";
        $data .= $generalobjAdmin->clearName($db_trip[$j]['drivername']) . "\t";
        $data .= $generalobjAdmin->clearName($db_trip[$j]['riderName']) . "\t";
        $data .= date('d-m-Y', strtotime($db_trip[$j]['tTripRequestDate'])) . "\t";
        $data .= ($db_trip[$j]['fTripGenerateFare'] != "" && $db_trip[$j]['fTripGenerateFare'] != 0) ? $generalobj->trip_currency($db_trip[$j]['fTripGenerateFare']) . "\t" : "- \t";
        $data .= ($db_trip[$j]['fCommision'] != "" && $db_trip[$j]['fCommision'] != 0) ? $generalobj->trip_currency($db_trip[$j]['fCommision']) . "\t" : "- \t";
        $data .= ($db_trip[$j]['fDiscount'] != "" && $db_trip[$j]['fDiscount'] != 0) ? $generalobj->trip_currency($db_trip[$j]['fDiscount']) . "\t" : "- \t";
        $data .= ($db_trip[$j]['fWalletDebit'] != "" && $db_trip[$j]['fWalletDebit'] != 0) ? $generalobj->trip_currency($db_trip[$j]['fWalletDebit']) . "\t" : "- \t";
        if ($ENABLE_TIP_MODULE == "Yes") {
            $data .= ($db_trip[$j]['fTipPrice'] != "" && $db_trip[$j]['fTipPrice'] != 0) ? $generalobj->trip_currency($db_trip[$j]['fTipPrice']) . "\t" : "- \t";
        }
        $data .= ($driver_payment != "" && $driver_payment != 0) ? $generalobj->trip_currency($driver_payment) . "\t" : "- \t";
        $data .= $db_trip[$j]['iActive'] . "\t";
        $data .= $paymentmode . "\t";
        $data .= $db_trip[$j]['eDriverPaymentStatus'] . "\n";
    }
    $data .= "\n\t\t\t\t\t\t\t\t\tTotal Fare\t" . $generalobj->trip_currency($tot_fare) . "\n";
    $data .= "\t\t\t\t\t\t\t\t\tTotal Platform Fees\t" . $generalobj->trip_currency($tot_site_commission) . "\n";
    $data .= "\t\t\t\t\t\t\t\t\tTotal Promo Discount\t" . $generalobj->trip_currency($tot_promo_discount) . "\n";
    $data .= "\t\t\t\t\t\t\t\t\tTotal Wallet Debit\t" . $generalobj->trip_currency($tot_wallentPayment) . "\n";
    if ($ENABLE_TIP_MODULE == "Yes") {
        $data .= "\t\t\t\t\t\t\t\t\tTotal Tip Amount\t" . $generalobj->trip_currency($total_tip) . "\n";
        //$data .= "\t\t\t\t\t\t\t\t\tTotal Driver Payment\t" . $generalobj->trip_currency($tot_driver_refund+$total_tip) . "\n";
        $data .= "\t\t\t\t\t\t\t\t\tTotal ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment\t" . $generalobj->trip_currency($tot_driver_refund) . "\n";
    }else {
        $data .= "\t\t\t\t\t\t\t\t\tTotal ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment\t" . $generalobj->trip_currency($tot_driver_refund) . "\n";
    }
    $data = str_replace("\r", "", $data);
#echo "<br>".$data; exit;
    ob_clean();
    header("Content-type: application/octet-stream; charset=utf-8");
    header("Content-Disposition: attachment; filename=payment_reports.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    print "$header\n$data";
    exit;
}


if ($section == 'driver_payment') {

    $trp_ssql = "";
    if (SITE_TYPE == 'Demo') {
        $trp_ssql = " And tr.tTripRequestDate > '" . WEEK_DATE . "'";
    }

    $ord = ' ORDER BY tr.iTripId DESC';

    if ($sortby == 1) {
        if ($order == 0)
            $ord = " ORDER BY rd.vName ASC";
        else
            $ord = " ORDER BY rd.vName DESC";
    }

    if ($sortby == 2) {
        if ($order == 0)
            $ord = " ORDER BY ru.vName ASC";
        else
            $ord = " ORDER BY ru.vName DESC";
    }

    if ($sortby == 3) {
        if ($order == 0)
            $ord = " ORDER BY tr.tStartDate ASC";
        else
            $ord = " ORDER BY tr.tStartDate DESC";
    }

    if ($sortby == 4) {
        if ($order == 0)
            $ord = " ORDER BY d.vName ASC";
        else
            $ord = " ORDER BY d.vName DESC";
    }

    if ($sortby == 5) {
        if ($order == 0)
            $ord = " ORDER BY u.vName ASC";
        else
            $ord = " ORDER BY u.vName DESC";
    }

    $ssql = "";
    if ($startDate != '') {
        $ssql .= " AND Date(tTripRequestDate) >='" . $startDate . "'";
    }
    if ($endDate != '') {
        $ssql .= " AND Date(tTripRequestDate) <='" . $endDate . "'";
    }
    if ($iCompanyId != '') {
        $ssql .= " AND rd.iCompanyId = '" . $iCompanyId . "'";
    }
    if ($iDriverId != '') {
        $ssql .= " AND tr.iDriverId = '" . $iDriverId . "'";
    }

    if ($iUserId != '') {
        $ssql .= " AND tr.iUserId = '" . $iUserId . "'";
    }
	if ($serachTripNo != '') {
        $ssql .= " AND tr.vRideNo ='" . $serachTripNo . "'";
    }

    if ($vTripPaymentMode != '') {
        $ssql .= " AND tr.vTripPaymentMode = '" . $vTripPaymentMode . "'";
    }
    if ($eDriverPaymentStatus != '') {
        $ssql .= " AND tr.eDriverPaymentStatus = '" . $eDriverPaymentStatus . "'";
    }
    //$sql_admin = "SELECT * from trips WHERE 1=1 ".$ssql." ORDER BY iTripId DESC";
    $sql_admin = "SELECT tr.iTripId,tr.vRideNo,tr.iDriverId,tr.iUserId,tr.tTripRequestDate,tr.fTripGenerateFare,tr.fCommision, tr.fDiscount, tr.fWalletDebit, tr.fTipPrice,tr.eDriverPaymentStatus,tr.ePaymentCollect,tr.vTripPaymentMode,tr.iActive,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName FROM trips AS tr 
		LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId 
		LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId 
		LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId 
		WHERE 1=1 $ssql $trp_ssql $ord";
    $db_trip = $obj->MySQLSelect($sql_admin);
//    echo "<pre>";print_r($db_trip); exit;

    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." No." . "\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Name" . "\t";
    $header .= $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']." Name" . "\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." Date" . "\t";
    $header .= "Total Fare" . "\t";
    $header .= "Platform Fees" . "\t";
    $header .= "Promo Code Discount" . "\t";
    $header .= "Wallet Debit" . "\t";
    if ($ENABLE_TIP_MODULE == "Yes") {
        $header .= "Tip" . "\t";
    }
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." pay Amount" . "\t";
    $header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." Status" . "\t";
    $header .= "Payment method" . "\t";
    $header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment Status";

    $tot_fare = 0.00;
    $tot_site_commission = 0.00;
    $tot_promo_discount = 0.00;
    $tot_driver_refund = 0.00;
    $tot_wallentPayment = 0.00;
    $total_tip = 0.00;

    for ($j = 0; $j < count($db_trip); $j++) {
        $driver_payment = 0.00;
        
        $totalfare = $db_trip[$j]['fTripGenerateFare'];
        $site_commission = $db_trip[$j]['fCommision'];
        $promocodediscount = $db_trip[$j]['fDiscount'];
        $wallentPayment = $db_trip[$j]['fWalletDebit'];
        $fTipPrice = $db_trip[$j]['fTipPrice'];
        $driver_payment = $totalfare - $site_commission;
        
        $tot_fare = $tot_fare + $totalfare;
        $tot_site_commission = $tot_site_commission + $site_commission;
        $tot_promo_discount = $tot_promo_discount + $promocodediscount;
        $tot_wallentPayment = $tot_wallentPayment + $wallentPayment;
        $total_tip = $total_tip + $fTipPrice;
        $tot_driver_refund = $tot_driver_refund + $driver_payment;
        
        if ($db_trip[$j]['eMBirr'] == "Yes") {
            $paymentmode = "M-birr";
        } else {
            $paymentmode = $db_trip[$j]['vTripPaymentMode'];
        }

        $data .= $db_trip[$j]['vRideNo'] . "\t";
        $data .= $generalobjAdmin->clearName($db_trip[$j]['drivername']) . "\t";
        $data .= $generalobjAdmin->clearName($db_trip[$j]['riderName']) . "\t";
        $data .= date('d-m-Y', strtotime($db_trip[$j]['tTripRequestDate'])) . "\t";
        $data .= ($db_trip[$j]['fTripGenerateFare'] != "" && $db_trip[$j]['fTripGenerateFare'] != 0) ? $generalobj->trip_currency($db_trip[$j]['fTripGenerateFare']) . "\t" : "- \t";
        $data .= ($db_trip[$j]['fCommision'] != "" && $db_trip[$j]['fCommision'] != 0) ? $generalobj->trip_currency($db_trip[$j]['fCommision']) . "\t" : "- \t";
        $data .= ($db_trip[$j]['fDiscount'] != "" && $db_trip[$j]['fDiscount'] != 0) ? $generalobj->trip_currency($db_trip[$j]['fDiscount']) . "\t" : "- \t";
        $data .= ($db_trip[$j]['fWalletDebit'] != "" && $db_trip[$j]['fWalletDebit'] != 0) ? $generalobj->trip_currency($db_trip[$j]['fWalletDebit']) . "\t" : "- \t";
        if ($ENABLE_TIP_MODULE == "Yes") {
            $data .= ($db_trip[$j]['fTipPrice'] != "" && $db_trip[$j]['fTipPrice'] != 0) ? $generalobj->trip_currency($db_trip[$j]['fTipPrice']) . "\t" : "- \t";
        }
        $data .= ($driver_payment != "" && $driver_payment != 0) ? $generalobj->trip_currency($driver_payment) . "\t" : "- \t";
        $data .= $db_trip[$j]['iActive'] . "\t";
        $data .= $paymentmode . "\t";
        $data .= $db_trip[$j]['eDriverPaymentStatus'] . "\n";
    }
    $data .= "\n\t\t\t\t\t\t\t\t\tTotal Fare\t" . $generalobj->trip_currency($tot_fare) . "\n";
    $data .= "\t\t\t\t\t\t\t\t\tTotal Platform Fees\t" . $generalobj->trip_currency($tot_site_commission) . "\n";
    $data .= "\t\t\t\t\t\t\t\t\tTotal Promo Discount\t" . $generalobj->trip_currency($tot_promo_discount) . "\n";
    $data .= "\t\t\t\t\t\t\t\t\tTotal Wallet Debit\t" . $generalobj->trip_currency($tot_wallentPayment) . "\n";
    if ($ENABLE_TIP_MODULE == "Yes") {
        $data .= "\t\t\t\t\t\t\t\t\tTotal ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment\t" . $generalobj->trip_currency($total_tip) . "\n";
        $data .= "\t\t\t\t\t\t\t\t\tTotal ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment\t" . $generalobj->trip_currency($tot_driver_refund+$total_tip) . "\n";
    }else {
        $data .= "\t\t\t\t\t\t\t\t\tTotal ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Payment\t" . $generalobj->trip_currency($tot_driver_refund) . "\n";
    }
    $data = str_replace("\r", "", $data);
#echo "<br>".$data; exit;
    ob_clean();
    header("Content-type: application/octet-stream; charset=utf-8");
    header("Content-Disposition: attachment; filename=payment_reports.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    print "$header\n$data";
    exit;
}


if ($section == 'driver_log_report') {

    $dlp_ssql = "";

    $ord = ' ORDER BY dlr.iDriverLogId DESC';

	//Start Sorting
	$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
	$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
	$ord = ' ORDER BY dlr.iDriverLogId DESC';

	if ($sortby == 1) {
		if ($order == 0)
			$ord = " ORDER BY rd.vName ASC";
		else
			$ord = " ORDER BY rd.vName DESC";
	}

	if ($sortby == 2) {
		if ($order == 0)
			$ord = " ORDER BY rd.vEmail ASC";
		else
			$ord = " ORDER BY rd.vEmail DESC";
	}

	if ($sortby == 3) {
		if ($order == 0)
			$ord = " ORDER BY dlr.dLoginDateTime ASC";
		else
			$ord = " ORDER BY dlr.dLoginDateTime DESC";
	}

	if ($sortby == 4) {
		if ($order == 0)
			$ord = " ORDER BY dlr.dLogoutDateTime ASC";
		else
			$ord = " ORDER BY dlr.dLogoutDateTime DESC";
	}
		// Start Search Parameters
		$ssql = '';
		$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
		$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : '';
		$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : '';
		$vEmail = isset($_REQUEST['vEmail']) ? $_REQUEST['vEmail'] : '';

		if ($startDate != '' && $endDate != '') {
			$ssql .= " AND dlr.dLoginDateTime BETWEEN '" . $startDate . "' AND '" . $endDate . "'";
		}
		if ($iDriverId != '') {
			$ssql .= " AND rd.iDriverId = '" . $iDriverId . "'";
		}
		if ($vEmail != '') {
			$ssql .= " AND rd.vEmail = '" . $vEmail . "'";
		}

				//$sql_admin = "SELECT * from dlips WHERE 1=1 ".$ssql." ORDER BY iDriverLogId DESC";
				$sql = "SELECT rd.vName, rd.vLastName, rd.vEmail, dlr.dLoginDateTime, dlr.dLogoutDateTime
						FROM driver_log_report AS dlr
						LEFT JOIN register_driver AS rd ON rd.iDriverId = dlr.iDriverId where 1=1 AND rd.eStatus != 'Deleted' $ssql $ord";
				$db_dlip = $obj->MySQLSelect($sql);
				#echo "<pre>";print_r($db_dlip); exit;

				$header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Name" . "\t";
				$header .= "Email". "\t";
				$header .= "Log DateTime" . "\t";
				$header .= "Logout TimeDate" . "\t";
				$header .= "Total Hours Login" . "\t";
			   
				for ($j = 0; $j < count($db_dlip); $j++) {
					
					
				  $dstart = $db_dlip[$j]['dLoginDateTime'];
				  if( $db_dlip[$j]['dLogoutDateTime'] == '0000-00-00 00:00:00' || $db_dlip[$j]['dLogoutDateTime'] == '' ){
						$dLogoutDateTime = '--';
						$totalTimecount = '--';
				 
				 }else{
					  
						$dLogoutDateTime = $db_dlip[$j]['dLogoutDateTime'];
						$totalhours = $generalobjAdmin->get_left_days_jobsave($dLogoutDateTime,$dstart);
						$totalTimecount = mediaTimeDeFormater ($totalhours);
				   }
						   
					$data .= $generalobjAdmin->clearName($db_dlip[$j]['vName'].'  '.$db_dlip[$j]['vLastName']) . "\t";
					$data .= $generalobjAdmin->clearEmail($db_dlip[$j]['vEmail']). "\t";
					$data .= $generalobjAdmin->DateTime($db_dlip[$j]['dLoginDateTime']) . "\t";
					$data .= $generalobjAdmin->DateTime($db_dlip[$j]['dLogoutDateTime']) ."\t";
					$data .= $totalTimecount ."\n";
					
					 }
				
				ob_clean();
				header("Content-type: application/octet-sdleam; charset=utf-8");
				header("Content-Disposition: attachment; filename= driver_log_report.xls");
				header("Pragma: no-cache");
				header("Expires: 0");
				print "$header\n$data";
				exit;
}




if ($section == 'cancelled_trip') {

    $dlp_ssql = "";
    if (SITE_TYPE == 'Demo') {
        $dlp_ssql = " And dl.dLoginDateTime > '" . WEEK_DATE . "'";
    }

	//Start Sorting
	$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
	$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
	$ord = ' ORDER BY t.iTripId DESC';
	if($sortby == 1){
	  if($order == 0)
	  $ord = " ORDER BY t.tStartDate ASC";
	  else
	  $ord = " ORDER BY t.tStartDate DESC";
	}

	if($sortby == 2){
	  if($order == 0)
	  $ord = " ORDER BY t.eCancelled ASC";
	  else
	  $ord = " ORDER BY t.eCancelled DESC";
	}

	if($sortby == 4){
	  if($order == 0)
	  $ord = " ORDER BY d.vName ASC";
	  else
	  $ord = " ORDER BY d.vName DESC";
	}
    if($sortby == 5){
      if($order == 0)
      $ord = " ORDER BY t.eType ASC";
      else
      $ord = " ORDER BY t.eType DESC";
    }
	//End Sorting
	
	
		
// Start Search Parameters
$ssql='';
$action = isset($_REQUEST['action']) ? $_REQUEST['action']: '';
$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : '';
$serachTripNo = isset($_REQUEST['serachTripNo']) ? $_REQUEST['serachTripNo'] : '';
$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : '';
$vStatus = isset($_REQUEST['vStatus']) ? $_REQUEST['vStatus'] : '';
$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : '';
if($action == 'search')
{
	if($startDate!=''){
		$ssql.=" AND Date(t.tTripRequestDate) >='".$startDate."'";
	}
	if($endDate!=''){
		$ssql.=" AND Date(t.tTripRequestDate) <='".$endDate."'";
	}
	if($iDriverId!=''){
		$ssql.=" AND t.iDriverId ='".$iDriverId."'";
	}
	if($serachTripNo!=''){
		$ssql.=" AND t.vRideNo ='".$serachTripNo."'";
	}
    if($eType!=''){
        $ssql.=" AND t.eType ='".$eType."'";
    }
}

				
$sql_admin = "SELECT t.tTripRequestDate,t.tStartDate ,t.tEndDate,t.eHailTrip,t.eCancelled,t.vCancelReason,t.vCancelComment,d.iDriverId, t.tSaddress,t.vRideNo,t.eType, t.tDaddress, t.fWalletDebit,t.eCarType,t.iTripId,t.iActive ,CONCAT(d.vName,' ',d.vLastName) AS dName FROM trips t LEFT JOIN register_driver d ON d.iDriverId = t.iDriverId
				WHERE 1=1 And t.iActive='Canceled' $ssql $trp_ssql $ord ";
				
				$db_dlip = $obj->MySQLSelect($sql_admin);
				// echo "<pre>";print_r($db_dlip); exit;
if($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery'){
    $header .= $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']. "\t";
}
				$header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." Date" . "\t";
				$header .= "Cancel By". "\t";
				$header .= "Cancel Reason" . "\t";
				$header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Name" . "\t";
				$header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." No". "\t";
				$header .= "Address". "\t";
			   
						
			for ($j = 0; $j < count($db_dlip); $j++) {
					
		$eType = $db_dlip[$j]['eType'];
        if($eType == 'Ride'){
            $trip_type = 'Ride';
        } else if($eType == 'UberX') {
            $trip_type = 'Other Services';
        }  else if($eType == 'Deliver') {
            $trip_type = 'Delivery';
        }
					$vCancelReason = $db_dlip[$j]['vCancelReason'];
					$trip_cancel = ($vCancelReason != '')? $vCancelReason: '--';
					$eCancelled = $db_dlip[$j]['eCancelled'];
					$CanceledBy = ($eCancelled == 'Yes' && $vCancelReason != '' )? 'Driver': 'Passenger';
								
		if($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery'){
           if($db_dlip[$j]['eHailTrip'] != "Yes"){
                $data .= $trip_type . "\t";
            }else{
               $data .= $trip_type ." ( Hail )". "\t";
            } 
        }			
					$data .= $generalobjAdmin->DateTime($db_dlip[$j]['tTripRequestDate'],'no'). "\t";
					$data .= $CanceledBy. "\t";
					$data .= $trip_cancel . "\t";
					$data .= $generalobjAdmin->clearName($db_dlip[$j]['dName']) ."\t";
					$data .= $db_dlip[$j]['vRideNo'] ."\t";
					$str = "";
					if($db_dlip[$j]['tDaddress'] != ""){
						$str = ' -> '.$db_dlip[$j]['tDaddress'];
					}
					// $data .= $db_dlip[$j]['tSaddress'].$str;
					$string = $db_dlip[$j]['tSaddress'].$str;
					$data .= str_replace(array("\n", "\r", "\r\n", "\n\r"),' ',$string);
					$data .= "\n";
				}
				// echo "<pre>";print_r($data);exit;
				ob_clean();
				header("Content-type: application/octet-sdleam; charset=utf-8");
				header("Content-Disposition: attachment; filename=cancelled_trip.xls");
				header("Pragma: no-cache");
				header("Expires: 0");
				print "$header\n$data";
				exit;
}


if ($section == 'ride_acceptance_report') {

		//Start Sorting
		$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
		$ord = ' ORDER BY rs.iDriverRequestId DESC';

		if ($sortby == 1) {
			if ($order == 0)
				$ord = " ORDER BY rd.vName ASC";
			else
				$ord = " ORDER BY rd.vName DESC";
		}
		//End Sorting

		// Start Search Parameters
		$ssql = '';
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
		$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
		$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : '';
		$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : '';
		$date1=$startDate.' '."00:00:00";
		$date2=$endDate.' '."23:59:59";

		if ($startDate != '' && $endDate != '') {
			$ssql .= " AND rs.tDate between '$date1' and '$date2'";
		}
		if ($iDriverId != '') {
			$ssql .= " AND rd.iDriverId = '".$iDriverId."'";
		}
        $chk_str_date = @date('Y-m-d H:i:s', strtotime('-'.$RIDER_REQUEST_ACCEPT_TIME.' second'));
        $sql_admin = "SELECT rd.iDriverId , rd.vLastName ,rd.vName ,
        COUNT(case when rs.eStatus = 'Accept' then 1 else NULL end) `Accept` ,
        COUNT(case when rs.eStatus != '' then 1 else NULL  end) `Total Request` ,
        COUNT(case when (rs.eStatus  = 'Decline' AND rs.eAcceptAttempted  = 'No') then 1 else NULL end) `Decline` ,
        COUNT(case when rs.eAcceptAttempted  = 'Yes' then 1 else NULL end) `Missed` ,
        COUNT(case when ((rs.eStatus  = 'Timeout' OR rs.eStatus  = 'Received') AND rs.eAcceptAttempted  = 'No' AND  rs.dAddedDate < '".$chk_str_date."')  then 1 else NULL end) `Timeout`,
        COUNT(case when ((rs.eStatus  = 'Timeout' OR rs.eStatus  = 'Received') AND rs.eAcceptAttempted  = 'No' AND rs.dAddedDate > '".$chk_str_date."' ) then 1 else NULL end) `inprocess`
        FROM register_driver rd  left join driver_request rs on rd.iDriverId=rs.iDriverId  
        WHERE 1=1 $ssql GROUP by rs.iDriverId $ord ";
/*				
				$sql_admin = "SELECT rd.iDriverId , rd.vLastName ,rd.vName ,
						COUNT(case when rs.eStatus = 'Accept' then 1 else NULL end) `Accept` ,
						COUNT(case when rs.eStatus != '' then 1 else NULL  end) `Total Request` ,
						COUNT(case when rs.eStatus  = 'Decline' then 1 else NULL end) `Decline` ,
						COUNT(case when rs.eStatus  = 'Timeout' then 1 else NULL end) `Timeout` 
						FROM register_driver rd 
						left join driver_request rs on rd.iDriverId=rs.iDriverId  
						WHERE 1=1 $ssql GROUP by rs.iDriverId $ord ";*/
				
		$db_dlip = $obj->MySQLSelect($sql_admin);
				#echo "<pre>";print_r($db_dlip); exit;

				$header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Name" . "\t";
				$header .= "Total ".$langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." Requests". "\t";
                $header .= "Requests Accepted" . "\t";
				$header .= "Requests Decline" . "\t";
				$header .= "Requests Timeout" . "\t";
				$header .= "Missed Attempts" . "\t";
				$header .= "In Process Request" . "\t";
				$header .= "Acceptance Percentage" . "\t";
			   
			    $total_trip_req ="";
                $total_trip_acce_req ="";
                $total_trip_dec_req ="";
						
			for ($j = 0; $j < count($db_dlip); $j++) {
					
					 $sql_acp="SELECT 
									COUNT(case when t.eCancelled = 'Yes' then 1 else NULL end) `Cancel` ,
									COUNT(case when t.eCancelled != '' then 1 else NULL  end) `Finish` 
									FROM trips t  where t.iDriverId='".$db_dlip[$j]['iDriverId']."'";
									$db_acp = $obj->MySQLSelect($sql_acp);
					
					$Accept = $db_dlip[$j]['Accept'];
					$tAccept = $tAccept + $Accept;
					$Request = $db_dlip[$j]['Total Request'];
					$tRequest =$tRequest + $Request ;
					$Decline = $db_dlip[$j]['Decline'];
					$tDecline =$tDecline + $Decline;
					$Timeout = $db_dlip[$j]['Timeout'];
					$tTimeout = $tTimeout + $Timeout ;
					$Cancel = $db_acp[0]['Cancel'];
					$tCancel = $tCancel + $Cancel ;
                    $missed = $db_dlip[$j]['Missed'];
                    $tmissed = $tmissed + $missed ;
                    $inprocess = $db_dlip[$j]['inprocess'];
                    $tinprocess = $tinprocess + $inprocess ;
					$Finish = $db_acp[0]['Finish'];
					$tFinish = $tFinish + $Finish ;
					$aceptance_percentage= (100 * ($Accept))/$Request;
								
								
					$data .= $generalobjAdmin->clearName($db_dlip[$j]['vName'].' '.$db_dlip[$j]['vLastName']). "\t";
					$data .= $Request. "\t";
					$data .= $Accept ."\t";
                    $data .= $Decline . "\t";
                    $data .= $Timeout ."\t";
					$data .= $missed."\t";
					$data .= $inprocess."\t";
					$data .= round($aceptance_percentage,2).' %'."\n";
					
				}
				
				ob_clean();
				header("Content-type: application/octet-sdleam; charset=utf-8");
				header("Content-Disposition: attachment; filename=ride_acceptance_report.xls");
				header("Pragma: no-cache");
				header("Expires: 0");
				print "$header\n$data";
				exit;
}


if ($section == 'driver_trip_detail') {
		
		//Start Sorting
		$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
		$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
		$ord = ' ORDER BY t.tStartdate DESC';

		if ($sortby == 1) {
			if ($order == 0)
				$ord = " ORDER BY t.tStartDate ASC";
			else
				$ord = " ORDER BY t.tStartDate DESC";
		}

		if ($sortby == 2) {
			if ($order == 0)
				$ord = " ORDER BY d.vName ASC";
			else                    
				$ord = " ORDER BY d.vName DESC";
		}
		//End Sorting

		$cmp_ssql = "";
		if(SITE_TYPE =='Demo'){
			$cmp_ssql = " And t.tStartDate > '".WEEK_DATE."'";
		}

		// Start Search Parameters
		$ssql = '';
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
		$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
		$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : '';
		$serachTripNo = isset($_REQUEST['serachTripNo']) ? $_REQUEST['serachTripNo'] : '';
		$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : '';
		$date1=$startDate.' '."00:00:00";
		$date2=$endDate.' '."23:59:59";

		if($startDate!=''){
			$ssql.=" AND Date(t.tStartDate) >='".$startDate."'";
		}
		if($endDate!=''){
			$ssql.=" AND Date(t.tStartDate) <='".$endDate."'";
		}
		if ($iDriverId != '') {
			$ssql .= " AND d.iDriverId = '".$iDriverId."'";
		}
		if($serachTripNo!=''){
			$ssql.=" AND t.vRideNo ='".$serachTripNo."'";
		}

				
				 $sql_admin = "SELECT u.vName, u.vLastName, d.vAvgRating,t.fGDtime,t.tStartdate,t.tEndDate, t.tTripRequestDate, t.iFare, d.iDriverId, t.tSaddress,t.vRideNo, t.tDaddress, d.vName AS name,c.vName AS comp,c.vCompany, d.vLastName AS lname,t.eCarType,t.iTripId,vt.vVehicleType,t.iActive FROM register_driver d RIGHT JOIN trips t ON d.iDriverId = t.iDriverId LEFT JOIN vehicle_type vt ON vt.iVehicleTypeId = t.iVehicleTypeId LEFT JOIN  register_user u ON t.iUserId = u.iUserId JOIN company c ON c.iCompanyId=d.iCompanyId
			     WHERE 1=1 AND t.iActive = 'Finished' AND t.eCancelled='No' $ssql $cmp_ssql $ord ";
				
				$db_dlip = $obj->MySQLSelect($sql_admin);
				#echo "<pre>";print_r($db_dlip); exit;

				$header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']."  No" . "\t";
				$header .= "Address". "\t";
				$header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']."  Date" . "\t";
				$header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']. "\t";
				$header .= "Estimated Time" . "\t";
				$header .= "Actual Time" . "\t";
				$header .= "Variance" . "\t";
		
			    		
			 for ($j = 0; $j < count($db_dlip); $j++) {
					
								
					 $data .= $db_dlip[$j]['vRideNo']. "\t";
					 $data .= $db_dlip[$j]['tSaddress'].' -> '.$db_dlip[$j]['tDaddress'] . "\t";
					 $data .= $generalobjAdmin->DateTime($db_dlip[$j]['tStartdate']). "\t";
					 $data .= $generalobjAdmin->clearName($db_dlip[$j]['name']." ".$db_dlip[$j]['lname']) . "\t";
					
																	$ans=$generalobjAdmin->set_hour_min($db_dlip[$j]['fGDtime']);
																	if($ans['hour']!=0)
																	{
																		$ans1= $ans['hour']." Hours ".$ans['minute']." Minutes";
																	}
																	else
																	{
																		$ans1='';
																		if($ans['minute']!= 0)
																		{
																			$ans1.= $ans['minute']." Minutes ";
																		}
																			
																			$ans1.= $ans['second']." Seconds";
																	}
				
						$data .= $ans1. "\t";
						
																	 $a=strtotime($db_dlip[$j]['tStartdate']);
																	 $b=strtotime($db_dlip[$j]['tEndDate']);;
																	$diff_time=($b-$a);
																	//$diff_time=$diff_time*1000;
																	$ans_diff=$generalobjAdmin->set_hour_min($diff_time);
																	//print_r($ans);exit;
																	if($ans_diff['hour']!=0)
																	{
																		$ans_diff12 = $ans_diff['hour']." Hours ".$ans_diff['minute']." Minutes";
																	}
																	else
																	{
																		$ans_diff12 ='';
																		
																		if($ans_diff['minute']!= 0){
																			$ans_diff12.= $ans_diff['minute']." Minutes ";
																		}
																			$ans_diff12.= $ans_diff['second']." Seconds";
																	}	

						 $data .= $ans_diff12. "\t";
						
																		$ori_time=$db_dlip[$j]['fGDtime'];
																		$tak_time=$diff_time;
																		$ori_diff=$ori_time-$tak_time;
																		echo $ans_ori=$generalobjAdmin->set_hour_min(abs($ori_diff));
																		if($ans_ori['hour']!=0)
																	{
																		$ans2.= $ans_ori['hour']." Hours ".$ans_ori['minute']." Minutes";
																		if($ori_diff < 0)
																		{
																			$ans2.= " Late";
																		}
																		else{
																			
																		$ans2.= " Early";}
																	}
																	else
																	{
																		$ans2 = ''; 
																		if($ans_ori['minute']!= 0){
																			$ans2.= $ans_ori['minute']." Minutes ";
																		}
																		$ans2.= $ans_ori['second']." Seconds";
																		
																		if($ori_diff < 0)
																		{
																			$ans2.= " Late";
																		}
																		else{
																				$ans2.= " Early";
																			}
																	}
						$data .= $ans2. "\n";
				   }
				
				
				ob_clean();
				header("Content-type: application/octet-sdleam; charset=utf-8");
				header("Content-Disposition: attachment; filename=driver_trip_detail.xls");
				header("Pragma: no-cache");
				header("Expires: 0");
				print "$header\n$data";
				exit;
}


if ($section == 'wallet_report') {

	
$action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
$ssql = '';

if ($action != '') {

    $startDate = $_REQUEST['startDate'];
    $endDate = $_REQUEST['endDate'];
    $eUserType = $_REQUEST['eUserType'];
    $eFor = $_REQUEST['searchBalanceType'];
    $Payment_type = $_REQUEST['searchPaymentType'];

    if ($eUserType == "Driver") {

        $iDriverId = $_REQUEST['iDriverId'];
        $iUserId = "";
        $user_available_balance = $generalobj->get_user_available_balance($iDriverId, $eUserType);
    }

    if ($eUserType == "Rider") {

        $iUserId = $_REQUEST['iUserId'];
        $iDriverId = "";
        $user_available_balance = $generalobj->get_user_available_balance($iUserId, $eUserType);
    }

    if ($iDriverId != '') {
        $ssql .= " AND iUserId = '" . $iDriverId . "'";
    }
    if ($iUserId != '') {
        $ssql .= " AND iUserId = '" . $iUserId . "'";
    }

    if ($startDate != '') {
        $ssql .= " AND Date(dDate) >='" . $startDate . "'";
    }
    if ($endDate != '') {
        $ssql .= " AND Date(dDate) <='" . $endDate . "'";
    }

    if ($eUserType) {
        $ssql .= " AND eUserType = '" . $eUserType . "'";
    }
    if ($eFor != '') {
        $ssql .= " AND eFor = '" . $eFor . "'";
    }

    if ($Payment_type != '') {
        $ssql .= " AND eType = '" . $Payment_type . "'";
    }
}



			$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
			$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
/*			$ord = ' ORDER BY iUserWalletId DESC';*/
            $ord = ' ORDER BY dDate ASC';


				$sql_admin = "SELECT * From user_wallet where 1=1 $ssql $ord ";
				
				$db_dlip = $obj->MySQLSelect($sql_admin);


				$header .= "Description" . "\t";
				$header .= "Amount". "\t";
				$header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." No." . "\t";
				$header .= "Transaction Datet" . "\t";
				$header .= "Balance Type" . "\t";
				$header .= "Type". "\t";
				$header .= "Balance". "\t";
				
						
					for ($j = 0; $j < count($db_dlip); $j++) {
					 
														if ($db_dlip[$j]['eType'] == "Credit") {
															$db_dlip[$j]['currentbal'] = $prevbalance + $db_dlip[$j]['iBalance'];
                                                        } else {
                                                            $db_dlip[$j]['currentbal'] = $prevbalance - $db_dlip[$j]['iBalance'];
                                                        }

                                                        $prevbalance = $db_dlip[$j]['currentbal'];
                                                        if ($db_dlip[$j]['iTripId'] > 0) {
                                                            $sql_query = "SELECT * FROM `trips` WHERE iTripId =" . $db_dlip[$j]['iTripId'];
                                                            $db_result_trips = $obj->MySQLSelect($sql_query);
                                                            $ride_number = $db_result_trips[0]['vRideNo'];
                                                        
														} else {
															
                                                            $ride_number = '--';
                                                        }
					$pat = '/\#([^\"]*?)\#/';
                    preg_match($pat, $db_dlip[$j]['tDescription'], $tDescription_value);
                    $tDescription_translate = $langage_lbl_admin[$tDescription_value[1]];
                    $row_tDescription = str_replace($tDescription_value[0], $tDescription_translate, $db_dlip[$j]['tDescription']);	
					$data .= $row_tDescription . "\t";
					$data .= $generalobj->trip_currency($db_dlip[$j]['iBalance']) . "\t";
					$data .= $ride_number ."\t";
					$data .= $generalobjAdmin->DateTime($db_dlip[$j]['dDate']) ."\t";
					$data .= $db_dlip[$j]['eFor']."\t";
					$data .= $db_dlip[$j]['eType']."\t";
					$data .= $generalobj->trip_currency($db_dlip[$j]['currentbal'])."\n";
					
				}
				
				ob_clean();
				header("Content-type: application/octet-sdleam; charset=utf-8");

				header("Content-Disposition: attachment; filename=wallet_report.xls");

				header("Pragma: no-cache");
				header("Expires: 0");
				print "$header\n$data";
				exit;
}

if ($section == 'cab_booking') {

$action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');

$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;

$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';

$option = isset($_REQUEST['option'])?stripslashes($_REQUEST['option']):"";

$keyword = isset($_REQUEST['keyword'])?stripslashes($_REQUEST['keyword']):"";
$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : "";

$ord = ' ORDER BY cb.iCabBookingId DESC';

if($sortby == 1){
  if($order == 0)
    $ord = " ORDER BY ru.vName ASC";
  else
    $ord = " ORDER BY ru.vName DESC";
}

if($sortby == 2){
  if($order == 0)
    $ord = " ORDER BY cb.dBooking_date ASC";
  else
    $ord = " ORDER BY cb.dBooking_date DESC";
}

if($sortby == 3){
  if($order == 0)
    $ord = " ORDER BY cb.vSourceAddresss ASC";
  else
    $ord = " ORDER BY cb.vSourceAddresss DESC";
}

if($sortby == 4){
  if($order == 0)
    $ord = " ORDER BY cb.tDestAddress ASC";
  else
    $ord = " ORDER BY cb.tDestAddress DESC";
}


if($sortby == 5){
  if($order == 0)
    $ord = " ORDER BY cb.eStatus ASC";
  else
    $ord = " ORDER BY cb.eStatus DESC";
}

if($sortby == 6){
  if($order == 0)
    $ord = " ORDER BY cb.vBookingNo ASC";
  else
    $ord = " ORDER BY cb.vBookingNo DESC";
}

if($sortby == 7){
  if($order == 0)
    $ord = " ORDER BY cb.eType ASC";
  else
    $ord = " ORDER BY cb.eType DESC";
}

$adm_ssql = "";

if (SITE_TYPE == 'Demo') {

    $adm_ssql = " And cb.dAddredDate > '" . WEEK_DATE . "'";

}

$ssql = '';

if($keyword != ''){

    if($option != '') {

        if($eType != ''){

         $ssql.= " AND ".stripslashes($option)." LIKE '%".$generalobjAdmin->clean($keyword)."%' AND cb.eType = '".$generalobjAdmin->clean($eType)."'";

        } else {

          $ssql.= " AND ".stripslashes($option)." LIKE '%".$generalobjAdmin->clean($keyword)."%'";

        }

    } else {
        if($eType != ''){
            $ssql.= " AND (CONCAT(ru.vName,' ',ru.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.tDestAddress LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.vSourceAddresss  LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.vBookingNo LIKE '".$generalobjAdmin->clean($keyword)."' OR cb.eStatus LIKE '%".$generalobjAdmin->clean($keyword)."%') AND cb.eType = '".$generalobjAdmin->clean($eType)."'";
        } else {
        $ssql.= " AND (CONCAT(ru.vName,' ',ru.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.tDestAddress LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.vSourceAddresss  LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.vBookingNo LIKE '".$generalobjAdmin->clean($keyword)."' OR cb.eStatus LIKE '%".$generalobjAdmin->clean($keyword)."%')";
        }

    }

} else if($eType != '' && $keyword == '') {
     $ssql.= " AND cb.eType = '".$generalobjAdmin->clean($eType)."'";
}

$sql = "SELECT cb.*,CONCAT(ru.vName,' ',ru.vLastName) as rider,CONCAT(rd.vName,' ',rd.vLastName) as driver,vt.vVehicleType FROM cab_booking as cb LEFT JOIN register_user as ru on ru.iUserId=cb.iUserId LEFT JOIN register_driver as rd on rd.iDriverId=cb.iDriverId LEFT JOIN vehicle_type as vt on vt.iVehicleTypeId=cb.iVehicleTypeId WHERE 1=1 $ssql $adm_ssql $ord";

$data_drv = $obj->MySQLSelect($sql);

//echo "<pre>";print_r($data_drv); exit;

if($APP_TYPE == 'Ride-Delivery' || $APP_TYPE == 'Ride-Delivery-UberX'){
    $header .= $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']. "\t";
}

$header .= $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']." No" . "\t";

$header .= $langage_lbl_admin['LBL_RIDERS_ADMIN']. "\t";

$header .= "Date" . "\t";

$header .= "Expected Source Location" . "\t";

if($APP_TYPE != "UberX"){
    $header .= "Expected Destination Location" . "\t";
}

$header .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'] . "\t";

$header .= "Status". "\t";
                

for ($j = 0; $j < count($data_drv); $j++) {
    $eType = $data_drv[$j]['eType'];

    if($eType == 'Ride'){

    $trip_type = 'Ride';

    } else if($eType == 'UberX') {

    $trip_type = 'Other Services';

    }  else if($eType == 'Deliver') {

    $trip_type = 'Delivery';

    }

    $systemTimeZone = date_default_timezone_get();

    if($data_drv[$j]['dBooking_date']!= "" && $data_drv[$j]['vTimeZone'] != "")  {

     $dBookingDate = converToTz($data_drv[$j]['dBooking_date'],$data_drv[$j]['vTimeZone'],$systemTimeZone);

    } else {

     $dBookingDate = $data_drv[$j]['dBooking_date'];

    }

    if($APP_TYPE == 'Ride-Delivery' || $APP_TYPE == 'Ride-Delivery-UberX'){
        $data .= $trip_type . "\t";
    }

    $data .= $generalobjAdmin->clearName($data_drv[$j]['vBookingNo']) . "\t";

    $data .= $generalobjAdmin->clearName($data_drv[$j]['rider']) . "\t";

    $data .= $generalobjAdmin->DateTime($dBookingDate) ."\t";

    $string = $data_drv[$j]['vSourceAddresss'];
    $data .= str_replace(array("\n", "\r", "\r\n", "\n\r"),' ',$string)."\t";

    if($APP_TYPE != "UberX"){
        $string1 = $data_drv[$j]['tDestAddress'];
        $data .= str_replace(array("\n", "\r", "\r\n", "\n\r"),' ',$string1)."\t";
    }

    /*Driver Details*/

    if ($data_drv[$j]['eAutoAssign'] == "Yes" && $data_drv[$j]['eType'] == "Deliver" && $data_drv[$j]['iDriverId'] == 0 && $data_drv[$j]['eStatus'] != 'Cancel' && $APP_DELIVERY_MODE == "Multi") {

        $data .=  $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']. ": Auto Assign ( Vehicle Type : ". $data_drv[$j]['vVehicleType']." )"."\t";
    } else if ($data_drv[$j]['eAutoAssign'] == "Yes" && $data_drv[$j]['iDriverId'] == 0) {
        $data .=  $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']. " : Auto Assign ( Car Type : ". $data_drv[$j]['vVehicleType']." )"."\t";
    } else if ($data_drv[$j]['eStatus'] == "Pending" && (strtotime($data_drv[$j]['dBooking_date'])>strtotime(date('Y-m-d'))) && $data_drv[$j]['iDriverId'] == 0) {
        $data .=  "( ".$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT']." : ". $data_drv[$j]['vVehicleType'] ." )"."\t";
    } else if($data_drv[$j]['eCancelBy'] == "Driver" && $data_drv[$j]['eStatus'] == "Cancel" && $data_drv[$j]['iDriverId'] == 0) { 
        $data .=  "( ". $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT']." : ". $data_drv[$j]['vVehicleType'].")"."\t";
    } else if ($data_drv[$j]['driver'] != "" && $data_drv[$j]['driver'] != "0") {
        $data .= $generalobjAdmin->clearName($data_drv[$j]['driver'])."( ".$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT']." :".$data_drv[$j]['vVehicleType'].")"."\t";
    } else  { 
        $data .= "( ". $langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT']." : ". $data_drv[$j]['vVehicleType'] .")"."\t";
    } 
/*Status*/
    $setcurrentTime = strtotime(date('Y-m-d H:i:s'));
    $bookingdate =  date("Y-m-d H:i", strtotime('+30 minutes',  strtotime($data_drv[$j]['dBooking_date'])));
    $bookingdatecmp =strtotime($bookingdate);
    if($data_drv[$j]['eStatus'] == "Assign" && $bookingdatecmp > $setcurrentTime) {

       $data .= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Assigned"."\n";

    } else if($data_drv[$j]['eStatus'] == 'Accepted'){

        $data .= $data_drv[$j]['eStatus']."\n";

    } else if($data_drv[$j]['eStatus'] == 'Declined'){

        $data .= $data_drv[$j]['eStatus']."\n";
    } else {
        $sql="select iActive, eCancelledBy from trips where iTripId=".$data_drv[$j]['iTripId'];
        $data_stat=$obj->MySQLSelect($sql);

        if($data_stat) {

            for($d=0;$d<count($data_stat);$d++) {

                if($data_stat[$d]['iActive'] == "Canceled") {

                    $eCancelledBy = ($data_stat[$d]['eCancelledBy'] == 'Passenger') ? $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'] : $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];

                    $data .= "Canceled By ".$eCancelledBy."\n";

                } else if($data_stat[$d]['iActive'] == "Finished" && $data_stat[$d]['eCancelledBy'] == "Driver" ){
                     $data .= "Canceled By ".$eCancelledBy."\n";
                } else {

                     $data .= $data_stat[$d]['iActive']."\n";     

                }

            }

        } else {

            if($data_drv[$j]['eStatus'] == "Cancel") {

                if($data_drv[$j]['eCancelBy'] == "Driver") {

                    $data .= "Canceled By ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']."\n";

                }else if($data_drv[$j]['eCancelBy'] == "Rider") {

                    $data .= "Canceled By ".$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']."\n";

                } else {

                    $data .= "Canceled By Admin"."\n";

                }

            } else {                




                if($data_drv[$j]['eStatus'] == 'Pending' && $bookingdatecmp > $setcurrentTime ){

                    $data .= $data_drv[$j]['eStatus']."\n";

                } else {

                    $data .= 'Expired'."\n";

                }

            }

        }
    }

                    
}
               

ob_clean();

header("Content-type: application/octet-sdleam; charset=utf-8");

header("Content-Disposition: attachment; filename=ScheduledBookings.xls");

header("Pragma: no-cache");

header("Expires: 0");

print "$header\n$data";

exit;

}


?>