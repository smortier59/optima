<?
/**
* Classe souscription pour les fonctions métier du Tunnel de souscription de cléodis
* @package Optima
* @subpackage Cléodis
*/
require_once dirname(__FILE__)."/../souscription.class.php";
class souscription_cleodis extends souscription {

  public $id_user = 16; // ID Du user qui sera en créateur des éléments
  public $id_agence = 1; // ID De l'agence qui sera attaché aux éléments
  public $fournisseur = 246; // ID Du fournisseur par défaut qui sera attaché aux éléments DEFAULT : cléodis

  public $codename = "cleodis"; // Utile pour le stockage des fichiers lors de la récuperation des fichiers signés

  public $id_partenaire = NULL;

  public $id_refinanceur_cleodis = 4;


  /*--------------------------------------------------------------*/
  /*                   Constructeurs                              */
  /*--------------------------------------------------------------*/
  public function __construct() {
    parent::__construct();
    $this->table = "affaire";
  }

  /**
   * Permet l'insertion de l'affaire et du devis en provenance du tunnel de souscription
   * @author Quentin JANON <qjanon@absystech.fr>
   */
  public function _devis($get, $post) {
    ATF::$usr->set('id_user',$post['id_user'] ? $post['id_user'] : $this->id_user);
    ATF::$usr->set('id_agence',$post['id_agence'] ? $post['id_agence'] : $this->id_agence);
    $email = $post["email"];
    $societe = ATF::societe()->select($post["id_societe"]);


    switch ($post['site_associe']) {
      case 'boulangerpro':
        ATF::societe()->q->reset()->where("siret", "45122067700087");
        $boulpro = ATF::societe()->select_row();
        $this->id_partenaire = $boulpro["id_societe"];
      break;

      case 'btwin':
        $this->id_partenaire = 29109; // ID de la société DECATHLON BTWIN (same in RCT - PROD - DEV)
      break;

      case 'bdomplus':
        $this->id_partenaire = 31458; // ID de la société BDOM PLUS (same in RCT - PROD - DEV)
      break;
    }

    if(!$post["no_iban"]){
      $this->checkIBAN($post['iban']);
    }


    ATF::db($this->db)->begin_transaction();
    try {
      // Gestion du code client
      $codeClient = $societe['code_client'];


      if (!$codeClient) {
        // Modification de la société pour lui générer sa ref si elle n'est pas déjà setté
        $codeClient = ATF::societe()->getCodeClient($societe, self::getPrefixCodeClient($post['site_associe']));
        $toUpdate = array(
          'id_societe' => $societe["id_societe"],
          'code_client' => $codeClient
        );
        ATF::societe()->u($toUpdate);
      }


      // On update le signataire de la societe pour y mettre celui qu'on reçoit.
      if ($post['id_contact']) {
        $toUpdate = array(
          'id_societe' => $societe["id_societe"],
          'id_contact_signataire' => $post['id_contact']
        );
        ATF::societe()->u($toUpdate);
      }

      //On check les durées sur chaque pack pour regrouper/affaire
      $lignes = json_decode($post["produits"], true);
      $post["produits"] = $affaires = $lignes_par_duree = array();


      foreach ($lignes as $key => $value) {
        $duree = ATF::pack_produit()->getDureePack($value["id_pack_produit"]);
        $lignes_par_duree[$duree][] = $value;
      }

      foreach ($lignes_par_duree as $key => $value) {
        $post["produits"] = json_encode($value);

        //On récupère les id pack de chaque ligne pour le libelle de l'affaire
        $post['id_pack_produit'] = array();
        foreach ($value as $k => $v) {
          $post['id_pack_produit'][] = $v["id_pack_produit"];
        }

        // On retire les doublons
        $post['id_pack_produit'] = array_unique($post['id_pack_produit']);

        // On génère le libellé du devis a partir des pack produit
        $libelle = $this->getLibelleAffaire($post['id_pack_produit'], $post['site_associe']);

        $id_devis = $this->createDevis($post, $libelle);

        ATF::devis()->q->reset()->addField('devis.id_affaire','id_affaire')->where('devis.id_devis', $id_devis);
        $id_affaire = ATF::devis()->select_cell();

        ATF::affaire()->q->reset()->addField('affaire.ref','ref')->where('affaire.id_affaire', $id_affaire);
        $ref_affaire = ATF::affaire()->select_cell();



        $affaires["ids"][] = $id_affaire;
        $affaires["refs"][] = $ref_affaire;
        $nameVendeur = false;
        // Il faut absolument laissé le  && $post['vendeur']!="null", sinon on va péter BDOM ;)
        if ($post['vendeur'] && $post['vendeur']!="null" && $post['vendeur']['nameid'] && $post['site_associe'] == 'bdomplus') {
          log::logger("A priori on aurait un vendeur magasin BDOM !", "souscription");
          log::logger($post['vendeur'], "souscription");
          $this->envoiMailVendeurABenjamin($affaires, $post['vendeur']);
          // Sélection d'un magasin au hasard
          $vendeur = json_decode($post['vendeur'], true);
          ATF::magasin()->q->reset()->where('code', 'F'.$vendeur['siteId'])->setLimit(1);
          $magasin = ATF::magasin()->select_row();
          $nameVendeur = $vendeur['displayName'];
          if (!$magasin) {
            log::logger("MAGASIN !!NON!! IDENTIFIE avec le siteId / code : ".$vendeur['siteId'].", il faut le créer.", "souscription");
            $id_magasin = ATF::magasin()->i(array(
              "magasin"=>$vendeur['siteName'],
              "code"=>'F'.$vendeur['siteId'],
              "site_associe"=>$post['site_associe']
            ));
            $magasin = ATF::magasin()->select($id_magasin);

          } else {
            log::logger("MAGASIN IDENTIFIE avec le siteId / code : ".$vendeur['siteId'], "souscription");
          }
          log::logger($magasin['magasin']."(".$magasin['id_magasin'].")", "souscription");
          $post['id_magasin'] = $magasin['id_magasin'];

        }

        // MAJ de l'affaire avec les bons site_associé et le bon etat comité
        $affToUpdate = array(
          "id_affaire"=>$id_affaire,
          "id_partenaire"=>$this->id_partenaire,
          "id_panier"=>$post['id_panier'],
          "hash_panier"=>$post['hash_panier'],
          "site_associe"=>$post['site_associe'],
          "provenance"=>$post['site_associe'],
          "etat_comite"=>"accepte",
          "adresse_livraison"=>$post['livraison']['adresse'],
          "adresse_livraison_2"=>$post['livraison']['adresse_2'],
          "cp_adresse_livraison"=>$post['livraison']['cp'],
          "ville_adresse_livraison"=>$post['livraison']['ville'],
          "adresse_facturation"=>$post['facturation']['adresse'],
          "adresse_facturation_2"=>$post['facturation']['adresse_2'],
          "cp_adresse_facturation"=>$post['facturation']['cp'],
          "ville_adresse_facturation"=>$post['facturation']['ville'],
          "id_magasin"=>$post["id_magasin"]
        );
        // ajout du vendeur pour bdomplus
        if ($post['site_associe'] == 'bdomplus' && $nameVendeur) {
          $affToUpdate['vendeur'] = $nameVendeur;
        }


        if($post["facture"]) ATF::facture_magasin()->i(array("id_affaire"=> $id_affaire, "ref_facture"=> strtoupper($post["facture"])));

        // On stock le JSON du pack complet au cas où.
        if ($post['id_pack_produit']) {
          foreach ($post['id_pack_produit'] as $id_pack_produit) {
            $pack_produit = ATF::pack_produit()->select($id_pack_produit);
            if (!$pack_produit) continue;
            $pack_produit['lignes'] = ATF::pack_produit_ligne()->select_special('id_pack_produit', $id_pack_produit);
            foreach ($pack_produit['lignes'] as $k=>$ligne) {
              $pack_produit['lignes'][$k]['produit'] = ATF::produit()->select($ligne['id_produit']);
            }
          }
          $affToUpdate['snapshot_pack_produit'] = json_encode($pack_produit, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT);
        }

        //Il ne faut pas écraser le RUM si il n'y en a pas sur le client (arrive lors de la 1ere affaire pour ce client)
        //if($societe["RUM"]) $affToUpdate["RUM"]=$societe["RUM"]; //Inutile le travail est fait dans devis->insert()
        ATF::affaire()->u($affToUpdate);

        if ($post['id_panier']) {
          ATF::panier()->u(array("id_panier"=>$post['id_panier'],"id_affaire"=>$id_affaire));
        }

        if($post["site_associe"] === "btwin"){
          $noticeAssurance = ATF::pdf()->generic("noticeAssurance",$id_affaire,true);
          ATF::affaire()->store($s, $id_affaire, "noticeAssurance", $noticeAssurance);
        }


        switch ($post["site_associe"]) {
          case 'boulangerpro':
            $this->createComite($id_affaire, $societe, "accepte", "Comité CreditSafe", date("Y-m-d"), date("Y-m-d"));
            $this->createComite($id_affaire, $societe, "en_attente", "Comité CLEODIS");
          break;

          case 'bdomplus':
            $this->createComite($id_affaire, $societe, "accepte", "Comité CLEODIS", date("Y-m-d"), date("Y-m-d"));
          break;

          default:
          break;
        }


        // Création du contrat
        $id_contrat = $this->createContrat($post, $libelle, $id_devis, $id_affaire);
      }

    } catch (errorATF $e) {
        ATF::db($this->db)->rollback_transaction();

        throw $e;
    }
    ATF::db($this->db)->commit_transaction();

    return $affaires;
  }

