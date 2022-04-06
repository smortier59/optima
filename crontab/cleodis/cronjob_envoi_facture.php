<?
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");

$date = date("Y-m-d");
if ($_SERVER["argv"][2]) {
    $date = $_SERVER["argv"][2];
}

ATF::facture()->sendFactureMail($date);

?>
