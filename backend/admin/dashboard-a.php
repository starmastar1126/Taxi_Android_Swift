<?php 
	include_once('../common.php');
	if(!isset($generalobjAdmin))
	{
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
		
		require_once(TPATH_CLASS."class.general_dashboard.php");
		$generalobjDashboard = new General_dashboard();
	}
	$generalobjAdmin->check_member_login();
	$script = 'site';
	
	$rider 		= $generalobjDashboard->getRiderCount("");
	$driver 	=$generalobjAdmin->getDriverDetailsDashboard('');
	//$driver 	= $generalobjDashboard->getDrivercount("");

	$company 	= $generalobjDashboard->getCompanycount();
	$totalEarns	= $generalobjDashboard->getTotalEarns();
	
	$totalRides = $generalobjDashboard->getTripStatescount('total');
	$onRides = $generalobjDashboard->getTripStatescount('on ride');
	$cancelRides = $generalobjDashboard->getTripStatescount('cancelled');
	$finishRides = $generalobjDashboard->getTripStatescount('finished');
	
	//	$vehicle	= $generalobjAdmin->getVehicleDetails();
	//	$trips		= $generalobjAdmin->getTripsDetails();
	
	
	
	$actDrive = $generalobjDashboard->getDrivercount('active');
	$inaDrive = $generalobjDashboard->getDrivercount('inactive');	
	$delDrive = $generalobjDashboard->getDrivercount('Deleted');
	
	
	/*****************************/
	$finishRides_1 = $generalobjDashboard->getTripStatescount('finished',date('Y-m', strtotime(date('Y-m')." -2 month"))."-"."01",date('Y-m', strtotime(date('Y-m')." -2 month"))."-"."31");
	$finishRides_2 = $generalobjDashboard->getTripStatescount('finished',date('Y-m', strtotime(date('Y-m')." -1 month"))."-"."01",date('Y-m', strtotime(date('Y-m')." -1 month"))."-"."31");
	$finishRides_3 = $generalobjDashboard->getTripStatescount('finished',date('Y-m', strtotime(date('Y-m').""))."-"."01",date('Y-m', strtotime(date('Y-m').""))."-"."31");
	
	$cancelledRides_1 = $generalobjDashboard->getTripStatescount('cancelled',date('Y-m', strtotime(date('Y-m')." -2 month"))."-"."01",date('Y-m', strtotime(date('Y-m')." -2 month"))."-"."31");
	$cancelledRides_2 = $generalobjDashboard->getTripStatescount('cancelled',date('Y-m', strtotime(date('Y-m')." -1 month"))."-"."01",date('Y-m', strtotime(date('Y-m')." -1 month"))."-"."31");
	$cancelledRides_3 = $generalobjDashboard->getTripStatescount('cancelled',date('Y-m', strtotime(date('Y-m').""))."-"."01",date('Y-m', strtotime(date('Y-m').""))."-"."31");
	
	/*****************************/
	
	/*****************************/
	$sql = "SELECT count(iDriverId) as TotalDriver FROM register_driver WHERE 1  AND tRegistrationDate BETWEEN '".date('Y-m', strtotime(date('Y-m-d')." -2 month"))."-"."01"." 00:00:00' AND '".date('Y-m', strtotime(date('Y-m-d')." -2 month"))."-"."31"." 23:59:59'";
	$driver_2 = $obj->MySQLSelect($sql);
	
	$sql = "SELECT count(iDriverId) as TotalDriver1 FROM register_driver WHERE 1  AND tRegistrationDate BETWEEN '".date('Y-m', strtotime(date('Y-m-d')." -1 month"))."-"."01"." 00:00:00' AND '".date('Y-m', strtotime(date('Y-m-d')." -1 month"))."-"."31"." 23:59:59'";
	$driver_1 = $obj->MySQLSelect($sql);
	
	$sql = "SELECT count(iDriverId) as TotalDriver2 FROM register_driver WHERE 1  AND tRegistrationDate BETWEEN '".date('Y-m', strtotime(date('Y-m-d')))."-"."01"." 00:00:00' AND '".date('Y-m', strtotime(date('Y-m-d')))."-"."31"." 23:59:59'";
	$driver_0 = $obj->MySQLSelect($sql);
	/*****************************/
	
	/*****************************/
	$sql = "SELECT count(iUserId) as TotalRider FROM register_user WHERE 1  AND tRegistrationDate BETWEEN '".date('Y-m', strtotime(date('Y-m-d')." -2 month"))."-"."01"." 00:00:00' AND '".date('Y-m', strtotime(date('Y-m-d')." -2 month"))."-"."31"." 23:59:59'";
	$pass_2 = $obj->MySQLSelect($sql);
	
	$sql = "SELECT count(iUserId) as TotalRider1 FROM register_user WHERE 1  AND tRegistrationDate BETWEEN '".date('Y-m', strtotime(date('Y-m-d')." -1 month"))."-"."01"." 00:00:00' AND '".date('Y-m', strtotime(date('Y-m-d')." -1 month"))."-"."31"." 23:59:59'";
	$pass_1 = $obj->MySQLSelect($sql);
	
	$sql = "SELECT count(iUserId) as TotalRider2 FROM register_user WHERE 1  AND tRegistrationDate BETWEEN '".date('Y-m', strtotime(date('Y-m-d')))."-"."01"." 00:00:00' AND '".date('Y-m', strtotime(date('Y-m-d')))."-"."31"." 23:59:59'";
	$pass_0 = $obj->MySQLSelect($sql);
	/*****************************/
	
	
	/*****************************/
	$startDate = date('Y-m', strtotime(date('Y-m-d')." -5 month"))."-"."01"." 00:00:00";
	$endDate = date('Y-m', strtotime(date('Y-m-d')." -5 month"))."-"."31"." 23:59:59";
	$trip_amt5 = $generalobjDashboard->getTripAmount($startDate,$endDate);
	$fTripGenerateFare5 = 0;
	$fDiscount5 = 0;
	$fWalletDebit5 = 0;
	$iFare5 = 0;
	
	$Cash5 = 0;
	$cashPayment = $generalobjDashboard->getTripAmount($startDate,$endDate,"Cash");
	for($i=0;$i<count($trip_amt5);$i++)
	{
		$Cash5 += $trip_amt5[$i]['iFare'];
	}
	for($i=0;$i<count($trip_amt5);$i++)
	{
		if($trip_amt5[$i]['fTripGenerateFare'])
		{
			$fTripGenerateFare5 += $trip_amt5[$i]['fTripGenerateFare'];
			$fDiscount5 += $trip_amt5[$i]['fDiscount'];
			$fWalletDebit5 += $trip_amt5[$i]['fWalletDebit'];
			$iFare5 += $trip_amt5[$i]['iFare'];
			
		}
	}
	
	$startDate = date('Y-m', strtotime(date('Y-m-d')." -4 month"))."-"."01"." 00:00:00";
	$endDate = date('Y-m', strtotime(date('Y-m-d')." -4 month"))."-"."31"." 23:59:59";
	$trip_amt4 = $generalobjDashboard->getTripAmount($startDate,$endDate);
	$fTripGenerateFare4 = 0;
	$fDiscount4 = 0;
	$fWalletDebit4 = 0;
	$iFare4 = 0;
	
	$Cash4 = 0;
	$cashPayment = $generalobjDashboard->getTripAmount($startDate,$endDate,"Cash");
	for($i=0;$i<count($trip_amt4);$i++)
	{
		$Cash4 += $trip_amt4[$i]['iFare'];
	}
	for($i=0;$i<count($trip_amt4);$i++)
	{
		if($trip_amt4[$i]['fTripGenerateFare'])
		{
			$fTripGenerateFare4 += $trip_amt4[$i]['fTripGenerateFare'];
			$fDiscount4 += $trip_amt4[$i]['fDiscount'];
			$fWalletDebit4 += $trip_amt4[$i]['fWalletDebit'];
			$iFare4 += $trip_amt4[$i]['iFare'];
		}
	}
	
	$startDate = date('Y-m', strtotime(date('Y-m-d')." -3 month"))."-"."01"." 00:00:00";
	$endDate = date('Y-m', strtotime(date('Y-m-d')." -3 month"))."-"."31"." 23:59:59";
	$trip_amt3 = $generalobjDashboard->getTripAmount($startDate,$endDate);
	
	$fTripGenerateFare3 = 0;
	$fDiscount3 = 0;
	$fWalletDebit3 = 0;
	$iFare3 = 0;
	
	$Cash3 = 0;
	$cashPayment = $generalobjDashboard->getTripAmount($startDate,$endDate,"Cash");
	for($i=0;$i<count($trip_amt3);$i++)
	{
		$Cash3 += $trip_amt3[$i]['iFare'];
	}
	for($i=0;$i<count($trip_amt3);$i++)
	{
		if($trip_amt3[$i]['fTripGenerateFare'])
		{
			$fTripGenerateFare3 += $trip_amt3[$i]['fTripGenerateFare'];
			$fDiscount3 += $trip_amt3[$i]['fDiscount'];
			$fWalletDebit3 += $trip_amt3[$i]['fWalletDebit'];
			$iFare3 += $trip_amt3[$i]['iFare'];
		}
	}
	
	
	$startDate = date('Y-m', strtotime(date('Y-m-d')." -2 month"))."-"."01"." 00:00:00";
	$endDate = date('Y-m', strtotime(date('Y-m-d')." -2 month"))."-"."31"." 23:59:59";
	$trip_amt2 = $generalobjDashboard->getTripAmount($startDate,$endDate);
	$fTripGenerateFare2 = 0;
	$fDiscount2 = 0;
	$fWalletDebit2 = 0;
	$iFare2 = 0;
	
	$Cash2 = 0;
	$cashPayment = $generalobjDashboard->getTripAmount($startDate,$endDate,"Cash");
	for($i=0;$i<count($trip_amt2);$i++)
	{
		$Cash2 += $trip_amt2[$i]['iFare'];
	}
	for($i=0;$i<count($trip_amt2);$i++)
	{
		if($trip_amt2[$i]['fTripGenerateFare'])
		{
			$fTripGenerateFare2 += $trip_amt2[$i]['fTripGenerateFare'];
			$fDiscount2 += $trip_amt2[$i]['fDiscount'];
			$fWalletDebit2 += $trip_amt2[$i]['fWalletDebit'];
			$iFare2 += $trip_amt2[$i]['iFare'];
		}
	}
	
	$startDate = date('Y-m', strtotime(date('Y-m-d')." -1 month"))."-"."01"." 00:00:00";
	$endDate = date('Y-m', strtotime(date('Y-m-d')." -1 month"))."-"."31"." 23:59:59";
	$trip_amt1 = $generalobjDashboard->getTripAmount($startDate,$endDate);
	$fTripGenerateFare1 = 0;
	$fDiscount1 = 0;
	$fWalletDebit1 = 0;
	$iFare1 = 0;
	
	$Cash1 = 0;
	$cashPayment = $generalobjDashboard->getTripAmount($startDate,$endDate,"Cash");
	for($i=0;$i<count($trip_amt1);$i++)
	{
		$Cash1 += $trip_amt1[$i]['iFare'];
	}
	for($i=0;$i<count($trip_amt1);$i++)
	{
		if($trip_amt1[$i]['fTripGenerateFare'])
		{
			$fTripGenerateFare1 += $trip_amt1[$i]['fTripGenerateFare'];
			$fDiscount1 += $trip_amt1[$i]['fDiscount'];
			$fWalletDebit1 += $trip_amt1[$i]['fWalletDebit'];
			$iFare1 += $trip_amt1[$i]['iFare'];
		}
	}
	
	$startDate = date('Y-m', strtotime(date('Y-m-d').""))."-"."01"." 00:00:00";
	$endDate = date('Y-m', strtotime(date('Y-m-d').""))."-"."31"." 23:59:59";
	$trip_amt0 = $generalobjDashboard->getTripAmount($startDate,$endDate);
	$fTripGenerateFare0 = 0;
	$fDiscount0 = 0;
	$fWalletDebit0 = 0;
	$iFare0 = 0;
	
	$Cash0 = 0;
	$cashPayment = $generalobjDashboard->getTripAmount($startDate,$endDate,"Cash");
	for($i=0;$i<count($trip_amt0);$i++)
	{
		$Cash0 += $trip_amt0[$i]['iFare'];
	}
	for($i=0;$i<count($trip_amt0);$i++)
	{
		if($trip_amt0[$i]['fTripGenerateFare'])
		{
			$fTripGenerateFare0 += $trip_amt0[$i]['fTripGenerateFare'];
			$fDiscount0 += $trip_amt0[$i]['fDiscount'];
			$fWalletDebit0 += $trip_amt1[$i]['fWalletDebit'];
			$iFare0 += $trip_amt0[$i]['iFare'];
		}
	}
	
	$vDefaultName = $generalobjDashboard->getDefaultCurency();
	$vDefaultName = $vDefaultName[0]['vName'];
	/*****************************/
	
	
	
	/*****************************/
	function get_left_days_jobsave($dend,$dstart)
	{
		$dayinpass = $dstart;
		$today = strtotime($dend); 
		$dayinpass= strtotime($dayinpass);
		return round(abs($today-$dayinpass));
	}
	function mediaTimeDeFormater($seconds)
	{
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
		if ($hours == 0)
		{
			if($mins > 1)
			{
				$ret = "$mins";
			}
			else
			{
				$ret = "$mins";
			}
		}      
		else
		{
			$mint="";
			if($mins > 01)
			{
				$mint = "$mins";
			}
			else
			{
				$mint = "$mins";
			}    
			if($hours > 1)
			{
				//$ret = "$hours hrs $mint";
				$ret = $hours * 60 + $mint;
			}
			else
			{
				$ret = "$hours hr $mint";
			}
		}
		return  $ret;
	}

