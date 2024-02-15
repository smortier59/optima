<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
if(exec('whoami')!="apache"){
	print_r("************************Penser à vérifier les droits de TEMP***********************************");
}
$periodeStart = ($_SERVER["argv"][2] && $_SERVER["argv"][2] !== "null")  ? $_SERVER["argv"][2] : null;
$periodeEnd = $_SERVER["argv"][3] ? $_SERVER["argv"][3] : null;

ATF::facturation_attente()->envoye_facturation($periodeStart, $periodeEnd);
?>
