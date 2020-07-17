<?php 
	Class General_dashboard
	{
		public function __construct(){}
		
		public function getCompanycount()
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tRegistrationDate > '".WEEK_DATE."'";
			}
			global $obj;
			$sql = "SELECT count(iCompanyId) tot_company FROM company WHERE eStatus != 'Deleted' $cmp_ssql ";
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
				$sql = "SELECT count(iUserId) as tot_rider FROM register_user WHERE eStatus != 'Deleted'".$cmp_ssql;
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
			else if($status != "" && $status == "Deleted") {
				$ssl = " AND rd.eStatus = '".$status."'";
			}
			$sql = "SELECT count(rd.iDriverId) as tot_driver FROM register_driver rd  WHERE  1 ".$ssl.$cmp_ssql;
			$data = $obj->MySQLSelect($sql);

			return $data;
		}
		
		public function getTotalEarns()
		{
			$cmp_ssql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tEndDate > '".WEEK_DATE."'";
			}
			global $obj;
			$sql = "SELECT SUM(`fCommision`) AS total FROM trips WHERE iActive = 'Finished' AND eCancelled = 'No' ".$cmp_ssql;
			$data = $obj->MySQLSelect($sql);
			$result = $data[0]['total'];
			return $result;
		}
		
		public function getTripStatescount($tripStatus=NULL,$startDate="",$endDate="")
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
				
				$sql = "SELECT count(iTripId) tot_trip FROM trips WHERE 1".$cmp_ssql.$ssl.$dsql;
				$data = $obj->MySQLSelect($sql);
			}
			return $data;
		}
		
		public function getTripDateStates($time)
		{
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
			
			$sql = "SELECT count(iTripId) as total FROM trips WHERE 1 ".$ssl.$cmp_ssql;
			$data = $obj->MySQLSelect($sql);
			return $data[0]['total'];
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
		
		
		public function getTripAmount($startDate,$endDate,$payment_method="")
		{
			$cmp_ssql = "";
			$psql = "";
			if(SITE_TYPE =='Demo'){
				$cmp_ssql = " And tTripRequestDate > '".WEEK_DATE."'";
			}
			global $obj;
			$data = array();
			
			if($startDate!= "" && $endDate != "")
			{
				$dsql = " AND tTripRequestDate BETWEEN '".$startDate."' AND '".$endDate."' ";
			}
			
			if($payment_method != "")
			{
				$psql = " AND vTripPaymentMode = '".$payment_method."' ";
			}
			
			$ssl = " AND iActive = 'Finished' AND eCancelled='No' ";
				
			$sql = "SELECT iTripId, fTripGenerateFare, fDiscount,fWalletDebit, iFare, vTripPaymentMode FROM trips WHERE 1 ".$cmp_ssql.$ssl.$dsql . $psql ;
			$data = $obj->MySQLSelect($sql);

			return $data;	
		}
		
		public function getDefaultCurency()
		{
			global $obj;
			$sql = "SELECT vName FROM  `currency` WHERE  `eDefault` =  'Yes' AND  `eStatus` =  'Active' LIMIT 0 , 1";
			$data = $obj->MySQLSelect($sql);
			return $data;	
		}

	}
?>