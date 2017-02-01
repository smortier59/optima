<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");

$mail = 'suivicommandes@abonnement.leroymerlin.fr';
$host = "outlook.office365.com";
$port = 993;
$password = __SUIVI_COMMANDE_MAILBOX__;
ATF::affaire()->checkMailBoxExpeditionCommande($mail, $host, $port, $password);

?>