  /**
   * Génère le libellé d'une afaire en fonction des pack produit sélectionné
   * @author Quentin JANON <qjanon@absystech.fr>
   * @param  array $id_pack_produits Ensemble des id_pack_produit
   * @return String                   Libellé de l'affaire
   */
  private function getLibelleAffaire ($id_pack_produits, $site_associe) {
    if ($id_pack_produits) {
      ATF::pack_produit()->q->reset()
          ->addField("GROUP_CONCAT(pack_produit.nom SEPARATOR ' + ')")
          ->setStrict()
          ->setLimit(1);
      foreach ($id_pack_produits as $id_pack) {
          ATF::pack_produit()->q->where("id_pack_produit", $id_pack);
      }

      $suffix = ATF::pack_produit()->select_cell();
    }

    switch ($site_associe) {
      case "btwin":
        $r = "BTWIN - Location ".$suffix;
      break;
      case "boulangerpro":
        $r = "BOULANGER PRO - Location ".$suffix;
      break;

      case "bdomplus":
        $r = "Abonnement Zen ".$suffix;
      break;
    }

    return $r;
  }

  /**
   * Créer le devis
   * @author Quentin JANON <qjanon@absystech.fr>
   * @param  Array $post    [description]
   * @param  [type] $libelle [description]
   * @return [type]          [description]
   */
  private function createDevis ($post, $libelle, $fournisseur) {
    // Construction du devis
    $devis = array(
        "id_societe" => $post['id_societe'],
        "type_contrat" => "lld",
        "validite" => date("d-m-Y", strtotime("+1 month")),
        "tva" => __TVA__,
        "devis" => $libelle,
        "date" => date("d-m-Y"),
        "type_devis" => "normal",
        "id_contact" => $post["id_contact"],
        "prix_achat"=>0,
        "type_affaire" => "normal",
        "IBAN"=> $post["iban"],
        "BIC"=> $post["bic"]
    );

    // COnstruction des lignes de devis a partir des produits en JSON
    $values_devis =array();
    $produits = json_decode($post['produits'], true);

    // Gestion des lignes de devis / produits
    $toInsertProduitDevis = array();
    // Loyer unique
    $toInsertLoyer[0] = array(
        "loyer__dot__loyer"=> 0,
        "loyer__dot__duree"=> 0,
        "loyer__dot__type"=>"engagement",
        "loyer__dot__assurance"=>"",
        "loyer__dot__frais_de_gestion"=>"",
        "loyer__dot__frequence_loyer"=> "mois",
        "loyer__dot__serenite"=>"",
        "loyer__dot__maintenance"=>"",
        "loyer__dot__hotline"=>"",
        "loyer__dot__supervision"=>"",
        "loyer__dot__support"=>"",
        "loyer__dot__avec_option"=>"non"
    );



    foreach ($produits as $k=>$produit) {
        ATF::produit()->q->reset()
          ->addField("loyer")
          ->addField("duree")
          ->addField("ean")
          ->addField("id_sous_categorie")
          ->addField("visible_sur_site")
          ->addField("prix_achat")
          ->addField("id_fournisseur")
          ->where("id_produit", $produit['id_produit']);
        $produitLoyer = ATF::produit()->select_row();

        if ($produit['id_pack_produit']) {
          $id_pack = $produit['id_pack_produit'];

          if($post["site_associe"] == "bdomplus"){
            $toInsertLoyer[0]["loyer__dot__frequence_loyer"] = ATF::pack_produit()->select($id_pack, "frequence");

            switch ($toInsertLoyer[0]["loyer__dot__frequence_loyer"]) {
              case 'mois':
                $devis["devis"] = $libelle." Mensuel";
              break;
               case 'an':
                 $devis["devis"] = $libelle." Annuel";
              break;
            }
          }


          //Il faut récupérer l'affichage sur PDF
          ATF::pack_produit_ligne()->q->reset()
                                   ->where("id_pack_produit", $produit['id_pack_produit'])
                                   ->where("id_produit", $produit['id_produit']);
          $packProduitLigne = ATF::pack_produit_ligne()->select_row();

        }

        // On force le prix d'achat en provenance des produit et non des lignes !
        $packProduitLigne['prix_achat'] = $produitLoyer['prix_achat'];

        $produitLoyer = array_merge($produitLoyer,$packProduitLigne);
        $souscategorie = ATF::sous_categorie()->select($produitLoyer['id_sous_categorie']);

        if ($toInsertProduitDevis[$produit['id_produit'].'-'.$produit['serial']]) {
          $toInsertProduitDevis[$produit['id_produit'].'-'.$produit['serial']]['devis_ligne__dot__quantite'] += $produit['quantite'];
        } else {
          $toInsertProduitDevis[$produit['id_produit'].'-'.$produit['serial']] =  array(
            "devis_ligne__dot__produit"=> $produit['produit'],
            "devis_ligne__dot__quantite"=>$produit['quantite'],
            "devis_ligne__dot__type"=>$produitLoyer['type'],
            "devis_ligne__dot__ref"=>$produit['ref'],
            "devis_ligne__dot__prix_achat"=>$produitLoyer["prix_achat"],
            "devis_ligne__dot__id_produit"=>$produit['produit'],
            "devis_ligne__dot__id_fournisseur"=>$produitLoyer['id_fournisseur'] ? $produitLoyer['id_fournisseur'] : $this->id_fournisseur,
            "devis_ligne__dot__visibilite_prix"=>"invisible",
            "devis_ligne__dot__date_achat"=>"",
            "devis_ligne__dot__commentaire"=>"",
            "devis_ligne__dot__neuf"=>"oui",
            "devis_ligne__dot__serial"=>$produit['serial'] ? $produit['serial'] : '',
            "devis_ligne__dot__id_produit_fk"=>$produit['id_produit'],
            "devis_ligne__dot__id_fournisseur_fk"=>$produitLoyer['id_fournisseur'] ? $produitLoyer['id_fournisseur'] : $this->id_fournisseur,
            "devis_ligne__dot__duree"=>$produitLoyer['duree'],
            "devis_ligne__dot__loyer"=>$produitLoyer['loyer'],
            "devis_ligne__dot__id_sous_categorie"=>$produitLoyer['id_sous_categorie'],
            "devis_ligne__dot__id_pack_produit"=>$id_pack,
            "devis_ligne__dot__sous_categorie"=>ATF::sous_categorie()->nom($produitLoyer['id_sous_categorie']),
            "devis_ligne__dot__pack_produit"=>ATF::pack_produit()->nom($id_pack),
            "devis_ligne__dot__ean"=>$produitLoyer['ean'],
            "devis_ligne__dot__id_categorie"=>$souscategorie['id_categorie'],
            "devis_ligne__dot__categorie"=>ATF::categorie()->nom($souscategorie['id_categorie']),
            "devis_ligne__dot__commentaire_produit"=>$produitLoyer['commentaire'],
            "devis_ligne__dot__visible"=>$packProduitLigne['visible'],
            "devis_ligne__dot__visible_sur_site"=>$produitLoyer['visible_sur_site'],
            "devis_ligne__dot__visible_pdf"=>$produitLoyer['visible_sur_pdf'],
            "devis_ligne__dot__ordre"=>$produitLoyer['ordre']
          );
        }

        $toInsertLoyer[0]["loyer__dot__loyer"] += $produitLoyer["loyer"] * $produit['quantite'];
        $toInsertLoyer[0]["loyer__dot__duree"] = $produitLoyer["duree"];


    }

    // Faire sauter les index
    $toInsertProduitDevis = array_values($toInsertProduitDevis);

    $values_devis = array("loyer"=>json_encode($toInsertLoyer), "produits"=>json_encode($toInsertProduitDevis));
    $toDevis = array("devis"=>$devis, "values_devis"=>$values_devis);
    $id_devis = ATF::devis()->insert(array("devis"=>$devis, "values_devis"=>$values_devis));

    return $id_devis;
  }

