<?
$_SERVER["argv"][1] = 'lm';
include(dirname(__FILE__)."/../global.inc.php");

ATF::$codename = $_SERVER["argv"][1];

ATF::$usr = new usr(18);
ATF::_s("user",ATF::$usr);

$infos = $_POST;

log::logger("DEVIS LM INSERT", "mfleurquin");
log::logger($infos, "mfleurquin");


//Creation de la tache pour alerter benjamin de la création d'une affaire terminée par SLIMPAY
if($infos["create_tache"]){
    try{
        $tache = array("tache"=>array("id_societe"=> $infos["id_societe"],
                                       "id_user"=>23,
                                       "origine"=>"societe_commande",
                                       "tache"=>"Une nouvelle commande viens d'être passée et validée par SLIMPAY",
                                       "id_affaire"=>$infos["id_affaire"],
                                       "type_tache"=>"creation_contrat",
                                       "horaire_fin"=>date('Y-m-d h:i:s', strtotime('+3 day')),
                                       "no_redirect"=>"true"
                                      ),
                        "dest"=>23
                    );
        $id_tache = ATF::tache()->insert($tache);

        ATF::comite()->insert(  array("date"=>date("Y-m-d"),
                                    "id_affaire"=>$infos["id_affaire"],
                                    "id_societe"=>$infos["id_societe"],
                                    "etat"=>"en_attente",
                                    "date_creation"=>date("Y-m-d"),
                                    "suivi_notifie"=>array(18,23)
                                ));
        die;
    }catch(errorATF $e){
        log::logger($e->getMessage(),'lm');
        echo "Une erreur s'est produit, merci de contacter le service client.";
    }
}

//Creation d'une offre Magasin
if($infos["OffreMagasin"]){
    try{
        ATF::contact()->q->reset()->where("id_societe",$infos["devis"]["devis"]["id_societe"]);
        $contact = ATF::contact()->select_row();
        if($contact){
           $id_contact = $contact["id_contact"];
       }else{
            $societe = ATF::societe()->select($infos["devis"]["devis"]["id_societe"]);
            $contact = array(
                "id_societe" => $societe["id_societe"],
                "civilite" => $societe["civilite"],
                "nom"    => $societe["nom"],
                "prenom" => $societe["prenom"],
                "email"  => $societe["email"],
                "tel"=>     $societe["tel"]
            );
            $id_contact = ATF::contact()->insert($contact);
       }
       $infos["devis"]["devis"]["id_contact"] = $id_contact;

       $id_devis = ATF::devis()->insert($infos["devis"]);
       echo ATF::devis()->select($id_devis , "id_affaire");
       die;
    }catch(errorATF $e){
        log::logger($e->getMessage(),'lm');
        echo "Erreur, merci de contacter le service client.";
    }
}

//Validation d'une offre Magasin par une hotesse, on crée la commande correspondante au devis
if($infos["create_commande"]){
    try{
        ATF::devis()->q->reset()->where("devis.id_affaire",$infos["id_affaire"]);
        $devis = ATF::devis()->select_row();
        ATF::devis_ligne()->q->reset()->where("id_devis",$devis["id_devis"]);
        $lignes = ATF::devis_ligne()->select_all();

        ATF::commande()->q->reset()->where("commande.id_affaire",$infos["id_affaire"]);
        if($com = ATF::commande()->select_row()){
            ATF::commande()->delete($com["id_commande"]);
        }

        $commande = $commande_ligne = array();
        $commande["ref"] = $devis["ref"];
        $commande["id_societe"] = $devis["id_societe"];
        $commande["commande"] = $devis["devis"];
        $commande["date"] = date("Y-m-d");
        $commande["id_devis"] = $devis["id_devis"];
        $commande["etat"] = $devis["non_loyer"];
        $commande["id_user"] = $devis["id_user"];
        $commande["id_affaire"] = $devis["id_affaire"];
        $commande["clause_logicielle"] =  "non";
        $commande["etat"] = "pending";
        $commande["from_web"] = true;

        foreach ($lignes as $key => $value) {
           $commande_ligne[$key]["id_produit"]            = $value["id_produit"];
           $commande_ligne[$key]["ref"]                   = $value["ref"];
           $commande_ligne[$key]["produit"]               = $value["produit"];
           $commande_ligne[$key]["quantite"]              = $value["quantite"];
           $commande_ligne[$key]["id_fournisseur"]        = $value["id_fournisseur"];
           $commande_ligne[$key]["prix_achat"]            = $value["prix_achat"];
           $commande_ligne[$key]["code"]                  = $value["code"];
           $commande_ligne[$key]["id_affaire_provenance"] = $value["id_affaire_provenance"];
           $commande_ligne[$key]["serial"]                = $value["serial"];
           $commande_ligne[$key]["visible"]               = $value["visible"];
           $commande_ligne[$key]["neuf"]                  = $value["neuf"];
           $commande_ligne[$key]["date_achat"]            = $value["date_achat"];
           $commande_ligne[$key]["commentaire"]           = $value["commentaire"];
        }

        $data = array("commande"=>$commande, "values_commande"=>array("produit"=>json_encode($commande_ligne)));

        $id_commande = ATF::commande()->insert($data);
        $id_commande = ATF::commande()->decryptId($id_commande);

        log::logger($id_commande , "mfleurquin");
        echo $id_commande;
        die;
    }catch(errorATF $e){
        log::logger($e->getMessage(),'lm');
        echo "Erreur, merci de contacter le service client.";
    }
}

