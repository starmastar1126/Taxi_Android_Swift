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
$message_print_id = $id;
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$action = ($id != '') ? 'Edit' : 'Add';

$tbl_name = 'document_master';
$script = 'Document Master';

$doc_usertype = isset($_POST['doc_type']) ? $_POST['doc_type'] : '';
$doc_country1 = isset($_POST['country']) ? $_POST['country'] : '';
$Document_type = isset($_POST['Document_type']) ? $_POST['Document_type'] : '';
$exp = isset($_POST['exp']) ? $_POST['exp'] : '';

//exit();


$vTitle_store = array();
$sql = "SELECT vCode,vTitle,eDefault FROM `language_master` where eStatus='Active' ORDER BY `iDispOrder`";
$db_master = $obj->MySQLSelect($sql);

$count_all = count($db_master);
if ($count_all > 0) {
    for ($i = 0; $i < $count_all; $i++) {
        $vValue = 'doc_name_' . $db_master[$i]['vCode'];
        array_push($vTitle_store, $vValue);
        $$vValue = isset($_POST[$vValue]) ? $_POST[$vValue] : '';
    }
}
//print_r($vTitle_store);exit; 
if (isset($_POST['btnsubmit'])) {

     $sql1 = "SELECT vCountry FROM country where iCountryId='".$doc_country1."'";
	 $data_contry = $obj->MySQLSelect($sql1);
     $doc_country=$data_contry[0]['vCountry'];

    if ($eFareType == "Fixed") {
        $ePickStatus = "Inactive";
        $eNightStatus = "Inactive";
    } else {
        $ePickStatus = $ePickStatus;
        $eNightStatus = $eNightStatus;
    }



    if ($eNightStatus == "Active") {
        if ($tNightStartTime > $tNightEndTime) {
            header("Location:vehicle_type_action.php?id=" . $id . "&success=4");
            exit;
        }
    }
    if (SITE_TYPE == 'Demo') {
        header("Location:vehicle_type_action.php?id=" . $id . "&success=2");
        exit;
    }

    for ($i = 0; $i < count($vTitle_store); $i++) {

        $vValue = 'doc_name_' . $db_master[$i]['vCode'];
        // echo $_POST[$vTitle_store[$i]] ; exit;
        $q = "INSERT INTO ";
        $where = '';
        if ($id != '') {

            $q = "UPDATE ";
            $where = " WHERE `doc_masterid` = '" . $id . "'";
        }


         $query = $q . " `" . $tbl_name . "` SET
				                 
				                 `doc_usertype` = '" . $doc_usertype . "',
                                 `doc_name` = '" . $Document_type . "' ,
                                 `country` = '" . $doc_country1 . "',
                                  `ex_status` = '".$exp."',   
				" . $vValue . " = '" . $_POST[$vTitle_store[$i]] . "'"
                . $where;
       
        $obj->sql_query($query);

        $id = ($id != '') ? $id : $obj->GetInsertId();
    }


	$_SESSION['success'] = '1';
	if($action == "Edit"){
		$msg = "Document updated successfully.";
	}else{
		$msg = "Document added successfully.";
	}
    $_SESSION['var_msg'] = $msg;   
    // $obj->sql_query($query);
    header("Location:document_master_list.php");
}

