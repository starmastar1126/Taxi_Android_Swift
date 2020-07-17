<?php 
include_once('../common.php');
$action= isset($_REQUEST['action'])?$_REQUEST['action']:'';
if($action==''){
	header("location:index.php");exit;
}
$iTripId = isset($_REQUEST['iTripId'])?$_REQUEST['iTripId']:'';
$Data = array();
$sql = "SELECT * FROM trips WHERE iTripId = ".$iTripId;
$db_trip = $obj->MySQLSelect($sql);
//echo "<pre>";print_r($db_trip);echo "</pre>";
$Data[0]['slocation'] = $db_trip[0]['tSaddress'];
$Data[0]['elocation'] = $db_trip[0]['tDaddress'];


$sql = "SELECT concat(vName,' ',vLastName) as name, iDriverId, vImage from register_driver where iDriverId = '".$db_trip[0]['iDriverId']."'";
$db_driver = $obj->MySQLSelect($sql);
$Data[0]['driver'] = $db_driver[0]['name'];
//echo "<pre>";print_r($db_driver);echo "</pre>";
if(file_exists($tconfig["tsite_upload_images_driver_path"]. '/' . $db_driver[0]['iDriverId'] . '/2_' . $db_driver[0]['vImage'])){
	$img=$tconfig["tsite_upload_images_driver"]. '/' . $db_driver[0]['iDriverId'] . '/2_' .$db_driver[0]['vImage'];
}
else{
	$img="../webimages/icons/help/driver.png";
}
$sql = "SELECT concat(vName,' ',vLastName) as rname,vEmail FROM register_user where iUserId = '".$db_trip[0]['iUserId']."'";
$db_user = $obj->MySQLSelect($sql);
$Data[0]['rider'] = $db_user[0]['rname'];
$Data[0]['email'] = $db_user[0]['vEmail'];
//echo "<pre>";print_r($db_driver);echo "</pre>";
$sql = "SELECT * from ratings_user_driver where iTripId = '".$iTripId."' and eUserType = 'Passenger'";
$db_rating = $obj->MySQLSelect($sql);
$Data[0]['vRating'] = $db_rating[0]['vRating1'];
#echo "<pre>";print_r($db_rating);echo "</pre>";

/*######## for total-time ########*/
$to_time = strtotime($db_trip[0]['tStartDate']);
$from_time = strtotime($db_trip[0]['tEndDate']);
$total_time = round(abs($to_time - $from_time) / 60,2). " minute";
$Data[0]['tot_time'] = $total_time;
/*######## for total-time end ########*/

$date1 = $db_trip[0]['tStartDate'];
$date2 = $db_trip[0]['tEndDate'];
$totalTimeInMinutes_trip=@round(abs(strtotime($date2) - strtotime($date1)) / 60,2);

$diff = abs(strtotime($date2) - strtotime($date1));
$years = floor($diff / (365*60*60*24)); $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
$minuts = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));
$Data[0]['time_taken'] = $hours.':'.$minuts.':'.$seconds;
$sql = "SELECT * from vehicle_type where iVehicleTypeId = '".$db_trip[0]['iVehicleTypeId']."'";
$db_vtype = $obj->MySQLSelect($sql);
//echo "<pre>";print_R($db_vtype);echo "</pre>";




/*$priceRatio=($obj->MySQLSelect("SELECT Ratio FROM currency WHERE vName='".$db_trip[0]['vCurrencyPassenger']."' ")[0]['Ratio']);

// $distance=round($db_trip[0]['fPricePerKM']*$db_trip[0]['fDistance'],2) * $priceRatio;
$distance=round($db_trip[0]['fPricePerKM']*$db_trip[0]['fDistance'],2) * $priceRatio;
$time=($db_trip[0]['fPricePerMin']*$totalTimeInMinutes_trip) * $priceRatio;

$total_amt=$db_trip[0]['iFare'] * $priceRatio;
$fCommision=  (($db_trip[0]['iFare']*$db_trip[0]['fCommision'])/100) * $priceRatio;
$basefare=$db_trip[0]['iBaseFare'] * $priceRatio;*/

//$currencySymbol = ($obj->MySQLSelect("SELECT vSymbol FROM currency WHERE vName='".$db_trip[0]['vCurrencyPassenger']."' ")[0]['vSymbol']);

