<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

//supprimer les tracabilités d'une date > 4mois
ATF::tracabilite()->deleteTracePerime();
?>