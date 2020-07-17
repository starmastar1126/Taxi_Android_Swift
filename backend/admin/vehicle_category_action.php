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
$sub_action = isset($_REQUEST['sub_action']) ? $_REQUEST['sub_action'] : '';
$sub_cid = isset($_REQUEST['sub_cid']) ? $_REQUEST['sub_cid'] : '';

$message_print_id=$id;
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$var_msg = isset($_REQUEST['var_msg']) ? $_REQUEST['var_msg'] : "";
$action = ($id != '') ? 'Edit' : 'Add';
$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
$previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';

$tbl_name = 'vehicle_category';
$script = 'VehicleCategory';

$vCategory_EN = isset($_POST['vCategory_EN']) ? $_POST['vCategory_EN'] : '';
$tCategoryDesc_EN = isset($_POST['tCategoryDesc_EN']) ? $_POST['tCategoryDesc_EN'] : '';
$eBeforeUpload   = isset($_POST['eBeforeUpload'])?$_POST['eBeforeUpload']:''; 
$eAfterUpload   = isset($_POST['eAfterUpload'])?$_POST['eAfterUpload']:''; 
$eStatus   = isset($_POST['eStatus'])?$_POST['eStatus']:'';  
$iParentId   = isset($_POST['vCategory'])?$_POST['vCategory']:'';

$ePriceType   = isset($_POST['ePriceType'])?$_POST['ePriceType']:'Service';
$vTitle_store =array();
$vDesc_store =array();
$sql = "SELECT * FROM `language_master` where eStatus='Active' ORDER BY `iDispOrder`";
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);
if($count_all > 0) {
  for($i=0;$i<$count_all;$i++) {
    $vValue = 'vCategory_'.$db_master[$i]['vCode'];
    $vValue_desc = 'tCategoryDesc_'.$db_master[$i]['vCode'];
    array_push($vTitle_store ,$vValue);   
    $$vValue  = isset($_POST[$vValue])?$_POST[$vValue]:'';
    array_push($vDesc_store ,$vValue_desc);   
    $$vValue_desc  = isset($_POST[$vValue_desc])?$_POST[$vValue_desc]:''; 
   
  }
}

