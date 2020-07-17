<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$script = 'Hotel Rider';

//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY vName ASC';
if($sortby == 1){
  if($order == 0)
  $ord = " ORDER BY vName ASC";
  else
  $ord = " ORDER BY vName DESC";
}

if($sortby == 2){
  if($order == 0)
  $ord = " ORDER BY vEmail ASC";
  else
  $ord = " ORDER BY vEmail DESC";
}

if($sortby == 3){
  if($order == 0)
  $ord = " ORDER BY tRegistrationDate ASC";
  else
  $ord = " ORDER BY tRegistrationDate DESC";
}

if($sortby == 4){
  if($order == 0)
  $ord = " ORDER BY eStatus ASC";
  else
  $ord = " ORDER BY eStatus DESC";
}
//End Sorting
$rdr_ssql = "";
if (SITE_TYPE == 'Demo') {
    $rdr_ssql = " And tRegistrationDate > '" . WEEK_DATE . "'";
}
// Start Search Parameters

$option = isset($_REQUEST['option'])?stripslashes($_REQUEST['option']):"";
$keyword = isset($_REQUEST['keyword'])?stripslashes($_REQUEST['keyword']):"";
$searchDate = isset($_REQUEST['searchDate'])?$_REQUEST['searchDate']:"";
$eStatus = isset($_REQUEST['eStatus']) ? $_REQUEST['eStatus'] : "";

$ssql = '';
if($keyword != ''){
    if($option != '') {
        if (strpos($option, 'eStatus') !== false) {
            $ssql.= " AND ".stripslashes($option)." LIKE '".$generalobjAdmin->clean($keyword)."'";
        }else {
            $ssql.= " AND ".stripslashes($option)." LIKE '%".$generalobjAdmin->clean($keyword)."%'";
        }
    } else {
        $ssql.= " AND (concat(vName,' ',vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR vEmail LIKE '%".$generalobjAdmin->clean($keyword)."%' OR vPhone LIKE '%".$generalobjAdmin->clean($keyword)."%' OR eStatus LIKE '%".$generalobjAdmin->clean($keyword)."%')";
    }
} else if(!empty($eStatus)) {
     $ssql.= " AND ".stripslashes($option)." LIKE '".$generalobjAdmin->clean($eStatus)."'";
}

// End Search Parameters


