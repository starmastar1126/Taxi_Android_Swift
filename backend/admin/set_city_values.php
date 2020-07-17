<?php 
	$address = isset($_REQUEST['address']) ? $_REQUEST['address'] : '';
	$con = "";
	$add_para = explode(',',$address);
	foreach($add_para as $adp) {
		$con .= "<p><input type='radio' name='setArea' value='".trim($adp)."'>&nbsp;&nbsp;&nbsp;".trim($adp)."</p>";
	}
	echo $con; exit;
?>