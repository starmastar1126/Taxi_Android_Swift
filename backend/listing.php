<?php 
include_once('common.php');
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>"> <!--<![endif]-->

<!-- BEGIN HEAD-->
<head>
	<meta charset="UTF-8" />
    <title><?=$SITE_NAME?> |<?=$langage_lbl['LBL_BLANK_PAGE']; ?> </title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
	<meta content="" name="keywords" />
	<meta content="" name="description" />
	<meta content="" name="author" />
    <link rel="stylesheet" href="assets/css/bootstrap-fileupload.min.css" />
    <?php  include_once('global_files.php');?>
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
            <div class="inner" style="min-height:600px;">
                <div class="row">
                    <div class="col-lg-12">
                        <h2><?=$langage_lbl['LBL_LEFT_MENU_VEHICLES']; ?><a href="#" class="small-fonts-head">+<?=$langage_lbl['LBL_ADD_VEHICLE']; ?></a></h2>
                         <hr>
                        <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default notification-listing">
                        <div class="panel-body notification-listing-inner">
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. <a href="#" class="alert-link">Alert Link</a>.
                            </div>
                             <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. <a href="#" class="alert-link">Alert Link</a>.
                            </div>
                             <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. <a href="#" class="alert-link">Alert Link</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                       
						<div class="box dark image-toggle-box">
        <header class="image-toggle">
            <div class="icons"><i class="icon-th-list"></i></div>
            <h5>AUDI A6   &nbsp;&nbsp;<span class="small">None 1234</span></h5>
            <div class="toolbar">
                <ul class="nav">
                    <li>
                        <a class="accordion-toggle minimize-box" data-toggle="collapse" href="#div-1">
                            <i class="icon-chevron-up"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </header>
        <div id="div-1" class="tabing-inner-pageaccordion-body collapse in body ">
            <div class="documents">
            	<div class="row">
                	<div class="col-sm-12">
                    	<h3><?=$langage_lbl['LBL_DOCUMENTS']; ?>	</h3>
                    </div>
                </div>    
                <div class="row">
                	<div class="col-sm-3">
                <form class="form-horizontal">
                    	 <div class="form-group">
                        <div class="col-lg-12">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px; "><?=$langage_lbl['LBL_INSURANCE']; ?>  <?=$langage_lbl['LBL_IMAGE'];?></div>
                                <div>
                                    <span class="btn btn-file btn-success"><span class="fileupload-new"><?=$langage_lbl['LBL_UPLOAD_INSURANCE']; ?></span><span class="fileupload-exists"><?=$langage_lbl['LBL_Driver_document_CHANGE']; ?></span><input type="file" /></span>
                                    <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><?=$langage_lbl['LBL_REMOVE_TEXT']; ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                   </form>
                    </div>
                    <div class="col-sm-3">
                    <form class="form-horizontal">
                    	 <div class="form-group">
                        <div class="col-lg-12">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px; "><?=$langage_lbl['LBL_VEHICLE_PERMIT_IMAGE']; ?></div>
                                <div>
                                    <span class="btn btn-file btn-success"><span class="fileupload-new"><?=$langage_lbl['LBL_UPLOAD_PERMIT']; ?></span><span class="fileupload-exists"><?=$langage_lbl['LBL_Driver_document_CHANGE']; ?></span><input type="file" /></span>
                                    <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><?=$langage_lbl['LBL_REMOVE_TEXT']; ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                   </form>
                    </div>
                    <div class="col-sm-3">
                    <form class="form-horizontal">
                    	 <div class="form-group">
                        <div class="col-lg-12">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-preview thumbnail" style="width: 200px; height: 150px; "><?=$langage_lbl['LBL_VEHICLE_REGI_IMAGE']; ?></div>
                                <div>
                                    <span class="btn btn-file btn-success"><span class="fileupload-new"><?=$langage_lbl['LBL_UPLOAD_REGISTEATION']; ?></span><span class="fileupload-exists"><?=$langage_lbl['LBL_CHANGE']; ?></span><input type="file" /></span>
                                    <a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload"><?=$langage_lbl['LBL_REMOVE_TEXT']; ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                   </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

                    </div>
                </div>
                <hr />
            </div>
		</div>
       <!--END PAGE CONTENT -->
    </div>
     <!--END MAIN WRAPPER -->

	<?php  include_once('footer.php');?>
   
     <script src="assets/plugins/jasny/js/bootstrap-fileupload.js"></script>
     <script src="assets/js/notifications.js"></script>
</body>
	<!-- END BODY-->    
</html>
