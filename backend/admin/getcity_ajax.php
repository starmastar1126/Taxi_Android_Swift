
<?php  
include_once("../common.php");
if(isset($_REQUEST['country'])){
	$sql = "SELECT vCity,iCityId FROM city where iCountryId='".$_REQUEST['country']."'";

	$db_data = $obj->MySQLSelect($sql);
	$cont = '';
	$cont .= '<select name="city" required>';
	$cont .= '';

	foreach ($db_data as $value) {

		$cityname = $_REQUEST['city'];
		$selectd = "";
		if($value['iCityId'] == $cityname){
			$selectd = "selected";
		}

		$cont .= '<option value="'.$value['iCityId'].'"'.$selectd.'>'.$value['vCity'].'</option>';
	}

	$cont .= '</select>';
	echo $cont; exit;
}
?>