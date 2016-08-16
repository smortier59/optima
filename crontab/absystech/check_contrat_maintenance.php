<?
/** Mise à jour des infos contrat maintenance d'une société
* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
*/
define("__BYPASS__",true);
//$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../../global.inc.php");

ATF::societe()->check_contrat_maintenance();
?>