<?php 
include_once('../common.php');


if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$APP_DELIVERY_MODE = $generalobj->getConfigurations("configurations","APP_DELIVERY_MODE");

$script = 'CabBooking';


//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$hdn_del_id = isset($_REQUEST['hdn_del_id']) ? $_REQUEST['hdn_del_id'] : '';

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
//End Sorting

$adm_ssql = "";
if (SITE_TYPE == 'Demo') {
    $adm_ssql = " And cb.dAddredDate > '" . WEEK_DATE . "'";
}

// Start Search Parameters
$option = isset($_REQUEST['option'])?stripslashes($_REQUEST['option']):"";
$keyword = isset($_REQUEST['keyword'])?stripslashes($_REQUEST['keyword']):"";
$searchDate = isset($_REQUEST['searchDate'])?$_REQUEST['searchDate']:"";
$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : "";
$ssql = '';
if($keyword != ''){
    if($option != '') {
        if($eType != ''){
          $ssql.= " AND ".stripslashes($option)." LIKE '%".$generalobjAdmin->clean($keyword)."%' AND cb.eType = '".$generalobjAdmin->clean($eType)."'";
        } else {
          $ssql.= " AND ".stripslashes($option)." LIKE '%".$generalobjAdmin->clean($keyword)."%'";
        }
        /*if (strpos($option, 'eStatus') !== false || strpos($option, 'vBookingNo') !== false) {
          $ssql.= " AND ".stripslashes($option)." LIKE '".$generalobjAdmin->clean($keyword)."'";
        }*/ 
    }else {
      if($eType != ''){
        $ssql.= " AND (CONCAT(ru.vName,' ',ru.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.tDestAddress LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.vSourceAddresss	 LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.vBookingNo LIKE '".$generalobjAdmin->clean($keyword)."' OR cb.eStatus LIKE '%".$generalobjAdmin->clean($keyword)."%') AND cb.eType = '".$generalobjAdmin->clean($eType)."'";
      } else {
        $ssql.= " AND (CONCAT(ru.vName,' ',ru.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.tDestAddress LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.vSourceAddresss	 LIKE '%".$generalobjAdmin->clean($keyword)."%' OR cb.vBookingNo LIKE '".$generalobjAdmin->clean($keyword)."' OR cb.eStatus LIKE '%".$generalobjAdmin->clean($keyword)."%')";
      }
    }
} else if($eType != '' && $keyword == '') {
     $ssql.= " AND cb.eType = '".$generalobjAdmin->clean($eType)."'";
}
// End Search Parameters


//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(cb.iCabBookingId) as Total FROM cab_booking as cb
	 LEFT JOIN register_user as ru on ru.iUserId=cb.iUserId
	 LEFT JOIN register_driver as rd on rd.iDriverId=cb.iDriverId
	 LEFT JOIN vehicle_type as vt on vt.iVehicleTypeId=cb.iVehicleTypeId WHERE 1=1 $ssql $adm_ssql";
	 //$ssql $adm_ssql

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


 $sql = "SELECT cb.*,CONCAT(ru.vName,' ',ru.vLastName) as rider,CONCAT(rd.vName,' ',rd.vLastName) as driver,vt.vVehicleType FROM cab_booking as cb LEFT JOIN register_user as ru on ru.iUserId=cb.iUserId LEFT JOIN register_driver as rd on rd.iDriverId=cb.iDriverId LEFT JOIN vehicle_type as vt on vt.iVehicleTypeId=cb.iVehicleTypeId WHERE 1=1 $ssql $adm_ssql $ord LIMIT $start, $per_page";
	 //$ssql $adm_ssql $ord LIMIT $start, $per_page

$data_drv = $obj->MySQLSelect($sql);

$endRecord = count($data_drv);

$var_filter = "";
foreach ($_REQUEST as $key=>$val) {
    if($key != "tpages" && $key != 'page')
    $var_filter.= "&$key=".stripslashes($val);
}

