<?php  
include_once('common.php');
include_once('generalFunctions.php');
if($WALLET_ENABLE == "No")
{
	header('Location: index.php'); exit;
}
	
$tbl_name 	= 'user_wallet';
$script="Rider Wallet";
$generalobj->check_member_login();
	
// $abc = 'admin,rider';
$abc = 'admin,driver,company';
$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//$generalobj->setRole($abc,$url);
$action=(isset($_REQUEST['action'])?$_REQUEST['action']:'');
$type=(isset($_REQUEST['type'])?$_REQUEST['type']:'');
$ssql='';
if($action!='')
{
	$startDate=$_REQUEST['startDate'];
	$endDate=$_REQUEST['endDate'];
	if($startDate!=''){
		$ssql.=" AND DATE(u.dDate) >='".$startDate."'";
	}
	if($endDate!=''){
		$ssql.=" AND DATE(u.dDate) <='".$endDate."'";
	}
}
// $sql = "SELECT u.vName, u.vLastName,t.tEndDate, t.iFare,t.fRatioPassenger,t.vCurrencyPassenger, d.iDriverId, t.vRideNo, t.tSaddress, d.vName AS name, d.vLastName AS lname,t.eCarType,t.iTripId,vt.vVehicleType
// FROM register_user u
// RIGHT JOIN trips t ON u.iUserId = t.iUserId
// LEFT JOIN register_driver d ON t.iDriverId = d.iDriverId
// LEFT JOIN vehicle_type vt ON vt.iVehicleTypeId = t.iVehicleTypeId
// WHERE u.iUserId = '".$_SESSION['sess_iUserId']."'".$ssql." ORDER BY t.iTripId DESC";

/*for Withdrawal Money Bank Details*/
#echo "type = ".$type;
if($type == 'Driver')
{
	$sql = "SELECT * from register_driver where iDriverId='".$_SESSION['sess_iUserId']."'";	
	$db_driver = $obj->MySQLSelect($sql);
}
/*for Withdrawal Money Bank Details end*/

$sql = "SELECT u.*,ru.vTimeZone from user_wallet as u LEFT JOIN register_user as ru on ru.iUserId=u.iUserId where u.iUserId='".$_SESSION['sess_iUserId']."' AND u.eUserType = '".$type."' ".$ssql." ORDER BY u.iUserWalletId ASC";	

$db_trip = $obj->MySQLSelect($sql);

