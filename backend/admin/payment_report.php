<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$script = 'Payment_Report';

function cleanNumber($num) {
	return str_replace(',','',$num);
}

//data for select fields
$sql = "select iCompanyId,vCompany,vEmail from company WHERE eStatus != 'Deleted' order by vCompany";
$db_company = $obj->MySQLSelect($sql);
// echo "<pre>"; print_r($db_company); die;

$sql = "select iDriverId,CONCAT(vName,' ',vLastName) AS driverName,vEmail from register_driver WHERE eStatus != 'Deleted' order by vName";
$db_drivers = $obj->MySQLSelect($sql);

$sql = "select iUserId,CONCAT(vName,' ',vLastName) AS riderName,vEmail from register_user WHERE eStatus != 'Deleted' order by vName";
$db_rider = $obj->MySQLSelect($sql);
//data for select fields
//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
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
//End Sorting
// Start Search Parameters
$ssql = '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$searchCompany = isset($_REQUEST['searchCompany']) ? $_REQUEST['searchCompany'] : '';
$searchDriver = isset($_REQUEST['searchDriver']) ? $_REQUEST['searchDriver'] : '';
$searchRider = isset($_REQUEST['searchRider']) ? $_REQUEST['searchRider'] : '';
$serachTripNo = isset($_REQUEST['serachTripNo']) ? $_REQUEST['serachTripNo'] : '';
$searchDriverPayment = isset($_REQUEST['searchDriverPayment']) ? $_REQUEST['searchDriverPayment'] : '';
$searchPaymentType = isset($_REQUEST['searchPaymentType']) ? $_REQUEST['searchPaymentType'] : '';
$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : '';
$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : '';
$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : '';

if ($action == 'search') {
    if ($startDate != '') {
        $ssql .= " AND Date(tr.tTripRequestDate) >='" . $startDate . "'";
    }
    if ($endDate != '') {
        $ssql .= " AND Date(tr.tTripRequestDate) <='" . $endDate . "'";
    }
    if ($serachTripNo != '') {
        $ssql .= " AND tr.vRideNo ='" . $serachTripNo . "'";
    }
    if ($searchCompany != '') {
        $ssql .= " AND rd.iCompanyId ='" . $searchCompany . "'";
    }
    if ($searchDriver != '') {
        $ssql .= " AND tr.iDriverId ='" . $searchDriver . "'";
    }
    if ($searchRider != '') {
        $ssql .= " AND tr.iUserId ='" . $searchRider . "'";
    }
    if ($searchDriverPayment != '') {
        $ssql .= " AND tr.eDriverPaymentStatus ='" . $searchDriverPayment . "'";
    }
    if ($searchPaymentType != '') {
        $ssql .= " AND tr.vTripPaymentMode ='" . $searchPaymentType . "'";
    }
    if ($eType != '') {
        $ssql .= " AND tr.eType ='" . $eType . "'";
    }
}

$trp_ssql = "";
if (SITE_TYPE == 'Demo') {
    $trp_ssql = " And tr.tTripRequestDate > '" . WEEK_DATE . "'";
}

//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT tr.iFare,tr.fTripGenerateFare,tr.fCommision, tr.fDiscount, tr.fWalletDebit, tr.fTipPrice,tr.vTripPaymentMode,( SELECT COUNT(tr.iTripId) FROM trips AS tr WHERE if(tr.iActive ='Canceled',if(tr.vTripPaymentMode='Card',1=1,0),1=1) AND tr.iActive ='Finished' $ssql $trp_ssql) AS Total FROM trips AS tr
	LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId 
	LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId 
	LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId 
	WHERE if(tr.iActive ='Canceled',if(tr.vTripPaymentMode='Card',1=1,0),1=1) AND tr.iActive ='Finished' $ssql $trp_ssql";
$totalData = $obj->MySQLSelect($sql);
//echo "<pre>"; print_r($totalData); exit;


