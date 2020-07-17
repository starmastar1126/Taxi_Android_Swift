<?php 
include_once('common.php');
include_once('generalFunctions.php');
$tbl_name 	= 'register_user';
$script="Trips";
$generalobj->check_member_login();
$abc = 'admin,rider';
$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$generalobj->setRole($abc,$url);
$action=(isset($_REQUEST['action'])?$_REQUEST['action']:'');
$ssql='';
if($action!='')
{
	$startDate=$_REQUEST['startDate'];
	$endDate=$_REQUEST['endDate'];
	if($startDate!=''){
		$ssql.=" AND Date(t.tTripRequestDate) >='".$startDate."'";
	}
	if($endDate!=''){
		$ssql.=" AND Date(t.tTripRequestDate) <='".$endDate."'";
	}
}

if($_SESSION['sess_user']== "driver")
{
  $sql = "SELECT * FROM register_".$_SESSION['sess_user']." WHERE iDriverId='".$_SESSION['sess_iUserId']."'";
  $db_booking = $obj->MySQLSelect($sql);

  $sql = "SELECT fThresholdAmount, Ratio, vName, vSymbol FROM currency WHERE vName='".$db_booking[0]['vCurrencyDriver']."'";
  $db_curr_ratio = $obj->MySQLSelect($sql);
}
else
{
  $sql = "SELECT * FROM register_user WHERE iUserId='".$_SESSION['sess_iUserId']."'";
  $db_booking = $obj->MySQLSelect($sql);  
  $sql = "SELECT fThresholdAmount, Ratio, vName, vSymbol FROM currency WHERE vName='".$db_booking[0]['vCurrencyPassenger']."'";
  $db_curr_ratio = $obj->MySQLSelect($sql);
}
$tripcursymbol=$db_curr_ratio[0]['vSymbol'];
$tripcur=$db_curr_ratio[0]['Ratio'];
$tripcurname=$db_curr_ratio[0]['vName'];
$tripcurthholsamt=$db_curr_ratio[0]['fThresholdAmount'];

	/*
	$sql = "SELECT t.*, t.tEndDate,t.iActive, t.iFare,t.fRatioPassenger,t.vCurrencyPassenger, d.iDriverId, t.vRideNo, t.tSaddress,t.eType, d.vName AS name, d.vLastName AS lname,t.eCarType,t.iTripId,vt.vVehicleType
	FROM register_user u
	RIGHT JOIN trips t ON u.iUserId = t.iUserId
	LEFT JOIN register_driver d ON t.iDriverId = d.iDriverId
	LEFT JOIN vehicle_type vt ON vt.iVehicleTypeId = t.iVehicleTypeId
	WHERE u.iUserId = '".$_SESSION['sess_iUserId']."'".$ssql." ORDER BY t.iTripId DESC";
	*/
	 $sql = "SELECT u.iUserId, t.*, t.tEndDate, t.tTripRequestDate, t.iActive, t.iFare,t.fRatioPassenger,t.vCurrencyPassenger, d.iDriverId, t.vRideNo, t.tSaddress,t.eType, d.vName AS name, d.vLastName AS lname,t.eCarType,t.iTripId,vt.vVehicleType_".$_SESSION['sess_lang']."
	FROM trips as t LEFT JOIN  register_user u ON t.iUserId = u.iUserId LEFT JOIN register_driver d ON t.iDriverId = d.iDriverId LEFT JOIN vehicle_type vt ON vt.iVehicleTypeId = t.iVehicleTypeId WHERE u.iUserId = '".$_SESSION['sess_iUserId']."'".$ssql." ORDER BY t.iTripId DESC";
	
	$db_trip = $obj->MySQLSelect($sql);
	$sql="select vName from currency where eDefault='Yes'";
	$db_currency=$obj->MySQLSelect($sql);
	

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
	
