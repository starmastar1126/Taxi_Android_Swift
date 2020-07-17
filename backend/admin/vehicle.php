<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();

$abc = 'admin,driver,company';
$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$generalobj->setRole($abc, $url);
//$generalobj->cehckrole();
$success = isset($_GET['success']) ? $_GET['success'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : ''; // delete
$id = isset($_GET['id']) ? $_GET['id'] : ''; // delete
$error = isset($_GET['error']) ? $_GET['error'] : ''; // delete
$var_msg = isset($_GET['var_msg']) ? $_GET['var_msg'] : ''; // delete
$tbl_name = 'driver_vehicle';
//echo '<pre>'; print_r($_SESSION); exit;
if ($_SESSION['sess_user'] == 'driver') {
    $sql = "select iCompanyId from `register_driver` where iDriverId = '" . $_SESSION['sess_iUserId'] . "'";
    $db_usr = $obj->MySQLSelect($sql);
    $iCompanyId = $db_usr[0]['iCompanyId'];
    $sql = "SELECT * FROM " . $tbl_name . " where iCompanyId = '" . $iCompanyId . "' and iDriverId = '" . $_SESSION['sess_iUserId'] . "' and eStatus != 'Deleted' ORDER BY iDriverVehicleId";
    $db_driver_vehicle = $obj->MySQLSelect($sql);
}
if ($_SESSION['sess_user'] == 'company') {
    $iCompanyId = $_SESSION['sess_iCompanyId'];
    $sql = "SELECT * FROM " . $tbl_name . " where iCompanyId = '" . $iCompanyId . "' and eStatus != 'Deleted' ORDER BY iDriverVehicleId";
    $db_driver_vehicle = $obj->MySQLSelect($sql);
}

if ($action == 'delete') {
    // to check user is valid or not to delete vehicle
    $valid_user = false;
    foreach ($db_driver_vehicle as $val) {
        if ($val['iDriverVehicleId'] == $id)
            $valid_user = true;
    }
    if (!$valid_user)
        header("Location:vehicle.php?error=1&var_msg=You can not Delete this vehicle");
    else {
        $sql = "select count(*) as trip_cnt from trips where iDriverVehicleId = '" . $id . "' GROUP BY iDriverVehicleId";
        $db_usr = $obj->MySQLSelect($sql);

        if (count($db_usr) > 0 && $db_usr['trip_cnt'] > 0) {
            header("Location:vehicle.php?error=1&var_msg=Trips are available. You can not delete this vehicle");
            exit;
        } else {
            $query = "UPDATE `driver_vehicle` SET eStatus = 'Deleted' WHERE iDriverVehicleId = '" . $id . "'";
            $obj->sql_query($query);

            header("Location:vehicle.php?success=1&var_msg=Vehicle deleted successfully");
            exit;
        }
    }
}

for ($i = 0; $i < count($db_driver_vehicle); $i++) {
//echo "<br>id == ".$db_data[$i]['iDriverVehicleId'];
    $sql = "select vMake from make where iMakeId = '" . $db_driver_vehicle[$i]['iMakeId'] . "'";
    $name1 = $obj->MySQLSelect($sql);
    $sql = "select vTitle from model where iModelId = '" . $db_driver_vehicle[$i]['iModelId'] . "'";
    $name2 = $obj->MySQLSelect($sql);
    $db_msk[$i] = $name1[0]['vMake'] . ' ' . $name2[0]['vTitle'];
}

if (isset($_POST['Submit'])) {
//echo "here";exit;
//echo "<pre>";print_R($_FILES);exit;

    $iVehicleId = isset($_REQUEST['iVehicleId']) ? $_REQUEST['iVehicleId'] : '';
    $image_object = $_FILES['insurance']['tmp_name'];
    $image_name = $_FILES['insurance']['name'];

    $image_object1 = $_FILES['permit']['tmp_name'];
    $image_name1 = $_FILES['permit']['name'];

    $image_object2 = $_FILES['regi']['tmp_name'];
    $image_name2 = $_FILES['regi']['name'];

    if ($image_name != "") {
        $check_file_query = "select iDriverVehicleId,vInsurance from driver_vehicle where iDriverVehicleId=" . $iVehicleId;
        $check_file = $obj->sql_query($check_file_query);
        $check_file['vInsurance'] = $tconfig["tsite_upload_vehicle_doc"] . '/' . $check_file[0]['vInsurance'];

        /* if ($check_file['vInsurance'] != '' && file_exists($check_file['vInsurance'])) {
          unlink($tconfig["tsite_upload_vehicle_doc"] . '/' . '/' . $check_file[0]['vInsurance']);
          unlink($tconfig["tsite_upload_vehicle_doc"] . '/' . '/1_' . $check_file[0]['vInsurance']);
          unlink($tconfig["tsite_upload_vehicle_doc"] . '/' . '/2_' . $check_file[0]['vInsurance']);
          } */
        $temp_gallery = $tconfig["tsite_upload_vehicle_doc"] . '/';
        $filecheck = basename($_FILES['insurance']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp" && $ext != "pdf" && $ext != "doc" && $ext != "docx") {
            $flag_error = 1;
            $var_msg = "You have selected wrong file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png";
        }
        /* if ($_FILES['insurance']['size'] > 1048576) {
          $flag_error = 1;
          $var_msg = "Image Size is too Large";
          } */

        if ($flag_error == 1) {
            $generalobj->getPostForm($_POST, $var_msg, $tconfig['tsite_url'] . "vehicle.php?success=0");
            exit;
        } else {
            $Photo_Gallery_folder = $tconfig["tsite_upload_vehicle_doc"];
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            //$img1 = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,doc,txt,pdf');
            $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
            //$img = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_documnet_size1"], $tconfig["tsite_upload_documnet_size2"], '', '', '', '', 'Y', '', $Photo_Gallery_folder);
            $vImage1 = $vFile[0];
            if ($iVehicleId != '') {
                //	echo "<pre>";print_R($_REQUEST);exit;
                $q = "UPDATE ";
                $where = " WHERE `iDriverVehicleId` = '" . $iVehicleId . "'";
            }


            $query = $q . " `" . $tbl_name . "` SET `vInsurance` = '" . $vImage1 . "'" . $where;
            $obj->sql_query($query);

            //Start :: Log Data Save
            if ($_SESSION['sess_user'] == 'company') {
                if (empty($check_file[0]['vInsurance'])) {
                    $vNocPath = $vImage;
                } else {
                    $vNocPath = $check_file[0]['vInsurance'];
                }
                $generalobj->save_log_data($iCompanyId, $_SESSION["sess_iUserId"], 'company', 'insurance', $vNocPath);

                // Start :: Status in edit a Document upload time
                $set_value = "`eStatus` ='inactive'";
                $generalobj->estatus_change('company', 'iCompanyId', $iCompanyId, $set_value);
                // End :: Status in edit a Document upload time
            } else if ($_SESSION['sess_user'] == 'driver') {
                if (empty($check_file[0]['vInsurance'])) {
                    $vNocPath = $vImage;
                } else {
                    $vNocPath = $check_file[0]['vInsurance'];
                }
                $generalobj->save_log_data('0', $_SESSION["sess_iUserId"], 'driver', 'insurance', $vNocPath);

                // Start :: Status in edit a Document upload time
                $set_value = "`eStatus` ='inactive'";
                $generalobj->estatus_change('register_driver', 'iDriverId', $_SESSION["sess_iUserId"], $set_value);
                // End :: Status in edit a Document upload time
            }
            //End :: Log Data Save
        }
    }
    if ($image_name1 != "") {
        $check_file_query = "select iDriverVehicleId,vPermit from driver_vehicle where iDriverVehicleId=" . $iVehicleId;
        $check_file = $obj->sql_query($check_file_query);
        $check_file['vPermit'] = $tconfig["tsite_upload_vehicle_doc"] . '/' . $check_file[0]['vPermit'];

        /*  if ($check_file['vPermit'] != '' && file_exists($check_file['vPermit'])) {
          unlink($tconfig["tsite_upload_vehicle_doc"] . '/' . '/' . $check_file[0]['vPermit']);
          unlink($tconfig["tsite_upload_vehicle_doc"] . '/' . '/1_' . $check_file[0]['vPermit']);
          unlink($tconfig["tsite_upload_vehicle_doc"] . '/' . '/2_' . $check_file[0]['vPermit']);
          } */

        $temp_gallery = $tconfig["tsite_upload_vehicle_doc"] . '/';
        $filecheck = basename($_FILES['permit']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;

        /* if ($_FILES['permit']['size'] > 1048576) {
          $flag_error = 1;
          $var_msg = "Image Size is too Large";
          } */
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp" && $ext != "pdf" && $ext != "doc" && $ext != "docx") {
            $flag_error = 1;
            $var_msg = "You have selected wrong file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png";
        }
        if ($flag_error == 1) {
            $generalobj->getPostForm($_POST, $var_msg, $tconfig['tsite_url'] . "vehicle.php?success=0");
            exit;
        } else {
            $Photo_Gallery_folder = $tconfig["tsite_upload_vehicle_doc"];
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            //$img = $generalobj->general_upload_image($image_object1, $image_name1, $Photo_Gallery_folder, $tconfig["tsite_upload_documnet_size1"], $tconfig["tsite_upload_documnet_size2"], '', '', '', '', 'Y', '', $Photo_Gallery_folder);
            $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object1, $image_name1, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
            $vImage1 = $vFile[0];
            if ($iVehicleId != '') {
//	echo "<pre>";print_R($_REQUEST);exit;
                $q = "UPDATE ";
                $where = " WHERE `iDriverVehicleId` = '" . $iVehicleId . "'";
            }

            $query = $q . " `" . $tbl_name . "` SET `vPermit` = '" . $vImage1 . "'" . $where;
            $obj->sql_query($query);

            //Start :: Log Data Save
            if ($_SESSION['sess_user'] == 'company') {
                if (empty($check_file[0]['vPermit'])) {
                    $vNocPath = $vImage1;
                } else {
                    $vNocPath = $check_file[0]['vPermit'];
                }
                $generalobj->save_log_data($iCompanyId, $_SESSION["sess_iUserId"], 'company', 'permit', $vNocPath);

                // Start :: Status in edit a Document upload time
                $set_value = "`eStatus` ='inactive'";
                $generalobj->estatus_change('company', 'iCompanyId', $iCompanyId, $set_value);
                // End :: Status in edit a Document upload time
            } else if ($_SESSION['sess_user'] == 'driver') {
                if (empty($check_file[0]['vPermit'])) {
                    $vNocPath = $vImage;
                } else {
                    $vNocPath = $check_file[0]['vPermit'];
                }
                $generalobj->save_log_data('0', $_SESSION["sess_iUserId"], 'driver', 'permit', $vNocPath);

                // Start :: Status in edit a Document upload time
                $set_value = "`eStatus` ='inactive'";
                $generalobj->estatus_change('register_driver', 'iDriverId', $_SESSION["sess_iUserId"], $set_value);
                // End :: Status in edit a Document upload time
            }
            //End :: Log Data Save
        }
    }
    if ($image_name2 != "") {
        $check_file_query = "select iDriverVehicleId,vRegisteration from driver_vehicle where iDriverVehicleId=" . $iVehicleId;
        $check_file = $obj->sql_query($check_file_query);
        $check_file['vRegisteration'] = $tconfig["tsite_upload_vehicle_doc"] . '/' . $check_file[0]['vRegisteration'];

        /* if ($check_file['vRegisteration'] != '' && file_exists($check_file['vRegisteration'])) {
          unlink($tconfig["tsite_upload_vehicle_doc"] . '/' . '/' . $check_file[0]['vRegisteration']);
          unlink($tconfig["tsite_upload_vehicle_doc"] . '/' . '/1_' . $check_file[0]['vRegisteration']);
          unlink($tconfig["tsite_upload_vehicle_doc"] . '/' . '/2_' . $check_file[0]['vRegisteration']);
          } */

        $temp_gallery = $tconfig["tsite_upload_vehicle_doc"] . '/';
        $filecheck = basename($_FILES['regi']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;

        /* if ($_FILES['regi']['size'] > 1048576) {
          $flag_error = 1;
          $var_msg = "Image Size is too Large";
          } */
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp" && $ext != "pdf" && $ext != "doc" && $ext != "docx") {
            $flag_error = 1;
            $var_msg = "You have selected wrong file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png";
        }
        if ($flag_error == 1) {
            $generalobj->getPostForm($_POST, $var_msg, $tconfig['tsite_url'] . "vehicle.php?success=0");
            exit;
        } else {
            $Photo_Gallery_folder = $tconfig["tsite_upload_vehicle_doc"];
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            //$img = $generalobj->general_upload_image($image_object2, $image_name2, $Photo_Gallery_folder, $tconfig["tsite_upload_documnet_size1"], $tconfig["tsite_upload_documnet_size2"], '', '', '', '', 'Y', '', $Photo_Gallery_folder);
            $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object2, $image_name2, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
            $vImage2 = $vFile[0];
            if ($iVehicleId != '') {
//	echo "<pre>";print_R($_REQUEST);exit;
                $q = "UPDATE ";
                $where = " WHERE `iDriverVehicleId` = '" . $iVehicleId . "'";
            }

            $query = $q . " `" . $tbl_name . "` SET `vRegisteration` = '" . $vImage2 . "'" . $where;
            $obj->sql_query($query);

            //Start :: Log Data Save
            if ($_SESSION['sess_user'] == 'company') {
                if (empty($check_file[0]['vRegisteration'])) {
                    $vNocPath = $vImage2;
                } else {
                    $vNocPath = $check_file[0]['vRegisteration'];
                }
                $generalobj->save_log_data($iCompanyId, $_SESSION["sess_iUserId"], 'company', 'registeration', $vNocPath);

                // Start :: Status in edit a Document upload time
                $set_value = "`eStatus` ='inactive'";
                $generalobj->estatus_change('company', 'iCompanyId', $iCompanyId, $set_value);
                // End :: Status in edit a Document upload time
            } else if ($_SESSION['sess_user'] == 'driver') {
                if (empty($check_file[0]['vRegisteration'])) {
                    $vNocPath = $vImage;
                } else {
                    $vNocPath = $check_file[0]['vRegisteration'];
                }
                $generalobj->save_log_data('0', $_SESSION["sess_iUserId"], 'driver', 'registeration', $vNocPath);

                // Start :: Status in edit a Document upload time
                $set_value = "`eStatus` ='inactive'";
                $generalobj->estatus_change('register_driver', 'iDriverId', $_SESSION["sess_iUserId"], $set_value);
                // End :: Status in edit a Document upload time
            }
            //End :: Log Data Save
        }
    }
//echo $vImage." ".$vImage1." ".$vImage2;exit;
    header('location:vehicle.php');
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?= $SITE_NAME ?> | Vehicles</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link rel="stylesheet" href="assets/css/bootstrap-fileupload.min.css" />
        <?php  include_once('global_files.php');?>

        <script type="text/javascript">
            function confirm_delete(id)
            {
                var tsite_url = '<?php  echo $tconfig["tsite_url"]; ?>';
                if (id != '') {
                    var confirm_ans = confirm("Are You sure You want to Delete Vehicle?");
                    if (confirm_ans == true) {
                        window.location.href = "vehicle.php?action=delete&id=" + id;
                    }
                }
                //document.getElementById(id).submit();
            }
        </script>
        <style>
            .fileupload-preview  { line-height:150px;}
        </style>
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53 " >

        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php  include_once('header.php'); ?>
            <?php  include_once('left_menu.php'); ?>

            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2><?php  echo $langage_lbl_admin['LBL_VEHICLE_CAPITAL_TXT_ADMIN']; ?></h2>
                            <a href="vehicle_add_form.php">
                                <input type="button" value="Add <?php  echo $langage_lbl_admin['LBL_VEHICLE_CAPITAL_TXT_ADMIN']; ?>" class="add-btn">
                            </a>
                        </div>
                    </div>
                    <hr />
                    <div class="body-div">
                        <?php 
#echo '<pre>'; print_R($db_driver_vehicle); echo '</pre>';exit;
                        if ($error) {
                            ?>
                            <div class="row">
                                <div class="col-sm-12 alert alert-error">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?= $var_msg ?>
                                </div>
                            </div>						
                            <?php 
                        }
                        if ($success) {
                            ?>
                            <div class="row">
                                <div class="alert alert-success paddiing-10">
                                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                    <?= $var_msg ?>
                                </div>
                            </div>						
                            <?php 
                        }
                        if (count($db_driver_vehicle) > 0) {
                            for ($i = 0; $i < count($db_driver_vehicle); $i++) {
                                ?>
                                <form id="<?= $i ?>" method="post" action="" enctype="multipart/form-data">
                                    <input type="hidden" name="iVehicleId" value = "<?php  echo $db_driver_vehicle[$i]['iDriverVehicleId']; ?>"/>
                                    <div class="row">
                                        <div class="col-lg-12">

                                            <div class="panel panel-default notification-listing">
                                                <div class="panel-body notification-listing-inner">
                                                    <?php if($db_driver_vehicle[$i]['vInsurance'] == ''){ ?>
                                                    <div class="alert alert-warning alert-dismissable">
                                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Please Upload Insurance Documents <!-- <a href="#" class="alert-link">Alert Link</a> -->.
                                                    </div>
                                                    <?php  } ?>
                                                    <?php if($db_driver_vehicle[$i]['vPermit'] == ''){?>
                                                    <div class="alert alert-warning alert-dismissable">
                                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Please Upload Permit documents<!-- <a href="#" class="alert-link">Alert Link</a> -->.
                                                    </div>
                                                    <?php  } ?>
                                                    <?php if($db_driver_vehicle[$i]['vRegisteration'] == ''){?>
                                                    <div class="alert alert-warning alert-dismissable">
                                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>Please Upload Registeration Documents<!-- <a href="#" class="alert-link">Alert Link</a> -->.
                                                    </div>
                                                    <?php  } ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="box dark image-toggle-box">
                                        <header class="image-toggle">
                                            <div class="icons"><i class="icon-th-list"></i></div>
                                            <h5><?= $db_msk[$i] ?></span></h5>
                                            <div class="toolbar">
                                                <ul class="nav">
                                                    <li>
                                                        <a href ="vehicle_add_form.php?id=<?= $db_driver_vehicle[$i]['iDriverVehicleId'] ?>" class="btn btn-danger">
                                                            <i class="icon-pencil icon-white"></i>  Edit
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="btn btn-danger" onClick="confirm_delete('<?= $db_driver_vehicle[$i]['iDriverVehicleId'] ?>');">
                                                            <i class="icon-remove icon-white"></i> Delete
                                                        </a>
                                                    </li>     
                                                    <li>
                                                        <a class="accordion-toggle minimize-box" data-toggle="collapse" href="#div-<?= $i; ?>">
                                                            <i class="icon-chevron-up"></i>
                                                        </a>												
                                                    </li>
                                                </ul>
                                            </div>
                                        </header>
                                        <div id="div-<?= $i; ?>" class="tabing-inner-pageaccordion-body collapse <?= ($i == 0) ? 'in' : 'out'; ?> ">
                                            <div class="documents">
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <h3>DOCUMENTS</h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <div class="col-lg-12">
                                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                                    <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px; ">
                                                                        <?php  if ($db_driver_vehicle[$i]['vInsurance'] != '') { ?>
                                                                            <?php 
                                                                            $file_ext = $generalobj->file_ext($db_driver_vehicle[$i]['vInsurance']);
                                                                            if ($file_ext == 'is_image') {
                                                                                ?>
                                                                                <img src = "<?= $tconfig["tsite_upload_vehicle_doc_panel"] . '/' . $db_driver_vehicle[$i]['vInsurance'] ?>" style="width:200px;" alt ="Insurance Image"/>
                                                                            <?php  } else { ?>
                                                                                <a href="<?= $tconfig["tsite_upload_vehicle_doc_panel"] . '/' . $db_driver_vehicle[$i]['vInsurance'] ?>" target="_blank">Insurance Doc</a> 
                                                                            <?php  } ?>
                                                                        <?php  } else { ?> Insurance Image
        <?php  } ?>
                                                                    </div>
                                                                    <div>
                                                                        <span class="btn btn-file btn-success"><span class="fileupload-new">Upload Insurance</span><span class="fileupload-exists">Change</span><input type="file"  name="insurance" class="ins" /></span>
                                                                        <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <div class="col-lg-12">
                                                                <div class="fileupload fileupload-new" data-provides="fileupload">
                                                                    <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px; ">
                                                                        <?php  if ($db_driver_vehicle[$i]['vPermit'] != '') { ?>
                                                                            <?php 
                                                                            $file_ext = $generalobj->file_ext($db_driver_vehicle[$i]['vPermit']);
                                                                            if ($file_ext == 'is_image') {
                                                                                ?>
                                                                                <img src = "<?= $tconfig["tsite_upload_vehicle_doc_panel"] . '/' . $db_driver_vehicle[$i]['vPermit'] ?>" style="width:200px;" alt ="Insurance Image"/>
                                                                            <?php  } else { ?>
                                                                                <a href="<?= $tconfig["tsite_upload_vehicle_doc_panel"] . '/' . $db_driver_vehicle[$i]['vPermit'] ?>" target="_blank">Insurance Doc</a> 
            <?php  } ?>
                                                                            <?php  } else { ?> Permit
                                                                            <?php  } ?></div>
                                                                        <div>
                                                                            <span class="btn btn-file btn-success"><span class="fileupload-new">Upload Permit</span><span class="fileupload-exists">Change</span><input type="file" name="permit"/></span>
                                                                            <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <div class="col-lg-12">
                                                                    <div class="fileupload fileupload-new" data-provides="fileupload">
                                                                        <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px; ">
                                                                            <?php  if($db_driver_vehicle[$i]['vRegisteration'] != ''){ ?>
                                                                            <?php 
                                                                            $file_ext = $generalobj->file_ext($db_driver_vehicle[$i]['vRegisteration']);
                                                                            if ($file_ext == 'is_image') {
                                                                                ?>
                                                                                <img src = "<?= $tconfig["tsite_upload_vehicle_doc_panel"] . '/' . $db_driver_vehicle[$i]['vRegisteration'] ?>" style="width:200px;" alt ="Insurance Image"/>
            <?php  } else { ?>
                                                                                <a href="<?= $tconfig["tsite_upload_vehicle_doc_panel"] . '/' . $db_driver_vehicle[$i]['vRegisteration'] ?>" target="_blank">Insurance Doc</a> 
            <?php  } ?>          
                                                                            <?php  } else { ?> Registeration
                                                                            <?php  } ?>
                                                                        </div>
                                                                        <div>
                                                                            <span class="btn btn-file btn-success"><span class="fileupload-new">Upload Registration</span><span class="fileupload-exists">Change</span><input type="file" name = "regi"/></span>
                                                                            <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <input type="submit" name="Submit" class="btn btn-info" value = "Save Documents">
                                                        </div>
                                                    </div>
                                                    <br/><br/>
                                                </div>

                                            </div>

                                        </div>

                                    </form>

        <?php 
        }
    } else {
        ?>
                                <div class="row">
                                    <div class="col-sm-12">
                                        No records found.
                                    </div>
                                </div>
    <?php  } ?>						
                    </div>
                </div>

            </div> 
        </div>
        <!--END MAIN WRAPPER -->

        <?php  include_once('footer.php');?>

        <script src="assets/plugins/jasny/js/bootstrap-fileupload.js"></script>
        <script src="assets/js/notifications.js"></script>
    </body>
    <!-- END BODY-->    
</html>
