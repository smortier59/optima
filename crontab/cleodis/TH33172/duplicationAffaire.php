<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);
ATF::$usr->set('id_user',16);

$affaire = $argv[2];
$maisonMere = $argv[3];

echo "========= DEBUT DE SCRIPT =========\n";

echo "Affaire : ".$affaire." - Maison mere ".$maisonMere."\n";

$filiales = [];
ATF::societe()->q->reset()->where("id_filiale", $maisonMere);

foreach (ATF::societe()->sa() as $key => $value) {
    $filiales[] = $value["id_societe"];
}

ATF::affaire()->q->reset()->where("affaire.ref", $affaire);
$a = ATF::affaire()->select_row();
$id_affaire = $a["affaire.id_affaire"];

$i = 0;
foreach ($filiales as $id_filiale) {
    $i++;
    try{
        ATF::db()->begin_transaction();
        duplicateAffaire($id_affaire, $id_filiale);
        echo "Affaire ".$i."/".count($filiales)."\n";
        ATF::db()->commit_transaction();
    } catch(errorATF $e) {
        ATF::db()->rollback_transaction();
    }
}



echo "========= FIN DU SCRIPT =========\n";


function duplicateAffaire($id_affaire, $id_societe) {

    $agence = ATF::societe()->select($id_societe);
    $affaire = ATF::affaire()->select($id_affaire);
    ATF::devis()->q->reset()->where("devis.id_affaire", $id_affaire);
    $devis = ATF::devis()->select_row();

    $id_devis = createDevis($agence, $affaire, $devis);
    $newAffaire = ATF::devis()->select($id_devis, "id_affaire");

    createComite($newAffaire, $id_affaire);

    $id_contrat = createContrat($id_societe, $newAffaire, $id_devis);

    echo "Contrat : ".$id_contrat."\n";

    ATF::commande()->updateDate(["id_commande" => $id_contrat, "key" => "retour_contrat", "value" => date("Y-m-d", strtotime("2024-09-30")) ]);
    ATF::commande()->updateDate(["id_commande" => $id_contrat, "key" => "date_debut", "value" => date("Y-m-d", strtotime("2024-10-01")) ]);
}


function createDevis($societe, $affaireParent, $devisParent) {
    $devis = array(
        "id_societe" => $societe["id_societe"],
        "type_contrat" => $devisParent["type_contrat"],
        "validite" => date("d-m-Y", strtotime("+1 month")),
        "tva" => __TVA__,
        "devis" => $devisParent["devis"],
        "date" => date("d-m-Y"),
        "type_devis" => $devisParent["type_devis"],
        "id_contact" => $devisParent["id_contact"],
        "prix_achat"=> $devisParent["prix_achat"],
        "id_type_affaire" => $affaireParent["id_type_affaire"],
        "IBAN"=> $societe["iban"],
        "BIC"=> $societe["bic"]
    );

    ATF::devis_ligne()->q->reset()->where("id_devis", $devisParent["id_devis"]);
    $lignesDevis = ATF::devis_ligne()->select_all();

    $toInsertLoyer = $toInsertProduitDevis = [];

    foreach ($lignesDevis as $value) {
        $toInsertProduitDevis[] =  array(
            "devis_ligne__dot__type" => $value["type"],
            "devis_ligne__dot__id_produit" => $value["produit"],
            "devis_ligne__dot__id_produit_fk" => $value["id_produit"],
            "devis_ligne__dot__ref" => $value["ref"],
            "devis_ligne__dot__produit" => $value["produit"],
            "devis_ligne__dot__quantite" => $value["quantite"],
            "devis_ligne__dot__id_fournisseur" => ATF::societe()->select($value["id_fournisseur"], "societe"),
            "devis_ligne__dot__id_fournisseur_fk" => $value["id_fournisseur"],
            "devis_ligne__dot__prix_achat_ttc" => $value["prix_achat_ttc"],
            "devis_ligne__dot__prix_achat" => $value["prix_achat"],
            "devis_ligne__dot__code" => $value["code"],
            "devis_ligne__dot__id_affaire_provenance" => $value["id_affaire_provenance"],
            "devis_ligne__dot__serial" => $value["serial"],
            "devis_ligne__dot__visible" => $value["visible"],
            "devis_ligne__dot__visibilite_prix" => $value["visibilite_prix"],
            "devis_ligne__dot__neuf" => $value["neuf"],
            "devis_ligne__dot__date_achat" => $value["date_achat"],
            "devis_ligne__dot__ref_simag" => $value["ref_simag"],
            "devis_ligne__dot__commentaire" => $value["commentaire"],
            "devis_ligne__dot__options" => $value["options"],
            "devis_ligne__dot__duree" => $value["duree"],
            "devis_ligne__dot__loyer" => $value["loyer"],
            "devis_ligne__dot__ean" => $value["ean"],
            "devis_ligne__dot__id_pack_produit" => $value["id_pack_produit"],
            "devis_ligne__dot__id_sous_categorie" => $value["id_sous_categorie"],
            "devis_ligne__dot__pack_produit" => $value["pack_produit"],
            "devis_ligne__dot__sous_categorie" => $value["sous_categorie"],
            "devis_ligne__dot__id_categorie" => $value["id_categorie"],
            "devis_ligne__dot__categorie" => $value["categorie"],
            "devis_ligne__dot__commentaire_produit" => $value["commentaire_produit"],
            "devis_ligne__dot__visible_sur_site" => $value["visible_sur_site"],
            "devis_ligne__dot__visible_pdf" => $value["visible_pdf"],
            "devis_ligne__dot__ordre" => $value["ordre"],
            "devis_ligne__dot__frequence_fournisseur" => $value["frequence_fournisseur"],
            "devis_ligne__dot__caracteristique" => $value["caracteristique"]
          );
    }

    ATF::loyer()->q->reset()->where("id_affaire", $affaireParent["id_affaire"]);
    foreach (ATF::loyer()->sa() as $value) {
        $toInsertLoyer[] = array(
            "loyer__dot__loyer"=> 0,
            "loyer__dot__duree"=> $value["duree"],
            "loyer__dot__type"=> $value["type"],
            "loyer__dot__assurance"=> $value["assurance"],
            "loyer__dot__frais_de_gestion"=> $value["frais_de_gestion"],
            "loyer__dot__frequence_loyer"=> $value["frequence_loyer"],
            "loyer__dot__serenite"=> $value["serenite"],
            "loyer__dot__maintenance"=> $value["maintenance"],
            "loyer__dot__hotline"=> $value["hotline"],
            "loyer__dot__supervision"=> $value["supervision"],
            "loyer__dot__support"=> $value["support"],
            "loyer__dot__avec_option"=> $value["avec_option"]
        );
    }

    $values_devis = array("loyer"=>json_encode($toInsertLoyer), "produits"=>json_encode($toInsertProduitDevis));

    return ATF::devis()->insert(array("devis"=>$devis, "values_devis"=>$values_devis));
}

