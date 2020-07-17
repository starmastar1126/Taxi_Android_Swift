<?php 
include_once('../common.php');

if (!isset($generalobjAdmin)) {
    require_once(TPATH_CLASS . "class.general_admin.php");
    $generalobjAdmin = new General_admin();
}
$generalobjAdmin->check_member_login();

if (isset($_REQUEST['name'])) {

    if ($_REQUEST['name'] != "") {
        if ($_REQUEST['name'] == 'Driver') {

            $user_name = $_REQUEST['name'];
            $sql = "SELECT iDriverId,vName,vLastName FROM register_driver";
            $db_comp = $obj->MySQLSelect($sql);
            echo "<option value=''>Search By Driver type</option>";
            for ($i = 0; $i < count($db_comp); $i++) {
                echo "<option value=" . $db_comp[$i]['iDriverId'] . ">" . $generalobjAdmin->clearName($db_comp[$i]['vName'] . " " . $db_comp[$i]['vLastName']) . "</option>";
            }
            exit;
        } else {
            $sql = "SELECT iUserId,vName,vLastName FROM register_user ";
            $db_register_user = $obj->MySQLSelect($sql);

            echo "<option value=''>Search By Passanger type</option>";
            for ($i = 0; $i < count($db_register_user); $i++) {
                echo "<option value=" . $db_register_user[$i]['iUserId'] . ">" . $generalobjAdmin->clearName($db_register_user[$i]['vName'] . " " . $db_register_user[$i]['vLastName']) . "</option>";
            }
            exit;
        }
    }
}
?>