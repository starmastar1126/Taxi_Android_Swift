<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$script = 'home_content';

$id 			= isset($_GET['id'])?$_GET['id']:'';
$status 		= isset($_GET['status'])?$_GET['status']:'';
$success 		= isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;

$tbl_name 		= 'home_content';

if($id != '' && $status != ''){
	  if(SITE_TYPE !='Demo'){
	$query = "UPDATE `".$tbl_name."` SET eStatus = '".$status."' WHERE id = '".$id."'";
	$obj->sql_query($query);
}
else{
	header("Location:home_content.php?success=2");exit;
}
}
	
//Start Sorting

$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY hc.vCode ASC';

if($sortby == 1){
  if($order == 0)
  $ord = " ORDER BY hc.vCode ASC";
  else
  $ord = " ORDER BY hc.vCode DESC";
}

if($sortby == 4){
  if($order == 0)
  $ord = " ORDER BY hc.eStatus ASC";
  else
  $ord = " ORDER BY hc.eStatus DESC";
}
//End Sorting


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
        $ssql.= " AND vCode LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%'";
    }
}
// End Search Parameters

//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(id) AS Total FROM `".$tbl_name."` WHERE 1=1 AND eStatus = 'Active' $ssql $ord";
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
$sql = "SELECT hc.*,lm.vTitle FROM home_content as hc LEFT JOIN language_master as lm on lm.vCode = hc.vCode WHERE 1 = 1 AND hc.eStatus = 'Active' $ssql $ord LIMIT $start, $per_page ";
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
       <title><?=$SITE_NAME?> | Home Content</title>
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
                                <h2><?php  echo $langage_lbl_admin['LBL_HOME_CONTENT_ADMIN'];?></h2>
                                <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php  include('valid_msg.php'); ?>
                    <!-- <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                              <tbody>
                                <tr>
                                    <td width="5%"><label for="textfield"><strong>Search:</strong></label></td>
                                    <td width="10%" class=" padding-right10"><select name="option" id="option" class="form-control">
                                          <option value="">All</option>
                                          <option value="hc.vCode" <?php  if ($option == "vCode") { echo "selected"; } ?> >Language Code</option>
                                    </select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php  echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='home_content.php'"/>
                                    </td>
                                     <td width="30%"><a class="add-btn" href="home_content_action.php" style="text-align: center;">Add Home Content</a></td> 
                                </tr>
                              </tbody>
                        </table>
                        
                      </form>
					  -->
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <form class="_list_form" id="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="58%"><a href="javascript:void(0);" onClick="Redirect(1,<?php  if($sortby == '1'){ echo $order; }else { ?>0<?php  } ?>)">Title<?php  if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>	
                                                        <th align="center" style="text-align:center;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php  
													//echo '<pre>--->';print_r($data_drv);
                                                    if(!empty($data_drv)) {
                                                    for ($i = 0; $i < count($data_drv); $i++) { 
                                                        
                                                        $default = '';
                                                        if(isset($data_drv[$i]['eDefault']) && $data_drv[$i]['eDefault']=='Yes'){
                                                                $default = 'disabled';
                                                        } ?>
                                                    <tr class="gradeA">
                                                        <td>Home Page - <?= $data_drv[$i]['vTitle']; ?></td> 
														<td align="center" style="text-align:center;" class="action-btn001">
															<a href="home_content_action.php?id=<?= $data_drv[$i]['id']; ?>" data-toggle="tooltip" title="Edit">
															<img src="img/edit-icon.png" alt="Edit">
															</a>
														</td>
                                                        </tr>
                                                    <?php  } }else { ?>
                                                        <tr class="gradeA">
                                                            <td colspan="2"> No Records Found.</td>
                                                        </tr>
                                                    <?php  } ?>
                                                    </tbody>
                                                </table>
                                            </form>
                                            <?php  //include('pagination_n.php'); ?>
                                    </div>
                                </div> <!--TABLE-END-->
                            </div>
                        </div>
                    <div class="admin-notes">
                            <h4>Notes:</h4>
                            <ul>
								<li>Admin can change the Home page content as per langunage</li>
								<li>Click on Action "Edit" icon to change the content for home page</li>
                            </ul>
                    </div>
                    </div>
                </div>
                <!--END PAGE CONTENT -->
            </div>
            <!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/home_content.php" method="post" >
<input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
<input type="hidden" name="iMakeId" id="iMainId01" value="" >
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
    $("#Search").on('click', function(){
        //$('html').addClass('loading');
        var action = $("#_list_form").attr('action');
       // alert(action);
        var formValus = $("#frmsearch").serialize();
		// alert(action+formValus);
        window.location.href = action+"?"+formValus;
    });

</script>
    </body>
    <!-- END BODY-->
</html>
