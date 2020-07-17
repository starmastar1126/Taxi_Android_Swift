<?php 
include_once('../common.php');

require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();

if (!isset($generalobjAdmin)) {
     require_once(TPATH_CLASS . "class.general_admin.php");
     $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$message_print_id=$id;
$ksuccess=isset($_REQUEST['ksuccess']) ? $_REQUEST['ksuccess'] : 0;
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$action = ($id != '') ? 'Edit' : 'Add';
$tbl_name = 'pet_type';
$script = 'Pettype';
$eStatus = isset($_POST['eStatus']) ? $_POST['eStatus'] : '';

$vTitle_store =array();
//$vTitleval_store =array();

$sql = "SELECT * FROM `language_master` where eStatus='Active' ORDER BY `iDispOrder`";
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);
if($count_all > 0) {
  for($i=0;$i<$count_all;$i++) {
    $vValue = 'vTitle_'.$db_master[$i]['vCode'];
    array_push($vTitle_store ,$vValue);   
    $$vValue  = isset($_POST[$vValue])?$_POST[$vValue]:'';
    //array_push($vTitleval_store ,$$vValue); 
   
  }
}
if (isset($_POST['submit'])) {    
      if(!empty($id)){
          if(SITE_TYPE=='Demo')
          {
            header("Location:pettype_action.php?id=" . $id . '&success=2');
            exit;
          }        
      }
      for($i=0;$i<count($vTitle_store);$i++)
      {
        
            $q = "INSERT INTO ";
                   $where = '';
                   if ($action == 'Edit') {
                        $str = " ";
                   } 
                 
                   if ($id != '') {
                        $q = "UPDATE ";
                        $where = " WHERE `iPetTypeId` = '" . $id . "'";
                   }
                   $vValue = 'vTitle_'.$db_master[$i]['vCode'];
                    $query = $q . " `" . $tbl_name . "` SET `eStatus` = '" . $eStatus . "',
                  ".$vValue." = '" .$_POST[$vTitle_store[$i]]. "'"
                   . $where;  

                   $obj->sql_query($query);         

                   $id = ($id != '') ? $id : $obj->GetInsertId();     
                
                   if($action=="Add")
                   {
                      $ksuccess="1";
                    }
                   else
                   {
                      $ksuccess="2";
                   }
                   //echo $ksuccess;exit;
                   header("Location:pettype_action.php?id=" . $id . '&success=1 &ksuccess='.$ksuccess);
           
        }
      }
          
if ($action == 'Edit') {
     $sql = "SELECT * FROM " . $tbl_name . " WHERE iPetTypeId = '" . $id . "'";
     $db_data = $obj->MySQLSelect($sql); 	    
    
     if (count($db_data) > 0) {        

         for($i=0;$i<count($db_master);$i++)
          {
            foreach($db_data as $key => $value) {
              $vValue = 'vTitle_'.$db_master[$i]['vCode'];
              $$vValue = $value[$vValue];
              $eStatus = $value['eStatus'];

            
            }
          }
     }
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

     <!-- BEGIN HEAD-->
     <head>
          <meta charset="UTF-8" />
          <title>Admin | <?php  echo $langage_lbl_admin['LBL_PET_TYPE'];?>  <?= $action; ?></title>
          <meta content="width=device-width, initial-scale=1.0" name="viewport" />
          <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
          <?php 
          include_once('global_files.php');
          ?>
          <!-- On OFF switch -->
          <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
          <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
     </head>
     <!-- END  HEAD-->
     <!-- BEGIN BODY-->
     <body class="padTop53 " >

          <!-- MAIN WRAPPER -->
          <div id="wrap">
               <?php 
               include_once('header.php');
               include_once('left_menu.php');
               ?>
               <!--PAGE CONTENT -->
               <div id="content">
                    <div class="inner">
                         <div class="row">
                              <div class="col-lg-12">
                                   <h2><?= $action; ?> <?php  echo $langage_lbl_admin['LBL_PET_TYPE'];?><?= $vTitle_EN; ?></h2>
                                   <a href="pettype.php">
                                        <input type="button" value="Back to Listing" class="add-btn">
                                   </a>
                              </div>
                         </div>
                         <hr />
                         <div class="body-div">
                              <div class="form-group">
                                   <?php  if ($success == 1) {?>
                                   <div class="alert alert-success alert-dismissable">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                          <?php 
                                          if($ksuccess == "1")
                                          {?>
                                              Record Insert Successfully.
                                          <?php  } else
                                          {?>
                                              Record Updated Successfully.
                                          <?php  } ?>
                                        
                                   </div><br/>
                                   <?php } ?>

                                   <?php  if ($success == 2) {?>
                                   <div class="alert alert-danger alert-dismissable">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                        "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                                   </div><br/>
                                   <?php } ?>
                                   <form method="post" action="" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= $id; ?>"/>   

                                      <?php 
                                        if($count_all > 0) {
                                          for($i=0;$i<$count_all;$i++) {
                                            $vCode = $db_master[$i]['vCode'];
                                            $vTitle = $db_master[$i]['vTitle'];
                                            $eDefault = $db_master[$i]['eDefault'];

                                            $vValue = 'vTitle_'.$vCode;

                                            $required = ($eDefault == 'Yes')?'required':'';
                                            $required_msg = ($eDefault == 'Yes')?'<span class="red"> *</span>':'';
                                          ?>
                                                                            

                                        <div class="row">
                                             <div class="col-lg-12">
                                             <label>Title (<?=$vTitle;?>) <span class="red"> *</span></label>
                                                 
                                             </div>
                                             <div class="col-lg-6">
                                             <input type="text" class="form-control" name="<?=$vValue;?>" id="<?=$vValue;?>" value="<?=$$vValue;?>" placeholder="<?=$vTitle;?>Value" <?=$required;?>>
                                                 
                                             </div>
                                        </div>
                                        <?php  }
                                  } ?>

                                        <!--<div class="row">
                                             <div class="col-lg-12">
                                                  <label>Titles (Spanish)<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text"  class="form-control" name="vTitle_ES"  id="vTitle_ES" value="<?= $vTitle_ES ?>" placeholder="Title Spanish" required >
                                             </div>
                                        </div>-->

                                         <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Status<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select  class="form-control" name = 'eStatus'  id= 'eStatus' required>                                   
                                                       <option value="Active" <?php if('Active' == $db_data[0]['eStatus']){?>selected<?php  } ?>>Active</option>
                                                       <option value="Inactive"<?php if('Inactive' == $db_data[0]['eStatus']){?>selected<?php  } ?>>Inactive</option>                                                      
                                                       </option>                                                    
                                                  </select>
                                             </div>
                                        </div>                                       
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <input type="submit" class="save btn-info" name="submit" id="submit" value="<?= $action; ?> Pet Type"  >
                                             </div>
                                        </div>
                                   </form>
                              </div>
                         </div>
                            <div style="clear:both;"></div>
                    </div>
                 
               </div>
               <!--END PAGE CONTENT -->
          </div>
          <!--END MAIN WRAPPER -->


          <?php 
          include_once('footer.php');
          ?>
          <script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>      
         
     </body>
     <!-- END BODY-->
</html>
