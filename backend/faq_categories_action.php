<?php 
include_once('common.php');
require_once(TPATH_CLASS."Imagecrop.class.php");

$default_lang = $generalobj->get_default_lang();
$id 		= isset($_REQUEST['id'])?$_REQUEST['id']:''; // iUniqueId
$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
$action 	= ($id != '')?'Edit':'Add';

//$temp_gallery = $tconfig["tpanel_path"];
$tbl_name 	= 'faq_categories';

//echo '<prE>'; print_R($_REQUEST); echo '</pre>';

// fetch all lang from language_master table 
$sql = "SELECT * FROM `language_master` ORDER BY `iDispOrder`";
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);
//echo '<pre>'; print_R($db_master); echo '</pre>';

// set all variables with either post (when submit) either blank (when insert)
$vImage 		= isset($_POST['vImage'])?$_POST['vImage']:'';
$eStatus_check 	= isset($_POST['eStatus'])?$_POST['eStatus']:'off';

$eStatus 		= ($eStatus_check == 'on')?'Active':'Inactive';
$thumb = new thumbnail();
/* to fetch max iDisplayOrder from table for insert */
$select_order	= $obj->MySQLSelect("SELECT MAX(iDisplayOrder) AS iDisplayOrder FROM ".$tbl_name." WHERE vCode = '".$default_lang."'");
$iDisplayOrder	= isset($select_order[0]['iDisplayOrder'])?$select_order[0]['iDisplayOrder']:0;
$iDisplayOrder	= $iDisplayOrder + 1; // Maximum order number

$iDisplayOrder	= isset($_POST['iDisplayOrder'])?$_POST['iDisplayOrder']:$iDisplayOrder;
$temp_order 	= isset($_POST['temp_order'])? $_POST['temp_order'] : "";

if($count_all > 0) {
	for($i=0;$i<$count_all;$i++) {
		$vTitle = 'vTitle_'.$db_master[$i]['vCode'];
		$$vTitle  = isset($_POST[$vTitle])?$_POST[$vTitle]:'';
	}
}

if(isset($_POST['submit'])) { //form submit
	
	if($temp_order > $iDisplayOrder) {
		for($i = $temp_order; $i >= $iDisplayOrder; $i--) { 
			$obj->sql_query("UPDATE ".$tbl_name." SET iDisplayOrder = ".($i+1)." WHERE iDisplayOrder = ".$i);
		}
	} else if($temp_order < $iDisplayOrder) {
		for($i = $temp_order; $i <= $iDisplayOrder; $i++) {
			$obj->sql_query("UPDATE ".$tbl_name." SET iDisplayOrder = ".($i-1)." WHERE iDisplayOrder = ".$i);
		}
	}
	
	$select_order		= $obj->MySQLSelect("SELECT MAX(iUniqueId) AS iUniqueId FROM ".$tbl_name." WHERE vCode = '".$default_lang."'");
	$iUniqueId			= isset($select_order[0]['iUniqueId'])?$select_order[0]['iUniqueId']:0;
	$iUniqueId			= $iUniqueId + 1; // Maximum order number
	
	if($count_all > 0) {
		for($i=0;$i<$count_all;$i++) {
			
			$q = "INSERT INTO ";
			$where = '';
			
			if($id != '' ){ 
				$q = "UPDATE ";
				$where = " WHERE `iUniqueId` = '".$id."' AND vCode = '".$db_master[$i]['vCode']."'";
				$iUniqueId = $id;
			}
		$image_object = $_FILES['vImage']['tmp_name'];  
        $image_name   = $_FILES['vImage']['name'];
 
  if($image_name != ""){
          $filecheck = basename($_FILES['vImage']['name']);                            
          $fileextarr = explode(".",$filecheck);
  		  $ext=strtolower($fileextarr[count($fileextarr)-1]);
          $flag_error = 0;
          if($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp"){
            $flag_error = 1;
            $var_msg = "Not valid image extension of .jpg, .jpeg, .gif, .png";
          }
          if($_FILES['vImage']['size'] > 1048576){
            $flag_error = 1;
            $var_msg = "Image Size is too Large";
          }
          if($flag_error == 1){
            $generalobj->getPostForm($_POST,$var_msg,$tconfig['tsite_url']."faq_categories_action&success=0");
            exit;
          }else{
            $Photo_Gallery_folder = $tconfig["tsite_upload_images_panel"].'/';
            if(!is_dir($Photo_Gallery_folder)){
                   	mkdir($Photo_Gallery_folder, 0777);
            }  
            $img = $generalobj->fileupload($Photo_Gallery_folder,$image_object,$image_name, '','jpg,png,gif,jpeg');
            $vImage = $img[0];
          }
  }
			$vTitle = 'vTitle_'.$db_master[$i]['vCode'];
			
			$query = $q ." `".$tbl_name."` SET 	
				`vTitle` = '".$$vTitle."',
				`vImage` = '".$vImage."',
				`eStatus` = '".$eStatus."',
				`iUniqueId` = '".$iUniqueId."',
				`iDisplayOrder` = '".$iDisplayOrder."',
				`vCode` = '".$db_master[$i]['vCode']."'"
				.$where;
			$obj->sql_query($query);
		}
	}
	header("Location:faq_categories_action.php?id=".$iUniqueId.'&success=1');
}

