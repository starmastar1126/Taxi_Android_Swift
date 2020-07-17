<?php 
include_once('common.php');
$generalobj->check_member_login();

require_once(TPATH_CLASS . "/Imagecrop.class.php");
require_once(TPATH_CLASS . "/class.general.php");
 
//echo "<pre>" ; print_r($_SESSION); exit;

$thumb = new thumbnail();
$script = "Driver";
$sql = "select * from country";
$db_country = $obj->MySQLSelect($sql);

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] :'';
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != '') ? 'Edit' : 'Add';
$action_show = (isset($_REQUEST['action']) && $_REQUEST['action'] != '') ? $langage_lbl['LBL_COMPANY_DRIVER_EDIT'] : $langage_lbl['LBL_COMPANY_DRIVER_ADD'];
$var_msg = isset($_REQUEST["var_msg"]) ? $_REQUEST["var_msg"] : '';

$sql = "select * from language_master where eStatus = 'Active'";
$db_lang = $obj->MySQLSelect($sql);

$sql = "select * from register_driver where iDriverId = '" . $_REQUEST['id'] . "'";
$db_user = $obj->MySQLSelect($sql);
#echo"<pre>";print_r($db_user);exit;
$LicenceEXP = $db_user[0]['dLicenceExp'] ? $db_user[0]['dLicenceExp'] : '';

$vName = $db_user[0]['vName'];
$vLicence = $db_user[0]['vLicence'];
$vNoc = $db_user[0]['vNoc'];
$vCerti = $db_user[0]['vCerti'];
$action_doc = isset($_REQUEST['action_doc']) ? $_REQUEST['action_doc'] : '';
$success = isset($_REQUEST["success"]) ? $_REQUEST["success"] : 0;
$var_msg = isset($_REQUEST["var_msg"]) ? $_REQUEST["var_msg"] : '';

#echo"<pre>";print_r($action_doc);exit;


