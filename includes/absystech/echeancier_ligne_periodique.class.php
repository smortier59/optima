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
    $return['result'] = $this->delete($get);
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
    $post["mise_en_service"]=date("Y-m-d",strtotime($post["mise_en_service"]));
    unset($post["total"]);
    try {
      $result = $this->insert($post);
    } catch (errorSQL $e) {
      throw new Exception($e->getMessage(),500); // L'erreur 500 permet pour telescope de savoir que c'est une erreur
    }

    return true;
  } 
}