function secToHR($seconds) {
  $hours = floor($seconds / 3600);
  $minutes = floor(($seconds / 60) % 60);
  $seconds = $seconds % 60;
  return "$hours:$minutes:$seconds";
}



	//	$iDriverIdBy = '1';
	//	$ssql.=" And rd.iDriverId = '".$iDriverIdBy."'";
	
	
	$sql = "SELECT rd.iDriverId, rd.vName, rd.vLastName, dlr.dLoginDateTime, dlr.dLogoutDateTime
	FROM driver_log_report AS dlr
	LEFT JOIN register_driver AS rd ON rd.iDriverId = dlr.iDriverId where 1=1 AND dlr.dLoginDateTime BETWEEN '".date('Y-m', strtotime(date('Y-m-d')))."-"."01"." 00:00:00' AND '".date('Y-m', strtotime(date('Y-m-d')))."-"."31"." 23:59:59' order by dlr.iDriverLogId DESC";
	$db_log_report = $obj->MySQLSelect($sql); 
	$log_report = array();
	
	/* $sql = "SELECT rd.iDriverId, rd.vName, rd.vLastName, dlr.dLoginDateTime, dlr.dLogoutDateTime
	FROM driver_log_report AS dlr
	LEFT JOIN register_driver AS rd ON rd.iDriverId = dlr.iDriverId where 1=1 ".$ssql." order by dlr.iDriverLogId DESC";
	$db_log_report = $obj->MySQLSelect($sql);  */
	//echo "<pre>"; print_r($db_log_report); exit;
	
	
	for($i=0;$i<count($db_log_report);$i++)
	{
		$dstart = $db_log_report[$i]['dLoginDateTime'];
		if( $db_log_report[$i]['dLogoutDateTime'] == '0000-00-00 00:00:00' || $db_log_report[$i]['dLogoutDateTime'] == '' )
		{
			$dLogoutDateTime = '--';
			$totalTimecount = '--';
			$totalhours = '--';
		}
		else
		{
			$dLogoutDateTime = $db_log_report[$i]['dLogoutDateTime'];
			$totalhours = get_left_days_jobsave ($dLogoutDateTime,$dstart);
			$totalTimecount = mediaTimeDeFormater ($totalhours);
			$db_log_report[$i]['totalTimecount'] = $totalTimecount;
			$db_log_report[$i]['totalHourTimecount'] = $totalhours;
			if($db_log_report[$i]['iDriverId'])
			{
				if($db_log_report[$i]['totalTimecount'] > 0)
				{
					//$log_report[$db_log_report[$i]['iDriverId']] += $db_log_report[$i]['totalTimecount'];
					$log_report[$db_log_report[$i]['iDriverId']]['totalHourTimecount'] += $db_log_report[$i]['totalHourTimecount'];
					$log_report[$db_log_report[$i]['iDriverId']]['totalTimecount']+= $db_log_report[$i]['totalTimecount'];
					$log_report[$db_log_report[$i]['iDriverId']]['Name']= $db_log_report[$i]['vName'] . " " . $db_log_report[$i]['vLastName'];
					$log_report[$db_log_report[$i]['iDriverId']]['iDriverId']= $db_log_report[$i]['iDriverId'];
				}	
			}
		}
	}
	
	/*****************************/
	if($log_report != '') {
	arsort($log_report);
	}

	$iii=0;
	if($log_report != '') {
		foreach($log_report as $log_report_key => $log_report_val)

		{

			$tmp_log_report[$iii]['totalHourTimecount'] = $log_report_val['totalHourTimecount'];

			$tmp_log_report[$iii]['totalTimecount'] = $log_report_val['totalTimecount'];

			$tmp_log_report[$iii]['iDriverId'] = $log_report_val['iDriverId'];

			$tmp_log_report[$iii]['Name'] = $log_report_val['Name'];

			$iii++;

		}
	}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	
	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin | Dashboard</title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<!--[if IE]>
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<![endif]-->
		<!-- GLOBAL STYLES -->
		<?php  include_once('global_files.php');?>
		<link rel="stylesheet" href="css/style.css" />
		<link rel="stylesheet" href="css/adminLTE/AdminLTE.min.css" />
		<script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
		<script type="text/javascript" src="js/plugins/morris/raphael-min.js"></script>
        <script type="text/javascript" src="js/plugins/morris/morris.min.js"></script> 
		<script type="text/javascript" src="js/actions.js"></script>
        <!-- END THIS PAGE PLUGINS-->
		<!--END GLOBAL STYLES -->
		
		<!-- PAGE LEVEL STYLES -->
		<!-- END PAGE LEVEL  STYLES -->
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
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
				
				<div class="inner" style="min-height: 700px;">
					<div class="row">
						<div class="col-lg-12">
							<h1>Site Statistics</h1>
						</div>
					</div>
					<hr />
					<!-- 
						<div class="row">
						<div class="col-lg-12">
						<div class="panel panel-primary bg-gray-light">
						<div class="panel-heading">
						<div class="panel-title-box">
						<i class="fa fa-bar-chart"></i> Site Statistics
						</div>                                  
						</div>
						<div class="row padding_005">
						<div class="col-lg-3"><a href="rider.php">
						<div class="info-box bg-box1">
						<span class="info-box-icon"><i class="fa fa-users"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_DASHBOARD_USERS_ADMIN'];?> </span>
						<span class="info-box-number"><?=$rider[0]['tot_rider'];?></span>
						</div>
						
						</div></a>
						
						</div> 
						<div class="col-lg-3"><a href="driver.php?type=approve">
						<div class="info-box bg-box2">
						<span class="info-box-icon"><i class="fa fa-male"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_DASHBOARD_DRIVERS_ADMIN'];?> </span>
						<span class="info-box-number"><?=($driver[0]['tot_driver']);?></span>
						</div> 
						</div></a> 
						</div>
						<div class="col-lg-3"><a href="company.php">
						<div class="info-box bg-box3">
						<span class="info-box-icon"><i class="fa fa-building-o"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text">Companies</span>
						<span class="info-box-number"><?=($company[0]['tot_company']);?></span>
						</div> 
						</div></a> 
						</div>
						
						<div class="col-lg-3"><a href="trip.php">
						<div class="info-box bg-box4">
						<span class="info-box-icon"><i class="fa fa-bar-chart"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text">Total Earnings</span>
						<span class="info-box-number"><?=number_format($totalEarns,2);?></span> 
						<span class="info-box-number"><?=$generalobj->trip_currency($totalEarns,'','',2);?></span>
						</div> 
						</div></a> 
						</div>
						
						
						
						<div class="col-lg-3"><a href="trip.php">
						<div class="info-box bg-box5">
						<span class="info-box-icon"><i class="fa fa-taxi"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_TOTAL_RIDES_ADMIN'];?> </span>
						<span class="info-box-number"><?=($totalRides[0]['tot_trip']);?></span>
						</div> 
						</div></a> 
						</div> 
						<div class="col-lg-3"><a href="trip.php?type=onRide">
						<div class="info-box bg-box6">
						<span class="info-box-icon"><i class="fa fa-taxi"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_ON_RIDES_ADMIN'];?> </span>
						<span class="info-box-number"><?=($onRides[0]['tot_trip']);?></span>
						</div> 
						</div></a> 
						</div>
						
						<div class="col-lg-3"><a href="trip.php?type=cancel">
						<div class="info-box bg-box7">
						<span class="info-box-icon"><i class="fa fa-times-circle-o"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_CANCELLED_RIDES_ADMIN'];?> </span>
						<span class="info-box-number"><?=($cancelRides[0]['tot_trip']);?></span>
						</div> 
						</div></a> 
						</div> 
						
						
						<div class="col-lg-3"><a href="trip.php?type=complete">
						<div class="info-box bg-box8">
						<span class="info-box-icon"><i class="fa fa-check"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text"><?php  echo $langage_lbl_admin['LBL_COMPLETED_RIDES_ADMIN'];?> </span>
						<span class="info-box-number"><?=($finishRides[0]['tot_trip']);?></span>
						</div> 
						</div></a> 
						</div>
						
						<div class="col-lg-3"><a href="trip.php">
						<div class="info-box bg-box9">
						<span class="info-box-icon"><i class="fa fa-usd"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text">Total Payment </span>
						<span class="info-box-number"></span>
						</div> 
						</div></a>  
						</div> 
						<div class="col-lg-3"><a href="trip.php?type=onRide">
						<div class="info-box bg-box10">
						<span class="info-box-icon"><i class="fa fa-file-text"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text">Commission </span>
						<span class="info-box-number"></span>
						</div> 
						</div></a> 
						</div>
						
						<div class="col-lg-3"><a href="trip.php?type=cancel">
						<div class="info-box bg-box11">
						<span class="info-box-icon"><i class="fa fa-cc"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text">Card Payment</span>
						<span class="info-box-number"></span>
						</div> 
						</div></a> 
						</div> 
						
						
						<div class="col-lg-3"><a href="trip.php?type=complete">
						<div class="info-box bg-box12">
						<span class="info-box-icon"><i class="fa fa-money"></i></span>
						
						<div class="info-box-content">
						<span class="info-box-text">Cash Payment </span>
						<span class="info-box-number"></span>
						</div> 
						</div></a> 
						</div>
						</div>
                        </div>
						</div>
						
						
						</div>
						
						<hr />
					-->
					<div class="row">
						<div class="col-lg-6">
							<div class="panel-heading">
								<div class="panel-title-box">
									<i class="fa fa-bar-chart"></i> <?php  echo $langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?> For last 3 Months
								</div>                                  
							</div>
							<div class="panel-body padding-0">
								<div id="last-6-rides"></div>
							</div>
						</div>
						
						<div class="col-lg-6">
							<div class="panel-heading">
								<div class="panel-title-box">
									<i class="fa fa-bar-chart"></i> Registered Users For last 3 Months
								</div>                                  
							</div>
							<div class="panel-body padding-0">
								<div id="total-users"></div>
							</div>
						</div>
						
					</div>
					
					<hr />
					
					
					
					
					
					
					<div class="row">
						<div class="col-lg-6">
							<div class="panel panel-primary bg-gray-light">
								
								<div class="panel-heading">
									<div class="panel-title-box">
										<i class="fa fa-bar-chart"></i> <?php  echo $langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>
									</div>                                  
								</div>
								
								
								
								<div class="panel-body padding-0">
									<div class="col-lg-6">
										<div class="chart-holder" id="dashboard-rides" style="height: 200px;"></div>
									</div>
									<div class="col-lg-6">
										<h3><?php  echo $langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>  Count : <?=(number_format($totalRides[0]['tot_trip']));?></h3>
										<p>Today : <b>
											<?php 
												$today = $generalobjDashboard->getTripDateStates('today');
												echo number_format($today);
											?></b></p>
											<p>This Month : <b><?=number_format($generalobjDashboard->getTripDateStates('month'));?></b></p>
											<p>This Year : <b><?=number_format($generalobjDashboard->getTripDateStates('year'));?></b></p>
											<br />
											<br />
											<p>
												* This is count for all <?=$langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?> (Finished, ongoing, cancelled.)
											</p>
									</div>
								</div>
							</div> 
						</div>
						
						<div class="col-lg-6">
							<div class="panel panel-primary bg-gray-light">
								<div class="panel-heading">
									<div class="panel-title-box">
										<i class="fa fa-bar-chart"></i> <?php  echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'];?>
									</div>                                  
								</div>
								<div class="panel-body padding-0">
									<div class="col-lg-6">
										<div class="chart-holder" id="dashboard-drivers" style="height: 200px;"></div>
									</div>
									<div class="col-lg-6">
										<h3><?php  echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'];?>  Count : <?=number_format($driver);?></h3>
										<p>Today : <b><?=number_format(count($generalobjAdmin->getDriverDateStatus('today')));?></b></p>
										<p>This Month : <b><?=number_format(count($generalobjAdmin->getDriverDateStatus('month')));?></b></p>
										<p>This Year : <b><?=number_format(count($generalobjAdmin->getDriverDateStatus('year')));?></b></p>
									</div>
								</div>
							</div> 
						</div>
					</div>
					
					<hr /> 
					
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-primary bg-gray-light">								
								
								<div class="panel-heading">
									<div class="panel-title-box">
										<i class="fa fa-bar-chart"></i> Total Generated fare for Last 6 months (In <?=$vDefaultName?>) 
									</div>                                  
								</div>
								
								
								<div class="panel-body padding-0">
									<div id="line-example"></div>
								</div>							
								
							</div>
							<!-- END VISITORS BLOCK -->
						</div>
						
					</div>
					
					
					<hr />
					<div class="row">
						<div class="col-lg-12">
							<div class="panel panel-primary bg-gray-light">
								
								
								
								
								<div class="panel-heading">
									<div class="panel-title-box">
										<i class="fa fa-bar-chart"></i> <?php  echo $langage_lbl_admin['LBL_DRIVER_NAME_ADMIN'];?> Log Report Of Current Month (In Hours)
									</div>                                  
								</div>
								
								
								<div class="panel-body padding-0">
									<div id="driver-log"></div>
								</div>							
								
							</div>
							<!-- END VISITORS BLOCK -->
						</div>
						
					</div>
					
				</div>
			</div>
			
			<!--END PAGE CONTENT -->
		</div>
		
		<?php  include_once('footer.php'); ?>
		
	</body>
	<!-- END BODY-->
	<?php 
		// if(SITE_TYPE=='Demo'){
		// $generalobjAdmin->remove_unwanted();
		// }
	?>
