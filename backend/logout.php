<?php 
	include_once("common.php");
	
	unset($_SESSION['sess_iUserId']);
	unset($_SESSION["sess_iCompanyId"]);
	unset($_SESSION["sess_vName"]);
	unset($_SESSION["sess_vEmail"]);
	unset($_SESSION["sess_user"]);
	
	if (isset($_SERVER['HTTP_COOKIE'])) {
		$cookies = explode(';', $_SERVER['HTTP_COOKIE']);
		foreach($cookies as $cookie) {
			$parts = explode('=', $cookie);
			$name = trim($parts[0]);
			setcookie($name, '', time()-1000);
			setcookie($name, '', time()-1000, '/');
		}
	}
	session_destroy();
	
	if(isset($_REQUEST['depart']) && $_REQUEST['depart'] == 'mobi') {
		header("Location:mobi");
	}else {
		header("Location:sign-in.php");
	}
	exit;
?>
