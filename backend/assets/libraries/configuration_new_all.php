<?php  
$tconfig["tsite_folder"] = ($_SERVER["HTTP_HOST"] == "localhost")?"/uber-app/":"/uber-app/web-new/";
$tconfig["tsite_url"] = "http://".$_SERVER["HTTP_HOST"].$tconfig["tsite_folder"];
$tconfig["tpanel_path"] = $_SERVER["DOCUMENT_ROOT"]."".$tconfig["tsite_folder"];
$tconfig["tsite_libraries"] = $tconfig["tsite_url"]."assets/libraries/";
$tconfig["tsite_libraries_v"] = $tconfig["tpanel_path"]."assets/libraries/";
$tconfig["tsite_img"] = $tconfig["tsite_url"]."assets/img";   

$tconfig["tsite_home_images"] = $tconfig["tsite_img"]."/home/";   
$tconfig["tsite_upload_images"] = $tconfig["tsite_img"]."/images/";   
$tconfig["tsite_upload_images_panel"] = $tconfig["tpanel_path"]."assets/img/images";


//Start ::Company folder
$tconfig["tsite_upload_images_compnay_path"] = $tconfig["tpanel_path"]."webimages/".$_SESSION['sess_systype']."/upload/Company";
$tconfig["tsite_upload_images_compnay"] = $tconfig["tsite_url"]."webimages/".$_SESSION['sess_systype']."/upload/Company";
//End ::Company folder


/* To upload compnay documents */
$tconfig["tsite_upload_compnay_doc_path"] = $tconfig["tpanel_path"]."webimages/".$_SESSION['sess_systype']."/upload/documents/company";
$tconfig["tsite_upload_compnay_doc"] = $tconfig["tsite_url"]."webimages/".$_SESSION['sess_systype']."/upload/documents/company";
$tconfig["tsite_upload_documnet_size1"] = "250";
$tconfig["tsite_upload_documnet_size2"] = "800";

//Start ::Driver folder
$tconfig["tsite_upload_images_driver_path"] = $tconfig["tpanel_path"]."webimages/".$_SESSION['sess_systype']."/upload/Driver";
$tconfig["tsite_upload_images_driver"] = $tconfig["tsite_url"]."webimages/".$_SESSION['sess_systype']."/upload/Driver";

/* To upload driver documents */
$tconfig["tsite_upload_driver_doc_path"] = $tconfig["tpanel_path"]."webimages/".$_SESSION['sess_systype']."/upload/documents/driver";
$tconfig["tsite_upload_driver_doc"] = $tconfig["tsite_url"]."webimages/".$_SESSION['sess_systype']."/upload/documents/driver";

//Start ::Passenger Profile Image
$tconfig["tsite_upload_images_passenger_path"] = $tconfig["tpanel_path"]."webimages/".$_SESSION['sess_systype']."/upload/Passenger";
$tconfig["tsite_upload_images_passenger"] = $tconfig["tsite_url"]."webimages/".$_SESSION['sess_systype']."/upload/Passenger";

/* To upload images for static pages */
 $tconfig["tsite_upload_page_images"] = $tconfig["tsite_img"]."/page/".$_SESSION['sess_systype']."/";
$tconfig["tsite_upload_page_images_panel"] = $tconfig["tpanel_path"]."assets/img/page".$_SESSION['sess_systype'];

/* To upload passenger Docunment */
$tconfig["tsite_upload_vehicle_doc"] = $tconfig["tpanel_path"]."webimages/".$_SESSION['sess_systype']."/upload/documents/vehicles/";
$tconfig["tsite_upload_vehicle_doc_panel"] = $tconfig["tsite_url"]."webimages/".$_SESSION['sess_systype']."/upload/documents/vehicles/";

/* To upload driver documents */
//$tconfig["tsite_upload_driver_doc"] = $tconfig["tsite_upload_vehicle_doc"]."driver/";
//$tconfig["tsite_upload_driver_doc_panel"] = $tconfig["tsite_upload_vehicle_doc_panel"]."driver/";


/* To upload images for Appscreenshort pages */
$tconfig["tsite_upload_apppage_images"] = $tconfig["tpanel_path"]."webimages/".$_SESSION['sess_systype']."/upload/Appscreens/";
$tconfig["tsite_upload_apppage_images_panel"] = $tconfig["tsite_url"]."webimages/".$_SESSION['sess_systype']."/upload/Appscreens/";


//Start ::Vehicle Type
$tconfig["tsite_upload_images_vehicle_type_path"] = $tconfig["tpanel_path"]."webimages/".$_SESSION['sess_systype']."/icons/VehicleType";
$tconfig["tsite_upload_images_vehicle_type"] = $tconfig["tsite_url"]."webimages/".$_SESSION['sess_systype']."/icons/VehicleType";
$tconfig["tsite_upload_images_vehicle_type_size1_android"] = "60";
$tconfig["tsite_upload_images_vehicle_type_size2_android"] = "90";
$tconfig["tsite_upload_images_vehicle_type_size3_both"] = "120";
$tconfig["tsite_upload_images_vehicle_type_size4_android"] = "180";
$tconfig["tsite_upload_images_vehicle_type_size5_both"] = "240";
$tconfig["tsite_upload_images_vehicle_type_size5_ios"] = "360";


$tconfig["tsite_upload_images_member_size1"] = "64";
$tconfig["tsite_upload_images_member_size2"] = "130";
$tconfig["tsite_upload_images_member_size3"] = "256";
$tconfig["tsite_upload_images_member_size4"] = "512"; 
?>