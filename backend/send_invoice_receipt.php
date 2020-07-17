<?php 
   	$action_from = isset($_REQUEST['action_from'])?$_REQUEST['action_from']:'';
   	$iTripId = isset($_REQUEST['iTripId'])?$_REQUEST['iTripId']:'';
    //$iTripId = "1";
   	//$action_from = "1";
	if($action_from != '' && $iTripId != ''){
   		include_once('common.php');   
   		include_once('generalFunctions.php');  	
		// global $generalobj;
		global $tconfig;
   		sendTripReceipt($iTripId);
   		//sendTripReceiptAdmin($iTripId);
   		//header('Location: admin/invoice.php?iTripId='.$iTripId.'&success=1'); exit;
		header("location:".$tconfig['tsite_url_main_admin'].'invoice.php?iTripId='.$iTripId.'&success=1'); exit;
   	}
	####################### FUNCTIONS:for email receipt ##########################	

	function sendTripReceipt($iTripId){
		global $obj,$generalobj,$tconfig,$APP_TYPE;

		$APP_DELIVERY_MODE = $generalobj->getConfigurations("configurations","APP_DELIVERY_MODE");
		$Data = array();
		$sql = "SELECT * FROM trips WHERE iTripId = ".$iTripId;
		$db_trip_data = $obj->MySQLSelect($sql);
		$db_trip = $generalobj->getTripPriceDetailsForWeb($iTripId,$db_trip_data[0]['iUserId'],'Passenger');
		//echo "<pre>";print_r($db_trip);echo "</pre>";die;
		$Data[0]['slocation'] = $db_trip['tSaddress'];
		$Data[0]['elocation'] = $db_trip['tDaddress'];
		$Data[0]['tStartLat'] = $db_trip['tStartLat'];
		$Data[0]['tStartLong'] = $db_trip['tStartLong'];
		$Data[0]['tEndLat'] = $db_trip['tEndLat'];
		$Data[0]['tEndLong'] = $db_trip['tEndLong'];
		$Data[0]['vReceiverName'] = $db_trip['vReceiverName'];
		$Data[0]['vReceiverMobile'] = $db_trip['vReceiverMobile'];
		$Data[0]['tPickUpIns'] = $db_trip['tPickUpIns'];
		$Data[0]['tDeliveryIns'] = $db_trip['tDeliveryIns'];
		$Data[0]['tPackageDetails'] = $db_trip['tPackageDetails'];
		$Data[0]['vDeliveryConfirmCode'] = $db_trip['vDeliveryConfirmCode'];
		$CancellationFare = $db_trip['fCancellationFare'];

		/*Driver Details*/
		$Data[0]['driver'] = $db_trip['DriverDetails']['vName']." ".$db_trip['DriverDetails']['vLastName'];

		/*Rider Details*/
		$Data[0]['rider'] = $db_trip['PassengerDetails']['vName']." ".$db_trip['PassengerDetails']['vLastName'];
		$Data[0]['email'] = $db_trip['PassengerDetails']['vEmail'];
		$Data[0]['vLang'] = $db_trip['PassengerDetails']['vLang'];
		

		
		/*############### language code################*/
		$user_lang_code = $Data[0]['vLang'];
        if($user_lang_code == ""){
        	$user_lang_code = "EN";
        }		

		$vLabel_user_mail = array();	
		$sql="select vLabel,vValue from language_label where vCode='".$user_lang_code."'";
		$db_lbl=$obj->MySQLSelect($sql);
		    
		    foreach ($db_lbl as $key => $value) {
		    	$vLabel_user_mail[$value['vLabel']] = $value['vValue'];	           
		}
		
		/*Language Label Other*/
		$sql="select vLabel,vValue from language_label_other where vCode='".$user_lang_code."'";
		$db_lbl=$obj->MySQLSelect($sql);
		foreach ($db_lbl as $key => $value) {
			$vLabel_user_mail[$value['vLabel']] = $value['vValue'];
		}

		$eUnit = $db_trip['vCountryUnitRider'];
		$tripDistance = $db_trip['fDistance'];
		if($eUnit == "Miles"){
			$DisplayDistanceTxt = $vLabel_user_mail['LBL_MILE_DISTANCE_TXT']; 
			$tripDistanceDisplay =round($tripDistance * 0.621371, 2);
			}else{
			$DisplayDistanceTxt = $vLabel_user_mail['LBL_KM_DISTANCE_TXT'];
			$tripDistanceDisplay = $tripDistance;
		}


   $mailcont_member_trips_img ='';
	 if($APP_TYPE == 'UberX' && ($db_trip['vBeforeImage'] != '' || $db_trip['vAfterImage'] != '')){

		 	$mailcont_member_trips_img='<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;color:#959595;line-height:14px;padding:0">
				<tbody>
			<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">';
			if($db_trip['vBeforeImage'] != '') {
			$mailcont_member_trips_img .='<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell!important;width:33%!important;line-height:16px;padding:6px 10px 10px" align="center" valign="top">
					<span style="font-size:9px;text-transform:uppercase">'.$vLabel_user_mail['LBL_SERVICE_BEFORE_TXT_ADMIN'].'</span><br>
					<span style="font-size:13px;color:#111125;font-weight:normal">
					<a href="'.$db_trip['vBeforeImage'].'" target="_blank"><img src="'.$db_trip['vBeforeImage'].'"  style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left" height="100" width="100" class="CToWUd"></a>
					</span>
				</td>';
			} 
			if($db_trip['vAfterImage'] != '') {
			$mailcont_member_trips_img .='<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell!important;width:33%!important;line-height:16px;padding:6px 10px 10px" align="center" valign="top">
					<span style="font-size:9px;text-transform:uppercase">'.$vLabel_user_mail['LBL_SERVICE_AFTER_TXT_ADMIN'].'</span><br>
					<span style="font-size:13px;color:#111125;font-weight:normal">
					<a href="'.$db_trip['vAfterImage'].'" target="_blank"><img src="'.$db_trip['vAfterImage'].'"  style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left" height="100" width="100" class="CToWUd"></a>
					</span>
				</td>';
			}
			$mailcont_member_trips_img .='</tr></tbody></table>';

	 }

		$border_tbl = "border-bottom-width:1px;border-bottom-color:#f0f0f0;border-bottom-style:solid;";
		$mailcont_member_trips ='';
		if($db_trip['eType'] == 'Deliver' && $APP_DELIVERY_MODE != 'Multi') {		
			
			$mailcont_member_trips='
				 <table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;padding:0">
						<tbody>
							<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
								<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
									<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
									</p>
								</td>
								<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:center;display:table-cell;width:120px!important;font-size:11px;white-space:pre-wrap;padding:12px 10px 5px" align="center" valign="middle">'.$vLabel_user_mail['LBL_DELIVERY_DETAILS_ADMIN'].'</td>
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
								'.$vLabel_user_mail['LBL_RECEIVER_NAME'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['vReceiverName'].'</td>
						</tr>
						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
								'.$vLabel_user_mail['LBL_RECEIVER_MOBILE'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['vReceiverMobile'].'</td>
						</tr>

						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
								'.$vLabel_user_mail['LBL_PICK_UP_INS'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['tPickUpIns'].'</td>
						</tr>						

						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
								'.$vLabel_user_mail['LBL_DELIVERY_INS'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['tDeliveryIns'].'</td>
						</tr>
						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
								'.$vLabel_user_mail['LBL_PACKAGE_DETAILS'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['tPackageDetails'].'</td>
						</tr>

						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
								'.$vLabel_user_mail['LBL_DELIVERY_CONFIRMATION_CODE_TXT'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['vDeliveryConfirmCode'].'</td>
						</tr>				

					</tbody>
				</table>';

		}
		
		if($db_trip['eType'] == 'Deliver' && $APP_DELIVERY_MODE == 'Multi'){		
		$border_tbl="";
		$mailcont_member_trips_multi ='';
		$sql1 = "SELECT * FROM trips_delivery_locations AS tdl WHERE iTripId = '".$iTripId."'";
		$db_trips_locations = $obj->MySQLSelect($sql1);
		$mailcont_member_trips_multi .='<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;padding:0;max-width:640px;border-bottom-width:1px;border-bottom-color:#f0f0f0;border-bottom-style:solid;"><tr><td>';	
			if(!empty($db_trips_locations)) {
				$i = 1;
				foreach($db_trips_locations as $dtls) {
				$mailcont_member_trips_multi .='
				 <table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;padding:0;max-width:640px;">
						<tbody>
							<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
								<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
									<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
									</p>
								</td>
									<td class="aaa" style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:center;display:table-cell;width:120px!important;font-size:11px;white-space:pre-wrap;padding:12px 10px 5px" align="center" valign="middle">'.$vLabel_user_mail['LBL_RECIPIENT_LIST_TXT']."&nbsp;".$i.'</td>
								<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
									<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
									</p>
								</td>
							</tr>
						</tbody>
						</table>';
				$mailcont_member_trips_multi .= '<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;margin-top:15px;width:100%;padding:0;max-width:640px;">
					<tbody>
						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">
								'.$vLabel_user_mail['LBL_RECIPIENT_NAME_HEADER_TXT'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-size:15px;" align="right" valign="top">'.$dtls['vReceiverName'].'</td>
						</tr>
						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">
								'.$vLabel_user_mail['LBL_DROP_OFF_LOCATION_RIDE_DETAIL'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-size:15px;" align="right" valign="top">'.$dtls['tPickUpIns'].",".$dtls['tDaddress'].'</td>
						</tr>				

						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">
								'.$vLabel_user_mail['LBL_DELIVERY_INS'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-size:15px;" align="right" valign="top">'.$dtls['tDeliveryIns'].'</td>
						</tr>
						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">
								'.$vLabel_user_mail['LBL_PACKAGE_DETAILS'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-size:15px;" align="right" valign="top">'.$dtls['tPackageDetails'].'</td>
						</tr>

						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">
								'.$vLabel_user_mail['LBL_DELIVERY_STATUS_TXT'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-weight:bold;font-size:15px;" align="right" valign="top">'.$dtls['iActive'].'</td>
						</tr>';
						if(!empty($dtls['vSignImage'])) {		
						$mailcont_member_trips_multi .='<tr>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">'.$vLabel_user_mail['LBL_RECEIVER_SIGN'].'</td>';
						if(file_exists($tconfig["tsite_upload_trip_signature_images_path"]. '/'. $dtls['vSignImage'])){
							$sign1=$tconfig["tsite_upload_trip_signature_images"]. '/' .$dtls['vSignImage'];
						}
						$mailcont_member_trips_multi .='<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-weight:bold;font-size:15px;" align="right" valign="top"><img src="'.$sign1.'" style="width:100px;"></td>
						</tr>';
						}	
					$mailcont_member_trips_multi .= '</tbody>
				</table>';
				$i++;
				} 
			}
			$mailcont_member_trips_multi .= '</td></tr></table>';
		}
		
		/*Rating*/
		$Data[0]['vRating'] = $db_trip['TripRating'];

		/*Profile img*/
		if(file_exists($tconfig["tsite_upload_images_driver_path"]. '/' . $db_trip['DriverDetails']['iDriverId'] . '/2_' . $db_trip['DriverDetails']['vImage'])){
			$img=$tconfig["tsite_upload_images_driver"]. '/' . $db_trip['DriverDetails']['iDriverId'] . '/2_' .$db_trip['DriverDetails']['vImage'];
		} else {
			$img=$tconfig["tsite_url"]."webimages/icons/help/driver.png";
		}

		if(!empty($db_trip['vVehicleCategory'])){
		 	$car = $db_trip['vVehicleCategory'] . "-" . $db_trip['vVehicleType'];
		} else {
			$car = $db_trip['vVehicleType'];
		} 	

		$ridenum = $db_trip['vRideNo'];
		$Data[0]['CurrencySymbol']=$db_trip['CurrencySymbol'];
		$Data[0]['ProjectName'] = $generalobj->getConfigurations("configurations","SITE_NAME");
		$Data[0]['ProjectName1'] ='<img class="logo" src="'.$tconfig["tsite_home_images"].'logo.png" alt="">';
		$Data[0]['car'] = $car;
		$Data[0]['ridenum'] = $ridenum;
		$Data[0]['total_amt'] = $db_trip['HistoryFareDetailsArr'][$vLabel_user_mail['LBL_SUBTOTAL_TXT']];
		$Data[0]['TripTimeInMinutes'] = $db_trip['TripTimeInMinutes'];

		$sql = "SELECT * from configurations where vName = 'COPYRIGHT_TEXT'";
		$copy = $obj->MySQLSELECT($sql);
		$Data[0]['copyright'] = $copy[0]['vValue'];

		$systemTimeZone = date_default_timezone_get();
		if(!empty($db_trip['vTimeZone'])){
			$starttime = converToTz($db_trip['tTripRequestDateOrig'],$db_trip['vTimeZone'],$systemTimeZone);
		    $endDate = converToTz($db_trip['tEndDate'],$db_trip['vTimeZone'],$systemTimeZone);
		} else {
			$starttime = $db_trip['tTripRequestDateOrig'];
			$endDate = $db_trip['tEndDate'];
		}
		$start_time = $generalobj->DateTime($starttime,18);
		$endtime = $generalobj->DateTime($endDate,18);

	    $kms = $db_trip['fDistance'];
	    if($db_trip['fCancellationFare'] > 0){
			$Data[0]['start_time'] = $endtime;
	    } else {
	    	$Data[0]['start_time'] = $start_time;
	    }

		$Data[0]['endtime'] = $endtime;
		$Data[0]['kms'] = $kms;

		$disp_km_txt ='';
		if($APP_TYPE != 'UberX'){

			$disp_km_txt ='<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell!important;width:33%!important;line-height:16px;padding:6px 10px 10px" align="center" valign="top">
				<span style="font-size:9px;text-transform:uppercase">'.$DisplayDistanceTxt.'</span><br><span style="font-size:13px;color:#111125;font-weight:normal">'.$tripDistanceDisplay.'</span>
			</td>';
		}

		$job_time ='';
		if($APP_TYPE != 'UberX'){
		$job_time = '<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell!important;width:33%!important;line-height:16px;padding:6px 10px 10px" align="center" valign="top"><span style="font-size:9px;text-transform:uppercase">'.$vLabel_user_mail['LBL_TRIP_TIME_TXT_ADMIN'].'</span><br><span style="font-size:13px;color:#111125;font-weight:normal"><span class="aBn" data-term="goog_43159642" tabindex="0"><span class="aQJ">'.$Data[0]['TripTimeInMinutes'].'</span></span></span></td>';
		}

		$email_con_location ='';
		if($APP_TYPE != 'UberX'){
			$img_route = "";
			if($APP_DELIVERY_MODE != "Multi" || $db_trip['eType'] == "Ride"){
				$img_route = '<img src="'.$tconfig["tsite_url"].'webimages/icons/help/route_line.png" style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left" height="80" width="13" class="CToWUd">';
			}else{	
				$img_route = '<img src="'.$tconfig["tsite_url"].'webimages/icons/help/green-lolo.png" style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left" width="13" class="CToWUd">';
			}
			$email_con_location ='<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
			<td rowspan="2" style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:17px!important;padding:3px 10px 10px 17px" align="left" valign="top">
				'.$img_route.'
			</td>
		
			<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:279px;line-height:16px;height:57px;padding:0 10px 10px 0" align="left" valign="top">
				<span style="font-size:15px;font-weight:500;color:#000000!important">
					<span class="aBn" data-term="goog_43159640" tabindex="0">

						<span class="aQJ"><a href="#" style="font-size:15px;font-weight:600;color:#000000!important;text-decoration:none;">'.$Data[0]['start_time'].'</a></span>
					</span>
				</span>
				<br>
				<span><a href="#" style="font-size:11px;color:#999999!important;line-height:16px;text-decoration:none">'.$Data[0]['slocation'].'</a></span>
			</td>
		</tr>';
		if($APP_DELIVERY_MODE != "Multi" || $db_trip['eType'] == "Ride"){
			$email_con_location.='<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">

				<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:279px;line-height:16px;height:auto;padding:0 0px 0 0" align="left" valign="top">
					<span style="font-size:15px;font-weight:500;color:#000000!important">
						<span class="aBn" data-term="goog_43159641" tabindex="0">

							<span class="aQJ"><a href="#" style="font-size:15px;font-weight:600;color:#000000!important;text-decoration:none;">'.$Data[0]['endtime'].'</a></span>
						</span>
					</span><br>
					<span><a href="#" style="font-size:11px;color:#999999!important;line-height:16px;text-decoration:none">'.$Data[0]['elocation'].'</a></span>
				</td>
			</tr>';
		}

		} else {

			$email_con_location ='<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left"><td rowspan="2" style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:17px!important;padding:3px 10px 10px 17px" align="left" valign="top">
				<img src="'.$tconfig["tsite_url"].'webimages/icons/help/green-lolo.png" style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left"  width="13" class="CToWUd">
			</td>			
		</tr>
		<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">

			<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:279px;line-height:16px;height:auto;padding:0 0px 0 0" align="left" valign="top">
				<span style="font-size:15px;font-weight:500;color:#000000!important">
					<span class="aBn" data-term="goog_43159641" tabindex="0">

						<span class="aQJ"><a href="#" style="font-size:15px;font-weight:600;color:#000000!important;text-decoration:none;">'.$Data[0]['start_time'].'</a></span>
					</span>
				</span><br>
				<span><a href="#" style="font-size:11px;color:#999999!important;line-height:16px;text-decoration:none">'.$Data[0]['slocation'].'</a></span>
			</td>
		</tr>';
		}
		
		$tripDeleteByDriverStatus='';
		if($db_trip['eCancelled']=="Yes"){
			$tripDeleteByDriverStatus = '<table style="border-spacing:0;border-collapse:collapse;vertical-align:middle;text-align:left;width:auto;padding:0">
					<tbody>
						<tr style="vertical-align:middle;text-align:left;width:100%;padding:0" align="left">
							
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:100%;padding:5px 5px 5px" align="left" valign="middle">
								<span style="padding-bottom:5px;display:inline-block">This trip has been canceled by driver. Reason: '.$db_trip['vCancelReason'].'</span>
							</td>
						</tr>
					</tbody>
				</table>';
		}
		
		$invoice = '<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;padding:0">
			<tbody>
			<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
				<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
					<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
					</p>
				</td>
				<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:center;display:table-cell;width:120px!important;font-size:11px;white-space:pre-wrap;padding:12px 10px 5px" align="center" valign="middle">'.$vLabel_user_mail['LBL_FARE_BREAKDOWN'].'</td>
				<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
					<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
					</p>
				</td>
			</tr>
			</tbody>
			</table><table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;margin-top:15px;width:auto;padding:0"><tbody>';

				foreach ($db_trip['HistoryFareDetailsNewArr'] as $key => $value) {									
					foreach ($value as $k => $val) {
						if($k == $vLabel_user_mail['LBL_EARNED_AMOUNT']) {
							continue;
						} else if($k == $vLabel_user_mail['LBL_SUBTOTAL_TXT']){
							continue;
						} else { 
							$invoice .= '<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left"><td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">'.$k.'</td><td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$val.'</td></tr>';
					}
					}
				}
			$invoice .=	'<tr style="vertical-align:top;text-align:left;font-weight:bold;width:100%;padding:0;vertical-align:top;text-align:left;border-top:1px;border-top-width:1px;border-top-color:#f0f0f0;border-top-style:solid;" align="left">
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#111125;padding:5px 4px 4px" align="left" valign="top">'.$vLabel_user_mail['LBL_SUBTOTAL_TXT'].'</td>
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:5px 4px 4px" align="right" valign="top">'.$db_trip['HistoryFareDetailsArr'][$vLabel_user_mail['LBL_SUBTOTAL_TXT']].'</td>
					</tr>';
			if($db_trip['fTipPrice'] !="" && $db_trip['fTipPrice'] !="0" && $db_trip['fTipPrice'] !="0.00")
			{
			$invoice .= '<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
				<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
					'.$vLabel_user_mail['LBL_TIP_GIVEN_TXT'].'
				</td>
				<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$db_trip['fTipPrice'].'</td>
			</tr>';
			}
			$paymentMode = ($db_trip['vTripPaymentMode'] == 'Cash')? $vLabel_user_mail['LBL_VIA_CASH_TXT']: $vLabel_user_mail['LBL_VIA_CARD_TXT'];
			$invoice .=  '<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;line-height:18px;padding:5px 4px" align="left" valign="top">
							<span style="font-size:9px;line-height:7px">'.$vLabel_user_mail['LBL_CHARGED_TXT'].'</span>
							<br>

							<img src="'.paymentimg($db_trip['vTripPaymentMode']).'" style="outline:none;text-decoration:none;float:left;clear:both;display:block;width:40px!important;min-height:25px;margin-right:5px;margin-top:3px" align="left" height="12" width="17" >
							<span style="font-size:13px">
								'.$paymentMode.'
							</span>
						</td>
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;font-size:19px;font-weight:bold;line-height:30px;padding:20px 4px 5px" align="right" valign="top">
							'.$db_trip['HistoryFareDetailsArr'][$vLabel_user_mail['LBL_SUBTOTAL_TXT']].'
						</td>
					</tr></tbody></table>';

		# User Email below code
		$mailcont_member =
'<div style="width:730px;!important;color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,
Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:19px;font-size:14px;margin:0;padding:0">
<table  style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;line-height:19px;font-size:14px;margin:0;padding:0"><tbody><tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left"><td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;padding:0" align="center" valign="top">
	<center style="width:100%;min-width:580px">
		<table style="border-color:#e3e3e3;border-style:solid;border-width:1px 1px 1px 1px;vertical-align:top;text-align:inherit;width:660px;margin:0 auto;padding:0"><tbody><tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left"><td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;padding:0" align="left" valign="top">
			<table style="vertical-align:top;text-align:left;width:640px;margin:0 10px;padding:0">
				<tbody >
					<!--<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;padding:28px 0" align="left" valign="top" width="127">
							
							<span style="color:white;">'.$Data[0]['ProjectName1'].'</span>
						</td>
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;font-size:11px;color:#999999;line-height:15px;text-transform:uppercase;padding:30px 0 26px" align="right" valign="top">
							<span>'.$vLabel_user_mail['LBL_RIDE_NUMBER_TXT_ADMIN'].':'.$Data[0]['ridenum'].'</span>
						</td>   
					</tr>   -->
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
												'.$Data[0]['total_amt'].'
											</span>
										</td>
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:inline-block;width:290px;border-radius:0 3px 0 0;background-color:#fafafa;padding:26px 10px 20px" align="right" bgcolor="#FAFAFA" valign="top">
										<span style="vertical-align:top;text-align:right;font-size:11px;color:#999999;text-transform:uppercase;padding-right:10px">'.$vLabel_user_mail['LBL_RIDE_TXT_ADMIN'].' Date :'.@date('d M Y',@strtotime($starttime)).'</span><br/>
											<span style="vertical-align:top;text-align:right;font-size:11px;color:#999999;text-transform:uppercase;padding-right:10px">'.$vLabel_user_mail['LBL_RIDE_TXT_ADMIN']." ".$vLabel_user_mail['LBL_NUMBER_TXT'].' :'.$Data[0]['ridenum'].'</span> <BR/>
											<span style="font-size:12px;font-weight:normal;color:#b2b2b2">'.$vLabel_user_mail['LBL_THANKS_FOR_CHOOSING_TXT_ADMIN'].' '.$Data[0]['ProjectName'].', '.$Data[0]['rider'].'</span>
										</td>
									</tr>
								</tbody>
							</table>
							<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;max-width:640px;'.$border_tbl.'padding:0">
								<tbody>
									<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:300px;padding:25px 10px 25px 5px" align="left" valign="top">
											<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;margin-left:19px;padding:0">
												<tbody>
													<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
														<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:300px;padding:0" align="left" valign="top">
															
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
																	'.$email_con_location.'
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
																			<span style="font-size:9px;text-transform:uppercase">'.$vLabel_user_mail['LBL_CAR_ADMIN'].'</span><br>
																			<span style="font-size:13px;color:#111125;font-weight:normal">'.$Data[0]['car'].'</span>
																		</td>
																		'.$disp_km_txt.'
																		'.$job_time.'
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
											'.$invoice.'
											'.$mailcont_member_trips.'
                    						'.$mailcont_member_trips_img.'	
											</span>
										</td>
									'.$mailcont_member_trips_multi.'
									</tr>
								</tbody>
							</table>
							
							'.$tripDeleteByDriverStatus.'
							
							
							<table style="border-spacing:0;border-collapse:collapse;vertical-align:middle;text-align:left;width:100%;max-width:640px;border-bottom-width:1px;border-bottom-color:#f0f0f0;border-bottom-style:solid;padding:0">
								<tbody>
									<tr style="vertical-align:middle;text-align:left;width:100%;padding:0" align="left">
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:inline-block;width:50%;padding:0px" align="left" valign="middle">
											<table style="border-spacing:0;border-collapse:collapse;vertical-align:middle;text-align:left;width:100%;max-width:640px;display:inline-block;padding:0">
												<tbody>
													<tr style="vertical-align:middle;text-align:left;width:100%;padding:0" align="left">
														<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:inline-block;width:100%!important;line-height:15px;padding:0px 0px 0px 10px" align="left" valign="middle">
															<table style="border-spacing:0;border-collapse:collapse;vertical-align:middle;text-align:left;width:auto;padding:0">
																<tbody>
																	<tr style="vertical-align:middle;text-align:left;width:100%;padding:0" align="left">
																		<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:45px;padding:5px 0px 5px" align="left" valign="middle">
																			<img src="'.$img.'" style="outline:none;text-decoration:none;float:left;clear:both;display:inline-block;width:45px!important;min-height:45px!important;border-radius:50em;margin-left:15px;max-width:45px!important;min-width:45px!important;border:1px solid #d7d7d7" align="left" height="45" width="45" class="CToWUd">
																		</td>
																		<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:300px;padding:5px 5px 5px" align="left" valign="middle">
																			<span style="padding-bottom:5px;display:inline-block;font-size:15px;">'.$vLabel_user_mail['LBL_You_ride_with'].' '.$Data[0]['driver'].'</span>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:inline-block;width:49%;height:100%;padding:0px 0px 0px" align="left" valign="middle">
											<table style="border-spacing:0;border-collapse:collapse;vertical-align:middle;text-align:left;width:100%;max-width:640px;display:block;">
												<tbody style="width:100%;display:block">
													<tr style="vertical-align:middle;text-align:right;width:100%;display:block;padding:0px" align="right">
														<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:inline;font-size:12px;color:#808080;text-transform:uppercase;padding:0px 5px 0px 0px" align="left" valign="middle">
															<span>'.$vLabel_user_mail['LBL_TRIP_RATING_TXT'].'</span>
														</td>
														<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:inline-block" align="left" valign="middle">
															<b style="font-size:11px;display:inline-block!important;padding:0px 2px">'.ratingmark($Data[0]['vRating']).'</b>
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
	</div>';
			//echo $mailcont_member;exit;
			 $maildata_member['details'] = $mailcont_member;
			$maildata_member['email'] = $Data[0]['email'];


	  return $generalobj->send_email_user("RIDER_INVOICE",$maildata_member);
	}
	
	####################### for email receipt end ##########################
	
	####################### for email receipt to admin ##########################
	
	function sendTripReceiptAdmin($iTripId){
			global $obj,$generalobj,$tconfig,$APP_TYPE,$DEFAULT_DISTANCE_UNIT;
			$APP_DELIVERY_MODE = $generalobj->getConfigurations("configurations","APP_DELIVERY_MODE");
			$Data = array();
/*			$sql = "SELECT * FROM trips WHERE iTripId = ".$iTripId;
			$db_trip = $obj->MySQLSelect($sql);
*/			//echo "<pre>";print_r($db_trip);echo "</pre>";

      		$db_trip = $generalobj->getTripPriceDetailsForWeb($iTripId,'','');
			$Data[0]['elocation'] =$db_trip['tDaddress'];
			$Data[0]['slocation'] =$db_trip['tSaddress'];
			$Data[0]['tStartLat'] = $db_trip['tStartLat'];
			$Data[0]['tStartLong'] = $db_trip['tStartLong'];
			$Data[0]['tEndLat'] = $db_trip['tEndLat'];
			$Data[0]['tEndLong'] = $db_trip['tEndLong'];
			$Data[0]['vReceiverName'] = $db_trip['vReceiverName'];
			$Data[0]['vReceiverMobile'] = $db_trip['vReceiverMobile'];
			$Data[0]['tPickUpIns'] = $db_trip['tPickUpIns'];
			$Data[0]['tDeliveryIns'] = $db_trip['tDeliveryIns'];
			$Data[0]['tPackageDetails'] = $db_trip['tPackageDetails'];
			$Data[0]['vDeliveryConfirmCode'] = $db_trip['vDeliveryConfirmCode'];
			$CancellationFare = $db_trip['fCancellationFare'];

			$sql="select vCode from language_master where eStatus = 'Active' and eDefault='Yes'";
			$data_lang =$obj->MySQLSelect($sql);
			$def_lang = $data_lang[0]['vCode'];
			
			$vLabel_admin_mail = array();	
			$sql1="select vLabel,vValue from language_label where vCode='$def_lang'";
			$db_lbl_admin=$obj->MySQLSelect($sql1);
			    
			    foreach ($db_lbl_admin as $key => $value) {
			    	$vLabel_admin_mail[$value['vLabel']] = $value['vValue'];	           
			}

			/*Language Label Other*/
			$sql2="select vLabel,vValue from language_label_other where vCode='$def_lang'";
			// $sql2="select vLabel,vValue,vCode from language_label_other where eStatus = 'Active' and eDefault='Yes'";
			$db_lbl_admin=$obj->MySQLSelect($sql2);
			foreach ($db_lbl_admin as $key => $value) {
				$vLabel_admin_mail[$value['vLabel']] = $value['vValue'];
			}
			
			$eUnit = $DEFAULT_DISTANCE_UNIT;
			$tripDistance = $db_trip['fDistance'];
			
			if($eUnit == "Miles"){
				$DisplayDistanceTxt = $vLabel_admin_mail['LBL_MILE_DISTANCE_TXT']; 
				$tripDistanceDisplay =round($tripDistance * 0.621371, 2);
			} else {
				$DisplayDistanceTxt = $vLabel_admin_mail['LBL_KM_DISTANCE_TXT'];
				$tripDistanceDisplay = $tripDistance;
			}


			
			// echo "<pre>"; print_r($vLabel_admin_mail); exit;
    	 $mailcont_member_trips_img ='';
		 if($APP_TYPE == 'UberX' && ($db_trip['vBeforeImage'] != '' || $db_trip['vAfterImage'] != '')){

		 	$mailcont_member_trips_img ='<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;color:#959595;line-height:14px;padding:0">
				<tbody>
			<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">';
			if($db_trip['vBeforeImage'] != '') {
			$mailcont_member_trips_img .='<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell!important;width:33%!important;line-height:16px;padding:6px 10px 10px" align="center" valign="top">
					<span style="font-size:9px;text-transform:uppercase">'.$vLabel_admin_mail['LBL_SERVICE_BEFORE_TXT_ADMIN'].'</span><br>
					<span style="font-size:13px;color:#111125;font-weight:normal">
					<a href="'.$db_trip['vBeforeImage'].'" target="_blank"><img src="'.$db_trip['vBeforeImage'].'"  style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left" height="100" width="100" class="CToWUd"></a>
					</span>
				</td>';
			} 
			if($db_trip['vAfterImage'] != '') {
			$mailcont_member_trips_img .='<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell!important;width:33%!important;line-height:16px;padding:6px 10px 10px" align="center" valign="top">
					<span style="font-size:9px;text-transform:uppercase">'.$vLabel_admin_mail['LBL_SERVICE_AFTER_TXT_ADMIN'].'</span><br>
					<span style="font-size:13px;color:#111125;font-weight:normal">
					<a href="'.$db_trip['vAfterImage'].'" target="_blank"><img src="'.$db_trip['vAfterImage'].'"  style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left" height="100" width="100" class="CToWUd"></a>
					</span>
				</td>';
			}
				$mailcont_member_trips_img .='</tr></tbody></table>';

		}			
		
		$border_tbl = "border-bottom-width:1px;border-bottom-color:#f0f0f0;border-bottom-style:solid;";
		$mailcont_member_trips ='';
		if($db_trip['eType'] == 'Deliver' && $APP_DELIVERY_MODE != 'Multi'){		

			$mailcont_member_trips='
				 <table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;padding:0">
						<tbody>
							<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
								<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
									<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
									</p>
								</td>
								<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:center;display:table-cell;width:120px!important;font-size:11px;white-space:pre-wrap;padding:12px 10px 5px" align="center" valign="middle">'.$vLabel_admin_mail['LBL_DELIVERY_DETAILS_ADMIN'].'</td>
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
								'.$vLabel_admin_mail['LBL_RECEIVER_NAME'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['vReceiverName'].'</td>
						</tr>
						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
								'.$vLabel_admin_mail['LBL_RECEIVER_MOBILE'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['vReceiverMobile'].'</td>
						</tr>

						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
								'.$vLabel_admin_mail['LBL_PICK_UP_INS'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['tPickUpIns'].'</td>
						</tr>						

						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
								'.$vLabel_admin_mail['LBL_DELIVERY_INS'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['tDeliveryIns'].'</td>
						</tr>
						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
								'.$vLabel_admin_mail['LBL_PACKAGE_DETAILS'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['tPackageDetails'].'</td>
						</tr>

						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
								'.$vLabel_admin_mail['LBL_DELIVERY_CONFIRMATION_CODE_TXT'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$Data[0]['vDeliveryConfirmCode'].'</td>
						</tr>				

					</tbody>
				</table>';

		}

		if($db_trip['eType'] == 'Deliver' && $APP_DELIVERY_MODE == 'Multi'){		
		$border_tbl="";
		$mailcont_member_trips_multi ='';
		$sql1 = "SELECT * FROM trips_delivery_locations AS tdl WHERE iTripId = '".$iTripId."'";
		$db_trips_locations = $obj->MySQLSelect($sql1);
		$mailcont_member_trips_multi .='<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;padding:0;max-width:640px;border-bottom-width:1px;border-bottom-color:#f0f0f0;border-bottom-style:solid;"><tr><td>';	
			if(!empty($db_trips_locations)) {
				$i = 1;
				foreach($db_trips_locations as $dtls) {
				$mailcont_member_trips_multi .='
				 <table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;padding:0;max-width:640px;">
						<tbody>
							<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
								<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
									<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
									</p>
								</td>
									<td class="aaa" style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:center;display:table-cell;width:120px!important;font-size:11px;white-space:pre-wrap;padding:12px 10px 5px" align="center" valign="middle">'.$vLabel_admin_mail['LBL_RECIPIENT_LIST_TXT']."&nbsp;".$i.'</td>
								<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
									<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
									</p>
								</td>
							</tr>
						</tbody>
						</table>';
				$mailcont_member_trips_multi .= '<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;margin-top:15px;width:100%;padding:0;max-width:640px;">
					<tbody>
						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">
								'.$vLabel_admin_mail['LBL_RECIPIENT_NAME_HEADER_TXT'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-size:15px;" align="right" valign="top">'.$dtls['vReceiverName'].'</td>
						</tr>
						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">
								'.$vLabel_admin_mail['LBL_DROP_OFF_LOCATION_RIDE_DETAIL'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-size:15px;" align="right" valign="top">'.$dtls['tPickUpIns'].",".$dtls['tDaddress'].'</td>
						</tr>				

						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">
								'.$vLabel_admin_mail['LBL_DELIVERY_INS'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-size:15px;" align="right" valign="top">'.$dtls['tDeliveryIns'].'</td>
						</tr>
						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">
								'.$vLabel_admin_mail['LBL_PACKAGE_DETAILS'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-size:15px;" align="right" valign="top">'.$dtls['tPackageDetails'].'</td>
						</tr>

						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">
								'.$vLabel_admin_mail['LBL_DELIVERY_STATUS_TXT'].'
							</td>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-weight:bold;font-size:15px;" align="right" valign="top">'.$dtls['iActive'].'</td>
						</tr>';
						if(!empty($dtls['vSignImage'])) {		
						$mailcont_member_trips_multi .='<tr>
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;min-width: 300px;color:#808080;padding:4px;font-size:15px;font-weight:bold;" align="left" valign="top">'.$vLabel_admin_mail['LBL_RECEIVER_SIGN'].'</td>';
						if(file_exists($tconfig["tsite_upload_trip_signature_images_path"]. '/'. $dtls['vSignImage'])){
							$sign1=$tconfig["tsite_upload_trip_signature_images"]. '/' .$dtls['vSignImage'];
						}
						$mailcont_member_trips_multi .='<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:300px;min-width: 300px;padding:4px;font-weight:bold;font-size:15px;" align="right" valign="top"><img src="'.$sign1.'" style="width:100px;"></td>
						</tr>';
						}	
					$mailcont_member_trips_multi .= '</tbody>
				</table>';
				$i++;
				} 
			}
			$mailcont_member_trips_multi .= '</td></tr></table>';
		}
		/*Driver Details*/
		$Data[0]['driver'] = $db_trip['DriverDetails']['vName']." ".$db_trip['DriverDetails']['vLastName'];

		/*Rider Details*/
		$Data[0]['rider'] = $db_trip['PassengerDetails']['vName']." ".$db_trip['PassengerDetails']['vLastName'];
		$Data[0]['email'] = $db_trip['PassengerDetails']['vEmail'];

		/*Rating*/
		$Data[0]['vRating'] = $db_trip['TripRating'];


		if(file_exists($tconfig["tsite_upload_images_driver_path"]. '/' . $db_trip['DriverDetails']['iDriverId'] . '/2_' . $db_trip['DriverDetails']['vImage'])){
			$img=$tconfig["tsite_upload_images_driver"]. '/' . $db_trip['DriverDetails']['iDriverId'] . '/2_' .$db_trip['DriverDetails']['vImage'];
		}else{
			$img=$tconfig["tsite_url"]."webimages/icons/help/driver.png";
		}

		if(file_exists($tconfig["tsite_upload_images_passenger_path"]. '/' . $db_trip['PassengerDetails']['iUserId'] . '/2_' . $db_trip['PassengerDetails']['vImgName'])){
			$img1=$tconfig["tsite_upload_images_passenger"]. '/' . $db_trip['PassengerDetails']['iUserId'] . '/2_' .$db_trip['PassengerDetails']['vImgName'];
		}else{
			$img1=$tconfig["tsite_url"]."webimages/icons/help/taxi_passanger.png";
		}


		$Data[0]['user']=$db_trip['PassengerDetails']['vName']." ".$db_trip['PassengerDetails']['vLastName'];
		$Data[0]['email']== $db_trip['DriverDetails']['vEmail'];
		$Data[0]['uEmail']=$db_trip['PassengerDetails']['vEmail'];

		if(!empty($db_trip['vVehicleCategory'])){
		 	$car = $db_trip['vVehicleCategory'] . "-" . $db_trip['vVehicleType'];
		} else {
			$car = $db_trip['vVehicleType'];
		}

		$payment_mode = $db_trip['vTripPaymentMode'];
		$ridenum = $db_trip['vRideNo'];
		$Data[0]['CurrencySymbol']=$db_trip['CurrencySymbol'];
		$Data[0]['ProjectName'] = $generalobj->getConfigurations("configurations","SITE_NAME");
		$Data[0]['ProjectName1'] ='<img class="logo" src="'.$tconfig["tsite_home_images"].'logo.png" alt="">';
		$Data[0]['total_amt'] = $db_trip['HistoryFareDetailsArr'][$vLabel_admin_mail['LBL_EARNED_AMOUNT']];
		$Data[0]['payment_mode'] = $payment_mode;
		$Data[0]['ridenum'] = $ridenum;
		$Data[0]['car'] = $car;

		$sql = "SELECT * from configurations where vName = 'COPYRIGHT_TEXT'";
		$copy = $obj->MySQLSELECT($sql);
		$Data[0]['copyright'] = $copy[0]['vValue'];

		$starttime = $db_trip['tTripRequestDateOrig'];
		$endDate = $db_trip['tEndDate'];
		$start_time = $generalobj->DateTime($starttime,18);
		$endtime = $generalobj->DateTime($endDate,18);
	    $kms = $db_trip['fDistance'];
	    if($db_trip['fCancellationFare'] > 0){
			$Data[0]['start_time'] = $endtime;
	    } else {
	    	$Data[0]['start_time'] = $start_time;
	    }
		$Data[0]['endtime'] = $endtime;
		$Data[0]['kms'] = $kms;
      
      $disp_km_txt ='';
		if($APP_TYPE != 'UberX'){

			$disp_km_txt ='<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell!important;width:33%!important;line-height:16px;padding:6px 10px 10px" align="center" valign="top">
				<span style="font-size:9px;text-transform:uppercase">'.$DisplayDistanceTxt.'</span><br><span style="font-size:13px;color:#111125;font-weight:normal">'.$tripDistanceDisplay.'</span>
			</td>';
		}

		$job_time = '';
		if($APP_TYPE != 'UberX') {
		$job_time = '<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;display:table-cell!important;width:33%!important;line-height:16px;padding:6px 10px 10px" align="center" valign="top"><span style="font-size:9px;text-transform:uppercase">'.$vLabel_admin_mail['LBL_TRIP_TIME_TXT_ADMIN'].'</span><br><span style="font-size:13px;color:#111125;font-weight:normal"><span class="aBn" data-term="goog_43159642" tabindex="0"><span class="aQJ">'.$db_trip['TripTimeInMinutes'].'</span></span></span></td>';
		}

		$email_con_location ='';
		if($APP_TYPE != 'UberX'){
			$img_route = "";
			if($APP_DELIVERY_MODE != "Multi" || $db_trip['eType'] == "Ride"){
				$img_route = '<img src="'.$tconfig["tsite_url"].'webimages/icons/help/route_line.png" style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left" height="80" width="13" class="CToWUd">';
			}else{	
				$img_route = '<img src="'.$tconfig["tsite_url"].'webimages/icons/help/green-lolo.png" style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left" width="13" class="CToWUd">';
			}
			$email_con_location ='<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
			<td rowspan="2" style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:17px!important;padding:3px 10px 10px 17px" align="left" valign="top">
				'.$img_route.'
			</td>
		
			<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:279px;line-height:16px;height:57px;padding:0 10px 10px 0" align="left" valign="top">
				<span style="font-size:15px;font-weight:500;color:#000000!important">
					<span class="aBn" data-term="goog_43159640" tabindex="0">

						<span class="aQJ"><a href="#" style="font-size:15px;font-weight:600;color:#000000!important;text-decoration:none;">'.$Data[0]['start_time'].'</a></span>
					</span>
				</span>
				<br>
				<span><a href="#" style="font-size:11px;color:#999999!important;line-height:16px;text-decoration:none">'.$Data[0]['slocation'].'</a></span>
			</td>
		</tr>';
			if($APP_DELIVERY_MODE != "Multi" || $db_trip['eType'] == "Ride"){
				$email_con_location.='<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">

					<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:279px;line-height:16px;height:auto;padding:0 0px 0 0" align="left" valign="top">
						<span style="font-size:15px;font-weight:500;color:#000000!important">
							<span class="aBn" data-term="goog_43159641" tabindex="0">

								<span class="aQJ"><a href="#" style="font-size:15px;font-weight:600;color:#000000!important;text-decoration:none;">'.$Data[0]['endtime'].'</a></span>
							</span>
						</span><br>
						<span><a href="#" style="font-size:11px;color:#999999!important;line-height:16px;text-decoration:none">'.$Data[0]['elocation'].'</a></span>
					</td>
				</tr>';
			}
		}else{

			$email_con_location ='<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left"><td rowspan="2" style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:17px!important;padding:3px 10px 10px 17px" align="left" valign="top">
				<img src="'.$tconfig["tsite_url"].'webimages/icons/help/green-lolo.png" style="outline:none;text-decoration:none;float:left;clear:both;display:block" align="left"  width="13" class="CToWUd">
			</td>			
		</tr>
		<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">

			<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:279px;line-height:16px;height:auto;padding:0 0px 0 0" align="left" valign="top">
				<span style="font-size:15px;font-weight:500;color:#000000!important">
					<span class="aBn" data-term="goog_43159641" tabindex="0">

						<span class="aQJ"><a href="#" style="font-size:15px;font-weight:600;color:#000000!important;text-decoration:none;">'.$Data[0]['start_time'].'</a></span>
					</span>
				</span><br>
				<span><a href="#" style="font-size:11px;color:#999999!important;line-height:16px;text-decoration:none">'.$Data[0]['slocation'].'</a></span>
			</td>
		</tr>';
		}
			
			$tripDeleteByDriverStatus='';
			if($db_trip['eCancelled']=="Yes"){
				$tripDeleteByDriverStatus = '<table style="border-spacing:0;border-collapse:collapse;vertical-align:middle;text-align:left;width:auto;padding:0">
						<tbody>
							<tr style="vertical-align:middle;text-align:left;width:100%;padding:0" align="left">
								
								<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:100%;padding:5px 5px 5px" align="left" valign="middle">
									<span style="padding-bottom:5px;display:inline-block">This trip has been canceled by driver. Reason: '.$db_trip['vCancelReason'].'</span>
								</td>
							</tr>
						</tbody>
					</table>';
			}
			

      
		$check_hail= "";
		 if($db_trip['eHailTrip']=="Yes"){
			 $check_hail = $vLabel_admin_mail['LBL_PAYMENT_RECEIPT_TXT']." | Hail Ride";
		 }else{
			 $check_hail = $vLabel_admin_mail['LBL_PAYMENT_RECEIPT_TXT'];
		 }
		 $pass_txt= "";
		 if($db_trip['eHailTrip']=="Yes"){
			 $pass_txt = "";
			 $font_size = 'font-size:24px';
		 }else{
			 $pass_txt = '<td valign="top" align="left" style="border-collapse: collapse ! important; vertical-align: top; text-align: left; display: inline-block; width: 48%; padding-left: 10px;margin-top:10px;padding: 10px 0px 0px 10px;font-weight:bold;">'.$vLabel_admin_mail['LBL_PASSANGER_TXT_ADMIN'].'</td>';
			  $font_size = 'font-size:32px';
		 }
		 
		 $pass_detail = "";
		  if($db_trip['eHailTrip']=="Yes"){
			 $pass_detail = '';
		 }else{
			 $pass_detail = '<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:48%;padding:0px" align="left" valign="top">
			<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;max-width:640px;display:inline-block;padding:0">
				<tbody>
					<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:100%!important;line-height:15px;padding:5px 0px 0px 10px" align="left" valign="top">
							<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:auto;padding:0">
								<tbody>
									<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:45px;padding:5px 0px 5px" align="left" valign="top">
											<img src="'.$img1.'" style="outline:none;text-decoration:none;float:left;clear:both;display:inline-block;width:45px!important;min-height:45px!important;border-radius:50em;margin-left:15px;max-width:45px!important;min-width:45px!important;border:1px solid #d7d7d7" align="left" height="45" width="45" class="CToWUd">
										</td>
										<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:300px;padding:5px 5px 5px" align="left" valign="middle">
											<span style="padding-bottom:5px;display:inline-block;font-size:15px;">'.$Data[0]['user'].'</span><br/>
											<span style="padding-bottom:5px;display:inline-block;font-size:15px;">'.$Data[0]['uEmail'].'</span>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
			</td>';
		}

		$invoice = '<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%!important;padding:0">
			<tbody>
			<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
				<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
					<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
					</p>
				</td>
				<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:center;display:table-cell;width:120px!important;font-size:11px;white-space:pre-wrap;padding:12px 10px 5px" align="center" valign="middle">'.$vLabel_admin_mail['LBL_FARE_BREAKDOWN'].'</td>
				<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:auto!important;padding:12px 0 5px" align="left" valign="middle">
					<p style="color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:0;font-size:14px;border-bottom-style:solid;border-bottom-width:1px;border-bottom-color:#e3e3e3;display:block;margin:0;padding:0" align="left">
					</p>
				</td>
			</tr>
			</tbody>
			</table><table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;margin-top:15px;width:auto;padding:0"><tbody>';

				foreach ($db_trip['HistoryFareDetailsNewArr'] as $key => $value) {									
					foreach ($value as $k => $val) {
						if($k == $vLabel_admin_mail['LBL_EARNED_AMOUNT']) {
							continue;
						} else if($k == $vLabel_admin_mail['LBL_SUBTOTAL_TXT']){
							continue;
						} else { 
							$invoice .= '<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left"><td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">'.$k.'</td><td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$val.'</td></tr>';
					}
					}
				}
			$invoice .=	'<tr style="vertical-align:top;text-align:left;font-weight:bold;width:100%;padding:0;vertical-align:top;text-align:left;border-top:1px;border-top-width:1px;border-top-color:#f0f0f0;border-top-style:solid;" align="left">
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#111125;padding:5px 4px 4px" align="left" valign="top">'.$vLabel_admin_mail['LBL_SUBTOTAL_TXT'].'</td>
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:5px 4px 4px" align="right" valign="top">'.$db_trip['HistoryFareDetailsArr'][$vLabel_admin_mail['LBL_EARNED_AMOUNT']].'</td>
					</tr>';
			if($db_trip['fTipPrice'] !="" && $db_trip['fTipPrice'] !="0" && $db_trip['fTipPrice'] !="0.00")
			{
			$invoice .= '<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
				<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;padding:4px" align="left" valign="top">
					'.$vLabel_admin_mail['LBL_TIP_GIVEN_TXT'].'
				</td>
				<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;padding:4px" align="right" valign="top">'.$db_trip['fTipPrice'].'</td>
			</tr>';
			}
			$paymentMode = ($db_trip['vTripPaymentMode'] == 'Cash')? $vLabel_admin_mail['LBL_VIA_CASH_TXT']: $vLabel_admin_mail['LBL_VIA_CARD_TXT'];
			$invoice .=  '<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:300px;color:#808080;line-height:18px;padding:5px 4px" align="left" valign="top">
							<span style="font-size:9px;line-height:7px">'.$vLabel_admin_mail['LBL_CHARGED_TXT'].'</span>
							<br>

							<img src="'.paymentimg($db_trip['vTripPaymentMode']).'" style="outline:none;text-decoration:none;float:left;clear:both;display:block;width:40px!important;min-height:25px;margin-right:5px;margin-top:3px" align="left" height="12" width="17" >
							<span style="font-size:13px">
								'.$paymentMode.'
							</span>
						</td>
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;width:90px;white-space:nowrap;font-size:19px;font-weight:bold;line-height:30px;padding:20px 4px 5px" align="right" valign="top">
							'.$db_trip['HistoryFareDetailsArr'][$vLabel_admin_mail['LBL_EARNED_AMOUNT']].'
						</td>
					</tr></tbody></table>';

			# User Email below code
			$mailcont_member =
	'<div style="width:730px;!important;color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,
Lucida Grande,sans-serif;font-weight:normal;text-align:left;line-height:19px;font-size:14px;margin:0;padding:0">
<table  style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;color:#222222;font-family:HelveticaNeue-Light,Helvetica Neue Light,Helvetica Neue,Helvetica,Arial,Lucida Grande,sans-serif;font-weight:normal;line-height:19px;font-size:14px;margin:0;padding:0"><tbody><tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left"><td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:center;padding:0" align="center" valign="top">
	<center style="width:100%;min-width:580px">
		<table style="border-color:#e3e3e3;border-style:solid;border-width:1px 1px 1px 1px;vertical-align:top;text-align:inherit;width:660px;margin:0 auto;padding:0"><tbody><tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left"><td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;padding:0" align="left" valign="top">
			<table style="vertical-align:top;text-align:left;width:640px;margin:0 10px;padding:0">
				<tbody >
					<!--<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;padding:28px 0" align="left" valign="top" width="127">
							
							<span style="color:white;">'.$Data[0]['ProjectName1'].'</span>
						</td>
						<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:right;display:table-cell;font-size:11px;color:#999999;line-height:15px;text-transform:uppercase;padding:30px 0 26px" align="right" valign="top">
							<span>'.$vLabel_admin_mail['LBL_RIDE_NUMBER_TXT_ADMIN'].':'.$Data[0]['ridenum'].'</span>
						</td>   
					</tr>   -->
				</tbody>
			</table>
				<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:640px;max-width:640px;border-radius:2px;background-color:#ffffff;margin:0 10px;padding:0" bgcolor="#ffffff">
					<tbody>
						<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
							<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:100%;padding:0" align="left" valign="top">
								<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;max-width:640px;border-bottom-width:1px;border-bottom-color:#e3e3e3;border-bottom-style:solid;padding:0">
									<tbody>
										<tr style="vertical-align:top;text-align:left;width:100%;background-color:rgb(250,250,250);padding:0" align="left" bgcolor="rgb(250,250,250)">
											<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:365px;border-radius:3px 0 0 0;background-color:#fafafa;padding:26px 10px 20px" align="left" bgcolor="#FAFAFA" valign="top">
												<span style="font-weight:bold;'.$font_size.';color:#000;line-height:30px;padding-left:15px">
													'.$check_hail.'
												</span>
											</td>
											<td style="word-break:break-word;vertical-align:top;text-align:right;display:table-cell;font-size:11px;color:#999999;line-height:15px;text-transform:uppercase;padding:10px 0 26px" align="right" valign="top">
											<span style="vertical-align:top;text-align:right;font-size:11px;color:#999999;text-transform:uppercase;padding-right:10px">'.$vLabel_admin_mail['LBL_RIDE_TXT_ADMIN'].' Date :'.@date('d M Y',@strtotime($starttime)).'</span><br/>
											<span>'.$vLabel_admin_mail['LBL_RIDE_NUMBER_TXT_ADMIN'].':'.$Data[0]['ridenum'].'</span>
										</td> 
										</tr>
									</tbody>
								</table>
								<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;max-width:640px;'.$border_tbl.'padding:0">
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
																	'.$email_con_location.'
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
																				<span style="font-size:9px;text-transform:uppercase">'.$vLabel_admin_mail['LBL_CAR_ADMIN'].'</span><br>
																				<span style="font-size:13px;color:#111125;font-weight:normal">'.$Data[0]['car'].'</span>
																			</td>
																			'.$disp_km_txt.'
																			'.$job_time.'
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
												'.$invoice.'													
												'.$mailcont_member_trips.'
												'.$mailcont_member_trips_img.'
												</span>
											</td>
										</tr>
										'.$mailcont_member_trips_multi.'
									</tbody>
								</table>
								
								'.$tripDeleteByDriverStatus.'
								
								<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;max-width:640px;border-bottom-width:1px;border-bottom-color:#f0f0f0;border-bottom-style:solid;padding:0">
									<tbody>
									<tr >
									<td valign="top" align="left" style="border-collapse: collapse ! important; vertical-align: top; text-align: left; display: inline-block; padding: 10px 0px 0px 10px;margin-top:10px; width: 48%;font-weight:bold;">'.$vLabel_admin_mail['LBL_DRIVER_TXT_ADMIN'].'</td>
									'.$pass_txt.'
									</tr>
										<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
											<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:48%;padding:0px" align="left" valign="top">
												<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:100%;max-width:640px;display:inline-block;padding:0">
													<tbody>
														<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
															<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:inline-block;width:100%!important;line-height:15px;padding:5px 0px 0px 10px" align="left" valign="top">
																<table style="border-spacing:0;border-collapse:collapse;vertical-align:top;text-align:left;width:auto;padding:0">
																	<tbody>
																		<tr style="vertical-align:top;text-align:left;width:100%;padding:0" align="left">
																			<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:top;text-align:left;display:table-cell;width:45px;padding:5px 0px 5px" align="left" valign="top">
																				<img src="'.$img.'" style="outline:none;text-decoration:none;float:left;clear:both;display:inline-block;width:45px!important;min-height:45px!important;border-radius:50em;margin-left:15px;max-width:45px!important;min-width:45px!important;border:1px solid #d7d7d7" align="left" height="45" width="45" class="CToWUd">
																			</td>
																			<td style="word-break:break-word;border-collapse:collapse!important;vertical-align:middle;text-align:left;display:table-cell;width:300px;padding:5px 5px 5px" align="left" valign="middle">
																				<span style="padding-bottom:5px;display:inline-block;font-size:15px;">'.$Data[0]['driver'].'</span><br/>
																				<span style="padding-bottom:5px;display:inline-block;font-size:15px;">'.$Data[0]['email'].'</span>
																			</td>
																		</tr>
																	</tbody>
																</table>
															</td>
														</tr>
													</tbody>
												</table>
											</td>
											'.$pass_detail.'
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
			
		</table>
		</div>';
			//echo $mailcont_member;exit;
				 $sql="select vValue from configurations where vName='ADMIN_EMAIL'";
				$db_mail=$obj->MySQLSELECT($sql);
				 $maildata_member['details'] = $mailcont_member;
				$maildata_member['email'] = $db_mail[0]['vValue'];
        	//print_R($maildata_member); exit;
		  return $generalobj->send_email_user("RIDER_INVOICE",$maildata_member);
		}
	
	####################### for email receipt to admin end ##########################
		
?>		
