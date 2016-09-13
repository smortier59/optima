<?
$_SERVER["argv"][1] = $_POST["codename"];
include(dirname(__FILE__)."/../global.inc.php");

ATF::$codename = $_POST["codename"];

ATF::$usr = new usr("18");
ATF::_s("user",ATF::$usr);

$panier = $_POST["panier"];
$infos = $_POST["infos"];

if(isset($_POST["generercontrat"])){
    $id_devis = $_POST["generercontrat"]["id_devis"];    

    $devis = ATF::devis()->select($id_devis);

    $marge = $devis["prix"] - $devis["prix_achat"];
    $margeabsolue = ($marge/$devis["prix"])*100;

    ATF::loyer()->q->reset()->where("id_affaire",$devis["id_affaire"]);
    $loyer = ATF::loyer()->select_row();

   
    $fields=array(    "devis_ligne.produit"
                    , "devis_ligne.id_devis_ligne"
                    , "devis_ligne.quantite"
                    , "devis_ligne.ref"
                    , "devis_ligne.id_fournisseur_fk"
                    , "devis_ligne.prix_achat"
                    , "devis_ligne.code"
                    , "devis_ligne.id_produit_fk"
                    , "devis_ligne.commentaire");

    ATF::devis_ligne()->q->reset()->addCondition("id_devis",$devis["id_devis"])
                             ->addCondition("id_affaire_provenance",null,null,false,"IS NULL")
                             ->addCondition("visible","oui","AND");
   
    $produits = ATF::devis_ligne()->toCommandeLigne();

    //log::logger($produits , "mfleurquin");

    unset($produits["data"]);
    foreach ($produits as $kRow => $row) {
        foreach ($row as $kCol => $value) {            
            if( in_array($kCol, $fields)){                                   
                if($kCol == "devis_ligne.id_devis_ligne"){
                    $return[$kRow]["commande_ligne__dot__id_commande_ligne"]=$value;
                }else{
                    $return[$kRow][str_replace("devis_ligne.","commande_ligne__dot__",$kCol)]=$value;
                }
            }            
        }
    }

   // log::logger($return , "mfleurquin");


    foreach ($loyer as $kRow => $row) {
        foreach ($row as $kCol => $value) {
            $return[$kRow][str_replace("devis_ligne.","commande_ligne__dot__",$kCol)]=$value;
        }
    }

    $produits = array();
    $produits = $return;    

    $commande["extAction"] = "commande";
    $commande["extMethod"] = "insert";


    $commande["commande"] = array("commande"=> $devis["ref"] ,
                                  "type"=> "prelevement",
                                  "id_societe"=> $devis["id_societe"],
                                  "date"=> date("Y-m-d"),
                                  "id_affaire"=> $devis["id_affaire"],
                                  "clause_logicielle"=> "non",
                                  "prix"=> $devis["prix"],
                                  "prix_achat"=> $devis["prix_achat"],
                                  "marge"=> $marge,
                                  "marge_absolue"=>$margeabsolue ,
                                  "date_demande_resiliation"=> NULL,
                                  "date_restitution_effective"=> NULL,
                                  "date_prevision_restitution"=> NULL,
                                  "id_devis"=> $devis["id_devis"]
                                );
    $commande["values_commande"] = array( 
                                   "loyer"=> json_encode($loyer),
                                   "produits" => json_encode($produits),
                                   "produits_repris" => "",
                                   "produits_non_visible" => ""
                                 );



    return ATF::commande()->insert($commande);
 

     
}else{

    if($infos["societe"] && $infos["siret"] && $infos["adresse"] && $infos["cp"] && $infos["ville"]  &&  $infos["civilite"] 
    && $infos["nom"] && $infos["prenom"] &&  $infos["email"] &&  $infos["tel"]){

        try{
            ATF::societe()->q->reset()->where('replace(siret, " ", "")' , str_replace(" ", "", $infos['siret']));
            $soc = ATF::societe()->select_row();
            
            if(!$soc){
                 $societe = array("societe" => $infos["societe"],
                                 "siret"   => $infos["siret"],
                                 "adresse" => $infos["adresse"],
                                 "cp"      => $infos["cp"],
                                 "ville"   => $infos["ville"],
                                 "cs_score"=> $infos["cs_score"]
                            );
                $id_societe = ATF::societe()->insert($societe);
            }else{
                $id_societe = $soc["id_societe"];
                ATF::societe()->u(array("id_societe"=>$soc["id_societe"] , "cs_score"=>$infos["cs_score"] )); 

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

            $devis = array();
            
            $prix_achat = $prix = 0;
           
            foreach ($panier as $key => $value) {
                $pack = ATF::pack_produit()->select($value["pack"]);
                $loyers[$pack["duree"]] += $pack["loyer"]*$value["quantite"];

                ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit", $value["pack"]);
                foreach (ATF::pack_produit_ligne()->select_all() as $k => $v) {
                    $pl[$v["id_produit"]] += $value["quantite"]*$v["quantite"];

                    $prix_achat += $v["prix_achat"];
                }

            }
            
            foreach ($loyers as $key => $value) {
                $loyer[] = array(   "loyer__dot__loyer" => $value,
                                    "loyer__dot__duree" => $key,
                                    "loyer__dot__assurance" => NULL,
                                    "loyer__dot__frais_de_gestion" => NULL ,
                                    "loyer__dot__frequence_loyer"=>"mois",
                                    "loyer__dot__loyer_total"=> $key*$value, //somme des loyers
                                );
                $prix += $key*$value;
            }
        
          

            
            $devis = $produits = array();

            

            $devis = array( "id_societe" => $id_societe,
                            "type_contrat"   => "lld",
                            "devis" => "devis_top_office_".date("ymdHi"),
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
            
            foreach ($pl as $k => $v) {
                $prod = ATF::produit()->select($k);

                $produits[] = array("devis_ligne__dot__produit"=>$prod["produit"],
                                    "devis_ligne__dot__quantite"=>$v,
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


            $values_devis = array( "loyer" => json_encode($loyer),
                                   "produits" => json_encode($produits)
                                );
            $data = array("devis"=>$devis, "values_devis"=>$values_devis);

            $id_devis =  ATF::devis()->insert($data);
            
            $devis = ATF::devis()->select($id_devis);

            $marge = $devis["prix"] - $devis["prix_achat"];
            $margeabsolue = ($marge/$devis["prix"])*100;

            ATF::loyer()->q->reset()->where("id_affaire",$devis["id_affaire"]);
            $loyer = ATF::loyer()->select_row();

           
            $fields=array(    "devis_ligne.produit"
                            , "devis_ligne.id_devis_ligne"
                            , "devis_ligne.quantite"
                            , "devis_ligne.ref"
                            , "devis_ligne.id_fournisseur_fk"
                            , "devis_ligne.prix_achat"
                            , "devis_ligne.code"
                            , "devis_ligne.id_produit_fk"
                            , "devis_ligne.commentaire");

            ATF::devis_ligne()->q->reset()->addCondition("id_devis",$devis["id_devis"])
                                     ->addCondition("id_affaire_provenance",null,null,false,"IS NULL")
                                     ->addCondition("visible","oui","AND");
           
            $produits = ATF::devis_ligne()->toCommandeLigne();

            //log::logger($produits , "mfleurquin");

            unset($produits["data"]);
            foreach ($produits as $kRow => $row) {
                foreach ($row as $kCol => $value) {            
                    if( in_array($kCol, $fields)){                                   
                        if($kCol == "devis_ligne.id_devis_ligne"){
                            $return[$kRow]["commande_ligne__dot__id_commande_ligne"]=$value;
                        }else{
                            $return[$kRow][str_replace("devis_ligne.","commande_ligne__dot__",$kCol)]=$value;
                        }
                    }            
                }
            }

           // log::logger($return , "mfleurquin");


            foreach ($loyer as $kRow => $row) {
                foreach ($row as $kCol => $value) {
                    $return[$kRow][str_replace("devis_ligne.","commande_ligne__dot__",$kCol)]=$value;
                }
            }

            $produits = array();
            $produits = $return;    

            $commande["extAction"] = "commande";
            $commande["extMethod"] = "insert";


            $commande["commande"] = array("commande"=> $devis["ref"] ,
                                          "type"=> "prelevement",
                                          "id_societe"=> $devis["id_societe"],
                                          "date"=> date("Y-m-d"),
                                          "id_affaire"=> $devis["id_affaire"],
                                          "clause_logicielle"=> "non",
                                          "prix"=> $devis["prix"],
                                          "prix_achat"=> $devis["prix_achat"],
                                          "marge"=> $marge,
                                          "marge_absolue"=>$margeabsolue ,
                                          "date_demande_resiliation"=> NULL,
                                          "date_restitution_effective"=> NULL,
                                          "date_prevision_restitution"=> NULL,
                                          "id_devis"=> $devis["id_devis"]
                                        );
            $commande["values_commande"] = array( 
                                           "loyer"=> json_encode($loyer),
                                           "produits" => json_encode($produits),
                                           "produits_repris" => "",
                                           "produits_non_visible" => ""
                                         );



            ATF::commande()->insert($commande);


            echo $id_devis;
        }catch(errorATF $e){
            echo $e->getMessage();
        }
    }else{  
        echo false;
    }


}


?>