<?
/** Routine permetant d'envoyer un mail de digest aux users ayant eu un suivi dans les 24h
* @author Mathieu TRIBOUILLARD <mtribouillard@absystech.fr>
*/
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

ATF::suivi()->digest();
?>