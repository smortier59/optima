<?
$_SERVER["argv"][1] = $_POST["codename"];
include(dirname(__FILE__)."/../global.inc.php");

ATF::$codename = $_POST["codename"];

ATF::$usr = new usr("18");
ATF::_s("user",ATF::$usr);

$panier = $_POST["panier"];
$infos = $_POST["infos"];


if($infos["societe"] && $infos["siret"] && $infos["adresse"] && $infos["cp"] && $infos["ville"]  &&  $infos["civilite"] && $infos["nom"] && $infos["prenom"] &&  $infos["email"] &&  $infos["tel"]){

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

        ATF::contact()->q->reset()->where("nom",$infos['nom'],"AND")->where("prenom",$infos['prenom']);

        $contact = ATF::contact()->select_row();
        if($contact){
            $id_contact = $contact["id_contact"];
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

        /*$devis = array();

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
                $prod = ATF::produit()->select($v["produit"]);

                $produits[] = array("devis_ligne__dot__produit"=>$prod["produit"],
                                    "devis_ligne__dot__quantite"=>$v["quantite"],
                                    "devis_ligne__dot__type"=>$prod["type"],
                                    "devis_ligne__dot__ref"=>$prod["ref"],
                                    "devis_ligne__dot__prix_achat"=>$prod["prix_achat"],
                                    "devis_ligne__dot__id_produit"=>$prod["id_produit"],
                                    "devis_ligne__dot__id_fournisseur"=>"1FOTEAM",
                                    "devis_ligne__dot__visibilite_prix"=>"visible",
                                    "devis_ligne__dot__date_achat"=>"",
                                    "devis_ligne__dot__commentaire"=>$prod["commentaire"],
                                    "devis_ligne__dot__neuf"=>"oui",
                                    "devis_ligne__dot__id_produit_fk"=>$prod["id_produit"],
                                    "devis_ligne__dot__id_fournisseur_fk"=>$prod["id_fournisseur"]);
                $prix_achat +=  $prod["prix_achat"];
            }

            $devis["prix_achat"] = $prix_achat;
            $devis["prix"] = $prix;
            if($_POST["codename"] == "cleodis"){ $devis["marge"] = $prix - $prix_achat;  }       
            //$devis["marge_absolue"] = 0;



            $values_devis = array( "loyer" => json_encode($loyer),
                                   "produits" => json_encode($produits)
                                );
            $data = array("devis"=>$devis, "values_devis"=>$values_devis);


            ATF::devis()->insert($data);
        }  */      

        return true;
    }catch(error $e){  
        print_r($e);
        die();      
        return $e->getMessage();
    }       
}else{    
    return false;
}




?>