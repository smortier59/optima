<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

//pour lancer le script : php generation_ci.php absystech

//génération du fichier de gestion des contraintes d'intégritées
$t = microtime(true);
$memory = memory_get_usage();
echo $t."/".$memory."\n";
//ATF::db()->recup_ci($_SERVER['argv'][1]);
$r = ATF::db()->fetch_foreign_keys();
$memory = memory_get_usage() - $memory;
$t = microtime(true) - $t;
//print_r($r);
echo $t."/".$memory."\n";


?>