//Récuperation du PDF du contrat
if ($infos["id_contrat"]) {
    ATF::commande()->move_files($infos["id_contrat"],$_SESSION,false,NULL,"contratA4");

    $filename = ATF::commande()->filepath($infos["id_contrat"],"contratA4");
    $handle = fopen($filename, "r");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
    echo $contents;
    die;
}

//Récuperation du PDF de la facture
if ($infos["id_facture"]) {

    $id_facture = ATF::facture()->decryptId($infos["id_facture"]);

    $filename = ATF::facture()->filepath($id_facture,"fichier_joint");

    if(file_exists($filename)){
        $handle = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        echo $contents;
        die;
    }else{
        echo "Probleme de récupération du PDF";
        die;
    }
}

//Récuperation du PDF signé du contrat
if ($infos["getPdfSigne"]) {

    $id_commande = ATF::commande()->decryptId($infos["id_commande"]);

    $filename = ATF::commande()->filepath($id_commande,"retour");

    if(file_exists($filename)){
        $handle = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        echo $contents;
        die;
    }else{
        $filename = ATF::commande()->filepath($id_commande,"contratA4");

        if(file_exists($filename)){
            $handle = fopen($filename, "r");
            $contents = fread($handle, filesize($filename));
            fclose($handle);
            echo $contents;
            die;
        }else{
            echo "Probleme de récupération du PDF";
            die;
        }
    }
}

//Récuperation du PDF signé du contrat
if ($infos["getPDFCourrierInformation"]) {

    $filename = ATF::courrier_information_pack()->filepath($infos["courrier_information_pack"],"fichier_joint");

    if(file_exists($filename)){
        $handle = fopen($filename, "r");
        $contents = fread($handle, filesize($filename));
        fclose($handle);
        echo $contents;
        die;
    }else{
        echo "Probleme de récupération du PDF";
        die;
    }
}


//Enregistrement du contrat signé par SLIMPAY
if($infos["save_contrat"]){
    log::logger("Insert PDF", "mfleurquin");
    util::file_put_contents(ATF::commande()->filepath($infos["id_commande"],"retour"), base64_decode($infos["pdf"]));
    $id_pdf_affaire = ATF::pdf_affaire()->insert(array("id_affaire"=>$infos["id_affaire"], "provenance"=>"Contrat signé par SLIMPAY"));
    copy(ATF::commande()->filepath($infos["id_commande"],"retour"), ATF::pdf_affaire()->filepath($id_pdf_affaire,"fichier_joint"));
    log::logger("AJOUT Date retour contrat & AP", "mfleurquin");
    ATF::commande()->u(array("id_commande"=>$infos["id_commande"], "retour_contrat"=>date("Y-m-d") , "retour_prel"=>date("Y-m-d")));
    die;
}

