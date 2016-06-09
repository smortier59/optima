<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);


	$q = "SELECT *  FROM import_fournisseur";
	$fournisseurs = ATF::db()->sql2array($q);	

	foreach ($fournisseurs as $key => $value) {
		ATF::societe()->i(array("societe"=>$value["societe"],
								"nom_commercial"=>$value["nom_commercial"]
						));
	}

	$q = "SELECT *  FROM import_fabriquant";
	$fabriquants = ATF::db()->sql2array($q);	

	foreach ($fabriquants as $key => $value) {
		ATF::fabriquant()->i(array("fabriquant"=>$value["fabriquant"]));
	}


	

$q = "SELECT *  FROM import_pack";
$packs = ATF::db()->sql2array($q);	

foreach ($packs as $key => $value) {
	$id_pack = ATF::pack_produit()->i(array("libelle"=>$value["libelle"],
								 "libelle_ecran_magasin"=>$value["libelle_ecran_magasin"],
								 "popup"=>$value["popup"],
								 "description"=>$value["description"],
								 "type_pack"=>"abo",
								 "ref_lm_principale"=>$value["ref_lm_principale"],
								 "etat"=>$value["etat"],
								 "fin_formulaire"=>$value["fin_formulaire"],
								 "service_inclus"=>$value["service_inclus"]
						   ));
	$packs = array("id_pack"=>$id_pack,
				   "id_produit"=>$value["id_produit"]
				  );

	
}

$q = "SELECT *  FROM import_produit";
$produits = ATF::db()->sql2array($q);
foreach ($produits as $key => $value) {
	$id_produit = ATF::produit()->i(array("produit"=>$value["produit"],
										  "url_produit"=>$value["url_produit"],
										  //""=>$value["prix_achat_ht"],
										  "id_fabriquant"=>$value["id_fabriquant"],
										  "etat"=>$value["etat"],
										  "tva_prix_achat"=>$value["tva_prix_achat"],
										  "id_pack_produit"=>$value["id_pack_produit"],
										  "ref_lm"=>$value["ref_lm"],
										  "nature"=>$value["nature"],
										  "libelle_a_revoyer_lm"=>$value["libelle_a_revoyer_lm"],
										  "controle_fournisseur"=>$value["controle_fournisseur"],
										  "declencheur_mep"=>$value["declencheur_mep"],
										  "min"=>$value["min"],
										  "max"=>$value["max"],
										  "defaut"=>$value["defaut"],
										  "mode_paiement"=>$value["mode_paiement"],
										  "tva_loyer"=>$value["tva_loyer"],
										  "description"=>$value["description"],
										  "ordre"=>$value["ordre"],
										  "afficher"=>$value["afficher"]
									));
}


foreach ($packs as $key => $value) {
	ATF::pack_produit()->u(array("id_pack_produit"=>$value["id_pack_produit"], "id_produit"=>$value["id_produit"]));
}

$q = "SELECT *  FROM import_produit_fournisseur";
$produit_fournisseur = ATF::db()->sql2array($q);

foreach ($produit_fournisseur as $key => $value) {
	$recurrence = ATF::produit()->select($value["id_produit"] , "mode_paiement");
	ATF::produit_fournisseur()->i(array("id_produit"=> $value["id_produit"],
										"id_fournisseur"=> $value["id_fournisseur"]+1,
										"prix_prestation"=> $value["prix_prestation"],
										"recurrence"=> $recurrence,
										"departement"=> $value["departement"]));
}

$q = "SELECT *  FROM import_fournisseur_loyer";
$fournisseur_loyer = ATF::db()->sql2array($q);

foreach ($fournisseur_loyer as $key => $value) {
	if($value["periodicite"] == "mensuel")	$periodicite = "mois";
	else $periodicite = $value["periodicite"];

	if($value["nature"] == "prolongation probable") $value["nature"] = "prolongation_probable";

	ATF::produit_fournisseur_loyer()->i(array(  "id_produit"=> $value["id_produit"],
												"nb_loyer"=> $value["nb"],
												"loyer"=> $value["loyer"],
												"ordre"=> $value["ordre"],
												"periodicite"=> $periodicite,
												"id_fournisseur"=> $value["id_fournisseur"]+1,
												"departement"=> $value["departement"],
												"nature"=>$value["nature"]));
}


$q = "SELECT *  FROM import_loyer_prduit";
$produit_loyer = ATF::db()->sql2array($q);

foreach ($produit_loyer as $key => $value) {
	if($value["periodicite"] == "mensuel")	$periodicite = "mois";
	else $periodicite = $value["periodicite"];

	if($value["nature"] == "prolongation probable") $value["nature"] = "prolongation_probable";
	if($value["nature"] == "Premier loyer") $value["nature"] = "majoration";
	if($value["nature"] == "Promotion") $value["nature"] = "promo";

	ATF::produit_loyer()->i(array(  "id_produit"=> $value["id_produit"],
												"duree"=> $value["nb"],
												"loyer"=> $value["loyer"],
												"ordre"=> $value["ordre"],
												"periodicite"=> $periodicite,												
												"nature"=>$value["nature"]));
}


?>
