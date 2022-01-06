<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$q= "SELECT *
	FROM `facture`
	WHERE `ref_externe` IS NULL
    ORDER BY id_facture ASC";
$data = ATF::db()->sql2array($q);

foreach ($data as $key => $value) {
    $ref_ext = ATF::facture()->getRefExterne();

    ATF::facture()->u(array("id_facture"=> $value["id_facture"], "ref_externe"=> $ref_ext));
}