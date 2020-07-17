<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$tbl_name = 'restricted_negative_area';
$script = 'Restricted Area';

//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY lm.vLocationName ASC';

if($sortby == 4){
  if($order == 0)
  $ord = " ORDER BY lm.vLocationName ASC";
  else
  $ord = " ORDER BY lm.vLocationName DESC";
}

if($sortby == 5){
  if($order == 0)
  $ord = " ORDER BY ra.eRestrictType ASC";
  else
  $ord = " ORDER BY ra.eRestrictType DESC";
}

if($sortby == 6){
  if($order == 0)
  $ord = " ORDER BY ra.eStatus ASC";
  else
  $ord = " ORDER BY ra.eStatus DESC";
}

if($sortby == 7){
  if($order == 0)
  $ord = " ORDER BY ra.eType ASC";
  else
  $ord = " ORDER BY ra.eType DESC";
}
//End Sorting

$adm_ssql = "";
if (SITE_TYPE == 'Demo') {
    $adm_ssql = " And tRegistrationDate > '" . WEEK_DATE . "'";
}

// Start Search Parameters
$option = isset($_REQUEST['option'])?stripslashes($_REQUEST['option']):"";
$keyword = isset($_REQUEST['keyword'])?stripslashes($_REQUEST['keyword']):"";
$searchDate = isset($_REQUEST['searchDate'])?$_REQUEST['searchDate']:"";
$ssql = '';
if($keyword != ''){
    if($option != '') {
        if (strpos($option, 'ra.eStatus') !== false) {
            $ssql.= " AND ".stripslashes($option)." LIKE '".$generalobjAdmin->clean($keyword)."'";
        }else {
            $ssql.= " AND ".stripslashes($option)." LIKE '%".$generalobjAdmin->clean($keyword)."%'";
        }
    }else {
        $ssql.= " AND (lm.vLocationName LIKE '%".$generalobjAdmin->clean($keyword)."%' OR ra.eStatus LIKE '%".$generalobjAdmin->clean($keyword)."%' OR ra.eRestrictType LIKE '%".$generalobjAdmin->clean($keyword)."%' OR ra.eType LIKE '%".$generalobjAdmin->clean($keyword)."%')";
    }
}
// End Search Parameters


//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(ra.iRestrictedNegativeId) AS Total FROM ".$tbl_name." AS ra
    LEFT JOIN location_master AS lm ON lm.iLocationId=ra.iLocationId
		WHERE 1=1 $ssql";
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

$sql = "SELECT ra.iRestrictedNegativeId, lm.vLocationName as Address, ra.eRestrictType, ra.eType, ra.eStatus FROM ".$tbl_name." AS ra
		LEFT JOIN location_master AS lm ON lm.iLocationId=ra.iLocationId
		WHERE 1=1 $ssql $ord LIMIT $start, $per_page";
$db_data = $obj->MySQLSelect($sql);

