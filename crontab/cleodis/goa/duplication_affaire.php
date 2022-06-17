<?php
define("__BYPASS__",true);
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);

// ARG 1 -> Codename
// ARG 2 -> fonction (produit, affaire)
// ARG 3 -> ref
// ARG 4 -> Nb de duplication

// Début de transaction SQL
ATF::db()->begin_transaction();



try {
    if ($_SERVER["argv"][2] == "produit") {
        $ref = $_SERVER["argv"][3];
        echo "Duplication de produit ".$ref."\n";


        ATF::produit()->q->reset()->where("ref", $ref);
        $produits = ATF::produit()->sa();

        foreach ($produits as $key => $value) {
            for($i=2; $i<=  $_SERVER["argv"][4]+1; $i++) {
                $p = $value;
                unset($p["ref"], $p["id_produit"]);
                $p["produit"] =  str_replace('1', $i,  $p["produit"]);

                $q = "SELECT MAX(ref) FROM produit";
                $max_ref = ATF::db()->ffc($q);
                $max_ref = str_replace('BMC', '', $max_ref);
                $max_ref = intval($max_ref, 10) + 1;

                if($max_ref){
                    if($max_ref<10){
                        $suffix="0000".$max_ref;
                    }elseif($max_ref<100){
                        $suffix="000".$max_ref;
                    } elseif($max_ref<1000){
                        $suffix="00".$max_ref;
                    } elseif($max_ref<10000){
                        $suffix="0".$max_ref;
                    }else{
                        $suffix=$max_ref;
                    }
                }else{
                    $suffix="00001";
                }
                $p["ref"] ="BMC".$suffix;

                ATF::produit()->insert($p);
            }
        }
    }

    if ($_SERVER["argv"][2] == "affaire") {

        $ref = $_SERVER["argv"][3];
        echo "Duplication de l'affaire ".$ref."\n";

        ATF::affaire()->q->reset()->where("affaire.ref", $ref);
        $affaire_init = ATF::affaire()->select_row();
        $affaire_init = ATF::affaire()->select($affaire_init["affaire.id_affaire"]);



        ATF::devis()->q->reset()->where("id_affaire", $affaire_init["id_affaire"]);
        $devis_init = ATF::devis()->select_row();

        ATF::loyer()->q->reset()->where("id_affaire", $affaire_init["id_affaire"]);
        $loyer_init = ATF::loyer()->select_all();

        ATF::devis_ligne()->q->reset()->where("id_devis", $devis_init["id_devis"]);
        $devis_lignes_init = ATF::devis_ligne()->select_all();


        for ($i=2; $i<=  $_SERVER["argv"][4]+1; $i++) {
            $devis = array(
                "id_societe" => $affaire_init['id_societe'],
                "type_contrat" => $devis_init['type_contrat'],
                "validite" => $devis_init['validite'],
                "tva" => $devis_init["tva"],
                "devis" => str_replace('1', $i,  $devis_init["devis"]),
                "date" => date("d-m-Y"),
                "type_devis" => "normal",
                "id_contact" => $devis_init["id_contact"],
                "prix_achat"=> $affaire_init["prix_achat"],
                "id_type_affaire" => $affaire_init["id_type_affaire"],
                "IBAN"=> $affaire_init["iban"],
                "BIC"=> $affaire_init["bic"],
                "id_user" => $devis_init["id_user"],
                "ref" => ATF::affaire()->getRef(date("Y-m-d"))
            );

            // COnstruction des lignes de devis a partir des produits en JSON
            $values_devis =array();
            $values_lignes = array();
            $toInsertLoyer = array();
            foreach ($devis_lignes_init as $key => $value) {
                $values_lignes[] =  array(
                    "devis_ligne__dot__produit"=> $value['produit'],
                    "devis_ligne__dot__quantite"=>$value['quantite'],
                    "devis_ligne__dot__type"=>$value['type'],
                    "devis_ligne__dot__ref"=>$value['ref'],
                    "devis_ligne__dot__prix_achat"=>$value["prix_achat"],
                    "devis_ligne__dot__id_produit"=>$value['produit'],
                    "devis_ligne__dot__id_fournisseur"=>$value['id_fournisseur'],
                    "devis_ligne__dot__visibilite_prix"=>$value["visibilite_prix"],
                    "devis_ligne__dot__date_achat"=>$value["date_achat"],
                    "devis_ligne__dot__commentaire"=>$value["commentaire"],
                    "devis_ligne__dot__neuf"=>$value["neuf"],
                    "devis_ligne__dot__serial"=>$value['serial'],
                    "devis_ligne__dot__id_produit_fk"=>$value['id_produit'],
                    "devis_ligne__dot__id_fournisseur_fk"=>$value['id_fournisseur'],
                    "devis_ligne__dot__duree"=>$value['duree'],
                    "devis_ligne__dot__loyer"=>$value['loyer'],
                    "devis_ligne__dot__id_sous_categorie"=>$value['id_sous_categorie'],
                    "devis_ligne__dot__id_pack_produit"=>$value["id_pack_produit"],
                    "devis_ligne__dot__sous_categorie"=> $value["sous_categorie"],
                    "devis_ligne__dot__pack_produit"=> $value["pack_produit"],
                    "devis_ligne__dot__ean"=>$value['ean'],
                    "devis_ligne__dot__id_categorie"=>$value['id_categorie'],
                    "devis_ligne__dot__categorie"=>$value['categorie'],
                    "devis_ligne__dot__commentaire_produit"=>$value['commentaire'],
                    "devis_ligne__dot__visible"=>$value['visible'],
                    "devis_ligne__dot__visible_sur_site"=>$value['visible_sur_site'],
                    "devis_ligne__dot__visible_pdf"=>$value['visible_sur_pdf'],
                    "devis_ligne__dot__ordre"=>$value['ordre'],
                    "devis_ligne__dot__frequence_fournisseur"=>$value['frequence_fournisseur'],
                    "devis_ligne__dot__caracteristique"=>$value['caracteristique']
                  );
            }


            foreach ($loyer_init as $key => $value) {
                $toInsertLoyer[] = array(
                    "loyer__dot__loyer"=> $value["loyer"],
                    "loyer__dot__duree"=> $value["duree"],
                    "loyer__dot__type"=> $value["type"],
                    "loyer__dot__assurance"=> $value["assurance"],
                    "loyer__dot__frais_de_gestion"=> $value["frais_de_gestion"],
                    "loyer__dot__frequence_loyer"=> $value["frequence_loyer"],
                    "loyer__dot__serenite"=>$value["serenite"],
                    "loyer__dot__maintenance"=>$value["maintenance"],
                    "loyer__dot__hotline"=>$value["hotline"],
                    "loyer__dot__supervision"=>$value["supervision"],
                    "loyer__dot__support"=>$value["support"],
                    "loyer__dot__avec_option"=>$value["avec_option"]
                );
            }

            $values_devis = array("loyer"=>json_encode($toInsertLoyer), "produits"=>json_encode($values_lignes));
            $id_devis = ATF::devis()->insert(array("devis"=>$devis, "values_devis"=>$values_devis));

            // Creation des comités
            ATF::comite()->q->reset()->where("id_affaire", $affaire_init["id_affaire"]);

            foreach (ATF::comite()->sa() as $key => $value) {
                $comite = $value;
                $comite["id_affaire"] = ATF::devis()->select($id_devis, "id_affaire");
                ATF::comite()->i($comite);
            }
        }
    }

} catch (errorATF $e) {
	ATF::db()->rollback_transaction();
	throw $e;
}
ATF::db()->commit_transaction();


echo "Fin\n";