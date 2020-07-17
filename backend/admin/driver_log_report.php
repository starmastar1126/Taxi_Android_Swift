<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$script = "Driver Log Report";

$sql = "select iDriverId, CONCAT(vName,' ',vLastName) AS driverName ,vEmail from register_driver WHERE eStatus != 'Deleted' order by vName";
$db_drivers = $obj->MySQLSelect($sql);

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
//End Sorting
// Start Search Parameters
$ssql = '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : '';
$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : '';
$vEmail = isset($_REQUEST['vEmail']) ? $_REQUEST['vEmail'] : '';

if ($startDate != '' && $endDate != '') {
	$search_startDate = $startDate.' 00:00:00';
	$search_endDate = $endDate.' 23:59:00';
    $ssql .= " AND dlr.dLoginDateTime BETWEEN '" . $search_startDate . "' AND '" . $search_endDate . "'";
}
if ($iDriverId != '') {
    $ssql .= " AND rd.iDriverId = '" . $iDriverId . "'";
}
if ($vEmail != '') {
    $ssql .= " AND rd.vEmail = '" . $vEmail . "'";
}

//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page

$sql = "SELECT COUNT(dlr.iDriverLogId) AS Total FROM driver_log_report AS dlr
LEFT JOIN register_driver AS rd ON rd.iDriverId = dlr.iDriverId where 1=1 AND rd.eStatus != 'Deleted' $ssql";
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
if ($page <= 0)
    $page = 1;
//Pagination End

$sql = "SELECT rd.vName, rd.vLastName, rd.vEmail, dlr.dLoginDateTime, dlr.dLogoutDateTime
FROM driver_log_report AS dlr LEFT JOIN register_driver AS rd ON rd.iDriverId = dlr.iDriverId where 1=1 AND rd.eStatus != 'Deleted' $ssql $ord LIMIT $start, $per_page";
$db_log_report = $obj->MySQLSelect($sql);
$endRecord = count($db_log_report);

$var_filter = "";
foreach ($_REQUEST as $key => $val) {
    if ($key != "tpages" && $key != 'page')
        $var_filter .= "&$key=" . stripslashes($val);
}
$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . $var_filter;
//echo "<pre>"; print_r($db_log_report); exit;

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

$sql1 = "SELECT dlr.dLoginDateTime, dlr.dLogoutDateTime FROM driver_log_report AS dlr LEFT JOIN register_driver AS rd ON rd.iDriverId = dlr.iDriverId where 1=1 AND rd.eStatus != 'Deleted' $ssql $ord";
$db_log_report_total_time = $obj->MySQLSelect($sql1);