  /**
   * Génère le contrat pour une affaire
   * @author Quentin JANON <qjanon@absystech.fr>
   * @param  Array $post       $_POST
   * @param  String $libelle    Libellé de l'affaire reconduit sur celui du contrat
   * @param  Integer $id_devis   ID du devis généré au préalable
   * @param  Integer $id_affaire ID de l'affaire généré au préalable
   * @return Integer             ID du contrat généré
   */
  private function createContrat($post, $libelle, $id_devis, $id_affaire) {
    ATF::devis_ligne()->q->reset()->where('id_devis', $id_devis);
    $lignesDevis = ATF::devis_ligne()->select_all();

    $commande =array(
        "commande" => $libelle,
        "type" => "prelevement",
        "id_societe" => $post["id_societe"],
        "date" => date("d-m-Y"),
        "id_affaire" => $id_affaire,
        "id_devis" => $id_devis
    );

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
          "commande_ligne__dot__ordre"=>$value['ordre']
      );
    }
    $values_commande = array( "produits" => json_encode($toInsertProduitContrat));

    $id_commande = ATF::commande()->insert(array("commande"=>$commande , "values_commande"=>$values_commande));
    return $id_commande;
  }

  /**
   * Retourne les differentes étapes SLIMPAY
   * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
   * @param  array $infos Simple dimension des champs à insérer
   * @return [type]       [description]

  public function _getSlimpaySteps($post, $get){
    log::logger("=============================","souscription");
    log::logger($post,"souscription");
    log::logger($get,"souscription");

    $id_affaire = $post["id"];

    log::logger("ID Affaire --> " , "souscription");
    log::logger($id_affaire , "souscription");
    $id_societe = ATF::affaire()->select($id_affaire,"id_societe");
    log::logger("ID Societe : " , "souscription");
    log::logger($id_societe , "souscription");

    if (!$id_societe) {
      throw new Exception('Aucune information pour cet identifiant.', 500);
    }

    log::logger('SWITCH SITE ASSOCIE '.$post['site_associe'],"souscription");
    switch ($post['site_associe']) {
      case 'bdomplus':
        if(ATF::affaire()->select($id_affaire, "id_magasin")){
          $passage_slimpay = array();

          ATF::loyer()->q->reset()->where("id_affaire", $id_affaire)->addOrder("id_loyer", "ASC");
          $loyer = ATF::loyer()->select_row();
          if($loyer["frequence_loyer"] != "an")
          $passage_slimpay["findOrCreateMandate"] = true;

        }else{
          $passage_slimpay = array('findOrCreateMandate'=> true, 'createOrder'=> true);
        }
      break;
    }
    return array("passage_slimpay" => $passage_slimpay);
  }*/

  /**
  * Appel Sell & Sign, verification de l'IBAN, envoi du mandat SEPA PDF
  * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  * @param array $infos Simple dimension des champs à insérer
  */
  public function _signAndGetPDF($post,$get) {
    log::logger("=============================","souscription");
    log::logger($post,"souscription");
    log::logger($get,"souscription");
    $tel  = $post["tel"];
    $bic  = $post["bic"];
    $iban = $post["iban"];
    $id_affaire = $post["id"];

    log::logger("ID Affaire --> " , "souscription");
    log::logger($id_affaire , "souscription");
    $id_societe = ATF::affaire()->select($id_affaire,"id_societe");
    log::logger("ID Societe : " , "souscription");
    log::logger($id_societe , "souscription");

    if (!$id_societe) {
      throw new Exception('Aucune information pour cet identifiant.', 500);
    }

    if (!$post['type']) {
      throw new errorATF("TYPE INCONNU : '".$post['type']."', ne peut pas faire de retour", 500);
    }

    if(!$tel){
      if($post['type'] == "particulier") $tel = ATF::societe()->select($id_societe, "particulier_portable");
    }

    $societe = ATF::societe()->select($id_societe);
    $toUpdate = array("id_societe"=>$id_societe, "BIC"=>$bic , "IBAN"=>$iban);
    // Gestion de la reference société
    $refSociete = $societe['ref'];
    if (!$refSociete) {
      // Modification de la société pour lui générer sa ref si elle n'est pas déjà setté
      $refSociete = ATF::societe()->create_ref($societe);
      $toUpdate['ref'] = $refSociete;
    }


    // Gestion du code client
    $codeClient = $societe['code_client'];

    log::logger('CODE CLIENT = '.$codeClient,"souscription");
    if (!$codeClient) {
      // Modification de la société pour lui générer sa ref si elle n'est pas déjà setté
      $codeClient = ATF::societe()->getCodeClient($societe, $post['site_associe']);
      $toUpdate['code_client'] = $codeClient;
      log::logger('CODE CLIENT = '.$codeClient,"souscription");
    }
    //Si il n'y a pas de num telephone sur la société, on enregistre ce numéro
    if($societe["tel"] === NULL) {
      $toUpdate['tel'] = $tel;
    }

    log::logger('UPDATE SOCIETE',"souscription");
    log::logger($toUpdate,"souscription");
    ATF::societe()->u($toUpdate);

    if (!$societe["id_contact_signataire"]) throw new errorATF("Aucun signataire au niveau de la société", 500);

    $contact = ATF::contact()->select($societe["id_contact_signataire"]);

    if(!$post["no_check_iban"] || $post["iban"]){
      log::logger('GET CONTACT',"souscription");
      log::logger($contact,"souscription");

      log::logger('CHECK IBAN',"souscription");
      log::logger($iban,"souscription");

      $this->checkIBAN($iban);
    }


    log::logger('UPDATE CONTACT',"souscription");
    log::logger(array("id_contact"=>$societe["id_contact_signataire"], "gsm"=>$tel),"souscription");

    ATF::contact()->u(array("id_contact"=>$societe["id_contact_signataire"], "gsm"=>$tel));

    //On stocke les infos de signature sur l'affaire
    log::logger('UPDATE AFFAIRE '.$id_affaire,"souscription");
    ATF::affaire()->u(array('id_affaire'=>$id_affaire,
                            'tel_signature'=> $tel,
                            'mail_signataire'=> $contact["email"],
                            'date_signature'=> date('Y-m-d H:i:s'),
                            'signataire'=> $contact["prenom"]." ".$contact["nom"]
                            )
                      );

    ATF::commande()->q->reset()->where('commande.id_affaire', $id_affaire);
    $contrat = ATF::commande()->select_row();

    log::logger('SWITCH SITE ASSOCIE '.$post['site_associe'],"souscription");
    switch ($post['site_associe']) {
      case 'btwin':
        $pdf_mandat = ATF::pdf()->generic('mandatSellAndSign',$id_affaire,true);
        $contratPV = ATF::pdf()->generic('contratPV',$contrat['commande.id_commande'],true);
        $noticeAssurance = file_get_contents(__PDF_PATH__."cleodis/notice_assurance.pdf");
        $f = array(
          "mandatSellAndSign.pdf"=> base64_encode($pdf_mandat), // base64
          "contrat-PV.pdf"=> base64_encode($contratPV), // base64
          "notice_assurance.pdf"=> base64_encode($noticeAssurance) // base64
        );
      break;

      case 'bdomplus':

        $pathMandat = "/tmp/".$infos["function"]."-".$infos["value"].".pdf";
        $pdf_mandat = ATF::pdf()->generic('mandatSellAndSign',$id_affaire,true);
        file_put_contents($pathMandat,$pdf_mandat);

        $f =  array(
          "mandatSellAndSign.pdf" => base64_encode($pdf_mandat)
        );

        if($post["send_file_mail"]){
          $mail_files = array(
            "contrat"=> $pathMandat
          );

          //On envoi le mail au client avec le contrat qu'il va signer
          $this->sendContrat($id_affaire, $mail_files, $contact);
        }

        if(ATF::affaire()->select($id_affaire, "id_magasin")){
          $passage_slimpay = array();

          /*ATF::loyer()->q->reset()->where("id_affaire", $id_affaire)->addOrder("id_loyer", "ASC");
          $loyer = ATF::loyer()->select_row();
          if($loyer["frequence_loyer"] != "an") */
          $passage_slimpay["findOrCreateMandate"] = true;

        }else{
          $passage_slimpay = array('findOrCreateMandate'=> true, 'payment'=> true);
        }

      break;

      case 'boulangerpro':
        $pdf_mandat = ATF::pdf()->generic('mandatSellAndSign',$id_affaire,true);
        $f = array(
          "mandatSellAndSign.pdf"=> base64_encode($pdf_mandat)
        );

        $docsHorsContrat = array();

        //On récupère les documents du/des produits de cette affaire
        ATF::commande_ligne()->q->reset()->where("id_commande", $contrat['commande.id_commande']);
        $lignes = ATF::commande_ligne()->sa();

        foreach ($lignes as $key => $value) {
          $id_doc = ATF::produit()->select($value["id_produit"], "id_document_contrat");
          if($id_doc){
            $doc = ATF::document_contrat()->select($id_doc);
            if($doc["etat"] == "actif" && $doc["type_signature"] == "hors_contrat"){
              $docsHorsContrat[$id_doc] = $doc["document_contrat"];
            }
          }
        }

        if($docsHorsContrat){
          foreach ($docsHorsContrat as $key => $value) {
            $file = util::mod_rewrite($value).".pdf";

            $CG = ATF::document_contrat()->filepath($key,"fichier_joint");
            $f[$file] = base64_encode(file_get_contents($CG));
          }
        }
      break;
      default:
        throw new errorATF("SITE ASSOCIE INCONNU : '".$post['site_associe']."', aucun document a générer.", 500);
      break;
    }

    $return = array(
      "id_affaire"=>$this->decryptId($id_affaire),
      "id_societe"=> ATF::affaire()->select($this->decryptId($id_affaire), "id_societe"),
      "civility"=>$contact["civilite"],
      "firstname"=>$contact["prenom"],
      "lastname"=>$contact["nom"],
      "address_1"=>$societe["adresse"],
      "address_2"=>$societe["adresse_2"]." ".$societe["adresse_3"],
      "postal_code"=>$societe["cp"],
      "city"=>$societe["ville"],
      "company_name"=>$societe["societe"],
      "ref"=>$refSociete,
      "country"=>$societe["id_pays"],
      "cell_phone"=>$tel,
      "files2sign"=>$f,
      "ref_affaire"=> ATF::affaire()->select($id_affaire, "ref"),
      "rum"=> ATF::affaire()->select($id_affaire, "RUM"),
      "bic"=> $bic,
      "iban"=> $iban
    );

    if($passage_slimpay)  $return["passage_slimpay"] = $passage_slimpay;


    if ($post['type'] == 'particulier') {
      $return["email"]=$societe["particulier_email"];
    } else if ($post['type'] == 'professionnel') {
      $return["email"]=$contact["email"];
    } else {
      $return['email'] = $societe["particulier_email"];
    }

    return $return;
  }

  /**
  * Permet de checker si un IBAN est correct
  * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  * @param array $infos Simple dimension des champs à insérer
  */
  public function checkIBAN($iban){
    $table_conversion = array("A"=>10,"B"=>11,"C"=>12,"D"=>13,"E"=>14,"F"=>15,"G"=>16,"H"=>17,"I"=>18,"J"=>19,"K"=>20,"L"=>21,"M"=>22,"N"=>23,"O"=>24,"P"=>25,"Q"=>26,"R"=>27,"S"=>28,"T"=>29,"U"=>30,"V"=>31,"W"=>32,"X"=>33,"Y"=>34,"Z"=>35);


    /*
    * Enlever les caractères indésirables (espaces, tirets)
    * Supprimer les 4 premiers caractères et les replacer à la fin du compte
    * Remplacer les lettres par des chiffres au moyen d'une table de conversion (A=10, B=11, C=12 etc.)
    * Diviser le nombre ainsi obtenu par 97.
    * Si le reste n'est pas égal à 1 l'IBAN est incorrect : Modulo de 97 égal à 1
    */
    if($iban){
      //Enlever les caractères indésirables (espaces, tirets)
      $iban = str_replace("-", "", $iban);
      $iban = str_replace(" ", "", $iban);


      //Supprimer les 4 premiers caractères et les replacer à la fin du compte
      $first = substr($iban, 0, 4);
      $iban = substr($iban, 4);

      $iban = $iban.$first;

      $char = "";

      //Remplacer les lettres par des chiffres au moyen d'une table de conversion (A=10, B=11, C=12 etc.)
      for($i=0;$i<strlen($iban); $i++){
        if(!is_numeric($iban[$i])){
          $char .= $table_conversion[$iban[$i]];
        }else{
          $char .= $iban[$i];
        }
      }

      //Diviser le nombre ainsi obtenu par 97
      if(bcmod($char , 97) != 1) throw new errorATF("IBAN incorrect", 500);

    }else{
      throw new errorATF("IBAN vide", 500);
    }
  }



  /**
  * Appel Sell & Sign, retourne les infos du client à partir de l'id_affaire
    * @author Yann GAUTHERON <ygautheron@absystech.fr>
  * @param array $post["id_affaire"]
  */
  public function _signGetInfosOnly($post){
    $id_societe = ATF::affaire()->select($post["id"],"id_societe");
    if (!$id_societe) {
      throw new Exception('Aucune information pour cet identifiant.', 500);
    }
    $societe = ATF::societe()->select($id_societe);
    $contact = ATF::contact()->select($societe["id_contact_signataire"]);
    $return = array(
      "civility"=>$contact["civilite"],
      "firstname"=>$contact["prenom"],
      "lastname"=>$contact["nom"],
      "email"=>$contact["email"],
      "tel"=>$contact["gsm"],
      "company_name"=>$societe["societe"],
      "ref"=>ATF::$codename.$societe["code_client"],
      "IBAN"=>$societe["IBAN"],
      "BIC"=>$societe["BIC"]
    );
    return $return;
  }

  /**
  * Appel Sell & Sign, store les documents signés dans Optima
  * @author Quentin JANON <qjanon@absystech.fr>
  * @param array $post["id_affaire"]
  */
  public function _storeSignedDocuments($post){

    switch ($post['type']) {
      case 'mandatSellAndSign': // Contrat signé
      case 'mandatSellAndSign.pdf': // Contrat signé
        $module = "commande";
        ATF::commande()->q->reset()->addfield("id_commande")->where('commande.id_affaire', $post["id_affaire"]);
        $id = ATF::commande()->select_cell();
        $type = 'retour';
      break;
      case 'contrat-PV': // PV signé
      case 'contrat-PV.pdf': // PV signé
        $module = "commande";
        ATF::commande()->q->reset()->addfield("id_commande")->where('commande.id_affaire', $post["id_affaire"]);
        $id = ATF::commande()->select_cell();
        $type = 'retourPV';
      break;
      case 'notice_assurance': // Notice d'assurance
      case 'notice_assurance.pdf': // Notice d'assurance
        $module = "affaire";
        $id = $post['id_affaire'];
        $type = 'others';
      break;
      case 'others':
        $module = "affaire";
        $id = $post['id_affaire'];
        $type = 'others';
      break;
    }

    if (!$id) throw new Exception('Il manque l\'identifiant', 500);
    if (!$module) throw new Exception('Il manque le module', 500);
    log::logger($type, "qjanon");
    log::logger($post['type'], "qjanon");
    if ($type == 'others') {
      // Ici on va traiter les documents annexe DGS/CGA, ces document doivent se retrouvé dans la GED de l'affaire et non sur l'affaire elle même
      $id_pdf_affaire = ATF::pdf_affaire()->insert(array(
        "id_affaire"=>$id,
        "provenance"=>"Retour autre document : ".ATF::affaire()->select($id, "ref")
      ));
      $file = ATF::pdf_affaire()->filepath($id_pdf_affaire,"fichier_joint", null, $this->codename);

    } else {
      $file = ATF::getClass($module)->filepath($id, $type, null, $this->codename);

      // Si c'est le module commande, on met à jour les dates de retour
      if($module == "commande"){
        if ($type == "retour") $champs = "retour_contrat";
        if ($type == "retourPV") $champs = "retour_pv";
        if($champs) ATF::commande()->u(array("id_commande"=> $id, $champs => date("Y-m-d")));
      }

    }

    try {
      util::file_put_contents($file,base64_decode($post['data']));
      $return = true;
    } catch (Exception $e) {
      $return  = array("error"=>true, "data"=>$e);
    }

    return $return;
  }

  public function getPrefixCodeClient($site_associe) {
    switch ($site_associe) {
      case 'boulangerpro':
        $r = "BG";
      break;
      case "btwin":
        $r = "BT";
      break;
      case "bdomplus":
        $r = "BP";
      break;
      default:
        $r = "";
      break;
    }
    return $r;
  }

  /**
   * Création d'un comité dans une affaire
   * @param  Integer $id_affaire      ID de l'affaire
   * @param  Array $societe         Infos de la société
   * @param  Enum $etat            en_cours|accepte|refuse : Etat du comité insérer
   * @param  Text $desc            Description associé au comité
   * @param  Date $reponse         Date de la réponse
   * @param  Date $validite_accord Date de validité de l'accord
   * @return Integer                  ID du comité créé
   */
  public function createComite($id_affaire, $societe, $etat, $desc, $reponse=NULL, $validite_accord=NULL) {
    //On crée le comité
    $comite = array  (
        "id_societe" => $societe["id_societe"],
        "id_affaire" => $id_affaire,
        "id_contact" => ATF::societe()->select($societe["id_societe"], "id_contact_signataire"),
        "etat"=>$etat,
        "decisionComite"=> $etat == 'accepte' ? "Accepté automatiquement" : '',
        "activite" => $societe["activite"],
        "reponse" => $reponse,
        "validite_accord" => $validite_accord,
        "id_refinanceur" => $this->id_refinanceur_cleodis,
        "date_creation" => $societe["date_creation"],
        "date_compte" => $societe["lastaccountdate"],
        "capitaux_propres" => $societe["capitaux_propres"],
        "note" => $societe["cs_score"],
        "dettes_financieres" => $societe["dettes_financieres"],
        "limite" => $societe["cs_avis_credit"],
        "ca" => $societe["ca"],
        "capital_social" => $societe["capital_social"],
        "resultat_exploitation" => $societe["resultat_exploitation"],
        "date" => date("d-m-Y"),
        "description" => $desc,
        "suivi_notifie"=>array(0=>"")
    );
    return ATF::comite()->insert(array("comite"=>$comite));
  }


}
class souscription_bdomplus extends souscription_cleodis {