if($host_system == 'cubetaxiplus') {
  $canceled_icon = "canceled-invoice.png";
  $invoice_icon = "driver-view-icon.png";
} else if($host_system == 'ufxforall') {
  $canceled_icon = "ufxforall-canceled-invoice.png";
  $invoice_icon = "ufxforall-driver-view-icon.png";
} else if($host_system == 'uberridedelivery4') {
  $canceled_icon = "ride-delivery-canceled-invoice.png";
  $invoice_icon = "ride-delivery-driver-view-icon.png";
} else if($host_system == 'uberdelivery4') {
  $canceled_icon = "delivery-canceled-invoice.png";
  $invoice_icon = "delivery-driver-view-icon.png";
} else {
  $invoice_icon = "driver-view-icon.png";
  $canceled_icon = "canceled-invoice.png";
}
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$SITE_NAME?> |<?=$langage_lbl['LBL_HEADER_TRIPS_TXT']; ?></title>
	
    <!-- Default Top Script and css -->
    <?php  include_once("top/top_script.php");
	$rtls = "";
	if($lang_ltr == "yes") {
		$rtls = "dir='rtl'";
	}
	?>
   
    <!-- <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" /> -->
    <!-- End: Default Top Script and css-->
</head>
<body>
    <!-- home page -->
    <div id="main-uber-page">
    <!-- Left Menu -->
    <?php  include_once("top/left_menu.php");?>
    <!-- End: Left Menu-->
        <!-- Top Menu -->
        <?php  include_once("top/header_topbar.php");?>
        <!-- End: Top Menu-->
        <!-- contact page-->
		<div class="page-contant">
			<div class="page-contant-inner">
			  	<h2 class="header-page"><?=$langage_lbl['LBL_MYTRIP_TRIPS']; ?></h2>
		  		<!-- trips page -->
			  	<div class="trips-page">
			  		<form name="search" action="" method="post" onSubmit="return checkvalid()">
			  			<div class="Posted-date mytrip-page">
							<input type="hidden" name="action" value="search" />
				      		<h3><?=$langage_lbl['LBL_SEARCH_RIDES_POSTED_BY_DATE']; ?></h3>
							<span>
                            <a onClick="return todayDate('dp4','dp5');"><?=$langage_lbl['LBL_Today']; ?></a>
                            <a onClick="return yesterdayDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Yesterday']; ?></a>
                            <a onClick="return currentweekDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Current_Week']; ?></a>
                            <a onClick="return previousweekDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Previous_Week']; ?></a>
                            <a onClick="return currentmonthDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Current_Month']; ?></a>
                            <a onClick="return previousmonthDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Previous Month']; ?></a>
                            <a onClick="return currentyearDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Current_Year']; ?></a>
                            <a onClick="return previousyearDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Previous_Year']; ?></a>
				      		</span> 
				      		<span>

                            <input type="text" id="dp4" name="startDate" placeholder="<?=$langage_lbl['LBL_MYTRIP_FROM_DATE']; ?>" class="form-control" value=""/>
                            <input type="text" id="dp5" name="endDate" placeholder="<?=$langage_lbl['LBL_MYTRIP_TO_DATE']; ?>" class="form-control" value=""/>
                            <b><button class="driver-trip-btn"><?=$langage_lbl['LBL_MYTRIP_Search']; ?></button>
								<button onClick="reset();" class="driver-trip-btn"><?=$langage_lbl['LBL_MYTRIP_RESET']; ?></button></b> 
					      	</span>
				      	</div>
		      		</form>
			    	<div class="trips-table"> 
			    	
			      		<div class="trips-table-inner">
			      		<div class="driver-trip-table">
			        		<table width="100%" border="0" cellpadding="0" cellspacing="1" id="dataTables-example" <?php  echo $rtls; ?>>
			          			<thead>
        							<tr>
										<th width="17%"><?=$langage_lbl['LBL_MYTRIP_RIDE_NO']; ?></th>
										<th width="18%"><?=$langage_lbl['LBL_MYTRIP_DRIVER']; ?></th>
										<th width="15%"><?=$langage_lbl['LBL_MYTRIP_Trip_Date']; ?></th>
										<th width="15%"><?=$langage_lbl['LBL_Your_Fare']; ?></th>
										<th width="15%"><?=$langage_lbl['LBL_MYTRIP_Car']; ?></th>
										<th width="16%"><?=$langage_lbl['LBL_MYTRIP_View_Invoice']; ?></th>
									</tr>
								</thead>
								<tbody>
								<?php 
									for($i=0;$i<count($db_trip);$i++)
									{
										$pickup = $db_trip[$i]['tSaddress'];
										$driver = $generalobj->clearName($db_trip[$i]['name'].' '.$db_trip[$i]['lname']);
										if($db_trip[$i]['fCancellationFare'] > 0){
											$fare = $generalobj->trip_currency_payment($db_trip[$i]['fCancellationFare'],$db_trip[$i]['fRatio_'.$tripcurname]);
										} else {

											$fare = $generalobj->trip_currency_payment($db_trip[$i]['iFare'],$db_trip[$i]['fRatio_'.$tripcurname]);
										}
										$car = $db_trip[$i]['vVehicleType_'.$_SESSION['sess_lang']];
										$eType = $db_trip[$i]['eType'];
										if($eType == 'Ride'){
											$trip_type = 'Ride';
										} else if($eType == 'UberX') {
											$trip_type = 'Other Services';
										} else {
											$trip_type = 'Delivery';
										}
										//$trip_type = ($eType == 'Ride')? 'Ride': 'Delivery';
										$systemTimeZone = date_default_timezone_get();
										if($db_trip[$i]['tTripRequestDate']!= "" && $db_trip[$i]['vTimeZone'] != "")  {
			                                $dBookingDate = converToTz($db_trip[$i]['tTripRequestDate'],$db_trip[$i]['vTimeZone'],$systemTimeZone);
			                            } else {
			                                $dBookingDate = $db_trip[$i]['tTripRequestDate'];
			                            }
								?>
									<tr class="gradeA">
										<td align="center"  data-order="<?=$db_trip[$i]['iTripId']?>"><?=$db_trip[$i]['vRideNo'];?></td>
										<td>
											<?php  if($driver==''){echo '--';}else{echo $driver;}?>
										</td>
										<td align="center"><?= $generalobj->DateTime1($dBookingDate,'no');?></td>
										<td align="right" class="center">
											<?php  if($db_trip[$i]['iActive']=='Canceled'){
												echo $tripcursymbol.' '.$fare;
												}else{
													echo $tripcursymbol.' '.$fare ;
												}?>
										</td>
										<td align="center" class="center">
											<?php  if($car==''){echo '--';}else{echo $car;}?>
										</td>
										<?php  if($db_trip[$i]['iActive'] == 'Canceled') {?>
										<td class="center">
											<img src="assets/img/<?php  echo $canceled_icon;?>" title="<?=$langage_lbl['LBL_MYTRIP_CANCELED_TXT']; ?>">
										</td>
										<?php  } else if(($db_trip[$i]['iActive'] == 'Finished' && $db_trip[$i]['eCancelled'] == "Yes") || ($db_trip[$i]['fCancellationFare'] > 0)) {?>
											<td align="center" width="10%">
											  	<a  target = "_blank" href="invoice.php?iTripId=<?=base64_encode(base64_encode($db_trip[$i]['iTripId']))?>"><strong><img src="assets/img/<?php  echo $invoice_icon;?>"></strong></a>
												<span>Cancelled</span>
											</td>
									<?php  } else {?>	
										<td class="center">
											<a  target = "_blank" href="invoice.php?iTripId=<?=base64_encode(base64_encode($db_trip[$i]['iTripId']))?>"><strong><img src="assets/img/<?php  echo $invoice_icon;?>"></strong></a>
										</td>
										<?php  } ?>
									</tr>
								<?php  } ?>
								</tbody>
			        		</table>
			      		</div></div>
			   
			    </div>
			    <!-- -->
			    <!-- <?php  //if(SITE_TYPE=="Demo"){?>
			    <div class="record-feature"> <span><strong>“Edit / Delete Record Feature”</strong> has been disabled on the Demo Admin Version you are viewing now.
			      This feature will be enabled in the main product we will provide you.</span> </div>
			      <?php  //}?> -->
			    <!-- -->
			  </div>
			  <!-- -->
			  <div style="clear:both;"></div>
			</div>
		</div>
    <!-- footer part -->
    <?php  include_once('footer/footer_home.php');?>
    <!-- footer part end -->
    <!-- End:contact page-->
    <div style="clear:both;"></div>
    </div>
    <!-- home page end-->
    <!-- Footer Script -->
    <?php  include_once('top/footer_script.php');?>
    <script src="assets/js/jquery-ui.min.js"></script>
    <script src="assets/plugins/dataTables/jquery.dataTables.js"></script>
    <script type="text/javascript">
         $(document).ready(function () {
         	$( "#dp4" ).datepicker({
         		dateFormat: "yy-mm-dd",
         		changeYear: true,
     		  	changeMonth: true,
     		  	yearRange: "-100:+10"
         	});
         	$( "#dp5" ).datepicker({
         		dateFormat: "yy-mm-dd",
         		changeYear: true,
     		  	changeMonth: true,
     		  	yearRange: "-100:+10"
         	});
			 if('<?=$startDate?>'!=''){
				 $("#dp4").val('<?=$startDate?>');
				 $("#dp4").datepicker('refresh');
			 }
			 if('<?=$endDate?>'!=''){
				 $("#dp5").val('<?= $endDate;?>');
				 $("#dp5").datepicker('refresh');
			 }
             $('#dataTables-example').dataTable({
			  "order": [[ 0, "desc" ]],
			  "aoColumns": [
			      null,
			      null,
			      null,
			      null,
			      null,
			      { "bSortable": false }
			    ]
			 });
			// formInit();
         });
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
			 $("#dp5").val('<?= $Yesterday;?>');
			 $("#dp4").datepicker('refresh');
			 $("#dp5").datepicker('refresh');			 
		 }
		 function currentweekDate(dt,df)
		 {
			 $("#dp4").val('<?= $monday;?>');			 
			 $("#dp5").val('<?= $sunday;?>');
			 $("#dp4").datepicker('refresh');
			 $("#dp5").datepicker('refresh');
		 }
		 function previousweekDate(dt,df)
		 {
			 $("#dp4").val('<?= $Pmonday;?>');
			 $("#dp5").val('<?= $Psunday;?>');
			 $("#dp4").datepicker('refresh');
			 $("#dp5").datepicker('refresh');
		 }
		 function currentmonthDate(dt,df)
		 {
			 $("#dp4").val('<?= $currmonthFDate;?>');
			 $("#dp5").val('<?= $currmonthTDate;?>');
			 $("#dp4").datepicker('refresh');
			 $("#dp5").datepicker('refresh');
		 }
		 function previousmonthDate(dt,df)
		 {
			 $("#dp4").val('<?= $prevmonthFDate;?>');
			 $("#dp5").val('<?= $prevmonthTDate;?>');
			 $("#dp4").datepicker('refresh');
			 $("#dp5").datepicker('refresh');
		 }
		 function currentyearDate(dt,df)
		 {
			 $("#dp4").val('<?= $curryearFDate;?>');
			 $("#dp5").val('<?= $curryearTDate;?>');
			 $("#dp4").datepicker('refresh');
			 $("#dp5").datepicker('refresh');
		 }
		 function previousyearDate(dt,df)
		 {
			 $("#dp4").val('<?= $prevyearFDate;?>');
			 $("#dp5").val('<?= $prevyearTDate;?>');
			 $("#dp4").datepicker('refresh');
			 $("#dp5").datepicker('refresh');
		 }
	 	function checkvalid(){
			 if($("#dp5").val() < $("#dp4").val()){
				 //bootbox.alert("<h4>From date should be lesser than To date.</h4>");
			 	bootbox.dialog({
				 	message: "<h4><?php  echo addslashes($langage_lbl['LBL_FROM_TO_DATE_ERROR_MSG']);?></h4>",
				 	buttons: {
				 		danger: {
				      		label: "OK",
				      		className: "btn-danger"
				   	 	}
			   	 	}
		   	 	});
			 	return false;
		 	}
	 	}
    </script>
    
    <script type="text/javascript">
    $(document).ready(function(){
        $("[name='dataTables-example_length']").each(function(){
            $(this).wrap("<em class='select-wrapper'></em>");
            $(this).after("<em class='holder'></em>");
        });
        $("[name='dataTables-example_length']").change(function(){
            var selectedOption = $(this).find(":selected").text();
            $(this).next(".holder").text(selectedOption);
        }).trigger('change');
    })
</script>
    <!-- End: Footer Script -->
</body>
</html>