if ($action = 'document' && isset($_POST['doc_type'])) {
    $expDate = $_POST['dLicenceExp'];
    if (SITE_TYPE == 'Demo') {
        header("location:driver_document_action.php?success=2&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        exit;
    }

    $masterid = $_REQUEST['master'];

    if (isset($_POST['doc_path'])) {
        $doc_path = $_POST['doc_path'];
    }
    $temp_gallery = $doc_path . '/';
    $image_object = $_FILES['driver_doc']['tmp_name'];
    $image_name = $_FILES['driver_doc']['name'];
    if( empty($image_name )) {
        $image_name = $_POST['driver_doc_hidden']; 
    }

    if($expDate != ""){
        $sql = "UPDATE `document_list` SET  ex_date='".$expDate."' WHERE doc_userid='".$_REQUEST['id']."' and doc_masterid='".$masterid."'";
        $obj->sql_query($sql);
    }

    if ($_FILES['driver_doc']['name'] != "") {
        $filecheck = basename($_FILES['driver_doc']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp" && $ext != "pdf" && $ext != "doc" && $ext != "docx") {
            $flag_error = 1;
            $var_msg = $langage_lbl['LBL_IMAGE_FORMAT_ERROR_MSG'];
        }
        if ($flag_error == 1) {
        exit;
        } else {
            $Photo_Gallery_folder = $doc_path . '/' . $_REQUEST['id'] . '/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
            $vImage = $vFile[0];
            $var_msg = $langage_lbl['LBL_UPLOAD_MSG'];
            $tbl = 'document_list';
            $sql = "select doc_id from  ".$tbl."  where doc_userid='".$_REQUEST[id]."' and doc_usertype='driver'  and doc_masterid=".$_REQUEST['doc_type'];
            $db_data = $obj->MySQLSelect($sql);


            if (count($db_data) > 0) {
                $query = "UPDATE `".$tbl."` SET `doc_file`='".$vImage."' , `ex_date`='".$expDate."' WHERE doc_userid='".$_REQUEST[id]."' and doc_usertype='driver'  and doc_masterid=".$_REQUEST['doc_type'];
            } else {
                $query = " INSERT INTO `".$tbl."` ( `doc_masterid`, `doc_usertype`, `doc_userid`, `ex_date`, `doc_file`, `status`, `edate`) " . "VALUES ". "( '".$_REQUEST['doc_type']."', 'driver', '".$_REQUEST['id']."', '".$expDate."', '".$vImage."', 'Inactive', 	CURRENT_TIMESTAMP)";
            }
            $obj->sql_query($query);

            $vNocPath = $vImage;
            ###### Email #######
            $maildata['NAME'] = $db_user[0]['vName'];
            $maildata['EMAIL'] = $db_user[0]['vEmail'];
            $maildata['COMPANY'] = $_SESSION['sess_vCompany']." Company ";
            $generalobj->send_email_user("DOCCUMENT_UPLOAD", $maildata);
            #######Email ##########
            $generalobj->save_log_data($_SESSION['sess_iUserId'], $_REQUEST['id'], 'company', 'noc', $vNocPath);
            header("location:driver_document_action.php?success=1&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        }
    } else {
        $vImage = $_POST['driver_doc_hidden'];
        $var_msg = $langage_lbl['LBL_UPLOAD_MSG'];
        $tbl = 'document_list';
        $sql = "select doc_id from  ".$tbl."  where doc_userid='".$_REQUEST[id]."' and doc_usertype='driver'  and doc_masterid=".$_REQUEST['doc_type'];
        $db_data = $obj->MySQLSelect($sql);

        if (count($db_data) > 0) {
        $query = "UPDATE `".$tbl."` SET `doc_file`='".$vImage."' , `ex_date`='".$expDate."' WHERE doc_userid='".$_REQUEST[id]."' and doc_usertype='driver'  and doc_masterid=".$_REQUEST['doc_type'];
        } else {
        $query = " INSERT INTO `".$tbl."` ( `doc_masterid`, `doc_usertype`, `doc_userid`, `ex_date`, `doc_file`, `status`, `edate`) "
        . "VALUES ". "( '".$_REQUEST['doc_type']."', 'driver', '".$_REQUEST['id']."', '".$expDate."', '".$vImage."', 'Inactive',    CURRENT_TIMESTAMP)";
        }
        $obj->sql_query($query);

        $vNocPath = $vImage;

        ###### Email #######
        $maildata['NAME'] = $db_user[0]['vName'];
        $maildata['EMAIL'] = $db_user[0]['vEmail'];
        $maildata['COMPANY'] = $_SESSION['sess_vCompany']." Company ";
        $generalobj->send_email_user("DOCCUMENT_UPLOAD", $maildata);
        #######Email ##########
        $generalobj->save_log_data($_SESSION['sess_iUserId'], $_REQUEST['id'], 'company', 'noc', $vNocPath);
        header("location:driver_document_action.php?success=1&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
    }

}

$sql = "SELECT dm.doc_masterid masterid, dm.doc_usertype ,dm.doc_name_".$_SESSION['sess_lang']."  as d_name , dm.doc_name ,dm.ex_status,dm.status, dl.doc_masterid masterid_list ,dl.ex_date,dl.doc_file , dl.status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" . $id . "' ) dl on dl.doc_masterid=dm.doc_masterid  
	where dm.doc_usertype='driver' and dm.status='Active' and (dm.country ='".$db_user[0]['vCountry']."' OR dm.country ='All')";
$db_userdoc = $obj->MySQLSelect($sql);
$count_all_doc = count($db_userdoc);
?>
<!DOCTYPE html>
<html lang="en" dir="<?= (isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr'; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?= $SITE_NAME ?> | <?= $langage_lbl['LBL_DRIVER_COMPANY_TXT']." ".$action_show; ?></title>
        <!-- Default Top Script and css -->
        <?php  include_once("top/top_script.php"); ?>
        <link rel="stylesheet" href="assets/css/bootstrap-fileupload.min.css" >
        <style>
            .fileupload-preview  { line-height:150px;}
        </style>
        <!-- End: Default Top Script and css-->
    </head>
    <body>
        <!-- home page -->
        <div id="main-uber-page">
            <!-- Left Menu -->
            <?php  include_once("top/left_menu.php"); ?>
            <!-- End: Left Menu-->
            <!-- Top Menu -->
            <?php  include_once("top/header_topbar.php"); ?>
            <!-- End: Top Menu-->
            <!-- contact page-->
            <div class="page-contant">
                <div class="page-contant-inner">
                    <h2 class="header-page trip-detail driver-detail1"><?= ucfirst($action_show); ?> <?= $langage_lbl['LBL_Driver_document_Document_of']; ?>  <?= $vName; ?> 
                        <a href="driver.php"><img src="assets/img/arrow-white.png" alt=""><?= $langage_lbl['LBL_Driver_document-back_to_listing']; ?></a>
                    </h2>
                    <!-- driver vehicles page -->
                    <div class="driver-vehicles-page">
                        <?php  if ($_REQUEST['success']==1) {?>
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button> 
                            <?= $var_msg ?>
                        </div>
                        <?php }else if($_REQUEST['success']==2){ ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?= $langage_lbl['LBL_EDIT_DELETE_RECORD']; ?>
                        </div>
                        <?php 
                        }
                        ?>
                        <div class="<?php  echo $class_name; ?>">
                          <h2 class="driver-head"><?= $langage_lbl['LBL_REQUIRED_DOCS']; ?></h2>
                            <div class="profile-req-doc profile-req-doc-driver driver-document-action-page">
                                <div class="profile-req-doc-inner">
                                    <?php  for ($i = 0; $i < $count_all_doc; $i++) { ?>
                                        <div class="panel panel-default upload-clicking">
                                            <input  type="hidden" id="ex_status" value="<?php  echo $db_userdoc[$i]['ex_status']; ?>">
                                            <div class="panel-heading"><?php  echo $db_userdoc[$i]['d_name']; ?> </div>
                                            <input type="hidden" id="doc_id" value="<?php  $db_userdoc[$i]['doc_file']; ?>">
                                            <div class="panel-body">
                                               <label>
												<?php  if ($db_userdoc[$i]['doc_file'] != '') { ?>
                                                    <?php 
                                                    $file_ext = $generalobj->file_ext($db_userdoc[$i]['doc_file']);
                                                    if ($file_ext == 'is_image') {
                                                        ?>
                                                        <a href="<?= $tconfig["tsite_upload_driver_doc"] . '/' . $id . '/' . $db_userdoc[$i]['doc_file'] ?>" target="_blank"><img src = "<?= $tconfig["tsite_upload_driver_doc"] . '/' . $id . '/' . $db_userdoc[$i]['doc_file'] ?>" style="width:200px;cursor:pointer;" alt ="<?= $db_userdoc[$i]['d_name']; ?> Image" /></a>

                                                    <?php  } else {
                                                        ?>
                                                        <a href="<?= $tconfig["tsite_upload_driver_doc"] . '/' . $id . '/' . $db_userdoc[$i]['doc_file'] ?>" target="_blank"><?php  echo $db_userdoc[$i]['d_name']; ?></a>

                                                    <?php  } ?>
                                                    <?php 
                                                } else {
                                                     echo $db_userdoc[$i]['d_name'] ." " . $langage_lbl['LBL_NOT_FOUND'];
                                                }
                                                ?>
                                                </label>
                                                <br/>
                                                <b><button class="btn btn-info" data-toggle="modal" data-target="#uiModal" id="custId"  onClick="setModel001('<?php  echo $db_userdoc[$i]['masterid']; ?>')" >

                                                        <?php 
                                                        if ($db_userdoc[$i]['doc_file'] != '') {
                                                            echo $db_userdoc[$i]['d_name'];
                                                        } else {
                                                            echo $db_userdoc[$i]['d_name'];
                                                        }
                                                        ?>
                                                    </button></b>
                                            </div>
                                        </div>
                                    <?php  } ?>
                                </div>  </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-content image-upload-1">
                                    <div class="fetched-data"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div style="clear:both;"></div>
                </div>
            </div>
            <!-- End:contact page-->



            <!-- footer part -->
            <?php  include_once('footer/footer_home.php'); ?>
            <!-- footer part end -->
            <div style="clear:both;"></div>
        </div>
        <!-- home page end-->
        <!-- Footer Script -->
        <?php  include_once('top/footer_script.php');
        $lang = get_langcode($_SESSION['sess_lang']);?>
        <script type="text/javascript" src="assets/js/validation/jquery.validate.min.js" ></script>
        <?php  if($lang != 'en') { ?>
        <script type="text/javascript" src="assets/js/validation/localization/messages_<?= $lang; ?>.js" ></script>
        <?php  } ?>
        <script type="text/javascript" src="assets/js/validation/additional-methods.js" ></script>
        <script src="assets/plugins/jasny/js/bootstrap-fileupload.js"></script>

        <!-- Start :: Datepicker css-->
        <link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css" />
        <script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
        <script src="assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
        <script src="assets/plugins/timepicker/js/bootstrap-timepicker.min.js"></script>
        <!-- Start :: Datepicker-->

        <script type="text/javascript">

            function setModel001(idVal) {
                // $('#uiModal').on('show.bs.modal', function (e) {
                // var rowid = $(e.relatedTarget).data('id');
                var id = '<?php  echo $id; ?>';

                $.ajax({
                    type: 'post',
                    url: 'driver_document_fetch1.php', //Here you will fetch records 
                    data: 'rowid=' + idVal + '-' + id, //Pass $id
                    success: function (data) {

                        $('#uiModal').modal('show');
                        $('.fetched-data').html(data);//Show fetched data from database

                    }
                });
            }
            function confirm_delete(id)
            {
                var tsite_url = '<?php  echo $tconfig["tsite_url"]; ?>';
                if (id != '') {
                    var confirm_ans = confirm('<?=addslashes($langage_lbl['LBL_DELETE_VEHICLE_CONFIRM_MSG']);?>');
                    if (confirm_ans == true) {
                        window.location.href = "vehicle.php?action=delete&id=" + id;
                    }
                }
                //document.getElementById(id).submit();
            }
        </script>


        <!-- End: Footer Script -->
    </body>
</html>

