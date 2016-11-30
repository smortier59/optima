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

}