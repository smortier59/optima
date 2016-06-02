<?
/**
* @package Optima
*/
class image extends classes_optima {
	function __construct() { // PHP5 hé
		parent::__construct();
		$this->table = __CLASS__;
		$this->colonnes["fields_column"] = array(	
			'image.id_user'
			,'image.date'
			,'image.image'
			,'image.description'
		);
		$this->fieldstructure();
	}
};
?>