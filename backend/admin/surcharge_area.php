<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
     require_once(TPATH_CLASS . "class.general_admin.php");
     $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
//$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'delete';
//$success	= isset($_REQUEST['success'])?$_REQUEST['success']:'';

$script = 'SurchargeArea'; 


//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY ls.iSurchargeId DESC';
if($sortby == 1){
  // if($order == 0)
  // $ord = " ORDER BY vVehicleType ASC";
  // else
  // $ord = " ORDER BY vVehicleType DESC";
}

if($sortby == 2){
  if($order == 0)
  $ord = " ORDER BY ls.vAddress ASC";
  else
  $ord = " ORDER BY ls.vAddress DESC";
}

if($sortby == 3){
  if($order == 0)
  $ord = " ORDER BY ls.fRadius ASC";
  else
  $ord = " ORDER BY ls.fRadius DESC";
}

//End Sorting

// $adm_ssql = "";
// if (SITE_TYPE == 'Demo') {
    // $adm_ssql = " And tRegistrationDate > '" . WEEK_DATE . "'";
// }

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
        $ssql.= " AND (ls.vAddress LIKE '%".$keyword."%' or ls.fRadius LIKE '%".$keyword."%')";
	}
}
// End Search Parameters
$Vehicle_type_name = ($APP_TYPE == 'Delivery')? 'Deliver':$APP_TYPE ; 
//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql="";
if($Vehicle_type_name == "Ride-Delivery")
{
      // $sql = "SELECT count(iVehicleTypeId) AS Total from  vehicle_type  as vt where(eType ='Ride' or eType ='Deliver') $ssql";
}
else
{
     if($APP_TYPE == 'UberX')
	 {
			   // $sql = "SELECT count(vt.iVehicleTypeId) as Total,vc.iVehicleCategoryId,vc.vCategory_EN 
			   // from  vehicle_type as vt 
			   // left join vehicle_category as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId where vt.eType='".$Vehicle_type_name."' $ssql";
	 }
	else
	{
			 $sql = "SELECT count(ls.iSurchargeId) as Total from location_surcharge ls 
					where 1=1 $ssql";
			
	}  
}   
// $sql; 
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

$Vehicle_type_name = ($APP_TYPE == 'Delivery')? 'Deliver':$APP_TYPE ; 
 $sql="";
 if($Vehicle_type_name == "Ride-Delivery")
 {
      // $sql = "SELECT * from  vehicle_type where(eType ='Ride' or eType ='Deliver') $ssql $adm_ssql $ord LIMIT $start, $per_page";  
 }
 else
{
	if($APP_TYPE == 'UberX')
	{
		/* $sql = "SELECT vt.*,vc.iVehicleCategoryId,vc.vCategory_EN 
		from  vehicle_type as vt  
		left join vehicle_category as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId 
		left join country as c ON c.iCountryId = vt.iCountryId
		left join state as st ON st.iStateId = vt.iStateId
		left join city as ct ON ct.iCityId = vt.iCityId
		where vt.eType='".$Vehicle_type_name."' $ssql $adm_ssql $ord LIMIT $start, $per_page";	 */
	
	}
	else if($APP_TYPE == 'Ride-Delivery-UberX')
	{
		/* $sql = "SELECT vt.*,c.vCountry,ct.vCity,st.vState 
		from vehicle_type as vt left join country as c ON c.iCountryId = vt.iCountryId 
		left join state as st ON st.iStateId = vt.iStateId 
		left join city as ct ON ct.iCityId = vt.iCityId 
		where 1=1 $ssql $adm_ssql $ord LIMIT $start, $per_page"; */
	}else {
		
		/* $sql = "SELECT ls.*,(select group_concat(vt.vVehicleType) from vehicle_type vt left join location_surcharge_rates lsr on lsr.iVehicleTypeId = vt.iVehicleTypeId where lsr.iSurchargeId = ls.iSurchargeId) as vVehicleType
		from location_surcharge ls 
		where 1=1 $ssql $adm_ssql $ord LIMIT $start, $per_page"; */
		
		$sql = "SELECT ls.* from location_surcharge ls 
		where 1=1 $ssql $adm_ssql $ord LIMIT $start, $per_page";
	}

      
}
//$sql;    
$data_surge = $obj->MySQLSelect($sql);
// echo "<pre>";print_r($data_surge);exit;

if($data_surge[0]['iSurchargeId'] != ""){
	$endRecord = count($data_surge);
}else{
	$endRecord = 0;
}

