<?
/**
 * Classe echeancier_ligne_periodique
 * @package Optima
 */
class echeancier_ligne_periodique extends classes_optima {

  /**
   * Constructeur
   */
  public function __construct() {
    parent::__construct();

    $this->table = "echeancier_ligne_periodique";
    $this->colonnes['fields_column'] = array(
       'echeancier_ligne_periodique.id_echeancier_ligne_periodique'
      ,'echeancier_ligne_periodique.designation'
      ,'echeancier_ligne_periodique.quantite'
      ,'echeancier_ligne_periodique.puht'
      ,'echeancier_ligne_periodique.valeur_variable'
      ,'echeancier_ligne_periodique.facture_prorata'
      ,'echeancier_ligne_periodique.mise_en_service'
      ,'echeancier_ligne_periodique.id_compte_absystech'
      ,'echeancier_ligne_periodique.id_echeancier'

    );

    $this->colonnes['primary'] = array(
       'id_echeancier_ligne_periodique'
      ,'designation'
      ,'quantite'
      ,'puht'
      ,'valeur_variable'
      ,'facture_prorata'
      ,'mise_en_service'
      ,'id_compte_absystech'
      ,'id_echeancier'
    );
  }
  /**
  * Permet de supprimer une ligne d'echeancier périodique
  * @package Telescope
  * @author Cyril CHARLIER <ccharlier@absystech.fr>
  * @author Quentin JANON <qjanon@absystech.fr>
  * @param $get array contient l'id a l'index 'id'
  * @param $post array vide
  * @return array result en booleen et notice sous forme d'un tableau
  */
  public function _DELETE($get,$post) {
    if (!$get['id']) throw new Exception("MISSING_ID",1000);

    $return['result'] = $this->delete($get);
    // Récupération des notices créés
    $return['notices'] = ATF::$msg->getNotices();
    return $return;
  }
  /**
  * Fonction _POST pour telescope
  * @package Telescope
  * @author Charlier Cyril <ccharlier@absystech.fr>
  * @author Quentin JANON <qjanon@absystech.fr>
  * @param $get array.
  * @param $post array Argument obligatoire.
  * @return boolean | integer
  */
  public function _POST($get,$post) {

    if (!$post['id_echeancier']) throw new errorATF("ID_ECHENCIER_MISSING",5042);
    if (!$post['ref']) throw new errorATF("REF_MISSING",5042);
    if (!$post['designation']) throw new errorATF("DESIGNATION_MISSING",5042);
    if (!$post['quantite']) throw new errorATF("QTE_MISSING",5042);
    if (!$post['mise_en_service']) throw new errorATF("MISE_EN_SERVICE_MISSING",5042);
    if (!$post['id_compte_absystech']) throw new errorATF("VENTILATION_MISSING",5042);


    $post['ref'] = strtoupper($post['ref']);
    // parser la date sous le bon format pour mysql
    $post["mise_en_service"] = date("Y-m-d",$post["mise_en_service"] ? strtotime($post["mise_en_service"]) : time());

    $post["valeur_variable"] = $post["valeur_variable"] == "on" ? 'oui' : 'non';

    ATF::db($this->db)->begin_transaction();
    try {
      // UPDATE DU CONTRAT pour mettre le flag variable
      if ($post['valeur_variable'] == "oui"){
        $update = array('id_echeancier'=> $post['id_echeancier'], 'variable'=> $post['valeur_variable']);
        ATF::echeancier()->u($update);
      }
      $id = $this->insert($post);
      // $post['id_echeancier_ligne_periodique'] = $id;
      $post['id'] = $id;
      $return['row'] = $post;

    } catch (errorATF $e) {
      ATF::db($this->db)->rollback_transaction();
      throw $e;
    }
    ATF::db($this->db)->commit_transaction();
    return $return;
  }

  /**
  * Fonction _PUT pour telescope
  * @package Telescope
  * @author Charlier Cyril <ccharlier@absystech.fr>
  * @author Quentin JANON <qjanon@absystech.fr>
  * @param $get array.
  * @param $post array Argument obligatoire.
  * @return boolean | integer
  */
  public function _PUT($get,$post){
    $input = file_get_contents('php://input');
    if (!empty($input)) parse_str($input,$post);

    if (!$post) throw new Exception("POST_DATA_MISSING",1000);
    // parser la date sous le bon format pour mysql
    $post["mise_en_service"]=date("Y-m-d",strtotime($post["mise_en_service"]));
    $post["valeur_variable"] = ($post["valeur_variable"] == "on")? 'oui':'non';
    try {
      $this->update($post);
      $post['id'] = $post['id_echeancier_ligne_periodique'];
      $return['row'] = $post;
      $return['notices'] = ATF::$msg->getNotices();
    } catch (errorATF $e) {
      throw $e; // L'erreur 500 permet pour telescope de savoir que c'est une erreur
    }
    return $return;
  }

  /**
   * Renvoi les lignes de facturation périodique
   * @author Quentin JANON <qjanon@absystech.fr>
   * @param  array $get  $_GET
   * @param  array $post $_POST
   * @return array       une ou plusieurs lignes selon les paramètres
   */
  public function _GET($get, $post) {
    // Gestion du tri
    if (!$get['tri'] ) $get['tri'] = "id_echeancier_ligne_periodique";
    if (!$get['trid']) $get['trid'] = "asc";

    // Gestion du limit
    if (!$get['limit']) $get['limit'] = 30;

    // Gestion de la page
    if (!$get['page']) $get['page'] = 0;

    $this->q->reset();

    $colsData = array(
      "echeancier_ligne_periodique.designation",
      "echeancier_ligne_periodique.quantite",
      "echeancier_ligne_periodique.puht",
      "echeancier_ligne_periodique.valeur_variable",
      "echeancier_ligne_periodique.facture_prorata",
      "echeancier_ligne_periodique.mise_en_service",
      "echeancier_ligne_periodique.id_echeancier",
      "echeancier_ligne_periodique.id_compte_absystech",
      "echeancier_ligne_periodique.ref"
    );

    $this->q->addField($colsData);

    // $this->q->from("echeancier","id_societe","societe","id_societe");
    // $this->q->from("echeancier","id_affaire","affaire","id_affaire");
    $this->q->from("echeancier","id_echeancier","echeancier_ligne_periodique","id_echeancier");
    // $this->q->from("echeancier","id_termes","termes","id_termes");

    $this->q->addGroup("echeancier_ligne_periodique.id_echeancier_ligne_periodique");

    if($get["search"]){
      header("ts-search-term: ".$get['search']);
      $this->q->setSearch($get['search']);
    }

    if ($get['id']) {
      $this->q->where("id_echeancier_ligne_periodique",$get['id'])->setLimit(1);
    } else {

      $this->q->setLimit($get['limit'])->setCount();

    }

    // TRI
    // switch ($get['tri']) {
    // }
    // $this->q->setToString();
    // log::logger($this->select_all($get['tri'],$get['trid'],$get['page'],true),'qjanon');
    // $this->q->unsetToString();
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
    }else{

      header("ts-total-row: ".$data['count']);
      header("ts-max-page: ".ceil($data['count']/$get['limit']));
      header("ts-active-page: ".$get['page']);
      $return = $data['data'];

    }

    return $return;
  }


}
