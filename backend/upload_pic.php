<?php 

//echo "<pre>";print_r($_REQUEST);exit;
include_once('common.php');

require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();

$iMemberId = isset($_SESSION['sess_iUserId']) ? $_SESSION['sess_iUserId'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

// if(SITE_TYPE=='Demo')
// {
	// header("location:profile_rider.php?success=2");
	// exit;
// }

if ($action == 'photo') {
     if (isset($_POST['img_path'])) {
          $img_path = $_POST['img_path'];
     }
     $temp_gallery = $img_path . '/';
     $image_object = $_FILES['photo']['tmp_name'];
     $image_name = $_FILES['photo']['name'];
     if( empty($image_name)) {
        $image_name = $_POST['driver_doc_hidden']; 
     }

     if ($image_name == "") {
        $var_msg = $langage_lbl['LBL_UPLOAD_IMG_ERROR'];
        header("location:profile_rider.php?success=0&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        exit;
     }
     
     if ($image_name != "") {
          $check_file_query = "select iUserId,vImgName from register_user where iUserId=" . $iMemberId;
          $check_file = $obj->sql_query($check_file_query);
          $check_file['vImgName'] = $img_path . '/' . $_SESSION['sess_iUserId'] . '/' . $check_file[0]['vImgName'];

          if ($check_file['vImgName'] != '' && file_exists($check_file['vImgName'])) {
               unlink($img_path . '/' . $_SESSION['sess_iUserId'] . '/' . $check_file[0]['vImgName']);
               unlink($img_path . '/' . $_SESSION['sess_iUserId'] . '/1_' . $check_file[0]['vImgName']);
               unlink($img_path . '/' . $_SESSION['sess_iUserId'] . '/2_' . $check_file[0]['vImgName']);
               unlink($img_path . '/' . $_SESSION['sess_iUserId'] . '/3_' . $check_file[0]['vImgName']);
          }
          $filecheck = basename($_FILES['photo']['name']);
          $fileextarr = explode(".", $filecheck);
          $ext = strtolower($fileextarr[count($fileextarr) - 1]);
          $flag_error = 0;
          if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp") {
               $flag_error = 1;
               $var_msg = $langage_lbl['LBL_UPLOAD_IMG_ERROR'];
          }
         /* if ($_FILES['photo']['size'] > 1048576) {
               $flag_error = 1;
               $var_msg = "Image Size is too Large";
          }*/
          if ($flag_error == 1) {
               $generalobj->getPostForm($_POST, $var_msg, "profile_rider.php?success=0&var_msg=" . $var_msg);
               exit;
          } else {
               
               $Photo_Gallery_folder = $img_path . '/' . $iMemberId . '/';
               
               if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
               }

               $img1 = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, '','','', '', '', '', 'Y', '', $Photo_Gallery_folder);
			   
				if($img1!=''){
				if(is_file($Photo_Gallery_folder.$img1))
				{
				   include_once(TPATH_CLASS."/SimpleImage.class.php");
				   $img = new SimpleImage();
				   list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$img1);
				   if($width < $height){
					  $final_width = $width;
				   }else{
					  $final_width = $height;
				   }
				   $img->load($Photo_Gallery_folder.$img1)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$img1);
				   $img1 = $generalobj->img_data_upload($Photo_Gallery_folder,$img1,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],"");
				}
				}
               $vImgName = $img1;
               $var_msg = $langage_lbl['LBL_PROFILE_IMAGE_UPLOADED_MSG'];
             
             
                    $tbl = 'register_user';
                    $where = " WHERE `iUserId` = '" . $iMemberId . "'";
               $q = "UPDATE ";


               $query = $q . " `" . $tbl . "` SET 	
	   `vImgName` = '" . $vImgName . "'
	   " . $where;
               $obj->sql_query($query);
               header("location:profile_rider.php?success=1&var_msg=" . $var_msg);
          }
     } /*else {
          header("location:profile_rider.php");
     }*/
}
?>