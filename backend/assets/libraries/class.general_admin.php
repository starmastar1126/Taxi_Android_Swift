<?php 
	Class General_admin
	{

		public function __construct(){
			$_SESSION['sess_lang'] = "EN";
		}

		

		public function getCompanyDetails()
		{
			global $obj;
			$cmp_ssql = "";
			// if(SITE_TYPE =='Demo'){
				// $cmp_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
			// }
			$sql = "SELECT COUNT(iCompanyId) AS Total FROM company WHERE eStatus != 'Deleted' $cmp_ssql";
			$data = $obj->MySQLSelect($sql);
			return $data[0]['Total'];
		}
		
		public function getCompanycount()
		{
			$cmp_ssql = "";
			// if(SITE_TYPE =='Demo'){
				// $cmp_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
			// }
			global $obj;
			$sql = "SELECT count(iCompanyId) tot_company FROM company WHERE eStatus != 'Deleted' $cmp_ssql order by tRegistrationDate desc";
			$data = $obj->MySQLSelect($sql);
			return $data;
		}

		public function getDriverDetails ($status)
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			$ssl = "";
			if($status != "" && $status == "active") {
				$ssl = " AND rd.eStatus = '".$status."'";
			} else if($status != "" && $status == "inactive") {
				$ssl = " AND rd.eStatus = '".$status."'";
			}
			$sql = "SELECT rd.*, c.vCompany companyFirstName, c.vLastName companyLastName FROM register_driver rd LEFT JOIN company c ON rd.iCompanyId = c.iCompanyId and c.eStatus != 'Deleted' WHERE  rd.eStatus != 'Deleted'".$ssl.$cmp_ssql;
			$data = $obj->MySQLSelect($sql);

			return $data;
		}
		
		public function getDrivercount ($status)
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			$ssl = "";
			if($status != "" && $status == "active") {
				$ssl = " AND rd.eStatus = '".$status."'";
			} else if($status != "" && $status == "inactive") {
				$ssl = " AND rd.eStatus = '".$status."'";
			}
			$sql = "SELECT count(rd.iDriverId) as tot_driver FROM register_driver rd LEFT JOIN company c ON rd.iCompanyId = c.iCompanyId and c.eStatus != 'Deleted' WHERE  rd.eStatus != 'Deleted'".$ssl.$cmp_ssql;
			$data = $obj->MySQLSelect($sql);

			return $data;
		}

		public function getVehicleDetails ()
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			$sql = "SELECT dv.*, m.vMake, md.vTitle,rd.vEmail, rd.vName, rd.vLastName, c.vName as companyFirstName, c.vLastName as companyLastName
				FROM driver_vehicle dv, register_driver rd, make m, model md, company c
				WHERE
				  dv.eStatus != 'Deleted'
				  AND dv.iDriverId = rd.iDriverId
				  AND dv.iCompanyId = c.iCompanyId
				  AND dv.iModelId = md.iModelId
				  AND dv.iMakeId = m.iMakeId".$cmp_ssql;
			$data = $obj->MySQLSelect($sql);

			return $data;
		}

		public function getRiderDetails ($status="")
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			if($status=="all")
				$sql = "SELECT * FROM register_user WHERE 1 = 1 ".$cmp_ssql;
			else
				$sql = "SELECT * FROM register_user WHERE eStatus != 'Deleted'".$cmp_ssql;
			$data = $obj->MySQLSelect($sql);

			return $data;
		}
		
		public function getRiderCount ($status="")
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			if($status=="all")
				$sql = "SELECT count(iUserId) as tot_rider FROM register_user WHERE 1 = 1 ".$cmp_ssql;
			else
				$sql = "SELECT count(iUserId) FROM register_user WHERE eStatus != 'Deleted'".$cmp_ssql;
			$data = $obj->MySQLSelect($sql);

			return $data;
		}

		public function getTripsDetails ()
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tEndDate > '".WEEK_DATE."'";
			}
			global $obj;
			$sql = "SELECT * FROM trips WHERE 1=1".$cmp_ssql;
			$data = $obj->MySQLSelect($sql);

			return $data;
		}

		/* check admin is login or not */
		function check_member_login()
		{
			global $tconfig;
			$previosLink = $_SERVER['REQUEST_URI'];
			if ((strpos($previosLink, 'ajax') === false) && (strpos($previosLink, 'get') === false)) {
				$_SESSION['current_link'] = $previosLink;
			}
			$sess_iAdminUserId = isset($_SESSION['sess_iAdminUserId'])?$_SESSION['sess_iAdminUserId']:'';
			$sess_iGroupId = isset($_SESSION['sess_iGroupId'])?$_SESSION['sess_iGroupId']:'';
			if($sess_iAdminUserId == "" && basename($_SERVER['PHP_SELF']) != "index.php") {
				header("Location:".$tconfig["tsite_admin_url"]."index.php");
        exit;
			}
			//If GroupId == 2
			//echo basename($_SERVER['PHP_SELF']); die;
			if($sess_iGroupId == '2' && basename($_SERVER['PHP_SELF']) == "dashboard.php") {
				header("Location:".$tconfig["tsite_admin_url"]."add_booking.php");
        exit;
			}else if($sess_iGroupId == '2' && basename($_SERVER['PHP_SELF']) != "cab_booking.php" && basename($_SERVER['PHP_SELF']) != "add_booking.php" && basename($_SERVER['PHP_SELF']) != "action_booking.php" && basename($_SERVER['PHP_SELF']) != "get_available_driver_list.php" && basename($_SERVER['PHP_SELF']) != "get_map_drivers_list.php" && basename($_SERVER['PHP_SELF']) != "ajax_find_rider_by_number.php" && basename($_SERVER['PHP_SELF']) != "change_code.php" && basename($_SERVER['PHP_SELF']) != "get_driver_detail_popup.php" && basename($_SERVER['PHP_SELF']) != "ajax_checkBooking_email.php" && basename($_SERVER['PHP_SELF']) != "admin_action.php" && basename($_SERVER['PHP_SELF']) != "map.php" && basename($_SERVER['PHP_SELF']) != "get_available_driver_list_in_godsview.php" && basename($_SERVER['PHP_SELF']) != "invoice.php" && basename($_SERVER['PHP_SELF']) != "ajax_booking_details.php" && basename($_SERVER['PHP_SELF']) != "checkForRestriction.php" && basename($_SERVER['PHP_SELF']) != "ajax_estimate_by_vehicle_type.php" &&  basename($_SERVER['PHP_SELF']) != "ajax_get_user_balance.php") {
				header("Location:".$tconfig["tsite_admin_url"]."add_booking.php" );
        exit;
			}
			//If GroupId == 3
			if($sess_iGroupId == '3' && basename($_SERVER['PHP_SELF']) == "dashboard.php") {
				header("Location:".$tconfig["tsite_admin_url"]."trip.php");
        exit;
			}else if($sess_iGroupId == '3' && basename($_SERVER['PHP_SELF']) != "trip.php" && basename($_SERVER['PHP_SELF']) != "referrer.php" && strpos(basename($_SERVER['PHP_SELF']), 'report') == false && basename($_SERVER['PHP_SELF']) != "admin_action.php" && basename($_SERVER['PHP_SELF']) != "invoice.php" && basename($_SERVER['PHP_SELF']) != "referrer_action.php" && basename($_SERVER['PHP_SELF']) != "export_driver_details.php" && basename($_SERVER['PHP_SELF']) != "report_export.php" && basename($_SERVER['PHP_SELF']) != "export_driver_pay_details.php" && basename($_SERVER['PHP_SELF']) != "export_trip_pay_details.php" && basename($_SERVER['PHP_SELF']) != "payment_report.php" && basename($_SERVER['PHP_SELF']) != "wallet_report.php" && basename($_SERVER['PHP_SELF']) != "driver_pay_report.php" && basename($_SERVER['PHP_SELF']) != "driver_log_report.php" && basename($_SERVER['PHP_SELF']) != "cancelled_trip.php" && basename($_SERVER['PHP_SELF']) != "ride_acceptance_report.php" && basename($_SERVER['PHP_SELF']) != "driver_trip_detail.php" && basename($_SERVER['PHP_SELF']) != "ajax_find_driver_by_company.php") {
				header("Location:".$tconfig["tsite_admin_url"]."trip.php");
        exit;
			}
		}

		

		function getPostForm($POST_Arr, $msg="", $action="") {
			$str = '
			<html>
			<form name="frm1" action="' . $action . '" method=post>';
			foreach ($POST_Arr as $key => $value) {
			  if ($key != "mode") {
				if (is_array($value)) {
					 foreach ($value as $kk => $vv)
						  $str .='<br><input type="Hidden" name="Data[' . $kk . ']" value="' . stripslashes($vv) . '">';
					 $str .='<br><input type="Hidden" name="' . $key . '[]" value="' . stripslashes($value[$i]) . '">';
				} else {
					 $str .='<br><input type="Hidden" name="' . $key . '" value="' . stripslashes($value) . '">';
				}
			  }
			}
			$str .='<input type="Hidden" name=var_msg value="' . $msg . '">
			</form>
			<script>
			document.frm1.submit();
			</script>
			</html>';
			echo $str;
			exit;
		 }
		 function clearEmail($email){
			 if(SITE_TYPE=="Demo"){
				 $mail=explode('.',$email);
				 $output=substr($mail[0],0,2);
				 return $output.'*****.'.$mail[count($mail)-1];
			 }
			 else{
				 return $email;
			 }
		 }
		 function clearPhone($text){
			 if(SITE_TYPE=="Demo"){
				return substr_replace( $text,"*****",0,-2);
			 }
			 else{
				 return $text;
			 }
		 }
		 
		 function clearName($text){
			 if(SITE_TYPE=="Demo"){
				 $mail=explode(' ',$text);
				 $output=substr($mail[1],0,1);
				 return $mail[0].' '.$output.'***';
			 }
			 else{
				 return $text;
			 }
		 }
		 
		 function clearCmpName($text){
			 if(SITE_TYPE=="Demo"){
				 if(strpos(trim($text), ' ') !== false){
					 $mail=explode(' ',$text);
					 $output=substr($mail[1],0,1);
					 return $mail[0].' '.$output.'***';
				 }else {
					 $output=substr($text,0,3);
					 return $output.'***';
				 }
			 }
			 else{
				 return $text;
			 }
		 }
		 
		 function remove_unwanted($day = 7)
		{
		
			
			global $tconfig,$obj;
			$later_date = date('Y-m-d H:i:s', strtotime("-".$day." day", strtotime(date('Y-m-d H:i:s'))));
		
			/***************** Delete Driver ***************************/
		
			$sql = "SELECT *
			FROM register_driver
			WHERE tRegistrationDate < '".$later_date."'";
			$data = $obj->MySQLSelect($sql);
			
			if(count($data)>0)
			{
				$common_member  = "SELECT iDriverId
				FROM register_driver
				WHERE tRegistrationDate < '".$later_date."'";
				
				$sql = "DELETE FROM driver_vehicle WHERE iDriverId IN (".$common_member.")";
				$db_sql=$obj->sql_query($sql);
				
				$sql = "DELETE FROM trips WHERE iDriverId IN (".$common_member.")";
				$db_sql=$obj->sql_query($sql);
				
				$sql = "DELETE FROM log_file WHERE iDriverId IN (".$common_member.")";
				$db_sql=$obj->sql_query($sql);
				
				$sql = "DELETE FROM register_driver WHERE tRegistrationDate < '".$later_date."'";
				$db_sql=$obj->sql_query($sql);

			}
			
			/**********************************************Delete Rider ********************************************/
			
			$sql = "SELECT *
			FROM register_user
			WHERE tRegistrationDate < '".$later_date."'";
			$data_user = $obj->MySQLSelect($sql);
			if(count($data_user)>0)
			{
				$common_member  = "SELECT iUserId
				FROM register_user
				WHERE tRegistrationDate < '".$later_date."'";
				
				$sql = "DELETE FROM trips WHERE iUserId IN (".$common_member.")";
				$db_sql=$obj->sql_query($sql);
				
				$sql = "DELETE FROM register_user WHERE tRegistrationDate < '".$later_date."'";
				$db_sql=$obj->sql_query($sql);

			}
			
			
		}
		
		public function getTripStates($tripStatus=NULL,$startDate="",$endDate="")
		{
			$cmp_ssql = "";
			$dsql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tTripRequestDate > '".WEEK_DATE."'";
			}
			global $obj;
			$data = array();
			
			if($startDate!= "" && $endDate != "")
			{
				$dsql = " AND tTripRequestDate BETWEEN '".$startDate."' AND '".$endDate."'";
				//$dsql = " AND tTripRequestDate >= '".$startDate."' OR tTripRequestDate <= '".$endDate."' ";
			}
			
			if($tripStatus != "") {
				if($tripStatus == "on ride") {
					$ssl = " AND (iActive = 'On Going Trip' OR iActive = 'Active') AND eCancelled='No'";
				}else if($tripStatus == "cancelled") {
					$ssl = " AND (iActive = 'Canceled' OR eCancelled='yes')";
				}else if($tripStatus == "finished") {
					$ssl = " AND iActive = 'Finished' AND eCancelled='No'";
				}else {
					$ssl = "";
				}
				
				$sql = "SELECT COUNT(iTripId) as tot FROM trips WHERE 1".$cmp_ssql.$ssl.$dsql;
				$data = $obj->MySQLSelect($sql);
			}
			return $data[0]['tot'];
		}
		
		public function getTripStatescount($tripStatus=NULL,$startDate="",$endDate="")
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tTripRequestDate > '".WEEK_DATE."'";
			}
			global $obj;
			$data = array();
			
			if($startDate!= "" && $endDate != "")
			{
				$dsql = " AND tTripRequestDate BETWEEN '".$startDate."' AND '".$endDate."'";
			}
			
			if($tripStatus != "") {
				if($tripStatus == "on ride") {
					$ssl = " AND (iActive = 'On Going Trip' OR iActive = 'Active') AND eCancelled='No'";
				}else if($tripStatus == "cancelled") {
					$ssl = " AND (iActive = 'Canceled' OR eCancelled='yes')";
				}else if($tripStatus == "finished") {
					$ssl = " AND iActive = 'Finished' AND eCancelled='No'";
				}else {
					$ssl = "";
				}
				
				$sql = "SELECT iTripId FROM trips WHERE 1".$cmp_ssql.$ssl.$dsql;
				$data = $obj->MySQLSelect($sql);
			}
			return $data;
		}
		
		public function getTotalEarns() {
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tEndDate > '".WEEK_DATE."'";
			}
			global $obj;
			$sql = "SELECT SUM( `fCommision` ) AS total FROM trips WHERE 1".$cmp_ssql;
			$data = $obj->MySQLSelect($sql);
			$result = $data[0]['total'];
			return $result;
		}
		
		public function getTripDateStates($time) {
			global $obj;
			$data = array();
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tEndDate > '".WEEK_DATE."'";
			}
			if($time == "month") {
				$startDate = date('Y-m')."-01 00:00:00";
				$endDate = date('Y-m')."-31 23:59:59";
				$ssl = " AND tTripRequestDate BETWEEN '".$startDate."' AND '".$endDate."'";
			}else if($time == "year") {
				$startDate1 = date('Y')."-00-01 00:00:00";
				$endDate1 = date('Y')."-12-31 23:59:59";
				$ssl = " AND tTripRequestDate BETWEEN '".$startDate1."' AND '".$endDate1."'";
			}else {
				$startDate2 = date('Y-m-d')." 00:00:00";
				$endDate2 = date('Y-m-d')." 23:59:59";
				$ssl = " AND tTripRequestDate BETWEEN '".$startDate2."' AND '".$endDate2."'";
			}
			
			$sql = "SELECT count(iTripId) as total FROM trips WHERE 1 ".$ssl.$cmp_ssql;
			$data = $obj->MySQLSelect($sql);
			return $data[0]['total'];
		}
		
		public function getDriverDateStatus($time) {
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			$data = array();
			if($time == "month") {
				$startDate = date('Y-m')."-00 00:00:00";
				$endDate = date('Y-m')."-31 23:59:59";
				$ssl = " AND rd.tRegistrationDate BETWEEN '".$startDate."' AND '".$endDate."'";
			}else if($time == "year") {
				$startDate1 = date('Y')."-00-00 00:00:00";
				$endDate1 = date('Y')."-12-31 23:59:59";
				$ssl = " AND rd.tRegistrationDate BETWEEN '".$startDate1."' AND '".$endDate1."'";
			}else {
				$startDate2 = date('Y-m-d')." 00:00:00";
				$endDate2 = date('Y-m-d')." 23:59:59";
				$ssl = " AND rd.tRegistrationDate BETWEEN '".$startDate2."' AND '".$endDate2."'";
			}
			$sql = "SELECT rd.*, c.vCompany companyFirstName, c.vLastName companyLastName FROM register_driver rd LEFT JOIN company c ON rd.iCompanyId = c.iCompanyId and c.eStatus != 'Deleted' WHERE  rd.eStatus != 'Deleted'".$ssl.$cmp_ssql;
			$data = $obj->MySQLSelect($sql);
			return $data;
		}
		
		public function getAllCashCountbyDriverId ($id,$ssql)
		{
			$total = '0.00';
			if($id != "") {
				global $obj;
				//$sql = "SELECT SUM(fTripGenerateFare) as totalAmount FROM trips AS tr WHERE vTripPaymentMode='Cash' AND iDriverId = '".$id."'".$ssql;
        $sql = "SELECT SUM(fCommision) as totalAmount FROM trips AS tr WHERE vTripPaymentMode='Cash' AND eDriverPaymentStatus = 'Unsettelled' AND iDriverId = '".$id."'".$ssql;
				$data = $obj->MySQLSelect($sql);
				$total = ($data[0]['totalAmount'] != "")?$data[0]['totalAmount']:'0.00';
			}
			return number_format($total,2);
		}
		
		public function getAllCardCountbyDriverId ($id,$ssql)
		{
			$total = '0.00';
			if($id != "") {
				global $obj;
				//$sql = "SELECT SUM(fTripGenerateFare) as totalAmount FROM trips AS tr WHERE eDriverPaymentStatus = 'Unsettelled' AND vTripPaymentMode='Card' AND iDriverId = '".$id."'".$ssql;
        $sql = "SELECT SUM(fTripGenerateFare) as totalTripAmount,SUM(fCommision) as totalCommissionAmount FROM trips as tr WHERE eDriverPaymentStatus = 'Unsettelled' AND vTripPaymentMode='Card' AND iDriverId = '".$id."'".$ssql;
				$data = $obj->MySQLSelect($sql);
        //$total = ($data[0]['totalAmount'] != "")?$data[0]['totalAmount']:'0.00';
        $totalAmount = $data[0]['totalTripAmount']-$data[0]['totalCommissionAmount'];      
        $total = ($totalAmount != "")?$totalAmount:'0.00';    
				
			}
			return number_format($total,2);
		}
		
			public function getAllWalletCountbyDriverId($id,$ssql)
		{
			$total = '0.00';
			if($id != "") {
				global $obj;
				$sql = "SELECT SUM(fWalletDebit) as totalAmount FROM trips AS tr WHERE vTripPaymentMode='Cash' AND eDriverPaymentStatus = 'Unsettelled' AND iDriverId = '".$id."'".$ssql;
				$data = $obj->MySQLSelect($sql);
				$total = ($data[0]['totalAmount'] != "")?$data[0]['totalAmount']:'0.00';
			}
			return number_format($total,2);
		}
		
			public function getAllPromocodeCountbyDriverId($id,$ssql)
		{
			$total = '0.00';
			if($id != "") {
				global $obj;
				$sql = "SELECT SUM(fDiscount) as totalAmount FROM trips AS tr WHERE vTripPaymentMode='Cash' AND eDriverPaymentStatus = 'Unsettelled' AND iDriverId = '".$id."'".$ssql;
				$data = $obj->MySQLSelect($sql);
				$total = ($data[0]['totalAmount'] != "")?$data[0]['totalAmount']:'0.00';
			}
			return number_format($total,2);
		}
		
		public function getAllTipCountbyDriverId ($id,$ssql)
		{
			$total = '0.00';
			if($id != "") {
				global $obj;
				$sql = "SELECT SUM(fTipPrice) as totalAmount FROM trips AS tr WHERE eDriverPaymentStatus = 'Unsettelled' AND vTripPaymentMode='Card' AND iDriverId = '".$id."'".$ssql;
				$data = $obj->MySQLSelect($sql);
				$total = ($data[0]['totalAmount'] != "")?$data[0]['totalAmount']:'0.00';
			}
			return number_format($total,2);
		}
		
		public function getTransforAmountbyDriverId ($id,$ssql,$tip='')
		{
			$total = '0.00';
			if($id != "") {
				global $obj;
				//get Cash commision
				$sql = "SELECT SUM(fCommision) AS totalAmount FROM trips AS tr WHERE eDriverPaymentStatus = 'Unsettelled' AND vTripPaymentMode='Cash' AND iDriverId = '".$id."'".$ssql;
				$data = $obj->MySQLSelect($sql);
				$cashCommision = ($data[0]['totalAmount'] != "")?$data[0]['totalAmount']:'0.00';

				//get Card total with deduct commision
				$sql = "SELECT IFNULL( SUM( IFNULL( fTripGenerateFare, 0 ) ) + SUM( IFNULL( fTipPrice, 0 ) ) , 0 ) - IFNULL( SUM( IFNULL( fCommision, 0 ) ) , 0 ) AS amounts FROM trips  AS tr WHERE eDriverPaymentStatus = 'Unsettelled' AND vTripPaymentMode='Card' AND iDriverId = '".$id."'".$ssql;
				$data = $obj->MySQLSelect($sql);
				$cardTotal = ($data[0]['amounts'] != "")?$data[0]['amounts']:'0.00';
				// if($tip != ''){
					// $cardTotal = str_replace(',','',$cardTotal)+str_replace(',','',$tip);
				// }
				//get Cash Trips Wallet and Promocode total  
				$sql = "SELECT IFNULL( SUM( IFNULL( fWalletDebit, 0 ) ) + SUM( IFNULL( fDiscount, 0 ) ) , 0 ) AS totalpromowalletamount FROM trips AS tr WHERE eDriverPaymentStatus = 'Unsettelled' AND vTripPaymentMode='Cash' AND iDriverId = '".$id."'".$ssql;
				$data = $obj->MySQLSelect($sql);
				$walletpromocodeTotal = ($data[0]['totalpromowalletamount'] != "")?$data[0]['totalpromowalletamount']:'0.00';
				
				$total = number_format($cardTotal-$cashCommision+$walletpromocodeTotal,2);
			}
			return $total;
		}
		
		public function getCompanyDetailsDashboard()
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			$sql = "SELECT count(iCompanyId) as Total FROM company WHERE eStatus != 'Deleted' $cmp_ssql order by tRegistrationDate desc";
			$data = $obj->MySQLSelect($sql);
			return $data[0]['Total'];
		}

		public function getDriverDetailsDashboard ($status)
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			$ssl = "";
			if(isset($status) && $status != "" && $status == "active") {
				$ssl = " AND rd.eStatus = '".$status."'";
			} else if(isset($status) && $status != "" && $status == "inactive") {
				$ssl = " AND rd.eStatus = '".$status."'";
			}
			$sql = "SELECT count(rd.iDriverId) as Total FROM register_driver rd LEFT JOIN company c ON rd.iCompanyId = c.iCompanyId and c.eStatus != 'Deleted' WHERE  rd.eStatus != 'Deleted'".$ssl.$cmp_ssql;
			$data = $obj->MySQLSelect($sql);

			return $data[0]['Total'];
		}

		public function getVehicleDetailsDashboard ()
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			$sql = "SELECT count(dv.iDriverVehicleId) as Total
				FROM driver_vehicle dv, register_driver rd, make m, model md, company c
				WHERE
				  dv.eStatus != 'Deleted'
				  AND dv.iDriverId = rd.iDriverId
				  AND dv.iCompanyId = c.iCompanyId
				  AND dv.iModelId = md.iModelId
				  AND dv.iMakeId = m.iMakeId".$cmp_ssql;
			$data = $obj->MySQLSelect($sql);

			return $data[0]['Total'];
		}

		public function getRiderDetailsDashboard ()
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			$sql = "SELECT count(iUserId) as Total FROM register_user WHERE eStatus != 'Deleted'".$cmp_ssql;
			$data = $obj->MySQLSelect($sql);

			return $data[0]['Total'];
		}

		public function getTripsDetailsDashboard ()
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tEndDate > '".WEEK_DATE."'";
			}
			global $obj;
			$sql = "SELECT count(iTripId) as Total FROM trips WHERE 1=1".$cmp_ssql;
			$data = $obj->MySQLSelect($sql);

			return $data[0]['Total'];
		}
		
		public function getTripStatesDashboard($tripStatus=NULL)
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tStartDate > '".WEEK_DATE."'";
			}
			global $obj;
			$data = array();
			if($tripStatus != "") {
				if($tripStatus == "on ride") {
					$ssl = " AND (iActive = 'On Going Trip' OR iActive = 'Active') AND eCancelled='No'";
				}else if($tripStatus == "cancelled") {
					$ssl = " AND (iActive = 'Canceled' OR eCancelled='yes')";
				}else if($tripStatus == "finished") {
					$ssl = " AND iActive = 'Finished' AND eCancelled='No'";
				}else {
					$ssl = "";
				}
				
				$sql = "SELECT count(iTripId) as Total FROM trips WHERE 1".$cmp_ssql.$ssl;
				$data = $obj->MySQLSelect($sql);
			}
			return $data[0]['Total'];
		}
		
		public function getTripDateStatesDashboard($time) {
			global $obj;
			$data = array();
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tEndDate > '".WEEK_DATE."'";
			}
			if($time == "month") {
				$startDate = date('Y-m')."-00 00:00:00";
				$endDate = date('Y-m')."-31 23:59:59";
				$ssl = " AND tTripRequestDate BETWEEN '".$startDate."' AND '".$endDate."'";
			}else if($time == "year") {
				$startDate1 = date('Y')."-00-00 00:00:00";
				$endDate1 = date('Y')."-12-31 23:59:59";
				$ssl = " AND tTripRequestDate BETWEEN '".$startDate1."' AND '".$endDate1."'";
			}else {
				$startDate2 = date('Y-m-d')." 00:00:00";
				$endDate2 = date('Y-m-d')." 23:59:59";
				$ssl = " AND tTripRequestDate BETWEEN '".$startDate2."' AND '".$endDate2."'";
			}
			$sql = "SELECT count(iTripId) as Total FROM trips WHERE 1 ".$ssl.$cmp_ssql;
			$data = $obj->MySQLSelect($sql);
			return $data[0]['Total'];
		}
		
		public function getDriverDateStatusDashboard($time) {
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And rd.tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			$data = array();
			if($time == "month") {
				$startDate = date('Y-m')."-00 00:00:00";
				$endDate = date('Y-m')."-31 23:59:59";
				$ssl = " AND rd.tRegistrationDate BETWEEN '".$startDate."' AND '".$endDate."'";
			}else if($time == "year") {
				$startDate1 = date('Y')."-00-00 00:00:00";
				$endDate1 = date('Y')."-12-31 23:59:59";
				$ssl = " AND rd.tRegistrationDate BETWEEN '".$startDate1."' AND '".$endDate1."'";
			}else {
				$startDate2 = date('Y-m-d')." 00:00:00";
				$endDate2 = date('Y-m-d')." 23:59:59";
				$ssl = " AND rd.tRegistrationDate BETWEEN '".$startDate2."' AND '".$endDate2."'";
			}
			$sql = "SELECT count(rd.iDriverId) as Total FROM register_driver rd LEFT JOIN company c ON rd.iCompanyId = c.iCompanyId and c.eStatus != 'Deleted' WHERE  rd.eStatus != 'Deleted'".$ssl.$cmp_ssql;
			$data = $obj->MySQLSelect($sql);
			return $data[0]['Total'];
		}
		
		public function set_hour_min($times)
		{
			$hour=0;
			$second=0;
			$minute=floor($times/60);
			if($times < 60)
			{
				$minute=0;
			}
			if($minute > 60)
			{
				$hour=floor($minute/60);
				$minute=floor($minute%60);
			}
			else
			{
				$second=floor($times%60);
			}
			$ansdata=Array("hour"=>$hour,"minute"=>$minute,"second"=>$second);
			
			return $ansdata;
		}
		
		public function getLocationName($Name,$Id)
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			if($Name == "country"){
				$sql = "SELECT vCountry FROM country WHERE iCountryId=".$Id;
				$data = $obj->MySQLSelect($sql);
				if(count($data)>0){
					return $data[0]['vCountry'];	
				}
				else{
					return "-";	
				}
			}
			elseif($Name == "state"){
				$sql = "SELECT vState FROM state WHERE iStateId=".$Id;
				$data = $obj->MySQLSelect($sql);
				if(count($data)>0){
					return $data[0]['vState'];
				}
				else{
					return "-";	
				}
				
			}
			else{
				$sql = "SELECT vCity FROM city WHERE iCityId=".$Id;
				$data = $obj->MySQLSelect($sql);
				if(count($data)>0){
					return $data[0]['vCity'];
				}
				else{
					return "-";	
				}
				
			}
			
		}
		
		
		public function get_left_days_jobsave($dend,$dstart){
			$dayinpass = $dstart;
			$today = strtotime($dend); 
			$dayinpass= strtotime($dayinpass);
			return round(abs($today-$dayinpass));
			// return round(abs($today-$dayinpass)/60/60);
		}
		
		public function mediaTimeDeFormater($seconds) {
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
				if($mins > 1){
				 $ret = "$mins mins";
				}else{
				  $ret = "$mins min";
				}
			}      
			else{
				$mint="";
				if($mins > 01){
					$mint = "$mins mins";
				}else{
					$mint = "$mins min";
				}
				if($hours > 1){
				  $ret = "$hours hrs $mint";
				}else{
					$ret = "$hours hr $mint";
				}
			  }
			return  $ret;
		}	
                
		public  function clean($str) {
			global $obj;
			$str = trim($str);
			// $str = mysqli_real_escape_string($str);
			$str = $obj->SqlEscapeString($str);
			$str = htmlspecialchars($str);
			$str = strip_tags($str);
			return($str);
		}
        
		public function DateTime($text, $time = 'yes') {
			
			if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
			return "---";
		
			$date= @date('jS F, Y', @strtotime($text));
			if($time == 'yes'){
				$date .= " ".@date('h:i a', @strtotime($text));;
			}
			return $date;
		}	
		/* if user is at login page */
		function go_to_home() {
			global $tconfig;
			
			$sess_iAdminUserId = isset($_SESSION['sess_iAdminUserId'])?$_SESSION['sess_iAdminUserId']:'';
			
			// $sess_user = isset($_SESSION['sess_user'])?$_SESSION['sess_user']:'';
			// $url = "";
			// echo "<pre>";
			// print_r($_SESSION); die;
			if($sess_iAdminUserId != "") {
				$url = "dashboard.php";
			}


			if(isset($url) && $url != '' && basename($_SERVER['PHP_SELF']) != $url) {
				// if user is at same page 
				echo'<script>window.location="'.$url.'";</script>';				
				@header("Location:".$url);
				exit;
			}
		}        
                
	}
?>