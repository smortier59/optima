<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");

$id_slimpay = $_SERVER['argv'][2];

$statut = ATF::slimpay()->getStatutDebit($id_slimpay);

print_r($statut);

?>