function secToHR($seconds) {
    $hours = floor($seconds / 3600);
    $mins = floor(($seconds / 60) % 60);
    $secs = $seconds % 60;

    if (strlen($hours) == 1)
    $hours = "0" . $hours;
    if (strlen($seconds) == 1)
    $seconds = "0" . $secs;
    if (strlen($mins) == 1)
    $minutes = "0" . $mins;
    
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
    return $ret;
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
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?= $SITE_NAME ?> | <?php  echo $langage_lbl_admin['LBL_DRIVER_NAME_ADMIN']; ?> Log Report<?php  echo $langage_lbl_admin['LBL_DRIVER_LOG_REPORT_SMALL_ADMIN']; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

        <?php  include_once('global_files.php');?>         
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53 " >

        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php  include_once('header.php'); ?>
            <?php  include_once('left_menu.php'); ?>

            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2><?php  echo $langage_lbl_admin['LBL_DRIVER_NAME_ADMIN']; ?> Log Report</h2>

                        </div>
                    </div>
                    <hr />
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                                    <div class="Posted-date mytrip-page mytrip-page-select payment-report">
                                        <input type="hidden" name="action" value="search" />
                                        <h3>Search by Date...</h3>
                                        <span>
                                            <a onClick="return todayDate('dp4', 'dp5');"><?= $langage_lbl_admin['LBL_MYTRIP_Today']; ?></a>
                                            <a onClick="return yesterdayDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Yesterday']; ?></a>
                                            <a onClick="return currentweekDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Current_Week']; ?></a>
                                            <a onClick="return previousweekDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Previous_Week']; ?></a>
                                            <a onClick="return currentmonthDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Current_Month']; ?></a>
                                            <a onClick="return previousmonthDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Previous Month']; ?></a>
                                            <a onClick="return currentyearDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Current_Year']; ?></a>
                                            <a onClick="return previousyearDate('dFDate', 'dTDate');"><?= $langage_lbl_admin['LBL_MYTRIP_Previous_Year']; ?></a>
                                        </span> 
                                        <span>
                                            <input type="text" id="dp4" name="startDate" placeholder="From Date" class="form-control" value=""/>
                                            <input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control" value=""/>
                                            <div class="col-lg-4 select001">
                                                <select class="form-control filter-by-text" name = 'iDriverId' data-text="Select Driver">
                                                    <option value="">Select Driver</option>
                                                    <?php  foreach ($db_drivers as $dbd) { ?>
                                                        <option value="<?php  echo $dbd['iDriverId']; ?>" <?php 
                                                        if ($iDriverId == $dbd['iDriverId']) {
                                                            echo "selected";
                                                        }
                                                        ?>> <?php  echo $generalobjAdmin->clearName($dbd['driverName']); ?> - ( <?php  echo $generalobjAdmin->clearEmail($dbd['vEmail']); ?> )</option>
                                                            <?php  } ?>
                                                </select>
                                            </div>
                                        </span>
                                    </div>
                                    <div class="tripBtns001"><b>
                                            <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                            <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'driver_log_report.php'"/>
                                            <?php  if (!empty($db_log_report)) { ?>
                                            <button type="button" onClick="reportExportTypes('driver_log_report')" class="export-btn001" >Export</button></b>
                                            <?php  } ?>
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <form name="_list_form" id="_list_form" class="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
                                        <table class="table table-striped table-bordered table-hover" id="dataTables-example1">
                                            <thead>
                                                <tr>  
                                                    <th><a href="javascript:void(0);" onClick="Redirect(1,<?php 
                                                        if ($sortby == '1') {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php  } ?>)">Name <?php 
                                                               if ($sortby == 1) {
                                                                   if ($order == 0) {
                                                                       ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php 
                                                                }
                                                            } else {
                                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                    <th><a href="javascript:void(0);" onClick="Redirect(2,<?php 
                                                        if ($sortby == '2') {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php  } ?>)">Email <?php 
                                                               if ($sortby == 2) {
                                                                   if ($order == 0) {
                                                                       ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php 
                                                                }
                                                            } else {
                                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                    <th style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(3,<?php 
                                                        if ($sortby == '3') {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php  } ?>)">Online Time <?php 
                                                          if ($sortby == 3) {
                                                              if ($order == 0) {
                                                                  ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php 
                                                                }
                                                            } else {
                                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                    <th style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(4,<?php 
                                                        if ($sortby == '4') {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php  } ?>)">Offline Time <?php 
                                                      if ($sortby == 4) {
                                                          if ($order == 0) {
                                                              ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php 
                                                                }
                                                            } else {
                                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>                 
                                                    <th>Total Hours Login</th>     
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                if (!empty($db_log_report)) {
                                                    for ($i = 0; $i < count($db_log_report); $i++) {
                                                        $dstart = $db_log_report[$i]['dLoginDateTime'];
                                                        if ($db_log_report[$i]['dLogoutDateTime'] == '0000-00-00 00:00:00' || $db_log_report[$i]['dLogoutDateTime'] == '') {
                                                            $dLogoutDateTime = '--';
                                                            $totalTimecount = '--';
                                                        } else {
                                                            $dLogoutDateTime = $db_log_report[$i]['dLogoutDateTime'];
                                                            $totalhours = $generalobjAdmin->get_left_days_jobsave($dLogoutDateTime, $dstart);
                                                            //$totalTimecount = $generalobjAdmin->mediaTimeDeFormater($totalhours);
                                                            $totalTimecount = mediaTimeDeFormater($totalhours);
                                                            //$totalTimecount = $totalTimecount.' hrs';
                                                        }
                                                        ?>
                                                        <tr class="gradeA">   
                                                            <td><?php  echo $generalobjAdmin->clearName($db_log_report[$i]['vName'].' '.$db_log_report[$i]['vLastName']); ?></td>
                                                            <td><?php  echo $generalobjAdmin->clearEmail($db_log_report[$i]['vEmail']); ?></td>
                                                            <td style="text-align:center;"><?php  echo $generalobjAdmin->DateTime($db_log_report[$i]['dLoginDateTime']); ?></td>
															<?php  $logooutdate = ($db_log_report[$i]['dLogoutDateTime'] == '0000-00-00 00:00:00' || $db_log_report[$i]['dLogoutDateTime'] == '') ? '--': $generalobjAdmin->DateTime($dLogoutDateTime); ?>
                                                            <td align="center"><?php  echo $logooutdate; ?></td>                          
                                                            <td align="center"><?php  echo $totalTimecount; ?></td>
                                                        </tr>
                                                        <?php  }  ?>
                                                        <?php   if (!empty($db_log_report_total_time)) {
                                                            $totalsecondssum = 0;
                                                        for ($i = 0; $i < count($db_log_report_total_time); $i++) {
                                                            $dstart1 = $db_log_report_total_time[$i]['dLoginDateTime'];
                                                            if ($db_log_report_total_time[$i]['dLogoutDateTime'] == '0000-00-00 00:00:00' || $db_log_report_total_time[$i]['dLogoutDateTime'] == '') {
                                                                // $totalsecondssum = '';
                                                            } else {
                                                                $dLogoutDateTime1 = $db_log_report_total_time[$i]['dLogoutDateTime'];
                                                                $totalhours1 = $generalobjAdmin->get_left_days_jobsave($dLogoutDateTime1, $dstart1);
                                                                $totalsecondssum +=  $totalhours1; 
                                                                //$totalTimecount = $totalTimecount.' hrs';
                                                            }
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td colspan="4" align="right">Grand Total Hours</td>
                                                            <?php  if($totalsecondssum != '') {  ?>
                                                                <td align="center"><?= secToHR($totalsecondssum); ?></td>
                                                            <?php  } else { ?>
                                                                <td align="center">--</td>
                                                            <?php  } ?>
                                                        </tr>
                                                        <?php  } ?>
                                                    <?php  } else {?>
                                                        <tr class="gradeA">
                                                            <td colspan="5"> No Records Found.</td>
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
                <input type="hidden" name="iDriverId" value="<?php  echo $iDriverId; ?>" >
                <input type="hidden" name="startDate" value="<?php  echo $startDate; ?>" >
                <input type="hidden" name="endDate" value="<?php  echo $endDate; ?>" >
                <input type="hidden" name="vStatus" value="" >
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

                $("select.filter-by-text").each(function () {
                    $(this).select2({
                        placeholder: $(this).attr('data-text'),
                        allowClear: true
                    }); //theme: 'classic'
                });
            });

            function setRideStatus(actionStatus) {
                window.location.href = "trip.php?type=" + actionStatus;
            }
            function todayDate()
            {
                //alert('sa');
                $("#dp4").val('<?= $Today; ?>');
                $("#dp5").val('<?= $Today; ?>');
            }
            function resetform()
            {
                //location.reload();
                document.search.reset();
                document.getElementById("iDriverId").value = " ";
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
        function checkvalid() {
            if ($("#dp5").val() < $("#dp4").val()) {
                alert("From date should be lesser than To date.")
                return false;
            }
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
        </script>
    </body>
    <!-- END BODY-->
</html>
