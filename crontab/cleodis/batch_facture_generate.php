<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


$factures = array( '1508001-4'
,'1604001-2'
,'1606001-1'
,'1404040-21'
,'1407020-21'
,'1502041-11'
,'1505002-10'
,'1506001-13'
,'1602004-1');

foreach ($factures as $key => $value) {
  $fact['id'] = $value;
  ATF::facture()->genererPdf($fact);
  log::logger('id facture -- '.$value,"ccharlier");
}
?>