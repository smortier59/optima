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
    ATF::echeancier()->increase($post['id_echeancier'],'montant_ht',$post["total"]);
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
    ATF::echeancier()->increase($post['id_echeancier'],'montant_ht','-'.$post["total"]);
    $return['result'] = $this->delete($get);
    // Récupération des notices créés
    $return['notices'] = ATF::$msg->getNotices();
    return $return;
  }
}