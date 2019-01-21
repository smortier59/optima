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

  public $id_partenaire = NULL;


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

    }

    $this->checkIBAN($post['iban']);

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

        $affaires[] = $id_affaire;

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
          "IBAN"=>$societe["IBAN"],
          "BIC"=>$societe["BIC"],
          "RUM"=>$societe["RUM"],
          "id_magasin"=>$post["id_magasin"]
        );

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
          $affToUpdate['snapshot_pack_produit'] = json_encode($pack_produit);
        }

        //Il ne faut pas écraser le RUM si il n'y en a pas sur le client (arrive lors de la 1ere affaire pour ce client)
        if($societe["RUM"]) $affToUpdate["RUM"]=$societe["RUM"];
        ATF::affaire()->u($affToUpdate);


        if ($post['id_panier']) {
          ATF::panier()->u(array("id_panier"=>$post['id_panier'],"id_affaire"=>$id_affaire));
        }

        if($post["site_associe"] === "btwin"){
          $noticeAssurance = ATF::pdf()->generic("noticeAssurance",$id_affaire,true);
          ATF::affaire()->store($s, $id_affaire, "noticeAssurance", $noticeAssurance);
        }
        // Création du contrat
        $id_contrat = $this->createContrat($post, $libelle, $id_devis, $id_affaire);

       // Mise à jour du panier avec l'ID affaire et le statut 'affaire'
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
        "type_affaire" => "normal"
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
        } else if ($produit['id_pack_produit_ligne']) {
          ATF::pack_produit_ligne()->q->reset()->where("id_pack_produit_ligne", $produit['id_pack_produit_ligne']);
          $packProduitLigne = ATF::pack_produit_ligne()->select_row();
          $id_pack = $packProduitLigne['id_pack_produit'];

          $produitLoyer = array_merge($produitLoyer,$packProduitLigne);

          $souscategorie = ATF::sous_categorie()->select($produitLoyer['id_sous_categorie']);

        }


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
            "devis_ligne__dot__visible_sur_site"=>$produitLoyer['visible_sur_site'],
            "devis_ligne__dot__visible_pdf"=>$produitLoyer['visible_pdf'],
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
  * Appel Sell & Sign, verification de l'IBAN, envoi du mandat SEPA PDF
  * @author Morgan FLEURQUIN <mfleurquin@absystech.fr>
  * @param array $infos Simple dimension des champs à insérer
  */
  public function _signAndGetPDF($post,$get) {
    log::logger("=============================","souscription");
    log::logger($post,"souscription");
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
    log::logger('GET CONTACT',"souscription");
    log::logger($contact,"souscription");

    log::logger('CHECK IBAN',"souscription");
    log::logger($iban,"souscription");

    $this->checkIBAN($iban);

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
      "files2sign"=>$f
    );

    if ($post['type'] == 'particulier') {
      $return["email"]=$societe["particulier_email"];
    } else if ($post['type'] == 'professionnel') {
      $return["email"]=$contact["email"];
    } else {
      $return['email'] = $societe["particulier_email"];
    }
    // log::logger("RETOUR","souscription");
    // log::logger($return,"souscription");
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
        $type = 'retourNoticeAssurance';
      break;
    }

    if (!$id) throw new Exception('Il manque l\'identifiant', 500);
    if (!$module) throw new Exception('Il manque le module', 500);

    $file = ATF::getClass($module)->filepath($id, $type, null, 'cleodis');

    try {
      util::file_put_contents($file,base64_decode($post['data']));
      $return = true;
    } catch (Exception $e) {
      $return  = array("error"=>true, "data"=>$e);
    }

    return $return;
  }

  private function getPrefixCodeClient($site_associe) {
    switch ($site_associe) {
      case 'boulangerpro':
        $r = "BG";
      break;
      case "btwin":
        $r = "BT";
      break;
      default:
        $r = "";
      break;
    }
    return $r;
  }



}
