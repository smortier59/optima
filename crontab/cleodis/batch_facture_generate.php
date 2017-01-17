<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodisbe";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$req = " SELECT id_facture, date FROM facture WHERE `date` BETWEEN '2016-07-01' AND '2016-07-31' ";
$factures = ATF::db()->sql2array($req);
foreach ($factures as $key => $value) {
  $fact['id'] = $value['id_facture'];
  ATF::facture()->genererPdf($fact);
  //log::logger('id facture -- '.$value['id_facture'],"ccharlier");
}
?>