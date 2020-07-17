<?php 

	include_once("common.php");

	$meta_arr = $generalobj->getsettingSeo(2);

	$sql = "SELECT * from language_master where eStatus = 'Active'" ;

	$db_lang = $obj->MySQLSelect($sql);

	$sql = "SELECT * from country where eStatus = 'Active'" ;

	$db_code = $obj->MySQLSelect($sql);

	//echo "<pre>";print_r($db_lang);

	$script="Contact Us";

	$add = "";

	$vName = base64_decode($_REQUEST['1']);

	$vName = explode(" ",$vName);

	$vName0 = $vName['0'];

	if($vName[1] == "")

	$_vLastName = "";	

	else

	$_vLastName = $vName[1];



	$vEmail = base64_decode($_REQUEST['2']);

	$vPhone = base64_decode($_REQUEST['3']);



	if(isset($_POST['action']) && $_POST['action'] == 'send_mail')

	{

		unset($_POST['action']);

		$maildata = array();

		$maildata['EMAIL'] = $_POST['vEmail'];

		$maildata['NAME'] = $_POST['vName']." ".$_POST['vLastName'];

		$maildata['PASSWORD'] = '123456';

		//$generalobj->send_email_user("DRIVER_REGISTRATION_ADMIN",$maildata);

		$generalobj->send_email_user("DRIVER_REGISTRATION_USER",$maildata);

	}

	if(isset($_POST['action']) && $_POST['action'] == 'add_dummy')

	{

		unset($_POST['action']);

		$email = $_POST['vEmail'];

		$msg= $generalobj->checkDuplicateFront('vEmail', 'register_driver' , Array('vEmail'),$tconfig["tsite_url"]."dummy_data_insert.php?error=1&var_msg=Email already Exists", "Email already Exists","" ,"");

		#echo "<pre>";print_r($_POST); die;

		//Insert Driver

		$eReftype1 = "Driver";

		$Data1['vRefCode'] = $generalobj->ganaraterefercode($eReftype1);

		$Data1['iRefUserId'] = '';

		$Data1['eRefType'] = '';

		$Data1['vName'] = $_POST['vName'];

		$Data1['vLastName'] = (isset($_POST['vLastName']) && $_POST['vLastName'] != '')?$_POST['vLastName']:'';

		$Data1['vLang'] = 'EN';

		$Data1['vPassword'] = $generalobj->encrypt_bycrypt('123456');

		$Data1['vEmail'] = $_POST['vEmail'];

		$Data1['dBirthDate'] = '1992-02-02';

		$Data1['vPhone'] = (isset($_POST['vPhone']) && $_POST['vPhone'] != '')?$_POST['vPhone']:'9876543210';

		$Data1['vCaddress'] = "test address";

		$Data1['vCadress2'] = "test address";

		$Data1['vCity'] = "test city";

		$Data1['vZip'] = "121212";

		$Data1['vCountry'] = "US";

		$Data1['vCode'] = "1";

		$Data1['vFathersName'] = 'test';

		$Data1['vCompany'] = 'test';

		$Data1['tRegistrationDate']=Date('Y-m-d H:i:s');

		$Data1['eStatus'] = 'Active';

		$Data1['vCurrencyDriver'] = 'USD';

		$Data1['iCompanyId'] = 1;

		$Data1['eEmailVerified'] = 'Yes';

		$Data1['ePhoneVerified'] = 'Yes';

		//echo "<pre>";print_r($Data1); echo "</pre>";

		$id = $obj->MySQLQueryPerform('register_driver',$Data1,'insert');

		//Add Driver Vehicle

		if($id != "") {

			if($APP_TYPE == 'UberX' || $APP_TYPE == 'Ride-Delivery-UberX'){
      
       $Drive_vehicle['iDriverId'] = $id;
			 $Drive_vehicle['iCompanyId'] = "1";
			 $Drive_vehicle['iMakeId'] = "3";
			 $Drive_vehicle['iModelId'] = "1";
			 $Drive_vehicle['iYear'] = Date('Y');
			 $Drive_vehicle['eStatus'] = "Active";
			 $Drive_vehicle['eCarX'] = "Yes";
			 $Drive_vehicle['eCarGo'] = "Yes";
       $Drive_vehicle['vLicencePlate'] = "My Services";
       $Drive_vehicle['eType'] = "UberX";
       
       $query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type` WHERE eType = 'UberX'";
			 $result = $obj->MySQLSelect($query);
       $Drive_vehicle['vCarType'] = $result[0]['countId'];
       $iDriver_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle,'insert');
       
       if($APP_TYPE == 'UberX'){ 
    			$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$id."'";
    			$obj->sql_query($sql);
       }
       
       $days =  array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
			 foreach ($days as $value) {
					$data_avilability['iDriverId'] = $id;
					$data_avilability['vDay'] = $value;
					$data_avilability['vAvailableTimes'] = '08-09,09-10,10-11,11-12,12-13,13-14,14-15,15-16,16-17,17-18,18-19,19-20,20-21,21-22';
					$data_avilability['dAddedDate'] = @date('Y-m-d H:i:s');
					$data_avilability['eStatus'] = 'Active';
					$data_avilability_add = $obj->MySQLQueryPerform('driver_manage_timing',$data_avilability,'insert');
			 }  
       
       if($APP_TYPE == 'Ride-Delivery-UberX'){
              $query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type` WHERE eType = 'Ride'";
    					$result_ride = $obj->MySQLSelect($query);
              $Drive_vehicle_ride['iDriverId'] = $id;
    					$Drive_vehicle_ride['iCompanyId'] = "1";
    					$Drive_vehicle_ride['iYear'] = "2014";
    					$Drive_vehicle_ride['vLicencePlate'] = "CK201";
    					$Drive_vehicle_ride['eStatus'] = "Active";
    					$Drive_vehicle_ride['eCarX'] = "Yes";
    					$Drive_vehicle_ride['eCarGo'] = "Yes";	
              $Drive_vehicle_ride['eType'] = "Ride";
              $Drive_vehicle_delivery = $Drive_vehicle_ride;
              $Drive_vehicle_ride['iMakeId'] = "3";
    					$Drive_vehicle_ride['iModelId'] = "1"; 	
    					$Drive_vehicle_ride['vCarType'] = $result_ride[0]['countId'];
    					$iDriver_Ride_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle_ride,'insert');
              
              $sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_Ride_VehicleId."' WHERE iDriverId='".$id."'";
    					$obj->sql_query($sql);
              
              $query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type` WHERE eType = 'Deliver'";
    					$result_delivery = $obj->MySQLSelect($query);
              $Drive_vehicle_delivery['iMakeId'] = "5";
    					$Drive_vehicle_delivery['iModelId'] = "18";
              $Drive_vehicle_delivery['eType'] = "Delivery";
              $Drive_vehicle_delivery['vCarType'] = $result_delivery[0]['countId'];
              $iDriver_Delivery_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle_delivery,'insert');
        }
      
              /*
				$query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type`";

				$result = $obj->MySQLSelect($query);

				

				$Drive_vehicle['iDriverId'] = $id;

				$Drive_vehicle['iCompanyId'] = "1";

				$Drive_vehicle['iMakeId'] = "3";

				$Drive_vehicle['iModelId'] = "1";

				$Drive_vehicle['iYear'] = Date('Y');

				$Drive_vehicle['vLicencePlate'] = "My Services";

				$Drive_vehicle['eStatus'] = "Active";

				$Drive_vehicle['eCarX'] = "Yes";

				$Drive_vehicle['eCarGo'] = "Yes";		

				$Drive_vehicle['vCarType'] = $result[0]['countId'];

				$iDriver_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle,'insert');

				$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$id."'";

				$obj->sql_query($sql);

				

				if($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes"){

					$sql="select iVehicleTypeId,iVehicleCategoryId,eFareType,fFixedFare,fPricePerHour from vehicle_type where 1=1";

					$data_vehicles = $obj->MySQLSelect($sql);

					//echo "<pre>";print_r($data_vehicles);exit;

					

					if($data_vehicles[$i]['eFareType'] != "Regular")

					{

						for($i=0 ; $i < count($data_vehicles); $i++){

							$Data_service['iVehicleTypeId'] = $data_vehicles[$i]['iVehicleTypeId'];

							$Data_service['iDriverVehicleId'] = $iDriver_VehicleId;

							

							if($data_vehicles[$i]['eFareType'] == "Fixed"){

								$Data_service['fAmount'] = $data_vehicles[$i]['fFixedFare'];

							}

							else if($data_vehicles[$i]['eFareType'] == "Hourly"){

								$Data_service['fAmount'] = $data_vehicles[$i]['fPricePerHour'];

							}

							$data_service_amount = $obj->MySQLQueryPerform('service_pro_amount',$Data_service,'insert');

						}

					}

				}
				$days =  array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
				foreach ($days as $value) {
					$data_avilability['iDriverId'] = $id;
					$data_avilability['vDay'] = $value;
					$data_avilability['vAvailableTimes'] = '08-09,09-10,10-11,11-12,12-13,13-14,14-15,15-16,16-17,17-18,18-19,19-20,20-21,21-22';
					$data_avilability['dAddedDate'] = Date('Y-m-d H:i:s');
					$data_avilability['eStatus'] = 'Active';
					$data_avilability_add = $obj->MySQLQueryPerform('driver_manage_timing',$data_avilability,'insert');
				}
       */
			} else {

			$query ="SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type`";

			$result = $obj->MySQLSelect($query);

			$Drive_vehicle['iDriverId'] = $id;

			$Drive_vehicle['iCompanyId'] = "1";

			$Drive_vehicle['iMakeId'] = "5";

			$Drive_vehicle['iModelId'] = "18";

			$Drive_vehicle['iYear'] = "2014";

			$Drive_vehicle['vLicencePlate'] = "CK201";

			$Drive_vehicle['eStatus'] = "Active";

			$Drive_vehicle['eCarX'] = "Yes";

			$Drive_vehicle['eCarGo'] = "Yes";		

			$Drive_vehicle['vCarType'] = $result[0]['countId'];

			$iDriver_VehicleId=$obj->MySQLQueryPerform('driver_vehicle',$Drive_vehicle,'insert');

			$sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$id."'";

			$obj->sql_query($sql);

			}

		}	



		



		//Insert Company



		$Data2['vName'] = $_POST['vName'];



		$Data2['vLastName'] = $_POST['vLastName'];



		$Data2['vLang'] = 'EN';



		$Data2['vPassword'] = $generalobj->encrypt_bycrypt('123456');



		$Data2['vEmail'] = "company-".$_POST['vEmail'];



		$Data2['dBirthDate'] = '1992-02-02';



		$Data2['vPhone'] = (isset($_POST['vPhone']) && $_POST['vPhone'] != '')?$_POST['vPhone']:'9876543210';



		$Data2['vCaddress'] = "test address";



		$Data2['vCadress2'] = "test address";



		$Data2['vCity'] = "test city";



		$Data2['vZip'] = "121212";



		$Data2['vCountry'] = "US";



		$Data2['vCompany'] = $_POST['vName']." ".$_POST['vLastName'];



		$Data2['vCode'] = "1";



		$Data2['vFathersName'] = 'test';



		$Data2['tRegistrationDate']=Date('Y-m-d H:i:s');



		$Data2['eStatus'] = 'Active';



		//echo "<pre>";print_r($Data2); echo "</pre>";



		//$id = $obj->MySQLQueryPerform('company',$Data2,'insert');



		



		//Insert rider



		$eReftype = "Rider";



		$Data['vRefCode'] = $generalobj->ganaraterefercode($eReftype);



		$Data['iRefUserId'] = '';



		$Data['eRefType'] = '';



		$Data['vName'] = $_POST['vName'];



		$Data['vLang'] = 'EN';



		$Data['vLastName'] = $_POST['vLastName'];



		//$Data['vLoginId'] = "";



		$Data['vPassword'] = $generalobj->encrypt_bycrypt('123456');



		$Data['vEmail'] = "user-".$_POST['vEmail'];



		$Data['vPhone'] = (isset($_POST['vPhone']) && $_POST['vPhone'] != '')?$_POST['vPhone']:'9876543210';



		$Data['vCountry']= "US";



		$Data['vPhoneCode'] = "1";



		//$Data['vExpMonth'] = $_POST['vExpMonth'];



		//$Data['vExpYear'] = $_POST['vExpYear'];



		$Data['vZip'] = '121212';



		//$Data['iDriverVehicleId	'] = "";



		$Data['vInviteCode'] = "";



		$Data['vCreditCard'] = "";



		$Data['vCvv'] = "";



		$Data['vCurrencyPassenger'] = "USD";



		$Data['dRefDate'] =  Date('Y-m-d H:i:s');



		$Data['eStatus'] = 'Active'; 

		$Data['eEmailVerified'] = 'Yes';

		$Data['ePhoneVerified'] = 'Yes';

		



		$id = $obj->MySQLQueryPerform("register_user",$Data,'insert');



		$add = "Yes";



	}



