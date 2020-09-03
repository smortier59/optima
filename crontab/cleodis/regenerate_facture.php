<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__."cleodis/";

ATF::facture()->q->reset()->addAllFields('facture')->where("facture.date","2020-10-01");
$factures = ATF::facture()->select_all();

echo count($factures);

foreach ($factures as $key => $value) {
  ATF::facture()->move_files($value["facture.id_facture"]);
}
?>