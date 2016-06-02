<?
/**
* Script de debug, uniquement poour le dveloppement
*/
include(dirname(__FILE__)."/../global.inc.php");
if (__DEV__===true) {
	switch ($_GET["cmd"]) {
		case "flush":
			ATF::_s("ATF","");
			//unset($_SESSION["ATF"]);
			ATF::_s("pager","");
			//unset($_SESSION["pager"]);
			ATF::_s("tpl_exists","");
			//unset($_SESSION["tpl_exists"]);
			ATF::getEnv()->commitSession();
			echo "Singletons supprimés, session restante : ";
			print_r($_SESSION);
			break;
		case "phpinfo":
			phpinfo();
			break;
	}
} else {
	echo "DEBUG NON ADMIS EN PRODUCTION";
}
?>