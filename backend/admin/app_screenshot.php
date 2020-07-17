<?php 
include_once('../common.php');
if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

$default_lang = $generalobj->get_default_lang();
$hdn_del_id = isset($_POST['hdn_del_id']) ? $_POST['hdn_del_id'] : '';
$appId = isset($_GET['id']) ? $_GET['id'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$tbl_name = 'home_screens';
$script = "Settings";

if ($hdn_del_id != '') {
    if (SITE_TYPE != 'Demo') {
        $query = "DELETE FROM `" . $tbl_name . "` WHERE iId ='" . $hdn_del_id . "'";
        $obj->sql_query($query);
    } else {
        header("Location:app_screenshot.php?success=2");
        exit;
    }
}
if ($appId != '' && $status != '') {
    if (SITE_TYPE != 'Demo') {
        $query = "UPDATE `" . $tbl_name . "` SET eStatus = '" . $status . "' WHERE iId = '" . $appId . "'";
        $obj->sql_query($query);
    } else {

        header("Location:app_screenshot.php?success=2");
        exit;
    }
}


$sql = "SELECT * FROM " . $tbl_name . " ORDER BY iId DESC";
$db_data = $obj->MySQLSelect($sql);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->

    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title>Admin | App ScreenShort</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />

        <?php  include_once('global_files.php');?>
        <script type="text/javascript">
            function confirm_delete()
            {
                var confirm_ans = confirm("Are You sure You want to Delete this Page?");
                return confirm_ans;

            }
        </script>
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53 " >

        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php  include_once('header.php'); ?>
            <?php  include_once('left_menu.php'); ?>

            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>App ScreenShot Page</h2>
                            <a href="appscreenshot_action.php">
                                <input type="button" value="Add Screenshort" class="add-btn">
                            </a>
                        </div>
                    </div>
                    <hr />
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        App ScreenShot
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                                <thead>
                                                    <tr>														
                                                        <th>Screen Title</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 

                                                    if(!empty($db_data)){

                                                    foreach($db_data as $value){				

                                                    $iId = $value['iId'];
                                                    $vImageTitle = $value['vImageTitle'];
                                                    $eStatus = $value['eStatus'];															
                                                    ?>
                                                    <tr class="gradeA">
                                                        <td width="40%" ><?= $vImageTitle; ?></td>
                                                        <td width="20%" align="center">
                                                            <?php  if($eStatus == 'Active') {
                                                            $dis_img = "img/active-icon.png";
                                                            }else if($eStatus == 'Inactive'){
                                                            $dis_img = "img/inactive-icon.png";
                                                            }else if($eStatus == 'Deleted'){
                                                            $dis_img = "img/delete-icon.png";
                                                            }?>
                                                            <img src="<?= $dis_img; ?>" alt="<?= $eStatus; ?>">
                                                        </td >

                                                        <td width="25%" align="center" class="veh_act">
                                                            <a href="appscreenshot_action.php?id=<?= $iId; ?>">
                                                                <button class="remove_btn001" data-toggle="tooltip" title="Edit App Screenshot">
                                                                    <img src="img/edit-icon.png" alt="Edit">
                                                                </button>
                                                            </a>

                                                            <a href="app_screenshot.php?id=<?= $iId; ?>&status=Active" data-toggle="tooltip" title="Active App Screenshot">
                                                                <img src="img/active-icon.png" alt="<?php  echo $eStatus ?>" >
                                                            </a>
                                                            <a href="app_screenshot.php?id=<?= $iId; ?>&status=Inactive" data-toggle="tooltip" title="Inactive App Screenshot">
                                                                <img src="img/inactive-icon.png" alt="<?php  echo $eStatus; ?>" >
                                                            </a>

                                                            <form name="delete_form" id="delete_frm" method="post" action="" onSubmit="return confirm_delete()" class="margin0">
                                                                <input type="hidden" name="hdn_del_id" id="hdn_del_id" value="<?= $iId; ?>">
                                                                <button class="remove_btn001" data-toggle="tooltip" title="Delete App Screenshot">
                                                                    <img src="img/delete-icon.png" alt="Delete">
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <?php  }
                                                    } else { ?>
                                                            <!--<tr class="gradeA">
                                                                    <td colspan="4">No Records found.</td>
                                                            </tr>-->
                                                    <?php  } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div> <!--TABLE-END-->
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>

            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->

        <?php  include_once('footer.php');?>
        <script src="../assets/plugins/dataTables/jquery.dataTables.js"></script>
        <script src="../assets/plugins/dataTables/dataTables.bootstrap.js"></script>
        <script>
            $(document).ready(function () {
                $('#dataTables-example').dataTable();
            });
        </script>
    </body>
    <!-- END BODY-->
</html>
