<?php 
error_reporting(1);
session_start();
defined( '_TEXEC' ) or die( 'Restricted access' );
$parts = explode( DS, TPATH_BASE );
define( 'TPATH_ROOT',			TPATH_BASE );
define( 'TPATH_ADMINISTRATOR', 	TPATH_ROOT.DS.'fdadmin' );
define( 'TPATH_LIBRARIES', 		TPATH_ROOT.DS.'libraries' );
define( 'TPATH_CLASS_APP', 		TPATH_ROOT.DS.'libraries'.DS.'application'.DS);
define( 'TPATH_TEMPLATES', 		TPATH_ROOT.DS.'templates' );
define( 'TPATH_TEMPLATES_C', 		TPATH_ROOT.  DS.'templates_c' );
define( 'TPATH_MODULES', 		TPATH_ROOT.DS.'modules' );
define( 'TPATH_ADMIN_TEMPLATES', 		TPATH_ADMINISTRATOR.DS.'templates' );
define( 'TPATH_ADMIN_TEMPLATES_C', 		TPATH_ADMINISTRATOR.DS.'templates_c' );
define( 'TPATH_ADMIN_MODULES', 		TPATH_ADMINISTRATOR.DS.'modules' );
define( 'TPATH_CLASS_DATABASE', 		TPATH_ROOT.DS.'libraries'.DS.'database/' );
define( 'TPATH_CLASS_GEN', 		TPATH_ROOT.DS.'libraries'.DS.'general/' );   
define( 'TPATH_PUBLIC_HTML', 	TPATH_ROOT.DS.'public_html' );
define( 'TPATH_CACHE', 		TPATH_PUBLIC_HTML.DS.'cache' );

define( 'TPATH_UPLOADS', 	TPATH_ROOT.DS.'public_html/uploads' );
define( 'TPATH_CLASS_RIDES', 		TPATH_ROOT.DS.'libraries'.DS.'rides/' );

if($_SERVER["HTTP_HOST"] == "192.168.0.75")
{
	define( 'TSITE_SERVER','localhost');
	define( 'TSITE_DB','eswtech_food_Demo');
	define( 'TSITE_USERNAME','eswtech_projects');
	define( 'TSITE_PASS','proj@12345');
}
else
{
	define( 'TSITE_SERVER','localhost');
	define( 'TSITE_DB','eswtech_food_Demo');
	define( 'TSITE_USERNAME','eswtech_projects');
	define( 'TSITE_PASS','proj@12345');
}

if(!isset($obj))
{
	require_once(TPATH_CLASS_DATABASE."class.dbquery.php");	$obj=	new DBConnection(TSITE_SERVER, TSITE_DB, TSITE_USERNAME,TSITE_PASS);
}
if(!isset($generalobj)){
	require_once(TPATH_CLASS_GEN."class.general.php");
	$generalobj = new General();	
}
$generalobj->xss_cleaner_all();
$generalobj->getGeneralVar();

if(!isset($ridesobj)){
	require_once(TPATH_CLASS_RIDES."class.rides.php");
	$ridesobj = new Rides();
} 

#Payment Option Settings 
#
define('PAYMENT_OPTION',$PAYMENT_OPTION);
#define('PAYMENT_OPTION','PayPal');
#define('PAYMENT_OPTION','Manual');
#define('PAYMENT_OPTION','Contact');
define('SITE_COLOR','#52C426');
?>
