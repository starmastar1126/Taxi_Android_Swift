<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$default_lang 	= $generalobj->get_default_lang();
$script = 'Trips';

$rdr_ssql = "";
if (SITE_TYPE == 'Demo') {
    $rdr_ssql = " And tRegistrationDate > '" . WEEK_DATE . "'";
}

//data for select fields
$sql = "select iCompanyId,vCompany from company WHERE eStatus != 'Deleted' $rdr_ssql";
$db_company = $obj->MySQLSelect($sql);

$sql = "select iDriverId,CONCAT(vName,' ',vLastName) AS driverName from register_driver WHERE eStatus != 'Deleted' $rdr_ssql";
$db_drivers = $obj->MySQLSelect($sql);

$sql = "select iUserId,CONCAT(vName,' ',vLastName) AS riderName from register_user WHERE eStatus != 'Deleted' $rdr_ssql";
$db_rider = $obj->MySQLSelect($sql);
//data for select fields

//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$promocode = isset($_REQUEST['promocode']) ? $_REQUEST['promocode'] : '';
$ord = ' ORDER BY t.iTripId DESC';
if($sortby == 1){
  if($order == 0)
  $ord = " ORDER BY t.eType ASC";
  else
  $ord = " ORDER BY t.eType DESC";
}

if($sortby == 2){
  if($order == 0)
  $ord = " ORDER BY t.tTripRequestDate ASC";
  else
  $ord = " ORDER BY t.tTripRequestDate DESC";
}

if($sortby == 3){
  if($order == 0)
  $ord = " ORDER BY c.vCompany ASC";
  else
  $ord = " ORDER BY c.vCompany DESC";
}

if($sortby == 4){
  if($order == 0)
  $ord = " ORDER BY d.vName ASC";
  else
  $ord = " ORDER BY d.vName DESC";
}

if($sortby == 5){
  if($order == 0)
  $ord = " ORDER BY u.vName ASC";
  else
  $ord = " ORDER BY u.vName DESC";
}
//End Sorting


// Start Search Parameters
$ssql='';
$action = isset($_REQUEST['action']) ? $_REQUEST['action']: '';
$searchCompany = isset($_REQUEST['searchCompany']) ? $_REQUEST['searchCompany'] : '';
$searchDriver = isset($_REQUEST['searchDriver']) ? $_REQUEST['searchDriver'] : '';
$searchRider = isset($_REQUEST['searchRider']) ? $_REQUEST['searchRider'] : '';
$serachTripNo = isset($_REQUEST['serachTripNo']) ? $_REQUEST['serachTripNo'] : '';
$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : '';
$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : '';
$vStatus = isset($_REQUEST['vStatus']) ? $_REQUEST['vStatus'] : '';
$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : '';
if($startDate!=''){
	$ssql.=" AND Date(t.tTripRequestDate) >='".$startDate."'";
}
if($endDate!=''){
	$ssql.=" AND Date(t.tTripRequestDate) <='".$endDate."'";
}
if($serachTripNo!=''){
	$ssql.=" AND t.vRideNo ='".$serachTripNo."'";
}
if($searchCompany!=''){
	$ssql.=" AND d.iCompanyId ='".$searchCompany."'";
}
if($searchDriver!=''){
	$ssql.=" AND t.iDriverId ='".$searchDriver."'";
}
if($searchRider!=''){
	$ssql.=" AND t.iUserId ='".$searchRider."'";
}
if($vStatus == "onRide") {
	$ssql .= " AND (t.iActive = 'On Going Trip' OR t.iActive = 'Active') AND t.eCancelled='No'";
}else if($vStatus == "cancel") {
	$ssql .= " AND (t.iActive = 'Canceled' OR t.eCancelled='yes')";
}else if($vStatus == "complete") {
	$ssql .= " AND t.iActive = 'Finished' AND t.eCancelled='No'";
}

if($eType!=''){
	$ssql.=" AND t.eType ='".$eType."'";
}

if(!empty($promocode) && isset($promocode)) {
	$ssql .= " AND t.vCouponCode LIKE '".$promocode."' AND t.iActive !='Canceled'";
}
$trp_ssql = "";
if(SITE_TYPE =='Demo'){
	$trp_ssql = " And t.tTripRequestDate > '".WEEK_DATE."'";
}