// for Edit
if($action == 'Edit') {
	$sql = "SELECT * FROM ".$tbl_name." WHERE iUniqueId = '".$id."'";
	$db_data = $obj->MySQLSelect($sql);	
	//echo '<pre>'; print_R($db_data); echo '</pre>'; 
	$iUniqueId = $id;
	if(count($db_data) > 0) {
		foreach($db_data as $key => $value) {
			$vTitle 			= 'vTitle_'.$value['vCode'];
			$$vTitle 			= $value['vTitle'];
			
			$eStatus 			= $value['eStatus'];
			$vImage 			= $value['vImage'];
			$iDisplayOrder 		= $value['iDisplayOrder'];
		}
	}
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>"> <!--<![endif]-->

<!-- BEGIN HEAD-->
<head>
	<meta charset="UTF-8" />
    <title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_CAR_FAQ_CAT_ADMIN']; ?> <?=$action;?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="keywords" />
	<meta content="" name="description" />
	<meta content="" name="author" />
    <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

    <?php  include_once('global_files.php');?>
	<!-- On OFF switch -->
	<link href="assets/css/jquery-ui.css" rel="stylesheet" />
	<link rel="stylesheet" href="assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />	
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
						<h2><?=$action;?> <?=$langage_lbl['LBL_CAR_FAQ_CAT_ADMIN']; ?></h2>
						<a href="faq_categories.php">
							<input type="button" value="Back to Listing" class="add-btn">
						</a>
					</div>
				</div>
				<hr />	
                <div class="body-div">
					<div class="form-group">
						<?php  if($success == 1) { ?>
						<div class="alert alert-success alert-dismissable">
							<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
							Record Updated successfully.
						</div><br/>
						<?php  } ?>
						<form method="post" action="" enctype="multipart/form-data">
							<input type="hidden" name="id" value="<?=$id;?>"/>
							<div class="row">
								<div class="col-lg-12">
									<label><?=$langage_lbl['LBL_IMAGE']; ?></label>
								</div>
								<div class="col-lg-6">
									<?php  if($vImage != '') { ?>
										<img src="<?=$tconfig['tsite_upload_images'].$vImage;?>" style="width:200px;height:100px;">
									<?php  } ?>
									<input type="file" name="vImage" id="vImage" value="<?=$vImage;?>"/>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<label><?=$langage_lbl['LBL_Status']; ?></label>
								</div>
								<div class="col-lg-6">
									<div class="make-switch" data-on="success" data-off="warning">
										<input type="checkbox" name="eStatus" <?=($id != '' && $eStatus == 'Inactive')?'':'checked';?>/>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-lg-12">
									<label><?=$langage_lbl['LBL_ORFER']; ?></label>
								</div>
								<div class="col-lg-6">
									<?php 
									$temp = 1;
									$dataArray = array();

									$query1 = "SELECT iDisplayOrder FROM ".$tbl_name." WHERE vCode = '".$default_lang."' ORDER BY iDisplayOrder";
									$data_order = $obj->MySQLSelect($query1);
									
									foreach($data_order as $value)
									{
										$dataArray[] = $value['iDisplayOrder'];
										$temp = $iDisplayOrder;
									}
									?>
									<input type="hidden" name="temp_order" id="temp_order" value="<?=$temp?>">
									<select name="iDisplayOrder" class="textbox">
										<?php  foreach($dataArray as $arr):?>
											<option <?= $arr == $temp ? ' selected="selected"' : '' ?> value="<?=$arr;?>" >
												-- <?= $arr ?> --
											</option>
										<?php  endforeach; ?>
										<?php if($action=="Add") {?>
											<option value="<?=$temp;?>" >
												-- <?= $temp ?> --
											</option>
										<?php  }?>
									</select>
								</div>
							</div>
							<?php  
							if($count_all > 0) {
								for($i=0;$i<$count_all;$i++) {
									$vCode = $db_master[$i]['vCode'];
									$vTitleLn = $db_master[$i]['vTitle'];
									$eDefault = $db_master[$i]['eDefault'];
									
									$vTitle = 'vTitle_'.$vCode;
									
									$required = ($eDefault == 'Yes')?'required':'';
									?>
									<div class="row">
										<div class="col-lg-12">
											<label><?=$vTitleLn;?><?=$langage_lbl['LBL_LABEL']; ?>  <i><?=ucfirst($required);?></i></label>
										</div>
										<div class="col-lg-6">
											<input type="text" class="form-control" name="<?=$vTitle;?>"  id="<?=$vTitle;?>" value="<?=$$vTitle;?>" placeholder="FAQ Category" <?=$required;?>>
										</div>
									</div>
								<?php  } 
							} ?>
							<div class="row">
								<div class="col-lg-12">							
									<input type="submit" class="save btn-info" name="submit" id="submit" value="<?=$action;?> FAQ Category">
								</div>
							</div>
						</form>
                	</div>
				</div>
            </div>
	   </div>
		<!--END PAGE CONTENT -->
    </div>
     <!--END MAIN WRAPPER -->
     

	<?php  include_once('footer.php');?>
	<script src="assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
</body>
	<!-- END BODY-->    
</html>