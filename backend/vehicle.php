<?php 
include_once('common.php');
//echo $url = $_SERVER['HTTP_REFERER'];exit;
$generalobj->check_member_login();

require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();
$script="Vehicle";
$abc = 'admin,driver,company';
$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$generalobj->setRole($abc, $url);
//$generalobj->cehckrole();
$success = isset($_GET['success']) ? $_GET['success'] :'';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : ''; // delete
$id = isset($_GET['id']) ? $_GET['id'] : ''; // delete
$driverid = isset($_GET['driverid']) ? $_GET['driverid'] : ''; // delete
$error = isset($_GET['success']) && $_GET['success']==0 ? 1 : ''; // delete
$var_msg = isset($_REQUEST['var_msg']) ? $_REQUEST['var_msg'] : ''; // delete
$tbl_name = 'driver_vehicle';

if (isset($_POST['Submit'])) {
	
		$iVehicleId = isset($_REQUEST['iVehicleId1']) ? $_REQUEST['iVehicleId1'] : '';		
		$doc_name = $_POST['doc_name'];
		$doc_path = $_POST['doc_path2'];	   
	  
		$expDate=$_POST['dLicenceExp'];
		
		  $image =$_FILES['file']['name'];
		 	$image_object = $_FILES['file']['tmp_name'];
			$masterid= $_POST['doc_id'];
					
			if($expDate != ""){

        $sql = "select ex_date from document_list where doc_userid='".$iVehicleId."' and doc_masterid='".$masterid."'"; 
		    $query = $obj->MySQLSelect($sql);
		    $fetch = $query[0];
              
			  // if($fetch['ex_date'] != "0000-00-00"){
						
          if($fetch['ex_date'] != $expDate || $image_name == ""){    
               $sql="UPDATE `document_list` SET  ex_date='".$expDate."' WHERE doc_userid='".$iVehicleId."' and doc_masterid='".$masterid."'";
	           $query= $obj->sql_query($sql);                 
          } else {
           if ($image_name == "") {
               $var_msg = $langage_lbl['LBL_DOC_UPLOAD_ERROR_'];
               header("location:vehicle.php?success=0&id=" . $iVehicleId . "&var_msg=" . $var_msg);
               exit();
           }

                // }
             } 
         }
			
if($image != ''){
			
			$Photo_Gallery_folder = $doc_path . '/' .$iVehicleId. '/';
		    if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
			//$img = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_documnet_size1"], $tconfig["tsite_upload_documnet_size2"], '', '', '', '', 'Y', '', $Photo_Gallery_folder);
      $vFile = $generalobj->fileupload($Photo_Gallery_folder, $image_object, $image, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
      $vImage = $vFile[0];
      if($vFile[2] == "1") {
        $var_msg = $vFile[1];
        header("location:vehicle.php?success=0&var_msg=" . $var_msg);
      } else {
        $var_msg = $langage_lbl['LBL_UPLOAD_MSG'];
      } 
      $tbl = 'document_list';
      $sql = "select doc_id from  ".$tbl."  where doc_userid='".$iVehicleId."' and doc_usertype='car'  and doc_masterid='".$masterid."'" ;
      $db_data = $obj->MySQLSelect($sql);
				
		  if (count($db_data) > 0) {
			$query="UPDATE `".$tbl."` SET `doc_file`='".$vImage."' WHERE doc_userid='".$iVehicleId."' and doc_usertype='car'  and doc_masterid='".$masterid."'";
             $obj->sql_query($query);
			}
             else {
			 $query =" INSERT INTO `".$tbl."` ( `doc_masterid`, `doc_usertype`, `doc_userid`,`ex_date`,`doc_file`, `status`, `edate`) "
                    . "VALUES "
                    . "( '".$masterid."', 'car', '".$iVehicleId."','".$expDate."','".$vImage."', 'Inactive', CURRENT_TIMESTAMP)";
						$obj->sql_query($query);
		   }

		}
		
	}

$dri_ssql = "";
if (SITE_TYPE == 'Demo') {
    $dri_ssql = " And rd.tRegistrationDate > '" . WEEK_DATE . "'";
}
if ($_SESSION['sess_user'] == 'driver') {
     $sql = "select iCompanyId from `register_driver` where iDriverId = '" . $_SESSION['sess_iUserId'] . "'";
     $db_usr = $obj->MySQLSelect($sql);
     $iCompanyId = $db_usr[0]['iCompanyId'];
	 
     // $sql = "SELECT * FROM " . $tbl_name . " where iCompanyId = '" . $iCompanyId . "' and iDriverId = '" . $_SESSION['sess_iUserId'] . "' and eStatus != 'Deleted'";
     // $db_driver_vehicle = $obj->MySQLSelect($sql);

      if($APP_TYPE == 'UberX'){
        $sql = "SELECT * FROM " . $tbl_name . " dv  where iCompanyId = '" . $iCompanyId . "' and dv.iDriverId = '" . $_SESSION['sess_iUserId'] . "' and dv.eStatus != 'Deleted' ORDER BY dv.iDriverVehicleId DESC";
		    $db_driver_vehicle = $obj->MySQLSelect($sql);

      }else{

         $sql = "SELECT dv.*,rd.vCountry,m.vTitle, mk.vMake,dv.vLicencePlate,dv.eStatus  FROM " . $tbl_name . " dv  JOIN model m ON dv.iModelId=m.iModelId JOIN make mk ON  dv.iMakeId=mk.iMakeId JOIN register_driver as rd ON rd.iDriverId = dv.iDriverId where dv.iCompanyId = '" . $iCompanyId . "' and dv.iDriverId = '" . $_SESSION['sess_iUserId'] . "' and dv.eStatus != 'Deleted' $dri_ssql ORDER BY dv.iDriverVehicleId DESC";
		$db_driver_vehicle = $obj->MySQLSelect($sql);
      }

     //echo "<pre>";print_r($db_driver_vehicle);exit;
}
if ($_SESSION['sess_user'] == 'company') {
     $iCompanyId = $_SESSION['sess_iUserId'];
	 // $sql = "SELECT * FROM " . $tbl_name . " where iCompanyId = '" . $iCompanyId . "' and eStatus != 'Deleted'";
     // $db_driver_vehicle = $obj->MySQLSelect($sql);

      if($APP_TYPE == 'UberX'){
        $sql = "SELECT * FROM " . $tbl_name . " dv  where iCompanyId = '" . $iCompanyId . "'and dv.eStatus != 'Deleted' ORDER BY dv.iDriverVehicleId DESC";
     
     $db_driver_vehicle = $obj->MySQLSelect($sql);

      }else{
         $sql = "SELECT dv.*,rd.vCountry,CONCAT(rd.vName,' ',rd.vLastName) AS driverName,m.vTitle, mk.vMake,dv.vLicencePlate,dv.eStatus  FROM " . $tbl_name . " dv  JOIN model m ON dv.iModelId=m.iModelId JOIN make mk ON  dv.iMakeId=mk.iMakeId JOIN register_driver as rd ON rd.iDriverId = dv.iDriverId where dv.iCompanyId = '" . $iCompanyId . "' and dv.eStatus != 'Deleted' $dri_ssql ORDER BY dv.iDriverVehicleId DESC";
       $db_driver_vehicle = $obj->MySQLSelect($sql);

      }
	//echo "<pre>";print_r($db_driver_vehicle);exit;
}

// $sql = "select iDriverVehicleId from driver_vehicle where iDriverId = '".$_SESSION['sess_iUserId']."'";
// $iDriverVehicleId = $obj->MySQLSelect($sql);
// $iDriverVehicleId=$iDriverVehicleId[0]['iDriverVehicleId'];

if(isset($iDriverVehicleId)){
$sql = "select * from register_driver where iDriverVehicleId = '".$iDriverVehicleId. "'";
$db_data = $obj->MySQLSelect($sql);
}

if ($action == 'delete') {
     // to check user is valid or not to delete vehicle

      // if(SITE_TYPE == 'Demo')
     // {
           // header("Location:vehicle.php?success=2");
           // exit;

     // }
     $valid_user = false;
     foreach ($db_driver_vehicle as $val) {
          if ($val['iDriverVehicleId'] == $id)
               $valid_user = true;
     }
     if (!$valid_user) {
          $var_msg = $langage_lbl['LBL_VEHICLE_DELETE_ERROR_MSG'];
          header("Location:vehicle.php?success=0&var_msg=". $var_msg);
     } else {

          $sql = "select count(*) as trip_cnt from trips where iDriverVehicleId = '" . $id . "' AND  iActive IN ('Active', 'On Going Trip')";
          $db_usr = $obj->MySQLSelect($sql);

          $sql1 = "SELECT count(iDriverId) as drivers FROM register_driver WHERE iDriverId = '".$driverid."' AND iDriverVehicleId = '".$id."'";
          $db_driver_data = $obj->MySQLSelect($sql1);
          if (count($db_usr) > 0 && $db_usr[0]['trip_cnt'] > 0) {
               $varmsg = $langage_lbl['LBL_TRIP_VEHICLE_DELETE_ERROR_MSG'];
               header("Location:vehicle.php?success=0&var_msg=". $varmsg);
               exit;
          } elseif(count($db_driver_data) > 0 && $db_driver_data[0]['drivers'] > 0){
               $varmsg = $langage_lbl['LBL_VEHICLE_DELETE_ERROR_MSG'];
               header("Location:vehicle.php?success=0&var_msg=". $varmsg);
               exit;
          } else {
                /*$sql= "SELECT * FROM register_driver WHERE iDriverId = '".$driverid."' AND iDriverVehicleId = '".$id."'";
                $avail_driver = $obj->MySQLSelect($sql);

                if(!empty($avail_driver)) {
                    $query = "UPDATE register_driver SET vAvailability = 'Not Avilable', `iDriverVehicleId`= '0' WHERE iDriverId = '".$driverid."' AND iDriverVehicleId = '" . $id . "'";
                    $obj->sql_query($query);
                }*/

               $query = "UPDATE `driver_vehicle` SET eStatus = 'Deleted' WHERE iDriverVehicleId = '" . $id . "'";
               $obj->sql_query($query);

               $var_msg = $langage_lbl['LBL_DELETE_VEHICLE'];
               header("Location:vehicle.php?success=1&var_msg=". $var_msg);
               exit;
          }
     }
}

for ($i = 0; $i < count($db_driver_vehicle); $i++) {
    $sql = "select vMake from make where iMakeId = '" . $db_driver_vehicle[$i]['iMakeId'] . "' where vMake !=''";
     $name1 = $obj->MySQLSelect($sql);
     $sql = "select vTitle from model where iModelId = '" . $db_driver_vehicle[$i]['iModelId'] . "' WHERE vTitle !=''";
     $name2 = $obj->MySQLSelect($sql);
     $db_msk[$i] = $name1[0]['vMake'] . ' ' . $name2[0]['vTitle'];
}

?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$SITE_NAME?> | <?=$langage_lbl['LBL_VEHICLES']; ?></title>
    <!-- Default Top Script and css -->
    <?php  include_once("top/top_script.php");?>
    <link rel="stylesheet" href="assets/css/bootstrap-fileupload.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/vehicles.css">
    <style>
        .fileupload-preview  { line-height:150px;}
    </style>
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
          
                <h2 class="header-page add-car-vehicle"><?=$langage_lbl['LBL_VEHICLES']; ?>
                  <?php  if($APP_TYPE != 'UberX'){ ?>
                    <a href="vehicle-add"><?=$langage_lbl['LBL_ADD_YOUR_CAR']; ?></a><?php  } ?>
                </h2>
                
                
              <?php  
                  if(SITE_TYPE =='Demo'){
              ?>
              <div class="demo-warning">
                <p><?=$langage_lbl['LBL_SINCE_THIS']; ?></p>
                </div>
              <?php 
                }
              ?>
              
          <!-- driver vehicles page -->
            <div class="driver-vehicles-page-new">
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
                        <?=$langage_lbl['LBL_VEHICLE_EDIT_DELETE_RECORD']; ?>
                    </div>
                </div>
            <?php 
                }
            ?>
                <div class="vehicles-page">
                    <div class="accordion">
					
                        <?php 
                            if (count($db_driver_vehicle) > 0) {
                                for ($i = 0; $i < count($db_driver_vehicle); $i++) {
                        ?>
                        <input type="hidden" name="iVehicleId" value = "<?php  echo $db_driver_vehicle[$i]['iDriverVehicleId']; ?>"/>
                            <div class="accordion-section">
                                <div class="accordionheading">
                                   <?php  if($APP_TYPE == 'UberX'){
                                      $displayname =  $db_driver_vehicle[$i]['vLicencePlate'];
                                    } else {
                                      $displayname =$db_driver_vehicle[$i]['vMake']."   ".$db_driver_vehicle[$i]['vTitle']."  ".$db_driver_vehicle[$i]['vLicencePlate']."  "  ;
                                      }?> 
                                    <h3><?php  echo $displayname  ;?></h3>
                                    <span> 
                                        <b>
                                          <?php  
                                          $class_name = ($db_driver_vehicle[$i]['eStatus'] == "Active")? 'badge success-vehicle-active': 'badge success-vehicle-inactive';
                                          ?>
                                           
                                      <span class="<?php  echo $class_name; ?>">
                                         <i class="<?= ($db_driver_vehicle[$i]['eStatus'] == "Active") ? 'icon-eye-open' : 'icon-eye-close' ?>"></i> <?= ucfirst($db_driver_vehicle[$i]['eStatus']); ?>
                                      </span>
                                            <a href ="vehicle_add_form.php?id=<?=base64_encode(base64_encode($db_driver_vehicle[$i]['iDriverVehicleId'])) ?>" class="active"><?=$langage_lbl['LBL_VEHICLE_EDIT']; ?></a>
                                            <?php  if($APP_TYPE != 'UberX'){?> 
                                            <a class="active active2" onClick="confirm_delete('<?= $db_driver_vehicle[$i]['iDriverVehicleId'] ?>','<?= $db_driver_vehicle[$i]['iDriverId'] ?>');" href="javascript:void(0);"><?=$langage_lbl['LBL_DELETE']; ?></a><?php  } ?>

                                        </b>
                                        <?php                       
                                        $sql1= "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, dl.doc_masterid masterid_list ,dl.ex_date,dl.doc_file , dl.status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" .$db_driver_vehicle[$i]['iDriverVehicleId']."') dl on dl.doc_masterid=dm.doc_masterid where dm.doc_usertype='car' and dm.status='Active' and (dm.country ='".$db_driver_vehicle[$i]['vCountry']."' OR dm.country ='All')";
                                      $db_userdoc = $obj->MySQLSelect($sql1);
                                      if($APP_TYPE != 'UberX' && !empty($db_userdoc)){?> 
                                        <strong><a class="accordion-section-title" href="#accordion-<?php  echo $i;?>">&nbsp;</a></strong> 

                                        <?php  } ?>

                                        </span>
                                        <?php  if ($_SESSION['sess_user'] == 'company') { ?>
                                        <div style=" clear: both;margin: 8px 0 0 10px;font-size: 10px;"><?php  echo $langage_lbl['LBL_DRIVER_NAME_ADMIN'];?> :<?php  echo $generalobj->clearName($db_driver_vehicle[$i]['driverName']); ?></div>
                                        <?php  } ?>
                                </div>
                <div id="accordion-<?php  echo $i;?>" class="accordion-section-content">
                    <div class="driver-vehicles-page-new">
                        <h2><?=$langage_lbl['LBL_DOCUMENTS']; ?></h2>
                                                                        
										<ul>
											<?php 
											for ($s = 0; $s < count($db_userdoc); $s++) { 
											?>
										
                    <li>
                    <form id="<?= $s ?>" class="upload_docform" method="post" action="" enctype="multipart/form-data">
									  <input type="hidden" name="iVehicleId1" value = "<?php  echo $db_driver_vehicle[$i]['iDriverVehicleId']; ?>"/>
									  <input type="hidden" name="doc_name" value="<?php  echo $db_userdoc[$s]['doc_name']; ?>">
										<input type="hidden" name="doc_id" value="<?php  echo $db_userdoc[$s]['masterid']; ?>">
										<input type="hidden" name="doc_path2" value="<?php  echo $tconfig["tsite_upload_vehicle_doc"];?>">  
                    <h4><?php  echo $db_userdoc[$s]['doc_name']; ?></h4>
                    <div class="fileupload fileupload-new" data-provides="fileupload">
                    <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px; ">
													 <?php  if ($db_userdoc[$s]['doc_file'] != '') { ?>
															<?php 
															$file_ext = $generalobj->file_ext($db_userdoc[$s]['doc_file']);
															if ($file_ext == 'is_image') {
																?>
																<img src = "<?= $tconfig["tsite_upload_vehicle_doc_panel"] . '/' .$db_driver_vehicle[$i]['iDriverVehicleId']. '/' . $db_userdoc[$s]['doc_file']; ?>" style="width:200px;cursor:pointer;" alt ="<?php  echo $db_userdoc[$s]['doc_name'];?>" data-toggle="modal" data-target="#myModallicence"/>
															<?php  } else { ?>
																<a href="<?= $tconfig["tsite_upload_vehicle_doc_panel"] . '/' .$db_driver_vehicle[$i]['iDriverVehicleId'].'/' . $db_userdoc[$s]['doc_file']; ?>" target="_blank"><?php  echo $db_userdoc[$s]['doc_name']; ?></a>
															<?php  } ?>
															<?php 
														} else {
															echo $db_userdoc[$s]['doc_name'] . ' not found';
														}
														?>
                            </b> 
                            </div><br>
													
													 <div class="select-image1">
                                <span class="btn btn-file btn-success">
                                <span class="fileupload-new"><?php  echo $db_userdoc[$s]['doc_name']; ?></span>
                                <input type="file"  name="file" <?php if($db_userdoc[$s]['doc_file'] == ""){?>required<?php }?> class="ins" accept="image/*,.doc, .docx,.pdf" onChange="validate_fileextension(<?php  echo $s;?>,this.value)"/>
                                </span>
                                <div class="fileerror error" style="font-weight: bold;"></div>
                                <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">X</a>
                            </div>
													<?php  
												
															if($db_userdoc[$s]['ex_status'] == 'yes'){?>
																 <div class="col-lg-13 exp-date">
																	<div class="input-group input-append date dp123">
																		<input class="form-control" type="text" name="dLicenceExp"  value="<?php  echo isset($db_userdoc[$s]['ex_date']) ? $db_userdoc[$s]['ex_date'] : ''; ?>" readonly="" required/>
																		<span class="input-group-addon add-on"><i class="icon-calendar"></i></span>
																	</div>
																</div>
														<?php  }?>
												 
                                                </div>
												  <abbr><input type="submit" name="Submit" class="save-document" value="<?=$langage_lbl['LBL_Save_Documents']; ?>"></abbr> 
                                           </form>
                                            </li>
										<?php  }?>
                                      </ul>
									  
                                    </div>
                                </div>
                                <!--end .accordion-section-content-->
                            </div>
                        <!--end .accordion-section-->
                        <?php 
                                }
                            }
                        ?>
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
    <script type="text/javascript" src="assets/js/accordion.js"></script>
    <script src="assets/plugins/jasny/js/bootstrap-fileupload.js"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="admin/css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
	<script type="text/javascript" src="admin/js/moment.min.js"></script>
	<script type="text/javascript" src="admin/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript">
        function confirm_delete(id, driverid)
        {
			//alert('sdf');
            var tsite_url = '<?php  echo $tconfig["tsite_url"]; ?>';
            if (id != '') {
                 var confirm_ans = confirm('<?= $langage_lbl['LBL_DELETE_VEHICLE_CONFIRM_MSG']?>');
                 if (confirm_ans == true) {
                      window.location.href = "vehicle.php?action=delete&id="+id+"&driverid="+ driverid;
                 }
                 }
            //document.getElementById(id).submit();
        }
		
		function del_veh_doc(id,type,img){
			ans=confirm('<?=$langage_lbl['LBL_CONFIRM_DELETE_DOC']?>');
			if(ans == true)
			{
				var request=$.ajax({
						type: "POST",
						url: "ajax_delete_docimage.php",
						data: "veh_id="+id+"&type="+type+"&img="+img+"&doc_type=veh_doc",
						success:function(data){
							var url      = window.location.href; 
							$("#"+type+"_"+id).load(url+" #"+type+"_"+id);
						}
					});
			}else{
				return false;
			}
		}
		
		$(function () {
			newDate = new Date('Y-M-D');
			$('.dp123').datetimepicker({
				format: 'YYYY-MM-DD',
				minDate: moment(),
				ignoreReadonly: true,
			});
		});

    function validate_fileextension(formid,filename) {
        var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp', 'doc' , 'docx' , 'pdf'];
        if ($.inArray(filename.split('.').pop().toLowerCase(), fileExtension) == -1) {
            $( "#"+formid+ " .fileerror" ).html( "Only formats are allowed : "+fileExtension.join(', ') );
            $('.save-document').prop("disabled", true);
            return false;
        } else {
            $('.save-document').prop("disabled", false);
            $( "#"+formid+ " .fileerror" ).html("");
        }
    } 
</script>
</body>
</html>

