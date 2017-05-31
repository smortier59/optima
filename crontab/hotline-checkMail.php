<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../global.inc.php");

$mail = 'optima-hotline@absystech.net';
$host = "zimbra.absystech.net";
$port = 143;
$password = "az78qs45";
ATF::hotline()->checkMailBox($mail, $host, $port, $password);

?>
