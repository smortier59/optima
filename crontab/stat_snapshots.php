<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

//Stock les snapshots d'aujourd'hui
ATF::stat_snap()->storeAllValues();
?>