<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");

if($_SERVER["argv"][1] == "2tmanagement"){
	ATF::hotline()->acceptationAutomatique(1);
}else{
	ATF::hotline()->acceptationAutomatique();
}