$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages.$var_filter;
$systemTimeZone = date_default_timezone_get();
function converToTz($time, $toTz, $fromTz,$dateFormat="Y-m-d H:i:s") {
    $date = new DateTime($time, new DateTimeZone($fromTz));
    $date->setTimezone(new DateTimeZone($toTz));
    $time = $date->format($dateFormat);
    return $time;
}

    
if($action == 'delete' && $hdn_del_id != '')
  {

      $iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
      $iUserId = isset($_REQUEST['iUserId']) ? $_REQUEST['iUserId'] : '';
      $cancelreason = isset($_REQUEST['cancel_reason']) ? $_REQUEST['cancel_reason'] : '';
      $query = "UPDATE cab_booking SET eStatus = 'Cancel',eAutoAssign = 'No', eCancelBy= 'Admin',`vCancelReason`='".$cancelreason."' WHERE iCabBookingId = '".$hdn_del_id."'";
      $obj->sql_query($query);
                        
      $sql1="select *  from cab_booking where iCabBookingId=".$hdn_del_id;
      $bookind_detail = $obj->MySQLSelect($sql1);
            
      $dBooking_date = $bookind_detail[0]['dBooking_date'];     
      $vBookingNo = $bookind_detail[0]['vBookingNo'];     
      $vSourceAddresss = $bookind_detail[0]['vSourceAddresss'];     
            
                        
      $sql2 = "select vName,vLastName,vEmail,iDriverVehicleId,vPhone,vcode,vLang from register_driver where iDriverId=".$iDriverId;
      $driver_db = $obj->MySQLSelect($sql2);
      $vPhone = $driver_db[0]['vPhone'];     
      $vcode = $driver_db[0]['vcode'];
      $vLang = $driver_db[0]['vLang'];   
            
      $SQL3 = "SELECT vName,vLastName,vEmail,iUserId,vPhone,vPhoneCode,vLang FROM register_user WHERE iUserId = '$iUserId'";
      $user_detail = $obj->MySQLSelect($SQL3);
      $vPhone1 = $user_detail[0]['vPhone'];   
      $vcode1 = $user_detail[0]['vPhoneCode'];
      $vLang1 = $user_detail[0]['vLang'];
          
      $Data1['vRider']=$user_detail[0]['vName']." ".$user_detail[0]['vLastName'];
      $Data1['vDriver']=$driver_db[0]['vName']." ".$driver_db[0]['vLastName'];  
      $Data1['vRiderMail']=$user_detail[0]['vEmail'];           
      $Data1['vSourceAddresss']=$vSourceAddresss;        
      $Data1['dBookingdate']=$dBooking_date;
      $Data1['vBookingNo']=$vBookingNo;
      
      $Data['vRider']=$user_detail[0]['vName']." ".$user_detail[0]['vLastName'];
      $Data['vDriver']=$driver_db[0]['vName']." ".$driver_db[0]['vLastName'];     
      $Data['vDriverMail']=$driver_db[0]['vEmail'];     
      $Data['vSourceAddresss']=$vSourceAddresss;          
      $Data['dBookingdate']=$dBooking_date;
      $Data['vBookingNo']=$vBookingNo;
            
      $return = $generalobj->send_email_user("MANUAL_CANCEL_TRIP_ADMIN_TO_DRIVER",$Data);
      $return1 = $generalobj->send_email_user("MANUAL_CANCEL_TRIP_ADMIN_TO_RIDER",$Data1);
            
      $Booking_Date = @date('d-m-Y',strtotime($dBooking_date));    
      $Booking_Time = @date('H:i:s',strtotime($dBooking_date));     
            
      $maildata['vDriver'] = $driver_db[0]['vName']." ".$driver_db[0]['vLastName'];  
      $maildata['dBookingdate'] = $Booking_Date;      
      $maildata['dBookingtime'] =  $Booking_Time;      
      $maildata['vBookingNo'] = $vBookingNo;      
                  
      $maildata1['vRider'] = $user_detail[0]['vName']." ".$user_detail[0]['vLastName'];      
      $maildata1['dBookingdate'] = $Booking_Date;      
      $maildata1['dBookingtime'] =  $Booking_Time;      
      $maildata1['vBookingNo'] = $vBookingNo;     
                  
      $message_layout = $generalobj->send_messages_user("DRIVER_SEND_MESSAGE_JOB_CANCEL",$maildata1,"",$vLang);
      $return5 = $generalobj->sendUserSMS($vPhone,$vcode,$message_layout,"");  
                  
      $message_layout = $generalobj->send_messages_user("USER_SEND_MESSAGE_JOB_CANCEL",$maildata,"",$vLang1);
      $return4 = $generalobj->sendUserSMS($vPhone1,$vcode1,$message_layout,"");    
            
    echo "<script>location.href='cab_booking.php'</script>";

    //header("Location:cab_booking.php");
    //exit; 
  } 
  
  $driverSql = "select iDriverId,vName,vLastName,vEmail,vPhone,vCode from register_driver where eStatus='active'";
  $driverData = $obj->MySQLSelect($driverSql);
