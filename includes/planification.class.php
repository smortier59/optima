<?
/**
* @package Optima
*/
class planification extends classes_optima {
	function __construct() { // PHP5
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes['fields_column'] = array('id_affaire'
												,'id_societe'
												,'resume'
												,'description'
												,'note'
												,'date_mise_en_place'
												,'annexes'
												);
												
		$this->fieldstructure();		
	}
};
?>