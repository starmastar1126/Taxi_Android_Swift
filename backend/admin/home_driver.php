<?php 
	include_once('../common.php');
	
	if (!isset($generalobjAdmin)) {
		require_once(TPATH_CLASS . "class.general_admin.php");
		$generalobjAdmin = new General_admin();
	}
	$generalobjAdmin->check_member_login();
	$script = 'home_driver';
	
	//Start Sorting
	$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
	$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
	$ord = ' ORDER BY iDriverId DESC';
	if($sortby == 1){
		if($order == 0)
		$ord = " ORDER BY vName_".$default_lang." ASC";
		else
		$ord = " ORDER BY vName_".$default_lang." DESC";
	}
	
	if($sortby == 2){
		if($order == 0)
		$ord = " ORDER BY vDesignation_".$default_lang." ASC";
		else
		$ord = " ORDER BY vDesignation_".$default_lang." DESC";
	}
	
	if($sortby == 3){
		if($order == 0)
		$ord = " ORDER BY eStatus ASC";
		else
		$ord = " ORDER BY eStatus DESC";
	}
	//End Sorting
	
	$adm_ssql = "";
	/*if (SITE_TYPE == 'Demo') {
		$adm_ssql = " And ad.tRegistrationDate > '" . WEEK_DATE . "'";
	}*/
	
	// Start Search Parameters
	$option = isset($_REQUEST['option'])?stripslashes($_REQUEST['option']):"";
	$keyword = isset($_REQUEST['keyword'])?stripslashes($_REQUEST['keyword']):"";
	$searchDate = isset($_REQUEST['searchDate'])?$_REQUEST['searchDate']:"";
	$ssql = '';
	if($keyword != ''){
		if($option != '') {
			if (strpos($option, 'eStatus') !== false) {
				$ssql.= " AND ".stripslashes($option)." LIKE '".stripslashes($keyword)."'";
				}else {
				$ssql.= " AND ".stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
			}
			}else {
			$ssql.= " AND ( vName_".$default_lang." LIKE '%".$keyword."%' OR vDesignation_".$default_lang." LIKE '%".$keyword."%')";
		}
	}
	// End Search Parameters
	
	
	//Pagination Start
	$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
	$sql = "SELECT count(iDriverId) as Total FROM home_driver where 1=1 $ssql";
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
	
	
	$sql = "  SELECT * FROM home_driver where 1=1  $ssql $ord LIMIT $start, $per_page";
	$data_drv = $obj->MySQLSelect($sql);
	// echo "<pre>"; print_r($data_drv); die;
	$endRecord = count($data_drv);
	$var_filter = "";
	foreach ($_REQUEST as $key=>$val) {
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
        <title><?=$SITE_NAME?> | Admin</title>
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
                                <h2>Our <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></h2>
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
										<option value="vName_<?=$default_lang;?>" <?php  if ($option == "vName_".$default_lang) { echo "selected"; } ?> >Name</option>
										<option value="vDesignation_<?=$default_lang?>" <?php  if ($option == 'vDesignation_'.$default_lang) {echo "selected"; } ?> >Designation</option>
										<option value="eStatus" <?php  if ($option == 'eStatus') {echo "selected"; } ?> >Status</option>
									</select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php  echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
										<input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
										<input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='home_driver.php'"/>
									</td>
                                    <td width="30%"><a class="add-btn" href="home_driver_action.php" style="text-align: center;">Add <?=$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?></a></td>
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
                                    <!-- <div class="panel-heading">
                                        <form name="_export_form" id="_export_form" method="post" >
										<button type="button" onclick="showExportTypes('admin')" >Export</button>
                                        </form>
									</div> -->
								</div>
								<div style="clear:both;"></div>
								<div class="table-responsive">
									<form class="_list_form" id="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
										<table class="table table-striped table-bordered table-hover">
											<thead>
												<tr>
													<th align="center" width="3%" style="text-align:center;"><input type="checkbox" id="setAllCheck" ></th>
													<th width="10%">Image</th>
													<th width="20%"><a href="javascript:void(0);" onClick="Redirect(1,<?php  if($sortby == '1'){ echo $order; }else { ?>0<?php  } ?>)">Name <?php  if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
													<th width="30%"><a href="javascript:void(0);" onClick="Redirect(2,<?php  if($sortby == '2'){ echo $order; }else { ?>0<?php  } ?>)">Designation <?php  if ($sortby == 2) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
													<th style="text-align:center;" width="15%">Order</th>
													<th width="8%" align="center" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(3,<?php  if($sortby == '3'){ echo $order; }else { ?>0<?php  } ?>)">Status <?php  if ($sortby == 3) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
													<th width="8%" align="center" style="text-align:center;">Action</th>
												</tr>
											</thead>
											<tbody>
												<?php  
                                                    if(!empty($data_drv)) {
														for ($i = 0; $i < count($data_drv); $i++) { 
															$default = '';
                                                        if(isset($data_drv[$i]['eDefault']) && $data_drv[$i]['eDefault']=='Yes'){
                                                                $default = 'disabled';
                                                        }?>
														<tr class="gradeA">
															<td align="center" style="text-align:center;"><input type="checkbox" id="checkbox" name="checkbox[]" <?php  echo $default; ?> value="<?php  echo $data_drv[$i]['iDriverId']; ?>" />&nbsp;</td>
															<td><img src="<?=$tconfig["tsite_upload_images"].'/'.$data_drv[$i]['vImage'];?>"alt="<?= $data_drv[$i]['vImage']; ?>" title="<?= $data_drv[$i]['vImage']; ?>" height="100" width="100" /></td>
															<td><?= $data_drv[$i]['vName_'.$default_lang]; ?></td>
															<td><?= $data_drv[$i]['vDesignation_'.$default_lang]; ?></td>
															<td width="10%" align="center"><?php  echo $data_drv[$i]['iDisplayOrder']; ?></td>
															<td align="center" style="text-align:center;">
																<?php  if ($data_drv[$i]['eStatus'] != 'Yes') { ?>
																	<?php  if($data_drv[$i]['eStatus'] == 'Active') {
																		$dis_img = "img/active-icon.png";
																		}else if($data_drv[$i]['eStatus'] == 'Inactive'){
																		$dis_img = "img/inactive-icon.png";
																		}else if($data_drv[$i]['eStatus'] == 'Deleted'){
																		$dis_img = "img/delete-icon.png";
																	}?>
																	<img src="<?= $dis_img; ?>" alt="image" data-toggle="tooltip" title="<?=$data_drv[$i]['eStatus'];?>">
																	<?php 
																		} else {
																		?><img src="img/active-icon.png" alt="image" data-toggle="tooltip" title="<?=$data_drv[$i]['eStatus'];?>"><?php 
																	}
																?>
															</td>
                                                            <td align="center" style="text-align:center;" class="action-btn001">
                                                                <div class="share-button openHoverAction-class" style="display: block;">
																<label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
																<div class="social show-moreOptions openPops_<?= $data_drv[$i]['iDriverId']; ?>">
																	<ul>
																		<li class="entypo-twitter" data-network="twitter"><a href="home_driver_action.php?id=<?= $data_drv[$i]['iDriverId']; ?>" data-toggle="tooltip" title="Edit">
																			<img src="img/edit-icon.png" alt="Edit">
																		</a></li>
																		
                                                                            <li class="entypo-facebook" data-network="facebook"><a href="javascript:void(0);" onclick="changeStatus('<?php  echo $data_drv[$i]['iDriverId']; ?>','Inactive')"  data-toggle="tooltip" title="Make Active">
                                                                                <img src="img/active-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >
																			</a></li>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatus('<?php  echo $data_drv[$i]['iDriverId']; ?>','Active')" data-toggle="tooltip" title="Make Inactive">
                                                                                <img src="img/inactive-icon.png" alt="<?php  echo $data_drv[$i]['eStatus']; ?>" >	
																			</a></li>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatusDelete('<?php  echo $data_drv[$i]['iDriverId']; ?>')"  data-toggle="tooltip" title="Delete">
                                                                                <img src="img/delete-icon.png" alt="Delete" >
																			</a></li>
																		<?php  } ?>
																	</ul>
																</div>
															</div>
                                                            </td>
														</tr>
													<?php  } else { ?>
													<tr class="gradeA">
														<td colspan="7"> No Records Found.</td>
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
								Home page driver module will list all home page drivers on this page.
							</li>
							<li>
								Administrator can Activate / Deactivate / Delete any home page driver.
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
		
		<form name="pageForm" id="pageForm" action="action/home_driver.php" method="post" >
			<input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
			<input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
			<input type="hidden" name="iDriverId" id="iMainId01" value="" >
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