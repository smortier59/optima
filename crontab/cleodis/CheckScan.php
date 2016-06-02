<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");

/*
$mail = 'scanner@absystech.fr';
$host = "zimbra.absystech.net"; 
$port = 143;
$password = "sc4nn3r!";
*/

$mail = 'scanner@cleodis.fr';
$host = "lithium.absystech.net"; 
$port = 143;
$password = "sdfis8HDS!";
ATF::scanner()->checkMailBox($mail, $host, $port, $password);

?>
