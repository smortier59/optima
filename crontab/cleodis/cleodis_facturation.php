<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
if(exec('whoami')!="apache"){
	print_r("************************Penser à vérifier les droits de TEMP***********************************");
}
if ($_SERVER["argv"][2]) {
	ATF::facturation()->facturationMensuelle(false, $_SERVER["argv"][2]);
} else {
	ATF::facturation()->facturationMensuelle();
}


if($_SERVER["argv"][1] !== "bdomplus" && $_SERVER["argv"][1] !== "go_abonnement") {
	if ($_SERVER["argv"][2]) {
		ATF::facturation()->facturationMensuelleRestitution(false, $_SERVER["argv"][2]);
	} else {
		ATF::facturation()->facturationMensuelleRestitution();
	}
}

?>
