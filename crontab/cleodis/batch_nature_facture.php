<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);

$q= "SELECT *
	FROM `facture`
	WHERE `type_facture` != 'refi'
	AND date_periode_debut IS NOT NULL
	AND date_periode_fin IS NOT NULL";
$data = ATF::db()->sql2array($q);

foreach ($data as $key => $value) {
	$commande = ATF::commande()->select($value["id_commande"]);

	$date_debut_contrat = strtotime($commande["date_debut"]);
	$date_fin_contrat = strtotime($commande["date_evolution"]);

	$date_debut_facture = strtotime($value["date_periode_debut"]);
	$date_fin_facture = strtotime($value["date_periode_fin"]);


	if($date_debut_facture < $date_debut_contrat){
		//prorata
		$nature = "prorata";
	}elseif($date_debut_facture > $date_fin_contrat){
		//Prolongation
		$nature = "prolongation";
	}else{
		//Engagement
		$nature = "engagement";
	}
	ATF::facture()->u(array("id_facture"=>$value["id_facture"], "nature"=>$nature));
}