<?
/** Transformation des interventions en tickets hotline
* @author J�r�mie Gwiazdowski <jgw@absystech.fr>
*/
include("/home/absystech/optima/core/global.inc.php");

$table = $_SERVER["argv"][1];
$methode = $_SERVER["argv"][2];
ATF::$table()->$methode();
?>