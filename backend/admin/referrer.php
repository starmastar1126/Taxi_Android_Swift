<?php 
include_once('../common.php');

if ($REFERRAL_SCHEME_ENABLE == "No") {
    header('Location: dashboard.php');
    exit;
}

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}

$generalobjAdmin->check_member_login();
$script = 'referrer';
$type = (isset($_REQUEST['reviewtype']) && $_REQUEST['reviewtype'] != '') ? $_REQUEST['reviewtype'] : 'Driver';

//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY uw.iUserWalletId DESC';

if($sortby == 1){
    if($type == 'Driver') {
        if($order == 0)
            $ord = " ORDER BY rd.vName ASC";
        else
            $ord = " ORDER BY rd.vName DESC";
    }else {
        if($order == 0)
            $ord = " ORDER BY ru.vName ASC";
        else
            $ord = " ORDER BY ru.vName DESC";  
    }
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
        $ssql.= " AND (concat(ad.vFirstName,' ',ad.vLastName) LIKE '%".$keyword."%' OR ad.vEmail LIKE '%".$keyword."%' OR ag.vGroup LIKE '%".$keyword."%' OR ad.vContactNo LIKE '%".$keyword."%' OR ad.eStatus LIKE '%".$keyword."%')";
    }
}
// End Search Parameters

//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT uw.iUserWalletId
        FROM user_wallet as uw
        LEFT JOIN register_driver as rd ON rd.iDriverId=uw.iUserId
        LEFT JOIN register_user as ru ON ru.iUserId=uw.iUserId
        WHERE eUserType='" . $type . "' AND eFor = 'Referrer' $ssql GROUP BY uw.iUserId $ord";
$totalData = $obj->MySQLSelect($sql);
// $total_results = $totalData[0]['Total'];
$total_results = count($totalData);
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

$sql = "SELECT uw.*,CONCAT(rd.vName,' ',rd.vLastName) AS driverName,rd.eRefType as eRefType ,rd.iDriverId as rduserid ,CONCAT(ru.vName,' ',ru.vLastName) AS passangerName,ru.eRefType as eRefType , ru.iUserId as ruuserid
        FROM user_wallet as uw
        LEFT JOIN register_driver as rd ON rd.iDriverId=uw.iUserId
        LEFT JOIN register_user as ru ON ru.iUserId=uw.iUserId
        WHERE eUserType='" . $type . "' AND eFor = 'Referrer' $ssql GROUP BY uw.iUserId $ord LIMIT $start, $per_page"; //GROUP BY uw.iUserId 
$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
$var_filter = "";
foreach ($_REQUEST as $key=>$val) {
    if($key != "tpages" && $key != 'page')
    $var_filter.= "&$key=".stripslashes($val);
}

$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages.$var_filter;

