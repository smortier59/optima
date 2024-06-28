<?
/** Classe facture_ligne
* @package Optima
* @subpackage Cleodis
*/
require_once dirname(__FILE__)."/../facture_ligne.class.php";
class grille_tarifaire_ligne extends classes_optima {
	function __construct() {
		parent::__construct();
		$this->colonnes['fields_column'] = array(
			 'grille_tarifaire_ligne.duree'
			,'grille_tarifaire_ligne.periodicite'
			,'grille_tarifaire_ligne.montant_max'=>array("renderer"=>"money")
			,'grille_tarifaire_ligne.taux'
		);

		$this->colonnes['primary'] = array(
			"duree",
            "periodicite",
            "montant_max",
            "taux",
			"id_grille_tarifaire"=>array("disabled"=>true),
		);

		$this->colonnes['bloquees']['insert'] = array('id_grille_tarifaire','id_grille_tarifaire_ligne');

		$this->fieldstructure();

		$this->foreign_key['id_grille_tarifaire'] =  "grille_tarifaire";
	}
};