//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(t.iTripId) AS Total FROM trips t
	LEFT JOIN register_driver d ON d.iDriverId = t.iDriverId
	LEFT JOIN vehicle_type vt ON vt.iVehicleTypeId = t.iVehicleTypeId
	LEFT JOIN  register_user u ON t.iUserId = u.iUserId LEFT JOIN company c ON c.iCompanyId=d.iCompanyId
	WHERE 1=1 $ssql $trp_ssql";
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
$tpages=$total_pages;
if ($page <= 0)
    $page = 1;
//Pagination End

$sql = "SELECT t.tStartDate ,t.tEndDate, t.tTripRequestDate,t.vCancelReason,t.vCancelComment,t.eHailTrip,t.iUserId,t.iFare,t.eType,d.iDriverId, t.tSaddress,t.vRideNo,t.tDaddress, t.fWalletDebit,t.eCarType,t.iTripId,t.iActive, t.fCancellationFare,t.eCancelled ,CONCAT(u.vName,' ',u.vLastName) AS riderName, CONCAT(d.vName,' ',d.vLastName) AS driverName, d.vAvgRating, c.vCompany, vt.vVehicleType_".$default_lang." as vVehicleType FROM trips t LEFT JOIN register_driver d ON d.iDriverId = t.iDriverId LEFT JOIN vehicle_type vt ON vt.iVehicleTypeId = t.iVehicleTypeId LEFT JOIN  register_user u ON t.iUserId = u.iUserId LEFT JOIN company c ON c.iCompanyId=d.iCompanyId WHERE 1=1 $ssql $trp_ssql $ord LIMIT $start, $per_page";
$db_trip = $obj->MySQLSelect($sql);

$endRecord = count($db_trip);
$var_filter = "";
foreach ($_REQUEST as $key=>$val) {
    if($key != "tpages" && $key != 'page')
    $var_filter.= "&$key=".stripslashes($val);
}

$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages.$var_filter;
$Today=Date('Y-m-d');
$tdate=date("d")-1;
$mdate=date("d");
$Yesterday = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));

$curryearFDate = date("Y-m-d",mktime(0,0,0,'1','1',date("Y")));
$curryearTDate = date("Y-m-d",mktime(0,0,0,"12","31",date("Y")));
$prevyearFDate = date("Y-m-d",mktime(0,0,0,'1','1',date("Y")-1));
$prevyearTDate = date("Y-m-d",mktime(0,0,0,"12","31",date("Y")-1));

$currmonthFDate = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$tdate,date("Y")));
$currmonthTDate = date("Y-m-d",mktime(0,0,0,date("m")+1,date("d")-$mdate,date("Y")));
$prevmonthFDate = date("Y-m-d",mktime(0,0,0,date("m")-1,date("d")-$tdate,date("Y")));
$prevmonthTDate = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-$mdate,date("Y")));

$monday = date( 'Y-m-d', strtotime( 'sunday this week -1 week' ) );
$sunday = date( 'Y-m-d', strtotime( 'saturday this week' ) );

