<?php
define("__BYPASS__",true);
$_SERVER["argv"][1] = "itrenting";
include(dirname(__FILE__)."/../../../global.inc.php");
ATF::define("tracabilite",false);
ATF::$usr->set('id_user',1);

echo "========= DEBUT DE SCRIPT =========\n";

$fichier = $path == '' ? "./fichier.csv" : $path;
$f = fopen($fichier, 'rb');
$entete = fgetcsv($f);
$societes = [];
$lines_count = 0;
$processed_lines = 0;

$partenaires = [];
$clients = [];

$produit = [
    "ref" => "LB",
    "produit" => "los bienes",
    "prix_achat" => 0,
    "id_fabriquant" => 281,
    "id_sous_categorie" => 1
];
ATF::produit()->q->reset()->where("ref", "LB");
$p = ATF::produit()->select_row();

if ($p) {
    $id_produit = $p["id_produit"];
} else {
    $id_produit = ATF::produit()->i($produit);
}

$produit["id_produit"] = $id_produit;


while (($ligne = fgetcsv($f, 0, ';'))) {
    echo $lines_count." - ";
    $lines_count++;

    // if ($lines_count === 52) {
        try{
            if (!$ligne[1]) continue;
            ATF::db()->begin_transaction();

            ATF::affaire()->q->reset()->where("ref_externe", $ligne[4]);

            if (!$a = ATF::affaire()->select_row()) {

                // CREATION / RECUPERATION PARTENAIRE
                $cifPartenaire = cleanCIF($ligne[2]);
                // if ($lines_count === 13 || $lines_count === 15) $cifPartenaire = "B23602956";
                $societeExist = findSociete($cifPartenaire);
                if ($societeExist) {
                    $partenaires[$cifPartenaire] = $societeExist;
                } else {
                    if (!isset($partenaires[$cifPartenaire])) {
                        $data = null;
                        try{
                            $data = ATF::meelo()->getInfosCompanyByRegistrationNumber($cifPartenaire, "ES");
                            echo "DATA Recu pour ".$cifPartenaire." - ";
                        } catch(errorATF $e) {
                            echo "PARTENAIRE CIF: ".$cifPartenaire." introuvable - Ligne ".$lines_count." \n";
                            throw $e;
                        }
                        if ($data) {
                            try{
                                $id_societe = insertSociete($data);
                                $partenaires[$cifPartenaire] = $id_societe;
                            } catch(errorATF $e) {
                                echo $e->getMessage();
                                throw $e;
                            }
                        }
                    }
                }

                // CREATION / RECUPERATION CLIENT
                $cifClient = cleanCIF($ligne[3]);
                // if ($lines_count === 13 || $lines_count === 15) $cifClient = "G08266298";
                $societeExist = findSociete($cifClient);
                if ($societeExist) {
                    $clients[$cifClient] = $societeExist;
                } else {
                    if (!isset($clients[$cifClient])) {
                        $data = null;
                        try{
                            $data = ATF::meelo()->getInfosCompanyByRegistrationNumber($cifClient, "ES");
                            echo "DATA Recu pour ".$cifClient." - ";
                        } catch(errorATF $e) {
                            echo "CLIENT CIF: ".$cifClient." introuvable - Ligne ".$lines_count." \n";
                            throw $e;
                        }
                        if ($data) {
                            try{
                                $id_societe = insertSociete($data);
                                $clients[$cifClient] = $id_societe;
                            } catch(errorATF $e) {
                                echo $e->getMessage();
                                throw $e;
                            }
                        }
                    }
                }
                // Création de l'affaire
                $infos = creationAffaire($ligne, $partenaires, $cifPartenaire, $clients, $cifClient, $id_produit);
                $idAffaire = $infos["idAffaire"];
                $idDevis = $infos["idDevis"];

                // Création du contrat
                $idContrat = createContrat($idDevis, $idAffaire, $clients[$cifClient]);
                ATF::commande()->updateDate(["id_commande" => $idContrat, "key" => "date_debut", "value" => str_replace("/", "-", $ligne[7])]);


                if ($ligne[12]) createProlongation($idAffaire, $clients[$cifClient], $idContrat, $ligne);

            } else {
                echo "Affaire ".$ligne[4]." déja présente \n";
            }
            ATF::db()->commit_transaction();
        } catch(errorATF $e) {
            ATF::db()->rollback_transaction();
            echo $e->getMessage()."\n";
        }
    // }
}