// for Edit
if ($action == 'Edit') {

   $sql = "SELECT * FROM " . $tbl_name . " WHERE doc_masterid = '" . $id . "'";
   $db_data = $obj->MySQLSelect($sql);
   

    $vLabel = $id;
    if (count($db_data) > 0) {
        for ($i = 0; $i < count($db_master); $i++) {

            foreach ($db_data as $key => $value) {
                $vValue = 'doc_name_' . $db_master[$i]['vCode'];
                $$vValue = $value[$vValue];
                $doc_usertype = $value['doc_usertype'];
                $doc_country = $value['country'];
                $doc_name = $value['doc_name'];
                $exp = $value['ex_status'];
            }
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
        <title>Admin | <?php  echo $langage_lbl_admin['LBL_DOCUMENT_TYPE']; ?> <?= $action; ?></title>
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
                            <h2> <?php  echo $langage_lbl_admin['LBL_DOCUMENT_TYPE']; ?> </h2>
                            <a href="document_master_list.php">
                                <input type="button" value="Back to Listing" class="add-btn">
                            </a>
                        </div>
                    </div>
                    <hr />
                    <div class="body-div">
                        <div class="form-group">
                            <?php  if ($success == 1) {?>
                            <div class="alert alert-success alert-dismissable msgs_hide">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
							<?= $langage_lbl_admin['LBL_DOCUMENT_TYPE']; ?> Updated successfully.
                            </div><br/>
                            <?php  } elseif ($success == 2) { ?>
                            <div class="alert alert-danger alert-dismissable ">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
                            </div><br/>
                            <?php  } elseif ($success == 3) { ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
							<?php  echo $_REQUEST['varmsg']; ?> 
                            </div><br/>	
                            <?php  } elseif ($success == 4) { ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                "Please Select Night Start Time less than Night End Time." 
                            </div><br/>	
                            <?php  } ?>
                            <?php  if($_REQUEST['var_msg'] !=Null) { ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                Record  Not Updated .
                            </div><br/>
                            <?php  } ?>		


                            <form id="vtype" method="post" action="" enctype="multipart/form-data" >
                                <input type="hidden" name="id" value="<?= $id; ?>"/>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Country <span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <select id="country"  class="form-control" name = 'country'  required >
                                              <option value="All">All Country</option>
											<?php  
												// country 
												$sql = "SELECT iCountryId,vCountry,vCountryCode FROM country ORDER BY iCountryId ASC";
												$db_data1= $obj->MySQLSelect($sql);
												foreach ($db_data1 as $value) { ?>
												<option <?php  if($db_data[0]['country'] == $value['vCountryCode']){ echo 'selected';}?> value="<?php  echo $value['vCountryCode']; ?>"><?php  echo $value['vCountry']; ?></option>
                                            <?php  }?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Document For <span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <select  class="form-control" name = 'doc_type'   required>
                                            <?php  if($APP_TYPE != "UberX") { ?>
                                            <option value="car" <?php  if ($doc_usertype == "car") echo 'selected="selected"'; ?> >Car</option>
                                            <?php  } ?>
                                            <option value="company"<?php  if ($doc_usertype == "company") echo 'selected="selected"'; ?>>Company</option>
                                            <option value="driver"<?php  if ($doc_usertype == "driver") echo 'selected="selected"'; ?>><?php  echo $langage_lbl_admin['LBL_RIDER_DRIVER_RIDE_DETAIL']?></option>


                                        </select>
                                    </div>
                                </div>
                                
                                <div class="row">

                                    <div class="col-lg-12">
                                        <label>Expire On Date <span class="red"> *</span> 
										<i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Yes option will ask for Date'></i>
                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="radio"  name="exp"  id="exp"  value="yes"  <?php  if ($exp == "yes") echo 'checked="checked"'; ?>  required > Yes
                                        <input type="radio"  name="exp"   id="exp" value="no"  <?php  if ($exp == "no") echo 'checked="checked"'; ?>  required > No
                                    </div>
                                </div>
								
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Document Name <span class="red"> *</span> 
                                            <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Name of Document for admin use. e.g. Insurance, Driving Licence... etc'></i>

                                        </label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" class="form-control" name="Document_type"  id="Docmaster"  value="<?= $doc_name; ?>"  required>
                                    </div>
                                </div>
								<?php 
								if ($count_all > 0) {
								    for ($i = 0; $i < $count_all; $i++) {
								        $vCode = $db_master[$i]['vCode'];
								        $vTitle = $db_master[$i]['vTitle'];
								        $eDefault = $db_master[$i]['eDefault'];
								
								        $vValue = 'doc_name_' . $vCode;
								
								        $required = ($eDefault == 'Yes') ? 'required' : '';
								        $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
								        
								        ?>
								        <div class="row">
                                            <div class="col-lg-12">
                                                <label><?php  echo $langage_lbl_admin['LBL_DOCUMENT_TYPE']; ?> (<?= $vTitle; ?>)<?php  echo $required_msg;?> 
												 <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Name of Document as per language. e.g. Insurance, Driving Licence... etc'></i>
												</label>

                                            </div>
                                            <div class="col-lg-6">
                                                <input type="text" class="form-control" name="<?= $vValue; ?>" id="<?= $vValue; ?>" value="<?= $$vValue; ?>" placeholder="<?= $vTitle; ?>Value" <?= $required; ?>>

                                            </div>
                                        </div>
                                        <?php  }
                                        } ?>



                                        <div class="row">
                                            <div class="col-lg-12">
                                                <input type="submit" class="save btn-info" name="btnsubmit" id="btnsubmit" value="<?= $action . " " . $langage_lbl_admin['LBL_DOCUMENT_TYPE']; ?>" >
                                            </div>
                                        </div>
                                    </form>
                                </div>

                            </div>
                            <div style="clear:both;"></div>
                        </div>

                    </div>

                    <!--END PAGE CONTENT -->
                </div>
                <!--END MAIN WRAPPER -->
				
                <?php  include_once('footer.php'); ?>
                <script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
                <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
                <script type="text/javascript" src="js/moment.min.js"></script>
                <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
                <!--For Faretype-->			
                <script>
                    $('[data-toggle="tooltip"]').tooltip();
                    window.onload = function () {
                        var vid = $("#vid").val();
                        var eFareType = $("#eFareType").val();
                        var AllowQty = $("#AllowQty").val();
                        if (vid == '') {
                            get_faretype('Regular');
                        } else {
                            get_faretype(eFareType);
                        }

                        if (AllowQty == 'Yes') {
                            $("#iMaxQty").show();
                            $("#iMaxQty").attr('required', 'required');
                        } else {
                            $("#iMaxQty").hide();
                            $("#iMaxQty").removeAttr('required');
                        }

                        var appTYpe = '<?php  echo $APP_TYPE; ?>';

                        if (appTYpe == 'UberX' && eFareType == 'Regular') {
                            $("#Regular_div2").show();
                            $("#Regular_div1").show();

                        } else if (appTYpe == 'Ride' || appTYpe == 'Delivery' || appTYpe == 'Ride-Delivery') {
                            $("#Regular_div2").show();
                            $("#Regular_div1").show();

                        } else {
                            $("#Regular_div2").hide();
                            $("#Regular_div1").show();

                        }
                    };
                    var successMSG1 = '<?php  echo $success; ?>';

                    if (successMSG1 != '') {
                        setTimeout(function () {
                            $(".msgs_hide").hide(1000)
                        }, 5000);
                    }

				function get_faretype(val)
				{
					var appTYpe = '<?php  echo $APP_TYPE; ?>';
					if (appTYpe == 'UberX') {

                    if (val == "Fixed")
                    {
                        $("#fixed_div").show();
                        $("#Regular_div1").hide();
                        $("#Regular_div2").hide();
                        $("#hide-priceHour").hide();
                        $("#hide-basefare").hide();
                        $("#hide-minimumfare").hide();
                        $("#hide-price").hide();
                        $("#hide-km").hide();
                        $("#show-in-fixed").show();
                        $("#fFixedFare").attr('required', 'required');
                        $("#iMaxQty").attr('required', 'required');
                        $("#fPricePerKM").removeAttr('required');
                        $("#fPricePerMin").removeAttr('required');
                        $("#iBaseFare").removeAttr('required');
                        $("#iPersonSize").removeAttr('required');
                        $("#fPickUpPrice").removeAttr('required');
                        $("#tPickStartTime").removeAttr('required');
                        $("#tPickEndTime").removeAttr('required');
                        $("#tNightStartTime").removeAttr('required');
                        $("#tNightEndTime").removeAttr('required');
                        $("#fPricePerHour").removeAttr('required');
                        $("#iMinFare").removeAttr('required');
                    } else if (val == "Regular") {
                        $("#fixed_div").hide();
                        $("#Regular_div2").show();
                        $("#Regular_div1").show();
                        $("#show-in-fixed").hide();
                        $("#hide-priceHour").hide();
                        $("#hide-km").show();
                        $("#hide-basefare").show();
                        $("#hide-minimumfare").show();
                        $("#hide-price").show();
                        $("#fPricePerHour").removeAttr('required');
                        $("#iMaxQty").removeAttr('required');
                        $("#fFixedFare").removeAttr('required');
                        $("#fPricePerKM").attr('required', 'required');
                        $("#iMinFare").attr('required', 'required');
                        $("#fPricePerMin").attr('required', 'required');
                        $("#iBaseFare").attr('required', 'required');
                        $("#iPersonSize").attr('required', 'required');
                        $("#fPickUpPrice").attr('required', 'required');
                        $("#tPickStartTime").attr('required', 'required');
                        $("#tPickEndTime").attr('required', 'required');
                        $("#tNightStartTime").attr('required', 'required');
                        $("#tNightEndTime").attr('required', 'required');
                    } else {
                        $("#fixed_div").hide();
                        $("#Regular_div1").show();
                        $("#Regular_div2").hide();
                        $("#hide-basefare").hide();
                        $("#hide-minimumfare").hide();
                        $("#hide-price").hide();
                        $("#hide-km").hide();
                        $("#hide-priceHour").show();
                        $("#show-in-fixed").hide();
                        $("#fFixedFare").removeAttr('required');
                        $("#iMaxQty").removeAttr('required');
                        $("#iMinFare").removeAttr('required');
                        $("#fPricePerHour").attr('required', 'required');
                        $("#iBaseFare").removeAttr('required');
                        $("#fPricePerKM").removeAttr('required');
                        $("#fPricePerMin").removeAttr('required');
                        $("#iPersonSize").removeAttr('required');
                        $("#fPickUpPrice").removeAttr('required');
                        $("#tPickStartTime").removeAttr('required');
                        $("#tPickEndTime").removeAttr('required');
                        $("#tNightStartTime").removeAttr('required');
                        $("#tNightEndTime").removeAttr('required');
                    }
                } else {
                    $("#Regular_div1").show();
                    $("#Regular_div2").show();
                    $("#fFixedFare").hide();
                    $("#show-in-fixed").hide();
                    $("#hide-priceHour").hide();
                    $("#fFixedFare").removeAttr('required');
                    $("#iMaxQty").removeAttr('required');
                    $("#fPricePerHour").removeAttr('required');
                    $("#fPricePerKM").attr('required', 'required');
                    $("#iMinFare").attr('required', 'required');
                    $("#fPricePerMin").attr('required', 'required');
                    $("#iBaseFare").attr('required', 'required');
                    $("#iPersonSize").attr('required', 'required');
                    $("#fPickUpPrice").attr('required', 'required');
                    $("#tPickStartTime").attr('required', 'required');
                    $("#tPickEndTime").attr('required', 'required');
                    $("#tNightStartTime").attr('required', 'required');
                    $("#tNightEndTime").attr('required', 'required');
                }
            }
            function get_AllowQty(val) {
                if (val == "Yes") {
                    $("#iMaxQty").show();
                    $("#iMaxQty").attr('required', 'required');
                } else {
                    $("#iMaxQty").hide();
                    $("#iMaxQty").removeAttr('required');
                }
            }
        </script>
        <script>
			function changeCode(id)
            {
                var request = $.ajax({
                    type: "POST",
                    url: 'change_code.php',
                    data: 'id=' + id,
                    success: function (data)
                    {
                        document.getElementById("code").value = data;
                    }
                });
            }
            function validate_email(id)
            {
                var request = $.ajax({
                    type: "POST",
                    url: 'validate_email.php',
                    data: 'id=' + id,
                    success: function (data)
                    {
                        if (data == 0)
                        {
                            $('#emailCheck').html('<i class="icon icon-remove alert-danger alert">Already Exist,Select Another</i>');
                            $('input[type="submit"]').attr('disabled', 'disabled');
                        } else if (data == 1)
                        {
                            var eml = /^[-.0-9a-zA-Z]+@[a-zA-z]+\.[a-zA-z]{2,3}$/;
                            result = eml.test(id);
                            if (result == true)
                            {
                                $('#emailCheck').html('<i class="icon icon-ok alert-success alert"> Valid</i>');
                                $('input[type="submit"]').removeAttr('disabled');
                            } else
                            {
                                $('#emailCheck').html('<i class="icon icon-remove alert-danger alert"> Enter Proper Email</i>');
                                $('input[type="submit"]').attr('disabled', 'disabled');
                            }
                        }
                    }
                });
            }
            function getpriceCheck(id)
            {
                if (id > 0)
                {
                    $('input[type="submit"]').removeAttr('disabled');
                } else
                {
                    $('#price').html('<i class="alert-danger alert"> You can not EnterAny price Zero or Letter</i>');
                    $('input[type="submit"]').attr('disabled', 'disabled');
                }
            }
            function onlydigit(id)
            {
                var digi = /^[1-9]{1}$/;
                result = digi.test(id);
                if (result == true)
                {
                    $('input[type="submit"]').removeAttr('disabled');
                } else
                {
                    $('#digit').html('<i class="alert-danger alert">Only Decimal Number less Than 10</i>');
                    $('input[type="submit"]').attr('disabled', 'disabled');
                }
            }
            $(function () {
                newDate = new Date('Y-M-D');
                $('#tMonPickStartTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                });
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tMonPickEndTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                })
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tTuePickStartTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                });
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tTuePickEndTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                })
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tWedPickStartTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                });
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tWedPickEndTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                })
            });


            $(function () {
                newDate = new Date('Y-M-D');
                $('#tThuPickStartTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                });
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tThuPickEndTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                })
            });


            $(function () {
                newDate = new Date('Y-M-D');
                $('#tFriPickStartTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                });
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tFriPickEndTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                })
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tSatPickStartTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                });
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tSatPickEndTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                })
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tSunPickStartTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                });
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tSunPickEndTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                })
            });
			
            $(function () {
                newDate = new Date('Y-M-D');
                $('#tNightStartTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                });
            });

            $(function () {
                newDate = new Date('Y-M-D');
                $('#tNightEndTime').datetimepicker({
                    format: 'HH:mm:ss',
                    //minDate: moment().format('l'),
                    ignoreReadonly: true,
                    //sideBySide: true,
                })
            });

            function showhidepickuptime() {
                if ($('input[name=ePickStatus]').is(':checked')) {
                    //alert('Checked');
                    $("#showpickuptime").show();
                } else {
                    //alert('Not checked');
                    $("#showpickuptime").hide();
                }
            }

            function showhidenighttime() {
                if ($('input[name=eNightStatus]').is(':checked')) {
                    //alert('Checked');
                    $("#shownighttime").show();
                } else {
                    //alert('Not checked');
                    $("#shownighttime").hide();
                }
            }
            showhidepickuptime();
            showhidenighttime();
        </script>

    </body>
    <!-- END BODY-->
</html>
