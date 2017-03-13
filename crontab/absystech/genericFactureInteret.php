<?php
define("__BYPASS__",true);

include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$dataPath = __DATA_PATH__.$_SERVER["argv"][1]."/";

ATF::facture_paiement()->q->whereIsNotNull("montant_interet");
$facture_paiements = ATF::facture_paiement()->select_all();

foreach ($facture_paiements as $key => $value) {
	$data = ATF::pdf()->generic("factureInteret",$value["facture_paiement.id_facture_paiement"],true);     
    ATF::facture_paiement()->store($s,$value["facture_paiement.id_facture_paiement"],"factureInteret",$data,false);
}
