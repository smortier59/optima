<?
/** Facturation des tickets hotline
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
*/
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

//pour lancer le script : php inter2hotline.php absystech
ATF::hotline()->traitement_facturation($_SESSION);
?>