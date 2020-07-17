<?php  
if( !isset($obj) ) 
{
    require_once(TPATH_CLASS . "class.dbquery.php");
    $obj = new DBConnection(TSITE_SERVER, TSITE_DB, TSITE_USERNAME, TSITE_PASS);
}

if( !isset($generalobj) ) 
{
    require_once(TPATH_CLASS . "class.general.php");
    $generalobj = new General();
}

define("SITE_TYPE", "Live");
define("RIIDE_LATER", "YES");
define("PROMO_CODE", "YES");
$generalobj->getGeneralVarAll();
$generalConfigArr = $generalobj->getGeneralVarAll_Array();
if( $_SERVER["HTTP_HOST"] == "192.168.1.112" ) 
{
}
else
{
    exit();
}


