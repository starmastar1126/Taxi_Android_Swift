<?php 
include_once("../common.php");
$booking_date = isset($_REQUEST['booking_date'])?$_REQUEST['booking_date']:'';
$iBaseFare = isset($_REQUEST['iBaseFare'])?$_REQUEST['iBaseFare']:'';
$fPricePerKM = isset($_REQUEST['fPricePerKM'])?$_REQUEST['fPricePerKM']:'';
$fPricePerMin = isset($_REQUEST['fPricePerMin'])?$_REQUEST['fPricePerMin']:'';
$iMinFare = isset($_REQUEST['iMinFare'])?$_REQUEST['iMinFare']:'';
$fCommision = isset($_REQUEST['fCommision'])?$_REQUEST['fCommision']:'';
$tot_time = isset($_REQUEST['time'])?$_REQUEST['time']:'';
$tot_distance = isset($_REQUEST['distance'])?$_REQUEST['distance']:'';
$iCountryId = isset($_REQUEST['iCountryId'])?$_REQUEST['iCountryId']:'';
$iVehicleTypeId = isset($_REQUEST['iVehicleTypeId'])?$_REQUEST['iVehicleTypeId']:'';

$default_currency = $generalobj->symbol_currency();

$sql="select vName,vSymbol,Ratio from currency where eDefault != 'Yes' order by iDispOrder";
$data_currency = $obj->MySQLSelect($sql);

if($booking_date == "")
{
	$booking_date = date("Y-m-d H:i:s");
}
function clean($str) {
	global $obj;
	$str = trim($str);
		$str = $obj->SqlEscapeString($str);
		$str = htmlspecialchars($str);
		$str = strip_tags($str);
		return($str);
	} 
	
	
	function getVehicleCountryUnit_PricePerKm($vehicleTypeID,$fPricePerKM){
		global $generalobj,$obj,$DEFAULT_DISTANCE_UNIT;
		
		$iCountryId = get_value("vehicle_type", "iCountryId", "iVehicleTypeId", $vehicleTypeID, '', 'true');
		if($iCountryId == "-1"){
			$eUnit = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
		}else{
			$eUnit = get_value("country", "eUnit", "iCountryId", $iCountryId, '', 'true');
		}
		
		if($eUnit == "" || $eUnit == NULL){
			$eUnit = $generalobj->getConfigurations("configurations","DEFAULT_DISTANCE_UNIT");
		}
		$PricePerKM = $fPricePerKM;
		/* if($iCountryId == "-1"){
			if($eUnit != $DEFAULT_DISTANCE_UNIT){
				if($eUnit == "KMs"){
					$PricePerKM = $fPricePerKM * 0.621371;
				}else if($eUnit == "Miles"){
					$PricePerKM = $fPricePerKM / 0.621371 ;
				}
			}
		} */
		
		/* if($eUnit == "Miles"){
			$PricePerKM = $fPricePerKM * 1.60934; 
		}else{
			$PricePerKM = $fPricePerKM;
		} */
		
		$Data['eUnit'] = $eUnit;
		$Data['PricePerKM'] = round($PricePerKM,2);
		return $Data;
	}
	
	function get_value($table, $field_name, $condition_field = '', $condition_value = '', $setParams = '', $directValue = '') {
		global $obj;
		$returnValue = array();
		
		$where = ($condition_field != '') ? ' WHERE ' . clean($condition_field) : '';
		$where .= ($where != '' && $condition_value != '') ? ' = "' . clean($condition_value) . '"' : '';
		
		if ($table != '' && $field_name != '' && $where != '') {
			$sql = "SELECT $field_name FROM  $table $where";
			if ($setParams != '') {
				$sql .= $setParams;
			}
			$returnValue = $obj->MySQLSelect($sql);
			} else if ($table != '' && $field_name != '') {
			$sql = "SELECT $field_name FROM  $table";
			if ($setParams != '') {
				$sql .= $setParams;
			}
			$returnValue = $obj->MySQLSelect($sql);
		}
		if ($directValue == '') {
			return $returnValue;
			} else {
			$temp = $returnValue[0][$field_name];
			return $temp;
		}
	}
	

