<?php 
	include_once('../common.php');
	
	if(!isset($generalobjAdmin)){
		require_once(TPATH_CLASS."class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	
	require_once(TPATH_CLASS."Imagecrop.class.php");
	
	$default_lang = $generalobj->get_default_lang();
	$id 		= isset($_REQUEST['id'])?$_REQUEST['id']:''; // iUniqueId
	$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
	$action 	= ($id != '')?'Edit':'Add';
	
	//$temp_gallery = $tconfig["tpanel_path"];
	$tbl_name 		= 'help_detail_categories';
	$script 		= 'help_detail_categories';
	
	//echo '<prE>'; print_R($_REQUEST); echo '</pre>';
	
	// fetch all lang from language_master table 
	$sql = "SELECT * FROM `language_master` ORDER BY `iDispOrder`";
	$db_master = $obj->MySQLSelect($sql);
	$count_all = count($db_master);
	//echo '<pre>'; print_R($db_master); echo '</pre>';
	
	
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
    $previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	
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
		
		if(!empty($id)){
          if(SITE_TYPE=='Demo')
          {
            header("Location:help_detail_categories_action.php?id=".$id.'&success=2');
            exit;
          }
          
      }
    
    
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
				$category_image=$_FILES['vImage']['name'];
				
				$vImage_name1 = str_replace(" ", "_", trim($category_image));
				$img_arr = explode(".", $vImage_name1);
				// $filename = $img_arr[0];
				$filename = mt_rand(11111, 99999);
				$fileextension = $img_arr[count($img_arr) - 1];
				
                $vImage = $category_image; 
				$vImgName;
				
				if($i == 0){
					$vImgName .= $filename.'.'.$fileextension;
					
				}else{
					
					$vImgName = $vImgName;
				}
				
				
				$folder= $tconfig['tsite_upload_images_panel']; 
				$suc=move_uploaded_file($_FILES['vImage']['tmp_name'], $folder.$vImgName);
               				
				
				
				$vTitle = 'vTitle_'.$db_master[$i]['vCode'];
				
				$query = $q ." `".$tbl_name."` SET 	
				`vTitle` = '".$$vTitle."',
				`vImage` = '".$vImgName."',
				`eStatus` = '".$eStatus."',
				`iUniqueId` = '".$iUniqueId."',
				`iDisplayOrder` = '".$iDisplayOrder."',
				`vCode` = '".$db_master[$i]['vCode']."'"
				.$where;
				$obj->sql_query($query); 
				
			}
		}		
		if ($action == "Add") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Help Detail Category Insert Successfully.';
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = 'Help Detail Category Updated Successfully.';
        }
		 //header("location:".$backlink);
		 header("Location:help_detail_categories.php?id=".$iUniqueId.'&success=1');
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
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	
	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin |Help Category <?=$action;?></title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
		
		<?php  include_once('global_files.php');?>
		<!-- On OFF switch -->
		<link href="../assets/css/jquery-ui.css" rel="stylesheet" />
		<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />	
		 <script type="text/javascript" language="javascript">	
    function getAllLanguageCode(){
	
	  var def_lang = '<?=$default_lang?>';
	  var def_lang_name = '<?=$def_lang_name?>';
      var getEnglishText = $('#vValue_'+def_lang).val();
	 // alert(getEnglishText);
      var error = false;
      var msg = '';
      
      if(getEnglishText==''){
          msg += '<div class="alert alert-danger fade in"><a href="#" class="close" data-dismiss="alert"><icon class="fa fa-close"></icon></a><strong>Please Enter '+def_lang_name+' Value</strong></div> <br>';
          error = true;
      }
      
      if(error==true){
              $('#errorMessage').html(msg);
              return false;
      }else{
        $('#imageIcon').show();
        $.ajax({
                url: "ajax_get_all_language_translate.php",
                type: "post",
                data: {'englishText':getEnglishText},
                dataType:'json',
                success:function(response){
				//alert(response);
                     $.each(response,function(name, Value){
                        $('#'+name).val(Value);
                     });
                     $('#imageIcon').hide();
                }
        });
      }
      
      
    }
    </script>
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
							<h2><?=$action;?> Help Category</h2>
							<a href="help_detail_categories.php" class="back_link">
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
									FAQ Category Updated successfully.
								</div><br/>
							<?php  } ?>
							 <?php  if ($success == 2) {?>
                 <div class="alert alert-danger alert-dismissable">
                      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                      "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                 </div><br/>
               <?php } ?>
							<form method="post" name="_help_detail_cat_form" id="_help_detail_cat_form"  action="" enctype="multipart/form-data">
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php  echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="help_detail_categories.php"/>
								
								<div class="row">
									<div class="col-lg-12">
										<label>Status</label>
									</div>
									<div class="col-lg-6">
										<div class="make-switch" data-on="success" data-off="warning">
											<input type="checkbox" name="eStatus" <?=($id != '' && $eStatus == 'Inactive')?'':'checked';?>/>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<label>Order</label>
									</div>
									<div class="col-lg-6">
										<?php 
											$temp = 1;
											$query1 = $obj->MySQLSelect("SELECT max(iDisplayOrder) as maxnumber FROM ".$tbl_name." WHERE vCode = '".$default_lang."' ORDER BY iDisplayOrder");
											$maxnum	= isset($query1[0]['maxnumber']) ? $query1[0]['maxnumber'] : 0;
											$dataArray = array();
											for ($i=1; $i <= $maxnum ; $i++) { 
												$dataArray[] = $i;
												$temp = $iDisplayOrder;
											}
										?>
										<input type="hidden" name="temp_order" id="temp_order" value="<?=$temp?>">
										<select name="iDisplayOrder" class="form-control">
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
											$vValue = 'vValue_'.$vCode;
											
											
											$required = ($eDefault == 'Yes')?'required':'';
											$required_msg = ($eDefault == 'Yes')?'<span class="red"> *</span>':'';
										?>
										<div class="row">
											<div class="col-lg-12">
												<label><?=$vTitleLn;?> Language<?=$required_msg;?></label>
											</div>
											<div class="col-lg-6">
												<input type="text" class="form-control" name="<?=$vTitle;?>"  id="<?=$vValue;?>" value="<?=$$vTitle;?>" placeholder="Help Detail Category" <?=$required;?>>
											</div>
											 <?php  
										  if($vCode== $default_lang  && count($db_master) > 1){
										  ?>
																<div class="col-lg-6">
																	<button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode();">Convert To All Language</button>
																</div>
															
										  <?php 
										  }
										  ?>
										</div>
										<?php  } 
									} ?>
									<div class="row faq-but">
										<div class="col-lg-12">							
											<input type="submit" class="btn btn-default" name="submit" id="submit" value="<?=$action;?> Help Topic Category">
											<input type="reset" value="Reset" class="btn btn-default">
											<!-- <a href="javascript:void(0);" onclick="reset_form('_faq_cat_form');" class="btn btn-default">Reset</a> -->
                                            <a href="help_detail_categories.php" class="btn btn-default back_link">Cancel</a>
										</div>
									</div>
							</form>
						</div>
					</div>
                    <div class="clear"></div>
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>
		<!--END MAIN WRAPPER -->
		 <div class="row loding-action" id="imageIcon" style="display:none;">
                  <div align="center">                                                                       
                    <img src="default.gif">                                                              
                    <span>Language Translation is in Process. Please Wait...</span>                       
                  </div>                                                                                 
                </div>
		
		
		<?php  include_once('footer.php');?>
		<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
		<script type="text/javascript" language="javascript">
    $(document).ready(function(){
    
        $('#imageIcon').hide();
        
      
    });
</script>
		<script>
$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){ 
		referrer =  document.referrer;
        // alert(referrer);		
	}else { 
		referrer = $("#previousLink").val();
	}
	if(referrer == "") {
		referrer = "help_detail_categories.php";
	}else { 
		$("#backlink").val(referrer);		
	}
	$(".back_link").attr('href',referrer); 	
});
</script>
	</body>
	<!-- END BODY-->    
</html>