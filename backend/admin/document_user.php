<?php 
include_once('../common.php');

require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();

if (!isset($generalobjAdmin)) {
     require_once(TPATH_CLASS . "class.general_admin.php");
     $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$ksuccess=isset($_REQUEST['ksuccess']) ? $_REQUEST['ksuccess'] : 0;
$action = ($id != '') ? 'Edit' : 'Add';

$tbl_name = 'administrators';
$script = 'Admin';

$sql1 = "SELECT * FROM admin_groups WHERE 1";
$db_group = $obj->MySQLSelect($sql1);

//echo '<prE>'; print_R($db_group); echo '</pre>';
// set all variables with either post (when submit) either blank (when insert)
$vFirstName = isset($_POST['vFirstName']) ? $_POST['vFirstName'] : '';
$vLastName = isset($_POST['vLastName']) ? $_POST['vLastName'] : '';
$vEmail = isset($_POST['vEmail']) ? $_POST['vEmail'] : '';
//$vUserName = isset($_POST['vEmail']) ? $_POST['vEmail'] : '';
$vPassword = isset($_POST['vPassword']) ? $_POST['vPassword'] : '';
$vContactNo = isset($_POST['vPhone']) ? $_POST['vPhone'] : '';
$eStatus = isset($_POST['eStatus']) ? $_POST['eStatus'] : '';
$iGroupId = isset($_POST['iGroupId']) ? $_POST['iGroupId'] : '';
$vPass = $generalobj->encrypt_bycrypt($vPassword);

if (isset($_POST['submit'])) {

     //echo '<pre>'; print_r($_POST); exit;
     //Start :: Upload Image Script
     //echo $id;exit;
     if(SITE_TYPE=='Demo')
     {

       header("Location:admin_action.php?id=" . $id . '&success=2');
       exit;
     }

    if($id != "") {
		$msg= $generalobj->checkDuplicateAdmin('iAdminId', 'administrators' , Array('vEmail'),$tconfig["tsite_url"]."/admin/admin_action.php?success=3&var_msg=Email already Exists", "Email already Exists",$id ,"");
	}else {
		$msg= $generalobj->checkDuplicateAdmin('vEmail', 'administrators' , Array('vEmail'),$tconfig["tsite_url"]."/admin/admin_action.php?success=3&var_msg=Email already Exists", "Email already Exists","" ,"");
	}
     if($msg == 1){
        if($id == ""){
           header("Location:admin_action.php?success=3");
           exit;
        }else{   
           header("Location:admin_action.php?id=" . $id . "&success=3");  
           exit;
        }
     }

     $q = "INSERT INTO ";
     $where = '';
     if ($action == 'Edit') {
          $str = ", eStatus = 'Inactive' ";
     } else {
          $str = '';
     }
     if ($id != '') {
          $q = "UPDATE ";
          $where = " WHERE `iAdminId` = '" . $id . "'";
     }


     $query = $q . " `" . $tbl_name . "` SET
		`vFirstName` = '" . $vFirstName . "',
		`vLastName` = '" . $vLastName . "',
		`vEmail` = '" . $vEmail . "',
		`vPassword` = '" . $vPass . "',
		`iGroupId` = '" . $iGroupId . "',
		`vContactNo` = '" . $vContactNo . "'
		 " . $where;
     //echo '<pre>'; print_r($id); exit;
     $obj->sql_query($query);

     $id = ($id != '') ? $id : $obj->GetInsertId();
     //echo"<pre>":print_r($id);exit;
     if($action=="Add")
     {
        $ksuccess="1";
      }
     else
     {
        $ksuccess="2";
     }
     header("Location:admin_action.php?id=" . $id . '&success=1 &ksuccess='.$ksuccess);
}
// for Edit

if ($action == 'Edit') {
     $sql = "SELECT * FROM " . $tbl_name . " WHERE iAdminId = '" . $id . "'";
     $db_data = $obj->MySQLSelect($sql);
     //echo "<pre>";print_R($db_data);echo "</pre>";
     $vPass = $generalobj->decrypt($db_data[0]['vPassword']);
     $vLabel = $id;
     if (count($db_data) > 0) {
          foreach ($db_data as $key => $value) {
               $vFirstName = $value['vFirstName'];

               $vLastName = $value['vLastName'];
               $vEmail = $generalobjAdmin->clearEmail($value['vEmail']);
              // $vUserName = $value['vUserName'];
               $vPassword = $value['vPassword'];
               $vContactNo = $value['vContactNo'];
			   $iGroupId = $value['iGroupId'];
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
          <title>Admin | <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> <?= $action; ?></title>
          <meta content="width=device-width, initial-scale=1.0" name="viewport" />
          <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
          <?php 
          include_once('global_files.php');
          ?>
          <!-- On OFF switch -->
          <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
          <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
     </head>
     <!-- END  HEAD-->
     <!-- BEGIN BODY-->
     <body class="padTop53 " >

          <!-- MAIN WRAPPER -->
          <div id="wrap">
               <?php 
               include_once('header.php');
               include_once('left_menu.php');
               ?>
               <!--PAGE CONTENT -->
               <div id="content">
                    <div class="inner">
                         <div class="row">
                              <div class="col-lg-12">

                                   <h2><?= $action; ?> Admin <?= $vFirstName; ?></h2>
                                   <a href="admin.php">
                                        <input type="button" value="Back to Listing" class="add-btn">
                                   </a>
                              </div>
                         </div>
                         <hr />
                         <div class="body-div">
                              <div class="form-group">
                                   <?php  if ($success == 1) {?>
                                   <div class="alert alert-success alert-dismissable">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                        <?php 
                                          if($ksuccess == "1")
                                          {?>
                                              Admin Insert Successfully.
                                          <?php  } else
                                          {?>
                                              Admin Updated Successfully.
                                          <?php  } ?>
                                        
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
                                        Email already Exists.
                                   </div><br/>
                                   <?php } ?>
                                   <form method="post" action="" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?= $id; ?>"/>
                                       <?php  if($id){?>

                                        <?php  }?>
                                         <div class="row">
                                    <div class="col-lg-12">
                                        <label>Document For<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <select  class="form-control" name = 'doc_type'   required>

                                            <option value="car" <?php  if ($doc_usertype == "car") echo 'selected="selected"'; ?> >Car</option>
                                            <option value="company"<?php  if ($doc_usertype == "company") echo 'selected="selected"'; ?>>company</option>
                                            <option value="driver"<?php  if ($doc_usertype == "driver") echo 'selected="selected"'; ?>>Driver</option>


                                        </select>
                                       </div>
                                       </div>   
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>First Name<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" pattern="[a-zA-Z]+" class="form-control" name="vFirstName"  id="vName" value="<?= $vFirstName; ?>" placeholder="First Name" required>
                                             </div>
                                        </div>
                                        
                                        
                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Last Name<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" pattern="[a-zA-Z]+" class="form-control" name="vLastName"  id="vLastName" value="<?= $generalobjAdmin->clearName(" ".$vLastName); ?>" placeholder="Last Name" required>
                                             </div>
                                        </div>

                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Email<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <!-- <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" class="form-control" name="vEmail" id="vEmail" value="<?= $vEmail; ?>" placeholder="Email" required <?php   if(!empty($_REQUEST['id'])){?> readonly="readonly" <?php  } ?>> -->
                                                  <input type="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" class="form-control" name="vEmail" id="vEmail" value="<?= $vEmail; ?>" placeholder="Email" required>
                                             </div><div id="emailCheck"></div>
                                        </div>
										<!--<div class="row">
                                             <div class="col-lg-12">
                                                  <label>User Name<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="text" class="form-control" name="vUserName"  value="<?= $vUserName; ?>" placeholder="User Name" required >
                                             </div><div id="emailCheck"></div>
                                        </div>-->

                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <label>Password<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <input type="password" pattern=".{6,}" class="form-control" name="vPassword"  id="vPassword" value="<?= $vPass ?>" placeholder="Password Label" title="Six or more characters" required >
                                             </div>
                                        </div>

										<div class="row">
                                             <div class="col-lg-12">
                                                  <label>Phone<span class="red"> *</span></label>
                                             </div>
                                             <div class="col-lg-6">

                                                  <input type="text" pattern="[0-9]{1,}" class="form-control" name="vPhone"  id="vContactNo" value="<?= $vContactNo; ?>" placeholder="Phone"  required >
                                             </div>
                                        </div>
										<?php  if($_SESSION['sess_iGroupId'] == 1) { ?>
										<div class="row">
                                             <div class="col-lg-12">
                                                  <label>Group<span class="red"> *</span><i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Admin Group has 3 types. 1) Super Administrator - He can manage whole admin panel. 2) Dispatcher Administrator - He can manage Manual Taxi Dispatch. 3) Billing Administrator - He can see rides and details of each ride.' ></i></label>
                                             </div>
                                             <div class="col-lg-6">
                                                  <select  class="form-control" name = 'iGroupId' required>
                                                       <option value="">--select--</option>
                                                       <?php  for ($i = 0; $i < count($db_group); $i++) { ?>
                                                       <option value = "<?= $db_group[$i]['iGroupId'] ?>" <?= ($db_group[$i]['iGroupId'] == $iGroupId) ? 'selected' : ''; ?>><?= $db_group[$i]['vGroup'] ?>
                                                       </option>
                                                       <?php  } ?>
                                                  </select>
                                             </div>
                                        </div>
										<?php  } ?>

                                        <div class="row">
                                             <div class="col-lg-12">
                                                  <input type="submit" class="save btn-info" name="submit" id="submit" value="<?= $action; ?> Admin" >
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


          <?php 
          include_once('footer.php');
          ?>
          <script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
		   
          <script>
			  $('[data-toggle="tooltip"]').tooltip();
               function changeCode(id)
               {
                    var request = $.ajax({
                         type: "POST",
                         url: 'change_code.php',
                         data: 'id=' + id,
                         success: function (data)
                         {
                              document.getElementById("code").value = data;
                              //window.location = 'profile.php';
                         }
                    });
               }
     		function validate_email(id)
               {

                    var request = $.ajax({
                         type: "POST",
                         url: 'validate_email.php',
                         data: 'id=' +id,
                         success: function (data)
                         {
     								if(data==0)
     								{
                              $('#emailCheck').html('<i class="icon icon-remove alert-danger alert">Already Exist,Select Another</i>');
     								 $('input[type="submit"]').attr('disabled','disabled');
     								}
     								else if(data==1)
     								{
     									var eml=/^[-.0-9a-zA-Z]+@[a-zA-z]+\.[a-zA-z]{2,3}$/;
     									result=eml.test(id);
     									if(result==true)
     									{
     									$('#emailCheck').html('<i class="icon icon-ok alert-success alert"> Valid</i>');
     									$('input[type="submit"]').removeAttr('disabled');
     									}
     									else
     									{
     										$('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> Enter Proper Email</i>');
     										 $('input[type="submit"]').attr('disabled','disabled');
     									}
     								}
                         }
                    });
               }
          </script>
     </body>
     <!-- END BODY-->
</html>