$driver_payment = 0.00;
$total_tip = 0.00;
$tot_fare = 0.00;
$tot_site_commission = 0.00;
$tot_promo_discount = 0.00;
$tot_driver_refund = 0.00;
$tot_wallentPayment = 0.00;
foreach ($totalData as $dtps) {
    $totalfare = $dtps['fTripGenerateFare'];
    $site_commission = $dtps['fCommision'];
    $promocodediscount = $dtps['fDiscount'];
    $wallentPayment = $dtps['fWalletDebit'];
    $fTipPrice = $dtps['fTipPrice'];
    //$driver_payment = $totalfare - cleanNumber($site_commission);
    if($dtps['vTripPaymentMode'] == "Cash"){
      $driver_payment =  cleanNumber($promocodediscount) + cleanNumber($wallentPayment) - cleanNumber($site_commission);
    }else{
      $driver_payment = $totalfare - cleanNumber($site_commission) + cleanNumber($fTipPrice);
    }
    $tot_fare = $tot_fare + cleanNumber($totalfare);
    $tot_site_commission = $tot_site_commission + cleanNumber($site_commission);
    $tot_promo_discount = $tot_promo_discount + cleanNumber($promocodediscount);
    $tot_wallentPayment = $tot_wallentPayment + cleanNumber($wallentPayment);
    $total_tip = $total_tip + cleanNumber($fTipPrice);
    $tot_driver_refund = $tot_driver_refund + cleanNumber($driver_payment);  
    $cashPayment = $site_commission;
    $cardPayment = $totalfare-$site_commission;
}
 
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); //total pages we going to have
$show_page = 1;

//-------------if page is setcheck------------------//
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             //it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    } else {
        // error - show first set of results
        $start = 0;
        $end = $per_page;
    }
} else {
    // if page isn't set, show first set of results
    $start = 0;
    $end = $per_page;
}

// display pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$tpages = $total_pages;
if ($page <= 0)
    $page = 1;
//Pagination End
$sql = "SELECT tr.iTripId,tr.vRideNo,tr.iDriverId,tr.iUserId,tr.tTripRequestDate, tr.eType, tr.eHailTrip,tr.fTripGenerateFare,tr.fCommision, tr.fDiscount, tr.fWalletDebit, tr.fTipPrice,tr.eDriverPaymentStatus,tr.ePaymentCollect,tr.vTripPaymentMode,tr.iActive,c.vCompany,concat(rd.vName,' ',rd.vLastName) as drivername,concat(ru.vName,' ',ru.vLastName) as riderName FROM trips AS tr LEFT JOIN register_driver AS rd ON tr.iDriverId = rd.iDriverId LEFT JOIN register_user AS ru ON tr.iUserId = ru.iUserId LEFT JOIN company as c ON rd.iCompanyId = c.iCompanyId WHERE  if(tr.iActive ='Canceled',if(tr.vTripPaymentMode='Card',1=1,0),1=1) AND tr.iActive ='Finished' $ssql $trp_ssql $ord LIMIT $start, $per_page";
$db_trip = $obj->MySQLSelect($sql);


/*print_r($db_trip);
die;*/


$endRecord = count($db_trip);
$var_filter = "";
foreach ($_REQUEST as $key => $val) {
    if ($key != "tpages" && $key != 'page')
        $var_filter .= "&$key=" . stripslashes($val);
}

$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . $var_filter;
$Today = Date('Y-m-d');
$tdate = date("d") - 1;
$mdate = date("d");
$Yesterday = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y")));

$curryearFDate = date("Y-m-d", mktime(0, 0, 0, '1', '1', date("Y")));
$curryearTDate = date("Y-m-d", mktime(0, 0, 0, "12", "31", date("Y")));
$prevyearFDate = date("Y-m-d", mktime(0, 0, 0, '1', '1', date("Y") - 1));
$prevyearTDate = date("Y-m-d", mktime(0, 0, 0, "12", "31", date("Y") - 1));

$currmonthFDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $tdate, date("Y")));
$currmonthTDate = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, date("d") - $mdate, date("Y")));
$prevmonthFDate = date("Y-m-d", mktime(0, 0, 0, date("m") - 1, date("d") - $tdate, date("Y")));
$prevmonthTDate = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - $mdate, date("Y")));