$tripFareResults= $generalobj->getFinalFare($db_trip[0]['iBaseFare'],$db_trip[0]['fPricePerMin'],$totalTimeInMinutes_trip,$db_trip[0]['fPricePerKM'],$db_trip[0]['fDistance'],$db_trip[0]['fCommision'],1,$db_trip[0]['vCurrencyPassenger'],$date1,$date2);


$time1=$currencySymbol . ' '. round($tripFareResults['FareOfMinutes']* $db_trip[0]['fRatioPassenger'],1);
$distance=$currencySymbol . ' '. round($tripFareResults['FareOfDistance']*$db_trip[0]['fRatioPassenger'],1);
// $row[$i]['iFare']=$row[$i]['iFare'] * $priceRatio;
// $row[$i]['iFare']=$tripFareResults['FinalFare'];
$total_fare=$currencySymbol . ' '. round($db_trip[0]['iFare'] * $db_trip[0]['fRatioPassenger'],1);
$iBaseFare=$currencySymbol . ' '. round($tripFareResults['iBaseFare'] * $db_trip[0]['fRatioPassenger'],1);
$Commision= $currencySymbol . ' '. round($tripFareResults['FareOfCommision']* $db_trip[0]['fRatioPassenger'],1) ;

$refDiscount  = 0;
	 $refAmount = $db_trip[0]['iRefAmount'];
	 
	if($refAmount > 0){
		$refDiscount_temp = round($db_trip[0]['iRefAmount']  * $db_trip[0]['fRatioPassenger'],1) ;
		$refDiscount =  $currencySymbol . ' '. $refDiscount_temp;
		$refamount = $db_trip[0]['iFare'] - $refDiscount_temp ;
		$total_fare_temp = round($db_trip[0]['iFare'] * $db_trip[0]['fRatioPassenger'],1) ;
		$total_fare =  $currencySymbol . ' '. round($total_fare_temp - $refDiscount_temp,1);
		
	}

$basefare=$iBaseFare;
$fCommision=$Commision;
$total_amt=$total_fare;

$car = $db_vtype[0]['vVehicleType'];
// $basefare = $db_trip[0]['iBaseFare'];
// $distance = $db_trip[0]['fPricePerKM']*$db_trip[0]['fDistance'];
// $time = $db_trip[0]['fPricePerMin'] * $total_time;
// $total_amt = $basefare+$distance+$time;
$payment_mode = $db_trip[0]['vTripPaymentMode'];
$ridenum = $db_trip[0]['vRideNo'];

$Data[0]['CurrencySymbol']=($obj->MySQLSelect("SELECT vSymbol FROM currency WHERE vName='".$db_trip[0]['vCurrencyPassenger']."' ")[0]['vSymbol']);
$Data[0]['ProjectName'] = $generalobj->getConfigurations("configurations","SITE_NAME");
$Data[0]['ProjectName1'] ='<img class="logo" src="'.$tconfig["tsite_home_images"].'logo.png" alt="">';
$Data[0]['car'] = $car;
$Data[0]['basefare'] = $basefare;
$Data[0]['distance'] = $distance;
$Data[0]['time'] = $time;
$Data[0]['total_amt'] = $total_amt;
$Data[0]['fCommision'] = $fCommision;
$Data[0]['payment_mode'] = $payment_mode;
$Data[0]['ridenum'] = $ridenum;
$Data[0]['refDiscount'] = $refDiscount;
$sql = "SELECT * from configurations where vName = 'COPYRIGHT_TEXT'";
$copy = $obj->MySQLSELECT($sql);
//echo "copyright".$copy[0]['vValue'];
$Data[0]['copyright'] = $copy[0]['vValue'];
$start_time = $generalobj->DateTime($db_trip[0]['tStartDate'],12);
$endtime = $generalobj->DateTime($db_trip[0]['tEndDate'],12);
$kms = $db_trip[0]['fDistance'];
$Data[0]['start_time'] = $start_time;
$Data[0]['endtime'] = $endtime;
$Data[0]['kms'] = $kms;
function paymentimg($paymentm){
	 global $tconfig;
	if($paymentm == "Paypal"){
		return $tconfig["tsite_url"]."webimages/icons/payment_images/ic_payment_type_paypal.png";
	}
	else
	{
		return $tconfig["tsite_url"]."webimages/icons/payment_images/ic_payment_type_cash.png";
	}
}
function ratingmark($ratingval)
{
	global $tconfig;
	$a = $ratingval;
	$b = explode('.', $a);
	$c = $b[0];

	$str = "";
	$count=0;
	for($i=0; $i<5; $i++)
	{
		if($c>$i){
			$str .= '<img src="'.$tconfig["tsite_url"].'webimages/icons/ratings_images/Star-Full.png" style="outline:none;text-decoration:none;width:20px;border:none" align="left" >';
		}
		elseif($a > $c && $count==0){
			$str .= '<img src="'.$tconfig["tsite_url"].'webimages/icons/ratings_images/Star-Half-Full.png" style="outline:none;text-decoration:none;width:20px;border:none" align="left" >';
			$count=1;
		}
		else
		{
			$str .= '<img src="../webimages/icons/ratings_images/Star-blank.png" style="outline:none;text-decoration:none;width:20px;border:none" align="left" >';
		}
	}
	return $str;

}

