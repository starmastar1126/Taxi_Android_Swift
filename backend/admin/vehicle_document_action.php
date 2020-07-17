<?php 
include_once('../common.php');
if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();

$sql = "select * from country";
$db_country = $obj->MySQLSelect($sql);

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != '') ? 'Edit' : 'Add';
$doc_type = isset($_REQUEST['doc_type']) && $_REQUEST['doc_type'] != '';
$backlink=isset($_POST['backlink'])?$_POST['backlink']:'';
$previousLink=isset($_POST['backlink'])?$_POST['backlink']:'';

$sql = "select iDriverId from driver_vehicle where iDriverVehicleId = '".$_REQUEST['id']."'";
$iDriverVehicleId = $obj->MySQLSelect($sql);
$iDriverVehicleId=$iDriverVehicleId[0]['iDriverId'];

$sql = "select vCountry,vCity from register_driver where iDriverId = '" .$iDriverVehicleId . "'";
$db_user = $obj->MySQLSelect($sql);

// get country id
 $sql = "select iCountryId from country where vCountryCode = '".$db_user[0]['vCountry']."'";
 $db_countrycode = $obj->MySQLSelect($sql);

// end country get  

$script = 'Vehicle';
$sql = "select * from language_master where eStatus = 'Active'";
$db_lang = $obj->MySQLSelect($sql);

   $sql1= "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, dl.doc_masterid masterid_list ,dl.ex_date,dl.doc_file , dl.status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" .$_REQUEST['id']."' and doc_usertype='car') dl on dl.doc_masterid=dm.doc_masterid  
    where dm.doc_usertype='car' and dm.status='Active' and (dm.country ='".$db_user[0]['vCountry']."' OR dm.country ='All')";

$db_userdoc = $obj->MySQLSelect($sql1);

$count_all = count($db_userdoc);
//print_r($db_userdoc);
 //exit();
$sql = "select * from driver_vehicle where iDriverVehicleId = '" . $_REQUEST['id'] . "'";
$db_user = $obj->MySQLSelect($sql);
$vName = $db_user[0]['vLicencePlate'];
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$success = isset($_REQUEST["success"]) ? $_REQUEST["success"] : '';
$var_msg = isset($_REQUEST["var_msg"]) ? $_REQUEST["var_msg"] : '';