?>



<!DOCTYPE html>



<html lang="en">



	<head>



		<meta charset="UTF-8">



		<meta name="viewport" content="width=device-width,initial-scale=1">



		<!--<title><?=$COMPANY_NAME?> | Contact Us</title>-->



		<title>Dummy</title>



		<!-- Default Top Script and css -->



		<?php  include_once("top/top_script.php");?>



		<?php  include_once("top/validation.php");?>



		<!-- End: Default Top Script and css-->



	</head>



	<body>



		<!-- home page -->



		<div id="main-uber-page">



			<!-- Top Menu -->



			<!-- End: Top Menu-->



			<!-- contact page-->



			



			<div class="page-contant">



				<div class="page-contant-inner">



					<div class="footer-text-center">			



						<?php  if($add == "Yes"){?>



							<!-- <h3 style="padding-top:15px;"> Company Details </h3>



								<h5>



								<p>Name: <?php  echo $_POST['vName']." ".$_POST['vLastName']; ?></p>



								<p>Email: company_<?php  echo $_POST['vEmail']; ?></p>



								<p>Password: 123456 </p>



							</h5> -->



							<h3 style="padding-top:15px;"> Driver Details </h3>



							<h5>



								<p>Name: <?php  echo $_POST['vName']." ".$_POST['vLastName']; ?></p>



								<p>Email: <?php  echo $_POST['vEmail']; ?></p>



								<p>Password: 123456 </p>



							</h5>



							<h3 style="padding-top:15px;"> Rider Details </h3>



							<h5>



								<p>Name: <?php  echo $_POST['vName']." ".$_POST['vLastName']; ?></p>



								<p>Email: user-<?php  echo $_POST['vEmail']; ?></p>



								<p>Password: 123456 </p>



							</h5>



							



							<form method="post" action="">



								<input type="hidden" name="vName" id="vName" value="<?=$_POST['vName'];?>">



								<input type="hidden" name="vLastName" id="vLastName" value="<?=$_POST['vLastName'];?>">



								<input type="hidden" name="vEmail" id="vEmail" value="<?=$_POST['vEmail'];?>">



								<input type="hidden" name="vPhone" id="vPhone" value="<?=$_POST['vPhone'];?>">



								<input type="hidden" name="action" id="action" value="send_mail">



								<div class="contact-form">



									<b>



										<input type="submit" class="submit-but" value="Send Email to Driver" name="send_email" />



									</b>



								</div>



							</form>



						<?php  } ?>



					</div>



					



					<h2 class="header-page">Add Dummy Data



						<p>It will automatically create dummy record for company , driver, driver vehicle , rider .</p>



					</h2>



					<!-- contact page -->



					<div style="clear:both;"></div>



					<?php 



						if ($_REQUEST['error']) {



						?>



						<div class="row" id="showError">



							<div class="col-sm-12 alert alert-danger">



								<button aria-hidden="true" data-dismiss="alert" class="close" type="button" onclick="hideError();" >×</button>



								<?=$_REQUEST['var_msg']; ?>



							</div>



						</div>



						<?php  



						}



					?>



                    <div style="clear:both;"></div>



					<form name="frmsignup" id="frmsignup" method="post" action="">



						<input type="hidden" name="action" value="add_dummy" >



						<div class="contact-form">



							<b>



								<strong>



									<em>First Name:</em><br/>



									<input type="text" name="vName" placeholder="<?=$langage_lbl['LBL_CONTECT_US_FIRST_NAME_HEADER_TXT']; ?>" class="contact-input required" value="<?=$vName0?>" />



								</strong>



								<strong>



									<em>Last Name:</em><br/>



									<input type="text" name="vLastName" placeholder="<?=$langage_lbl['LBL_CONTECT_US_LAST_NAME_HEADER_TXT']; ?>" class="contact-input" value="<?=$_vLastName?>" />



								</strong>



								<strong>



									<em>Email address:</em><br/>



									<input type="text" placeholder="<?=$langage_lbl['LBL_CONTECT_US_EMAIL_LBL_TXT']; ?>" name="vEmail" value="<?=$vEmail?>" autocomplete="off" class="contact-input required"/>



								</strong>



								<strong>



									<em>Phone Number:</em><br/>



									<input type="text" placeholder="777-777-7777" value="<?=$vPhone?>" name="vPhone" class="contact-input" />



								</strong>



							</b>



							<b>



								<input type="submit" onClick="return submit_form();"  class="submit-but floatLeft" value="ADD" name="SUBMIT" />



							</b> 



						</div>

					</form>

					<div style="clear:both;"></div>

				</div>

			</div>

			<script>

				function submit_form()

				{

					if( validatrix() ){

						//alert("Submit Form");

						document.frmsignup.submit();

						}else{

						console.log("Some fields are required");

						return false;

					}

					return false; //Prevent form submition

				}

			</script>

			<script type="text/javascript">

				function hideError() {

					$('#showError').fadeOut();

				}

			</script>

		<!-- End: Footer Script -->

		</body>

	</html>