?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?=$SITE_NAME?> | <?=$langage_lbl_admin['LBL_RIDE_LATER_BOOKINGS_ADMIN'];?></title>
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
                                <h2><?=$langage_lbl_admin['LBL_RIDE_LATER_BOOKINGS_ADMIN'];?></h2>
                                <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php  include('valid_msg.php'); ?>
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                              <tbody>
                                <tr>
                                    <td width="1%"><label for="textfield"><strong>Search:</strong></label></td>
                                    <?php  if($APP_TYPE == 'Ride-Delivery-UberX' || $APP_TYPE == 'Ride-Delivery'){ ?>
                                    <td width="5%" class="padding-right10">
                                          <select class="form-control" name = 'eType' >
                                             <option value="">&nbsp;&nbsp;Service Type</option>
                                             <option value="Ride" <?php  if($eType == "Ride") { echo "selected"; } ?>>&nbsp;&nbsp;<?php  echo $langage_lbl_admin['LBL_RIDE_TXT_ADMIN_SEARCH'];?> </option>
                                             <option value="Deliver" <?php  if($eType == "Deliver") { echo "selected"; } ?>>&nbsp;&nbsp;Delivery</option>
                                             <option value="UberX" <?php  if($eType == "UberX") { echo "selected"; } ?>>&nbsp;&nbsp;Other Services</option>
                                          </select>
                                    </td>
                                    <?php  } ?>
                                    <td width="10%" class=" padding-right10">
                                    <select name="option" id="option" class="form-control">
										<option value="">All</option>
										<option value="" <?php  if ($option == "CONCAT(ru.vName,' ',ru.vLastName)") { echo "selected"; } ?> ><?=$langage_lbl_admin['LBL_RIDERS_ADMIN'];?></option>
										<option value="cb.vSourceAddresss" <?php  if ($option == 'cb.vSourceAddresss') {echo "selected"; } ?> >Expected Source Location </option>
										<?php if($APP_TYPE != "UberX"){?>
										<option value="cb.tDestAddress" <?php  if ($option == 'cb.tDestAddress') {echo "selected"; } ?> >Expected Destination Location</option>
										<?php  } ?>
                    <option value="cb.vBookingNo" <?php  if ($option == 'cb.vBookingNo') {echo "selected"; } ?> >Booking Number </option>
										<option value="cb.eStatus" <?php  if ($option == 'cb.eStatus') {echo "selected"; } ?> >Status</option>
                                    </select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php  echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='cab_booking.php'"/>
                                      <?php   if(!empty($data_drv)) { ?>
                                        <button type="button" onClick="reportExportTypes('cab_booking')" class="export-btn001"  style="float:none;">Export</button></b>
                                      <?php  } ?>
                                    </td>
                                </tr>
                              </tbody>
                        </table>
                        
                      </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                               
                                    <div style="clear:both;"></div>
                                        <div class="table-responsive">
                                            <form class="_list_form" id="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                            <?php  if($APP_TYPE == 'Ride-Delivery-UberX' || $APP_TYPE == 'Ride-Delivery'){?>                        
                            <th width="10%" class="align-left"><a href="javascript:void(0);" onClick="Redirect(7,<?php  if($sortby == '7'){ echo $order; }else { ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_TRIP_TYPE_TXT_ADMIN'];?> <?php  if ($sortby == 7) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                            <?php  } ?>                       
                            <th width="12%"><a href="javascript:void(0);" onClick="Redirect(6,<?php  if($sortby == '6'){ echo $order; }else { ?>0<?php  } ?>)">Booking No.<?php  if ($sortby == 6) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
														<th width=""><a href="javascript:void(0);" onClick="Redirect(1,<?php  if($sortby == '1'){ echo $order; }else { ?>0<?php  } ?>)"><?=$langage_lbl_admin['LBL_RIDERS_ADMIN'];?><?php  if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                        
														<th width=""><a href="javascript:void(0);" onClick="Redirect(2,<?php  if($sortby == '2'){ echo $order; }else { ?>0<?php  } ?>)">	Date <?php  if ($sortby == 2) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                        
														<th width=""><a href="javascript:void(0);" onClick="Redirect(3,<?php  if($sortby == '3'){ echo $order; }else { ?>0<?php  } ?>)">Expected Source Location <?php  if ($sortby == 3) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
														<?php if($APP_TYPE != "UberX"){?>
														<th width=""><a href="javascript:void(0);" onClick="Redirect(4,<?php  if($sortby == '4'){ echo $order; }else { ?>0<?php  } ?>)">Expected Destination Location <?php  if ($sortby == 4) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                            <?php  } ?>                          
														<th width="" align="left" style="text-align:left;"><?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></th>
														
														<th><?=$langage_lbl_admin['LBL_TRIP_DETAILS'];?></th>
                                                        
														<th width="" align="left" style="text-align:left;"><a href="javascript:void(0);" onClick="Redirect(5,<?php  if($sortby == '5'){ echo $order; }else { ?>0<?php  } ?>)">Status <?php  if ($sortby == 5) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                          <?php  
                          if(!empty($data_drv)) {
                          for ($i = 0; $i < count($data_drv); $i++) { 
													$setcurrentTime = strtotime(date('Y-m-d H:i:s'));
													$bookingdate = 	date("H:i", strtotime('+30 minutes',  strtotime($data_drv[$i]['dBooking_date'])));
													$bookingdatecmp =strtotime($bookingdate);
                                                        
                          $default = '';
                          if(isset($data_drv[$i]['eDefault']) && $data_drv[$i]['eDefault']=='Yes'){
                                  $default = 'disabled';
                          }

                          $eType_new = $data_drv[$i]['eType'];
                        if($eType_new == 'Ride'){
                          $trip_type = 'Ride';
                        } else if($eType_new == 'UberX') {
                          $trip_type = 'Other Services';
                        }  else if($eType_new == 'Deliver') {
                          $trip_type = 'Delivery';
                        }
                           ?>
                        <tr class="gradeA">
                           <?php  if($APP_TYPE == 'Ride-Delivery-UberX' || $APP_TYPE == 'Ride-Delivery'){ ?> 
                            <td align="left">
                            <?php   echo $trip_type; ?>
                            </td>
                            <?php  } ?> 
                            <td width="12%"><?=$generalobjAdmin->clearName($data_drv[$i]['vBookingNo']); ?></td>
													  <td width="10%"><?=$generalobjAdmin->clearName($data_drv[$i]['rider']); ?></td>
													  <td width="10%" data-order="<?=$data_drv[$i]['iCabBookingId']; ?>"><?php 
                              if($data_drv[$i]['dBooking_date']!= "" && $data_drv[$i]['vTimeZone'] != "")  {
                                 $dBookingDate = converToTz($data_drv[$i]['dBooking_date'],$data_drv[$i]['vTimeZone'],$systemTimeZone);
                              } else {
                                 $dBookingDate = $data_drv[$i]['dBooking_date'];
                              }
                              echo $generalobjAdmin->DateTime($dBookingDate); 
                              ?></td>
													  <td><?= $data_drv[$i]['vSourceAddresss']; ?></td>
													  <?php if($APP_TYPE != "UberX"){?>
														<td><?= $data_drv[$i]['tDestAddress']; ?></td>
													  <?php  } ?>
													  <?php  if ($data_drv[$i]['eAutoAssign'] == "Yes" && $data_drv[$i]['eType'] == "Deliver" && $data_drv[$i]['iDriverId'] == 0 && $data_drv[$i]['eStatus'] != 'Cancel' && $APP_DELIVERY_MODE == "Multi") { ?>

															<td width="10%"><?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> : Auto Assign </b><br />( Vehicle Type : <?= $data_drv[$i]['vVehicleType']; ?>)<br/><?php  if(strtotime($data_drv[$i]['dBooking_date'])>strtotime(date('Y-m-d'))){ ?><a class="btn btn-info" href="javascript:void(0);" onclick="assignDriver('<?= $data_drv[$i]['iCabBookingId']; ?>');" data-tooltip="tooltip" title="Edit"><i class="icon-edit icon-flip-horizontal icon-white"></i> <?=$langage_lbl_admin['LBL_ASSIGN_DRIVER_BUTTON'];?></a><?php  } ?></td>

														<?php  } else if ($data_drv[$i]['eAutoAssign'] == "Yes" && $data_drv[$i]['iDriverId'] == 0 && $data_drv[$i]['eStatus'] != 'Cancel') { ?>

															<td width="10%"><?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> : Auto Assign </b><br />( Car Type : <?= $data_drv[$i]['vVehicleType']; ?>)<br/><?php  if(strtotime($data_drv[$i]['dBooking_date'])>strtotime(date('Y-m-d'))){ ?><a class="btn btn-info" href="add_booking.php?booking_id=<?= $data_drv[$i]['iCabBookingId']; ?>" data-tooltip="tooltip" title="Edit"><i class="icon-edit icon-flip-horizontal icon-white"></i></a><?php  } ?></td>

														<?php  } else if ($data_drv[$i]['eStatus'] == "Pending" && (strtotime($data_drv[$i]['dBooking_date'])>strtotime(date('Y-m-d'))) && $data_drv[$i]['iDriverId'] == 0) { ?>

															<td width="10%"><a class="btn btn-info" href="add_booking.php?booking_id=<?= $data_drv[$i]['iCabBookingId']; ?>"><i class="icon-shield icon-flip-horizontal icon-white"></i> Assign <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></a><br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>)</td>

														<?php  } else if($data_drv[$i]['eCancelBy'] == "Driver" && $data_drv[$i]['eStatus'] == "Cancel" && $data_drv[$i]['iDriverId'] == 0) { ?>

															<td width="10%"><a class="btn btn-info" href="add_booking.php?booking_id=<?= $data_drv[$i]['iCabBookingId']; ?>"><i class="icon-shield icon-flip-horizontal icon-white"></i> Assign <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></a><br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>)</td>

														<?php  } else if ($data_drv[$i]['driver'] != "" && $data_drv[$i]['driver'] != "0") { ?>

															<td width="10%"><b><?= $generalobjAdmin->clearName($data_drv[$i]['driver']); ?></b><br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>) </td>

														<?php  } else  { ?>

															<td width="10%">---<br>( <?=$langage_lbl_admin['LBL_VEHICLE_TYPE_SMALL_TXT'];?> : <?= $data_drv[$i]['vVehicleType']; ?>)</td>

														<?php  } ?>

													  <td width="10%"><?php  
                            $sql="select iActive, eCancelledBy from trips where iTripId=".$data_drv[$i]['iTripId'];
                            $data_stat_check=$obj->MySQLSelect($sql);
                            if(!empty($data_stat_check))
                              {
                                for($d=0;$d<count($data_stat_check);$d++)
                                {
                                  if($data_stat_check[$d]['iActive'] == "Canceled") {
                                    echo "---";
                                  } else if($data_stat_check[$d]['iActive'] == "Finished"){ ?>
                                    <a target = "_blank" class="btn btn-primary" href="invoice.php?iTripId=<?=$data_drv[$i]['iTripId']?>" target="_blank">View</a>
                                 <?php   } else { 
                                   echo "---";
                                 }
                                }
                              } else {
                                  if($data_drv[$i]['iTripId'] != "" && $data_drv[$i]['eStatus'] == "Completed") { ?>
      														<a target = "_blank" class="btn btn-primary" href="invoice.php?iTripId=<?=$data_drv[$i]['iTripId']?>" target="_blank">View</a>
  													<?php  } else {
                                echo "---"; 
                              }
                            } ?>
														</td>
														<td width="15%">
                            <?php  
                            $setcurrentTime = strtotime(date('Y-m-d H:i:s'));
                            $bookingdate =  date("Y-m-d H:i", strtotime('+30 minutes',  strtotime($data_drv[$i]['dBooking_date'])));
                            $bookingdatecmp =strtotime($bookingdate);
                            if($data_drv[$i]['eStatus'] == "Assign" && $bookingdatecmp > $setcurrentTime) {
														  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']." Assigned";
													   } else if($data_drv[$i]['eStatus'] == 'Accepted'){
                                    echo $data_drv[$i]['eStatus'];
                            } else if($data_drv[$i]['eStatus'] == 'Declined'){
                                    echo $data_drv[$i]['eStatus'];
                                    ?>
                            <br /><a href="javascript:void(0);" class="btn btn-info" data-toggle="modal" data-target="#uiModal_<?=$data_drv[$i]['iCabBookingId'];?>">Cancel Reason</a>
                            <?php  
                            } else {
															$sql="select iActive, eCancelledBy from trips where iTripId=".$data_drv[$i]['iTripId'];
															$data_stat=$obj->MySQLSelect($sql);
															// echo "<pre>";print_r($data_stat);
															if($data_stat)
															{
																for($d=0;$d<count($data_stat);$d++)
																{
																	if($data_stat[$d]['iActive'] == "Canceled") {
																		$eCancelledBy = ($data_stat[$d]['eCancelledBy'] == 'Passenger') ? $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'] : $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
                                    //echo "Canceled By ".$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];
                                    echo "Canceled By ".$eCancelledBy;
																	} else if($data_stat[$d]['iActive'] == "Finished" && $data_stat[$d]['eCancelledBy'] == "Driver" ){
                                    echo "Canceled By ".$eCancelledBy;
                                  } else {
																		echo $data_stat[$d]['iActive']; 	
																	}
																}
															} else {
																if($data_drv[$i]['eStatus'] == "Cancel") {
																	//echo "Canceled By ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
																	if($data_drv[$i]['eCancelBy'] == "Driver"){
																		echo "Canceled By ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
																	}else if($data_drv[$i]['eCancelBy'] == "Rider"){
																		echo "Canceled By ".$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];
																	} else{
                                    echo "Canceled By Admin";
                                  }
																} else {
																	
																	
																	if($data_drv[$i]['eStatus'] == 'Pending' && $bookingdatecmp > $setcurrentTime ){
																		echo $data_drv[$i]['eStatus'];
																	} else {
																		echo 'Expired';
																	}
																}
															}
														}
														?>
													<?php 
														if ($data_drv[$i]['eStatus'] == "Cancel") {
													?>
														<br /><a href="javascript:void(0);" class="btn btn-info" data-toggle="modal" data-target="#uiModal_<?=$data_drv[$i]['iCabBookingId'];?>">Cancel Reason</a>
													<?php            
														}
                            if(($bookingdatecmp >  time()) && ($data_drv[$i]['eStatus'] == 'Pending' || $data_drv[$i]['eStatus'] == "Assign" || $data_drv[$i]['eStatus'] == "Accepted") ) {
													?>
                          <div>
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#delete_form<?php  echo $data_drv[$i]['iCabBookingId'];?>">Cancel Booking</button>
                            <!-- Modal -->
                            <div id="delete_form<?php  echo $data_drv[$i]['iCabBookingId'];?>" class="modal fade delete_form" role="dialog">
                              <div class="modal-dialog">

                                <!-- Modal content-->
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">x</button>
                                    <h4 class="modal-title">Booking Cancel</h4>
                                  </div>
                                    <form  role="form" name="delete_form" id="delete_form1" method="post" action="" class="margin0">
                                  <div class="modal-body">
                                    <div class="form-group" style="display: inline-block;">
                                        <label class="col-xs-4 control-label">Cancel Reason<span class="red">*</span></label>
                                        <div class="col-xs-7">
                                            <textarea name="cancel_reason" id="cancel_reason" rows="4" cols="40" required="required"></textarea>
                                            <div class="cnl_error error red"></div>
                                        </div>
                                    </div>
                                      <input type="hidden" name="hdn_del_id" id="hdn_del_id" value="<?= $data_drv[$i]['iCabBookingId']; ?>">
                                      <input type="hidden" name="action" id="action" value="delete">
                                      <input type="hidden" name="iDriverId" id="iDriverId" value="<?= $data_drv[$i]['iDriverId']; ?>">
                                      <input type="hidden" name="iUserId" id="iUserId" value="<?= $data_drv[$i]['iUserId']; ?>">
                                  </div>
                                  <div class="modal-footer">
                                    <button type="submit" class="btn btn-info" id="cnl_booking" title="Cancel Booking">Cancel Booking</button>
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                  </div>
                                  </form> 
                                </div>

                              </div>
                            </div>
                            <!-- Modal -->
                          </div> 
                          <?php  } ?>
												  </td>
												</tr>
												<div class="modal fade" id="uiModal_<?=$data_drv[$i]['iCabBookingId'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
													  <div class="modal-content image-upload-1" style="width:400px;">
														   <div class="upload-content" style="width:350px; padding:0px;">
																<h3>Booking Cancel Reason</h3>
                                <?php  if(!empty($data_drv[$i]['eCancelBy'])) { ?> 
															  <h4>Cancel By: 
																<?php 
																if($APP_TYPE != "UberX"){
																
																echo $data_drv[$i]['eCancelBy'];
																
																} else{
																	if($data_drv[$i]['eCancelBy'] == "Driver"){
																	echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
																	} else if($data_drv[$i]['eCancelBy'] == "Rider"){
																	echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];
																	}
																} 
																?></h4>
                                <?php  } ?>
																<h4>Cancel Reason: <?=$data_drv[$i]['vCancelReason'];?></h4>
																<form class="form-horizontal" id="frm6" method="post" enctype="multipart/form-data" action="" name="frm6">
																<input style="margin:10px 0 20px;" type="button" class="save" data-dismiss="modal" name="cancel" value="Close"></form>
														   </div>
													  </div>
                                                    <?php  } }else { ?>
                                                        <tr class="gradeA">
                                                            <td colspan="8"> No Records Found.</td>
                                                        </tr>
                                                    <?php  } ?>
                                                    </tbody>
                                                </table>
                                            </form>
										<?php  include('pagination_n.php'); ?>
                                    </div>
                                </div> <!--TABLE-END-->
                            </div>
                        </div>
                    <div class="admin-notes">
                            <h4>Notes:</h4>
                            <ul>
								<li>
										Bookings module will list all Bookings on this page.
								</li>
								<li>
										Administrator can Activate / Deactivate / Delete any booking.
								</li>
								<li>
										Administrator can export data in XLS or PDF format.
								</li>
                            </ul>
                    </div>
                    </div>
                </div>
                <!--END PAGE CONTENT -->
            </div>
            <!--END MAIN WRAPPER -->
			
			<div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Manual Taxi Dispatch</h4>
          </div>
          <div class="modal-body">
          <div class="row">
            <div class="col-lg-12">
              <label><?=$langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']?> <span class="red"> *</span></label>
            </div>
            <div class="col-lg-6">
              <select name="frmDriver" id="frmDriver" onChange="shoeDriverDetail002(this.value);" class="form-control  filter-by-text">
                <option value="">Select <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']?></option>
                <?php 
                if(count($driverData)>0){
                  for($i=0;$i<count($driverData);$i++){
                ?>
                  <option value="<?php  echo $driverData[$i]['iDriverId'];?>"><?php  echo $driverData[$i]['vName'].' '.$driverData[$i]['vLastName']." ( +". $driverData[$i]['vCode']."&nbsp;".$driverData[$i]['vPhone']." )" ;?></option>
                <?php 
                  }
                }
                ?>
              </select>
            </div>
          </div>
          <br><span class="col-lg-6" id="showDriver003"></span>
          <input type="hidden" name="iBookingId" id="iBookingId" value="" >
          </div>
          <div class="modal-footer">
          <button type="button" class="btn btn-primary" onclick="assignDriverForBooking();">Assign <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']?></button>
          <button type="button" class="btn btn-default" onclick="closeModal();">Close</button>
          </div>
        </div>
        </div>
      </div>        
            
