<?php 
$curr_url = basename($_SERVER['PHP_SELF']);
//include 'common.php' ;
$user = (isset($_SESSION["sess_user"]))?$_SESSION["sess_user"]:'';
if ($user == 'driver') {
     $sql = "select * from register_driver where iDriverId = '" . $_SESSION['sess_iUserId'] . "'";
     $db_data = $obj->sql_query($sql);
     if ($db_data[0]['vImage'] == "NONE" || $db_data[0]['vImage'] == '' ) {
          $db_data[0]['img'] = "";
     } else {

		   $db_data[0]['img'] = $tconfig["tsite_upload_images_driver"] . '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_data[0]['vImage'];
     }
}
if ($user == 'company') {
  $sql = "select * from company where iCompanyId = '" . $_SESSION['sess_iUserId'] . "'";
     $db_data = $obj->sql_query($sql);

     if ($db_data[0]['vImage'] == "NONE" || $db_data[0]['vImage'] =='') {
         $db_data[0]['img'] = "";
     } else {
		   $db_data[0]['img'] = $tconfig["tsite_upload_images_compnay"] . '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_data[0]['vImage'];
     }
}
if ($user == 'rider') {
     $sql = "select * from register_user where iUserId = '" . $_SESSION['sess_iUserId'] . "'";
     $db_data = $obj->sql_query($sql);
     if ($db_data[0]['vImgName'] != "NONE") {
          $db_data[0]['img'] = $tconfig["tsite_upload_images_passenger"] . '/' . $_SESSION['sess_iUserId'] . '/2_' . $db_data[0]['vImgName'];
     } else {
          $db_data[0]['img'] = "";
     }
}  //echo "<pre>";print_r($db_data);echo "</pre>";
?>

<!-- MENU SECTION -->
<div id="left">
     <div class="media user-media well-small">
          <a class="user-link" href="#">
               <?php  if ($db_data[0]['img'] != '') { ?>
                    <img class="media-object img-thumbnail user-img" alt="User Picture" src="<?= $db_data[0]['img'] ?>" style="width:68px;height:68px;"/>
               <?php  } else { ?>
                    <img class="media-object img-thumbnail user-img" alt="User Picture" src="assets/img/profile-user-img.png" />
               <?php  } ?>
          </a>
          <br />
          <div class="media-body">
               <h5 class="media-heading"><?= $db_data[0]['vName'] ?></h5>
               <!-- <ul class="list-unstyled user-info">
                            <li>
                            <a class="btn btn-success btn-xs btn-circle" style="width: 10px;height: 12px;"></a> Online
                            </li>
               </ul> -->
          </div>
          <br />
     </div>
     <ul id="menu" class="collapse">
          <?php  if($user == 'company') {?>
         <li class="panel <?=(isset($script) && $script == 'Profile')?'active':'';?>">
               <a href="profile.php" >
                    <i class="icon-table"></i> Profile
               </a>
          </li>

         <li class="panel <?=(isset($script) && $script == 'Driver')?'active':'';?>">
               <a href="driver.php" >
                    <i class="icon-table"></i> Driver
               </a>
          </li>
          <li class="panel <?=(isset($script) && $script == 'Vehicle')?'active':'';?>">
               <a href="vehicle.php" >
                    <i class="icon-table"></i> Vehicles
               </a>
          </li>


           <li class="panel <?=(isset($script) && $script == 'Trips')?'active':'';?>">
               <a href="company_trip.php" >
                    <i class="icon-table"></i> Trips
               </a>
          </li>
          
           
          <li class="panel <?php 
          if ($curr_url == "logout.php") {
               echo "active";
          }
          ?>">
               <a href="logout.php" >
                    <i class="icon-table"></i> Logout
               </a>
          </li>
          <?php  } if($user == 'driver'){?>

         <li class="panel <?=(isset($script) && $script == 'Profile')?'active':'';?>">
               <a href="profile.php" >
                    <i class="icon-table"></i> Profile
               </a>
          </li>

           <li class="panel <?=(isset($script) && $script == 'Vehicle')?'active':'';?>">
               <a href="vehicle.php" >
                    <i class="icon-table"></i> Vehicles
               </a>
          </li>

         <li class="panel <?=(isset($script) && $script == 'Trips')?'active':'';?>">
               <a href="driver_trip.php" >
                    <i class="icon-table"></i> Trips
               </a>
          </li>
		 <?php if($PAYMENT_ENABLED=='Yes'){?>
		 <li class="panel <?=(isset($script) && $script == 'payment_request')?'active':'';?>">
               <a href="payment_request.php" >
                    <i class="icon-table"></i> Payment
               </a>
          </li>
		  <?php  }?>
          <li class="panel <?php 
          if ($curr_url == "logout.php") {
               echo "active";
          }
          ?>">
               <a href="logout.php" >
                    <i class="icon-table"></i> Logout
               </a>
          </li>
          <?php  }  if($user == 'rider'){?>
            <li class="panel <?=(isset($script) && $script == 'Profile')?'active':'';?>">
               <a href="profile_rider.php">
                    <i class="icon-user"></i> Profile
               </a>
          </li>

          <li class="panel <?=(isset($script) && $script == 'Trips')?'active':'';?>">
               <a href="mytrip.php" >
                    <i class="icon-table"></i> Trips
               </a>
          </li>

          <li class="panel <?php 
          if ($curr_url == "logout.php") {
               echo "active";
          }
          ?>">
               <a href="logout.php" >
                    <i class="icon-off"></i> Logout
               </a>
          </li>
          <?php  } ?>
     </ul>

</div>
<!--END MENU SECTION -->
