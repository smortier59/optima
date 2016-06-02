<?php
/**
* Préinitialisation du framework
* @package ATF
* @version ATF 5 2009-01-01
* @author Yann GAUTHERON <ygautheron@absystech.fr> Jérémie GWIAZDOWSKI <jgw@absystech.fr>
*/
require_once(dirname(__FILE__)."/libs/ATF/gate.class.php");
require_once(dirname(__FILE__)."/libs/ATF/ATF.class.php");

/* Timezone selected */
date_default_timezone_set('Europe/Paris');

/* Initialisation des variables statiques */
@define("__ABSOLUTE_PATH__",dirname(__FILE__)."/../../");
define("__ATF_PATH__",dirname(__FILE__)."/");
@define("__ABSOLUTE_WEB_URI__","http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"]);
if (@$_SERVER["HTTP_HOST"]) {
	@$url = ($_SERVER['HTTPS']=="on"?"https":"http")."://".$_SERVER["HTTP_HOST"]."/";
}
@define("__ABSOLUTE_WEB_PATH__",$url?$url:__MANUAL_WEB_PATH__);
define("__TEMPLATE__",basename(substr($_SERVER["PHP_SELF"],0,strpos($_SERVER["PHP_SELF"],'.'))));
define("__INCLUDE_PATH__",__ABSOLUTE_PATH__."includes/");
define("__LIBS_PATH__",__ABSOLUTE_PATH__."libs/");
define("__DATA_PATH__",__ABSOLUTE_PATH__."../data/");
define("__TEMP_PATH__",__ABSOLUTE_PATH__."../temp/");
define("__SMARTY_PATH__",__ATF_PATH__.'libs/Smarty/');
// POur la version local d'Optima
// define("__SMARTY_PATH__",__ATF_PATH__.'libs/smarty/libs/');
define("__TRASH_PATH__",__ABSOLUTE_PATH__."../trash/");
define("__TEST_PATH__",__ABSOLUTE_PATH__."test/");
define("__PDF_PATH__",__ABSOLUTE_PATH__."www/images_pdf/");
ini_set("include_path",ini_get("include_path").":".__LIBS_PATH__.":".__ATF_PATH__."libs/");
define("__FONT_PATH__",__ABSOLUTE_PATH__."fonts/");
define("__LOG_PATH__","/var/log/ATF/");
@define("__SUBDOMAIN__",substr($_SERVER["HTTP_HOST"],0,strpos($_SERVER["HTTP_HOST"],'.')));
define("__DEFAULT_LANGUAGE__","fr");
define("__SESSION_NAME__","ATFSess");

/* Constantes secondaires */
define("__LIMITE_LOCALISATION__",650);

/* Chemins de recherche des fichiers includes */
ATF::addPath("include",__INCLUDE_PATH__.'/{codename}/');
ATF::addPath("include",__INCLUDE_PATH__);
ATF::addPath("include",__ATF_PATH__."/libs/ATF/");
ATF::addPath("include",__ATF_PATH__."/includes/");

/* Chemins de recherche des fichiers templates */
ATF::addPath("template",__ATF_PATH__."/templates/");
if (isset($_SESSION["user"]->website_codename)) {
	ATF::addPath("template", __ABSOLUTE_PATH__."/templates/".$_SESSION["user"]->website_codename."/");
}
ATF::addPath("template", __ABSOLUTE_PATH__."/templates/");

/**
* Autochargement de classes
* @param string $class Nom de la classe
* @author Yann GAUTHERON <ygautheron@absystech.fr>
*/
function ATF_autoload($class) {
//if (class_exists("log"))log::logger($class." ".$_SESSION["user"]->website_codename." activity=".$_SESSION['date_activity'],ygautheron);
	// Gestion des Namespaces
	if (strpos($class,"\\")!==false) {
		$class = "namespaces/".str_replace('\\','/',$class);
	}

//if (class_exists("log")) log::logger($class." "."codename=".$_SESSION["user"]->website_codename." // ".__WEBSITE_CODENAME__." // ".ATF::$codename,ygautheron);
//if (class_exists("log")) log::logger("autoload ".$class,"ygautheron");
	$codename = ATF::$codename;
	if (isset($_SESSION["user"]) && $_SESSION["user"]->website_codename) {
		$codename = $_SESSION["user"]->website_codename;
	}
	foreach (ATF::getPaths('include') as $path) {
		// On ajoute le path du codename de session
		$path = str_replace("{codename}",$codename,$path);

		// En supposant que la classe est suffixée
		$class_explode = explode("_",$class);
		$class_codename = array_pop($class_explode);
		$path_ = $path.$class_codename."/".implode("_",$class_explode);
		if (file_exists($path_.'.class.php')) {
			// En supposant que la classe est suffixée
			return require_once $path_.'.class.php';
		} else if (file_exists($path.$class.'.class.php')) {
			// Sinon on cherche dans le $path
			return require_once $path.$class.'.class.php';
		} else {
			// Le nom de fichier doit être exactement le même pour ces classes particulières
//			if (in_array($class,array(ATF::$usrClass,ATF::$controllerClass,ATF::$motherClass))) {
//				continue;
//			}

			if (ATF::$codename) {
				$class_ = str_replace("_".ATF::$codename,"",$class);
			} elseif(isset($_SESSION["user"]) && $_SESSION["user"]->website_codename) {
				$class_ = str_replace("_".$_SESSION["user"]->website_codename,"",$class);
			} else {
				continue;
			}
//if (class_exists("log")) log::logger($class." ".$class_,ygautheron);
			// Dernier recours, on enlève le codename dans le nom pour trouver le nom de fichier !
			if (file_exists($path.$class_.'.class.php')) {
				return require_once $path.$class_.'.class.php';
			}
		}
	}
}
spl_autoload_register('ATF_autoload');
?>
