<?
/**
 * Classe echeancier
 * @package Optima
 */
class echeancier extends classes_optima {

  public $maxDelayFacturation = 5;
  /**
   * Constructeur
   */
  public function __construct() {
    parent::__construct();

    $this->table = "echeancier";
    $this->colonnes['fields_column'] = array(
       'id_societe'
      ,'id_affaire'
      ,'designation'
      ,'montant_ht'=>array("custom"=>true)
      ,'debut'
      ,'fin'
      ,'variable'
      ,'periodicite'

    );

    $this->colonnes['primary'] = array(
       'id_societe'
      ,'id_affaire'
      ,'designation'
      ,'debut'
      ,'fin'
      ,'variable'
      ,'periodicite'
      ,'jour_paiement'
    );

    $this->field_nom = 'designation';
  }
  /**
   *
  * Fonctions _GET pour telescope
  * @package Telescope
  * @author Charlier Cyril <ccharlier@absystech.fr>
  * @param $get array contient le tri, page limit et potentiellement un id.
  * @param $post array Argument obligatoire mais inutilisé ici.
  * @return array un tableau avec les données
  */
  public function _GET($get,$post) {
    // Gestion du tri
    if (!$get['tri'] ) $get['tri'] = "id_echeancier";
    if (!$get['trid']) $get['trid'] = "asc";

    // Gestion du limit
    if (!$get['limit']) $get['limit'] = 30;

    // Gestion de la page
    if (!$get['page']) $get['page'] = 0;

    $this->q->reset();

    $colsData = array(
      "echeancier.id_echeancier",
      "echeancier.designation",
      "echeancier.commentaire",
      "affaire.id_affaire",
      "echeancier.debut",
      "echeancier.fin",
      "echeancier.variable",
      "echeancier.periodicite",
      "echeancier.actif",
      "societe.id_societe",
      "echeancier.jour_facture",
      "CASE echeancier.periodicite
        WHEN 'trimestrielle' THEN DATE_ADD(DATE_ADD(echeancier.prochaine_echeance, INTERVAL 3 MONTH), INTERVAL ".$this->maxDelayFacturation." DAY)
        WHEN 'semestrielle' THEN DATE_ADD(DATE_ADD(echeancier.prochaine_echeance, INTERVAL 6 MONTH), INTERVAL ".$this->maxDelayFacturation." DAY)
        WHEN 'annuelle' THEN DATE_ADD(DATE_ADD(echeancier.prochaine_echeance, INTERVAL 1 YEAR), INTERVAL ".$this->maxDelayFacturation." DAY)
        ELSE DATE_ADD(DATE_ADD(echeancier.prochaine_echeance, INTERVAL 1 MONTH), INTERVAL ".$this->maxDelayFacturation." DAY)
      END
      "=>array('alias'=>"date_limite_paiement"),
      "echeancier.prochaine_echeance"=>array('alias'=>'debut_periode'),
      "CASE echeancier.periodicite
        WHEN 'trimestrielle' THEN LAST_DAY(DATE_ADD(echeancier.prochaine_echeance, INTERVAL 2 MONTH))
        WHEN 'semestrielle' THEN LAST_DAY(DATE_ADD(echeancier.prochaine_echeance, INTERVAL 5 MONTH))
        WHEN 'annuelle' THEN LAST_DAY(DATE_ADD(echeancier.prochaine_echeance, INTERVAL 11 MONTH))
        ELSE LAST_DAY(echeancier.prochaine_echeance)
      END
      "=>array('alias'=>'fin_periode'),
      "echeancier.jour_facture",
      "echeancier.id_termes",
      "echeancier.id_affaire",
      "SUM(echeancier_ligne_periodique.quantite*echeancier_ligne_periodique.puht)"=>array("alias"=>"montant_ht")
    );

    $this->q->addField($colsData);

    $this->q->from("echeancier","id_societe","societe","id_societe");
    $this->q->from("echeancier","id_affaire","affaire","id_affaire");
    $this->q->from("echeancier","id_echeancier","echeancier_ligne_periodique","id_echeancier");
    $this->q->from("echeancier","id_termes","termes","id_termes");

    $this->q->addGroup("echeancier.id_echeancier");

    if($get["search"]){
      header("ts-search-term: ".$get['search']);
      $this->q->setSearch($get['search']);
    }

    if ($get['id']) {
      $this->q->where("echeancier.id_echeancier",$get['id'])->setLimit(1);
    } else {
      // gestion des filtres
      if ($get['filters']['encours'] == "on") {
        $this->q->where("echeancier.prochaine_echeance",'CURRENT_DATE','OR','echeance',"<",false,true);
      }
      if ($get['filters']['actif'] == "on") {
        $this->q->where("actif","oui");
      }

      // Filtre mensuel
      if ($get['filters']['mensuel'] == "on") {
        $this->q->where("echeancier.periodicite","mensuelle","OR","periodicite");
      }
      // Filtre trimestre
      if ($get['filters']['trimestriel'] == "on") {
        $this->q->where("echeancier.periodicite","trimestrielle","OR","periodicite");
      }
      // Filtre trimestre
      if ($get['filters']['annuel'] == "on") {
        $this->q->where("echeancier.periodicite","annuelle","OR","periodicite");
      }
      // Filtre semestre
      if ($get['filters']['semestriel'] == "on") {
        $this->q->where("echeancier.periodicite","semestrielle","OR","periodicite");
      }


      $this->q->setLimit($get['limit'])->setCount();
      // $data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);

    }

    // TRI
    switch ($get['tri']) {
      case 'id_echeancier':
        $get['tri'] = "echeancier.".$get['tri']."_fk";
      break;
      case 'debut_periode':
        $get['tri'] .= ",fin_periode";
      break;
    }

    $data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);

