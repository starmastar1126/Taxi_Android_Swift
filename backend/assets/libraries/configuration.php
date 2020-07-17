<?php                                                          

$tconfig["tsite_folder"] = ($_SERVER["HTTP_HOST"] == "localhost")?"/":"/";

if($_SERVER["HTTP_HOST"] == "localhost"){

  $hst_arr = explode("/",$_SERVER["REQUEST_URI"]);

  $hst_var = $hst_arr[1];

  $tconfig["tsite_folder"] = "/".$hst_arr[1]."/"; 

}



// if($_SERVER["HTTPS"] == "on"){

//   $http = "https://";

// } else {

  $http = "http://";

// }







$tconfig["tsite_url"] = $http.$_SERVER["HTTP_HOST"].$tconfig["tsite_folder"];

$tconfig["tsite_url_main_admin"] = $http.$_SERVER["HTTP_HOST"].$tconfig["tsite_folder"].'admin/';

$tconfig["tsite_url_admin"] = $http.$_SERVER["HTTP_HOST"].$tconfig["tsite_folder"].'appadmin/';

$tconfig["tpanel_path"] = $_SERVER["DOCUMENT_ROOT"]."".$tconfig["tsite_folder"];

$tconfig["tsite_libraries"] = $tconfig["tsite_url"]."assets/libraries/";

$tconfig["tsite_libraries_v"] = $tconfig["tpanel_path"]."assets/libraries/";

$tconfig["tsite_img"] = $tconfig["tsite_url"]."assets/img";   



$tconfig["tsite_home_images"] = $tconfig["tsite_img"]."/home/";   

$tconfig["tsite_upload_images"] = $tconfig["tsite_img"]."/images/";   

$tconfig["tsite_upload_images_panel"] = $tconfig["tpanel_path"]."assets/img/images/";





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





//Start ::Hotel Passenger Profile Image

$tconfig["tsite_upload_images_hotel_passenger_path"] = $tconfig["tpanel_path"]."webimages/upload/Hotel_Passenger";

$tconfig["tsite_upload_images_hotel_passenger"] = $tconfig["tsite_url"]."webimages/upload/Hotel_Passenger";



/* To upload images for static pages */

 $tconfig["tsite_upload_page_images"] = $tconfig["tsite_img"]."/page/";

$tconfig["tsite_upload_page_images_panel"] = $tconfig["tpanel_path"]."assets/img/page";



/* To upload passenger Docunment */

$tconfig["tsite_upload_vehicle_doc"] = $tconfig["tpanel_path"]."webimages/upload/documents/vehicles";

$tconfig["tsite_upload_vehicle_doc_panel"] = $tconfig["tsite_url"]."webimages/upload/documents/vehicles/";



/* To upload driver documents */

//$tconfig["tsite_upload_driver_doc"] = $tconfig["tsite_upload_vehicle_doc"]."driver/";

//$tconfig["tsite_upload_driver_doc_panel"] = $tconfig["tsite_upload_vehicle_doc_panel"]."driver/";





/* To upload images for Appscreenshort pages */

$tconfig["tsite_upload_apppage_images"] = $tconfig["tpanel_path"]."webimages/upload/Appscreens/";

$tconfig["tsite_upload_apppage_images_panel"] = $tconfig["tsite_url"]."webimages/upload/Appscreens/";





//Start ::Vehicle Type

$tconfig["tsite_upload_images_vehicle_type_path"] = $tconfig["tpanel_path"]."webimages/icons/VehicleType";

$tconfig["tsite_upload_images_vehicle_type"] = $tconfig["tsite_url"]."webimages/icons/VehicleType";

$tconfig["tsite_upload_images_vehicle_type_size1_android"] = "60";

$tconfig["tsite_upload_images_vehicle_type_size2_android"] = "90";

$tconfig["tsite_upload_images_vehicle_type_size3_both"] = "120";

$tconfig["tsite_upload_images_vehicle_type_size4_android"] = "180";

$tconfig["tsite_upload_images_vehicle_type_size5_both"] = "240";

$tconfig["tsite_upload_images_vehicle_type_size5_ios"] = "360";





$tconfig["tsite_upload_images_member_size1"] = "64";

$tconfig["tsite_upload_images_member_size2"] = "150";

