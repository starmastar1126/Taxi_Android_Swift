<?php 
include_once('common.php');
//echo $url = $_SERVER['HTTP_REFERER'];exit;
$generalobj->check_member_login();

$script="Preferences";
$abc = 'admin,driver,company';
$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$generalobj->setRole($abc, $url);
//$generalobj->cehckrole();

$var_msg = isset($_REQUEST['var_msg']) ? $_REQUEST['var_msg'] : ''; 
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : ''; 


$tbl_name = 'preferences';
$tbl_name1 = 'driver_preferences';

$iDriverId = ($_SESSION['sess_user'] == 'driver') ? $_SESSION['sess_iUserId'] : ((isset($_REQUEST['id']) && $_REQUEST['id'] != "") ? $_REQUEST['id'] : "");
$driver_name = isset($_REQUEST['d_name']) ? $_REQUEST['d_name'] : ''; 

if (isset($_REQUEST['btnsubmit'])) {
	
		// echo "<pre>";print_r($_REQUEST);exit;
			
		if($SITE_VERSION == "v5"){
			$data_driver_pref = $generalobj->Update_User_Preferences($iDriverId,$_REQUEST);
			$var_msg=$langage_lbl['LBL_PREFERENCE_UPDATE_SUCCESS'];
			$redi_param="";
			if($_SESSION['sess_user'] != 'driver'){
				$redi_param = $iDriverId;
				$driver_name = $driver_name;
			}
			header("Location:preferences.php?success=1&var_msg=".$var_msg."&id=".$redi_param."&d_name=".$driver_name);
			exit;
		}else{
			$var_msg=$langage_lbl['LBL_FEATURE_NOT_AVAILABLE_MSG'];
			$redi_param="";
			if($_SESSION['sess_user'] != 'driver'){
				$redi_param = $iDriverId;
			}
			header("Location:preferences.php?success=2&var_msg=".$var_msg."&id=".$redi_param);
			exit;
		}
		
	}
// if ($_SESSION['sess_user'] == 'driver') {
     $sql="select * from $tbl_name where eStatus ='Active'";
	 $data_preference = $obj->MySQLSelect($sql);

	 $sql="select iPreferenceId,eType from $tbl_name1 where iDriverId = '$iDriverId'";
	 $data_driver_pref_edit = $obj->MySQLSelect($sql);
	 
// }


?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_VEHICLES']; ?></title>
	
	
    <!-- Default Top Script and css -->
    <?php  include_once("top/top_script.php");?>
    <!-- End: Default Top Script and css-->