$user_available_balance = $generalobj->get_user_available_balance($_SESSION['sess_iUserId'],$type);
//$user_available_balance = get_user_available_balance($_SESSION['sess_iUserId'],$type);

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
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_WALLET_RIDER_WALLET']; ?></title>
    <!-- Default Top Script and css -->
    <?php  include_once("top/top_script.php");
	$rtls = "";
	if($lang_ltr == "yes") {
		$rtls = "dir='rtl'";
		//$direction_lng = "dir='rtl'";
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
			  	<h2 class="header-page"><?=$langage_lbl['LBL_WALLET_RIDER_WALLET']; ?></h2>
		  		<!-- trips page -->
			  	<div class="trips-page wallet-page">
			  		<form name="search" action="" method="post" onSubmit="return checkvalid()">
			  			<input type="hidden" name="action" value="search" />
				    	<div class="Posted-date">
				      		<h3><?=$langage_lbl['LBL_SEARCH_TRANSACTION_BY_DATE']; ?></h3>
				      		<span>
				      			<input type="text" id="dp4" name="startDate" placeholder="<?=$langage_lbl['LBL_WALLET_FROM_DATE'];?>" class="form-control" value=""/>
				      			<input type="text" id="dp5" name="endDate" placeholder="<?=$langage_lbl['LBL_WALLET_TO_DATE'];?>" class="form-control" value=""/>
					      	</span>
				      	</div>
				    	<div class="time-period">
				      		<h3><?=$langage_lbl['LBL_SEARCH_TRANSACTION_BY_DATE']; ?></h3>
				      		<span>
								<a onClick="return todayDate('dp4','dp5');"><?=$langage_lbl['LBL_Wallet_Today']; ?></a>
								<a onClick="return yesterdayDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Wallet_Yesterday']; ?></a>
								<a onClick="return currentweekDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Wallet_Current_Week']; ?></a>
								<a onClick="return previousweekDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Wallet_Previous_Week']; ?></a>
								<a onClick="return currentmonthDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Wallet_Current_Month']; ?></a>
								<a onClick="return previousmonthDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Wallet_Previous Month']; ?></a>
								<a onClick="return currentyearDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Wallet_Current_Year']; ?></a>
								<a onClick="return previousyearDate('dFDate','dTDate');"><?=$langage_lbl['LBL_Wallet_Previous_Year']; ?></a>
				      		</span> 
				      		<b><button class="driver-trip-btn"><?=$langage_lbl['LBL_Wallet_Search']; ?></button><button onClick="reset();" class="driver-trip-btn"><?=$langage_lbl['LBL_MYTRIP_RESET']; ?></button></b>
			      		</div>
		      		</form>
			    	<div class="trips-table">
			      		<div class="trips-table-inner">
			      		<div class="driver-trip-table">
			        		<table width="100%" border="0" cellpadding="0" cellspacing="1" id="dataTables-example" <?php  echo $rtls; ?>>
			          			<thead>
									<tr>
	        						<th width="20%"><?=$langage_lbl['LBL_DESCRIPTION']; ?></th>
										<th width="15%"><?=$langage_lbl['LBL_AMOUNT']; ?></th>
										<!-- <th width="15%"><?=$langage_lbl['LBL_WALLET_TRIP_NO']; ?></th> -->
										<th width="10%"><?=$langage_lbl['LBL_BALANCE_FOR']; ?></th>
										<th width="15%"><?=$langage_lbl['LBL_TRANSACTION_DATE']; ?></th>
										<th width="20%"><?=$langage_lbl['LBL_BALANCE_TYPE']; ?></th>
										<th width="10%"><?=$langage_lbl['LBL_BALANCE']; ?></th>
									</tr>
								</thead>
								<tbody>
								<?php 
									if(count($db_trip) > 0){
     								$prevbalance = 0;
									for($i=0;$i<count($db_trip);$i++)
									{
										echo $direction_lng;
										$tDescription = $db_trip[$i]['tDescription'];
										$iBalance = $db_trip[$i]['iBalance'];
										$iTripId = $db_trip[$i]['iTripId'];
										if($iTripId == "0")
											$iTripId = "-";


										if($db_trip[$i]['eFor'] == "Deposit"){
											$eFor = $langage_lbl['LBL_DEPOSIT'];
										} else if($db_trip[$i]['eFor'] == "Booking"){
											$eFor = $langage_lbl['LBL_BOOKING'];
										} else if($db_trip[$i]['eFor'] == "Refund"){
											$eFor = $langage_lbl['LBL_REFUND'];
										} else if($db_trip[$i]['eFor'] == "Withdrawl"){
											$eFor = $langage_lbl['LBL_WITHDRAWL'];
										} else if($db_trip[$i]['eFor'] == "Charges"){
											$eFor = $langage_lbl['LBL_CHARGES_TXT'];
										}else if($db_trip[$i]['eFor'] == "Referrer"){
											$eFor = $langage_lbl['LBL_DEPOSIT'];
										}

										if($db_trip[$i]['eType'] == "Credit"){
											$eType = $langage_lbl['LBL_CREDIT'];
										} else if($db_trip[$i]['eType'] == "Debit") {
											$eType = $langage_lbl['LBL_DEBIT'];
										}

										$systemTimeZone = date_default_timezone_get();
										if($db_trip[$i]['dDate']!= "" && $db_trip[$i]['vTimeZone'] != "")  {
									        $dBookingDate = converToTz($db_trip[$i]['dDate'],$db_trip[$i]['vTimeZone'],$systemTimeZone);
									    } else {
									        $dBookingDate = $db_trip[$i]['dDate'];
									    }
										//  if($direction_lng != ''){
											//$dDate = date('M-d-Y',strtotime($db_trip[$i]['dDate']));
										 //}else{
										$dDate = $generalobj->DateTime1($dBookingDate,'no');
										// }											 
										
										if($db_trip[$i]['eType'] == "Credit"){
									       $db_trip[$i]['currentbal'] = $prevbalance+$db_trip[$i]['iBalance'];
									    }else{
									       $db_trip[$i]['currentbal'] = $prevbalance-$db_trip[$i]['iBalance'];
									    }
										$prevbalance = $db_trip[$i]['currentbal'];

								?>
									<tr class="gradeA">
										<td align="center" data-order="<?=$db_trip[$i]['iUserWalletId']?>">
											<?php   $pat = '/\#([^\"]*?)\#/';
                                           preg_match($pat, $db_trip[$i]['tDescription'], $tDescription_value);
                                           $tDescription_translate = $langage_lbl[$tDescription_value[1]];
                                           $row_tDescription = str_replace($tDescription_value[0], $tDescription_translate, $db_trip[$i]['tDescription']);
                                            echo $row_tDescription;?>
										</td>
										<td align="right" class="center"><?=$generalobj->userwalletcurrency($db_trip[$i]['fRatio_'.$_SESSION["sess_vCurrency"]],$iBalance,$_SESSION["sess_vCurrency"]);?></td>
										<!-- <td align="right" class="center"><?=$iTripId;?></td> -->
										<td align="right" class="center"><?=$eFor;?></td>
										<td align="right" class="center"><?=$dDate;?></td>
										<td align="right" class="center"><?=$eType;?></td>
										<td class="center"><?=$final = $generalobj->userwalletcurrency($db_trip[$i]['fRatio_'.$_SESSION["sess_vCurrency"]],$db_trip[$i]['currentbal'],$_SESSION["sess_vCurrency"]);?>
										</td>
									</tr>
								<?php  }?>
								</tbody>
								<tfoot>
									<tr class="gradeA odd ">
										 <td class="last_record_row" style="border-right:0px;"></td>
										 <td></td>
										<td></td>
										 <!-- <td></td> -->
										 <td></td>
										 <td rowspan="1" colspan="1" align="right" style="font-weight:bold;text-align:right;"><?=$langage_lbl['LBL_WALLET_TOTAL_BALANCE']; ?></td>
          								 <td rowspan="1" colspan="1" align="center" class="center"><?=$final;?></td>
									</tr>
								</tfoot>	
								<?php  }else{ ?>
								   <!--  <tr class="odd">
								    	<td class="center" align="center" colspan="7">No Details found</td>
								    </tr>	 -->
								<?php }?>
								</tbody>
			        		</table>
			      		</div>
					</div>			   
			    </div>
			    <div class="singlerow-login-log">
			    	<a href="javascript:void(0);" data-toggle="modal" data-target="#uiModal"><?=$langage_lbl['LBL_WITHDRAW_REQUEST']; ?></a>&nbsp;&nbsp;&nbsp;
			    	<!-- <a href="javascript:void(0); onClick=add_money_to_wallet();"><?=$langage_lbl['LBL_ADD_MONEY']; ?></a> -->
			    	
			    	<!--<a href="javascript:void(0);" data-toggle="modal" data-target="#uiModal1"><?=$langage_lbl['LBL_ADD_MONEY']; ?></a>-->
			    </div>
                        
			    <!-- -->
			    <?php  //if(SITE_TYPE=="Demo"){?>
			    <!-- <div class="record-feature"> <span><strong>“Edit / Delete Record Feature”</strong> has been disabled on the Demo Admin Version you are viewing now.
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
     <div style="clear:both;"></div>
    <!-- End:contact page-->
    </div>
    <!-- home page end-->
    <!-- Footer Script -->
    <?php  include_once('top/footer_script.php');?>

</div>
<div class="col-lg-12">
    <div class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-content image-upload-1 popup-box1">
            <div class="upload-content">
                <h4><?=$langage_lbl['LBL_WITHDRAW_REQUEST'];?></h4>
                <form class="form-horizontal" id="frm6" method="post" enctype="multipart/form-data" name="frm6">
                   <input type="hidden" id="action" name="action" value="send_equest">
                   <input type="hidden"  name="eTransRequest" id="eTransRequest" value="">
                   <input type="hidden"  name="iUserId" id="iUserId" value="<?=$_SESSION['sess_iUserId'];?>">
                   <input type="hidden"  name="eUserType" id="eUserType" value="<?=$type;?>">
                   <input type="hidden"  name="User_Available_Balance" id="User_Available_Balance" value="<?=$user_available_balance;?>">
                    
                    <div class="col-lg-13">
                    
					<div class="input-group input-append" >
							<h5><?=$langage_lbl['LBL_WALLET_ACCOUNT_HOLDER_NAME']; ?></h5>
                            <input type="text" name="vHolderName" id="vHolderName" class="form-control vHolderName"  <?php if($type == 'Driver'){?>value="<?=$db_driver[0]['vBankAccountHolderName'];?>"<?php }?>>
							<h5><?=$langage_lbl['LBL_WALLET_NAME_OF_BANK']; ?></h5>
                            <input type="text" name="vBankName" id="vBankName" class="form-control vBankName" <?php if($type == 'Driver'){?>value="<?=$db_driver[0]['vBankName'];?>"<?php }?>>
							<h5><?=$langage_lbl['LBL_WALLET_ACCOUNT_NUMBER']; ?></h5>
                            <input type="text" name="iBankAccountNo" id="iBankAccountNo" class="form-control iBankAccountNo" <?php if($type == 'Driver'){?>value="<?=$db_driver[0]['vAccountNumber'];?>"<?php }?>>
							<h5><?=$langage_lbl['LBL_WALLET_BIC_SWIFT_CODE']; ?></h5>
                            <input type="text" name="BICSWIFTCode" id="BICSWIFTCode" class="form-control BICSWIFTCode" <?php if($type == 'Driver'){?>value="<?=$db_driver[0]['vBIC_SWIFT_Code'];?>"<?php }?>>
							<h5><?=$langage_lbl['LBL_WALLET_BANK_LOCATION']; ?></h5>
                            <input type="text" name="vBankBranch" id="vBankBranch" class="form-control vBankBranch" <?php if($type == 'Driver'){?>value="<?=$db_driver[0]['vBankLocation'];?>"<?php }?>>
							<h5><?=$langage_lbl['LBL_ENTER_AMOUNT']; ?></h5>
                            <input type="text" name="fAmount" id="fAmount" class="form-control fAmount" value="">
                            <!-- <span class="input-group-addon add-on"><i class="icon-calendar"></i></span> -->
                        </div>
                    </div>
                    <input type="button" onClick="check_login_small();" id="withdrawal_request" class="save" name="<?=$langage_lbl['LBL_WALLET_save']; ?>" value="<?=$langage_lbl['LBL_BTN_SEND_TXT']; ?>">
                    <input type="button" class="cancel" data-dismiss="modal" name="<?=$langage_lbl['LBL_WALLET_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>" value="<?=$langage_lbl['LBL_WALLET_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>">
                </form>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>
<!-- add money-->
<div class="col-lg-12">
    <div class="modal fade" id="uiModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-content image-upload-1 popup-box1">
            <div class="upload-content">
                <h4><?=$langage_lbl['LBL_ADD_MONEY'];?></h4>
	                 <form class="form-horizontal" id="addmoney" method="post" enctype="multipart/form-data" action="add_money.php" name="addmoney">
					        <input type="hidden" id="action" name="action" value="add_money">
					        <input type="hidden"  name="iMemberId" id="iMemberId" value="<?=$_SESSION['sess_iUserId'];?>">
					        <input type="hidden"  name="eMemberType" id="eMemberType" value="<?=$type;?>">
					        <input type="hidden"  name="Member_Available_Balance" id="Member_Available_Balance" value="<?=$user_available_balance;?>">
				                                  
                    <div class="col-lg-13">                    
                        <div class="input-group input-append" >
                        <h5><?=$langage_lbl['LBL_ENTER_AMOUNT']; ?></h5>
                            <input type="text" name="fAmount" id="fAmountprice" class="form-control fAmount" value="">
                            <!-- <span class="input-group-addon add-on"><i class="icon-calendar"></i></span> -->
                        </div>
                    </div>
                    <input type="button" onClick="add_money_to_wallet();" class="save" name="<?=$langage_lbl['LBL_WALLET_save']; ?>" value="<?=$langage_lbl['LBL_WALLET_save']; ?>">
                    <input type="button" class="cancel" data-dismiss="modal" name="<?=$langage_lbl['LBL_WALLET_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>" value="<?=$langage_lbl['LBL_WALLET_BTN_PROFILE_CANCEL_TRIP_TXT']; ?>">
                </form>
                <div style="clear:both;"></div>
            </div>
        </div>
    </div>
</div>
<!-- add money-->

<!-- <form class="form-horizontal" id="addmoney" method="post" enctype="multipart/form-data" action="add_money.php" name="addmoney">
        <input type="hidden" id="action" name="action" value="add_money">
        <input type="hidden"  name="iMemberId" id="iMemberId" value="<?=$_SESSION['sess_iUserId'];?>">
        <input type="hidden"  name="eMemberType" id="eMemberType" value="<?=$type;?>">
        <input type="hidden"  name="Member_Available_Balance" id="Member_Available_Balance" value="<?=$user_available_balance;?>">
 </form>   -->    

    <script src="assets/js/jquery-ui.min.js"></script>
    <script src="assets/plugins/dataTables/jquery.dataTables.js"></script>
    <script type="text/javascript">
         $(document).ready(function() {
			    $(".fAmount").keydown(function (e) {
			        // Allow: backspace, delete, tab, escape, enter and .
			        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
			             // Allow: Ctrl+A
			            (e.keyCode == 65 && e.ctrlKey === true) ||
			             // Allow: Ctrl+C
			            (e.keyCode == 67 && e.ctrlKey === true) ||
			             // Allow: Ctrl+X
			            (e.keyCode == 88 && e.ctrlKey === true) ||
			             // Allow: home, end, left, right
			            (e.keyCode >= 35 && e.keyCode <= 39)) {
			                 // let it happen, don't do anything
			                 return;
			        }
			        // Ensure that it is a number and stop the keypress
			        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			            e.preventDefault();
			        }
			    });
				
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
            /* $('#dataTables-example').dataTable({
			  "order": [[ 0, "desc" ]]
			 });*/
			 
         	$('#dataTables-example').dataTable({
				fixedHeader: {
					footer: true
				},
				"order": [[ 3, "desc" ]],
				"aaSorting": []});
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
			 	message: "<h4><?php  echo $langage_lbl['LBL_FROM_TO_DATE_ERROR_MSG']?></h4>",
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
	 	function check_skills_edit(){
			y = getCheckCount(document.frmbooking);
			if(y>0)
			{  
			 	$("#eTransRequest").val('Yes');
			    document.frmbooking.submit();
			}
			else{
			  	alert("<?php  echo $langage_lbl['LBL_SELECT_RIDE_FOR_TRANSFER_MSG']?>")
			  	return false;
		  	}
		}
		function check_login_small(){
			var maxamount = document.getElementById("User_Available_Balance").value;
			var requestamount = document.getElementById("fAmount").value;
			var vHolderName = document.getElementById("vHolderName").value;
			var vBankName = document.getElementById("vBankName").value;
			var iBankAccountNo = document.getElementById("iBankAccountNo").value;
			var BICSWIFTCode = document.getElementById("BICSWIFTCode").value;
			var vBankBranch = document.getElementById("vBankBranch").value;
			
			if(vHolderName == ''){
				alert("<?php  echo $langage_lbl['LBL_ACCOUNT_HOLDER_NAME_MSG']?>");
				return false;
			}
			if(vBankName == ''){
				alert("<?php  echo $langage_lbl['LBL_BANK_MSG']?>");
				return false;
			}
			if(iBankAccountNo == ''){
				alert("<?php  echo $langage_lbl['LBL_ACCOUNT_NUM_MSG']?>");
				return false;
			}
			if(BICSWIFTCode == ''){
				alert("<?php  echo $langage_lbl['LBL_BIC_SWIFT_CODE_MSG']?>");
				return false;
			}
			if(vBankBranch == ''){
				alert("<?php  echo $langage_lbl['LBL_BANK_BRANCH_MSG']?>");
				return false;
			}
			
			if(requestamount == ''){
				alert("<?php  echo $langage_lbl['LBL_WITHDRAW_AMT_MSG']?>");
				return false;
			}

			if(requestamount == 0){
				alert("<?php  echo $langage_lbl['LBL_WITHDRAW_AMT_ERROR']?>");
				return false;
			}
			/*if(parseFloat(requestamount) > parseFloat(maxamount)){
				alert("Please Enter Withdraw Amount Less Than " + maxamount );
				return false;
			}*/
			$("#eTransRequest").val('Yes');
			//document.frm6.submit();
			if(vHolderName != "" && vBankName != "" && iBankAccountNo != "" && BICSWIFTCode != "" && vBankBranch != "" && requestamount != ""){
				$("#withdrawal_request").val('Please wait ...').attr('disabled','disabled');	
				var request = $.ajax({
					type: "POST",
					url: 'user_withdraw_request.php',
					data: $("#frm6").serialize(),
					success: function (data)
					{  
						//alert(data);
						if(data == 0)
						{
							var err = "<?php  echo $langage_lbl['LBL_WITHDRAW_AMT_VALIDATION_MSG']?>";
							bootbox.dialog({
								message: "<h3>"+err+"</h3>",
								buttons: {
									danger: {
										label: "Ok",
										className: "btn-danger",
										callback: function () {
											$("#withdrawal_request").val('Send').removeAttr('disabled');
										}
									},
								}
							});
							
							return false;
						}
						else if(data == 1)
						{
							$('#uiModal').modal('hide');
							var err = "<?php  echo $langage_lbl['LBL_WITHDRAW_AMT_SUCCESS_MSG']?>";
							bootbox.dialog({
								message: "<h3>"+err+"</h3>",
								buttons: {
									danger: {
										label: "Ok",
										className: "btn-danger",
										callback: function () {
										   $("#withdrawal_request").val('Send').removeAttr('disabled');
						                   $('#uiModal #frm6')[0].reset();
						                }
									},
								}
							});
							
							return true;  
						}
						
					}
				});
				request.fail(function (jqXHR, textStatus) {
					alert("Request failed: " + textStatus);
				});
			}
		}
		function add_money_to_wallet(){
			var priceamount = document.getElementById("fAmountprice").value;
			if(priceamount == ''){
				alert("<?php  echo $langage_lbl['LBL_WITHDRAW_AMT_MSG']?>");
				return false;
			}

			document.addmoney.submit();	
		}
    </script>
    
    <script type="text/javascript">
    $(document).ready(function(){
    	$('[data-dismiss=modal]').on('click', function (e) {
    		 $('#uiModal #frm6')[0].reset();
    	});
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