<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$script = 'Review';

$type=(isset($_REQUEST['reviewtype']) && $_REQUEST['reviewtype'] !='')?$_REQUEST['reviewtype']:'Driver';
$reviewtype=$type;


//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY iRatingId DESC';
if($sortby == 1){
  if($order == 0)
  $ord = " ORDER BY t.vRideNo ASC";
  else
  $ord = " ORDER BY t.vRideNo DESC";
}
if($sortby == 2)
{
	if($reviewtype=='Driver')
	{
		if($order == 0)
		$ord = " ORDER BY rd.vName ASC";
		else
		$ord = " ORDER BY rd.vName DESC";
	}
	else
	{
		if($order == 0)
		$ord = " ORDER BY ru.vName ASC";
		else
		$ord = " ORDER BY ru.vName DESC";
	}
}
if($sortby == 6)
{
	if($reviewtype=='Driver')
	{
		if($order == 0)
		$ord = " ORDER BY ru.vName ASC";
		else
		$ord = " ORDER BY ru.vName DESC";
	}
	else
	{
		if($order == 0)
		$ord = " ORDER BY rd.vName ASC";
		else
		$ord = " ORDER BY rd.vName DESC";
	}
}

if($sortby == 3){
  if($order == 0)
  $ord = " ORDER BY r.vRating1 ASC";
  else
  $ord = " ORDER BY r.vRating1 DESC";
}

if($sortby == 4){
  if($order == 0)
  $ord = " ORDER BY r.tDate ASC";
  else
  $ord = " ORDER BY r.tDate DESC";
}

if($sortby == 5){
  if($order == 0)
  $ord = " ORDER BY r.vMessage ASC";
  else
  $ord = " ORDER BY r.vMessage DESC";
}
//End Sorting

$adm_ssql = "";
if (SITE_TYPE == 'Demo') {
    $adm_ssql = " And ru.tRegistrationDate > '" . WEEK_DATE . "'";
}

// Start Search Parameters
$option = isset($_REQUEST['option'])?stripslashes($_REQUEST['option']):"";
$keyword = isset($_REQUEST['keyword'])?stripslashes($_REQUEST['keyword']):"";
$searchDate = isset($_REQUEST['searchDate'])?$_REQUEST['searchDate']:"";
$ssql = '';
if($keyword != ''){
    if($option != '') {
        if (strpos($option, 'r.eStatus') !== false) {
            $ssql.= " AND ".stripslashes($option)." LIKE '".$generalobjAdmin->clean($keyword)."'";
        }else {
            $option_new = $option;
            if($option == 'drivername'){
              $option_new = "CONCAT(rd.vName,' ',rd.vLastName)";
            } 
            if($option == 'ridername'){
              $option_new = "CONCAT(ru.vName,' ',ru.vLastName)";
            }
            $ssql.= " AND ".stripslashes($option_new)." LIKE '%".$generalobjAdmin->clean($keyword)."%'";
        }
    }else {
        $ssql.= " AND (t.vRideNo LIKE '%".$generalobjAdmin->clean($keyword)."%' OR  concat(rd.vName,' ',rd.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR concat(ru.vName,' ',ru.vLastName) LIKE '%".$generalobjAdmin->clean($keyword)."%' OR r.vRating1 LIKE '%".$generalobjAdmin->clean($keyword)."%')";
    }
}
// End Search Parameters


//Pagination Start
$chkusertype ="";
if($type == "Driver")
{
	$chkusertype = "Passenger";
}
else
{
	$chkusertype = "Driver";
}
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT count(r.iRatingId) as Total
			FROM ratings_user_driver as r 
			LEFT JOIN trips as t ON r.iTripId=t.iTripId 
			LEFT JOIN register_driver as rd ON rd.iDriverId=t.iDriverId 
			LEFT JOIN register_user as ru ON ru.iUserId=t.iUserId 
			WHERE eUserType='".$chkusertype."' And ru.eStatus!='Deleted' $ssql $adm_ssql"; 
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
$chkusertype ="";
if($type == "Driver")
{
	$chkusertype = "Passenger";
}
else
{
	$chkusertype = "Driver";
}	

 $sql = "SELECT r.iRatingId,r.iTripId,r.vRating1,r.tDate,r.eUserType,r.vMessage,CONCAT(rd.vName,' ',rd.vLastName) as driverName ,rd.vAvgRating,CONCAT(ru.vName,' ',ru.vLastName) as passangerName,ru.vAvgRating as passangerrate,t.iDriverId,t.iUserId,t.vRideNo 
