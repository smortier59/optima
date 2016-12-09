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
       'echeancier_ligne_ponctuelle.id_echeancier_ponctuel'
      ,'echeancier_ligne_ponctuelle.designation'
      ,'echeancier_ligne_ponctuelle.id_echeancier'
      ,'echeancier_ligne_ponctuelle.quantite'
      ,'echeancier_ligne_ponctuelle.total'
      ,'echeancier_ligne_ponctuelle.puht'
      ,'echeancier_ligne_ponctuelle.date_valeur'
      ,'echeancier_ligne_ponctuelle.ventilation_analytique'

     
    );

    $this->colonnes['primary'] = array(
       'id_echeancier_ponctuel'
      ,'designation'
      ,'id_echeancier'
      ,'quantite'
      ,'total'
      ,'puht'
      ,'date_valeur'
      ,'ventilation_analytique'
    );
  }

}