if ($action='document' && isset($_POST['doc_type'])) {
		
    $expDate=$_POST['dLicenceExp'];
	
    // if (SITE_TYPE == 'Demo') {
        // header("location:vehicle_document_fetch.php?success=2&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        // exit;
    // }
    $masterid= $_REQUEST['doc_type'];

    if (isset($_POST['doc_path'])) {
        $doc_path = $_POST['doc_path'];
    }
    $temp_gallery = $doc_path . '/';
     $image_object = $_FILES['vehicle_doc']['tmp_name'];
     $image_name = $_FILES['vehicle_doc']['name'];

    if( empty($image_name )) {
        $image_name = $_POST['vehicle_doc_hidden']; 
    } 
    if ($image_name == "") {
     if($expDate != ""){

              $sql = "select ex_date from document_list where doc_userid='".$_REQUEST['id']."' and doc_masterid='".$masterid."'"; 
			  // $query = mysqli_query($sql);
			  $query = $obj->MySQLSelect($sql);
              $fetch = $query[0];
			  
   
                    if($fetch['ex_date'] != $expDate){    
                    
                       $sql="UPDATE `document_list` SET  ex_date='".$expDate."' WHERE doc_userid='".$_REQUEST['id']."' and doc_masterid='".$masterid."'";
            } else {
                $sql = "INSERT INTO `document_list` ( `doc_masterid`, `doc_usertype`, `doc_userid`, `ex_date`, `doc_file`, `status`, `edate`) VALUES ( '".$_REQUEST['doc_type']."', 'car', '".$_REQUEST['id']."', '".$expDate."', '', 'Inactive', CURRENT_TIMESTAMP)";
                  }

            // $query= mysqli_query($sql);
            $query= $obj->sql_query($sql);
        }

         $var_msg = "Please Upload valid file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png";
        header("location:vehicle_document_action.php?success=3&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        exit;
    }

    if ($_FILES['vehicle_doc']['name'] != "") {
        $check_file_query = "select iDriverId,vNoc from register_driver where iDriverId=" . $_REQUEST['id'];
        $check_file = $obj->sql_query($check_file_query);
        $check_file['vNoc'] = $doc_path . '/' . $_REQUEST['id'] . '/' . $check_file[0]['vNoc'];



        $filecheck = basename($_FILES['vehicle_doc']['name']);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp" && $ext != "pdf" && $ext != "doc" && $ext != "docx") {
            $flag_error = 1;
            $var_msg = "You have selected wrong file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png";
        }

        if ($flag_error == 1) {
            header("location:vehicle_document_action.php?success=3&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);

            exit;

        } else {
             $Photo_Gallery_folder = $doc_path . '/' . $_REQUEST['id'] . '/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            //$img = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_documnet_size1"], $tconfig["tsite_upload_documnet_size2"], '', '', '', '', 'Y', '', $Photo_Gallery_folder);
            $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
            $vImage = $vFile[0];
            $var_msg = "document uploaded successfully";
            $tbl = 'document_list';
            $sql = "select doc_id from  ".$tbl."  where doc_userid='".$_REQUEST[id]."' and doc_usertype='car'  and doc_masterid=".$_REQUEST['doc_type'] ;
			$db_data = $obj->MySQLSelect($sql);
/*            $q = "INSERT INTO ";
            $where = '';
*/
            if (count($db_data) > 0) {
	        $query="UPDATE `".$tbl."` SET `doc_file`='".$vImage."' , `ex_date`='".$expDate."' WHERE doc_userid='".$_REQUEST[id]."' and doc_usertype='car'  and doc_masterid=".$_REQUEST['doc_type'];
/*                $q = "UPDATE ";
                $where = " WHERE `iDriverId` = '" . $_REQUEST['id'] . "'";*/
            } else {
            $query =" INSERT INTO `".$tbl."` ( `doc_masterid`, `doc_usertype`, `doc_userid`, `ex_date`, `doc_file`, `status`, `edate`) "
                   . "VALUES "
                   . "( '".$_REQUEST['doc_type']."', 'car', '".$_REQUEST['id']."', '".$expDate."', '".$vImage."', 'Inactive', CURRENT_TIMESTAMP)";
           
			}
            $obj->sql_query($query);

            //Start :: Log Data Save
            if (empty($check_file[0]['vNoc'])) {
                $vNocPath = $vImage;
            } else {
                $vNocPath = $check_file[0]['vNoc'];
            }
            $generalobj->save_log_data($_SESSION['sess_iUserId'], $_REQUEST['id'], 'company', 'noc', $vNocPath);
            //End :: Log Data Save
            // Start :: Status in edit a Document upload time
            // $set_value = "`eStatus` ='inactive'";
            //$generalobj->estatus_change('register_driver','iDriverId',$_REQUEST['id'],$set_value);
            // End :: Status in edit a Document upload time
            header("location:vehicle_document_action.php?success=1&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
        }
    } else {
        $check_file_query = "select iDriverId,vNoc from register_driver where iDriverId=" . $_REQUEST['id'];
        $check_file = $obj->sql_query($check_file_query);
        $check_file['vNoc'] = $doc_path . '/' . $_REQUEST['id'] . '/' . $check_file[0]['vNoc'];
        $vImage = $_POST['vehicle_doc_hidden'];
        $var_msg = "document uploaded successfully";
        $tbl = 'document_list';
        $sql = "select doc_id from  ".$tbl."  where doc_userid='".$_REQUEST[id]."' and doc_usertype='car'  and doc_masterid=".$_REQUEST['doc_type'] ;
        $db_data = $obj->MySQLSelect($sql);
        if (count($db_data) > 0) {
        $query="UPDATE `".$tbl."` SET `doc_file`='".$vImage."' , `ex_date`='".$expDate."' WHERE doc_userid='".$_REQUEST[id]."' and doc_usertype='car'  and doc_masterid=".$_REQUEST['doc_type'];
        } else {
        $query =" INSERT INTO `".$tbl."` ( `doc_masterid`, `doc_usertype`, `doc_userid`, `ex_date`, `doc_file`, `status`, `edate`) "
               . "VALUES " . "( '".$_REQUEST['doc_type']."', 'car', '".$_REQUEST['id']."', '".$expDate."', '".$vImage."', 'Inactive', CURRENT_TIMESTAMP)";
        }
        $obj->sql_query($query);
        //Start :: Log Data Save
        if (empty($check_file[0]['vNoc'])) {
            $vNocPath = $vImage;
        } else {
            $vNocPath = $check_file[0]['vNoc'];
        }
        $generalobj->save_log_data($_SESSION['sess_iUserId'], $_REQUEST['id'], 'company', 'noc', $vNocPath);
        header("location:vehicle_document_action.php?success=1&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
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
        <title><?= $SITE_NAME ?> | <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> <?= $action; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <meta content="" name="keywords" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

        <?php   include_once('global_files.php'); ?>
        <!-- On OFF switch -->
        <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
        <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
        <link rel="stylesheet" href="../assets/css/bootstrap-fileupload.min.css" >
        <script src="../	assets/plugins/jasny/js/bootstrap-fileupload.js"></script>
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53 " >

        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php 
            include_once('header.php');
            ?>
            <?php 
            include_once('left_menu.php');
            ?>
            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2><?= ucfirst($action); ?> Document of  <?= $vName; ?></h2>
                            <input type="button" class="add-btn" value="Close" onClick="javascript:window.top.close();">
                            <!-- <a class="back_link" href="vehicles.php?type=<?php  echo $_REQUEST['type']?>">
                                <input type="button" value="Back to Listing" class="add-btn">
                            </a> -->
                        </div>
                    </div>
                    <hr />
                    <div class="body-div">
                        <div class="form-group">
                            <?php  if ($success == 1) {?>
                            <div class="alert alert-success alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <?= $var_msg; ?>
                            </div><br/>
                            <?php } ?>

                            <?php  if ($success == 2) {?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                            </div><br/>
                            <?php } ?>
                            <?php  if ($success == 3) {?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                <?= $var_msg; ?>
                            </div><br/>
                            <?php } ?>
                            <input type="hidden" name="id" value="<?= $id; ?>"/>
                            <input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
                            <input type="hidden" name="backlink" id="backlink" value="vehicles.php"/>
                            <div class="row">
                                <div class="col-sm-12">
                                    <h4 style="margin-top:0px;">DOCUMENTS</h4>
                                </div>
                            </div>
                            <div class="row company-document-action">

                                <?php  for ($i = 0; $i < $count_all; $i++) {  ?>
                                        <div class="col-lg-3">
                                        <div class="panel panel-default upload-clicking">
                                            <div class="panel-heading"><?php  echo $db_userdoc[$i]['doc_name'];?> </div>
                                            <div class="panel-body">

                                                <?php  if ($db_userdoc[$i]['doc_file'] != '' && file_exists('../webimages/upload/documents/vehicles/' . $_REQUEST['id'] . '/' . $db_userdoc[$i]['doc_file'])) { ?>
                                                    <?php 
                                                    $file_ext = $generalobj->file_ext($db_userdoc[$i]['doc_file']);
                                                    if ($file_ext == 'is_image') {
                                                        ?>
                                                        <a href="<?= $tconfig["tsite_upload_vehicle_doc_panel"] . '/' . $_REQUEST['id'] . '/' . $db_userdoc[$i]['doc_file'] ?>" target="_blank"><img src = "<?= $tconfig["tsite_upload_vehicle_doc_panel"] . '/' . $_REQUEST['id'] . '/' . $db_userdoc[$i]['doc_file'] ?>" style="width:200px;cursor:pointer;" alt ="YOUR DRIVING LICENCE" /></a>
                                                    <?php  } else { ?>
                                                        <p><a href="<?= $tconfig["tsite_upload_vehicle_doc_panel"] . '/' . $_REQUEST['id'] . '/' . $db_userdoc[$i]['doc_file'] ?>" target="_blank"><?php  echo $db_userdoc[$i]['doc_name']; ?></a></p>
                                                    <?php  } ?>
                                                    <?php 
                                                } else {
                                                    echo "<p>".$db_userdoc[$i]['doc_name'] . ' not found'."</p>";
                                                }
                                                ?>
                                                <br/>
                                                <b><button class="btn btn-info" data-toggle="modal" data-target="#uiModal" id="custId"  onClick="setModel001('<?php  echo $db_userdoc[$i]['masterid']; ?>','<?php  echo $db_userdoc[$i]['ex_status']; ?>');"  >

                                                        <?php 
                                                        if ($db_userdoc[$i]['doc_name'] != '') {
                                                            echo $db_userdoc[$i]['doc_name'];
                                                        } 
                                                        ?>
                                                    </button></b>
                                            </div>
                                        </div>
                                    </div>
                                 <?php  } ?>


                              
                                <div class="col-lg-12">
                                    <div class="modal fade" id="uiModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-content image-upload-1">
                                            <div class="fetched-data"></div>
                                        </div>
                                    </div>
                                </div>
                             
                             
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--END PAGE CONTENT -->
    </div>
    <!--END MAIN WRAPPER -->

    <!-- Modal -->              
   
    <script>
    /*$(document).ready(function() {
        var referrer;
        if($("#previousLink").val() == "" ){
            referrer =  document.referrer;
        }else {
            referrer = $("#previousLink").val();
        }
        if(referrer == "") {
            referrer = "vehicles.php";
        } else {
            $("#backlink").val(referrer);
        }
        $(".back_link").attr('href',referrer);
    });*/
        // $(document).ready(function () {
            // $('#uiModal').on('show.bs.modal', function (e) {
                // var rowid = $(e.relatedTarget).data('id');
                // $.ajax({
                    // type: 'post',
                    // url: 'vehicle_document_fetch.php', //Here you will fetch records 
                    // data: 'rowid=' + rowid + '-' + <?php  echo $_REQUEST['id']; ?>, //Pass $id
                // success: function (data) {
                    // $('.fetched-data').html(data);//Show fetched data from database
					  // $('#dp3').datepicker();
                // }
            // });
        // });
    // });
	
	 function setModel001(idVal,ex_status) {
            // $('#uiModal').on('show.bs.modal', function (e) {
                // var rowid = $(e.relatedTarget).data('id');
				
			$.ajax({
				type: 'post',
				url: 'vehicle_document_fetch.php', //Here you will fetch records 
				data: 'rowid=' + idVal + '-' + <?php  echo $_REQUEST['id']; ?>+'-'+ex_status, //Pass $id
				success: function (data) {
				
					$('#uiModal').modal('show');
					$('.fetched-data').html(data);//Show fetched data from database
					
				}
			});
		}
		
</script>
<?php  include_once('footer.php');?>
<link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>

<script src="../assets/js/jquery-ui.min.js"></script>
<script src="../assets/plugins/uniform/jquery.uniform.min.js"></script>
<script src="../assets/plugins/inputlimiter/jquery.inputlimiter.1.3.1.min.js"></script>
<script src="../assets/plugins/chosen/chosen.jquery.min.js"></script>
<script src="../assets/plugins/colorpicker/js/bootstrap-colorpicker.js"></script>
<script src="../assets/plugins/tagsinput/jquery.tagsinput.min.js"></script>
<script src="../assets/plugins/validVal/js/jquery.validVal.min.js"></script>


<script src="../assets/plugins/autosize/jquery.autosize.min.js"></script>
<script src="../assets/plugins/jasny/js/bootstrap-inputmask.js"></script>
<script src="../assets/js/formsInit.js"></script>
<script>
    // $(function () {

        // var nowTemp = new Date();
        // var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

        // $('#dp3').datepicker({
            // onRender: function (date) {
                // return date.valueOf() < now.valueOf() ? 'disabled' : '';
            // }
        // });
        // formInit();
    // });
</script>

</body>
<!-- END BODY-->
</html>
