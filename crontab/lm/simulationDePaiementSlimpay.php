<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");


$refMandate = $_SERVER['argv'][2];


ATF::slimpay()->simulateIssue($refMandate);


?>