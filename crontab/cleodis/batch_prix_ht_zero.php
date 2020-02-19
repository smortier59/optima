<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "cleodis";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


$q = "SELECT id_devis, id_affaire, ref FROM devis WHERE prix = '0.00'";
$devis = ATF::db()->sql2array($q);


foreach ($devis as $key => $value) {
	$prix = 0;

	//On recupere les loyers de l'affaire
	ATF::loyer()->q->reset()->where("id_affaire", $value["id_affaire"]);
	$loyers = ATF::loyer()->select_all();

	if($loyers){

		foreach ($loyers as $kl => $vl) {
			$prix += $vl["duree"] * ($vl["loyer"]+ $vl["frais_de_gestion"]);
		}

		ATF::devis()->u(array("id_devis"=> $value["id_devis"], "prix"=> $prix));

		log::logger("MAJ devis ".$value["ref"]." -> ".$prix, "batch_prix_devis");
	}
}