<form name="pageForm" id="pageForm" action="action/admin.php" method="post" >
<input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
<input type="hidden" name="iAdminId" id="iMainId01" value="" >
<input type="hidden" name="status" id="status01" value="" >
<input type="hidden" name="statusVal" id="statusVal" value="" >
<input type="hidden" name="option" value="<?php  echo $option; ?>" >
<input type="hidden" name="keyword" value="<?php  echo $keyword; ?>" >
<input type="hidden" name="sortby" id="sortby" value="<?php  echo $sortby; ?>" >
<input type="hidden" name="order" id="order" value="<?php  echo $order; ?>" >
<input type="hidden" name="eType" id="eType" value="<?php  echo $eType; ?>" >
<input type="hidden" name="method" id="method" value="" >
</form>
<link rel="stylesheet" href="css/select2/select2.min.css" />
<script src="js/plugins/select2.min.js"></script>
    <?php  include_once('footer.php'); ?>
        <script>
            $("#setAllCheck").on('click',function(){
                if($(this).prop("checked")) {
                    jQuery("#_list_form input[type=checkbox]").each(function() {
                        if($(this).attr('disabled') != 'disabled'){
                            this.checked = 'true';
                        }
                    });
                }else {
                    jQuery("#_list_form input[type=checkbox]").each(function() {
                        this.checked = '';
                    });
                }
            });

            $("#Search").on('click', function(){
                var action = $("#_list_form").attr('action');
                var formValus = $("#frmsearch").serialize();
                window.location.href = action+"?"+formValus;
            });

            $('.entypo-export').click(function(e){
                 e.stopPropagation();
                 var $this = $(this).parent().find('div');
                 $(".openHoverAction-class div").not($this).removeClass('active');
                 $this.toggleClass('active');
            });

            $(document).on("click", function(e) {
                if ($(e.target).is(".openHoverAction-class,.show-moreOptions,.entypo-export") === false) {
                  $(".show-moreOptions").removeClass("active");
                }
            });
              