//Enregistrement du mandat SLIMPAY
if($infos["save_mandat"]){
    log::logger("Insert Mandat PDF", "mfleurquin");
    log::logger($infos , "mfleurquin");
    util::file_put_contents(ATF::affaire()->filepath($infos["id_affaire"],"mandat_slimpay"), base64_decode($infos["pdf"]));
    $id_pdf_affaire = ATF::pdf_affaire()->insert(array("id_affaire"=>$infos["id_affaire"], "provenance"=>"Mandat SLIMPAY"));
    copy(ATF::affaire()->filepath($infos["id_affaire"],"mandat_slimpay"), ATF::pdf_affaire()->filepath($id_pdf_affaire,"fichier_joint"));
    die;
}


//Création d'une souscription faite par le site WEB
if($infos["id_societe"]){


    try{
        $societe = ATF::societe()->select($infos["id_societe"]);

        if($infos["societe_form"]["adresse"]){
            $adresse_livraison = $infos["societe_form"]["adresse"];
            $adresse_livraison_2 = $adresse_livraison_3 = NULL;

            if($infos["societe_form"]["adresse_2"] !== "")  $adresse_livraison_2 = $infos["societe_form"]["adresse_2"];
            if($infos["societe_form"]["adresse_3"] !== "")  $adresse_livraison_3 = $infos["societe_form"]["adresse_3"];

            $cp_livraison = $infos["societe_form"]["cp"];
            $ville_livraison = $infos["societe_form"]["ville"];
            $pays_livraison = $infos["societe_form"]["id_pays"];

            $adresse_facturation = $adresse_facturation_2 = $adresse_facturation_3 = NULL;
            if($infos["societe_form"]["facturation_same_livraisonCheckbox"] == "non"){
                $adresse_facturation = $infos["societe_form"]["facturation_adresse"];
                if($infos["societe_form"]["facturation_adresse_2"] )  $adresse_facturation_2  = $infos["societe_form"]["facturation_adresse_2"];
                if($infos["societe_form"]["facturation_adresse_3"] )  $adresse_facturation_3  = $infos["societe_form"]["facturation_adresse_3"];
                $cp_facturation = $infos["societe_form"]["facturation_cp"];
                $ville_facturation =  $infos["societe_form"]["facturation_ville"];
                $pays_facturation = $infos["societe_form"]["facturation_id_pays"];


            }else{
                $adresse_facturation = $adresse_livraison;
                $adresse_facturation_2 = $adresse_livraison_2;
                $adresse_facturation_3 = $adresse_livraison_3;
                $cp_facturation = $cp_livraison;
                $ville_facturation =  $ville_livraison;
                $pays_facturation = $pays_livraison;
            }
        }else{
            $adresse_livraison = $societe["adresse"];
            if($societe["adresse_2"] !== "" )  $adresse_livraison_2  = $societe["adresse_2"];
            if($societe["adresse_3"] !== "" )  $adresse_livraison_3  = $societe["adresse_3"];

            $cp_livraison = $societe["cp"];
            $ville_livraison = $societe["ville"];
            $pays_livraison = $societe["id_pays"];

            $adresse_facturation = $adresse_livraison;
            $adresse_facturation_2 = $adresse_livraison_2;
            $adresse_facturation_3 = $adresse_livraison_3;
            $cp_facturation = $cp_livraison;
            $ville_facturation =  $ville_livraison;
            $pays_facturation = $pays_livraison;
        }


        ATF::contact()->q->reset()->where("id_societe",$infos["id_societe"]);
        $contact = ATF::contact()->select_row();
        if($contact){
            $id_contact = $contact["id_contact"];
        }else{
            $contact = array(
                "id_societe" => $infos["id_societe"],
                "civilite" => $societe["civilite"],
                "nom"    => $societe["nom"],
                "prenom" => $societe["prenom"],
                "email"  => $societe["email"],
                "tel"    => $societe["tel"],
                "gsm" => $societe["mobile"]
            );
            $id_contact = ATF::contact()->insert($contact);
        }

        // Calcul des loyers par rapport aux produits choisis
        $loyers=array();
        ATF::produit()->q->reset()->where("produit.id_pack_produit",$infos["panier"]["pack"]["id_pack_produit"])->addOrder("produit.ordre");
        $loyers["produits"]=ATF::produit()->sa();
        foreach ($loyers["produits"] as $key => $value) {
            ATF::produit_loyer()->q->reset()->where("id_produit",$value["id_produit"])->addOrder('ordre');
            $l = ATF::produit_loyer()->sa();

            foreach ($l as $kl => $vl) {
                if($qte = $infos["panier"]["product"][$value["id_produit"]]["quantite"]){

                    //Si pas un sous produit
                    if(!$value["id_produit_principal"]){
                        $loyers["produits"][$key]["loyer"][$vl["ordre"]]["loyer"] = number_format(($vl["loyer"]*$value["tva_loyer"]),2,".","");
                        $loyers["loyer"][$vl["ordre"]]["duree"] = $vl["duree"];
                        $loyers["loyer"][$vl["ordre"]]["loyer"] += (number_format(($vl["loyer"]*$value["tva_loyer"]),2,".","")*$qte);
                        $loyers["loyer"][$vl["ordre"]]["nature"] = $vl["nature"];
                    }else{
                        $qte_produit_princ = $infos["panier"]["product"][$value["id_produit_principal"]]["quantite"];
                        if($qte_produit_princ && $qte_produit_princ > 0){
                            if($value["qte_lie_principal"] == "non"){
                                $loyers["produits"][$key]["loyer"][$vl["ordre"]]["loyer"] = number_format(($vl["loyer"]*$value["tva_loyer"]),2,".","");
                                $loyers["loyer"][$vl["ordre"]]["duree"] = $vl["duree"];
                                $loyers["loyer"][$vl["ordre"]]["loyer"] += (number_format(($vl["loyer"]*$value["tva_loyer"]),2,".","")*$qte);
                                $loyers["loyer"][$vl["ordre"]]["nature"] = $vl["nature"];
                            }else{
                                $loyers["produits"][$key]["loyer"][$vl["ordre"]]["loyer"] = number_format(($vl["loyer"]*$value["tva_loyer"]),2,".","");
                                $loyers["loyer"][$vl["ordre"]]["duree"] = $vl["duree"];
                                $loyers["loyer"][$vl["ordre"]]["loyer"] += (number_format(($vl["loyer"]*$value["tva_loyer"]),2,".","")*($qte*$qte_produit_princ));
                                $loyers["loyer"][$vl["ordre"]]["nature"] = $vl["nature"];
                            }
                        }
                    }
                }
            }
        }

        $loyer = $devis = $produits = array();
        foreach ($loyers["loyer"] as $key => $value) {
            $loyer[] = array(
                "loyer__dot__loyer" => $value["loyer"],
                "loyer__dot__duree" => $value["duree"],
                "loyer__dot__assurance" => NULL,
                "loyer__dot__frais_de_gestion" => NULL ,
                "loyer__dot__frequence_loyer"=>"mois",
                "loyer__dot__nature"=>$value["nature"],
                "loyer__dot__loyer_total"=> $value["duree"]*$value["loyer"], //somme des loyers
            );
            $prix += $value["duree"]*$value["loyer"];
        }



        $prix_achat = $prix = 0;
        $devis = array(
            "id_societe" => $infos["id_societe"],
            "type_contrat"   => "lld",
            "devis" => $infos["panier"]["pack"]["libelle"],
            "tva" => "1.2",
            "validite" => date("Y-m-d",strtotime("+15 day")),
            "date" => date("d-m-Y"),
            //"type_devis"=> "normal",
            "id_contact" => $id_contact,
            "prix_achat" => $prix_achat,//somme des prix_achat des produits
            "prix" => $prix,//somme des loyers * duree
            "adresse_facturation"=>$adresse_facturation ,
            "adresse_facturation_2"=>$adresse_facturation_2 ,
            "adresse_facturation_3"=>$adresse_facturation_3 ,
            "cp_adresse_facturation"=>$cp_facturation ,
            "ville_adresse_facturation"=>$ville_facturation ,
            "pays_facturation"=>$pays_facturation,
            "adresse_livraison"=>$adresse_livraison ,
            "adresse_livraison_2"=>$adresse_livraison_2 ,
            "adresse_livraison_3"=>$adresse_livraison_3 ,
            "cp_adresse_livraison"=>$cp_livraison,
            "ville_adresse_livraison"=>$ville_livraison,
            "pays_livraison"=>$pays_livraison
        );

        if($infos["id_magasin"]) $devis["id_magasin"] = $infos["id_magasin"];

        foreach ($loyers["produits"] as $k => $v) {
            if($qte = $infos["panier"]["product"][$v["id_produit"]]["quantite"]){
                $prod = ATF::produit()->select($v["id_produit"]);
                $produits[] = array(
                    "devis_ligne__dot__produit"=>$v["produit"],
                    "devis_ligne__dot__quantite"=>$qte,
                    "devis_ligne__dot__type"=>$v["type"],
                    "devis_ligne__dot__ref"=>$v["ref_lm"],
                    "devis_ligne__dot__prix_achat"=>$v["prix_achat_ht"],
                    "devis_ligne__dot__id_produit"=>$v["id_produit"],
                    "devis_ligne__dot__id_fournisseur"=>"LM",
                    "devis_ligne__dot__visibilite_prix"=>"visible",
                    "devis_ligne__dot__date_achat"=>"",
                    "devis_ligne__dot__commentaire"=>$v["commentaire"],
                    "devis_ligne__dot__neuf"=>"oui",
                    "devis_ligne__dot__id_produit_fk"=>$v["id_produit"],
                    "devis_ligne__dot__id_fournisseur_fk"=>$v["id_fournisseur"],
                    "devis_ligne__dot__visible"=>$v["afficher"]
                );
                $produits_commande[] = array(
                    "commande_ligne__dot__produit"=>$v["produit"],
                    "commande_ligne__dot__quantite"=>$qte,
                    "commande_ligne__dot__ref"=>$v["ref_lm"],
                    "commande_ligne__dot__prix_achat"=>$v["prix_achat_ht"],
                    "commande_ligne__dot__id_produit"=>$v["id_produit"],
                    "commande_ligne__dot__id_fournisseur"=>"LM",
                    "commande_ligne__dot__date_achat"=>"",
                    "commande_ligne__dot__commentaire"=>$v["commentaire"],
                    "commande_ligne__dot__neuf"=>"oui",
                    "commande_ligne__dot__id_produit_fk"=>$v["id_produit"],
                    "commande_ligne__dot__id_fournisseur_fk"=>$v["id_fournisseur"],
                    "commande_ligne__dot__visible"=>$v["afficher"]
                );
                $prix_achat +=  $prod["prix_achat_ht"];
            }
        }

        $devis["prix_achat"] = $prix_achat;
        $devis["prix"] = $prix;

        $values_devis = array("loyer"=>json_encode($loyer),"produits"=>json_encode($produits));
        $data = array("devis"=>$devis, "values_devis"=>$values_devis);


        if ($id_devis = ATF::devis()->insert($data)) {

            // On génère le contrat directement
            $values_commande = array(
                "loyer"=>json_encode($loyer),
                "produits"=>json_encode($produits_commande)
            );
            $commande = array(
                "id_societe" => $infos["id_societe"],
                "type"   => "prelevement",
                "commande" => $infos["panier"]["pack"]["libelle"],
                "date" => date("d-m-Y"),
                "id_devis" => $id_devis,
                "id_affaire" => ATF::devis()->select($id_devis,"id_affaire"),
                "clause_logicielle" => "non",
                "prix_achat" => $prix_achat,//somme des prix_achat des produits
                "prix" => $prix,//somme des loyers * duree
                //"marge_absolue" => 0, //prix - prix_achat
                //"marge" => 0, //?
                "etat"=>"pending",
                "from_web"=>true
            );
            $data = array("commande"=>$commande, "values_commande"=>$values_commande);

            $id_commande = ATF::commande()->insert($data);
            $id_commande = ATF::commande()->decryptId($id_commande);

            echo $id_commande;
        } else {
            throw new Exception("aucun id_devis !");
        }

    }catch(errorATF $e){
        unset($_POST["societe_form"]["password"]);
        $mail = new mail(array(
                "recipient"=>"benjamin.tronquit@leroymerlin.fr"
                //"recipient"=>"debug@absystech.fr"
                ,"objet"=>"Une erreur s'est produite lors d'une souscription"
                ,"html"=>true
                ,"template"=>"erreur"
                ,'erreur'=>$e
                ,'data'=>array("Client"=>$_POST["societe_form"], "Pack"=>$infos["panier"]["pack"]["libelle"])
                ,"from"=>"contact@abonnement.leroymerlin.fr"));

        $mail->send();

        log::logger($e->getMessage(),'lm');
        echo "Erreur, merci de contacter le service client.";
    }
}