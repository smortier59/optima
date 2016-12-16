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
      ,'echeancier_ligne_periodique.total'
      ,'echeancier_ligne_periodique.valeur_variable'
      ,'echeancier_ligne_periodique.facture_prorataisee'
      ,'echeancier_ligne_periodique.mise_en_service'
      ,'echeancier_ligne_periodique.ventilation_analytique'
      ,'echeancier_ligne_periodique.id_echeancier'
     
    );

    $this->colonnes['primary'] = array(
       'id_echeancier_ligne_periodique'
      ,'designation'
      ,'quantite'
      ,'puht'
      ,'total'
      ,'valeur_variable'
      ,'facture_prorataisee'
      ,'mise_en_service'
      ,'ventilation_analytique'
      ,'id_echeancier'
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
    $post["mise_en_service"]=date("Y-m-d",strtotime($post["mise_en_service"]));

    try {
      $result = $this->insert($post);
    } catch (errorSQL $e) {
      throw new Exception($e->getMessage(),500); // L'erreur 500 permet pour telescope de savoir que c'est une erreur
    }

    return true;
  } 
}