$monday = date('Y-m-d', strtotime('sunday this week -1 week'));
$sunday = date('Y-m-d', strtotime('saturday this week'));

$Pmonday = date('Y-m-d', strtotime('sunday this week -2 week'));
$Psunday = date('Y-m-d', strtotime('saturday this week -1 week'));
?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?= $SITE_NAME ?> | Payment Report</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
<?php  include_once('global_files.php'); ?>
		<style>
			.setteled-class{
				background-color:#bddac5
			}
		</style>
    </head>
    <!-- END  HEAD-->

    <!-- BEGIN BODY-->
    <body class="padTop53 " >
        <!-- Main LOading -->
        <!-- MAIN WRAPPER -->
        <div id="wrap">
<?php  include_once('header.php'); ?>
<?php  include_once('left_menu.php'); ?>
            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div id="add-hide-show-div">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2>Payment Report</h2>
                                <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php  include('valid_msg.php'); ?>
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post" >
                        <div class="Posted-date mytrip-page payment-report">
                            <input type="hidden" name="action" value="search" />
                            <h3>Search <?php  echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN'];?>...</h3>
                            <span>
                                <a style="cursor:pointer" onClick="return todayDate('dp4', 'dp5');"><?= $langage_lbl_admin['LBL_MYTRIP_Today']; ?></a>
                                <a style="cursor:pointer" onClick="return yesterdayDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Yesterday']; ?></a>
                                <a style="cursor:pointer" onClick="return currentweekDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Current_Week']; ?></a>
                                <a style="cursor:pointer" onClick="return previousweekDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Previous_Week']; ?></a>
                                <a style="cursor:pointer" onClick="return currentmonthDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Current_Month']; ?></a>
                                <a style="cursor:pointer" onClick="return previousmonthDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Previous Month']; ?></a>
                                <a style="cursor:pointer" onClick="return currentyearDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Current_Year']; ?></a>
                                <a style="cursor:pointer" onClick="return previousyearDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Previous_Year']; ?></a>
                            </span> 
                            <span>
                                <input type="text" id="dp4" name="startDate" placeholder="From Date" class="form-control" value="" readonly="" style="cursor:default; background-color: #fff" />
                                <input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control" value="" readonly="" style="cursor:default; background-color: #fff"/>
                                <div class="col-lg-3 select001">
                                    <select class="form-control filter-by-text" name = 'searchCompany' data-text="Select Company" id="searchCompany">
                                        <option value="">Select Company</option>
                                            <?php  foreach ($db_company as $dbc) { ?>
                                            <option value="<?php  echo $dbc['iCompanyId']; ?>" <?php  if ($searchCompany == $dbc['iCompanyId']) {
                                                echo "selected";
                                            } ?>><?php  echo $generalobjAdmin->clearCmpName($dbc['vCompany']); ?> - ( <?php  echo $generalobjAdmin->clearEmail($dbc['vEmail']); ?> )</option>
                                        <?php  } ?>
                                    </select>
                                </div>
                                <div class="col-lg-3 select001">
                                    <select class="form-control filter-by-text driver_container" name = 'searchDriver' data-text="Select <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?>">
                                        <option value="">Select <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></option>
                                            <?php  foreach ($db_drivers as $dbd) { ?>
                                            <option value="<?php  echo $dbd['iDriverId']; ?>" <?php  if ($searchDriver == $dbd['iDriverId']) {
                                                echo "selected";
                                            } ?>><?php  echo $generalobjAdmin->clearName($dbd['driverName']); ?> - ( <?php  echo $generalobjAdmin->clearEmail($dbd['vEmail']); ?> )</option>
                                        <?php  } ?>
                                    </select>
                                </div>
                            </span>
                        </div>

                        <div class="row payment-report payment-report1 payment-report2">
                            <div class="col-lg-3">
                                <select class="form-control filter-by-text" name = 'searchRider' data-text="Select <?php  echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN'];?>">
                                    <option value="">Select <?php  echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN'];?></option>
                                    <?php  foreach ($db_rider as $dbr) { ?>
                                        <option value="<?php  echo $dbr['iUserId']; ?>" <?php  if ($searchRider == $dbr['iUserId']) {
                                        echo "selected";
                                    } ?>><?php  echo $generalobjAdmin->clearName($dbr['riderName']); ?> - ( <?php  echo $generalobjAdmin->clearEmail($dbr['vEmail']); ?> )</option>
                                    <?php  } ?>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <select class="form-control" name='searchPaymentType' data-text="Select <?php  echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN'];?>">
                                    <option value="">Select Payment Type</option>
                                    <option value="Cash" <?php if($searchPaymentType == "Cash"){?>selected <?php }?>>Cash</option>
                                    <option value="Card" <?php if($searchPaymentType == "Card"){?>selected <?php }?>>Card</option>
                                   <!--  <option value="Paypal" <?php if($searchPaymentType == "Paypal"){?>selected <?php }?>>Paypal</option> -->
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <select class="form-control" name='searchDriverPayment' data-text="Select <?php  echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN'];?>">
                                    <option value="">Select <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> Payment Status</option>
                                    <option value="Settelled" <?php  if($searchDriverPayment == "Settelled"){?>selected <?php  } ?>>Settled</option>
                                    <option value="Unsettelled" <?php  if($searchDriverPayment == "Unsettelled"){?>selected <?php  } ?>>Unsettled</option>
                                </select>
                            </div>
							<div class="col-lg-2">
								  <input type="text" id="serachTripNo" name="serachTripNo" placeholder="<?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'];?> Number" class="form-control search-trip001" value="<?php  echo $serachTripNo; ?>"/>
							</div>
                        </div>

                        <?php  if($APP_TYPE == 'Ride-Delivery-UberX' || $APP_TYPE == 'Ride-Delivery'){ ?>
                        <div class="row payment-report payment-report1 payment-report2" style="margin-top: 21px;">
                            <div class="col-lg-2">
                                  <select class="form-control" name = 'eType' >
                                       <option value="">&nbsp;&nbsp;Service Type</option>
                                       <option value="Ride" <?php  if($eType == "Ride") { echo "selected"; } ?>>&nbsp;&nbsp;<?php  echo $langage_lbl_admin['LBL_RIDE_TXT_ADMIN_SEARCH'];?> </option>
                                       <option value="Deliver" <?php  if($eType == "Deliver") { echo "selected"; } ?>>&nbsp;&nbsp;Delivery</option>
                                       <option value="UberX" <?php  if($eType == "UberX") { echo "selected"; } ?>>&nbsp;&nbsp;Other Services</option>
                                  </select>
                            </div>
                        </div>
                        <?php  } ?>
                        <div class="tripBtns001"><b>
                                <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'payment_report.php'"/>
                                <?php   if(count($db_trip) > 0){ ?>
                                <button type="button" onClick="reportExportTypes('driver_payment')" class="export-btn001" >Export</button></b>
                                <?php  } ?>
                        </div>
                    </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                               <div class="table-responsive">
                                    <form name="_list_form" id="_list_form" class="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
                                        <input type="hidden" id="actionpayment" name="actionpayment" value="pay_driver">
                                        <input type="hidden"  name="iTripId" id="iTripId" value="">
                                        <input type="hidden"  name="ePayDriver" id="ePayDriver" value="">
                                        <table class="table table-bordered" id="dataTables-example123" >
                                            <thead>
                                                <tr>
                                                    <?php  if($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery'){?>
                                                   <th width="10%"><a href="javascript:void(0);" onClick="Redirect(6,<?php  if ($sortby == '6') {
                                                        echo $order;
                                                    } else { ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN']; ?> <?php  if ($sortby == 6) {
                                                        if ($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  }
                                                    } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                    <?php  } ?>
                                                    <th><?php  echo $langage_lbl_admin['LBL_RIDE_NO_ADMIN']; ?> </th>
                                                    <th width="10%"><a href="javascript:void(0);" onClick="Redirect(1,<?php  if ($sortby == '1') {
                                                            echo $order;
                                                        } else { ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> <?php  if ($sortby == 1) {
                                                            if ($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  }
                                                        } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                    <th width="10%"><a href="javascript:void(0);" onClick="Redirect(2,<?php  if ($sortby == '2') {
                                                        echo $order;
                                                } else {
                                                            ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?> <?php  if ($sortby == 2) {
                                                                if ($order == 0) {
                                                                ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  }
                                                    } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>

                                                    <th width="10%"><a href="javascript:void(0);" onClick="Redirect(3,<?php  if ($sortby == '3') {
                                                echo $order;
                                            } else { ?>0<?php  } ?>)"><?= $langage_lbl_admin['LBL_RIDE_TXT_ADMIN']; ?> Date <?php  if ($sortby == 3) {
                                                if ($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  }
                                            } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                    <!--<th>Address</th>-->
                                                    <!--<th style="text-align:right;">A=Total Fare</th>-->
                                                    <th style="text-align:right;">A=(Base Fare+ Distance Fare+ Time Fare)</th>
                                                    <th style="text-align:right;">B=Platform Fees</th>
                                                    <th>C= Promo Code Discount</th>
                                                    <th style="text-align:right;">D = Wallet Debit</th>
                                                    <?php  if ($ENABLE_TIP_MODULE == "Yes") { ?>
                                                    <th>E=Tip</th>
                                                    <?php  } ?>
                                                    <th style="text-align:right;"><?= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> pay Amount</th>
                                                    <th><?= $langage_lbl_admin['LBL_RIDE_TXT_ADMIN']; ?> Status</th>
                                                    <th>Payment method</th>
                                                    <th><?= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Payment Status</th> 
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $set_unsetarray = array();
                                                if(count($db_trip) > 0){
                                                for($i=0;$i<count($db_trip);$i++)
                                                {
													$class_setteled ="";
													if($db_trip[$i]['eDriverPaymentStatus'] == 'Settelled'){
														$class_setteled = "setteled-class";
													}
                                                $totalfare = $db_trip[$i]['fTripGenerateFare'];
                                                $site_commission = $db_trip[$i]['fCommision'];
                                                $promocodediscount = $db_trip[$i]['fDiscount'];
                                                $wallentPayment = $db_trip[$i]['fWalletDebit'];
												$tipPayment = 0;
												if ($ENABLE_TIP_MODULE == "Yes") {
													$tipPayment = $db_trip[$i]['fTipPrice'];
												}
                                                //$driver_payment = ($totalfare + $tipPayment) - $site_commission;
                                                if($db_trip[$i]['vTripPaymentMode'] == "Cash"){
                                                  $driver_payment =  cleanNumber($promocodediscount) + cleanNumber($wallentPayment) - cleanNumber($site_commission);
                                                }else{
                                                  $driver_payment = $totalfare - cleanNumber($site_commission) + cleanNumber($tipPayment);
                                                }
                                                $set_unsetarray[] = $db_trip[$i]['eDriverPaymentStatus'];

                                                $eTypenew = $db_trip[$i]['eType'];
                                                if($eTypenew == 'Ride'){
                                                    $trip_type = 'Ride';
                                                } else if($eTypenew == 'UberX') {
                                                    $trip_type = 'Other Services';
                                                } else {
                                                    $trip_type = 'Delivery';
                                                }

                                                ?>
                                                <tr class="gradeA <?=$class_setteled?>">
                                                    <?php  if($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery'){ ?> 
                                                    <td align="left">
                                                    <?php  if($db_trip[$i]['eHailTrip'] != "Yes"){
                                                            echo $trip_type;
                                                        }else{
                                                            echo $trip_type." ( Hail )";
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php  } ?>
                                                    <td><?= $db_trip[$i]['vRideNo']; ?></td>
                                                    <td><?= $generalobjAdmin->clearName($db_trip[$i]['drivername']); ?></td>
                                                    <td><?= $generalobjAdmin->clearName($db_trip[$i]['riderName']); ?></td>
                                                    <td><?= $generalobjAdmin->DateTime($db_trip[$i]['tTripRequestDate']); ?></td>

                                                    <td align="right">
                                                    <?php 
                                                    if ($db_trip[$i]['fTripGenerateFare'] != "" && $db_trip[$i]['fTripGenerateFare'] != 0) {
                                                        echo $generalobj->trip_currency($db_trip[$i]['fTripGenerateFare']);
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                    </td>

                                                    <td align="right"><?php  if ($db_trip[$i]['fCommision'] != "" && $db_trip[$i]['fCommision'] != 0) {
                                                            echo $generalobj->trip_currency($db_trip[$i]['fCommision']);
                                                        } else {
                                                            echo '-';
                                                        } ?></td>

                                                    <td align="right"><?php  if ($db_trip[$i]['fDiscount'] != "" && $db_trip[$i]['fDiscount'] != 0) {
                                                            echo $generalobj->trip_currency($db_trip[$i]['fDiscount']);
                                                        } else {
                                                            echo '-';
                                                        } ?></td>

                                                    <td align="right"><?php  if ($db_trip[$i]['fWalletDebit'] != "" && $db_trip[$i]['fWalletDebit'] != 0) {
                                                            echo $generalobj->trip_currency($db_trip[$i]['fWalletDebit']);
                                                        } else {
                                                            echo '-';
                                                        } ?></td>
                                                        <?php  if ($ENABLE_TIP_MODULE == "Yes") { ?>
                                                        <td>
                                                            <?php 
                                                            if ($db_trip[$i]['fTipPrice'] != "0") {
                                                                echo $generalobj->trip_currency($db_trip[$i]['fTipPrice']);
                                                                //echo $db_trip[$i]['fTipPrice'];
                                                            } else {
                                                                echo "-";
                                                            }
                                                            ?>
                                                        </td>
                                                        <?php  } ?>
                                                    <td align="right">
                                                        <?php 
                                                        if ($driver_payment != "" && $driver_payment != 0) {
                                                            echo $generalobj->trip_currency($driver_payment);
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?= $db_trip[$i]['iActive']; ?></td>
                                                    <td><?= $db_trip[$i]['vTripPaymentMode']; ?></td>
                                                    <td><?php 
                                                            if($db_trip[$i]['eDriverPaymentStatus'] == "Settelled"){
                                                                echo "Settled";
                                                            }
                                                            else if($db_trip[$i]['eDriverPaymentStatus'] == "Unsettelled"){
                                                                echo "Unsettled";
                                                            }
                                                            else{
                                                                echo $db_trip[$i]['eDriverPaymentStatus']; 
                                                            }
                                                        ?>
                                                     </td>
                                                    <td>
                                                        <?php  
                                                        if($db_trip[$i]['eDriverPaymentStatus'] == 'Unsettelled'){
                                                        ?>
                                                        <input class="validate[required]" type="checkbox" value="<?= $db_trip[$i]['iTripId'] ?>" id="iTripId_<?= $db_trip[$i]['iTripId'] ?>" name="iTripId[]">
                                                        <?php 
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php  } ?>
                                                <tr class="gradeA">
                                                    <td colspan="12" align="right">Total Fare</td>
                                                    <td align="right" colspan="2"><?= $generalobj->trip_currency($tot_fare); ?></td>
                                                </tr>
                                                <tr class="gradeA">
                                                    <td colspan="12" align="right">Total Platform Fees</td>
                                                    <td  align="right" colspan="2"><?= $generalobj->trip_currency($tot_site_commission); ?></td>
                                                </tr>
                                                <tr class="gradeA">
                                                    <td colspan="12" align="right">Total Promo Discount</td>
                                                    <td  align="right" colspan="2"><?= $generalobj->trip_currency($tot_promo_discount); ?></td>
                                                </tr>
                                                <tr class="gradeA">
                                                    <td colspan="12" align="right">Total Wallet Debit</td>
                                                    <td  align="right" colspan="2"><?= $generalobj->trip_currency($tot_wallentPayment); ?></td>
                                                </tr>
                                                    <?php  if ($ENABLE_TIP_MODULE == "Yes") { ?>
                                                    <tr class="gradeA">
                                                        <td colspan="12" align="right">Total Tip Amount</td>
                                                        <td  align="right" colspan="2"><?= $generalobj->trip_currency($total_tip); ?></td>
                                                    </tr>
                                                    <tr class="gradeA">
                                                        <td colspan="12" align="right">Total <?= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Payment</td>
                                                        <!--<td  align="right" colspan="2"><?= $generalobj->trip_currency($tot_driver_refund + $total_tip); ?></td>-->
                                                        <td  align="right" colspan="2"><?= $generalobj->trip_currency($tot_driver_refund); ?></td>
                                                    </tr>
                                                    <?php }else{?>
                                                    <tr class="gradeA">
                                                        <td colspan="12" align="right">Total <?= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Payment</td>
                                                        <td  align="right" colspan="2"><?= $generalobj->trip_currency($tot_driver_refund); ?></td>
                                                    </tr>
                                                    <?php  } ?>
                                                    <?php   if (in_array("Unsettelled", $set_unsetarray)) { ?>
                                                        <tr class="gradeA">
                                                            <td colspan="14" align="right"><div class="row payment-report-button">
                                                            <span style="margin-right: 15px;">
                                                            <a onClick="Paytodriver()" href="javascript:void(0);"><button class="btn btn-primary" type="button">Mark As Settled</button></a>
                                                            </span>
                                                            </div>
                                                            </td>
                                                        </tr>
                                                    <?php  }
                                                } else {?>
                                                    <tr class="gradeA">
                                                        <td colspan="14" style="text-align:center;">No Payment Details Found.</td>
                                                    </tr>
                                                    <?php  } ?>
                                                </tbody>
                                            </table>
                                        </form>
                                        <?php  include('pagination_n.php'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <!--END PAGE CONTENT -->
            </div>
        <!--END MAIN WRAPPER -->

<form name="pageForm" id="pageForm" action="action/payment_report.php" method="post" >
    <input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php  echo $sortby; ?>" >
    <input type="hidden" name="order" id="order" value="<?php  echo $order; ?>" >
    <input type="hidden" name="action" value="<?php  echo $action; ?>" >
    <input type="hidden" name="searchCompany" value="<?php  echo $searchCompany; ?>" >
    <input type="hidden" name="searchDriver" value="<?php  echo $searchDriver; ?>" >
    <input type="hidden" name="searchRider" value="<?php  echo $searchRider; ?>" >
    <input type="hidden" name="serachTripNo" value="<?php  echo $serachTripNo; ?>" >
    <input type="hidden" name="searchPaymentType" value="<?php  echo $searchPaymentType; ?>" >
    <input type="hidden" name="searchDriverPayment" value="<?php  echo $searchDriverPayment; ?>" >
    <input type="hidden" name="startDate" value="<?php  echo $startDate; ?>" >
    <input type="hidden" name="endDate" value="<?php  echo $endDate; ?>" >
    <input type="hidden" name="vStatus" value="" >
    <input type="hidden" name="eType" value="<?php  echo $eType; ?>" >
    <input type="hidden" name="method" id="method" value="" >
</form>

<?php  include_once('footer.php');?>
<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css" />
<link rel="stylesheet" href="css/select2/select2.min.css" />
<script src="js/plugins/select2.min.js"></script>
<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script>
    $('#dp4').datepicker()
        .on('changeDate', function (ev) {
        var endDate = $('#dp5').val();
        if (ev.date.valueOf() < endDate.valueOf()) {
            $('#alert').show().find('strong').text('The start date can not be greater then the end date');
        } else {
            $('#alert').hide();
            var startDate = new Date(ev.date);
            $('#startDate').text($('#dp4').data('date'));
        }
        $('#dp4').datepicker('hide');
    });
    $('#dp5').datepicker()
    .on('changeDate', function (ev) {
        var startDate = $('#dp4').val();
        if (ev.date.valueOf() < startDate.valueOf()) {
            $('#alert').show().find('strong').text('The end date can not be less then the start date');
        } else {
            $('#alert').hide();
            var endDate = new Date(ev.date);
            $('#endDate').text($('#dp5').data('date'));
        }
        $('#dp5').datepicker('hide');
    });

    $(document).ready(function () {
        $("#dp5").click(function(){
             $('#dp5').datepicker('show');
             $('#dp4').datepicker('hide');
        });

        $("#dp4").click(function(){
             $('#dp4').datepicker('show');
             $('#dp5').datepicker('hide');
        });

        if ('<?= $startDate ?>' != '') {
            $("#dp4").val('<?= $startDate ?>');
            $("#dp4").datepicker('update', '<?= $startDate ?>');
        }
        if ('<?= $endDate ?>' != '') {
            $("#dp5").datepicker('update', '<?= $endDate; ?>');
            $("#dp5").val('<?= $endDate; ?>');
        }
    });

    function setRideStatus(actionStatus) {
        window.location.href = "trip.php?type=" + actionStatus;
    }
    function todayDate() {
        $("#dp4").val('<?= $Today; ?>');
        $("#dp5").val('<?= $Today; ?>');
    }
    function reset() {
        location.reload();
    }
    function yesterdayDate()
    {
        $("#dp4").val('<?= $Yesterday; ?>');
        $("#dp4").datepicker('update', '<?= $Yesterday; ?>');
        $("#dp5").datepicker('update', '<?= $Yesterday; ?>');
        $("#dp4").change();
        $("#dp5").change();
        $("#dp5").val('<?= $Yesterday; ?>');
    }
    function currentweekDate(dt, df)
    {
        $("#dp4").val('<?= $monday; ?>');
        $("#dp4").datepicker('update', '<?= $monday; ?>');
        $("#dp5").datepicker('update', '<?= $sunday; ?>');
        $("#dp5").val('<?= $sunday; ?>');
    }
    function previousweekDate(dt, df)
    {
        $("#dp4").val('<?= $Pmonday; ?>');
        $("#dp4").datepicker('update', '<?= $Pmonday; ?>');
        $("#dp5").datepicker('update', '<?= $Psunday; ?>');
        $("#dp5").val('<?= $Psunday; ?>');
    }
    function currentmonthDate(dt, df)
    {
        $("#dp4").val('<?= $currmonthFDate; ?>');
        $("#dp4").datepicker('update', '<?= $currmonthFDate; ?>');
        $("#dp5").datepicker('update', '<?= $currmonthTDate; ?>');
        $("#dp5").val('<?= $currmonthTDate; ?>');
    }
    function previousmonthDate(dt, df)
    {
        $("#dp4").val('<?= $prevmonthFDate; ?>');
        $("#dp4").datepicker('update', '<?= $prevmonthFDate; ?>');
        $("#dp5").datepicker('update', '<?= $prevmonthTDate; ?>');
        $("#dp5").val('<?= $prevmonthTDate; ?>');
    }
    function currentyearDate(dt, df)
    {
        $("#dp4").val('<?= $curryearFDate; ?>');
        $("#dp4").datepicker('update', '<?= $curryearFDate; ?>');
        $("#dp5").datepicker('update', '<?= $curryearTDate; ?>');
        $("#dp5").val('<?= $curryearTDate; ?>');
    }
    function previousyearDate(dt, df)
    {
        $("#dp4").val('<?= $prevyearFDate; ?>');
        $("#dp4").datepicker('update', '<?= $prevyearFDate; ?>');
        $("#dp5").datepicker('update', '<?= $prevyearTDate; ?>');
        $("#dp5").val('<?= $prevyearTDate; ?>');
    }
    $("#Search").on('click', function () {
        if ($("#dp5").val() < $("#dp4").val()) {
            alert("From date should be lesser than To date.")
            return false;
        } else {
            var action = $("#_list_form").attr('action');
            var formValus = $("#frmsearch").serialize();
            window.location.href = action + "?" + formValus;
        }
    });
    $(function () {
        $("select.filter-by-text").each(function () {
            $(this).select2({
                placeholder: $(this).attr('data-text'),
                allowClear: true
            }); //theme: 'classic'
        });
    });
    $('#searchCompany').change(function() {
        var company_id = $(this).val(); //get the current value's option
        $.ajax({
            type:'POST',
            url:'ajax_find_driver_by_company.php',
            data:{'company_id':company_id},
            cache: false,
            success:function(data){
                $(".driver_container").html(data);
            }
        });
    });
</script>
</body>
<!-- END BODY-->
</html>