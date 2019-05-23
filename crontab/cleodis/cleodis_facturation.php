<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
if(exec('whoami')!="apache"){
	print_r("************************Penser à vérifier les droits de TEMP***********************************");
}

ATF::facturation()->facturationMensuelle();
if($_SERVER["argv"][1] !== "bdomplus") ATF::facturation()->facturationMensuelleRestitution();

?>
