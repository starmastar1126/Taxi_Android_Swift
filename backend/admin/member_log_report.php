<?php 
	include_once('../common.php');
	
	if (!isset($generalobjAdmin)) {
		require_once(TPATH_CLASS . "class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	$script = "Application Usage Report";
	
	$sql = "select iDriverId, CONCAT(vName,' ',vLastName) AS driverName ,vEmail from register_driver WHERE eStatus != 'Deleted' order by vName";
	$db_drivers = $obj->MySQLSelect($sql);
	
	//Start Sorting
	$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
	$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
	$ord = ' ORDER BY iMemberLogId DESC';
	
	
	//End Sorting
	// Start Search Parameters
	
	$cur_date = date('Y-m-d');
	$prev_date = date('Y-m-d',strtotime("-1 day"));
	// $cur_date = "2018-02-28";
	
	
	$ssql = '';
	$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
	$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
	$startDate = isset($_REQUEST['startDate']) ? $_REQUEST['startDate'] : $prev_date;
	$endDate = isset($_REQUEST['endDate']) ? $_REQUEST['endDate'] : $prev_date;
	$vEmail = isset($_REQUEST['vEmail']) ? $_REQUEST['vEmail'] : '';
	
	if ($startDate != '' && $endDate != '') {
		$search_startDate = $startDate.' 00:00:00';
		$search_endDate = $endDate.' 23:59:00';
		$ssql .= " AND dDateTime >= '".$search_startDate."' and dDateTime <= '".$search_endDate."' ";
	}
	if ($iDriverId != '') {
		$ssql .= " AND rd.iDriverId = '" . $iDriverId . "'";
	}
	if ($vEmail != '') {
		$ssql .= " AND rd.vEmail = '" . $vEmail . "'";
	}
	
	//Pagination Start
	$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
	// $per_page = "10";
	
	$sql = "select iMemberId from member_log where 1=1 $ssql group by iMemberId ";
	$totalData = $obj->MySQLSelect($sql);
	$total_results = count($totalData);
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
	
	
	$sql = "select iMemberId, count(iMemberId) as totlogin, eMemberType, vIP,dDateTime from member_log where 1=1 $ssql group by iMemberId $ord LIMIT $start, $per_page";
	$db_log_report = $obj->MySQLSelect($sql);
	
	$tot_login = "0";
	$tot_visitor = "0";
	$tot_trips = "0";
	
	/* for($i=0;$i<count($db_log_report);$i++){
		$tot_login = $tot_login+$db_log_report[$i]['totlogin'];
	} */
	$sql="select count(iMemberId) as TOTAL from member_log where 1=1 $ssql ";
	$data_tot_login = $obj->MySQLSelect($sql);
	
	$tot_login = $data_tot_login[0]['TOTAL'];
	$tot_visitor = $total_results;

	$endRecord = count($db_log_report);
	
	$var_filter = "";
	foreach ($_REQUEST as $key => $val) {
		if ($key != "tpages" && $key != 'page')
        $var_filter .= "&$key=" . stripslashes($val);
	}
	$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . $var_filter;
	// echo "<pre>"; print_r($db_log_report); exit;
	
	$Today = $cur_date;
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
	
	
	function getusername($id,$type){
		global $obj;
		if($type == "Passenger"){
			$sql = "SELECT concat(vName,' ',vLastName) as name, vEmail, vCountry from register_user where iUserId='".$id."'";
			$db_user = $obj->MySQLSelect($sql);
			if($db_user[0]['vEmail'] == "rider@gmail.com"){
				return $db_user[0]['name']." (".$db_user[0]['vEmail'].") <font color='red'>[Demo User]</font>";			
				}else{
				return $db_user[0]['name']." (".$db_user[0]['vEmail'].")";
			}
		}
		
		if($type == "Driver"){
			$sql = "SELECT concat(vName,' ',vLastName) as name, vEmail, vCountry from register_driver where iDriverId='".$id."'";
			$db_user = $obj->MySQLSelect($sql);
			if($db_user[0]['vEmail'] == "driver@gmail.com"){
				return $db_user[0]['name']." (".$db_user[0]['vEmail'].") <font color='red'>[Demo User]</font>";			
				}else{
				return $db_user[0]['name']." (".$db_user[0]['vEmail'].")";
			}
			
		}
	}
	
	function getvisitortype($id){
		global $obj, $prev_date;
		$sql = "SELECT count(iMemberLogId) as tot from member_log where iMemberId='".$id."' and dDateTime < '".$prev_date." 00:00:00'";
		$db_memlog = $obj->MySQLSelect($sql);
		if($db_memlog[0]['tot'] > 0){
			return "Returning";
			}else{
			return "New";		
		}
	}
	
	function get_trips_taken_today($id="",$type="",$time1,$time2){
		global $obj;
		if($id != ""){
			if($type == "Passenger"){
				$ssql= " and iUserId='".$id."'";
			}
			if($type == "Driver"){
				$ssql= " and iDriverId='".$id."'";
			}
		}
		
		$sql = "SELECT iTripId,tSaddress from trips where 1=1 $ssql and tTripRequestDate >= '$time1' and tTripRequestDate <= '$time2'";
		$db_memtrips_tdy = $obj->MySQLSelect($sql);
		
		$total = count($db_memtrips_tdy);
		$Data['Total_Trip'] = $total;
		if($total > 0){
			$Data['LAST_ADD'] = $db_memtrips_tdy[($total-1)]['tSaddress'];
		}
		return $Data;
	}
	
	$tot_trips_data = get_trips_taken_today("","",$startDate." 00:00:00",$endDate." 23:59:59");
	$tot_trips = $tot_trips_data['Total_Trip'];
	
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?= $SITE_NAME ?> | Application Usage Report</title>
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
                            <h2>Application Usage Report</h2>
							
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
                                            <!-- <div class="col-lg-4 select001">
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
											</div> -->
										</span>
									</div>
                                    <div class="tripBtns001"><b>
										<input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
										<input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'member_log_report.php'"/>
										
										<!--	<?php  //if (!empty($db_log_report)) { ?>
                                            <button type="button" onClick="reportExportTypes('driver_log_report')" class="export-btn001" >Export</button>
										<?php  //} ?> -->
									</b>
                                    </div>
								</form>
								<div style="clear:both"></div>
								<div class="row" style="margin:15px;">
									<div class="col-md-12" >
										<div class="col-md-4"><h4>Total Visitors: <?=$tot_visitor?></h4></div>
										<div class="col-md-4"><h4>Total Login Count: <?=$tot_login?></h4></div>
										<div class="col-md-4"><h4>Total Trip Count: <?=$tot_trips?></h4></div>
									</div>
								</div>
                                <div class="table-responsive">
                                    <form name="_list_form" id="_list_form" class="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
										
										<?php 
											if (!empty($db_log_report)) {
												for ($i = 0; $i < count($db_log_report); $i++) {
												?>
												<table class="table table-striped table-bordered table-hover" id="dataTables-example1">
													<tbody>
														<tr class="gradeA">   
															<td>
																<div class="row">
																		<div class="col-md-12" >
																			<div class="col-md-4"><h5>Name: <?=getusername($db_log_report[$i]['iMemberId'],$db_log_report[$i]['eMemberType'])?></h5></div>
																			<div class="col-md-4"><h5>User Type: <?=$db_log_report[$i]['eMemberType']?></h5></div>
																			<div class="col-md-4"><h5>Visitor: <?=getvisitortype($db_log_report[$i]['iMemberId'])?></h5></div>
																		</div>
																	</div>
															</td>
														</tr>
														<tr>
															<td>
																<table width="100%" cellpadding="3">
																		<tr>
																			<th width="20%">Login Time</th>
																			<th width="20%">IP</th>
																			<th width="20%">Trips Taken</th>
																			<th width="40%">Last Trip Location</th>
																		</tr>
																	<?php 
																		$sql="select iMemberId,dDateTime,vIP from member_log where iMemberId = '".$db_log_report[$i]['iMemberId']."' $ssql order by dDateTime ASC";
																		$data_drv = $obj->MySQLSelect($sql);
																		
																		for($j=0;$j<count($data_drv);$j++){
																	?>	
																		<tr>
																			<td><?=date('d\<\s\u\p\>S</\s\u\p\> M  \a\t H:i',strtotime($data_drv[$j]['dDateTime']));?></td>
																			<td><?=$data_drv[$j]['vIP'];?></td>
																			<td>
																				<?php 
																					$trip_data = get_trips_taken_today($db_log_report[$i]['iMemberId'],$db_log_report[$i]['eMemberType'],$data_drv[$j]['dDateTime'],$data_drv[$j+1]['dDateTime']);
																					
																					echo $trip_data['Total_Trip'];
																					
																				?>
																			</td>
																			<td>
																				<?php 
																					$str = "---";
																					if(isset($trip_data['LAST_ADD'])){
																						$str = $trip_data['LAST_ADD'];
																					}
																					echo $str;
																				?>
																			</td>
																		</tr>
																	<?php  } ?>
																	</table>
															</td>
														</tr>
													</tbody>
												</table>
											<?php  }  ?>
											
											<?php  } else {?>
											<table class="table table-striped table-bordered table-hover" id="dataTables-example1">
												<tbody>
													<tr class="gradeA">
														<td colspan="7"> No Records Found for date from <?=$startDate?> to <?=$endDate?>.</td>
													</tr>
												</tbody>
											</table>
										<?php  } ?>
										
										
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
			<input type="hidden" name="vStatus" value="<?php  echo $vStatus; ?>" >
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
