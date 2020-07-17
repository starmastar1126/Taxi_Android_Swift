<?php 
Class General {

    public $role;

    public function __construct() {
        $this->role = array();
        //$this->url = isset($this->url)?$this->url:'';
    }

    /**
     * @access	public
     * @Print Element input type
     */
    function DateTime($text, $format = '') {
        if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
            return "---";
        switch ($format) {
            //us formate
            case "1":
                return @date('M j, Y', @strtotime($text));
                break;

            case "2":
                return @date('M j, y  [G:i] ', @strtotime($text));
                break;

            case "3":
                return @date("M j, Y", $text);
                break;

            case "4":
                return @date('Y,n,j,G,', $text) . intval(date('i', $text)) . ',' . intval(date('s', $text));
                break;

            case "5":
                return @date('l, F j, Y', @strtotime($text));
                break;

            case "6":
                return @date('g:i:s', $text);
                break;

            case "7":
                return @date('F j, Y  h:i A', @strtotime($text));
                break;

            case "8":
                return @date('Y-m-d', @strtotime($text));
                break;
            case "9":
                return @date('F j, Y', @strtotime($text));
                break;
            case "10":
                return @date('d/m/Y', @strtotime($text));
                break;
            case "11":
                return @date('m/d/y', @strtotime($text));
                break;
            case "12":
                return @date('H:i', @strtotime($text));
                break;
            case "13":
                return @date('F j, Y (H:i:s)', @strtotime($text));
                break;
            case "14":
                return @date('j M Y', @strtotime($text));
                break;
            case "15":
                return @date('D', @strtotime($text));
                break;
            case "16":
                return @date('d', @strtotime($text));
                break;
            case "17":
                return @date('M Y', @strtotime($text));
                break;
            case "18":
                return @date('h:i A', @strtotime($text));
                break;
            case "19":
                return @date('M j, Y', @strtotime($text));
                break;
            case "20":
                return @date('l,F d', @strtotime($text));
                break;
            default :
                return @date('M j, Y', @strtotime($text));
                break;
        }
    }

    function DateTime1($text, $time = 'yes') {

        if ($text == "" || $text == "0000-00-00 00:00:00" || $text == "0000-00-00")
            return "---";

        $date = @date('jS F, Y', @strtotime($text));
        if ($time == 'yes') {
            $date .= " " . @date('h:i a', @strtotime($text));
        }
        return $date;
    }

    function encrypt_bycrypt($data) {
        $options = [
            'cost' => 12,
        ];
        return password_hash($data, PASSWORD_BCRYPT, $options);
    }

    function checkAuthntication() {
        global $tconfig;
        //echo $_SESSION["sess_iAdminId"];
        if ($_SESSION["sess_iAdminId"] == '') {
            if ($_REQUEST["file"] != 'au-login' && $_REQUEST["file"] != 'au-login_a' && $_REQUEST["file"] != 'c-unsubscribe' && $_REQUEST["file"] != 'c-unsubscribe_a') {
                header("location:" . $tconfig["tpanel_url"] . "/index.php?file=au-login");
                exit;
            }
        }
    }

    function imageupload($photopath, $vphoto, $vphoto_name, $prefix = '', $vaildExt = "gif,jpg,jpeg,bmp,png") {
        global $langage_lbl;
        $msg = "";
        if (!empty($vphoto_name) and is_file($vphoto)) {
            // Remove Dots from File name
            $tmp = explode(".", $vphoto_name);
            for ($i = 0; $i < count($tmp) - 1; $i++) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode("_", $tmp1);
            $ext = $tmp[count($tmp) - 1];

            $vaildExt_arr = explode(",", strtoupper($vaildExt));
            if (in_array(strtoupper($ext), $vaildExt_arr)) {
                //$vphotofile=$file.".".$ext;
                $vphotofile = $file . "_" . date("YmdHis") . "." . $ext;
                $ftppath1 = $photopath . $vphotofile;
                if (!copy($vphoto, $ftppath1)) {
                    $vphotofile = '';
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_UNSUCCESS_MSG'];
                } else
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_SUCCESS_MSG'];
            }
            else {
                $vphotofile = '';
                $msg = $langage_lbl['LBL_FILE_EXT_VALID_ERROR_MSG'] . $vaildExt . "!!!";
            }
        }
        $ret[0] = $vphotofile;
        $ret[1] = $msg;
        return $ret;
    }

    function general_upload_image($temp_name, $image_name, $path, $size1, $size2 = "", $size3 = "", $size4 = "", $option = "", $modulename = "", $original = "", $size5 = "", $temp_gallery) {

        include_once(TPATH_CLASS . 'Imagecrop.class.php');
        $thumb = new thumbnail;

        //global $thumb;
        $time_val = time();
        $vImage1 = $temp_name;

        $vImage_name1 = str_replace(" ", "_", trim($image_name));
        $img_arr = explode(".", $vImage_name1);
        if ($modulename == '') {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }
        $filename = mt_rand(11111, 99999);
        $fileextension = $img_arr[count($img_arr) - 1];

        if ($vImage1 != "") {
            //$temp_gallery . "/" . $vImage_name1;
            copy($vImage1, $temp_gallery . "/" . $vImage_name1);
            if ($option == 'menu' && $option != "") {
                list($width, $height) = getimagesize($temp_gallery . "/" . $vImage_name1);
                $size3 = $width;
            }
            if ($original == "Y" || $original == "y") {
                copy($temp_gallery . "/" . $vImage_name1, $path . $time_val . "_" . $filename . "." . $fileextension);
            }

            //$temp_gallery."/".$vImage_name1;
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1); // generate image_file, set filename to resize/resample
            $thumb->size_auto($size1);    // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);
            $thumb->save($path . "1" . "_" . $time_val . "_" . $filename . "." . $fileextension);
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size2);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save($path . "2" . "_" . $time_val . "_" . $filename . "." . $fileextension);
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size3);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save($path . "3" . "_" . $time_val . "_" . $filename . "." . $fileextension);
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size5);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save($path . "5" . "_" . $time_val . "_" . $filename . "." . $fileextension);
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size4);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save($path . "4" . "_" . $time_val . "_" . $filename . "." . $fileextension);
            @unlink($temp_gallery . "/" . $vImage_name1);
            @unlink($path . $old_image1);
            @unlink($path . "1_" . $old_image1);
            @unlink($path . "2_" . $old_image1);
            @unlink($path . "3_" . $old_image1);
            @unlink($path . "4_" . $old_image1);
            @unlink($path . "5_" . $old_image1);
            $vImage1 = $time_val . "_" . $filename . "." . $fileextension;
            return $vImage1;
        } else {
            return $old_image1;
        }
    }


    function general_upload_image_function($temp_name, $image_name, $path, $size1, $size2 = "", $size3 = "", $size4 = "", $option = "", $modulename = "", $original = "", $size5 = "", $temp_gallery) {
        include_once(TPATH_CLASS . 'Imagecrop.class.php');
        $thumb = new thumbnail;
        $vImage1 = $temp_name;
        $vImage_name1 = str_replace(" ", "_", trim($image_name));
        $img_arr = explode(".", $vImage_name1);
        if ($modulename == '') {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }
        $fileextension = $img_arr[count($img_arr) - 1];

        if ($vImage1 != "") {
            //$temp_gallery . "/" . $vImage_name1;
            copy($vImage1, $temp_gallery . "/" . $vImage_name1);
            if ($option == 'menu' && $option != "") {
                list($width, $height) = getimagesize($temp_gallery . "/" . $vImage_name1);
                $size3 = $width;
            }
            if ($original == "Y" || $original == "y") {
                copy($temp_gallery . "/" . $vImage_name1, $path ."org_".$filename ."_".date("YmdHis") . "." . $fileextension);
            }


            //$temp_gallery."/".$vImage_name1;
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1); // generate image_file, set filename to resize/resample
            $thumb->size_auto($size1);    // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);
            $thumb->save($path . $filename . "_" . date("YmdHis") . "." . $fileextension);
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size2);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save($path . "2_" . $filename . "_" . date("YmdHis") . "." . $fileextension);
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size3);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save($path . "3_" . $filename . "_" . date("YmdHis") . "." . $fileextension);
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size5);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save($path . "5_" . $filename . "_" . date("YmdHis") . "." . $fileextension);
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size4);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save($path . "4_" . $filename . "_" . date("YmdHis") . "." . $fileextension);
            @unlink($temp_gallery . "/" . $vImage_name1);
            @unlink($path . $old_image1);
            @unlink($path . "1_" . $old_image1);
            @unlink($path . "2_" . $old_image1);
            @unlink($path . "3_" . $old_image1);
            @unlink($path . "4_" . $old_image1);
            @unlink($path . "5_" . $old_image1);
            $vImage1 = $filename ."_".date("YmdHis") . "." . $fileextension;
            return $vImage1;
        } else {
            return $old_image1;
        }
    }
    function check_member_login() {
        global $tconfig;
        $sess_iUserId = isset($_SESSION['sess_iUserId']) ? $_SESSION['sess_iUserId'] : '';
        if ($sess_iUserId == "" && basename($_SERVER['PHP_SELF']) != "login.php")
            header("Location:" . $tconfig["tsite_url"] . "sign-in.php");
        else {
            // $this->go_to_home(); // need to think about other accessible page
        }
    }

    // will go to dashboard page only if user is logged in -i.e. login and register page
    function go_to_home() {
        global $tconfig;
        $sess_iUserId = isset($_SESSION['sess_iUserId']) ? $_SESSION['sess_iUserId'] : '';
        $sess_user = isset($_SESSION['sess_user']) ? $_SESSION['sess_user'] : '';
        $url = "";
        if ($sess_iUserId != "" && $sess_user != '') {
            switch ($sess_user) {
                case 'driver':
                    $url = "profile.php";
                    break;
                case 'rider':
                    $url = "profile_rider.php";
                    break;
                case 'company':
                    $url = "profile.php";
                    break;
                default:
                    $url = "index.php";
                    break;
            }
        }
        if ($url != '' && basename($_SERVER['PHP_SELF']) != $url) // if user is at same page
            header("Location:" . $url);
    }

    function general_upload_image1($image_name, $path, $size1, $size2 = "", $size3 = "", $size4 = "", $option = "", $modulename = "", $original = "", $size5 = "") {
        global $temp_gallery, $thumb;

        $time_val = time();
        //$vImage1 = $temp_name;
        $vImage1 = $image_name;
        //echo $path.$vImage1;exit;
        $vImage_name1 = str_replace(" ", "_", trim($image_name));

        $img_arr = explode(".", $vImage_name1);
        if ($modulename == '') {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }
        $filename = mt_rand(11111, 99999);
        $fileextension = $img_arr[1];
        //print_r($filename);exit;
        if ($vImage1 != "") {
            $temp_gallery . "/" . $vImage_name1;
            copy($path . $vImage1, $temp_gallery . "/" . $vImage_name1);
            @unlink($path . $vImage1);      //  Delete downloading image of url link because display
            if ($option == 'menu' && $option != "") {
                list($width, $height) = getimagesize($temp_gallery . "/" . $vImage_name1);
                $size3 = $width;
            }
            if ($original == "Y" || $original == "y") {
                copy($temp_gallery . "/" . $vImage_name1, $path . $time_val . "_" . $filename . "." . $fileextension);
            }


            //echo "filename"." ".$fileextension;exit;
            $temp_gallery . "/" . $vImage_name1;

            if ($size1 != "") {
                $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1); // generate image_file, set filename to resize/resample
                $thumb->size_auto($size1);       // set the biggest width or height for thumbnail
                $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
                $thumb->save($path . "1" . "_" . $time_val . "_" . $filename . "." . $fileextension);
            }
            if ($size2 != "") {
                $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
                $thumb->size_auto($size2);       // set the biggest width or height for thumbnail
                $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
                $thumb->save($path . "2" . "_" . $time_val . "_" . $filename . "." . $fileextension);
            }

            if ($size3 != "") {
                $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
                $thumb->size_auto($size3);       // set the biggest width or height for thumbnail
                $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
                $thumb->save($path . "3" . "_" . $time_val . "_" . $filename . "." . $fileextension);
            }

            if ($size5 != "") {
                $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
                $thumb->size_auto($size5);       // set the biggest width or height for thumbnail
                $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
                $thumb->save($path . "5" . "_" . $time_val . "_" . $filename . "." . $fileextension);
            }

            if ($size4 != "") {

                $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
                $thumb->size_auto($size4);       // set the biggest width or height for thumbnail
                $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
                $thumb->save($path . "4" . "_" . $time_val . "_" . $filename . "." . $fileextension);
            }

            @unlink($temp_gallery . "/" . $vImage_name1);
            @unlink($path . $old_image1);
            @unlink($path . "1_" . $old_image1);
            @unlink($path . "2_" . $old_image1);
            @unlink($path . "3_" . $old_image1);
            @unlink($path . "4_" . $old_image1);
            @unlink($path . "5_" . $old_image1);
            $vImage1 = $time_val . "_" . $filename . "." . $fileextension;
            return $vImage1;
        } else {
            return $old_image1;
        }
    }

    function upload_resize_image($image_path, $image_object, $image_name, $mode, $image_size_array, $old_image = "", $prefix = "", $insert_time = "") {
        /*
          global $tconfig;
          $temp_gallery=$tconfig["tsite_temp_gallery"];

          if($insert_time == ""){
          $time_val = time();
          }
          if($prefix != ""){
          $prefix = "_".$prefix."_";
          }
          if($image_name != "")
          {
          copy($image_object,$temp_gallery."/".$image_name);
          foreach($image_size_array as $size_number => $size)
          {
          $image_size = explode("||",$size);
          if($size_number == "4" || $size_number == "3")
          {
          $image_orig_size = @getimagesize($temp_gallery."/".$image_name);
          if($image_orig_size[0]<$image_size[0])
          {
          copy($temp_gallery."/".$image_name,$image_path.$size_number."_".$time_val.$prefix.$image_name);
          }
          else
          {
          $thumb=new thumbnail($temp_gallery."/".$image_name);
          if(!check_image_ratio($image_orig_size[0],$image_orig_size[1]))
          {
          $thumb->size_woaspect($image_size[0],$image_size[1]);
          }
          else
          {
          $thumb->size_width($image_size[0],$size_number);
          $thumb->size_height($image_size[1],$size_number);
          }
          //$thumb->size_auto($size);
          $thumb->jpeg_quality(100);
          $thumb->save($image_path.$size_number."_".$time_val.$prefix.$image_name);
          }
          }
          else
          {
          $thumb=new thumbnail($temp_gallery."/".$image_name);
          if(!check_image_ratio($image_orig_size[0],$image_orig_size[1]))
          {
          $thumb->size_woaspect($image_size[0],$image_size[1]);
          }
          else
          {
          $thumb->size_width($image_size[0],$size_number);
          $thumb->size_height($image_size[1],$size_number);
          }
          $thumb->jpeg_quality(100);
          $thumb->save($image_path.$size_number."_".$time_val.$prefix.$image_name);
          }
          if($mode == "Update")
          {
          @unlink($image_path.$size_number."_".$old_image);
          }
          }
          $vImage=$time_val.$prefix.$image_name;
          @unlink($temp_gallery."/".$image_name);
          }
          else
          {
          if($mode == "Update"){
          $vImage = $old_image;
          }
          }
         */
        return $vImage;
    }

    function fileupload($filepath, $vfile, $vfile_name, $prefix = '', $vaildExt = "mp3,wav") {
        global $langage_lbl;
        $msg = "";

        if (!empty($vfile_name) and is_file($vfile)) {
            $tmp = explode(".", $vfile_name);
            for ($i = 0; $i < count($tmp) - 1; $i++) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode("_", $tmp1);
            $ext = $tmp[count($tmp) - 1];

            $vaildExt_arr = explode(",", strtoupper($vaildExt));
            if (in_array(strtoupper($ext), $vaildExt_arr)) {

                $vfilefile = $file . "_" . date("YmdHis") . "." . $ext;
                $ftppath1 = $filepath . $vfilefile;
                if (!copy($vfile, $ftppath1)) {

                    $vfilefile = '';
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_UNSUCCESS_MSG'];
                    $errorflag = "1";
                } else {
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_SUCCESS_MSG'];
                    $errorflag = "0";
                }
            } else {
                $vfilefile = '';
                $msg = $langage_lbl['LBL_FILE_EXT_VALID_ERROR_MSG'] . $vaildExt . "!!!";
                $errorflag = "1";
            }
        }
        $ret[0] = $vfilefile;
        $ret[1] = $msg;
        $ret[2] = $errorflag;

        return $ret;
    }
    
        function fileupload_home($filepath, $vfile, $vfile_name, $prefix = '', $vaildExt = "mp3,wav",$vCode="EN") {
        global $langage_lbl;
        $msg = "";

        if (!empty($vfile_name) and is_file($vfile)) {
            $tmp = explode(".", $vfile_name);
            for ($i = 0; $i < count($tmp) - 1; $i++) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode("_", $tmp1);
            $ext = $tmp[count($tmp) - 1];

            $vaildExt_arr = explode(",", strtoupper($vaildExt));
            if (in_array(strtoupper($ext), $vaildExt_arr)) {

                $vfilefile = $file . "_" . $vCode . "." . $ext;
                $ftppath1 = $filepath . $vfilefile;
                if (!copy($vfile, $ftppath1)) {

                    $vfilefile = '';
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_UNSUCCESS_MSG'];
                    $errorflag = "1";
                } else {
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_SUCCESS_MSG'];
                    $errorflag = "0";
                }
            } else {
                $vfilefile = '';
                $msg = $langage_lbl['LBL_FILE_EXT_VALID_ERROR_MSG'] . $vaildExt . "!!!";
                $errorflag = "1";
            }
        }
        $ret[0] = $vfilefile;
        $ret[1] = $msg;
        $ret[2] = $errorflag;

        return $ret;
    }

    function fileupload_new($filepath, $vfile, $vfile_name, $prefix = '', $vaildExt = "mp3,wav", $name) {
        global $langage_lbl;
        $msg = "";
        //	echo $filepath;exit;
        if (!empty($vfile_name) and is_file($vfile)) {
            $tmp = explode(".", $vfile_name);
            for ($i = 0; $i < count($tmp) - 1; $i++) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode("_", $tmp1);
            $ext = $tmp[count($tmp) - 1];

            $vaildExt_arr = explode(",", strtoupper($vaildExt));
            if (in_array(strtoupper($ext), $vaildExt_arr)) {

                $vfilefile = $prefix . "_" . $name . "." . $ext;
                $ftppath1 = $filepath . $vfilefile;
                if (!copy($vfile, $ftppath1)) {
                    $vfilefile = '';
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_UNSUCCESS_MSG'];
                    $errorflag = "1";
                } else {
                    $msg = $langage_lbl['LBL_FILE_UPLOADED_SUCCESS_MSG'];
                    $errorflag = "0";
                }
            } else {
                echo "third";
                exit;
                $vfilefile = '';
                $msg = $langage_lbl['LBL_FILE_EXT_VALID_ERROR_MSG'] . $vaildExt . "!!!";
                $errorflag = "1";
            }
        }
        $ret[0] = $vfilefile;
        $ret[1] = $msg;
        $ret[2] = $errorflag;
        return $ret;
    }

    function getSystemDateTime() {
        return @date("Y-m-j H-i-s"); //2005-04-01 17:16:17
    }

    function DisplayCountry($ssql = '') {
        global $obj;
        if ($ssql != "")
            $ssql = $ssql;
        $db = $obj->MySQLSelect("select * from country where eStatus = 'Active'");
        if (count($db) > 0) {
            for ($i = 0; $i < count($db); $i++) {
                $longname[$i] = $db[$i]['vCountry'];
                $shortname[$i] = $db[$i]['vCountryCode'];
            }
        }
        if (empty($selcountry))
            $selcountry = "CA";

        $countrycombo = "";

        for ($i = 0; $i < count($shortname); $i++) {

            if (trim($selcountry) == trim($shortname[$i])) {
                $countrycombo .= "<option value='" . $shortname[$i] . "' selected>" . $longname[$i] . "</option>";
            } else {
                $countrycombo .= "<option value='" . $shortname[$i] . "'>" . $longname[$i] . "</option>";
            }
        }
        return $countrycombo;
    }

    function get_user_preffered_language($vEmail) {
        global $obj, $tconfig;
        $preflang = "EN";

        $sql = "select vLang from register_user where vEmail ='" . $vEmail . "'";
        $res = $obj->MySQLSelect($sql);
        if (count($res) > 0) {
            $preflang = $res[0]['vLang'];
        } else {
            $sql = "select vLang From register_driver where vEmail = '" . $vEmail . "'";
            $res1 = $obj->MySQLSelect($sql);
            if (count($res1) > 0) {
                $preflang = $res1[0]['vLang'];
            } else {
                $sql = "select vCode from language_master where eDefault = 'Yes'";
                $lang1 = $obj->MySQLSelect($sql);

                if (count($lang1) > 0) {
                    $preflang = $lang1[0]['vCode'];
                }
            }
        }
        return $preflang;
    }

    function send_email_user($type, $db_rec = '', $newsid = '') {
        global $MAIL_FOOTER, $EMAIL_FROM_NAME, $SITE_NAME, $obj, $tconfig, $SUPPORT_MAIL, $SEND_EMAIL, $NOREPLY_EMAIL, $ADMIN_EMAIL,$MAILGUN_ENABLE,$generalobj;
		$MAILGUN_ENABLE = $generalobj->getConfigurations("configurations", "MAILGUN_ENABLE");
        $replyto = "";
        $str = "select * from email_templates where vEmail_Code='" . $type . "'";
        $res = $obj->MySQLSelect($str);
        //$mailsubject = $res[0]['vSubject_EN'];
        //$tMessage = $res[0]['vBody_EN'];
        //echo "<pre>"; print_r($db_rec);
        switch ($type) {
            case "NEWSLETTER_SUBSCRIBER":
                $to_email = $db_rec['vEmail'];
                $key_arr = Array("#MailFooter#");
                $val_arr = Array($MAIL_FOOTER);
                break;

            case "CONTACTUS":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['vEmail'];
                $key_arr = Array("#Contact_Name#", "#Contact_Phone#", "#Contact_Email#", "#Contact_Subject#", "#Contact_Message#", "#MailFooter#");
                $val_arr = Array($db_rec['vFirstName'] . " " . $db_rec['vLastName'], $db_rec['cellno'], $db_rec['vEmail'], $db_rec['eSubject'], $db_rec['tSubject'], $MAIL_FOOTER);
                break;

            case "WITHDRAWAL_MONEY_REQUEST_Admin":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['User_Email'];
                $key_arr = Array("#User_Name#", "#User_Phone#", "#User_Email#", "#Account_Name#", "#Bank_Name#", "#Account_Number#", "#BIC/SWIFT_Code#", "#Bank_Branch#", "#Withdrawal_amount#", "#MailFooter#");
                $val_arr = Array($db_rec['User_Name'], $db_rec['User_Phone'], $db_rec['User_Email'], $db_rec['Account_Name'], $db_rec['Bank_Name'], $db_rec['Account_Number'], $db_rec['BIC/SWIFT_Code'], $db_rec['Bank_Branch'], $db_rec['Withdrawal_amount'], $MAIL_FOOTER);
                break;

            case "WITHDRAWAL_MONEY_REQUEST_USER":
                $to_email = $db_rec['User_Email'];
                $key_arr = Array("#User_Name#", "#Withdrawal_amount#", "#MailFooter#");
                $val_arr = Array($db_rec['User_Name'], $db_rec['Withdrawal_amount'], $MAIL_FOOTER);
                break;

            case "CUSTOMER_FORGETPASSWORD":
                $to_email = $db_rec[0]['vEmail'];
                $key_arr = Array("#Name#", "#Email#", "#Password#", "#MailFooter#", "#SITE_NAME#");

                if ($db_rec[0]['vName'] != "" && $db_rec[0]['vLastName'] != "") {
                    $User_Name = $db_rec[0]['vName'] . " " . $db_rec[0]['vLastName'];
                } else {
                    $User_Name = $db_rec[0]['vCompany'];
                }

                $val_arr = Array($User_Name, $db_rec[0]['vEmail'], $this->decrypt($db_rec[0]['vPassword']), $MAIL_FOOTER, $SITE_NAME);
                break;

            case "EMAIL_VERIFICATION_USER":
                $to_email = $db_rec['vEmail'];
                $key_arr = Array("#Name#", "#activate_account#", "#MailFooter#");
                $val_arr = Array($db_rec['vName'], $db_rec['act_link'], $MAIL_FOOTER);
                break;

            case "MEMBER_RECEIVE_RATING":
                $to_email = $db_rec['ToEmail'];
                $key_arr = Array("#ToName#", "#FromName#", "#Feedback#", "#Rating#", "#MailFooter#");
                $val_arr = Array($db_rec['ToName'], $db_rec['FromName'], $db_rec['Feedback'], $db_rec['iRate'], $MAIL_FOOTER);
                break;

            case "MEMBER_GIVE_RATING":
                $to_email = $db_rec['FromEmail'];
                $key_arr = Array("#ToName#", "#FromName#", "#Feedback#", "#Rating#", "#MailFooter#");
                $val_arr = Array($db_rec['ToName'], $db_rec['FromName'], $db_rec['Feedback'], $db_rec['iRate'], $MAIL_FOOTER);
                break;

            case "MEMBER_RECEIVE_MESSAGE":
                $to_email = $db_rec['ToEmail'];
                $key_arr = Array("#ToName#", "#FromName#", "#Message#", "#MailFooter#");
                $val_arr = Array($db_rec['ToName'], $db_rec['FromName'], $db_rec['tMessage'], $MAIL_FOOTER);
                break;

            case "MEMBER_PUBLISH_STORY":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['FromEmail'];
                $key_arr = Array("#FromName#", "#FromEmail#", "#Title#", "#Description#", "#MailFooter#");
                $val_arr = Array($db_rec['FromName'], $db_rec['FromEmail'], $db_rec['Title'], $db_rec['tDescription'], $MAIL_FOOTER);
                break;

            case "DELETE_ACCOUNT":
                $to_email = $db_rec['EMAIL'];
                $key_arr = Array("#Name#", "#Email#", "#SITE_FOOTER#");
                $val_arr = Array($db_rec['NAME'] . " " . $db_rec['LAST_NAME'], $db_rec['EMAIL'], $MAIL_FOOTER);
                break;

            case "NEWRIDEOFFER_MEMBER":
                $to_email = $db_rec['vEmail'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "RIDER_INVOICE":
                $to_email = $db_rec['email'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "NEWRIDEOFFER_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "BOOKING_PASSENGER":
                $to_email = $db_rec['vBookerEmail'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "BOOKING_DRIVER":
                $to_email = $db_rec['vDriverEmail'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "BOOKING_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = Array("#Detail#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "TRIP_COMPLETION_MESSAGE":
                $to_email = $db_rec['vBookerEmail'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "RIDE_COMPLETION_CONFIRMATION_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "RIDE_COMPLETION_CONFIRMATION_DRIVER":
                $to_email = $db_rec['vDriverEmail'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "MEMBER_REGISTRATION_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = Array("#NAME#", "#EMAIL#", "#PASSWORD#", "#SOCIALNOTES#", "#MAILFOOTER#");
                $val_arr = Array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PASSWORD'], $db_rec['SOCIALNOTES'], $MAIL_FOOTER);
                break;

            case "DRIVER_REGISTRATION_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = Array("#NAME#", "#EMAIL#", "#PASSWORD#", "#SOCIALNOTES#", "#MAILFOOTER#");
                $val_arr = Array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PASSWORD'], $db_rec['SOCIALNOTES'], $MAIL_FOOTER);
                break;

            case "COMPANY_REGISTRATION_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = Array("#NAME#", "#EMAIL#", "#PASSWORD#", "#MAILFOOTER#");
                $val_arr = Array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PASSWORD'], $MAIL_FOOTER);
                break;

            case "DRIVER_REGISTRATION_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = Array("#NAME#", "#EMAIL#", "#MAILFOOTER#");
                $val_arr = Array($db_rec['NAME'], $db_rec['EMAIL'], $MAIL_FOOTER);
                break;

            case "COMPANY_REGISTRATION_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = Array("#NAME#", "#EMAIL#", "#MAILFOOTER#");
                $val_arr = Array($db_rec['NAME'], $db_rec['EMAIL'], $MAIL_FOOTER);
                break;

            case "RIDE_ALERT_EMAIL":
                $to_email = $db_rec['Email'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "RIDE_PAYMENT_EMAIL_DRIVER":
                $to_email = $db_rec['Email'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "DRIVER_CANCEL_BOOKING_TO_PASSENGER":
                $to_email = $db_rec['vBookerEmail'];
                $key_arr = Array("#vBookerName#", "#vBookingNo#", "#vFromPlace#", "#vToPlace#", "#dBookingDate#", "#tCancelreason#", "#vDriverName#", "#MailFooter#");
                $val_arr = Array($db_rec['vBookerName'], $db_rec['vBookingNo'], $db_rec['vFromPlace'], $db_rec['vToPlace'], $db_rec['dBookingDate'], $db_rec['tCancelreason'], $db_rec['vDriverName'], $MAIL_FOOTER);
                break;

            case "PASSENGER_CANCEL_BOOKING_TO_DRIVER":
                $to_email = $db_rec['vDriverEmail'];
                $key_arr = Array("#vBookerName#", "#vBookingNo#", "#vFromPlace#", "#vToPlace#", "#dBookingDate#", "#tCancelreason#", "#vDriverName#", "#MailFooter#");
                $val_arr = Array($db_rec['vBookerName'], $db_rec['vBookingNo'], $db_rec['vFromPlace'], $db_rec['vToPlace'], $db_rec['dBookingDate'], $db_rec['tCancelreason'], $db_rec['vDriverName'], $MAIL_FOOTER);
                break;

            case "CANCELLATION_USER":
                $to_email = $db_rec['email'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "CANCELLATION_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "REFUND_USER":
                $to_email = $db_rec['email'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "FORGOT_PASSWORD":
                $to_email = $db_rec['email'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "PAYMENT_VERIFICATION":
                $to_email = $db_rec['email'];
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "ACCOUNT_STATUS":
                $to_email = $db_rec['EMAIL'];
                $key_arr = Array("#NAME#", "#EMAIL#", "#details#", "#MAILFOOTER#");
                #echo "<pre>"; print_r($key_arr);
                $val_arr = Array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['DETAIL'], $MAIL_FOOTER);
                #echo "<pre>"; print_r($val_arr); exit;
                break;

            case "VEHICLE_BOOKING":
                $to_email = $db_rec['EMAIL'];
                $key_arr = Array("#NAME#", "#EMAIL#", "#details#", "#MAILFOOTER#");
                #echo "<pre>"; print_r($key_arr);
                $val_arr = Array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['DETAIL'], $MAIL_FOOTER);
                #echo "<pre>"; print_r($val_arr); exit;
                break;

            case "VEHICLE_BOOKING_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = Array("#NAME#", "#EMAIL#", "#details#", "#MAILFOOTER#");
                #echo "<pre>"; print_r($key_arr);
                $val_arr = Array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['DETAIL'], $MAIL_FOOTER);
                #echo "<pre>"; print_r($val_arr); exit;
                break;

            case "DOCCUMENT_UPLOAD":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = Array("#Company#", "#Name#", "#Email#", "#SITE_FOOTER#");
                #echo "<pre>"; print_r($key_arr);
                $val_arr = Array($db_rec['COMPANY'], $db_rec['NAME'], $db_rec['EMAIL'], $SITE_FOOTER);
                #echo "<pre>"; print_r($val_arr); exit;
                break;
            case "PROFILE_UPLOAD":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = Array("#USER#", "#Name#", "#Email#", "#SITE_FOOTER#");
                #echo "<pre>"; print_r($key_arr);
                $val_arr = Array($db_rec['USER'], $db_rec['NAME'], $db_rec['EMAIL'], $SITE_FOOTER);
                #echo "<pre>"; print_r($val_arr); exit;
                break;

            case "MANUAL_TAXI_DISPATCH_DRIVER":
                $to_email = $db_rec['vDriverMail'];
                $key_arr = Array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#DestinationAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = Array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['tDestAddress'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;

            case "MANUAL_TAXI_DISPATCH_RIDER":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = Array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#DestinationAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = Array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['tDestAddress'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;

            case "APP_EMAIL_VERIFICATION_USER":
                $to_email = $db_rec['vEmail'];
                $key_arr = Array("#NAME#", "#CODE#", "#MAIL_FOOTER#");
                $val_arr = Array($db_rec['vName'], $db_rec['CODE'], $MAIL_FOOTER);
                break;

            case "CRON_BOOKING_EMAIL":
                $to_email = $ADMIN_EMAIL;
                $key_arr = Array("#details#", "#MailFooter#");
                $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                break;

            case "CUSTOMER_RESET_PASSWORD":

                $to_email = $db_rec['EMAIL'];
                $key_arr = Array("#NAME#", "#EMAIL#", "#LINK#", "#MailFooter#");
                $val_arr = Array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['LINK'], $MAIL_FOOTER);
                break;

            case "PAYMENT_REQUEST_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = Array("#driver#", "#Name#", "#Email#", "#Trips#", "#Amount#", "#Account_Name#", "#Bank_Name#", "#Account_Number#", "#BIC/SWIFT_Code#", "#Bank_Branch#", "#MailFooter#");
                $val_arr = Array($db_rec['Name'], $db_rec['Name'], $db_rec['vEmail'], $db_rec['TripIds'], $db_rec['Total_Amount'] , $db_rec['Account_Name'], $db_rec['Bank_Name'], $db_rec['Account_Number'], $db_rec['BIC/SWIFT_Code'], $db_rec['Bank_Branch'] , $MAIL_FOOTER);
                break;
                
            case "MANUAL_TAXI_DISPATCH_DRIVER_APP":
				$to_email = $db_rec['vDriverMail'];
				$key_arr = Array("#Driver#","#Rider#","#BookingNo#","#SourceAddress#","#Ddate#","#MailFooter#");
				$val_arr = Array($db_rec['vDriver'],$db_rec['vRider'],$db_rec['vBookingNo'],$db_rec['vSourceAddresss'],$db_rec['dBookingdate'],$MAIL_FOOTER);
				break;
        
         case "MANUAL_TAXI_DISPATCH_DRIVER_APP_SP":
				$to_email = $db_rec['vDriverMail'];
				$key_arr = Array("#Driver#","#Rider#","#Booking_Number#","#SourceAddress#","#Ddate#","#MailFooter#");
				$val_arr = Array($db_rec['vDriver'],$db_rec['vRider'],$db_rec['vBookingNo'],$db_rec['vSourceAddresss'],$db_rec['dBookingdate'],$MAIL_FOOTER);
				break;
				
    		case "MANUAL_TAXI_DISPATCH_RIDER_APP":
				$to_email = $db_rec['vRiderMail'];
				$key_arr = Array("#Driver#","#Rider#","#BookingNo#","#SourceAddress#","#Ddate#","#MailFooter#");
				$val_arr = Array($db_rec['vDriver'],$db_rec['vRider'],$db_rec['vBookingNo'],$db_rec['vSourceAddresss'],$db_rec['dBookingdate'],$MAIL_FOOTER);
				break;
					
			case "MANUAL_CANCEL_TRIP_ADMIN":
				$to_email = $ADMIN_EMAIL;
				$key_arr = Array("#BookingNo#","#Driver#","#Rider#","#SourceAddress#","#DestinationAddress#","#Ddate#","#Reason#","#MailFooter#");
				$val_arr = Array($db_rec['vBookingNo'],$db_rec['DriverName'],$db_rec['RiderName'],$db_rec['vSourceAddresss'],$db_rec['tDestAddress'],$db_rec['dBooking_date'],$db_rec['vCancelReason'],$MAIL_FOOTER);
				break;
           
      case "MANUAL_CANCEL_TRIP_ADMIN_TO_DRIVER":
        $to_email = $db_rec['vDriverMail'];
        $key_arr = Array("#Driver#","#Rider#","#BookingNo#","#SourceAddress#","#Ddate#","#MailFooter#");
        $val_arr = Array($db_rec['vDriver'],$db_rec['vRider'],$db_rec['vBookingNo'],$db_rec['vSourceAddresss'],$db_rec['dBookingdate'],$MAIL_FOOTER);
        break;
        
      case "MANUAL_CANCEL_TRIP_ADMIN_TO_RIDER":
        $to_email = $db_rec['vRiderMail'];
        $key_arr = Array("#Driver#","#Rider#","#BookingNo#","#SourceAddress#","#Ddate#","#MailFooter#");
        $val_arr = Array($db_rec['vDriver'],$db_rec['vRider'],$db_rec['vBookingNo'],$db_rec['vSourceAddresss'],$db_rec['dBookingdate'],$MAIL_FOOTER);
        break;
        
      case "MANUAL_BOOKING_ACCEPT_BYDRIVER_SP":
        $to_email = $db_rec['vRiderMail'];
        $key_arr = Array("#PROVIDER_NAME#","#Rider#","#BOOKING_NUMBER#","#Ddate#","#MailFooter#");
        $val_arr = Array($db_rec['vDriver'],$db_rec['vRider'],$db_rec['vBookingNo'],$db_rec['dBookingdate'],$MAIL_FOOTER);
        break;
        
      case "MANUAL_BOOKING_DECLINED_BYDRIVER_SP":
        $to_email = $db_rec['vRiderMail'];
        $key_arr = Array("#PROVIDER_NAME#","#Rider#","#BOOKING_NUMBER#","#Ddate#","#MailFooter#");
        $val_arr = Array($db_rec['vDriver'],$db_rec['vRider'],$db_rec['vBookingNo'],$db_rec['dBookingdate'],$MAIL_FOOTER);
        break;
        
      case "MANUAL_BOOKING_CANCEL_BYDRIVER_SP":
        $to_email = $db_rec['vRiderMail'];
        $key_arr = Array("#Rider#","#PROVIDERNAME#","#BOOKING_NUMBER#","#Ddate#","#MailFooter#");
        $val_arr = Array($db_rec['RiderName'],$db_rec['DriverName'],$db_rec['vBookingNo'],$db_rec['dBookingdate'],$MAIL_FOOTER);
        break;
        
      case "MANUAL_BOOKING_CANCEL_BYRIDER_SP":
        $to_email = $db_rec['vDriverMail'];
        $key_arr = Array("#Rider#","#PROVIDERNAME#","#BOOKING_NUMBER#","#Ddate#","#MailFooter#");
        $val_arr = Array($db_rec['RiderName'],$db_rec['DriverName'],$db_rec['vBookingNo'],$db_rec['dBookingdate'],$MAIL_FOOTER);
        break;  

		 case "MEMBER_REGISTRATION_USER_FOR_MANUAL_BOOKING":
			$to_email = $db_rec['EMAIL'];
			$key_arr = Array("#NAME#", "#EMAIL#", "#PASSWORD#","#MAILFOOTER#");
			$val_arr = Array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PASSWORD'], $MAIL_FOOTER);
			break;
			
  	case "BANK_DETAIL_NOTIFY_ADMIN":
        $to_email = $ADMIN_EMAIL;
        $replyto = $db_rec['EMAIL'];
        $key_arr = Array("#NAME#", "#EMAIL#","#PHONE#" ,"#MAILFOOTER#");
        $val_arr = Array($db_rec['NAME'], $db_rec['EMAIL'],$db_rec['PHONE'] ,$MAIL_FOOTER);
        break;	
  				
  		 case "CRON_EXPIRY_DOCUMENT_EMAIL":
                  $to_email = $ADMIN_EMAIL;
                  $key_arr = Array("#details#", "#MailFooter#");
                  $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                  break;	
  		case "CRON_EXPIRY_DOCUMENT_EMAIL_SEVEN_DAY_AGO":
                  $to_email = $ADMIN_EMAIL;
                  $key_arr = Array("#details#", "#MailFooter#");
                  $val_arr = Array($db_rec['details'], $MAIL_FOOTER);
                  break;	
                  
      case "MANUAL_TAXI_DISPATCH_RIDER_RESCEDULE_APP":
				$to_email = $db_rec['vRiderMail'];
				$key_arr = Array("#Driver#","#Rider#","#BookingNo#","#SourceAddress#","#Ddate#","#MailFooter#");
				$val_arr = Array($db_rec['vDriver'],$db_rec['vRider'],$db_rec['vBookingNo'],$db_rec['vSourceAddresss'],$db_rec['dBookingdate'],$MAIL_FOOTER);
				break; 
        
       case "MANUAL_TAXI_DISPATCH_RIDER_RESCEDULE_ADMIN_APP":
				$to_email = $ADMIN_EMAIL;
				$key_arr = Array("#Driver#","#Rider#","#BookingNo#","#SourceAddress#","#Ddate#","#MailFooter#");
				$val_arr = Array($db_rec['vDriver'],$db_rec['vRider'],$db_rec['vBookingNo'],$db_rec['vSourceAddresss'],$db_rec['dBookingdate'],$MAIL_FOOTER);
				break;  
				
		  case "RIDER_TRIP_HELP_DETAIL":
                $to_email = $ADMIN_EMAIL;               
                $key_arr = Array("#NAME#","#iTripId#","#vComment#","#Ddate#", "#MailFooter#");
                $val_arr = Array($db_rec['NAME'], $db_rec['iTripId'],$db_rec['vComment'],$db_rec['Ddate'], $MAIL_FOOTER);
                break;		
        
    }
        $maillanguage = $this->get_user_preffered_language($to_email);
        $maillanguage = (isset($maillanguage) && $maillanguage != '') ? $maillanguage : 'EN';

        $mailsubject = $res[0]['vSubject_' . $maillanguage];
        $tMessage = $res[0]['vBody_' . $maillanguage];
        $tMessage = str_replace($key_arr, $val_arr, $tMessage);
        $tMessage = $this->general_mail_format_html($tMessage);

        $headers = '';
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . $EMAIL_FROM_NAME . "\n";
        /*$headers = "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=utf-8\nContent-Transfer-Encoding: 8bit\nX-Priority: 1\nX-MSMail-Priority: High\n";
        $headers .= "From: " . $EMAIL_FROM_NAME . " < $NOREPLY_EMAIL >" . "\n" . "X-Mailer: PHP/" . phpversion() . "\nX-originating-IP: " . $_SERVER['REMOTE_ADDR'] . "\n";*/

        if ($_SERVER['HTTP_HOST'] == "192.168.1.131") {
            $emailsend = mail($to_email, $mailsubject, $tMessage, $headers);
        } else {
           if($MAILGUN_ENABLE == "Yes"){
				$emailsend = $this->send_email_smtp($to_email, $NOREPLY_EMAIL, $EMAIL_FROM_NAME, $mailsubject, $tMessage,$replyto);
		   }else{
				$emailsend = mail($to_email, $mailsubject, $tMessage, $headers);
		   }
        }
        return $emailsend;
    }

    function send_messages_user($type, $db_rec = '', $newsid = '', $maillanguage='') {
        global $generalobj, $obj;
        $str = "select * from send_message_templates where vEmail_Code='" . $type . "'";
        $res = $obj->MySQLSelect($str);
        switch ($type) {
            case "DRIVER_SEND_MESSAGE":
                $key_arr = Array("#PASSENGER_NAME#", "#BOOKING_DATE#", "#BOOKING_TIME#", "#BOOKING_NUMBER#");
                $val_arr = Array($db_rec['PASSENGER_NAME'], $db_rec['BOOKING_DATE'], $db_rec['BOOKING_TIME'], $db_rec['BOOKING_NUMBER']);
                break;
                
            case "DRIVER_SEND_MESSAGE_SP":
                $key_arr = Array("#Booking_Number#");
                $val_arr = Array($db_rec['BOOKING_NUMBER']);
                break;    

            case "USER_SEND_MESSAGE":
                $key_arr = Array("#DRIVER_NAME#", "#PLATE_NUMBER#", "#BOOKING_DATE#", "#BOOKING_TIME#", "#BOOKING_NUMBER#");
                $val_arr = Array($db_rec['DRIVER_NAME'], $db_rec['PLATE_NUMBER'], $db_rec['BOOKING_DATE'], $db_rec['BOOKING_TIME'], $db_rec['BOOKING_NUMBER']);
                break;
                
            case "USER_SEND_MESSAGE_APP":               
                $key_arr = Array("#DRIVER_NAME#","#BOOKING_DATE#","#BOOKING_TIME#","#BOOKING_NUMBER#");
                $val_arr = Array($db_rec['DRIVER_NAME'],$db_rec['BOOKING_DATE'], $db_rec['BOOKING_TIME'] , $db_rec['BOOKING_NUMBER']);      
                break; 

            case "USER_SEND_MESSAGE_JOB_CANCEL":               
                $key_arr = Array("#Driver#","#Ddate#","#Bookingtime#","#BookingNo#");
                $val_arr = Array($db_rec['vDriver'],$db_rec['dBookingdate'], $db_rec['dBookingtime'] , $db_rec['vBookingNo']);      
                break;  
                                
            case "DRIVER_SEND_MESSAGE_JOB_CANCEL":               
                $key_arr = Array("#Rider#","#Ddate#","#Bookingtime#","#BookingNo#");
                $val_arr = Array($db_rec['vRider'],$db_rec['dBookingdate'], $db_rec['dBookingtime'] , $db_rec['vBookingNo']);      
                break;   

            case "BOOKING_ACCEPT_BYDRIVER_MESSAGE_SP":
                $key_arr = Array("#PROVIDER_NAME#","#BOOKING_NUMBER#");
                $val_arr = Array($db_rec['vDriver'],$db_rec['vBookingNo']);
                break;
                
            case "BOOKING_DECLINED_BYDRIVER_MESSAGE_SP":
                $key_arr = Array("#PROVIDER_NAME#","#BOOKING_NUMBER#");
                $val_arr = Array($db_rec['vDriver'],$db_rec['vBookingNo']);
                break;
                
            case "BOOKING_CANCEL_BYRIDER_MESSAGE_SP":
                $key_arr = Array("#USERNAME#","#BOOKING_NUMBER#");
                $val_arr = Array($db_rec['RiderName'],$db_rec['vBookingNo']);
                break;
                
            case "BOOKING_CANCEL_BYDRIVER_MESSAGE_SP":
                $key_arr = Array("#PROVIDERNAME#","#BOOKING_NUMBER#");
                $val_arr = Array($db_rec['DriverName'],$db_rec['vBookingNo']);
                break;                
        }

        // $maillanguage = get_user_preffered_language($to_email);
        $maillanguage = (isset($maillanguage) && $maillanguage != '') ? $maillanguage : 'EN';

        $mailsubject = $res[0]['vSubject_' . $maillanguage];
        $tMessage = $res[0]['vBody_' . $maillanguage];
        $tMessage = str_replace($key_arr, $val_arr, $tMessage);
        return $tMessage;
    }

    function sendUserSMS($mobileNo, $code, $fpass, $pass = '') {
        require_once(TPATH_CLASS . 'twilio/Services/Twilio.php');
        $account_sid = $this->getConfigurations("configurations", "MOBILE_VERIFY_SID_TWILIO");
        $auth_token = $this->getConfigurations("configurations", "MOBILE_VERIFY_TOKEN_TWILIO");
        $twilioMobileNum = $this->getConfigurations("configurations", "MOBILE_NO_TWILIO");

        $client = new Services_Twilio($account_sid, $auth_token);
        $toMobileNum = "+" . $code . $mobileNo;
        try {
            $sms = $client->account->messages->sendMessage($twilioMobileNum, $toMobileNum, $fpass);
            $success = "1";
        } catch (Services_Twilio_RestException $e) {
            $success = "0";
        }
        return $success;
    }

    function general_mail_format_html($mail_body) {
        global $tconfig, $COPYRIGHT_TEXT, $COMPANY_NAME;
        $mail_str = "";
        $mail_str = '<div style="width:100%!important;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;font-family:Arial, Helvetica, sans-serif;">
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
      <tr style="border-collapse:collapse">
        <td style="font-family:Arial, Helvetica, sans-serif; border-collapse:collapse" align="center"><table style="margin-top:0;margin-bottom:0;margin-right:0px;margin-left:0px" border="0" cellpadding="0" cellspacing="0" width="800">
            <tbody>
            <tr>
                <td style="font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; background:#54545e;" align="center" width="800">
                <table border="0" cellpadding="0" cellspacing="0"><tr><td>&nbsp;</td></tr></table>
                <table border="0" cellpadding="0" cellspacing="0"><tr><td>&nbsp;</td></tr></table>
                  <table border="0" cellpadding="0" cellspacing="0" width="700">
                    <tbody>
                      <tr>
                        <td style="background:#fff; border-left: 1px solid #e1e1e1;border-right: 1px solid #e1e1e1;"><div mc:edit="std_content10">
                            <table border="0" cellpadding="0" cellspacing="0" width="698">
                              <tbody>
                              <tr><td>&nbsp;</td></tr>
                                <tr>
                                  <td style="padding-top:10px; text-align:center" align="center" valign="top"><a target="_blank" href="' . $tconfig['tsite_url'] . '"><img src="' . $tconfig['tsite_img'] . '/header-logo-email.png" align="none" border="0"></a></td>
                                </tr>
                              <tr><td>&nbsp;</td></tr>
                              <tr><td>&nbsp;</td></tr>
                              </tbody>
                            </table>
                          </div></td>
                      </tr>
                    </tbody>
                  </table>
                  </td>
                  </tr>
              <tr>
                <td style="font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; background:#f5f5f5;" align="center" width="800">
                  <table border="0" cellpadding="0" cellspacing="0" width="700" style="border-top:1px solid #e1e1e1;">
                    <tbody>
                      <tr>
                        <td style="background:#fff; border-left:1px solid #e1e1e1;border-right:1px solid #e1e1e1;"><div mc:edit="std_content13">
                            <table align="center" border="0" cellpadding="0" cellspacing="0" width="671">
                              <tbody>
                                <tr><td>&nbsp;</td></tr>
                              </tbody>
                            </table>
                          </div></td>
                      </tr>
                    </tbody>
                  </table>
                  <table border="0" cellpadding="0" cellspacing="0" width="700">
                    <tbody>
                      <tr>
                        <td style="background:#fff; border-left: 1px solid #e1e1e1;border-right: 1px solid #e1e1e1;"><div mc:edit="std_content00">
                            <table align="center" border="0" cellpadding="2" cellspacing="0" width="671">
                              <tbody>
                                <tr>
                                  <td style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:#4d4d4d; text-align:justify;" valign="top"><table border="0" cellpadding="0" cellspacing="0" width="100%">
                                      <tbody>
                                        <tr>
                                          <td style="line-height:25px; font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#4d4d4d; text-align:justify;" valign="top">' . $mail_body . '</td>
                                        </tr>
                                      </tbody>
                                    </table>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </div></td>
                      </tr>
                    </tbody>
                  </table>
                  <table border="0" cellpadding="0" cellspacing="0" width="700">
                    <tbody>
                      <tr>
                        <td style="background:#fff; border-left:1px solid #e1e1e1; border-bottom:1px solid #e1e1e1; border-right:1px solid #e1e1e1;">
                          <div mc:edit="std_content17">
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <table border="0" cellpadding="0" cellspacing="0" width="700">
                  <tbody>
                   <tr><td>&nbsp;</td></tr>
                    <tr>
                     <td align="center" style="color:#626262; font-size:13px; font-family:Arial, Helvetica, sans-serif;">'.$COPYRIGHT_TEXT.'<br />
                      <a href="' . $tconfig['tsite_url'] . '" style="color:#070707; font-size:13px; font-family:Arial, Helvetica, sans-serif; text-decoration:none;">' . $COMPANY_NAME . '</a></td>
                      </tr>
                      <tr><td>&nbsp;</td></tr>
                      </tbody></table></td>
              </tr>
            </tbody>
          </table></td>
      </tr>
      <tr><td>&nbsp;</td></tr>
    </tbody>
  </table>
</div>';
/*<div style="width:100%!important;margin-top:0;margin-bottom:0;margin-right:0;margin-left:0;padding-top:0;padding-bottom:0;padding-right:0;padding-left:0;font-family:Arial, Helvetica, sans-serif;">
  <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody>
      <tr style="border-collapse:collapse">
        <td style="font-family:Arial, Helvetica, sans-serif; border-collapse:collapse" align="center"><table style="margin-top:0;margin-bottom:0;margin-right:0px;margin-left:0px" border="0" cellpadding="0" cellspacing="0" width="800">
            <tbody>
              <tr>
                <td style="font-family:Arial, Helvetica, sans-serif; border-collapse:collapse; background:#007d76;" align="center" width="800">
                <table border="0" cellpadding="0" cellspacing="0" width="700">
                    <tbody>
                      <tr>
                        <td><div mc:edit="std_content10">
                            <table border="0" cellpadding="0" cellspacing="0" width="698">
                              <tbody>
                              <tr><td colspan="4">&nbsp;</td></tr>
                                <tr>
                                <td align="center" valign="middle" colspan="4"><a target="_blank" href="' . $tconfig['tsite_url'] . '"><img src="' . $tconfig['tsite_img'] . '/header-logo-email.png" align="none" border="0"></a></td>
                                </tr>
                              <tr><td colspan="4">&nbsp;</td></tr>
                              </tbody>
                            </table>
                          </div></td>
                      </tr>
                    </tbody>
                  </table>
                  
                  <!-----------------------bot-end------------------->
                  
                  <table border="0" cellpadding="0" cellspacing="0" width="700">
                  <tbody>
                   <tr>

                    <td style="background:#fff;"><div mc:edit="std_content17">
            
            <table id="main" width="700" align="center" cellpadding="0" cellspacing="15" bgcolor="ffffff">
            <tr>
                <td>&nbsp;</td>
                </tr>
                              
            <tr>
            <td>
            <table id="content-2" cellpadding="0" cellspacing="0" align="center">
            <tr>
            <td width="770" style="font-family:Arial, Helvetica, sans-serif; color:#4d4d4d; font-size:16px; text-align:justify" valign="top">' . $mail_body . '</td>
            </tr>
            </table>
            </td>
            </tr>
            </table>

                     <table border="0" cellpadding="0" cellspacing="0" width="100%">
                        <tbody>
                            <tr>
                                <td>
                                    <img src="' . $tconfig['tsite_img'] . '/footer-img-email.jpg" alt="" />
                                </td>
                            </tr>
                        </tbody>
                     </table>
                          </div></td>
                      </tr>
                    </tbody>
                  </table>
                  
                  
                  <table border="0" cellpadding="0" cellspacing="0" width="700">
                    <tbody>
                      <tr>
                        <td><table align="center" border="0" cellpadding="5" cellspacing="0" width="671">
                            <tbody>
                            <tr>
                                <td>&nbsp;</td>
                              </tr>
                              <tr>
                                <td align="center" style="color:#FFFFFF; font-family:Arial, Helvetica, sans-serif; font-size:13px;">' . $COPYRIGHT_TEXT . '</td>
                              </tr>
                              <tr>
                                <td align="center"><a href="' . $tconfig['tsite_url'] . '" style="color:#ffa955; font-family:Arial, Helvetica, sans-serif; text-decoration:none;">' . $COMPANY_NAME . '</a></td>
                              </tr>
                              <tr>
                                <td>&nbsp;</td>
                              </tr>
                            </tbody>
                          </table></td>
                      </tr>
                    </tbody>
                  </table></td>
              </tr>
            </tbody>
          </table></td>
      </tr>
    </tbody>
  </table>
</div>*/
        return $mail_str;
    }

    function formatEventTime($time, $type, $locale = 'no_NO') {
        if ($locale == "fn_FN") {
            $locale = 'fr_FR';
        }
        //setlocale(LC_ALL, $locale);
        //setlocale(LC_ALL, $locale.".UTF-8");
        setlocale(LC_TIME, $locale . ".UTF-8");

        switch ($type) {
            case 'date' : $format = '%d';
                break;
            case 'm' : $format = '%B';
                break;
            case 'dm' : $format = '%d. %B';
                break;
            case 'time' : $format = '%H:%M';
                break;
            case 'dmy' : $format = '%B %d, %Y';
                break;
            case 'mdy' : $format = '%d %B %Y';
                break;
            case 'day' : $format = '%A';
                break;
            case 'month_year' : $format = '%B %Y';
                break;
            case 'dmy_main' : $format = '%d. %B %Y';
                break;
        }
        return strftime($format, @strtotime($time));
    }

    function DateTimeFormat($date) {
        $lwr = strtolower($_SESSION['sess_lang']);
        $upr = strtoupper($_SESSION['sess_lang']);
        $dlang = $lwr . "_" . $upr;
        $dt = $this->DateTime($date, 9);
        $newdate = $this->formatEventTime($dt, 'mdy', $dlang);
        return $newdate;
    }

    function DateDayFormat($date) {
        $lwr = strtolower($_SESSION['sess_lang']);
        $upr = strtoupper($_SESSION['sess_lang']);
        $dlang = $lwr . "_" . $upr;
        $dt = $this->DateTime($date, 9);
        $newdate = $this->formatEventTime($dt, 'm', $dlang);
        $newdate = substr($newdate, 0, 3);
        return $newdate;
    }

    function send_email_smtp($to, $from, $fromname, $subject, $body, $replyto, $attachment_path1 = "", $attachment_path2 = "", $pdf_attach = "") {

        global $site_path, $emailattach_dir, $MAILGUN_USER, $MAILGUN_KEY, $MAILGUN_HOST;

        require_once('class.phpmailer.php');
        //$to = "chiragd.esw@gmail.com";
        $mail = new PHPMailer();

        //$body             = eregi_replace("[\]",'',$message);

        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->Host = $MAILGUN_HOST ; // SMTP server
        $mail->SMTPDebug = false;                     // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only
        $mail->SMTPAuth = true;                  // enable SMTP authentication
        $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
        $mail->Host = "smtp.mailgun.org";      // sets GMAIL as the SMTP server
        $mail->Port = 465;                   // set the SMTP port for the GMAIL server 587
        if ($_SERVER['HTTP_HOST'] == "webprojectsdemo.com") {
            $mail->Username = $MAILGUN_USER;  // GMAIL username
            $mail->Password = $MAILGUN_KEY;            // GMAIL password
        } else {
            $mail->Username = $MAILGUN_USER;  // GMAIL username
            $mail->Password = $MAILGUN_KEY;            // GMAIL password
        }
        if ($replyto != "") {
            $mail->AddReplyTo($replyto, "");
        }

        $mail->SetFrom($from, $fromname);
        $mail->Subject = $subject;
        $mail->MsgHTML($body);
        $mail->AddAddress($to, "");

        if ($attachment_path1 != "") {
            $mail->AddAttachment($emailattach_dir . $attachment_path1);      // attachment
        }
        if ($attachment_path2 != "") {
            $mail->AddAttachment($emailattach_dir . $attachment_path2);      // attachment
        }
        if ($pdf_attach != "") {
            $mail->AddAttachment($pdf_attach);      // attachment
        }


        if (!$mail->Send()) {
            return false;
            //echo "Mailer Error: " . $mail->ErrorInfo;
            //exit;
        } else {
            return true;
        }
    }

    function gend_DisplayYear($selday = "", $fieldId = "", $limitStart = "", $limitEnd = "", $title = "", $onchange = "", $extra = "", $order = "asc") {
        $daycombo = "";
        $daycombo .= "<select name=\"$fieldId\" id=\"$fieldId\" $onchange $extra>";
        if ($title != "")
            $daycombo .= "<option value='' selected>" . $title . "</option>";
        if ($selday == "") {
            //$selday = date("Y");
        }
        if ($order == "asc") {
            for ($i = $limitStart; $i <= $limitEnd; $i++) {
                if (trim($selday) == trim($i)) {
                    $daycombo .= "<option value=" . $i . " selected>" . $i . "</option>";
                } else {
                    $daycombo .= "<option value=" . $i . ">" . $i . "</option>";
                }
            }
        } else {
            for ($i = $limitEnd; $i >= $limitStart; $i--) {
                if (trim($selday) == trim($i)) {
                    $daycombo .= "<option value=" . $i . " selected>" . $i . "</option>";
                } else {
                    $daycombo .= "<option value=" . $i . ">" . $i . "</option>";
                }
            }
        }
        $daycombo .= "</select>";

        return $daycombo;
    }

    function gend_DisplayMonth($selmonth = "", $fieldId = "", $extra = "", $All_Display = '') {
        $vmonth = array("January", "February", "March", "April ", "May", "June", "July", "August", "September", "October", "November", "December");
        $vmonthvalue = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");

        $monthcombo = "";
        $monthcombo .= "<select class=\"input\" name=\"$fieldId\" id=\"$fieldId\" $extra>";
        $monthcombo .= "<option value=''>Month</option>";
        for ($i = 0; $i < count($vmonthvalue); $i++) {
            if (trim($selmonth) == trim($vmonthvalue[$i])) {
                $monthcombo .= "<option value=" . $vmonthvalue[$i] . " selected>" . $vmonth[$i] . "</option>";
            } else {
                $monthcombo .= "<option value=" . $vmonthvalue[$i] . ">" . $vmonth[$i] . "</option>";
            }
        }
        $monthcombo .= "</select>";
        return $monthcombo;
    }

    function gend_DisplayDay($selday = "", $fieldId = "", $title = "", $extra = "") {
        $iday = array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31");
        $daycombo = "";
        $daycombo .= "<select  name=\"$fieldId\" id=\"$fieldId\" class='input' $extra>";
        if ($title != "")
            $daycombo .= "<option value='' selected>" . $title . "</option>";
        for ($i = 0; $i < count($iday); $i++) {

            if (trim($selday) == trim($iday[$i])) {
                $daycombo .= "<option value=" . $iday[$i] . " selected>" . $iday[$i] . "</option>";
            } else {
                $daycombo .= "<option value=" . $iday[$i] . ">" . $iday[$i] . "</option>";
            }
        }
        $daycombo .= "</select>";
        return $daycombo;
    }

    function gend_DisplayHour($selhr = "", $fieldId = "", $title = "", $extra = "") {

        $ihr = array("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23");
        $hrcombo = "";
        $hrcombo .= "<select  name=\"$fieldId\" id=\"$fieldId\" class='input' $extra>";
        if ($title != "")
            $hrcombo .= "<option value='' selected>" . $title . "</option>";
        for ($i = 0; $i < count($ihr); $i++) {

            if (trim($selhr) == trim($ihr[$i])) {
                $hrcombo .= "<option value=" . $ihr[$i] . " selected>" . $ihr[$i] . "</option>";
            } else {
                $hrcombo .= "<option value=" . $ihr[$i] . ">" . $ihr[$i] . "</option>";
            }
        }
        $hrcombo .= "</select>";
        return $hrcombo;
    }

    function gend_DisplayMnts($selmnts = "", $fieldId = "", $title = "", $extra = "") {
        $imnts = array("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46", "47", "48", "49", "50", "51", "52", "53", "54", "55", "56", "57", "58", "59");
        $mntscombo = "";
        $mntscombo .= "<select  name=\"$fieldId\" id=\"$fieldId\" class='input' $extra>";
        if ($title != "")
            $mntscombo .= "<option value='' selected>" . $title . "</option>";

        for ($i = 0; $i < count($imnts); $i++) {

            if (trim($selmnts) == trim($imnts[$i])) {
                $mntscombo .= "<option value=" . $imnts[$i] . " selected>" . $imnts[$i] . "</option>";
            } else {
                $mntscombo .= "<option value=" . $imnts[$i] . ">" . $imnts[$i] . "</option>";
            }
        }
        $mntscombo .= "</select>";
        return $mntscombo;
    }

    function DisplayYear($selday = "", $fieldId = "", $limitStart = "", $limitEnd = "", $AllDisplay = '') {
        //echo $selday;
        $daycombo = "";
        $daycombo .= "<select class=\"inputselectbox\" name=\"$fieldId\" id=\"$fieldId\">";
        if ($AllDisplay)
            $daycombo .= "<option value='All'>Years</option>";
        for ($i = $limitStart; $i <= $limitEnd; $i++) {
            if (trim($selday) == trim($i)) {
                $daycombo .= "<option value=" . $i . " selected>" . $i;
            } else {
                $daycombo .= "<option value=" . $i . ">" . $i;
            }
        }
        $daycombo .= "</select>";
        return $daycombo;
    }

    function getUniqueId($len = "") {
        if ($len == "")
            $len = 15;
        $better_token = strtoupper(md5(uniqid(rand(), true)));
        if ($len != "")
            $better_token = substr($better_token, 1, $len);
        return $better_token;
    }

    function getDateDiff($dformat, $endDate, $beginDate) {
        $date_parts1 = explode($dformat, $beginDate);
        $date_parts2 = explode($dformat, $endDate);
        $start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
        $end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
        $res = $end_date - $start_date;
        return $res;
    }

    //calculate years of age (input string: YYYY-MM-DD)
    function birthday($birthday) {
        list($year, $month, $day) = explode("-", $birthday);
        $year_diff = date("Y") - $year;
        $month_diff = date("m") - $month;
        $day_diff = date("d") - $day;
        if ($day_diff < 0 || $month_diff < 0)
            $year_diff--;
        return $year_diff;
    }

    function getLanguage() {
        global $obj;
        $sql = "SELECT iLanguageSpeakId, vLanguage" . $_SESSION['sess_lang_master'] . " as vLanguage FROM  language_speak_write WHERE eStatus =  'Active' ORDER BY vLanguage" . $_SESSION['sess_lang_master'] . "";
        $db_language = $obj->MySQLSelect($sql);
        return $db_language;
    }

    function getCategory() {
        global $obj;
        $sql = "SELECT iJobCategoryId, vJobCategory" . $_SESSION['sess_lang_master'] . " as vJobCategory  FROM job_category WHERE eStatus =  'Active' ORDER BY vJobCategory" . $_SESSION['sess_lang_master'] . "";
        $db_category = $obj->MySQLSelect($sql);

        return $db_category;
    }

    function pay_paypal_form($item_number, $type) {
        global $PAYPAL_MODE, $PAYPAL_ID, $site_url, $tconfig, $db_plans;
        $returnURL = $tconfig['tsite_url'] . "index.php?file=em-paypalstdpay&x_response_code=1";
        $cancleURL = $tconfig['tsite_url'] . "index.php?file=em-paypalstdpay&x_response_code=0";

        $logoURL = $tconfig['tsite_img'] . "/logo_paypal.jpg";
        $notifyURL = $tconfig['tsite_url'] . "index.php?file=em-paypal_ipn";
        if ($PAYPAL_MODE == "Test") {
            $paymentUrl = "https://www.sandbox.paypal.com/cgi-bin/webscr";
        } else {
            $paymentUrl = "https://www.paypal.com/cgi-bin/webscr";
        }

        $frmPaypal = '
			<FORM ACTION="' . $paymentUrl . '" METHOD="post" name="frmPaypal">
			<!-- <INPUT TYPE="hidden" NAME="cmd" VALUE="_xclick-subscriptions"> -->
			<INPUT TYPE="hidden" NAME="cmd" VALUE="_xclick">
			<INPUT TYPE="hidden" NAME="business" VALUE="' . $PAYPAL_ID . '">
			<INPUT TYPE="hidden" NAME="item_name" VALUE="' . $db_plans[0]['vPlanName'] . '">
			<INPUT TYPE="hidden" NAME="item_number" VALUE="' . $item_number . '">
			<input type="hidden" name="no_shipping" value="1">
			<input type="hidden" name="return" value="' . $returnURL . '">
			<input type="hidden" name="cancel_return" value="' . $cancleURL . '">
			<input type="hidden" name="notify_url" value="' . $notifyURL . '">
			<input type="Hidden" name="custom" value="' . $type . '">
			<input type="Hidden" name="orderType" value="New">
			<input type="hidden" name="amount" value="' . number_format($db_plans[0]['fPrice'], 2) . '">
			<input type="Hidden" name="currency" value="CAD">
			<input type="Hidden" name="cpp_header_image" value="' . $logoURL . '">
			<!-- <INPUT type="Submit" name="sub_mit" value="Submit">     -->
			<script>
			document.frmPaypal.submit();
			</script>
			';
        return $frmPaypal;
    }

    function display_banner($eLocation = "", $iCategoryId = "") {
        global $obj, $tconfig, $smarty;
        $get_se_prov = "SELECT * FROM banners WHERE eStatus = 'Active' and ePortion = '" . $eLocation . "' ORDER BY iDispOrder,rand()";
        $result = $obj->MySQLSelect($get_se_prov);

        if (count($result) > 0) {
            for ($i = 0; $i < count($result); $i++) {
                if (substr($result[$i]['tURL'], 0, 7) == "http://" || substr($result[$i]['tURL'], 0, 7) == "https://") {
                    $vLink = $result[$i]['tURL'];
                } else {
                    if ($result[$i]['tURL'] != "") {
                        $vLink = "http://" . $result[$i]['tURL'];
                    } else {
                        $vLink = "";
                    }
                }

                #$dis_banner_code = "";
                if ($result[$i]['eType'] == 'Image') {
                    if (@file_exists($tconfig["tsite_upload_images_banner_path"] . $result[$i]["vImage"]) && $result[$i]["vImage"] != "") {
                        if (strtolower(substr($result[$i]['vImage'], -3)) == 'swf') {
                            $dis_banner_code .= '<a href="' . $vLink . '" title="' . $result[$i]['vTitle'] . '" target="_blank" style="cursor:pointer" onclick="count_click(' . $result[$i]['iBannerId'] . ')">
								<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0" height="239" width="175">
								<param name="movie" value="' . $banner_image_url . $result[$i]['vImage'] . '" />
								<param name="wmode" value="transparent">
								<param name="quality" value="high" />
								<embed src="' . $banner_image_url . $result[$i]['vImage'] . '" quality="high" swliveconnect="true" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" height="239" width="175" ></embed>
								</object>
								</a><br/><br/>';
                        } else {
                            if ($vLink != "") {
                                $dis_banner_code .= '<a href="' . $vLink . '" title="' . $result[$i]['vTitle'] . '" target="_blank"><img src="' . $tconfig["tsite_upload_images_banner"] . $result[$i][vImage] . '" border="0" /></a><br/><br/>';
                            } else {
                                $dis_banner_code .= '<img src="' . $tconfig["tsite_upload_images_banner"] . $result[$i][vImage] . '" border="0" /><br/><br/>';
                            }
                        }
                    }
                } else {
                    $dis_banner_code .= stripslashes($result[$i]['tCustomCode']);
                }
            }
        }
        if ($result[0]["vImage"] != "") {
            return $dis_banner_code;
        } else {
            return $dis_banner_code;
        }
    }

    function cache_main_category() {
        global $obj;

        $sql = "select c.iMenuId, c.vMenu from menu c WHERE c.eStatus = 'Active' AND c.iParentId='0'";
        $db_sql = $obj->MySQLSelect($sql);

        for ($i = 0; $i < count($db_sql); $i++) {
            if ($this->isExistSubCategoryNew($db_sql[$i]['iMenuId'])) {
                $level_index++;
                $update_sql = "UPDATE menu set eSubExit='1' WHERE iMenuId='" . $db_sql[$i]['iMenuId'] . "'";

                $db_update = $obj->sql_query($update_sql);
            } else {
                $update_sql = "UPDATE menu set eSubExit='0' WHERE iMenuId='" . $db_sql[$i]['iMenuId'] . "'";
                $db_update = $obj->sql_query($update_sql);
            }
            $this->cache_sub_category($db_sql[$i][iParentId], $db_sql[$i][iMenuId]);
        }
    }

    function isExistSubCategoryNew($iCategoryId) {
        global $obj;
        $sub_sql = "select distinct c.iMenuId,c.vMenu  from menu c where c.eStatus = 'Active' and c.iParentId='" . $iCategoryId . "'";
        $db_sub = $obj->MySQLSelect($sub_sql);
        if (count($db_sub) > 0)
            return true;
        else
            return false;
    }

    function cache_sub_category($iParentId, $iCategoryId) {
        global $obj, $level_index;

        $sub_sql = "select c.iMenuId,c.vMenu,c.iParentId
			from menu c where c.eStatus = 'Active' AND
			c.iParentId='" . $iCategoryId . "'";
        $db_sub = $obj->MySQLSelect($sub_sql);
        if (count($db_sub) > 0) {
            for ($k = 0; $k < count($db_sub); $k++) {
                if ($this->isExistSubCategoryNew($db_sub[$k]['iMenuId'])) {
                    $update_sql = "UPDATE menu set eSubExit='1' WHERE iMenuId='" . $db_sub[$k]['iMenuId'] . "'";
                    $db_update = $obj->sql_query($update_sql);
                } else {
                    $update_sql = "UPDATE menu set eSubExit='0' WHERE iMenuId='" . $db_sub[$k]['iMenuId'] . "'";
                    $db_update = $obj->sql_query($update_sql);
                }
                $level_index = $db_sub[$k][iLevel] + 1;
                $this->cache_sub_category($db_sub[$k][iParentId], $db_sub[$k][iMenuId]);
            }
        } else {
            return false;
        }
    }

    function cache_left_menu() {
        global $obj, $display, $tconfig, $display;

        //$sql = "SELECT c.iMenuId, c.vMenu FROM menu c WHERE c.eStatus = 'Active' AND c.iParentId='0' and eTop='Yes' order by c.iDisplayOrder ASC";
        $sql = "SELECT m.iMenuId , m.iParentId, m.vMenu, p.iPageId, p.vPageTitle FROM menu as m LEFT JOIN pages as p ON m.iMenuId = p.iMenuId where m.eStatus = 'Active' AND m.iParentId='0' AND eTop = 'Yes' ORDER BY m.iDisplayOrder ASC LIMIT 0,6";
        $db_sql = $obj->MySQLSelect($sql);
        $display = "";
        $display = '<ul class="sf-menu" id="topnavigation">';
        for ($i = 0; $i < count($db_sql); $i++) {
            $activeclass = '';
            $titleCategory = ucwords($db_sql[$i]['vMenu']);
            $titleCategory = $this->replace_content($titleCategory);
            if ($this->isExistSubCategoryNew($db_sql[$i]['iMenuId'])) {
                //$new_url = $tconfig['tsite_url']."category/".$titleCategory."/".$db_sql[$i]['iMenuId'];
                $new_url = "javascript:void(0)";
            } else {
                if ($db_sql[$i]['iMenuId'] == 1) {
                    $new_url = $tconfig['tsite_url'];
                    //$activeclass = 'class="active"';
                } else {
                    $new_url = $tconfig['tsite_url'] . "pages/" . $titleCategory . "/" . $db_sql[$i]['iPageId'];
                }
            }
            $imgMenu = $db_sql[$i]['vMenu'];
            if ($db_sql[$i]['iMenuId'] == 3) {
                $name = "Who we are";
            }if ($db_sql[$i]['iMenuId'] == 4) {
                $name = "What we do";
            }if ($db_sql[$i]['iMenuId'] == 5) {
                $name = "Get in touch with us";
            } //text write under menu's name
            $IsBlank = "";
            if ($db_sql[$i]['iMenuId'] == 5) {
                $display .= '<li><a href="' . $new_url . '" ' . $activeclass . ' id="menu' . $db_sql[$i]['iMenuId'] . '" rel="parentmenu" style="width:108px;">' . $imgMenu . '<br /><span>' . $name . '</span></a>';
            } else {
                $display .= '<li><a href="' . $new_url . '" ' . $activeclass . ' id="menu' . $db_sql[$i]['iMenuId'] . '" rel="parentmenu">' . $imgMenu . '<br /><span>' . $name . '</span></a>';
            }
            //$display .= '<li><a href="'.$new_url.'" class="item1">'.$imgMenu.'</a>';
            $this->getChildLeftMenu($i, $db_sql[$i]['iParentId'], $db_sql[$i]['iMenuId'], 0, 'sub', $titleCategory);
            $display .= '</li>';
        }
        $display .= '</ul>';
        return $display;
        //$menu_file = $site_path."templates/left/left_menu.tpl";
        //$fp = fopen($menu_file, 'w');
        //fwrite($fp, $display);
        //fclose($fp);
    }

    function getChildLeftMenu($j, $iParentId, $iCategoryId, $k = '', $flag = '', $titleCategory) {
        global $obj, $display, $tconfig;

        $sub_sql = "select distinct c.iMenuId, c.iParentId,p.iPageId, c.vMenu from menu c left join pages p ON c.iMenuId=p.iMenuId  where c.eStatus = 'Active' AND c.iParentId='" . $iCategoryId . "' ORDER BY iDisplayOrder ASC";
        $db_sub = $obj->MySQLSelect($sub_sql);

        if (count($db_sub) > 0) {
            $display .= '<ul>';
            for ($i = 0; $i < count($db_sub); $i++) {
                $subTitle = ucwords($db_sub[$i]['vMenu']);
                $subTitle = $this->replace_content($subTitle);
                if (!$this->isExistSubCategoryNew($db_sub[$i]['iMenuId'])) {
                    if ($db_sub[$i]['iPageId'] == 14) {
                        $new_url = $tconfig['tsite_url'] . "ask-a-question";
                    } else if ($db_sub[$i]['iPageId'] == 15) {
                        $new_url = $tconfig['tsite_url'] . "request-information";
                    } else {
                        $new_url = $tconfig['tsite_url'] . "pages/" . $this->replace_content($subTitle) . "/" . $db_sub[$i]['iPageId'];
                    }
                } else {
                    $new_url = $tconfig['tsite_url'] . "pages/" . $this->replace_content($subTitle) . "/" . $db_sub[$i]['iPageId'];
                }
                if ((count($db_sub) - 1) == $i)
                    $style = 'style="border:none;"';
                if ($this->isExistSubCategoryNew($db_sub[$i]['iMenuId'])) {
                    $display .= '<li><a href="' . $new_url . '" id="menu' . $iCategoryId . $i . '" class="item2 arrow" ' . $style . ' ' . $IsBlank . '>' . stripslashes($db_sub[$i]["vMenu"]) . "" . '</a>';
                    $this->getChildLeftMenu('', $db_sub[$i]['iParentId'], $db_sub[$i]['iMenuId'], $i, 'sub', $titleCategory . "/" . $subTitle);
                    $display .= '</li>';
                } else {
                    $display .= '<li><a href="' . $new_url . '" id="menu' . $iCategoryId . $i . '" class="item2" ' . $style . ' ' . $IsBlank . '>' . stripslashes($db_sub[$i]["vMenu"]) . '</a></li>';
                }
            }
            $display .= '</ul>';
        } else {
            return false;
        }
    }

    /*
      function cache_left_menu()
      {
      global $obj,$display,$tconfig,$display;

      $sql = "SELECT c.iMenuId, c.vMenu FROM menu c WHERE c.eStatus = 'Active' AND c.iParentId='0' and eTop='Yes' order by c.iDisplayOrder ASC";
      $db_sql = $obj->MySQLSelect($sql);
      $display = "";
      $display = '<ul class="sf-menu">';
      for($i=0;$i<count($db_sql);$i++)
      {
      $titleCategory = ucwords($db_sql[$i]['vMenu']);
      $titleCategory = $this->replace_content($titleCategory);
      if($this->isExistSubCategoryNew($db_sql[$i]['iMenuId'])){
      //$new_url = $tconfig['tsite_url']."category/".$titleCategory."/".$db_sql[$i]['iMenuId'];
      $new_url = "javascript:void(0)";
      }else{
      $new_url = $tconfig['tsite_url']."pages/".$titleCategory."/".$db_sql[$i]['iMenuId'];
      }
      $imgMenu = $db_sql[$i]['vMenu'];
      $IsBlank = "";
      $display .= '<li><a href="'.$new_url.'" class="item1">'.$imgMenu.'</a>';
      $this->getChildLeftMenu($i,$db_sql[$i]['iParentId'],$db_sql[$i]['iMenuId'],0,'sub',$titleCategory);
      $display .= '</li>';
      }
      $display .= '</ul>';
      return $display;
      //$menu_file = $site_path."templates/left/left_menu.tpl";
      //$fp = fopen($menu_file, 'w');
      //fwrite($fp, $display);
      //fclose($fp);
      }

      function getChildLeftMenu($j,$iParentId,$iCategoryId,$k='',$flag='',$titleCategory)
      {
      global $obj,$display,$tconfig;

      $sub_sql = "select distinct c.iMenuId, c.iParentId, c.vMenu from menu c where c.eStatus = 'Active' AND c.iParentId='".$iCategoryId."'";
      $db_sub = $obj->MySQLSelect($sub_sql);

      if(count($db_sub) > 0 )
      {
      $display .= '<ul>';
      for($i=0;$i<count($db_sub);$i++)
      {
      $subTitle = ucwords($db_sub[$i]['vMenu']);
      $subTitle	= $this->replace_content($subTitle);
      if(!$this->isExistSubCategoryNew($db_sub[$i]['iMenuId'])){
      $new_url = $tconfig['tsite_url']."pages/".$this->replace_content($subTitle)."/".$db_sub[$i]['iMenuId'];
      }else{
      $new_url = $tconfig['tsite_url']."pages/".$this->replace_content($subTitle)."/".$db_sub[$i]['iParentId']."/".$db_sub[$i]['iMenuId'];
      }
      if((count($db_sub)-1) == $i)
      $style='style="border:none;"';
      if($this->isExistSubCategoryNew($db_sub[$i]['iMenuId']))
      {
      $display .= '<li><a href="'.$new_url.'" class="item2 arrow" '.$style.' '.$IsBlank.'>'. stripslashes($db_sub[$i]["vMenu"])."".'</a>';
      $this->getChildLeftMenu('',$db_sub[$i]['iParentId'],$db_sub[$i]['iMenuId'],$i,'sub',$titleCategory."/".$subTitle);
      $display .= '</li>';
      }
      else
      {
      $display .= '<li><a href="'.$new_url.'" class="item2" '.$style.' '.$IsBlank.'>'.stripslashes($db_sub[$i]["vMenu"]).'</a></li>';
      }

      }
      $display .= '</ul>';
      }
      else
      {
      return false;
      }
      }

     */

    function replace_content($vTitle) {
        $rs_catname = trim(strtolower(($vTitle)));
        $rs_catname = str_replace("/", "-", $rs_catname);
        $rs_catname = str_replace("�", "-", $rs_catname);
        $rs_catname = str_replace("(", "-", $rs_catname);
        $rs_catname = str_replace(")", "-", $rs_catname);
        $rs_catname = str_replace("?", "-", $rs_catname);
        $rs_catname = str_replace("-", "-", $rs_catname);
        $rs_catname = str_replace("#", "-", $rs_catname);
        $rs_catname = str_replace(",", "-", $rs_catname);
        $rs_catname = str_replace(";", "-", $rs_catname);
        $rs_catname = str_replace(":", "-", $rs_catname);
        $rs_catname = str_replace("'", "-", $rs_catname);
        $rs_catname = str_replace("\"", "-", $rs_catname);
        $rs_catname = str_replace("�", "-", $rs_catname);
        $rs_catname = str_replace("+", "-", $rs_catname);
        $rs_catname = str_replace("+", "-", $rs_catname);
        $rs_catname = str_replace("�", "-", $rs_catname);
        //$rs_catname = str_replace("s","_",$rs_catname);

        $rs_catname = str_replace(" ", "-", str_replace("&", "and", $rs_catname));
        return $rs_catname;
    }

    function get_menu_path_name($iPageId) {
        global $obj;
        $sql_catname = "select m.iMenuId, m.vPath from page_settings p left join menu m on p.iPageId = m.iPageId where m.iPageId ='" . $iPageId . "'";
        $db_sub = $obj->MySQLSelect($sql_catname);
        if ($db_sub[0]['vPath'] != "") {
            $vPath = $db_sub[0]['vPath'];
        } else {
            $vPath = "---";
        }
        return $vPath;
    }

    function get_menu_path($iMenuId) {
        global $obj, $category_arr, $category_name_arr;
        if ($iMenuId != "") {
            $category_arr = array();
            $category_name_arr = array();

            $this->get_parent_category($iMenuId);
            $category_arr = array_reverse($category_arr);
            $category_name_arr = array_reverse($category_name_arr);
            #print_r($category_name_arr);exit;
            $path = @implode(" >> ", $category_name_arr);
        }
        return $path;
    }

    function get_parent_category($iMenuId = '') {
        global $obj, $category_arr, $category_name_arr;
        if ($iMenuId != "") {
            $sql_catname = "select vMenu, iMenuId, iParentId from menu where iMenuId ='" . $iMenuId . "' order by vMenu";
            $db_sub = $obj->MySQLSelect($sql_catname);
            if ($db_sub[0]['iParentId'] != "") {
                $category_arr[] = $db_sub[0]["iMenuId"];
                $category_name_arr[] = $db_sub[0]["vMenu"];
                $this->get_parent_category($db_sub[0]['iParentId']);
            } else {
                return false;
            }
        }
        return $category_arr;
    }

    function get_service_type($iCandidateId) {
        global $obj, $candidate_arr, $candidate_name_arr;
        if ($iCandidateId != "") {
            $candidate_arr = array();
            $candidate_name_arr = array();

            $this->get_candidate_service($iCandidateId);
            $candidate_arr = array_reverse($candidate_arr);
            $candidate_name_arr = array_reverse($candidate_name_arr);
            //print_r($candidate_name_arr);exit;
            $path = @implode(",", $candidate_name_arr);
        }
        return $path;
    }

    function get_candidate_service($iCandidateId) {
        global $obj, $service_name_arr;
        if ($iCandidateId != "") {
            $service_name_arr = array();
            $sql_servicename = "SELECT c1.vTitle_EN,
				c.iServiceTypeId,
				c.iCandidateId
				FROM  candidate_service c left join service_type c1 on c1.iServiceTypeId = c.iServiceTypeId
				WHERE 1=1 and c.iCandidateId ='" . $iCandidateId . "'
				group by c.iServiceTypeId order by c.iServiceTypeId";
            $db_service = $obj->MySQLSelect($sql_servicename);
            for ($i = 0; $i < count($db_service); $i++) {
                if ($db_service[$i]['iServiceTypeId'] != "") {
                    $service_name_arr[] = $db_service[$i]["vTitle_EN"];
                } else {
                    return false;
                }
            }
            $name = implode(',', $service_name_arr);
            return $name;
        }
    }

    function Make_Price($text, $parameter = 2) {
        return number_format($text, $parameter, '.', ',');
    }

    function Make_Currency($text, $parameter = 2, $defCurrency = "") {
        global $DEFAULT_PRICE_RATIO;
        $defCurrency = $_SESSION['sess_price_ratio'];

        /* if($defCurrency == "GBP"){
          $defCurrency = "&pound;";
          }else if($defCurrency == "NOK"){
          $defCurrency = "NOK";
          }else if($defCurrency == "SEK"){
          $defCurrency = "kr";
          }else if($defCurrency == "DKK"){
          $defCurrency = "kr";
          }else if($defCurrency == "PLN"){
          $defCurrency = "zl";
          }else if($defCurrency == "RUB"){
          $defCurrency = "RUB";
          }else if($defCurrency == "USD"){
          $defCurrency = "USD";
          }else{
          //$defCurrency = "&euro;";
          $defCurrency = "&dollar;";
          } */

        $db_curr_mst = unserialize(db_curr_mst);
        for ($i = 0; $i < count($db_curr_mst); $i++) {
            if ($defCurrency == $db_curr_mst[$i]['vName']) {
                $defCurrency = $db_curr_mst[$i]['vSymbole'];
            }
        }

        if ($text == 0) {
            /* if($defCurrency == "GBP"){
              return "&pound; 0.00";
              }else if($defCurrency == "NOK"){
              return "NOK 0.00";
              }else if($defCurrency == "SEK"){
              return "kr 0.00";
              }else if($defCurrency == "DKK"){
              return "kr 0.00";
              }else if($defCurrency == "PLN"){
              return "zl 0.00";
              }else if($defCurrency == "RUB"){
              return "RUB 0.00";
              }else if($defCurrency == "USD"){
              return "&dollar; 0.00";
              }else{
              //return "&euro; 0.00";
              return "&dollar; 0.00";
              } */

            for ($i = 0; $i < count($db_curr_mst); $i++) {
                if ($defCurrency == $db_curr_mst[$i]['vName']) {
                    return $db_curr_mst[$i]['vSymbole'] . " 0.00";
                }
            }
        } else {
            return $defCurrency . " " . number_format($text, $parameter, '.', ',');
        }
    }

    function getstate_name($field, $table, $whereclouse, $code, $countrytext, $countrycode) {
        global $obj;
        $sqlsta = "select " . $field . " from " . $table . " where " . $whereclouse . "='" . $code . "' and " . $countrytext . "='" . $countrycode . "'";
        $db_sta = $obj->MySQLSelect($sqlsta);
        $state = $db_sta[0]["$field"];
        return $state;
    }

    function return_country_name($vCountry) {
        global $obj, $TableObj;
        $sql = "SELECT vCountry FROM " . $TableObj->tbl_arr['CountryMaster'] . " WHERE vCountryCode = '" . $vCountry . "'";
        $adminname = $obj->MySQLSelect($sql);
        return $adminname[0]['vCountry'];
    }

    function return_count($iUserId) {
        global $obj, $TableObj;
        $sql = "SELECT * FROM photos WHERE iUserId = '" . $iUserId . "'";
        $adminname = $obj->MySQLSelect($sql);
        $num = count($adminname);
        return $num;
    }

    function getParentCategory_StaticList($iParentId = 0, $old_cat = "", $loop = 1, $showsub = "", $loopfalse = "") {
        global $obj, $par_menu_arr;

        if ($showsub == "No") {
            $ssql = "and eSubExit='1'";
        }
        $sql_query = "select iMenuId, vMenu, eSubExit from menu where iParentId='$iParentId' and eStatus = 'Active' $ssql";
        //$sql_query .= " order by iDisplayOrder";

        $db_cat_rs = $obj->MySQLSelect($sql_query);
        $n = count($db_cat_rs);
        if ($n > 0) {
            if ($loopfalse != '') {
                if ($loop >= $loopfalse)
                    return false;
            }
            for ($i = 0; $i < $n; $i++) {
                $par_menu_arr[] = array('iMenuId' => $db_cat_rs[$i]['iMenuId'], 'vMenu' => $old_cat . "--|" . $loop . "|&nbsp;&nbsp;" . $db_cat_rs[$i]['vMenu'], 'loop' => $loop, 'eSubExit' => $db_cat_rs[$i]['eSubExit']);
                $this->getParentCategory_StaticList($db_cat_rs[$i]['iMenuId'], $old_cat . "&nbsp;&nbsp;&nbsp;&nbsp;", $loop + 1, $showsub, $loopfalse);
            }
            $old_cat = "";
        }
        return $par_menu_arr;
    }

    function getPostFormData($POST_Arr, $msg = "", $action = "") {
        $str = '
			<html>
			<form name="frm1" action="' . $action . '" method=post>';
        foreach ($POST_Arr as $key => $value) {
            if ($key != "mode") {
                if (is_array($value)) {
                    foreach ($value as $subkey => $subval) {
                        $str .= '<input type="Hidden" name="' . $key . '[' . $subkey . ']" value="' . stripslashes($subval) . '">';
                    }
                } else {
                    $str .= '<input type="Hidden" name="' . $key . '" value="' . stripslashes($value) . '">';
                }
            }
        }
        $str .= '<input type="Hidden" name="var_err_msg" value="' . $msg . '">
			</form>
			<script>
			document.frm1.submit();
			</script>
			</html>';

        echo $str;
        exit;
    }

    /* function formatEventTime($time, $type, $locale = 'no_NO') {
      setlocale(LC_ALL, $locale);

      switch($type) {
      case 'date' :  $format = '%d'; break;
      case 'dm'   :  $format = '%d. %B'; break;
      case 'time' :  $format = '%H:%M'; break;
      case 'dmy'  :  $format = '%B %d, %Y'; break;
      case 'day'  :  $format = '%A'; break;
      case 'month_year'  :  $format = '%B %Y'; break;
      }
      return strftime($format, @strtotime($time));
      } */

    function ride_total_price($id) {
        global $obj;
        //$sql="select sum(rp.fPrice) as tot_price,c.vName from ride_price as rp LEFT JOIN currency as c ON c.iCurrencyId=rp.iCurrencyId where rp.iRideId='".$id."' group by rp.iCurrencyId";
        $sql = "select SUM(fPrice) as tot_price from ride_points_new where iRideId='" . $id . "' and eReverse='No'";
        $db_pirce = $obj->MySQLSelect($sql);

        return $db_pirce[0]['tot_price'];
        //return $db_pirce[0]['vName']." ".$db_pirce[0]['tot_price'];
        //  echo "<pre>"; print_r($db_pirce);  exit;
    }

    function booking_currency($text, $defCurrency = "", $parameter = 2) {

        $db_curr_mst = unserialize(db_curr_mst);
        for ($i = 0; $i < count($db_curr_mst); $i++) {
            if ($defCurrency == $db_curr_mst[$i]['vName']) {
                $defCurrency = $db_curr_mst[$i]['vSymbole'];
            }
        }

        if ($text == 0) {
            for ($i = 0; $i < count($db_curr_mst); $i++) {
                if ($defCurrency == $db_curr_mst[$i]['vName']) {
                    $defCurrency = $db_curr_mst[$i]['vSymbole'] . " 0.00";
                }
            }
            return $defCurrency;
        } else {
            if ($text > 1) {
                return $defCurrency . " " . number_format($text, $parameter, '.', '');
            } else {
                return $defCurrency . " " . number_format($text, $parameter, '.', '');
            }
        }
    }

    function send_sms($phone_number, $message) {
        global $CLICATEL_USERNAME, $CLICATEL_PASSWORD, $CLICATEL_API_ID, $TWILIO_ACCOUNT_SID, $TWILIO_AUTH_TOKEN, $TWILIO_FROM_NUMBER;
        $sendby = "twilio";
        if ($sendby == "clicatel") {
            $user = $CLICATEL_USERNAME;
            $password = $CLICATEL_PASSWORD;
            $api_id = $CLICATEL_API_ID;
            $baseurl = "http://api.clickatell.com";

            $text = urlencode($message);
            $to = $phone_number;

            // auth call
            $url = "$baseurl/http/auth?user=$user&password=$password&api_id=$api_id";

            // do auth call
            $ret = file($url);

            // explode our response. return string is on first line of the data returned
            $sess = explode(":", $ret[0]);
            if ($sess[0] == "OK") {
                $sess_id = trim($sess[1]); // remove any whitespace
                $url = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text";

                // do sendmsg call
                $ret = file($url);
                $send = explode(":", $ret[0]);

                if ($send[0] == "ID") {
                    //echo "successnmessage ID: ". $send[1];
                } else {
                    //echo "send message failed";
                }
            } else {
                //echo "Authentication failure: ". $ret[0];
            }
        }

        if ($sendby == "twilio") {

            require TPATH_LIBRARIES . '/Services/Twilio.php';
            // Step 2: set our AccountSid and AuthToken from www.twilio.com/user/account
            $AccountSid = $TWILIO_ACCOUNT_SID;
            $AuthToken = $TWILIO_AUTH_TOKEN;

            // Step 3: instantiate a new Twilio Rest Client
            $client = new Services_Twilio($AccountSid, $AuthToken);

            // Step 4: make an array of people we know, to send them a message.
            // Feel free to change/add your own phone number and name here.
            $people = array(
                "+" . $phone_number => "name",
            );

            // Step 5: Loop over all our friends. $number is a phone number above, and
            // $name is the name next to it
            foreach ($people as $number => $name) {

                $sms = $client->account->messages->sendMessage(
                        // Step 6: Change the 'From' number below to be a valid Twilio number
                        // that you've purchased, or the (deprecated) Sandbox number
                        //from number
                        $TWILIO_FROM_NUMBER,
                        // the number we are sending to - Any phone number
                        $number,
                        // the sms body
                        $message
                );

                // Display a confirmation message on the screen
                //echo "Sent message to $name";
            }
        }
    }

    function clean_phone($text) {
        $pattern = '/(\d{3}|\d{4})|(\d{3,+})/i';
        preg_match($pattern, $text, $matches);
        return (isset($matches[1])) ? str_replace($matches[1], "*****", $text) : $text;
    }

    function replace_phone_email_url($x) {
		if($x != ''){
			$x = $this->clean_phone($x);
			$x = preg_replace("/([0-9]{3})\.?([0-9]{3})\.?([0-9]{4})/", "*****", $x);
			$format_one .= '\d{10}'; //5085551234
			$format_two_and_three .= '\d{3}(\.|\-)\d{3}\2\d{4}'; //508.555.1234 or 508-555-1234
			$format_four .= '\(\d{3}\)\-\d{3}\-\d{4}'; //(508)-555-1234
			$pattern = '!(\b\+?[0-9()\[\]./ -]{7,17}\b|\b\+?[0-9()\[\]./ -]{7,17}\s+(extension|x|#|-|code|ext)\s+[0-9]{1,6})!i';

			$x = preg_replace("~($pattern)~", '*****', $x);
			$x = preg_replace("~({$format_one}|{$format_two_and_three}|{$format_four})~", '*****', $x);
			$x = preg_replace('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i', '*****', $x); // extract email
			$x = preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', '****', $x); // extract url
			$x = preg_replace('/(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?/', '*****', $x); // extract phonenumber
			//$x = preg_replace('/(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([1-9]1[01-9]|[1-9][01-8]1|[1-9][01-9][01-9])\s*\)|([1-9]1[01-9]|[1-9][01-9]1|[1-9][01-8][01-9]))\s*(?:[.-]\s*)?)?([1-9]1[01-9]|[1-9][01-9]1|[1-9][01-9]{1})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?/','*****',$x); // extract phonenumber
			$words = array('Zero', 'One', 'Two', 'Four', 'five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen', 'Twenty', 'Twentyone', 'Twentytwo', 'Twentythree', 'Twentyfour', 'Twentyfive', 'Twentysix', 'Twentyseven', 'Twentyeight', 'Twentynine', 'Thirty', 'Thirtyone', 'Thirtytwo', 'Thirtythree', 'Thirtyfour', 'Thirtyfive', 'Thirtysix', 'Thirtyseven', 'Thirtyeight', 'Thirtynine', 'Forty', 'Fortyone', 'Fortytwo', 'Fortythree', 'Fortyfour', 'Fortyfive', 'Fortysix', 'Fortyseven', 'Fortyeight', 'Fortynine', 'Fifty', 'Fiftyone', 'Fiftytwo', 'Fiftythree', 'Fiftyfour', 'Fiftyfive', 'Fiftysix', 'Fiftyseven', 'Fiftyeight', 'Fiftynine', 'Sixty', 'Sixtyone', 'Sixtytwo', 'Sixtythree', 'Sixtyfour', 'Sixtyfive', 'Sixtysix', 'Sixtyseven', 'Sixtyeight', 'Sixtynine', 'Seventy', 'Seventyone', 'Seventytwo', 'Seventythree', 'Seventyfour', 'Seventyfive', 'Seventysix', 'Seventyseven', 'Seventyeight', 'Seventynine', 'Eighty', 'Eightyone', 'Eightytwo', 'Eightythree', 'Eightyfour', 'Eightyfive', 'Eightysix', 'Eightyseven', 'Eightyeight', 'Eightynine', 'Ninety', 'Ninetyone', 'Ninetytwo', 'Ninetythree', 'Ninetyfour', 'Ninetyfive', 'Ninetysix', 'Ninetyseven', 'Ninetyeight', 'Ninetynine', 'Hundred');
			$words1 = array('zero', 'one', 'two', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen', 'twenty', 'twentyone', 'twentytwo', 'twentythree', 'twentyfour', 'twentyfive', 'twentysix', 'twentyseven', 'twentyeight', 'twentynine', 'thirty', 'thirtyone', 'thirtytwo', 'thirtythree', 'thirtyfour', 'thirtyfive', 'thirtysix', 'thirtyseven', 'thirtyeight', 'thirtynine', 'forty', 'fortyone', 'fortytwo', 'fortythree', 'fortyfour', 'fortyfive', 'fortysix', 'fortyseven', 'fortyeight', 'fortynine', 'fifty', 'fiftyone', 'fiftytwo', 'fiftythree', 'fiftyfour', 'fiftyfive', 'fiftysix', 'fiftyseven', 'fiftyeight', 'fiftynine', 'sixty', 'sixtyone', 'sixtytwo', 'sixtythree', 'sixtyfour', 'sixtyfive', 'sixtysix', 'sixtyseven', 'sixtyeight', 'sixtynine', 'seventy', 'seventyone', 'seventytwo', 'seventythree', 'seventyfour', 'seventyfive', 'seventysix', 'seventyseven', 'seventyeight', 'seventynine', 'eighty', 'eightyone', 'eightytwo', 'eightythree', 'eightyfour', 'eightyfive', 'eightysix', 'eightyseven', 'eightyeight', 'eightynine', 'ninety', 'ninetyone', 'ninetytwo', 'ninetythree', 'ninetyfour', 'ninetyfive', 'ninetysix', 'ninetyseven', 'ninetyeight', 'ninetynine', 'hundred');
			$x = str_replace($words, '*****', $x);
			$x = str_replace($words1, '*****', $x);
		}
        return $x;
    }

    function img_data_upload($temp_gallery, $vImage_name1, $path, $size1, $size2, $size3, $size4) {
        global $thumb;
        $filename = $vImage_name1;
        $time_val = time();
        $img_arr = explode(".", $vImage_name1);
        $fileextension = $img_arr[count($img_arr) - 1]; 

        $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1); // generate image_file, set filename to resize/resample
        $thumb->size_auto($size1);    // set the biggest width or height for thumbnail
        $thumb->jpeg_quality(100);
        $thumb->save($path . "1" . "_" . $filename);
        $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
        $thumb->size_auto($size2);       // set the biggest width or height for thumbnail
        $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
        $thumb->save($path . "2" . "_" . $filename);
        $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
        $thumb->size_auto($size3);       // set the biggest width or height for thumbnail
        $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
        $thumb->save($path . "3" . "_" . $filename);
        $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
        $thumb->size_auto($size5);       // set the biggest width or height for thumbnail
        $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
        $thumb->save($path . "5" . "_" . $filename);
        $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
        $thumb->size_auto($size4);       // set the biggest width or height for thumbnail
        $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
        $thumb->save($path . "4" . "_" . $filename);
        $vImage1 = $filename;

        return $vImage1;
    }

    function getpagedetails($iPageId) {
        global $obj, $tconfig;
        $sql = "SELECT iPageId, vImage, vPageTitle" . $_SESSION['sess_lang_pref'] . " AS vPageTitle,tPageDesc" . $_SESSION['sess_lang_pref'] . " as tPageDesc FROM pages WHERE iPageId = '" . $iPageId . "'";
        $db_page = $obj->MySQLSelect($sql);
        $Photo_Gallery_folder = $tconfig["tsite_upload_media_partners_path"];
        $imgname = $Photo_Gallery_folder . "/" . $db_pages[0]['vImage'];
        if (is_file($tconfig["tsite_upload_media_partners_path"] . $db_page[0]['vImage'])) {
            $db_page[0]['img_url'] = $tconfig["tsite_upload_images_media_partners"] . $db_page[0]['vImage'];
        } else {
            $db_page[0]['img_url'] = '';
        }

        return $db_page;
    }

    /* Add by Hemali to find base URL */

    function home_base_url() {
        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
        $tmpURL = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace(chr(92), '/', dirname(__FILE__)));
        $tmpURL = rtrim(ltrim($tmpURL, '/'), '/');
        if (strpos($tmpURL, '/')) {
            $tmpURL = explode('/', $tmpURL);
            $tmpURL = $tmpURL[0];
        }
        if ($tmpURL !== $_SERVER['HTTP_HOST'])
            $base_url .= $_SERVER['HTTP_HOST'] . '/' . $tmpURL . '/';
        else
            $base_url .= $tmpURL . '/';

        return $base_url;
    }

    /* Added by Urvashi to fetch pagetitle frm pages table */

    function getPageTitle($id) {
        global $obj;
        $sql = "SELECT vPageName,iPageId,vPageTitle" . $_SESSION['sess_lang_pref'] . " AS vPageTitle,tPageDesc" . $_SESSION['sess_lang_pref'] . " as tPageDesc FROM pages WHERE iPageId = '" . $id . "'";
        $db_page = $obj->MySQLSelect($sql);
        return $db_page[0]["vPageTitle"];
    }

    /* function added by urvashi for creating url name for footer Static pages */

    function getPageUrlName($id) {
        global $obj;
        $sql = "SELECT vPageName,iPageId,vPageTitle" . $_SESSION['sess_lang_pref'] . " AS vPageTitle,tPageDesc" . $_SESSION['sess_lang_pref'] . " as tPageDesc FROM pages WHERE iPageId = '" . $id . "'";
        $db_page = $obj->MySQLSelect($sql);
        $st = strtolower($db_page[0]["vPageTitle"]);
        $st = preg_replace("/[^a-z0-9_\s-]/", "", $st);
        //Clean up multiple dashes or whitespaces
        $st = preg_replace("/[\s-]+/", " ", $st);
        //Convert whitespaces and underscore to dash
        $st = preg_replace("/[\s_]/", "-", $st);
        return $st;
    }

    //Function addded for url creation of Rides Details
    function getRideDetail($str) {

        $st = strtolower($str);
        $st = preg_replace("/[^a-z0-9_\s-]/", "", $st);
        $st = preg_replace("/[\s-]+/", " ", $st);
        $st = preg_replace("/[\s_]/", "-", $st);
        return $st;
    }

    // function for finding SEO meta tags from configurations or pages
    function setMeta($arg, $script = '', $id = '') {
        global $tconfig, $obj;

        $db_page = array();
        if ($id != '' && $script == 'page') {
            /* for static pages */
            $sql = "SELECT vPageName,iPageId,vPageTitle" . $_SESSION['sess_lang_pref'] . " AS vPageTitle,tPageDesc" . $_SESSION['sess_lang_pref'] . " as tPageDesc,vTitle,tMetaKeyword,tMetaDescription FROM pages WHERE iPageId = '" . $id . "'";
            $db_page = $obj->MySQLSelect($sql);
        } else {
            /* find from configurations */
           // $wri_usql = "SELECT * FROM configurations where eStatus='Active'";
            $wri_usql = "SELECT * FROM configurations where eStatus='Active'";
            $wri_ures = $obj->MySQLSelect($wri_usql);

            for ($i = 0; $i < count($wri_ures); $i++) {
                $vName = $wri_ures[$i]["vName"];
                $vValue = $wri_ures[$i]["vValue"];
                global $$vName;
                $$vName = $vValue;
            }
        }
        switch ($script) {
            case 'page':

                if ($arg == 'title')
                    return $db_page[0]["vTitle"];
                if ($arg == 'keyword')
                    return $db_page[0]["tMetaKeyword"];
                if ($arg == 'desc')
                    return $db_page[0]["tMetaDescription"];
                break;

            case 'contactus' :
                if ($arg == 'title')
                    return $CONTACT_US_TITLE;
                if ($arg == 'keyword')
                    return $CONTACT_US_KEYWORD;
                if ($arg == 'desc')
                    return $CONTACT_US_DESC;
                break;

            case 'faqs' :
                if ($arg == 'title')
                    return $FAQS_TITLE;
                ;
                if ($arg == 'keyword')
                    return $FAQS_KEYWORD;;
                if ($arg == 'desc')
                    return $FAQS_DESC;;
                break;

            case 'login' :
                if ($arg == 'title')
                    return $LOGIN_TITLE;
                ;
                if ($arg == 'keyword')
                    return $LOGIN_KEYWORD;;
                if ($arg == 'desc')
                    return $LOGIN_DESC;;
                break;

            case 'offer_ride' :
                if ($arg == 'title')
                    return $OFFER_RIDE_TITLE;
                ;
                if ($arg == 'keyword')
                    return $OFFER_RIDE_KEYWORD;;
                if ($arg == 'desc')
                    return $OFFER_RIDE_DESC;;
                break;

            case 'find_ride' :
                if ($arg == 'title')
                    return $FIND_RIDES_TITLE;
                ;
                if ($arg == 'keyword')
                    return $FIND_RIDES_KEYWORD;;
                if ($arg == 'desc')
                    return $FIND_RIDES_DESC;;
                break;

            case 'dashboard' :
                if ($arg == 'title')
                    return $DASHBOARD_TITLE;
                ;
                if ($arg == 'keyword')
                    return $DASHBOARD_KEYWORD;;
                if ($arg == 'desc')
                    return $DASHBOARD_DESC;;
                break;

            case 'car_details' :
                if ($arg == 'title')
                    return $CAR_DETAILS_TITLE;
                ;
                if ($arg == 'keyword')
                    return $CAR_DETAILS_KEYWORD;;
                if ($arg == 'desc')
                    return $CAR_DETAILS_DESC;;
                break;

            case 'mybooking' :
                if ($arg == 'title')
                    return $MYBOOKING_TITLE;
                if ($arg == 'keyword')
                    return $MYBOOKING_KEYWORD;
                if ($arg == 'desc')
                    return $MYBOOKING_DESC;
                break;

            case 'list_rides_offer' :
                if ($arg == 'title')
                    return $LIST_RIDES_OFFER_TITLE;
                if ($arg == 'keyword')
                    return $LIST_RIDES_OFFER_KEYWORD;
                if ($arg == 'desc')
                    return $LIST_RIDES_OFFER_DESC;
                break;

            case 'received_messages' :
                if ($arg == 'title')
                    return $RECEIVED_MESSAGE_TITLE;
                if ($arg == 'keyword')
                    return $RECEIVED_MESSAGE_KEYWORD;
                if ($arg == 'desc')
                    return $RECEIVED_MESSAGE_DESC;
                break;

            case 'archived_messages' :
                if ($arg == 'title')
                    return $ARCHIVED_MESSAGE_TITLE;
                if ($arg == 'keyword')
                    return $ARCHIVED_MESSAGE_KEYWORD;
                if ($arg == 'desc')
                    return $ARCHIVED_MESSAGE_DESC;
                break;

            case 'user_profile' :
                if ($arg == 'title')
                    return $USER_PROFILE_TITLE;
                if ($arg == 'keyword')
                    return $USER_PROFILE_KEYWORD;
                if ($arg == 'desc')
                    return $USER_PROFILE_DESC;
                break;

            case 'home4' :
                if ($arg == 'title')
                    return $DEFAULT_META_TITLE;
                if ($arg == 'keyword')
                    return $DEFAULT_META_KEYWORD;
                if ($arg == 'desc')
                    return $DEFAULT_META_DESCRIPTION;
                break;

            case 'sent_messages' :
                if ($arg == 'title')
                    return $SENT_MESSAGE_TITLE;
                if ($arg == 'keyword')
                    return $SENT_MESSAGE_KEYWORD;
                if ($arg == 'desc')
                    return $SENT_MESSAGE_DESC;
                break;

            case 'member_alert' :
                if ($arg == 'title')
                    return $MEMBER_ALERT_TITLE;
                if ($arg == 'keyword')
                    return $MEMBER_ALERT_KEYWORD;
                if ($arg == 'desc')
                    return $MEMBER_ALERT_DESC;
                break;

            case 'leaverating' :
                if ($arg == 'title')
                    return $LEAVE_RATING_TITLE;
                if ($arg == 'keyword')
                    return $LEAVE_RATING_KEYWORD;
                if ($arg == 'desc')
                    return $LEAVE_RATING_DESC;
                break;

            case 'edit_profile' :
                if ($arg == 'title')
                    return $EDIT_PROFILE_TITLE;
                if ($arg == 'keyword')
                    return $EDIT_PROFILE_KEYWORD;
                if ($arg == 'desc')
                    return $EDIT_PROFILE_DESC;
                break;

            case 'profile_photo' :
                if ($arg == 'title')
                    return $PROFILE_PHOTO_TITLE;
                if ($arg == 'keyword')
                    return $PROFILE_PHOTO_KEYWORD;
                if ($arg == 'desc')
                    return $PROFILE_PHOTO_DESC;
                break;

            case 'preferences':
                if ($arg == 'title')
                    return $PREFERANCES_TITLE;
                if ($arg == 'keyword')
                    return $PREFERANCES_KEYWORD;
                if ($arg == 'desc')
                    return $PREFERANCES_DESC;
                break;

            case 'verification':
                if ($arg == 'title')
                    return $VERIFICATION_TITLE;
                if ($arg == 'keyword')
                    return $VERIFICATION_KEYWORD;
                if ($arg == 'desc')
                    return $VERIFICATION_DESC;
                break;

            case 'notification':
                if ($arg == 'title')
                    return $NOTIFICATION_TITLE;
                if ($arg == 'keyword')
                    return $NOTIFICATION_KEYWORD;
                if ($arg == 'desc')
                    return $NOTIFICATION_DESC;
                break;

            case 'changepassword':
                if ($arg == 'title')
                    return $CHANGE_PASSWORD_TITLE;
                if ($arg == 'keyword')
                    return $CHANGE_PASSWORD_KEYWORD;
                if ($arg == 'desc')
                    return $CHANGE_PASSWORD_DESC;
                break;

            case 'delete_account':
                if ($arg == 'title')
                    return $DELETE_ACCOUNT_TITLE;
                if ($arg == 'keyword')
                    return $DELETE_ACCOUNT_KEYWORD;
                if ($arg == 'desc')
                    return $DELETE_ACCOUNT_DESC;
                break;

            case 'receiverating':
                if ($arg == 'title')
                    return $RECEIVE_RATING_TITLE;
                if ($arg == 'keyword')
                    return $RECEIVE_RATING_KEYWORD;
                if ($arg == 'desc')
                    return $RECEIVE_RATING_DESC;
                break;

            case 'ratinggiven' :
                if ($arg == 'title')
                    return $RATING_GIVEN_TITLE;
                if ($arg == 'keyword')
                    return $RATING_GIVEN_KEYWORD;
                if ($arg == 'desc')
                    return $RATING_GIVEN_DESC;
                break;

            default:
                if ($arg == 'title')
                    return $DEFAULT_META_TITLE;
                if ($arg == 'keyword')
                    return $DEFAULT_META_KEYWORD;
                if ($arg == 'desc')
                    return $DEFAULT_META_DESCRIPTION;
                break;
        }
    }

    /* Function to clean string when submit form */

    function clean($str) {
        global $inwebservice,$obj;
        if (!is_array($str)) { // should not be array only string will be clean
            $str = trim($str);
            if($inwebservice != "1"){
			//$str = mysqli_real_escape_string($str);
              $str = $obj->SqlEscapeString($str);
            }
            //$str = mysqli_real_escape_string($str);
        }
        //$str = htmlspecialchars($str);
        //$str = strip_tags($str);
        return($str);
    }

    function xss_cleaner_all() {
        foreach ($_GET as $keyy => $vall) {
            if (is_array($vall))
                foreach ($vall as $keyy1 => $vall1)
                    $_GET[$vall][$keyy1] = $this->clean($vall1);
            else
                $_GET[$keyy] = $this->clean($vall);
        }
        foreach ($_REQUEST as $keyy => $vall) {
            if (is_array($vall))
                foreach ($vall as $keyy1 => $vall1)
                    $_REQUEST[$vall][$keyy1] = $this->clean($vall1);
            else
                $_REQUEST[$keyy] = $this->clean($vall);
        }
        foreach ($_POST as $keyy => $vall) {
            if (is_array($vall))
                foreach ($vall as $keyy1 => $vall1)
                    $_POST[$vall][$keyy1] = $this->clean($vall1);
            else
                $_POST[$keyy] = $this->clean($vall);
        }
    }

    /* get system default language */

    function get_default_lang_name() {
        global $obj;

        $sql = "SELECT vTitle FROM language_master where eStatus='Active' AND eDefault = 'Yes'";
        $data = $obj->MySQLSelect($sql);

        $vTitle = isset($data[0]["vTitle"]) ? $data[0]["vTitle"] : 'EN';
        return $vTitle;
    }

    function get_default_lang() {
        global $obj;

        $sql = "SELECT vCode FROM language_master where eStatus='Active' AND eDefault = 'Yes'";
        $data = $obj->MySQLSelect($sql);

        $vCode = isset($data[0]["vCode"]) ? $data[0]["vCode"] : 'EN';
        return $vCode;
    }

    function getConfigurations($tabelName, $LABEL) {
        global $obj;

        $sql = "SELECT vValue FROM `" . $tabelName . "` WHERE vName='$LABEL'";
        $Data = $obj->MySQLSelect($sql);
        $Data_value = $Data[0]['vValue'];
        return $Data_value;
    }

    /* to set user role */

    function setRole($arr_role, $url) {
        $arr = array();
        $arr = explode(",", $arr_role);
        $this->role = $arr;
        $this->checkValid($url);
    }

    function checkValid($url) {
        $user = isset($_SESSION['sess_user']) ? $_SESSION['sess_user'] : '';
        $val = in_array($user, $this->role);
        $val = isset($val) ? $val : 0;
        //echo "url".$url;exit;
        //exit;
        if ($val == 0) {
            switch ($user) {
                case 'driver':
                    header('Location:profile.php');
                    break;
                case 'rider':
                    header('Location:profile_rider.php');
                    break;
                case 'company':
                    header('Location:profile.php');
                    break;

                    exit;
                //header('location:login.php');
            }
        } else {
            
        }
    }

    function image_path_set($path) {
        global $tconfig, $obj;

        if ($path == 'driver') {
            $return_path[0] = $tconfig["tpanel_path"] . "webimages/upload/Driver"; //Driver image
            $return_path[1] = $tconfig["tsite_upload_driver_doc_path"]; //Document image
        }
        if ($path == 'company') {
            $return_path[0] = $tconfig["tsite_upload_images_compnay_path"]; //Driver image
            $return_path[1] = $tconfig["tsite_upload_compnay_doc_path"]; //Document image
        }
        return $return_path;
        exit;
    }

    function save_log_data($iCompanyId, $iDriverId, $eUserType, $eType, $vLogName) {
        global $obj;
        $curr_date = Date('Y-m-d H:i:s');
        $sql = "INSERT INTO `log_file` (`vLogName`,`tDate`,`iCompanyId`,`iDriverId`,`eUserType`, `eType`) VALUES ('" . $vLogName . "','" . $curr_date . "', '" . $iCompanyId . "','" . $iDriverId . "', '" . $eUserType . "', '" . $eType . "')";
        $check_file = $obj->sql_query($sql);
    }

    function file_ext($file_name) {
        $filecheck = basename($file_name);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);

        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp") {
            $check_ext = 'is_file';
        } else {
            $check_ext = 'is_image';
        }
        return $check_ext;
    }

    function estatus_change($table_name, $id_field_name, $id, $set_value) {
        global $obj;

        $update_sql = "UPDATE " . $table_name . " set " . $set_value . " WHERE " . $id_field_name . "='" . $id . "'";
        $check_file = $obj->sql_query($update_sql);
    }

    function getStaticPage($id, $lang_code = "EN") {
        global $obj;

        $data['meta_title'] = "";
        $data['meta_keyword'] = "";
        $data['meta_desc'] = "";
        $data['page_title'] = "";
        $data['page_desc'] = "";
        $data['vImage'] = "";
        $data['vImage1'] = "";

        if ($id != '') {
            $q = "SELECT * FROM pages WHERE iPageId = " . $id;
            $data = $obj->MySQLSelect($q);
            //echo"<pre>";print_r($data);exit;
            if (count($data) > 0) {
                $data['meta_title'] = $data[0]["vTitle"];
                $data['vImage'] = $data[0]["vImage"];
                $data['meta_keyword'] = $data[0]["tMetaKeyword"];
                $data['meta_desc'] = $data[0]["tMetaDescription"];
                $data['page_title'] = $data[0]["vPageTitle_" . $lang_code];
                $data['page_desc'] = $data[0]["tPageDesc_" . $lang_code];
                $data['vImage1'] = $data[0]["vImage1"];
            }
        }
        return $data;
    }


    function gethomeData($vCode) {
        global $obj;
        if ($vCode != '') {
            $q = "SELECT * FROM home_content WHERE vCode = '".$vCode."'";
            $data = $obj->MySQLSelect($q);
            //echo"<pre>";print_r($data);exit;
            if (count($data) > 0) {
                $data['meta_title'] = $data[0]["third_sec_title"];
                $data['meta_keyword'] = '';
                $data['meta_desc'] = $data[0]["third_sec_desc"];
            }
        }
        return $data;
    }

    //Return trip fare with symbol
    function trip_currency($price = '', $ratio = '', $defCurrency = "", $parameter = 2) {
        global $obj;
        if ($defCurrency == '') {
            $ssql = " eDefault='Yes'";
        } else {
            $ssql = " vName='" . $defCurrency . "'";
        }
        $sql = "select vSymbol from currency where" . $ssql;
        $db_curr_mst = $obj->MySQLSelect($sql);
        if (count($db_curr_mst) > 0) {
            if ($ratio == '' || $ratio == 0) {
                // return $db_curr_mst[0]['vSymbol'] . ' ' . number_format($price, $parameter, '.', '');
                return $db_curr_mst[0]['vSymbol'] . ' ' . number_format($price, $parameter, '.', ',');
            } else {
                //return $db_curr_mst[0]['vSymbol'] . ' ' . number_format(($price * $ratio), $parameter, '.', '');
                return $db_curr_mst[0]['vSymbol'] . ' ' . number_format(($price * $ratio), $parameter, '.', ',');
            }
        }
    }

    /* used in my earning page */

    function trip_currency_payment($price = '', $ratio = '', $defCurrency = "", $parameter = 2) {
        global $obj;
        #echo $ratio;exit;
        if ($defCurrency == '') {
            $ssql = " eDefault='Yes'";
        } else {
            $ssql = " vName='" . $defCurrency . "'";
        }
        $sql = "select vSymbol from currency where" . $ssql;
        $db_curr_mst = $obj->MySQLSelect($sql);
        if (count($db_curr_mst) > 0) {
            if ($ratio == '' || $ratio == 0) {
                return number_format($price, $parameter, '.', '');
            } else {
                return number_format(($price * $ratio), $parameter, '.', '');
            }
        }
    }

    //Return trip fare without symbol
    function trip_price($price = '', $ratio = '', $parameter = 2) {
        if ($ratio == '') {
            return number_format($price, $parameter, '.', '');
        } else {
            return number_format(($price * $ratio), $parameter, '.', '');
        }
    }

    // Calculate Trip Fare
    function getFinalFare($iBaseFare, $priceParMin, $tripTimeInMinutes, $priceParKM, $distance, $siteCommision, $priceRatio, $vCurrencyCode, $startDate, $endDate) {

        if ($startDate != '' && $endDate != '') {
            $tripTimeInMinutes = @round(abs(strtotime($startDate) - strtotime($endDate)) / 60, 2);
        }

        $Minute_Fare = round($priceParMin * $tripTimeInMinutes, 2) * $priceRatio;
        $Distance_Fare = round($priceParKM * $distance, 2) * $priceRatio;
        $iBaseFare = round($iBaseFare, 2) * $priceRatio;

        $total_fare = $iBaseFare + $Minute_Fare + $Distance_Fare;

        $Commision_Fare = round((($total_fare * $siteCommision) / 100), 2) * $priceRatio;

        $total_fare = $total_fare + $Commision_Fare;

        $result['FareOfMinutes'] = $Minute_Fare;
        $result['FareOfDistance'] = $Distance_Fare;
        $result['FareOfCommision'] = $Commision_Fare;
        $result['iBaseFare'] = $iBaseFare;
        $result['fPricePerMin'] = $priceParMin * $priceRatio;
        $result['fPricePerKM'] = $priceParKM * $priceRatio;
        $result['fCommision'] = $siteCommision * $priceRatio;
        $result['FinalFare'] = $total_fare;

        return $result;
    }

    function clearPhone($phone) {
        $phone = preg_replace("/[^\d]/", "", $phone);
        if ($phone[0] == '0') {
            $phone = ltrim($phone, '0');
        }
        return $phone;
    }

    function clearEmail($email) {
        if (SITE_TYPE == "Demo") {
            $mail = explode('.', $email);
            $output = substr($mail[0], 0, 2);
            return $output . '*****.' . $mail[count($mail) - 1];
        } else {
            return $email;
        }
    }

    function clearMobile($text) {
        if (SITE_TYPE == "Demo") {
            return substr_replace($text, "*****", 0, -2);
        } else {
            return $text;
        }
    }

    function clearName($text) {
        if (SITE_TYPE == "Demo") {
            $mail = explode(' ', $text);
            $output = substr($mail[1], 0, 1);
            return $mail[0] . ' ' . $output . '***';
        } else {
            return $text;
        }
    }

    function sendCode($mobileNo, $code, $fpass = 'code', $pass = '') {
        global $site_path, $langage_lbl;
        $mobileNo = $this->clearPhone($mobileNo);
        $mobileNo = $code . $mobileNo;

        require_once(TPATH_CLASS . 'twilio/Services/Twilio.php');

        $account_sid = $this->getConfigurations("configurations", "MOBILE_VERIFY_SID_TWILIO");
        $auth_token = $this->getConfigurations("configurations", "MOBILE_VERIFY_TOKEN_TWILIO");
        $twilioMobileNum = $this->getConfigurations("configurations", "MOBILE_NO_TWILIO");

        $client = new Services_Twilio($account_sid, $auth_token);

        $toMobileNum = "+" . $mobileNo;
        if ($fpass == "forgot") {
            $text_prefix_reset_pass = $this->getConfigurations("configurations", "PREFIX_PASS_RESET_SMS");
            // $verificationCode='Your Password is '.$this->decrypt($pass);
            $code = $this->decrypt($pass);
            $verificationCode = $text_prefix_reset_pass . ' ' . $code;
        } else {
            //$text_prefix_verification_code = $this->getConfigurations("configurations","PREFIX_VERIFICATION_CODE_SMS");

            $str = "select * from send_message_templates where vEmail_Code='VERIFICATION_CODE_MESSAGE'";
            $res = $obj->MySQLSelect($str);
            $text_prefix_verification_code = $res[0]['vBody_EN'];

            //$text_prefix_verification_code = $langage_lbl['LBL_VERIFICATION_CODE_TXT'];
            $code = mt_rand(1000, 9999);
            $verificationCode = $text_prefix_verification_code . ' ' . $code;
        }
        // echo $client;exit;
        try {
            $sms = $client->account->messages->sendMessage($twilioMobileNum, $toMobileNum, $verificationCode);
            $returnArr['action'] = "1";
        } catch (Services_Twilio_RestException $e) {
            $returnArr['action'] = "0";
        }
        $returnArr['verificationCode'] = $code;
        return $returnArr;
    }

    // Seo setting function		
    function getsettingSeo($id) {
        global $obj;
        if ($id != '') {
            $q = "SELECT * FROM seo_sections WHERE iId = " . $id;
            $data = $obj->MySQLSelect($q);
            //echo"<pre>";print_r($data);exit;
            if (count($data) > 0) {
                $data['meta_title'] = $data[0]["vPagetitle"];
                $data['meta_keyword'] = $data[0]["vMetakeyword"];
                $data['meta_desc'] = $data[0]["tDescription"];
            }
        }
        return $data;
    }


    function general_upload_image_vehicle_android($temp_name, $image_name, $path, $size1, $size2 = "", $size3 = "", $size4 = "", $option = "", $modulename = "", $original = "", $size5 = "", $temp_gallery, $vehicle_type, $hover) {

        include_once(TPATH_CLASS . 'Imagecrop.class.php');
        $thumb = new thumbnail;
        $time_val = time();
        $vImage1 = $temp_name;

        $vImage_name1 = str_replace(" ", "_", trim($image_name));
        $img_arr = explode(".", $vImage_name1);
        if ($modulename == '') {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }
        $filename = mt_rand(11111, 99999);
        $fileextension = $img_arr[count($img_arr) - 1];

        if ($vImage1 != "") {
            //$temp_gallery . "/" . $vImage_name1;
            copy($vImage1, $temp_gallery . "/" . $vImage_name1);
            if ($option == 'menu' && $option != "") {
                list($width, $height) = getimagesize($temp_gallery . "/" . $vImage_name1);
                $size3 = $width;
            }
            if ($original == "Y" || $original == "y") {
                copy($temp_gallery . "/" . $vImage_name1, $path . "ic_car_" . $vehicle_type . "." . $fileextension);
            }


            //$temp_gallery."/".$vImage_name1;
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1); // generate image_file, set filename to resize/resample
            $thumb->size_auto($size1);    // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path . "mdpi" . "_" . $hover . "ic_car_" . $vehicle_type . "." . $fileextension, $path . "ic_car_" . $vehicle_type . "." . $fileextension, $size1, "360");
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size2);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save_pngs($path . "hdpi" . "_" . $hover . "ic_car_" . $vehicle_type . "." . $fileextension, $path . "ic_car_" . $vehicle_type . "." . $fileextension, $size2, "360");
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size3);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);  // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save_pngs($path . "xhdpi" . "_" . $hover . "ic_car_" . $vehicle_type . "." . $fileextension, $path . "ic_car_" . $vehicle_type . "." . $fileextension, $size3, "360");
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size5);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save_pngs($path . "xxxhdpi" . "_" . $hover . "ic_car_" . $vehicle_type . "." . $fileextension, $path . "ic_car_" . $vehicle_type . "." . $fileextension, $size5, "360");
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size4);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save_pngs($path . "xxhdpi" . "_" . $hover . "ic_car_" . $vehicle_type . "." . $fileextension, $path . "ic_car_" . $vehicle_type . "." . $fileextension, $size4, "360");
            $vImage1 = $time_val . "_" . $filename . "." . $fileextension;
            @unlink($temp_gallery . "/" . $vImage_name1);
            @unlink($path . $old_image1);
            @unlink($path . "1_" . $old_image1);
            @unlink($path . "2_" . $old_image1);
            @unlink($path . "3_" . $old_image1);
            @unlink($path . "4_" . $old_image1);
            @unlink($path . "5_" . $old_image1);
            return $vImage1;
        } else {
            return $old_image1;
        }
    }

    function general_upload_image_vehicle_ios($temp_name, $image_name, $path, $size1, $size2 = "", $size3 = "", $size4 = "", $option = "", $modulename = "", $original = "", $size5 = "", $temp_gallery, $vehicle_type, $hover) {

        include_once(TPATH_CLASS . 'Imagecrop.class.php');
        $thumb = new thumbnail;
        //global $thumb;
        $time_val = time();
        $vImage1 = $temp_name;

        $vImage_name1 = str_replace(" ", "_", trim($image_name));
        $img_arr = explode(".", $vImage_name1);
        if ($modulename == '') {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }
        $filename = mt_rand(11111, 99999);
        $fileextension = $img_arr[count($img_arr) - 1];

        if ($vImage1 != "") {
            //$temp_gallery . "/" . $vImage_name1;
            copy($vImage1, $temp_gallery . "/" . $vImage_name1);
            if ($option == 'menu' && $option != "") {
                list($width, $height) = getimagesize($temp_gallery . "/" . $vImage_name1);
                $size3 = $width;
            }
            if ($original == "Y" || $original == "y") {
                copy($temp_gallery . "/" . $vImage_name1, $path . "ic_car_" . $vehicle_type . "." . $fileextension);
            }


            //$temp_gallery."/".$vImage_name1;
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1); // generate image_file, set filename to resize/resample
            $thumb->size_auto($size1);    // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($path . "mdpi" . "_" . $hover . "ic_car_" . $vehicle_type . "." . $fileextension, $path . "ic_car_" . $vehicle_type . "." . $fileextension, $size1, "360");
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size2);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save_pngs($path . "hdpi" . "_" . $hover . "ic_car_" . $vehicle_type . "." . $fileextension, $path . "ic_car_" . $vehicle_type . "." . $fileextension, $size2, "360");
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size3);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save_pngs($path . "1x" . "_" . $hover . "ic_car_" . $vehicle_type . "." . $fileextension, $path . "ic_car_" . $vehicle_type . "." . $fileextension, $size3, "360");
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size5);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save_pngs($path . "3x" . "_" . $hover . "ic_car_" . $vehicle_type . "." . $fileextension, $path . "ic_car_" . $vehicle_type . "." . $fileextension, $size5, "360");
            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);   // generate image_file, set filename to resize/resample
            $thumb->size_auto($size4);       // set the biggest width or height for thumbnail
            $thumb->jpeg_quality(100);      // [OPTIONAL] set quality for jpeg only (0 - 100) (worst - best), default = 75
            $thumb->save_pngs($path . "2x" . "_" . $hover . "ic_car_" . $vehicle_type . "." . $fileextension, $path . "ic_car_" . $vehicle_type . "." . $fileextension, $size4, "360");
            $vImage1 = $time_val . "_" . $filename . "." . $fileextension;
                        
            @unlink($temp_gallery . "/" . $vImage_name1);
            @unlink($path . $old_image1);
            @unlink($path . "1_" . $old_image1);
            @unlink($path . "2_" . $old_image1);
            @unlink($path . "3_" . $old_image1);
            @unlink($path . "4_" . $old_image1);
            @unlink($path . "5_" . $old_image1);
            return $vImage1;
        } else {
            return $old_image1;
        }
    }

    function general_upload_image_vehicle_type($vehicleid, $image_name, $temp_name, $old_image_name) {

        include_once(TPATH_CLASS . 'Imagecrop.class.php');
        $thumb = new thumbnail;

        global $currrent_upload_time,$tconfig;

        if($currrent_upload_time != '') {
            $time_val = $currrent_upload_time;
        } else {
            $time_val = time();
        }

        $vImage1 = $temp_name;

        $img_path = $tconfig["tsite_upload_images_vehicle_type_path"];
        $temp_gallery = $img_path . '/'. $vehicleid;

        if ($vehicleid != "") {

            $check_file['vLogo'] = $img_path . '/' . $vehicleid . '/android/' . $old_image_name;
            $android_path = $img_path . '/' . $vehicleid . '/android';
            $ios_path = $img_path . '/' . $vehicleid . '/ios';

            if ($check_file['vLogo'] != '' && file_exists($check_file['vLogo'])) {
                @unlink($android_path . '/' . $old_image_name);
                @unlink($android_path . '/mdpi_' . $old_image_name);
                @unlink($android_path . '/hdpi_' . $old_image_name);
                @unlink($android_path . '/xhdpi_' . $old_image_name);
                @unlink($android_path . '/xxhdpi_' . $old_image_name);
                @unlink($android_path . '/xxxhdpi_' . $old_image_name);
                @unlink($ios_path . '/' . $old_image_name);
                @unlink($ios_path . '/1x_' . $old_image_name);
                @unlink($ios_path . '/2x_' . $old_image_name);
                @unlink($ios_path . '/3x_' . $old_image_name);
            }
        }

        $Photo_Gallery_folder = $img_path . '/' . $vehicleid . '/';
        $Photo_Gallery_folder_android = $Photo_Gallery_folder . 'android/';
        $Photo_Gallery_folder_ios = $Photo_Gallery_folder . 'ios/';
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            mkdir($Photo_Gallery_folder_android, 0777);
            mkdir($Photo_Gallery_folder_ios, 0777);
        }

      $vImage_name1 = str_replace(" ", "_", trim($image_name));

        $img_arr = explode(".", $vImage_name1);
        if ($modulename == '') {
            $filename = $img_arr[0];
        } else {
            $filename = $modulename;
        }

        $filename = mt_rand(11111, 99999);
        $fileextension = $img_arr[count($img_arr) - 1];

        if ($vImage1 != "") {
            copy($vImage1, $temp_gallery . "/" . $vImage_name1);
            copy($temp_gallery . "/" . $vImage_name1, $Photo_Gallery_folder_android .$time_val . "." . $fileextension);
            copy($temp_gallery . "/" . $vImage_name1, $Photo_Gallery_folder_ios . $time_val . "." . $fileextension);


            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);
            $thumb->size_auto($tconfig["tsite_upload_images_vehicle_type_size1_android"]);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_android . "mdpi" . "_" . $time_val . "." . $fileextension, $Photo_Gallery_folder_android . $time_val . "." . $fileextension, $tconfig["tsite_upload_images_vehicle_type_size1_android"], "360");

            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);
            $thumb->size_auto($tconfig["tsite_upload_images_vehicle_type_size2_android"]);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_android . "hdpi" . "_" . $time_val . "." . $fileextension, $Photo_Gallery_folder_android  . $time_val . "." . $fileextension, $tconfig["tsite_upload_images_vehicle_type_size2_android"], "360");

            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);
            $thumb->size_auto($tconfig["tsite_upload_images_vehicle_type_size3_both"]);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_android . "xhdpi" . "_" . $time_val . "." . $fileextension, $Photo_Gallery_folder_android  . $time_val . "." . $fileextension, $tconfig["tsite_upload_images_vehicle_type_size3_both"], "360");

            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);
            $thumb->size_auto($tconfig["tsite_upload_images_vehicle_type_size4_android"]);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_android . "xxhdpi" . "_" . $time_val . "." . $fileextension, $Photo_Gallery_folder_android  . $time_val . "." . $fileextension, $tconfig["tsite_upload_images_vehicle_type_size4_android"], "360");

            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);
            $thumb->size_auto($tconfig["tsite_upload_images_vehicle_type_size5_both"]);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_android . "xxxhdpi" . "_" . $time_val . "." . $fileextension, $Photo_Gallery_folder_android  . $time_val . "." . $fileextension, $tconfig["tsite_upload_images_vehicle_type_size5_both"], "360");

            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);
            $thumb->size_auto($tconfig["tsite_upload_images_vehicle_type_size3_both"]);
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_ios . "1x" . "_" . $time_val . "." . $fileextension, $Photo_Gallery_folder_ios  . $time_val . "." . $fileextension, $tconfig["tsite_upload_images_vehicle_type_size3_both"], "360");

            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);
            $thumb->size_auto($tconfig["tsite_upload_images_vehicle_type_size5_both"]);   
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_ios . "2x" . "_" . $time_val . "." . $fileextension, $Photo_Gallery_folder_ios  . $time_val . "." . $fileextension, $tconfig["tsite_upload_images_vehicle_type_size5_both"], "360");

            $thumb->createthumbnail($temp_gallery . "/" . $vImage_name1);
            $thumb->size_auto($tconfig["tsite_upload_images_vehicle_type_size5_ios"]);   
            $thumb->jpeg_quality(100);
            $thumb->save_pngs($Photo_Gallery_folder_ios . "3x" . "_" . $time_val . "." . $fileextension, $Photo_Gallery_folder_ios  . $time_val . "." . $fileextension, $tconfig["tsite_upload_images_vehicle_type_size5_ios"], "360");

            @unlink($temp_gallery . "/" . $vImage_name1);
            $vImage1 = $time_val . "_" . $filename . "." . $fileextension;
            return $vImage1;
        } else {
            return $old_image1;
        }
    }

    
    // get reffercode
    function validationrefercode($id) {
        global $obj;
        $str = "";
        $sql = "SELECT iUserId,vRefCode FROM register_user WHERE vRefCode = '" . $id . "' ";

        $db_user = $obj->MySQLSelect($sql);
        if (count($db_user) > 0) {
            $eRefType = 'Rider';
            $str .= $db_user[0]['iUserId'] . "|" . $eRefType;
        } else {

            $sql = "SELECT iDriverId,vRefCode FROM register_driver WHERE vRefCode = '" . $id . "' ";
            $db_driver = $obj->MySQLSelect($sql);
            if (count($db_driver) > 0) {
                $eRefType = 'Driver';
                $str .= $db_driver[0]['iDriverId'] . "|" . $eRefType;
            } else {

                $str .= 0;
            }
        }
        return $str;
    }

    // ganerate reffer code
    function ganaraterefercode($ereftype) {

        global $obj;
        $str = "";
        //$milliseconds = round(microtime(true) * 1000);
        $milliseconds = time();
        if ($ereftype == "Rider") {

            $newstring = substr($milliseconds, -4);
            $str .= 'pr' . $newstring;
        } else if ($ereftype == "Driver") {


            $newstring = substr($milliseconds, -4);
            $str .= 'dr' . $newstring;
        }
        return $str;
    }

    // insert of user_wallet table
    function InsertIntoUserWallet($iUserId, $eUserType, $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate) {

        global $obj;

        $sql = "INSERT INTO `user_wallet` (`iUserId`,`eUserType`,`iBalance`,`eType`,`iTripId`, `eFor`, `tDescription`, `ePaymentStatus`, `dDate`) VALUES ('" . $iUserId . "','" . $eUserType . "', '" . $iBalance . "','" . $eType . "', '" . $iTripId . "', '" . $eFor . "', '" . $tDescription . "', '" . $ePaymentStatus . "', '" . $dDate . "')";

        $result = $obj->MySQLInsert($sql);

        $sql = "SELECT * FROM currency WHERE eStatus = 'Active'";
        $db_curr = $obj->MySQLSelect($sql);
        $where = " iUserWalletId = '" . $result . "'";
        for ($i = 0; $i < count($db_curr); $i++) {
            $data_currency_ratio['fRatio_' . $db_curr[$i]['vName']] = $db_curr[$i]['Ratio'];
            $obj->MySQLQueryPerform("user_wallet", $data_currency_ratio, 'update', $where);
        }
    }

    function getTotalbalance($id, $eUserType) {

        global $obj;
        $sql = "SELECT sum(iBalance) as totalbalance from  `user_wallet` WHERE  iUserId = '" . $id . "' AND eUserType = '" . $eUserType . "' AND eFor = 'Referrer'";
        $db_sql_bal = $obj->MySQLSelect($sql);
        //print_r($db_sql_bal);
        return $totalbalance = $db_sql_bal[0]['totalbalance'];
    }

    function getTotalReferrer($id, $eUserType) {

        global $obj;
        $sql = "SELECT count(iUserWalletId) as totalreferrer from  `user_wallet` WHERE  iUserId = '" . $id . "' AND eUserType = '" . $eUserType . "' AND eFor = 'Referrer'";
        $db_sql_bal = $obj->MySQLSelect($sql);
        //print_r($db_sql_bal);
        return $totalreferrer = $db_sql_bal[0]['totalreferrer'];
    }

    function get_user_available_balance($sess_iMemberId, $type) {
        global $obj;

		// echo "sss";exit;
        $balance = 0;
        $sql = "SELECT SUM(iBalance) as totcredit FROM user_wallet WHERE iUserId = '" . $sess_iMemberId . "' AND eUserType = '" . $type . "' AND eType = 'Credit'";
        $db_credit_balance = $obj->MySQLSelect($sql);

        $sql = "SELECT SUM(iBalance) as totdebit FROM user_wallet WHERE iUserId = '" . $sess_iMemberId . "' AND eUserType = '" . $type . "' AND eType = 'Debit'";
        $db_debit_balance = $obj->MySQLSelect($sql);

        $balance = $db_credit_balance[0]['totcredit'] - $db_debit_balance[0]['totdebit'];


        return $balance;
    }

    /* function get_user_available_balance_admin($sess_iMemberId,$type,$startdate,$enddate) {

      global $obj;

      $sql = "SELECT SUM(iBalance) as totcredit FROM user_wallet WHERE iUserId = '".$sess_iMemberId."' AND eUserType = '".$type."' AND eType = 'Credit' AND Date(dDate)>='".$startdate."' AND Date(dDate) <='".$enddate."'";
      $db_credit_balance = $obj->MySQLSelect($sql);

      //echo "<br>";
      $sql = "SELECT SUM(iBalance) as totdebit FROM user_wallet WHERE iUserId = '".$sess_iMemberId."' AND eUserType = '".$type."' AND eType = 'Debit' AND Date(dDate)>='".$startdate."' AND Date(dDate) <='".$enddate."'";
      $db_debit_balance = $obj->MySQLSelect($sql);

      $balance = $db_credit_balance[0]['totcredit']-$db_debit_balance[0]['totdebit'];


      return $balance;

      } */

    function get_user_available_balance_admin($iDriverId, $iUserId, $eUserType, $startdate, $enddate, $eFor, $Payment_type) {

        global $obj;
        $balance = 0;
        $ssql = '';
        if ($eUserType == "Driver") {
            $sess_iMemberId = $iDriverId;
        } else {
            $sess_iMemberId = $iUserId;
        }
        if ($startdate != '') {

            $ssql .= " AND Date(dDate) >='" . $startdate . "'";
        }
        if ($enddate != '') {

            $ssql .= " AND Date(dDate) <='" . $enddate . "'";
        }
        if ($eUserType != '') {
            $ssql .= " AND eUserType = '" . $eUserType . "'";
        }
        if ($eFor != '') {
            $ssql .= " AND eFor = '" . $eFor . "'";
        }

        if ($Payment_type == "Credit") {

            $sql = "SELECT SUM(iBalance) as totcredit FROM user_wallet WHERE iUserId = '" . $sess_iMemberId . "'AND eType = 'Credit'" . $ssql . " ";
            $db_credit_balance = $obj->MySQLSelect($sql);
            $balance = $db_credit_balance[0]['totcredit'];
        } else if ($Payment_type == "Debit") {

            $sql = "SELECT SUM(iBalance) as totdebit FROM user_wallet WHERE iUserId = '" . $sess_iMemberId . "' AND eType = 'Debit'" . $ssql . "";
            $db_debit_balance = $obj->MySQLSelect($sql);
            $balance = $db_debit_balance[0]['totdebit'];
        } else {

            $sql = "SELECT SUM(iBalance) as totcredit FROM user_wallet WHERE iUserId = '" . $sess_iMemberId . "' AND eUserType = '" . $eUserType . "' AND eType = 'Credit'";
            $db_credit_balance = $obj->MySQLSelect($sql);

            $sql = "SELECT SUM(iBalance) as totdebit FROM user_wallet WHERE iUserId = '" . $sess_iMemberId . "' AND eUserType = '" . $eUserType . "' AND eType = 'Debit'";
            $db_debit_balance = $obj->MySQLSelect($sql);
            $balance = $db_credit_balance[0]['totcredit'] - $db_debit_balance[0]['totdebit'];
        }

        return $balance;
    }

    /* function get_benefit_amount($iTripId) {


      global $obj;
      global $FIRST_YEAR_REFERRAL_AMOUNT;
      global $SECOND_YEAR_REFERRAL_AMOUNT;
      global $THIRD_YEAR_REFERRAL_AMOUNT;
      global $FOURTH_YEAR_REFERRAL_AMOUNT;

      $sql = "SELECT * from trips where iTripId=" . $iTripId;
      $db_result = $obj->MySQLSelect($sql);
      $count_rider = count($db_result);



      if ($count_rider > 0) {

      //	Referral for passanger code start

      $sql = "SELECT * from register_user where iUserId=" . $db_result[0]['iUserId'];
      $db_rider_user = $obj->MySQLSelect($sql);
      //echo "<br>"; print_r($db_rider_user );

      $count_rider_user = count($db_rider_user);


      if ($count_rider_user > 0) {


      if ($db_rider_user[0]['eRefType'] == "Rider") {

      $sql = "SELECT * from register_user where iUserId=" . $db_rider_user[0]['iRefUserId'];
      $db_rider_user_detail = $obj->MySQLSelect($sql);
      $count_rider_user_detail = count($db_rider_user_detail);

      if ($count_rider_user_detail > 0) {

      $discount = $this->daysDifference(date("Y-m-d H:i:s"), $db_rider_user_detail[0]['tRegistrationDate']);

      if ($discount != 0) {

      $total_amount = (float) (($db_result[0]['iFare'] * $discount) / 100);

      // user_wallet table insert data

      $eFor = "Referrer";
      //$tDescription = "Referal amount credit $ ".$total_amount." into your account";
      $tDescription = "Referral amount credit " . $total_amount . " into your account for trip number #" . $db_result[0]['vRideNo'];

      $dDate = Date('Y-m-d H:i:s');
      $ePaymentStatus = "Unsettelled";

      $insert_user_wallet = $this->InsertIntoUserWallet($db_rider_user_detail[0]['iUserId'], "Rider", $total_amount, 'Credit', $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
      }
      }
      } else if ($db_rider_user[0]['eRefType'] == "Driver") {

      $sql = "SELECT * from register_driver where iDriverId=" . $db_rider_user[0]['iRefUserId'];
      $db_driver_user_detail = $obj->MySQLSelect($sql);

      $count_driver = count($db_driver_user_detail);

      if ($count_driver > 0) {

      $discount = $this->daysDifference(date("Y-m-d H:i:s"), $db_driver_user_detail[0]['tRegistrationDate']);

      if ($discount != 0) {

      $total_amount = (float) (($db_result[0]['iFare'] * $discount) / 100);


      // user_wallet table insert data

      $eFor = "Referrer";
      //$tDescription = "Referal amount credit $ ".$total_amount." into your account";
      $tDescription = "Referral amount credit " . $total_amount . " into your account for trip number #" . $db_result[0]['vRideNo'];
      $dDate = Date('Y-m-d H:i:s');
      $ePaymentStatus = "Unsettelled";
      //$total_amount;

      $insert_user_wallet = $this->InsertIntoUserWallet($db_driver_user_detail[0]['iDriverId'], "Driver", $total_amount, 'Credit', $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
      //return $insert_user_wallet;
      }
      }
      }
      }
      //	Referral for passanger code end
      //	Referral for driver code start

      $sql = "SELECT * from register_driver where iDriverId=" . $db_result[0]['iDriverId'];
      $db_driver_user_data = $obj->MySQLSelect($sql);
      $count_driver_user = count($db_driver_user_data);

      if ($count_driver_user > 0) {

      if ($db_driver_user_data[0]['eRefType'] == "Rider") {

      $sql = "SELECT * from register_user where iUserId=" . $db_driver_user_data[0]['iRefUserId'];
      $db_rider_user_deta = $obj->MySQLSelect($sql);

      $count_rider_user_deta = count($db_rider_user_deta);
      if ($count_rider_user_deta > 0) {

      $discount = $this->daysDifference(date("Y-m-d H:i:s"), $db_rider_user_deta[0]['tRegistrationDate']);

      if ($discount != 0) {

      $total_amount = (float) (($db_result[0]['iFare'] * $discount) / 100);

      // user_wallet table insert data

      $eFor = "Referrer";
      //$tDescription = "Referal amount credit $ ".$total_amount." into your account";
      $tDescription = "Referral amount credit " . $total_amount . " into your account for trip number #" . $db_result[0]['vRideNo'];
      $dDate = Date('Y-m-d H:i:s');
      $ePaymentStatus = "Unsettelled";

      $insert_user_wallet = $this->InsertIntoUserWallet($db_rider_user_deta[0]['iUserId'], "Rider", $total_amount, 'Credit', $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
      //return $insert_user_wallet;
      }
      }
      } else if ($db_driver_user_data[0]['eRefType'] == "Driver") {
      $sql = "SELECT * from register_driver where iDriverId=" . $db_driver_user_data[0]['iRefUserId'];
      $driver_user_detail = $obj->MySQLSelect($sql);

      $count_driver_data = count($driver_user_detail);

      if ($count_driver_data > 0) {

      $discount = $this->daysDifference(date("Y-m-d H:i:s"), $driver_user_detail[0]['tRegistrationDate']);


      if ($discount != 0) {

      $total_amount = (float) (($db_result[0]['iFare'] * $discount) / 100);
      // user_wallet table insert data

      $eFor = "Referrer";
      //$tDescription = "Referal amount credit $ ".$total_amount." into your account";
      $tDescription = "Referral amount credit " . $total_amount . " into your account for trip number #" . $db_result[0]['vRideNo'];
      $dDate = Date('Y-m-d H:i:s');
      $ePaymentStatus = "Unsettelled";
      //$total_amount;

      $insert_user_wallet = $this->InsertIntoUserWallet($driver_user_detail[0]['iDriverId'], "Driver", $total_amount, 'Credit', $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);

      //return $insert_user_wallet;
      }
      }
      }
      }
      //	Referral for driver code start
      }

      return $insert_user_wallet;
      }

      /* for display balance with currency ratio of user wallet */

    function get_benefit_amount($iTripId) {
        global $obj, $generalobj;
       
        $REFERRAL_AMOUNT = $generalobj->getConfigurations("configurations", "REFERRAL_AMOUNT");
        $sql = "SELECT iUserId,iDriverId  from trips where iTripId=" . $iTripId;
        $db_result = $obj->MySQLSelect($sql);
        $count_rider = count($db_result);

        if ($count_rider > 0) {

            //	Referral for passanger code start

            $sql = "SELECT iUserId,iRefUserId ,eRefType from register_user where iUserId=" . $db_result[0]['iUserId'];
            $db_rider_user = $obj->MySQLSelect($sql);
            $count_rider_user = count($db_rider_user);

            ### Code For Referral Amount Credit into Rider's Refferer ###
            if ($count_rider_user > 0) {

                if ($db_rider_user[0]['iRefUserId'] != 0) {

                    $sql1 = "SELECT iUserId,vRideNo from trips where iUserId='" . $db_rider_user[0]['iUserId'] ."' AND iActive = 'Finished'";
                    $db_trips_user = $obj->MySQLSelect($sql1);

                    $count_rider = count($db_trips_user);
                    if ($count_rider == 1) {

                        $eFor = "Referrer";
                        $tDescription = "#LBL_REFERRAL_AMOUNT_CREDIT#";
                        $dDate = Date('Y-m-d H:i:s');
                        $ePaymentStatus = "Unsettelled";
                        $generalobj->InsertIntoUserWallet($db_rider_user[0]['iRefUserId'], $db_rider_user[0]['eRefType'], $REFERRAL_AMOUNT, 'Credit', $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
                    }
                }
            }
            ### Code For Referral Amount Credit into Rider's Refferer ###
            //	Referral for passanger code ends
            //	Referral for driver code start

            $sql = "SELECT * from register_driver where iDriverId=" . $db_result[0]['iDriverId'];
            $db_driver_user = $obj->MySQLSelect($sql);
            $count_driver_user = count($db_driver_user);

            ### Code For Referral Amount Credit into Driver's Refferer ###
            if ($count_driver_user > 0) {

                if ($db_driver_user[0]['iRefUserId'] != 0) {

                    $sql1 = "SELECT iDriverId,vRideNo from trips where iDriverId='" . $db_driver_user[0]['iDriverId']."' AND iActive = 'Finished'";
                    $db_trips_driver = $obj->MySQLSelect($sql1);

                    $count_driver = count($db_trips_driver);
                    if ($count_driver == 1) {

                        $eFor_Driver = "Referrer";
                        $tDescription_Driver = "#LBL_REFERRAL_AMOUNT_CREDIT#";
                        $dDate_Driver = Date('Y-m-d H:i:s');
                        $ePaymentStatus_Driver = "Unsettelled";
                        $generalobj->InsertIntoUserWallet($db_driver_user[0]['iRefUserId'], $db_driver_user[0]['eRefType'], $REFERRAL_AMOUNT, 'Credit', $iTripId, $eFor_Driver, $tDescription_Driver, $ePaymentStatus_Driver, $dDate_Driver);
                    }
                }
            }
            ### Code For Referral Amount Credit into Driver's Refferer ###
            //	Referral for driver code ends
        }

        return $count_rider;
    }

    function userwalletcurrency($currencyratio, $amount, $currencysymbol) {
        global $obj;
        $parameter = 2;

        $sql = "select vSymbol, vName, Ratio from currency where vName = '" . $currencysymbol . "'";
        $db_currency = $obj->MySQLSelect($sql);

        if ($currencyratio == '' || $currencyratio == 0) {
            $amt = $amount * $db_currency[0]['Ratio'];
        } else {
            $amt = $amount * $currencyratio;
        }

        if ($amt == 0) {
            $finalamt = $db_currency[0]['vSymbol'] . " 0.00";
            return $finalamt;
        } else {
            $finalamt = $db_currency[0]['vSymbol'] . ' ' . number_format($amt, $parameter, '.', '');
            //$finalamt = $db_currency[0]['vSymbol'].' '.number_format(round($amt), $parameter, '.', '');
            return $finalamt;
        }
    }

    /* for display balance with currency ratio of user wallet end */

    /* get amount with currency symbol */

    function get_currency_with_symbol($amt, $currency) {
        global $obj;
        $parameter = 2;

        $sql = "select vSymbol from currency where vName = '" . $currency . "'";
        $db_currency = $obj->MySQLSelect($sql);

        if ($amt == 0) {
            $finalamt = $db_currency[0]['vSymbol'] . " 0.00";
            return $finalamt;
        } else {
            $finalamt = $db_currency[0]['vSymbol'] . ' ' . number_format($amt, $parameter, '.', '');
            return $finalamt;
        }
    }

    /* get amount with currency symbol end */
    /* get default currency */

    function symbol_currency($defCurrency='') {

        global $obj;
        if ($defCurrency == '') {
            $ssql = " eDefault='Yes'";
        } else {
            $ssql = " vName='" . $defCurrency . "'";
        }
        $sql = "select vSymbol from currency where" . $ssql;
        $db_curr_mst = $obj->MySQLSelect($sql);
        if (count($db_curr_mst) > 0) {

            return $db_curr_mst[0]['vSymbol'];
        }
    }

    function getVehicleType() {
        global $obj;
        $vehicleType = "SELECT * FROM vehicle_type WHERE 1";
        $result = $obj->MySQLSelect($vehicleType);
        return $result;
    }

    function cleanall($str) {

        $str = trim($str);
        $str = stripslashes($str);
        return($str);
    }

    function RandomString($limit) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $limit; $i++) {
            $randstring .= $characters[rand(0, strlen($characters))];
        }
        return $randstring;
    }
	function Checkverification_mobile($userid,$type){
	
			 global $generalobj,$obj,$tconfig;
			
			if($type == 'rider'){				
				$where = " iUserId = '$userid'";
				$table = "register_user";
				
			}else{
				$where = "iDriverId = '$userid'";
				$table = "register_driver";
				
			}
			
			$db_sql = "select * from $table WHERE $where"; //die;
			$db_user = $obj->MySQLSelect($db_sql);
				
			if($db_user[0]['vPhone'] != "" && $db_user[0]['vEmail'] != "") {
				$_SESSION['sess_iMemberId']=$userid;
				$_SESSION['sess_iUserId'] =$userid;			
				$_SESSION["sess_vName"]= $db_user[0]['vName'];
				$_SESSION["sess_vLastName"]= isset($db_user[0]['vName'])?ucfirst($db_user[0]['vLastName']):'';
				$_SESSION["sess_vEmail"] =$db_user[0]['vEmail'];
				$_SESSION["sess_eGender"]= $db_user[0]['eGender'];
					
				if($type == 'rider'){
					$_SESSION["sess_user"] = "rider";
					$_SESSION["sess_vImage"]= $db_user[0]['vImgName'];
					$link = $tconfig["tsite_url"]."profile_rider.php";
					unset($_SESSION['fb_user']);
					header("Location:".$link);
					exit;	
				}else{
					$_SESSION["sess_user"] = "driver";
					$_SESSION["sess_vImage"]= $db_user[0]['vImage'];
					$link = $tconfig["tsite_url"]."profile.php";
					unset($_SESSION['fb_user']);
					header("Location:".$link);
					exit;	
				}			
			} else {	
				
				$type= base64_encode(base64_encode($type));
				$id = $this->encrypt($userid);
				$link = $tconfig["tsite_url"]."phone_number_verification.php?action=".$type."&id=".$id;		
				header("Location:".$link);
				exit;
			}
		}
		
		
		function Insert_Default_Preferences($user_id,$set_default_etype="",$user_type="Driver"){
			 global $obj;
			$tbl_name = "preferences";
			$tbl_name1 = "driver_preferences";
			
			$sql="select iPreferenceId from $tbl_name where eStatus='Active'";
			$db_preference = $obj->MySQLSelect($sql);
			
			foreach($db_preference as $values){
				$q = "Insert Into ";
				$query = $q . " `" . $tbl_name1 . "` SET
				`iPreferenceId` = '".$values['iPreferenceId']."',
				`iDriverId` = '" . $user_id . "'";  
				
				$obj->sql_query($query);
			}
			return true;
		}
		
		function Get_User_Preferences($user_id,$user_type="Driver"){
			global $obj;
			$tbl_name = "driver_preferences";
			// echo "sa";exit;
			$sql="select dp.iPreferenceId as pref_Id,dp.eType as pref_Type,if(dp.eType = 'Yes',p.vPreferenceImage_Yes,p.vPreferenceImage_No) as pref_Image,if(dp.eType = 'Yes',p.vYes_Title,p.vNo_Title) as pref_Title from driver_preferences dp
			 left join preferences p on p.iPreferenceId = dp.iPreferenceId
			 where dp.iDriverId = '$user_id'";
			 $data_preferences = $obj->MySQLSelect($sql);
			 
			 // echo "<pre>";print_r($data_preferences);exit;
			 return $data_preferences;
		}
		
		function Update_User_Preferences($user_id,$pref_array=array(),$set_default_etype="No",$user_type="Driver"){
			//insert transaction type entries
			global $obj;
			
			if(!empty($pref_array) && $user_id != ""){
				$tbl_name = "driver_preferences";
				
				$sql="select iPreferenceId from preferences where eStatus='Active'";
				$db_preference = $obj->MySQLSelect($sql);
				
				// echo "<pre>";print_r($pref_array);exit;
				
				$sql="select iDriverPreferenceId from $tbl_name where iDriverId = '$user_id'";
				$data_driver_pref = $obj->MySQLSelect($sql);
				
				if(count($data_driver_pref) > 0){
					$sql="delete from $tbl_name where iDriverId = '$user_id'";
					$obj->sql_query($sql);
				}
				
				foreach($db_preference as $value){
					$iPreferenceId = $value['iPreferenceId'];
					$pref_val = $pref_array['vChecked_'.$iPreferenceId];
					$eType = (isset($pref_val) && $pref_val != "") ? $pref_val : $set_default_etype;
					
					$q = "Insert Into ";
					$query = $q . " `" . $tbl_name . "` SET
					`iPreferenceId` = '".$iPreferenceId."',
					`iDriverId` = '" . $user_id . "',
					`eType` = '" . $eType . "'";  
					
					$obj->sql_query($query);
				}
				return true;
			}
		}		

		 function clearEmailFront($email){
			 if(SITE_TYPE=="Demo"){
				 $mail=explode('.',$email);
				 $output=substr($mail[0],0,2);
				 return $output.'*****.'.$mail[count($mail)-1];
			 }
			 else{
				 return $email;
			 }
		 }
		 function clearPhoneFront($text){
			 if(SITE_TYPE=="Demo"){
				return substr_replace( $text,"*****",0,-2);
			 }
			 else{
				 return $text;
			 }
		 }
		 
		 function clearNameFront($text){
			 if(SITE_TYPE=="Demo"){
				 $mail=explode(' ',$text);
				 $output=substr($mail[1],0,1);
				 return $mail[0].' '.$output.'***';
			 }
			 else{
				 return $text;
			 }
		 }
		 
		 function clearCmpNameFront($text){
			 if(SITE_TYPE=="Demo"){
				 if(strpos(trim($text), ' ') !== false){
					 $mail=explode(' ',$text);
					 $output=substr($mail[1],0,1);
					 return $mail[0].' '.$output.'***';
				 }else {
					 $output=substr($text,0,3);
					 return $output.'***';
				 }
			 }
			 else{
				 return $text;
			 }
		 }
	
	
		function getGeneralVar() {
        global $obj;
        //$listField = $obj->MySQLGetFieldsQuery("setting");
        $wri_usql = "SELECT * FROM configurations where eStatus='Active'";

        $wri_ures = $obj->MySQLSelect($wri_usql);

        //print_r($wri_ures );

        for ($i = 0; $i < count($wri_ures); $i++) {
            $vName = $wri_ures[$i]["vName"];
            $vValue = $wri_ures[$i]["vValue"];
            global $$vName;
            $$vName = $vValue;
        }
    }
    
    function getGeneralVarAll() {
        global $obj;
        //$listField = $obj->MySQLGetFieldsQuery("setting");
        $wri_usql = "SELECT vName,TRIM(vValue) as vValue FROM configurations where 1";

        $wri_ures = $obj->MySQLSelect($wri_usql);

        //print_r($wri_ures );

        for ($i = 0; $i < count($wri_ures); $i++) {
            $vName = $wri_ures[$i]["vName"];
            $vValue = $wri_ures[$i]["vValue"];
            global $$vName;
            $$vName = $vValue;
        }
    }
    
    function getGeneralVarAll_Array() {
        global $obj;
        //$listField = $obj->MySQLGetFieldsQuery("setting");
        $wri_usql = "SELECT iSettingId,vName,TRIM(vValue) as vValue FROM configurations where 1";
        $wri_ures = $obj->MySQLSelect($wri_usql);

        return $wri_ures; 
    }
	
	    function encrypt($data) {
        for ($i = 0, $key = 27, $c = 48; $i <= 255; $i++) {
            $c = 255 & ($key ^ ($c << 1));
            $table[$key] = $c;
            $key = 255 & ($key + 1);
        }
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $data[$i] = chr($table[ord($data[$i])]);
        }
        return base64_encode($data);
    }

    function decrypt($data) {
        $data = base64_decode($data);
        for ($i = 0, $key = 27, $c = 48; $i <= 255; $i++) {
            $c = 255 & ($key ^ ($c << 1));
            $table[$c] = $key;
            $key = 255 & ($key + 1);
        }
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $data[$i] = chr($table[ord($data[$i])]);
        }
        return $data;
    }

    function check_password($pass, $hash) {
        if (password_verify($pass, $hash)) {
            $test = 1;
        } else {
            $test = 0;
        }
        return $test;
    }

    public function getParentCatNew($iParentId = 0, $old_cat = "", $iCatIdNot = "0", $loop = 1, $iCategoryId) {
        global $obj, $par_arr_new;
        $sql_query = "select iMenuId, vMenu, iParentId from menu  where iParentId='$iParentId' and eStatus='Active'";
        //$sql_query .= " order by iDisporder ASC";
        $db_cat_rs = $obj->MySQLSelect($sql_query);
        $n = count($db_cat_rs);
        //echo $n;exit;
        if ($n > 0) {
            for ($i = 0; $i < $n; $i++) {
                $par_arr_new[] = array('iMenuId' => $db_cat_rs[$i]['iMenuId'], 'vMenu' => $old_cat . "--|" . $loop . "|&nbsp;&nbsp;" . $db_cat_rs[$i]['vMenu']);
                $this->getParentCatNew($db_cat_rs[$i]['iMenuId'], $old_cat . "&nbsp;&nbsp;&nbsp;&nbsp;", $iCatIdNot, $loop + 1, $iCategoryId);
            }
            $old_cat = "";
        }
        return $par_arr_new;
    }
	
	public function PrintComboBoxNew($arr, $selVal, $name, $title, $key = "", $val = "", $ext = "", $onchange = '', $selectboxName = '', $multiple_select = '') {
        $dcombo = "";
        $a = strrpos($name, "[]");
        if ($a)
            $id = substr($name, 0, $a);
        else
            $id = $name;
        if ($multiple_select != "") {
            $id = $selectboxName;
            $selectboxName = $selectboxName . '[]';
            $multiple_select = "multiple=" . $multiple_select;
        } else {
            $multiple_select = '';
        }

        if ($onchange != "")
            $onchange = "onchange='$onchange'";
        $dcombo .= "<select $multiple_select name=\"$name\" style=\"width:250px;\"   id=\"$id\" class=INPUT $ext $onchange>";
        if ($title != "") {
            if (empty($selVal))
                $sel = "selected";
            else
                $sel = "";
            $dcombo .= "<option value='' $sel>" . $title . "</option>";
        }
        if ($key == "")
            $key = 0;if ($val == "")
            $val = 1;


        for ($i = 0; $i < count($arr); $i++) {
            if (@is_array($selVal)) {
                if (@in_array(trim($arr[$i][$key]), $selVal)) {
                    $dcombo .= "<option value=" . $arr[$i][$key] . " selected>" . $arr[$i][$val] . "</option>";
                } else {
                    $dcombo .= "<option value=" . $arr[$i][$key] . ">" . $arr[$i][$val] . "</option>";
                }
            } else {
                if (trim($selVal) == trim($arr[$i][$key])) {
                    $dcombo .= "<option value=" . $arr[$i][$key] . " selected>" . $arr[$i][$val] . "</option>";
                } else {
                    $dcombo .= "<option value=" . $arr[$i][$key] . ">" . $arr[$i][$val] . "</option>";
                }
            }
        }
        $dcombo .= "</select>";
        return $dcombo;
    }
	
	
	function checkDuplicate($iDbKeyName, $TableName, $db_duplicateFieldArr, $vRedirectFile, $msg, $iDbKeyValue = '', $con = ' or ') {
        // echo "<pre>";print_r($db_duplicateFieldArr); exit;
        global $obj;
        if ($iDbKeyValue != '') {
            $ssql = " and $iDbKeyName <> '" . $iDbKeyValue . "'";
        }
        for ($i = 0; $i < count($db_duplicateFieldArr); $i++) {
            $ssql_field[] = " $db_duplicateFieldArr[$i] = '" . $_REQUEST['Data'][$db_duplicateFieldArr[$i]] . "' ";
        }
        $ssql .= " and ( " . @implode($con, $ssql_field) . ")";
        $sql = "select count($iDbKeyName) as tot from $TableName where 1 " . $ssql;

        $db_cnt = $obj->MySQLSelect($sql);
        //echo $sql;echo "<pre>";print_r($db_cnt); exit;

        if ($db_cnt[0]['tot'] > 0) {
            $_POST['duplicate'] = 1;

            $this->getPostForm($_POST, $msg, $vRedirectFile);
            exit;
        }
    }
	
	function checkDuplicateFront($iDbKeyName, $TableName, $db_duplicateFieldArr, $vRedirectFile, $msg, $iDbKeyValue = '', $con = ' or ') {
        global $obj;
        $ssql = '';
        if ($iDbKeyValue != '') {
            $ssql = " and $iDbKeyName <> '" . $iDbKeyValue . "'";
        }
        for ($i = 0; $i < count($db_duplicateFieldArr); $i++) {
            $ssql_field[] = " $db_duplicateFieldArr[$i] = '" . $_REQUEST[$db_duplicateFieldArr[$i]] . "' ";
        }
        $ssql .= " and ( " . @implode($con, $ssql_field) . ")";
        $sql = "select count($iDbKeyName) as tot from $TableName where 1 " . $ssql;

        $db_cnt = $obj->MySQLSelect($sql);
        //echo "<pre>";print_r($db_cnt);exit;
        if ($db_cnt[0]['tot'] > 0) {
            $_POST['duplicate'] = 1;
            $this->getPostForm($_POST, $msg, $vRedirectFile);
            exit;
        }
    }
	
	function checkDuplicateAdmin($iDbKeyName, $TableName, $db_duplicateFieldArr, $vRedirectFile, $msg, $iDbKeyValue = '', $con = ' or ') {
        global $obj;
        $ssql = '';
        if ($iDbKeyValue != '') {
            $ssql = " and $iDbKeyName <> '" . $iDbKeyValue . "'";
        }
        for ($i = 0; $i < count($db_duplicateFieldArr); $i++) {
            $ssql_field[] = " $db_duplicateFieldArr[$i] = '" . $_REQUEST[$db_duplicateFieldArr[$i]] . "' ";
        }
        $ssql .= " and ( " . @implode($con, $ssql_field) . ")";
        $sql = "select count($iDbKeyName) as tot from $TableName where 1 " . $ssql;

        $db_cnt = $obj->MySQLSelect($sql);
        //echo "<pre>";print_r($db_cnt);exit;
        if ($db_cnt[0]['tot'] > 0) {
            $duplicate = 1;
            // $_POST['duplicate'] = 1;
            //$this->getPostForm($_POST, $msg, $vRedirectFile);
            // exit;
        } else {
            $duplicate = 0;
        }

        return $duplicate;
    }
	
	function checkDuplicateAdminNew($iDbKeyName, $TableName, $db_duplicateFieldArr, $iDbKeyValue = '', $con = ' or ') {
        global $obj;
        $ssql = '';
        if ($iDbKeyValue != '') {
            $ssql = " and $iDbKeyName <> '" . $iDbKeyValue . "'";
        }
        for ($i = 0; $i < count($db_duplicateFieldArr); $i++) {
            $ssql_field[] = " $db_duplicateFieldArr[$i] = '" . $_REQUEST[$db_duplicateFieldArr[$i]] . "' ";
        }
        $ssql .= " and ( " . @implode($con, $ssql_field) . ")";
        $sql = "select count($iDbKeyName) as tot from $TableName where 1 " . $ssql;

        $db_cnt = $obj->MySQLSelect($sql);
        //echo "<pre>";print_r($db_cnt);exit;
        if ($db_cnt[0]['tot'] > 0) {
            $duplicate = 1;
            // $_POST['duplicate'] = 1;
            //$this->getPostForm($_POST, $msg, $vRedirectFile);
            // exit;
        } else {
            $duplicate = 0;
        }

        return $duplicate;
    }
	
	function getPostForm1($POST_Arr, $msg = "", $action = "") {
        $str = '
			<html>
			<form name="frm1" action="' . $action . '" method=post>';
        foreach ($POST_Arr as $key => $value) {
            if ($key != "mode") {
                if (is_array($value)) {
                    for ($i = 0; $i < count($value); $i++)
                        $str .= '<br><input type="Hidden" name="' . $key . '[]" value="' . stripslashes($value[$i]) . '">';
                } else {
                    $str .= '<br><input type="Hidden" name="' . $key . '" value="' . stripslashes($value) . '">';
                }
            }
        }
        $str .= '<input type="Hidden" name=var_msg_err value="' . $msg . '">
			</form>
			<script>
			document.frm1.submit();
			</script>
			</html>';

        $str;
        exit;
    }
	
	function getPostForm($POST_Arr, $msg = "", $action = "") {
        $str = '
			<html>
			<form name="frm1" action="' . $action . '" method=post>';
        foreach ($POST_Arr as $key => $value) {
            if ($key != "mode") {
                if (is_array($value)) {
                    foreach ($value as $kk => $vv)
                        $str .= '<br><input type="Hidden" name="Data[' . $kk . ']" value="' . stripslashes($vv) . '">';
                    $str .= '<br><input type="Hidden" name="' . $key . '[]" value="' . stripslashes($value[$i]) . '">';
                } else {
                    $str .= '<br><input type="Hidden" name="' . $key . '" value="' . stripslashes($value) . '">';
                }
            }
        }
        $str .= '<input type="Hidden" name=var_msg value="' . $msg . '">
			</form>
			<script>
			document.frm1.submit();
			</script>
			</html>';

        echo $str;
        exit;
    }
	
	function get_value($table, $field_name, $condition_field = '', $condition_value = '', $setParams = '', $directValue = '') {
		global $obj;
		$returnValue = array();
		
		$where = ($condition_field != '') ? ' WHERE ' . $this->clean($condition_field) : '';
		$where .= ($where != '' && $condition_value != '') ? ' = "' . $this->clean($condition_value) . '"' : '';
		
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
	
	
	function checkSurgePrice($vehicleTypeID, $selectedDateTime=""){
		// echo "sa";exit;
		
		$ePickStatus=$this->get_value('vehicle_type', 'ePickStatus', 'iVehicleTypeId',$vehicleTypeID,'','true');
		$eNightStatus=$this->get_value('vehicle_type', 'eNightStatus', 'iVehicleTypeId',$vehicleTypeID,'','true');
		
		$fPickUpPrice = 1;
		$fNightPrice = 1;
		
		if($selectedDateTime == ""){
			// $currentTime = @date("Y-m-d H:i:s");
			$currentTime = @date("H:i:s");
			$currentDay = @date("D");
		}else{
			// $currentTime = $selectedDateTime;
			$currentTime = @date("H:i:s",strtotime($selectedDateTime));
			$currentDay = @date("D",strtotime($selectedDateTime));
		}
			
		if($ePickStatus == "Active" || $eNightStatus == "Active"){
			
			$startTime_str = "t".$currentDay."PickStartTime";
			$endTime_str = "t".$currentDay."PickEndTime";
			$price_str = "f".$currentDay."PickUpPrice";
			
			$pickStartTime=$this->get_value('vehicle_type', $startTime_str, 'iVehicleTypeId',$vehicleTypeID,'','true');
			$pickEndTime=$this->get_value('vehicle_type', $endTime_str, 'iVehicleTypeId',$vehicleTypeID,'','true');
			$fPickUpPrice=$this->get_value('vehicle_type', $price_str, 'iVehicleTypeId',$vehicleTypeID,'','true');
			
			$nightStartTime=$this->get_value('vehicle_type', 'tNightStartTime', 'iVehicleTypeId',$vehicleTypeID,'','true');
			$nightEndTime=$this->get_value('vehicle_type', 'tNightEndTime', 'iVehicleTypeId',$vehicleTypeID,'','true');
			$fNightPrice=$this->get_value('vehicle_type', 'fNightPrice', 'iVehicleTypeId',$vehicleTypeID,'','true');
				
			$tempNightHour = "12:00:00";		
			if($currentTime > $pickStartTime && $currentTime < $pickEndTime && $ePickStatus == "Active"){
				$returnArr['Action'] = "0";
				$returnArr['fPickUpPrice'] = $fPickUpPrice;
				$returnArr['fNightPrice'] = "1";
				$returnArr['surgeprice'] = $fPickUpPrice;
				$returnArr['surgetype'] = "PickUp";
				$returnArr['pickStartTime'] = $pickStartTime;
				$returnArr['pickEndTime'] = $pickEndTime;
			
			}else if((($currentTime > $nightStartTime && $currentTime < $nightEndTime && $nightEndTime > $tempNightHour) || ($currentTime <$nightStartTime && $currentTime < $nightEndTime && $nightEndTime < $tempNightHour && $nightStartTime > $tempNightHour) || ($currentTime > $nightStartTime && $currentTime > $nightEndTime && $nightEndTime < $tempNightHour && $nightStartTime > $tempNightHour) || ($currentTime > $nightStartTime && $currentTime < $nightEndTime && $nightEndTime < $tempNightHour)) && $eNightStatus == "Active"){
					
				$returnArr['Action'] = "0";
				$returnArr['fPickUpPrice'] = "1";
				$returnArr['fNightPrice'] = $fNightPrice;
				$returnArr['surgeprice'] = $fNightPrice;
				$returnArr['surgetype'] = "Night";
				$returnArr['nightStartTime'] = $nightStartTime;
				$returnArr['nightEndTime'] = $nightEndTime;
					
			}else{
				$returnArr['Action'] = "1";
			}
			
		}else{
			$returnArr['Action'] = "1";
		}
		
		return $returnArr;
	}


    function cal_trip_price_details($iTripId, $iDriverVehicleId, $iVehicleTypeId ) {
        global $obj;


        $iVehicleCategoryId = $this->get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId', $iVehicleTypeId, '', 'true');
        $vVehicleCategoryData = $this->get_value('vehicle_category', 'iParentId,ePriceType,vLogo,vCategory_EN as vCategory', 'iVehicleCategoryId', $iVehicleCategoryId);
        $vVehicleFare = $this->get_value('vehicle_type','fFixedFare', 'iVehicleTypeId', $iVehicleTypeId , '', 'true');
        $iParentId = $vVehicleCategoryData[0]['iParentId'];

        if($iParentId == 0){
            $ePriceType = $vVehicleCategoryData[0]['ePriceType'];
        }else{ 
            $ePriceType = $this->get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId,'','true'); 
        }
        $ALLOW_SERVICE_PROVIDER_AMOUNT = $ePriceType == "Provider"? "Yes" :"No";
        if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes") {
            $sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $iVehicleTypeId . "'";
            $serviceProData = $obj->MySQLSelect($sqlServicePro);



            if (count($serviceProData) > 0) {
                    $fAmount = $serviceProData[0]['fAmount'];
                    $vVehicleFare = $fAmount;
            }
        }
        return $vVehicleFare;
    }
	
	function GetAllSurgePriceDetails($vehicleTypeID, $selectedDateTime=""){
		// echo "sa";exit;
		
		$ePickStatus=$this->get_value('vehicle_type', 'ePickStatus', 'iVehicleTypeId',$vehicleTypeID,'','true');
		$eNightStatus=$this->get_value('vehicle_type', 'eNightStatus', 'iVehicleTypeId',$vehicleTypeID,'','true');
		
		$fPickUpPrice = 1;
		$fNightPrice = 1;
		
		if($selectedDateTime == ""){
			// $currentTime = @date("Y-m-d H:i:s");
			$currentTime = @date("H:i:s");
			$currentDay = @date("D");
		}else{
			// $currentTime = $selectedDateTime;
			$currentTime = @date("H:i:s",strtotime($selectedDateTime));
			$currentDay = @date("D",strtotime($selectedDateTime));
		}
			
		$returnArr['PickUpDetails']['ePickStatus'] = $ePickStatus;
		$returnArr['NightDetails']['eNightStatus'] = $eNightStatus;
		
		if($ePickStatus == "Active" || $eNightStatus == "Active"){
			
			$startTime_str = "t".$currentDay."PickStartTime";
			$endTime_str = "t".$currentDay."PickEndTime";
			$price_str = "f".$currentDay."PickUpPrice";
			
			$pickStartTime=$this->get_value('vehicle_type', $startTime_str, 'iVehicleTypeId',$vehicleTypeID,'','true');
			$pickEndTime=$this->get_value('vehicle_type', $endTime_str, 'iVehicleTypeId',$vehicleTypeID,'','true');
			$fPickUpPrice=$this->get_value('vehicle_type', $price_str, 'iVehicleTypeId',$vehicleTypeID,'','true');
			
			$nightStartTime=$this->get_value('vehicle_type', 'tNightStartTime', 'iVehicleTypeId',$vehicleTypeID,'','true');
			$nightEndTime=$this->get_value('vehicle_type', 'tNightEndTime', 'iVehicleTypeId',$vehicleTypeID,'','true');
			$fNightPrice=$this->get_value('vehicle_type', 'fNightPrice', 'iVehicleTypeId',$vehicleTypeID,'','true');
				
			$tempNightHour = "12:00:00";
			
			$returnArr['PickUpDetails']['PickStartTime'] = $pickStartTime;
			$returnArr['PickUpDetails']['PickEndTime'] = $pickEndTime;
			$returnArr['PickUpDetails']['fPickUpPrice'] = $fPickUpPrice;
			$returnArr['NightDetails']['NightStartTime'] = $nightStartTime;
			$returnArr['NightDetails']['NightEndTime'] = $nightEndTime;
			$returnArr['NightDetails']['fNightPrice'] = $fNightPrice;
			
			if($currentTime > $pickStartTime && $currentTime < $pickEndTime && $ePickStatus == "Active"){
				$returnArr['Action'] = "0";
				$returnArr['SurgePrice'] = $fPickUpPrice;
				$returnArr['SurgeType'] = "PickUp";
			}else if((($currentTime > $nightStartTime && $currentTime < $nightEndTime && $nightEndTime > $tempNightHour) || ($currentTime <$nightStartTime && $currentTime < $nightEndTime && $nightEndTime < $tempNightHour && $nightStartTime > $tempNightHour) || ($currentTime > $nightStartTime && $currentTime > $nightEndTime && $nightEndTime < $tempNightHour && $nightStartTime > $tempNightHour) || ($currentTime > $nightStartTime && $currentTime < $nightEndTime && $nightEndTime < $tempNightHour)) && $eNightStatus == "Active"){
					
				$returnArr['Action'] = "0";
				$returnArr['SurgePrice'] = $fNightPrice;
				$returnArr['SurgeType'] = "Night";
			}else{
				$returnArr['Action'] = "1";
				$returnArr['Status'] = "NoSurgesApplied";
			}
			
		}else{
			$returnArr['Action'] = "1";
			$returnArr['Status'] = "NoSurgesAdded";
		}
		
		// echo "<pre>";print_r($returnArr);exit;
		return $returnArr;
	}
  
  
   function copyRemoteFile($url, $localPathname){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    
        $data = curl_exec($ch);
        curl_close($ch);
    
        if ($data) {
            $fp = fopen($localPathname, 'wb');
    
            if ($fp) {
                fwrite($fp, $data);
                fclose($fp);
            } else {
                fclose($fp);
                //return false;
            }
        } else {
           // return false;
        }
    
        return true;
   }
   
   function contains($point, $polygon)
	{
	    if($polygon[0] != $polygon[count($polygon)-1])
	        $polygon[count($polygon)] = $polygon[0];
		
	    $j = 0;
	    $oddNodes = false;
	    $x = $point[1];
	    $y = $point[0];
	    $n = count($polygon);
	    for ($i = 0; $i < $n; $i++)
	    {
	        $j++;
	        if ($j == $n)
	        {
	            $j = 0;
	        }
	        if ((($polygon[$i][0] < $y) && ($polygon[$j][0] >= $y)) || (($polygon[$j][0] < $y) && ($polygon[$i][0] >=
	            $y)))
	        {
	            if ($polygon[$i][1] + ($y - $polygon[$i][0]) / ($polygon[$j][0] - $polygon[$i][0]) * ($polygon[$j][1] -
	                $polygon[$i][1]) < $x)
	            {
	                $oddNodes = !$oddNodes;
	            }
	        }
	    }
	    return $oddNodes;
	}
	
	function GetVehicleTypeFromGeoLocation($Address_Array){
		global $generalobj, $obj;
		
    $Vehicle_Str = "-1";     
    if(!empty($Address_Array)){
			$sqlaa = "SELECT * FROM location_master WHERE eStatus='Active' AND eFor = 'VehicleType'";
			$allowed_data = $obj->MySQLSelect($sqlaa);   
			if(!empty($allowed_data)){
				$polygon = array();
				foreach($allowed_data as $key => $val) {
					$latitude = explode(",",$val['tLatitude']);
					$longitude = explode(",",$val['tLongitude']);
					for ($x = 0; $x < count($latitude); $x++) {
						if(!empty($latitude[$x]) || !empty($longitude[$x])) {
							$polygon[$key][] = array($latitude[$x],$longitude[$x]);
						}
					}
					
					//print_r($polygon[$key]);
					if($polygon[$key]){
						
						$address = $this->contains($Address_Array,$polygon[$key]) ? 'IN' : 'OUT';
						if($address == 'IN'){
							$Vehicle_Str .= ",".$val['iLocationId'];
							//break;
						}
					}
				}    
			} 
		}     
		return $Vehicle_Str;
	}
	 
