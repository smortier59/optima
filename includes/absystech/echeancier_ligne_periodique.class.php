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
  * @param $get array contient l'id a l'index 'id'
  * @param $post array vide
  * @return array result en booleen et notice sous forme d'un tableau
  */
  public function _DELETE($get,$post) {
    if (!$get['id']) throw new Exception("MISSING_ID",1000);
    // gerer le cas du montant sur le delete
    // requete qui recupère la qté et le puht pour le retirer au montant total de l'echeancier
    $this->q->reset();
    $this->q->addField("quantite")->addField("puht")->addField("valeur_variable")->addField("id_echeancier")
            ->where("id_echeancier_ligne_periodique",$get['id'])
            ->setDimension('row');

    $data =$this->select_all();
    // on exec le delete
    $delete =$this->delete($get);
    // puis on regarde s'il reste des lignes avec une valeur variable
    $this->q->reset();
    $this->q->addField("id_echeancier_ligne_periodique")
            ->where("id_echeancier",$data["id_echeancier"])
            ->addCondition("valeur_variable", 'oui')
            ->setCount();
    $count = $this->select_all();
    // s'il y en a pas, on met variable à non dans l'echeancier
    if($count['count'] ==0){
      $row = array('id_echeancier'=>$data["id_echeancier"],'variable'=>'non');
      ATF::echeancier()->update($row);
    }
    $total = number_format($data["quantite"]* $data['puht'],2,'.','');
    //ATF::echeancier()->increase($data['id_echeancier'],'montant_ht','-'.$total);
    $return['result'] = $delete;
    // Récupération des notices créés
    $return['notices'] = ATF::$msg->getNotices();
    return $return;
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
    $post["mise_en_service"] = date("Y-m-d",$post["mise_en_service"] ? strtotime($post["mise_en_service"]) : time());
    $post["valeur_variable"] = ($post["valeur_variable"] == "on")? 'oui':'non';
    unset($post["total"]);
    if ($post['valeur_variable'] == "oui"){
      $update = array('id_echeancier'=> $post['id_echeancier'], 'variable'=> $post['valeur_variable']);
      ATF::echeancier()->u($update);
    }
    try {
      $result = $this->insert($post);
    } catch (errorSQL $e) {
      throw new Exception($e->getMessage(),500); // L'erreur 500 permet pour telescope de savoir que c'est une erreur
    }
    return true;
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
    $post["mise_en_service"] = date("Y-m-d",$post["mise_en_service"] ? strtotime($post["mise_en_service"]) : time());
    $post["valeur_variable"] = ($post["valeur_variable"] == "on")? 'oui':'non';
    try {
      $return = $this->update($post);
    } catch (errorSQL $e) {
      throw new Exception($e->getMessage(),500); // L'erreur 500 permet pour telescope de savoir que c'est une erreur
    }
    return $return;
  }
}