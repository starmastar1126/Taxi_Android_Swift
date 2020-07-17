<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$script = 'Wallet Report';
$sess_iAdminUserId = isset($_SESSION['sess_iAdminUserId'])?$_SESSION['sess_iAdminUserId']:'';
//echo $sess_iAdminUserId;exit;
//data for select fields
$rdr_ssql = "";
if (SITE_TYPE == 'Demo') {
    $rdr_ssql = " And tRegistrationDate > '" . WEEK_DATE . "'";
}

$sql = "select iDriverId,CONCAT(vName,' ',vLastName) AS driverName,vEmail from register_driver WHERE eStatus != 'Deleted' $rdr_ssql order by vName";
$db_drivers = $obj->MySQLSelect($sql);

$sql = "select iUserId,CONCAT(vName,' ',vLastName) AS riderName,vEmail from register_user WHERE eStatus != 'Deleted' $rdr_ssql order by vName";
$db_rider = $obj->MySQLSelect($sql);
//data for select fields

$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];


//$generalobj->setRole($abc,$url);
$script = 'Wallet Report';


$action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
$ssql = '';

$sql = "select * from register_driver WHERE 1 = 1 $rdr_ssql";
$db_driver_disp = $obj->MySQLSelect($sql);

$sql = "select * from register_user WHERE 1 = 1 $rdr_ssql";
$db_rider_dis = $obj->MySQLSelect($sql);


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

