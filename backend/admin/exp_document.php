<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();
$script = 'Make';

//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY doc.ex_date DESC';
if($sortby == 1){
  if($order == 0)
  $ord = " ORDER BY doc.ex_date ASC";
  else
  $ord = " ORDER BY dm.doc_name DESC";
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
        $ssql.= " AND (dm.doc_name LIKE '%".$keyword."%' OR doc.doc_usertype LIKE '%".$keyword."%')";
    }
}

if($option == "eStatus"){	
	 $eStatussql = " AND doc.status = '".ucfirst($keyword)."'";
}else{
 $eStatussql = " AND doc.status != 'Deleted'";
}
// End Search Parameters

//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
//$sql = "SELECT COUNT(iMakeId) AS Total FROM make WHERE 1=1 $eStatussql $ssql";
$ToDate = date('Y-m-d', strtotime("-1 days"));

$sql ="SELECT COUNT(doc.doc_id) AS Total FROM document_list as doc 
Left Join document_master as dm  ON doc.doc_masterid = dm.doc_masterid 
WHERE  doc.ex_date != '0000-00-00'  AND doc.ex_date <= '$ToDate' AND doc.ex_date BETWEEN '".date('Y-m', strtotime(date('Y-m-d')))."-"."01"." 00:00:00' AND '".date('Y-m', strtotime(date('Y-m-d')))."-"."31"." 23:59:59'  $eStatussql $ssql";
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