</head>
<body>
     <!-- home page -->
    <div id="main-uber-page">
     <!-- Top Menu -->
    <!-- Left Menu -->
    <?php  include_once("top/left_menu.php");?>
    <!-- End: Left Menu-->
        <?php  include_once("top/header_topbar.php");?>
        <!-- End: Top Menu-->
        <!-- contact page-->
        <div class="page-contant">
		
            <div class="page-contant-inner">
               <?php 
                if ($error) {
            ?>
                <div class="row">
                    <div class="col-sm-12 alert alert-danger">
                         <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <?= $var_msg ?>
                    </div>
                </div>
            <?php  
                }
                if ($success==1) {
            ?>
                <div class="row">
                    <div class="alert alert-success paddiing-10">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <?= $var_msg ?>
                    </div>
                </div>
            <?php 
                }else if($success==2) {
            ?>
                <div class="row">
                    <div class="alert alert-danger paddiing-10">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                         <?= $var_msg ?>
                    </div>
                </div>
            <?php 
                }
            ?>
              <?php  
                  if(SITE_TYPE =='Demo'){
              ?>
              <div class="demo-warning">
                <p><?=$langage_lbl['LBL_SINCE_THIS']; ?></p>
                </div>
              <?php 
                }
				
				$label_text = ($_SESSION['sess_user'] == "driver") ? $langage_lbl['LBL_MANAGE_PREFERENCES_TXT'] : $langage_lbl['LBL_PREFERENCES_TEXT']." ".$langage_lbl['LBL_OF_TXT']." ".$driver_name;
				// $class_pref = ($_SESSION['sess_user'] == "driver") ? "pref_class" : "";
              ?>
			  
			  <h2 class="header-page add-car-vehicle"><?=$label_text?>
			  
			  <?php 
				$back_link = ($_SESSION['sess_user'] == "driver") ? "profile.php" : "driver_action.php?id=".$iDriverId."&action=edit";
			  ?>
			  <a href="<?=$back_link?>">
						<img src="assets/img/arrow-white.png" alt=""> <?=$langage_lbl['LBL_BACK_To_Listing']; ?>
					</a></h2>
				<div style="clear:both;"></div>
              
          <!-- driver vehicles page -->
            <div class="driver-vehicles-page-new">
				
                <div class="vehicles-page">
                    <div class="accordion">
							<span>
								<form name="frm1" action="" method="POST">
									<?php  foreach($data_preference as $value){?>
                                  
										<div class="preferences-chat">
											<b class="car-preferences-right-part"><?=$value['vName']?></b>
                                            
										  <b class="car-preferences-right-part-a">
                                          <span data-toggle="tooltip" title="<?=$value['vYes_Title']?>"><a href="#"><img class="borderClass" src="<?=$tconfig["tsite_upload_preference_image_panel"].$value['vPreferenceImage_Yes']?>" alt="" id="img_Yes_<?=$value['iPreferenceId']?>" onClick="checked_val('<?=$value['iPreferenceId']?>','Yes')"/></a></span></b>
										  <b class="car-preferences-right-part-a left_class"><span data-toggle="tooltip" title="<?=$value['vNo_Title']?>"><a href="#"><img class="borderClass" src="<?=$tconfig["tsite_upload_preference_image_panel"].$value['vPreferenceImage_No']?>" alt="" id="img_No_<?=$value['iPreferenceId']?>" onClick="checked_val('<?=$value['iPreferenceId']?>','No')"/></a></span></b>
										</div>
                                        
                                        
										<span style="display:none;">
											<input type="radio" name="vChecked_<?=$value['iPreferenceId']?>" id="Yes_<?=$value['iPreferenceId']?>" value="Yes">
											<input type="radio" name="vChecked_<?=$value['iPreferenceId']?>" id="No_<?=$value['iPreferenceId']?>" value="No">
										</span> 
									<?php }?>
									<p class="car-preferences-right-part-b">
                                        <input name="btnsubmit" type="submit" value="<?= $langage_lbl['LBL_Save']; ?>" class="save-but">
                                        
                                    </p>
                                    
								</form>
							</span>
					</div>
                </div>
            </div>
          <div style="clear:both;"></div>
        </div>
  </div>
 
    <!-- footer part -->
    <?php  include_once('footer/footer_home.php');?>
    <!-- footer part end -->
            <!-- End:contact page-->
            <div style="clear:both;"></div>
    </div>
    <!-- home page end-->
    <!-- Footer Script -->
    <?php  include_once('top/footer_script.php');?>
   
    <script type="text/javascript">
	
		 var successMSG1 = '<?php  echo $success; ?>';

		if (successMSG1 != '') {
			setTimeout(function () {
				$(".row").hide(1000)
			}, 5000);
		}
		
       function checked_val(id,value){
				 // alert("#img_"+value+"_"+id);
				$("#img_Yes_"+id).removeClass('border_class');
				$("#img_No_"+id).removeClass('border_class');
				
				$("#img_"+value+"_"+id).addClass('border_class');
				
				$("#Yes_"+id).prop("checked", false);
				$("#No_"+id).prop("checked", false);
				
				$("#"+value+"_"+id).prop("checked", true);
				return false;
			}
			
		
		 $(window).on("load",function(){	
			<?php  if(count($data_driver_pref_edit) > 0){ ?>
				var dataarr = '<?=json_encode($data_driver_pref_edit)?>';
				var arr1 = JSON.parse(dataarr);
				
				for(var i=0;i<arr1.length;i++){
					checked_val(arr1[i].iPreferenceId,arr1[i].eType)
				}
			<?php  } ?>
		}); 
		
		$(document).ready(function(){
			$('[data-toggle="tooltip"]').tooltip();
		});
	</script>
</body>
</html>