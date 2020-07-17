<?php 
	include_once('../common.php');

	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	$tbl_name 	= 'city';
	$script 	= 'Settings';
	
	$country_id = $_POST['country_id'];
	$country_id_not_required = $_POST['country_id_not_required'];
	$country_main_id = $_POST['country_main_id'];
	$state_id = $_POST['state_id'];
	$state_id_not_required = $_POST['state_id_not_required'];
	$vehicletype_id = $_POST['vehicletype_id'];
	
	if($country_main_id != " "){
		$sql_country_main = "SELECT * FROM country where iCountryId=".$country_main_id." AND eStatus='Active' order by iCountryId desc";
		$db_data_country_main = $obj->MySQLSelect($sql_country_main);
		//print_r($db_data_country_main);
		if(empty($db_data_country_main)){
			//echo "<option selected='selected' value=''>"; echo "No records for State."; echo "</option>";
		}
		else{
			for($i=0;$i<count($db_data_country_main);$i++){
				if($country_main_id){
				
					echo "<option value='".$db_data_country_main[$i]['iCountryId']."'>"; echo $db_data_country_main[$i]['vCountry'];echo "</option>";
				}
			}
		}
	}
	
	if($country_id != " "){
		$sql_state = "SELECT * FROM state where iCountryId=".$country_id." AND eStatus='Active' order by iCountryId desc";
		$db_data_state = $obj->MySQLSelect($sql_state);
		if(empty($db_data_state)){
			//echo "<option selected='selected' value=''>"; echo "No records for State."; echo "</option>";
		}
		else{
			for($i=0;$i<count($db_data_state);$i++){
				if($country_id){
				
					echo "<option  value='".$db_data_state[$i]['iStateId']."'>"; echo $db_data_state[$i]['vState'];echo "</option>";
				}
			}
		}
	}
	
	if($state_id != " "){
		$sql_city = "SELECT * FROM city where iStateId=".$state_id." AND eStatus='Active' order by iCityId desc";
		$db_data_city = $obj->MySQLSelect($sql_city);
		
			for($i=0;$i<count($db_data_city);$i++){
				if($state_id){
				
					echo "<option name='vCity' value='".$db_data_city[$i]['iCityId']."'>"; echo $db_data_city[$i]['vCity'];echo "</option>";
				}
			}
		
	}
	
	if($vehicletype_id != " "){
		$sql_vehicle_type = "SELECT * FROM vehicle_type where ivehicleTypeId=".$vehicletype_id;
		$db_data_vehicle_type = $obj->MySQLSelect($sql_vehicle_type);
		$db_data_vehicle_type = $db_data_vehicle_type[0];
		echo json_encode($db_data_vehicle_type);
			/* for($i=0;$i<count($db_data_vehicle_type);$i++){
				if($state_id){
				
					echo "<option name='vCity' value='".$db_data_city[$i]['iCityId']."'>"; echo $db_data_city[$i]['vCity'];echo "</option>";
				}
			}
		 */
	}
	
	if($country_id_not_required != " "  ){
		$sql_state = "SELECT * FROM state where iCountryId=".$country_id_not_required." AND eStatus='Active' order by iCountryId Asc";
		$db_data_state = $obj->MySQLSelect($sql_state);
		if(empty($db_data_state)){
			//echo "<option selected='selected' value=''>"; echo "No records for State."; echo "</option>";
		}
		else{
			echo "<option selected='selected' value=''>"; echo "Select State"; echo "</option>";
			for($i=0;$i<count($db_data_state);$i++){
				if($country_id_not_required){
						
					echo "<option  value='".$db_data_state[$i]['iStateId']."'>"; echo $db_data_state[$i]['vState'];echo "</option>";
				}
			}
		}
	}
	
	
	if($state_id_not_required != " "){
		$sql_city = "SELECT * FROM city where iStateId=".$state_id_not_required." AND eStatus='Active' order by iCityId desc";
		$db_data_city = $obj->MySQLSelect($sql_city);
			echo "<option  value=''>"; echo "Select City"; echo "</option>";
			for($i=0;$i<count($db_data_city);$i++){
				if($state_id_not_required){
				
					echo "<option name='vCity' value='".$db_data_city[$i]['iCityId']."'>"; echo $db_data_city[$i]['vCity'];echo "</option>";
				}
			}
		
	}
	

	/* $sql_country = "SELECT * FROM country";
	$db_data_country = $obj->MySQLSelect($sql_country);

	//echo '<prE>'; print_R($_REQUEST); echo '</pre>';

	// set all variables with either post (when submit) either blank (when insert)
	$vCountry = isset($_POST['vCountry'])?$_POST['vCountry']:'';
	$vCountryCode = isset($_POST['vCountryCode'])?$_POST['vCountryCode']:'';
	$vCountryCodeISO_3 = isset($_POST['vCountryCodeISO_3'])?$_POST['vCountryCodeISO_3']:'';
	$vPhoneCode = isset($_POST['vPhoneCode'])?$_POST['vPhoneCode']:'';
	$eStatus_check = isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	$eStatus = ($eStatus_check == 'on')?'Active':'Inactive';

	if(isset($_POST['submit'])) {


				if(SITE_TYPE=='Demo')
				{
						header("Location:country_action.php?id=".$id.'&success=2');
						exit;
				}

		$q = "INSERT INTO ";
		$where = '';

		if($id != '' ){
			$q = "UPDATE ";
			$where = " WHERE `iCountryId` = '".$id."'";
		}


		$query = $q ." `".$tbl_name."` SET
		`vCountry` = '".$vCountry."',
		`vCountryCode` = '".$vCountryCode."',
		`vCountryCodeISO_3` = '".$vCountryCodeISO_3."',
		`vPhoneCode` = '".$vPhoneCode."',
		`eStatus` = '".$eStatus."'"
		.$where;

		$obj->sql_query($query);
		$id = ($id != '')?$id:$obj->GetInsertId();
		header("Location:country_action.php?id=".$id.'&success=1');

	}

	// for Edit
	if($action == 'Edit') {
		$sql = "SELECT * FROM ".$tbl_name." WHERE iCityId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);

		$vLabel = $id;
		if(count($db_data) > 0) {
			foreach($db_data as $key => $value) {
				$vCountry	 = $value['vCountry'];
				$vCountryCode	 = $value['vCountryCode'];
				$vCountryCodeISO_3	 = $value['vCountryCodeISO_3'];
				$vPhoneCode	 = $value['vPhoneCode'];
				$eStatus = $value['eStatus'];
			}
		}
	}
	
	$sql_country = "SELECT * FROM country";
	$db_data_country = $obj->MySQLSelect($sql_country);
	
	$sql_state = "SELECT * FROM state where iCountryId='".$country_id."'";
	$db_data_state = $obj->MySQLSelect($sql_state); */
	//echo '<pre>'; print_R($db_data_state); echo '</pre>';die;
	
?>
