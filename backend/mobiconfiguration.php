<?php  
$tconfig["tsite_folder"] = ($_SERVER["HTTP_HOST"] == "localhost")?"/uber-app/web-new/":"/uber-app/web-new/";
$tconfig["tsite_url"] = "http://".$_SERVER["HTTP_HOST"].$tconfig["tsite_folder"];
$tconfig["tpanel_path"] = $_SERVER["DOCUMENT_ROOT"]."".$tconfig["tsite_folder"];
$tconfig["tsite_libraries"] = $tconfig["tsite_url"]."assets/libraries/";
$tconfig["tsite_libraries_v"] = $tconfig["tpanel_path"]."assets/libraries/";
$tconfig["tsite_img"] = $tconfig["tsite_url"]."assets/img";   

$tconfig["tsite_home_images"] = $tconfig["tsite_img"]."/home/";   
$tconfig["tsite_upload_images"] = $tconfig["tsite_img"]."/images/";   
$tconfig["tsite_upload_images_panel"] = $tconfig["tpanel_path"]."assets/img/images";


//Start ::Company folder
$tconfig["tsite_upload_images_compnay_path"] = $tconfig["tpanel_path"]."webimages/upload/Company";
$tconfig["tsite_upload_images_compnay"] = $tconfig["tsite_url"]."webimages/upload/Company";
//End ::Company folder


/* To upload compnay documents */
$tconfig["tsite_upload_compnay_doc_path"] = $tconfig["tpanel_path"]."webimages/upload/documents/company";
$tconfig["tsite_upload_compnay_doc"] = $tconfig["tsite_url"]."webimages/upload/documents/company";
$tconfig["tsite_upload_documnet_size1"] = "250";
$tconfig["tsite_upload_documnet_size2"] = "800";

//Start ::Driver folder
$tconfig["tsite_upload_images_driver_path"] = $tconfig["tpanel_path"]."webimages/upload/Driver";
$tconfig["tsite_upload_images_driver"] = $tconfig["tsite_url"]."webimages/upload/Driver";

/* To upload driver documents */
$tconfig["tsite_upload_driver_doc_path"] = $tconfig["tpanel_path"]."webimages/upload/documents/driver";
$tconfig["tsite_upload_driver_doc"] = $tconfig["tsite_url"]."webimages/upload/documents/driver";

//Start ::Passenger Profile Image
$tconfig["tsite_upload_images_passenger_path"] = $tconfig["tpanel_path"]."webimages/upload/Passenger";
$tconfig["tsite_upload_images_passenger"] = $tconfig["tsite_url"]."webimages/upload/Passenger";

/* To upload images for static pages */
$tconfig["tsite_upload_page_images"] = $tconfig["tsite_img"]."/page/";
$tconfig["tsite_upload_page_images_panel"] = $tconfig["tpanel_path"]."assets/img/page";

/* To upload passenger Docunment */
$tconfig["tsite_upload_vehicle_doc"] = $tconfig["tpanel_path"]."webimages/upload/documents/vehicles/";
$tconfig["tsite_upload_vehicle_doc_panel"] = $tconfig["tsite_url"]."webimages/upload/documents/vehicles/";

/* To upload driver documents */
//$tconfig["tsite_upload_driver_doc"] = $tconfig["tsite_upload_vehicle_doc"]."driver/";
//$tconfig["tsite_upload_driver_doc_panel"] = $tconfig["tsite_upload_vehicle_doc_panel"]."driver/";




$tconfig["tsite_upload_images_member_size1"] = "64";
$tconfig["tsite_upload_images_member_size2"] = "130";
$tconfig["tsite_upload_images_member_size3"] = "256";
$tconfig["tsite_upload_images_member_size4"] = "512"; 
?>