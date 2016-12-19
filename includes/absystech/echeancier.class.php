<?
/**
 * Classe echeancier
 * @package Optima
 */
class echeancier extends classes_optima {
 
  /**
   * Constructeur
   */
  public function __construct() {
    parent::__construct();

    $this->table = "echeancier";
    $this->colonnes['fields_column'] = array(
       'echeancier.id_societe'
      ,'echeancier.id_affaire'
      ,'echeancier.designation'
      ,'echeancier.montant_ht'
      ,'echeancier.debut'
      ,'echeancier.fin'
      ,'echeancier.variable'
      ,'echeancier.periodicite'
     
    );

    $this->colonnes['primary'] = array(
       'id_societe'
      ,'id_affaire'
      ,'designation'
      ,'montant_ht'
      ,'debut'
      ,'fin'
      ,'variable'
      ,'periodicite'
      ,'jour_paiement'
    );
  }
  /**
  * Fonctions _GET pour telescope
  * @package Telescope
  * @author Charlier Cyril <ccharlier@absystech.fr> 
  * @param $get array contient le tri, page limit et potentiellement un id.
  * @param $post array Argument obligatoire mais inutilisé ici.
  * @return array un tableau avec les données
  */
  public function _GET($get,$post) {
    // Gestion du tri
    if (!$get['tri']) $get['tri'] = "designation";
    if (!$get['trid']) $get['trid'] = "asc";

    // Gestion du limit
    if (!$get['limit']) $get['limit'] = 30;

    // Gestion de la page
    if (!$get['page']) $get['page'] = 0;

    $colsData = array("id_echeancier","designation","montant_ht","commentaire","affaire","societe.id_societe","debut","fin","variable","periodicite","actif","societe","mise_en_service","prochaine_echeance","jour_facture","methode_reglement","echeance.id_affaire");
    $this->q->reset();
    $this->q->addField($colsData)
        ->from("echeancier","id_societe","societe","id_societe")
        ->addJointure("echeancier",'id_affaire',"affaire","id_affaire")
    ;

    if($get["search"]){
      header("ts-search-term: ".$get['search']);
      $this->q->setSearch($get['search']);
    }

    if ($get['id_echeancier']) {
      $this->q->where("id_echeancier",$get['id_echeancier'])->setCount(false)->setDimension('row');
      $data = $this->select_all();
    } else {
      // gestion des filtres
      if ($get['filters']['actif'] == "on") {
        $this->q->andWhere("actif","oui");
      }
      $this->q->setLimit($get['limit'])->setCount();
      $data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);
    }

    foreach ($data["data"] as $k=>$lines) {
      foreach ($lines as $k_=>$val) {
        if (strpos($k_,".")) {
          $tmp = explode(".",$k_);
          $data['data'][$k][$tmp[1]] = $val;
          unset($data['data'][$k][$k_]);
        }       
      }
    }