  public $id_user = 116;
  public $codename = "bdomplus";

  /**
   * Démarrage du contrat ou annulation de l'affaire selon le retour order SLIMPAY
   * @param  Integer $id_affaire      ID de l'affaire
   * @param  Array $societe         Infos de la société
   * @param  Enum $etat            en_cours|accepte|refuse : Etat du comité insérer
   * @param  Text $desc            Description associé au comité
   * @param  Date $reponse         Date de la réponse
   * @param  Date $validite_accord Date de validité de l'accord
   */
  public function _startOrCancelAffaire($get, $post){

    if($post["order"]["id"]){
      $order = $post["order"];
      $ref = $order["id"];
      $state = $order["state"];
      ATF::affaire()->q->reset()
        ->addAllFields("affaire")
        ->where("affaire.ref_sign", $ref);
      $affaire = ATF::affaire()->select_row();

      $suivi = array(
        "id_societe" => $affaire["affaire.id_societe_fk"],
        "id_affaire" => $affaire["affaire.id_affaire_fk"],
        "type"=> "note",
        "type_suivi"=> "Contrat",
        "texte" => "Retour Order SLIMPAY : ".json_encode($order)
      );
      ATF::suivi()->i($suivi);
      $return = $this->controle_affaire($affaire, $post["order"]);

    }elseif($post["order"]["affaires"]){
      ATF::affaire()->q->reset()
        ->addAllFields("affaire")
        ->where("affaire.id_affaire", $post["order"]["affaires"][0]);
      $affaire = ATF::affaire()->select_row();
      ATF::affaire_etat()->i(array("id_affaire"=> $affaire["affaire.id_affaire_fk"], "etat"=> "finalisation_souscription"));
      $return["order"] =  $this->controle_affaire($affaire);

    }else{
      throw new errorATF("Data manquante en paramètre d'entrée", 500);
    }
    log::logger($return , "mfleurquin");

    return $return;

  }

