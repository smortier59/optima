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
      ,'echeancier_ligne_ponctuelle.total'
      ,'echeancier_ligne_ponctuelle.puht'
      ,'echeancier_ligne_ponctuelle.date_valeur'
      ,'echeancier_ligne_ponctuelle.ventilation_analytique'

     
    );

    $this->colonnes['primary'] = array(
       'id_echeancier_ligne_ponctuelle'
      ,'designation'
      ,'id_echeancier'
      ,'quantite'
      ,'total'
      ,'puht'
      ,'date_valeur'
      ,'ventilation_analytique'
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

    try {
      $result = $this->insert($post);
    } catch (errorSQL $e) {
      throw new Exception($e->getMessage(),500); // L'erreur 500 permet pour telescope de savoir que c'est une erreur
    }

    return true;
  } 

}