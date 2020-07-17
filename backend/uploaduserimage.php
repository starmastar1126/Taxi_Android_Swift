<?php 

//echo "<pre>";print_r($_REQUEST);exit;
include_once('common.php');
require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();
//echo "<pre>";print_r($tconfig);exit;
//$sql = "SELECT replace(vAddress, ' ','') as vAddress FROM `restricted_negative_area`";
//$row = $obj->MySQLSelect($sql);
//echo "<pre>";print_r($row);exit;
		

$Photo_Gallery_folder = $tconfig["tsite_upload_images_passenger_path"]."/209/";
    		
unlink($Photo_Gallery_folder.$db_user[0]['vImgName']);
unlink($Photo_Gallery_folder."1_".$db_user[0]['vImgName']);
unlink($Photo_Gallery_folder."2_".$db_user[0]['vImgName']);
unlink($Photo_Gallery_folder."3_".$db_user[0]['vImgName']);   
unlink($Photo_Gallery_folder."4_".$db_user[0]['vImgName']);   

if(!is_dir($Photo_Gallery_folder)) {                  
    mkdir($Photo_Gallery_folder, 0777);
}

#####################Code For FaceBook ########################################################################
/*$user= "1619507604758072";    
$baseurl =  "http://graph.facebook.com/".$user."/picture?width=500";
$url = $user.".jpg";
echo $baseurl;exit;
$profile_Image = $baseurl;
$userImage = $url;
//$path = '/home/xm36xy4vbn/public_html/webimages/upload/Banner/';  // your saving path
$thumb_image = file_get_contents($baseurl);
$thumb_file = $Photo_Gallery_folder . $url;
$image_name = file_put_contents($thumb_file, $thumb_image);
         
if(is_file($Photo_Gallery_folder.$url)) {

list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$url);           
if($width < $height){
    $final_width = $width;
}else{
    $final_width = $height;
}       
//$img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],""); 
//$vImageName = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"], '', '', '', 'Y', '', $Photo_Gallery_folder);
}  
 */
#####################Code For FaceBook Ends ########################################################################

#####################Code For Google ########################################################################
/*$user= "106493821213156170332";                                                  
//$baseurl1 =  "https://www.googleapis.com/plus/v1/people/114434193354602240754?fields=image&key=AIzaSyB7_FaMl2gU1ItcomolF2S1Fzh8prnvNNw";
$baseurl1 =  "https://www.googleapis.com/plus/v1/people/106493821213156170332?fields=image&key=AIzaSyBjA92EIcwcbIYVa78x-yJK9gQNnzF6rXI";
$url = $user.".jpg";
$jsonfile = file_get_contents($baseurl1);
$jsondata = json_decode($jsonfile);
$baseurl = $jsondata->image->url;
$baseurl = str_replace("?sz=50","?sz=500",$baseurl);

$profile_Image = $baseurl;
$userImage = $url;
//$path = '/home/xm36xy4vbn/public_html/webimages/upload/Banner/';  // your saving path
$thumb_image = file_get_contents($baseurl);
$thumb_file = $Photo_Gallery_folder . $url;
$image_name = file_put_contents($thumb_file, $thumb_image);
         
if(is_file($Photo_Gallery_folder.$url)) {

list($width, $height, $type, $attr)= getimagesize($Photo_Gallery_folder.$url);           
if($width < $height){
    $final_width = $width;
}else{
    $final_width = $height;
}       
//$img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
$imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],""); 
//$vImageName = $generalobj->general_upload_image($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"], '', '', '', 'Y', '', $Photo_Gallery_folder);
}  
*/
#####################Code For Google Ends########################################################################

#####################Code For Twitter ########################################################################
// http://192.168.1.131/cubetaxidev/assets/libraries/twitter/getuserimage.php
require_once('assets/libraries/twitter/TwitterAPIExchange.php');
$settings = array(
    'oauth_access_token' => "875667835535499265-NpMDvzyWhMsNLHgefII03myBvqlOpvG",
    'oauth_access_token_secret' => "FzDnbplhDc6a4tZULmzbkbnuqMAzPeBZy66xE6vmAa0Ln",
    'consumer_key' => "xxRy20BWLBgTrQZKiW410hxPQ",
    'consumer_secret' => "7UEfFYAmMWhrK84ptZzU2dACvroC7CmD64omyz1m0n8FD4vqDt"
);
$url = 'https://api.twitter.com/1.1/users/show.json';
$getfield = '?user_id=883218074903629826';
$requestMethod = 'GET';
$twitter = new TwitterAPIExchange($settings);
$twitterArr = $twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest();
$jsondata = json_decode($twitterArr); //echo "<pre>";print_r($jsondata);exit;   
$profile_image_url = $jsondata->profile_image_url;

$baseurl = str_replace("_normal","",$profile_image_url);
$user = "883218074903629826";
$url = $user.time().".jpg";         
/* file_get_content */
$profile_Image = $baseurl;
$userImage = $url;
$thumb_image = file_get_contents($baseurl);
$thumb_file = $Photo_Gallery_folder . $url;
$image_name = file_put_contents($thumb_file, $thumb_image);
/* file_get_content  ends*/
if(is_file($Photo_Gallery_folder.$url)) {
 $imgname = $generalobj->img_data_upload($Photo_Gallery_folder,$url,$Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"],"");
 $vimage = $imgname; 
}  
echo $vimage;exit;
#####################Code For Twitter ########################################################################

?>