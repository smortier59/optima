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
       'echeancier_ligne_periodique.id_echeancier_periodique'
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
       'id_echeancier_periodique'
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
  // GET A FAIRE
/**
  * Fonction _POST pour telescope
  * @package Telescope
  * @author Charlier Cyril <ccharlier@absystech.fr> 
  * @param $get array.
  * @param $post array Argument obligatoire.
  * @return boolean | integer 
  */
  public function _POST($get,$post) {
    // verif si tous les champs sont bien renseignés
    log::logger($post,"ccharlier");
    if (!$post['id_echeancier']) throw new errorATF(ATF::$usr->trans('id_echeancier_missing','echeancier_ligne_periodique'));
    if (!$post['designation']) throw new errorATF(ATF::$usr->trans('designation','echeancier_ligne_periodique'));
    if (!$post['quantite']) throw new errorATF(ATF::$usr->trans('quantite_missing','echeancier_ligne_periodique'));
    if (!$post['puht']) throw new errorATF(ATF::$usr->trans('puht_missing','echeancier_ligne_periodique'));
    if (!$post['total']) throw new errorATF(ATF::$usr->trans('total_reglement_missing','echeancier_ligne_periodique'));
    if (!$post['valeur_variable']) throw new errorATF(ATF::$usr->trans('valeur_variable_missing','echeancier_ligne_periodique'));
    if (!$post['mise_en_service']) throw new errorATF(ATF::$usr->trans('mise_en_service_missing','echeancier_ligne_periodique'));
    if (!$post['facture_prorataisee']) throw new errorATF(ATF::$usr->trans('facture_prorataisee_missing','echeancier_ligne_periodique'));
    if (!$post['ventilation_analytique']) throw new errorATF(ATF::$usr->trans('ventilation_analytique_missing','echeancier_ligne_periodique'

    // parser la date sous le bon format pour mysql
    $post["mise_en_service"]=date("Y-m-d",strtotime($post["mise_en_service"]));
    // traitement pour les switchery
    /*$post["facture_prorataisee"]=($post["facture_prorataisee"] == "on")
    $post["valeur_variable"]=*/

    $result = $this->insert($post);       
    $return['result'] = true;
    $return['id_echeancier'] = $post["id_echeancier"];
    $return['id_echeancier_periodique'] = $result;
    return $return; 
  } 
}