    if($get['id_echeancier']){
      // GET d'un élément, on ajoute ses lignes récurrentes et ponctuelles
      $data['periodique'] = ATF::echeancier_ligne_periodique()->select_special('id_echeancier', $get['id_echeancier']);
      $data['ponctuelle'] = ATF::echeancier_ligne_ponctuelle()->select_special('id_echeancier', $get['id_echeancier']);
      $return = $data;
    }else{
      header("ts-total-row: ".$data['count']);
      header("ts-max-page: ".ceil($data['count']/$get['limit']));
      header("ts-active-page: ".$get['page']);
      $return = $data['data'];
    }
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
    log::logger("echeancier post ","ccharlier");
    $input = file_get_contents('php://input');
    if (!empty($input)) parse_str($input,$post);
    $return = array();
    if (!$post) throw new Exception("POST_DATA_MISSING",1000);
    // Si on fait un ajout de l'echeance
    else {
      // Check des champs obligatoire
      if (!$post['id_societe']) throw new errorATF(ATF::$usr->trans('id_societe_missing','echeancier'));
      if (!$post['id_affaire']) throw new errorATF(ATF::$usr->trans('id_affaire_missing','echeancier'));
      if (!$post['designation']) throw new errorATF(ATF::$usr->trans('designation','echeancier'));
      if (!$post['periodicite']) throw new errorATF(ATF::$usr->trans('periodicite_missing','echeancier'));
      if (!$post['debut']) throw new errorATF(ATF::$usr->trans('debut_missing','echeancier'));
      if (!$post['methode_reglement']) throw new errorATF(ATF::$usr->trans('methode_reglement_missing','echeancier'));
      if (!$post['mise_en_service']) throw new errorATF(ATF::$usr->trans('mise_en_service_missing','echeancier'));
      if (!$post['methode_reglement']) throw new errorATF(ATF::$usr->trans('methode_reglement_missing','echeancier'));
      if(!$post['jour_facture']) throw new errorATF(ATF::$usr->trans('jour_facture_missing','echeancier'));

      // Insertion
      $post["debut"]=date("Y-m-d",strtotime($post["debut"]));
      $post["mise_en_service"]= date("Y-m-d",strtotime($post["mise_en_service"]));
      if($post['jour_facture'] =="custom") $post["jour_facture"]= $post["custom"];
      $explodeDebut =explode("-", $post["debut"]);
      
      // switch permettant de calculer la prochaine date d'echeance en fonction de la periodicité
      switch($post["periodicite"]){
        case "annuel":
          $post["prochaine_echeance"]= $explodeDebut[0]."-01-01";
        break;
        case "semestriel":
          if($explodeDebut[1]/6 <=1)
            $sem = $explodeDebut[0].'-01-01';
          else
            $sem = $explodeDebut[0].'-07-01';
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($sem));
        break;
        case "trimestriel":
          if($explodeDebut[1]/3 <=1 )
              $sem = $explodeDebut[0].'-01-01';
          elseif($explodeDebut[1]/3 <=2)
              $sem = $explodeDebut[0].'-04-01';
          elseif($explodeDebut[1]/3 <=3)
            $sem = $explodeDebut[0].'-07-01';
          elseif($explodeDebut[1]/3 <=4)
            $sem = $explodeDebut[0].'-10-01';
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($sem));
        break;
        case "mensuel":
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($post["debut"]."first day of this month"));
        break;
      }
      if($post['jour_facture'] =='fin_mois')
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($post["prochaine_echeance"]."first day of this month"));
      else{
          $temp =explode("-", $post["prochaine_echeance"]);
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($temp[0]."-".$temp[1])."-".$post["jour_facture"]);
      }
      unset($post["id_echeancier"],$post['custom']);
      $post['fin']==" "? $post['fin']= NULL :$post['fin']=$post['fin'];
      $result = $this->insert($post);       
      $return['result'] = true;
      $return['id_echeancier'] = $result;
    }
    return $return;
  }

  public function _PUT($get,$post){
    log::logger("echeancier put ","ccharlier");
    $input = file_get_contents('php://input');
    if (!empty($input)) parse_str($input,$post);
    $return = array();
    if (!$post) throw new Exception("POST_DATA_MISSING",1000);
    // Si on fait un ajout de l'echeance
    else {
      // Check des champs obligatoire
      if (!$post["id_echeancier"]) throw new errorATF(ATF::$usr->trans('id_echeancier_missing','echeancier'));
      if (!$post['id_societe']) throw new errorATF(ATF::$usr->trans('id_societe_missing','echeancier'));
      if (!$post['id_affaire']) throw new errorATF(ATF::$usr->trans('id_affaire_missing','echeancier'));
      if (!$post['designation']) throw new errorATF(ATF::$usr->trans('designation','echeancier'));
      if (!$post['periodicite']) throw new errorATF(ATF::$usr->trans('periodicite_missing','echeancier'));
      if (!$post['debut']) throw new errorATF(ATF::$usr->trans('debut_missing','echeancier'));
      if (!$post['methode_reglement']) throw new errorATF(ATF::$usr->trans('methode_reglement_missing','echeancier'));
      if (!$post['mise_en_service']) throw new errorATF(ATF::$usr->trans('mise_en_service_missing','echeancier'));
      if (!$post['methode_reglement']) throw new errorATF(ATF::$usr->trans('methode_reglement_missing','echeancier'));
      if(!$post['jour_facture']) throw new errorATF(ATF::$usr->trans('jour_facture_missing','echeancier'));
      // Insertion
      $post["debut"]=date("Y-m-d",strtotime($post["debut"]));
      $post["mise_en_service"]= date("Y-m-d",strtotime($post["mise_en_service"]));

      // Insertion
      $post["debut"]=date("Y-m-d",strtotime($post["debut"]));
      $post["mise_en_service"]= date("Y-m-d",strtotime($post["mise_en_service"]));
      if($post['jour_facture'] =="custom") $post["jour_facture"]= $post["custom"];
      $explodeDebut =explode("-", $post["debut"]);
      
      // switch permettant de calculer la prochaine date d'echeance en fonction de la periodicité
      switch($post["periodicite"]){
        case "annuel":
          $post["prochaine_echeance"]= $explodeDebut[0]."-01-01";
        break;
        case "semestriel":
          if($explodeDebut[1]/6 <=1)
            $sem = $explodeDebut[0].'-01-01';
          else
            $sem = $explodeDebut[0].'-07-01';
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($sem));
        break;
        case "trimestriel":
          if($explodeDebut[1]/3 <=1 )
              $sem = $explodeDebut[0].'-01-01';
          elseif($explodeDebut[1]/3 <=2)
              $sem = $explodeDebut[0].'-04-01';
          elseif($explodeDebut[1]/3 <=3)
            $sem = $explodeDebut[0].'-07-01';
          elseif($explodeDebut[1]/3 <=4)
            $sem = $explodeDebut[0].'-10-01';
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($sem));
        break;
        case "mensuel":
          $post["prochaine_echeance"]= date("Y-m-d",strtotime($post["debut"]."first day of this month"));
        break;
      }
      if($post['jour_facture'] =='fin_mois')
        $post["prochaine_echeance"]= date("Y-m-d",strtotime($post["prochaine_echeance"]."first day of this month"));
      else{
        $temp =explode("-", $post["prochaine_echeance"]);
        $post["prochaine_echeance"]= date("Y-m-d",strtotime($temp[0]."-".$temp[1])."-".$post["jour_facture"]);
      }
      $post['fin']==" "? $post['fin']= NULL :$post['fin']=$post['fin'];
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

  public function _getPdf($get, $post){

    log::logger($get , "mfleurquin");

    $pdf_mandat = ATF::pdf()->generic('echeancier',$id_affaire,true);

    return base64_encode($pdf_mandat);
  }
}