if (isset($_POST['action']) && $_POST['action'] == "paymentmember") {

    $eUserType = $_REQUEST['eUserType'];
    if ($eUserType == "Driver") {
        $iUserId = $_REQUEST['iDriverId'];
    } else {

        $iUserId = $_REQUEST['iUserId'];
    }

    $iBalance = $_REQUEST['iBalance'];
    $eFor = $_REQUEST['eFor'];
    $eType = $_REQUEST['eType'];
    $iTripId = 0;
    $tDescription = '#LBL_AMOUNT_DEBIT#';
    //$tDescription = 'Amount debited';
   // $tDescription = ' Amount ' . $_REQUEST['iBalance'] . ' debited from your account for withdrawal request';
    $ePaymentStatus = 'Unsettelled';
    $dDate = Date('Y-m-d H:i:s');
    
    $generalobj->InsertIntoUserWallet($iUserId, $eUserType, $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
    header("Location:wallet_report.php?" . $_SERVER['QUERY_STRING']);
    exit;
}

if (isset($_POST['action']) && $_POST['action'] == "addmoney") {
    $eUserType = $_REQUEST['eUserType'];
    if ($eUserType == "Driver") {
        $iUserId = $_REQUEST['iDriverId'];
    } else {
        $iUserId = $_REQUEST['iUserId'];
    }
    $iBalance = $_REQUEST['iBalance'];
    $eFor = $_REQUEST['eFor'];
    $eType = $_REQUEST['eType'];
    $iTripId = 0;
    $tDescription = '#LBL_AMOUNT_CREDIT#';
    //$tDescription = 'Amount credited';
   // $tDescription = ' Amount ' . $_REQUEST['iBalance'] . ' credited into your account from administrator';
    $ePaymentStatus = 'Unsettelled';
    $dDate = Date('Y-m-d H:i:s');
    if($sess_iAdminUserId != ""){
    $generalobj->InsertIntoUserWallet($iUserId, $eUserType, $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
    }
    header("Location:wallet_report.php?" . $_SERVER['QUERY_STRING']);
    exit;
}


$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY dDate ASC';//iUserWalletId DESC

$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(iUserWalletId) AS Total From user_wallet where 1=1 ".$ssql;
$totalData = $obj->MySQLSelect($sql);
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
if ($page <= 0){
    $page = 1;
}
//Pagination End
if($action == "search") { 
    $sql = "SELECT * From user_wallet where 1=1 $ssql $ord"; /*LIMIT $start,$per_page*/
    $db_result = $obj->MySQLSelect($sql);
    $endRecord = count($db_result);
}

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
        <title><?= $SITE_NAME ?> | <?php  echo $langage_lbl_admin['LBL_TRIPS_TXT_ADMIN']; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
<?php  include_once('global_files.php'); ?>
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
                                <h2>User Wallet Report</h2>
                                <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                            </div>
                        </div>
                        <hr />
                    </div>
					<?php  include('valid_msg.php'); ?>
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post" >
                        <div class="Posted-date mytrip-page payment-report">
                            <input type="hidden" name="action" value="search" />
                            <h3>Search by Date...</h3>
                            <span>
                                <a style="cursor:pointer" onClick="return todayDate('dp4', 'dp5');"><?= $langage_lbl['LBL_MYTRIP_Today']; ?></a>
                                <a style="cursor:pointer" onClick="return yesterdayDate('dFDate', 'dTDate');"><?= $langage_lbl['LBL_MYTRIP_Yesterday']; ?></a>
                                <a style="cursor:pointer" onClick="return currentweekDate('dFDate', 'dTDate');"><?= $langage_lbl['LBL_MYTRIP_Current_Week']; ?></a>
                                <a style="cursor:pointer" onClick="return previousweekDate('dFDate', 'dTDate');"><?= $langage_lbl['LBL_MYTRIP_Previous_Week']; ?></a>
                                <a style="cursor:pointer" onClick="return currentmonthDate('dFDate', 'dTDate');"><?= $langage_lbl['LBL_MYTRIP_Current_Month']; ?></a>
                                <a style="cursor:pointer" onClick="return previousmonthDate('dFDate', 'dTDate');"><?= $langage_lbl['LBL_MYTRIP_Previous Month']; ?></a>
                                <a style="cursor:pointer" onClick="return currentyearDate('dFDate', 'dTDate');"><?= $langage_lbl['LBL_MYTRIP_Current_Year']; ?></a>
                                <a style="cursor:pointer" onClick="return previousyearDate('dFDate', 'dTDate');"><?= $langage_lbl['LBL_MYTRIP_Previous_Year']; ?></a>
                            </span> 
                            <span>
                                <input type="text" id="dp4" name="startDate" placeholder="From Date" class="form-control" value="" readonly=""style="cursor:default; background-color: #fff" />
                                <input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control" value="" readonly="" style="cursor:default; background-color: #fff"/>
                                <div class="col-lg-3 select001">
                                    <select class="form-control" name='eUserType' id="eUserType" data-text="Select Rider" onChange="return show_hide_user_type(this.value);">
                                        <option value="">Search By User type</option>
                                        <option value="Driver" <?php if($eUserType == "Driver"){?>selected <?php }?> > <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> </option>
                                        <option value="Rider" <?php if($eUserType == "Rider"){?>selected <?php }?>> <?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?> </option>
                                    </select>
                                </div>
                                <div class="col-lg-3 select001 showhide-box001" id="sec_driver">
                                    <select class="form-control filter-by-text" name = 'iDriverId' id="searchDriver" data-text="Select <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?>">
                                        <option value="">Select <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></option>
                                        <?php  foreach ($db_drivers as $dbd) { ?>
                                            <option value="<?php  echo $dbd['iDriverId']; ?>" <?php  if ($iDriverId == $dbd['iDriverId']) { echo "selected"; } ?>><?php  echo $generalobjAdmin->clearName($dbd['driverName']); ?> - ( <?php  echo $generalobjAdmin->clearEmail($dbd['vEmail']); ?> )</option>
                                        <?php  } ?>
                                    </select>
                                </div>
                                <div class="col-lg-3 select001 showhide-box001" id="sec_rider">
                                    <select class="form-control filter-by-text" name = 'iUserId' id="searchRider" data-text="Select <?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?>">
                                        <option value="">Select <?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?></option>
                                                <?php  foreach ($db_rider as $dbr) { ?>
                                            <option value="<?php  echo $dbr['iUserId']; ?>" <?php 
                                                if ($iUserId == $dbr['iUserId']) {
                                                    echo "selected";
                                                }
                                                ?>><?php  echo $generalobjAdmin->clearName($dbr['riderName']); ?> - ( <?php  echo $generalobjAdmin->clearEmail($dbr['vEmail']); ?> )</option>
                                        <?php  } ?>
                                    </select>
                                </div>
                            </span>
                        </div>

                        <div class="row payment-report payment-report1">
                            <div class="col-lg-3">
                                <select class="form-control" name='searchPaymentType' data-text="Select Rider">
                                    <option value="">Search By Payment type</option>
                                    <option value="Credit" <?php if($Payment_type == "Credit"){?>selected <?php }?> >Credit</option>
                                    <option value="Debit" <?php if($Payment_type == "Debit"){?>selected <?php }?> >Debit</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <select class="form-control" name='searchBalanceType' data-text="Select Rider">
                                    <option value="">Search By Balance Type</option>
                                    <option value="Deposit" <?php if($eFor == "Deposit"){?>selected <?php }?>>Deposit</option>
                                    <option value="Booking" <?php if($eFor == "Booking"){?>selected <?php }?>>Booking</option>
                                    <option value="Refund" <?php if($eFor == "Refund"){?>selected <?php }?>>Refund</option>
                                    <option value="Withdrawl" <?php if($eFor == "Withdrawl"){?>selected <?php }?>>Withdrawal</option>
                                    <option value="Charges" <?php if($eFor == "Charges"){?>selected <?php }?>>Charges</option>
                                    <option value="referral"<?php if($eFor == "referral	"){?>selected <?php }?>>referral</option>
                                </select>
                            </div>
                        </div>
                        <div class="tripBtns001"><b>
                                <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'wallet_report.php'"/>
                                 <?php  if (isset($db_result) && count($db_result) > 0) { ?>
								<button type="button" onClick="reportExportTypes('wallet_report')" class="export-btn001" >Export</button></b>
								<?php  } ?>
								</b>
                        </div>
                    </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="table-responsive" <?php  if($action != "search"){ ?>style="display:none;"<?php  }else{?> <?php  } ?>>
                                    <form class="_list_form" id="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
                                        <table class="table table-striped table-bordered table-hover" >
                                            <thead>
                                                <tr>
                                                    <th>Description</th>
                                                    <th class="align-right">Amount</th>
                                                    <th><?php  echo $langage_lbl_admin['LBL_TRIP_NO']; ?></th>
                                                    <th>Transaction Date</th>
                                                    <th>Balance Type</th>
                                                    <th>Type</th>
                                                    <th class="align-right">Balance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                if (count($db_result) > 0) {
                                                    $prevbalance = 0;
                                                    for ($i = 0; $i < count($db_result); $i++) {
                                                        if ($db_result[$i]['eType'] == "Credit") {
                                                            $db_result[$i]['currentbal'] = $prevbalance + $db_result[$i]['iBalance'];
                                                        } else {
                                                            $db_result[$i]['currentbal'] = $prevbalance - $db_result[$i]['iBalance'];
                                                        }

                                                        $prevbalance = $db_result[$i]['currentbal'];
                                                        if ($db_result[$i]['iTripId'] > 0) {
                                                            $sql_query = "SELECT * FROM `trips` WHERE iTripId =" . $db_result[$i]['iTripId'];
                                                            $db_result_trips = $obj->MySQLSelect($sql_query);
                                                            $ride_number = $db_result_trips[0]['vRideNo'];
                                                        } else {
                                                            $ride_number = '--';
                                                        }
                                                        ?>
                                                        <tr class="gradeA">
                                                            <td>
                                                               <?php   $pat = '/\#([^\"]*?)\#/';
                                                               preg_match($pat, $db_result[$i]['tDescription'], $tDescription_value);
                                                               $tDescription_translate = $langage_lbl[$tDescription_value[1]];
                                                               $row_tDescription = str_replace($tDescription_value[0], $tDescription_translate, $db_result[$i]['tDescription']);
                                                                echo $row_tDescription; ?>
                                                            <!-- <?= str_replace("withdrawl","withdrawal",$db_result[$i]['tDescription']); ?> --></td>
                                                            <!-- <td>$ <?= $db_result[$i]['iBalance']; ?></td>-->
                                                            <td align="right"><?= $generalobj->trip_currency($db_result[$i]['iBalance']); ?></td>
                                                            <td><?php  echo $ride_number; ?></td>
                                                            <td><?= $generalobjAdmin->DateTime($db_result[$i]['dDate']); ?></td>
                                                            <td><?php  echo str_replace("Withdrawl","Withdrawal",$db_result[$i]['eFor']); ?></td>
                                                            <td><?php  echo $db_result[$i]['eType']; ?></td>
                                                            <!-- <td class="center">$ <?= $db_result[$i]['currentbal']; ?></td>-->
                                                            
                                                            <td align="right"><?= $generalobj->trip_currency($db_result[$i]['currentbal']); ?></td>
                                                        </tr>
                                                        <?php  } ?>
                                                    <tr class="gradeA">
                                                        <td colspan="6" align="right"><b>Total Balance</b></td>
                                                        <!--<td rowspan="1" colspan="1" align="center" class="center">$ <?= $user_available_balance; ?> </td> -->
                                                        <td rowspan="1" colspan="1" align="right"><?= $generalobj->trip_currency($user_available_balance); ?></td>
                                                    </tr>
                                                        <?php  } else { ?>
                                                    <tr class="gradeA">
                                                        <td colspan="12" style="text-align:center;"> No Details Found.</td>
                                                    </tr>
                                                <?php  } ?>
                                            </tbody>
                                        </table>
                                    </form>
                                    <?php // include('pagination_n.php'); ?>
                                </div>
                                <div class="singlerow-login-log wallet-report" <?php  if($action != "search"){ ?>style="display:none;"<?php  }else{?> <?php  } ?>>
                                    <span> <a href="javascript:void(0);" onClick="open_paymentmember();" class="add-btn">Payment To member</a> <a style="text-align: center;margin-left:10px;" class="btn btn-danger" data-toggle="modal" onclick="open_addmonery_popup();">ADD MONEY</a></span> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<!--- start popup-->
<div class="col-lg-12">
    <div class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-content image-upload-1 popup-box1">
            <div class="upload-content" style="width:260px;">
                <div class="addusername"><b style="font-size:20px;"><?php echo $USERNAME; ?></b></div>
                <h4>
                <?= $langage_lbl['LBL_WITHDRAW_REQUEST']; ?>
                </h4>
                <form class="form-horizontal" id="payment_member" method="POST" enctype="multipart/form-data" action="" name="payment_member">
                    <input type="hidden" id="action" name="action" value="paymentmember">
                    <input type="hidden"  name="eTransRequest" id="eTransRequest" value="">
                    <input type="hidden"  name="eType" id="eType" value="Debit">
                    <input type="hidden"  name="eFor" id="eFor" value="Withdrawl">
                    <input type="hidden"  name="iDriverId" id="iDriverId" value="<?= $iDriverId; ?>">
                    <input type="hidden"  name="iUserId" id="iUserId" value="<?= $iUserId; ?>">
                    <input type="hidden"  name="eUserType" id="eUserType" value="<?= $eUserType; ?>">
                    <input type="hidden"  name="User_Available_Balance" id="User_Available_Balance" value="<?= $user_available_balance; ?>">
                    <div class="col-lg-13">
                        <div class="input-group input-append" >
                            <h5>
                            <?= $langage_lbl['LBL_ENTER_AMOUNT']; ?>
                            </h5>
                            <input type="text" name="iBalance" id="iBalance" class="form-control iBalance" onKeyup="checkzeroAdd(this.value);">
                             <br/>
                            <div id="iLimitmsgadd"></div>
                            <!-- <span class="input-group-addon add-on"><i class="icon-calendar"></i></span> -->
                        </div>
                    </div>
                    <input type="button" onClick="check_payment_member();"  id="pay_member" class="save" name="<?= $langage_lbl['LBL_save']; ?>" value="<?= $langage_lbl['LBL_Save']; ?>">
                    <input type="button" class="cancel" data-dismiss="modal" name="<?= $langage_lbl['LBL_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>" value="<?= $langage_lbl['LBL_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>">
                </form>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>
<!--- end popup -->
<!--- start popup-->
<div class="col-lg-12">
    <div class="modal fade" id="Addmoney" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-content image-upload-1 popup-box1">
            <div class="upload-content" style="width:260px;">
                <div class="addusername"><b style=" font-size:20px;"><?php echo $USERNAME; ?></b></div>
                <h4>
                <?= $langage_lbl['LBL_ADD_MONEY']; ?>
                </h4>
                <form class="form-horizontal" id="add_money_frm" method="POST" enctype="multipart/form-data" action="" name="add_money_frm">
                    <input type="hidden" id="action" name="action" value="addmoney">
                    <input type="hidden"  name="eTransRequest" id="eTransRequest" value="">
                    <input type="hidden"  name="eType" id="eType" value="Credit">
                    <input type="hidden"  name="eFor" id="eFor" value="Deposit">
                    <input type="hidden"  name="iDriverId" id="iDriverId" value="<?= $iDriverId; ?>">
                    <input type="hidden"  name="iUserId" id="iUserId" value="<?= $iUserId; ?>">
                    <input type="hidden"  name="eUserType" id="eUserType" value="<?= $eUserType; ?>">
                    <input type="hidden"  name="User_Available_Balance" id="User_Available_Balance" value="<?= $user_available_balance; ?>">
                    <div class="col-lg-13">
                        <div class="input-group input-append" >
                            <h5>
                            <?= $langage_lbl['LBL_ENTER_AMOUNT']; ?>
                            </h5>
                            <div><input type="text" name="iBalance" id="iBalance" class="form-control iBalance add-ibalance" onKeyup="checkzero(this.value);"></div>
                            <br/>
                            <div id="iLimitmsg"></div>
                            <!-- <span class="input-group-addon add-on"><i class="icon-calendar"></i></span> -->
                        </div>
                    </div>
                    <div>
                        <input type="button" onClick="check_add_money();" class="save"  id="add_money" name="<?= $langage_lbl['LBL_save']; ?>" value="<?= $langage_lbl['LBL_Save']; ?>">
                        <input type="button" class="cancel" data-dismiss="modal" name="<?= $langage_lbl['LBL_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>" value="<?= $langage_lbl['LBL_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>">
                    </div>
                </form>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>
<!--- end popup -->
<?php  include_once('footer.php');?>

<form name="pageForm" id="pageForm" action="action/payment_report.php" method="post" >
    <input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php  echo $sortby; ?>" >
    <input type="hidden" name="order" id="order" value="<?php  echo $order; ?>" >
    <input type="hidden" name="action" value="<?php  echo $action; ?>" >
    <input type="hidden" name="eUserType" value="<?php  if(isset($eUserType)) echo $eUserType; ?>" >
    <input type="hidden" name="iDriverId" value="<?php  if(isset($iDriverId)) echo $iDriverId; ?>" >
    <input type="hidden" name="iUserId" value="<?php  if(isset($iUserId)) echo $iUserId; ?>" >
    <input type="hidden" name="searchBalanceType" value="<?php  if(isset($eFor)) echo $eFor; ?>" >
    <input type="hidden" name="searchPaymentType" value="<?php  if(isset($Payment_type)) echo $Payment_type; ?>" >
    <input type="hidden" name="searchDriverPayment" value="<?php  if(isset($searchDriverPayment)) echo $searchDriverPayment; ?>" >
    <input type="hidden" name="startDate" value="<?php  if(isset($startDate)) echo $startDate; ?>" >
    <input type="hidden" name="endDate" value="<?php  if(isset($endDate)) echo $endDate; ?>" >
    <input type="hidden" name="vStatus" value="<?php  if(isset($vStatus)) echo $vStatus; ?>" >
    <input type="hidden" name="method" id="method" value="" >
</form>

<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css" />
<link rel="stylesheet" href="css/select2/select2.min.css" />
<script src="js/plugins/select2.min.js"></script>
<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script>

              $('#dp4').datepicker().on('changeDate', function (ev) {
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
                $('#dp5').datepicker().on('changeDate', function (ev) {
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
                    //$('#iDriverId').hide();
/*                    var eusertype = $("#eUserType").val();
                    if (eusertype == "") {
                      $('.singlerow-login-log').hide();
                    } else {
                      $('.singlerow-login-log').show();
                    }*/
                    if ('<?= $startDate ?>' != '') {
                      $("#dp4").val('<?= $startDate ?>');
                      $("#dp4").datepicker('update', '<?= $startDate ?>');
                    }
                    if ('<?= $endDate ?>' != '') {
                      $("#dp5").datepicker('update', '<?= $endDate; ?>');
                      $("#dp5").val('<?= $endDate; ?>');
                    }
              });
              function todayDate()
              {
                  $("#dp4").val('<?= $Today; ?>');
                  $("#dp5").val('<?= $Today; ?>');
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
              
              function redirectpaymentpage(url)
              {
                  //$("#frmsearch").reset();
                  document.getElementById("action").value = '';
                  document.getElementById("frmsearch").reset();
                  window.location = url;
              }

              function getCheckCount(frmpayment)
              {
                  var x = 0;
                  var threasold_value = 0;
                  for (i = 0; i < frmpayment.elements.length; i++)
                  {
                      if (frmpayment.elements[i].checked == true)
                      {
                          x++;
                      }
                  }
                  return x;
              }


              function Paytodriver() {
                  y = getCheckCount(document.frmpayment);

                  if (y > 0)
                  {
                      ans = confirm("Are you sure you want to Pay To Driver?");
                      if (ans == false)
                      {
                          return false;
                      }
                      $("#ePayDriver").val('Yes');
                      document.frmbooking.submit();
                  } else {
                      alert("Select Trip for Pay To Driver");
                      return false;
                  }
              }

              function exportlist() {
                  document.search.action = "export_driver_details.php";
                  document.search.submit();
              }

              function validate_username(name) {


                  var request = $.ajax({
                      type: "POST",
                      url: 'ajax_user_wallet.php',
                      data: {name: name},
                      success: function (data)
                      {
                          $('#iDriverId').show();

                          $('#iDriverId').html(data);

                      }
                  });

              }
              
                 $("#Search").on('click', function () {
                    var eusertype = $("#eUserType").val();
                    var username_driver = $("#searchDriver").val();
                    var username_rider = $("#searchRider").val();

                    if (eusertype == "") {
                        alert("Please Select Usertype ");
                        return false;
                    }
                    if (eusertype == "Driver" && username_driver == "") {
                        alert("Please Select Driver name");
                        return false;
                    }
                    if (eusertype == "Rider" && username_rider == "") {
                        alert("Please Select Rider name");
                        return false;
                    }
                    if ($("#dp5").val() < $("#dp4").val()) {
                        alert("From date should be lesser than To date.")
                        return false;
                    } else {
                        var action = $("#_list_form").attr('action');
                        var formValus = $("#frmsearch").serialize();
                        window.location.href = action + "?" + formValus;
                    }
                });

              function open_paymentmember() {
                  $('#uiModal').modal('show');
              }
              function open_addmonery_popup() {
                  $('#Addmoney').modal('show');
              }

              function check_payment_member() {
                  var maxamount = document.getElementById("User_Available_Balance").value;
                  var requestamount = document.getElementById("iBalance").value;
                  if (requestamount == '') {
                      alert("Please Enter Withdraw Amount");
                      return false;
                  } else if (requestamount == 0) {
                        alert("You Can Not Enter Zero Number");
                        return false;
                  }else if (parseFloat(requestamount) > parseFloat(maxamount)) {
                      alert("Please Enter Withdraw Amount Less Than " + maxamount);
                      return false;
                  } else {
                    $("#pay_member").val('Please wait ...').attr('disabled','disabled');
                    $('#payment_member').submit();
                  }
                    //document.payment_member.submit();

              }

              function check_add_money() {
                var iBalance = $( ".add-ibalance" ).val();
                if (iBalance == '') {
                    alert("Please Enter Amount");
                    return false;
                } else if (iBalance == 0) {
                    alert("You Can Not Enter Zero Number");
                    return false;
                } else {
                    $("#add_money").val('Please wait ...').attr('disabled','disabled');
                    $('#add_money_frm').submit();
                }
                //document.add_money_frm.submit();
              }

        $(".iBalance").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode == 67 && e.ctrlKey === true) ||
                (e.keyCode == 88 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
        
        function show_hide_user_type(username) {
            if (username == "Driver") {
                $('#sec_driver').show();
                $('#sec_rider').hide();
            } else if (username == "Rider") {
                $('#sec_rider').show();
                $('#sec_driver').hide();
            } else {
                $('#sec_driver').hide();
                $('#sec_rider').hide();
            }
        }
        
        $(function () {
            $("select.filter-by-text").each(function () {
                $(this).select2({
                    placeholder: $(this).attr('data-text'),
                    allowClear: true
                }); //theme: 'classic'
            });
        });

 function checkzero(userlimit)
{
    if(userlimit != ""){
        if (userlimit == 0)
        {       
            $('#iLimitmsg').html('<span class="red">You Can Not Enter Zero Number</span>');
        } else if(userlimit <= 0) {
          $('#iLimitmsg').html('<span class="red">You Can Not Enter Negative Number</span>');
      } else {
         $('#iLimitmsg').html('');
        } 
    } else{
         $('#iLimitmsg').html('');
    } 
                    
}
 function checkzeroAdd(userlimit)
{
    if(userlimit != ""){
        if (userlimit == 0)
        {       
            $('#iLimitmsgadd').html('<span class="red">You Can Not Enter Zero Number</span>');
        } else if(userlimit <= 0) {
          $('#iLimitmsgadd').html('<span class="red">You Can Not Enter Negative Number</span>');
      } else {
         $('#iLimitmsgadd').html('');
        } 
    } else{
         $('#iLimitmsgadd').html('');
    } 
                    
}
</script>
<?php  if ($action != '') { ?>
<script>
    usertype = document.getElementById('eUserType').value;
    if (usertype == "Driver") {
        $('#sec_driver').show();
    } else {
        $('#sec_rider').hide();
    }
    show_hide_user_type(usertype);
</script>
<?php  } else { ?>
    <script>
        $('#sec_rider').hide();
        $('#sec_driver').hide();
    </script>
<?php  } ?>
</body>
<!-- END BODY-->
</html>