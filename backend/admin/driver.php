<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$script = 'Driver';

$iCompanyId = isset($_REQUEST['iCompanyId']) ? $_REQUEST['iCompanyId'] : '';

//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY rd.iDriverId DESC';
if($sortby == 1){
  if($order == 0)
  $ord = " ORDER BY rd.vName ASC";
  else
  $ord = " ORDER BY rd.vName DESC";
}
if($sortby == 2){
  if($order == 0)
  $ord = " ORDER BY c.vCompany ASC";
  else
  $ord = " ORDER BY c.vCompany DESC";
}
if($sortby == 3){
  if($order == 0)
  $ord = " ORDER BY rd.vEmail ASC";
  else
  $ord = " ORDER BY rd.vEmail DESC";
}

if($sortby == 4){
  if($order == 0)
  $ord = " ORDER BY rd.tRegistrationDate ASC";
  else
  $ord = " ORDER BY rd.tRegistrationDate DESC";
}

if($sortby == 5){
  if($order == 0)
  $ord = " ORDER BY rd.eStatus ASC";
  else
  $ord = " ORDER BY rd.eStatus DESC";
}

if($sortby == 6){
  if($order == 0)
  $ord = " ORDER BY `count` ASC";
  else
  $ord = " ORDER BY `count` DESC";
}
//End Sorting
$dri_ssql = "";
if (SITE_TYPE == 'Demo') {
    $dri_ssql = " And rd.tRegistrationDate > '" . WEEK_DATE . "'";
}



// Start Search Parameters
$option = isset($_REQUEST['option'])?stripslashes($_REQUEST['option']):"";
$keyword = isset($_REQUEST['keyword'])?stripslashes($_REQUEST['keyword']):"";
$searchDate = isset($_REQUEST['searchDate'])?$_REQUEST['searchDate']:"";
$eStatus = isset($_REQUEST['eStatus']) ? $_REQUEST['eStatus'] : "";
$action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');

//$_POST['action'] = (isset($_POST['action']) ? $_REQUEST['action'] : '');
//if(asset($_POST['action']))
if ($action == "addmoney") {
    $eUserType = $_REQUEST['eUserType'];
	 $iUserId = $_REQUEST['iDriverId'];   
    $iBalance = $_REQUEST['iBalance'];
    $eFor = $_REQUEST['eFor'];
    $eType = $_REQUEST['eType'];
    $iTripId = 0;
    $tDescription = '#LBL_AMOUNT_CREDIT#';  
    $ePaymentStatus = 'Unsettelled';
    $dDate = Date('Y-m-d H:i:s');
 
    $generalobj->InsertIntoUserWallet($iUserId, $eUserType, $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);	
	$_SESSION['success'] = '1';
    $_SESSION['var_msg'] = $langage_lbl_admin["LBL_DRIVER_TXT_ADMIN"].' in add balance successfully';   
  //header("Location:".$tconfig["tsite_url_main_admin"]."driver.php"); exit;
   //exit;
}

$ssql = '';
$cmp_name = "";
if($iCompanyId != "") {
	$ssql .= " AND rd.iCompanyId='".$iCompanyId."'";
	
	$sql="select vCompany from company where iCompanyId = '".$iCompanyId."'";
	$data_cmp1 = $obj->MySQLSelect($sql);
	
	$cmp_name = $data_cmp1[0]['vCompany'];
	$keyword = $cmp_name;
}

if($keyword != '') {
    if($option != '') {
        $option_new = $option;
        if($option == 'DriverName'){
          $option_new = "CONCAT(rd.vName,' ',rd.vLastName)";
        } 
        if($eStatus != ''){
            $ssql.= " AND ".stripslashes($option_new)." LIKE '%".$generalobjAdmin->clean($keyword)."%' AND rd.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
		    } else {
            $ssql.= " AND ".stripslashes($option_new)." LIKE '%".$generalobjAdmin->clean($keyword)."%'";
		    }
    } else {
      if($eStatus != ''){
        $ssql.= " AND (concat(rd.vName,' ',rd.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR c.vCompany LIKE '%".$generalobjAdmin->clean($keyword)."%' OR rd.vEmail LIKE '%".$generalobjAdmin->clean($keyword)."%' OR rd.vPhone LIKE '%".$generalobjAdmin->clean($keyword)."%') AND rd.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
      } else {
        $ssql.= " AND (concat(rd.vName,' ',rd.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR c.vCompany LIKE '%".$generalobjAdmin->clean($keyword)."%' OR rd.vEmail LIKE '%".$generalobjAdmin->clean($keyword)."%' OR rd.vPhone LIKE '%".$generalobjAdmin->clean($keyword)."%')";
      }
	}
} else if($eStatus != '' && $keyword == '') {
     $ssql.= " AND rd.eStatus = '".$generalobjAdmin->clean($eStatus)."'";
}