  public function controle_affaire($affaire, $order=null){
    if($affaire){

      ATF::commande()->q->reset()->addAllFields("commande")->where("commande.id_affaire", $affaire["affaire.id_affaire_fk"]);
      $commande = ATF::commande()->select_row();

      ATF::loyer()->q->reset()->where("loyer.id_affaire",$affaire["affaire.id_affaire_fk"]);
      $loyer = ATF::loyer()->select_row();


      if(!$affaire["affaire.id_magasin"] && ($order && $order["state"])){
        switch ($order["state"]) {
          case "closed.completed" :
            ATF::affaire_etat()->i(array("id_affaire"=> $affaire["affaire.id_affaire_fk"], "etat"=> "finalisation_souscription"));
            $this->demarrageContrat($affaire,$commande);
          break;

          case "closed" :
          case "closed.aborted" :
          case "closed.aborted.aborted_byclient" :
          case "closed.aborted.aborted_byserver" :
          case "open.not_running" :
          case "open.running" :
          case "open.not_running.suspended" :
          case "open.not_running.suspended.awaiting_input" :
          case "open.not_running.suspended.awaiting_validation" :
          case "open.not_running.not_started" :
            $this->annuleContrat($affaire,$commande, json_encode( $order ));
          break;
        }
      }

      if($affaire["affaire.id_magasin"]){
        log::logger("Affaire Magasin ".$affaire["affaire.ref"], "controle_affaire_magasin_facture");

        ATF::affaire_etat()->q->reset()->where("id_affaire", $affaire["affaire.id_affaire_fk"])
                                       ->where("etat", "signature_document_ok", "OR")
                                       ->where("etat", "finalisation_souscription");
        $affaire_etats = ATF::affaire_etat()->sa();

        $etats = array("signature_document_ok"=> 0, "finalisation_souscription"=> 0);

        foreach ($affaire_etats as $key => $value) {
          $etats[$value["etat"]] = 1;
        }

        if($etats["signature_document_ok"] && $etats["finalisation_souscription"] ){
          if($loyer["frequence_loyer"] == "mois"){
            log::logger("Affaire Magasin Mensuelle ".$affaire["affaire.ref"], "controle_affaire_magasin_facture");
            $this->demarrageContrat($affaire,$commande);

            // Si on est à J+1 et la facture pas payée on envoi un mail au client pour 1er loyer en prelevement + tache à Benjamin pour prelever
            if(date("Y-m-d", strtotime($affaire["affaire.date"]. ' + 1 days')) == date("Y-m-d")){
              log::logger("On est à J+1 ", "controle_affaire_magasin_facture");

              ATF::facture_magasin()->q->reset()->where("id_affaire", $affaire["affaire.id_affaire_fk"]);
              $facture_magasin = ATF::facture_magasin()->select_row();
              if(!$facture_magasin || $facture_magasin["etat"] == "non_recu"){
                log::logger("Facture Magasin non recu, on crée la tache + envoi du mail au client ", "controle_affaire_magasin_facture");

                $this->envoiMailFactureMagNonPayee($affaire,$loyer,$facture_magasin);

                //Passer la facture en impayée + retirer la date de paiement
                ATF::facture()->q->reset()->where("facture.id_affaire", $affaire["affaire.id_affaire_fk"])
                                        ->addOrder("facture.id_facture", "ASC");
                $facture = ATF::facture()->select_row();
                if($facture)  ATF::facture()->u(array("id_facture" => $facture["facture.id_facture"], "etat"=>"impayee", "date_paiement"=> NULL));
              }
            }

          }else{

            log::logger("Affaire Magasin Annuelle ".$affaire["affaire.ref"], "controle_affaire_magasin_facture");
            // Si on est à J+1
            if(date("Ymd", strtotime($affaire["affaire.date"]. ' + 1 days')) <= date("Ymd")){

              ATF::facture_magasin()->q->reset()->where("id_affaire", $affaire["affaire.id_affaire_fk"]);
              $facture_magasin = ATF::facture_magasin()->select_row();

              //Si on a la facture de payée (retourné par Boulanger)
              if(!$facture_magasin || $facture_magasin["etat"] == "non_recu"){
                log::logger("Annulation de l'affaire car pas de facture magasin ou facture non recue ", "controle_affaire_magasin_facture");
                $this->annuleContrat($affaire,$commande, "Facture magasin ".$facture_magasin["ref_facture"]." non reçu");
              }else{
                log::logger("Facture magasin recu, on demarre le contrat ".$affaire["affaire.ref"], "controle_affaire_magasin_facture");
                $this->demarrageContrat($affaire,$commande);

                ATF::facture()->q->reset()->where("facture.id_affaire", $affaire["affaire.id_affaire_fk"])
                                        ->addOrder("facture.id_facture", "ASC");
                $facture = ATF::facture()->select_row();
                if($facture)  ATF::facture()->u(array("id_facture" => $facture["facture.id_facture"], "ref_magasin"=> $facture_magasin["ref_facture"]));
              }
            }
          }
        }else{
          $raison = "La souscription pour cette affaire n'a pas été terminée, voici les étapes : ";
          foreach ($etats as $k_etat => $v_etat) {
            $raison .= "\n".$k_etat." -> ".(($v_etat == 1) ? "Oui" : "Non") . "\n";
          }
          $this->annuleContrat($affaire, $commande, $raison);
        }
      }
    }else{
      throw new errorATF("Pas d'affaire trouvée pour la ref_sign ".$ref." ou l'id ".$affaire["affaire.id_affaire_fk"], 500);
    }

    $order["id_affaire"] = $affaire["affaire.id_affaire_fk"];
    $order["id_societe"] = $affaire["affaire.id_societe_fk"];
    $order["id_magasin"] = $affaire["affaire.id_magasin"];
    $order["frequence_loyer"] = $loyer["frequence_loyer"];

    return array("order" => $order);
  }

