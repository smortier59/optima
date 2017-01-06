<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
if(exec('whoami')!="apache"){
	print_r("************************Penser à vérifier les droits de TEMP***********************************");
}

ATF::facturation()->facturationMensuelle(false, $_SERVER["argv"][2]);
ATF::facturation()->facturationMensuelleRestitution();?>