</html>
<script>
	$(document).ready(function(){
		/* Donut dashboard chart */
		var total_ride = '<?=$totalRides[0]['tot_trip'];?>';
		var complete_ride = '<?=$finishRides[0]['tot_trip'];?>';
		var cancel_ride = '<?=$cancelRides[0]['tot_trip'];?>';
		var on_ride = '<?=$onRides[0]['tot_trip'];?>';
		
		if(complete_ride > 0 || cancel_ride > 0 || total_ride > 0 ) 
		{
			Morris.Donut({
				
				element: 'dashboard-rides',
				
				data: [
				
				{label: "On Going <?=$langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>", value: on_ride},
				
				{label: "Completed <?=$langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>", value: complete_ride},
				
				{label: "Cancelled <?=$langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>", value: cancel_ride}
				
				],
				
				formatter: function (x) { return (x/total_ride *100).toFixed(2)+'%'+ ' ('+x+')'; },
				
				colors: ['#ee3324', '#f39c12', '#2baab1'],
				
				resize: true
				
			});
			} else {
			Morris.Donut({
				
				element: 'dashboard-rides',
				
				data: [
				
				{label: "On Going <?=$langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>", value: on_ride},
				
				{label: "Completed <?=$langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>", value: complete_ride},
				
				{label: "Cancelled <?=$langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>", value: cancel_ride}
				
				],
				
				formatter: function (x) { return (0)+'%'+ ' ('+x+')'; },
				
				colors: ['#ee3324', '#f39c12', '#2baab1'],
				
				resize: true
				
			});
		}
		
		var active_drive = '<?=$actDrive[0]['tot_driver'];?>';
		var inactive_drive = '<?=$inaDrive[0]['tot_driver'];?>';		
		var total_drive = '<?=$driver;?>';
		
		Morris.Donut({
			element: 'dashboard-drivers',
			data: [
			{label: "Active <?=$langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'];?>", value: active_drive},
			{label: "Pending <?=$langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'];?>", value: inactive_drive}
			],
			formatter: function (x) { return (x/total_drive *100).toFixed(2)+'%'+ '('+x+')'; },
			colors: ['#ee3324', '#f39c12'], 
			resize: true
		});
		/* END Donut dashboard chart  '#2baab1'*/
	});
