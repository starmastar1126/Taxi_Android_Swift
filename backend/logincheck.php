<?php  
	if($_SESSION['sess_iDriverId'] == "")
	{
		header("Location:".$tconfig["tsite_url"]."Login");
		exit;
	}
?>