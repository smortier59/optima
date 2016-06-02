<?
/**
* @package Optima
*/
class registrar extends classes_optima {
	function __construct() { //hé
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column']  = array(
			'registrar.registrar'
		);
		$this->fieldstructure();
	}
};
?>