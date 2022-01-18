<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "lm";
include(dirname(__FILE__)."/../../global.inc.php");
ATF::define("tracabilite",false);
$id_pack = $_SERVER["argv"][2];


error_reporting(E_ALL);

try{
	ATF::begin_transaction();

	ATF::pack_produit()->q->reset()->where("id_pack_produit", $id_pack);
	$pack = ATF::pack_produit()->select_row();
	unset($pack["id_pack_produit"]);
	$pack["libelle"] .= " copie";

	$pack["id_pack_produit"] = ATF::pack_produit()->i($pack);


	$files = array("photo", "photo1", "photo2");
	foreach ($files as $key => $value) {
		if (file_exists(ATF::pack_produit()->filepath($id_pack,$value))) {
	        copy(ATF::pack_produit()->filepath($id_pack,$value), ATF::pack_produit()->filepath($pack["id_pack_produit"],$value));
	    }
	}

	ATF::produit()->q->reset()->where("produit.id_pack_produit", $id_pack);
	$produits = ATF::produit()->sa();



	$liaison_produit = array();
	foreach ($produits as $key => $value) {
		$p = $value;

		unset($p["id_produit"]);
		$p["id_pack_produit"] = $pack["id_pack_produit"];

		$id_produit = ATF::produit()->i($p);

		if (file_exists(ATF::produit()->filepath($value["id_produit"],"photo_pop_up"))) {
	        copy(ATF::produit()->filepath($value["id_produit"],"photo_pop_up"), ATF::produit()->filepath($id_produit,"photo_pop_up"));
	    }

		$liaison_produit[$value["id_produit"]] = $id_produit;

		ATF::produit_loyer()->q->reset()->where("id_produit", $value["id_produit"]);
		$produit_loyers = ATF::produit_loyer()->select_all();
		foreach ($produit_loyers as $k => $v) {
			unset($v["id_produit_loyer"]);
			$v["id_produit"] = $id_produit;
			ATF::produit_loyer()->i($v);
		}

		ATF::produit_fournisseur()->q->reset()->where("id_produit", $value["id_produit"]);
		$produit_fournisseurs = ATF::produit_fournisseur()->select_all();
		foreach ($produit_fournisseurs as $k => $v) {
			unset($v["id_produit_fournisseur"]);
			$v["id_produit"] = $id_produit;
			ATF::produit_fournisseur()->i($v);
		}

		ATF::produit_fournisseur_loyer()->q->reset()->where("id_produit", $value["id_produit"]);
		$produit_fournisseur_loyers = ATF::produit_fournisseur_loyer()->select_all();
		foreach ($produit_fournisseur_loyers as $k => $v) {
			unset($v["id_produit_fournisseur_loyer"]);
			$v["id_produit"] = $id_produit;
			ATF::produit_fournisseur_loyer()->i($v);
		}
	}

	ATF::produit()->q->reset()->where("produit.id_pack_produit", $pack["id_pack_produit"]);
	$produits = ATF::produit()->sa();
	foreach ($produits as $key => $value) {
		if($value["id_produit_principal"]){
			ATF::produit()->u(array("id_produit"=> $value["id_produit"], "id_produit_principal"=> $liaison_produit[$value["id_produit_principal"]]));
		}

	}

	ATF::commit_transaction();
}catch(errorATF $e){
	echo $e->getMessage();
	ATF::rollback_transaction();
}