  /**
   * Démarre une affaire (Démarrage du contrat)
   * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
   */
  public function demarrageContrat($affaire,$commande){
     if($affaire["commande.etat"] == "non_loyer"){
        ATF::db($this->db)->begin_transaction();

        try{
          #On démarre le contrat avec envoi les licences
          if($commande && $commande["commande.etat"] == "non_loyer"){
            $infos = array(
              "id_commande" => $commande["commande.id_commande_fk"],
              "value" => date("Y-m-01"),
              "key" => "date_debut"
            );

            ATF::commande()->updateDate($infos);
            //Contrat Démarré, il faut également mettre la 1ere facture en payé (Paiement CB)
            ATF::facture()->q->reset()->where("facture.id_affaire", $affaire["affaire.id_affaire_fk"])
                                      ->addOrder("facture.id_facture", "ASC");
            $facture = ATF::facture()->select_row();

            if($facture){
              $f = array("id_facture" => $facture["facture.id_facture"],
                                       "mode_paiement"=> "cb",
                                       "etat"=>"payee",
                                       "date_paiement"=>date("Y-m-d"));
              if($affaire["affaire.id_magasin"]){
                $f["mode_paiement"] = "pre-paiement";
                $f["etat"] = "impayee";
              }
              ATF::facture()->u($f);
              ATF::facture()->generatePDF(array("id"=>$f["id_facture"]));
            }

            $licence_a_envoyer = $this->envoi_licence($commande["commande.id_commande_fk"]);


            //On crée tout les bons de commande de l'affaire
            ATF::$usr->set('id_user',$post['id_user'] ? $post['id_user'] : $this->id_user);
            ATF::bon_de_commande()->createAllBDC(array("id_commande"=> $commande["commande.id_commande_fk"]));

            ATF::db($this->db)->commit_transaction();

            $this->envoiMailLicence($affaire["affaire.id_affaire_fk"], $affaire["affaire.id_societe_fk"], $licence_a_envoyer);

            //Installation à domicile
            $this->envoiMailInstallationZen($affaire, $commande);

          }

        }catch(errorATF $e){
          ATF::db($this->db)->rollback_transaction();
          throw $e;
        }
     }
  }


