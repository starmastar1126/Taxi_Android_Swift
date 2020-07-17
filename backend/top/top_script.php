<?php 
	
	//print_r($_REQUEST);
	
	if(isset($_REQUEST['edit']) && $_REQUEST['edit'] == 'yes')
	{
		$_SESSION['edita'] = 1;	
	}
	
	if(isset($_REQUEST['edit']) && $_REQUEST['edit'] == 'no'){
		//setcookie('edit', $cookie_value, time() - (86400 * 30));
		unset($_SESSION['edit']);
		$_SESSION['edita'] = "";
	}
	
	include_once("include/config.php");
  	include($templatePath."top/top_script.php");
?>
<script>
    var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    document.cookie = "vUserDeviceTimeZone="+timezone;
</script>
<?=$GOOGLE_ANALYTICS;?>
