<?php 
include_once('../common.php');
/*require_once(TPATH_CLASS . "Imagecrop.class.php");
$thumb = new thumbnail();*/
$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : '';
$id = explode('-',$rowid);
      
/* $sql = "select  dm.`doc_masterid`, dm.`doc_usertype`, dm.`doc_name`, dm.`ex_status`, dl.`doc_id`, dl.`doc_masterid`, dl.`doc_usertype`, dl.`doc_userid`, dl.`ex_date`, dl.`doc_file`,rd.`iDriverId` 
     from document_master as dm
left join document_list  as dl on dl.doc_masterid= dm.doc_masterid
left join  register_driver as rd on  dl.doc_userid= rd.iDriverId
where dl.doc_usertype='company' AND dl.doc_userid='".$id[1]."'  AND rd.iDriverId='".$id[1]."' and dm.doc_masterid='".$id[0]."'" ;
$db_user = $obj->MySQLSelect($sql);
	
$sql1="select doc_name,ex_status from document_master where doc_masterid='".$id[0]."'";
$db_user1 = $obj->MySQLSelect($sql1); */

$sql = "select  *  from document_master  where doc_masterid='".$id[0]."'" ;
$db_user_doc = $obj->MySQLSelect($sql);


$sql = "select * from document_list where doc_masterid='".$id[0]."' AND doc_userid='".$id[1]."'";
$db_user_li = $obj->MySQLSelect($sql);
//if($db_user[0]['doc_name']== ''){ $vName = $db_user1[0]['doc_name'];}else{ $vName=$db_user[0]['doc_name'];}
?>


<div class="upload-content">
    <h4><?php  echo $db_user_doc[0]['doc_name']; ?></h4>
    <form class="form-horizontal" id="frm6" method="post" enctype="multipart/form-data" action="company_document_action.php?id=<?php  echo $id[1] ; ?>&master=<?php  echo $id[0] ; ?> " name="frm6">
        <input type="hidden" name="action" value ="document"/>
        <input type="hidden" name="doc_type" value="<?php  echo $id[0]; ?>" />
        <input type="hidden" name="doc_path" value =" <?php  echo $tconfig["tsite_upload_compnay_doc_path"]; ?>"/>
        
        <div class="form-group">
            <div class="col-lg-12">
                <div class="fileupload fileupload-new" data-provides="fileupload">
                    <div class="fileupload-preview thumbnail" style="width: 100%; height: 150px; ">
                        <?php  if ($db_user_li[0]['doc_file'] == '') { 
                            echo 'No '.$db_user_doc[0]['doc_name'].' Photo';
                            
                        } else { ?>
                            <?php 
                            $file_ext = $generalobj->file_ext($db_user_li[0]['doc_file']);
                            if ($file_ext == 'is_image') {
                                ?>

                                <img src = "<?= $tconfig["tsite_upload_compnay_doc"] . '/' . $id[1] . '/' . $db_user_li[0]['doc_file'] ?>" style="width:100%;" alt ="<?php  echo $db_user_doc[0]['doc_name'];?> not found"/>
                            <?php  } else { ?>
                                <a href="<?= $tconfig["tsite_upload_compnay_doc"] . '/' . $id[1] . '/' . $db_user_li[0]['doc_file'] ?>" target="_blank"><?php  echo $db_user_li[0]['doc_name']; ?></a>
                            <?php  } ?>
                        <?php  } ?>
                    </div>
                    <div>
                        <span class="btn btn-file btn-success"><span class="fileupload-new">Upload <?php  echo $db_user_doc[0]['doc_name'] ?> Photo</span>
                            <span class="fileupload-exists">Change</span>
                            <input type="file" name="company_doc" /></span>
                        <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a>
                        <input type="hidden" name="company_doc_hidden"  id="company_doc" value="<?php  echo ($db_user_li[0]['doc_file'] !="") ? $db_user_li[0]['doc_file'] : '';?>" />
                    </div>
                    <div class="upload-error"><span class="file_error"></span></div>
                </div>
            </div>
        </div>
        <?php  if($db_user_doc[0]['ex_status']=='yes') { ?>
        <h5><b>EXP. DATE</b></h5>      
         <div class="col-lg-13 exp-date">
            <div class="input-group input-append date" id="dp122">
                <input class="form-control" type="text" name="dLicenceExp" value="<?php  echo ($db_user_li[0]['ex_date'] !="") ? $db_user_li[0]['ex_date'] : '';?>" readonly="" required/>
                <span class="input-group-addon add-on"><i class="icon-calendar"></i></span>
            </div>
            <div class="exp-error"><span class="exp_error"></span></div>
        </div>    
        <?php  }  ?>
       <input type="submit" class="save" name="save" value="Save">
        <input type="button" class="cancel" data-dismiss="modal" name="cancel" value="Cancel">
    </form>
</div>
<script>
$(document).ready(function() {
    $('#frm6').validate({
        ignore: 'input[type=hidden]',
        errorClass: 'help-block error',
        errorElement: 'span',
        errorPlacement: function(error, element) {
            if (element.attr("name") == "company_doc")
            {
                error.insertAfter("span.file_error");
            } else if(element.attr("name") == "dLicenceExp"){
                error.insertAfter("span.exp_error");
            } else {
                error.insertAfter(element);
            }
        },
        rules: {
            company_doc: {
                required: {
                    depends: function(element) {
                        if ($("#company_doc").val() == "") { 
                            return true;
                        } else { 
                            return false;
                        } 
                    }
                },
                extension: "jpg|jpeg|png|gif|pdf|doc|docx"
            }
        },
        messages: {
            company_doc: {
                required: 'Please Upload Image.',
                extension: 'Please Upload valid file format. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png'
            }
        }
    });
});
    $(function () {
       newDate = new Date('Y-M-D');
		$('#dp122').datetimepicker({
			format: 'YYYY-MM-DD',
			minDate: moment(),
			ignoreReadonly: true,
		});
    });
</script>