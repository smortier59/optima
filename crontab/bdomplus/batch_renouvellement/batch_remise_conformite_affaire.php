<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "bdomplus";
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);

ATF::$usr->set('id_user', 116);


$q = "select b.id_affaire AS id_parent , b.ref AS ref_parent, b.affaire AS lib_parent, a.id_affaire  , a.ref, a.affaire
from affaire a
inner join affaire b on (a.id_parent = b.id_affaire)
where a.id_parent is not null
and a.date ='2020-09-25'
and b.affaire <> CONCAT('BDOM + : ', a.affaire)";


$data = ATF::db()->sql2array($q);


ATF::db()->begin_transaction();
foreach ($data as $key => $value) {
	$data[$key]["lib_parent"] = str_replace("BDOM + : ", "", $value["lib_parent"]);
}

foreach ($data as $key => $value) {

	log::logger($value["ref"] , "mfleurquin");

	$affaire = ATF::affaire()->select($value["id_affaire"]);

	ATF::devis()->q->reset()->where("devis.id_affaire", $value["id_affaire"]);
	$devis = ATF::devis()->select_row();

	ATF::loyer()->q->reset()->where("id_affaire", $value["id_parent"]);
	$loyer_parent = ATF::loyer()->select_row();

	ATF::loyer()->q->reset()->where("id_affaire", $value["id_affaire"]);
	$loyer_enfant = ATF::loyer()->select_row();

	ATF::affaire()->u(array("id_affaire" => $value["id_affaire"], "affaire"=> $value["lib_parent"]));
	ATF::loyer()->u(array( "id_loyer"=> $loyer_enfant["id_loyer"], "frequence_loyer" => $loyer_parent["frequence_loyer"]));

	ATF::commande()->q->reset()->where("commande.id_affaire", $value["id_affaire"]);
	$commande = ATF::commande()->select_row();


	ATF::facture()->q->reset()->where("id_affaire", $value["id_affaire"]);
	foreach (ATF::facture()->sa() as $kf => $vf) {
		ATF::facture()->delete($vf["id_facture"]);
	}


	ATF::commande()->delete($commande["commande.id_commande"]);


	ATF::devis_ligne()->q->reset()->where('id_devis', $devis["id_devis"]);
    $lignesDevis = ATF::devis_ligne()->select_all();

    $commande =array(
        "commande" => $value["lib_parent"],
        "type" => "prelevement",
        "id_societe" => $affaire["id_societe"],
        "date" => date("d-m-Y"),
        "id_affaire" => $value["id_affaire"],
        "id_devis" => $devis["id_devis"],
        "prix_achat" =>0
    );

    $total_achat = 0;

    $toInsertProduitContrat = array();
    foreach ($lignesDevis as $kd => $vd) {
      $toInsertProduitContrat[] = array(
          "commande_ligne__dot__produit"=>$vd["produit"],
          "commande_ligne__dot__quantite"=>$vd["quantite"],
          "commande_ligne__dot__ref"=>$vd["ref"],
          "commande_ligne__dot__id_fournisseur"=>$vd['id_fournisseur'],
          "commande_ligne__dot__id_fournisseur_fk"=>$vd['id_fournisseur'],
          "commande_ligne__dot__prix_achat"=>$vd["prix_achat"],
          "commande_ligne__dot__id_produit"=>$vd["produit"],
          "commande_ligne__dot__id_produit_fk"=>$vd["id_produit"],
          "commande_ligne__dot__visible"=>$vd["visible"],
          "commande_ligne__dot__serial"=>$vd['serial'] ? $vd['serial'] : '',

          "commande_ligne__dot__duree"=>$vd['duree'],
          "commande_ligne__dot__loyer"=>$vd['loyer'],
          "commande_ligne__dot__id_sous_categorie"=>$vd['id_sous_categorie'],
          "commande_ligne__dot__id_pack_produit"=>$vd['id_pack_produit'],
          "commande_ligne__dot__sous_categorie"=>$vd['sous_categorie'],
          "commande_ligne__dot__pack_produit"=>$vd['pack_produit'],
          "commande_ligne__dot__ean"=>$vd['ean'],
          "commande_ligne__dot__id_categorie"=>$vd['id_categorie'],
          "commande_ligne__dot__categorie"=>$vd['categorie'],
          "commande_ligne__dot__commentaire_produit"=>$vd['commentaire'],
          "commande_ligne__dot__visible_sur_site"=>$vd['visible_sur_site'],
          "commande_ligne__dot__visible_pdf"=>$vd['visible_pdf'],
          "commande_ligne__dot__frequence_fournisseur"=>$vd['frequence_fournisseur'],
          "commande_ligne__dot__ordre"=>$vd['ordre']
      );
      $commande["prix_achat"] += ($vd["prix_achat"] * $vd["quantite"]);
    }

    $values_commande = array( "produits" => json_encode($toInsertProduitContrat));

    $id_commande = ATF::commande()->insert(array("commande"=>$commande , "values_commande"=>$values_commande));


	$infos = array(
      "id_commande" => $id_commande,
      "value" => date("Y-m-01", strtotime("+1 month")),
      "key" => "date_debut"
    );
    ATF::commande()->updateDate($infos);

}

ATF::db()->commit_transaction();


?>