<?
/** Classe tache
* @package Optima
* @subpackage LMA
*/
require_once dirname(__FILE__)."/../tache.class.php";
class tache_lm extends tache {
	function __construct() {
		$this->table = "tache";
		parent::__construct();
	}


	/**
    * Retourne la valeur par défaut spécifique aux données passées en paramètres
    * @author Morgan FLEURQUIN <mfleurquind@absystech.fr>
	* @param string $field
	* @return string
    */
	public function default_value($field){
		if(ATF::_r('id_affaire')){
			switch ($field) {
				case "id_societe":
					return ATF::affaire()->select(ATF::_r("id_affaire"), "id_societe");
				break;
			}
		}
		return parent::default_value($field);
	}

};