$Pmonday = date( 'Y-m-d', strtotime('sunday this week -2 week'));
$Psunday = date( 'Y-m-d', strtotime('saturday this week -1 week'));
?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?=$SITE_NAME?> | <?php  echo $langage_lbl_admin['LBL_TRIPS_TXT_ADMIN'];?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php  include_once('global_files.php');?>
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
                                <h2><?php  echo $langage_lbl_admin['LBL_TRIPS_TXT_ADMIN'];?> </h2>
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php  include('valid_msg.php'); ?>
					<form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post" >
						<div class="Posted-date mytrip-page">
							<input type="hidden" name="action" value="search" />
							<h3>Search <?php  echo $langage_lbl_admin['LBL_TRIPS_TXT_ADMIN'];?> ...</h3>
							<span>
							<a style="cursor:pointer" onClick="return todayDate('dp4','dp5');"><?=$langage_lbl_admin['LBL_MYTRIP_Today']; ?></a>
							<a style="cursor:pointer" onClick="return yesterdayDate('dFDate','dTDate');"><?=$langage_lbl_admin['LBL_MYTRIP_Yesterday']; ?></a>
							<a style="cursor:pointer" onClick="return currentweekDate('dFDate','dTDate');"><?=$langage_lbl_admin['LBL_MYTRIP_Current_Week']; ?></a>
							<a style="cursor:pointer" onClick="return previousweekDate('dFDate','dTDate');"><?=$langage_lbl_admin['LBL_MYTRIP_Previous_Week']; ?></a>
							<a style="cursor:pointer" onClick="return currentmonthDate('dFDate','dTDate');"><?=$langage_lbl_admin['LBL_MYTRIP_Current_Month']; ?></a>
							<a style="cursor:pointer" onClick="return previousmonthDate('dFDate','dTDate');"><?=$langage_lbl_admin['LBL_MYTRIP_Previous Month']; ?></a>
							<a style="cursor:pointer" onClick="return currentyearDate('dFDate','dTDate');"><?=$langage_lbl_admin['LBL_MYTRIP_Current_Year']; ?></a>
							<a style="cursor:pointer" onClick="return previousyearDate('dFDate','dTDate');"><?=$langage_lbl_admin['LBL_MYTRIP_Previous_Year']; ?></a>
							</span> 
							<span>
							<input type="text" id="dp4" name="startDate" placeholder="From Date" class="form-control" value="" readonly=""style="cursor:default; background-color: #fff" />
							<input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control" value="" readonly="" style="cursor:default; background-color: #fff"/>
							
							<div class="col-lg-3">
								  <select class="form-control filter-by-001" name = 'vStatus' >
									   <option value="">&nbsp;&nbsp;All</option>
									   <option value="onRide" <?php  if($vStatus == "onRide") { echo "selected"; } ?>>&nbsp;&nbsp;On Going <?php  echo $langage_lbl_admin['LBL_RIDE_TXT_ADMIN'];?> </option>
									   <option value="complete" <?php  if($vStatus == "complete") { echo "selected"; } ?>>&nbsp;&nbsp;Completed</option>
									   <option value="cancel" <?php  if($vStatus == "cancel") { echo "selected"; } ?>>&nbsp;&nbsp;Cancelled</option>
								  </select>
							</div>
							<div class="col-lg-2">
								  <input type="text" id="serachTripNo" name="serachTripNo" placeholder="<?php  echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN'];?> Number" class="form-control search-trip001" value="<?php  echo $serachTripNo; ?>"/>
							</div>
							</span>
						</div>

						<div class="row">
						
						<div class="col-lg-3">
							<select class="form-control filter-by-text" name = 'searchCompany' id="searchCompany" data-text="Select Company">
							   <option value="">Select Company</option>
							   <?php  foreach($db_company as $dbc){ ?>
							   <option value="<?php  echo $dbc['iCompanyId']; ?>" <?php  if($searchCompany == $dbc['iCompanyId']) { echo "selected"; } ?>><?php  echo $generalobjAdmin->clearCmpName($dbc['vCompany']); ?></option>
							   <?php  } ?>
							</select>
						</div>
						<div class="col-lg-3">
							<select class="form-control filter-by-text driver_container" name = 'searchDriver' data-text="Select <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?>">
							   <option value="">Select <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></option>
							   <?php  foreach($db_drivers as $dbd){ ?>
							   <option value="<?php  echo $dbd['iDriverId']; ?>" <?php  if($searchDriver == $dbd['iDriverId']) { echo "selected"; } ?>><?php  echo $generalobjAdmin->clearName($dbd['driverName']); ?></option>
							   <?php  } ?>
							</select>
						</div>
						<div class="col-lg-3">
							<select class="form-control filter-by-text" name = 'searchRider' data-text="Select <?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?>">
								<option value="">Select <?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?></option>
							   <?php  foreach($db_rider as $dbr){ ?>
							   <option value="<?php  echo $dbr['iUserId']; ?>" <?php  if($searchRider == $dbr['iUserId']) { echo "selected"; } ?>><?php  echo $generalobjAdmin->clearName($dbr['riderName']); ?></option>
							   <?php  } ?>
							</select>
						</div>
						<?php  if($APP_TYPE == 'Ride-Delivery-UberX' || $APP_TYPE == 'Ride-Delivery'){ ?>
						<div class="col-lg-2">
							  <select class="form-control" name = 'eType' >
								   <option value="">&nbsp;&nbsp;Service Type</option>
								   <option value="Ride" <?php  if($eType == "Ride") { echo "selected"; } ?>>&nbsp;&nbsp;<?php  echo $langage_lbl_admin['LBL_RIDE_TXT_ADMIN_SEARCH'];?> </option>
								   <option value="Deliver" <?php  if($eType == "Deliver") { echo "selected"; } ?>>&nbsp;&nbsp;Delivery</option>
								   <option value="UberX" <?php  if($eType == "UberX") { echo "selected"; } ?>>&nbsp;&nbsp;Other Services</option>
							  </select>
						</div>
						<?php  } ?>
						</div>
					<div class="tripBtns001"><b>
					<input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
					<input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='trip.php'"/></b>
					</div>
					</form>
					<div class="table-list">
						<div class="row">
							<div class="col-lg-12">
									<div class="table-responsive">
										
										<form class="_list_form" id="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
										<table class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
												<?php  if($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery'){ ?>
													<th width="10%" class="align-left"><a href="javascript:void(0);" onClick="Redirect(1,<?php  if($sortby == '1'){ echo $order; }else { ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN'];?> <?php  if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
													<?php  } ?> 
													<th class="align-center"></th>
													<th>Address</th>
													<th width="8%" class="align-left"><a href="javascript:void(0);" onClick="Redirect(2,<?php  if($sortby == '2'){ echo $order; }else { ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_TRIP_DATE_ADMIN'];?> <?php  if ($sortby == 2) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
													
													<th width="12%"><a href="javascript:void(0);" onClick="Redirect(3,<?php  if($sortby == '3'){ echo $order; }else { ?>0<?php  } ?>)">Company <?php  if ($sortby == 3) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
													<th width="12%"><a href="javascript:void(0);" onClick="Redirect(4,<?php  if($sortby == '4'){ echo $order; }else { ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> <?php  if ($sortby == 4) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
													<th width="12%"><a href="javascript:void(0);" onClick="Redirect(5,<?php  if($sortby == '5'){ echo $order; }else { ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?> <?php  if ($sortby == 5) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
													<th width="8%" class="align-right"><?php  echo $langage_lbl_admin['LBL_DRIVER_TRIP_FARE_TXT'];?></th>
													<th class="align-center"><?php  echo $langage_lbl_admin['LBL_TEXI_ADMIN'];?> </th>
													<th class="align-center">View Invoice</th>
												</tr>
											</thead>
											<tbody>
												<?php  if(!empty($db_trip)) {
												for($i=0;$i<count($db_trip);$i++)
												{
														$eTypenew = $db_trip[$i]['eType'];
														if($eTypenew == 'Ride'){
															$trip_type = 'Ride';
														} else if($eTypenew == 'UberX') {
															$trip_type = 'Other Services';
														} else {
															$trip_type = 'Delivery';
														}
														?>
														<tr class="gradeA">
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
															<td align="center">
																<?=$db_trip[$i]['vRideNo'];?>
															</td>
															<td width="30%"><?php  echo $db_trip[$i]['tSaddress'];
															if($APP_TYPE != "UberX" && !empty($db_trip[$i]['tDaddress'])){echo ' -> '.$db_trip[$i]['tDaddress']; } ?>
															</td>
															<!--<td><?= date('d F, Y',strtotime($db_trip[$i]['tStartDate']));?></td>-->
															<!-- <td align="center"><?php if($db_trip[$i]['tStartDate']=="0000-00-00 00:00:00"){echo date('d-F-Y',strtotime($db_trip[$i]['tTripRequestDate']));}else{echo date('d-F-Y',strtotime($db_trip[$i]['tStartDate']));}?></td>-->
															<td align="center"><?php  echo date('d-F-Y',strtotime($db_trip[$i]['tTripRequestDate'])); ?></td>
															<td> 
																<?=$generalobjAdmin->clearCmpName($db_trip[$i]['vCompany']);?>
															</td>
															<td>
																<a href="driver_action.php?id=<?=$db_trip[$i]['iDriverId']?>" target="_blank"><?=$generalobjAdmin->clearName($db_trip[$i]['driverName']);?></a>
															</td>

															<td>
																<?php  if($db_trip[$i]['eHailTrip'] != "Yes"){?>
																	<a href="rider_action.php?id=<?=$db_trip[$i]['iUserId']?>" target="_blank"><?=$generalobjAdmin->clearName($db_trip[$i]['riderName']);?></a>
																<?php  } else{
																	echo " ---- ";
																} ?>
															</td>
															<td align="right">
															<?php  if($db_trip[$i]['fCancellationFare'] > 0) {
																echo $generalobj->trip_currency($db_trip[$i]['fCancellationFare']);
															} else { 
																echo $generalobj->trip_currency($db_trip[$i]['iFare']);
															} ?>
															</td>
															<td align="center">
																<?=$db_trip[$i]['vVehicleType'];?>
															</td>
															<td align="center" width="10%">
															<?php if(($db_trip[$i]['iActive'] == 'Finished' && $db_trip[$i]['eCancelled'] == "Yes") || ($db_trip[$i]['fCancellationFare'] > 0)) {?>
															  	<button class="btn btn-primary" onclick='javascript:window.open("invoice.php?iTripId=<?=$db_trip[$i]['iTripId']?>","_blank")';">
																	<i class="icon-th-list icon-white"><b>View Invoice</b></i>
																</button>
																<div style="font-size: 12px;">Cancelled</div>
															<?php  } else if($db_trip[$i]['iActive'] == 'Finished') { ?>
																 <!-- <a href="javascript:void(0);" onclick='javascript:window.open("invoice.php?iTripId=<?=$db_trip[$i]['iTripId']?>","_blank")';"> -->
																<button class="btn btn-primary" onclick='return !window.open("invoice.php?iTripId=<?=$db_trip[$i]['iTripId']?>","_blank")';">
																	<i class="icon-th-list icon-white"><b>View Invoice</b></i>
																</button>
															 <!-- </a> -->
															<?php  }else {
																if($db_trip[$i]['iActive']== "Active" OR $db_trip[$i]['iActive']== "On Going Trip")
																{
																	if($APP_TYPE == 'UberX' || $APP_TYPE == 'Ride-Delivery-UberX') {
																		echo "On Job";
																	} else {
																		echo "On Ride";
																	}
																} else if($db_trip[$i]['iActive']== "Canceled"  && $db_trip[$i]['vCancelReason'] != '') { ?>
																	<a href="javascript:void(0);" class="btn btn-info" data-toggle="modal" data-target="#uiModal1_<?=$db_trip[$i]['iTripId'];?>">Cancel Reason</a>
																	
																<?php  } else if($db_trip[$i]['iActive']== "Canceled") {

																	echo "Cancelled"; ?>
																	 <!--<a href="javascript:void(0);" onclick='javascript:window.open("cancel_trips_invoice.php?iTripId=<?=$db_trip[$i]['iTripId']?>","_blank")';">
																		<button class="btn btn-primary">
																	<i class="icon-th-list  icon-white"><b>Cancel Invoice</b></i>
																</button>
															 </a>-->
																	
															<?php  	} else {
																	echo $db_trip[$i]['iActive'];
																}
															} ?>
															</td>
														</tr>
														  <div class="clear"></div>
														 <div class="modal fade" id="uiModal1_<?=$db_trip[$i]['iTripId'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
															  <div class="modal-content image-upload-1" style="width:400px;">
																   <div class="upload-content" style="width:350px;">
																		<h3><?=$langage_lbl_admin['LBL_RIDE_TXT_ADMIN'];?> Cancel Reason</h3>	
																		<h4>Cancel Reason: <?=stripcslashes($db_trip[$i]['vCancelReason']." ".$db_trip[$i]['vCancelComment']);?></h4>
																		<input type="button" class="save" data-dismiss="modal" name="cancel" value="Close">
																   </div>
															  </div>
														 </div>
												<?php  } }else { ?>
													<tr class="gradeA">
														<td colspan="10"> No Records Found.</td>
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

<form name="pageForm" id="pageForm" action="" method="post" >
<input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
<input type="hidden" name="sortby" id="sortby" value="<?php  echo $sortby; ?>" >
<input type="hidden" name="order" id="order" value="<?php  echo $order; ?>" >
<input type="hidden" name="action" value="<?php  echo $action; ?>" >
<input type="hidden" name="searchCompany" value="<?php  echo $searchCompany; ?>" >
<input type="hidden" name="searchDriver" value="<?php  echo $searchDriver; ?>" >
<input type="hidden" name="searchRider" value="<?php  echo $searchRider; ?>" >
<input type="hidden" name="serachTripNo" value="<?php  echo $serachTripNo; ?>" >
<input type="hidden" name="startDate" value="<?php  echo $startDate; ?>" >
<input type="hidden" name="endDate" value="<?php  echo $endDate; ?>" >
<input type="hidden" name="vStatus" value="<?php  echo $vStatus; ?>" >
<input type="hidden" name="eType" value="<?php  echo $eType; ?>" >
<input type="hidden" name="method" id="method" value="" >
</form>
	<?php  include_once('footer.php');?>
	<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css" />
	<link rel="stylesheet" href="css/select2/select2.min.css" />
	<script src="js/plugins/select2.min.js"></script>
	<script src="../assets/js/jquery-ui.min.js"></script>
	<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <script>
			$('#dp4').datepicker()
            .on('changeDate', function (ev) {
                if (ev.date.valueOf() < endDate.valueOf()) {
                    $('#alert').show().find('strong').text('The start date can not be greater then the end date');
                } else {
                    $('#alert').hide();
                    startDate = new Date(ev.date);
                    $('#startDate').text($('#dp4').data('date'));
                }
                $('#dp4').datepicker('hide');
            });
			$('#dp5').datepicker()
            .on('changeDate', function (ev) {
                if (ev.date.valueOf() < startDate.valueOf()) {
                    $('#alert').show().find('strong').text('The end date can not be less then the start date');
                } else {
                    $('#alert').hide();
                    endDate = new Date(ev.date);
                    $('#endDate').text($('#dp5').data('date'));
                }
                $('#dp5').datepicker('hide');
            });
	
         $(document).ready(function () {
			 if('<?=$startDate?>'!=''){
				 $("#dp4").val('<?=$startDate?>');
				 $("#dp4").datepicker('update' , '<?=$startDate?>');
			 }
			 if('<?=$endDate?>'!=''){
				 $("#dp5").datepicker('update' , '<?= $endDate;?>');
				 $("#dp5").val('<?= $endDate;?>');
			 }
			 
         });
		 
		 function setRideStatus(actionStatus) {
			 window.location.href = "trip.php?type="+actionStatus;
		 }
		 function todayDate()
		 {
			 $("#dp4").val('<?= $Today;?>');
			 $("#dp5").val('<?= $Today;?>');
		 }
		 function reset() {
			location.reload();
			
		}	
		 function yesterdayDate()
		 {
			 $("#dp4").val('<?= $Yesterday;?>');
			 $("#dp4").datepicker('update' , '<?= $Yesterday;?>');
			 $("#dp5").datepicker('update' , '<?= $Yesterday;?>');
			 $("#dp4").change();
			 $("#dp5").change();
			 $("#dp5").val('<?= $Yesterday;?>');
		 }
		 function currentweekDate(dt,df)
		 {
			 $("#dp4").val('<?= $monday;?>');
			 $("#dp4").datepicker('update' , '<?= $monday;?>');
			 $("#dp5").datepicker('update' , '<?= $sunday;?>');
			 $("#dp5").val('<?= $sunday;?>');
		 }
		 function previousweekDate(dt,df)
		 {
			 $("#dp4").val('<?= $Pmonday;?>');
			 $("#dp4").datepicker('update' , '<?= $Pmonday;?>');
			 $("#dp5").datepicker('update' , '<?= $Psunday;?>');
			 $("#dp5").val('<?= $Psunday;?>');
		 }
		 function currentmonthDate(dt,df)
		 {
			 $("#dp4").val('<?= $currmonthFDate;?>');
			 $("#dp4").datepicker('update' , '<?= $currmonthFDate;?>');
			 $("#dp5").datepicker('update' , '<?= $currmonthTDate;?>');
			 $("#dp5").val('<?= $currmonthTDate;?>');
		 }
		 function previousmonthDate(dt,df)
		 {
			 $("#dp4").val('<?= $prevmonthFDate;?>');
			 $("#dp4").datepicker('update' , '<?= $prevmonthFDate;?>');
			 $("#dp5").datepicker('update' , '<?= $prevmonthTDate;?>');
			 $("#dp5").val('<?= $prevmonthTDate;?>');
		 }
		 function currentyearDate(dt,df)
		 {
			 $("#dp4").val('<?= $curryearFDate;?>');
			 $("#dp4").datepicker('update' , '<?= $curryearFDate;?>');
			 $("#dp5").datepicker('update' , '<?= $curryearTDate;?>');
			 $("#dp5").val('<?= $curryearTDate;?>');
		 }
		 function previousyearDate(dt,df)
		 {
			 $("#dp4").val('<?= $prevyearFDate;?>');
			 $("#dp4").datepicker('update' , '<?= $prevyearFDate;?>');
			 $("#dp5").datepicker('update' , '<?= $prevyearTDate;?>');
			 $("#dp5").val('<?= $prevyearTDate;?>');
		 }
		$("#Search").on('click', function(){
			 if($("#dp5").val() < $("#dp4").val()){
				 alert("From date should be lesser than To date.")
				 return false;
			 }else {
				var action = $("#_list_form").attr('action');
                var formValus = $("#frmsearch").serialize();
                window.location.href = action+"?"+formValus;
			 }
		});
		$(function () {
		  $("select.filter-by-text").each(function(){
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
