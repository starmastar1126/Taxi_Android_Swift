<?php 
include_once('common.php');

require_once(TPATH_CLASS . "Imagecrop.class.php");
$thumb = new thumbnail();
$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : '';
$id = explode('-',$rowid);

$sql = "select  dm.`doc_masterid`, dm.`doc_usertype`, dm.`doc_name`, dm.doc_name_".$_SESSION['sess_lang']." as document , dm.`ex_status`, dl.`doc_id`, dl.`doc_masterid`, dl.`doc_usertype`, dl.`doc_userid`, dl.`ex_date`, dl.`doc_file`,rd.`iDriverId` from document_master as dm left join document_list  as dl on  dl.doc_masterid= dm.doc_masterid left join  register_driver as rd on  dl.doc_userid= rd.iDriverId where dl.doc_usertype='driver' AND  iDriverId='".$id[1]."' and dm.doc_masterid='".$id[0]."'" ;	
$db_user = $obj->MySQLSelect($sql);

$sql1="select doc_name,ex_status,doc_name_".$_SESSION['sess_lang']." as document from document_master where doc_masterid='".$id[0]."'";
$db_user1 = $obj->MySQLSelect($sql1);
    
if($db_user[0]['document']== ''){ $vName = $db_user1[0]['document'];}else{ $vName=$db_user[0]['document'];}
?>
<div class="upload-content">
    <h4><?php  echo $vName; ?></h4>
    <form class="form-horizontal frm6" id="frm6" method="post" enctype="multipart/form-data" action="driver_document_action.php?id=<?php  echo $id[1] ; ?>&master=<?php  echo $id[0] ; ?> " name="frm6">
        <input type="hidden" name="action" value ="document"/>
		<input type="hidden" name="user" value ="<?php  echo $user;?>"/>
		<input type="hidden" name="doc_type" value="<?php  echo $id[0]; ?>" />
        <input type="hidden" name="doc_path" value =" <?php   echo $tconfig["tsite_upload_driver_doc_path"]; ?>"/>
        
        <div class="form-group">
            <div class="col-lg-12">
                <div class="fileupload fileupload-new" data-provides="fileupload">
                    <div class="fileupload-preview thumbnail" style="width: 100%; height: 150px; ">
                        <?php  if ($db_user[0]['doc_file'] == '') { 
                            echo $langage_lbl['LBL_NO'] .$vName. $langage_lbl['LBL_PHOTO'];
                        } else { ?>
                            <?php 
                            $file_ext = $generalobj->file_ext($db_user[0]['doc_file']);
                            if ($file_ext == 'is_image') { ?>
                                <img src = "<?= $tconfig["tsite_upload_driver_doc"]. '/' . $id[1] . '/' . $db_user[0]['doc_file'] ?>" style="width:100%;" alt ="<?php  echo $db_user[0]['doc_name']; ?> not found"/>
                            <?php  } else { ?>
                                <a href="<?= $tconfig["tsite_upload_driver_doc"]. '/' . $id[1] . '/' . $db_user[0]['doc_file'] ?>" target="_blank"><?php  echo $db_user[0]['doc_name']; ?></a>
                            <?php  } ?>
                        <?php  } ?>
                    </div>
                    <div>
                        <span class="btn btn-file btn-success"><span class="fileupload-new"><?=$langage_lbl['LBL_UPLOAD']; ?> <?php  echo $vName ?> <?=$langage_lbl['LBL_PHOTO']; ?></span>
                            <span class="fileupload-exists"><?=$langage_lbl['LBL_CHANGE']; ?></span>
                            <input type="file" name="driver_doc" /></span>
                        <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><?=$langage_lbl['LBL_REMOVE_TEXT']; ?></a>
                        <input type="hidden" name="driver_doc_hidden"  id="driver_doc" value="<?php  echo ($db_user[0]['doc_file'] !="") ? $db_user[0]['doc_file'] : '';?>" />
                    </div>
                    <div class="upload-error"><span class="file_error"></span></div>
                </div>
            </div>
        </div>
        <?php  if($db_user[0]['ex_status']=='yes' || $db_user1[0]['ex_status']=='yes') { ?>
        <h5><b><?=$langage_lbl['LBL_EXP_DATE']; ?></b></h5>
        <div class="col-lg-13 exp-date">
            <div class="input-group input-append date" id="dp123" data-date="" data-date-format="yyyy-mm-dd">
                <input class="form-control" type="text" name="dLicenceExp" value="<?php  if($db_user[0]['ex_date'] != ''){ echo $db_user[0]['ex_date'];}?>" readonly="" required/>
                <span class="input-group-addon add-on"><i class="icon-calendar"></i></span>
            </div>
            <div class="exp-error"><span class="exp_error"></span></div>
        </div>
        <?php  }  ?>
        <input type="submit" class="save save11" name="save" value="<?= $langage_lbl['LBL_Save']; ?>">
        <input type="button" class="cancel cancel11" data-dismiss="modal" name="cancel" value="<?= $langage_lbl['LBL_CANCEL_TXT']; ?>">
    </form>
</div>
<script>
$(document).ready(function() {
    $('#frm6').validate({
        ignore: 'input[type=hidden]',
        errorClass: 'help-block error',
        errorElement: 'span',
        errorPlacement: function(error, element) {
            if (element.attr("name") == "driver_doc")
            {
                error.insertAfter("span.file_error");
            }  else if(element.attr("name") == "dLicenceExp"){
                error.insertAfter("span.exp_error");
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            driver_doc: {
                required: {
                    depends: function(element) {
                        if ($("#driver_doc").val() == "") { 
                            return true;
                        } else { 
                            return false;
                        } 
                    }
                },
                accept: "image/*,.doc,.docx,.pdf"
            }
        },
        messages: {
            driver_doc: {
                required: '<?php  echo $langage_lbl['LBL_UPLOAD_IMG']; ?>',
                accept: '<?php  echo $langage_lbl['LBL_UPLOAD_IMG_ERROR'];?>'
            }
        }
    });
});

$(function () {

    // var nowTemp = new Date();
    // var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    $('#dp123').datepicker({
        // onRender: function (date) {
            // return date.valueOf() < now.valueOf() ? 'disabled' : '';
       // }
    });
    //formInit();
});
</script>