<?
/**
* Classe statut
* @package Optima
*/
class statut extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() { 
		parent::__construct();
		$this->table = "statut";

		$this->fieldstructure();	
		$this->field_nom = "statut";

	}
}