  /**
   * Annule une affaire (Passage de l'affaire en annulée, suppression du contrat)
   * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
   */
  public function annuleContrat($affaire,$commande, $raison){
    if($affaire["affaire.etat"] !== "perdue"){
      ATF::devis()->q->reset()->where("devis.id_affaire", $affaire["affaire.id_affaire_fk"]);
      $devis = ATF::devis()->select_row();

      //On passe le devis en attente pour pouvoir annuler l'affaire
      ATF::devis()->u(array("id_devis" => $devis["id_devis"], "etat"=> "attente"));

      //On supprime le contrat également
      if($commande) ATF::commande()->d($commande["commande.id_commande_fk"]);


      $infos = array(
        "id_devis" => $devis["id_devis"],
        "raison_refus"=> $raison
      );
      ATF::devis()->perdu($infos);
    }
  }

  /**
   * Récupération des numéros de licences
   * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
   */
  public function envoi_licence($id_commande){
    //On envoi les licences
    ATF::commande_ligne()->q->reset()->where("id_commande", $id_commande)
                                     ->from("commande_ligne", "id_produit", "produit", "id_produit")
                                     ->whereIsNotNull("produit.id_licence_type");
    $lignes = ATF::commande_ligne()->select_all();

    $licence_a_envoyer = array();

    foreach ($lignes as $key => $value) {

      ATF::licence()->q->reset()->where("id_licence_type", $value["id_licence_type"],"AND")
                                ->whereIsNull("licence.id_commande_ligne","AND")
                                ->addOrder("id_licence", "ASC")->setLimit($value["quantite"]);
      $licence = ATF::licence()->sa();

      if(count($licence)){
        foreach ($licence as $kl => $vl) {
          ATF::licence()->u(array("id_licence" => $vl["id_licence"], "id_commande_ligne" => $value["id_commande_ligne"], "date_envoi"=>date("Y-m-d H:i:s")));
          $vl["url_telechargement"] = ATF::licence_type()->select($vl["id_licence_type"], "url_telechargement");
          $licence_a_envoyer[$value["id_produit"]][] = $vl;
        }

      }else{
        /*ATF::suivi()->i(array("id_affaire"=>ATF::commande()->select($id_commande , "id_affaire") ,
                              "id_societe"=> ATF::commande()->select($id_commande , "id_societe") ,
                              "texte"=> "Il n'y a plus assez de clé de licences pour ".ATF::licence_type()->select($value["id_licence_type"], "licence_type")));*/

        throw new errorATF("Il n'y a plus assez de clé de licences pour ".$value["id_licence_type"], 500);
      }
    }
    return $licence_a_envoyer;
  }

  public function sendContrat($affaire, $files, $contact){

    if($contact["email"] || $contact["email_perso"]){
      $info_mail["from"] = "L'équipe Cléodis (ne pas répondre) <no-reply@cleodis.com>";
      $info_mail["recipient"] = ($contact["email"]) ? $contact["email"] : $contact["email_perso"];
      $info_mail["html"] = true;
      $info_mail["template"] = "mail_contrat_a_signer";
      $info_mail["objet"] = "Abonnement BDOM PLUS - Offre ZEN - Votre contrat à signer";

      $mail = new mail($info_mail);

      foreach ($files as $key => $infos) {
          $mail->addFile($infos,$key.".pdf",true);
      }


      $send = $mail->send();

      $suivi = array(
        "id_contact" => $contact["id_contact"],
        "id_societe" => ATF::affaire()->select($affaire , "id_societe"),
        "id_affaire" => $affaire,
        "type"=> "note",
        "type_suivi"=> "Contrat",
        "texte" => "Objet : ".$info_mail["objet"]."\nDestinataire : ".$info_mail["recipient"]

      );

      if($send){
        $suivi["texte"] =  "Envoi du mail au client contenant le contrat avant la signature\n".$suivi["texte"];
      }else{
        $suivi["texte"] =  "Probleme lors de l'envoi du mail au client contenant le contrat avant la signature";
      }
      ATF::suivi()->i($suivi);
    }


  }

  public function envoiMailVendeurABenjamin($affaires, $vendeur){
    log::logger("=================envoiMailVendeurABenjamin================", "souscription");
    log::logger($affaires, "souscription");

    if ($vendeur && $affaires) {
      $vendeur = json_decode($vendeur, true);
      log::logger($vendeur, "souscription");

      $info_mail["from"] = "L'équipe Cléodis (ne pas répondre) <no-reply@cleodis.com>";
      $info_mail["recipient"] = "benjamin.tronquit@cleodis.com,BDOMPlusLicence@absystech.fr";
      $info_mail["html"] = true;
      $info_mail["template"] = "bdomplus-mailVendeurMagasin";
      $info_mail["texte"] = "Souscription magasin par le vendeur suivant ";
      $info_mail["vendeur"] = $vendeur;
      $info_mail["affaires"] = $affaires;

      $info_mail["objet"] = "Souscription BDOM par un vendeur en magasin";

      $mail = new mail($info_mail);
      log::logger($mail, "souscription");


      $send = $mail->send();
      log::logger($send, "souscription");
    } else {
      log::logger("Il manque le vendeur ou les références affaires, on envoi pas le mail a Benjamin." , "souscription");
      log::logger($affaires , "souscription");
      log::logger($vendeur , "souscription");
    }

    log::logger("=================FIN envoiMailVendeurABenjamin================", "souscription");


  }

  /**
   * Envoi du mail au client pour l'avertir que la facture magasin n"a pas été faite
   * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
   */
  public function envoiMailFactureMagNonPayee($affaire, $loyer, $facture_magasin){

    if($email_pro = ATF::societe()->select($affaire["affaire.id_societe_fk"], "email")){
      $email = $email_pro;
    }else{
      $email = ATF::societe()->select($affaire["affaire.id_societe_fk"], "  particulier_email");
    }

    $info_mail["from"] = "L'équipe Cléodis (ne pas répondre) <no-reply@cleodis.com>";
    $info_mail["recipient"] = $email;
    $info_mail["html"] = true;
    $info_mail["template"] = "facture_magasin_non_reglee";
    if(ATF::$codename == "bdomplus") $info_mail["objet"] = "Abonnement BDOM PLUS - Offre ZEN - Paiement de la première facture";

    $info_mail["date_signature"] = date("d/m/Y", strtotime($affaire["affaire.date"]));
    $info_mail["facture_magasin"] = $facture_magasin["ref_facture"];

    $mail = new mail($info_mail);

    $send = $mail->send();

    $suivi = array(
      "id_contact" => $contact["id_contact"],
      "id_societe" => ATF::affaire()->select($affaire["affaire.id_affaire_fk"] , "id_societe"),
      "id_affaire" => $affaire["affaire.id_affaire_fk"],
      "type"=> "note",
      "type_suivi"=> "Contrat",
      "texte" => "Objet : ".$info_mail["objet"]."\nDestinataire : ".$info_mail["recipient"]

    );

    if($send){
      $suivi["texte"] =  "Envoi du mail au client pour le prevenir de la non reception de la facture magasin et paiement de la 1ere facture par prelevement\n".$suivi["texte"];
    }else{
      $suivi["texte"] =  "Probleme lors de l'envoi du mail au client pour le prevenir de la non reception de la facture magasin et paiement de la 1ere facture par prelevement";
    }
    ATF::suivi()->i($suivi);

  }

