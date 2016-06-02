<?
/** Upgrade des priorités hotline
* @author Jérémie Gwiazdowski <jgw@absystech.fr>
*/
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

//pour lancer le script : php hotline_priorite.php absystech
ATF::hotline()->upgradePriorite();
?>