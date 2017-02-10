<?
/**
 * Classe echeancier_ligne_ponctuelle
 * @package Optima
 */
class echeancier_ligne_ponctuelle extends classes_optima {

  /**
   * Constructeur
   */
  public function __construct() {
    parent::__construct();

    $this->table = "echeancier_ligne_ponctuelle";
    $this->colonnes['fields_column'] = array(
       'echeancier_ligne_ponctuelle.id_echeancier_ligne_ponctuelle'
      ,'echeancier_ligne_ponctuelle.designation'
      ,'echeancier_ligne_ponctuelle.id_echeancier'
      ,'echeancier_ligne_ponctuelle.quantite'
      ,'echeancier_ligne_ponctuelle.puht'
      ,'echeancier_ligne_ponctuelle.date_valeur'
      ,'echeancier_ligne_ponctuelle.id_compte_absystech'
    );

    $this->colonnes['primary'] = array(
       'id_echeancier_ligne_ponctuelle'
      ,'designation'
      ,'id_echeancier'
      ,'quantite'
      ,'puht'
      ,'date_valeur'
      ,'id_compte_absystech'
    );
  }

  /**
  * Fonction _POST pour telescope
  * @package Telescope
  * @author Charlier Cyril <ccharlier@absystech.fr>
  * @param $get array.
  * @param $post array Argument obligatoire.
  * @return boolean | integer
  */
  public function _POST($get,$post) {

    // parser la date sous le bon format pour mysql
    $post["date_valeur"]=date("Y-m-d",strtotime($post["date_valeur"]));
    unset($post["total"]);
    try {
      $result = $this->insert($post);
    } catch (errorSQL $e) {
      throw new Exception($e->getMessage(),500); // L'erreur 500 permet pour telescope de savoir que c'est une erreur
    }
    return true;
  }
  /**
  * Permet de supprimer une ligne d'echeancier ponctuelle
  * @package Telescope
  * @author Cyril CHARLIER <ccharlier@absystech.fr>
  * @param $get array contient l'id a l'index 'id'
  * @param $post array vide
  * @return array result en booleen et notice sous forme d'un tableau
  */
  public function _DELETE($get,$post) {
    if (!$get['id']) throw new Exception("MISSING_ID",1000);
    // gerer le cas du montant sur le delete
    $this->q->reset();
    $return['result'] = $this->delete($get);
    // Récupération des notices créés
    $return['notices'] = ATF::$msg->getNotices();
    return $return;
  }

  /**
  * Fonction _PUT pour telescope
  * @package Telescope
  * @author Charlier Cyril <ccharlier@absystech.fr>
  * @param $get array.
  * @param $post array Argument obligatoire.
  * @return boolean | integer
  */

  public function _PUT($get,$post){
    $input = file_get_contents('php://input');
    if (!empty($input)) parse_str($input,$post);
      $return = array();
    if (!$post) throw new Exception("POST_DATA_MISSING",1000);
    unset($post["id_echeancier"],$post["total"]);
    // parser la date sous le bon format pour mysql
    $post["date_valeur"]=date("Y-m-d",strtotime($post["date_valeur"]));
    try {
      $return = $this->update($post);
    } catch (errorSQL $e) {
      throw new Exception($e->getMessage(),500); // L'erreur 500 permet pour telescope de savoir que c'est une erreur
    }
    return $return;
  }

  /**
   * Renvoi les lignes de facturation ponctuelle
   * @author Quentin JANON <qjanon@absystech.fr>
   * @param  array $get  $_GET
   * @param  array $post $_POST
   * @return array       une ou plusieurs lignes selon les paramètres
   */
  public function _GET($get, $post) {
    // Gestion du tri
    if (!$get['tri'] ) $get['tri'] = "id_echeancier_ligne_ponctuelle";
    if (!$get['trid']) $get['trid'] = "asc";

    // Gestion du limit
    if (!$get['limit']) $get['limit'] = 30;

    // Gestion de la page
    if (!$get['page']) $get['page'] = 0;

    $this->q->reset();

    $colsData = array(
      "echeancier_ligne_ponctuelle.designation",
      "echeancier_ligne_ponctuelle.quantite",
      "echeancier_ligne_ponctuelle.puht",
      "echeancier_ligne_ponctuelle.date_valeur",
      "echeancier_ligne_ponctuelle.id_echeancier",
      "echeancier_ligne_ponctuelle.id_compte_absystech",
      "echeancier_ligne_ponctuelle.ref"
    );

    $this->q->addField($colsData);

    // $this->q->from("echeancier","id_societe","societe","id_societe");
    // $this->q->from("echeancier","id_affaire","affaire","id_affaire");
    $this->q->from("echeancier","id_echeancier","echeancier_ligne_ponctuelle","id_echeancier");
    // $this->q->from("echeancier","id_termes","termes","id_termes");

    $this->q->addGroup("echeancier_ligne_ponctuelle.id_echeancier_ligne_ponctuelle");

    if($get["search"]){
      header("ts-search-term: ".$get['search']);
      $this->q->setSearch($get['search']);
    }

    if ($get['id']) {
      $this->q->where("id_echeancier_ligne_ponctuelle",$get['id'])->setLimit(1);
    } else {

      $this->q->setLimit($get['limit'])->setCount();
      // $data = $this->select_all($get['tri'],$get['trid'],$get['page'],true);

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