$var_filter = "";
foreach ($_REQUEST as $key=>$val)
{
    if($key != "tpages" && $key != 'page')
    $var_filter.= "&$key=".stripslashes($val);
}
$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages.$var_filter;
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

     <!-- BEGIN HEAD-->
     <head>
          <meta charset="UTF-8" />
          <title><?=$SITE_NAME;?> | Surcharge Locations</title>
          <meta content="width=device-width, initial-scale=1.0" name="viewport" />

          <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
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
                                <h2>Surcharge Locations</h2>
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
                                          <option value="ls.vAddress" <?php  if ($option == 'ls.vAddress') {echo "selected"; } ?> >Address</option>
                                    </select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php  echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='surcharge_area.php'"/>
                                    </td>
                                    <td width="30%"><a class="add-btn" href="surcharge_area_action.php" style="text-align: center;">Add Surcharge Area</a></td>
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
                                                    <option value="Deleted" <?php  if ($option == 'Delete') {echo "selected"; } ?> >Make Delete</option>
                                            </select>
                                    </span>
                                    </div>
                                   
                                    </div>
                                    <div style="clear:both;"></div>
                                        <div class="table-responsive">
                                            <form class="_list_form" id="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th align="center" width="3%" style="text-align:center;"><input type="checkbox" id="setAllCheck" ></th>
                                                        <!--<th width="11%"><a href="javascript:void(0);" onClick="Redirect(1,<?php  if($sortby == '1'){ echo $order; }else { ?>0<?php  } ?>)">Vehicle Types<?php  if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
														<th width="17%">Localization : Country/State/City</th> -->
														
                                                        <th width="25%"><a href="javascript:void(0);" onClick="Redirect(2,<?php  if($sortby == '2'){ echo $order; }else { ?>0<?php  } ?>)">Address <?php  if ($sortby == 2) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                        <th width="8%"><a href="javascript:void(0);" onClick="Redirect(3,<?php  if($sortby == '3'){ echo $order; }else { ?>0<?php  } ?>)">Radius <?php  if ($sortby == 3) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
														<th width="8%" align="center" style="text-align:center;">Action</th>
                                                    </tr>
                                                </thead>
                                               <tbody>
												<?php  
												if(!empty($data_surge) && $data_surge[0]['iSurchargeId']!="") {
														for ($i = 0; $i < count($data_surge); $i++) { ?>
                                                                 <tr class="gradeA">
																 <td  style="text-align:center;"><input type="checkbox" id="checkbox" name="checkbox[]" <?php  echo $default; ?> value="<?php  echo $data_surge[$i]['iSurchargeId']; ?>" />&nbsp;</td>
                                                                  <!--  <td><?php //= $data_surge[$i]['vVehicleType'] ?></td>
																	 <td ><?php  
																		// $str = "";
																		// $str.=($data_surge[$i]['vCountry']!='') ? $data_surge[$i]['vCountry'] : '';
																		// $str.=($data_surge[$i]['vState']!='') ? "/".$data_surge[$i]['vState'] : '';
																		// $str.=($data_surge[$i]['vCity']!='') ? "/".$data_surge[$i]['vCity'] : '';
																		// echo $str;
																	 ?>
																	 
																	 
																	 </td> -->

																	<?php  if($APP_TYPE != 'UberX'){ ?> 
																			 <td><?= $data_surge[$i]['vAddress'] ?></td>

																			 
																			  <td><?= $data_surge[$i]['fRadius'] ?> Km</td>
																			  
                                                                      <?php  } ?>                          
																	 
                                                                    
                                                                     <td align="center" style="text-align:center;" class="action-btn001">
                                                                <div class="share-button openHoverAction-class" style="display: block;">
                                                                    <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
                                                                    <div class="social show-moreOptions for-two openPops_<?= $data_surge[$i]['iSurchargeId']; ?>">
                                                                        <ul>
                                                                            <li class="entypo-twitter" data-network="twitter"><a href="surcharge_area_action.php?id=<?= $data_surge[$i]['iSurchargeId']; ?>" data-toggle="tooltip" title="Edit">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                            </a></li>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatusDelete('<?php  echo $data_surge[$i]['iSurchargeId']; ?>')"data-toggle="tooltip" title="Delete">
                                                                                <img src="img/delete-icon.png" alt="Delete" >
                                                                            </a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            
																	</td>
                                                                 </tr>    
																<?php  } } else{?>
																<tr class="gradeA">
																	<td colspan="12"> No Records Found.</td>
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
                                           Surcharge Locations will list all surchare locations on this page.
                                    </li>
									 <li>
                                            Administrator can Add new Surcharge Location to apply surcharge in specific radius. 
                                    </li>
                                    <li>
                                            Administrator can Edit / Delete any Surcharge Areas. 
                                    </li>
                                    <!--li>
                                            Administrator can export data in XLS or PDF format.
                                    </li>
                                    <li>
                                            "Export by Search Data" will export only search result data in XLS or PDF format.
                                    </li-->
                            </ul>
                    </div>
                    </div>
                </div>
                <!--END PAGE CONTENT -->
            </div>
            <!--END MAIN WRAPPER -->
            
<form name="pageForm" id="pageForm" action="action/surcharge_action.php" method="post" >
<input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
<input type="hidden" name="iSurchargeId" id="iMainId01" value="" >
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
                        if($(this).attr('disabled') != 'disabled'){ this.checked = 'true'; }
                    });
                }else {
                    jQuery("#_list_form input[type=checkbox]").each(function() { this.checked = ''; });
                }
            });
            
            $("#Search").on('click', function(){
                var action = $("#_list_form").attr('action');
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