  /**
   * Envoi du mail au client avec les licences
   * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
   */
  public function envoiMailLicence($id_affaire, $id_societe, $licence_a_envoyer){
    if($email_pro = ATF::societe()->select($id_societe, "email")){
      $email = $email_pro;
    }else{
      $email = ATF::societe()->select($id_societe, "  particulier_email");
    }

    $info_mail["from"] = "L'équipe Cléodis (ne pas répondre) <no-reply@cleodis.com>";
    $info_mail["recipient"] = $email;
    $info_mail["html"] = true;
    $info_mail["template"] = "envoi_licence";
    if(ATF::$codename == "bdomplus") $info_mail["objet"] = "Les solutions Zen – Information sur votre licence";

    $info_mail["licences"] = $licence_a_envoyer;
    $info_mail["client"] = ATF::societe()->select($id_societe);

    $mail = new mail($info_mail);

    $send = $mail->send();

    $suivi = array(
      "id_contact" => $contact["id_contact"],
      "id_societe" => ATF::affaire()->select($id_affaire , "id_societe"),
      "id_affaire" => $id_affaire,
      "type"=> "note",
      "type_suivi"=> "Contrat",
      "texte" => "Objet : ".$info_mail["objet"]."\nDestinataire : ".$info_mail["recipient"]

    );

    if($send){
      $suivi["texte"] =  "Envoi du mail au client contenant les licences\n".$suivi["texte"];
    }else{
      $suivi["texte"] =  "Probleme lors de l'envoi du mail au client contenant les licences";
    }
    ATF::suivi()->i($suivi);
  }

  /**
   * Envoi du mail à BDOM et au client pour les prevenir d'une installation de leur produit
   * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
   */
  public function envoiMailInstallationZen($affaire, $commande){

    ATF::commande_ligne()->q->reset()->where("id_commande", $commande["commande.id_commande_fk"])
                                     ->from("commande_ligne", "id_produit", "produit", "id_produit")
                                     ->where("produit.produit", "Installation à domicile");
    $lignes = ATF::commande_ligne()->select_all();



    if($lignes){
      log::logger("Produit Installation inclus, on envoi le mail" , "souscription");
      if($email_pro = ATF::societe()->select($affaire["affaire.id_societe_fk"], "email")){
        $email = $email_pro;
      }else{
        $email = ATF::societe()->select($affaire["affaire.id_societe_fk"], "  particulier_email");
      }

      $info_mail["from"] = "L'équipe Cléodis (ne pas répondre) <no-reply@cleodis.com>";
      $info_mail["recipient"] = $email;
      $info_mail["html"] = true;
      $info_mail["mail_to_client"] = "oui";
      $info_mail["template"] = "installation_domicile";
      if(ATF::$codename == "bdomplus") $info_mail["objet"] = "Les solutions Zen – Installation à domicile";


      $mail = new mail($info_mail);

      $send = $mail->send();

      $suivi = array(
        "id_contact" => $contact["id_contact"],
        "id_societe" => $affaire["affaire.id_societe_fk"],
        "id_affaire" => $affaire["affaire.id_affaire_fk"],
        "type"=> "note",
        "type_suivi"=> "Contrat",
        "texte" => "Objet : ".$info_mail["objet"]."\nDestinataire : ".$info_mail["recipient"]
      );

      if($send){
        $suivi["texte"] =  "Envoi du mail au client pour la prise de rendez-vous pour l'installation\n".$suivi["texte"];
      }else{
        $suivi["texte"] =  "Probleme lors de l'envoi du mail au client pour la prise de rendez-vous pour l'installation";
      }
      ATF::suivi()->i($suivi);


      $info_mail["from"] = "L'équipe Cléodis (ne pas répondre) <no-reply@cleodis.com>";
      $info_mail["html"] = true;
      $info_mail["template"] = "installation_domicile";
      $client =  ATF::societe()->select($affaire["affaire.id_societe_fk"]);

      $info_mail["client"] = $client["societe"];
      $info_mail["adresse"] = $client["adresse"];
      if($client["adresse_2"]) $info_mail["adresse"] .= " - ".$client["adresse_2"];
      $info_mail["adresse"] .= " - ".$client["cp"]." ".$client["ville"];
      $info_mail["tel"] = $client["tel"];
      $info_mail["email"] = $email;
      $info_mail["mail_to_client"] = "non";

      if(ATF::$codename == "bdomplus"){
        $info_mail["recipient"] = "infos-bdom@bdom.fr";
        $info_mail["objet"] = "Mail Automatique - Installation Offre ZEN à effectuer";
      }


      $mail2 = new mail($info_mail);

      $send = $mail2->send();

      $suivi = array(
        "id_contact" => $contact["id_contact"],
        "id_societe" => $affaire["affaire.id_societe_fk"],
        "id_affaire" => $affaire["affaire.id_affaire_fk"],
        "type"=> "note",
        "type_suivi"=> "Contrat",
        "texte" => "Objet : ".$info_mail["objet"]."\nDestinataire : ".$info_mail["recipient"]
      );

      if($send){
        $suivi["texte"] =  "Envoi du mail à BDOM pour la prise de rendez-vous pour l'installation\n".$suivi["texte"];
      }else{
        $suivi["texte"] =  "Probleme lors de l'envoi du mail à BDOM pour la prise de rendez-vous pour l'installation";
      }
      ATF::suivi()->i($suivi);


    }else{
      log::logger("Pas d'Installation inclus dans l'offre" , "souscription");
    }
  }

  /**
   * Permet de demarrer ou arreter une affaire magasin créée à J-1 selon si la facture magasin a été recu ou non
   * @author : Morgan FLEURQUIN <mfleurquin@absystech.fr>
   */
  public function check_affaires_magasin($day){
    log::logger("=====================", "controle_affaire_magasin_facture");

    ATF::affaire()->q->reset()
      ->whereIsNotNull("id_magasin","AND", "affaire")
      ->where("affaire.date", date("Y-m-d", strtotime("-1 days")), "AND", "affaire", "=");
      //->where("affaire.date", date("Y-m-d"), "AND", "affaire");

    $affaireshier = ATF::affaire()->select_all();

    if($affaireshier){
      foreach ($affaireshier as $key => $value) {
        ATF::affaire()->q->reset()->addAllFields("affaire")
          ->where("affaire.id_affaire", $value["affaire.id_affaire"])
          ->whereIsNotNull("affaire.id_magasin");
        $affaire = ATF::affaire()->select_row();

        try{
          $this->controle_affaire($affaire);
        }catch(errorATF $e){
          log::logger($e->getMessage(), "controle_affaire_magasin_facture");
        }

      }
    } else {
      log::logger("Aucune affaire créée le ".date("d M Y", strtotime("-".$day." days")), "controle_affaire_magasin_facture");
    }
  }

};
class souscription_bdom extends souscription_cleodis { };
class souscription_boulanger extends souscription_cleodis { };