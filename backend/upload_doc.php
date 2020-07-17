<?php 
include_once('common.php');
require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();

$iMemberId = $_SESSION['sess_iCompanyId'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
// if(SITE_TYPE=='Demo')
// {
// header("location:profile.php?success=2");
// exit;
// }

if ($action == 'noc') {
    if (isset($_POST['doc_path'])) {
        $doc_path = $_POST['doc_path'];
    }
    $temp_gallery = $doc_path . '/';
    $image_object = $_FILES['noc']['tmp_name'];
    $image_name = $_FILES['noc']['name'];

    if ($image_name == "") {
        $var_msg = $langage_lbl['LBL_DOC_UPLOAD_ERROR_'];
        header("location:profile.php?success=0&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        //$generalobjAdmin->getPostForm($_POST, $var_msg, "company_document_action.php?success=0&id=".$_REQUEST['id']."&var_msg=".$var_msg);
        exit;
    }

    if ($image_name != "") {
        if ($_SESSION['sess_user'] == 'driver') {
            $check_file_query = "select iDriverId,vNoc from register_driver where iDriverId=" . $_SESSION['sess_iUserId'];
        } else {
            $check_file_query = "select iCompanyId,vNoc from company where iCompanyId=" . $iMemberId;
        }
        $check_file = $obj->sql_query($check_file_query);
        $check_file['vNoc'] = $doc_path . '/' . $_SESSION['sess_iUserId'] . '/' . $check_file[0]['vNoc'];

        /*  if ($check_file['vNoc'] != '' && file_exists($check_file['vNoc'])) {
          unlink($doc_path . '/' . $_SESSION['sess_iUserId'] . '/' . $check_file[0]['vNoc']);
          unlink($doc_path . '/' . $_SESSION['sess_iUserId'] . '/1_' . $check_file[0]['vNoc']);
          unlink($doc_path . '/' . $_SESSION['sess_iUserId'] . '/2_' . $check_file[0]['vNoc']);
          } */

        $filecheck = basename($_FILES['noc']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp" && $ext != "doc" && $ext != "docx" && $ext != "pdf") {
            $flag_error = 1;
            $var_msg = $langage_lbl['LBL_IMAGE_FORMAT_ERROR_MSG'];
        }

        if ($flag_error == 1) {
            $generalobj->getPostForm($_POST, $var_msg, "profile.php?success=0&var_msg=" . $var_msg);
            exit;
        } else {
            if ($_SESSION['sess_user'] == 'company') {
                $Photo_Gallery_folder = $doc_path . '/' . $iMemberId . '/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                }
                $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
                // $img = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_documnet_size1"], $tconfig["tsite_upload_documnet_size2"], '', '', '', '', 'Y', '', $Photo_Gallery_folder);
                $vImage = $vFile[0];
                $var_msg = $langage_lbl['LBL_NOC_UPLOADED'];
                $tbl = 'company';
                $sql = "SELECT * FROM " . $tbl . " WHERE iCompanyId = '" . $iMemberId . "'";
                $db_data = $obj->MySQLSelect($sql);
                $q = "INSERT INTO ";
                $where = '';

                if (count($db_data) > 0) {
                    $q = "UPDATE ";
                    $where = " WHERE `iCompanyId` = '" . $iMemberId . "'";
                }

                /* $query = $q . " `" . $tbl . "` SET 	
                  `vNoc` = '" . $vImage . "',
                  `iCompanyId` = '" . $iMemberId . "',`eStatus`='Active' " . $where;
                  $obj->sql_query($query); */

                $query = $q . " `" . $tbl . "` SET   
                  `vNoc` = '" . $vImage . "',
                  `iCompanyId` = '" . $iMemberId . "'" . $where;
                $obj->sql_query($query);



                //Start :: Log Data Save
                if (empty($check_file[0]['vNoc'])) {
                    $vNocPath = $vImage;
                } else {
                    $vNocPath = $check_file[0]['vNoc'];
                }
                $generalobj->save_log_data($iMemberId, '0', 'company', 'noc', $vNocPath);
                //End :: Log Data Save
                // Start :: Status in edit a Document upload time
                // $set_value = "`eStatus` ='Active'";
                // $generalobj->estatus_change('company','iCompanyId',$iMemberId,$set_value);
                // End :: Status in edit a Document upload time     
                check_email_send($_SESSION['sess_iUserId'], 'company', 'iCompanyId');
                header("location:profile.php?success=1&var_msg=" . $var_msg);
                exit;
            }
            if ($_SESSION['sess_user'] == 'driver') {
                $Photo_Gallery_folder = $doc_path . '/' . $_SESSION['sess_iUserId'] . '/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                }

                //$img = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_documnet_size1"], $tconfig["tsite_upload_documnet_size2"], '', '', '', '', 'Y', '', $Photo_Gallery_folder);
                //die($Photo_Gallery_folder);

                $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
                $vImage = $vFile[0];
                $var_msg = $langage_lbl['LBL_NOC_UPLOADED'];
                $tbl = 'register_driver';
                $sql = "SELECT * FROM " . $tbl . " WHERE iDriverId = '" . $_SESSION['sess_iUserId'] . "'";
                $db_data = $obj->MySQLSelect($sql);
                $q = "INSERT INTO ";
                $where = '';

                if (count($db_data) > 0) {
                    $q = "UPDATE ";
                    $where = " WHERE `iDriverId` = '" . $_SESSION['sess_iUserId'] . "'";
                }
                /*  $query = $q . " `" . $tbl . "` SET `vNoc` = '" . $vImage . "',`eStatus`='active'" . $where ;
                  $obj->sql_query($query); */

                $query = $q . " `" . $tbl . "` SET `vNoc` = '" . $vImage . "'" . $where;
                $obj->sql_query($query);


                //Start :: Log Data Save
                if (empty($check_file[0]['vNoc'])) {
                    $vNocPath = $vImage;
                } else {
                    $vNocPath = $check_file[0]['vNoc'];
                }
                $generalobj->save_log_data('0', $_SESSION['sess_iUserId'], 'driver', 'noc', $vNocPath);
                //End :: Log Data Save
                // Start :: Status in edit a Document upload time
                // $set_value = "`eStatus` ='active'";
                // $generalobj->estatus_change('register_driver','iDriverId',$_SESSION['sess_iUserId'],$set_value);
                // End :: Status in edit a Document upload time     
                check_email_send($_SESSION['sess_iUserId'], 'register_driver', 'iDriverId');
                header("location:profile.php?success=1&var_msg=" . $var_msg);
                exit;
            }
        }
    } /* else {
      $var_msg = "NOC File uploaded successfully";
      header("location:profile.php?success=1&var_msg=" . $var_msg);
      } */
}
if ($action == 'certi') {
    if (isset($_POST['doc_path'])) {
        $doc_path = $_POST['doc_path'];
    }
    $temp_gallery = $doc_path . '/';
    $image_object = $_FILES['certi']['tmp_name'];
    $image_name = $_FILES['certi']['name'];


    if ($image_name == "") {
        $var_msg = $langage_lbl['LBL_DOC_UPLOAD_ERROR_'];
        header("location:profile.php?success=0&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        //$generalobjAdmin->getPostForm($_POST, $var_msg, "company_document_action.php?success=0&id=".$_REQUEST['id']."&var_msg=".$var_msg);

        exit;
    }

    if ($image_name != "") {
        if ($_SESSION['sess_user'] == 'driver') {
            $check_file_query = "select iDriverId,vCerti from register_driver where iDriverId=" . $_SESSION['sess_iUserId'];
        } else {
            $check_file_query = "select iCompanyId,vCerti from company where iCompanyId=" . $iMemberId;
        }
        $check_file = $obj->sql_query($check_file_query);
        $check_file['vCerti'] = $doc_path . '/' . $_SESSION['sess_iUserId'] . '/' . $check_file[0]['vCerti'];

        /* if ($check_file['vCerti'] != '' && file_exists($check_file['vCerti'])) {
          unlink($doc_path . '/' . $_SESSION['sess_iUserId'] . '/' . $check_file[0]['vCerti']);
          unlink($doc_path . '/' . $_SESSION['sess_iUserId'] . '/1_' . $check_file[0]['vCerti']);
          unlink($doc_path . '/' . $_SESSION['sess_iUserId'] . '/2_' . $check_file[0]['vCerti']);
          } */

        $filecheck = basename($_FILES['certi']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        //  if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp") {
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp" && $ext != "doc" && $ext != "docx" && $ext != "pdf") {
            $flag_error = 1;
            //$var_msg = "You have selected wrong file format for Image. Valid formats are jpg,jpeg,gif,png";
            $var_msg = $langage_lbl['LBL_IMAGE_FORMAT_ERROR_MSG'];
        }
        /* else if ($_FILES['certi']['size'] > 1048000) {
          $flag_error = 1;
          $var_msg = "Image Size is too Large";
          } */
        if ($flag_error == 1) {
            $generalobj->getPostForm($_POST, $var_msg, "profile.php?success=0&var_msg=" . $var_msg);
            exit;
        } else {
            if ($_SESSION['sess_user'] == 'company') {
                $Photo_Gallery_folder = $doc_path . '/' . $iMemberId . '/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                }
                //$img = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_documnet_size1"], $tconfig["tsite_upload_documnet_size2"], '', '', '', '', 'Y', '', $Photo_Gallery_folder);
                $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
                $vImage = $vFile[0];
                $var_msg = $langage_lbl['LBL_CERTIFICATION_UPLOADED'];
                $tbl = 'company';
                $sql = "SELECT * FROM " . $tbl . " WHERE iCompanyId = '" . $iMemberId . "'";
                $db_data = $obj->MySQLSelect($sql);
                $q = "INSERT INTO ";
                $where = '';

                if (count($db_data) > 0) {
                    $q = "UPDATE ";
                    $where = " WHERE `iCompanyId` = '" . $iMemberId . "'";
                }
                $query = $q . " `" . $tbl . "` SET 	
	   `vCerti` = '" . $vImage . "',
	   `iCompanyId` = '" . $iMemberId . "'" . $where;
                $obj->sql_query($query);

                //Start :: Log Data Save
                if (empty($check_file[0]['vCerti'])) {
                    $vCertiPath = $vImage;
                } else {
                    $vCertiPath = $check_file[0]['vCerti'];
                }
                $generalobj->save_log_data($iMemberId, '0', 'company', 'certificate', $vCertiPath);
                //End :: Log Data Save
                // Start :: Status in edit a Document upload time
                // $set_value = "`eStatus` ='Active'";
                //  $generalobj->estatus_change('company','iCompanyId',$iMemberId,$set_value);
                // End :: Status in edit a Document upload time
                check_email_send($_SESSION['sess_iUserId'], 'company', 'iCompanyId');
                header("location:profile.php?success=1&var_msg=" . $var_msg);
                exit;
            }
            if ($_SESSION['sess_user'] == 'driver') {
                $Photo_Gallery_folder = $doc_path . '/' . $_SESSION['sess_iUserId'] . '/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                }
                //$img = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_documnet_size1"], $tconfig["tsite_upload_documnet_size2"], '', '', '', '', 'Y', '', $Photo_Gallery_folder);
                $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
                $vImage = $vFile[0];
                $var_msg = $langage_lbl['LBL_CERTIFICATION_UPLOADED'];
                $tbl = 'register_driver';
                $sql = "SELECT * FROM " . $tbl . " WHERE iDriverId = '" . $_SESSION['sess_iUserId'] . "'";
                $db_data = $obj->MySQLSelect($sql);
                $q = "INSERT INTO ";
                $where = '';

                if (count($db_data) > 0) {
                    $q = "UPDATE ";
                    $where = " WHERE `iDriverId` = '" . $_SESSION['sess_iUserId'] . "'";
                }
                $query = $q . " `" . $tbl . "` SET 	
                              `vCerti` = '" . $vImage . "',
                              `iDriverId` = '" . $_SESSION['sess_iUserId'] . "'" . $where;
                $obj->sql_query($query);

                //Start :: Log Data Save
                if (empty($check_file[0]['vCerti'])) {
                    $vCertiPath = $vImage;
                } else {
                    $vCertiPath = $check_file[0]['vCerti'];
                }
                $generalobj->save_log_data('0', $_SESSION['sess_iUserId'], 'driver', 'certificate', $vCertiPath);
                //End :: Log Data Save
                // Start :: Status in edit a Document upload time
                //  $set_value = "`eStatus` ='active'";
                //$generalobj->estatus_change('register_driver','iDriverId',$_SESSION['sess_iUserId'],$set_value);
                // End :: Status in edit a Document upload time
                check_email_send($_SESSION['sess_iUserId'], 'register_driver', 'iDriverId');
                header("location:profile.php?success=1&var_msg=" . $var_msg);
                exit;
            }
        }
    } /* else {
      $var_msg = "Certificate File uploaded successfully";
      header("location:profile.php?success=1&var_msg=" . $var_msg);
      } */
}

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

    if ($image_name == "" || $image_name == "NONE") {
        $var_msg = $langage_lbl['LBL_DOC_UPLOAD_ERROR_'];
        header("location:profile.php?success=0&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        exit;
    }

    if ($image_name != "" || $image_name != "NONE") {

        if ($_SESSION['sess_user'] == 'driver') {
            $check_file_query = "select iDriverId,vImage from register_driver where iDriverId=" . $_SESSION['sess_iUserId'];
        } else if ($_SESSION['sess_user'] == 'company') {
            $check_file_query = "select iCompanyId,vImage from company where iCompanyId=" . $_SESSION['sess_iUserId'];
        }
        $check_file = $obj->sql_query($check_file_query);
        $check_file['vImage'] = $img_path . '/' . $_SESSION['sess_iUserId'] . '/' . $check_file[0]['vImage'];

        if ($check_file['vImage'] != '' && file_exists($check_file['vImage'])) {
            unlink($img_path . '/' . $_SESSION['sess_iUserId'] . '/' . $check_file[0]['vImage']);
            unlink($img_path . '/' . $_SESSION['sess_iUserId'] . '/1_' . $check_file[0]['vImage']);
            unlink($img_path . '/' . $_SESSION['sess_iUserId'] . '/2_' . $check_file[0]['vImage']);
            unlink($img_path . '/' . $_SESSION['sess_iUserId'] . '/3_' . $check_file[0]['vImage']);
        }
        $filecheck = basename($_FILES['photo']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp") {
            // if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp"  && $ext != "doc"  && $ext != "docx" && $ext != "pdf") {
            $flag_error = 1;
            $var_msg = $langage_lbl['LBL_UPLOAD_IMG_ERROR'];
        }
        /* else if ($_FILES['photo']['size'] > 1048000) {
          $flag_error = 1;
          $var_msg = "Image Size is too Large";
          } */
        if ($flag_error == 1) {
            $generalobj->getPostForm($_POST, $var_msg, "profile.php?success=0&var_msg=" . $var_msg);
            exit;
        } else {
            if ($_SESSION['sess_user'] == 'driver') {
                $Photo_Gallery_folder = $img_path . '/' . $_SESSION['sess_iUserId'] . '/';
            }
            if ($_SESSION['sess_user'] == 'company') {
                $Photo_Gallery_folder = $img_path . '/' . $_SESSION['sess_iUserId'] . '/';
            }
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            //echo  $Photo_Gallery_folder;exit;
            /* $img1 = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"], '', '', '', 'Y', '', $Photo_Gallery_folder);

              $vImage = $img; */
            $img1 = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, '', '', '', '', '', '', 'Y', '', $Photo_Gallery_folder);
            if ($img1 != '') {
                if (is_file($Photo_Gallery_folder . $img1)) {
                    include_once(TPATH_CLASS . "/SimpleImage.class.php");
                    $img = new SimpleImage();
                    list($width, $height, $type, $attr) = getimagesize($Photo_Gallery_folder . $img1);
                    if ($width < $height) {
                        $final_width = $width;
                    } else {
                        $final_width = $height;
                    }
                    $img->load($Photo_Gallery_folder . $img1)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder . $img1);

                    $img1 = $generalobj->img_data_upload($Photo_Gallery_folder, $img1, $Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"], "");
                }
            }
            $vImage = $img1;
            $var_msg = "Profile image uploaded successfully";
            if ($_SESSION['sess_user'] == 'driver') {
                $tbl = 'register_driver';
                $where = " WHERE `iDriverId` = '" . $_SESSION['sess_iUserId'] . "'";
            }
            if ($_SESSION['sess_user'] == 'company') {
                $tbl = 'company';
                $where = " WHERE `iCompanyId` = '" . $_SESSION['sess_iUserId'] . "'";
            }

            $q = "UPDATE ";


            $query = $q . " `" . $tbl . "` SET 	
	   `vImage` = '" . $vImage . "'
	   " . $where;
            $obj->sql_query($query);
            header("location:profile.php?success=1&var_msg=" . $var_msg);
            exit;
        }
    } /* else {
      header("location:profile.php");
      } */
}

if ($action == 'masterbase') {
    if (isset($_POST['doc_path'])) {
        $doc_path = $_POST['doc_path'];
         $rowid=$_POST['type'];
        //echo $rowid = isset($_POST['rowid']) ? $_POST['rowid'] : '';
        $expDate = isset($_REQUEST['dLicenceExp']) ? $_REQUEST['dLicenceExp'] : '';
        // $expDate=$_POST['dLicenceExp'];
    }
    $temp_gallery = $doc_path . '/';
    $image_object = $_FILES['licence']['tmp_name'];
    $image_name = $_FILES['licence']['name'];


    // $sql = "select * from register_driver where iDriverId = '" . $_SESSION['sess_iUserId'] . "'";
//    echo  $sql = "select  dm.`doc_masterid`, dm.`doc_usertype`, dm.`doc_name`, dm.`ex_status`, dl.`doc_id`, dl.`doc_masterid`, dl.`doc_usertype`, dl.`doc_userid`, dl.`ex_date`, dl.`doc_file`,rd.`iDriverId` 
//     from document_master as dm
//left join document_list  as dl on dl.doc_masterid= dm.doc_masterid
//left join  register_driver as rd on  dl.doc_userid= rd.iDriverId
//where iDriverId='" . $_SESSION['sess_iUserId'] . "' and dm.doc_masterid='" . $rowid . "' ";
$sql="SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, dl.doc_masterid masterid_list ,dl.ex_date,dl.doc_file , dl.status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" . $_SESSION['sess_iUserId'] . "'  ) dl on dl.doc_masterid=dm.doc_masterid  
where dm.doc_usertype='driver' and dm.status='Active' and dm.doc_masterid='" . $rowid . "' ";

    $db_user = $obj->MySQLSelect($sql);
    //print_r($db_user); exit();
    // echo strtotime(date("Y-m-d"));echo "<br>";
    // echo strtotime($_POST['dLicenceExp']);exit;
    if ($db_user[0]['ex_status'] == 'yes') {
        if (strtotime(date("Y-m-d")) > strtotime($_POST['dLicenceExp'])) {
            $var_msg = "Expired Date is Lower Then Current Date";
            header("location:profile.php?success=0&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
            exit;
        }
        $curdate = date("Y-m-d");
        $input_date = $db_user[0]['ex_date'];
    }


//echo $input_date;exit;
    $in_date = $_POST['dLicenceExp'];
//echo $in_date;exit;
    $cur_date = explode("-", $curdate);
    $inp_date = explode("/", $in_date);

    $cur_year = $cur_date[0];
    $cur_month = $cur_date[1];
    $cur_date = $cur_date[2];

    $inp_year = $inp_date[0];
    $inp_mon = $inp_date[1];
    $inp_date = $inp_date[2];


//echo $input_date, $curdate;exit;
//if($inp_year < $cur_year || $inp_mon < $cur_month || $inp_date < $curdate){
    //echo "demo for date validation";exit;
    // $var_msg="Expired Date is Lower Then Current Date";
    // header("location:profile.php?success=0&id=".$_REQUEST['id']."&var_msg=" . $var_msg);
    // exit;
//}
    if ($db_user[0]['ex_status'] == 'yes') {
        $tbl = 'document_list';
        $q = "UPDATE ";
        $where = " WHERE `doc_userid` = '" . $_SESSION['sess_iUserId'] . "' and doc_masterid='" . $rowid . "' ";

         $query = $q . " `" . $tbl . "` SET  
  `ex_date` = '" . $_POST['dLicenceExp'] . "'" . $where;
        $obj->sql_query($query);
        $var_msg = "Expire date Updated";
      header("location:profile.php?success=1&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        exit;
    } else if ($image_name == "") {
        $var_msg =$langage_lbl['LBL_DOC_UPLOAD_ERROR_'];
        header("location:profile.php?success=0&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        //$generalobjAdmin->getPostForm($_POST, $var_msg, "company_document_action.php?success=0&id=".$_REQUEST['id']."&var_msg=".$var_msg);

        exit;
    }
    
   // print_r($_SESSION);
    if ($image_name != "") {
        // $check_file_query = "select iCompanyId,vLicence from company where iCompanyId=" . $iMemberId;
       //.m  $check_file_query = "select iDriverId,vLicence from register_driver where iDriverId=" . $_SESSION['sess_iUserId'];
      //.m  $check_file = $obj->sql_query($check_file_query);
//      .m  $check_file['vLicence'] = $doc_path . '/' . $_SESSION['sess_iUserId'] . '/' . $check_file[0]['vLicence'];

         $check_file['doc_file'] = $doc_path . '/' . $_SESSION['sess_iUserId'] . '/' . $check_file[0]['doc_fiel'];
  
        /*  if ($check_file['vLicence'] != '' && file_exists($check_file['vLicence'])) {
          unlink($doc_path . '/' . $_SESSION['sess_iUserId'] . '/' . $check_file[0]['vLicence']);
          unlink($doc_path . '/' . $_SESSION['sess_iUserId'] . '/1_' . $check_file[0]['vLicence']);
          unlink($doc_path . '/' . $_SESSION['sess_iUserId'] . '/2_' . $check_file[0]['vLicence']);
          } */

        $filecheck = basename($_FILES['licence']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;

        // if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp") {
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp" && $ext != "doc" && $ext != "docx" && $ext != "pdf") {
            $flag_error = 1;
            $var_msg = $langage_lbl['LBL_IMAGE_FORMAT_ERROR_MSG'];
        }
        /* else if ($_FILES['licence']['size'] > 1048000) {
          $flag_error = 1;
          $var_msg = "Image Size is too Large";
          } */
        if ($flag_error == 1) {
            $generalobj->getPostForm($_POST, $var_msg, "profile.php?success=0&var_msg=" . $var_msg);
            exit;
        } else {
            $Photo_Gallery_folder = $doc_path . '/' . $_SESSION['sess_iUserId'] . '/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }

            //$img = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_documnet_size1"], $tconfig["tsite_upload_documnet_size2"], '', '', '', '', 'Y', '', $Photo_Gallery_folder);
            $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
            $vImage = $vFile[0];
            //$vImage = $img;
            $var_msg = $langage_lbl['LBL_LICENCE_UPLOADED'];
//      m.      $tbl = 'document_list';
//            $sql = "SELECT * FROM " . $tbl . " WHERE  = '" . $_SESSION['sess_iUserId'] . "'";
//            $db_data = $obj->MySQLSelect($sql);
//            $q = "INSERT INTO ";
//            $where = '';
//
//            if (count($db_data) > 0) {
//                $q = "UPDATE ";
//                 $where = " WHERE `doc_userid` = '" . $_SESSION['sess_iUserId ']. "' and doc_masterid='" . $rowid . "' ";
//            }
//            $query = $q . " `" . $tbl . "` SET 	
//	`doc_file` = '" . $vImage . "',
//	`ex_date` = '" . $_POST['dLicenceExp'] . "'" . $where;
//            $obj->sql_query($query);

            //master base //
            
             $tbl = 'document_list';
      
            if ($db_user[0]['doc_file'] != "") {
            $query="UPDATE `".$tbl."` SET `doc_file`='".$vImage."' , `ex_date`='".$_POST['dLicenceExp']."' WHERE doc_userid='".$_SESSION['sess_iUserId']."' and doc_usertype='driver'  and doc_masterid=".$rowid;
               
            }
            else {
             $query =" INSERT INTO `".$tbl."` ( `doc_masterid`, `doc_usertype`, `doc_userid`, `ex_date`, `doc_file`, `status`, `edate`) "
                   . "VALUES "
            . "( '".$rowid."', 'driver', '".$_SESSION['sess_iUserId']."', '".$_POST['dLicenceExp']."', '".$vImage."', 'Inactive', CURRENT_TIMESTAMP)";
            }
          //  ECHO $query = $q . " `" . $tbl . "` SET `vNoc` = '" . $vImage . "'" . $where;
            $obj->sql_query($query);
            

            //////////////////////////
            //////////////////////////
            //Start :: Log Data Save
            if (empty($db_user[0]['doc_file'])) {
                $vLicencePath = $vImage;
            } else {
                $vCertiPath = $db_user[0]['doc_file'];
            }
            $generalobj->save_log_data('0',$_SESSION['sess_iUserId'], 'driver', '".$db_user[0][doc_name]."', $vLicencePath);
            //End :: Log Data Save
            // Start :: Status in edit a Document upload time
            //$set_value = "`eStatus` ='active'";
            // $generalobj->estatus_change('register_driver','iDriverId',$_SESSION['sess_iUserId'],$set_value);
            // End :: Status in edit a Document upload time

            check_email_send($_SESSION['sess_iUserId'], 'register_driver', 'iDriverId');
            header("location:profile.php?success=1&var_msg=" . $var_msg);
            exit;
        }
    } /* else {
      $sql = "UPDATE register_driver SET `dLicenceExp` = '".$_POST['dLicenceExp']."' WHERE `iDriverId` = '" . $_SESSION['sess_iUserId'] . "'";
      $obj->sql_query($sql);
      $var_msg = "Licence uploaded successfully";
      header("location:profile.php?success=1&var_msg=" . $var_msg);
      } */
}

function check_email_send($iDriverId, $tablename, $field) {
    global $obj, $generalobj;
    $sql = "SELECT * FROM " . $tablename . " WHERE " . $field . "= '" . $iDriverId . "'";
    $db_data = $obj->MySQLSelect($sql);
    //print_r($db_data);
    //exit;
    //$valid=0;
    if ($tablename == 'register_driver') {
        //echo "hi";exit;
        if ($db_data[0]['vNoc'] != NULL && $db_data[0]['vLicence'] != NULL && $db_data[0]['vCerti'] != NULL) {
            //global $generalobj;
            $maildata['USER'] = "Driver";
            $maildata['NAME'] = $db_data[0]['vName'];
            $maildata['EMAIL'] = $db_data[0]['vEmail'];
            $generalobj->send_email_user("PROFILE_UPLOAD", $maildata);
            //header("location:profile.php?success=1&var_msg=" . $var_msg);
            //return;
        }
    } else {
        if ($db_data[0]['vNoc'] != NULL && $db_data[0]['vCerti'] != NULL) {
            $maildata['USER'] = "Company";
            $maildata['NAME'] = $db_data[0]['vCompany'];
            $maildata['EMAIL'] = $db_data[0]['vEmail'];
            //var_dump($maildata);
            //var_dump(($generalobj));
            $generalobj->send_email_user("PROFILE_UPLOAD", $maildata);
        }
    }

    return true;
}

?>