FROM ratings_user_driver as r 
LEFT JOIN trips as t ON r.iTripId=t.iTripId 
LEFT JOIN register_driver as rd ON rd.iDriverId=t.iDriverId 
LEFT JOIN register_user as ru ON ru.iUserId=t.iUserId 
WHERE 1=1 AND r.eUserType='".$chkusertype."' And ru.eStatus!='Deleted'  $ssql $adm_ssql $ord LIMIT $start, $per_page";
			
//$ssql $adm_ssql $ord
$data_drv = $obj->MySQLSelect($sql);
//echo "<pre>";
//print_r($data_drv);
//exit;
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
                                <h2>Reviews</h2>
                                <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php  include('valid_msg.php'); ?>
					<div class="panel-heading">
                     
					
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                              <tbody>
                                <tr>
                                    <td width="5%"><label for="textfield"><strong>Search:</strong></label></td>
                                    <td width="10%" class=" padding-right10"><select name="option" id="option" class="form-control">
                                          <option value="">All</option>
                                          <option value="t.vRideNo" <?php  if ($option == "t.vRideNo") { echo "selected"; } ?> >Ride Number</option>
                                          <option value="drivername" <?php  if ($option == "drivername") {echo "selected"; } ?> >Driver Name</option>
                                          <option value="ridername" <?php  if ($option == "ridername") {echo "selected"; } ?> >Rider Name</option>
                                          <option value="r.vRating1" <?php  if ($option == 'r.vRating1') {echo "selected"; } ?> >Rate</option>
                                    </select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php  echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='review.php'"/>
                                      <?php  if(!empty($data_drv)) { ?>
                                      <button type="button" onClick="showExportTypes('review')" class="panel-heading-av" >Export</button>
                                      <?php  } ?>
                                    </td>
                                    
                                </tr>
                              </tbody>
                        </table>
						<?php  
						if(count($data_drv) > 0){ ?>
						<div class="panel-heading ">
							<form name="_export_form" id="_export_form" method="post" >
								
							</form>
					   </div>
					  <?php   } ?>
                        
                      </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                
                                    <div style="clear:both;"></div>
                                        <div class="table-responsive">
                                            <form class="_list_form" id="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
											<div class="panel-heading">
                                                  <ul class="nav nav-tabs">
													  <li <?php  if($reviewtype=='Driver'){?> class="active" <?php  } ?>>
														  <a data-toggle="tab"  onclick="getReview('Driver')"  href="#home" ><?=$langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'];?></a></li>
													  <li <?php  if($reviewtype=='Passenger'){?> class="active" <?php  } ?>>
														  <a data-toggle="tab" onClick="getReview('Passenger')"  href="#menu1"><?=$langage_lbl_admin['LBL_DASHBOARD_USERS_ADMIN'];?></a></li>
												  </ul>
											</div>
											<table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                                            <thead>
                                                                 <tr>
																 <th><a href="javascript:void(0);" onClick="Redirect(1,<?php  if($sortby == '1'){ echo $order; }else { ?>0<?php  } ?>)"><?=$langage_lbl_admin['LBL_RIDE_TXT_ADMIN'];?> Number <?php  if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?>  <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
																	<?php  if($reviewtype=='Driver'){?>
                                                                      <th width="15%"><a href="javascript:void(0);" onClick="Redirect(2,<?php  if($sortby == '2'){ echo $order; }else { ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?>  Name (Average Rate) <?php  if ($sortby == 2) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
																	  
                                                                      <th width="15%"><a href="javascript:void(0);" onClick="Redirect(6,<?php  if($sortby == '6'){ echo $order; }else { ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?>  Name <?php  if ($sortby == 6) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
																	  
																	<?php  }else{?>
																	   <th width="15%"><a href="javascript:void(0);" onClick="Redirect(2,<?php  if($sortby == '2'){ echo $order; }else { ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN'];?>   Name(Average Rate) <?php  if ($sortby == 2) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
																	   <th width="15%"><a href="javascript:void(0);" onClick="Redirect(6,<?php  if($sortby == '6'){ echo $order; }else { ?>0<?php  } ?>)"><?php  echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];?> Name <?php  if ($sortby == 6) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?>  <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
																	<?php }?>
																	
                                                                      <th width="8%" class="align-center"><a href="javascript:void(0);" onClick="Redirect(3,<?php  if($sortby == '3'){ echo $order; }else { ?>0<?php  } ?>)">Rate <?php  if ($sortby == 3) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                                     
                                                                      <th width="15%" class="align-center"><a href="javascript:void(0);" onClick="Redirect(4,<?php  if($sortby == '4'){ echo $order; }else { ?>0<?php  } ?>)">Date <?php  if ($sortby == 4) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?> <i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
																	  
																	  <th width="15%"><a href="javascript:void(0);" onClick="Redirect(5,<?php  if($sortby == '5'){ echo $order; }else { ?>0<?php  } ?>)">Comment <?php  if ($sortby == 5) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?>  <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
																	  
                                                                      <th width="3%">DELETE</th>
                                                                 </tr>
                                                            </thead>
															<tbody>
                                                    <?php  
                                                    if(!empty($data_drv)) {
                                                    for ($i = 0; $i < count($data_drv); $i++) { 
                                                        
                                                        $default = '';
                                                        if(isset($data_drv[$i]['eDefault']) && $data_drv[$i]['eDefault']=='Yes'){
                                                                $default = 'disabled';
                                                        } ?>
                                                    <tr class="gradeA">
																 <td width="10%"><?php  echo $data_drv[$i]['vRideNo']; ?></td>

																 <?php  if($reviewtype=='Driver'){?>
																		
                                                                      <td><?php  echo $generalobjAdmin->clearName($data_drv[$i]['driverName']);

																		  echo " <b dir='ltr'> ( ".$data_drv[$i]['vAvgRating']." )</b>";
																		  ?></td>
																	  
                                                                      <td><?php  echo $generalobjAdmin->clearName($data_drv[$i]['passangerName']); ?></td>
																	  <?php  }else{?>
																	  <td><?php  echo $generalobjAdmin->clearName($data_drv[$i]['passangerName']); 
																		   echo " <b dir='ltr'>( ".$data_drv[$i]['passangerrate']." ) </b>";
																		  ?></td>
																	   <td><?php  echo $generalobjAdmin->clearName($data_drv[$i]['driverName']); ?></td>
                                                                      
																	  <?php }?>
																	    <td align="center"> <?= $data_drv[$i]['vRating1'] ?> </td>
                                                                     
                                                                    
                                                                      <td align="center" ><?= $generalobjAdmin->DateTime($data_drv[$i]['tDate']);?></td>
                                                                      <td > <?= $data_drv[$i]['vMessage'] ?></td>  
																	   <td align="center" >
																			<a href="javascript:void(0);" onClick="changeStatusDelete('<?php  echo $data_drv[$i]['iRatingId']; ?>')"  data-toggle="tooltip" title="Delete">
                                                                                <img src="img/delete-icon.png" alt="Delete" >
                                                                            </a>
                                                                      </td>
                                                                 </tr>
                                                    <?php  } }else { ?>
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
                                            Review module will list all reviews on this page.
                                    </li>
                                    <li>
                                            Administrator can Delete any review.
                                    </li>
                            </ul>
                    </div>
                    </div>
                </div>
                <!--END PAGE CONTENT -->
            </div>
            <!--END MAIN WRAPPER -->
            
<form name="pageForm" id="pageForm" action="action/review.php" method="post" >
<input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
<input type="hidden" name="iRatingId" id="iMainId01" value="" >
<input type="hidden" name="reviewtype" id="reviewtype" value="<?php  echo $reviewtype; ?>" >
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
			function getReview(type)
			{
				$('#reviewtype').val(type);
				var action = $("#_list_form").attr('action');
                var formValus = $("#pageForm").serialize();
                window.location.href = action+"?"+formValus;
			}
            
        </script>
		 <script>
		  
	</script>
    </body>
    <!-- END BODY-->
</html>