function createComite($id_affaire, $id_affaireParente) {
    ATF::comite()->q->reset()->where("id_affaire", $id_affaireParente);
    foreach (ATF::comite()->select_all() as $value) {
        $c = $value;
        $c["id_societe"] = ATF::affaire()->select($id_affaire, "id_societe");
        $c["id_affaire"] = $id_affaire;
        ATF::comite()->i($c);
    }
}

function createContrat($id_societe, $id_affaire, $id_devis) {
    ATF::devis_ligne()->q->reset()->where('id_devis', $id_devis);
    $lignesDevis = ATF::devis_ligne()->select_all();

    $commande =array(
        "commande" => ATF::devis()->select($id_devis, "devis"),
        "type" => "prelevement",
        "id_societe" => $id_societe,
        "date" => date("d-m-Y"),
        "id_affaire" => $id_affaire,
        "id_devis" => $id_devis,
        "prix_achat" =>0
    );

    $total_achat = 0;

    $toInsertProduitContrat = array();
    foreach ($lignesDevis as $key => $value) {
      $toInsertProduitContrat[] = array(
          "commande_ligne__dot__produit"=>$value["produit"],
          "commande_ligne__dot__quantite"=>$value["quantite"],
          "commande_ligne__dot__ref"=>$value["ref"],
          "commande_ligne__dot__id_fournisseur"=>$value['id_fournisseur'],
          "commande_ligne__dot__id_fournisseur_fk"=>$value['id_fournisseur'],
          "commande_ligne__dot__prix_achat"=>$value["prix_achat"],
          "commande_ligne__dot__id_produit"=>$value["produit"],
          "commande_ligne__dot__id_produit_fk"=>$value["id_produit"],
          "commande_ligne__dot__visible"=>$value["visible"],
          "commande_ligne__dot__serial"=>$value['serial'] ? $value['serial'] : '',
          "commande_ligne__dot__duree"=>$value['duree'],
          "commande_ligne__dot__loyer"=>$value['loyer'],
          "commande_ligne__dot__id_sous_categorie"=>$value['id_sous_categorie'],
          "commande_ligne__dot__id_pack_produit"=>$value['id_pack_produit'],
          "commande_ligne__dot__sous_categorie"=>$value['sous_categorie'],
          "commande_ligne__dot__pack_produit"=>$value['pack_produit'],
          "commande_ligne__dot__ean"=>$value['ean'],
          "commande_ligne__dot__id_categorie"=>$value['id_categorie'],
          "commande_ligne__dot__categorie"=>$value['categorie'],
          "commande_ligne__dot__commentaire_produit"=>$value['commentaire'],
          "commande_ligne__dot__visible_sur_site"=>$value['visible_sur_site'],
          "commande_ligne__dot__visible_pdf"=>$value['visible_pdf'],
          "commande_ligne__dot__frequence_fournisseur"=>$value['frequence_fournisseur'],
          "commande_ligne__dot__ordre"=>$value['ordre'],
          "commande_ligne__dot__caracteristique"=>$value['caracteristique']
      );
      $commande["prix_achat"] += ($value["prix_achat"] * $value["quantite"]);
    }

    $values_commande = array( "produits" => json_encode($toInsertProduitContrat));

    $id_commande = ATF::commande()->insert(array("commande"=>$commande , "values_commande"=>$values_commande));

    return ATF::commande()->decryptId($id_commande);
}