if(isset($_POST['btnsubmit'])){
  if(isset($_FILES['vLogo']) && $_FILES['vLogo']['name'] != "") {
    $filecheck = basename($_FILES['vLogo']['name']);
    $fileextarr = explode(".", $filecheck);
    $ext = strtolower($fileextarr[count($fileextarr) - 1]);
    $flag_error = 0;
    if($ext != "png") {
      $flag_error = 1;
      $var_msg = "Upload only png image";
    }
    $data = getimagesize($_FILES['vLogo']['tmp_name']);

    $width = $data[0];
    $height = $data[1];
     
    if($width != 360 && $height != 360) {
      $flag_error = 1;
      $var_msg = "Please Upload image only 360px * 360px";
    }

     if ($flag_error == 1) {
      
        if($action == "Add") {
  		  
          if($sub_action == "sub_category"){			 
            header("Location:vehicle_category_action.php?sub_action=sub_category&sub_cid=".$sub_cid."&var_msg=".$var_msg);
            exit; 
          } else {
            header("Location:vehicle_category_action.php?id=".$id."&var_msg=".$var_msg);
            exit; 			 
          }  
        
        } else {

          if($sub_action == "sub_category"){
            header("Location:vehicle_category_action.php?id=".$id."&sub_action=sub_category&sub_cid=".$sub_cid."&var_msg=".$var_msg);   
            exit;				 
          } else {			 
            header("Location:vehicle_category_action.php?id=".$id."&var_msg=".$var_msg);      
            exit; 
          }

        }       
       
     }
  }
      
    if(SITE_TYPE =='Demo'){
  		 if($sub_action == "sub_category"){
  			  header("Location:vehicle_category_action.php?id=".$id."&sub_action=sub_category&sub_cid=".$sub_cid."&success=2");
  			  exit;	
  		 } else {
    			header("Location:vehicle_category_action.php?id=".$id."&success=2");
    			exit; 
  		 }
    }  
    for($i=0;$i<count($vTitle_store);$i++) {   
          $q = "INSERT INTO ";
          $where = '';

        if($id != '' ) {
          $q = "UPDATE ";
          $where = " WHERE `iVehicleCategoryId` = '".$id."'";
        }   
        
      $vValue = 'vCategory_'.$db_master[$i]['vCode'];
      $vValue_desc = 'tCategoryDesc_'.$db_master[$i]['vCode'];

      $query = $q . " `" . $tbl_name . "` SET
      `eBeforeUpload` = '" . $eBeforeUpload . "',
      `eAfterUpload` = '" . $eAfterUpload . "',
      `eStatus` = '" . $eStatus . "',
      `iParentId` = '" . $iParentId . "',
      `ePriceType`= '". $ePriceType ."',
      ".$vValue." = '" .$_POST[$vTitle_store[$i]]. "',
      ".$vValue_desc." = '" .$_POST[$vDesc_store[$i]]. "'"
      . $where;
      $obj->sql_query($query);
      $id = ($id != '') ? $id : $obj->GetInsertId();  

     }   
    

    if(isset($_FILES['vLogo']) && $_FILES['vLogo']['name'] != ""){    
    
      $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];      
      $temp_gallery = $img_path . '/';
      $image_object = $_FILES['vLogo']['tmp_name'];
      $image_name = $_FILES['vLogo']['name'];

      $check_file_query = "select iVehicleCategoryId,vLogo from vehicle_category where iVehicleCategoryId=" . $id;
      $check_file = $obj->sql_query($check_file_query);   

      
      if($image_name != "") {
      
        if($message_print_id != "") {
           $check_file['vLogo'] = $img_path . '/' . $id . '/android/' . $check_file[0]['vLogo'];    


           $android_path = $img_path . '/' . $id . '/android';
           $ios_path = $img_path . '/' . $id . '/ios';
         
          
          if ($check_file['vLogo'] != '' && file_exists($check_file['vLogo'])) {
            @unlink($android_path . '/'.$check_file['vLogo']);
            @unlink($android_path . '/mdpi_'.$check_file['vLogo']);
            @unlink($android_path . '/hdpi_'.$check_file['vLogo']);
            @unlink($android_path . '/xhdpi_'.$check_file['vLogo']);
            @unlink($android_path . '/xxhdpi_'.$check_file['vLogo']);
            @unlink($android_path . '/xxxhdpi_'.$check_file['vLogo']);
            @unlink($ios_path . '/'.$check_file['vLogo']);
            @unlink($ios_path . '/1x_'.$check_file['vLogo']);
            @unlink($ios_path . '/2x_'.$check_file['vLogo']);
            @unlink($ios_path . '/3x_'.$check_file['vLogo']);
          }
        }
          $Photo_Gallery_folder = $img_path . '/' . $id . '/';
         
          $Photo_Gallery_folder_android = $Photo_Gallery_folder . 'android/';
         $Photo_Gallery_folder_ios = $Photo_Gallery_folder . 'ios/';
        if (!is_dir($Photo_Gallery_folder)) {
           mkdir($Photo_Gallery_folder, 0777);
           mkdir($Photo_Gallery_folder_android, 0777);
           mkdir($Photo_Gallery_folder_ios, 0777);
        }   
       
       $vVehicleType1 = str_replace(' ','',$vCategory_.$default_lang);  
      $img = $generalobj->general_upload_image_vehicle_android($image_object, $image_name, $Photo_Gallery_folder_android, $tconfig["tsite_upload_images_vehicle_category_size1_android"], $tconfig["tsite_upload_images_vehicle_category_size2_android"], $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size4_android"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_category_size5_both"], $Photo_Gallery_folder_android,$vVehicleType1,NULL);
      $img1 = $generalobj->general_upload_image_vehicle_ios($image_object, $image_name, $Photo_Gallery_folder_ios, '', '', $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size5_both"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_category_size5_ios"], $Photo_Gallery_folder_ios,$vVehicleType1,NULL);
      $vImage = "ic_car_".$vVehicleType1.".png";      

        $sql = "UPDATE ".$tbl_name." SET `vLogo` = '" . $vImage . "' WHERE `iVehicleCategoryId` = '" . $id . "'"; 
      
        $obj->sql_query($sql);
      }
    }
     
      if(isset($_FILES['vLogo1']) && $_FILES['vLogo1']['name'] != ""){
      $img_path = $tconfig["tsite_upload_images_vehicle_category_path"];
      $temp_gallery = $img_path . '/';
      $image_object = $_FILES['vLogo1']['tmp_name'];
      $image_name = $_FILES['vLogo1']['name'];
       $check_file_query = "select iVehicleCategoryId,vLogo1 from vehicle_category where iVehicleCategoryId=" . $id;
      $check_file = $obj->sql_query($check_file_query);
        if($image_name != "") {
          if($message_print_id != "") {
            $check_file['vLogo1'] = $img_path . '/' . $id . '/android/' . $check_file[0]['vLogo1'];
            $android_path = $img_path . '/' . $id . '/android';
            $ios_path = $img_path . '/' . $id . '/ios';
            
            if ($check_file['vLogo1'] != '' && file_exists($check_file['vLogo1'])) {
              @unlink($android_path . '/'.$check_file['vLogo1']);
              @unlink($android_path . '/mdpi_hover_'.$check_file['vLogo1']);
              @unlink($android_path . '/hdpi_hover_'.$check_file['vLogo1']);
              @unlink($android_path . '/xhdpi_hover_'.$check_file['vLogo1']);
              @unlink($android_path . '/xxhdpi_hover_'.$check_file['vLogo1']);
              @unlink($android_path . '/xxxhdpi_hover_'.$check_file['vLogo1']);
              @unlink($ios_path . '/'.$check_file['vLogo1']);
              @unlink($ios_path . '/1x_hover_'.$check_file['vLogo1']);
              @unlink($ios_path . '/2x_hover_'.$check_file['vLogo1']);
              @unlink($ios_path . '/3x_hover_'.$check_file['vLogo1']);
            }
          }
          $Photo_Gallery_folder = $img_path . '/' . $id . '/';
          $Photo_Gallery_folder_android = $Photo_Gallery_folder . '/android/';
          $Photo_Gallery_folder_ios = $Photo_Gallery_folder . '/ios/';
          if (!is_dir($Photo_Gallery_folder)) {
             mkdir($Photo_Gallery_folder, 0777);
             mkdir($Photo_Gallery_folder_android, 0777);
             mkdir($Photo_Gallery_folder_ios, 0777);
          } 
          $vVehicleType1 = str_replace(' ','',$vCategory_.$default_lang);    
            $img = $generalobj->general_upload_image_vehicle_android($image_object, $image_name, $Photo_Gallery_folder_android, $tconfig["tsite_upload_images_vehicle_category_size1_android"], $tconfig["tsite_upload_images_vehicle_category_size2_android"], $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size4_android"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_type_size5_both"], $Photo_Gallery_folder_android,$vVehicleType1,"hover_");
            $img1 = $generalobj->general_upload_image_vehicle_ios($image_object, $image_name, $Photo_Gallery_folder_ios, '', '', $tconfig["tsite_upload_images_vehicle_category_size3_both"], $tconfig["tsite_upload_images_vehicle_category_size5_both"], '', '', 'Y', $tconfig["tsite_upload_images_vehicle_category_size5_ios"], $Photo_Gallery_folder_ios,$vVehicleType1,"hover_");
            $vImage1 = "ic_car_".$vVehicleType1.".png";
            
            $sql = "UPDATE ".$tbl_name." SET `vLogo1` = '" . $vImage1 . "' WHERE `iVehicleCategoryId` = '" . $id . "'";
            $obj->sql_query($sql);
        }
      } 
    
     //$obj->sql_query($query);
	if ($action == "Add") {
      $_SESSION['success'] = '1';
      $_SESSION['var_msg'] = 'Record Insert Successfully.';
    } else {
      $_SESSION['success'] = '1';
      $_SESSION['var_msg'] = 'Record Updated Successfully.';
    }
    header("Location:".$backlink);exit;
	
    
  }