echo "========= FIN DE SCRIPT =========\n";


function cleanCIF($cif) {
    $cif = str_replace('.','',$cif);
    $cif = str_replace('-','',$cif);
    return $cif;
}

function findSociete($cif) {
    ATF::societe()->q->reset()->where("cif", $cif);
    $res = ATF::societe()->sa();
    if (count($res) > 0) {
        return $res[0]["id_societe"];
    }
    return null;
}

function insertSociete($data) {
    try{
        if ($data) {
            $company = $data->company;
            $legalUnit = $company->legalUnit;
            $gerants = array_merge($company->representatives, $company->shareHolders);

            $data_soc = [
                "societe" => $legalUnit->corporateName,
                "id_pays" =>  $company->country,
                "activite" => $legalUnit->activity,
                "date_creation" => $legalUnit->registrationDate,
                "capital" => $legalUnit->shareCapital,
                "activite" => $legalUnit->activity,
                "cif" => $legalUnit->companyRegistrationNumber,
                "capital" => $legalUnit->shareCapital->value,
                "adresse" =>$company->establishments[0]->address->address,
                "cp" =>$company->establishments[0]->address->zipcode,
                "ville" =>$company->establishments[0]->address->city,
                "province" =>$company->establishments[0]->address->province,
                "id_pays" => "ES"
            ];

            $idSociete = ATF::societe()->insert(array("societe" => $data_soc));

            $existContact = false;
            if($gerants){
                foreach ($gerants as $gerant) {
                    if ($gerant->type === "Natural Person" || $gerant->type === "Other" || $gerant->birthName) {
                      $nom = $gerant->lastName;
                      $prenom = $gerant->firstNames;
                      if (!$nom && !$prenom) $nom = $gerant->name;

                      ATF::contact()->q->reset()->where("LOWER(nom)", ATF::db()->real_escape_string(strtolower($nom)), "AND")
                                              ->where("LOWER(prenom)", ATF::db()->real_escape_string(strtolower($prenom)), "AND")
                                              ->where("id_societe", $idSociete, "AND");
                      $c = ATF::contact()->select_row();

                      $fonction = "GERANT";
                      if ($gerant->position) $fonction = $gerant->position;
                      if ($gerant->positions) $fonction = $gerant->positions[count($gerant->positions)]["positionName"];

                      //Si le contact n'exite pas dans optima, on l'insert
                      if(!$c) {
                          $contact = array( "nom" => $nom,
                                            "prenom" => $prenom,
                                            "fonction" => $fonction,
                                            "id_societe" => $idSociete,
                                            "est_dirigeant" => "oui"
                                          );
                          ATF::contact()->insert($contact);
                          $existContact = true;
                      } else {
                        $existContact = true;
                      }
                    }
                }
            }

            if (!$existContact){
                //Si Credit Safe n'a retourné aucun dirigeant, on en crée un en attendant
                $contact = array( "nom"=>"GERANT", "id_societe"=> $idSociete);
                ATF::contact()->insert( $contact );
            }

            return $idSociete;
        }
    } catch(errorATF $e) {
        throw $e;
    }
}

