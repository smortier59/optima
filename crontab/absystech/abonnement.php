<?
/** Remplir la table abonnement
* @author Nicolas BERTEMONT <nbertemont@absystech.fr>
*/
define("__BYPASS__",true);
$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../../global.inc.php");

//pour lancer le script : php abonnement.php absystech
ATF::abonnement()->insertMassif();
?>