$tconfig["tsite_upload_images_member_size3"] = "256";

$tconfig["tsite_upload_images_member_size4"] = "512"; 





//Start ::Vehicle category

$tconfig["tsite_upload_images_vehicle_category_path"] = $tconfig["tpanel_path"]."webimages/icons/VehicleCategory";

$tconfig["tsite_upload_images_vehicle_category"] = $tconfig["tsite_url"]."webimages/icons/VehicleCategory";

$tconfig["tsite_upload_images_vehicle_category_size1_android"] = "60";

$tconfig["tsite_upload_images_vehicle_category_size2_android"] = "90";

$tconfig["tsite_upload_images_vehicle_category_size3_both"] = "120";

$tconfig["tsite_upload_images_vehicle_category_size4_android"] = "180";

$tconfig["tsite_upload_images_vehicle_category_size5_both"] = "240";

$tconfig["tsite_upload_images_vehicle_category_size5_ios"] = "360";





/*$tconfig["tsite_upload_images_member_size1"] = "64";

$tconfig["tsite_upload_images_member_size2"] = "150";

$tconfig["tsite_upload_images_member_size3"] = "256";

$tconfig["tsite_upload_images_member_size4"] = "512";   */



/* To upload images for trips */

$tconfig["tsite_upload_trip_images_path"] = $tconfig["tpanel_path"]."webimages/upload/beforeafter/";

$tconfig["tsite_upload_trip_images"] = $tconfig["tsite_url"]."webimages/upload/beforeafter/"; 



/* For Back-up Database*/

$tconfig["tsite_upload_files_db_backup_path"] = $tconfig["tpanel_path"]."webimages/upload/backup/";

$tconfig["tsite_upload_files_db_backup"] = $tconfig["tsite_url"]."webimages/upload/backup/"; 



/* To upload preference images */

$tconfig["tsite_upload_preference_image"] = $tconfig["tpanel_path"]."webimages/upload/preferences/";

$tconfig["tsite_upload_preference_image_panel"] = $tconfig["tsite_url"]."webimages/upload/preferences/";

/*Home Page Image Size*/

$tconfig["tsite_upload_images_home"] = "300";



/* To upload images for trip delivery signatures */

$tconfig["tsite_upload_trip_signature_images_path"] = $tconfig["tpanel_path"]."webimages/upload/trip_signature/";

$tconfig["tsite_upload_trip_signature_images"] = $tconfig["tsite_url"]."webimages/upload/trip_signature/"; 





//$host_system = "uberridedelivery4"; 

//Ride=cubetaxiplus   Ride+Delivery = uberridedelivery4,  Deliveryonly = uberdelivery4  

if($hst_var == "ufxforall"){

	$host_system = "ufxforall"; 

}elseif($hst_var == "deliveryonly"){

	$host_system = "uberdelivery4"; 

}elseif($hst_var == "ridedelivery"){

	$host_system = "uberridedelivery4"; 

}elseif($hst_var == "massage"){

  $host_system = "ufxforall"; 

}elseif($hst_var == "doctor"){

  $host_system = "ufxforall"; 

}elseif($hst_var == "beautician"){

  $host_system = "ufxforall"; 

}elseif($hst_var == "carwash"){

  $host_system = "ufxforall"; 

}elseif($hst_var == "dogwalking"){

  $host_system = "ufxforall"; 

}elseif($hst_var == "towtruck_v4"){

  $host_system = "ufxforall"; 

}else{

	$host_system = "cubetaxiplus"; 

}

/*

if($host_system == "ufxforall"){

  $APP_TYPE = "UberX";

  define('APP_TYPE',$APP_TYPE);

}elseif($host_system == "uberdelivery4"){

  $APP_TYPE = "Delivery";

  define('APP_TYPE',$APP_TYPE);

}elseif($host_system == "uberridedelivery4"){

  $APP_TYPE = "Ride-Delivery";

  define('APP_TYPE',$APP_TYPE);

}elseif($host_system == "rideufxdelivery"){

  $APP_TYPE = "Ride-Delivery-UberX";

  define('APP_TYPE',$APP_TYPE);

}else{

  $APP_TYPE = "Ride";

  define('APP_TYPE',$APP_TYPE);

}

*/



?>