function creationAffaire($ligne, $partenaires, $cifPartenaire, $clients, $cifClient, $idProduit) {
    $loyers = [];
    $l = [
        "loyer__dot__loyer" => "",
        "loyer__dot__duree" => "",
        "loyer__dot__frequence_loyer" => "",
        "loyer__dot__type" => "engagement",
        "loyer__dot__avec_option" => "non",
        "loyer__dot__assurance" => "",
        "loyer__dot__frais_de_gestion" => "",
        "loyer__dot__serenite" => "",
        "loyer__dot__maintenance" => "",
        "loyer__dot__hotline" => "",
        "loyer__dot__supervision" => "",
        "loyer__dot__support" => ""
    ];
    $i = 0;
    if ($ligne[10]) {
        $loyers[$i] = $l;
        $loyers[$i]["loyer__dot__loyer"] = str_replace(",", ".", $ligne[10]);
        $loyers[$i]["loyer__dot__duree"] = 1;
        $loyers[$i]["loyer__dot__frequence_loyer"] = "mois";
        $i++;
    }
    $loyers[$i] = $l;
    $loyers[$i]["loyer__dot__loyer"] = str_replace(",", ".", $ligne[11]);
    $loyers[$i]["loyer__dot__duree"] = $ligne[8];
    $loyers[$i]["loyer__dot__frequence_loyer"] = "mois";


    $produits[] = [
        "devis_ligne__dot__caracteristique" => "",
        "devis_ligne__dot__quantite" => "1",
        "devis_ligne__dot__type" => "fixe",
        "devis_ligne__dot__ref" => "LB",
        "devis_ligne__dot__prix_achat" => "1",
        "devis_ligne__dot__produit" => "los bienes",
        "devis_ligne__dot__visibilite_prix" => "invisible",
        "devis_ligne__dot__date_achat" => "",
        "devis_ligne__dot__commentaire" => "",
        "devis_ligne__dot__neuf" => "oui",
        "devis_ligne__dot__id_produit_fk" => $idProduit,
        "devis_ligne__dot__id_fournisseur_fk" => $partenaires[$cifPartenaire]
    ];

    $contacts = [];
    ATF::contact()->q->reset()->where('id_societe', $clients[$cifClient]);
    $contacts = ATF::contact()->sa();

    $devis = [
        "devis" => [
            "id_societe" => $clients[$cifClient],
            "ref_externe" => $ligne[4],
            "date" => date("d-m-Y", strtotime($ligne["1"]."-".$ligne[0]."-01")),
            "type_devis" => "normal",
            "id_contact" => $contacts[0]["id_contact"],
            "id_type_affaire" => 1,
            "id_commercial" => 2,
            "devis" => "importation",
            "id_filiale" => 1,
            "type_contrat" => "lld",
            "validite" =>  date("d-m-Y", strtotime($ligne["1"]."-".$ligne[0]."-20")),
            "tva" => "1.1",
            "id_apporteur" => $partenaires["$cifPartenaire"],
            "id_user" => 1
        ],
        "values_devis" => [
            "loyer" => json_encode($loyers),
            "produits" => json_encode($produits),
        ]
    ];

    try{
        $idDevis = ATF::devis()->insert($devis);
        $idAffaire = ATF::devis()->select($idDevis, "id_affaire");
        echo "ID DEVIS : ".$idDevis." ID AFFAIRE : ".$idAffaire."\n";
        return ["idAffaire" => $idAffaire, "idDevis" => $idDevis];
    }catch(errorATF $e) {
        throw $e;
    }
}

function createContrat($id_devis, $id_affaire, $idSociete) {
    ATF::devis_ligne()->q->reset()->where('id_devis', $id_devis);
    $lignesDevis = ATF::devis_ligne()->select_all();

    $commande =array(
        "commande" => "importation",
        "type" => "prelevement",
        "id_societe" => $idSociete,
        "date" => date("d-m-Y"),
        "id_affaire" => $id_affaire,
        "id_devis" => $id_devis,
        "prix_achat" =>0,
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

function createProlongation($idAffaire, $idSociete, $idCommande, $ligne) {
    try{
        $refi = null;
        ATF::refinanceur()->q->reset()->where("refinanceur", str_replace(', S.A.', "", $ligne[5]));

        if ($re = ATF::refinanceur()->select_row()) {
            $refi = $re["id_refinanceur"];
        } else {
            $refi = ATF::refinanceur()->insert(["refinanceur" => str_replace(', S.A.', "", $ligne[5]), "code" => "R00", "code_refi"=> "REFACTURATION"]);
        }

        $commande=ATF::commande()->select($idCommande);


        $prol = [
            "id_affaire" => $idAffaire,
            "date_debut" => date("Y-m-d",strtotime($commande["date_evolution"]."+1 day")),
            "id_societe" => $idSociete,
            "id_refinanceur" => $refi,
            "prix" => $ligne[12],
            "id_commande" => $idCommande
        ];

        $loyers[] = [
            "loyer_prolongation__dot__loyer" => str_replace(",", ".", $ligne[12]),
            "loyer_prolongation__dot__duree" => 1,
            "loyer_prolongation__dot__frequence_loyer" => "mois",
            "loyer_prolongation__dot__assurance" => "",
            "loyer_prolongation__dot__frais_de_gestion" => "",
            "loyer_prolongation__dot__serenite" => "",
            "loyer_prolongation__dot__maintenance" => "",
            "loyer_prolongation__dot__hotline" => "",
            "loyer_prolongation__dot__supervision" => "",
            "loyer_prolongation__dot__support" => ""
        ];

        $values = ["loyer_prolongation" => json_encode($loyers)];

        ATF::prolongation()->insert(["prolongation" => $prol, "values_prolongation" => $values]);
    }catch(errorATF $e) {
        throw $e;
    }
}

?>