if($iBaseFare != '' && $booking_date != "")
{
	global $generalobj;
	// $fPickUpPrice = "1";
	// $fNightPrice = "1";
	$SurgePrice = "1";
	$SurgeType = "None";
	
	$Data=$generalobj->GetAllSurgePriceDetails($iVehicleTypeId,$booking_date);
	// echo "<pre>";print_r($Data);exit;
	
	$PickSurgeTiming = "From ".$Data['PickUpDetails']['PickStartTime']." To ".$Data['PickUpDetails']['PickEndTime'];
	$NightSurgeTiming = "From ".$Data['NightDetails']['NightStartTime']." To ".$Data['NightDetails']['NightEndTime'];
	$PickSurgeApplied = "No";
	$NightSurgeApplied = "No";
	$PickFactor = number_format($Data['PickUpDetails']['fPickUpPrice'],2);
	$NightFactor = number_format($Data['NightDetails']['fNightPrice'],2);
	$PickStatus = $Data['PickUpDetails']['ePickStatus'];
	$NightStatus = $Data['NightDetails']['eNightStatus'];
		
	if($Data['Action'] == "0"){
		$SurgeApplied = "Yes";
		$SurgePrice = $Data['SurgePrice'];
		$SurgeType = $Data['SurgeType'];
		
		if($SurgeType == "PickUp"){
			$PickSurgeApplied = "Yes";
		}else{
			$NightSurgeApplied = "Yes";
		}
	}else{
		$SurgeApplied = "No";
	}
	// echo $PickSurgeApplied;
	// echo $NightSurgeApplied;exit;
	$Data_vehicle = getVehicleCountryUnit_PricePerKm($iVehicleTypeId,$fPricePerKM);
	$fPricePerKM = $Data_vehicle['PricePerKM']; 
	$eUnit = $Data_vehicle['eUnit'];
	$tot_distance = ($eUnit == "Miles") ? round($tot_distance / 1.6,2) : $tot_distance;
	$tot_distance =  number_format(round($tot_distance,2),2,".","");
	
	$total_dist_price = number_format($fPricePerKM * $tot_distance,2,".","");
	$total_time_price = number_format($fPricePerMin * $tot_time,2,".","");
	$total_fare = $iBaseFare + $total_dist_price + $total_time_price;
	$total_pick_surge_diff = ($PickFactor > 1 && $PickStatus == "Active") ? number_format(($PickFactor * $total_fare) - $total_fare,2,".","") : '0.00';
	$total_night_surge_diff = ($NightFactor > 1 && $NightStatus == "Active") ? number_format(($NightFactor * $total_fare) - $total_fare,2,".","") : '0.00';
	
	$PickSurgePrice = "0.00";
	$NightSurgePrice = "0.00";
	$GenerateFare = $total_fare;
	if($PickSurgeApplied == "Yes"){
		$GenerateFare = $total_fare + $total_pick_surge_diff;
		$PickSurgePrice = $GenerateFare;
	}else if($NightSurgeApplied == "Yes"){
		$GenerateFare = $total_fare + $total_night_surge_diff;
		$NightSurgePrice = $GenerateFare;
	}
	$GenerateFare_ori = $GenerateFare;
	$MinFareApplied = "No";
	if($GenerateFare < $iMinFare){
		$MinFareApplied = "Yes";
		$GenerateFare = $iMinFare;
	}
	$FinalFare = number_format($GenerateFare,2,".","");
	$total_commision = number_format($FinalFare * $fCommision /100,2,".","");
	
	$con = "";
	$con.= '
	<!-- design new HS-->
			<div class="map-page-box1">
                    <h3>Estimation :</h3>
                    <ul>
                    <li><em>Distance</em>'.$tot_distance.' '.$eUnit.'</li>
                    <li><em>Time</em>'.$tot_time.' Minutes</li>
                    <li><em>Date</em>'.date('M d \a\t h:i  a',strtotime($booking_date)).'</li>
                    </ul>
                    </div>
					
					<div class="map-page-box2"> 
						<h3>Calculation</h3><BR>
						<ul>
							<li>Base Fare <em><b>'.$default_currency.' '.number_format($iBaseFare,2,".","").'</b></em></li>
						
						
						<li> Price Per '.$eUnit.' (<B>'.$default_currency.' '.number_format($fPricePerKM,2,".","").'</B>) X  Estimated Distance (<B>'.$tot_distance.' '.$eUnit.'</B>)
							<em><B>'.$default_currency.' '.$total_dist_price.'</B></em></li>
						
							<li>Price Per Minute (<b>'.$default_currency.' '.number_format($fPricePerMin,2,".","").'</b>) X Estimated Time (<b>'.number_format($tot_time,2,".","").' Minutes</b>)
							<em><B>'.$default_currency.' '.$total_time_price.'</B></em></li>
						
						
							<li class="add-border">Total Fare <em><B>'.$default_currency.' '.number_format($total_fare,2,".","").'</B></em></li>
						';
							
			if($PickStatus == "Active"){
				$con.='<li><b class="small-heading">Pick Up Surcharge Calculation:</b>
						<div class="row">
							<div class="col-sm-8">
								<span><div class="col-sm-5 pull-left">Surcharge Timing</div><b class="col-sm-6 pull-right">'.$PickSurgeTiming.'</b></span><br/>
								<span><div class="col-sm-5 pull-left">Surcharge Factor</div><b class="col-sm-6 pull-right">'.$PickFactor.' X</b></span><br/>
								<span><div class="col-sm-5 pull-left">Surcharge Applied</div><b class="col-sm-6 pull-right">'.$PickSurgeApplied.'</b></span><br/>
							</div>';
							
						if($PickSurgeApplied == "Yes"){
							$con.='<span class="col-sm-12 add-border"><div class="col-sm-8 pull-left">Total Fare <b>('.$default_currency.' '.number_format($total_fare,2,".","").')</b> * Surcharge Factor <b>('.$PickFactor.' X)</b></div><b class="col-sm-4 pull-right">'.$default_currency.' '.$GenerateFare_ori.'</em></b></span>';
						}
						$con.='</div></li>';
			}else{
				$con.='<li><b class="small-heading">Pick Up Surcharge Calculation:</b><br>
							<span>PickUp Surcharge Not Enabled</span>
					</li>';
			}
			
			if($NightStatus == "Active"){
					$con.='<li><b class="small-heading">Night Surcharge Calculation:</b>
							<div class="row">
							<div class="col-sm-8">
								<span><div class="col-sm-5 pull-left">Surcharge Timing</div><b class="col-sm-6 pull-right">'.$NightSurgeTiming.'</b></span><br/>
								<span><div class="col-sm-5 pull-left">Surcharge Factor</div><b class="col-sm-6 pull-right">'.$NightFactor.' X</b></span><br/>
								<span><div class="col-sm-5 pull-left">Surcharge Applied</div><b class="col-sm-6 pull-right">'.$NightSurgeApplied.'</b></span><br/>
								</div>';
								
							if($PickSurgeApplied == "Yes"){
								$con.='<span class="col-sm-8 pull-left">*Night Surcharge will not apply as pick up surcharge applied.</span>';
							}else if($NightSurgeApplied == "Yes"){
								$con.='<span class="col-sm-12 add-border"><div class="col-sm-8 pull-left">Total Fare <b>('.$default_currency.' '.number_format($total_fare,2,".","").')</b> * Surcharge Factor <b>('.$NightFactor.' X)</b></div><b class="col-sm-4 pull-right">'.$default_currency.' '.$GenerateFare_ori.'</b></span>
								';
							}
							$con.='</div></li><br/>';
				}else{
					$con.='<li><b class="small-heading">Night Surcharge Calculation:</b><br>
								<span >Night Surcharge Not Enabled</span>
								</li>';
				}
				$con.='<li><b class="small-heading">Minimum Fare Calculation:</b>
						<div class="row">
						<div class="col-sm-8">
							<span><div class="col-sm-5 pull-left">Minimum Fare</div><b class="col-sm-6 pull-right">'.$default_currency.' '.number_format($iMinFare,2,".","").'</b></span>
							<span><div class="col-sm-5 pull-left">Total Fare</div><b class="col-sm-6 pull-right">'.$default_currency.' '.number_format($GenerateFare_ori,2,".","").'</b></span>
							<span><div class="col-sm-5 pull-left">Minimum Fare Applied</div><b class="col-sm-6 pull-right">'.$MinFareApplied.'</b></span>
						</div>
						<span><div class="col-sm-12 pull-left">*Minimum fare apply when total fare is less then minimum fare.</div></span>
                    </div></li>
					<hr/>
					<li class="add-border">Total Fare <em><B>'.$default_currency.' '.number_format($FinalFare,2,".","").'</B></em></li><BR><BR><BR>
					<li><b class="small-heading">Commision (Included in total fare):</b>
						<div class="row">
							<span class="col-sm-8 pull-left">Site Commision <b>('.number_format($fCommision,2,".","").' %)</b> of Total Fare <b>('.$default_currency.' '.number_format($FinalFare,2,".","").')</b></span><b class="col-sm-4 pull-right">'.$default_currency.' '.$total_commision.'</b></div></li>
					<BR><BR><BR>			
					<li><div class="row show-total"><div class="col-sm-10 pull-left">Total Estimated Fare is sum of Base Fare,Total Distance Price,Total Time Price and Surcharge Amounts.</div><b class="col-sm-2 pull-right">'.$default_currency.' '.number_format($FinalFare,2,".","").'</b></div></li>
					</ul>
				</div>';	
				
		// if(!empty($data_currency)){
			
		// }
						
	
		echo $con;exit;					
	
	}
?>