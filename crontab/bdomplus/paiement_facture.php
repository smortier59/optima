<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$log_file = "bdomplus/paiement_facture".date("Ymd");

$periode = $_SERVER["argv"][2];


$q = 'SELECT * FROM `facture` WHERE `date` LIKE "%'.$periode.'%" AND etat="impayee" AND rejet="non_rejet"';
$data = ATF::db()->sql2array($q);


foreach($data as $key => $value){
	ATF::facture()->updateDate(
		array(
			"id_facture"=> $value["id_facture"],
			"key"=> "date_paiement",
			"value" => $periode."-03"
		)
	);

}