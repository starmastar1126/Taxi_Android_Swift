<?php 
    include_once('common.php');
	$sess_user 		= isset($_SESSION['sess_user'])?$_SESSION['sess_user']:'';
	$sess_iUserId 	= isset($_SESSION['sess_iUserId'])?$_SESSION['sess_iUserId']:'';
	$db_ride 		= array();
	
	$field 		= ($sess_user == 'driver')?"iDriverId":($sess_user == 'rider')?"iUserId":($sess_user == 'company')?"iCompanyId":'';

	if($field != '') {
		$sql = "SELECT * FROM trips WHERE ".$field." = '".$sess_iUserId."' ORDER BY iTripId DESC LIMIT 5";
		$db_ride  = $obj->MySQLSelect($sql);
	}
	$tbl_name = 'register_driver';
if ($_SESSION['sess_user'] =='driver') {
     $sql = "SELECT vNoc,vLicence,vCerti from register_driver where iDriverId = '" . $_SESSION['sess_iUserId'] . "'";
     $db_doc = $obj->MySQLSelect($sql);    
}
if ($_SESSION['sess_user'] =='company') {
     $sql = "SELECT vNoc,vCerti from company where iCompanyId = '" . $_SESSION['sess_iUserId'] . "'";
     $db_doc = $obj->MySQLSelect($sql);    
}

if (count($db_doc) > 0) {
     $noc = $db_doc[0]['vNoc'];
     $certi = $db_doc[0]['vCerti'];
     if ($_SESSION['sess_user'] == 'driver')
          $licence = $db_doc[0]['vLicence'];
} else {
     $noc = '';
     $certi = '';
     $licence = '';
}

if($host_system == 'cubetaxiplus') {
  $logo = "logo.png";
} else if($host_system == 'ufxforall') {
  $logo = "ufxforall-logo.png";
}  else if($host_system == 'uberridedelivery4') {
  $logo = "ride-delivery-logo.png";
} else if($host_system == 'uberdelivery4') {
  $logo = "delivery-logo-only.png";
} else {
  $logo = "logo.png";
}
?>
<!-- HEADER SECTION -->
<div id="top">
	<nav class="navbar navbar-inverse navbar-fixed-top " style="padding-top: 10px;">
		<a data-original-title="Show/Hide Menu" data-placement="bottom" data-tooltip="tooltip" class="accordion-toggle btn btn-primary btn-sm visible-xs" data-toggle="collapse" href="#menu" id="menu-toggle">
			<i class="icon-align-justify"></i>
		</a>
		<!-- LOGO SECTION -->
		<header class="navbar-header">
			<a href="profile.php" class="navbar-brand">
				<img src="<?=$tconfig['tsite_img']."/".$logo;?>" alt="" />
			</a>
		</header>
		<!-- END LOGO SECTION -->
		<ul class="nav navbar-top-links navbar-right">
			<!--ALERTS SECTION -->
			
			<?php  
			if ($_SESSION['sess_user'] =='driver') 
			{
				if($noc=='' || $certi=='' || $licence=='')
				{?>
			<li class="chat-panel dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<span class="label label-info"></span>   <i class="icon-comments"></i>&nbsp; <i class="icon-chevron-down"></i>
				</a>

				<ul class="dropdown-menu dropdown-alerts">
					<?php  if($noc==''){?>
						<li>
						  <a href="#">
								
								<i class="icon-comment" >
									Please Upload Noc
								</i>

								<!--<span class="pull-right text-muted small"> 4 minutes ago</span> -->
						  </a>
						</li>
						<?php }?>
						
						<li class="divider"></li>
						
						<?php  if($certi==''){?>
						<li>
						  <a href="#">
								
								<i class="icon-comment" >
									Please Upload Certi
								</i>

								<!--<span class="pull-right text-muted small"> 4 minutes ago</span> -->
						  </a>
						</li>
						<?php }?>
					<li class="divider"></li>
						<?php  if($licence==''){?>
						<li>
						  <a href="#">
								
								<i class="icon-comment" >
									Please Upload Licence
								</i>

								<!--<span class="pull-right text-muted small"> 4 minutes ago</span> -->
						  </a>
						</li>
						<?php }?>
						
					
				</ul>

			</li>
			<?php  }
			}
			else if ($_SESSION['sess_user'] =='company') 
			{
				if($noc=='' || $certi=='' )
				{
			?>
				<li class="chat-panel dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<span class="label label-info"></span>   <i class="icon-comments"></i>&nbsp; <i class="icon-chevron-down"></i>
				</a>

				<ul class="dropdown-menu dropdown-alerts">
						<?php  if($noc==''){?>
						<li>
						  <a href="#">
								<i class="icon-comment" >
									Please Upload Noc
								</i>
						  </a>
						</li>
						<?php }?>
					<li class="divider"></li>
						<?php  if($certi==''){?>
						<li>
						  <a href="#">
								<i class="icon-comment" >
									Please Upload Certi
								</i>
						  </a>
						</li>
						<?php }?>
					
				</ul>

			</li>
			<?php }
			
			}
			?>
			<!-- END ALERTS SECTION -->

			<!--ADMIN SETTINGS SECTIONS -->

			<li class="dropdown">
				<a class="dropdown-toggle" data-toggle="dropdown" href="#">
					<i class="icon-user "></i>&nbsp; <i class="icon-chevron-down "></i>
				</a>
				<ul class="dropdown-menu dropdown-user">
					<li><a href="profile.php"><i class="icon-user"></i> <?=$langage_lbl['LBL_USER_PROFILE']; ?> </a></li>
					<li class="divider"></li>
					<li><a href="logout.php"><i class="icon-signout"></i> <?=$langage_lbl['LBL_LOGOUT']; ?> </a></li>
				</ul>
			</li>
			<!--END ADMIN SETTINGS -->
		</ul>

	</nav>

</div>
<!-- END HEADER SECTION -->