############### Invoice Price For Web   ###################################################################
function getTripPriceDetailsForWeb($iTripId, $iMemberId = '', $eUserType = '') {
    global $obj, $generalobj, $tconfig;
    $returnArr = array();
    $userlangcode = "";
    $currencycode = "";
    $eUnit = "";
    $TripRating = "";
    if ($eUserType == "Passenger") {
        $tblname = "register_user";
        $vLang = "vLang";
        $iUserId = "iUserId";
        $vCurrency = "vCurrencyPassenger";
        //$currencycode = get_value("trips", $vCurrency, "iTripId", $iTripId, '', 'true');
        $sqlp = "SELECT ru.vCurrencyPassenger,ru.vLang,cu.vSymbol FROM register_user as ru LEFT JOIN currency as cu ON ru.vCurrencyPassenger = cu.vName WHERE iUserId = '".$iMemberId."'";
        $passengerData = $obj->MySQLSelect($sqlp);
        $currencycode = $passengerData[0]['vCurrencyPassenger'];
        $userlangcode = $passengerData[0]['vLang'];
        $currencySymbol = $passengerData[0]['vSymbol']; 
    } else if($eUserType == "Driver") {
        $tblname = "register_driver";
        $vLang = "vLang";
        $iUserId = "iDriverId";
        $vCurrency = "vCurrencyDriver";
        
        //$currencycode = get_value($tblname, $vCurrency, $iUserId, $iMemberId, '', 'true');
        $sqld = "SELECT rd.vCurrencyDriver,rd.vLang,cu.vSymbol FROM register_driver as rd LEFT JOIN currency as cu ON rd.vCurrencyDriver = cu.vName WHERE iDriverId = '".$iMemberId."'";
        $driverData = $obj->MySQLSelect($sqld);
        $currencycode = $driverData[0]['vCurrencyDriver'];
        $userlangcode = $driverData[0]['vLang'];
        $currencySymbol = $driverData[0]['vSymbol'];
    }
    //$userlangcode = get_value($tblname, $vLang, $iUserId, $iMemberId, '', 'true');
    if ($userlangcode == "" || $userlangcode == NULL) {
        $userlangcode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
    }
    $userlangcode = $_SESSION['sess_lang'];
    $languageLabelsArr = getLanguageLabelsArr($userlangcode, "1");

    if($currencycode == "" || $currencycode == NULL) {
      $sql = "SELECT vName,vSymbol from currency WHERE eDefault = 'Yes'";
      $currencyData = $obj->MySQLSelect($sql);
      $currencycode = $currencyData[0]['vName'];
      $currencySymbol = $currencyData[0]['vSymbol'];
    }
        
    //$sql = "SELECT * from trips WHERE iTripId = '" . $iTripId . "'";                                                                      
    $sql = "SELECT tr.*,vt.vVehicleType_".$userlangcode." as vVehicleType,vt.vLogo,vt.iVehicleCategoryId,vt.fFixedFare,vt.eIconType,COALESCE(vc.iParentId, '0') as iParentId,COALESCE(vc.ePriceType, '') as ePriceType,COALESCE(vc.vLogo, '') as vLogoVehicleCategory,COALESCE(vc.vCategory_".$userlangcode.", '') as vCategory from trips as tr LEFT JOIN  vehicle_type as vt ON tr.iVehicleTypeId = vt.iVehicleTypeId  LEFT JOIN vehicle_category as vc ON vt.iVehicleCategoryId = vc.iVehicleCategoryId WHERE tr.iTripId = '" . $iTripId . "'";
    $tripData = $obj->MySQLSelect($sql);
    $priceRatio = $tripData[0]['fRatio_' . $currencycode];
    $iActive = $tripData[0]['iActive'];
    // Convert Into Timezone
    $tripTimeZone = $tripData[0]['vTimeZone'];
    if($tripTimeZone != "") {
        $serverTimeZone = date_default_timezone_get();
        $tripData[0]['tTripRequestDate'] = converToTz($tripData[0]['tTripRequestDate'],$tripTimeZone,$serverTimeZone);
        $tripData[0]['tDriverArrivedDate'] = converToTz($tripData[0]['tDriverArrivedDate'],$tripTimeZone,$serverTimeZone);
        if($tripData[0]['tStartDate'] != "0000-00-00 00:00:00") {
            $tripData[0]['tStartDate'] = converToTz($tripData[0]['tStartDate'],$tripTimeZone,$serverTimeZone);
        }
        $tripData[0]['tEndDate'] = converToTz($tripData[0]['tEndDate'],$tripTimeZone,$serverTimeZone);
    }
    // Convert Into Timezone
    
    $returnArr = array_merge($tripData[0], $returnArr);
    if ($tripData[0]['iUserPetId'] > 0) {
        $petDetails_arr = get_value('user_pets', 'iPetTypeId,vTitle as PetName,vWeight as PetWeight, tBreed as PetBreed, tDescription as PetDescription', 'iUserPetId', $tripData[0]['iUserPetId'], '', '');
    } else {
        $petDetails_arr = array();
    }
    
    $iPackageTypeId = $tripData[0]['iPackageTypeId'];
    if($iPackageTypeId != 0) {
       $returnArr['PackageType'] = get_value('package_type', 'vName', 'iPackageTypeId', $iPackageTypeId, '', 'true');
    }
        
    if (count($petDetails_arr) > 0) {
        $petTypeName = get_value('pet_type', 'vTitle_' . $userlangcode, 'iPetTypeId', $petDetails_arr[0]['iPetTypeId'], '', 'true');
        $returnArr['PetDetails']['PetName'] = $petDetails_arr[0]['PetName'];
        $returnArr['PetDetails']['PetWeight'] = $petDetails_arr[0]['PetWeight'];
        $returnArr['PetDetails']['PetBreed'] = $petDetails_arr[0]['PetBreed'];
        $returnArr['PetDetails']['PetDescription'] = $petDetails_arr[0]['PetDescription'];
        $returnArr['PetDetails']['PetTypeName'] = $petTypeName;
    } else {
        $returnArr['PetDetails']['PetName'] = '';
        $returnArr['PetDetails']['PetWeight'] = '';
        $returnArr['PetDetails']['PetBreed'] = '';
        $returnArr['PetDetails']['PetDescription'] = '';
        $returnArr['PetDetails']['PetTypeName'] = '';
    }
        
    /* User Wallet Information */
    $returnArr['UserDebitAmount'] = strval($tripData[0]['fWalletDebit']);
    /* User Wallet Information */

    /*$vVehicleType = get_value('vehicle_type', "vVehicleType_" . $userlangcode, 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');
    $vVehicleTypeLogo = get_value('vehicle_type', "vLogo", 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');
    $iVehicleCategoryId = get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');
    $vVehicleCategoryData = get_value('vehicle_category', 'iParentId,ePriceType,vLogo,vCategory_' . $userlangcode . ' as vCategory', 'iVehicleCategoryId', $iVehicleCategoryId);
    $vVehicleFare = get_value('vehicle_type','fFixedFare', 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');                    
    $iParentId = $vVehicleCategoryData[0]['iParentId'];*/
    $vVehicleType = $tripData[0]['vVehicleType'];
    $vVehicleTypeLogo = $tripData[0]['vLogo'];   
    $iVehicleCategoryId = $tripData[0]['iVehicleCategoryId'];  
    $vVehicleCategoryData[0]['vLogo'] = $tripData[0]['vLogoVehicleCategory']; 
    $vVehicleCategoryData[0]['vCategory'] = $tripData[0]['vCategory'];        
    $vVehicleFare = $tripData[0]['fFixedFare'];         
    $iParentId = $tripData[0]['iParentId'];

    if($iParentId == 0) {
        $ePriceType = $tripData[0]['ePriceType']; 
    } else { 
        $ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId,'','true'); 
    }

    //$eIconType = get_value('vehicle_type', "eIconType", 'iVehicleTypeId', $tripData[0]['iVehicleTypeId'], '', 'true');
    $eIconType = $tripData[0]['eIconType'];
    $TripTime = date('h:iA', strtotime($tripData[0]['tTripRequestDate']));
    $tTripRequestDateOrig = $tripData[0]['tTripRequestDate'];
        
    // Convert Into Timezone
    // $tripTimeZone = $tripData[0]['vTimeZone'];
    // if($tripTimeZone != ""){
    // $serverTimeZone = date_default_timezone_get();
    // $tTripRequestDateOrig = converToTz($tTripRequestDateOrig,$tripTimeZone,$serverTimeZone);
    // }
        
    // Convert Into Timezone
    $tTripRequestDate = date('dS M Y \a\t h:i a', strtotime($tripData[0]['tTripRequestDate']));
    $tStartDate = $tripData[0]['tStartDate'];
    $tEndDate = $tripData[0]['tEndDate'];
    $totalTime = 0;
    if($tStartDate != '' && $tStartDate != '0000-00-00 00:00:00' && $tEndDate != '' && $tEndDate != '0000-00-00 00:00:00') {
        if ($tripData[0]['eFareType'] == "Hourly") {
            // $hours       =   0; 
            // $minutes     =   0;
            $totalSec   =   0;
            $sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$iTripId'";
            $db_tripTimes = $obj->MySQLSelect($sql22);
            foreach($db_tripTimes as $dtT){
                if($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
                    $totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
                }
            }
            
            $years = floor($totalSec / (365*60*60*24)); $months = floor(($totalSec - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            $hours = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
            $minuts = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
            $seconds = floor(($totalSec - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));
            
            if ($days > 0) {
                $hours = ($days * 24) + $hours;
            }
            if ($hours > 0) {
                $totalTime = $hours.':'.$minuts.':'.$seconds;
            } else if ($minuts > 0) {
                $totalTime = $minuts.':'.$seconds. " " . $languageLabelsArr['LBL_MINUTES_TXT'];
            }
            if ($totalTime < 1) {
                $totalTime = $seconds . " " . $languageLabelsArr['LBL_SECONDS_TXT'];
            }
        } else {
            $days = dateDifference($tStartDate, $tEndDate,'%a');
            $hours = dateDifference($tStartDate, $tEndDate, '%h');
            $minutes = dateDifference($tStartDate, $tEndDate, '%i');
            $seconds = dateDifference($tStartDate, $tEndDate, '%s');
            $LBL_HOURS_TXT = ($hours > 1)? $languageLabelsArr['LBL_HOURS_TXT'] : $languageLabelsArr['LBL_HOUR_TXT'];
            $LBL_MINUTES_TXT = ($minutes > 1)? $languageLabelsArr['LBL_MINUTES_TXT'] : $languageLabelsArr['LBL_MINUTE'];
            $hours = str_pad($hours, 2, '0', STR_PAD_LEFT);
            $minutes = str_pad($minutes, 2, '0', STR_PAD_LEFT);
            $seconds = str_pad($seconds, 2, '0', STR_PAD_LEFT);
            if ($days > 0) {
                $hours = ($days * 24) + $hours;
            }
            if ($hours > 0) {
                //$totalTime = $hours * 60;
                //$totalTime = $hours.':'.$minutes.':'.$seconds." " .$languageLabelsArr['LBL_HOUR'] ;
                $totalTime = $hours.':'.$minutes.':'.$seconds." " .$LBL_HOURS_TXT ;
            } else if ($minutes > 0) {
                //$totalTime = $totalTime + $minutes;
                //$totalTime = $minutes.':'.$seconds. " " . $languageLabelsArr['LBL_MINUTES_TXT'];
                $totalTime = $minutes.':'.$seconds. " " . $LBL_MINUTES_TXT;
            }
            //$totalTime = $totalTime . ":" . $seconds . " " . $languageLabelsArr['LBL_MINUTES_TXT'];
            if ($totalTime < 1) {
                $totalTime = $seconds . " " . $languageLabelsArr['LBL_SECONDS_TXT'];
            }
        }
    }
    if($totalTime == 0){
       $totalTime = "0.00 ".$languageLabelsArr['LBL_MINUTE'];
    }

    $returnArr['carTypeName'] = $vVehicleType;
    $returnArr['carImageLogo'] = $vVehicleTypeLogo;
    if ($eUserType == "Passenger") {
        $TripRating = get_value('ratings_user_driver', 'vRating1', 'iTripId', $iTripId, ' AND eUserType="Driver"', 'true');
        $returnArr['vDriverImage'] = get_value('register_driver', 'vImage', 'iTripId', $tripData[0]['iDriverId'], '', 'true');
        //$driverDetailArr = get_value('register_driver', '*', 'iDriverId', $tripData[0]['iDriverId']);
        $eUnit = $tripData[0]['vCountryUnitRider'];
    } else if($eUserType == "Driver") {
        $TripRating = get_value('ratings_user_driver', 'vRating1', 'iTripId', $iTripId, ' AND eUserType="Passenger"', 'true');
        //$passgengerDetailArr = get_value('register_user', '*', 'iUserId', $tripData[0]['iUserId']);
        $eUnit = $tripData[0]['vCountryUnitDriver'];
        //$eUnit = $tripData[0]['vCountryUnitRider'];
    }
        
    if($eUnit == "Miles"){
        $DisplayDistanceTxt = $languageLabelsArr['LBL_MILE_DISTANCE_TXT']; 
    } else {
        $DisplayDistanceTxt = $languageLabelsArr['LBL_KM_DISTANCE_TXT'];
    }
    
    if ($TripRating == "" || $TripRating == NULL) {
        $TripRating = "0";
    }

    $iFare = $tripData[0]['iFare'];
    //$iFare = $tripData[0]['iFare']+$tripData[0]['fTollPrice'];
    $fPricePerKM = $tripData[0]['fPricePerKM'] * $priceRatio;
    $iBaseFare = $tripData[0]['iBaseFare'] * $priceRatio;
    $fPricePerMin = $tripData[0]['fPricePerMin'] * $priceRatio;
    $fCommision = $tripData[0]['fCommision'];
    $fDistance = $tripData[0]['fDistance'];
    if($eUnit == "Miles"){
        $fDistance = round($fDistance * 0.621371,2);  
    }
    $vDiscount = $tripData[0]['vDiscount']; // 50 $
    $fDiscount = $tripData[0]['fDiscount']; // 50
    $fMinFareDiff = $tripData[0]['fMinFareDiff'] * $priceRatio;
    $fWalletDebit = $tripData[0]['fWalletDebit'];
    $fSurgePriceDiff = $tripData[0]['fSurgePriceDiff'] * $priceRatio;
    $fTripGenerateFare = $tripData[0]['fTripGenerateFare'] * $priceRatio;
    $fPickUpPrice = $tripData[0]['fPickUpPrice'];
    $fNightPrice = $tripData[0]['fNightPrice'];
    $eFlatTrip = $tripData[0]['eFlatTrip']; 
    $fFlatTripPrice = $tripData[0]['fFlatTripPrice'] * $priceRatio;
    $fTipPrice = $tripData[0]['fTipPrice'] * $priceRatio;
    $fVisitFee = $tripData[0]['fVisitFee'] * $priceRatio;
    $fMaterialFee = $tripData[0]['fMaterialFee'] * $priceRatio;
    $fMiscFee = $tripData[0]['fMiscFee'] * $priceRatio;
    $fDriverDiscount = $tripData[0]['fDriverDiscount'] * $priceRatio;
    $vVehicleFare = $vVehicleFare * $priceRatio;
    $fCancelPrice = $tripData[0]['fCancellationFare'] * $priceRatio;
    $fTollPrice = $tripData[0]['fTollPrice'] * $priceRatio;
    $fTax1 = $tripData[0]['fTax1'] * $priceRatio;
    $fTax2 = $tripData[0]['fTax2'] * $priceRatio;
    if($fTollPrice > 0){
        $eTollSkipped = $tripData[0]['eTollSkipped'];
    } else {
        $eTollSkipped = "Yes";
    }
    $tUserComment = $tripData[0]['tUserComment']; 
        
    $returnArr['tUserComment'] = $tUserComment;
    $returnArr['vVehicleType'] = $vVehicleType;
    $returnArr['eIconType'] = $eIconType;
    $returnArr['vVehicleCategory'] = $vVehicleCategoryData[0]['vCategory'];
    $returnArr['TripTime'] = $TripTime;
    $returnArr['ConvertedTripRequestDate'] = $tTripRequestDate;
    $returnArr['FormattedTripDate'] = $tTripRequestDate;
    $returnArr['tTripRequestDateOrig'] = $tTripRequestDateOrig;
    $returnArr['tTripRequestDate'] = $tTripRequestDate;
    $returnArr['TripTimeInMinutes'] = $totalTime;
    $returnArr['TripRating'] = $TripRating;
    $returnArr['CurrencySymbol'] = $currencySymbol;
    $returnArr['TripFare'] = formatNum($iFare * $priceRatio);
    $returnArr['iTripId'] = $tripData[0]['iTripId'];
    $returnArr['vTripPaymentMode'] = $tripData[0]['vTripPaymentMode'];
    $returnArr['eType'] = $tripData[0]['eType'];
    if($tripData[0]['eType'] == "UberX" && $tripData[0]['eFareType'] != "Regular") {
      $returnArr['tDaddress'] = "";
    }
    if ($tripData[0]['vBeforeImage'] != "") {
        $returnArr['vBeforeImage'] = $tconfig['tsite_upload_trip_images'] . $tripData[0]['vBeforeImage'];
    }
    if ($tripData[0]['eType'] == "UberX") {
        $returnArr['vLogoVehicleCategoryPath'] = $tconfig['tsite_upload_images_vehicle_category'] . "/" . $iVehicleCategoryId . "/";
        $returnArr['vLogoVehicleCategory'] = $vVehicleCategoryData[0]['vLogo'];
    } else {
        $returnArr['vLogoVehicleCategory'] = "";
        $returnArr['vLogoVehicleCategoryPath'] = "";
    }
    if ($tripData[0]['vAfterImage'] != "") {
        $returnArr['vAfterImage'] = $tconfig['tsite_upload_trip_images'] . $tripData[0]['vAfterImage'];
    }
    $originalFare = $iFare;
    if ($eUserType == "Passenger") {
        $iFare = $iFare;
    } else {
        //$iFare = $tripData[0]['fTripGenerateFare'] - $fCommision;
        //$iFare = $tripData[0]['fTripGenerateFare'] + $tripData[0]['fTipPrice'] - $fCommision;
        // $iFare = $tripData[0]['fTripGenerateFare'] + $tripData[0]['fTipPrice'] - $tripData[0]['fTollPrice'] - $fCommision;
        $iFare = $tripData[0]['fTripGenerateFare'] + $tripData[0]['fTipPrice'] - $fCommision - $tripData[0]['fTax1'] - $tripData[0]['fTax2'];
    }
    $surgePrice = 1;
    if ($tripData[0]['fPickUpPrice'] > 1) {
        $surgePrice = $tripData[0]['fPickUpPrice'];
    } else {
        $surgePrice = $tripData[0]['fNightPrice'];
    }
    $SurgePriceFactor = strval($surgePrice);
    
    $returnArr['TripFareOfMinutes'] = formatNum($tripData[0]['fPricePerMin'] * $priceRatio);
    $returnArr['TripFareOfDistance'] = formatNum($tripData[0]['fPricePerKM'] * $priceRatio);
    $returnArr['iFare'] = formatNum($iFare * $priceRatio);
    $returnArr['iOriginalFare'] = formatNum($originalFare * $priceRatio);
    $returnArr['TotalFare'] = formatNum($iFare * $priceRatio);
    $returnArr['fPricePerKM'] = formatNum($fPricePerKM);
    $returnArr['iBaseFare'] = formatNum($iBaseFare);
    $returnArr['fPricePerMin'] = formatNum($fPricePerMin);
    $returnArr['fCommision'] = formatNum($fCommision * $priceRatio);
    $returnArr['fDistance'] = formatNum($fDistance);
    $returnArr['fDiscount'] = formatNum($fDiscount * $priceRatio);
    $returnArr['fMinFareDiff'] = formatNum($fMinFareDiff);
    $returnArr['fWalletDebit'] = formatNum($fWalletDebit * $priceRatio);
    $returnArr['fSurgePriceDiff'] = formatNum($fSurgePriceDiff);
    $returnArr['fTripGenerateFare'] = formatNum($fTripGenerateFare);
    $returnArr['fFlatTripPrice'] = formatNum($fFlatTripPrice);
    if($eTollSkipped == "No"){
        $returnArr['fTollPrice'] = formatNum($fTollPrice);   
    }
    if($fTipPrice > 0){
        $returnArr['fTipPrice'] = $currencySymbol.formatNum($fTipPrice);
    }
    $returnArr['SurgePriceFactor'] = $SurgePriceFactor;
    $returnArr['fVisitFee'] = formatNum($fVisitFee);
    $returnArr['fMaterialFee'] = formatNum($fMaterialFee);
    $returnArr['fMiscFee'] = formatNum($fMiscFee);
    $returnArr['fDriverDiscount'] = formatNum($fDriverDiscount);
    $returnArr['fCancelPrice'] = formatNum($fCancelPrice);
    $returnArr['fTax1'] = formatNum($fTax1);
    $returnArr['fTax2'] = formatNum($fTax2);
    $returnArr['DisplayDistanceTxt'] = $DisplayDistanceTxt;
    //echo "<pre>"; print_r($tripData); die;
        
    $iDriverId = $tripData[0]['iDriverId'];
    $driverDetails = get_value('register_driver', '*', 'iDriverId', $iDriverId);
    $driverDetails[0]['vImage'] = ($driverDetails[0]['vImage'] != "" && $driverDetails[0]['vImage'] != "NONE") ? $driverDetails[0]['vImage'] : "";
    $driverDetails[0]['vPhone'] = '+'.$driverDetails[0]['vCode'].$driverDetails[0]['vPhone'];
    $returnArr['DriverDetails'] = $driverDetails[0];
    
    $iUserId = $tripData[0]['iUserId'];
    $passengerDetails = get_value('register_user', '*', 'iUserId', $iUserId);
    $passengerDetails[0]['vImgName'] = ($passengerDetails[0]['vImgName'] != "" && $passengerDetails[0]['vImgName'] != "NONE") ?  $passengerDetails[0]['vImgName'] : "";
    $passengerDetails[0]['vPhone'] = '+'.$passengerDetails[0]['vPhoneCode'].$passengerDetails[0]['vPhone'];
    $returnArr['PassengerDetails'] = $passengerDetails[0];
    $TaxArr = getMemberCountryTax($iUserId,"Passenger");
    $fUserCountryTax1 = $TaxArr['fTax1'];  
    $fUserCountryTax2 = $TaxArr['fTax2'];
        
    $iDriverVehicleId = $tripData[0]['iDriverVehicleId'];
    $sql = "SELECT make.vMake, model.vTitle, dv.*  FROM `driver_vehicle` dv, make, model WHERE dv.iDriverVehicleId='" . $iDriverVehicleId . "' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId`";
    $vehicleDetailsArr = $obj->MySQLSelect($sql);
    $vehicleDetailsArr[0]['vModel'] = $vehicleDetailsArr[0]['vTitle'];
    //if ($eUserType == "Passenger" && $tripData[0]['eType'] == "UberX") {
        if($tripData[0]['eType'] == "UberX") {
            
            //$ALLOW_SERVICE_PROVIDER_AMOUNT = $generalobj->getConfigurations("configurations", "ALLOW_SERVICE_PROVIDER_AMOUNT");
            $ALLOW_SERVICE_PROVIDER_AMOUNT = $ePriceType == "Provider"? "Yes" :"No";            
            $fAmount = "0";
            if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes") {
                
                $sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $tripData[0]['iVehicleTypeId'] . "'";
                $serviceProData = $obj->MySQLSelect($sqlServicePro);
                
                $vehicleTypeData = get_value('vehicle_type', 'eFareType,fPricePerHour,fFixedFare', 'iVehicleTypeId', $tripData[0]['iVehicleTypeId']);
                if ($vehicleTypeData[0]['eFareType'] == "Fixed") {
                    $fAmount = $currencySymbol . $vehicleTypeData[0]['fFixedFare'];
                    } else if ($vehicleTypeData[0]['eFareType'] == "Hourly") {
                    $fAmount = $currencySymbol . $vehicleTypeData[0]['fPricePerHour'] . "/hour";
                }
                
                if (count($serviceProData) > 0) {
                    $fAmount = $serviceProData[0]['fAmount'];
                    $vVehicleFare = $fAmount * $priceRatio;
                    $vVehicleFare = formatNum($vVehicleFare);
                    if ($vehicleTypeData[0]['eFareType'] == "Fixed") {
                        $fAmount = $currencySymbol . $fAmount;
                        } else if ($vehicleTypeData[0]['eFareType'] == "Hourly") {
                        $fAmount = $currencySymbol . $fAmount . "/hour";
                    }
                }
                
                $vehicleDetailsArr[0]['fAmount'] = strval($fAmount);
            }
        }
    $returnArr['DriverCarDetails'] = $vehicleDetailsArr[0];
        
    if ($eUserType == "Passenger") {
        $tripFareDetailsArr = array();
        if($eFlatTrip == "Yes" && $iActive != "Canceled") {
            $i = 0;
            $tripFareDetailsArr[$i][$languageLabelsArr['LBL_FLAT_TRIP_FARE_TXT']] = $currencySymbol." ".$returnArr['fFlatTripPrice'];
            if ($fSurgePriceDiff > 0) {
                $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";
                $i++;
            }
            if ($fDiscount > 0) {
                $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";
                $i++;
            }
            if ($fWalletDebit > 0) {
                $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";
                $i++;
            }
            $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iFare']:"--";
      
        } elseif($eFlatTrip == "Yes" && $iActive == "Canceled") {
            $tripFareDetailsArr[0][$languageLabelsArr['LBL_Total_Fare']] = $currencySymbol." 0.00";
        } elseif($fCancelPrice > 0) {
            $tripFareDetailsArr[0][$languageLabelsArr['LBL_CANCELLATION_FEE']] = $currencySymbol.$returnArr['fCancelPrice'];
            $tripFareDetailsArr[1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = $currencySymbol.$returnArr['fCancelPrice'];
        } else {
            $i = 0;
            $countUfx = 0;
            if ($tripData[0]['eType'] == "UberX") {
                $tripFareDetailsArr[$i][$languageLabelsArr['LBL_VEHICLE_TYPE_SMALL_TXT']] = $returnArr['vVehicleCategory'] . "-" . $returnArr['vVehicleType'];
                $countUfx = 1;
            }
                
            if ($tripData[0]['eFareType'] == "Regular") {
                //$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vVehicleType . " " . $currencySymbol . $returnArr['iBaseFare'];
                $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = ($iActive != "Canceled")? $currencySymbol . $returnArr['iBaseFare']:"--";
                if ($countUfx == 1) {
                    $i++;
                }
                //$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $languageLabelsArr['LBL_KM_DISTANCE_TXT'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $DisplayDistanceTxt . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";
                $i++;
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";
                $i++;
            } else if ($tripData[0]['eFareType'] == "Fixed") {
                //  $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] = $currencySymbol . ($fTripGenerateFare - $fSurgePriceDiff - $fMinFareDiff);
                $SERVICE_COST = ($tripData[0]['iQty'] > 1)?$tripData[0]['iQty'].' X '.$currencySymbol . $vVehicleFare : $currencySymbol . $vVehicleFare;
                $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] =  ($iActive != "Canceled")?$SERVICE_COST:"--";
                if ($countUfx == 1) {
                    $i++;
                }
            } else if ($tripData[0]['eFareType'] == "Hourly") {
                $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";
                
                if ($countUfx == 1) {
                    $i++;
                }
            }
                
            if ($fVisitFee > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_VISIT_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fVisitFee']:"--";
                $i++;
            }
            if ($fMaterialFee > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MATERIAL_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMaterialFee']:"--";
                $i++;
            }
            if ($fMiscFee > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MISC_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMiscFee']:"--";
                $i++;
            }
            if ($fDriverDiscount > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROVIDER_DISCOUNT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDriverDiscount']:"--";
                $i++;
            }
                
            // print_r($tripFareDetailsArr);exit;
            // echo $tripData[0]['eFareType'];exit;
            if ($fSurgePriceDiff > 0) {
                $normalfare = $fTripGenerateFare - $fSurgePriceDiff - $fTax1 - $fTax2 - $fMinFareDiff;
                if($eTollSkipped == "No"){
                $normalfare = $fTripGenerateFare - $fSurgePriceDiff - $fTax1 - $fTax2 - $fMinFareDiff - $fTollPrice;
                }
                $normalfare = formatNum($normalfare);
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_NORMAL_FARE']] = ($iActive != "Canceled")?$currencySymbol . $normalfare:"--";
                $i++;
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";
                $i++;
            }
                
            if ($fMinFareDiff > 0) {
                //$minimamfare = $iBaseFare + $fPricePerKM + $fPricePerMin + $fMinFareDiff;
                $minimamfare = $fTripGenerateFare;
                if($eTollSkipped == "No"){
                $minimamfare = $fTripGenerateFare - $fTollPrice;
                }
                $minimamfare = formatNum($minimamfare);
                $tripFareDetailsArr[$i + 1][$currencySymbol . $minimamfare . " " . $languageLabelsArr['LBL_MINIMUM']] = $currencySymbol . $returnArr['fMinFareDiff'];
                $returnArr['TotalMinFare'] = ($iActive != "Canceled")?$minimamfare:"--";
                $i++;
            }

            if($eTollSkipped == "No") {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TOLL_PRICE_TOTAL']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTollPrice']:"--";
                $i++;   
            }
        
            if ($fDiscount > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";
                $i++;
            }
            if ($fWalletDebit > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";
                $i++;
            }
                
            /*if ($fTipPrice > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIP_AMOUNT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTipPrice']:"--";
                $i++;
            } */

            if($fTax1 > 0) {
               $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX1_TXT']." @ ". $fUserCountryTax1. " % "] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTax1']:"--";
               $i++;
            }
            if($fTax2 > 0) {
               $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX2_TXT']." @ ". $fUserCountryTax2. " % "] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTax2']:"--";
               $i++;
            }
                
            $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iFare']:"--";
        }    
            $returnArr['FareSubTotal'] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iOriginalFare']:"--";
            $returnArr['FareDetailsNewArr'] = $tripFareDetailsArr;
            $FareDetailsArr = array();
            foreach ($tripFareDetailsArr as $data) {
                $FareDetailsArr = array_merge($FareDetailsArr, $data);
            }
            $returnArr['FareDetailsArr'] = $FareDetailsArr;
            $returnArr['HistoryFareDetailsNewArr'] = $tripFareDetailsArr;
            if ($tripData[0]['eType'] == "UberX") {
                if($fCancelPrice == 0){
                    array_splice($returnArr['HistoryFareDetailsNewArr'], 0, 1);
                }
                    array_splice($returnArr['FareDetailsNewArr'], 0, 1); 
            }

    } else if($eUserType == "Driver") {
        $tripFareDetailsArr = array();
        if($eFlatTrip == "Yes"){
            $i = 0;         
            $tripFareDetailsArr[$i][$languageLabelsArr['LBL_FLAT_TRIP_FARE_TXT']] = $currencySymbol." ".$returnArr['fFlatTripPrice'];
            
            if($fSurgePriceDiff > 0) {   
              $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";
                    $i++;   
                }

            if ($fDiscount > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";
                $i++;
            }
            if ($fWalletDebit > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";
                $i++;
            }
        
        } else {
            $i = 0;
            $countUfx = 0;
            if ($tripData[0]['eType'] == "UberX") {
                $tripFareDetailsArr[$i][$languageLabelsArr['LBL_VEHICLE_TYPE_SMALL_TXT']] = $returnArr['vVehicleCategory'] . "-" . $returnArr['vVehicleType'];
                $countUfx = 1;
            }
            if ($tripData[0]['eFareType'] == "Regular") {
                //$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vVehicleType . " " . $currencySymbol . $returnArr['iBaseFare'];
                $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iBaseFare']:"--";
                if ($countUfx == 1) {
                    $i++;
                }
                //$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $languageLabelsArr['LBL_KM_DISTANCE_TXT'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $DisplayDistanceTxt . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";
                $i++;
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";
                $i++;
            } else if ($tripData[0]['eFareType'] == "Fixed") {
                //$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] = $currencySymbol . ($fTripGenerateFare - $fSurgePriceDiff - $fMinFareDiff);
                $SERVICE_COST = ($tripData[0]['iQty'] > 1)?$tripData[0]['iQty'].' X '.$currencySymbol . $vVehicleFare : $currencySymbol . $vVehicleFare;
                $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] =   ($iActive != "Canceled")?$SERVICE_COST:"--";                    
                if ($countUfx == 1) {
                    $i++;
                }
            } else if ($tripData[0]['eFareType'] == "Hourly") {
                $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";
                
                if ($countUfx == 1) {
                    $i++;
                }
            }
            
            if ($fVisitFee > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_VISIT_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fVisitFee']:"--";
                $i++;
            }
            if ($fMaterialFee > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MATERIAL_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMaterialFee']:"--";
                $i++;
            }
            if ($fMiscFee > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MISC_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMiscFee']:"--";
                $i++;
            }
            if ($fDriverDiscount > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROVIDER_DISCOUNT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDriverDiscount']:"--";
                $i++;
            }
            
            if ($fSurgePriceDiff > 0) {
                $normalfare = $fTripGenerateFare - $fSurgePriceDiff - $fTax1 - $fTax2 - $fMinFareDiff;
                if($eTollSkipped == "No"){
                $normalfare = $fTripGenerateFare - $fSurgePriceDiff - $fTax1 - $fTax2 - $fMinFareDiff - $fTollPrice;
                }
                $normalfare = formatNum($normalfare);
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_NORMAL_FARE']] = ($iActive != "Canceled")?$currencySymbol . $normalfare:"--";
                $i++;
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";
                $i++;   
            }

            if ($fMinFareDiff > 0) {
                //$minimamfare = $iBaseFare + $fPricePerKM + $fPricePerMin + $fMinFareDiff;
                $minimamfare = $fTripGenerateFare;
                if($eTollSkipped == "No"){
                $minimamfare = $fTripGenerateFare - $fTollPrice;
                }
                $minimamfare = formatNum($minimamfare);
                $tripFareDetailsArr[$i + 1][$currencySymbol . $minimamfare . " " . $languageLabelsArr['LBL_MINIMUM']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMinFareDiff']:"--";
                $returnArr['TotalMinFare'] = $minimamfare;
                $i++;
            }

            if($eTollSkipped == "No"){
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TOLL_PRICE_TOTAL']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTollPrice']:"--";
                $i++;   
            }
            
            if ($fDiscount > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";
                $i++;
            }

            if ($fWalletDebit > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";
                $i++;
            }

            if($fTax1 > 0){
               $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX1_TXT']." @ ". $fUserCountryTax1. " % "] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTax1']:"--";
               $i++;
            }

            if($fTax2 > 0){
               $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX2_TXT']." @ ". $fUserCountryTax2. " % "] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTax2']:"--";
               $i++;
            }
  
            /* if ($fDiscount > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";
                $i++;
                }
                if ($fWalletDebit > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";
                $i++;
            } */
            
            /*if ($fTipPrice > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIP_AMOUNT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTipPrice']:"--";
                $i++;
            } */
        }

        $returnArr['FareSubTotal'] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iOriginalFare']:"--";
        $returnArr['FareDetailsNewArr'] = $tripFareDetailsArr;
        $FareDetailsArr = array();
        foreach ($tripFareDetailsArr as $data) {
            $FareDetailsArr = array_merge($FareDetailsArr, $data);
        }
        $returnArr['FareDetailsArr'] = $FareDetailsArr;
        $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_Commision']] = ($iActive != "Canceled")?"-" . $currencySymbol . $returnArr['fCommision']:"--";
        $i++;
        $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_EARNED_AMOUNT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iFare']:"--";
        $returnArr['HistoryFareDetailsNewArr'] = $tripFareDetailsArr;
        
        if ($tripData[0]['eType'] == "UberX") {
            array_splice($returnArr['HistoryFareDetailsNewArr'], 0, 1);
        }
    } else {
        $tripFareDetailsArr = array();
        if($eFlatTrip == "Yes" && $iActive != "Canceled") {
            $i = 0;
            $tripFareDetailsArr[$i][$languageLabelsArr['LBL_FLAT_TRIP_FARE_TXT']] = $currencySymbol." ".$returnArr['fFlatTripPrice'];
            if ($fSurgePriceDiff > 0) {
                $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";
                $i++;
            }
            if ($fDiscount > 0) {
                $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";
                $i++;
            }
            if ($fWalletDebit > 0) {
                $tripFareDetailsArr[$i+1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";
                $i++;
            }
      
        } else if($eFlatTrip == "Yes" && $iActive == "Canceled") {
            $tripFareDetailsArr[0][$languageLabelsArr['LBL_Total_Fare']] = $currencySymbol." 0.00";
        } else if($fCancelPrice > 0) {
            $tripFareDetailsArr[0][$languageLabelsArr['LBL_CANCELLATION_FEE']] = $currencySymbol.$returnArr['fCancelPrice'];
        } else {
            $i = 0;
            $countUfx = 0;
            if ($tripData[0]['eType'] == "UberX") {
                $tripFareDetailsArr[$i][$languageLabelsArr['LBL_VEHICLE_TYPE_SMALL_TXT']] = $returnArr['vVehicleCategory'] . "-" . $returnArr['vVehicleType'];
                $countUfx = 1;
            }
            if ($tripData[0]['eFareType'] == "Regular") {
                //$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vVehicleType . " " . $currencySymbol . $returnArr['iBaseFare'];
                $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iBaseFare']:"--";
                if ($countUfx == 1) {
                    $i++;
                }
                //$tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $languageLabelsArr['LBL_KM_DISTANCE_TXT'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_DISTANCE_TXT'] . " (" . $returnArr['fDistance'] . " " . $DisplayDistanceTxt . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfDistance']:"--";
                $i++;
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";
                $i++;
            } else if ($tripData[0]['eFareType'] == "Fixed") {
                //$tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] = $currencySymbol . ($fTripGenerateFare - $fSurgePriceDiff - $fMinFareDiff);
                $SERVICE_COST = ($tripData[0]['iQty'] > 1)?$tripData[0]['iQty'].' X '.$currencySymbol . $vVehicleFare : $currencySymbol . $vVehicleFare;
                $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_SERVICE_COST']] =   ($iActive != "Canceled")?$SERVICE_COST:"--";                    
                if ($countUfx == 1) {
                    $i++;
                }
            } else if ($tripData[0]['eFareType'] == "Hourly") {
                $tripFareDetailsArr[$i + $countUfx][$languageLabelsArr['LBL_TIME_TXT'] . " (" . $returnArr['TripTimeInMinutes'] . ")"] = ($iActive != "Canceled")?$currencySymbol . $returnArr['TripFareOfMinutes']:"--";
                
                if ($countUfx == 1) {
                    $i++;
                }
            }
            
            if ($fVisitFee > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_VISIT_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fVisitFee']:"--";
                $i++;
            }
            if ($fMaterialFee > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MATERIAL_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMaterialFee']:"--";
                $i++;
            }
            if ($fMiscFee > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_MISC_FEE']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMiscFee']:"--";
                $i++;
            }
            if ($fDriverDiscount > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROVIDER_DISCOUNT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDriverDiscount']:"--";
                $i++;
            }
            
            if ($fSurgePriceDiff > 0) {
                $normalfare = $fTripGenerateFare - $fSurgePriceDiff - $fTax1 - $fTax2 - $fMinFareDiff;
                if($eTollSkipped == "No"){
                $normalfare = $fTripGenerateFare - $fSurgePriceDiff - $fTax1 - $fTax2 - $fMinFareDiff - $fTollPrice;
                }
                $normalfare = formatNum($normalfare);
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_NORMAL_FARE']] = ($iActive != "Canceled")?$currencySymbol . $normalfare:"--";
                $i++;
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_SURGE'] . " x" . $SurgePriceFactor] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fSurgePriceDiff']:"--";
                $i++;   
            }

            if ($fMinFareDiff > 0) {
                //$minimamfare = $iBaseFare + $fPricePerKM + $fPricePerMin + $fMinFareDiff;
                $minimamfare = $fTripGenerateFare;
                if($eTollSkipped == "No"){
                $minimamfare = $fTripGenerateFare - $fTollPrice;
                }
                $minimamfare = formatNum($minimamfare);
                $tripFareDetailsArr[$i + 1][$currencySymbol . $minimamfare . " " . $languageLabelsArr['LBL_MINIMUM']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fMinFareDiff']:"--";
                $returnArr['TotalMinFare'] = $minimamfare;
                $i++;
            }

            if($eTollSkipped == "No"){
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TOLL_PRICE_TOTAL']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTollPrice']:"--";
                $i++;   
            }
            
            if ($fDiscount > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";
                $i++;
            }

            if ($fWalletDebit > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";
                $i++;
            }

            if($fTax1 > 0){
               $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX1_TXT']." @ ". $fUserCountryTax1. " % "] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTax1']:"--";
               $i++;
            }

            if($fTax2 > 0){
               $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TAX2_TXT']." @ ". $fUserCountryTax2. " % "] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTax2']:"--";
               $i++;
            }
  
            /* if ($fDiscount > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fDiscount']:"--";
                $i++;
                }
                if ($fWalletDebit > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = ($iActive != "Canceled")?"- " . $currencySymbol . $returnArr['fWalletDebit']:"--";
                $i++;
            } */
            
            /*if ($fTipPrice > 0) {
                $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_TIP_AMOUNT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['fTipPrice']:"--";
                $i++;
            } */
        }
        if($fCancelPrice > 0){    
            $returnArr['FareSubTotal'] = $currencySymbol.$returnArr['fCancelPrice'];
        } else {
            $returnArr['FareSubTotal'] = $currencySymbol . $returnArr['iOriginalFare'];
        }
        $returnArr['FareDetailsNewArr'] = $tripFareDetailsArr;
        $FareDetailsArr = array();
        foreach ($tripFareDetailsArr as $data) {
            $FareDetailsArr = array_merge($FareDetailsArr, $data);
        }
        $returnArr['FareDetailsArr'] = $FareDetailsArr;

        $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_Commision']] = ($iActive != "Canceled")?"-" . $currencySymbol . $returnArr['fCommision']:$currencySymbol . $returnArr['fCommision'];
        $i++;

        $tripFareDetailsArr[$i + 1][$languageLabelsArr['LBL_EARNED_AMOUNT']] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iFare']:"--";

        $returnArr['HistoryFareDetailsNewArr'] = $tripFareDetailsArr;
        
        if ($tripData[0]['eType'] == "UberX") {
            if($fCancelPrice == 0){
            array_splice($returnArr['HistoryFareDetailsNewArr'], 0, 1);
            }
        }
    }
    /*      $returnArr['FareSubTotal'] = ($iActive != "Canceled")?$currencySymbol . $returnArr['iOriginalFare']:"--";*/
        //passengertripfaredetails
        
        $HistoryFareDetailsArr = array();
        foreach ($tripFareDetailsArr as $inner) {
            $HistoryFareDetailsArr = array_merge($HistoryFareDetailsArr, $inner);
        }
        $returnArr['HistoryFareDetailsArr'] = $HistoryFareDetailsArr;
        
        
        //drivertripfarehistorydetails
        //echo "<pre>";print_r($returnArr);exit;
        return $returnArr;
}
############### Invoice Price For Web   ###################################################################   


  

}


/* Function to clean string when submit form */
?>