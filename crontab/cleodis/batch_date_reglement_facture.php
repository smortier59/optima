<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


$q= "SELECT *
	FROM `facture`
	WHERE date_paiement IS NULL";
$facture_sans_date_paiement = ATF::db()->sql2array($q);

foreach ($facture_sans_date_paiement as $key => $value) {
    try {
        ATF::facture()->_updateDate(array(
            "schema" => $_SERVER["argv"][1],
            "id_facture" => $value["id_facture"],
            "key" => "date_paiement",
            "value" => "2000-01-01"
        ));
    } catch (errorATF $th) {

    }
}

?>