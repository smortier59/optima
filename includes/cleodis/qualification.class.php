<?
/**
* Classe qualification
* @package Optima
*/
class qualification extends classes_optima {
	/**
	* Constructeur
	*/
	public function __construct() { 
		parent::__construct();
		$this->table = "qualification";

		$this->fieldstructure();	
		$this->field_nom = "qualification";

	}
}