/*            function confirm_delete()
            {
                var confirm_ans = confirm("Are You sure You want to Cancel this Booking?");
                
                if (confirm_ans == true) {

                  document.getElementById('delete_form1').submit();

                }else{

                  return false;
                }        
                
            }*/

            $(function(){
              $("#cnl_booking").on('click', function(e) {
                 var cancel_reason = $('#cancel_reason');
                 if(!cancel_reason.val()) {
                  $(".cnl_error").html("This Field is required.");
                  return false;
                 } else {
                  $( "#delete_form1" )[0].submit();
                 }

              });
            });
			
			function assignDriver(bookingId){
                  $('#iBookingId').val(bookingId);
                  $('#txtDriverEmail').val('');
                  $('#txtDriverCompanyName').val('');
                  $('#txtDriverMobileNumber').val('');
                  $('#driverdetail').css('display','none');
                  $('#myModal').modal('show');
           }
		   
		    function assignDriverForBooking(){
            driverId = $('#frmDriver').val();
            if(driverId != "") {
              bookingId = $('#iBookingId').val();
              var request = $.ajax({
                  type: "POST",
                  url: 'ajax_assign_driver_cabbooking.php',
                  data: {'driverId':driverId,'bookingId':bookingId},
                  success: function (data)
                  {
                    if(data.trim() == 1) {
                      window.location = 'cab_booking.php';
                    }else {
                      alert('Email sending failed.');
                      window.location = 'cab_booking.php';
                    }
                  }
              });
            }else {
              alert('Please assign a Driver.');
            }
           }
		   
		   function closeModal(){
                $('#myModal').modal('hide');
                $('#driverdetail').css('display','none');
                $('#frmDriver').val('');
                $('#txtDriverEmail').val('');
                $('#txtDriverCompanyName').val('');
                $('#txtDriverMobileNumber').val('');
          }
		   $('select.filter-by-text').select2();
          function shoeDriverDetail002(id) {
            if(id != "") {
            var request2 = $.ajax({
              type: "POST",
              url: 'show_driver.php',
              dataType: 'html',
              data: 'id=' + id,
              success: function (data)
              {
                $("#showDriver003").html(data);
              }, error: function(data) {
              }
            });
            }else {
              $("#showDriver003").html('');
            }
          }
        </script>

    </body>
    <!-- END BODY-->
</html>