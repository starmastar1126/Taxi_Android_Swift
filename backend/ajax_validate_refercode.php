<?php 

	
	include_once('common.php');
	
	
	if(isset($_REQUEST['refcode']))
	{

		echo $generalobj->validationrefercode($_REQUEST['refcode']);
		exit;
			
		
	}
?>