// for Edit
  if($action == 'Edit') {

     $sql = "SELECT * FROM " . $tbl_name . " WHERE iVehicleCategoryId = '" . $id . "'";
     $db_data = $obj->MySQLSelect($sql);

     $vLabel = $id;
     if (count($db_data) > 0) {

          for($i=0;$i<count($db_master);$i++)
          {
            foreach($db_data as $key => $value) {
              $vValue = 'vCategory_'.$db_master[$i]['vCode'];
              $$vValue = $value[$vValue];
              $vValue_desc = 'tCategoryDesc_'.$db_master[$i]['vCode'];
              $$vValue_desc = $value[$vValue_desc];
              $eBeforeUpload = $value['eBeforeUpload'];
              $eAfterUpload = $value['eAfterUpload'];
              $eStatus = $value['eStatus'];
              $iParentId = $value['iParentId'];
              $ePriceType = $value['ePriceType'];
              $vLogo = $value['vLogo'];
              $iVehicleCategoryId = $value['iVehicleCategoryId'];            
            }
          }

    }

  }   

  $sql="select vCategory_".$default_lang.", iVehicleCategoryId from vehicle_category where iVehicleCategoryId='".$sub_cid."'";
  $db_data1 = $obj->MySQLSelect($sql);

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

     <!-- BEGIN HEAD-->
     <head>
          <meta charset="UTF-8" />
          <title>Admin | <?=$langage_lbl_admin['LBL_VEHICLE_CATEGORY_ADMIN'];?> <?= $action; ?></title>
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
                                   <h2> <?php  if($sub_cid !=""){ ?><?= "Sub ".$langage_lbl_admin['LBL_VEHICLE_CATEGORY_TXT_ADMIN'];?>(<?php  echo $db_data1[0]['vCategory_'.$default_lang] ?>)<?php  } else {?> <?=$langage_lbl_admin['LBL_VEHICLE_CATEGORY_TXT_ADMIN'];?><?php  }?></h2>
								  <?php  if($sub_cid !=""){
									  $redirect_back_page = 'vehicle_sub_category.php?sub_cid='.$sub_cid;
								  }else{
                  	$redirect_back_page = 'vehicle_category.php';
								  }?>
                                   <a href="<?php  echo $redirect_back_page;?>">
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
                                        <?=$langage_lbl_admin['LBL_VEHICLE_CATEGORY_ADMIN'];?> Updated successfully.
                                   </div><br/>
                                   <?php  } elseif ($success == 2) { ?>
                                    <div class="alert alert-danger alert-dismissable">
                                         <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                         "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                                    </div><br/>
                                  <?php  } 
								  if($_REQUEST['var_msg']  != ""){
								  ?>
									 <div class="alert alert-danger alert-dismissable">
                                         <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                        <?php  echo $_REQUEST['var_msg']; ?>
                                    </div><br/>  
								  <?php  }
								  ?>
                  
                   <div id="price1" ></div>
                   <div id="price" ></div>
                                   <form id="vtype" method="post" action="" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= $id; ?>"/> 
                                        <input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
                                        <input type="hidden" name="backlink" id="backlink" value="vehicle_category.php"/>
										<?php         
										if($sub_action == "sub_category"){
										?>                             
                                        <div class="row" style="display: none;">
                                             <div class="col-lg-12">
                                                  <label>Parent Category :</label>
                                             </div>
                                             <div class="col-lg-6"> 
         										<select  class="form-control" name = 'vCategory'  id= 'vCategory' >
        														<?php  for ($i = 0; $i < count($db_data1); $i++) { ?>													  
        														<option value = "<?php  echo $db_data1[$i]['iVehicleCategoryId'] ?>" <?= ($db_data1[$i]['iVehicleCategoryId'] == $iVehicleCategoryId) ? 'selected' :'' ; ?>><?php  echo $db_data1[$i]['vCategory_'.$default_lang]; ?></option>
        														<?php  } ?>
                            </select> 
                             </div>
                        </div>
		
										
										<?php  }else{ ?>
											
											
											<input type="hidden" name= "vCategory" value="0">
										<?php  } ?>

										
                                        <div class="row epricetype" style="display: none;">
                                            <div class="col-lg-12">
                                              <label>Price Based On :</label>
                                            </div>
                                            <div class="col-lg-6">
                                              <select  class="form-control" name = 'ePriceType'  id= 'ePriceType' >
                                                <option value="Service" <?php if('Service' == $db_data[0]['ePriceType']){?>selected<?php  } ?>>Service ( Site Administrator will define the price)</option>
                                                <option value="Provider" <?php if('Provider' == $db_data[0]['ePriceType']){?>selected<?php  } ?>>Provider ( Provider will set their own price )</option>
                                              </select>
                                            </div>
                                        </div>
                                         <?php 
                                        if($count_all > 0) {
                                          for($i=0;$i<$count_all;$i++) {
                                            $vCode = $db_master[$i]['vCode'];
                                            $vTitle = $db_master[$i]['vTitle'];
                                            $eDefault = $db_master[$i]['eDefault'];

                                            $vValue = 'vCategory_'.$vCode;
                                            $vValue_desc = 'tCategoryDesc_'.$vCode;
                                            $required = ($eDefault == 'Yes')?'required':'';
                                            $required_msg = ($eDefault == 'Yes')?'<span class="red"> *</span>':'';
                                          ?>
                                                <div class="row">
                                                   <div class="col-lg-12">
                                                   <label>Category (<?=$vTitle;?>) <?php  echo $required_msg;?></label>
                                                       
                                                   </div>
                                                   <div class="col-lg-6">
                                                   <input type="text" class="form-control" name="<?=$vValue;?>" id="<?=$vValue;?>" value="<?=$$vValue;?>" placeholder="<?=$vTitle;?>Value" <?=$required;?>>
                                                       
                                                   </div>
                                                </div>
                                                <?php   if($sub_action == "sub_category"){ ?>
                                                <div class="row">
                                                   <div class="col-lg-12">
                                                   <label>Category Description (<?=$vTitle;?>) </label>
                                                       
                                                   </div>
                                                   <div class="col-lg-6">
                                                    <textarea class="form-control" name="<?=$vValue_desc;?>" id="<?=$vValue_desc;?>" placeholder="<?=$vTitle;?>Value"><?=$$vValue_desc;?></textarea>                                              
                                                   </div>
                                                </div>

                                               <?php  }
                                             }
                                        } ?>

                                          <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Logo</label>
                                             </div>
                                             <div class="col-lg-6">
                                                <?php  if($vLogo != '') { ?>                                               
                                                <img src="<?=$tconfig['tsite_upload_images_vehicle_category']."/".$id."/ios/3x_".$vLogo;?>" style="width:100px;height:100px;">

                                                <?php }?>
                                                  <input type="file" class="form-control" name="vLogo" <?php  echo $required_rule; ?> id="vLogo" placeholder="" style="padding-bottom: 39px;">
                                                  <br/>
                                                  [Note: Upload only png image size of 360px*360px.]
                                             </div>
                                        </div> 
                                     <?php   if($sub_action == "sub_category"){  ?>            
                                      <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Allow Before Photo Upload For Job</label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select  class="form-control" name = 'eBeforeUpload'  id= 'eBeforeUpload' required>                                   
                                                       <option value="No"<?php if('No' == $db_data[0]['eBeforeUpload']){?>selected<?php  } ?>>No</option>                           
                                                       <option value="Yes" <?php if('Yes' == $db_data[0]['eBeforeUpload']){?>selected<?php  } ?>>Yes</option>
                                                       </option>                                                    
                                                  </select>
                                             </div>
                                        </div> 
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Allow After Photo Upload For Job</label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select  class="form-control" name = 'eAfterUpload'  id= 'eAfterUpload' required>                                   
                                                       <option value="No"<?php if('No' == $db_data[0]['eAfterUpload']){?>selected<?php  } ?>>No</option>                     
                                                       <option value="Yes" <?php if('Yes' == $db_data[0]['eAfterUpload']){?>selected<?php  } ?>>Yes</option>
                                                       </option>                                                    
                                                  </select>
                                             </div>
                                        </div> 
                                        <?php  } ?>
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
                                                  <input type="submit" class="save btn-info" name="btnsubmit" id="btnsubmit" value="Update" >
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
          <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
          <script type="text/javascript" src="js/moment.min.js"></script>              
          <script>
          $(document).ready(function() {
            var referrer;
            if($("#previousLink").val() == "" ){
              referrer =  document.referrer;  
              //alert(referrer);
            }else { 
              referrer = $("#previousLink").val();
            }
            if(referrer == "") {
              referrer = "vehicles.php";
            }else {
              $("#backlink").val(referrer);
            }
            $(".back_link").attr('href',referrer);
          });
            $(function() {
              var value = $("#vCategory").val();
              if (value > 0) {
                  $(".epricetype").hide();
              } else {
                  $(".epricetype").show();
              }
            });
          </script>
     </body>
     <!-- END BODY-->
</html>