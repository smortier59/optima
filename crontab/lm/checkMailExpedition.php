<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");

$mail = 'suivicommandes@abonnement.leroymerlin.fr';
$host = "outlook.office365.com"; 
$port = 993;
$password = "m5q9Z6=F_*Wm";
ATF::affaire()->checkMailBoxExpeditionCommande($mail, $host, $port, $password);

?>