$endRecord = count($db_data);
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
        <title><?=$SITE_NAME?> | Restricted Area</title>
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
                                <h2>Restricted Area</h2>
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
                                          <option value="lm.vLocationName" <?php  if ($option == 'lm.vLocationName') {echo "selected"; } ?> >Address</option>
                                          <option value="ra.eStatus" <?php  if ($option == 'ra.eStatus') {echo "selected"; } ?> >Status</option>
                                    </select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php  echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='restricted_area.php'"/>
                                    </td>
                                    <td width="30%"><a class="add-btn" href="restricted_area_action.php" style="text-align: center;">Add Restricted Area</a></td>
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
                                            <select name="changeStatus" id="changeStatus" class="form-control" onchange="ChangeStatusAll(this.value);">
                                                    <option value="" >Select Action</option>
                                                    <option value='Active' <?php  if ($option == 'Active') { echo "selected"; } ?> >Make Active</option>
                                                    <option value="Inactive" <?php  if ($option == 'Inactive') {echo "selected"; } ?> >Make Inactive</option>
                                                    <option value="Deleted" <?php  if ($option == 'Delete') {echo "selected"; } ?> >Make Delete</option>
                                            </select>
                                    </span>
                                    </div>
                                    <?php  $count_all = count($db_data);
                                    if($count_all > 0) { ?>
                                    <div class="panel-heading">
                                        <form name="_export_form" id="_export_form" method="post" >
                                            <button type="button" onclick="showExportTypes('restrict_area')" >Export</button>
                                        </form>
                                   </div>
                                   <?php  } ?>
                                    </div>
                                    <div style="clear:both;"></div>
                                        <div class="table-responsive">
                        <form class="_list_form" id="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-example">
												<thead>
													<tr>
														<th align="center" width="3%" style="text-align:center;"><input type="checkbox" id="setAllCheck" ></th>
														<th><a href="javascript:void(0);" onClick="Redirect(4,<?php  if($sortby == '4'){ echo $order; }else { ?>0<?php  } ?>)">Address <?php  if ($sortby == 4) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                        <?php  if($APP_TYPE != 'UberX') {?>
														<th width="15%"><a href="javascript:void(0);" onClick="Redirect(5,<?php  if($sortby == '5'){ echo $order; }else { ?>0<?php  } ?>)">Area <?php  if ($sortby == 5) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                        <?php  } ?>
														<th width="15%" ><a href="javascript:void(0);" onClick="Redirect(7,<?php  if($sortby == '7'){ echo $order; }else { ?>0<?php  } ?>)">Type <?php  if ($sortby == 7) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
														<th width="6%" class='align-center'><a href="javascript:void(0);" onClick="Redirect(6,<?php  if($sortby == '6'){ echo $order; }else { ?>0<?php  } ?>)">Status <?php  if ($sortby == 6) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
														<th style="text-align: center;">Action</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$count_all = count($db_data);
														if($count_all > 0) {
															for($i=0;$i<$count_all;$i++) {
															?>
															<tr class="gradeA">
																<td align="center" style="text-align:center;"><input type="checkbox" id="checkbox" name="checkbox[]" value="<?php  echo $db_data[$i]['iRestrictedNegativeId']; ?>" />&nbsp;</td>
																<td><?php  echo ($db_data[$i]['Address'] != '') ? $db_data[$i]['Address'] : '---';?></td>
                                                                <?php  if($APP_TYPE != 'UberX') {?>
																    <td><?php  echo $db_data[$i]['eRestrictType'];?></td>
                                                                <?php  } ?>
																<td><?php  echo $db_data[$i]['eType'];?></td>
																<td width="20%" align="center" class="center">
																	<?php  if($db_data[$i]['eStatus'] == 'Active') {
																		   $dis_img = "img/active-icon.png";
																			}else if($db_data[$i]['eStatus'] == 'Inactive'){
																			 $dis_img = "img/inactive-icon.png";
																				}else if($db_data[$i]['eStatus'] == 'Deleted'){
																				$dis_img = "img/delete-icon.png";
																				}?>
																		<img src="<?=$dis_img;?>" alt="<?=$db_data[$i]['eStatus'];?>" data-toggle="tooltip" title="<?=$db_data[$i]['eStatus'];?>">
																</td>
																<td align="center" style="text-align:center;" class="action-btn001">
                                                                <div class="share-button  openHoverAction-class" style="display: block;">
                                                                    <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
                                                                    <div class="social show-moreOptions leastcol openPops_<?= $db_data[$i]['iRestrictedNegativeId']; ?>">
                                                                        <ul>
                                                                            <li class="entypo-twitter" data-network="twitter"><a href="restricted_area_action.php?restricted_id=<?= $db_data[$i]['iRestrictedNegativeId']; ?>" data-toggle="tooltip" title="Edit">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                            </a></li>
                                                                            <li class="entypo-facebook" data-network="facebook"><a href="javascript:void(0);" onclick="changeStatus('<?php  echo $db_data[$i]['iRestrictedNegativeId']; ?>','Inactive')"  data-toggle="tooltip" title="Make Active">
                                                                                <img src="img/active-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >
                                                                            </a></li>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatus('<?php  echo $db_data[$i]['iRestrictedNegativeId']; ?>','Active')" data-toggle="tooltip" title="Make Inactive">
                                                                                <img src="img/inactive-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >	
                                                                            </a></li>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatusDelete('<?php  echo $db_data[$i]['iRestrictedNegativeId']; ?>')"  data-toggle="tooltip" title="Delete">
                                                                                <img src="img/delete-icon.png" alt="Delete" >
                                                                            </a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </td>
															</tr>
															<?php  }
														} else { ?>
														<tr class="gradeA">
															<td colspan="10">No Records found.</td>
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
                                            Restricted Area module will list all Restricted Area on this page.
                                    </li>
                                    <li>
                                            Administrator can Activate / Deactivate / Delete any Restricted Area. Super Admin cannot be Activated / Deactivated / Deleted.
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
            
<form name="pageForm" id="pageForm" action="action/restrict_area.php" method="post" >
<input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
<input type="hidden" name="iRestrictedNegativeId" id="iMainId01" value="" >
<input type="hidden" name="status" id="status01" value="" >
<input type="hidden" name="statusVal" id="statusVal" value="" >
<input type="hidden" name="option" value="<?php  echo $option; ?>" >
<input type="hidden" name="keyword" value="<?php  echo $keyword; ?>" >
<input type="hidden" name="sortby" id="sortby" value="<?php  echo $sortby; ?>" >
<input type="hidden" name="order" id="order" value="<?php  echo $order; ?>" >
<input type="hidden" name="method" id="method" value="" >
</form>
    <?php 
    include_once('footer.php');
    ?>
        <script>
            
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
               // alert(action);
                var formValus = $("#frmsearch").serialize();
//                alert(action+formValus);
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