if (isset($data_drv[0]['eUserType']) && $data_drv[0]['eUserType'] != null) {
    $data_drv[0]['eUserType'] = $type;
}
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : '';
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?=$SITE_NAME?> | Referral Report</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
        <?php  include_once('global_files.php');?>
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53">
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
                                <h2>Referral Report</h2>
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php  include('valid_msg.php'); ?>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading referrer-page-tab">
                                        <ul class="nav nav-tabs">
                                            <li <?php  if ($type == 'Driver') { ?> class="active" <?php  } ?>>
                                                <a data-toggle="tab"  onclick="getReview('Driver')"  href="#home" ><?= $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></a></li>
                                            <li <?php  if ($type == 'Rider') { ?> class="active" <?php  } ?>>
                                                <a data-toggle="tab" onClick="getReview('Rider')"  href="#menu1"><?= $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?></a></li>
                                        </ul>
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <form class="_list_form" id="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="35%"><a href="javascript:void(0);" onClick="Redirect(1,<?php  if($sortby == '1'){ echo $order; }else { ?>0<?php  } ?>)">Member Name <?php  if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th>
                                                        <th width="25%">Total Members Referred</th>
                                                        <th width="25%">Total Amount Earned <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Amount earned in wallet once refferal do a successful first trip.'></i></th>																	
                                                        <th width="15%">Detail</th>																	
                                                    </tr>
                                                </thead>
                                                <tbody>                                                               
                                                    <?php 
                                                    if (!empty($data_drv)) {
                                                        for ($i = 0; $i < count($data_drv); $i++) {
                                                            //if($data_drv[$i]['eRefType'] == "Driver"){
                                                            if ($type == "Driver") {

                                                                $eUserType = $data_drv[$i]['eUserType'];
                                                                $id = $data_drv[$i]['rduserid'];
                                                            } else {

                                                                $eUserType = $data_drv[$i]['eUserType'];
                                                                $id = $data_drv[$i]['ruuserid'];
                                                            }

                                                            $query_driver = "SELECT rd.iRefUserId FROM register_driver as rd LEFT JOIN user_wallet as uw ON rd.iRefUserId=uw.iUserId WHERE uw.eUserType='".$eUserType."' AND uw.iUserId='".$id."' AND uw.eFor = 'Referrer' AND rd.eRefType = '".$eUserType."' GROUP BY rd.iDriverId";
                                                            $result_driver = $obj->MySQLSelect($query_driver);

                                                            $query_reider = "SELECT urd.iRefUserId FROM register_user as urd LEFT JOIN user_wallet as uw ON urd.iRefUserId=uw.iUserId WHERE uw.eUserType='".$eUserType."' AND uw.iUserId='".$id."' AND uw.eFor = 'Referrer' AND urd.eRefType = '".$eUserType."' GROUP BY urd.iUserId";
                                                            $result_rider = $obj->MySQLSelect($query_reider);
                                                            
                                                            $totalreffer = count($result_driver) + count($result_rider);

                                                            $totalbalance = $generalobj->getTotalbalance($id, $eUserType);
                                                            //$totalreffer = $generalobj->getTotalReferrer($id, $eUserType);
                                                            $data_drv[$i]['totalbalance'] = $totalbalance;
                                                            $data_drv[$i]['totalreffer'] = $totalreffer;
                                                            ?>
                                                            <tr class="gradeA">
                                                            <?php  if ($data_drv[0]['eUserType'] == 'Driver') { ?>
                                                                    <td><?php  echo $generalobjAdmin->clearName($data_drv[$i]['driverName']); ?></td>

                                                                    <?php  } else { ?>

                                                                    <td><?php  echo $generalobjAdmin->clearName($data_drv[$i]['passangerName']); ?></td>

                                                                    <?php  } ?>                                            

                                                                    <td> <?= $data_drv[$i]['totalreffer']; ?></td>  
                                                                    <td> <?= $generalobj->trip_currency($data_drv[$i]['totalbalance']); ?></td>  
                                                                    <td>
                                                                    <?php  if ($data_drv[0]['eUserType'] == 'Driver') { ?>
                                                                            <a href="referrer_action.php?id=<?php  echo $data_drv[$i]['rduserid']; ?>&eUserType=Driver" data-toggle="tooltip" title="View Details">
                                                                                <img src="img/view-details.png" alt="View Details">
                                                                            </a>
                                                                            <?php  } else { ?>
                                                                            <a href="referrer_action.php?id=<?php  echo $data_drv[$i]['ruuserid']; ?>&eUserType=Rider" data-toggle="tooltip" title="View Details">
                                                                                <img src="img/view-details.png" alt="View Details">
                                                                            </a>
                                                                    <?php  } ?>
                                                        </td>
                                                    </tr>
                                                    <?php  }}else { ?> 													
                                                        <tr class="gradeA">
                                                            <td colspan="4"> No Records Found.</td>
                                                        </tr>
                                                    <?php  } ?>
                                                </tbody>
                                            </table>
                                            </form>
                                            <?php  include('pagination_n.php'); ?>
                                            <form name="frmreview" id="frmreview" method="post" action="">
                                                <input type="hidden" name="reviewtype" value="" id="reviewtype">
                                                <input type="hidden" name="action" value="" id="action">
                                                <!--<input type="hidden" name="iRatingId" value="" id="iRatingId">-->
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div> <!--TABLE-END-->
                        </div>
                    </div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->

<form name="pageForm" id="pageForm" action="action/admin.php" method="post" >
<input type="hidden" name="page" id="page" value="<?php  echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php  echo $tpages; ?>">
<input type="hidden" name="iAdminId" id="iMainId01" value="" >
<input type="hidden" name="reviewtype" value="<?php  echo $type; ?>" >
<input type="hidden" name="status" id="status01" value="" >
<input type="hidden" name="statusVal" id="statusVal" value="" >
<input type="hidden" name="option" value="<?php  echo $option; ?>" >
<input type="hidden" name="keyword" value="<?php  echo $keyword; ?>" >
<input type="hidden" name="sortby" id="sortby" value="<?php  echo $sortby; ?>" >
<input type="hidden" name="order" id="order" value="<?php  echo $order; ?>" >
<input type="hidden" name="method" id="method" value="" >
</form>
<?php  include_once('footer.php');?>
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
            
            function getReview(type)
            {

                $('#reviewtype').val(type);
                document.frmreview.submit();

            }
        </script>
    </body>
    <!-- END BODY-->
</html>
