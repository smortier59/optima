<?

$_SERVER["argv"][1] = 'lm';
include(dirname(__FILE__)."/../global.inc.php");

ATF::$codename = $_SERVER["argv"][1];

ATF::$usr = new usr(18);
ATF::_s("user",ATF::$usr);

$infos = $_POST;

log::logger("DEVIS LM INSERT", "mfleurquin");
log::logger($infos, "mfleurquin");


if ($infos["id_contrat"]) {
    ATF::pdf()->generic('contratA4',$infos["id_contrat"]);    
    die;
}


if($infos["save_contrat"]){
    log::logger("Insert PDF", "mfleurquin");    
    util::file_put_contents(ATF::commande()->filepath($infos["id_commande"],"retour"), base64_decode($infos["pdf"]));
    $id_pdf_affaire = ATF::pdf_affaire()->insert(array("id_affaire"=>$infos["id_affaire"], "provenance"=>"Contrat signé par SLIMPAY"));
    copy(ATF::commande()->filepath($infos["id_commande"],"retour"), ATF::pdf_affaire()->filepath($id_pdf_affaire,"fichier_joint"));
    log::logger("AJOUT Date retour contrat & AP", "mfleurquin");
    ATF::commande()->u(array("id_commande"=>$infos["id_commande"], "retour_contrat"=>date("Y-m-d") , "retour_prel"=>date("Y-m-d")));
    die;    
}

if($infos["id_societe"]){
    try{
        $societe = ATF::societe()->select($infos["id_societe"]);

        if($infos["societe_form"]["adresse"]){
            $adresse_livraison = $infos["societe_form"]["adresse"];
            if($infos["societe_form"]["adresse_2"] )  $adresse_livraison .= "\n".$infos["societe_form"]["adresse_2"];
            if($infos["societe_form"]["adresse_3"] )  $adresse_livraison .= "\n".$infos["societe_form"]["adresse_3"];
            
            $cp_livraison = $infos["societe_form"]["cp"];
            $ville_livraison = $infos["societe_form"]["ville"];

            $adresse_facturation = "";
            if($infos["societe_form"]["facturation_same_livraisonCheckbox"] == "non"){
                $adresse_facturation = $infos["societe_form"]["facturation_adresse"];
                if($infos["societe_form"]["facturation_adresse_2"] )  $adresse_facturation .= "\n".$infos["societe_form"]["facturation_adresse_2"];
                if($infos["societe_form"]["facturation_adresse_3"] )  $adresse_facturation .= "\n".$infos["societe_form"]["facturation_adresse_3"];
                $cp_facturation = $infos["societe_form"]["facturation_cp"];
                $ville_facturation =  $infos["societe_form"]["ville"];

            }else{
                $adresse_facturation = $adresse_livraison;
                $cp_facturation = $cp_livraison;
                $ville_facturation =  $ville_livraison;
            }
        }else{
            $adresse_livraison = $societe["adresse"];
            if($societe["adresse_2"] )  $adresse_livraison .= "\n".$societe["adresse_2"];
            if($societe["adresse_3"] )  $adresse_livraison .= "\n".$societe["adresse_3"];
            
            $cp_livraison = $societe["cp"];
            $ville_livraison = $societe["ville"];

            $adresse_facturation = $adresse_livraison;
            $cp_facturation = $cp_livraison;
            $ville_facturation =  $ville_livraison;
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
                    $loyers["produits"][$key]["loyer"][$vl["ordre"]]["loyer"] = number_format(($vl["loyer"]*$value["tva_loyer"]),2);
                    $loyers["loyer"][$vl["ordre"]]["duree"] = $vl["duree"];
                    $loyers["loyer"][$vl["ordre"]]["loyer"] += (number_format(($vl["loyer"]*$value["tva_loyer"]),2)*$qte);
                    $loyers["loyer"][$vl["ordre"]]["nature"] = $vl["nature"];
                   
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
            "cp_adresse_facturation"=>$cp_facturation ,
            "ville_adresse_facturation"=>$ville_facturation ,
            "adresse_livraison"=>$adresse_livraison ,
            "cp_adresse_livraison"=>$cp_livraison,
            "ville_adresse_livraison"=>$ville_livraison          
        );

        foreach ($loyers["produits"] as $k => $v) {
            if($qte = $infos["panier"]["product"][$v["id_produit"]]["quantite"]){
                $prod = ATF::produit()->select($v["id_produit"]);
                $produits[] = array(
                    "devis_ligne__dot__produit"=>$v["produit"],
                    "devis_ligne__dot__quantite"=>$qte,
                    "devis_ligne__dot__type"=>$v["type"],
                    "devis_ligne__dot__ref"=>$v["ref"],
                    "devis_ligne__dot__prix_achat"=>$v["prix_achat_ht"],
                    "devis_ligne__dot__id_produit"=>$v["id_produit"],
                    "devis_ligne__dot__id_fournisseur"=>"LM",
                    "devis_ligne__dot__visibilite_prix"=>"visible",
                    "devis_ligne__dot__date_achat"=>"",
                    "devis_ligne__dot__commentaire"=>$v["commentaire"],
                    "devis_ligne__dot__neuf"=>"oui",
                    "devis_ligne__dot__id_produit_fk"=>$v["id_produit"],
                    "devis_ligne__dot__id_fournisseur_fk"=>$v["id_fournisseur"]
                );
                $produits_commande[] = array(
                    "commande_ligne__dot__produit"=>$v["produit"],
                    "commande_ligne__dot__quantite"=>$qte,
                    "commande_ligne__dot__ref"=>$v["ref"],
                    "commande_ligne__dot__prix_achat"=>$v["prix_achat_ht"],
                    "commande_ligne__dot__id_produit"=>$v["id_produit"],
                    "commande_ligne__dot__id_fournisseur"=>"LM",
                    "commande_ligne__dot__date_achat"=>"",
                    "commande_ligne__dot__commentaire"=>$v["commentaire"],
                    "commande_ligne__dot__neuf"=>"oui",
                    "commande_ligne__dot__id_produit_fk"=>$v["id_produit"],
                    "commande_ligne__dot__id_fournisseur_fk"=>$v["id_fournisseur"]
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

    }catch(error $e){
        log::logger($e->getMessage(),'lm');
        echo "Erreur, merci de contacter le service client.";
    }
}