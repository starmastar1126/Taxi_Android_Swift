<?php 
include_once("common.php");

$id = isset($_REQUEST['lang_id'])?$_REQUEST['lang_id']:'';
$from = isset($_REQUEST['from'])?$_REQUEST['from']:'';
if($from == 'other'){
	$tbl_name = 'language_label_other';
}else {
	$tbl_name = 'language_label';
}
$sql = "SELECT * FROM `language_master` ORDER BY `iDispOrder`";
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);


$sql = "SELECT vLabel FROM ".$tbl_name." WHERE LanguageLabelId = '".$id."'";
$db_data = $obj->MySQLSelect($sql);

$sql = "SELECT * FROM ".$tbl_name." WHERE vLabel = '".$db_data[0]['vLabel']."'";
$db_data = $obj->MySQLSelect($sql);

$vLabel = $db_data[0]['vLabel'];
if(count($db_data) > 0) {
	foreach($db_data as $key => $value) {
		$vValue = 'vValue_'.$value['vCode'];
		$$vValue = $value['vValue'];
	}
}
?>

<form method="post" name="_languages_form" id="_languages_form" action="javascript:void(0);">
	<input type="hidden" name="id" value="<?=$id;?>"/>
	<input type="hidden" name="from" value="<?=$from;?>"/>
	<div class="row">
		<div class="col-lg-12">
			<label>Language Label<?=($id != '')?'':'<span class="red"> *</span>';?></label>
		</div>
		<div class="col-lg-6">
			<input type="text" class="form-control" name="vLabel"  id="vLabel" value="<?=$vLabel;?>" placeholder="Language Label" <?=($id != '')?'disabled':'required';?>>
		</div>
	</div>

	<?php 
		if($count_all > 0) {
			for($i=0;$i<$count_all;$i++) {
				$vCode = $db_master[$i]['vCode'];
				$vTitle = $db_master[$i]['vTitle'];
				$eDefault = $db_master[$i]['eDefault'];

				$vValue = 'vValue_'.$vCode;

				$required = ($eDefault == 'Yes')?'required':'';
				$required_msg = ($eDefault == 'Yes')?'<span class="red"> *</span>':'';
			?>
			<div class="row">
				<div class="col-lg-12">
					<label><?=$vTitle;?> Value <?php  echo $required_msg; ?></label>
				</div>
				<div class="col-lg-6">
					<input type="text" class="form-control" name="<?=$vValue;?>" id="<?=$vValue;?>" value="<?=$$vValue;?>" placeholder="<?=$vTitle;?> Value" <?=$required;?>>
				</div>
		<?php  if($vCode=="EN"){ ?>
				<div class="col-lg-3">
					<button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode();">Convert To All Language</button>
				</div>
		<?php  } ?>
			</div>
		<?php  } } ?>
</form>

<div class="row loding-action" id="imageIcon" style="display:none;position:absolute; top: 10%;">
  <div align="center">
	<img src="<?php  echo $tconfig["tsite_url_main_admin"]?>default.gif">
	<span>Language Translation is in Process. Please Wait...</span>
  </div>
</div>

<script type="text/javascript" language="javascript">
	function getAllLanguageCode(){
		var getEnglishText = $('#vValue_EN').val();
		var error = false;
		var msg = '';
	  
		if(getEnglishText==''){
			msg += '<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert"><icon class="fa fa-close"></icon></a><strong>Please Enter English Value</strong></div> <br>';
			error = true;
		}
	  
		if(error==true){
			  $('#errorMessage').html(msg);
			  return false;
		}else{
			$('#imageIcon').show();
			$.ajax({
				url: "<?php  echo $tconfig["tsite_url_main_admin"]?>ajax_get_all_language_translate.php",
				type: "post",
				data: {'englishText':getEnglishText},
				dataType:'json',
				success:function(response){
					 $.each(response,function(name, Value){
						$('#'+name).val(Value);
					 });
					 $('#imageIcon').hide();
				}
			});
		}  
	}
</script>