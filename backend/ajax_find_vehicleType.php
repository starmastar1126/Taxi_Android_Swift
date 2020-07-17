<?php 
include_once("common.php");
$iDriverId = isset($_REQUEST['iDriverId'])?$_REQUEST['iDriverId']:'';
$vCarType = isset($_REQUEST['selected'])?$_REQUEST['selected']:'';
$vCarTyp = explode(",", $vCarType);
if($iDriverId != '')
{
	$userSQL = "SELECT c.iCountryId from register_driver AS rd LEFT JOIN country AS c ON c.vCountryCode=rd.vCountry where rd.iDriverId='".$iDriverId."'";
	$drivers = $obj->MySQLSelect($userSQL);

	$iCountryId = $drivers[0]['iCountryId'];
	if($iCountryId != ''){
		$Vehicle_type_name = ($APP_TYPE == 'Delivery')? 'Deliver':$APP_TYPE ;	
		if($Vehicle_type_name == "Ride-Delivery"){
			$vehicle_type_sql = "SELECT vt.*,lm.vLocationName from  vehicle_type as vt LEFT JOIN location_master as lm ON lm.iLocationId = vt.iLocationid where(vt.eType ='Ride' or vt.eType ='Deliver') AND lm.iCountryId='".$iCountryId."'";
			$vehicle_type_data = $obj->MySQLSelect($vehicle_type_sql);
		}else{
			if($Vehicle_type_name == 'UberX'){
				$vehicle_type_sql = "SELECT vt.*,vc.iVehicleCategoryId,vc.vCategory_".$default_lang.",lm.vLocationName from  vehicle_type as vt  left join vehicle_category as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId left join location_master as lm ON lm.iLocationId = vt.iLocationid where vt.eType='".$Vehicle_type_name."' AND lm.iCountryId='".$iCountryId."'";
				$vehicle_type_data = $obj->MySQLSelect($vehicle_type_sql);
			}else{
				$vehicle_type_sql = "SELECT vt.*,c.vCountry,ct.vCity,st.vState,lm.vLocationName from  vehicle_type as vt left join country as c ON c.iCountryId = vt.iCountryId left join state as st ON st.iStateId = vt.iStateId left join city as ct ON ct.iCityId = vt.iCityId left join location_master as lm ON lm.iLocationId = vt.iLocationid where vt.eType='".$Vehicle_type_name."' AND lm.iCountryId='".$iCountryId."'";		
				$vehicle_type_data = $obj->MySQLSelect($vehicle_type_sql);
			}
		}
		foreach ($vehicle_type_data as $key => $value) { 
			if($Vehicle_type_name =='UberX'){
				$vname = $value['vCategory_'.$default_lang].'-'.$value['vVehicleType_'.$default_lang];
			}else{
				$vname= $value['vVehicleType_'.$default_lang];
				$vCountry = $value['vCountry'];
				$vCity = $value['vCity'];
				$vState = $value['vState'];
			}
			 ?>
			<div class="row">
				<div class="col-lg-2">										
					<div><?php  echo $vname;?></div>
					<div style="font-size: 12px;">
						<?php 
						$localization = '';
						if(!empty($value['vLocationName'])) {
							$localization.= $value['vLocationName'];
						}
						echo "( Location : ".$localization.")";
						?>
					</div>
				</div>
				<div class="col-lg-2">
					<div class="make-switch make-swith001" data-on="success" data-off="warning">
						<input type="checkbox" class="chk" name="vCarType[]" <?php  if(in_array($value['iVehicleTypeId'],$vCarTyp)){?>checked<?php  } ?> value="<?=$value['iVehicleTypeId'] ?>"/>
					</div>
				</div>
			</div>
<?php  } } } ?>
<script>
	$(".make-swith001").bootstrapSwitch();
</script>