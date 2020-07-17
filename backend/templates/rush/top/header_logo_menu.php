<?php 
$sql="select vLabel,vValue from language_label where vCode='".$_SESSION['sess_lang']."'";
$db_lbl=$obj->MySQLSelect($sql);
//echo '<pre>';print_r($db_lbl);exit;
foreach ($db_lbl as $key => $value) {
		$vLabel=$value['vLabel'];
		$$vLabel=$value['vValue'];
}

?>
<section class="header">
	<!--<div class="icon-menu hidden-sm hidden-xs hidden-sm">--><!-- <i class="glyphicon glyphicon-menu-hamburger">&nbsp;<b>Menu</b></i>-->
	<!--<div class="header-dropdown-1">
  		<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
   		 Using Company name
    	<span class="caret"></span>
  		</button>
  		<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
   		 <li><a href="#">aaaaaa</a></li>
   		 <li><a href="#">aaaaaa</a></li>
   		 <li><a href="#">aaaaaa</a></li>
  		 <li><a href="#">aaaaaa</a></li>
 		</ul>
	</div>
	<div class="header-dropdown-2">
  		<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
		Our Company
    	<span class="caret"></span>
  		</button>
  	<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
    <li><a href="#">Action</a></li>
    <li><a href="#">Another action</a></li>
    <li><a href="#">Something else here</a></li>
    <li><a href="#">Separated link</a></li>
  	</ul>
  	</div>
	</div>-->
	<div class="container">
	<a href="index.php"><img class="logo" src="<?=$tconfig["tsite_home_images"]?>logo.png" alt=""></a>
    <!--<div class="login-center">
		<span>
			<a href="login_new.php?action=rider"><?php echo $LBL_LOGIN_RIDER;?></a> | <a href="login_new.php?action=driver"><?php echo $LBL_LOGIN_DRIVER;?></a>
		</span>
	</div>-->
    <div class="top-right-part">
    <div class="login">
		<span>
			<a href="login-signup.php" class="login-signup"><?php echo $LBL_SIGN_UP;?></a>
		</span>
		<span>
			<a href="sign-in.php" class="login-signup"><?php echo $LBL_SIGN_IN_TXT;?></a>
		</span>
		</div>
	<div class="lang">
		<span>
			<select name="sess_language" id="sess_language" onchange="change_lang(this.value);">
					<?php 
					$sql="select vTitle, vCode, vCurrencyCode, eDefault from language_master where eStatus='Active'";
					$db_lng_mst=$obj->MySQLSelect($sql);
					foreach ($db_lng_mst as $key => $value) {
						echo '
							<option value="'.$value['vCode'].'"'.($_SESSION['sess_lang']==$value['vCode']?'selected':'').'>'.$value['vTitle'].'</option>
						';
					}
					?>
			</select>
		</span>
	</div>
    </div>
	</div>
</section>
<script>
function change_lang(lang){
	document.location='common.php?lang='+lang;
}
</script>
