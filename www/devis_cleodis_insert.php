<?
$_SERVER["argv"][1] = $_POST["codename"];
include(dirname(__FILE__)."/../global.inc.php");

ATF::$codename = $_POST["codename"];

ATF::$usr = new usr("18");
ATF::_s("user",ATF::$usr);

$panier = $_POST["panier"];
$infos = $_POST["infos"];
$vente = $_POST["vente"];

if($infos["societe"] && $infos["siret"] && $infos["adresse"] && $infos["cp"] && $infos["ville"]  &&  $infos["civilite"] && $infos["nom"] && $infos["prenom"] &&  $infos["email"] &&  $infos["mobile"]){
    log::logger("Ok", "mfleurquin");
    try{
        ATF::societe()->q->reset()->where("siret",$infos['siret']);
        $soc = ATF::societe()->select_row();

        if(!$soc){
             $societe = array("societe" => $infos["societe"],
                         "siret"   => $infos["siret"],
                         "adresse" => $infos["adresse"],
                         "cp"      => $infos["cp"],
                         "ville"   => $infos["ville"]
                        );
            $id_societe = ATF::societe()->insert($societe);
        }else{
            $id_societe = $soc["id_societe"];
        }

        ATF::contact()->q->reset()->where("nom",ATF::db()->real_escape_string($infos['nom']),"AND")
                                  ->where("prenom",ATF::db()->real_escape_string($infos['prenom']));

        $contact = ATF::contact()->select_row();
        if($contact){
            $id_contact = $contact["id_contact"];
            ATF::contact()->u(array("id_contact"=> $id_contact,
                                    "civilite" => $infos["civilite"],
                                    "nom"    => $infos["nom"],
                                    "prenom" => $infos["prenom"],
                                    "email"  => $infos["email"],
                                    "tel"    => $infos["tel"],
                                    "gsm" => $infos["mobile"]
                                )
                             );

        }else{
            $contact = array("id_societe" => $id_societe,
                             "civilite" => $infos["civilite"],
                             "nom"    => $infos["nom"],
                             "prenom" => $infos["prenom"],
                             "email"  => $infos["email"],
                             "tel"    => $infos["tel"],
                             "gsm" => $infos["mobile"]
                            );
            $id_contact = ATF::contact()->insert($contact);
        }
        ATF::societe()->u(array("id_societe"=>$id_societe , "id_contact_signataire"=>$id_contact));

        $devis = array();

        foreach ($panier as $key => $value) {
            $devis[$value["duree"]][]=$value;
            $devis[$value["duree"]]["loyer"]= $devis[$value["duree"]]["loyer"] + ($value["loyer"]*$value["quantite"]);
        }


        foreach ($devis as $key => $value) {
            $loyer = $devis = $produits = array();

            $prix_achat = $prix = 0;

            $devis = array( "id_societe" => $id_societe,
                            "type_contrat"   => "lld",
                            "devis" => "devis_site_web_".date("ymdHi"),
                            "tva" => "1,2",
                            "validite" => date("Y-m-d",strtotime("+15 day")),
                            "date" => date("d-m-Y"),
                            "type_devis"=> "normal",
                            "id_contact" => $id_contact,
                            "prix_achat" => $prix_achat,//somme des prix_achat des produits
                            "prix" => $prix,//somme des loyers * duree
                            //"marge_absolue" => 0, //prix - prix_achat
                            //"marge" => 0, //?
                          );
            $loyer[] = array("loyer__dot__loyer" => $value["loyer"],
                             "loyer__dot__duree" => $key,
                             "loyer__dot__assurance" => NULL,
                             "loyer__dot__frais_de_gestion" => NULL ,
                             "loyer__dot__frequence_loyer"=>"mois",
                             "loyer__dot__loyer_total"=> $key*$value["loyer"], //somme des loyers
                            );
            $prix = $key*$value["loyer"];
            unset($value["loyer"]);

            foreach ($value as $k => $v) {

                if($v["produit"]){
                    $prod = ATF::produit()->select($v["produit"]);

                    $produits[] = array("devis_ligne__dot__produit"=>$prod["produit"],
                                        "devis_ligne__dot__quantite"=>$v["quantite"],
                                        "devis_ligne__dot__type"=>$prod["type"],
                                        "devis_ligne__dot__ref"=>$prod["ref"],
                                        "devis_ligne__dot__prix_achat"=>$prod["prix_achat"],
                                        "devis_ligne__dot__id_produit"=>$prod["id_produit"],
                                        "devis_ligne__dot__id_fournisseur"=>"1FOTEAM",
                                        "devis_ligne__dot__visibilite_prix"=>"invisible",
                                        "devis_ligne__dot__date_achat"=>"",
                                        "devis_ligne__dot__commentaire"=>$prod["commentaire"],
                                        "devis_ligne__dot__neuf"=>"oui",
                                        "devis_ligne__dot__id_produit_fk"=>$prod["id_produit"],
                                        "devis_ligne__dot__id_fournisseur_fk"=>$prod["id_fournisseur"]);
                    $prix_achat +=  $prod["prix_achat"]*$v["quantite"];

                }


                if($v["pack_produit"]){

                    $devis["devis"] = $v["quantite"]." ".ATF::pack_produit()->select($v["pack_produit"] , "nom");

                    ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit", $v["pack_produit"])->addOrder("ordre");
                    $produits_pack = ATF::pack_produit_ligne()->select_all();

                    foreach ($produits_pack as $kpp => $vpp) {

                        if($vpp["id_produit"]){
                             $prod = ATF::produit()->select($vpp["id_produit"]);

                            $produits[] = array("devis_ligne__dot__produit"=>$prod["produit"],
                                                "devis_ligne__dot__quantite"=>$v["quantite"]*$vpp["quantite"],
                                                "devis_ligne__dot__type"=>$prod["type"],
                                                "devis_ligne__dot__ref"=>$prod["ref"],
                                                "devis_ligne__dot__prix_achat"=>$prod["prix_achat"],
                                                "devis_ligne__dot__id_produit"=>$prod["id_produit"],
                                                "devis_ligne__dot__id_fournisseur"=>"1FOTEAM",
                                                "devis_ligne__dot__visibilite_prix"=>$vpp["visibilite_prix"],
                                                "devis_ligne__dot__visible"=>$vpp["visible"],
                                                "devis_ligne__dot__date_achat"=>"",
                                                "devis_ligne__dot__commentaire"=>$prod["commentaire"],
                                                "devis_ligne__dot__neuf"=>"oui",
                                                "devis_ligne__dot__id_produit_fk"=>$prod["id_produit"],
                                                "devis_ligne__dot__id_fournisseur_fk"=>$vpp["id_fournisseur"]);
                            $prix_achat +=  $vpp["prix_achat"]*($v["quantite"]*$vpp["quantite"]);
                        }
                    }
                }
            }


            $devis["prix_achat"] = $prix_achat;
            $devis["prix"] = $prix;
            if($_POST["codename"] == "cleodis"){ $devis["marge"] = $prix - $prix_achat;  }
            //$devis["marge_absolue"] = 0;

            $values_devis = array( "loyer" => json_encode($loyer),
                                   "produits" => json_encode($produits)
                                );
            $data = array("devis"=>$devis, "values_devis"=>$values_devis);


            $id_devis = ATF::devis()->insert($data);


            if($vente){

                $tache['tache'] = array(
                    "tache" => 'Demande de devis de vente par le client',
                    "id_societe" => $id_societe,
                    "type_tache" => 'creation_contrat',
                    "horaire_fin" => date("Y-m-d H:i:s", strtotime("+7 jours")),
                    "etat" => 'en_cours',
                    "type" => 'vtodo',
                    "id_affaire" => ATF::devis()->select($id_devis, "id_affaire")
                );
                $tache["no_redirect"] = true;
                $tache["dest"]=  array(112);

                $id_tache = ATF::tache()->insert($tache);
                log::logger($id_tache , "mfleurquin");
            }
            $id_affaire = ATF::devis()->select($id_devis, "id_affaire");
            ATF::affaire()->u(array("id_affaire"=>$id_affaire, "site_associe"=>"location_evolutive","provenance"=>"cleodis"));
            ATF::affaire()->createTacheAffaireFromSite($id_affaire);

        }

        return true;
    }catch(errorATF $e){
        print_r($e);
        die();
        return $e->getMessage();
    }
}else{
    log::logger("Fuck", "mfleurquin");
    return false;
}




?>