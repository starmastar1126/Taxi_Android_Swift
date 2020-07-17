<?php 
      
//echo "<pre>";print_r($_REQUEST);exit;
include_once('common.php');
require_once(TPATH_CLASS . "/Imagecrop.class.php");
$thumb = new thumbnail();
//echo "<pre>";print_r($tconfig);exit;
//$sql = "SELECT replace(vAddress, ' ','') as vAddress FROM `restricted_negative_area`";
//$row = $obj->MySQLSelect($sql);
//echo "<pre>";print_r($row);exit;
		
UploadUserImage("363","Passenger","Google","106376075109690810712");
function UploadUserImage($iMemberId,$UserType="Passenger",$eSignUpType,$vFbId){
    global $generalobj,$tconfig;
    $vimage = "";
    if($UserType == "Passenger"){
       $Photo_Gallery_folder =$tconfig["tsite_upload_images_passenger_path"]."/".$iMemberId."/";
       $OldImage=get_value('register_user', 'vImgName', 'iUserId', $iMemberId,'','true');
    }else{
       $Photo_Gallery_folder =$tconfig["tsite_upload_images_driver_path"]."/".$iMemberId."/";
       $OldImage=get_value('register_driver', 'vImage', 'iDriverId', $iMemberId,'','true');
    }
    //echo $Photo_Gallery_folder.$OldImage;exit;
    unlink($Photo_Gallery_folder.$OldImage);
    unlink($Photo_Gallery_folder."1_".$OldImage);
    unlink($Photo_Gallery_folder."2_".$OldImage);
    unlink($Photo_Gallery_folder."3_".$OldImage);   
    unlink($Photo_Gallery_folder."4_".$OldImage);  
    if(!is_dir($Photo_Gallery_folder)) {                  
       mkdir($Photo_Gallery_folder, 0777);
    }
    if($eSignUpType == "Facebook"){
       //$baseurl =  "http://graph.facebook.com/".$vFbId."/picture?type=large";
       $baseurl =  "http://graph.facebook.com/".$vFbId."/picture?width=256";
       //$url = $vFbId."_".time().".jpg";
       $url = time().".jpg";
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
    }
    if($eSignUpType == "Google"){
       $GOOGLE_SEVER_API_KEY_WEB = $generalobj->getConfigurations("configurations", "GOOGLE_SEVER_API_KEY_WEB");
       //$baseurl1 =  "https://www.googleapis.com/plus/v1/people/114434193354602240754?fields=image&key=AIzaSyB7_FaMl2gU1ItcomolF2S1Fzh8prnvNNw";
       $baseurl1 =  "https://www.googleapis.com/plus/v1/people/".$vFbId."?fields=image&key=".$GOOGLE_SEVER_API_KEY_WEB;
       //$url = $vFbId."_".time().".jpg";
       //$url = time().".jpg";
       $url = time().".png";
       try{
        $jsonfile = file_get_contents($baseurl1);
        $jsondata = json_decode($jsonfile);
        $baseurl = $jsondata->image->url;
        $baseurl = str_replace("?sz=50","?sz=256",$baseurl);
       }catch (ErrorException $ex) {
         $imgname = "";
         $vimage = $imgname; 
       }
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
    }
    if($eSignUpType == "Twitter"){
       require_once('assets/libraries/twitter/TwitterAPIExchange.php');
       $TWITTER_OAUTH_ACCESS_TOKEN = $generalobj->getConfigurations("configurations", "TWITTER_OAUTH_ACCESS_TOKEN");
       $TWITTER_OAUTH_ACCESS_TOKEN_SECRET = $generalobj->getConfigurations("configurations", "TWITTER_OAUTH_ACCESS_TOKEN_SECRET");
       $TWITTER_CONSUMER_KEY = $generalobj->getConfigurations("configurations", "TWITTER_CONSUMER_KEY");
       $TWITTER_CONSUMER_SECRET = $generalobj->getConfigurations("configurations", "TWITTER_CONSUMER_SECRET");
       $settings = array(
            'oauth_access_token' => $TWITTER_OAUTH_ACCESS_TOKEN,
            'oauth_access_token_secret' => $TWITTER_OAUTH_ACCESS_TOKEN_SECRET,
            'consumer_key' => $TWITTER_CONSUMER_KEY,
            'consumer_secret' => $TWITTER_CONSUMER_SECRET
        );
       $url = 'https://api.twitter.com/1.1/users/show.json';
       $getfield = '?user_id='.$vFbId;
       $requestMethod = 'GET';
       $twitter = new TwitterAPIExchange($settings);
       $twitterArr = $twitter->setGetfield($getfield)
                     ->buildOauth($url, $requestMethod)
                     ->performRequest();
       $jsondata = json_decode($twitterArr); //echo "<pre>";print_r($jsondata);exit;   
       $profile_image_url = $jsondata->profile_image_url;
       $baseurl = str_replace("_normal","",$profile_image_url);
       //$url = $vFbId."_".time().".jpg";
       $url = time().".jpg";       
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
    }
    return $vimage;
}

function get_value($table, $field_name, $condition_field = '', $condition_value = '', $setParams = '', $directValue = '') {
    global $obj;
    $returnValue = array();

    $where = ($condition_field != '') ? ' WHERE ' . clean($condition_field) : '';
    $where .= ($where != '' && $condition_value != '') ? ' = "' . clean($condition_value) . '"' : '';

    if ($table != '' && $field_name != '' && $where != '') {
        $sql = "SELECT $field_name FROM  $table $where";
        if ($setParams != '') {
            $sql .= $setParams;
        }
        $returnValue = $obj->MySQLSelect($sql);
    } else if ($table != '' && $field_name != '') {
        $sql = "SELECT $field_name FROM  $table";
        if ($setParams != '') {
            $sql .= $setParams;
        }
        $returnValue = $obj->MySQLSelect($sql);
    }
    if ($directValue == '') {
        return $returnValue;
    } else {
        $temp = $returnValue[0][$field_name];
        return $temp;
    }
}

function clean($str) {
	global $obj;
    $str = trim($str);
    $str = $obj->SqlEscapeString($str);
    $str = htmlspecialchars($str);
    $str = strip_tags($str);
    return($str);
}

?>