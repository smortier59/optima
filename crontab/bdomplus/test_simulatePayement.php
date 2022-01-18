<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");


$refMandate = $_SERVER['argv'][2];
$montant = $_SERVER['argv'][3];


ATF::slimpay()->simulateIssue($refMandate, $montant);


?>