//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(iHotelId) AS Total FROM hotel WHERE eStatus != 'Deleted' $ssql $rdr_ssql";
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); //total pages we going to have
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
if(!empty($eStatus)) { 
    $sql = "SELECT iHotelId,CONCAT(vName,' ',vLastName) AS name, vEmail, CONCAT(vPhoneCode,' ',vPhone) AS mobile,tRegistrationDate,eStatus FROM hotel
    WHERE 1=1 $ssql $rdr_ssql $ord LIMIT $start, $per_page";
} else {
    $sql = "SELECT iHotelId,CONCAT(vName,' ',vLastName) AS name, vEmail, CONCAT(vPhoneCode,' ',vPhone) AS mobile,tRegistrationDate,eStatus FROM hotel
        WHERE eStatus != 'Deleted' $ssql $rdr_ssql $ord LIMIT $start, $per_page";
}
$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
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
        <title><?=$SITE_NAME?> | <?php  echo $langage_lbl_admin['LBL_EDIT_RIDERS_TXT_ADMIN'];?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php  include_once('global_files.php');?>
    </head>
    <!-- END  HEAD-->
    
    <!-- BEGIN BODY-->
    <body class="padTop53 " >
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
                                <h2>Hotel (kiosk)  Rider</h2>
                                <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php  include('valid_msg.php'); ?>
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                              <tbody>
                                <tr>
                                    <td width="5%"><label for="textfield"><strong>Search:</strong></label></td>
                                    <td width="10%" class=" padding-right10"><select name="option" id="option" class="form-control">
                                          <option value="">All</option>
                                          <option  value="concat(vName,' ',vLastName)" <?php  if ($option == "concat(vName,' ',vLastName)") { echo "selected"; } ?> >Name</option>
                                          <option value="vEmail" <?php  if ($option == 'vEmail') {echo "selected"; } ?> >E-mail</option>
                                          <option value="vPhone" <?php  if ($option == 'vPhone') {echo "selected"; } ?> >Mobile</option>
                                          <option value="eStatus" <?php  if ($option == 'eStatus') {echo "selected"; } ?> >Status</option>
                                    </select>
                                    </td>
                                    <td width="15%" class="searchform"><input type="Text" id="keyword" name="keyword" value="<?php  echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%" class="estatus_options" id="eStatus_options" >
                                        <select name="eStatus" id="estatus_value" class="form-control">
                                            <option value="" >Select Status</option>
                                            <option value='Active' <?php  if ($eStatus == 'Active') { echo "selected"; } ?> >Active</option>
                                            <option value="Inactive" <?php  if ($eStatus == 'Inactive') {echo "selected"; } ?> >Inactive</option>
                                            <option value="Deleted" <?php  if ($eStatus == 'Deleted') {echo "selected"; } ?> >Delete</option>
                                        </select>
                                    </td>
                                    <td width="12%">
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='hotel_rider.php'"/>
                                    </td>
                                    <td width="30%"><a class="add-btn" href="hotel_rider_action.php" style="text-align: center;">Add <?=$langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?></a></td>
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
                                                    <option value="" >Select Action</option>
                                                    <option value='Active' <?php  if ($option == 'Active') { echo "selected"; } ?> >Make Active</option>
                                                    <option value="Inactive" <?php  if ($option == 'Inactive') {echo "selected"; } ?> >Make Inactive</option>
                                                    <?php  if($eStatus != 'Deleted') { ?>
                                                    <option value="Deleted" <?php  if ($option == 'Delete') {echo "selected"; } ?> >Make Delete</option>
                                                    <?php  } ?>
                                            </select>
                                    </span>
                                    </div>
                                    <?php  if(!empty($data_drv)) { ?>
                                    <div class="panel-heading">
                                        <form name="_export_form" id="_export_form" method="post" >
                                            <button type="button" onClick="showExportTypes('hotel_rider')" >Export</button>
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
                                                        <th align="center" width="3%" style="text-align:center;"><input type="checkbox" id="setAllCheck" ></th>
                                                        <th width="22%"><a href="javascript:void(0);" onClick="Redirect(1,<?php  if($sortby == '1'){ echo $order; }else { ?>0<?php  } ?>)">Rider Name <?php  if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                        <th width="22%"><a href="javascript:void(0);" onClick="Redirect(2,<?php  if($sortby == '2'){ echo $order; }else { ?>0<?php  } ?>)">Email <?php  if ($sortby == 2) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                        <th width="22%"><a href="javascript:void(0);" onClick="Redirect(3,<?php  if($sortby == '3'){ echo $order; }else { ?>0<?php  } ?>)">Sign Up Date <?php  if ($sortby == 3) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                        <th width="22%">Mobile</th>
                                                        <th width="8%" align="center" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(4,<?php  if($sortby == '4'){ echo $order; }else { ?>0<?php  } ?>)">Status <?php  if ($sortby == 4) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                        <th width="8%" align="center" style="text-align:center;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
													<?php  if(!empty($data_drv)) { 
                                                        for($i=0;$i<count($data_drv);$i++) {?>
														<tr class="gradeA">
														<td align="center" style="text-align:center;"><input type="checkbox" id="checkbox" name="checkbox[]" <?php  echo $default; ?> value="<?php  echo $data_drv[$i]['iHotelId']; ?>" />&nbsp;</td>
															<td><?php  echo $generalobjAdmin->clearName($data_drv[$i]['name']); ?></td>
															<td><?php  echo $generalobjAdmin->clearEmail($data_drv[$i]['vEmail']); ?></td>
															<td data-order="<?=$data_drv[$i]['iHotelId']; ?>"><?php  echo $generalobjAdmin->DateTime($data_drv[$i]['tRegistrationDate']) ?></td>
															<td class="center"><?= $generalobjAdmin->clearPhone($data_drv[$i]['mobile']);?></td>
															<td width="10%" align="center">
																<?php  if($data_drv[$i]['eStatus'] == 'Active') {
																	$dis_img = "img/active-icon.png";
																	}else if($data_drv[$i]['eStatus'] == 'Inactive'){
																	$dis_img = "img/inactive-icon.png";
																	}else if($data_drv[$i]['eStatus'] == 'Deleted'){
																	$dis_img = "img/delete-icon.png";
																}?>
																<img src="<?=$dis_img;?>" alt="<?=$data_drv[$i]['eStatus']?>" data-toggle="tooltip" title="<?php  echo $data_drv[$i]['eStatus']; ?>" >
															</td>
															
															<td align="center" style="text-align:center;" class="action-btn001">
                                                                <div class="share-button openHoverAction-class" style="display: block;">
                                                                    <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
                                                                    <div class="social show-moreOptions for-five openPops_<?= $data_drv[$i]['iHotelId']; ?>">
                                                                        <ul>
                                                                            <li class="entypo-twitter" data-network="twitter"><a href="hotel_rider_action.php?id=<?= $data_drv[$i]['iHotelId']; ?>" data-toggle="tooltip" title="Edit">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                            </a></li>
                                                                            
                                                                            <li class="entypo-facebook" data-network="facebook"><a href="javascript:void(0);" onClick="changeStatus('<?php  echo $data_drv[$i]['iHotelId']; ?>','Inactive')"  data-toggle="tooltip" title="Make Active">
                                                                                <img src="img/active-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >
                                                                            </a></li>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="changeStatus('<?php  echo $data_drv[$i]['iHotelId']; ?>','Active')" data-toggle="tooltip" title="Make Inactive">
                                                                                <img src="img/inactive-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >	
                                                                            </a></li>
                                                                            <?php  if($eStatus != 'Deleted') { ?>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="changeStatusDelete('<?php  echo $data_drv[$i]['iHotelId']; ?>')"  data-toggle="tooltip" title="Delete">
                                                                                <img src="img/delete-icon.png" alt="Delete" >
                                                                            </a></li>
                                                                            <?php  } ?>
                                                                           
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </td>
															
														</tr>
													<?php  } 
                                                    } else { ?>
                                                    <tr class="gradeA">
                                                        <td colspan="8"> No Records Found.</td>
                                                    </tr>
                                                    <?php  } ?>
													
												</tbody>
                                                </table>
                                            </form>
                                            <?php  include('pagination_n.php'); ?>
                                    </div>
                                </div> <!--TABLE-END-->
                            </div>
                        </div>
                    <div class="admin-notes">
                            <h4>Notes:</h4>
                            <ul>
                                    <li>
                                            <?php  echo $langage_lbl_admin['LBL_EDIT_RIDERS_TXT_ADMIN'];?> module will list all <?php  echo $langage_lbl_admin['LBL_EDIT_RIDERS_TXT_ADMIN'];?> on this page.
                                    </li>
                                    <li>
                                            Administrator can Activate / Deactivate / Delete any <?php  echo $langage_lbl_admin['LBL_EDIT_RIDERS_TXT_ADMIN'];?>
                                    </li>
                                    <li>
                                            Administrator can export data in XLS or PDF format.
                                    </li>
                                    <!--li>
                                            "Export by Search Data" will export only search result data in XLS or PDF format.
                                    </li-->
                            </ul>
                    </div>
                    </div>
                </div>
                <!--END PAGE CONTENT -->
            </div>
            <!--END MAIN WRAPPER -->
            
<form name="pageForm" id="pageForm" action="action/hotel_rider.php" method="post" >
<input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
<input type="hidden" name="iHotelId" id="iMainId01" value="" >
<input type="hidden" name="status" id="status01" value="" >
<input type="hidden" name="statusVal" id="statusVal" value="" >
<input type="hidden" name="option" value="<?php  echo $option; ?>" >
<input type="hidden" name="keyword" value="<?php  echo $keyword; ?>" >
<input type="hidden" name="sortby" id="sortby" value="<?php  echo $sortby; ?>" >
<input type="hidden" name="order" id="order" value="<?php  echo $order; ?>" >
<input type="hidden" name="method" id="method" value="" >
</form>
<?php  include_once('footer.php'); ?>
<script>
    $(document).ready(function() {
        $('#eStatus_options').hide(); 
        $('#option').each(function(){
            if (this.value == 'eStatus') {
                $('#eStatus_options').show(); 
                $('.searchform').hide(); 
            }
        });
    });
    $(function() {
        $('#option').change(function(){
            if($('#option').val() == 'eStatus') {
                $('#eStatus_options').show();
                $("input[name=keyword]").val("");
                $('.searchform').hide(); 
            } else {
                $('#eStatus_options').hide();
                $("#estatus_value").val("");
                $('.searchform').show();
            } 
        });
    });

    $("#setAllCheck").on('click',function(){
        if($(this).prop("checked")) {
            jQuery("#_list_form input[type=checkbox]").each(function() {
                if($(this).attr('disabled') != 'disabled'){
                    this.checked = 'true';
                }
            });
        }else {
            jQuery("#_list_form input[type=checkbox]").each(function() {
                this.checked = '';
            });
        }
    });
    
    $("#Search").on('click', function(){
        //$('html').addClass('loading');
        var action = $("#_list_form").attr('action');
        //alert(action);
        var formValus = $("#frmsearch").serialize();
        window.location.href = action+"?"+formValus;
    });
    
    $('.entypo-export').click(function(e){
         e.stopPropagation();
         var $this = $(this).parent().find('div');
         $(".openHoverAction-class div").not($this).removeClass('active');
         $this.toggleClass('active');
    });
    
    $(document).on("click", function(e) {
        if ($(e.target).is(".openHoverAction-class,.show-moreOptions,.entypo-export") === false) {
          $(".show-moreOptions").removeClass("active");
        }
    });
</script>
    </body>
    <!-- END BODY-->
</html>