// End Search Parameters

//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
if($eStatus != ''){
  $eStatussql = "";
} else {
 $eStatussql = " AND rd.eStatus != 'Deleted'";
}
$sql = "SELECT COUNT(iDriverId) AS Total FROM register_driver rd LEFT JOIN company c ON rd.iCompanyId = c.iCompanyId WHERE 1 = 1  $eStatussql $ssql $dri_ssql";
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page);//total pages we going to have
$show_page = 1;

//-------------if page is setcheck------------------//
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             //it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    } else {
        // error - show first set of results
        $start = 0;
        $end = $per_page;
    }
} else {
    // if page isn't set, show first set of results
    $start = 0;
    $end = $per_page;
}
// display pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$tpages=$total_pages;
if ($page <= 0)
    $page = 1;
//Pagination End

// $sql = "select iDriverVehicleId from driver_vehicle WHERE iDriverId='".$data_drv[$i]['iDriverId']."' AND eStatus!='Deleted'";
// $dbVehicle = $obj->MySQLSelect($sql);
if(!empty($eStatus)) { 
	$eQuery = "";
} else {
  $eQuery = " AND rd.eStatus != 'Deleted'";
}
    
$sql = "SELECT rd.iDriverId,rd.vEmail,rd.tRegistrationDate,rd.vPhone,rd.eStatus,(SELECT count(dv.iDriverVehicleId) FROM driver_vehicle AS dv WHERE dv.iDriverId=rd.iDriverId AND dv.eStatus != 'Deleted' AND dv.iMakeId != 0 AND dv.iModelId != 0) AS `count`,CONCAT(rd.vName,' ',rd.vLastName) AS driverName, c.vCompany,c.eStatus as cmp_status FROM register_driver rd LEFT JOIN company c ON rd.iCompanyId = c.iCompanyId WHERE 1=1 $eQuery $ssql $dri_ssql $ord LIMIT $start, $per_page";

$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);

$sql1 = "SELECT doc_masterid as total FROM `document_master` WHERE `doc_usertype` ='driver' AND status = 'Active'";
$doc_count_query = $obj->MySQLSelect($sql1);
$doc_count = count($doc_count_query);


$var_filter = "";
foreach ($_REQUEST as $key=>$val)
{
    if($key != "tpages" && $key != 'page')
    $var_filter.= "&$key=".stripslashes($val);
}

$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages.$var_filter;
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->

<head>
    <meta charset="UTF-8" />
    <title>
        <?=$SITE_NAME?> |
        <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?>
    </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <?php  include_once('global_files.php');?>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->

