<?
/**
* Classe type_contrat
* @package Optima
*/
class type_contrat extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() { 
		parent::__construct();
		$this->table = "type_contrat";

		$this->fieldstructure();	
		$this->field_nom = "type_contrat";

	}
}