$sql_query = "SELECT doc.ex_date,doc.status as DocumentStatus,doc.doc_usertype,dm.doc_name as DocumentName,cmp.vEmail as cmpEmail,cmp.iCompanyId as cmpid,cmp.eStatus as CmpStatus,rd.eStatus as DriverStatus,rd.iDriverId as driverid,rd.vEmail,cmp.vCompany as CmpName, CONCAT(rd.vName,' ',rd.vLastName) as DriverName FROM document_list as doc 
Left Join company as cmp  ON doc.doc_userid = cmp.iCompanyId 
Left Join register_driver as rd  ON doc.doc_userid = rd.iDriverId 
Left Join document_master as dm  ON doc.doc_masterid = dm.doc_masterid 
WHERE  doc.ex_date != '0000-00-00'  AND 
doc.ex_date <= '$ToDate' AND doc.ex_date BETWEEN '".date('Y-m', strtotime(date('Y-m-d')))."-"."01"." 00:00:00' AND '".date('Y-m', strtotime(date('Y-m-d')))."-"."31"." 23:59:59' $eStatussql $ssql $ord LIMIT $start, $per_page";
//doc.ex_date LIKE '%$ToDate%'
$data_ex_doc = $obj->MySQLSelect($sql_query);
//echo "<pre>"; print_r($data_ex_doc); exit;
$endRecord = count($data_ex_doc);

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
       <title><?=$SITE_NAME?> | <?php  echo $langage_lbl_admin['LBL_EXPIRY_DOMENT_TXT'];?></title>
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
                                <h2><?php  echo $langage_lbl_admin['LBL_EXPIRY_DOMENT_TXT'];?></h2>                               
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
                                          <option value="dm.doc_name" <?php  if ($option == "dm.doc_name") { echo "selected"; } ?> >Document Name</option>  
										   <option value="doc.doc_usertype" <?php  if ($option == "doc.doc_usertype") { echo "selected"; } ?> >Document Type</option> 
                                          <!--<option value="doc.ex_date" <?php  if ($option == "doc.ex_date") { echo "selected"; } ?> >Expiry Date</option>-->
                                          
                                    </select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php  echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='exp_document.php'"/>
                                    </td>                                   
                                </tr>
                              </tbody>
                        </table>
                        
                      </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="admin-nir-export">
                                    <div class="changeStatus col-lg-12 option-box-left">
                                   
                                    </div>
                                    <?php  if(!empty($data_drv)) {?>
                                   <!-- <div class="panel-heading">
                                        <form name="_export_form" id="_export_form" method="post" >
                                            <button type="button" onclick="showExportTypes('make')" >Export</button>
                                        </form>
                                   </div>-->
                                   <?php  } ?>
                                    </div>
                                    <div style="clear:both;"></div>
                                        <div class="table-responsive">
                                            <form class="_list_form" id="_list_form" method="post" action="<?php  echo $_SERVER['PHP_SELF'] ?>">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>                                                       
                                                        <th width="20%"><a href="javascript:void(0);" onClick="Redirect(1,<?php  if($sortby == '1'){ echo $order; }else { ?>0<?php  } ?>)">Document Name <?php  if ($sortby == 1) { if($order == 0) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php  } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php  } }else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php  } ?></a></th> 											 
														 
														<th>Document Type </th> 
														<th>Driver Name /Company Name </th> 
														<th>Vehicle Name</th>
														<th> Ex.Date </th> 
													<!--	<th>Document Status </th> -->
														<th>Driver/Company Status</th> 
														<th width="8%" align="center" style="text-align:center;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php  
													
                                                    if(!empty($data_ex_doc)) {
                                                    for ($i = 0; $i < count($data_ex_doc); $i++) {
													
													//	echo $data_ex_doc[$i]['DocumentStatus'];  echo "<br>";
														$Username = ($data_ex_doc[$i]['doc_usertype'] == "driver" || $data_ex_doc[$i]['doc_usertype'] == "car" ) ? $data_ex_doc[$i]['DriverName'] : $data_ex_doc[$i]['CmpName'];
														
														$Status = ($data_ex_doc[$i]['doc_usertype'] == "driver") ? ucfirst($data_ex_doc[$i]['DriverStatus']) : $data_ex_doc[$i]['CmpStatus'];
														
														$btnlink = ($data_ex_doc[$i]['doc_usertype'] == "driver") ? "driver_document_action.php?id=". $data_ex_doc[$i]['driverid']."&action=edit&user_type=driver":"company_document_action.php?id=".$data_ex_doc[$i]['cmpid']."&action=edit" ;
														
														 if($data_ex_doc[$i]['doc_usertype'] == "driver" || $data_ex_doc[$i]['doc_usertype'] == "car"){
														
															$sql_query = "SELECT m.vMake, md.vTitle FROM 
															register_driver as rd
															Left Join driver_vehicle as dv ON dv.iDriverVehicleId = rd.iDriverVehicleId Left Join model as md ON dv.iModelId = md.iModelId
															Left Join make as m ON dv.iMakeId = m.iMakeId Where rd.iDriverId = '".$data_ex_doc[$i]['driverid']."'"; 
															$data_car_detail = $obj->MySQLSelect($sql_query);
															} 
														
														?>
                                                    <tr class="gradeA">
                                                        <td><?= $data_ex_doc[$i]['DocumentName']; ?></td>
                                                        <td><?= $data_ex_doc[$i]['doc_usertype']; ?></td>
                                                        <td><?= $Username; ?></td>
														<?php   $CarDetail = ($data_ex_doc[$i]['doc_usertype'] == "driver" || $data_ex_doc[$i]['doc_usertype'] == "car" ) ? $data_car_detail[0]['vMake'].'-'.$data_car_detail[0]['vTitle'] :'--'; ?>
                                                        <td><?= $CarDetail; ?></td>
                                                        <td><?= $data_ex_doc[$i]['ex_date']; ?></td>
                                                        
                                                       <!-- <td align="center" style="text-align:center;">
                                                                <?php  if($data_ex_doc[$i]['DocumentStatus'] == 'Active') {
                                                                $dis_img_doc = "img/active-icon.png";
                                                                }else if($data_ex_doc[$i]['DocumentStatus'] == 'Inactive'){
                                                                $dis_img_doc = "img/inactive-icon.png";
                                                                }else if($data_ex_doc[$i]['DocumentStatus'] == 'Deleted'){
                                                                $dis_img_doc = "img/delete-icon.png";
                                                                }else{
																$dis_img_doc = '--';
																}
																if(!empty($data_ex_doc[$i]['DocumentStatus'])){ 
																?>
																
                                                                <img src="<?= $dis_img_doc; ?>" alt="<?=$data_ex_doc[$i]['DocumentStatus'];?>" data-toggle="tooltip" title="<?=$data_ex_doc[$i]['DocumentStatus'];?>">
																<?php  }else{  echo $dis_img_doc; } ?>
                                                            </td>-->
															 <td align="center" style="text-align:center;">
                                                                <?php  if($Status == 'Active') {
                                                                $dis_img = "img/active-icon.png";
                                                                }else if($Status == 'Inactive'){
                                                                $dis_img = "img/inactive-icon.png";
                                                                }else if($Status == 'Deleted'){
                                                                $dis_img = "img/delete-icon.png";
                                                                }?>
                                                                <img src="<?= $dis_img; ?>" alt="<?=$Status;?>" data-toggle="tooltip" title="<?=$Status;?>">
                                                            </td>
															<?php   if($data_ex_doc[$i]['doc_usertype'] == "driver" || $data_ex_doc[$i]['doc_usertype'] == "company"){ ?>
															
                                                            <td align="center" style="text-align:center;" >
                                                              <a href="<?php  echo $btnlink;?> " class="btn btn-info btn-sm" target="_blank">Click</a>
                                                            </td>
															<?php  }else{ ?>
																<td align="center" style="text-align:center;" class="action-btn001">
                                                              <?php  echo '--';?>
                                                            </td>
																<?php  } ?>
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
                                            Expiry Document module will list all makes on this page.
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
<form name="pageForm" id="pageForm" action="javascript:void(0);" method="post" >
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
//               alert(action+formValus);
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