    foreach ($data["data"] as $k=>$lines) {
      foreach ($lines as $k_=>$val) {
        if (strpos($k_,".")) {
          $tmp = explode(".",$k_);
          $data['data'][$k][$tmp[1]] = $val;
          unset($data['data'][$k][$k_]);
        }
      }
    }


    if($get['id']){
      $return = $data['data'][0];

      ATF::echeancier_ligne_ponctuelle()->q->reset()->where("id_echeancier",$get['id']);
      if ($get['periode_debut']) {
        ATF::echeancier_ligne_ponctuelle()->q->where("date_valeur",$get['periode_debut'],"OR","periode",">");
      }
      if ($get['periode_fin']) {
        ATF::echeancier_ligne_ponctuelle()->q->where("date_valeur",$get['periode_fin'],"OR","periode","<");
      }

      $return['ponctuelle'] = ATF::echeancier_ligne_ponctuelle()->select_all();

      ATF::echeancier_ligne_periodique()->q->reset()->where("id_echeancier",$get['id']);
      if ($get['periode_debut']) {
        ATF::echeancier_ligne_periodique()->q->where("mise_en_service",$get['periode_debut'],"OR","periode",">");
      }
      if ($get['periode_fin']) {
        ATF::echeancier_ligne_periodique()->q->where("mise_en_service",$get['periode_fin'],"OR","periode","<");
      }
      $return['periodique'] = ATF::echeancier_ligne_periodique()->select_all();

      $return['fin_periode'] = self::getFinPeriodEstimated($return['debut_periode'], $return['periodicite']);


    }else{


      header("ts-total-row: ".$data['count']);
      header("ts-max-page: ".ceil($data['count']/$get['limit']));
      header("ts-active-page: ".$get['page']);
      $return = $data['data'];

      foreach ($return as $k=>$i) {
        if (date('Y-m-d')>$i['date_limite_paiement']) {
          $return[$k]['retard'] = true;
          $return[$k]['fin_periode'] = self::getFinPeriodEstimated($i['debut_periode'], $i['periodicite']);
        }
      }
    }

