<?php 
  include_once('../common.php');
  if(!isset($generalobjAdmin)){
  	require_once(TPATH_CLASS."class.general_admin.php");
  	$generalobjAdmin = new General_admin();
  }
  
  $generalobjAdmin->check_member_login();

  $id = $_GET['iTripId'];

 $sql = "SELECT tm.iTripId AS tm_iTripId,tm.dAddedDate,tm.iMessageId AS tm_Messageid,tm.tMessage,t.iTripId AS t_iTripId,t.vRideNo,t.tStartDate AS t_tStartDate,t.tEndDate AS t_tEndDate,t.iUserId AS t_iUserId,t.iDriverId AS t_iDriverId,d.iDriverId AS d_iDriverId, CONCAT(d.vName,' ',d.vLastName) AS driverName,d.vImage AS d_vImage,u.iUserId AS u_iUserId,CONCAT(u.vName,' ',u.vLastName) AS riderName,u.vImgName AS u_ImgName FROM trip_messages tm 
  LEFT JOIN trips t ON tm.iTripId = t.iTripId
  LEFT JOIN register_driver d ON d.iDriverId = tm.iToMemberId
  LEFT JOIN register_user u ON u.iUserId = tm.iFromMemberId WHERE tm.iTripId = '".$id."'"; 
   $db_message = $obj->MySQLSelect($sql);
  
?>
<!DOCTYPE html>
<!--[if IE 8]> 
<html lang="en" class="ie8">
  <![endif]-->
  <!--[if IE 9]> 
  <html lang="en" class="ie9">
    <![endif]-->
    <!--[if !IE]><!--> 
    <html lang="en">
      <!--<![endif]-->
      <!-- BEGIN HEAD-->
      <head>
        <meta charset="UTF-8" />
        <title><?=$SITE_NAME;?> | Dashboard</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <![endif]-->
        <!-- GLOBAL STYLES -->
        <?php  include_once('global_files.php');?>
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/new_main.css" />
        <link rel="stylesheet" href="css/adminLTE/AdminLTE.min.css" />
        <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="js/plugins/morris/raphael-min.js"></script>
        <script type="text/javascript" src="js/plugins/morris/morris.min.js"></script> 
        <script type="text/javascript" src="js/actions.js"></script>
        <!-- END THIS PAGE PLUGINS-->
        <!--END GLOBAL STYLES -->
        <!-- PAGE LEVEL STYLES -->
        <!-- END PAGE LEVEL  STYLES -->
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">
          .chat li{
              border-bottom:none !important;
          }
        </style>
      </head>
      <!-- END  HEAD-->
      <!-- BEGIN BODY-->
      <body class="padTop53">
        <!-- MAIN WRAPPER -->
        <div id="wrap">
          <?php  include_once('header.php'); ?>
          <?php  include_once('left_menu.php'); ?>
          <!--PAGE CONTENT -->
          <div id="content">
            <div class="inner" style="min-height:700px;">
              <div class="row">
                <div class="col-lg-12">
                  <h1> <?php  echo $langage_lbl_admin['LBL_MESSAGE_ADMIN'];?> </h1>
                </div>
              </div>
            
              <!--BLOCK SECTION -->
              <!-- COMMENT AND NOTIFICATION  SECTION -->
              <div class="row">
                <div class="col-lg-12">
                  <div class="chat-panel panel panel-success">
                    <div class="panel-heading">
                      <div class="panel-title-box">
                     <h3><?php  echo $langage_lbl_admin['LBL_TRIP_NO'];?> : <?php  echo $db_message[0]['vRideNo']; ?></h3>
                      <!--  <input type="button" class="add-btn" value="<?=$langage_lbl['LBL_CLOSE_TXT']; ?>" onClick="javascript:window.top.close();"> -->
                        <a class="btn btn-info btn-sm ride-view-all001" style="margin-top: -24px;" value="<?=$langage_lbl['LBL_CLOSE_TXT']; ?>" onClick="javascript:window.top.close();"><?=$langage_lbl['LBL_CLOSE_TXT']; ?></a>
                      </div>
                    </div>
                    <?php   for($i=0,$n=$i+2;$i<count($db_message);$i++,$n++){?>
                    <div class="panel-heading" style="background:none;">
                      <ul class="chat">
                        <?php  if($n%2==0){ ?>
                         <li class="left clearfix">
                            <span class="chat-img pull-left">
                            <?php  if($db_message[$i]['d_vImage']!='' && $db_message[$i]['d_vImage']!="NONE" && file_exists( "../webimages/upload/Driver/".$db_message[$i]['d_iDriverId']."/".$db_message[$i]['d_vImage'])){?>
                                    <img src="../webimages/upload/Driver/<?php  echo $db_message[$i]['d_iDriverId']."/".$db_message[$i]['d_vImage'];?>" alt="User Avatar" class="img-circle"  height="50" width="50"/>
                            <?php  }else{?>
                                   <img src="../assets/img/profile-user-img.png" alt="" class="img-circle"  height="50" width="50">
                            <?php }?>
                            </span>
                            <div class="chat-body clearfix">
                              <div class="header">
                                <strong class="primary-font "> <?php  echo $generalobjAdmin->clearName($db_message[$i]['driverName']); ?> </strong>
                                <small class="pull-right text-muted label label-danger">
                                 <?php 
                                   echo date('d-F-Y h A',strtotime($db_message[$i]['dAddedDate'])); 
                                 ?>
                                </small>
                                <strong class="pull-right primary-font"> <?php  //echo $db_message[$i]['driverName']; ?></strong>
                               </div>
                               <br />
                               <p>
                                <?php  echo $db_message[$i]['tMessage'];?>
                              </p>
                            </div>
                          </li>
                           
                        <?php  } else { ?>
                             <li class="right clearfix">
                          
                              <span class="chat-img pull-right">
                              <?php  if($db_message[$i]['u_ImgName']!='' && $db_message[$i]['u_ImgName']!="NONE" && file_exists( "../webimages/upload/Passenger/".$db_message[$i]['u_iUserId']."/".$db_message[$i]['u_ImgName'])){?>
                              <img src="../webimages/upload/Passenger/<?php  echo $db_message[$i]['u_iUserId']."/".$db_message[$i]['u_ImgName'];?>" alt="User Avatar" class="img-circle"  height="50" width="50"/>
                              <?php  }else{?>
                              <img src="../assets/img/profile-user-img.png" alt="" class="img-circle"  height="50" width="50">
                              <?php }?>
                              </span>
                              <div class="chat-body clearfix">
                                <div class="header">
                                  <small class=" text-muted label label-info">
                                  <?php 
                                    echo date('d-F-Y h A',strtotime($db_message[$i]['dAddedDate'])); 
                                   ?>
                                  </small>
                                  <strong class="pull-right primary-font"> <?php  echo $db_message[$i]['riderName']; ?></strong>
                              </div>
                               <p style="text-align: right;">
                                      <?php  echo $db_message[$i]['tMessage'];?>
                                   </p>
                                </div>
                            </li>
                       <?php  }?>
                      </ul>
                    </div>
                    <?php  } ?>
                  </div>
                </div>
              </div>
            </div>
            <!-- END COMMENT AND NOTIFICATION  SECTION -->
          </div>
        </div>
        <!--END PAGE CONTENT -->
        
        <?php  include_once('footer.php'); ?>
      </body>
      <!-- END BODY-->
      <?php 
        // if(SITE_TYPE=='Demo'){
        	// $generalobjAdmin->remove_unwanted();
          // }
        ?>
    </html>