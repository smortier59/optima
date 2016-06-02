<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "att";
include(dirname(__FILE__)."/../../global.inc.php");

$mail = 'scanner@absystech-telecom.fr';
$host = "zimbra.absystech.net"; 
$port = 143;
$password = "sc4nn3r!";
ATF::scanner()->checkMailBox($mail, $host, $port, $password);

?>