<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
$_SERVER["argv"][1] = "atoutcoms";



$q = "SELECT * FROM `TABLE 156`";
$soc_contacts = ATF::db()->sql2array($q);


