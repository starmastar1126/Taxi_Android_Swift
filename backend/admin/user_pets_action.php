<?php 
include_once('../common.php');

//print_r($_SESSION['sess_lang']);

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
$tbl_name = 'user_pets';
$script = 'user_pets';
$iPetTypeId = isset($_POST['iPetTypeId']) ? $_POST['iPetTypeId'] : '';
$iUserId = isset($_POST['iUserId']) ? $_POST['iUserId'] : '';
$vTitle = isset($_POST['vTitle']) ? $_POST['vTitle'] : '';
$vWeight = isset($_POST['vWeight']) ? $_POST['vWeight'] : '';
$tBreed = isset($_POST['tBreed']) ? $_POST['tBreed'] : '';
$tDescription = isset($_POST['tDescription']) ? $_POST['tDescription'] : '';

//For register_user
$sql="select * from  register_user where eStatus='Active'";
$db_res_user = $obj->MySQLSelect($sql);

$sql1="select vTitle_".$default_lang.",iPetTypeId from  pet_type where eStatus='Active'";
$db_pet_type = $obj->MySQLSelect($sql1);

if (isset($_POST['submit'])) {
     //echo '<pre>'; print_r($_POST); exit;
     //Start :: Upload Image Script
      if(!empty($id)){
          if(SITE_TYPE=='Demo')
          {
            header("Location:user_pets_action.php?id=" . $id . '&success=2');
            exit;
          }        
      }
     $q = "INSERT INTO ";
     $where = '';
     if ($action == 'Edit') {
          $str = " ";
     } 
	 
     if ($id != '') {
          $q = "UPDATE ";
          $where = " WHERE `iUserPetId` = '" . $id . "'";
     }
     $query = $q . " `" . $tbl_name . "` SET
		`iUserId` = '" . $iUserId . "',
    `iPetTypeId` = '" . $iPetTypeId . "', 
    `vTitle` = '" . $vTitle . "', 
    `vWeight` = '" . $vWeight . "', 
		`tBreed` = '" . $tBreed . "',	
		`tDescription` = '" . $tDescription . "' $str" . $where;
     //echo '<pre>'; print_r($query); exit;
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
     header("Location:user_pets_action.php?id=" . $id . '&success=1&ksuccess='.$ksuccess);
}
// for Edit

if ($action == 'Edit') {
     $sql = "SELECT * FROM " . $tbl_name . " WHERE  iUserPetId = '" . $id . "'";
     $db_data = $obj->MySQLSelect($sql); 	  
    
     if (count($db_data) > 0) {
          foreach ($db_data as $key => $value) {
               $iUserId = $value['iUserId'];
               $iPetTypeId = $value['iPetTypeId'];
               $vTitle = $value['vTitle'];
               $vWeight = $value['vWeight'];
               $tBreed = $value['tBreed'];
               $tDescription = $value['tDescription'];
      
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
                                   <h2><?= $action; ?> <?php  echo $langage_lbl_admin['LBL_USER_PETS_ADMIN'];?> <?= $vTitle; ?></h2>
                                   <a href="user_pets.php">
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
                                                                            
                                         <div class="row">
                                             <div class="col-lg-12">
                                                  <label><?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?><span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select class="form-control" name = 'iUserId' required  id="iUserId" onChange="validate_rider_langcode(this.value)" >
                                                       <option value="">--select--</option>
                                                       <?php  for($i=0;$i<count($db_res_user);$i++){ ?>
                                                       <option value = "<?= $db_res_user[$i]['iUserId'] ?>" <?php if($iUserId==$db_res_user[$i]['iUserId']){?>selected<?php  }  ?>><?= $db_res_user[$i]['vName'] ?></option>
                                                       <?php  } ?>
                                                  </select>
                                             </div>
                                        </div>  

                                         <div class="row">
                                             <div class="col-lg-12">
                                                  <label><?php  echo $langage_lbl_admin['LBL_PET_TYPE'];?><span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select class="form-control" name = 'iPetTypeId' id="iPetTypeId"   required>
                                                       <option value="">--select--</option>
                                                       
                                                  </select>
                                             </div>
                                        </div>          

                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label><?php  echo $langage_lbl_admin['LBL_TITLE_TXT_ADMIN'];?><span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text"  class="form-control" name=" vTitle"  id="vTitle" value="<?= $vTitle ?>" placeholder="Title" required>
                                             </div>
                                        </div>


                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label><?php  echo $langage_lbl_admin['LBL_WEIGHT_TXT_ADMIN'];?><span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text"  class="form-control" name="vWeight"  id="vWeight" value="<?= $vWeight ?>" placeholder="Weight" required >
                                             </div>
                                        </div>

                                         <div class="row">
                                             <div class="col-lg-12">
                                                  <label><?php  echo $langage_lbl_admin['LBL_BREED_TXT_ADMIN'];?><span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                              <textarea  class="form-control ckeditor" rows="10" name="tBreed" required><?php  echo $tBreed;?></textarea>                                                
                                             </div>
                                        </div>
                                         <div class="row">
                                             <div class="col-lg-12">
                                                  <label><?php  echo $langage_lbl_admin['LBL_DESCRIPTION_TXT_ADMIN'];?><span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                              <textarea class="form-control ckeditor" rows="10" name="tDescription" required><?php  echo $tDescription;?></textarea>

                                                                             
                                             </div>
                                        </div>                                                                          
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <input type="submit" class="save btn-info" name="submit" id="submit" value="<?= $action; ?> User Pets"  >
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
<?php  
if ($action == 'Edit') { ?>  

  <script> 
  $( window ).load(function() {
 
    validate_rider_langcode('<?php  echo $iUserId;?>','<?php  echo $_REQUEST['id']; ?>')

  });

  
<?php  } ?>

<script type="text/javascript">
      function validate_rider_langcode(id,iUserPetId){
      // alert(id);
       //alert(id);
        if(id != ""){

           var request = $.ajax({
            type: "POST",
            url: 'ajax_find_rider_by_langcode.php?uid',
            data: {uid:id,iUserPetId:iUserPetId},
            success: function (data) {
           // alert(data);
             $('#iPetTypeId').html(data);
            }
          });    

              
        }        
      }

      </script>