</script>



<script>
	$(document).ready(function(){
		/* Donut chart */
		Morris.Bar({
			element: 'last-6-rides',
			data: [
			{ y: '<?=date('M - y', strtotime(date('Y-m')." -2 month"));?>', a: <?=($finishRides_1[0]['tot_trip'])?>, b: <?=($cancelledRides_1[0]['tot_trip'])?>},
			{ y: '<?=date('M - y', strtotime(date('Y-m')." -1 month"));?>', a: <?=($finishRides_2[0]['tot_trip'])?>, b: <?=($cancelledRides_2[0]['tot_trip'])?>},
			{ y: '<?=date('M - y', strtotime(date('Y-m').""));?>', a: <?=($finishRides_3[0]['tot_trip'])?>, b: <?=($cancelledRides_3[0]['tot_trip'])?>},
			
			],
			xkey: 'y',
			ykeys: ['a','b'],
			barColors: ['#0088cc', '#e36159'],
			gridTextColor: '#ee3324',
			labels: ['Finished <?=$langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>','Cancelled <?=$langage_lbl_admin['LBL_RIDES_NAME_ADMIN'];?>']
		});
		/* END Donut chart */
		
		/* Donut chart */
		Morris.Bar({
			element: 'total-users',
			data: [
				{ yy: '<?=date('M - y', strtotime(date('Y-m')." -2 month"));?>', aa: <?=$driver_2[0]['TotalDriver'];?>, bb: <?=$pass_2[0]['TotalRider'];?>},
				
				{ yy: '<?=date('M - y', strtotime(date('Y-m')." -1 month"));?>', aa: <?=$driver_1[0]['TotalDriver1'];?>,  bb: <?=$pass_1[0]['TotalRider1'];?> },
				{ yy: '<?=date('M - y', strtotime(date('Y-m').""));?>', aa: <?=$driver_0[0]['TotalDriver2'];?>,  bb: <?=$pass_0[0]['TotalRider2'];?> },
			],
			xkey: 'yy',
			ykeys: ['aa', 'bb'],
			barColors: ['#ee3324', '#2baab1'],
			gridTextColor: '#ee3324',
			labels: ['<?=$langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'];?>', '<?=$langage_lbl_admin['LBL_DASHBOARD_USERS_ADMIN'];?>']
		});
		/* END Donut chart */
		
		Morris.Bar({
			element: 'line-example',
			data: [
			{ y: '<?=date('M - y', strtotime(date('Y-m')." -5 month"));?>', a: <?=$fTripGenerateFare5?>, b: <?=$fDiscount5?> , c: <?=$fWalletDebit5?>, d : <?=$iFare5?> , e : <?=$Cash5?>},
			{ y: '<?=date('M - y', strtotime(date('Y-m')." -4 month"));?>', a: <?=$fTripGenerateFare4?>, b: <?=$fDiscount4?> , c: <?=$fWalletDebit4?>, d : <?=$iFare4?>  , e : <?=$Cash4?>},
			{ y: '<?=date('M - y', strtotime(date('Y-m')." -3 month"));?>', a: <?=$fTripGenerateFare3?>, b: <?=$fDiscount3?> , c: <?=$fWalletDebit3?> , d : <?=$iFare3?>  , e : <?=$Cash3?>},
			{ y: '<?=date('M - y', strtotime(date('Y-m')." -2 month"));?>', a: <?=$fTripGenerateFare2?>, b: <?=$fDiscount2?> , c: <?=$fWalletDebit2?> , d : <?=$iFare2?>  , e : <?=$Cash2?>},
			{ y: '<?=date('M - y', strtotime(date('Y-m')." -1 month"));?>', a: <?=$fTripGenerateFare1?>,  b: <?=$fDiscount1?> , c: <?=$fWalletDebit1?> , d : <?=$iFare1?> , e : <?=$Cash1?> },
			{ y: '<?=date('M - y', strtotime(date('Y-m').""));?>', a: <?=$fTripGenerateFare0?>,  b: <?=$fDiscount0?> , c: <?=$fWalletDebit0?> , d : <?=$iFare0?> , e : <?=$Cash0?>}
			],
			xkey: 'y',
			gridTextColor: '#000000',
			ykeys: ['a', 'b','c','d','e'],
			gridTextColor: '#ee3324',
			barColors: ['#ee3324', '#2baab1', '#8fa928', '#0088cc','#f39c12'],
			labels: ['Generated Fare', 'Discount','Wallet','Paid By Passenger','Paid In Cash']
		});
		
		
		Morris.Bar({
			element: 'driver-log',
			data: [
			//totalTimecount
		<?php  for($i = 0; $i < 5; $i ++ ) {
			if(isset($tmp_log_report[$i]['Name'])) { ?>
			{ y: '<?=$generalobjAdmin->clearName($tmp_log_report[$i]['Name'])?>', a: '<?= secToHR($tmp_log_report[$i]['totalHourTimecount']);?>'},
		<?php  } } ?>
			
			],
			xkey: 'y',
			gridTextColor: '#ee3324',
			ykeys: ['a'],
			barColors: ['#2baab1'],
			labels: ['Hours']
		});
		
		/*Morris.Bar({
			element: 'driver-log',
			data: [
			//totalTimecount
		<?php  for($i = 0; $i < 5; $i ++ ) {
			if(isset($tmp_log_report[$i]['Name'])) { ?>
			{ y: '<?=$generalobjAdmin->clearName($tmp_log_report[$i]['Name'])?>', a: <?=$tmp_log_report[$i]['totalTimecount']?>},
		<?php  } } ?>
			
			],
			xkey: 'y',
			gridTextColor: '#ee3324',
			ykeys: ['a'],
			barColors: ['#2baab1'],
			labels: ['Minutes']
		});*/
	});
</script>