$mailcont_member_trips ='';
$mailcont_member ='';
$mailcont_member .=
'<div style="width:730px;!important;color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,
Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:19px;font-size:14px;margin:0;padding:0">
<table style="border-spacing:0;border-collapse:collapse;background-color:#111125!important;vertical-align:top;text-align:left;height:100%;width:100%;color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;line-height:19px;font-size:14px;margin:0;padding:0"><tbody><tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left"><td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;padding:0" align="center" valign="top">
	<center style="width:100%;min-width:580px">
		<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:inherit;width:660px;margin:0 auto;padding:0"><tbody><tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left"><td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;padding:0" align="left" valign="top">
			<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:640px;margin:0 10px;padding:0">
				<tbody>
					<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;padding:28px 0" align="left" valign="top" width="127">
							<!-- <img src="https://ci3.googleusercontent.com/proxy/3dwiBmRpD37Ne91tAbh8QIfarRWdaUEuSwzkA8cSo8RMC3K90BNYQm_WQeiiUgZYwkvfumd8QLKHXDBoYxoptAI9rSI0WIeYqQNWPeV9VZJ7zYImyn2VfSQWwWzo=s0-d-e1-ft#http://d1a3f4spazzrp4.cloudfront.net/receipt-new/uber-logo-light@2x.png" style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left" height="19" width="127" class="CToWUd">-->
							<span style="color:white;">'.$Data[0]['ProjectName1'].'</span>
						</td>
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;font-size:11px;color:#999999;line-height:15px;text-transform:uppercase;padding:30px 0 26px" align="right" valign="top">
							<span>Ride Number:'.$Data[0]['ridenum'].'</span>
						</td>
					</tr>
				</tbody>
			</table>
			<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:640px;max-width:640px;border-radius:2px;background-color:#ffffff;margin:0 10px;padding:0" bgcolor="#ffffff">
				<tbody>
					<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:100%;padding:0" align="left" valign="top">
							<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;max-width:640px;border-bottom-width:1px;border-bottom-color:#e3e3e3;border-bottom-style:solid;padding:0">
								<tbody>
									<tr style="vertical-align:top;text-align:left;width:100%;background-color:rgb(250,250,250);padding:0" align="left" bgcolor="rgb(250,250,250)">
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:299px;border-radius:3px 0 0 0;background-color:#fafafa;padding:26px 10px 20px" align="left" bgcolor="#FAFAFA" valign="top">
											<span style="font-weight:bold;font-size:32px;color:#000;line-height:30px;padding-left:15px">
												'.$Data[0]['CurrencySymbol'].$Data[0]['total_amt'].'
											</span>
										</td>
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:inline-block;width:299px;border-radius:0 3px 0 0;background-color:#fafafa;padding:26px 10px 20px" align="right" bgcolor="#FAFAFA" valign="top">
											<span style="padding-right:15px;line-height:10px;font-size:13px;font-weight:normal;color:#b2b2b2">Thanks for choosing '.$Data[0]['ProjectName'].', '.$Data[0]['rider'].'</span>
										</td>
									</tr>
								</tbody>
							</table>
							<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;max-width:640px;border-bottom-width:1px;border-bottom-color:#f0f0f0;border-bottom-style:solid;padding:0">
								<tbody>
									<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:300px;padding:25px 10px 25px 5px" align="left" valign="top">
											<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;margin-left:19px;padding:0">
												<tbody>
													<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
														<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:300px;padding:0" align="left" valign="top">
															<!-- <img src="?ui=2&amp;ik=664899e0a6&amp;view=fimg&amp;th=14ff8dfb563a7ad2&amp;attid=0.1&amp;disp=emb&amp;realattid=ec4b5213f81c2da8_0.2.1&amp;attbid=ANGjdJ9KWDUTnGjon4TMCJyHlBLeY_vRzg1MhZ-u1502HfwLRWMrSuAhmayg-Qx2uhXI8uxweF0QWFvM1xK7HA12g_oLyrfBEcCcg1PScmONkBhQbiK3m8VI07TVhwg&amp;sz=w558-h434&amp;ats=1443003800594&amp;rm=14ff8dfb563a7ad2&amp;zw&amp;atsh=1" style="outline:none;text-decoration:none;float:none;clear:none;display:block;width:279px;min-height:217px;border-radius:3px 3px 0 0;border:1px solid #d7d7d7" align="none" height="217" width="279" class="CToWUd a6T" tabindex="0">-->
															<div class="a6S" dir="ltr" style="opacity: 0.01; left: 432.922px; top: 670px;">
																<div id=":n0" class="T-I J-J5-Ji aQv T-I-ax7 L3 a5q" role="button" tabindex="0" aria-label="Download attachment map_32aba2dc-7679-4c0e-bea3-7f4d8e8f934a" data-tooltip-class="a1V" data-tooltip="Download">
																	<div class="aSK J-J5-Ji aYr"></div>
																</div>
																<div id=":n1" class="T-I J-J5-Ji aQv T-I-ax7 L3 a5q" role="button" tabindex="0" aria-label="Save attachment to Drive map_32aba2dc-7679-4c0e-bea3-7f4d8e8f934a" data-tooltip-class="a1V" data-tooltip="Save to Drive">
																	<div class="wtScjd J-J5-Ji aYr aQu">
																		<div class="T-aT4" style="display: none;">
																			<div></div>
																			<div class="T-aT4-JX"></div>
																		</div>
																	</div>
																</div>
															</div>
														</td>
													</tr>
													<tr style="vertical-align:top;text-align:left;width:279px;display:block;background-color:#fafafa;padding:20px 0;border-color:#e3e3e3;border-style:solid;border-width:1px 1px 0px" align="left" bgcolor="#FAFAFA">
														<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:279px;padding:0" align="left" valign="top">
															<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:auto;padding:0">
																<tbody>
																	<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
																		<td rowspan="2" style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:17px!important;padding:3px 10px 10px 17px" align="left" valign="top">
																			<img src="https://ci4.googleusercontent.com/proxy/QDRZoQfmGR7KUMRZMDrQOKbTjllsRIYMXIlDHE1YncLVdO-8wBhUMBgUk1UXR-ZsWF2TOknOjcgcANGhitUdKdv8vTpm5SOelGjYB-j-OLg=s0-d-e1-ft#http://d1a3f4spazzrp4.cloudfront.net/receipt-new/route.png" style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left" height="80" width="13" class="CToWUd">
																		</td>
																		<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:279px;line-height:16px;height:57px;padding:0 10px 10px 0" align="left" valign="top">
																			<span style="font-size:15px;font-weight:500;color:#000000!important">
																				<span class="aBn" data-term="goog_43159640" tabindex="0">
																					<span class="aQJ">'.$Data[0]['start_time'].'</span>
																				</span>
																			</span>
																			<br>
																			<span style="font-size:11px;color:#999999!important;line-height:16px;text-decoration:none">'.$Data[0]['slocation'].'</span>
																		</td>
																	</tr>
																	<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
																		<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:279px;line-height:16px;height:auto;padding:0 0px 0 0" align="left" valign="top">
																			<span style="font-size:15px;font-weight:500;color:#000000!important">
																				<span class="aBn" data-term="goog_43159641" tabindex="0">
																					<span class="aQJ">'.$Data[0]['endtime'].'</span>
																				</span>
																			</span><br>
																			<span style="font-size:11px;color:#999999!important;line-height:16px;text-decoration:none">'.$Data[0]['elocation'].'</span>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
													<tr style="vertical-align:top;text-align:left;width:279px;display:block;background-color:#fafafa;padding:0;border:1px solid #e3e3e3" align="left" bgcolor="#FAFAFA">
														<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell!important;width:279px!important;padding:0" align="left" valign="top">
															<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;color:#959595;line-height:14px;padding:0">
																<tbody>
																	<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
																		<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell!important;width:33%!important;line-height:16px;padding:6px 10px 10px" align="center" valign="top">
																			<span style="font-size:9px;text-transform:uppercase">CAR</span><br>
																			<span style="font-size:13px;color:#111125;font-weight:normal">'.$Data[0]['car'].'</span>
																		</td>
																		<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell!important;width:33%!important;line-height:16px;padding:6px 10px 10px" align="center" valign="top">
																			<span style="font-size:9px;text-transform:uppercase">kilometers</span><br><span style="font-size:13px;color:#111125;font-weight:normal">'.$Data[0]['kms'].'</span>
																		</td>
																		<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell!important;width:33%!important;line-height:16px;padding:6px 10px 10px" align="center" valign="top">
																			<span style="font-size:9px;text-transform:uppercase">TRIP TIME</span><br><span style="font-size:13px;color:#111125;font-weight:normal"><span class="aBn" data-term="goog_43159642" tabindex="0"><span class="aQJ">'.$Data[0]['time_taken'].'</span></span></span>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:300px;padding:10px" align="left" valign="top">
											<span style="display:block;padding:0px 8px 0 10px">
												<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;padding:0">
													<tbody>
														<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
																<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
																</p>
															</td>
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:center;display:table-cell;width:120px!important;font-size:11px;white-space:pre-wrap;padding:12px 10px 5px" align="center" valign="middle">FARE BREAKDOWN</td>
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
																<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
																</p>
															</td>
														</tr>
													</tbody>
												</table>
												<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;margin-top:15px;width:auto;padding:0">
													<tbody>
														<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
																Base Fare
															</td>
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['basefare'].'</td>
														</tr>
														<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
																Distance
															</td>
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['distance'].'</td>
														</tr>

														<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
																Time
															</td>
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['time'].'</td>
														</tr>

														<tr style="vertical-align:top;text-align:left;border-bottom-width:1px;border-bottom-color:#f0f0f0;border-bottom-style:solid;width:100%;padding:0" align="left">
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px 4px 15px" align="left" valign="top">
																Platform Fees
															</td>
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px 4px 15px" align="right" valign="top">'.$Data[0]['fCommision'].'</td>
														</tr>';
														 if($refAmount != 0){
															$mailcont_member .= '<tr style="vertical-align:top;text-align:left;border-bottom-width:1px;width:100%;padding:0" align="left">
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px 4px 15px" align="left" valign="top">
																Referral Discount
															</td>
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px 4px 15px" align="right" valign="top">'.$Data[0]['refDiscount'].'</td>
														</tr>';
														}
														$mailcont_member .= '<tr style="vertical-align:top;text-align:left;font-weight:bold;width:100%;padding:0" align="left">
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#111125;padding:15px 4px 4px" align="left" valign="top">Subtotal</td>
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:15px 4px 4px" align="right" valign="top">'.$Data[0]['CurrencySymbol'].$Data[0]['total_amt'].'</td>
														</tr>
														<tr style="vertical-align:top;text-align:left;border-bottom-width:1px;border-bottom-color:#f0f0f0;border-bottom-style:solid;width:100%;padding:0" align="left">
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;line-height:18px;padding:15px 4px" align="left" valign="top">
																<span style="font-size:9px;line-height:7px">CHARGED</span>
																<br>

																<img src="'.paymentimg($Data[0]['payment_mode']).'" style="outline:none;text-decoration:none;float:left;clear:both;display:block;width:40px!important;min-height:25px;margin-right:5px;margin-top:3px" align="left" height="12" width="17" class="CToWUd">
																<span style="font-size:13px">
																	'.$Data[0]['payment_mode'].'
																</span>
															</td>
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;font-size:19px;font-weight:bold;line-height:30px;padding:26px 4px 15px" align="right" valign="top">
																'.$Data[0]['CurrencySymbol'].$Data[0]['total_amt'].'
															</td>
														</tr>
													</tbody>
												</table>											


												<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:auto;padding:0">
													<tbody>
														<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell;width:300px;font-size:15px;padding:16px 10px 0px" align="center" valign="top">
																<span></span>
															</td>
														</tr>
													</tbody>
												</table>

												<span style="line-height:1px;font-size:1px;color:#ffffff">xid32aba2dc-7679-4c0e-bea3-<wbr>7f4d8e8f934a</span>
												<br>
												<span style="line-height:1px;font-size:1px;color:#ffffff">pGvlI2ANUbXFfyEOgxta1RMV082993</span>
											</span>
										</td>
									</tr>
								</tbody>
							</table>
							<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;max-width:640px;border-bottom-width:1px;border-bottom-color:#f0f0f0;border-bottom-style:solid;padding:0">
								<tbody>
									<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:55%;padding:0px" align="left" valign="top">
											<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;max-width:640px;display:inline-block;padding:0">
												<tbody>
													<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
														<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:100%!important;line-height:15px;padding:0px 0px 0px 10px" align="left" valign="top">
															<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:auto;padding:0">
																<tbody>
																	<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
																		<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:45px;padding:22px 0px 20px" align="left" valign="top">
																			<img src="'.$img.'" style="outline:none;text-decoration:none;float:left;clear:both;display:inline-block;width:45px!important;min-height:45px!important;border-radius:50em;margin-left:15px;max-width:45px!important;min-width:45px!important;border:1px solid #d7d7d7" align="left" height="45" width="45" class="CToWUd">
																		</td>
																		<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:300px;padding:22px 10px 20px" align="left" valign="middle">
																			<span style="padding-bottom:5px;display:inline-block">You ride with '.$generalobjAdmin->clearName($Data[0]['driver']).'</span>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:42%;height:100%;padding:10px 0px 0px" align="left" valign="top">
											<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;max-width:640px;display:block;padding:20px 0px 0px">
												<tbody style="width:100%;display:block">
													<tr style="vertical-align:top;text-align:right;width:100%;display:block;padding:0px" align="right">
														<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline;width:100px;font-size:9px;color:#b2b2b2;text-transform:uppercase;padding:0px 5px 0px 0px" align="left" valign="top">
															<span>Trip Rating</span>
														</td>
														<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:225px;padding:0px 0px 10px" align="left" valign="top">
															<span style="font-size:11px;min-height:20px;display:inline-block!important;padding:0px 2px">'.ratingmark($Data[0]['vRating']).'</span>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</td>
		</tr>
		</tbody>
		</table>
		<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:inherit;width:640px;max-width:640px;margin:0 auto;padding:0">
			<tbody>
				<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
					<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:319px!important;height:35px;font-size:12px; align="left" valign="top">
						<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:inherit;width:640px;max-width:640px;background-color:#1a1b2c;margin:0 auto 20px;padding:0;border:2px solid #2f2f3f" bgcolor="#1A1B2C">
							<tbody>
								<tr style="vertical-align:top;text-align:center!important;width:100%;padding:0" align="center !important">
									<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center!important;display:inline-block;padding:10px" align="center !important" valign="top">
										<span style="font-size:11px;color:#b2b2b2">'.$Data[0]['copyright'].'</span>
									</td>
								</tr>
							</tbody>
						</table>
					</center>
				</td>
			</tr>
		</tbody>
	</table>
	<img src="https://ci6.googleusercontent.com/proxy/i-Mv-nqPHLep8LtlltGXcNdQd6nEgT6dVIwLSczpMHh1MhJsZs8ktCOkWldo4trnZC6w0GWy1-qUh8zsJh1Ha_KDIo_bHW80xaEoZVu--qVD-yqUPqY7_y1p3e8qemf9-Ma82nGvTM7wxyv94GA_uKseztJU9N7sYBGLXGEBZFCqRb8bBUZ-9fLahtSK-SUH2kVXT17K9nFu_VNRsoOySVqs7hVmR-hJTskd4EKiVX48O0K8x_fJhsQp4hfW11ZYC-Iwa81p6SE4mRwwXfUrWUnHiuUWzo3PLmzPpjPb2eqHZt7EHPM_vls_lPa72CYzcnmNqLGOjy-8L4xXeqHplLg5I7sKR-KiDlr5aKgcJ56vp1aMQ-q6sZzdA1Q8fRO-nFdg7amQpSYKEvQ6qjKh4idME23aMrwak4_euMCLUDJ_QW9VGhVx8alLL_8LTo4y8OPpGz8AmCX5NPAF8d-iNWu2aBU_6Yd5VEz8NnPnqpdxIB4HPJZ1NaP4GYJZfAc13a0v0orwPzFd8c2jnVWZNjK_oeeVf2NNJ8DwIWYhOZoAWhpfib5QP21wsArXDX8dQDdnFlafKuDCOQLZ7GE2U0vMiHBXDqPobyrcYDqw7-nQ5EG26n1VdYHoPuEi18CbKfdjzHadezgiYWPjxda2GL5CBkKVlBbubCUdVOf2iCgiC3h0iP6VhNSvm7la_sDepzep0fMezs4D57ewVxIAJzh9Uxpvc3QKw4w6xNptwnXLelZ14zwEcC_B4XPUq8uUclxjutexGqnIc6UNRvcCZT15P65FOap9BALMkMTPOaDhGhs1MHdBU1z567Rc2iJ3rgj6YKptcpvzyyRynrvdr977R7tw5l3XuDq-n1NJ-S4JOfZOTnxo1YjddqI7Cj7Z_fV2tDAVCAqIxC7ZeQxjwR8SxV4M9Kgh2-HUpZEC=s0-d-e1-ft#https://email.uber.com/wf/open?upn=-2B9Po7aio80eD5z5-2BpZz5s4mgweqG6ytypDbDN7qj-2FV5sZX-2FgiwDFSXHLeDIHJJx9h6oKyP26QDWOs4UmoGPH0znBS1yzoTxtpiXV0R2tlnWJ1cOBsmmHYqgtjl3chqK7rn1vEldIFBV1ZF0xKurtj2X-2BfrqVoNuHN-2Bet-2F0Wn9MuQBXDeNFOU6dDpL65WSQZsDwvCtg8nW0WoxjrFPaAo-2FWpd0dE4aIZ3YEQUntDkWVI650k-2BKxcHeWBoD3xMN4ZeUhD2IcW2RgN9-2FW-2Bkpx9xDilAe1w1beIASBbDtlPDkmzBRpIZNyK5jJTuQyOBq9VFxpE9NFhzkQcQUgNhPOVxbkETMlNC3km-2FMylmfG-2BDLFLbKktdDuJsmd3pnkexYo5XosX7xBNi7E61msDswKCvB-2FjDEqpjgNVQ5ssKesEXTMMUZLWv7aSdwFJIgyiDzfAZg54wcTiF7-2BfNGcHCz06eJIk1twl-2FQq7ZwXOCvcrlg8mXZlbDNFuRbWINtF1QYMOaONmNeIZA-2BC-2BRG208VP8lY7jZLO895JnZ1rHIas8gn-2FbwdOotwXvSIlMoq8tf-2FvC7orYNs1piqO72eDghoTWvdQ-3D-3D" alt="" style="min-height:1px!important;width:1px!important;border-width:0!important;margin-top:0!important;margin-bottom:0!important;margin-right:0!important;margin-left:0!important;padding-top:0!important;padding-bottom:0!important;padding-right:0!important;padding-left:0!important" height="1" border="0" width="1" class="CToWUd">
	<span>
		<font color="#888888"></font>
	</span>
	</div>';
	echo $mailcont_member;exit;
	if($action=="mail"){
		$maildata_member['details'] = $mailcont_member;
	 	$maildata_member['email'] = $Data[0]['email'];
	  $generalobj->send_email_user("RIDER_INVOICE",$maildata_member);
	 	header("location:invoice.php?success=1&iTripId=".$iTripId);exit;
	}
	else if($action=="print"){

	}
	else{
			header("dashboard.php");exit;
	}
	echo $mailcont_member;exit;
?>
