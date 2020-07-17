<?php 
include_once('common.php');

require_once(TPATH_CLASS . "Imagecrop.class.php");
$thumb = new thumbnail();

 $rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : '';
  $id = explode('-',$rowid);
  //print_r($id);
      
   $sql = "select  dm.`doc_masterid`, dm.`doc_usertype`, dm.`doc_name`, dm.`ex_status`, dl.`doc_id`, dl.`doc_masterid`, dl.`doc_usertype`, dl.`doc_userid`, dl.`ex_date`, dl.`doc_file`,c.`iCompanyId` 
     from document_master as dm
	left join document_list  as dl on dl.doc_masterid= dm.doc_masterid
	left join  company as c on  dl.doc_userid= c.iCompanyId
	where dl.doc_usertype='company' AND  iCompanyId='".$id[1]."' and dm.doc_masterid='".$id[0]."'" ;

	
	$sql1="select doc_name,ex_status from document_master where doc_masterid='".$id[0]."'";
	$db_user1 = $obj->MySQLSelect($sql1);


$db_user = $obj->MySQLSelect($sql);
if($db_user[0]['doc_name']== ''){ $vName = $db_user1[0]['doc_name'];}else{ $vName=$db_user[0]['doc_name'];}
?>

<link rel="stylesheet" href="assets/plugins/datepicker/css/datepicker.css" />
<script src="assets/plugins/daterangepicker/daterangepicker.js"></script>
<div class="upload-content">
    <h4><?php  echo $vName; ?></h4>
    <form class="form-horizontal" id="frm6" method="post" enctype="multipart/form-data" action="profile.php?id=<?php  echo $id[1] ; ?>&master=<?php  echo $id[0] ; ?> " name="frm6">
        <input type="hidden" name="action" value ="company"/>
        <input type="hidden" name="doc_type" value="<?php  echo $id[0]; ?>" />
        <input type="hidden" name="doc_path" value =" <?php  echo $tconfig["tsite_upload_compnay_doc_path"]; ?>"/>
        
        <div class="form-group">
            <div class="col-lg-12">
                <div class="fileupload fileupload-new" data-provides="fileupload">
                    <div class="fileupload-preview thumbnail" style="width: 100%; height: 150px; ">
                        <?php  if ($db_user[0]['doc_file'] == '') { 
                            echo 'No '.$vName.' Photo';
                            
                        } else { ?>
                            <?php 
                            $file_ext = $generalobj->file_ext($db_user[0]['doc_file']);
                            if ($file_ext == 'is_image') {
                                ?>

                                <img src = "<?= $tconfig["tsite_upload_compnay_doc"] . '/' . $id[1] . '/' . $db_user[0]['doc_file'] ?>" style="width:100%;" alt ="Licence not found"/>
                            <?php  } else { ?>
                                <a href="<?= $tconfig["tsite_upload_compnay_doc"] . '/' . $id[1] . '/' . $db_user[0]['doc_file'] ?>" target="_blank"><?php  echo $db_user[0]['doc_name']; ?></a>
                            <?php  } ?>
                        <?php  } ?>
                    </div>
                    <div>
                        <span class="btn btn-file btn-success"><span class="fileupload-new"><?=$langage_lbl['LBL_UPLOAD'];?> &nbsp;&nbsp;&nbsp; <?php  echo $vName ?>&nbsp;&nbsp;&nbsp; <?=$langage_lbl['LBL_PHOTO']; ?></span>
                            <span class="fileupload-exists"><?=$langage_lbl['LBL_CHANGE']; ?></span>
                            <input type="file" name="driver_doc" /></span>
                        <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><?=$langage_lbl['LBL_REMOVE_TEXT']; ?></a>
                    </div>
                </div>
            </div>
        </div>
        <?php  if($db_user[0]['ex_status']=='yes' || $db_user1[0]['ex_status']=='yes') { ?>
        <h5><b><?=$langage_lbl['LBL_EXP_DATE']; ?></b></h5>
        <div class="col-lg-13 exp-date">
            <div class="input-group input-append date" id="dp123" data-date="" data-date-format="yyyy-mm-dd">
                <input class="form-control" type="text" name="dLicenceExp" value="<?php  if($db_user[0]['ex_date'] == ''){echo '0000-00-00';}else{ echo $db_user[0]['ex_date'];}?>" readonly="" />
                <span class="input-group-addon add-on"><i class="icon-calendar"></i></span>
            </div>
        </div>
        <?php  }  ?>
        <input type="submit" class="save" name="save" value="Save" style="margin: 15px 3px;padding: 12px 0;background: #ff7e00;color: #FFFFFF;font-size: 18px;width: 48%;border-radius: 3px;">
        <input type="button" class="cancel" data-dismiss="modal" name="cancel" value="Cancel" style="padding: 12px 0;background: #ff7e00;color: #FFFFFF;font-size: 18px;width: 48%;border-radius: 3px;">
    </form>
</div>
<script>
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