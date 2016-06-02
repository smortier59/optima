<?
define("__BYPASS__",true);
include("../global.inc.php");

foreach (ATF::module()->select_all() as $i) {
	if (file_exists($file = __ABSOLUTE_PATH__."www/images/icones/module_icone_".$i["id_module"].".png")) {
		rename($file,__ABSOLUTE_PATH__."www/images/module/16/".$i["module"].".png");
		echo "$file moved\n";
	}
	if (file_exists($file = __ABSOLUTE_PATH__."www/images/icones/module_icone_big_".$i["id_module"].".png")) {
		rename($file,__ABSOLUTE_PATH__."www/images/module/48/".$i["module"].".png");
		echo "$file moved\n";
	}
}
?>