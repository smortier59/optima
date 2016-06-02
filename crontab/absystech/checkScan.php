<?
define("__BYPASS__",true);
$_SERVER["argv"][1] = "absystech";
include(dirname(__FILE__)."/../../global.inc.php");

//Scanner
$mail = 'scanner@absystech.fr';
$host = "zimbra.absystech.net"; 
$port = 143;
$password = "sc4nn3r!";
ATF::scanner()->checkMailBox($mail, $host, $port, $password, "scanner");


// Factures fournisseurs
$mail = 'factures.fournisseurs@absystech.fr';
$host = "zimbra.absystech.net"; 
$port = 143;
$password = "az78qs45!";
ATF::scanner()->checkMailBox($mail, $host, $port, $password, "facture_fournisseur");


?>