    return $return;
  }

  /**
   * Estime la date de fin de période pour un contrat en fonction du début de période et de la périodicité
   * @author Quentin JANON <qjanon@absystech.fr>
   * @param  Date $debPeriod  Date de début de période
   * @param  string $perodicite Periodicité : mensuelle, trimestrielle, semestrielle ou annuelle
   * @return date             Date estimée de fin de contrat.
   */
  private function getFinPeriodEstimated ($debPeriod, $perodicite) {
    log::logger($debPeriod.' - '.$perodicite,"qjanon");
    $start = new DateTime($debPeriod);
    switch ($perodicite) {
      case 'trimestrielle':
        $end = new DateTime(date('Y-m-d',strtotime($debPeriod." + 3 month")));
        $return = date("Y-m-t",strtotime($debPeriod." + 2 MONTH"));
        break;
      case 'semestrielle':
        $end = new DateTime(date('Y-m-d',strtotime($debPeriod." + 6 month")));
        $return = date("Y-m-t",strtotime($debPeriod." + 5 MONTH"));
        break;
      case 'annuelle':
        $end = new DateTime(date('Y-m-d',strtotime($debPeriod." + 12 month")));
        $return = date("Y-m-t",strtotime($debPeriod." + 11 MONTH"));
        break;
      default:
        $end = new DateTime(date('Y-m-d',strtotime($debPeriod)));
        $return = date("Y-m-t",strtotime($debPeriod));
        break;
    }
    // $now = new DateTime();

    // // Si on facture dans le passé
    // if ($end < $now) {
    //   log::logger("FACTURE PASSEE","qjanon");
    //   $monthInterval = $start->diff($now)->format('%m');
    //   log::logger($start->format('Ymd').' - '.$end->format('Ymd').' - '.$now->format('Ymd').' - '.$monthInterval,"qjanon");
    //   switch ($perodicite) {
    //     case 'trimestrielle':
    //       $return = date("Y-m-t",strtotime($debPeriod." + ".(2+$monthInterval*3)." MONTH"));
    //     break;
    //     case 'semestrielle':
    //       $return = date("Y-m-t",strtotime($debPeriod." + ".(5+$monthInterval*6)." MONTH"));
    //     break;
    //     case 'annuelle':
    //       $return = date("Y-m-t",strtotime($debPeriod." + ".(11+$monthInterval*12)." MONTH"));
    //     break;
    //     default:
    //       $return = date("Y-m-t",strtotime($debPeriod." + ".$monthInterval." MONTH"));
    //     break;
    //   }
    // } else {
    //   log::logger("FACTURE FUTURE","qjanon");
    //   // SI on facture dans le futur, alors ona  pas besoin de calculer l'interval de mois entre les deux dates.
    //   log::logger($start->format('Ymd').' - '.$end->format('Ymd').' - '.$now->format('Ymd'),"qjanon");
    //   switch ($perodicite) {
    //     case 'trimestrielle':
    //       $return = date("Y-m-t",strtotime($debPeriod." + 2 MONTH"));
    //     break;
    //     case 'semestrielle':
    //       $return = date("Y-m-t",strtotime($debPeriod." + 5 MONTH"));
    //     break;
    //     case 'annuelle':
    //       $return = date("Y-m-t",strtotime($debPeriod." + 11 MONTH"));
    //     break;
    //     default:
    //       $return = date("Y-m-t",strtotime($debPeriod));
    //     break;
    //   }

    // }
    return $return;
  }

  /**
  * Fonctions _POST echeancier pour telescope
  * @package Telescope
  * @author Charlier Cyril <ccharlier@absystech.fr>
  * @param   $[get] array
  * @param $post array contient toutes les donnees envoyées par le formulaire
  * @return Integer & boolean
  */
  public function _POST($get,$post){
    $input = file_get_contents('php://input');
    if (!empty($input)) parse_str($input,$post);
    $return = array();


    if (!$post) throw new Exception("POST_DATA_MISSING",1000);
    // Si on fait un ajout de l'echeance
    else {
      // Insertion
      $post["debut"]=date("Y-m-d",strtotime($post["debut"]));
      $post["prochaine_echeance"]=date("Y-m-d",strtotime($post["prochaine_echeance"]));

      unset($post["id_echeancier"],$post['custom']);
      empty(rtrim($post['fin']))? $post['fin']= NULL :$post['fin']=date("Y-m-d",strtotime($post['fin']));
      try {
        // Try / catch pour avoir une erreur 500 forcée
        $result = $this->insert($post);
      }catch(errorATF $e){
        throw new errorATF($e->getMessage(),500);
      }
      $return['result'] = true;
      $return['id_echeancier'] = $result;
    }
    return $return;
  }

  /**
   * Modification d'un contrat
   * @author Quentin JANON <qjanon@absystech.fr>
   * @param  array $get  $_GET
   * @param  array $post $_POST
   * @return array       resultat de lupdate, ID de l'echeancier, notifications, et result (flag booleen)
   */
  public function _PUT($get,$post){
    $input = file_get_contents('php://input');
    if (!empty($input)) parse_str($input,$post);
    $return = array();
    if (!$post) throw new Exception("POST_DATA_MISSING",1000);
    // Si on fait un ajout de l'echeance
    else {
      if (!$post["prochaine_echeance"]) {
        throw new Exception("La date de prochaine échéance est obligatoire",2655);
      }
      // Insertion
      $post["debut"]=date("Y-m-d",strtotime($post["debut"]));
      $post["prochaine_echeance"]=date("Y-m-d",strtotime($post["prochaine_echeance"]));

      if ($post["debut"] > $post["prochaine_echeance"]) {
        throw new Exception("La date de prochaine échéance ne peut pas être antérieure à la date de début",2654);
      }

      if($post['jour_facture'] == "custom") $post["jour_facture"]= $post["custom"];

      empty(rtrim($post['fin'])) ? $post['fin']= NULL :$post['fin']=date("Y-m-d",strtotime($post['fin']));
      unset($post['custom']);
      $result = $this->update($post);
      $return['result'] = true;
      $return['id_echeancier'] = $post["id_echeancier"];
    }
    return $return;
  }
  /**
  * Permet de supprimer un contrat
  * @package Telescope
  * @author Cyril CHARLIER <ccharlier@absystech.fr>
  * @param $get array contient l'id a l'index 'id'
  * @param $post array vide
  * @return array result en booleen et notice sous forme d'un tableau
  */
  public function _DELETE($get,$post) {
    if (!$get['id']) throw new Exception("MISSING_ID",1000);
    $get["actif"]="non";
    $get["id_echeancier"]=$get['id'];
    unset($get["id"],$get["path"],$get["method"]);
    $return['result'] = $this->update($get);
    return $return;
  }

  /**
   * Créer une facture soit en preview soit en réel
   * @author Quentin JANON <qjanon@absystech.fr>
   * @param  array &$get COntient les infos pour générer la facture et le flag de preview
   * @param  array $post Same ci dessus
   * @return mixed       base64 du contneu du PDF généré pour la preview OU ID de la facture insérée
   */
  public function _createFacture(&$get, $post){
    $input = file_get_contents('php://input');
    if (!empty($input)) parse_str($input,$post);
    if (!$post) throw new Exception("POST_DATA_MISSING",1000);

    if (!$post['lignes']) throw new errorATF("LINES_MISSING",1523);
    $lignes = $post['lignes'];

    if (!$post['id']) throw new errorATF("MISSING_ID",1524);
    $id_echeancier = $post["id"];

    if (!$post['date_facture']) throw new errorATF("MISSING_DATE",1525);
    $date_facture = date('Y-m-d',strtotime($post["date_facture"]));

    if (!$post['debut_periode']) throw new errorATF("MISSING_DEBUT_PERIOD",1525);
    $date_debut_periode = date('Y-m-d',strtotime($post["debut_periode"]));

    if (!$post['fin_periode']) throw new errorATF("MISSING_FIN_PERIOD",1525);
    $date_fin_periode = date('Y-m-d',strtotime($post["fin_periode"]));

    $this->q->reset()->addField('fin')->where('id_echeancier',$id_echeancier);
    $fin_de_contrat = $this->select_cell();


    $produits = array();
    foreach ($lignes['designation'] as $idx=>$des) {
      $p=array();

      $p["facture_ligne__dot__ref"] = $lignes["ref"][$idx];
      $p["facture_ligne__dot__produit"] = $des;

      $p["facture_ligne__dot__quantite"] = $lignes["quantite"][$idx];
      $p["facture_ligne__dot__prix"] = $lignes["puht"][$idx];

      $p["facture_ligne__dot__prix_achat"] = NULL;
      $p["facture_ligne__dot__id_fournisseur_fk"] = NULL;
      $p["facture_ligne__dot__id_compte_absystech_fk"] = $lignes["id_compte_absystech"][$idx];
      $p["facture_ligne__dot__serial"] = null;
      $p["facture_ligne__dot__prix_nb"] = null;
      $p["facture_ligne__dot__prix_couleur"] = null;
      $p["facture_ligne__dot__prix_achat_nb"] = null;
      $p["facture_ligne__dot__prix_achat_couleur"] = null;
      $p["facture_ligne__dot__index_nb"] = null;
      $p["facture_ligne__dot__index_couleur"] = null;
      $p["facture_ligne__dot__visible"] = "oui";
      $p["facture_ligne__dot__marge"] = NULL;
      $p["facture_ligne__dot__marge_absolue"] = NULL;

      $produits[] = $p;
    }

    $affaire_sans_devis_libelle = ($post["affaire_sans_devis_libelle"])? $post["affaire_sans_devis_libelle"]: NULL;
    $facture = array();

    $facture["facture"] = array(
      "id_societe"=> $post["id_societe"],
      "type_facture" => "facture_periodique",
      "date"=> $date_facture,
      "infosSup" => $post["infosSup"],
      "id_affaire" => $post["id_affaire"],
      "date_previsionnelle" => NULL,
      "date_relance" => NULL,
      "affaire_sans_devis_libelle" => $affaire_sans_devis_libelle,
      "mode" => NULL,
      "periodicite" => $post["periodicite"],
      "id_facture_parente" => NULL,
      "id_termes" => $post["id_termes"],
      "id_echeancier" => $id_echeancier,
      "date_debut_periode" => $date_debut_periode,
      "date_fin_periode" => $date_fin_periode,
      "sous_total" => NULL,
      "marge" => NULL,
      "frais_de_port" => NULL,
      "marge_absolue" => NULL,
      "prix" =>  NULL,
      "tva" => "1.200",
      "prix_achat" => NULL,
      "email" => NULL,
      "emailTexte" => NULL,
      "emailCopie" => NULL,
    );

    if($post["affaire_sans_devis"]){
      $facture["facture"]["affaire_sans_devis"]= $post["affaire_sans_devis"];
    }
    $facture["values_facture"]["produits"] = json_encode($produits);
    ATF::db($this->db)->begin_transaction();
    try{
      if($post["preview"]){
        $facture["preview"] = $post['preview'];
      } else {

        if($fin_contrat) {
          $fin_contrat = strtotime($fin_contrat);
          $fin_period = strtotime($date_fin_periode);

          // si la date de fin de contrat est avant la fin de période actuelle
          if($fin_contrat < $fin_period){
            // le contrat est fini, on peut cacher la ligne de la liste des facturations
            ATF::echeancier()->u(array("id_echeancier"=>$id_echeancier,"actif"=>"non"));
            ATF::$msg->addWarning(ATF::$usr->trans("contrat_desactive",$this->table));

          }else{
            $next_echeance = strtotime($date_fin_periode."+1 days");
            ATF::echeancier()->u(array("id_echeancier"=>$id_echeancier,"prochaine_echeance"=>date("Y-m-d",$next_echeance)));
            ATF::$msg->addNotice(ATF::$usr->trans("maj_prochaine_echeance",$this->table)." : ".date("Y-m-d",$next_echeance));
          }
        } else {
          $next_echeance = strtotime($date_fin_periode."+1 days");
          ATF::echeancier()->u(array("id_echeancier"=>$id_echeancier,"prochaine_echeance"=>date("Y-m-d",$next_echeance)));
          ATF::$msg->addNotice(ATF::$usr->trans("maj_prochaine_echeance",$this->table)." : ".date("Y-m-d",$next_echeance));
        }

      }

      if ($post["preview"]) {
        $return = base64_encode(ATF::facture()->insert($facture));
      } else {
        $return['result'] = ATF::facture()->insert($facture);
        $return['notices'] = ATF::$msg->getNotices();
        $return['warning'] = ATF::$msg->getWarnings();
      }

    }catch(errorATF $e){
      ATF::db($this->db)->rollback_transaction();
      throw $e;
    }
    ATF::db($this->db)->commit_transaction();
    return $return;
  }

}