<body class="padTop53">
    <!-- Main LOading -->
    <!-- MAIN WRAPPER -->
    <div id="wrap">
        <?php  include_once('header.php'); ?>
        <?php  include_once('left_menu.php'); ?>
        <!--PAGE CONTENT -->
        <div id="content">
            <div class="inner">
                <div id="add-hide-show-div">
                    <div class="row">
                        <div class="col-lg-12">
                            <?php 
									$company_name = ($cmp_name != "") ? " of ".$cmp_name : "";
								?>
                            <h2>
                                <?php  echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'].$company_name;?>
                            </h2>
                            <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                        </div>
                    </div>
                    <hr />
                </div>
                <?php  include('valid_msg.php'); ?>
                <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                    <input type="hidden" name="iDriverId" value="">
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                        <tbody>
                            <tr>
                                <td width="5%"><label for="textfield"><strong>Search:</strong></label></td>
                                <td width="10%" class="padding-right10"><select name="option" id="option" class="form-control">
                                        <option value="">All</option>
                                        <option value="DriverName" <?php  if ($option=="DriverName" ) { echo "selected"
                                            ; } ?> >
                                            <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> Name</option>
                                        <option value="c.vCompany" <?php  if ($option=="c.vCompany" || ($iCompanyId !=""
                                            && $cmp_name !="" )) { echo "selected" ; } ?> >Company Name</option>
                                        <option value="rd.vEmail" <?php  if ($option=='rd.vEmail' ) {echo "selected" ; }
                                            ?> >E-mail</option>
                                        <option value="rd.vPhone" <?php  if ($option=='rd.vPhone' ) {echo "selected" ; }
                                            ?> >Mobile</option>
                                        <!-- <option value="rd.eStatus" <?php  if ($option == 'rd.eStatus') {echo "selected"; } ?> >Status</option> -->

                                    </select>
                                </td>
                                <td width="15%" class="searchform"><input type="Text" id="keyword" name="keyword" value="<?php  echo $keyword; ?>"
                                        class="form-control" /></td>
                                <td width="12%" class="estatus_options" id="eStatus_options">
                                    <select name="eStatus" id="estatus_value" class="form-control">
                                        <option value="">Select Status</option>
                                        <option value='Active' <?php  if ($eStatus=='Active' ) { echo "selected" ; } ?>
                                            >Active</option>
                                        <option value="Inactive" <?php  if ($eStatus=='Inactive' ) {echo "selected" ; }
                                            ?> >Inactive</option>
                                        <option value="Deleted" <?php  if ($eStatus=='Deleted' ) {echo "selected" ; } ?>
                                            >Delete</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                                        title="Search" />
                                    <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='driver.php'" />
                                </td>
                                <td width="30%"><a class="add-btn" href="driver_action.php" style="text-align: center;">Add
                                        <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></a></td>
                            </tr>
                        </tbody>
                    </table>

                </form>
                <div class="table-list">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="admin-nir-export">
                                <div class="changeStatus col-lg-12 option-box-left">
                                    <span class="col-lg-2 new-select001">
                                        <select name="changeStatus" id="changeStatus" class="form-control" onChange="ChangeStatusAll(this.value);">
                                            <option value="">Select Action</option>
                                            <!--  <option value='Active' <?php  if ($option == 'Active') { echo "selected"; } ?> >Make Active</option> -->
                                            <option value="Inactive" <?php  if ($option=='Inactive' ) {echo "selected" ;
                                                } ?> >Make Inactive</option>
                                            <?php  if($eStatus != 'Deleted') { ?>
                                            <option value="Deleted" <?php  if ($option=='Delete' ) {echo "selected" ; }
                                                ?> >Make Delete</option>
                                            <?php  } ?>
                                        </select>
                                    </span>
                                </div>
                                <?php  if(!empty($data_drv)) {?>
                                <div class="panel-heading">
                                    <form name="_export_form" id="_export_form" method="post">
                                        <button type="button" onClick="showExportTypes('driver')">Export</button>
                                    </form>
                                </div>
                                <?php  } ?>
                            </div>
                            <div style="clear:both;"></div>
                            <div class="table-responsive">
                                <form class="_list_form" id="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th width="3%" class="align-center"><input type="checkbox" id="setAllCheck"></th>
                                                <th width="13%"><a href="javascript:void(0);" onClick="Redirect(1,<?php  if($sortby == '1'){ echo $order; }else { ?>0<?php  } ?>)">
                                                        <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> Name
                                                        <?php  if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc"
                                                            aria-hidden="true"></i>
                                                        <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i>
                                                        <?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i>
                                                        <?php  } ?></a></th>

                                                <th width="18%"><a href="javascript:void(0);" onClick="Redirect(2,<?php  if($sortby == '2'){ echo $order; }else { ?>0<?php  } ?>)">Company
                                                        Name
                                                        <?php  if ($sortby == 2) { if($order == 0) { ?><i class="fa fa-sort-amount-asc"
                                                            aria-hidden="true"></i>
                                                        <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i>
                                                        <?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i>
                                                        <?php  } ?></a></th>

                                                <th width="18%"><a href="javascript:void(0);" onClick="Redirect(3,<?php  if($sortby == '3'){ echo $order; }else { ?>0<?php  } ?>)">Email
                                                        <?php  if ($sortby == 3) { if($order == 0) { ?><i class="fa fa-sort-amount-asc"
                                                            aria-hidden="true"></i>
                                                        <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i>
                                                        <?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i>
                                                        <?php  } ?></a></th>
                                                <?php  if($APP_TYPE != "UberX"){?>
                                                <th width="12%" class="align-center"><a href="javascript:void(0);"
                                                        onClick="Redirect(6,<?php  if($sortby == '6'){ echo $order; }else { ?>0<?php  } ?>)">Taxi
                                                        Count
                                                        <?php  if ($sortby == 6) { if($order == 0) { ?><i class="fa fa-sort-amount-asc"
                                                            aria-hidden="true"></i>
                                                        <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i>
                                                        <?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i>
                                                        <?php  } ?></a></th>
                                                <?php  } ?>

                                                <th width="12%" class="align-left"><a href="javascript:void(0);"
                                                        onClick="Redirect(4,<?php  if($sortby == '4'){ echo $order; }else { ?>0<?php  } ?>)">Signup
                                                        Date
                                                        <?php  if ($sortby == 4) { if($order == 0) { ?><i class="fa fa-sort-amount-asc"
                                                            aria-hidden="true"></i>
                                                        <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i>
                                                        <?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i>
                                                        <?php  } ?></a></th>
                                                <th width="12%" class="align-Left">Mobile</th>
                                                <th class="align-Left">Wallet Balance</th>
                                                <?php  if($doc_count != 0) { ?>
                                                <th width="12%" class="align-center">View/Edit Document(s)</th>
                                                <?php  } ?>
                                                <?php  if($APP_TYPE == "UberX"){?>
                                                <th width="12%" class="align-center">Manage Services</th>
                                                <th width="12%" class="align-center">
                                                    <?php  echo "View/Edit ".$langage_lbl['LBL_AVAILABILITY'];?>
                                                </th>
                                                <?php  } ?>
                                                <th width="12%" class="align-center"><a href="javascript:void(0);"
                                                        onClick="Redirect(5,<?php  if($sortby == '5'){ echo $order; }else { ?>0<?php  } ?>)">Status
                                                        <?php  if ($sortby == 5) { if($order == 0) { ?><i class="fa fa-sort-amount-asc"
                                                            aria-hidden="true"></i>
                                                        <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i>
                                                        <?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i>
                                                        <?php  } ?></a></th>
                                                <th width="8%" class="align-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php  //echo"<pre>";print_r($data_drv);die;
                          if(!empty($data_drv)) {
													for ($i = 0; $i < count($data_drv); $i++) { 
														$status_cmp = ($data_drv[$i]['cmp_status'] == "Inactive") ? " (Inactive)" : "";
													?>
                                            <tr class="gradeA">
                                                <td align="center"><input type="checkbox" id="checkbox" name="checkbox[]"
                                                        value="<?php  echo $data_drv[$i]['iDriverId']; ?>" />&nbsp;</td>
                                                <td>
                                                    <?php  if($APP_TYPE == "Ride"){?>
                                                    <a href="javascript:void(0);" onClick="show_driver_details('<?=$data_drv[$i]['iDriverId'];?>')"
                                                        style="text-decoration: underline;">
                                                        <?= $generalobjAdmin->clearName($data_drv[$i]['driverName']); ?></a>
                                                    <?php  }else{?>
                                                    <?= $generalobjAdmin->clearName($data_drv[$i]['driverName']); ?>
                                                    <?php  }?>
                                                </td>
                                                <td>
                                                    <?= $generalobjAdmin->clearCmpName($data_drv[$i]['vCompany'].$status_cmp); ?>
                                                </td>
                                                <td>
                                                    <?= $generalobjAdmin->clearEmail($data_drv[$i]['vEmail']);?>
                                                </td>
                                                <?php  if($APP_TYPE != "UberX"){
                                if($data_drv[$i]['count'] != 0) {?>
                                                <td align="center"><a href="vehicles.php?&actionSearch=1&iDriverId=<?=$data_drv[$i]['iDriverId'];?>"
                                                        target="_blank">
                                                        <?php  echo $data_drv[$i]['count'];?></a></td>
                                                <?php  } else { ?>
                                                <td align="center">
                                                    <?php  echo $data_drv[$i]['count'];?>
                                                </td>
                                                <?php 
                              }
                            }?>
                                                <td align="left">
                                                    <?= $generalobjAdmin->DateTime($data_drv[$i]['tRegistrationDate'],'No')?>
                                                </td>
                                                <td align="left">
                                                    <?= $generalobjAdmin->clearPhone($data_drv[$i]['vPhone']);?>
                                                </td>
                                                <td>

                                                    <?php  
															$user_available_balance = $generalobj->get_user_available_balance($data_drv[$i]['iDriverId'], "Driver");
															 
															
															if($data_drv[$i]['eStatus'] != "Deleted"){ 
																echo $generalobj->trip_currency($user_available_balance);
																?>
                                                    <button type="button" onClick="Add_money_driver('<?=$data_drv[$i]['iDriverId'];?>')"
                                                        class="btn btn-success btn-xs">Add Balance</button>
                                                    <?php  }else{
																echo $generalobj->trip_currency($user_available_balance);
																} ?>

                                                </td>
                                                <?php  if($doc_count != 0) { ?>
                                                <td align="center">
                                                    <?php  
																	$newUrl2 = "driver_document_action.php?id=".$data_drv[$i]['iDriverId']."&action=edit&user_type=driver";
																?>
                                                    <?php  // if($data_drv[$i]['eStatus']!="Deleted"){?>
                                                    <a href="<?= $newUrl2; ?>" data-toggle="tooltip" title="Edit <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> Document"
                                                        target="_blank">
                                                        <img src="img/edit-doc.png" alt="Edit Document">
                                                    </a>
                                                    <?php  // }?>
                                                </td>
                                                <?php  } ?>
                                                <?php  if($APP_TYPE == "UberX"){?>
                                                <td align="center">
                                                    <?php  if($data_drv[$i]['eStatus']=="Deleted"){
																	$newUrl2 = "javascript:void(0);";
																}else {
																	$sql = "select iDriverVehicleId from register_driver where iDriverId = '" . $data_drv[$i]['iDriverId'] . "' ";
																	$db_iDriverVehicleId = $obj->MySQLSelect($sql);
																	$newUrl2 = "manage_service_type.php?id=".$db_iDriverVehicleId[0]['iDriverVehicleId']."";
																}
																?>
                                                    <?php  if($data_drv[$i]['eStatus']!="Deleted"){?>
                                                    <a href="<?= $newUrl2; ?>" data-toggle="tooltip" title="Edit Service Type">
                                                        <img src="img/view-details.png" alt="Edit Document">
                                                    </a>
                                                    <?php  }?>
                                                </td>
                                                <td align="center">
                                                    <a href="add_availability.php?id=<?= $data_drv[$i]['iDriverId']; ?>">
                                                        Edit
                                                        <?= $langage_lbl['LBL_AVAILABILITY'];?>
                                                    </a>
                                                </td>
                                                <?php  } ?>
                                                <td align="center">
                                                    <?php  if($data_drv[$i]['eStatus'] == 'active') {
																			$dis_img = "img/active-icon.png";
																		}else if($data_drv[$i]['eStatus'] == 'inactive'){
																			 $dis_img = "img/inactive-icon.png";
																		}else if($data_drv[$i]['eStatus'] == 'Deleted'){
																			$dis_img = "img/delete-icon.png";
																		}?>
                                                    <img src="<?=$dis_img;?>" alt="image" data-toggle="tooltip" title="<?php  echo $data_drv[$i]['eStatus']; ?>">
                                                </td>

                                                <td align="center" class="action-btn001">
                                                    <div class="share-button openHoverAction-class" style="display: block;">
                                                        <label class="entypo-export"><span><img src="images/settings-icon.png"
                                                                    alt=""></span></label>
                                                        <div class="social show-moreOptions for-five openPops_<?= $data_drv[$i]['iDriverId']; ?>">
                                                            <ul>
                                                                <li class="entypo-twitter" data-network="twitter"><a
                                                                        href="driver_action.php?id=<?=$data_drv[$i]['iDriverId']; ?>"
                                                                        data-toggle="tooltip" title="Edit">
                                                                        <img src="img/edit-icon.png" alt="Edit">
                                                                    </a></li>
                                                                
                                                                <li class="entypo-facebook" data-network="facebook"><a
                                                                        href="javascript:void(0);" onClick="changeStatus('<?php  echo $data_drv[$i]['iDriverId']; ?>','Inactive')"
                                                                        data-toggle="tooltip" title="Make Active">
                                                                        <img src="img/active-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>">
                                                                    </a></li>
                                                                <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);"
                                                                        onClick="changeStatus('<?php  echo $data_drv[$i]['iDriverId']; ?>','Active')"
                                                                        data-toggle="tooltip" title="Make Inactive">
                                                                        <img src="img/inactive-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>">
                                                                    </a></li>
                                                                <?php  if($eStatus != 'Deleted') { ?>
                                                                <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);"
                                                                        onClick="changeStatusDelete('<?php  echo $data_drv[$i]['iDriverId']; ?>')"
                                                                        data-toggle="tooltip" title="Delete">
                                                                        <img src="img/delete-icon.png" alt="Delete">
                                                                    </a></li>
                                                                <?php  } ?>
                                                                <?php   if (SITE_TYPE == 'Demo') { ?>
                                                                <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);"
                                                                        onClick="resetTripStatus('<?php  echo $data_drv[$i]['iDriverId']; ?>')"
                                                                        data-toggle="tooltip" title="Reset">
                                                                        <img src="img/reset-icon.png" alt="Reset">
                                                                    </a></li>
                                                                <?php  }

                                      } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>

                                            </tr>
                                            <?php  } else { ?>
                                            <tr class="gradeA">
                                                <td colspan="12"> No Records Found.</td>
                                            </tr>
                                            <?php  } ?>
                                        </tbody>
                                    </table>
                                </form>
                                <?php  include('pagination_n.php'); ?>
                            </div>
                        </div>
                        <!--TABLE-END-->
                    </div>
                </div>
                <div class="admin-notes">
                    <h4>Notes:</h4>
                    <ul>
                        <li>
                            <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> module will list all
                            <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> on this page.
                        </li>
                        <li>
                            Administrator can Activate / Deactivate / Delete any
                            <?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> .
                        </li>
                        <li>
                            Administrator can export data in XLS or PDF format.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--END PAGE CONTENT -->
    </div>
    <!--END MAIN WRAPPER -->
    <form name="pageForm" id="pageForm" action="action/driver.php" method="post">
        <input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
        <input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
        <input type="hidden" name="iDriverId" id="iMainId01" value="">
        <input type="hidden" name="iCompanyId" id="iCompanyId" value="<?php  echo $iCompanyId; ?>">
        <input type="hidden" name="eStatus" id="eStatus" value="<?php  echo $eStatus; ?>">
        <input type="hidden" name="status" id="status01" value="">
        <input type="hidden" name="statusVal" id="statusVal" value="">
        <input type="hidden" name="option" value="<?php  echo $option; ?>">
        <input type="hidden" name="keyword" value="<?php  echo $keyword; ?>">
        <input type="hidden" name="sortby" id="sortby" value="<?php  echo $sortby; ?>">
        <input type="hidden" name="order" id="order" value="<?php  echo $order; ?>">
        <input type="hidden" name="method" id="method" value="">
    </form>
    <div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>
                        <i style="margin:2px 5px 0 2px;"><img src="images/icon/driver-icon.png" alt=""></i>
                        <!--<i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i>-->
                        Driver Details
                        <button type="button" class="close" data-dismiss="modal">x</button>
                    </h4>
                </div>
                <div class="modal-body" style="max-height: 450px;overflow: auto;">
                    <div id="imageIcons" style="display:none">
                        <div align="center">
                            <img src="default.gif"><br />
                            <span>Retrieving details,please Wait...</span>
                        </div>
                    </div>
                    <div id="driver_detail"></div>
                </div>
            </div>
        </div>

    </div>
    <div class="modal fade" id="driver_add_wallet_money" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content nimot-class">
                <div class="modal-header">
                    <h4><i style="margin:2px 5px 0 2px;" class="fa fa-google-wallet"></i>Add Balance
                        <button type="button" class="close" data-dismiss="modal">x</button>
                    </h4>
                </div>
                <form class="form-horizontal" id="add_money_frm" method="POST" enctype="multipart/form-data" action=""
                    name="add_money_frm">
                    <input type="hidden" id="action" name="action" value="addmoney">
                    <input type="hidden" name="eTransRequest" id="eTransRequest" value="">
                    <input type="hidden" name="eType" id="eType" value="Credit">
                    <input type="hidden" name="eFor" id="eFor" value="Deposit">
                    <input type="hidden" name="iDriverId" id="iDriver-Id" value="">
                    <input type="hidden" name="eUserType" id="eUserType" value="Driver">
                    <div class="col-lg-12">
                        <div class="input-group input-append">
                            <h5>
                                <?= $langage_lbl['LBL_ADD_WALLET_DESC_TXT']; ?>
                            </h5>
                            <div class="ddtt">
                                <h4>
                                    <?= $langage_lbl['LBL_ENTER_AMOUNT']; ?>
                                </h4>
                                <input type="text" name="iBalance" id="iBalance" class="form-control iBalance add-ibalance"
                                    onKeyup="checkzero(this.value);">
                            </div>
                            <div id="iLimitmsg"></div>
                        </div>
                    </div>
                    <div class="nimot-class-but">
                        <input type="button" onClick="check_add_money();" class="save" id="add_money" name="Save"
                            value="Save">
                        <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Close</button>
                    </div>
                </form>
                <div style="clear:both;"></div>
            </div>
        </div>

    </div>
    <?php 
    include_once('footer.php');
    ?>
    <script>
        /*$(document).ready(function() {
          $('#eStatus_options').hide(); 
          $('#option').each(function(){
              if (this.value == 'rd.eStatus') {
                  $('#eStatus_options').show(); 
                  $('.searchform').hide(); 
              }
          });
      });
      $(function() {
          $('#option').change(function(){
              if($('#option').val() == 'rd.eStatus') {
                  $('#eStatus_options').show();
                  $("input[name=keyword]").val("");
                  $('.searchform').hide(); 
              } else {
                  $('#eStatus_options').hide();
                  $("#estatus_value").val("");
                  $('.searchform').show();
              } 
          });
      });*/

        $("#setAllCheck").on('click', function () {
            if ($(this).prop("checked")) {
                jQuery("#_list_form input[type=checkbox]").each(function () {
                    if ($(this).attr('disabled') != 'disabled') {
                        this.checked = 'true';
                    }
                });
            } else {
                jQuery("#_list_form input[type=checkbox]").each(function () {
                    this.checked = '';
                });
            }
        });

        $("#Search").on('click', function () {
            //$('html').addClass('loading');
            var action = $("#_list_form").attr('action');
            // alert(action);
            var formValus = $("#frmsearch").serialize();
            //                alert(action+formValus);
            window.location.href = action + "?" + formValus;
        });

        $('.entypo-export').click(function (e) {
            e.stopPropagation();
            var $this = $(this).parent().find('div');
            $(".openHoverAction-class div").not($this).removeClass('active');
            $this.toggleClass('active');
        });

        $(document).on("click", function (e) {
            if ($(e.target).is(".openHoverAction-class,.show-moreOptions,.entypo-export") === false) {
                $(".show-moreOptions").removeClass("active");
            }
        });

        function show_driver_details(driverid) {
            $("#driver_detail").html('');
            $("#imageIcons").show();
            $("#detail_modal").modal('show');

            if (driverid != "") {
                var request = $.ajax({
                    type: "POST",
                    url: "ajax_driver_details.php",
                    data: "iDriverId=" + driverid,
                    datatype: "html",
                    success: function (data) {
                        $("#driver_detail").html(data);
                        $("#imageIcons").hide();
                    }
                });
            }
        }

        function Add_money_driver(driverid) {
            $("#driver_add_wallet_money").modal('show');
            $(".add-ibalance").val("");
            if (driverid != "") {
                var setDriverId = $('#iDriver-Id').val(driverid);

            }
        }

        function changeOrder(iAdminId) {
            $('#is_dltSngl_modal').modal('show');
            $(".action_modal_submit").unbind().click(function () {
                var action = $("#pageForm").attr('action');
                var page = $("#pageId").val();
                $("#pageId01").val(page);
                $("#iMainId01").val(iAdminId);
                $("#method").val('delete');
                var formValus = $("#pageForm").serialize();
                window.location.href = action + "?" + formValus;
            });
        }

        function check_add_money() {

            var iBalance = $(".add-ibalance").val();
            if (iBalance == '') {
                alert("Please Enter Amount");
                return false;
            } else if (iBalance == 0) {
                alert("You Can Not Enter Zero Number");
                return false;
            } else {
                $("#add_money").val('Please wait ...').attr('disabled', 'disabled');
                $('#add_money_frm').submit();
            }
        }

        $(".iBalance").keydown(function (e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode == 67 && e.ctrlKey === true) ||
                (e.keyCode == 88 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        function checkzero(userlimit) {
            if (userlimit != "") {
                if (userlimit == 0) {
                    $('#iLimitmsg').html('<span class="red">You Can Not Enter Zero Number</span>');
                } else if (userlimit <= 0) {
                    $('#iLimitmsg').html('<span class="red">You Can Not Enter Negative Number</span>');
                } else {
                    $('#iLimitmsg').html('');
                }
            } else {
                $('#iLimitmsg').html('');
            }

        }
    </script>
</body>
<!-- END BODY-->

</html>